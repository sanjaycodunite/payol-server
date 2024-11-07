<?php 
class Transfer extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkRetailerPermission();
        $this->load->model('retailer/Wallet_model');
        $this->load->model('retailer/Complain_model');      
        $this->lang->load('retailer/recharge', 'english');		
        $this->lang->load('retailer/wallet', 'english');
        
    }

	public function index(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('retailer/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
        $this->parser->parse('retailer/layout/column-1' , $data);
    
	
	}


	public function getPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID')";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID')";
			
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
				$nestedData[] = $list['ifsc'];
				$nestedData[] = $list['transfer_amount'].' /-';
				$nestedData[] = $list['transfer_charge_amount'].' /-';
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = $list['rrn'];

				$nestedData[] = isset($list['description']) ? $list['description'] : 'Not Available';

				$nestedData[] = '<a href="'.base_url('retailer/report/transferInvoice/'.$list['id'].'').'" target="_blank">Invoice</a>';
				
				
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

				$nestedData[] = '<i class="fa fa-comments" onclick="showTransferComplainBox('.$list['id'].')" aria-hidden="true"></i>';
				
				
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
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('retailer/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// get bank list
  		$bankList = $this->db->get('aeps_bank_list')->result_array();	
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/transfer',
            'bankList' => $bankList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('retailer/layout/column-1', $data);
		
    }

    // save member
	public function transferAuth()
	{
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('retailer/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateCIBLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
		$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
		$this->form_validation->set_rules('confirm_account_no', 'Confirm Account No.', 'required|xss_clean|matches[account_no]');
		$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
		$this->form_validation->set_rules('bankID', 'Bank', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->moneyTransfer();
		}
		else
		{	
			
			$memberID = $loggedUser['user_code'];
			$mobile = trim($post['mobile']);
			$account_holder_name = trim($post['account_holder_name']);
			$account_no = trim($post['account_no']);
			$ifsc = trim($post['ifsc']);
			$bankID = $post['bankID'];
			$txnType = 'IFS';
			$amount = trim($post['amount']);
			$transaction_id = time().rand(1111,9999);
			$receipt_id = rand(111111,999999);

			$is_admin_surcharge = 0;
			// get dmr surcharge
            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID,$txnType);
            
			
        	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
            $final_amount = $amount + $surcharge_amount;
            $before_balance = $chk_wallet_balance['wallet_balance'];
			$final_deduct_wallet_balance = $final_amount;

			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Surcharge Amount - '.$surcharge_amount.' - Before Wallet Balance - '.$before_balance.' - Final Deduct Amount - '.$final_amount.']'.PHP_EOL;
	        $this->User->generateCIBLog($log_msg);

            if($chk_wallet_balance['wallet_balance'] < $final_amount){
                
                // save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Wallet Balance Error.]'.PHP_EOL;
		        $this->User->generateCIBLog($log_msg);

                $this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
            }

			$dmt_status = $this->db->get_where('otp_status',array('id'=>1,'account_id'=>$account_id))->row_array();
			
			if ($dmt_status['is_active'] == 1) {

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - OTP Send.]'.PHP_EOL;
		        $this->User->generateCIBLog($log_msg);

	         $otp_code = rand(1111,9999);
             $decode_otp_code = do_hash($otp_code);
                
                // save OTP Data
                $otpData = array(
                    'member_id' =>  $loggedUser['id'],
                    'account_id'=>$account_id,
                    'otp_code' => $otp_code,
                    'encrypt_otp_code' => $decode_otp_code,
                    //'otp_date' => date('Y-m-d'),
                    'json_post_data' => json_encode($post),
                    'status' => 0,
                    'created' => date('Y-m-d H:i:s')
                );

                $this->db->insert('users_otp',$otpData);

               // $userData = $this->db->get_where('users',array(''))
                $get_member_mobile = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id,))->row_array();

                $member_mobile = '91'.$get_member_mobile['mobile'];


               

                //send sms
                $sms = $otp_code.' is the One Time Password (OTP) for Fund Transfer of INR '.$transferAmount.' at Cranespay.';

                        
                	$api_url = 'http://bulksms.thedigitalindia.in/index.php/smsapi/httpapi/?uname=U1135&password=BK0oAT0i&sender=CRNPAY&receiver='.$member_mobile.'&route=TA&tempid=1207163057503337884&msgtype=1&sms='.urlencode($sms);

                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
                    $output = curl_exec($ch); 
                    curl_close($ch);

                   

                    $this->Az->redirect('retailer/transfer/transferOTP/'.$decode_otp_code, 'system_message_error',lang('TRANSFER_OTP_SUCCESSFULLY'));	

			}
			else
			{

				$after_wallet_balance = $before_balance - $final_amount;    

	            $data = array(
					'account_id' => $account_id,
					'user_id' => $loggedAccountID,
					'before_wallet_balance' => $before_balance,
					'transfer_amount' => $amount,
					'transfer_charge_amount' => $surcharge_amount,
					'total_wallet_charge' => $final_amount,
					'after_wallet_balance' => $after_wallet_balance,
					'transaction_id' => $transaction_id,
					'encode_transaction_id' => do_hash($transaction_id),
					'status' => 2,
					'wallet_type' => 1,
					'memberID' => $memberID,
					'mobile' => $mobile,
					'account_holder_name' => $account_holder_name,
					'account_no' => $account_no,
					'ifsc' => $ifsc,
					'txnType' => $txnType,
					'bankID' => $bankID,
					'created' => date('Y-m-d H:i:s')
				);
				$this->db->insert('user_fund_transfer',$data);
				$recharge_id = $this->db->insert_id();

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Record Saved Record ID - '.$recharge_id.'.]'.PHP_EOL;
		        $this->User->generateCIBLog($log_msg);

	            

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
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Wallet Deducted Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateCIBLog($log_msg);

				
				$responseData = $this->User->cibFundTransfer($loggedUser['user_code'],$account_no,$account_holder_name,$amount,$bankID,$ifsc,$txnType,$transaction_id,$loggedAccountID,'RT');

	            // save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - API Final Response - '.json_encode($responseData).'.]'.PHP_EOL;
		        $this->User->generateCIBLog($log_msg);
			
				if($responseData['status'])
				{
					if($responseData['status'] == 3)
					{
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Transaction Failed Status Updated.]'.PHP_EOL;
				        $this->User->generateCIBLog($log_msg);

						$apiMsg = $responseData['msg'];
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
			                'description'         => 'Fund Transfer #'.$transaction_id.' Amount Refunded.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			            $user_wallet = array(
			                'wallet_balance'=>$after_wallet_balance,        
			            );    
			            $this->db->where('id',$loggedAccountID);
			            $this->db->where('account_id',$account_id);
			            $this->db->update('users',$user_wallet);

			            // save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Wallet Refunded - Refund Amount - '.$final_amount.' - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
				        $this->User->generateCIBLog($log_msg);

			            $this->db->where('account_id',$account_id);
				        $this->db->where('user_id',$loggedAccountID);
				        $this->db->where('transaction_id',$transaction_id);
				        $this->db->update('user_fund_transfer',array('status'=>4,'updated'=>date('Y-m-d H:i:s')));

						$this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_FAILED'),$apiMsg));
					}
					elseif($responseData['status'] == 2)
					{
						
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Transaction SUCCESS Status Updated.]'.PHP_EOL;
				        $this->User->generateCIBLog($log_msg);
	                    
			            $this->db->where('account_id',$account_id);
				        $this->db->where('user_id',$loggedAccountID);
				        $this->db->where('transaction_id',$transaction_id);
				        $this->db->update('user_fund_transfer',array('status'=>3,'rrn' => $responseData['rrno'],'updated'=>date('Y-m-d H:i:s')));

						
						$this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));

					}
					else
					{
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Transaction Pending Status Updated.]'.PHP_EOL;
				        $this->User->generateCIBLog($log_msg);
						$this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',lang('MANUAL_TRANSFER_PENDING'));
					}
				}
				else
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Transaction Failed from Operator Side.]'.PHP_EOL;
			        $this->User->generateCIBLog($log_msg);

					$this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_ERROR'),$responseData['msg']));
				}

			}


			
	        
			
			
		}
	
	}



	//transfer OTP Process




	public function transferOTP($decode_otp_code = ''){
        //get logged user info
    	
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();
        // check OTP valid or not
        $chk_otp = $this->db->get_where('users_otp',array('member_id'=>$loggedAccountID,'account_id'=>$account_id,'encrypt_otp_code'=>$decode_otp_code,'status'=>0))->row_array();
        	
        if(!$chk_otp)
        {
            $this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',lang('DB_ERROR'));
        }
        
        // get user data
        $userData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();


        
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,           
            'account_id'=>$account_id,
            'userData' => $userData,           
            'decode_otp_code' => $decode_otp_code,
            'page_title' => lang('SITE_NAME'),
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'transfer/transfer-otp'
        );
        $this->parser->parse('retailer/layout/column-1', $data);
    }


    public function updateTransferOTPAuth() {
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->input->post();

        $decode_otp_code = $post['decode_otp_code'];

        $memberID = $loggedUser['user_code'];
        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('otp_code', 'OTP Code', 'required|xss_clean');        
        if ($this->form_validation->run() == FALSE) {
            
            $this->transferOTP($decode_otp_code);
            
        } 
        else {
            
            
            // check OTP valid or not

            $chk_otp = $this->db->get_where('users_otp',array('member_id'=>$loggedAccountID,'account_id'=>$account_id,'encrypt_otp_code'=>do_hash($post['otp_code']),'status'=>0))->row_array();

            
            if(!$chk_otp)
            {
                $this->Az->redirect('retailer/transfer/transferOTP/'.$decode_otp_code, 'system_message_error',lang('OTP_ERROR'));
            }

            
           

              $get_user_data =$this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();



               $this->db->where('otp_code',$chk_otp['otp_code']);
               $this->db->where('member_id',$loggedAccountID);
               $this->db->where('account_id',$account_id);
               $this->db->update('users_otp',array('status'=>1));


               $post_data = (array) json_decode($chk_otp['json_post_data']);
				

			$memberID = $loggedUser['user_code'];
			$mobile = trim($post_data['mobile']);
			$account_holder_name = trim($post_data['account_holder_name']);
			$account_no = trim($post_data['account_no']);
			$ifsc = trim($post_data['ifsc']);
			$bankID = $post_data['bankID'];
			$txnType = 'IFS';
			$amount = trim($post_data['amount']);
			$transaction_id = time().rand(1111,9999);
			$receipt_id = rand(111111,999999);

			$is_admin_surcharge = 0;
			// get dmr surcharge
            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID,$txnType);
            
			
        	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
            $final_amount = $amount + $surcharge_amount;
            $before_balance = $chk_wallet_balance['wallet_balance'];
			$final_deduct_wallet_balance = $final_amount;

			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Surcharge Amount - '.$surcharge_amount.' - Before Wallet Balance - '.$before_balance.' - Final Deduct Amount - '.$final_amount.']'.PHP_EOL;
	        $this->User->generateCIBLog($log_msg);

            if($chk_wallet_balance['wallet_balance'] < $final_amount){
                
                // save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Wallet Balance Error.]'.PHP_EOL;
		        $this->User->generateCIBLog($log_msg);

                $this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
            }

			
			$after_wallet_balance = $before_balance - $final_amount;    

	            $data = array(
					'account_id' => $account_id,
					'user_id' => $loggedAccountID,
					'before_wallet_balance' => $before_balance,
					'transfer_amount' => $amount,
					'transfer_charge_amount' => $surcharge_amount,
					'total_wallet_charge' => $final_amount,
					'after_wallet_balance' => $after_wallet_balance,
					'transaction_id' => $transaction_id,
					'encode_transaction_id' => do_hash($transaction_id),
					'status' => 2,
					'wallet_type' => 1,
					'memberID' => $memberID,
					'mobile' => $mobile,
					'account_holder_name' => $account_holder_name,
					'account_no' => $account_no,
					'ifsc' => $ifsc,
					'txnType' => $txnType,
					'bankID' => $bankID,
					'created' => date('Y-m-d H:i:s')
				);
				$this->db->insert('user_fund_transfer',$data);
				$recharge_id = $this->db->insert_id();

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Record Saved Record ID - '.$recharge_id.'.]'.PHP_EOL;
		        $this->User->generateCIBLog($log_msg);

	            

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
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Wallet Deducted Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateCIBLog($log_msg);

				
				$responseData = $this->User->cibFundTransfer($loggedUser['user_code'],$account_no,$account_holder_name,$amount,$bankID,$ifsc,$txnType,$transaction_id,$loggedAccountID,'RT');

	            // save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - API Final Response - '.json_encode($responseData).'.]'.PHP_EOL;
		        $this->User->generateCIBLog($log_msg);
			
				
				if($responseData['status'])
				{
					if($responseData['status'] == 3)
					{
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Transaction Failed Status Updated.]'.PHP_EOL;
				        $this->User->generateCIBLog($log_msg);

						$apiMsg = $responseData['msg'];
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
			                'description'         => 'Fund Transfer #'.$transaction_id.' Amount Refunded.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			            $user_wallet = array(
			                'wallet_balance'=>$after_wallet_balance,        
			            );    
			            $this->db->where('id',$loggedAccountID);
			            $this->db->where('account_id',$account_id);
			            $this->db->update('users',$user_wallet);

			            // save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Wallet Refunded - Refund Amount - '.$final_amount.' - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
				        $this->User->generateCIBLog($log_msg);

			            $this->db->where('account_id',$account_id);
				        $this->db->where('user_id',$loggedAccountID);
				        $this->db->where('transaction_id',$transaction_id);
				        $this->db->update('user_fund_transfer',array('status'=>4,'updated'=>date('Y-m-d H:i:s')));

						$this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_FAILED'),$apiMsg));
					}
					elseif($responseData['status'] == 2)
					{
						
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Transaction SUCCESS Status Updated.]'.PHP_EOL;
				        $this->User->generateCIBLog($log_msg);
	                    
			            $this->db->where('account_id',$account_id);
				        $this->db->where('user_id',$loggedAccountID);
				        $this->db->where('transaction_id',$transaction_id);
				        $this->db->update('user_fund_transfer',array('status'=>3,'rrn' => $responseData['rrno'],'updated'=>date('Y-m-d H:i:s')));

						
						$this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));

					}
					else
					{
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Transaction Pending Status Updated.]'.PHP_EOL;
				        $this->User->generateCIBLog($log_msg);
						$this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',lang('MANUAL_TRANSFER_PENDING'));
					}
				}
				else
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - Transaction Failed from Operator Side.]'.PHP_EOL;
			        $this->User->generateCIBLog($log_msg);

					$this->Az->redirect('retailer/transfer/moneyTransfer', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_ERROR'),$responseData['msg']));
				}
	        



            

             
        }
        
    }



    public function getTransferData($recharge_id = 0)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);

        $response = array();
        if(!$recharge_id || $recharge_id == '')
        {
            $response = array(
                'status' => 0,
                'msg' => 'Sorry ! Something went wrong.'
            );
        }
        else
        {
            // check recharge is valid or not
            $chk_recharge = $this->db->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id,'user_id'=>$loggedUser['id']))->num_rows();
            if(!$chk_recharge)
            {
                $response = array(
                    'status' => 0,
                    'msg' => 'Sorry ! Something went wrong.'
                );
            }
            else
            {
                $chk_recharge = $this->db->select('transaction_id,transfer_amount')->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id,'user_id'=>$loggedUser['id']))->row_array();

                
                $response = array(
                    'status' => 1,
                    'msg' => 'Success',
                    'txnid' => $chk_recharge['transaction_id'],
                    'amount' => $chk_recharge['transfer_amount'],
                );
                
            }
        }

        echo json_encode($response);
    }


    public function complainAuth()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        //check for foem validation
        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('recordID', 'Member Type', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Name', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            
            $this->Az->redirect('retailer/report/recharge', 'system_message_error',lang('FORM_ERROR'));
        }
        else
        {   
            $recharge_id = $post['recordID'];
            // check recharge is valid or not
            $chk_recharge = $this->db->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id,'user_id'=>$loggedUser['id']))->num_rows();
            if(!$chk_recharge)
            {

                $this->Az->redirect('retailer/transfer', 'system_message_error',lang('AUTHORIZE_ERROR'));  

            }

            $status = $this->Complain_model->saveTransferComplain($post);
            
            if($status == true)
            {
                $this->Az->redirect('retailer/transfer', 'system_message_error',lang('COMPLAIN_SAVED'));
            }
            else
            {
                $this->Az->redirect('retailer/transfer', 'system_message_error',lang('COMMON_ERROR'));
            }
            
        }
    
    }

    public function verifyAccountAuth($account_no = '', $ifsc = '')
    {
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
        $before_balance = $chk_wallet_balance['wallet_balance'];
        // get dmr surcharge
        $surcharge_amount = $this->User->get_account_verify_surcharge($loggedAccountID);
        if($before_balance < $surcharge_amount)
        {
        	$response = array(
        		'status' => 0,
        		'msg' => 'Sorry ! You have insufficient balance.'
        	);
        }
        else
        {
        	$txnid = time().rand(1111,9999);

        	$after_wallet_balance = $before_balance - $surcharge_amount;

        	$recordData = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'account_no'      	  => $account_no,
                'ifsc_code'           => $ifsc,  
                'txnid'       		  => $txnid,      
                'before_balance'      => $before_balance,
                'surcharge'           => $surcharge_amount,   
                'after_balance' 	  => $after_wallet_balance,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by' => $loggedAccountID
            );

            $this->db->insert('account_verify_history',$recordData);
            $recordID = $this->db->insert_id();
        	
        	$responseData = $this->User->compositeAccountVerify($txnid,$account_no,$ifsc,$loggedAccountID);

            if($responseData['status'] == 1)
            {
            	if($surcharge_amount)
	        	{
		        	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
		        	$before_balance = $chk_wallet_balance['wallet_balance'];
		        	
		        	$after_wallet_balance = $before_balance - $surcharge_amount;
		        	$wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $loggedAccountID,    
		                'before_balance'      => $before_balance,
		                'amount'              => $surcharge_amount,  
		                'after_balance'       => $after_wallet_balance,      
		                'status'              => 1,
		                'type'                => 2,   
		                'wallet_type'		  => 1,   
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'Account #'.$account_no.' Txn #'.$txnid.' Verify Amount Deducted.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            $user_wallet = array(
		                'wallet_balance'=>$after_wallet_balance,        
		            );    
		            $this->db->where('id',$loggedAccountID);
		            $this->db->where('account_id',$account_id);
		            $this->db->update('users',$user_wallet);
		        }

		        $this->db->where('id',$recordID);
		        $this->db->update('account_verify_history',array('bank_rrn'=>$responseData['BankRRN'],'verified_name'=>$responseData['BeneName'],'status'=>1));

            	$response = array(
	        		'status' => 1,
	        		'msg' => $responseData['msg'],
	        		'BeneName' => $responseData['BeneName']
	        	);
            }
            else
            {
            	$this->db->where('id',$recordID);
		        $this->db->update('account_verify_history',array('status'=>2));
            	$response = array(
	        		'status' => 0,
	        		'msg' => $responseData['msg']
	        	);
            }
        }
        echo json_encode($response);
    }

	
	
}