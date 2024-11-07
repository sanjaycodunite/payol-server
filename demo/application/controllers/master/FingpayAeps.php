<?php 
class FingpayAeps extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
        $this->load->model('master/FingpayAeps_model');		
        $this->lang->load('master/aeps', 'english');
        
    }

	public function index(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

        
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(25, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
		if(!$user_aeps_status){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}


		$user_2fa_aeps_status = $this->User->get_member_fingpay_2fa_aeps_status($loggedUser['id']);
		
		if(!$user_2fa_aeps_status){
		   
			$this->Az->redirect('master/fingpayAeps/memberLogin', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}

		$user_2fa_ap_status = $this->User->get_member_fingpay_2fa_ap_status($loggedUser['id']);
		
		if(!$user_2fa_ap_status){
		   
			$this->Az->redirect('master/fingpayAeps/memberRegister', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
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
            'content_block' => 'fingpayaeps/list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function activeAeps(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(25, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_kyc = $this->db->get_where('fingpay_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'status'=>1))->num_rows();
		if($chk_kyc)
		{
			$this->db->where('id',$memberID);
			$this->db->update('users',array('fingpay_aeps_status'=>1));

			$this->Az->redirect('master/fingpayaeps', 'system_message_error',lang('AEPS_ACTIVE_SUCCESS'));
		}

        $memberData = $this->db->get_where('users',array('id'=>$memberID,'fingpay_aeps_status'=>0))->row_array();

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
            'content_block' => 'fingpayaeps/member-activation'
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
			if(!in_array(25, $activeService)){
				$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
			}
			
			$user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
			
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
					$this->Az->redirect('master/fingpayAeps/activeAeps', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
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
					$this->Az->redirect('master/fingpayAeps/activeAeps', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$pancard_photo = substr($config02['upload_path'] . $fileData['file_name'], 2);
				}
			}

			$response = $this->FingpayAeps_model->activeAEPSMember($post,$aadhar_photo,$pancard_photo,$memberID);
			$status = $response['status'];

			if($status == 1)
			{
				$encodeFPTxnId = $response['encodeFPTxnId'];
				$this->Az->redirect('master/fingpayAeps/otpVerify/'.$encodeFPTxnId, 'system_message_error',lang('AEPS_OTP_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('master/fingpayAeps/activeAeps', 'system_message_error',sprintf(lang('AEPS_ACTIVE_FAILED'),$response['msg']));
			}
			
		}
	
	}


	public function otpVerify($encodeFPTxnId = ''){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(25, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_encode_id = $this->db->get_where('fingpay_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
		if(!$chk_encode_id)
		{
			$this->Az->redirect('master/fingpayAeps/activeAeps', 'system_message_error',lang('AEPS_ENCODED_ID_ERROR'));
		}

        $memberData = $this->db->get_where('users',array('id'=>$memberID,'fingpay_aeps_status'=>0))->row_array();

        
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
            'content_block' => 'fingpayaeps/otp-verify'
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
		if(!in_array(25, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		$encodeFPTxnId = $post['encodeFPTxnId'];

		if(!isset($post['otp_code']) || $post['otp_code'] == '')
		{
			$this->Az->redirect('master/FingpayAeps/otpVerify/'.$encodeFPTxnId, 'system_message_error',lang('AEPS_OTP_ERROR'));
		}

		
		$response = $this->FingpayAeps_model->aepsOTPAuth($post,$memberID,$encodeFPTxnId);
		$status = $response['status'];

		if($status == 1)
		{
			$this->Az->redirect('master/FingpayAeps/capture/'.$encodeFPTxnId, 'system_message_error',lang('AEPS_OTP_VERIFY_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('master/FingpayAeps/activeAeps', 'system_message_error',sprintf(lang('AEPS_ACTIVE_FAILED'),$response['msg']));
		}
			
		
	
	}

	public function resendOtp($encodeFPTxnId = ''){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(25, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_encode_id = $this->db->get_where('fingpay_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
		if(!$chk_encode_id)
		{
			$this->Az->redirect('master/FingpayAeps/activeAeps', 'system_message_error',lang('AEPS_ENCODED_ID_ERROR'));
		}

		$response = $this->FingpayAeps_model->aepsResendOtp($memberID,$encodeFPTxnId);
		$status = $response['status'];

		if($status == 1)
		{
			$this->Az->redirect('master/FingpayAeps/otpVerify/'.$encodeFPTxnId, 'system_message_error',lang('AEPS_OTP_RESEND_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('master/FingpayAeps/activeAeps', 'system_message_error',sprintf(lang('AEPS_ACTIVE_FAILED'),$response['msg']));
		}

	
	}

	public function capture($encodeFPTxnId = ''){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(25, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
		
		if($user_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		// check already kyc approved or not
		$chk_encode_id = $this->db->get_where('fingpay_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
		if(!$chk_encode_id)
		{
			$this->Az->redirect('master/fingpayAeps/activeAeps', 'system_message_error',lang('AEPS_ENCODED_ID_ERROR'));
		}


        $memberData = $this->db->get_where('users',array('id'=>$memberID,'fingpay_aeps_status'=>0))->row_array();

        
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
            'content_block' => 'fingpayaeps/capture'
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
		if(!in_array(25, $activeService)){
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
				$get_kyc_data = $this->db->get_where('fingpay_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->row_array();
				$primaryKeyId = isset($get_kyc_data['primaryKeyId']) ? $get_kyc_data['primaryKeyId'] : '';
				$encodeFPTxnId = isset($get_kyc_data['encodeFPTxnId']) ? $get_kyc_data['encodeFPTxnId'] : '';
				$pancard_no = isset($get_kyc_data['pancard_no']) ? $get_kyc_data['pancard_no'] : '';
				$aadharNumber = isset($get_kyc_data['aadhar_no']) ? $get_kyc_data['aadhar_no'] : '';
				$mobile = isset($get_kyc_data['mobile']) ? $get_kyc_data['mobile'] : '';
				$recordID = isset($get_kyc_data['id']) ? $get_kyc_data['id'] : 0;

				$bmPIData   = simplexml_load_string($biometricData);
	        $xmlarray = json_decode(json_encode((array) $bmPIData), true);

	        $serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
	        $piddatatype = $bmPIData->Data[0]['type'];
	        $ci = $bmPIData->Skey[0]['ci'];
	        if($xmlarray['Resp']['@attributes']['errCode'] == 0)
	        {
	            $captureData = array(
                'errCode' => $xmlarray['Resp']['@attributes']['errCode'],
                'errInfo' => $xmlarray['Resp']['@attributes']['errInfo'],
                'fCount' => $xmlarray['Resp']['@attributes']['fCount'],
                'fType' => $xmlarray['Resp']['@attributes']['fType'],
                'iCount' => $xmlarray['Resp']['@attributes']['iCount'],
                'iType' => null,
                'pCount' => $xmlarray['Resp']['@attributes']['pCount'],
                'pType' => "0",
                'nmPoints' => $xmlarray['Resp']['@attributes']['nmPoints'],
                'qScore' => $xmlarray['Resp']['@attributes']['qScore'],
                'dpID' => $xmlarray['DeviceInfo']['@attributes']['dpId'],
                'rdsID' => $xmlarray['DeviceInfo']['@attributes']['rdsId'],
                'rdsVer' => $xmlarray['DeviceInfo']['@attributes']['rdsVer'],
                'dc' => $xmlarray['DeviceInfo']['@attributes']['dc'],
                'mi' => $xmlarray['DeviceInfo']['@attributes']['mi'],
                'mc' => $xmlarray['DeviceInfo']['@attributes']['mc'],
                'ci' => $ci,
                'sessionKey' => $xmlarray['Skey'],
                //'Skey' => $xmlarray['Skey'],
                'hmac' => $xmlarray['Hmac'],
                'PidDatatype' => $piddatatype,
                'Piddata' => $xmlarray['Data']
            );
            $captureData = json_decode(json_encode((array) $captureData), true);
            $captureData['ci'] = $captureData['ci'][0];
            $captureData['PidDatatype'] = $captureData['PidDatatype'][0];

            // Create Data
            $data = array 
            (
                "superMerchantId"=>$accountData['aeps_supermerchant_id'],    
                "merchantLoginId" => $member_code, 
                "primaryKeyId" => $primaryKeyId,
                "encodeFPTxnId" => $encodeFPTxnId,
                "requestRemarks" => "EKYC Biomatric",
                "cardnumberORUID" => array(
                    "nationalBankIdentificationNumber" => null,
                    "indicatorforUID" => "0",
                    "adhaarNumber" => $aadharNumber
                ),
                "captureResponse" => $captureData
            );

            // Generate JSON
            $json = json_encode($data);

            // Generate Session Key
            $key = '';
            $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
            foreach ($mt_rand as $chr)
            {             $key .= chr($chr);         }

            // Read Public Key
            $pub_key_string = file_get_contents('fingpay_public_production.txt');

            // Encrypt using Public Key
            openssl_public_encrypt($key, $crypttext, $pub_key_string);

            // Create Header
            $header = array
            (
                'Content-type: application/json',
                'trnTimestamp: ' . date('d/m/Y H:i:s'),
                'hash: ' . base64_encode(hash('sha256', $json, true)),
                'eskey: ' . base64_encode($crypttext),
                'deviceIMEI:'.$serialno
            );

            // Initialization Vector
            $iv =   '06f2f04cc530364f';

            // Encrypt using AES-128
            $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

            // Create Body
            $request = base64_encode($ciphertext_raw);

            $api_url = FINGPAY_AEPS_EKYC_BIOMATRIC_API_URL;

            // Initialize
            $curl = curl_init();

            //Set Options - Open

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

            // Request Header
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

            // Request Body
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

            // Set Options - Close

            // Execute
            $output = curl_exec($curl);

            // Close
            curl_close ($curl);


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

		        if(isset($responseData['message']) && $responseData['message'] == 'EKYC Completed Successfully')
		        {
		        	// update aeps status
                	$this->db->where('id',$memberID);
                	$this->db->update('users',array('fingpay_aeps_status'=>1));

                	// update aeps status
		            $this->db->where('id',$recordID);
		            $this->db->update('fingpay_aeps_member_kyc',array('status'=>1,'clear_step'=>4));

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
		if(!in_array(25, $activeService)){
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! AEPS Service Not Active.'
			);
		}
		else
		{
			$agentID = $loggedUser['user_code'];
			$get_member_pin = $this->db->get_where('users',array('user_code'=>$agentID,'account_id'=>$account_id))->row_array();
			$member_pin = isset($get_member_pin['decoded_transaction_password']) ? $get_member_pin['decoded_transaction_password'] : '';
			$user_aeps_status = $this->User->get_member_fingpay_aeps_status($memberID);
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
						    $txnType = 'BE';
							$txnID = 'FIAB'.time();
							$is_bal_info = 1;
							$remarks = 'Balance Inquiry';
							$is_withdrawal = 0;
							$Servicestype = 'GetBalanceaeps';
							$api_url = FINGPAY_AEPS_BE_API_URL;
							if($serviceType == 'ministatement')
							{
								$txnID = 'FIMS'.time();
								$Servicestype = 'getministatment';
								$is_bal_info = 0;
								$txnType = 'MS';
								$remarks = 'Mini Statement';
								$api_url = FINGPAY_AEPS_MS_API_URL;
							}
							if($amount == 0)
							{
								$bmPIData   = simplexml_load_string($biometricData);
									$xmlarray = json_decode(json_encode((array) $bmPIData), true);

									$serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
									$piddatatype = $bmPIData->Data[0]['type'];
									$ci = $bmPIData->Skey[0]['ci'];
									if($xmlarray['Resp']['@attributes']['errCode'] == 0)
									{
										$captureData = array(
											'errCode' => $xmlarray['Resp']['@attributes']['errCode'],
											'errInfo' => $xmlarray['Resp']['@attributes']['errInfo'],
											'fCount' => $xmlarray['Resp']['@attributes']['fCount'],
											'fType' => $xmlarray['Resp']['@attributes']['fType'],
											'iCount' => "0",
											'iType' => "",
											'pCount' => "0",
											'pType' => "0",
											'nmPoints' => $xmlarray['Resp']['@attributes']['nmPoints'],
											'qScore' => $xmlarray['Resp']['@attributes']['qScore'],
											'dpID' => $xmlarray['DeviceInfo']['@attributes']['dpId'],
											'rdsID' => $xmlarray['DeviceInfo']['@attributes']['rdsId'],
											'rdsVer' => $xmlarray['DeviceInfo']['@attributes']['rdsVer'],
											'dc' => $xmlarray['DeviceInfo']['@attributes']['dc'],
											'mi' => $xmlarray['DeviceInfo']['@attributes']['mi'],
											'mc' => $xmlarray['DeviceInfo']['@attributes']['mc'],
											'ci' => $ci,
											'sessionKey' => $xmlarray['Skey'],
											//'Skey' => $xmlarray['Skey'],
											'hmac' => $xmlarray['Hmac'],
											'PidDatatype' => "X",
											'Piddata' => $xmlarray['Data']
										);
										$captureData = json_decode(json_encode((array) $captureData), true);
								 		$captureData['ci'] = $captureData['ci'][0];
								
										// Create Data
										$data = array 
										(	
										    "cardnumberORUID" => array(
										    	"nationalBankIdentificationNumber" => $iin,
										    	"indicatorforUID" => "0",
										    	"adhaarNumber" => $aadharNumber
										    ),
										    "captureResponse" => $captureData,
										    "languageCode" => "en",
										    "latitude"=>"22.9734229",
										    "longitude"=>"78.6568942",
										    "mobileNumber" => $mobile,
										    "paymentType" => "B",
										    "requestRemarks" => $remarks,
										    "timestamp" => date('d/m/Y H:i:s'),
										    "merchantUserName" => $agentID,
										    "merchantPin" => md5($member_pin),
										    "subMerchantId" => "",
										    "superMerchantId" => $accountData['aeps_supermerchant_id'],
										    "transactionType" => $txnType
										);
										if($serviceType == 'balinfo')
										{
											$data["merchantTransactionId"] = $txnID;
										}
										else
										{
											$data["merchantTranId"] = $txnID;
										}

										// Generate JSON
										$json = json_encode($data);
                                        
										// Generate Session Key
										$key = '';
										$mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
										foreach ($mt_rand as $chr)
										{             $key .= chr($chr);         }

										// Read Public Key
										$pub_key_string = file_get_contents('fingpay_public_production.txt');

										// Encrypt using Public Key
										openssl_public_encrypt($key, $crypttext, $pub_key_string);

										// Create Header
										$header = array
										(
										    'Content-type: application/json',
										    'trnTimestamp: ' . date('d/m/Y H:i:s'),
										    'hash: ' . base64_encode(hash('sha256', $json, true)),
										    'eskey: ' . base64_encode($crypttext),
										    'deviceIMEI:'.$serialno
										);

										// Initialization Vector
										$iv =   '06f2f04cc530364f';

										// Encrypt using AES-128
										$ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

										// Create Body
										$request = base64_encode($ciphertext_raw);

										// Initialize
										$curl = curl_init();

										//Set Options - Open

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

										// Request Header
										curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

										// Request Body
										curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

										// Set Options - Close

										// Execute
										$output = curl_exec($curl);

										// Close
										curl_close ($curl);
                                        
						        $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'api_response' => $output,
						        	'post_data' =>json_encode($post),
						        	'created' => date('Y-m-d H:i:s'),
						        	'created_by' => $memberID
						        );
						        $this->db->insert('aeps_api_response',$apiData);

						        if(isset($responseData['message']) && $responseData['message'] == 'Request Completed')
						        {	
						          
						            $statementList = $responseData['data']['miniStatementStructureModel'];    
						          	$balanceAmount = $responseData['data']['balanceAmount'];
							        $bankRRN = $responseData['data']['bankRRN'];
						        	$recordID = $this->FingpayAeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$statementList,$balanceAmount,$bankRRN);
						        	$str = '';
						        	if($is_bal_info == 0)
									{
										$this->FingpayAeps_model->addStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID);
										
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
						        	$this->FingpayAeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3);
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
						
						    
						}
						
						elseif($serviceType == 'balwithdraw' || $serviceType == 'aadharpay')
						{	
							$txnType = 'CW';
							$txnID = 'FIAW'.time();
							$is_withdrawal = 1;
							$is_bal_info = 0;
							$api_url = FINGPAY_AEPS_CW_API_URL;
							$Servicestype = 'AccountWithdrowal';
							if($serviceType == 'aadharpay')
							{
								$txnType = 'M';
								$Servicestype = 'Aadharpay';
								$txnID = 'FIAP'.time();
								$api_url = FINGPAY_AEPS_AP_API_URL;
							}
							
							if($amount >= 100 && $amount <= 10000)
							{
								$bmPIData   = simplexml_load_string($biometricData);
									$xmlarray = json_decode(json_encode((array) $bmPIData), true);

									$serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
									$piddatatype = $bmPIData->Data[0]['type'];
									$ci = $bmPIData->Skey[0]['ci'];
									if($xmlarray['Resp']['@attributes']['errCode'] == 0)
									{
										$captureData = array(
											'errCode' => $xmlarray['Resp']['@attributes']['errCode'],
											'errInfo' => $xmlarray['Resp']['@attributes']['errInfo'],
											'fCount' => $xmlarray['Resp']['@attributes']['fCount'],
											'fType' => $xmlarray['Resp']['@attributes']['fType'],
											'iCount' => "0",
											'iType' => "",
											'pCount' => "0",
											'pType' => "0",
											'nmPoints' => $xmlarray['Resp']['@attributes']['nmPoints'],
											'qScore' => $xmlarray['Resp']['@attributes']['qScore'],
											'dpID' => $xmlarray['DeviceInfo']['@attributes']['dpId'],
											'rdsID' => $xmlarray['DeviceInfo']['@attributes']['rdsId'],
											'rdsVer' => $xmlarray['DeviceInfo']['@attributes']['rdsVer'],
											'dc' => $xmlarray['DeviceInfo']['@attributes']['dc'],
											'mi' => $xmlarray['DeviceInfo']['@attributes']['mi'],
											'mc' => $xmlarray['DeviceInfo']['@attributes']['mc'],
											'ci' => $ci,
											'sessionKey' => $xmlarray['Skey'],
											//'Skey' => $xmlarray['Skey'],
											'hmac' => $xmlarray['Hmac'],
											'PidDatatype' => "X",
											'Piddata' => $xmlarray['Data']
										);
										$captureData = json_decode(json_encode((array) $captureData), true);
								 		$captureData['ci'] = $captureData['ci'][0];
								
										
										// Create Data
										$data = array 
										(
										    "cardnumberORUID" => array(
										    	"nationalBankIdentificationNumber" => $iin,
										    	"indicatorforUID" => "0",
										    	"adhaarNumber" => $aadharNumber
										    ),
										    "captureResponse" => $captureData,
										    "languageCode" => "en",
										    "latitude"=>"22.9734229",
										    "longitude"=>"78.6568942",
										    "mobileNumber" => $mobile,
										    "paymentType" => "B",
										    "requestRemarks" => $remarks,
										    "timestamp" => date('d/m/Y H:i:s'),
										   "merchantUserName" => $agentID,
										    "merchantPin" => md5($member_pin),
										    "subMerchantId" => "",
										    "superMerchantId" => $accountData['aeps_supermerchant_id'],
										    "transactionType" => $txnType,
										    "merchantTranId" => $txnID,
										    "transactionAmount" => $amount
										);
										
										// Generate JSON
										$json = json_encode($data);

										// Generate Session Key
										$key = '';
										$mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
										foreach ($mt_rand as $chr)
										{             $key .= chr($chr);         }

										// Read Public Key
										$pub_key_string = $accountData['aeps_certificate'];

										// Encrypt using Public Key
										openssl_public_encrypt($key, $crypttext, $pub_key_string);

										// Create Header
										$header = array
										(
										    'Content-type: application/json',
										    'trnTimestamp: ' . date('d/m/Y H:i:s'),
										    'hash: ' . base64_encode(hash('sha256', $json, true)),
										    'eskey: ' . base64_encode($crypttext),
										    'deviceIMEI:'.$serialno
										);

										// Initialization Vector
										$iv =   '06f2f04cc530364f';

										// Encrypt using AES-128
										$ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

										// Create Body
										$request = base64_encode($ciphertext_raw);

										// Initialize
										$curl = curl_init();

										//Set Options - Open

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

										// Request Header
										curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

										// Request Body
										curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

										// Set Options - Close

										// Execute
										$output = curl_exec($curl);

										// Close
										curl_close ($curl);


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
							        $recordID = $this->FingpayAeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$statementList,$balanceAmount,$bankRRN,$transactionAmount);
							        $this->FingpayAeps_model->addBalance($txnID,$aadharNumber,$iin,$amount,$recordID,$serviceType);
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
						        	$this->FingpayAeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3);
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
								'msg' => 'Somethis Wrong ! Please Try Again Later 1.'
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
						'msg' => 'Somethis Wrong ! Please Try Again Later 2.'
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
			} 
        log_message('debug', ' Fingpay Aeps Api Response Data - '.json_encode($response));	
		echo json_encode($response);
	}

	public function transactionHistory(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(25, $activeService)){
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
            'content_block' => 'fingpayaeps/transaction-list'
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



			$sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";


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

				  $get_bank_name = $this->db->get_where('aeps_bank_list',array('iinno'=>$list['iinno']))->row_array();
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
		if(!in_array(25, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
		if(!$user_aeps_status){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}

		
		$siteUrl = base_url();	
		
  		$get_member_data  = $this->db->get_where('fingpay_aeps_member_kyc',array('member_id'=>$loggedUser['id'],'account_id'=>$account_id,'status'=>1))->row_array();
  		
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
			'content_block' => 'fingpayaeps/member-login'
		);
		$this->parser->parse('master/layout/column-1' , $data);
		
	}


	public function api2FaAuth()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(25, $activeService)){
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! AEPS Service Not Active.'
			);
		}
		else
		{
			$agentID = $loggedUser['user_code'];
			$get_member_pin = $this->db->get_where('users',array('user_code'=>$agentID,'account_id'=>$account_id))->row_array();
			$member_pin = isset($get_member_pin['decoded_transaction_password']) ? $get_member_pin['decoded_transaction_password'] : '';
			$user_aeps_status = $this->User->get_member_fingpay_aeps_status($memberID);
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
					$iin = '607076';
					$account_number = $post['account_number'];
				
					$requestTime = date('Y-m-d H:i:s');
					if($aadharNumber && $mobile && $biometricData)
					{
						if($serviceType == '2FAAuth')
						{
							$txnID = 'FIVO'.time();
								
							if($amount == 0)
							{
								         $bmPIData   = simplexml_load_string($biometricData);
									$xmlarray = json_decode(json_encode((array) $bmPIData), true);

									$serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
									$piddatatype = $bmPIData->Data[0]['type'];
									$ci = $bmPIData->Skey[0]['ci'];
									if($xmlarray['Resp']['@attributes']['errCode'] == 0)
									{
										$captureData = array(
											'errCode' => $xmlarray['Resp']['@attributes']['errCode'],
											'errInfo' => $xmlarray['Resp']['@attributes']['errInfo'],
											'fCount' => $xmlarray['Resp']['@attributes']['fCount'],
											'fType' => $xmlarray['Resp']['@attributes']['fType'],
											'iCount' => "0",
											'iType' => "",
											'pCount' => "0",
											'pType' => "0",
											'nmPoints' => $xmlarray['Resp']['@attributes']['nmPoints'],
											'qScore' => $xmlarray['Resp']['@attributes']['qScore'],
											'dpID' => $xmlarray['DeviceInfo']['@attributes']['dpId'],
											'rdsID' => $xmlarray['DeviceInfo']['@attributes']['rdsId'],
											'rdsVer' => $xmlarray['DeviceInfo']['@attributes']['rdsVer'],
											'dc' => $xmlarray['DeviceInfo']['@attributes']['dc'],
											'mi' => $xmlarray['DeviceInfo']['@attributes']['mi'],
											'mc' => $xmlarray['DeviceInfo']['@attributes']['mc'],
											'ci' => $ci,
											'sessionKey' => $xmlarray['Skey'],
											//'Skey' => $xmlarray['Skey'],
											'hmac' => $xmlarray['Hmac'],
											'PidDatatype' => "X",
											'Piddata' => $xmlarray['Data']
										);
										$captureData = json_decode(json_encode((array) $captureData), true);
								 		$captureData['ci'] = $captureData['ci'][0];
								            
								       
										// Create Data
										$data = array 
										(	
										    "cardnumberORUID" => array(
										    	"nationalBankIdentificationNumber" => $iin,
										    	"indicatorforUID" => "0",
										    	"adhaarNumber" => $aadharNumber
										    ),
										    "captureResponse" => $captureData,
										    "latitude"=>"22.9734229",
										    "longitude"=>"78.6568942",
										    "mobileNumber" => $mobile,
										    "serviceType" => "AEPS",
										    "requestRemarks" => "2FA",
										    "timestamp" => date('d/m/Y H:i:s'),
										    "merchantUserName" => $agentID,
										    "merchantPin" => md5($member_pin),
										    "subMerchantId" => $agentID,
										    "superMerchantId" => $accountData['aeps_supermerchant_id'],
										    "transactionType" => "AUO"
										);
										if($serviceType == '2FAAuth')
										{
											$data["merchantTransactionId"] = $txnID;
										}
										else
										{
											$data["merchantTranId"] = $txnID;
										}

										// Generate JSON
										$json = json_encode($data);
                                        
										// Generate Session Key
										$key = '';
										$mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
										foreach ($mt_rand as $chr)
										{             $key .= chr($chr);         }

										// Read Public Key
										$pub_key_string = file_get_contents('fingpay_public_production.txt');

										// Encrypt using Public Key
										openssl_public_encrypt($key, $crypttext, $pub_key_string);

										// Create Header
										$header = array
										(
										    'Content-type: application/json',
										    'trnTimestamp: ' . date('d/m/Y H:i:s'),
										    'hash: ' . base64_encode(hash('sha256', $json, true)),
										    'eskey: ' . base64_encode($crypttext),
										    'deviceIMEI:'.$serialno
										);

										// Initialization Vector
										$iv =   '06f2f04cc530364f';

										// Encrypt using AES-128
										$ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

										// Create Body
										$request = base64_encode($ciphertext_raw);
                                        
                                        
                                        $api_url = FINGPAY_2FA_API_URL;
										// Initialize
										$curl = curl_init();

										//Set Options - Open

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

										// Request Header
										curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

										// Request Body
										curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

										// Set Options - Close

										// Execute
										$output = curl_exec($curl);

										// Close
										curl_close ($curl);
						        

						        $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'post_data' =>$json,
						        	'api_response' => $output,
						        	'created' => date('Y-m-d H:i:s'),
						        	'created_by' => $memberID
						        );
						        $this->db->insert('aeps_api_response',$apiData);

						        if(isset($responseData['message']) && $responseData['message'] == 'successful')
						        {
						        	    
						        	    
						        	    $this->db->where('id',$memberID);
						        	    $this->db->where('account_id',$account_id);
						        	    $this->db->update('users',array('fingpay_2fa_aeps_status'=>1));
						        	
						        	$response = array(
										'status' => 1,
										'msg' => $responseData['message']
									);


						        }
						        else
						        {
						        	
						        	$response = array(
										'status' => 0,
										'msg' => $responseData['message']
									);
						        }

							}
						}
					}
					    
				else
				{
					$response = array(
						'status' => 0,
						'msg' => 'Something Went Wrong !'
					);
				}
				
				}
				else
				{
				    $response = array(
						'status' => 0,
						'msg' => 'Something Went Wrong !'
					);
				    
				}
			} 
			
		}
		
		}
		
		
		

		echo json_encode($response);
	}



	// member 2fa aadhar pay

	public function memberRegister(){

			
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);



		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(25, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
		if(!$user_aeps_status){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}

		
		$siteUrl = base_url();	
		
  		$get_member_data  = $this->db->get_where('fingpay_aeps_member_kyc',array('member_id'=>$loggedUser['id'],'account_id'=>$account_id,'status'=>1))->row_array();
  		
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
			'content_block' => 'fingpayaeps/member-register'
		);
		$this->parser->parse('master/layout/column-1' , $data);
		
	}


	public function api2FaAuthNew()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(25, $activeService)){
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! AEPS Service Not Active.'
			);
		}
		else
		{
			$agentID = $loggedUser['user_code'];
			$get_member_pin = $this->db->get_where('users',array('user_code'=>$agentID,'account_id'=>$account_id))->row_array();
			$member_pin = isset($get_member_pin['decoded_transaction_password']) ? $get_member_pin['decoded_transaction_password'] : '';
			$user_aeps_status = $this->User->get_member_fingpay_aeps_status($memberID);
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
					$iin = '607076';
					$account_number = $post['account_number'];
				
					$requestTime = date('Y-m-d H:i:s');
					if($aadharNumber && $mobile && $biometricData)
					{
						if($serviceType == '2FAAuth')
						{
							$txnID = 'FIVO'.time();
								
							if($amount == 0)
							{
								         $bmPIData   = simplexml_load_string($biometricData);
									$xmlarray = json_decode(json_encode((array) $bmPIData), true);

									$serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
									$piddatatype = $bmPIData->Data[0]['type'];
									$ci = $bmPIData->Skey[0]['ci'];
									if($xmlarray['Resp']['@attributes']['errCode'] == 0)
									{
										$captureData = array(
											'errCode' => $xmlarray['Resp']['@attributes']['errCode'],
											'errInfo' => $xmlarray['Resp']['@attributes']['errInfo'],
											'fCount' => $xmlarray['Resp']['@attributes']['fCount'],
											'fType' => $xmlarray['Resp']['@attributes']['fType'],
											'iCount' => "0",
											'iType' => "",
											'pCount' => "0",
											'pType' => "0",
											'nmPoints' => $xmlarray['Resp']['@attributes']['nmPoints'],
											'qScore' => $xmlarray['Resp']['@attributes']['qScore'],
											'dpID' => $xmlarray['DeviceInfo']['@attributes']['dpId'],
											'rdsID' => $xmlarray['DeviceInfo']['@attributes']['rdsId'],
											'rdsVer' => $xmlarray['DeviceInfo']['@attributes']['rdsVer'],
											'dc' => $xmlarray['DeviceInfo']['@attributes']['dc'],
											'mi' => $xmlarray['DeviceInfo']['@attributes']['mi'],
											'mc' => $xmlarray['DeviceInfo']['@attributes']['mc'],
											'ci' => $ci,
											'sessionKey' => $xmlarray['Skey'],
											//'Skey' => $xmlarray['Skey'],
											'hmac' => $xmlarray['Hmac'],
											'PidDatatype' => "X",
											'Piddata' => $xmlarray['Data']
										);
										$captureData = json_decode(json_encode((array) $captureData), true);
								 		$captureData['ci'] = $captureData['ci'][0];
								            
								       
										// Create Data
										$data = array 
										(	
										    "cardnumberORUID" => array(
										    	"nationalBankIdentificationNumber" => $iin,
										    	"indicatorforUID" => "0",
										    	"adhaarNumber" => $aadharNumber
										    ),
										    "captureResponse" => $captureData,
										    "latitude"=>"22.9734229",
										    "longitude"=>"78.6568942",
										    "mobileNumber" => $mobile,
										    "serviceType" => "AP",
										    "requestRemarks" => "2FA",
										    "timestamp" => date('d/m/Y H:i:s'),
										    "merchantUserName" => $agentID,
										    "merchantPin" => md5($member_pin),
										    "subMerchantId" => $agentID,
										    "superMerchantId" => $accountData['aeps_supermerchant_id'],
										    "transactionType" => "AUO"
										);
										if($serviceType == '2FAAuth')
										{
											$data["merchantTransactionId"] = $txnID;
										}
										else
										{
											$data["merchantTranId"] = $txnID;
										}

										// Generate JSON
										$json = json_encode($data);
                                        
										// Generate Session Key
										$key = '';
										$mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
										foreach ($mt_rand as $chr)
										{             $key .= chr($chr);         }

										// Read Public Key
										$pub_key_string = file_get_contents('fingpay_public_production.txt');

										// Encrypt using Public Key
										openssl_public_encrypt($key, $crypttext, $pub_key_string);

										// Create Header
										$header = array
										(
										    'Content-type: application/json',
										    'trnTimestamp: ' . date('d/m/Y H:i:s'),
										    'hash: ' . base64_encode(hash('sha256', $json, true)),
										    'eskey: ' . base64_encode($crypttext),
										    'deviceIMEI:'.$serialno
										);

										// Initialization Vector
										$iv =   '06f2f04cc530364f';

										// Encrypt using AES-128
										$ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

										// Create Body
										$request = base64_encode($ciphertext_raw);
                                        
                                        
                                        $api_url = FINGPAY_2FA_API_URL;
										// Initialize
										$curl = curl_init();

										//Set Options - Open

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

										// Request Header
										curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

										// Request Body
										curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

										// Set Options - Close

										// Execute
										$output = curl_exec($curl);

										// Close
										curl_close ($curl);
						        

						        $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'post_data' =>$json,
						        	'api_response' => $output,
						        	'created' => date('Y-m-d H:i:s'),
						        	'created_by' => $memberID
						        );
						        $this->db->insert('aeps_api_response',$apiData);

						        if(isset($responseData['message']) && $responseData['message'] == 'successful')
						        {
						        	    
						        	    
						        	    $this->db->where('id',$memberID);
						        	    $this->db->where('account_id',$account_id);
						        	    $this->db->update('users',array('fingpay_2fa_ap_status'=>1));
						        	
						        	$response = array(
										'status' => 1,
										'msg' => $responseData['message']
									);


						        }
						        else
						        {
						        	
						        	$response = array(
										'status' => 0,
										'msg' => $responseData['message']
									);
						        }

							}
						}
					}
					    
				else
				{
					$response = array(
						'status' => 0,
						'msg' => 'Something Went Wrong !'
					);
				}
				
				}
				else
				{
				    $response = array(
						'status' => 0,
						'msg' => 'Something Went Wrong !'
					);
				    
				}
			} 
			
		}
		
		}
		
		
		

		echo json_encode($response);
	}

		




	
	
}