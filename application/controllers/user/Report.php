<?php 
class Report extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkUserPermission();
       	$this->lang->load('user/recharge', 'english');
       	$this->load->model('user/Complain_model');      
        
    }

	public function recharge(){
		$loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        
		$siteUrl = base_url();
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'recharge' => $recharge,
            'loggedUser'=>$loggedUser, 
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/recharge-history'
        );
        $this->parser->parse('user/layout/column-1' , $data);
    }

    public function getRechargeList()
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
			0 => 'id',	
			1 => 'recharge_display_id',
			2 => 'user_code',
			3 => 'name',
			5 => 'created',
			9 => 'recharge_type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')) as x";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')) as x WHERE x.id > 0";
			
			if($keyword != '') {   
				$sql.=" AND ( user_code LIKE '".$keyword."%' ";    
				$sql.=" OR mobile LIKE '".$keyword."%'";
				$sql.=" OR circle_code LIKE '".$keyword."%'";
				$sql.=" OR recharge_type LIKE '".$keyword."%'";
				$sql.=" OR recharge_display_id LIKE '".$keyword."%'";
				$sql.=" OR name LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( Date(created) = '".$date."' )";    
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
				
				$list['operator_name'] = $this->User->get_api_operator_name($list['api_id'],$list['operator_code'],$account_id);
				
				if($list['is_bbps_api'] == 1)
				{
					$list['operator_name'] = $list['operator_code'];
				}
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['recharge_display_id']."</a>";
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['operator_name'];
				$nestedData[] = $list['amount'].' /-';
				if($list['before_balance'])
				{
					$nestedData[] = $list['before_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				if($list['after_balance'])
				{
					$nestedData[] = $list['after_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
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

				$nestedData[] = '<i class="fa fa-comments" onclick="showComplainBox('.$list['id'].')" aria-hidden="true"></i>';

				
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

	public function bbps(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
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
            'content_block' => 'report/bbps-list'
        );
        $this->parser->parse('user/layout/column-1' , $data);
    
	
	}

	public function getBBPSList()
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
			0 => 'a.id',	
			1 => 'a.recharge_display_id',
			2 => 'b.user_code',
			3 => 'b.name',
			5 => 'a.created',
			9 => 'a.recharge_type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.recharge_type = 7 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.recharge_type = 7 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.mobile LIKE '".$keyword."%'";
				$sql.=" OR a.operator_code LIKE '".$keyword."%'";
				$sql.=" OR a.circle_code LIKE '".$keyword."%'";
				$sql.=" OR a.recharge_type LIKE '".$keyword."%'";
				$sql.=" OR a.recharge_display_id LIKE '".$keyword."%'";
				$sql.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( Date(a.created) = '".$date."' )";    
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
				
				
				$operator = $this->db->get_where('operator',array('operator_code'=>$list['operator_code']))->row_array();

				

				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['recharge_display_id']."</a>";
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $operator['operator_name'];
				$nestedData[] = $list['account_number'];
				$nestedData[] = $list['customer_name'];
				$nestedData[] = $list['amount'].' /-';
				if($list['before_balance'])
				{
					$nestedData[] = $list['before_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				if($list['after_balance'])
				{
					$nestedData[] = $list['after_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				$nestedData[] = $list['txid'];
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
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

	

	public function moneyTransfer(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
		
		
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
            'content_block' => 'report/money-transfer-list'
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
				$nestedData[] = $list['account_no'];
				$nestedData[] = $list['ifsc'];
				$nestedData[] = $list['transfer_amount'].' /-';
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






	public function moneyTransferHistory(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
		
		
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
            'content_block' => 'report/money-transfer-history'
        );
        $this->parser->parse('user/layout/column-1' , $data);
    
	
	}


	public function getMoneyTransferList()
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
			$sql = "SELECT a.* FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID')";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,c.name as sender_name,c.mobile as sender_mobile FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID')";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '".$keyword."%'";
				$sql.=" OR a.account_no LIKE '".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '".$keyword."%'";
				$sql.=" OR c.name LIKE '".$keyword."%'";
				$sql.=" OR c.mobile LIKE '".$keyword."%'";
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
				$nestedData[] = $list['sender_name'].'<br />'.$list['sender_mobile'];
				$nestedData[] = $list['account_holder_name'].'<br />'.$list['mobile'];
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






	public function rechargeCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
		
		
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
            'content_block' => 'report/recharge-commission-list'
        );
        $this->parser->parse('user/layout/column-1' , $data);
    
	
	}


	public function getRechargeCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
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
			5 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'RECHARGE' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.recharge_display_id FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'RECHARGE' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR c.recharge_display_id LIKE '".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
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
				$nestedData[] = $list['user_code'];
				$nestedData[] = $list['member_name'];
				$nestedData[] = $list['recharge_display_id'];
				$nestedData[] = '&#8377; '.$list['commision_amount'];
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

	public function bbpsHistory($status = 0){
		$loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        
		$siteUrl = base_url();
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'recharge' => $recharge,
            'loggedUser'=>$loggedUser,
            'status' => $status, 
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/bbpsHistory'
        );
        $this->parser->parse('user/layout/column-1' , $data);
    }

    public function getBBPSHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
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
			5 => 'a.created',
		);
		
		
			$sql = "SELECT a.* FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";

			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
			$sql = "SELECT a.*,b.name,b.user_code,c.title as service_name FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR b.mobile LIKE '".$keyword."%'";
				$sql.=" OR a.operator_code LIKE '".$keyword."%'";
				$sql.=" OR a.recharge_display_id LIKE '".$keyword."%'";
				$sql.=" OR c.title LIKE '".$keyword."%' )";
			}

			
			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

			if($status)
            {
                $sql.=" AND a.status = '$status'";
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['recharge_display_id']."</a>";
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['service_name'];
				$nestedData[] = $list['operator_code'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['amount'].' /-';
				if($list['before_balance'])
				{
					$nestedData[] = $list['before_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				if($list['after_balance'])
				{
					$nestedData[] = $list['after_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
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

				$nestedData[] = "<a href=".base_url('user/report/bbpsLiveInvoice/').$list['recharge_display_id']." style='text-decoration:none;' target='_blank'>Invoice</a>";	

				$nestedData[] = '<i class="fa fa-comments" onclick="showBBPSComplainBox('.$list['id'].')" aria-hidden="true"></i>';

				
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

	// save member
    public function complainAuth()
    {
    	
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        //check for foem validation
        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('recordID', 'Member Type', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Name', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            
            $this->Az->redirect('user/report/bbpsHistory', 'system_message_error',lang('FORM_ERROR'));
        }
        else
        {   
            $recharge_id = $post['recordID'];
            // check recharge is valid or not
            $chk_recharge = $this->db->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->num_rows();
            if(!$chk_recharge)
            {

                $this->Az->redirect('user/report/bbpsHistory', 'system_message_error',lang('AUTHORIZE_ERROR'));  

            }

            $status = $this->Complain_model->saveBBPSComplain($post);
            
            if($status == true)
            {
                $this->Az->redirect('user/report/bbpsHistory', 'system_message_error',lang('COMPLAIN_SAVED'));
            }
            else
            {
                $this->Az->redirect('user/report/bbpsHistory', 'system_message_error',lang('COMMON_ERROR'));
            }
            
        }
    
    }


     
     public function transferInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.name as member_name,c.name as sender_name FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.account_id = '$account_id' AND a.id = '$id'";
		$detail = $this->db->query($sql)->row_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/transfer-invoice'
        );
        $this->parser->parse('user/layout/column-2' , $data);
    
	
	}	


	public function moneyTransferInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.name as user_name FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.id = '$id'";
		$detail = $this->db->query($sql)->row_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/moneytransfer-invoice'
        );
        $this->parser->parse('user/layout/column-2' , $data);
    
	
	}	

	public function upiTxnReport(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
		
		
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
            'content_block' => 'report/upi-transaction-report'
        );
        $this->parser->parse('user/layout/column-1' , $data);
    
	
	}


	public function getUpiTxnList()
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
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR b.name LIKE '".$keyword."%'";
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR a.bank_rrno LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.description LIKE '%".$keyword."%'";
				$sql.=" OR a.vpa_id LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['user_code'].'<br />'.$list['name'];
				$nestedData[] = $list['amount'].' /-';
				if($list['type_id'] == 1)
				{
					$nestedData[] = 'UPI Request';
				}
				elseif($list['type_id'] == 2)
				{
					$nestedData[] = 'Static QR';
				}
				elseif($list['type_id'] == 3)
				{
					$nestedData[] = 'Dynamic QR';
				}
				else
				{
					$nestedData[] = '';
				}
				
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['bank_rrno'];
				$nestedData[] = $list['vpa_id'];
				$nestedData[] = $list['description'];

				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
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

	
	public function cashDepositeReport(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
		
		
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
            'content_block' => 'report/cash-deposite-report'
        );
        $this->parser->parse('user/layout/column-1' , $data);
    
	
	}


	public function getCashDepositeList()
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
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_cash_deposite_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.status > 1";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_cash_deposite_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.status > 1";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR b.name LIKE '".$keyword."%'";
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.remark LIKE '%".$keyword."%'";
				$sql.=" OR a.bank_rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.txnid LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['user_code'].'<br />'.$list['name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['account_no'];
				$nestedData[] = $list['amount'].' /-';
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['bank_rrn'];
				$nestedData[] = $list['remark'];

				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4) {
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

	public function upiCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
		
		
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
            'content_block' => 'report/upi-commission-list'
        );
        $this->parser->parse('user/layout/column-1' , $data);
    
	
	}


	public function getUpiCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
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
			5 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPI' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPI' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR c.txnid LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
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
				$nestedData[] = $list['user_code'];
				$nestedData[] = $list['member_name'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = '&#8377; '.$list['commision_amount'];
				if($list['is_surcharge'] == 1)
				{
					$nestedData[] = '<font color="red">DR</font>';
				}
				else
				{
					$nestedData[] = '<font color="green">CR</font>';
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
    

    public function bbpsLiveInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
		$loggedAccountID = $loggedUser['id'];

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.name,b.user_code,c.title as service_name FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_mobikwik_bbps_service as c ON c.id = a.service_id WHERE a.recharge_display_id = '$id' AND a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";

		$detail = $this->db->query($sql)->row_array();

		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'operator' => $operator,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/bbps-live-invoice'
        );
        $this->parser->parse('user/layout/column-2' , $data);
    
	
	}


}