<?php 
class Dmt extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
        $this->load->model('master/Dmt_model');		
        $this->lang->load('master/dmt', 'english');
        $this->load->model('admin/Jwt_model');
        
    }

	public function index(){

		//get logged user info
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
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
            'content_block' => 'dmt/list'
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
			$sql = "SELECT a.* FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID')";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID')";
			
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
				$nestedData[] = $list['account_no'];
				$nestedData[] = $list['ifsc'];
				$nestedData[] = $list['transfer_amount'].' /-';
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


	// add member
	public function transferNow()
    {
    	$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'dmt/login-register',
            'manager_description' => lang('SITE_NAME'),
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    // save member
	public function registerAuth()
	{
		$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		//check for foem validation
		$post = $this->input->post();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Register Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		$this->form_validation->set_rules('dob', 'DOB', 'required|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
		$this->form_validation->set_rules('pin_code', 'Pin Code', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->transferNow();
		}
		else
		{	
			// check mobile no. already registered or not
			$memberData = $this->db->select('mobile')->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$post['mobile'],'status'=>1))->num_rows();
			if($memberData)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Customer Already Registered.]'.PHP_EOL;
		        $this->User->generateDMTLog($log_msg);

				$this->Az->redirect('master/dmt/transferNow', 'system_message_error',lang('DMT_CUSTOMER_ALREADY_REGISTER_ERROR'));
			}

			$response = $this->Dmt_model->memberActivation($post);
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Final API Response - '.json_encode($response).'.]'.PHP_EOL;
	        $this->User->generateDMTLog($log_msg);
            
            
			if($response['status'] == 1)
			{
				$this->Az->redirect('master/dmt/activeOtp/'.$response['token'], 'system_message_error',lang('DMT_CUSTOMER_OTP_SENT'));
			}
			elseif($response['status'] == 2)
			{
				$this->Az->redirect('master/dmt/transferBen/'.$response['mobile'], 'system_message_error',lang('DMT_CUSTOMER_VERIFY_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('master/dmt/transferNow', 'system_message_error',sprintf(lang('DMT_CUSTOMER_REGISTER_ERROR'),$response['message']));
			}
		}
	}

	public function activeOtp($token = '')
    {
    	$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
    
        
		// check token valid or not
		$chk_token = $this->db->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'token'=>$token,'status'=>0))->num_rows();
		if(!$chk_token)
		{
			$this->Az->redirect('master/dmt/transferNow', 'system_message_error',lang('DMT_TOKEN_ERROR'));
		}

		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'dmt/active-otp',
            'manager_description' => lang('SITE_NAME'),
            'token' => $token,
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    public function activeOtpAuth()
	{
		$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		//check for foem validation
		$post = $this->input->post();
		$token = $post['token'];
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT OTP Auth Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);
		// check token valid or not
		$chk_token = $this->db->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'token'=>$token,'status'=>0))->num_rows();
		if(!$chk_token)
		{
			$this->Az->redirect('master/dmt/transferNow', 'system_message_error',lang('DMT_TOKEN_ERROR'));
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('otp_code', 'OTP Code', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			
			$this->activeOtp($token);
		}
		else
		{	
			$response = $this->Dmt_model->memberActivationOtpAuth($post);
			 
			if($response['status'] == 1)
			{
				$this->Az->redirect('master/dmt/transferBen/'.$response['mobile'], 'system_message_error',lang('DMT_CUSTOMER_VERIFY_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('master/dmt/activeOtp/'.$token, 'system_message_error',sprintf(lang('DMT_OTP_ERROR'),$response['message']));
			}
		}
	}

	
    // save member
	public function loginAuth()
	{
		$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		//check for foem validation
		$post = $this->input->post();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Login Auth Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE) {
			
			$this->transferNow();
		}
		else
		{	
			// check mobile no. already registered or not
			$memberData = $this->db->select('mobile')->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$post['mobile'],'status'=>1))->num_rows();
			if(!$memberData)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Login Auth Remitter not found in system.]'.PHP_EOL;
		        $this->User->generateDMTLog($log_msg);
				$response = $this->Dmt_model->memberFetchDetail($post);
				if($response['status'] == 1)
				{
					$this->Az->redirect('master/dmt/transferBen/'.$post['mobile'], 'system_message_error','');
				}
				else
				{
					$this->Az->redirect('master/dmt/transferNow', 'system_message_error',lang('DMT_CUSTOMER_NOT_REGISTER_ERROR'));
				}
			}

			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Login Auth Remitter already found in system.]'.PHP_EOL;
	        $this->User->generateDMTLog($log_msg);

			$this->Az->redirect('master/dmt/transferBen/'.$post['mobile'], 'system_message_error','');

			
		}
	}

	public function transferBen($mobile = '')
    {
    	$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check mobile is registered or not
		$member_dmt_status = $this->db->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$mobile,'status'=>1))->num_rows();
		if(!$member_dmt_status)
		{
			$this->Az->redirect('master/dmt/transferNow', 'system_message_error',lang('WALLET_ERROR'));
		}

		// check mobile is registered or not
		$member_dmt_data = $this->db->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$mobile,'status'=>1))->row_array();

		if($member_dmt_data['dob'] == '' || $member_dmt_data['dob'] == '0000-00-00' || $member_dmt_data['address'] == '' || $member_dmt_data['pin_code'] == '')
		{
			$this->Az->redirect('master/dmt/updateDetail/'.$mobile, 'system_message_error',lang('UPDATE_DETAIL_ERROR'));
		}

		$sql = "SELECT a.* FROM tbl_user_dmt_beneficiary as a where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.register_mobile = '$mobile' AND a.status = 1";
		$beneficiaryList = $this->db->query($sql)->result_array();

	
		$bankList = $this->db->get('paysprint_dmt_bank_list')->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'dmt/transfer-ben',
            'manager_description' => lang('SITE_NAME'),
            'beneficiaryList' => $beneficiaryList,
            'mobile' => $mobile,
            'member_dmt_data' => $member_dmt_data,
            'bankList' => $bankList,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    public function updateDetail($mobile = '')
    {
    	$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check mobile is registered or not
		$member_dmt_status = $this->db->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$mobile,'status'=>1))->num_rows();
		if(!$member_dmt_status)
		{
			$this->Az->redirect('master/dmt/transferNow', 'system_message_error',lang('WALLET_ERROR'));
		}

		// check mobile is registered or not
		$member_dmt_data = $this->db->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$mobile,'status'=>1))->row_array();

		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'dmt/update-detail',
            'manager_description' => lang('SITE_NAME'),
            'mobile' => $mobile,
            'member_dmt_data' => $member_dmt_data,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    public function updatePersonalDetailAuth()
	{
		$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		//check for foem validation
		$post = $this->input->post();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Update Personal Detail Auth Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('dob', 'DOB', 'required|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
		$this->form_validation->set_rules('pin_code', 'Pin Code', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->updateDetail($post['accountMobile']);
		}
		else
		{	
			// save activation data
	        $data = array(
	            'first_name' => $post['first_name'],
	            'last_name' => $post['last_name'],
	            'dob' => $post['dob'],
	            'address' => $post['address'],
	            'pin_code' => $post['pin_code'],
	            'updated' => date('Y-m-d H:i:s'),
	            'updated_by' => $loggedAccountID
	        );
	        $this->db->where('account_id',$account_id);
	        $this->db->where('member_id',$loggedAccountID);
	        $this->db->where('mobile',$post['accountMobile']);
	        $this->db->update('user_paysprint_dmt_activation',$data);
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Update Personal Detail Success.]'.PHP_EOL;
	        $this->User->generateDMTLog($log_msg);
	        
			$this->Az->redirect('master/dmt/transferBen/'.$post['accountMobile'], 'system_message_error',lang('DMT_CUS_UPDATE_SUCCESS'));
			
		}
	}

    public function beneficiaryAuth()
	{
		$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		//check for foem validation
		$post = $this->input->post();

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

		$mobile = $post['accountMobile'];
		// check mobile is registered or not
		$member_dmt_status = $this->db->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$mobile,'status'=>1))->num_rows();
		if(!$member_dmt_status)
		{
			$this->Az->redirect('master/dmt/transferNow', 'system_message_error',lang('WALLET_ERROR'));
		}
		
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
		$this->form_validation->set_rules('ben_mobile', 'Beneficiary Mobile', 'required|xss_clean|min_length[10]');
		$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
		$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
		$this->form_validation->set_rules('bankID', 'Bank', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->transferBen($mobile);
		}
		else
		{	
			$is_admin_surcharge = 0;
			$surcharge_amount = 0;
			$admin_surcharge_amount = 0;
			if(isset($post['verfiy']))
			{
				
				// get dmr surcharge
	            $surcharge_amount = $this->User->get_account_verify_surcharge($amount,$loggedAccountID);
	            // save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
		        $this->User->generateDMTLog($log_msg);
				if(!$surcharge_amount)
				{
					$is_admin_surcharge = 1;
					// get admin data
					$admin_id = $this->User->get_admin_id();
					$admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);
					// get admin dmr surcharge
		            $admin_surcharge_amount = $this->User->get_admin_account_verify_surcharge($amount,$admin_id);
		            
		            // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Admin Surcharge Amount - '.$admin_surcharge_amount.' - Admin Wallet Balance - '.$admin_wallet_balance.'.]'.PHP_EOL;
			        $this->User->generateDMTLog($log_msg);
		            
		            $admin_after_wallet_balance = $admin_wallet_balance - $admin_surcharge_amount;   

		            if($admin_wallet_balance < $admin_surcharge_amount){

		            	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Admin Wallet Balance Error.]'.PHP_EOL;
				        $this->User->generateDMTLog($log_msg);
		              
		              $this->Az->redirect('master/dmt/transferBen/'.$mobile, 'system_message_error',lang('ADMIN_WALLET_BALANCE_ERROR'));
		            
		            }
	        	}

	        	$final_amount = $surcharge_amount;
	            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
				
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Member Wallet Balance - '.$before_balance.' Member Final Deduct Amount - '.$final_amount.']'.PHP_EOL;
		        $this->User->generateDMTLog($log_msg);

	            if($before_balance < $final_amount){
	                
	                // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Member Wallet Balance Error]'.PHP_EOL;
			        $this->User->generateDMTLog($log_msg);

	                $this->Az->redirect('master/dmt/transferBen/'.$mobile, 'system_message_error',lang('WALLET_BALANCE_ERROR'));
	            }
			}
			$response = $this->Dmt_model->addBeneficiary($post,$mobile,$is_admin_surcharge,$surcharge_amount,$admin_surcharge_amount);

			if($response['status'] == 1)
			{
				$this->Az->redirect('master/dmt/transferBen/'.$mobile, 'system_message_error',lang('DMT_BEN_VERIFY_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('master/dmt/transferBen/'.$mobile, 'system_message_error',sprintf(lang('DMT_BEN_REGISTER_ERROR'),$response['message']));
			}
		}
	}

	public function updateDetailAuth()
	{
		$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		//check for foem validation
		$post = $this->input->post();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Update Personal Detail Auth Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('dob', 'DOB', 'required|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
		$this->form_validation->set_rules('pin_code', 'Pin Code', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->transferBen($post['accountMobile']);
		}
		else
		{	
			// save activation data
	        $data = array(
	            'first_name' => $post['first_name'],
	            'last_name' => $post['last_name'],
	            'dob' => $post['dob'],
	            'address' => $post['address'],
	            'pin_code' => $post['pin_code'],
	            'updated' => date('Y-m-d H:i:s'),
	            'updated_by' => $loggedAccountID
	        );
	        $this->db->where('account_id',$account_id);
	        $this->db->where('member_id',$loggedAccountID);
	        $this->db->where('mobile',$post['accountMobile']);
	        $this->db->update('user_paysprint_dmt_activation',$data);
			
	        // save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Update Personal Detail Success.]'.PHP_EOL;
	        $this->User->generateDMTLog($log_msg);
			
			$this->Az->redirect('master/dmt/transferBen/'.$post['accountMobile'], 'system_message_error',lang('DMT_CUS_UPDATE_SUCCESS'));
			
		}
	}

	public function deleteBen($benId = '',$mobile = '')
    {
    	$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check benId valid or not
		$chk_ben_id = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$benId,'status'=>1))->num_rows();
		if(!$chk_ben_id)
		{
			$this->Az->redirect('master/dmt/transferBen/'.$mobile, 'system_message_error',lang('DMT_BEN_ID_ERROR'));
		}

		$response = $this->Dmt_model->deleteBen($benId,$mobile);
		if($response['status'] == 1)
		{
			$this->Az->redirect('master/dmt/transferBen/'.$mobile, 'system_message_error',lang('DMT_BEN_DELETE_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('master/dmt/transferBen/'.$mobile, 'system_message_error',sprintf(lang('DMT_BEN_DELETE_ERROR'),$response['message']));
		}
		
    }

    public function moneyTransfer($benId = '')
    {
    	$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$account_no = '';
		$ifsc = '';

		if($benId != '')
		{
			// check benId valid or not
			$chk_ben_id = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$benId,'status'=>1))->num_rows();
			if(!$chk_ben_id)
			{
				$this->Az->redirect('master/dmt/transferNow', 'system_message_error',lang('DMT_BEN_ID_ERROR'));
			}
			$benData = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$benId,'status'=>1))->row_array();
			$account_no = isset($benData['account_no']) ? $benData['account_no'] : '';
			$ifsc = isset($benData['ifsc']) ? $benData['ifsc'] : '';
		}

		$beneficiaryList = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>1))->result_array();
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'dmt/transfer',
            'manager_description' => lang('SITE_NAME'),
            'beneficiaryList' => $beneficiaryList,
            'benId' => $benId,
            'account_no' => $account_no,
            'ifsc' => $ifsc,
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    public function getBenData($beneId = '')
	{
		$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

		$benData = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$beneId,'status'=>1))->row_array();

		$response = array(
			'status' => 1,
			'account_no' => isset($benData['account_no']) ? $benData['account_no'] : '',
			'ifsc' => isset($benData['ifsc']) ? $benData['ifsc'] : ''
		);

		echo json_encode($response);

	}

    function maximumCheck($num)
    {
        if ($num < 5)
        {
            $this->form_validation->set_message(
                            'maximumCheck',
                            'The %s field must be grater than 5'
                        );
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    // save member
	public function transferAuth()
	{
		$account_id = $this->User->get_domain_account();
         $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(8, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		//check for foem validation
		$post = $this->input->post();
		
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('benId', 'Beneficiary', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->moneyTransfer($post['benId']);
		}
		else
		{	
			// check benId valid or not
			$chk_ben_id = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$post['benId'],'status'=>1))->num_rows();
			if(!$chk_ben_id)
			{
				$this->Az->redirect('master/dmt/transferNow', 'system_message_error',lang('DMT_BEN_ID_ERROR'));
			}
			//$active_api_id = $this->User->get_dmt_active_api();
			$active_api_id =  2 ;
			if(!$active_api_id)
			{
				$this->Az->redirect('master/dmt/moneyTransfer/'.$post['benId'], 'system_message_error',lang('DMT_API_ERROR'));
			}
			$requestAmount = $post['amount'];
			$remainingAmount = 0;
			if($requestAmount <= 5000)
			{
				$amount = $post['amount'];
				$loopTime = 1;
			}
			else
			{
				$loopTime = intval($requestAmount/5000);
				$remainingAmount = $requestAmount - ($loopTime*5000);
				$amount = 5000;
				$post['amount'] = $amount;
			}
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Total Loop Time - '.$loopTime.' - Loop Amount - '.$amount.'.]'.PHP_EOL;
	        $this->User->generateDMTLog($log_msg);
	        for($startLoop = 1; $startLoop <= $loopTime; $startLoop++)
	        {
				$is_admin_surcharge = 0;
				// get dmr surcharge
	            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
	            // save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
		        $this->User->generateDMTLog($log_msg);
				if(!$surcharge_amount)
				{
					$is_admin_surcharge = 1;
					// get admin data
					$admin_id = $this->User->get_admin_id();
					$admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);
					// get admin dmr surcharge
		            $admin_surcharge_amount = $this->User->get_admin_dmr_surcharge($amount,$admin_id);
		            
		            // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Admin ID - '.$admin_id.' Admin Wallet Balance - '.$admin_wallet_balance.' Admin Charge Amount - '.$admin_surcharge_amount.'.]'.PHP_EOL;
			        $this->User->generateDMTLog($log_msg);
		            
		            $admin_after_wallet_balance = $admin_wallet_balance - $admin_surcharge_amount;   

		            if($admin_wallet_balance < $admin_surcharge_amount){

		            	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Admin Wallet Balance Error.]'.PHP_EOL;
				        $this->User->generateDMTLog($log_msg);
		              
		              $this->Az->redirect('master/dmt/moneyTransfer/'.$post['benId'], 'system_message_error',lang('ADMIN_WALLET_BALANCE_ERROR'));
		            
		            }
	        	}

	        	$final_amount = $amount + $surcharge_amount;
	            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
				$final_deduct_wallet_balance = $final_amount;

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Member Wallet Balance - '.$before_balance.' Member Final Deduct Amount - '.$final_amount.'.]'.PHP_EOL;
		        $this->User->generateDMTLog($log_msg);

	            if($before_balance < $final_amount){
	                
	                // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Member Wallet Balance Error.]'.PHP_EOL;
			        $this->User->generateDMTLog($log_msg);

	                $this->Az->redirect('master/dmt/moneyTransfer/'.$post['benId'], 'system_message_error',lang('WALLET_BALANCE_ERROR'));
	            }

				$response = $this->Dmt_model->transferFund($post,$is_admin_surcharge,$surcharge_amount,$admin_surcharge_amount,$active_api_id);
				if($response['status'] != 1 && $response['status'] != 2)
				{
					$this->Az->redirect('master/dmt', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_FAILED'),$response['message']));
				}
				
			}
			if($remainingAmount > 101)
			{
				$amount = $remainingAmount;
				$post['amount'] = $amount;
				$is_admin_surcharge = 0;
				// get dmr surcharge
	            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
	            // save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
		        $this->User->generateDMTLog($log_msg);
				if(!$surcharge_amount)
				{
					$is_admin_surcharge = 1;
					// get admin data
					$admin_id = $this->User->get_admin_id();
					$admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);
					// get admin dmr surcharge
		            $admin_surcharge_amount = $this->User->get_admin_dmr_surcharge($amount,$admin_id);
		            
		            // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Admin ID - '.$admin_id.' Admin Wallet Balance - '.$admin_wallet_balance.' Admin Charge Amount - '.$admin_surcharge_amount.'.]'.PHP_EOL;
			        $this->User->generateDMTLog($log_msg);
		            
		            $admin_after_wallet_balance = $admin_wallet_balance - $admin_surcharge_amount;   

		            if($admin_wallet_balance < $admin_surcharge_amount){

		            	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Admin Wallet Balance Error.]'.PHP_EOL;
				        $this->User->generateDMTLog($log_msg);
		              
		              $this->Az->redirect('master/dmt/moneyTransfer/'.$post['benId'], 'system_message_error',lang('ADMIN_WALLET_BALANCE_ERROR'));
		            
		            }
	        	}

	        	$final_amount = $amount + $surcharge_amount;
	            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
				$final_deduct_wallet_balance = $final_amount;

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Member Wallet Balance - '.$before_balance.' Member Final Deduct Amount - '.$final_amount.'.]'.PHP_EOL;
		        $this->User->generateDMTLog($log_msg);

	            if($before_balance < $final_amount){
	                
	                // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') -  DMT Transfer Auth Member Wallet Balance Error.]'.PHP_EOL;
			        $this->User->generateDMTLog($log_msg);

	                $this->Az->redirect('master/dmt/moneyTransfer/'.$post['benId'], 'system_message_error',lang('WALLET_BALANCE_ERROR'));
	            }

				$response = $this->Dmt_model->transferFund($post,$is_admin_surcharge,$surcharge_amount,$admin_surcharge_amount,$active_api_id);
				if($response['status'] != 1 && $response['status'] != 2)
				{
					$this->Az->redirect('master/dmt', 'system_message_error',sprintf(lang('MANUAL_TRANSFER_FAILED'),$response['message']));
				}
			}
			$this->Az->redirect('master/dmt', 'system_message_error',lang('MANUAL_TRANSFER_SUCCESS'));

			
		}
	
	}

	public function getBankDefaultIfsc($bankID = 0)
	{
		// get default ifsc
		$get_default_ifsc = $this->db->get_where('paysprint_dmt_bank_list',array('id'=>$bankID))->row_array();
		$ifsc = isset($get_default_ifsc['default_ifsc']) ? $get_default_ifsc['default_ifsc'] : '';

		echo json_encode(array('ifsc'=>$ifsc));
	}

	
    
}