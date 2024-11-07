<?php 
class Upi extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkAdminPermission();
        $this->lang->load('admin/wallet', 'english');
        $this->load->model('admin/Wallet_model');		
        
    }

    public function collection(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$totalQr = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id))->num_rows();

	 	$totalMapQr = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'is_map'=>1))->num_rows();

		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'totalQr' => $totalQr,
			'totalMapQr' => $totalMapQr,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'upi/collection'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function generateQr(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

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
            'content_block' => 'upi/generate-qr'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	// save member
	public function generateQrAuth()
	{
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		$admin_id = $loggedUser['id'];
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('qr_count', 'QR No. ', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->generateQr();
		}
		else
		{	
			if($post['qr_count'] < 1)
			{
				$this->Az->redirect('admin/upi/generateQr', 'system_message_error',lang('QR_NO_ERROR'));
			}
			
			$response = $this->Wallet_model->upiGenerateStaticQr($account_id,$admin_id,$post['qr_count']);
			
			if($response['status'] == 1)
			{
				$this->Az->redirect('admin/upi/collection', 'system_message_error',lang('QR_SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('admin/upi/generateQr', 'system_message_error',sprintf(lang('QR_ERROR'),$response['message']));
			}
			
		}
	
	}

	public function qrList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
            'content_block' => 'upi/qrList'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getQRList()
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
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_upi_collection_qr as a LEFT JOIN tbl_users as b ON b.id = a.map_member_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_upi_collection_qr as a LEFT JOIN tbl_users as b ON b.id = a.map_member_id where a.account_id = '$account_id'";
			
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
				$nestedData[] = $list['txnid'].'.';
				$nestedData[] = '<a href="'.base_url('admin/upi/viewQr/'.$list['txnid']).'" target="_blank">View QR</a>';
				$nestedData[] = $list['ref_id'];
				$nestedData[] = $list['qr_str'];
				if($list['is_map'] == 1) {
					$nestedData[] = '<font color="green">Yes</font>';
				}
				else{
					$nestedData[] = '<font color="red">No</font>';
				}
				if($list['is_map'] == 1) {
					$nestedData[] = $list['user_code'].'<br />'.$list['name'];
				}
				else{
					$nestedData[] = 'Not Mapped';
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

	public function viewQr($txnid = ''){

		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$chk_txnid = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->num_rows();
	 	if(!$chk_txnid)
	 	{
			$this->Az->redirect('admin/upi/qrList', 'system_message_error',lang('WALLET_ERROR'));
	 	}

	 	$get_qr_img = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->row_array();
	 	$qr_image = $get_qr_img['qr_image'];

	 	
		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'qr' => $qr_image,
			'accountData' => $accountData,
			'txnid' => $txnid,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'upi/view-qr'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function cash(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$totalQr = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id))->num_rows();

	 	$totalMapQr = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'is_map'=>1))->num_rows();

		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'totalQr' => $totalQr,
			'totalMapQr' => $totalMapQr,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'upi/cash'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function generateCashQr(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

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
            'content_block' => 'upi/generate-cash-qr'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	// save member
	public function generateCashQrAuth()
	{
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		$admin_id = $loggedUser['id'];
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('qr_count', 'QR No. ', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->generateCashQr();
		}
		else
		{	
			if($post['qr_count'] < 1)
			{
				$this->Az->redirect('admin/upi/generateCashQr', 'system_message_error',lang('QR_NO_ERROR'));
			}
			
			$response = $this->Wallet_model->upiCashGenerateStaticQr($account_id,$admin_id,$post['qr_count']);
			
			if($response['status'] == 1)
			{
				$this->Az->redirect('admin/upi/cash', 'system_message_error',lang('QR_SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('admin/upi/generateCashQr', 'system_message_error',sprintf(lang('QR_ERROR'),$response['message']));
			}
			
		}
	
	}

	public function cashQrList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
            'content_block' => 'upi/cash-qr-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getCashQRList()
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
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_upi_cash_qr as a LEFT JOIN tbl_users as b ON b.id = a.map_member_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_upi_cash_qr as a LEFT JOIN tbl_users as b ON b.id = a.map_member_id where a.account_id = '$account_id'";
			
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
				$nestedData[] = $list['txnid'].'.';
				$nestedData[] = '<a href="'.base_url('admin/upi/viewCashQr/'.$list['txnid']).'" target="_blank">View QR</a>';
				$nestedData[] = $list['ref_id'];
				$nestedData[] = $list['qr_str'];
				if($list['is_map'] == 1) {
					$nestedData[] = '<font color="green">Yes</font>';
				}
				else{
					$nestedData[] = '<font color="red">No</font>';
				}
				if($list['is_map'] == 1) {
					$nestedData[] = $list['user_code'].'<br />'.$list['name'];
				}
				else{
					$nestedData[] = 'Not Mapped';
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

	public function viewCashQr($txnid = ''){

		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$chk_txnid = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->num_rows();
	 	if(!$chk_txnid)
	 	{
			$this->Az->redirect('admin/upi/cashQrList', 'system_message_error',lang('WALLET_ERROR'));
	 	}

	 	$get_qr_img = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->row_array();
	 	$qr_image = $get_qr_img['qr_image'];

	 	
		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'qr' => $qr_image,
			'accountData' => $accountData,
			'txnid' => $txnid,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'upi/view-cash-qr'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	
	
}