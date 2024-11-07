<?php 
class Saving extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkRetailerPermission();
        $this->load->model('retailer/Saving_model');		
        $this->lang->load('retailer/saving', 'english');

        $adminActiveService = $this->User->admin_active_service();
		if(!in_array(24, $adminActiveService)){
			$this->Az->redirect('retailer/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
        
    }

	
	public function clubList()
    {
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];

		$getUserCreated = $this->db->select('created')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$userCreated = isset($getUserCreated['created']) ? $getUserCreated['created'] : 0 ;
        
        $clubList = $this->db->order_by('id','desc')->where_in('status',array(1))->get_where('club_list',array('account_id'=>$account_id,'created >='=>$userCreated))->result_array();

        if($clubList)
        {
        	foreach($clubList as $key=>$list)
        	{
        		// get member action
        		$getMemberAction = $this->db->get_where('club_member_request',array('account_id'=>$account_id,'member_id'=>$loggedUser['id'],'club_id'=>$list['id']))->row_array();
        		$clubList[$key]['member_action'] = isset($getMemberAction['action_type']) ? $getMemberAction['action_type'] : 0;
        		$member_status = isset($getMemberAction['status']) ? $getMemberAction['status'] : 0;
        		$clubList[$key]['member_status'] = $member_status;
        		$clubList[$key]['requestID'] = isset($getMemberAction['id']) ? $getMemberAction['id'] : 0;

        		$totalApproveRequest = $this->db->get_where('club_member_request',array('club_id'=>$list['id'],'status'=>2))->num_rows();
        		$isClubFull = 0;
        		if($totalApproveRequest == $list['member_limit'])
				{
					$isClubFull = 1;
				}
				$clubList[$key]['isClubFull'] = $isClubFull;

        		if($list['status'] == 2 && $member_status != 2)
        		{
        			unset($clubList[$key]);
        		}
        	}
        }

        $this->User->saveMemberLiveStatus($loggedAccountID, 0);

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'saving/club-list',
            'clubList'   => $clubList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('retailer/layout/column-1', $data);
		
    }

    public function getCloseClubList()
	{	
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created'
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.title as tenureType FROM tbl_club_list as a INNER JOIN tbl_club_duration_type as b ON b.id = a.tenure_type where a.account_id = '$account_id' AND a.status = 2 AND a.id IN (SELECT club_id FROM tbl_club_member_request WHERE member_id = '$loggedAccountID' AND status = 2)";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.title as tenureType FROM tbl_club_list as a INNER JOIN tbl_club_duration_type as b ON b.id = a.tenure_type where a.account_id = '$account_id' AND a.status = 2 AND a.id IN (SELECT club_id FROM tbl_club_member_request WHERE member_id = '$loggedAccountID' AND status = 2)";

			
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
				$nestedData[] = "<a href='#' style='text-decoration:none;'>".$list['club_name']."</a>";
				$nestedData[] = $list['total_amount'].' /-';
				$nestedData[] = $list['per_member_amount'].' /-';
				$nestedData[] = $list['tenureType'];
				$nestedData[] = date('d-M-Y',strtotime($list['start_date']));
				$nestedData[] = $list['state_time'];
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['status'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="red">Close</font>';
				}

				$getMemberAction = $this->db->get_where('club_member_request',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'club_id'=>$list['id']))->row_array();
				$requestID = isset($getMemberAction['id']) ? $getMemberAction['id'] : 0 ;
				
				$nestedData[] = '<a href="'.base_url().'retailer/saving/clubLiveAuth/'.$list['id'].'/'.$requestID.'"><button type="button" class="btn btn-success">View LIVE</button></a>';
				
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

    public function clubRequestAuth()
    {
    	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$getUserCreated = $this->db->select('created')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$userCreated = isset($getUserCreated['created']) ? $getUserCreated['created'] : 0 ;

		$post = $this->input->post();
		$club_id = $post['club_id'];
		// club id valid or not
		$chkClub = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status'=>1,'created >='=>$userCreated))->num_rows();
		if(!$chkClub)
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check member already sent request or not
		$chkMemberAction  = $this->db->get_where('club_member_request',array('member_id'=>$loggedAccountID,'account_id'=>$account_id,'club_id'=>$club_id))->num_rows();
		if($chkMemberAction)
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('REQUEST_ALREADY_FOUND'));
		}

		if(!isset($post['is_agree']) && isset($post['accept']))
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('TERMS_ERROR'));
		}

		$this->Saving_model->sendRequest($post);

		if(isset($post['accept']))
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('REQUEST_SENT_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('REQUEST_SENT_DECLINE'));
		}
    }

    public function clubLiveAuth($club_id = 0, $requestID = 0)
    {
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$getUserCreated = $this->db->select('created')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$userCreated = isset($getUserCreated['created']) ? $getUserCreated['created'] : 0 ;
		// club id valid or not
		$chkClub = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status >='=>1,'created >='=>$userCreated))->num_rows();
		if(!$chkClub)
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// club id valid or not
		$chkRequest = $this->db->get_where('club_member_request',array('id'=>$requestID,'member_id'=>$loggedAccountID,'account_id'=>$account_id,'status'=>2))->num_rows();
		if(!$chkRequest)
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$this->User->saveMemberLiveStatus($loggedAccountID, 0);

		$memberList = $this->db->select('users.id,users.name,users.user_code')->join('users','users.id = club_member_request.member_id')->get_where('club_member_request',array('club_member_request.club_id'=>$club_id,'club_member_request.status'=>2))->result_array();

		$clubData = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id))->row_array();

		// get club round
		$clubRoundData = $this->db->order_by('id','ASC')->where_in('status',array(1,2))->get_where('club_rounds',array('club_id'=>$club_id,'account_id'=>$account_id))->row_array();
        
        $clubRoundStatus = array();
		// club round status
		for($i = 1; $i<=$clubData['member_limit']; $i++){
			$getStatus = $this->db->select('club_rounds.*,users.name')->join('users','users.id = club_rounds.winner_member_id','left')->get_where('club_rounds',array('club_rounds.account_id'=>$account_id,'club_rounds.club_id'=>$club_id,'club_rounds.round_no'=>$i))->row_array();
			$clubRoundStatus[$i]['status'] = isset($getStatus['status']) ? $getStatus['status'] : 0 ;
			$clubRoundStatus[$i]['winner_member_id'] = isset($getStatus['winner_member_id']) ? $getStatus['winner_member_id'] : 0 ;
			$clubRoundStatus[$i]['winner_name'] = isset($getStatus['name']) ? $getStatus['name'] : 0 ;
			$clubRoundStatus[$i]['bid_amount'] = isset($getStatus['bid_amount']) ? $getStatus['bid_amount'] : 0 ;
		}

		// get total earning
		$getTotalEarning = $this->db->select('SUM(devided_amount) as totalAmount')->get_where('club_rounds',array('account_id'=>$account_id,'club_id'=>$club_id))->row_array();
		$totalEarning = isset($getTotalEarning['totalAmount']) ? $getTotalEarning['totalAmount'] : 0;

		$totalRoundEarning = $this->db->order_by('round_no','ASC')->get_where('club_rounds',array('account_id'=>$account_id,'club_id'=>$club_id,'status'=>3))->result_array();


		$isDueAmount = $this->db->select('SUM(amount) as totalAmount,round_no')->get_where('club_round_member_payment',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'club_id'=>$club_id,'is_paid'=>0))->row_array();
		$totalAmount = isset($isDueAmount['totalAmount']) ? $isDueAmount['totalAmount'] : 0 ;


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'saving/club-live',
            'clubList'   => $clubList,
            'memberList'   => $memberList,
            'clubData'   => $clubData,
            'clubRoundData'   => $clubRoundData,
            'club_id'   => $club_id,
            'requestID'   => $requestID,
            'clubRoundStatus' => $clubRoundStatus,
            'totalEarning' => $totalEarning,
            'totalRoundEarning' => $totalRoundEarning,
            'totalAmount' => $totalAmount,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('retailer/layout/column-1', $data);
		
    }

    public function clubChatLiveAuth($club_id = 0, $requestID = 0)
    {
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$getUserCreated = $this->db->select('created')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$userCreated = isset($getUserCreated['created']) ? $getUserCreated['created'] : 0 ;
		// club id valid or not
		$chkClub = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status >='=>1,'created >='=>$userCreated))->num_rows();
		if(!$chkClub)
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// club id valid or not
		$chkRequest = $this->db->get_where('club_member_request',array('id'=>$requestID,'member_id'=>$loggedAccountID,'account_id'=>$account_id,'status'=>2))->num_rows();
		if(!$chkRequest)
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$this->User->saveMemberLiveStatus($loggedAccountID, 1, $club_id);

		

		$clubData = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id))->row_array();

		// get club round
		$clubRoundData = $this->db->order_by('id','ASC')->where_in('status',array(1,2))->get_where('club_rounds',array('club_id'=>$club_id,'account_id'=>$account_id))->row_array();

		// get last bid member name
		$getLastMemberName = $this->db->select('users.name')->order_by('club_round_member_bid.id','DESC')->join('users','users.id = club_round_member_bid.member_id')->get_where('club_round_member_bid',array('club_round_member_bid.account_id'=>$account_id,'club_round_member_bid.club_id'=>$club_id,'club_round_member_bid.round_no'=>$clubRoundData['round_no']))->row_array();
		$lastMemberName = isset($getLastMemberName['name']) ? $getLastMemberName['name'] : '';

		// get total bid amount
		$getTotalBidAmount = $this->db->select('SUM(bid_amount) as totalAmount')->get_where('club_round_member_bid',array('account_id'=>$account_id,'club_id'=>$club_id,'round_no'=>$clubRoundData['round_no']))->row_array();
		$totalBidAmount = isset($getTotalBidAmount['totalAmount']) ? $getTotalBidAmount['totalAmount'] : 0;

		// get total super timer no
		$totalSuperTimer = $this->db->get_where('club_round_chat',array('account_id'=>$account_id,'club_id'=>$club_id,'round_no'=>$clubRoundData['round_no'],'type'=>3))->num_rows();
		

		$start_datetime = $clubRoundData['end_datetime'];
		

		

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'saving/club-chat-live',
            'clubList'   => $clubList,
            'clubData'   => $clubData,
            'clubRoundData'   => $clubRoundData,
            'club_id'   => $club_id,
            'requestID'   => $requestID,
            'roundNo'   => $clubRoundData['round_no'],
            'isLive'   => ($clubRoundData['status'] == 2) ? 1 : 0,
            'lastMemberName' => $lastMemberName,
            'totalBidAmount' => $totalBidAmount,
            'start_datetime' => $start_datetime,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('retailer/layout/column-1', $data);
		
    }

    public function clubRoundSuperTimer($club_id = 0, $requestID = 0, $round_no = 0)
    {
    	$response = array();
    	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$getUserCreated = $this->db->select('created')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$userCreated = isset($getUserCreated['created']) ? $getUserCreated['created'] : 0 ;
		$post = $this->input->post();
		// club id valid or not
		$chkClub = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status >='=>1,'created >='=>$userCreated))->num_rows();
		if(!$chkClub)
		{
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! You are not authorized.'
			);
		}
		else
		{
			// club id valid or not
			$chkRequest = $this->db->get_where('club_member_request',array('id'=>$requestID,'member_id'=>$loggedAccountID,'account_id'=>$account_id,'status'=>2))->num_rows();
			if(!$chkRequest)
			{
				$response = array(
					'status' => 0,
					'msg' => 'Sorry ! You are not authorized.'
				);
			}
			else
			{
				// get club round
				$clubRoundData = $this->db->order_by('id','ASC')->get_where('club_rounds',array('round_no'=>$round_no,'account_id'=>$account_id,'club_id'=>$club_id))->row_array();

				$status = isset($clubRoundData['status']) ? $clubRoundData['status'] : 0 ;
				$end_datetime = isset($clubRoundData['end_datetime']) ? $clubRoundData['end_datetime'] : '' ;

				$response = array(
					'status' => $status,
					'end_datetime' => $end_datetime
				);

			}
		}

		echo json_encode($response);
    }

    public function getClubLiveMembers($club_id = 0, $requestID = 0)
    {
    	$response = array();
    	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$getUserCreated = $this->db->select('created')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$userCreated = isset($getUserCreated['created']) ? $getUserCreated['created'] : 0 ;
		// club id valid or not
		$chkClub = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status >='=>1,'created >='=>$userCreated))->num_rows();
		if(!$chkClub)
		{
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! You are not authorized.'
			);
		}
		else
		{
			// club id valid or not
			$chkRequest = $this->db->get_where('club_member_request',array('id'=>$requestID,'member_id'=>$loggedAccountID,'account_id'=>$account_id,'status'=>2))->num_rows();
			if(!$chkRequest)
			{
				$response = array(
					'status' => 0,
					'msg' => 'Sorry ! You are not authorized.'
				);
			}
			else
			{
				$memberList = $this->db->select('users.id,users.name,users.user_code')->join('users','users.id = club_live_member.member_id')->get_where('club_live_member',array('club_live_member.club_id'=>$club_id,'club_live_member.status'=>1))->result_array();

				$str = '<ul>';
				foreach($memberList as $mlist){
					$str.='<li class="user_club_list">';
					$str.='<img class="img-profile rounded-circle" src="'.base_url().'skin/admin/img/user.png">';
					$str.='<div class="club_mamber_details chat_live_user">';
					$str.='<span class="user_online"></span>';
					$str.='<span class="mamber_id">'.$mlist['user_code'].'</span>';
					$str.='<h5>'.$mlist['name'].'</h5> ';
					$str.='</div>';
					$str.='</li>';
				}

				$response = array(
					'status' => 1,
					'msg' => 'SUCCESS',
					'str' => $str
				);
			}
		}

		echo json_encode($response);
    }

    public function clubChatAuth($club_id = 0, $requestID = 0, $round_no = 0)
    {
    	$response = array();
    	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$getUserCreated = $this->db->select('created')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$userCreated = isset($getUserCreated['created']) ? $getUserCreated['created'] : 0 ;
		$post = $this->input->post();

		// club id valid or not
		$chkClub = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status'=>1,'created >='=>$userCreated))->num_rows();
		if(!$chkClub)
		{
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! You are not authorized.'
			);
		}
		else
		{
			// club id valid or not
			$chkRequest = $this->db->get_where('club_member_request',array('id'=>$requestID,'member_id'=>$loggedAccountID,'account_id'=>$account_id,'status'=>2))->num_rows();
			if(!$chkRequest)
			{
				$response = array(
					'status' => 0,
					'msg' => 'Sorry ! You are not authorized.'
				);
			}
			else
			{
				$msgData = array(
					'account_id' => $account_id,
					'club_id' => $club_id,
					'round_no' => $round_no,
					'member_id' => $loggedAccountID,
					'type' => 1,
					'msg' => $post['message'],
					'created' => date('Y-m-d H:i:s')
				);
				$this->db->insert('club_round_chat',$msgData);
				$str = '<div class="right_Area">';
				$str.='<div class="chat_user_right">';
				$str.='<div class="chat_Usertext">'.$post['message'].'</div>';
				$str.='<div class="chat_Username"><span class="chat_Date">'.date('d M').' <span class="time">'.date('h:i:s A').'</span></span></div>';
				$str.='</div></div>';
				$response = array(
					'status' => 1,
					'msg' => 'SUCCESS',
					'str' => $str
				);
			}
		}

		echo json_encode($response);
    }

    public function getClubChatList($club_id = 0, $requestID = 0, $round_no = 0)
    {
    	$response = array();
    	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$post = $this->input->post();
		// club id valid or not
		$chkClub = $this->db->where_in('status',array(1,2))->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id))->num_rows();
		if(!$chkClub)
		{
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! You are not authorized.'
			);
		}
		else
		{
			// club id valid or not
			$chkRequest = $this->db->get_where('club_member_request',array('id'=>$requestID,'member_id'=>$loggedAccountID,'account_id'=>$account_id,'status'=>2))->num_rows();
			if(!$chkRequest)
			{
				$response = array(
					'status' => 0,
					'msg' => 'Sorry ! You are not authorized.'
				);
			}
			else
			{
				$msgList = $this->db->select('club_round_chat.*,users.name as member_name')->order_by('club_round_chat.id','ASC')->join('users','users.id = club_round_chat.member_id','left')->get_where('club_round_chat',array('club_round_chat.account_id'=>$account_id,'club_round_chat.club_id'=>$club_id))->result_array();
				$str = '';
				$lastChatDatetime = '';
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
						elseif($list['type'] == 5)
						{
							$str.='<div class="center_Area">';
							$str.='<div class="chat_user_right right_bg_4">';
							$str.='<div class="chat_Usertext">'.$list['msg'].'</div>';
							$str.='<div class="chat_Username"><span class="chat_Date">'.date('d M',strtotime($list['created'])).' <span class="time">'.date('h:i:s A',strtotime($list['created'])).'</span></span></div>';
							$str.='</div></div>';
						}
						elseif($list['type'] == 6)
						{
							$str.='<div class="center_Area">';
							$str.='<div class="chat_user_right right_bg_5">';
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
						$lastChatDatetime = $list['created'];
					}
				}

				// get last bid member name
				$getLastMemberName = $this->db->select('users.name')->order_by('club_round_member_bid.id','DESC')->join('users','users.id = club_round_member_bid.member_id')->get_where('club_round_member_bid',array('club_round_member_bid.account_id'=>$account_id,'club_round_member_bid.club_id'=>$club_id,'club_round_member_bid.round_no'=>$round_no,'club_round_member_bid.status'=>1))->row_array();
				$lastMemberName = isset($getLastMemberName['name']) ? $getLastMemberName['name'] : '';

				// get total bid amount
				$getTotalBidAmount = $this->db->select('bid_amount as totalAmount')->order_by('club_round_member_bid.id','DESC')->get_where('club_round_member_bid',array('account_id'=>$account_id,'club_id'=>$club_id,'round_no'=>$round_no,'status'=>1))->row_array();
				$totalBidAmount = isset($getTotalBidAmount['totalAmount']) ? $getTotalBidAmount['totalAmount'] : 0;

				// get last round
				$getLastRound = $this->db->select('round_no,end_datetime,status')->order_by('id','ASC')->get_where('club_rounds',array('account_id'=>$account_id,'club_id'=>$club_id,'status !='=>3))->row_array();
				$lastRoundNo = isset($getLastRound['round_no']) ? $getLastRound['round_no'] : 0 ;
				$end_datetime = isset($getLastRound['end_datetime']) ? $getLastRound['end_datetime'] : '' ;
				$newRoundStatus = isset($getLastRound['status']) ? $getLastRound['status'] : 0 ;

				$getReserveNo = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status'=>1))->row_array();
				$reserve_no = isset($getReserveNo['reserve_no']) ? $getReserveNo['reserve_no'] : 0 ;

				$isNewRound = 0;
				if($round_no != $lastRoundNo && $newRoundStatus == 2 && $lastRoundNo != $reserve_no)
				{
					$isNewRound = 1;
				}
				$response = array(
					'status' => 1,
					'msg' => 'SUCCESS',
					'str' => $str,
					'lastMemberName' => $lastMemberName,
					'totalBidAmount' => $totalBidAmount,
					'lastRoundNo' => $lastRoundNo,
					'isNewRound' => $isNewRound,
					'end_datetime' => $end_datetime,
					'lastChatDatetime' => $lastChatDatetime
				);
			}
		}

		echo json_encode($response);
    }

    public function getClubRoundStatus($club_id = 0, $requestID = 0, $round_no = 0)
    {
    	$response = array();
    	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$post = $this->input->post();
		// club id valid or not
		$chkClub = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status >='=>1))->num_rows();
		if(!$chkClub)
		{
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! You are not authorized.'
			);
		}
		else
		{
			// club id valid or not
			$chkRequest = $this->db->get_where('club_member_request',array('id'=>$requestID,'member_id'=>$loggedAccountID,'account_id'=>$account_id,'status'=>2))->num_rows();
			if(!$chkRequest)
			{
				$response = array(
					'status' => 0,
					'msg' => 'Sorry ! You are not authorized.'
				);
			}
			else
			{
				$isLive = $this->db->get_where('club_rounds',array('account_id'=>$account_id,'club_id'=>$club_id,'round_no'=>$round_no,'status'=>2))->num_rows();
				
				$response = array(
					'status' => 1,
					'msg' => 'SUCCESS',
					'isLive' => $isLive
				);
			}
		}

		echo json_encode($response);
    }

    public function clubBidAuth($club_id = 0, $requestID = 0, $round_no = 0)
    {
    	$response = array();
    	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		$getUserCreated = $this->db->select('created')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$userCreated = isset($getUserCreated['created']) ? $getUserCreated['created'] : 0 ;
		$post = $this->input->post();
		// club id valid or not
		$chkClub = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status'=>1,'created >='=>$userCreated))->num_rows();
		if(!$chkClub)
		{
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! You are not authorized.'
			);
		}
		else
		{
			if(!is_numeric($post['message']))
			{
				$response = array(
					'status' => 0,
					'msg' => 'Sorry ! Please enter number value.'
				);
			}
			else
			{
				// club id valid or not
				$chkRequest = $this->db->get_where('club_member_request',array('id'=>$requestID,'member_id'=>$loggedAccountID,'account_id'=>$account_id,'status'=>2))->num_rows();
				if(!$chkRequest)
				{
					$response = array(
						'status' => 0,
						'msg' => 'Sorry ! You are not authorized.'
					);
				}
				else
				{
					$isLive = $this->db->get_where('club_rounds',array('account_id'=>$account_id,'club_id'=>$club_id,'round_no'=>$round_no,'status'=>2))->num_rows();
					if(!$isLive)
					{
						$response = array(
							'status' => 0,
							'msg' => 'Sorry ! You can chat only.'
						);
					}
					else
					{
						// check member already won or not
						$isMemberWon = $this->db->get_where('club_rounds',array('account_id'=>$account_id,'club_id'=>$club_id,'winner_member_id'=>$loggedAccountID))->num_rows();
						if($isMemberWon)
						{
							$response = array(
								'status' => 0,
								'msg' => 'Sorry ! You already won this club, you can chat only now.'
							);
						}
						else
						{
							$clubData = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status'=>1))->row_array();
							$clubName = $clubData['club_name'];
							$min_bid_amount = $clubData['min_bid_amount'];
							$bid_diff_amount = $clubData['bid_diff_amount'];

							// get bid amount
							$getLastBidAmount = $this->db->order_by('id','DESC')->get_where('club_round_member_bid',array('account_id'=>$account_id,'club_id'=>$club_id,'round_no'=>$round_no,'status'=>1))->row_array();
							$isLastBid = isset($getLastBidAmount['bid_amount']) ? $getLastBidAmount['bid_amount'] : 0;
							$lastBid = isset($getLastBidAmount['bid_amount']) ? $getLastBidAmount['bid_amount'] + $bid_diff_amount : $min_bid_amount;
							if($isLastBid && $post['message'] < $lastBid)
							{
								$response = array(
									'status' => 0,
									'msg' => 'Bid amount should be greater than &#8377; '.$lastBid
								);
							}
							elseif(!$isLastBid && $post['message'] < $min_bid_amount)
							{
								$response = array(
									'status' => 0,
									'msg' => 'Bid amount should be greater than &#8377; '.$min_bid_amount
								);
							}
							else
							{

								$msgData = array(
									'account_id' => $account_id,
									'club_id' => $club_id,
									'round_no' => $round_no,
									'member_id' => $loggedAccountID,
									'bid_amount' => $post['message'],
									'status' => 1,
									'created' => date('Y-m-d H:i:s')
								);
								$this->db->insert('club_round_member_bid',$msgData);

								

								$msgData = array(
									'account_id' => $account_id,
									'club_id' => $club_id,
									'round_no' => $round_no,
									'member_id' => $loggedAccountID,
									'type' => 2,
									'msg' => $loggedUser['name'].' bid '.$post['message'].' for '.$clubName,
									'created' => date('Y-m-d H:i:s')
								);
								$this->db->insert('club_round_chat',$msgData);
								
								$response = array(
									'status' => 1,
									'msg' => 'SUCCESS'
								);
							}
						}
					}
				}
			}
		}

		echo json_encode($response);
    }

    public function clearDuePayment($club_id = 0, $requestID = 0)
    {
    	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
		$loggedAccountID = $loggedUser['id'];
		// club id valid or not
		$chkClub = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status >='=>1))->num_rows();
		if(!$chkClub)
		{
			$this->Az->redirect('retailer/saving/clubList', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check is there any due for this club
		$isDueAmount = $this->db->get_where('club_round_member_payment',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'club_id'=>$club_id,'is_paid'=>0))->num_rows();
		if(!$isDueAmount)
		{
			$this->Az->redirect('retailer/saving/clubLiveAuth/'.$club_id.'/'.$requestID, 'system_message_error',lang('NO_CLUB_DUE_AMOUNT'));
		}

		$getClubName = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id,'status >='=>1))->row_array();
		$clubName = isset($getClubName['club_name']) ? $getClubName['club_name'] : '';

		$isDueAmount = $this->db->select('SUM(amount) as totalAmount,round_no')->get_where('club_round_member_payment',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'club_id'=>$club_id,'is_paid'=>0))->row_array();
		$totalAmount = isset($isDueAmount['totalAmount']) ? $isDueAmount['totalAmount'] : 0 ;
		$round_no = isset($isDueAmount['round_no']) ? $isDueAmount['round_no'] : 0 ;
		$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

		if($before_wallet_balance < $totalAmount)
		{
			$this->Az->redirect('retailer/saving/clubLiveAuth/'.$club_id.'/'.$requestID, 'system_message_error',lang('WALLET_BAL_ERROR'));
		}



		$after_balance = $before_wallet_balance - $totalAmount;
		$msg = 'Club #'.$clubName.' Round #'.$round_no.' Amount Debited.';
		$wallet_data = array(
	        'account_id'          => $account_id,
	        'member_id'           => $loggedAccountID,    
	        'before_balance'      => $before_wallet_balance,
	        'amount'              => $totalAmount,  
	        'after_balance'       => $after_balance,      
	        'status'              => 1,
	        'type'                => 2,      
	        'created'             => date('Y-m-d H:i:s'),      
	        'credited_by'         => $loggedUser['id'],
	        'description'         => $msg
        );

        $this->db->insert('member_wallet',$wallet_data);

        $msg = 'Dear '.$loggedUser['name'].', your payment received manually.';
        $chatData = array(
        	'account_id' => $account_id,
        	'club_id' => $club_id,
        	'round_no' => $round_no,
        	'type' => 6,
        	'msg' => $msg,
        	'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('club_round_chat',$chatData);

        $this->db->where('account_id',$account_id);
        $this->db->where('member_id',$loggedAccountID);
        $this->db->where('club_id',$club_id);
        $this->db->where('is_paid',0);
        $this->db->update('club_round_member_payment',array('is_paid'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));

        $this->Az->redirect('retailer/saving/clubLiveAuth/'.$club_id.'/'.$requestID, 'system_message_error',lang('DUE_CLEAR_SUCCESS'));
    }

    public function closeClubNotification($recordID = 0)
    {
    	$this->db->where('id',$recordID);
    	$this->db->update('club_notification',array('is_read'=>1));
    	echo 1;
    }

}