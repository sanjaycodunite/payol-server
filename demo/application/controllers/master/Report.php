<?php 
class Report extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
       	$this->lang->load('master/recharge', 'english');
       	$this->load->model('master/Complain_model');      
       	$this->load->model('admin/Jwt_model');
        
    }

	public function recharge($status = 0){
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        
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
            'content_block' => 'report/recharge-history'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    }

   public function getRechargeList()
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
		
		$firstLoad = 0;

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

			if($firstLoad == 1)
			{
				$sql.=" AND DATE(created) = '".date('Y-m-d')."'";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			if($status)
            {
                $sql.=" AND status = '$status'";
            }

			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		

			$get_filter_data = $this->db->query($sql)->result_array();


			$sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id' AND  (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')) as x WHERE x.id > 0";


			if($keyword != '') {   
				$sql_summery.=" AND ( user_code LIKE '".$keyword."%' ";    
				$sql_summery.=" OR mobile LIKE '".$keyword."%'";
				$sql_summery.=" OR circle_code LIKE '".$keyword."%'";
				$sql_summery.=" OR operator_name LIKE '".$keyword."%'";
				$sql_summery.=" OR recharge_type LIKE '".$keyword."%'";
				$sql_summery.=" OR recharge_display_id LIKE '".$keyword."%'";
				$sql_summery.=" OR name LIKE '".$keyword."%' )";
			}
			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            if($firstLoad == 1)
			{
				$sql.=" AND DATE(created) = '".date('Y-m-d')."'";
			}
			
				if($status)
            {
                $sql.=" AND x.status = '$status'";
            }

				
            
			
			 
			$sql_success_summery = $sql_summery;	
			$sql_success_summery.=" AND x.status = 2";

			
			$get_success_recharge = $this->db->query($sql_success_summery)->row_array();
			
			$successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;
	    	
			
			$sql_pending_summery = $sql_summery;	
			$sql_pending_summery.=" AND x.status = 1";	
			$get_pending_recharge = $this->db->query($sql_pending_summery)->row_array();
			
			$pendingAmount = isset($get_pending_recharge['totalAmount']) ? number_format($get_pending_recharge['totalAmount'],2) : '0.00';
	        $pendingRecord = isset($get_pending_recharge['totalRecord']) ? $get_pending_recharge['totalRecord'] : 0;
	    	

	        $sql_failed_summery = $sql_summery;
			$sql_failed_summery.=" AND x.status = 3";	
			$get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();
			
			
	        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
	        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;


		
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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,
					"pendingAmount" => $pendingAmount,
					"pendingRecord" => $pendingRecord,
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}

	public function bbps(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function getBBPSList()
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
				
				$recharge_type = $this->db->get_where('recharge_type',array('id'=>$list['recharge_type']))->row_array();

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
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
				$nestedData[] = $list['transfer_amount'].' /-';
				$nestedData[] = $list['transfer_charge_amount'].' /-';
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
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getMoneyTransferList()
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


			$sql_summery = "SELECT SUM(COALESCE(CASE WHEN a.status = 3 THEN a.total_wallet_charge END,0)) totalSuccessAmount,count( case when a.status=3 then 1 else NULL end) totalSuccessRecord,SUM(COALESCE(CASE WHEN a.status = 4 THEN a.total_wallet_charge END,0)) totalFailedAmount,count( case when a.status=4 then 1 else NULL end) totalFailedRecord,SUM(COALESCE(CASE WHEN a.status = 2 THEN a.total_wallet_charge END,0)) totalPendingAmount,count( case when a.status=2 then 1 else NULL end) totalPendingRecord FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id WHERE a.account_id = '$account_id' AND a.user_id ='$loggedAccountID'";
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
				$nestedData[] = $list['account_holder_name'].'<br />'.$list['mobile'];
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
				if($list['invoice_no']){
					
				 $nestedData[] = '<a href="'.base_url('distributor/report/transferInvoice/'.$list['id'].'').'" target="_blank">'.$list['invoice_no'].'</a>';

				}
				else{

					$nestedData[] = 'Not Available';
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




	public function rechargeCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getRechargeCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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


	public function fundTransferCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
            'content_block' => 'report/fund-transfer-commission-list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getFundTransferCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_fund_transfer as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'PAYOUT' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.transaction_id,c.transfer_amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_fund_transfer as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'PAYOUT' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR c.transaction_id LIKE '".$keyword."%' )";
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
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = '&#8377; '.$list['transfer_amount'];
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

	public function bbpsHistory($status = 0){
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        
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
        $this->parser->parse('master/layout/column-1' , $data);
    }

    public function getBBPSHistoryList()
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
			5 => 'a.created',
		);
		
		
			$sql = "SELECT a.* FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_mobikwik_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";

			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
			$sql = "SELECT a.*,b.name,b.user_code,c.title as service_name FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_mobikwik_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";
			
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


			$sql_success_summery = "SELECT a.*,SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND a.status = 2 AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";

			if($keyword != '') {   
				$sql_success_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_success_summery.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.operator_code LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.account_number LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.txid LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.recharge_display_id LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR c.title LIKE '%".$keyword."%' )";
			}
			if($fromDate && $toDate)
            {
                $sql_success_summery.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
				
			$get_success_recharge = $this->db->query($sql_success_summery)->row_array();
			
			$successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;


	        $sql_pending_summery = "SELECT a.*,SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND a.status = 1 AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID') ";

			if($keyword != '') {   
				$sql_pending_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_pending_summery.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.operator_code LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.account_number LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.txid LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.recharge_display_id LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR c.title LIKE '%".$keyword."%' )";
			}
			if($fromDate && $toDate)
            {
                $sql_pending_summery.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
        	
	    	
			$get_pending_recharge = $this->db->query($sql_pending_summery)->row_array();
			
			$pendingAmount = isset($get_pending_recharge['totalAmount']) ? number_format($get_pending_recharge['totalAmount'],2) : '0.00';
	        $pendingRecord = isset($get_pending_recharge['totalRecord']) ? $get_pending_recharge['totalRecord'] : 0;
	    	

	        $sql_failed_summery = "SELECT a.*,SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND a.status = 3 AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";

			if($keyword != '') {   
				$sql_failed_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_failed_summery.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.operator_code LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.account_number LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.txid LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.recharge_display_id LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR c.title LIKE '%".$keyword."%' )";
			}
			if($fromDate && $toDate)
            {
                $sql_failed_summery.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
            
			
	        $get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();
			
			
	        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
	        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;


		
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


				$nestedData[] = "<a href=".base_url('master/report/bbpsLiveInvoice/').$list['recharge_display_id']." style='text-decoration:none;' target='_blank'>Invoice</a>";


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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,
					"pendingAmount" => $pendingAmount,
					"pendingRecord" => $pendingRecord,
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}

	// save member
    public function complainAuth()
    {
    	
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        //check for foem validation
        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('recordID', 'Member Type', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Name', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            
            $this->Az->redirect('master/report/bbpsHistory', 'system_message_error',lang('FORM_ERROR'));
        }
        else
        {   
            $recharge_id = $post['recordID'];
            // check recharge is valid or not
            $chk_recharge = $this->db->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->num_rows();
            if(!$chk_recharge)
            {

                $this->Az->redirect('master/report/bbpsHistory', 'system_message_error',lang('AUTHORIZE_ERROR'));  

            }

            $status = $this->Complain_model->saveBBPSComplain($post);
            
            if($status == true)
            {
                $this->Az->redirect('master/report/bbpsHistory', 'system_message_error',lang('COMPLAIN_SAVED'));
            }
            else
            {
                $this->Az->redirect('master/report/bbpsHistory', 'system_message_error',lang('COMMON_ERROR'));
            }
            
        }
    
    }

    //genrate Invoice


    public function transferInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();

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
			'contactDetail' => $contactDetail,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/transfer-invoice'
        );
        $this->parser->parse('master/layout/column-2' , $data);
    
	
	}	



	 public function moneyTransferInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
        $this->parser->parse('master/layout/column-2' , $data);
    
	
	}	

	//aeps invoice


	 public function aepsInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


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
            'content_block' => 'report/aeps-invoice'
        );
        $this->parser->parse('master/layout/column-2' , $data);
    
	
	}	



	public function iciciAepsInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


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
            'content_block' => 'report/icici-aeps-invoice'
        );
        $this->parser->parse('master/layout/column-2' , $data);
    
	
	}	



	public function upiTxnReport(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getUpiTxnList()
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
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getCashDepositeList()
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

	public function moneyTransferCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
            'content_block' => 'report/money-transfer-commission-list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getMoneyTransferCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_money_transfer as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'DMT' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.transaction_id,c.transfer_amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_money_transfer as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'DMT' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR c.transaction_id LIKE '".$keyword."%' )";
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
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = '&#8377; '.$list['transfer_amount'];
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

	public function aepsCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
            'content_block' => 'report/aeps-commission-list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getAepsCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_member_aeps_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'AEPS' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnID,c.amount,c.service FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_member_aeps_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'AEPS' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR c.service LIKE '%".$keyword."%'";
				$sql.=" OR c.txnID LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['txnID'];
				if($list['service'] == 'balinfo')
				{
					$nestedData[] = 'Balance Inquiry';
				}
				elseif($list['service'] == 'ministatement')
				{
					$nestedData[] = 'Mini Statement';
				}
				elseif($list['service'] == 'balwithdraw')
				{
					$nestedData[] = 'Withdrawal';
				}
				elseif($list['service'] == 'aadharpay')
				{
					$nestedData[] = 'Aadhar Pay';
				}
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

	public function cashDepositeCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
            'content_block' => 'report/cash-deposite-commission-list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getCashDepositeCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_cash_deposite_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'CD' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_cash_deposite_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'CD' AND a.member_id = '$loggedAccountID'";
			
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

	public function upiCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getUpiCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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

	public function upiCashTxnReport(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
            'content_block' => 'report/upi-cash-report'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getUpiCashTxnList()
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
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_cash_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_cash_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
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

	public function upiCashCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
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
            'content_block' => 'report/upi-cash-commission-list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getUpiCashCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPICASH' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPICASH' AND a.member_id = '$loggedAccountID'";
			
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

	public function matmHistory(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
            'content_block' => 'report/matm-list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function getMatmHistoryList()
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
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_matm_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_matm_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.mobile LIKE '".$keyword."%'";
				$sql.=" OR a.ref_no LIKE '".$keyword."%'";
				$sql.=" OR a.amount LIKE '".$keyword."%'";
				$sql.=" OR a.txn_type LIKE '".$keyword."%'";
				$sql.=" OR a.member_code LIKE '".$keyword."%'";
				$sql.=" OR a.mpos_number LIKE '".$keyword."%'";
				$sql.=" OR a.txn_id LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( Date(a.created) = '".$date."' )";    
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
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />Name - '.$list['user_name'];
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = $list['txn_id'];
				$nestedData[] = $list['txn_type'];
				$nestedData[] = $list['bank_rrn'];
				$nestedData[] = $list['mpos_number'];
				$nestedData[] = $list['card_number'].'<br />'.$list['name'].'<br />'.$list['mobile'];
	
				
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
					$nestedData[] = '<font color="blue">Hold</font>';
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

	public function axisAccountReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(10, $activeService)){
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
            'content_block' => 'report/axis-account-report'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function getAxisAccountList()
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_axis_account_api_response as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.member_id = '$loggedAccountID' AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_axis_account_api_response as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.member_id = '$loggedAccountID' AND a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.first_name LIKE '".$keyword."%' ";    
				$sql.=" OR a.last_name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.pincode LIKE '".$keyword."%' ";
				$sql.=" OR a.application_no LIKE '".$keyword."%' ";
				$sql.=" OR a.tracker_id LIKE '".$keyword."%' ";
				$sql.=" OR b.user_code LIKE '".$keyword."%' ";
				$sql.=" OR b.name LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( Date(a.created) = '".$date."' )";    
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
				
				$decodeResponse = json_decode($list['api_response'],true);
				$webUrl = isset($decodeResponse['data']) ? $decodeResponse['data'] : '';
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['user_code'].'<br />'.$list['name'];
				$nestedData[] = $list['reqid'];
				$nestedData[] = '<a href="'.$webUrl.'" target="_blank">Open URL</a>';
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
	
	public function nsdlPanReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(22, $activeService)){
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
            'content_block' => 'report/nsdl-pan-report'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function getNsdlPanList()
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_nsdl_transcation as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.member_id = '$loggedAccountID' AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_nsdl_transcation as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.member_id = '$loggedAccountID' AND a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.first_name LIKE '".$keyword."%' ";    
				$sql.=" OR a.last_name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.pincode LIKE '".$keyword."%' ";
				$sql.=" OR a.application_no LIKE '".$keyword."%' ";
				$sql.=" OR a.tracker_id LIKE '".$keyword."%' ";
				$sql.=" OR b.user_code LIKE '".$keyword."%' ";
				$sql.=" OR b.name LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( Date(a.created) = '".$date."' )";    
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
				$nestedData[] = $list['first_name'];
				$nestedData[] = $list['last_name'];
				$nestedData[] = $list['middle_name'];
				$nestedData[] = $list['email_id'];
				$nestedData[] = $list['gender'];
				$nestedData[] = $list['transaction_id'];
				if($list['utr_no'])
				{
					$nestedData[] = $list['utr_no'];

				}
				else
				{
					$nestedData[] = 'Not Available';
				}
				if($list['ack_no'])
				{
					$nestedData[] = $list['ack_no'];

				}
				else
				{
					$nestedData[] = 'Not Available';
				}

				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('master/report/checkNsdlPanStatus').'/'.$list['id'].'">Check PAN Status</a>';
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('master/report/checkNsdlPanTranscationStatus').'/'.$list['id'].'">Check Transcation Status</a>';
				
			if($list)

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
	
	
	
	public function checkNsdlPanStatus($ref_id = '') {
		
		//get logged user info
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		$memberID = $loggedUser['id'];


		$chk_ref_id = $this->db->get_where('member_nsdl_transcation',array('account_id'=>$account_id,'member_id'=>$memberID,'id'=>$ref_id))->row_array();

		


		if(!$chk_ref_id){

			$this->Az->redirect('master/report/nsdlPanReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
		}
		

		log_message('debug', 'NSDL PAN  check status api called.');

		$datapost = array();
		$datapost['refid'] = $chk_ref_id['transaction_id'];
		

		log_message('debug', 'NSDL PAN  check status  api post request data - '.json_encode($datapost));
		
		$key = $accountData['paysprint_aeps_key'];
		$iv = $accountData['paysprint_aeps_iv'];
		
		
		$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
		$body=       base64_encode($cipher);
		$jwt_payload = array(
			'timestamp'=>time(),
			'partnerId'=>$accountData['paysprint_partner_id'],
			//'partnerId'=>PAYSPRINT_PARTNER_ID,
			'reqid'=>time().rand(1111,9999)
		);
		
		$secret = $accountData['paysprint_secret_key'];

		//$secret = PAYSPRINT_SECRET_KEY;

		$token = $this->Jwt_model->encode($jwt_payload,$secret);
		
	        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
		
		$header = [
			'Token:'.$token,
			//'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY,
			
		];
		
		
		$httpUrl = PAYSPRINT_PAN_STATUS_CHECK_URL;
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $httpUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $datapost,
			CURLOPT_HTTPHEADER => $header
		));

		$output = curl_exec($curl);
		curl_close($curl);
		
		log_message('debug', 'PAN STATUS check  status api final response - '.$output);

		$responseData = json_decode($output,true);
		
		$api_data = array(
			'account_id'=>$account_id,
			'user_id' => $memberID,
			'api_url' => $httpUrl,
			'post_data' => json_encode($datapost),
			'api_response' => $output,
			'created' => date('Y-m-d H:i:s')	
		);
		$this->db->insert('nsdl_api_response',$api_data);

		
		if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

			
			$this->Az->redirect('master/report/nsdlPanReport', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>');

		}
		else{

			
			$this->Az->redirect('master/report/nsdlPanReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>');
		}
		
	}
    

    //check transcation status

    public function checkNsdlPanTranscationStatus($ref_id = '') {
		
		//get logged user info
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		$memberID = $loggedUser['id'];


		$chk_ref_id = $this->db->get_where('member_nsdl_transcation',array('account_id'=>$account_id,'member_id'=>$memberID,'id'=>$ref_id))->row_array();

		


		if(!$chk_ref_id){

			$this->Az->redirect('master/report/nsdlPanReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
		}
		

		log_message('debug', 'NSDL PAN Transcation check status api called.');

		$datapost = array();
		$datapost['refid'] = $chk_ref_id['transaction_id'];
		

		log_message('debug', 'NSDL PAN Transcation check status  api post request data - '.json_encode($datapost));
		
		$key = $accountData['paysprint_aeps_key'];
		$iv = $accountData['paysprint_aeps_iv'];
		 /*$key =PAYSPRINT_AEPS_KEY;
        $iv=  PAYSPRINT_AEPS_IV;*/
		
		$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
		$body=       base64_encode($cipher);
		$jwt_payload = array(
			'timestamp'=>time(),
			'partnerId'=>$accountData['paysprint_partner_id'],
			//'partnerId'=>PAYSPRINT_PARTNER_ID,
			'reqid'=>time().rand(1111,9999)
		);
		
		$secret = $accountData['paysprint_secret_key'];

		//$secret = PAYSPRINT_SECRET_KEY;

		$token = $this->Jwt_model->encode($jwt_payload,$secret);
		
	        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
		
		$header = [
			'Token:'.$token,
			//'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY,
			
		];
		
		
		$httpUrl = PAYSPRINT_PAN_TRANSCATION_STATUS_CHECK_URL;
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $httpUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $datapost,
			CURLOPT_HTTPHEADER => $header
		));

		$output = curl_exec($curl);
		curl_close($curl);
		
		log_message('debug', 'PAN  Transcation check  status api final response - '.$output);

		$responseData = json_decode($output,true);
		
		$api_data = array(
			'account_id'=>$account_id,
			'user_id' => $memberID,
			'api_url' => $httpUrl,
			'post_data' => json_encode($datapost),
			'api_response' => $output,
			'created' => date('Y-m-d H:i:s')	
		);
		$this->db->insert('nsdl_api_response',$api_data);

		
		if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){


		//pan transcation data

			$utr_no = $responseData['transaction']['utr_no'];
			$ack_no = $responseData['transaction']['ack_no'];
			$status = '';
			$added_date = $responseData['transaction']['addeddate'];

			if($responseData['transaction']['status'] == 'success')

			{
				$status = 2 ;

			}

				$this->db->where('account_id',$account_id);
				$this->db->where('member_id',$memberID);
				$this->db->where('id',$ref_id);
				$this->db->update('member_nsdl_transcation',array('status'=>$status,'ack_no'=>$ack_no,'utr_no'=>$utr_no,'creation_date'=>$added_date,'updated'=>date('Y-m-d H:i:s')));

			$this->Az->redirect('master/report/nsdlPanReport', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transcation Status Check Succesfully.</div>');

		}
		else{

			
			$this->Az->redirect('master/report/nsdlPanReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>');
		}
		
	}
	



	public function utiBalanceReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'get_list'=>$get_list,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/uti-balance-request'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getUtiBalanceList()
	{	
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
			0 => 'id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as retailer_name FROM tbl_uti_balance_request as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql ="SELECT a.*, b.user_code as user_code, b.name as retailer_name FROM tbl_uti_balance_request as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txn_id LIKE '%".$keyword."%' ";
				$sql.=" OR a.uti_pan_id LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
			//echo $this->db->last_query();
			//print_r($get_filter_data);
			//die;
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['user_code']."<br />".$list['retailer_name'];
				$nestedData[] = $list['txn_id'];
				$nestedData[] = $list['uti_pan_id'];
				$nestedData[] = $list['coupon'];	
				
				if($list['status'] == 1)
				{
					
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2)
				{
					$nestedData[] = '<font color="green">Approved</font>';
					//$nestedData[] ='Updated';
				}
				elseif($list['status'] == 3)
				{
					$nestedData[] = '<font color="red">Rejected</font>';
					//$nestedData[] ='Updated';
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
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
        $this->parser->parse('master/layout/column-2' , $data);
    
	
	}




    
}