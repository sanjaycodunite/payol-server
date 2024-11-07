<?php 
class Transfer extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkUserPermission();
        $this->load->model('user/Wallet_model');		
        $this->lang->load('user/wallet', 'english');
        
    }

	public function index(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
        $this->parser->parse('user/layout/column-1' , $data);
    
	
	}


	public function getPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
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
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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

        $this->parser->parse('user/layout/column-1', $data);
		
    }

    
	public function beneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$benificaryList = $this->db->select('user_benificary.*,aeps_bank_list.bank_name,user_sender.name as sender_name,user_sender.mobile as sender_mobile')->join('aeps_bank_list','aeps_bank_list.id = user_benificary.bankID')->join('user_sender','user_sender.id = user_benificary.sender_id','left')->get_where('user_benificary',array('user_benificary.account_id'=>$account_id,'user_benificary.user_id'=>$loggedAccountID))->result_array();

		// get bank list
  		$bankList = $this->db->get('aeps_bank_list')->result_array();

  		$senderList = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>1))->result_array();

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
            'senderList' => $senderList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('user/layout/column-1', $data);
		
    }



    public function benificaryAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bankID', 'Bank', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
        $this->form_validation->set_rules('sender_id', 'Sender', 'required');
	        
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
        	 'sender_id' => $post['sender_id'],
        	 'status' => 1,
        	 'created' => date('Y-m-d H:i:s')

        	);
        	
        	$this->db->insert('user_benificary',$bene_data);

        	$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }




    // add member
	public function fundTransfer($bene_id = 0,$sender_id = 0)
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$bene_id))->row_array();

		if($bene_id && !$chk_beneficiary){

			$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}

		$chk_sender_id = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'id'=>$sender_id,'status'=>1))->row_array();

		if($sender_id && !$chk_sender_id){

			$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}

		if(!$sender_id)
		{
			$sender_id = $chk_beneficiary['sender_id'];
		}

		$mobile = isset($loggedUser['mobile']) ? $loggedUser['mobile'] : '';

		$benList = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->result_array();	

		$senderList = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>1))->result_array();			
 		
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
            'sender_id' => $sender_id,
            'senderList' => $senderList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('user/layout/column-1', $data);
		
    }

    // save member
	public function fundTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - DMT Api Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - DMT Api Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('sender_id', 'Sender', 'required|xss_clean');
		$this->form_validation->set_rules('bene_id', 'Beneficiary', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');
        $this->form_validation->set_rules('txnType', 'Transaction Type', 'required|xss_clean');
        
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->fundTransfer($post['bene_id'],$post['sender_id']);
		}
		else
		{	
			$chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['bene_id']))->row_array();

			if(!$chk_beneficiary){

				$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
			}	

			$chk_sender_id = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'id'=>$post['sender_id'],'status'=>1))->row_array();

			if(!$chk_sender_id){

				$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
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
	        $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - DMT Api R-Wallet Balance - '.$chk_wallet_balance['wallet_balance'].'.]'.PHP_EOL;
	        $this->User->generateLog($log_msg);

            // get dmr surcharge
            $surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$loggedAccountID);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - DMT API - Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $final_amount = $amount + $surcharge_amount;
            $before_balance = $chk_wallet_balance['wallet_balance'];

            if($chk_wallet_balance['wallet_balance'] < $final_amount){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - DMT API - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
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
            $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - DMT API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
            $this->User->generateLog($log_msg);

			$data = array(
				'account_id' => $account_id,
				'user_id' => $loggedAccountID,
				'from_sender_id' => $post['sender_id'],
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

			$responseData = $this->Wallet_model->cibMoneyTransfer($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType);

			// save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - DMT Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
            $this->User->generateLog($log_msg);   

            if(isset($responseData['status']) && $responseData['status'] == 1)
			{
				$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
				
			}
			elseif(isset($responseData['status']) && $responseData['status'] == 2)
			{
				$requestID = $responseData['requestID'];
				$rrno = $responseData['rrno'];
				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$loggedAccountID);
				$this->db->where('transaction_id',$transaction_id);
				$this->db->update('user_money_transfer',array('op_txn_id'=>$requestID,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));
				$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
			}
			elseif(isset($responseData['status']) && $responseData['status'] == 3)
			{
				$apiMsg = $responseData['msg'];

				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$loggedAccountID);
				$this->db->where('transaction_id',$transaction_id);
				$this->db->update('user_money_transfer',array('status'=>4,'updated'=>date('Y-m-d H:i:s')));

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
	            $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - DMT API - Member Wallet Refund - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
	            $this->User->generateLog($log_msg);
				
				$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_FAILED'),$apiMsg));
			}

			
		}
	
	}

	//payout beneficiary list 

	public function payoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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

        $this->parser->parse('user/layout/column-1', $data);
		
    }


    //payout benificery auth

    public function payoutBenificaryAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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

        	$this->Az->redirect('user/transfer/payoutBeneficiaryList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }

    //payout transfer
    public function payoutFundTransfer($bene_id = 0)
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$chk_beneficiary = $this->db->get_where('payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$bene_id))->row_array();
		

		if($bene_id && !$chk_beneficiary){

			$this->Az->redirect('user/transfer/payoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}

		$mobile = isset($loggedUser['mobile']) ? $loggedUser['mobile'] : '';

		$benList = $this->db->get_where('payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->result_array();		
 		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/transfer',
            'mobile' => $mobile,
            'bene_id' => $bene_id,
            'benList' => $benList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('user/layout/column-1', $data);
		
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

    public function payoutTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - AEPS Payout Api Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - AEPS Payout Api Post Data - '.json_encode($post).'.]'.PHP_EOL;
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

				$this->Az->redirect('user/transfer/payoutBeneficiaryList', 'system_message_error',lang('DB_ERROR'));
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
	        $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - AEPS Payout Api E-Wallet Balance - '.$chk_wallet_balance['wallet_balance'].'.]'.PHP_EOL;
	        $this->User->generateLog($log_msg);

            // get dmr surcharge
            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID,$txnType);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - AEPS Payout Api - Payout Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            $final_amount = $amount + $surcharge_amount;
            $before_balance = $chk_wallet_balance['wallet_balance'];

            if($chk_wallet_balance['wallet_balance'] < $final_amount){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - AEPS Payout Api - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('user/transfer/payoutBeneficiaryList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
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
            $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - AEPS Payout API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
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

			$responseData = $this->Wallet_model->cibPayout($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType);

			// save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - AEPS Payout Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            

			
			if(isset($responseData['status']) && $responseData['status'] == 1)
			{
				$this->Az->redirect('user/transfer/payoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
				
			}
			elseif(isset($responseData['status']) && $responseData['status'] == 2)
			{
				$requestID = $responseData['requestID'];
				$rrno = $responseData['rrno'];
				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$loggedAccountID);
				$this->db->where('transaction_id',$transaction_id);
				$this->db->update('user_fund_transfer',array('op_txn_id'=>$requestID,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));
				$this->Az->redirect('user/transfer/payoutBeneficiaryList', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
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
	            $log_msg = '['.date('d-m-Y H:i:s').' - U('.$loggedUser['user_code'].') - AEPS Payout API - Member Wallet Refund - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
	            $this->User->generateLog($log_msg);
				
				$this->Az->redirect('user/transfer/payoutBeneficiaryList', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_FAILED'),$apiMsg));
			}
			
		}
	
	}


	public function benificaryAccountList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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

        $this->parser->parse('user/layout/column-1', $data);
		
    }

    //change bank account request

    public function benificaryAccountAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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

        	$this->Az->redirect('user/transfer/benificaryAccountList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }

    // delete beneficiary

    public function deleteBeneficiary($id)
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        // check beneficiary valid or not
        $chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$id))->num_rows();

		if(!$chk_beneficiary){

			$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
		}	

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedUser['id']);
		$this->db->where('id',$id);
		$this->db->delete('user_benificary');
		
		$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('DELETE_SUCCESS'));
	}

	public function senderList()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$stateList = $this->db->order_by('name','asc')->get_where('states',array('country_code_char2'=>'IN'))->result_array();
		
		$benificaryList = $this->db->select('user_sender.*,states.name as state_name')->join('states','states.id = user_sender.state_id')->get_where('user_sender',array('user_sender.account_id'=>$account_id,'user_sender.member_id'=>$loggedAccountID))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/sender',
            'benificaryList' => $benificaryList,
            'stateList' => $stateList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('user/layout/column-1', $data);
		
    }



    public function senderAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|min_length[10]');
        $this->form_validation->set_rules('state_id', 'State', 'required');
        $this->form_validation->set_rules('city', 'City', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('pincode', 'Pincode', 'required');
	        
        if ($this->form_validation->run() == FALSE) {
            
            $this->senderList();
        }
        else
        {   

        	// check mobile already exits or not
        	$chk_mobile = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$post['mobile'],'status'=>1))->num_rows();
        	if($chk_mobile)
        	{
        		$this->Az->redirect('user/transfer/senderList', 'system_message_error',lang('SENDER_MOBILE_EXITS_ERROR'));
        	}

        	$encodeTxnId = do_hash(time().rand(1111,9999));

        	$bene_data = array(
        	 'account_id' => $account_id,	
        	 'member_id' => $loggedAccountID,
        	 'encodeTxnId' => $encodeTxnId,
        	 'name' => $post['name'],
        	 'mobile' => $post['mobile'],
        	 'state_id' => $post['state_id'],
        	 'city' => $post['city'],
        	 'address' => $post['address'],	
        	 'pincode' => $post['pincode'],	
        	 'status' => 0,
        	 'created' => date('Y-m-d H:i:s'),
        	 'created_by' => $loggedAccountID
			);
        	
        	$this->db->insert('user_sender',$bene_data);

        	$api_url = SMS_OTP_SEND_API_URL;

        	$api_url = str_replace('{AUTHKEY}',$accountData['sms_auth_key'],$api_url);
            $api_url = str_replace('{TEMPID}',$accountData['sms_template_id'],$api_url);
            $api_url = str_replace('{MOBILE}',$post['mobile'],$api_url);
            
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

			// Execute
			$output = curl_exec($curl);

			// Close
			curl_close ($curl);

			$smsLogData = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'api_url' => $api_url,
        	 'api_response' => $output,
        	 'created' => date('Y-m-d H:i:s'),
        	 'created_by' => $loggedAccountID
			);
        	
        	$this->db->insert('sms_api_response',$smsLogData);


        	$this->Az->redirect('user/transfer/senderOtp/'.$encodeTxnId, 'system_message_error',lang('SENDER_OTP_SEND_SUCCESS'));
        }
    
    }

    public function senderOtp($encodeTxnId = '')
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check mobile already exits or not
    	$chk_mobile = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->num_rows();
    	if(!$chk_mobile)
    	{
    		$this->Az->redirect('user/transfer/senderList', 'system_message_error',lang('WALLET_ERROR'));
    	}

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'transfer/sender-otp',
            'manager_description' => lang('SITE_NAME'),
            'encodeTxnId' => $encodeTxnId,
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('user/layout/column-1', $data);
		
    }

    public function resendSenderOtp($encodeTxnId = ''){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

    	// check mobile already exits or not
    	$chk_mobile = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->num_rows();
    	if(!$chk_mobile)
    	{
    		$this->Az->redirect('user/transfer/senderList', 'system_message_error',lang('WALLET_ERROR'));
    	}

    	$getTxnData = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->row_array();
    	$mobile = isset($getTxnData['mobile']) ? $getTxnData['mobile'] : '';

    	$request = array(
    		'authkey' => $accountData['sms_auth_key'],
    		'mobile' => '+91'.$mobile,
    		'retrytype' => 'text'
    	);
    	
    	$api_url = SMS_OTP_RESEND_API_URL;

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

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

		// Execute
		$output = curl_exec($curl);

		// Close
		curl_close ($curl);

		$smsLogData = array(
    	 'account_id' => $account_id,	
    	 'user_id' => $loggedAccountID,
    	 'api_url' => $api_url,
    	 'api_response' => $output,
    	 'post_data' => json_encode($request),
    	 'created' => date('Y-m-d H:i:s'),
    	 'created_by' => $loggedAccountID
		);
    	
    	$this->db->insert('sms_api_response',$smsLogData);


    	$this->Az->redirect('user/transfer/senderOtp/'.$encodeTxnId, 'system_message_error',lang('SENDER_OTP_SEND_SUCCESS'));
        
    
    }

    public function senderOtpAuth(){

		//check for foem validation
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $encodeTxnId = $post['encodeTxnId'];
        $this->load->library('form_validation');
        $this->form_validation->set_rules('otp_code', 'OTP', 'required');
            
        if ($this->form_validation->run() == FALSE) {
            
            $this->senderOtp($encodeTxnId);
        }
        else
        {   

        	// check mobile already exits or not
	    	$chk_mobile = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->num_rows();
	    	if(!$chk_mobile)
	    	{
	    		$this->Az->redirect('user/transfer/senderList', 'system_message_error',lang('WALLET_ERROR'));
	    	}

	    	$getTxnData = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->row_array();
	    	$mobile = isset($getTxnData['mobile']) ? $getTxnData['mobile'] : '';

	    	$request = array(
	    		'authkey' => $accountData['sms_auth_key'],
	    		'mobile' => '+91'.$mobile,
	    		'otp' => $post['otp_code']
	    	);
        	
        	$api_url = SMS_OTP_AUTH_API_URL;

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

			// Request Body
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

			// Execute
			$output = curl_exec($curl);

			// Close
			curl_close ($curl);

			$smsLogData = array(
        	 'account_id' => $account_id,	
        	 'user_id' => $loggedAccountID,
        	 'api_url' => $api_url,
        	 'api_response' => $output,
        	 'post_data' => json_encode($request),
        	 'created' => date('Y-m-d H:i:s'),
        	 'created_by' => $loggedAccountID
			);
        	
        	$this->db->insert('sms_api_response',$smsLogData);

        	$decodeResponse = json_decode($output,true);
        	if(isset($decodeResponse['type']) && $decodeResponse['type'] == 'success')
        	{
        		$this->db->where('account_id',$account_id);
        		$this->db->where('member_id',$loggedAccountID);
        		$this->db->where('encodeTxnId',$encodeTxnId);
        		$this->db->update('user_sender',array('otp_verify'=>1,'status'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));
				$this->Az->redirect('user/transfer/senderList', 'system_message_error',lang('SENDER_OTP_VERIFY_SUCCESS'));
        	}
        	else
        	{
        		$this->Az->redirect('user/transfer/senderOtp/'.$encodeTxnId, 'system_message_error',lang('SENDER_OTP_VERIFY_FAILED'));
        	}
        }
    
    }

    public function activeSender($encodeTxnId = '')
    {
    	$account_id = $this->User->get_domain_account();
    	$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check mobile already exits or not
    	$chk_mobile = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->num_rows();
    	if(!$chk_mobile)
    	{
    		$this->Az->redirect('user/transfer/senderList', 'system_message_error',lang('WALLET_ERROR'));
    	}

   		$getTxnData = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->row_array();
    	$mobile = isset($getTxnData['mobile']) ? $getTxnData['mobile'] : '';

    	$api_url = SMS_OTP_SEND_API_URL;

    	$api_url = str_replace('{AUTHKEY}',$accountData['sms_auth_key'],$api_url);
        $api_url = str_replace('{TEMPID}',$accountData['sms_template_id'],$api_url);
        $api_url = str_replace('{MOBILE}',$mobile,$api_url);
        
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

		// Execute
		$output = curl_exec($curl);

		// Close
		curl_close ($curl);

		$smsLogData = array(
    	 'account_id' => $account_id,	
    	 'user_id' => $loggedAccountID,
    	 'api_url' => $api_url,
    	 'api_response' => $output,
    	 'post_data' => json_encode($request),
    	 'created' => date('Y-m-d H:i:s'),
    	 'created_by' => $loggedAccountID
		);
    	
    	$this->db->insert('sms_api_response',$smsLogData);

    	$decodeResponse = json_decode($output,true);
    	if(isset($decodeResponse['type']) && $decodeResponse['type'] == 'success')
    	{
    		$this->Az->redirect('user/transfer/senderOtp/'.$encodeTxnId, 'system_message_error',lang('SENDER_OTP_SEND_SUCCESS'));
    	}
    	else
    	{
    		$msg = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$decodeResponse['message'].'</div>';
    		$this->Az->redirect('user/transfer/senderList', 'system_message_error',$msg);
    	}
		
    }

    public function getBenData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
    	$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
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

  			$senderList = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>1))->result_array();		

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
	        $str.='<label><b>Sender*</b></label>';
	        $str.='<select class="form-control" name="sender_id">';
	        $str.='<option value="">Select Sender</option>';
	        if($senderList){
	        	foreach($senderList as $list){
	        		if($list['id'] == $dmrData['sender_id'])
	        		{
	        			$str.='<option value="'.$list['id'].'" selected="selected">'.$list['name'].' ('.$list['mobile'].')</option>';
	        		}
	        		else
	        		{
	        			$str.='<option value="'.$list['id'].'">'.$list['name'].' ('.$list['mobile'].'</option>';
	        		}
	        	}
	        }
	        $str.='</select>';
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
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(6, $activeService)){
			$this->Az->redirect('user/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bankID', 'Bank', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
        $this->form_validation->set_rules('sender_id', 'Sender', 'required');
	        
        if ($this->form_validation->run() == FALSE) {
            
            $msg = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Please enter required fields.</div>';
    		$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',$msg);
        }
        else
        {   
        	// check beneficiary valid or not
	        $chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['recordID']))->num_rows();

			if(!$chk_beneficiary){

				$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('DB_ERROR'));
			}	
        	$bene_data = array(
        	 'account_holder_name' => $post['account_holder_name'],
        	 'bankID' => $post['bankID'],
        	 'account_no' => $post['account_number'],
        	 'ifsc' => $post['ifsc'],
        	 'encode_ban_id' => do_hash($post['account_number']),	
        	 'sender_id' => $post['sender_id'],
        	 'status' => 1,
        	 'updated' => date('Y-m-d H:i:s')
			);
        	
        	$this->db->where('id',$post['recordID']);
        	$this->db->where('account_id',$account_id);
        	$this->db->where('user_id',$loggedAccountID);
        	$this->db->update('user_benificary',$bene_data);

        	$this->Az->redirect('user/transfer/beneficiaryList', 'system_message_error',lang('BENEFICIARY_SAVE_SUCCESS'));
        }
    
    }

	
	
}