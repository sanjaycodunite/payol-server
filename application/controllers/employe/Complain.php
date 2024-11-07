<?php 
class Complain extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkEmployePermission();
        $this->lang->load('employe/ticket', 'english');
        
    }

    public function index(){

    	if(!$this->User->admin_menu_permission(25,1)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$user_ip_address = $this->User->get_user_ip();

	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin View Complain Page.]'.PHP_EOL;
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
            'content_block' => 'complain/list'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function getComplainList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.id',	
		);
		
		
		
			// getting total number records without any search
			
			$sql = "SELECT a.*, b.recharge_display_id, b.mobile, b.status as recharge_status, b.amount, b.force_status, e.recharge_display_id as bbps_display_id, e.status as bbps_status, e.mobile as bbps_mobile, e.amount as bbps_amount, c.title as status_title, d.title as complain_type_title,f.name as member_name,f.user_code FROM tbl_complain as a LEFT JOIN tbl_recharge_history as b ON b.id = a.record_id LEFT JOIN tbl_bbps_history as e ON e.id = a.record_id INNER JOIN tbl_complain_status as c ON c.id = a.status INNER JOIN tbl_complain_type as d ON d.id = a.complain_type INNER JOIN tbl_users as f ON f.id = a.member_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.recharge_display_id, b.mobile, b.status as recharge_status, b.amount, b.force_status, e.recharge_display_id as bbps_display_id, e.status as bbps_status, e.mobile as bbps_mobile, e.amount as bbps_amount, e.force_status as bbps_force_status, c.title as status_title, d.title as complain_type_title,f.name as member_name,f.user_code FROM tbl_complain as a LEFT JOIN tbl_recharge_history as b ON b.id = a.record_id LEFT JOIN tbl_bbps_history as e ON e.id = a.record_id INNER JOIN tbl_complain_status as c ON c.id = a.status INNER JOIN tbl_complain_type as d ON d.id = a.complain_type INNER JOIN tbl_users as f ON f.id = a.member_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.complain_id LIKE '".$keyword."%' ";    
				$sql.=" OR b.recharge_display_id LIKE '".$keyword."%' ";
				$sql.=" OR a.description LIKE '".$keyword."%' )";
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
				$nestedData[] = $list['member_name'].'<br />'.$list['user_code'];
				$nestedData[] = $list['complain_type_title'];
				$nestedData[] = $list['complain_id'];
				if($list['complain_type'] == 1)
				{
					$nestedData[] = $list['recharge_display_id'];
					$nestedData[] = $list['mobile'];
					$nestedData[] = $list['amount'];
				}
				else
				{
					$nestedData[] = $list['bbps_display_id'];
					$nestedData[] = $list['bbps_mobile'];
					$nestedData[] = $list['bbps_amount'];
				}
				$nestedData[] = $list['description'];
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));

				
				
				if($list['status'] == 1) {
					
					
					if($list['complain_type'] == 1){

						if($list['force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						elseif($list['force_status'] == 2)
						{
							$nestedData[] = '<font color="green">Success</font>';
						}
						else
						{	
							if($list['recharge_status'] == 1){

								$nestedData[] = '<font color="orange">Pending</font>';
							}
							elseif($list['recharge_status'] == 2){

								$nestedData[] = '<font color="green">Success</font>';
							}
							elseif($list['recharge_status'] == 3){

								$nestedData[] = '<font color="red">Failed</font>';
							}
							elseif($list['recharge_status'] == 4){

								$nestedData[] = '<font color="green">Refund</font>';
							}
						}
					}
					elseif($list['complain_type'] == 5){

						if($list['bbps_force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						elseif($list['bbps_force_status'] == 2)
						{
							$nestedData[] = '<font color="green">Success</font>';
						}
						else
						{
							if($list['bbps_status'] == 1){

								$nestedData[] = '<font color="orange">Pending</font>';
							}
							elseif($list['bbps_status'] == 2){

								$nestedData[] = '<font color="green">Success</font>';
							}
							elseif($list['bbps_status'] == 3){

								$nestedData[] = '<font color="red">Failed</font>';
							}
							elseif($list['bbps_status'] == 4){

								$nestedData[] = '<font color="green">Refund</font>';
							}
						}
					}
					else{

						$nestedData[] = '<font color="black">'.$list['status_title'].'</font>';	
					}
					$nestedData[] = '<a href="'.base_url('employe/complain/resolveComplain').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to resolve this complain?\')" class="btn btn-sm btn-success">Resolve</a> <a href="'.base_url('employe/complain/closeComplain').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to close this complain?\')" class="btn btn-sm btn-danger">Close</a>';
				}
				elseif($list['status'] == 2) {
					
					if($list['complain_type'] == 1){

						if($list['force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						elseif($list['force_status'] == 2)
						{
							$nestedData[] = '<font color="green">Success</font>';
						}
						else
						{
							if($list['recharge_status'] == 1){

								$nestedData[] = '<font color="orange">Pending</font>';
							}
							elseif($list['recharge_status'] == 2){

								$nestedData[] = '<font color="green">Success</font>';
							}
							elseif($list['recharge_status'] == 3){

								$nestedData[] = '<font color="red">Failed</font>';
							}
							elseif($list['recharge_status'] == 4){

								$nestedData[] = '<font color="green">Refund</font>';
							}
						}
					}
					elseif($list['complain_type'] == 5){

						if($list['bbps_force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						elseif($list['bbps_force_status'] == 2)
						{
							$nestedData[] = '<font color="green">Success</font>';
						}
						else
						{
							if($list['bbps_status'] == 1){

								$nestedData[] = '<font color="orange">Pending</font>';
							}
							elseif($list['bbps_status'] == 2){

								$nestedData[] = '<font color="green">Success</font>';
							}
							elseif($list['bbps_status'] == 3){

								$nestedData[] = '<font color="red">Failed</font>';
							}
							elseif($list['bbps_status'] == 4){

								$nestedData[] = '<font color="green">Refund</font>';
							}
						}
					}
					else{

						$nestedData[] = '<font color="green">'.$list['status_title'].'</font>';
					}
					$nestedData[] = '<font color="green">Resolved</font>';

				}
				elseif($list['status'] == 3) {
					
					if($list['complain_type'] == 1){

						if($list['force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						elseif($list['force_status'] == 2)
						{
							$nestedData[] = '<font color="green">Success</font>';
						}
						else
						{
							if($list['recharge_status'] == 1){

								$nestedData[] = '<font color="orange">Pending</font>';
							}
							elseif($list['recharge_status'] == 2){

								$nestedData[] = '<font color="green">Success</font>';
							}
							elseif($list['recharge_status'] == 3){

								$nestedData[] = '<font color="red">Failed</font>';
							}
							elseif($list['recharge_status'] == 4){

								$nestedData[] = '<font color="green">Refund</font>';
							}
						}
					}
					elseif($list['complain_type'] == 5){

						if($list['bbps_force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						elseif($list['bbps_force_status'] == 2)
						{
							$nestedData[] = '<font color="green">Success</font>';
						}
						else
						{
							if($list['bbps_status'] == 1){

								$nestedData[] = '<font color="orange">Pending</font>';
							}
							elseif($list['bbps_status'] == 2){

								$nestedData[] = '<font color="green">Success</font>';
							}
							elseif($list['bbps_status'] == 3){

								$nestedData[] = '<font color="red">Failed</font>';
							}
							elseif($list['bbps_status'] == 4){

								$nestedData[] = '<font color="green">Refund</font>';
							}
						}
					}
					else{

						$nestedData[] = '<font color="red">'.$list['status_title'].'</font>';
					}
					$nestedData[] = '<font color="red">Close</font>';

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

	public function resolveComplain($record_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		$user_ip_address = $this->User->get_user_ip();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Resolve Complain Page - Complain ID - '.$record_id.'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
		// check member
		$chkMember = $this->db->get_where('complain',array('id'=>$record_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Resolve Complain Page - Complain ID - '.$record_id.' not associated with this account redirect back to complain page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('employe/complain', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// update status
		$this->db->where('id',$record_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('complain',array('status'=>2));

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Resolve Complain Page - Complain ID - '.$record_id.' resolved redirect back to complain page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

		$this->Az->redirect('employe/complain', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Complain resolved successfully.</div>');
	}

	public function closeComplain($record_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		$user_ip_address = $this->User->get_user_ip();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Close Complain Page - Complain ID - '.$record_id.'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
		// check member
		$chkMember = $this->db->get_where('complain',array('id'=>$record_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Close Complain Page - Complain ID - '.$record_id.' not associated with this account redirect back to complain page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('employe/complain', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// update status
		$this->db->where('id',$record_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('complain',array('status'=>3));

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Employe Close Complain Page - Complain ID - '.$record_id.' closed redirect back to complain page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

		$this->Az->redirect('employe/complain', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Complain closed successfully.</div>');
	}

	
	
	
}