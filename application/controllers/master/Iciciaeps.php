<?php 
class Iciciaeps extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
        $this->load->model('master/IciciAeps_model');		
        $this->lang->load('master/aeps', 'english');
        
    }

	public function index(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];
	 
        
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(19, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($loggedUser['id']);
		if(!$user_instantpay_aeps_status){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}

		$user_2fa_instantpay_aeps_status = $this->User->get_member_2fa_instantpay_aeps_status($loggedUser['id']);
       
		if(!$user_2fa_instantpay_aeps_status){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}
		
		$user_2fa_instantpay_aeps_loginn_status = $this->User->get_member_2fa_instantpay_aeps_login_status($loggedUser['id']);
		
       
		if(!$user_2fa_instantpay_aeps_loginn_status){
			$this->Az->redirect('master/iciciaeps/memberLogin', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}

		
  		$siteUrl = base_url();	

  		// get bank list
  		$bankList = $this->db->get('instantpay_aeps_bank_list')->result_array();	

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'bankList' => $bankList,
			'account_id' =>$account_id,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'iciciaeps/list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function activeAeps(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(19, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($loggedUser['id']);
		
		if($user_instantpay_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_kyc = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$memberID,'status'=>1))->num_rows();
		if($chk_kyc)
		{
			$this->db->where('id',$memberID);
			$this->db->update('users',array('instantpay_aeps_status'=>1));

			$this->Az->redirect('master/iciciaeps', 'system_message_error',lang('AEPS_ACTIVE_SUCCESS'));
		}

        $memberData = $this->db->get_where('users',array('id'=>$memberID,'instantpay_aeps_status'=>0))->row_array();

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
            'content_block' => 'iciciaeps/member-activation'
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
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]');
        $this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|xss_clean');
        $this->form_validation->set_rules('pancard_no', 'Pancard No', 'required|xss_clean');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('father_name', 'Father Name', 'required|xss_clean');
        $this->form_validation->set_rules('mother_name', 'Mother Name', 'required|xss_clean');
        $this->form_validation->set_rules('shop_name', 'Shop', 'required|xss_clean');
        $this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');
        $this->form_validation->set_rules('city_id', 'City', 'required|xss_clean');
        $this->form_validation->set_rules('pin_code', 'Pincode', 'required|xss_clean');
        $this->form_validation->set_rules('village', 'village', 'required|xss_clean');
        $this->form_validation->set_rules('post', 'Post', 'required|xss_clean');
        $this->form_validation->set_rules('police_station', 'Police Station', 'required|xss_clean');
        $this->form_validation->set_rules('block', 'Block', 'required|xss_clean');
        $this->form_validation->set_rules('district', 'District', 'required|xss_clean');
        $this->form_validation->set_rules('account_no', 'Bank Account', 'required|xss_clean');
          $this->form_validation->set_rules('bank_ifsc', 'Bank Ifsc', 'required|xss_clean');

        if(!isset($_FILES['aadhar_photo']['name']) || $_FILES['aadhar_photo']['name'] == ''){
			$this->form_validation->set_rules('aadhar_photo', 'Aadhar Front Image', 'required|xss_clean');
		}
		 if(!isset($_FILES['aadhar_back_photo']['name']) || $_FILES['aadhar_back_photo']['name'] == ''){
			$this->form_validation->set_rules('aadhar_back_photo', 'Aadhar Back Image', 'required|xss_clean');
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
			if(!in_array(19, $activeService)){
				$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
			}
			
			$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($loggedUser['id']);
			
			if($user_instantpay_aeps_status)
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
					$this->Az->redirect('master/iciciaeps/activeAeps', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$aadhar_photo = substr($config['upload_path'] . $fileData['file_name'], 2);
				}
			}


			$aadhar_back_photo = '';
			if(isset($_FILES['aadhar_back_photo']['name']) && $_FILES['aadhar_back_photo']['name']){
				$config['upload_path'] = './media/aeps_kyc_doc/';
				$config['allowed_types'] = 'jpg|png|jpeg';
				$config['max_size'] = 2048;
				$fileName = time().rand(111111,999999);
				$config['file_name'] = $fileName;
				$this->load->library('upload', $config);
				$this->upload->do_upload('aadhar_back_photo');		
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('master/iciciaeps/activeAeps', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$aadhar_back_photo = substr($config['upload_path'] . $fileData['file_name'], 2);
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
					$this->Az->redirect('master/iciciaeps/activeAeps', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$pancard_photo = substr($config02['upload_path'] . $fileData['file_name'], 2);
				}
			}

			$response = $this->IciciAeps_model->activeAEPSMember($post,$aadhar_photo,$aadhar_back_photo,$pancard_photo);

			$status = $response['status'];

			if($status == 1)
			{
				$otpReferenceID = $response['otpReferenceID'];
				$this->Az->redirect('master/iciciaeps/otpVerify/'.$otpReferenceID, 'system_message_error',lang('AEPS_OTP_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('master/iciciaeps/activeAeps', 'system_message_error',sprintf(lang('AEPS_ACTIVE_FAILED'),$response['msg']));
			}
			
		}
	
	}


	public function otpVerify($otpReferenceID = ''){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(19, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($loggedUser['id']);
		
		if($user_instantpay_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_encode_id = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$memberID,'otpReferenceID'=>$otpReferenceID,'status'=>0))->num_rows();
		if(!$chk_encode_id)
		{
			$this->Az->redirect('master/iciciaeps/activeAeps', 'system_message_error',lang('AEPS_ENCODED_ID_ERROR'));
		}

        $memberData = $this->db->get_where('users',array('id'=>$memberID,'aeps_status'=>0))->row_array();

        $get_data = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$memberID,'otpReferenceID'=>$otpReferenceID))->row_array();
        $hash_data = $get_data['hash'];
        $aadhar = $get_data['aadhar'];

  		$siteUrl = base_url();	

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'memberID' => $memberID,
			'memberData' => $memberData,
			'otpReferenceID' => $otpReferenceID,
			'hash_data' =>$hash_data,
			'aadhar'    =>$aadhar,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'iciciaeps/otp-verify'
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
		if(!in_array(19, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($loggedUser['id']);
		
		if($user_instantpay_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		$otpReferenceID = $post['otpReferenceID'];
		$hash = $post['hash'];
		$aadhar = $post['aadhar'];

		if(!isset($post['otp_code']) || $post['otp_code'] == '')
		{
			$this->Az->redirect('master/iciciaeps/otpVerify/'.$otpReferenceID, 'system_message_error',lang('AEPS_OTP_ERROR'));
		}

		
		$response = $this->IciciAeps_model->aepsOTPAuth($post,$memberID,$otpReferenceID,$hash,$aadhar);
		$status = $response['status'];

		if($status == 1)
		{
			$this->Az->redirect('master/iciciaeps', 'system_message_error',lang('AEPS_OTP_VERIFY_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('master/iciciaeps/activeAeps', 'system_message_error',sprintf(lang('AEPS_ACTIVE_FAILED'),$response['msg']));
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

	

	public function apiAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];
	 	$get_outlet_id = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
	 	$outlet_id = $get_outlet_id['instantpay_outlet_id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(19, $activeService)){
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! AEPS Service Not Active.'
			);
		}
		else
		{
			$agentID = $loggedUser['user_code'];
			$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($memberID);
			$response = array();
			if($user_instantpay_aeps_status)
			{
				$post = file_get_contents('php://input');
				$post = json_decode($post, true);
				if($post)
				{
					$serviceType = $post['ServiceType'];
					$deviceIMEI = $post['deviceIMEI'];
					
					$ivlen = openssl_cipher_iv_length('aes-256-cbc');
                    $iv = openssl_random_pseudo_bytes($ivlen);
                    $ciphertext = openssl_encrypt($post['AadharNumber'],'aes-256-cbc', $accountData['instant_encryption_key'], OPENSSL_RAW_DATA, $iv);
                    $encryptedData = base64_encode($iv . $ciphertext);

					$aadharNumber = $encryptedData;
					$mobile = $post['mobileNumber'];
					$biometricData = $post['BiometricData'];
		    	 //  echo $biometricData;
		    	   
           
					$amount = $post['Amount'];
					$iin = $post['IIN'];
					
					$requestTime = date('Y-m-d H:i:s');
					if($aadharNumber && $mobile && $biometricData && $iin)
					{
						if($serviceType == 'balinfo' || $serviceType == 'ministatement')
						{
							log_message('debug', 'ICICI AEPS api API Call');

							$txnID = 'IPIAB'.time();
							$is_bal_info = 1;
							$is_withdrawal = 0;
							$Servicestype = 'GetBalanceaeps';
							$api_url = INSTANTPAY_AEPS_BALANCE_ENQUIRY;
							if($serviceType == 'ministatement')
							{
							    
								$txnID = 'IPMS'.time();
								$Servicestype = 'getministatment';
								$is_bal_info = 0;
								$api_url = INSTANTPAY_AEPS_MINI_STATEMENT_API_URL;
							}
							log_message('debug', 'ICICI AEPS api API Url - '.$api_url);
							if($amount == 0)
							{
								

								//convert biometric data xml to json

								$xmldata = simplexml_load_string($biometricData , "SimpleXMLElement", LIBXML_BIGLINES, FALSE);

								$get_ci_value = explode("<Skey",$biometricData);

								$get_ci = substr($get_ci_value[1],5);

								$ci_value = explode('">',$get_ci);


								 $jsondata = json_encode($xmldata);
								
								$result = json_decode($jsondata, TRUE);

								$sr_no = '';
								if($result['DeviceInfo']['@attributes']['dpId'] == 'Morpho.SmartChip')

								{
									$sr_no = $result['DeviceInfo']['additional_info']['Param']['@attributes']['value'];

								}

								else
								{
										$sr_no  = $result['DeviceInfo']['additional_info']['Param'][0]['@attributes']['value'];
								}

								$get_biometric_data =array(
									'encryptedAadhaar' =>$aadharNumber,
									'dc'=>$result['DeviceInfo']['@attributes']['dc'],
									'hmac'=>$result['Hmac'],
									'dpId' =>$result['DeviceInfo']['@attributes']['dpId'],
									'ci' =>$ci_value[0],
									'mc' =>$result['DeviceInfo']['@attributes']['mc'],
									'pidDataType'=>"X",
									'sessionKey' =>$result['Skey'],
									'mi' => $result['DeviceInfo']['@attributes']['mi'],
									'rdsId'=>$result['DeviceInfo']['@attributes']['rdsId'],
									'pType'=>'',
									'srno' =>$sr_no,
									'sysid'=>$result['DeviceInfo']['additional_info']['Param'][1]['@attributes']['value'],
									'ts'=>$result['DeviceInfo']['additional_info']['Param'][2]['@attributes']['value'],
									'pidData'=>$result['Data'],
									'qScore' => $result['Resp']['@attributes']['qScore'],
									'nmPoints'=>$result['Resp']['@attributes']['nmPoints'],
									'rdsVer' =>$result['DeviceInfo']['@attributes']['rdsVer'],
									'errCode'=>0,
									'errInfo'=>"",
									'fCount'=> 1,
                                    'fType'=>$result['Resp']['@attributes']['fType'],
                                    'iCount'=> 0,
                                    'iType'=> "",
                                    'pCount'=> 0,

								);

								$request = array(
				                 'bankiin' => $iin,
					              "latitude"=>"22.9734229",
			                      "longitude"=>"78.6568942",
				                   'mobile' =>$mobile,
				                   'externalRef' =>$txnID,
				                   'biometricData' =>$get_biometric_data
		            			);
                        	

                        	log_message('debug', 'ICICI AEPS api post data - '.json_encode($request));
                        

								$header = array(
					                'X-Ipay-Auth-Code: 1',
					                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
					                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
					                 'X-Ipay-Outlet-Id:'.$outlet_id,
					                'X-Ipay-Endpoint-Ip: 164.52.219.77',
					                'content-type: application/json'
					            );


					        log_message('debug', 'ICICI AEPS header  data - '.json_encode($header));


						        	 $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $api_url);

		            // Return Transfer
		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		            // SSL Verify Peer
		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		            // SSL Verify Host
		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		            // Timeout
		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		            // HTTP Version
		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		            // Request Method
		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		            // Request Body
		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

		            // Execute
		             $output = curl_exec($curl);

		            // Close
		            curl_close ($curl);

		            log_message('debug', 'ICICI AEPS api response - '.json_encode($output)); 


						        $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'post_data' =>json_encode($request),
						        	'api_response' => json_encode($output),
						        	'created' => date('Y-m-d H:i:s'),						        	
						        	'created_by' => $memberID
						        );
						        $this->db->insert('aeps_api_response',$apiData);

						        if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
						        {	
						            
						        	$statementList = $responseData['data']['miniStatement'];
						        	$balanceAmount = $responseData['data']['bankAccountBalance'];
							       	$bankRRN = $responseData['data']['ipayId'];
						        	$recordID = $this->IciciAeps_model->saveAepsTxn($txnID,$serviceType,$post['AadharNumber'],$mobile,$amount,$iin,$api_url,$output,$responseData['status'],2,$statementList,$bankRRN,$balanceAmount);
						        	$str = '';
						        	if($is_bal_info == 0)
									{
										$this->IciciAeps_model->addStatementCom($txnID,$post['AadharNumber'],$iin,$amount,$recordID);
										
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
												if($list['txnType'] == 'DR')
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
										'msg' => $responseData['status'],
										'balanceAmount' => $responseData['data']['bankAccountBalance'],
										'bankRRN' => $responseData['data']['bankRRN'],
										'is_bal_info' => $is_bal_info,
										'is_withdrawal' => $is_withdrawal,
										'str' => $str
									);


						        }
						        else
						        {
						        	$this->IciciAeps_model->saveAepsTxn($txnID,$serviceType,$post['AadharNumber'],$mobile,$amount,$iin,$api_url,$output,$responseData['status'],3);
						        	$response = array(
										'status' => 0,
										'msg' => $responseData['status']
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
							log_message('debug', 'ICICI AEPS api API Call');

							$txnID = 'FIAW'.time();
							$is_withdrawal = 1;
							$is_bal_info = 0;
							$Servicestype = 'AccountWithdrowal';
							$api_url = INSTANTPAY_AEPS_WITHDRAWAL_API_URL;
							if($serviceType == 'aadharpay')
							{
								$Servicestype = 'Aadharpay';
								$txnID = 'FIAP'.time();
								$api_url = INSTANTPAY_AEPS_AADHARPAY_API_URL;
							}
							log_message('debug', 'ICICI AEPS api API Url - '.$api_url);
							
							if($amount >= 100 && $amount <= 10000)
							{	
						        $xmldata = simplexml_load_string($biometricData , "SimpleXMLElement", LIBXML_BIGLINES, FALSE);

						        $get_ci_value = explode("<Skey",$biometricData);

								$get_ci = substr($get_ci_value[1],5);

								$ci_value = explode('">',$get_ci);


								 $jsondata = json_encode($xmldata);
								
								$result = json_decode($jsondata, TRUE);


									$sr_no = '';
								if($result['DeviceInfo']['@attributes']['dpId'] == 'Morpho.SmartChip')

								{
									$sr_no = $result['DeviceInfo']['additional_info']['Param']['@attributes']['value'];

								}

								else
								{
										$sr_no  = $result['DeviceInfo']['additional_info']['Param'][0]['@attributes']['value'];
								}



								$get_biometric_data =array(
									'encryptedAadhaar' =>$aadharNumber,
									'dc'=>$result['DeviceInfo']['@attributes']['dc'],
									'hmac'=>$result['Hmac'],
									'dpId' =>$result['DeviceInfo']['@attributes']['dpId'],
									'ci' =>$ci_value[0],
									'mc' =>$result['DeviceInfo']['@attributes']['mc'],
									'pidDataType'=>"X",
									'sessionKey' =>$result['Skey'],
									'mi' => $result['DeviceInfo']['@attributes']['mi'],
									'rdsId'=>$result['DeviceInfo']['@attributes']['rdsId'],
									'pType'=>'',
									'srno' =>$sr_no,
									'sysid'=>$result['DeviceInfo']['additional_info']['Param'][1]['@attributes']['value'],
									'ts'=>$result['DeviceInfo']['additional_info']['Param'][2]['@attributes']['value'],
									'pidData'=>$result['Data'],
									'qScore' => $result['Resp']['@attributes']['qScore'],
									'nmPoints'=>$result['Resp']['@attributes']['nmPoints'],
									'rdsVer' =>$result['DeviceInfo']['@attributes']['rdsVer'],
									'errCode'=>0,
									'errInfo'=>"",
									'fCount'=> 1,
                                    'fType'=>$result['Resp']['@attributes']['fType'],
                                    'iCount'=> 0,
                                    'iType'=> "",
                                    'pCount'=> 0,

								);

									$request = array(
					                 'bankiin' => $iin,
						              "latitude"=>"22.9734229",
				                      "longitude"=>"78.6568942",
					                   'mobile' =>$mobile,
					                    'amount'=>$amount,
					                   'externalRef' =>$txnID,
					                   'biometricData' =>$get_biometric_data
			            			);
                        
                        	
                        	log_message('debug', 'ICICI AEPS api post data - '.json_encode($request));

										$header = array(
					                'X-Ipay-Auth-Code: 1',
					                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
					                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
					                 'X-Ipay-Outlet-Id:'.$outlet_id,
					                'X-Ipay-Endpoint-Ip: 164.52.219.77',
					                'content-type: application/json'
					            );

							log_message('debug', 'ICICI AEPS api header data - '.json_encode($request));			


								        	 $curl = curl_init();
				            // URL
				            curl_setopt($curl, CURLOPT_URL, $api_url);

				            // Return Transfer
				            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

				            // SSL Verify Peer
				            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

				            // SSL Verify Host
				            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

				            // Timeout
				            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

				            // HTTP Version
				            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

				            // Request Method
				            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

				            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

				            // Request Body
				            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

				            // Execute
				             $output = curl_exec($curl);

				            // Close
				            curl_close ($curl);

				            log_message('debug', 'ICICI AEPS api response - '.json_encode($output));

						        $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'api_response' => $output,
						        	'post_data' =>json_encode($request),
						        	'created' => date('Y-m-d H:i:s'),
						        	'created_by' => $memberID
						        );

						        $this->db->insert('aeps_api_response',$apiData);

						        if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
						        {
						        	$balanceAmount = $responseData['data']['bankAccountBalance'];
							        $bankRRN = $responseData['data']['ipayId'];
							        $transactionAmount = $responseData['data']['transactionValue'];
							        $statementList = array();
							        $recordID = $this->IciciAeps_model->saveAepsTxn($txnID,$serviceType,$post['AadharNumber'],$mobile,$amount,$iin,$api_url,$output,$responseData['status'],2,$statementList,$balanceAmount,$bankRRN,$transactionAmount);
							        $this->IciciAeps_model->addBalance($txnID,$post['AadharNumber'],$iin,$amount,$recordID,$serviceType);
						        	$str = '';
						        	$str = '<div class="table-responsive">';
									$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
									$str.='<tr>';
									$str.='<td>Txn Status</td><td><font color="green">Successful</font></td>';
									$str.='</tr>';
									
									$str.='<tr>';
									$str.='<td>Transfer Amount</td><td>INR '.$responseData['data']['transactionValue'].'/-</td>';
									$str.='</tr>';

									$str.='<tr>';
									$str.='<td>Balance Amount</td><td>INR '.$responseData['data']['bankAccountBalance'].'/-</td>';
									$str.='</tr>';

									$str.='<tr>';
									$str.='<td>Bank RRN</td><td>'.$responseData['data']['ipayId'].'/-</td>';
									$str.='</tr>';

									$str.='</table>';
									$str.='</div>';
									
									
						        	$response = array(
										'status' => 1,
										'msg' => $responseData['status'],
										'balanceAmount' => $responseData['data']['bankAccountBalance'],
										'bankRRN' => $responseData['data']['ipayId'],
										'is_bal_info' => $is_bal_info,
										'is_withdrawal' => $is_withdrawal,
										'str' => $str
									);


						        }
						        else
						        {
						        	$this->IciciAeps_model->saveAepsTxn($txnID,$serviceType,$post['AadharNumber'],$mobile,$amount,$iin,$api_url,$output,$responseData['status'],3);
						        	$response = array(
										'status' => 0,
										'msg' => $responseData['status']
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
		if(!in_array(19, $activeService)){
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
            'content_block' => 'iciciaeps/transaction-list'
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
	   	$fromDate = '';
        $toDate = '';
        $status = 0;
        $service = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $service = isset($filterData[4]) ? trim($filterData[4]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '".$keyword."%'";
				$sql.=" OR a.account_no LIKE '".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '".$keyword."%' )";
			}

			if($status)
            {
                $sql.=" AND status = '$status'";
            }

             if($service != ''){
            
             $sql.=" AND service = '$service'";	
            
            }

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }


			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();


			$sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR b.name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.aadhar_no LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.amount LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.service LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.message LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.txnID LIKE '%".$keyword."%' )";
			}

			$sql_summery.=" ) as x WHERE x.id > 0";



		if($firstLoad == 1)
			{
				$sql_summery.=" AND DATE(created) = '".date('Y-m-d')."'";
			}


			if($status)
            {
                $sql_summery.=" AND status = '$status'";
            }

              if($service != ''){
            
             $sql_summery.=" AND service = '$service'";	
            
            }

	


			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }


            $sql_success_summery = $sql_summery;	
			$sql_success_summery.=" AND x.status = 2";



			
			$get_success_recharge = $this->db->query($sql_success_summery)->row_array();
			


			$successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;
	    	
			
			
	    	

	        $sql_failed_summery = $sql_summery;
			$sql_failed_summery.=" AND x.status = 3";	
			$get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();
			
			
	        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
	        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;


		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){

					 $get_bank_name = $this->db->get_where('instantpay_aeps_bank_list',array('iinno'=>$list['iinno']))->row_array();
				    $bank_name = $get_bank_name['bank_name'];

				
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
				$nestedData[] = $bank_name;	
				if($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				else{
					$nestedData[] = '<font color="black">Pending</font>';
				}

				if($list['receipt_id']){
					
				 $nestedData[] = '<a href="'.base_url('master/report/iciciAepsInvoice/'.$list['id'].'').'" target="_blank">'.$list['receipt_id'].'</a>';

				}
				else{

					$nestedData[] = 'Not Available';
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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,					
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}



	public function memberLogin(){

			
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
    	$activeService = $this->User->account_active_service($loggedUser['id']);
	    
	   
	
		if(!in_array(19, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
			$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($loggedUser['id']);
			
			if(!$user_instantpay_aeps_status)
			{
				$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
			}
		
		$siteUrl = base_url();	
		
  		$get_member_data  = $this->db->get_where('users',array('id'=>$loggedUser['id'],'account_id'=>$account_id))->row_array();
  		
  	
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'bankList' => $bankList,
			'account_id'=>$account_id,
			'get_member_data' =>$get_member_data,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'iciciaeps/member-login'
		);
		$this->parser->parse('master/layout/column-1' , $data);
		
	}


	public function kycBioAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		$response = array();
		if(!in_array(19, $activeService)){
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
				//$encodeFPTxnId = $post['encodeFPTxnId'];
				$biometricData = $post['BiometricData'];
				$iin = '';
				$requestTime = date('Y-m-d H:i:s');
				$txnID = 'FIAK'.time();

				$memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        		$member_code = $memberData['user_code'];

				// check already kyc approved or not
				$get_kyc_data =$this->db->get_where('instantpay_ekyc',array('member_id'=>$loggedUser['id'],'account_id'=>$account_id,'status'=>1))->row_array();
				
				$aadhar_no = isset($get_kyc_data['aadhar']) ? $get_kyc_data['aadhar'] : '';
				$mobile = isset($get_kyc_data['mobile']) ? $get_kyc_data['mobile'] : '';
				$agentID = $loggedUser['user_code'];
				$outlet_id = $loggedUser['instantpay_outlet_id'];
				$biometricData = $post['BiometricData'];
                
                
                	$every_txn_auth =  $memberData['instantpay_every_aeps_status'];
				
				if($every_txn_auth == 1)
				{
					$txn_type = 'TXN_AUTH';
					$iin_no = '508534';
				}
				else
				{
					$txn_type = 'DAILY_LOGIN';
					$iin_no = '';
				}
				
                
			
				$api_url = INSTANTPAY_2FA_LOGIN_URL;


                $ivlen = openssl_cipher_iv_length('aes-256-cbc');
                $iv = openssl_random_pseudo_bytes($ivlen);
                $ciphertext = openssl_encrypt($aadhar_no,'aes-256-cbc', $accountData['instant_encryption_key'], OPENSSL_RAW_DATA, $iv);
                $encryptedData = base64_encode($iv . $ciphertext);
                $aadharNumber = $encryptedData;


                  $xmldata = simplexml_load_string($biometricData , "SimpleXMLElement", LIBXML_BIGLINES, FALSE);

								$get_ci_value = explode("<Skey",$biometricData);

								$get_ci = substr($get_ci_value[1],5);

								$ci_value = explode('">',$get_ci);


								 $jsondata = json_encode($xmldata);
								
								$result = json_decode($jsondata, TRUE);

								$sr_no = '';
								if($result['DeviceInfo']['@attributes']['dpId'] == 'Morpho.SmartChip')

								{
									$sr_no = $result['DeviceInfo']['additional_info']['Param']['@attributes']['value'];

								}

								else
								{
										$sr_no  = $result['DeviceInfo']['additional_info']['Param'][0]['@attributes']['value'];
								}

								$get_biometric_data =array(
									'encryptedAadhaar' =>$aadharNumber,
									'dc'=>$result['DeviceInfo']['@attributes']['dc'],
									'hmac'=>$result['Hmac'],
									'dpId' =>$result['DeviceInfo']['@attributes']['dpId'],
									'ci' =>$ci_value[0],
									'mc' =>$result['DeviceInfo']['@attributes']['mc'],
									'pidDataType'=>"X",
									'sessionKey' =>$result['Skey'],
									'mi' => $result['DeviceInfo']['@attributes']['mi'],
									'rdsId'=>$result['DeviceInfo']['@attributes']['rdsId'],
									'pType'=>'',
									'srno' =>$sr_no,
									'sysid'=>$result['DeviceInfo']['additional_info']['Param'][1]['@attributes']['value'],
									'ts'=>$result['DeviceInfo']['additional_info']['Param'][2]['@attributes']['value'],
									'pidData'=>$result['Data'],
									'qScore' => $result['Resp']['@attributes']['qScore'],
									'nmPoints'=>$result['Resp']['@attributes']['nmPoints'],
									'rdsVer' =>$result['DeviceInfo']['@attributes']['rdsVer'],
									'errCode'=>0,
									'errInfo'=>"",
									'fCount'=> 1,
                                    'fType'=>$result['Resp']['@attributes']['fType'],
                                    'iCount'=> 0,
                                    'iType'=> "",
                                    'pCount'=> 0,

								);
							
								$request = array(
								     "type" =>$txn_type,
								     "bankiin" =>$iin_no,
					              "latitude"=>"22.9734229",
			                      "longitude"=>"78.6568942",
				                   'biometricData' =>$get_biometric_data
		            			);
                        	

                        	log_message('debug', 'ICICI AEPS api post data - '.json_encode($request));
                        

								$header = array(
					                'X-Ipay-Auth-Code: 1',
					                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
					                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
					                 'X-Ipay-Outlet-Id:'.$outlet_id,
					                'X-Ipay-Endpoint-Ip: 164.52.219.77',
					                'content-type: application/json'
					            );


					        log_message('debug', 'ICICI AEPS header  data - '.json_encode($header));


						        	 $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $api_url);

		            // Return Transfer
		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		            // SSL Verify Peer
		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		            // SSL Verify Host
		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		            // Timeout
		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		            // HTTP Version
		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		            // Request Method
		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		            // Request Body
		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

		            // Execute
		             $output = curl_exec($curl);

		            // Close
		            curl_close ($curl);

		            log_message('debug', 'ICICI 2FA AEPS api response - '.json_encode($output)); 


						        $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'post_data' =>json_encode($request),
						        	'api_response' => json_encode($output),
						        	'created' => date('Y-m-d H:i:s'),						        	
						        	'created_by' => $memberID
						        );
						        $this->db->insert('aeps_api_response',$apiData);

		        if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
		        {
		        	
		        					$this->db->where('account_id',$account_id);
						        	$this->db->where('member_id',$memberID);
						        	$this->db->update('instantpay_aeps_member_login_status',array('status'=>1));	
						        	

                	 			
		        	$response = array(
						'status' => true,
						'msg' => $responseData['status']
					);
		        }
		        else
		        {
		        	$response = array(
						'status' => false,
						'msg' => $responseData['status']
					);
		        }
			}
			else
			{
				$response = array(
					'status' => false,
					'msg' => 'Somethis Wrong ! Please Try Again Later.'
				);
			}
		}

		echo json_encode($response);
	}
	


	
	
}