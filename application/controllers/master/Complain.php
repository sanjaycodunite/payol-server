<?php 
class Complain extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
        $this->load->model('master/Complain_model');		
        $this->lang->load('master/ticket', 'english');
        
    }

    public function index(){

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
            'content_block' => 'complain/list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getComplainList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
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
			$sql = "SELECT a.*, b.recharge_display_id, b.mobile, b.amount, e.recharge_display_id as bbps_display_id, e.mobile as bbps_mobile, e.amount as bbps_amount, c.title as status_title, d.title as complain_type_title FROM tbl_complain as a LEFT JOIN tbl_recharge_history as b ON b.id = a.record_id LEFT JOIN tbl_bbps_history as e ON e.id = a.record_id INNER JOIN tbl_complain_status as c ON c.id = a.status INNER JOIN tbl_complain_type as d ON d.id = a.complain_type where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.recharge_display_id, b.mobile, b.amount, e.recharge_display_id as bbps_display_id, e.mobile as bbps_mobile, e.amount as bbps_amount, c.title as status_title, d.title as complain_type_title FROM tbl_complain as a LEFT JOIN tbl_recharge_history as b ON b.id = a.record_id LEFT JOIN tbl_bbps_history as e ON e.id = a.record_id INNER JOIN tbl_complain_status as c ON c.id = a.status INNER JOIN tbl_complain_type as d ON d.id = a.complain_type where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
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
					$nestedData[] = '<font color="black">'.$list['status_title'].'</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">'.$list['status_title'].'</font>';

				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">'.$list['status_title'].'</font>';

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

	
	
	
}