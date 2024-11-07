<?php 
class Aeps extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
        $this->load->model('master/Aeps_model');		
        $this->lang->load('master/aeps', 'english');
        
    }

	public function index(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

        
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(3, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_aeps_status($loggedUser['id']);
		if(!$user_aeps_status){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}
		
  		$siteUrl = base_url();	

  		// get bank list
  		$bankList = $this->db->get('aeps_bank_list')->result_array();	

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'bankList' => $bankList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'aeps/list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function activeAeps(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(3, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_kyc = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'status'=>1))->num_rows();
		if($chk_kyc)
		{
			$this->db->where('id',$memberID);
			$this->db->update('users',array('aeps_status'=>1));

			$this->Az->redirect('master/aeps', 'system_message_error',lang('AEPS_ACTIVE_SUCCESS'));
		}

        $memberData = $this->db->get_where('users',array('id'=>$memberID,'aeps_status'=>0))->row_array();

        // get state list
  		$stateList = $this->db->order_by('state','asc')->get('aeps_state')->result_array();	
		
		
  		$siteUrl = base_url();	

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'memberID' => $memberID,
			'memberData' => $memberData,
			'stateList' => $stateList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'aeps/member-activation'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	// save member
	public function activeAuth()
	{
		//check for foem validation
		$post = $this->input->post();
		$memberID = $post['memberID'];
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]');
        $this->form_validation->set_rules('shop_name', 'Shop Name', 'required|xss_clean');
        $this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');
        $this->form_validation->set_rules('city_id', 'City', 'required|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
        $this->form_validation->set_rules('pin_code', 'PIN Code', 'required|xss_clean');
        $this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|xss_clean');
        $this->form_validation->set_rules('pancard_no', 'Pancard No', 'required|xss_clean');
        if(!isset($_FILES['aadhar_photo']['name']) || $_FILES['aadhar_photo']['name'] == ''){
			$this->form_validation->set_rules('aadhar_photo', 'Aadhar Image', 'required|xss_clean');
		}
		if(!isset($_FILES['pancard_photo']['name']) || $_FILES['pancard_photo']['name'] == ''){
			$this->form_validation->set_rules('pancard_photo', 'Pancard Image', 'required|xss_clean');
		}
		if ($this->form_validation->run() == FALSE) {
			
			$this->activeAeps();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
		 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		 	$memberID = $loggedUser['id'];

		 	$activeService = $this->User->account_active_service($loggedUser['id']);
			if(!in_array(3, $activeService)){
				$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
			}
			
			$user_aeps_status = $this->User->get_member_aeps_status($loggedUser['id']);
			
			if($user_aeps_status)
			{
				$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
			}

			// upload front document
			$aadhar_photo = '';
			if(isset($_FILES['aadhar_photo']['name']) && $_FILES['aadhar_photo']['name']){
				$config['upload_path'] = './media/aeps_kyc_doc/';
				$config['allowed_types'] = 'jpg|png|jpeg';
				$config['max_size'] = 2048;
				$fileName = time().rand(111111,999999);
				$config['file_name'] = $fileName;
				$this->load->library('upload', $config);
				$this->upload->do_upload('aadhar_photo');		
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('master/aeps/activeAeps', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$aadhar_photo = substr($config['upload_path'] . $fileData['file_name'], 2);
				}
			}
			
			
			// upload back document
			$pancard_photo = '';
			if(isset($_FILES['pancard_photo']['name']) && $_FILES['pancard_photo']['name']){
				$config02['upload_path'] = './media/aeps_kyc_doc/';
				$config02['allowed_types'] = 'jpg|png|jpeg';
				$config02['max_size'] = 2048;
				$fileName = time().rand(111111,999999);
				$config02['file_name'] = $fileName;
				$this->load->library('upload', $config02);
				$this->upload->do_upload('pancard_photo');		
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('master/aeps/activeAeps', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$pancard_photo = substr($config02['upload_path'] . $fileData['file_name'], 2);
				}
			}

			$response = $this->Aeps_model->activeAEPSMember($post,$aadhar_photo,$pancard_photo);
			$status = $response['status'];

			if($status == 1)
			{
				$encodeFPTxnId = $response['encodeFPTxnId'];
				$this->Az->redirect('master/aeps/otpVerify/'.$encodeFPTxnId, 'system_message_error',lang('AEPS_OTP_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('master/aeps/activeAeps', 'system_message_error',sprintf(lang('AEPS_ACTIVE_FAILED'),$response['msg']));
			}
			
		}
	
	}


	public function otpVerify($encodeFPTxnId = ''){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(3, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
		if(!$chk_encode_id)
		{
			$this->Az->redirect('master/aeps/activeAeps', 'system_message_error',lang('AEPS_ENCODED_ID_ERROR'));
		}

        $memberData = $this->db->get_where('users',array('id'=>$memberID,'aeps_status'=>0))->row_array();

        
  		$siteUrl = base_url();	

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'memberID' => $memberID,
			'memberData' => $memberData,
			'encodeFPTxnId' => $encodeFPTxnId,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'aeps/otp-verify'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	// save member
	public function otpAuth()
	{
		//check for foem validation
		$post = $this->input->post();
		$memberID = $post['memberID'];
		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(3, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		$encodeFPTxnId = $post['encodeFPTxnId'];

		if(!isset($post['otp_code']) || $post['otp_code'] == '')
		{
			$this->Az->redirect('master/aeps/otpVerify/'.$encodeFPTxnId, 'system_message_error',lang('AEPS_OTP_ERROR'));
		}

		
		$response = $this->Aeps_model->aepsOTPAuth($post,$memberID,$encodeFPTxnId);
		$status = $response['status'];

		if($status == 1)
		{
			$this->Az->redirect('master/aeps/capture/'.$encodeFPTxnId, 'system_message_error',lang('AEPS_OTP_VERIFY_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('master/aeps/activeAeps', 'system_message_error',sprintf(lang('AEPS_ACTIVE_FAILED'),$response['msg']));
		}
			
		
	
	}

	public function resendOtp($encodeFPTxnId = ''){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(3, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
		if(!$chk_encode_id)
		{
			$this->Az->redirect('master/aeps/activeAeps', 'system_message_error',lang('AEPS_ENCODED_ID_ERROR'));
		}

		$response = $this->Aeps_model->aepsResendOtp($memberID,$encodeFPTxnId);
		$status = $response['status'];

		if($status == 1)
		{
			$this->Az->redirect('master/aeps/otpVerify/'.$encodeFPTxnId, 'system_message_error',lang('AEPS_OTP_RESEND_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('master/aeps/activeAeps', 'system_message_error',sprintf(lang('AEPS_ACTIVE_FAILED'),$response['msg']));
		}

	
	}

	public function capture($encodeFPTxnId = ''){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(3, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
		if(!$chk_encode_id)
		{
			$this->Az->redirect('master/aeps/activeAeps', 'system_message_error',lang('AEPS_ENCODED_ID_ERROR'));
		}


        $memberData = $this->db->get_where('users',array('id'=>$memberID,'aeps_status'=>0))->row_array();

        
  		$siteUrl = base_url();	

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'memberID' => $memberID,
			'memberData' => $memberData,
			'encodeFPTxnId' => $encodeFPTxnId,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'aeps/capture'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function getCityList($state_id = 0)
	{
		// get state name
		$get_state_name = $this->db->get_where('aeps_state',array('id'=>$state_id))->row_array();
		$state_name = isset($get_state_name['state']) ? $get_state_name['state'] : '';
		$str = '<option value="">Select City</option>';
		if($state_name)
		{
			// get city list
			$cityList = $this->db->order_by('city_name','asc')->get_where('city',array('state_name'=>$state_name))->result_array();
			if($cityList)
			{
				foreach($cityList as $list)
				{
					$str.='<option value="'.$list['city_id'].'">'.$list['city_name'].'</option>';
				}
			}
		}
		echo json_encode(array('status'=>1,'str'=>$str));
	}

	public function kycBioAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		$response = array();
		if(!in_array(3, $activeService)){
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! AEPS Service Not Active.'
			);
		}
		else
		{
			$post = file_get_contents('php://input');
			$post = json_decode($post, true);
			if($post)
			{
				$encodeFPTxnId = $post['encodeFPTxnId'];
				$biometricData = $post['BiometricData'];
				$iin = '';
				$requestTime = date('Y-m-d H:i:s');
				$txnID = 'FIAK'.time();

				$memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        		$member_code = $memberData['user_code'];

				// check already kyc approved or not
				$get_kyc_data = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->row_array();
				$primaryKeyId = isset($get_kyc_data['primaryKeyId']) ? $get_kyc_data['primaryKeyId'] : '';
				$encodeFPTxnId = isset($get_kyc_data['encodeFPTxnId']) ? $get_kyc_data['encodeFPTxnId'] : '';
				$pancard_no = isset($get_kyc_data['pancard_no']) ? $get_kyc_data['pancard_no'] : '';
				$aadharNumber = isset($get_kyc_data['aadhar_no']) ? $get_kyc_data['aadhar_no'] : '';
				$mobile = isset($get_kyc_data['mobile']) ? $get_kyc_data['mobile'] : '';
				$recordID = isset($get_kyc_data['id']) ? $get_kyc_data['id'] : 0;

				$api_url = AEPS_EKYC_BIOMATRIC_API_URL."AgentId=".$member_code."&DeviceIMEI=&pan=".$pancard_no."&adhaarNumber=".$aadharNumber."&NBIIN=".$iin."&mobileNumber=".$mobile."&timestamp=".urlencode($requestTime)."&primarykeyid=".$primaryKeyId."&orderid=".$txnID."&encodeFPTxnId=".$encodeFPTxnId;

				$ch = curl_init();
		        curl_setopt($ch, CURLOPT_URL,$api_url);
		        curl_setopt($ch, CURLOPT_POST, 1);
		        curl_setopt($ch, CURLOPT_POSTFIELDS,$biometricData);
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		        
		        $headers = [
		            'username: '.$accountData['aeps_username'],
            		'password: '.$accountData['aeps_password'],
		            'Content-Type:text/xml'
		        ];
		        
		        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		        $output = curl_exec ($ch);
		        curl_close ($ch);

		        $responseData = json_decode($output,true);
		        $finalResponse = isset($responseData['message']) ? json_decode($responseData['message'],true) : array();

		        $apiData = array(
		        	'account_id' => $account_id,
            		'user_id' => $memberID,
		        	'api_url' => $api_url,
		        	'api_response' => $output,
		        	'post_data' => $biometricData,
		        	'created' => date('Y-m-d H:i:s'),
		        	'created_by' => $account_id
		        );
		        $this->db->insert('aeps_api_response',$apiData);

		        if(isset($finalResponse['status']) && $finalResponse['status'] == 1)
		        {
		        	// update aeps status
                	$this->db->where('id',$memberID);
                	$this->db->update('users',array('aeps_status'=>1));

                	// update aeps status
		            $this->db->where('id',$recordID);
		            $this->db->update('aeps_member_kyc',array('status'=>1,'clear_step'=>5));

		            $this->session->set_flashdata('system_message_error', '<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulation ! Your EKYC has been approved.</div>');

		        	$response = array(
						'status' => 1,
						'msg' => 'Congratulation ! Your EKYC has been approved.'
					);
		        }
		        else
		        {
		        	$response = array(
						'status' => 0,
						'msg' => 'Sorry ! Your BiometricData not valid.'
					);
		        }
			}
			else
			{
				$response = array(
					'status' => 0,
					'msg' => 'Somethis Wrong ! Please Try Again Later.'
				);
			}
		}

		echo json_encode($response);
	}

	public function apiAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(3, $activeService)){
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! AEPS Service Not Active.'
			);
		}
		else
		{
			$agentID = $loggedUser['user_code'];
			$user_aeps_status = $this->User->get_member_aeps_status($memberID);
			$response = array();
			if($user_aeps_status)
			{
				$post = file_get_contents('php://input');
				$post = json_decode($post, true);
				if($post)
				{
					$serviceType = $post['ServiceType'];
					$deviceIMEI = $post['deviceIMEI'];
					$aadharNumber = $post['AadharNumber'];
					$mobile = $post['mobileNumber'];
					$biometricData = $post['BiometricData'];
					
					$amount = $post['Amount'];
					$iin = $post['IIN'];
					
					$requestTime = date('Y-m-d H:i:s');
					if($aadharNumber && $mobile && $biometricData && $iin)
					{
						if($serviceType == 'balinfo' || $serviceType == 'ministatement')
						{
						    
							$txnID = 'FIAB'.time();
							$is_bal_info = 1;
							$is_withdrawal = 0;
							$Servicestype = 'GetBalanceaeps';
							if($serviceType == 'ministatement')
							{
								$txnID = 'FIMS'.time();
								$Servicestype = 'getministatment';
								$is_bal_info = 0;
							}
							if($amount == 0)
							{
								$api_url = AEPS_BALANCE_API_URL."AgentId=".$agentID."&DeviceIMEI=&merchantTranId=".$txnID."&adhaarNumber=".$aadharNumber."&NBIIN=".$iin."&mobileNumber=".$mobile."&timestamp=".urlencode($requestTime)."&transactionAmount=".$amount."&merchantTransactionId=".$txnID."&Servicestype=".$Servicestype;


								$ch = curl_init();
						        curl_setopt($ch, CURLOPT_URL,$api_url);
						        curl_setopt($ch, CURLOPT_POST, 1);
						        curl_setopt($ch, CURLOPT_POSTFIELDS,$biometricData);
						        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						        
						        $headers = [
						            'username: '.$accountData['aeps_username'],
            						'password: '.$accountData['aeps_password'],
						            'Content-Type:text/xml'
						        ];
						        
						        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
						        $output = curl_exec ($ch);
						        curl_close ($ch);

						        $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'api_response' => $output,
						        	'created' => date('Y-m-d H:i:s'),
						        	'created_by' => $memberID
						        );
						        $this->db->insert('aeps_api_response',$apiData);

						        if(isset($responseData['message']) && $responseData['message'] == 'Request Completed')
						        {	
						        	$statementList = $responseData['data']['miniStatementStructureModel'];
						        	$balanceAmount = $responseData['data']['balanceAmount'];
							        $bankRRN = $responseData['data']['bankRRN'];
						        	$recordID = $this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$statementList,$balanceAmount,$bankRRN);
						        	$str = '';
						        	if($is_bal_info == 0)
									{
										$this->Aeps_model->addStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID);
										
										if($statementList)
										{
											$str = '<div class="table-responsive">';
											$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
											$str.='<tr>';
											$str.='<th>#</th>';
											$str.='<th>Date</th>';
											$str.='<th>CR/DR</th>';
											$str.='<th>Amount</th>';
											$str.='<th>Description</th>';
											$str.='</tr>';
											$i = 1;
											foreach($statementList as $list)
											{
												$str.='<tr>';
												$str.='<td>'.$i.'</td>';
												$str.='<td>'.$list['date'].'</td>';
												if($list['txnType'] == 'Dr')
												{
													$str.='<td><font color="red">DR</font></td>';
												}
												else
												{
													$str.='<td><font color="green">CR</font></td>';
												}
												$str.='<td>INR '.$list['amount'].'/-</td>';
												$str.='<td>'.$list['narration'].'</td>';
												$str.='</tr>';
												$i++;
											}
											$str.='</table>';
											$str.='</div>';
										}
									}
						        	$response = array(
										'status' => 1,
										'msg' => $responseData['message'],
										'balanceAmount' => $responseData['data']['balanceAmount'],
										'bankRRN' => $responseData['data']['bankRRN'],
										'is_bal_info' => $is_bal_info,
										'is_withdrawal' => $is_withdrawal,
										'str' => $str
									);


						        }
						        else
						        {
						        	$this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3);
						        	$response = array(
										'status' => 0,
										'msg' => $responseData['message']
									);
						        }

						        
						       
							}
							else
							{
								$response = array(
									'status' => 0,
									'msg' => 'Sorry ! Amount is not valid.'
								);
							}
						}
						elseif($serviceType == 'balwithdraw' || $serviceType == 'aadharpay')
						{
							$txnID = 'FIAW'.time();
							$is_withdrawal = 1;
							$is_bal_info = 0;
							$Servicestype = 'AccountWithdrowal';
							if($serviceType == 'aadharpay')
							{
								$Servicestype = 'Aadharpay';
								$txnID = 'FIAP'.time();
							}
							
							if($amount >= 100 && $amount <= 10000)
							{
								$api_url = AEPS_BALANCE_API_URL."AgentId=".$agentID."&DeviceIMEI=&merchantTranId=".$txnID."&adhaarNumber=".$aadharNumber."&NBIIN=".$iin."&mobileNumber=".$mobile."&timestamp=".urlencode($requestTime)."&transactionAmount=".$amount."&merchantTransactionId=".$txnID."&Servicestype=".$Servicestype;


								$ch = curl_init();
						        curl_setopt($ch, CURLOPT_URL,$api_url);
						        curl_setopt($ch, CURLOPT_POST, 1);
						        curl_setopt($ch, CURLOPT_POSTFIELDS,$biometricData);
						        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						        
						        $headers = [
						            'username: '.$accountData['aeps_username'],
            						'password: '.$accountData['aeps_password'],
						            'Content-Type:text/xml'
						        ];
						        
						        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
						        $output = curl_exec ($ch);
						        curl_close ($ch);

						        $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'api_response' => $output,
						        	'created' => date('Y-m-d H:i:s'),
						        	'created_by' => $memberID
						        );
						        $this->db->insert('aeps_api_response',$apiData);

						        if(isset($responseData['message']) && $responseData['message'] == 'Request Completed')
						        {
						        	$balanceAmount = $responseData['data']['balanceAmount'];
							        $bankRRN = $responseData['data']['bankRRN'];
							        $transactionAmount = $responseData['data']['transactionAmount'];
							        $statementList = array();
							        $recordID = $this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$statementList,$balanceAmount,$bankRRN,$transactionAmount);
							        $this->Aeps_model->addBalance($txnID,$aadharNumber,$iin,$amount,$recordID,$serviceType);
						        	$str = '';
						        	$str = '<div class="table-responsive">';
									$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
									$str.='<tr>';
									$str.='<td>Txn Status</td><td><font color="green">Successful</font></td>';
									$str.='</tr>';
									
									$str.='<tr>';
									$str.='<td>Transfer Amount</td><td>INR '.$responseData['data']['transactionAmount'].'/-</td>';
									$str.='</tr>';

									$str.='<tr>';
									$str.='<td>Balance Amount</td><td>INR '.$responseData['data']['balanceAmount'].'/-</td>';
									$str.='</tr>';

									$str.='<tr>';
									$str.='<td>Bank RRN</td><td>INR '.$responseData['data']['bankRRN'].'/-</td>';
									$str.='</tr>';

									$str.='</table>';
									$str.='</div>';
									
									
						        	$response = array(
										'status' => 1,
										'msg' => $responseData['message'],
										'balanceAmount' => $responseData['data']['balanceAmount'],
										'bankRRN' => $responseData['data']['bankRRN'],
										'is_bal_info' => $is_bal_info,
										'is_withdrawal' => $is_withdrawal,
										'str' => $str
									);


						        }
						        else
						        {
						        	$this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3);
						        	$response = array(
										'status' => 0,
										'msg' => $responseData['message']
									);
						        }

						        
						       
							}
							else
							{
								$response = array(
									'status' => 0,
									'msg' => 'Sorry ! Amount should be less than 10000 and grater than or equal 101.'
								);
							}
						}
						else
						{
							$response = array(
								'status' => 0,
								'msg' => 'Somethis Wrong ! Please Try Again Later.'
							);		
						}
					}
					else
					{
						$response = array(
							'status' => 0,
							'msg' => 'Sorry ! Please enter required data.'
						);		
					}

				}
				else
				{
					$response = array(
						'status' => 0,
						'msg' => 'Somethis Wrong ! Please Try Again Later.'
					);
				}
			}
			else
			{
				$response = array(
					'status' => 0,
					'msg' => 'Sorry ! AEPS not activated.'
				);
			}
		}

		echo json_encode($response);
	}

	public function transactionHistory(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(3, $activeService)){
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
            'content_block' => 'aeps/transaction-list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getTransactionList()
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
			$sql = "SELECT a.* FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
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
				$nestedData[] = $list['member_code'];
				$nestedData[] = $list['member_name'];
				if($list['service'] == 'balwithdraw' || $list['service'] == 'aadharpay')
				{
					$nestedData[] = 'Account Withdrawal';
				}
				elseif($list['service'] == 'balinfo')
				{
					$nestedData[] = 'Balance Info';
				}
				elseif($list['service'] == 'ministatement')
				{
					$nestedData[] = 'Mini Statement';
				}
				$nestedData[] = $list['aadhar_no'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['amount'].' /-';
				$nestedData[] = $list['txnID'];
				if($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				else{
					$nestedData[] = '<font color="black">Pending</font>';
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