<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Api extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->lang->load('api/api', 'english');
        $this->load->model('portal/Wallet_model');				
    }

    public function rechargeAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();

        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;
        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$before_wallet_balance = $getAccountData['wallet_balance'];
				$min_wallet_balance = $getAccountData['min_wallet_balance'];

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				$this->load->library('form_validation');
		        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|max_length[12]|xss_clean');
		        $this->form_validation->set_rules('operator', 'Operator', 'required');
		        $this->form_validation->set_rules('circle', 'Circle', 'required');
		        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
		        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
		        if ($this->form_validation->run() == FALSE) {
		        	// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Post Parameters Not Valid.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => lang('400_ERROR_MSG'),
					);
		        }
		        else
		        {
		        	$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		        	$final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];
		        	if($before_wallet_balance < $post['amount']){
		        		// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Insufficient Wallet Error.]'.PHP_EOL;
				        $this->User->generateLog($log_msg);
		        		$response = array(
							'status_code' => 401,
							'status_msg' => lang('401_WALLET_BALANCE_ERROR'),
						);
		        	}
		        	elseif($before_wallet_balance < $final_deduct_wallet_balance){
		        		// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Minimum Wallet Error.]'.PHP_EOL;
				        $this->User->generateLog($log_msg);
		        		$response = array(
							'status_code' => 401,
							'status_msg' => lang('401_MIN_WALLET_BALANCE_ERROR'),
						);
		        	}
		        	else
		        	{
		        		// get oprator id
		        		$get_op_id = $this->db->get_where('operator',array('operator_code'=>$post['operator']))->row_array();
		        		$op_id = isset($get_op_id['id']) ? $get_op_id['id'] : 0 ;

		        		// get circle id
		        		$get_circle_id = $this->db->get_where('circle',array('circle_code'=>$post['circle']))->row_array();
		        		$cirlce_id = isset($get_circle_id['id']) ? $get_circle_id['id'] : 0 ;
		        		if($op_id)
		        		{
		        			// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Operator Verified Successfully.]'.PHP_EOL;
					        $this->User->generateLog($log_msg);
		        			$apiResponse = $this->User->generate_api_url($loggedAccountID,$op_id,$post['amount'],$loggedAccountID);
		        			// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Generate API Response - '.json_encode($apiResponse).'.]'.PHP_EOL;
					        $this->User->generateLog($log_msg);
		        			if($apiResponse['status'] && $apiResponse['api_id'])
            				{
            					if($accountData['account_type'] == 2)
				                {
				                    // get operator code
				                    $get_operator_code = $this->db->get_where('api_operator',array('account_id'=>SUPERADMIN_ACCOUNT_ID,'api_id'=>$response['api_id'],'opt_id'=>$post['operator']))->row_array();
				                }
				                else
				                {
            						// get operator code
				                	$get_operator_code = $this->db->get_where('api_operator',array('account_id'=>$domain_account_id,'api_id'=>$apiResponse['api_id'],'opt_id'=>$op_id))->row_array();
				            	}
				                $opt_code = isset($get_operator_code['opt_code']) ? $get_operator_code['opt_code'] : '';

				                // get circle code
				                $get_circle_code = $this->db->get_where('api_circle',array('account_id'=>$domain_account_id,'api_id'=>$apiResponse['api_id'],'circle_id'=>$cirlce_id))->row_array();
				                $circle_code = isset($get_circle_code['circle_code']) ? $get_circle_code['circle_code'] : '';

				                // generate recharge unique id
                				$recharge_unique_id = $post['txnID'];

                				$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                				$user_after_balance = $before_wallet_balance - $post['amount'];

                				$system_opt_id = $op_id;

                				$data = array(
				                    'account_id'         => $domain_account_id,
				                    'member_id'          => $loggedAccountID,
				                    'api_id'             => $apiResponse['api_id'],
				                    'recharge_type'      => 1,
				                    'recharge_subtype'   => 1,
				                    'recharge_display_id'=> $recharge_unique_id,
				                    'mobile'             => $post['mobile'],
				                    'account_number'     => isset($post['acnumber']) ? $post['acnumber'] : '',
				                    'operator_code'      => $opt_code,
				                    'system_opt_id'      => $system_opt_id,
				                    'circle_code'        => $circle_code,
				                    'amount'             => $post['amount'],
				                    'before_balance'     => $before_wallet_balance,
                    				'after_balance'      => $user_after_balance,
				                    'status'             => 1,
				                    'created'            => date('Y-m-d H:i:s')                  
				                );
								$this->db->insert('recharge_history',$data);
				                $recharge_id = $this->db->insert_id();

				                $after_balance = $before_wallet_balance - $post['amount'];    

			                    $wallet_data = array(
			                        'account_id'          => $domain_account_id,
			                        'member_id'           => $loggedAccountID,    
			                        'before_balance'      => $before_wallet_balance,
			                        'amount'              => $post['amount'],  
			                        'after_balance'       => $after_balance,      
			                        'status'              => 1,
			                        'type'                => 2,      
			                        'created'             => date('Y-m-d H:i:s'),      
			                        'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
			                    );

			                    $this->db->insert('member_wallet',$wallet_data);

				                // save system log
						        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Record Saved Recharge ID - '.$recharge_id.'.]'.PHP_EOL;
						        $this->User->generateLog($log_msg);

				                $api_url = $apiResponse['api_url'];
				                $api_post_data = $apiResponse['post_data'];
				                $api_url = str_replace('{AMOUNT}',$post['amount'],$api_url);
				                $api_url = str_replace('{OPERATOR}',$opt_code,$api_url);
				                $api_url = str_replace('{CIRCLE}',$circle_code,$api_url);
				                $api_url = str_replace('{TXNID}',$recharge_unique_id,$api_url);
				                $api_url = str_replace('{MOBILE}',$post['mobile'],$api_url);
				                $api_url = str_replace('{MEMBERID}',$memberID,$api_url);

				                // replace post data
				                if($api_post_data)
				                {
				                    foreach($api_post_data as $apikey=>$apival)
				                    {
				                        if($apival == '{AMOUNT}')
				                        {
				                            $api_post_data[$apikey] = $post['amount'];
				                        }
				                        elseif($apival == '{OPERATOR}')
				                        {
				                            $api_post_data[$apikey] = $opt_code;
				                        }
				                        elseif($apival == '{CIRCLE}')
				                        {
				                            $api_post_data[$apikey] = $circle_code;
				                        }
				                        elseif($apival == '{TXNID}')
				                        {
				                            $api_post_data[$apikey] = $recharge_unique_id;
				                        }
				                        elseif($apival == '{MOBILE}')
				                        {
				                            $api_post_data[$apikey] = $post['mobile'];
				                        }
				                        elseif($apival == '{MEMBERID}')
				                        {
				                            $api_post_data[$apikey] = $memberID;
				                        }
				                    }
				                }

				                // call recharge API
                				$api_response = $this->User->prepaid_rechage_api($api_url,$api_post_data,$loggedAccountID,$recharge_unique_id,$apiResponse['api_id'],$apiResponse['response_type'],$apiResponse['responsePara'],$apiResponse['seperator'],$apiResponse['header_data'],$memberID,'API');
                				// save system log
						        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Rechage API Called Response - '.json_encode($api_response).'.]'.PHP_EOL;
						        $this->User->generateLog($log_msg);
                				if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
				                {
				                    
				                    
				                    // save system log
							        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Member Wallet Updated New Balance - '.$after_balance.'.]'.PHP_EOL;
							        $this->User->generateLog($log_msg);

				                    if($api_response['status'] == 1){
				                        // update recharge status
				                        $this->db->where('id',$recharge_id);
				                        $this->db->where('recharge_display_id',$recharge_unique_id);
				                        $this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
				                        $response = array(
											'status_code' => 200,
											'status_msg' => lang('200_ERROR_MSG'),
											'status' => 'PENDING',
											'txnid' => $recharge_unique_id,
											'operator_txnid' => $api_response['operator_ref']
										);
				                         
				                    }
				                    elseif($api_response['status'] == 2)
				                    {
				                        // update recharge status
				                        $this->db->where('id',$recharge_id);
				                        $this->db->where('recharge_display_id',$recharge_unique_id);
				                        $this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));

				                       // save system log
								        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Member Commision Distribute Start.]'.PHP_EOL;
								        $this->User->generateLog($log_msg);
				                        // distribute commision
				                        $this->User->distribute_recharge_commision($recharge_id,$recharge_unique_id,$post['amount'],$loggedAccountID);
				                        // save system log
								        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Member Commision Distribute End.]'.PHP_EOL;
								        $this->User->generateLog($log_msg);
				                        
				                        $response = array(
											'status_code' => 200,
											'status_msg' => lang('200_ERROR_MSG'),
											'status' => 'SUCCESS',
											'txnid' => $recharge_unique_id,
											'operator_txnid' => $api_response['operator_ref']
										);
				                       
				                    }
				                }
				                else
				                {
				                    // save system log
								    $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Recharge Failed.]'.PHP_EOL;
								    $this->User->generateLog($log_msg);
				                    // update recharge status
				                    $this->db->where('id',$recharge_id);
				                    $this->db->where('recharge_display_id',$recharge_unique_id);
				                    $this->db->update('recharge_history',array('status'=>3));

				                    $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

				                    $after_balance = $before_wallet_balance + $post['amount'];    

				                    $wallet_data = array(
				                        'account_id'          => $domain_account_id,
				                        'member_id'           => $loggedAccountID,    
				                        'before_balance'      => $before_wallet_balance,
				                        'amount'              => $post['amount'],  
				                        'after_balance'       => $after_balance,      
				                        'status'              => 1,
				                        'type'                => 1,      
				                        'created'             => date('Y-m-d H:i:s'),      
				                        'description'         => 'Recharge #'.$recharge_unique_id.' Amount Refund Credited.'
				                    );

				                    $this->db->insert('member_wallet',$wallet_data);

				                    $response = array(
											'status_code' => 200,
											'status_msg' => lang('200_ERROR_MSG'),
											'status' => 'FAILED',
											'txnid' => $recharge_unique_id,
											'opt_msg' => $api_response['opt_msg']
									); 
				                }

            				}
            				else
            				{
            					$response = array(
									'status_code' => 404,
									'status_msg' => lang('404_ERROR_MSG'),
								);
            				}
		        		}
		        		else
		        		{
		        			// save system log
						    $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Operator Not Valid Error.]'.PHP_EOL;
						    $this->User->generateLog($log_msg);
		        			$response = array(
								'status_code' => 401,
								'status_msg' => lang('401_OPERATOR_ERROR'),
							);
		        		}
		        	}
		        }
			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Recharge API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    public function balanceAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Balance Check API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $post = $this->input->get();
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Balance Check API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Balance Check API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Balance Check API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
		        $response = array(
						'status_code' => 200,
						'status_msg' => lang('200_ERROR_MSG'),
						'balance' => $before_wallet_balance
				); 

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Balance Check API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Balance Check API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Balance Check API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    public function statusAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge Status API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge Status API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge Status API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge Status API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$before_wallet_balance = $getAccountData['wallet_balance'];
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge Status API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
		        $this->load->library('form_validation');
		        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
		        if ($this->form_validation->run() == FALSE) {
		        	// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Recharge Status API Post Parameters Not Valid.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => lang('400_ERROR_MSG'),
					);
		        }
		        else
		        {
		        	$txnID = $post['txnID'];
		        	// check txn id valid or not
		        	$chk_txnid = $this->db->get_where('recharge_history',array('account_id'=>$domain_account_id,'member_id'=>$loggedAccountID,'recharge_display_id'=>$txnID))->num_rows();
		        	if($chk_txnid)
		        	{
		        		// check txn id valid or not
		        		$txnData = $this->db->get_where('recharge_history',array('account_id'=>$domain_account_id,'member_id'=>$loggedAccountID,'recharge_display_id'=>$txnID))->row_array();
		        		$message = '';
		        		if($txnData['status'] == 1)
		        		{
		        			$message = 'PENDING';
		        		}
		        		elseif($txnData['status'] == 2)
		        		{
		        			$message = 'SUCCESS';
		        		}
		        		elseif($txnData['status'] == 3)
		        		{
		        			$message = 'FAILED';
		        		}
				        $response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => $message,
								'txnid' => $txnID,
								'amount' => $txnData['amount'],
								'operator_txnid' => $txnData['operator_ref']
						); 
			    	}
			    	else
			    	{
			    		$response = array(
							'status_code' => 401,
							'status_msg' => lang('401_TXNID_ERROR'),
						);
			    	}
		    	}

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Recharge Status API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Recharge Status API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Recharge Status API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    public function transferAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
		    
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
		    
		    
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd),'is_active'=>1))->num_rows();
			if($chk_user)
			{
			  
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd),'is_active'=>1))->row_array();
				
				$whitelist_ip = $getAccountData['whitelist_ip'];
				$whitelist_ip_list = explode(',',$getAccountData['whitelist_ip']);

				$user_ip_address = $this->User->get_user_ip();
				if($whitelist_ip != '*' && !in_array($user_ip_address, $whitelist_ip_list))
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - Payout API - IP not whitelisted.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
					$response = array(
						'status_code' => 401,
						'status_msg' => 'Sorry ! Your IP Address not whitelisted.'
					);
				}
				else
				{
					$user_email = 	$getAccountData['email'];		
					$loggedAccountID = $getAccountData['id'];
					$api_member_code = $getAccountData['user_code'];
					$user_mobile = $getAccountData['mobile'];
					$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
					$min_wallet_balance = $getAccountData['min_wallet_balance'];
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
			        $this->load->library('form_validation');
			        
					$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
					$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
					$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
			        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
			        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
			        if ($this->form_validation->run() == FALSE) {
			            
			           
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
							'status' => 'FAILED',
							'txn_amount' =>$post['amount'],
							'transaction_id' => strval($post['txnID'])
						);
			        }
			        else
			        {
			           
			        	
						$account_holder_name = $post['account_holder_name'];
						$account_no = $post['account_no'];
						$ifsc = $post['ifsc'];
						$amount = $post['amount'];
						$transaction_id =  $post['txnID']."";
						$mode = 'IMPS';

						
						if($amount < 0)
						{
							// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Post Amount Not Valid.]'.PHP_EOL;
					        $this->User->generateLog($log_msg);
				        	$response = array(
								'status_code' => 400,
								'status_msg' => 'Amount is not valid.',
								'status' => 'FAILED',
								'txn_amount' =>$post['amount'],
								'transaction_id' => strval($post['txnID'])
							);
						}
						else
						{
							// get dmr surcharge
		            		$surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
		            		// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Member Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
					        $this->User->generateLog($log_msg);

					        $final_amount = $amount + $surcharge_amount;
					        
					        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

					        $final_deduct_wallet_balance = $before_wallet_balance - $min_wallet_balance;  
		                    
		                    if($final_deduct_wallet_balance < $final_amount){
				                // save system log
				                $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Member Insufficient Wallet Error]'.PHP_EOL;
				                $this->User->generateLog($log_msg);
				                
				                $message = 'Insufficient Wallet Balance';
				                if($min_wallet_balance)
				                {
				                	$message = 'Minimum wallet balance required';
				                }

				                $response = array(
									'status_code' => 401,
									'status_msg' => lang('401_WALLET_BALANCE_ERROR'),
									'status' => 'FAILED',
		    						'txn_amount' =>$post['amount'],
		    						'txnid' =>$transaction_id,
		    						'rrn' =>null,
									'orderID' =>null,
									'opt_msg'=>$message
								);
		            		}
		            		else
		            		{
		            		    // check txn id already extis or not
		            		    $chkTxnId = $this->db->get_where('user_new_fund_transfer',array('transaction_id'=>$transaction_id,'account_id'=>$domain_account_id))->num_rows();
		            		    if($chkTxnId)
		            		    {
		            		        // save system log
		    		                $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Member Duplicate Txnid Error]'.PHP_EOL;
		    		                $this->User->generateLog($log_msg);
		    		                
		    		                $response = array(
		    							'status_code' => 401,
		    							'status_msg' => 'Duplicate Txnid Found.',
		    							'status' => 'FAILED',
		        						'txn_amount' =>$post['amount'],
		        						'txnid' =>$transaction_id,
		        						'rrn' =>null,
		    							'orderID' =>null,
		    							'opt_msg'=>'Duplicate Txnid Found.'
		    
		    							
		    						);
		            		    }
		            		    else
		            		    {
		                			// get wallet balance
		    				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		    			            $after_wallet_balance = $before_wallet_balance - $final_amount;    
		    			            
		    			            $wallet_data = array(
		    	                        'account_id'          => $domain_account_id,
		    	                        'member_id'           => $loggedAccountID,    
		    	                        'before_balance'      => $before_wallet_balance,
		    	                        'amount'              => $final_amount,  
		    	                        'after_balance'       => $after_wallet_balance,      
		    	                        'status'              => 1,
		    	                        'type'                => 2, 
		    	                        'wallet_type'         => 1,      
		    	                        'created'             => date('Y-m-d H:i:s'),      
		    	                        'description'         => 'Payout #'.$transaction_id.' Amount Deducted.'
		    	                    );
		    
		    	                    $this->db->insert('member_wallet',$wallet_data);
		    						
		    						$txnType = 'IFS';
		    						$receipt_id = rand(111111,999999);
		    	                    
		    	                    $data = array(
		    							'account_id' => $domain_account_id,
		    							'user_id' => $loggedAccountID,
		    							'transfer_amount' => $amount,
		    							'transfer_charge_amount' => $surcharge_amount,
		    							'total_wallet_charge' => $final_amount,
		    							'after_wallet_balance' => $after_wallet_balance,
		    							'transaction_id' => $transaction_id,
		    							'encode_transaction_id' => do_hash($transaction_id),
		    							'status' => 2,
		    							'memberID' => $memberID,
		    							'mobile' => $mobile,
		    							'account_holder_name' => $account_holder_name,
		    							'account_no' => $account_no,
		    							'ifsc' => $ifsc,
		    							'txnType'=>$mode,
		    							'created' => date('Y-m-d H:i:s')
		    						);
		    						$this->db->insert('user_new_fund_transfer',$data);
		    						$txnRecordID = $this->db->insert_id();

		    						$api_url = INSTANTPAY_PAYOUT_API_URL;
		                			// save system log
		                            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout API URL - '.$api_url.']'.PHP_EOL;
		                            $this->User->generateLog($log_msg);
		    				
		    
		    				        $request = array(
		    		                
		        		                'payer' => array(
		            		                'bankId' => '0',
		            		                'bankProfileId' => 0,
		            		                'accountNumber' => $accountData['instant_account_no'],
		            		                ),
		            		                
		        	                        'payee' => array(
		        	                            'name' => $account_holder_name,
		        	                            'accountNumber' => $account_no,
		        	                            'bankIfsc' =>$ifsc
		        	                       ),
		        	                       'transferMode' => $mode,
		        	                       'transferAmount' => $amount,
		        	                       'externalRef' => $transaction_id,
		        	                       'latitude' =>'22.9734229',
		        	                       'longitude' => '78.6568942',
		        	                       'remarks'  => 'Payout',
		        	                       'alertEmail' => $user_email,
		        	                       'purpose' =>'REIMBURSEMENT'
		    		                
		    		                );
		    
		    
		    
		        		            $header = array(
		        		                'X-Ipay-Auth-Code: 1',
		        		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		        		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		        		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		        		                
		        		                'content-type: application/json'
		        		                
		        		            );
		    		            
		    		       
		        		            $curl = curl_init();
		        		            // URL
		        		            curl_setopt($curl, CURLOPT_URL, $api_url);
		        
		        		            // Return Transfer
		        		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		        
		        		            // SSL Verify Peer
		        		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		        
		        		            // SSL Verify Host
		        		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		        
		        		            // Timeout
		        		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		        
		        		            // HTTP Version
		        		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		        
		        		            // Request Method
		        		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		        
		        		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		        
		        		            // Request Body
		        		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
		        
		        		            // Execute
		        		           $output = curl_exec($curl);
		        	       	
		        	        		curl_close ($curl);
		        
		        	        		$responseData = json_decode($output,true);
		    
		    
		    				        // save system log
		    			            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout API Response - '.$output.']'.PHP_EOL;
		    				            $this->User->generateLog($log_msg);
		    
		    							// save api response
		    							$apiData = array(
		    								'account_id' => $domain_account_id,
		    								'user_id' => $loggedAccountID,
		    								'api_response' => $output,
		    								'api_url' => $api_url,
		    								'post_data'=>json_encode($request),
		    								'created' => date('Y-m-d H:i:s'),
		    								'created_by'=>$loggedAccountID
		    							);
		    							$this->db->insert('instantpay_api_response',$apiData);
		    
		    							
		    							if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
		    							{
		    								
		    								$api_msg = $responseData['status'];
		    
		    					             $log_msg = '['.date('d-m-Y H:i:s').' - Payout Transfer API - DMT Transaction Success.]'.PHP_EOL;
		    
		    					            $this->User->generateLog($log_msg);
		    			                    
		    
		    			                    // save system log
		    					            $log_msg = '['.date('d-m-Y H:i:s').' - Payout API -  Transaction Wallet Deducation Done.]'.PHP_EOL;
		    					            $this->User->generateLog($log_msg);
		    					            
		    					            $this->db->where('id',$txnRecordID);
		    					            $this->db->where('account_id',$domain_account_id);
		    					            $this->db->where('user_id',$loggedAccountID);
		    					            $this->db->update('user_new_fund_transfer',array('api_response'=>$output,'status'=>3,'rrn'=>$responseData['data']['txnReferenceId'],'updated'=>date('Y-m-d H:i:s')));
		    
		    								$response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'SUCCESS',
		    											'txn_amount'=>$responseData['data']['txnValue'],
		    											'txnid' => "$transaction_id",
		    											'rrn' =>$responseData['data']['txnReferenceId'],
		    											'orderID' =>$responseData['orderid'],
		    											'opt_msg'=>$api_msg
		    								);
		    
		    								
		    							}
		    
		    							elseif(isset($responseData['statuscode']) && ($responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR'))
		    							{
		    								
		    								$api_msg = $responseData['status'];
		    
		    					            $log_msg = '['.date('d-m-Y H:i:s').' - Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
		    
		    					            $this->User->generateLog($log_msg);
		    					            
		    					            $this->db->where('id',$txnRecordID);
		    					            $this->db->where('account_id',$domain_account_id);
		    					            $this->db->where('user_id',$loggedAccountID);
		    					            $this->db->update('user_new_fund_transfer',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

		    					            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
											$after_wallet_balance = $before_balance + $final_amount;    

								            $wallet_data = array(
								                'account_id'          => $domain_account_id,
								                'member_id'           => $loggedAccountID,    
								                'before_balance'      => $before_balance,
								                'amount'              => $final_amount,  
								                'after_balance'       => $after_wallet_balance,      
								                'status'              => 1,
								                'type'                => 1,   
								                'wallet_type'		  => 1,   
								                'created'             => date('Y-m-d H:i:s'),      
								                'description'         => 'Payout #'.$transaction_id.' Amount Refund.'
								            );

								            $this->db->insert('member_wallet',$wallet_data);
		    					            
		    					            
		    								
		    								$response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'FAILED',
		    											'txn_amount'=>$responseData['data']['txnValue'],
		    											'txnid' => $transaction_id,
		    											'rrn' =>null,
		    											'orderID' =>null,
		    											'opt_msg'=>$api_msg
		    								);
		    
		    							}
		    
		    						else
		    						{	
		    
		    							// save system log
		    				            $log_msg = '['.date('d-m-Y H:i:s').' - Payout API -  Transaction Pending From API Operator Side.]'.PHP_EOL;
		    				            $this->User->generateLog($log_msg);
		    
		    
		    				            $response = array(
		    									    'status_code' => 200,
		    										'status_msg' => lang('200_ERROR_MSG'),
		    										'status' => 'PENDING',
		    										'txnid' => $transaction_id,
		    										'rrn' => null,
		    										'orderID' =>$responseData['orderid'],
		    										'opt_msg' => 'Your transaction is under processing, status will be updated soon.'
		    							);
		    
		    						}
		    			            
		    						
		            		    }
								
		            		}
		            	}
		            	
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
				    'status_code' => 401,
					'status_msg' => 'Member ID And Password Not Valid.',
					'status' => 'FAILED'
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
				'status' =>'FAILED'
			
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,true);
		}

    }

    // Define a function that converts array to xml. 
	public function arrayToXml($array, $rootElement = null, $xml = null) { 
	    $_xml = $xml; 
	      
	    // If there is no Root Element then insert root 
	    if ($_xml === null) { 
	        $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<response/>'); 
	    } 
	      
	    // Visit all key value pair 
	    foreach ($array as $k => $v) { 
	          
	        // If there is nested array then 
	        if (is_array($v)) {  
	              
	            // Call function for nested array 
	            arrayToXml($v, $k, $_xml->addChild($k)); 
	            } 
	              
	        else { 
	              
	            // Simply add child element.  
	            $_xml->addChild($k, $v); 
	        } 
	    } 
	      
	    return $_xml->asXML(); 
	}

	public function aepsOnBoardAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Called.]'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateAepsLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];

				$activeService = $this->User->account_active_service($loggedAccountID);
				if(!in_array(3, $activeService)){
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Aeps Service Not Active for this member.]'.PHP_EOL;
			        $this->User->generateAepsLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => 'Sorry ! AEPS service is not activated.',
					);
				}
				else
				{
				
			        $this->load->library('form_validation');
			        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
			        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
			        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]');
			        $this->form_validation->set_rules('shop_name', 'Shop Name', 'required|xss_clean');
			        $this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');
			        $this->form_validation->set_rules('city_id', 'City', 'required|xss_clean');
			        $this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
			        $this->form_validation->set_rules('pin_code', 'PIN Code', 'required|xss_clean');
			        $this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|xss_clean');
			        $this->form_validation->set_rules('pancard_no', 'Pancard No', 'required|xss_clean');
			        $this->form_validation->set_rules('aadhar_photo', 'Aadhar Photo', 'required|xss_clean');
			        $this->form_validation->set_rules('pancard_photo', 'Pancard Photo', 'required|xss_clean');
			        $this->form_validation->set_rules('member_id', 'Member ID', 'required|xss_clean');
			        $this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
			        $this->form_validation->set_rules('txn_pin', 'Txn PIN', 'required|xss_clean');
			        if ($this->form_validation->run() == FALSE) {
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
						);
			        }
			        else
			        {
			        	
		            	// upload front document
						$aadhar_photo = '';
						if(isset($post['aadhar_photo']) && !empty($post['aadhar_photo']))
						{
		                    $encodedData = base64_encode(file_get_contents($post['aadhar_photo']));
		                    if(strpos($post['aadhar_photo'], ' ')){
		                        $encodedData = str_replace(' ','+', $post['aadhar_photo']);
		                    }
		                    $profile = base64_decode($encodedData);
		                    $file_name = time().rand(1111,9999).'.jpg';
						
							$profile_img_name = AEPS_KYC_PHOTO_SERVER_PATH.$file_name;
		                    $aadhar_photo = 'media/aeps_kyc_doc/'.$file_name;
		                    file_put_contents($profile_img_name, $profile);
		                }
						
						// upload back document
						$pancard_photo = '';
						if(isset($post['pancard_photo']) && !empty($post['pancard_photo']))
						{
		                    $encodedData = base64_encode(file_get_contents($post['pancard_photo']));
		                    if(strpos($post['pancard_photo'], ' ')){
		                        $encodedData = str_replace(' ','+', $post['pancard_photo']);
		                    }
		                    $profile = base64_decode($encodedData);
		                    $file_name = time().rand(1111,9999).'.jpg';
						
							$profile_img_name = AEPS_KYC_PHOTO_SERVER_PATH.$file_name;
		                    $pancard_photo = 'media/aeps_kyc_doc/'.$file_name;
		                    file_put_contents($profile_img_name, $profile);
		                }

		                $statusCheckResponse = $this->Aeps_model->checkAepsStatusLive($loggedAccountID);
						if($statusCheckResponse == true)
						{
							$response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => 'EKYC APPROVED ALREADY'
							);
						}
						else
						{
							$apiResponse = $this->Aeps_model->activeAEPSMember($post,$aadhar_photo,$pancard_photo,$loggedAccountID);
							// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Response - '.json_encode($apiResponse).'.]'.PHP_EOL;
					        $this->User->generateAepsLog($log_msg);
							$status = $apiResponse['status'];

							if($status == 1)
							{
								$response = array(
									'status_code' => 200,
									'status_msg' => lang('200_ERROR_MSG'),
									'status' => 'SUCCESS'
								);
							}
							else
							{
								$response = array(
									'status_code' => 200,
									'status_msg' => $apiResponse['msg'],
									'status' => 'FAILED'
								);
							}
						}
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateAepsLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateAepsLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateAepsLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    public function aepsOnBoardSendOtp()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Send OTP API Called.]'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Send OTP API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Send OTP API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Send OTP API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateAepsLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$activeService = $this->User->account_active_service($loggedAccountID);
				if(!in_array(3, $activeService)){
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Aeps Service Not Active for this member.]'.PHP_EOL;
			        $this->User->generateAepsLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => 'Sorry ! AEPS service is not activated.',
					);
				}
				else
				{
			        $this->load->library('form_validation');
			        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]');
			        $this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|xss_clean');
			        $this->form_validation->set_rules('pancard_no', 'Pancard No', 'required|xss_clean');
			        $this->form_validation->set_rules('member_id', 'Member ID', 'required|xss_clean');
			        $this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
			        $this->form_validation->set_rules('txn_pin', 'Txn PIN', 'required|xss_clean');
			        if ($this->form_validation->run() == FALSE) {
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Send OTP API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
						);
			        }
			        else
			        {
			        	
		            	$apiResponse = $this->Aeps_model->aepsSendOtp($post,$loggedAccountID);
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Send OTP API Response - '.json_encode($apiResponse).'.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
						$status = $apiResponse['status'];

						if($status == 1)
						{
							$response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => 'SUCCESS',
								'primaryKeyId' => $apiResponse['primaryKeyId'],
								'encodeFPTxnId' => $apiResponse['encodeFPTxnId']
							);
						}
						else
						{
							$response = array(
								'status_code' => 200,
								'status_msg' => $apiResponse['msg'],
								'status' => 'FAILED'
							);
						}
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Send OTP API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateAepsLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Send OTP API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateAepsLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Send OTP API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateAepsLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    public function aepsOnBoardResendOtp()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Resend OTP API Called.]'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Resend OTP API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Resend OTP API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Resend OTP API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateAepsLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$activeService = $this->User->account_active_service($loggedAccountID);
				if(!in_array(3, $activeService)){
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Aeps Service Not Active for this member.]'.PHP_EOL;
			        $this->User->generateAepsLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => 'Sorry ! AEPS service is not activated.',
					);
				}
				else
				{
			        $this->load->library('form_validation');
			        $this->form_validation->set_rules('member_id', 'Member ID', 'required|xss_clean');
			        $this->form_validation->set_rules('primaryKeyId', 'Email', 'required|xss_clean');
			        $this->form_validation->set_rules('encodeFPTxnId', 'Txn PIN', 'required|xss_clean');
			        if ($this->form_validation->run() == FALSE) {
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Resend OTP API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
						);
			        }
			        else
			        {
			        	
		            	$apiResponse = $this->Aeps_model->aepsResendOtp($post,$loggedAccountID);
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Resend OTP API Response - '.json_encode($apiResponse).'.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
						$status = $apiResponse['status'];

						if($status == 1)
						{
							$response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => 'SUCCESS',
								'primaryKeyId' => $apiResponse['primaryKeyId'],
								'encodeFPTxnId' => $apiResponse['encodeFPTxnId']
							);
						}
						else
						{
							$response = array(
								'status_code' => 200,
								'status_msg' => $apiResponse['msg'],
								'status' => 'FAILED'
							);
						}
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Resend OTP API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateAepsLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Resend OTP API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateAepsLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Resend OTP API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateAepsLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    public function aepsOnBoardOtpAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board OTP Auth API Called.]'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board OTP Auth API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board OTP Auth API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board OTP Auth API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateAepsLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$activeService = $this->User->account_active_service($loggedAccountID);
				if(!in_array(3, $activeService)){
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Aeps Service Not Active for this member.]'.PHP_EOL;
			        $this->User->generateAepsLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => 'Sorry ! AEPS service is not activated.',
					);
				}
				else
				{
			        $this->load->library('form_validation');
			        $this->form_validation->set_rules('member_id', 'Member ID', 'required|xss_clean');
			        $this->form_validation->set_rules('primaryKeyId', 'Email', 'required|xss_clean');
			        $this->form_validation->set_rules('encodeFPTxnId', 'Txn PIN', 'required|xss_clean');
			        $this->form_validation->set_rules('otp_code', 'OTP Code', 'required|xss_clean');
			        if ($this->form_validation->run() == FALSE) {
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board OTP Auth API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
						);
			        }
			        else
			        {
			        	
		            	$apiResponse = $this->Aeps_model->aepsOTPAuth($post,$loggedAccountID);
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board OTP Auth API Response - '.json_encode($apiResponse).'.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
						$status = $apiResponse['status'];

						if($status == 1)
						{
							$response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => 'SUCCESS'
							);
						}
						else
						{
							$response = array(
								'status_code' => 200,
								'status_msg' => $apiResponse['msg'],
								'status' => 'FAILED'
							);
						}
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board OTP Auth API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateAepsLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board OTP Auth API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateAepsLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board OTP Auth API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateAepsLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    public function aepsOnBoardBioAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Bio Auth API Called.]'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        $post2 = $post;
        $post2['BiometricData'] = 'Finger Print Data';
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Bio Auth API Post Data - '.json_encode($post2).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Bio Auth API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Bio Auth API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateAepsLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$activeService = $this->User->account_active_service($loggedAccountID);
				if(!in_array(3, $activeService)){
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Aeps Service Not Active for this member.]'.PHP_EOL;
			        $this->User->generateAepsLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => 'Sorry ! AEPS service is not activated.',
					);
				}
				else
				{
			        $this->load->library('form_validation');
			        $this->form_validation->set_rules('member_id', 'Member ID', 'required|xss_clean');
			        $this->form_validation->set_rules('primaryKeyId', 'Email', 'required|xss_clean');
			        $this->form_validation->set_rules('encodeFPTxnId', 'Txn PIN', 'required|xss_clean');
			        $this->form_validation->set_rules('BiometricData', 'BiometricData', 'required|xss_clean');
			        if ($this->form_validation->run() == FALSE) {
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Bio Auth API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
						);
			        }
			        else
			        {
			        	
		            	$apiResponse = $this->Aeps_model->aepsBioAuth($post,$loggedAccountID);
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Bio Auth API Response - '.json_encode($apiResponse).'.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
						$status = $apiResponse['status'];

						if($status == 1)
						{
							$response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => 'SUCCESS'
							);
						}
						else
						{
							$response = array(
								'status_code' => 200,
								'status_msg' => $apiResponse['msg'],
								'status' => 'FAILED'
							);
						}
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Bio Auth API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateAepsLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Bio Auth API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateAepsLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board Bio Auth API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateAepsLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    public function aepsTxnAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps Txn Auth API Called.]'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        $post2 = $post;
        $post2['biometricData'] = 'Finger Print Data';
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps Txn Auth API Post Data - '.json_encode($post2).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps Txn Auth API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps Txn Auth API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateAepsLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				/*$activeService = $this->User->account_active_service($loggedAccountID);*/
				$activeService = array();
				$activeService[0] = 100;
				if(!in_array(3, $activeService)){
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Aeps Service Not Active for this member.]'.PHP_EOL;
			        $this->User->generateAepsLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => 'Sorry Your account is in under reviewed for AEPS withdrawal, Aadhar transaction as per new NPCI guidelines, wait till it gets approved.',
					);
				}
				else
				{
			        $this->load->library('form_validation');
			        $this->form_validation->set_rules('member_id', 'Member ID', 'required|xss_clean');
			        $this->form_validation->set_rules('txn_pin', 'Txn PIN', 'required|xss_clean');
			        $this->form_validation->set_rules('serviceType', 'Service Type', 'required|xss_clean');
			        $this->form_validation->set_rules('deviceIMEI', 'deviceIMEI', 'xss_clean');
			        $this->form_validation->set_rules('aadharNumber', 'Aadhar No', 'required|xss_clean');
			        $this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
			        $this->form_validation->set_rules('biometricData', 'BiometricData', 'required|xss_clean');
			        $this->form_validation->set_rules('amount', 'Amount', 'required|xss_clean');
			        $this->form_validation->set_rules('iin', 'IIN', 'required|xss_clean');
			        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|xss_clean');
			        if ($this->form_validation->run() == FALSE) {
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps Txn Auth API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
						);
			        }
			        else
			        {
			        	
		            	
						$member_code = $post['member_id'];
	        			$member_pin = md5($post['txn_pin']);
	        			$serviceType = $post['serviceType'];
						$deviceIMEI = $post['deviceIMEI'];
						$aadharNumber = $post['aadharNumber'];
						$mobile = $post['mobile'];
						$biometricData = $post['biometricData'];
						$amount = $post['amount'];
						$iin = $post['iin'];
						$txnID = $post['txnID'];

						/*
						$txnID = uniqid('BINQ' . time()); -- Balance Info
						$txnID = uniqid('MNST' . time()); -- Mini Statement
						$txnID = uniqid('CSWD' . time()); -- Cash Withdrawal
						$txnID = uniqid('APAY' . time()); -- Aadhar Pay

						$serviceType = balinfo,ministatement,balwithdraw,aadharpay
						*/

						$requestTime = date('Y-m-d H:i:s');
						if($aadharNumber && $mobile && $biometricData && $iin)
						{
							if($serviceType == 'balinfo' || $serviceType == 'ministatement')
							{
								$txnType = 'BE';
								$remarks = 'Balance Inquiry';
								$is_bal_info = 1;
								$is_withdrawal = 0;
								$Servicestype = 'GetBalanceaeps';
								$api_url = AEPS_BALANCE_INQIRY_API_URL;
								if($serviceType == 'ministatement')
								{
									$Servicestype = 'getministatment';
									$is_bal_info = 0;
									$txnType = 'MS';
									$remarks = 'Mini Statement';
									$api_url = AEPS_MINI_STATEMENT_API_URL;
								}
								if($amount == 0)
								{
									
									$bmPIData   = simplexml_load_string($biometricData);
									$xmlarray = json_decode(json_encode((array) $bmPIData), true);

									$serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
									$piddatatype = $bmPIData->Data[0]['type'];
									$ci = $bmPIData->Skey[0]['ci'];
									if($xmlarray['Resp']['@attributes']['errCode'] == 0)
									{
										$captureData = array(
											'errCode' => $xmlarray['Resp']['@attributes']['errCode'],
											'errInfo' => $xmlarray['Resp']['@attributes']['errInfo'],
											'fCount' => $xmlarray['Resp']['@attributes']['fCount'],
											'fType' => $xmlarray['Resp']['@attributes']['fType'],
											'iCount' => $xmlarray['Resp']['@attributes']['iCount'],
											'iType' => null,
											'pCount' => $xmlarray['Resp']['@attributes']['pCount'],
											'pType' => "0",
											'nmPoints' => $xmlarray['Resp']['@attributes']['nmPoints'],
											'qScore' => $xmlarray['Resp']['@attributes']['qScore'],
											'dpID' => $xmlarray['DeviceInfo']['@attributes']['dpId'],
											'rdsID' => $xmlarray['DeviceInfo']['@attributes']['rdsId'],
											'rdsVer' => $xmlarray['DeviceInfo']['@attributes']['rdsVer'],
											'dc' => $xmlarray['DeviceInfo']['@attributes']['dc'],
											'mi' => $xmlarray['DeviceInfo']['@attributes']['mi'],
											'mc' => $xmlarray['DeviceInfo']['@attributes']['mc'],
											'ci' => $ci,
											'sessionKey' => $xmlarray['Skey'],
											'Skey' => $xmlarray['Skey'],
											'hmac' => $xmlarray['Hmac'],
											'PidDatatype' => $piddatatype,
											'Piddata' => $xmlarray['Data']
										);
										$captureData = json_decode(json_encode((array) $captureData), true);
										$captureData['ci'] = $captureData['ci'][0];
										$captureData['PidDatatype'] = $captureData['PidDatatype'][0];

										// Create Data
										$data = array 
										(
										    "cardnumberORUID" => array(
										    	"nationalBankIdentificationNumber" => $iin,
										    	"indicatorforUID" => "0",
										    	"adhaarNumber" => $aadharNumber
										    ),
										    "captureResponse" => $captureData,
										    "languageCode" => "en",
										    "latitude"=>"22.9734229",
										    "longitude"=>"78.6568942",
										    "mobileNumber" => $mobile,
										    "paymentType" => "B",
										    "requestRemarks" => $remarks,
										    "timestamp" => date('d/m/Y H:i:s'),
										    "merchantUserName" => $member_code,
										    "merchantPin" => $member_pin,
										    "subMerchantId" => "",
										    "superMerchantId" => $accountData['aeps_supermerchant_id'],
										    "transactionType" => $txnType
										);
										if($serviceType == 'balinfo')
										{
											$data["merchantTransactionId"] = $txnID;
										}
										else
										{
											$data["merchantTranId"] = $txnID;
										}

										// Generate JSON
										$json = json_encode($data);

										// Generate Session Key
										$key = '';
										$mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
										foreach ($mt_rand as $chr)
										{             $key .= chr($chr);         }

										// Read Public Key
										$pub_key_string = $accountData['aeps_certificate'];

										// Encrypt using Public Key
										openssl_public_encrypt($key, $crypttext, $pub_key_string);

										// Create Header
										$header = array
										(
										    'Content-type: application/json',
										    'trnTimestamp: ' . date('d/m/Y H:i:s'),
										    'hash: ' . base64_encode(hash('sha256', $json, true)),
										    'eskey: ' . base64_encode($crypttext),
										    'deviceIMEI:'.$serialno
										);

										// Initialization Vector
										$iv =   '06f2f04cc530364f';

										// Encrypt using AES-128
										$ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

										// Create Body
										$request = base64_encode($ciphertext_raw);

										// Initialize
										$curl = curl_init();

										//Set Options - Open

										// URL
										curl_setopt($curl, CURLOPT_URL, $api_url);

										// Return Transfer
										curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

										// SSL Verify Peer
										curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

										// SSL Verify Host
										curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

										// Timeout
										curl_setopt($curl, CURLOPT_TIMEOUT, 30);

										// HTTP Version
										curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

										// Request Method
										curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

										// Request Header
										curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

										// Request Body
										curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

										// Set Options - Close

										// Execute
										$output = curl_exec($curl);

										// Close
										curl_close ($curl);

								        $responseData = json_decode($output,true);

								        $apiData = array(
								        	'account_id' => $domain_account_id,
		            						'user_id' => $loggedAccountID,
								        	'api_url' => $api_url,
								        	'api_response' => $output,
								        	'post_data' => json_encode($data),
								        	'header_data' => json_encode($header),
								        	'created' => date('Y-m-d H:i:s'),
								        	'created_by' => $loggedAccountID
								        );
								        $this->db->insert('aeps_api_response',$apiData);

								        if(isset($responseData['data']['responseCode']) && $responseData['data']['responseCode'] == '00' && $responseData['data']['bankRRN'] != '')
								        {
								        	$statementList = $responseData['data']['miniStatementStructureModel'];
								        	$balanceAmount = $responseData['data']['balanceAmount'];
								        	$bankRRN = $responseData['data']['bankRRN'];
								        	$recordID = $this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$loggedAccountID,$member_code,$statementList,$balanceAmount,$bankRRN);
								        	$str = '';
								        	if($is_bal_info == 0)
											{
												$this->Aeps_model->addStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID,$loggedAccountID);
												$statementList = $responseData['data']['miniStatementStructureModel'];
												$response = array(
													'status_code' => 200,
													'status_msg' => lang('200_ERROR_MSG'),
													'status' => 'SUCCESS',
													'balanceAmount' => $responseData['data']['balanceAmount'],
													'bankRRN' => $responseData['data']['bankRRN'],
													'data' => $statementList
												);
												
											}
											else
											{
												$response = array(
													'status_code' => 200,
													'status_msg' => lang('200_ERROR_MSG'),
													'status' => 'SUCCESS',
													'balanceAmount' => $responseData['data']['balanceAmount'],
													'bankRRN' => $responseData['data']['bankRRN']
												);
											}
								        	

								        }
								        else
								        {
								        	$this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3,$loggedAccountID,$member_code);
								        	$response = array(
												'status_code' => 200,
												'status_msg' => $responseData['message'],
												'status' => 'FAILED'
											);
								        }
								    }
								    else
								    {
								    	$response = array(
											'status_code' => 401,
											'status_msg' => 'Sorry ! Device is not connected, please connect it.',
										);
								    }
								}
								else
								{
									$response = array(
										'status_code' => 401,
										'status_msg' => 'Sorry ! Amount is not valid.',
									);
								}
							}
							elseif($serviceType == 'balwithdraw' || $serviceType == 'aadharpay')
							{
								$txnType = 'CW';
								$remarks = 'Withdrawal';
								$api_url = AEPS_WITHDRAWAL_API_URL;
								$is_withdrawal = 1;
								$is_bal_info = 0;
								$Servicestype = 'AccountWithdrowal';
								if($serviceType == 'aadharpay')
								{
									$Servicestype = 'Aadharpay';
									$txnType = 'M';
									$remarks = 'Aadharpay';
									$api_url = AEPS_AADHAR_PAY_API_URL;
								}
								
								if($amount >= 100 && $amount <= 10000)
								{

									$bmPIData   = simplexml_load_string($biometricData);
									$xmlarray = json_decode(json_encode((array) $bmPIData), true);

									$serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
									$piddatatype = $bmPIData->Data[0]['type'];
									$ci = $bmPIData->Skey[0]['ci'];
									if($xmlarray['Resp']['@attributes']['errCode'] == 0)
									{
										$captureData = array(
											'errCode' => $xmlarray['Resp']['@attributes']['errCode'],
											'errInfo' => $xmlarray['Resp']['@attributes']['errInfo'],
											'fCount' => $xmlarray['Resp']['@attributes']['fCount'],
											'fType' => $xmlarray['Resp']['@attributes']['fType'],
											'iCount' => $xmlarray['Resp']['@attributes']['iCount'],
											'iType' => null,
											'pCount' => $xmlarray['Resp']['@attributes']['pCount'],
											'pType' => "0",
											'nmPoints' => $xmlarray['Resp']['@attributes']['nmPoints'],
											'qScore' => $xmlarray['Resp']['@attributes']['qScore'],
											'dpID' => $xmlarray['DeviceInfo']['@attributes']['dpId'],
											'rdsID' => $xmlarray['DeviceInfo']['@attributes']['rdsId'],
											'rdsVer' => $xmlarray['DeviceInfo']['@attributes']['rdsVer'],
											'dc' => $xmlarray['DeviceInfo']['@attributes']['dc'],
											'mi' => $xmlarray['DeviceInfo']['@attributes']['mi'],
											'mc' => $xmlarray['DeviceInfo']['@attributes']['mc'],
											'ci' => $ci,
											'sessionKey' => $xmlarray['Skey'],
											'Skey' => $xmlarray['Skey'],
											'hmac' => $xmlarray['Hmac'],
											'PidDatatype' => $piddatatype,
											'Piddata' => $xmlarray['Data']
										);
										$captureData = json_decode(json_encode((array) $captureData), true);
										$captureData['ci'] = $captureData['ci'][0];
										$captureData['PidDatatype'] = $captureData['PidDatatype'][0];
										
										// Create Data
										$data = array 
										(
										    "cardnumberORUID" => array(
										    	"nationalBankIdentificationNumber" => $iin,
										    	"indicatorforUID" => "0",
										    	"adhaarNumber" => $aadharNumber
										    ),
										    "captureResponse" => $captureData,
										    "languageCode" => "en",
										    "latitude"=>"22.9734229",
										    "longitude"=>"78.6568942",
										    "mobileNumber" => $mobile,
										    "paymentType" => "B",
										    "requestRemarks" => $remarks,
										    "timestamp" => date('d/m/Y H:i:s'),
										    "merchantUserName" => $member_code,
										    "merchantPin" => $member_pin,
										    "subMerchantId" => "",
										    "superMerchantId" => $accountData['aeps_supermerchant_id'],
										    "transactionType" => $txnType,
										    "merchantTranId" => $txnID,
										    "transactionAmount" => $amount
										);
										
										// Generate JSON
										$json = json_encode($data);

										// Generate Session Key
										$key = '';
										$mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
										foreach ($mt_rand as $chr)
										{             $key .= chr($chr);         }

										// Read Public Key
										$pub_key_string = $accountData['aeps_certificate'];

										// Encrypt using Public Key
										openssl_public_encrypt($key, $crypttext, $pub_key_string);

										// Create Header
										$header = array
										(
										    'Content-type: application/json',
										    'trnTimestamp: ' . date('d/m/Y H:i:s'),
										    'hash: ' . base64_encode(hash('sha256', $json, true)),
										    'eskey: ' . base64_encode($crypttext),
										    'deviceIMEI:'.$serialno
										);

										// Initialization Vector
										$iv =   '06f2f04cc530364f';

										// Encrypt using AES-128
										$ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

										// Create Body
										$request = base64_encode($ciphertext_raw);

										// Initialize
										$curl = curl_init();

										//Set Options - Open

										// URL
										curl_setopt($curl, CURLOPT_URL, $api_url);

										// Return Transfer
										curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

										// SSL Verify Peer
										curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

										// SSL Verify Host
										curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

										// Timeout
										curl_setopt($curl, CURLOPT_TIMEOUT, 30);

										// HTTP Version
										curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

										// Request Method
										curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

										// Request Header
										curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

										// Request Body
										curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

										// Set Options - Close

										// Execute
										$output = curl_exec($curl);

										// Close
										curl_close ($curl);

								        $responseData = json_decode($output,true);

								        $apiData = array(
								        	'account_id' => $domain_account_id,
		            						'user_id' => $loggedAccountID,
								        	'api_url' => $api_url,
								        	'api_response' => $output,
								        	'post_data' => json_encode($data),
								        	'header_data' => json_encode($header),
								        	'created' => date('Y-m-d H:i:s'),
								        	'created_by' => $loggedAccountID
								        );
								        $this->db->insert('aeps_api_response',$apiData);

								        if(isset($responseData['data']['responseCode']) && $responseData['data']['responseCode'] == '00' && $responseData['data']['bankRRN'] != '')
								        {
								        	$balanceAmount = $responseData['data']['balanceAmount'];
								        	$bankRRN = $responseData['data']['bankRRN'];
								        	$transactionAmount = $responseData['data']['transactionAmount'];
								        	$statementList = array();
								        	$recordID = $this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$loggedAccountID,$member_code,$statementList,$balanceAmount,$bankRRN,$transactionAmount);
								        	$this->Aeps_model->addBalance($txnID,$aadharNumber,$iin,$amount,$recordID,$serviceType,$loggedAccountID);
								        	
											$response = array(
												'status_code' => 200,
												'status_msg' => lang('200_ERROR_MSG'),
												'status' => 'SUCCESS',
												'transactionAmount' => $responseData['data']['transactionAmount'],
												'balanceAmount' => $responseData['data']['balanceAmount'],
												'bankRRN' => $responseData['data']['bankRRN'],
												'data' => $statementList
											);
								        	
								        }
								        else
								        {
								        	$this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3,$loggedAccountID,$member_code);
								        	$response = array(
												'status_code' => 200,
												'status_msg' => $responseData['message'],
												'status' => 'FAILED'
											);
								        }
								    }
								    else
								    {
								    	$response = array(
											'status_code' => 401,
											'status_msg' => 'Sorry ! Device is not connected, please connect it.',
										);
								    }

							        
							       
								}
								else
								{
									$response = array(
										'status_code' => 401,
										'status_msg' => 'Sorry ! Amount should be less than 10000 and grater than or equal 101.',
									);
								}
							}
							else
							{
								$response = array(
									'status_code' => 401,
									'status_msg' => 'Somethis Wrong ! Please Try Again Later.',
								);
								
							}
						}
						else
						{
							$response = array(
									'status_code' => 401,
									'status_msg' => 'Sorry ! Please enter required data.',
								);
									
						}

			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps Txn Auth API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateAepsLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps Txn Auth API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateAepsLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Aeps Txn Auth API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateAepsLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    function maximumCheck($num)
    {
    	$this->load->library('form_validation');
        if ($num < 1)
        {
            $this->form_validation->set_message(
                            'maximumCheck',
                            'The %s field must be grater than 0'
                        );
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function cashDepositeAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite Auth API Called.]'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite Auth API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite Auth API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite Auth API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateAepsLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$collection_wallet_balance = $getAccountData['wallet_balance'];
				/*$activeService = $this->User->account_active_service($loggedAccountID);*/
				$activeService = array();
				$activeService[0] = 100;
				if(!in_array(3, $activeService)){
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Aeps Service Not Active for this member.]'.PHP_EOL;
			        $this->User->generateAepsLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => 'Sorry Your account is in under reviewed for AEPS withdrawal, Aadhar transaction as per new NPCI guidelines, wait till it gets approved.',
					);
				}
				else
				{
			        $this->load->library('form_validation');
			        $this->form_validation->set_rules('member_id', 'Member ID', 'required|xss_clean');
			        $this->form_validation->set_rules('txn_pin', 'Txn PIN', 'required|xss_clean');
			        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]|min_length[10]');
			        $this->form_validation->set_rules('account_no', 'Account No', 'required|xss_clean');
			        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');
			        $this->form_validation->set_rules('remark', 'Remark', 'required|xss_clean');
			        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|xss_clean');
			        if ($this->form_validation->run() == FALSE) {
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite Auth API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
						);
			        }
			        else
			        {
			        	$commisionData = $this->User->get_aeps_commission($post['amount'],$loggedAccountID,4);
				        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
				        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

				        $final_deduct_amount = $post['amount'];
				        if($is_surcharge)
				        {
				        	$final_deduct_amount = $post['amount'] + $com_amount;
				        }

				        $collection_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

						if($collection_wallet_balance < $final_deduct_amount)
						{
							$response = array(
								'status_code' => 401,
								'status_msg' => lang('401_WALLET_BALANCE_ERROR'),
							);
						}
						else
						{
							$admin_id = $this->User->get_admin_id($domain_account_id);
							$admin_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

							$adminCommisionData = $this->User->get_admin_aeps_commission($post['amount'],$domain_account_id,4);
					        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
					        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

					        $final_deduct_admin_amount = $post['amount'];
					        if($admin_is_surcharge)
					        {
					        	$final_deduct_admin_amount = $post['amount'] + $admin_com_amount;
					        }

							if($admin_wallet_balance < $final_deduct_admin_amount)
							{
								$response = array(
									'status_code' => 401,
									'status_msg' => lang('401_ADMIN_WALLET_BALANCE_ERROR'),
								);
							}
							else
							{
								$apiResponse = $this->Aeps_model->sendCashDepositeOtp($post,$loggedAccountID);
								// save system log
						        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite Auth API Response - '.json_encode($apiResponse).'.]'.PHP_EOL;
						        $this->User->generateAepsLog($log_msg);
								$status = $apiResponse['status'];

								if($status == 1)
								{
									$response = array(
										'status_code' => 200,
										'status_msg' => lang('200_ERROR_MSG'),
										'status' => 'SUCCESS',
										'txnID' => $apiResponse['txnID']
									);
								}
								else
								{
									$response = array(
										'status_code' => 200,
										'status_msg' => $apiResponse['msg'],
										'status' => 'FAILED'
									);
								}
							}
						}

		            	
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite Auth API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateAepsLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite Auth API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateAepsLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite Auth API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateAepsLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }

    public function cashDepositeOtpAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite OTP Auth API Called.]'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite OTP Auth API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite OTP Auth API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateAepsLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite OTP Auth API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateAepsLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$collection_wallet_balance = $getAccountData['wallet_balance'];
				/*$activeService = $this->User->account_active_service($loggedAccountID);*/
				$activeService = array();
				$activeService[0] = 100;
				if(!in_array(3, $activeService)){
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Aeps On Board API Aeps Service Not Active for this member.]'.PHP_EOL;
			        $this->User->generateAepsLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => 'Sorry Your account is in under reviewed for AEPS withdrawal, Aadhar transaction as per new NPCI guidelines, wait till it gets approved.',
					);
				}
				else
				{
			        $this->load->library('form_validation');
			        $this->form_validation->set_rules('member_id', 'Member ID', 'required|xss_clean');
			        $this->form_validation->set_rules('txn_pin', 'Txn PIN', 'required|xss_clean');
			        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|xss_clean');
			        $this->form_validation->set_rules('otp_code', 'OTP', 'required|xss_clean');
			        
			        if ($this->form_validation->run() == FALSE) {
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite OTP Auth API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateAepsLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
						);
			        }
			        else
			        {
			        	$txnid = $post['txnID'];
			        	$member_code = $post['member_id'];
			        	// check txnid valid or not
						$chk_txn_id = $this->db->get_where('cash_deposite_history',array('account_id'=>$domain_account_id,'member_id'=>$loggedAccountID,'member_code'=>$member_code,'txnid'=>$txnid,'status'=>1))->row_array();
						if(!$chk_txn_id)
						{
							$response = array(
								'status_code' => 401,
								'status_msg' => lang('401_TXNID_ERROR'),
							);
						}
						else
						{
							$commisionData = $this->User->get_aeps_commission($chk_txn_id['amount'],$loggedAccountID,4);
					        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
					        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

					        $final_deduct_amount = $chk_txn_id['amount'];
					        if($is_surcharge)
					        {
					        	$final_deduct_amount = $chk_txn_id['amount'] + $com_amount;
					        }

					        $collection_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

							if($collection_wallet_balance < $final_deduct_amount)
							{
								$response = array(
									'status_code' => 401,
									'status_msg' => lang('401_WALLET_BALANCE_ERROR'),
								);
							}
							else
							{
								$admin_id = $this->User->get_admin_id($domain_account_id);
								$admin_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

								$adminCommisionData = $this->User->get_admin_aeps_commission($chk_txn_id['amount'],$domain_account_id,4);
						        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
						        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

						        $final_deduct_admin_amount = $chk_txn_id['amount'];
						        if($admin_is_surcharge)
						        {
						        	$final_deduct_admin_amount = $chk_txn_id['amount'] + $admin_com_amount;
						        }

								if($admin_wallet_balance < $final_deduct_admin_amount)
								{
									$response = array(
										'status_code' => 401,
										'status_msg' => lang('401_ADMIN_WALLET_BALANCE_ERROR'),
									);
								}
								else
								{
									$apiResponse = $this->Aeps_model->verifyCashDepositeOtp($post,$loggedAccountID);
									// save system log
							        $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite OTP Auth API Response - '.json_encode($apiResponse).'.]'.PHP_EOL;
							        $this->User->generateAepsLog($log_msg);
									$status = $apiResponse['status'];

									if($status == 1)
									{
										$response = array(
											'status_code' => 200,
											'status_msg' => lang('200_ERROR_MSG'),
											'status' => 'SUCCESS',
											'bankRrn' => $apiResponse['bankRrn'],
											'txnid' => $apiResponse['txnid']
										);
									}
									else
									{
										$response = array(
											'status_code' => 200,
											'status_msg' => $apiResponse['msg'],
											'status' => 'FAILED'
										);
									}
								}
							}
						}
			        	

		            	
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite OTP Auth API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateAepsLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite OTP Auth API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateAepsLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Cash Deposite OTP Auth API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateAepsLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }




    // upi payout




    public function upiTransferAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
		    
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
		    
		    
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
			  
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();

				$user_email = 	$getAccountData['email'];		
				$loggedAccountID = $getAccountData['id'];
				$api_member_code = $getAccountData['user_code'];
				$before_wallet_balance = $getAccountData['wallet_balance'];
				$min_wallet_balance = $getAccountData['min_wallet_balance'];
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
		        $this->load->library('form_validation');
		        
				$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
				$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
				//$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
		        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
		        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
		        if ($this->form_validation->run() == FALSE) {
		            
		           
		        	// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API Post Parameters Not Valid.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => lang('400_ERROR_MSG'),
						'status' => 'FAILED',
						'txn_amount' =>$post['amount'],
						'transaction_id' => strval($post['txnID'])
					);
		        }
		        else
		        {
		           
		        	
					$account_holder_name = $post['account_holder_name'];
					$account_no = $post['account_no'];
					//$ifsc = $post['ifsc'];
					$amount = $post['amount'];
					$transaction_id =  $post['txnID']."";
					$mode = 'UPI';

					

					// get dmr surcharge
            		$surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
            		// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Member Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);

			        $final_amount = $amount + $surcharge_amount;

			        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
                    
                    if($before_wallet_balance < $final_amount){
		                // save system log
		                $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API Member Insufficient Wallet Error]'.PHP_EOL;
		                $this->User->generateLog($log_msg);
		                
		                $response = array(
							'status_code' => 401,
							'status_msg' => lang('401_WALLET_BALANCE_ERROR'),
							'status' => 'FAILED',
    						'txn_amount' =>$post['amount'],
    						'txnid' =>$transaction_id,
    						'rrn' =>null,
							'orderID' =>null,
							'opt_msg'=>'Insufficient Wallet Balance'

							
						);
            		}
            		else
            		{
            			
				        $api_url = INSTANTPAY_PAYOUT_API_URL;
			// save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - UPI Payout API URL - '.$api_url.']'.PHP_EOL;
            $this->User->generateLog($log_msg);
				

				$request = array(
		                
		                'payer' => array(
    		                'bankId' => '0',
    		                'bankProfileId' => 0,
    		                'accountNumber' => $accountData['instant_account_no'],
    		                ),
    		                
	                        'payee' => array(
	                            'name' => $account_holder_name,
	                            'accountNumber' => $account_no,
	                            'bankIfsc' =>''
	                       ),
	                       'transferMode' => $mode,
	                       'transferAmount' => $amount,
	                       'externalRef' => $transaction_id,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       'remarks'  => 'UPI Payout',
	                       'alertEmail' => $user_email,
	                       'purpose' =>'REIMBURSEMENT'
		                
		            );



		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                
		                'content-type: application/json'
		                
		            );
		            
		       
		            $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $api_url);

		            // Return Transfer
		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		            // SSL Verify Peer
		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		            // SSL Verify Host
		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		            // Timeout
		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		            // HTTP Version
		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		            // Request Method
		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		            // Request Body
		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

		            // Execute
		           $output = curl_exec($curl);
	       	
	        		curl_close ($curl);

	        		$responseData = json_decode($output,true);


				        // save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - UPI Payout API Response - '.$output.']'.PHP_EOL;
				            $this->User->generateLog($log_msg);

							// save api response
							$apiData = array(
								'account_id' => $domain_account_id,
								'user_id' => $loggedAccountID,
								'api_response' => $output,
								'api_url' => $api_url,
								'post_data'=>json_encode($request),
								'created' => date('Y-m-d H:i:s'),
								'created_by'=>$loggedAccountID
							);
							$this->db->insert('instantpay_api_response',$apiData);

							


			            $after_wallet_balance = $before_wallet_balance - $final_amount;    

					
							if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
							{
								
								$api_msg = $responseData['status'];

					             $log_msg = '['.date('d-m-Y H:i:s').' -  UPI Payout Transfer API - DMT Transaction Success.]'.PHP_EOL;

					            $this->User->generateLog($log_msg);

					            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

			                    $wallet_data = array(
			                        'account_id'          => $domain_account_id,
			                        'member_id'           => $loggedAccountID,    
			                        'before_balance'      => $before_wallet_balance,
			                        'amount'              => $final_amount,  
			                        'after_balance'       => $after_wallet_balance,      
			                        'status'              => 1,
			                        'type'                => 2, 
			                        'wallet_type'         => 1,      
			                        'created'             => date('Y-m-d H:i:s'),      
			                        'description'         => 'UPI Payout #'.$transaction_id.' Amount Deducted.'
			                    );

			                    $this->db->insert('member_wallet',$wallet_data);


			                    // save system log
					            $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API -  Transaction Wallet Deducation Done.]'.PHP_EOL;
					            $this->User->generateLog($log_msg);

								$data = array(
									'account_id' => $domain_account_id,
									'user_id' => $loggedAccountID,
									'transfer_amount' => $amount,
									'transfer_charge_amount' => $surcharge_amount,
									'total_wallet_charge' => $final_amount,
									'after_wallet_balance' => $after_wallet_balance,
									'transaction_id' => $transaction_id,
									'encode_transaction_id' => do_hash($transaction_id),
									'api_response' => $output,
									'status' => 3,
									'memberID' => $memberID,
									'rrn' =>$responseData['data']['txnReferenceId'],
									'mobile' => $mobile,
									'account_holder_name' => $account_holder_name,
									'account_no' => $account_no,
									//'ifsc' => $ifsc,
									'txnType'=>$mode,
									'created' => date('Y-m-d H:i:s')
								);
								$this->db->insert('user_new_fund_transfer',$data);

								$response = array(
											'status_code' => 200,
											'status_msg' => lang('200_ERROR_MSG'),
											'status' => 'SUCCESS',
											'txn_amount'=>$responseData['data']['txnValue'],
											'txnid' => "$transaction_id",
											'rrn' =>$responseData['data']['txnReferenceId'],
											'orderID' =>$responseData['orderid'],
											'opt_msg'=>$api_msg
								);

								
							}

							elseif(isset($responseData['statuscode']) && $responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR')
							{
								
								$api_msg = $responseData['status'];

					             $log_msg = '['.date('d-m-Y H:i:s').' - Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;

					            $this->User->generateLog($log_msg);
			                    
								$data = array(
									'account_id' => $domain_account_id,
									'user_id' => $loggedAccountID,
									'transfer_amount' => $amount,
									'transfer_charge_amount' => $surcharge_amount,
									'total_wallet_charge' => $final_amount,
									'after_wallet_balance' => $after_wallet_balance,
									'transaction_id' => $transaction_id,
									'encode_transaction_id' => do_hash($transaction_id),
									'api_response' => $output,
									'status' => 4,
									'memberID' => $memberID,
									'mobile' => $mobile,
									'account_holder_name' => $account_holder_name,
									'account_no' => $account_no,
									//'ifsc' => $ifsc,
									'txnType'=>$mode,
									'created' => date('Y-m-d H:i:s')
								);
								$this->db->insert('user_new_fund_transfer',$data);

								$response = array(
											'status_code' => 200,
											'status_msg' => lang('200_ERROR_MSG'),
											'status' => 'FAILED',
											'txn_amount'=>$responseData['data']['txnValue'],
											'txnid' => $transaction_id,
											'rrn' =>null,
											'orderID' =>null,
											'opt_msg'=>$api_msg
								);

							}

						else
						{	

							$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

							$wallet_data = array(
			                        'account_id'          => $domain_account_id,
			                        'member_id'           => $loggedAccountID,    
			                        'before_balance'      => $before_wallet_balance,
			                        'amount'              => $final_amount,  
			                        'after_balance'       => $after_wallet_balance,
			                        'status'              => 1,
			                        'type'                => 2, 
			                        'wallet_type'         => 1,      
			                        'created'             => date('Y-m-d H:i:s'),      
			                        'description'         => 'UPI Payout #'.$transaction_id.' Amount Deducted.'
			                    );

			                    $this->db->insert('member_wallet',$wallet_data);

			                    

			                     $log_msg = '['.date('d-m-Y H:i:s').' -  UPI Payout API -  Transaction Wallet Deducation Done.]'.PHP_EOL;
					            $this->User->generateLog($log_msg);

									$data = array(
							    'account_id' => $domain_account_id,
								'user_id' => $loggedAccountID,
								'transfer_amount' => $amount,
								'transfer_charge_amount' => $surcharge_amount,
								'total_wallet_charge' => $final_amount,
								'after_wallet_balance' => $after_wallet_balance,
								'transaction_id' => $transaction_id,
								'encode_transaction_id' => do_hash($transaction_id),
								'api_response' => $output,
								'status' => 2,
								'invoice_no'=>$receipt_id,
								'memberID' => $memberID,
								'mobile' => $mobile,						
								'account_holder_name' => $account_holder_name,
								'account_no' => $account_no,
								//'ifsc' => $ifsc,
								'txnType'=>$mode,
								'created' => date('Y-m-d H:i:s')
							);
						$this->db->insert('user_new_fund_transfer',$data);

							// save system log
				            $log_msg = '['.date('d-m-Y H:i:s').' - UPi Payout API -  Transaction Pending From API Operator Side.]'.PHP_EOL;
				            $this->User->generateLog($log_msg);


				            $response = array(
									    'status_code' => 200,
										'status_msg' => lang('200_ERROR_MSG'),
										'status' => 'PENDING',
										'txnid' => $transaction_id,
										'rrn' => null,
										'orderID' =>$responseData['orderid'],
										'opt_msg' => 'Your transaction is under processing, status will be updated soon.'
							);

						}
						
            		}
	            	
		    	}

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
				    'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
					'status' => 'FAILED',
					'txn_amount'=>$responseData['data']['txnValue'],
					'txnid' => $transaction_id,
					'rrn' =>null,
					'orderID' =>$responseData['orderid'],
					'opt_msg'=>' Member ID And Password Not Valid.'
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - UPi Payout API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
				'status' =>'FAILED',
			
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,true);
		}

    }

    public function generateQrAuth2()
	{
		$log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API Called.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
    	//get logged user info
        //$domain_account_id = $this->User->get_domain_account();
        $domain_account_id = 1;

        $get = $this->input->get();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API Get Data - '.json_encode($get).']'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
        
		$memberID = isset($get['memberid']) ? $get['memberid'] : '';
        $txnPwd = isset($get['txnpwd']) ? $get['txnpwd'] : '';
        $name = isset($get['name']) ? $get['name'] : '';
        $amount = isset($get['amount']) ? $get['amount'] : 0;
        $txnid = isset($get['txnid']) ? $get['txnid'] : '';
		
		if($memberID && $txnPwd)
		{
			if(strlen($txnid) < 10)
			{
				// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - Txnid Length Error.]'.PHP_EOL;
			        $this->User->generateUpiCollectionLog($log_msg);
					$response = array(
						'status_code' => 401,
						'status_msg' => 'Transaction id should be min 10 digit.'
					);
			}
			else
			{
				$callStoreProc = "CALL qrCreateValidate(?, ?, ?, ?)";
	            $queryData = array('transactionID' => $txnid, 'accountID' => $domain_account_id, 'memberID' => $memberID, 'txnPwd'=>do_hash($txnPwd));
	            $procQuery = $this->db->query($callStoreProc, $queryData);
	            $procResponse = $procQuery->row_array();

	            //add this two line 
	            $procQuery->next_result(); 
	            $procQuery->free_result(); 
	            if(isset($procResponse['status']) && $procResponse['status'] == 200)
	            {
					// check txnid already registered or not
					if($procResponse['txnExits'])
					{
						$log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - Txnid Already Exits.]'.PHP_EOL;
				        $this->User->generateUpiCollectionLog($log_msg);
						$response = array(
							'status_code' => 401,
							'status_msg' => 'Transaction id already exits.'
						);
					}
					else
					{
						// check member id and password
						if($procResponse['isUserValid'])
						{
							// check member id and password
							$loggedAccountID = $procResponse['loggedAccountID'];
							$whitelist_ip = $procResponse['whiteListIP'];
							$whitelist_ip_list = explode(',',$procResponse['whiteListIP']);

							$user_ip_address = $this->User->get_user_ip();
							if($whitelist_ip != '*' && !in_array($user_ip_address, $whitelist_ip_list))
							{
								$log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - IP not whitelisted.]'.PHP_EOL;
						        $this->User->generateUpiCollectionLog($log_msg);
								$response = array(
									'status_code' => 401,
									'status_msg' => 'Sorry ! Your IP Address not whitelisted.'
								);
							}
							else
							{
								$responseData = $this->Wallet_model->upiGenerateDynamicQr($domain_account_id,$loggedAccountID,$amount,$txnid);

					            $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth SP Response - '.json_encode($responseData).'.]'.PHP_EOL;
								$this->User->generateUpiCollectionLog($log_msg);

					            if(isset($responseData['status']) && $responseData['status'] == 1)
					            {
					            	$qr_image = $responseData['qr_image'];
					            	$intent = $responseData['intent'];
					            	$txnid = $responseData['txnid'];
					            	$refId = $responseData['refId'];
					            	$response = array(
										'status_code' => 200,
										'status_msg' => lang('200_ERROR_MSG'),
										'status' => 'SUCCESS',
										'qr_image' => $qr_image,
										'intent' => $intent,
										'txnid' => $txnid,
										'refId' => $refId
									);
					            }
					            else
					            {
					            	$response = array(
										'status_code' => 400,
										'status_msg' => $responseData['message']
									);
					            }
					        }
						}
						else
						{
							// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - Account Not Valid.]'.PHP_EOL;
					        $this->User->generateUpiCollectionLog($log_msg);
							$response = array(
								'status_code' => 401,
								'status_msg' => lang('401_ERROR_MSG'),
							);
						}
					}
				}
				else
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - SP Error.]'.PHP_EOL;
			        $this->User->generateUpiCollectionLog($log_msg);
					$response = array(
						'status_code' => 401,
						'status_msg' => lang('401_ERROR_MSG'),
					);
				}
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateUpiCollectionLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateUpiCollectionLog($log_msg);
	    
		echo json_encode($response,JSON_NUMERIC_CHECK);
		
	}

    public function generateQrAuth()
	{
		$log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API Called.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
    	//get logged user info
        //$domain_account_id = $this->User->get_domain_account();
        $domain_account_id = 2;

        $get = $this->input->get();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API Get Data - '.json_encode($get).']'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
        
		$memberID = isset($get['memberid']) ? $get['memberid'] : '';
        $txnPwd = isset($get['txnpwd']) ? $get['txnpwd'] : '';
        $name = isset($get['name']) ? $get['name'] : '';
        $amount = isset($get['amount']) ? $get['amount'] : 0;
        $txnid = isset($get['txnid']) ? $get['txnid'] : '';
		
		if($memberID && $txnPwd)
		{
			if(strlen($txnid) < 10)
			{
				// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - Txnid Length Error.]'.PHP_EOL;
			        $this->User->generateUpiCollectionLog($log_msg);
					$response = array(
						'status_code' => 401,
						'status_msg' => 'Transaction id should be min 10 digit.'
					);
			}
			else
			{
				$callStoreProc = "CALL qrCreateValidate(?, ?, ?, ?)";
	            $queryData = array('transactionID' => $txnid, 'accountID' => $domain_account_id, 'memberID' => $memberID, 'txnPwd'=>do_hash($txnPwd));
	            
	            $procQuery = $this->db->query($callStoreProc, $queryData);
	            $procResponse = $procQuery->row_array();
	            //add this two line 
	            $procQuery->next_result(); 
	            $procQuery->free_result(); 
	            $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - Proc Response - '.json_encode($procResponse).']'.PHP_EOL;
				$this->User->generateUpiCollectionLog($log_msg);
	            if(isset($procResponse['status']) && $procResponse['status'] == 200)
	            {
					// check txnid already registered or not
					if($procResponse['txnExits'])
					{
						$log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - Txnid Already Exits.]'.PHP_EOL;
				        $this->User->generateUpiCollectionLog($log_msg);
						$response = array(
							'status_code' => 401,
							'status_msg' => 'Transaction id already exits.'
						);
					}
					else
					{
						// check member id and password
						if($procResponse['isUserValid'])
						{
							// check member id and password
							$loggedAccountID = $procResponse['loggedAccountID'];
							$memberMobile = $procResponse['memberMobile'];
							$whitelist_ip = $procResponse['whiteListIP'];
							$whitelist_ip_list = explode(',',$procResponse['whiteListIP']);

							$user_ip_address = $this->User->get_user_ip();
							$activeService = $this->User->account_active_service($loggedAccountID);
                            
							if(!in_array(5, $activeService)){
								// save system log
						        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Collection  Service Not Active for this member.]'.PHP_EOL;
						        $this->User->generateUpiCollectionLog($log_msg);
					        	$response = array(
									'status_code' => 400,
									'status_msg' => 'Sorry ! This service is not active in your account.',
								);
							}
							
							elseif($whitelist_ip != '*' && !in_array($user_ip_address, $whitelist_ip_list))
							{
								$log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - IP not whitelisted.]'.PHP_EOL;
						        $this->User->generateUpiCollectionLog($log_msg);
								$response = array(
									'status_code' => 401,
									'status_msg' => 'Sorry ! Your IP Address not whitelisted.'
								);
							}
							else
							{
								$active_api_id = $this->User->get_upi_payin_active_api($loggedAccountID)['active_api_id'];

								if($active_api_id == 1)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQr($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'M1UVHBZFEA7P');
								}
								elseif($active_api_id == 2)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrYesBank($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile);
								}
								elseif($active_api_id == 3)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrCosmosBank($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'STYLE-5205');
								}
								elseif($active_api_id == 4)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrCosmosBank($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'GELAX-0942');
								}
								elseif($active_api_id == 5)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrCosmosBank($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'JUMBA-1343');
								}
								elseif($active_api_id == 6)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQr($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'M22UABDBKA8PP');
								}
								elseif($active_api_id == 7)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrCosmosBank($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'BILWA-2757');
								}
								elseif($active_api_id == 8)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrCosmosBank($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'SHOWD-8258');
								}
								elseif($active_api_id == 9)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrCosmosBank($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'MSSA-3745');
								}
								elseif($active_api_id == 10)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrCosmosBank($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'SHRI-3208');
								}
								elseif($active_api_id == 11)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrCosmosBank($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,'VISHW-1750');
								}
								
								elseif($active_api_id == 12)
								{
									$responseData = $this->Wallet_model->upiGenerateDynamicQrCollectPay($domain_account_id,$loggedAccountID,$amount,$txnid,$memberID,$memberMobile,$name);
								}

					            $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth SP Response - '.json_encode($responseData).'.]'.PHP_EOL;
								$this->User->generateUpiCollectionLog($log_msg);

					            if(isset($responseData['status']) && $responseData['status'] == 1)
					            {
					            	$qr_image = $responseData['qr_image'];
					            	$intent = $responseData['intent'];
					            	$txnid = $responseData['txnid'];
					            	$refId = $responseData['refId'];
					            	$response = array(
										'status_code' => 200,
										'status_msg' => lang('200_ERROR_MSG'),
										'status' => 'SUCCESS',
										'qr_image' => $qr_image,
										'intent' => $intent,
										'txnid' => $txnid,
										'refId' => $refId
									);
					            }
					            else
					            {
					            	$response = array(
										'status_code' => 400,
										'status_msg' => $responseData['message']
									);
					            }
					        }
						}
						else
						{
							// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - Account Not Valid.]'.PHP_EOL;
					        $this->User->generateUpiCollectionLog($log_msg);
							$response = array(
								'status_code' => 401,
								'status_msg' => lang('401_ERROR_MSG'),
							);
						}
					}
				}
				else
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API - SP Error.]'.PHP_EOL;
			        $this->User->generateUpiCollectionLog($log_msg);
					$response = array(
						'status_code' => 401,
						'status_msg' => lang('401_ERROR_MSG'),
					);
				}
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateUpiCollectionLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Genetate QR Auth API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateUpiCollectionLog($log_msg);
	    
		echo json_encode($response,JSON_NUMERIC_CHECK);
		
	}

	public function checkUpiTxnAuth()
	{
		$log_msg = '['.date('d-m-Y H:i:s').' - Check UPI Txn Auth API Called.]'.PHP_EOL;
        $this->User->generateAPIUserLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();

        $accountData = $this->User->get_account_data($domain_account_id);
        
        $get = $this->input->get();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Check UPI Txn Auth API Get Data - '.json_encode($get).']'.PHP_EOL;
        $this->User->generateAPIUserLog($log_msg);
        
		$ref_id = isset($get['ref_id']) ? $get['ref_id'] : '';
        $bank_rrno = isset($get['bank_rrno']) ? $get['bank_rrno'] : '';
        
		if($ref_id && $bank_rrno)
		{
			// check member id and password
			$chk_user = $this->db->get_where('upi_hold_transaction',array('bank_rrno'=>$bank_rrno,'is_clear_txn'=>0))->num_rows();
			if($chk_user)
			{
				// check txn is from qr code api user
        		$chk_txn_qr_api_user = $this->db->get_where('upi_api_qr_activation',array('ref_id'=>$ref_id))->num_rows();
        		if($chk_txn_qr_api_user)
        		{
        			// check member id and password
					$bankData = $this->db->get_where('upi_hold_transaction',array('bank_rrno'=>$bank_rrno,'is_clear_txn'=>0))->row_array();
        			// get member id
		        	$get_member_data = $this->db->get_where('upi_api_qr_activation',array('ref_id'=>$ref_id))->row_array();
		        	$member_id = isset($get_member_data['user_id']) ? $get_member_data['user_id'] : 0 ;
		        	$account_id = isset($get_member_data['account_id']) ? $get_member_data['account_id'] : 0 ;

		        	$PayerAmount = isset($bankData['amount']) ? $bankData['amount'] : 0 ;
		        	$PayerVA = isset($bankData['vpa_id']) ? $bankData['vpa_id'] : '' ;
		        	$recordID = isset($bankData['id']) ? $bankData['id'] : 0 ;
		        	$PayerName = isset($bankData['payerName']) ? $bankData['payerName'] : '' ;
		        	$TxnInitDate = isset($bankData['TxnInitDate']) ? $bankData['TxnInitDate'] : '' ;
		        	$TxnCompletionDate = isset($bankData['TxnCompletionDate']) ? $bankData['TxnCompletionDate'] : '' ;
		        	
		        	// save transaction data
					$txnData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'type_id' => 2,
						'txnid' => $ref_id,
						'bank_rrno' => $bank_rrno,
						'amount' => $PayerAmount,
						'vpa_id' => $PayerVA,
						'description' => 'QR Scan #'.$bank_rrno.' Amount Received.',
						'status' => 2,
						'created' => date('Y-m-d H:i:s'),
						'created_by' => 1
					);
					$this->db->insert('upi_transaction',$txnData);
					$record_id = $this->db->insert_id();

					$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
			                    
		            $after_balance = $before_balance + $PayerAmount;  


		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $PayerAmount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'QR Scan #'.$bank_rrno.' Amount Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            // get account role id
					$get_role_id = $this->db->select('role_id,upi_call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
					$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
					$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Check UPI Txn Auth API - UPI Callback Member Role ID - '.$user_role_id.']'.PHP_EOL;
			        $this->User->generateAPIUserLog($log_msg);
					
					if($user_role_id == 6)
					{
						$user_call_back_url = isset($get_role_id['upi_call_back_url']) ? $get_role_id['upi_call_back_url'] : '' ;
						// save system log
			        	$log_msg = '['.date('d-m-Y H:i:s').' - Check UPI Txn Auth API - UPI Callback API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
			        	$this->User->generateAPIUserLog($log_msg);

		        		$api_post_data = array();
		        		$api_post_data['status'] = 'SUCCESS';
		        		$api_post_data['payerAmount'] = $PayerAmount;
		        		$api_post_data['payerName'] = $PayerName;
		        		$api_post_data['txnID'] = $ref_id;
		        		$api_post_data['BankRRN'] = $bank_rrno;
		        		$api_post_data['payerVA'] = $PayerVA;
		        		$api_post_data['TxnInitDate'] = $TxnInitDate;
		        		$api_post_data['TxnCompletionDate'] = $TxnCompletionDate;

		        		$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
						curl_setopt($ch, CURLOPT_TIMEOUT, 30);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));		
						$output = curl_exec($ch); 
						curl_close($ch);

						// save system log
			        	$log_msg = '['.date('d-m-Y H:i:s').' - Check UPI Txn Auth API - UPI Callback API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
			        	$this->User->generateAPIUserLog($log_msg);

					}

					$this->db->where('id',$recordID);
					$this->db->update('upi_hold_transaction',array('is_clear_txn'=>1,'updated'=>date('Y-m-d H:i:s')));

        			$response = array(
						'status_code' => 200,
						'status_msg' => 'Transaction Successfully Credited.'
					);
        		}
        		else
        		{
        			// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Check UPI Txn API - Ref No. Not Valid.]'.PHP_EOL;
			        $this->User->generateAPIUserLog($log_msg);
					$response = array(
						'status_code' => 400,
						'status_msg' => 'QR Reference not valid or not found in hold.',
					);
        		}
				
		 		
			}
			else
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Check UPI Txn API - Txn Not Valid.]'.PHP_EOL;
		        $this->User->generateAPIUserLog($log_msg);
				$response = array(
					'status_code' => 400,
					'status_msg' => 'Bank RRN not valid or not found in hold.',
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Check UPI Txn API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateAPIUserLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => 'Please provide Bank RRN and QR Refernece ID.',
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Check UPI Txn API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateAPIUserLog($log_msg);
	    
		echo json_encode($response,JSON_NUMERIC_CHECK);
		
	}
	
	
	public function fundRequestAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Fund Request Auth API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $post = $this->input->post();


        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Fund Request Auth API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $log_msg = '['.date('d-m-Y H:i:s').' - Fund Request Auth API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			
			  
			        
			        $this->load->library('form_validation');
			        
					$this->form_validation->set_rules('from_account', 'From Account', 'required|xss_clean');
					$this->form_validation->set_rules('to_account', 'To Account', 'required|xss_clean');
					$this->form_validation->set_rules('to_bank', 'To Bank', 'required|xss_clean');
			        $this->form_validation->set_rules('txn_id', 'Txn ID ', 'required|xss_clean|numeric');
			        $this->form_validation->set_rules('ref_no', 'Ref No', 'required|xss_clean');
			        //$this->form_validation->set_rules('utr_no', 'UTR No ', 'required|xss_clean');
			        $this->form_validation->set_rules('remark', 'Remark', 'required|xss_clean');
			        $this->form_validation->set_rules('amount', 'Amount', 'required|xss_clean|numeric');
			        $this->form_validation->set_rules('image_url', 'Image', 'required|xss_clean');
			        
			        if ($this->form_validation->run() == FALSE) {
			            
			           
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Fund Request Auth API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
							'status' => 'FAILED',
							'txnid' => $post['txn_id'],
							'opt_msg'=>'Post Parameters Not Valid .'
						
	    							
						);
			        }

			        else
			        {
			        	$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();

			        	$loggedAccountID = $getAccountData['id'];

			        	$from_account = $post['from_account'];
						$to_account = $post['to_account'];
						$to_bank = $post['to_bank'];
						$txn_id = $post['txn_id'];
						$ref_no =  $post['ref_no'];
						$utr_no =$post['utr_no'];
						$remark = $post['remark'];
						$amount = $post['amount'];
						$img_url = $post['image_url'];

			        	 // check txn id already extis or not
	            		    $chkTxnId = $this->db->get_where('api_member_fund_request',array('txn_id'=>$txn_id,'account_id'=>$domain_account_id))->num_rows();
	            		 $chkUtr = $this->db->get_where('api_member_fund_request',array('ref_no'=>$ref_no,'account_id'=>$domain_account_id))->num_rows();
	            		    
	            		    if($chkTxnId)
	            		    {
	            		        // save system log
	    		                $log_msg = '['.date('d-m-Y H:i:s').' - Fund Request API Member Duplicate Txnid Error]'.PHP_EOL;
	    		                $this->User->generateLog($log_msg);
	    		                
	    		                $response = array(
	    							'status_code' => 401,
	    							'status_msg' => 'Duplicate Txnid Found.',
	    							'status' => 'FAILED',
	        						'txnid' =>$txn_id,			
	    							'opt_msg'=>'Duplicate Txnid Found.'
	    
	    							
	    						);
	            		    }
	            		    elseif($chkUtr)
	            		    {
	            		        // save system log
	    		                $log_msg = '['.date('d-m-Y H:i:s').' - Fund Request API Member Duplicate Txnid Error]'.PHP_EOL;
	    		                $this->User->generateLog($log_msg);
	    		                
	    		                $response = array(
	    							'status_code' => 401,
	    							'status_msg' => 'Duplicate Ref  Found.',
	    							'status' => 'FAILED',
	        						'txnid' =>$txn_id,			
	    							'opt_msg'=>'Duplicate Ref Found.'
	    
	    							
	    						);
	            		    }

	            		    else

	            		    {

	            		    	

						 $data = array(
	    							'account_id' => $domain_account_id,
	    							'member_id' => $loggedAccountID,
	    							'from_account' =>$from_account,
	    							'to_account'	=>$to_account,
	    							'to_bank' 		=>$to_bank,
	    							'txn_id'		=>$txn_id,
	    							'ref_no'		=>$ref_no,
	    							'utr_no'		=>$utr_no,
	    							'remark'		=>$remark,
	    							'amount'		=>$amount,
	    							'status'		=>1,
	    							'image_url' =>$img_url,
	    							'created' => date('Y-m-d H:i:s')
	    						);
	    						$this->db->insert('tbl_api_member_fund_request',$data);

	    				$log_msg = '['.date('d-m-Y H:i:s').' - Fund Request Auth API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateLog($log_msg);
			        				$response = array(
	    											'status_code' => 200,
	    											'status_msg' => 'Fund Request Generated Successfully',
	    											'status' => 'SUCCESS',			
	    											'txnid' => $txn_id,
	    											'opt_msg'=>'Fund Request Generated Successfully.'
	    											
	    								);

	            		    }

			        	

			        }

		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Fund Request Auth API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Fund Request Auth API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }
    
    //payout check status api


    public function payoutCheckStatus()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payout Check Status API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payout Check Status API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payout Check Status API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Payout Check Status API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$before_wallet_balance = $getAccountData['wallet_balance'];
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Payout Check Status API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
		        $this->load->library('form_validation');
		        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
		        if ($this->form_validation->run() == FALSE) {
		        	// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Payout Check Status API Post Parameters Not Valid.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => lang('400_ERROR_MSG'),
					);
		        }
		        else
		        {
		        	$txnID = $post['txnID'];
		        	// check txn id valid or not
		        	$chk_txnid = $this->db->get_where('user_new_fund_transfer',array('account_id'=>$domain_account_id,'user_id'=>$loggedAccountID,'transaction_id'=>$txnID))->num_rows();
		        	if($chk_txnid)
		        	{
		        		// check txn id valid or not
		        		$txnData = $this->db->get_where('user_new_fund_transfer',array('account_id'=>$domain_account_id,'user_id'=>$loggedAccountID,'transaction_id'=>$txnID))->row_array();
		        		$message = '';
		        		if($txnData['status'] == 2)
		        		{
		        			$message = 'PENDING';
		        		}
		        		elseif($txnData['status'] == 3)
		        		{
		        			$message = 'SUCCESS';
		        		}
		        		elseif($txnData['status'] == 4)
		        		{
		        			$message = 'FAILED';
		        		}
				        $response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => $message,
								'txnid' => $txnID,
								'amount' => $txnData['transfer_amount'],
								'rrn' => $txnData['rrn'],
								'opt_msg' => 'Transaction Fetch Successfully.'
						); 
			    	}
			    	else
			    	{
			    		$response = array(
							'status_code' => 401,
							'status_msg' => lang('401_TXNID_ERROR'),
						);
			    	}
		    	}

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Payout check Status API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Payout check Status API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Payout check Status API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }
    
    
    public function pennyDrop()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
		    
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
		    
		    
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
			  
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				
				$whitelist_ip = $getAccountData['whitelist_ip'];
				$whitelist_ip_list = explode(',',$getAccountData['whitelist_ip']);

				$user_ip_address = $this->User->get_user_ip();
				if($whitelist_ip != '*' && !in_array($user_ip_address, $whitelist_ip_list))
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API - IP not whitelisted.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
					$response = array(
						'status_code' => 401,
						'status_msg' => 'Sorry ! Your IP Address not whitelisted.'
					);
				}
				else
				{
					$user_email = 	$getAccountData['email'];		
					$loggedAccountID = $getAccountData['id'];
					$api_member_code = $getAccountData['user_code'];
					$user_mobile = $getAccountData['mobile'];
					$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
					$min_wallet_balance = $getAccountData['min_wallet_balance'];
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
			        $this->load->library('form_validation');
			        
					$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
					$this->form_validation->set_rules('account_number', 'Account No.', 'required|xss_clean');
					$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
			        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
			        if ($this->form_validation->run() == FALSE) {
			            
			           
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
							'status' => 'FAILED',
							'transaction_id' => strval($post['txnID'])
						);
			        }
			        else
			        {
			           
			        	
						$account_holder_name = $post['account_holder_name'];
						$account_no = $post['account_number'];
						$ifsc = $post['ifsc'];
						$transaction_id =  $post['txnID']."";
						    
						
							// get account verify surcharge
							
		            		$surcharge_amount = $this->User->get_account_verify_surcharge($loggedAccountID);
		            		// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API Member Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
					        $this->User->generateLog($log_msg);

					        $final_amount = $surcharge_amount;
					        
					        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

					        $final_deduct_wallet_balance = $before_wallet_balance - $min_wallet_balance;  
		                    
		                    if($final_deduct_wallet_balance < $final_amount){
				                // save system log
				                $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API Member Insufficient Wallet Error]'.PHP_EOL;
				                $this->User->generateLog($log_msg);
				                
				                $message = 'Insufficient Wallet Balance';
				                if($min_wallet_balance)
				                {
				                	$message = 'Minimum wallet balance required';
				                }

				                $response = array(
									'status_code' => 401,
									'status_msg' => lang('401_WALLET_BALANCE_ERROR'),
									'status' => 'FAILED',
		    						'txnid' =>$transaction_id,
		    						'rrn' =>null,
									'orderID' =>null,
									'opt_msg'=>$message
								);
		            		}
		            		else
		            		{
		            		    // check txn id already extis or not
		            		    $chkTxnId = $this->db->get_where('bank_verification',array('txn_id'=>$transaction_id,'account_id'=>$domain_account_id))->num_rows();
		            		    if($chkTxnId)
		            		    {
		            		        // save system log
		    		                $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify  API Member Duplicate Txnid Error]'.PHP_EOL;
		    		                $this->User->generateLog($log_msg);
		    		                
		    		                $response = array(
		    							'status_code' => 401,
		    							'status_msg' => 'Duplicate Txnid Found.',
		    							'status' => 'FAILED',		        						
		        						'txnid' =>$transaction_id,
		    							'opt_msg'=>'Duplicate Txnid Found.'
		    
		    							
		    						);
		            		    }
		            		    else
		            		    {
		                			// get wallet balance
		    				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		    			            $after_wallet_balance = $before_wallet_balance - $final_amount;    
		    			            
		    			            $wallet_data = array(
		    	                        'account_id'          => $domain_account_id,
		    	                        'member_id'           => $loggedAccountID,    
		    	                        'before_balance'      => $before_wallet_balance,
		    	                        'amount'              => $final_amount,  
		    	                        'after_balance'       => $after_wallet_balance,      
		    	                        'status'              => 1,
		    	                        'type'                => 2, 
		    	                        'wallet_type'         => 1,      
		    	                        'created'             => date('Y-m-d H:i:s'),      
		    	                        'description'         => 'Account Verification #'.$transaction_id.' Amount Deducted.'
		    	                    );
		    
		    	                    $this->db->insert('member_wallet',$wallet_data);
		    							
		    						$receipt_id = rand(111111,999999);
		    	                    
		    	                    $data = array(
		    							'account_id' => $domain_account_id,
		    							'member_id' => $loggedAccountID,
		    						    'api_url' => BANK_VERIFICATION_URL,
		    						   'post_data' => json_encode($post),
					                   'txn_id' => $transaction_id,
					                   'before_balance' => $before_wallet_balance,
					                   'amount' => $verification_charge,
					                   'after_balance' =>$after_balance, 
					                   'status' => 'Success',
					                   'created' => date('Y-m-d H:i:s') 
		    						);
		    						$this->db->insert('bank_verification',$data);
		    						$txnRecordID = $this->db->insert_id();

		    						$api_url = OPEN_MONEY_ACCOUNT_VERIFY_URL;
		                			// save system log
		                            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout API URL - '.$api_url.']'.PHP_EOL;
		                            $this->User->generateLog($log_msg);
		    				
		                            
		                            $bank_verification_url = OPEN_MONEY_ACCOUNT_VERIFY_URL;
		                          		
		                             $request = array(
		    				           'force_penny_drop'=>false,
		    				           'force_penny_drop_amount'=>1,
			                       'bank_account_number' => $account_no,
			                       'bank_ifsc_code' =>$ifsc,
			                       'merchant_reference_id' =>$transaction_id
				            );


				            $header = array(
				                'Authorization: Bearer ak_live_LElPYoDkk9uCjMFy2B34oodOG5VJJlwjJTYR:sk_live_jUaNDUcCfb7xCq1nPTWyLlKKxM3ith3e5tGr',
				                'content-type: application/json'
				            );


		         
		            $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $bank_verification_url);

		            // Return Transfer
		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		            // SSL Verify Peer
		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		            // SSL Verify Host
		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		            // Timeout
		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		            // HTTP Version
		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		            // Request Method
		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		            // Request Body
		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

		            // Execute
		           $output = curl_exec($curl);

		            // Close
		            curl_close ($curl);

		            $responseData = json_decode($output,true);

		    				        // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Account Verify API Response - '.$output.']'.PHP_EOL;
		    				            $this->User->generateLog($log_msg);
		    
		    							// save api response
		    							$apiData = array(
		    								'account_id' => $domain_account_id,
		    								'user_id' => $loggedAccountID,
		    								'api_response' => $output,
		    								'api_url' => $api_url,
		    								'post_data'=>json_encode($request),
		    								'created' => date('Y-m-d H:i:s'),
		    								'created_by'=>$loggedAccountID
		    							);
		    							$this->db->insert('instantpay_api_response',$apiData);
		    
		    							
		    							if(isset($responseData) && $responseData['status'] == "success"){
		    								
		    								$api_msg = 'Account Verified Successfully.';
		    
		    					             $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API - Transaction Success.]'.PHP_EOL;
		    
		    					            $this->User->generateLog($log_msg);
		    			                    
		    
		    			                    // save system log
		    					            $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API -  Transaction Wallet Deducation Done.]'.PHP_EOL;
		    					            $this->User->generateLog($log_msg);
		    								
		    								$response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'SUCCESS',
		    											'account_number'=>$responseData['bank_account_number'],
		    											'txnid' => $transaction_id,
		    											'account_holder_name' =>$responseData['name_as_per_bank'],
		    											'opt_msg'=>$api_msg
		    								);
		    
		    								
		    							}
		    
		    							else
		    							{
		    								
		    								$api_msg = $responseData['status'];
		    
		    					            $log_msg = '['.date('d-m-Y H:i:s').' - Account Verify API -  Transaction Failed.]'.PHP_EOL;
		    
		    					            $this->User->generateLog($log_msg);
		    					            
		    					            
		    					            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
											$after_wallet_balance = $before_balance + $final_amount;    

								            $wallet_data = array(
								                'account_id'          => $domain_account_id,
								                'member_id'           => $loggedAccountID,    
								                'before_balance'      => $before_balance,
								                'amount'              => $final_amount,  
								                'after_balance'       => $after_wallet_balance,      
								                'status'              => 1,
								                'type'                => 1,   
								                'wallet_type'		  => 1,   
								                'created'             => date('Y-m-d H:i:s'),      
								                'description'         => 'Account Verification #'.$transaction_id.' Amount Refund.'
								            );

								            $this->db->insert('member_wallet',$wallet_data);
		    					            
		    					            
		    								
		    								$response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'FAILED',
		    											'account_number'=>$response_data[1],
		    											'txnid' => $transaction_id,
		    											'account_holder_name' =>$response_data['name'],
		    											'opt_msg'=>$api_msg
		    								);
		    
		    							}
		    
		    								
		            		    }
								
		            		}
		            	
		            	
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Accont Verify Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
				    'status_code' => 401,
					'status_msg' => 'Member ID And Password Not Valid.',
					'status' => 'FAILED'
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Accont Verify API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
				'status' =>'FAILED'
			
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Accont Verify API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,true);
		}

    }
    
     //add beneficiary


     public function addBeneficiary()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API Called.]'.PHP_EOL;
        #$this->User->generateLog($log_msg);
    	//get logged user info
        #$domain_account_id = $this->User->get_domain_account();
        $domain_account_id = 2;
        #$accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API Post Data - '.json_encode($post).']'.PHP_EOL;
        #$this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        #$this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
		    
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
		    
		    
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
			  
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API Account Verified Successfully.]'.PHP_EOL;
		        #$this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				
				$whitelist_ip = $getAccountData['whitelist_ip'];
				$whitelist_ip_list = explode(',',$getAccountData['whitelist_ip']);

				$user_ip_address = $this->User->get_user_ip();
				if($whitelist_ip != '*' && !in_array($user_ip_address, $whitelist_ip_list))
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API - IP not whitelisted.]'.PHP_EOL;
			        #$this->User->generateLog($log_msg);
					$response = array(
						'status_code' => 401,
						'status_msg' => 'Sorry ! Your IP Address not whitelisted.'
					);
				}
				else
				{
					$user_email = 	$getAccountData['email'];		
					$loggedAccountID = $getAccountData['id'];
					$api_member_code = $getAccountData['user_code'];
					$user_mobile = $getAccountData['mobile'];
					
			        $this->load->library('form_validation');
			        
					$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
					$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
					$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
					$this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
					$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|min_length[10]|max_length[10]');
			        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
			        if ($this->form_validation->run() == FALSE) {
			            
			           
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API Post Parameters Not Valid.]'.PHP_EOL;
				        #$this->User->generateLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
							'status' => 'FAILED',
							'transaction_id' => strval($post['txnID'])
						);
			        }
			        else
			        {
			           
			        	
						$account_holder_name = $post['account_holder_name'];
						$account_no = $post['account_no'];
						$ifsc = $post['ifsc'];
						$email = $post['email'];
						$mobile = $post['mobile'];
						$transaction_id =  $post['txnID']."";
					
		            		    // check txn id already extis or not
		            		    $chkTxnId = $this->db->get_where('open_money_payout_user_benificary',array('txn_id'=>$transaction_id,'account_id'=>$domain_account_id,'is_active'=>1))->num_rows();

		            		    $chkBeneficiary = $this->db->get_where('open_money_payout_user_benificary',array('account_no'=>$account_no,'account_id'=>$domain_account_id,'is_active'=>1))->num_rows();

		            		    if($chkTxnId)
		            		    {
		            		        // save system log
		    		                $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary  API Member Duplicate Txnid Error]'.PHP_EOL;
		    		                #$this->User->generateLog($log_msg);
		    		                
		    		                $response = array(
		    							'status_code' => 401,
		    							'status_msg' => 'Duplicate Txnid Found.',
		    							'status' => 'FAILED',		        						
		        						'txnid' =>$transaction_id,
		    							'opt_msg'=>'Duplicate Txnid Found.'
		    
		    							
		    						);
		            		    }
		            		    elseif($chkBeneficiary)
		            		    {
		            		        // save system log
		    		                $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary Duplicate Error]'.PHP_EOL;
		    		                #$this->User->generateLog($log_msg);

		    		                $chkBeneficiary = $this->db->get_where('open_money_payout_user_benificary',array('account_no'=>$account_no,'account_id'=>$domain_account_id,'is_active'=>1))->row_array();

		    		                $api_msg = 'Beneficiary Already Exits.';
		    		                
		    		                $response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'SUCCESS',
		    											'bene_id' =>$chkBeneficiary['ben_id'],
		    											'txnid' =>$transaction_id."",
		    											'account_holder_name' =>$chkBeneficiary['account_holder_name'],
		    											'account_number'=>$chkBeneficiary['account_no'],
		    											'ifsc' => $chkBeneficiary['ifsc'],
		    											'email' => $chkBeneficiary['email'],
		    											'mobile' => $chkBeneficiary['mobile'],
		    											'opt_msg'=>$api_msg
		    								);


		            		    }
		            		    else
		            		    {

		    						$api_url = OPEN_MONEY_CREATE_BENEFICIARY_URL;
		                			// save system log
		                            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Add Beneficiary API URL - '.$api_url.']'.PHP_EOL;
		                            #$this->User->generateLog($log_msg);
		    							
		    				       $request = array(
			                       'type' => 'account_number',
			                       'name_of_account_holder'    =>$account_holder_name,
			                       'email'  => $email,
			                       'phone' =>$mobile,
			                       'bank_account_number' => $account_no,
			                       'bank_ifsc_code' =>$ifsc,
				            );


				            $header = array(
				                'Authorization: Bearer ak_live_bq0SO69ZdaATI2dabwpeuF7GPfWw09XAIsOP:sk_live_L0TiS0BSbJVeMR6oiEYJ16zc49bfQErxuMai',
				                'content-type: application/json'
				            );
		            
		         
		            $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $api_url);

		            // Return Transfer
		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		            // SSL Verify Peer
		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		            // SSL Verify Host
		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		            // Timeout
		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		            // HTTP Version
		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		            // Request Method
		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		            // Request Body
		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

		            // Execute
		           $output = curl_exec($curl);

		            // Close
		            curl_close ($curl);

		            $responseData = json_decode($output,true);

		    
		    				        // save system log
		    			            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Add Beneficiary API Response - '.$output.']'.PHP_EOL;
		    				            #$this->User->generateLog($log_msg);
		    
		    							// save api response
		    							$apiData = array(
		    								'account_id' => $domain_account_id,
		    								'user_id' => $loggedAccountID,
		    								'api_response' => $output,
		    								'api_url' => $api_url,
		    								'post_data'=>json_encode($request),
		    								'created' => date('Y-m-d H:i:s'),
		    								'created_by'=>$loggedAccountID
		    							);
		    							$this->db->insert('open_money_api_response',$apiData);
		    
		    							
		    							if(isset($responseData) && $responseData['status'] == "active" )
		    							{

		    								$bene_data = array(
								        	 'account_id' => $domain_account_id,
								        	 'type' => 1,
								        	 'email' =>$responseData['email'],
								        	 'mobile' => $responseData['phone'],
								        	 'txn_id' =>$transaction_id,
								        	 'user_id' => $loggedAccountID,
								        	 'account_holder_name' => $responseData['name_of_account_holder'],
								        	 'account_no' => $responseData['bank_account_number'],
								        	 'ifsc' => $responseData['bank_ifsc_code'],
								        	 'ben_id' => $responseData['id'],	
								        	 'is_active' => 1,
								        	 'created' => date('Y-m-d H:i:s')
								        	);
								        	$this->db->insert('open_money_payout_user_benificary',$bene_data);

		    								
		    								$api_msg = 'Beneficiary Added Successfully';
		    
		    					             $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API - Transaction Success.]'.PHP_EOL;
		    
		    					            #$this->User->generateLog($log_msg);

		    								$response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'SUCCESS',
		    											'bene_id' =>$responseData['id'],
		    											'txnid' =>$transaction_id."",
		    											'account_holder_name' =>$responseData['name_of_account_holder'],
		    											'account_number'=>$responseData['bank_account_number'],
		    											'ifsc' => $responseData['bank_ifsc_code'],
		    											'email' => $responseData['email'],
		    											'mobile' => $responseData['phone'],
		    											'opt_msg'=>$api_msg
		    								);
		    
		    								
		    							}
		    								
		    						else
		    						{	
		    
		    							// save system log
		    				            $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary Api Error.]'.PHP_EOL;
		    				            #$this->User->generateLog($log_msg);
		    
		    				            $response = array(
		    							'status_code' => 401,
		    							'status_msg' => 'Error From Bank Side',
		    							'status' => 'FAILED',		        						
		        						'txnid' =>$transaction_id."",
		    							'opt_msg'=>'Error From Bank Side.'
		    							
		    						);
		    
		    						}
		    			            
		    						
		            		    }
								
		            		}
		            	
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary Account Not Valid.]'.PHP_EOL;
			    #$this->User->generateLog($log_msg);
				$response = array(
				    'status_code' => 401,
					'status_msg' => 'Member ID And Password Not Valid.',
					'status' => 'FAILED'
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API Header Data Not Found.]'.PHP_EOL;
		    #$this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
				'status' =>'FAILED'
			
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    #$this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,true);
		}

    }


    //beneficiary Api List


     public function beneficiaryList()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary List API Called.]'.PHP_EOL;
        #$this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = 2;
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary List API Post Data - '.json_encode($post).']'.PHP_EOL;
        #$this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary List API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        #$this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;
                                                        
        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
		    
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary List API Account Verified Successfully.]'.PHP_EOL;
		        #$this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				

		        $this->load->library('form_validation');
		        $this->form_validation->set_rules('transaction_id', 'Txn ID', 'required|min_length[10]');
		        if ($this->form_validation->run() == FALSE) {
		        	// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary List API Post Parameters Not Valid.]'.PHP_EOL;
			        #$this->User->generateLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => lang('400_ERROR_MSG'),
					);
		        }
		        else
		        {
		        	$txnID = $post['transaction_id'];

		        	// check txn id valid or not
		        	$chk_beneficiary = $this->db->get_where('open_money_payout_user_benificary',array('account_id'=>$domain_account_id,'user_id'=>$loggedAccountID,'is_active'=>1))->num_rows();
		        	if($chk_beneficiary)
		        	{
		        		// check txn id valid or not
		        		$benificaryList = $this->db->get_where('open_money_payout_user_benificary',array('account_id'=>$domain_account_id,'user_id'=>$loggedAccountID,'is_active'=>1))->result_array();
                        
		        		$bene_data = array();

		        		if($benificaryList)
				{
					foreach ($benificaryList as $key => $list) {
						
						$bene_data[$key]['bene_id'] = $list['ben_id'];
						$bene_data[$key]['account_holder_name'] = $list['account_holder_name'];
						$bene_data[$key]['account_no'] = $list['account_no'];
						$bene_data[$key]['ifsc'] = $list['ifsc'];
						$bene_data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
						
					}
				}
                    
				        $response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => 'Beneficiary Fetch Successfully',
								'txnid' => $txnID,
								'bene_list' =>$bene_data,
								'opt_msg' => 'Beneficiary Fetch Successfully.'
						); 
			    	}
			    	else
			    	{
			    		$response = array(
							'status_code' => 401,
							'status_msg' => 'Beneficiary Not found',
						);
			    	}
		    	}

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary List API Account Not Valid.]'.PHP_EOL;
			    #$this->User->generateLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
		    
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary List API Header Data Not Found.]'.PHP_EOL;
		    #$this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary List  API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    #$this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }
    
    public function openPayout()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Called.]'.PHP_EOL;
        #$this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = 2;
        
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Post Data - '.json_encode($post).']'.PHP_EOL;
        #$this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payout API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        #$this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
		    
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
		    
		    
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd),'is_active'=>1))->num_rows();
			if($chk_user)
			{
			  
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Account Verified Successfully.]'.PHP_EOL;
		        #$this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd),'is_active'=>1))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$whitelist_ip = $getAccountData['whitelist_ip'];
				$whitelist_ip_list = explode(',',$getAccountData['whitelist_ip']);

				$user_ip_address = $this->User->get_user_ip();
				
				$activeService = $this->db->get_where('account_user_services',array('account_id'=>$domain_account_id,'member_id'=>$loggedAccountID,'status'=>1,'service_id'=>30))->num_rows();
                            
							if(!$activeService){
								// save system log
						        $log_msg = '['.date('d-m-Y H:i:s').' - Open Money Payout Service Not Active for this member.]'.PHP_EOL;
						        #$this->User->generateLog($log_msg);
					        	$response = array(
									'status_code' => 401,
									'status_msg' => 'Sorry ! This service is not active in your account.',
								);
							}
							
        				elseif($whitelist_ip != '*' && !in_array($user_ip_address, $whitelist_ip_list))
        				{
        					$log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API - IP not whitelisted.]'.PHP_EOL;
        			        #$this->User->generateLog($log_msg);
        					$response = array(
        						'status_code' => 401,
        						'status_msg' => 'Sorry ! Your IP Address not whitelisted.'
        					);
        				}
				else
				{
				    
					$user_email = 	$getAccountData['email'];		
					$loggedAccountID = $getAccountData['id'];
					$api_member_code = $getAccountData['user_code'];
					$user_mobile = $getAccountData['mobile'];
					$min_wallet_balance = $getAccountData['min_wallet_balance'];
					$member_package_id = $getAccountData['package_id'];
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Member ID - '.$loggedAccountID.'.]'.PHP_EOL;
			        #$this->User->generateLog($log_msg);
			        $this->load->library('form_validation');
			        
					$this->form_validation->set_rules('bene_id', 'Beneficiary ID', 'required|xss_clean');
					$this->form_validation->set_rules('payment_mode', 'Payment Mode', 'required|xss_clean');					
			        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
			        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
			        if ($this->form_validation->run() == FALSE) {
			            
			           
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Post Parameters Not Valid.]'.PHP_EOL;
				        #$this->User->generateLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
							'status' => 'FAILED',
							'txn_amount' =>$post['amount'],
							'transaction_id' => strval($post['txnID'])
						);
			        }
			        else
			        {
			           
			        	
						$amount = $post['amount'];
						$transaction_id =  $post['txnID']."";
						$mode = $post['payment_mode'];
						$bene_id = $post['bene_id'];

						
						if($amount < 0)
						{
							// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Post Amount Not Valid.]'.PHP_EOL;
					        #$this->User->generateLog($log_msg);
				        	$response = array(
								'status_code' => 400,
								'status_msg' => 'Amount is not valid.',
								'status' => 'FAILED',
								'txn_amount' =>$post['amount'],
								'transaction_id' => strval($post['txnID'])
							);
						}
						else
						{
						    
							// get dmr surcharge
		            		$surcharge_amount = 0;
		            		$getSurcharge = $this->db->get_where('open_money_payout_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'start_range <='=>$amount,'end_range >='=>$amount,'com_type'=>strtoupper($mode)))->row_array();
		            		if($getSurcharge)
		                    {
		                        $is_flat = $getSurcharge['is_flat'];
		                        $surcharge = $getSurcharge['api_charge'];
		                        $surcharge_amount = round(($surcharge/100)*$amount,2);
                                if($is_flat)
                    			{
                    				$surcharge_amount = $surcharge;
                    			}
		                    }
		            		// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Member Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
					        #$this->User->generateLog($log_msg);

					        $final_amount = $amount + $surcharge_amount;
					        
					        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

					        $final_deduct_wallet_balance = $before_wallet_balance - $min_wallet_balance;  
		                    
		                    if($final_deduct_wallet_balance < $final_amount){
				                // save system log
				                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Member Insufficient Wallet Error]'.PHP_EOL;
				                #$this->User->generateLog($log_msg);
				                
				                $message = 'Insufficient Wallet Balance';
				                if($min_wallet_balance)
				                {
				                	$message = 'Minimum wallet balance required';
				                }

				                $response = array(
									'status_code' => 401,
									'status_msg' => lang('401_WALLET_BALANCE_ERROR'),
									'status' => 'FAILED',
		    						'txn_amount' =>$post['amount'],
		    						'txnid' =>$transaction_id,
		    						'rrn' =>null,
									'orderID' =>null,
									'opt_msg'=>$message
								);
		            		}
		            		else
		            		{
		            		    // check txn id already extis or not
		            		    $chkTxnId = $this->db->get_where('open_money_payout',array('transaction_id'=>$transaction_id,'account_id'=>$domain_account_id))->num_rows();
		            		    if($chkTxnId)
		            		    {
		            		        // save system log
		    		                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Member Duplicate Txnid Error]'.PHP_EOL;
		    		                #$this->User->generateLog($log_msg);
		    		                
		    		                $response = array(
		    							'status_code' => 401,
		    							'status_msg' => 'Duplicate Txnid Found.',
		    							'status' => 'FAILED',
		        						'txn_amount' =>$post['amount'],
		        						'txnid' =>$transaction_id,
		        						'rrn' =>null,
		    							'orderID' =>null,
		    							'opt_msg'=>'Duplicate Txnid Found.'
		    
		    							
		    						);
		            		    }
		            		    else
		            		    {
		                			// get wallet balance
		    				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		    			            $after_wallet_balance = $before_wallet_balance - $final_amount;    
		    			            
		    			            $wallet_data = array(
		    	                        'account_id'          => $domain_account_id,
		    	                        'member_id'           => $loggedAccountID,    
		    	                        'before_balance'      => $before_wallet_balance,
		    	                        'amount'              => $final_amount,  
		    	                        'after_balance'       => $after_wallet_balance,      
		    	                        'status'              => 1,
		    	                        'type'                => 2, 
		    	                        'wallet_type'         => 1,      
		    	                        'created'             => date('Y-m-d H:i:s'),      
		    	                        'description'         => 'Payout #'.$transaction_id.' Amount Deducted.'
		    	                    );
		    
		    	                    $this->db->insert('member_wallet',$wallet_data);
		    							
		    						$receipt_id = rand(111111,999999);
		    	                    
		    	                    $data = array(
		    							'account_id' => $domain_account_id,
		    							'user_id' => $loggedAccountID,
		    							'ben_id' =>$bene_id,
		    							'transfer_amount' => $amount,
		    							'transfer_charge_amount' => $surcharge_amount,
		    							'total_wallet_charge' => $final_amount,
		    							'after_wallet_balance' => $after_wallet_balance,
		    							'transaction_id' => $transaction_id,
		    							'encode_transaction_id' => do_hash($transaction_id),
		    							'status' => 2,
		    							'txnType'=>$mode,
		    							'created' => date('Y-m-d H:i:s')
		    						);
		    						$this->db->insert('open_money_payout',$data);
		    						$txnRecordID = $this->db->insert_id();

		    						$api_url = OPEN_MONEY_PAYOUT_URL;
		                			// save system log
		                            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Open Payout API URL - '.$api_url.']'.PHP_EOL;
		                            #$this->User->generateLog($log_msg);
		    				
		    
		    				        $request = array(
		    		                
		        		                	    'type'=> 'account_number',
											  'currency_code'=> 'inr',
											  'debit_account_id'=> 'va_FRn6pSZp3oZ8kZehNul1eEUWP',
											  'beneficiary_id'=> $bene_id,
											  'amount'=> floatval($amount),
											  'payment_mode' => $mode,
											  'merchant_reference_id'=> $transaction_id,
											  'payment_remark'=> 'payout'
		    		                );	
		    
		        		            $header = array(
		        		               'Authorization: Bearer ak_live_bq0SO69ZdaATI2dabwpeuF7GPfWw09XAIsOP:sk_live_L0TiS0BSbJVeMR6oiEYJ16zc49bfQErxuMai',
		        		                'content-type: application/json',
		        		                'accept: application/json'
		        		                
		        		            );
		    		            
		    		       
		        		            $curl = curl_init();
		        		            // URL
		        		            curl_setopt($curl, CURLOPT_URL, $api_url);
		        
		        		            // Return Transfer
		        		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		        
		        		            // SSL Verify Peer
		        		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		        
		        		            // SSL Verify Host
		        		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		        
		        		            // Timeout
		        		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		        
		        		            // HTTP Version
		        		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		        
		        		            // Request Method
		        		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		        
		        		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		        
		        		            // Request Body
		        		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
		        
		        		            // Execute
		        		           $output = curl_exec($curl);
		        	       	
		        	        		curl_close ($curl);
		        
		        	        		$responseData = json_decode($output,true);
		                       
		                    
		    				        // save system log
		    			            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Open Payout API Response - '.$output.']'.PHP_EOL;
		    				            #$this->User->generateLog($log_msg);
		    
		    							// save api response
		    							$apiData = array(
		    								'account_id' => $domain_account_id,
		    								'user_id' => $loggedAccountID,
		    								'api_response' => $output,
		    								'api_url' => $api_url,
		    								'post_data'=>json_encode($request),
		    								'created' => date('Y-m-d H:i:s'),
		    								'created_by'=>$loggedAccountID
		    							);
		    							$this->db->insert('open_money_api_response',$apiData);
		    
		    							if($output == 'error code: 1015'){
		    								
		    								$api_msg = 'Failed From Bank Side';
		    
		    					            $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
		    
		    					            #$this->User->generateLog($log_msg);
		    					            
		    					            $this->db->where('id',$txnRecordID);
		    					            $this->db->where('account_id',$domain_account_id);
		    					            $this->db->where('user_id',$loggedAccountID);
		    					            $this->db->update('open_money_payout',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

		    					            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
											$after_wallet_balance = $before_balance + $final_amount;    

								             $wallet_data = array(
								                 'account_id'          => $domain_account_id,
								                 'member_id'           => $loggedAccountID,    
								                 'before_balance'      => $before_balance,
								                 'amount'              => $final_amount,  
								                 'after_balance'       => $after_wallet_balance,      
								                 'status'              => 1,
								                 'type'                => 1,   
								                 'wallet_type'		  => 1,   
								                 'created'             => date('Y-m-d H:i:s'),      
								                 'description'         => 'Payout #'.$transaction_id.' Amount Refund.'
								             );

								             $this->db->insert('member_wallet',$wallet_data);
		    					            
		    					            
		    								
		    								$response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'FAILED',
		    											'txn_amount'=>$amount,
		    											'txnid' => $transaction_id,
		    											'rrn' =>null,
		    											'orderID' =>null,
		    											'opt_msg'=>$api_msg
		    								);
		    
		    							}
		    							
		    							elseif(isset($responseData['error'])){
		    								
		    								$api_msg = $responseData['error']['message'];
		    
		    					            $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
		    
		    					            #$this->User->generateLog($log_msg);
		    					            
		    					            $this->db->where('id',$txnRecordID);
		    					            $this->db->where('account_id',$domain_account_id);
		    					            $this->db->where('user_id',$loggedAccountID);
		    					            $this->db->update('open_money_payout',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

		    					            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
											$after_wallet_balance = $before_balance + $final_amount;    

								             $wallet_data = array(
								                 'account_id'          => $domain_account_id,
								                 'member_id'           => $loggedAccountID,    
								                 'before_balance'      => $before_balance,
								                 'amount'              => $final_amount,  
								                 'after_balance'       => $after_wallet_balance,      
								                 'status'              => 1,
								                 'type'                => 1,   
								                 'wallet_type'		  => 1,   
								                 'created'             => date('Y-m-d H:i:s'),      
								                 'description'         => 'Payout #'.$transaction_id.' Amount Refund.'
								             );

								             $this->db->insert('member_wallet',$wallet_data);
		    					            
		    					            
		    								
		    								$response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'FAILED',
		    											'txn_amount'=>$amount,
		    											'txnid' => $transaction_id,
		    											'rrn' =>null,
		    											'orderID' =>null,
		    											'opt_msg'=>$api_msg
		    								);
		    
		    							}
		    
		    						else
		    						{	
		    
		    							// save system log
		    				            $log_msg = '['.date('d-m-Y H:i:s').' - Payout API -  Transaction Pending From API Operator Side.]'.PHP_EOL;
		    				            #$this->User->generateLog($log_msg);
		    
		    
		    				            $response = array(
		    									    'status_code' => 200,
		    										'status_msg' => lang('200_ERROR_MSG'),
		    										'status' => 'PENDING',
		    										'txnid' => $transaction_id,
		    										'rrn' => null,
		    										'orderID' =>$responseData['orderid'],
		    										'opt_msg' => 'Your transaction is under processing, status will be updated soon.'
		    							);
		    
		    						}
		    			            
		    						
		            		    }
								
		            		}
		            	}
		            	
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Account Not Valid.]'.PHP_EOL;
			    #$this->User->generateLog($log_msg);
				$response = array(
				    'status_code' => 401,
					'status_msg' => 'Member ID And Password Not Valid.',
					'status' => 'FAILED'
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Header Data Not Found.]'.PHP_EOL;
		    #$this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
				'status' =>'FAILED'
			
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,true);
		}

    }
    
    
    
    
    //create vpa beneficiary

      public function addVpaBeneficiary()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
		    
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
		    
		    
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
			  
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				
				$whitelist_ip = $getAccountData['whitelist_ip'];
				$whitelist_ip_list = explode(',',$getAccountData['whitelist_ip']);

				$user_ip_address = $this->User->get_user_ip();
				if($whitelist_ip != '*' && !in_array($user_ip_address, $whitelist_ip_list))
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - Add Beneficiary API - IP not whitelisted.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
					$response = array(
						'status_code' => 401,
						'status_msg' => 'Sorry ! Your IP Address not whitelisted.'
					);
				}
				else
				{
					$user_email = 	$getAccountData['email'];		
					$loggedAccountID = $getAccountData['id'];
					$api_member_code = $getAccountData['user_code'];
					$user_mobile = $getAccountData['mobile'];
					$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
					$min_wallet_balance = $getAccountData['min_wallet_balance'];
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
			        $this->load->library('form_validation');
			        
					$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
					$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
					
					$this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
					$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|min_length[10]|max_length[10]');
			        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
			        if ($this->form_validation->run() == FALSE) {
			            
			           
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
							'status' => 'FAILED',
							'transaction_id' => strval($post['txnID'])
						);
			        }
			        else
			        {
			           
			        	
						$account_holder_name = $post['account_holder_name'];
						$account_no = $post['account_no'];
						$email = $post['email'];
						$mobile = $post['mobile'];
						$transaction_id =  $post['txnID']."";
					
		            		    // check txn id already extis or not
		            		    $chkTxnId = $this->db->get_where('open_money_payout_vpa_user_benificary',array('txn_id'=>$transaction_id,'account_id'=>$domain_account_id,'is_active'=>1))->num_rows();

		            		    $chkBeneficiary = $this->db->get_where('open_money_payout_vpa_user_benificary',array('account_no'=>$account_no,'account_id'=>$domain_account_id,'is_active'=>1))->num_rows();

		            		    if($chkTxnId)
		            		    {
		            		        // save system log
		    		                $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary  API Member Duplicate Txnid Error]'.PHP_EOL;
		    		                $this->User->generateLog($log_msg);
		    		                
		    		                $response = array(
		    							'status_code' => 401,
		    							'status_msg' => 'Duplicate Txnid Found.',
		    							'status' => 'FAILED',		        						
		        						'txnid' =>$transaction_id,
		    							'opt_msg'=>'Duplicate Txnid Found.'
		    
		    							
		    						);
		            		    }
		            		    elseif($chkBeneficiary)
		            		    {
		            		        // save system log
		    		                $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary Duplicate Error]'.PHP_EOL;
		    		                $this->User->generateLog($log_msg);
		    		                
		    		                $response = array(
		    							'status_code' => 401,
		    							'status_msg' => 'Beneficiary Already Exits.',
		    							'status' => 'FAILED',		        						
		        						'txnid' =>$transaction_id,
		    							'opt_msg'=>'Beneficiary Already Found.'
		    
		    							
		    						);
		            		    }
		            		    else
		            		    {

		    						$api_url = OPEN_MONEY_CREATE_BENEFICIARY_URL;
		                			// save system log
		                            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Add Vpa Beneficiary API URL - '.$api_url.']'.PHP_EOL;
		                            $this->User->generateLog($log_msg);
		    							
		    				       $request = array(
			                       'type' => 'vpa',
			                       'name_of_account_holder'    =>$account_holder_name,
			                       'email'  => $email,
			                       'phone' =>$mobile,
			                       'vpa' => $account_no,
				            );


				            $header = array(
				                'Authorization: Bearer ak_live_bq0SO69ZdaATI2dabwpeuF7GPfWw09XAIsOP:sk_live_L0TiS0BSbJVeMR6oiEYJ16zc49bfQErxuMai',
				                'content-type: application/json'
				            );
		            
		         
		            $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $api_url);

		            // Return Transfer
		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		            // SSL Verify Peer
		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		            // SSL Verify Host
		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		            // Timeout
		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		            // HTTP Version
		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		            // Request Method
		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		            // Request Body
		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

		            // Execute
		           $output = curl_exec($curl);

		            // Close
		            curl_close ($curl);

		            $responseData = json_decode($output,true);

		    
		    				        // save system log
		    			            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Add Vpa Beneficiary API Response - '.$output.']'.PHP_EOL;
		    				            $this->User->generateLog($log_msg);
		    
		    							// save api response
		    							$apiData = array(
		    								'account_id' => $domain_account_id,
		    								'user_id' => $loggedAccountID,
		    								'api_response' => $output,
		    								'api_url' => $api_url,
		    								'post_data'=>json_encode($request),
		    								'created' => date('Y-m-d H:i:s'),
		    								'created_by'=>$loggedAccountID
		    							);
		    							$this->db->insert('open_money_api_response',$apiData);
		    
		    							
		    							if(isset($responseData) && $responseData['status'] == "active" )
		    							{

		    								$bene_data = array(
								        	 'account_id' => $domain_account_id,
								        	 'type' => 2,
								        	 'email' =>$responseData['email'],
								        	 'mobile' => $responseData['phone'],
								        	 'txn_id' =>$transaction_id,
								        	 'user_id' => $loggedAccountID,
								        	 'account_holder_name' => $responseData['name_of_account_holder'],
								        	 'account_no' => $responseData['vpa'],
								        	 //'ifsc' => $responseData['bank_ifsc_code'],
								        	 'ben_id' => $responseData['id'],	
								        	 'is_active' => 1,
								        	 'created' => date('Y-m-d H:i:s')
								        	);
								        	$this->db->insert('open_money_payout_vpa_user_benificary',$bene_data);

		    								
		    								$api_msg = 'Beneficiary Added Successfully';
		    
		    					             $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary API - Transaction Success.]'.PHP_EOL;
		    
		    					            $this->User->generateLog($log_msg);

		    								$response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'SUCCESS',
		    											'bene_id' =>$responseData['id'],
		    											'txnid' =>$transaction_id,
		    											'account_holder_name' =>$responseData['name_of_account_holder'],
		    											'account_number'=>$responseData['bank_account_number'],
		    											'email' => $responseData['email'],
		    											'mobile' => $responseData['phone'],
		    											'opt_msg'=>$api_msg
		    								);
		    
		    								
		    							}
		    								
		    						else
		    						{	
		    
		    							// save system log
		    				            $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary Api Error.]'.PHP_EOL;
		    				            $this->User->generateLog($log_msg);
		    
		    				            $response = array(
		    							'status_code' => 401,
		    							'status_msg' => 'Error From Bank Side',
		    							'status' => 'FAILED',		        						
		        						'txnid' =>$transaction_id,
		    							'opt_msg'=>'Error From Bank Side.'
		    							
		    						);
		    
		    						}
		    			            
		    						
		            		    }
								
		            		}
		            	
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
				    'status_code' => 401,
					'status_msg' => 'Member ID And Password Not Valid.',
					'status' => 'FAILED'
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
				'status' =>'FAILED'
			
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Add Vpa Beneficiary API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,true);
		}

    }


    public function vpaBeneficiaryList()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - VPA Beneficiary List API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - VPA Beneficiary List API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - VPA Beneficiary List API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;
                                                        
        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
		    
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - VPA Beneficiary List API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				

		        $this->load->library('form_validation');
		        $this->form_validation->set_rules('transaction_id', 'Txn ID', 'required|min_length[10]');
		        if ($this->form_validation->run() == FALSE) {
		        	// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Beneficiary List API Post Parameters Not Valid.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => lang('400_ERROR_MSG'),
					);
		        }
		        else
		        {
		        	$txnID = $post['transaction_id'];

		        	// check txn id valid or not
		        	$chk_beneficiary = $this->db->get_where('open_money_payout_vpa_user_benificary',array('account_id'=>$domain_account_id,'user_id'=>$loggedAccountID,'is_active'=>1))->num_rows();
		        	if($chk_beneficiary)
		        	{
		        		// check txn id valid or not
		        		$benificaryList = $this->db->get_where('open_money_payout_vpa_user_benificary',array('account_id'=>$domain_account_id,'user_id'=>$loggedAccountID,'is_active'=>1))->result_array();
                        
		        		$bene_data = array();

		        		if($benificaryList)
				{
					foreach ($benificaryList as $key => $list) {
						
						$bene_data[$key]['bene_id'] = $list['ben_id'];
						$bene_data[$key]['account_holder_name'] = $list['account_holder_name'];
						$bene_data[$key]['account_no'] = $list['account_no'];						
						$bene_data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
						
					}
				}
                    
				        $response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => 'Beneficiary Fetch Successfully',
								'txnid' => $txnID,
								'bene_list' =>$bene_data,
								'opt_msg' => 'Beneficiary Fetch Successfully.'
						); 
			    	}
			    	else
			    	{
			    		$response = array(
							'status_code' => 401,
							'status_msg' => 'Beneficiary Not found',
						);
			    	}
		    	}

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - VPA Beneficiary List API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
		    
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - VPA Beneficiary List API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - VPA Beneficiary List  API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }



    public function openUpiPayout()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' -  Open Upi Payout API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($domain_account_id);
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Open Upi Payout API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Open Upi Payout API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
		    
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
		    
		    
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
			  
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Open Upi Payout API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				
				$whitelist_ip = $getAccountData['whitelist_ip'];
				$whitelist_ip_list = explode(',',$getAccountData['whitelist_ip']);

				$user_ip_address = $this->User->get_user_ip();
				if($whitelist_ip != '*' && !in_array($user_ip_address, $whitelist_ip_list))
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API - IP not whitelisted.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
					$response = array(
						'status_code' => 401,
						'status_msg' => 'Sorry ! Your IP Address not whitelisted.'
					);
				}
				else
				{
				    
					$user_email = 	$getAccountData['email'];		
					$loggedAccountID = $getAccountData['id'];
					$api_member_code = $getAccountData['user_code'];
					$user_mobile = $getAccountData['mobile'];
					$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
					$min_wallet_balance = $getAccountData['min_wallet_balance'];
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Open Upi Payout API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
			        $this->load->library('form_validation');
			        
					$this->form_validation->set_rules('bene_id', 'Beneficiary ID', 'required|xss_clean');			
			        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
			        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
			        if ($this->form_validation->run() == FALSE) {
			            
			           
			        	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Post Parameters Not Valid.]'.PHP_EOL;
				        $this->User->generateLog($log_msg);
			        	$response = array(
							'status_code' => 400,
							'status_msg' => lang('400_ERROR_MSG'),
							'status' => 'FAILED',
							'txn_amount' =>$post['amount'],
							'transaction_id' => strval($post['txnID'])
						);
			        }
			        else
			        {
			           
			        	
						$amount = $post['amount'];
						$transaction_id =  $post['txnID']."";
						$mode = 'UPI';
						$bene_id = $post['bene_id'];

						
						if($amount < 0)
						{
							// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Open UPI Payout API Post Amount Not Valid.]'.PHP_EOL;
					        $this->User->generateLog($log_msg);
				        	$response = array(
								'status_code' => 400,
								'status_msg' => 'Amount is not valid.',
								'status' => 'FAILED',
								'txn_amount' =>$post['amount'],
								'transaction_id' => strval($post['txnID'])
							);
						}
						else
						{
							// get dmr surcharge
		            		$surcharge_amount = $this->User->get_open_money_surcharge($amount,$loggedAccountID,$mode);
		            		// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Open UPI Payout API Member Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
					        $this->User->generateLog($log_msg);

					        $final_amount = $amount + $surcharge_amount;
					        
					        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

					        $final_deduct_wallet_balance = $before_wallet_balance - $min_wallet_balance;  
		                    
		                    if($final_deduct_wallet_balance < $final_amount){
				                // save system log
				                $log_msg = '['.date('d-m-Y H:i:s').' - Open UPI Payout API Member Insufficient Wallet Error]'.PHP_EOL;
				                $this->User->generateLog($log_msg);
				                
				                $message = 'Insufficient Wallet Balance';
				                if($min_wallet_balance)
				                {
				                	$message = 'Minimum wallet balance required';
				                }

				                $response = array(
									'status_code' => 401,
									'status_msg' => lang('401_WALLET_BALANCE_ERROR'),
									'status' => 'FAILED',
		    						'txn_amount' =>$post['amount'],
		    						'txnid' =>$transaction_id,
		    						'rrn' =>null,
									'orderID' =>null,
									'opt_msg'=>$message
								);
		            		}
		            		else
		            		{
		            		    // check txn id already extis or not
		            		    $chkTxnId = $this->db->get_where('open_money_payout',array('transaction_id'=>$transaction_id,'account_id'=>$domain_account_id))->num_rows();
		            		    if($chkTxnId)
		            		    {
		            		        // save system log
		    		                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout API Member Duplicate Txnid Error]'.PHP_EOL;
		    		                $this->User->generateLog($log_msg);
		    		                
		    		                $response = array(
		    							'status_code' => 401,
		    							'status_msg' => 'Duplicate Txnid Found.',
		    							'status' => 'FAILED',
		        						'txn_amount' =>$post['amount'],
		        						'txnid' =>$transaction_id,
		        						'rrn' =>null,
		    							'orderID' =>null,
		    							'opt_msg'=>'Duplicate Txnid Found.'
		    
		    							
		    						);
		            		    }
		            		    else
		            		    {
		                			// get wallet balance
		    				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		    			            $after_wallet_balance = $before_wallet_balance - $final_amount;    
		    			            
		    			            $wallet_data = array(
		    	                        'account_id'          => $domain_account_id,
		    	                        'member_id'           => $loggedAccountID,    
		    	                        'before_balance'      => $before_wallet_balance,
		    	                        'amount'              => $final_amount,  
		    	                        'after_balance'       => $after_wallet_balance,      
		    	                        'status'              => 1,
		    	                        'type'                => 2, 
		    	                        'wallet_type'         => 1,      
		    	                        'created'             => date('Y-m-d H:i:s'),      
		    	                        'description'         => 'Open UPI Payout #'.$transaction_id.' Amount Deducted.'
		    	                    );
		    
		    	                    $this->db->insert('member_wallet',$wallet_data);
		    							
		    						$receipt_id = rand(111111,999999);
		    	                    
		    	                    $data = array(
		    							'account_id' => $domain_account_id,
		    							'user_id' => $loggedAccountID,
		    							'ben_id' =>$bene_id,
		    							'transfer_amount' => $amount,
		    							'transfer_charge_amount' => $surcharge_amount,
		    							'total_wallet_charge' => $final_amount,
		    							'after_wallet_balance' => $after_wallet_balance,
		    							'transaction_id' => $transaction_id,
		    							'encode_transaction_id' => do_hash($transaction_id),
		    							'status' => 2,
		    							'txnType'=>$mode,
		    							'created' => date('Y-m-d H:i:s')
		    						);
		    						$this->db->insert('open_money_payout',$data);
		    						$txnRecordID = $this->db->insert_id();

		    						$api_url = OPEN_MONEY_PAYOUT_URL;
		                			// save system log
		                            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Open Payout API URL - '.$api_url.']'.PHP_EOL;
		                            $this->User->generateLog($log_msg);
		    				
		    
		    				        $request = array(
		    		                
		        		                	    'type'=> 'vpa',
											  'currency_code'=> 'inr',
											  'debit_account_id'=> 'va_vbx85JXfZF2eYwJqySLvppVVL',
											  'beneficiary_id'=> $bene_id,
											  'amount'=> floatval($amount),
											  'merchant_reference_id'=> $transaction_id,
											  'payment_remark'=> 'payout'
		    		                );	
		    
		        		            $header = array(
		        		               'Authorization: Bearer ak_live_bq0SO69ZdaATI2dabwpeuF7GPfWw09XAIsOP:sk_live_L0TiS0BSbJVeMR6oiEYJ16zc49bfQErxuMai',
		        		                'content-type: application/json',
		        		                'accept: application/json'
		        		                
		        		            );
		    		            
		    		       
		        		            $curl = curl_init();
		        		            // URL
		        		            curl_setopt($curl, CURLOPT_URL, $api_url);
		        
		        		            // Return Transfer
		        		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		        
		        		            // SSL Verify Peer
		        		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		        
		        		            // SSL Verify Host
		        		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		        
		        		            // Timeout
		        		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		        
		        		            // HTTP Version
		        		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		        
		        		            // Request Method
		        		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		        
		        		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		        
		        		            // Request Body
		        		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
		        
		        		            // Execute
		        		           $output = curl_exec($curl);
		        	       	
		        	        		curl_close ($curl);
		        
		        	        		$responseData = json_decode($output,true);
		                       
		                    
		    				        // save system log
		    			            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Open UPI Payout API Response - '.$output.']'.PHP_EOL;
		    				            $this->User->generateLog($log_msg);
		    
		    							// save api response
		    							$apiData = array(
		    								'account_id' => $domain_account_id,
		    								'user_id' => $loggedAccountID,
		    								'api_response' => $output,
		    								'api_url' => $api_url,
		    								'post_data'=>json_encode($request),
		    								'created' => date('Y-m-d H:i:s'),
		    								'created_by'=>$loggedAccountID
		    							);
		    							$this->db->insert('open_money_api_response',$apiData);
		    
		    							
		    							if(isset($responseData['error'])){
		    								
		    								$api_msg = 'Transaction Failed';
		    
		    					            $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
		    
		    					            $this->User->generateLog($log_msg);
		    					            
		    					            $this->db->where('id',$txnRecordID);
		    					            $this->db->where('account_id',$domain_account_id);
		    					            $this->db->where('user_id',$loggedAccountID);
		    					            $this->db->update('open_money_payout',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

		    					            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
											$after_wallet_balance = $before_balance + $final_amount;    

								             $wallet_data = array(
								                 'account_id'          => $domain_account_id,
								                 'member_id'           => $loggedAccountID,    
								                 'before_balance'      => $before_balance,
								                 'amount'              => $final_amount,  
								                 'after_balance'       => $after_wallet_balance,      
								                 'status'              => 1,
								                 'type'                => 1,   
								                 'wallet_type'		  => 1,   
								                 'created'             => date('Y-m-d H:i:s'),      
								                 'description'         => 'Open UPI Payout #'.$transaction_id.' Amount Refund.'
								             );

								             $this->db->insert('member_wallet',$wallet_data);
		    					            
		    					            
		    								
		    								$response = array(
		    											'status_code' => 200,
		    											'status_msg' => lang('200_ERROR_MSG'),
		    											'status' => 'FAILED',
		    											'txn_amount'=>$amount,
		    											'txnid' => $transaction_id,
		    											'rrn' =>null,
		    											'orderID' =>null,
		    											'opt_msg'=>$api_msg
		    								);
		    
		    							}
		    
		    						else
		    						{	
		    
		    							// save system log
		    				            $log_msg = '['.date('d-m-Y H:i:s').' - Payout API -  Transaction Pending From API Operator Side.]'.PHP_EOL;
		    				            $this->User->generateLog($log_msg);
		    
		    
		    				            $response = array(
		    									    'status_code' => 200,
		    										'status_msg' => lang('200_ERROR_MSG'),
		    										'status' => 'PENDING',
		    										'txnid' => $transaction_id,
		    										'rrn' => null,
		    										'orderID' =>$responseData['orderid'],
		    										'opt_msg' => 'Your transaction is under processing, status will be updated soon.'
		    							);
		    
		    						}
		    			            
		    						
		            		    }
								
		            		}
		            	}
		            	
			    	}
			    }

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Open UPI Payout API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
				    'status_code' => 401,
					'status_msg' => 'Member ID And Password Not Valid.',
					'status' => 'FAILED'
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Open UPI Payout API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
				'status' =>'FAILED'
			
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Open UPI Payout API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,true);
		}

    }
    
    
    
     public function openPayoutCheckStatus()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Open  Payout Check Status API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Open  Payout Check Status API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Check Status API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Payout Check Status API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$before_wallet_balance = $getAccountData['wallet_balance'];
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Check Status API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
		        $this->load->library('form_validation');
		        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
		        if ($this->form_validation->run() == FALSE) {
		        	// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Payout Check Status API Post Parameters Not Valid.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => lang('400_ERROR_MSG'),
					);
		        }
		        else
		        {
		        	$txnID = $post['txnID'];
		        	// check txn id valid or not
		        	$chk_txnid = $this->db->get_where('open_money_payout',array('account_id'=>$domain_account_id,'user_id'=>$loggedAccountID,'transaction_id'=>$txnID))->num_rows();
		        	if($chk_txnid)
		        	{
		        		// check txn id valid or not
		        		$txnData = $this->db->get_where('open_money_payout',array('account_id'=>$domain_account_id,'user_id'=>$loggedAccountID,'transaction_id'=>$txnID))->row_array();
		        		$message = '';
		        		if($txnData['status'] == 2)
		        		{
		        			$message = 'PENDING';
		        		}
		        		elseif($txnData['status'] == 3)
		        		{
		        			$message = 'SUCCESS';
		        		}
		        		elseif($txnData['status'] == 4)
		        		{
		        			$message = 'FAILED';
		        		}
				        $response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => $message,
								'txnid' => $txnID,
								'amount' => $txnData['transfer_amount'],
								'rrn' => $txnData['rrn'],
								'opt_msg' => 'Transaction Fetch Successfully.'
						); 
			    	}
			    	else
			    	{
			    		$response = array(
							'status_code' => 401,
							'status_msg' => lang('401_TXNID_ERROR'),
						);
			    	}
		    	}

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Open  Payout check Status API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout check Status API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout check Status API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }
    
    
    public function payinCheckStatus()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payin Check Status API Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payin Check Status API Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Payin Check Status API Header Data - '.json_encode($header_data).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $memberID = '';
        $txnPwd = '';
        $response_type = isset($post['response_type']) ? $post['response_type'] : 1;

        if($header_data && isset($header_data['Memberid']) && isset($header_data['Txnpwd']))
		{
			$memberID = $header_data['Memberid'];
        	$txnPwd = $header_data['Txnpwd'];
		}
		if($memberID && $txnPwd)
		{
			// check member id and password
			$chk_user = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->num_rows();
			if($chk_user)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Payin Check Status API Account Verified Successfully.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				// get logged account id
				// check member id and password
				$getAccountData = $this->db->get_where('users',array('account_id'=>$domain_account_id,'username'=>$memberID,'transaction_password'=>do_hash($txnPwd)))->row_array();
				$loggedAccountID = $getAccountData['id'];
				$before_wallet_balance = $getAccountData['wallet_balance'];
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Payin Check Status API Member ID - '.$loggedAccountID.' Wallet Blance - '.$before_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
		        $this->load->library('form_validation');
		        $this->form_validation->set_rules('txnID', 'Txn ID', 'required|min_length[10]');
		        if ($this->form_validation->run() == FALSE) {
		        	// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Payin Check Status API Post Parameters Not Valid.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
		        	$response = array(
						'status_code' => 400,
						'status_msg' => lang('400_ERROR_MSG'),
					);
		        }
		        else
		        {
		        	$txnID = $post['txnID'];
		        	// check txn id valid or not
		        	$chk_txnid = $this->db->get_where('upi_transaction',array('account_id'=>$domain_account_id,'member_id'=>$loggedAccountID,'txnid'=>$txnID))->num_rows();
		        	if($chk_txnid)
		        	{
		        		// check txn id valid or not
		        		$txnData = $this->db->get_where('upi_transaction',array('account_id'=>$domain_account_id,'member_id'=>$loggedAccountID,'txnid'=>$txnID))->row_array();
		        		$message = '';
		        		if($txnData['status'] == 1)
		        		{
		        			$message = 'PENDING';
		        		}
		        		elseif($txnData['status'] == 2)
		        		{
		        			$message = 'SUCCESS';
		        		}
		        		elseif($txnData['status'] == 3)
		        		{
		        			$message = 'FAILED';
		        		}
				        $response = array(
								'status_code' => 200,
								'status_msg' => lang('200_ERROR_MSG'),
								'status' => $message,
								'txnid' => $txnID,
								'amount' => $txnData['amount'],
								'rrn' => $txnData['bank_rrno'],
								'opt_msg' => 'Transaction Fetch Successfully.'
						); 
			    	}
			    	else
			    	{
			    		$response = array(
							'status_code' => 401,
							'status_msg' => lang('401_TXNID_ERROR'),
						);
			    	}
		    	}

			}
			else
			{
				// save system log
			    $log_msg = '['.date('d-m-Y H:i:s').' - Payin check Status API Account Not Valid.]'.PHP_EOL;
			    $this->User->generateLog($log_msg);
				$response = array(
					'status_code' => 401,
					'status_msg' => lang('401_ERROR_MSG'),
				);
			}
		}
		else
		{
			// save system log
		    $log_msg = '['.date('d-m-Y H:i:s').' - Payin check Status API Header Data Not Found.]'.PHP_EOL;
		    $this->User->generateLog($log_msg);
			$response = array(
				'status_code' => 400,
				'status_msg' => lang('400_ERROR_MSG'),
			);
		}
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - Payin check Status API Final Response - '.json_encode($response).'.]'.PHP_EOL;
	    $this->User->generateLog($log_msg);
	    if($response_type == 2)
	    {
			// This function prints xml document. 
			echo $this->arrayToXml($response); 
		}
		else
		{
			echo json_encode($response,JSON_NUMERIC_CHECK);
		}

    }



    
   
}
