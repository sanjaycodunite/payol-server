<?php 
class Newaeps extends CI_Controller {    
	
	
	public function __construct() 
	{
		parent::__construct();
		 $this->User->checkMasterPermission();
		$this->load->model('master/Newaeps_model');		
	   $this->lang->load('master/aeps', 'english');
		$this->load->model('admin/Jwt_model');
		
	}

	public function index(){
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(17, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_new_aeps_status = $this->User->get_member_new_aeps_status($loggedUser['id']);
		if(!$user_new_aeps_status){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}

		$user_2fa_aeps_status = $this->User->get_member_2fa_aeps_status($loggedUser['id']);

		if(!$user_2fa_aeps_status){
			$this->Az->redirect('master/newaeps/memberRegistration', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}
		
		$user_2fa_aeps_loginn_status = $this->User->get_member_2fa_aeps_login_status($loggedUser['id']);

		if(!$user_2fa_aeps_loginn_status){
			$this->Az->redirect('master/newaeps/memberLogin', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}


		
		$siteUrl = base_url();	

  		// get bank list
		$bankList = $this->db->get('new_bank_list')->result_array();	

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
			'content_block' => 'newaeps/list'
		);
		$this->parser->parse('master/layout/column-1' , $data);
		
		
	}

	public function activeAeps(){
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$memberID = $loggedUser['id'];

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(17, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_new_aeps_status = $this->User->get_member_new_aeps_status();
		if($user_new_aeps_status)
		{
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_MEMBER_ERROR'));
		}

		$get_kyc_exists = $this->db->order_by('created','desc')->get_where('new_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'status'=>0,'clear_step'=>1))->num_rows();

		if($get_kyc_exists > 0)

		{
			
			$get_kyc_exists = $this->db->order_by('created','desc')->get_where('new_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'status'=>0,'clear_step'=>1))->row_array();

			$recordID = $get_kyc_exists['id']; 
			//call onboard status pipe wise
        		log_message('debug', 'Onboard check status api called.');

					$datapost = array();
					$datapost['merchantcode'] = $get_kyc_exists['member_code'];
					$datapost['mobile'] = $get_kyc_exists['mobile'];
					$datapost['pipe'] = 'bank2';

					log_message('debug', 'Onboard check status  api post request data - '.json_encode($datapost));
					
					$key =$accountData['paysprint_aeps_key'];
					$iv=  $accountData['paysprint_aeps_iv'];
					
					
					$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
					$body=       base64_encode($cipher);
					$jwt_payload = array(
						'timestamp'=>time(),						
						'partnerId'=>$accountData['paysprint_partner_id'],
						'reqid'=>time().rand(1111,9999)
					);
					
					$secret = $accountData['paysprint_secret_key'];

					$token = $this->Jwt_model->encode($jwt_payload,$secret);
					
					$header = [
						'Token:'.$token,
						'accept:application/json'
					];

					 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						}

					
					$httpUrl = PAYSPRINT_ONBOARD_PIPE_STATUS_CHECK_API_URL;
					$curl = curl_init();

					curl_setopt_array($curl, array(
						CURLOPT_URL => $httpUrl,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 60,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS => $datapost,
						CURLOPT_HTTPHEADER => $header
					));

					$output = curl_exec($curl);
					curl_close($curl);
					
					log_message('debug', 'Onboard check status api final response - '.$output);

					$responseData = json_decode($output,true);

				if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true && $responseData['is_approved'] == 'Accepted'){
				// update aeps status
				$this->db->where('account_id',$account_id);
	        	$this->db->where('id',$memberID);
	        	$this->db->update('users',array('new_aeps_status'=>1));

	        	// update aeps status
	        	$this->db->where('account_id',$account_id);
	            $this->db->where('id',$recordID);
	            $this->db->update('new_aeps_member_kyc',array('status'=>1,'clear_step'=>2,'kyc_data'=>$jsonData));

	            $this->Az->redirect('master/dashboard', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Your merchant onboard is activated.</div>');
	            }

	            elseif (isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true && $responseData['is_approved'] == 'Pending') {

	            	 $this->Az->redirect('master/dashboard', 'system_message_error','<div class="alert alert-warning alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Onboarding complete,Please wait 6hr for AEPS Activation on FINO Payment Bank activation.</div>');
	            	
	            }

	            elseif(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true && $responseData['is_approved'] == 'Rejected')

	            {
	            	$this->Az->redirect('master/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Onboarding Rejected by bank.</div>');
	            }
	            elseif (isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == false && $responseData['is_approved'] == 'Pending') {
	            	$this->Az->redirect('master/dashboard', 'system_message_error','<div class="alert alert-warning alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Onboarding Not Completed.</div>');
	            }

		}

		// check already kyc approved or not
		$chk_kyc = $this->db->get_where('new_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'status'=>1))->num_rows();
		if($chk_kyc)
		{
			$this->db->where('id',$memberID);
			$this->db->update('users',array('new_aeps_status'=>1));

			$this->Az->redirect('master/newaeps', 'system_message_error',lang('AEPS_ACTIVE_SUCCESS'));
		}

		$memberData = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID,'aeps_status'=>0))->row_array();

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
			'content_block' => 'newaeps/member-activation'
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
		$this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|min_length[12]|xss_clean');
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
			$accountData = $this->User->get_account_data($account_id);
			$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
			$memberID = $loggedUser['id'];

			$activeService = $this->User->account_active_service($loggedUser['id']);
			if(!in_array(17, $activeService)){
				$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
			}
			
			$user_new_aeps_status = $this->User->get_member_new_aeps_status();
			
			if($user_new_aeps_status)
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
					$this->Az->redirect('master/newaeps/activeAeps', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
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
					$this->Az->redirect('master/newaeps/activeAeps', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$pancard_photo = substr($config02['upload_path'] . $fileData['file_name'], 2);
				}
			}


			$response = $this->Newaeps_model->activeAEPSMember($post,$aadhar_photo,$pancard_photo);
			$status = $response['status'];

			if($status == 1)
			{	
				$chk_already = $this->db->get_where('new_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID))->row_array();

				if(!$chk_already){

					$before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

					$get_kyc_charge = $this->db->get_where('master_setting',array('id'=>1))->row_array();

					$kyc_charge = isset($get_kyc_charge['fino_kyc_charge']) ? $get_kyc_charge['fino_kyc_charge'] : 0;
					
					if($kyc_charge > 0){
			
						$after_balance = $before_wallet_balance - $kyc_charge;
						$wallet_data = array(
				            'member_id'           => $memberID,    
				            'before_balance'      => $before_wallet_balance,
				            'amount'              => $kyc_charge,  
				            'after_balance'       => $after_balance,      
				            'status'              => 1,
				            'type'                => 2,   
				            'wallet_type'         => 1,   
				            'created'             => date('Y-m-d H:i:s'),      
				            'description'         => 'AEPS Kyc charge deducted.',
				            'credited_by'         => $memberID
				        );

				        $this->db->insert('member_wallet',$wallet_data);

				    }
				}

				$redirecturl = $response['redirecturl'];
				redirect($redirecturl);
			}
			else
			{
				if($aadhar_photo)
				{
					if (file_exists($aadhar_photo)) 
					{
						unlink(str_replace('system/', '', BASEPATH . $aadhar_photo));
					}
				}
				if($pancard_photo)
				{
					if (file_exists($pancard_photo)) 
					{
						unlink(str_replace('system/', '', BASEPATH . $pancard_photo));
					}
				}
				$this->Az->redirect('master/newaeps/activeAeps', 'system_message_error',sprintf(lang('AEPS_ACTIVE_FAILED'),$response['msg']));
			}

		}
		
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
		$memberData = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
		$member_code = $memberData['user_code'];
		$member_pin = md5($memberData['decoded_transaction_password']);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(17, $activeService)){
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! AEPS Service Not Active.'
			);
		}
		else
		{
			$agentID = $loggedUser['user_code'];
			$user_aeps_status = $this->User->get_member_new_aeps_status($memberID);
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

					log_message('debug', 'Aeps API Called');
					
					$requestTime = date('Y-m-d H:i:s');
					if($aadharNumber && $mobile && $biometricData && $iin)
					{	
						log_message('debug', 'New AEPS api API Call');

						if($serviceType == 'balinfo' || $serviceType == 'ministatement')
						{
							$txnID = uniqid('BINQ' . rand(11,99));
							$txnType = 'BE';
							$remarks = 'Balance Inquiry';
							$is_bal_info = 1;
							$is_withdrawal = 0;
							$Servicestype = 'GetBalanceaeps';
							$api_url = PAYSPRINT_AEPS_NEW_BALANCE_API_URL;
							if($serviceType == 'ministatement')
							{
								$txnID = uniqid('MNST' . rand(11,99));
								$Servicestype = 'getministatment';
								$is_bal_info = 0;
								$txnType = 'MS';
								$remarks = 'Mini Statement';
								$api_url = PAYSPRINT_AEPS_NEW_MINI_STATEMENT_API_URL;
							}

							log_message('debug', 'New AEPS api API Url - '.$api_url);

							if($amount == 0)
							{	
								$accessmodetype = 'SITE';
								
								$key =$accountData['paysprint_aeps_key'];
								$iv=  $accountData['paysprint_aeps_iv'];
								$datapost = array();
								$datapost['latitude'] = '22.44543';
								$datapost['longitude'] = '77.434';
								$datapost['mobilenumber'] = $mobile;
								$datapost['referenceno'] = $txnID;
								$datapost['ipaddress'] = $_SERVER['REMOTE_ADDR'];
								$datapost['adhaarnumber'] = $aadharNumber;
								$datapost['accessmodetype'] = $accessmodetype;
								$datapost['nationalbankidentification'] = $iin;
								$datapost['requestremarks'] = $remarks;
								$datapost['data'] = $biometricData;
								$datapost['pipe'] = 'bank2';
								$datapost['timestamp'] = date('Y-m-d H:i:s');
								$datapost['transcationtype'] = $txnType;
								$datapost['submerchantid'] = $agentID;

								log_message('debug', 'New AEPS api post data - '.json_encode($datapost));
								
								$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
								$body=       base64_encode($cipher);
								$jwt_payload = array(
									'timestamp'=>time(),
									'partnerId'=>$accountData['paysprint_partner_id'],
									'reqid'=>time().rand(1111,9999)
								);
								
								$secret = $accountData['paysprint_secret_key'];

								$token = $this->Jwt_model->encode($jwt_payload,$secret);
								
						        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
								
								$header = [
									'Token:'.$token									
								];
								

								 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						        }


								log_message('debug', 'New AEPS api header data - '.json_encode($header));
								

								$httpUrl = $api_url;

								$curl = curl_init();

								curl_setopt_array($curl, array(
									CURLOPT_URL => $httpUrl,
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_MAXREDIRS => 10,
									CURLOPT_TIMEOUT => 60,
									CURLOPT_CUSTOMREQUEST => 'POST',
									CURLOPT_POSTFIELDS => array('body'=>$body),
									CURLOPT_HTTPHEADER => $header
								));

								$output = curl_exec($curl);
								curl_close($curl);

								log_message('debug', 'New AEPS api response - '.json_encode($output));

								$responseData = json_decode($output,true);

								$apiData = array(
									'account_id' => $account_id,
									'user_id' => $memberID,
									'api_url' => $api_url,
									'api_response' => $output,
									'post_data' => json_encode($datapost),
									'created' => date('Y-m-d H:i:s'),
									'created_by' => $memberID
								);
								$this->db->insert('aeps_api_response',$apiData);

								
								if(isset($responseData['response_code']) && $responseData['response_code'] == 1 && $responseData['status'] == true)
								{
									$statementList = isset($responseData['ministatement']) ? $responseData['ministatement'] : array();
									$balanceAmount = $responseData['balanceamount'];
									$bankRRN = $responseData['bankrrn'];
									$recordID = $this->Newaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$statementList,$balanceAmount,$bankRRN);
									$str = '';
									if($is_bal_info == 0)
									{
										$this->Newaeps_model->addStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID);
										
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
										'balanceAmount' => $responseData['balanceamount'],
										'bankRRN' => $responseData['bankrrn'],
										'is_bal_info' => $is_bal_info,
										'is_withdrawal' => $is_withdrawal,
										'str' => $str
									);


								}
								else
								{
									$this->Newaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3);
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
							log_message('debug', 'New AEPS api API Call');

							$txnID = uniqid('CSWD' . rand(11,99));
							$txnType = 'CW';
							$remarks = 'Withdrawal';
							$api_url = PAYSPRINT_AEPS_NEW_WITHDRAWAL_API_URL;
							$is_withdrawal = 1;
							$is_bal_info = 0;
							$Servicestype = 'AccountWithdrowal';
							if($serviceType == 'aadharpay')
							{
								$Servicestype = 'Aadharpay';
								$txnID = uniqid('APAY' . rand(11,99));
								$txnType = 'M';
								$remarks = 'Aadharpay';
								$api_url = PAYSPRINT_AEPS_NEW_AADHARPAY_API_URL;
							}

							log_message('debug', 'New AEPS api API Url - '.$api_url);
							
							if($amount >= 10 && $amount <= 10000)
							{
								$accessmodetype = 'SITE';
								
								$key =$accountData['paysprint_aeps_key'];
								$iv=  $accountData['paysprint_aeps_iv'];
								$datapost = array();
								$datapost['latitude'] = '22.44543';
								$datapost['longitude'] = '77.434';
								$datapost['mobilenumber'] = $mobile;
								$datapost['referenceno'] = $txnID;
								$datapost['ipaddress'] = $_SERVER['REMOTE_ADDR'];
								$datapost['adhaarnumber'] = $aadharNumber;
								$datapost['accessmodetype'] = $accessmodetype;
								$datapost['nationalbankidentification'] = $iin;
								$datapost['requestremarks'] = $remarks;
								$datapost['data'] = $biometricData;
								$datapost['pipe'] = 'bank2';
								$datapost['timestamp'] = date('Y-m-d H:i:s');
								$datapost['transcationtype'] = $txnType;
								$datapost['amount'] = $amount;
								$datapost['submerchantid'] = $agentID;
								$datapost['MerAuthTxnId'] = $post['MerAuthTxnId'];

								log_message('debug', 'New AEPS api post data - '.json_encode($datapost));
								
								$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
								$body=       base64_encode($cipher);
								$jwt_payload = array(
									'timestamp'=>time(),
									'partnerId'=>$accountData['paysprint_partner_id'],
									'reqid'=>time().rand(1111,9999)
								);
								
								$secret = $accountData['paysprint_secret_key'];

								$token = $this->Jwt_model->encode($jwt_payload,$secret);
								
						        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
								
								$header = [
									'Token:'.$token									
									
								];

								 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						        }
								
								log_message('debug', 'New AEPS api header data - '.json_encode($header));

								$httpUrl = $api_url;
								$curl = curl_init();

								curl_setopt_array($curl, array(
									CURLOPT_URL => $httpUrl,
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_MAXREDIRS => 10,
									CURLOPT_TIMEOUT => 60,
									CURLOPT_CUSTOMREQUEST => 'POST',
									CURLOPT_POSTFIELDS => array('body'=>$body),
									CURLOPT_HTTPHEADER => $header
								));

								$output = curl_exec($curl);
								curl_close($curl);

								log_message('debug', 'New AEPS api response - '.json_encode($output));

								$responseData = json_decode($output,true);

								$apiData = array(
									'account_id' =>$account_id,
									'user_id' => $memberID,
									'api_url' => $httpUrl,
									'api_response' => $output,
									'post_data' => json_encode($datapost),
									'created' => date('Y-m-d H:i:s'),
									'created_by' => $memberID
								);
								$this->db->insert('aeps_api_response',$apiData);

								if(isset($responseData['response_code']) && $responseData['response_code'] == 1 && $responseData['status'] == true)
								{
									$balanceAmount = $responseData['balanceamount'];
									$bankRRN = $responseData['bankrrn'];

									$transactionAmount = $responseData['amount'];
									$statementList = array();
									$recordID = $this->Newaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$statementList,$balanceAmount,$bankRRN,$transactionAmount);

									$com_type = 0;
									if($serviceType == 'balinfo' || $serviceType == 'ministatement')
									{
										$com_type = 2;
									}
									elseif($serviceType == 'balwithdraw')
									{
										$com_type = 1;
									}
									elseif($serviceType == 'aadharpay'){

										$com_type = 3;
									}

									$this->Newaeps_model->addBalance($txnID,$aadharNumber,$iin,$amount,$recordID,$serviceType);
									$str = '';
									$str = '<div class="table-responsive">';
									$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
									$str.='<tr>';
									$str.='<td>Txn Status</td><td><font color="green">Successful</font></td>';
									$str.='</tr>';
									
									$str.='<tr>';
									$str.='<td>Transfer Amount</td><td>INR '.$responseData['amount'].'/-</td>';
									$str.='</tr>';

									$str.='<tr>';
									$str.='<td>Balance Amount</td><td>INR '.$responseData['balanceamount'].'/-</td>';
									$str.='</tr>';

									$str.='<tr>';
									$str.='<td>Bank RRN</td><td>'.$responseData['bankrrn'].'</td>';
									$str.='</tr>';

									$str.='</table>';
									$str.='</div>';
									
									
									$response = array(
										'status' => 1,
										'msg' => $responseData['message'],
										'balanceAmount' => $responseData['balanceamount'],
										'bankRRN' => $responseData['bankrrn'],
										'is_bal_info' => $is_bal_info,
										'is_withdrawal' => $is_withdrawal,
										'str' => $str
									);


								}
								else
								{
									$this->Newaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3);
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
        
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$memberID = $loggedUser['id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(17, $activeService)){
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
			'content_block' => 'newaeps/transaction-list'
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
		$sql = "SELECT a.* FROM tbl_member_new_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
		
		$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
			
			
			$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_new_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND  a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '".$keyword."%'";
				$sql.=" OR a.account_no LIKE '".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

             if($status)
            {
                $sql.=" AND status = '$status'";
            }
            
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			
			
			
			$get_filter_data = $this->db->query($sql)->result_array();


			$sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_new_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";


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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,					
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
				);

		echo json_encode($json_data);  // send data as json format
	}




	public function payout(){

		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);

		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id']))->row_array();

		$memberID = $loggedUser['id'];

		// get Benificary list
		$benificaryList = $this->db->query("SELECT a.*,b.bank_name as bank_name FROM tbl_new_payout_beneficiary as a INNER JOIN tbl_new_payout_bank_list as b ON a.bank_id = b.id WHERE a.user_id = '$memberID'")->result_array();

		$siteUrl = base_url();
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'memberDetail'=> $memberDetail,
			'benificaryList' => $benificaryList,
			'loggedUser' => $loggedUser, 
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'newaeps/benificary'
		);

