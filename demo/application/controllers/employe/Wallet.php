<?php 
class Wallet extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkEmployePermission();
        $this->load->model('employe/Wallet_model');		
        $this->lang->load('employe/wallet', 'english');
        
    }

    public function myWalletList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getMyWalletList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
				$sql.=" OR a.description LIKE '%".$keyword."%'";
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

	public function walletList(){

		$account_id = $this->User->get_domain_account();
		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$memberList = $this->db->where_in('role_id',array(3,4,5,6,8))->get_where('users',array('account_id'=>$account_id))->result_array();

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
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getWalletList()
	{	
		$account_id = $this->User->get_domain_account();
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$member_id = '';
	   	$fromDate = '';
	   	$toDate = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$member_id = isset($filterData[1]) ? trim($filterData[1]) : '';
			$fromDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $toDate = isset($filterData[3]) ? trim($filterData[3]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		    $this->db->query("SET SESSION group_concat_max_len = 1000000;");
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND b.role_id > 2 AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;

			$sql2Filter = '';
			if($fromDate && $toDate)
            {
                $sql2Filter = " AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
            if($keyword != '') {   
				$sql2Filter.=" AND ( b.user_code LIKE '%".$keyword."%' ";  
				$sql2Filter.=" OR a.description LIKE '%".$keyword."%'";
				$sql2Filter.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql2Filter.=" AND  b.id = $member_id ";    
			}

			$sql2 = "SELECT SUM(COALESCE(CASE WHEN a.type = 1 THEN a.amount END,0)) totalCreditAmount,count( case when a.type=1 then 1 else NULL end) totalCreditRecord, SUM(COALESCE(CASE WHEN a.type = 2 THEN a.amount END,0)) totalDebitAmount,count( case when a.type=2 then 1 else NULL end) totalDebitRecord FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.wallet_type = 1 AND b.role_id > 2 AND a.account_id = '$account_id'".$sql2Filter;
			
            $getCreditData = $this->db->query($sql2)->row_array();
            $totalCreditAmount = isset($getCreditData['totalCreditAmount']) ? $getCreditData['totalCreditAmount'] : '0.00' ;
            $totalCreditRecord = isset($getCreditData['totalCreditRecord']) ? $getCreditData['totalCreditRecord'] : 0 ;
            $totalDebitAmount = isset($getCreditData['totalDebitAmount']) ? $getCreditData['totalDebitAmount'] : '0.00' ;
            $totalDebitRecord = isset($getCreditData['totalDebitRecord']) ? $getCreditData['totalDebitRecord'] : 0 ;
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";  
				$sql.=" OR a.description LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql.=" AND  b.id = $member_id ";    
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
					"data"            => $data,
					"totalCreditAmount" => '&#8377; '.number_format($totalCreditAmount,2),
					'totalCreditRecord' => $totalCreditRecord,
					"totalDebitAmount" => '&#8377; '.number_format($totalDebitAmount,2),
					'totalDebitRecord' => $totalDebitRecord
					);

		echo json_encode($json_data);  // send data as json format
	}



	public function creditList(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$account_id = $this->User->get_domain_account();
		// get users list
		$memberList = $this->db->where_in('role_id',array(3,4,5,6,8))->get_where('users',array('account_id'=>$account_id))->result_array();
		
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
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getCreditList()
	{	
		$account_id = $this->User->get_domain_account();
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$member_id = '';
	   	$fromDate = '';
	   	$toDate = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$member_id = isset($filterData[1]) ? trim($filterData[1]) : '';
			$fromDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $toDate = isset($filterData[3]) ? trim($filterData[3]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.type=1 AND a.wallet_type = 1 AND a.account_id = '$account_id' AND b.role_id > 2";

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;

			$sql2Filter = '';
			if($fromDate && $toDate)
            {
                $sql2Filter = " AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
            if($keyword != '') {   
				$sql2Filter.=" AND ( b.user_code LIKE '%".$keyword."%' ";  
				$sql2Filter.=" OR a.description LIKE '%".$keyword."%'";
				$sql2Filter.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql2Filter.=" AND  b.id = $member_id ";    
			}

			$sql2 = "SELECT SUM(COALESCE(CASE WHEN a.type = 1 THEN a.amount END,0)) totalCreditAmount,count( case when a.type=1 then 1 else NULL end) totalCreditRecord, SUM(COALESCE(CASE WHEN a.type = 2 THEN a.amount END,0)) totalDebitAmount,count( case when a.type=2 then 1 else NULL end) totalDebitRecord FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.type=1 AND  a.wallet_type = 1 AND b.role_id > 2 AND a.account_id = '$account_id'".$sql2Filter;
			
            $getCreditData = $this->db->query($sql2)->row_array();
            $totalCreditAmount = isset($getCreditData['totalCreditAmount']) ? $getCreditData['totalCreditAmount'] : '0.00' ;
            $totalCreditRecord = isset($getCreditData['totalCreditRecord']) ? $getCreditData['totalCreditRecord'] : 0 ;
            $totalDebitAmount = isset($getCreditData['totalDebitAmount']) ? $getCreditData['totalDebitAmount'] : '0.00' ;
            $totalDebitRecord = isset($getCreditData['totalDebitRecord']) ? $getCreditData['totalDebitRecord'] : 0 ;
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";  
				$sql.=" OR a.description LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql.=" AND  b.id = $member_id ";    
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
					"data"            => $data,
					"totalCreditAmount" => '&#8377; '.number_format($totalCreditAmount,2),
					'totalCreditRecord' => $totalCreditRecord,
					
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function debitList(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		// get users list
		$memberList = $this->db->where_in('role_id',array(3,4,5,6,8))->get_where('users',array('account_id'=>$account_id))->result_array();
		
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
            'content_block' => 'wallet/debitList'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getDebitList()
	{	
		$account_id = $this->User->get_domain_account();
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$member_id = '';
	   	$fromDate = '';
	   	$toDate = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$member_id = isset($filterData[1]) ? trim($filterData[1]) : '';
			$fromDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $toDate = isset($filterData[3]) ? trim($filterData[3]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.type=2 AND a.wallet_type = 1 AND a.account_id = '$account_id' AND b.role_id > 2";

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;

			$sql2Filter = '';
			if($fromDate && $toDate)
            {
                $sql2Filter = " AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
            if($keyword != '') {   
				$sql2Filter.=" AND ( b.user_code LIKE '%".$keyword."%' ";  
				$sql2Filter.=" OR a.description LIKE '%".$keyword."%'";
				$sql2Filter.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql2Filter.=" AND  b.id = $member_id ";    
			}

			$sql2 = "SELECT SUM(COALESCE(CASE WHEN a.type = 1 THEN a.amount END,0)) totalCreditAmount,count( case when a.type=1 then 1 else NULL end) totalCreditRecord, SUM(COALESCE(CASE WHEN a.type = 2 THEN a.amount END,0)) totalDebitAmount,count( case when a.type=2 then 1 else NULL end) totalDebitRecord FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.type=2 AND  a.wallet_type = 1 AND b.role_id > 2 AND a.account_id = '$account_id'".$sql2Filter;
			
            $getCreditData = $this->db->query($sql2)->row_array();
            $totalCreditAmount = isset($getCreditData['totalCreditAmount']) ? $getCreditData['totalCreditAmount'] : '0.00' ;
            $totalCreditRecord = isset($getCreditData['totalCreditRecord']) ? $getCreditData['totalCreditRecord'] : 0 ;
            $totalDebitAmount = isset($getCreditData['totalDebitAmount']) ? $getCreditData['totalDebitAmount'] : '0.00' ;
            $totalDebitRecord = isset($getCreditData['totalDebitRecord']) ? $getCreditData['totalDebitRecord'] : 0 ;
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";  
				$sql.=" OR a.description LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql.=" AND  b.id = $member_id ";    
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
					"data"            => $data,
					"totalDebitAmount" => '&#8377; '.number_format($totalDebitAmount,2),
					'totalDebitRecord' => $totalDebitRecord,
					
					);

		echo json_encode($json_data);  // send data as json format
	}



	
	// add member
	public function addWallet()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		// get users list
		$memberList = $this->db->where_in('role_id',array(3,4,5,6,8))->get_where('users',array('account_id'=>$account_id))->result_array();

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

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveWallet()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$admin_id = $loggedUser['id'];
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
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
			$chk_member = $this->db->get_where('users',array('id'=>$post['member'],'account_id'=>$account_id,'role_id >'=>2))->num_rows();
			if(!$chk_member)
			{
				$this->Az->redirect('employe/wallet/walletList', 'system_message_error',lang('WALLET_ERROR'));
			}

			if($post['type'] == 2)
			{
				$member_before_balance = $this->User->getMemberWalletBalanceSP($post['member'],1);
				if($member_before_balance < $post['amount']){
					$this->Az->redirect('employe/wallet/addWallet', 'system_message_error',lang('MEMBER_WALLET_BALANCE_ERROR'));
				}
			}

			if($accountData['account_type'] == 2){

				$admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id,1);
				if($post['type'] == 1)
				{
					if($admin_wallet_balance < $post['amount']){

						$this->Az->redirect('employe/wallet/addWallet', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
					}
				}
			}
			
			$status = $this->Wallet_model->saveWallet($post);
			
			if($status == true)
			{
				$this->Az->redirect('employe/wallet/walletList', 'system_message_error',lang('WALLET_SAVED'));
			}
			else
			{
				$this->Az->redirect('employe/wallet/walletList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
		}
	
	}
	
	public function getMemberWalletBalance($memberID = 0)
	{
		$account_id = $this->User->get_domain_account();
		echo json_encode(array(
			'status' => 1,
			'balance' => $this->User->getMemberWalletBalanceSP($memberID)
		));
	}
	
	public function requestList()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
        $this->parser->parse('employe/layout/column-1', $data);
		
    }
	
	public function getRequestList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_member_fund_request as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.request_wallet_type = 1 AND a.account_id = '$account_id' AND b.role_id > 2";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name, b.user_code as member_code FROM tbl_member_fund_request as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.request_wallet_type = 1 AND a.account_id = '$account_id' AND b.role_id > 2";	

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
					$nestedData[] ='<a title="Approve" class="btn btn-success btn-sm" href="'.base_url('employe/wallet/updateRequestAuth').'/'.$list['id'].'/1" onclick="return confirm(\'Are you sure you want to approve this request?\')"><i class="fa fa-check" aria-hidden="true"></i></a> <a title="Reject" class="btn btn-danger btn-sm" href="'.base_url('employe/wallet/updateRequestAuth').'/'.$list['id'].'/2" onclick="return confirm(\'Are you sure you want to reject this request?\')"><i class="fa fa-times" aria-hidden="true"></i></a>';
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
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		// check request id valid or not
		$chk_request_id = $this->db->get_where('member_fund_request',array('id'=>$requestID,'status'=>1,'account_id'=>$account_id))->num_rows();
		if(!$chk_request_id)
		{
			$this->Az->redirect('employe/wallet/requestList', 'system_message_error',lang('WALLET_ERROR'));
		}

		// get request member id
		$chk_request_id = $this->db->join('users','users.id = member_fund_request.member_id')->get_where('member_fund_request',array('member_fund_request.id'=>$requestID,'member_fund_request.status'=>1,'member_fund_request.account_id'=>$account_id,'users.role_id >'=>2))->num_rows();
		if(!$chk_request_id)
		{
			$this->Az->redirect('employe/wallet/requestList', 'system_message_error',lang('WALLET_ERROR'));
		}

		if($accountData['account_type'] == 2){
			if($status == 1)
			{
				// check account wallet balance
				$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

				$get_request_data = $this->db->get_where('member_fund_request',array('id'=>$requestID,'status'=>1))->row_array();
		        $memberID = $get_request_data['member_id'];
		        $amount = $get_request_data['request_amount'];

				if($amount > $wallet_balance)
				{
					$this->Az->redirect('employe/wallet/requestList', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
				}
			}
		}
		
		$this->Wallet_model->updateRequestAuth($requestID,$status);
		if($status == 1){
			$this->Az->redirect('employe/wallet/requestList', 'system_message_error',lang('REQUEST_APPROVE_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('employe/wallet/requestList', 'system_message_error',lang('REQUEST_REJECT_SUCCESS'));
		}
	}

	public function myRequestList()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
        $this->parser->parse('employe/layout/column-1', $data);
		
    }
	
	public function getMyRequestList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    public function requestAuth()
	{
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
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
				$this->Az->redirect('employe/wallet/myRequestList', 'system_message_error',lang('REQUEST_GENERATE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/wallet/myRequestList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
		}
	
	}

	public function topupHistory(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getTopupHistory()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_fund_request as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.request_wallet_type = 1 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_fund_request as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.request_wallet_type = 1 AND a.account_id = '$account_id'";
			
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
				$nestedData[] = $list['request_id'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['request_amount'].' /-';
				$nestedData[] = ($list['surcharge_amount']) ? $list['surcharge_amount'].' /-' : '0 /-';
				$nestedData[] = ($list['wallet_settlement_amount']) ? $list['wallet_settlement_amount'].' /-' : '0 /-';
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				if($list['gateway_status'] == 1 || $list['gateway_status'] == 0) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['gateway_status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['gateway_status'] == 3) {
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


	public function rwalletDeduct(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$accountData = $this->User->get_account_data($account_id);
	 	if($accountData['is_wallet_deduction'] == 0){
	 		$this->Az->redirect('admin/dashboard', 'system_message_error',lang('WALLET_ERROR'));
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
            'content_block' => 'wallet/rwalletDeduct'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function rwalletDeductAuth()
	{
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $accountData = $this->User->get_account_data($account_id);
	 	if($accountData['is_wallet_deduction'] == 0){
	 		$this->Az->redirect('admin/dashboard', 'system_message_error',lang('WALLET_ERROR'));
	 	}
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_type', 'User Type', 'required');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
			
			$this->rwalletDeduct();
		}
		else
		{	
			$user_type = isset($post['user_type']) ? $post['user_type'] : '';
			$amount = isset($post['amount']) ? $post['amount'] : 0;
			
			if($user_type == 0){

			  $user = $this->db->where_in('role_id',array(3,4,5,6,8))->get_where('users',array('account_id'=>$account_id))->result_array();	

			}
			else{

				$user = $this->db->get_where('users',array('role_id'=>$user_type,'account_id'=>$account_id))->result_array();
			}
			

			if($user){

				  $total_user = 0;
				  $total_deduct_user = 0;	
				  foreach($user as $list){

				  	
				  	$wallet_balance = $this->User->getMemberWalletBalanceSP($list['id']);
				  	
				  	if($wallet_balance >= $amount){

				  	  // save member wallet deduction entry	

				  	  $before_balance = $wallet_balance;
				  	  $after_balance = $before_balance - $amount;

				  	  $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $list['id'],    
			            'before_balance'      => $before_balance,
			            'amount'              => $amount,  
			            'after_balance'       => $after_balance,      
			            'status'              => 1,
			            'type'                => 2,
			            'wallet_type'         => 1,      
			            'created'             => date('Y-m-d H:i:s'),      
			            'credited_by'         => $loggedUser['id'],
			            'description'         => 'Wallet deduction by admin.'            
			            );

			            $this->db->insert('member_wallet',$wallet_data);


				  	  $total_deduct_user++;	
				  	}

				  	$total_user++;
				  }

				  
				  if($total_deduct_user){
					  //save deduction history

					  $total_deduct_amount = $amount * $total_deduct_user;

					  $deduction_data = array(

					   'account_id'  => $account_id,
					   'user_type'   => $user_type,
					   'wallet_type' => 1,
					   'amount'      => $amount,
					   'description' => $post['description'],
					   'total_user'  => $total_user,
					   'total_deduct_user' => $total_deduct_user,
					   'total_deduct_amount' => $total_deduct_amount, 	
					   'created' => date('Y-m-d H:i:s')

					  );

					 $status = $this->db->insert('wallet_deduction_history',$deduction_data);
				   }
				   else{

				   	$this->Az->redirect('employe/wallet/rwalletDeduct', 'system_message_error',lang('USER_WALLET_ERROR'));

				   }	
				  
				

					if($status == true)
					{
						$this->Az->redirect('employe/wallet/rwalletDeduct', 'system_message_error',lang('R-WALLET_DEDUCT_SUCCESS'));
					}
					else
					{
						$this->Az->redirect('employe/wallet/rwalletDeduct', 'system_message_error',lang('WALLET_ERROR'));
					}
			}
			else{

				$this->Az->redirect('employe/wallet/rwalletDeduct', 'system_message_error',lang('WALLET_ERROR'));

			}
		}
	
	}






	public function apiFundRequestList()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$siteUrl = site_url();

		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/api-fund-request-list',
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('employe/layout/column-1', $data);
		
    }
	
	public function getApiFundRequestList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_api_member_fund_request as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.account_id = '$account_id' AND b.role_id = 6";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name, b.user_code as member_code FROM tbl_api_member_fund_request as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE  a.account_id = '$account_id' AND b.role_id = 6";	

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
				$nestedData[] = 'From Account - '.$list['from_account'].'<br>'.'To Account - '.$list['to_account'].'<br>'.'To Bank - '.$list['to_bank'];
				$nestedData[] = $list['txn_id'];
				$nestedData[] = $list['ref_no'];
				$nestedData[] = $list['utr_no'];
				$nestedData[] = 'INR '.number_format($list['amount'],2);
				$nestedData[]= '<a title="View" href="'.$list['image_url'].'" target="_blank">View</a>';
				$nestedData[] = date('d-m-Y',strtotime($list['created']));
				
				if($list['status'] == 1)
				{
					$nestedData[] = '<font color="black">Pending</font>';
					$nestedData[] ='<a title="Approve" class="btn btn-success btn-sm" href="'.base_url('employe/wallet/updateApiFundRequestAuth').'/'.$list['id'].'/1" onclick="return confirm(\'Are you sure you want to approve this request?\')"><i class="fa fa-check" aria-hidden="true"></i></a>';
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



	public function updateApiFundRequestAuth($requestID = 0, $status = 0)
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $siteUrl = site_url();
		// check request id valid or not
		$chk_request_id = $this->db->get_where('api_member_fund_request',array('id'=>$requestID,'status'=>1,'account_id'=>$account_id))->num_rows();
		if(!$chk_request_id)
		{
			$this->Az->redirect('employe/wallet/requestList', 'system_message_error',lang('WALLET_ERROR'));
		}

		$request_data = $this->db->get_where('api_member_fund_request',array('id'=>$requestID,'status'=>1,'account_id'=>$account_id))->row_array();


		$member_id = $request_data['member_id'];

		$member_wallet_balance = $this->User->getMemberWalletBalanceSP($member_id);

		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'member_wallet_balance'=> $member_wallet_balance,
            'request_data'		=> $request_data,
            'member_id'			=> $member_id,
            'requestID'	=>$requestID,
            'content_block' => 'wallet/api-fund-request-update',
            'manager_description' => lang('SITE_NAME'),           
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );

        $this->parser->parse('employe/layout/column-1', $data);
			
		
	}


	public function saveApiFundRequest()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$admin_id = $loggedUser['id'];
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');
		//$this->form_validation->set_rules('member', 'Member', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->addWallet();
		}
		else
		{	

			
			// check member is valid or not
			$chk_member = $this->db->get_where('users',array('id'=>$post['member'],'account_id'=>$account_id))->num_rows();
			if(!$chk_member)
			{
				$this->Az->redirect('employe/wallet/apiFundRequestList', 'system_message_error',lang('WALLET_ERROR'));
			}

			$chk_request_id = $this->db->get_where('api_member_fund_request',array('id'=>$post['request_id'],'status'=>1,'account_id'=>$account_id))->num_rows();
			if(!$chk_request_id)
			{
				$this->Az->redirect('employe/wallet/apiFundRequestList', 'system_message_error',lang('WALLET_ERROR'));
			}

			
			
			$chk_request_data = $this->db->get_where('api_member_fund_request',array('id'=>$post['request_id'],'status'=>1,'account_id'=>$account_id))->row_array();
			
			$status = $this->Wallet_model->saveApiFundWallet($post,$chk_request_data);
			
			if($status == true)
			{
				$this->Az->redirect('employe/wallet/apiFundRequestList', 'system_message_error',lang('WALLET_SAVED'));
			}
			else
			{
				$this->Az->redirect('employe/wallet/apiFundRequestList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
		}
	
	}
	
	
	public function rejectApiFundRequest($requestID =0)
	{

		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];


        $chk_request_id = $this->db->get_where('api_member_fund_request',array('id'=>$requestID,'status'=>1,'account_id'=>$account_id))->num_rows();
			if(!$chk_request_id)
			{
				$this->Az->redirect('employe/wallet/apiFundRequestList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
			$chk_request_data = $this->db->get_where('api_member_fund_request',array('id'=>$requestID,'status'=>1,'account_id'=>$account_id))->row_array();
			
			$txnid = $chk_request_data['txn_id'];
            $utrno = $chk_request_data['utr_no'];
            $member_id = $chk_request_data['member_id'];
            $amount = $chk_request_data['amount'];
            $post['description'] = 'Failed From Bank';
         // update request status
            $this->db->where('id',$requestID);
            $this->db->update('api_member_fund_request',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));
            
        //     $api_url = 'https://www.paymyrecharge.in/myapi/dmr/MorningwalletRequest.aspx?transcionid='.$txnid.'&utrno='.$utrno.'&status=FAILED&memberID='.$member_id.'&Amount='.$amount.'&reason='.$post['description'].'';
            
            
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL,$api_url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // //curl_setopt($ch, CURLOPT_HTTPHEADER,'');
        // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // $output = curl_exec ($ch);
        // curl_close ($ch);
        // log_message('debug', 'PMR Request Data Reject url - '.json_encode($api_url));
        // log_message('debug', 'PMR Request Reject Api response - '.json_encode($output));   



            $this->Az->redirect('employe/wallet/apiFundRequestList', 'system_message_error',lang('REQUEST_REJECT_SUCCESS'));

	}







	
	
}