<?php
class FingpayAeps extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->User->checkRetailerPermission();
        $this->load->model('retailer/FingpayAeps_model');
        $this->lang->load('retailer/aeps', 'english');
    }

    public function index()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);

        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AUTHORIZE_ERROR'));
        }

        $user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
        if (!$user_aeps_status) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AEPS_ACTIVE_ERROR'));
        }

        $user_2fa_aeps_status = $this->User->get_member_fingpay_2fa_aeps_status($loggedUser['id']);

        if (!$user_2fa_aeps_status) {
            $this->Az->redirect('retailer/fingpayAeps/memberLogin', 'system_message_error', lang('AEPS_ACTIVE_ERROR'));
        }

        // 		$user_2fa_ap_status = $this->User->get_member_fingpay_2fa_ap_status($loggedUser['id']);

        // 		if(!$user_2fa_ap_status){

        // 			$this->Az->redirect('retailer/fingpayAeps/memberRegister', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
        // 		}

        $siteUrl = base_url();

        // get bank list
        $bankList = $this->db->get('aeps_bank_list')->result_array();

        $data = [
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser' => $loggedUser,
            'bankList' => $bankList,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'fingpayaeps/list',
        ];
        $this->parser->parse('retailer/layout/column-1', $data);
    }

    public function activeAeps()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];

        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AUTHORIZE_ERROR'));
        }

        $user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);

        if ($user_aeps_status) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AEPS_MEMBER_ERROR'));
        }

        // check already kyc approved or not
        $chk_kyc = $this->db->get_where('fingpay_aeps_member_kyc', ['account_id' => $account_id, 'member_id' => $memberID, 'status' => 1])->num_rows();
        if ($chk_kyc) {
            $this->db->where('id', $memberID);
            $this->db->update('users', ['aeps_status' => 1]);

            $this->Az->redirect('retailer/fingpayaeps', 'system_message_error', lang('AEPS_ACTIVE_SUCCESS'));
        }

        $memberData = $this->db->get_where('users', ['id' => $memberID, 'fingpay_aeps_status' => 0])->row_array();

        // get state list
        $stateList = $this->db
            ->order_by('state', 'asc')
            ->get('aeps_state')
            ->result_array();

        $siteUrl = base_url();

        $data = [
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser' => $loggedUser,
            'memberID' => $memberID,
            'memberData' => $memberData,
            'stateList' => $stateList,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'fingpayaeps/member-activation',
        ];
        $this->parser->parse('retailer/layout/column-1', $data);
    }

    // save member
    public function activeAuth()
	{
		$post = $this->input->post();
		$response = [];
		$validationErrors = [];
        $uploadedFiles = [];
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];

        $activeService = $this->User->account_active_service($memberID);
        if (!in_array(25, $activeService)) {
            $response = [
                'error' => true,
                'auth_errors' => 'Sorry ! You are not authorized to access this page.'
            ];
            echo json_encode($response);
            return;
        }

        $user_aeps_status = $this->User->get_member_fingpay_aeps_status($memberID);
        if ($user_aeps_status) {
            $response = [
                'error' => true,
                'auth_errors' => 'Sorry ! Fingpay AEPS already activated for this member.'
            ];
            echo json_encode($response);
            return;
        }

		$this->load->library('form_validation');
		// Set validation rules for fields
		$this->form_validation->set_rules('first_name', 'First Name', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('middle_name', 'Middle Name', 'trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('father_name', 'Father Name', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('mother_name', 'Mother Name', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('person_dob', 'User Date Of Birth', 'required|trim|xss_clean');
		$this->form_validation->set_rules('gender', 'Gender', 'required|trim|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|valid_email');
		$this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|trim|xss_clean|numeric|min_length[12]|max_length[12]');
		$this->form_validation->set_rules('pancard_no', 'Pancard No', 'required|xss_clean|min_length[10]|max_length[10]|regex_match[/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/]');
		$this->form_validation->set_rules('street_locality', 'Street/Locality', 'required|trim|xss_clean');
		$this->form_validation->set_rules('address', 'Aadhar Card Back Address', 'required|trim|xss_clean');
		$this->form_validation->set_rules('shop_business_name', 'Shop/Business Name', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('shop_business_address', 'Shop/Business Address', 'required|trim|xss_clean');
		$this->form_validation->set_rules('business_type', 'Business Type', 'required|xss_clean');
		$this->form_validation->set_rules('selState', 'State', 'required|xss_clean');
		$this->form_validation->set_rules('city_id', 'City', 'required|xss_clean');
		$this->form_validation->set_rules('pin_code', 'Pincode', 'required|trim|xss_clean|numeric|min_length[6]|max_length[6]');
		$this->form_validation->set_rules('village', 'Village', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('post_office', 'Post office', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('police_station', 'Police Station', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('block', 'Block', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('district', 'District', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('bank_branch_name', 'Bank Branch Name', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
		$this->form_validation->set_rules('account_no', 'Bank Account', 'required|trim|xss_clean|regex_match[/^[a-zA-Z0-9]+( [a-zA-Z0-9]+)*$/]');
		$this->form_validation->set_rules('bank_ifsc', 'Bank Ifsc', 'required|trim|xss_clean|alpha_numeric');

		$this->form_validation->set_rules('mobile', 'Mobile', 'required|trim|xss_clean|numeric|min_length[10]|max_length[10]|regex_match[/^[6789]\d{9}$/]');

		$files = [
			'aadharfront_photo' => true,
			'aadharback_photo'  => true,
			'pancard_photo'     => true,
			'user_photo'        => true,
			'bps_photo'         => false,
			'shop_photo'        => false,
		];

		$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
		$maxSizeKB = 2048; // 2 MB
		$validationErrors = []; // Initialize validation errors array

		foreach ($files as $fileField => $isRequired) {
			if (!empty($_FILES[$fileField]['name'])) {
				$fileTmpName = $_FILES[$fileField]['tmp_name'];
				$fileSize = $_FILES[$fileField]['size'];
				$fileType = mime_content_type($fileTmpName);

				// Validate file type
				if (!in_array($fileType, $allowedTypes)) {
					$validationErrors[$fileField][] = ucfirst(str_replace('_', ' ', $fileField)) .
						" must be a valid image file (jpg, jpeg, png).";
				}

				// Validate file size
				if ($fileSize > $maxSizeKB * 1024) {
					$validationErrors[$fileField][] = ucfirst(str_replace('_', ' ', $fileField)) .
						" must not exceed {$maxSizeKB} KB.";
				}
			} elseif ($isRequired) {
				// If file is required but not uploaded
				$validationErrors[$fileField][] = ucfirst(str_replace('_', ' ', $fileField)) . " is required.";
			}
		}

		// Run form validation
		if ($this->form_validation->run() === false || !empty($validationErrors)) {
			$response = [
				'error' => true,
				'errors' => $this->form_validation->error_array(),
				'imageErrors' => $validationErrors
			];
			log_message('debug', ' Fingpay activeAuth Incici Api Response Data - ' . json_encode($response));
			log_message('debug', ' Fingpay activeAuth Post Response Data - ' . json_encode($post));

			echo json_encode($response);
			return;
		} else {

            $statusCheckResponse = $this->FingpayAeps_model->checkAepsStatusLive($memberID);
            if ($statusCheckResponse == true) {
                // get member address
                $getAddress = $this->db
                    ->select('address,pin_code')
                    ->order_by('id', 'desc')
                    ->get_where('fingpay_aeps_member_kyc', ['account_id' => $account_id, 'member_id' => $memberID])
                    ->row_array();
                $address = isset($getAddress['pin_code']) ? $getAddress['pin_code'] : '';

                // update aeps status
                $this->db->where('account_id', $account_id);
                $this->db->where('id', $memberID);
                $this->db->update('users', ['fingpay_aeps_status' => 1]);

                $this->db->where('account_id', $account_id);
                $this->db->where('member_id', $memberID);
                $this->db->update('fingpay_aeps_member_kyc', ['status' => 1]);
                $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AEPS_ACTIVE_SUCCESS'));

                $response = [
					'error'   => false,
					'is_api_error' => false,
					'dataval' => 'Congratulations ! Member AEPS service is activated now.',
					'redirectUrl' => 'retailer/iciciaeps/otpVerify/' . $response['otpReferenceID']
				];
				log_message('debug', ' activeAuth API Success Response Data - ' . json_encode($response));
				echo json_encode($response);
				return;

            } else {

                foreach ($files as $fileField => $isRequired) {
                    $uploadedFiles[$fileField] = $this->_upload_file($fileField);

                    // Check if a required file fails to upload
                    if ($isRequired && !$uploadedFiles[$fileField]) {
                        $response = [
                            'error'   => true,
                            'dataval' => ucfirst(str_replace('_', ' ', $fileField)) . " upload failed.",
                        ];

                        log_message('debug', 'Fingpay activeAuth Post Image Docs Response Data - ' . json_encode($response));
                        echo json_encode($response);
                        return;
                    }
                }
                $response = $this->FingpayAeps_model->activeAEPSMember($post, ...array_values($uploadedFiles));
                $status = $response['status'];

                if ($status == 1) {
                    $encodeFPTxnId = $response['encodeFPTxnId'];
                    $response = [
                        'error'   => false,
                        'is_api_error' => false,
                        'dataval' => 'Congratulations ! Member AEPS service is activated now.',
                        'redirectUrl' => 'retailer/iciciaeps/otpVerify/' . $response['otpReferenceID']
                    ];
                    log_message('debug', 'Fingpay activeAuth API Success Response Data - ' . json_encode($response));
                    echo json_encode($response);

                    $this->Az->redirect('retailer/fingpayAeps/otpVerify/' . $encodeFPTxnId, 'system_message_error', lang('AEPS_OTP_SUCCESS'));
                } else {
                    $response = [
                        'error'   => true,
                        'is_api_error' => true,
                        'dataval' => 'Sorry ! Activation failed due to '.$response['msg'],
                        'redirectUrl' => 'retailer/fingpayAeps/activeAeps'
                    ];
                    log_message('debug', 'Fingpay activeAuth API Success Response Data - ' . json_encode($response));
                    echo json_encode($response);
                }
            }
        }
    }

    public function otpVerify($encodeFPTxnId = '')
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];

        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AUTHORIZE_ERROR'));
        }

        $user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);

        if ($user_aeps_status) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AEPS_MEMBER_ERROR'));
        }

        // check already kyc approved or not
        $chk_encode_id = $this->db->get_where('fingpay_aeps_member_kyc', ['account_id' => $account_id, 'member_id' => $memberID, 'encodeFPTxnId' => $encodeFPTxnId, 'status' => 0])->num_rows();
        if (!$chk_encode_id) {
            $this->Az->redirect('retailer/fingpayAeps/activeAeps', 'system_message_error', lang('AEPS_ENCODED_ID_ERROR'));
        }

        $memberData = $this->db->get_where('users', ['id' => $memberID, 'fingpay_aeps_status' => 0])->row_array();

        $siteUrl = base_url();

        $data = [
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser' => $loggedUser,
            'memberID' => $memberID,
            'memberData' => $memberData,
            'encodeFPTxnId' => $encodeFPTxnId,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'fingpayaeps/otp-verify',
        ];
        $this->parser->parse('retailer/layout/column-1', $data);
    }

    // save member
    public function otpAuth()
    {
        //check for foem validation
        $post = $this->input->post();
        $memberID = $post['memberID'];

        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];

        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AUTHORIZE_ERROR'));
        }

        $user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);

        if ($user_aeps_status) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AEPS_MEMBER_ERROR'));
        }

        $encodeFPTxnId = $post['encodeFPTxnId'];

        if (!isset($post['otp_code']) || $post['otp_code'] == '') {
            $this->Az->redirect('retailer/FingpayAeps/otpVerify/' . $encodeFPTxnId, 'system_message_error', lang('AEPS_OTP_ERROR'));
        }

        $response = $this->FingpayAeps_model->aepsOTPAuth($post, $memberID, $encodeFPTxnId);
        $status = $response['status'];

        if ($status == 1) {
            $this->Az->redirect('retailer/FingpayAeps/capture/' . $encodeFPTxnId, 'system_message_error', lang('AEPS_OTP_VERIFY_SUCCESS'));
        } else {
            $this->Az->redirect('retailer/FingpayAeps/activeAeps', 'system_message_error', sprintf(lang('AEPS_ACTIVE_FAILED'), $response['msg']));
        }
    }

    public function resendOtp($encodeFPTxnId = '')
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];

        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AUTHORIZE_ERROR'));
        }

        $user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);

        if ($user_aeps_status) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AEPS_MEMBER_ERROR'));
        }

        // check already kyc approved or not
        $chk_encode_id = $this->db->get_where('aeps_member_kyc', ['account_id' => $account_id, 'member_id' => $memberID, 'encodeFPTxnId' => $encodeFPTxnId, 'status' => 0])->num_rows();
        if (!$chk_encode_id) {
            $this->Az->redirect('retailer/FingpayAeps/activeAeps', 'system_message_error', lang('AEPS_ENCODED_ID_ERROR'));
        }

        $response = $this->FingpayAeps_model->aepsResendOtp($memberID, $encodeFPTxnId);
        $status = $response['status'];

        if ($status == 1) {
            $this->Az->redirect('retailer/FingpayAeps/otpVerify/' . $encodeFPTxnId, 'system_message_error', lang('AEPS_OTP_RESEND_SUCCESS'));
        } else {
            $this->Az->redirect('retailer/FingpayAeps/activeAeps', 'system_message_error', sprintf(lang('AEPS_ACTIVE_FAILED'), $response['msg']));
        }
    }

    public function capture($encodeFPTxnId = '')
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];

        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AUTHORIZE_ERROR'));
        }

        $user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);

        if ($user_aeps_status) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AEPS_MEMBER_ERROR'));
        }

        // check already kyc approved or not
        $chk_encode_id = $this->db->get_where('fingpay_aeps_member_kyc', ['account_id' => $account_id, 'member_id' => $memberID, 'encodeFPTxnId' => $encodeFPTxnId, 'status' => 0])->num_rows();
        if (!$chk_encode_id) {
            $this->Az->redirect('retailer/fingpayAeps/activeAeps', 'system_message_error', lang('AEPS_ENCODED_ID_ERROR'));
        }

        $memberData = $this->db->get_where('users', ['id' => $memberID, 'fingpay_aeps_status' => 0])->row_array();

        $siteUrl = base_url();

        $data = [
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser' => $loggedUser,
            'memberID' => $memberID,
            'memberData' => $memberData,
            'encodeFPTxnId' => $encodeFPTxnId,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'fingpayaeps/capture',
        ];
        $this->parser->parse('retailer/layout/column-1', $data);
    }

    public function getCityList($state_id = 0)
    {
        // get state name
        $get_state_name = $this->db->get_where('aeps_state', ['id' => $state_id])->row_array();
        $state_name = isset($get_state_name['state']) ? $get_state_name['state'] : '';
        $str = '<option value="">Select City</option>';
        if ($state_name) {
            // get city list
            $cityList = $this->db
                ->order_by('city_name', 'asc')
                ->get_where('city', ['state_name' => $state_name])
                ->result_array();
            if ($cityList) {
                foreach ($cityList as $list) {
                    $str .= '<option value="' . $list['city_id'] . '">' . $list['city_name'] . '</option>';
                }
            }
        }
        echo json_encode(['status' => 1, 'str' => $str]);
    }

    public function kycBioAuth()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];
        $activeService = $this->User->account_active_service($loggedUser['id']);
        $response = [];
        if (!in_array(25, $activeService)) {
            $response = [
                'status' => 0,
                'msg' => 'Sorry ! AEPS Service Not Active.',
            ];
        } else {
            $post = file_get_contents('php://input');
            $post = json_decode($post, true);
            if ($post) {
                $encodeFPTxnId = $post['encodeFPTxnId'];
                $biometricData = $post['BiometricData'];
                $iin = '';
                $requestTime = date('Y-m-d H:i:s');
                $txnID = 'FIAK' . time();

                $memberData = $this->db->get_where('users', ['id' => $memberID])->row_array();
                $member_code = $memberData['user_code'];

                // check already kyc approved or not
                $get_kyc_data = $this->db->get_where('fingpay_aeps_member_kyc', ['account_id' => $account_id, 'member_id' => $memberID, 'encodeFPTxnId' => $encodeFPTxnId, 'status' => 0])->row_array();
                $primaryKeyId = isset($get_kyc_data['primaryKeyId']) ? $get_kyc_data['primaryKeyId'] : '';
                $encodeFPTxnId = isset($get_kyc_data['encodeFPTxnId']) ? $get_kyc_data['encodeFPTxnId'] : '';
                $pancard_no = isset($get_kyc_data['pancard_no']) ? $get_kyc_data['pancard_no'] : '';
                $aadharNumber = isset($get_kyc_data['aadhar_no']) ? $get_kyc_data['aadhar_no'] : '';
                $mobile = isset($get_kyc_data['mobile']) ? $get_kyc_data['mobile'] : '';
                $recordID = isset($get_kyc_data['id']) ? $get_kyc_data['id'] : 0;

                $bmPIData = simplexml_load_string($biometricData);
                $xmlarray = json_decode(json_encode((array) $bmPIData), true);

                $serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
                $piddatatype = $bmPIData->Data[0]['type'];
                $ci = $bmPIData->Skey[0]['ci'];
                if ($xmlarray['Resp']['@attributes']['errCode'] == 0) {
                    $captureData = [
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
                        'Piddata' => $xmlarray['Data'],
                    ];
                    $captureData = json_decode(json_encode((array) $captureData), true);
                    $captureData['ci'] = $captureData['ci'][0];
                    $captureData['PidDatatype'] = $captureData['PidDatatype'][0];

                    // Create Data
                    $data = [
                        "superMerchantId" => $accountData['aeps_supermerchant_id'],
                        "merchantLoginId" => $member_code,
                        "primaryKeyId" => $primaryKeyId,
                        "encodeFPTxnId" => $encodeFPTxnId,
                        "requestRemarks" => "EKYC Biomatric",
                        "cardnumberORUID" => [
                            "nationalBankIdentificationNumber" => null,
                            "indicatorforUID" => "0",
                            "adhaarNumber" => $aadharNumber,
                        ],
                        "captureResponse" => $captureData,
                    ];

                    // Generate JSON
                    $json = json_encode($data);

                    // Generate Session Key
                    $key = '';
                    $mt_rand = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
                    foreach ($mt_rand as $chr) {
                        $key .= chr($chr);
                    }

                    // Read Public Key
                    $pub_key_string = file_get_contents('fingpay_public_production.txt');

                    // Encrypt using Public Key
                    openssl_public_encrypt($key, $crypttext, $pub_key_string);

                    // Create Header
                    $header = ['Content-type: application/json', 'trnTimestamp: ' . date('d/m/Y H:i:s'), 'hash: ' . base64_encode(hash('sha256', $json, true)), 'eskey: ' . base64_encode($crypttext), 'deviceIMEI:' . $serialno];

                    // Initialization Vector
                    $iv = '06f2f04cc530364f';

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
                    curl_close($curl);

                    $responseData = json_decode($output, true);
                    $finalResponse = isset($responseData['message']) ? json_decode($responseData['message'], true) : [];

                    $apiData = [
                        'account_id' => $account_id,
                        'user_id' => $memberID,
                        'api_url' => $api_url,
                        'api_response' => $output,
                        'post_data' => $data,
                        'created' => date('Y-m-d H:i:s'),
                        'created_by' => $account_id,
                    ];
                    $this->db->insert('aeps_api_response', $apiData);

                    if (isset($responseData['message']) && $responseData['message'] == 'EKYC Completed Successfully') {
                        // update aeps status
                        $this->db->where('id', $memberID);
                        $this->db->update('users', ['fingpay_aeps_status' => 1]);

                        // update aeps status
                        $this->db->where('id', $recordID);
                        $this->db->update('fingpay_aeps_member_kyc', ['status' => 1, 'clear_step' => 4]);

                        $this->session->set_flashdata(
                            'system_message_error',
                            '<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulation ! Your EKYC has been approved.</div>'
                        );

                        $response = [
                            'status' => 1,
                            'msg' => 'Congratulation ! Your EKYC has been approved.',
                        ];
                    } else {
                        $response = [
                            'status' => 0,
                            'msg' => 'Sorry ! Your BiometricData not valid.',
                        ];
                    }
                } else {
                    $response = [
                        'status' => 0,
                        'msg' => 'Somethis Wrong ! Please Try Again Later.',
                    ];
                }
            }
        }

        echo json_encode($response);
    }

    public function apiAuth()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $response = [
                'status' => 0,
                'msg' => 'Sorry ! AEPS Service Not Active.',
            ];
        } else {
            $post = file_get_contents('php://input');
            $post = json_decode($post, true);
            $agentID = $loggedUser['user_code'];
            $get_member_pin = $this->db->get_where('users', ['user_code' => $agentID, 'account_id' => $account_id])->row_array();
            $member_pin = isset($get_member_pin['decoded_transaction_password']) ? $get_member_pin['decoded_transaction_password'] : '';
            $user_aeps_status = $this->User->get_member_fingpay_aeps_status($memberID);
            $serviceType = $post['ServiceType'];
            $user_2fa_ap_status = $this->User->get_member_fingpay_2fa_ap_status($loggedUser['id']);
            $response = [];

            if ($serviceType == 'aadharpay' && $user_2fa_ap_status == 0) {
                $response = [
                    'status' => 0,
                    'is_daily_login' => 1,
                    'msg' => 'Please Inititate Aadharpay Daily Login.',
                ];
            } else {
                if ($user_aeps_status) {
                    $post = file_get_contents('php://input');
                    $post = json_decode($post, true);
                    if ($post) {
                        $serviceType = $post['ServiceType'];
                        $deviceIMEI = $post['deviceIMEI'];
                        $aadharNumber = $post['AadharNumber'];
                        $mobile = $post['mobileNumber'];
                        $biometricData = $post['BiometricData'];
                        $amount = $post['Amount'];
                        $iin = $post['IIN'];

                        $requestTime = date('Y-m-d H:i:s');
                        if ($aadharNumber && $mobile && $biometricData && $iin) {
                            if ($serviceType == 'balinfo' || $serviceType == 'ministatement') {
                                $txnType = 'BE';
                                $txnID = 'FIAB' . time();
                                $is_bal_info = 1;
                                $remarks = 'Balance Inquiry';
                                $is_withdrawal = 0;
                                $Servicestype = 'GetBalanceaeps';
                                $api_url = FINGPAY_AEPS_BE_API_URL;
                                if ($serviceType == 'ministatement') {
                                    $txnID = 'FIMS' . time();
                                    $Servicestype = 'getministatment';
                                    $is_bal_info = 0;
                                    $txnType = 'MS';
                                    $remarks = 'Mini Statement';
                                    $api_url = FINGPAY_AEPS_MS_API_URL;
                                }
                                if ($amount == 0) {
                                    $bmPIData = simplexml_load_string($biometricData);
                                    $xmlarray = json_decode(json_encode((array) $bmPIData), true);

                                    $serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
                                    $piddatatype = $bmPIData->Data[0]['type'];
                                    $ci = $bmPIData->Skey[0]['ci'];
                                    if ($xmlarray['Resp']['@attributes']['errCode'] == 0) {
                                        $captureData = [
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
                                            'Piddata' => $xmlarray['Data'],
                                        ];
                                        $captureData = json_decode(json_encode((array) $captureData), true);
                                        $captureData['ci'] = $captureData['ci'][0];

                                        if ($agentID == 'PAOLR243059') {
                                            $member_pin = md5(123456);
                                        } elseif ($agentID == 'PAOLR034568') {
                                            $member_pin = '0d5a4a5a748611231b945d28436b8ece';
                                        } elseif ($agentID == 'PAOLR783496') {
                                            $member_pin = '8a1ee9f2b7abe6e88d1a479ab6a42c5e';
                                        } elseif ($agentID == 'MPCNR439065' || $agentID == 'MPCNR340897') {
                                            $member_pin = md5(123456);
                                        } elseif ($agentID == 'PAOLR027168') {
                                            $member_pin = 'd93a5def7511da3d0f2d171d9c344e91';
                                        } elseif ($agentID == 'PAOLR237604') {
                                            $member_pin = '81dc9bdb52d04dc20036dbd8313ed055';
                                        } else {
                                            $member_pin = md5(123456);
                                            $member_pin = md5($member_pin);
                                        }

                                        // Create Data
                                        $data = [
                                            "cardnumberORUID" => [
                                                "nationalBankIdentificationNumber" => $iin,
                                                "indicatorforUID" => "0",
                                                "adhaarNumber" => $aadharNumber,
                                            ],

                                            "captureResponse" => $captureData,
                                            "languageCode" => "en",
                                            "latitude" => "22.9734229",
                                            "longitude" => "78.6568942",
                                            "mobileNumber" => $mobile,
                                            "paymentType" => "B",
                                            "requestRemarks" => $remarks,
                                            "timestamp" => date('d/m/Y H:i:s'),
                                            "merchantUserName" => $agentID,
                                            "merchantPin" => $member_pin,
                                            "subMerchantId" => "",
                                            "superMerchantId" => $accountData['aeps_supermerchant_id'],
                                            "transactionType" => $txnType,
                                        ];
                                        if ($serviceType == 'balinfo') {
                                            $data["merchantTransactionId"] = $txnID;
                                        } else {
                                            $data["merchantTranId"] = $txnID;
                                        }

                                        // Generate JSON
                                        $json = json_encode($data);

                                        // Generate Session Key
                                        $key = '';
                                        $mt_rand = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
                                        foreach ($mt_rand as $chr) {
                                            $key .= chr($chr);
                                        }

                                        // Read Public Key
                                        $pub_key_string = file_get_contents('fingpay_public_production.txt');

                                        // Encrypt using Public Key
                                        openssl_public_encrypt($key, $crypttext, $pub_key_string);

                                        // Create Header
                                        $header = [
                                            'Content-type: application/json',
                                            'trnTimestamp: ' . date('d/m/Y H:i:s'),
                                            'hash: ' . base64_encode(hash('sha256', $json, true)),
                                            'eskey: ' . base64_encode($crypttext),
                                            'deviceIMEI:' . $serialno,
                                        ];

                                        // Initialization Vector
                                        $iv = '06f2f04cc530364f';

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
                                        curl_close($curl);

                                        $responseData = json_decode($output, true);

                                        $apiData = [
                                            'account_id' => $account_id,
                                            'user_id' => $memberID,
                                            'api_url' => $api_url,
                                            'api_response' => $output,
                                            'post_data' => json_encode($post),
                                            'created' => date('Y-m-d H:i:s'),
                                            'created_by' => $memberID,
                                        ];
                                        $this->db->insert('aeps_api_response', $apiData);

                                        if (isset($responseData['message']) && $responseData['message'] == 'Request Completed') {
                                            $statementList = $responseData['data']['miniStatementStructureModel'];
                                            $balanceAmount = $responseData['data']['balanceAmount'];
                                            $bankRRN = $responseData['data']['bankRRN'];
                                            $recordID = $this->FingpayAeps_model->saveAepsTxn(
                                                $txnID,
                                                $serviceType,
                                                $aadharNumber,
                                                $mobile,
                                                $amount,
                                                $iin,
                                                $api_url,
                                                $output,
                                                $responseData['message'],
                                                2,
                                                $statementList,
                                                $balanceAmount,
                                                $bankRRN
                                            );
                                            $str = '';
                                            if ($is_bal_info == 0) {
                                                $this->FingpayAeps_model->addStatementCom($txnID, $aadharNumber, $iin, $amount, $recordID);

                                                if ($statementList) {
                                                    $str = '<div class="table-responsive">';
                                                    $str .= '<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
                                                    $str .= '<tr>';
                                                    $str .= '<th>#</th>';
                                                    $str .= '<th>Date</th>';
                                                    $str .= '<th>CR/DR</th>';
                                                    $str .= '<th>Amount</th>';
                                                    $str .= '<th>Description</th>';
                                                    $str .= '</tr>';
                                                    $i = 1;
                                                    foreach ($statementList as $list) {
                                                        $str .= '<tr>';
                                                        $str .= '<td>' . $i . '</td>';
                                                        $str .= '<td>' . $list['date'] . '</td>';
                                                        if ($list['txnType'] == 'Dr') {
                                                            $str .= '<td><font color="red">DR</font></td>';
                                                        } else {
                                                            $str .= '<td><font color="green">CR</font></td>';
                                                        }
                                                        $str .= '<td>INR ' . $list['amount'] . '/-</td>';
                                                        $str .= '<td>' . $list['narration'] . '</td>';
                                                        $str .= '</tr>';
                                                        $i++;
                                                    }
                                                    $str .= '</table>';
                                                    $str .= '</div>';
                                                }
                                            }
                                            $response = [
                                                'status' => 1,
                                                'msg' => $responseData['message'],
                                                'balanceAmount' => $responseData['data']['balanceAmount'],
                                                'bankRRN' => $responseData['data']['bankRRN'],
                                                'is_bal_info' => $is_bal_info,
                                                'is_withdrawal' => $is_withdrawal,
                                                'str' => $str,
                                            ];
                                        } else {
                                            $this->FingpayAeps_model->saveAepsTxn($txnID, $serviceType, $aadharNumber, $mobile, $amount, $iin, $api_url, $output, $responseData['message'], 3);
                                            $response = [
                                                'status' => 0,
                                                'msg' => $responseData['message'],
                                            ];
                                        }
                                    } else {
                                        $response = [
                                            'status' => 0,
                                            'msg' => 'Sorry ! Amount is not valid.',
                                        ];
                                    }
                                }
                            } elseif ($serviceType == 'balwithdraw' || $serviceType == 'aadharpay') {
                                $txnType = 'CW';
                                $txnID = 'FIAW' . time();
                                $is_withdrawal = 1;
                                $is_bal_info = 0;
                                $api_url = FINGPAY_AEPS_CW_API_URL;
                                $Servicestype = 'AccountWithdrowal';
                                if ($serviceType == 'aadharpay') {
                                    $txnType = 'M';
                                    $Servicestype = 'Aadharpay';
                                    $txnID = 'FIAP' . time();
                                    $api_url = FINGPAY_AEPS_AP_API_URL;
                                }

                                if ($amount >= 50 && $amount <= 10000) {
                                    $bmPIData = simplexml_load_string($biometricData);
                                    $xmlarray = json_decode(json_encode((array) $bmPIData), true);

                                    $serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
                                    $piddatatype = $bmPIData->Data[0]['type'];
                                    $ci = $bmPIData->Skey[0]['ci'];
                                    if ($xmlarray['Resp']['@attributes']['errCode'] == 0) {
                                        $captureData = [
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
                                            'Piddata' => $xmlarray['Data'],
                                        ];
                                        $captureData = json_decode(json_encode((array) $captureData), true);
                                        $captureData['ci'] = $captureData['ci'][0];

                                        if ($agentID == 'PAOLR243059') {
                                            $member_pin = md5(123456);
                                        } elseif ($agentID == 'PAOLR034568') {
                                            $member_pin = '0d5a4a5a748611231b945d28436b8ece';
                                        } elseif ($agentID == 'PAOLR783496') {
                                            $member_pin = '8a1ee9f2b7abe6e88d1a479ab6a42c5e';
                                        } elseif ($agentID == 'MPCNR439065' || $agentID == 'MPCNR340897') {
                                            $member_pin = md5(123456);
                                        } elseif ($agentID == 'PAOLR027168') {
                                            $member_pin = 'd93a5def7511da3d0f2d171d9c344e91';
                                        } elseif ($agentID == 'PAOLR237604') {
                                            $member_pin = '81dc9bdb52d04dc20036dbd8313ed055';
                                        } else {
                                            $member_pin = md5(123456);
                                            $member_pin = md5($member_pin);
                                        }

                                        // Create Data
                                        $data = [
                                            "cardnumberORUID" => [
                                                "nationalBankIdentificationNumber" => $iin,
                                                "indicatorforUID" => "0",
                                                "adhaarNumber" => $aadharNumber,
                                            ],
                                            "captureResponse" => $captureData,
                                            "languageCode" => "en",
                                            "latitude" => "22.9734229",
                                            "longitude" => "78.6568942",
                                            "mobileNumber" => $mobile,
                                            "paymentType" => "B",
                                            "requestRemarks" => $remarks,
                                            "timestamp" => date('d/m/Y H:i:s'),
                                            "merchantUserName" => $agentID,
                                            "merchantPin" => $member_pin,
                                            "subMerchantId" => "",
                                            "superMerchantId" => $accountData['aeps_supermerchant_id'],
                                            "transactionType" => $txnType,
                                            "merchantTranId" => $txnID,
                                            "transactionAmount" => $amount,
                                        ];

                                        // Generate JSON
                                        $json = json_encode($data);

                                        // Generate Session Key
                                        $key = '';
                                        $mt_rand = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
                                        foreach ($mt_rand as $chr) {
                                            $key .= chr($chr);
                                        }

                                        // Read Public Key
                                        $pub_key_string = $accountData['aeps_certificate'];

                                        // Encrypt using Public Key
                                        openssl_public_encrypt($key, $crypttext, $pub_key_string);

                                        // Create Header
                                        $header = [
                                            'Content-type: application/json',
                                            'trnTimestamp: ' . date('d/m/Y H:i:s'),
                                            'hash: ' . base64_encode(hash('sha256', $json, true)),
                                            'eskey: ' . base64_encode($crypttext),
                                            'deviceIMEI:' . $serialno,
                                        ];

                                        // Initialization Vector
                                        $iv = '06f2f04cc530364f';

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
                                        curl_close($curl);

                                        $responseData = json_decode($output, true);

                                        $apiData = [
                                            'account_id' => $account_id,
                                            'user_id' => $memberID,
                                            'api_url' => $api_url,
                                            'api_response' => $output,
                                            'created' => date('Y-m-d H:i:s'),
                                            'created_by' => $memberID,
                                        ];
                                        $this->db->insert('aeps_api_response', $apiData);

                                        if (isset($responseData['message']) && $responseData['message'] == 'Request Completed') {
                                            $balanceAmount = $responseData['data']['balanceAmount'];
                                            $bankRRN = $responseData['data']['bankRRN'];
                                            $transactionAmount = $responseData['data']['transactionAmount'];
                                            $statementList = [];
                                            $recordID = $this->FingpayAeps_model->saveAepsTxn(
                                                $txnID,
                                                $serviceType,
                                                $aadharNumber,
                                                $mobile,
                                                $amount,
                                                $iin,
                                                $api_url,
                                                $output,
                                                $responseData['message'],
                                                2,
                                                $statementList,
                                                $balanceAmount,
                                                $bankRRN,
                                                $transactionAmount
                                            );
                                            $this->FingpayAeps_model->addBalance($txnID, $aadharNumber, $iin, $amount, $recordID, $serviceType);
                                            $str = '';
                                            $str = '<div class="table-responsive">';
                                            $str .= '<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
                                            $str .= '<tr>';
                                            $str .= '<td>Txn Status</td><td><font color="green">Successful</font></td>';
                                            $str .= '</tr>';

                                            $str .= '<tr>';
                                            $str .= '<td>Withdrawal Amount</td><td>INR ' . $responseData['data']['transactionAmount'] . '/-</td>';
                                            $str .= '</tr>';

                                            $str .= '<tr>';
                                            $str .= '<td>Balance Amount</td><td>INR ' . $responseData['data']['balanceAmount'] . '/-</td>';
                                            $str .= '</tr>';

                                            $str .= '<tr>';
                                            $str .= '<td>Bank RRN</td><td>' . $responseData['data']['bankRRN'] . '</td>';
                                            $str .= '</tr>';

                                            $str .= '</table>';
                                            $str .= '</div>';

                                            $response = [
                                                'status' => 1,
                                                'msg' => $responseData['message'],
                                                'balanceAmount' => $responseData['data']['balanceAmount'],
                                                'bankRRN' => $responseData['data']['bankRRN'],
                                                'is_bal_info' => $is_bal_info,
                                                'is_withdrawal' => $is_withdrawal,
                                                'str' => $str,
                                            ];
                                        } else {
                                            $this->FingpayAeps_model->saveAepsTxn($txnID, $serviceType, $aadharNumber, $mobile, $amount, $iin, $api_url, $output, $responseData['message'], 3);
                                            $response = [
                                                'status' => 0,
                                                'msg' => $responseData['message'],
                                            ];
                                        }
                                    } else {
                                        $response = [
                                            'status' => 0,
                                            'msg' => 'Somethis Wrong ! Please Try Again Later.',
                                        ];
                                    }
                                } else {
                                    $response = [
                                        'status' => 0,
                                        'msg' => 'Sorry ! Amount should be less than 10000 and grater than or equal 50.',
                                    ];
                                }
                            } else {
                                $response = [
                                    'status' => 0,
                                    'msg' => 'Sorry ! Please enter required data.',
                                ];
                            }
                        } else {
                            $response = [
                                'status' => 0,
                                'msg' => 'Somethis Wrong ! Please Try Again Later.',
                            ];
                        }
                    } else {
                        $response = [
                            'status' => 0,
                            'msg' => 'Sorry ! AEPS not activated.',
                        ];
                    }
                }
            }
        }
        log_message('debug', ' Fingpay Aeps Api Response Data - ' . json_encode($response));
        echo json_encode($response);
    }

    public function transactionHistory()
    {
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AUTHORIZE_ERROR'));
        }

        $siteUrl = base_url();

        $data = [
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser' => $loggedUser,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'fingpayaeps/transaction-list',
        ];
        $this->parser->parse('retailer/layout/column-1', $data);
    }

    public function getTransactionList()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $requestData = $this->input->get();
        $extra_search = $requestData['extra_search'];
        $keyword = '';
        $fromDate = '';
        $toDate = '';
        $status = 0;
        $service = '';
        if ($extra_search) {
            $filterData = explode('|', $extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $service = isset($filterData[4]) ? trim($filterData[4]) : '';
        }

        $columns = [
            // datatable column index  => database column name
            0 => 'a.created',
        ];

        // getting total number records without any search
        $sql = "SELECT a.* FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";

        $totalData = $this->db->query($sql)->num_rows();
        $totalFiltered = $totalData; // when there is no search parameter then total number rows = total number filtered rows.

        $sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";

        if ($keyword != '') {
            $sql .= " AND ( a.memberID LIKE '" . $keyword . "%' ";
            $sql .= " OR a.account_holder_name LIKE '" . $keyword . "%'";
            $sql .= " OR a.account_no LIKE '" . $keyword . "%'";
            $sql .= " OR a.transaction_id LIKE '" . $keyword . "%'";
            $sql .= " OR a.transfer_amount LIKE '" . $keyword . "%' )";
        }

        if ($status) {
            $sql .= " AND status = '$status'";
        }

        if ($service != '') {
            $sql .= " AND service = '$service'";
        }

        if ($fromDate && $toDate) {
            $sql .= " AND DATE(a.created) >= '" . date('Y-m-d', strtotime($fromDate)) . "' AND DATE(a.created) <= '" . date('Y-m-d', strtotime($toDate)) . "'";
        }

        $order_type = $requestData['order'][0]['dir'];
        //if($requestData['draw'] == 1)
        //	$order_type = 'DESC';

        $order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0 ? 0 : $requestData['order'][0]['column']) : 0;
        $totalFiltered = $this->db->query($sql)->num_rows();
        $sql .= " ORDER BY " . $columns[$order_no] . "   " . $order_type . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";

        $get_filter_data = $this->db->query($sql)->result_array();

        $sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";

        if ($keyword != '') {
            $sql_summery .= " AND ( b.user_code LIKE '%" . $keyword . "%' ";
            $sql_summery .= " OR b.name LIKE '%" . $keyword . "%'";
            $sql_summery .= " OR a.mobile LIKE '%" . $keyword . "%'";
            $sql_summery .= " OR a.aadhar_no LIKE '%" . $keyword . "%'";
            $sql_summery .= " OR a.amount LIKE '%" . $keyword . "%'";
            $sql_summery .= " OR a.service LIKE '%" . $keyword . "%'";
            $sql_summery .= " OR a.message LIKE '%" . $keyword . "%'";
            $sql_summery .= " OR a.txnID LIKE '%" . $keyword . "%' )";
        }

        $sql_summery .= " ) as x WHERE x.id > 0";

        if ($firstLoad == 1) {
            $sql_summery .= " AND DATE(created) = '" . date('Y-m-d') . "'";
        }

        if ($status) {
            $sql_summery .= " AND status = '$status'";
        }

        if ($service != '') {
            $sql_summery .= " AND service = '$service'";
        }

        if ($fromDate && $toDate) {
            $sql_summery .= " AND DATE(created) >= '" . date('Y-m-d', strtotime($fromDate)) . "' AND DATE(created) <= '" . date('Y-m-d', strtotime($toDate)) . "'";
        }

        $sql_success_summery = $sql_summery;
        $sql_success_summery .= " AND x.status = 2";

        $get_success_recharge = $this->db->query($sql_success_summery)->row_array();

        $successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'], 2) : '0.00';
        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;

        $sql_failed_summery = $sql_summery;
        $sql_failed_summery .= " AND x.status = 3";
        $get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();

        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'], 2) : '0.00';
        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;

        $data = [];
        $totalrecord = 0;
        if ($get_filter_data) {
            $i = 1;
            foreach ($get_filter_data as $list) {
                $get_bank_name = $this->db->get_where('aeps_bank_list', ['iinno' => $list['iinno']])->row_array();
                $bank_name = $get_bank_name['bank_name'];

                $nestedData = [];
                $nestedData[] = $i;
                $nestedData[] = $list['member_code'];
                $nestedData[] = $list['member_name'];
                if ($list['service'] == 'balwithdraw' || $list['service'] == 'aadharpay') {
                    $nestedData[] = 'Account Withdrawal';
                } elseif ($list['service'] == 'balinfo') {
                    $nestedData[] = 'Balance Info';
                } elseif ($list['service'] == 'ministatement') {
                    $nestedData[] = 'Mini Statement';
                }
                $nestedData[] = $list['aadhar_no'];
                $nestedData[] = $list['mobile'];
                $nestedData[] = $list['amount'] . ' /-';
                $nestedData[] = $list['txnID'];
                $nestedData[] = $bank_name;
                if ($list['status'] == 2) {
                    $nestedData[] = '<font color="green">Success</font>';
                } elseif ($list['status'] == 3) {
                    $nestedData[] = '<font color="red">Failed</font>';
                } else {
                    $nestedData[] = '<font color="black">Pending</font>';
                }

                if ($list['receipt_id']) {
                    $nestedData[] = '<a href="' . base_url('retailer/report/iciciAepsInvoice/' . $list['id'] . '') . '" target="_blank">' . $list['receipt_id'] . '</a>';
                } else {
                    $nestedData[] = 'Not Available';
                }

                $nestedData[] = date('d-M-Y H:i:s', strtotime($list['created']));

                $data[] = $nestedData;

                $i++;
            }
        }

        $json_data = [
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal" => intval($totalData), // total number of records
            "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data, // total data array
            //"total_selected_students" => $total_selected_students
            "successAmount" => $successAmount,
            "successRecord" => $successRecord,
            "failedAmount" => $failedAmount,
            "failedRecord" => $failedRecord,
        ];

        echo json_encode($json_data); // send data as json format
    }

    public function memberLogin()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);

        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AUTHORIZE_ERROR'));
        }

        $user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
        if (!$user_aeps_status) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AEPS_ACTIVE_ERROR'));
        }

        $siteUrl = base_url();

        $get_member_data = $this->db->get_where('fingpay_aeps_member_kyc', ['member_id' => $loggedUser['id'], 'account_id' => $account_id, 'status' => 1])->row_array();

        $data = [
			'meta_title'       => lang('SITE_NAME'),
			'meta_keywords'    => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url'         => $siteUrl,
			'loggedUser'       => $loggedUser,
			'bankList'         => $bankList,
			'account_id'       => $account_id,
			'get_member_data'  => $get_member_data,
			'system_message'   => $this->Az->getSystemMessageError(),
			'system_info'      => $this->Az->getsystemMessageInfo(),
			'system_warning'   => $this->Az->getSystemMessageWarning(),
			'content_block'    => 'fingpayaeps/member-login',
		];

        $this->parser->parse('retailer/layout/column-1', $data);
    }

    public function api2FaAuth()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $response = [
                'status' => 0,
                'msg' => 'Sorry ! AEPS Service Not Active.',
            ];
        } else {
            $agentID = $loggedUser['user_code'];
            $get_member_pin = $this->db->get_where('users', ['user_code' => $agentID, 'account_id' => $account_id])->row_array();
            $fingpay_txn_status = $get_member_pin['fingpay_every_aeps_status'];
            $fingpay_daily_txn_status = $get_member_pin['fingpay_2fa_aeps_status'];
            $fingpay_2fa_charge = $get_member_pin['fingpay_2fa_charge'];
            $member_pin = md5(123456);
            $user_aeps_status = $this->User->get_member_fingpay_aeps_status($memberID);
            $response = [];
            if ($user_aeps_status) {
                $post = file_get_contents('php://input');
                $post = json_decode($post, true);
                if ($post) {
                    $serviceType = $post['ServiceType'];
                    $deviceIMEI = $post['deviceIMEI'];

                    if ($fingpay_txn_status == 1) {
                        $aadharNumber = '';
                    } else {
                        $aadharNumber = $post['AadharNumber'];
                    }

                    $mobile = $post['mobileNumber'];
                    $biometricData = $post['BiometricData'];
                    $amount = $post['Amount'];
                    $iin = '607076';
                    $account_number = $post['account_number'];

                    $requestTime = date('Y-m-d H:i:s');
                    if ($mobile && $biometricData) {
                        if ($serviceType == '2FAAuth') {
                            $txnID = 'FIVO' . time();

                            if ($amount == 0) {
                                $bmPIData = simplexml_load_string($biometricData);
                                $xmlarray = json_decode(json_encode((array) $bmPIData), true);

                                $serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
                                $piddatatype = $bmPIData->Data[0]['type'];
                                $ci = $bmPIData->Skey[0]['ci'];
                                if ($xmlarray['Resp']['@attributes']['errCode'] == 0) {
                                    $captureData = [
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
                                        'Piddata' => $xmlarray['Data'],
                                    ];
                                    $captureData = json_decode(json_encode((array) $captureData), true);
                                    $captureData['ci'] = $captureData['ci'][0];

                                    if ($agentID == 'PAOLR968753') {
                                        $member_pin = 'e10adc3949ba59abbe56e057f20f883e';
                                    } elseif ($agentID == 'PAOLR034568') {
                                        $member_pin = '0d5a4a5a748611231b945d28436b8ece';
                                    } elseif ($agentID == 'PAOLR783496') {
                                        $member_pin = '8a1ee9f2b7abe6e88d1a479ab6a42c5e';
                                    } elseif ($agentID == 'MPCNR439065') {
                                        $member_pin = md5(123456);
                                    } elseif ($agentID == 'MPCNR340897') {
                                        $member_pin = md5(123456);
                                        log_message('debug', 'SAMU LAL MADHESIA.');
                                    } elseif ($agentID == 'PAOLR027168') {
                                        $member_pin = 'd93a5def7511da3d0f2d171d9c344e91';
                                    } elseif ($agentID == 'PAOLR237604') {
                                        $member_pin = '81dc9bdb52d04dc20036dbd8313ed055';
                                    } elseif ($agentID == 'PAOLR243059') {
                                        $member_pin = md5(123456);
                                    } else {
                                        $member_pin = md5($member_pin);
                                    }

                                    //$member_pin = md5($member_pin);

                                    // Create Data
                                    $data = [
                                        "cardnumberORUID" => [
                                            "nationalBankIdentificationNumber" => $iin,
                                            "indicatorforUID" => "0",
                                            "adhaarNumber" => $aadharNumber,
                                        ],
                                        "captureResponse" => $captureData,
                                        "latitude" => "22.9734229",
                                        "longitude" => "78.6568942",
                                        "mobileNumber" => $mobile,
                                        "serviceType" => "AEPS",
                                        "requestRemarks" => "2FA",
                                        "timestamp" => date('d/m/Y H:i:s'),
                                        "merchantUserName" => $agentID,
                                        "merchantPin" => $member_pin,
                                        "subMerchantId" => $agentID,
                                        "superMerchantId" => $accountData['aeps_supermerchant_id'],
                                        "transactionType" => "AUO",
                                    ];
                                    if ($serviceType == '2FAAuth') {
                                        $data["merchantTransactionId"] = $txnID;
                                    } else {
                                        $data["merchantTranId"] = $txnID;
                                    }

                                    // Generate JSON
                                    $json = json_encode($data);

                                    // Generate Session Key
                                    $key = '';
                                    $mt_rand = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
                                    foreach ($mt_rand as $chr) {
                                        $key .= chr($chr);
                                    }

                                    // Read Public Key
                                    $pub_key_string = file_get_contents('fingpay_public_production.txt');

                                    // Encrypt using Public Key
                                    openssl_public_encrypt($key, $crypttext, $pub_key_string);

                                    // Create Header
                                    $header = [
                                        'Content-type: application/json',
                                        'trnTimestamp: ' . date('d/m/Y H:i:s'),
                                        'hash: ' . base64_encode(hash('sha256', $json, true)),
                                        'eskey: ' . base64_encode($crypttext),
                                        'deviceIMEI:' . $serialno,
                                    ];

                                    // Initialization Vector
                                    $iv = '06f2f04cc530364f';

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
                                    curl_close($curl);

                                    $responseData = json_decode($output, true);

                                    $apiData = [
                                        'account_id' => $account_id,
                                        'user_id' => $memberID,
                                        'api_url' => $api_url,
                                        'post_data' => $json,
                                        'api_response' => $output,
                                        'created' => date('Y-m-d H:i:s'),
                                        'created_by' => $memberID,
                                        'is_2fa' => 1,
                                    ];
                                    $this->db->insert('aeps_api_response', $apiData);

                                    if (isset($responseData['message']) && $responseData['message'] == 'successful') {
                                        if ($fingpay_2fa_charge == 0) {
                                            $surcharge_amount = $this->User->get_aeps_transcation_surcharge($memberID);

                                            $user_before_balance = $this->User->getMemberWalletBalanceSP($memberID);

                                            $after_balance = $user_before_balance - $surcharge_amount;

                                            $wallet_data = [
                                                'account_id' => $account_id,
                                                'member_id' => $memberID,
                                                'before_balance' => $user_before_balance,
                                                'amount' => $surcharge_amount,
                                                'after_balance' => $after_balance,
                                                'status' => 1,
                                                'type' => 2,
                                                'created' => date('Y-m-d H:i:s'),
                                                'description' => 'AEPS 2FA DAILY  #' . $txnID . ' Amount Deducted.',
                                            ];

                                            $this->db->insert('member_wallet', $wallet_data);
                                        }

                                        $this->db->where('id', $memberID);
                                        $this->db->where('account_id', $account_id);
                                        $this->db->update('users', ['fingpay_2fa_aeps_status' => 1, 'fingpay_2fa_charge' => 1]);

                                        $response = [
                                            'status' => 1,
                                            'msg' => $responseData['message'],
                                        ];
                                    } else {
                                        $response = [
                                            'status' => 0,
                                            'msg' => $responseData['message'],
                                        ];
                                    }
                                }
                            }
                        } else {
                            $response = [
                                'status' => 0,
                                'msg' => 'Something Went Wrong !',
                            ];
                        }
                    } else {
                        $response = [
                            'status' => 0,
                            'msg' => 'Something Went Wrong !',
                        ];
                    }
                }
            }
        }

        echo json_encode($response);
    }

    // member 2fa aadhar pay

    public function memberRegister()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);

        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AUTHORIZE_ERROR'));
        }

        $user_aeps_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
        if (!$user_aeps_status) {
            $this->Az->redirect('retailer/dashboard', 'system_message_error', lang('AEPS_ACTIVE_ERROR'));
        }

        $siteUrl = base_url();

        $get_member_data = $this->db->get_where('fingpay_aeps_member_kyc', ['member_id' => $loggedUser['id'], 'account_id' => $account_id, 'status' => 1])->row_array();

        $data = [
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser' => $loggedUser,
            'bankList' => $bankList,
            'account_id' => $account_id,
            'get_member_data' => $get_member_data,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'fingpayaeps/member-register',
        ];
        $this->parser->parse('retailer/layout/column-1', $data);
    }

    public function api2FaAuthNew()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if (!in_array(25, $activeService)) {
            $response = [
                'status' => 0,
                'msg' => 'Sorry ! AEPS Service Not Active.',
            ];
        } else {
            $agentID = $loggedUser['user_code'];
            $get_member_pin = $this->db->get_where('users', ['user_code' => $agentID, 'account_id' => $account_id])->row_array();
            $member_pin = md5(123456);
            $user_aeps_status = $this->User->get_member_fingpay_aeps_status($memberID);
            $response = [];
            if ($user_aeps_status) {
                $post = file_get_contents('php://input');
                $post = json_decode($post, true);
                if ($post) {
                    $serviceType = $post['ServiceType'];
                    $deviceIMEI = $post['deviceIMEI'];
                    $aadharNumber = $post['AadharNumber'];
                    $mobile = $post['mobileNumber'];
                    $biometricData = $post['BiometricData'];
                    $amount = $post['Amount'];
                    $iin = '607076';
                    $account_number = $post['account_number'];

                    $requestTime = date('Y-m-d H:i:s');
                    if ($aadharNumber && $mobile && $biometricData) {
                        if ($serviceType == '2FAAuth') {
                            $txnID = 'FIVO' . time();

                            if ($amount == 0) {
                                $bmPIData = simplexml_load_string($biometricData);
                                $xmlarray = json_decode(json_encode((array) $bmPIData), true);

                                $serialno = $bmPIData->DeviceInfo[0]->additional_info[0]->Param[0]['value'];
                                $piddatatype = $bmPIData->Data[0]['type'];
                                $ci = $bmPIData->Skey[0]['ci'];
                                if ($xmlarray['Resp']['@attributes']['errCode'] == 0) {
                                    $captureData = [
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
                                        'Piddata' => $xmlarray['Data'],
                                    ];
                                    $captureData = json_decode(json_encode((array) $captureData), true);
                                    $captureData['ci'] = $captureData['ci'][0];

                                    if ($agentID == 'PAOLR968753') {
                                        $member_pin = 'e10adc3949ba59abbe56e057f20f883e';
                                    } elseif ($agentID == 'PAOLR034568') {
                                        $member_pin = '0d5a4a5a748611231b945d28436b8ece';
                                    } elseif ($agentID == 'PAOLR783496') {
                                        $member_pin = '8a1ee9f2b7abe6e88d1a479ab6a42c5e';
                                    } elseif ($agentID == 'MPCNR439065') {
                                        $member_pin = md5(123456);
                                    } elseif ($agentID == 'MPCNR340897') {
                                        $member_pin = md5(123456);
                                        log_message('debug', 'SAMU LAL MADHESIA.');
                                    } elseif ($agentID == 'PAOLR027168') {
                                        $member_pin = 'd93a5def7511da3d0f2d171d9c344e91';
                                    } elseif ($agentID == 'PAOLR237604') {
                                        $member_pin = '81dc9bdb52d04dc20036dbd8313ed055';
                                    } elseif ($agentID == 'PAOLR243059') {
                                        $member_pin = md5(123456);
                                    } else {
                                        $member_pin = md5($member_pin);
                                    }

                                    //$member_pin = md5(123456);

                                    // Create Data
                                    $data = [
                                        "cardnumberORUID" => [
                                            "nationalBankIdentificationNumber" => $iin,
                                            "indicatorforUID" => "0",
                                            "adhaarNumber" => $aadharNumber,
                                        ],
                                        "captureResponse" => $captureData,
                                        "latitude" => "22.9734229",
                                        "longitude" => "78.6568942",
                                        "mobileNumber" => $mobile,
                                        "serviceType" => "AP",
                                        "requestRemarks" => "2FA",
                                        "timestamp" => date('d/m/Y H:i:s'),
                                        "merchantUserName" => $agentID,
                                        "merchantPin" => $member_pin,
                                        "subMerchantId" => $agentID,
                                        "superMerchantId" => $accountData['aeps_supermerchant_id'],
                                        "transactionType" => "AUO",
                                    ];
                                    if ($serviceType == '2FAAuth') {
                                        $data["merchantTransactionId"] = $txnID;
                                    } else {
                                        $data["merchantTranId"] = $txnID;
                                    }

                                    // Generate JSON
                                    $json = json_encode($data);

                                    // Generate Session Key
                                    $key = '';
                                    $mt_rand = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
                                    foreach ($mt_rand as $chr) {
                                        $key .= chr($chr);
                                    }

                                    // Read Public Key
                                    $pub_key_string = file_get_contents('fingpay_public_production.txt');

                                    // Encrypt using Public Key
                                    openssl_public_encrypt($key, $crypttext, $pub_key_string);

                                    // Create Header
                                    $header = [
                                        'Content-type: application/json',
                                        'trnTimestamp: ' . date('d/m/Y H:i:s'),
                                        'hash: ' . base64_encode(hash('sha256', $json, true)),
                                        'eskey: ' . base64_encode($crypttext),
                                        'deviceIMEI:' . $serialno,
                                    ];

                                    // Initialization Vector
                                    $iv = '06f2f04cc530364f';

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
                                    curl_close($curl);

                                    $responseData = json_decode($output, true);

                                    $apiData = [
                                        'account_id' => $account_id,
                                        'user_id' => $memberID,
                                        'api_url' => $api_url,
                                        'post_data' => $json,
                                        'api_response' => $output,
                                        'created' => date('Y-m-d H:i:s'),
                                        'created_by' => $memberID,
                                        'is_2fa' => 1,
                                    ];
                                    $this->db->insert('aeps_api_response', $apiData);

                                    if (isset($responseData['message']) && $responseData['message'] == 'successful') {
                                        $this->db->where('id', $memberID);
                                        $this->db->where('account_id', $account_id);
                                        $this->db->update('users', ['fingpay_2fa_ap_status' => 1]);

                                        $response = [
                                            'status' => 1,
                                            'msg' => $responseData['message'],
                                        ];
                                    } else {
                                        $response = [
                                            'status' => 0,
                                            'msg' => $responseData['message'],
                                        ];
                                    }
                                }
                            }
                        } else {
                            $response = [
                                'status' => 0,
                                'msg' => 'Something Went Wrong !',
                            ];
                        }
                    } else {
                        $response = [
                            'status' => 0,
                            'msg' => 'Something Went Wrong !',
                        ];
                    }
                }
            }
        }

        echo json_encode($response);
    }

    public function update2FaStatus($member_id = 0)
    {
        $account_id = $this->User->get_domain_account();

        date_default_timezone_set('Your/Timezone'); // Set your desired timezone

        $current_time = date("Y-m-d H:i:s");

        //echo json_encode(['current_time' => $current_time]);

        if ($member_id) {
            $this->db->where('account_id', $account_id);
            $this->db->where('id', $member_id);
            $this->db->update('users', ['fingpay_2fa_aeps_status' => 0]);
        }

        echo json_encode(['status' => 1, 'str' => $str, 'current_time' => $current_time]);
    }
    // Helper function to handle file uploads
	public function _upload_file($fieldName)
	{
		$uploadPath = 'media/aeps_kyc_doc/';
		$config = [
			'upload_path'   => $uploadPath,
			'allowed_types' => 'jpg|jpeg|png|JPEG',
			'max_size'      => 2048,
			'file_name'     => time() . rand(111111, 999999),
		];

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload($fieldName)) {
			$uploadError = $this->upload->display_errors();
			log_message('error', 'File Upload Error for ' . $fieldName . ': ' . $uploadError);
			return false;
		}
		$uploadedFileName = $this->upload->data('file_name');
		return $uploadPath . $uploadedFileName;
	}
}