		$this->parser->parse('master/layout/column-1' , $data);
	}



	public function addBeneficiary(){

		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);

		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id']))->row_array();

		$memberID = $loggedUser['id'];

		// get bankList
		$bankList = $this->db->get('new_payout_bank_list')->result_array();

		$siteUrl = base_url();
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'memberDetail'=> $memberDetail,
			'bankList' => $bankList,
			'loggedUser' => $loggedUser, 
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'newaeps/addBeneficiary'
		);
		$this->parser->parse('master/layout/column-1' , $data);
	}



	public function benificaryAuth(){
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		//check for foem validation
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		$memberID = $loggedUser['id'];
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
		$this->form_validation->set_rules('bank', 'Bank', 'required');
		$this->form_validation->set_rules('account_number', 'Account Number', 'required');
		$this->form_validation->set_rules('ifsc', 'IFSC', 'required');
		
		if ($this->form_validation->run() == FALSE) {
			
			$this->addBeneficiary();
		}
		else
		{   

			log_message('debug', 'Add account api call.');

			$datapost = array();
			$datapost['bankid'] = $post['bank'];
			$datapost['merchant_code'] = $loggedUser['user_code'];
			$datapost['account'] = $post['account_number'];
			$datapost['ifsc'] = $post['ifsc'];
			$datapost['name'] = $post['account_holder_name'];
			$datapost['account_type'] = 'PRIMARY';


			log_message('debug', 'Account id - '.$account_id.' - Add account api post request data - '.json_encode($datapost));    
			
			$key = $accountData['paysprint_aeps_key'];
			$iv = $accountData['paysprint_aeps_iv'];
			
			$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
			$body=       base64_encode($cipher);
			$jwt_payload = array(
				'timestamp'=>time(),
				'partnerId'=>$accountData['paysprint_partner_id'],
				'reqid'=>time().rand(1111,9999)
			);
			
			$secret = $accountData['paysprint_secret_key'];

			$token = $this->Jwt_model->encode($jwt_payload,$secret);
			
			log_message('debug', 'Account id - '.$account_id.' - AEPS Payout Add Account Api Post Data - '.$token);
			
	        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
			
			$header = [
									'Token:'.$token									
								];
								
								 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						        }
			
		
			
			$httpUrl = PAYSPRINT_ADD_BENEFICIARY_URL;

			log_message('debug', ' Account id - '.$account_id.' - Add account api url - '.$httpUrl);

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $httpUrl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $datapost,
				CURLOPT_HTTPHEADER => $header
			));

			$output = curl_exec($curl);
			curl_close($curl);


			log_message('debug', ' Account id - '.$account_id.' - Add account api final response - '.$output);
			
			$responseData = json_decode($output,true);
			
			$api_data = array(
				'account_id'=>$account_id,
				'user_id' => $loggedUser['id'],
				'api_url' => $httpUrl,
				'post_data' => json_encode($datapost),
				'api_response' => $output,
				'created' => date('Y-m-d H:i:s')	
			);
			$this->db->insert('new_aeps_payout_api_response',$api_data);

			if(isset($responseData) && $responseData['response_code'] == 2 && $responseData['status'] == true){

				$bene_id = $responseData['bene_id'];

				$beneData = array(
					'account_id'=>$account_id,
					'user_id' => $loggedUser['id'],
					'account_holder_name' => $post['account_holder_name'],
					'account_number' => $post['account_number'],
					'ifsc' => $post['ifsc'],
					'bank_id' => $post['bank'],
					'account_type' => 'PRIMARY',
					'bene_id' => $bene_id,
					'is_verified' => 0,
					'created' => date('Y-m-d H:i:s') 	

				);

				$this->db->insert('new_payout_beneficiary',$beneData);

				$this->Az->redirect('master/newaeps/uploadDocument/'.$bene_id, 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Account Detailed saved successfully. Please upload Supportive Document to activate</div>');
			}
			elseif(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

				$bene_id = $responseData['bene_id'];

				$beneData = array(
					'account_id'=>$account_id,
					'user_id' => $loggedUser['id'],
					'account_holder_name' => $post['account_holder_name'],
					'account_number' => $post['account_number'],
					'ifsc' => $post['ifsc'],
					'bank_id' => $post['bank'],
					'account_type' => 'PRIMARY',
					'bene_id' => $bene_id,
					'is_verified' => 1,
					'created' => date('Y-m-d H:i:s') 	

				);

				$this->db->insert('new_payout_beneficiary',$beneData);

				$this->Az->redirect('master/newaeps/payout', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Account Detailed saved successfully</div>');
			}
			else{

				$error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! something went wrong. Please try again.';

				$this->Az->redirect('master/newaeps/addBeneficiary', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$error.'</div>');
			}

			log_message('debug', 'Add account api stopped.');
			
		}
		
	}






	public function uploadDocument($bene_id = ''){
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		


		$chk_bene = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'user_id'=>$loggedUser['id'],'bene_id'=>$bene_id))->row_array();

		if(!$chk_bene){

			$this->Az->redirect('master/newaeps/payout', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! Something went wrong.</div>');
		}
		

		$siteUrl = base_url();
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'bene_id' => $bene_id, 
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'newaeps/uploadDocument'
		);
		$this->parser->parse('master/layout/column-1' , $data);
	}



	public function uploadDocumentAuth(){
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		//check for foem validation
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		$memberID = $loggedUser['id'];
		$post = $this->input->post();
		$this->load->library('form_validation');


		if (!$post) {
			
			$this->uploadDocument($post['bene_id']);
		}
		else
		{   

			log_message('debug', 'Upload document api called.');

			$bene_id = $post['bene_id'];
			
			$datapost = array();
			$datapost['doctype'] = $post['document_type'];
			
			$datapost['passbook'] = new CURLFile($_FILES['passbook']['tmp_name'], $_FILES['passbook']['type'], $_FILES['passbook']['name']);
			
			if($post['document_type'] == 'PAN'){

				$datapost['panimage'] = new CURLFile($_FILES['panimage']['tmp_name'], $_FILES['panimage']['type'], $_FILES['panimage']['name']);
			}

			if($post['document_type'] == 'AADHAAR'){

				$datapost['front_image'] = new CURLFile($_FILES['aadhar_front']['tmp_name'], $_FILES['aadhar_front']['type'], $_FILES['aadhar_front']['name']);

				$datapost['back_image'] = new CURLFile($_FILES['aadhar_back']['tmp_name'], $_FILES['aadhar_back']['type'], $_FILES['aadhar_back']['name']);

			}
			
			$datapost['bene_id'] = $bene_id;


			log_message('debug', ' Account id - '.$account_id.' - Upload document api post request data - '.json_encode($datapost));

			$key = $accountData['paysprint_aeps_key'];
			$iv = $accountData['paysprint_aeps_iv'];

			
			$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
			$body=       base64_encode($cipher);
			$jwt_payload = array(
				'timestamp'=>time(),
				'partnerId'=>$accountData['paysprint_partner_id'],
				'reqid'=>time().rand(1111,9999)
			);
			
			$secret = $accountData['paysprint_secret_key'];

			$token = $this->Jwt_model->encode($jwt_payload,$secret);
			
	        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
			
				$header = [
									'Token:'.$token									
								];
								
								 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						        }
			
			
			$httpUrl = PAYSPRINT_BENEFICIARY_UPLOAD_DOCUMENT_URL;
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $httpUrl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $datapost,
				CURLOPT_HTTPHEADER => $header
			));

			$output = curl_exec($curl);
			curl_close($curl);

			log_message('debug', 'Upload document api final response - '.$output);
			
			$responseData = json_decode($output,true);
			
			$api_data = array(
				'account_id'=>$account_id,
				'user_id' => $loggedUser['id'],
				'api_url' => $httpUrl,
				'post_data' => json_encode($datapost),
				'api_response' => $output,
				'created' => date('Y-m-d H:i:s')	
			);
			$this->db->insert('new_aeps_payout_api_response',$api_data);

			
			if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

				$passbook = '';
				if(isset($_FILES['passbook']['name']) && $_FILES['passbook']['name']){
					$config['upload_path'] = './media/kyc_document/';
					$config['allowed_types'] = 'jpg|png|jpeg|pdf|PDF';
					$config['max_size'] = 2048;
					$fileName = time().rand(111111,999999);
					$config['file_name'] = $fileName;
					$this->load->library('upload', $config);
					$this->upload->do_upload('passbook');		
					$uploadError = $this->upload->display_errors();
					if($uploadError){
						$this->Az->redirect('master/newaeps/payout', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
					}
					else
					{
						$fileData = $this->upload->data();
						//get uploaded file path
						$passbook = substr($config['upload_path'] . $fileData['file_name'], 2);
					}
				}


				$pancard = '';
				if(isset($_FILES['panimage']['name']) && $_FILES['panimage']['name']){
					$config['upload_path'] = './media/kyc_document/';
					$config['allowed_types'] = 'jpg|png|jpeg|pdf|PDF';
					$config['max_size'] = 2048;
					$fileName = time().rand(111111,999999);
					$config['file_name'] = $fileName;
					$this->load->library('upload', $config);
					$this->upload->do_upload('panimage');		
					$uploadError = $this->upload->display_errors();
					if($uploadError){
						$this->Az->redirect('master/newaeps/payout', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
					}
					else
					{
						$fileData = $this->upload->data();
						//get uploaded file path
						$pancard = substr($config['upload_path'] . $fileData['file_name'], 2);
					}
				}


				$aadhar_front = '';
				if(isset($_FILES['aadhar_front']['name']) && $_FILES['aadhar_front']['name']){
					$config['upload_path'] = './media/kyc_document/';
					$config['allowed_types'] = 'jpg|png|jpeg|pdf|PDF';
					$config['max_size'] = 2048;
					$fileName = time().rand(111111,999999);
					$config['file_name'] = $fileName;
					$this->load->library('upload', $config);
					$this->upload->do_upload('aadhar_front');		
					$uploadError = $this->upload->display_errors();
					if($uploadError){
						$this->Az->redirect('master/newaeps/payout', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
					}
					else
					{
						$fileData = $this->upload->data();
						//get uploaded file path
						$aadhar_front = substr($config['upload_path'] . $fileData['file_name'], 2);
					}
				}


				$aadhar_back = '';
				if(isset($_FILES['aadhar_back']['name']) && $_FILES['aadhar_back']['name']){
					$config['upload_path'] = './media/kyc_document/';
					$config['allowed_types'] = 'jpg|png|jpeg|pdf|PDF';
					$config['max_size'] = 2048;
					$fileName = time().rand(111111,999999);
					$config['file_name'] = $fileName;
					$this->load->library('upload', $config);
					$this->upload->do_upload('aadhar_back');		
					$uploadError = $this->upload->display_errors();
					if($uploadError){
						$this->Az->redirect('master/newaeps/payout', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
					}
					else
					{
						$fileData = $this->upload->data();
						//get uploaded file path
						$aadhar_back = substr($config['upload_path'] . $fileData['file_name'], 2);
					}
				}


				$this->db->where('user_id',$loggedUser['id']);
				$this->db->where('account_id',$account_id);
				$this->db->where('bene_id',$bene_id);
				$this->db->update('new_payout_beneficiary',array('is_verified'=>1,'document_type'=>$post['document_type'],'passbook'=>$passbook,'pancard'=>$pancard,'aadhar_front'=>$aadhar_front,'aadhar_back'=>$aadhar_back));

				$this->Az->redirect('master/newaeps/payout', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Account Documet upload successfully and verified</div>');
			}
			else{

				$error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! something went wrong. Please try again.';

				$this->Az->redirect('master/newaeps/uploadDocument/'.$post['bene_id'], 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$error.'</div>');
			}
			

		}



		
	}


	public function fundTransfer($bene_id = 0){

		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);

		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

		
        // get Benificary list
		$benificaryData = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'user_id'=>$loggedUser['id'],'id'=>$bene_id))->row_array();

		if(!$benificaryData){
			$this->Az->redirect('master/new/payout', 'system_message_error',lang('DB_ERROR'));
		}

		
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id']))->row_array();

		$memberID = $loggedUser['id'];

		// get Benificary list
		$benificaryList = $this->db->order_by('created','desc')->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'user_id'=>$memberID,'is_verified'=>1))->result_array();

		// get account detail
		$accountDetail = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();

		$siteUrl = base_url();
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'memberDetail'=> $memberDetail,
			'benificaryList' => $benificaryList,
			'accountDetail' => $accountDetail,
			'fund_transfer_charge' => $fund_transfer_charge,
			'current_package_id' => $current_package_id,
			'loggedUser' => $loggedUser, 
			'bene_id' => $bene_id,
			'benificaryData' => $benificaryData,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'newaeps/fund-transfer'
		);
		$this->parser->parse('master/layout/column-1' , $data);
	}

	/*function amountCheck($num)
	{
		$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
		$min_amount = 10;
		if($walletSetting['aeps_wallet_min_bank_transfer'] != '*' && $walletSetting['aeps_wallet_min_bank_transfer'] != ''){
			$min_amount = $walletSetting['aeps_wallet_min_bank_transfer'];
		}
		if ($num < $min_amount)
		{
			$this->form_validation->set_message(
				'amountCheck',
				'The %s field must be grater than '.$min_amount
			);
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
*/
	public function fundTransferAuth() {
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);

		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		$memberID = $loggedUser['id'];

		$this->load->library('template');
		$siteUrl = site_url();
		$post = $this->input->post();
		log_message('debug', ' Account id - '.$account_id.' -Fund Transfer Account ID - '.$memberID.' Post Data - '.json_encode($post));	
		

        //check for foem validation
		$this->load->library('form_validation');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
		$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
		$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
		$this->form_validation->set_rules('txn_pass', 'Transaction Password', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');

		if ($this->form_validation->run() == FALSE) {
			
			log_message('debug', 'Fund Transfer Form Validation Error.');	
			$this->session->set_flashdata('system_message_error', lang('CHK_FIELDS'));
			$this->fundTransfer($post['bene_id']);
		} 
		else {

				$amount = $post['amount'];

        	// get Benificary list
			$benificaryData = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'user_id'=>$loggedUser['id'],'id'=>$post['bene_id'],'is_verified'=>1))->row_array();

			if(!$benificaryData){
				$this->Az->redirect('master/newaeps/payout', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! beneficiary not valid.</div>');
			}
			

            // get account detail
			$accountDetail = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
			
			
			
			if($accountDetail['transaction_password'] != do_hash($post['txn_pass'])){

				$this->Az->redirect('master/newaeps/fundTransfer/'.$post['bene_id'], 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! transaction password id wrong.</div>');		

			}


			$charge_amount = $this->User->get_dmr_surcharge($amount,$memberID);
			
			$min_wallet_balance = $accountDetail['min_wallet_balance'];
			
			$total_wallet_deduct = $post['amount'] + $charge_amount + $min_wallet_balance;

			$wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

        	// check account balance
			if($wallet_balance < $total_wallet_deduct)
			{
				log_message('debug', 'Fund Transfer Low Balance Error');	
				$this->Az->redirect('master/newaeps/fundTransfer/'.$post['bene_id'], 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! Insufficient balance in your wallet.</div>');
			}
			
				$final_wallet_deduct = $post['amount'] + $charge_amount;
				
			$transaction_id = rand(111111,999999).time();

			$dmt_status = $this->db->get_where('tbl_payout_master_setting',array('id'=>1,'account_id'=>$account_id))->row_array();
			$get_payout_amount  = $dmt_status['amount'];

			if($accountData['is_payout_otp'] == 1 && $total_wallet_deduct > $get_payout_amount)

			{

				//call sms otp api

				 $otp_code = rand(1111,9999);
             $decode_otp_code = do_hash($otp_code);
                
                // save OTP Data
                $otpData = array(
                    'member_id' =>  $loggedUser['id'],
                    'account_id'=>$account_id,
                    'otp_code' => $otp_code,
                    'encrypt_otp_code' => $decode_otp_code,
                    //'otp_date' => date('Y-m-d'),
                    'json_post_data' => json_encode($post),
                    'charge_amount' =>$charge_amount,
                    'total_wallet_deduct'=>$final_wallet_deduct,
                    'transaction_id'=>$transaction_id,
                    'benificaryData' =>json_encode($benificaryData),
                    'status' => 0,
                    'created' => date('Y-m-d H:i:s')
                );

                $this->db->insert('users_otp',$otpData);

                 $get_member_mobile = $this->db->get_where('users',array('id'=>$memberID,'account_id'=>$account_id,))->row_array();

                $member_mobile = $get_member_mobile['mobile'];

                $api_url = SMS_REGISTER_MSG_API_URL;
				
            	$request = array(
                'flow_id' => $accountData['sms_otp_flow_id'],
                'sender' => $accountData['sender_id'],
                'mobiles' => '91'.$member_mobile,
                'otp' => $otp_code,
                
            );


            				
            $header = array(
                'content-type: application/JSON',
                'authkey: '.$accountData['sms_auth_key']
            );
            
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

             $this->Az->redirect('master/newaeps/transferOTP/'.$decode_otp_code, 'system_message_error',lang('PAYOUT_OTP_SENT'));	

			}

			else
			{

				$wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);
				$after_balance = $wallet_balance - $final_wallet_deduct;    

			$wallet_data = array(
				'account_id' 		  =>$account_id,
				'member_id'           => $memberID,    
				'before_balance'      => $wallet_balance,
				'amount'              => $final_wallet_deduct,  
				'after_balance'       => $after_balance,      
				'status'              => 1,
				'type'                => 2,
				'wallet_type'         => 1,      
				'created'             => date('Y-m-d H:i:s'),      
				'description'         => 'Aeps  Payout #'.$transaction_id.' Amount Deducted.'
			);

			$this->db->insert('member_wallet',$wallet_data);

			

			log_message('debug', 'Fund transfer api called.');


			$datapost = array();
			$datapost['bene_id'] = $benificaryData['bene_id'];
			$datapost['amount'] = $post['amount'];
			$datapost['refid']  = $transaction_id;
			$datapost['mode'] = 'IMPS';

			log_message('debug', 'Fund transfer api post request data - '.json_encode($datapost));

			
			$key = $accountData['paysprint_aeps_key'];
			$iv= $accountData['paysprint_aeps_iv'];
			
			$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
			$body=       base64_encode($cipher);
			$jwt_payload = array(
				'timestamp'=>time(),
				'partnerId'=>$accountData['paysprint_partner_id'],
				'reqid'=>time().rand(1111,9999)
			);
			
			$secret = $accountData['paysprint_secret_key'];

			$token = $this->Jwt_model->encode($jwt_payload,$secret);
			
	        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
			
				$header = [
									'Token:'.$token									
								];
								
								 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						        }
			
			
			$httpUrl = PAYSPRINT_FUND_TRANSFER_URL;
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $httpUrl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $datapost,
				CURLOPT_HTTPHEADER => $header
			));

			$output = curl_exec($curl);
			curl_close($curl);

			
			$responseData = json_decode($output,true);
			
			$api_data = array(
				'account_id'=>$account_id,
				'user_id' => $loggedUser['id'],
				'api_url' => $httpUrl,
				'post_data' => json_encode($datapost),
				'api_response' => $output,
				'created' => date('Y-m-d H:i:s')	
			);
			$this->db->insert('new_aeps_payout_api_response',$api_data);

			log_message('debug', 'Transfer Fund Final API Response - '.json_encode($output));	

			$payoutData = array(
				'account_id'=>$account_id,
				'user_id' => $memberID,
				'bene_id' => $post['bene_id'],
				'transfer_amount' => $post['amount'],
				'transfer_charge_amount' => $charge_amount,
				'total_wallet_deduct' => $post['amount'] + $charge_amount,
				'refid' => $transaction_id,		
				'status' => 1,
				'created' => date('Y-m-d H:i:s')	
			);
			$this->db->insert('new_aeps_payout',$payoutData);
			$transfer_id = $this->db->insert_id();

			if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

				$ackno = $responseData['ackno'];

				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$memberID);
				$this->db->where('id',$transfer_id);
				$this->db->update('new_aeps_payout',array('status'=>2,'ackno'=>$ackno,'updated'=>date('Y-m-d H:i:s')));

				$this->Az->redirect('master/newaeps/transferReport', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Aeps payout successfully.</div>');

			}
			else{

				$wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

				$after_balance = $wallet_balance + $final_wallet_deduct;    

				$wallet_data = array(
					'account_id'		  =>$account_id,
					'member_id'           => $memberID,    
					'before_balance'      => $wallet_balance,
					'amount'              => $final_wallet_deduct,  
					'after_balance'       => $after_balance,      
					'status'              => 1,
					'type'                => 1,
					'wallet_type'         => 1,      
					'created'             => date('Y-m-d H:i:s'),      
					'description'         => 'Aeps Payout #'.$transaction_id.' Amount Refund.'
				);

				$this->db->insert('member_wallet',$wallet_data);

				
				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$memberID);
				$this->db->where('id',$transfer_id);
				$this->db->update('new_aeps_payout',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));



				$error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! aeps payout failed.';

				$this->Az->redirect('master/newaeps/fundTransfer/'.$post['bene_id'], 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$error.'</div>');
			}


			}

			
			
		}
		
	}



	public function transferReport(){

		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		
		$memberID = $loggedUser['id'];

		$recharge = $this->db->query("SELECT a.*,b.account_holder_name,b.account_number FROM tbl_new_aeps_payout as a INNER JOIN tbl_new_payout_beneficiary as b ON a.bene_id = b.id WHERE a.user_id = '$memberID' and a.account_id='$account_id'")->result_array();

		$siteUrl = base_url();
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'recharge' => $recharge,
			'loggedUser' => $loggedUser, 
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'newaeps/transfer-report'
		);
		$this->parser->parse('master/layout/column-1' , $data);
	}
	


