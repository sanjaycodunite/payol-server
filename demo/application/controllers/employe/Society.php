<?php 
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

class Society extends CI_Controller {   



    public function __construct() 
    {
		parent::__construct();
		$this->User->checkEmployePermission();
		$this->load->model('employe/Society_model');	
		$this->lang->load('employe/society', 'english');
		
    }				

	
    public function index()
	{

		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'society/list',
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('employe/layout/column-1', $data);
	}

	public function getClubList()
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
			1 => 'id',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.title as tenureType FROM tbl_club_list as a INNER JOIN tbl_club_duration_type as b ON b.id = a.tenure_type where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.title as tenureType FROM tbl_club_list as a INNER JOIN tbl_club_duration_type as b ON b.id = a.tenure_type where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.club_name LIKE '".$keyword."%' ";   
				$sql.=" OR a.member_limit LIKE '%".$keyword."%'";
				$sql.=" OR b.title LIKE '".$keyword."%' )";
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
				$nestedData[] = "<a href='".base_url('employe/society/clubDetail/'.$list['id'])."' style='text-decoration:none;'>".$list['club_name']."</a>";
				$nestedData[] = $list['member_limit'];
				$nestedData[] = $list['total_amount'].' /-';
				$nestedData[] = $list['per_member_amount'].' /-';
				if($list['is_flat'])
				{
					$nestedData[] = $list['commission'].' /-';
				}
				else
				{
					$nestedData[] = $list['commission'].'%';
				}

