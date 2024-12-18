<?php 
class Transfer extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
        $this->load->model('master/Wallet_model');		
        $this->lang->load('master/wallet', 'english');
        
    }

	public function index(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'transfer/list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$date = isset($filterData[1]) ? trim($filterData[1]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.is_payout_open = 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.is_payout_open = 0";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '".$keyword."%'";
				$sql.=" OR a.account_no LIKE '".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();


		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['memberID'];
				$nestedData[] = $list['account_holder_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['account_no'].'<br />'.$list['ifsc'];
				$nestedData[] = 'Tran. Amount - '.$list['transfer_amount'].'<br />Charge - '.$list['transfer_charge_amount'];
				if($list['txnType'] == 'RGS')
				{
					$nestedData[] = 'NEFT';
				}
				elseif($list['txnType'] == 'RTG')
				{
					$nestedData[] = 'RTGS';
				}
				elseif($list['txnType'] == 'IFS')
				{
					$nestedData[] = 'IMPS';
				}
				else{
					$nestedData[] = '';
				}
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = $list['rrn'];

				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4 || $list['status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					);

		echo json_encode($json_data);  // send data as json format
	}


	
	// add member
	public function moneyTransfer()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/transfer',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    
	public function beneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$benificaryList = $this->db->select('user_benificary.*,aeps_bank_list.bank_name')->join('aeps_bank_list','aeps_bank_list.id = user_benificary.bankID')->get_where('user_benificary',array('user_benificary.account_id'=>$account_id,'user_benificary.user_id'=>$loggedAccountID))->result_array();

		// get bank list
  		$bankList = $this->db->get('aeps_bank_list')->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/benificary',
            'benificaryList' => $benificaryList,
            'bankList' => $bankList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }



    public function benificaryAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bankID', 'Bank', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
            
        if ($this->form_validation->run() == FALSE) {
            
            $this->beneficiaryList();
        }
        else
        {   

        	$bene_data = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'account_holder_name' => $post['account_holder_name'],
        	 'bankID' => $post['bankID'],
        	 'account_no' => $post['account_number'],
        	 'ifsc' => $post['ifsc'],
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	
        	$this->db->insert('user_benificary',$bene_data);

        	$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }




    // add member
	public function fundTransfer($bene_id = 0,$sender_id = 0)
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$bene_id))->row_array();

		if($bene_id && !$chk_beneficiary){

			$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}

		$mobile = isset($loggedUser['mobile']) ? $loggedUser['mobile'] : '';

		$benList = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->result_array();		

 		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/fundTransfer',
            'mobile' => $mobile,
            'benList' => $benList,
            'bene_id' => $bene_id,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    // save member
	public function fundTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Api Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Api Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('bene_id', 'Beneficiary', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');
        $this->form_validation->set_rules('txnType', 'Transaction Type', 'required|xss_clean');
        
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->fundTransfer($post['bene_id']);
		}
		else
		{	
			if($post['amount'] > 25000){

				$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('MAX_AMOUNT_ERROR'));
			}	
			$chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['bene_id']))->row_array();

			if(!$chk_beneficiary){

				$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
			}	

			
			$memberID = $loggedUser['user_code'];
			$mobile = $loggedUser['mobile'];
			$account_holder_name = $chk_beneficiary['account_holder_name'];
			$account_no = $chk_beneficiary['account_no'];
			$ifsc = $chk_beneficiary['ifsc'];
			$bankID = $chk_beneficiary['bankID'];
			$amount = $post['amount'];
			$txnType = $post['txnType'];
			$transaction_id = time().rand(1111,9999);
			$receipt_id = rand(111111,999999);


			
			$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
            
            // save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Api R-Wallet Balance - '.$chk_wallet_balance['wallet_balance'].'.]'.PHP_EOL;
	        $this->User->generateLog($log_msg);

            // get dmr surcharge
            $surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$loggedAccountID);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API - Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            $final_deduct_wallet_balance = $amount + $surcharge_amount + $min_wallet_balance;  

            $final_amount = $amount + $surcharge_amount;
            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            if($before_balance < $final_deduct_wallet_balance){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
            } 

            
			$after_wallet_balance = $before_balance - $final_amount;    

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_balance,
                'amount'              => $final_amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'		  => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'Fund Transfer #'.$transaction_id.' Amount Deducted.'
            );

            $this->db->insert('member_wallet',$wallet_data);

            

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
            $this->User->generateLog($log_msg);

			$data = array(
				'account_id' => $account_id,
				'user_id' => $loggedAccountID,
				'to_ben_id' => $post['bene_id'],
				'transfer_amount' => $amount,
				'transfer_charge_amount' => $surcharge_amount,
				'total_wallet_charge' => $final_amount,
				'after_wallet_balance' => $after_wallet_balance,
				'txnType' => $txnType,
				'transaction_id' => $transaction_id,
				'encode_transaction_id' => do_hash($transaction_id),
				'status' => 2,
				'wallet_type' => 1,
				'invoice_no' => $receipt_id,
				'memberID' => $memberID,
				'mobile' => $mobile,
				'account_holder_name' => $account_holder_name,
				'account_no' => $account_no,
				'ifsc' => $ifsc,
				'created' => date('Y-m-d H:i:s')
			);
			$this->db->insert('user_money_transfer',$data); 

			$recordID = $this->db->insert_id();


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
	                       'transferMode' => $txnType,
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

	        /*$output = '{"Error":"True","Message":"Invalid member or password and invalid request ip address please whitelist your ip","Data":null}';*/

	        $responseData = json_decode($output,true);

	        // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout API Response - '.$output.']'.PHP_EOL;
            $this->User->generateLog($log_msg);

			// save api response
			$apiData = array(
				'account_id' => $account_id,
				'user_id' => $loggedAccountID,
				'api_response' => $output,
				'api_url' => $api_url,
				'post_data'=>json_encode($request),
				'created' => date('Y-m-d H:i:s'),
				'created_by'=>$loggedAccountID
			);
			$this->db->insert('instantpay_api_response',$apiData);

			// save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - DMT Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
            $this->User->generateLog($log_msg);   


			if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
			{
				
				$rrno = $responseData['data']['txnReferenceId'];
				$this->db->where('id',$recordID);
				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$loggedAccountID);				
				$this->db->update('user_money_transfer',array('rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));
				$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
			}

			elseif(isset($responseData['statuscode']) && ($responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR'))
			{
				$api_msg = $responseData['status'];
    
    			$log_msg = '['.date('d-m-Y H:i:s').' - Fund Transfer API - Payout Transaction Failed.]'.PHP_EOL;
    
    			$this->User->generateLog($log_msg);
    					            
    					$this->db->where('id',$recordID);
    					$this->db->where('account_id',$account_id);
    					$this->db->where('user_id',$loggedAccountID);
    					$this->db->update('user_money_transfer',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

    					            //refund amount to wallet

    					            // get wallet balance
            				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
            			            $after_wallet_balance = $before_wallet_balance + $final_amount;    
            			            
            			            $wallet_data = array(
            	                        'account_id'          => $account_id,
            	                        'member_id'           => $loggedAccountID,    
            	                        'before_balance'      => $before_wallet_balance,
            	                        'amount'              => $final_amount,  
            	                        'after_balance'       => $after_wallet_balance,      
            	                        'status'              => 1,
            	                        'type'                => 1, 
            	                        'wallet_type'         => 1,      
            	                        'created'             => date('Y-m-d H:i:s'),      
            	                        'description'         => 'Fund Transfer #'.$transaction_id.' Amount Refund Credited.'
            	                    );
            
            	                    $this->db->insert('member_wallet',$wallet_data);
            
            	                    

            	                    $this->Az->redirect('master/transfer/transfer', 'system_message_error',lang('MANUAL_TRANSFER_FAILED',$api_msg));

			}



			else
			{
				

				$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_SUCCESS')));
			}

			
		}
	
	}

	//payout beneficiary list 

	public function payoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$benificaryList = $this->db->select('payout_user_benificary.*,aeps_bank_list.bank_name')->join('aeps_bank_list','aeps_bank_list.id = payout_user_benificary.bankID')->get_where('payout_user_benificary',array('payout_user_benificary.account_id'=>$account_id,'payout_user_benificary.user_id'=>$loggedAccountID))->result_array();

		// get bank list
  		$bankList = $this->db->get('aeps_bank_list')->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'account_id'=>$account_id,
			'loggedAccountID'=>$loggedAccountID,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/payout-benificary',
            'benificaryList' => $benificaryList,
            'bankList' => $bankList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }


    //payout benificery auth

    public function payoutBenificaryAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bankID', 'Bank', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
	        
        if ($this->form_validation->run() == FALSE) {
            
            $this->payoutBeneficiaryList();
        }
        else
        {   

        	$bene_data = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'account_holder_name' => $post['account_holder_name'],
        	 'bankID' => $post['bankID'],
        	 'account_no' => $post['account_number'],
        	 'ifsc' => $post['ifsc'],
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	
        	$this->db->insert('payout_user_benificary',$bene_data);

        	$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }

    //payout transfer
     public function payoutFundTransfer($bene_id = 0)
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$chk_beneficiary = $this->db->get_where('payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$bene_id))->row_array();
		

		if(!$chk_beneficiary){

			$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}

		$mobile = isset($loggedUser['mobile']) ? $loggedUser['mobile'] : '';

		$account_holder_name = isset($chk_beneficiary['account_holder_name']) ? $chk_beneficiary['account_holder_name'] : '';

		$account_no = isset($chk_beneficiary['account_no']) ? $chk_beneficiary['account_no'] : '';

		$ifsc = isset($chk_beneficiary['ifsc']) ? $chk_beneficiary['ifsc'] : '';
 		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/transfer',
            'mobile' => $mobile,
            'account_holder_name' => $account_holder_name,
            'account_no' => $account_no,
            'ifsc' => $ifsc,
            'bene_id' => $bene_id,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    function maximumCheck($num)
    {
    	$this->load->library('form_validation');
        if ($num < 1)
        {
            $this->form_validation->set_message(
                            'maximumCheck',
                            'The %s field must be grater than 10'
                        );
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

   /* public function payoutTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('bene_id', 'Beneficiary', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');
        $this->form_validation->set_rules('txnType', 'Transaction Type', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->payoutFundTransfer($post['bene_id']);
		}
		else
		{	

			$chk_beneficiary = $this->db->get_where('payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['bene_id']))->row_array();

			if(!$chk_beneficiary){

				$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
			}	
			
			$memberID = $loggedUser['user_code'];
			$mobile = $loggedUser['mobile'];
			$account_holder_name = $chk_beneficiary['account_holder_name'];
			$account_no = $chk_beneficiary['account_no'];
			$ifsc = $chk_beneficiary['ifsc'];
			$bankID = $chk_beneficiary['bankID'];
			$amount = $post['amount'];
			$txnType = $post['txnType'];
			$transaction_id = time().rand(1111,9999);
			$receipt_id = rand(111111,999999);

			
			$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
            
            // save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api E-Wallet Balance - '.$chk_wallet_balance['wallet_balance'].'.]'.PHP_EOL;
	        $this->User->generateLog($log_msg);

            // get dmr surcharge
            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID,$txnType);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api - Payout Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            $final_deduct_wallet_balance = $amount + $surcharge_amount + $min_wallet_balance;

            $final_amount = $amount + $surcharge_amount;
            $before_balance = $chk_wallet_balance['wallet_balance'];

            if($chk_wallet_balance['wallet_balance'] < $final_deduct_wallet_balance){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
            }

            $after_wallet_balance = $before_balance - $final_amount;    

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_balance,
                'amount'              => $final_amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'		  => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'Fund Transfer #'.$transaction_id.' Amount Deducted.'
            );

            $this->db->insert('member_wallet',$wallet_data);

            $user_wallet = array(
                'wallet_balance'=>$after_wallet_balance,        
            );    
            $this->db->where('id',$loggedAccountID);
            $this->db->where('account_id',$account_id);
            $this->db->update('users',$user_wallet);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
            $this->User->generateLog($log_msg);

			$data = array(
				'account_id' => $account_id,
				'user_id' => $loggedAccountID,
				'transfer_amount' => $amount,
				'transfer_charge_amount' => $surcharge_amount,
				'total_wallet_charge' => $final_amount,
				'after_wallet_balance' => $after_wallet_balance,
				'txnType' => $txnType,
				'transaction_id' => $transaction_id,
				'encode_transaction_id' => do_hash($transaction_id),
				'status' => 2,
				'wallet_type' => 1,
				'invoice_no' => $receipt_id,
				'memberID' => $memberID,
				'mobile' => $mobile,
				'account_holder_name' => $account_holder_name,
				'account_no' => $account_no,
				'ifsc' => $ifsc,
				'created' => date('Y-m-d H:i:s')
			);
			$this->db->insert('user_fund_transfer',$data); 
			$recordID = $this->db->insert_id();

			$responseData = $this->Wallet_model->cibPayout($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType);

			// save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            

			
			if(isset($responseData['status']) && $responseData['status'] == 1)
			{
				$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
				
			}
			elseif(isset($responseData['status']) && $responseData['status'] == 2)
			{
				$requestID = $responseData['requestID'];
				$rrno = $responseData['rrno'];
				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$loggedAccountID);
				$this->db->where('transaction_id',$transaction_id);
				$this->db->update('user_fund_transfer',array('op_txn_id'=>$requestID,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));

				// save system log
	            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api - Distribute Commision/Surcharge Start]'.PHP_EOL;
	            $this->User->generateLog($log_msg);

				$this->User->distribute_payout_commision($recordID,$transaction_id,$amount,$loggedAccountID,$surcharge_amount,'MD',$loggedUser['user_code'],$txnType);

				// save system log
	            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api - Distribute Commision/Surcharge End]'.PHP_EOL;
	            $this->User->generateLog($log_msg);

				$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
			}
			elseif(isset($responseData['status']) && $responseData['status'] == 3)
			{
				$apiMsg = $responseData['msg'];

				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$loggedAccountID);
				$this->db->where('transaction_id',$transaction_id);
				$this->db->update('user_fund_transfer',array('status'=>4,'updated'=>date('Y-m-d H:i:s')));

				$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
				$before_balance = $chk_wallet_balance['wallet_balance'];
				$after_wallet_balance = $before_balance + $final_amount;    

	            $wallet_data = array(
	                'account_id'          => $account_id,
	                'member_id'           => $loggedAccountID,    
	                'before_balance'      => $before_balance,
	                'amount'              => $final_amount,  
	                'after_balance'       => $after_wallet_balance,      
	                'status'              => 1,
	                'type'                => 1,   
	                'wallet_type'		  => 1,   
	                'created'             => date('Y-m-d H:i:s'),      
	                'description'         => 'Fund Transfer #'.$transaction_id.' Amount Refund.'
	            );

	            $this->db->insert('member_wallet',$wallet_data);

	            $user_wallet = array(
	                'wallet_balance'=>$after_wallet_balance,        
	            );    
	            $this->db->where('id',$loggedAccountID);
	            $this->db->where('account_id',$account_id);
	            $this->db->update('users',$user_wallet);

	            // save system log
	            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout API - Member Wallet Refund - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
	            $this->User->generateLog($log_msg);
				
				$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_FAILED'),$apiMsg));
			}
			
		}
	
	}
*/


	 public function payoutTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->payoutFundTransfer($post['bene_id']);
		}
		else
		{	
			$chk_beneficiary = $this->db->get_where('payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['bene_id']))->row_array();

			if(!$chk_beneficiary){

				$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
			}	
			
			$memberID = $loggedUser['user_code'];
			$mobile = $post['mobile'];
			$account_holder_name = $chk_beneficiary['account_holder_name'];
			$account_no = $chk_beneficiary['account_no'];
			$ifsc = $chk_beneficiary['ifsc'];
			$amount = $post['amount'];
			$transaction_id = time().rand(1111,9999);
			$receipt_id = rand(111111,999999);

			
			$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Wallet Balance - '.$chk_wallet_balance['wallet_balance'].']'.PHP_EOL;
            $this->User->generateLog($log_msg);

            // get dmr surcharge
            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            $final_amount = $amount + $surcharge_amount;
            
            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            $final_deduct_wallet_balance = $min_wallet_balance + $final_amount;

            if($before_balance < $final_amount){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
            } 

            if($before_balance < $final_deduct_wallet_balance){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Minimum Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                
                $this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('MIN_WALLET_ERROR'));
                
            } 

            $after_wallet_balance = $before_balance - $final_amount;    

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_balance,
                'amount'              => $final_amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'		  => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'Fund Transfer #'.$transaction_id.' Amount Deducted.'
            );

            $this->db->insert('member_wallet',$wallet_data);
			
			$api_url = DMR_API_URL."customernumber=".$mobile."&Accountnumber=".$account_no."&CustomerName=".urlencode($account_holder_name)."&amount=".$amount."&ifsccode=".$ifsc."&usertx=".$transaction_id;
			// save system log
            /*$log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API URL - '.$api_url.']'.PHP_EOL;
            $this->User->generateLog($log_msg);*/
			$headers = [
	            'memberid: '.$accountData['dmt_username'],
	            'password: '.$accountData['dmt_password']
	        ];


			$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL,$api_url);
	        #curl_setopt($ch, CURLOPT_POST, 1);
	        #curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  //Post Fields
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        
	        $output = curl_exec ($ch);
	        
	        
	        curl_close ($ch);

	        /*$output = '{"Error":"True","Message":"Invalid member or password and invalid request ip address please whitelist your ip","Data":null}';*/

	        $responseData = json_decode($output,true);

	        // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API Response - '.$output.']'.PHP_EOL;
            $this->User->generateLog($log_msg);

			// save api response
			$apiData = array(
				'account_id' => $account_id,
				'user_id' => $loggedAccountID,
				'recharge_id' => $transaction_id,
				'api_response' => $output,
				'api_url' => $api_url,
				'status' => 1,
				'is_dmr' => 1,
				'created' => date('Y-m-d H:i:s')
			);
			$this->db->insert('api_response',$apiData);

			

            

			if(isset($responseData['Error']) && $responseData['Error'] == 'False')
			{
				if(isset($responseData['Data']['status']) && $responseData['Data']['status'] == 'FAILURE')
				{
					// save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Transaction Failed.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);
					$apiMsg = $responseData['Data']['statusMessage'];
					$data = array(
						'account_id' => $account_id,
						'user_id' => $loggedAccountID,
						'transfer_amount' => $amount,
						'transfer_charge_amount' => $surcharge_amount,
						'total_wallet_charge' => $final_amount,
						'after_wallet_balance' => $after_wallet_balance,
						'transaction_id' => $transaction_id,
						'encode_transaction_id' => do_hash($transaction_id),
						'api_response' => $output,
						'status' => 4,
						'invoice_no' => $receipt_id,
						'memberID' => $memberID,
						'mobile' => $mobile,
						'account_holder_name' => $account_holder_name,
						'account_no' => $account_no,
						'ifsc' => $ifsc,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_fund_transfer',$data);

					$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

					$after_wallet_balance = $before_balance + $final_amount;    

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $loggedAccountID,    
		                'before_balance'      => $before_balance,
		                'amount'              => $final_amount,  
		                'after_balance'       => $after_wallet_balance,      
		                'status'              => 1,
		                'type'                => 1,   
		                'wallet_type'		  => 1,   
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'Fund Transfer #'.$transaction_id.' Amount Refund.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);


					$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_FAILED'),$apiMsg));
				}
				elseif(isset($responseData['Data']['status']) && $responseData['Data']['status'] == 'SUCCESS')
				{
					// save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Transaction Success.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);
                    


                    // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Transaction Wallet Deducation Done.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);

		            $rrn = $responseData['Data']['statusMessage'];

					$data = array(
						'account_id' => $account_id,
						'user_id' => $loggedAccountID,
						'transfer_amount' => $amount,
						'transfer_charge_amount' => $surcharge_amount,
						'total_wallet_charge' => $final_amount,
						'after_wallet_balance' => $after_wallet_balance,
						'transaction_id' => $transaction_id,
						'encode_transaction_id' => do_hash($transaction_id),
						'api_response' => $output,
						'status' => 3,
						'rrn' => $rrn,
						'invoice_no' => $receipt_id,
						'memberID' => $memberID,
						'mobile' => $mobile,
						'account_holder_name' => $account_holder_name,
						'account_no' => $account_no,
						'ifsc' => $ifsc,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_fund_transfer',$data);
					

					$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
				}
				else
				{
					
                    // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Transaction Wallet Deducation Done.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);

					$data = array(
						'account_id' => $account_id,
						'user_id' => $loggedAccountID,
						'transfer_amount' => $amount,
						'transfer_charge_amount' => $surcharge_amount,
						'total_wallet_charge' => $final_amount,
						'after_wallet_balance' => $after_wallet_balance,
						'transaction_id' => $transaction_id,
						'encode_transaction_id' => do_hash($transaction_id),
						'api_response' => $output,
						'status' => 2,
						'invoice_no' => $receipt_id,
						'memberID' => $memberID,
						'mobile' => $mobile,
						'account_holder_name' => $account_holder_name,
						'account_no' => $account_no,
						'ifsc' => $ifsc,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_fund_transfer',$data);
					

					$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
				}
			}
			else
			{
				// save system log
	            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Transaction Failed From API Operator Side.]'.PHP_EOL;
	            $this->User->generateLog($log_msg);
				$this->Az->redirect('master/transfer/payoutBeneficiaryList', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_ERROR'),$responseData['Data']['statusMessage']));
			}
	        
			
			
		}
	
	}




	public function benificaryAccountList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$benificaryList = $this->db->order_by('created','desc')->get_where('payout_user_request',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
            'account_id'=>$account_id,
            'loggedAccountID'=>$loggedAccountID,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/benificaryAccountList',
            'benificaryList' => $benificaryList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    //change bank account request

    public function benificaryAccountAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bank_name', 'Bank Name', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
	        
        if ($this->form_validation->run() == FALSE) {
            
            $this->benificaryAccountList();
        }
        else
        {   

        	$bene_data = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'account_holder_name' => $post['account_holder_name'],
        	 'bank_name' => $post['bank_name'],
        	 'account_no' => $post['account_number'],
        	 'ifsc' => $post['ifsc'],
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	
        	$this->db->insert('payout_user_request',$bene_data);

        	$this->Az->redirect('master/transfer/benificaryAccountList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }

    // delete beneficiary

    public function deleteBeneficiary($id)
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        // check beneficiary valid or not
        $chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$id))->num_rows();

		if(!$chk_beneficiary){

			$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}	

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedUser['id']);
		$this->db->where('id',$id);
		$this->db->delete('user_benificary');
		
		$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('DELETE_SUCCESS'));
	}

	

    public function getBenData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
    	$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $chk_member = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$recordID))->num_rows();

		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			// get bank list
  			$bankList = $this->db->get('aeps_bank_list')->result_array();

 			$dmrData = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$recordID))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Account Holder Name*</label>';
	        $str.='<input type="text" autocomplete="off" name="account_holder_name" class="form-control" value="'.$dmrData['account_holder_name'].'">';
	        $str.='</div>';

	        $str.='<div class="form-group">';
	        $str.='<label><b>Bank*</b></label>';
	        $str.='<select class="form-control" name="bankID">';
	        $str.='<option value="">Select Bank</option>';
	        if($bankList){
	        	foreach($bankList as $list){
	        		if($list['id'] == $dmrData['bankID'])
	        		{
	        			$str.='<option value="'.$list['id'].'" selected="selected">'.$list['bank_name'].'</option>';
	        		}
	        		else
	        		{
	        			$str.='<option value="'.$list['id'].'">'.$list['bank_name'].'</option>';
	        		}
	        	}
	        }
	        $str.='</select>';
	        $str.='</div>';

	        $str.='<div class="form-group">';
	        $str.='<label>Account No.*</label>';
	        $str.='<input type="text" autocomplete="off" name="account_number" class="form-control" value="'.$dmrData['account_no'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>IFSC Code*</label>';
	        $str.='<input type="text" autocomplete="off" name="ifsc" class="form-control" value="'.$dmrData['ifsc'].'">';
	        $str.='</div>';
	        

	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}

	public function updateBenificaryAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bankID', 'Bank', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
            
        if ($this->form_validation->run() == FALSE) {
            
            $msg = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Please enter required fields.</div>';
    		$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',$msg);
        }
        else
        {   
        	// check beneficiary valid or not
	        $chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['recordID']))->num_rows();

			if(!$chk_beneficiary){

				$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
			}	
        	$bene_data = array(
        	 'account_holder_name' => $post['account_holder_name'],
        	 'bankID' => $post['bankID'],
        	 'account_no' => $post['account_number'],
        	 'ifsc' => $post['ifsc'],
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'updated' => date('Y-m-d H:i:s')
			);
        	
        	$this->db->where('id',$post['recordID']);
        	$this->db->where('account_id',$account_id);
        	$this->db->where('user_id',$loggedAccountID);
        	$this->db->update('user_benificary',$bene_data);

        	$this->Az->redirect('master/transfer/beneficiaryList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }






     //instantpay payout


    public function newPayoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$benificaryList = $this->db->select('instantpay_payout_user_benificary.*,aeps_bank_list.bank_name')->join('aeps_bank_list','aeps_bank_list.id = instantpay_payout_user_benificary.bankID')->get_where('instantpay_payout_user_benificary',array('instantpay_payout_user_benificary.account_id'=>$account_id,'instantpay_payout_user_benificary.user_id'=>$loggedAccountID))->result_array();

		// get bank list
  		$bankList = $this->db->get('aeps_bank_list')->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'account_id'=>$account_id,
			'loggedAccountID'=>$loggedAccountID,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/instantpay-payout-benificary',
            'benificaryList' => $benificaryList,
            'bankList' => $bankList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }


    //payout benificery auth

    public function newPayoutBenificaryAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bankID', 'Bank', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
	        
        if ($this->form_validation->run() == FALSE) {
            
            $this->newPayoutBeneficiaryList();
        }
        else
        {   

        	$bene_data = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'account_holder_name' => $post['account_holder_name'],
        	 'bankID' => $post['bankID'],
        	 'account_no' => $post['account_number'],
        	 'ifsc' => $post['ifsc'],
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	
        	$this->db->insert('instantpay_payout_user_benificary',$bene_data);

        	$this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }



     public function newPayoutFundTransfer($bene_id = 0)
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$chk_beneficiary = $this->db->get_where('instantpay_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$bene_id))->row_array();

		

		if(!$chk_beneficiary){

			$this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}

		$mobile = isset($loggedUser['mobile']) ? $loggedUser['mobile'] : '';

		$account_holder_name = isset($chk_beneficiary['account_holder_name']) ? $chk_beneficiary['account_holder_name'] : '';

		$account_no = isset($chk_beneficiary['account_no']) ? $chk_beneficiary['account_no'] : '';

		$ifsc = isset($chk_beneficiary['ifsc']) ? $chk_beneficiary['ifsc'] : '';
 		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/instantpay-payout',
            'mobile' => $mobile,
            'account_holder_name' => $account_holder_name,
            'account_no' => $account_no,
            'ifsc' => $ifsc,
            'bene_id' => $bene_id,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }



    public function newPayoutTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		
	
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Instantpay payout Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->newPayoutFundTransfer($post['bene_id']);
		}
		else
		{	
			$chk_beneficiary = $this->db->get_where('instantpay_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['bene_id']))->row_array();

			if(!$chk_beneficiary){

				$this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
			}	
			
			$memberID = $loggedUser['user_code'];
			$get_user_email = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
			$user_email = $get_user_email['email'];
			$mobile = $post['mobile'];
			$account_holder_name = $chk_beneficiary['account_holder_name'];
			$account_no = $chk_beneficiary['account_no'];
			$ifsc = $chk_beneficiary['ifsc'];
			$amount = $post['amount'];
			$mode = $post['mode'];
			$transaction_id = time().rand(1111,9999);
			$receipt_id = rand(111111,999999);

			
			$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
            // save system log

           

            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Wallet Balance - '.$chk_wallet_balance['wallet_balance'].']'.PHP_EOL;
            $this->User->generateLog($log_msg);

            // get dmr surcharge
            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
            // save system log
          
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            $final_amount = $amount + $surcharge_amount;
            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            $final_deduct_wallet_balance = $min_wallet_balance + $final_amount;

            if($before_balance < $final_amount){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
            } 

            if($before_balance < $final_deduct_wallet_balance){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Minimum Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                
                $this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('MIN_WALLET_ERROR'));
                
            } 
			
			
			$after_wallet_balance = $before_balance - $final_amount;

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_balance,
                'amount'              => $final_amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'		  => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'Payout Transfer #'.$transaction_id.' Amount Deducted.'
            );

            $this->db->insert('member_wallet',$wallet_data);

            
            //save fund transfer record
            			$data = array(
						'account_id' => $account_id,
						'user_id' => $loggedAccountID,
						'transfer_amount' => $amount,
						'transfer_charge_amount' => $surcharge_amount,
						'total_wallet_charge' => $final_amount,
						'after_wallet_balance' => $after_wallet_balance,
						'transaction_id' => $transaction_id,
						'encode_transaction_id' => do_hash($transaction_id),
						'status' => 2,
						'invoice_no'=>$receipt_id,
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

	        	   $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout API Response - '.$output.']'.PHP_EOL;
            	 $this->User->generateLog($log_msg);

			// save api response
			$apiData = array(
				'account_id' => $account_id,
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


				// save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout Transaction Success.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);
                    // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout Transaction Wallet Deducation Done.]'.PHP_EOL;
		            
		            $this->User->generateLog($log_msg);


		            	//update record
		            				$this->db->where('id',$txnRecordID);
    					            $this->db->where('account_id',$account_id);
    					            $this->db->where('user_id',$loggedAccountID);
    					            $this->db->update('user_new_fund_transfer',array('api_response'=>$output,'status'=>3,'rrn'=>$responseData['data']['txnReferenceId'],'updated'=>date('Y-m-d H:i:s')));


					$this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));

			}
			elseif(isset($responseData['statuscode']) && ($responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR'))

			{
									$api_msg = $responseData['status'];
    
    					            $log_msg = '['.date('d-m-Y H:i:s').' - Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
    
    					            $this->User->generateLog($log_msg);
    					            
    					            $this->db->where('id',$txnRecordID);
    					            $this->db->where('account_id',$account_id);
    					            $this->db->where('user_id',$loggedAccountID);
    					            $this->db->update('user_new_fund_transfer',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

    					            //refund amount to wallet

    					            // get wallet balance
            				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
            			            $after_wallet_balance = $before_wallet_balance + $final_amount;    
            			            
            			            $wallet_data = array(
            	                        'account_id'          => $account_id,
            	                        'member_id'           => $loggedAccountID,    
            	                        'before_balance'      => $before_wallet_balance,
            	                        'amount'              => $final_amount,  
            	                        'after_balance'       => $after_wallet_balance,      
            	                        'status'              => 1,
            	                        'type'                => 1, 
            	                        'wallet_type'         => 1,      
            	                        'created'             => date('Y-m-d H:i:s'),      
            	                        'description'         => 'Payout #'.$transaction_id.' Amount Refund Credited.'
            	                    );
            
            	                    $this->db->insert('member_wallet',$wallet_data);
            

            	                    $this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_FAILED',$api_msg));

			}
			
			else
			{	
				
					$this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));

			}
	        
		}
	
	}





	//report


	public function newPayoutReport(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'transfer/new-payout-transfer-list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getNewPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
	   	$toDate = '';
	   	$status = 0;
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.is_payout_open = 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.is_payout_open = 0";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '".$keyword."%'";
				$sql.=" OR a.account_no LIKE '".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            if($status)
            {
                $sql.=" AND status = '$status'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();


			$sql_summery = "SELECT SUM(COALESCE(CASE WHEN a.status = 3 THEN a.total_wallet_charge END,0)) totalSuccessAmount,count( case when a.status=3 then 1 else NULL end) totalSuccessRecord,SUM(COALESCE(CASE WHEN a.status = 4 THEN a.total_wallet_charge END,0)) totalFailedAmount,count( case when a.status=4 then 1 else NULL end) totalFailedRecord,SUM(COALESCE(CASE WHEN a.status = 2 THEN a.total_wallet_charge END,0)) totalPendingAmount,count( case when a.status=2 then 1 else NULL end) totalPendingRecord FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id WHERE a.account_id = '$account_id'  AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.is_payout_open = 0";

			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql_summery.=" OR b.name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.txnType LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.op_txn_id LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($status)
            {
                $sql_summery.=" AND status = '$status'";
            }

            
            $get_success_recharge = $this->db->query($sql_summery)->row_array();
			

			$successAmount = isset($get_success_recharge['totalSuccessAmount']) ? number_format($get_success_recharge['totalSuccessAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalSuccessRecord']) ? $get_success_recharge['totalSuccessRecord'] : 0;
	        $failedAmount = isset($get_success_recharge['totalFailedAmount']) ? number_format($get_success_recharge['totalFailedAmount'],2) : '0.00';
	        $failedRecord = isset($get_success_recharge['totalFailedRecord']) ? $get_success_recharge['totalFailedRecord'] : 0;
	        $pendingAmount = isset($get_success_recharge['totalPendingAmount']) ? number_format($get_success_recharge['totalPendingAmount'],2) : '0.00';
	        $pendingRecord = isset($get_success_recharge['totalPendingRecord']) ? $get_success_recharge['totalPendingRecord'] : 0;


		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['memberID'];
				$nestedData[] = $list['account_holder_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['account_no'].'<br />'.$list['ifsc'];
				$nestedData[] = 'Tran. Amount - '.$list['transfer_amount'].'<br />Charge - '.$list['transfer_charge_amount'];
				if($list['txnType'] == 'NEFT')
				{
					$nestedData[] = 'NEFT';
				}
				elseif($list['txnType'] == 'RTGS')
				{
					$nestedData[] = 'RTGS';
				}
				elseif($list['txnType'] == 'IMPS')
				{
					$nestedData[] = 'IMPS';
				}
				else{
					$nestedData[] = '';
				}
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = $list['rrn'];

				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4 || $list['status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,
					"pendingAmount"  => $pendingAmount,
					"pendingRecord"  => $pendingRecord,					
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,					
					);

		echo json_encode($json_data);  // send data as json format
	}



	//Account Request

	public function iciciBenificaryAccountList()
    {
    	$account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$benificaryList = $this->db->order_by('created','desc')->get_where('icici_payout_user_request',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->result_array();

		$bankList = $this->db->get('aeps_bank_list')->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
            'account_id'=>$account_id,
            'loggedAccountID'=>$loggedAccountID,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/iciciBenificaryAccountList',
            'benificaryList' => $benificaryList,
            'bankList'		=>$bankList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    //change bank account request

    public function iciciBenificaryAccountAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bank_id', 'Bank Name', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
	        
        if ($this->form_validation->run() == FALSE) {
            
            $this->iciciBenificaryAccountList();
        }
        else
        {   

        	$bene_data = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'account_holder_name' => $post['account_holder_name'],
        	 //'bank_name' => $post['bank_name'],
        	 'bank_id'   =>$post['bank_id'],
        	 'account_no' => $post['account_number'],
        	 'ifsc' => $post['ifsc'],
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	
        	$this->db->insert('icici_payout_user_request',$bene_data);

        	$this->Az->redirect('master/transfer/iciciBenificaryAccountList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }





     public function upiPayoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		
            
            $benificaryList = $this->db->get_where('instantpay_upi_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->result_array();
		// get bank list
  		$bankList = $this->db->get('aeps_bank_list')->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'account_id'=>$account_id,
			'loggedAccountID'=>$loggedAccountID,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/upi-payout-benificary',
            'benificaryList' => $benificaryList,
            'bankList' => $bankList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }


    //payout benificery auth

    public function upiPayoutBenificaryAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        //$this->form_validation->set_rules('bankID', 'Bank', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        //$this->form_validation->set_rules('ifsc', 'IFSC', 'required');
	        
        if ($this->form_validation->run() == FALSE) {
            
            $this->upiPayoutBeneficiaryList();
        }
        else
        {   

        	$bene_data = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'account_holder_name' => $post['account_holder_name'],
        	 //'bankID' => $post['bankID'],
        	 'account_no' => $post['account_number'],
        	 //'ifsc' => $post['ifsc'],
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	
        	$this->db->insert('instantpay_upi_payout_user_benificary',$bene_data);

        	$this->Az->redirect('master/transfer/upiPayoutBeneficiaryList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }



     public function upiPayoutFundTransfer($bene_id = 0)
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$chk_beneficiary = $this->db->get_where('instantpay_upi_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$bene_id))->row_array();

		

		if(!$chk_beneficiary){

			$this->Az->redirect('master/transfer/upiPayoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}

		$mobile = isset($loggedUser['mobile']) ? $loggedUser['mobile'] : '';

		$account_holder_name = isset($chk_beneficiary['account_holder_name']) ? $chk_beneficiary['account_holder_name'] : '';

		$account_no = isset($chk_beneficiary['account_no']) ? $chk_beneficiary['account_no'] : '';

		//$ifsc = isset($chk_beneficiary['ifsc']) ? $chk_beneficiary['ifsc'] : '';
 		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/upi-payout',
            'mobile' => $mobile,
            'account_holder_name' => $account_holder_name,
            'account_no' => $account_no,
            //'ifsc' => $ifsc,
            'bene_id' => $bene_id,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }



    public function upiPayoutTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		
	
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Instantpay payout Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->upiPayoutFundTransfer($post['bene_id']);
		}
		else
		{	
			$chk_beneficiary = $this->db->get_where('instantpay_upi_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['bene_id']))->row_array();

			if(!$chk_beneficiary){

				$this->Az->redirect('master/transfer/upiPayoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
			}	
			
			$memberID = $loggedUser['user_code'];
			$get_user_email = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
			$user_email = $get_user_email['email'];
			$mobile = $post['mobile'];
			$account_holder_name = $chk_beneficiary['account_holder_name'];
			$account_no = $chk_beneficiary['account_no'];
			//$ifsc = $chk_beneficiary['ifsc'];
			$amount = $post['amount'];
			$mode = 'UPI';
			$transaction_id = time().rand(1111,9999);
			$receipt_id = rand(111111,999999);

			
			$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
            // save system log

           

            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Wallet Balance - '.$chk_wallet_balance['wallet_balance'].']'.PHP_EOL;
            $this->User->generateLog($log_msg);

            // get dmr surcharge
            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
            // save system log
          
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            $final_amount = $amount + $surcharge_amount;
            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            $final_deduct_wallet_balance = $min_wallet_balance + $final_amount;

            if($before_balance < $final_amount){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('master/transfer/upiPayoutBeneficiaryList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
            } 

            if($before_balance < $final_deduct_wallet_balance){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Minimum Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                
                $this->Az->redirect('master/transfer/upiPayoutBeneficiaryList', 'system_message_error',lang('MIN_WALLET_ERROR'));
                
            } 
			

			$after_wallet_balance = $before_balance - $final_amount;

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_balance,
                'amount'              => $final_amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'		  => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'UPI Payout Transfer #'.$transaction_id.' Amount Deducted.'
            );

            $this->db->insert('member_wallet',$wallet_data);

            

            //save fund transfer record
            			$data = array(
						'account_id' => $account_id,
						'user_id' => $loggedAccountID,
						'transfer_amount' => $amount,
						'transfer_charge_amount' => $surcharge_amount,
						'total_wallet_charge' => $final_amount,
						'after_wallet_balance' => $after_wallet_balance,
						'transaction_id' => $transaction_id,
						'encode_transaction_id' => do_hash($transaction_id),
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
					$txnRecordID = $this->db->insert_id();


            //call api

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

	        /*$output = '{"Error":"True","Message":"Invalid member or password and invalid request ip address please whitelist your ip","Data":null}';*/

	        $responseData = json_decode($output,true);

	        // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout API Response - '.$output.']'.PHP_EOL;
            $this->User->generateLog($log_msg);

			// save api response
			$apiData = array(
				'account_id' => $account_id,
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


				// save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout Transaction Success.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);
                    // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout Transaction Wallet Deducation Done.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);

		             $this->db->where('id',$txnRecordID);
    				$this->db->where('account_id',$account_id);
    				$this->db->where('user_id',$loggedAccountID);
    				$this->db->update('user_new_fund_transfer',array('api_response'=>$output,'status'=>3,'rrn'=>$responseData['data']['txnReferenceId'],'updated'=>date('Y-m-d H:i:s')));

					$this->Az->redirect('master/transfer/upiPayoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));

			}

			elseif(isset($responseData['statuscode']) && ($responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR'))
			{
				$api_msg = $responseData['status'];
    
    					            $log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
    
    					            $this->User->generateLog($log_msg);
    					            
    					            $this->db->where('id',$txnRecordID);
    					            $this->db->where('account_id',$account_id);
    					            $this->db->where('user_id',$loggedAccountID);
    					            $this->db->update('user_new_fund_transfer',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

    					            //refund amount to wallet

    					            // get wallet balance
            				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
            			            $after_wallet_balance = $before_wallet_balance + $final_amount;    
            			            
            			            $wallet_data = array(
            	                        'account_id'          => $account_id,
            	                        'member_id'           => $loggedAccountID,    
            	                        'before_balance'      => $before_wallet_balance,
            	                        'amount'              => $final_amount,  
            	                        'after_balance'       => $after_wallet_balance,      
            	                        'status'              => 1,
            	                        'type'                => 1, 
            	                        'wallet_type'         => 1,      
            	                        'created'             => date('Y-m-d H:i:s'),      
            	                        'description'         => 'UPI Payout Transfer #'.$transaction_id.' Amount Refund Credited.'
            	                    );
            
            	                    $this->db->insert('member_wallet',$wallet_data);
            
            	                    $this->Az->redirect('retailer/transfer/upiPayoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_FAILED',$api_msg));

			}


			else
			{	

					$this->Az->redirect('master/transfer/upiPayoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));

				
			}
	        
			
			
		}
	
	}



	//report


	public function upiPayoutReport(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'transfer/upi-payout-transfer-list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getUpiPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$date = isset($filterData[1]) ? trim($filterData[1]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.txnType = 'UPI'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.txnType = 'UPI'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '".$keyword."%'";
				$sql.=" OR a.account_no LIKE '".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();


		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['memberID'];
				$nestedData[] = $list['account_holder_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['account_no'];
				$nestedData[] = 'Tran. Amount - '.$list['transfer_amount'].'<br />Charge - '.$list['transfer_charge_amount'];
				if($list['txnType'] == 'NEFT')
				{
					$nestedData[] = 'NEFT';
				}
				elseif($list['txnType'] == 'RTGS')
				{
					$nestedData[] = 'RTGS';
				}
				elseif($list['txnType'] == 'IMPS')
				{
					$nestedData[] = 'IMPS';
				}
				elseif($list['txnType'] == 'UPI')
				{
					$nestedData[] = 'UPI';
				}
				else{
					$nestedData[] = '';
				}
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = $list['rrn'];

				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4 || $list['status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					);

		echo json_encode($json_data);  // send data as json format
	}

public function openPayout(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'transfer/open-payout'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}



	public function upiOpenPayoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		
            
            $benificaryList = $this->db->get_where('instantpay_upi_open_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->result_array();
		// get bank list
  		$bankList = $this->db->get('aeps_bank_list')->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'account_id'=>$account_id,
			'loggedAccountID'=>$loggedAccountID,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/upi-open-payout-benificary',
            'benificaryList' => $benificaryList,
            'bankList' => $bankList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }


    //payout benificery auth

    public function upiOpenPayoutBenificaryAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
            
        if ($this->form_validation->run() == FALSE) {
            
            $this->upiPayoutBeneficiaryList();
        }
        else
        {   

        	$bene_data = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'account_holder_name' => $post['account_holder_name'],        	 
        	 'account_no' => $post['account_number'],        	 
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	
        	$this->db->insert('instantpay_upi_open_payout_user_benificary',$bene_data);

        	$this->Az->redirect('master/transfer/upiOpenPayoutBeneficiaryList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }



     public function upiOpenPayoutFundTransfer($bene_id = 0)
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$chk_beneficiary = $this->db->get_where('instantpay_upi_open_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$bene_id))->row_array();

		

		if(!$chk_beneficiary){

			$this->Az->redirect('master/transfer/upiOpenPayoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}

		$mobile = isset($loggedUser['mobile']) ? $loggedUser['mobile'] : '';

		$account_holder_name = isset($chk_beneficiary['account_holder_name']) ? $chk_beneficiary['account_holder_name'] : '';

		$account_no = isset($chk_beneficiary['account_no']) ? $chk_beneficiary['account_no'] : '';

		
 		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/upi-open-payout',
            'mobile' => $mobile,
            'account_holder_name' => $account_holder_name,
            'account_no' => $account_no,
            //'ifsc' => $ifsc,
            'bene_id' => $bene_id,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }



    public function upiOpenPayoutTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		
	
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Instantpay payout Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->upiOpenPayoutFundTransfer($post['bene_id']);
		}
		else
		{	
			$chk_beneficiary = $this->db->get_where('instantpay_upi_open_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['bene_id']))->row_array();

			if(!$chk_beneficiary){

				$this->Az->redirect('master/transfer/upiOpenPayoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
			}	
			
			$memberID = $loggedUser['user_code'];
			$get_user_email = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
			$user_email = $get_user_email['email'];
			$mobile = $post['mobile'];
			$account_holder_name = $chk_beneficiary['account_holder_name'];
			$account_no = $chk_beneficiary['account_no'];
			//$ifsc = $chk_beneficiary['ifsc'];
			$amount = $post['amount'];
			$mode = 'UPI';
			$transaction_id = time().rand(1111,9999);
			$receipt_id = rand(111111,999999);

			
			$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
            // save system log

           

            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Wallet Balance - '.$chk_wallet_balance['wallet_balance'].']'.PHP_EOL;
            $this->User->generateLog($log_msg);

            // get dmr surcharge
            $surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$loggedAccountID);
            // save system log
          
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            $final_amount = $amount + $surcharge_amount;
            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            $final_deduct_wallet_balance = $min_wallet_balance + $final_amount;

            if($before_balance < $final_amount){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('master/transfer/upiOpenPayoutBeneficiaryList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
            } 

            if($before_balance < $final_deduct_wallet_balance){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Minimum Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                
                $this->Az->redirect('master/transfer/upiOpenPayoutBeneficiaryList', 'system_message_error',lang('MIN_WALLET_ERROR'));
                
            } 
			
			

			$after_wallet_balance = $before_balance - $final_amount;

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_balance,
                'amount'              => $final_amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'		  => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'UPI Payout Transfer #'.$transaction_id.' Amount Deducted.'
            );

            $this->db->insert('member_wallet',$wallet_data);

            
            //SAVE RECORD

            $data = array(
						'account_id' => $account_id,
						'user_id' => $loggedAccountID,
						'transfer_amount' => $amount,
						'transfer_charge_amount' => $surcharge_amount,
						'total_wallet_charge' => $final_amount,
						'after_wallet_balance' => $after_wallet_balance,
						'transaction_id' => $transaction_id,
						'encode_transaction_id' => do_hash($transaction_id),
						'status' => 2,
						'invoice_no'=>$receipt_id,
						'memberID' => $memberID,
						'mobile' => $mobile,						
						'account_holder_name' => $account_holder_name,
						'account_no' => $account_no,						
						'txnType'=>$mode,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_money_transfer',$data);

					$recordID = $this->db->insert_id();



            //CALL API

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

	        /*$output = '{"Error":"True","Message":"Invalid member or password and invalid request ip address please whitelist your ip","Data":null}';*/

	        $responseData = json_decode($output,true);

	        // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout API Response - '.$output.']'.PHP_EOL;
            $this->User->generateLog($log_msg);

			// save api response
			$apiData = array(
				'account_id' => $account_id,
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


				// save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout Transaction Success.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);
                    // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Payout Transaction Wallet Deducation Done.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);

		             $rrno = $responseData['data']['txnReferenceId'];
					$this->db->where('id',$recordID);
					$this->db->where('account_id',$account_id);
					$this->db->where('user_id',$loggedAccountID);				
					$this->db->update('user_money_transfer',array('rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));
					

					$this->Az->redirect('master/transfer/upiOpenPayoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));

			}

			elseif(isset($responseData['statuscode']) && ($responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR'))
			{
				$api_msg = $responseData['status'];
    
    			$log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
    
    			$this->User->generateLog($log_msg);
    					            
    					$this->db->where('id',$recordID);
    					$this->db->where('account_id',$account_id);
    					$this->db->where('user_id',$loggedAccountID);
    					$this->db->update('user_money_transfer',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

    					            //refund amount to wallet

    					            // get wallet balance
            				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
            			            $after_wallet_balance = $before_wallet_balance + $final_amount;    
            			            
            			            $wallet_data = array(
            	                        'account_id'          => $account_id,
            	                        'member_id'           => $loggedAccountID,    
            	                        'before_balance'      => $before_wallet_balance,
            	                        'amount'              => $final_amount,  
            	                        'after_balance'       => $after_wallet_balance,      
            	                        'status'              => 1,
            	                        'type'                => 1, 
            	                        'wallet_type'         => 1,      
            	                        'created'             => date('Y-m-d H:i:s'),      
            	                        'description'         => 'Fund Transfer #'.$transaction_id.' Amount Refund Credited.'
            	                    );
            
            	                    $this->db->insert('member_wallet',$wallet_data);
            
            	                    $this->Az->redirect('master/transfer/upiOpenPayoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_FAILED',$api_msg));

			}
			

			
			

			else
			{	
					
					$this->Az->redirect('master/transfer/upiOpenPayoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));

			}
	        
			
			
		}
	
	}


	public function deleteBeneficiaryAccount($id)
	{	
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

		$chk_user = $this->db->get_where('instantpay_payout_user_benificary',array('account_id'=>$account_id,'id'=>$id))->num_rows();
        
      
        if($chk_user < 1)

        {
        	$this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('MEMBER_ERROR'));
        }
		
		$this->db->where('account_id',$account_id);
		$this->db->where('id',$id);
		$this->db->delete('instantpay_payout_user_benificary');
		
		$this->Az->redirect('master/transfer/newPayoutBeneficiaryList', 'system_message_error',lang('DELETE_SUCCESS'));
	}




	public function settlement(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'transfer/settlement'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}




	public function getBankBeneficiary()
	{
		//get logged user info
        $account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $benificaryList = $this->db->select('instantpay_payout_user_benificary.*,aeps_bank_list.bank_name')->join('aeps_bank_list','aeps_bank_list.id = instantpay_payout_user_benificary.bankID')->get_where('instantpay_payout_user_benificary',array('instantpay_payout_user_benificary.account_id'=>$account_id,'instantpay_payout_user_benificary.user_id'=>$loggedAccountID))->num_rows();
      
        	if($benificaryList)
        {

			// check recharge status
			$beneficiary_data = $this->db->select('instantpay_payout_user_benificary.*,aeps_bank_list.bank_name')->join('aeps_bank_list','aeps_bank_list.id = instantpay_payout_user_benificary.bankID')->get_where('instantpay_payout_user_benificary',array('instantpay_payout_user_benificary.account_id'=>$account_id,'instantpay_payout_user_benificary.user_id'=>$loggedAccountID))->result_array();
				
				$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Beneficiary Name</th>';
			$str.='<th>Account No.</th>';
			$str.='<th>Bank</th>';
			$str.='<th>IFSC</th>';
			$str.='<th>Added On	</th>';
			$str.='<th>Fund</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';

				if($beneficiary_data){
                $i=1;
                foreach($beneficiary_data as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['account_holder_name'].'</td>';
                	$str.='<td>'.$list['account_no'].'</td>';
                	$str.='<td>'.$list['bank_name'].'</td>';
                	$str.='<td>'.$list['ifsc'].'</td>';
                	$str.='<td>'.date('d-m-Y',strtotime($list['created'])).'</td>';
                	            
                	
                	$str.='<td><a title="Fund Transfer" class="btn btn-primary btn-sm" href="'.base_url().'master/transfer/newPayoutFundTransfer/'.$list['id'].'"> Transfer</a>&nbsp;<a title="Fund Transfer" class="btn btn-danger btn-sm" href="'.base_url().'master/transfer/deleteBeneficiaryAccount/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')" > Delete</a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }

            else
            {
            	$str.='<tr><td colspan="12" align="center">No Record Found.</td></tr>';
            }
			
			$response = array(
				'status'=>1,				
				'str' => $str
			);
		}
		else
		{
			$response = array(
				'status'=>0,				
				'str' => '<tr><td colspan="12" align="center">No Record Found.</td></tr>'
			);
		}
		echo json_encode($response);
	}


	public function getUpiBankBeneficiary()
	{
		//get logged user info
       	$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $benificaryList = $this->db->get_where('instantpay_upi_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->num_rows();
      
        	if($benificaryList)
        {

			// check recharge status
			$beneficiary_data = $this->db->get_where('instantpay_upi_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->result_array();
				
				$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Beneficiary Name</th>';
			$str.='<th>Account No.</th>';			
			$str.='<th>Added On	</th>';
			$str.='<th>Fund</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';

				if($beneficiary_data){
                $i=1;
                foreach($beneficiary_data as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['account_holder_name'].'</td>';
                	$str.='<td>'.$list['account_no'].'</td>';                	
                	$str.='<td>'.date('d-m-Y',strtotime($list['created'])).'</td>';
                	$str.='<td><a title="Fund Transfer" class="btn btn-primary btn-sm" href="'.base_url().'master/transfer/upiPayoutFundTransfer/'.$list['id'].'"> Transfer</a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }

            else
            {
            	$str.='<tr><td colspan="12" align="center">No Record Found.</td></tr>';
            }
			
			$response = array(
				'status'=>1,				
				'str' => $str
			);
		}
		else
		{
			$response = array(
				'status'=>0,				
				'str' => '<tr><td colspan="12" align="center">No Record Found.</td></tr>'
			);
		}
		echo json_encode($response);
	}



	public function addBankAccount(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		
		$bankList = $this->db->get('aeps_bank_list')->result_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'bankList'		=>$bankList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'transfer/add-bank-account'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function saveBenificaryBankAccount(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
       
        $this->load->library('form_validation');
       	if($post['type'] == 1)
       	{
       		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bankID', 'Bank', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
       	}
       	else
       	{
       		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');        
        	$this->form_validation->set_rules('account_number', 'Account Number', 'required');

       	}
        
	        
        if ($this->form_validation->run() == FALSE) {
            
            $this->addBankAccount();
        }
        else
        {   

        	if($post['type'] == 1)
        	{
        		$bene_data = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'account_holder_name' => $post['account_holder_name'],
        	 'bankID' => $post['bankID'],
        	 'account_no' => $post['account_number'],
        	 'ifsc' => $post['ifsc'],
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	$this->db->insert('instantpay_payout_user_benificary',$bene_data);

        	}
        	else
        	{
        		$bene_data = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'account_holder_name' => $post['account_holder_name'],        	 
        	 'account_no' => $post['account_number'],        	 
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	
        	$this->db->insert('instantpay_upi_payout_user_benificary',$bene_data);
        	}

        	$this->Az->redirect('master/transfer/addBankAccount', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }	


    

	
	
}