// 	public function checkTransferStatus($ref_id = '') {
		
// 		//get logged user info
// 		$account_id = $this->User->get_domain_account();
// 		$accountData = $this->User->get_account_data($account_id);
// 		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
// 		$memberID = $loggedUser['id'];


// 		$chk_ref_id = $this->db->get_where('new_aeps_payout',array('account_id'=>$account_id,'user_id'=>$memberID,'refid'=>$ref_id,'status < '=>3))->row_array();

// 		if(!$chk_ref_id){

// 			$this->Az->redirect('master/newaeps/transferReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
// 		}
		

// 		log_message('debug', 'Fund transfer check status api called.');

// 		$datapost = array();
// 		$datapost['refid'] = $ref_id;
// 		$datapost['ackno'] = $chk_ref_id['ackno'];

// 		log_message('debug', 'Fund transfer check status api post request data - '.json_encode($datapost));
		
// 		$key = $accountData['paysprint_aeps_key'];
// 		$iv = $accountData['paysprint_aeps_iv'];
		
// 		$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
// 		$body=       base64_encode($cipher);
// 		$jwt_payload = array(
// 			'timestamp'=>time(),
// 			'partnerId'=>$accountData['paysprint_partner_id'],
// 			'reqid'=>time().rand(1111,9999)
// 		);
		
