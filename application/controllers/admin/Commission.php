<?php 
class Commission extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkAdminPermission();
        $this->load->model('admin/Ewallet_model');		
        $this->lang->load('admin/ewallet', 'english');
        
    }

	public function walletList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$user_ip_address = $this->User->get_user_ip();

	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Commission Wallet List Page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
		
		// get users list
		$memberList = $this->db->where_in('role_id',array(6))->get_where('users',array('account_id'=>$account_id))->result_array();

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
            'content_block' => 'commission/walletList'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getWalletList()
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
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'id',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_aeps_comm as a INNER JOIN tbl_users as b ON b.id = a.member_id  where b.role_id = 2 AND b.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_aeps_comm as a INNER JOIN tbl_users as b ON b.id = a.member_id  where b.role_id = 2 AND b.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.txnID LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['user_code'];
				$nestedData[] = $list['name'];
				$nestedData[] = $list['txnID'];
				if($list['type'] == 4)
				{
					$nestedData[] = 'Cash Deposite';
				}
				elseif($list['type'] == 2)
				{
					$nestedData[] = 'Mini Statement';
				}
				elseif($list['type'] == 1)
				{
					$nestedData[] = 'Withdrawal';
				}
				elseif($list['type'] == 3)
				{
					$nestedData[] = 'Aadhar Pay';
				}
				elseif($list['type'] == 5)
				{
					$nestedData[] = 'MATM';
				}
				elseif($list['type'] == 6)
				{
					$nestedData[] = 'Recharge';
				}
				elseif($list['type'] == 7)
				{
					$nestedData[] = 'BBPS';
				}
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = '&#8377; '.$list['com_amount'];
				if($list['is_surcharge'] == 1)
				{
					$nestedData[] = '<font color="red">DR</font>';
				}
				else
				{
					$nestedData[] = '<font color="green">CR</font>';
				}
				if($list['is_paid'] == 1)
				{
					$nestedData[] = '<font color="green">Yes</font>';
				}
				else
				{
					$nestedData[] = '<font color="red">No</font>';
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