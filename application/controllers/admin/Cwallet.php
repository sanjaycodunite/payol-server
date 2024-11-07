<?php 
class Cwallet extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkAdminPermission();
        
    }

	public function walletList(){

		$account_id = $this->User->get_domain_account();
		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet History Page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
		
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
            'content_block' => 'cwallet/walletList'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getWalletList()
	{	
		$account_id = $this->User->get_domain_account();
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
	   	$toDate = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
			$toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'id',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_collection_wallet as a where a.wallet_type = 1 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_collection_wallet as a where a.wallet_type = 1 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.description LIKE '%".$keyword."%' )";    
			}

			if($fromDate != '' && $toDate != '') {   
				$sql.=" AND  DATE(a.created) >= '".$fromDate."'  AND  DATE(a.created) <= '".$toDate."' ";    
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY a.id DESC  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['before_balance'].' /-';
				if($list['type'] == 1) {
					$nestedData[] = '<font color="green">'.$list['amount'].' /-</font>';
				}
				elseif($list['type'] == 2) {
					$nestedData[] = '<font color="red">'.$list['amount'].' /-</font>';

				}
				$nestedData[] = $list['after_balance'].' /-';
				
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				if($list['type'] == 1) {
					$nestedData[] = '<font color="green">Cr.</font>';
				}
				elseif($list['type'] == 2) {
					$nestedData[] = '<font color="red">Dr.</font>';

				}

				
				$nestedData[] = $list['description'];

				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['status'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				

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

	public function bankTransfer()
    {
    	$account_id = $this->User->get_domain_account();
    	$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();
        $loggedAccountID = $loggedUser['id'];

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer Page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
        
		if($accountData['is_auto_bank_settlement'] == 0){
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer is not allowed.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/cwallet/walletList', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// get account bank detail
		$accountData = $this->db->select('account_holder_name,account_number,ifsc,bankID')->get_where('account',array('id'=>$account_id))->row_array();
		$account_holder_name = isset($accountData['account_holder_name']) ? $accountData['account_holder_name'] : '';
		$account_number = isset($accountData['account_number']) ? $accountData['account_number'] : '';
		$ifsc = isset($accountData['ifsc']) ? $accountData['ifsc'] : '';
		$bankID = isset($accountData['bankID']) ? $accountData['bankID'] : 0;

		$availableBalance = $this->User->getMemberCollectionWalletBalanceSP($loggedUser['id']);

		// get today total collection
		$getTodayBalance = $this->db->select('SUM(amount) as totalAmount')->get_where('collection_wallet',array('account_id'=>$account_id,'type'=>1,'DATE(created)'=>date('Y-m-d')))->row_array();
		$todayBalance = isset($getTodayBalance['totalAmount']) ? $getTodayBalance['totalAmount'] : 0 ;

		$tranferLimit = $availableBalance - $todayBalance;
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'cwallet/bankTransfer',
            'account_holder_name' => $account_holder_name,
            'account_number' => $account_number,
            'availableBalance' => $availableBalance,
            'tranferLimit' => $tranferLimit,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('admin/layout/column-1', $data);
		
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

    public function transferAuth()
	{
		$account_id = $this->User->get_domain_account();
    	$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();
        $loggedAccountID = $loggedUser['id'];
        $admin_id = $loggedAccountID;
        $post = $this->security->xss_clean($this->input->post());
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer Auth Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

		if($accountData['is_auto_bank_settlement'] == 0){
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer Not allowed.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/cwallet/walletList', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('bene_id', 'Bank Account', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');
        $this->form_validation->set_rules('txnType', 'Transaction Type', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
			
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer Validation Error.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->bankTransfer();
		}
		else
		{	

			// get account bank detail
			$accountData = $this->db->select('account_holder_name,account_number,ifsc,bankID')->get_where('account',array('id'=>$account_id))->row_array();
			$account_holder_name = isset($accountData['account_holder_name']) ? $accountData['account_holder_name'] : '';
			$account_number = isset($accountData['account_number']) ? $accountData['account_number'] : '';
			$ifsc = isset($accountData['ifsc']) ? $accountData['ifsc'] : '';
			$bankID = isset($accountData['bankID']) ? $accountData['bankID'] : 0;

			$availableBalance = $this->User->getMemberCollectionWalletBalanceSP($loggedUser['id']);

			// get today total collection
			$getTodayBalance = $this->db->select('SUM(amount) as totalAmount')->get_where('collection_wallet',array('account_id'=>$account_id,'type'=>1,'DATE(created)'=>date('Y-m-d')))->row_array();
			$todayBalance = isset($getTodayBalance['totalAmount']) ? $getTodayBalance['totalAmount'] : 0 ;

			$tranferLimit = $availableBalance - $todayBalance;

			if($tranferLimit < $post['amount']){

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer Limit Error - Transfer Limit - '.$tranferLimit.'.]'.PHP_EOL;
		        $this->User->generateAccountActivityLog($log_msg);
                
                $this->Az->redirect('admin/cwallet/bankTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Insufficient balance in your account.</div>');
            }


			if($account_holder_name && $account_number && $ifsc)
    		{
    			$availableBalance = $this->User->getMemberCollectionWalletBalanceSP($loggedUser['id']);
    			
    			$transaction_id = time().rand(1111,9999);

    			$settlementAmount = $post['amount'];

    			$updatedSettlementBalance = $availableBalance - $settlementAmount;

		        $wallet_data = array(
		            'account_id'          => $account_id,
		            'member_id'           => $admin_id,    
		            'before_balance'      => $availableBalance,
		            'amount'              => $settlementAmount,  
		            'after_balance'       => $updatedSettlementBalance,      
		            'status'              => 1,
		            'type'                => 2,   
		            'wallet_type'         => 1,   
		            'created'             => date('Y-m-d H:i:s'),      
		            'description'         => 'Bank Account#'.$account_number.' TxnID #'.$transaction_id.' Settlement'
		        );

		        $this->db->insert('collection_wallet',$wallet_data);

		        // save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer Wallet Updated - New Balance - '.$updatedSettlementBalance.' - TxnID - '.$transaction_id.'.]'.PHP_EOL;
		        $this->User->generateAccountActivityLog($log_msg);

		        $txnType = $post['txnType'];
		        $receipt_id = rand(111111,999999);

		        $data = array(
					'account_id' => $account_id,
					'user_id' => $loggedAccountID,
					'before_wallet_balance' => $availableBalance,
					'transfer_amount' => $settlementAmount,
					'total_wallet_charge' => $settlementAmount,
					'after_wallet_balance' => $updatedSettlementBalance,
					'txnType' => $txnType,
					'transaction_id' => $transaction_id,
					'encode_transaction_id' => do_hash($transaction_id),
					'status' => 2,
					'wallet_type' => 1,
					'invoice_no' => $receipt_id,
					'memberID' => $loggedUser['user_code'],
					'mobile' => $loggedUser['mobile'],
					'account_holder_name' => $account_holder_name,
					'account_no' => $account_number,
					'ifsc' => $ifsc,
					'created' => date('Y-m-d H:i:s')
				);
				$this->db->insert('settlement_bank_transfer',$data); 

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer Record Saved to DB - TxnID - '.$transaction_id.'.]'.PHP_EOL;
		        $this->User->generateAccountActivityLog($log_msg);
				
		        $responseData = $this->User->cibAutoSettlement($account_holder_name,$account_number,$ifsc,$settlementAmount,$transaction_id,$bankID,$txnType,$loggedUser['user_code'],$loggedUser['name'],$account_id,$admin_id);

				
	            if(isset($responseData['status']) && $responseData['status'] == 2)
				{
					$requestID = $responseData['requestID'];
					$rrno = $responseData['rrno'];
					$this->db->where('account_id',$account_id);
					$this->db->where('user_id',$loggedAccountID);
					$this->db->where('transaction_id',$transaction_id);
					$this->db->update('settlement_bank_transfer',array('op_txn_id'=>$requestID,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));

					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer successfully redirect back to bank transfer page.]'.PHP_EOL;
			        $this->User->generateAccountActivityLog($log_msg);

					$this->Az->redirect('admin/cwallet/bankTransfer', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulation ! Your transaction credited successfully.</div>');
				}
	            elseif(isset($responseData['status']) && $responseData['status'] == 3)
				{
					$apiMsg = $responseData['msg'];

					$before_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
					$after_wallet_balance = $before_balance + $settlementAmount;    

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $admin_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $settlementAmount,  
		                'after_balance'       => $after_wallet_balance,      
		                'status'              => 1,
		                'type'                => 1,   
		                'wallet_type'		  => 1,   
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'Bank Account#'.$account_number.' TxnID #'.$transaction_id.' Settlement Refund'
		            );

		            $this->db->insert('collection_wallet',$wallet_data);

			        // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer failed Updated Wallet Balance - '.$after_wallet_balance.' redirect back to bank transfer page.]'.PHP_EOL;
			        $this->User->generateAccountActivityLog($log_msg);

			        $this->Az->redirect('admin/cwallet/bankTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Your transaction failed, your wallet is refunded.</div>');
				}

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Wallet Bank Transfer is under process redirect back to bank transfer page.]'.PHP_EOL;
		        $this->User->generateAccountActivityLog($log_msg);

				$this->Az->redirect('admin/cwallet/bankTransfer', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulation ! Your transaction proceed successfully.</div>');

		        
    		}
    		else
    		{
    			$this->Az->redirect('admin/cwallet/bankTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Account Detail Not Found.</div>');
    		}
			
		}
	
	}

	public function bankTransferReport(){

		$account_id = $this->User->get_domain_account();
    	$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $admin_id = $loggedAccountID;
        $user_ip_address = $this->User->get_user_ip();

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Bank Transfer Report Page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
        
		if($accountData['is_auto_bank_settlement'] == 0){
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Settlement Bank Transfer Report Page - Not allowed redirect back to settlement wallet list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/cwallet/walletList', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'cwallet/bankTransferReport'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_settlement_bank_transfer as a where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_settlement_bank_transfer as a where a.account_id = '$account_id'";
			
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

	
	
}