				$nestedData[] = $list['tenureType'];
				$nestedData[] = $list['min_bid_amount'].' /-';
				$nestedData[] = date('d-M-Y',strtotime($list['start_date']));
				$nestedData[] = $list['state_time'];
				$nestedData[] = $list['reserve_no'];

				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['status'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="red">Close</font>';
				}

				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));

				
				$nestedData[] = '<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('employe/society/editClub').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/society/deleteClub/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a>';

				
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

	public function addClub()
	{

		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$tenureType = $this->db->get('club_duration_type')->result_array();

		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'society/addClub',
			'tenureType' => $tenureType,
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('employe/layout/column-1', $data);
	}



	public function saveClub()
	{
		$domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('club_name', 'Club Name', 'required');
		$this->form_validation->set_rules('member_limit', 'Member Limit ', 'required|xss_clean');
		$this->form_validation->set_rules('total_amount', 'Total Amount ', 'required|xss_clean');
		$this->form_validation->set_rules('commission', 'Commission ', 'required|xss_clean');
		$this->form_validation->set_rules('tenure_type', 'Tenure ', 'required|xss_clean');
		$this->form_validation->set_rules('min_bid_amount', 'Min Bid Amount ', 'required|xss_clean');
		$this->form_validation->set_rules('diff_amount', 'Diff Bid Amount ', 'required|xss_clean');
		$this->form_validation->set_rules('start_date', 'Start Date ', 'required|xss_clean');
		$this->form_validation->set_rules('reserve_no', 'Reserve No ', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {

			$this->addClub();

		}

		else{

			
			$this->Society_model->saveClub($post);

			$this->Az->redirect('employe/society', 'system_message_error',lang('CLUB_SAVE_SUCCESS'));
		}
	}


	public function editClub($club_id = 0)
	{

		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$tenureType = $this->db->get('club_duration_type')->result_array();

		$clubData = $this->db->get_where('club_list',array('id'=>$club_id))->row_array();

		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'society/editClub',
			'tenureType' => $tenureType,
			'clubData' => $clubData,
			'id' => $club_id,
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('employe/layout/column-1', $data);
	}

	public function updateClub()
	{
		$domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$id = $post['id'];
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('club_name', 'Club Name', 'required');
		$this->form_validation->set_rules('member_limit', 'Member Limit ', 'required|xss_clean');
		$this->form_validation->set_rules('total_amount', 'Total Amount ', 'required|xss_clean');
		$this->form_validation->set_rules('commission', 'Commission ', 'required|xss_clean');
		$this->form_validation->set_rules('tenure_type', 'Tenure ', 'required|xss_clean');
		$this->form_validation->set_rules('min_bid_amount', 'Min Bid Amount ', 'required|xss_clean');
		$this->form_validation->set_rules('diff_amount', 'Diff Bid Amount ', 'required|xss_clean');
		$this->form_validation->set_rules('start_date', 'Start Date ', 'required|xss_clean');
		$this->form_validation->set_rules('reserve_no', 'Reserve No ', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {

			$this->editClub($id);

		}

		else{

			
			$this->Society_model->updateClub($post);

			$this->Az->redirect('employe/society', 'system_message_error',lang('CLUB_UPDATED'));
		}
	}

	public function deleteClub($club_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();
		//check for foem validation
		
		$this->db->where('id',$club_id);
		$this->db->delete('club_list');

		$this->db->where('club_id',$club_id);
		$this->db->delete('club_notification');

		$this->db->where('club_id',$club_id);
		$this->db->delete('club_rounds');

		$this->Az->redirect('employe/society', 'system_message_error',lang('CLUB_DELETED'));
		
	}


	public function clubDetail($club_id = 0)
	{

		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$tenureType = $this->db->get('club_duration_type')->result_array();

		$clubData = $this->db->get_where('club_list',array('id'=>$club_id))->row_array();

		$requestList = $this->db->select('club_member_request.*,users.user_code,users.name')->join('users','users.id = club_member_request.member_id')->get_where('club_member_request',array('club_member_request.account_id'=>$account_id,'club_member_request.club_id'=>$club_id))->result_array();

		$roundList = $this->db->select('club_rounds.*,users.name,users.user_code')->join('users','users.id = club_rounds.winner_member_id','left')->get_where('club_rounds',array('club_rounds.account_id'=>$account_id,'club_rounds.club_id'=>$club_id))->result_array();

		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'society/club-detail',
			'tenureType' => $tenureType,
			'clubData' => $clubData,
			'id' => $club_id,
			'requestList' => $requestList,
			'roundList' => $roundList,
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('employe/layout/column-1', $data);
	}

	public function editClubRound($roundID = 0, $club_id = 0)
	{

		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		// check round is valid or not
		$chkRound = $this->db->get_where('club_rounds',array('id'=>$roundID,'club_id'=>$club_id,'status'=>1))->num_rows();
		if(!$chkRound)
		{
			$this->Az->redirect('employe/society', 'system_message_error',lang('MEMBER_ERROR'));
		}
		
		$clubData = $this->db->get_where('club_list',array('id'=>$club_id))->row_array();
		$roundData = $this->db->get_where('club_rounds',array('id'=>$roundID))->row_array();

		
		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'society/editClubRound',
			'clubData' => $clubData,
			'roundData' => $roundData,
			'roundID' => $roundID,
			'club_id' => $club_id,
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('employe/layout/column-1', $data);
	}

	public function updateClubRoundAuth()
	{
		$domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$roundID = $post['roundID'];
		$club_id = $post['club_id'];

		// check round is valid or not
		$chkRound = $this->db->get_where('club_rounds',array('id'=>$roundID,'club_id'=>$club_id,'status'=>1))->num_rows();
		if(!$chkRound)
		{
			$this->Az->redirect('employe/society', 'system_message_error',lang('MEMBER_ERROR'));
		}
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('start_date', 'Start Date ', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {

			$this->editClubRound($roundID,$club_id);

		}

		else{

			
			$this->Society_model->updateClubRound($post);

			$this->Az->redirect('employe/society/clubDetail/'.$club_id, 'system_message_error',lang('ROUND_UPDATED'));
		}
	}

	public function requestAuth($requestID = 0, $club_id = 0, $status = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();
		
		if($status == 1)
		{
			$this->db->where('id',$requestID);
			$this->db->update('club_member_request',array('status'=>2,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedUser['id']));

			// get club total member limit
			$getClubData = $this->db->get_where('club_list',array('id'=>$club_id))->row_array();
			$member_limit = isset($getClubData['member_limit']) ? $getClubData['member_limit'] : 0 ;

			$getClubRequestData = $this->db->select('club_member_request.*,users.name')->join('users','users.id = club_member_request.member_id')->get_where('club_member_request',array('club_member_request.id'=>$requestID))->row_array();
			$member_id = isset($getClubRequestData['member_id']) ? $getClubRequestData['member_id'] : 0 ;
			$memberName = isset($getClubRequestData['name']) ? $getClubRequestData['name'] : '' ;

			if($member_limit)
			{
				$totalApproveRequest = $this->db->get_where('club_member_request',array('club_id'=>$club_id,'status'=>2))->num_rows();
				if($totalApproveRequest == $member_limit)
				{
					$this->db->where('club_id',$club_id);
					$this->db->where('to_member_id',0);
					$this->db->update('club_notification',array('is_read'=>1));
				}
			}

			// save notification
        	$notificationData = array(
        		'account_id' => $domain_account_id,
        		'to_member_id' => $member_id,
        		'msg' => 'Dear '.$memberName.', Congratulations your club request approved.',
        		'created' =>  date('Y-m-d H:i:s'),
        	);
        	$this->db->insert('club_notification',$notificationData);

			$this->Az->redirect('employe/society/clubDetail/'.$club_id, 'system_message_error',lang('REQUEST_APPROVED'));
		}
		elseif($status == 2)
		{
			$this->db->where('id',$requestID);
			$this->db->update('club_member_request',array('status'=>3,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedUser['id']));
			$getClubRequestData = $this->db->select('club_member_request.*,users.name')->join('users','users.id = club_member_request.member_id')->get_where('club_member_request',array('club_member_request.id'=>$requestID))->row_array();
			$member_id = isset($getClubRequestData['member_id']) ? $getClubRequestData['member_id'] : 0 ;
			$memberName = isset($getClubRequestData['name']) ? $getClubRequestData['name'] : '' ;
			// save notification
        	$notificationData = array(
        		'account_id' => $domain_account_id,
        		'to_member_id' => $member_id,
        		'msg' => 'Dear '.$memberName.', Sorry your club request declined.',
        		'created' =>  date('Y-m-d H:i:s'),
        	);
        	$this->db->insert('club_notification',$notificationData);
			$this->Az->redirect('employe/society/clubDetail/'.$club_id, 'system_message_error',lang('REQUEST_DECLINED'));
		}
		elseif($status == 3)
		{
			$this->db->where('id',$requestID);
			$this->db->delete('club_member_request');
			$this->Az->redirect('employe/society/clubDetail/'.$club_id, 'system_message_error',lang('REQUEST_DELETED'));
		}
		$this->Az->redirect('employe/society/clubDetail/'.$club_id, 'system_message_error',lang('MEMBER_ERROR'));

		
		
	}

	public function getClubChatList($round_no = 0, $club_id = 0)
    {
    	$response = array();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		
		$msgList = $this->db->select('club_round_chat.*,users.name as member_name')->order_by('club_round_chat.id','ASC')->join('users','users.id = club_round_chat.member_id','left')->get_where('club_round_chat',array('club_round_chat.account_id'=>$account_id,'club_round_chat.club_id'=>$club_id,'club_round_chat.round_no'=>$round_no))->result_array();
		$str = '';
		if($msgList)
		{
			foreach($msgList as $list)
			{
				if($list['member_id'] == $loggedAccountID && $list['type'] == 1)
				{
					$str.='<div class="right_Area">';
					$str.='<div class="chat_user_right">';
					$str.='<div class="chat_Usertext">'.$list['msg'].'</div>';
					$str.='<div class="chat_Username"><span class="chat_Date">'.date('d M',strtotime($list['created'])).' <span class="time">'.date('h:i:s A',strtotime($list['created'])).'</span></span></div>';
					$str.='</div></div>';
				}
				elseif($list['type'] == 1)
				{
					$str.='<div class="left_Area">';
					$str.='<div class="chat_user_left">';
					$str.='<div class="chat_Username"><span>'.$list['member_name'].'</span><span class="chat_Date">'.date('d M',strtotime($list['created'])).' <span class="time">'.date('h:i:s A',strtotime($list['created'])).'</span></span></div>';
					$str.='<div class="chat_Usertext">'.$list['msg'].'</div>';
					$str.='</div></div>';
				}
				elseif($list['type'] == 2)
				{
					$str.='<div class="center_Area">';
					$str.='<div class="chat_user_right right_bg_2">';
					$str.='<div class="chat_Usertext">'.$list['msg'].'</div>';
					$str.='<div class="chat_Username"><span class="chat_Date">'.date('d M',strtotime($list['created'])).' <span class="time">'.date('h:i:s A',strtotime($list['created'])).'</span></span></div>';
					$str.='</div></div>';
				}
				elseif($list['type'] == 4)
				{
					$str.='<div class="center_Area">';
					$str.='<div class="chat_user_right right_bg_3">';
					$str.='<div class="chat_Usertext">'.$list['msg'].'</div>';
					$str.='<div class="chat_Username"><span class="chat_Date">'.date('d M',strtotime($list['created'])).' <span class="time">'.date('h:i:s A',strtotime($list['created'])).'</span></span></div>';
					$str.='</div></div>';
				}
				elseif($list['type'] == 3)
				{
					$str.='<div class="center_Area">';
					$str.='<div class="Super_timer">';
					$str.='<div class="d-flex">';
					$str.='<div class="chat_bid_icon"><i class="fa fa-clock"></i> </div>';
					$str.='<div class="chat_super_timer"><span>'.$list['msg'].'</span> </div>';
					$str.='</div>';
					$str.='<div class="chat_bid_time"><span class="time">'.date('h:i:s A',strtotime($list['created'])).'</span></div>';
					$str.='</div></div>';
				}
				
			}
		}

		
		$response = array(
			'status' => 1,
			'msg' => 'SUCCESS',
			'str' => $str
		);
			
		

		echo json_encode($response);
    }

    public function payWinAmountAuth($recordID = 0, $club_id = 0)
    {
    	$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		// check round is valid or not
		$chkRound = $this->db->get_where('club_rounds',array('id'=>$recordID,'is_paid'=>0))->num_rows();
		if(!$chkRound)
		{
			$this->Az->redirect('employe/society/clubDetail/'.$club_id, 'system_message_error',lang('ALREADY_PAID'));
		}

		$roundData = $this->db->get_where('club_rounds',array('id'=>$recordID,'is_paid'=>0))->row_array();
		$winner_member_id = $roundData['winner_member_id'];
		$win_amount = $roundData['win_amount'];
		$round_no = $roundData['round_no'];
		if($winner_member_id > 1)
		{
			// get club name
			$getClubName = $this->db->select('club_name')->get_where('club_list',array('id'=>$club_id))->row_array();
			$clubName = isset($getClubName['club_name']) ? $getClubName['club_name'] : '';

			$getMemberName = $this->db->select('name')->get_where('users',array('id'=>$winner_member_id))->row_array();
			$memberName = isset($getMemberName['name']) ? $getMemberName['name'] : '';

			$before_wallet_balance = $this->User->getMemberWalletBalanceSP($winner_member_id);
			
			
			$after_balance = $before_wallet_balance + $win_amount;    
			

            $wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $winner_member_id,    
	            'before_balance'      => $before_wallet_balance,
	            'amount'              => $win_amount,  
	            'after_balance'       => $after_balance,      
	            'status'              => 1,
	            'type'                => 1,      
	            'created'             => date('Y-m-d H:i:s'),      
	            'credited_by'         => $loggedUser['id'],
	            'description'         => 'Club #'.$clubName.' Round #'.$round_no.' Winning Amount Credited.'
            );

            $this->db->insert('member_wallet',$wallet_data);

            $chatData = array(
	            'account_id'          => $account_id,
	            'club_id'             => $club_id,    
	            'round_no'      	  => $round_no,
	            'type'                => 6,  
	            'msg'       		  => 'Dear '.$memberName.', your winning amount credited into your wallet of round #'.$round_no.'.',      
	            'created'             => date('Y-m-d H:i:s')
            );

            $this->db->insert('club_round_chat',$chatData);

            $this->db->where('id',$recordID);
            $this->db->update('club_rounds',array('is_paid'=>1));
		}
		$this->Az->redirect('employe/society/clubDetail/'.$club_id, 'system_message_error',lang('WIN_AMOUNT_PAID'));

    }



}



?>