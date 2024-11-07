<?php 
class Member extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkEmployePermission();
        $this->load->model('employe/Member_model');		
        $this->lang->load('employe/member', 'english');
        
    }

	public function memberList(){

		if(!$this->User->admin_menu_permission(2,1) || !$this->User->admin_menu_permission(11,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		
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
            'content_block' => 'member/memberList'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getMemberList()
	{	
		$account_id = $this->User->get_domain_account();
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
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3,4,5,6,8) AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3,4,5,6,8) AND a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
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
		$totalBalance = 0;
		$totalDownlineBalance = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				//$nestedData[] = $list['name'];

				$sponserData = $this->User->get_member_sponser_data($list['created_by']);

				$creatorData = $this->User->get_member_sponser_data($list['creator_id']);
				
				$str = '<table class="table">';
				$str.='<tr><td><b>Name </b></td><td>'.$list['name'].'</td></tr>';
				$str.='<tr><td><b>User name </b></td><td>'.$list['username'].'</td></tr>';
				$str.='<tr><td><b>Password </b></td><td>'.$list['decode_password'].'</td></tr>';
				$str.='<tr><td><b>Transaction Password </b></td><td>'.$list['decoded_transaction_password'].'</td></tr>';
				$str.='<tr><td><b>Email </b></td><td>'.$list['email'].'</td></tr>';
				$str.='<tr><td><b>Mobile </b></td><td>'.$list['mobile'].'</td></tr>';
				if($list['package_name']){
					$str.='<tr><td><b>Pacakge </b></td><td>'.$list['package_name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Pacakge </b></td><td>N/A</td></tr>';
				}
				$str.='<tr><td><b>Minimum Wallet </b></td><td>'.$list['min_wallet_balance'].'</td></tr>';
				if($sponserData['role_id'] == 2)
				{
					$str.='<tr><td><b>Sponser </b></td><td>'.$sponserData['name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Sponser </b></td><td><a href="'.base_url('employe/member/viewUpline/'.$sponserData['id']).'">'.$sponserData['name'].' ('.$sponserData['user_code'].')</a></td></tr>';
				}

				$str.='<tr><td><b>Creator </b></td><td>'.$creatorData['name'].' ('.$creatorData['user_code'].')</td></tr>';
				
				$str.='</table>';
				$nestedData[] = $str;

				$str = '<table class="table">';
				if($list['state_id'])
				{
					$get_state_name = $stateList = $this->db->get_where('states',array('id'=>$list['state_id']))->row_array();

					$str.='<tr><td><b>State </b></td><td>'.$get_state_name['name'].'</td></tr>';
				}

				$str.='<tr><td><b>District </b></td><td>'.$list['district'].'</td></tr>';
				$str.='<tr><td><b>Block </b></td><td>'.$list['block'].'</td></tr>';
				$str.='<tr><td><b>Village </b></td><td>'.$list['village'].'</td></tr>';
				$str.='<tr><td><b>Aadhar No  </b></td><td>'.$list['aadhar_no'].'</td></tr>';
				$str.='<tr><td><b>Pan No </b></td><td>'.$list['pan_no'].'</td></tr>';
				
				$str.='<tr><td><b>Pincode </b></td><td>'.$list['pincode'].'</td></tr>';
				$str.='<tr><td><b>Address </b></td><td>'.$list['address'].'</td></tr>';
				
				$str.='</table>';
				$nestedData[] = $str;


				$nestedData[]='&#8377; '.number_format($this->User->getMemberWalletBalanceSP($list['id']),2);
				$downline_balance = $list['aeps_wallet_balance'];


				/*$downline_balance = 0;
				if($downline_id)
				{
					$get_downline_balance = $this->db->select('SUM(wallet_balance) as total_balance')->where_in('id',$downline_id)->get_where('users',array('account_id'=>$account_id))->row_array();
					$downline_balance = isset($get_downline_balance['total_balance']) ? $get_downline_balance['total_balance'] : 0 ;
				}*/

				$downline_id = $this->User->get_member_downline_id($list['id']);
				if(count($downline_id)){
					$nestedData[]='<a href="'.base_url('employe/member/viewDownline/'.$list['id']).'" title="View Downline">'.count($downline_id).'</a>';
				}
				else
				{
					$nestedData[]='<a href="#" title="View Downline">'.count($downline_id).'</a>';
				}

				$nestedData[] = date('d-M-Y',strtotime($list['created']));
				if($list['is_active'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['is_active'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/member/editMember').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
				
				$data[] = $nestedData;

				$totalBalance+=$list['wallet_balance'];
				$totalDownlineBalance+=$downline_balance;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,
					"totalBalance" => '&#8377; '.number_format($totalBalance,2),
					"totalDownlineBalance" => '&#8377; '.number_format($totalDownlineBalance,2),
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function mdMemberList(){

		if(!$this->User->admin_menu_permission(2,1) || !$this->User->admin_menu_permission(12,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		
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
            'content_block' => 'member/mdMemberList'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getMDMemberList()
	{	
		$account_id = $this->User->get_domain_account();
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
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3) AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3) AND a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
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
		$totalBalance = 0;
		$totalDownlineBalance = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];

				$sponserData = $this->User->get_member_sponser_data($list['created_by']);

				$creatorData = $this->User->get_member_sponser_data($list['creator_id']);
				
				$str = '<table class="table">';
				$str.='<tr><td><b>User name </b></td><td>'.$list['username'].'</td></tr>';
				$str.='<tr><td><b>Password </b></td><td>'.$list['decode_password'].'</td></tr>';
				$str.='<tr><td><b>Transaction Password </b></td><td>'.$list['decoded_transaction_password'].'</td></tr>';
				$str.='<tr><td><b>Email </b></td><td>'.$list['email'].'</td></tr>';
				$str.='<tr><td><b>Mobile </b></td><td>'.$list['mobile'].'</td></tr>';
				if($list['package_name']){
					$str.='<tr><td><b>Pacakge </b></td><td>'.$list['package_name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Pacakge </b></td><td>N/A</td></tr>';
				}
				$str.='<tr><td><b>Minimum Wallet </b></td><td>'.$list['min_wallet_balance'].'</td></tr>';
				if($sponserData['role_id'] == 2)
				{
					$str.='<tr><td><b>Sponser </b></td><td>'.$sponserData['name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Sponser </b></td><td><a href="'.base_url('employe/member/viewUpline/'.$sponserData['id']).'">'.$sponserData['name'].' ('.$sponserData['user_code'].')</a></td></tr>';
				}
				$str.='<tr><td><b>Creator </b></td><td>'.$creatorData['name'].' ('.$creatorData['user_code'].')</td></tr>';
				$str.='</table>';
				$nestedData[] = $str;

				$nestedData[]='&#8377; '.number_format($this->User->getMemberWalletBalanceSP($list['id']),2);
				$downline_balance = $list['aeps_wallet_balance'];


				/*$downline_balance = 0;
				if($downline_id)
				{
					$get_downline_balance = $this->db->select('SUM(wallet_balance) as total_balance')->where_in('id',$downline_id)->get_where('users',array('account_id'=>$account_id))->row_array();
					$downline_balance = isset($get_downline_balance['total_balance']) ? $get_downline_balance['total_balance'] : 0 ;
				}*/

				$downline_id = $this->User->get_member_downline_id($list['id']);
				if(count($downline_id)){
					$nestedData[]='<a href="'.base_url('employe/member/viewDownline/'.$list['id']).'" title="View Downline">'.count($downline_id).'</a>';
				}
				else
				{
					$nestedData[]='<a href="#" title="View Downline">'.count($downline_id).'</a>';
				}


				$nestedData[] = date('d-M-Y',strtotime($list['created']));
				if($list['is_active'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['is_active'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/member/editMember').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
				
				$data[] = $nestedData;

				$totalBalance+=$list['wallet_balance'];
				$totalDownlineBalance+=$downline_balance;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,
					"totalBalance" => '&#8377; '.number_format($totalBalance,2),
					"totalDownlineBalance" => '&#8377; '.number_format($totalDownlineBalance,2),
					);

		echo json_encode($json_data);  // send data as json format
	}
	
	public function distributorList(){

		if(!$this->User->admin_menu_permission(2,1) || !$this->User->admin_menu_permission(13,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		
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
            'content_block' => 'member/distributorList'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getDistributorList()
	{	
		$account_id = $this->User->get_domain_account();
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
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (4) AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (4) AND a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
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
		$totalBalance = 0;
		$totalDownlineBalance = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];

				$sponserData = $this->User->get_member_sponser_data($list['created_by']);

				$creatorData = $this->User->get_member_sponser_data($list['creator_id']);
				
				$str = '<table class="table">';
				$str.='<tr><td><b>User name </b></td><td>'.$list['username'].'</td></tr>';
				$str.='<tr><td><b>Password </b></td><td>'.$list['decode_password'].'</td></tr>';
				$str.='<tr><td><b>Transaction Password </b></td><td>'.$list['decoded_transaction_password'].'</td></tr>';
				$str.='<tr><td><b>Email </b></td><td>'.$list['email'].'</td></tr>';
				$str.='<tr><td><b>Mobile </b></td><td>'.$list['mobile'].'</td></tr>';
				if($list['package_name']){
					$str.='<tr><td><b>Pacakge </b></td><td>'.$list['package_name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Pacakge </b></td><td>N/A</td></tr>';
				}
				$str.='<tr><td><b>Minimum Wallet </b></td><td>'.$list['min_wallet_balance'].'</td></tr>';
				if($sponserData['role_id'] == 2)
				{
					$str.='<tr><td><b>Sponser </b></td><td>'.$sponserData['name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Sponser </b></td><td><a href="'.base_url('employe/member/viewUpline/'.$sponserData['id']).'">'.$sponserData['name'].' ('.$sponserData['user_code'].')</a></td></tr>';
				}
				$str.='<tr><td><b>Creator </b></td><td>'.$creatorData['name'].' ('.$creatorData['user_code'].')</td></tr>';
				$str.='</table>';
				$nestedData[] = $str;

				$nestedData[]='&#8377; '.number_format($this->User->getMemberWalletBalanceSP($list['id']),2);

				$downline_balance = $list['aeps_wallet_balance'];


				/*$downline_balance = 0;
				if($downline_id)
				{
					$get_downline_balance = $this->db->select('SUM(wallet_balance) as total_balance')->where_in('id',$downline_id)->get_where('users',array('account_id'=>$account_id))->row_array();
					$downline_balance = isset($get_downline_balance['total_balance']) ? $get_downline_balance['total_balance'] : 0 ;
				}*/

				$downline_id = $this->User->get_member_downline_id($list['id']);
				if(count($downline_id)){
					$nestedData[]='<a href="'.base_url('employe/member/viewDownline/'.$list['id']).'" title="View Downline">'.count($downline_id).'</a>';
				}
				else
				{
					$nestedData[]='<a href="#" title="View Downline">'.count($downline_id).'</a>';
				}

				$nestedData[] = date('d-M-Y',strtotime($list['created']));
				if($list['is_active'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['is_active'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/member/editMember').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
				
				$data[] = $nestedData;

				$totalBalance+=$list['wallet_balance'];
				$totalDownlineBalance+=$downline_balance;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					"totalBalance" => '&#8377; '.number_format($totalBalance,2),
					"totalDownlineBalance" => '&#8377; '.number_format($totalDownlineBalance,2),
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function retailerList(){

		if(!$this->User->admin_menu_permission(2,1) || !$this->User->admin_menu_permission(14,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		
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
            'content_block' => 'member/retailerList'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getRetailerList()
	{	
		$account_id = $this->User->get_domain_account();
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
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (5) AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (5) AND a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
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
		$totalBalance = 0;
		$totalDownlineBalance = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				
				$sponserData = $this->User->get_member_sponser_data($list['created_by']);

				$creatorData = $this->User->get_member_sponser_data($list['creator_id']);

				$str = '<table class="table">';
				$str.='<tr><td><b>User name </b></td><td>'.$list['username'].'</td></tr>';
				$str.='<tr><td><b>Password </b></td><td>'.$list['decode_password'].'</td></tr>';
				$str.='<tr><td><b>Transaction Password </b></td><td>'.$list['decoded_transaction_password'].'</td></tr>';
				$str.='<tr><td><b>Email </b></td><td>'.$list['email'].'</td></tr>';
				$str.='<tr><td><b>Mobile </b></td><td>'.$list['mobile'].'</td></tr>';
				if($list['package_name']){
					$str.='<tr><td><b>Pacakge </b></td><td>'.$list['package_name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Pacakge </b></td><td>N/A</td></tr>';
				}
				$str.='<tr><td><b>Minimum Wallet </b></td><td>'.$list['min_wallet_balance'].'</td></tr>';
				if($sponserData['role_id'] == 2)
				{
					$str.='<tr><td><b>Sponser </b></td><td>'.$sponserData['name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Sponser </b></td><td><a href="'.base_url('employe/member/viewUpline/'.$sponserData['id']).'">'.$sponserData['name'].' ('.$sponserData['user_code'].')</a></td></tr>';
				}
				$str.='<tr><td><b>Creator </b></td><td>'.$creatorData['name'].' ('.$creatorData['user_code'].')</td></tr>';
				$str.='</table>';
				$nestedData[] = $str;

				$nestedData[]='&#8377; '.number_format($this->User->getMemberWalletBalanceSP($list['id']),2);

				$downline_balance = $list['aeps_wallet_balance'];


				/*$downline_balance = 0;
				if($downline_id)
				{
					$get_downline_balance = $this->db->select('SUM(wallet_balance) as total_balance')->where_in('id',$downline_id)->get_where('users',array('account_id'=>$account_id))->row_array();
					$downline_balance = isset($get_downline_balance['total_balance']) ? $get_downline_balance['total_balance'] : 0 ;
				}*/


				$nestedData[] = date('d-M-Y',strtotime($list['created']));
				if($list['is_active'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['is_active'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/member/editMember').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
				
				$data[] = $nestedData;
				
				$totalBalance+=$list['wallet_balance'];
				$totalDownlineBalance+=$downline_balance;
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					"totalBalance" => '&#8377; '.number_format($totalBalance,2),
					"totalDownlineBalance" => '&#8377; '.number_format($totalDownlineBalance,2),
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function apiMemberList(){

		if(!$this->User->admin_menu_permission(2,1) || !$this->User->admin_menu_permission(15,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		
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
            'content_block' => 'member/apiMemberList'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getApiMemberList()
	{	
		$account_id = $this->User->get_domain_account();
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
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (6) AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (6) AND a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
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

				$sponserData = $this->User->get_member_sponser_data($list['created_by']);

				$creatorData = $this->User->get_member_sponser_data($list['creator_id']);
				
				$str = '<table class="table">';
				$str.='<tr><td><b>User name </b></td><td>'.$list['username'].'</td></tr>';
				$str.='<tr><td><b>Password </b></td><td>'.$list['decode_password'].'</td></tr>';
				$str.='<tr><td><b>Transaction Password </b></td><td>'.$list['decoded_transaction_password'].'</td></tr>';
				$str.='<tr><td><b>Email </b></td><td>'.$list['email'].'</td></tr>';
				$str.='<tr><td><b>Mobile </b></td><td>'.$list['mobile'].'</td></tr>';
				if($list['package_name']){
					$str.='<tr><td><b>Pacakge </b></td><td>'.$list['package_name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Pacakge </b></td><td>N/A</td></tr>';
				}
				$str.='<tr><td><b>Minimum Wallet </b></td><td>'.$list['min_wallet_balance'].'</td></tr>';
				if($sponserData['role_id'] == 2)
				{
					$str.='<tr><td><b>Sponser </b></td><td>'.$sponserData['name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Sponser </b></td><td><a href="'.base_url('employe/member/viewUpline/'.$sponserData['id']).'">'.$sponserData['name'].' ('.$sponserData['user_code'].')</a></td></tr>';
				}
				$str.='<tr><td><b>Creator </b></td><td>'.$creatorData['name'].' ('.$creatorData['user_code'].')</td></tr>';
				$str.='</table>';
				$nestedData[] = $str;

				$nestedData[]='&#8377; '.number_format($this->User->getMemberWalletBalanceSP($list['id']),2);
				$downline_balance = $list['aeps_wallet_balance'];


				/*$downline_balance = 0;
				if($downline_id)
				{
					$get_downline_balance = $this->db->select('SUM(wallet_balance) as total_balance')->where_in('id',$downline_id)->get_where('users',array('account_id'=>$account_id))->row_array();
					$downline_balance = isset($get_downline_balance['total_balance']) ? $get_downline_balance['total_balance'] : 0 ;
				}*/

				$nestedData[] = date('d-M-Y',strtotime($list['created']));
				if($list['is_active'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['is_active'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/member/editMember').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
				
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


	public function userList(){


		if(!$this->User->admin_menu_permission(2,1) || !$this->User->admin_menu_permission(16,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		
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
            'content_block' => 'member/userList'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getUserList()
	{	
		$account_id = $this->User->get_domain_account();
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
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (8) AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (8) AND a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
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
				
				$str = '<table class="table">';
				$str.='<tr><td><b>User name </b></td><td>'.$list['username'].'</td></tr>';
				$str.='<tr><td><b>Password </b></td><td>'.$list['decode_password'].'</td></tr>';
				$str.='<tr><td><b>Transaction Password </b></td><td>'.$list['decoded_transaction_password'].'</td></tr>';
				$str.='<tr><td><b>Email </b></td><td>'.$list['email'].'</td></tr>';
				$str.='<tr><td><b>Mobile </b></td><td>'.$list['mobile'].'</td></tr>';
				if($list['package_name']){
					$str.='<tr><td><b>Pacakge </b></td><td>'.$list['package_name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Pacakge </b></td><td>N/A</td></tr>';
				}
				$str.='<tr><td><b>Minimum Wallet </b></td><td>'.$list['min_wallet_balance'].'</td></tr>';
				$str.='</table>';
				$nestedData[] = $str;

				$nestedData[]='&#8377; '.number_format($this->User->getMemberWalletBalanceSP($list['id']),2);

				$nestedData[] = date('d-M-Y',strtotime($list['created']));
				if($list['is_active'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['is_active'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/member/editMember').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
				
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
	public function addMember()
    {

    	if(!$this->User->admin_menu_permission(2,1) || !$this->User->admin_menu_permission(9,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


    	$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$accountData = $this->User->get_account_data($account_id);

		$role_id = array(3,4,5);

		if($accountData['is_disable_api_role'] == 0)
		{
			$role_id[3] = 6;
		}

		if($accountData['is_disable_user_role'] == 0)
		{
			$role_id[4] = 8;
		}
		
		// get role list
		$roleList = $this->db->where_in('id',$role_id)->get('user_roles')->result_array();

		// get country list
		$countryList = $this->db->order_by('name','asc')->get('countries')->result_array();

		$stateList = $this->db->order_by('name','asc')->get_where('states',array('country_code_char2'=>'IN'))->result_array();

		// get package list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'member/addMember',
            'roleList' => $roleList,
            'countryList' => $countryList,
            'stateList' => $stateList,
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveMember()
	{
		$account_id = $this->User->get_domain_account();
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('role_id', 'Member Type', 'required|xss_clean');
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email ', 'xss_clean|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]');
        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
        $this->form_validation->set_rules('transaction_password', 'Transaction Password', 'required|xss_clean|max_length[6]|min_length[4]');
        $this->form_validation->set_rules('country_id', 'Country', 'required|xss_clean');
        $this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');
        $this->form_validation->set_rules('city', 'City', 'required|xss_clean');
        $this->form_validation->set_rules('package_id', 'Package', 'required|xss_clean');
        $this->form_validation->set_rules('district', 'District', 'required|xss_clean');
        $this->form_validation->set_rules('block', 'Block', 'required|xss_clean');
        $this->form_validation->set_rules('village', 'Village', 'required|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
        $this->form_validation->set_rules('pincode', 'Pincode', 'required|xss_clean');
        $this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|xss_clean');
        $this->form_validation->set_rules('pan_no', 'Pan No', 'required|xss_clean');

		if ($this->form_validation->run() == FALSE) {
			
			$this->addMember();
		}
		else
		{	
			
			if($post['role_id'] == 1 || $post['role_id'] == 2  || $post['role_id'] == 7){

				$this->Az->redirect('employe/member/addMember', 'system_message_error',lang('ROLE_ERROR'));	

			}

			// check mobile already exits or not
			$chk_user_mobile = $this->db->get_where('users',array('account_id'=>$account_id,'mobile'=>$post['mobile']))->num_rows();
			if($chk_user_mobile){

				$this->Az->redirect('employe/member/addMember', 'system_message_error',lang('MOBILE_ERROR'));	

			}

			// check pacakge id valid or not
			$chk_package = $this->db->get_where('package',array('account_id'=>$account_id,'id'=>$post['package_id']))->num_rows();
			if(!$chk_package){

				$this->Az->redirect('employe/member/addMember', 'system_message_error',lang('PACKAGE_ERROR'));	

			}


			$status = $this->Member_model->saveMember($post);
			
			if($status == true)
			{
				$this->Az->redirect('employe/member/addMember', 'system_message_error',lang('MEMBER_SAVED'));
			}
			else
			{
				$this->Az->redirect('employe/member/addMember', 'system_message_error',lang('MEMBER_ERROR'));
			}
			
		}
	
	}

	// edit employe
	public function viewDownline($id = 0)
    {    

    	$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
    	$accountData = $this->User->get_account_data($account_id);
		
		// check member
		$chkMember = $this->db->get_where('users',array('id'=>$id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('employe/member/memberList', 'system_message_error',lang('MEMBER_ERROR'));
		}

		// check member
		$memberData = $this->db->get_where('users',array('id'=>$id,'account_id'=>$account_id))->row_array();

		$accountID=$loggedUser['id'];	
    	$siteUrl = site_url();
    	$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'member/viewDownline',
            'manager_description' => lang('SITE_NAME'),
			'accountID'=>$accountID,
			'id'=>$id,
			'memberData' => $memberData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    public function getDownlineMemberList()
	{	
		$account_id = $this->User->get_domain_account();
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$date = '';
	   	$memberID = 0;
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$date = isset($filterData[1]) ? trim($filterData[1]) : '';
			$memberID = isset($filterData[2]) ? trim($filterData[2]) : 0;
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3,4,5,6,8) AND a.account_id = '$account_id' AND a.created_by = '$memberID'";


			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3,4,5,6,8) AND a.account_id = '$account_id' AND a.created_by = '$memberID'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
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
		$totalBalance = 0;
		$totalDownlineBalance = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];

				$sponserData = $this->User->get_member_sponser_data($list['created_by']);
				
				$str = '<table class="table">';
				$str.='<tr><td><b>User name </b></td><td>'.$list['username'].'</td></tr>';
				$str.='<tr><td><b>Password </b></td><td>'.$list['decode_password'].'</td></tr>';
				$str.='<tr><td><b>Transaction Password </b></td><td>'.$list['decoded_transaction_password'].'</td></tr>';
				$str.='<tr><td><b>Email </b></td><td>'.$list['email'].'</td></tr>';
				$str.='<tr><td><b>Mobile </b></td><td>'.$list['mobile'].'</td></tr>';
				if($list['package_name']){
					$str.='<tr><td><b>Pacakge </b></td><td>'.$list['package_name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Pacakge </b></td><td>N/A</td></tr>';
				}
				$str.='<tr><td><b>Minimum Wallet </b></td><td>'.$list['min_wallet_balance'].'</td></tr>';
				if($sponserData['role_id'] == 2)
				{
					$str.='<tr><td><b>Sponser </b></td><td>'.$sponserData['name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Sponser </b></td><td><a href="'.base_url('employe/member/viewUpline/'.$sponserData['id']).'">'.$sponserData['name'].' ('.$sponserData['user_code'].')</a></td></tr>';
				}
				$str.='</table>';
				$nestedData[] = $str;

				$nestedData[]='&#8377; '.number_format($this->User->getMemberWalletBalanceSP($list['id']),2);
				$downline_balance = $list['aeps_wallet_balance'];


				/*$downline_balance = 0;
				if($downline_id)
				{
					$get_downline_balance = $this->db->select('SUM(wallet_balance) as total_balance')->where_in('id',$downline_id)->get_where('users',array('account_id'=>$account_id))->row_array();
					$downline_balance = isset($get_downline_balance['total_balance']) ? $get_downline_balance['total_balance'] : 0 ;
				}*/

				$downline_id = $this->User->get_member_downline_id($list['id']);
				if(count($downline_id)){
					$nestedData[]='<a href="'.base_url('employe/member/viewDownline/'.$list['id']).'" title="View Downline">'.count($downline_id).'</a>';
				}
				else
				{
					$nestedData[]='<a href="#" title="View Downline">'.count($downline_id).'</a>';
				}

				$nestedData[] = date('d-M-Y',strtotime($list['created']));
				if($list['is_active'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['is_active'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/member/editMember').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
				
				$data[] = $nestedData;

				$totalBalance+=$list['wallet_balance'];
				$totalDownlineBalance+=$downline_balance;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,
					"totalBalance" => '&#8377; '.number_format($totalBalance,2),
					"totalDownlineBalance" => '&#8377; '.number_format($totalDownlineBalance,2),
					);

		echo json_encode($json_data);  // send data as json format
	}

	// edit employe
	public function editMember($id)
    {    

    	if(!$this->User->admin_menu_permission(2,1) || !$this->User->admin_menu_permission(10,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

    	$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
    	$accountData = $this->User->get_account_data($account_id);
		
		// check member
		$chkMember = $this->db->get_where('users',array('id'=>$id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('employe/member/memberList', 'system_message_error',lang('MEMBER_ERROR'));
		}

		//get member list
		$memberList = $this->db->get_where('users',array('id'=>$id,'account_id'=>$account_id))->row_array();

		$role_id = array(3,4,5);

		if($accountData['is_disable_api_role'] == 0)
		{
			$role_id[3] = 6;
		}

		if($accountData['is_disable_user_role'] == 0)
		{
			$role_id[4] = 8;
		}

		if($memberList['role_id'] == 3)
		{
			unset($role_id[1]);
			unset($role_id[2]);
			unset($role_id[3]);
			unset($role_id[4]);
		}
		elseif($memberList['role_id'] == 4)
		{
			unset($role_id[2]);
			unset($role_id[3]);
			unset($role_id[4]);
		}
		elseif($memberList['role_id'] == 5)
		{
			unset($role_id[3]);
			unset($role_id[4]);
		}
		elseif($memberList['role_id'] == 6)
		{
			unset($role_id[0]);
			unset($role_id[1]);
			unset($role_id[2]);
		}

		// get role list
		$roleList = $this->db->where_in('id',$role_id)->get('user_roles')->result_array();

		// get country list
		$countryList = $this->db->order_by('name','asc')->get('countries')->result_array();

		$stateList = $this->db->order_by('name','asc')->get_where('states',array('country_code_char2'=>'IN'))->result_array();

		// get package list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();
		
		$accountID=$loggedUser['id'];	
    	$siteUrl = site_url();
    	$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'member/editMember',
            'manager_description' => lang('SITE_NAME'),
			'memberList' => $memberList,
			'accountID'=>$accountID,
			'id'=>$id,
			'roleList' => $roleList,
			'countryList' => $countryList,
			'stateList' => $stateList,
			'packageList' => $packageList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    //update member
	public function updateMember()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$memberID = $post['id'];
		$account_id = $this->User->get_domain_account();
    	// check member
		$chkMember = $this->db->get_where('users',array('id'=>$memberID,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('employe/member/memberList', 'system_message_error',lang('MEMBER_ERROR'));
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('role_id', 'Member Type', 'required|xss_clean');
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email ', 'xss_clean|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'xss_clean|numeric|max_length[12]');
        $this->form_validation->set_rules('password', 'Password', 'xss_clean');
        $this->form_validation->set_rules('transaction_password', 'Transaction Password', 'xss_clean|max_length[6]|min_length[4]');
        $this->form_validation->set_rules('country_id', 'Country', 'required|xss_clean');
        $this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');
        $this->form_validation->set_rules('city', 'City', 'required|xss_clean');
        $this->form_validation->set_rules('package_id', 'Package', 'required|xss_clean');
        $this->form_validation->set_rules('district', 'District', 'required|xss_clean');
        $this->form_validation->set_rules('block', 'Block', 'required|xss_clean');
        $this->form_validation->set_rules('village', 'Village', 'required|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
        $this->form_validation->set_rules('pincode', 'Pincode', 'required|xss_clean');
        $this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|xss_clean');
        $this->form_validation->set_rules('pan_no', 'Pan No', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->editMember($post['id']);
		}
		else
		{	
			if($post['role_id'] == 1 || $post['role_id'] == 2  || $post['role_id'] == 7){

				$this->Az->redirect('employe/member/editMember/'.$post['id'], 'system_message_error',lang('ROLE_ERROR'));	

			}

			// check pacakge id valid or not
			$chk_package = $this->db->get_where('package',array('account_id'=>$account_id,'id'=>$post['package_id']))->num_rows();
			if(!$chk_package){

				$this->Az->redirect('employe/member/editMember/'.$post['id'], 'system_message_error',lang('PACKAGE_ERROR'));	

			}

			// check mobile already exits or not
			$chk_user_mobile = $this->db->get_where('users',array('account_id'=>$account_id,'mobile'=>$post['mobile'],'id !='=>$memberID))->num_rows();
			if($chk_user_mobile){

				$this->Az->redirect('employe/member/editMember/'.$post['id'], 'system_message_error',lang('MOBILE_ERROR'));	

			}


			$status = $this->Member_model->updateMember($post);
			
			if($status == true)
			{
				$this->Az->redirect('employe/member/memberList', 'system_message_error',lang('MEMBER_UPDATED'));
			}
			else
			{
				$this->Az->redirect('employe/member/memberList', 'system_message_error',lang('MEMBER_ERROR'));
			}
			
		}
	
	}
	
	

	public function viewUpline($id = 0)
    {    

    	$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
    	$accountData = $this->User->get_account_data($account_id);
		
		// check member
		$chkMember = $this->db->get_where('users',array('id'=>$id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('employe/member/memberList', 'system_message_error',lang('MEMBER_ERROR'));
		}

		// check member role
		$chkMemberRole = $this->db->get_where('users',array('id'=>$id,'role_id'=>2,'account_id'=>$account_id))->num_rows();
		if($chkMemberRole)
		{
			$this->Az->redirect('employe/member/memberList', 'system_message_error',lang('MEMBER_ERROR'));
		}

		// check member
		$memberData = $this->db->get_where('users',array('id'=>$id,'account_id'=>$account_id))->row_array();

		$accountID=$loggedUser['id'];	
    	$siteUrl = site_url();
    	$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'member/viewUpline',
            'manager_description' => lang('SITE_NAME'),
			'accountID'=>$accountID,
			'id'=>$id,
			'memberData' => $memberData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    public function getUplineMemberList()
	{	
		$account_id = $this->User->get_domain_account();
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$date = '';
	   	$memberID = 0;
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$date = isset($filterData[1]) ? trim($filterData[1]) : '';
			$memberID = isset($filterData[2]) ? trim($filterData[2]) : 0;
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3,4,5,6,8) AND a.account_id = '$account_id' AND a.id = '$memberID'";


			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3,4,5,6,8) AND a.account_id = '$account_id' AND a.id = '$memberID'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
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
		$totalBalance = 0;
		$totalDownlineBalance = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];

				$sponserData = $this->User->get_member_sponser_data($list['created_by']);
				
				$str = '<table class="table">';
				$str.='<tr><td><b>User name </b></td><td>'.$list['username'].'</td></tr>';
				$str.='<tr><td><b>Password </b></td><td>'.$list['decode_password'].'</td></tr>';
				$str.='<tr><td><b>Transaction Password </b></td><td>'.$list['decoded_transaction_password'].'</td></tr>';
				$str.='<tr><td><b>Email </b></td><td>'.$list['email'].'</td></tr>';
				$str.='<tr><td><b>Mobile </b></td><td>'.$list['mobile'].'</td></tr>';
				if($list['package_name']){
					$str.='<tr><td><b>Pacakge </b></td><td>'.$list['package_name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Pacakge </b></td><td>N/A</td></tr>';
				}
				$str.='<tr><td><b>Minimum Wallet </b></td><td>'.$list['min_wallet_balance'].'</td></tr>';
				if($sponserData['role_id'] == 2)
				{
					$str.='<tr><td><b>Sponser </b></td><td>'.$sponserData['name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Sponser </b></td><td><a href="'.base_url('employe/member/viewUpline/'.$sponserData['id']).'">'.$sponserData['name'].' ('.$sponserData['user_code'].')</a></td></tr>';
				}
				$str.='</table>';
				$nestedData[] = $str;

				$nestedData[]='&#8377; '.number_format($this->User->getMemberWalletBalanceSP($list['id']),2);
				$downline_balance = $list['aeps_wallet_balance'];


				/*$downline_balance = 0;
				if($downline_id)
				{
					$get_downline_balance = $this->db->select('SUM(wallet_balance) as total_balance')->where_in('id',$downline_id)->get_where('users',array('account_id'=>$account_id))->row_array();
					$downline_balance = isset($get_downline_balance['total_balance']) ? $get_downline_balance['total_balance'] : 0 ;
				}*/

				$downline_id = $this->User->get_member_downline_id($list['id']);
				if(count($downline_id)){
					$nestedData[]='<a href="'.base_url('employe/member/viewDownline/'.$list['id']).'" title="View Downline">'.count($downline_id).'</a>';
				}
				else
				{
					$nestedData[]='<a href="#" title="View Downline">'.count($downline_id).'</a>';
				}

				$nestedData[] = date('d-M-Y',strtotime($list['created']));
				if($list['is_active'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['is_active'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/member/editMember').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a>';
				
				$data[] = $nestedData;

				$totalBalance+=$list['wallet_balance'];
				$totalDownlineBalance+=$downline_balance;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,
					"totalBalance" => '&#8377; '.number_format($totalBalance,2),
					"totalDownlineBalance" => '&#8377; '.number_format($totalDownlineBalance,2),
					);

		echo json_encode($json_data);  // send data as json format
	}

	public function instantLoan(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$userList = $this->db->where_in('role_id',array(3,4,5,6,8))->get_where('users',array('account_id'=>$account_id))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'member/intant-loan-url',
            'userList' => $userList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	

	}

	public function getIntantLoanData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id > 0)
	 	{
	 		$chk_member = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$is_error = 1;
	 			
	 		}
	 	}

	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Member is not valid.'
	 			);
	 	}
	 	else
	 	{
	 		$serviceData = $this->db->get_where('account_instant_loan',array('account_id'=>$account_id,'member_id'=>$member_id))->row_array();
	 		$text_url = isset($serviceData['text_url']) ? $serviceData['text_url'] : '';

			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Text/URL</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			$i=1;
	    	$str.='<tr>';
        	$str.='<td>'.$i.'</td>';
        	$str.='<td><textarea class="form-control" placeholder="Enter your Text/URL" name="text_url">'.$text_url.'</textarea></td>';
        	$str.='</tr>';
        	$i++;
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Text/URL</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	public function saveInstantLoanQr(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	

 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$msg = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Please select member.</div>';
	 		$this->Az->redirect('employe/member/instantLoan', 'system_message_error',$msg);
	 	}
 	 	if($memberID > 0)
	 	{
	 		$chk_member = $this->db->get_where('users',array('id'=>$memberID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$msg = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Member is not valid or exits.</div>';
	 			$this->Az->redirect('employe/member/instantLoan', 'system_message_error',$msg);
	 			
	 		}
	 	}

	 	$chkData = $this->db->get_where('account_instant_loan',array('account_id'=>$account_id,'member_id'=>$memberID))->num_rows();
 	 		if($chkData)
 	 		{
 	 			$data = array(
			  	 'text_url' => $post['text_url'],
			  	 'updated' => date('Y-m-d H:i:s'), 
		 	  	 'updated_by' => $loggedUser['id']
		 	  	);
		 	  	$this->db->where('account_id',$account_id);
		 	  	$this->db->where('member_id',$memberID);
				$this->db->update('account_instant_loan',$data);
 	 		}
 	 		else
 	 		{
		 	 	$data = array(
			  	 'account_id' => $account_id,
			  	 'member_id' => $memberID,
			  	 'text_url' => $post['text_url'],
			  	 'created' => date('Y-m-d H:i:s'), 
		 	  	 'created_by' => $loggedUser['id']
		 	  	);
				$this->db->insert('account_instant_loan',$data);
	 	  	}
	 	  	
			

		 	$this->Az->redirect('employe/member/instantLoan', 'system_message_error',lang('CONTENT_SAVED'));

	}
	
	public function memberBeneficiaryList(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		
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
            'content_block' => 'member/memberBeneficiaryList'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getMemberBeneficiaryList()
	{	
		$account_id = $this->User->get_domain_account();
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
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3,4,5,6,8) AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (3,4,5,6,8) AND a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
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
		$totalBalance = 0;
		$totalDownlineBalance = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];

				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/member/viewBeneficiary').'/'.$list['id'].'"> View Beneficiary</a>';
				
			
				$data[] = $nestedData;

				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data					
					);

		echo json_encode($json_data);  // send data as json format
	}




	public function viewBeneficiary($id = ''){

		//get logged user info
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		

        $chk_user = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'user_id'=>$id))->num_rows();
        
      
        if($chk_user < 1)

        {
        	$this->Az->redirect('employe/member/memberBeneficiaryList', 'system_message_error',lang('MEMBER_ERROR'));
        }
        
        $account_list = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'user_id'=>$id))->result_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'id'=>$id,
			'account_list' =>$account_list,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'member/beneficiary-list'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getAccountBeneficiaryList()
	{	
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.name,b.user_code FROM tbl_new_payout_beneficiary as a INNER JOIN tbl_users as b ON b.id = a.user_id where  a.account_id = '$account_id'";
			
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name,b.user_code FROM tbl_new_payout_beneficiary as a INNER JOIN tbl_users as b ON b.id = a.user_id where  a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR b.account_holder_name LIKE '".$keyword."%' ";
				$sql.=" OR b.account_number LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
			echo $sql;
			die;
			//print_r($sql);die;
			
		
		$data = array();
		$totalrecord = 0;
		$totalBalance = 0;
		$totalDownlineBalance = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
					
					$get_bank_name = $this->db->get_where('new_payout_bank_list',array('id'=>$list['bank_id']))->row_array();

					$bank_name = $get_bank_name['bank_name'];
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['account_holder_name'];
				$nestedData[] = $list['account_number'];
				$nestedData[] = $bank_name;
				$nestedData[] = $list['ifsc'];				
				$data[] = $nestedData;

			$i++;}
		}

		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data					
					);

		echo json_encode($json_data);  // send data as json format
	}
	
	    
	    
	    
	public function deleteAccount($id)
	{	
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$chk_user = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'bene_id'=>$id))->num_rows();
        
      
        if($chk_user < 1)

        {
        	$this->Az->redirect('employe/member/memberBeneficiaryList', 'system_message_error',lang('MEMBER_ERROR'));
        }
		
		$this->db->where('account_id',$account_id);
		$this->db->where('bene_id',$id);
		$this->db->delete('new_payout_beneficiary');
		
		$this->Az->redirect('employe/member/memberBeneficiaryList', 'system_message_error',lang('DELETE_SUCCESS'));
	}
	
	
	
}