// 		$secret = $accountData['paysprint_secret_key'];

// 		$token = $this->Jwt_model->encode($jwt_payload,$secret);
		
// 	        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
		
// 		$header = [
// 			'Token:'.$token,
// 			//'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY,
			
// 		];
		
		
// 		$httpUrl = PAYSPRINT_STATUS_CHECK_URL;
// 		$curl = curl_init();

// 		curl_setopt_array($curl, array(
// 			CURLOPT_URL => $httpUrl,
// 			CURLOPT_RETURNTRANSFER => true,
// 			CURLOPT_MAXREDIRS => 10,
// 			CURLOPT_TIMEOUT => 60,
// 			CURLOPT_CUSTOMREQUEST => 'POST',
// 			CURLOPT_POSTFIELDS => $datapost,
// 			CURLOPT_HTTPHEADER => $header
// 		));

// 		$output = curl_exec($curl);
// 		curl_close($curl);
		
// 		log_message('debug', 'Fund transfer check status api final response - '.$output);

// 		$responseData = json_decode($output,true);
		
// 		$api_data = array(
// 			'account_id'=>$account_id,
// 			'user_id' => $memberID,
// 			'api_url' => $httpUrl,
// 			'post_data' => json_encode($datapost),
// 			'api_response' => $output,
// 			'created' => date('Y-m-d H:i:s')	
// 		);
// 		$this->db->insert('new_aeps_payout_api_response',$api_data);

		
// 		if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

