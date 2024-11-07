<?php 
class Payout extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkDistributorPermission();
        $this->load->model('distributor/Wallet_model');		
        $this->lang->load('distributor/wallet', 'english');
        
    }

	public function index(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'payout/list'
        );
        $this->parser->parse('distributor/layout/column-1' , $data);
    
	
	}


	public function getPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.is_payout_open = 1";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.is_payout_open = 1";
			
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

    //payout transfer
    public function payoutFundTransfer()
    {
    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'payout/transfer',
            'bankList' => $bankList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
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
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(2, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Payout Open Api Called.]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Payout Open Api Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateLog($log_msg);
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
		$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
		$this->form_validation->set_rules('confirm_account_no', 'Confirm Account No.', 'required|xss_clean|matches[account_no]');
		$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
		$this->form_validation->set_rules('bankID', 'Bank', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->payoutFundTransfer();
		}
		else
		{	
			$memberID = $loggedUser['user_code'];
			$mobile = $post['mobile'];
			$account_holder_name = $post['account_holder_name'];
			$account_no = $post['account_no'];
			$ifsc = $post['ifsc'];
			$bankID = $post['bankID'];
			$amount = $post['amount'];
			$txnType = 'IFS';
			$transaction_id = time().rand(1111,9999);
			$receipt_id = rand(111111,999999);

			
			

            // get dmr surcharge
            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Payout Open Api - Payout Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            $final_amount = $amount + $surcharge_amount;
            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            // save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Payout Open E-Wallet Balance - '.$chk_wallet_balance['wallet_balance'].'.]'.PHP_EOL;
	        $this->User->generateLog($log_msg);

            if($before_balance < $final_amount){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Payout Open Api - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('distributor/payout/payoutFundTransfer', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
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
                'description'         => 'Payout #'.$transaction_id.' Amount Deducted.'
            );

            $this->db->insert('member_wallet',$wallet_data);


            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Payout Open API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
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
				'is_payout_open' => 1,
				'created' => date('Y-m-d H:i:s')
			);
			$this->db->insert('user_fund_transfer',$data); 
			$recordID = $this->db->insert_id(); 

			$responseData = $this->Wallet_model->cibPayoutOpen($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType);

			// save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Payout Open Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            

			
			if(isset($responseData['status']) && $responseData['status'] == 1)
			{
				$this->Az->redirect('distributor/payout/payoutFundTransfer', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
				
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
	            $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - Payout Open Api - Distribute Commision/Surcharge Start]'.PHP_EOL;
	            $this->User->generateLog($log_msg);

				$this->User->distribute_payout_commision($recordID,$transaction_id,$amount,$loggedAccountID,$surcharge_amount,'DT',$loggedUser['user_code']);

				// save system log
	            $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - Payout Open Api - Distribute Commision/Surcharge End]'.PHP_EOL;
	            $this->User->generateLog($log_msg);

				$this->Az->redirect('distributor/payout/payoutFundTransfer', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));
			}
			elseif(isset($responseData['status']) && $responseData['status'] == 3)
			{
				$apiMsg = $responseData['msg'];

				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$loggedAccountID);
				$this->db->where('transaction_id',$transaction_id);
				$this->db->update('user_fund_transfer',array('status'=>4,'updated'=>date('Y-m-d H:i:s')));

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
	                'description'         => 'Payout #'.$transaction_id.' Amount Refund.'
	            );

	            $this->db->insert('member_wallet',$wallet_data);

	            // save system log
	            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - AEPS Payout API - Member Wallet Refund - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
	            $this->User->generateLog($log_msg);
				
				$this->Az->redirect('distributor/payout/payoutFundTransfer', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_FAILED'),$apiMsg));
			}
			
		}
	
	}

	
}