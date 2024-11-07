<?php 
class Ticket extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkDistributorPermission();
        $this->load->model('distributor/Ticket_model');		
        $this->lang->load('distributor/ticket', 'english');
        
    }

    public function ticketList(){

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
            'content_block' => 'ticket/ticketList'
        );
        $this->parser->parse('distributor/layout/column-1' , $data);
    
	
	}


	public function getTicketList()
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
			0 => 'a.id',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.title as related_to_title, c.title as status_title FROM tbl_ticket as a INNER JOIN tbl_ticket_related as b ON b.id = a.related_to INNER JOIN tbl_ticket_status as c ON c.id = a.status  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.title as related_to_title, c.title as status_title FROM tbl_ticket as a INNER JOIN tbl_ticket_related as b ON b.id = a.related_to INNER JOIN tbl_ticket_status as c ON c.id = a.status  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.ticket_id LIKE '".$keyword."%' ";    
				$sql.=" OR a.subject LIKE '".$keyword."%' )";
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
				$nestedData[] = '<a href="'.base_url('distributor/ticket/viewTicket/'.$list['id']).'">'.$list['ticket_id'].'</a>';
				$nestedData[] = $list['subject'];
				$nestedData[] = $list['related_to_title'];
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['updated']));
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">'.$list['status_title'].'</font>';
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

	
	// add member
	public function create()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	// get related list
	 	$relatedList = $this->db->get('ticket_related')->result_array();
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'ticket/create',
            'relatedList' => $relatedList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('distributor/layout/column-1', $data);
		
    }

    // save member
	public function saveTicketAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('subject', 'Subject', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			
			$this->create();
		}
		else
		{	
			$filePath = '';
			if($_FILES['attachment']['name'])
			{
				//generate icon name randomly
				$fileName = time().rand(1111,9999);
				$config['upload_path'] = './media/ticket/';
				$config['allowed_types'] = 'gif|jpeg|JPEG|JPG|PNG|jpg|png';
				$config['file_name'] 		= $fileName;
				$config['max_size'] 		= 2048;

				$this->load->library('upload', $config);
				$this->upload->do_upload('attachment');
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('distributor/ticket/create', 'system_message_error',$uploadError);
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$filePath = substr($config['upload_path'] . $fileData['file_name'], 2);

				}

			}

			$status = $this->Ticket_model->saveTicket($post,$filePath);
			$this->Az->redirect('distributor/ticket/ticketList', 'system_message_error',lang('TICKET_SAVED'));
			
		}
	
	}
	
	public function viewTicket($ticket_id = 0){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	// check member
		$chkMember = $this->db->get_where('ticket',array('id'=>$ticket_id,'account_id'=>$account_id,'member_id'=>$loggedAccountID))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('distributor/ticket/ticketList', 'system_message_error',lang('MEMBER_ERROR'));
		}

		$sql = "SELECT a.*, b.title as related_to_title, c.title as status_title FROM tbl_ticket as a INNER JOIN tbl_ticket_related as b ON b.id = a.related_to INNER JOIN tbl_ticket_status as c ON c.id = a.status  where a.id = '$ticket_id'";
			
		$ticketData = $this->db->query($sql)->row_array();
		
		// get ticket reply list
		$replyList = $this->db->select('ticket_reply.*,users.name as member_name')->order_by('ticket_reply.created','desc')->join('users','users.id = ticket_reply.created_by')->get_where('ticket_reply',array('ticket_reply.account_id'=>$account_id,'ticket_reply.ticket_id'=>$ticket_id))->result_array();

		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'ticketData' => $ticketData,
			'replyList' => $replyList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'ticket/ticketDetail'
        );
        $this->parser->parse('distributor/layout/column-1' , $data);
    
	
	}

	// save member
	public function ticketResponseAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->input->post();
		$ticket_id = $post['ticket_id'];
		// check member
		$chkMember = $this->db->get_where('ticket',array('id'=>$ticket_id,'account_id'=>$account_id,'member_id'=>$loggedAccountID))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('distributor/ticket/ticketList', 'system_message_error',lang('MEMBER_ERROR'));
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('message', 'Message', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			
			$this->viewTicket($ticket_id);
		}
		else
		{	
			$filePath = '';
			if($_FILES['attachment']['name'])
			{
				//generate icon name randomly
				$fileName = time().rand(1111,9999);
				$config['upload_path'] = './media/ticket/';
				$config['allowed_types'] = 'gif|jpeg|JPEG|JPG|PNG|jpg|png';
				$config['file_name'] 		= $fileName;
				$config['max_size'] 		= 2048;

				$this->load->library('upload', $config);
				$this->upload->do_upload('attachment');
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('distributor/ticket/viewTicket/'.$ticket_id, 'system_message_error',$uploadError);
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$filePath = substr($config['upload_path'] . $fileData['file_name'], 2);

				}

			}

			$status = $this->Ticket_model->saveTicketResponse($post,$filePath);
			$this->Az->redirect('distributor/ticket/viewTicket/'.$ticket_id, 'system_message_error',lang('TICKET_REPLY_SAVED'));
			
		}
	
	}


	
	
}