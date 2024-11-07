		<?php
		ob_start();
		if(!defined('BASEPATH'))
			exit('No direct scrip access allowed');

		/*
		* login Register controller for Frontend
		* 
		* this controller user for login, register, logout, forgot password, reset password
		* @author trilok
		*/
 require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
		class Web extends CI_Controller{

			public function __construct() {
				parent::__construct();
		    //load language
				$this->lang->load('admin/api', 'english');
				$this->lang->load('email', 'english');
				$this->lang->load('front/recharge', 'english');
				$this->load->model('service/Api_model');
				$this->load->model('service/Bbps_model');
				$this->load->model('service/Iciciaeps_model');
				$this->load->model('service/Aeps_model');
				$this->load->model('service/Newaeps_model');
				$this->load->model('admin/Jwt_model');	

			}

			public function version()
			{
				$data = array();
				$country_data = array();
			// get country list
				$country_list = $this->db->get('api_version')->row_array();
				$response = array(
					'oldVersion' => $country_list['old_version'],
					'newVersion' => $country_list['new_version']
				);


				echo json_encode(array($response));
			}



			public function registerAuth(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'Registeration API Post Data - '.json_encode($post));	
				$this->load->library('form_validation');
				$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
				$this->form_validation->set_rules('email', 'Email', 'required|xss_clean|valid_email');
				$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|min_length[10]|max_length[10]|numeric');
				$this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
				$this->form_validation->set_rules('refercode', 'Refer Code', 'required|xss_clean');
				$this->form_validation->set_rules('transaction_password', 'Transaction Password', 'required|xss_clean|min_length[4]|max_length[4]');

				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry!! Details Not Valid.'
					);
				}
				else
				{

					$referral_id = $post['refercode'];

					$post['member_position'] = 'L';

		        // check member id valid or not
					if($post['refercode'] == "SNPADMIN"){
						$chk_member = 1;    
					}
					elseif(!empty($post['refercode'])){
						$chk_member = $this->db->where_in('role_id',array(2,5))->get_where('users',array('user_code'=>$referral_id))->num_rows();
					}


					$is_franchise_paid_limit_error = 0;
					if($chk_member['role_id'] == 5){

						$paid_member_limit = isset($chk_member['member_limit']) ? $chk_member['member_limit'] : 0;

						$franchise_total_paid_member = $this->db->get_where('users',array('created_by'=>$chk_member['id']))->num_rows();

						if($paid_member_limit <= $franchise_total_paid_member){

							$is_franchise_paid_limit_error = 1;
						}

					}


					if($is_franchise_paid_limit_error == 1){

						$response = array(
							'status' => 0,
							'message' => 'Sorry ! this franchise exceed the refferal member limit.'
						);

					}
					else{


						$chk_email_mobile =$this->db->query("SELECT * FROM tbl_users WHERE email = '$post[email]' or mobile = '$post[mobile]'")->num_rows();

						if($chk_email_mobile)           
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Email or Mobile Already Exists in our system.'
							);
						}
						elseif(!$chk_member)           
						{
							log_message('debug', 'Front Register Referral Not Exits');	

							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Referral Id is not valid.'
							);
						}
						else
						{	

							$mobile = isset($post['mobile']) ? $post['mobile'] : '';

							$email = isset($post['email']) ? $post['email'] : '';

							$otp_code = rand(111111,999999);

							$encrypt_otp_code = do_hash($otp_code);

							$otp_data = array(

								'otp_code' => $otp_code,
								'encrypt_otp_code' => $encrypt_otp_code,
								'mobile' => $mobile,
								'status' => 0,
								'json_post_data' => json_encode($post),
								'created' => date('Y-m-d h:i:s')

							);

							$this->db->insert('users_otp',$otp_data);

							// send login otp
							$email_status = $this->User->sendRegisterOtp($otp_code,$email);

		                    // send login otp sms
							$sms_status = $this->User->sendLoginOtpSms($otp_code,$mobile);

							if($email_status || $sms_status){

								$response = array(
									'status' => 1,
									'message' => 'Otp sent to your mobile or email. Please verify',
									'encrypt_otp_code' => $encrypt_otp_code
								); 

							}
							else{

								$response = array(
									'status' => 0,
									'message' => 'Sorry!! Something Went Wrong.Please try again.'
								); 

							}

						}
					}		

				}
				log_message('debug', 'Registeration API Response - '.json_encode($response));	
				echo json_encode($response);

			}


			public function registerOtpVerifyAuth(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'Registeration Otp Verify API Post Data - '.json_encode($post));	
				$this->load->library('form_validation');
				$this->form_validation->set_rules('otp_code', 'Otp Code', 'required|xss_clean');
				$this->form_validation->set_rules('encrypt_otp_code', 'encrypt_otp_code', 'required|xss_clean');
				
				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry!! Otp Not Valid.'
					);
				}
				else
				{	
					$otp_code = isset($post['otp_code']) ? $post['otp_code'] : '';

					$encrypt_otp_code = isset($post['encrypt_otp_code']) ? $post['encrypt_otp_code'] : '';	

					$chk_otp = $this->db->get_where('users_otp',array('otp_code'=>$otp_code,'encrypt_otp_code'=>$encrypt_otp_code,'status'=>0))->row_array();

					if(!$chk_otp){

						$response = array(
						  'status'  => 0,
						  'message'	=> 'Sorry!! otp not valid.'
						);

					}
					else{

						$this->db->where('otp_code',$otp_code);
						$this->db->where('encrypt_otp_code',$encrypt_otp_code);
						$this->db->update('users_otp',array('status'=>1));

						$post_data = json_decode($chk_otp['json_post_data'],true);

						$referral_id = $post_data['refercode'];

						$post_data['member_position'] = 'L';

			       		// check member id valid or not
						if($post_data['refercode'] == "SNPADMIN"){
							$chk_member = 1;    
						}
						elseif(!empty($post_data['refercode'])){
							$chk_member = $this->db->where_in('role_id',array(2,5))->get_where('users',array('user_code'=>$referral_id))->num_rows();
						}


						$is_franchise_paid_limit_error = 0;
						if($chk_member['role_id'] == 5){

							$paid_member_limit = isset($chk_member['member_limit']) ? $chk_member['member_limit'] : 0;

							$franchise_total_paid_member = $this->db->get_where('users',array('created_by'=>$chk_member['id']))->num_rows();

							if($paid_member_limit <= $franchise_total_paid_member){

								$is_franchise_paid_limit_error = 1;
							}

						}


						if($is_franchise_paid_limit_error == 1){

							$response = array(
								'status' => 0,
								'message' => 'Sorry ! this franchise exceed the refferal member limit.'
							);

						}
						else{


							$chk_email_mobile =$this->db->query("SELECT * FROM tbl_users WHERE email = '$post_data[email]' or mobile = '$post_data[mobile]'")->num_rows();

							if($chk_email_mobile)           
							{
								$response = array(
									'status' => 0,
									'message' => 'Sorry!! Email or Mobile Already Exists in our system.'
								);
							}
							elseif(!$chk_member)           
							{
								log_message('debug', 'Front Register Referral Not Exits');	

								$response = array(
									'status' => 0,
									'message' => 'Sorry!! Referral Id is not valid.'
								);
							}
							else
							{	

								log_message('debug', 'Front Register Success.');	
								$user_id = $this->Api_model->registerMember($post_data);

								if($user_id)
								{  
									
									$get_user_data =$this->db->get_where('users',array('id'=>$user_id,'role_id'=>2))->row_array();

									$user_ip_address = $_SERVER['REMOTE_ADDR'];
				                	// update cart temp data
									$this->db->where('ip',$user_ip_address);
									$this->db->update('cart_temp_data',array('user_id'=>$get_user_data['id']));

				                	
									$data = array(

										'name' => $get_user_data['name'],
										'user_code'=>$get_user_data['user_code'],
										'user_id'  =>$get_user_data['id'] 

									);

									$user_ip_address = $this->User->get_user_ip();

									$password = $get_user_data['password'];

									// generate token
									$plain_txt = $get_user_data['id'].'|'.$password.'|'.$user_ip_address;
									$token = $this->User->generateAppToken('encrypt', $plain_txt);


									$header_data = apache_request_headers();

									$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

									$this->db->where('id',$get_user_data['id']);
									$this->db->update('users',array('device_id'=>$Deviceid));

									$response = array(
										'status' => 1,
										'message' => 'Logged in Successfully.',
										'user_data'=>$data,
										'token' => $token
									);  

								}
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry!! Something Went Wrong.'
									);    

								}
							}
						}
					}		

				}
				log_message('debug', 'Registeration Otp Verify API Response - '.json_encode($response));	
				echo json_encode($response);

			}



			public function userAuth(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'Login User Auth API Post Data - '.json_encode($post));	
				$this->load->library('form_validation');

				$this->form_validation->set_rules('username', 'Email or Mobile', 'required|xss_clean');
				$this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
				$this->form_validation->set_rules('device_id', 'DeviceID', 'required|xss_clean');
				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => 'Please Enter Username or Password.'
					);
				}
				else
				{

					$username = $post['username'];
					$password = do_hash($post['password']);


					$chk_email_mobile =$this->db->query("SELECT * FROM tbl_users WHERE (username = '$username' or mobile = '$username' or email = '$username') and password = '$password' and role_id = 2")->num_rows();
					if(!$chk_email_mobile)           
					{

						$response = array(
							'status' => 0,
							'message' => 'Sorry!! Username or Password is Wrong.'
						);  

					}
					else
					{
						$get_user_data =$this->db->query("SELECT * FROM tbl_users WHERE (username = '$username' or mobile = '$username'  or email = '$username') and password = '$password' and role_id = 2")->row_array();
						if($get_user_data['is_active'] == 0)
						{

							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Your Account is not Active.'
							);   
						}
						elseif($get_user_data['is_verified'] == 0)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Your Account is not Verified.'
							);   

						}
						else
						{	

							$chk_fcm = $this->db->get_where('users',array('id'=>$get_user_data['id'],'device_id'=>$post['device_id']))->row_array();

							if(!$chk_fcm){

								$login_id = time().rand(1111,9999);
								$otp_code = rand(111111,999999);
								$encode_login_id = do_hash($login_id);
		                    // save login temp data
								$tempData = array(
									'login_id' => $login_id,
									'encode_login_id' => $encode_login_id,
									'post_data' => json_encode($post),
									'logged_user_id' => $get_user_data['id'],
									'is_verify' => 1,
									'otp_code' => $otp_code,
									'status' => 0,
									'created' => date('Y-m-d H:i:s')
								);
								$this->db->insert('login_temp',$tempData);

		                    // send login otp
								$this->User->sendLoginOtp($otp_code,$get_user_data['email']);

		                    // send login otp sms
								$this->User->sendLoginOtpSms($otp_code,$get_user_data['mobile']);

								$response = array(
									'status' => 1,
									'message' => 'Otp sent to your registered Email and Mobile. Please verify to login.',
									'is_otp' => 1,
									'encode_login_id'=>$encode_login_id,
								);
							}
							else{

								$get_user_data =$this->db->get_where('users',array('id'=>$get_user_data['id'],'role_id'=>2))->row_array();

								$user_ip_address = $_SERVER['REMOTE_ADDR'];
			                // update cart temp data
								$this->db->where('ip',$user_ip_address);
								$this->db->update('cart_temp_data',array('user_id'=>$get_user_data['id']));

								$user_ip_address = $this->User->get_user_ip();

								// generate token
								$plain_txt = $get_user_data['id'].'|'.$password.'|'.$user_ip_address;
								$token = $this->User->generateAppToken('encrypt', $plain_txt);

								$data = array(

									'name' => $get_user_data['name'],
									'user_code'=>$get_user_data['user_code'],
									'user_id'  =>$get_user_data['id'], 
									'token' => $token
								);


								$header_data = apache_request_headers();

								$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

								$this->db->where('id',$get_user_data['id']);
								$this->db->update('users',array('device_id'=>$Deviceid));
								
								$response = array(
									'status' => 1,
									'message' => 'Logged in Successfully.',
									'user_data'=>$data,
								);
							}    

						}

					}	

				}
				log_message('debug', 'Login User Auth API Response - '.json_encode($response));	
				echo json_encode($response);

			}


			public function loginAuth(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'Login Auth API Post Data - '.json_encode($post));	
				$this->load->library('form_validation');

				$this->form_validation->set_rules('transaction_password', 'Transaction Password', 'required|xss_clean');
				$this->form_validation->set_rules('encode_login_id', 'encode_login_id', 'required|xss_clean');

				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => 'Please Enter Transaction Password.'
					);
				}
				else
				{

					$encode_login_id = $post['encode_login_id'];
		        // check login id valid or not
					$chk_login_id = $this->db->get_where('login_temp',array('encode_login_id'=>$encode_login_id,'status'=>0))->num_rows();
					if(!$chk_login_id)
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry!! Login Failed.'
						);   
					}
					else{

						$transaction_password = $post['transaction_password'];

						$get_user_id = $this->db->get_where('login_temp',array('encode_login_id'=>$encode_login_id,'otp_code'=>$transaction_password,'status'=>0))->row_array();
						$user_id = isset($get_user_id['logged_user_id']) ? $get_user_id['logged_user_id'] : 0 ;
						if(!$get_user_id)           
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Otp not valid.'
							);   
						}
						else
						{

							$get_user_data =$this->db->get_where('users',array('id'=>$user_id,'role_id'=>2))->row_array();

							$user_ip_address = $_SERVER['REMOTE_ADDR'];
		                	// update cart temp data
							$this->db->where('ip',$user_ip_address);
							$this->db->update('cart_temp_data',array('user_id'=>$get_user_data['id']));

		                	// update login id status
							$this->db->where('encode_login_id',$encode_login_id);
							$this->db->where('status',0);
							$this->db->update('login_temp',array('status'=>1));


							$user_ip_address = $this->User->get_user_ip();

							$password = $get_user_data['password'];
							
							// generate token
							$plain_txt = $get_user_data['id'].'|'.$password.'|'.$user_ip_address;
							$token = $this->User->generateAppToken('encrypt', $plain_txt);

							$header_data = apache_request_headers();

							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

							$data = array(

								'name' => $get_user_data['name'],
								'user_code'=>$get_user_data['user_code'],
								'user_id'  =>$get_user_data['id'],
								'token' => $token

							);

							
							$this->db->where('id',$get_user_data['id']);
							$this->db->update('users',array('device_id'=>$Deviceid));

							$response = array(
								'status' => 1,
								'message' => 'Logged in Successfully.',
								'user_data'=>$data,
								'token' => $token
							);


						}
					}
					log_message('debug', 'Login Auth API Response - '.json_encode($response));	
					echo json_encode($response);

				}

			}



			public function forgotAuth(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'Forgot API Post Data - '.json_encode($post));	
				$this->load->library('form_validation');
			    //$this->form_validation->set_data($this->input->get());
				$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => lang('LOGIN_VALID_FAILED')
					);
				}
				else
				{
					$username = $post['mobile'];
					$chk_email_mobile =$this->db->query("SELECT * FROM tbl_users WHERE (username = '$username' or mobile = '$username' or email = '$username') and role_id = 2")->num_rows();
					if(!$chk_email_mobile)           
					{
						$response = array(
							'status' => 0,
							'message' => lang('FORGOT_ERROR')
						);
					}
					else
					{
						$get_user_data =$this->db->query("SELECT * FROM tbl_users WHERE (username = '$username' or mobile = '$username' or email = '$username') and role_id = 2")->row_array();
						if($get_user_data['is_active'] == 0)
						{
							$response = array(
								'status' => 0,
								'message' => lang('ACCOUNT_ACTIVE_ERROR')
							);
						}
						else
						{
							
							$post = array();
							$post['mobile'] = $get_user_data['mobile'];
							$post['userID'] = $get_user_data['id'];
						    
							$otp_code = $this->User->generate_unique_otp();
							$encrypt_otp_code = do_hash($otp_code);
                            
							$otp_data = array(
								'otp_code' => $otp_code,
								'encrypt_otp_code' => $encrypt_otp_code,
								'mobile' => $get_user_data['mobile'],
								'status' => 0,
								'api_response' => '',
								'json_post_data' => json_encode($post),
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('users_otp',$otp_data);

							$msg = 'Dear User,
							You are trying to update your password on Sonika Pay, Your OTP is : '.$otp_code.'
							If you have any issue please contact us.
							Thanks Sonika Pay';

							$message = sprintf(lang('REGISTRATION_EMAIL'),$msg,$msg);
							$this->User->forgot_otp_mail($get_user_data['email'],$message);

		                    // send forgot otp sms
							$this->User->sendLoginOtpSms($otp_code,$get_user_data['mobile']);	

							$response = array(
								'status' => 1,
								'message' => lang('REGISTER_OTP_SEND_SUCCESS'),
								'message' => 'OTP sent to your registered Email and Mobile.',
								'otp'     => $encrypt_otp_code
							);

						}

					}

				}
				log_message('debug', 'Forgot API Response - '.json_encode($response));	
				echo json_encode($response);

			}

			public function forgotOTPAuth(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'Forgot OTP API Post Data - '.json_encode($post));	
				$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
				$this->form_validation->set_rules('otp', 'OTP', 'required|xss_clean');
				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => 'Please Enter Otp'
					);
				}
				else
				{
					$chk_email_mobile =$this->db->get_where('users_otp',array('otp_code'=>$post['otp'],'status'=>0))->num_rows();
					if($chk_email_mobile)           
					{
						$get_otp_data =$this->db->get_where('users_otp',array('otp_code'=>$post['otp'],'status'=>0))->row_array();

						$post_data = json_decode($get_otp_data['json_post_data'],true);

						$this->db->where('id',$get_otp_data['id']);
						$this->db->update('users_otp',array('status'=>1));
						$response = array(
							'status' => 1,
							'message' => 'Otp Verified Successfully',
							'userID' => $post_data['userID']
						);

					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Otp Not valid'
						);

					}

				}
				log_message('debug', 'Forgot OTP API Response - '.json_encode($response));	
				echo json_encode($response);

			}

			public function updatePasswordAuth(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'Update Password API Post Data - '.json_encode($post));	
				$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
				$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
				$this->form_validation->set_rules('password', 'New Password', 'required|xss_clean');
				$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|xss_clean|matches[password]');
				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => lang('LOGIN_VALID_FAILED')
					);
				}
				else
				{	
					$user_id = $post['userID'];

					
					$this->db->where('id',$post['userID']);
					$this->db->update('users',array('password'=>do_hash($post['password']),'decode_password'=>$post['password']));
					$response = array(
						'status' => 1,
						'message' => lang('PASSWORD_UPDATE_SUCCESS')
					);
					
				}
				log_message('debug', 'Update Password API Response - '.json_encode($response));	
				echo json_encode($response);

			}

			public function userDetail(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'User Detail API Response Post Data - '.json_encode($post));	
				$this->load->library('form_validation');
		    	$this->form_validation->set_rules('user_id', 'User Id', 'required|xss_clean');
				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => 'User Id Not Valid'
					);
				}
				else
				{	

					$user_id = $post['user_id'];

					$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

					$password = isset($userData['password']) ? $userData['password'] : '';

					$header_data = apache_request_headers();
					
					log_message('debug', 'User Detail API Response Post Data - '.json_encode($header_data));

					$token = isset($header_data['Token']) ? $header_data['Token'] : '';

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

					if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

						$response = array(
							'status' => 0,
							'message' => 'Session out.Please Login Again.'
						);
					}
					else{


						$userID = $post['user_id'];
						$fcm_id = isset($post['fcm_id']) ? $post['fcm_id'] : '';
						$device_id = isset($post['device_id']) ? $post['device_id'] : ''; 
			        // check user credential
						$chk_user_credential =$this->db->query("SELECT * FROM tbl_users WHERE (id = '$userID') and role_id = 2")->row_array();

						if($fcm_id != ''){

							$this->db->where('id',$userID);
							$this->db->update('users',array('fcm_id'=>$fcm_id));

						}

						if($device_id != ''){

							$this->db->where('id',$userID);
							$this->db->update('users',array('device_id'=>$device_id));

						}

						if(!$chk_user_credential)
						{
							$response = array(
								'status' => 0,
								'message' => 'User Id Not Valid'
							);

						}
						else
						{
							$get_user_data =$this->db->query("SELECT * FROM tbl_users WHERE (id = '$userID') and role_id = 2")->row_array();
							$is_active = isset($get_user_data['is_active']) ? $get_user_data['is_active'] : 0 ;
							if(!$is_active)
							{
								$response = array(
									'status' => 0,
									'message' => lang('PROFILE_ACTIVE_ERROR')
								);
							}
							else
							{   

								$activeService = $this->User->account_active_service($userID);
								if(in_array(1, $activeService)){
									$is_recharge_active = 1;	
								}
								else{
									$is_recharge_active = 0;	
								}

								if(in_array(2, $activeService)){
									$is_money_transfer_active = 1;	
								}
								else{
									$is_money_transfer_active = 0;	
								}


								if(in_array(7, $activeService)){
									$is_main_wallet_transfer_active = 1;	
								}
								else{
									$is_main_wallet_transfer_active = 0;	
								}

								if(in_array(8, $activeService)){
									$is_aeps_wallet_transfer_active = 1;	
								}
								else{
									$is_aeps_wallet_transfer_active = 0;	
								}

								if(in_array(9, $activeService)){
									$is_commission_wallet_transfer_active = 1;	
								}
								else{
									$is_commission_wallet_transfer_active = 0;	
								}


								if(in_array(4, $activeService)){
									$is_bbps_active = 1;	
								}
								else{
									$is_bbps_active = 0;	
								}

								$is_apes_active = 0;
								$activeService = $this->User->account_active_service($userID);
								if(in_array(3, $activeService)){
									$is_apes_active = 1;
								}

								$is_new_apes_active = 0;
								if(in_array(3, $activeService)){
									$is_new_apes_active = 1;
								}

								$user_aeps_status = $this->User->get_user_aeps_status($userID);
								$user_icici_aeps_status = $this->User->get_member_icici_aeps_status($userID);
								$user_new_aeps_status = $this->User->get_member_new_aeps_status($userID);

								$sliderList = $this->db->get_where('website_slider',array('is_app'=>1))->result_array();
								$sliderData = array();
								if($sliderList)
								{
									foreach($sliderList as $skey=>$slist)
									{
										$sliderData[$skey]['link'] = $slist['link'];
										$sliderData[$skey]['imageUrl'] = $slist['image'];
									}
								}


								$documentList = $this->db->get_where('document_category',array('status'=>1))->result_array();

								$documentData = array();
								if($documentList)
								{
									foreach ($documentList as $key => $value) {
										$documentData[$key]['title'] = $value['title'];
										$documentData[$key]['id'] = $value['id'];
									}
								}




								$affiliate = $this->db->get('affiliate')->result_array();

								$affiliateList = array();
								if($affiliate)
								{
									foreach ($affiliate as $afkey => $afvalue) {
										$affiliateList[$afkey]['title'] = $afvalue['title'];
										$affiliateList[$afkey]['image'] = $afvalue['image'];
										$affiliateList[$afkey]['link'] = $afvalue['link'];
									}
								}
								
								$popup = $this->db->get_where('site_settings',array('id'=>1))->row_array();
								$popup_image = $popup['popup_banner'];
								




								$today_date = date('Y-m-d');


								$get_user_address = $this->db->get_where('user_residential_address',array('user_id'=>$userID))->row_array();

								$user_pincode = isset($get_user_address['pincode']) ? $get_user_address['pincode'] : '';

								$user_district = isset($get_user_address['city_id']) ? $get_user_address['city_id'] : '';

								$user_state = isset($get_user_address['state_id']) ? $get_user_address['state_id'] : '';

								$areaBanner = $this->db->query("SELECT * FROM tbl_vendor_banner WHERE status = 2")->result_array();


								$areaBannerData = array();
								if($areaBanner)
								{   
									$i = 0;
									foreach ($areaBanner as $arKey => $arvalue) {

										$vendor_id = $arvalue['vendor_id'];

										$get_store_vendor = $this->db->get_where('store_vendor',array('id'=>$vendor_id))->row_array();

										$vendor_pincode = isset($get_store_vendor['pincode']) ? $get_store_vendor['pincode'] : '';

										$vendor_city = isset($get_store_vendor['city_id']) ? $get_store_vendor['city_id'] : '';

										$vendor_state = isset($get_store_vendor['state_id']) ? $get_store_vendor['state_id'] : '';

										$chk_vendor_pincode_package = $this->db->query("SELECT a.* FROM tbl_vendor_package_purchase_history AS a INNER JOIN tbl_vendor_package as b ON a.package_id = b.id WHERE b.type = 'Pincode' AND a.end_date > '$today_date' AND a.vendor_id = '$vendor_id'")->row_array();

										$chk_vendor_district_package = $this->db->query("SELECT a.* FROM tbl_vendor_package_purchase_history AS a INNER JOIN tbl_vendor_package as b ON a.package_id = b.id WHERE b.type = 'District' AND a.end_date > '$today_date' AND a.vendor_id = '$vendor_id'")->row_array();

										$chk_vendor_state_package = $this->db->query("SELECT a.* FROM tbl_vendor_package_purchase_history AS a INNER JOIN tbl_vendor_package as b ON a.package_id = b.id WHERE b.type = 'State' AND a.end_date > '$today_date' AND a.vendor_id = '$vendor_id'")->row_array();

										if(($chk_vendor_pincode_package && $vendor_pincode == $user_pincode) || ($chk_vendor_district_package && $vendor_city == $user_district) || ($chk_vendor_state_package && $vendor_state == $user_state)){

											$areaBannerData[$i]['banner'] = base_url($arvalue['banner']);
											$i++;}

										}
									}




				            //get direct income
									$get_direct_income = $this->db->select('SUM(wallet_settle_amount) as total_income')->get_where('direct_income',array('paid_to_member_id'=>$userID,'is_paid'=>1))->row_array();
									$direct_income = isset($get_direct_income['total_income']) ? 'INR '.number_format($get_direct_income['total_income'],2) : 'INR 0.00' ;

					        //get level income
									$get_level_income = $this->db->select('SUM(wallet_settle_amount) as total_income')->get_where('level_income',array('paid_to_member_id'=>$userID,'is_paid'=>1))->row_array();
									$level_income = isset($get_level_income['total_income']) ? 'INR '.number_format($get_level_income['total_income'],2) : 'INR 0.00';


									$get_total_recharge_income = $this->db->select('SUM(commision_amount) as total_income')->get_where('tbl_level_commision',array('level_num >'=>0,'commission_type'=>'RECHARGE','paid_to_member_id'=>$userID))->row_array();
									$total_recharge_income = isset($get_total_recharge_income['total_income']) ? $get_total_recharge_income['total_income'] : 0 ;


					        // get total binary income
									$get_total_bbps_income = $this->db->select('SUM(commision_amount) as total_income')->get_where('tbl_level_commision',array('level_num >'=>0,'commission_type'=>'BBPS','paid_to_member_id'=>$userID))->row_array();
									$total_bbps_income = isset($get_total_bbps_income['total_income']) ? $get_total_bbps_income['total_income'] : 0 ;


					        // get total binary income
									$get_total_aeps_income = $this->db->select('SUM(commision_amount) as total_income')->get_where('tbl_level_commision',array('level_num >'=>0,'commission_type'=>'AEPS','paid_to_member_id'=>$userID))->row_array();
									$total_aeps_income = isset($get_total_aeps_income['total_income']) ? $get_total_aeps_income['total_income'] : 0 ;

                                     $get_total_royalty_income = "SELECT SUM(amount) as total_amount FROM tbl_member_wallet as a  where a.member_id = $userID AND a.description LIKE '%rank achived%'";
                                     $get_filter_data = $this->db->query($get_total_royalty_income)->row_array();
                                     $total_royalty_income = isset($get_filter_data['total_amount']) ? $get_filter_data['total_amount'] : 0 ;
        
									$total_income = $get_level_income['total_income'] + $get_direct_income['total_income'] + $total_recharge_income + $total_bbps_income + $total_aeps_income + $total_royalty_income;


									$today_date = date('Y-m-d');

									$get_today_direct_income = $this->db->select('SUM(wallet_settle_amount) as total_income')->get_where('direct_income',array('paid_to_member_id'=>$userID,'is_paid'=>1,'DATE(created)'=>$today_date))->row_array();
									$today_direct_income = isset($get_today_direct_income['total_income']) ? $get_today_direct_income['total_income'] : 0 ;

					        //get level income
									$get_today_level_income = $this->db->select('SUM(wallet_settle_amount) as total_income')->get_where('level_income',array('paid_to_member_id'=>$userID,'is_paid'=>1,'DATE(created)'=>$today_date))->row_array();
									$today_level_income = isset($get_today_level_income['total_income']) ? $get_today_level_income['total_income'] : 0 ;


									$get_today_total_recharge_income = $this->db->select('SUM(commision_amount) as total_income')->get_where('tbl_level_commision',array('level_num >'=>0,'commission_type'=>'RECHARGE','paid_to_member_id'=>$userID,'DATE(created)'=>$today_date))->row_array();
									$today_recharge_income = isset($get_today_total_recharge_income['total_income']) ? $get_today_total_recharge_income['total_income'] : 0 ;


					        // get total binary income
									$get_today_total_bbps_income = $this->db->select('SUM(commision_amount) as total_income')->get_where('tbl_level_commision',array('level_num >'=>0,'commission_type'=>'BBPS','paid_to_member_id'=>$userID,'DATE(created)'=>$today_date))->row_array();
									$today_bbps_income = isset($get_today_total_bbps_income['total_income']) ? $get_today_total_bbps_income['total_income'] : 0 ;


					        // get total binary income
									$get_today_total_aeps_income = $this->db->select('SUM(commision_amount) as total_income')->get_where('tbl_level_commision',array('level_num >'=>0,'commission_type'=>'AEPS','paid_to_member_id'=>$userID,'DATE(created)'=>$today_date))->row_array();
									$today_aeps_income = isset($get_today_total_aeps_income['total_income']) ? $get_today_total_aeps_income['total_income'] : 0 ;


									$today_total_income = $today_level_income + $today_direct_income + $today_recharge_income + $today_bbps_income + $today_aeps_income;


									$rank = 'Not Achieved';
									if($get_user_data['current_rank'] == 1)
									{
										$rank = 'Bronze';
									}
									elseif($get_user_data['current_rank'] == 2)
									{
										$rank = 'Silver';
									}
									elseif($get_user_data['current_rank'] == 3)
									{
										$rank = 'Gold';
									}
									elseif($get_user_data['current_rank'] == 4)
									{
										$rank = 'Platinum';
									}
									elseif($get_user_data['current_rank'] == 5)
									{
										$rank = 'Diamond';
									}
									elseif($get_user_data['current_rank'] == 6)
									{
										$rank = 'Royal Star';
									}
									elseif($get_user_data['current_rank'] == 7)
									{
										$rank = 'Crown Star';
									}


									$total_notification = $this->db->query("SELECT * FROM tbl_app_notification WHERE DATE(created) = '$today_date' AND user_id = '$userID' AND is_new = 1")->num_rows();


									$is_cyrus_qr_active = isset($get_user_data['is_cyrus_qr_active']) ? $get_user_data['is_cyrus_qr_active'] : 0;


									$chk_vendor_status = $this->db->get_where('store_vendor',array('user_id'=>$userID))->row_array();

									$vendor_status = isset($chk_vendor_status['status']) ? $chk_vendor_status['status'] : 0;

									$addressData = $this->db->get_where('user_residential_address',array('user_id'=>$userID))->row_array();

									$is_address_updated = 0;
									if($addressData){

										$is_address_updated = 1;	

									}


									$get_store_vendor = $this->db->get_where('store_vendor',array('user_id'=>$userID))->row_array();

									$vendor_id = isset($get_store_vendor['id']) ? $get_store_vendor['id'] : '';

									$today_date = date('Y-m-d');

									$chk_package_purchase = $this->db->query("SELECT * FROM tbl_vendor_package_purchase_history WHERE user_id = '$userID' AND vendor_id = '$vendor_id' AND end_date > '$today_date'")->row_array();

									$is_vendor_package_purchased = 0;

									if($chk_package_purchase){

										$is_vendor_package_purchased = 1;
									}

									$get_new_aeps_kyc_charge = $this->db->get_where('master_setting',array('id'=>1))->row_array();

									$fino_kyc_charge = isset($get_new_aeps_kyc_charge['fino_kyc_charge']) ? $get_new_aeps_kyc_charge['fino_kyc_charge'] : 0;	
									
								$get_is_fund_request = $this->db->get_where('users',array('id'=>$post['user_id']))->row_array();
								
								$is_fund_request = isset($get_is_fund_request['is_fund_request']) ? $get_is_fund_request['is_fund_request'] : 0;	
								
								$get_active_gateway= $this->db->get_where('gateway_setting',array('id'=>1))->row_array();
								$is_active_gateway = isset($get_active_gateway['is_active']) ? $get_active_gateway['is_active'] : '';	
								
								$total_team = $this->db->get_where('users',array('role_id'=>2))->num_rows();
								$total_active_team = $this->db->get_where('users',array('role_id'=>2,'paid_status'=>1))->num_rows();
								$total_deactive_team = $this->db->get_where('users',array('role_id'=>2,'paid_status'=>0))->num_rows();

								$today_active_team = $this->db->get_where('users',array('role_id'=>2,'paid_status'=>1,'DATE(created)'=>$today_date))->num_rows();
								$today_deactive_team = $this->db->get_where('users',array('role_id'=>2,'paid_status'=>0,'DATE(created)'=>$today_date))->num_rows();
								
								$getBinaryDownline = $this->db->query("SELECT * FROM `tbl_member_tree` WHERE `member_id` = ".$userID)->row_array();
                                $binary_downline_str = isset($getBinaryDownline['binary_downline_str']) ? $getBinaryDownline['binary_downline_str'] : '';
                                $todayTotalPaid = 0;
                                $todayTotalUnPaid = 0;
                                if($binary_downline_str)
                                {
                                    $todayTotalPaid = $this->db->query("SELECT * FROM tbl_member_tree as a INNER JOIN tbl_member_investment as b ON b.member_id = a.member_id WHERE a.binary_downline_str LIKE '%".$binary_downline_str."%' AND DATE(b.created) = '".date('Y-m-d')."'")->num_rows();
                                    $todayTotalUnPaid = $this->db->query("SELECT * FROM tbl_member_tree as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.binary_downline_str LIKE '%".$binary_downline_str."%' AND DATE(b.created) = '".date('Y-m-d')."' AND b.paid_status = 0")->num_rows();
                                }

									$user_detail = array(
										'userID' => $get_user_data['id'],
										'user_display_id' => $get_user_data['user_code'],
										'name' => $get_user_data['name'],
										'email' => $get_user_data['email'],
										'mobile' => $get_user_data['mobile'],
										'wallet_balance'=>isset($get_user_data['wallet_balance']) ? $get_user_data['wallet_balance'] : 0.00,
										'aeps_wallet_balance'=>isset($get_user_data['aeps_wallet_balance']) ? $get_user_data['aeps_wallet_balance'] : 0.00,
										'commision_wallet_balance'=>isset($get_user_data['commision_wallet_balance']) ? $get_user_data['commision_wallet_balance'] : 0.00,
										'point'=>isset($get_user_data['point_wallet_balance']) ? $get_user_data['point_wallet_balance'] : 0,
										'profile_photo' => isset($get_user_data['photo']) ? base_url($get_user_data['photo']) : '',
										'fcm_id'        => $get_user_data['fcm_id'],
										'account_upgrade_status' => $get_user_data['current_package_id'] > 1 ? 1 : 0,
										'is_upi_active' => isset($get_user_data['is_upi_active']) ? $get_user_data['is_upi_active'] : 0,
										'vpa_id' => isset($get_user_data['vpa_id']) ? $get_user_data['vpa_id'] : '',
										'user_code' => $get_user_data['user_code'],
										'is_recharge_active' => $is_recharge_active,
										'is_money_transfer_active' => $is_money_transfer_active,
										'is_main_wallet_transfer_active' => $is_main_wallet_transfer_active,
										'is_aeps_wallet_transfer_active' => $is_aeps_wallet_transfer_active,
										'is_commission_wallet_transfer_active' => $is_commission_wallet_transfer_active,
										'is_bbps_active' => $is_bbps_active,
										'is_apes_active' => $is_apes_active,
										'user_aeps_status' => $user_aeps_status,
										'user_icici_aeps_status' => $user_icici_aeps_status,
										'is_new_apes_active' => $is_new_apes_active,
										'user_new_aeps_status' => $user_new_aeps_status,
										'total_direct_downline' => $this->User->get_member_direct_downline_count($userID),
										'total_direct_active' => $this->User->get_member_direct_active_downline_count($userID),
										'total_direct_deactive' => $this->User->get_member_direct_deactive_downline_count($userID),
										'total_downline' => $this->User->get_member_total_downline_member_count($userID),
										'direct_income' => $direct_income,
										'level_income'  => $level_income,
										'total_income'  => $total_income,
										'today_total_income' => $today_total_income,
										'membership'    => $this->User->get_user_membership_type($userID),
										'rank'          => $rank,
										'refferal_link' => base_url('register?referral_id='.$chk_user_credential['user_code']),
										'total_notification' => $total_notification,
										'sliderData' => $sliderData,
										'documentData'=>$documentData,
										'kyc_status' => $get_user_data['kyc_status'],
										'is_cyrus_qr_active' => $is_cyrus_qr_active,
										'vendor_status' => $vendor_status,
										'address'  => isset($addressData['address']) ? $addressData['address'] : '',
										'pincode'  => isset($addressData['pincode']) ? $addressData['pincode'] : '',
										'city_id'  => isset($addressData['city_id']) ? $addressData['city_id'] : '',
										'state_id' => isset($addressData['state_id']) ? $addressData['state_id'] : '',
										'block_id' => isset($addressData['block_id']) ? $addressData['block_id'] : '',
										'is_address_updated' => $is_address_updated,
										'is_vendor_package_purchased' => $is_vendor_package_purchased,
										'affiliateList' => $affiliateList,
										'areaBannerData' => $areaBannerData,
										'paysprint_partner_id' => PAYSPRINT_PARTNER_ID,
										'paysprint_aeps_key' => PAYSPRINT_AEPS_KEY,
										'paysprint_iv' => PAYSPRINT_AEPS_IV,
										'paysprint_secret_key' => PAYSPRINT_SECRET_KEY,
										'paysprint_authorized_key' => PAYSPRINT_AUTHORIZED_KEY,
										'fino_kyc_charge' => $fino_kyc_charge,
										'is_fund_request' =>$is_fund_request,
										'razor_pay_key' => RAZOR_KEY_ID,
										'is_razorypay_active' => 1,
										'pg_type'=>$is_active_gateway,
										'total_active_team' =>$total_active_team,
										'total_deactive_team' =>$total_deactive_team,
										'today_active_team' =>$today_active_team,
										'today_deactive_team' =>$today_deactive_team,
										'total_team' =>$total_team,
										'today_my_active_team' =>  $todayTotalPaid,
										'today_my_deactive_team' => $todayTotalUnPaid,
										'is_popup_banner'=>$popup_image
									);

									$response = array(
										'status' => 1,
										'message' => 'Success',
										'user_detail'=>$user_detail

									);
								}
							}
						}

					}
					log_message('debug', 'User Detail API Response - '.json_encode($response));	
					echo json_encode($response);

				}



				public function operatorList()
				{
		    /*
			Type - Prepaid, Postpaid for Mobile
			Type - DTH
			Type - Datacard
			Type - Landline
			Type - Electricity
			*/
			$post = $this->input->post();

			log_message('debug', 'Get Operator List API Post Data - '.json_encode($_POST));	
			
			$type = isset($post['type']) ? $post['type'] : '';
			$response = array();
			$operator = $this->db->get_where('operator',array('type'=>$type))->result_array();
			
			$data = array();
			if($operator)
			{
				foreach ($operator as $key => $value) {
					$data[$key]['name'] = $value['operator_name'];
					$data[$key]['code'] = $value['operator_code'];
				}
			}

			$response = array(
				'status' => 1,
				'message' => 'Success',
				'data' => $data
			);
			log_message('debug', 'Get Operator List API Response - '.json_encode($response));	
			echo json_encode($response);
		}


		public function circleList()
		{
			$response = array();
			$operator = $this->db->get('circle')->result_array();
			
			$data = array();
			if($operator)
			{
				foreach ($operator as $key => $value) {
					$data[$key]['name'] = $value['circle_name'];
					$data[$key]['code'] = $value['circle_code'];
				}
			}

			$response = array(
				'status' => 1,
				'message' => 'Success',
				'data' => $data
			);
			echo json_encode($response);
		}




		function maximumCheck($num)
		{
			if ($num < 1)
			{
				$this->form_validation->set_message(
					'maximumCheck',
					'The %s field must be grater than 10'
				);
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}


		public function rechargeAuth(){
			
			$response = array();
			$post = $this->input->post();
			log_message('debug', 'Recharge API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('type', 'Type', 'required|xss_clean');
			$this->form_validation->set_rules('txn_pass', 'Txn Pass', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => lang('LOGIN_VALID_FAILED')
				);
			}
			else
			{	
				$user_id = $post['userID'];

				$txn_pass = isset($post['txn_pass']) ? $post['txn_pass'] : '';

				$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

				$password = isset($userData['password']) ? $userData['password'] : '';

				$header_data = apache_request_headers();
				
				log_message('debug', 'User Detail API Response Post Data - '.json_encode($header_data));

				$token = isset($header_data['Token']) ? $header_data['Token'] : '';

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

				if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.'
					);
				}
				else{

					if(do_hash($txn_pass) != $userData['transaction_password']){

						$response = array(
							'status' => 0,
							'message' => 'Txn Password is wrong.'
						);

					}		
					else{

					    /*$response = array(
							'status' => 0,
							'message' => 'Dear Customer, our systems will be under maintenance from 00.00 hrs Sunday 05 July to 18:00 hrs IST Monday,06 July 2020. Services through Website and Mobile application for Recharges, Bill payments and Fund transfer will not be available during this time. We regret the inconvenience caused.'
						);*/

						$userID = $post['userID'];
						$type = $post['type'];
						/* Type = 1 for Mobile */
						/* Type = 2 for DTH */
						/* Type = 3 for Datacard */
						/* Type = 5 for Landline */
						/* Type = 7 for Electricity */
						if($post['type'] == 1)
						{
							$this->load->library('form_validation');
							$this->form_validation->set_rules('number', 'Mobile Number', 'required|numeric|max_length[12]|xss_clean');
							$this->form_validation->set_rules('rechargeType', 'Recharge Type', 'required');
							$this->form_validation->set_rules('operator', 'Operator', 'required');
							$this->form_validation->set_rules('circle', 'Circle', 'required');
							$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
							if ($this->form_validation->run() == FALSE)
							{
								$response = array(
									'status' => 0,
									'message' => 'Please enter required field.'
								);
							}
							else
							{
									// check user valid or not
								$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
								if($chk_user)
								{
									$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
									
									 $reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
									 

									if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									elseif($reserved_wallet_balance < $post['amount']){

										$response = array(
											'status' => 0,
											'message' => 'Sorry!! your wallet balance not sufficient for this recharge.'
										);
									}
									else
									{
											/*// send recharge OTP
											$status = $this->Api_model->sendRechargeOTP($post,$userID);
											$response = array(
												'status' => 1,
												'otp' => $status,
												'message' => lang('RECHARGE_OTP_SEND_SUCCESS')
											);*/


											// generate recharge unique id
											$recharge_unique_id = rand(1111,9999).time();

											$data = array(
												'member_id'          => $userID,
												'recharge_type'      => $type,
												'recharge_subtype'   => $post['rechargeType'],
												'recharge_display_id'=> $recharge_unique_id,
												'mobile'             => $post['number'],
												'account_number'     => isset($post['acnumber']) ? $post_data['acnumber'] : '',
												'operator_code'      => $post['operator'],
												'circle_code'        => $post['circle'],
												'amount'             => $post['amount'],
												'status'         	 => 1,
												'is_from_app'		 =>	1,
												'created'            => date('Y-m-d H:i:s')                  
											);

											$this->db->insert('recharge_history',$data);
											$recharge_id = $this->db->insert_id();

											if($post['rechargeType'] == 1){
												$mobile = $post['number'];
												$operator_code = $post['operator'];
												$circle_code = $post['circle'];
												$amount = $post['amount'];
												// call recharge API
												$api_response = $this->User->prepaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$recharge_unique_id,$userID);
											}
											elseif($post['rechargeType'] == 2){
												$mobile = $post['number'];
												$operator_code = $post['operator'];
												$circle_code = $post['circle'];
												$amount = $post['amount'];
												$account = $post['acnumber'];
												// call recharge API
												$api_response = $this->User->postpaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$account,$recharge_unique_id,$userID);
											}

											if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
											{
												$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

												$after_balance = $before_balance['wallet_balance'] - $post['amount'];    

												$wallet_data = array(
													'member_id'           => $userID,    
													'before_balance'      => $before_balance['wallet_balance'],
													'amount'              => $post['amount'],  
													'after_balance'       => $after_balance,      
													'status'              => 1,
													'type'                => 2,      
													'created'             => date('Y-m-d H:i:s'),      
													'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
												);

												$this->db->insert('member_wallet',$wallet_data);

												$user_wallet = array(
													'wallet_balance'=>$after_balance,        
												);    
												$this->db->where('id',$userID);
												$this->db->update('users',$user_wallet);
												if($api_response['status'] == 1){

													$is_from_paysprint_api = isset($api_response['is_from_paysprint_api']) ? $api_response['is_from_paysprint_api'] : 0;
													// update recharge status
													$this->db->where('id',$recharge_id);
													$this->db->where('recharge_display_id',$recharge_unique_id);
													$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id'],'is_from_paysprint_api'=>$is_from_paysprint_api));

													$message = isset($api_response['msg']) ? $api_response['msg'] : 'Your recharge is in pending.';
													
													$response = array(
														'status' => 1,
														'message' => $message
													);
													
												}
												else
												{	
												    $is_from_paysprint_api = isset($api_response['is_from_paysprint_api']) ? $api_response['is_from_paysprint_api'] : 0;	 
													// update recharge status
													$this->db->where('id',$recharge_id);
													$this->db->where('recharge_display_id',$recharge_unique_id);
													$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id'],'is_from_paysprint_api'=>$is_from_paysprint_api));
													
													// check sponser is franchise or not
                $isSponserFranchise = $this->db->query("SELECT * FROM tbl_users as a INNER JOIN tbl_users as b ON b.id = a.created_by WHERE a.id = '$userID' AND b.role_id = 5")->num_rows();
                if($isSponserFranchise)
                {
                    $getSponserId = $this->db->get_where('users',array('role_id'=>2,'id'=>$userID))->row_array();
                    $sponserId = isset($getSponserId['created_by']) ? $getSponserId['created_by'] : 0 ;
                    if($sponserId)
                    {
                        $this->User->distribute_recharge_commision($recharge_id,$userID,1);

                        $this->User->distribute_franchise_direct_recharge_commision($recharge_id,$userID); 
                    }
                }
                else
                {
                    $this->User->distribute_recharge_commision($recharge_id,$userID);

                    $this->User->distribute_franchise_recharge_commision($recharge_id,$userID);
                }


													$message = isset($api_response['msg']) ? $api_response['msg'] : 'Congratulations!! your recharge is succesfull.';

													$this->User->sendNotification($userID,'Recharge',$message);
													
													$response = array(
														'status' => 1,
														'message' => $message
													);

												}
											}
											else
											{   
												$is_from_paysprint_api = isset($api_response['is_from_paysprint_api']) ? $api_response['is_from_paysprint_api'] : 0;
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('status'=>3,'is_from_paysprint_api'=>$is_from_paysprint_api));
												
												$message = isset($api_response['msg']) ? $api_response['msg'] : 'Sorry!! your recharge is failed.';

												$response = array(
													'status' => 0,
													'message' => $message
												);
											}


											
										}
									}  
									else
									{
										$response = array(
											'status' => 0,
											'message' => 'Sorry!! something went wrong.'
										);
									}
								}
							}
							elseif($post['type'] == 2)
							{
								$this->load->library('form_validation');
								$this->form_validation->set_rules('operator', 'Operator', 'required');
								$this->form_validation->set_rules('circle', 'Circle', 'required');
								$this->form_validation->set_rules('number', 'Card Number', 'required|xss_clean');
								$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
								if ($this->form_validation->run() == FALSE)
								{
									$response = array(
										'status' => 0,
										'message' => 'Please enter required field.'
									);
								}
								else
								{
								// check user valid or not
									$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
									if($chk_user)
									{
										$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
										
										$reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;

											if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									
										elseif($reserved_wallet_balance < $post['amount']){

											$response = array(
												'status' => 0,
												'message' => 'Sorry!! your wallet balance not sufficient for this recharge.'
											);
										}
										else
										{
										/*// send recharge OTP
										$status = $this->Api_model->sendRechargeOTP($post,$userID);
										$response = array(
											'status' => 1,
											'message' => lang('RECHARGE_OTP_SEND_SUCCESS')
										);*/

										// generate recharge unique id
										$recharge_unique_id = rand(1111,9999).time();

										$data = array(
											'member_id'          => $userID,
											'recharge_type'      => $type,
											'recharge_display_id'=> $recharge_unique_id,
											'mobile'             => $post['number'],
											'account_number'     => isset($post['acnumber']) ? $post['acnumber'] : '',
											'operator_code'      => $post['operator'],
											'circle_code'        => $post['circle'],
											'amount'             => $post['amount'],
											'status'         	 => 1,
											'created'            => date('Y-m-d H:i:s')                  
										);

										$this->db->insert('recharge_history',$data);
										$recharge_id = $this->db->insert_id();


										$mobile = $post['number'];
										$operator_code = $post['operator'];
										$circle_code = $post['circle'];
										$amount = $post['amount'];
										// call recharge API
										$api_response = $this->User->prepaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$recharge_unique_id,$userID);

										if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
										{
											$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

											$after_balance = $before_balance['wallet_balance'] - $post['amount'];    

											$wallet_data = array(
												'member_id'           => $userID,    
												'before_balance'      => $before_balance['wallet_balance'],
												'amount'              => $post['amount'],  
												'after_balance'       => $after_balance,      
												'status'              => 1,
												'type'                => 2,      
												'created'             => date('Y-m-d H:i:s'),      
												'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
											);

											$this->db->insert('member_wallet',$wallet_data);

											$user_wallet = array(
												'wallet_balance'=>$after_balance,        
											);    
											$this->db->where('id',$userID);
											$this->db->update('users',$user_wallet);
											if($api_response['status'] == 1){

												$is_from_paysprint_api = isset($api_response['is_from_paysprint_api']) ? $api_response['is_from_paysprint_api'] : 0;
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id'],'is_from_paysprint_api'=>$is_from_paysprint_api));

												$message = isset($api_response['msg']) ? $api_response['msg'] : 'Your recharge is in pending.';
												
												$response = array(
													'status' => 1,
													'message' => $message
												);
												
											}
											else
											{   
												$is_from_paysprint_api = isset($api_response['is_from_paysprint_api']) ? $api_response['is_from_paysprint_api'] : 0;
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id'],'is_from_paysprint_api'=>$is_from_paysprint_api));
												
												// check sponser is franchise or not
                $isSponserFranchise = $this->db->query("SELECT * FROM tbl_users as a INNER JOIN tbl_users as b ON b.id = a.created_by WHERE a.id = '$userID' AND b.role_id = 5")->num_rows();
                if($isSponserFranchise)
                {
                    $getSponserId = $this->db->get_where('users',array('role_id'=>2,'id'=>$userID))->row_array();
                    $sponserId = isset($getSponserId['created_by']) ? $getSponserId['created_by'] : 0 ;
                    if($sponserId)
                    {
                        $this->User->distribute_recharge_commision($recharge_id,$userID,1);

                        $this->User->distribute_franchise_direct_recharge_commision($recharge_id,$userID); 
                    }
                }
                else
                {
                    $this->User->distribute_recharge_commision($recharge_id,$userID);

                    $this->User->distribute_franchise_recharge_commision($recharge_id,$userID);
                }


												$message = isset($api_response['msg']) ? $api_response['msg'] : 'Congratulations!! your recharge is succesfull.';

												$this->User->sendNotification($userID,'Recharge',$message);
												
												$response = array(
													'status' => 1,
													'message' => $message
												);

											}
										}
										else
										{   
											$is_from_paysprint_api = isset($api_response['is_from_paysprint_api']) ? $api_response['is_from_paysprint_api'] : 0;
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('status'=>3,'is_from_paysprint_api'=>$is_from_paysprint_api));

											$message = isset($api_response['msg']) ? $api_response['msg'] : 'Sorry!! your recharge is failed.';
											
											$response = array(
												'status' => 0,
												'message' => $message
											);
										}
										
									}
								}  
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry!! something went wrong.'
									);
								}
							}
						}
						elseif($post['type'] == 3)
						{
							$this->load->library('form_validation');
							$this->form_validation->set_rules('operator', 'Operator', 'required');
							$this->form_validation->set_rules('circle', 'Circle', 'required');
							$this->form_validation->set_rules('number', 'Card Number', 'required|xss_clean');
							$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
							$this->form_validation->set_rules('rechargeType', 'Recharge Type', 'required');
							if ($this->form_validation->run() == FALSE)
							{
								$response = array(
									'status' => 0,
									'message' => 'Please enter required field.'
								);
							}
							else
							{
								// check user valid or not
								$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
								if($chk_user)
								{
									$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
									
									$reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
									

										if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									
									elseif($reserved_wallet_balance < $post['amount']){

										$response = array(
											'status' => 0,
											'message' => 'Sorry!! your wallet balance not sufficient for this recharge.'
										);
									}
									else
									{
										/*// send recharge OTP
										$status = $this->Api_model->sendRechargeOTP($post,$userID);
										$response = array(
											'status' => 1,
											'message' => lang('RECHARGE_OTP_SEND_SUCCESS')
										);*/


										// generate recharge unique id
										$recharge_unique_id = rand(1111,9999).time();

										$data = array(
											'member_id'          => $userID,
											'recharge_type'      => $type,
											'recharge_subtype'   => $post['rechargeType'],
											'recharge_display_id'=> $recharge_unique_id,
											'mobile'             => $post['number'],
											'account_number'     => isset($post['acnumber']) ? $post['acnumber'] : '',
											'operator_code'      => $post['operator'],
											'circle_code'        => $post['circle'],
											'amount'             => $post['amount'],
											'status'         	 => 1,
											'created'            => date('Y-m-d H:i:s')                  
										);

										$this->db->insert('recharge_history',$data);
										$recharge_id = $this->db->insert_id();


										$mobile = $post['number'];
										$operator_code = $post['operator'];
										$circle_code = $post['circle'];
										$amount = $post['amount'];
										// call recharge API
										$api_response = $this->User->prepaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$recharge_unique_id,$userID);

										if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
										{
											$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

											$after_balance = $before_balance['wallet_balance'] - $post['amount'];    

											$wallet_data = array(
												'member_id'           => $userID,    
												'before_balance'      => $before_balance['wallet_balance'],
												'amount'              => $post['amount'],  
												'after_balance'       => $after_balance,      
												'status'              => 1,
												'type'                => 2,      
												'created'             => date('Y-m-d H:i:s'),      
												'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
											);

											$this->db->insert('member_wallet',$wallet_data);

											$user_wallet = array(
												'wallet_balance'=>$after_balance,        
											);    
											$this->db->where('id',$userID);
											$this->db->update('users',$user_wallet);
											if($api_response['status'] == 1){
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
												
												$response = array(
													'status' => 1,
													'message' => 'Your recharge is in pending.'
												);
												
											}
											else
											{
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
												
												// check sponser is franchise or not
                $isSponserFranchise = $this->db->query("SELECT * FROM tbl_users as a INNER JOIN tbl_users as b ON b.id = a.created_by WHERE a.id = '$userID' AND b.role_id = 5")->num_rows();
                if($isSponserFranchise)
                {
                    $getSponserId = $this->db->get_where('users',array('role_id'=>2,'id'=>$userID))->row_array();
                    $sponserId = isset($getSponserId['created_by']) ? $getSponserId['created_by'] : 0 ;
                    if($sponserId)
                    {
                        $this->User->distribute_recharge_commision($recharge_id,$userID,1);

                        $this->User->distribute_franchise_direct_recharge_commision($recharge_id,$userID); 
                    }
                }
                else
                {
                    $this->User->distribute_recharge_commision($recharge_id,$userID);

                    $this->User->distribute_franchise_recharge_commision($recharge_id,$userID);
                }


												$message = 'Congratulations!! your recharge is succesfull.';

												$this->User->sendNotification($userID,'Recharge',$message);
												
												$response = array(
													'status' => 1,
													'message' => 'Congratulations!! your recharge is succesfull.'
												);

											}
										}
										else
										{
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('status'=>3));
											
											$response = array(
												'status' => 0,
												'message' => 'Sorry!! your recharge is failed.'
											);
										}
										
									}
								}  
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry!! something went wrong.'
									);
								}
							}
						}
						elseif($post['type'] == 5)
						{
							$this->load->library('form_validation');
							$this->form_validation->set_rules('operator', 'Operator', 'required');
							$this->form_validation->set_rules('circle', 'Circle', 'required');
							$this->form_validation->set_rules('number', 'Telephone Number', 'required|xss_clean');
							$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
							if ($this->form_validation->run() == FALSE)
							{
								$response = array(
									'status' => 0,
									'message' => 'Please enter required field.'
								);
							}
							else
							{
								// check user valid or not
								$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
								if($chk_user)
								{
									$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
									
										$reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
										

										if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									
									elseif($reserved_wallet_balance < $post['amount']){

										$response = array(
											'status' => 0,
											'message' => 'Sorry!! your wallet balance not sufficient for this recharge.'
										);
									}
									else
									{
										/*// send recharge OTP
										$status = $this->Api_model->sendRechargeOTP($post,$userID);
										$response = array(
											'status' => 1,
											'message' => lang('RECHARGE_OTP_SEND_SUCCESS')
										);*/


										// generate recharge unique id
										$recharge_unique_id = rand(1111,9999).time();

										$data = array(
											'member_id'          => $userID,
											'recharge_type'      => $type,
											'recharge_display_id'=> $recharge_unique_id,
											'mobile'             => $post['number'],
											'account_number'     => isset($post['acnumber']) ? $post['acnumber'] : '',
											'operator_code'      => $post['operator'],
											'circle_code'        => $post['circle'],
											'amount'             => $post['amount'],
											'status'         	 => 1,
											'created'            => date('Y-m-d H:i:s')                  
										);

										$this->db->insert('recharge_history',$data);
										$recharge_id = $this->db->insert_id();


										$mobile = $post['number'];
										$operator_code = $post['operator'];
										$circle_code = $post['circle'];
										$amount = $post['amount'];
										// call recharge API
										$api_response = $this->User->prepaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$recharge_unique_id,$userID);

										if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
										{
											$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

											$after_balance = $before_balance['wallet_balance'] - $post['amount'];    

											$wallet_data = array(
												'member_id'           => $userID,    
												'before_balance'      => $before_balance['wallet_balance'],
												'amount'              => $post['amount'],  
												'after_balance'       => $after_balance,      
												'status'              => 1,
												'type'                => 2,      
												'created'             => date('Y-m-d H:i:s'),      
												'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
											);

											$this->db->insert('member_wallet',$wallet_data);

											$user_wallet = array(
												'wallet_balance'=>$after_balance,        
											);    
											$this->db->where('id',$userID);
											$this->db->update('users',$user_wallet);
											if($api_response['status'] == 1){
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
												
												$response = array(
													'status' => 1,
													'message' => 'Your recharge is in pending.'
												);
												
											}
											else
											{
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
												
												// check sponser is franchise or not
                $isSponserFranchise = $this->db->query("SELECT * FROM tbl_users as a INNER JOIN tbl_users as b ON b.id = a.created_by WHERE a.id = '$userID' AND b.role_id = 5")->num_rows();
                if($isSponserFranchise)
                {
                    $getSponserId = $this->db->get_where('users',array('role_id'=>2,'id'=>$userID))->row_array();
                    $sponserId = isset($getSponserId['created_by']) ? $getSponserId['created_by'] : 0 ;
                    if($sponserId)
                    {
                        $this->User->distribute_recharge_commision($recharge_id,$userID,1);

                        $this->User->distribute_franchise_direct_recharge_commision($recharge_id,$userID); 
                    }
                }
                else
                {
                    $this->User->distribute_recharge_commision($recharge_id,$userID);

                    $this->User->distribute_franchise_recharge_commision($recharge_id,$userID);
                }

												
												

												$message = 'Congratulations!! your recharge is succesfull.';

												$this->User->sendNotification($userID,'Recharge',$message);
												
												$response = array(
													'status' => 1,
													'message' => 'Congratulations!! your recharge is succesfull.'
												);

											}
										}
										else
										{
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('status'=>3));
											
											$response = array(
												'status' => 0,
												'message' => 'Sorry!! your recharge is failed.'
											);
										}
										
									}
								}  
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry!! something went wrong.'
									);
								}
							}
						}
						elseif($post['type'] == 7)
						{
							$this->load->library('form_validation');
							$this->form_validation->set_rules('operator', 'Operator', 'required');
							$this->form_validation->set_rules('number', 'Account Number', 'required');
							$this->form_validation->set_rules('customer_name', 'Customer Number', 'required');
					        //$this->form_validation->set_rules('reference_id', 'Reference ID', 'required');
							$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
							if ($this->form_validation->run() == FALSE)
							{
								$response = array(
									'status' => 0,
									'message' => 'Please enter required field.'
								);
							}
							else
							{
								// check user valid or not
								$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
								if($chk_user)
								{
									$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
									$mobile = $chk_wallet_balance['mobile'];
									
									$reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
									

										if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									
									
									elseif($reserved_wallet_balance < $post['amount']){

										$response = array(
											'status' => 0,
											'message' => 'Sorry!! your wallet balance not sufficient for this recharge.'
										);
									}
									else
									{
										/*// send recharge OTP
										$status = $this->Api_model->sendRechargeOTP($post,$userID);
										$response = array(
											'status' => 1,
											'message' => lang('RECHARGE_OTP_SEND_SUCCESS')
										);*/


										// generate recharge unique id
										$recharge_unique_id = rand(1111,9999).time();

										$data = array(
											'member_id'          => $userID,
											'recharge_type'      => $type,
											'recharge_display_id'=> $recharge_unique_id,
											'mobile'             => $post['number'],
											'account_number'     => isset($post['account_number']) ? $post['account_number'] : '',
											'operator_code'      => $post['operator'],
											'amount'             => $post['amount'],
											'status'         	 => 1,
											'reference_id'             => $post['reference_id'],
											'customer_name'             => $post['customer_name'],
											'created'            => date('Y-m-d H:i:s')           
										);

										$this->db->insert('recharge_history',$data);
										$recharge_id = $this->db->insert_id();


										$account_number = $post['account_number'];
										$operator_code = $post['operator'];
										$amount = $post['amount'];
										$reference_id = $post['reference_id'];
										// call recharge API
										$api_response = $this->User->electricity_rechage_api($account_number,$operator_code,$amount,$reference_id,$recharge_unique_id,$userID,$mobile);

										if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
										{
											$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

											$after_balance = $before_balance['wallet_balance'] - $post['amount'];    

											$wallet_data = array(
												'member_id'           => $userID,    
												'before_balance'      => $before_balance['wallet_balance'],
												'amount'              => $post['amount'],  
												'after_balance'       => $after_balance,      
												'status'              => 1,
												'type'                => 2,      
												'created'             => date('Y-m-d H:i:s'),      
												'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
											);

											$this->db->insert('member_wallet',$wallet_data);

											$user_wallet = array(
												'wallet_balance'=>$after_balance,        
											);    
											$this->db->where('id',$userID);
											$this->db->update('users',$user_wallet);
											if($api_response['status'] == 1){
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
												
												$response = array(
													'status' => 1,
													'message' => 'Your recharge is in pending.'
												);
												
											}
											else
											{
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
												
												// check sponser is franchise or not
                $isSponserFranchise = $this->db->query("SELECT * FROM tbl_users as a INNER JOIN tbl_users as b ON b.id = a.created_by WHERE a.id = '$userID' AND b.role_id = 5")->num_rows();
                if($isSponserFranchise)
                {
                    $getSponserId = $this->db->get_where('users',array('role_id'=>2,'id'=>$userID))->row_array();
                    $sponserId = isset($getSponserId['created_by']) ? $getSponserId['created_by'] : 0 ;
                    if($sponserId)
                    {
                        $this->User->distribute_recharge_commision($recharge_id,$userID,1);

                        $this->User->distribute_franchise_direct_recharge_commision($recharge_id,$userID); 
                    }
                }
                else
                {
                    $this->User->distribute_recharge_commision($recharge_id,$userID);

                    $this->User->distribute_franchise_recharge_commision($recharge_id,$userID);
                }

												$message = 'Congratulations!! your recharge is succesfull.';

												$this->User->sendNotification($userID,'Recharge',$message);
												
												$response = array(
													'status' => 1,
													'message' => 'Congratulations!! your recharge is succesfull.'
												);

											}
										}
										else
										{
												// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('status'=>3));

											$response = array(
												'status' => 0,
												'message' => 'Sorry!! your recharge is failed.'
											);
										}
										
									}
								}  
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry!! something went wrong.'
									);
								}
							}
						}
					}
				}
				
			}
			log_message('debug', 'Recharge API Response - '.json_encode($response));	
			echo json_encode($response);
			
		}

		public function rechargeOTPAuth(){
			
			$response = array();
			$post = $this->input->post();
			log_message('debug', 'Recharge OTP API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('otp', 'OTP', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => lang('LOGIN_VALID_FAILED')
				);
			}
			else
			{	

				$user_id = $post['userID'];

				$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

				$password = isset($userData['password']) ? $userData['password'] : '';

				$header_data = apache_request_headers();

				$token = isset($header_data['Token']) ? $header_data['Token'] : '';

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

				if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.'
					);
				}
				else{


					$userID = $post['userID'];
					$encrypt_otp_code = do_hash($post['otp']);
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						$chk_email_mobile = $this->db->get_where('recharge_otp',array('userID'=>$userID,'encrypt_otp_code'=>$encrypt_otp_code,'status'=>0))->num_rows();
						if($chk_email_mobile)           
						{
							$get_otp_data = $this->db->get_where('recharge_otp',array('userID'=>$userID,'encrypt_otp_code'=>$encrypt_otp_code,'status'=>0))->row_array();

							$post_data = (array) json_decode($get_otp_data['json_post_data']);

							$this->db->where('id',$get_otp_data['id']);
							$this->db->update('recharge_otp',array('status'=>1));

							$type = $post_data['type'];
							if($post_data['type'] == 1)
							{
								
								if($post_data['rechargeType'] == 2 && (!isset($post_data['acnumber']) || $post_data['acnumber'] == ''))
								{
									$response = array(
										'status' => 0,
										'message' => lang('LOGIN_VALID_FAILED')
									);
								}
								else
								{
									
									
									$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
									
										$reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
										

										if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									
									
									elseif($reserved_wallet_balance < $post_data['amount']){

										$response = array(
											'status' => 0,
											'message' => lang('WALLET_ERROR')
										);
									}
									else
									{
										// generate recharge unique id
										$recharge_unique_id = rand(1111,9999).time();

										$data = array(
											'member_id'          => $userID,
											'recharge_type'      => $type,
											'recharge_subtype'   => $post_data['rechargeType'],
											'recharge_display_id'=> $recharge_unique_id,
											'mobile'             => $post_data['number'],
											'account_number'     => isset($post_data['acnumber']) ? $post_data['acnumber'] : '',
											'operator_code'      => $post_data['operator'],
											'circle_code'        => $post_data['circle'],
											'amount'             => $post_data['amount'],
											'status'         	 => 1,
											'is_from_app'		 =>	1,
											'created'            => date('Y-m-d H:i:s')                  
										);

										$this->db->insert('recharge_history',$data);
										$recharge_id = $this->db->insert_id();

										if($post_data['rechargeType'] == 1){
											$mobile = $post_data['number'];
											$operator_code = $post_data['operator'];
											$circle_code = $post_data['circle'];
											$amount = $post_data['amount'];
											// call recharge API
											$api_response = $this->User->prepaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$recharge_unique_id,$userID);
										}
										elseif($post_data['rechargeType'] == 2){
											$mobile = $post_data['number'];
											$operator_code = $post_data['operator'];
											$circle_code = $post_data['circle'];
											$amount = $post_data['amount'];
											$account = $post_data['acnumber'];
											// call recharge API
											$api_response = $this->User->postpaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$account,$recharge_unique_id,$userID);
										}

										if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
										{
											$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

											$after_balance = $before_balance['wallet_balance'] - $post_data['amount'];    

											$wallet_data = array(
												'member_id'           => $userID,    
												'before_balance'      => $before_balance['wallet_balance'],
												'amount'              => $post_data['amount'],  
												'after_balance'       => $after_balance,      
												'status'              => 1,
												'type'                => 2,      
												'created'             => date('Y-m-d H:i:s'),      
												'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
											);

											$this->db->insert('member_wallet',$wallet_data);

											$user_wallet = array(
												'wallet_balance'=>$after_balance,        
											);    
											$this->db->where('id',$userID);
											$this->db->update('users',$user_wallet);
											if($api_response['status'] == 1){
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
												
												$response = array(
													'status' => 1,
													'message' => lang('RECHARGE_PENDING')
												);
												
											}
											else
											{
												// update recharge status
												$this->db->where('id',$recharge_id);
												$this->db->where('recharge_display_id',$recharge_unique_id);
												$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
												
												// add 100% CM Points
												$this->User->recharge_cm_points_add($userID,$post_data['amount'],'Mobile',$recharge_unique_id);
												
												$response = array(
													'status' => 1,
													'message' => lang('RECHARGE_SUCCESS')
												);

											}
										}
										else
										{
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('status'=>3));
											
											$response = array(
												'status' => 0,
												'message' => lang('RECHARGE_FAILED')
											);
										}
									}
									
								}
								
							}
							elseif($post_data['type'] == 2)
							{
								
								
								$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
								
									$reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
									
                                
                                
                                	if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									
									
								elseif($reserved_wallet_balance < $post_data['amount']){

									$response = array(
										'status' => 0,
										'message' => lang('WALLET_ERROR')
									);
								}
								else
								{
									// generate recharge unique id
									$recharge_unique_id = rand(1111,9999).time();

									$data = array(
										'member_id'          => $userID,
										'recharge_type'      => $type,
										'recharge_display_id'=> $recharge_unique_id,
										'mobile'             => $post_data['number'],
										'account_number'     => isset($post_data['acnumber']) ? $post_data['acnumber'] : '',
										'operator_code'      => $post_data['operator'],
										'circle_code'        => $post_data['circle'],
										'amount'             => $post_data['amount'],
										'status'         	 => 1,
										'created'            => date('Y-m-d H:i:s')                  
									);

									$this->db->insert('recharge_history',$data);
									$recharge_id = $this->db->insert_id();


									$mobile = $post_data['number'];
									$operator_code = $post_data['operator'];
									$circle_code = $post_data['circle'];
									$amount = $post_data['amount'];
									// call recharge API
									$api_response = $this->User->prepaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$recharge_unique_id,$userID);

									if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
									{
										$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

										$after_balance = $before_balance['wallet_balance'] - $post_data['amount'];    

										$wallet_data = array(
											'member_id'           => $userID,    
											'before_balance'      => $before_balance['wallet_balance'],
											'amount'              => $post_data['amount'],  
											'after_balance'       => $after_balance,      
											'status'              => 1,
											'type'                => 2,      
											'created'             => date('Y-m-d H:i:s'),      
											'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
										);

										$this->db->insert('member_wallet',$wallet_data);

										$user_wallet = array(
											'wallet_balance'=>$after_balance,        
										);    
										$this->db->where('id',$userID);
										$this->db->update('users',$user_wallet);
										if($api_response['status'] == 1){
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
											
											$response = array(
												'status' => 1,
												'message' => lang('RECHARGE_PENDING')
											);
											
										}
										else
										{
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
											
											// add 100% CM Points
											$this->User->recharge_cm_points_add($userID,$post_data['amount'],'Mobile');
											
											$response = array(
												'status' => 1,
												'message' => lang('RECHARGE_SUCCESS')
											);

										}
									}
									else
									{
										// update recharge status
										$this->db->where('id',$recharge_id);
										$this->db->where('recharge_display_id',$recharge_unique_id);
										$this->db->update('recharge_history',array('status'=>3));
										
										$response = array(
											'status' => 0,
											'message' => lang('RECHARGE_FAILED')
										);
									}
								}
								
							}
							elseif($post_data['type'] == 3)
							{
								

								$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
								
									$reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
									
									

							    	if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									
							
								elseif($reserved_wallet_balance < $post_data['amount']){

									$response = array(
										'status' => 0,
										'message' => lang('WALLET_ERROR')
									);
								}
								else
								{
									// generate recharge unique id
									$recharge_unique_id = rand(1111,9999).time();

									$data = array(
										'member_id'          => $userID,
										'recharge_type'      => $type,
										'recharge_subtype'   => $post_data['rechargeType'],
										'recharge_display_id'=> $recharge_unique_id,
										'mobile'             => $post_data['number'],
										'account_number'     => isset($post_data['acnumber']) ? $post_data['acnumber'] : '',
										'operator_code'      => $post_data['operator'],
										'circle_code'        => $post_data['circle'],
										'amount'             => $post_data['amount'],
										'status'         	 => 1,
										'created'            => date('Y-m-d H:i:s')                  
									);

									$this->db->insert('recharge_history',$data);
									$recharge_id = $this->db->insert_id();


									$mobile = $post_data['number'];
									$operator_code = $post_data['operator'];
									$circle_code = $post_data['circle'];
									$amount = $post_data['amount'];
									// call recharge API
									$api_response = $this->User->prepaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$recharge_unique_id,$userID);

									if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
									{
										$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

										$after_balance = $before_balance['wallet_balance'] - $post_data['amount'];    

										$wallet_data = array(
											'member_id'           => $userID,    
											'before_balance'      => $before_balance['wallet_balance'],
											'amount'              => $post_data['amount'],  
											'after_balance'       => $after_balance,      
											'status'              => 1,
											'type'                => 2,      
											'created'             => date('Y-m-d H:i:s'),      
											'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
										);

										$this->db->insert('member_wallet',$wallet_data);

										$user_wallet = array(
											'wallet_balance'=>$after_balance,        
										);    
										$this->db->where('id',$userID);
										$this->db->update('users',$user_wallet);
										if($api_response['status'] == 1){
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
											
											$response = array(
												'status' => 1,
												'message' => lang('RECHARGE_PENDING')
											);
											
										}
										else
										{
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
											
											// add 100% CM Points
											$this->User->recharge_cm_points_add($userID,$post_data['amount'],'Mobile');
											
											$response = array(
												'status' => 1,
												'message' => lang('RECHARGE_SUCCESS')
											);

										}
									}
									else
									{
										// update recharge status
										$this->db->where('id',$recharge_id);
										$this->db->where('recharge_display_id',$recharge_unique_id);
										$this->db->update('recharge_history',array('status'=>3));
										
										$response = array(
											'status' => 0,
											'message' => lang('RECHARGE_FAILED')
										);
									}
								}

							}
							elseif($post_data['type'] == 5)
							{
								

								$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
								
								$reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
								

									if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									
									
								
								elseif($reserved_wallet_balance < $post_data['amount']){

									$response = array(
										'status' => 0,
										'message' => lang('WALLET_ERROR')
									);
								}
								else
								{
									// generate recharge unique id
									$recharge_unique_id = rand(1111,9999).time();

									$data = array(
										'member_id'          => $userID,
										'recharge_type'      => $type,
										'recharge_display_id'=> $recharge_unique_id,
										'mobile'             => $post_data['number'],
										'account_number'     => isset($post_data['acnumber']) ? $post_data['acnumber'] : '',
										'operator_code'      => $post_data['operator'],
										'circle_code'        => $post_data['circle'],
										'amount'             => $post_data['amount'],
										'status'         	 => 1,
										'created'            => date('Y-m-d H:i:s')                  
									);

									$this->db->insert('recharge_history',$data);
									$recharge_id = $this->db->insert_id();


									$mobile = $post_data['number'];
									$operator_code = $post_data['operator'];
									$circle_code = $post_data['circle'];
									$amount = $post_data['amount'];
									// call recharge API
									$api_response = $this->User->prepaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$recharge_unique_id,$userID);

									if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
									{
										$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

										$after_balance = $before_balance['wallet_balance'] - $post_data['amount'];    

										$wallet_data = array(
											'member_id'           => $userID,    
											'before_balance'      => $before_balance['wallet_balance'],
											'amount'              => $post_data['amount'],  
											'after_balance'       => $after_balance,      
											'status'              => 1,
											'type'                => 2,      
											'created'             => date('Y-m-d H:i:s'),      
											'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
										);

										$this->db->insert('member_wallet',$wallet_data);

										$user_wallet = array(
											'wallet_balance'=>$after_balance,        
										);    
										$this->db->where('id',$userID);
										$this->db->update('users',$user_wallet);
										if($api_response['status'] == 1){
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
											
											$response = array(
												'status' => 1,
												'message' => lang('RECHARGE_PENDING')
											);
											
										}
										else
										{
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
											
											// add 100% CM Points
											$this->User->recharge_cm_points_add($userID,$post_data['amount'],'Mobile');
											
											$response = array(
												'status' => 1,
												'message' => lang('RECHARGE_SUCCESS')
											);

										}
									}
									else
									{
										// update recharge status
										$this->db->where('id',$recharge_id);
										$this->db->where('recharge_display_id',$recharge_unique_id);
										$this->db->update('recharge_history',array('status'=>3));
										
										$response = array(
											'status' => 0,
											'message' => lang('RECHARGE_FAILED')
										);
									}
								}

								
							}
							elseif($post_data['type'] == 7)
							{
								
								$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();
								$mobile = $chk_wallet_balance['mobile'];
                                    
                                    $reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
                                    
								
									if($chk_wallet_balance['is_main_wallet_block'] ==1){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
										);
									}
									
									
								
								elseif($reserved_wallet_balance < $post_data['amount']){

									$response = array(
										'status' => 0,
										'message' => lang('WALLET_ERROR')
									);
								}
								else
								{
									// generate recharge unique id
									$recharge_unique_id = rand(1111,9999).time();

									$data = array(
										'member_id'          => $userID,
										'recharge_type'      => $type,
										'recharge_display_id'=> $recharge_unique_id,
										'mobile'             => $mobile,
										'account_number'     => isset($post_data['account_number']) ? $post_data['account_number'] : '',
										'operator_code'      => $post_data['operator'],
										'amount'             => $post_data['amount'],
										'status'         	 => 1,
										'reference_id'             => $post_data['reference_id'],
										'customer_name'             => $post_data['customer_name'],
										'created'            => date('Y-m-d H:i:s')           
									);

									$this->db->insert('recharge_history',$data);
									$recharge_id = $this->db->insert_id();


									$account_number = $post_data['account_number'];
									$operator_code = $post_data['operator'];
									$amount = $post_data['amount'];
									$reference_id = $post_data['reference_id'];
									// call recharge API
									$api_response = $this->User->electricity_rechage_api($account_number,$operator_code,$amount,$reference_id,$recharge_unique_id,$userID,$mobile);

									if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
									{
										$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

										$after_balance = $before_balance['wallet_balance'] - $post_data['amount'];    

										$wallet_data = array(
											'member_id'           => $userID,    
											'before_balance'      => $before_balance['wallet_balance'],
											'amount'              => $post_data['amount'],  
											'after_balance'       => $after_balance,      
											'status'              => 1,
											'type'                => 2,      
											'created'             => date('Y-m-d H:i:s'),      
											'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
										);

										$this->db->insert('member_wallet',$wallet_data);

										$user_wallet = array(
											'wallet_balance'=>$after_balance,        
										);    
										$this->db->where('id',$userID);
										$this->db->update('users',$user_wallet);
										if($api_response['status'] == 1){
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
											
											$response = array(
												'status' => 1,
												'message' => lang('RECHARGE_PENDING')
											);
											
										}
										else
										{
											// update recharge status
											$this->db->where('id',$recharge_id);
											$this->db->where('recharge_display_id',$recharge_unique_id);
											$this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
											
											// add 100% CM Points
											$this->User->recharge_cm_points_add($userID,$post_data['amount'],'Mobile');
											
											$response = array(
												'status' => 1,
												'message' => lang('RECHARGE_SUCCESS')
											);

										}
									}
									else
									{
										// update recharge status
										$this->db->where('id',$recharge_id);
										$this->db->where('recharge_display_id',$recharge_unique_id);
										$this->db->update('recharge_history',array('status'=>3));
										
										$response = array(
											'status' => 0,
											'message' => lang('RECHARGE_FAILED')
										);
									}
								}

							}
							
						}
						else
						{
							$response = array(
								'status' => 0,
								'message' => lang('OTP_ERROR')
							);
							
						}
					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => lang('USER_ID_ERROR')
						);
					}
				}
				
			}
			log_message('debug', 'Recharge OTP API Response - '.json_encode($response));	
			echo json_encode($response);
			
		}

		public function userWalletDetail()
		{
			$post = $this->input->post();
			log_message('debug', 'Wallet Detail API Get Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			$response = array();

			$user_id = $post['userID'];

			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

			$password = isset($userData['password']) ? $userData['password'] : '';

			$header_data = apache_request_headers();

			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
			
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

				$response = array(
					'status' => 0,
					'message' => 'Session out.Please Login Again.'
				);
			}
			else{

				// check user valid or not
				$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
				if($chk_user)
				{
					$wallet_data = $this->db->select('users.wallet_balance,users.cm_points,package.package_name')->join('package','package.id = users.current_package_id','left')->get_where('users',array('users.id'=>$userID))->row_array();
					$response = array(
						'status' => 1,
						'message' => 'Success',
						'data' => array(
							'premium_wallet_balance' => $wallet_data['wallet_balance'],
							'cm_points' => $wallet_data['cm_points'],
							'package' => $wallet_data['package_name'],
						)
					);	
				}
				else
				{
					$response = array(
						'status' => 0,
						'message' => lang('USER_ID_ERROR')
					);
				}
			}
			log_message('debug', 'Wallet Detail API Response - '.json_encode($response));	
			echo json_encode($response);
		}


		public function getPremiumWalletHistory()
		{
			$post = $this->input->post();
			log_message('debug', 'Premium Wallet History API Get Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;

			$user_id = $post['userID'];

			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

			$password = isset($userData['password']) ? $userData['password'] : '';

			$header_data = apache_request_headers();

			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
			
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

				$response = array(
					'status' => 0,
					'message' => 'Session out.Please Login Again.'
				);
			}
			else{

				$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
				$limit = $page_no * 20; 

				$response = array();
				// check user valid or not
				$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
				if($chk_user)
				{	
					$historyList = $this->db->limit($limit_end,$limit_start)->order_by('created','desc')->get_where('member_wallet',array('member_id'=>$userID,'wallet_type'=>1))->result_array();


					$get_wallet_balance = $this->db->select('wallet_balance')->get_where('users',array('id'=>$userID))->row_array();
					$wallet_balance = isset($get_wallet_balance['wallet_balance']) ? $get_wallet_balance['wallet_balance'] : 0;

					
					$data = array();
					if($historyList)
					{
						foreach ($historyList as $key => $list) {
							
							$data[$key]['before_balance'] = $list['before_balance'];
							$data[$key]['amount'] = $list['amount'];
							$data[$key]['type'] = $list['type'] == 1 ? 'CR' : 'DR';
							$data[$key]['after_balance'] = $list['after_balance'];
							$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
							$data[$key]['description'] = $list['description'];
						}
					}

					$response = array(
						'status' => 1,
						'message' => 'Success',
						'pages' => $pages,
						'data' => $data,
						'wallet_balance'=>$wallet_balance
					);	
				}
				else
				{
					$response = array(
						'status' => 0,
						'message' => lang('USER_ID_ERROR')
					);
				}
			}
			log_message('debug', 'Premium Wallet History API Response - '.json_encode($response));	
			echo json_encode($response);
		}




		public function getPointHistory()
		{
			$post = $this->input->post();
			log_message('debug', 'CM Point History API Get Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;

			$user_id = $post['userID'];

			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

			$password = isset($userData['password']) ? $userData['password'] : '';

			$header_data = apache_request_headers();

			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
			
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

				$response = array(
					'status' => 0,
					'message' => 'Session out.Please Login Again.'
				);
			}
			else{

				$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
				$limit = $page_no * 20;
				$response = array();
				// check user valid or not
				$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
				if($chk_user)
				{
					$historyList = $this->db->order_by('created','desc')->get_where('member_wallet',array('member_id'=>$userID,'wallet_type'=>2))->result_array();

					$data = array();
					if($historyList)
					{
						foreach ($historyList as $key => $list) {
							
							$data[$key]['before_balance'] = $list['before_balance'];
							$data[$key]['points'] = $list['amount'];
							$data[$key]['type'] = $list['type'];
							$data[$key]['after_balance'] = $list['after_balance'];
							$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
							$data[$key]['description'] = $list['description'];
						}
					}

					$response = array(
						'status' => 1,
						'message' => 'Success',
						'data' => $data
					);	
				}
				else
				{
					$response = array(
						'status' => 0,
						'message' => lang('USER_ID_ERROR')
					);
				}
			}
			log_message('debug', 'CM Point History API Response - '.json_encode($response));	
			echo json_encode($response);
		}


		public function getRechargeHistory()
		{
			$post = $this->input->post();
			log_message('debug', 'Recharge History API Get Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;

			$user_id = $post['userID'];

			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

			$password = isset($userData['password']) ? $userData['password'] : '';

			$header_data = apache_request_headers();

			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
			
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
			
			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

				$response = array(
					'status' => 0,
					'message' => 'Session out.Please Login Again.'
				);
			}
			else{

				$response = array();
				$fromDate = $post['fromDate'];
				$toDate =   $post['toDate'];
				$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
				$limit = $page_no * 50;

			    // check user valid or not
				$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
				if($chk_user)
				{
					$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.operator_code = a.operator_code where a.id > 0 AND a.recharge_type != 7 AND (b.created_by = '$userID' OR a.member_id = '$userID')) as x WHERE x.id > 0";
					if($fromDate && $toDate)
					{
						$sql.=" AND DATE(x.created) >= '".$fromDate."' AND DATE(x.created) <= '".$toDate."'";

						$sql.=" ORDER BY x.created DESC";
                    
                            
						$count = $this->db->query($sql)->num_rows();

						$limit_start = $limit - 50; 

						$limit_end = 50;

						$sql.=" LIMIT ".$limit_start." ,".$limit_end."";

					}
					else{

						$sql.=" ORDER BY created DESC";

						$count = $this->db->query($sql)->num_rows();

						$limit_start = $limit - 50; 

						$limit_end = 50;

						$sql.=" LIMIT ".$limit_start." ,".$limit_end."";
					}

					$historyList = $this->db->query($sql)->result_array();

					$pages = ceil($count / 50);

					$data = array();
					if($historyList)
					{
						foreach ($historyList as $key => $list) {
							
							// get rechrage type
							$get_recharge_type = $this->db->get_where('recharge_type',array('id'=>$list['recharge_type']))->row_array();
							$type = isset($get_recharge_type['type']) ? $get_recharge_type['type'] : '';

							$data[$key]['recharge_id'] = $list['id'];
							$data[$key]['order_id'] = $list['recharge_display_id'];
							$data[$key]['amount'] = $list['amount'];
							$data[$key]['before_balance'] = ($list['before_balance']) ? $list['before_balance'] : 0;
							$data[$key]['after_balance'] = ($list['after_balance']) ? $list['after_balance'] : 0;
							$data[$key]['operator'] = $list['operator_name'];
							$data[$key]['type'] = $type;
							$data[$key]['mobile'] = $list['mobile'];
							$data[$key]['member_name'] = $list['name'];
							$data[$key]['member_code'] = $list['user_code'];
							$data[$key]['txid'] = $list['operator_ref'];
							$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
							$data[$key]['status'] = $list['status'];
							
						}
					}

					if($data)
					{
						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data' => $data,
							'pages' => $pages,
						);	
					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! No Record Found.',
						);	
					}
				}
				else
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry ! Member not valid.'
					);
				}
			}
			log_message('debug', 'Recharge History API Response - '.json_encode($response));	
			echo json_encode($response);
		}

		public function getReferralLink(){
			
			$response = array();
			$post = $this->input->post();
			log_message('debug', 'Get Refferal API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => lang('LOGIN_VALID_FAILED')
				);
			}
			else
			{
				$userID = $post['userID'];

				$user_id = $post['userID'];

				$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

				$password = isset($userData['password']) ? $userData['password'] : '';

				$header_data = apache_request_headers();

				$token = isset($header_data['Token']) ? $header_data['Token'] : '';

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

				if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.'
					);
				}
				else{

					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						$userData = $this->db->get_where('users',array('id'=>$userID))->row_array();
						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data' => base_url('register?referral_id='.$userData['user_code'])
						);	
					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => lang('USER_ID_ERROR')
						);
					}
					
				}
			}
			log_message('debug', 'Get Refferal API Response - '.json_encode($response));	
			echo json_encode($response);
			
		}

		public function getPackageList(){
			
			$response = array();
			$post = $this->input->post();
			log_message('debug', 'Get Package API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => lang('LOGIN_VALID_FAILED')
				);
			}
			else
			{
				$userID = $post['userID'];

				$user_id = $post['userID'];

				$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

				$password = isset($userData['password']) ? $userData['password'] : '';

				$header_data = apache_request_headers();

				$token = isset($header_data['Token']) ? $header_data['Token'] : '';

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

				if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.'
					);
				}
				else{

					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						$packageList = $this->db->order_by('order_no','asc')->get_where('package',array('id >'=>1,'status'=>1))->result_array();
						$member_current_package = $this->User->get_member_current_package($userID);
						if($member_current_package == 2)
						{
							unset($packageList[0]);
							unset($packageList[2]);
						}
						elseif($member_current_package == 3)
						{
							unset($packageList[0]);
							unset($packageList[1]);
							unset($packageList[2]);
						}
						elseif($member_current_package == 4)
						{
							unset($packageList[1]);
							unset($packageList[2]);
						}
						elseif($member_current_package == 1)
						{
							unset($packageList[1]);
						}


						$packageData = array();
						if($packageList)
						{
							foreach($packageList as $key=>$list)
							{
								$packageData[$key]['package_id'] = $list['id'];
								$packageData[$key]['package_name'] = $list['package_name'];
								$packageData[$key]['package_amount'] = $list['package_amount'];
								$packageData[$key]['cm_points'] = $list['cm_points'];
								$packageData[$key]['refer_cm_points'] = $list['refer_cm_points'];
								$packageData[$key]['cashback'] = $list['cashback'];
							}
						}

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data' => $packageData
						);	
					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => lang('USER_ID_ERROR')
						);
					}
					
				}
			}
			log_message('debug', 'Get Package API Response - '.json_encode($response));	
			echo json_encode($response);
			
		}

		


		public function updateUserData(){
			$response = array();
			$post = $this->input->post();

			log_message('debug', 'Update User Data API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
			$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
			$this->form_validation->set_rules('pincode', 'Pincode', 'required|xss_clean');
			$this->form_validation->set_rules('city_id', 'City ID', 'required|xss_clean');
			$this->form_validation->set_rules('state_id', 'State ID', 'required|xss_clean');
			$this->form_validation->set_rules('block_id', 'Block ID', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => lang('LOGIN_VALID_FAILED')
				);
			}
			else
			{
				$userID = $post['userID'];

				$user_id = $post['userID'];

				$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

				$password = isset($userData['password']) ? $userData['password'] : '';

				$header_data = apache_request_headers();

				$token = isset($header_data['Token']) ? $header_data['Token'] : '';

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

				if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.'
					);
				}
				else{

					$siteUrl = base_url();
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						// update user data
						$updateData = array(
							'name' => $post['name']
						);
						if(isset($post['photo']) && !empty($post['photo']))
						{
							$encodedData = $post['photo'];
							if(strpos($post['photo'], ' ')){
								$encodedData = str_replace(' ','+', $post['photo']);
							}
							$profile = base64_decode($encodedData);
							$file_name = time().rand(1111,9999).'.jpg';
						// 	$profile_img_name = base_url('media/user_profile/'.$file_name);
							$profile_img_name = FILE_UPLOAD_SERVER_PATH.$file_name;
							$path = 'media/member/';
							if (!is_dir($path)) {
								mkdir($path, 0777, true);
							}
							$targetDir = $path.$file_name;
							if(file_put_contents($targetDir, $profile)){
								$updateData['photo'] = $targetDir;
							}
						}
						$this->db->where('id',$userID);
						$this->db->update('users',$updateData);

						$chk_address = $this->db->get_where('user_residential_address',array('user_id'=>$userID))->num_rows();

						if($chk_address){

							$addressData = array(

								'address'  => $post['address'],
								'pincode'  => $post['pincode'],
								'city_id'  => $post['city_id'],
								'state_id' => $post['state_id'],
								'block_id' => $post['block_id']  

							);
							$this->db->where('user_id',$userID);
							$this->db->update('user_residential_address',$addressData);
						}
						else{

							$addressData = array(

								'user_id'  => $userID,  
								'address'  => $post['address'],
								'pincode'  => $post['pincode'],
								'city_id'  => $post['city_id'],
								'state_id' => $post['state_id'],  
								'block_id' => $post['block_id']
							);
							$this->db->insert('user_residential_address',$addressData);
						}

						$userData = $this->db->get_where('users',array('id'=>$userID))->row_array();
						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data' => array(
								'name' => $userData['name'],
								'email' => $userData['email'],
								'mobile' => $userData['mobile'],
								'photo' => !empty($userData['photo']) ? base_url($userData['photo']) : ''
							)
						);
						
					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => lang('USER_ID_ERROR')
						);
					}
					
				}
			}
			log_message('debug', 'Update User Data API Response - '.json_encode($response));	
			echo json_encode($response);
			
		}

		public function getCountryList()
		{
			$response = array();
			$operator = $this->db->get('countries')->result_array();
			
			$data = array();
			if($operator)
			{
				foreach ($operator as $key => $value) {
					$data[$key]['countryID'] = $value['id'];
					$data[$key]['countryCode'] = $value['sortname'];
					$data[$key]['name'] = $value['name'];
					
				}
			}

			$response = array(
				'status' => 1,
				'message' => 'Success',
				'data' => $data
			);
			echo json_encode($response);
		}


		public function getCityList()
		{
			$response = array();
			$operator = $this->db->get('city')->result_array();
			
			$data = array();
			if($operator)
			{
				foreach ($operator as $key => $value) {
					$data[$key]['city_id'] = $value['city_id'];
					$data[$key]['city_name'] = $value['city_name'];
					$data[$key]['city_code'] = $value['city_code'];
					
				}
			}

			$response = array(
				'status' => 1,
				'message' => 'Success',
				'data' => $data
			);
			echo json_encode($response);
		}

		public function getStateList(){
			
			$response = array();
			$post = $this->input->post();
			log_message('debug', 'Get State List API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('countryCode', 'Country Code', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => lang('LOGIN_VALID_FAILED')
				);
			}
			else
			{
				$operator = $this->db->get_where('states',array('country_code_char2'=>$post['countryCode']))->result_array();

				$data = array();
				if($operator)
				{
					foreach ($operator as $key => $value) {
						$data[$key]['stateID'] = $value['id'];
						$data[$key]['name'] = $value['name'];
						
					}
				}
				$response = array(
					'status' => 1,
					'message' => 'Success',
					'data' => $data
				);
				
			}
			log_message('debug', 'Get State List API Response - '.json_encode($response));	
			echo json_encode($response);
			
		}



		public function kycAuth(){
			
			$response = array();
			$post = $this->input->post();
			log_message('debug', 'KYC Auth API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('accountName', 'Account Name', 'required|xss_clean');
			$this->form_validation->set_rules('accountNumber', 'Account Number', 'required|xss_clean');
			$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
			$this->form_validation->set_rules('bankName', 'Bank Name', 'required|xss_clean');
			$this->form_validation->set_rules('dob', 'Dob', 'required|xss_clean');
			$this->form_validation->set_rules('aadhar_no', 'aadhar_no', 'required|xss_clean');
			$this->form_validation->set_rules('mobile_no', 'mobile_no', 'required|xss_clean');
			$this->form_validation->set_rules('adharFront', 'Adhar Back', 'required|xss_clean');
			$this->form_validation->set_rules('adharBack', 'Adhar Back', 'required|xss_clean');
			$this->form_validation->set_rules('pancard', 'Pancard', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => lang('LOGIN_VALID_FAILED')
				);
			}
			else
			{
				$userID = $post['userID'];

				$user_id = $post['userID'];

				$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

				$password = isset($userData['password']) ? $userData['password'] : '';

				$header_data = apache_request_headers();

				$token = isset($header_data['Token']) ? $header_data['Token'] : '';

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

				if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.'
					);
				}
				else{

					$siteUrl = base_url();
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{	

						$chk_kyc = $this->db->get_where('member_kyc_detail',array('member_id'=>$userID))->row_array();

						if(!$chk_kyc || $chk_kyc['status'] == 4){


							$adhar_front_img = '';
							if(isset($post['adharFront']) && $post['adharFront'])
							{
								$profile = isset($post['adharFront']) ? $post['adharFront'] : '';

								if(strpos($post['adharFront'], ' ')){
									$profile = str_replace(' ','+', $post['adharFront']);
								}

								$file_name = time().rand(1111,9999).'.jpg';
								//$profile_img_name = base_url('media/user_profile/'.$file_name);
								$profile_img_name = KYC_FILE_UPLOAD_SERVER_PATH.$file_name;
								
								$img_data = base64_decode($profile);

								$path = 'media/kyc_document/';
								if (!is_dir($path)) {
									mkdir($path, 0777, true);
								}
								$targetDir = $path.$file_name;
								file_put_contents($targetDir, $img_data);

								$adhar_front_img = 'media/kyc_document/'.$file_name;

							}
							$adhar_back_img = '';
							if(isset($post['adharBack']) && $post['adharBack'])
							{
								$profile = isset($post['adharBack']) ? $post['adharBack'] : '';

								if(strpos($post['adharBack'], ' ')){
									$profile = str_replace(' ','+', $post['adharBack']);
								}

								$file_name = time().rand(1111,9999).'.jpg';
								//$profile_img_name = base_url('media/user_profile/'.$file_name);
								$profile_img_name = KYC_FILE_UPLOAD_SERVER_PATH.$file_name;
								
								$img_data = base64_decode($profile);
								
								$path = 'media/kyc_document/';
								if (!is_dir($path)) {
									mkdir($path, 0777, true);
								}
								$targetDir = $path.$file_name;
								file_put_contents($targetDir, $img_data);

								$adhar_back_img = 'media/kyc_document/'.$file_name;

							}
							$pancard_img = '';
							if(isset($post['pancard']) && $post['pancard'])
							{
								$profile = isset($post['pancard']) ? $post['pancard'] : '';

								if(strpos($post['pancard'], ' ')){
									$profile = str_replace(' ','+', $post['pancard']);
								}

								$file_name = time().rand(1111,9999).'.jpg';
								//$profile_img_name = base_url('media/user_profile/'.$file_name);
								$profile_img_name = KYC_FILE_UPLOAD_SERVER_PATH.$file_name;
								
								$img_data = base64_decode($profile);


								$path = 'media/kyc_document/';
								if (!is_dir($path)) {
									mkdir($path, 0777, true);
								}
								$targetDir = $path.$file_name;
								file_put_contents($targetDir, $img_data);

								$pancard_img = 'media/kyc_document/'.$file_name;
							}

							if(!$chk_kyc){

								$data = array(    
									'member_id'            =>  $userID,      
									'account_holder_name'  =>  $post['accountName'],
									'account_number'   =>  $post['accountNumber'],
									'ifsc'               =>  $post['ifsc'],
									'bank_name'               =>  $post['bankName'],
									'dob'     => $post['dob'],
									'aadhar_no'     => $post['aadhar_no'],
									'mobile_no'     => $post['mobile_no'],
									'front_document'           =>  $adhar_front_img,
									'back_document'           =>  $adhar_back_img,
									'pancard_document'           =>  $pancard_img,
									'status'           =>  2,
									'created'            =>  date('Y-m-d H:i:s')
								);

								$this->db->insert('member_kyc_detail',$data);
								
								$this->db->where('id',$userID);
								$this->db->update('users',array('kyc_status'=>2));
							}
							else{


								$data = array(    
									'account_holder_name'  =>  $post['accountName'],
									'account_number'   =>  $post['accountNumber'],
									'ifsc'               =>  $post['ifsc'],
									'bank_name'               =>  $post['bankName'],
									'dob'     => $post['dob'],
									'aadhar_no'     => $post['aadhar_no'],
									'mobile_no'     => $post['mobile_no'],
									'status'           =>  2,
									'created'            =>  date('Y-m-d H:i:s')
								);

								if(!empty($adhar_front_img)){

									$data['front_document'] = $adhar_front_img;
								}

								if(!empty($adhar_back_img)){

									$data['back_document'] = $adhar_back_img;
								}

								if(!empty($pancard_document)){

									$data['pancard_document'] = $pancard_img;
								}

								$this->db->where('member_id',$userID);
								$this->db->update('member_kyc_detail',$data);
								
								$this->db->where('id',$userID);
								$this->db->update('users',array('kyc_status'=>2));

							}

							// get account detail
							$get_user_data = $this->db->select('mobile')->get_where('users',array('id'=>$userID))->row_array();
							$mobile = isset($get_user_data['mobile']) ? $get_user_data['mobile'] : '';
							if($mobile)
							{
								$output = '';
								$sms = 'Thank you for submitting KYC Documents. You will be notified by sms or mail within 2 working days.';

								$api_url = SMS_API_URL.'receiver='.$mobile.'&sms='.urlencode($sms);

								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $api_url);
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
								$output = curl_exec($ch); 
								curl_close($ch);
							}

							$message = 'Congratulations!! kyc submitted successfully.';

							$this->User->sendNotification($userID,'KYC',$message);

							$response = array(
								'status' => 1,
								'message' => 'Congratulations!! kyc submitted successfully.'
							);
						}
						else{


							$response = array(

								'status' => 0,
								'message'=>'Sorry!! your kyc is already aproved or pending.'	
							);

						}
						
					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => lang('USER_ID_ERROR')
						);
					}
					
				}
			}
			log_message('debug', 'KYC Auth API Response - '.json_encode($response));	
			echo json_encode($response);
			
		}

		public function getUserKycDetails(){

			$response = array("status"=>0);
			$post = $this->input->post();
			log_message('debug', 'Get User Kyc Details API Post Data - '.json_encode($post));
			$this->load->library('form_validation');
		    //$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response['message'] = lang('LOGIN_VALID_FAILED');
			}else{

				$user_id = $post['userID'];

				$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

				$password = isset($userData['password']) ? $userData['password'] : '';

				$header_data = apache_request_headers();

				$token = isset($header_data['Token']) ? $header_data['Token'] : '';

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

				if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.'
					);
				}
				else{

			        // check user valid or not
					$response['message'] = lang('USER_ID_ERROR');
					$chk_user = $this->db->get_where('users',array('id'=>$post['userID']))->num_rows();
					if(!empty($chk_user))
					{
						$data = array();
						$query = $this->db->get_where('member_kyc_detail', array('member_id'=>$post['userID']));
						if($query->num_rows() > 0)
						{
							$row = $query->row_array();
							$status = 'Pending';
							if($row['status']==3){
								$status = 'Approved';
							}elseif ($row['status']==4){
								$status = 'Rejected';
							}
							$data = array(
								'ac_holder_name' => $row['account_holder_name'],
								'ac_no' => $row['account_number'],
								'ifsc' => $row['ifsc'],
								'bank_name' => $row['bank_name'],
								'dob' => $row['dob'],
								'aadhar_no' => $row['aadhar_no'],
								'mobile_no' => $row['mobile_no'],
								'aadhar_front' => base_url($row['front_document']),
								'aadhar_back' => base_url($row['back_document']),
								'pan_card' => base_url($row['pancard_document']),
								'status' => $status
							);

							$response = array(
								'status' => 1,
								'message'=>'Success',
								'data' => $data
							);
						}
						else{

							$response = array(
								'status' => 0,
								'message'=>'Kyc detail not found.',
							);
						}
					}

				}
			}
			log_message('debug', 'Get User KYC Details Response - '.json_encode($response));
			echo json_encode($response);
		}







		public function getUserDirectDownline(){
			
			$response = array();
			$post = $this->input->post();
			log_message('debug', 'Get User Direct Downline API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => lang('LOGIN_VALID_FAILED')
				);
			}
			else
			{
				$userID = $post['userID'];

				$user_id = $post['userID'];

				$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

				$password = isset($userData['password']) ? $userData['password'] : '';

				$header_data = apache_request_headers();

				$token = isset($header_data['Token']) ? $header_data['Token'] : '';

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

				if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.'
					);
				}
				else{

					$siteUrl = base_url();
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						// get member direct downline
						$directDownlineList = $this->User->get_member_direct_downline($userID);
						
						$data = array();
						if($directDownlineList)
							{	$i = 0;
								foreach($directDownlineList as $key=>$list){

									$data[$i]['memberID'] = $list['memberID'];
									$data[$i]['name'] = $list['name'];
									$data[$i]['user_code'] = $list['user_code'];
									$data[$i]['email'] = $list['email'];
									$data[$i]['mobile'] = $list['mobile'];
									$data[$i]['level'] = $list['level'];
									$data[$i]['membership'] = $this->User->get_user_membership_type($list['memberID']);
									$i++;}
								}

								$response = array(
									'status' => 1,
									'message' => 'Success',
									'data' => $data,
									'total'=> isset($i) ? $i : 0 
								);

							}
							else
							{
								$response = array(
									'status' => 0,
									'message' => lang('USER_ID_ERROR')
								);
							}

						}
					}
					log_message('debug', 'Get User Direct Downline API Response - '.json_encode($response));	
					echo json_encode($response);

				}

				public function getUserDirectActiveDownline(){

					$response = array();
					$post = $this->input->post();
					log_message('debug', 'Get User Direct Active Downline API Post Data - '.json_encode($post));	
					$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
					$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
					if ($this->form_validation->run() == FALSE)
					{
						$response = array(
							'status' => 0,
							'message' => lang('LOGIN_VALID_FAILED')
						);
					}
					else
					{	
						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							$userID = $post['userID'];
							$siteUrl = base_url();
							// check user valid or not
							$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
							if($chk_user)
							{
								// get member direct downline
								$directDownlineList = $this->User->get_member_direct_active_downline($userID);

								$data = array();
								if($directDownlineList)
									{	$i = 0;
										foreach($directDownlineList as $key=>$list){

											$data[$i]['memberID'] = $list['memberID'];
											$data[$i]['name'] = $list['name'];
											$data[$i]['user_code'] = $list['user_code'];
											$data[$i]['email'] = $list['email'];
											$data[$i]['mobile'] = $list['mobile'];
											$data[$i]['level'] = $list['level'];
											$data[$i]['membership'] = $this->User->get_user_membership_type($list['memberID']);
											$i++;}
										}

										$response = array(
											'status' => 1,
											'message' => 'Success',
											'data' => $data,
											'total'=> isset($i) ? $i : 0
										);

									}
									else
									{
										$response = array(
											'status' => 0,
											'message' => lang('USER_ID_ERROR')
										);
									}

								}
							}
							log_message('debug', 'Get User Direct Active Downline API Response - '.json_encode($response));	
							echo json_encode($response);

						}

						public function getUserDirectDeactiveDownline(){

							$response = array();
							$post = $this->input->post();
							log_message('debug', 'Get User Direct Deactive Downline API Post Data - '.json_encode($post));	
							$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
							$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
							if ($this->form_validation->run() == FALSE)
							{
								$response = array(
									'status' => 0,
									'message' => lang('LOGIN_VALID_FAILED')
								);
							}
							else
							{	

								$user_id = $post['userID'];

								$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

								$password = isset($userData['password']) ? $userData['password'] : '';

								$header_data = apache_request_headers();

								$token = isset($header_data['Token']) ? $header_data['Token'] : '';
								
								$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
								
								$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

								$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

								if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

									$response = array(
										'status' => 0,
										'message' => 'Session out.Please Login Again.'
									);
								}
								else{


									$userID = $post['userID'];
									$siteUrl = base_url();
									// check user valid or not
									$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
									if($chk_user)
									{
										// get member direct downline
										$directDownlineList = $this->User->get_member_direct_deactive_downline($userID);

										$data = array();
										if($directDownlineList)
											{	$i = 0;
												foreach($directDownlineList as $key=>$list){

													$data[$i]['memberID'] = $list['memberID'];
													$data[$i]['name'] = $list['name'];
													$data[$i]['user_code'] = $list['user_code'];
													$data[$i]['email'] = $list['email'];
													$data[$i]['mobile'] = $list['mobile'];
													$data[$i]['level'] = $list['level'];
													$data[$i]['membership'] = $this->User->get_user_membership_type($list['memberID']);
													$i++;}
												}

												$response = array(
													'status' => 1,
													'message' => 'Success',
													'data' => $data,
													'total'=> isset($i) ? $i : 0
												);

											}
											else
											{
												$response = array(
													'status' => 0,
													'message' => lang('USER_ID_ERROR')
												);
											}

										}
									}
									log_message('debug', 'Get User Direct Deactive Downline API Response - '.json_encode($response));	
									echo json_encode($response);

								}

								public function getUserTotalDownline(){

									$response = array();
									$post = $this->input->post();
									log_message('debug', 'Get User Total Downline API Post Data - '.json_encode($post));	
									$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
									$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
									if ($this->form_validation->run() == FALSE)
									{
										$response = array(
											'status' => 0,
											'message' => lang('LOGIN_VALID_FAILED')
										);
									}
									else
									{	
										$user_id = $post['userID'];

										$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

										$password = isset($userData['password']) ? $userData['password'] : '';

										$header_data = apache_request_headers();

										$token = isset($header_data['Token']) ? $header_data['Token'] : '';
										
										$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
										
										$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

										$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

										if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

											$response = array(
												'status' => 0,
												'message' => 'Session out.Please Login Again.'
											);
										}
										else{

											$userID = $post['userID'];
											$siteUrl = base_url();
											// check user valid or not
											$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
											if($chk_user)
											{
												// get member direct downline
												$directDownlineList = $this->User->get_level_wise_all_member($userID,2);

												$data = array();
												if($directDownlineList)
													{	$i = 0;
														foreach($directDownlineList as $key=>$list){

															$data[$key]['memberID'] = $list['memberID'];
															$data[$key]['name'] = $list['name'];
															$data[$key]['user_code'] = $list['user_code'];
															$data[$key]['email'] = $list['email'];
															$data[$key]['mobile'] = $list['mobile'];
															$data[$key]['level'] = $list['level'];
															$data[$key]['membership'] = $this->User->get_user_membership_type($list['memberID']);
															$i++;}
														}

														$response = array(
															'status' => 1,
															'message' => 'Success',
															'data' => $data,
															'total'=> isset($i) ? $i : 0
														);

													}
													else
													{
														$response = array(
															'status' => 0,
															'message' => lang('USER_ID_ERROR')
														);
													}

												}
											
											}
											log_message('debug', 'Get User Total Downline API Response - '.json_encode($response));	
											echo json_encode($response);

										}

										public function getUserTotalActiveDownline(){

											$response = array();
											$post = $this->input->post();
											log_message('debug', 'Get User Total Active Downline API Post Data - '.json_encode($post));	
											$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
											$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
											if ($this->form_validation->run() == FALSE)
											{
												$response = array(
													'status' => 0,
													'message' => lang('LOGIN_VALID_FAILED')
												);
											}
											else
											{	
												$user_id = $post['userID'];

												$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

												$password = isset($userData['password']) ? $userData['password'] : '';

												$header_data = apache_request_headers();

												$token = isset($header_data['Token']) ? $header_data['Token'] : '';
												
												$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
												
												$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

												$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

												if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

													$response = array(
														'status' => 0,
														'message' => 'Session out.Please Login Again.'
													);
												}
												else{

													$userID = $post['userID'];
													$siteUrl = base_url();
													// check user valid or not
													$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
													if($chk_user)
													{
														// get member direct downline
														$directDownlineList = $this->User->get_level_wise_all_member($userID,1);

														

														$data = array();
														if($directDownlineList)
															{	$i = 0;
																foreach($directDownlineList as $key=>$list){

																	$data[$i]['memberID'] = $list['memberID'];
																	$data[$i]['name'] = $list['name'];
																	$data[$i]['user_code'] = $list['user_code'];
																	$data[$i]['email'] = $list['email'];
																	$data[$i]['mobile'] = $list['mobile'];
																	$data[$i]['level'] = $list['level'];
																	$data[$i]['membership'] = $this->User->get_user_membership_type($list['memberID']);
																	$i++;}
																}

																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data' => $data,
																	'total'=> isset($i) ? $i : 0 
																);

															}
															else
															{
																$response = array(
																	'status' => 0,
																	'message' => lang('USER_ID_ERROR')
																);
															}

														}
													}
													log_message('debug', 'Get User Total Active Downline API Response - '.json_encode($response));	
													echo json_encode($response);

												}

												public function getUserTotalDeactiveDownline(){

													$response = array();
													$post = $this->input->post();
													log_message('debug', 'Get User Total Deactive Downline API Post Data - '.json_encode($post));	
													$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
													$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
													if ($this->form_validation->run() == FALSE)
													{
														$response = array(
															'status' => 0,
															'message' => lang('LOGIN_VALID_FAILED')
														);
													}
													else
													{	
														$user_id = $post['userID'];

														$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

														$password = isset($userData['password']) ? $userData['password'] : '';

														$header_data = apache_request_headers();

														$token = isset($header_data['Token']) ? $header_data['Token'] : '';
														
														$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
														
														$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

														$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

														if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

															$response = array(
																'status' => 0,
																'message' => 'Session out.Please Login Again.'
															);
														}
														else{
															$userID = $post['userID'];
															$siteUrl = base_url();
															// check user valid or not
															$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
															if($chk_user)
															{
																// get member direct downline
																$directDownlineList = $this->User->get_level_wise_all_member($userID,0);

																
																$data = array();
																if($directDownlineList)
																	{	$i = 0;
																		foreach($directDownlineList as $key=>$list){

																			$data[$i]['memberID'] = $list['memberID'];
																			$data[$i]['name'] = $list['name'];
																			$data[$i]['user_code'] = $list['user_code'];
																			$data[$i]['email'] = $list['email'];
																			$data[$i]['mobile'] = $list['mobile'];
																			$data[$i]['level'] = $list['level'];
																			$data[$i]['membership'] = $this->User->get_user_membership_type($list['memberID']);
																			$i++;}
																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data' => $data,
																			'total'=> isset($i) ? $i : 0 
																		);

																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => lang('USER_ID_ERROR')
																		);
																	}

																}
															}
															log_message('debug', 'Get User Total Deactive Downline API Response - '.json_encode($response));	
															echo json_encode($response);

														}






														public function changePassword(){

															$response = array();
															$post = $this->input->post();
															log_message('debug', 'Change Password Post Data - '.json_encode($post));	
															$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
															$this->form_validation->set_rules('userID', 'User Id', 'required|xss_clean');
															$this->form_validation->set_rules('opw', 'Old Password', 'required|xss_clean');     
															$this->form_validation->set_rules('npw', 'New Password', 'required|xss_clean');     
															$this->form_validation->set_rules('cpw', 'Confirm New Password', 'required|xss_clean|matches[npw]');
															if ($this->form_validation->run() == FALSE)
															{
																$response = array(
																	'status' => 0,
																	'message' => lang('LOGIN_VALID_FAILED')
																);
															}
															else
															{	

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$chk_old_pwd = $this->db->get_where('users',array('id'=>$post['userID'],'password'=>do_hash($post['opw'])))->num_rows();
																	if($chk_old_pwd)           
																	{
																		$data = array(
																			'password' => do_hash($post['npw']),
																			'decode_password' =>$post['npw'],
																			'updated' => date('Y-m-d h:i:s')
																		);

																		$this->db->where('id',$post['userID']);
																		$this->db->update('users',$data);

																		$message = 'Password Changed Successfully.';

																		$this->User->sendNotification($post['userID'],'Change Password',$message);

																		$response = array(
																			'status' => 1,
																			'message' => 'Password Changed Successfully',
																		);

																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'old password is not valid'
																		);

																	}

																}
															}
															log_message('debug', 'Change Password API Response - '.json_encode($response));	
															echo json_encode($response);

														}




														public function changeTransactionPassword(){

															$response = array();
															$post = $this->input->post();
															log_message('debug', 'Change Transaction Password Post Data - '.json_encode($post));	
															$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
															$this->form_validation->set_rules('userID', 'User Id', 'required|xss_clean');
															$this->form_validation->set_rules('otpw', 'Old Password', 'required|xss_clean');     
															$this->form_validation->set_rules('ntpw', 'New Password', 'required|xss_clean|max_length[4]|min_length[4]');     
															$this->form_validation->set_rules('ctpw', 'Confirm New Password', 'required|xss_clean|matches[ntpw]');
															if ($this->form_validation->run() == FALSE)
															{
																$response = array(
																	'status' => 0,
																	'message' => 'Please Enter Valid Data'
																);
															}
															else
															{	
																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$chk_old_pwd = $this->db->get_where('users',array('id'=>$post['userID'],'transaction_password'=>do_hash($post['otpw'])))->num_rows();
																	if($chk_old_pwd)           
																	{
																		$data = array(
																			'transaction_password' => do_hash($post['ntpw']),
																			'decoded_transaction_password' =>$post['ntpw'],
																			'updated' => date('Y-m-d h:i:s')
																		);

																		$this->db->where('id',$post['userID']);
																		$this->db->update('users',$data);

																		$message = 'Transaction Password Changed Successfully.';

																		$this->User->sendNotification($post['userID'],'Change Password',$message);

																		$response = array(
																			'status' => 1,
																			'message' => 'Transaction Password Changed Successfully',
																		);

																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'old transaction password is not valid'
																		);

																	}

																}
															}
															log_message('debug', 'Change Transaction Password API Response - '.json_encode($response));	
															echo json_encode($response);

														}


														public function upgrade(){

			//check for foem validation
															$response = array();
															$post = $this->input->post();
															log_message('debug', 'Upgrade API Post Data - '.json_encode($post));
															$this->load->library('form_validation');

															$this->form_validation->set_rules('userID', 'User ID', 'required');
															$this->form_validation->set_rules('package_id', 'Package ID', 'required|xss_clean'); 

															if ($this->form_validation->run() == FALSE) {

																$response = array(
																	'status' => 0,
																	'message' => 'Please Enter all required data'
																);
															}
															else
															{   

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{


																	if($post['package_id'] < 2)
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! Upgrade Failed'
																		);
																	}
																	else{


																		if(!isset($post['memberID']) || $post['memberID'] == ''){

																			$response = array(
																				'status' => 0,
																				'message' => 'Please Enter MemberID.'
																			);	
																		}
																		else{


																			$chk_member_id = $this->db->get_where('users',array('user_code'=>trim($post['memberID'])))->row_array();

																			$account_id = isset($chk_member_id['id']) ? $chk_member_id['id'] : 0;
																			$package_id = $post['package_id'];
																			$get_package_amount = $this->db->get_where('package',array('id'=>$post['package_id']))->row_array();
																			$package_amount = isset($get_package_amount['final_amount']) ? $get_package_amount['final_amount'] : 0 ;


																			if(!$chk_member_id){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry memberID is not valid.'
																				);
																			}
																			else{

																				if($chk_member_id['current_package_id'] == $post['package_id']){ 

																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! Account is already upgraded.'
																					);
																				}
																				elseif($chk_member_id['current_package_id'] > $post['package_id']){ 

																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! You can not upgrade high package to low package.'
																					);
																				}
																				else{


																					if($post['upgrade_by'] == 1){	

																						if(!isset($post['token']) || $post['token'] == '')
																						{
																							$response = array(
																								'status' => 0,
																								'message' => 'Please Enter Pin.'
																							);
																						}
																						else{

																							$chk_pin = $this->db->get_where('member_token',array('member_id'=>$post['userID'],'package_id'=>$post['package_id'],'token'=>$post['token'],'is_used'=>0))->num_rows();
																							if(!$chk_pin)
																							{
																								$response = array(
																									'status' => 0,
																									'message' => 'Sorry! Pin is not valid or expired.'
																								);
																							}
																							else{

																								$upgrade_by = $post['upgrade_by'];

																								$this->User->upgrade_member_package($account_id,$package_id,$post['token'],$upgrade_by,$post['userID']);

																								$message = 'Congratulations!! account upgraded succesfully.';

																								$this->User->sendNotification($account_id,'Account Upgrade',$message);

																								$message = 'Congratulations!! account upgraded succesfully.';

																								$this->User->sendNotification($post['userID'],'Account Upgrade',$message);

																								$response = array(
																									'status' => 1,
																									'message' => 'Congratulations!! account upgraded succesfully.'
																								);
																							}
																						}
																					}
																					elseif($post['upgrade_by'] == 2){

																						$chk_wallet_balance = $this->db->get_where('users',array('id'=>$post['userID']))->row_array();
																						
																						$reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
																						

																						$wallet_balance = $chk_wallet_balance['wallet_balance'];
                                                                                        
                                                                                        
                                                                                        	if($chk_wallet_balance['is_main_wallet_block'] ==1){

                                                        										$response = array(
                                                        											'status' => 0,
                                                        											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
                                                        										);
                                                        									}
                                                        									
																						elseif($reserved_wallet_balance < $package_amount){

																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! you have insufficient balance in your wallet.'
																							);

																						}
																						else{

																							$upgrade_by = $post['upgrade_by'];

																							$this->User->upgrade_member_package($account_id,$package_id,$post['token'],$upgrade_by,$post['userID']);

																							$this->db->where('member_id',$post['userID']);
																							$this->db->where('token',$post['token']);
																							$this->db->update('member_token',array('is_used'=>1));


																							$message = 'Congratulations!! account upgraded succesfully.';

																							$this->User->sendNotification($account_id,'Account Upgrade',$message);

																							$message = 'Congratulations!! account upgraded succesfully.';

																							$this->User->sendNotification($post['userID'],'Account Upgrade',$message);


																							$response = array(
																								'status' => 1,
																								'message' => 'Congratulations!! account upgraded succesfully.'
																							);

																						}

																					}
																					else{

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! something went wrong..'
																						);

																					}

																				}
																			}
																		}
																	}



																}
															}

															log_message('debug', 'Upgrade API Response - '.json_encode($response));	
															echo json_encode($response);

														}


														public function fundTransferHistory(){

															$response = array();

															$post = $this->input->post();
															$user_id = isset($post['user_id']) ? $post['user_id'] : 0;


															$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

															$password = isset($userData['password']) ? $userData['password'] : '';

															$header_data = apache_request_headers();

															$token = isset($header_data['Token']) ? $header_data['Token'] : '';
															
															$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
															
															$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

															$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

															if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																$response = array(
																	'status' => 0,
																	'message' => 'Session out.Please Login Again.'
																);
															}
															else{

																$fromDate = $post['fromDate'];
																$toDate   = $post['toDate'];
																$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																$limit = $page_no * 50;

																if($fromDate && $toDate){

																	$count = $this->db->order_by('created','desc')->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'is_dmr'=>1,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->num_rows();

																	$limit_start = $limit - 50; 

																	$limit_end = $limit;

																	$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'is_dmr'=>1,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->result_array();
																}
																else{

																	$count = $this->db->order_by('created','desc')->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'is_dmr'=>1))->num_rows();

																	$limit_start = $limit - 50; 

																	$limit_end = $limit;

																	$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'is_dmr'=>1))->result_array();
																}		

																$data = array();

																$pages = ceil($count / 50);

																if($userList)
																{
																	foreach($userList as $key=>$list)
																	{
																		$data[$key]['memberID'] = $list['memberID'];
																		$data[$key]['account_holder_name'] = $list['account_holder_name'];
																		$data[$key]['mobile'] = $list['mobile'];
																		$data[$key]['ifsc'] = $list['ifsc'];
																		$data[$key]['transfer_amount'] = $list['transfer_amount'].' /-';
																		$data[$key]['transaction_id'] = $list['transaction_id'];
																		$data[$key]['rrn'] = $list['rrn'];

																		if($list['status'] == 2) {
																			$data[$key]['status'] = 'Pending';
																		}
																		elseif($list['status'] == 3) {
																			$data[$key]['status'] = 'Success';
																		}
																		elseif($list['status'] == 4 || $list['status'] == 0) {
																			$data[$key]['status'] = 'Failed';
																		}

																		$data[$key]['date'] = date('d-M-Y H:i:s',strtotime($list['created']));

																	}

																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data'=>$data,
																		'pages' => $pages
																	);
																}
																else{

																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry!! record not found.',
																	);
																}
															}
															log_message('debug', 'Get Fund Transfer List API Response - '.json_encode($response));	
															echo json_encode($response);

														}

														public function addBenificary(){

			//check for foem validation
															$response = array();
															$post = $this->input->post();
															log_message('debug', 'Add Benificiary API Post Data - '.json_encode($post));
															$this->load->library('form_validation');

															$this->form_validation->set_rules('userID', 'User ID', 'required');
															$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');     
															$this->form_validation->set_rules('bank_name', 'Bank Name', 'required|xss_clean');     
															$this->form_validation->set_rules('account_number', 'Account Number', 'required|xss_clean|numeric|min_length[10]');     
															$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean'); 

															if ($this->form_validation->run() == FALSE) {

																$response = array(
																	'status' => 0,
																	'message' => 'Please Enter all required data'
																);
															}
															else
															{   

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{


																		$account_id = $post['userID'];

																		$bene_data = array(

																			'user_id' => $account_id,
																			'account_holder_name' => $post['account_holder_name'],
																			'bank_name' => $post['bank_name'],
																			'account_no' => $post['account_number'],
																			'ifsc' => $post['ifsc'],
																			'encode_ban_id' => do_hash($post['account_number']),	
																			'status' => 1,
																			'created' => date('Y-m-d H:i:s')

																		);

																		$this->db->insert('user_benificary',$bene_data);


																		$message = 'Congratulations!! beneficiary added succesfully.';

																		$this->User->sendNotification($account_id,'Add Beneficiary',$message);

																		$response = array(
																			'status' => 1,
																			'message' => 'Congratulations!! beneficiary added succesfully.'
																		);
																	}
																}
															}


															log_message('debug', 'Add Benificary api Response - '.json_encode($response));	
															echo json_encode($response);

														}


														public function benificaryList()
														{
															$post = $this->input->post();
															log_message('debug', 'Benificary List API POST Data - '.json_encode($post));	
															$userID = isset($post['userID']) ? $post['userID'] : 0;

															$user_id = $post['userID'];

															$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

															$password = isset($userData['password']) ? $userData['password'] : '';

															$header_data = apache_request_headers();

															$token = isset($header_data['Token']) ? $header_data['Token'] : '';
															
															$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
															
															$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

															$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

															if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																$response = array(
																	'status' => 0,
																	'message' => 'Session out.Please Login Again.'
																);
															}
															else{

																$response = array();
																// check user valid or not
																$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																if($chk_user)
																{
																	$benificiaryList = $this->db->order_by('created','desc')->get_where('user_benificary',array('user_id'=>$userID))->result_array();

																	$data = array();
																	if($benificiaryList)
																	{
																		foreach ($benificiaryList as $key => $list) {

																			$data[$key]['bene_id'] = $list['id'];
																			$data[$key]['benificiary_name'] = $list['account_holder_name'];
																			$data[$key]['account_no'] = $list['account_no'];
																			$data[$key]['bank'] = $list['bank_name'];
																			$data[$key]['ifsc'] = $list['ifsc'];
																			$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));

																		}
																	}

																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data' => $data,
																	);	
																}
																else
																{
																	$response = array(
																		'status' => 0,
																		'message' => lang('USER_ID_ERROR')
																	);
																}
															}
															log_message('debug', 'Benificary List API Response - '.json_encode($response));	
															echo json_encode($response);
														}


														function amountCheck($num)
														{

															if ($num < 100)
															{
																$this->form_validation->set_message(
																	'amountCheck',
																	'The %s field must be grater than 100'
																);
																return FALSE;
															}
															else
															{
																return TRUE;
															}
														}



														public function fundTransferAuth(){

			//check for foem validation
															$response = array();
															$post = $this->input->post();
															log_message('debug', 'Fund Transfer API Post Data - '.json_encode($post));
															$this->load->library('form_validation');

															$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
															$this->form_validation->set_rules('bene_id', 'Benificary Id', 'required|xss_clean');
															$this->form_validation->set_rules('txn_pass', 'Transaction Password', 'required|xss_clean');
															$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_amountCheck');

															if ($this->form_validation->run() == FALSE) {

																$response = array(
																	'status' => 0,
																	'message' => 'Please Enter all required data'
																);
															}
															else
															{   
																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{	

																	$account_id = $post['userID'];
																	$activeService = $this->User->account_active_service($account_id);
																	if(!in_array(2, $activeService)){

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! money transfer is not active.'
																		);
																	}	
																	else{


																		$get_kyc_status = $this->db->select('kyc_status.*')->join('kyc_status','kyc_status.id = users.kyc_status')->get_where('users',array('users.id'=>$account_id))->row_array();

																		if($get_kyc_status['id'] != 3){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry ! your kyc is not completed. Please submit your kyc.'
																			);		

																		}				
																		else{

					        // get account detail
																			$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();

																			$get_beneficiary_detail = $this->db->get_where('user_benificary',array('user_id'=>$account_id,'id'=>$post['bene_id']))->row_array();
																			if(!$get_beneficiary_detail){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! beneficiary is not exists.'
																				);

																			}
																			else{	

																				$post['mobile'] = $accountDetail['mobile'];
																				$post['account_holder_name'] = $get_beneficiary_detail['account_holder_name'];
																				$post['account_no'] = $get_beneficiary_detail['account_no'];
																				$post['ifsc'] = $get_beneficiary_detail['ifsc'];

																				$wallet_balance = $accountDetail['wallet_balance'];


																				if($accountDetail['transaction_password'] != do_hash($post['txn_pass'])){

																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! transaction password is wrong.'
																					);	

																				}
																				else{

																					$transfer_amount = $post['amount'];

																					if($transfer_amount < 1){


																						$response = array(
																							'status' => 0,
																							'message' => 'Please enter valid amount.'
																						);

																					}
																					else{
										// get transfer surcharge
																						$surcharge_amount = $this->User->get_dmr_transfer_surcharge($transfer_amount);

																						$total_wallet_deduct = $transfer_amount + $surcharge_amount;

							        	// check account balance
							        	                                                $reserved_wallet_balance = $accountDetail['wallet_balance'] - $accountDetail['reserve_wallet_balance'] ;
							        	                                                
							        	
							        	                                                	if($accountDetail['is_main_wallet_block'] ==1){

                                                        										$response = array(
                                                        											'status' => 0,
                                                        											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
                                                        										);
                                                        									}
																						elseif($reserved_wallet_balance < $total_wallet_deduct)
																						{
																							log_message('debug', 'Main Wallet Fund Transfer Low Balance Error');	
																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! you have insufficient balance in your wallet.'
																							);
																						}
																						else{

																							$encrypt_otp_code = $this->User->sendBankTransferOtp($post,$account_id);

																							$response = array(
																								'status' => 1,
																								'message' => 'Otp sent to your registered email. Please verify.'
																							);

								    //         // save fund transfer request
								    //         $response = $this->Api_model->dmr_bank_transfer($post,$account_id);

								    //         log_message('debug', 'Main Wallet Transfer Fund Final API Response - '.json_encode($response));	

								    //         if($response['status'] == 1)
								    //         {	
								    //         	$message = 'Congratulations! Fund transfered succesfully to beneficiary account.';

				    				// 			$this->User->sendNotification($account_id,'Fund Transfer',$message);

								    //         	$response = array(
												// 	'status' => 1,
												// 	'message' => 'Congratulations! Fund transfered succesfully to beneficiary account.'
												// );
								    //     	}
								    //     	elseif($response['status'] == 2)
								    //         {
								    //         	$response = array(
												// 	'status' => 1,
												// 	'message' => 'Your transaction is in under process, status will be update soon.'
												// );
								    //     	}
								    //     	else
								    //     	{
								    //     		$response = array(
												// 	'status' => 0,
												// 	'message' => 'Sorry! Your transaction is failed.'
												// );

								    //     	}


																						}
																					}
																				}
																			}

																		}
																	}    

																}
															}

															log_message('debug', 'Fund Transfer api Response - '.json_encode($response));	
															echo json_encode($response);

														}



														public function fundTransferOtpAuth(){

			//check for foem validation
															$response = array();
															$post = $this->input->post();
															log_message('debug', 'Fund Transfer OTP Auth API Post Data - '.json_encode($post));
															$this->load->library('form_validation');

															$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
															$this->form_validation->set_rules('otp_code', 'Otp', 'required|xss_clean');

															if ($this->form_validation->run() == FALSE) {

																$response = array(
																	'status' => 0,
																	'message' => 'Otp is required.'
																);
															}
															else
															{   
																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$account_id = $post['userID'];
																	$activeService = $this->User->account_active_service($account_id);
																	if(!in_array(2, $activeService)){

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! money transfer is not active.'
																		);
																	}	
																	else{


																		$get_kyc_status = $this->db->select('kyc_status.*')->join('kyc_status','kyc_status.id = users.kyc_status')->get_where('users',array('users.id'=>$account_id))->row_array();

																		if($get_kyc_status['id'] != 3){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry ! your kyc is not completed. Please submit your kyc.'
																			);		

																		}				
																		else{


																			$chk_otp = $this->db->get_where('users_otp',array('user_id'=>$account_id,'otp_code'=>$post['otp_code'],'status'=>0))->row_array();

																			if(!$chk_otp){

																				$response = array(

																					'status'  => 0,
																					'message' => 'Sorry!! otp not valid'
																				);	
																			}
																			else{

																				$this->db->where('id',$chk_otp['id']);
																				$this->db->update('users_otp',array('status'=>1));

																				$post_data = json_decode($chk_otp['json_post_data'],true);

						        // get account detail
																				$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();

																				$get_beneficiary_detail = $this->db->get_where('user_benificary',array('user_id'=>$account_id,'id'=>$post_data['bene_id']))->row_array();
																				if(!$get_beneficiary_detail){

																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! beneficiary is not exists.'
																					);

																				}
																				else{	

																					$post_data['mobile'] = $accountDetail['mobile'];
																					$post_data['account_holder_name'] = $get_beneficiary_detail['account_holder_name'];
																					$post_data['account_no'] = $get_beneficiary_detail['account_no'];
																					$post_data['ifsc'] = $get_beneficiary_detail['ifsc'];

																					$wallet_balance = $accountDetail['wallet_balance'];


																					if($accountDetail['transaction_password'] != do_hash($post_data['txn_pass'])){

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! transaction password is wrong.'
																						);	

																					}
																					else{

																						$transfer_amount = $post_data['amount'];

																						if($transfer_amount < 1){


																							$response = array(
																								'status' => 0,
																								'message' => 'Please enter valid amount.'
																							);

																						}
																						else{
											// get transfer surcharge
																							$surcharge_amount = $this->User->get_dmr_transfer_surcharge($transfer_amount);

																							$total_wallet_deduct = $transfer_amount + $surcharge_amount;

								        	// check account balance
								        	                                               $reserved_wallet_balance = $accountDetail['wallet_balance'] - $accountDetail['reserve_wallet_balance'] ;
								        	                                               
								        	                                               
								        	                                               if($accountDetail['is_main_wallet_block'] ==1){

                                                        										$response = array(
                                                        											'status' => 0,
                                                        											'message' => 'Sorry ! Your fund is blocked for 2 Days.'
                                                        										);
                                                        									}
                                                        									
																							elseif($reserved_wallet_balance < $total_wallet_deduct)
																							{
																								log_message('debug', 'Main Wallet Fund Transfer Low Balance Error');	
																								$response = array(
																									'status' => 0,
																									'message' => 'Sorry!! you have insufficient balance in your wallet.'
																								);
																							}
																							else{

								        		// save fund transfer request
																								$response = $this->Api_model->dmr_bank_transfer($post_data,$account_id);

																								log_message('debug', 'Main Wallet Transfer Fund Final API Response - '.json_encode($response));	

																								if($response['status'] == 1)
																								{	
																									$message = 'Congratulations! Fund transfered succesfully to beneficiary account.';

																									$this->User->sendNotification($account_id,'Fund Transfer',$message);

																									$response = array(
																										'status' => 1,
																										'message' => 'Congratulations! Fund transfered succesfully to beneficiary account.'
																									);
																								}
																								elseif($response['status'] == 2)
																								{
																									$response = array(
																										'status' => 1,
																										'message' => 'Your transaction is in under process, status will be update soon.'
																									);
																								}
																								else
																								{
																									$response = array(
																										'status' => 0,
																										'message' => 'Sorry! Your transaction is failed.'
																									);

																								}


																							}
																						}
																					}
																				}
																			}

																		}
																	}    

																}
															}

															log_message('debug', 'Fund Transfer api Response - '.json_encode($response));	
															echo json_encode($response);

														}


														public function getPackage()
														{
															$response = array();

															$get_package_data = $this->db->get_where('package',array('id >'=>1,'status'=>1))->result_array();


															$packageData = array();
															if($get_package_data)
															{
																foreach($get_package_data as $key=>$list)
																{
																	$packageData[$key]['package_id'] = $list['id'];
																	$packageData[$key]['package_name'] = $list['package_name'];
																	$packageData[$key]['package_amount'] = $list['final_amount'];
																}      		
															}

															$response = array(
																'status' => 1,
																'message' => 'Success',
																'data' => $packageData
															);	


															echo json_encode($response);
														}



														public function getLevelIncome ()
														{
															$post = $this->input->post();
															log_message('debug', 'Level income API POST Data - '.json_encode($post));	
															$userID = isset($post['userID']) ? $post['userID'] : 0;

															$user_id = $post['userID'];

															$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

															$password = isset($userData['password']) ? $userData['password'] : '';

															$header_data = apache_request_headers();

															$token = isset($header_data['Token']) ? $header_data['Token'] : '';
															
															$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
															
															$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

															$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

															if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																$response = array(
																	'status' => 0,
																	'message' => 'Session out.Please Login Again.'
																);
															}
															else{

																$response = array();
				// check user valid or not
																$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																if($chk_user)
																{
																	$recharge = $this->db->query("SELECT a.*,b.name as from_member_name,b.user_code as from_member_code FROM tbl_level_income as a INNER JOIN tbl_users as b ON b.id = a.paid_from_member_id where a.paid_to_member_id = '$userID' AND a.is_paid = 1 ")->result_array();

																	$data = array();
																	if($recharge)
																	{
																		foreach ($recharge as $key => $list) {

																			$data[$key]['from_member'] = $list['from_member_name'].' ('.$list['from_member_code'].')';
																			$data[$key]['level_num'] = $list['level_num'];
																			$data[$key]['date'] = date('d-M-Y',strtotime($list['created']));
																			$data[$key]['level_amount'] = number_format($list['level_amount'],2).'/-';
																			$data[$key]['tds_amount'] = number_format($list['tds_amount'],2).'/-';
																			$data[$key]['service_tax_amount'] = number_format($list['service_tax_amount'],2).'/-';
																			$data[$key]['wallet_settle_amount'] = number_format($list['wallet_settle_amount'],2).'/-';
																			$data[$key]['is_paid'] = isset($list['is_paid']) ? 'Paid' : 'Not Paid';

																		}
																	}

																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data' => $data,
																	);	
																}
																else
																{
																	$response = array(
																		'status' => 0,
																		'message' => lang('USER_ID_ERROR')
																	);
																}
															}
															log_message('debug', 'Level income API Response - '.json_encode($response));	
															echo json_encode($response);
														}


														public function getDirectIncome ()
														{
															$post = $this->input->post();
															log_message('debug', 'Direct income API POST Data - '.json_encode($post));	
															$userID = isset($post['userID']) ? $post['userID'] : 0;

															$user_id = $post['userID'];

															$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

															$password = isset($userData['password']) ? $userData['password'] : '';

															$header_data = apache_request_headers();

															$token = isset($header_data['Token']) ? $header_data['Token'] : '';
															
															$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
															
															$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

															$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

															if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																$response = array(
																	'status' => 0,
																	'message' => 'Session out.Please Login Again.'
																);
															}
															else{

																$response = array();
				// check user valid or not
																$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																if($chk_user)
																{
																	$recharge = $this->db->select('direct_income.*,users.user_code as by_member_code,users.name as by_member_name')->order_by('direct_income.created','desc')->join('users','users.id = direct_income.paid_from_member_id')->get_where('direct_income',array('direct_income.paid_to_member_id'=>$userID))->result_array();

																	$data = array();
																	if($recharge)
																	{
																		foreach ($recharge as $key => $list) {

																			$data[$key]['by_member'] = $list['by_member_name'].' ('.$list['by_member_code'].')';
																			$data[$key]['direct_amount'] = number_format($list['direct_amount'],2).'/-';
																			$data[$key]['tds_amount'] = number_format($list['tds_amount'],2).'/-';
																			$data[$key]['wallet_settle_amount'] = number_format($list['wallet_settle_amount'],2).'/-';
																			$data[$key]['is_paid'] = isset($list['is_paid']) ? 'Paid' : 'Not Paid';
																			$data[$key]['date'] = date('d-M-Y',strtotime($list['created']));

																		}
																	}

																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data' => $data,
																	);	
																}
																else
																{
																	$response = array(
																		'status' => 0,
																		'message' => lang('USER_ID_ERROR')
																	);
																}
															}
															log_message('debug', 'Direct income API Response - '.json_encode($response));	
															echo json_encode($response);
														}



														public function getElectricityField()
														{
															$post = $this->input->post();
															log_message('debug', 'Electricity Field Post Data - '.json_encode($post));	
															$operatorCode = isset($post['operatorCode']) ? $post['operatorCode'] : '';
															$response = array();

															if($operatorCode != '')
															{

																$response_data = $this->User->getElectricityOperatorDetail($operatorCode);

																$data = $response_data;

																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data' => $data
																);	
															}
															else
															{
																$response = array(
																	'status' => 0,
																	'message' => 'Operator Code Invalid'
																);
															}
															log_message('debug', 'Electricity Field API Response - '.json_encode($response));	
															echo json_encode($response);
														}


														public function getElectricityBillerDetail()
														{
															$post = $this->input->post();
															log_message('debug', 'Electricity Biller Detail API Post Data - '.json_encode($post));	
															$account_number = isset($post['account_number']) ? $post['account_number'] : '';
															$operator_code = isset($post['operatorCode']) ? $post['operatorCode'] : '';
															$userID = isset($post['userID']) ? $post['userID'] : 0;

															$user_id = $post['userID'];

															$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

															$password = isset($userData['password']) ? $userData['password'] : '';

															$header_data = apache_request_headers();

															$token = isset($header_data['Token']) ? $header_data['Token'] : '';
															
															$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
															
															$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

															$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

															if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																$response = array(
																	'status' => 0,
																	'message' => 'Session out.Please Login Again.'
																);
															}
															else{


																$response = $this->User->getElectricityOperatorBillerDetail($operator_code,$account_number,$userID);
																log_message('debug', 'Electricity Biller Detail API Response - '.json_encode($response));
															}	
															echo json_encode($response);

														}



														public function requestPinAuth(){

															$response = array();
															$post = $this->input->post();
															log_message('debug', 'Request Pin Auth API Post Data - '.json_encode($post));	
															$this->load->library('form_validation');

															$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
															$this->form_validation->set_rules('package_id', 'Package ID', 'required|xss_clean');
															$this->form_validation->set_rules('token_number', 'Number of Pin', 'required|xss_clean|numeric');

															if ($this->form_validation->run() == FALSE)
															{
																$response = array(
																	'status' => 0,
																	'message' => 'Please Enter valid data.'
																);
															}
															else
															{	

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$chk_user = $this->db->get_where('users',array('id'=>$post['userID']))->row_array();	

																	if($chk_user){

																		$member_id = $post['userID'];
																		$package_id = $post['package_id'];
																		$token_number = $post['token_number'];
																		if($token_number < 1)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! Pin number is not correct.'
																			);
																		}
																		else
																		{
							// get package amount
																			$get_package_detail = $this->db->get_where('package',array('status'=>1,'id'=>$package_id))->row_array();
																			$package_amount = isset($get_package_detail['package_amount']) ? $get_package_detail['package_amount'] : 0;

																			$tokenData = array(
																				'member_id' => $member_id,
																				'package_id' => $package_id,
																				'package_amount' => $package_amount,
																				'total_pin' => $token_number,
																				'status' => 1,
																				'created' => date('Y-m-d H:i:s'),
																				'created_by' => $member_id,
																			);
																			$this->db->insert('member_token_request',$tokenData);

																			$message = 'Pin Request sent Succesfully.';

																			$this->User->sendNotification($member_id,'E-Pin Request',$message); 

																			$response = array(
																				'status' => 1,
																				'message' => 'Pin Request sent Succesfully.'
																			);
																		} 	

																	}
																	else{

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! User not valid.'
																		);

																	}	


																}
															}
															log_message('debug', 'Request Pin Auth API Response - '.json_encode($response));	
															echo json_encode($response);

														}




														public function pinRequestList(){

															$response = array();
															$post = $this->input->post();
															log_message('debug', 'Pin Request List API Post Data - '.json_encode($post));	
															$this->load->library('form_validation');
															$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
															if ($this->form_validation->run() == FALSE)
															{
																$response = array(
																	'status' => 0,
																	'message' => 'Please Enter Valid Data.'
																);
															}
															else
															{
																$userID = $post['userID'];

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$siteUrl = base_url();
					// check user valid or not
																	$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																	if($chk_user)
																	{

																		$sql = "SELECT a.*,b.package_name FROM tbl_member_token_request as a INNER JOIN tbl_package as b ON b.id = a.package_id where a.member_id = '$userID' ";

																		$transaction = $this->db->query($sql)->result_array();

																		$data = array();
																		if($transaction)
																		{
																			foreach($transaction as $key=>$list){

																				$data[$key]['package_name'] = $list['package_name'];
																				$data[$key]['package_amount'] = 'INR '.$list['package_amount'];
																				$data[$key]['total_pin'] = $list['total_pin'];


																				if($list['status'] == 1) {
																					$data[$key]['status'] = 'Pending';
																				}
																				elseif($list['status'] == 2) {
																					$data[$key]['status'] = 'Approved';
																				}
																				elseif($list['status'] == 3){

																					$data[$key]['status'] = 'Rejected';	
																				}

																				$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));

																			}

																			$response = array(
																				'status' => 1,
																				'message' => 'Success',
																				'data' => $data
																			);
																		}
																		else{

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! data not found.',
																			);
																		}


																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! user not valid.'
																		);
																	}

																}
															}
															log_message('debug', 'Pin Request List API Response - '.json_encode($response));	
															echo json_encode($response);

														}



														public function transferPinAuth(){

															$response = array();
															$post = $this->input->post();
															log_message('debug', 'Request Pin Auth API Post Data - '.json_encode($post));	
															$this->load->library('form_validation');

															$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
															$this->form_validation->set_rules('package_id', 'Package ID', 'required|xss_clean');
															$this->form_validation->set_rules('token_number', 'Number of Pin', 'required|xss_clean|numeric');
															$this->form_validation->set_rules('transfer_to_user', 'transfer_to_user', 'required|xss_clean');

															if ($this->form_validation->run() == FALSE)
															{
																$response = array(
																	'status' => 0,
																	'message' => 'Please Enter valid data.'
																);
															}
															else
															{
																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$chk_user = $this->db->get_where('users',array('id'=>$post['userID']))->row_array();	

																	if($chk_user){

																		$loggedUser = $this->db->get_where('users',array('id'=>$post['userID']))->row_array();

																		$package_id = $post['package_id'];

																		$transfer_to_user = $post['transfer_to_user'];

																		$token_number = $post['token_number'];	
																		if($token_number < 1)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! pin no. not correct.'
																			);	
																		}
																		else{

																			$chk_transfer_to_user = $this->db->query("SELECT * FROM tbl_users WHERE user_code = '$transfer_to_user' OR mobile='$transfer_to_user'")->row_array();


																			if(!$chk_transfer_to_user){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! transfer to user not valid.'
																				);		
																			}
																			else{

																				$chk_available_pin = $this->db->get_where('member_token',array('is_used'=>0,'status'=>1,'package_id'=>$package_id,'member_id'=>$loggedUser['id']))->num_rows();

																				if($chk_available_pin < $token_number){


																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! you have insufficient pin to transfer.'
																					);

																				}
																				else{

																					$get_available_pin =  $this->db->get_where('member_token',array('is_used'=>0,'status'=>1,'package_id'=>$package_id,'member_id'=>$loggedUser['id']))->result_array();

																					if($get_available_pin){
																						$i = 0;
																						foreach($get_available_pin as $list){

																							if($i < $token_number){

																								$description = 'Pin received by #'.$loggedUser['user_code'];
																								$this->db->where('id',$list['id']);
																								$this->db->where('is_used',0);
																								$this->db->where('member_id',$loggedUser['id']);
																								$this->db->update('member_token',array('member_id'=>$chk_transfer_to_user['id'],'description'=>$description));
																							}	


																							$i++;}
																						}


																						$message = 'Pin transfered successfully.';

																						$this->User->sendNotification($loggedUser['id'],'E-Pin Transfer',$message); 

																						$response = array(
																							'status' => 1,
																							'message' => 'Pin transfered successfully.'
																						);
																					}
																				} 	
																			}

																		}
																		else{

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! User not valid.'
																			);

																		}	


																	}
																}
																log_message('debug', 'Request Pin Auth API Response - '.json_encode($response));	
																echo json_encode($response);

															}


															public function pinList(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Pin List API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
																$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter Valid Data.'
																	);
																}
																else
																{	
																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$userID = $post['userID'];
																		$siteUrl = base_url();
					// check user valid or not
																		$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																		if($chk_user)
																		{

																			$sql = "SELECT a.*,b.package_name FROM tbl_member_token as a INNER JOIN tbl_package as b ON b.id = a.package_id where a.member_id = '$userID' ";

																			$transaction = $this->db->query($sql)->result_array();

																			$data = array();
																			if($transaction)
																			{
																				foreach($transaction as $key=>$list){

																					$data[$key]['package_name'] = $list['package_name'];
																					$data[$key]['package_amount'] = 'INR '.$list['package_amount'];
																					$data[$key]['token'] = $list['token'];
																					if($list['is_used'] == 1) {
																						$data[$key]['is_used'] = 'Used';
																					}
																					else{
																						$data[$key]['is_used'] = 'Not Used';
																					}


																					if($list['used_by'])
																					{
									// get member name
																						$get_used_member = $this->db->get_where('users',array('id'=>$list['used_by']))->row_array();
																						$data[$key]['used_by'] = $get_used_member['name'].' ('.$get_used_member['user_code'].')';
																					}
																					else
																					{
																						$data[$key]['used_by'] = 'Not Available';
																					}

																					if($list['used_date'])
																					{
																						$data[$key]['used_date'] = date('d-M-Y H:i:s',strtotime($list['used_date']));
																					}
																					else
																					{
																						$data[$key]['used_date'] = 'Not Available';
																					}



																				}

																				$response = array(
																					'status' => 1,
																					'message' => 'Success',
																					'data' => $data
																				);
																			}
																			else{

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! data not found.',
																				);
																			}


																		}
																		else
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! user not valid.'
																			);
																		}

																	}
																}
																log_message('debug', 'Pin List API Response - '.json_encode($response));	
																echo json_encode($response);

															}


															public function genratePinAuth(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Generate Pin Auth API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('package_id', 'Package ID', 'required|xss_clean');
																$this->form_validation->set_rules('token_number', 'Number of Pin', 'required|xss_clean|numeric');

																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter valid data.'
																	);
																}
																else
																{	

																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry!! Please contact to administrator or request pin.'
																	);			

			  //  $chk_user = $this->db->get_where('users',array('id'=>$post['userID']))->row_array();	

			  //  if($chk_user){

			  //  		$member_id = $post['userID'];
					// $package_id = $post['package_id'];
					// $token_number = $post['token_number'];

					// $get_package_detail = $this->db->get_where('package',array('status'=>1,'id'=>$package_id))->row_array();
					// $package_amount = isset($get_package_detail['package_amount']) ? $get_package_detail['package_amount'] : 0;

					// $chk_wallet_balance = $this->db->select('wallet_balance')->get_where('users',array('id'=>$member_id))->row_array();

					// if($token_number < 1)
					// {
					// 	$response = array(
					// 			'status' => 0,
					// 			'message' => 'Sorry!! PIN No. is not correct.'
					// 	  );
					// }
					// else
					// {

					// 	$total_amount = $package_amount * $token_number;

					// 	if($chk_wallet_balance['wallet_balance'] < $total_amount){

					// 	  $response = array(
					// 			'status' => 0,
					// 			'message' => 'Sorry!! you have insufficient balance in your wallet.'
					// 	  );	

					// 	}
					// 	else{

					// 		// update member wallet
					// 		$after_balance = $chk_wallet_balance['wallet_balance'] - $total_amount;
					// 		$wallet_data = array(
					// 			'member_id'           => $member_id,    
					// 			'before_balance'      => $chk_wallet_balance['wallet_balance'],
					// 			'amount'              => $total_amount,  
					// 			'after_balance'       => $after_balance,      
					// 			'status'              => 1,
					// 			'type'                => 2,      
					// 			'wallet_type'		  => 1,
					// 			'created'             => date('Y-m-d H:i:s'),      
					// 			'description'         => 'Pin Generate Wallet Deducation' 
					// 		);

					// 		$this->db->insert('member_wallet',$wallet_data);

					// 		// update member current wallet balance
					// 		$this->db->where('id',$member_id);
					// 		$this->db->update('users',array('wallet_balance'=>$after_balance));


					// 		for($i = 1; $i<=$token_number; $i++)
					// 		{
					// 			$decode_token = rand(1111,9999).time();
					// 			$tokenData = array(
					// 				'member_id' => $member_id,
					// 				'package_id' => $package_id,
					// 				'package_amount' => $package_amount,
					// 				'token' => do_hash($decode_token),
					// 				'decode_token' => $decode_token,
					// 				'status' => 1,
					// 				'created' => date('Y-m-d H:i:s'),
					// 				'created_by' => $member_id,
					// 			);
					// 			$this->db->insert('member_token',$tokenData);
					// 		}

					// 		$response = array(
					// 			'status' => 1,
					// 			'message' => 'Congratulations!! pin generated successfully.'
					// 		);

					// 	}
					// }

			  //  }
			  //  else{

				 //   	$response = array(
					// 	'status' => 0,
					// 	'message' => 'Sorry!! User not valid.'
					// );

			  //  }	


																}
																log_message('debug', 'Generate Pin Auth API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function getUserName(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Get user name API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																$this->form_validation->set_rules('user_code', 'User Code', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry!! Enter memberID.'
																	);
																}
																else
																{
																	$user_code = $post['user_code'];

																	$get_user_data =$this->db->query("SELECT * FROM tbl_users WHERE (user_code = '$user_code') OR mobile = '$user_code' OR email = '$user_code' OR qr_unique_id = '$user_code' AND role_id = 2")->row_array();
																	if($get_user_data){

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'member_name'=>$get_user_data['name']
																		);
																	}
																	else{

																		$response = array(
																			'status' => 1,
																			'message' => 'Sorry!! member not exists.',
																		);
																	}

																}
																log_message('debug', 'Get user name API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function getAvailablePin(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'getAvailablePin API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																$this->form_validation->set_rules('userID', 'UserID', 'required|xss_clean');
																$this->form_validation->set_rules('package_id', 'PackageID', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry!! Enter required detail.'
																	);
																}
																else
																{	

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$pin = $this->db->get_where('member_token',array('is_used'=>0,'status'=>1,'package_id'=>$post['package_id'],'member_id'=>$post['userID']))->num_rows();


																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'pin'=>$pin
																		);

																	}
																}
																log_message('debug', 'getAvailablePin API Response - '.json_encode($response));	
																echo json_encode($response);

															}


															public function getQr(){

																$response = array();


																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'qr_code'=>base_url('qrcode')
																);

																echo json_encode($response);

															}


															public function staticQrAuth(){

																$response = array();

																$post = $this->input->post();

																$userID = isset($post['userID']) ? $post['userID'] : 0;

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	if(!$userID){

																		$resposne = array(

																			'status' => 0,
																			'message'=>'Please enter userID.'	

																		);
																	}
																	else{


																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'qr_code'=>base_url('qrcode/index/'.$userID.'')
																		);
																	}
																}

																echo json_encode($response);

															}


															public function getWalletQr(){

																$response = array();
																$post = $this->input->post();
																$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																$this->form_validation->set_rules('userID', 'UserID', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry!! Enter userID.'
																	);
																}
																else
																{	
																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'qr_code'=>base_url('WalletQrcode/index/'.$post['userID'])
																		);
																	}
																}

																echo json_encode($response);

															}


															public function getManualQr(){

																$response = array();


																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'qr_code'=>base_url('manualqrcode')
																);

																echo json_encode($response);

															}


															public function upiTopupAuth(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'UPI topup auth API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('txnid', 'Txn ID', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry!! Enter required parameter.'
																	);
																}
																else
																{	

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{


																		$userID = $post['userID'];

																		$chk_user_credential =$this->db->query("SELECT * FROM tbl_users WHERE (id = '$userID') and role_id = 2")->num_rows();

																		if(!$chk_user_credential)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! member not exists.'
																			);

																		}
																		else
																		{
																			$txnid = $post['txnid'];

							// check random id valid or not
																			$chk_txn_id = $this->db->get_where('member_upi_transaction',array('rrn'=>$txnid,'status'=>1,'is_used'=>0))->num_rows();
																			if(!$chk_txn_id)
																			{
																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! UPI RefferenceID is not valid.'
																				);
																			}
																			else{

								// check random id valid or not
																				$chk_txn_id = $this->db->get_where('member_upi_transaction',array('rrn'=>$txnid,'status'=>1,'is_used'=>0))->row_array();

																				$upi_amount = $chk_txn_id['amount'];


																				$before_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

																				$after_balance = $before_balance['wallet_balance'] + $upi_amount;    

																				$wallet_data = array(
																					'member_id'           => $userID,    
																					'before_balance'      => $before_balance['wallet_balance'],
																					'amount'              => $upi_amount,  
																					'after_balance'       => $after_balance,      
																					'status'              => 1,
																					'type'                => 1,      
																					'created'             => date('Y-m-d H:i:s'),      
																					'credited_by'         => $userID,
																					'description'         => 'UPI Topup Amount Credited #'.$txnid
																				);

																				$this->db->insert('member_wallet',$wallet_data);

																				$user_wallet = array(
																					'wallet_balance'=>$after_balance,        
																				);    

																				$this->db->where('id',$userID);
																				$this->db->update('users',$user_wallet); 


																				$this->db->where('rrn',$txnid);
																				$this->db->update('member_upi_transaction',array('member_id'=>$userID,'is_used'=>1));


																				$message = 'Congratulations!! you account credited with INR'.$upi_amount;

																				$this->User->sendNotification($userID,'Upi Topup',$message); 

																				$response = array(
																					'status' => 1,
																					'message' => 'Congratulations!! you account credited with INR'.$upi_amount
																				);
																			}
																		}

																	}
																}
																log_message('debug', 'UPI topup auth API Response - '.json_encode($response));	
																echo json_encode($response);

															}


															public function getUpiTransactionHistory(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'getUpiTransactionHistory API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
																$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter Valid Data.'
																	);
																}
																else
																{	

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$userID = $post['userID'];

					// check user valid or not
																		$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																		if($chk_user)
																		{

																			$sql = "SELECT a.*,b.name,b.user_code FROM tbl_member_upi_transaction as a LEFT JOIN tbl_users as b ON b.id = a.member_id where a.member_id = '$userID'";

																			$transaction = $this->db->query($sql)->result_array();

																			$data = array();
																			if($transaction)
																			{
																				foreach($transaction as $key=>$list){

																					$data[$key]['member_vpa_id'] = $list['member_vpa_id'];
																					$data[$key]['amount'] = 'Rs. '.$list['amount'];
																					$data[$key]['payer_name'] = $list['payer_name'];
																					$data[$key]['payer_vpa_id'] = $list['payer_vpa_id'];
																					$data[$key]['rrn'] = $list['rrn'];

																					if($list['status'] == 1)
																					{
																						$data[$key]['status'] = 'Success';
																					}	
																					else
																					{
																						$data[$key]['status'] = 'Failed';
																					}

																					if($list['is_used'] == 1){
																						$data[$key]['is_used'] = 'Used';
																						$data[$key]['used_by'] = $list['name'].' ('.$list['user_code'].')';
																					}
																					else{
																						$data[$key]['is_used'] = 'Not Yet';
																						$data[$key]['used_by'] = '';    
																					}

																					$data[$key]['date'] = date('d-M-Y H:i:s',strtotime($list['created']));


																				}

																				$response = array(
																					'status' => 1,
																					'message' => 'Success',
																					'data' => $data
																				);
																			}
																			else{

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! data not found.',
																				);
																			}


																		}
																		else
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! user not valid.'
																			);
																		}

																	}
																}
																log_message('debug', 'getUpiTransactionHistory API Response - '.json_encode($response));	
																echo json_encode($response);

															}


															public function dashboardDetail(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'dashboardDetail API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
																$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter UserID.'
																	);
																}
																else
																{	

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$userID = $post['userID'];

					// check user valid or not
																		$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																		if($chk_user)
																		{
						  // get wallet balance
																			$get_user_data = $this->db->select('wallet_balance,aeps_wallet_balance,user_code,name,created')->get_where('users',array('id'=>$userID))->row_array();
																			$premium_wallet_balance =  isset($get_user_data['wallet_balance']) ? ($get_user_data['wallet_balance']) ? $get_user_data['wallet_balance'] : 0 : 0 ;


																			$recharge_wallet_balance = isset($get_user_data['aeps_wallet_balance']) ? ($get_user_data['aeps_wallet_balance']) ? $get_user_data['aeps_wallet_balance'] : 0 : 0 ;

																			$member_current_package = $this->User->get_member_current_package($userID);

																			if($member_current_package != 2){

																				$membership = $this->User->get_user_membership_type($userID);

																			} else {

																				$membership = $this->User->get_user_membership_type($userID);
																			}

																			$refferal_link = base_url('register?referral_id=').$get_user_data['user_code'];


																			$sql = "SELECT a.*,c.name as sponser_name,c.user_code as sponser_code FROM tbl_users as a INNER JOIN tbl_member_tree as b ON a.id = b.member_id INNER JOIN tbl_users as c ON c.id = b.reffrel_id where  a.role_id = 2 and a.id = ".$userID." ";

																			$sponser = $this->db->query($sql)->row_array();

																			$get_package = $this->db->get_where('member_investment',array('member_id'=>$userID))->row_array();


																			$memberDetail = array(

																				'name'    => $get_user_data['name'],
																				'package'	=> isset($get_package['package_amount']) ? 'INR '.number_format($get_package['package_amount'],2).'/-' : 'Not Purchased',
																				'registration_date' => date('d M Y',strtotime($get_user_data['created'])),
																				'activate_date'  => isset($get_package['created']) ? date('d M Y',strtotime($get_package['created'])) : 'Not Activated',
																				'sponser_id'  => $sponser['sponser_code'],
																				'sponser_name'=> $sponser['sponser_name']
																			);


																			$response = array(
																				'status' => 1,
																				'message' => 'Success',
																				'premium_wallet_balance' => $premium_wallet_balance,
																				'recharge_wallet_balance' => $recharge_wallet_balance,
																				'membership' => $membership,
																				'refferal_link' => $refferal_link,
																				'memberDetail' => $memberDetail


																			);


																		}
																		else
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! user not valid.'
																			);
																		}

																	}
																}
																log_message('debug', 'dashboardDetail API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function getAepsStateList()
															{
		    // get state list
																$stateList = $this->db->order_by('state','asc')->get('aeps_state')->result_array(); 
																$data = array();
																if($stateList)
																{
																	foreach($stateList as $key=>$list)
																	{
																		$data[$key]['state_id'] = $list['id'];
																		$data[$key]['state_name'] = $list['state'];
																	}
																}
																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data'=>$data
																);
																echo json_encode($response);
															}


															public function getAepsCityList()
															{
																$post = $this->input->post();
																$state_id = isset($post['state_id']) ? $post['state_id'] : 0 ;
		    // get state name
																$get_state_name = $this->db->get_where('aeps_state',array('id'=>$state_id))->row_array();
																$state_name = isset($get_state_name['state']) ? $get_state_name['state'] : '';
		    // get state list
																// $cityList = $this->db->order_by('city_name','asc')->get_where('city',array('state_name'=>$state_name))->result_array();

																$cityList = $this->db->order_by('city_name','asc')->select('*')->from('city')->where("state_name LIKE '%$state_name%'")->get()->result_array();

																

																$data = array();
																if($cityList)
																{
																	foreach($cityList as $key=>$list)
																	{
																		$data[$key]['city_id'] = $list['city_id'];
																		$data[$key]['city_name'] = $list['city_name'];
																	}
																}
																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data'=>$data
																);
																echo json_encode($response);
															}


															public function aepsActiveAuth(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'AEPS Active Auth API Post Data - '.json_encode($post));   
																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
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
																$this->form_validation->set_rules('aadhar_photo', 'Aadhar Photo', 'required|xss_clean');
																$this->form_validation->set_rules('pancard_photo', 'Pancard Photo', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please enter required field'
																	);
																}
																else
																{	

																	


																		$userID = $post['user_id'];
			        // check user credential
																		$chk_user_credential =$this->db->get_where('users',array('id'=>$userID))->num_rows();
																		if(!$chk_user_credential)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'User Id Not Valid'
																			);

																		}
																		else
																		{   
																			$is_apes_active = 0;
																			$activeService = $this->User->account_active_service($userID);
																			if(in_array(3, $activeService)){
																				$is_apes_active = 1;
																			}


																			if(!$is_apes_active){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! AEPS not active.'
																				);

																			}
																			else{        
																				$user_aeps_status = $this->User->get_user_aeps_status($userID);

																				if($user_aeps_status)
																				{
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry ! AEPS already actived in your account.'
																					);
																				}
																				else
																				{
																					$aadhar_photo = '';
																					$encodedData = $post['aadhar_photo'];
																					if(strpos($post['aadhar_photo'], ' ')){
																						$encodedData = str_replace(' ','+', $post['aadhar_photo']);
																					}
																					$profile = base64_decode($encodedData);
																					if($profile)
																					{
																						$file_name = time().rand(1111,9999).'.jpg';
																						$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
																						$path = 'media/aeps_kyc_doc/';
																						$targetDir = $path.$file_name;
																						if(file_put_contents($targetDir, $profile)){
																							$aadhar_photo = $targetDir;
																						}
																					}


																					$pancard_photo = '';
																					$encodedData = $post['pancard_photo'];
																					if(strpos($post['pancard_photo'], ' ')){
																						$encodedData = str_replace(' ','+', $post['pancard_photo']);
																					}
																					$profile = base64_decode($encodedData);
																					if($profile)
																					{
																						$file_name = time().rand(1111,9999).'.jpg';
																						$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
																						$path = 'media/aeps_kyc_doc/';
																						$targetDir = $path.$file_name;
																						if(file_put_contents($targetDir, $profile)){
																							$pancard_photo = $targetDir;
																						}
																					}

																					$api_response = $this->Aeps_model->activeAEPSMember($post,$aadhar_photo,$pancard_photo,$userID);
																					$status = $api_response['status'];

																					if($status == 1)
																					{
																						$encodeFPTxnId = $api_response['encodeFPTxnId'];
																						$response = array(
																							'status' => 1,
																							'message' => 'We have sent OTP on your registered mobile, please verfiy.',
																							'encodeFPTxnId' => $encodeFPTxnId
																						);
																					}
																					else
																					{
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Activation failed due to '.$api_response['msg']
																						);
																					}

																				}
																			}

																		}

																	
																}
																log_message('debug', 'AEPS Active Auth API Response - '.json_encode($response));    
																echo json_encode($response);

															}



															public function aepsOtpAuth(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'AEPS OTP Auth API Post Data - '.json_encode($post));  
																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
																$this->form_validation->set_rules('encodeFPTxnId', 'Txn ID', 'required|xss_clean');
																$this->form_validation->set_rules('otp_code', 'OTP Code', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please enter required field'
																	);
																}
																else
																{	
																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$userID = $post['user_id'];
																		$encodeFPTxnId = $post['encodeFPTxnId'];
			        // check user credential
																		$chk_user_credential =$this->db->get_where('users',array('id'=>$userID))->num_rows();
																		if(!$chk_user_credential)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'User Id Not Valid'
																			);

																		}
																		else
																		{   

																			$is_apes_active = 0;
																			$activeService = $this->User->account_active_service($userID);
																			if(in_array(3, $activeService)){
																				$is_apes_active = 1;
																			}


																			if(!$is_apes_active){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! AEPS not active.'
																				);

																			}
																			else{

																				$user_aeps_status = $this->User->get_user_aeps_status($userID);
																				if($user_aeps_status)
																				{
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry ! AEPS already actived in your account.'
																					);
																				}
																				else
																				{
			                        // check already kyc approved or not
																					$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
																					if(!$chk_encode_id)
																					{
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Encoded Transaction ID not valid.'
																						);
																					}
																					else
																					{
																						$api_response = $this->Aeps_model->aepsOTPAuth($post,$userID,$encodeFPTxnId);
																						$status = $api_response['status'];

																						if($status == 1)
																						{	
																							$message = 'Congratulations ! OTP Verified successfully.';

																							$this->User->sendNotification($userID,'AEPS',$message);

																							$response = array(
																								'status' => 1,
																								'message' => 'Congratulations ! OTP Verified successfully. Please capture your finger print.',
																								'encodeFPTxnId' => $encodeFPTxnId
																							);
																						}
																						else
																						{
																							$response = array(
																								'status' => 0,
																								'message' => $api_response['msg']
																							);
																						}
																					}
																				}
																			}
																		}

																	}
																}
																log_message('debug', 'AEPS OTP Auth API Response - '.json_encode($response));   
																echo json_encode($response);

															}



															public function aepsResendOtpAuth(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'AEPS Resend OTP Auth API Post Data - '.json_encode($post));   
																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
																$this->form_validation->set_rules('encodeFPTxnId', 'Txn ID', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please enter required field'
																	);
																}
																else
																{	

																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$userID = $post['user_id'];
																		$encodeFPTxnId = $post['encodeFPTxnId'];
			        // check user credential
																		$chk_user_credential =$this->db->get_where('users',array('id'=>$userID))->num_rows();
																		if(!$chk_user_credential)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'User Id Not Valid'
																			);

																		}
																		else
																		{   
																			$is_apes_active = 0;
																			$activeService = $this->User->account_active_service($userID);
																			if(in_array(3, $activeService)){
																				$is_apes_active = 1;
																			}


																			if(!$is_apes_active){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! AEPS not active.'
																				);

																			}
																			else{
																				$user_aeps_status = $this->User->get_user_aeps_status($userID);

																				if($user_aeps_status)
																				{
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry ! AEPS already actived in your account.'
																					);
																				}
																				else
																				{
			                        // check already kyc approved or not
																					$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
																					if(!$chk_encode_id)
																					{
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Encoded Transaction ID not valid.'
																						);
																					}
																					else
																					{
																						$api_response = $this->Aeps_model->aepsResendOtp($userID,$encodeFPTxnId);
																						$status = $api_response['status'];

																						if($status == 1)
																						{
																							$response = array(
																								'status' => 1,
																								'message' => 'OTP Resent successfully, Please verify.',
																								'encodeFPTxnId' => $encodeFPTxnId
																							);
																						}
																						else
																						{
																							$response = array(
																								'status' => 0,
																								'message' => $api_response['msg']
																							);
																						}
																					}
																				}
																			}
																		}

																	}
																}
																log_message('debug', 'AEPS Resend OTP Auth API Response - '.json_encode($response));    
																echo json_encode($response);

															}


															public function aepsKycBioAuth(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'AEPS KYC Bio Auth API Post Data - '.json_encode($post));  
																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
																$this->form_validation->set_rules('encodeFPTxnId', 'Txn ID', 'required|xss_clean');
																$this->form_validation->set_rules('BiometricData', 'BiometricData', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please enter required field'
																	);
																}
																else
																{	
																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{
																		$userID = $post['user_id'];
																		$encodeFPTxnId = $post['encodeFPTxnId'];
																		$biometricData = $post['BiometricData'];
																		$iin = '';
																		$requestTime = date('Y-m-d H:i:s');
																		$txnID = 'FIAK'.time();
			        // check user credential
																		$chk_user_credential =$this->db->get_where('users',array('id'=>$userID))->num_rows();
																		if(!$chk_user_credential)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'User Id Not Valid'
																			);

																		}
																		else
																		{   

																			$is_apes_active = 0;
																			$activeService = $this->User->account_active_service($userID);
																			if(in_array(3, $activeService)){
																				$is_apes_active = 1;
																			}


																			if(!$is_apes_active){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! AEPS not active.'
																				);

																			}
																			else{

																				$user_aeps_status = $this->User->get_user_aeps_status($userID);

																				if($user_aeps_status)
																				{
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry ! AEPS already actived in your account.'
																					);
																				}
																				else
																				{
			                        // check already kyc approved or not
																					$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
																					if(!$chk_encode_id)
																					{
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Encoded Transaction ID not valid.'
																						);
																					}
																					else
																					{
																						$memberData = $this->db->get_where('users',array('id'=>$userID))->row_array();
																						$member_code = $memberData['user_code'];

			                            // check already kyc approved or not
																						$get_kyc_data = $this->db->get_where('aeps_member_kyc',array('member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->row_array();
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
																							'username: '.RECHARGE_MEMBERID,
																							'password: '.RECHARGE_API_PWD,
																							'Content-Type:text/xml'
																						];

																						curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
																						$output = curl_exec ($ch);
																						curl_close ($ch);

																						$responseData = json_decode($output,true);
																						$finalResponse = isset($responseData['message']) ? json_decode($responseData['message'],true) : array();

																						$apiData = array(
																							'user_id' => $userID,
																							'api_url' => $api_url,
																							'api_response' => $output,
																							'post_data' => $biometricData,
																							'created' => date('Y-m-d H:i:s'),
																							'created_by' => $userID
																						);
																						$this->db->insert('aeps_api_response',$apiData);

																						if(isset($finalResponse['status']) && $finalResponse['status'] == 1)
																						{
			                                // update aeps status
																							$this->db->where('id',$userID);
																							$this->db->update('users',array('aeps_status'=>1));

			                                // update aeps status
																							$this->db->where('id',$recordID);
																							$this->db->update('aeps_member_kyc',array('status'=>1,'clear_step'=>5));

																							$message = 'Congratulation ! Your EKYC has been approved.';

																							$this->User->sendNotification($userID,'AEPS',$message);

																							$response = array(
																								'status' => 1,
																								'message' => 'Congratulation ! Your EKYC has been approved.'
																							);
																						}
																						else
																						{
																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry ! Your BiometricData not valid.'
																							);
																						}
																					}
																				}
																			}
																		}

																	}
																}
																log_message('debug', 'AEPS KYC Bio Auth API Response - '.json_encode($response));   
																echo json_encode($response);

															}


															public function getAepsBankList()
															{
		    // get state list
																$bankList = $this->db->get('aeps_bank_list')->result_array();

																$data = array();
																if($bankList)
																{
																	foreach($bankList as $key=>$list)
																	{
																		$data[$key]['iinno'] = $list['iinno'];
																		$data[$key]['bank_name'] = $list['bank_name'];
																	}
																}
																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data'=>$data
																);
																echo json_encode($response);
															}



															public function aepsApiAuth()
															{   
		    //$post = file_get_contents('php://input');
		    //$post = json_decode($post, true);
																$request = $_REQUEST['user_data'];
																$post =  json_decode($request,true);

																log_message('debug', 'AEPS api Auth API Post Data - '.json_encode($post));

																


																	$memberID = $post['userID'];
																	$loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
																	if(!$loggedUser){

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry ! user not valid.'
																		);  
																	}
																	else{

																		$agentID = $loggedUser['user_code'];
																		$is_apes_active = 0;
																		$activeService = $this->User->account_active_service($memberID);
																		if(in_array(3, $activeService)){
																			$is_apes_active = 1;
																		}


																		if(!$is_apes_active){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! AEPS not active.'
																			);

																		}
																		else{
																			$user_aeps_status = $this->User->get_user_aeps_status($memberID);
																			$response = array();
																			if($user_aeps_status)
																			{
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
																								curl_setopt($ch, CURLOPT_POST, true);
																								curl_setopt($ch, CURLOPT_POSTFIELDS,$biometricData);
																								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

																								$headers = [
																									'username: '.RECHARGE_MEMBERID,
																									'password: '.RECHARGE_API_PWD,
																									'Content-Type:text/xml'
																								];

																								curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
																								$output = curl_exec ($ch);
																								curl_close ($ch);

																								$responseData = json_decode($output,true);

																								$apiData = array(
																									'user_id' => $memberID,
																									'api_url' => $api_url,
																									'post_data'=> json_encode($post),
																									'api_response' => $output,
																									'created' => date('Y-m-d H:i:s'),
																									'created_by' => $memberID
																								);
																								$this->db->insert('aeps_api_response',$apiData);

																								if(isset($responseData['message']) && $responseData['message'] == 'Request Completed')
																								{
																									$this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$memberID);
																									$str = '';
																									if($is_bal_info == 0)
																									{
																										$this->Aeps_model->addStatementCom($txnID,$aadharNumber,$iin,$amount,$memberID);
																										$statementList = $responseData['data']['miniStatementStructureModel'];
																										if($statementList)
																										{
																											$str = array();
																											foreach($statementList as $key => $list)
																											{   
																												$str[$key]['date'] = $list['date'];
																												if($list['txnType'] == 'Dr'){

																													$str[$key]['txnType'] = 'DR';
																												}
																												else{

																													$str[$key]['txnType'] = 'CR';
																												}
																												$str[$key]['amount'] = $list['amount'];
																												$str[$key]['narration'] = $list['narration'];

																											}
																										}
																									}
																									$response = array(
																										'status' => 1,
																										'message' => $responseData['message'],
																										'balanceAmount' => $responseData['data']['balanceAmount'],
																										'bankRRN' => $responseData['data']['bankRRN'],
																										'is_bal_info' => $is_bal_info,
																										'is_withdrawal' => $is_withdrawal,
																										'invoiceUrl' => '',
																										'str' => $str
																									);


																								}
																								else
																								{
																									$this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3,$memberID);
																									$response = array(
																										'status' => 0,
																										'message' => $responseData['message'],
																									);
																								}



																							}
																							else
																							{
																								$response = array(
																									'status' => 0,
																									'message' => 'Sorry ! Amount is not valid.'
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
																									'username: '.RECHARGE_MEMBERID,
																									'password: '.RECHARGE_API_PWD,
																									'Content-Type:text/xml'
																								];

																								curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
																								$output = curl_exec ($ch);
																								curl_close ($ch);

																								$responseData = json_decode($output,true);

																								$apiData = array(
																									'user_id' => $memberID,
																									'api_url' => $api_url,
																									'post_data'=> json_encode($post),
																									'api_response' => $output,
																									'created' => date('Y-m-d H:i:s'),
																									'created_by' => $memberID
																								);
																								$this->db->insert('aeps_api_response',$apiData);

																								if(isset($responseData['message']) && $responseData['message'] == 'Request Completed')
																								{
																									$this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$memberID);
																									$this->Aeps_model->addBalance($txnID,$aadharNumber,$iin,$amount,$memberID);
																									$str = array(
																										'txnStatus'    => 'Successfull',
																										'amount'       => $responseData['data']['transactionAmount'],
																										'balanceAmount'=> $responseData['data']['balanceAmount'],
																										'bankRRN'      => $responseData['data']['bankRRN']    
																									);


																									$response = array(
																										'status' => 1,
																										'message' => $responseData['message'],
																										'balanceAmount' => $responseData['data']['balanceAmount'],
																										'bankRRN' => $responseData['data']['bankRRN'],
																										'is_bal_info' => $is_bal_info,
																										'is_withdrawal' => $is_withdrawal,
																										'invoiceUrl' => '',
																										'str' => $str
																									);


																								}
																								else
																								{
																									$this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3,$memberID);
																									$response = array(
																										'status' => 0,
																										'message' => $responseData['message'],
																									);
																								}



																							}
																							else
																							{
																								$response = array(
																									'status' => 0,
																									'message' => 'Sorry ! Amount should be less than 10000 and grater than or equal 101.'
																								);
																							}
																						}
																						else
																						{
																							$response = array(
																								'status' => 0,
																								'message' => 'Something Wrong ! Please Try Again Later.'
																							);      
																						}
																					}
																					else
																					{
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Please enter required data.'
																						);      
																					}

																				}
																				else
																				{
																					$response = array(
																						'status' => 0,
																						'message' => 'Something Wrong ! Please Try Again Later.'
																					);
																				}
																			}
																		}
																	
																}

																log_message('debug', 'AEPS api Auth API Response - '.json_encode($response));
																echo json_encode($response);
															}


															public function getAepsHistory()
															{
																$post = $this->input->post();
																log_message('debug', 'AEPS History API Post Data - '.json_encode($post));

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$userID = isset($post['userID']) ? $post['userID'] : 0;


																	$response = array();
																	$fromDate = $post['fromDate'];
																	$toDate =   $post['toDate'];
																	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																	$limit = $page_no * 50;

			    // check user valid or not
																	$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																	if($chk_user)
																	{
																		$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.member_id = '$userID'";
																		if($fromDate && $toDate)
																		{
																			$sql.=" AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."'";

																			$sql.=" ORDER BY a.created DESC";

																			$count = $this->db->query($sql)->num_rows();

																			$limit_start = $limit - 50; 

																			$limit_end = 50;

																			$sql.=" LIMIT ".$limit_start." ,".$limit_end."";

																		}
																		else{

																			$sql.=" ORDER BY a.created DESC";

																			$count = $this->db->query($sql)->num_rows();

																			$limit_start = $limit - 50; 

																			$limit_end = 50;

																			$sql.=" LIMIT ".$limit_start." ,".$limit_end."";
																		}

																		$historyList = $this->db->query($sql)->result_array();

																		$pages = ceil($count / 50);

																		$data = array();
																		if($historyList)
																		{
																			foreach ($historyList as $key => $list) {


																				$data[$key]['member_code'] = $list['member_code'];
																				$data[$key]['member_name'] = $list['member_name'];
																				if($list['service'] == 'balwithdraw' || $list['service'] == 'aadharpay')
																				{
																					$data[$key]['service'] = 'Account Withdrawal';
																				}
																				elseif($list['service'] == 'balinfo')
																				{
																					$data[$key]['service'] = 'Balance Info';
																				}
																				elseif($list['service'] == 'ministatement')
																				{
																					$data[$key]['service'] = 'Mini Statement';
																				}
																				$data[$key]['aadhar_no'] = $list['aadhar_no'];
																				$data[$key]['mobile'] = $list['mobile'];
																				$data[$key]['amount'] = $list['amount'];
																				$data[$key]['txnID'] = $list['txnID'];
																				if($list['status'] == 2) {
																					$data[$key]['status'] = 'Success';
																				}
																				elseif($list['status'] == 3) {
																					$data[$key]['status'] = 'Failed';
																				}
																				else{
																					$data[$key]['status'] = 'Pending';
																				}
																				$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));

																			}
																		}

																		if($data)
																		{
																			$response = array(
																				'status' => 1,
																				'message' => 'Success',
																				'data' => $data,
																				'pages' => $pages,
																			);  
																		}
																		else
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry ! No Record Found.',
																			);  
																		}
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry ! Member not valid.'
																		);
																	}
																}
																log_message('debug', 'AEPS History API Response - '.json_encode($response));    
																echo json_encode($response);
															}




															public function getBbpsServiceList(){

																$response = array();

																if(IS_MOBIKWIK_ACTIVE == 1)
																{

																	$countryList = $this->db->order_by('title','desc')->get('mobikwik_bbps_service')->result_array();
																}
																else
																{
																	$countryList = $this->db->order_by('title','desc')->get('bbps_service')->result_array();

																}
																
																$data = array();
																if($countryList)
																{
																	foreach($countryList as $key=>$list)
																	{
																		$data[$key]['service_id'] = $list['id'];
																		$data[$key]['title'] = $list['title'];
																	}
																}
																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data'=>$data
																);
																echo json_encode($response);

															}

				// 											public function getBbpsElectricityOperator()
				// 											{
				// 												log_message('debug', 'Get BBPS Electricity Operator List API.');	

		  //  // get electricity biller list
				// 												$electricityBillerList = $this->User->get_bbps_biller_list(4);

				// 												$data = array();

				// 												if($electricityBillerList)
				// 												{
				// 													foreach($electricityBillerList as $key=>$list)
				// 													{
				// 														$is_fetch = 0;
				// 														$fetchOption = $list['fetchOption'];
				// 														if($fetchOption == 'MANDATORY')
				// 														{
				// 															$is_fetch = 1;
				// 														}

				// 														$data[$key]['biller_id'] = $list['biller_id'];
				// 														$data[$key]['billerName'] = $list['billerName'];
				// 														$data[$key]['billerAliasName'] = $list['billerAliasName'];
				// 														$data[$key]['is_fetch'] = $is_fetch;
				// 													}
				// 												}

				// 												$response = array(
				// 													'status' => 1,
				// 													'message' => 'Success',
				// 													'data'=>$data
				// 												);

				// 												log_message('debug', 'Get BBPS Electricity Operator List API Response - '.json_encode($response).'.');	

				// 												echo json_encode($response,JSON_NUMERIC_CHECK);

				// 											}
				
				
				                        	public function getBbpsElectricityOperator()
															{
																log_message('debug', 'Get BBPS Electricity Operator List API.');	

		    // get electricity biller list(varname)				
																if(IS_MOBIKWIK_ACTIVE == 1)
																{
																	$electricityBillerList = $this->User->get_mobikwik_bbps_biller_list(4);
																}
																else
																{
																	$electricityBillerList = $this->User->get_bbps_biller_list(4);	
																}
																

																$data = array();

																if($electricityBillerList)
																{
																	foreach($electricityBillerList as $key=>$list)
																	{
																		$is_fetch = 0;
																		$fetchOption = $list['fetchOption'];
																		if($fetchOption == 'MANDATORY')
																		{
																			$is_fetch = 1;
																		}

																		$data[$key]['biller_id'] = $list['biller_id'];
																		$data[$key]['billerName'] = $list['billerName'];
																		$data[$key]['billerAliasName'] = $list['billerAliasName'];
																		$data[$key]['is_fetch'] = $is_fetch;
																	}
																}

																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data'=>$data
																);

																log_message('debug', 'Get BBPS Electricity Operator List API Response - '.json_encode($response).'.');	

																echo json_encode($response,JSON_NUMERIC_CHECK);

															}


															public function getBbpsElectricityFormParams()
															{
																$service_id = 4;
																log_message('debug', 'Get BBPS Electricity Operator Form API Called.');	

																$get = $this->input->post();

																log_message('debug', 'Get BBPS Electricity Operator Form API Get Data - '.json_encode($get).'.');	


																$biller_id = isset($get['biller_id']) ? $get['biller_id'] : '';



		    // get biller system id

																	if(IS_MOBIKWIK_ACTIVE == 1)
																	{
																		$get_biller_id = $this->db->get_where('mobikwik_bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
																		
																$billerID = isset($get_biller_id['id']) ? $get_biller_id['id'] : 0 ;

																	}
																	else
																	{
																		$get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
																$billerID = isset($get_biller_id['id']) ? $get_biller_id['id'] : 0 ;

																	}
																

																if($billerID)
																{
		        // get biller Params1
																	if(IS_MOBIKWIK_ACTIVE ==1)
																	{
																		$billerParams = $this->User->get_mobikwik_bbps_biller_param($service_id,$billerID);

																	}
																	else
																	{
																		$billerParams = $this->User->get_bbps_biller_param($service_id,$billerID);	
																	}
																	

																	$data = array();

																	if($billerParams)
																	{
																		foreach($billerParams as $key=>$list)
																		{
																			$data[$key]['fieldKey'] = 'para'.($key+1);
																			$data[$key]['paramName'] = $list['paramName'];
																			$data[$key]['datatype'] = $list['datatype'];
																			$data[$key]['minlength'] = $list['minlength'];
																			$data[$key]['maxlength'] = $list['maxlength'];
																			$data[$key]['optional'] = $list['optional'];
																		}
																	}

																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data'=>$data
																	);
																}
																else
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry ! Biller not found.'
																	);
																}

																log_message('debug', 'Get BBPS Electricity Form List API Response - '.json_encode($response).'.');	

																echo json_encode($response,JSON_NUMERIC_CHECK);

															} 

																public function electricityBillFetchAuth()
															{
			// 400 - Means Variable realted error
			// 401 - Variable Data not valid
			// 200 - Success
			// response type 1 = JSON, 2 = XML
			// save system log
																log_message('debug', 'Get BBPS Electricity Fetch Bill API Called.');

																$post = $this->input->post();
		    // save system log


																log_message('debug', 'Get BBPS Electricity Fetch Bill API Post Data - '.json_encode($post).'.');

																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required');
																$this->form_validation->set_rules('biller_id', 'Biller ID', 'required');
																$this->form_validation->set_rules('para1', 'Params1', 'required');
																if ($this->form_validation->run() == FALSE) {
		    	// save system log
																	log_message('debug', 'Get BBPS Electricity Fetch Bill API Parameter not valid error.');
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry ! Parameter are not valid.'
																	);
																}
																else
																{	

																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{


																		$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
																		$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;

			    	// check user valid or not
																		$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID))->num_rows();
																		if($chk_user)
																		{
			    		//get logged user info
																			$activeService = $this->User->account_active_service($loggedAccountID);
																			if(!in_array(4, $activeService)){
				            // save system log
																				log_message('debug', 'Get BBPS Electricity Fetch Bill API Service not active.');
																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! This service is not active in your account.'
																				);
																			}
																			else
																			{
																				$service_id = 4;
					        // get biller system id
																				if(IS_MOBIKWIK_ACTIVE == 1)
																				{
																					$get_biller_id = $this->db->get_where('mobikwik_bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
																				}
																				else
																				{
																					$get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();

																				}
																				
																				$biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
																				$service_id = isset($get_biller_id['service_id']) ? $get_biller_id['service_id'] : '';

					        // get pmr service id
																				
																				$get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
																				$pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

																				$postData = array();
																				$postData['params'][0] = isset($post['para1']) ? $post['para1'] : '';
																				$postData['params'][1] = isset($post['para2']) ? $post['para2'] : '';
																				$postData['params'][2] = isset($post['para3']) ? $post['para3'] : '';
																				$postData['params'][3] = isset($post['para4']) ? $post['para4'] : '';
																				$postData['params'][4] = isset($post['para5']) ? $post['para5'] : '';


																				if(IS_MOBIKWIK_ACTIVE == 1)
																				{

																					
																					$bill_fetch_respone = $this->User->call_mobikwik_bbps_electricity_bill_fetch_api($loggedAccountID,$biller_payu_id,$pmr_service_id,$postData);

																				}
																				else
																				{

																				$bill_fetch_respone = $this->User->call_bbps_electricity_bill_fetch_api($loggedAccountID,$biller_payu_id,$pmr_service_id,$postData);

																				}
																				
																				if($bill_fetch_respone['status'] == 1)
																				{
																					$response = array(
																						'status' => 1,
																						'message' => 'Success',
																						'amount' => $bill_fetch_respone['amount'],
																						'accountHolderName' => $bill_fetch_respone['accountHolderName']
																					);
																				}
																				else
																				{
																					$response = array(
																						'status' => 1,
																						'message' => 'Success',
																						'amount' => 0,
																						'accountHolderName' => ''
																					);
																				}
																			}
																		}
																		else
																		{
				    	// save system log
																			log_message('debug', 'Get BBPS Electricity Fetch Bill API Member not valid error.');
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry ! You are not authorized to access this service.'
																			);
																		}


																	}
																}




																echo json_encode($response,JSON_NUMERIC_CHECK);

															}


																public function electricityBillPayAuth()
															{
			// 400 - Means Variable realted error
			// 401 - Variable Data not valid
			// 200 - Success
			// response type 1 = JSON, 2 = XML
			// save system log
																log_message('debug', 'Get BBPS Electricity Pay Bill API Called.');

																$post = $this->input->post();
																log_message('debug', 'Get BBPS Electricity Pay Bill API Post Data - '.json_encode($post).'.');


																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required');
																$this->form_validation->set_rules('biller_id', 'Biller ID', 'required');
																$this->form_validation->set_rules('para1', 'Params1', 'required');
																$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
																$this->form_validation->set_rules('txn_pass', 'Txn Password', 'required');
																if ($this->form_validation->run() == FALSE) {
		    	// save system log
																	log_message('debug', 'Get BBPS Electricity Pay Bill API Parameter not valid error.');
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry ! Parameter are not valid.'
																	);
																}
																else
																{	

																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$txn_pass = isset($post['txn_pass']) ? $post['txn_pass'] : '';

																		if(do_hash($txn_pass) != $userData['transaction_password']){

																			$response = array(
																				'status' => 0,
																				'message' => 'Txn Password wrong.'
																			);

																		}
																		else{

																			$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
																			$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;

				    	// check user valid or not
																			$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID))->num_rows();
																			if($chk_user)
																			{
				    		//get logged user info
																				$activeService = $this->User->account_active_service($loggedAccountID);
																				if(!in_array(4, $activeService)){
					            // save system log
																					log_message('debug', 'Get BBPS Electricity Pay Bill API Service not active.');
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry ! This service is not active in your account.'
																					);
																				}
																				else
																				{
					        	// check member id and password
																					$getAccountData = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
																					$before_wallet_balance = $getAccountData['wallet_balance'];
																					$min_wallet_balance = $getAccountData['min_wallet_balance'];
																					$memberName = $getAccountData['name'];
																					$memberMobile = $getAccountData['mobile'];
																					$memberEmail = $getAccountData['email'];
																					$memberCode = $getAccountData['user_code'];
																					
																					if($getAccountData['is_main_wallet_block'] == 1){
					        		// save system log
																					//	log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Your fund is blocked for 2 Days.'
																						);
																					}
																					
																					elseif($before_wallet_balance < $post['amount']){
					        		// save system log
																						log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Insufficient balance in your account.'
																						);
																					}
																					else
																					{
																						$api_response = $this->Bbps_model->bbpsElectricityAuth($post,$loggedAccountID,$memberCode);
						        	// save system log
																						log_message('debug', 'Get BBPS Electricity Pay Bill API Response - '.json_encode($api_response).'.');

																						if($api_response['status'] == 0)
																						{
																							$response = array(
																								'status' => 1,
																								'message' => 'Sorry ! Your bill payment got failed.',
																								'status' => 'FAILED',
																								'optMsg' => $api_response['msg'],
																								'txnID' => $post['txnID'],
																								'optTxnID' => $api_response['txnid']
																							);
																						}
																						elseif($api_response['status'] == 1)
																						{	

																							$message = 'Congratulations ! Your Bill Payment credited successfully.';

																							$this->User->sendNotification($loggedAccountID,'BBPS',$message);

																							$response = array(
																								'status' => 1,
																								'message' => 'Congratulations ! Your Bill Payment credited successfully.',
																								'status' => 'SUCCESS',
																								'optMsg' => $api_response['msg'],
																								'txnID' => $post['txnID'],
																								'optTxnID' => $api_response['txnid']
																							);
																						}
																						elseif($api_response['status'] == 2)
																						{
																							$response = array(
																								'status' => 1,
																								'message' => 'Your Bill Payment is under processing, status will be updated soon.',
																								'status' => 'PENDING',
																								'optMsg' => $api_response['msg'],
																								'txnID' => $post['txnID'],
																								'optTxnID' => $api_response['txnid']
																							);
																						}
																					}
																				}
																			}
																			else
																			{
					    	// save system log
																				log_message('debug', 'Get BBPS Electricity Pay Bill API Member not valid error.');
																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! You are not authorized to access this service.'
																				);
																			}


																		}
																	}
																}
																log_message('debug', 'Get BBPS Electricity Pay Bill API Final Response - '.json_encode($response).'.');
																echo json_encode($response,JSON_NUMERIC_CHECK);

															}

															public function getBbpsFastagOperator()
															{
																log_message('debug', 'Get BBPS Fastag Operator List API.');	

		    // get electricity biller list
																if(IS_MOBIKWIK_ACTIVE == 1)
																{
																	

																	$electricityBillerList = $this->User->get_mobikwik_bbps_biller_list(12); 
																	

																}
																else
																{
																	$electricityBillerList = $this->User->get_bbps_biller_list(6);
																}
																

																$data = array();

																if($electricityBillerList)
																{
																	foreach($electricityBillerList as $key=>$list)
																	{
																		$is_fetch = 0;
																		$fetchOption = $list['fetchOption'];
																		if($fetchOption == 'MANDATORY')
																		{
																			$is_fetch = 1;
																		}

																		$data[$key]['biller_id'] = $list['id'];
																		$data[$key]['billerName'] = $list['billerName'];
																		$data[$key]['billerAliasName'] = $list['billerAliasName'];
																		$data[$key]['is_fetch'] = $is_fetch;
																	}
																}

																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data'=>$data
																);

																log_message('debug', 'Get BBPS Fastag Operator List API Response - '.json_encode($response).'.');	

																echo json_encode($response,JSON_NUMERIC_CHECK);

															}


															public function getBbpsFastagFormParams()
															{
																if(IS_MOBIKWIK_ACTIVE == 1)
																{
																	$service_id = 12;
																}
																else
																{
																	$service_id = 6;	
																}
																
																log_message('debug', 'Get BBPS Fastag Operator Form API Called.');	

																$get = $this->input->post();

																log_message('debug', 'Get BBPS Fastag Operator Form API Get Data - '.json_encode($get).'.');	

																$biller_id = isset($get['biller_id']) ? $get['biller_id'] : '';

		    // get biller system id
																if(IS_MOBIKWIK_ACTIVE == 1)
																{
																	$get_biller_id = $this->db->get_where('mobikwik_bbps_service_category',array('service_id'=>$service_id,'id'=>$biller_id))->row_array();


																}
																else
																{
																	$get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
																}
																


																$billerID = isset($get_biller_id['id']) ? $get_biller_id['id'] : 0 ;

																if($billerID)
																{
		        // get biller params
																		if(IS_MOBIKWIK_ACTIVE == 1)
																		{

																			$billerParams = $this->User->get_mobikwik_bbps_biller_param($service_id,$billerID);

																		}
																		else
																		{
																			$billerParams = $this->User->get_bbps_biller_param($service_id,$billerID);

																		}
																		
																	$data = array();

																	if($billerParams)
																	{
																		foreach($billerParams as $key=>$list)
																		{
																			$data[$key]['fieldKey'] = 'para'.($key+1);
																			$data[$key]['paramName'] = $list['paramName'];
																			$data[$key]['datatype'] = $list['datatype'];
																			$data[$key]['minlength'] = $list['minlength'];
																			$data[$key]['maxlength'] = $list['maxlength'];
																			$data[$key]['optional'] = $list['optional'];
																		}
																	}

																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data'=>$data
																	);
																}
																else
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry ! Biller not found.'
																	);
																}

																log_message('debug', 'Get BBPS Fastag Form List API Response - '.json_encode($response).'.');	

																echo json_encode($response,JSON_NUMERIC_CHECK);

															} 
															
																public function fastagBillFetchAuth()
															{
			// 400 - Means Variable realted error
			// 401 - Variable Data not valid
			// 200 - Success
			// response type 1 = JSON, 2 = XML
			// save system log
																log_message('debug', 'Get BBPS Fastag Fetch Bill API Called.');
			//check for foem validation
																$post = $this->input->post();
		    // save system log
																log_message('debug', 'Get BBPS Fastag Fetch Bill API Post Data - '.json_encode($post).'.');

																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required');
																$this->form_validation->set_rules('biller_id', 'Biller ID', 'required');
																$this->form_validation->set_rules('para1', 'Params1', 'required');

																if ($this->form_validation->run() == FALSE) {
		    	// save system log
																	log_message('debug', 'Get BBPS Fastag Fetch Bill API Parameter not valid error.');
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry ! Parameter are not valid.'
																	);
																}
																else
																{	

																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{


																		$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
																		$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;

			    	// check user valid or not
																		$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID))->num_rows();
																		if($chk_user)
																		{
			    		//get logged user info
																			$activeService = $this->User->account_active_service($loggedAccountID);
																			if(!in_array(4, $activeService)){
				            // save system log
																				log_message('debug', 'Get BBPS Fastag Fetch Bill API Service not active.');
																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! This service is not active in your account.'
																				);
																			}
																			else
																			{
																				if(IS_MOBIKWIK_ACTIVE == 1)
																				{

																					$service_id = 12;
					        // get biller system id
																				$get_biller_id = $this->db->get_where('mobikwik_bbps_service_category',array('service_id'=>$service_id,'id'=>$biller_id))->row_array();

																				}
																				else
																				{
																					$service_id = 6;
					        // get biller system id
																				$get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();

																				}
																				
																				$biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
																				$biller_fastag_id = isset($get_biller_id['id']) ? $get_biller_id['id'] : '';
																				$service_id = isset($get_biller_id['service_id']) ? $get_biller_id['service_id'] : '';

					        // get pmr service id
																				if(IS_MOBIKWIK_ACTIVE == 1)
																				{
																					$pmr_service_id = 12;
																				}
																				else
																				{
																					$get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
																				$pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;
																				}
																				

																				if($pmr_service_id == 32)
																				{
																					$postData = array();
																					$postData['number'] = isset($post['para1']) ? $post['para1'] : '';

																				}
																				else
																				{
																					$postData = array();
																					$postData['params'][0] = isset($post['para1']) ? $post['para1'] : '';
																					$postData['params'][1] = isset($post['para2']) ? $post['para2'] : '';
																					$postData['params'][2] = isset($post['para3']) ? $post['para3'] : '';
																					$postData['params'][3] = isset($post['para4']) ? $post['para4'] : '';
																					$postData['params'][4] = isset($post['para5']) ? $post['para5'] : '';
																					$postData['billerID'] = $biller_fastag_id;
																				}

																				if(IS_MOBIKWIK_ACTIVE == 1)

																				{
																					$bill_fetch_respone = $this->User->call_mobikwik_bbps_electricity_bill_fetch_api($loggedAccountID,$biller_payu_id,$pmr_service_id,$postData);
																				}
																				else
																				{
																					$bill_fetch_respone = $this->User->call_bbps_service_bill_fetch_api($loggedAccountID,$biller_payu_id,$pmr_service_id,$postData);
																				}


																				
																				if($bill_fetch_respone['status'] == 1)
																				{
																					$response = array(
																						'status' => 1,
																						'message' => 'Success',
																						'amount' => $bill_fetch_respone['amount'],
																						'accountHolderName' => $bill_fetch_respone['accountHolderName']
																					);
																				}
																				else
																				{
																					$response = array(
																						'status' => 1,
																						'message' => 'Success'
																					);
																				}
																			}
																		}
																		else
																		{
				    	// save system log
																			log_message('debug', 'Get BBPS Fastag Fetch Bill API Member not valid error.');
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry ! You are not authorized to access this service.'
																			);
																		}


																	}
																}




																echo json_encode($response,JSON_NUMERIC_CHECK);

															}



															public function fastagBillPayAuth()
															{
			// 400 - Means Variable realted error
			// 401 - Variable Data not valid
			// 200 - Success
			// response type 1 = JSON, 2 = XML
			// save system log
																log_message('debug', 'Get BBPS Fastag Pay Bill API Called.');

			//check for foem validation
																$post = $this->input->post();
																log_message('debug', 'Get BBPS Fastag Pay Bill API Post Data - '.json_encode($post).'.');


																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required');
																$this->form_validation->set_rules('biller_id', 'Biller ID', 'required');
																$this->form_validation->set_rules('txn_pass', 'Txn Pass', 'required');
																$this->form_validation->set_rules('para1', 'Params1', 'required');
																$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
																if ($this->form_validation->run() == FALSE) {
		    	// save system log
																	log_message('debug', 'Get BBPS Fastag Pay Bill API Parameter not valid error.');
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry ! Parameter are not valid.'
																	);
																}
																else
																{	

																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	// if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	// 	$response = array(
																	// 		'status' => 0,
																	// 		'message' => 'Session out.Please Login Again.'
																	// 	);
																	// }
																	// else{

																		$txn_pass = isset($post['txn_pass']) ? $post['txn_pass'] : '';

																		if(do_hash($txn_pass) != $userData['transaction_password']){

																			$response = array(
																				'status' => 0,
																				'message' => 'Txn Password is wrong.'
																			);
																		}
																		else{

																			$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
																			$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;

				    	// check user valid or not
																			$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID))->num_rows();
																			if($chk_user)
																			{
				    		//get logged user info
																				$activeService = $this->User->account_active_service($loggedAccountID);
																				if(!in_array(4, $activeService)){
					            // save system log
																					log_message('debug', 'Get BBPS Fastag Pay Bill API Service not active.');
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry ! This service is not active in your account.'
																					);
																				}
																				else
																				{
					        	// check member id and password
																					$getAccountData = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
																					$loggedAccountID = $getAccountData['id'];
																					$before_wallet_balance = $getAccountData['wallet_balance'];
																					$min_wallet_balance = $getAccountData['min_wallet_balance'];
																					$memberName = $getAccountData['name'];
																					$memberMobile = $getAccountData['mobile'];
																					$memberEmail = $getAccountData['email'];
																					$memberCode = $getAccountData['user_code'];
																					
																					
																					if($getAccountData['is_main_wallet_block'] == 1){
					        		// save system log
																					//	log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Your fund is blocked for 2 Days.'
																						);
																					}
																					
																					
																					elseif($before_wallet_balance < $post['amount']){
					        		// save system log
																						log_message('debug', 'Get BBPS Fastag Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Insufficient balance in your account.'
																						);
																					}
																					else
																					{
																						if(IS_MOBIKWIK_ACTIVE == 1)
																						{
																							$service_id = 12;
																						$api_response = $this->Bbps_model->bbpsMasterBillPayAuth($post,$loggedAccountID,$service_id,$memberCode);

																						}
																						else
																						{
																							$service_id = 6;
																						$api_response = $this->Bbps_model->bbpsMasterBillPayAuth($post,$loggedAccountID,$service_id,$memberCode);

																						}
																						
						        	// save system log
																						log_message('debug', 'Get BBPS Fastag Pay Bill API Response - '.json_encode($api_response).'.');

																						if($api_response['status'] == 0)
																						{
																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry ! Your bill payment got failed.',
																								'status' => 'FAILED',
																								'optMsg' => $api_response['msg'],
																								'txnID' => $post['txnID'],
																								'optTxnID' => $api_response['txnid']
																							);
																						}
																						elseif($api_response['status'] == 1)
																						{	

																							$message = 'Congratulations ! Your Bill Payment credited successfully.';

																							$this->User->sendNotification($loggedAccountID,'BBPS',$message);

																							$response = array(
																								'status' => 1,
																								'message' => 'Congratulations ! Your Bill Payment credited successfully.',
																								'status' => 'SUCCESS',
																								'optMsg' => $api_response['msg'],
																								'txnID' => $post['txnID'],
																								'optTxnID' => $api_response['txnid']
																							);
																						}
																						elseif($api_response['status'] == 2)
																						{
																							$response = array(
																								'status' => 1,
																								'message' => 'Your Bill Payment is under processing, status will be updated soon.',
																								'status' => 'PENDING',
																								'optMsg' => $api_response['msg'],
																								'txnID' => $post['txnID'],
																								'optTxnID' => $api_response['txnid']
																							);
																						}
																					}
																				}
																			}
																			else
																			{
					    	// save system log
																				log_message('debug', 'Get BBPS Fastag Pay Bill API Member not valid error.');
																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! You are not authorized to access this service.'
																				);
																			}


																		//}
																	}
																}
																log_message('debug', 'Get BBPS Fastag Pay Bill API Final Response - '.json_encode($response).'.');
																echo json_encode($response,JSON_NUMERIC_CHECK);

															}

																public function getServiceOperator()
															{

																log_message('debug', 'Service Operator API Called.');

																$get = $this->input->get();
																$service_id = isset($get['service_id']) ? $get['service_id'] : 0 ;
                                                                    
                                                                    
		    // get electricity biller list
																if(IS_MOBIKWIK_ACTIVE == 1)
																{
																    
																    if($service_id == 6)
                                                                    {
                                                                        $service_id = 12;
                                                                    }
                                                                    elseif($service_id == 13)
                                                                    {
                                                                        $service_id = 1;
                                                                    }
                                                                    elseif($service_id == 11)
                                                                    {
                                                                        $service_id = 3;
                                                                    }
                                                                     elseif($service_id == 14)
                                                                    {
                                                                        $service_id = 19;
                                                                    }
                                                                     elseif($service_id == 10)
                                                                    {
                                                                        $service_id = 2;
                                                                    }
                                                                     elseif($service_id == 8)
                                                                    {
                                                                        $service_id = 21;
                                                                    }
                                                                     elseif($service_id == 3)
                                                                    {
                                                                        $service_id = 6;
                                                                    }
                                                                     elseif($service_id == 15)
                                                                    {
                                                                        $service_id = 11;
                                                                    }
                                                                     elseif($service_id == 1)
                                                                    {
                                                                        $service_id = 17;
                                                                    }
                                                                    elseif($service_id == 2)
                                                                    {
                                                                        $service_id = 5;
                                                                    }
                                                                    elseif($service_id == 12)
                                                                    {
                                                                        $service_id = 9;
                                                                    }
                                                                     elseif($service_id == 16)
                                                                    {
                                                                        $service_id = 18;
                                                                    }
                                                                    elseif($service_id == 18)
                                                                    {
                                                                        $service_id = 18;
                                                                    }
                                                                    elseif($service_id == 50)
                                                                    {
                                                                        $service_id = 10;
                                                                    }

																	$electricityBillerList = $this->User->get_mobikwik_bbps_biller_list($service_id);
																}
																else
																{
																	$electricityBillerList = $this->User->get_bbps_biller_list($service_id);

																}
																

																$data = array();

																if($electricityBillerList)
																{
																	foreach($electricityBillerList as $key=>$list)
																	{
																		$is_fetch = 0;
																		$fetchOption = $list['fetchOption'];
																		if($fetchOption == 'MANDATORY')
																		{
																			$is_fetch = 1;
																		}

																		$data[$key]['biller_id'] = $list['biller_id'];
																		$data[$key]['billerName'] = $list['billerName'];
																		$data[$key]['billerAliasName'] = $list['billerAliasName'];
																		$data[$key]['is_fetch'] = $is_fetch;
																	}
																}

																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data' => $data
																);

																echo json_encode($response,JSON_NUMERIC_CHECK);

															}

															public function getBbpsOperatorList()
															{


																$bbps_operator_list = $this->db->get('bbps_operator')->result_array();

																$data = array();

																if($bbps_operator_list)
																{
																	foreach($bbps_operator_list as $key=>$oList)
																	{
																		$data[$key]['operator_code'] = $oList['operator_code'];
																		$data[$key]['operator_name'] = $oList['operator_name'];
																	}
																}

																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data' => $data
																);

																echo json_encode($response,JSON_NUMERIC_CHECK);

															}

															public function getBbpsCircleList()
															{


																$bbps_operator_list = $this->db->get('bbps_circle')->result_array();

																$data = array();

																if($bbps_operator_list)
																{
																	foreach($bbps_operator_list as $key=>$oList)
																	{
																		$data[$key]['circle_code'] = $oList['circle_code'];
																		$data[$key]['circle_name'] = $oList['circle_name'];
																	}
																}

																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data' => $data
																);

																echo json_encode($response,JSON_NUMERIC_CHECK);

															}

															public function getServiceFormParams()
															{

																log_message('debug', 'Service Form Params API Called.');

																$get = $this->input->get();

																log_message('debug', 'Service Form Params Get Data - '.json_encode($get));

																$biller_id = isset($get['biller_id']) ? $get['biller_id'] : '';
															    $service_id = isset($get['service_id']) ? $get['service_id'] : 0;
																

		    // get biller system id
																if(IS_MOBIKWIK_ACTIVE == 1)
																{
																     if($service_id == 6)
                                                                        {
                                                                            $service_id = 12;
                                                                        }
                                                                        elseif($service_id == 13)
                                                                        {
                                                                            $service_id = 1;
                                                                        }
                                                                        elseif($service_id == 11)
                                                                        {
                                                                            $service_id = 3;
                                                                        }
                                                                         elseif($service_id == 14)
                                                                        {
                                                                            $service_id = 19;
                                                                        }
                                                                         elseif($service_id == 10)
                                                                        {
                                                                            $service_id = 2;
                                                                        }
                                                                         elseif($service_id == 8)
                                                                        {
                                                                            $service_id = 21;
                                                                        }
                                                                         elseif($service_id == 3)
                                                                        {
                                                                            $service_id = 6;
                                                                        }
                                                                         elseif($service_id == 15)
                                                                        {
                                                                            $service_id = 11;
                                                                        }
                                                                         elseif($service_id == 1)
                                                                        {
                                                                            $service_id = 17;
                                                                        }
                                                                        elseif($service_id == 2)
                                                                        {
                                                                            $service_id = 5;
                                                                        }
                                                                        elseif($service_id == 12)
                                                                        {
                                                                            $service_id = 9;
                                                                        }
                                                                         elseif($service_id == 16)
                                                                        {
                                                                            $service_id = 18;
                                                                        }
                                                                        elseif($service_id == 18)
                                                                        {
                                                                            $service_id = 18;
                                                                        }
                                                                         elseif($service_id == 50)
                                                                    {
                                                                        $service_id = 10;
                                                                    }

                                                                    
																	$get_biller_id = $this->db->get_where('mobikwik_bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
                                                                        
                                                                       $billerID = isset($get_biller_id['id']) ? $get_biller_id['id'] : 0 ;
                                                                       
                                                                     
                                                                        if($biller_id == 111)
                                                                        {
																	    $billerID = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : 0 ;
                                                                        }
                                                                        else
                                                                        {
                                                                            $billerID = isset($get_biller_id['id']) ? $get_biller_id['id'] : 0 ;
                                                                            
                                                                        }


																}
																else
																{
																    	
																	$get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
																	$billerID = isset($get_biller_id['id']) ? $get_biller_id['id'] : 0 ;

																}
																
																

																if($billerID)
																{
		        // get biller params
																		if(IS_MOBIKWIK_ACTIVE == 1)
																		{

																			$billerParams = $this->User->get_mobikwik_bbps_biller_param($service_id,$billerID);
                                                                            
																		}
																		else
																		{

																		$billerParams = $this->User->get_bbps_biller_param($service_id,$billerID);

																		}
																	

																	$data = array();

																	if($billerParams)
																	{
																		foreach($billerParams as $key=>$list)
																		{
																			$data[$key]['fieldKey'] = 'para'.($key+1);
																			$data[$key]['paramName'] = $list['paramName'];
																			$data[$key]['datatype'] = $list['datatype'];
																			$data[$key]['minlength'] = $list['minlength'];
																			$data[$key]['maxlength'] = $list['maxlength'];
																			$data[$key]['optional'] = $list['optional'];
																		}
																	}

																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data' => $data
																	);
																}
																else
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Biller ID is not found in the system.'
																	);
																}
																log_message('debug', 'Service Form Params API Final Response - '.json_encode($response).'.');
																echo json_encode($response,JSON_NUMERIC_CHECK);

															} 

															
																public function serviceBillFetchAuth()
															{
			// 400 - Means Variable realted error
			// 401 - Variable Data not valid
			// 200 - Success
			// response type 1 = JSON, 2 = XML
			// save system log
																log_message('debug', 'Service Bill Fetch Bill API Called.');

																$post = $this->input->post();
		    // save system log
																log_message('debug', 'Service Bill Fetch Bill API Post Data - '.json_encode($post).'.');

																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required');
																$this->form_validation->set_rules('service_id', 'Service ID', 'required');
																$this->form_validation->set_rules('biller_id', 'Biller ID', 'required');
																$this->form_validation->set_rules('para1', 'Params1', 'required');
																if ($this->form_validation->run() == FALSE) {
		    	// save system log
																	log_message('debug', 'Service Bill Fetch Bill API Parameter not valid error.');
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry ! Parameter are not valid.'
																	);
																}
																else
																{	

																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	// if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	// 	$response = array(
																	// 		'status' => 0,
																	// 		'message' => 'Session out.Please Login Again.'
																	// 	);
																	// }
																	// else{

																		$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
																		$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
																		$service_id = isset($post['service_id']) ? $post['service_id'] : 0;
																		

			    	// check user valid or not
																		$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID))->num_rows();
																		if($chk_user)
																		{
			    		//get logged user info
																			$activeService = $this->User->account_active_service($loggedAccountID);
																			if(!in_array(4, $activeService)){
				            // save system log
																				log_message('debug', 'Service Bill Fetch Bill API Service not active.');
																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! This service is not active in your account.'
																				);
																			}
																			else
																			{
				        	// get biller system id
																				if(IS_MOBIKWIK_ACTIVE == 1)
																				{
																				    
																				    if($service_id == 6)
                                                                        {
                                                                            $service_id = 12;
                                                                        }
                                                                        elseif($service_id == 13)
                                                                        {
                                                                            $service_id = 1;
                                                                        }
                                                                        elseif($service_id == 11)
                                                                        {
                                                                            $service_id = 3;
                                                                        }
                                                                         elseif($service_id == 14)
                                                                        {
                                                                            $service_id = 19;
                                                                        }
                                                                         elseif($service_id == 10)
                                                                        {
                                                                            $service_id = 2;
                                                                        }
                                                                         elseif($service_id == 8)
                                                                        {
                                                                            $service_id = 21;
                                                                        }
                                                                         elseif($service_id == 3)
                                                                        {
                                                                            $service_id = 6;
                                                                        }
                                                                         elseif($service_id == 15)
                                                                        {
                                                                            $service_id = 11;
                                                                        }
                                                                         elseif($service_id == 1)
                                                                        {
                                                                            $service_id = 17;
                                                                        }
                                                                        elseif($service_id == 2)
                                                                        {
                                                                            $service_id = 5;
                                                                        }
                                                                        elseif($service_id == 12)
                                                                        {
                                                                            $service_id = 9;
                                                                        }
                                                                         elseif($service_id == 16)
                                                                        {
                                                                            $service_id = 18;
                                                                        }
                                                                        elseif($service_id == 18)
                                                                        {
                                                                            $service_id = 18;
                                                                        }
                                                                         elseif($service_id == 50)
                                                                    {
                                                                        $service_id = 10;
                                                                    }


																				    
																					$get_biller_id = $this->db->get_where('mobikwik_bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
                                                                                   
                                                                                   $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
                                                                                   
                                                                                   
																				}
																				else
																				{
																					$get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
																			        $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
																				}
																				
																				
																				$service_id = isset($get_biller_id['service_id']) ? $get_biller_id['service_id'] : '';

					        // get pmr service id
																				if(IS_MOBIKWIK_ACTIVE == 1)
																				{
																				     if($service_id == 6)
                                                                                    {
                                                                                        $service_id = 12;
                                                                                    }
                                                                                    elseif($service_id == 13)
                                                                                    {
                                                                                        $service_id = 1;
                                                                                    }
                                                                                    elseif($service_id == 11)
                                                                                    {
                                                                                        $service_id = 3;
                                                                                    }
                                                                                     elseif($service_id == 14)
                                                                                    {
                                                                                        $service_id = 19;
                                                                                    }
                                                                                     elseif($service_id == 10)
                                                                                    {
                                                                                        $service_id = 2;
                                                                                    }
                                                                                     elseif($service_id == 8)
                                                                                    {
                                                                                        $service_id = 21;
                                                                                    }
                                                                                     elseif($service_id == 3)
                                                                                    {
                                                                                        $service_id = 6;
                                                                                    }
                                                                                     elseif($service_id == 15)
                                                                                    {
                                                                                        $service_id = 11;
                                                                                    }
                                                                                     elseif($service_id == 1)
                                                                                    {
                                                                                        $service_id = 17;
                                                                                    }
                                                                                    elseif($service_id == 2)
                                                                                    {
                                                                                        $service_id = 5;
                                                                                    }
                                                                                    elseif($service_id == 12)
                                                                                    {
                                                                                        $service_id = 9;
                                                                                    }
                                                                                     elseif($service_id == 16)
                                                                                    {
                                                                                        $service_id = 18;
                                                                                    }
                                                                                    elseif($service_id == 18)
                                                                                    {
                                                                                        $service_id = 18;
                                                                                    }
                                                                                     elseif($service_id == 50)
                                                                                        {
                                                                                            $service_id = 10;
                                                                                        }

																					$pmr_service_id = $service_id;
																				}
																				else
																				{
																					$get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
																				$pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;
																				}
																				

																				if($pmr_service_id == 32)
																				{
																					$postData = array();
																					$postData['number'] = isset($post['para1']) ? $post['para1'] : '';

																				}
																				else
																				{
																					$postData = array();
																					$postData['params'][0] = isset($post['para1']) ? $post['para1'] : '';
																					$postData['params'][1] = isset($post['para2']) ? $post['para2'] : '';
																					$postData['params'][2] = isset($post['para3']) ? $post['para3'] : '';
																					$postData['params'][3] = isset($post['para4']) ? $post['para4'] : '';
																					$postData['params'][4] = isset($post['para5']) ? $post['para5'] : '';
																				}

																				if(IS_MOBIKWIK_ACTIVE == 1)
																				{
																				   
																					$bill_fetch_respone = $this->User->call_mobikwik_bbps_electricity_bill_fetch_api($loggedAccountID,$biller_payu_id,$pmr_service_id,$postData);

																				}
																				else
																				{
																					$bill_fetch_respone = $this->User->call_bbps_service_bill_fetch_api($loggedAccountID,$biller_payu_id,$pmr_service_id,$postData);
																				}

																				
																				if($bill_fetch_respone['status'] == 1)
																				{
																					$response = array(
																						'status' => 1,
																						'message' => 'Success',
																						'amount' => $bill_fetch_respone['amount'],
																						'accountHolderName' => $bill_fetch_respone['accountHolderName']
																					);
																				}
																				else
																				{
																					$response = array(
																						'status' => 1,
																						'message' => 'Success',
																						'amount' => 0,
																						'accountHolderName' => ''
																					);
																				}
																			}
																		}
																		else
																		{
				    	// save system log
																			log_message('debug', 'Service Bill Fetch Bill API Member not valid error.');
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry ! You are not authorized to access this service.'
																			);
																		}


																	}
																//}




																echo json_encode($response,JSON_NUMERIC_CHECK);
															}
															
																public function serviceBillPayAuth()
															{
			// 400 - Means Variable realted error
			// 401 - Variable Data not valid
			// 200 - Success
			// response type 1 = JSON, 2 = XML
			// save system log
																log_message('debug', 'Service Pay Bill API Called.');

			//check for foem validation
																$post = $this->input->post();
																log_message('debug', 'Service Pay Bill API Post Data - '.json_encode($post).'.');


																$this->load->library('form_validation');
																$this->form_validation->set_rules('user_id', 'User ID', 'required');
																$this->form_validation->set_rules('service_id', 'Service ID', 'required');
																$this->form_validation->set_rules('biller_id', 'Biller ID', 'required');
																$this->form_validation->set_rules('txn_pass', 'Txn Pass', 'required');
																$this->form_validation->set_rules('para1', 'Params1', 'required');
																$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
																if ($this->form_validation->run() == FALSE) {
		    	// save system log
																	log_message('debug', 'Service Pay Bill API Parameter not valid error.');
																	$response = array(
																		'status' => 0,
																		'message' => 'Sorry ! Parameter are not valid.'
																	);
																}
																else
																{	

																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	// if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	// 	$response = array(
																	// 		'status' => 0,
																	// 		'message' => 'Session out.Please Login Again.'
																	// 	);
																	// }
																	// else{

																		$txn_pass = isset($post['txn_pass']) ? $post['txn_pass'] : '';

																		if(do_hash($txn_pass) != $userData['transaction_password']){

																			$response = array(
																				'status' => 0,
																				'message' => 'Txn Password wrong.'
																			);

																		}
																		else{

																			$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
																			$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
																			$service_id = isset($post['service_id']) ? $post['service_id'] : 0;

				    	// check user valid or not
																			$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID))->num_rows();
																			if($chk_user)
																			{
				    		//get logged user info
																				$activeService = $this->User->account_active_service($loggedAccountID);
																				if(!in_array(4, $activeService)){
					            // save system log
																					log_message('debug', 'Service Pay Bill API Service not active.');
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry ! This service is not active in your account.'
																					);
																				}
																				else
																				{
					        	// check member id and password
																					$getAccountData = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
																					$loggedAccountID = $getAccountData['id'];
																					$before_wallet_balance = $getAccountData['wallet_balance'];
																					$min_wallet_balance = $getAccountData['min_wallet_balance'];
																					$memberName = $getAccountData['name'];
																					$memberMobile = $getAccountData['mobile'];
																					$memberEmail = $getAccountData['email'];
																					$memberCode = $getAccountData['user_code'];
																					
																					
																					if($getAccountData['is_main_wallet_block'] == 1){
					        		// save system log
																					//	log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Your fund is blocked for 2 Days.'
																						);
																					}
																					
																					
																					elseif($before_wallet_balance < $post['amount']){
					        		// save system log
																						log_message('debug', 'Service Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Insufficient balance in your account.'
																						);
																					}
																					else
																					{

																						$api_response = $this->Bbps_model->bbpsMasterBillPayAuth($post,$loggedAccountID,$service_id,$memberCode);
						        	// save system log
																						log_message('debug', 'Service Pay Bill API Response - '.json_encode($api_response).'.');

																						if($api_response['status'] == 0)
																						{
																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry ! Your bill payment got failed.',
																								'status' => 'FAILED',
																								'optMsg' => $api_response['msg'],
																								'txnID' => $post['txnID'],
																								'optTxnID' => $api_response['txnid']
																							);
																						}
																						elseif($api_response['status'] == 1)
																						{	

																							$message = 'Congratulations ! Your Bill Payment credited successfully.';

																							$this->User->sendNotification($loggedAccountID,'BBPS',$message);

																							$response = array(
																								'status' => 1,
																								'message' => 'Congratulations ! Your Bill Payment credited successfully.',
																								'status' => 'SUCCESS',
																								'optMsg' => $api_response['msg'],
																								'txnID' => $post['txnID'],
																								'optTxnID' => $api_response['txnid']
																							);
																						}
																						elseif($api_response['status'] == 2)
																						{
																							$response = array(
																								'status' => 1,
																								'message' => 'Your Bill Payment is under processing, status will be updated soon.',
																								'status' => 'PENDING',
																								'optMsg' => $api_response['msg'],
																								'txnID' => $post['txnID'],
																								'optTxnID' => $api_response['txnid']
																							);
																						}
																					}
																				}
																			}
																			else
																			{
					    	// save system log
																				log_message('debug', 'Service Pay Bill API Member not valid error.');
																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! You are not authorized to access this service.'
																				);
																			}


																		}
																	}
																//}
																log_message('debug', 'Service Pay Bill API Final Response - '.json_encode($response).'.');
																echo json_encode($response,JSON_NUMERIC_CHECK);
															}




															public function getRechargeCommission()
															{
																$post = $this->input->post();
																log_message('debug', 'Recharge Commission API POST Data - '.json_encode($post));

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$userID = isset($post['userID']) ? $post['userID'] : 0;
																	$response = array();
				// check user valid or not
																	$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																	if($chk_user)
																	{
																		$recharge = $this->db->query("SELECT a.*,b.name as from_member_name,b.user_code as from_member_code,c.recharge_display_id FROM tbl_level_commision as a INNER JOIN tbl_users as b ON b.id = a.paid_from_member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.paid_to_member_id = '$userID' and a.level_num = 0 and a.commission_type = 'RECHARGE'")->result_array();

																		$data = array();
																		if($recharge)
																		{
																			foreach ($recharge as $key => $list) {

																				$data[$key]['recharge_display_id'] = $list['recharge_display_id'];
																				$data[$key]['recharge_amount'] = number_format($list['recharge_amount'],2).'/-';
																				$data[$key]['commission_amount'] = number_format($list['commision_amount'],2).'/-';
																				$data[$key]['date'] = date('d-M-Y',strtotime($list['created']));

																			}
																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data' => $data,
																		);	
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => lang('USER_ID_ERROR')
																		);
																	}
																}
																log_message('debug', 'Recharge Commission API Response - '.json_encode($response));	
																echo json_encode($response);
															}



															public function getBbpsCommission()
															{
																$post = $this->input->post();
																log_message('debug', 'BBPS Commission API POST Data - '.json_encode($post));

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$userID = isset($post['userID']) ? $post['userID'] : 0;
																	$response = array();
				// check user valid or not
																	$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																	if($chk_user)
																	{
																		$recharge = $this->db->query("SELECT a.*,b.name as from_member_name,b.user_code as from_member_code,c.recharge_display_id FROM tbl_level_commision as a INNER JOIN tbl_users as b ON b.id = a.paid_from_member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.paid_to_member_id = '$userID' and a.level_num = 0 and a.commission_type = 'BBPS'")->result_array();

																		$data = array();
																		if($recharge)
																		{
																			foreach ($recharge as $key => $list) {

																				$data[$key]['recharge_display_id'] = $list['recharge_display_id'];
																				$data[$key]['recharge_amount'] = number_format($list['recharge_amount'],2).'/-';
																				$data[$key]['commission_amount'] = number_format($list['commision_amount'],2).'/-';
																				$data[$key]['date'] = date('d-M-Y',strtotime($list['created']));

																			}
																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data' => $data,
																		);	
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => lang('USER_ID_ERROR')
																		);
																	}
																}
																log_message('debug', 'BBPS Commission API Response - '.json_encode($response));	
																echo json_encode($response);
															}




															public function getRechargeIncome()
															{
																$post = $this->input->post();
																log_message('debug', 'getRechargeIncome API POST Data - '.json_encode($post));	
																$userID = isset($post['userID']) ? $post['userID'] : 0;

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$response = array();
				// check user valid or not
																	$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																	if($chk_user)
																	{
																		$recharge = $this->db->query("SELECT a.*,b.name as from_member_name,b.user_code as from_member_code,c.recharge_display_id FROM tbl_level_commision as a INNER JOIN tbl_users as b ON b.id = a.paid_from_member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.paid_to_member_id = '$userID' and a.level_num != 0 and a.commission_type = 'RECHARGE'")->result_array();

																		$data = array();
																		if($recharge)
																		{
																			foreach ($recharge as $key => $list) {

																				$data[$key]['recharge_display_id'] = $list['recharge_display_id'];
																				$data[$key]['recharge_amount'] = number_format($list['recharge_amount'],2).'/-';
																				$data[$key]['commission_amount'] = number_format($list['commision_amount'],2).'/-';
																				$data[$key]['date'] = date('d-M-Y',strtotime($list['created']));

																			}
																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data' => $data,
																		);	
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => lang('USER_ID_ERROR')
																		);
																	}
																}
																log_message('debug', 'getRechargeIncome API Response - '.json_encode($response));	
																echo json_encode($response);
															}



															public function getBbpsIncome()
															{
																$post = $this->input->post();
																log_message('debug', 'getBbpsIncome API POST Data - '.json_encode($post));	
																$userID = isset($post['userID']) ? $post['userID'] : 0;

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$response = array();
				// check user valid or not
																	$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																	if($chk_user)
																	{
																		$recharge = $this->db->query("SELECT a.*,b.name as from_member_name,b.user_code as from_member_code,c.recharge_display_id FROM tbl_level_commision as a INNER JOIN tbl_users as b ON b.id = a.paid_from_member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.paid_to_member_id = '$userID' and a.level_num != 0 and a.commission_type = 'BBPS'")->result_array();

																		$data = array();
																		if($recharge)
																		{
																			foreach ($recharge as $key => $list) {

																				$data[$key]['recharge_display_id'] = $list['recharge_display_id'];
																				$data[$key]['recharge_amount'] = number_format($list['recharge_amount'],2).'/-';
																				$data[$key]['commission_amount'] = number_format($list['commision_amount'],2).'/-';
																				$data[$key]['date'] = date('d-M-Y',strtotime($list['created']));

																			}
																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data' => $data,
																		);	
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => lang('USER_ID_ERROR')
																		);
																	}
																}
																log_message('debug', 'getBbpsIncome API Response - '.json_encode($response));	
																echo json_encode($response);
															}



															public function getPlanList(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'View Plan API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
																$this->form_validation->set_rules('operator', 'Operator', 'required|xss_clean');
																$this->form_validation->set_rules('circle', 'Circle', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please enter required field'
																	);
																}
																else
																{
																	$op_id = $post['operator'];
																	$circle_id = $post['circle'];
		        // get operator name
																	$get_operator_name = $this->db->select('operator_name,active_api_id')->get_where('operator',array('operator_code'=>$op_id))->row_array();
																	$operator_name = isset($get_operator_name['operator_name']) ? $get_operator_name['operator_name'] : '';

																	if($op_id == 3 || $op_id == 4 || $op_id == 11)
																	{
																		$operator_name = 'BSNL';
																	}

		        // get circle name
																	$get_circle_name = $this->db->select('circle_name')->get_where('circle',array('id'=>$circle_id))->row_array();
																	$circle_name = isset($get_circle_name['circle_name']) ? $get_circle_name['circle_name'] : '';

																	if($get_operator_name['active_api_id'] == 2){

																		$key=PAYSPRINT_RECHARGE_API_KEY;
																		$iv=PAYSPRINT_RECHARGE_API_IV;

																		$reqid = time().rand(1111,9999);

																		log_message('debug', 'Paysprint recharge browse plan api call');     

																		$jwt_payload = array(
																			'timestamp'=>time(),
																			'partnerId'=>PAYSPRINT_RECHARGE_API_PARTNER_ID,
																			'reqid'=>$reqid
																		);

																		log_message('debug', 'Paysprint recharge browse plan api payload - '.json_encode($jwt_payload));


																		$secret = PAYSPRINT_RECHARGE_API_PARTNER_SECRET;

																		$token = $this->Jwt_model->encode($jwt_payload,$secret);


																		$datapost = array();
																		$datapost['circle'] = $circle_name;
																		$datapost['op'] = $operator_name;   

																		log_message('debug', 'Paysprint recharge browse plan api post data - '.json_encode($datapost));


																		$header = [
																			'Token:'.$token,
                        													//'AuthorisedKey:'.PAYSPRINT_RECHARGE_API_AUTHORISED_KEY,
																			'accept:application/json'
																		];

																		log_message('debug', 'Paysprint recharge browse plan api header data - '.json_encode($header));

																		$httpUrl = PAYSPRINt_RECHARGE_BROWSE_PLAN_API;

																		log_message('debug', 'Paysprint recharge browse plan api url - '.$httpUrl);

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

																		log_message('debug', 'Paysprint recharge browse plan api response - '.$output);

																		$plan = json_decode($output,true);

																		$planList = array();
																		if(isset($plan['info']) && $plan['status'] == 1 && $plan['response_code'] == 1)
																		{
																			$records = isset($plan['info']) ? $plan['info'] : array();
																			if($records)
																			{
																				$i = 0;
																				foreach($records as $tabKey=>$tabData)
																				{
																					$planList[$i]['type'] = $tabKey;
																					$typeData = array();
																					$j = 0;
																					if($tabData)
																					{
																						foreach($tabData as $planKey=>$planData)
																						{
																							$typeData[$j]['amount'] = $planData['rs'];
																							$typeData[$j]['desc'] = $planData['desc'];
																							$typeData[$j]['validity'] = $planData['validity'];
																							$j++;
																						}
																					}

																					$planList[$i]['data'] = $typeData;

																					$i++;
																				}

																			}
																			$response = array(
																				'status' => 1,
																				'message' => 'Success',
																				'data' => $planList
																			);
																		}
																		else
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Error From Operator Side',
																			);
																		}

																	}
																	else{

																		$api_url = PLAN_FINDER_API_URL;

																		$headers = [
																			'Token: '.PLAN_FINDER_API_TOKEN,
																			'Content-Type: application/x-www-form-urlencoded'
																		];

																		$api_post_data = array();
																		$api_post_data['oparetorName'] = $operator_name;
																		$api_post_data['circleName'] = $circle_name;
																		$ch = curl_init();
																		curl_setopt($ch, CURLOPT_URL,$api_url);
																		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
																		curl_setopt($ch, CURLOPT_POST, true);
																		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));     
																		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
																		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
																		$output = curl_exec ($ch);
																		curl_close ($ch);


																		$plan = json_decode($output,true);

																		$planList = array();
																		if(isset($plan['Error']) && $plan['Error'] == 'False')
																		{
																			$records = isset($plan['Data']['records']) ? $plan['Data']['records'] : array();
																			if($records)
																			{
																				$i = 0;
																				foreach($records as $tabKey=>$tabData)
																				{
																					$planList[$i]['type'] = $tabKey;
																					$typeData = array();
																					$j = 0;
																					if($tabData)
																					{
																						foreach($tabData as $planKey=>$planData)
																						{
																							$typeData[$j]['amount'] = $planData['rs'];
																							$typeData[$j]['desc'] = $planData['desc'];
																							$typeData[$j]['validity'] = $planData['validity'];
																							$j++;
																						}
																					}

																					$planList[$i]['data'] = $typeData;

																					$i++;
																				}

																			}
																			$response = array(
																				'status' => 1,
																				'message' => 'Success',
																				'data' => $planList
																			);
																		}
																		else
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Error From Operator Side',
																			);
																		}
																	}

																}
																log_message('debug', 'View Plan API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function getDTHRofferList(){


																$response = array();
																$post = $this->input->post();
																log_message('debug', 'DTH Roffer API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
																$this->form_validation->set_rules('operator', 'Operator', 'required|xss_clean');
																$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please enter required field'
																	);
																}
																else
																{
																	$op_id = $post['operator'];
																	$mobile = $post['mobile'];

		        // get operator name
																	$get_operator_name = $this->db->select('plan_code')->get_where('operator',array('operator_code'=>$op_id))->row_array();
																	$operator_name = isset($get_operator_name['plan_code']) ? $get_operator_name['plan_code'] : '';

																	$api_url = DTH_PLAN_FINDER_API_URL;

																	$headers = [
																		'Token: '.PLAN_FINDER_API_TOKEN,
																		'Content-Type: application/x-www-form-urlencoded'
																	];

																	$api_post_data = array();
																	$api_post_data['oparetorName'] = $operator_name;
																	$ch = curl_init();
																	curl_setopt($ch, CURLOPT_URL,$api_url);
																	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
																	curl_setopt($ch, CURLOPT_POST, true);
																	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));     
																	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
																	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
																	$output = curl_exec ($ch);
																	curl_close ($ch);

																	$plan = json_decode($output,true);

																	$planList = array();
																	if(isset($plan['Error']) && $plan['Error'] == 'False')
																	{
																		$records = isset($plan['Data']['records']['Plan']) ? $plan['Data']['records']['Plan'] : array();
																		if($records)
																		{
																			$i = 0;
																			foreach($records as $tabKey=>$tabData)
																			{
																				if($tabData['rs'])
																				{
																					foreach($tabData['rs'] as $planKey=>$planData)
																					{
																						$planList[$i]['amount'] = $planData;
																						$planList[$i]['desc'] = $tabData['desc'];
																						$planList[$i]['plan_name'] = $tabData['plan_name'];
																						$planList[$i]['validity'] = $planKey;
																						$i++;
																					}
																				}


																			}

																		}
																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data' => $planList
																		);
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'Error From Operator Side',
																		);
																	}

																}
																log_message('debug', 'DTH Roffer API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function getDTHPlanList(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'DTH View Plan API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
																$this->form_validation->set_rules('operator', 'Operator', 'required|xss_clean');
																$this->form_validation->set_rules('number', 'Card Number', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please enter required field'
																	);
																}
																else
																{
																	$op_id = $post['operator'];
																	$cardNumber = $post['number'];

		        // get operator name
																	$get_operator_name = $this->db->select('plan_code')->get_where('operator',array('operator_code'=>$op_id))->row_array();
																	$operator_name = isset($get_operator_name['plan_code']) ? $get_operator_name['plan_code'] : '';

																	$api_url = DTH_BILLER_DETAIL_API_URL;

																	$headers = [
																		'Token: '.PLAN_FINDER_API_TOKEN,
																		'Content-Type: application/x-www-form-urlencoded'
																	];

																	$api_post_data = array();
																	$api_post_data['operatorName'] = $operator_name;
																	$api_post_data['VCnumber'] = $cardNumber;
																	$ch = curl_init();
																	curl_setopt($ch, CURLOPT_URL,$api_url);
																	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
																	curl_setopt($ch, CURLOPT_POST, true);
																	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));     
																	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
																	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
																	$output = curl_exec ($ch);
																	curl_close ($ch);

																	$plan = json_decode($output,true);

																	$monthlyRechargeAmount = 0;
																	$balance = 0;
																	$customerName = '';
																	$planList = array();
																	if(isset($plan['Error']) && $plan['Error'] == 'False')
																	{
																		$monthlyRechargeAmount = isset($plan['Data']['records'][0]['MonthlyRecharge']) ? $plan['Data']['records'][0]['MonthlyRecharge'] : 0;
																		$balance = isset($plan['Data']['records'][0]['Balance']) ? $plan['Data']['records'][0]['Balance'] : 0;
																		$customerName = isset($plan['Data']['records'][0]['customerName']) ? $plan['Data']['records'][0]['customerName'] : '';
																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'rechargeAmount' => $monthlyRechargeAmount,
																			'balance' => $balance,
																			'customerName' => $customerName
																		);
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'Error From Operator Side',
																		);
																	}



																}
																log_message('debug', 'DTH View Plan API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function getOperatorId(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Operator Finder API Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
																$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'Please enter required field'
																	);
																}
																else
																{
																	$api_url = OPERATOR_FINDER_API_URL;

																	$headers = [
																		'Token: '.PLAN_FINDER_API_TOKEN,
																		'Content-Type: application/x-www-form-urlencoded'
																	];

																	$api_post_data = array();
																	$api_post_data['Mobile'] = $post['mobile'];
																	$ch = curl_init();
																	curl_setopt($ch, CURLOPT_URL,$api_url);
																	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
																	curl_setopt($ch, CURLOPT_POST, true);
																	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));     
																	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
																	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
																	$output = curl_exec ($ch);
																	curl_close ($ch);

																	$plan = json_decode($output,true);


																	$is_error = 0;
																	$operator_id = 0;
																	$circle_id = 0;
																	if(isset($plan['Error']) && $plan['Error'] == 'False')
																	{
																		$records = isset($plan['Data']['records']) ? $plan['Data']['records'] : array();
																		if($records)
																		{
																			$operator = $records['Operator'];
																			$circle = !empty($records['comcircle']) ? $records['comcircle'] : $records['circle'];

																			$get_operator_name = $this->db->select('operator_code')->get_where('operator',array('operator_name'=>$operator))->row_array();
																			$operator_id = isset($get_operator_name['operator_code']) ? $get_operator_name['operator_code'] : '';
		                // get operator name
																			$get_circle_name = $this->db->select('id')->get_where('circle',array('circle_name'=>$circle))->row_array();


                                                                            
                                                                             if($circle == 'MP and Chattisgarh')
                                                                            {
                                                                                $circle_id = '14';
                                                                            }
                                                                            else
                                                                            {
                                                                                $circle_id = isset($get_circle_name['id']) ? $get_circle_name['id'] : '';
                                                                            }

																			
																		}
																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'operator_id' => $operator_id,
																			'circle_id' => $circle_id
																		);
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'Error From Operator Side',
																		);
																	}



																}
																log_message('debug', 'Operator Finder API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function getBBSPHistory()
															{
																$post = $this->input->post();
																log_message('debug', 'BBSP History API Get Data - '.json_encode($post));	
																$userID = isset($post['userID']) ? $post['userID'] : 0;

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$response = array();
																	$fromDate = isset($post['fromDate']) ? ($post['fromDate']) ? $post['fromDate'] : date('Y-m-d') : date('Y-m-d');
																	$toDate = isset($post['toDate']) ? ($post['toDate']) ? $post['toDate'] : date('Y-m-d') : date('Y-m-d');
				// check user valid or not
																	$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																	if($chk_user)
																	{

																		$sql = "SELECT a.*, b.user_code as user_code, b.name as name,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.operator_code = a.operator_code where a.id > 0 AND a.recharge_type = 7 AND (b.created_by = '$userID' OR a.member_id = '$userID')";

																		if($fromDate && $toDate)
																		{
																			$sql.=" AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."'";
																		}
																		$sql.=" ORDER BY created DESC";
																		$historyList = $this->db->query($sql)->result_array();

																		$data = array();
																		if($historyList)
																		{
																			foreach ($historyList as $key => $list) {

																				$data[$key]['order_id'] = $list['recharge_display_id'];
																				$data[$key]['amount'] = $list['amount'];
																				$data[$key]['before_balance'] = ($list['before_balance']) ? $list['before_balance'] : 0;
																				$data[$key]['after_balance'] = ($list['after_balance']) ? $list['after_balance'] : 0;
																				$data[$key]['operator'] = $list['operator_name'];
																				$data[$key]['type'] = 'Electricity';
																				$data[$key]['account_number'] = $list['account_number'];
																				$data[$key]['customer_name'] = $list['customer_name'];
																				$data[$key]['member_name'] = $list['name'];
																				$data[$key]['member_code'] = $list['user_code'];
																				$data[$key]['txid'] = $list['txid'];
																				$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
																				$data[$key]['status'] = $list['status'];

																			}
																		}

																		if($data)
																		{
																			$response = array(
																				'status' => 1,
																				'message' => 'Success',
																				'data' => $data
																			);	
																		}
																		else
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry ! No Record Found.',
																				'data' => $data
																			);	
																		}
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry ! Member not valid.'
																		);
																	}
																}
																log_message('debug', 'BBPS History API Response - '.json_encode($response));	
																echo json_encode($response);
															}

															public function getBBPSLiveHistory()
															{
																$post = $this->input->post();
																log_message('debug', 'BBSP Live History API Get Data - '.json_encode($post));	
																$userID = isset($post['userID']) ? $post['userID'] : 0;

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$response = array();
																	$fromDate = $post['fromDate'];
																	$toDate = $post['toDate'];
																	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																	$limit = $page_no * 50;
				// check user valid or not
																	$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																	if($chk_user)
																	{

																		$sql = "SELECT a.*,b.name,b.user_code,c.title as service_name FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND (b.created_by = '$userID' OR a.member_id = '$userID')";

																		if($fromDate && $toDate)
																		{
																			$sql.=" AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."'";

																			$sql.=" ORDER BY a.created DESC";

																			$count = $this->db->query($sql)->num_rows();

																			$limit_start = $limit - 50; 

																			$limit_end = 50;

																			$sql.=" LIMIT ".$limit_start." ,".$limit_end."";

																		}
																		else{

																			$sql.=" ORDER BY created DESC";

																			$count = $this->db->query($sql)->num_rows();

																			$limit_start = $limit - 50; 

																			$limit_end = 50;

																			$sql.=" LIMIT ".$limit_start." ,".$limit_end."";
																		}

																		$historyList = $this->db->query($sql)->result_array();

																		$pages = ceil($count / 50);

																		$data = array();
																		if($historyList)
																		{
																			foreach ($historyList as $key => $list) {

																				$data[$key]['recharge_id'] = $list['id'];
																				$data[$key]['order_id'] = $list['recharge_display_id'];
																				$data[$key]['amount'] = $list['amount'];
																				$data[$key]['before_balance'] = ($list['before_balance']) ? $list['before_balance'] : 0;
																				$data[$key]['after_balance'] = ($list['after_balance']) ? $list['after_balance'] : 0;
																				$data[$key]['operator'] = $list['operator_code'];
																				$data[$key]['type'] = $list['service_name'];
																				$data[$key]['mobile'] = $list['mobile'];
																				$data[$key]['account_number'] = $list['account_number'];
																				$data[$key]['customer_name'] = $list['customer_name'];
																				$data[$key]['member_name'] = $list['name'];
																				$data[$key]['member_code'] = $list['user_code'];
																				$data[$key]['txid'] = $list['txid'];
																				$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
																				$data[$key]['status'] = $list['status'];

																			}
																		}

																		if($data)
																		{
																			$response = array(
																				'status' => 1,
																				'message' => 'Success',
																				'data' => $data,
																				'pages' => $pages,
																			);	
																		}
																		else
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry ! No Record Found.',
																				'data' => $data
																			);	
																		}
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry ! Member not valid.'
																		);
																	}
																}
																log_message('debug', 'BBPS Live History API Response - '.json_encode($response));	
																echo json_encode($response);
															}



															public function getMainWalletList(){

																$response = array();

																$post = $this->input->post();

																$user_id = $post['user_id'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
																	$fromDate = $post['fromDate'];
																	$toDate  =  $post['toDate'];
																	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																	$limit = $page_no * 50;



																	if($fromDate && $toDate){

																		$count = $this->db->order_by('created','desc')->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>1,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>1,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();
																	}
																	else{


																		$count = $this->db->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>1))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>1))->result_array();
																	}

																	$pages = ceil($count / 50);

																	$data = array();
																	if($userList)
																	{
																		foreach($userList as $key=>$list)
																		{	
																			$data[$key]['before_balance'] = $list['before_balance'];
																			$data[$key]['amount'] = $list['amount'];
																			$data[$key]['type'] = ($list['type'] == 1) ? 'CR' : 'DR';
																			$data[$key]['after_balance'] = $list['after_balance'];
																			$data[$key]['description'] = $list['description'];
																			$data[$key]['datetime'] = date('d-m-Y H:i:s',strtotime($list['created']));

																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data'=>$data,
																			'pages' => $pages,
																		);
																	}
																	else{

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! record not found.',
																		);
																	}
																}
																log_message('debug', 'Get Wallet List API Response - '.json_encode($response));	
																echo json_encode($response);

															}	



															public function getAepsWalletList(){

																$response = array();

																$post = $this->input->post();

																$user_id = $post['user_id'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
																	$fromDate = $post['fromDate'];
																	$toDate  =  $post['toDate'];
																	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																	$limit = $page_no * 50;

																	if($fromDate && $toDate){

																		$count = $this->db->order_by('created','desc')->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>2,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>2,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();
																	}
																	else{

																		$count = $this->db->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>2))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>2))->result_array();
																	}

																	$pages = ceil($count / 50);

																	$data = array();
																	if($userList)
																	{
																		foreach($userList as $key=>$list)
																		{	
																			$data[$key]['before_balance'] = $list['before_balance'];
																			$data[$key]['amount'] = $list['amount'];
																			$data[$key]['type'] = ($list['type'] == 1) ? 'CR' : 'DR';
																			$data[$key]['after_balance'] = $list['after_balance'];
																			$data[$key]['description'] = $list['description'];
																			$data[$key]['datetime'] = date('d-m-Y H:i:s',strtotime($list['created']));

																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data'=>$data,
																			'pages' => $pages,
																		);
																	}
																	else{

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! record not found.',
																		);
																	}
																}
																log_message('debug', 'Get Wallet List API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function getCommissionWalletList(){

																$response = array();

																$post = $this->input->post();

																$user_id = $post['user_id'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
																	$fromDate = $post['fromDate'];
																	$toDate  =  $post['toDate'];
																	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																	$limit = $page_no * 50;

																	if($fromDate && $toDate){

																		$count = $this->db->order_by('created','desc')->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>3,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>3,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();
																	}
																	else{

																		$count = $this->db->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>3))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>3))->result_array();
																	}

																	$pages = ceil($count / 50);

																	$data = array();
																	if($userList)
																	{
																		foreach($userList as $key=>$list)
																		{	
																			$data[$key]['before_balance'] = $list['before_balance'];
																			$data[$key]['amount'] = $list['amount'];
																			$data[$key]['type'] = ($list['type'] == 1) ? 'CR' : 'DR';
																			$data[$key]['after_balance'] = $list['after_balance'];
																			$data[$key]['description'] = $list['description'];
																			$data[$key]['datetime'] = date('d-m-Y H:i:s',strtotime($list['created']));

																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data'=>$data,
																			'pages' => $pages,
																		);
																	}
																	else{

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! record not found.',
																		);
																	}
																}
																log_message('debug', 'Get Wallet List API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function getPointWalletList(){

																$response = array();

																$post = $this->input->post();

																$user_id = $post['user_id'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
																	$fromDate = $post['fromDate'];
																	$toDate  =  $post['toDate'];
																	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																	$limit = $page_no * 50;

																	if($fromDate && $toDate){

																		$count = $this->db->order_by('created','desc')->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>4,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>4,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();
																	}
																	else{

																		$count = $this->db->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>4))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('member_id'=>$user_id,'wallet_type'=>4))->result_array();
																	}

																	$pages = ceil($count / 50);

																	$data = array();
																	if($userList)
																	{
																		foreach($userList as $key=>$list)
																		{	
																			$data[$key]['before_balance'] = $list['before_balance'];
																			$data[$key]['amount'] = $list['amount'];
																			$data[$key]['type'] = ($list['type'] == 1) ? 'CR' : 'DR';
																			$data[$key]['after_balance'] = $list['after_balance'];
																			$data[$key]['description'] = $list['description'];
																			$data[$key]['datetime'] = date('d-m-Y H:i:s',strtotime($list['created']));

																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data'=>$data,
																			'pages' => $pages,
																		);
																	}
																	else{

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! record not found.',
																		);
																	}
																}
																log_message('debug', 'Get Wallet List API Response - '.json_encode($response));	
																echo json_encode($response);

															}


															function amountCheckMainWallet($num)
															{
																$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																$min_amount = 10;
																if($walletSetting['main_wallet_min_bank_transfer'] != '*' && $walletSetting['main_wallet_min_bank_transfer'] != ''){
																	$min_amount = $walletSetting['main_wallet_min_bank_transfer'];
																}
																if ($num < $min_amount)
																{

																	$message = 'Sorry!! you can transfer minimum '.$min_amount;

																	$response = array(

																		'status' => 0,
																		'message'=> $message
																	);


																}
																else
																{
																	$response = array(
																		'status' => 1,
																		'message'=> 'Success'
																	);
																}

																echo json_encode($response);
															}


															public function mainWalletPayoutAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Fund Transfer API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
																$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
																$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
																$this->form_validation->set_rules('confirm_account_no', 'Confirm Account No.', 'required|xss_clean|matches[account_no]');
																$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
																$this->form_validation->set_rules('txn_pass', 'Transaction Password', 'required|xss_clean');
																$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter all required data'
																	);
																}
																else
																{   

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{


																		$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																		$min_amount = 10;
																		if($walletSetting['main_wallet_min_bank_transfer'] != '*' && $walletSetting['main_wallet_min_bank_transfer'] != ''){
																			$min_amount = $walletSetting['main_wallet_min_bank_transfer'];
																		}

																		if($post['amount'] < $min_amount){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! you can transfer minimum '.$min_amount
																			);
																		}
																		else{

																			$account_id = $post['userID'];
																			$activeService = $this->User->account_active_service($account_id);
																			if(!in_array(7, $activeService)){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! money transfer is not active.'
																				);
																			}	
																			else{


																				$get_kyc_status = $this->db->select('kyc_status.*')->join('kyc_status','kyc_status.id = users.kyc_status')->get_where('users',array('users.id'=>$account_id))->row_array();

																				if($get_kyc_status['id'] != 3){

																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry ! your kyc is not completed. Please submit your kyc.'
																					);		

																				}				
																				else{

																					$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();
																					$wallet_balance = $accountDetail['wallet_balance'];

																					if($accountDetail['transaction_password'] != do_hash($post['txn_pass'])){

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! transaction password is wrong.'
																						);		

																					}
																					else{

																						$transfer_amount = $post['amount'];

																						$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																						$daily_limit_error = 0;
																						if($walletSetting['main_wallet_daily_bank_transfer_limit'] != '*' && $walletSetting['main_wallet_daily_bank_transfer_limit'] != ''){
																							$daily_limit = $walletSetting['main_wallet_daily_bank_transfer_limit'];

										// get today transfer amount
																							$get_today_transfer_amount = $this->db->select('SUM(transfer_amount) as totalTransferAmount')->where_in('status',array(1,2,3))->get_where('user_fund_transfer',array('user_id'=>$account_id,'DATE(created)'=>date('Y-m-d'),'wallet_type'=>1))->row_array();
																							$today_transfer_amount = isset($get_today_transfer_amount['totalTransferAmount']) ? $get_today_transfer_amount['totalTransferAmount'] : 0 ;

																							if(($today_transfer_amount+$transfer_amount) > $daily_limit)
																							{
																								$daily_limit_error = 1;
																							}
																						}

									// get transfer surcharge
																						$surcharge_amount = $this->User->get_main_wallet_bank_transfer_surcharge($transfer_amount);

																						$total_wallet_deduct = $transfer_amount + $surcharge_amount;

						        	// check account balance
						        	                                                      $reserved_wallet_balance = $accountDetail['wallet_balance'] - $accountDetail['reserve_wallet_balance'] ;
						        	                                                      
																						if($daily_limit_error)
																						{
																							log_message('debug', 'Main Wallet Fund Transfer Daily Limit Error');	
																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! Your daily limit exceeded, please try again after 24 hours.'
																							);
																						}
																						elseif($accountDetail['is_main_wallet_block'] == 1){
					        		// save system log
																					//	log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Your fund is blocked for 2 Days.'
																						);
																					}
																						elseif($reserved_wallet_balance < $total_wallet_deduct)
																						{
																							log_message('debug', 'Main Wallet Fund Transfer Low Balance Error');	
																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! you have insufficient balance in your main wallet.'
																							);
																						}
																						else{


																							$encrypt_otp_code = $this->User->sendBankTransferOtp($post,$account_id);


																							$response = array(

																								'status'  => 1,
																								'message'	=> 'Otp sent to your registered email. Please verify.',
																								'encrypt_otp_code' => $encrypt_otp_code 

																							);
																						}

																					}
																				}	

																			}
																		}    

																	}
																}

																log_message('debug', 'Fund Transfer api Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function mainWalletPayoutOtpAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Fund Transfer Otp Auth API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('otp_code', 'Otp', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Otp is required'
																	);
																}
																else
																{  	

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{ 

																		$account_id = $post['userID'];
																		$activeService = $this->User->account_active_service($account_id);
																		if(!in_array(7, $activeService)){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! money transfer is not active.'
																			);
																		}	
																		else{


																			$get_kyc_status = $this->db->select('kyc_status.*')->join('kyc_status','kyc_status.id = users.kyc_status')->get_where('users',array('users.id'=>$account_id))->row_array();

																			if($get_kyc_status['id'] != 3){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! your kyc is not completed. Please submit your kyc.'
																				);		

																			}				
																			else{


																				$chk_otp = $this->db->get_where('users_otp',array('user_id'=>$account_id,'otp_code'=>$post['otp_code'],'status'=>0))->row_array();

																				if(!$chk_otp){

																					$resposne = array(

																						'status'  => 0,
																						'message' => 'Sorry!! otp not valid'
																					);	
																				}
																				else{

																					$this->db->where('id',$chk_otp['id']);
																					$this->db->update('users_otp',array('status'=>1));

																					$post_data = json_decode($chk_otp['json_post_data'],true);


																					$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();
																					$wallet_balance = $accountDetail['wallet_balance'];

																					if($accountDetail['transaction_password'] != do_hash($post_data['txn_pass'])){

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! transaction password is wrong.'
																						);		

																					}
																					else{

																						$transfer_amount = $post_data['amount'];

																						$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																						$daily_limit_error = 0;
																						if($walletSetting['main_wallet_daily_bank_transfer_limit'] != '*' && $walletSetting['main_wallet_daily_bank_transfer_limit'] != ''){
																							$daily_limit = $walletSetting['main_wallet_daily_bank_transfer_limit'];

											// get today transfer amount
																							$get_today_transfer_amount = $this->db->select('SUM(transfer_amount) as totalTransferAmount')->where_in('status',array(1,2,3))->get_where('user_fund_transfer',array('user_id'=>$account_id,'DATE(created)'=>date('Y-m-d'),'wallet_type'=>1))->row_array();
																							$today_transfer_amount = isset($get_today_transfer_amount['totalTransferAmount']) ? $get_today_transfer_amount['totalTransferAmount'] : 0 ;

																							if(($today_transfer_amount+$transfer_amount) > $daily_limit)
																							{
																								$daily_limit_error = 1;
																							}
																						}

										// get transfer surcharge
																						$surcharge_amount = $this->User->get_main_wallet_bank_transfer_surcharge($transfer_amount);

																						$total_wallet_deduct = $transfer_amount + $surcharge_amount;

							        	// check account balance
							        	                                                 $reserved_wallet_balance = $accountDetail['wallet_balance'] - $accountDetail['reserve_wallet_balance'] ;
							        	                                                 
																						if($daily_limit_error)
																						{
																							log_message('debug', 'Main Wallet Fund Transfer Daily Limit Error');	
																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! Your daily limit exceeded, please try again after 24 hours.'
																							);
																						}
																							elseif($accountDetail['is_main_wallet_block'] == 1){
					        		// save system log
																					//	log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Your fund is blocked for 2 Days.'
																						);
																					}
																						elseif($reserved_wallet_balance < $total_wallet_deduct)
																						{
																							log_message('debug', 'Main Wallet Fund Transfer Low Balance Error');	
																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! you have insufficient balance in your main wallet.'
																							);
																						}
																						else{

								            // save fund transfer request
																							$response = $this->Api_model->main_wallet_bank_transfer($post_data,$account_id);

																							log_message('debug', 'Main Wallet Transfer Fund Final API Response - '.json_encode($response));	

																							if($response['status'] == 1)
																							{	

																								$message = 'Congratulations!! your fund transfered successfully.';

																								$this->User->sendNotification($account_id,'Money Transfer',$message);

																								$response = array(
																									'status' => 1,
																									'message' => 'Congratulations!! your fund transfered successfully.'
																								);
																							}
																							elseif($response['status'] == 2)
																							{
																								$response = array(
																									'status' => 1,
																									'message' => 'Your fund transfer is in pending.'
																								);
																							}
																							else
																							{
																								$response = array(
																									'status' => 0,
																									'message' => 'Sorry!! your fund transfer is failed.'
																								);

																							}




																						}
																					}
																				}	

																			}
																		}    

																	}
																}

																log_message('debug', 'Fund Transfer api Response - '.json_encode($response));	
																echo json_encode($response);

															}


															public function mainWalletfundTransferHistory(){

																$response = array();

																$post = $this->input->post();
																$user_id = isset($post['user_id']) ? $post['user_id'] : 0;

																$user_id = $post['user_id'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$fromDate = $post['fromDate'];
																	$toDate = $post['toDate'];
																	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																	$limit = $page_no * 50;

																	if($fromDate && $toDate){

																		$count = $this->db->order_by('created','desc')->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>1,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>1,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->result_array();
																	}
																	else{

																		$count = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>1))->num_rows();

																		$limit_start = $limit - 50;

																		$limit_end = $limit; 

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>1))->result_array();
																	}

																	$pages = ceil($count / 50);

																	$data = array();
																	if($userList)
																	{
																		foreach($userList as $key=>$list)
																		{
																			$data[$key]['memberID'] = $list['memberID'];
																			$data[$key]['account_holder_name'] = $list['account_holder_name'];
																			$data[$key]['mobile'] = $list['mobile'];
																			$data[$key]['ifsc'] = $list['ifsc'];
																			$data[$key]['transfer_amount'] = $list['transfer_amount'].' /-';
																			$data[$key]['transaction_id'] = $list['transaction_id'];
																			$data[$key]['rrn'] = $list['rrn'];

																			if($list['status'] == 2) {
																				$data[$key]['status'] = 'Pending';
																			}
																			elseif($list['status'] == 3) {
																				$data[$key]['status'] = 'Success';
																			}
																			elseif($list['status'] == 4 || $list['status'] == 0) {
																				$data[$key]['status'] = 'Failed';
																			}

																			$data[$key]['date'] = date('d-M-Y H:i:s',strtotime($list['created']));

																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data'=>$data,
																			'pages' => $pages,
																		);
																	}
																	else{

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! record not found.',
																		);
																	}
																}
																log_message('debug', 'Main Wallet Fund Transfer List API Response - '.json_encode($response));	
																echo json_encode($response);

															}	



															function amountCheckAepsWallet($num)
															{
																$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																$min_amount = 10;
																if($walletSetting['aeps_wallet_min_bank_transfer'] != '*' && $walletSetting['aeps_wallet_min_bank_transfer'] != ''){
																	$min_amount = $walletSetting['aeps_wallet_min_bank_transfer'];
																}
																if ($num < $min_amount)
																{
																	$this->form_validation->set_message(
																		'amountCheckAepsWallet',
																		'The %s field must be grater than '.$min_amount
																	);
																	return FALSE;
																}
																else
																{
																	return TRUE;
																}
															}



															public function addAepsPayoutBenificary(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Add Benificiary API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'User ID', 'required');
																$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');     
																$this->form_validation->set_rules('bank_name', 'Bank Name', 'required|xss_clean');     
																$this->form_validation->set_rules('account_number', 'Account Number', 'required|xss_clean|numeric|min_length[10]');     
																$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean'); 

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter all required data'
																	);
																}
																else
																{   

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$account_id = $post['userID'];

																		$bene_data = array(

																			'user_id' => $account_id,
																			'account_holder_name' => $post['account_holder_name'],
																			'bank_name' => $post['bank_name'],
																			'account_no' => $post['account_number'],
																			'ifsc' => $post['ifsc'],
																			'encode_ban_id' => do_hash($post['account_number']),	
																			'status' => 1,
																			'created' => date('Y-m-d H:i:s')

																		);

																		$this->db->insert('user_aeps_payout_benificary',$bene_data);

																		$message = 'Congratulations!! beneficiary added succesfully.';

																		$this->User->sendNotification($account_id,'Add Beneficiary',$message);

																		$response = array(
																			'status' => 1,
																			'message' => 'Congratulations!! beneficiary added succesfully.'
																		);
																	}
																}


																log_message('debug', 'Add Benificary api Response - '.json_encode($response));	
																echo json_encode($response);

															}


															public function aepsPayoutBenificaryList()
															{
																$post = $this->input->post();
																log_message('debug', 'Benificary List API POST Data - '.json_encode($post));	
																$userID = isset($post['userID']) ? $post['userID'] : 0;

																$user_id = $post['userID'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$response = array();
				// check user valid or not
																	$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																	if($chk_user)
																	{
																		$benificiaryList = $this->db->order_by('created','desc')->get_where('user_aeps_payout_benificary',array('user_id'=>$userID))->result_array();

																		$data = array();
																		if($benificiaryList)
																		{
																			foreach ($benificiaryList as $key => $list) {

																				$data[$key]['bene_id'] = $list['id'];
																				$data[$key]['benificiary_name'] = $list['account_holder_name'];
																				$data[$key]['account_no'] = $list['account_no'];
																				$data[$key]['bank'] = $list['bank_name'];
																				$data[$key]['ifsc'] = $list['ifsc'];
																				$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));

																			}
																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data' => $data,
																		);	
																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => lang('USER_ID_ERROR')
																		);
																	}
																}
																log_message('debug', 'Benificary List API Response - '.json_encode($response));	
																echo json_encode($response);
															}



															public function aepsWalletPayoutAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Fund Transfer API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('bene_id', 'Benificary ID', 'required|xss_clean');
																$this->form_validation->set_rules('txn_pass', 'Transaction Password', 'required|xss_clean');
																$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter all required data'
																	);
																}
																else
																{   
																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$account_id = $post['userID'];
																		$activeService = $this->User->account_active_service($account_id);
																		if(!in_array(8, $activeService)){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! money transfer is not active.'
																			);
																		}	
																		else{


																			$get_kyc_status = $this->db->select('kyc_status.*')->join('kyc_status','kyc_status.id = users.kyc_status')->get_where('users',array('users.id'=>$account_id))->row_array();

																			if($get_kyc_status['id'] != 3){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! your kyc is not completed. Please submit your kyc.'
																				);		

																			}				
																			else{	

																				$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();

																				$get_beneficiary_detail = $this->db->get_where('user_aeps_payout_benificary',array('user_id'=>$account_id,'id'=>$post['bene_id']))->row_array();
																				if(!$get_beneficiary_detail){

																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! beneficiary is not exists.'
																					);

																				}
																				else{	

																					$post['mobile'] = $accountDetail['mobile'];
																					$post['account_holder_name'] = $get_beneficiary_detail['account_holder_name'];
																					$post['account_no'] = $get_beneficiary_detail['account_no'];
																					$post['ifsc'] = $get_beneficiary_detail['ifsc'];

																					$wallet_balance = $accountDetail['aeps_wallet_balance'];

																					if($accountDetail['transaction_password'] != do_hash($post['txn_pass'])){

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! transaction password is wrong.'
																						);		

																					}
																					else{


																						$transfer_amount = $post['amount'];

																						$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																						$min_amount = 10;
																						if($walletSetting['aeps_wallet_min_bank_transfer'] != '*' && $walletSetting['aeps_wallet_min_bank_transfer'] != ''){
																							$min_amount = $walletSetting['aeps_wallet_min_bank_transfer'];
																						}

																						if($transfer_amount < $min_amount){

																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! you can transfer minimum '.$min_amount
																							);		

																						}
																						else{

																							$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																							$min_wallet_bal = 0;
																							if($walletSetting['aeps_wallet_maintain_min'] != ''){
																								$min_wallet_bal = $walletSetting['aeps_wallet_maintain_min'];
																							}
																							$daily_limit_error = 0;
																							if($walletSetting['aeps_wallet_daily_bank_transfer_limit'] != '*' && $walletSetting['aeps_wallet_daily_bank_transfer_limit'] != ''){
																								$daily_limit = $walletSetting['aeps_wallet_daily_bank_transfer_limit'];

											// get today transfer amount
																								$get_today_transfer_amount = $this->db->select('SUM(transfer_amount) as totalTransferAmount')->where_in('status',array(1,2,3))->get_where('user_fund_transfer',array('user_id'=>$account_id,'DATE(created)'=>date('Y-m-d'),'wallet_type'=>2))->row_array();
																								$today_transfer_amount = isset($get_today_transfer_amount['totalTransferAmount']) ? $get_today_transfer_amount['totalTransferAmount'] : 0 ;

																								if(($today_transfer_amount+$transfer_amount) > $daily_limit)
																								{
																									log_message('debug', 'AEPS Fund Transfer Dailye Limit Error');	
																									$daily_limit_error = 1;
																								}
																							}

																							if($daily_limit_error){

																								$response = array(
																									'status' => 0,
																									'message' => 'Sorry!! you exceeded you daily transfer limit.'
																								);	

																							}
																							else{

											// get transfer surcharge
																								$surcharge_amount = $this->User->get_aeps_bank_transfer_surcharge($transfer_amount);

																								$total_wallet_deduct = $transfer_amount + $surcharge_amount;

																								$remaining_balance = $wallet_balance - $total_wallet_deduct;

								        	// check account balance
																								if($wallet_balance < $total_wallet_deduct)
																								{
																									log_message('debug', 'AEPS Fund Transfer Low Balance Error');	
																									$response = array(
																										'status' => 0,
																										'message' => 'Sorry!! you have insufficient balance in your aeps wallet.'
																									);
																								}
																								elseif($remaining_balance < $min_wallet_bal)
																								{
																									log_message('debug', 'AEPS Fund Transfer Minimum Balance Error');	
																									$response = array(
																										'status' => 0,
																										'message' => 'Sorry ! You have to maintain minimum balance in your wallet.'
																									);
																								}
																								else{


																									$encrypt_otp_code = $this->User->sendBankTransferOtp($post,$account_id);


																									$response = array(

																										'status'  => 1,
																										'message'	=> 'Otp sent to your registered email. Please verify',
																										'encrypt_otp_code' => $encrypt_otp_code,

																									); 


									    //         // save fund transfer request
									    //         $response = $this->Api_model->aeps_wallet_bank_transfer($post,$account_id);

									    //         log_message('debug', 'AEPS Transfer Fund Final API Response - '.json_encode($response));	

									    //         if($response['status'] == 1)
									    //         {	
									    //         	$message = 'Congratulations!! your fund transfered succesfully.';

				    					// 			$this->User->sendNotification($account_id,'Money Transfer',$message);

									    //         	$response = array(
													// 	'status' => 1,
													// 	'message' => 'Congratulations!! your fund transfered succesfully.'
													// );
									    //     	}
									    //     	elseif($response['status'] == 2)
									    //         {
									    //         	$response = array(
													// 	'status' => 1,
													// 	'message' => 'Your fund transfer is in pending.'
													// );
									    //     	}
									    //     	else
									    //     	{
									    //     		$response = array(
													// 	'status' => 0,
													// 	'message' => 'Sorry!! your fund transfer failed.'
													// );

									    //     	}

																								}
																							}

																						}    
																					}
																				}
																			}	

																		}    

																	}
																}

																log_message('debug', 'Fund Transfer api Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function aepsWalletPayoutOtpAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Fund Transfer Otp Auth API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('otp_code', 'Otp Code', 'required|xss_clean');

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Otp is required'
																	);
																}
																else
																{   

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$account_id = $post['userID'];
																		$activeService = $this->User->account_active_service($account_id);
																		if(!in_array(8, $activeService)){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! money transfer is not active.'
																			);
																		}	
																		else{


																			$get_kyc_status = $this->db->select('kyc_status.*')->join('kyc_status','kyc_status.id = users.kyc_status')->get_where('users',array('users.id'=>$account_id))->row_array();

																			if($get_kyc_status['id'] != 3){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! your kyc is not completed. Please submit your kyc.'
																				);		

																			}				
																			else{


																				$chk_otp = $this->db->get_where('users_otp',array('user_id'=>$account_id,'otp_code'=>$post['otp_code'],'status'=>0))->row_array();

																				if(!$chk_otp){

																					$response = array(

																						'status'  => 0,
																						'message' => 'Sorry!! otp not valid'	

																					);	
																				}
																				else{


																					$this->db->where('id',$chk_otp['id']);
																					$this->db->update('users_otp',array('status'=>1));

																					$post_data = json_decode($chk_otp['json_post_data'],true);	

																					$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();

																					$get_beneficiary_detail = $this->db->get_where('user_aeps_payout_benificary',array('user_id'=>$account_id,'id'=>$post_data['bene_id']))->row_array();
																					if(!$get_beneficiary_detail){

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! beneficiary is not exists.'
																						);

																					}
																					else{	

																						$post_data['mobile'] = $accountDetail['mobile'];
																						$post_data['account_holder_name'] = $get_beneficiary_detail['account_holder_name'];
																						$post_data['account_no'] = $get_beneficiary_detail['account_no'];
																						$post_data['ifsc'] = $get_beneficiary_detail['ifsc'];

																						$wallet_balance = $accountDetail['aeps_wallet_balance'];

																						if($accountDetail['transaction_password'] != do_hash($post_data['txn_pass'])){

																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! transaction password is wrong.'
																							);		

																						}
																						else{


																							$transfer_amount = $post_data['amount'];

																							$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																							$min_amount = 10;
																							if($walletSetting['aeps_wallet_min_bank_transfer'] != '*' && $walletSetting['aeps_wallet_min_bank_transfer'] != ''){
																								$min_amount = $walletSetting['aeps_wallet_min_bank_transfer'];
																							}

																							if($transfer_amount < $min_amount){

																								$response = array(
																									'status' => 0,
																									'message' => 'Sorry!! you can transfer minimum '.$min_amount
																								);		

																							}
																							else{

																								$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																								$min_wallet_bal = 0;
																								if($walletSetting['aeps_wallet_maintain_min'] != ''){
																									$min_wallet_bal = $walletSetting['aeps_wallet_maintain_min'];
																								}
																								$daily_limit_error = 0;
																								if($walletSetting['aeps_wallet_daily_bank_transfer_limit'] != '*' && $walletSetting['aeps_wallet_daily_bank_transfer_limit'] != ''){
																									$daily_limit = $walletSetting['aeps_wallet_daily_bank_transfer_limit'];

												// get today transfer amount
																									$get_today_transfer_amount = $this->db->select('SUM(transfer_amount) as totalTransferAmount')->where_in('status',array(1,2,3))->get_where('user_fund_transfer',array('user_id'=>$account_id,'DATE(created)'=>date('Y-m-d'),'wallet_type'=>2))->row_array();
																									$today_transfer_amount = isset($get_today_transfer_amount['totalTransferAmount']) ? $get_today_transfer_amount['totalTransferAmount'] : 0 ;

																									if(($today_transfer_amount+$transfer_amount) > $daily_limit)
																									{
																										log_message('debug', 'AEPS Fund Transfer Dailye Limit Error');	
																										$daily_limit_error = 1;
																									}
																								}

																								if($daily_limit_error){

																									$response = array(
																										'status' => 0,
																										'message' => 'Sorry!! you exceeded you daily transfer limit.'
																									);	

																								}
																								else{

												// get transfer surcharge
																									$surcharge_amount = $this->User->get_aeps_bank_transfer_surcharge($transfer_amount);

																									$total_wallet_deduct = $transfer_amount + $surcharge_amount;

																									$remaining_balance = $wallet_balance - $total_wallet_deduct;

									        	// check account balance
																									if($wallet_balance < $total_wallet_deduct)
																									{
																										log_message('debug', 'AEPS Fund Transfer Low Balance Error');	
																										$response = array(
																											'status' => 0,
																											'message' => 'Sorry!! you have insufficient balance in your aeps wallet.'
																										);
																									}
																									elseif($remaining_balance < $min_wallet_bal)
																									{
																										log_message('debug', 'AEPS Fund Transfer Minimum Balance Error');	
																										$response = array(
																											'status' => 0,
																											'message' => 'Sorry ! You have to maintain minimum balance in your wallet.'
																										);
																									}
																									else{



										            // save fund transfer request
																										$response = $this->Api_model->aeps_wallet_bank_transfer($post_data,$account_id);

																										log_message('debug', 'AEPS Transfer Fund Final API Response - '.json_encode($response));	

																										if($response['status'] == 1)
																										{	
																											$message = 'Congratulations!! your fund transfered succesfully.';

																											$this->User->sendNotification($account_id,'Money Transfer',$message);

																											$response = array(
																												'status' => 1,
																												'message' => 'Congratulations!! your fund transfered succesfully.'
																											);
																										}
																										elseif($response['status'] == 2)
																										{
																											$response = array(
																												'status' => 1,
																												'message' => 'Your fund transfer is in pending.'
																											);
																										}
																										else
																										{
																											$response = array(
																												'status' => 0,
																												'message' => 'Sorry!! your fund transfer failed.'
																											);

																										}

																									}
																								}

																							}
																						}    
																					}
																				}
																			}	

																		}    

																	}
																}

																log_message('debug', 'Fund Transfer api Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function aepsWalletfundTransferHistory(){

																$response = array();

																$post = $this->input->post();
																$user_id = isset($post['user_id']) ? $post['user_id'] : 0;


																$user_id = $post['user_id'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$fromDate = $post['fromDate'];
																	$toDate = $post['toDate'];
																	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																	$limit = $page_no * 50;

																	if($fromDate && $toDate){

																		$count = $this->db->order_by('created','desc')->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>2,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>2,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->result_array();
																	}
																	else{

																		$count = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>2))->num_rows();

																		$limit_start = $limit - 50;

																		$limit_end = $limit; 

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>2))->result_array();
																	}

																	$pages = ceil($count / 50);

																	$data = array();
																	if($userList)
																	{
																		foreach($userList as $key=>$list)
																		{
																			$data[$key]['memberID'] = $list['memberID'];
																			$data[$key]['account_holder_name'] = $list['account_holder_name'];
																			$data[$key]['mobile'] = $list['mobile'];
																			$data[$key]['ifsc'] = $list['ifsc'];
																			$data[$key]['transfer_amount'] = $list['transfer_amount'].' /-';
																			$data[$key]['transaction_id'] = $list['transaction_id'];
																			$data[$key]['rrn'] = $list['rrn'];

																			if($list['status'] == 2) {
																				$data[$key]['status'] = 'Pending';
																			}
																			elseif($list['status'] == 3) {
																				$data[$key]['status'] = 'Success';
																			}
																			elseif($list['status'] == 4 || $list['status'] == 0) {
																				$data[$key]['status'] = 'Failed';
																			}

																			$data[$key]['date'] = date('d-M-Y H:i:s',strtotime($list['created']));

																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data'=>$data,
																			'pages' => $pages,
																		);
																	}
																	else{

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! record not found.',
																		);
																	}
																}
																log_message('debug', 'Main Wallet Fund Transfer List API Response - '.json_encode($response));	
																echo json_encode($response);

															}


															function amountCheckCommissionWallet($num)
															{
																$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																$min_amount = 10;
																if($walletSetting['commission_wallet_min_bank_transfer'] != '*' && $walletSetting['commission_wallet_min_bank_transfer'] != ''){
																	$min_amount = $walletSetting['commission_wallet_min_bank_transfer'];
																}
																if ($num < $min_amount)
																{
																	$this->form_validation->set_message(
																		'amountCheckCommissionWallet',
																		'The %s field must be grater than '.$min_amount
																	);
																	return FALSE;
																}
																else
																{
																	return TRUE;
																}
															}


															public function commissionWalletPayoutAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Fund Transfer API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
																$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
																$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
																$this->form_validation->set_rules('confirm_account_no', 'Confirm Account No.', 'required|xss_clean|matches[account_no]');
																$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
																$this->form_validation->set_rules('txn_pass', 'Transaction Password', 'required|xss_clean');
																$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter all required data'
																	);
																}
																else
																{   

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$account_id = $post['userID'];
																		$activeService = $this->User->account_active_service($account_id);
																		if(!in_array(9, $activeService)){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! money transfer is not active.'
																			);
																		}	
																		else{


																			$get_kyc_status = $this->db->select('kyc_status.*')->join('kyc_status','kyc_status.id = users.kyc_status')->get_where('users',array('users.id'=>$account_id))->row_array();

																			if($get_kyc_status['id'] != 3){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! your kyc is not completed. Please submit your kyc.'
																				);		

																			}				
																			else{


																				$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																				$min_amount = 10;
																				if($walletSetting['commission_wallet_min_bank_transfer'] != '*' && $walletSetting['commission_wallet_min_bank_transfer'] != ''){
																					$min_amount = $walletSetting['commission_wallet_min_bank_transfer'];
																				}

																				if($post['amount'] < $min_amount){

																					$response = array(

																						'status' => 0,
																						'message'=>'Sorry !! you can transfer minimum '.$min_amount

																					);

																				}
																				else{
																					$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();
																					$wallet_balance = $accountDetail['commision_wallet_balance'];

																					if($accountDetail['transaction_password'] != do_hash($post['txn_pass'])){

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! transaction password is wrong.'
																						);		

																					}
																					else{


																						$transfer_amount = $post['amount'];

																						$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																						$daily_limit_error = 0;
																						if($walletSetting['commission_wallet_daily_bank_transfer_limit'] != '*' && $walletSetting['commission_wallet_daily_bank_transfer_limit'] != ''){
																							$daily_limit = $walletSetting['commission_wallet_daily_bank_transfer_limit'];

										// get today transfer amount
																							$get_today_transfer_amount = $this->db->select('SUM(transfer_amount) as totalTransferAmount')->where_in('status',array(1,2,3))->get_where('user_fund_transfer',array('user_id'=>$account_id,'DATE(created)'=>date('Y-m-d'),'wallet_type'=>3))->row_array();
																							$today_transfer_amount = isset($get_today_transfer_amount['totalTransferAmount']) ? $get_today_transfer_amount['totalTransferAmount'] : 0 ;

																							if(($today_transfer_amount+$transfer_amount) > $daily_limit)
																							{
																								log_message('debug', 'Commission Fund Transfer Dailye Limit Error');	
																								$daily_limit_error = 1;
																							}
																						}

																						if($daily_limit_error){

																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! you exceeded your daily transfer limit.'
																							);
																						}
																						else{
										// get transfer surcharge
																							$surcharge_amount = $this->User->get_commission_bank_transfer_surcharge($transfer_amount);

																							$total_wallet_deduct = $transfer_amount + $surcharge_amount;

							        	// check account balance
																							if($wallet_balance < $total_wallet_deduct)
																							{
																								log_message('debug', 'Commission Fund Transfer Low Balance Error');	
																								$response = array(
																									'status' => 0,
																									'message' => 'Sorry!! you have insufficient balance in your commission wallet.'
																								);
																							}
																							else{


																								$encrypt_otp_code = $this->User->sendBankTransferOtp($post,$account_id);


																								$response = array(

																									'status'  => 1,
																									'message'	=> 'Otp sent to your registered email. Please verify.',
																									'encrypt_otp_code' => $encrypt_otp_code

																								);

								    //         // save fund transfer request
								    //         $response = $this->Api_model->commission_wallet_bank_transfer($post,$account_id);

								    //         log_message('debug', 'Commission Transfer Fund Final API Response - '.json_encode($response));	

								    //         if($response['status'] == 1)
								    //         {	
								    //         	$message = 'Congratulations!! your fund transfered succesfully.';

				    				// 			$this->User->sendNotification($account_id,'Money Transfer',$message);

								    //         	$response = array(
												// 	'status' => 1,
												// 	'message' => 'Congratulations!! your fund transfered succesfully.'
												// );
								    //     	}
								    //     	elseif($response['status'] == 2)
								    //         {
								    //         	$response = array(
												// 	'status' => 1,
												// 	'message' => 'Your fund transfer is in pending.'
												// );
								    //     	}
								    //     	else
								    //     	{
								    //     		$response = array(
												// 	'status' => 0,
												// 	'message' => 'Sorry!! your fund transfer is failed.'
												// );

								    //     	}


																							}
																						}

																					}
																				}
																			}	

																		}    

																	}
																}

																log_message('debug', 'Fund Transfer api Response - '.json_encode($response));	
																echo json_encode($response);

															}




															public function commissionWalletPayoutOtpAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Fund Transfer Otp Auth API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('otp_code', 'Otp', 'required|xss_clean');

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Otp is required'
																	);
																}
																else
																{   

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{


																		$account_id = $post['userID'];
																		$activeService = $this->User->account_active_service($account_id);
																		if(!in_array(9, $activeService)){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! money transfer is not active.'
																			);
																		}	
																		else{


																			$get_kyc_status = $this->db->select('kyc_status.*')->join('kyc_status','kyc_status.id = users.kyc_status')->get_where('users',array('users.id'=>$account_id))->row_array();

																			if($get_kyc_status['id'] != 3){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry ! your kyc is not completed. Please submit your kyc.'
																				);		

																			}				
																			else{


																				$chk_otp = $this->db->get_where('users_otp',array('user_id'=>$account_id,'otp_code'=>$post['otp_code'],'status'=>0))->row_array();

																				if(!$chk_otp){

																					$response = array(
																						'status'  => 0,
																						'message'	=> 'Sorry!! Otp is not valid.' 
																					);	
																				}
																				else{

																					$this->db->where('id',$chk_otp['id']);
																					$this->db->update('users_otp',array('status'=>1));

																					$post_data = json_decode($chk_otp['json_post_data'],true);

																					$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();
																					$wallet_balance = $accountDetail['commision_wallet_balance'];

																					if($accountDetail['transaction_password'] != do_hash($post_data['txn_pass'])){

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! transaction password is wrong.'
																						);		

																					}
																					else{


																						$transfer_amount = $post_data['amount'];

																						$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																						$daily_limit_error = 0;
																						if($walletSetting['commission_wallet_daily_bank_transfer_limit'] != '*' && $walletSetting['commission_wallet_daily_bank_transfer_limit'] != ''){
																							$daily_limit = $walletSetting['commission_wallet_daily_bank_transfer_limit'];

											// get today transfer amount
																							$get_today_transfer_amount = $this->db->select('SUM(transfer_amount) as totalTransferAmount')->where_in('status',array(1,2,3))->get_where('user_fund_transfer',array('user_id'=>$account_id,'DATE(created)'=>date('Y-m-d'),'wallet_type'=>3))->row_array();
																							$today_transfer_amount = isset($get_today_transfer_amount['totalTransferAmount']) ? $get_today_transfer_amount['totalTransferAmount'] : 0 ;

																							if(($today_transfer_amount+$transfer_amount) > $daily_limit)
																							{
																								log_message('debug', 'Commission Fund Transfer Dailye Limit Error');	
																								$daily_limit_error = 1;
																							}
																						}

																						if($daily_limit_error){

																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! you exceeded your daily transfer limit.'
																							);
																						}
																						else{
											// get transfer surcharge
																							$surcharge_amount = $this->User->get_commission_bank_transfer_surcharge($transfer_amount);

																							$total_wallet_deduct = $transfer_amount + $surcharge_amount;

								        	// check account balance
																							if($wallet_balance < $total_wallet_deduct)
																							{
																								log_message('debug', 'Commission Fund Transfer Low Balance Error');	
																								$response = array(
																									'status' => 0,
																									'message' => 'Sorry!! you have insufficient balance in your commission wallet.'
																								);
																							}
																							else{


								        		// save fund transfer request
																								$response = $this->Api_model->commission_wallet_bank_transfer($post_data,$account_id);

																								log_message('debug', 'Commission Transfer Fund Final API Response - '.json_encode($response));	

																								if($response['status'] == 1)
																								{	
																									$message = 'Congratulations!! your fund transfered succesfully.';

																									$this->User->sendNotification($account_id,'Money Transfer',$message);

																									$response = array(
																										'status' => 1,
																										'message' => 'Congratulations!! your fund transfered succesfully.'
																									);
																								}
																								elseif($response['status'] == 2)
																								{
																									$response = array(
																										'status' => 1,
																										'message' => 'Your fund transfer is in pending.'
																									);
																								}
																								else
																								{
																									$response = array(
																										'status' => 0,
																										'message' => 'Sorry!! your fund transfer is failed.'
																									);

																								}



																							}
																						}

																					}
																				}
																			}	

																		}    

																	}
																}

																log_message('debug', 'Fund Transfer OTP api Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function commissionWalletfundTransferHistory(){

																$response = array();

																$post = $this->input->post();
																$user_id = isset($post['user_id']) ? $post['user_id'] : 0;

																$user_id = $post['user_id'];

																$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																$password = isset($userData['password']) ? $userData['password'] : '';

																$header_data = apache_request_headers();

																$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																
																$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																
																$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																	$response = array(
																		'status' => 0,
																		'message' => 'Session out.Please Login Again.'
																	);
																}
																else{

																	$fromDate = $post['fromDate'];
																	$toDate = $post['toDate'];

																	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
																	$limit = $page_no * 50;

																	if($fromDate && $toDate){

																		$count = $this->db->order_by('created','desc')->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>3,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->num_rows();

																		$limit_start = $limit - 50; 

																		$limit_end = $limit;

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>3,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->result_array();
																	}
																	else{

																		$count = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>3))->num_rows();

																		$limit_start = $limit - 50;

																		$limit_end = $limit; 

																		$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('user_id'=>$user_id,'wallet_type'=>3))->result_array();
																	}

																	$pages = ceil($count / 50);

																	$data = array();
																	if($userList)
																	{
																		foreach($userList as $key=>$list)
																		{
																			$data[$key]['memberID'] = $list['memberID'];
																			$data[$key]['account_holder_name'] = $list['account_holder_name'];
																			$data[$key]['mobile'] = $list['mobile'];
																			$data[$key]['ifsc'] = $list['ifsc'];
																			$data[$key]['transfer_amount'] = $list['transfer_amount'].' /-';
																			$data[$key]['transaction_id'] = $list['transaction_id'];
																			$data[$key]['rrn'] = $list['rrn'];

																			if($list['status'] == 2) {
																				$data[$key]['status'] = 'Pending';
																			}
																			elseif($list['status'] == 3) {
																				$data[$key]['status'] = 'Success';
																			}
																			elseif($list['status'] == 4 || $list['status'] == 0) {
																				$data[$key]['status'] = 'Failed';
																			}

																			$data[$key]['date'] = date('d-M-Y H:i:s',strtotime($list['created']));

																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data'=>$data,
																			'pages' => $pages,
																		);
																	}
																	else{

																		$response = array(
																			'status' => 0,
																			'message' => 'Sorry!! record not found.',
																		);
																	}
																}
																log_message('debug', 'Main Wallet Fund Transfer List API Response - '.json_encode($response));	
																echo json_encode($response);

															}



															public function userUplineDetail(){

																$response = array();
																$post = $this->input->post();
																log_message('debug', 'User Upline Detail API Response Post Data - '.json_encode($post));	
																$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																$this->form_validation->set_rules('user_id', 'User Id', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'User Id Not Valid'
																	);
																}
																else
																{
																	$userID = $post['user_id'];

																	$user_id = $post['user_id'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

		        // check user credential
																		$chk_user_credential =$this->db->query("SELECT * FROM tbl_users WHERE (id = '$userID') and role_id = 2")->row_array();
																		if(!$chk_user_credential)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'User Id Not Valid'
																			);

																		}
																		else
																		{
																			$get_user_data =$this->db->query("SELECT * FROM tbl_users WHERE (id = '$userID') and role_id = 2")->row_array();
																			$is_active = isset($get_user_data['is_active']) ? $get_user_data['is_active'] : 0 ;
																			if(!$is_active)
																			{
																				$response = array(
																					'status' => 0,
																					'message' => lang('PROFILE_ACTIVE_ERROR')
																				);
																			}
																			else
																			{   

																				$sql = "SELECT a.*,c.name as sponser_name,c.user_code as sponser_code,c.mobile as sponser_mobile FROM tbl_users as a INNER JOIN tbl_member_tree as b ON a.id = b.member_id INNER JOIN tbl_users as c ON c.id = b.reffrel_id where  a.role_id = 2 and a.id = ".$userID." ";

																				$sponser = $this->db->query($sql)->row_array();


																				$response = array(
																					'status' => 1,
																					'message' => 'Success',
																					'upline_user_code'=>$sponser['sponser_code'],
																					'upline_name' => $sponser['sponser_name'],
																					'upline_mobile'=>$sponser['sponser_mobile']

																				);
																			}
																		}

																	}
																}
																log_message('debug', 'User Upline Detail API Response - '.json_encode($response));	
																echo json_encode($response);

															}




															public function mainWalletTransferAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Main Wallet Transfer API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('member_id', 'MemberID', 'required');
																$this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
																$this->form_validation->set_rules('transaction_password', 'Transaction Password', 'required');

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter all required data'
																	);
																}
																else
																{   

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$member_id = trim($post['member_id']);

																		$chk_member_id = $this->db->query("SELECT * FROM tbl_users WHERE user_code = '$member_id' OR mobile = '$member_id'")->num_rows();

																		if(!$chk_member_id){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! MemberID is not valid.'
																			);

																		}	
																		else{

																			$transfer_amount = $post['amount'];
																			if($transfer_amount < 1)
																			{
																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! Transfer amount is not valid.'
																				);
																			}
																			else
																			{
				        	// get fund transfer percentage
																				$get_fund_transfer_charge = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																				$transfer_charge_percentage = isset($get_fund_transfer_charge['main_wallet_transfer_charge']) ? $get_fund_transfer_charge['main_wallet_transfer_charge'] : 0 ;
																				$fund_transfer_charge = 0;
																				if($transfer_charge_percentage)
																				{
																					$fund_transfer_charge = round(($transfer_charge_percentage/100)*$transfer_amount,2);
																				}

																				$get_wallet_balance = $this->db->get_where('users',array('id'=>$post['userID']))->row_array();

																				$wallet_balance = isset($get_wallet_balance['wallet_balance']) ? $get_wallet_balance['wallet_balance'] : 0;

																				$final_transfer_amount = $fund_transfer_charge + $post['amount'];
																				
																				 $reserved_wallet_balance = $get_wallet_balance['wallet_balance'] - $get_wallet_balance['reserve_wallet_balance'] ;
																				 

																					if($get_wallet_balance['is_main_wallet_block'] == 1){
					        		// save system log
																					//	log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Your fund is blocked for 2 Days.'
																						);
																					}
																					
																				
																				elseif($reserved_wallet_balance < $final_transfer_amount){

																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! you have insufficient balance in your wallet.'
																					);

																				}
																				
																				else{

																					if($get_wallet_balance['transaction_password'] != do_hash($post['transaction_password'])){

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! transaction password is wrong.'
																						);		

																					}
																					else{

																						$response = $this->Api_model->walletTransferAuthentication($post,$post['userID']);

																						log_message('debug', 'Wallet Transfer Auth Response - '.json_encode($response));	

																						if($response['status'])
																						{	
																							$message = 'Congratulations!! your fund transfered succesfully.';

																							$this->User->sendNotification($post['userID'],'Wallet Transfer',$message);

																							$response = array(
																								'status' => 1,
																								'message' => 'Congratulations!! amount transfered succesfully.'
																							);

																						}
																						else
																						{
																							$response = array(
																								'status' => 0,
																								'message' => 'Sorry!! transfer failed.'
																							);

																						}
																					}
																				}
																			}
																		}    

																	}
																}

																log_message('debug', 'Main Wallet Transfer api Response - '.json_encode($response));	
																echo json_encode($response);

															}




															function aepswalletAmountCheck($num)
															{
																$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																$min_amount = 10;
																if($walletSetting['aeps_wallet_min_wallet_to_wallet_transfer'] != '*' && $walletSetting['aeps_wallet_min_wallet_to_wallet_transfer'] != ''){
																	$min_amount = $walletSetting['aeps_wallet_min_wallet_to_wallet_transfer'];
																}
																if ($num < $min_amount)
																{
																	$this->form_validation->set_message(
																		'walletAmountCheck',
																		'The %s field must be grater than '.$min_amount
																	);
																	return FALSE;
																}
																else
																{
																	return TRUE;
																}
															}




															public function aepsWalletTransferAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Aeps Wallet Transfer API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_aepswalletAmountCheck');
																$this->form_validation->set_rules('transaction_password', 'Transaction Password', 'required');
																$this->form_validation->set_rules('description', 'Description', 'required');

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter all required data'
																	);
																}
																else
																{   

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$account_id = $post['userID'];

																		$get_wallet_balance = $this->db->get_where('users',array('id'=>$account_id))->row_array();

																		$wallet_balance = isset($get_wallet_balance['aeps_wallet_balance']) ? $get_wallet_balance['aeps_wallet_balance'] : 0;

																		$transfer_amount = $post['amount'];

																		$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																		$transfer_charge_percentage = $walletSetting['aeps_wallet_transfer_charge'];
																		$min_wallet_bal = 0;
																		if($walletSetting['aeps_wallet_maintain_min'] != ''){
																			$min_wallet_bal = $walletSetting['aeps_wallet_maintain_min'];
																		}
																		$charge_amount = 0;
																		if($transfer_charge_percentage)
																		{
																			$charge_amount = round(($transfer_charge_percentage/100)*$transfer_amount,2);
																		}

																		$final_transfer_amount = $transfer_amount + $charge_amount;

																		$remaining_balance = $wallet_balance - $final_transfer_amount;

																		if($wallet_balance < $final_transfer_amount){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! you have insufficient balance in your wallet.'
																			);

																		}
																		elseif($remaining_balance < $min_wallet_bal){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry ! You have to maintain minimum balance in your wallet.'
																			);

																		}
																		else{

																			if($get_wallet_balance['transaction_password'] != do_hash($post['transaction_password'])){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! your transaction password is wrong.'
																				);		

																			}
																			else{

																				$response = $this->Api_model->aepsToMainWalletTransfer($post,$account_id);

																				log_message('debug', 'Wallet Transfer Auth Response - '.json_encode($response));	

																				if($response['status'])
																				{	

																					$message = 'Congratulations!! amount transfered succesfully.';

																					$this->User->sendNotification($account_id,'Wallet Transfer',$message);

																					$response = array(
																						'status' => 1,
																						'message' => 'Congratulations!! amount transfered succesfully.'
																					);

																				}
																				else
																				{
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! transfer failed.'
																					);

																				}
																			}
																		}    

																	}
																}

																log_message('debug', 'Aeps Wallet Transfer api Response - '.json_encode($response));	
																echo json_encode($response);

															}



															function comwalletAmountCheck($num)
															{
																$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																$min_amount = 10;
																if($walletSetting['commission_wallet_min_wallet_to_wallet_transfer'] != '*' && $walletSetting['commission_wallet_min_wallet_to_wallet_transfer'] != ''){
																	$min_amount = $walletSetting['commission_wallet_min_wallet_to_wallet_transfer'];
																}
																if ($num < $min_amount)
																{
																	$this->form_validation->set_message(
																		'walletAmountCheck',
																		'The %s field must be grater than '.$min_amount
																	);
																	return FALSE;
																}
																else
																{
																	return TRUE;
																}
															}


															public function commissionWalletTransferAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Commission Wallet Transfer API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_comwalletAmountCheck');
																$this->form_validation->set_rules('transaction_password', 'Transaction Password', 'required');
																$this->form_validation->set_rules('description', 'Description', 'required');

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter all required data'
																	);
																}
																else
																{   

																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{

																		$account_id = $post['userID'];

																		$get_wallet_balance = $this->db->get_where('users',array('id'=>$account_id))->row_array();

																		$wallet_balance = isset($get_wallet_balance['commision_wallet_balance']) ? $get_wallet_balance['commision_wallet_balance'] : 0;

																		$transfer_amount = $post['amount'];

																		$walletSetting = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																		$transfer_charge_percentage = $walletSetting['commission_wallet_transfer_charge'];
																		$charge_amount = 0;
																		if($transfer_charge_percentage)
																		{
																			$charge_amount = round(($transfer_charge_percentage/100)*$transfer_amount,2);
																		}

																		$final_transfer_amount = $transfer_amount + $charge_amount;

																		if($wallet_balance < $final_transfer_amount){

																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! you have insufficient balance in your wallet.'
																			);

																		}
																		else{

																			if($get_wallet_balance['transaction_password'] != do_hash($post['transaction_password'])){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! transaction password wrong.'
																				);		

																			}
																			else{

																				$response = $this->Api_model->comToMainWalletTransfer($post,$account_id);

																				log_message('debug', 'Wallet Transfer Auth Response - '.json_encode($response));	

																				if($response['status'])
																				{

																					$message = 'Congratulations!! amount transfered succesfully.';

																					$this->User->sendNotification($account_id,'Wallet Transfer',$message);

																					$response = array(
																						'status' => 1,
																						'message' => 'Congratulations!! amount transfered succesfully.'
																					);

																				}
																				else
																				{
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! transfer failed.'
																					);

																				}
																			}

																		}    

																	}
																}

																log_message('debug', 'Commission Wallet Transfer api Response - '.json_encode($response));	
																echo json_encode($response);

															}




															public function scanMainWalletTransferAuth(){

			//check for foem validation
																$response = array();
																$post = $this->input->post();
																log_message('debug', 'Main Wallet Transfer API Post Data - '.json_encode($post));
																$this->load->library('form_validation');

																$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																$this->form_validation->set_rules('encoded_member_id', 'Encoded MemberID', 'required');
																$this->form_validation->set_rules('amount', 'Amount', 'required|numeric');

																if ($this->form_validation->run() == FALSE) {

																	$response = array(
																		'status' => 0,
																		'message' => 'Please Enter all required data'
																	);
																}
																else
																{   
																	$user_id = $post['userID'];

																	$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																	$password = isset($userData['password']) ? $userData['password'] : '';

																	$header_data = apache_request_headers();

																	$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																	
																	$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																	
																	$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																	$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																	if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																		$response = array(
																			'status' => 0,
																			'message' => 'Session out.Please Login Again.'
																		);
																	}
																	else{


																		if($post['amount'] < 1){

																			$response = array(

																				'status' => 0,
																				'message'=>'Please enter valid amount.'	

																			);

																		}
																		else{
																			$chk_member_id = $this->db->get_where('users',array('qr_unique_id'=>$post['encoded_member_id']))->num_rows();

																			if(!$chk_member_id){

																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! MemberID is not valid.'
																				);

																			}	
																			else{

																				$account_id = $post['userID'];
				        	// get fund transfer percentage
																				$get_fund_transfer_charge = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																				$fund_transfer_charge = isset($get_fund_transfer_charge['wallet_transfer_charge']) ? $get_fund_transfer_charge['wallet_transfer_charge'] : 0 ;

																				$get_wallet_balance = $this->db->get_where('users',array('id'=>$post['userID']))->row_array();

																				$wallet_balance = isset($get_wallet_balance['wallet_balance']) ? $get_wallet_balance['wallet_balance'] : 0;

																				$final_transfer_amount = $fund_transfer_charge + $post['amount'];
																				
																				
																				 $reserved_wallet_balance = $get_wallet_balance['wallet_balance'] - $get_wallet_balance['reserve_wallet_balance'] ;
                                                                                
                                                                                
                                                                                if($get_wallet_balance['is_main_wallet_block'] == 1){
					        		// save system log
																					//	log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Your fund is blocked for 2 Days.'
																						);
																					}
																					
																				elseif($reserved_wallet_balance < $final_transfer_amount){

																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! you have insufficient balance in your wallet.'
																					);

																				}
																				else{

																					$response = $this->Api_model->scanWalletTransferAuthentication($post,$account_id);

																					log_message('debug', 'Wallet Transfer Auth Response - '.json_encode($response));	

																					if($response['status'])
																					{

																						$message = 'Congratulations!! amount transfered succesfully.';

																						$this->User->sendNotification($account_id,'Wallet Transfer',$message);

																						$response = array(
																							'status' => 1,
																							'message' => 'Congratulations!! amount transfered succesfully.'
																						);

																					}
																					else
																					{
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! transfer failed.'
																						);

																					}

																				}
																			}
																		}    

																	}
																}

																log_message('debug', 'Main Wallet Transfer api Response - '.json_encode($response));	
																echo json_encode($response);

															}








															public function getCategoryList()
															{
																$response = array();
																$category = $this->db->get_where('category',array('parent_id'=>0))->result_array();

																$data = array();
																if($category)
																{
																	foreach ($category as $key => $value) {
																		$data[$key]['catID'] = $value['id'];
																		$data[$key]['title'] = $value['title'];
																		$data[$key]['slug'] = $value['slug'];
																	}
																}

																$response = array(
																	'status' => 1,
																	'message' => 'Success',
																	'data' => $data
																);
																echo json_encode($response);
															}



															public function getSubCategoryList()
															{
																$post = $this->input->post();
																log_message('debug', 'Subcategory List Get Data - '.json_encode($post));	

																$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																$this->form_validation->set_rules('catID', 'Category ID', 'required|xss_clean');
																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => lang('LOGIN_VALID_FAILED')
																	);
																}

																else{

																	$catID = isset($post['catID']) ? $post['catID'] : 0;
																	$response = array();

																	if($catID > 0)
																	{
																		$sub_category = $this->db->get_where('category',array('parent_id'=>$catID))->result_array();

																		$data = array();
																		if($sub_category)
																		{
																			foreach ($sub_category as $key => $value) {
																				$data[$key]['catID'] = $value['id'];
																				$data[$key]['title'] = $value['title'];
																				$data[$key]['slug'] = $value['slug'];
																				$data[$key]['category_icon'] = isset($value['category_icon']) ? base_url($value['category_icon']) :'';
																			}
																		}

																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data' => $data
																		);

																	}
																	else
																	{
																		$response = array(
																			'status' => 0,
																			'message' => lang('CATEGORY_ID_ERROR')
																		);
																	}
																}
																log_message('debug', 'Subcategory List Response - '.json_encode($response));	
																echo json_encode($response);
															}


															public function getProductList()
															{
																$post = $this->input->post();
																log_message('debug', 'Product List Api  Data - '.json_encode($post));	


																$catID = isset($post['catID']) ? $post['catID'] : 0;
																$response = array();
																$today_date = date('Y-m-d');
																if($catID > 0)
																{
																	$product =  $this->db->select('products.id,products.product_name,products.slug,products.sku,products.hsncode,products.tax_rule_id,products.price,products.special_price,products.special_price_to')->join('product_category','products.id = product_category.product_id','left')->get_where('products',array('product_category.category_id'=>$catID))->result_array();



																	$data = array();
																	if($product)
																	{	
																		foreach ($product as $key => $value) {

																			if($value['special_price'] && $value['special_price_to'] >= $today_date)
																			{
																				$data[$key]['is_special_price'] = 1;
																			}
																			else
																			{
																				$data[$key]['is_special_price'] = 0;
																			}


																			$get_product_img = $this->db->select('image_path,file_name')->get_where('product_images',array('product_id'=>$value['id'],'is_base'=>1))->row_array();
																			$product_img = isset($get_product_img['file_name']) ? 'media/product_images/thumbnail-400x400/'.$get_product_img['file_name'] : 'skin/front/images/product-default-img.png' ;
																			$data[$key]['img'] = $product_img;
																			$data[$key]['proID'] = $value['id'];
																			$data[$key]['name'] = substr($value['product_name'],0,50);
																			$data[$key]['slug'] = $value['slug'];
																			$data[$key]['sku'] = $value['sku'];
																			$data[$key]['hsncode'] = $value['hsncode'];
																			$data[$key]['price'] = $value['price'];
																			$data[$key]['special_price'] = $value['special_price'];

																		}
																	}

																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data' => $data
																	);

																}

																elseif($catID == 0){

																	$product =  $this->db->get('products')->result_array();

																	$data = array();
																	if($product)
																	{
																		foreach ($product as $key => $value) {
																			if($value['special_price'] && $value['special_price_to'] >= $today_date)
																			{
																				$data[$key]['is_special_price'] = 1;
																			}
																			else
																			{
																				$data[$key]['is_special_price'] = 0;
																			}


																			$get_product_img = $this->db->select('image_path,file_name')->get_where('product_images',array('product_id'=>$value['id'],'is_base'=>1))->row_array();
																			$product_img = isset($get_product_img['file_name']) ? base_url('media/product_images/thumbnail-400x400/'.$get_product_img['file_name']) : base_url('skin/front/images/product-default-img.png');
																			$data[$key]['img'] = $product_img;

																			$data[$key]['proID'] = $value['id'];
																			$data[$key]['name'] = $value['product_name'];
																			$data[$key]['slug'] = $value['slug'];
																			$data[$key]['sku'] = $value['sku'];
																			$data[$key]['hsncode'] = $value['hsncode'];
																			$data[$key]['price'] = $value['price'];
																			$data[$key]['special_price'] = $value['special_price'];

																		}
																	}

																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data' => $data
																	);	


																}

																else
																{
																	$response = array(
																		'status' => 0,
																		'message' => lang('CATEGORY_ID_ERROR')
																	);
																}
																log_message('debug', 'Products List Api Response - '.json_encode($response));	
																echo json_encode($response);
															}



															public function searchProduct()
															{
																$post = $this->input->post();
																log_message('debug', 'Search Product Api  Data - '.json_encode($post));	


																$response = array();

																$keyword = isset($post['keyword']) ? $post['keyword'] : '';


			// product list
																$productList = $this->db->query('SELECT a.* FROM tbl_products as a INNER JOIN tbl_product_category as b ON b.product_id = a.id WHERE (b.category_id IN (SELECT id FROM tbl_category as a WHERE a.title LIKE "%'.$keyword.'%") OR a.product_name LIKE "%'.$keyword.'%") GROUP BY b.product_id')->result_array();
																if($productList)
																{
																	foreach ($productList as $key => $value) {
																		if($value['special_price'] && $value['special_price_to'] >= $today_date)
																		{
																			$data[$key]['is_special_price'] = 1;
																		}
																		else
																		{
																			$data[$key]['is_special_price'] = 0;
																		}


																		$get_product_img = $this->db->select('image_path,file_name')->get_where('product_images',array('product_id'=>$value['id'],'is_base'=>1))->row_array();
																		$product_img = isset($get_product_img['file_name']) ? base_url('media/product_images/thumbnail-400x400/'.$get_product_img['file_name']) : base_url('skin/front/images/product-default-img.png');
																		$data[$key]['img'] = $product_img;

																		$data[$key]['proID'] = $value['id'];
																		$data[$key]['name'] = $value['product_name'];
																		$data[$key]['slug'] = $value['slug'];
																		$data[$key]['sku'] = $value['sku'];
																		$data[$key]['hsncode'] = $value['hsncode'];
																		$data[$key]['price'] = $value['price'];
																		$data[$key]['special_price'] = $value['special_price'];

																	}


																	$response = array(
																		'status' => 1,
																		'message' => 'Success',
																		'data' => $data
																	); 	



																}

																else
																{
																	$response = array(
																		'status' => 0,
																		'message' => 'No Product Available For This Search'
																	);
																}
																log_message('debug', 'Search Products List Api Response - '.json_encode($response));	
																echo json_encode($response);
															}





															public function getProductDetail()
															{
																$post = $this->input->post();
																log_message('debug', 'Product Detail Api Data - '.json_encode($post));	

																$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																$this->form_validation->set_rules('proID', 'Product ID', 'required|xss_clean');

																if ($this->form_validation->run() == FALSE)
																{
																	$response = array(
																		'status' => 0,
																		'message' => lang('LOGIN_VALID_FAILED')
																	);
																}
																else
																{

																	$proID = isset($post['proID']) ? $post['proID'] : 0;
																	$response = array();

																	if($proID > 0)
																	{
																		$productDetail = $this->db->get_where('products',array('id'=>$proID))->row_array();
																		$today_date = date('Y-m-d');
																		if($productDetail)
																		{
																			if($productDetail['special_price'] && $productDetail['special_price_to'] >= $today_date)
																			{
																				$special_price_status = 1;
																			}
																			else
																			{
																				$special_price_status = 0;
																			}


																			$get_product_img = $this->db->select('image_path,file_name')->get_where('product_images',array('product_id'=>$productDetail['id']))->result_array();
																			$product_images = array();
																			if($get_product_img)
																				{   $i = 1;
																					foreach ($get_product_img as $key => $value) {

																						$product_images[$key] = isset($value['file_name']) ? base_url('media/product_images/thumbnail-400x400/'.$value['file_name']) : base_url('skin/front/images/product-default-img.png');

																						$i++;}
																					}


						//$product_img = isset($get_product_img['file_name']) ? base_url('media/product_images/thumbnail-400x400/'.$get_product_img['file_name']) : base_url('skin/front/images/product-default-img.png');



																					if($productDetail['attribute_set_id'])
																					{
							// get weight unit
																						$chk_attribute_set = $this->db->get_where('attribute_set',array('id'=>$productDetail['attribute_set_id'],'status'=>1))->num_rows();
																						if($chk_attribute_set){

								// get attribute list
																							$attribute_list = $this->db->select('attribute.label,attribute.id,attribute.form_type,attribute.is_input_box')->join('attribute','attribute.id = attribute_set_attributes.attribute_id')->get_where('attribute_set_attributes',array('attribute_set_attributes.attribute_set_id'=>$productDetail['attribute_set_id'],'attribute.status'=>1))->result_array();
																							if($attribute_list)
																							{
																								foreach($attribute_list as $aKey=>$aList)
																								{
										// get attribute product data
																									$pro_attribute_data = $this->db->select('attribute_data.label,attribute_data.description,product_attribute.attribute_input_value,product_attribute.attribute_value')->join('attribute_data','attribute_data.id = product_attribute.attribute_value')->get_where('product_attribute',array('product_attribute.product_id'=>$proID,'product_attribute.attribute_id'=>$aList['id']))->result_array();


																									$total_attribute_data = count($pro_attribute_data);



																									$attribute_list[$aKey][$aList['label']] = !empty($pro_attribute_data)?$pro_attribute_data:null;
																								}
																							}
																							$productDetail['attribute_list'] = $attribute_list;
																						}
																					}

																					$response = array(
																						'status' => 1,
																						'message' => 'Success',
																						'img' => $product_images,
																						'proID'  => $productDetail['id'],
																						'name'=> substr($productDetail['product_name'],0,50),
																						'description'=> !empty($productDetail['description'])?$productDetail['description']:null,
																						'slug' =>$productDetail['slug'],
																						'sku' => $productDetail['sku'],
																						'hsncode' => $productDetail['hsncode'],
																						'price' => $productDetail['price'],
																						'is_special_price'=>$special_price_status,
																						'special_price' => $productDetail['special_price'],
																						'attribute_list' => $attribute_list
																					);

																				}
																				else
																				{
																					$response = array(
																						'status' => 0,
																						'message' => lang('PRODUCT_ID_ERROR')
																					);
																				}


																			}

																			else{

																				$response = array(
																					'status' => 0,
																					'message' => lang('PRODUCT_ID_ERROR')
																				);	

																			}
																		}
																		log_message('debug', 'Product  Detail API List Response - '.json_encode($response));	
																		echo json_encode($response);

																	}



																	public function cart(){

																		$response = array();
																		$post = $this->input->post();
																		log_message('debug', 'Add to Cart API Post Data - '.json_encode($post));	
																		$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																		$this->form_validation->set_rules('userID', 'User Id', 'required|xss_clean');
																		$this->form_validation->set_rules('ip', 'IP Address', 'required|xss_clean');
																		$this->form_validation->set_rules('proID', 'Product Id', 'required|xss_clean');
																		$this->form_validation->set_rules('qty', 'Qty', 'required|xss_clean');
																		$this->form_validation->set_rules('attribute_data', 'attribute data', 'required|xss_clean');
																		if ($this->form_validation->run() == FALSE)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => lang('LOGIN_VALID_FAILED')
																			);
																		}
																		else
																		{	

																			$user_id = $post['userID'];

																			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																			$password = isset($userData['password']) ? $userData['password'] : '';

																			$header_data = apache_request_headers();

																			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																			
																			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																			
																			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																				$response = array(
																					'status' => 0,
																					'message' => 'Session out.Please Login Again.'
																				);
																			}
																			else{

																				$chk_user = $this->db->get_where('users',array('id'=>$post['userID']))->num_rows();	
																				if($chk_user){

																					$productID = isset($post['proID']) ? $post['proID'] : 0;
																					$variationStatus = isset($post['variationStatus']) ? $post['variationStatus'] : 0;
																					$variationProID = isset($post['variationProID']) ? base64_decode($post['variationProID']) : 0;
																					$response = array();
																					$today_date = date('Y-m-d');
																					$attribute_data = $post['attribute_data'];


							// check product id valid or not
																					$chk_product = $this->db->get_where('products',array('id'=>$productID,'status'=>1,'approve_status'=>2))->num_rows();
																					if($chk_product)
																					{
																						$account_id = isset($post['userID']) ? $post['userID'] : 0;
																						$user_ip_address = $post['ip'];

										// check product already is in cart or not
																						$chk_cart_product = $this->db->get_where('cart_temp_data',array('user_id'=>$account_id,'product_id'=>$productID,'is_variation'=>0));
																						$qty = $post['qty'];
																						if($chk_cart_product->num_rows())
																						{

																							$pro_qty = $chk_cart_product->row_array();
																							$prod_qty = isset($pro_qty['qty']) ? $pro_qty['qty'] + $qty : 1;

																							$cartData = array(
																								'qty' => $prod_qty,
																								'updated' => date('Y-m-d H:i:s')
																							);

																							$this->db->where('user_id',$account_id);
																							$this->db->where('product_id',$productID);
																							$this->db->where('is_variation',0);
																							$this->db->update('cart_temp_data',$cartData);

																						}
																						else
																						{

																							$cartData = array(
																								'user_id' => $account_id,
																								'ip' => $user_ip_address,
																								'product_id' => $productID,
																								'is_variation'=>$variationStatus,
																								'variation_pro_id'=>$variationProID,
																								'qty' => $qty,
																								'attribute_data' => $attribute_data,
																								'created' => date('Y-m-d H:i:s')
																							);
																							$this->db->insert('cart_temp_data',$cartData);

																						}

										// get total product in cart
																						$get_total_product = $this->db->select('sum(qty) as total_qty')->get_where('cart_temp_data',array('user_id'=>$account_id))->row_array();
																						$total_product = isset($get_total_product['total_qty']) ? $get_total_product['total_qty'] : 0 ;

																						$response = array(
																							'status' => 1,
																							'msg' => 'Product added in Cart.',
																							'total_product' => $total_product
																						);
																					}

																					else
																					{
																						$response = array(
																							'status' => 0,
																							'msg' => 'Sorry ! Product is not valid.'
																						);
																					}		

																				} 

																				else{

																					$response = array(
																						'status' => 0,
																						'msg' => 'Sorry ! User is not valid.'
																					);
																				}

																			}
																		}



																		log_message('debug', 'Add to Cart API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}




																	public function cartList(){

																		$response = array();
																		$post = $this->input->post();
																		log_message('debug', 'Cart List API Post Data - '.json_encode($post));	
																		$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																		$this->form_validation->set_rules('userID', 'User Id', 'required|xss_clean');
																		if ($this->form_validation->run() == FALSE)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => lang('LOGIN_VALID_FAILED')
																			);
																		}
																		else
																		{		

																			$user_id = $post['userID'];

																			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																			$password = isset($userData['password']) ? $userData['password'] : '';

																			$header_data = apache_request_headers();

																			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																			
																			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																			
																			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																				$response = array(
																					'status' => 0,
																					'message' => 'Session out.Please Login Again.'
																				);
																			}
																			else{

																				$chk_user = $this->db->get_where('cart_temp_data',array('user_id'=>$post['userID']))->num_rows();
																				if($chk_user){

																					$today_date = date('Y-m-d');
																					$account_id = isset($post['userID']) ? $post['userID'] : 0;

																					$user_ip_address = $post['ip'];

																					$productList = $this->db->query("SELECT b.*,a.qty,a.id as temp_id,a.is_variation,a.variation_pro_id,a.attribute_data FROM tbl_cart_temp_data as a INNER JOIN tbl_products as b on b.id = a.product_id where (a.user_id = '$account_id') AND b.status = 1 AND b.approve_status = 2")->result_array();



																					if($productList)
																					{
																						foreach($productList as $key=>$list)
																						{

									// get product image
																							$get_product_img = $this->db->select('image_path,file_name')->get_where('product_images',array('product_id'=>$list['id'],'is_base'=>1))->row_array();
																							$product_img = isset($get_product_img['file_name']) ? base_url('media/product_images/thumbnail-70x70/'.$get_product_img['file_name']) : base_url('skin/front/images/product-default-img.png') ;

																							$productList[$key]['product_img'] = $product_img;

																							$productList[$key]['mrp_price'] = $list['price'];

																							if($list['special_price'] && $list['special_price_to'] >= $today_date)
																							{
																								$productList[$key]['price'] = $list['special_price'];
																								$productList[$key]['is_special_price'] = 1;
																								$deduct_cm_point = $list['price'] - $list['special_price'];

																								$cm_percentage = $list['cm_percentage'];
																								$deduct_cm_point_percentage = 0;
																								if($cm_percentage)
																								{
																									$deduct_cm_point_percentage = round(($cm_percentage/100)*$list['special_price']);
																								}
																								$deduct_cm_point = $deduct_cm_point+$deduct_cm_point_percentage;
																								$productList[$key]['deduct_cm_point'] = $deduct_cm_point;

																							}
																							else
																							{

																								$productList[$key]['is_special_price'] = 0;

																								$cm_percentage = $list['cm_percentage'];
																								$deduct_cm_point = 0;
																								if($cm_percentage)
																								{
																									$deduct_cm_point = round(($cm_percentage/100)*$list['price']);
																								}
																								$productList[$key]['deduct_cm_point'] = $deduct_cm_point;

																							}

																							$productList[$key]['qty'] = $list['qty'];	
																							$productList[$key]['temp_id'] = $list['temp_id'];	
																							$productList[$key]['final_price'] = $list['price'] - $deduct_cm_point;
																							$productList[$key]['credit_cm_point'] = $list['price'] - $deduct_cm_point;

																							$attributeData = array();

									// attributes
																							if($list['attribute_data'])
																							{
										// convert attribute
																								$attribute_list = (array) json_decode($list['attribute_data']);

																								if($attribute_list)
																								{
																									$ab = 0;
																									foreach($attribute_list as $attribute_id=>$value)
																									{
												// get attribute name
																										$get_attribute_name = $this->db->select('label')->get_where('attribute',array('id'=>$attribute_id))->row_array();
												// get attribute value
																										$get_attribute_value = $this->db->select('label,description')->get_where('attribute_data',array('id'=>$value))->row_array();
																										$attributeData[$ab]['attribute_id'] = $attribute_id;
																										$attributeData[$ab]['attribute_label'] = isset($get_attribute_name['label']) ? $get_attribute_name['label'] : '';
																										$attributeData[$ab]['attribute_value_id'] = $value;
																										$attributeData[$ab]['attribute_value_label'] = isset($get_attribute_value['label']) ? $get_attribute_value['label'] : '';
																										$attributeData[$ab]['attribute_description'] = isset($get_attribute_value['description']) ? $get_attribute_value['description'] : '';
																										$ab++;
																									}
																								}
																							}
																							$productList[$key]['attribute'] = $attributeData;
																						}
																					}


																					$response = array(
																						'status' => 1,
																						'msg' => 'Success',
																						'cartList' => $productList
																					);
																				}

																				else{

																					$response = array(
																						'status' => 0,
																						'msg' => 'User Not Valid',
																					);

																				}


																			}
																		}

																		log_message('debug', 'Cart List API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}



																	public function deleteCartData(){

																		$response = array();
																		$post = $this->input->post();
																		log_message('debug', 'Delete Cart Data API Post Data - '.json_encode($post));	
																		$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																		$this->form_validation->set_rules('temp_id', 'Temp Cart Id', 'required|xss_clean');
																		$this->form_validation->set_rules('userID', 'UserID', 'required|xss_clean');
																		$this->form_validation->set_rules('proID', 'Product Id', 'xss_clean');
																		if ($this->form_validation->run() == FALSE)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => lang('LOGIN_VALID_FAILED')
																			);
																		}
																		else
																		{	

																			$user_id = $post['userID'];

																			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																			$password = isset($userData['password']) ? $userData['password'] : '';

																			$header_data = apache_request_headers();

																			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																			
																			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																			
																			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																				$response = array(
																					'status' => 0,
																					'message' => 'Session out.Please Login Again.'
																				);
																			}
																			else{

																				$this->db->where('id',$post['temp_id']);
																				$this->db->where('user_id',$post['userID']);
																				$this->db->where('product_id',$post['proID']);
																				$this->db->delete('cart_temp_data');	
																				$response = array(
																					'status' => 1,
																					'message' => 'Cart Data Deleted Successfully'
																				);

																			}
																		}

																		log_message('debug', 'Delete Cart Data API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}



																	public function productSectionList()
																	{

																		$response = array();
																		$sectionList = $this->db->order_by('order_no','asc')->get_where('sections',array('status'=>1))->result_array();

																		$data = array();
																		if($sectionList){

																			foreach($sectionList as $bKey=>$bList){

																				$data[$bKey]['section_name'] = $bList['section_name'];
																				$sectionProductList = $this->User->get_section_product_list($bList['product_id']);

																				if($bList['section_type_id'] == 1 && $sectionProductList){
																					$product_data = array();
																					foreach($sectionProductList as $pKey=>$pList){

			      	  	// get product image
																						$get_product_img = $this->db->select('image_path,file_name')->get_where('product_images',array('product_id'=>$pList['id'],'is_base'=>1))->row_array();
																						$product_img = isset($get_product_img['file_name']) ? 'media/product_images/thumbnail-400x400/'.$get_product_img['file_name'] : 'skin/front/images/product-default-img.png' ;

																						$product_data[$pKey]['product_id'] = $pList['id'];
																						$product_data[$pKey]['product_name'] = $pList['product_name'];
																						$product_data[$pKey]['product_slug'] = $pList['slug'];
																						$product_data[$pKey]['product_img'] = $product_img;
																						$product_data[$pKey]['price'] = $pList['price'];

																						if($pList['special_price'] && $pList['special_price_to'] >= $today_date)
																						{
																							$product_data[$pKey]['special_price_status'] = 1;
																							$product_data[$pKey]['special_price'] = $pList['special_price'];
																						}
																						else
																						{
																							$product_data[$pKey]['special_price_status'] = 0;
																						}	

																					}

																					$data[$bKey]['product_data'] = $product_data;			

																				}
																			}
																		}


																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data' => $data
																		);

																		log_message('debug', 'Get Product Sectin List API Response - '.json_encode($response));	
																		echo json_encode($response);
																	}




																	public function getContactSupport()
																	{
																		$post = $this->input->post();
																		log_message('debug', 'getContactSupport API Post Data - '.json_encode($post));	
																		$userID = isset($post['userID']) ? $post['userID'] : 0;
																		$response = array();
			// check user valid or not
																		$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																		if($chk_user)
																		{

																			$sql = "SELECT a.*,c.name as sponser_name,c.user_code as sponser_code,c.mobile as sponser_mobile FROM tbl_users as a INNER JOIN tbl_member_tree as b ON a.id = b.member_id INNER JOIN tbl_users as c ON c.id = b.reffrel_id where  a.role_id = 2 and a.id = ".$userID." ";
																			$sponser = $this->db->query($sql)->row_array();

																			$sponser_mobile = isset($sponser['sponser_mobile']) ? $sponser['sponser_mobile'] : '';

																			$site_setting = $this->db->get_where('site_settings',array('id'=>1))->row_array();

																			$company_mobile = isset($site_setting['customer_no']) ? $site_setting['customer_no'] : '';

																			$response = array(

																				'status'  => 1,
																				'message' => 'Success',
																				'company_mobile' => $company_mobile,
																				'sponser_mobile' => $sponser_mobile

																			);	
																		}
																		else
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! user not valid.'
																			);
																		}
																		log_message('debug', 'getContactSupport API Response - '.json_encode($response));	
																		echo json_encode($response);
																	}


																	public function getAepsIncome()
																	{
																		$post = $this->input->post();
																		log_message('debug', 'getAepsIncome API POST Data - '.json_encode($post));

																		$user_id = $post['userID'];

																		$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																		$password = isset($userData['password']) ? $userData['password'] : '';

																		$header_data = apache_request_headers();

																		$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																		
																		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																		
																		$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																		if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																			$response = array(
																				'status' => 0,
																				'message' => 'Session out.Please Login Again.'
																			);
																		}
																		else{

																			$userID = isset($post['userID']) ? $post['userID'] : 0;
																			$response = array();
				// check user valid or not
																			$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																			if($chk_user)
																			{
																				$recharge = $this->db->query("SELECT a.*,b.name as from_member_name,b.user_code as from_member_code,c.service,c.txnID FROM tbl_level_commision as a INNER JOIN tbl_users as b ON b.id = a.paid_from_member_id INNER JOIN tbl_member_aeps_transaction as c ON c.id = a.record_id where a.paid_to_member_id = '$userID' and a.level_num != 0 and a.commission_type = 'AEPS' ")->result_array();

																				$data = array();
																				if($recharge)
																				{
																					foreach ($recharge as $key => $list) {


																						if($list['service'] == 'balwithdraw' || $list['service'] == 'aadharpay')
																						{
																							$data[$key]['service'] = 'Account Withdrawal';
																						}
																						elseif($list['service'] == 'balinfo')
																						{
																							$data[$key]['service'] = 'Balance Info';
																						}
																						elseif($list['service'] == 'ministatement')
																						{
																							$data[$key]['service'] = 'Mini Statement';
																						}

																						$data[$key]['amount'] = number_format($list['recharge_amount'],2).'/-';
																						$data[$key]['txnID'] = $list['txnID'];
																						$data[$key]['commision_amount'] = number_format($list['commision_amount'],2).'/-';
																						$data[$key]['date'] = date('d-M-Y H:i:s',strtotime($list['created']));

																					}
																				}

																				$response = array(
																					'status' => 1,
																					'message' => 'Success',
																					'data' => $data,
																				);	
																			}
																			else
																			{
																				$response = array(
																					'status' => 0,
																					'message' => lang('USER_ID_ERROR')
																				);
																			}
																		}
																		log_message('debug', 'getAepsIncome API Response - '.json_encode($response));	
																		echo json_encode($response);
																	}



																	public function getAepsCommission()
																	{
																		$post = $this->input->post();
																		log_message('debug', 'getAepsCommission API POST Data - '.json_encode($post));

																		$user_id = $post['userID'];

																		$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																		$password = isset($userData['password']) ? $userData['password'] : '';

																		$header_data = apache_request_headers();

																		$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																		
																		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																		
																		$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																		if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																			$response = array(
																				'status' => 0,
																				'message' => 'Session out.Please Login Again.'
																			);
																		}
																		else{

																			$userID = isset($post['userID']) ? $post['userID'] : 0;
																			$response = array();
				// check user valid or not
																			$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																			if($chk_user)
																			{
																				$recharge = $this->db->query("SELECT a.*,b.name as from_member_name,b.user_code as from_member_code,c.service,c.txnID FROM tbl_level_commision as a INNER JOIN tbl_users as b ON b.id = a.paid_from_member_id INNER JOIN tbl_member_aeps_transaction as c ON c.id = a.record_id where a.paid_to_member_id = '$userID' and a.level_num = 0 and a.commission_type = 'AEPS' ")->result_array();

																				$data = array();
																				if($recharge)
																				{
																					foreach ($recharge as $key => $list) {


																						if($list['service'] == 'balwithdraw' || $list['service'] == 'aadharpay')
																						{
																							$data[$key]['service'] = 'Account Withdrawal';
																						}
																						elseif($list['service'] == 'balinfo')
																						{
																							$data[$key]['service'] = 'Balance Info';
																						}
																						elseif($list['service'] == 'ministatement')
																						{
																							$data[$key]['service'] = 'Mini Statement';
																						}

																						$data[$key]['amount'] = number_format($list['recharge_amount'],2).'/-';
																						$data[$key]['txnID'] = $list['txnID'];
																						$data[$key]['commision_amount'] = number_format($list['commision_amount'],2).'/-';
																						$data[$key]['date'] = date('d-M-Y H:i:s',strtotime($list['created']));

																					}
																				}

																				$response = array(
																					'status' => 1,
																					'message' => 'Success',
																					'data' => $data,
																				);	
																			}
																			else
																			{
																				$response = array(
																					'status' => 0,
																					'message' => lang('USER_ID_ERROR')
																				);
																			}
																		}
																		log_message('debug', 'getAepsCommission API Response - '.json_encode($response));	
																		echo json_encode($response);
																	}



																	public function getPancardKycData()
																	{
																		$post = $this->input->post();
																		log_message('debug', 'getPancardKycData API Post Data - '.json_encode($post));	
																		$user_id = $post['userID'];

																		$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																		$password = isset($userData['password']) ? $userData['password'] : '';

																		$header_data = apache_request_headers();

																		$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																		
																		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																		
																		$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																		if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																			$response = array(
																				'status' => 0,
																				'message' => 'Session out.Please Login Again.'
																			);
																		}
																		else{

																			$userID = isset($post['userID']) ? $post['userID'] : 0;
																			$response = array();
				// check user valid or not
																			$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																			if($chk_user)
																			{

																				$activeService = $this->User->account_active_service($userID);
																				if(!in_array(6, $activeService)){
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! this service not active for you.'
																					);
																				}
																				else{



																					$kycData = $this->db->get_where('member_pancard_kyc',array('user_id'=>$userID))->row_array();

																					if($kycData){

																						$check_active_uti = $this->User->utiKycStatus($kycData['mobile']);

																						$psaLoginId = '';
																						$reason = '';
																						if($check_active_uti['statuscode'] == "PEN"){

																							$data = array(
																								'kyc_status' => 'Pending',
																								'reason'     => '',
																								'psaLoginId' => '',
																								'name'  => $kycData['name'],
																								'email' => $kycData['email'],
																								'mobile'=> $kycData['mobile'],
																								'aadhar_card' => base_url($kycData['aadhar_card']),
																								'pancard' => base_url($kycData['pancard']),
																							);	

																							$is_active_uti = 1;
																							$response = array(
																								'status' => 0,
																								'message'=>'Success',
																								'data' => $data
																							);

																						}
																						elseif(strpos($check_active_uti['status'], "Approved") !== false){

																							$is_active_uti = 2;	
																							$explode = explode("Approved and PSAId is : ",$check_active_uti['status']);
																							$psaLoginId = $explode[1];

																							$data = array(
																								'kyc_status' => 'Approved',
																								'reason'     => '',
																								'psaLoginId' => $psaLoginId,
																								'name'  => $kycData['name'],
																								'email' => $kycData['email'],
																								'mobile'=> $kycData['mobile'],
																								'aadhar_card' => base_url($kycData['aadhar_card']),
																								'pancard' => base_url($kycData['pancard']),
																							);

																							$response = array(
																								'status' => 1,
																								'message'=>'Success',
																								'data' => $data
																							);
																						}
																						else{

																							$is_active_uti = 3;
																							$reason = $check_active_uti['status'];

																							$data = array(

																								'kyc_status' => 'Rejected',
																								'reason'     => $reason,
																								'psaLoginId' => '',
																								'name'  => $kycData['name'],
																								'email' => $kycData['email'],
																								'mobile'=> $kycData['mobile'],
																								'aadhar_card' => base_url($kycData['aadhar_card']),
																								'pancard' => base_url($kycData['pancard']),

																							);

																							$response = array(
																								'status' => 0,
																								'message'=>'Success',
																								'data' => $data
																							);
																						}
																					}
																					else{

																						$data = array(
																							'kyc_status' => 'Not Activated',
																							'reason'     => '',
																							'psaLoginId' => '',
																							'name'  => '',
																							'email' => '',
																							'mobile'=> '',
																							'aadhar_card' => '',
																							'pancard' => '',
																						);

																						$response = array(
																							'status' => 0,
																							'message'=>'Success',
																							'data' => $data
																						);
																					}

																				}

																			}
																			else
																			{
																				$response = array(
																					'status' => 0,
																					'message' => 'Sorry!! user not valid.'
																				);
																			}
																		}
																		log_message('debug', 'getPancardKycData API Response - '.json_encode($response));	
																		echo json_encode($response);
																	}




																	public function pancardActiveAuth(){

																		$response = array();
																		$post = $this->input->post();
																		log_message('debug', 'pancardActiveAuth API Post Data - '.json_encode($post));	
																		$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
																		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
																		$this->form_validation->set_rules('mobile', 'Mobile', 'required|numeric|min_length[10]|max_length[10]|xss_clean');
																		$this->form_validation->set_rules('aadhar_card', 'Aadhar Card', 'required|xss_clean');
																		$this->form_validation->set_rules('pancard', 'Pancard', 'required|xss_clean');

																		if ($this->form_validation->run() == FALSE)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Please enter all detail and documents.'
																			);
																		}
																		else
																		{	

																			$user_id = $post['userID'];

																			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																			$password = isset($userData['password']) ? $userData['password'] : '';

																			$header_data = apache_request_headers();

																			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																			
																			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																			
																			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																				$response = array(
																					'status' => 0,
																					'message' => 'Session out.Please Login Again.'
																				);
																			}
																			else{

																				$userID = $post['userID']; 

																				$activeService = $this->User->account_active_service($userID);
																				if(!in_array(6, $activeService)){
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! this service not active for you.'
																					);
																				}
																				else{

																					$aadhar_card = '';
																					if(isset($post['aadhar_card']) && !empty($post['aadhar_card']))
																					{
																						$encodedData = $post['aadhar_card'];
																						if(strpos($post['aadhar_card'], ' ')){
																							$encodedData = str_replace(' ','+', $post['aadhar_card']);
																						}
																						$profile = base64_decode($encodedData);
																						$file_name = time().rand(1111,9999).'.jpg';

																						$path = 'media/pancard_kyc_doc/';
																						if (!is_dir($path)) {
																							mkdir($path, 0777, true);
																						}
																						$targetDir = $path.$file_name;
																						if(file_put_contents($targetDir, $profile)){
																							$aadhar_card = $targetDir;
																						}
																					}

																					$pancard = '';
																					if(isset($post['pancard']) && !empty($post['pancard']))
																					{
																						$encodedData = $post['pancard'];
																						if(strpos($post['pancard'], ' ')){
																							$encodedData = str_replace(' ','+', $post['pancard']);
																						}
																						$profile = base64_decode($encodedData);
																						$file_name = time().rand(1111,9999).'.jpg';

																						$path = 'media/pancard_kyc_doc/';
																						if (!is_dir($path)) {
																							mkdir($path, 0777, true);
																						}
																						$targetDir = $path.$file_name;
																						if(file_put_contents($targetDir, $profile)){
																							$pancard = $targetDir;
																						}
																					}


																					$api_response = $this->Api_model->activePancardMember($post,$aadhar_card,$pancard,$userID);
																					$status = $api_response['status'];

																					if($status == 1)
																					{
																						$message = $api_response['msg'];

																						$this->User->sendNotification($userID,'UTI Pancard Kyc',$message);
																						$response = array(

																							'status' => 1,
																							'message'=> $api_response['msg']	

																						);

																					}
																					else
																					{
																						$response = array(

																							'status' => 0,
																							'message'=> $api_response['msg']	

																						);
																					}

																				}

																			}
																		}

																		log_message('debug', 'pancardActiveAuth API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}



																	public function purchaseCouponAuth(){

																		$response = array();
																		$post = $this->input->post();
																		log_message('debug', 'purchaseCouponAuth API Post Data - '.json_encode($post));	
																		$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																		$this->form_validation->set_rules('psa_login_id', 'PSA LoginID', 'required|xss_clean');
																		$this->form_validation->set_rules('coupon', 'Coupon', 'required|numeric|xss_clean');

																		if ($this->form_validation->run() == FALSE)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Please enter all detail.'
																			);
																		}
																		else
																		{	

																			$user_id = $post['userID'];

																			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																			$password = isset($userData['password']) ? $userData['password'] : '';

																			$header_data = apache_request_headers();

																			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																			
																			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																			
																			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																				$response = array(
																					'status' => 0,
																					'message' => 'Session out.Please Login Again.'
																				);
																			}
																			else{

																				$userID = $post['userID']; 

																				$activeService = $this->User->account_active_service($userID);
																				if(!in_array(6, $activeService)){
																					$response = array(
																						'status' => 0,
																						'message' => 'Sorry!! this service not active for you.'
																					);
																				}
																				else{


						// get commission
																					$get_uti_charge = $this->db->get_where('master_setting',array('id'=>1))->row_array();
																					$charge = isset($get_uti_charge['uti_charge']) ? $get_uti_charge['uti_charge'] : 0 ;

																					$charge_amount = $charge * $post['coupon'];

																					$chk_wallet_balance =$this->db->get_where('users',array('id'=>$userID))->row_array();
																					
																					
																					 $reserved_wallet_balance = $chk_wallet_balance['wallet_balance'] - $chk_wallet_balance['reserve_wallet_balance'] ;
																					 
																					
																					if($chk_wallet_balance['is_main_wallet_block'] == 1){
					        		// save system log
																					//	log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry ! Your fund is blocked for 2 Days.'
																						);
																					}
																					
																					elseif($reserved_wallet_balance < $charge_amount){

																						$response = array(
																							'status' => 0,
																							'message' => 'Sorry!! you have insufficient balance in your wallet.'
																						);   
																					}
																					else{

																						$response = $this->Api_model->purchaseCoupon($post,$userID);

																						$status = $response['status'];

																						if($status == 1)
																						{	

								// charges cut pancard

																							$after_balance = $chk_wallet_balance['wallet_balance'] - $charge_amount;

																							$wallet_data = array(
																								'member_id'           => $userID,    
																								'before_balance'      => $chk_wallet_balance['wallet_balance'],
																								'amount'              => $charge_amount, 
																								'after_balance'       => $after_balance,      
																								'status'              => 1,
																								'type'                => 2, 
																								'wallet_type'         => 1,     
																								'created'             => date('Y-m-d H:i:s'),      
																								'description'         => 'UTI Pancard Coupon amount deducted.'
																							);

																							$this->db->insert('member_wallet',$wallet_data);

																							$user_wallet = array(
																								'wallet_balance'=>$after_balance,        
																							);    
																							$this->db->where('id',$userID);
																							$this->db->update('users',$user_wallet);

					            //save coupon

																							$coupon_data = array(

																								'user_id'     => $userID,
																								'psa_login_id'=> $post['psa_login_id'],
																								'coupon'      => $response['token'],
																								'created'     => date('Y-m-d H:i:s')  	
																							);

																							$this->db->insert('uti_pancard_coupon',$coupon_data);


																							$message = 'Congratulations!! coupon purchased successfully.';

																							$this->User->sendNotification($userID,'UTI Pancard Coupon Purchase',$message);

																							$response = array(

																								'status' => 1,
																								'message'=> 'Congratulations!! coupon purchased successfully.'	

																							);
																						}
																						else
																						{
																							$response = array(

																								'status' => 0,
																								'message'=> 'Sorry!! coupon purchased failed.'	

																							);
																						}

																					}
																				}

																			}
																		}

																		log_message('debug', 'purchaseCouponAuth API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}



																	public function getUtiCouponList()
																	{
																		$post = $this->input->post();
																		log_message('debug', 'getUtiCouponList API POST Data - '.json_encode($post));	
																		$userID = isset($post['userID']) ? $post['userID'] : 0;

																		$user_id = $post['userID'];

																		$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																		$password = isset($userData['password']) ? $userData['password'] : '';

																		$header_data = apache_request_headers();

																		$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																		
																		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																		
																		$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																		if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																			$response = array(
																				'status' => 0,
																				'message' => 'Session out.Please Login Again.'
																			);
																		}
																		else{

																			$response = array();
				// check user valid or not
																			$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
																			if($chk_user)
																			{
																				$recharge = $this->db->get_where('uti_pancard_coupon',array('user_id'=>$userID))->result_array();

																				$data = array();
																				if($recharge)
																				{
																					foreach ($recharge as $key => $list) {

																						$data[$key]['psa_login_id'] = $list['psa_login_id'];
																						$data[$key]['coupon'] = $list['coupon'];
																						$data[$key]['date'] = date('d-M-Y',strtotime($list['created']));

																					}

																					$response = array(
																						'status' => 1,
																						'message' => 'Success',
																						'data' => $data,
																					);
																				}
																				else{

																					$response = array(

																						'status' => 0,
																						'message'=> 'Sorry!! record not found.'	

																					);
																				}

																			}
																			else
																			{
																				$response = array(
																					'status' => 0,
																					'message' => lang('USER_ID_ERROR')
																				);
																			}
																		}
																		log_message('debug', 'getUtiCouponList API Response - '.json_encode($response));	
																		echo json_encode($response);
																	}



																	public function getTicketTypeList(){

																		$response = array();

																		$userList = $this->db->get('ticket_related')->result_array();
																		$data = array();
																		if($userList)
																		{
																			foreach($userList as $key=>$list)
																			{
																				$data[$key]['id'] = $list['id'];
																				$data[$key]['title'] = $list['title'];
																			}
																		}
																		$response = array(
																			'status' => 1,
																			'message' => 'Success',
																			'data'=>$data
																		);
																		log_message('debug', 'Get Account Member List API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}

																	public function ticketAuth(){

																		$response = array();
																		$post = $this->input->post();
																		log_message('debug', 'Ticket Auth API Post Data - '.json_encode($post));	
																		$this->load->library('form_validation');
																		$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
																		$this->form_validation->set_rules('subject', 'Subject', 'required|xss_clean');
																		$this->form_validation->set_rules('type_id', 'Type', 'required|xss_clean');

																		if ($this->form_validation->run() == FALSE)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Please Enter Required field.'
																			);
																		}
																		else
																		{	

																			$user_id = $post['user_id'];

																			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																			$password = isset($userData['password']) ? $userData['password'] : '';

																			$header_data = apache_request_headers();

																			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																			
																			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																			
																			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																				$response = array(
																					'status' => 0,
																					'message' => 'Session out.Please Login Again.'
																				);
																			}
																			else{

																				$loggedAccountID = $post['user_id'];

																				$filePath = '';
																				$encodedData = $post['photo'];
																				if(strpos($post['photo'], ' ')){
																					$encodedData = str_replace(' ','+', $post['photo']);
																				}
																				$profile = base64_decode($encodedData);
																				if($profile)
																				{
																					$file_name = time().rand(1111,9999).'.jpg';
					// 	$profile_img_name = base_url('media/user_profile/'.$file_name);
																					$profile_img_name = FILE_UPLOAD_SERVER_PATH.$file_name;
																					$path = 'media/ticket/';
																					$targetDir = $path.$file_name;
																					if(file_put_contents($targetDir, $profile)){
																						$filePath = $targetDir;
																					}
																				}


				  	// generate ticket id
																				$ticket_id = rand(111111,999999).'-'.date('Y').'-'.date('m').'-'.date('d');

																				$wallet_data = array(
																					'ticket_id' => $ticket_id,
																					'member_id'           => $loggedAccountID,    
																					'subject'      => $post['subject'],
																					'related_to'              => $post['type_id'],  
																					'message'       => $post['message'],      
																					'attachment'                => $filePath,      
																					'status'              => 1,
																					'created'             => date('Y-m-d H:i:s'),      
																					'updated'             => date('Y-m-d H:i:s'),      
																				);

																				$this->db->insert('ticket',$wallet_data);
																				$system_ticket_id = $this->db->insert_id();

																				$ticketData = array(
																					'ticket_id' => $system_ticket_id,
																					'message' => $post['message'],
																					'attachment' => $filePath,
																					'status' => 1,
																					'created'             => date('Y-m-d H:i:s'),      
																					'created_by' => $loggedAccountID
																				);
																				$this->db->insert('ticket_reply',$ticketData);

																				$message = 'Ticket generated successfully.';

																				$this->User->sendNotification($loggedAccountID,'Support Ticket',$message);

																				$response = array(
																					'status' => 1,
																					'message' => 'Ticket generated successfully.'
																				);


																			}
																		}
																		log_message('debug', 'Ticket Auth API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}

																	public function getTicketList(){

																		$response = array();

																		$post = $this->input->post();
																		$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;

																		$user_id = $post['user_id'];

																		$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																		$password = isset($userData['password']) ? $userData['password'] : '';

																		$header_data = apache_request_headers();

																		$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																		
																		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																		
																		$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																		if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																			$response = array(
																				'status' => 0,
																				'message' => 'Session out.Please Login Again.'
																			);
																		}
																		else{
			// get users list
																			$sql = "SELECT a.*, b.title as related_to_title, c.title as status_title FROM tbl_ticket as a INNER JOIN tbl_ticket_related as b ON b.id = a.related_to INNER JOIN tbl_ticket_status as c ON c.id = a.status  where a.member_id = '$loggedAccountID'";
																			$userList = $this->db->query($sql)->result_array();
																			$data = array();
																			if($userList)
																			{
																				foreach($userList as $key=>$list)
																				{
																					$data[$key]['ticket_id'] = $list['ticket_id'];
																					$data[$key]['subject'] = $list['subject'];
																					$data[$key]['message'] = $list['message'];
																					$data[$key]['type'] = $list['related_to_title'];
																					$data[$key]['attachment'] = base_url($list['attachment']);
																					$data[$key]['status'] = $list['status_title'];
																					$data[$key]['datetime'] = date('d-M-Y H:i:s',strtotime($list['updated']));
																				}
																			}
																			$response = array(
																				'status' => 1,
																				'message' => 'Success',
																				'data'=>$data
																			);
																		}
																		log_message('debug', 'Get Ticket List API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}



																	public function addNomineeAuth(){

																		$response = array();
																		$post = $this->input->post();
																		log_message('debug', 'addNomineeAuth Post Data - '.json_encode($post));	
																		$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
																		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
																		$this->form_validation->set_rules('nominee_name', 'Nominee Name', 'required|xss_clean');     
																		$this->form_validation->set_rules('mobile', 'Phone No.', 'required|xss_clean');     
																		$this->form_validation->set_rules('email', 'Email', 'required|xss_clean|valid_email');
																		$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
																		if ($this->form_validation->run() == FALSE)
																		{
																			$response = array(
																				'status' => 0,
																				'message' => 'Sorry!! Enter required details.'
																			);
																		}
																		else
																		{	

																			$user_id = $post['userID'];

																			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																			$password = isset($userData['password']) ? $userData['password'] : '';

																			$header_data = apache_request_headers();

																			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																			
																			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																			
																			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																				$response = array(
																					'status' => 0,
																					'message' => 'Session out.Please Login Again.'
																				);
																			}
																			else{


																				$account_id = $post['userID'];

																				$chk_data = $this->db->get_where('nominee',array('user_id'=>$account_id))->row_array();

																				if(!$chk_data){

																					$data = array(
																						'user_id' => $account_id,
																						'nominee_name' => $post['nominee_name'],
																						'mobile' => $post['mobile'],
																						'email' => $post['email'],
																						'address' => $post['address'],
																						'created' => date('Y-m-d h:i:s')
																					);

																					$this->db->insert('nominee',$data); 	

																				}
																				else{


																					$data = array(
																						'nominee_name' => $post['nominee_name'],
																						'mobile' => $post['mobile'],
																						'email' => $post['email'],
																						'address' => $post['address'],
																					);

																					$this->db->where('user_id',$account_id);	
																					$this->db->update('nominee',$data); 

																				}	


																				$message = 'Nominee add successfully.';

																				$this->User->sendNotification($account_id,'Add Nominee',$message);  

																				$response = array(

																					'status' => 1,
																					'message'=>'Nominee add successfully.'	

																				);

																			}
																		}
																		log_message('debug', 'addNomineeAuth API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}


																	public function getUserNomineeDetails(){

																		$response = array("status"=>0);
																		$post = $this->input->post();
																		log_message('debug', 'getUserNomineeDetails API Post Data - '.json_encode($post));
																		$this->load->library('form_validation');
		    //$this->form_validation->set_data($this->input->get());
																		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
																		if ($this->form_validation->run() == FALSE)
																		{
																			$response['message'] = lang('LOGIN_VALID_FAILED');
																		}else{
		        // check user valid or not
																			$user_id = $post['userID'];

																			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																			$password = isset($userData['password']) ? $userData['password'] : '';

																			$header_data = apache_request_headers();

																			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																			
																			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																			
																			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																				$response = array(
																					'status' => 0,
																					'message' => 'Session out.Please Login Again.'
																				);
																			}
																			else{

																				$response['message'] = lang('USER_ID_ERROR');
																				$chk_user = $this->db->get_where('users',array('id'=>$post['userID']))->num_rows();
																				if(!empty($chk_user))
																				{
																					$data = array();
																					$query = $this->db->get_where('nominee', array('user_id'=>$post['userID']));
																					if($query->num_rows() > 0)
																					{
																						$row = $query->row_array();
																						$data = array(
																							'nominee_name' => $row['nominee_name'],
																							'mobile' => $row['mobile'],
																							'email' => $row['email'],
																							'address' => $row['address']
																						);

																						$response = array(
																							'status' => 1,
																							'message'=>'Success',
																							'data' => $data
																						);
																					}
																					else{

																						$response = array(
																							'status' => 0,
																							'message'=>'Nominee not found.',
																						);
																					}
																				}

																			}
																		}
																		log_message('debug', 'getUserNomineeDetails Response - '.json_encode($response));
																		echo json_encode($response);
																	}



																	public function getRankList(){

																		$response = array();

																		$post = $this->input->post();

																		$user_id = isset($post['userID']) ? $post['userID'] : 0;

																		$user_id = $post['userID'];

																		$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																		$password = isset($userData['password']) ? $userData['password'] : '';

																		$header_data = apache_request_headers();

																		$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																		
																		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																		
																		$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																		if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																			$response = array(
																				'status' => 0,
																				'message' => 'Session out.Please Login Again.'
																			);
																		}
																		else{

																			if(!$user_id){

																				$response = array(
																					'status' => 0,
																					'message'=>'Please enter userID'	
																				);
																			}
																			else{

																				$rank = array(

																					array(
																						'rank_id' => 1,
																						'title' => 'Bronze',
																						'total_user' => $this->User->get_total_member_rank_achiever($user_id,1)
																					),
																					array(
																						'rank_id' => 2,
																						'title' => 'Silver',
																						'total_user' => $this->User->get_total_member_rank_achiever($user_id,2)
																					),
																					array(
																						'rank_id' => 3,
																						'title' => 'Gold',
																						'total_user' => $this->User->get_total_member_rank_achiever($user_id,3)
																					),
																					array(
																						'rank_id' => 4,
																						'title' => 'Platinum',
																						'total_user' => $this->User->get_total_member_rank_achiever($user_id,4)
																					),
																					array(
																						'rank_id' => 5,
																						'title' => 'Diamond',
																						'total_user' => $this->User->get_total_member_rank_achiever($user_id,5)
																					),

																				);

																				$data = array();
																				if($rank)
																				{
																					foreach($rank as $key=>$list)
																					{
																						$data[$key]['rank_id'] = $list['rank_id'];
																						$data[$key]['title'] = $list['title'];
																						$data[$key]['total_user'] = $list['total_user'];

																					}
																				}
																				$response = array(
																					'status' => 1,
																					'message' => 'Success',
																					'data'=>$data
																				);
																			}
																		}
																		log_message('debug', 'Get Rank List API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}


																	public function getRankWiseTeamList(){

																		$response = array();

																		$post = $this->input->post();
																		log_message('debug', 'Get Rank List API Post Data - '.json_encode($post));
																		//$loggedAccountID = isset($post['userID']) ? $post['userID'] : 0;

																		$user_id = $post['userID'];

																		$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

																		$password = isset($userData['password']) ? $userData['password'] : '';

																		$header_data = apache_request_headers();

																		$token = isset($header_data['Token']) ? $header_data['Token'] : '';
																		
																		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
																		
																		$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

																		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

																// 		if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

																// 			$response = array(
																// 				'status' => 0,
																// 				'message' => 'Session out.Please Login Again.'
																// 			);
																// 		}
																// 		else{
                                                                                            
																			$rank_id = isset($post['rank_id']) ? $post['rank_id'] : 0;
																			$getBinaryDownline = $this->db->query("SELECT * FROM `tbl_member_tree` WHERE `member_id` = ".$user_id)->row_array();
                                                                            $binary_downline_str = isset($getBinaryDownline['binary_downline_str']) ? $getBinaryDownline['binary_downline_str'] : '';
        
				// get users list
																			$sql = "SELECT c.user_code,c.name,a.created,b.id,b.is_paid,b.old_rank_id,b.new_rank_id FROM tbl_member_tree as a INNER JOIN tbl_member_rank_history as b ON b.member_id = a.member_id INNER JOIN tbl_users as c ON c.id = a.member_id WHERE a.binary_downline_str LIKE '%".$binary_downline_str."%' AND b.new_rank_id ='$rank_id'";
																		
																			$userList = $this->db->query($sql)->result_array();
																			$data = array();
																			if($userList)
																			{
																				foreach($userList as $key=>$list)
																				{
																					$data[$key]['user_code'] = $list['user_code'];
																					$data[$key]['name'] = $list['name'];
																					$data[$key]['old_rank'] = $this->User->get_rank_title($list['old_rank_id']);
																					$data[$key]['new_rank'] = $this->User->get_rank_title($list['new_rank_id']);
																					$data[$key]['datetime'] = date('d-M-Y',strtotime($list['created']));
																				}
																			}
																			$response = array(
																				'status' => 1,
																				'message' => 'Success',
																				'data'=>$data
																			);
																	//	}
																		log_message('debug', 'getRankWiseTeamList API Response - '.json_encode($response));	
																		echo json_encode($response);

																	}


	public function getLevelList(){

		$response = array();

		$post = $this->input->post();

		$user_id = isset($post['userID']) ? $post['userID'] : 0;

		$user_id = $post['userID'];

		$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

		$password = isset($userData['password']) ? $userData['password'] : '';

		$header_data = apache_request_headers();

		$token = isset($header_data['Token']) ? $header_data['Token'] : '';

		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

		$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

// 		if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

// 			$response = array(
// 			'status' => 0,
// 			'message' => 'Session out.Please Login Again.'
// 			);
// 		}
// 		else{

// 		if(!$user_id){

// 			$response = array(
// 			'status' => 0,
// 			'message'=>'Please enter userID'	
// 			);
// 		}
// 		else{

			$levelWiseMember = $this->User->get_level_wise_member($user_id);
		

            $first_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[0]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $second_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[1]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $third_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[2]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $fourth_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[3]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $fifth_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[4]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $six_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[5]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $seven_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[6]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $eight_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[7]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $nine_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[8]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $ten_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[9]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $eleven_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[10]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $tweleve_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[11]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $thirteen_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[12]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $fourteen_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[13]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $fifteen_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[14]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $sixteen_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[15]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $seventeen_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[16]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $eighteen_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[17]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $nineteen_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[18]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            $twenty_level_active = $this->db->where_in('id',explode(',', $levelWiseMember[19]['levelStr']))->get_where('users',array('paid_status'=>1))->num_rows();
            //inactive
            
             $first_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[0]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $second_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[1]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $third_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[2]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $fourth_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[3]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $fifth_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[4]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $six_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[5]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $seven_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[6]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $eight_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[7]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $nine_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[8]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $ten_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[9]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $eleven_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[10]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $tweleve_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[11]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $thirteen_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[12]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $fourteen_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[13]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $fifteen_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[14]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $sixteen_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[15]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $seventeen_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[16]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $eighteen_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[17]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $nineteen_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[18]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            $twenty_level_inactive = $this->db->where_in('id',explode(',', $levelWiseMember[19]['levelStr']))->get_where('users',array('paid_status'=>0))->num_rows();
            
            
			$rank = array(
                
			array(
			'level_id' => 1,
			'title' => 'First',
			'total_user' => isset($levelWiseMember[0]['totalMember']) ? $levelWiseMember[0]['totalMember'] : 0,
			'total_active' =>$first_level_active,
			'total_inactive' =>$first_level_inactive
			
			),
			array(
			'level_id' => 2,
			'title' => 'Second',
			'total_user' => isset($levelWiseMember[1]['totalMember']) ? $levelWiseMember[1]['totalMember'] : 0,
				'total_active' =>$second_level_active,
					'total_inactive' =>$second_level_inactive
			),
			array(
			'level_id' => 3,
			'title' => 'Third',
			'total_user' => isset($levelWiseMember[2]['totalMember']) ? $levelWiseMember[2]['totalMember'] : 0,
				'total_active' =>$third_level_active,
					'total_inactive' =>$third_level_inactive
			),
			array(
			'level_id' => 4,
			'title' => 'Fourth',
			'total_user' => isset($levelWiseMember[3]['totalMember']) ? $levelWiseMember[3]['totalMember'] : 0,
				'total_active' =>$fourth_level_active,
					'total_inactive' =>$fourth_level_inactive
			),
			array(
			'level_id' => 5,
			'title' => 'Five',
			'total_user' => isset($levelWiseMember[4]['totalMember']) ? $levelWiseMember[4]['totalMember'] : 0,
				'total_active' =>$fifth_level_active,
					'total_inactive' =>$fifth_level_inactive
			),
			array(
			'level_id' => 6,
			'title' => 'Six',
			'total_user' => isset($levelWiseMember[5]['totalMember']) ? $levelWiseMember[5]['totalMember'] : 0,
				'total_active' =>$six_level_active,
					'total_inactive' =>$six_level_inactive
			),
			array(
			'level_id' => 7,
			'title' => 'Seven',
			'total_user' => isset($levelWiseMember[6]['totalMember']) ? $levelWiseMember[6]['totalMember'] : 0,
				'total_active' =>$seven_level_active,
					'total_inactive' =>$seven_level_inactive
			),
			array(
			'level_id' => 8,
			'title' => 'Eight',
			'total_user' => isset($levelWiseMember[7]['totalMember']) ? $levelWiseMember[7]['totalMember'] : 0,
				'total_active' =>$eight_level_active,
					'total_inactive' =>$eight_level_inactive
			),
			array(
			'level_id' => 9,
			'title' => 'Nine',
			'total_user' => isset($levelWiseMember[8]['totalMember']) ? $levelWiseMember[8]['totalMember'] : 0,
				'total_active' =>$nine_level_active,
					'total_inactive' =>$nine_level_inactive
			),
			array(
			'level_id' => 10,
			'title' => 'Ten',
			'total_user' => isset($levelWiseMember[9]['totalMember']) ? $levelWiseMember[9]['totalMember'] : 0,
				'total_active' =>$ten_level_active,
					'total_inactive' =>$ten_level_inactive
			)
			,
			array(
			'level_id' => 11,
			'title' => 'Eleven',
			'total_user' => isset($levelWiseMember[10]['totalMember']) ? $levelWiseMember[10]['totalMember'] : 0,
				'total_active' =>$eleven_level_active,
					'total_inactive' =>$eleven_level_inactive
			)
			,
			array(
			'level_id' => 12,
			'title' => 'Tweleve',
			'total_user' => isset($levelWiseMember[11]['totalMember']) ? $levelWiseMember[11]['totalMember'] : 0,
				'total_active' =>$tweleve_level_active,
					'total_inactive' =>$tweleve_level_inactive
			)
			,
			array(
			'level_id' => 13,
			'title' => 'Thirteen',
			'total_user' => isset($levelWiseMember[12]['totalMember']) ? $levelWiseMember[12]['totalMember'] : 0,
				'total_active' =>$thirteen_level_active,
					'total_inactive' =>$thirteen_level_inactive
			)
			,
			array(
			'level_id' => 14,
			'title' => 'Fourteen',
			'total_user' => isset($levelWiseMember[13]['totalMember']) ? $levelWiseMember[13]['totalMember'] : 0,
				'total_active' =>$fourteen_level_active,
					'total_inactive' =>$fourteen_level_inactive
			)
			,
			array(
			'level_id' => 15,
			'title' => 'Fiveteen',
			'total_user' => isset($levelWiseMember[14]['totalMember']) ? $levelWiseMember[14]['totalMember'] : 0,
				'total_active' =>$fifteen_level_active,
					'total_inactive' =>$fifteen_level_inactive
			)
			,
			array(
			'level_id' => 16,
			'title' => 'Sixteen',
			'total_user' => isset($levelWiseMember[15]['totalMember']) ? $levelWiseMember[15]['totalMember'] : 0,
				'total_active' =>$sixteen_level_active,
					'total_inactive' =>$sixteen_level_inactive
			)
			,
			array(
			'level_id' => 17,
			'title' => 'Seventeen',
			'total_user' => isset($levelWiseMember[16]['totalMember']) ? $levelWiseMember[16]['totalMember'] : 0,
				'total_active' =>$seventeen_level_active,
					'total_inactive' =>$seventeen_level_inactive
			)
			,
			array(
			'level_id' => 18,
			'title' => 'Eightteen',
			'total_user' => isset($levelWiseMember[17]['totalMember']) ? $levelWiseMember[17]['totalMember'] : 0,
				'total_active' =>$eighteen_level_active,
				'total_inactive' =>$eighteen_level_inactive
			)
			,
			array(
			'level_id' => 19,
			'title' => 'Nineteen',
			'total_user' => isset($levelWiseMember[18]['totalMember']) ? $levelWiseMember[18]['totalMember'] : 0,
				'total_active' =>$nineteen_level_active,
				'total_inactive' =>$nineteen_level_inactive
			)
			,
			array(
			'level_id' => 20,
			'title' => 'Twenty',
			'total_user' => isset($levelWiseMember[19]['totalMember']) ? $levelWiseMember[19]['totalMember'] : 0,
				'total_active' =>$twenty_level_active,
				'total_inactive' =>$twenty_level_inactive
			)
			);

			$data = array();
			if($rank)
			{
				foreach($rank as $key=>$list)
				{
					$data[$key]['level_id'] = $list['level_id'];
					$data[$key]['title'] = $list['title'];
					$data[$key]['total_user'] = $list['total_user'];
					$data[$key]['active_user'] = $list['total_active'];
					$data[$key]['inactive_user'] = $list['total_inactive'];

				}
			}
			$response = array(
				'status' => 1,
				'message' => 'Success',
				'data'=>$data
			);
// 			}
// 		}
		log_message('debug', 'Get Level List API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function getLevelWiseTeamList(){

		$response = array();

		$post = $this->input->post();
		$loggedAccountID = isset($post['userID']) ? $post['userID'] : 0;

		$user_id = $post['userID'];

		$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

		$password = isset($userData['password']) ? $userData['password'] : '';

		$header_data = apache_request_headers();

		$token = isset($header_data['Token']) ? $header_data['Token'] : '';
		
		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

		$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

		if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

			$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.'
			);
		}
		else{

			$level_id = isset($post['level_id']) ? $post['level_id'] : 0;
			$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;

			$limit = $page_no * 50;
			
			// get member direct downline
			$directDownlineList = $this->User->get_account_level_member_list($user_id,$level_id);

			
				$data = array();
				if($directDownlineList)
					{	$i = 0;
						$j = 0;
						foreach($directDownlineList as $key=>$list){

							

								

									$data[$j]['name'] = $list['name'];
									$data[$j]['user_code'] = $list['user_code'];
									$data[$j]['email'] = $list['email'];
									$data[$j]['mobile'] = $list['mobile'];
									$data[$j]['level'] = $list['level'];
									$data[$j]['membership'] = $this->User->get_user_membership_type($list['memberID']);

									

									$j++;

								}
							}   


							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data'=>$data,
								'pages' => 1,
							);
						}
						log_message('debug', 'getLevelWiseTeamList API Response - '.json_encode($response));	
						echo json_encode($response);

					}



					public function iciciAepsActiveAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'AEPS Active Auth API Post Data - '.json_encode($post));   
						$this->load->library('form_validation');
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
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
						$this->form_validation->set_rules('aadhar_photo', 'Aadhar Photo', 'required|xss_clean');
						$this->form_validation->set_rules('pancard_photo', 'Pancard Photo', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Please enter required field'
							);
						}
						else
						{	

							

								$userID = $post['user_id'];
			        			// check user credential
								$chk_user_credential =$this->db->get_where('users',array('id'=>$userID))->num_rows();
								if(!$chk_user_credential)
								{
									$response = array(
										'status' => 0,
										'message' => 'User Id Not Valid'
									);

								}
								else
								{   
									$is_apes_active = 0;
									$activeService = $this->User->account_active_service($userID);
									if(in_array(3, $activeService)){
										$is_apes_active = 1;
									}


									if(!$is_apes_active){

										$response = array(
											'status' => 0,
											'message' => 'Sorry!! AEPS not active.'
										);

									}
									else{        
										$user_icici_aeps_status = $this->User->get_member_icici_aeps_status($userID);

										if($user_aeps_status)
										{
											$response = array(
												'status' => 0,
												'message' => 'Sorry ! AEPS already actived in your account.'
											);
										}
										else
										{
											$aadhar_photo = '';
											$encodedData = $post['aadhar_photo'];
											if(strpos($post['aadhar_photo'], ' ')){
												$encodedData = str_replace(' ','+', $post['aadhar_photo']);
											}
											$profile = base64_decode($encodedData);
											if($profile)
											{
												$file_name = time().rand(1111,9999).'.jpg';
												$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
												$path = 'media/aeps_kyc_doc/';
												$targetDir = $path.$file_name;
												if(file_put_contents($targetDir, $profile)){
													$aadhar_photo = $targetDir;
												}
											}


											$pancard_photo = '';
											$encodedData = $post['pancard_photo'];
											if(strpos($post['pancard_photo'], ' ')){
												$encodedData = str_replace(' ','+', $post['pancard_photo']);
											}
											$profile = base64_decode($encodedData);
											if($profile)
											{
												$file_name = time().rand(1111,9999).'.jpg';
												$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
												$path = 'media/aeps_kyc_doc/';
												$targetDir = $path.$file_name;
												if(file_put_contents($targetDir, $profile)){
													$pancard_photo = $targetDir;
												}
											}

											$api_response = $this->Iciciaeps_model->activeAEPSMember($post,$aadhar_photo,$pancard_photo,$userID);
											$status = $api_response['status'];

											if($status == 1)
											{
												$encodeFPTxnId = $api_response['encodeFPTxnId'];
												$response = array(
													'status' => 1,
													'message' => 'We have sent OTP on your registered mobile, please verfiy.',
													'encodeFPTxnId' => $encodeFPTxnId
												);
											}
											elseif($status == 2)
											{
												$response = array(
													'status' => 2,
													'message' => 'Congratulation ! Your EKYC has been already approved.'
												);
											}
											else
											{
												$response = array(
													'status' => 0,
													'message' => 'Sorry ! Activation failed due to '.$api_response['msg']
												);
											}

										}
									}

								

							}
						}
						log_message('debug', 'AEPS Active Auth API Response - '.json_encode($response));    
						echo json_encode($response);

					}



					public function iciciAepsOtpAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'AEPS OTP Auth API Post Data - '.json_encode($post));  
						$this->load->library('form_validation');
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('encodeFPTxnId', 'Txn ID', 'required|xss_clean');
						$this->form_validation->set_rules('otp_code', 'OTP Code', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Please enter required field'
							);
						}
						else
						{	

							$user_id = $post['user_id'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$userID = $post['user_id'];
								$encodeFPTxnId = $post['encodeFPTxnId'];
			        			// check user credential
								$chk_user_credential =$this->db->get_where('users',array('id'=>$userID))->num_rows();
								if(!$chk_user_credential)
								{
									$response = array(
										'status' => 0,
										'message' => 'User Id Not Valid'
									);

								}
								else
								{   

									$is_apes_active = 0;
									$activeService = $this->User->account_active_service($userID);
									if(in_array(3, $activeService)){
										$is_apes_active = 1;
									}


									if(!$is_apes_active){

										$response = array(
											'status' => 0,
											'message' => 'Sorry!! AEPS not active.'
										);

									}
									else{

										$user_icici_aeps_status = $this->User->get_member_icici_aeps_status($userID);
										if($user_aeps_status)
										{
											$response = array(
												'status' => 0,
												'message' => 'Sorry ! AEPS already actived in your account.'
											);
										}
										else
										{
			                        // check already kyc approved or not
											$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0,'is_icici_aeps'=>1))->num_rows();
											if(!$chk_encode_id)
											{
												$response = array(
													'status' => 0,
													'message' => 'Sorry ! Encoded Transaction ID not valid.'
												);
											}
											else
											{
												$api_response = $this->Iciciaeps_model->aepsOTPAuth($post,$userID,$encodeFPTxnId);
												$status = $api_response['status'];

												if($status == 1)
												{	

													$message = 'Congratulations ! OTP Verified successfully.';

													$this->User->sendNotification($userID,'Aeps OTP',$message);

													$response = array(
														'status' => 1,
														'message' => 'Congratulations ! OTP Verified successfully. Please capture your finger print.',
														'encodeFPTxnId' => $encodeFPTxnId
													);
												}
												else
												{
													$response = array(
														'status' => 0,
														'message' => $api_response['msg']
													);
												}
											}
										}
									}
								}

							}
						}
						log_message('debug', 'AEPS OTP Auth API Response - '.json_encode($response));   
						echo json_encode($response);

					}



					public function iciciAepsResendOtpAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'AEPS Resend OTP Auth API Post Data - '.json_encode($post));   
						$this->load->library('form_validation');
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('encodeFPTxnId', 'Txn ID', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Please enter required field'
							);
						}
						else
						{	
							$user_id = $post['user_id'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$userID = $post['user_id'];
								$encodeFPTxnId = $post['encodeFPTxnId'];
			        			// check user credential
								$chk_user_credential =$this->db->get_where('users',array('id'=>$userID))->num_rows();
								if(!$chk_user_credential)
								{
									$response = array(
										'status' => 0,
										'message' => 'User Id Not Valid'
									);

								}
								else
								{   
									$is_apes_active = 0;
									$activeService = $this->User->account_active_service($userID);
									if(in_array(3, $activeService)){
										$is_apes_active = 1;
									}


									if(!$is_apes_active){

										$response = array(
											'status' => 0,
											'message' => 'Sorry!! AEPS not active.'
										);

									}
									else{
										$user_icici_aeps_status = $this->User->get_member_icici_aeps_status($userID);

										if($user_aeps_status)
										{
											$response = array(
												'status' => 0,
												'message' => 'Sorry ! AEPS already actived in your account.'
											);
										}
										else
										{
			                        // check already kyc approved or not
											$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0,'is_icici_aeps'=>1))->num_rows();
											if(!$chk_encode_id)
											{
												$response = array(
													'status' => 0,
													'message' => 'Sorry ! Encoded Transaction ID not valid.'
												);
											}
											else
											{
												$api_response = $this->Iciciaeps_model->aepsResendOtp($userID,$encodeFPTxnId);
												$status = $api_response['status'];

												if($status == 1)
												{
													$response = array(
														'status' => 1,
														'message' => 'OTP Resent successfully, Please verify.',
														'encodeFPTxnId' => $encodeFPTxnId
													);
												}
												else
												{
													$response = array(
														'status' => 0,
														'message' => $api_response['msg']
													);
												}
											}
										}
									}
								}

							}
						}
						log_message('debug', 'AEPS Resend OTP Auth API Response - '.json_encode($response));    
						echo json_encode($response);

					}


					public function iciciAepsKycBioAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'AEPS KYC Bio Auth API Post Data - '.json_encode($post));  
						$this->load->library('form_validation');
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('encodeFPTxnId', 'Txn ID', 'required|xss_clean');
						$this->form_validation->set_rules('BiometricData', 'BiometricData', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Please enter required field'
							);
						}
						else
						{	
							$user_id = $post['user_id'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$userID = $post['user_id'];
								$encodeFPTxnId = $post['encodeFPTxnId'];
								$biometricData = $post['BiometricData'];
								$iin = '';
								$requestTime = date('Y-m-d H:i:s');
								$txnID = 'FIAK'.time();
			        			// check user credential
								$chk_user_credential =$this->db->get_where('users',array('id'=>$userID))->num_rows();
								if(!$chk_user_credential)
								{
									$response = array(
										'status' => 0,
										'message' => 'User Id Not Valid'
									);

								}
								else
								{   

									$is_apes_active = 0;
									$activeService = $this->User->account_active_service($userID);
									if(in_array(3, $activeService)){
										$is_apes_active = 1;
									}


									if(!$is_apes_active){

										$response = array(
											'status' => 0,
											'message' => 'Sorry!! AEPS not active.'
										);

									}
									else{

										$user_icici_aeps_status = $this->User->get_member_icici_aeps_status($userID);

										if($user_aeps_status)
										{
											$response = array(
												'status' => 0,
												'message' => 'Sorry ! AEPS already actived in your account.'
											);
										}
										else
										{
			                        // check already kyc approved or not
											$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0,'is_icici_aeps'=>1))->num_rows();
											if(!$chk_encode_id)
											{
												$response = array(
													'status' => 0,
													'message' => 'Sorry ! Encoded Transaction ID not valid.'
												);
											}
											else
											{
												$memberData = $this->db->get_where('users',array('id'=>$userID))->row_array();
												$member_code = $memberData['user_code'];

			                            // check already kyc approved or not
												$get_kyc_data = $this->db->get_where('aeps_member_kyc',array('member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0,'is_icici_aeps'=>1))->row_array();
												$primaryKeyId = isset($get_kyc_data['primaryKeyId']) ? $get_kyc_data['primaryKeyId'] : '';
												$encodeFPTxnId = isset($get_kyc_data['encodeFPTxnId']) ? $get_kyc_data['encodeFPTxnId'] : '';
												$pancard_no = isset($get_kyc_data['pancard_no']) ? $get_kyc_data['pancard_no'] : '';
												$aadharNumber = isset($get_kyc_data['aadhar_no']) ? $get_kyc_data['aadhar_no'] : '';
												$mobile = isset($get_kyc_data['mobile']) ? $get_kyc_data['mobile'] : '';
												$recordID = isset($get_kyc_data['id']) ? $get_kyc_data['id'] : 0;

												$api_url = ICICI_AEPS_EKYC_BIOMATRIC_API_URL;

												$PostData = array(
													'member_id' => $member_code,
													'primaryKeyId' => $primaryKeyId,
													'encodeFPTxnId' => $encodeFPTxnId,
													'BiometricData' => $biometricData,
												);

												$api_post_data = http_build_query($PostData);

												$ch = curl_init();
												curl_setopt($ch, CURLOPT_URL,$api_url);
												curl_setopt($ch, CURLOPT_POST, 1);
												curl_setopt($ch, CURLOPT_POSTFIELDS,$api_post_data);
												curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
												curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

												$headers = [
													'Memberid: '.ICICI_AEPS_MEMBER_ID,
													'Txnpwd: '.ICICI_AEPS_PASSWORD,
												];

												curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
												$output = curl_exec ($ch);
												curl_close ($ch);

												$responseData = json_decode($output,true);
												$finalResponse = isset($responseData['message']) ? json_decode($responseData['message'],true) : array();

												$apiData = array(
													'user_id' => $userID,
													'api_url' => $api_url,
													'api_response' => $output,
													'post_data' => json_encode($PostData),
													'created' => date('Y-m-d H:i:s'),
													'created_by' => $userID
												);
												$this->db->insert('aeps_api_response',$apiData);

												if(isset($responseData['status']) && $responseData['status'] == 'SUCCESS')
												{
			                                // update aeps status
													$this->db->where('id',$userID);
													$this->db->update('users',array('icici_aeps_status'=>1));

			                                // update aeps status
													$this->db->where('id',$recordID);
													$this->db->update('aeps_member_kyc',array('status'=>1,'clear_step'=>5));


													$message = 'Congratulation ! Your EKYC has been approved.';

													$this->User->sendNotification($userID,'Aeps Kyc',$message);

													$response = array(
														'status' => 1,
														'message' => 'Congratulation ! Your EKYC has been approved.'
													);
												}
												else
												{
													$response = array(
														'status' => 0,
														'message' => 'Sorry ! Your BiometricData not valid.'
													);
												}
											}
										}
									}
								}

							}
						}
						log_message('debug', 'AEPS KYC Bio Auth API Response - '.json_encode($response));   
						echo json_encode($response);

					}


					public function iciciAepsApiAuth()
					{   
		    			//$post = file_get_contents('php://input');
		    			//$post = json_decode($post, true);
						$request = $_REQUEST['user_data'];
						$post =  json_decode($request,true);

						log_message('debug', 'AEPS api Auth API Post Data - '.json_encode($post));  

						$memberID = $post['userID'];

						

							$loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
							if(!$loggedUser){

								$response = array(
									'status' => 0,
									'message' => 'Sorry ! user not valid.'
								);  
							}
							else{

								$agentID = $loggedUser['user_code'];
								$is_apes_active = 0;
								$activeService = $this->User->account_active_service($memberID);
								if(in_array(3, $activeService)){
									$is_apes_active = 1;
								}


								if(!$is_apes_active){

									$response = array(
										'status' => 0,
										'message' => 'Sorry!! AEPS not active.'
									);

								}
								else{

									$user_aeps_status = $this->User->get_member_icici_aeps_status($memberID);
									$response = array();
									if($user_aeps_status)
									{
										if($post)
										{
											$serviceType = $post['ServiceType'];
											$deviceIMEI = $post['deviceIMEI'];
											$aadharNumber = $post['AadharNumber'];
											$mobile = $post['mobileNumber'];
											$biometricData = $post['BiometricData'];
											$amount = $post['Amount'];
											$iin = $post['IIN'];
											$txn_pin = $loggedUser['decoded_transaction_password'];

											$requestTime = date('Y-m-d H:i:s');
											if($aadharNumber && $mobile && $biometricData && $iin)
											{
												if($serviceType == 'balinfo' || $serviceType == 'ministatement')
												{
													$txnID = 'BINQ'.time();
													$is_bal_info = 1;
													$is_withdrawal = 0;
													$Servicestype = 'GetBalanceaeps';
													if($serviceType == 'ministatement')
													{
														$txnID = 'MNST'.time();
														$Servicestype = 'getministatment';
														$is_bal_info = 0;
													}
													if($amount == 0)
													{
														$api_url = ICICI_AEPS_BALANCE_API_URL;

														$PostData = array(
															'member_id' => $agentID,
															'txn_pin' => $txn_pin,
															'serviceType' => $serviceType,
															'deviceIMEI' => $deviceIMEI,
															'aadharNumber' => $aadharNumber,
															'mobile' => $mobile,
															'biometricData' => $biometricData,
															'amount' => $amount,
															'iin' => $iin,
															'txnID' => $txnID,
														);

														$api_post_data = $PostData;


														$ch = curl_init();
														curl_setopt($ch, CURLOPT_URL,$api_url);
														curl_setopt($ch, CURLOPT_POST, 1);
														curl_setopt($ch, CURLOPT_POSTFIELDS,$api_post_data);
														curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
														curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

														$headers = [
															'Memberid: '.ICICI_AEPS_MEMBER_ID,
															'Txnpwd: '.ICICI_AEPS_PASSWORD,
														];

														curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
														$output = curl_exec ($ch);
														curl_close ($ch);

														$responseData = json_decode($output,true);

														$apiData = array(
															'user_id' => $memberID,
															'api_url' => $api_url,
															'post_data' => json_encode($api_post_data),
															'api_response' => $output,
															'created' => date('Y-m-d H:i:s'),
															'created_by' => $memberID
														);
														$this->db->insert('aeps_api_response',$apiData);

														if(isset($responseData['status']) && $responseData['status'] == 'SUCCESS')
														{
															$this->Iciciaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['status_msg'],2,$memberID);
															$str = '';
															if($is_bal_info == 0)
															{
																$this->Iciciaeps_model->addStatementCom($txnID,$aadharNumber,$iin,$amount,$memberID);
																$statementList = $responseData['data'];
																if($statementList)
																{
																	$str = array();
																	foreach($statementList as $key => $list)
																	{   
																		$str[$key]['date'] = $list['date'];
																		if($list['txnType'] == 'Dr'){

																			$str[$key]['txnType'] = 'DR';
																		}
																		else{

																			$str[$key]['txnType'] = 'CR';
																		}
																		$str[$key]['amount'] = $list['amount'];
																		$str[$key]['narration'] = $list['narration'];

																	}
																}
															}
															$response = array(
																'status' => 1,
																'message' => $responseData['message'],
																'balanceAmount' => $responseData['balanceAmount'],
																'bankRRN' => $responseData['bankRRN'],
																'is_bal_info' => $is_bal_info,
																'is_withdrawal' => $is_withdrawal,
																'invoiceUrl' => '',
																'str' => $str
															);


														}
														else
														{
															$this->Iciciaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['status_msg'],3,$memberID);
															$response = array(
																'status' => 0,
																'message' => $responseData['status_msg'],
															);
														}



													}
													else
													{
														$response = array(
															'status' => 0,
															'message' => 'Sorry ! Amount is not valid.'
														);
													}
												}
												elseif($serviceType == 'balwithdraw' || $serviceType == 'aadharpay')
												{
													$txnID = 'CSWD'.time();
													$is_withdrawal = 1;
													$is_bal_info = 0;
													$Servicestype = 'AccountWithdrowal';
													if($serviceType == 'aadharpay')
													{
														$Servicestype = 'Aadharpay';
														$txnID = 'APAY'.time();
													}

													if($amount >= 100 && $amount <= 10000)
													{
														$api_url = ICICI_AEPS_BALANCE_API_URL;

														$PostData = array(
															'member_id' => $agentID,
															'txn_pin' => $txn_pin,
															'serviceType' => $serviceType,
															'deviceIMEI' => $deviceIMEI,
															'aadharNumber' => $aadharNumber,
															'mobile' => $mobile,
															'biometricData' => $biometricData,
															'amount' => $amount,
															'iin' => $iin,
															'txnID' => $txnID,
														);

														$api_post_data = $PostData;


														$ch = curl_init();
														curl_setopt($ch, CURLOPT_URL,$api_url);
														curl_setopt($ch, CURLOPT_POST, 1);
														curl_setopt($ch, CURLOPT_POSTFIELDS,$api_post_data);
														curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
														curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

														$headers = [
															'Memberid: '.ICICI_AEPS_MEMBER_ID,
															'Txnpwd: '.ICICI_AEPS_PASSWORD,
														];

														curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
														$output = curl_exec ($ch);
														curl_close ($ch);

														$responseData = json_decode($output,true);

														$apiData = array(
															'user_id' => $memberID,
															'api_url' => $api_url,
															'post_data' => json_encode($api_post_data),
															'api_response' => $output,
															'created' => date('Y-m-d H:i:s'),
															'created_by' => $memberID
														);
														$this->db->insert('aeps_api_response',$apiData);

														if(isset($responseData['status']) && $responseData['status'] == 'SUCCESS')
														{   

															$com_type = 0;
															if($service == 'balinfo' || $service == 'ministatement')
															{
																$com_type = 2;
															}
															elseif($service == 'balwithdraw')
															{
																$com_type = 1;
															}
															elseif($service == 'aadharpay'){

																$com_type = 3;
															}

															$this->Iciciaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['status_msg'],2,$memberID);
															$this->Iciciaeps_model->addBalance($txnID,$aadharNumber,$iin,$amount,$memberID);
															$str = array(
																'txnStatus'    => 'Successfull',
																'amount'       => $responseData['transactionAmount'],
																'balanceAmount'=> $responseData['balanceAmount'],
																'bankRRN'      => $responseData['bankRRN']    
															);


															$response = array(
																'status' => 1,
																'message' => $responseData['message'],
																'balanceAmount' => $responseData['balanceAmount'],
																'bankRRN' => $responseData['bankRRN'],
																'is_bal_info' => $is_bal_info,
																'is_withdrawal' => $is_withdrawal,
																'invoiceUrl' => '',
																'str' => $str
															);


														}
														else
														{
															$this->Iciciaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['status_msg'],3,$memberID);
															$response = array(
																'status' => 0,
																'message' => $responseData['status_msg'],
															);
														}



													}
													else
													{
														$response = array(
															'status' => 0,
															'message' => 'Sorry ! Amount should be less than 10000 and grater than or equal 101.'
														);
													}
												}
												else
												{
													$response = array(
														'status' => 0,
														'message' => 'Something Wrong ! Please Try Again Later.'
													);      
												}
											}
											else
											{
												$response = array(
													'status' => 0,
													'message' => 'Sorry ! Please enter required data.'
												);      
											}

										}
										else
										{
											$response = array(
												'status' => 0,
												'message' => 'Something Wrong ! Please Try Again Later.'
											);
										}
									}
								
							}
						}

						log_message('debug', 'AEPS api Auth API Response - '.json_encode($response));
						echo json_encode($response);
					}


					public function getIciciAepsHistory()
					{
						$post = $this->input->post();
						log_message('debug', 'AEPS History API Post Data - '.json_encode($post));   
						$userID = isset($post['userID']) ? $post['userID'] : 0;

						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							$response = array();
							$fromDate = isset($post['fromDate']) ? ($post['fromDate']) ? $post['fromDate'] : date('Y-m-d') : date('Y-m-d');
							$toDate = isset($post['toDate']) ? ($post['toDate']) ? $post['toDate'] : date('Y-m-d') : date('Y-m-d');
			    			// check user valid or not
							$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
							if($chk_user)
							{
								$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.member_id = '$userID' AND a.is_icici_aeps = 1";
								if($fromDate && $toDate)
								{
									$sql.=" AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."'";
								}
								$sql.=" ORDER BY created DESC";
								$historyList = $this->db->query($sql)->result_array();

								$data = array();
								if($historyList)
								{
									foreach ($historyList as $key => $list) {


										$data[$key]['member_code'] = $list['member_code'];
										$data[$key]['member_name'] = $list['member_name'];
										if($list['service'] == 'balwithdraw' || $list['service'] == 'aadharpay')
										{
											$data[$key]['service'] = 'Account Withdrawal';
										}
										elseif($list['service'] == 'balinfo')
										{
											$data[$key]['service'] = 'Balance Info';
										}
										elseif($list['service'] == 'ministatement')
										{
											$data[$key]['service'] = 'Mini Statement';
										}
										$data[$key]['aadhar_no'] = $list['aadhar_no'];
										$data[$key]['mobile'] = $list['mobile'];
										$data[$key]['amount'] = $list['amount'];
										$data[$key]['txnID'] = $list['txnID'];
										if($list['status'] == 2) {
											$data[$key]['status'] = 'Success';
										}
										elseif($list['status'] == 3) {
											$data[$key]['status'] = 'Failed';
										}
										else{
											$data[$key]['status'] = 'Pending';
										}
										$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));

									}
								}

								if($data)
								{
									$response = array(
										'status' => 1,
										'message' => 'Success',
										'data' => $data
									);  
								}
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry ! No Record Found.',
									);  
								}
							}
							else
							{
								$response = array(
									'status' => 0,
									'message' => 'Sorry ! Member not valid.'
								);
							}
						}
						log_message('debug', 'AEPS History API Response - '.json_encode($response));    
						echo json_encode($response);
					}


					public function zoom_detail()
					{
						$data = array();
			// get country list
						$zoom_detail = $this->db->get_where('zoom_detail',array('id'=>1))->row_array();
						$response = array(
							'meeting_time' => $zoom_detail['meeting_time'],
							'meeting_link' => $zoom_detail['meeting_link'],
							'zoom_id' => $zoom_detail['zoom_id'],
							'password' => $zoom_detail['password']
						);


						echo json_encode(array($response));
					}


					public function fundRequestAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'fundRequestAuth API Post Data - '.json_encode($post));	
						$this->load->library('form_validation');
						$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
						$this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
						$this->form_validation->set_rules('txnID', 'txnID', 'required|numeric|min_length[12]|max_length[12]');
						$this->form_validation->set_rules('payment_screenshot', 'Payment Screenshot', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Enter required details.'
							);
						}
						else
						{	
							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{
							    	$check_utr_no = $this->db->get_where('member_fund_request',array('txnid'=>$post['txnID']))->num_rows();
								

								if($post['amount'] < 1){

									$response = array(
										'status' => 0,
										'message'=>'Sorry!! amount not valid.'	
									);
								}
							
								elseif($check_utr_no)
								{
										$response = array(
										'status' => 0,
										'message'=>'Sorry ! UTR No Already Exist in our system.'	
									);
								}
								else{

									$payment_screenshot = '';
									if(isset($post['payment_screenshot']) && !empty($post['payment_screenshot']))
									{
										$encodedData = $post['payment_screenshot'];
										if(strpos($post['payment_screenshot'], ' ')){
											$encodedData = str_replace(' ','+', $post['payment_screenshot']);
										}
										$profile = base64_decode($encodedData);
										$file_name = time().rand(1111,9999).'.jpg';

										$profile_img_name = FILE_UPLOAD_SERVER_PATH.$file_name;
										$path = 'media/payment_screenshot/';
										if (!is_dir($path)) {
											mkdir($path, 0777, true);
										}
										$targetDir = $path.$file_name;
										if(file_put_contents($targetDir, $profile)){
											$payment_screenshot = $targetDir;
										}
									}


									$account_id = $post['userID'];
									$amount = $post['amount'];

						// generate request id
									$request_id = time().rand(111,333);

						//get member wallet_balance
									$get_member_status = $this->db->select('wallet_balance')->get_where('users',array('id'=>$account_id))->row_array();
									$before_wallet_balance = isset($get_member_status['wallet_balance']) ? $get_member_status['wallet_balance'] : 0 ;


									$transfer_amount = $amount;

									$after_wallet_balance = $before_wallet_balance + $amount;

									$service_amount = 0;

									$tokenData = array(
										'request_id' => $request_id,
										'member_id' => $account_id,
										'request_amount' => $amount,
										'txnid' => $post['txnID'],
										'payment_screenshot' => $payment_screenshot,
										'before_wallet_balance' => $before_wallet_balance,
										'service_amount' => $service_amount,
										'transfer_amount' => $transfer_amount,
										'after_wallet_balance' => $after_wallet_balance,
										'status' => 1,
										'created' => date('Y-m-d H:i:s'),
									);
									$status = $this->db->insert('member_fund_request',$tokenData);

									if($status == true)
									{	

										$message = 'Congratulations!! request generated successfully.';

										$this->User->sendNotification($account_id,'Fund Request',$message);


										$response = array(

											'status' => 1,
											'message'=>'Congratulations!! request generated successfully.'	

										);
									}
									else
									{
										$response = array(

											'status' => 0,
											'message'=>'Sorry!! something went wrong.'	

										);
									}
								}	

							}
						}
						log_message('debug', 'fundRequestAuth API Response - '.json_encode($response));	
						echo json_encode($response);

					}


					public function getFundRequestList()
					{
						$post = $this->input->post();
						log_message('debug', 'getFundRequestList API Get Data - '.json_encode($post));	
						$userID = isset($post['userID']) ? $post['userID'] : 0;

						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							$response = array();
							$fromDate = $post['fromDate'];
							$toDate =   $post['toDate'];
							$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
							$limit = $page_no * 50;

			    // check user valid or not
							$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
							if($chk_user)
							{
								$sql = "SELECT a.*,b.name as member_name, b.user_code as member_code FROM tbl_member_fund_request as a LEFT JOIN tbl_users as b ON b.id = a.member_id WHERE a.id > 0 AND a.member_id = '$userID'";
								if($fromDate && $toDate)
								{
									$sql.=" AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."'";

									$sql.=" ORDER BY a.created DESC";

									$count = $this->db->query($sql)->num_rows();

									$limit_start = $limit - 50; 

									$limit_end = 50;

									$sql.=" LIMIT ".$limit_start." ,".$limit_end."";

								}
								else{

									$sql.=" ORDER BY created DESC";

									$count = $this->db->query($sql)->num_rows();

									$limit_start = $limit - 50; 

									$limit_end = 50;

									$sql.=" LIMIT ".$limit_start." ,".$limit_end."";
								}

								$historyList = $this->db->query($sql)->result_array();

								$pages = ceil($count / 50);

								$data = array();
								if($historyList)
								{
									foreach ($historyList as $key => $list) {

										$data[$key]['member_name'] = $list['member_name'].' ('.$list['member_code'].')';
										$data[$key]['request_id'] = $list['request_id'];
										$data[$key]['txnid'] = $list['txnid'];
										$data[$key]['request_amount'] = 'INR '.number_format($list['request_amount'],2);
										if($list['payment_screenshot']){

											$data[$key]['payment_screenshot'] = base_url($list['payment_screenshot']);
										}
										else{

											$data[$key]['payment_screenshot'] = 'Not Available';
										}

										$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));

										if($list['status'] == 1)
										{
											$data[$key]['status'] = 'Pending';
										}
										elseif($list['status'] == 2)
										{
											$data[$key]['status'] = 'Approved';

										}
										elseif($list['status'] == 3)
										{
											$data[$key]['status'] = 'Rejected';
										}

									}
								}

								if($data)
								{
									$response = array(
										'status' => 1,
										'message' => 'Success',
										'data' => $data,
										'pages' => $pages,
									);	
								}
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry ! No Record Found.',
									);	
								}
							}
							else
							{
								$response = array(
									'status' => 0,
									'message' => 'Sorry ! Member not valid.'
								);
							}
						}
						log_message('debug', 'getFundRequestList History API Response - '.json_encode($response));	
						echo json_encode($response);
					}



					public function changeAccountAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'changeAccountAuth API Post Data - '.json_encode($post));	
						$this->load->library('form_validation');
						$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('account_name', 'Account Holder Name', 'required|xss_clean');
						$this->form_validation->set_rules('account_number', 'Account No.', 'required|xss_clean');
						$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
						$this->form_validation->set_rules('bank_name', 'Bank Name', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Please enter all details'
							);
						}
						else
						{
							$userID = $post['userID'];

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$chk_user = $this->db->get_where('users',array('id'=>$userID))->row_array();

								if(!$chk_user){

									$response = array(
										'status' => 0,
										'message' => 'Please enter all details'
									);
								}
								else{

									$get_kyc_detail = $this->db->order_by('id','desc')->get_where('member_kyc_detail',array('member_id'=>$userID))->row_array();

									if(!$get_kyc_detail){

										$response = array(
											'status' => 0,
											'message' => 'Please update your kyc.'
										);
									}
									else{

										$data = array(    
											'account_holder_name' =>  $post['account_name'],
											'account_number'      =>  $post['account_number'],
											'ifsc'                =>  $post['ifsc'],
											'bank_name'           =>  $post['bank_name'],
											'updated'             =>  date('Y-m-d H:i:s')
										);

										$this->db->where('member_id',$userID);
										$this->db->where('id',$get_kyc_detail['id']);
										$this->db->update('member_kyc_detail',$data);

										$message = 'Congratulations!! account changed successfully.';

										$this->User->sendNotification($userID,'Account Change',$message);

										$response = array(
											'status' => 1,
											'message' => 'Congratulations!! account changed successfully.'
										);
									}

								}

							}
						}
						log_message('debug', 'changeAccountAuth API Response - '.json_encode($response));	
						echo json_encode($response);

					}


					public function affiliateList()
					{
						$post = $this->input->post();

						log_message('debug', 'Get affiliateList List API Post Data - '.json_encode($_POST));	

						$response = array();
						$operator = $this->db->get('affiliate')->result_array();

						$data = array();
						if($operator)
						{
							foreach ($operator as $key => $value) {
								$data[$key]['title'] = $value['title'];
								$data[$key]['image'] = $value['image'];
								$data[$key]['link'] = $value['link'];
							}
						}

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data' => $data
						);
						log_message('debug', 'Get affiliateList API Response - '.json_encode($response));	
						echo json_encode($response);
					}	


					public function notificationList()
					{
						$post = $this->input->post();

						$userID = isset($post['userID']) ? $post['userID'] : '';

						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							log_message('debug', 'Get notificationList List API Post Data - '.json_encode($post));	

							$response = array();

							if(!$userID){

								$response = array(

									'status' => 0,
									'message'=> 'Please enter userID'

								);	

							}
							else{

								$this->db->where('user_id',$userID);
								$this->db->update('app_notification',array('is_new'=>0));

								$today = date('Y-m-d');
								$operator = $this->db->query("SELECT * FROM tbl_app_notification WHERE DATE(created) = '$today' AND user_id = '$userID' ORDER BY id DESC")->result_array();

								$data = array();
								if($operator)
								{
									foreach ($operator as $key => $value) {
										$data[$key]['title'] = $value['title'];
										$data[$key]['description'] = $value['description'];
										$data[$key]['image_url'] = base_url($value['image']);
									}
								}

								$response = array(
									'status' => 1,
									'message' => 'Success',
									'data' => $data,
								);
							}
						}

						log_message('debug', 'Get notificationList API Response - '.json_encode($response));	
						echo json_encode($response);
					}



					public function activeCyrusQrAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'activeCyrusQrAuth API Post Data - '.json_encode($post));	
						$this->load->library('form_validation');
						$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
						$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
						$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
						$this->form_validation->set_rules('pancard_no', 'Pancard No', 'required|xss_clean');
						$this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|xss_clean');
						$this->form_validation->set_rules('zip_code', 'Zip Code', 'required|xss_clean');
						$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Please enter all valid details'
							);
						}
						else
						{
							$userID = $post['userID'];

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$chk_user = $this->db->get_where('users',array('id'=>$userID))->row_array();

								if(!$chk_user){

									$response = array(
										'status' => 0,
										'message' => 'Sorry!! user not valid.'
									);
								}
								else{


									$chk_cyrus_qr_active = $this->db->get_where('users',array('id'=>$userID))->row_array();

									if($chk_cyrus_qr_active['is_cyrus_qr_active'] == 1){

										$response = array(

											'status'  => 0,
											'message' => 'Sorry!! qr already activated.'

										);
									}
									else{


										$data = array(

											'user_id'    => $userID,
											'name'       => $post['name'],
											'email'      => $post['email'],
											'mobile'     => $post['mobile'],
											'pancard_no' => $post['pancard_no'],
											'aadhar_no'  => $post['aadhar_no'],
											'zip_code'   => $post['zip_code'],
											'address'    => $post['address'],
											'status'     => 1, 
											'created'    => date('Y-m-d H:i:s')

										);

										$this->db->insert('cyrus_qr_activation',$data);

										$activation_id = $this->db->insert_id();

										$api_url = CYRUS_UPI_API_URL;

										$post_data = 'MerchantID='.CYRUS_MERCHANT_ID.'&MerchantKey='.CYRUS_MERCHANT_KEY.'&MethodName=REGISTRATION&Mobile='.$post['mobile'].'&Email='.$post['email'].'&Company=Sonikapay&Name=Sonikapay&Pan='.$post['pancard_no'].'&Pincode='.$post['zip_code'].'&Address='.$post['address'].'&Aadhar='.$post['aadhar_no'].'&OTP=123456';

										$curl = curl_init();
										curl_setopt_array($curl, array(
											CURLOPT_URL => $api_url,
											CURLOPT_RETURNTRANSFER => true,
											CURLOPT_ENCODING => '',
											CURLOPT_MAXREDIRS => 10,
											CURLOPT_TIMEOUT => 0,
											CURLOPT_FOLLOWLOCATION => true,
											CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
											CURLOPT_CUSTOMREQUEST => 'POST',
											CURLOPT_POSTFIELDS => $post_data,
											CURLOPT_HTTPHEADER => array(
												'Content-Type: application/x-www-form-urlencoded',
											),
										));

										$response = curl_exec($curl);

										curl_close($curl);

										$responseData = json_decode($response,true);


										$api_data = array(

											'user_id'   => $userID,
											'api_url'   => $api_url,
											'post_data' => json_encode($post_data),
											'response'  => $response,
											'created'   => date('Y-m-d H:i:s')

										);

										$this->db->insert('cyrus_upi_api_response',$api_data);

										$response_id = $this->db->insert_id();


										if(isset($responseData) && $responseData['statuscode'] == "TXN" && $responseData['status'] == "Transaction Successful"){

											$response = array(

												'status'  => 1,
												'message'	=> 'Otp sent to your aadhar linked number. Please verify.',
												'response_id' => $response_id,
												'activation_id' => $activation_id

											);
										}
										else{


											$error = isset($responseData['status']) ? $responseData['status'] : 'Sorry!! activation failed. Please try again with valid details.';

											$response = array(
												'status'  => 0,
												'message'	=> $error,
											);
										}

									}


								}

							}
						}
						log_message('debug', 'activeCyrusQrAuth API Response - '.json_encode($response));	
						echo json_encode($response);

					}	



					public function activeCyrusQrOtpAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'activeCyrusQrOtpAuth API Post Data - '.json_encode($post));	
						$this->load->library('form_validation');
						$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('otp_code', 'Otp Code', 'required|xss_clean');
						$this->form_validation->set_rules('response_id', 'response_id', 'required|xss_clean');
						$this->form_validation->set_rules('activation_id', 'activation_id', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Please enter valid OTP'
							);
						}
						else
						{
							$userID = $post['userID'];

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$otp_code = $post['otp_code'];
								$response_id = $post['response_id'];
								$activation_id = $post['activation_id'];

								$chk_user = $this->db->get_where('users',array('id'=>$userID))->row_array();

								if(!$chk_user){

									$response = array(
										'status' => 0,
										'message' => 'Sorry!! user not valid.'
									);
								}
								else{


									$chk_cyrus_qr_active = $this->db->get_where('users',array('id'=>$userID))->row_array();

									if($chk_cyrus_qr_active['is_cyrus_qr_active'] == 1){

										$response = array(

											'status'  => 0,
											'message' => 'Sorry!! qr already activated.'

										);
									}
									else{


										$chk_response = $this->db->get_where('cyrus_upi_api_response',array('user_id'=>$userID,'id'=>$response_id))->row_array();

										if(!$chk_response){

											$response = array(

												'status'  => 0,
												'message' => 'Sorry!! something went wrong.'	

											);
										}
										else{


											$chk_activation = $this->db->get_where('cyrus_qr_activation',array('user_id'=>$userID,'id'=>$activation_id))->row_array();

											if(!$chk_activation){

												$response = array(

													'status'  => 0,
													'message' => 'Sorry!! something went wrong.'	

												);
											}
											else{

												$otp_response_data = json_decode($chk_response['response'],true);

												$otpReferenceID = $otp_response_data['data']['data']['otpReferenceID'];

												$hash = $otp_response_data['data']['data']['hash'];


												$api_url = CYRUS_UPI_API_URL;

												$post_data = 'MerchantID='.CYRUS_MERCHANT_ID.'&MerchantKey='.CYRUS_MERCHANT_KEY.'&MethodName=submitotp&Mobile='.$chk_activation['mobile'].'&Email='.$chk_activation['email'].'&Company=Sonikapay&Name=Sonikapay&Pan='.$chk_activation['pancard_no'].'&Pincode='.$chk_activation['zip_code'].'&Address='.$chk_activation['address'].'&Aadhar='.$chk_activation['aadhar_no'].'&OTP='.$post['otp_code'].'&otpReferenceID='.$otpReferenceID.'&hash='.urlencode($hash).'';

												$curl = curl_init();
												curl_setopt_array($curl, array(
													CURLOPT_URL => $api_url,
													CURLOPT_RETURNTRANSFER => true,
													CURLOPT_ENCODING => '',
													CURLOPT_MAXREDIRS => 10,
													CURLOPT_TIMEOUT => 0,
													CURLOPT_FOLLOWLOCATION => true,
													CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
													CURLOPT_CUSTOMREQUEST => 'POST',
													CURLOPT_POSTFIELDS => $post_data,
													CURLOPT_HTTPHEADER => array(
														'Content-Type: application/x-www-form-urlencoded',
													),
												));

												$response = curl_exec($curl);

												curl_close($curl);

												$responseData = json_decode($response,true);


												$api_data = array(

													'user_id'   => $userID,
													'api_url'   => $api_url,
													'post_data' => json_encode($post_data),
													'response'  => $response,
													'created'   => date('Y-m-d H:i:s')

												);

												$this->db->insert('cyrus_upi_api_response',$api_data);

												$response_id = $this->db->insert_id();


												if(isset($responseData) && $responseData['statuscode'] == "TXN" && $responseData['status'] == "Transaction Successful"){

													$outlet_id = $responseData['data']['data']['outletId'];

													$this->db->where('id',$userID);
													$this->db->update('users',array('is_cyrus_qr_active'=>1,'cyrus_qr_outlet_id'=>$outlet_id));

													$response = array(

														'status'  => 1,
														'message'	=> 'Qr Activated Successfully.'

													);
												}
												else{


													$error = isset($responseData['status']) ? $responseData['status'] : 'Sorry!! activation failed. Please try again with valid details.';

													$response = array(

														'status'  => 0,
														'message'	=> $error

													);

												}
											}
										}

									}


								}

							}
						}
						log_message('debug', 'activeCyrusQrOtpAuth API Response - '.json_encode($response));	
						echo json_encode($response);

					}	


					public function staticCyrusQrAuth(){

						$response = array();

						$post = $this->input->post();

						$userID = isset($post['userID']) ? $post['userID'] : '';

						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							if(!$userID){

								$resposne = array(

									'status' => 0,
									'message'=>'Please enter userID.'	

								);
							}
							else{


								$chk_cyrus_qr_active = $this->db->get_where('users',array('id'=>$userID))->row_array();

								if($chk_cyrus_qr_active['is_cyrus_qr_active'] != 1){

									$response = array(

										'status'  => 0,
										'message'	=>'Sorry!! qr not activated.'

									);
								}
								else{

									$response = array(
										'status' => 1,
										'message' => 'Success',
										'qr_code'=>base_url('Cyrusqrcode/index/'.$userID.'')
									);
								}
							}
						}

						echo json_encode($response);

					}



					public function vendorRegisterAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'vendorRegisterAuth API Post Data - '.json_encode($post));	
						$this->load->library('form_validation');
						$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
						$this->form_validation->set_rules('business_name', 'business_name', 'required|xss_clean');
						$this->form_validation->set_rules('mobile', 'mobile', 'required|numeric|xss_clean');
						$this->form_validation->set_rules('commission', 'commission', 'required|numeric|xss_clean');
						$this->form_validation->set_rules('address', 'address', 'required|xss_clean');
						$this->form_validation->set_rules('description', 'description', 'required|xss_clean');
						$this->form_validation->set_rules('latitude', 'latitude', 'required|xss_clean');
						$this->form_validation->set_rules('longitude', 'longitude', 'required|xss_clean');
						$this->form_validation->set_rules('pincode', 'Pincode', 'required|xss_clean');
						$this->form_validation->set_rules('city_id', 'District', 'required|xss_clean');
						$this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');

						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! enter all details.'
							);
						}
						else
						{	
							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$userID = $post['userID'];	

								$chk_user = $this->db->get_where('users',array('id' => $userID))->row_array();

								if(!$chk_user){

									$response = array(

										'status'  => 0,
										'message' => 'Sorry!! user not valid.'

									);

								}
								else{


									$chk_vendor = $this->db->get_where('store_vendor',array('user_id'=>$userID,'status < '=>3))->row_array();

									if($chk_vendor){

										$response = array(

											'status'  => 0,
											'message' => 'Sorry!! you already applied for vendor.'

										);
									}
									else{


										if(trim($post['mobile']) == trim($post['alternate_mobile'])){

											$response = array(

												'status'  => 0,
												'message' => 'Sorry!! mobile no. and alternate mobile can not be same.'

											);
										}
										else{

											$chk_vendor_status = $this->db->get_where('store_vendor',array('user_id'=>$userID))->row_array();

											$profile = '';
											if(isset($post['profile']) && !empty($post['profile']))
											{
												$encodedData = $post['profile'];
												if(strpos($post['profile'], ' ')){
													$encodedData = str_replace(' ','+', $post['profile']);
												}
												$profile = base64_decode($encodedData);
												$file_name = time().rand(1111,9999).'.jpg';
								// 	$profile_img_name = base_url('media/user_profile/'.$file_name);
												$profile_img_name = FILE_UPLOAD_SERVER_PATH.$file_name;
												$path = 'media/member/';
												if (!is_dir($path)) {
													mkdir($path, 0777, true);
												}
												$targetDir = $path.$file_name;
												if(file_put_contents($targetDir, $profile)){
													$profile = $targetDir;
												}
											}

											$data = array(

												'user_id'          => $userID,
												'business_name'    => $post['business_name'],
												'mobile'           => $post['mobile'],
												'alternate_mobile' => $post['alternate_mobile'],
												'gst_no'           => $post['gst_no'],
												'commission'       => $post['commission'],
												'address'          => $post['address'],
												'description'      => $post['description'],
												'latitude'         => $post['latitude'],
												'longitude'        => $post['longitude'],
												'pincode'          => $post['pincode'],
												'city_id'          => $post['city_id'],
												'state_id'         => $post['state_id'],
												'profile'          => $profile, 
												'status'           => 1,
												'created'          => date('Y-m-d H:i:s')    

											);

											if($chk_vendor_status['status'] == 3){

												$this->db->where('user_id',$userID);
												$this->db->update('store_vendor',$data);
											}
											else{

												$this->db->insert('store_vendor',$data);
											}

											$response = array(

												'status'  => 1,
												'message' => 'Congratulations!! vendor request sent successfully.'

											);
										}

									}

								}			

							}
						}
						log_message('debug', 'vendorRegisterAuth API Response - '.json_encode($response));	
						echo json_encode($response);

					}



					public function generateVendorBillAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'generateVendorBillAuth API Post Data - '.json_encode($post));	
						$this->load->library('form_validation');
						$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
						$this->form_validation->set_rules('member_id', 'member_id', 'required|xss_clean');
						$this->form_validation->set_rules('amount', 'amount', 'required|numeric|xss_clean');

						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! enter all details.'
							);
						}
						else
						{

							$userID = $post['userID'];

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{	

								$chk_user = $this->db->get_where('users',array('id' => $userID))->row_array();

								if(!$chk_user){

									$response = array(

										'status'  => 0,
										'message' => 'Sorry!! user not valid.'

									);

								}
								else{


									$chk_vendor = $this->db->get_where('store_vendor',array('user_id'=>$userID,'status'=>2))->row_array();

									if(!$chk_vendor){


										$response = array(

											'status'  => 0,
											'message'	=> 'Sorry!! your vendor request not approved' 

										);

									}
									else{

										$chk_member = $this->db->get_where('users',array('user_code'=>$post['member_id']))->row_array();

										if(!$chk_member){

											$response = array(

												'status'  => 0,
												'message'	=> 'Sorry!! memberID not valid' 

											);

										}
										else{

											$vendor_id = $chk_vendor['id'];

											$bill_id = 'VBILL'.time();

											$billData = array(

												'user_id'   => $userID,
												'vendor_id' => $vendor_id,
												'member_id' => $post['member_id'],
												'bill_id'   => $bill_id,
												'amount'    => $post['amount'],
												'created'   => date('Y-m-d H:i:s')	

											);

											$this->db->insert('vendor_bill',$billData);

											$response = array(

												'status'  => 1,
												'message'	=> 'Bill generated successfully.' 

											);
										}

									}

								}			

							}
						}
						log_message('debug', 'generateVendorBillAuth API Response - '.json_encode($response));	
						echo json_encode($response);

					}



					public function vendorBillList()
					{
						$post = $this->input->post();

						$userID = isset($post['userID']) ? $post['userID'] : '';

						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							log_message('debug', 'Get vendorBillList List API Post Data - '.json_encode($post));	

							$response = array();

							if(!$userID){

								$response = array(

									'status' => 0,
									'message'=> 'Please enter userID'

								);	

							}
							else{

								$operator = $this->db->query("SELECT a.*,b.business_name,c.name,c.user_code FROM tbl_vendor_bill as a INNER JOIN tbl_store_vendor as b ON a.vendor_id = b.id INNER JOIN tbl_users as c ON c.id = a.user_id WHERE a.user_id = '$userID' ORDER BY a.id DESC")->result_array();

								$data = array();
								if($operator)
								{
									foreach ($operator as $key => $value) {
										$data[$key]['bill_id'] = $value['bill_id'];
										$data[$key]['user'] = $value['name'].'('.$value['user_code'].')';
										$data[$key]['business_name'] = $value['business_name'];
										$data[$key]['member_id'] = $value['member_id'];
										$data[$key]['amount'] = $value['amount'].' /-';
										$data[$key]['date'] = date('d-M-Y',strtotime($value['created']));
									}
								}

								$response = array(
									'status' => 1,
									'message' => 'Success',
									'data' => $data,
								);
							}
						}

						log_message('debug', 'Get vendorBillList API Response - '.json_encode($response));	
						echo json_encode($response);
					}




					public function searchStoreAuth()
					{
						$post = $this->input->post();

						$user_id = isset($post['userID']) ? $post['userID'] : '';

						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							$state_id = isset($post['state_id']) ? $post['state_id'] : '';
							$city_id = isset($post['city_id']) ? $post['city_id'] : '';
							$pincode = isset($post['pincode']) ? $post['pincode'] : '';

							log_message('debug', 'Get searchStoreAuth List API Post Data - '.json_encode($post));	

							$response = array();

							if(!$user_id){

								$response = array(

									'status' => 0,
									'message'=> 'Please enter userID'

								);	

							}
							else{


								$get_user_address = $this->db->get_where('user_residential_address',array('user_id'=>$user_id))->row_array();

								$user_pincode = isset($get_user_address['pincode']) ? $get_user_address['pincode'] : '';
								$user_state_id = isset($get_user_address['state_id']) ? $get_user_address['state_id'] : '';
								$user_city_id = isset($get_user_address['city_id']) ? $get_user_address['city_id'] : '';



								if($state_id || $city_id || $pincode){

									$sql = "SELECT a.*,b.name,b.user_code,c.name as state,d.city_name as city FROM tbl_store_vendor as a INNER JOIN tbl_users as b ON a.user_id = b.id INNER JOIN tbl_states as c ON a.state_id = c.id INNER JOIN tbl_city as d ON d.city_id = a.city_id WHERE a.status = 2";

									if($state_id){

										$sql.=" AND a.state_id = '$state_id'";
									}

									if($city_id){

										$sql.=" AND a.city_id = '$city_id'";
									}

									if($pincode){

										$sql.=" AND a.pincode = '$pincode'";
									}
								}
								else{

									$sql = "SELECT a.*,b.name,b.user_code,c.name as state,d.city_name as city FROM tbl_store_vendor as a INNER JOIN tbl_users as b ON a.user_id = b.id INNER JOIN tbl_states as c ON a.state_id = c.id INNER JOIN tbl_city as d ON d.city_id = a.city_id WHERE a.pincode = '$user_pincode' AND a.state_id = '$user_state_id' AND a.city_id = '$user_city_id' AND a.status = 2";
								}

								$operator = $this->db->query($sql)->result_array();

								$data = array();
								if($operator)
								{
									foreach ($operator as $key => $value) {

										$data[$key]['profile'] = base_url($value['profile']);	
										$data[$key]['business_name'] = $value['business_name'];
										$data[$key]['address'] = $value['address'];
										$data[$key]['mobile'] = $value['mobile'];
										$data[$key]['pincode'] = $value['pincode'];
										$data[$key]['city'] = $value['city'];
										$data[$key]['state'] = $value['state'];
										$data[$key]['description'] = $value['description'];
									}

									$response = array(
										'status' => 1,
										'message' => 'Success',
										'data' => $data,
									);
								}
								else{


									$response = array(
										'status' => 0,
										'message' => 'Sorry!! store not found for this location',
									);	

								}

							}
						}

						log_message('debug', 'Get searchStoreAuth API Response - '.json_encode($response));	
						echo json_encode($response);	
					}


					public function getVendorRequestDetails(){

						$response = array("status"=>0);
						$post = $this->input->post();


						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							log_message('debug', 'Get Vendor Request Details API Post Data - '.json_encode($post));
							$this->load->library('form_validation');
			    //$this->form_validation->set_data($this->input->get());
							$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
							if ($this->form_validation->run() == FALSE)
							{
								$response['message'] = lang('LOGIN_VALID_FAILED');
							}else{
			        // check user valid or not
								$response['message'] = lang('USER_ID_ERROR');
								$chk_user = $this->db->get_where('users',array('id'=>$post['userID']))->num_rows();
								if(!empty($chk_user))
								{
									$data = array();
									$query = $this->db->get_where('store_vendor', array('user_id'=>$post['userID']));
									if($query->num_rows() > 0)
									{
										$row = $query->row_array();
										$status = 'Pending';
										if($row['status']==2){
											$status = 'Approved';
										}elseif ($row['status']==3){
											$status = 'Rejected';
										}
										$data = array(
											'profile' => base_url($row['profile']),
											'business_name' => $row['business_name'],
											'mobile' => $row['mobile'],
											'alternate_mobile' => $row['alternate_mobile'],
											'gst_no' => $row['gst_no'],
											'commission' => $row['commission'],
											'address' => $row['address'],
											'description' => $row['description'],
											'latitude' => $row['latitude'],
											'longitude' => $row['longitude'],
											'pincode' => $row['pincode'],
											'city_id' => $row['city_id'],
											'state_id' => $row['state_id'],
											'status' => $row['status'],
											'status_title' => $status
										);

										$response = array(
											'status' => 1,
											'message'=>'Success',
											'data' => $data
										);
									}
									else{

										$response = array(
											'status' => 0,
											'message'=>'detail not found.',
										);
									}
								}

							}
						}
						log_message('debug', 'Get Vendor Request Details Response - '.json_encode($response));
						echo json_encode($response);
					}



					public function getVendorPackageList()
					{
						$response = array();
						$post = $this->input->post();
						$this->load->library('form_validation');
		    //$this->form_validation->set_data($this->input->get());
						$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response['message'] = lang('LOGIN_VALID_FAILED');
						}else{

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$get_store_vendor = $this->db->get_where('store_vendor',array('user_id'=>$post['userID']))->row_array();

								$user_id = $post['userID'];

								$vendor_id = isset($get_store_vendor['id']) ? $get_store_vendor['id'] : '';

								$package = $this->db->get_where('vendor_package',array('status'=>1))->result_array();

								$today_date = date('Y-m-d');		

								$data = array();
								if($package)
								{
									foreach ($package as $key => $value) {

										$package_id = $value['id'];

										$chk_package_purchase = $this->db->query("SELECT * FROM tbl_vendor_package_purchase_history WHERE user_id = '$user_id' AND vendor_id = '$vendor_id' AND end_date > '$today_date' AND package_id = '$package_id'")->row_array();

										$is_purchased = 0;
										if($chk_package_purchase){

											$is_purchased = 1;	
										}

										$data[$key]['package_id'] = $value['id'];
										$data[$key]['package_display_id'] = $value['package_display_id'];
										$data[$key]['package_name'] = $value['package_name'];
										$data[$key]['type'] = $value['type'];
										$data[$key]['duration'] = 'Monthly';
										$data[$key]['package_amount'] = $value['package_amount'];
										$data[$key]['is_purchased'] = $is_purchased;
									}
								}

								$response = array(
									'status' => 1,
									'message' => 'Success',
									'data' => $data
								);
							}
						}
						echo json_encode($response);
					}



					public function vendorPackagePurchaseAuth()
					{
						$response = array();
						$post = $this->input->post();
						$this->load->library('form_validation');
		    //$this->form_validation->set_data($this->input->get());
						$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('package_id', 'Package ID', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{	
							$response = array(
								'status' => 0,
								'message' => lang('LOGIN_VALID_FAILED'),
							);

						}else{

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$get_store_vendor = $this->db->get_where('store_vendor',array('user_id'=>$post['userID']))->row_array();

								$user_id = $post['userID'];

								$vendor_id = isset($get_store_vendor['id']) ? $get_store_vendor['id'] : '';

								$package = $this->db->get_where('vendor_package',array('status'=>1))->result_array();

								$today_date = date('Y-m-d');

								$package_id = $post['package_id'];

								$chk_package_purchase = $this->db->query("SELECT * FROM tbl_vendor_package_purchase_history WHERE user_id = '$user_id' AND vendor_id = '$vendor_id' AND end_date > '$today_date' AND package_id = '$package_id'")->row_array();

								if($chk_package_purchase){

									$response = array(

										'status'  => 0,
										'message' => 'Sorry!! this package already purchased'
									);
								}
								else{


									$get_package_data = $this->db->get_where('vendor_package',array('id'=>$package_id))->row_array();

									$package_amount = isset($get_package_data['package_amount']) ? $get_package_data['package_amount'] : 0;

									$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();

									$wallet_balance = isset($user_data['wallet_balance']) ? $user_data['wallet_balance'] : 0;

									if($wallet_balance < $package_amount){

										$response = array(

											'status'  => 0,
											'message'	=> 'Sorry!! insufficient balance in your wallet.'

										);
									}
									else{


										$before_balance = $this->db->get_where('users',array('id'=>$user_id))->row_array();

										$after_balance = $before_balance['wallet_balance'] - $package_amount;    

										$wallet_data = array(
											'member_id'           => $user_id,    
											'before_balance'      => $before_balance['wallet_balance'],
											'amount'              => $package_amount,  
											'after_balance'       => $after_balance,      
											'status'              => 1,
											'type'                => 2,      
											'created'             => date('Y-m-d H:i:s'),      
											'description'         => 'Vendor package purchase Amount Deducted.'
										);

										$this->db->insert('member_wallet',$wallet_data);

										$user_wallet = array(
											'wallet_balance'=>$after_balance,        
										);    
										$this->db->where('id',$user_id);
										$this->db->update('users',$user_wallet);

										$start_date = date('Y-m-d');

										$end_date = date('Y-m-d', strtotime($start_date. ' + 1 months'));

										$packageData = array(

											'user_id'       => $user_id,
											'vendor_id'     => $vendor_id,
											'package_id'    => $package_id,
											'package_amount'=> $package_amount,
											'start_date'    => $start_date,
											'end_date'	  => $end_date,
											'created'       => date('Y-m-d H:i:s')	 	

										);

										$this->db->insert('vendor_package_purchase_history',$packageData);

										$response = array(

											'status'   => 1,
											'message'  => 'Congratulations!! package purchased successfully.'
										);	

									}


								}
							}
						}
						echo json_encode($response);
					}



					public function uploadVendorBanner()
					{
						$response = array();
						$post = $this->input->post();
						$this->load->library('form_validation');
		    //$this->form_validation->set_data($this->input->get());
						$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('banner', 'Banner', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{	
							$response = array(
								'status' => 0,
								'message' => lang('LOGIN_VALID_FAILED'),
							);

						}else{

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$get_store_vendor = $this->db->get_where('store_vendor',array('user_id'=>$post['userID']))->row_array();

								$user_id = $post['userID'];

								$vendor_id = isset($get_store_vendor['id']) ? $get_store_vendor['id'] : '';

								$today_date = date('Y-m-d');

								$chk_package_purchase = $this->db->query("SELECT * FROM tbl_vendor_package_purchase_history WHERE user_id = '$user_id' AND vendor_id = '$vendor_id' AND end_date > '$today_date'")->row_array();

								if(!$chk_package_purchase){

									$response = array(

										'status'  => 0,
										'message' => 'Sorry!! package not purchased.'
									);
								}
								else{

									$banner = '';
									if(isset($post['banner']) && !empty($post['banner']))
									{
										$encodedData = $post['banner'];
										if(strpos($post['banner'], ' ')){
											$encodedData = str_replace(' ','+', $post['banner']);
										}
										$profile = base64_decode($encodedData);
										$file_name = time().rand(1111,9999).'.jpg';
						// 	$profile_img_name = base_url('media/user_profile/'.$file_name);
										$profile_img_name = FILE_UPLOAD_SERVER_PATH.$file_name;
										$path = 'media/vendor_banner/';
										if (!is_dir($path)) {
											mkdir($path, 0777, true);
										}
										$targetDir = $path.$file_name;
										if(file_put_contents($targetDir, $profile)){
											$banner = $targetDir;
										}
									}


									$bannerData = array(

										'user_id'   => $user_id,
										'vendor_id' => $vendor_id,
										'banner'    => $banner,
										'status'    => 1,
										'created'   => date('Y-m-d H:i:s')	

									);

									$this->db->insert('vendor_banner',$bannerData);

									$response = array(

										'status'  => 1,
										'message'	=> 'Banner uploaded successfully.'

									);

								}
							}
						}
						echo json_encode($response);
					}


					public function getVendorBannerList()
					{
						$response = array();
						$post = $this->input->post();
						$this->load->library('form_validation');
		    //$this->form_validation->set_data($this->input->get());
						$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response['message'] = lang('LOGIN_VALID_FAILED');
						}else{

							$get_store_vendor = $this->db->get_where('store_vendor',array('user_id'=>$post['userID']))->row_array();

							$user_id = $post['userID'];

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$vendor_id = isset($get_store_vendor['id']) ? $get_store_vendor['id'] : '';

								$banner = $this->db->get_where('vendor_banner',array('user_id'=>$user_id,'vendor_id'=>$vendor_id))->result_array();


								$data = array();
								if($banner)
								{
									foreach ($banner as $key => $value) {

										$data[$key]['id'] = $value['id'];
										$data[$key]['banner'] = base_url($value['banner']);

										if($value['status'] == 1){

											$data[$key]['status'] = 'Pending';
										}
										elseif($value['status'] == 2){

											$data[$key]['status'] = 'Approved';
										}
										elseif($value['status'] == 3){

											$data[$key]['status'] = 'Rejected';
										}
										else{

											$data[$key]['status'] = 'Pending';
										}

										$data[$key]['date'] = $value['created'];
									}
								}

								$response = array(
									'status' => 1,
									'message' => 'Success',
									'data' => $data
								);
							}
						}
						echo json_encode($response);
					}



					public function deleteVendorBannerAuth()
					{
						$response = array();
						$post = $this->input->post();
						$this->load->library('form_validation');
		    //$this->form_validation->set_data($this->input->get());
						$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('banner_id', 'User ID', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response['message'] = lang('LOGIN_VALID_FAILED');
						}
						else{

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$chk_banner = $this->db->get_where('vendor_banner',array('user_id'=>$post['userID'],'id'=>$post['banner_id']))->row_array();

								if(!$chk_banner){

									$response = array(

										'status'  => 0,
										'message' =>'Sorry!! something went wrong.'	

									);
								}
								else{

									$this->db->where('user_id',$post['userID']);
									$this->db->where('id',$post['banner_id']);
									$this->db->delete('vendor_banner');

									$response = array(

										'status'  => 1,
										'message' =>'Banner deleted successfully.'	

									);
								}
							}	
						}
						echo json_encode($response);
					}






					public function getAreaBannerList()
					{
						$response = array();
						$post = $this->input->post();
						$this->load->library('form_validation');
		    //$this->form_validation->set_data($this->input->get());
						$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response['message'] = lang('LOGIN_VALID_FAILED');
						}else{

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$user_id = $post['userID'];

								$today_date = date('Y-m-d');


								$get_user_address = $this->db->get_where('user_residential_address',array('user_id'=>$user_id))->row_array();

								$user_pincode = isset($get_user_address['pincode']) ? $get_user_address['pincode'] : '';

								$user_district = isset($get_user_address['city_id']) ? $get_user_address['city_id'] : '';

								$user_state = isset($get_user_address['state_id']) ? $get_user_address['state_id'] : '';

								$banner = $this->db->query("SELECT * FROM tbl_vendor_banner WHERE status = 2")->result_array();


								$data = array();
								if($banner)
								{
									foreach ($banner as $key => $value) {

										$vendor_id = $value['vendor_id'];

										$get_store_vendor = $this->db->get_where('store_vendor',array('id'=>$vendor_id))->row_array();

										$vendor_pincode = isset($get_store_vendor['pincode']) ? $get_store_vendor['pincode'] : '';

										$vendor_city = isset($get_store_vendor['city_id']) ? $get_store_vendor['city_id'] : '';

										$vendor_state = isset($get_store_vendor['state_id']) ? $get_store_vendor['state_id'] : '';

										$chk_vendor_pincode_package = $this->db->query("SELECT a.* FROM tbl_vendor_package_purchase_history AS a INNER JOIN tbl_vendor_package as b ON a.package_id = b.id WHERE b.type = 'Pincode' AND a.end_date > '$today_date' AND a.vendor_id = '$vendor_id'")->row_array();

										$chk_vendor_district_package = $this->db->query("SELECT a.* FROM tbl_vendor_package_purchase_history AS a INNER JOIN tbl_vendor_package as b ON a.package_id = b.id WHERE b.type = 'District' AND a.end_date > '$today_date' AND a.vendor_id = '$vendor_id'")->row_array();

										$chk_vendor_state_package = $this->db->query("SELECT a.* FROM tbl_vendor_package_purchase_history AS a INNER JOIN tbl_vendor_package as b ON a.package_id = b.id WHERE b.type = 'State' AND a.end_date > '$today_date' AND a.vendor_id = '$vendor_id'")->row_array();

										if(($chk_vendor_pincode_package && $vendor_pincode == $user_pincode) || ($chk_vendor_district_package && $vendor_city == $user_district) || ($chk_vendor_state_package && $vendor_state == $user_state)){

											$data[$key]['banner'] = base_url($value['banner']);
										}

									}
								}

								if($data){

									$response = array(
										'status' => 1,
										'message' => 'Success',
										'data' => $data
									);
								}
								else{

									$response = array(
										'status' => 0,
										'message' => 'No Banner Found!!',
									);
								}
							}
						}
						echo json_encode($response);
					}



					public function documentCategory()
					{
						$response = array();

						$list = $this->db->get_where('document_category',array('status'=>1))->result_array();

						$data = array();
						if($list)
						{
							foreach ($list as $key => $value) {
								$data[$key]['title'] = $value['title'];
								$data[$key]['id'] = $value['id'];
							}
						}

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data' => $data
						);
						echo json_encode($response);
					}



					public function getDocument()
					{
						$response = array();
						$post = $this->input->post();

						$list = $this->db->get_where('company_document',array('cat_id'=>$post['cat_id']))->result_array();

						$data = array();
						if($list)
						{
							foreach ($list as $key => $value) {
								$data[$key]['image'] = base_url($value['image']);
								$data[$key]['id'] = $value['id'];
							}
						}

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data' => $data
						);
						echo json_encode($response);
					}



					public function getPrivacyContent()
					{
						$response = array();

						$get_content = $this->db->get_where('privacy_content',array('id'=>1))->row_array();

						$content = '';

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'content' => $get_content['content']
						);
						echo json_encode($response);
					}

					public function getWebsiteDisclaimer()
					{
						$response = array();

						$get_content = $this->db->get_where('disclaimer_content',array('id'=>1))->row_array();
						$content = '';

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'content' => $get_content['content']
						);
						echo json_encode($response);
					}


					public function getProductDesign()
					{
						$response = array();

						$get_content = $this->db->get_where('product_design_content',array('id'=>1))->row_array();

						$content = '';

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'content' => $get_content['content']
						);
						echo json_encode($response);
					}

					public function getTermsAndConditions()
					{
						$response = array();

						$get_content = $this->db->get_where('terms_content',array('id'=>1))->row_array();

						$content = '';

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'content' => $get_content['content']
						);
						echo json_encode($response);
					}

					public function getContactContent()
					{
						$response = array();

						$get_content = $this->db->get_where('tbl_site_settings',array('id'=>1))->row_array();

						$address = '';
						$contact = '';
						$mail = '';
						$gst = '';
						$cin = '';

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'address' => $get_content['address'],
							'contact' => $get_content['consumer_no'],
							'mail' => $get_content['email_id'],
							'gst' => $get_content['gst_number'],
							'cin' => $get_content['cin_no']
						);
						echo json_encode($response);
					}

					public function submitContactUsForm(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'Registeration API Post Data - '.json_encode($post));	
						$this->load->library('form_validation');
						$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
						$this->form_validation->set_rules('email', 'Email', 'required|xss_clean|valid_email');
						$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|min_length[10]|max_length[10]|numeric');
						$this->form_validation->set_rules('message', 'Message', 'required|xss_clean');

						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Please Fill All the Details.'
							);
						}
						else
						{		 

							$data=array(
								'name'=>$post['name'],
								'mobile'=>$post['mobile'],
								'email'=>$post['email'],
								'message'=>$post['message'],
								'created'=>date('Y-m-d H:i:s'),
							);


							log_message('debug', 'Front Register Success.');	
							$status = $this->db->insert('enquiry',$data);

							if($status == true)
							{  
								$response = array(
									'status' => 1,
									'message' => 'Congratulations!! Enquiry Sent Successfully.'
								);  

							}
							else
							{
								$response = array(
									'status' => 0,
									'message' => 'Sorry!! Something Went Wrong.'
								);    

							}
						}
						log_message('debug', 'Registeration API Response - '.json_encode($response));	
						echo json_encode($response);

					}


					// public function topupPgAuth(){

					// 	$response = array();
					// 	$post = $this->input->post();


					// 	$this->load->library('form_validation');
					// 	$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
					// 	$this->form_validation->set_rules('order_id', 'Order Id', 'required|xss_clean');
					// 	$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean');
					// 	if ($this->form_validation->run() == FALSE)
					// 	{
					// 		$response = array(
					// 			'status' => 0,
					// 			'message' => 'Sorry!! Please Fill All the Details.'
					// 		);
					// 	}
					// 	else
					// 	{	

					// 		if($post['amount'] > 5000){

					// 			$response = array(
					// 				'status'  => 0,
					// 				'message' => 'Sorry!! you can add maximum 5000.'	
					// 			);
					// 		}
					// 		else{

	    //                         $order_id = $post['order_id'];

					// 			$pgData = array(

					// 				'user_id'  => $post['user_id'],
					// 				'order_id' => $order_id,	
					// 				'amount'   => $post['amount'],
					// 				'status'   => 1,
					// 				'created'  => date('Y-m-d H:i:s') 

					// 			);

					// 			$status =	$this->db->insert('pg_history',$pgData);


					// 			if($status == true)
					// 			{  


					// 				$user_data = $this->db->get_where('users',array('id'=>$post['user_id']))->row_array();

					// 				$data = array(
					// 					"orderId"       => $order_id,
					// 					"orderAmount"   => $post['amount'],
					// 					"orderCurrency" =>"INR",
					// 				);

					// 				$payload = json_encode($data);

					// 				$curl = curl_init();

					// 				curl_setopt_array($curl, array(
					// 					CURLOPT_URL => 'https://api.cashfree.com/api/v2/cftoken/order',
					// 					CURLOPT_RETURNTRANSFER => true,
					// 					CURLOPT_ENCODING => '',
					// 					CURLOPT_MAXREDIRS => 10,
					// 					CURLOPT_TIMEOUT => 0,
					// 					CURLOPT_FOLLOWLOCATION => true,
					// 					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					// 					CURLOPT_CUSTOMREQUEST => 'POST',
					// 					CURLOPT_POSTFIELDS =>  $payload,
					// 					CURLOPT_HTTPHEADER => array(
					// 					    'Content-Type:application/json',
					// 						'x-client-id:'.CASHFREE_APP_ID,
					// 						'x-client-secret:'.CASHFREE_SECRET_KEY,
					// 					),
					// 				));

					// 				$response = curl_exec($curl);



					// 				curl_close($curl);
					// 				//echo $response;

					// 				$responseData = json_decode($response,true);


					// 				if(isset($responseData) && $responseData['status'] == 'OK' && $responseData['message'] == 'Token generated'){

					// 					$cftoken = $responseData['cftoken'];

					// 					$response = array(
					// 						'status' => 1,
					// 						'order_id'=>$order_id,
					// 						'cftoken' =>$cftoken,
					// 						'message' => 'Order Create Successfully'
					// 					);
					// 				}
					// 				else{

					// 					$response = array(

					// 						'status'  => 0,
					// 						'message' => 'Sorry!! something went wrong.'

					// 					);	

					// 				}

					// 			}
					// 			else
					// 			{
					// 				$response = array(
					// 					'status' => 0,
					// 					'message' => 'Sorry!! Something Went Wrong.'
					// 				);    

					// 			}
					// 		}

					// 	}

					// 	echo json_encode($response);

					// }


					public function topupPgAuth(){

						$response = array();
						$post = $this->input->post();
						$get_active_gateway= $this->db->get_where('gateway_setting',array('id'=>1))->row_array();
						$is_active_gateway = isset($get_active_gateway['is_active']) ? $get_active_gateway['is_active'] : '';	
						$pg_type = $is_active_gateway;
						log_message('debug', 'Topup Pg Auth - '.json_encode($post));

						$this->load->library('form_validation');
						if($pg_type == 'cashfree')
						{
						    $this->form_validation->set_rules('order_id', 'Order ID', 'required|xss_clean');
						    $this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean');
						}
						else
						{
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean');
						}
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Please Fill All the Details.'
							);
						}
						else
						{	

							$user_id = $post['user_id'];

							$is_add_fund = 1;
							/*$activeService = $this->User->account_active_service($user_id);
							if(in_array(10, $activeService)){
								
								$is_add_fund = 1;
							}*/

							if($is_add_fund == 0){

								$response = array(
									'status' => 0,
									'message' => 'Sorry!! service is not active.'
								);
							}
							else{

								$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

								$password = isset($userData['password']) ? $userData['password'] : '';

								$header_data = apache_request_headers();

								$token = isset($header_data['Token']) ? $header_data['Token'] : '';
								
								$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
								
								$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

								$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

								if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

									$response = array(
										'status' => 0,
										'message' => 'Session out.Please Login Again.'
									);
								}
								else{

									if($post['amount'] > 2000){

										$response = array(
											'status'  => 0,
											'message' => 'Sorry!! you can add maximum 2000.'	
										);
									}
									else{

								// 		$keyId = RAZOR_KEY_ID;
								// 		$keySecret = RAZOR_KEY_SECRET;

								// 		$amount = $post['amount'];
                                        
                                       

								// 		$request_id = rand(1111,9999).time();
								// 		$api = new Api($keyId, $keySecret);
								// 		$orderData = [
								// 			'receipt'         => $request_id,
								// 			'amount'          => $amount * 100, // 2000 rupees in paise
								// 			'currency'        => 'INR',
								// 			'payment_capture' => 1 // auto capture
								// 		];

								// 		$razorpayOrder = $api->order->create($orderData);
								// 		$order_id = $razorpayOrder['id'];

								//         // get member data
								//         $userData = $this->db->select('name,email,mobile')->get_where('users',array('id'=>$user_id))->row_array();
								        
								//         $client_name = $userData['name'];
								// 		$client_email = $userData['email'];
								// 		$client_mobile = $userData['mobile'];

								// 		$tokenData = array(	            
								//             'user_id' => $user_id,
								//             'request_id' => $request_id,
								//             'amount' => $amount,
								//             'status' => 1,
								//             'created' => date('Y-m-d H:i:s'),
								//             'created_by' => $user_id
								//         );
								//         $this->db->insert('pg_history',$tokenData);

								//         $razorPayData = [
								// 		    "key"               => $keyId,
								// 		    "amount"            => $amount,
								// 		    "name"              => $client_name,
								// 		    "description"       => "Topup Wallet",
								// 		    "image"             => "",
								// 		    "prefill"           => [
								// 		    "name"              => $client_name,
								// 		    "email"             => $client_email,
								// 		    "contact"           => $client_mobile,
								// 		    ],
								// 		    "notes"             => [
								// 		    "address"           => "",
								// 		    "merchant_order_id" => $request_id,
								// 		    ],
								// 		    "theme"             => [
								// 		    "color"             => "#F37254"
								// 		    ],
								// 		    "order_id"          => $order_id,
								// 		];

								// 		$jsondata = json_encode($razorPayData);
                                        
                                        $today_date = date('Y-m-d');
                                        
                                        	$order_id = rand(1111,9999).time();

                                        $get_today_fund_limit = $this->db->select('SUM(amount) as total_amount')->get_where('pg_history',array('user_id'=>$user_id,'DATE(created)'=>$today_date,'status'=>2))->row_array();
                                      	
                                			//check amount limit
                                			$this->db->get_where('pg_history',array('user_id'=>$loggedAccountID))->row_array();
                                
                                			if($get_today_fund_limit['total_amount'] == DAILY_LIMIT_AMOUNT)
                                			{
                                				
                                					$response = array(
										'status' => 0,
										'message' => 'Sorry!! Your Daily Add Money Limit is over.',
										
									    );
									
                                			}
                                			else
                                			{   
                                			    
                                			    if($pg_type == 'cashfree' )
                                			    {
                                			        
                                			        

	                            $order_id = $post['order_id'];

								$pgData = array(

									'user_id'  => $post['user_id'],
									'order_id' => $order_id,	
									'amount'   => $post['amount'],
									'status'   => 1,
									'created'  => date('Y-m-d H:i:s') 

								);

								$status =	$this->db->insert('pg_history',$pgData);


								if($status == true)
								{  


									$user_data = $this->db->get_where('users',array('id'=>$post['user_id']))->row_array();

									$data = array(
										"orderId"       => $order_id,
										"orderAmount"   => $post['amount'],
										"orderCurrency" =>"INR",
									);

									$payload = json_encode($data);

									$curl = curl_init();

									curl_setopt_array($curl, array(
										CURLOPT_URL => 'https://api.cashfree.com/api/v2/cftoken/order',
										CURLOPT_RETURNTRANSFER => true,
										CURLOPT_ENCODING => '',
										CURLOPT_MAXREDIRS => 10,
										CURLOPT_TIMEOUT => 0,
										CURLOPT_FOLLOWLOCATION => true,
										CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										CURLOPT_CUSTOMREQUEST => 'POST',
										CURLOPT_POSTFIELDS =>  $payload,
										CURLOPT_HTTPHEADER => array(
										    'Content-Type:application/json',
											'x-client-id:'.CASHFREE_APP_ID,
											'x-client-secret:'.CASHFREE_SECRET_KEY,
										),
									));

									$response = curl_exec($curl);



									curl_close($curl);
								

									$responseData = json_decode($response,true);


									if(isset($responseData) && $responseData['status'] == 'OK' && $responseData['message'] == 'Token generated'){

										$cftoken = $responseData['cftoken'];

										$response = array(
											'status' => 1,
											'order_id'=>$order_id,
											'cftoken' =>$cftoken,
											'message' => 'Order Create Successfully'
										);
									}
									else{

										$response = array(

											'status'  => 0,
											'message' => 'Sorry!! something went wrong.'

										);	

									}

								}
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry!! Something Went Wrong.'
									);    

								}
							
                                			    }
                                			    
                                			    else
                                			    
                                			    {
                                			        $response = array(
										'status' => 1,
										'order_id'=>$order_id,
										'message' => 'Congratulations ! order create successfully.',
									
									);
                                			    }
                                			    	
                                			}
                                			
                                        
									}

								}
							}
						}

						echo json_encode($response);

					}
                    
                    
                    
                    	public function topupPgAuth_v2(){

						$response = array();
						$post = $this->input->post();	
						$get_active_gateway= $this->db->get_where('gateway_setting',array('id'=>1))->row_array();
						$is_active_gateway = isset($get_active_gateway['is_active']) ? $get_active_gateway['is_active'] : '';	
						$pg_type = $is_active_gateway;
						log_message('debug', 'Topup Pg Auth - '.json_encode($post));
            
						$this->load->library('form_validation');
						if($pg_type == 'cashfree')
						{
						    $this->form_validation->set_rules('order_id', 'Order ID', 'required|xss_clean');
						    $this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean');
						}
						else
						{
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean');
						}
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Please Fill All the Details.'
							);
						}
						else
						{	

							$user_id = $post['user_id'];

							$is_add_fund = 1;
							/*$activeService = $this->User->account_active_service($user_id);
							if(in_array(10, $activeService)){
								
								$is_add_fund = 1;
							}*/

							if($is_add_fund == 0){

								$response = array(
									'status' => 0,
									'message' => 'Sorry!! service is not active.'
								);
							}
							else{

								$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

								$password = isset($userData['password']) ? $userData['password'] : '';

								$header_data = apache_request_headers();

								$token = isset($header_data['Token']) ? $header_data['Token'] : '';
								
								$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
								
								$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

								$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

								// if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								// 	$response = array(
								// 		'status' => 0,
								// 		'message' => 'Session out.Please Login Again.'
								// 	);
								// }
								// else{

									if($post['amount'] > 2000){

										$response = array(
											'status'  => 0,
											'message' => 'Sorry!! you can add maximum 2000.'	
										);
									}
									else{

								// 		$keyId = RAZOR_KEY_ID;
								// 		$keySecret = RAZOR_KEY_SECRET;

								// 		$amount = $post['amount'];
                                        
                                       

								// 		$request_id = rand(1111,9999).time();
								// 		$api = new Api($keyId, $keySecret);
								// 		$orderData = [
								// 			'receipt'         => $request_id,
								// 			'amount'          => $amount * 100, // 2000 rupees in paise
								// 			'currency'        => 'INR',
								// 			'payment_capture' => 1 // auto capture
								// 		];

								// 		$razorpayOrder = $api->order->create($orderData);
								// 		$order_id = $razorpayOrder['id'];

								//         // get member data
								//         $userData = $this->db->select('name,email,mobile')->get_where('users',array('id'=>$user_id))->row_array();
								        
								//         $client_name = $userData['name'];
								// 		$client_email = $userData['email'];
								// 		$client_mobile = $userData['mobile'];

								// 		$tokenData = array(	            
								//             'user_id' => $user_id,
								//             'request_id' => $request_id,
								//             'amount' => $amount,
								//             'status' => 1,
								//             'created' => date('Y-m-d H:i:s'),
								//             'created_by' => $user_id
								//         );
								//         $this->db->insert('pg_history',$tokenData);

								//         $razorPayData = [
								// 		    "key"               => $keyId,
								// 		    "amount"            => $amount,
								// 		    "name"              => $client_name,
								// 		    "description"       => "Topup Wallet",
								// 		    "image"             => "",
								// 		    "prefill"           => [
								// 		    "name"              => $client_name,
								// 		    "email"             => $client_email,
								// 		    "contact"           => $client_mobile,
								// 		    ],
								// 		    "notes"             => [
								// 		    "address"           => "",
								// 		    "merchant_order_id" => $request_id,
								// 		    ],
								// 		    "theme"             => [
								// 		    "color"             => "#F37254"
								// 		    ],
								// 		    "order_id"          => $order_id,
								// 		];

								// 		$jsondata = json_encode($razorPayData);
                                        
                                        $today_date = date('Y-m-d');
                                        
                                        	$order_id = rand(1111,9999).time();

                                        $get_today_fund_limit = $this->db->select('SUM(amount) as total_amount')->get_where('pg_history',array('user_id'=>$user_id,'DATE(created)'=>$today_date,'status'=>2))->row_array();
                                      	
                                			//check amount limit
                                			$this->db->get_where('pg_history',array('user_id'=>$loggedAccountID))->row_array();
                                
                                			if($get_today_fund_limit['total_amount'] == DAILY_LIMIT_AMOUNT)
                                			{
                                				
                                					$response = array(
										'status' => 0,
										'message' => 'Sorry!! Your Daily Add Money Limit is over.',
										
									    );
									
                                			}
                                			else
                                			{   
                                			    
                                			    if($pg_type == 'cashfree' )
                                			    {
                                			        
                                			        

	                            $order_id = $post['order_id'];

								$pgData = array(

									'user_id'  => $post['user_id'],
									'order_id' => $order_id,	
									'amount'   => $post['amount'],
									'status'   => 1,
									'created'  => date('Y-m-d H:i:s') 

								);

								$status =	$this->db->insert('pg_history',$pgData);
                               
                                
								if($status == 1)
								{  


									$user_data = $this->db->get_where('users',array('id'=>$post['user_id']))->row_array();

									$customer_data = array(
									    
									   "customer_id"=> $user_data['user_code'],
                                        "customer_name"=> $user_data['name'],
                                        "customer_email"=> $user_data['email'],
                                        "customer_phone"=>$user_data['mobile']
									    
									    );
									
									$data = array(
										"order_id"       => $order_id,
										"order_amount"   => $post['amount'],
										"order_currency" =>"INR",
										    "customer_details" =>$customer_data
										
									);
                                    
									$payload = json_encode($data);

									$curl = curl_init();
                                    $api_url = 'https://api.cashfree.com/pg/orders';
									curl_setopt_array($curl, array(
										CURLOPT_URL => $api_url,
										CURLOPT_RETURNTRANSFER => true,
										CURLOPT_ENCODING => '',
										CURLOPT_MAXREDIRS => 10,
										CURLOPT_TIMEOUT => 0,
										CURLOPT_FOLLOWLOCATION => true,
										CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										CURLOPT_CUSTOMREQUEST => 'POST',
										CURLOPT_POSTFIELDS =>  $payload,
										CURLOPT_HTTPHEADER => array(
										    'Content-Type:application/json',
											'x-client-id:'.CASHFREE_APP_ID,
											'x-client-secret:'.CASHFREE_SECRET_KEY,
											'x-api-version:2022-09-01',
										),
									));

									$response = curl_exec($curl);
                                    
									curl_close($curl);
							            
							            $apiData = array(
            						'user_id' => $post['user_id'],
						        	'api_url' => $api_url,
						        	'header_data' =>json_encode($request),
						        	'api_response' => json_encode($response),
						        	'created' => date('Y-m-d H:i:s'),						        	
						        	
						        );
						        $this->db->insert('api_response',$apiData);
						        
									$responseData = json_decode($response,true);
                                    
									if(isset($responseData) && $responseData['order_status'] == 'ACTIVE'){

										$cftoken = $responseData['payment_session_id'];
										$cf_order_id = $responseData['cf_order_id'];

										$response = array(
											'status' => 1,
											'order_id'=>$order_id,
											'order_token' =>$cftoken,
											'message' => 'Order Create Successfully'
										);
									}
									else{

										$response = array(

											'status'  => 0,
											'message' => 'Sorry!! something went wrong.'

										);	

									}

								}
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry!! Something Went Wrong.'
									);    

								}
							
                                			    }
                                			    
                                			    else
                                			    
                                			    {
                                			        $response = array(
										'status' => 1,
										'order_id'=>$order_id,
										'message' => 'Congratulations ! order create successfully.',
									
									);
                                			    }
                                			    	
                                			}
                                			
                                        
									}

								//}
							}
						}

						echo json_encode($response);

					}

					public function paymentCallback(){

						$response = array();
						$post = $this->input->post();

						$this->load->library('form_validation');
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean');
						$this->form_validation->set_rules('razorpay_response_id', 'razorpay_response_id', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Please Fill All the Details.'
							);
						}
						else
						{	

							$user_id = $post['user_id'];
							$amount = $post['amount'];
							$razorpay_response_id = $post['razorpay_response_id'];

							$chk_user_credential =$this->db->query("SELECT * FROM tbl_users WHERE id = '$user_id'")->row_array();
							if(!$chk_user_credential)
					            {
									$response = array(
										'status' => 0,
										'message' => 'User Id Not Valid'
									);
					                
					            }
							
							else{

								$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

								$password = isset($userData['password']) ? $userData['password'] : '';

								$header_data = apache_request_headers();

								$token = isset($header_data['Token']) ? $header_data['Token'] : '';
								
								$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
								
								$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

								$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

								if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

									$response = array(
										'status' => 0,
										'message' => 'Session out.Please Login Again.'
									);
								}
								else
					            {	

									$tokenData = array(	            
								            'user_id' => $user_id,
								            'request_id' => $razorpay_response_id,
								            'amount' => $amount,
								            'status' => 2,
								            'created' => date('Y-m-d H:i:s'),
								            'created_by' => $user_id
								        );

								    $this->db->insert('pg_history',$tokenData);


            	$get_member_status = $this->db->select('wallet_balance')->get_where('users',array('id'=>$post['user_id']))->row_array();

	            $before_wallet_balance = isset($get_member_status['wallet_balance']) ? $get_member_status['wallet_balance'] : 0 ;

	            $after_wallet_balance = $before_wallet_balance + $amount;
	            // update member wallet
	            $wallet_data = array(
	                //'account_id' => $account_id,
	                'member_id'           => $post['user_id'],    
	                'before_balance'      => $before_wallet_balance,
	                'amount'              => $amount,  
	                'after_balance'       => $after_wallet_balance,      
	                'status'              => 1,
	                'type'                => 1,      
	                'wallet_type'         => 1,
	                'created'             => date('Y-m-d H:i:s'),      
	                'credited_by'         => 1,
	                'description'         => 'Topup Credited #'.$request_id.' Credited.' 
	            );

	            $this->db->insert('member_wallet',$wallet_data);
	            
	            // update member current wallet balance
	            $this->db->where('id',$post['user_id']);
	            $this->db->update('users',array('wallet_balance'=>$after_wallet_balance));

	            // update request status
	            
	            // $this->db->where('request_id',$request_id);
	            // $this->db->where('user_id',$post['user_id']);
	            // $this->db->update('pg_history',array('status'=>2,'order_id'=>$razorpay_response_id,'updated'=>date('Y-m-d H:i:s')));


	             $response = array(
					'status' => 1,
					'message' => 'Congratulations ! transaction proceeded successfully.',
					
				);

				
				}
			
			}

		}
					log_message('debug', 'Payment Callback API Response - '.json_encode($response));
					echo json_encode($response);
				
		}



					public function PaymentReceiveFreecash(){

						$response = array();
						$post = $this->input->post();

						log_message('debug', 'Gateway callback data - '.json_encode($post));  

						$this->load->library('form_validation');
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('order_id', 'Order ID', 'required|xss_clean');
						$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean');
						$this->form_validation->set_rules('payment_status', 'Payment Status', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Please Fill All the Details.'
							);
						}
						else
						{	

							$user_id = $post['user_id'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$order_id = $post['order_id'];
								$user_id   = $post['user_id'];
								
								log_message('debug', 'Gateway callback start.');  
								$get_post_data = $this->db->get_where('pg_history',array('order_id'=>$order_id,'status'=>1))->row_array();

								log_message('debug', 'Gateway callback order data - '.json_encode($get_post_data));  


								if($get_post_data){

									$user_id = $get_post_data['user_id'];

									$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();


									$curl = curl_init();
									curl_setopt_array($curl, array(
										CURLOPT_URL => 'https://api.cashfree.com/api/v1/order/info/status',
										CURLOPT_RETURNTRANSFER => true,
										CURLOPT_ENCODING => '',
										CURLOPT_MAXREDIRS => 10,
										CURLOPT_TIMEOUT => 0,
										CURLOPT_FOLLOWLOCATION => true,
										CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										CURLOPT_CUSTOMREQUEST => 'POST',
										CURLOPT_POSTFIELDS => array('appId' => ''.CASHFREE_APP_ID.'',
											'secretKey' => ''.CASHFREE_SECRET_KEY.'',
											'orderId' => $order_id),
									));
									$statusResponse = curl_exec($curl);
									curl_close($curl);
									
									$statusResponseData = json_decode($statusResponse,true);

									log_message('debug', 'Gateway callback data - '.json_encode($statusResponse));  

									if($statusResponseData['txStatus'] == "SUCCESS") {

										log_message('debug', 'Gateway callback response success');

										$this->db->where('order_id',$order_id);
										$this->db->update('pg_history',array('status'=>2,'gateway_callback_response'=>json_encode($statusResponseData),'updated'=>date('Y-m-d H:i:s')));

			            				// get post data
										$get_post_data = $this->db->get_where('pg_history',array('order_id'=>$order_id))->row_array();

										$amount = isset($get_post_data['amount']) ? $get_post_data['amount'] : 0;

										$before_wallet_balance = isset($user_data['wallet_balance']) ? $user_data['wallet_balance'] : 0;

										log_message('debug', 'before user wallet balance - '.$before_wallet_balance);

										$after_wallet_balance = $before_wallet_balance + $amount;

										$wallet_data = array(
											'member_id' => $user_id,
											'before_balance' => $before_wallet_balance,
											'amount' => $amount,
											'after_balance' => $after_wallet_balance,
											'status' => 1,
											'type' => 1,
											'wallet_type' => 1,
											'credited_by' => $user_id,
											'description' => 'Wallet added payment gateway orderId#'.$order_id,
											'created' => date('Y-m-d H:i:s')
										);

										$this->db->insert('member_wallet',$wallet_data);

										$this->db->where('id',$user_id);           
										$this->db->update('users',array('wallet_balance'=>$after_wallet_balance));

										log_message('debug', 'after update user wallet balance - '.$after_wallet_balance);

										$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();

										log_message('debug', 'Gateway callback stopped.');



										$msg = 'INR '.$amount.' added succesfully in your wallet.';

										$response = array(
											'status' => 1,
											'message' => $msg
										);  

									}
									elseif($statusResponseData['txStatus'] == "PENDING"){

										log_message('debug', 'Gateway callback response PENDING.');

										$this->db->where('order_id',$order_id);
										$this->db->update('pg_history',array('status'=>1,'gateway_callback_response'=>json_encode($statusResponseData),'updated'=>date('Y-m-d H:i:s')));

										$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();

										log_message('debug', 'Gateway callback stopped.');

										$msg = 'Sorry!! transaction is in pending';

										$response = array(
											'status' => 0,
											'message' => $msg
										);  

									}
									else{

										log_message('debug', 'Gateway callback response failed.');

										$this->db->where('order_id',$order_id);
										$this->db->update('pg_history',array('status'=>3,'gateway_callback_response'=>json_encode($statusResponseData),'updated'=>date('Y-m-d H:i:s')));

										$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();

										log_message('debug', 'Gateway callback stopped.');

										$msg = 'Sorry!! transaction is failed';

										$response = array(
											'status' => 0,
											'message' => $msg
										);  

									}
								}
								else{

									log_message('debug', 'Gateway callback order_id invalid.');

									$this->db->where('order_id',$order_id);
									$this->db->update('pg_history',array('status'=>3,'gateway_callback_response'=>json_encode($statusResponseData),'updated'=>date('Y-m-d H:i:s')));

									$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();

									log_message('debug', 'Gateway callback stopped.');

									$msg = 'Sorry!! transaction is failed';

									$response = array(
										'status' => 0,
										'message' => $msg
									);  

								}

							}
						}

						echo json_encode($response);

					}



					public function getPgHistory(){

						$response = array();

						$post = $this->input->post();
						$user_id = isset($post['user_id']) ? $post['user_id'] : 0;

						$user_id = $post['user_id'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							$fromDate = $post['fromDate'];
							$toDate  =  $post['toDate'];
							$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
							$limit = $page_no * 50;



							if($fromDate && $toDate){

								$count = $this->db->order_by('created','desc')->get_where('pg_history',array('user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

								$limit_start = $limit - 50; 

								$limit_end = $limit;

								$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('pg_history',array('user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();
							}
							else{


								$count = $this->db->get_where('pg_history',array('user_id'=>$user_id))->num_rows();

								$limit_start = $limit - 50; 

								$limit_end = $limit;

								$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('pg_history',array('user_id'=>$user_id))->result_array();
							}

							$pages = ceil($count / 50);

							$data = array();
							if($userList)
							{
								foreach($userList as $key=>$list)
								{	
									$data[$key]['order_id'] = $list['order_id'];
									$data[$key]['amount'] = $list['amount'];
									
									if($list['status'] == 1) {
										$data[$key]['status'] = 'Pending';
									}
									elseif($list['status'] == 2) {

										$data[$key]['status'] = 'Success';

									}
									else{

										$data[$key]['status'] = 'Failed';
									}	

									$data[$key]['datetime'] = date('d-m-Y H:i:s',strtotime($list['created']));

								}

								$response = array(
									'status' => 1,
									'message' => 'Success',
									'data'=>$data,
									'pages' => $pages,
								);
							}
							else{

								$response = array(
									'status' => 0,
									'message' => 'Sorry!! record not found.',
								);
							}
						}
						log_message('debug', 'Get Pg History API Response - '.json_encode($response));	
						echo json_encode($response);

					}	


					public function checkPgOrderStatus(){

						$response = array();
						$post = $this->input->post();

						log_message('debug', 'checkPgOrderStatus API Post Data - '.json_encode($post));

						$this->load->library('form_validation');
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
						$this->form_validation->set_rules('order_id', 'Order ID', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Please Fill All the Details.'
							);
						}
						else
						{	
							$user_id = $post['user_id'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$user_id = $post['user_id'];

								$order_id = $post['order_id'];

								$get_post_data = $this->db->get_where('pg_history',array('user_id'=>$user_id,'order_id'=>$order_id,'status'=>1))->row_array();

								if($get_post_data){

									$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();

									$curl = curl_init();
									curl_setopt_array($curl, array(
										CURLOPT_URL => 'https://api.cashfree.com/api/v1/order/info/status',
										CURLOPT_RETURNTRANSFER => true,
										CURLOPT_ENCODING => '',
										CURLOPT_MAXREDIRS => 10,
										CURLOPT_TIMEOUT => 0,
										CURLOPT_FOLLOWLOCATION => true,
										CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										CURLOPT_CUSTOMREQUEST => 'POST',
										CURLOPT_POSTFIELDS => array('appId' => ''.CASHFREE_APP_ID.'',
											'secretKey' => ''.CASHFREE_SECRET_KEY.'',
											'orderId' => $order_id),
									));
									$statusResponse = curl_exec($curl);
									curl_close($curl);

									$statusResponseData = json_decode($statusResponse,true);


									if($statusResponseData['txStatus'] == "SUCCESS") {

										$this->db->where('order_id',$order_id);
										$this->db->where('user_id',$user_id);
										$this->db->update('pg_history',array('status'=>2,'gateway_callback_response'=>json_encode($statusResponseData),'updated'=>date('Y-m-d H:i:s')));

							            // get post data
										$get_post_data = $this->db->get_where('pg_history',array('user_id'=>$user_id,'order_id'=>$order_id))->row_array();

										$amount = isset($get_post_data['amount']) ? $get_post_data['amount'] : 0;

										$before_wallet_balance = isset($user_data['wallet_balance']) ? $user_data['wallet_balance'] : 0;

										$after_wallet_balance = $before_wallet_balance + $amount;

										$wallet_data = array(
											'member_id' => $user_id,
											'before_balance' => $before_wallet_balance,
											'amount' => $amount,
											'after_balance' => $after_wallet_balance,
											'status' => 1,
											'type' => 1,
											'wallet_type' => 1,
											'credited_by' => $user_id,
											'description' => 'Wallet added payment gateway orderId#'.$order_id,
											'created' => date('Y-m-d H:i:s')
										);

										$this->db->insert('member_wallet',$wallet_data);

										$this->db->where('id',$user_id);           
										$this->db->update('users',array('wallet_balance'=>$after_wallet_balance));

										$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();

										$msg = 'INR '.$amount.' added succesfully in your wallet.';

										$response = array(
											'status'  => 1,
											'message'	=> $msg
										);

									}
									elseif($statusResponseData['txStatus'] == "PENDING"){

										$this->db->where('order_id',$order_id);
										$this->db->where('user_id',$user_id);
										$this->db->update('pg_history',array('status'=>1,'gateway_callback_response'=>json_encode($statusResponseData),'updated'=>date('Y-m-d H:i:s')));

										$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();

										$msg = 'Sorry!! transaction is in pending';

										$response = array(
											'status'  => 0,
											'message'	=> $msg
										);

									}
									else{

										$this->db->where('order_id',$order_id);
										$this->db->where('user_id',$user_id);
										$this->db->update('pg_history',array('status'=>3,'gateway_callback_response'=>json_encode($statusResponseData),'updated'=>date('Y-m-d H:i:s')));

										$user_data = $this->db->get_where('users',array('id'=>$user_id))->row_array();

										$msg = 'Sorry!! transaction is failed';

										$response = array(
											'status'  => 0,
											'message'	=> $msg
										); 

									}

								}
								else{

									$response = array(
										'status'  => 0,
										'message' => 'Sorry!! something went wrong.'
									);
								}

							}
						}

						log_message('debug', 'checkPgOrderStatus API Final Response - '.json_encode($response));

						echo json_encode($response);

					}



					public function newAepsActiveAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'New AEPS Active Auth API Post Data - '.json_encode($post));   
						$this->load->library('form_validation');
						$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
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
						$this->form_validation->set_rules('aadhar_photo', 'Aadhar Photo', 'required|xss_clean');
						$this->form_validation->set_rules('pancard_photo', 'Pancard Photo', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Please enter required field'
							);
						}
						else
						{	
							

								$userID = $post['user_id'];
			        			// check user credential
								$chk_user_credential =$this->db->get_where('users',array('id'=>$userID))->num_rows();
								if(!$chk_user_credential)
								{
									$response = array(
										'status' => 0,
										'message' => 'User Id Not Valid'
									);

								}
								else
								{   
									$is_apes_active = 0;
									$activeService = $this->User->account_active_service($userID);
									if(in_array(3, $activeService)){
										$is_apes_active = 1;
									}


									if(!$is_apes_active){

										$response = array(
											'status' => 0,
											'message' => 'Sorry!! AEPS not active.'
										);

									}
									else{        
										$user_new_aeps_status = $this->User->get_member_new_aeps_status($userID);

										if($user_new_aeps_status)
										{
											$response = array(
												'status' => 0,
												'message' => 'Sorry ! AEPS already actived in your account.'
											);
										}
										else
										{
											$aadhar_photo = '';
											$encodedData = $post['aadhar_photo'];
											if(strpos($post['aadhar_photo'], ' ')){
												$encodedData = str_replace(' ','+', $post['aadhar_photo']);
											}
											$profile = base64_decode($encodedData);
											if($profile)
											{
												$file_name = time().rand(1111,9999).'.jpg';
												$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
												$path = 'media/aeps_kyc_doc/';
												$targetDir = $path.$file_name;
												if(file_put_contents($targetDir, $profile)){
													$aadhar_photo = $targetDir;
												}
											}


											$pancard_photo = '';
											$encodedData = $post['pancard_photo'];
											if(strpos($post['pancard_photo'], ' ')){
												$encodedData = str_replace(' ','+', $post['pancard_photo']);
											}
											$profile = base64_decode($encodedData);
											if($profile)
											{
												$file_name = time().rand(1111,9999).'.jpg';
												$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
												$path = 'media/aeps_kyc_doc/';
												$targetDir = $path.$file_name;
												if(file_put_contents($targetDir, $profile)){
													$pancard_photo = $targetDir;
												}
											}

											$chk_already = $this->db->get_where('new_aeps_member_kyc',array('member_id'=>$userID))->row_array();

											if(!$chk_already){

												$chk_wallet_balance = $this->db->get_where('users',array('id'=>$userID))->row_array();

												$before_wallet_balance = isset($chk_wallet_balance['wallet_balance']) ? $chk_wallet_balance['wallet_balance'] : 0;

												$get_kyc_charge = $this->db->get_where('master_setting',array('id'=>1))->row_array();

												$kyc_charge = isset($get_kyc_charge['fino_kyc_charge']) ? $get_kyc_charge['fino_kyc_charge'] : 0;

												if($kyc_charge > 0){

													$after_balance = $before_wallet_balance - $kyc_charge;
													$wallet_data = array(
														'member_id'           => $userID,    
														'before_balance'      => $before_wallet_balance,
														'amount'              => $kyc_charge,  
														'after_balance'       => $after_balance,      
														'status'              => 1,
														'type'                => 2,   
														'wallet_type'         => 1,   
														'created'             => date('Y-m-d H:i:s'),      
														'description'         => 'Fino Kyc charge deducted.',
														'credited_by'         => $userID
													);

													$this->db->insert('member_wallet',$wallet_data);

													$user_wallet = array(
														'wallet_balance'=>$after_balance,        
													);    
													$this->db->where('id',$userID);
													$this->db->update('users',$user_wallet);
												}
											}

											$api_response = $this->Newaeps_model->activeAEPSMember($post,$aadhar_photo,$pancard_photo,$userID);
											$status = $api_response['status'];

											if($status == 1)
											{   
												$redirecturl = $api_response['redirecturl'];

												$response = array(
													'status' => 1,
													'message' => 'Success',
													'redirecturl' => $redirecturl
												);
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

												$response = array(
													'status' => 0,
													'message' => 'Sorry ! Activation failed due to '.$api_response['msg']
												);
											}

										}
									}

								

							}
						}
						log_message('debug', 'New AEPS Active Auth API Response - '.json_encode($response));    
						echo json_encode($response);

					}


				public function newAepsStatusActive(){

					$response = array();
					$post = $this->input->post();
					log_message('debug', 'newAepsStatusActive API Post Data - '.json_encode($post));  
					$this->load->library('form_validation');
					$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');

					if ($this->form_validation->run() == FALSE)
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry!! Details Not Valid.'
						);
					}
					else
					{

						$userID = $post['userID'];

						$chk_user = $this->db->get_where('users',array('id'=>$userID))->row_array();

						if(!$chk_user){

							$response = array(

								'status'  => 0,
								'message' => 'Sorry!! user not valid.' 

							);
						}
						else{

								$kycData = $this->db->order_by('created','desc')->get_where('new_aeps_member_kyc',array('member_id'=>$userID,'status'=>0))->row_array();
								$merchant_id = $kycData['member_code'];
								$mobile = $kycData['mobile'];


								log_message('debug', 'Onboard check status api called.');

								$datapost = array();
								$datapost['merchantcode'] = $merchant_id;
								$datapost['mobile'] = $mobile;
								$datapost['pipe'] = 'bank2';

								log_message('debug', 'Onboard check status  api post request data - '.json_encode($datapost));
								
								$key =PAYSPRINT_AEPS_KEY;
								$iv=  PAYSPRINT_AEPS_IV;
								
								
								$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
								$body=       base64_encode($cipher);
								$jwt_payload = array(
									'timestamp'=>time(),						
									'partnerId'=>PAYSPRINT_PARTNER_ID,
									'reqid'=>time().rand(1111,9999)
								);
								
								$secret = PAYSPRINT_SECRET_KEY;

								$token = $this->Jwt_model->encode($jwt_payload,$secret);
								
								$header = [
									'Token:'.$token,
									'accept:application/json'
								];
								
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
						
	            			$this->db->where('id',$userID);
							$this->db->update('users',array('new_aeps_status'=>1));

	            			// update aeps status
							$this->db->where('id',$kycData['id']);
							$this->db->update('new_aeps_member_kyc',array('status'=>1,'clear_step'=>2));

							$response = array(

								'status'  => 1,
								'message' => 'Congratulations ! Your merchant onboard is activated.' 

							);   

						}
						elseif(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true && $responseData['is_approved'] == 'Pending'){
								
							$response = array(

								'status'  => 0,
								'message' => 'Onboarding complete,Please wait 6hr for AEPS Activation on FINO Payment Bank activation.' 

							);   

						}
						elseif(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true && $responseData['is_approved'] == 'Rejected'){
							
							$response = array(

								'status'  => 0,
								'message' => 'Congratulations ! Your merchant onboard is activated.' 

							);   

						}
						elseif(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == false && $responseData['is_approved'] == 'Pending'){
							
							$response = array(

								'status'  => 0,
								'message' => 'Onboarding Not Completed.' 

							);   

						}

	            }

						}        

					
					log_message('debug', 'newAepsStatusActive API Response - '.json_encode($response));   
					echo json_encode($response);

				}


					public function newAepsApiAuth()
					{   
		    			//$post = file_get_contents('php://input');
		    			//$post = json_decode($post, true);
						$request = $_REQUEST['user_data'];
						$post =  json_decode($request,true);

						

							log_message('debug', 'New AEPS api Auth API Post Data - '.json_encode($post));  

							$memberID = $post['userID'];
							$loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
							if(!$loggedUser){

								$response = array(
									'status' => 0,
									'message' => 'Sorry ! user not valid.'
								);  
							}
							else{

								$agentID = $loggedUser['user_code'];
								$member_code = $loggedUser['user_code'];
								$is_apes_active = 0;
								$activeService = $this->User->account_active_service($memberID);
								if(in_array(3, $activeService)){
									$is_apes_active = 1;
								}


								if(!$is_apes_active){

									$response = array(
										'status' => 0,
										'message' => 'Sorry!! AEPS not active.'
									);

								}
								else{

									$user_new_aeps_status = $this->User->get_member_new_aeps_status($memberID);
									$response = array();
									if($user_new_aeps_status)
									{
										if($post)
										{
											$serviceType = $post['ServiceType'];
											$deviceIMEI = $post['deviceIMEI'];
											$aadharNumber = $post['AadharNumber'];
											$mobile = $post['mobileNumber'];
											$biometricData = $post['BiometricData'];
											$amount = $post['Amount'];
											$iin = $post['IIN'];
											$txn_pin = $loggedUser['decoded_transaction_password'];

											$requestTime = date('Y-m-d H:i:s');
											if($aadharNumber && $mobile && $biometricData && $iin)
											{
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
													if($amount == 0)
													{

														$accessmodetype = 'APP';

														$key =PAYSPRINT_AEPS_KEY;
														$iv=  PAYSPRINT_AEPS_IV;
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
														$datapost['pipe'] = 'bank1';
														$datapost['timestamp'] = date('Y-m-d H:i:s');
														$datapost['transcationtype'] = $txnType;
														$datapost['submerchantid'] = $agentID;

														$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
														$body=       base64_encode($cipher);
														$jwt_payload = array(
															'timestamp'=>time(),
															'partnerId'=>PAYSPRINT_PARTNER_ID,
															'reqid'=>time().rand(1111,9999)
														);

														$secret = PAYSPRINT_SECRET_KEY;

														$token = $this->Jwt_model->encode($jwt_payload,$secret);

											        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

														$header = [
															'Token:'.$token,
														];



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

														$responseData = json_decode($output,true);

														$apiData = array(
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
															$recordID = $this->Newaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$statementList,$balanceAmount,0,$bankRRN,$memberID);
															$str = array();
															if($is_bal_info == 0)
															{
																$this->Newaeps_model->addStatementCom($txnID,$aadharNumber,$iin,$amount,$memberID);

																if($statementList)
																{
																	$str = array();
																	foreach($statementList as $key => $list)
																	{   
																		$str[$key]['date'] = $list['date'];
																		if($list['txnType'] == 'Dr'){

																			$str[$key]['txnType'] = 'DR';
																		}
																		else{

																			$str[$key]['txnType'] = 'CR';
																		}
																		$str[$key]['amount'] = $list['amount'];
																		$str[$key]['narration'] = $list['narration'];

																	}
																}
															}
															$response = array(
																'status' => 1,
																'message' => $responseData['message'],
																'balanceAmount' => $responseData['balanceamount'],
																'bankRRN' => $responseData['bankrrn'],
																'is_bal_info' => $is_bal_info,
																'is_withdrawal' => $is_withdrawal,
																'invoiceUrl' => '',
																'str' => $str
															);


														}
														else
														{	
															$api_response_data = array();
															$balanceAmount = 0;
															$bankRRN = 0;
															$transactionAmount = 0;

															$this->Newaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3,$api_response_data,$balanceAmount,$bankRRN,$transactionAmount,$memberID);
															$response = array(
																'status' => 0,
																'message' => $responseData['message'],
															);
														}



													}
													else
													{
														$response = array(
															'status' => 0,
															'message' => 'Sorry ! Amount is not valid.'
														);
													}
												}
												elseif($serviceType == 'balwithdraw' || $serviceType == 'aadharpay')
												{
													$txnID = uniqid('CSWD' . rand(11,99));
													$txnType = 'CW';
													$remarks = 'Withdrawal';
													$is_withdrawal = 1;
													$is_bal_info = 0;
													$Servicestype = 'AccountWithdrowal';
													$api_url = PAYSPRINT_AEPS_NEW_WITHDRAWAL_API_URL;
													if($serviceType == 'aadharpay')
													{
														$Servicestype = 'Aadharpay';
														$txnID = uniqid('APAY' . rand(11,99));
														$txnType = 'M';
														$remarks = 'Aadharpay';
														$api_url = PAYSPRINT_AEPS_NEW_AADHARPAY_API_URL;
													}

													if($amount >= 100 && $amount <= 10000)
													{
														$accessmodetype = 'APP';

														$key = PAYSPRINT_AEPS_KEY;
														$iv = PAYSPRINT_AEPS_IV;
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
														$datapost['pipe'] = 'bank1';
														$datapost['timestamp'] = date('Y-m-d H:i:s');
														$datapost['transcationtype'] = $txnType;
														$datapost['amount'] = $amount;
														$datapost['submerchantid'] = $agentID;

														$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
														$body=       base64_encode($cipher);
														$jwt_payload = array(
															'timestamp'=>time(),
															'partnerId'=>PAYSPRINT_PARTNER_ID,
															'reqid'=>time().rand(1111,9999)
														);

														$secret = PAYSPRINT_SECRET_KEY;

														$token = $this->Jwt_model->encode($jwt_payload,$secret);

											        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

														$header = [
															'Token:'.$token,

														];



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

														$responseData = json_decode($output,true);

														$apiData = array(
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

															$balanceAmount = $responseData['balanceamount'];
															$bankRRN = $responseData['bankrrn'];

															$transactionAmount = $responseData['amount'];
															$statementList = array();
															$recordID = $this->Newaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$statementList,$balanceAmount,$bankRRN,$transactionAmount,$memberID);

															$this->Newaeps_model->addBalance($txnID,$aadharNumber,$iin,$amount,$memberID,$com_type);
															$str = array(
																'txnStatus'    => 'Successfull',
																'amount'       => $responseData['amount'],
																'balanceAmount'=> $responseData['balanceamount'],
																'bankRRN'      => $responseData['bankrrn']    
															);


															$response = array(
																'status' => 1,
																'message' => $responseData['message'],
																'balanceAmount' => $responseData['balanceamount'],
																'bankRRN' => $responseData['bankrrn'],
																'is_bal_info' => $is_bal_info,
																'is_withdrawal' => $is_withdrawal,
																'invoiceUrl' => '',
																'str' => $str
															);


														}
														else
														{	
															$api_response_data = array();
															$balanceAmount = 0;
															$bankRRN = 0;
															$transactionAmount = 0;

															$this->Newaeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],3,$api_response_data,$balanceAmount,$bankRRN,$transactionAmount,$memberID);

															$response = array(
																'status' => 0,
																'message' => $responseData['message'],
															);
														}



													}
													else
													{
														$response = array(
															'status' => 0,
															'message' => 'Sorry ! Amount should be less than 10000 and grater than or equal 101.'
														);
													}
												}
												else
												{
													$response = array(
														'status' => 0,
														'message' => 'Something Wrong ! Please Try Again Later.'
													);      
												}
											}
											else
											{
												$response = array(
													'status' => 0,
													'message' => 'Sorry ! Please enter required data.'
												);      
											}

										}
										else
										{
											$response = array(
												'status' => 0,
												'message' => 'Something Wrong ! Please Try Again Later.'
											);
										}
									}
								
							}
						}

						log_message('debug', 'New AEPS api Auth API Response - '.json_encode($response));
						echo json_encode($response);
					}



					public function getNewAepsHistory()
					{
						$post = $this->input->post();
						log_message('debug', 'New AEPS History API Post Data - '.json_encode($post));   
						$userID = isset($post['userID']) ? $post['userID'] : 0;

						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							$response = array();
							$fromDate = isset($post['fromDate']) ? ($post['fromDate']) ? $post['fromDate'] : date('Y-m-d') : date('Y-m-d');
							$toDate = isset($post['toDate']) ? ($post['toDate']) ? $post['toDate'] : date('Y-m-d') : date('Y-m-d');
			   				 // check user valid or not
							$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
							if($chk_user)
							{
								$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_new_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.member_id = '$userID'";
								if($fromDate && $toDate)
								{
									$sql.=" AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."'";
								}
								$sql.=" ORDER BY created DESC";
								$historyList = $this->db->query($sql)->result_array();

								$data = array();
								if($historyList)
								{
									foreach ($historyList as $key => $list) {


										$data[$key]['member_code'] = $list['member_code'];
										$data[$key]['member_name'] = $list['member_name'];
										if($list['service'] == 'balwithdraw' || $list['service'] == 'aadharpay')
										{
											$data[$key]['service'] = 'Account Withdrawal';
										}
										elseif($list['service'] == 'balinfo')
										{
											$data[$key]['service'] = 'Balance Info';
										}
										elseif($list['service'] == 'ministatement')
										{
											$data[$key]['service'] = 'Mini Statement';
										}
										$data[$key]['aadhar_no'] = $list['aadhar_no'];
										$data[$key]['mobile'] = $list['mobile'];
										$data[$key]['amount'] = $list['amount'];
										$data[$key]['txnID'] = $list['txnID'];
										if($list['status'] == 2) {
											$data[$key]['status'] = 'Success';
										}
										elseif($list['status'] == 3) {
											$data[$key]['status'] = 'Failed';
										}
										else{
											$data[$key]['status'] = 'Pending';
										}
										$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));

									}
								}

								if($data)
								{
									$response = array(
										'status' => 1,
										'message' => 'Success',
										'data' => $data
									);  
								}
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry ! No Record Found.',
									);  
								}
							}
							else
							{
								$response = array(
									'status' => 0,
									'message' => 'Sorry ! Member not valid.'
								);
							}
						}
						log_message('debug', 'New AEPS History API Response - '.json_encode($response));    
						echo json_encode($response);
					}



					public function getNewAepsBankList()
					{
		    // get state list
						$bankList = $this->db->get('new_bank_list')->result_array();

						$data = array();
						if($bankList)
						{
							foreach($bankList as $key=>$list)
							{   
								$data[$key]['id'] = $list['id'];
								$data[$key]['iinno'] = $list['iinno'];
								$data[$key]['bank_name'] = $list['bankName'];
							}
						}
						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data'=>$data
						);
						echo json_encode($response);
					} 




					public function getNewAepsPayoutBankList()
					{
				        // get state list
						$bankList = $this->db->get('new_payout_bank_list')->result_array();

						$data = array();
						if($bankList)
						{
							foreach($bankList as $key=>$list)
							{   
								$data[$key]['id'] = $list['id'];
								$data[$key]['bank_name'] = $list['bank_name'];
							}
						}
						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data'=>$data
						);
						echo json_encode($response);
					}


					public function newPayoutBeneAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'newPayoutBeneAuth API Post Data - '.json_encode($post));  
						$this->load->library('form_validation');
						$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
						$this->form_validation->set_rules('account_holder_name', 'account_holder_name', 'required|xss_clean');
						$this->form_validation->set_rules('bank', 'Bank', 'required|xss_clean');
						$this->form_validation->set_rules('account_number', 'account_number', 'required|xss_clean');
						$this->form_validation->set_rules('ifsc', 'ifsc', 'required|xss_clean');

						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Details Not Valid.'
							);
						}
						else
						{

							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$userID = $post['userID'];

								$chk_user = $this->db->get_where('users',array('id'=>$userID))->row_array();

								if(!$chk_user){

									$response = array(

										'status'  => 0,
										'message' => 'Sorry!! user not valid.' 

									);
								}
								else{

									$user_new_aeps_status = $this->User->get_member_new_aeps_status($userID);

									if(!$user_new_aeps_status){

										$response = array(
											'status'  => 0,
											'message' => 'Sorry!! your aeps kyc not completed.' 
										);
									}
									else{

										$datapost = array();
										$datapost['bankid'] = $post['bank'];
										$datapost['merchant_code'] = $chk_user['user_code'];
										$datapost['account'] = $post['account_number'];
										$datapost['ifsc'] = $post['ifsc'];
										$datapost['name'] = $post['account_holder_name'];
										$datapost['account_type'] = 'PRIMARY';

										log_message('debug', 'Add account api call.');
										$key = PAYSPRINT_AEPS_KEY;
										$iv = PAYSPRINT_AEPS_IV;
										
										$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
										$body=       base64_encode($cipher);
										$jwt_payload = array(
											'timestamp'=>time(),
											'partnerId'=>PAYSPRINT_PARTNER_ID,
											'reqid'=>time().rand(1111,9999)
										);
										
										$secret = PAYSPRINT_SECRET_KEY;

										$token = $this->Jwt_model->encode($jwt_payload,$secret);
										
										log_message('debug', 'AEPS Payout Add Account Api Post Data - '.$token);
										
								        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
										
										$header = [
											'Token:'.$token,
											
										];
										
										
										$httpUrl = PAYSPRINT_ADD_BENEFICIARY_URL;

										log_message('debug', 'Add account api url - '.$httpUrl);

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

										$raw_response = curl_exec($curl);
										curl_close($curl);


										log_message('debug', 'Add account api final response - '.$raw_response);

										$responseData = json_decode($raw_response,true);

										$api_data = array(
											'user_id' => $chk_user['id'],
											'api_url' => $httpUrl,
											'post_data' => json_encode($datapost),
											'api_response' => $raw_response,
											'created' => date('Y-m-d H:i:s')  
										);
										$this->db->insert('new_aeps_payout_api_response',$api_data);

										if(isset($responseData) && $responseData['response_code'] == 2 && $responseData['status'] == true){

											$bene_id = $responseData['bene_id'];

											$beneData = array(

												'user_id' => $chk_user['id'],
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

											$response = array(

												'status'  => 2,
												'message' => 'Account Detailed saved successfully. Please upload Supportive Document to activate.',
												'bene_id' => $bene_id,
												'redirect_url' => base_url('VerifyBeneDocument/index/'.$bene_id) 

											);

										}
										elseif(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

											$bene_id = $responseData['bene_id'];

											$beneData = array(

												'user_id' => $chk_user['id'],
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

											$response = array(

												'status'  => 1,
												'message' => 'Account Detailed saved successfully.',
												'bene_id' => $bene_id, 

											);

										}
										else{

											$error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! something went wrong. Please try again.';

											$response = array(

												'status'  => 0,
												'message' => $error,
											);

										}


									}
								}        

							}
						}
						log_message('debug', 'newPayoutBeneAuth API Response - '.json_encode($response));   
						echo json_encode($response);

					}


					public function newPayoutAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'newPayoutAuth API Post Data - '.json_encode($post));  
						$this->load->library('form_validation');
						$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
						$this->form_validation->set_rules('bene_id', 'bene_id', 'required|xss_clean');
						$this->form_validation->set_rules('txn_pass', 'Transaction Password', 'required|xss_clean');
						$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_amountCheck');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Details Not Valid.'
							);
						}
						else
						{	
							$user_id = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$userID = $post['userID'];

								$account_id = $userID;

								$bene_id = $post['bene_id'];

								$chk_user = $this->db->get_where('users',array('id'=>$userID))->row_array();

								if(!$chk_user){

									$response = array(

										'status'  => 0,
										'message' => 'Sorry!! user not valid.' 

									);
								}
								else{

									$user_new_aeps_status = $this->User->get_member_new_aeps_status($userID);

									if(!$user_new_aeps_status){

										$response = array(
											'status'  => 0,
											'message' => 'Sorry!! your aeps kyc not completed.' 
										);
									}
									else{

										$bene_id = $post['bene_id'];

										$benificaryData = $this->db->get_where('new_payout_beneficiary',array('user_id'=>$chk_user['id'],'bene_id'=>$bene_id,'is_verified'=>1))->row_array();

										if(!$benificaryData){

											$response = array(
												'status'  => 0,
												'message' => 'Sorry!! beneficiary not valid.' 
											);
										}
										else{

											$chk_aeps_payout_active = $this->db->get_where('master_setting',array('id'=>1))->row_array();
					                                // get account detail
											$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();
											$wallet_balance = $accountDetail['aeps_wallet_balance'];

											if($accountDetail['transaction_password'] != do_hash($post['txn_pass'])){

												$response = array(
													'status'  => 0,
													'message' => 'Sorry!! transaction password is wrong.',
													'bene_id' => $post['bene_id']
												);
											}
											else{


												$charge_amount = $this->User->get_aeps_bank_transfer_surcharge($post['amount']);

												$total_wallet_deduct = $post['amount'] + $charge_amount;

					                                    // check account balance
												if($wallet_balance < $total_wallet_deduct)
												{
													log_message('debug', 'Fund Transfer Low Balance Error');

													$response = array(
														'status'  => 0,
														'message' => 'Sorry!! Insufficient balance in your wallet.',
														'bene_id' => $post['bene_id']
													);    

												}
												else{

													$transaction_id = rand(111111,999999).time();

													$after_balance = $accountDetail['aeps_wallet_balance'] - $total_wallet_deduct;    

													$wallet_data = array(
														'member_id'           => $account_id,    
														'before_balance'      => $accountDetail['aeps_wallet_balance'],
														'amount'              => $total_wallet_deduct,  
														'after_balance'       => $after_balance,      
														'status'              => 1,
														'type'                => 2,
														'wallet_type'         => 2,      
														'created'             => date('Y-m-d H:i:s'),      
														'description'         => 'Aeps Payout #'.$transaction_id.' Amount Deducted.'
													);

													$this->db->insert('member_wallet',$wallet_data);

													$user_wallet = array(
														'aeps_wallet_balance'=>$after_balance,        
													);    
													$this->db->where('id',$account_id);
													$this->db->update('users',$user_wallet);


													log_message('debug', 'Fund transfer api called.');


													$datapost = array();
													$datapost['bene_id'] = $benificaryData['bene_id'];
													$datapost['amount'] = $post['amount'];
													$datapost['refid']  = $transaction_id;
													$datapost['mode'] = 'IMPS';

													log_message('debug', 'Fund transfer api post request data - '.json_encode($datapost));


													$key = PAYSPRINT_AEPS_KEY;
													$iv = PAYSPRINT_AEPS_IV;

													$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
													$body=       base64_encode($cipher);
													$jwt_payload = array(
														'timestamp'=>time(),
														'partnerId'=>PAYSPRINT_PARTNER_ID,
														'reqid'=>time().rand(1111,9999)
													);

													$secret = PAYSPRINT_SECRET_KEY;

													$token = $this->Jwt_model->encode($jwt_payload,$secret);

													        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

													$header = [
														'Token:'.$token,

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

													$raw_response = curl_exec($curl);
													curl_close($curl);

													log_message('debug', 'Fund transfer api final response - '.$raw_response);

													$responseData = json_decode($raw_response,true);

													$api_data = array(
														'user_id' => $chk_user['id'],
														'api_url' => $httpUrl,
														'post_data' => json_encode($datapost),
														'api_response' => $raw_response,
														'created' => date('Y-m-d H:i:s')  
													);
													$this->db->insert('new_aeps_payout_api_response',$api_data);

													log_message('debug', 'Transfer Fund Final API Response - '.json_encode($raw_response)); 

													$payoutData = array(

														'user_id' => $account_id,
														'bene_id' => $benificaryData['id'],
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

														$this->db->where('user_id',$account_id);
														$this->db->where('id',$transfer_id);
														$this->db->update('new_aeps_payout',array('status'=>2,'ackno'=>$ackno,'updated'=>date('Y-m-d H:i:s')));

														$response = array(
															'status'  => 1,
															'message' => 'Aeps payout successfully.' 
														);

													}
													else{

														$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();

														$after_balance = $accountDetail['aeps_wallet_balance'] + $total_wallet_deduct;    

														$wallet_data = array(
															'member_id'           => $account_id,    
															'before_balance'      => $accountDetail['aeps_wallet_balance'],
															'amount'              => $total_wallet_deduct,  
															'after_balance'       => $after_balance,      
															'status'              => 1,
															'type'                => 1,
															'wallet_type'         => 2,      
															'created'             => date('Y-m-d H:i:s'),      
															'description'         => 'Aeps Payout #'.$transaction_id.' Amount Refund.'
														);

														$this->db->insert('member_wallet',$wallet_data);

														$user_wallet = array(
															'aeps_wallet_balance'=>$after_balance,        
														);    
														$this->db->where('id',$account_id);
														$this->db->update('users',$user_wallet);


														$this->db->where('user_id',$account_id);
														$this->db->where('id',$transfer_id);
														$this->db->update('new_aeps_payout',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));

														$error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! aeps payout failed.';

														$response = array(
															'status'  => 0,
															'message' => $error 
														);

													}
												}

											}

										}
									}
								}        

							}
						}
						log_message('debug', 'newPayoutAuth API Response - '.json_encode($response));   
						echo json_encode($response);

					}



				// 	public function aepsPayoutCheckStatusAuth(){

				// 		$response = array();
				// 		$post = $this->input->post();
				// 		log_message('debug', 'aepsPayoutCheckStatusAuth API Post Data - '.json_encode($post));  
				// 		$this->load->library('form_validation');
				// 		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
				// 		$this->form_validation->set_rules('ref_id', 'ref_id', 'required|xss_clean');
				// 		if ($this->form_validation->run() == FALSE)
				// 		{
				// 			$response = array(
				// 				'status' => 0,
				// 				'message' => 'Sorry!! Details Not Valid.'
				// 			);
				// 		}
				// 		else
				// 		{
				// 			$user_id = $post['userID'];

				// 			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

				// 			$password = isset($userData['password']) ? $userData['password'] : '';

				// 			$header_data = apache_request_headers();

				// 			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
				// 			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
				// 			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

				// 			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

				// 			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

				// 				$response = array(
				// 					'status' => 0,
				// 					'message' => 'Session out.Please Login Again.'
				// 				);
				// 			}
				// 			else{

				// 				$userID = $post['userID'];

				// 				$account_id = $userID;

				// 				$ref_id = $post['ref_id'];

				// 				$chk_user = $this->db->get_where('users',array('id'=>$userID))->row_array();

				// 				if(!$chk_user){

				// 					$response = array(

				// 						'status'  => 0,
				// 						'message' => 'Sorry!! user not valid.' 

				// 					);
				// 				}
				// 				else{



				// 					$user_new_aeps_status = $this->User->get_member_new_aeps_status($userID);

				// 					if(!$user_new_aeps_status){

				// 						$response = array(
				// 							'status'  => 0,
				// 							'message' => 'Sorry!! your aeps kyc not completed.' 
				// 						);
				// 					}
				// 					else{

				// 						$ref_id = $post['ref_id'];

				// 						$chk_ref_id = $this->db->get_where('new_aeps_payout',array('user_id'=>$account_id,'refid'=>$ref_id,'status < '=>3))->row_array();

				// 						if(!$chk_ref_id){

				// 							$response = array(
				// 								'status'  => 0,
				// 								'message' => 'Sorry!! refID not valid.' 
				// 							);
				// 						}
				// 						else{


				// 							log_message('debug', 'Fund transfer check status api called.');

				// 							$datapost = array();
				// 							$datapost['refid'] = $ref_id;
				// 							$datapost['ackno'] = $chk_ref_id['ackno'];

				// 							log_message('debug', 'Fund transfer check status api post request data - '.json_encode($datapost));

				// 							$key = PAYSPRINT_AEPS_KEY;
				// 							$iv = PAYSPRINT_AEPS_IV;

				// 							$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
				// 							$body=       base64_encode($cipher);
				// 							$jwt_payload = array(
				// 								'timestamp'=>time(),
				// 								'partnerId'=>PAYSPRINT_PARTNER_ID,
				// 								'reqid'=>time().rand(1111,9999)
				// 							);

				// 							$secret = PAYSPRINT_SECRET_KEY;

				// 							$token = $this->Jwt_model->encode($jwt_payload,$secret);

				// 							        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

				// 							$header = [
				// 								'Token:'.$token,

				// 							];


				// 							$httpUrl = PAYSPRINT_STATUS_CHECK_URL;
				// 							$curl = curl_init();

				// 							curl_setopt_array($curl, array(
				// 								CURLOPT_URL => $httpUrl,
				// 								CURLOPT_RETURNTRANSFER => true,
				// 								CURLOPT_MAXREDIRS => 10,
				// 								CURLOPT_TIMEOUT => 60,
				// 								CURLOPT_CUSTOMREQUEST => 'POST',
				// 								CURLOPT_POSTFIELDS => $datapost,
				// 								CURLOPT_HTTPHEADER => $header
				// 							));

				// 							$raw_response = curl_exec($curl);
				// 							curl_close($curl);

				// 							log_message('debug', 'Fund transfer check status api final response - '.$raw_response);

				// 							$responseData = json_decode($raw_response,true);

				// 							$api_data = array(
				// 								'user_id' => $chk_user['id'],
				// 								'api_url' => $httpUrl,
				// 								'post_data' => json_encode($datapost),
				// 								'api_response' => $raw_response,
				// 								'created' => date('Y-m-d H:i:s')    
				// 							);
				// 							$this->db->insert('new_aeps_payout_api_response',$api_data);


				// 							if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

				// 								$acno = $responseData['data']['acno'];

				// 								$utr = $responseData['data']['utr'];

				// 								$this->db->where('user_id',$account_id);
				// 								$this->db->where('refid',$ref_id);
				// 								$this->db->update('new_aeps_payout',array('status'=>2,'acno'=>$acno,'utr'=>$utr,'updated'=>date('Y-m-d H:i:s')));

				// 								$response = array(
				// 									'status'  => 1,
				// 									'message' =>'Status checked successfully.'
				// 								);
				// 							}
				// 							else{

				// 								$accountDetail = $this->db->get_where('users',array('id'=>$account_id))->row_array();

				// 								$after_balance = $accountDetail['aeps_wallet_balance'] + $chk_ref_id['total_wallet_deduct'];

				// 								$transaction_id = $refid;    

				// 								$wallet_data = array(
				// 									'member_id'           => $account_id,    
				// 									'before_balance'      => $accountDetail['aeps_wallet_balance'],
				// 									'amount'              => $total_wallet_deduct,  
				// 									'after_balance'       => $after_balance,      
				// 									'status'              => 1,
				// 									'type'                => 1,
				// 									'wallet_type'         => 2,      
				// 									'created'             => date('Y-m-d H:i:s'),      
				// 									'description'         => 'Aeps Payout #'.$transaction_id.' Amount Refund.'
				// 								);

				// 								$this->db->insert('member_wallet',$wallet_data);

				// 								$user_wallet = array(
				// 									'aeps_wallet_balance'=>$after_balance,        
				// 								);    
				// 								$this->db->where('id',$account_id);
				// 								$this->db->update('users',$user_wallet);


				// 								$this->db->where('user_id',$account_id);
				// 								$this->db->where('refid',$ref_id);
				// 								$this->db->update('new_aeps_payout',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));

				// 								$error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! aeps payout failed.';

				// 								$response = array(
				// 									'status'  => 0,
				// 									'message' =>$error
				// 								);


				// 							}


				// 						}
				// 					}
				// 				}        

				// 			}
				// 		}
				// 		log_message('debug', 'aepsPayoutCheckStatusAuth API Response - '.json_encode($response));   
				// 		echo json_encode($response);

				// 	}
                    
                    public function aepsPayoutCheckStatusAuth(){

						$response = array();
						$post = $this->input->post();
						log_message('debug', 'aepsPayoutCheckStatusAuth API Post Data - '.json_encode($post));  
						$this->load->library('form_validation');
						$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
						$this->form_validation->set_rules('ref_id', 'ref_id', 'required|xss_clean');
						if ($this->form_validation->run() == FALSE)
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! Details Not Valid.'
							);
						}
						else
						{
							$userID = $post['userID'];

							$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

							$password = isset($userData['password']) ? $userData['password'] : '';

							$header_data = apache_request_headers();

							$token = isset($header_data['Token']) ? $header_data['Token'] : '';
							
							$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
							$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

							$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

							if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

								$response = array(
									'status' => 0,
									'message' => 'Session out.Please Login Again.'
								);
							}
							else{

								$ref_id = $post['ref_id'];

								$chk_user = $this->db->get_where('users',array('id'=>$userID))->row_array();

								if(!$chk_user){

									$response = array(

										'status'  => 0,
										'message' => 'Sorry!! user not valid.' 

									);
								}
								else{

									$user_new_aeps_status = $this->User->get_member_new_aeps_status($userID);

									if(!$user_new_aeps_status){

										$response = array(
											'status'  => 0,
											'message' => 'Sorry!! your aeps kyc not completed.' 
										);
									}
									else{

										$ref_id = $post['ref_id'];

										$chk_ref_id = $this->db->get_where('new_aeps_payout',array('user_id'=>$userID,'refid'=>$ref_id,'status < '=>3))->row_array();

										if(!$chk_ref_id){

											$response = array(
												'status'  => 0,
												'message' => 'Sorry!! refID not valid.' 
											);
										}
										else{


											log_message('debug', 'Fund transfer check status api called.');

											$datapost = array();
											$datapost['refid'] = $ref_id;
											$datapost['ackno'] = $chk_ref_id['ackno'];

											log_message('debug', 'Fund transfer check status api post request data - '.json_encode($datapost));

											$key = PAYSPRINT_AEPS_KEY;
											$iv = PAYSPRINT_AEPS_IV;

											$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
											$body=       base64_encode($cipher);
											$jwt_payload = array(
												'timestamp'=>time(),
												'partnerId'=>PAYSPRINT_PARTNER_ID,
												'reqid'=>time().rand(1111,9999)
											);

											$secret = PAYSPRINT_SECRET_KEY;

											$token = $this->Jwt_model->encode($jwt_payload,$secret);

											        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

											$header = [
												'Token:'.$token,

											];


											$httpUrl = PAYSPRINT_STATUS_CHECK_URL;
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

											$raw_response = curl_exec($curl);
											curl_close($curl);

											log_message('debug', 'Fund transfer check status api final response - '.$raw_response);

											$responseData = json_decode($raw_response,true);

											$api_data = array(
												'user_id' => $chk_user['id'],
												'api_url' => $httpUrl,
												'post_data' => json_encode($datapost),
												'api_response' => $raw_response,
												'created' => date('Y-m-d H:i:s')    
											);
											$this->db->insert('new_aeps_payout_api_response',$api_data);


											if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

												$api_response_status = strtolower($responseData['data']['status']);
												if($api_response_status == 'processed')
												{
													$acno = $responseData['data']['acno'];

												$utr = $responseData['data']['utr'];

												$this->db->where('user_id',$userID);
												$this->db->where('refid',$ref_id);
												$this->db->update('new_aeps_payout',array('status'=>2,'acno'=>$acno,'utr'=>$utr,'updated'=>date('Y-m-d H:i:s')));

												$response = array(
													'status'  => 1,
													'message' =>'Status checked successfully.'
												);

												}
												elseif($api_response_status == 'refunded')
												{
													$accountDetail = $this->db->get_where('users',array('id'=>$userID))->row_array();

												$after_balance = $accountDetail['aeps_wallet_balance'] + $chk_ref_id['total_wallet_deduct'];

												$transaction_id = $ref_id;    

												$wallet_data = array(
													'member_id'           => $userID,    
													'before_balance'      => $accountDetail['aeps_wallet_balance'],
													'amount'              => $chk_ref_id['total_wallet_deduct'],  
													'after_balance'       => $after_balance,      
													'status'              => 1,
													'type'                => 1,
													'wallet_type'         => 2,      
													'created'             => date('Y-m-d H:i:s'),      
													'description'         => 'Aeps Payout #'.$transaction_id.' Amount Refund.'
												);

												$this->db->insert('member_wallet',$wallet_data);

												$user_wallet = array(
													'aeps_wallet_balance'=>$after_balance,        
												);    
												$this->db->where('id',$userID);
												$this->db->update('users',$user_wallet);


												$this->db->where('user_id',$userID);
												$this->db->where('refid',$ref_id);
												$this->db->update('new_aeps_payout',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));

												$error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! aeps payout failed.';

												$response = array(
													'status'  => 0,
													'message' =>$error
												);


												}
												else
												{
													$response = array(
													'status'  => 0,
													'message' =>'Sorry!! something went wrong.'
												);

												}

											}
											else{

												$response = array(
													'status'  => 0,
													'message' =>'Sorry!! something went wrong.'
												);

											}


										}
									}
								}        

							}
						}
						log_message('debug', 'aepsPayoutCheckStatusAuth API Response - '.json_encode($response));   
						echo json_encode($response);

					}


					public function newPayoutBeneList()
					{
						$post = $this->input->post();
						log_message('debug', 'newPayoutBeneList API Post Data - '.json_encode($post));   
						$userID = isset($post['userID']) ? $post['userID'] : 0;

						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							$response = array();
					        // check user valid or not
							$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
							if($chk_user)
							{
								$historyList = $this->db->query("SELECT a.*,b.bank_name as bank_name FROM tbl_new_payout_beneficiary as a INNER JOIN tbl_new_payout_bank_list as b ON a.bank_id = b.id WHERE a.user_id = '$userID'")->result_array();

								$data = array();
								if($historyList)
								{
									foreach ($historyList as $key => $list) {

										$data[$key]['id'] = $list['id'];
										$data[$key]['bene_id'] = $list['bene_id'];
										$data[$key]['account_holder_name'] = $list['account_holder_name'];
										$data[$key]['account_number'] = $list['account_number'];
										$data[$key]['ifsc'] = $list['ifsc'];
										$data[$key]['bank_name'] = $list['bank_name'];
										$data[$key]['is_verified'] = $list['is_verified'] == 1 ? 'Yes' : 'No';
										$data[$key]['verify_url'] = $list['is_verified'] == 1 ? '' : base_url('VerifyBeneDocument/index/'.$list['bene_id']);
										$data[$key]['date'] = $list['created'];
									}
								}

								if($data)
								{
									$response = array(
										'status' => 1,
										'message' => 'Success',
										'data' => $data
									);  
								}
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry ! No Record Found.',
									);  
								}
							}
							else
							{
								$response = array(
									'status' => 0,
									'message' => 'Sorry ! Member not valid.'
								);
							}
						}
						log_message('debug', 'newPayoutBeneList API Response - '.json_encode($response));    
						echo json_encode($response);
					}




					public function newPayoutList()
					{
						$post = $this->input->post();
						log_message('debug', 'newPayoutList API Post Data - '.json_encode($post));   
						$userID = isset($post['userID']) ? $post['userID'] : 0;

						$user_id = $post['userID'];

						$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

						$password = isset($userData['password']) ? $userData['password'] : '';

						$header_data = apache_request_headers();

						$token = isset($header_data['Token']) ? $header_data['Token'] : '';
						
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
						
						$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

						if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

							$response = array(
								'status' => 0,
								'message' => 'Session out.Please Login Again.'
							);
						}
						else{

							$response = array();
					        // check user valid or not
							$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
							if($chk_user)
							{
								$historyList = $this->db->query("SELECT a.*,b.account_holder_name,b.account_number FROM tbl_new_aeps_payout as a INNER JOIN tbl_new_payout_beneficiary as b ON a.bene_id = b.id WHERE a.user_id = '$userID'")->result_array();

								$data = array();
								if($historyList)
								{
									foreach ($historyList as $key => $list) {

										$data[$key]['beneficiary'] = $list['account_holder_name'].'('.$list['account_number'].')';
										$data[$key]['transfer_amount'] = $list['transfer_amount'];
										$data[$key]['account_holder_name'] = $list['account_holder_name'];
										$data[$key]['total_wallet_deduct'] = $list['total_wallet_deduct'];
										$data[$key]['refid'] = $list['refid'];
										$data[$key]['utr'] = isset($list['utr']) ? $list['utr'] : 'Not Available';
										if($list['status'] == 1){

											$data[$key]['status'] = 'Pending';  
										}
										elseif($list['status'] == 2){

											$data[$key]['status'] = 'Success';
										}
										else{

											$data[$key]['status'] = 'Failed';
										}    

										$data[$key]['date'] = $list['created'];
									}
								}

								if($data)
								{
									$response = array(
										'status' => 1,
										'message' => 'Success',
										'data' => $data
									);  
								}
								else
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry ! No Record Found.',
									);  
								}
							}
							else
							{
								$response = array(
									'status' => 0,
									'message' => 'Sorry ! Member not valid.'
								);
							}
						}
						log_message('debug', 'newPayoutList API Response - '.json_encode($response));    
						echo json_encode($response);
					}



					public function getOttPlan(){

						$response = array(); 

						$post = $this->input->post();

						$operator_id = isset($post['operator_id']) ? $post['operator_id'] : 0;

						if(!$operator_id){

							$response = array(
								'status' => 0,
								'message' => 'Please pass operatorID'
							);
						}
						else{

							$key = PAYSPRINT_AEPS_KEY;
							$iv = PAYSPRINT_AEPS_IV;

							$jwt_payload = array(
								'timestamp'=>time(),
								'partnerId'=>PAYSPRINT_PARTNER_ID,
								'reqid'=>time().rand(1111,9999)
							);

							$secret = PAYSPRINT_SECRET_KEY;

							$token = $this->Jwt_model->encode($jwt_payload,$secret);

					                                    //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

							$header = [
								'Token:'.$token,
								'Content-Type:application/json'
							];


							$body = '{"operatorid":'.$operator_id.'}';

							$httpUrl = PAYSPRINT_OTT_PLAN_API_URL;

							$curl = curl_init();

							curl_setopt_array($curl, array(
								CURLOPT_URL => $httpUrl,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 60,
								CURLOPT_CUSTOMREQUEST => 'POST',
								CURLOPT_POSTFIELDS => $body,
								CURLOPT_HTTPHEADER => $header
							));

							$output = curl_exec($curl);
							curl_close($curl);

							$responseData = json_decode($output,true);

							if(isset($responseData) && $responseData['responsecode'] == 1 && $responseData['status'] == 'true' && !empty($responseData['data'])){

								$planList = $responseData['data'];
								$data = array();
								foreach($planList as $key => $pList){

									$data[$key]['planid'] = $pList['planid'];	
									$data[$key]['operator_name'] = $this->User->get_ott_operator_name($pList['operatorid']);
									$data[$key]['plan'] = $pList['plan'];
									$data[$key]['duration'] = $pList['duration'];
									$data[$key]['amount'] = $pList['amount'];	
								}

								$response = array(
									'status' => 1,
									'message'=> $responseData['message'],
									'planrefid' => $responseData['planrefid'],
									'data' => $data 
								);
							}
							else{

								$response = array(
									'status' => 0,
									'message'=> $responseData['message'] 
								);
							}
						}

						echo json_encode($response);
					}


					public function licStatusCheck(){

						log_message('debug', 'Lic status check api call');

						$key = 'f611a2b612e22289';
						$iv = 'f9bf511478f4d710';

						$jwt_payload = array(
							'timestamp'=>time(),
							'partnerId'=>'PS00797',
							'reqid'=>time().rand(1111,9999)
						);

						$secret = 'UFMwMDc5N2JhMzQ3ZTYwN2E2NmJmYzkxZDI1Zjc4NDM1OGYyYjA5';

						$token = $this->Jwt_model->encode($jwt_payload,$secret);

							        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

						$header = [
							'Token:'.$token,
							'Authorisedkey:YmVjMjhkYzUzY2YwMTU0MGJhY2Y4MzQ1ZWFmYzNhY2Y=',
							'Content-Type:application/json'
						];

						$body = '{"referenceid":"2021052415"}';


						$httpUrl = 'https://paysprint.in/service-api/api/v1/service/bill-payment/bill/licstatus';

						log_message('debug', 'Lic status check api url - '.$httpUrl);

						log_message('debug', 'Lic status check api header data - '.json_encode($header));

						log_message('debug', 'Lic status check api body data - '.$body);

						$curl = curl_init();

						curl_setopt_array($curl, array(
							CURLOPT_URL => $httpUrl,
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 60,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS => $body,
							CURLOPT_HTTPHEADER => $header
						));

						$output = curl_exec($curl);
						curl_close($curl);

						log_message('debug', 'Lic status check api response data - '.$output);

						echo $output;
						die;

				    	/*

						Success Response

						{"responsecode":1,"status":true,"data":{"txnid":"35120","operatorname":"Life Insurance Corporation","canumber":"554984552","amount":"100","ad1":"nitesh@rnfiservices.com","ad2":"HDC610532","ad3":"HDC416601","comm":"0.00","tds":"0.00","status":"1","refid":"2021052415","operatorid":"dsfsdfdsdfs","dateadded":"2022-08-23 12:30:36","refunded":"0","refundtxnid":"","daterefunded":null},"message":"Transaction Enquiry Successful"}

				    	*/

						$responseData = json_decode($output,true);

					}



					public function ottStatusCheck(){

						log_message('debug', 'Ott subscription status check api call');

						$key = 'f611a2b612e22289';
						$iv = 'f9bf511478f4d710';

						$jwt_payload = array(
							'timestamp'=>time(),
							'partnerId'=>'PS00797',
							'reqid'=>time().rand(1111,9999)
						);

						$secret = 'UFMwMDc5N2JhMzQ3ZTYwN2E2NmJmYzkxZDI1Zjc4NDM1OGYyYjA5';

						$token = $this->Jwt_model->encode($jwt_payload,$secret);

							        //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

						$header = [
							'Token:'.$token,
							'Authorisedkey:YmVjMjhkYzUzY2YwMTU0MGJhY2Y4MzQ1ZWFmYzNhY2Y=',
							'Content-Type:application/json'
						];

						$body = '{"referenceid":"30901661168991"}';


						$httpUrl = 'https://paysprint.in/service-api/api/v1/service/ott/ott/status';

						log_message('debug', 'Ott subscription status check api url - '.$httpUrl);

						log_message('debug', 'Ott subscription status check api header data - '.json_encode($header));

						log_message('debug', 'Ott subscription status check api body data - '.$body);

						$curl = curl_init();

						curl_setopt_array($curl, array(
							CURLOPT_URL => $httpUrl,
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 60,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS => $body,
							CURLOPT_HTTPHEADER => $header
						));

						$output = curl_exec($curl);
						curl_close($curl);

						log_message('debug', 'Ott subscription status check api response data - '.$output);

						echo $output;
						die;

				    	/*

						Success Response

						{"status":true,"response_code":1,"ackno":34798,"refid":"6075631660991154","amount":99,"message":"Subscription for zee five of Amount 99 is Success"}

				    	*/

						$responseData = json_decode($output,true);

					}


					public function getFranchiseStateList()
					{
						$response = array();
						$operator = $this->db->get('franchise_state')->result_array();
						
						$data = array();
						if($operator)
						{
							foreach ($operator as $key => $value) {

								$data[$key]['state_id'] = $value['state_id'];
								$data[$key]['state_title'] = $value['state_title'];
								
							}
						}

						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data' => $data
						);
						echo json_encode($response);
					}



					public function getFranchiseDistrictList()
					{	
						$response = array();
						$post = $this->input->post();

						$state_id = isset($post['state_id']) ? $post['state_id'] : 0;

						if($state_id){

							$operator = $this->db->get_where('franchise_district',array('state_id'=>$state_id))->result_array();
							
							$data = array();
							if($operator)
							{
								foreach ($operator as $key => $value) {
									
									$data[$key]['districtid'] = $value['districtid'];
									$data[$key]['district_title'] = $value['district_title'];
									
								}
							}

							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data
							);
						}
						else{

							$response = array(
								'status' => 0,
								'message' => 'Please select state',
							);
						}
						echo json_encode($response);
					}



					public function getFranchiseBlockList()
					{	
						$response = array();
						$post = $this->input->post();

						$district_id = isset($post['district_id']) ? $post['district_id'] : 0;

						if($district_id){

							$operator = $this->db->get_where('franchise_city',array('districtid'=>$district_id))->result_array();
							
							$data = array();
							if($operator)
							{
								foreach ($operator as $key => $value) {
									
									$data[$key]['block_id'] = $value['id'];
									$data[$key]['name'] = $value['name'];
									
								}
							}

							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data
							);
						}
						else{

							$response = array(
								'status' => 0,
								'message' => 'Please select district',
							);
						}
						echo json_encode($response);
					}



					public function getDemoLink()
					{	
						$response = array();
						$post = $this->input->post();
						
							$service_id = isset($post['service_id']) ? $post['service_id'] : '';

						if($service_id){

							$get_demo_link = $this->db->get_where('service',array('id'=>$service_id))->row_array();
							
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'service' => isset($get_demo_link['title']) ? $get_demo_link['title'] : '',
								'demo_link' => isset($get_demo_link['demo_link']) ? $get_demo_link['demo_link'] : ''
							);
						}
						else{

							$response = array(
								'status' => 0,
								'message' => 'Please enter service id.',
							);
						}
						echo json_encode($response);
					}
					
					
					
					public function searchDownlineUser(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'searchDownlineUser API Post Data - '.json_encode($post));	
				$this->load->library('form_validation');

				$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
				$this->form_validation->set_rules('user_code', 'userCode', 'required|xss_clean');

				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => 'Please Enter Valid Detail.'
					);
				}
				else
				{	

					$user_code = trim($post['user_code']);

					$userID = $post['userID'];

					$get_member_id = $this->db->get_where('users',array('user_code'=>$user_code))->row_array();

					if(!$get_member_id){

						$response = array(
							'status' => 0,
							'message' => 'User Code Invalid.'
						);
					}
					else{

						$member_id = isset($get_member_id['id']) ? $get_member_id['id'] : '';


						// get logged user direct downline str
						$getDirectDownlineStr = $this->db->get_where('member_tree',array('member_id'=>$member_id))->row_array();
						$direct_downline_str = isset($getDirectDownlineStr['direct_downline_str']) ? explode(',', $getDirectDownlineStr['direct_downline_str']) : array();
						if($direct_downline_str && in_array($userID, $direct_downline_str))
						{
							$get_downline_user_data = $this->db->query("SELECT a.*,b.user_code,b.name,b.mobile,c.package_name,e.name as sponser_name,e.user_code as sponser_code FROM tbl_member_tree as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_package as c ON c.id = b.current_package_id LEFT JOIN tbl_users as e ON e.id = a.reffrel_id WHERE a.member_id = '$member_id'")->row_array();
							
							if($get_downline_user_data){

								$response = array(

									'status'  => 1,
									'message' => 'Data fetched successfully.',
									'user_code' => $get_downline_user_data['user_code'],
									'name' => $get_downline_user_data['name'],
									'mobile' => $get_downline_user_data['mobile'],
									'package_name' => $get_downline_user_data['package_name'],
									'sponser_code' => $get_downline_user_data['sponser_code'],
									'sponser_name' => $get_downline_user_data['sponser_name'],
									'total_downline' => $this->User->get_member_direct_downline_count($get_downline_user_data['member_id'])
								);

							}
							else{

								$response = array(
									'status'  => 0,
									'message' => "Sorry!! user is not in your downline",
									'direct_downline_str' => $direct_downline_str
								);
							}
						}
						else{

							$response = array(
								'status'  => 0,
								'message' => "Sorry!! user is not in your downline"
							);
						}
						
						
					
					}
					
				}
				
				log_message('debug', 'Login Auth API Response - '.json_encode($response));	
				echo json_encode($response);

			}


					public function licBillFetch(){

						$response = array();

						$post = $this->input->get();

						if(empty($post['canumber'])){

							$response = array(

								'status'  => false,
								'response_code' => 8,
								'message' => 'The CA Number field is required'
							);
						}
						else{

							$key = PAYSPRINT_AEPS_KEY;
							$iv = PAYSPRINT_AEPS_IV;

							$jwt_payload = array(
								'timestamp'=>time(),
								'partnerId'=>PAYSPRINT_PARTNER_ID,
								'reqid'=>time().rand(1111,9999)
							);

							$secret = PAYSPRINT_SECRET_KEY;

							$token = $this->Jwt_model->encode($jwt_payload,$secret);

							$header = [
								'Token:'.$token,
								'Content-Type:application/json'
							];

							$params = isset($post_data['params']) ? $post_data['params'] : array();

							$canumber = $post['canumber'];

							//$email = $params[1];

							$body = '{"canumber":'.$canumber.',"ad1":"","mode":"offline"}';

							$httpUrl = PAYSPRINT_LIC_BILL_FETCH_API_URL;
							
							$curl = curl_init();

							curl_setopt_array($curl, array(
								CURLOPT_URL => $httpUrl,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 60,
								CURLOPT_CUSTOMREQUEST => 'POST',
								CURLOPT_POSTFIELDS => $body,
								CURLOPT_HTTPHEADER => $header
							));

							$outputt = curl_exec($curl);
							curl_close($curl);

							// save api response 
							$api_data = array(
								'user_id' => 00,
								'api_response' => $outputt,
								'api_url' => $httpUrl,
								'api_post_data' => $body,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('bbps_api_response',$api_data);

							$responsee = json_decode($outputt,true);

							$response = $responsee;
						}

						echo json_encode($response);

					}


					public function testDecryptToken(){

						$token = 'K3lYTnJZS2s2OE80RnhhMlpXRGwwdHJLMDBSbUY2N1dtM1VuRzMyODI1RHR2dVNOWGNrMEFUZ0RuZDNwTmdSaDFwYVhmNUdNdEhCeVZCcWhVUFBZeHc9PQ==';

						echo $decryptToken = $this->User->generateAppToken('decrypt',$token);

						die;

					}
					
					public function checkFinoPipeStatus(){

				$response = array();
				$post = $this->input->post();
				log_message('debug', 'Fino Pipe Check  Status API Post Data - '.json_encode($post));	
				$this->load->library('form_validation');

				$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
				
				if ($this->form_validation->run() == FALSE)
				{
					$response = array(
						'status' => 0,
						'message' => 'Please Enter User ID.'
					);
				}
				else
				{

					$memberID = $post['user_id'];

					$get_kyc_exists = $this->db->order_by('created','desc')->get_where('new_aeps_member_kyc',array('member_id'=>$memberID,'status'=>0,'clear_step'=>1))->num_rows();
		       		
		       		if($get_kyc_exists > 0)
		       		{

		       			$get_kyc_exists = $this->db->order_by('created','desc')->get_where('new_aeps_member_kyc',array('member_id'=>$memberID,'status'=>0,'clear_step'=>1))->row_array();

		       			//call onboard status pipe wise
        			log_message('debug', 'Onboard check status api called.');

					$datapost = array();
					$datapost['merchantcode'] = $get_kyc_exists['member_code'];
					$datapost['mobile'] = $get_kyc_exists['mobile'];
					$datapost['pipe'] = 'bank2';

					log_message('debug', 'Onboard check status  api post request data - '.json_encode($datapost));
					
					$key =PAYSPRINT_AEPS_KEY;
					$iv=  PAYSPRINT_AEPS_IV;
					
					
					$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
					$body=       base64_encode($cipher);
					$jwt_payload = array(
						'timestamp'=>time(),						
						'partnerId'=>PAYSPRINT_PARTNER_ID,
						'reqid'=>time().rand(1111,9999)
					);
					
					$secret = PAYSPRINT_SECRET_KEY;

					$token = $this->Jwt_model->encode($jwt_payload,$secret);
					
					$header = [
						'Token:'.$token,
						'accept:application/json'
					];
					
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
	        	$this->db->where('id',$memberID);
	        	$this->db->update('users',array('new_aeps_status'=>1));

	        	// update aeps status
	            $this->db->where('id',$recordID);
	            $this->db->update('new_aeps_member_kyc',array('status'=>1,'clear_step'=>2));

	            $response = array(
	            	'status' =>1,
	            	'is_approved' =>1,
	            	'msg' =>'Congratulations ! Your merchant onboard is activated.'

	            );

	            }
	            elseif (isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true && $responseData['is_approved'] == 'Pending') {

	            	
	            	 $response = array(
	            	'status' =>1,
	            	'is_approved' => 2,
	            	'msg' =>'Onboarding complete,Please wait 6hr for AEPS Activation on FINO Payment Bank activation.'
					);
	            

	            	
	            }
	            elseif(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true && $responseData['is_approved'] == 'Rejected')

	            {
	            	

	            	 $response = array(
	            	'status' =>1,
	            	'is_approved' => 3,
	            	'msg' =>'Onboarding Rejected by bank.'
					);

	            }
	            elseif (isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == false && $responseData['is_approved'] == 'Pending') {
	            	
	            	 
	            	 $response = array(
	            	'status' =>1,
	            	'is_approved' => 4,
	            	'msg' =>'Onboarding Not Completed.'
					);

	            }
	            else
	            {
	            	$response =array(
	            		'status' =>0,
	            		'msg' => 'Something Went Wrong'

	            	);
	            }
		       		}

		       		else

		       		{
		       			$response =array(
	            		'status' =>0,
	            		'msg' => 'Something Went Wrong'

	            	);

		       		}

					log_message('debug', 'Fino Pipe Check Status API Response - '.json_encode($response));	
					echo json_encode($response);

				}

			}
			
			
			
			  public function nsdlPanAuth()
	{
		

		
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'NSDL Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('title', 'Title', 'required|xss_clean');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('mode', 'Mode', 'required|xss_clean');
        $this->form_validation->set_rules('gender', 'Gender', 'required|xss_clean');


        if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}


		else

		{

			$memberID = $post['user_id'];


			$txn_pass = isset($post['txn_pass']) ? $post['txn_pass'] : '';

			$userData = $this->db->get_where('users',array('id'=>$memberID,'is_active'=>1))->row_array();

			$password = isset($userData['password']) ? $userData['password'] : '';

			$header_data = apache_request_headers();
				
			log_message('debug', 'NSDL Auht Header API Response Post Data - '.json_encode($header_data));

				$token = isset($header_data['Token']) ? $header_data['Token'] : '';

				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$memberID,$password,$Deviceid);

					if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.'
					);
				}

				else

				{
					$activeService = $this->User->account_active_service($memberID);
		            
		            if(!in_array(19, $activeService)){
		               $response = array(
								'status' => 0,
								'message' => 'Sorry ! NSDL PAN Service Not Active.'
							);
		            }

		             $get_com_data = $this->db->get_where('master_setting',array('id'=>1))->row_array();
             
			          	$charge = 0;
			          	if($post['mode'] == 'P')
			          	{
			          		$charge = isset($get_com_data['nsdl_charge']) ? $get_com_data['nsdl_charge'] : 0 ;	
			          	}
			          	elseif($post['mode'] == 'E')
			          	{
			          		$charge = isset($get_com_data['nsdl_e_pan_charge']) ? $get_com_data['nsdl_e_pan_charge'] : 0 ;
			          	}
         				
            $chk_wallet_balance =$this->db->get_where('users',array('id'=>$memberID))->row_array();

            $user_before_balance = $chk_wallet_balance['wallet_balance'];
            if($chk_wallet_balance['wallet_balance'] < $charge){
                
                $response = array(
						'status' => 0,
						'message' => 'Sorry ! Insufficient Wallet Balance.'
					);
                   

            }


            else


            {
            	$transaction_id = time().rand(1111,9999);


            $pan_data = array(
            'member_id' =>$memberID,
            'first_name' =>$post['first_name'],
            'last_name' =>$post['last_name'],
            'middle_name'=>$post['middle_name'],
            'mode' =>$post['mode'],
            'gender' =>$post['gender'],
            'email_id'=>$post['email'],    
            'charge_amount' =>$charge,        
            'transaction_id'=>$transaction_id,
            'created'=>date('Y-m-d H:i:s'),
            'created_by'=>$memberID
          );

         $this->db->insert('member_nsdl_transcation',$pan_data);

         $pan_redirect_url = 'https://www.sonikapay.com/cron/returnVerify';
        
        
        
        $gender = '';
        if($post['gender'] == '1')
        {
            $gender = 'male';
        }
        else
        {
            $gender = 'female';
        }
        
         $key = PAYSPRINT_AEPS_KEY;
         $iv = PAYSPRINT_AEPS_IV;
        $datapost =array();

                $datapost['refid'] = $transaction_id;
                $datapost['title'] = $post['title'];
                $datapost['firstname'] = $post['first_name'];
                $datapost['lastname'] = $post['last_name'];
                $datapost['middlename'] = $post['middle_name'];
                $datapost['mode'] = $post['mode'];
                $datapost['gender'] =$gender;
                $datapost['email'] =$post['email'];               
                $datapost['redirect_url'] = 'https://www.sonikapay.com/cron/returnVerify';

        $cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
        $body=       base64_encode($cipher);
        $reqid = time().rand(1111,9999);

        log_message('debug', 'NSDL Api RequestID - '.$reqid);     

        $jwt_payload = array(
            'timestamp'=>time(),
            'partnerId'=>PAYSPRINT_PARTNER_ID,
            'reqid'=>$reqid
        );

        log_message('debug', 'NSDL Auth Api jwt payload - '.json_encode($jwt_payload));

        $secret = PAYSPRINT_SECRET_KEY;

        $token = $this->Jwt_model->encode($jwt_payload,$secret);

            //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

        $header = [
            'Token:'.$token,
           // 'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY            
        ];

         log_message('debug', 'NSDL  Api Header Data - '.json_encode($header));



         log_message('debug', 'NSDL  Api Body  Data - '.json_encode($datapost));



        $httpUrl = PAYSPRINT_NSDL_URL;
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
        

        log_message('debug', 'NSDL  Api Response  Data - '.json_encode($output));
      

        $responseData = json_decode($output,true);

        $apiData = array(            
            'user_id' => $memberID,
            'api_url' => $httpUrl,
            'api_response' => $output,
            'redirect_url'=>$responseData['data']['url'],
            'encode' =>$responseData['data']['encdata'],
            'post_data' => json_encode($datapost),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('nsdl_api_response',$apiData);

        $record_id = $this->db->insert_id();
        if(isset($responseData['response_code']) && $responseData['response_code'] == 1)
        {

            $before_balance = $this->db->get_where('users',array('id'=>$memberID))->row_array();
            $after_balance = $before_balance['wallet_balance'] - $charge;    

                    $wallet_data = array(                       
                        'member_id'           => $memberID,    
                        'before_balance'      => $before_balance['wallet_balance'],
                        'amount'              => $charge,  
                        'after_balance'       => $after_balance,      
                        'status'              => 1,
                        'type'                => 2,      
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'Nsdl Pan Card #'.$transaction_id.' Amount Debited.'
                    );

                    $this->db->insert('member_wallet',$wallet_data);

                    $user_wallet = array(
                        'wallet_balance'=>$after_balance,        
                    );    
                    $this->db->where('id',$memberID);
                    $this->db->update('users',$user_wallet);
         			
       				$response = array(
					'status' => 1,
					'message' => 'Pan Form Submitted Successfully .',
					'redirect_url' => base_url('PanRedirect/index/'.$record_id) 
					
						);

            }

            else

            {
            	$response = array(
					'status' => 0,
					'message' => $responseData['message'],
					
						);

            }
		}

				}

	    }

	     log_message('debug', 'Nsdl Auth API Response - '.json_encode($response));	
		echo json_encode($response);


	}
			
    
				public function getNsdlHistory()
		{
			$post = $this->input->post();
			log_message('debug', 'NSDL History API Get Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;

			$user_id = $post['userID'];

			$userData = $this->db->get_where('users',array('id'=>$user_id,'is_active'=>1))->row_array();

			$password = isset($userData['password']) ? $userData['password'] : '';

			$header_data = apache_request_headers();

			$token = isset($header_data['Token']) ? $header_data['Token'] : '';
			
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
			
			$decryptToken = $this->User->generateAppToken('decrypt',$token); 	

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$user_id,$password,$Deviceid);

			if($chk_user_token['status'] == 0 || $chk_user_token['is_login'] == 0){

				$response = array(
					'status' => 0,
					'message' => 'Session out.Please Login Again.'
				);
			}
			else{

				$response = array();
				$fromDate = $post['fromDate'];
				$toDate =   $post['toDate'];
				$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
				$limit = $page_no * 50;

			    // check user valid or not
				$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
				if($chk_user)
				{
					$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_nsdl_transcation as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.member_id = '$userID'";
					if($fromDate && $toDate)
					{
						$sql.=" AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."'";

						$sql.=" ORDER BY a.created DESC";

						$count = $this->db->query($sql)->num_rows();

						$limit_start = $limit - 50; 

						$limit_end = 50;

						$sql.=" LIMIT ".$limit_start." ,".$limit_end."";

					}
					else{

						$sql.=" ORDER BY created DESC";

						$count = $this->db->query($sql)->num_rows();

						$limit_start = $limit - 50; 

						$limit_end = 50;

						$sql.=" LIMIT ".$limit_start." ,".$limit_end."";
					}

					$historyList = $this->db->query($sql)->result_array();

					$pages = ceil($count / 50);

					$data = array();
					if($historyList)
					{
						foreach ($historyList as $key => $list) {
								
							$data[$key]['user_code'] = $list['user_code'];
							$data[$key]['first_name'] = $list['first_name'];
							$data[$key]['middle_name'] = $list['middle_name'];						
							$data[$key]['last_name'] = $list['last_name'];							
							$data[$key]['email_id'] = $list['email_id'];
							$data[$key]['gender'] = $list['gender'];
							$data[$key]['transaction_id'] = $list['transaction_id'];
							if($list['utr_no'])
							{
								$data[$key]['utr_no'] = $list['utr_no'];

							}
							else
							{
								$nestedData[] = 'Not Available';
							}
								if($list['ack_no'])
							{
								$data[$key]['ack_no'] = $list['ack_no'];

							}
							else
							{
								$nestedData[] = 'Not Available';
							}

							if($list['status'] == 1) {
								$data[$key]['status']  = 'Pending';
							}
							elseif($list['status'] == 2) {
								$data[$key]['status']  = 'Success';
							}
							elseif($list['status'] == 3) {
								$data[$key]['status'] = 'Failed';
							}

							$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
							
						}
					}

					if($data)
					{
						$response = array(
							'status' => 1,
							'message' => 'Success',
							'data' => $data,
							'pages' => $pages,
						);	
					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! No Record Found.',
						);	
					}
				}
				else
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry ! Member not valid.'
					);
				}
			}
			log_message('debug', 'NSDL PAN History API Response - '.json_encode($response));	
			echo json_encode($response);
		}
		
		//royalty 
		
		
			
			
			public function getMemberRoyaltyLevelWise()
			{
				
				$response = array();
				$post = $this->input->post();
				
				$user_id = $post['user_id'];
				
				$get_member_rank = $this->db->get_where('users',array('id'=>$user_id))->row_array();
				
				$rank = isset($get_member_rank['current_rank']) ? $get_member_rank['current_rank'] : 0;
				
				//$data = $this->User->getMemberRoyaltyLevelWise($user_id,2,$requiredDirect);
			    
 				$royalty_array = array();
				    	    
				if($rank == 1)
				{
				    $totalLevel = 2;
				    $requiredDirect = 2;
				    $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
				    
				    $rank_array[0] =array(
				        'rank'=>'Bronze',
				        'level' =>$totalLevel,
				        'is_active'=>$data['total_all_level_paid'],
				        'isAchieved' =>$data['isAchieved']
				        );
				        
				    
				}
				elseif($rank == 2){
				    
				        for($i=0;$i<2;$i++)
				        
				        {
				            if($i==0)
				            {
				                
        				    $totalLevel = 2;
        				    $rank_name = 'Bronze';
        				    $requiredDirect = 2;
        				     $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
        			            	    
				            }
				            elseif($i==1){
				                $rank_name = 'Silver';
				                 $totalLevel = 3;
			                    $requiredDirect = 4;
			                     $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
				                
				            }
				                
				            $rank_array[$i] =array(
				        'rank'=>$rank_name,
				        'level' =>$totalLevel,
				        'is_active'=>$data['total_all_level_paid'],
				        'isAchieved' =>$data['isAchieved']
				        );
				        
				            
				        }
				        
				}
				
				elseif($rank == 3){
				    
				        for($i=0;$i<3;$i++)
				        
				        {
				            if($i==0)
				            {
				                
        				    $totalLevel = 2;
        				    $rank_name = 'Bronze';
        				    $requiredDirect = 2;
        				     $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
        			            	    
				            }
				            elseif($i==1){
				                $rank_name = 'Silver';
				                 $totalLevel = 3;
			                    $requiredDirect = 4;
			                     $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
				                
				            }
				            elseif($i==2){
				                $rank_name = 'Gold';
				                 $totalLevel = 4;
			                    $requiredDirect = 8;
			                     $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
				                
				            }
				            
				            $rank_array[$i] =array(
				        'rank'=>$rank_name,
				        'level' =>$totalLevel,
				        'is_active'=>$data['total_all_level_paid'],
				        'isAchieved' =>$data['isAchieved']
				        );
				        
				            
				        }
				}
				
				elseif($rank == 4){
				    
				        for($i=0;$i<4;$i++)
				        
				        {
				            if($i==0)
				            {
				                
        				    $totalLevel = 2;
        				    $rank_name = 'Bronze';
        				    $requiredDirect = 2;
        				     $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
        			            	    
				            }
				            elseif($i==1){
				                $rank_name = 'Silver';
				                 $totalLevel = 3;
			                    $requiredDirect = 4;
			                     $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
				                
				            }
				            elseif($i==2){
				                $rank_name = 'Gold';
				                 $totalLevel = 4;
			                    $requiredDirect = 8;
			                     $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
				                
				            }
				            elseif($i==3){
				                $rank_name = 'Platinum';
				                 $totalLevel = 5;
			                    $requiredDirect = 16;
			                     $data = $this->User->getMemberRoyaltyLevelWise($user_id,$totalLevel,$requiredDirect);
				                
				            }
				            
				           
				            
				            $rank_array[$i] =array(
				        'rank'=>$rank_name,
				        'level' =>$totalLevel,
				        'is_active'=>$data['total_all_level_paid'],
				        'isAchieved' =>$data['isAchieved']
				        );
				        
				            
				        }
				}
				
				$response =array(
				        'status'=>1,
				        'message'=>'Success',
				        'data' =>$rank_array
				        );
				
				echo json_encode($response);
				
    
		}
}





				/* End of file login.php */
		/* Location: ./application/controllers/login.php */