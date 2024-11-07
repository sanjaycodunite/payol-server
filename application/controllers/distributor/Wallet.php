<?php 
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
class Wallet extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkDistributorPermission();
        $this->load->model('distributor/Wallet_model');		
        $this->lang->load('distributor/wallet', 'english');
        
    }

    public function myWalletList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
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
            'content_block' => 'wallet/myWalletList'
        );
        $this->parser->parse('distributor/layout/column-1' , $data);
    
	
	}


	public function getMyWalletList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$member_id = '';
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$member_id = isset($filterData[1]) ? trim($filterData[1]) : '';
			$date = isset($filterData[2]) ? trim($filterData[2]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql.=" AND  b.id = $member_id ";    
			}

			if($date != '') {   
				$sql.=" AND  DATE(a.created) = '".$date."' ";    
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
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

	public function walletList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		// get users list
		$memberList = $this->db->where_in('role_id',array(5))->get_where('users',array('account_id'=>$account_id,'created_by'=>$loggedAccountID))->result_array();

  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'memberList' => $memberList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'wallet/walletList'
        );
        $this->parser->parse('distributor/layout/column-1' , $data);
    
	
	}


	public function getWalletList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$member_id = '';
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$member_id = isset($filterData[1]) ? trim($filterData[1]) : '';
			$date = isset($filterData[2]) ? trim($filterData[2]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND b.created_by = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND b.created_by = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql.=" AND  b.id = $member_id ";    
			}

			if($date != '') {   
				$sql.=" AND  DATE(a.created) = '".$date."' ";    
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
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



	public function creditList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		// get users list
		$memberList = $this->db->where_in('role_id',array(4,5))->get_where('users',array('account_id'=>$account_id,'created_by'=>$loggedAccountID))->result_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'memberList'  => $memberList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'wallet/creditList'
        );
        $this->parser->parse('distributor/layout/column-1' , $data);
    
	
	}


	public function getCreditList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$member_id = '';
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$member_id = isset($filterData[1]) ? trim($filterData[1]) : '';
			$date = isset($filterData[2]) ? trim($filterData[2]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.type = 1 AND a.account_id = '$account_id' AND b.created_by = '$loggedAccountID' AND a.wallet_type = 1";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.type = 1 AND a.account_id = '$account_id' AND b.created_by = '$loggedAccountID' AND a.wallet_type = 1";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR b.mobile LIKE '".$keyword."%' ";
				$sql.=" OR b.user_code LIKE '".$keyword."%' ";
				$sql.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql.=" AND  b.id = $member_id ";    
			}

			if($date != '') {   
				$sql.=" AND  DATE(a.created) = '".$date."' ";    
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
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


	
	
	// add member
	public function addWallet()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		// get users list
		$memberList = $this->db->where_in('role_id',array(5))->get_where('users',array('account_id'=>$account_id,'created_by'=>$loggedAccountID))->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/addWallet',
            'memberList'	=> $memberList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    // save member
	public function saveWallet()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->input->post();

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Credit/Debit Wallet Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('member', 'Member', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->addWallet();
		}
		else
		{	
			// check member is valid or not
			$chk_member = $this->db->get_where('users',array('id'=>$post['member'],'account_id'=>$account_id,'created_by'=>$loggedAccountID))->num_rows();
			if(!$chk_member)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Credit/Debit Wallet Member Valid Error.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				$this->Az->redirect('distributor/wallet/walletList', 'system_message_error',lang('WALLET_ERROR'));
			}

			$post['type'] = 1;
			
			$type = $post['type'];
			if($type == 1)
			{
				// check account wallet balance
				$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Credit/Debit Member Wallet Balance - '.$wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
		        $chk_wallet_balance = $this->db->select('min_wallet_balance')->get_where('users',array('id'=>$loggedUser['id']))->row_array();
		        $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            	$final_deduct_wallet_balance = $wallet_balance - $min_wallet_balance;  
				if($post['amount'] > $final_deduct_wallet_balance)
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Credit/Debit Member Insufficient Wallet Error.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
					$this->Az->redirect('distributor/wallet/walletList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
				}
			}
			else
			{
				// check account wallet balance
				$wallet_balance = $this->User->getMemberWalletBalanceSP($post['member']);
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Credit/Debit Member Wallet Balance - '.$wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				if($post['amount'] > $wallet_balance)
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Credit/Debit Member Insufficient Wallet Error.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);
					$this->Az->redirect('distributor/wallet/walletList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
				}
			}
			
			$status = $this->Wallet_model->saveWallet($post);
			
			if($status == true)
			{
				$this->Az->redirect('distributor/wallet/walletList', 'system_message_error',lang('WALLET_SAVED'));
			}
			else
			{
				$this->Az->redirect('distributor/wallet/walletList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
		}
	
	}
	
	public function getMemberWalletBalance($memberID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		echo json_encode(array(
			'status' => 1,
			'balance' => $this->User->getMemberWalletBalanceSP($memberID)
		));
	}
	
	public function requestList()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
		$siteUrl = site_url();

		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/requestList',
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('distributor/layout/column-1', $data);
		
    }
	
	public function getRequestList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
		$accountID = $loggedUser['id'];
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
			6 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_member_fund_request as a LEFT JOIN tbl_users as b ON b.id = a.member_id WHERE a.request_wallet_type = 1 AND a.account_id = '$account_id' AND b.created_by = '$accountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name, b.user_code as member_code FROM tbl_member_fund_request as a LEFT JOIN tbl_users as b ON b.id = a.member_id WHERE a.request_wallet_type = 1 AND a.account_id = '$account_id' AND b.created_by = '$accountID'";	

			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR a.request_id LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( DATE(a.created) = '".$date."%' )";
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 6 : $requestData['order'][0]['column'] : 6;
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
				$nestedData[] = $list['member_name'].' ('.$list['member_code'].')';
				$nestedData[] = $list['request_id'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = 'INR '.number_format($list['request_amount'],2);
				$nestedData[] = date('d-m-Y',strtotime($list['created']));
				
				if($list['status'] == 1)
				{
					$nestedData[] = '<font color="black">Pending</font>';
					$nestedData[] ='<a title="Approve" class="btn btn-success btn-sm" href="'.base_url('distributor/wallet/updateRequestAuth').'/'.$list['id'].'/1" onclick="return confirm(\'Are you sure you want to approve this request?\')"><i class="fa fa-check" aria-hidden="true"></i></a> <a title="Reject" class="btn btn-danger btn-sm" href="'.base_url('distributor/wallet/updateRequestAuth').'/'.$list['id'].'/2" onclick="return confirm(\'Are you sure you want to reject this request?\')"><i class="fa fa-times" aria-hidden="true"></i></a>';
				}
				elseif($list['status'] == 2)
				{
					$nestedData[] = '<font color="green">Approved</font>';
					$nestedData[] ='Updated';
				}
				elseif($list['status'] == 3)
				{
					$nestedData[] = '<font color="red">Rejected</font>';
					$nestedData[] ='Updated';
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
	
	public function updateRequestAuth($requestID = 0, $status = 0)
	{
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		// check request id valid or not
		$chk_request_id = $this->db->join('users','users.id = member_fund_request.member_id')->get_where('member_fund_request',array('member_fund_request.id'=>$requestID,'member_fund_request.status'=>1,'member_fund_request.account_id'=>$account_id,'users.created_by' => $loggedAccountID))->num_rows();
		if(!$chk_request_id)
		{
			$this->Az->redirect('distributor/wallet/requestList', 'system_message_error',lang('WALLET_ERROR'));
		}

		if($status == 1)
		{
			// check account wallet balance
			$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

			$get_request_data = $this->db->get_where('member_fund_request',array('id'=>$requestID,'status'=>1))->row_array();
	        $memberID = $get_request_data['member_id'];
	        $amount = $get_request_data['request_amount'];

			if($amount > $wallet_balance)
			{
				$this->Az->redirect('distributor/wallet/requestList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
			}
		}
		
		$this->Wallet_model->updateRequestAuth($requestID,$status);
		if($status == 1){
			$this->Az->redirect('distributor/wallet/requestList', 'system_message_error',lang('REQUEST_APPROVE_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('distributor/wallet/requestList', 'system_message_error',lang('REQUEST_REJECT_SUCCESS'));
		}
	}


	public function myRequestList()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
		$siteUrl = site_url();

		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/myRequestList',
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('distributor/layout/column-1', $data);
		
    }
	
	public function getMyRequestList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
		$accountID = $loggedUser['id'];
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
			5 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_member_fund_request as a LEFT JOIN tbl_users as b ON b.id = a.member_id WHERE a.request_wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id = '$accountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name, b.user_code as member_code FROM tbl_member_fund_request as a LEFT JOIN tbl_users as b ON b.id = a.member_id WHERE a.request_wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id = '$accountID'";	

			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR a.request_id LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( DATE(a.created) = '".$date."%' )";
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 6 : $requestData['order'][0]['column'] : 6;
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
				$nestedData[] = $list['member_name'].' ('.$list['member_code'].')';
				$nestedData[] = $list['request_id'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = 'INR '.number_format($list['request_amount'],2);
				$nestedData[] = date('d-m-Y',strtotime($list['created']));
				
				if($list['status'] == 1)
				{
					$nestedData[] = '<font color="black">Pending</font>';
				}
				elseif($list['status'] == 2)
				{
					$nestedData[] = '<font color="green">Approved</font>';
					
				}
				elseif($list['status'] == 3)
				{
					$nestedData[] = '<font color="red">Rejected</font>';
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

	public function fundRequest()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
		$siteUrl = site_url();

		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/fundRequest',
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    public function requestAuth()
	{
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->fundRequest();
		}
		else
		{
			
			$status = $this->Wallet_model->generateFundRequest($post);
			
			if($status == true)
			{
				$this->Az->redirect('distributor/wallet/myRequestList', 'system_message_error',lang('REQUEST_GENERATE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('distributor/wallet/myRequestList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
		}
	
	}

	public function topup()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	//get logged user info
        $activeGateway = $this->User->account_active_gateway();
        if(!in_array(1, $activeGateway)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }

	 	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/topup',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    // save member
	public function topupAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->input->post();

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Topup Wallet Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->topup();
		}
		else
		{	
			$activeKeyData = $this->User->account_razorpay_key();
			$keyId = $activeKeyData['key'];
			$keySecret = $activeKeyData['secret'];

			$amount = $post['amount'];


			$request_id = rand(1111,9999).time();
			$api = new Api($keyId, $keySecret);
			$orderData = [
				'receipt'         => $request_id,
				'amount'          => $amount * 100, // 2000 rupees in paise
				'currency'        => 'INR',
				'payment_capture' => 1 // auto capture
			];

			$razorpayOrder = $api->order->create($orderData);
			$order_id = $razorpayOrder['id'];

			

	        // get member data
	        $userData = $this->db->select('name,email,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();
	        
	        $client_name = $userData['name'];
			$client_email = $userData['email'];
			$client_mobile = $userData['mobile'];

			$accountData = $this->User->get_account_data($account_id);
			$account_name = $accountData['title'];
			
	        $commisionData = $this->User->get_gateway_charge($amount,$loggedUser['id']);
        	$com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        	$is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

			$surcharge_amount = 0;
			if($is_surcharge)
			{
				$surcharge_amount = $com_amount;
			}
			$wallet_settlement_amount = $amount - $surcharge_amount;

			$tokenData = array(
	            'account_id' => $account_id,
	            'member_id' => $loggedAccountID,
	            'request_id' => $request_id,
	            'request_amount' => $amount,
	            'charge_amount' => $surcharge_amount,
	            'wallet_settlement_amount' => $wallet_settlement_amount,
	            'status' => 1,
	            'created' => date('Y-m-d H:i:s'),
	            'created_by' => $loggedAccountID
	        );
	        $this->db->insert('member_gateway_history',$tokenData);

	        $razorPayData = [
			    "key"               => $keyId,
			    "amount"            => $amount,
			    "name"              => $client_name,
			    "description"       => "Topup Wallet",
			    "image"             => "",
			    "prefill"           => [
			    "name"              => $client_name,
			    "email"             => $client_email,
			    "contact"           => $client_mobile,
			    ],
			    "notes"             => [
			    "address"           => "",
			    "merchant_order_id" => $request_id,
			    ],
			    "theme"             => [
			    "color"             => "#F37254"
			    ],
			    "order_id"          => $order_id,
			];

			$jsondata = json_encode($razorPayData);

			

			$siteUrl = site_url();
	        $data = array(
	            'site_url' => $siteUrl,
				'loggedUser' => $loggedUser,
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'content_block' => 'wallet/topup-confirm',
	            'manager_description' => lang('SITE_NAME'),
	            'client_name' => $client_name,
	            'client_email' => $client_email,
	            'client_mobile' => $client_mobile,
	            'account_name' => $account_name,
	            'order_id'  => $order_id,
	            'request_id'=> $request_id,
	            'loggedAccountID' => $loggedAccountID,
	            'amount' => $amount,
	            'activeKeyData' => $activeKeyData,
	            'jsondata' => $jsondata,
	            'com_amount' => $com_amount,
	            'is_surcharge' => $is_surcharge,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getSystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning() 
			);

	        $this->parser->parse('distributor/layout/column-1', $data);
			
		}
	
	}

	public function paymentResponse()
	{
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$success = false;
		$activeKeyData = $this->User->account_razorpay_key();
		$keyId = $activeKeyData['key'];
		$keySecret = $activeKeyData['secret'];



		
		$default_status = 1;

		$error = "Payment Failed";
		if (empty($_POST['razorpay_payment_id']) === false)
		{
			// save payment request data
			
			$loggedAccountID = $_POST['loggedAccountID'];
			$ORDER_ID = $_POST['order_id'];
			$payment_request_id = $_POST['razorpay_payment_id'];
			$request_id = $_POST['shopping_order_id'];
			$amount = $_POST['amount'];

			
 
			$api = new Api($keyId, $keySecret);

			try
			{
				// Please note that the razorpay order ID must
				// come from a trusted source (session here, but
				// could be database or something else)
				$attributes = array(
					'razorpay_order_id' => $_POST['order_id'],
					'razorpay_payment_id' => $_POST['razorpay_payment_id'],
					'razorpay_signature' => $_POST['razorpay_signature']
				);

				$api->utility->verifyPaymentSignature($attributes);
				$success = true;
			}
			catch(SignatureVerificationError $e)
			{
				// update request status
	            $this->db->where('account_id',$account_id);
	            $this->db->where('request_id',$request_id);
	            $this->db->where('member_id',$loggedAccountID);
	            $this->db->update('member_gateway_history',array('order_id'=>$ORDER_ID,'gateway_txn_id'=>$payment_request_id,'status'=>3,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));
				$success = false;
				$error = 'Razorpay Error : ' . $e->getMessage();
				$this->Az->redirect('distributor/wallet/topup', 'system_message_error',$e->getMessage());
			}
		}

		if ($success === true)
		{	

			$loggedAccountID = $_POST['loggedAccountID'];
			$ORDER_ID = $_POST['order_id'];
			$payment_request_id = $_POST['razorpay_payment_id'];
			$request_id = $_POST['shopping_order_id'];
			$amount = $_POST['amount'];
			
            $this->Az->redirect('distributor/wallet/topup', 'system_message_error',lang('PAYMENT_SUCCESS'));
	    
	    }
		else
		{	
			$loggedAccountID = $_POST['loggedAccountID'];
			$ORDER_ID = $_POST['order_id'];
			$payment_request_id = $_POST['razorpay_payment_id'];
			$request_id = $_POST['shopping_order_id'];
			$amount = $_POST['amount'];

			// update request status
            $this->db->where('account_id',$account_id);
            $this->db->where('request_id',$request_id);
            $this->db->where('member_id',$loggedAccountID);
            $this->db->update('member_gateway_history',array('order_id'=>$ORDER_ID,'gateway_txn_id'=>$payment_request_id,'status'=>3,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));
			
			$this->Az->redirect('distributor/wallet/topup', 'system_message_error',lang('PAYMENT_FAILED'));
		}

		
	}

	public function topupHistory(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
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
            'content_block' => 'wallet/topupHistory'
        );
        $this->parser->parse('distributor/layout/column-1' , $data);
    
	
	}


	public function getTopupHistory()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$member_id = '';
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$member_id = isset($filterData[1]) ? trim($filterData[1]) : '';
			$date = isset($filterData[2]) ? trim($filterData[2]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			6 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_gateway_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_gateway_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.request_id LIKE '".$keyword."%' ";
				$sql.=" OR a.gateway_txn_id LIKE '".$keyword."%' ";
				$sql.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql.=" AND  b.id = $member_id ";    
			}

			if($date != '') {   
				$sql.=" AND  DATE(a.created) = '".$date."' ";    
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['request_id'];
				$nestedData[] = $list['gateway_txn_id'];
				$nestedData[] = '&#8377; '.$list['request_amount'];
				$nestedData[] = '&#8377; '.$list['charge_amount'];
				$nestedData[] = '&#8377; '.$list['wallet_settlement_amount'];
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Not Confirm</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				elseif($list['status'] == 4) {
					$nestedData[] = '<font color="red">Refund</font>';
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



	public function walletTransfer()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		// get users list
		$memberList = $this->db->where_in('role_id',array(4,5))->get_where('users',array('account_id'=>$account_id,'created_by'=>$loggedAccountID))->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/walletTransfer',
            'memberList'	=> $memberList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    // save member
	public function walletTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->input->post();

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - Credit/Debit Wallet Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('member_id', 'MemberID', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('transaction_password', 'Transaction Password ', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->walletTransfer();
		}
		else
		{	
			// check member is valid or not
			$chk_member = $this->db->get_where('users',array('user_code'=>$post['member_id'],'account_id'=>$account_id))->num_rows();
			if(!$chk_member)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Wallet Insufficient balance Error.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);
				$this->Az->redirect('distributor/wallet/walletTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Sorry !!</b> MemberID not exists.</div>');
			}

			$chk_txn_password = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();

			if(do_hash($post['transaction_password']) != $chk_txn_password['transaction_password']){

				$this->Az->redirect('distributor/wallet/walletTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Sorry !!</b> Transaction password is wrong.</div>');

			}

			$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

			if($wallet_balance < $post['amount']){

				$this->Az->redirect('distributor/wallet/walletTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b>Sorry !!</b> you have insufficient balance in your wallet.</div>');

			}
			
			
			$status = $this->Wallet_model->walletTransfer($post);
			
			if($status == true)
			{
				$this->Az->redirect('distributor/wallet/walletTransfer', 'system_message_error',lang('WALLET_SAVED'));
			}
			else
			{
				$this->Az->redirect('distributor/wallet/walletTransfer', 'system_message_error',lang('WALLET_ERROR'));
			}
			
		}
	
	}

	public function addFund()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
		$siteUrl = site_url();
		$loggedAccountID = $loggedUser['id'];

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(5, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check qr is active or not
	 	$chk_qr_status = $this->db->get_where('users',array('is_upi_qr_active'=>1,'id'=>$loggedAccountID))->num_rows();
		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/addFund',
            'chk_qr_status' => $chk_qr_status,
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    // topup upi wallet
	public function sendRequest()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(5, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
	 	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/sendRequest',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    function requestAmountCheck($num)
    {
    	$this->load->library('form_validation');
        if ($num < 1)
        {
            $this->form_validation->set_message(
                            'requestAmountCheck',
                            'The %s field must be grater than 0'
                        );
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    // upi wallet topup auth
	public function upiRequestAuth()
	{	
		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(5, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean|callback_requestAmountCheck');
		$this->form_validation->set_rules('vpa_id', 'VPA ID', 'required|xss_clean');
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        if($this->form_validation->run() == FALSE) {
			
			$response = array(

				'status' => 0,
				'is_api_error' => 0,
				'message'=> 'failed',
				'amount_error' => form_error('amount', '<div class="error">', '</div>'),
				'vpa_error'	   => form_error('vpa_id', '<div class="error">', '</div>'),
				'description_error'	   => form_error('description', '<div class="error">', '</div>'),		
			);
		}
		else
		{	

			$responseData = $this->Wallet_model->upiSendRequest($account_id,$loggedAccountID,$post);

			if($responseData['status'] == 1){

				$response = array(
					'status' => 1,
					'txnid' => $responseData['merchantTranId'],
					'message'=> 'Transaction initiated',
					'is_api_error' => 0
				);
			}
			else
			{
				
				$response = array(
					'status' => 0,
					'message'=> $responseData['message'],
					'is_api_error' => 1
				);
			}
			
		}

		echo json_encode($response);
	
	}

	public function getUpiCallbackResponse($txnid = '')
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		// get member id
        $get_member_data = $this->db->get_where('upi_transaction',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'txnid'=>$txnid,'is_api_response'=>1))->row_array();
        if($get_member_data)
        {
        	if($get_member_data['status'] == 2)
        	{
        		$this->session->set_flashdata('system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Your transaction successfully credited.</div>');
        		$response = array(
	        		'status' => 1,
	        		'message' => 'Congratulations ! Your transaction successfully credited.',
	        		'api_status' => 2
	        	);
        	}
        	elseif($get_member_data['status'] == 3)
        	{
        		$this->session->set_flashdata('system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Your transaction failed from VPA side.</div>');
        		$response = array(
	        		'status' => 1,
	        		'message' => 'Sorry ! Your transaction failed from VPA side.',
	        		'api_status' => 3
	        	);
        	}
        }
        else
        {
        	$response = array(
        		'status' => 0,
        		'message' => 'Something went wrong, please try again later.'
        	);
        }
        echo json_encode($response);
	}

	// topup upi wallet
	public function dynamicQr()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(5, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
	 	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/dynamicQr',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    // upi wallet topup auth
	public function upiDynamicQrAuth()
	{	
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(5, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean|callback_requestAmountCheck');
		if($this->form_validation->run() == FALSE) {
			
			$this->dynamicQr();
		}
		else
		{	

			$responseData = $this->Wallet_model->upiGenerateDynamicQr($account_id,$loggedAccountID,$post);
			
			if($responseData['status'] == 1){

				$siteUrl = site_url();
		        $data = array(
		            'site_url' => $siteUrl,
					'loggedUser' => $loggedUser,
		            'meta_title' => lang('SITE_NAME'),
		            'meta_keywords' => lang('SITE_NAME'),
		            'meta_description' => lang('SITE_NAME'),
		            'content_block' => 'wallet/dynamicQrView',
		            'qr' => $responseData['qr'],
		            'accountData' => $accountData,
		            'manager_description' => lang('SITE_NAME'),
		          	'system_message' => $this->Az->getSystemMessageError(),
		            'system_info' => $this->Az->getSystemMessageInfo(),
		            'system_warning' => $this->Az->getSystemMessageWarning() 
				);

		        $this->parser->parse('distributor/layout/column-1', $data);
			}
			else
			{
				$error = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>';
				$this->Az->redirect('distributor/wallet/dynamicQr', 'system_message_error',$error);
			}
			
		}

	}

	// topup upi wallet
	public function activeQr()
    {
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(5, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

	 	// check qr is active or not
	 	$chk_qr_status = $this->db->get_where('users',array('is_upi_qr_active'=>1,'id'=>$loggedAccountID))->num_rows();

	 	if($chk_qr_status)
	 	{
	 		$get_qr_url = $this->db->select('qr_url')->get_where('users',array('is_upi_qr_active'=>1,'id'=>$loggedAccountID))->row_array();
	 		$responseData = array();
	 		$responseData['status'] = 1;
	 		$responseData['qr'] = $get_qr_url['qr_url'];
	 	}
	 	else
	 	{
	 		$responseData = $this->Wallet_model->upiGenerateStaticQr($account_id,$loggedAccountID);
	 	}
			
		if($responseData['status'] == 1){

			$siteUrl = site_url();
	        $data = array(
	            'site_url' => $siteUrl,
				'loggedUser' => $loggedUser,
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'content_block' => 'wallet/dynamicQrView',
	            'qr' => $responseData['qr'],
	            'accountData' => $accountData,
	            'manager_description' => lang('SITE_NAME'),
	          	'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getSystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning() 
			);

	        $this->parser->parse('distributor/layout/column-1', $data);
		}
		else
		{
			$error = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>';
			$this->Az->redirect('distributor/wallet/addFund', 'system_message_error',$error);
		}
    }

    // topup upi wallet
	public function mapQr()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(5, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check qr is active or not
	 	$chk_qr_status = $this->db->get_where('users',array('is_upi_qr_active'=>1,'id'=>$loggedAccountID))->num_rows();
	 	if($chk_qr_status)
		{
			$this->Az->redirect('distributor/wallet/addFund', 'system_message_error',lang('QR_ALREDY_ACTIVE_ERROR'));
		}
	 	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/map-qr',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    
    // upi wallet topup auth
	public function mapQrAuth()
	{	
		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(5, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('txnid', 'Txn ID', 'required|xss_clean');
		if($this->form_validation->run() == FALSE) {
			$this->mapQr();
		}
		else
		{	
			$txnid = $post['txnid'];
			// check txnid valid or not
			$chk_txn_id = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->num_rows();
			if(!$chk_txn_id)
			{
				$this->Az->redirect('distributor/wallet/mapQr', 'system_message_error',lang('MAP_TXN_ERROR'));
			}

			// check txnid valid or not
			$chk_txn_id = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'txnid'=>$txnid,'is_map'=>1))->num_rows();
			if($chk_txn_id)
			{
				$this->Az->redirect('distributor/wallet/mapQr', 'system_message_error',lang('MAP_ALREADY_ERROR'));
			}

			// check txnid valid or not
			$get_txn_data = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->row_array();
			$qr_code = $get_txn_data['qr_image'];
			$ref_id = $get_txn_data['ref_id'];

			$this->db->where('account_id',$account_id);
            $this->db->where('id',$loggedAccountID);
            $this->db->update('users',array('qr_url'=>$qr_code,'is_upi_qr_active'=>1,'upi_qr_ref_id'=>$ref_id));

            $this->db->where('account_id',$account_id);
            $this->db->where('txnid',$txnid);
            $this->db->update('upi_collection_qr',array('is_map'=>1,'map_member_id'=>$loggedAccountID,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));

            $this->Wallet_model->mapQrName($ref_id,$loggedAccountID);
            
			$this->Az->redirect('distributor/wallet/addFund', 'system_message_error',lang('MAP_SUCCESS'));
			
		}

	}

	public function addCashFund()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
		$siteUrl = site_url();
		$loggedAccountID = $loggedUser['id'];

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(7, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check qr is active or not
	 	$chk_qr_status = $this->db->get_where('users',array('is_upi_cash_qr_active'=>1,'id'=>$loggedAccountID))->num_rows();
		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/addCashFund',
            'chk_qr_status' => $chk_qr_status,
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    // topup upi wallet
	public function sendCashRequest()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(7, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
	 	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/sendCashRequest',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    // upi wallet topup auth
	public function upiCashRequestAuth()
	{	
		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(7, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean|callback_requestAmountCheck');
		$this->form_validation->set_rules('vpa_id', 'VPA ID', 'required|xss_clean');
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        if($this->form_validation->run() == FALSE) {
			
			$response = array(

				'status' => 0,
				'is_api_error' => 0,
				'message'=> 'failed',
				'amount_error' => form_error('amount', '<div class="error">', '</div>'),
				'vpa_error'	   => form_error('vpa_id', '<div class="error">', '</div>'),
				'description_error'	   => form_error('description', '<div class="error">', '</div>'),		
			);
		}
		else
		{	

			$responseData = $this->Wallet_model->upiSendCashRequest($account_id,$loggedAccountID,$post);

			if($responseData['status'] == 1){

				$response = array(
					'status' => 1,
					'txnid' => $responseData['merchantTranId'],
					'message'=> 'Transaction initiated',
					'is_api_error' => 0
				);
			}
			else
			{
				
				$response = array(
					'status' => 0,
					'message'=> $responseData['message'],
					'is_api_error' => 1
				);
			}
			
		}

		echo json_encode($response);
	
	}

	public function getUpiCashCallbackResponse($txnid = '')
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		// get member id
        $get_member_data = $this->db->get_where('upi_cash_transaction',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'txnid'=>$txnid,'is_api_response'=>1))->row_array();
        if($get_member_data)
        {
        	if($get_member_data['status'] == 2)
        	{
        		$this->session->set_flashdata('system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Your transaction successfully credited.</div>');
        		$response = array(
	        		'status' => 1,
	        		'message' => 'Congratulations ! Your transaction successfully credited.',
	        		'api_status' => 2
	        	);
        	}
        	elseif($get_member_data['status'] == 3)
        	{
        		$this->session->set_flashdata('system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Your transaction failed from VPA side.</div>');
        		$response = array(
	        		'status' => 1,
	        		'message' => 'Sorry ! Your transaction failed from VPA side.',
	        		'api_status' => 3
	        	);
        	}
        }
        else
        {
        	$response = array(
        		'status' => 0,
        		'message' => 'Something went wrong, please try again later.'
        	);
        }
        echo json_encode($response);
	}

	// topup upi wallet
	public function dynamicCashQr()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(7, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
	 	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/dynamicCashQr',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    // upi wallet topup auth
	public function upiCashDynamicQrAuth()
	{	
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(7, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean|callback_requestAmountCheck');
		if($this->form_validation->run() == FALSE) {
			
			$this->dynamicCashQr();
		}
		else
		{	

			$responseData = $this->Wallet_model->upiCashGenerateDynamicQr($account_id,$loggedAccountID,$post);
			
			if($responseData['status'] == 1){

				$siteUrl = site_url();
		        $data = array(
		            'site_url' => $siteUrl,
					'loggedUser' => $loggedUser,
		            'meta_title' => lang('SITE_NAME'),
		            'meta_keywords' => lang('SITE_NAME'),
		            'meta_description' => lang('SITE_NAME'),
		            'content_block' => 'wallet/dynamicQrView',
		            'qr' => $responseData['qr'],
		            'accountData' => $accountData,
		            'manager_description' => lang('SITE_NAME'),
		          	'system_message' => $this->Az->getSystemMessageError(),
		            'system_info' => $this->Az->getSystemMessageInfo(),
		            'system_warning' => $this->Az->getSystemMessageWarning() 
				);

		        $this->parser->parse('distributor/layout/column-1', $data);
			}
			else
			{
				$error = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>';
				$this->Az->redirect('distributor/wallet/dynamicCashQr', 'system_message_error',$error);
			}
			
		}

	}

	// topup upi wallet
	public function activeCashQr()
    {
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(7, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

	 	// check qr is active or not
	 	$chk_qr_status = $this->db->get_where('users',array('is_upi_cash_qr_active'=>1,'id'=>$loggedAccountID))->num_rows();

	 	if($chk_qr_status)
	 	{
	 		$get_qr_url = $this->db->select('cash_qr_url')->get_where('users',array('is_upi_cash_qr_active'=>1,'id'=>$loggedAccountID))->row_array();
	 		$responseData = array();
	 		$responseData['status'] = 1;
	 		$responseData['qr'] = $get_qr_url['cash_qr_url'];
	 	}
	 	else
	 	{
	 		$responseData = $this->Wallet_model->upiCashGenerateStaticQr($account_id,$loggedAccountID);
	 	}
			
		if($responseData['status'] == 1){

			$siteUrl = site_url();
	        $data = array(
	            'site_url' => $siteUrl,
				'loggedUser' => $loggedUser,
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'content_block' => 'wallet/dynamicQrView',
	            'qr' => $responseData['qr'],
	            'accountData' => $accountData,
	            'manager_description' => lang('SITE_NAME'),
	          	'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getSystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning() 
			);

	        $this->parser->parse('distributor/layout/column-1', $data);
		}
		else
		{
			$error = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>';
			$this->Az->redirect('distributor/wallet/addCashFund', 'system_message_error',$error);
		}
    }

    // topup upi wallet
	public function mapCashQr()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(7, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check qr is active or not
	 	$chk_qr_status = $this->db->get_where('users',array('is_upi_cash_qr_active'=>1,'id'=>$loggedAccountID))->num_rows();
	 	if($chk_qr_status)
		{
			$this->Az->redirect('distributor/wallet/addCashFund', 'system_message_error',lang('QR_ALREDY_ACTIVE_ERROR'));
		}
	 	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/map-cash-qr',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    
    // upi wallet topup auth
	public function mapCashQrAuth()
	{	
		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(7, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('txnid', 'Txn ID', 'required|xss_clean');
		if($this->form_validation->run() == FALSE) {
			$this->mapCashQr();
		}
		else
		{	
			$txnid = $post['txnid'];
			// check txnid valid or not
			$chk_txn_id = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->num_rows();
			if(!$chk_txn_id)
			{
				$this->Az->redirect('distributor/wallet/mapCashQr', 'system_message_error',lang('MAP_TXN_ERROR'));
			}

			// check txnid valid or not
			$chk_txn_id = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'txnid'=>$txnid,'is_map'=>1))->num_rows();
			if($chk_txn_id)
			{
				$this->Az->redirect('distributor/wallet/mapCashQr', 'system_message_error',lang('MAP_ALREADY_ERROR'));
			}

			// check txnid valid or not
			$get_txn_data = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->row_array();
			$qr_code = $get_txn_data['qr_image'];
			$ref_id = $get_txn_data['ref_id'];

			$this->db->where('account_id',$account_id);
            $this->db->where('id',$loggedAccountID);
            $this->db->update('users',array('cash_qr_url'=>$qr_code,'is_upi_cash_qr_active'=>1,'upi_cash_qr_ref_id'=>$ref_id));

            $this->db->where('account_id',$account_id);
            $this->db->where('txnid',$txnid);
            $this->db->update('upi_cash_qr',array('is_map'=>1,'map_member_id'=>$loggedAccountID,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));

            $this->Wallet_model->mapCashQrName($ref_id,$loggedAccountID);
            
			$this->Az->redirect('distributor/wallet/addCashFund', 'system_message_error',lang('MAP_SUCCESS'));
			
		}

	}

	public function payolTransfer()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(28, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/payolTransfer',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    public function getMemberName($mobile = '')
    {
    	$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$mobile = trim($mobile);
	 	$getMemberName = $this->db->query("SELECT name FROM tbl_users WHERE (mobile = '$mobile' OR username = '$mobile') AND role_id IN (3,4,5,8) AND account_id = '$account_id'")->num_rows();
	 	if($getMemberName)
	 	{
	 		$getMemberName = $this->db->query("SELECT name,is_active FROM tbl_users WHERE (mobile = '$mobile' OR username = '$mobile') AND role_id IN (3,4,5,8) AND account_id = '$account_id'")->row_array();
	 		$name = isset($getMemberName['name']) ? $getMemberName['name'] : '';
	 		$is_active = isset($getMemberName['is_active']) ? $getMemberName['is_active'] : 0;
	 		if($is_active)
	 		{
		 		$response = array(
		 			'status' => 1,
		 			'msg' => '<font color="green">Name : '.$name.'</font>'
		 		);
	 		}
	 		else
	 		{
	 			$response = array(
		 			'status' => 0,
		 			'msg' => '<font color="red">Member found but not activated</font>'
		 		);
	 		}
	 	}
	 	else
	 	{
	 		$response = array(
	 			'status' => 0,
	 			'msg' => '<font color="red">Member not found</font>'
	 		);
	 	}

	 	echo json_encode($response);
    }

    public function payolWalletAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(28, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		//check for foem validation
		$post = $this->input->post();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('mobile', 'Mobile/UserID', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->payolTransfer();
		}
		else
		{	
			if($post['amount'] < 1)
			{
				$this->Az->redirect('distributor/wallet/payolTransfer', 'system_message_error',lang('AMOUNT_VALID_ERROR'));
			}
			$mobile = trim($post['mobile']);
	 		$chk_member = $this->db->query("SELECT id FROM tbl_users WHERE (mobile = '$mobile' OR username = '$mobile') AND role_id IN (3,4,5,8) AND account_id = '$account_id'")->num_rows();
			if(!$chk_member)
			{
				$this->Az->redirect('distributor/wallet/payolTransfer', 'system_message_error',lang('MEMBER_VALID_ERROR'));
			}

			// check account wallet balance
			$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
			if($post['amount'] > $wallet_balance)
			{
				$this->Az->redirect('distributor/wallet/payolTransfer', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
			}

			$getMemberID = $this->db->query("SELECT id FROM tbl_users WHERE (mobile = '$mobile' OR username = '$mobile') AND role_id IN (3,4,5,8) AND account_id = '$account_id'")->row_array();
			$to_member_id = isset($getMemberID['id']) ? $getMemberID['id'] : 0 ;
			if($loggedAccountID == $to_member_id)
			{
				$this->Az->redirect('distributor/wallet/payolTransfer', 'system_message_error',lang('SELF_TRANSFER_ERROR'));
			}

			$txnid = rand(11,99).time().rand(11,99);
			$encode_txnid = do_hash($txnid);
			$data = array(
				'account_id' => $account_id,
				'txnid' => $txnid,
				'encode_txnid' => $encode_txnid,
				'from_member_id' => $loggedAccountID,
				'to_member_id' => $to_member_id,
				'amount' => $post['amount'],
				'description' => $post['description'],
				'post_data' => json_encode($post),
				'is_verify' => 0,
				'status' => 1,
				'created' => date('Y-m-d H:i:s'),
				'created_by' => $loggedAccountID
			);
			$this->db->insert('payol_wallet_transfer',$data);
			
			$this->Az->redirect('distributor/wallet/payolTransferVerify/'.$encode_txnid, 'system_message_error','');
			
			
		}
	
	}

	public function payolTransferVerify($encode_txnid = '')
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(28, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

	 	$chk_member = $this->db->get_where('payol_wallet_transfer',array('from_member_id'=>$loggedAccountID,'encode_txnid'=>$encode_txnid,'status'=>1))->num_rows();
		if(!$chk_member)
		{
			$this->Az->redirect('distributor/wallet/payolTransfer', 'system_message_error',lang('WALLET_ERROR'));
		}

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/payolTransferVerify',
            'encode_txnid' => $encode_txnid,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    public function payolWalletPinAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(28, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		$encode_txnid = $post['encode_txnid'];
		$chk_member = $this->db->get_where('payol_wallet_transfer',array('from_member_id'=>$loggedAccountID,'encode_txnid'=>$encode_txnid,'status'=>1))->num_rows();
		if(!$chk_member)
		{
			$this->Az->redirect('distributor/wallet/payolTransfer', 'system_message_error',lang('WALLET_ERROR'));
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('txnpin', 'Transaction Pin', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			
			$this->payolTransferVerify($encode_txnid);
		}
		else
		{	

			$txnpin = do_hash(trim($post['txnpin']));
	 		$chk_member = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$loggedAccountID,'transaction_password'=>$txnpin))->num_rows();
			if(!$chk_member)
			{
				$this->Az->redirect('distributor/wallet/payolTransferVerify/'.$encode_txnid, 'system_message_error',lang('TXN_PIN_ERROR'));
			}

			$txnData = $this->db->get_where('payol_wallet_transfer',array('from_member_id'=>$loggedAccountID,'encode_txnid'=>$encode_txnid,'status'=>1))->row_array();
			$amount = $txnData['amount'];
			$description = $txnData['description'];
			$to_member_id = $txnData['to_member_id'];
			$txnid = $txnData['txnid'];

			// check account wallet balance
			$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
			if($amount > $wallet_balance)
			{
				$this->Az->redirect('distributor/wallet/payolTransfer', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
			}

			$after_balance = $wallet_balance - $amount;
			$wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $loggedAccountID,    
	            'before_balance'      => $wallet_balance,
	            'amount'              => $amount,  
	            'after_balance'       => $after_balance,      
	            'status'              => 1,
	            'type'                => 2,      
	            'created'             => date('Y-m-d H:i:s'),      
	            'credited_by'         => $loggedAccountID,
	            'description'         => 'Payol Transfer Txn #'.$txnid.' '.$description
            );

            $this->db->insert('member_wallet',$wallet_data);

            $wallet_balance = $this->User->getMemberWalletBalanceSP($to_member_id);

            $after_balance = $wallet_balance + $amount;
			$wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $to_member_id,    
	            'before_balance'      => $wallet_balance,
	            'amount'              => $amount,  
	            'after_balance'       => $after_balance,      
	            'status'              => 1,
	            'type'                => 1,      
	            'created'             => date('Y-m-d H:i:s'),      
	            'credited_by'         => $loggedAccountID,
	            'description'         => 'Payol Transfer Txn #'.$txnid.' '.$description
            );

            $this->db->insert('member_wallet',$wallet_data);

            $this->db->where('encode_txnid',$encode_txnid);
            $this->db->where('from_member_id',$loggedAccountID);
            $this->db->where('status',1);
            $this->db->update('payol_wallet_transfer',array('is_verify'=>1,'status'=>2,'updated'=>date('Y-m-d H:i:s')));
			
			$this->Az->redirect('distributor/wallet/payolTransfer', 'system_message_error',lang('PAYOL_TRANSFER_SUCCESS'));
			
			
		}
	
	}


	public function upiAddFundAuth()
	{	
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(31, $activeService)){
			$this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean|callback_requestAmountCheck');
		if($this->form_validation->run() == FALSE) {
			
			$this->addFund();
		}
		else
		{	

			$responseData = $this->Wallet_model->addFund($account_id,$loggedAccountID,$post);
			
			if($responseData['status'] == 1){

				$siteUrl = site_url();
		        $data = array(
		            'site_url' => $siteUrl,
					'loggedUser' => $loggedUser,
		            'meta_title' => lang('SITE_NAME'),
		            'meta_keywords' => lang('SITE_NAME'),
		            'meta_description' => lang('SITE_NAME'),
		            'content_block' => 'wallet/dynamicQrView',
		            'qr' => $responseData['qr'],
		            'accountData' => $accountData,
		            'manager_description' => lang('SITE_NAME'),
		          	'system_message' => $this->Az->getSystemMessageError(),
		            'system_info' => $this->Az->getSystemMessageInfo(),
		            'system_warning' => $this->Az->getSystemMessageWarning() 
				);

		        $this->parser->parse('distributor/layout/column-1', $data);
			}
			else
			{
				$error = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>';
				$this->Az->redirect('distributor/wallet/addFund', 'system_message_error',$error);
			}
			
		}

	}
	
	
}