// 			$acno = $responseData['data']['acno'];

// 			$utr = $responseData['data']['utr'];
// 			$this->db->where('account_id',$account_id);
// 			$this->db->where('user_id',$memberID);
// 			$this->db->where('refid',$ref_id);
// 			$this->db->update('new_aeps_payout',array('status'=>2,'acno'=>$acno,'utr'=>$utr,'updated'=>date('Y-m-d H:i:s')));

// 			$this->Az->redirect('master/newaeps/transferReport', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Status checked successfully.</div>');

// 		}
// 		else{

// 			$accountDetail = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();

// 			$after_balance = $accountDetail['wallet_balance'] + $chk_ref_id['total_wallet_deduct'];

// 			$transaction_id = $refid;    

// 			$wallet_data = array(
// 				'account_id'		  =>$account_id,
// 				'member_id'           => $memberID,    
// 				'before_balance'      => $accountDetail['wallet_balance'],
// 				'amount'              => $total_wallet_deduct,  
// 				'after_balance'       => $after_balance,      
// 				'status'              => 1,
// 				'type'                => 1,
// 				'wallet_type'         => 1,      
// 				'created'             => date('Y-m-d H:i:s'),      
// 				'description'         => 'Aeps Payout #'.$transaction_id.' Amount Refund.'
// 			);

