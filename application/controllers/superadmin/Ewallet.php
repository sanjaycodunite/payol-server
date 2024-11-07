<?php 
class Ewallet extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkPermission();
        $this->load->model('superadmin/Ewallet_model');		
        $this->lang->load('superadmin/ewallet', 'english');
        
    }

	public function walletList(){

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		// get users list
		$memberList = $this->db->where_in('role_id',array(2))->get('users')->result_array();

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
            'content_block' => 'ewallet/walletList'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getWalletList()
	{	
		
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where b.role_id = 2 AND  a.wallet_type = 2";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where b.role_id = 2 AND  a.wallet_type = 2";
			
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



	public function creditList(){

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		// get users list
		$memberList = $this->db->where_in('role_id',array(2))->get_where('users',array('account_id'=>$account_id))->result_array();
		
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
            'content_block' => 'ewallet/creditList'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getCreditList()
	{	
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.type = 1 AND b.role_id = 2 AND  a.wallet_type = 2";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.type = 1 AND b.role_id = 2 AND  a.wallet_type = 2";
			
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


	public function debitList(){

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		// get users list
		$memberList = $this->db->where_in('role_id',array(2))->get('users')->result_array();
		
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
            'content_block' => 'ewallet/debitList'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getDebitList()
	{	
		
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.type = 2 AND b.role_id = 2 AND  a.wallet_type = 2";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.type = 2 AND b.role_id = 2 AND  a.wallet_type = 2";
			
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
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		// get users list
		$memberList = $this->db->where_in('role_id',array(2))->get('users')->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'ewallet/addWallet',
            'memberList'	=> $memberList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('superadmin/layout/column-1', $data);
		
    }

    // save member
	public function saveWallet()
	{
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		//check for foem validation
		$post = $this->input->post();
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
			$chk_member = $this->db->get_where('users',array('id'=>$post['member'],'role_id'=>2))->num_rows();
			if(!$chk_member)
			{
				$this->Az->redirect('superadmin/ewallet/walletList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
			$status = $this->Ewallet_model->saveWallet($post);
			
			if($status == true)
			{
				$this->Az->redirect('superadmin/ewallet/walletList', 'system_message_error',lang('WALLET_SAVED'));
			}
			else
			{
				$this->Az->redirect('superadmin/ewallet/walletList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
		}
	
	}
	
	public function getMemberWalletBalance($memberID = 0)
	{
		// get member available balance
		$wallet_balance = $this->User->getMemberWalletBalanceSP($memberID,2);
		echo json_encode(array(
			'status' => 1,
			'balance' => $wallet_balance
		));
	}
	
	public function requestList()
    {    

    	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$siteUrl = site_url();

		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'ewallet/requestList',
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('superadmin/layout/column-1', $data);
		
    }
	
	public function getRequestList()
	{	
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_member_fund_request as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE b.role_id = 2 AND a.request_wallet_type = 2";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name, b.user_code as member_code FROM tbl_member_fund_request as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE b.role_id = 2 AND a.request_wallet_type = 2";	

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
					$nestedData[] ='<a title="Approve" class="btn btn-success btn-sm" href="'.base_url('superadmin/ewallet/updateRequestAuth').'/'.$list['id'].'/1" onclick="return confirm(\'Are you sure you want to approve this request?\')"><i class="fa fa-check" aria-hidden="true"></i></a> <a title="Reject" class="btn btn-danger btn-sm" href="'.base_url('superadmin/ewallet/updateRequestAuth').'/'.$list['id'].'/2" onclick="return confirm(\'Are you sure you want to reject this request?\')"><i class="fa fa-times" aria-hidden="true"></i></a>';
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
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		// check request id valid or not
		$chk_request_id = $this->db->get_where('member_fund_request',array('id'=>$requestID,'status'=>1))->num_rows();
		if(!$chk_request_id)
		{
			$this->Az->redirect('superadmin/ewallet/requestList', 'system_message_error',lang('WALLET_ERROR'));
		}
		
		$this->Ewallet_model->updateRequestAuth($requestID,$status);
		if($status == 1){
			$this->Az->redirect('superadmin/ewallet/requestList', 'system_message_error',lang('REQUEST_APPROVE_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('superadmin/ewallet/requestList', 'system_message_error',lang('REQUEST_REJECT_SUCCESS'));
		}
	}


	
	
}