// 			$this->db->insert('member_wallet',$wallet_data);

// 			$user_wallet = array(
// 				'wallet_balance'=>$after_balance,        
// 			);
// 			$this->db->where('account_id',$account_id);    
// 			$this->db->where('id',$memberID);
// 			$this->db->update('users',$user_wallet);

// 			$this->db->where('account_id',$account_id); 
// 			$this->db->where('user_id',$memberID);
// 			$this->db->where('refid',$ref_id);
// 			$this->db->update('new_aeps_payout',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));

// 			$error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! aeps payout failed.';

// 			$this->Az->redirect('master/newaeps/transferReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$error.'</div>');
// 		}
		
// 	}



	public function transferOTP($decode_otp_code = ''){
        //get logged user info
    	$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();
        // check OTP valid or not
        $chk_otp = $this->db->get_where('users_otp',array('member_id'=>$loggedAccountID,'account_id'=>$account_id,'encrypt_otp_code'=>$decode_otp_code,'status'=>0))->row_array();

        if(!$chk_otp)
        {
            $this->Az->redirect('master/newaeps/fundTransfer', 'system_message_error',lang('DB_ERROR'));
        }
        
        // get user data
        $userData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();


        
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,           
            'account_id'=>$account_id,
            'userData' => $userData,           
            'decode_otp_code' => $decode_otp_code,
            'page_title' => lang('SITE_NAME'),
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'newaeps/transfer-otp'
        );
        $this->parser->parse('master/layout/column-1', $data);
    }



     public function updateTransferOTPAuth() {
     	$account_id = $this->User->get_domain_account();

		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
         $memberID = $loggedUser['id'];

        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->input->post();


        $decode_otp_code = $post['decode_otp_code'];

        //$memberID = $loggedUser['user_code'];
        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('otp_code', 'OTP Code', 'required|xss_clean');        
        if ($this->form_validation->run() == FALSE) {
            
            $this->transferOTP($decode_otp_code);
            
        } 
        else {
            
            
            // check OTP valid or not

            $chk_otp = $this->db->get_where('users_otp',array('member_id'=>$memberID,'account_id'=>$account_id,'encrypt_otp_code'=>do_hash($post['otp_code']),'status'=>0))->row_array();

            
            if(!$chk_otp)
            {
                $this->Az->redirect('master/newaeps/transferOTP/'.$decode_otp_code, 'system_message_error',lang('OTP_ERROR'));
            }

            
              $get_user_data =$this->db->get_where('users',array('id'=>$memberID,'account_id'=>$account_id))->row_array();



            $this->db->where('otp_code',$chk_otp['otp_code']);
            $this->db->where('member_id',$memberID);
            $this->db->where('account_id',$account_id);
            $this->db->update('users_otp',array('status'=>1));


             $post_data =  json_decode($chk_otp['json_post_data'],true);

			$post['amount'] = $post_data['amount'];

			$charge_amount = $chk_otp['charge_amount'];

			$get_beneficiaryData = json_decode($chk_otp['benificaryData'],true);

			

			$benificaryData['bene_id'] = $get_beneficiaryData['bene_id'];


			$transaction_id = $chk_otp['transaction_id'];

			$total_wallet_deduct = $chk_otp['total_wallet_deduct'];

             
            $wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

             $after_balance = $wallet_balance - $total_wallet_deduct;    

			$wallet_data = array(
				'account_id' 		  =>$account_id,
				'member_id'           => $memberID,    
				'before_balance'      => $wallet_balance,
				'amount'              => $total_wallet_deduct,  
				'after_balance'       => $after_balance,      
				'status'              => 1,
				'type'                => 2,
				'wallet_type'         => 1,      
				'created'             => date('Y-m-d H:i:s'),      
				'description'         => 'Aeps  Payout #'.$transaction_id.' Amount Deducted.'
			);

			$this->db->insert('member_wallet',$wallet_data);

			log_message('debug', 'Fund transfer api called.');


			$datapost = array();
			$datapost['bene_id'] = $benificaryData['bene_id'];
			$datapost['amount'] = $post['amount'];
			$datapost['refid']  = $transaction_id;
			$datapost['mode'] = 'IMPS';

			log_message('debug', 'Fund transfer api post request data - '.json_encode($datapost));

			
			$key = $accountData['paysprint_aeps_key'];
			$iv= $accountData['paysprint_aeps_iv'];
			
			$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
			$body=       base64_encode($cipher);
			$jwt_payload = array(
				'timestamp'=>time(),
				'partnerId'=>$accountData['paysprint_partner_id'],
				'reqid'=>time().rand(1111,9999)
			);
			
			$secret = $accountData['paysprint_secret_key'];

			$token = $this->Jwt_model->encode($jwt_payload,$secret);
			
	        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
			
			$header = [
				'Token:'.$token,
				//'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY,
				
			];
			
			
			$httpUrl = PAYSPRINT_FUND_TRANSFER_URL;
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $httpUrl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $datapost,
				CURLOPT_HTTPHEADER => $header
			));

			$output = curl_exec($curl);
			curl_close($curl);

			
			$responseData = json_decode($output,true);
			
			$api_data = array(
				'account_id'=>$account_id,
				'user_id' => $loggedUser['id'],
				'api_url' => $httpUrl,
				'post_data' => json_encode($datapost),
				'api_response' => $output,
				'created' => date('Y-m-d H:i:s')	
			);
			$this->db->insert('new_aeps_payout_api_response',$api_data);

			log_message('debug', 'Transfer Fund Final API Response - '.json_encode($output));	

			$payoutData = array(
				'account_id'=>$account_id,
				'user_id' => $memberID,
				'bene_id' => $get_beneficiaryData['id'],
				'transfer_amount' => $post['amount'],
				'transfer_charge_amount' => $charge_amount,
				'total_wallet_deduct' => $post['amount'] + $charge_amount,
				'refid' => $transaction_id,		
				'status' => 1,
				'created' => date('Y-m-d H:i:s')	
			);
			$this->db->insert('new_aeps_payout',$payoutData);
			$transfer_id = $this->db->insert_id();

			if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

				$ackno = $responseData['ackno'];

				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$memberID);
				$this->db->where('id',$transfer_id);
				$this->db->update('new_aeps_payout',array('status'=>2,'ackno'=>$ackno,'updated'=>date('Y-m-d H:i:s')));

				$this->Az->redirect('master/newaeps/transferReport', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Aeps payout successfully.</div>');

			}
			else{

				$wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

				$after_balance = $wallet_balance + $total_wallet_deduct;    

				$wallet_data = array(
					'account_id'		  =>$account_id,
					'member_id'           => $memberID,    
					'before_balance'      => $wallet_balance,
					'amount'              => $total_wallet_deduct,  
					'after_balance'       => $after_balance,      
					'status'              => 1,
					'type'                => 1,
					'wallet_type'         => 1,      
					'created'             => date('Y-m-d H:i:s'),      
					'description'         => 'Aeps Payout #'.$transaction_id.' Amount Refund.'
				);

				$this->db->insert('member_wallet',$wallet_data);

				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$memberID);
				$this->db->where('id',$transfer_id);
				$this->db->update('new_aeps_payout',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));



				$error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! aeps payout failed.';

				$this->Az->redirect('master/newaeps/fundTransfer/'.$get_beneficiaryData['id'], 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$error.'</div>');
			}

			

             
        }
        
    }
    
    
     public function checkAccountStatus($ref_id = '') {
		
		//get logged user info
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		
		$memberID = $loggedUser['id'];


		$chk_ref_id = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'user_id'=>$memberID,'id'=>$ref_id))->row_array();

		$get_merchant_id = $this->db->get_where('users',array('id'=>$memberID))->row_array();
		$merchant_id = $get_merchant_id['user_code'];



		if(!$chk_ref_id){

			$this->Az->redirect('distributor/newaeps/payout', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
		}
		

		log_message('debug', 'Beneficiary Account check status api called.');

		$datapost = array();
		$datapost['beneid'] = $chk_ref_id['bene_id'];
		$datapost['merchantid'] = $merchant_id;

		log_message('debug', 'Beneficiary Account  check status api post request data - '.json_encode($datapost));
		
		$key = $accountData['paysprint_aeps_key'];
		$iv = $accountData['paysprint_aeps_iv'];
		
		$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
		$body=       base64_encode($cipher);
		$jwt_payload = array(
			'timestamp'=>time(),
			'partnerId'=>$accountData['paysprint_partner_id'],
			'reqid'=>time().rand(1111,9999)
		);
		
		$secret = $accountData['paysprint_secret_key'];

		$token = $this->Jwt_model->encode($jwt_payload,$secret);
		
	        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
		
			$header = [
									'Token:'.$token									
								];
								
								 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						        }
		
		
		$httpUrl = PAYSPRINT_ACCOUNT_STATUS_CHECK_URL;
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $httpUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $datapost,
			CURLOPT_HTTPHEADER => $header
		));

		$output = curl_exec($curl);
		curl_close($curl);
		
		log_message('debug', 'Beneficiary Account  check  status api final response - '.$output);

		$responseData = json_decode($output,true);
		
		$api_data = array(
			'account_id'=>$account_id,
			'user_id' => $memberID,
			'api_url' => $httpUrl,
			'post_data' => json_encode($datapost),
			'api_response' => $output,
			'created' => date('Y-m-d H:i:s')	
		);
		$this->db->insert('new_aeps_payout_api_response',$api_data);

		
		if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true && $responseData['accountstatus'] == 1 ){

			$account_status = $responseData['accountstatus'];

			
			$this->db->where('account_id',$account_id);
			$this->db->where('user_id',$memberID);
			$this->db->where('id',$ref_id);
			$this->db->update('new_payout_beneficiary',array('is_verified'=>1));

			$this->Az->redirect('master/newaeps/payout', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Status checked successfully.</div>');

		}
		else{

			
			$this->Az->redirect('distributor/newaeps/payout', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>');
		}
		
	}



	public function memberRegistration(){

			
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);



		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(17, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_new_aeps_status = $this->User->get_member_new_aeps_status($loggedUser['id']);
		if(!$user_new_aeps_status){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}
		
		$siteUrl = base_url();	
		
  		$get_member_data  = $this->db->get_where('new_aeps_member_kyc',array('member_id'=>$loggedUser['id'],'account_id'=>$account_id,'clear_step'=>2))->row_array();
  		
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
			'content_block' => 'newaeps/member-registration'
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
		if(!in_array(17, $activeService)){
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
				$get_kyc_data =$this->db->get_where('new_aeps_member_kyc',array('member_id'=>$loggedUser['id'],'account_id'=>$account_id,'clear_step'=>2))->row_array();
				
				$aadharNumber = isset($get_kyc_data['aadhar_no']) ? $get_kyc_data['aadhar_no'] : '';
				$mobile = isset($get_kyc_data['mobile']) ? $get_kyc_data['mobile'] : '';
				$agentID = $loggedUser['user_code'];
			
				$api_url = PAYSPRINT_2FA_API_URL;
				$accessmodetype = 'SITE';
								
				$key = $accountData['paysprint_aeps_key'];
				$iv=  $accountData['paysprint_aeps_iv'];
				$datapost = array();
				$datapost['latitude'] = '22.44543';
				$datapost['longitude'] = '77.434';
				$datapost['mobilenumber'] = $mobile;
				$datapost['referenceno'] = $txnID;
				$datapost['ipaddress'] = $_SERVER['REMOTE_ADDR'];
				$datapost['adhaarnumber'] = $aadharNumber;
				$datapost['accessmodetype'] = $accessmodetype;
			//	$datapost['nationalbankidentification'] = $iin;
			//	$datapost['requestremarks'] = $remarks;
				$datapost['data'] = $biometricData;				
				$datapost['timestamp'] = date('Y-m-d H:i:s');				
				$datapost['submerchantid'] = $agentID;

								log_message('debug', '2FA AEPS api post data - '.json_encode($datapost));
								
								$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
								$body=       base64_encode($cipher);
								$jwt_payload = array(
									'timestamp'=>time(),
									'partnerId'=>$accountData['paysprint_partner_id'],
									'reqid'=>time().rand(1111,9999)
								);
								
								$secret = $accountData['paysprint_secret_key'];

								$token = $this->Jwt_model->encode($jwt_payload,$secret);
								
						        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
								
								$header = [
									'Token:'.$token									
								];
								
								 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						        }
						        
								

								log_message('debug', '2FA  AEPS api header data - '.json_encode($header));
								

								$httpUrl = $api_url;

								$curl = curl_init();

								curl_setopt_array($curl, array(
									CURLOPT_URL => $httpUrl,
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_MAXREDIRS => 10,
									CURLOPT_TIMEOUT => 60,
									CURLOPT_CUSTOMREQUEST => 'POST',
									CURLOPT_POSTFIELDS => array('body'=>$body),
									CURLOPT_HTTPHEADER => $header
								));

								$output = curl_exec($curl);
								curl_close($curl);

								$finalResponse = json_decode($output,true);

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

						        $memberData = array(
						        	'account_id' => $account_id,
				            		'member_id' => $memberID,	
				            		'aadhar_number' =>$aadharNumber,
				            		'mobile' =>$mobile,
				            		'txn_id' =>$txnID,
				            		'merchant_code'=>$agentID,
				            		'status' => 0,					        	
						        	'created' => date('Y-m-d H:i:s'),

						        );
						        $this->db->insert('aeps_member_registration',$memberData);

		        if(isset($finalResponse['response_code']) && $finalResponse['response_code'] == 1)
		        {
		        	// update aeps status
                	$this->db->where('member_id',$memberID);
                	$this->db->update('aeps_member_registration',array('status'=>1));

                	 			$loginData = array(
						        	'account_id' => $account_id,
				            		'member_id' => $memberID,	
				            		'aadhar_number' =>$aadharNumber,
				            		'mobile' =>$mobile,
				            		'txn_id' =>$txnID,
				            		'merchant_code'=>$agentID,
				            		'status' => 0,					        	
						        	'created' => date('Y-m-d H:i:s'),

						        );
						        $this->db->insert('aeps_member_login_status',$loginData);

		        	$response = array(
						'status' => true,
						'msg' => $finalResponse['message']
					);
		        }
		        else
		        {
		        	$response = array(
						'status' => 0,
						'msg' => $finalResponse['message']
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
	
	
	
		public function memberLogin(){

			
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(17, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$user_new_aeps_status = $this->User->get_member_new_aeps_status($loggedUser['id']);
		if(!$user_new_aeps_status){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AEPS_ACTIVE_ERROR'));
		}
		
		$siteUrl = base_url();	
		
  		$get_member_data  = $this->db->get_where('new_aeps_member_kyc',array('member_id'=>$loggedUser['id'],'account_id'=>$account_id,'clear_step'=>2))->row_array();
  		
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
			'content_block' => 'newaeps/member-login'
		);
		$this->parser->parse('master/layout/column-1' , $data);
		
		
	}
	
	
	public function kycBioAuthNew()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$memberID = $loggedUser['id'];
		$activeService = $this->User->account_active_service($loggedUser['id']);
		$response = array();
		if(!in_array(17, $activeService)){
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
				$get_kyc_data =$this->db->get_where('new_aeps_member_kyc',array('member_id'=>$loggedUser['id'],'account_id'=>$account_id,'clear_step'=>2))->row_array();
				
				$aadharNumber = isset($get_kyc_data['aadhar_no']) ? $get_kyc_data['aadhar_no'] : '';
				$mobile = isset($get_kyc_data['mobile']) ? $get_kyc_data['mobile'] : '';
				$agentID = $loggedUser['user_code'];
			
				$api_url = PAYSPRINT_2FA_API_LOGIN_URL;
				$accessmodetype = 'SITE';
								
				$key = $accountData['paysprint_aeps_key'];
				$iv=  $accountData['paysprint_aeps_iv'];
				$datapost = array();
				$datapost['latitude'] = '22.44543';
				$datapost['longitude'] = '77.434';
				$datapost['mobilenumber'] = $mobile;
				$datapost['referenceno'] = $txnID;
				$datapost['ipaddress'] = $_SERVER['REMOTE_ADDR'];
				$datapost['adhaarnumber'] = $aadharNumber;
				$datapost['accessmodetype'] = $accessmodetype;
			//	$datapost['nationalbankidentification'] = $iin;
			//	$datapost['requestremarks'] = $remarks;
				$datapost['data'] = $biometricData;				
				$datapost['timestamp'] = date('Y-m-d H:i:s');				
				$datapost['submerchantid'] = $agentID;

								log_message('debug', '2FA Login AEPS  api post data - '.json_encode($datapost));
								
								$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
								$body=       base64_encode($cipher);
								$jwt_payload = array(
									'timestamp'=>time(),
									'partnerId'=>$accountData['paysprint_partner_id'],
									'reqid'=>time().rand(1111,9999)
								);
								
								$secret = $accountData['paysprint_secret_key'];

								$token = $this->Jwt_model->encode($jwt_payload,$secret);
								
						        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
								
								$header = [
									'Token:'.$token									
								];
								
								 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						        }
						        
								

								log_message('debug', '2FA  AEPS api header data - '.json_encode($header));
								

								$httpUrl = $api_url;

								$curl = curl_init();

								curl_setopt_array($curl, array(
									CURLOPT_URL => $httpUrl,
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_MAXREDIRS => 10,
									CURLOPT_TIMEOUT => 60,
									CURLOPT_CUSTOMREQUEST => 'POST',
									CURLOPT_POSTFIELDS => array('body'=>$body),
									CURLOPT_HTTPHEADER => $header
								));

								$output = curl_exec($curl);
								curl_close($curl);

								$finalResponse = json_decode($output,true);

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


		        if(isset($finalResponse['response_code']) && $finalResponse['response_code'] == 1)
		        {
		        	// update aeps status
                	$this->db->where('member_id',$memberID);
                	$this->db->update('aeps_member_login_status',array('status'=>1));
                        
		        	$response = array(
						'status' => true,
						'msg' => $finalResponse['message']
					);
		        }
		        else
		        {
		        	$response = array(
						'status' => 0,
						'msg' => $finalResponse['message']
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


	public function merchantAuthenticity(){
	    $account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$memberID = $loggedUser['id'];
		$memberData = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
		$member_code = $memberData['user_code'];
		$member_pin = md5($memberData['decoded_transaction_password']);
		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(17, $activeService)){
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! AEPS Service Not Active.'
			);
		}
		else
		{
			$agentID = $loggedUser['user_code'];
			$user_aeps_status = $this->User->get_member_new_aeps_status($memberID);
			$response = array();
			if($user_aeps_status)
			{
				$post = file_get_contents('php://input');
				$post = json_decode($post, true);
				if($post)
				{
				    $get_kyc_data =$this->db->order_by('id', 'desc')->get_where('new_aeps_member_kyc',array('member_id'=>$loggedUser['id'],'account_id'=>$account_id))->row_array();
			        $aadharNumber = isset($get_kyc_data['aadhar_no']) ? $get_kyc_data['aadhar_no'] : '';
			        $mobile = isset($get_kyc_data['mobile']) ? $get_kyc_data['mobile'] : '';
			        
					$serviceType = $post['ServiceType'];
					$deviceIMEI = $post['deviceIMEI'];
					$biometricData = $post['BiometricData'];
					$amount = $post['Amount'];
					$iin = $post['IIN'];
					$bank_pipe = $post['bank_pipe'];

					log_message('debug', 'Merchant Authenticity Aeps API Called');
					
					$accessmodetype = 'SITE';
				    $txnID = uniqid('CSWD' . rand(11,99));
			    
				    //Txn Merchant Authenticity CW (NEW) start
				    if($bank_pipe == "bank2"){
				        $login2fa_api_url = PAYSPRINT_MERCHANT_AUTHENTICITY_URL_BANK2;
				    }
				    elseif($getBank['bank_type'] == ''){
    				    $login2fa_api_url = PAYSPRINT_MERCHANT_AUTHENTICITY_URL_BANK2;
    				}
				    else{
				        $login2fa_api_url = PAYSPRINT_MERCHANT_AUTHENTICITY_URL_BANK3;
				    }
				    
				    $key =$accountData['paysprint_aeps_key'];
					$iv=  $accountData['paysprint_aeps_iv'];
					$datapost = array();
					$datapost['latitude'] = '22.44543';
					$datapost['longitude'] = '77.434';
					$datapost['mobilenumber'] = $mobile;
					$datapost['referenceno'] = $txnID;
					$datapost['ipaddress'] = $_SERVER['REMOTE_ADDR'];
					$datapost['adhaarnumber'] = $aadharNumber;
					$datapost['accessmodetype'] = $accessmodetype;
				// 	$datapost['nationalbankidentification'] = $iin;
		// 			$datapost['requestremarks'] = $remarks;
					$datapost['data'] = $biometricData;
		// 			$datapost['pipe'] = $bank_pipe;
					$datapost['timestamp'] = date('Y-m-d H:i:s');
		// 			$datapost['transcationtype'] = $txnType;
					$datapost['submerchantid'] = $agentID;
					
					log_message('debug', 'Txn Merchant Authenticity CW (NEW) ONE Post Data - '.json_encode($datapost));
						
					$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
					$body=       base64_encode($cipher);
					$jwt_payload = array(
						'timestamp'=>time(),
						'partnerId'=>$accountData['paysprint_partner_id'],
						'reqid'=>time().rand(1111,9999)
					);
					
					$secret = $accountData['paysprint_secret_key'];

					$token = $this->Jwt_model->encode($jwt_payload,$secret);
					
			        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
					
					$header = [
						'Token:'.$token									
					];

					 if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						        }
						        
					
					log_message('debug', 'Txn Merchant Authenticity CW (NEW) ONE header data - '.json_encode($header));

					$httpUrl = $login2fa_api_url;
					$curl = curl_init();

					curl_setopt_array($curl, array(
						CURLOPT_URL => $httpUrl,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 60,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS => array('body'=>$body),
						CURLOPT_HTTPHEADER => $header
					));

					$output = curl_exec($curl);
					curl_close($curl);

					log_message('debug', 'Txn Merchant Authenticity CW (NEW) ONE api response - '.json_encode($output));
					
					//Txn Merchant Authenticity CW (NEW) end
					
					$responseData = json_decode($output,true);
					
					$apiData = array(
						'account_id' =>$account_id,
						'user_id' => $memberID,
						'api_url' => $httpUrl,
						'api_response' => $output,
						'post_data' => json_encode($datapost),
						'created' => date('Y-m-d H:i:s'),
						'created_by' => $memberID
					);
					$this->db->insert('aeps_api_response',$apiData);
					
				    if(isset($responseData['response_code']) && $responseData['response_code'] == 1 && $responseData['status'] == true)
					{
					    $MerAuthTxnId = $responseData['MerAuthTxnId'];
					    $response = array(
            				'status' => 1,
            				'msg' => $responseData['message'].' Please Attech Customer Biomatric',
            				'MerAuthTxnId' => $MerAuthTxnId
            			);
					}
					else{
					    $response = array(
            				'status' => 0,
            				'msg' => $responseData['message']
            			);
					}
				}
				else{
				    $response = array(
        				'status' => 0,
        				'msg' => "Please Provide All Details"
        			);
				}
			}
			else{
			    $response = array(
    				'status' => 0,
    				'msg' => "Service Not Active"
    			);
			}
		}
		echo json_encode($response);
	}

	
}