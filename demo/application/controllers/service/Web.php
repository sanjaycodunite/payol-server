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
        $this->load->model('service/Api_model');
        $this->load->model('service/Bbps_model');
        $this->load->model('service/Aeps_model');
        $this->load->model('service/Newaeps_model');
         $this->load->model('service/IciciAeps_model');
        $this->load->model('service/Dmt_model');
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


	/*public function registerAuth(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'Register Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email ', 'xss_clean|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]');
        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
        $this->form_validation->set_rules('transaction_password', 'Transaction Password', 'required|xss_clean|max_length[6]|min_length[4]');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{		

			// check mobile already exits or not
			$chk_user_mobile = $this->db->get_where('users',array('account_id'=>$account_id,'mobile'=>$post['mobile']))->num_rows();
			if($chk_user_mobile){

				$response = array(

				  'status' => 0,
				  'message'=>'Sorry!! mobile already registered.'	

				);

			}
			else{

			  	$user_display_id = $this->User->generate_unique_member_id(5);

	            $account_id = $this->User->get_domain_account();
	            $accountData = $this->User->get_account_data($account_id);

	            $admin_id = $this->User->get_admin_id($account_id);
	            
	            $getDefaultPackID = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$admin_id,'is_default'=>1))->row_array();
            	$default_package_id = isset($getDefaultPackID['id']) ? $getDefaultPackID['id'] : 0 ;

            	$is_refferal_error = 0;
            	if(!empty($post['refercode'])){

            	   $refferal_id = trim($post['refercode']);	

            	   $get_created_by = $this->db->where_in('role_id',array(3,4))->get_where('users',array('user_code'=>$refferal_id))->row_array();

            	   $admin_id = $get_created_by['id'];

            	   if(!$get_created_by){

            	   	  $is_refferal_error = 1;
            	   }
            	}

            	if($is_refferal_error){

            		$response = array(

            			'status'  => 0,
            			'message' => 'Sorry!! refferalID not valid.'

            		);

            	}
            	else{
	            
		            $data = array(   
		                'account_id'         =>  $account_id, 
		                'role_id'            =>  5,      
		                'user_code'          =>  $user_display_id,      
		                'name'               =>  ucwords($post['name']),
		                'username'           =>  $user_display_id,
		                'password'           =>  do_hash($post['password']),
		                'decode_password'    =>  $post['password'],
		                'transaction_password'=>  do_hash($post['transaction_password']),
		                'decoded_transaction_password'=>  $post['transaction_password'],
		                'email'              =>  trim(strtolower($post['email'])),
		                'mobile'             =>  $post['mobile'],
		                'created_by'         =>  $admin_id,   
		                'is_active'          =>  1,
		                'wallet_balance'     =>  0,   
		                'min_wallet_balance' =>  0,
		                'is_verified'        =>  1,   
		                'created'            =>  date('Y-m-d H:i:s'),
		                'package_id'         =>  $default_package_id
		            );

		            $this->db->insert('users',$data);
		            $member_id = $this->db->insert_id();

		            $data = array(
		             'account_id' => $account_id,
		             'member_id' => $member_id,
		             'service_id' => 1,
		             'status' => 1,
		             'created_by' => $admin_id      
		            );

		            $this->db->insert('account_user_services',$data);

		            // SEND SMS
		            $api_url = SMS_REGISTER_MSG_API_URL;

		            $request = array(
		                'flow_id' => $accountData['sms_flow_id'],
		                'sender' => $accountData['sms_sender'],
		                'mobiles' => '91'.$post['mobile'],
		                'userid' => $user_display_id,
		                'password' => $post['password']
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

		            $smsLogData = array(
		             'account_id' => $account_id,   
		             'user_id' => $member_id,
		             'api_url' => $api_url,
		             'api_response' => $output,
		             'post_data' => json_encode($request),
		             'header_data' => json_encode($header),
		             'created' => date('Y-m-d H:i:s'),
		             'created_by' => $loggedUser['id']
		            );
		            
		            $this->db->insert('sms_api_response',$smsLogData);

					$response = array(
					 'status' => 1,
					 'message' => 'Congratulations ! You are registered successfully. Your Member ID - '.$user_display_id.' and Password is '.$post['password'],
					);
				}
			}
		  	
	    }
	    log_message('debug', 'Register Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}*/

    public function loginAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'Login Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('username', 'Username', 'required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Username & Password.'
			);
		}
		else
		{
		  
		  	// check referral_id valid or not
			$username = trim($post['username']);
			$password = do_hash($post['password']);
			// check user credentials
			$chk_user_auth = $this->db->where_in('role_id',array(3,4,5,8))->get_where('users',array('username'=>$username,'password'=>$password,'account_id'=>$account_id))->num_rows();
			// check user credentials
			$chk_user_mobile = $this->db->where_in('role_id',array(3,4,5,8))->get_where('users',array('mobile'=>$username,'password'=>$password,'account_id'=>$account_id))->num_rows();
			if(!$chk_user_auth && !$chk_user_mobile)
			{
				$response = array(
					'status' => 0,
					'message' => 'Sorry ! Username or Password is not valid.'
				);
			}
			else{
				
				if($chk_user_auth)
				{
					$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,email,mobile,wallet_balance,photo,fcm_id')->where_in('role_id',array(3,4,5,8))->get_where('users',array('username'=>$username,'password'=>$password,'account_id'=>$account_id))->row_array();
				}
				else
				{
					$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,email,mobile,wallet_balance,photo,fcm_id')->where_in('role_id',array(3,4,5,8))->get_where('users',array('mobile'=>$username,'password'=>$password,'account_id'=>$account_id))->row_array();
				}
				if($status['is_active'] == 1){
				    
				   

					$userID = $status['id'];

					$get_user_data =$this->db->query("SELECT * FROM tbl_users WHERE account_id='$account_id' AND id = '$userID'")->row_array();

					$activeService = $this->User->account_active_service($userID);   
					$user_aeps_status = $this->User->get_member_aeps_status($userID);
					$user_new_aeps_status = $this->User->get_member_new_aeps_status($userID);
					$user_icici_aeps_status = $this->User->get_member_instantpay_aeps_status($userID);  

					$is_recharge_active = 0;
					$is_transfer_active = 0;
					$is_bbps_live_active = 0;
					$is_aeps_active = 0;
					$is_aeps_active = 0;
					$is_new_aeps_active = 0;
					$is_icici_aeps_active = 0;
					$is_aeps_payout_active = 0;
					$is_upi_collection_active = 0;
					$is_upi_cash_active = 0;
					if(in_array(1, $activeService)){
						$is_recharge_active = 1;
					}
					if(in_array(2, $activeService)){
						$is_transfer_active = 1;
					}
					if(in_array(3, $activeService)){
						$is_aeps_active = 1;
					}
					if(in_array(4, $activeService)){
						$is_bbps_live_active = 1;
					}
					if(in_array(2, $activeService)){
						$is_aeps_payout_active = 1;
					}
					if(in_array(5, $activeService)){
						$is_upi_collection_active = 1;
					}
					if(in_array(7, $activeService)){
						$is_upi_cash_active = 1;
					}
					$is_account_manage_active = 0;
					if(in_array(10, $activeService)){
						$is_account_manage_active = 1;
					}
					$is_digital_sign_active = 0;
					if(in_array(11, $activeService)){
						$is_digital_sign_active = 1;
					}
					$is_instant_loan_active = 0;
					if(in_array(12, $activeService)){
						$is_instant_loan_active = 1;
					}
					$is_travel_active = 0;
					if(in_array(13, $activeService)){
						$is_travel_active = 1;
					}
					$is_insurance_active = 0;
					if(in_array(14, $activeService)){
						$is_insurance_active = 1;
					}
					if(in_array(17, $activeService)){
						$is_new_aeps_active = 1;
					}
					
					if(in_array(19, $activeService)){
						$is_icici_aeps_active = 1;
					}


					$activeGateway = $this->User->account_active_gateway();   
					$is_razorypay_active = 0;
					if(in_array(1, $activeGateway)){
						$is_razorypay_active = 1;
					}

					$activeKeyData = $this->User->account_razorpay_key();

					// get news list
					$newsList = $this->db->get_where('website_news',array('account_id'=>$account_id))->result_array(); 
					$news = '';
					if($newsList)
					{
						foreach($newsList as $nkey=>$nlist)
						{
							if($nkey == 0)
							{
								$news.=$nlist['news'];
							}
							else
							{
								$news.=' '.$nlist['news'];
							}
						}
					}

					$sliderList = $this->db->get_where('website_slider',array('account_id'=>$account_id,'is_app'=>1))->result_array();
					$sliderData = array();
					if($sliderList)
					{
						foreach($sliderList as $skey=>$slist)
						{
							$sliderData[$skey]['link'] = $slist['link'];
							$sliderData[$skey]['imageUrl'] = $slist['image'];
						}
					}

					$today_date = date('Y-m-d');

			        // get total success recharge
			        $get_success_recharge = $this->db->query("SELECT SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_recharge_history as a  INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.account_id = '$account_id' AND a.status = 2 AND DATE(a.created) = '$today_date' AND (b.created_by = '$userID' OR a.member_id = '$userID')")->row_array();
			        $successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
			        
			        // get total success recharge
			        $get_pending_recharge = $this->db->query("SELECT SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_recharge_history as a  INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.account_id = '$account_id' AND a.status = 1 AND DATE(a.created) = '$today_date' AND (b.created_by = '$userID' OR a.member_id = '$userID')")->row_array();
			        $pendingAmount = isset($get_pending_recharge['totalAmount']) ? number_format($get_pending_recharge['totalAmount'],2) : '0.00';

			        // get total success recharge
			        $get_failed_recharge = $this->db->query("SELECT SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_recharge_history as a  INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.account_id = '$account_id' AND a.status = 3 AND DATE(a.created) = '$today_date' AND (b.created_by = '$userID' OR a.member_id = '$userID')")->row_array();
			        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
			        
			        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();

			        $accountDetail = $this->db->get_where('website_account_detail',array('account_id'=>$account_id))->row_array();

			        $upi_qr_status = $this->db->get_where('users',array('is_upi_qr_active'=>1,'id'=>$userID))->num_rows();

			        $cash_qr_status = $this->db->get_where('users',array('is_upi_cash_qr_active'=>1,'id'=>$userID))->num_rows();
			        
			        $paysprint_api_detail = $this->db->get_where('account',array('id'=>$account_id))->row_array();
			        
			        $paysprint_key = $paysprint_api_detail['paysprint_aeps_key'];
			        $paysprint_jwt_key = $paysprint_api_detail['paysprint_secret_key'];
			        $paysprint_partner_id = $paysprint_api_detail['paysprint_partner_id'];
			        
			         
			        $nsdl_pan_type = 0;
			        
			        if($account_id == 3)
			        {
			            $nsdl_pan_type = 1;
			        }
			        else
			        {
			             $nsdl_pan_type = 2;
			        }
			        

			        // generate token
			        $plain_txt = $get_user_data['id'].'|'.$password.'|'.$user_ip_address;
			        $token = $this->User->generateAppToken('encrypt', $plain_txt);
			        
			        log_message('debug', 'Login Auth API Account ID - '.$account_id.' Token String - '.$plain_txt);	
			        log_message('debug', 'Login Auth API Account ID - '.$account_id.' Token - '.$token);
			        
			        $header_data = apache_request_headers();

					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
					
					$this->db->where('id',$get_user_data['id']);
					$this->db->where('account_id',$account_id);
					$this->db->update('users',array('device_id'=>$Deviceid));
					

					$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

				    $user_detail = array(
				        'userID' => $get_user_data['id'],
				        'role_id' => $get_user_data['role_id'],
						'user_code' => $get_user_data['user_code'],
						'name' => $get_user_data['name'],
						'email' => $get_user_data['email'],
						'mobile' => $get_user_data['mobile'],
						'wallet_balance'=>$wallet_balance,
						'e_wallet_balance'=>$wallet_balance,
						'profile_photo' => base_url($get_user_data['photo']),
						'fcm_id'        => $get_user_data['fcm_id'],
						'is_recharge_active' => $is_recharge_active,
						'is_transfer_active' => $is_transfer_active,
						'is_razorypay_active' => $is_razorypay_active,
						'is_bbps_live_active' => $is_bbps_live_active,
						'is_aeps_active' => $is_aeps_active,
						'user_aeps_status' => $user_aeps_status,
						'user_new_aeps_status' => $user_new_aeps_status,
						'user_icici_aeps_status' => $user_icici_aeps_status,
						'is_new_aeps_active' => $is_new_aeps_active,
						'is_icici_aeps_active' => $is_icici_aeps_active,
						'is_aeps_payout_active' => $is_aeps_payout_active,
						'is_upi_collection_active' => $is_upi_collection_active,
						'is_upi_cash_active' => $is_upi_cash_active,
						'is_account_manage_active' => $is_account_manage_active,
						'is_digital_sign_active' => $is_digital_sign_active,
						'is_instant_loan_active' => $is_instant_loan_active,
						'is_travel_active' => $is_travel_active,
						'is_insurance_active' => $is_insurance_active,
						'razor_pay_key' => $activeKeyData['key'],
						'razor_pay_secret' => $activeKeyData['secret'],
						'news' => $news,
						'upi_qr_status' => $upi_qr_status,
						'cash_qr_status' => $cash_qr_status,
						'sliderData' => $sliderData,
						'successAmount' => $successAmount,
						'pendingAmount' => $pendingAmount,
						'failedAmount' => $failedAmount,
						'user_dob'  => isset($get_user_data['dob']) ? $get_user_data['dob'] : '',
						'user_aadhar_no'  => isset($get_user_data['aadhar']) ? $get_user_data['aadhar'] : '',
						'company_mobile' => isset($contactDetail['mobile']) ? $contactDetail['mobile'] : '',
						'company_email' => isset($contactDetail['email']) ? $contactDetail['email'] : '',
						'company_address' => isset($contactDetail['address']) ? $contactDetail['address'] : '',
						'support_working_time' => isset($contactDetail['support_working_time']) ? $contactDetail['support_working_time'] : '',
						'bank_name' => isset($accountDetail['bank_name']) ? $accountDetail['bank_name'] : '',
						'branch' => isset($accountDetail['branch']) ? $accountDetail['branch'] : '',
						'account_holder_name' => isset($accountDetail['account_holder_name']) ? $accountDetail['account_holder_name'] : '',
						'account_no' => isset($accountDetail['account_no']) ? $accountDetail['account_no'] : '',
						'ifsc' => isset($accountDetail['ifsc']) ? $accountDetail['ifsc'] : '',
						'phonepe_number' => isset($accountDetail['phonepe']) ? $accountDetail['phonepe'] : '',
						'google_pay_number' => isset($accountDetail['google_pay']) ? $accountDetail['google_pay'] : '',
						'login_user_txnpass' => $get_user_data['decoded_transaction_password'],
						'encoded_user_password' => $get_user_data['password'],
						'paysprint_key' =>$paysprint_key,
						'paysprint_partner_id' =>$paysprint_partner_id,
						'paysprint_jwt_key' =>$paysprint_jwt_key,
						'nsdl_pan_type' =>$nsdl_pan_type,
						'token' => $token
				    );
	                
	                $response = array(
					 'status' => 1,
					 'message' => 'Logged in Successfully.',
					 'user_detail'=>$user_detail
					);
				
				
					
				}
				else
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry ! Your account is not active.'
					);
					
				}
				
			}
			
		
	    }
	    log_message('debug', 'Login Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function verifyUserAuth()
	{
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
        $response =array();
        // get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'verifyUserAuth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			//check for foem validation
			$post = $this->input->post();
			
			log_message('debug', 'verifyUserAuth - '.$account_id.' Post Data - '.json_encode($post));	
			
			$this->load->library('form_validation');

			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('password', 'Amount', 'required');
			
	        if($this->form_validation->run() == FALSE) {
				
				$response = array(
					'status' => 0,
					'message' => 'Session out.Please Login Again.',
					'is_login' => 0
				);

			}
			else
			{
				$userID = $post['userID'];
				$password = $post['password'];

				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);

				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				if($tokenUserID && $tokenPwd && $tokenIP)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address)
					{

						$chk_user = $this->db->get_where('users',array('id'=>$userID,'password'=>$password,'is_active'=>1))->row_array();

						if($chk_user){

							$response = array(
							  'status' => 1,
							  'message' => 'Success',
							  'is_login'=>1	
							);
						}else{

							$response = array(
							  'status' => 0,
							  'message' => 'Session out.Please Login Again.',
							  'is_login'=>0	
							);
						}
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

			}
		}
		log_message('debug', 'verifyUserAuth Response - '.json_encode($response));	
		echo json_encode($response);
	}


	public function getCountryList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();
		// get country list
		$countryList = $this->db->order_by('name','asc')->get('countries')->result_array();
		$data = array();
		if($countryList)
		{
			foreach($countryList as $key=>$list)
			{
				$data[$key]['country_id'] = $list['id'];
				$data[$key]['country_code'] = $list['sortname'];
				$data[$key]['country_name'] = $list['name'];
			}
		}
		$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
		);
		log_message('debug', 'Country List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getStateList($country_code = ''){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$get = $this->input->get();
		$code = isset($get['code']) ? $get['code'] : '';

		if($country_code == '')
		{
			if($code == '')
			{
				$country_code = 'IN';
			}
			else
			{
				$country_code = $code;
			}
		}

		$countryList = $this->db->order_by('name','asc')->get_where('states',array('country_code_char2'=>$country_code))->result_array();
		$data = array();
		if($countryList)
		{
			foreach($countryList as $key=>$list)
			{
				$data[$key]['state_id'] = $list['id'];
				$data[$key]['state_name'] = $list['name'];
			}
		}
		$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
		);
		log_message('debug', 'State List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function addMemberAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Add Member Auth API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Add Member Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('role_id', 'Member Type', 'required|xss_clean');
			$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
	        $this->form_validation->set_rules('email', 'Email ', 'xss_clean|valid_email');
	        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]');
	        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
	        $this->form_validation->set_rules('transaction_password', 'Transaction Password', 'required|xss_clean|max_length[6]|min_length[4]');
	        $this->form_validation->set_rules('country_id', 'Country', 'required|xss_clean');
	        $this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');
	        $this->form_validation->set_rules('city', 'City', 'required|xss_clean');

			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please Enter Username & Password.'
				);
			}
			else
			{
			  	if($post['role_id'] == 1 || $post['role_id'] == 2 || $post['role_id'] == 3 || $post['role_id'] == 6  || $post['role_id'] == 7){

					$response = array(
						 'status' => 0,
						 'message' => 'Sorry ! You are not authorized to create member.',
					);

				}
				else
				{
					// decrypt token
					$decryptToken = $this->User->generateAppToken('decrypt',$token);
					log_message('debug', 'Add Member Auth API Decrypt Token String - '.$decryptToken);
					$explodeToken = explode('|',$decryptToken);
					$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
					$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
					$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
					if($tokenUserID && $tokenPwd && $tokenIP)
					{
						$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
						if($chk_token_user && $tokenUserID == $post['user_id'] && $tokenIP == $user_ip_address)
						{
							// check mobile already exits or not
							$chk_user_mobile = $this->db->get_where('users',array('account_id'=>$account_id,'mobile'=>$post['mobile']))->num_rows();
							if($chk_user_mobile){

								$response = array(
									 'status' => 0,
									 'message' => 'Sorry ! Mobile No. Already Registered.',
								);

							}
							else
							{
							  	$user_display_id = $this->User->generate_unique_member_id($post['role_id']);

							  	$admin_id = $this->User->get_admin_id($account_id);

					            // get default package id
					            $getDefaultPackID = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$admin_id,'is_default'=>1))->row_array();
					            $default_package_id = isset($getDefaultPackID['id']) ? $getDefaultPackID['id'] : 0 ;

							  	$data = array(   
					                'account_id'         =>  $account_id, 
					                'role_id'            =>  $post['role_id'],      
					                'user_code'          =>  $user_display_id,      
					                'name'               =>  ucwords($post['name']),
					                'username'           =>  $user_display_id,
					                'password'           =>  do_hash($post['password']),
					                'decode_password'    =>  $post['password'],
					                'transaction_password'=>  do_hash($post['transaction_password']),
					                'decoded_transaction_password'=>  $post['transaction_password'],
					                'email'              =>  trim(strtolower($post['email'])),
					                'mobile'             =>  $post['mobile'],
					                'country_id'         =>  $post['country_id'],
					                'state_id'         =>  $post['state_id'],
					                'city'         =>  $post['city'],
					                'created_by'         =>  $post['user_id'],
					                'creator_id'         =>  $post['user_id'],   
					                'is_active'          =>  1,
					                'wallet_balance'     =>  0,   
					                'is_verified'        =>  1,   
					                'created'            =>  date('Y-m-d H:i:s'),
					                'package_id'         =>  $default_package_id
					            );

					            $this->db->insert('users',$data);
					            $member_id = $this->db->insert_id();

					            $data = array(
					             'account_id' => $account_id,
					             'member_id' => $member_id,
					             'service_id' => 1,
					             'status' => 1,
					             'created_by' => $post['user_id']      
					            );

					            $this->db->insert('account_user_services',$data);

								$response = array(
									 'status' => 1,
									 'message' => 'Member saved successfully.',
								);
							}
						}
						else
						{
							$response = array(
							  'status' => 0,
							  'message' => 'Session out.Please Login Again.',
							  'is_login'=>0	
							);
						}
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
			
		    }
		}
	    log_message('debug', 'Add Member Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function getMemberList(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Member List API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();

			$user_id = isset($post['user_id']) ? $post['user_id'] : 0 ;
			// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			if($tokenUserID && $tokenPwd && $tokenIP)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $user_id && $tokenIP == $user_ip_address)
				{
					$userList = $this->db->where_in('role_id',array(4,5))->get_where('users',array('account_id'=>$account_id,'created_by'=>$user_id))->result_array();
					$data = array();
					if($userList)
					{
						foreach($userList as $key=>$list)
						{
							$data[$key]['user_id'] = $list['id'];
							$data[$key]['user_code'] = $list['user_code'];
							$data[$key]['name'] = $list['name'];
							$data[$key]['email'] = $list['email'];
							$data[$key]['mobile'] = $list['mobile'];
							$data[$key]['wallet_balance'] = $list['wallet_balance'];
							$data[$key]['e_wallet_balance'] = $list['aeps_wallet_balance'];
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
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Get Member List API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function getOperatorList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();


		$post = $this->input->post();
		$type = isset($post['type']) ? $post['type'] : '';

			
		if($account_id == 4 || $account_id == 1){

			if($type == 'Prepaid'){
				$countryList = $this->db->where_in('id',array(1,2,3,4,5,31))->order_by("order_no", "asc")->get_where('operator',array('type'=>$type,'status'=>1))->result_array();
			}
			elseif($type == 'DTH'){

				$countryList = $this->db->where_in('id',array(17,19,20,21,91,99))->order_by("order_no", "asc")->get_where('operator',array('type'=>$type,'status'=>1))->result_array();
			}
			else{

				$countryList = $this->db->get_where('operator',array('type'=>$type))->result_array();
			}
			$data = array();
			if($countryList)
			{
				foreach($countryList as $key=>$list)
				{
					$data[$key]['operator_id'] = $list['id'];
					$data[$key]['operator_name'] = $list['operator_name'];
					$data[$key]['icon'] = $list['icon'];
				}
			}
		}
		else{

			$countryList = $this->db->get_where('operator',array('type'=>$type,'status'=>1))->result_array();
			$data = array();
			if($countryList)
			{
				foreach($countryList as $key=>$list)
				{
					$data[$key]['operator_id'] = $list['id'];
					$data[$key]['operator_name'] = $list['operator_name'];
				}
			}

		}


		$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
		);
		log_message('debug', 'Get Operator List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getCircleList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$countryList = $this->db->get('circle')->result_array();
		$data = array();
		if($countryList)
		{
			foreach($countryList as $key=>$list)
			{
				$data[$key]['circle_id'] = $list['id'];
				$data[$key]['circle_name'] = $list['circle_name'];
			}
		}
		$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
		);
		log_message('debug', 'Get Circle List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function rechargeAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Recharge Auth API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {

        $post = $this->input->post();
		log_message('debug', 'Recharge Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|max_length[12]|xss_clean');
        $this->form_validation->set_rules('operator', 'Operator', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Username & Password.'
			);
		}
		else
		{
		  	$loggedAccountID = $post['user_id'];

		  	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Recharge Auth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';

			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Recharge Auth Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)

			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();

				if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] ==1 && $chk_user_token['is_login'] == 1)
					{
						 $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

						$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
		  	$member_code = $chk_wallet_balance['user_code'];
		  	$min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
		  	$final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];
		  	log_message('debug', 'Recharge Auth API Member Code - '.$member_code.' Wallet Balance - '.$chk_wallet_balance['wallet_balance']);	
		  	$is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
		  	$admin_id = $this->User->get_admin_id();
            $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

		  	if($user_before_balance < $post['amount']){
		  		$response = array(
					'status' => 0,
					'message' => 'Sorry ! Insufficient balance in your account.'
				);
		  	}
		  	elseif($user_before_balance < $final_deduct_wallet_balance){
		  		$response = array(
					'status' => 0,
					'message' => 'Sorry ! You have to maintain minimum balance in your account.'
				);
		  	}
		  	elseif($is_cogent_instantpay_api && $admin_wallet_balance < $post['amount']){
		  		$response = array(
					'status' => 0,
					'message' => 'Sorry ! Insufficient balance in admin account.'
				);
		  	}
		  	else
		  	{
		  		// get account role id
				$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();
				$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
				$md_id = $loggedAccountID;
				if($user_role_id == 4)
				{
					$md_id = $this->User->get_master_distributor_id($loggedAccountID);
				}
				elseif($user_role_id == 5)
				{
					$md_id = $this->User->get_master_distributor_id($loggedAccountID);
				}

		  		$urlResponse = $this->User->generate_api_url($md_id,$post['operator'],$post['amount'],$loggedAccountID);
		  		log_message('debug', 'Recharge Auth API - Generate API Response - '.json_encode($urlResponse));	
		  		if($urlResponse['status'] && $urlResponse['api_id'])
	            {
	            	if($accountData['account_type'] == 2)
	                {
	                    // get operator code
	                    $get_operator_code = $this->db->get_where('api_operator',array('account_id'=>SUPERADMIN_ACCOUNT_ID,'api_id'=>$urlResponse['api_id'],'opt_id'=>$post['operator']))->row_array();
	                }
	                else
	                {
	                	// get operator code
	                	$get_operator_code = $this->db->get_where('api_operator',array('account_id'=>$account_id,'api_id'=>$urlResponse['api_id'],'opt_id'=>$post['operator']))->row_array();
	                }
	                $opt_code = isset($get_operator_code['opt_code']) ? $get_operator_code['opt_code'] : '';
	                
	                log_message('debug', 'Recharge Auth API - Operator Code - '.$opt_code);	
	                
	                $circle_code = 19;
	                
	                log_message('debug', 'Recharge Auth API - Circle Code - '.$circle_code);	
	                // generate recharge unique id
	                $recharge_unique_id = rand(1111,9999).time();
	                
	                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

	                $user_after_balance = $user_before_balance - $post['amount'];

	                $eKycError = 0;

	                if($urlResponse['is_instantpay_api'])
	                {
	                    if(!$chk_wallet_balance['is_instantpay_ekyc'])
	                    {
	                        $eKycError = 1;
	                    }
	                }

	                if($eKycError)
	                {
	                	$response = array(
									'status' => 0,
									'message' => 'Sorry ! Your eKyc not approved yet, please submit your eKyc.'
								);
	                }
	                else
	                {
		                // get system operator code
	                	$system_opt_id = $post['operator'];

		                $data = array(
		                    'account_id'         => $account_id,
		                    'member_id'          => $loggedAccountID,
		                    'api_id'             => $urlResponse['api_id'],
		                    'recharge_type'      => 1,
		                    'recharge_subtype'   => 1,
		                    'recharge_display_id'=> $recharge_unique_id,
		                    'mobile'             => $post['mobile'],
		                    'account_number'     => isset($post['acnumber']) ? $post['acnumber'] : '',
		                    'operator_code'      => $opt_code,
		                    'system_opt_id'      => $system_opt_id,
		                    'circle_code'        => $circle_code,
		                    'amount'             => $post['amount'],
		                    'before_balance'     => $user_before_balance,
	                    	'after_balance'      => $user_after_balance,
		                    'status'             => 1,
		                    'is_from_app'		 => 1,
		                    'created'            => date('Y-m-d H:i:s')                  
		                );


		                $this->db->insert('recharge_history',$data);
		                $recharge_id = $this->db->insert_id();

		                log_message('debug', 'Recharge Auth API - Save Recharge Data System Recharge ID - '.$recharge_id);

		                $before_balance =  $this->User->getMemberWalletBalanceSP($loggedAccountID);

                    
		                $after_balance = $before_balance - $post['amount'];    

		                $wallet_data = array(
		                    'account_id'          => $account_id,
		                    'member_id'           => $loggedAccountID,    
		                    'before_balance'      => $before_balance,
		                    'amount'              => $post['amount'],  
		                    'after_balance'       => $after_balance,      
		                    'status'              => 1,
		                    'type'                => 2,      
		                    'created'             => date('Y-m-d H:i:s'),      
		                    'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);
		                if($is_cogent_instantpay_api)
		                {
		                    $admin_id = $this->User->get_admin_id();
		                    $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
		                    $admin_after_wallet_balance = $admin_before_wallet_balance - $post['amount'];

		                    $wallet_data = array(
		                        'account_id'          => $account_id,
		                        'member_id'           => $admin_id,    
		                        'before_balance'      => $admin_before_wallet_balance,
		                        'amount'              => $post['amount'],  
		                        'after_balance'       => $admin_after_wallet_balance,      
		                        'status'              => 1,
		                        'type'                => 2,   
		                        'wallet_type'         => 1,   
		                        'created'             => date('Y-m-d H:i:s'),      
		                        'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
		                    );

		                    $this->db->insert('virtual_wallet',$wallet_data);

		                }

		                if($urlResponse['is_instantpay_api'])
		                {
		                    if($chk_wallet_balance['is_instantpay_ekyc'])
		                    {
		                        // call recharge API
		                        $api_response = $this->User->instantpay_rechage_api($opt_code,$loggedAccountID,$recharge_unique_id,$post['mobile'],$post['amount'],$urlResponse['api_id'],$member_code,'MD');
		                    }
		                    else
		                    {
		                        $api_response = array(
		                            'status' => 3,
		                            'opt_msg' => 'Sorry ! Your eKyc not approved yet, please submit your eKyc.'
		                        );
		                    }
		                }
		                else
		                {
		                
			                $api_url = $urlResponse['api_url'];
			                $api_post_data = $urlResponse['post_data'];
			                $api_url = str_replace('{AMOUNT}',$post['amount'],$api_url);
			                $api_url = str_replace('{OPERATOR}',$opt_code,$api_url);
			                $api_url = str_replace('{CIRCLE}',$circle_code,$api_url);
			                $api_url = str_replace('{TXNID}',$recharge_unique_id,$api_url);
			                $api_url = str_replace('{MOBILE}',$post['mobile'],$api_url);
			                $api_url = str_replace('{MEMBERID}',$member_code,$api_url);

			                // replace post data
			                if($api_post_data)
			                {
			                    foreach($api_post_data as $apikey=>$apival)
			                    {
			                        if($apival == '{AMOUNT}')
			                        {
			                            $api_post_data[$apikey] = $post['amount'];
			                        }
			                        elseif($apival == '{OPERATOR}')
			                        {
			                            $api_post_data[$apikey] = $opt_code;
			                        }
			                        elseif($apival == '{CIRCLE}')
			                        {
			                            $api_post_data[$apikey] = $circle_code;
			                        }
			                        elseif($apival == '{TXNID}')
			                        {
			                            $api_post_data[$apikey] = $recharge_unique_id;
			                        }
			                        elseif($apival == '{MOBILE}')
			                        {
			                            $api_post_data[$apikey] = $post['mobile'];
			                        }
			                        elseif($apival == '{MEMBERID}')
			                        {
			                            $api_post_data[$apikey] = $member_code;
			                        }
			                    }
			                }

			                log_message('debug', 'Recharge Auth API - Final API URL - '.$api_url);
			                // call recharge API
			                $api_response = $this->User->prepaid_rechage_api($api_url,$api_post_data,$loggedAccountID,$recharge_unique_id,$urlResponse['api_id'],$urlResponse['response_type'],$urlResponse['responsePara'],$urlResponse['seperator'],$urlResponse['header_data'],$member_code,'MD');
			            }

		                log_message('debug', 'Recharge Auth API - API Final Response - '.json_encode($api_response));

		                if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
		                {
		                    
		                    if($api_response['status'] == 1){
		                        // update recharge status
		                        $this->db->where('id',$recharge_id);
		                        $this->db->where('recharge_display_id',$recharge_unique_id);
		                        $this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
		                        
		                        $response = array(
									'status' => 1,
									'message' => 'Your recharge is pending. Status will be updated soon.'
								);
		                          
		                    }
		                    elseif($api_response['status'] == 2)
		                    {
		                        // update recharge status
		                        $this->db->where('id',$recharge_id);
		                        $this->db->where('recharge_display_id',$recharge_unique_id);
		                        $this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));

		                        // distribute commision
		                        $this->User->distribute_recharge_commision($recharge_id,$recharge_unique_id,$post['amount'],$loggedAccountID);
		                        
		                        $message = 'Congratulations!! Your recharge successfully credited.';

			        			//$this->User->sendNotification($loggedAccountID,'Recharge',$message);
		                        
		                        $response = array(
									'status' => 1,
									'message' => 'Congratulations ! Your recharge successfully credited.'
								);
		                    }
		                }
		                else
		                {
		                    // update recharge status
		                    $this->db->where('id',$recharge_id);
		                    $this->db->where('recharge_display_id',$recharge_unique_id);
		                    $this->db->update('recharge_history',array('status'=>3));

		                    $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                    
		                    $after_balance = $before_balance + $post['amount'];    

		                    $wallet_data = array(
		                        'account_id'          => $account_id,
		                        'member_id'           => $loggedAccountID,    
		                        'before_balance'      => $before_balance,
		                        'amount'              => $post['amount'],  
		                        'after_balance'       => $after_balance,      
		                        'status'              => 1,
		                        'type'                => 1,      
		                        'created'             => date('Y-m-d H:i:s'),      
		                        'description'         => 'Recharge #'.$recharge_unique_id.' Amount Refund Credited.'
		                    );

		                    $this->db->insert('member_wallet',$wallet_data);

		                    if($is_cogent_instantpay_api)
		                    {
		                        $admin_id = $this->User->get_admin_id();
		                        $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
		                        $admin_after_wallet_balance = $admin_before_wallet_balance + $post['amount'];

		                        $wallet_data = array(
		                            'account_id'          => $account_id,
		                            'member_id'           => $admin_id,    
		                            'before_balance'      => $admin_before_wallet_balance,
		                            'amount'              => $post['amount'],  
		                            'after_balance'       => $admin_after_wallet_balance,      
		                            'status'              => 1,
		                            'type'                => 1,   
		                            'wallet_type'         => 1,   
		                            'created'             => date('Y-m-d H:i:s'),      
		                            'description'         => 'Recharge #'.$recharge_unique_id.' Amount Refund Credited.'
		                        );

		                        $this->db->insert('virtual_wallet',$wallet_data);

		                    }
		                    $response = array(
									'status' => 0,
									'message' => 'Sorry ! Your recharge failed from operator side.'
								);
		                }
		            }
	            }
	            else
	            {
	                $response = array(
						'status' => 0,
						'message' => 'Sorry ! There is no active api found.'
					);
	            }
				
			  }

			}

			else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}

				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

		  	
	    	}

        }
		
	    log_message('debug', 'Recharge Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	
	public function getWalletList(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Wallet List API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];
	        // decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get Wallet List API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			if($tokenUserID && $tokenPwd && $tokenIP)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $user_id && $tokenIP == $user_ip_address)
				{

			    	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
			    	$limit = $page_no * 50;


					if($fromDate && $toDate){	

					  
					  $count = $this->db->get_where('member_wallet',array('account_id'=>$account_id,'member_id'=>$user_id,'wallet_type'=>1,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

					  $limit_start = $limit - 50; 
				                     
				      $limit_end = $limit;	

					  $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('account_id'=>$account_id,'member_id'=>$user_id,'wallet_type'=>1,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();

					}
					else{


					  $count = $this->db->get_where('member_wallet',array('account_id'=>$account_id,'member_id'=>$user_id,'wallet_type'=>1))->num_rows();

					  $limit_start = $limit - 50; 
				                     
				      $limit_end = $limit;	
				      
					  $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('account_id'=>$account_id,'member_id'=>$user_id,'wallet_type'=>1))->result_array();

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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Get Wallet List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getEWalletList(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Ewallet List API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];
	    	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get Wallet List API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			if($tokenUserID && $tokenPwd && $tokenIP)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $user_id && $tokenIP == $user_ip_address)
				{
					if($fromDate && $toDate){	

					  
					  $count = $this->db->get_where('member_wallet',array('account_id'=>$account_id,'member_id'=>$user_id,'wallet_type'=>2,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

					  $limit_start = $limit - 50; 
				                     
				      $limit_end = $limit;	

					  $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('account_id'=>$account_id,'member_id'=>$user_id,'wallet_type'=>2,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();

					}
					else{


					  $count = $this->db->get_where('member_wallet',array('account_id'=>$account_id,'member_id'=>$user_id,'wallet_type'=>2))->num_rows();

					  $limit_start = $limit - 50; 
				                     
				      $limit_end = $limit;	
				      
					  $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('member_wallet',array('account_id'=>$account_id,'member_id'=>$user_id,'wallet_type'=>2))->result_array();

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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		
		log_message('debug', 'Get E-Wallet List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function fundTransferAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);

		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Fund Transfer Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Fund Transfer API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
			$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
			$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
			$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
	        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');

			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please Enter Required field.'
				);
			}
			else
			{
			  	$loggedAccountID = $post['user_id'];
			  	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Fund Transfer Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				if($tokenUserID && $tokenPwd && $tokenIP)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address)
					{
					  	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
					  	$member_code = $chk_wallet_balance['user_code'];
					  	log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' Wallet Balance - '.$chk_wallet_balance['wallet_balance']);

					  	$memberID = $member_code;
						$mobile = $post['mobile'];
						$account_holder_name = $post['account_holder_name'];
						$account_no = $post['account_no'];
						$ifsc = $post['ifsc'];
						$amount = $post['amount'];
						$transaction_id = time().rand(1111,9999);


			            // get dmr surcharge
			            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
			            log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' DMT Surcharge Amount - '.$surcharge_amount);
			            
			            $final_amount = $amount + $surcharge_amount;
			            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
			            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
						$final_deduct_wallet_balance = $min_wallet_balance + $final_amount;

			            if($before_balance < $final_amount){
			                log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' Insufficient wallet error.');
			                $response = array(
								'status' => 0,
								'message' => 'Sorry ! Insufficient balance in account.'
							);
			            }
			            elseif($before_balance < $final_deduct_wallet_balance){
			                log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' Minimum wallet error.');
			                $response = array(
								'status' => 0,
								'message' => 'Sorry ! You have to maintain minimum balance in account.'
							);
			            }
			            else
			            { 
						
							$api_url = DMR_API_URL."customernumber=".$mobile."&Accountnumber=".$account_no."&CustomerName=".urlencode($account_holder_name)."&amount=".$amount."&ifsccode=".$ifsc."&usertx=".$transaction_id;
							log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' - DMT API URL - '.$api_url);
							$headers = [
					            'memberid: '.$accountData['dmt_username'],
					            'password: '.$accountData['dmt_password']
					        ];


							$ch = curl_init();
					        curl_setopt($ch, CURLOPT_URL,$api_url);
					        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					        
					        $output = curl_exec ($ch);
					        
					        
					        curl_close ($ch);

					        /*$output = '{"Error":"False","Message":null,"Data":{"status":"ACCEPTED","statusCode":"DE_002","statusMessage":"Request accepted"}}';*/

					        $responseData = json_decode($output,true);

					        log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' - DMT API Response - '.$output);

							// save api response
							$apiData = array(
								'account_id' => $account_id,
								'user_id' => $loggedAccountID,
								'recharge_id' => $transaction_id,
								'api_response' => $output,
								'api_url' => $api_url,
								'status' => 1,
								'is_dmr' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('api_response',$apiData);

							

				            $after_wallet_balance = $before_balance - $final_amount;    

							if(isset($responseData['Error']) && $responseData['Error'] == 'False')
							{
								if(isset($responseData['Data']['status']) && $responseData['Data']['status'] == 'FAILURE')
								{
									log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' - DMT Transaction Failed.');
									$apiMsg = $responseData['Data']['statusMessage'];
									$data = array(
										'account_id' => $account_id,
										'user_id' => $loggedAccountID,
										'before_wallet_balance'=>$before_balance,
										'transfer_amount' => $amount,
										'transfer_charge_amount' => $surcharge_amount,
										'total_wallet_charge' => $final_amount,
										'after_wallet_balance' => $after_wallet_balance,
										'transaction_id' => $transaction_id,
										'encode_transaction_id' => do_hash($transaction_id),
										'api_response' => $output,
										'status' => 4,
										'memberID' => $memberID,
										'mobile' => $mobile,
										'account_holder_name' => $account_holder_name,
										'account_no' => $account_no,
										'ifsc' => $ifsc,
										'is_app' => 1,
										'created' => date('Y-m-d H:i:s')
									);
									$this->db->insert('user_fund_transfer',$data);

									$response = array(
										'status' => 0,
										'message' => 'Sorry ! Your transaction failed due to '.$apiMsg
									);
									
								}
								else
								{
									log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' - DMT Transaction Success.');
									$wallet_data = array(
				                        'account_id'          => $account_id,
				                        'member_id'           => $loggedAccountID,    
				                        'before_balance'      => $before_balance,
				                        'amount'              => $final_amount,  
				                        'after_balance'       => $after_wallet_balance,      
				                        'status'              => 1,
				                        'type'                => 2,
				                        'wallet_type'         => 1,      
				                        'created'             => date('Y-m-d H:i:s'),      
				                        'description'         => 'Fund Transfer #'.$transaction_id.' Amount Deducted.'
				                    );

				                    $this->db->insert('member_wallet',$wallet_data);

				                    log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' - DMT Transaction Wallet Deducation Done.');
				                    
									$data = array(
										'account_id' => $account_id,
										'user_id' => $loggedAccountID,
										'before_wallet_balance' =>$before_balance,
										'transfer_amount' => $amount,
										'transfer_charge_amount' => $surcharge_amount,
										'total_wallet_charge' => $final_amount,
										'after_wallet_balance' => $after_wallet_balance,
										'transaction_id' => $transaction_id,
										'encode_transaction_id' => do_hash($transaction_id),
										'api_response' => $output,
										'status' => 2,
										'memberID' => $memberID,
										'mobile' => $mobile,
										'account_holder_name' => $account_holder_name,
										'account_no' => $account_no,
										'ifsc' => $ifsc,
										'is_app' => 1,
										'created' => date('Y-m-d H:i:s')
									);
									$this->db->insert('user_fund_transfer',$data);

									$message = 'Your transaction successfully proceed.';

					        		//$this->User->sendNotification($loggedAccountID,'Fund Transfer',$message);

									$response = array(
										'status' => 1,
										'message' => 'Your transaction successfully proceed.'
									);
								}
							}
							else
							{
								log_message('debug', 'Fund Transfer Auth API Member Code - '.$member_code.' - DMT Transaction Failed From API Operator Side.');
								$response = array(
										'status' => 0,
										'message' => 'Sorry ! Your transaction failed due to '.$responseData['Message']
									);
					        }
							
						}
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
			
		    }
		}
	    log_message('debug', 'Fund Transfer Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getPayoutTransferHistory(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Payout Transfer History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {

			$post = $this->input->post();
			$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	    	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get Payout Transfer History API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			if($tokenUserID && $tokenPwd && $tokenIP)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $user_id && $tokenIP == $user_ip_address)
				{
			    	if($fromDate && $toDate){

			    		$count = $this->db->get_where('tbl_user_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->num_rows();

					    $limit_start = $limit - 50; 
					                     
					    $limit_end = $limit;

						$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,"DATE(tbl_user_fund_transfer.created) >=" => $fromDate,"DATE(tbl_user_fund_transfer.created) <=" => $toDate))->result_array();

					}	
					else{

					   $count = $this->db->get_where('tbl_user_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->num_rows();

					   $limit_start = $limit - 50; 
					                     
					   $limit_end = $limit;	
			    	  		
					   $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('tbl_user_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->result_array();

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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Get Fund Transfer List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getAccountMemberList(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Account Member List API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
			// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get Account Member List API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			if($tokenUserID && $tokenPwd && $tokenIP)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address)
				{

					// get users list
					$memberList = $this->db->where_in('role_id',array(4,5))->get_where('users',array('account_id'=>$account_id,'created_by'=>$loggedAccountID))->result_array();
					$data = array();
					if($userList)
					{
						foreach($userList as $key=>$list)
						{
							$data[$key]['user_id'] = $list['id'];
							$data[$key]['name'] = $list['name'];
							$data[$key]['user_code'] = $list['user_code'];
							$data[$key]['wallet_balance'] = $list['wallet_balance'];
							$data[$key]['e_wallet_balance'] = $list['aeps_wallet_balance'];
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
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Get Account Member List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getTicketTypeList(){
		
		$account_id = $this->User->get_domain_account();
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
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Ticket Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Ticket Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
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
			  	$loggedAccountID = $post['user_id'];
			  	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				if($tokenUserID && $tokenPwd && $tokenIP)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address)
					{

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
			                'account_id'          => $account_id,
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
			                'account_id' => $account_id,
			                'ticket_id' => $system_ticket_id,
			                'message' => $post['message'],
			                'attachment' => $filePath,
			                'status' => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'created_by' => $loggedAccountID
			            );
			            $this->db->insert('ticket_reply',$ticketData);
			            $response = array(
							'status' => 1,
							'message' => 'Ticket generated successfully.'
						);

						$message = 'Ticket generated successfully.';
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

			    //$this->User->sendNotification($loggedAccountID,'Support Ticket',$message);
			
		    }
		}
	    log_message('debug', 'Ticket Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getTicketList(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Ticket List API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
			// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get Ticket List API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			if($tokenUserID && $tokenPwd && $tokenIP)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address)
				{
					// get users list
					$sql = "SELECT a.*, b.title as related_to_title, c.title as status_title FROM tbl_ticket as a INNER JOIN tbl_ticket_related as b ON b.id = a.related_to INNER JOIN tbl_ticket_status as c ON c.id = a.status  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Get Ticket List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	

    public function userDetail(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'User Detail API Header - '.json_encode($header_data));	
        
        
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'User Detail API Response Post Data - '.json_encode($post));	
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
	            $fcm_id = isset($post['fcm_id']) ? $post['fcm_id'] : ''; 
	             $device_id = isset($post['device_id']) ? $post['device_id'] : ''; 
	            // decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'User Detail API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			
				$get_token_pass = $this->db->get_where('users',array('id'=>$userID,'is_active'=>1))->row_array();
				$token_pass = $get_token_pass['password'];

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$userID,$token_pass,$Deviceid);
				
				log_message('debug', 'User Detail Check User Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
				    
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
			            // check user credential
						$chk_user_credential =$this->db->query("SELECT * FROM tbl_users WHERE account_id='$account_id' AND id = '$userID'")->num_rows();
						
						if(!$chk_user_credential)
			            {
							$response = array(
								'status' => 0,
								'message' => 'User Id Not Valid'
							);
			                
			            }
						else
						{
							if($fcm_id != ''){
						  
							  $this->db->where('id',$userID);
							  $this->db->where('account_id',$account_id);
							  $this->db->update('users',array('fcm_id'=>$fcm_id));
							    
							}
							
							if($device_id != ''){

							$this->db->where('id',$userID);
							 $this->db->where('account_id',$account_id);
							$this->db->update('users',array('device_id'=>$device_id));

							}

							$get_user_data =$this->db->query("SELECT * FROM tbl_users WHERE account_id='$account_id' AND id = '$userID'")->row_array();
							$is_active = isset($get_user_data['is_active']) ? $get_user_data['is_active'] : 0 ;
							if(!$is_active)
							{
								$response = array(
									'status' => 0,
									'message' => 'Sorry ! Your account is not active.'
								);
							}
							else
							{
									$activeService = $this->User->account_active_service($userID);   
									$user_aeps_status = $this->User->get_member_aeps_status($userID);
									$user_new_aeps_status = $this->User->get_member_new_aeps_status($userID);
									$user_icici_aeps_status = $this->User->get_member_instantpay_aeps_status($userID); 

									$is_recharge_active = 0;
									$is_transfer_active = 0;
									$is_bbps_live_active = 0;
									$is_aeps_active = 0;
									$is_new_aeps_active = 0;
									$is_icici_aeps_active = 0;
									$is_aeps_payout_active = 0;
									$is_upi_collection_active = 0;
									$is_upi_cash_active = 0;
									if(in_array(1, $activeService)){
										$is_recharge_active = 1;
									}
									if(in_array(6, $activeService)){
										$is_transfer_active = 1;
									}
									if(in_array(3, $activeService)){
										$is_aeps_active = 1;
									}
									if(in_array(4, $activeService)){
										$is_bbps_live_active = 1;
									}
									if(in_array(2, $activeService)){
										$is_aeps_payout_active = 1;
									}
									if(in_array(5, $activeService)){
										$is_upi_collection_active = 1;
									}
									if(in_array(7, $activeService)){
										$is_upi_cash_active = 1;
									}
									if(in_array(17, $activeService)){
										$is_new_aeps_active = 1;
									}
									
									if(in_array(19, $activeService)){
										$is_icici_aeps_active = 1;
									}


									$activeGateway = $this->User->account_active_gateway();   
									$is_razorypay_active = 0;
									if(in_array(1, $activeGateway)){
										$is_razorypay_active = 1;
									}

									$activeKeyData = $this->User->account_razorpay_key();

									// get news list
									$newsList = $this->db->get_where('website_news',array('account_id'=>$account_id))->result_array(); 
									$news = '';
									if($newsList)
									{
										foreach($newsList as $nkey=>$nlist)
										{
											if($nkey == 0)
											{
												$news.=$nlist['news'];
											}
											else
											{
												$news.=' '.$nlist['news'];
											}
										}
									}

									$sliderList = $this->db->get_where('website_slider',array('account_id'=>$account_id,'is_app'=>1))->result_array();
									$sliderData = array();
									if($sliderList)
									{
										foreach($sliderList as $skey=>$slist)
										{
											$sliderData[$skey]['link'] = $slist['link'];
											$sliderData[$skey]['imageUrl'] = base_url().$slist['image'];
										}
									}

									$today_date = date('Y-m-d');

							        // get total success recharge
							        $get_success_recharge = $this->db->query("SELECT SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_recharge_history as a  INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.account_id = '$account_id' AND a.status = 2 AND DATE(a.created) = '$today_date' AND (b.created_by = '$userID' OR a.member_id = '$userID')")->row_array();
							        $successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
							        
							        // get total success recharge
							        $get_pending_recharge = $this->db->query("SELECT SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_recharge_history as a  INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.account_id = '$account_id' AND a.status = 1 AND DATE(a.created) = '$today_date' AND (b.created_by = '$userID' OR a.member_id = '$userID')")->row_array();
							        $pendingAmount = isset($get_pending_recharge['totalAmount']) ? number_format($get_pending_recharge['totalAmount'],2) : '0.00';

							        // get total success recharge
							        $get_failed_recharge = $this->db->query("SELECT SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_recharge_history as a  INNER JOIN tbl_users as b ON b.id = a.member_id WHERE a.account_id = '$account_id' AND a.status = 3 AND DATE(a.created) = '$today_date' AND (b.created_by = '$userID' OR a.member_id = '$userID')")->row_array();
							        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
							        
							        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();

							        $accountDetail = $this->db->get_where('website_account_detail',array('account_id'=>$account_id))->row_array();


							        $upi_qr_status = $this->db->get_where('users',array('is_upi_qr_active'=>1,'id'=>$userID))->num_rows();

							        $cash_qr_status = $this->db->get_where('users',array('is_upi_cash_qr_active'=>1,'id'=>$userID))->num_rows();
							        
							        
							        $paysprint_api_detail = $this->db->get_where('account',array('id'=>$account_id))->row_array();
							        
							        $paysprint_key = $paysprint_api_detail['paysprint_aeps_key'];
							        $paysprint_jwt_key = $paysprint_api_detail['paysprint_secret_key'];
							        $paysprint_partner_id = $paysprint_api_detail['paysprint_partner_id'];
							        
							        $nsdl_pan_type = 0;
							        
							        if($account_id == 3)
							        {
							            $nsdl_pan_type = 1;
							        }
							        else
							        {
							             $nsdl_pan_type = 2;
							        }
							        
							         $plain_txt = $get_user_data['id'].'|'.$get_user_data['password'].'|'.$user_ip_address;
								     $token = $this->User->generateAppToken('encrypt', $plain_txt);
								        
								     log_message('debug', 'User Detail API Account ID - '.$account_id.' Token String - '.$plain_txt);	
								     log_message('debug', 'Login Auth API Account ID - '.$account_id.' Token - '.$token);

								     $header_data = apache_request_headers();

									$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
									
									$this->db->where('id',$get_user_data['id']);
									$this->db->update('users',array('device_id'=>$Deviceid));

									 $wallet_balance = $this->User->getMemberWalletBalanceSP($userID);
									 $aeps_wallet_balance = $this->User->getMemberWalletBalanceSP($userID , 2);
									 
									 
									 
									 $get_instantpay_2fa_register_status = $get_user_data['is_2fa_register'];
									 
									 if($get_instantpay_2fa_register_status != 1)
									 
									 {
									     $user_2fa_instantpay_aeps_status = $this->User->get_member_2fa_instantpay_aeps_status($userID);
       
								if(!$user_2fa_instantpay_aeps_status){
									$icici_2fa_register_status = 0;

								}
								else
								{
									$icici_2fa_register_status = 1;
								}
									 
									     
									 }
									 
									 else
									 
									 {
									     $icici_2fa_register_status = 1;
									 }
									 
									 
									 
									 $user_2fa_instantpay_aeps_loginn_status = $this->User->get_member_2fa_instantpay_aeps_login_status($userID);
       								
								if(!$user_2fa_instantpay_aeps_loginn_status){
									
									$icici_2fa_login_status = 0;
								}
								else{

									$icici_2fa_login_status = 1;
								}


                                    	$user_2fa_fino_aeps_status = $this->User->get_member_2fa_aeps_status($userID);
                                    	$user_2fa_fino_aeps_loginn_status = $this->User->get_member_2fa_aeps_login_status($userID);


								    $user_detail = array(
								        'userID' => $get_user_data['id'],
								        'role_id' => $get_user_data['role_id'],
										'user_code' => $get_user_data['user_code'],
										'name' => $get_user_data['name'],
										'email' => $get_user_data['email'],
										'mobile' => $get_user_data['mobile'],
										'wallet_balance'=>$wallet_balance,
										'e_wallet_balance'=>$aeps_wallet_balance,
										'profile_photo' => base_url($get_user_data['photo']),
										'fcm_id'        => $get_user_data['fcm_id'],
										'is_recharge_active' => $is_recharge_active,
										'is_transfer_active' => $is_transfer_active,
										'is_razorypay_active' => $is_razorypay_active,
										'is_bbps_live_active' => $is_bbps_live_active,
										'is_aeps_active' => $is_aeps_active,
										'is_new_aeps_active' => $is_new_aeps_active,
										'is_icici_aeps_active' => $is_icici_aeps_active,
										'is_aeps_payout_active' => $is_aeps_payout_active,
										'is_upi_collection_active' => $is_upi_collection_active,
										'is_upi_cash_active' => $is_upi_cash_active,
										'user_aeps_status' => $user_aeps_status,
										'user_new_aeps_status' => $user_new_aeps_status,
										'user_icici_aeps_status' => $user_icici_aeps_status,
										'razor_pay_key' => $activeKeyData['key'],
										'razor_pay_secret' => $activeKeyData['secret'],
										'news' => $news,
										'upi_qr_status' => $upi_qr_status,
										'cash_qr_status' => $cash_qr_status,
										'sliderData' => $sliderData,
										'successAmount' => $successAmount,
										'pendingAmount' => $pendingAmount,
										'failedAmount' => $failedAmount,
										'user_dob'  => isset($get_user_data['dob']) ? $get_user_data['dob'] : '',
										'user_aadhar_no'  => isset($get_user_data['aadhar']) ? $get_user_data['aadhar'] : '',
										'company_mobile' => isset($contactDetail['mobile']) ? $contactDetail['mobile'] : '',
										'company_email' => isset($contactDetail['email']) ? $contactDetail['email'] : '',
										'company_address' => isset($contactDetail['address']) ? $contactDetail['address'] : '',
										'support_working_time' => isset($contactDetail['support_working_time']) ? $contactDetail['support_working_time'] : '',
										'bank_name' => isset($accountDetail['bank_name']) ? $accountDetail['bank_name'] : '',
										'branch' => isset($accountDetail['branch']) ? $accountDetail['branch'] : '',
										'account_holder_name' => isset($accountDetail['account_holder_name']) ? $accountDetail['account_holder_name'] : '',
										'account_no' => isset($accountDetail['account_no']) ? $accountDetail['account_no'] : '',
										'ifsc' => isset($accountDetail['ifsc']) ? $accountDetail['ifsc'] : '',
										'phonepe_number' => isset($accountDetail['phonepe']) ? $accountDetail['phonepe'] : '',
										'google_pay_number' => isset($accountDetail['google_pay']) ? $accountDetail['google_pay'] : '',
										'login_user_txnpass' => $get_user_data['decoded_transaction_password'],
										'paysprint_key' =>$paysprint_key,
										'paysprint_partner_id' =>$paysprint_partner_id,
										'paysprint_jwt_key' =>$paysprint_jwt_key,
										'nsdl_pan_type' =>$nsdl_pan_type,
										'token' =>$token,
										'outlet_id' => $get_user_data['instantpay_outlet_id'],
										'icici_2fa_login_status'=>$icici_2fa_login_status,
										'icici_2fa_register_status' =>$icici_2fa_register_status,
										'user_2fa_fino_aeps_status'=>$user_2fa_fino_aeps_status,
										'user_2fa_fino_aeps_loginn_status'=>$user_2fa_fino_aeps_loginn_status
								        );
								    
									$response = array(
										'status' => 1,
										'message' => 'Success',
										'user_detail'=>$user_detail
										
									);
							}
						}
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
			}
		}
		log_message('debug', 'User Detail API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }



    public function topupWalletAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Topup Wallet Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Topup Wallet API Response Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			$this->form_validation->set_rules('user_id', 'User Id', 'required|xss_clean');
			$this->form_validation->set_rules('amount', 'Amount', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please provide required fields.'
				);
			}
			else
			{
				$userID = $post['user_id'];
				$amount = $post['amount'];
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Topup Wallet Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Topup Wallet Check User Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
			            // check user credential
						$chk_user_credential =$this->db->query("SELECT * FROM tbl_users WHERE account_id='$account_id' AND id = '$userID'")->row_array();
						
						if(!$chk_user_credential)
			            {
							$response = array(
								'status' => 0,
								'message' => 'User Id Not Valid'
							);
			                
			            }
						else
						{	
							
							$activeKeyData = $this->User->account_razorpay_key();
							$keyId = $activeKeyData['key'];
							$keySecret = $activeKeyData['secret'];

							$amount = $post['amount'];


							$request_id = rand(1111,9999).time();
							$api = new Api($keyId, $keySecret);
							$orderData = [
								'receipt'         => $request_id,
								'amount'          => $amount * 100, // 2000 rupees in paise
								'currency'        => 'INR',
								'payment_capture' => 1 // auto capture
							];

							$razorpayOrder = $api->order->create($orderData);
							$order_id = $razorpayOrder['id'];

							

					        // get member data
					        $userData = $this->db->select('name,email,mobile')->get_where('users',array('id'=>$userID))->row_array();
					        
					        $client_name = $userData['name'];
							$client_email = $userData['email'];
							$client_mobile = $userData['mobile'];

							$accountData = $this->User->get_account_data($account_id);
							$account_name = $accountData['title'];

							$commisionData = $this->User->get_gateway_charge($amount,$userID);
				        	$com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
				        	$is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

							$surcharge_amount = 0;
							if($is_surcharge)
							{
								$surcharge_amount = $com_amount;
							}
							$wallet_settlement_amount = $amount - $surcharge_amount;

							$tokenData = array(
					            'account_id' => $account_id,
					            'member_id' => $userID,
					            'request_id' => $request_id,
					            'request_amount' => $amount,
					            'charge_amount' => $surcharge_amount,
					            'wallet_settlement_amount' => $wallet_settlement_amount,
					            'status' => 1,
					            'created' => date('Y-m-d H:i:s'),
					            'created_by' => $userID
					        );
					        $this->db->insert('member_gateway_history',$tokenData);

				            $response = array(
								'status' => 1,
								'message' => 'Congratulations ! transaction proceeded successfully.',
								'client_name' => $client_name,
								'client_email' => $client_email,
								'client_mobile' => $client_mobile,
								'account_name' => $account_name,
								'com_amount' => $com_amount,
								'is_surcharge' => $is_surcharge,
								'surcharge_amount' => $surcharge_amount,
								'wallet_settlement_amount' => $wallet_settlement_amount,
								'razorpay_order_id' => $order_id,
							);
						}
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
			}
		}
		log_message('debug', 'Topup Wallet API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }



    public function getPgHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get PG History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'Get PG History API Get Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get PG History API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get PG History Check  Decrypt Token - '.json_encode($chk_user_token));


			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
			    	$response = array();
			    	$fromDate = $post['fromDate'];
			        $toDate = $post['toDate'];

			        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
			    	$limit = $page_no * 50;
			        // check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_gateway_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$userID'";

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
								
								$data[$key]['user_code'] = $list['user_code'];
								$data[$key]['name'] = $list['name'];
								$data[$key]['request_id'] = $list['request_id'];
								$data[$key]['gateway_txn_id'] = $list['gateway_txn_id'];
								$data[$key]['request_amount'] = $list['request_amount'];
								$data[$key]['charge_amount'] = $list['charge_amount'];
								$data[$key]['wallet_settlement_amount'] = $list['wallet_settlement_amount'];
								if($list['status'] == 1) {
								  $data[$key]['status'] = 'Not Confirm';
								}
								elseif($list['status'] == 2) {
									$data[$key]['status'] = 'Success';
								}
								elseif($list['status'] == 3) {
									$data[$key]['status'] = 'Failed';
								}
								elseif($list['status'] == 4) {
									$data[$key]['status'] = 'Refund';
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
								'pages' => $pages
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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'getPgHistory API Response - '.json_encode($response));	
		echo json_encode($response);
    }

    public function getElectricityOperatorForm()
	{
		$post = $this->input->post();
		log_message('debug', 'Electricity Biller Form API Post Data - '.json_encode($post));	
		$operator_id = isset($post['code']) ? $post['code'] : '';

		$get_operator_code = $this->db->get_where('operator',array('id'=>$operator_id))->row_array();
        $operator_code = isset($get_operator_code['operator_code']) ? $get_operator_code['operator_code'] : '';

		$response = $this->User->getElectricityOperatorDetail($operator_code);
		log_message('debug', 'Electricity Biller Form API Response - '.json_encode($response));	
		echo json_encode($response);
	}

	public function getElectricityBillerDetail()
	{
		$post = $this->input->post();
		log_message('debug', 'Electricity Biller Detail API Post Data - '.json_encode($post));	
		$account_number = isset($post['account_number']) ? $post['account_number'] : '';
		$userID = isset($post['userID']) ? $post['userID'] : 0;

		$operator_id = isset($post['code']) ? $post['code'] : '';

		$get_operator_code = $this->db->get_where('operator',array('id'=>$operator_id))->row_array();
        $operator_code = isset($get_operator_code['operator_code']) ? $get_operator_code['operator_code'] : '';

		$response = $this->User->getElectricityOperatorBillerDetail($operator_code,$account_number,$userID);
		log_message('debug', 'Electricity Biller Detail API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function electricityAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Electricity Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Electricity Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('operator', 'Operator', 'required');
	        $this->form_validation->set_rules('number', 'Account Number', 'required');
	        $this->form_validation->set_rules('customer_name', 'Customer Number', 'required');
	        $this->form_validation->set_rules('reference_id', 'Reference ID', 'required');
			$this->form_validation->set_rules('amount', 'Amount', 'required|numeric');

			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please Enter Required Field.'
				);
			}
			else
			{
			  	$loggedAccountID = $post['user_id'];
			  	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Electricity Auth Check  Decrypt Token - '.json_encode($chk_user_token));


				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
						 $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

					  	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
					  	$member_code = $chk_wallet_balance['user_code'];
					  	$mobile = $chk_wallet_balance['mobile'];
					  	
					  	$min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
			            $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

					  	log_message('debug', 'Electricity Auth API Member Code - '.$member_code.' Wallet Balance - '.$chk_wallet_balance['wallet_balance']);	
					  	if($user_before_balance < $final_deduct_wallet_balance){
					  		$response = array(
								'status' => 0,
								'message' => 'Sorry ! Insufficient balance in your account.'
							);
					  	}
					  	else
					  	{
					  		// generate recharge unique id
				            $recharge_unique_id = rand(1111,9999).time();
				            // get operator code
				            $get_operator_code = $this->db->get_where('operator',array('id'=>$post['operator']))->row_array();
				            $operator_code = isset($get_operator_code['operator_code']) ? $get_operator_code['operator_code'] : '';

				            $user_after_balance = $user_before_balance - $post['amount'];

				            $data = array(
				                'account_id' => $account_id,
				                'member_id'          => $loggedAccountID,
				                'recharge_type'      => 7,
				                'recharge_display_id'=> $recharge_unique_id,
				                'mobile'             => $mobile,
				                'account_number'     => isset($post['number']) ? $post['number'] : '',
								'operator_code'      => $operator_code,
								'amount'             => $post['amount'],
								'before_balance'     => $user_before_balance,
			                	'after_balance'      => $user_after_balance,
								'status'         	 => 1,
								'reference_id'             => $post['reference_id'],
								'customer_name'             => $post['customer_name'],
				                'created'            => date('Y-m-d H:i:s')                  
				            );

				            $this->db->insert('recharge_history',$data);
				            $recharge_id = $this->db->insert_id();

				            log_message('debug', 'Electricity Auth API - Save Electricity Data System Recharge ID - '.$recharge_id);
				            
				            $account_number = $post['number'];
				            $amount = $post['amount'];
				            $reference_id = $post['reference_id'];
				            $customer_mobile = $post['customer_name'];
				            // call recharge API
				            $api_response = $this->User->electricity_rechage_api($account_number,$operator_code,$amount,$reference_id,$recharge_unique_id,$loggedAccountID,$mobile,$customer_mobile);
				            

				            if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
				            {
				                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

				                $after_balance = $user_before_balance - $post['amount'];    

				                $wallet_data = array(
				                    'account_id' => $account_id,
				                    'member_id'           => $loggedAccountID,    
				                    'before_balance'      => $user_before_balance,
				                    'amount'              => $post['amount'],  
				                    'after_balance'       => $after_balance,      
				                    'status'              => 1,
				                    'type'                => 2,     
				                    'wallet_type'         => 2,       
				                    'created'             => date('Y-m-d H:i:s'),      
				                    'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Deducted.'
				                );

				                $this->db->insert('member_wallet',$wallet_data);

				                log_message('debug', 'Electricity Auth API - Member Wallet Updated.');

				                if($api_response['status'] == 1){
				                    // update recharge status
				                    $this->db->where('id',$recharge_id);
				                    $this->db->where('recharge_display_id',$recharge_unique_id);
				                    $this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
				                    $response = array(
											'status' => 1,
											'message' => 'Your bill payment is pending. Status will be updated soon.'
										);
				                }
				                else
				                {
				                    // update recharge status
				                    $this->db->where('id',$recharge_id);
				                    $this->db->where('recharge_display_id',$recharge_unique_id);
				                    $this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
				                    
				                    
				                    // distribute commision
				                    $this->User->distribute_electricity_commision($recharge_id,$recharge_unique_id,$post['amount'],$loggedAccountID);

				                    $message = 'Congratulations!! Your bill payment successfully credited.';

					        		//$this->User->sendNotification($loggedAccountID,'Electricity Bill',$message);
				                    
				                    $response = array(
											'status' => 1,
											'message' => 'Congratulations ! Your bill payment successfully credited.'
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
											'status' => 1,
											'message' => 'Sorry ! Your bill payment failed from operator side.'
										);
				            }
							
						}
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
			
		    }
		}
	    log_message('debug', 'Electricity Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function updateUserData(){
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Update User Data API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();

	    	log_message('debug', 'Update User Data API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Bad Request. Check URI and its signatures in the request'
				);
			}
			else
			{
				$userID = $post['userID'];
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Update User Data API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Update User Check  Decrypt Token - '.json_encode($chk_user_token));


				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['login'] == 1 )
					{
						$siteUrl = base_url();
						// check user valid or not
						$chk_user = $this->db->get_where('users',array('id'=>$userID,'account_id'=>$account_id))->num_rows();
						if($chk_user)
						{
							// update user data
							$updateData = array(
								'name' => $post['name'],
								'aadhar' => isset($post['user_aadhar_no']) ? $post['user_aadhar_no'] : '',
								'dob'    => isset($post['user_dob']) ? $post['user_dob'] : ''
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
								$profile_img_name = PROFILE_PHOTO_SERVER_PATH.$file_name;
			                    $path = 'media/profile/';
			                    if (!is_dir($path)) {
			                        mkdir($path, 0777, true);
			                    }
			                    $targetDir = $path.$file_name;
			                    if(file_put_contents($targetDir, $profile)){
			                        $updateData['photo'] = $targetDir;
			                    }
							}
							$this->db->where('id',$userID);
							$this->db->where('account_id',$account_id);
							$this->db->update('users',$updateData);
							$userData = $this->db->get_where('users',array('id'=>$userID))->row_array();
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => array(
									'name' => $userData['name'],
									'email' => $userData['email'],
									'mobile' => $userData['mobile'],
									'photo' => !empty($userData['photo']) ? base_url($userData['photo']) : '',
									'user_dob'  => isset($userData['dob']) ? $userData['dob'] : '',
									'user_aadhar_no'  => isset($userData['aadhar']) ? $userData['aadhar'] : ''
								)
							);
							
						}
						else
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry ! User not registered or valid.'
							);
						}
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
			}
		}
		log_message('debug', 'Update User Data API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

    public function requestwalletamount(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Request Wallet Amount API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Fund Request API Response Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			$this->form_validation->set_rules('user_id', 'User Id', 'required|xss_clean');
			$this->form_validation->set_rules('amount', 'Amount', 'required|xss_clean');
			$this->form_validation->set_rules('transid', 'Txn ID', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'User Id Not Valid'
				);
			}
			else
			{
				$loggedAccountID = $post['user_id'];
	            $amount = $post['amount'];
	            $transid = $post['transid'];
	        	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Request Wallet Amount Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
				        // generate request id
				        $request_id = time().rand(111,333);
				        
				        
				        $tokenData = array(
				            'account_id' => $account_id,
				            'request_id' => $request_id,
				            'member_id' => $loggedAccountID,
				            'request_amount' => $amount,
				            'txnid' => $transid,
				            'status' => 1,
				            'created' => date('Y-m-d H:i:s'),
				        );
				        $this->db->insert('member_fund_request',$tokenData);

				        $response = array(
							'status' => 1,
							'message' => 'Request submitted successfully.',
						);

						$message = 'Request submitted successfully.';
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

			    //$this->User->sendNotification($loggedAccountID,'Fund Request',$message);
				
			}
		}
		log_message('debug', 'Fund Request API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }


    public function getRequestHistory(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Request History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get Request History API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Fund Request Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] ==1 && $chk_user_token['is_login'] == 1)
				{
					// get users list
					$sql = "SELECT a.* FROM tbl_member_fund_request as a where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
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

					$userList = $this->db->query($sql)->result_array();

					$pages = ceil($count / 50);
					
					$data = array();
					if($userList)
					{
						foreach($userList as $key=>$list)
						{
							$data[$key]['request_id'] = $list['request_id'];
							$data[$key]['txnid'] = $list['txnid'];
							$data[$key]['request_amount'] = $list['request_amount'];
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
							$data[$key]['datetime'] = date('d-M-Y H:i:s',strtotime($list['created']));
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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Get Fund Request List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function changePassword(){

		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Change Password API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
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
					'message' => 'Please enter required field'
				);
			}
			else
			{
				$userID = $post['userID'];
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Get PG History Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
						$chk_old_pwd = $this->db->get_where('users',array('id'=>$post['userID'],'account_id'=>$account_id,'password'=>do_hash($post['opw'])))->num_rows();
			            if($chk_old_pwd)           
			            {
							$data = array(
			                'password' => do_hash($post['npw']),
			                'decode_password' =>$post['npw'],
			                'updated' => date('Y-m-d h:i:s')
			                );
			                
			                $this->db->where('account_id',$account_id);
			                $this->db->where('id',$post['userID']);
			                $this->db->update('users',$data);
							$response = array(
								'status' => 1,
								'message' => 'Password Changed Successfully',
							);

							$message = 'Password Changed Successfully';

					        //$this->User->sendNotification($post['userID'],'Change Password',$message);
							
			            }
			            else
			            {
			            	$response = array(
								'status' => 0,
								'message' => 'old password is not valid'
							);
							
			            }
			        }
			        else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
		        }
		        else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
			}
		}
		log_message('debug', 'Change Password API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

    public function forgotAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'Forgot API Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		//$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('mobile', 'Member ID', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please enter required field'
			);
		}
		else
		{
			$member_code = $post['mobile'];
			$chk_email_mobile = $this->db->query("SELECT * FROM tbl_users WHERE account_id = '$account_id' and (user_code = '$member_code' or mobile = '$member_code')")->num_rows();

            if(!$chk_email_mobile)           
            {
				$response = array(
					'status' => 0,
					'message' => 'Sorry ! Member ID not valid.'
				);
            }
            else
            {
                $get_user_data = $this->db->query("SELECT * FROM tbl_users WHERE account_id = '$account_id' and (user_code = '$member_code' or mobile = '$member_code')")->row_array();
				if($get_user_data['is_active'] == 0)
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry ! Your account not active.'
					);
				}
				else
				{
					$post = array();
					$post['mobile'] = $get_user_data['mobile'];
					$post['userID'] = $get_user_data['id'];
					$otp_code = rand(1111,9999);
			        $encrypt_otp_code = do_hash($otp_code);
			        $mobile = $get_user_data['mobile'];
			  		
			        $api_url = SMS_OTP_SEND_API_URL;

		        	$api_url = str_replace('{AUTHKEY}',$accountData['sms_auth_key'],$api_url);
		            $api_url = str_replace('{TEMPID}',$accountData['sms_template_id'],$api_url);
		            $api_url = str_replace('{MOBILE}',$mobile,$api_url);
		            
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

					// Execute
					$output = curl_exec($curl);

					// Close
					curl_close ($curl);

					$smsLogData = array(
		        	 'account_id' => $account_id,	
		        	 'user_id' => $get_user_data['id'],
		        	 'api_url' => $api_url,
		        	 'api_response' => $output,
		        	 'created' => date('Y-m-d H:i:s'),
		        	 'created_by' => $get_user_data['id']
					);
		        	
		        	$this->db->insert('sms_api_response',$smsLogData);

		        	$otp_code = rand(111111,999999);
		        	$encrypt_otp_code = do_hash($otp_code);

		        	$user_otp = array(

		        	  'account_id' => $account_id,
		        	  'member_id'  => $get_user_data['id'],
		        	  'otp_code'   => $otp_code,
		        	  'encrypt_otp_code' => $encrypt_otp_code,
		        	  'mobile' => $mobile,
		        	  'status' => 0,
		        	  'created' => date('Y-m-d H:i:s')	

		        	);

		        	$this->db->insert('users_otp',$user_otp);


					$response = array(
						'status' => 1,
						'message' => 'We have sent an OTP on your mobile no., please verify.',
						'encrypt_otp_code' => $encrypt_otp_code
					);
					
				}
                         
            }
			
		}
		log_message('debug', 'Forgot API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }


    public function forgotResentOtpAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'forgotResentOtpAuth API Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		//$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('encrypt_otp_code', 'encrypt_otp_code', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please enter required field'
			);
		}
		else
		{	
			
			$chk_email_mobile =$this->db->get_where('users_otp',array('encrypt_otp_code'=>$post['encrypt_otp_code'],'status'=>0,'account_id'=>$account_id))->num_rows();
            if($chk_email_mobile)           
            {
				$get_otp_data =$this->db->get_where('users_otp',array('encrypt_otp_code'=>$post['encrypt_otp_code'],'status'=>0,'account_id'=>$account_id))->row_array();

				$mobile = $get_otp_data['mobile'];
				$member_id = $get_otp_data['member_id'];
				$request = array(
	    		'authkey' => $accountData['sms_auth_key'],
	    		'mobile' => '+91'.$mobile,
	    		'retrytype' => 'text'
		    	);
	        	
	        	$api_url = SMS_OTP_RESEND_API_URL;

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

				// Request Body
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

				// Execute
				$output = curl_exec($curl);

				// Close
				curl_close ($curl);

				$decodeResponse = json_decode($output,true);

				$smsLogData = array(
	        	 'account_id' => $account_id,	
	        	 'user_id' => $member_id,
	        	 'api_url' => $api_url,
	        	 'api_response' => $output,
	        	 'post_data' => json_encode($request),
	        	 'created' => date('Y-m-d H:i:s'),
	        	 'created_by' => $member_id
				);
	        	
	        	$this->db->insert('sms_api_response',$smsLogData);

	        	if(isset($decodeResponse['type']) && $decodeResponse['type'] == 'success')
	    		{

		        	$otp_code = rand(111111,999999);
			        $encrypt_otp_code = do_hash($otp_code);

			        $user_otp = array(

			        	  'account_id' => $account_id,
			        	  'member_id'  => $member_id,
			        	  'otp_code'   => $otp_code,
			        	  'encrypt_otp_code' => $encrypt_otp_code,
			        	  'mobile' => $mobile,
			        	  'status' => 0,
			        	  'created' => date('Y-m-d H:i:s')	

			        );

			        $this->db->insert('users_otp',$user_otp);

		        	$response = array(

		        	  'status' => 1,
		        	  'message'=>'Otp sent to your registered mobile number. Please verify.',	
		        	  'encrypt_otp_code' => $encrypt_otp_code
		        	);
		        }
		        else{

		        	$error = $decodeResponse['message'];
		        	$response = array(
		        	  'status' => 0,
		        	  'message'=>$error,	
		        	  'encrypt_otp_code' => $encrypt_otp_code
		        	);
		        }	

            }
            else
            {
            	$response = array(
					'status' => 0,
					'message' => 'Sorry ! something went wrong.'
				);
				
            }
			
		}
		log_message('debug', 'Forgot API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }


      public function forgotOTPAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'Forgot OTP API Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		//$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('encrypt_otp_code', 'encrypt_otp_code', 'required|xss_clean');
		$this->form_validation->set_rules('otp', 'OTP', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please enter required field'
			);
		}
		else
		{
			$chk_email_mobile =$this->db->get_where('users_otp',array('encrypt_otp_code'=>$post['encrypt_otp_code'],'status'=>0,'account_id'=>$account_id))->num_rows();
            if($chk_email_mobile)           
            {
				$get_otp_data =$this->db->get_where('users_otp',array('encrypt_otp_code'=>$post['encrypt_otp_code'],'status'=>0,'account_id'=>$account_id))->row_array();

				$mobile = $get_otp_data['mobile'];
				$member_id = $get_otp_data['member_id'];
				$request = array(
	    		'authkey' => $accountData['sms_auth_key'],
	    		'mobile' => '+91'.$mobile,
	    		'otp' => $post['otp']
		    	);
	        	
	        	$api_url = SMS_OTP_AUTH_API_URL;

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

				// Request Body
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

				// Execute
				$output = curl_exec($curl);

				// Close
				curl_close ($curl);

				$smsLogData = array(
	        	 'account_id' => $account_id,	
	        	 'user_id' => $member_id,
	        	 'api_url' => $api_url,
	        	 'api_response' => $output,
	        	 'post_data' => json_encode($request),
	        	 'created' => date('Y-m-d H:i:s'),
	        	 'created_by' => $member_id
				);
	        	
	        	$this->db->insert('sms_api_response',$smsLogData);

	        	$decodeResponse = json_decode($output,true);
	        	if(isset($decodeResponse['type']) && $decodeResponse['type'] == 'success')
	        	{
	        		
	        		$this->db->where('id',$get_otp_data['id']);
					$this->db->where('account_id',$account_id);
					$this->db->update('users_otp',array('status'=>1));
					$response = array(
						'status' => 1,
						'message' => 'OTP is verified successfully, Please update your new password.',
						'userID' => $get_otp_data['member_id']
					);
	        	}
	        	else
	        	{	
	        		$response = array(
						'status' => 0,
						'message' => 'OTP is verified failed. Please try again.',
						'encrypt_otp_code' => $get_otp_data['encrypt_otp_code']
					);
	        	}

            }
            else
            {
            	$response = array(
					'status' => 0,
					'message' => 'Sorry ! something went wrong.'
				);
				
            }
			
		}
		log_message('debug', 'Forgot OTP API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

     public function updatePasswordAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Update Password API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
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
					'message' => 'Please enter required field'
				);
			}
			else
			{
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Update Password API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Get PG History Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $post['userID'] && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'])
					{
						$this->db->where('account_id',$account_id);
						$this->db->where('id',$post['userID']);
						$this->db->update('users',array('password'=>do_hash($post['password']),'decode_password'=>$post['password']));
						$response = array(
							'status' => 1,
							'message' => 'Congratulations ! Your New password updated successfully.'
						);
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
			}
		}
		log_message('debug', 'Update Password API Response - '.json_encode($response));	
		echo json_encode($response);
    }

       public function getRechargeHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	$post = $this->input->post();
		log_message('debug', 'Recharge History API Get Data - '.json_encode($post));	
		$userID = isset($post['userID']) ? $post['userID'] : 0;
    	$response = array();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Recharge History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
        	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Recharge History Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
			    	$fromDate = $post['fromDate'];
			        $toDate = $post['toDate'];
			        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
			    	$limit = $page_no * 50;
			        // check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id' AND (b.created_by = '$userID' OR a.member_id = '$userID')) as x WHERE x.id > 0";
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

			            	$sql.=" ORDER BY x.created DESC";

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
								if($list['status'] == 1)
								{
									$data[$key]['status'] = 'Pending';
								}
								elseif($list['status'] == 2)
								{
									$data[$key]['status'] = 'Success';
								}
								elseif($list['status'] == 3)
								{
									$data[$key]['status'] = 'Failed';
								}
								
							}
						}

						if($data)
						{
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
								'pages' => $pages
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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Recharge History API Response - '.json_encode($response));	
		echo json_encode($response);
    }


   public function complainAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Complain Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Complain API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('recharge_id', 'Recharge ID', 'required|xss_clean');
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
	        if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please enter required field'
				);
			}
			else
			{
				$recharge_id = $post['recharge_id'];
				$member_id = $post['userID'];
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Complain Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Complain Auth Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $member_id && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
			            // check recharge is valid or not
			            $chk_recharge = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
			            if(!$chk_recharge)
			            {
							$response = array(
								'status' => 0,
								'message' => 'Sorry ! You are not authorized to access this request.'
							);
			            }
			            else
			            {
			            	// generate ticket id
				            $complain_id = rand(111111,999999).'-'.date('Y').'-'.date('m').'-'.date('d');
				    	    
				            $wallet_data = array(
				                'account_id'          => $account_id,
				                'member_id'           => $member_id,    
				                'complain_id' => $complain_id,
				                'complain_type' => 1,
				                'record_id' => $recharge_id,
				                'description'      => $post['description'],
				                'status'              => 1,
				                'created'             => date('Y-m-d H:i:s'),      
				                'created_by'             => $member_id
				            );

				            $this->db->insert('complain',$wallet_data);

				            $response = array(
								'status' => 1,
								'message' => 'Complain submitted successfully.'
							);

							$message = 'Complain submitted successfully';

					        //$this->User->sendNotification($member_id,'Recharge Complain',$message);
			            }
			        }
			        else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
			    }
			    else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
				
			}
		}
		log_message('debug', 'Complain API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

     public function bbpsComplainAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'BBPS Complain Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Complain API Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			//$this->form_validation->set_data($this->input->get());
			$this->form_validation->set_rules('recharge_id', 'Recharge ID', 'required|xss_clean');
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
	        if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please enter required field'
				);
			}
			else
			{
				$recharge_id = $post['recharge_id'];
				$member_id = $post['userID'];
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'BBPS Compain Auth Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $member_id && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
			            // check recharge is valid or not
			            $chk_recharge = $this->db->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
			            if(!$chk_recharge)
			            {
							$response = array(
								'status' => 0,
								'message' => 'Sorry ! You are not authorized to access this request.'
							);
			            }
			            else
			            {
			            	// generate ticket id
				            $complain_id = rand(111111,999999).'-'.date('Y').'-'.date('m').'-'.date('d');
				    	    
				            $wallet_data = array(
				                'account_id'          => $account_id,
				                'member_id'           => $member_id,    
				                'complain_id' => $complain_id,
				                'complain_type' => 5,
				                'record_id' => $recharge_id,
				                'description'      => $post['description'],
				                'status'              => 1,
				                'created'             => date('Y-m-d H:i:s'),      
				                'created_by'             => $member_id
				            );

				            $this->db->insert('complain',$wallet_data);

				            $response = array(
								'status' => 1,
								'message' => 'Complain submitted successfully.'
							);

							$message = 'Complain submitted successfully';

					        //$this->User->sendNotification($member_id,'BBPS Complain',$message);
			            }
			        }
			        else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
			    }
			    else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
				
			}
		}
		log_message('debug', 'Complain API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }


    public function getComplainHistory(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Complain History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
			// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Complain History API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', ' Complain History Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
					// get users list
					$sql = "SELECT a.*, b.recharge_display_id, b.mobile, b.amount, e.recharge_display_id as bbps_display_id, e.mobile as bbps_mobile, e.amount as bbps_amount, c.title as status_title, d.title as complain_type_title FROM tbl_complain as a LEFT JOIN tbl_recharge_history as b ON b.id = a.record_id LEFT JOIN tbl_bbps_history as e ON e.id = a.record_id INNER JOIN tbl_complain_status as c ON c.id = a.status INNER JOIN tbl_complain_type as d ON d.id = a.complain_type where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
					$userList = $this->db->query($sql)->result_array();
					$data = array();
					if($userList)
					{
						foreach($userList as $key=>$list)
						{
							$data[$key]['complain_type'] = $list['complain_type_title'];
							$data[$key]['complain_id'] = $list['complain_id'];
							if($list['complain_type'] == 1)
							{
								$data[$key]['order_id'] = $list['recharge_display_id'];
								$data[$key]['mobile'] = $list['mobile'];
								$data[$key]['amount'] = $list['amount'];
							}
							else
							{
								$data[$key]['order_id'] = $list['bbps_display_id'];
								$data[$key]['mobile'] = $list['bbps_mobile'];
								$data[$key]['amount'] = $list['bbps_amount'];
							}
							$data[$key]['description'] = $list['description'];
							if($list['status'] == 1)
							{
								$data[$key]['status'] = $list['status_title'];
							}
							elseif($list['status'] == 2)
							{
								$data[$key]['status'] = $list['status_title'];
							}
							elseif($list['status'] == 3)
							{
								$data[$key]['status'] = $list['status_title'];
							}
							$data[$key]['datetime'] = date('d-M-Y H:i:s',strtotime($list['created']));
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
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Get Complain History API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getMemberByMobile(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$mobile = isset($post['mobile']) ? $post['mobile'] : '';

		$userList = array();
		if($mobile)
		{
			// get users list
			$sql = "SELECT id,user_code,name FROM tbl_users WHERE mobile='$mobile' AND account_id = '$account_id'";
			$userList = $this->db->query($sql)->result_array();
		}
		$data = array();
		if($userList)
		{
			foreach($userList as $key=>$list)
			{
				$data[$key]['member_id'] = $list['id'];
				$data[$key]['member_code'] = $list['user_code'];
				$data[$key]['member_name'] = $list['name'];
				
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
				 'message' => 'Sorry ! Mobile no. not exits.',
			);
		}
		
		log_message('debug', 'Get Member by Mobile API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
	public function getUserName(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$mobile = isset($post['user_code']) ? $post['user_code'] : '';

		$userList = array();
		if($mobile)
		{
			// get users list
			$sql = "SELECT id,user_code,name FROM tbl_users WHERE user_code='$mobile' AND account_id = '$account_id'";
			$userList = $this->db->query($sql)->row_array();
		}
		//$data = array();
		if($userList)
		{
		
			$response = array(
				 'status' => 1,
				 'message' => 'Success',
				 'member_name'=>$userList['name']
			);
		}
		else
		{
			$response = array(
				 'status' => 0,
				 'message' => 'Sorry ! Member  not exits.',
			);
		}
		
		log_message('debug', 'Get Member by Mobile API Response - '.json_encode($response));	
		echo json_encode($response);

	}

    public function getBBSPHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get BBPS History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'BBSP History API Get Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'BBPS History API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get BBPS History Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] ==1 && $chk_user_token['is_login'] == 1)
				{
			    	$response = array();
			    	$fromDate = $post['fromDate'];
			        $toDate = $post['toDate'];
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						
						$sql = "SELECT a.*, b.user_code as user_code, b.name as name,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type = 7 AND a.account_id = '$account_id' AND (b.created_by = '$userID' OR a.member_id = '$userID')";
						
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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'BBPS History API Response - '.json_encode($response));	
		echo json_encode($response);
    }


    public function getBBPSLiveHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get BBPS Live History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'BBSP Live History API Get Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'BBPS LIVE API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get BBPS LIVE History Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
			    	$response = array();
			    	$fromDate = $post['fromDate'];
			        $toDate = $post['toDate'];

			    	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
			    	$limit = $page_no * 50;
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						
						$sql = "SELECT a.*,b.name,b.user_code,c.title as service_name FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND (b.created_by = '$userID' OR a.member_id = '$userID')";

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
								'pages' => $pages
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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'BBPS Live History API Response - '.json_encode($response));	
		echo json_encode($response);
    }

     public function getRechargeCommisionHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Recharge Commision History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'Recharge Commision History API POST Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Recharge Commission Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get Recharge Commision Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
			    	$response = array();
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{	

						$sql = "SELECT a.*,b.name as member_name,b.user_code,c.recharge_display_id FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'RECHARGE' AND a.member_id = '$userID'";

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
								
								$data[$key]['order_id'] = $list['recharge_display_id'];
								$data[$key]['member_id'] = $list['user_code'];
								$data[$key]['member_name'] = $list['member_name'];
								$data[$key]['amount'] = $list['commision_amount'];
								$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
								
							}
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
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
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! Member not valid.'
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Recharge Commision History API Response - '.json_encode($response));	
		echo json_encode($response);
    }



     public function getFundTransferCommisionHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'getFundTransferCommisionHistory API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'getFundTransferCommisionHistory API POST Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'getFundTransferCommisionHistory API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get FundTransferCommisionHistory Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1 )
				{
			    	$response = array();
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{	

						$sql = "SELECT a.*,b.name as member_name,b.user_code,c.transaction_id,c.transfer_amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_fund_transfer as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'PAYOUT' AND a.member_id = '$userID'";

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
								
								$data[$key]['order_id'] = $list['transaction_id'];
								$data[$key]['member_id'] = $list['user_code'];
								$data[$key]['member_name'] = $list['member_name'];
								$data[$key]['transfer_amount'] = $list['transfer_amount'];
								$data[$key]['commision_amount'] = $list['commision_amount'];
								if($list['is_surcharge'] == 1)
								{
									$data[$key]['type'] = 'DR';
								}
								else
								{
									$data[$key]['type'] = 'CR';
								}
								$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
								
							}
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
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
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! Member not valid.'
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'getFundTransferCommisionHistory API Response - '.json_encode($response));	
		echo json_encode($response);
    }



    public function getMoneyTransferCommisionHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'getMoneyTransferCommisionHistory API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'getMoneyTransferCommisionHistory API POST Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Money Transfer  Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get Money Transfer Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] ==1)
				{
			    	$response = array();
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{	

						$sql = "SELECT a.*,b.name as member_name,b.user_code,c.transaction_id,c.transfer_amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_money_transfer as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'DMT' AND a.member_id = '$userID'";

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
								
								$data[$key]['order_id'] = $list['transaction_id'];
								$data[$key]['member_id'] = $list['user_code'];
								$data[$key]['member_name'] = $list['member_name'];
								$data[$key]['transfer_amount'] = $list['transfer_amount'];
								$data[$key]['commision_amount'] = $list['commision_amount'];
								if($list['is_surcharge'] == 1)
								{
									$data[$key]['type'] = 'DR';
								}
								else
								{
									$data[$key]['type'] = 'CR';
								}
								$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
								
							}
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
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
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! Member not valid.'
						);
					}
				}
			}
		}
		log_message('debug', 'getMoneyTransferCommisionHistory API Response - '.json_encode($response));	
		echo json_encode($response);
    }




    public function getAepsCommisionHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get AEPS Commision History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'getAepsCommisionHistory API POST Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;

	    	$response = array();
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get Aeps Commision Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{	

						$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnID,c.amount,c.service FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_member_aeps_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'AEPS' AND a.member_id = '$userID'";

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
								
								$data[$key]['user_code'] = $list['user_code'];
								$data[$key]['member_name'] = $list['member_name'];
								$data[$key]['txnID'] = $list['txnID'];
								
								if($list['service'] == 'balinfo')
								{
									$data[$key]['service'] = 'Balance Inquiry';
								}
								elseif($list['service'] == 'ministatement')
								{
									$data[$key]['service'] = 'Mini Statement';
								}
								elseif($list['service'] == 'balwithdraw')
								{
									$data[$key]['service'] = 'Withdrawal';
								}
								elseif($list['service'] == 'aadharpay')
								{
									$data[$key]['service'] = 'Aadhar Pay';
								}

								$data[$key]['amount'] = $list['amount'];
								$data[$key]['commision_amount'] = $list['commision_amount'];

								if($list['is_surcharge'] == 1)
								{
									$data[$key]['type'] = 'DR';
								}
								else
								{
									$data[$key]['type'] = 'CR';
								}
								$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
								
							}
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
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
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! Member not valid.'
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'getAepsCommisionHistory API Response - '.json_encode($response));	
		echo json_encode($response);
    }




     public function getCashDepositeCommisionHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Cash Deposite Commision History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'getCashDepositeCommisionHistory API POST Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;

	    	$response = array();
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get Cash Deposite Commision History API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get Cash Deposite Commision Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{	

						$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_cash_deposite_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'CD' AND a.member_id = '$userID'";

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
								
								$data[$key]['user_code'] = $list['user_code'];
								$data[$key]['member_name'] = $list['member_name'];
								$data[$key]['txnid'] = $list['txnid'];
								$data[$key]['amount'] = $list['amount'];
								$data[$key]['commision_amount'] = $list['commision_amount'];
								
								if($list['is_surcharge'] == 1)
								{
									$data[$key]['type'] = 'DR';
								}
								else
								{
									$data[$key]['type'] = 'CR';
								}
								$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
								
							}
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
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
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! Member not valid.'
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'getCashDepositeCommisionHistory API Response - '.json_encode($response));	
		echo json_encode($response);
    }



     public function getUpiCommisionHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get UPI Commision History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'getUpiCommisionHistory API POST Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;

	    	$response = array();
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Upi Commision API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get Upi Commision Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{	

						$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPI' AND a.member_id = '$userID'";

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
								
								$data[$key]['user_code'] = $list['user_code'];
								$data[$key]['member_name'] = $list['member_name'];
								$data[$key]['txnid'] = $list['txnid'];
								$data[$key]['amount'] = $list['amount'];
								$data[$key]['commision_amount'] = $list['commision_amount'];
								
								if($list['is_surcharge'] == 1)
								{
									$data[$key]['type'] = 'DR';
								}
								else
								{
									$data[$key]['type'] = 'CR';
								}
								$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
								
							}
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
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
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! Member not valid.'
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'getUpiCommisionHistory API Response - '.json_encode($response));	
		echo json_encode($response);
    }




    public function getUpiCashCommisionHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'getUpiCashCommisionHistory API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'getUpiCashCommisionHistory API POST Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;

	    	$response = array();
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Cash Upi API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get Cash Upi Commision Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{	

						$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPICASH' AND a.member_id = '$userID'";

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
								
								$data[$key]['user_code'] = $list['user_code'];
								$data[$key]['member_name'] = $list['member_name'];
								$data[$key]['txnid'] = $list['txnid'];
								$data[$key]['amount'] = $list['amount'];
								$data[$key]['commision_amount'] = $list['commision_amount'];
								
								if($list['is_surcharge'] == 1)
								{
									$data[$key]['type'] = 'DR';
								}
								else
								{
									$data[$key]['type'] = 'CR';
								}
								$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
								
							}
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
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
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! Member not valid.'
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'getUpiCashCommisionHistory API Response - '.json_encode($response));	
		echo json_encode($response);
    }


    public function getROfferList(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'ROffer API Post Data - '.json_encode($post));	
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
            $get_operator_name = $this->db->select('operator_name')->get_where('operator',array('id'=>$op_id))->row_array();
            $operator_name = isset($get_operator_name['operator_name']) ? $get_operator_name['operator_name'] : '';

            $api_url = ROFFER_API_URL;
                
            $headers = [
                'Token: '.$accountData['dmt_token'],
                'Content-Type: application/x-www-form-urlencoded'
            ];

            $api_post_data = array();
            $api_post_data['operatorName'] = $operator_name;
            $api_post_data['Mobile'] = $mobile;
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
                    foreach($records as $tabKey=>$planData)
                    {
                        $planList[$i]['amount'] = $planData['rs'];
                        $planList[$i]['desc'] = $planData['desc'];
                        
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
		log_message('debug', 'Roffer API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

    /*public function getPlanList(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
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
                $get_operator_name = $this->db->select('instantpay_code')->get_where('operator',array('id'=>$op_id))->row_array();
                $operator_name = isset($get_operator_name['instantpay_code']) ? $get_operator_name['instantpay_code'] : '';

                // get circle name
                $get_circle_name = $this->db->select('instantpay_circle_code')->get_where('circle',array('id'=>$circle_id))->row_array();
                $circle_name = isset($get_circle_name['instantpay_circle_code']) ? $get_circle_name['instantpay_circle_code'] : '';

                
                $api_url = INSTANTPAY_VIEW_PLAN_API;

                $request = array(
                    'token' => $accountData['instant_token'],
                    'request' => array(
                        'biller_id' => $operator_name,
                        'circle' => $circle_name
                    )
                );

                $header = array(
                    'content-type: application/json'
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
                
                 
                $bmPIData   = simplexml_load_string($output);
                $jsonResponse = json_encode((array) $bmPIData);

                $decodeResponse = json_decode($jsonResponse,true);

            $planList = array();
            if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
            {
                $records = isset($decodeResponse['data']['item']) ? $decodeResponse['data']['item'] : array();
                if($records)
                {
                    $i = 0;
                    for($k = 0; $k <= 0; $k++)
                    {
                        $planList[$i]['type'] = 'Plan';
                        $typeData = array();
                        $j = 0;
                        if($records)
                        {
                            foreach($records as $planKey=>$planData)
                            {
                            	$typeData[$j]['amount'] = $planData['recharge_value'];
                        		$typeData[$j]['desc'] = $planData['recharge_description'];
                        		$typeData[$j]['validity'] = $planData['recharge_validity'];
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
		log_message('debug', 'View Plan API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }*/
    
    
    public function getPlanList(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
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
            $get_operator_name = $this->db->select('operator_name')->get_where('operator',array('id'=>$op_id))->row_array();
            $operator_name = isset($get_operator_name['operator_name']) ? $get_operator_name['operator_name'] : '';

            if($op_id == 3 || $op_id == 4 || $op_id == 11)
            {
                $operator_name = 'BSNL';
            }

            // get circle name
            $get_circle_name = $this->db->select('circle_name')->get_where('circle',array('id'=>$circle_id))->row_array();
            $circle_name = isset($get_circle_name['circle_name']) ? $get_circle_name['circle_name'] : '';


            $api_url = PLAN_FINDER_API_URL;
                
            $headers = [
                'Token: '.$accountData['dmt_token'],
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
		log_message('debug', 'View Plan API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }
    
    


    public function getOperatorId(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
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
                'Token: '.$accountData['dmt_token'],
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
                    $circle = $records['circle'];
                    // get operator name
                    $get_operator_name = $this->db->select('id')->get_where('operator',array('operator_name'=>$operator))->row_array();
                    $operator_id = isset($get_operator_name['id']) ? $get_operator_name['id'] : '';
                    // get operator name
                    $get_circle_name = $this->db->select('id')->get_where('circle',array('circle_name'=>$circle))->row_array();
                    $circle_id = isset($get_circle_name['id']) ? $get_circle_name['id'] : '';
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

    public function getDTHPlanList(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
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
            $get_operator_name = $this->db->select('offer_code')->get_where('operator',array('id'=>$op_id))->row_array();
            $operator_name = isset($get_operator_name['offer_code']) ? $get_operator_name['offer_code'] : '';

            $api_url = DTH_BILLER_DETAIL_API_URL;
            
            $headers = [
                'Token: '.$accountData['dmt_token'],
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

    public function getDTHRofferList(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
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
                $get_operator_name = $this->db->select('instantpay_code')->get_where('operator',array('id'=>$op_id))->row_array();
                $operator_name = isset($get_operator_name['instantpay_code']) ? $get_operator_name['instantpay_code'] : '';

                $circle_name = 'IN';

                
                $api_url = INSTANTPAY_VIEW_PLAN_API;

                $request = array(
                    'token' => $accountData['instant_token'],
                    'request' => array(
                        'biller_id' => $operator_name,
                        'circle' => $circle_name
                    )
                );

                $header = array(
                    'content-type: application/json'
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
                
                 
                $bmPIData   = simplexml_load_string($output);
                $jsonResponse = json_encode((array) $bmPIData);

                $decodeResponse = json_decode($jsonResponse,true);

            $planList = array();
            if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
            {
                $records = isset($decodeResponse['data']['item']) ? $decodeResponse['data']['item'] : array();
                if($records)
                {
                    $i = 0;
                    foreach($records as $tabKey=>$planData)
                    {
                        
	                    $planList[$i]['amount'] = $planData['recharge_value'];
	                    $planList[$i]['desc'] = $planData['recharge_description'];
	                    $planList[$i]['plan_name'] = $planData['recharge_short_description'];
	                    $planList[$i]['validity'] = $planData['recharge_validity'];
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
		log_message('debug', 'DTH Roffer API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

    public function getRechargeCommisionList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();

		$user_id = isset($post['user_id']) ? $post['user_id'] : 0 ;

		$member_package_id = $this->User->getMemberPackageID($user_id);

		$operatorList = $this->db->get_where('operator',array('type !='=>'Electricity'))->result_array();

		$get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$user_id))->row_array();

		$role_id = isset($get_user_role['role_id']) ? $get_user_role['role_id'] : '';

		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				// get commission
				$get_com_data = $this->db->get_where('recharge_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id,'op_id'=>$list['id']))->row_array();
				
				if($role_id == 3){
				
					$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;
				}
				elseif ($role_id == 4) {

					$operatorList[$key]['commision'] = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['dt_is_flat']) ? $get_com_data['dt_is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['dt_is_surcharge']) ? $get_com_data['dt_is_surcharge'] : 0 ;
				}
				elseif ($role_id == 5) {
					
					$operatorList[$key]['commision'] = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['rt_is_flat']) ? $get_com_data['rt_is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['rt_is_surcharge']) ? $get_com_data['rt_is_surcharge'] : 0 ;
				}
				elseif ($role_id == 8) {
					
					$operatorList[$key]['commision'] = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['user_is_flat']) ? $get_com_data['user_is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['user_is_surcharge']) ? $get_com_data['user_is_surcharge'] : 0 ;
				}
				else{
				
					$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;
				}

			}
		}

		$data = array();
		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				$data[$key]['operator_name'] = $list['operator_name'];
				$data[$key]['operator_code'] = $list['operator_code'];
				$data[$key]['type'] = $list['type'];
				$data[$key]['commision'] = $list['commision'];

				if($list['is_flat'] == 1){

					$data[$key]['is_flat'] = 'Yes';
				}
				else{

					$data[$key]['is_flat'] = 'No';
				}

				if($list['is_surcharge'] == 1){

					$data[$key]['is_surcharge'] = 'Yes';
				}
				else{

					$data[$key]['is_surcharge'] = 'No';
				}
				
			}

			$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		log_message('debug', 'Get Recharge Commision List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getBBPSCommisionList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();

		$user_id = isset($post['user_id']) ? $post['user_id'] : 0 ;

		$member_package_id = $this->User->getMemberPackageID($user_id);

		$operatorList = $this->db->get_where('operator',array('type'=>'Electricity'))->result_array();

		$get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$user_id))->row_array();

		$role_id = isset($get_user_role['role_id']) ? $get_user_role['role_id'] : '';

		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				// get commission
				$get_com_data = $this->db->get_where('recharge_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id,'op_id'=>$list['id']))->row_array();
				
				if($role_id == 3){
				
					$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;
				}
				elseif ($role_id == 4) {

					$operatorList[$key]['commision'] = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['dt_is_flat']) ? $get_com_data['dt_is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['dt_is_surcharge']) ? $get_com_data['dt_is_surcharge'] : 0 ;
				}
				elseif ($role_id == 5) {
					
					$operatorList[$key]['commision'] = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['rt_is_flat']) ? $get_com_data['rt_is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['rt_is_surcharge']) ? $get_com_data['rt_is_surcharge'] : 0 ;
				}
				elseif ($role_id == 8) {
					
					$operatorList[$key]['commision'] = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['user_is_flat']) ? $get_com_data['user_is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['user_is_surcharge']) ? $get_com_data['user_is_surcharge'] : 0 ;
				}
				else{
				
					$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;
				}
			}
		}

		$data = array();
		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				$data[$key]['operator_name'] = $list['operator_name'];
				$data[$key]['operator_code'] = $list['operator_code'];
				$data[$key]['type'] = $list['type'];
				$data[$key]['commision'] = $list['commision'];
				$data[$key]['is_flat'] = $list['is_flat'];
				$data[$key]['is_surcharge'] = $list['is_surcharge'];
			}

			$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		
		log_message('debug', 'Get BBPS Commision List API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function getBBPSLiveCommisionList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();

		$user_id = isset($post['user_id']) ? $post['user_id'] : 0 ;


		$member_package_id = $this->User->getMemberPackageID($user_id);

		$operatorList = $this->db->get_where('bbps_service')->result_array();

		$get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$user_id))->row_array();

		$role_id = isset($get_user_role['role_id']) ? $get_user_role['role_id'] : '';

		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				// get commission
				$get_com_data = $this->db->get_where('bbps_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id,'service_id'=>$list['id']))->row_array();

				if($role_id == 3){
				
					$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;
				}
				elseif ($role_id == 4) {

					$operatorList[$key]['commision'] = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['dt_is_flat']) ? $get_com_data['dt_is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['dt_is_surcharge']) ? $get_com_data['dt_is_surcharge'] : 0 ;
				}
				elseif ($role_id == 5) {
					
					$operatorList[$key]['commision'] = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['rt_is_flat']) ? $get_com_data['rt_is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['rt_is_surcharge']) ? $get_com_data['rt_is_surcharge'] : 0 ;
				}
				elseif ($role_id == 8) {
					
					$operatorList[$key]['commision'] = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['user_is_flat']) ? $get_com_data['user_is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['user_is_surcharge']) ? $get_com_data['user_is_surcharge'] : 0 ;
				}
				else{
				
					$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;
				}
			}
		}

		$data = array();
		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				$data[$key]['operator_name'] = $list['title'];
				$data[$key]['commision'] = $list['commision'];
				if($list['is_flat'] == 1){

					$data[$key]['is_flat'] = 'Yes';
				}
				else{

					$data[$key]['is_flat'] = 'No';
				}

				if($list['is_surcharge'] == 1){

					$data[$key]['is_surcharge'] = 'Yes';
				}
				else{

					$data[$key]['is_surcharge'] = 'No';
				}
			}

			$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		
		log_message('debug', 'Get BBPS Commision List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

	public function getMoneyTransferCommisionList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();

		$user_id = isset($post['user_id']) ? $post['user_id'] : 0 ;

		$member_package_id = $this->User->getMemberPackageID($user_id);

		$get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$user_id))->row_array();

		$role_id = isset($get_user_role['role_id']) ? $get_user_role['role_id'] : '';


		$recordList = $this->db->get_where('dmr_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		$data = array();
		if($recordList)
		{
			foreach($recordList as $key=>$list)
			{
				$data[$key]['start_range'] = $list['start_range'];
				$data[$key]['end_range'] = $list['end_range'];
				if($role_id == 3){

					$data[$key]['surcharge'] = $list['md_charge'];	
				}
				elseif($role_id == 4){

					$data[$key]['surcharge'] = $list['dt_charge'];
				}
				elseif($role_id == 5){

					$data[$key]['surcharge'] = $list['rt_charge'];
				}
				elseif($role_id == 8){

					$data[$key]['surcharge'] = $list['user_charge'];
				}
				
				$data[$key]['is_flat'] = isset($list['is_flat']) ? 'Yes' : 'No' ;

				if($list['com_type'] == "RGS"){

					$data[$key]['com_type'] = 'NEFT';
				}
				elseif($list['com_type'] == "RTG"){

					$data[$key]['com_type'] = 'RTGS';
				}
				elseif($list['com_type'] == "IFS"){

					$data[$key]['com_type'] = 'IMPS';
				}
				else{

					$data[$key]['com_type'] = 'Not Available';
				}
			}
			$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		log_message('debug', 'Get Money Transfer Commision List API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function getMyMoneyTransferCommisionList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();

		$user_id = isset($post['user_id']) ? $post['user_id'] : 0 ;

		$member_package_id = $this->User->getMemberPackageID($user_id);

		$get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$user_id))->row_array();

		$role_id = isset($get_user_role['role_id']) ? $get_user_role['role_id'] : '';


		$recordList = $this->db->get_where('money_transfer_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		$data = array();
		if($recordList)
		{
			foreach($recordList as $key=>$list)
			{
				$data[$key]['start_range'] = $list['start_range'];
				$data[$key]['end_range'] = $list['end_range'];
				if($role_id == 3){

					$data[$key]['surcharge'] = $list['md_charge'];	
				}
				elseif($role_id == 4){

					$data[$key]['surcharge'] = $list['dt_charge'];
				}
				elseif($role_id == 5){

					$data[$key]['surcharge'] = $list['rt_charge'];
				}
				elseif($role_id == 8){

					$data[$key]['surcharge'] = $list['user_charge'];
				}
				
				$data[$key]['is_flat'] = isset($list['is_flat']) ? 'Yes' : 'No' ;
			}
			$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		log_message('debug', 'Get Money Transfer Commision List API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function getAEPSCommisionList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();

		$user_id = isset($post['user_id']) ? $post['user_id'] : 0 ;

		$member_package_id = $this->User->getMemberPackageID($user_id);

		$get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$user_id))->row_array();

		$role_id = isset($get_user_role['role_id']) ? $get_user_role['role_id'] : '';

		$recordList = $this->db->get_where('aeps_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		$data = array();
		if($recordList)
		{
			foreach($recordList as $key=>$list)
			{
				$data[$key]['start_range'] = $list['start_range'];
				$data[$key]['end_range'] = $list['end_range'];

				if($role_id == 3){

					$data[$key]['commission'] = $list['md_commision'];
				}
				elseif($role_id == 4){

					$data[$key]['commission'] = $list['dt_commision'];
				}
				elseif($role_id == 5){

					$data[$key]['commission'] = $list['rt_commision'];
				}
				elseif($role_id == 8){

					$data[$key]['commission'] = $list['user_commision'];
				}	
				
				if($list['com_type'] == 1){
                    $data[$key]['com_type'] = 'Account Withdrawal';
                }
                elseif($list['com_type'] == 2){
                    $data[$key]['com_type'] = 'Mini Statement';
                }
                elseif($list['com_type'] == 3){
                    $data[$key]['com_type'] = 'Aadhar Pay';
                }
                elseif($list['com_type'] == 4){
                    $data[$key]['com_type'] = 'Cash Deposite';
                }
                elseif($list['com_type'] == 5){
                    $data[$key]['com_type'] = 'MATM';
                }

                if($list['is_flat'] == 1){

                	$data[$key]['is_flat'] = 'Yes';
                }
                else{

                	$data[$key]['is_flat'] = 'No';
                }

                if($list['is_surcharge'] == 1){

                	$data[$key]['is_surcharge'] = 'Yes';
                }
                else{

                	$data[$key]['is_surcharge'] = 'No';
                }

				
			}
			$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		log_message('debug', 'Get AEPS Commision List API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function getUpiCommisionList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();

		$user_id = isset($post['user_id']) ? $post['user_id'] : 0 ;

		$member_package_id = $this->User->getMemberPackageID($user_id);

		$get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$user_id))->row_array();

		$role_id = isset($get_user_role['role_id']) ? $get_user_role['role_id'] : '';

		$recordList = $this->db->get_where('upi_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		$data = array();
		if($recordList)
		{
			foreach($recordList as $key=>$list)
			{
				$data[$key]['service'] = 'UPI';
				if($role_id == 3){

					$data[$key]['commission'] = $list['md_commision'];
				}
				elseif($role_id == 4){

					$data[$key]['commission'] = $list['dt_commision'];
				}
				elseif($role_id == 5){

					$data[$key]['commission'] = $list['rt_commision'];
				}
				elseif($role_id == 8){

					$data[$key]['commission'] = $list['user_commision'];
				}	
				
				if($list['is_flat'] == 1){

                	$data[$key]['is_flat'] = 'Yes';
                }
                else{

                	$data[$key]['is_flat'] = 'No';
                }

                if($list['is_surcharge'] == 1){

                	$data[$key]['is_surcharge'] = 'Yes';
                }
                else{

                	$data[$key]['is_surcharge'] = 'No';
                }

				
			}
			$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		log_message('debug', 'Get getUpiCommisionList List API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function getUpiCashCommisionList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();

		$user_id = isset($post['user_id']) ? $post['user_id'] : 0 ;

		$member_package_id = $this->User->getMemberPackageID($user_id);

		$get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$user_id))->row_array();

		$role_id = isset($get_user_role['role_id']) ? $get_user_role['role_id'] : '';

		$recordList = $this->db->get_where('upi_cash_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		$data = array();
		if($recordList)
		{
			foreach($recordList as $key=>$list)
			{
				$data[$key]['service'] = 'UPI';
				if($role_id == 3){

					$data[$key]['commission'] = $list['md_commision'];
				}
				elseif($role_id == 4){

					$data[$key]['commission'] = $list['dt_commision'];
				}
				elseif($role_id == 5){

					$data[$key]['commission'] = $list['rt_commision'];
				}
				elseif($role_id == 8){

					$data[$key]['commission'] = $list['user_commision'];
				}	
				
				if($list['is_flat'] == 1){

                	$data[$key]['is_flat'] = 'Yes';
                }
                else{

                	$data[$key]['is_flat'] = 'No';
                }

                if($list['is_surcharge'] == 1){

                	$data[$key]['is_surcharge'] = 'Yes';
                }
                else{

                	$data[$key]['is_surcharge'] = 'No';
                }

				
			}
			$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>$data
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		log_message('debug', 'Get getUpiCashCommisionList List API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function getPrivacyContent(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$contactData = $this->db->get_where('page_content',array('account_id'=>$account_id,'page_id'=>1))->row_array();

		$response = array(
			 'status' => 1,
			 'message' => 'Success',
			 'data'=>isset($contactData['description']) ? $contactData['description'] : ''
		);
		echo json_encode($response);

	}
	

	public function getBbpsServiceList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$countryList = $this->db->order_by('title','asc')->get_where('bbps_service')->result_array();
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

	public function getBbpsElectricityOperator()
	{
		log_message('debug', 'Get BBPS Electricity Operator List API.');	
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();

        $accountData = $this->User->get_account_data($domain_account_id);

        // get electricity biller list
        $electricityBillerList = $this->User->get_bbps_biller_list(4);

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

    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();

        $accountData = $this->User->get_account_data($domain_account_id);

        $biller_id = isset($get['biller_id']) ? $get['biller_id'] : '';

        // get biller system id
        $get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
        $billerID = isset($get_biller_id['id']) ? $get_biller_id['id'] : 0 ;

        if($billerID)
        {
	        // get biller params
	        $billerParams = $this->User->get_bbps_biller_param($service_id,$billerID);

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
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $user_ip_address = $this->User->get_user_ip();
        // get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get BBPS Electricity Fetch Bill API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	        $accountData = $this->User->get_account_data($domain_account_id);
	        //check for foem validation
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
	        	$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
	        	$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
	        	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'BBPS Electricity API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Get BBPS Electricity Fetch Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
			        	// check user valid or not
			        	$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$domain_account_id))->num_rows();
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
						        $get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
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
						        
						          
						        $bill_fetch_respone = $this->User->call_bbps_electricity_bill_fetch_api($loggedAccountID,$biller_payu_id,$pmr_service_id,$postData);
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
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}


	        }
	    }
	    
		echo json_encode($response,JSON_NUMERIC_CHECK);
		
	}

	function maximumCheck($num)
    {
    	$this->load->library('form_validation');
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

	public function electricityBillPayAuth()
    {
    	// 400 - Means Variable realted error
    	// 401 - Variable Data not valid
    	// 200 - Success
    	// response type 1 = JSON, 2 = XML
    	// save system log
        log_message('debug', 'Get BBPS Electricity Pay Bill API Called.');
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $user_ip_address = $this->User->get_user_ip();
        // get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get BBPS Electricity Pay Bill  API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	        $accountData = $this->User->get_account_data($domain_account_id);
	        //check for foem validation
	        $post = $this->input->post();
	        log_message('debug', 'Get BBPS Electricity Pay Bill API Post Data - '.json_encode($post).'.');
	        
			
	    	$this->load->library('form_validation');
	    	$this->form_validation->set_rules('user_id', 'User ID', 'required');
			$this->form_validation->set_rules('biller_id', 'Biller ID', 'required');
	        $this->form_validation->set_rules('para1', 'Params1', 'required');
	        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
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
	        	$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
	        	$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
	        	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Electricity Bill Pay API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Get BBPS Electricity Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
			        	// check user valid or not
			        	$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$domain_account_id))->num_rows();
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
								$getAccountData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$domain_account_id))->row_array();
								$before_wallet_balance =  $this->User->getMemberWalletBalanceSP($loggedAccountID);
								$min_wallet_balance = $getAccountData['min_wallet_balance'];
								$final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];
								$memberName = $getAccountData['name'];
						        $memberMobile = $getAccountData['mobile'];
						        $memberEmail = $getAccountData['email'];
						        $memberCode = $getAccountData['user_code'];

						        // check instantpay cogent api
				                $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($domain_account_id);
				                $admin_id = $this->User->get_admin_id($domain_account_id);
				                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

					        	if($before_wallet_balance < $final_deduct_wallet_balance){
					        		// save system log
							        log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Balance Error.');
						        	$response = array(
										 'status' => 0,
										 'message' => 'Sorry ! Insufficient balance in your account.'
									);
					        	}
					        	elseif($is_cogent_instantpay_api && $admin_wallet_balance < $post['amount']){
					        		log_message('debug', 'Get BBPS Electricity Pay Bill API Insufficient Admin Balance Error.');
				                    $response = array(
				                        'status' => 0,
				                        'msg' => 'Sorry ! Insufficient balance in admin account.'
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

								    	$message = 'Congratulations!! Your Bill Payment credited successfully.';

					        			//$this->User->sendNotification($loggedAccountID,'Electricity Bill',$message);

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
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}


	        }
	    }
	    log_message('debug', 'Get BBPS Electricity Pay Bill API Final Response - '.json_encode($response).'.');
		echo json_encode($response,JSON_NUMERIC_CHECK);
		
	}

	public function getBbpsFastagOperator()
	{
		log_message('debug', 'Get BBPS Fastag Operator List API.');	
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();

        $accountData = $this->User->get_account_data($domain_account_id);

        // get electricity biller list
        $electricityBillerList = $this->User->get_bbps_biller_list(6);

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

		log_message('debug', 'Get BBPS Fastag Operator List API Response - '.json_encode($response).'.');	

		echo json_encode($response,JSON_NUMERIC_CHECK);

	}


	public function getBbpsFastagFormParams()
	{
		$service_id = 6;
		log_message('debug', 'Get BBPS Fastag Operator Form API Called.');	

        $get = $this->input->post();

        log_message('debug', 'Get BBPS Fastag Operator Form API Get Data - '.json_encode($get).'.');	

    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();

        $accountData = $this->User->get_account_data($domain_account_id);

        $biller_id = isset($get['biller_id']) ? $get['biller_id'] : '';

        // get biller system id
        $get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
        $billerID = isset($get_biller_id['id']) ? $get_biller_id['id'] : 0 ;

        if($billerID)
        {
	        // get biller params
	        $billerParams = $this->User->get_bbps_biller_param($service_id,$billerID);

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
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $user_ip_address = $this->User->get_user_ip();
        // get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get BBPS Fastag Fetch Bill API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {

	        $accountData = $this->User->get_account_data($domain_account_id);
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
	        	$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
	        	$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
	        	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Get BBPS Fastag Fetch Bill API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Get BBPS Fastag Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
			        	// check user valid or not
			        	$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$domain_account_id))->num_rows();
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
					        	$service_id = 6;
						        // get biller system id
						        $get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
						        $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
						        $service_id = isset($get_biller_id['service_id']) ? $get_biller_id['service_id'] : '';

						        // get pmr service id
						        $get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
						        $pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

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
						        
						          
						        $bill_fetch_respone = $this->User->call_bbps_service_bill_fetch_api($loggedAccountID,$biller_payu_id,$pmr_service_id,$postData);
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
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
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
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $user_ip_address = $this->User->get_user_ip();
        // get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Fasteg Bill Pay Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	        $accountData = $this->User->get_account_data($domain_account_id);
	        //check for foem validation
	        $post = $this->input->post();
	        log_message('debug', 'Get BBPS Fastag Pay Bill API Post Data - '.json_encode($post).'.');
	        
			
	    	$this->load->library('form_validation');
	    	$this->form_validation->set_rules('user_id', 'User ID', 'required');
			$this->form_validation->set_rules('biller_id', 'Biller ID', 'required');
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
	        	$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
	        	$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
	        	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Get BBPS Fastag Pay Bill API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Fastag Bill Pay Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1 )
					{
			        	// check user valid or not
			        	$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$domain_account_id))->num_rows();
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
								$getAccountData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$domain_account_id))->row_array();

								$before_wallet_balance =  $this->User->getMemberWalletBalanceSP($loggedAccountID);
								$min_wallet_balance = $getAccountData['min_wallet_balance'];
								$final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];
								$memberName = $getAccountData['name'];
						        $memberMobile = $getAccountData['mobile'];
						        $memberEmail = $getAccountData['email'];
						        $memberCode = $getAccountData['user_code'];

						        // check instantpay cogent api
				                $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($domain_account_id);
				                $admin_id = $this->User->get_admin_id($domain_account_id);
				                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

					        	if($before_wallet_balance < $final_deduct_wallet_balance){
					        		// save system log
							        log_message('debug', 'Get BBPS Fastag Pay Bill API Insufficient Balance Error.');
						        	$response = array(
										 'status' => 0,
										 'message' => 'Sorry ! Insufficient balance in your account.'
									);
					        	}
					        	elseif($is_cogent_instantpay_api && $admin_wallet_balance < $post['amount']){
				                    log_message('debug', 'Get BBPS Fastag Pay Bill API Insufficient Admin Balance Error.');
				                    $response = array(
				                        'status' => 0,
				                        'msg' => 'Sorry ! Insufficient balance in admin account.'
				                    );

				                }
					        	else
					        	{
					        		$service_id = 6;
						        	$api_response = $this->Bbps_model->bbpsMasterBillPayAuth($post,$loggedAccountID,$service_id,$memberCode);
						        	// save system log
							        log_message('debug', 'Get BBPS Fastag Pay Bill API Response - '.json_encode($api_response).'.');

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
								    	$message = 'Congratulations!! Your Bill Payment credited successfully.';

					        			//$this->User->sendNotification($loggedAccountID,'Fastag Bill',$message);
								    	
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
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}


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

    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();

        $accountData = $this->User->get_account_data($domain_account_id);

        // get electricity biller list
        $electricityBillerList = $this->User->get_bbps_biller_list($service_id);
       
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

    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();

        $accountData = $this->User->get_account_data($domain_account_id);

        $biller_id = isset($get['biller_id']) ? $get['biller_id'] : '';
        $service_id = isset($get['service_id']) ? $get['service_id'] : 0;

        // get biller system id
        $get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
        $billerID = isset($get_biller_id['id']) ? $get_biller_id['id'] : 0 ;

        if($billerID)
        {
	        // get biller params
	        $billerParams = $this->User->get_bbps_biller_param($service_id,$billerID);

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
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $user_ip_address = $this->User->get_user_ip();
        // get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Service Bill Fetch Bill API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	        $accountData = $this->User->get_account_data($domain_account_id);
	        //check for foem validation
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
	        	$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
	        	$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
	        	$service_id = isset($post['service_id']) ? $post['service_id'] : 0;
	        	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Service Bill Fetch Bill API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Service Bill Fetch Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
			        	// check user valid or not
			        	$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$domain_account_id))->num_rows();
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
						        $get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
						        $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
						        $service_id = isset($get_biller_id['service_id']) ? $get_biller_id['service_id'] : '';

						        // get pmr service id
						        $get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
						        $pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

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
						        
						          
						        $bill_fetch_respone = $this->User->call_bbps_service_bill_fetch_api($loggedAccountID,$biller_payu_id,$pmr_service_id,$postData);
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
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}


	        }
	    }
	    
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
    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $user_ip_address = $this->User->get_user_ip();
        // get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Service Pay Bill API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	        $accountData = $this->User->get_account_data($domain_account_id);
	        //check for foem validation
	        $post = $this->input->post();
	        log_message('debug', 'Service Pay Bill API Post Data - '.json_encode($post).'.');
	        
			
	    	$this->load->library('form_validation');
	    	$this->form_validation->set_rules('user_id', 'User ID', 'required');
	    	$this->form_validation->set_rules('service_id', 'Service ID', 'required');
			$this->form_validation->set_rules('biller_id', 'Biller ID', 'required');
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
	        	$biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
	        	$loggedAccountID = isset($post['user_id']) ? $post['user_id'] : 0;
	        	$service_id = isset($post['service_id']) ? $post['service_id'] : 0;
	        	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Service Pay Bill API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Service Bill Pay Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
			        	// check user valid or not
			        	$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$domain_account_id))->num_rows();
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
								$getAccountData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$domain_account_id))->row_array();
								
								$before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
								$min_wallet_balance = $getAccountData['min_wallet_balance'];
								$final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];
								$memberName = $getAccountData['name'];
						        $memberMobile = $getAccountData['mobile'];
						        $memberEmail = $getAccountData['email'];
						        $memberCode = $getAccountData['user_code'];
					        	if($before_wallet_balance < $final_deduct_wallet_balance){
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
								    	$message = 'Congratulations!! Your Bill Payment credited successfully.';

					        			//$this->User->sendNotification($loggedAccountID,'Bill Payment',$message);

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
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}


	        }
	    }
	    log_message('debug', 'Service Pay Bill API Final Response - '.json_encode($response).'.');
		echo json_encode($response,JSON_NUMERIC_CHECK);
	}



	public function addPayoutBeneficiaryAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'addPayoutBeneficiaryAuth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'addPayoutBeneficiaryAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
	        $this->form_validation->set_rules('bank_id', 'Bank ID', 'required');
	        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
	        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');

			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please all details.'
				);
			}
			else
			{	
				$userID = $post['userID'];
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'addPayoutBeneficiaryAuth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Add Beneficiary Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
						$chk_beneficiary = $this->db->get_where('payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$userID))->row_array();

					  	$activeService = $this->User->account_active_service($post['userID']);
						if(!in_array(2, $activeService)){
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! this service is not active for you.'
							);
						}
						elseif($chk_beneficiary){

							$response = array(
								'status' => 0,
								'message' => 'Sorry!! you can add only one account.'
							);
						}
						else{

							$bene_data = array(
				        	 'account_id' => $account_id,	
				        	 'user_id' => $post['userID'],
				        	 'account_holder_name' => $post['account_holder_name'],
				        	 'bankID' => $post['bank_id'],
				        	 'account_no' => $post['account_number'],
				        	 'ifsc' => $post['ifsc'],
				        	 'encode_ban_id' => do_hash($post['account_number']),	
				        	 'status' => 1,
				        	 'created' => date('Y-m-d H:i:s')

				        	);
				        	
				        	$this->db->insert('payout_user_benificary',$bene_data);

				        	$message = 'Congratulations!! beneficiary added successfully.';

					        //$this->User->sendNotification($post['userID'],'Payout Beneficiary',$message);

				        	$response = array(

				        	 'status'  => 1,
				        	 'message' => 'Congratulations!! beneficiary added successfully.'	

				        	);

				        }
				    }
				    else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
		    }
		}
	    log_message('debug', 'Add Beneficiary Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



		public function payoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'payoutBeneficiaryList API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'payoutBeneficiaryList List API Post Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
	    	$response = array();
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'payoutBeneficiaryList API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'payout Beneficiary List  Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
			    	$activeService = $this->User->account_active_service($post['userID']);
					if(!in_array(2, $activeService)){
						$response = array(
							'status' => 0,
							'message' => 'Sorry!! this service is not active for you.'
						);
					}
					else{

				    	// check user valid or not
						$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
						if($chk_user)
						{
							$benificaryList = $this->db->select('payout_user_benificary.*,aeps_bank_list.bank_name')->join('aeps_bank_list','aeps_bank_list.id = payout_user_benificary.bankID')->get_where('payout_user_benificary',array('payout_user_benificary.account_id'=>$account_id,'payout_user_benificary.user_id'=>$userID))->result_array();

							$data = array();
							if($benificaryList)
							{
								foreach ($benificaryList as $key => $list) {
									
									$data[$key]['beneID'] = $list['id'];
									$data[$key]['account_holder_name'] = $list['account_holder_name'];
									$data[$key]['account_no'] = $list['account_no'];
									$data[$key]['bank_name'] = $list['bank_name'];
									$data[$key]['ifsc'] = $list['ifsc'];
									$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
									
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
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Beneficiary List API Response - '.json_encode($response));	
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
  		$cityList = $this->db->order_by('city_name','asc')->get_where('city',array('state_name'=>$state_name))->result_array();
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

	public function getAepsBankList()
	{
		// get state list
  		$bankList = $this->db->get('aeps_bank_list')->result_array();
  			
  		$data = array();
		if($bankList)
		{
			foreach($bankList as $key=>$list)
			{
				$data[$key]['bank_id'] = $list['id'];
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

	public function aepsActiveAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS Active Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
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
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'AEPS Active Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Get Aeps Commision Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
						// check user credential
						$chk_user_credential =$this->db->get_where('users',array('id'=>$userID,'account_id'=>$account_id))->num_rows();
						if(!$chk_user_credential)
			            {
							$response = array(
								'status' => 0,
								'message' => 'User Id Not Valid'
							);
			                
			            }
						else
						{
							$activeService = $this->User->account_active_service($userID);
							$user_aeps_status = $this->User->get_member_aeps_status($userID);
							if(!in_array(3, $activeService)){

								$response = array(
									'status' => 0,
									'message' => 'Sorry ! This service is not active in your account.'
								);
							}
							else
							{
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
						        	$statusCheckResponse = $this->Aeps_model->checkAepsStatusLive($userID);
									if($statusCheckResponse == true)
									{
										// get member address
										$getAddress = $this->db->select('address,pin_code')->order_by('id','desc')->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$userID))->row_array();
										$address = isset($getAddress['pin_code']) ? $getAddress['pin_code'] : '';

										//get latitute longtitude
								        $googleResponse = $this->User->get_lat_lon($address);
								        $lat = isset($googleResponse['lat']) ? $googleResponse['lat'] : '';
								        $lng = isset($googleResponse['lng']) ? $googleResponse['lng'] : '';

										// update aeps status
						                	$this->db->where('id',$userID);
						                	$this->db->update('users',array('aeps_status'=>1,'aeps_lat'=>$lat,'aeps_lng'=>$lng));
											$response = array(
												'status' => 1,
												'message' => 'EKYC APPROVED ALREADY',
												'encodeFPTxnId' => ''
											);
									}
									else
									{
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
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

			}
		}
		log_message('debug', 'AEPS Active Auth API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

  public function aepsOtpAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS OTP Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
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
				$userID = $post['user_id'];
				$encodeFPTxnId = $post['encodeFPTxnId'];
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'AEPS OTP Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Get Aeps Commision Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] ==1)
					{
						// check user credential
						$chk_user_credential =$this->db->get_where('users',array('id'=>$userID,'account_id'=>$account_id))->num_rows();
						if(!$chk_user_credential)
			            {
							$response = array(
								'status' => 0,
								'message' => 'User Id Not Valid'
							);
			                
			            }
						else
						{
							$activeService = $this->User->account_active_service($userID);
							$user_aeps_status = $this->User->get_member_aeps_status($userID);
							if(!in_array(3, $activeService)){

								$response = array(
									'status' => 0,
									'message' => 'Sorry ! This service is not active in your account.'
								);
							}
							else
							{
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
									$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
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
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

			}
		}
		log_message('debug', 'AEPS OTP Auth API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

      public function aepsResendOtpAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS Resend OTP Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
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
				$userID = $post['user_id'];
				$encodeFPTxnId = $post['encodeFPTxnId'];
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'AEPS Resend OTP Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Get Aeps Commision Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
						// check user credential
						$chk_user_credential =$this->db->get_where('users',array('id'=>$userID,'account_id'=>$account_id))->num_rows();
						if(!$chk_user_credential)
			            {
							$response = array(
								'status' => 0,
								'message' => 'User Id Not Valid'
							);
			                
			            }
						else
						{
							$activeService = $this->User->account_active_service($userID);
							$user_aeps_status = $this->User->get_member_aeps_status($userID);
							if(!in_array(3, $activeService)){

								$response = array(
									'status' => 0,
									'message' => 'Sorry ! This service is not active in your account.'
								);
							}
							else
							{
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
									$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
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
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

			}
		}
		log_message('debug', 'AEPS Resend OTP Auth API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

    public function aepsKycBioAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS KYC Bio Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
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
				$userID = $post['user_id'];
				$encodeFPTxnId = $post['encodeFPTxnId'];
				$biometricData = $post['BiometricData'];
				$iin = '';
				$requestTime = date('Y-m-d H:i:s');
				$txnID = 'FIAK'.time();
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'AEPS KYC Bio Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Get Aeps Commision Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
						// check user credential
						$chk_user_credential =$this->db->get_where('users',array('id'=>$userID,'account_id'=>$account_id))->num_rows();
						if(!$chk_user_credential)
			            {
							$response = array(
								'status' => 0,
								'message' => 'User Id Not Valid'
							);
			                
			            }
						else
						{
							$activeService = $this->User->account_active_service($userID);
							$user_aeps_status = $this->User->get_member_aeps_status($userID);
							if(!in_array(3, $activeService)){

								$response = array(
									'status' => 0,
									'message' => 'Sorry ! This service is not active in your account.'
								);
							}
							else
							{
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
									$chk_encode_id = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->num_rows();
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
										$get_kyc_data = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$userID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->row_array();
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
												'Skey' => $xmlarray['Skey'],
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

											$api_url = AEPS_EKYC_BIOMATRIC_API_URL;

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
							            		'user_id' => $userID,
									        	'api_url' => $api_url,
									        	'api_response' => $output,
									        	'post_data' => $data,
									        	'is_from_app' => 1,
									        	'created' => date('Y-m-d H:i:s'),
									        	'created_by' => $account_id
									        );
									        $this->db->insert('aeps_api_response',$apiData);

									        if(isset($responseData['message']) && $responseData['message'] == 'EKYC Completed Successfully')
									        {
									        	// update aeps status
							                	$this->db->where('id',$memberID);
							                	$this->db->update('users',array('aeps_status'=>1));

							                	// update aeps status
									            $this->db->where('id',$recordID);
									            $this->db->update('aeps_member_kyc',array('status'=>1,'clear_step'=>4));

									            $message = 'Congratulation!! Your EKYC has been approved.';

						        				//$this->User->sendNotification($userID,'Aeps Kyc',$message);

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
									    else
									    {
									    	$response = array(
													'status' => 0,
													'message' => 'Sorry ! Device is not connected, please connect it.'
												);
									    }
									}
								}
							}
						}
					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

			}
		}
		log_message('debug', 'AEPS KYC Bio Auth API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }

    public function aepsApiAuth()
	{	
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS api Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$accountData = $this->User->get_account_data($account_id);
		    //$post = file_get_contents('php://input');
			//$post = json_decode($post, true);
			$request = $_REQUEST['user_data'];
	        $post =  json_decode($request,true);
			
			log_message('debug', 'AEPS api Auth API Post Data - '.json_encode($post));	
			
				$account_id = $this->User->get_domain_account();
			 	$memberID = $post['userID'];
			 	$loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
			 	$member_pin = md5($loggedUser['decoded_transaction_password']);
			 	$member_code = $loggedUser['user_code'];
			 	$lat = ($loggedUser['aeps_lat']) ? $loggedUser['aeps_lat'] : '22.9734229';
	        	$lng = ($loggedUser['aeps_lng']) ? $loggedUser['aeps_lng'] : '78.6568942';
			 	if(!$loggedUser){

			 	   $response = array(
							'status' => 0,
							'message' => 'Sorry ! user not valid.'
						);	
			 	}
			 	else{

					$activeService = $this->User->account_active_service($memberID);
					if(!in_array(3, $activeService)){
						$response = array(
							'status' => 0,
							'message' => 'Sorry ! AEPS Service Not Active.'
						);
					}
					else
					{	

						$agentID = $loggedUser['user_code'];
						$user_aeps_status = $this->User->get_member_aeps_status($memberID);
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
										$txnID = uniqid('BINQ' . time(). rand(1111,9999));
										$txnType = 'BE';
										$remarks = 'Balance Inquiry';
										$is_bal_info = 1;
										$is_withdrawal = 0;
										$Servicestype = 'GetBalanceaeps';
										$api_url = AEPS_BALANCE_INQIRY_API_URL;
										if($serviceType == 'ministatement')
										{
											$txnID = uniqid('MNST' . time(). rand(1111,9999));
											$Servicestype = 'getministatment';
											$is_bal_info = 0;
											$txnType = 'MS';
											$remarks = 'Mini Statement';
											$api_url = AEPS_MINI_STATEMENT_API_URL;
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
													'Skey' => $xmlarray['Skey'],
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
												    "cardnumberORUID" => array(
												    	"nationalBankIdentificationNumber" => $iin,
												    	"indicatorforUID" => "0",
												    	"adhaarNumber" => $aadharNumber
												    ),
												    "captureResponse" => $captureData,
												    "languageCode" => "en",
												    "latitude"=>$lat,
										    		"longitude"=>$lng,
												    "mobileNumber" => $mobile,
												    "paymentType" => "B",
												    "requestRemarks" => $remarks,
												    "timestamp" => date('d/m/Y H:i:s'),
												    "merchantUserName" => $member_code,
												    "merchantPin" => $member_pin,
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
										        	'post_data'=> json_encode($data),
										        	'header_data' => json_encode($header),
										        	'api_response' => $output,
										        	'is_from_app' => 1,
										        	'created' => date('Y-m-d H:i:s'),
										        	'created_by' => $memberID
										        );
										        $this->db->insert('aeps_api_response',$apiData);

										        if(isset($responseData['data']['responseCode']) && $responseData['data']['responseCode'] == '00' && $responseData['data']['bankRRN'] != '')
								        		{
										        	$recordID = $this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$memberID);
										        	$str = '';
										        	if($is_bal_info == 0)
													{
														$this->Aeps_model->addStatementCom($txnID,$aadharNumber,$iin,$amount,$memberID,$recordID);
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
													'message' => 'Sorry ! Device is not connected, please connect it.'
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
										$txnID = uniqid('CSWD' . time(). rand(1111,9999));
										$txnType = 'CW';
										$remarks = 'Withdrawal';
										$api_url = AEPS_WITHDRAWAL_API_URL;
										$is_withdrawal = 1;
										$is_bal_info = 0;
										$Servicestype = 'AccountWithdrowal';
										if($serviceType == 'aadharpay')
										{
											$Servicestype = 'Aadharpay';
											$txnID = uniqid('APAY' . time(). rand(1111,9999));
											$txnType = 'M';
											$remarks = 'Aadharpay';
											$api_url = AEPS_AADHAR_PAY_API_URL;
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
													'Skey' => $xmlarray['Skey'],
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
												    "cardnumberORUID" => array(
												    	"nationalBankIdentificationNumber" => $iin,
												    	"indicatorforUID" => "0",
												    	"adhaarNumber" => $aadharNumber
												    ),
												    "captureResponse" => $captureData,
												    "languageCode" => "en",
												    "latitude"=>$lat,
										    		"longitude"=>$lng,
												    "mobileNumber" => $mobile,
												    "paymentType" => "B",
												    "requestRemarks" => $remarks,
												    "timestamp" => date('d/m/Y H:i:s'),
												    "merchantUserName" => $member_code,
												    "merchantPin" => $member_pin,
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
										        	'post_data'=> json_encode($post),
										        	'api_response' => $output,
										        	'is_from_app' => 1,
										        	'created' => date('Y-m-d H:i:s'),
										        	'created_by' => $memberID
										        );
										        $this->db->insert('aeps_api_response',$apiData);

										        if(isset($responseData['data']['responseCode']) && $responseData['data']['responseCode'] == '00' && $responseData['data']['bankRRN'] != '')
								        		{
										        	$recordID = $this->Aeps_model->saveAepsTxn($txnID,$serviceType,$aadharNumber,$mobile,$amount,$iin,$api_url,$output,$responseData['message'],2,$memberID);
										        	$this->Aeps_model->addBalance($txnID,$aadharNumber,$iin,$amount,$memberID,$recordID,$serviceType);
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
													'message' => 'Sorry ! Device is not connected, please connect it.'
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
						else
						{
							$response = array(
								'status' => 0,
								'message' => 'Sorry ! AEPS not activated.'
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
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'AEPS History API Post Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
	    	$response = array();
	    	$fromDate = isset($post['fromDate']) ? $post['fromDate'] : '';
	        $toDate = isset($post['toDate']) ? $post['toDate'] : '';
	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Get Aeps History Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$userID'";
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

								$data[$key]['invoiceUrl'] = base_url('aepsinvoice/index/'.$list['id'].'');

								$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
								
							}
						}

						if($data)
						{
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
								'pages' => $pages
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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'AEPS History API Response - '.json_encode($response));	
		echo json_encode($response);
    }




    public function addBeneficiaryAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Add Beneficiary Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Add Beneficiary Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
	        $this->form_validation->set_rules('bank_id', 'Bank ID', 'required');
	        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
	        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
	        
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please Enter Username & Password.'
				);
			}
			else
			{	
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Add Beneficiary Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'Add Beneficiary Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $post['userID'] && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
					  	$activeService = $this->User->account_active_service($post['userID']);
						if(!in_array(6, $activeService)){
							$response = array(
								'status' => 0,
								'message' => 'Sorry!! this service is not active for you.'
							);
						}
						else{

							$bene_data = array(
				        	 'account_id' => $account_id,	
				        	 'user_id' => $post['userID'],
				        	 'account_holder_name' => $post['account_holder_name'],
				        	 'bankID' => $post['bank_id'],
				        	 'account_no' => $post['account_number'],
				        	 'ifsc' => $post['ifsc'],
				        	 'encode_ban_id' => do_hash($post['account_number']),
				        	 'status' => 1,
				        	 'created' => date('Y-m-d H:i:s')

				        	);
				        	
				        	$this->db->insert('user_benificary',$bene_data);

				        	$message = 'Congratulations!! beneficiary added successfully.';

					        //$this->User->sendNotification($post['userID'],'Payout Beneficiary',$message);

				        	$response = array(

				        	 'status'  => 1,
				        	 'message' => 'Congratulations!! beneficiary added successfully.'	

				        	);

				        }
				    }
				    else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
		    }
		}
	    log_message('debug', 'Add Beneficiary Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function beneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Beneficiary List API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'Beneficiary List API Post Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
	    	$response = array();
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Beneficiary List API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Beneficiary List Check  Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
			    	$activeService = $this->User->account_active_service($post['userID']);
					if(!in_array(6, $activeService)){
						$response = array(
							'status' => 0,
							'message' => 'Sorry!! this service is not active for you.'
						);
					}
					else{

				    	// check user valid or not
						$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
						if($chk_user)
						{
							$benificaryList = $this->db->select('user_benificary.*,aeps_bank_list.bank_name,user_sender.name as sender_name,user_sender.mobile as sender_mobile')->join('aeps_bank_list','aeps_bank_list.id = user_benificary.bankID')->join('user_sender','user_sender.id = user_benificary.sender_id','left')->get_where('user_benificary',array('user_benificary.account_id'=>$account_id,'user_benificary.user_id'=>$userID))->result_array();

							$data = array();
							if($benificaryList)
							{
								foreach ($benificaryList as $key => $list) {
									
									$data[$key]['beneID'] = $list['id'];
									$data[$key]['sender'] = $list['sender_name'].' ('.$list['sender_mobile'].')';
									$data[$key]['account_holder_name'] = $list['account_holder_name'];
									$data[$key]['account_no'] = $list['account_no'];
									$data[$key]['bank_name'] = $list['bank_name'];
									$data[$key]['ifsc'] = $list['ifsc'];
									$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
									
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
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Beneficiary List API Response - '.json_encode($response));	
		echo json_encode($response);
    }



    public function senderAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'senderAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|min_length[10]');
        $this->form_validation->set_rules('state_id', 'State', 'required');
        $this->form_validation->set_rules('city', 'City', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('pincode', 'Pincode', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(6, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				// check mobile already exits or not
	        	$chk_mobile = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$post['mobile'],'status'=>1))->num_rows();
	        	if($chk_mobile)
	        	{
	        		$response = array(
						'status' => 0,
						'message' => 'Sorry!! sender already exists.'
					);
	        	}
	        	else{

		        	$encodeTxnId = do_hash(time().rand(1111,9999));

		        	$bene_data = array(
		        	 'account_id' => $account_id,	
		        	 'member_id' => $loggedAccountID,
		        	 'encodeTxnId' => $encodeTxnId,
		        	 'name' => $post['name'],
		        	 'mobile' => $post['mobile'],
		        	 'state_id' => $post['state_id'],
		        	 'city' => $post['city'],
		        	 'address' => $post['address'],	
		        	 'pincode' => $post['pincode'],	
		        	 'status' => 0,
		        	 'created' => date('Y-m-d H:i:s'),
		        	 'created_by' => $loggedAccountID
					);
		        	
		        	$this->db->insert('user_sender',$bene_data);

		        	$api_url = SMS_OTP_SEND_API_URL;

		        	$api_url = str_replace('{AUTHKEY}',$accountData['sms_auth_key'],$api_url);
		            $api_url = str_replace('{TEMPID}',$accountData['sms_template_id'],$api_url);
		            $api_url = str_replace('{MOBILE}',$post['mobile'],$api_url);
		            
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

					// Execute
					$output = curl_exec($curl);

					// Close
					curl_close ($curl);

					$smsLogData = array(
		        	 'account_id' => $account_id,	
		        	 'user_id' => $loggedAccountID,
		        	 'api_url' => $api_url,
		        	 'api_response' => $output,
		        	 'created' => date('Y-m-d H:i:s'),
		        	 'created_by' => $loggedAccountID
					);
		        	
		        	$this->db->insert('sms_api_response',$smsLogData);
		        }

	        	$response = array(

	        	 'status'  => 1,
	        	 'message' => 'Otp send to sender mobile number. Please verify.',	
	        	 'encodeTxnId' => $encodeTxnId
	        	);

	        }
	    }
	    log_message('debug', 'senderAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function resendSenderOtpAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'resendSenderOtpAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('encodeTxnId', 'Name', 'required');
       
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(6, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				$encodeTxnId = $post['encodeTxnId'];

				// check mobile already exits or not
		    	$chk_mobile = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->num_rows();
		    	if(!$chk_mobile)
		    	{
		    		$response = array(
						'status' => 0,
						'message' => 'Sorry!! something went wrong.'
					);
		    	}
		    	else{

			    	$getTxnData = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->row_array();
			    	$mobile = isset($getTxnData['mobile']) ? $getTxnData['mobile'] : '';

			    	$request = array(
			    		'authkey' => $accountData['sms_auth_key'],
			    		'mobile' => '+91'.$mobile,
			    		'retrytype' => 'text'
			    	);
			    	
			    	$api_url = SMS_OTP_RESEND_API_URL;

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

					// Request Body
					curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

					// Execute
					$output = curl_exec($curl);

					// Close
					curl_close ($curl);

					$smsLogData = array(
			    	 'account_id' => $account_id,	
			    	 'user_id' => $loggedAccountID,
			    	 'api_url' => $api_url,
			    	 'api_response' => $output,
			    	 'post_data' => json_encode($request),
			    	 'created' => date('Y-m-d H:i:s'),
			    	 'created_by' => $loggedAccountID
					);
			    	
			    	$this->db->insert('sms_api_response',$smsLogData);

			    	$response = array(

			    	 'status' => 1,
			    	 'message'=>'Otp sent to sender mobile number. Please Verify.',
			    	 'encodeTxnId' => $encodeTxnId	

			    	);
			    }

			}

				
	    }
	    log_message('debug', 'resendSenderOtpAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function activeSenderAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'activeSenderAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('encodeTxnId', 'Name', 'required');
       
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(6, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				$encodeTxnId = $post['encodeTxnId'];

				
				// check mobile already exits or not
		    	$chk_mobile = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->num_rows();
		    	if(!$chk_mobile)
		    	{
		    		$response = array(
						'status' => 0,
						'message' => 'Sorry!! something went wrong.'
					);
		    	}
		    	else{

			   		$getTxnData = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->row_array();
			    	$mobile = isset($getTxnData['mobile']) ? $getTxnData['mobile'] : '';

			    	
			    	$api_url = SMS_OTP_SEND_API_URL;

			    	$api_url = str_replace('{AUTHKEY}',$accountData['sms_auth_key'],$api_url);
			        $api_url = str_replace('{TEMPID}',$accountData['sms_template_id'],$api_url);
			        $api_url = str_replace('{MOBILE}',$mobile,$api_url);
			        
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

					// Execute
					$output = curl_exec($curl);

					// Close
					curl_close ($curl);

					$smsLogData = array(
			    	 'account_id' => $account_id,	
			    	 'user_id' => $loggedAccountID,
			    	 'api_url' => $api_url,
			    	 'api_response' => $output,
			    	 'created' => date('Y-m-d H:i:s'),
			    	 'created_by' => $loggedAccountID
					);
			    	
			    	$this->db->insert('sms_api_response',$smsLogData);

			    	$decodeResponse = json_decode($output,true);
			    	if(isset($decodeResponse['type']) && $decodeResponse['type'] == 'success')
			    	{	
			    		$response = array(

			    		  'status' => 1,
			    		  'message'=>'Otp sent to sender mobile. Please verify.',
			    		  'encodeTxnId'	=> $encodeTxnId	
			    		);
			    		
			    	}
			    	else
			    	{
			    		$response = array(
			    		  'status' => 0,
			    		  'message'=>$decodeResponse['message'],
			    		);
			    	}
			    }

			}

				
	    }
	    log_message('debug', 'activeSenderAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function senderOtpAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'senderOtpAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('otp_code', 'otp_code', 'required');
		$this->form_validation->set_rules('encodeTxnId', 'Name', 'required');
       
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(6, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				$encodeTxnId = $post['encodeTxnId'];

				// check mobile already exits or not
		    	$chk_mobile = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->num_rows();
		    	if(!$chk_mobile)
		    	{
		    		$response = array(
						'status' => 0,
						'message' => 'Sorry!! something went wrong.'
					);
		    	}
		    	else{

			    	$getTxnData = $this->db->get_where('user_sender',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'encodeTxnId'=>$encodeTxnId,'status'=>0))->row_array();
			    	$mobile = isset($getTxnData['mobile']) ? $getTxnData['mobile'] : '';

			    	$request = array(
			    		'authkey' => $accountData['sms_auth_key'],
			    		'mobile' => '+91'.$mobile,
			    		'otp' => $post['otp_code']
			    	);
		        	
		        	$api_url = SMS_OTP_AUTH_API_URL;

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

					// Request Body
					curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

					// Execute
					$output = curl_exec($curl);

					// Close
					curl_close ($curl);

					$smsLogData = array(
		        	 'account_id' => $account_id,	
		        	 'user_id' => $loggedAccountID,
		        	 'api_url' => $api_url,
		        	 'api_response' => $output,
		        	 'post_data' => json_encode($request),
		        	 'created' => date('Y-m-d H:i:s'),
		        	 'created_by' => $loggedAccountID
					);
		        	
		        	$this->db->insert('sms_api_response',$smsLogData);

		        	$decodeResponse = json_decode($output,true);
		        	if(isset($decodeResponse['type']) && $decodeResponse['type'] == 'success')
		        	{
		        		$this->db->where('account_id',$account_id);
		        		$this->db->where('member_id',$loggedAccountID);
		        		$this->db->where('encodeTxnId',$encodeTxnId);
		        		$this->db->update('user_sender',array('otp_verify'=>1,'status'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));
						
						$response = array(

						  'status' => 1,
						  'message'=>'Congratulations!! sender otp verified successfully.'	

						);
		        	}
		        	else
		        	{	
		        		$response = array(

						  'status' => 0,
						  'message'=>'Sorry!! otp verified failed.',
						  'encodeTxnId' => $encodeTxnId	

						);
		        		
		        	}
		        }

			}

				
	    }
	    log_message('debug', 'senderOtpAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	


	public function senderList()
    {
    	$account_id = $this->User->get_domain_account();
    	$post = $this->input->post();
		log_message('debug', 'senderList List API Post Data - '.json_encode($post));	
		$userID = isset($post['userID']) ? $post['userID'] : 0;
    	$response = array();
    	
    	$activeService = $this->User->account_active_service($post['userID']);
		if(!in_array(6, $activeService)){
			$response = array(
				'status' => 0,
				'message' => 'Sorry!! this service is not active for you.'
			);
		}
		else{

	    	// check user valid or not
			$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
			if($chk_user)
			{
				$benificaryList = $this->db->select('user_sender.*,states.name as state_name')->join('states','states.id = user_sender.state_id')->get_where('user_sender',array('user_sender.account_id'=>$account_id,'user_sender.member_id'=>$userID))->result_array();

				$data = array();
				if($benificaryList)
				{
					foreach ($benificaryList as $key => $list) {
						
						$data[$key]['senderID'] = $list['id'];
						$data[$key]['name'] = $list['name'];
						$data[$key]['mobile'] = $list['mobile'];
						$data[$key]['state_name'] = $list['state_name'];
						$data[$key]['city'] = $list['city'];
						$data[$key]['address'] = $list['address'];
						$data[$key]['pincode'] = $list['pincode'];
						$data[$key]['pincode'] = $list['pincode'];
						$data[$key]['pincode'] = $list['pincode'];
						if($list['status'] == 1){
						$data[$key]['status'] = 'Active'; 	
						}
						else{
						$data[$key]['status'] = 'Deactive';
						}
						$data[$key]['encodeTxnId'] = $list['encodeTxnId'];
						$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
						
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
		log_message('debug', 'senderList List API Response - '.json_encode($response));	
		echo json_encode($response);
    }


    public function moneyTransferAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'Money Transfer Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
		$this->form_validation->set_rules('beneID', 'beneID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');
		$this->form_validation->set_rules('txnType', 'Transaction Type', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(6, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! Money Transfer not active.'
				);
			}
		  	else{

		  		$loggedAccountID = $post['userID'];

		  		$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

		  		if($post['amount'] > 25000){
		  			$response = array(
							'status' => 0,
							'message' => 'Sorry!! Maximum Transfer Limit is 25 Thousand Only.'
						);
		  		}
		  		else {
			  		$chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['beneID']))->row_array();

					if(!$chk_beneficiary){

						$response = array(
							'status' => 0,
							'message' => 'Sorry!! beneficiary not valid.'
						);
					}	
					else{
					
						$memberID = $loggedUser['user_code'];
						$mobile = $loggedUser['mobile'];
						$account_holder_name = $chk_beneficiary['account_holder_name'];
						$account_no = $chk_beneficiary['account_no'];
						$ifsc = $chk_beneficiary['ifsc'];
						$bankID = $chk_beneficiary['bankID'];
						$amount = $post['amount'];
						$txnType = $post['txnType'];
						$transaction_id = time().rand(1111,9999);
						$receipt_id = rand(111111,999999);


						
						$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
			            
			            // save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Api R-Wallet Balance - '.$chk_wallet_balance['wallet_balance'].'.]'.PHP_EOL;
				        $this->User->generateLog($log_msg);

			            // get dmr surcharge
			            $surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$loggedAccountID);
			            // save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API - Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
			            $this->User->generateLog($log_msg);

			            $before_balance =  $this->User->getMemberWalletBalanceSP($loggedAccountID);
			            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
	        			$final_deduct_wallet_balance = $amount + $surcharge_amount + $min_wallet_balance;  

			            $final_amount = $amount + $surcharge_amount;
			            

			            if($before_balance < $final_deduct_wallet_balance){
			                // save system log
			                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API - Insufficient Wallet Error]'.PHP_EOL;
			                $this->User->generateLog($log_msg);
			                $response = array(
								'status' => 0,
								'message' => 'Sorry!! insufficient balance in your wallet.'
							);
			            } 
			            else{
						
							$after_wallet_balance = $before_balance - $final_amount;    

				            $wallet_data = array(
				                'account_id'          => $account_id,
				                'member_id'           => $loggedAccountID,    
				                'before_balance'      => $before_balance,
				                'amount'              => $final_amount,  
				                'after_balance'       => $after_wallet_balance,      
				                'status'              => 1,
				                'type'                => 2,   
				                'wallet_type'		  => 1,   
				                'created'             => date('Y-m-d H:i:s'),      
				                'description'         => 'Fund Transfer #'.$transaction_id.' Amount Deducted.'
				            );

				            $this->db->insert('member_wallet',$wallet_data);
				            
				            // save system log
				            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
				            $this->User->generateLog($log_msg);

							$data = array(
								'account_id' => $account_id,
								'user_id' => $loggedAccountID,
								'transfer_amount' => $amount,
								'transfer_charge_amount' => $surcharge_amount,
								'total_wallet_charge' => $final_amount,
								'after_wallet_balance' => $after_wallet_balance,
								'txnType' => $txnType,
								'transaction_id' => $transaction_id,
								'encode_transaction_id' => do_hash($transaction_id),
								'status' => 2,
								'wallet_type' => 1,
								'invoice_no' => $receipt_id,
								'memberID' => $memberID,
								'mobile' => $mobile,
								'account_holder_name' => $account_holder_name,
								'account_no' => $account_no,
								'ifsc' => $ifsc,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_money_transfer',$data); 

							$responseData = $this->Api_model->cibMoneyTransfer($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType,$loggedAccountID);

							// save system log
				            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
				            $this->User->generateLog($log_msg);   

				            if(isset($responseData['status']) && $responseData['status'] == 1)
							{
								$response = array(
									'status' => 1,
									'message' => 'Congratulations!! transfered successfully.'
								);
								
							}
							elseif(isset($responseData['status']) && $responseData['status'] == 2)
							{
								$requestID = $responseData['requestID'];
								$rrno = $responseData['rrno'];
								$this->db->where('account_id',$account_id);
								$this->db->where('user_id',$loggedAccountID);
								$this->db->where('transaction_id',$transaction_id);
								$this->db->update('user_money_transfer',array('op_txn_id'=>$requestID,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));
								$response = array(
									'status' => 1,
									'message' => 'Congratulations!! transfered successfully.'
								);
							}
							elseif(isset($responseData['status']) && $responseData['status'] == 3)
							{
								$apiMsg = $responseData['msg'];

								$this->db->where('account_id',$account_id);
								$this->db->where('user_id',$loggedAccountID);
								$this->db->where('transaction_id',$transaction_id);
								$this->db->update('user_money_transfer',array('status'=>4,'updated'=>date('Y-m-d H:i:s')));

								
								$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
								$after_wallet_balance = $before_balance + $final_amount;    

					            $wallet_data = array(
					                'account_id'          => $account_id,
					                'member_id'           => $loggedAccountID,    
					                'before_balance'      => $before_balance,
					                'amount'              => $final_amount,  
					                'after_balance'       => $after_wallet_balance,      
					                'status'              => 1,
					                'type'                => 1,   
					                'wallet_type'		  => 1,   
					                'created'             => date('Y-m-d H:i:s'),      
					                'description'         => 'Fund Transfer #'.$transaction_id.' Amount Refund.'
					            );

					            $this->db->insert('member_wallet',$wallet_data);

					            // save system log
					            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API - Member Wallet Refund - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
					            $this->User->generateLog($log_msg);

					            $response = array(
									'status' => 0,
									'message' => 'Sorry!! transfer failed due to '.$apiMsg
								);
								
							}

						}
					}
				}

				
		  	}
		  	
	    }
	    log_message('debug', 'Money Transfer Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function getMoneyTransferHistory(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

    	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

		$sql = "SELECT a.*,c.name as sender_name,c.mobile as sender_mobile FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.account_id = '$account_id' AND (b.created_by = '$user_id' OR a.user_id = '$user_id')";

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

		$userList = $this->db->query($sql)->result_array();

		$pages = ceil($count / 50);

		$data = array();
		if($userList)
		{
			foreach($userList as $key=>$list)
			{
				$data[$key]['memberID'] = $list['memberID'];
				$data[$key]['sender_name'] = $list['sender_name'].' ('.$list['sender_mobile'].')';
				$data[$key]['account_holder_name'] = $list['account_holder_name'];
				$data[$key]['mobile'] = $list['mobile'];
				$data[$key]['ifsc'] = $list['ifsc'];
				$data[$key]['transfer_amount'] = $list['transfer_amount'].' /-';
				$data[$key]['transfer_charge_amount'] = $list['transfer_charge_amount'].' /-';
				$data[$key]['total_amount'] = $list['total_wallet_charge'].' /-';
				if($list['txnType'] == 'RGS')
				{
					$data[$key]['txnType'] = 'NEFT';
				}
				elseif($list['txnType'] == 'RTG')
				{
					$data[$key]['txnType'] = 'RTGS';
				}
				elseif($list['txnType'] == 'IFS')
				{
					$data[$key]['txnType'] = 'IMPS';
				}
				else{

					$data[$key]['txnType'] = 'Not Available';
				}
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

				$data[$key]['invoiceUrl'] = base_url('transferinvoice/index/'.$list['id'].'');

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
		log_message('debug', 'Get Money Transfer List API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	



	public function walletTransferAuth(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'walletTransferAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
		$this->form_validation->set_rules('member_id', 'MemberID', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{		
			$loggedAccountID = $post['userID'];
			// check member is valid or not
			$chk_member = $this->db->get_where('users',array('user_code'=>$post['member_id'],'account_id'=>$account_id))->num_rows();
			if(!$chk_member)
			{	

				$response = array(

					'status' => 0,
					'message' => 'Sorry !! MemberID not exists.'

				);
				
			}
			else{

				$chk_txn_password = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();

				
					if($chk_txn_password['wallet_balance'] < $post['amount']){

						
						$response = array(

							'status' => 0,
							'message' => 'Sorry !! you have insufficient balance in your wallet.'

						);
					}
					else{
					
						$loggedAccountID = $post['userID'];
			            
			            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

			            $before_balance = $this->db->get_where('users',array('user_code'=>$post['member_id'],'account_id'=>$account_id))->row_array();
			            $member_code = $before_balance['user_code'];
			            $member_name = $before_balance['name'];
			            $member_id = $before_balance['id'];

			            $type = 1; 
			            $after_balance = $before_wallet_balance + $post['amount'];    
			            $type_title = 'Credited';
			            
			            $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $member_id,    
			            'before_balance'      => $before_wallet_balance,
			            'amount'              => $post['amount'],  
			            'after_balance'       => $after_balance,      
			            'status'              => 1,
			            'type'                => $type,      
			            'created'             => date('Y-m-d H:i:s'),      
			            'credited_by'         => $loggedAccountID,
			            'description'         => $post['description']            
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			            // debit wallet
			            $accountBalanceData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();
			             
			            $after_balance = $before_wallet_balance - $post['amount'];    
			                

			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $loggedAccountID,    
			                'before_balance'      => $before_wallet_balance,
			                'amount'              => $post['amount'],  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2,      
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $loggedAccountID,
			                'description'         => 'Credited into Member #'.$member_code.' ('.$member_name.')'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

						$response = array(

							'status' => 1,
							'message' => 'Congratulations!! wallet transfer successfull.'

						);
						
				}
			}
		  	
	    }
	    log_message('debug', 'walletTransferAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function payoutAccountChangeRequest(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'payoutAccountChangeRequest API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'userID', 'required');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bank_name', 'Bank Name', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$userID = $post['userID'];		
			
			$chk_already_pending_request = $this->db->get_where('payout_user_request',array('account_id'=>$account_id,'user_id'=>$userID,'status'=>1))->num_rows();

			if($chk_already_pending_request){

				$response = array(

				  'status'=>0,
				  'message'=>'Sorry!! your request is already in pending.'	

				);

			}
			else{


				$bene_data = array(
	        	 'account_id' => $account_id,	
	        	 'user_id' => $userID,
	        	 'account_holder_name' => $post['account_holder_name'],
	        	 'bank_name' => $post['bank_name'],
	        	 'account_no' => $post['account_number'],
	        	 'ifsc' => $post['ifsc'],
	        	 'encode_ban_id' => do_hash($post['account_number']),	
	        	 'status' => 1,
	        	 'created' => date('Y-m-d H:i:s')

	        	);
	        	
	        	$this->db->insert('payout_user_request',$bene_data);

	        	$message = 'Congratulations!! request sent successfully.';
				
		        //$this->User->sendNotification($userID,'Payout Account Chnage Request',$message);

	        	$response = array(

	        	  'status'=>1,
	        	  'message'=>'Congratulations!! request sent successfully.'	

	        	);

			}
		  	
	    }
	    log_message('debug', 'payoutAccountChangeRequest API Response - '.json_encode($response));	
		echo json_encode($response);

	}	


	public function getPayoutAccountChangeRequestList(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();

		if(!$post['userID']){

			$response = array(

			 'status' => 0,
			 'message'=>'Please enter userID.'	

			);
		}
		else{
			// get country list
			$countryList = $this->db->order_by('created','desc')->get_where('payout_user_request',array('account_id'=>$account_id,'user_id'=>$post['userID']))->result_array();
			$data = array();
			if($countryList)
			{
				foreach($countryList as $key=>$list)
				{
					$data[$key]['account_holder_name'] = $list['account_holder_name'];
					$data[$key]['account_no'] = $list['account_no'];
					$data[$key]['bank_name'] = $list['bank_name'];
					$data[$key]['ifsc'] = $list['ifsc'];

					if($list['status'] == 1){

						$data[$key]['status'] = 'Pending';
					}
					elseif($list['status'] == 2){

						$data[$key]['status'] = 'Approved';
					}
					else{

						$data[$key]['status'] = 'Rejected';
					}

					$data[$key]['date'] = date('d-M-Y',strtotime($list['created']));
				}

				$response = array(
					'status' => 1,
					'message' => 'Success',
					'data'=>$data
				);
			}
			else{


				$response = array(

				 'status'=>0,
				 'message'=>'Sorry!! record not found.'	

				);

			}
		}

		log_message('debug', 'getPayoutAccountChangeRequestList API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function deleteMoneyTransferBeneficiary(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'deleteMoneyTransferBeneficiary API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'userID', 'required');
		$this->form_validation->set_rules('beneID', 'beneID', 'required');
	
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$userID = $post['userID'];		
			
			$chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$userID,'id'=>$post['beneID']))->num_rows();

			if(!$chk_beneficiary){

				$response = array(

				  'status'=>0,
				  'message'=>'Sorry!! beneficiary not valid.'	

				);

			}
			else{


				$this->db->where('account_id',$account_id);
				$this->db->where('user_id',$userID);
				$this->db->where('id',$post['beneID']);
				$this->db->delete('user_benificary');

				$message = 'Beneficiary deleted successfully.';

		        //$this->User->sendNotification($userID,'Beneficiary',$message);

	        	$response = array(

	        	  'status'=>1,
	        	  'message'=>'Beneficiary deleted successfully.'	

	        	);

			}
		  	
	    }
	    log_message('debug', 'deleteMoneyTransferBeneficiary API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function getCurrentAccountType(){
		
		$response = array();

		$typeList = array(

			    array(
			   	   'account_type' => 'Individual',
	            ),
			    array(
			   	   'account_type' => 'Propietorship',
	            ),
			    array(
			   	   'account_type' => 'Partnership',
	            ),
	            array(
			   	   'account_type' => 'Private Ltd',
	            ),
	            array(
			   	   'account_type' => 'Public Ltd',
	            ),
	            array(
			   	   'account_type' => 'LLP',
	            ),
	            array(
			   	   'account_type' => 'HUF',
	            ),
	            array(
			   	   'account_type' => 'OPC',
	            )
		);	

		$data = array();
		if($typeList)
		{
			foreach($typeList as $key=>$list)
			{
				$data[$key]['account_type'] = $list['account_type'];
				
			}
		}
		
		$response = array(
			'status' => 1,
			'message' => 'Success',
			'data'=>$data
		);
		log_message('debug', 'Get Current Account Type List API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function currentAccountOpenAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'currentAccountOpenAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'userID', 'required');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|min_length[10]');
		$this->form_validation->set_rules('email', 'Email', 'required|xss_clean|valid_email');
		$this->form_validation->set_rules('account_type', 'Account Type', 'required|xss_clean');
		$this->form_validation->set_rules('pincode', 'Pincode', 'required|xss_clean|min_length[6]');
	
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$loggedAccountID = $post['userID'];		
			
			// Create request	
			$request = [
		        "user" => $accountData['current_account_user'],
		        "passCode" => $accountData['current_account_passcode'],
		        "mobileNo" => $post['mobile'],
		        "emailId" => $post['email'],
		        "p_CATEGORY_TYPE" => $post['account_type'],
		        "data"=>array
		        (
		            "p_FNAME" => $post['first_name'],
		            "p_LNAME" => $post['last_name']
		        )
		    ];

		    if($post['account_type'] == 'Individual')
		    {
		    	$request['data']['p_COMMPCODE'] = $post['pincode'];
		    }
		    else
		    {
		    	$request['data']['p_PERPCODE'] = $post['pincode'];
		    }

		    // Convert to json
		    $json_enc = json_encode($request);

		    // Create header
		    $header = [
		        'Content-type:application/json'
		    ];    
		    
		    // Create url
		    $url = CURRENT_ACCOUNT_OPEN_API_URL;
		    
		    $curl = curl_init();
		    
		    curl_setopt_array($curl, array(
		        CURLOPT_RETURNTRANSFER => true,        
		        CURLOPT_CUSTOMREQUEST => "POST",
		        CURLOPT_POSTFIELDS => $json_enc,
		        CURLOPT_HTTPHEADER => $header,
		        CURLOPT_URL => $url
		    ));
		    
		    // Get response
		    $response = curl_exec($curl);
		  	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$err = curl_error($curl);    
		    curl_close($curl);

		    /*$response = '{"status":"Success","message":"Your Application Number : 777-000471807 and Tracking Id : WEB111111111582327","errorCode":"0","p_APPLICATION_NO":"777-000471807","trackerId":"WEB111111111582327","webUrl":"https://cadigital.icicibank.com/SmartFormWeb/apps/services/www/SmartFormWeb/desktopbrowser/default/index.html?trackerId=WEB111111111582327#/login"}';*/

		    // save upi api response
	        $apiData = array(
	            'account_id' => $account_id,
	            'member_id' => $loggedAccountID,
	            'api_url' => $url,
	            'post_data' => $json_enc,
	            'response' => $response,
	            'created' => date('Y-m-d H:i:s')
	        );
	        $this->db->insert('current_account_api_response',$apiData);

	        $decodeResponse = json_decode($response,true);

	        if(isset($decodeResponse['status']) && $decodeResponse['status'] == 'Success')
	        {
	        	// save data
		        $data = array(
		            'account_id' => $account_id,
		            'member_id' => $loggedAccountID,
		            'first_name' => $post['first_name'],
		            'last_name' => $post['last_name'],
		            'mobile' => $post['mobile'],
		            'email' => $post['email'],
		            'account_type' => $post['account_type'],
		            'pincode' => $post['pincode'],
		            'application_no' => $decodeResponse['p_APPLICATION_NO'],
		            'tracker_id' => $decodeResponse['trackerId'],
		            'web_url' => $decodeResponse['webUrl'],
		            'status' => 1,
		            'created' => date('Y-m-d H:i:s'),
		            'created_by' => $loggedAccountID
		        );
		        $this->db->insert('current_account_list',$data);
		        
		        $response = array(
		          'status' => 1,
		          'message'=> $decodeResponse['message']	
		        );	
		    }
	        else
	        {
	        	$response = array(
		          'status' => 0,
		          'message'=> $decodeResponse['message']	
		        );
	        }
		  	
	    }
	    log_message('debug', 'currentAccountOpenAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}	



	public function getCurrentAccountList(){
		
		$response = array();
		$account_id = $this->User->get_domain_account();
		$post = $this->input->post();

		$userID = isset($post['userID']) ? $post['userID'] : '';

		if(!$userID){

			$resposne = array(
			  'status' => 0,
			  'message'=>'Please enter userID.'	
			);
		}
		else{
		
			$AccountList = $this->db->query("SELECT a.* FROM tbl_current_account_list as a where a.account_id = '$account_id' AND a.member_id = '$userID'")->result_array();	

			$data = array();
			if($AccountList)
			{
				foreach($AccountList as $key=>$list)
				{
					$data[$key]['name'] = $list['first_name'].' '.$list['last_name'];
					$data[$key]['mobile'] = $list['mobile'];
					$data[$key]['email'] = $list['email'];
					$data[$key]['account_type'] = $list['account_type'];
					$data[$key]['pincode'] = $list['pincode'];
					$data[$key]['application_no'] = $list['application_no'];
					$data[$key]['tracker_id'] = $list['tracker_id'];
					$data[$key]['web_url'] = $list['web_url'];
					$data[$key]['date'] = date('d-M-Y H:i:s',strtotime($list['created']));
				}
			}
			
			$response = array(
				'status' => 1,
				'message' => 'Success',
				'data'=>$data
			);
		}
		log_message('debug', 'Get Current Account Type List API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function cashDepositeAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'cashDepositeAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'userID', 'required');
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]|min_length[10]');
        $this->form_validation->set_rules('account_no', 'Account No', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');
        $this->form_validation->set_rules('remark', 'Remark', 'required|xss_clean');
	
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$loggedAccountID = $post['userID'];

			$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();		
			
			$activeService = $this->User->admin_active_service();
			if(!in_array(15, $activeService)){
				
				$response = array(
				  'status' => 0,
				  'message'=>'Sorry!! cash deposite not active for you.'	
				);
			}
			else{


				$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
				$collection_wallet_balance = $chk_wallet_balance['wallet_balance'];

				$commisionData = $this->User->get_aeps_commission($post['amount'],$loggedAccountID,4);
		        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
		        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

		        $final_deduct_amount = $post['amount'];
		        if($is_surcharge)
		        {
		        	$final_deduct_amount = $post['amount'] + $com_amount;
		        }

		        $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            	$final_deduct_wallet_balance = $final_deduct_amount + $min_wallet_balance;  

				if($collection_wallet_balance < $final_deduct_wallet_balance)
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Sorry!! insufficient balance in your wallet.'	 
					);
				}
				else{

					$admin_id = $this->User->get_admin_id($account_id);
					$admin_wallet_balance = $this->User->getMemberCollectionWalletBalance($admin_id);

					$adminCommisionData = $this->User->get_admin_aeps_commission($post['amount'],$account_id,4);
			        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
			        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

			        $final_deduct_admin_amount = $post['amount'];
			        if($admin_is_surcharge)
			        {
			        	$final_deduct_admin_amount = $post['amount'] + $admin_com_amount;
			        }

					if($admin_wallet_balance < $final_deduct_admin_amount)
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Sorry!! insufficient balance in admin wallet.'	 
						);
					}
					else{

						$response = $this->Aeps_model->sendCashDepositeOtp($post,$loggedUser['id']);
						
						if($response['status'] == 1)
						{	
							$response = array(
							  'status' => 1,
							  'message' => 'Congratulations!! otp sent successfully on your mobile number.',
							  'txnID' => $response['txnID']	 
							);
							
						}
						else
						{
							$response = array(
							  'status' => 1,
							  'message' => $response['msg'],
							);
						}
					}

				}


			}
		  	
	    }
	    log_message('debug', 'cashDepositeAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function cashDepositeOtpAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'cashDepositeOtpAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'userID', 'required');
		$this->form_validation->set_rules('otp_code', 'Otp Code', 'required|xss_clean');
        $this->form_validation->set_rules('txnid', 'txnid', 'required|xss_clean');
       
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$loggedAccountID = $post['userID'];

			$txnid = $post['txnid'];

			$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();		
			
			$activeService = $this->User->admin_active_service();
			if(!in_array(15, $activeService)){
				
				$response = array(
				  'status' => 0,
				  'message'=>'Sorry!! cash deposite not active for you.'	
				);
			}
			else{

				$chk_txn_id = $this->db->get_where('cash_deposite_history',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'txnid'=>$txnid,'status'=>1))->row_array();
				if(!$chk_txn_id)
				{
					$response = array(

					  'status' => 0,
					  'message'=>'Sorry!! txnID not valid.'	

					);
				}
				else{

					$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
					$collection_wallet_balance = $chk_wallet_balance['wallet_balance'];

					$commisionData = $this->User->get_aeps_commission($chk_txn_id['amount'],$loggedAccountID,4);
			        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
			        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

			        $final_deduct_amount = $chk_txn_id['amount'];
			        if($is_surcharge)
			        {
			        	$final_deduct_amount = $chk_txn_id['amount'] + $com_amount;
			        }

					if($collection_wallet_balance < $final_deduct_amount)
					{
						$response = array(

						  'status' => 0,
						  'message'=>'Sorry!! insufficient balance in your wallet.'	

						);
					}
					else{

						$admin_id = $this->User->get_admin_id($account_id);
						$admin_wallet_balance = $this->User->getMemberCollectionWalletBalance($admin_id);

						$adminCommisionData = $this->User->get_admin_aeps_commission($chk_txn_id['amount'],$account_id,4);
				        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
				        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

				        $final_deduct_admin_amount = $chk_txn_id['amount'];
				        if($admin_is_surcharge)
				        {
				        	$final_deduct_admin_amount = $chk_txn_id['amount'] + $admin_com_amount;
				        }

						if($admin_wallet_balance < $final_deduct_admin_amount)
						{
							$response = array(

							  'status' => 0,
							  'message'=>'Sorry!! insufficient balance in admin wallet.'	

							);
						}
						else{

							$response = $this->Aeps_model->verifyCashDepositeOtp($post,$loggedUser['id']);
							
							if($response['status'] == 1)
							{
								$response = array(

								  'status' => 1,
								  'message'=>'Congratulations!! cash deposited successfully.'	

								);
							}
							else
							{	
								$response = array(

								  'status' => 0,
								  'message'=>$response['msg']	

								);
							}
						}
					}

				}

			}
		  	
	    }
	    log_message('debug', 'cashDepositeOtpAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function payoutOpenAuth(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'payoutOpenAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
		$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
		$this->form_validation->set_rules('confirm_account_no', 'Confirm Account No.', 'required|xss_clean|matches[account_no]');
		$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
		$this->form_validation->set_rules('bankID', 'Bank', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric|callback_maximumCheck');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Valid Details.'
			);
		}
		else
		{	
			$loggedAccountID = $post['userID'];

			$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(2, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$memberID = $loggedUser['user_code'];
				$mobile = $post['mobile'];
				$account_holder_name = $post['account_holder_name'];
				$account_no = $post['account_no'];
				$ifsc = $post['ifsc'];
				$bankID = $post['bankID'];
				$amount = $post['amount'];
				$txnType = 'IFS';
				$transaction_id = time().rand(1111,9999);
				$receipt_id = rand(111111,999999);

				
				$chk_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
	            
	            // save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout Open E-Wallet Balance - '.$chk_wallet_balance.'.]'.PHP_EOL;
		        $this->User->generateLog($log_msg);

	            // get dmr surcharge
	            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID);
	            // save system log
	            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout Open Api - Payout Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
	            $this->User->generateLog($log_msg);
	            $final_amount = $amount + $surcharge_amount;
	            $before_balance = $chk_wallet_balance;

	            if($before_balance < $final_amount){
	                // save system log
	                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout Open Api - Insufficient Wallet Error]'.PHP_EOL;
	                $this->User->generateLog($log_msg);
	                $response = array(

	                  'status' => 0,
	                  'message'=>'Sorry!! insufficient balance in your wallet.'

	                );
	            }
	            else{

		            $after_wallet_balance = $before_balance - $final_amount;    

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $loggedAccountID,    
		                'before_balance'      => $before_balance,
		                'amount'              => $final_amount,  
		                'after_balance'       => $after_wallet_balance,      
		                'status'              => 1,
		                'type'                => 2,   
		                'wallet_type'		  => 2,   
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'Payout #'.$transaction_id.' Amount Deducted.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout Open API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
		            $this->User->generateLog($log_msg);

					$data = array(
						'account_id' => $account_id,
						'user_id' => $loggedAccountID,
						'transfer_amount' => $amount,
						'transfer_charge_amount' => $surcharge_amount,
						'total_wallet_charge' => $final_amount,
						'after_wallet_balance' => $after_wallet_balance,
						'txnType' => $txnType,
						'transaction_id' => $transaction_id,
						'encode_transaction_id' => do_hash($transaction_id),
						'status' => 2,
						'wallet_type' => 2,
						'invoice_no' => $receipt_id,
						'memberID' => $memberID,
						'mobile' => $mobile,
						'account_holder_name' => $account_holder_name,
						'account_no' => $account_no,
						'ifsc' => $ifsc,
						'is_payout_open' => 1,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_fund_transfer',$data); 

					$responseData = $this->Api_model->cibPayoutOpen($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType,$loggedAccountID);

					// save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout Open Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
		            $this->User->generateLog($log_msg);
		            

					
					if(isset($responseData['status']) && $responseData['status'] == 1)
					{
						$response = array(
						  'status' => 1,
						  'message'=>'Congratulations!! transfered successfully.'	
						);
						
					}
					elseif(isset($responseData['status']) && $responseData['status'] == 2)
					{
						$requestID = $responseData['requestID'];
						$rrno = $responseData['rrno'];
						$this->db->where('account_id',$account_id);
						$this->db->where('user_id',$loggedAccountID);
						$this->db->where('transaction_id',$transaction_id);
						$this->db->update('user_fund_transfer',array('op_txn_id'=>$requestID,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));
						$response = array(
						  'status' => 1,
						  'message'=>'Congratulations!! transfered successfully.'	
						);
					}
					elseif(isset($responseData['status']) && $responseData['status'] == 3)
					{
						$apiMsg = $responseData['msg'];

						$this->db->where('account_id',$account_id);
						$this->db->where('user_id',$loggedAccountID);
						$this->db->where('transaction_id',$transaction_id);
						$this->db->update('user_fund_transfer',array('status'=>4,'updated'=>date('Y-m-d H:i:s')));

						
						$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
						$after_wallet_balance = $before_balance + $final_amount;    

			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $loggedAccountID,    
			                'before_balance'      => $before_balance,
			                'amount'              => $final_amount,  
			                'after_balance'       => $after_wallet_balance,      
			                'status'              => 1,
			                'type'                => 1,   
			                'wallet_type'		  => 2,   
			                'created'             => date('Y-m-d H:i:s'),      
			                'description'         => 'Payout #'.$transaction_id.' Amount Refund.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			            // save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout API - Member Wallet Refund - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
			            $this->User->generateLog($log_msg);
						
						$response = array(
						  'status' => 0,
						  'message'=>'Sorry!! transfer failed due to '.$apiMsg	
						);

					}
				}

	        }
	    }
	    log_message('debug', 'payoutOpenAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function getPayoutOpenHistory(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

		$userList = $this->db->query("SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$user_id' OR a.user_id = '$user_id') AND a.is_payout_open = 1 AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."'")->result_array();
		
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
			 'data'=>$data
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		log_message('debug', 'Get Fund Transfer List API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	function requestAmountCheck($num)
    {
    	$this->load->library('form_validation');
        if ($num < 1)
        {
            $this->form_validation->set_message(
                            'requestAmountCheck',
                            'The %s field must be grater than 0'
                        );
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }


	public function upiRequestAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'upiRequestAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean|callback_requestAmountCheck');
		$this->form_validation->set_rules('vpa_id', 'VPA ID', 'required|xss_clean');
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
       
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(5, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				$responseData = $this->Api_model->upiSendRequest($account_id,$loggedAccountID,$post);

				if($responseData['status'] == 1){

					$response = array(
						'status' => 1,
						'txnid' => $responseData['merchantTranId'],
						'message'=> 'Transaction initiated',
						'is_api_error' => 0
					);
				}
				else
				{
					
					$response = array(
						'status' => 0,
						'message'=> $responseData['message'],
						'is_api_error' => 1
					);
				}

			}

				
	    }
	    log_message('debug', 'upiRequestAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function getUpiCallbackResponse(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'getUpiCallbackResponse API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('txnid', 'txnid', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(5, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				$txnid = $post['txnid'];

				// get member id
		        $get_member_data = $this->db->get_where('upi_transaction',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'txnid'=>$txnid,'is_api_response'=>1))->row_array();
		        if($get_member_data)
		        {
		        	if($get_member_data['status'] == 2)
		        	{
		        		$response = array(
			        		'status' => 1,
			        		'message' => 'Congratulations ! Your transaction successfully credited.',
			        		'api_status' => 2
			        	);
		        	}
		        	elseif($get_member_data['status'] == 3)
		        	{
		        		$response = array(
			        		'status' => 0,
			        		'message' => 'Sorry ! Your transaction failed from VPA side.',
			        		'api_status' => 3
			        	);
		        	}
		        }
		        else
		        {
		        	$response = array(
		        		'status' => 0,
		        		'message' => 'Something went wrong, please try again later.'
		        	);
		        }

			}

				
	    }
	    log_message('debug', 'getUpiCallbackResponse API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function upiDynamicQrAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'upiDynamicQrAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean|callback_requestAmountCheck');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(5, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				
				$responseData = $this->Api_model->upiGenerateDynamicQr($account_id,$loggedAccountID,$post);

				if($responseData['status'] == 1){

					$record_id = $responseData['record_id'];

					$qr_code = base_url('qrcode/index/'.$record_id.'');

					$response = array(
						'status' => 1,
						'message'=> 'Success',
						'qr_code' => $qr_code,
						'is_api_error' => 0
					);
				}
				else
				{	

					$response = array(
					  'status' => 0,
					  'message'=>$responseData['message']	
					);
					
				}
				

			}

				
	    }
	    log_message('debug', 'upiDynamicQrAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function staticQrAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'staticQrAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(5, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				
				// check qr is active or not
			 	$chk_qr_status = $this->db->get_where('users',array('is_upi_qr_active'=>1,'id'=>$loggedAccountID))->num_rows();

			 	if($chk_qr_status)
			 	{
			 		$get_qr_url = $this->db->select('qr_url')->get_where('users',array('is_upi_qr_active'=>1,'id'=>$loggedAccountID))->row_array();

			 		$get_record_id = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'member_id'=>$loggedAccountID))->row_array();

			 		$record_id = $get_record_id['id'];

			 		$responseData = array();
			 		$responseData['status'] = 1;
			 		$responseData['qr'] = $get_qr_url['qr_url'];
			 		$responseData['qr'] = $get_qr_url['qr_url'];
			 		$responseData['record_id'] = $record_id;
			 	}
			 	else
			 	{
			 		$responseData = $this->Api_model->upiGenerateStaticQr($account_id,$loggedAccountID);
			 	}
					
				if($responseData['status'] == 1){

					$record_id  = $responseData['record_id'];

					$response = array(

					 'status' => 1,
					 'message' => 'Success',
					 'qr_url'  => base_url('StaticQr/index/'.$loggedAccountID.'')	

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
	    log_message('debug', 'staticQrAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function mapQrAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'mapQrAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('txnid', 'Txn ID', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(5, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				
				$txnid = $post['txnid'];
				// check txnid valid or not
				$chk_txn_id = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->num_rows();
				if(!$chk_txn_id)
				{
					$response = array(

					  'status' => 0,
					  'message'=>'TxnID Not Valid.'	
					);
				}
				else{

					// check txnid valid or not
					$chk_txn_id = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'txnid'=>$txnid,'is_map'=>1))->num_rows();
					if($chk_txn_id)
					{
						$response = array(

						  'status' => 0,
						  'message'=>'Qr already mapped.'	
						);
					}
					else{

						// check txnid valid or not
						$get_txn_data = $this->db->get_where('upi_collection_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->row_array();
						$qr_code = $get_txn_data['qr_image'];
						$ref_id = $get_txn_data['ref_id'];
						$record_id = $get_txn_data['id'];

						$this->db->where('account_id',$account_id);
			            $this->db->where('id',$loggedAccountID);
			            $this->db->update('users',array('qr_url'=>$qr_code,'is_upi_qr_active'=>1,'upi_qr_ref_id'=>$ref_id));

			            $this->db->where('account_id',$account_id);
			            $this->db->where('txnid',$txnid);
			            $this->db->update('upi_collection_qr',array('is_map'=>1,'map_member_id'=>$loggedAccountID,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));

			            $this->Api_model->mapQrName($ref_id,$loggedAccountID);

			            $qr_code = base_url('qrcode/index/'.$record_id.'');
						
						$response = array(

						 'status' => 1,
						 'message'=>'Congratulations!! qr mapped successfully.',
						 'qr_code' => $qr_code,

						);
					}
				}
				

			}

				
	    }
	    log_message('debug', 'mapQrAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function cashRequestAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'cashRequestAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean|callback_requestAmountCheck');
		$this->form_validation->set_rules('vpa_id', 'VPA ID', 'required|xss_clean');
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
       
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(7, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				$responseData = $this->Api_model->upiSendCashRequest($account_id,$loggedAccountID,$post);

				if($responseData['status'] == 1){

					$response = array(
						'status' => 1,
						'txnid' => $responseData['merchantTranId'],
						'message'=> 'Transaction initiated',
						'is_api_error' => 0
					);
				}
				else
				{
					
					$response = array(
						'status' => 0,
						'message'=> $responseData['message'],
						'is_api_error' => 1
					);
				}

			}

				
	    }
	    log_message('debug', 'cashRequestAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function getUpiCashCallbackResponse(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'getUpiCashCallbackResponse API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('txnid', 'txnid', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(5, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				$txnid = $post['txnid'];

				// get member id
		        $get_member_data = $this->db->get_where('upi_cash_transaction',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'txnid'=>$txnid,'is_api_response'=>1))->row_array();
		        if($get_member_data)
		        {
		        	if($get_member_data['status'] == 2)
		        	{
		        		$response = array(
			        		'status' => 1,
			        		'message' => 'Congratulations ! Your transaction successfully credited.',
			        		'api_status' => 2
			        	);
		        	}
		        	elseif($get_member_data['status'] == 3)
		        	{
		        		$response = array(
			        		'status' => 0,
			        		'message' => 'Sorry ! Your transaction failed from VPA side.',
			        		'api_status' => 3
			        	);
		        	}
		        }
		        else
		        {
		        	$response = array(
		        		'status' => 0,
		        		'message' => 'Something went wrong, please try again later.'
		        	);
		        }

			}

				
	    }
	    log_message('debug', 'getUpiCashCallbackResponse API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function cashDynamicQrAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'cashDynamicQrAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean|callback_requestAmountCheck');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(7, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				
				$responseData = $this->Api_model->upiCashGenerateDynamicQr($account_id,$loggedAccountID,$post);

				if($responseData['status'] == 1){

					$record_id = $responseData['record_id'];

					$qr_code = base_url('cashqrcode/index/'.$record_id.'');

					$response = array(
						'status' => 1,
						'message'=> 'Success',
						'qr_code' => $qr_code,
						'is_api_error' => 0
					);
				}
				else
				{	

					$response = array(
					  'status' => 0,
					  'message'=>$responseData['message']	
					);
					
				}
				

			}

				
	    }
	    log_message('debug', 'cashDynamicQrAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function cashStaticQrAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'cashStaticQrAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(7, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				
				// check qr is active or not
			 	$chk_qr_status = $this->db->get_where('users',array('is_upi_cash_qr_active'=>1,'id'=>$loggedAccountID))->num_rows();

			 	if($chk_qr_status)
			 	{
			 		$get_qr_url = $this->db->select('cash_qr_url')->get_where('users',array('is_upi_qr_active'=>1,'id'=>$loggedAccountID))->row_array();

			 		$get_record_id = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'member_id'=>$loggedAccountID))->row_array();

			 		$record_id = $get_record_id['id'];

			 		$responseData = array();
			 		$responseData['status'] = 1;
			 		$responseData['qr'] = $get_qr_url['cash_qr_url'];
			 		$responseData['record_id'] = $record_id;
			 	}
			 	else
			 	{
			 		$responseData = $this->Api_model->upiCashGenerateStaticQr($account_id,$loggedAccountID);
			 	}
					
				if($responseData['status'] == 1){

					$record_id  = $responseData['record_id'];

					$response = array(

					 'status' => 1,
					 'message' => 'Success',
					 'qr_url'  => base_url('CashstaticQr/index/'.$loggedAccountID.'')	

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
	    log_message('debug', 'cashStaticQrAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function mapCashQrAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'mapCashQrAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('txnid', 'Txn ID', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(7, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				
				$txnid = $post['txnid'];
				// check txnid valid or not
				$chk_txn_id = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->num_rows();
				if(!$chk_txn_id)
				{
					$response = array(

					  'status' => 0,
					  'message'=>'TxnID Not Valid.'	
					);
				}
				else{

					// check txnid valid or not
					$chk_txn_id = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'txnid'=>$txnid,'is_map'=>1))->num_rows();
					if($chk_txn_id)
					{
						$response = array(

						  'status' => 0,
						  'message'=>'Qr already mapped.'	
						);
					}
					else{

						// check txnid valid or not
						$get_txn_data = $this->db->get_where('upi_cash_qr',array('account_id'=>$account_id,'txnid'=>$txnid))->row_array();
						$qr_code = $get_txn_data['qr_image'];
						$ref_id = $get_txn_data['ref_id'];
						$record_id = $get_txn_data['id'];

						$this->db->where('account_id',$account_id);
			            $this->db->where('id',$loggedAccountID);
			            $this->db->update('users',array('cash_qr_url'=>$qr_code,'is_upi_cash_qr_active'=>1,'upi_cash_qr_ref_id'=>$ref_id));

			            $this->db->where('account_id',$account_id);
			            $this->db->where('txnid',$txnid);
			            $this->db->update('upi_cash_qr',array('is_map'=>1,'map_member_id'=>$loggedAccountID,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));

			            $this->Api_model->mapCashQrName($ref_id,$loggedAccountID);
						$qr_code = base_url('qrcode/index/'.$record_id.'');
						$response = array(

						 'status' => 1,
						 'message'=>'Congratulations!! qr mapped successfully.'	,
						 'qr_code' => $qr_code,

						);
					}
				}
				

			}

				
	    }
	    log_message('debug', 'mapCashQrAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function getUpiCollectionHistory(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

    	$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$user_id'";

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

		$userList = $this->db->query($sql)->result_array();

		$pages = ceil($count / 50);

		$data = array();
		if($userList)
		{
			foreach($userList as $key=>$list)
			{
				$data[$key]['user_name'] = $list['user_code'].' ('.$list['name'].')';
				$data[$key]['amount'] = $list['amount'].' /-';
				
				if($list['type_id'] == 1)
				{
					$data[$key]['type'] = 'UPI Request';
				}
				elseif($list['type_id'] == 2)
				{
					$data[$key]['type'] = 'Static QR';
				}
				elseif($list['type_id'] == 3)
				{
					$data[$key]['type'] = 'Dynamic QR';
				}
				else
				{
					$data[$key]['type'] = 'Not Available';
				}	


				$data[$key]['txnid'] = $list['txnid'];
				$data[$key]['bank_rrno'] = $list['bank_rrno'];
				$data[$key]['vpa_id'] = $list['vpa_id'];
				$data[$key]['transaction_id'] = $list['transaction_id'];
				$data[$key]['description'] = $list['description'];
						
				if($list['status'] == 1) {
					$data[$key]['status'] = 'Pending';
				}
				elseif($list['status'] == 2) {
					$data[$key]['status'] = 'Success';
				}
				elseif($list['status'] == 3 || $list['status'] == 0) {
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
		log_message('debug', 'getUpiCollectionHistory List API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function getUpiCashHistory(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

    	$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_cash_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$user_id'";

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

		$userList = $this->db->query($sql)->result_array();

		$pages = ceil($count / 50);

		$data = array();
		if($userList)
		{
			foreach($userList as $key=>$list)
			{
				$data[$key]['user_name'] = $list['user_code'].' ('.$list['name'].')';
				$data[$key]['amount'] = $list['amount'].' /-';
				
				if($list['type_id'] == 1)
				{
					$data[$key]['type'] = 'UPI Request';
				}
				elseif($list['type_id'] == 2)
				{
					$data[$key]['type'] = 'Static QR';
				}
				elseif($list['type_id'] == 3)
				{
					$data[$key]['type'] = 'Dynamic QR';
				}
				else
				{
					$data[$key]['type'] = 'Not Available';
				}	


				$data[$key]['txnid'] = $list['txnid'];
				$data[$key]['bank_rrno'] = $list['bank_rrno'];
				$data[$key]['vpa_id'] = $list['vpa_id'];
				$data[$key]['transaction_id'] = $list['transaction_id'];
				$data[$key]['description'] = $list['description'];
						
				if($list['status'] == 1) {
					$data[$key]['status'] = 'Pending';
				}
				elseif($list['status'] == 2) {
					$data[$key]['status'] = 'Success';
				}
				elseif($list['status'] == 3 || $list['status'] == 0) {
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
		log_message('debug', 'getUpiCashHistory List API Response - '.json_encode($response));	
		echo json_encode($response);

	}




	public function getCashDepositeHistory(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

    	$sql = "SELECT a.*,b.user_code,b.name FROM tbl_cash_deposite_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$user_id' AND a.status > 1";

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

		$userList = $this->db->query($sql)->result_array();

		$pages = ceil($count / 50);

		$data = array();
		if($userList)
		{
			foreach($userList as $key=>$list)
			{
				$data[$key]['user_name'] = $list['user_code'].' ('.$list['name'].')';
				$data[$key]['mobile'] = $list['mobile'];
				$data[$key]['account_no'] = $list['account_no'];
				$data[$key]['amount'] = $list['amount'].' /-';
				$data[$key]['txnid'] = $list['txnid'];
				$data[$key]['bank_rrn'] = $list['bank_rrn'];
				$data[$key]['remark'] = $list['remark'];
						
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
		log_message('debug', 'getCashDepositeHistory List API Response - '.json_encode($response));	
		echo json_encode($response);

	}

    
    public function dmtRegisterAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'DMT Register API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }

        else

        {
        	$post = $this->input->post();
		log_message('debug', 'dmtRegisterAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean');
		$this->form_validation->set_rules('dob', 'DOB', 'required|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
		$this->form_validation->set_rules('pin_code', 'Pin Code', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(8, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				

				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'DMT Register API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';

				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'DMT Register Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)

					{
						$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();

						if($chk_token_user && $tokenUserID == $user_id && $tokenIP == $user_ip_address)
						{

												// check mobile no. already registered or not
				$memberData = $this->db->select('mobile')->get_where('user_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$post['mobile'],'status'=>1))->num_rows();
				if($memberData)
				{
					$response = array(

					  'status'  => 0,
					  'message' => 'Sorry!! beneficiary already registered.'

					);
				}
				else{

					$response = $this->Dmt_model->memberActivation($post,$loggedAccountID);
					if($response['status'] == 1)
					{	
						$response = array(
						  'status'  => 1,
						  'message' => 'OTP sent successfully.Please verify.',
						  'token'   => $response['token']
						);
					}
					else
					{	
						$response = array(
						  'status'  => 0,
						  'message' => 'Registration failed due to '.$response['message'],
						);

					}
				}

		    }

						else
						{
							$response = array(
							  'status' => 0,
							  'message' => 'Session out.Please Login Again.',
							  'is_login'=>0	
							);

						}
				

			}

						else
						{
							$response = array(
							  'status' => 0,
							  'message' => 'Session out.Please Login Again.',
							  'is_login'=>0	
							);
						}


					}
        
    		}

				
	    }
	    
	    log_message('debug', 'dmtRegisterAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}

    
    public function dmtActiveOtpAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'DMT Activate Otp  API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }

        else
        {

        	$post = $this->input->post();
		log_message('debug', 'dmtActiveOtpAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('token', 'token', 'required|xss_clean');
		$this->form_validation->set_rules('otp_code', 'otp_code', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid otp.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(8, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				
				$token = $post['token'];
				// check token valid or not
				$chk_token = $this->db->get_where('user_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'token'=>$token,'status'=>0))->num_rows();
				if(!$chk_token)
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry!! token not valid.'
					);
				}
				else{


					$response = $this->Dmt_model->memberActivationOtpAuth($post,$loggedAccountID);
					if($response['status'] == 1)
					{	
						$response = array(
							'status' => 1,
							'message' => 'Congratulations!! otp verified successfully.',
							'mobile'  => $response['mobile']
						);
					}
					else
					{	
						$response = array(
							'status' => 0,
							'message' => 'Sorry!! otp verification failed due to '.$response['message'],
							'token'  => $token,
						);

					}

				}

			}

				
	    }

        }
		
	    log_message('debug', 'dmtActiveOtpAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	


	public function verifyIfscCode(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'verifyIfscCode API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('ifsc', 'ifsc', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid detail.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(8, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				$ifsc = $post['ifsc'];
				
				$response = $this->Dmt_model->verifyIfscCode($ifsc,$loggedAccountID);
				if($response['status'] == 1)
				{
					$res = array(
						'status' => 1,
						'message' => $response['message'],
						'address' => $response['address'],
						'bankName' => $response['bankName'],
						'branchName' => $response['branchName'],
						'city' => $response['city'],
						'district' => $response['district'],
						'ifscDetails' => $response['ifscDetails'],
						'state' => $response['state']
					);

					$response = array(
						'status' => 1,
						'message' => 'Success',
						'res' => $res
					);
				}
				else
				{
					$response = array(
						'status' => 0,
						'message' => $response['message']
					);
				}

			}

				
	    }
	    log_message('debug', 'verifyIfscCode API Response - '.json_encode($response));	
		echo json_encode($response);

	}

    public function dmtLoginAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Dmt Login Auth API Header - '.json_encode($header_data));	
         if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
        	$post = $this->input->post();
		log_message('debug', 'dmtLoginAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'mobile', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid detail.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(8, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'DMT Login Auth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';

			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'DMT Login Auth Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{

				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();


					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] ==1 && $chk_user_token['is_login'] == 1)

					{
						$memberData = $this->db->select('mobile')->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$post['mobile'],'status'=>1))->num_rows();
				if(!$memberData)
				{
					$response = $this->Dmt_model->memberFetchDetail($post,$loggedAccountID);
					if($response['status'] == 1)
					{
						$response = array(
							'status' => 1,
							'message' => 'Success',
							'mobile'  => $post['mobile'] 
						);
					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry!! beneficiary not registered.',
							'mobile'  => $post['mobile'] 
						);
					}
				}
				else{

					$response = array(
						'status' => 1,
						'message' => 'Success',
						'mobile'  => $post['mobile'] 
					);
				}

			}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);

					}

			}
			else
			{
				$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);

			}
			
			}
	
	    }
	    
        }
        log_message('debug', 'dmtLoginAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function dmtBeneficiaryAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);		
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'DMT Beneficiary Auth API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }

        else

        {

        	$post = $this->input->post();
		log_message('debug', 'dmtBeneficiaryAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');		
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
		$this->form_validation->set_rules('ben_mobile', 'Beneficiary Mobile', 'required|xss_clean|min_length[10]');
		$this->form_validation->set_rules('account_no', 'Account No.', 'required|xss_clean');
		$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
		$this->form_validation->set_rules('bankID', 'Bank', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid detail.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(8, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'DMT Beneficiary Auth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';

			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Dmt Beneficiary Auth Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{

				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();

					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] ==1 && $chk_user_token['is_login'] == 1)
					{
						$mobile = $post['accountMobile'];
				// check mobile is registered or not
				$member_dmt_status = $this->db->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$mobile,'status'=>1))->num_rows();
				if(!$member_dmt_status)
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry!! dmt account not activated.'
					);
				}
				else{

					$response = $this->Dmt_model->addBeneficiary($post,$mobile,$loggedAccountID);
					if($response['status'] == 1)
					{	
						$response = array(
							'status' => 1,
							'message' => 'Congratulations!! beneficiary registered successfully.',
							'mobile'  => $mobile
						);

					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry!! beneficiary regiterd failed due to '.$response['message'],
							'mobile'  => $mobile
						);
					}


				}

					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				

			}
			else
			{

				$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);


			}

			
			}

				
	    }

        }
		
	    log_message('debug', 'dmtBeneficiaryAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function dmtBeneficiaryVerifyAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'dmtBeneficiaryVerifyAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('benId', 'benId', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'mobile', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid detail.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(8, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				$benId = $post['benId'];
				$mobile = $post['mobile'];

				// check benId valid or not
				$chk_ben_id = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$benId,'status'=>1,'is_verify'=>0))->num_rows();
				if(!$chk_ben_id)
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry!! beneficiary not valid.',
						'mobile' => $mobile
					);
				}
				else{

					// get dmr surcharge
			        $surcharge_amount = $this->User->get_account_verify_surcharge($loggedAccountID);
			        
			    	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

			    	$min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
			        $final_deduct_wallet_balance = $surcharge_amount + $min_wallet_balance;  

			        $final_amount = $surcharge_amount;
			        $before_balance = $chk_wallet_balance['wallet_balance'];
					
			        if($chk_wallet_balance['wallet_balance'] < $final_deduct_wallet_balance){
			            
			            $response = array(
							'status' => 0,
							'message' => 'Sorry!! Insufficient Fund in Your Wallet.',
							'mobile' => $mobile 
						);
			        }
			        else
			        {
			        	$admin_id = $this->User->get_admin_id($account_id);
						$admin_wallet_balance = $this->User->getMemberVirtualWalletBalance($admin_id);

						$admin_surcharge_amount = $this->User->get_admin_account_verify_surcharge();

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Admin Account Verify Charge Amount - '.$admin_surcharge_amount.' - Admin Wallet Balance - '.$admin_wallet_balance.']'.PHP_EOL;
				        $this->User->generateLog($log_msg);

						$final_admin_amount = $admin_surcharge_amount;

						if($admin_wallet_balance < $final_admin_amount){
				            
				            $response = array(
								'status' => 0,
								'message' => 'Sorry!! Insufficient Fund in Admin Wallet.',
								'mobile' => $mobile 
							);
				        }
				        else
				        {
				        	$response = $this->Dmt_model->verifyBen($benId,$mobile,$loggedAccountID,$final_amount,$final_admin_amount,$admin_id);
							if($response['status'] == 1)
							{
								$response = array(
									'status' => 1,
									'message' => 'Congratulations!! beneficiary verified successfully.',
									'mobile' => $mobile
								);
							}
							else
							{
								$response = array(
									'status' => 0,
									'message' => 'Sorry!! beneficiary verification failed due to '.$response['message'],
									'mobile' => $mobile 
								);
							}
				        }
			        }

			        

					
				}
			
			}

				
	    }
	    log_message('debug', 'dmtBeneficiaryVerifyAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



	public function dmtBeneficiaryDeleteAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'dmtBeneficiaryDeleteAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('benId', 'benId', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'mobile', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid detail.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(8, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				$benId = $post['benId'];
				$mobile = $post['mobile'];

				// check benId valid or not
				$chk_ben_id = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$benId))->num_rows();
				if(!$chk_ben_id)
				{
					$response = array(
						'status' => 0,
						'message' => 'Sorry!! beneficiary not valid.',
						'mobile' => $mobile
					);
				}
				else{

					$response = $this->Dmt_model->deleteBen($benId,$mobile,$loggedAccountID);
					if($response['status'] == 1)
					{
						$response = array(
							'status' => 1,
							'message' => 'Congratulations!! beneficiary deleted successfully.',
							'mobile' => $mobile
						);
					}
					else
					{
						$response = array(
							'status' => 0,
							'message' => 'Sorry!! beneficiary delete failed due to '.$response['message'],
							'mobile' => $mobile 
						);
					}
				}
			
			}

				
	    }
	    log_message('debug', 'dmtBeneficiaryDeleteAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



        public function dmtGetBeneficiaryData(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Recharge Auth API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
        	$post = $this->input->post();
		log_message('debug', 'dmtGetBeneficiaryData API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('benId', 'benId', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid detail.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(8, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];
				// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Dmt Get Beneficiary Auth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';

			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Dmt Get Beneficiary Auth Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{

				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();

				if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] ==1 && $chk_user_token['is_login'] == 1)

				{
					$benId = $post['benId'];
				
				$benData = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$benId,'status'=>1))->row_array();
				if($benData){

					$response = array(
						'status' => 1,
						'message'=>'Success',
						'account_no' => isset($benData['account_no']) ? $benData['account_no'] : '',
						'ifsc' => isset($benData['ifsc']) ? $benData['ifsc'] : ''
					);
				}
				else{


					$response = array(
						'status' => 0,
						'message'=>'Sorry!! beneficiary not exists.',
					);

				}

				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);

				}
				

			}
			else
			{
				$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);

			}
				
			
			}

				
	    }

        }
		
	    log_message('debug', 'dmtGetBeneficiaryData API Response - '.json_encode($response));	
		echo json_encode($response);

	}


    public function dmtTransferAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Dmt Transfer Auth API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
        	$response = array();
		$post = $this->input->post();
		log_message('debug', 'dmtTransferAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('benId', 'Beneficiary', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid detail.'
			);
		}
		else
		{	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(8, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			else{

				$loggedAccountID = $post['userID'];

				// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Dmt Transfer Auth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';

			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Dmt Transfer Auth Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{

					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();

				if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] ==1 && $chk_user_token['is_login'] == 1)

				{

					$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

				if($post['amount'] < 101){

					$response = array(

					  'status'  => 0,
					  'message'	=> 'Sorry!! transfer amount should be more than 100.'

					);

				}
				elseif($post['amount'] > 5000){

					$response = array(

					  'status'  => 0,
					  'message'	=> 'Sorry!! transfer amount should be less than 5000.'

					);

				}
				else{


					$chk_beneficiary = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$post['benId']))->row_array();

					if(!$chk_beneficiary){

						$response = array(
						  'status'  => 0,
						  'message'	=> 'Sorry!! beneficiary not valid.'
						);
					}
					else{

						$amount = $post['amount'];
						// get dmr surcharge
			            $surcharge_amount = $this->User->get_member_dmt_surcharge($amount,$loggedAccountID);
			            // save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - API('.$loggedUser['user_code'].') - DMT Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
				        $this->User->generateLog($log_msg);
						
			        	$chk_wallet_balance =$this->User->getMemberWalletBalanceSP($loggedAccountID);

			        	$min_wallet_balance = $chk_wallet_balance;
            			$final_deduct_wallet_balance = $amount + $surcharge_amount + $min_wallet_balance;  

			            $final_amount = $amount + $surcharge_amount;
			            $before_balance = $chk_wallet_balance;
						
						// save system log
					    $log_msg = '['.date('d-m-Y H:i:s').' - API('.$loggedUser['user_code'].') - DMT Member Wallet Balance - '.$before_balance.' Member Final Deduct Amount - '.$final_amount.']'.PHP_EOL;
					    $this->User->generateLog($log_msg);

			            if($before_balance < $final_deduct_wallet_balance){
			                
			                // save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - API('.$loggedUser['user_code'].') - DMT Member Wallet Balance Error]'.PHP_EOL;
					        $this->User->generateLog($log_msg);

			                $response = array(
							  'status'  => 0,
							  'message'	=> 'Sorry!! insufficient balance in your wallet.'
							);
			            }
			            else{

			            		$active_api_id = 2;
								
								$response = $this->Dmt_model->transferFund($post,$surcharge_amount,$loggedAccountID,$active_api_id);
								if($response['status'] == 1)
								{
									$response = array(
									  'status'  => 1,
									  'message'	=> 'Congratulations!! amount transfered successfully.'
									);
								}
								elseif($response['status'] == 2)
								{
									$response = array(
									  'status'  => 1,
									  'message'	=> 'Bank transfer is in pending.'
									);
								}
								else
								{	
									$response = array(
									  'status'  => 0,
									  'message'	=> 'Sorry!! bank transfer failed due to '.$response['message']
									);
								}
							
						}
					}
				}

				}
				else
				{

					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}


			}

			else

			{
				$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);

			}

			
			}

				
	    }

        }
		
	    log_message('debug', 'dmtTransferAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}



    public function dmtBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);
    	$post = $this->input->post();
		log_message('debug', 'dmtBeneficiaryList List API Post Data - '.json_encode($post));	
		
    	$response = array();
    	$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Dmt Beneficiary List Auth API Header - '.json_encode($header_data));	
        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {	
        	

        	
        	$activeService = $this->User->account_active_service($post['userID']);
		if(!in_array(8, $activeService)){
			$response = array(
				'status' => 0,
				'message' => 'Sorry!! this service is not active for you.'
			);
		}
		else{

				$loggedAccountID = isset($post['userID']) ? $post['userID'] : 0;
			$mobile = isset($post['mobile']) ? $post['mobile'] : 0;

			// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Dmt Beneficiary List Auth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';

			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Dmt Beneficiary List Auth Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)

			{	
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();

				if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] ==1 && $chk_user_token['is_login'] == 1)


				{

						// check user valid or not
			$chk_user = $this->db->get_where('users',array('id'=>$loggedAccountID))->num_rows();
			if($chk_user)
			{
				$sql = "SELECT a.* FROM tbl_user_dmt_beneficiary as a where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.register_mobile = '$mobile' AND a.status = 1";
				$beneficiaryList = $this->db->query($sql)->result_array();

				$data = array();
				if($beneficiaryList)
				{
					foreach ($beneficiaryList as $key => $list) {
						
						$data[$key]['benId'] = $list['beneId'];
						$data[$key]['account_holder_name'] = $list['account_holder_name'];
						$data[$key]['verifiedName'] = $list['verified_name'];
						$data[$key]['account_no'] = $list['account_no'];
						$data[$key]['ben_mobile'] = $list['ben_mobile'];
						$data[$key]['bank_name'] = $list['bank_name'];
						$data[$key]['ifsc'] = $list['ifsc'];
						$data[$key]['is_verify'] = isset($list['is_verify']) ? $list['is_verify'] : 0;
						$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
						
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

				else

				{
					$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);

				}
				

			}

			else

			{
				$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);

			}

	    	
		}

        }
    	
    	
		log_message('debug', 'dmtBeneficiaryList List API Response - '.json_encode($response));	
		echo json_encode($response);
    }



    public function dmtBankList()
    {
    	$account_id = $this->User->get_domain_account();
    	$post = $this->input->post();
		log_message('debug', 'dmtBankList List API Post Data - '.json_encode($post));	
		$userID = isset($post['userID']) ? $post['userID'] : 0;
		$response = array();
    	
    	$activeService = $this->User->account_active_service($post['userID']);
		if(!in_array(8, $activeService)){
			$response = array(
				'status' => 0,
				'message' => 'Sorry!! this service is not active for you.'
			);
		}
		else{

	    	// check user valid or not
			$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
			if($chk_user)
			{
				$bankList = $this->db->get('paysprint_dmt_bank_list')->result_array();

				$data = array();
				if($bankList)
				{
					foreach ($bankList as $key => $list) {
						
						$data[$key]['bank_id'] = $list['id'];
						$data[$key]['bank_name'] = $list['title'];
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
		log_message('debug', 'dmtBankList List API Response - '.json_encode($response));	
		echo json_encode($response);
    }



    public function getDmtTransferHistory(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		log_message('debug', 'Dmt Transfer History API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

    	if($fromDate && $toDate){

    	  $count = $this->db->order_by('created','desc')->get_where('user_dmt_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,"DATE(tbl_user_dmt_transfer.created) >=" => $fromDate,"DATE(tbl_user_dmt_transfer.created) <=" => $toDate))->num_rows();
    	  
    	  $limit_start = $limit - 50; 
	                     
	      $limit_end = $limit;		
    	  	
		  $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_dmt_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,"DATE(tbl_user_dmt_transfer.created) >=" => $fromDate,"DATE(tbl_user_dmt_transfer.created) <=" => $toDate))->result_array();
		}
		else{


		  $count = $this->db->order_by('created','desc')->get_where('user_dmt_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->num_rows();
    	  
    	  $limit_start = $limit - 50; 
	                     
	      $limit_end = $limit;		
    	  	
		  $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_dmt_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->result_array();

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
			 'pages' => $pages
			);
		}
		else{

			$response = array(
			 'status' => 0,
			 'message' => 'Sorry!! record not found.',
			);
		}
		log_message('debug', 'Get DMT Transfer List API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function getMatmHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$post = $this->input->post();
		log_message('debug', 'Matm History API Get Data - '.json_encode($post));	
		$userID = isset($post['userID']) ? $post['userID'] : 0;
    	$response = array();
    	$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

        // check user valid or not
		$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
		if($chk_user)
		{
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_matm_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$userID'";
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
					
					
					$data[$key]['member'] = $list['user_name'].' ('.$list['user_code'].')';
					$data[$key]['amount'] = $list['amount'];
					$data[$key]['txn_id'] = $list['txn_id'];
					$data[$key]['txn_type'] = $list['txn_type'];
					$data[$key]['bank_rrn'] = $list['bank_rrn'];
					$data[$key]['mpos_number'] = $list['mpos_number'];
					$data[$key]['card_number'] = $list['card_number'];
					$data[$key]['card_number'] = $list['card_number'];
					$data[$key]['card_number'] = $list['card_number'];
					$data[$key]['card_holder_name'] = $list['name'];
					$data[$key]['card_holder_mobile'] = $list['mobile'];

					if($list['status'] == 1) {
					 $data[$key]['status'] = 'Pending';
					}
					elseif($list['status'] == 2) {
						$data[$key]['status'] = 'Success';
					}
					elseif($list['status'] == 3) {
						$data[$key]['status'] = 'Failed';
					}
					elseif($list['status'] == 4) {
						$data[$key]['status'] = 'Hold';
					}

					$data[$key]['date'] = date('d-M-Y H:i:s',strtotime($list['created']));
					
				}
			}

			if($data)
			{
				$response = array(
					'status' => 1,
					'message' => 'Success',
					'data' => $data,
					'pages' => $pages
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
		log_message('debug', 'Matm History API Response - '.json_encode($response));	
		echo json_encode($response);
    }





	public function cibGenerateToken()
	{
		// Create Data
		$data = array 
		(
		    "email"=>"boardmytrip@gmail.com",    
		    "password" => "SljLX4a3OC9c", 
		);

		// Generate JSON
		$json = json_encode($data);
		
		// Create Header
		$header = array
		(
		    'Content-type: application/json',
		    'X-Requested-With: XMLHttpRequest'
		);

		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, "https://api.cogentmind.tech/v1/auth/login");

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
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

		// Set Options - Close

		// Execute
		$result = curl_exec($curl);

		// Close
		curl_close ($curl);

		log_message('debug', 'CIB Token Create API Post Json - '.$json);	

		log_message('debug', 'CIB Token Create API Result - '.$result);	
		echo $result;

		// error response
		/*{"message":"Unauthorized"}*/
		
		// success response
		/*{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZGFiNzI2YjllYmU3OTZiMThlYWZlYTZjMzc3Y2ZkMzVmOTRiOWIwNWU3YWM4NmEzZTkwN2M1NjM5OGE5Yjc2ZWQ2ODkyNmEwNTFmZTQwYmUiLCJpYXQiOjE2MzYzNzM1MjQsIm5iZiI6MTYzNjM3MzUyNCwiZXhwIjoxNjY3OTA5NTI0LCJzdWIiOiIxNSIsInNjb3BlcyI6W119.lQke9YX4iq0agg_g1g3N6Q4_DbGkZs-qKXQ4UQ0UVkFAVJZLdk-XCDlK_yKMyPSjXKtBji_P31Z4zL2t2nQUCduq7KzN9SH798fwLMp6IAqsuPBkrISnx5zSWVz10mpi5eXFnYKf27diH8FlZ_PiJAKZJ69GJeVF7Ir6L4X_vaTxLOu9ZGBQHK07qi4g6nCcPe6JGzKUD0V6AXG85AYDv6ztcBVqNAcydgUKWhmPiLxgDx851IlhUTLomhQ593f1BNzVR9_xyZynwTJdELTufe3QVn9aYi3fTPQI77T7Y5jZhVAqbWsP_vewggYP4_eSEDeaeU5PQkyZNZj1Ne9uQ0aZG1R4oisZE9Ecy2cTQdYW_1kvzVkwXak8KFS4IaH_u7VkfayUoaJ8pY0wm4UuFBh-8b-D9E1ajGPmpIx_GyOm0wvemr280xezuFFAWQdmdP6U9wfXKclMAwj9DwEyuEpITyzP_XdWFuuYqHES0IikfvV6whcfLaI2Xfg1LMdre4kyZxhjB3oCagBg_3Veu3W-1OCeoTJdPcWt4zXINBEhOPL651zwWhra6Btzt-Kz5zJMGIKdzgfxOX4A88CydL-Eje2u9fIxyFEiXYe647_rok2Dpo-gM4KU8a1ycP8ND0UmopT47W6xh8vqSAD6M3IwD8c0OAgMVOA1XQMlwkg","token_type":"Bearer","expires_at":"2022-11-08 12:12:04"}*/
	}

	public function cibTransaction()
	{
		$transaction_id = time().rand(1111,9999);
		// Create Data
		$data = array 
		(
		    "AGGRID"=>"BAAS0007",    
		    "AGGRNAME" => "COGENT", 
		    "CORPID" => "568464421", 
		    "USERID" => "USER1", 
		    "URN" => "URN568464421", 
		    "UNIQUEID" => $transaction_id, 
		    "DEBITACC" => "348005500557", 
		    "CREDITACC" => "023501546776", 
		    "IFSC" => "ICIC0000011", 
		    "TXNTYPE" => "TPA", 
		    "AMOUNT" => "10", 
		    "PAYEENAME" => "Sonu Jangid", 
		    "REMARKS" => "Fund Transfer", 
		    "CURRENCY" => "INR", 
		    "CUSTOMERINDUCED" => "N", 
		);

		$plainText = json_encode($data);
		$sslEncrypt = $this->sslEncrypt($plainText);
		$key = "T1KiSsx5AOKKgx8dSuvu5l8dAWU4Bfq0l4i";
		$encryptedData = $this->encrypt($sslEncrypt, $key);
		$payload = json_encode($encryptedData); 

		
		// Create Header
		$header = array
		(
		    'Content-type: application/json',
		    'key: WmXsYXBTa4wgR7fWjGHKkLwerID2eAi70Pn',
		    'X-Requested-With: XMLHttpRequest',
		    'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMDhiYWU5MzY4NDA4ZGYyMWJiZjYxYjE3NDEyNTdiOWY0NjZhNmM1ZjMyYzZkNDUyNGQ2Y2IzY2IwZjliZGM2OGYzZjRlZjMwZmM4YzM3YmEiLCJpYXQiOjE2MzY2MTM3MjQsIm5iZiI6MTYzNjYxMzcyNCwiZXhwIjoxNjY4MTQ5NzI0LCJzdWIiOiIxNSIsInNjb3BlcyI6W119.KW5ZBePLpddU_bd3Qc4SScctY15gHZKoDR3w3Ev1F-hkO1FwanDaTcvjz8DsoxwjyxCGNdlhV-5NkDoHm45cdAKnhz82JU0vx0xRy7_9UlJhDh-tFrZp6ikz6kRL9bZwENnMcb0m5GPq8wrwoOy8F4yvUkpnIxJxjgtUcdd7A8t1MHOQNQ18P5bniTphdSp2uECVeH4lqnmwiR4xjt2Ii0Hicp06BGTjdV0x3zd4AUVCteK7urkrwDaxZfXL1_LRALk__m3bC_aiZyzxG-z_bW49xmWEYPRP7miiafUWqisignR4sassv5B1S2Vp1FgE6w83BUlW4ImPzm2Pw11eN2SYLlIJUwqkigz6My7s4nkB4JYaNkp2tidtM4gpB4brWEr0Upg40w6NNEV_TbY9d7Wt1ocWYEvNLvoPrcgu9pJGS6_a3rVvexa4YDXXV4G5Bs1A_V9dpdIyPpTas1T2uUSXVF03TlqQnE2NZXOoG7uJUOnibs0wxKzMyRJBHJMWvbjEyNCH_FZzrJU2WAVZzKXvUrcIIbef1QzITP67sYs7euYjGjJ39x29UubIIiI0jTHA4yOjUU8GTAzJdVgiS_4IQYULdJ-9xkkqpFuOKl_nF2n_U974yelwK4-kiteICeeX53UEYXOtGTb6yZNVSgNXWrCwJMUy-nXU1Sq6Jqg'
		);

		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, "https://api.cogentmind.tech/v1/cibpayout-transaction");

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
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		$result = curl_exec($curl);

		// Close
		curl_close ($curl);

		log_message('debug', 'CIB Txn API Post Json - '.$json);	

		log_message('debug', 'CIB Txn API Result - '.$result);	
		echo $result;
		echo "<br />";
		$response = $this->sslDecrypt($this->decrypt(json_decode($result), $key)); 
	    echo $finalResponse = json_decode($response);
	    
	    // error response
		/*{"message":"Unauthorized"}*/
		
		// success response
		/*{"status":200,"message":"SUCCESS","data":{"CORP_ID":"568464421","USER_ID":"USER1","AGGR_ID":"BAAS0007","AGGR_NAME":"COGENT","REQID":"654937198","STATUS":"SUCCESS","UNIQUEID":"16366141251294","URN":"URN568464421","UTRNUMBER":"025450147131","RESPONSE":"SUCCESS"}}*/
	}
	
	public function cibDecodeResponse()
	{
	    $key = "T1KiSsx5AOKKgx8dSuvu5l8dAWU4Bfq0l4i";
	    $result = '"ed1aa071eb77be4d4f5d699b335b5b33c0c3c47124f61d28efe2124569aca054d9ed6632cedb18915117988cfb08df812bc0a16361f642abc20d14582f3c3253dbe4b9b9853ab9ca3234c368c211a792023a458e9fb7c1972e6e0f55ad081bba4ab1a8d71d746b013d22363b087713bcba8c1adf127348bdd4618fcd2082b42a5179aadd190fcce21a26c12e7c1934a80aa946abb7c921e474880e2cea78d4c8faffb026b396c8dc49ba2fba7bdb010e2aa075cff825cc5cdc0aa4f390bd21aca51cf013a8a3558ebc6c6f8e2fed3a90814de798fd8b639599613d39ef3de79aa0e5fb8c047c80cf7df3f7f978c91501e7fabd8600cd7224d2f9f10b3406a5e229b23a8a44cbbd1fb55d88dbe9a6f69c6999fd47d44aca102fc2d28328b826d28a1edc5748243c8b072204a69c977828455302b723e32ef2f705868674c11abec0fce54105b6e729a8797c19a2ecd52c3a73f42e178d007e381fd259d4793b63db532a7e717e70677d54df6c40083742b43c16758c99ecf5b9afeb7ceb4089bcd8c837bc4fa88539b53a3d7adb690f244c7a6e996e25ec6ed3c0ea5ca4e4a3d3bd643fd0dabfd4384d41de40d14c5491e514308fd4b4381db040abe942ce48d22804e9b6c4da939bf3437b5328632b5eb8d586a95c7082a5c46c7d01441b90e3cdc885410ed307a1a039eafc2681097521b44256ed463648b6ca443379936524c5d3e74f8ad9b81d4796ba32ca2e0960"';
	    
	    $response = $this->sslDecrypt($this->decrypt(json_decode($result), $key)); 
	    echo json_decode($response);
	    echo "<pre>";
	    print_r($response);
	    die;
	}

	public function sslEncrypt($dataToEncrypt)
	{
		
		$public_key = CIB_CERTIFICATE;
		
		openssl_get_publickey($public_key);
		openssl_public_encrypt($dataToEncrypt,$encryptedText,$public_key);
		return $encryptedText;
	} 

	public function encrypt($plainText, $key)
	{
		$secretKey = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$openMode = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
		$encryptedText = bin2hex($openMode);
		return $encryptedText;
	} 

	public function decrypt($encryptedText, $key)
	{
		$key = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$encryptedText = $this->hextobin($encryptedText);
		$decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
		return $decryptedText;
	} 

	public function sslDecrypt($dataToDecrypt)
	{
		$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIIJKgIBAAKCAgEAp8gRxO1grzR6UHuCiJ0nRN5RQIu7CEzbtWBK90NJZcso5UZz
NFRBFG7n+byyYi74mt93N//AUPz3qRNwW6qB4YC3YvSgvVNxovh9OKMwXODiNOzg
xyb42CZ9Cw7SIuqBvB6bIOl90ChoHdr/I8A9xg0fTxOIkgnVmmeUyVhaCcfXiIGt
6wiDMmCBrgHlhT6wwrzbnXstg6nHtiN/8BDaRibweRiUdkFq4iGZOa2trSjkX+FW
QBHIeNzJUOkQ2PjL4slKkQcPaHqhLU104IgYzi9hWKaDGVyRe75Jgt/RhSEENBm5
pUbLhfpy8XW6gBZxwYIm3dFcXQBrefL29BpT5lw4+0ON4Qd2yXSMID4TZaM4rjO0
8YT2H8xsnOkf7l+xUwAbVBQq/YJRKKTDJGM0xIh5ewIE7Uhy0DZCsnOQEn0s7SZK
fH2vDA682Btg6b0HjpqvbRAhrB35RUZsvc9V5fq4TqVruDCkHxVler/UpRUb1Nt5
tsiPFySpSNDsE5V9deei7rMiRoXXm366CkFhwAoPqjOCzJUUpRqfBcimTlvmGlGV
urfj9m67812kMWXiiDPIzRDLH6Pu6Pwm1vROtKLYboiUe+LJIBv5/puqX5Vd7HpK
a1N1FkhWzOFbn3bxJ7cH9rqFm/SShAMZILWb+GfSMyg4LqY1X2hx5v8VanUCAwEA
AQKCAgEAk4FoZdpP4xje4/0B8LtBN+HAS1/NSenQSzBiF+p3D+BBjviV9g6QvLcY
iM3lgj5LYFVQSdI2ML2FuoaVhHFuCGQSVhQ9cNV4lU+jP3Tw4ubu2JrcrxnLMrT7
wAoCoqHK9yltNs0wSB4OExlir/qmFIWa3fmNWt78hOfFvhJH7ktcaO2hin2MYnDH
8cRHFhLccgh5h4UwqY1NQAsLwnH+hChdyAjdFO5EWpQxUq0ngJHv3X+NRXyc4+0v
rEuK/UYWccaIIOK1ICSXqO1s8K2WKUcsbTTPEy/303Oiy0WcvU8ek/N97BlGev6J
qVDYax3QWtDi/KbAbh8dXjRF+qte0D2HNhZgT1YuShf1KeLCpbIPC2EFWESgYZyP
yoS2Q+poRp9sNt9BEZ6YR51/9ROdyHoo3H9jipoqdvbxQJyxcXsB4rT9tihFsAdB
Z+sh1rtMJzkrgwbaENV/KS3pzGbTd0ZIE4j9EJiYoO5VH0xmzJQUdL3BER0iQ3g4
j8/Z1LUyGtuPpBZXqzSHibLsdn6JmkhjWLU5IdAzff4E3m+OB8Qhc1BtkRZfOl23
Wdk5j/mLhlkEedkt99rZholsP1WOBqA9iH5wmRFr5kvvssYqc0rUtStLfWFBs/dW
VYvHTd3rm3NhzbeednKFTqBVrQFRtzL6YsCwN1U/PsIvBiDO6C0CggEBANt5WWSU
Yl4BCuby3vDp1l2fHd21knKRGeBPpnNdQ1dVP3nOQRS4CtiQbnqxRkAJI4KaHEK7
J8zdf/VyVKbHRItK4nheCBm/tGqcEVE1SOMGLVfHcYkiCiI3BXj20qbSVAdM3JnO
GjfcEim1xckMHMN+TPMic0jAxwYiYr15SNbguonOoa99ejcKXqncq5gBcT3HEdb7
CSjILrIm7OjEB52k/wfh0lSr8vtE1uuGDNiyjhu42It+r2zaIsHwsRQ4/i0sJ7No
LC+GX7r8kVbayOvwWtUnjkHOW3LOecTEeZLV+c3vqbkywe63SdSGA1mD29cS4pzi
l2mDJouKzuGdS2sCggEBAMO0XuSBKIHmYqwnJtRedABQln1Hz5lJBqEIAjEsOIe2
XFA4UIh9JBlQ3/VnHZUw07lKCJJQidE04jMMUvM2el4VwI4eKw+cAW+Kp9YjCTGK
QdjSt4F79j61O3c7mwKfnV7u3qctDnyc1rJjMX5p+m/Qr3tMYOqU5ETYKdqoaeXB
z0KJkIBFMDTS5/KldcCpiAw8OHnh40GxLtzLoESsnqCs6In6R78R/qg6jr4o/xht
tLLgDMXZZ8z5FmLc2ddDewt+pHeodqqbDRX5/34vH2uTPOEDa8LkVlo79yo8Zy1s
tYzwU1NsTTcyzP97BcMlH4sukmZqnrnli3MwLq66eZ8CggEAMI70vFAoQ2wvoVFz
ChJyn1wpG3ik4jxAYWS+CyBDWfs+hBCiTZc6rxelmffG9zwOY0L9pbYK5ETNntyg
5hWIkNkMql0Dpc7IeB33puQHMFOZjKZP9GtXmqJZz52slcRLWyIiXNVA78L9McVJ
8WWAp7A2DkU9BIfCgRTyi8Fd4EzweLUDCPTWKX9d2m88d/E5wNVemRYJvMAttLTw
Db/xf2uWEYRhKOKya+2bL5kFFpzK3E9VeeeZoJfSwN4kD7lcY1o1nngZ7pnobFKd
RX46nhkbv7V0wBKMISaVwndF/rrg/jNcdeFJDyv2ZdMQwqlt5nQDN3razTl7ObyI
cVp/6QKCAQEAmQ5c1Isq2gULkKYCGT9Rq9lbCNn7w184fwJbbIewInt90QNqAIUW
kXIN5chie4a3X4dGEuBIGMUqT1BJI4uswh5y/PMdLFUPTmP7hV7bVtJRUzjhabRA
TqTAwCxuu+uHUXKx0b9MQCsNQnCPidVqlr54L475kR0nNax1d1wVjio4ZUpfJ+J0
pCt6WewLnsU38JG8fZ4rdPoUs3vReUQjv3fbWeXS7N0u8/TwJEq9zyYll2vgsW+p
XcZFOsaM3G0bM081Y5vuStl+r3xY1CAYi7KYf0aEpeScoG+bi324F++YQYTHNkxS
S2RArWJSjF9hPyIKP20NfEfI+ypoqCCQ8wKCAQEAnu1cRJko1r0jLA388JmNTD4s
WP1cLMBI9FJUZtm9vTWk5Oz99qUHpG6BEZ9K2/9NCUc0EJP4VdVyH805v0q1qFaG
vBbqChikTrs/82ouobIlLZtnTWVzjGfXZ6kD433REI324X8kBRszpzEhMDr8ZhgC
ca0T0ckNKXtRo6ENRrBzBB2Y0+3d241WFU1wqPk9inJ00EQIRnAFuyiMgImPs7vM
qS+nZmlxepH+QyprhaYJ4WHePRub7CZXiCPTosuEBbV5d36uLYRf3kK4D5vsfClE
qJSWl8+AA1uYYorA5jM9HkEPMVCaa940OMx1zWAfMiwHmqjtzNcKEy7i609meg==
-----END RSA PRIVATE KEY-----';
		$private_key = openssl_get_privatekey($private_key, "");
		openssl_private_decrypt($dataToDecrypt,$decryptedText,$private_key);
		return $decryptedText;
	} 

	public function hextobin($hexString)
	{
		$length = strlen($hexString);
		$binString = "";
		$count = 0;
		while ($count < $length)
		{
			$subString = substr($hexString, $count, 2);
			$packedString = pack("H*", $subString);
			if ($count == 0)
			{ $binString = $packedString; }
			else
			{ $binString .= $packedString; }
			$count += 2;
		}
		return $binString;
	} 


	public function upiSendRequest()
	{
		$txnid = time().rand(1111,9999);
		$data = [
			"merchantId" => "420661",
			"merchantName" => "Board My Trip",
			"terminalId" => "6012",
			"subMerchantId" => "8104758957",
			"subMerchantName" => "Sonu Jangid",
			"merchantTranId" => $txnid,
			"billNumber" => $txnid,
			"payerVa" => "8104758957@ybl",
			"amount" => 1,
			"note" => "Test",
			"collectByDate" => date('d/m/Y H:i A', strtotime('+1 day'))
		];
		
		$plainText = json_encode($data);
		
		$pub_key_string = UPI_BANK_CERTIFICATE;

		openssl_get_publickey($pub_key_string);
		openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
		$key = "qktGGZJa9AuviUAZXLdnXZp4SonQzSm36jg";
		$hexString = md5($key);
		$length = strlen($hexString);
		$binString = "";
		$count = 0;
		while ($count < $length)
		{
			$subString = substr($hexString, $count, 2);
			$packedString = pack("H*", $subString);
			if ($count == 0)
			{ $binString = $packedString; }
			else
			{ $binString .= $packedString; }
			$count += 2;
		} 
		$secretKey = $binString;
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
		$encryptedData = bin2hex($openMode);
		$payload = json_encode($encryptedData);
		$header = array(
		 'Content-Type:application/json',
		 'Key:WmXsYXBTa4wgR7fWjGHKkLwerID2eAi70Pn',
		 'X-Requested-With:XMLHttpRequest',
		 'Authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNDZjOGEzOWQ1NmI5YjdkNWRhMWEyMDQ4ZDY3NDM0ZGVlOTcwZmY0YTkxNDViMTZiYzIyMWEwMDczYzdiNTE3NDQyY2ZhZDYwNWY0OTdlODAiLCJpYXQiOjE2Mzc2NjQwOTYsIm5iZiI6MTYzNzY2NDA5NiwiZXhwIjoxNjY5MjAwMDk2LCJzdWIiOiIxNSIsInNjb3BlcyI6W119.y4d1UssmQknktW_Y8tf_ZOlnihuGB5VeTOqTW9C7tw8pBTZDUlkv3h-_I4uV42YJIa59Xbg7H05z89353sh0wFkFkMGI5kIUVhUc7NlaQKB24wlN_au7mXYFzTpyBPyZgLYsGUsw6URIB5XG8-CqFSx9lsXwktydhzKpkBdkcyHj8wuFgL6rJ7V6UIPzMsdvfPToNs826OUGPXAVF3Z2iAxqgShO32Yfh3Th-aS8TB2YF1h6l966T-_pZR8E-eTrLMoJ1uQ3UMLOeAOa4gN7w4xWM8hSxJlPj9cMwgyIKgtSn0MZNwsMjx-vhDDcB8paQ9pv1U10JPTDdfrtnwlLekzbnotkFFz_rp0xdhy4Pfi2U6s7dzsOSTY7sKnEzct2Q3aZcBjgAuMst1FyP-kEPNILxozfdzyiszHC9noWZEPWfN6sjqT6UnCFHFmgrJwvDmQJ1gMCNjKQ4Q52CrcAT77oKAmyYCQUYBf_O4kIsMXP954TCYTE5O47U_MIc94qq8AUDyh5q8AkJJCWMQS-SF9Loqpw1FP2y6wXoBMiqy1i4lS_cg7X3LzfXW2kNLdQYBtUanFGx8_tL5wqoXrgYgQnTN4QVX0G0YXAgtbWCdcpe_0DLptPFS5Z43jnPuqtT-EaBaCNLXTm0dN9kEZ7Ed3rLvkNVoc3p2uRli9WL_s'
		 ); 
		
		$httpUrl = 'https://api.cogentmind.tech/v1/upicollection-sendrequest';
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $httpUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $payload,
			CURLOPT_HTTPHEADER => $header
		));

		$result = curl_exec($curl);
		curl_close($curl);
		$decodeResult = json_decode($result);
	    $decryptData = $this->decrypt($decodeResult, $key);

		$private_key = UPI_PRIVATE_CERTIFICATE;

		$private_key = openssl_get_privatekey($private_key, "");
		openssl_private_decrypt($decryptData,$response,$private_key);
	//echo $response ; 
		echo json_decode($response);
		die;

		// ERROR RESPONSE
		/*{"status":200,"message":"PSP is not registered","data":{"response":"5008","merchantId":"","subMerchantId":"","terminalId":"","success":"false","message":"PSP is not registered","merchantTranId":"","BankRRN":""}}*/

		// SUCCESS RESPONSE
		/*{"status":200,"message":"Transaction initiated","data":{"response":"92","merchantId":"420661","subMerchantId":"8104758957","terminalId":"6012","success":"true","message":"Transaction initiated","merchantTranId":"16376709769700","BankRRN":"132785139496"}}*/
	}

	public function upiDynamicQr()
	{
		$txnid = time().rand(1111,9999);
		$data = [
			"merchantId" => "420661",
			"terminalId" => "6012",
			"merchantTranId" => $txnid,
			"billNumber" => $txnid,
			"amount" => 1
		];
		
		$plainText = json_encode($data);
		
		$pub_key_string = UPI_BANK_CERTIFICATE;

		openssl_get_publickey($pub_key_string);
		openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
		$key = "qktGGZJa9AuviUAZXLdnXZp4SonQzSm36jg";
		$hexString = md5($key);
		$length = strlen($hexString);
		$binString = "";
		$count = 0;
		while ($count < $length)
		{
			$subString = substr($hexString, $count, 2);
			$packedString = pack("H*", $subString);
			if ($count == 0)
			{ $binString = $packedString; }
			else
			{ $binString .= $packedString; }
			$count += 2;
		} 
		$secretKey = $binString;
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
		$encryptedData = bin2hex($openMode);
		$payload = json_encode($encryptedData);
		$header = array(
		 'Content-Type:application/json',
		 'Key:WmXsYXBTa4wgR7fWjGHKkLwerID2eAi70Pn',
		 'X-Requested-With:XMLHttpRequest',
		 'Authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNDZjOGEzOWQ1NmI5YjdkNWRhMWEyMDQ4ZDY3NDM0ZGVlOTcwZmY0YTkxNDViMTZiYzIyMWEwMDczYzdiNTE3NDQyY2ZhZDYwNWY0OTdlODAiLCJpYXQiOjE2Mzc2NjQwOTYsIm5iZiI6MTYzNzY2NDA5NiwiZXhwIjoxNjY5MjAwMDk2LCJzdWIiOiIxNSIsInNjb3BlcyI6W119.y4d1UssmQknktW_Y8tf_ZOlnihuGB5VeTOqTW9C7tw8pBTZDUlkv3h-_I4uV42YJIa59Xbg7H05z89353sh0wFkFkMGI5kIUVhUc7NlaQKB24wlN_au7mXYFzTpyBPyZgLYsGUsw6URIB5XG8-CqFSx9lsXwktydhzKpkBdkcyHj8wuFgL6rJ7V6UIPzMsdvfPToNs826OUGPXAVF3Z2iAxqgShO32Yfh3Th-aS8TB2YF1h6l966T-_pZR8E-eTrLMoJ1uQ3UMLOeAOa4gN7w4xWM8hSxJlPj9cMwgyIKgtSn0MZNwsMjx-vhDDcB8paQ9pv1U10JPTDdfrtnwlLekzbnotkFFz_rp0xdhy4Pfi2U6s7dzsOSTY7sKnEzct2Q3aZcBjgAuMst1FyP-kEPNILxozfdzyiszHC9noWZEPWfN6sjqT6UnCFHFmgrJwvDmQJ1gMCNjKQ4Q52CrcAT77oKAmyYCQUYBf_O4kIsMXP954TCYTE5O47U_MIc94qq8AUDyh5q8AkJJCWMQS-SF9Loqpw1FP2y6wXoBMiqy1i4lS_cg7X3LzfXW2kNLdQYBtUanFGx8_tL5wqoXrgYgQnTN4QVX0G0YXAgtbWCdcpe_0DLptPFS5Z43jnPuqtT-EaBaCNLXTm0dN9kEZ7Ed3rLvkNVoc3p2uRli9WL_s'
		 ); 
		
		$httpUrl = 'https://api.cogentmind.tech/v1/upicollection-dynamicqr';
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $httpUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $payload,
			CURLOPT_HTTPHEADER => $header
		));

		$result = curl_exec($curl);
		curl_close($curl);
		$decodeResult = json_decode($result);
	    $decryptData = $this->decrypt($decodeResult, $key);

		$private_key = UPI_PRIVATE_CERTIFICATE;

		$private_key = openssl_get_privatekey($private_key, "");
		openssl_private_decrypt($decryptData,$response,$private_key);
	//echo $response ; 
		echo json_decode($response);
		die;

		// ERROR RESPONSE
		/*{"status":400,"message":"Empty parameters not allowed.","data":""}*/

		// SUCCESS RESPONSE
		/*{"status":200,"message":"Transaction initiated","data":"https:\/\/cogentmind.tech\/api\/dynamicQRAPIWLCollection\/Board My TripEZV2021112517524300907870.png"}*/
	}

	public function upiStaticQr()
	{
		$txnid = time().rand(1111,9999);
		$data = [
			"qrCount" => 2
		];
		
		$plainText = json_encode($data);
		
		$pub_key_string = UPI_BANK_CERTIFICATE;

		openssl_get_publickey($pub_key_string);
		openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
		$key = "qktGGZJa9AuviUAZXLdnXZp4SonQzSm36jg";
		$hexString = md5($key);
		$length = strlen($hexString);
		$binString = "";
		$count = 0;
		while ($count < $length)
		{
			$subString = substr($hexString, $count, 2);
			$packedString = pack("H*", $subString);
			if ($count == 0)
			{ $binString = $packedString; }
			else
			{ $binString .= $packedString; }
			$count += 2;
		} 
		$secretKey = $binString;
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
		$encryptedData = bin2hex($openMode);
		$payload = json_encode($encryptedData);
		$header = array(
		 'Content-Type:application/json',
		 'Key:WmXsYXBTa4wgR7fWjGHKkLwerID2eAi70Pn',
		 'X-Requested-With:XMLHttpRequest',
		 'Authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNDZjOGEzOWQ1NmI5YjdkNWRhMWEyMDQ4ZDY3NDM0ZGVlOTcwZmY0YTkxNDViMTZiYzIyMWEwMDczYzdiNTE3NDQyY2ZhZDYwNWY0OTdlODAiLCJpYXQiOjE2Mzc2NjQwOTYsIm5iZiI6MTYzNzY2NDA5NiwiZXhwIjoxNjY5MjAwMDk2LCJzdWIiOiIxNSIsInNjb3BlcyI6W119.y4d1UssmQknktW_Y8tf_ZOlnihuGB5VeTOqTW9C7tw8pBTZDUlkv3h-_I4uV42YJIa59Xbg7H05z89353sh0wFkFkMGI5kIUVhUc7NlaQKB24wlN_au7mXYFzTpyBPyZgLYsGUsw6URIB5XG8-CqFSx9lsXwktydhzKpkBdkcyHj8wuFgL6rJ7V6UIPzMsdvfPToNs826OUGPXAVF3Z2iAxqgShO32Yfh3Th-aS8TB2YF1h6l966T-_pZR8E-eTrLMoJ1uQ3UMLOeAOa4gN7w4xWM8hSxJlPj9cMwgyIKgtSn0MZNwsMjx-vhDDcB8paQ9pv1U10JPTDdfrtnwlLekzbnotkFFz_rp0xdhy4Pfi2U6s7dzsOSTY7sKnEzct2Q3aZcBjgAuMst1FyP-kEPNILxozfdzyiszHC9noWZEPWfN6sjqT6UnCFHFmgrJwvDmQJ1gMCNjKQ4Q52CrcAT77oKAmyYCQUYBf_O4kIsMXP954TCYTE5O47U_MIc94qq8AUDyh5q8AkJJCWMQS-SF9Loqpw1FP2y6wXoBMiqy1i4lS_cg7X3LzfXW2kNLdQYBtUanFGx8_tL5wqoXrgYgQnTN4QVX0G0YXAgtbWCdcpe_0DLptPFS5Z43jnPuqtT-EaBaCNLXTm0dN9kEZ7Ed3rLvkNVoc3p2uRli9WL_s'
		 ); 
		
		$httpUrl = 'https://api.cogentmind.tech/v1/upicollection-staticqr';
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $httpUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $payload,
			CURLOPT_HTTPHEADER => $header
		));

		$result = curl_exec($curl);
		curl_close($curl);
		$decodeResult = json_decode($result);
	    $decryptData = $this->decrypt($decodeResult, $key);

		$private_key = UPI_PRIVATE_CERTIFICATE;

		$private_key = openssl_get_privatekey($private_key, "");
		openssl_private_decrypt($decryptData,$response,$private_key);
	//echo $response ; 
		echo json_decode($response);
		die;

		// ERROR RESPONSE
		/*{"status":400,"message":"Empty parameters not allowed.","data":""}*/

		// SUCCESS RESPONSE
		/*{"status":200,"message":"Static QR generated.","data":["https:\/\/cogentmind.tech\/api\/staticQRAPIWLCollection\/Board My TripNU3zCL8X4Vwy.png","https:\/\/cogentmind.tech\/api\/staticQRAPIWLCollection\/Board My TripZRb8YBodMYae.png"]}*/
	}

	public function currentAccountApiTest()
	{
		// Create request	
		$request = [
	        "user" => "Cogentmind",
	        "passCode" => "31d48f6ee335f5d941b7a50dfdeb4f37",
	        "mobileNo" => "8104758",
	        "emailId" => "sonujangid2011@gmail.com",
	        "p_CATEGORY_TYPE" => "Private Ltd",//"Proprietorship",//"Individual",        
	        "data"=>array
	        (
	            "p_FNAME" => "Sonu",
	            "p_LNAME" => "Jangid",
	            "p_PERPCODE" => "302021"//"p_COMMPCODE" => "496227"
	        )
	    ];

	    // Convert to json
	    $json_enc = json_encode($request);

	    // Create header
	    $header = [
	        'Content-type:application/json'
	    ];    
	    
	    // Create url
	    $url = 'https://cadigital.icicibank.com/caSmartFormSrv/sendBcReq';
	    
	    $curl = curl_init();
	    
	    curl_setopt_array($curl, array(
	        CURLOPT_RETURNTRANSFER => true,        
	        CURLOPT_CUSTOMREQUEST => "POST",
	        CURLOPT_POSTFIELDS => $json_enc,
	        CURLOPT_HTTPHEADER => $header,
	        CURLOPT_URL => $url
	    ));
	    
	    // Get response
	    echo $response = curl_exec($curl);
	  
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$err = curl_error($curl);    
	    curl_close($curl);

	    //SUCCESS RESPONSE
	    /*{"status":"Success","message":"Your Application Number : 777-000471807 and Tracking Id : WEB111111111582327","errorCode":"0","p_APPLICATION_NO":"777-000471807","trackerId":"WEB111111111582327","webUrl":"https://cadigital.icicibank.com/SmartFormWeb/apps/services/www/SmartFormWeb/desktopbrowser/default/index.html?trackerId=WEB111111111582327#/login"}*/


	}

	
	public function categoryInstantApi()
	{
	   
							
		            $api_url = 'https://api.instantpay.in/marketplace/utilityPayments/category';

		            $request = array(
		                'token' => '8eb2607eab4812cc1ff67b5f3dcf6e58'
		            );

		            $header = array(
		                'content-type: application/json'
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
		            
		             
	                $bmPIData   = simplexml_load_string($output);
					echo $jsonResponse = json_encode((array) $bmPIData);
						die;
						
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","status":"Transaction Successful","data":{"item":[{"category_key":"C00","category_name":"Mobile (Prepaid)","billing_model":"P2A"},{"category_key":"CP0","category_name":"Mobile (Prepaid)","billing_model":"P2P"},{"category_key":"C01","category_name":"Mobile (Postpaid)","billing_model":"P2A"},{"category_key":"C02","category_name":"Landline","billing_model":"P2A"},{"category_key":"C06","category_name":"TV - Cable","billing_model":"P2A"},{"category_key":"C03","category_name":"TV - DTH","billing_model":"P2A"},{"category_key":"CP3","category_name":"TV - DTH","billing_model":"P2P"},{"category_key":"C04","category_name":"Electricity","billing_model":"P2A"},{"category_key":"C05","category_name":"Broadband","billing_model":"P2A"},{"category_key":"C07","category_name":"Gas (Piped)","billing_model":"P2A"},{"category_key":"C08","category_name":"Municipal & Water","billing_model":"P2A"},{"category_key":"C09","category_name":"Education Fee","billing_model":"P2A"},{"category_key":"C10","category_name":"FASTag","billing_model":"P2A"},{"category_key":"C11","category_name":"Insurance","billing_model":"P2A"},{"category_key":"C13","category_name":"Loan Repayments","billing_model":"P2A"},{"category_key":"C14","category_name":"Gas Cylinder (LPG)","billing_model":"P2A"}]},"timestamp":"2022-05-14 12:06:05","ipay_uuid":"BFD2C1FA7480CA642FCD","orderid":{},"environment":"PRODUCTION"}*/
	}
	
	public function billerInstantApi()
	{
	   
							
		            $api_url = 'https://www.instantpay.in/ws/services/bbps/biller';

		            $request = array(
		                'token' => '8eb2607eab4812cc1ff67b5f3dcf6e58',
		                'request' => array(
    		                'sp_key' => 'CP0',
    		                'page' => 1
		                )
		            );

		            $header = array(
		                'content-type: application/json'
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
		            
		             
	                $bmPIData   = simplexml_load_string($output);
					echo $jsonResponse = json_encode((array) $bmPIData);
						die;
			//ERROR RESPONSE
			/*{"statuscode":"RPI","status":"Missing key request","data":{},"timestamp":"2022-05-14 12:08:22","ipay_uuid":"A6E447AA5057EBF09855","orderid":{},"environment":"PRODUCTION"}*/
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","status":"Transaction Successful","data":{"biller":{"item":[{"biller_id":"ATP","biller_name":"Airtel","category_key":"C00","category_name":"Mobile (Prepaid)"},{"biller_id":"VFP","biller_name":"Vi","category_key":"C00","category_name":"Mobile (Prepaid)"}]}},"timestamp":"2022-05-14 12:09:13","ipay_uuid":"B4275AB1FFC44A5AC8EA","orderid":{},"environment":"PRODUCTION"}*/
	}
	
	public function billerDetailInstantApi()
	{
	   
							
		            $api_url = 'https://www.instantpay.in/ws/services/bbps/biller_details';

		            $request = array(
		                'token' => '8eb2607eab4812cc1ff67b5f3dcf6e58',
		                'request' => array(
    		                'biller_id' => 'ATP'
		                )
		            );

		            $header = array(
		                'content-type: application/json'
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
		            
		             
	                $bmPIData   = simplexml_load_string($output);
					echo $jsonResponse = json_encode((array) $bmPIData);
						die;
			//ERROR RESPONSE
			/*{"statuscode":"RPI","status":"Missing key request","data":{},"timestamp":"2022-05-14 12:08:22","ipay_uuid":"A6E447AA5057EBF09855","orderid":{},"environment":"PRODUCTION"}*/
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","status":"Transaction Successful","data":{"biller":{"item":{"biller_id":"ATP","mode":"OFFLINEB","accepts_adhoc":"T","fetch_requirement":"NOT_SUPPORTED","payment_amount_exactness":"ANY","support_bill_validation":"NOT_SUPPORTED","bbps_platform":"F","biller_info":{"name":"Airtel","alias":"Airtel","description":"-","ownership":"PRIVATE","is_parent_biller":"F","parent_biller_id":"-","effect_from":"0","effect_to":"0"},"category":{"key":"C00","name":"Mobile (Prepaid)"},"registered_at":{"address":"-","city":"0","state":"0","pincode":"0","country":"-"},"communication_at":{"address":"-","city":"0","state":"0","pincode":"0","country":"-"},"coverage":{"address":"IND","country_id":"1","country_code":"IND","country":"India","state_id":"0","state_code":"-","state":"-","city_id":"0","city":"-","pincode":"0"},"payment_modes":{"item":{"payment_mode":"Cash","mode_desc":"Cash","min_limit":"10.00","max_limit":"5000000.00","payment_info":{"param_name":"Remarks","param_desc":"Remarks when the mode of payment is cash","min_length":"1","max_length":"50","param_type":"ALL","mandatory":"1","regex":"^[A-Za-z0-9- .]{1,50}$"}}},"payment_channels":{"item":{"payment_channel":"AGT","channel_desc":"Agent","min_limit":"10.00","max_limit":"5000000.00","device_info":{"item":[{"param_name":"TERMINAL_ID","param_desc":"Terminal ID of the device","min_length":"1","max_length":"10","param_type":"NUMERIC","mandatory":"1","regex":"^[0-9]{1,10}$"},{"param_name":"MOBILE","param_desc":"Mobile number of the agent","min_length":"10","max_length":"10","param_type":"NUMERIC","mandatory":"1","regex":"^[5-9][0-9]{9}$"},{"param_name":"GEOCODE","param_desc":"Latitude and longitude of the device - Represented in degrees with 4 digits after decimal","min_length":"13","max_length":"15","param_type":"ALL","mandatory":"1","regex":"^[0-9]{1,2}+[.]{1}+[0-9]{4}+[,]{1}+[0-9]{1,2}+[.]{1}+[0-9]{4}$"},{"param_name":"POSTAL_CODE","param_desc":"Postal code of the agent","min_length":"6","max_length":"6","param_type":"NUMERIC","mandatory":"1","regex":"^[1-9][0-9]{5}$"}]}}},"params":{"item":{"param_name":"param1","param_desc":"Mobile Number","min_length":"10","max_length":"10","param_type":"NUMERIC","mandatory":"1","regex":"^[5-9][0-9]{9}$"}},"timeout":"100"}}},"timestamp":"2022-05-14 12:12:20","ipay_uuid":"BB131792E2E331C870A0","orderid":{},"environment":"PRODUCTION"}*/
	}
	
	public function fetchPlanInstantApi()
	{
	   
							
		            $api_url = 'https://www.instantpay.in/ws/services/bbps/plans';

		            $request = array(
		                'token' => 'd7d3f307bbb476bad7d3f307bbb476ba',
		                'request' => array(
    		                'biller_id' => 'ATP',
    		                'circle' => 'RJ'
		                )
		            );

		            $header = array(
		                'content-type: application/json'
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
		            
		             
	                $bmPIData   = simplexml_load_string($output);
					echo $jsonResponse = json_encode((array) $bmPIData);
						die;
			//ERROR RESPONSE
			/*{"statuscode":"RPI","status":"Missing key request","data":{},"timestamp":"2022-05-14 12:08:22","ipay_uuid":"A6E447AA5057EBF09855","orderid":{},"environment":"PRODUCTION"}*/
			//SUCCESS RESPONSE
			
	}
	
	public function fetchBillInstantApi()
	{
	   
							
		            $api_url = 'https://www.instantpay.in/ws/services/bbps/api';

		            $request = array(
		                'token' => 'd7d3f307bbb476bad7d3f307bbb476ba',
		                'request' => array(
    		                'request_type' => 'PAYMENT',
    		                'outlet_id' => 186448,
    		                'biller_id' => 'ATP',
    		                'reference_txnid' => array(
    		                    'agent_external' => time().rand(1111,9999),
    		                    'billfetch_internal' => "",
    		                    'validate_internal' => ""
	                        ),
	                        'params' => array(
	                            'param1' => '8104758957',
	                            'param2' => ""
	                       ),
	                       'payment_channel' => 'AGT',
	                       'payment_mode' => 'Cash',
	                       'payment_info' => 'bill',
	                       'device_info' => array(
	                           'TERMINAL_ID' => '12813923',
	                           'MOBILE' => '8104758957',
	                           'GEOCODE' => '12.1234,12.1234',
	                           'POSTAL_CODE' => '302021',
	                       ),
	                       'remarks' => array(
	                           'param1' => '8104758957',
	                           'param2' => ""
	                       ),
	                       'amount' => 10
    		                
		                )
		            );

		            $header = array(
		                'content-type: application/json'
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
		            
		            
	                $bmPIData   = simplexml_load_string($output);
					echo $jsonResponse = json_encode((array) $bmPIData);
						die;
			//ERROR RESPONSE
			/*{"statuscode":"RPI","status":"Missing key request","data":{},"timestamp":"2022-05-14 12:08:22","ipay_uuid":"A6E447AA5057EBF09855","orderid":{},"environment":"PRODUCTION"}*/
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","status":"Transaction Successful","data":{"response_type":"PAYMENT","ipay_id":"1220518155944GGNWT","biller":"Airtel","biller_refid":"91164209","value_order":"10.00","value_commercial":"0.0590","type_pricing":"MARGIN","value_tds":"0.0030","convenience_fee":"0.00","value_transaction":"9.94","transaction_mode":"DR","params":{"param1":"8104758957"}},"timestamp":"2022-05-18 15:59:49","ipay_uuid":"C5691EFCA4E98F2BF65B","orderid":"1220518155944GGNWT","environment":"PRODUCTION"}*/
	}
	
	    
	    public function onboardMerchantInstantApi()
	{
	   
							
		            $api_url = 'https://api.instantpay.in/user/outlet/signup/initiate';
		            $aadhar = '496231127006';
		            
                    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
                    $iv = openssl_random_pseudo_bytes($ivlen);
                    $ciphertext = openssl_encrypt($aadhar,'aes-256-cbc', 'd7d3f307bbb476bad7d3f307bbb476ba', OPENSSL_RAW_DATA, $iv);
                    $encryptedData = base64_encode($iv . $ciphertext);
                    
		            $request = array(
		                'mobile' => '8619651646',
		                'pan' => 'DJBPG3725F',
		                'email' => 'lakshyagujrati7@gmail.com',
		                'aadhaar' => $encryptedData,
		                "latitude"=>"22.9734229",
		                "longitude"=>"78.6568942",
		                'consent' => "Y"
		            );
		            
		           echo json_encode($request);
		            echo '<br />';

		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: YWY3OTAzYzNlM2ExZTJlOTiCwv/jys9zRoS1vFYAByc=',
		                'X-Ipay-Client-Secret: e14baa3fd2d6a8a7edafa9ac7afa16dfb47bec0833b9962d7cfab47c8521dba7',
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                'content-type: application/json'
		            );
		            
		           /* echo json_encode($header);
		            echo '<br />';
		           */ 
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
		           echo $output;
		            die;
		            
		            
			//ERROR RESPONSE
			/*{"statuscode":"ERR","actcode":null,"status":"Invalid Aadhaar Id #1","data":null,"timestamp":"2022-05-18 13:33:16","ipay_uuid":"h0689653ab3e-ca3e-45b9-8057-3ae9d7c47766","orderid":"1220518133314KDNBW","environment":"LIVE"}*/
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","actcode":null,"status":"Transaction Successful","data":{"aadhaar":"xxxxxxxx7006","otpReferenceID":"Y2E3NTZmZmUtYzllZC00YTllLWExYWMtZDk5MTFiZmFkNjI0","hash":"1Dz\/s5Th1M9SaAkk1zH+9nqVZh+RqvP05KshIEQvZV1ga0taEBFLzQVYBEipRczkyENkQzSAC7eOOyjw254zEEsoyM\/EERcGb9Kl7mlqaQ7f+w6zmRh9KE\/1muG+xVvqpPpVz+ZqMvmteJkLJFhyAt2cRMJiXlIFVeJT+Dk0+z4UUv6S5MySOeEaZulBTVL14ZiyPfsJZlO03fqxuO3s+eqCBFf00gQjySAF20mLRRqwpwmNkaozuFoOcNoCcKBf\/CU7o19QtoZkzrY1WAEzLw=="},"timestamp":"2022-10-06 12:50:39","ipay_uuid":"h006976f40bf-9c83-44dd-8775-765f89d87af1","orderid":"1221006125034KLAVZ","environment":"LIVE","internalCode":null}*/
	}
	
	
	
	public function onboardMerchantValidateInstantApi()
	{
	   
							
		            $api_url = 'https://api.instantpay.in/user/outlet/signup/validate';
		            $aadhar = '496231127006';
		            
                    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
                    $iv = openssl_random_pseudo_bytes($ivlen);
                    $ciphertext = openssl_encrypt($aadhar,'aes-256-cbc', 'd7d3f307bbb476bad7d3f307bbb476ba', OPENSSL_RAW_DATA, $iv);
                    $encryptedData = base64_encode($iv . $ciphertext);
                    
		            $request = array(
		                'otpReferenceID' => 'Y2E3NTZmZmUtYzllZC00YTllLWExYWMtZDk5MTFiZmFkNjI0',
		                'hash' => '1Dz\/s5Th1M9SaAkk1zH+9nqVZh+RqvP05KshIEQvZV1ga0taEBFLzQVYBEipRczkyENkQzSAC7eOOyjw254zEEsoyM\/EERcGb9Kl7mlqaQ7f+w6zmRh9KE\/1muG+xVvqpPpVz+ZqMvmteJkLJFhyAt2cRMJiXlIFVeJT+Dk0+z4UUv6S5MySOeEaZulBTVL14ZiyPfsJZlO03fqxuO3s+eqCBFf00gQjySAF20mLRRqwpwmNkaozuFoOcNoCcKBf\/CU7o19QtoZkzrY1WAEzLw==',
		                'otp' => '910839'
		            );
		            
		            echo json_encode($request);
		            echo '<br />';

		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: YWY3OTAzYzNlM2ExZTJlOTiCwv/jys9zRoS1vFYAByc=',
		                'X-Ipay-Client-Secret: e14baa3fd2d6a8a7edafa9ac7afa16dfb47bec0833b9962d7cfab47c8521dba7',
		                'X-Ipay-Endpoint-Ip: 164.52.219.77',
		                'content-type: application/json'
		            );
		            
		            echo json_encode($header);
		            echo '<br />';
		            
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
		            echo $output = curl_exec($curl);
                    
		            // Close
		            curl_close ($curl);
		            die;
		            
			//ERROR RESPONSE
			/*{"statuscode":"ERR","actcode":"EXPIRED","status":"otpReferenceID is expired","data":null,"timestamp":"2022-05-18 15:57:35","ipay_uuid":"h0689653ded7-7727-4eff-b0c8-0799b53cbb09","orderid":null,"environment":"LIVE"}*/
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","actcode":null,"status":"Transaction Successful","data":{"outletId":210461,"name":"LAKSHYA GUJRATI","dateOfBirth":"11-09-1999","gender":"M","pincode":"311201","state":"Rajasthan","districtName":"Bhilwara","address":"WARD NO 10, Jahazpur, Jahazpur, CHIPO KA MOHALLA, ","profilePic":"\/9j\/4AAQSkZJRgABAgAAAQABAAD\/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL\/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL\/wAARCADIAKADASIAAhEBAxEB\/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL\/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6\/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL\/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6\/9oADAMBAAIRAxEAPwDrO9KKXFKK4jYOgpKX2owaBijpS+tIB6U4c59KAExxTSpB6nn9KkwQKQikA3NHUUY5pR6UWATpmnYJHoaAKUUAJggk0Y9KcSaMdaAE7cUduacB9KMDrQA3tShTilAPqaUZpARke9JipCPWmimIjFHenUY5qgEpQKUAY96UDH1pDEwetI6F1KhyhyDlcZ4Occ+vT8acOKcelACDge9IRVPU9UttJsmubh8KOAAMknBwMfhXEaj4znuNkcOIWyw+Q5DjI2k5HHGfzNUot7Cbseg8ev500H5iuM46+1eYnxPdRqbyeVnuDIfLORtXIHQY7Zz36AEYrIuPEF5eQeTNcSSRKpUqXJU59c9e1UqbE2eyQTRTDMUiOPVWB\/lUpOBXjlh4tn06eLaXfaMAE9R2HJIHv\/SuoX4jxPDEBYSF2JWQ7uE9wP4vpx+NDptApI7zkjvS9fauOsvHaXMqQy2SQNu\/eSPPhEXPX7pOf09xXUW1\/a3ib7S4inTu0Thh+dQ00O6LWOKOwwaQe\/FO+lIYfrQOTRk57UoWgBp60g9acc0nAGfzoEQg9KUcUmBmnAVVgCloxTgMfSkMSqepX4sIMgK0jZCIWIycZ7An8h+XWruOK8+8eahD9pWythI90qiRpDKFjQNkYOeGJwOD04x6U0rsTMjW\/EEt3Y3NvdQtG0jCRAWzsbv7jiuQe7\/ek546jjvTLm5lkdi7FyTknrk1ELd5eAuDW6SRL1FkuC8xcnqcmkaU+UQOlTrpznk1Oulyt90Uc6H7ORnJIVYNk5BzUpu5OPmI4x17VpDQZmHGBQ+gTKCTz9Kn2se4\/ZSIY74SReW2Mnu2elbGi6m2m3yT+RDKNwLnYWfA7KcjB5rnZ7KWBvmUgVPArvCVDdOarSSIs0e92N3DfWiTQMSrDOD1HsQeQfY1ZGccda8u8FXbpdKVmm3xqTJGOQyYxkLjkj869SHTIz+dYyVmWgHJ5xTkOHxSAE+ufShRk\/xA4qbDHED1xTfxFKcjvSZ9f5UWERdaXFGKUVQC4z3pRSDpS0DEOAM14p4vvjc+IL9gQdzhBjBwF44\/LP4mvapGZYnKAFgpwD0z7189XcjyzSSMMO7FiPQk5qobksjRRvCgfMa2LW2CqCcZqHT7MonnSDlhxWggOeKU5dDanHqTQQKGBPJq3HCoOcVBGrGrcQOcZ4rBmyLCRj0qzHAHXbtyKrQHIGTzWraA5GRxU2uMzbzQhcQNhOvTjFccbdrS6aJvlPfivXoFWRcHFcn4r0VopBfRRkxEHeQM7fc1vTvFmNRXRy2l3X2PXbaVXESpIMtngA46mvcBnA+Y9Mc4zXgUqeU3zYODXsXhC+OoeG7Z25aP90SSSTt4BJ9cVpNdTBG4S2OXJ+oFEZO45OfqKcV47UoAFZ2KFJY9MfjTNznjC\/lS8Ac9aX5aYiIUopAKd1pgHFKOlIOKXFAwx\/k14bqNoG8U3kWzYouJDt7AbjxXuf1ryjX7cReN9SPUsqyfTIX\/AD+NC0BasyLq5S3UA8nsoql\/a4TH7r9aL4hJmyCzsaploiMPKqZ77Tj86ElY0bfc2rPV4pW2shHvWqlxG4wpBNcbt8qQFWV0YZV0OQa3NJjaSVcscHjilOKKhJmwb6G3++6j6mrcGt2eAPPXIFQa14eS2XzNxbjJzXLFI\/NKKn47sVMYxG3Lod5DrkMUmQ5YDqVBOBXQxXEV7bcEPFKuD3BBrz\/TopbNVma3LQk5ypz+OK6rTljRhPbPiKQYZOoz6j0puy2JV+pxPiHTTZX0kY+7nK\/TtXo3gWN08L2+7GGZmGPTJrmPGca77eUqC5Uj8Af\/AK9dx4ftvsmg2UO4EiIEkd881d7x1MpK0jTAPPApwHrTDjJxmnL06GkIDz+FJgEdDTjR1HTmhARLTqaOtO70wCjHOaKOvWkBFc3UFlbSXFzII4UGWY\/kB9SSAB715tr95a6n4hF9aSl43tfKYMCCro\/IIPsw6cV2fjFA3hDUATgbUP5SLivObZcSEsct5X9RSZpCN1coXln5m5iDzWfdIJkRGTYFG35R1H0rpFdcEEVWuIlc5JGBUqdjVwTMV0a5lDkHP8TMcsx7knuSec1v6DB\/pSnHANVVjRR8o49a6Pw7bRmZcfOx7ZpSk5FQikaOs201zaNErcY6\/hXnl3ZIVKAkShv4+4r1vyHimEM8ZUH7pb+VYt7o8TyttiVsHoaUZcq1CUUzA0nTAmkw\/ZLtkvpGxKkeTEy9AGUkZPGcgcE9a6rTtNntIz5q7e5A6Zp2mWXlnEEChhwSAK05laFNrtk96qU7kqPLoch4oButTsrFAS7hVGOuWbH9K9GRBGiovAUYFcS+ntca6980hRoNqxcZwdvJA\/Hj3rc0DUpbyS9hkVwtu6qhdizMCOpP4dOapPZGc4PWRtntzT1Ge5phNPjOAaqxkKR7035h\/wDW6UpNIGHvTAjHFPpop1ABRijHFLQBleJIo5\/DWoLKuVEJfGM\/MvzL+oFeUWjbrhpGyS3y\/hXrmtxGbQr+MZyYHxjrwM15F\/q5wwAVMjnPWpZvT+FonZ9ppjyZB6Ukv3zUL3Cx4BFZ2NE1YqXi3EhUROVHt0rc8NLePeRQBgN33n7D3rIe5UnkhR6d62dPuokjQwypx94tkVethbvQ00PijT9bX7VNA9vM5URxHK7c9eefzrpIy32pgxyuePes6z1H7TlFZZAoydp5H4HmtCG5hZhhhuHWhoE7GzE8MMe\/GDis67lEshPbrUc1wOgNVHl+VmqGMrteva6wgkUGCZDtfP3WBwR+WPzra8PoFW+kQDa1wRn1wAP8a5m4UahJGoMaCPO5i2Wz6AV2VjEILKNMkHGT9TVRWpNRpQsXNx9KlTnPrVXec53ZqSOUqDycGtbHKTEsG7j8KTd9Kb54z1IpPOHfH4igBAadTelOBoGOpaUAGkK4pAHeuek8F6N9pkuktSX2sY4i5Mauc\/MF\/HgdB2AwK6DODjFKGwaYJtbHiMxOTxg+9ZtwC78Nj6V1HivTv7N1ydAuIpj50R7YPUfgcj8q5x49zcVGxsndFeKGHf8AvXY57g4rctLLTiBieRcjkZFZ8dnDJ\/rK0bLRrCVxvdlyezGjnNIadDRk0S0MaTWV\/LFcrypJ4z+HNTRG9juQ08kUg7PHnmrMPh1LdQ8crEY4BOaa0TKfm7VMpDepoCfcOualWzfUIngil8t8blbGQCORn2zis+I4ro9Ig8u1Mx6y8j6CiKuyJysipY6bePKGu4I4dh+Yo+7f9Pb64+lb27nA\/U00kjoahL4bBAH1rRJIxlJy3Jyzf3fyqYbHwAQGxmqO4f3RxUy9iCRx1Bpkkro69eR60zJ\/yaN7IODz7UeZnqAaALRXj6VHuIzUy8kioZU2gketMRPG4OKmH908ehFUI5Dn0I7VcikBYe3WiwCOp\/8Ar1ExwQfarRG5M+nQ+1ROnGO9AHGePip0uzyoJExwe+NvP9K81kl8tsGvQfH0m2KwhJPJkb6Y2j+tcFLCsgqW0nqbRT5dCsbobuKsw3wWRSG6VTewcthTU0Ok3TnAFL3SryOustaVo1VnAH1qWfUImxg5PoKwbbQLpQGdto+ladtpbRsNx6VL5R3bLsTGTnotdnChhtoo+PlQD68Vy0EQ8yOP+8wXp711QYkccj09KqJFQRty8rn6U3zc43KcU47icgHPpSAr0YcE\/kaozEzG3IbHsQasxcqB19COaqMoU4HTscc1YtcMSMUCJWU+lNKnOeafIChJHK9x6UZIGQdwp2ESg4fcehqcKJIj6g1E6\/uwR6UW8gHB4B4qgIJUMbUscwB5NXZYRKgJ+may5lMUhVwRQBtQuGwB16AV574v+JUNgz2Oh7JrlCVkuXGUQjjCj+I+\/T\/e7aHinW5dN8OyLDKY7m4PkxuOqj+Ij0OOh7EivEr0PHctGV24xtHt2q1H3eZkt62Oggv7zUllur65kuJ5HJLuc\/gB0A9hgU7ODVSx\/d2yKfSrWa5pv3jrpq0Rd+0g10WkyxSKC2OK5wZLCtbTY9hzt61DLWx0hnVhtUcUg2jk8mo0HyjinN1HoKVwHpcw29xHLcOEiVhknpknAz+JFdBuKncvTvivP\/FFx5ekMqnlnUD88\/0rp\/D9xJdaPCxJaSMbGyeenH14xXRTjeFznqv3kjoECzgYba9NYyx\/eGR0qqkuxxn5W+lXopo518tiAx6e9Ikj81dwD\/KfXtU1sDv4JYHoRyKilieM8jKnkE06EbBvC5VjxzyKaEXDvHTke9N4ByPxBFKsqnI5Df3T\/jQccn9KYicDdEeOVJ4qsBtZkIJJ5FY+q+M9L0mSSOJ\/tk+TmOBhhTjHzP0HI7ZI9K4LVfGmramzKLg2cZ\/gtTtP\/ff3s\/QgH0reFGc9UQ6iR6nN4g0zS4T\/AGnf29qCCVEj\/Mwx2XqfwFcbrPxP0x1C2Gnz3GSQJZT5Q+oHJI+u2vNZIxvZ+WJ5Z2OSx9z3NVAxlmye1X7G25PtL7G3qWsXGsX6XFzt3BAihMgKOvAJPf3rI1Bc6gGJBBRf5VNF92Rs9B\/Wi7XMMVxjgHa\/9KqUbwaXQmL95MtwfdFTvlV3CpbC2WWAMhyMdqmS2MhaPvXmy3PSjsVIiSwINblpLjHasIxS29xtIOK1YJSQMcUhm6s+BnPNDTFupwKoRsx+82APSsfWNXOxre3J9GYUkrsCrrd9\/aGoxW0XMUbZOO5roNH1dtKuI9+77PIQko6gD+9+H8q5KyhxIrHlic1r3OTBvA+VWwc+\/T+VethqS9k0+p5leb9poeoFlcgnaysMhhyCKj8pHVR09a8wsNau9JukMMpEEpy0bfcJ78ds+tdpY+KbC6K+Y\/2aUno5yv59PzrCph5R21NI1E9zpEubiBdrHzY\/Ruo\/GtGyMF0hWLjuVODisiG6RkDD5kYfK6NkH3q1FbeYPMtpdsqcgjg1jYu5dmttjEMuPQioXZowNwyDxmrEGoSECG8jywHDgVMY0lBMbBkI5H+IpoTPAs5+lVnOGB9KsgcVWcckV7cY2jocN9RJ3VIu2TVO1G6RyaW5ZnnIXnaKWDcq\/Lxmueo7ysXHRFoIEtZDuxlgP61Z06NLiKSCQHa44I7Gsye+WOPyyd7Zzj0qpFfXC3UcquQUYMoHTisW0rp9TRXdjo0hu9HfzYCHhP3kYcH\/AANbek3NteXayRkfMPmjJ5WpLSSK\/s45lB2uOh\/IiqOp6OlvGby1kME6HgqcZry5b2kejHa6N+90xD82Bj6VkypHEwUEZrlF1G\/s5JFN1OpY\/N85wT9KtwxTXqiS4lYKffrQ6dtbhGpd2saWpavDb25iik3yN1K9vasCOR7qYKIyq9ST1rTkhgX7iADGM9aZbRb58ZCg459B3qoJN2QqjaWo+FQs+3cNyrkrnnmrqASWd6hxkBJF56YJB\/8AQq5K4a6N49zhlYtkEdh6Vqafr0YcrdKUV1KuyjPB9q9enJKHI9DzZJuXMiy0ImjKN1HIPpUCEhgD1FSxzxPkxyI30PP5UgIdmcduCK6LJ7GV3syfTdautOuGEUzKhOdvVT9RXc6L4xhMqi5CxMeCc\/KffPavMpeJiwqa3uT0zWFSlGb13NVNo+goWgvoQ8Tq3fIpTblfmHb0rx\/RPEN5pkimGUmPvGx4P+FenaF4ptdUQRF1E\/8AzzJwf\/r\/AIVxzoygaxmpHhumTtNEyMcsvT6Us4O8kUUV6lF3pps5ZaS0K8SkKxYck1XuC4XapI+lFFZzXulR+IpbT3p23GCDzRRXHZG503hTUCJWs3fAILRj37j+v4Vsa1dDykhB5JyaKK5cRFXTOqg3sc3dxNcXpgXqHIx3P+cVPaysIQpcso6ZoorOXwlU\/jY9czTKvVd3IzjPtTdQkZLkwA4QMVwBjpRRXpYOlBQUranHiaknNorXDLDCR3xgVAsKR2wMgXJoortmlzHMtiO2szK+7GFJrRRFi+RfSiilTikroJNtleX72arKds2KKKyq6MqGqL0Mx3ADrV+G7e1uI2jciRSDuHY0UVcEmtRPc\/\/Z"},"timestamp":"2022-10-06 14:47:11","ipay_uuid":"h068976f6a71-3039-42db-8350-4900591bc4ea","orderid":"1221006144710ZTSKU","environment":"LIVE","internalCode":null}*/
	}
	    
	    
	    
	    
	    //instantpay bank list
	    
	    
	     public function instantBankList()
	{
	   
							
		            $api_url = 'https://api.instantpay.in/fi/aeps/banks';
		            
		           

		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: YWY3OTAzYzNlM2ExZTJlOTiCwv/jys9zRoS1vFYAByc=',
		                'X-Ipay-Client-Secret: e14baa3fd2d6a8a7edafa9ac7afa16dfb47bec0833b9962d7cfab47c8521dba7',
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                'X-Ipay-Outlet-Id: 210461',
		                'content-type: application/json'
		                
		            );
		            
		           /* echo json_encode($header);
		            echo '<br />';
		           */ 
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
		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");

		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		            // Request Body
		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

		            // Execute
		           $output = curl_exec($curl);

		            // Close
		            curl_close ($curl);
		           echo $output;
		            die;
		            
		            
			//ERROR RESPONSE
			/*{"statuscode":"ERR","actcode":null,"status":"Invalid Aadhaar Id #1","data":null,"timestamp":"2022-05-18 13:33:16","ipay_uuid":"h0689653ab3e-ca3e-45b9-8057-3ae9d7c47766","orderid":"1220518133314KDNBW","environment":"LIVE"}*/
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","actcode":null,"status":"Banks fetched successfully","data":[{"bankId":109005,"name":"STATE BANK OF INDIA","iin":"607094","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"65","aadhaarpayFailureRate":"91"},{"bankId":74984,"name":"Punjab National Bank","iin":"607027","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"28","aadhaarpayFailureRate":"100"},{"bankId":91606,"name":"UNION BANK OF INDIA","iin":"607161","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"30","aadhaarpayFailureRate":"100"},{"bankId":20500,"name":"INDIAN BANK","iin":"607105","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"35","aadhaarpayFailureRate":"100"},{"bankId":47267,"name":"BANK OF INDIA","iin":"508505","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"15","aadhaarpayFailureRate":"100"},{"bankId":53201,"name":"CANARA BANK","iin":"607396","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"24","aadhaarpayFailureRate":"100"},{"bankId":23061,"name":"INDIAN OVERSEAS BANK","iin":"607126","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"28","aadhaarpayFailureRate":"100"},{"bankId":3487,"name":"CENTRAL BANK OF INDIA","iin":"607264","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"61","aadhaarpayFailureRate":"100"},{"bankId":96035,"name":"UCO BANK","iin":"607066","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134157,"name":"INDIA POST PAYMENTS BANK","iin":"608314","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"50","aadhaarpayFailureRate":"0"},{"bankId":134126,"name":"DAKSHIN BIHAR GRAMIN BANK","iin":"607136","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"32","aadhaarpayFailureRate":"100"},{"bankId":134414,"name":"UTTAR BIHAR GRAMIN BANK","iin":"607069","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"13","aadhaarpayFailureRate":"50"},{"bankId":134125,"name":"BARODA UP BANK","iin":"606993","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"42","aadhaarpayFailureRate":"50"},{"bankId":38669,"name":"ANDHRA PRAGATHI GRAMEENA BANK","iin":"607121","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"18","aadhaarpayFailureRate":"0"},{"bankId":134550,"name":"PRATHAMA UP GRAMIN BANK","iin":"607135","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"33","aadhaarpayFailureRate":"0"},{"bankId":27970,"name":"BANK OF MAHARASHTRA","iin":"607387","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"33","aadhaarpayFailureRate":"50"},{"bankId":134120,"name":"BARODA RAJASTHAN KSHETRIYA GRAMIN BANK","iin":"607280","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"32","aadhaarpayFailureRate":"100"},{"bankId":1,"name":"AIRTEL PAYMENTS BANK","iin":"990320","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"30","aadhaarpayFailureRate":"100"},{"bankId":39287,"name":"BANK OF BARODA","iin":"606985","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"70","aadhaarpayFailureRate":"50"},{"bankId":64597,"name":"IndusInd Bank","iin":"607189","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"36","aadhaarpayFailureRate":"67"},{"bankId":30273,"name":"ANDHRA PRADESH GRAMEENA VIKAS BANK","iin":"607198","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134138,"name":"SAPTAGIRI GRAMEENA BANK","iin":"607053","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"63","aadhaarpayFailureRate":"0"},{"bankId":30301,"name":"ARYAVART BANK","iin":"607024","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"44","aadhaarpayFailureRate":"0"},{"bankId":15910,"name":"ICICI Bank","iin":"508534","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"33","aadhaarpayFailureRate":"100"},{"bankId":31652,"name":"RAJASTHAN MARUDHARA GRAMIN BANK","iin":"607509","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"50","aadhaarpayFailureRate":"100"},{"bankId":62431,"name":"IDBI BANK","iin":"607095","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"50","aadhaarpayFailureRate":"100"},{"bankId":30728,"name":"FINO PAYMENTS BANK","iin":"608001","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":130878,"name":"Axis Bank","iin":"607153","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"67","aadhaarpayFailureRate":"100"},{"bankId":106167,"name":"PUNJAB AND SIND BANK","iin":"607087","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"50","aadhaarpayFailureRate":"100"},{"bankId":134123,"name":"BANGIYA GRAMIN VIKASH BANK","iin":"607063","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134133,"name":"MADHYA PRADESH GRAMIN BANK","iin":"607022","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"86","aadhaarpayFailureRate":"0"},{"bankId":134137,"name":"PUNJAB GRAMIN BANK","iin":"607138","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"50","aadhaarpayFailureRate":"100"},{"bankId":74138,"name":"KARNATAKA GRAMIN BANK","iin":"607400","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"25","aadhaarpayFailureRate":"100"},{"bankId":11263,"name":"HDFC BANK","iin":"607152","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"80","aadhaarpayFailureRate":"100"},{"bankId":30520,"name":"CHHATTISGARH RAJYA GRAMIN BANK","iin":"607214","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":33975,"name":"JHARKHAND RAJYA GRAMIN BANK","iin":"607210","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134139,"name":"SARVA HARYANA GRAMIN BANK","iin":"607139","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134127,"name":"CHAITANYA GODAVARI GRAMEENA BANK","iin":"607080","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":64460,"name":"IDFC FIRST BANK","iin":"608117","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134142,"name":"TELANGANA GRAMEENA BANK","iin":"607195","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"33","aadhaarpayFailureRate":"0"},{"bankId":68286,"name":"KERALA GRAMIN BANK","iin":"607399","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":52599,"name":"CITY UNION BANK","iin":"607324","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":31276,"name":"MADHYANCHAL GRAMIN BANK","iin":"607232","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134135,"name":"TAMIL NADU GRAMA BANK","iin":"607052","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"43","aadhaarpayFailureRate":"100"},{"bankId":60588,"name":"FEDERAL BANK","iin":"607363","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134223,"name":"FINCARE SMALL FINANCE BANK","iin":"817304","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":31197,"name":"Kotak Mahindra Bank","iin":"990309","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"50","aadhaarpayFailureRate":"0"},{"bankId":134121,"name":"PAYTM PAYMENTS BANK","iin":"608032","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"67","aadhaarpayFailureRate":"100"},{"bankId":33945,"name":"UTKAL GRAMEEN BANK","iin":"607234","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"60","aadhaarpayFailureRate":"100"},{"bankId":134167,"name":"UTTAR BANGA KSHETRIYA GRAMIN BANK","iin":"607073","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"80"},{"bankId":134124,"name":"BARODA GUJARAT GRAMIN BANK","iin":"606995","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":66009,"name":"KARNATAKA BANK","iin":"607270","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134168,"name":"VIDHARBHA KONKAN GRAMIN BANK","iin":"607020","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"75","aadhaarpayFailureRate":"0"},{"bankId":69808,"name":"KARNATAKA VIKAS GRAMEENA BANK","iin":"607122","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"50"},{"bankId":134132,"name":"MANIPUR RURAL BANK","iin":"607062","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":82074,"name":"RBL BANK","iin":"607393","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134155,"name":"AU SMALL FINANCE BANK","iin":"608087","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"0"},{"bankId":60446,"name":"EQUITAS SMALL FINANCE BANK","iin":"508998","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134134,"name":"ODISHA GRAMYA BANK","iin":"607060","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"50","aadhaarpayFailureRate":"100"},{"bankId":69038,"name":"KARUR VYSYA BANK","iin":"508662","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134154,"name":"SURYODAY SMALL FINANCE BANK","iin":"608022","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"33","aadhaarpayFailureRate":"0"},{"bankId":127783,"name":"SOUTH INDIAN BANK","iin":"607439","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"33","aadhaarpayFailureRate":"100"},{"bankId":134143,"name":"TRIPURA GRAMIN BANK","iin":"607065","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134608,"name":"THE VILLUPURAM DISTRICT CENTRAL CO-OP BANK","iin":"508737","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":27476,"name":"LAKSHMI VILAS BANK","iin":"607058","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"0"},{"bankId":130362,"name":"TAMILNAD MERCANTILE BANK","iin":"607187","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134553,"name":"ERODE DISTRICT CENTRAL CO-OP BANK","iin":"508654","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134169,"name":"PUDUVAI BHARATHIAR GRAMA BANK","iin":"607054","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":86997,"name":"SARASWAT BANK","iin":"652150","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":33950,"name":"UTTARAKHAND GRAMIN BANK","iin":"607197","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"50","aadhaarpayFailureRate":"0"},{"bankId":86944,"name":"SHIVALIK MERCANTILE CO-OP BANK","iin":"607119","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":91570,"name":"TAMILNADU STATE APEX CO-OP BANK (TNSC BANK)","iin":"607131","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134647,"name":"THE RAMANATHAPURAM DISTRICT CENTRAL CO-OP BANK","iin":"508676","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"0"},{"bankId":134656,"name":"THE TIRUCHIRAPALLI DISTRICT CENTRAL CO-OP BANK","iin":"508680","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134648,"name":"THE TIRUVANNAMALAI DISTRICT CENTRAL CO-OP BANK","iin":"508657","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134122,"name":"ASSAM GRAMIN VIKASH BANK","iin":"607064","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"75","aadhaarpayFailureRate":"100"},{"bankId":59543,"name":"CSB BANK","iin":"607082","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134601,"name":"DHARMAPURI CENTRAL CO-OP BANK","iin":"508658","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":30722,"name":"ELLAQUAI DEHATI BANK","iin":"607218","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134153,"name":"ESAF SMALL FINANCE BANK","iin":"652254","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134129,"name":"HIMACHAL PRADESH GRAMIN BANK","iin":"607140","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":26523,"name":"JAMMU AND KASHMIR BANK","iin":"607440","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":70437,"name":"MAHARASHTRA GRAMEEN BANK","iin":"607000","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":31788,"name":"SAURASHTRA GRAMIN BANK","iin":"607200","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134503,"name":"THE COIMBATORE DISTRICT CENTRAL CO-OP BANK","iin":"508646","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"67","aadhaarpayFailureRate":"100"},{"bankId":134602,"name":"THE CUDDALORE DISTRICT CENTRAL CO-OP BANK","iin":"508647","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134652,"name":"THE DINDIGUL CENTRAL CO-OP BANK","iin":"508659","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"0"},{"bankId":134653,"name":"THE KANCHIPURAM CENTRAL CO-OP BANK","iin":"508734","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134604,"name":"THE KUMBAKONAM CENTRAL CO-OP BANK","iin":"508720","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134654,"name":"THE NILGIRIS DISTRICT CENTRAL CO-OP BANK","iin":"508660","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134655,"name":"THE PUDUKKOTTAI DISTRICT CENTRAL CO-OP BANK","iin":"508656","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134645,"name":"THE SALEM DISTRICT CENTRAL CO-OP BANK","iin":"508648","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134646,"name":"THE TAMILNADU STATE APEX CO-OP BANK","iin":"508681","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"100"},{"bankId":134650,"name":"THE THANJAVUR CENTRAL CO-OP BANK","iin":"508665","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134605,"name":"THE THOOTHUKUDI DISTRICT CENTRAL CO-OP BANK","iin":"508678","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"0"},{"bankId":134606,"name":"THE TIRUNELVELI DISTRICT CENTRAL CO-OP BANK","iin":"508677","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"0"},{"bankId":134607,"name":"THE VELLORE DISTRICT CENTRAL CO-OP BANK","iin":"508679","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134651,"name":"THE VIRUDHUNAGAR DISTRICT CENTRAL CO-OP BANK","iin":"508732","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"0","aadhaarpayFailureRate":"0"},{"bankId":134192,"name":"TRIPURA STATE CO-OP BANK","iin":"607978","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134152,"name":"UJJIVAN SMALL FINANCE BANK","iin":"508991","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134119,"name":"Yes Bank","iin":"607618","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":30499,"name":"CAUVERI KALPATARU GRAMEENA BANK","iin":"607308","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134409,"name":"JAMMU AND KASHMIR GRAMEEN BANK","iin":"607808","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":31362,"name":"MEGHALAYA RURAL BANK","iin":"607206","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":31370,"name":"MIZORAM RURAL BANK","iin":"607230","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134424,"name":"NSDL PAYMENTS BANK","iin":"607768","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134147,"name":"PASCHIM BANGA GRAMIN BANK","iin":"607079","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"100"},{"bankId":134531,"name":"SBM BANK","iin":"607395","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":39189,"name":"THE AP MAHESH CO-OP URBAN BANK","iin":"607051","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134603,"name":"THE KANYAKUMARI DISTRICT CENTRAL CO-OP BANK","iin":"508655","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134657,"name":"THE SIVAGANGAI DISTRICT CENTRAL CO-OP BANK","iin":"508649","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"},{"bankId":134649,"name":"THE TAMILNADU STATE APEX CO-OP BANK","iin":"508664","aepsEnabled":true,"aadhaarpayEnabled":true,"aepsFailureRate":"100","aadhaarpayFailureRate":"0"}],"timestamp":"2022-10-06 14:49:56","ipay_uuid":"h006976f6b70-0080-4710-b0cc-440f985f355d","orderid":null,"environment":"LIVE","internalCode":null}*/
	}
	
	
	
	 public function FinoCms()
   {
   	$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
   	$api_url = 'https://paysprint.in/service-api/api/v1/service/finocms/fino/generate_url';

            	$datapost =array();

               	$datapost['transaction_id'] = '45884585415245';
               	$datapost['redirect_url'] = 'https://pay-sprint.readme.io/reference';

   								$key = PAYSPRINT_AEPS_KEY;
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
									'Token:'.$token
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

								echo $output;
								die();


   	


   }
   
   
   
                         public function TranscationtatusCheck(){
                        
                        
                        $account_id = $this->User->get_domain_account();
                        
	            	$accountData = $this->User->get_account_data($account_id);
		            
   	
   	
			    	log_message('debug', 'Withdraw Transcation api call');
                        
			            	$key =$accountData['paysprint_aeps_key'];
							$iv=  $accountData['paysprint_aeps_iv'];
                        
                        
                        $datapost = array();
                        $datapost['reference'] = 'CSWD69634559332d820';
                        
                        	$cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                        	
							$body= base64_encode($cipher);
                    
                    
                    
                    
			    	$jwt_payload = array(
			    		'timestamp'=>time(),
			    		'partnerId'=>$accountData['paysprint_partner_id'],
			    		'reqid'=>time().rand(1111,9999)
			    	);

			    	$secret = $accountData['paysprint_secret_key'];

			    	$token = $this->Jwt_model->encode($jwt_payload,$secret);
                    	
			    	$header = [
			    		'Token:'.$token,
			    		'Authorisedkey:'.$accountData['paysprint_authorized_key']
			    	];
                        
			    	$httpUrl = 'https://paysprint.in/service-api/api/v1/service/aeps/aepsquery/query';

			    	log_message('debug', ' Cash Withdraw Transcation  status check api url - '.$httpUrl);

			    	log_message('debug', 'Cash Withdraw Transcation status check api header data - '.json_encode($header));

			    	log_message('debug', ' Cash Withdraw Transcation status check api body data - '.$body);

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

			    	log_message('debug', 'Cash Withdraw Transcation status check api response data - '.$output);

			    	echo $output;
			    	die;

			    	/*

					Success Response

					{"status":true,"response_code":1,"ackno":34798,"refid":"6075631660991154","amount":99,"message":"Subscription for zee five of Amount 99 is Success"}

			    	*/

			    	$responseData = json_decode($output,true);

			    }
			    
			    
			    
			    
			    public function aepsWithdrawalThreeWay()
    {
         $account_id = $this->User->get_domain_account();
	   $accountData = $this->User->get_account_data($account_id);
       	$key =$accountData['paysprint_aeps_key'];
		$iv=  $accountData['paysprint_aeps_iv'];
		$datapost = array();
		$datapost['reference'] = 'CSWD69634559332d820';
		$datapost['status'] = 'success';
		
		log_message('debug', 'Threeway Recon api call');
		echo json_encode($datapost);
		
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
			    		'Authorisedkey:'.$accountData['paysprint_authorized_key']
			    	];

		$httpUrl = 'https://paysprint.in/service-api/api/v1/service/aeps/threeway/threeway';
		
		
			log_message('debug', ' Threeway recon  status check api url - '.$httpUrl);

			    	log_message('debug', 'Threeway recon status check api header data - '.json_encode($header));

			    	log_message('debug', ' Threeway recon status check api body data - '.$body);
			    	
			    	
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

		$raw_response = curl_exec($curl);
		curl_close($curl);
			log_message('debug', 'Cash Withdraw Transcation status check api response data - '.$raw_response);
		echo $raw_response;
		
    }
	
	
	
	//instantpay payout
	
	
	  public function instantBankPayout()
	{
	   
							
		            $api_url = 'https://api.instantpay.in/payments/payout';
		            
		           	

		           	$request = array(
		                
		                'payer' => array(
    		                'bankId' => '0',
    		                'bankProfileId' => 0,
    		                'accountNumber' => '8292777339',
    		                ),
    		                
	                        'payee' => array(
	                            'name' => 'Lakshya Gujrati',
	                            'accountNumber' => "8619651646@ybl",
	                            'bankIfsc' =>""
	                       ),
	                       'transferMode' => 'UPI',
	                       'transferAmount' => 12,
	                       'externalRef' => 'UPI',
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       'remarks'  => 'UPI PAYMENT',
	                       'alertEmail' => 'lakshyagujrati7@gmail.com',
	                       'purpose' =>'REIMBURSEMENT'
		                //)
		            );




		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: YWY3OTAzYzNlM2ExZTJlOTiCwv/jys9zRoS1vFYAByc=',
		                'X-Ipay-Client-Secret: e14baa3fd2d6a8a7edafa9ac7afa16dfb47bec0833b9962d7cfab47c8521dba7',
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                
		                'content-type: application/json'
		                
		            );
		            
		           /* echo json_encode($header);
		            echo '<br />';
		           */ 
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
		           echo $output;
		            die;
		            
		            
			//ERROR RESPONSE
			/*{"statuscode":"ERR","actcode":null,"status":"Invalid Aadhaar Id #1","data":null,"timestamp":"2022-05-18 13:33:16","ipay_uuid":"h0689653ab3e-ca3e-45b9-8057-3ae9d7c47766","orderid":"1220518133314KDNBW","environment":"LIVE"}*/
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","actcode":null,"status":"Transaction Successful","data":{"externalRef":"IMPS1","poolReferenceId":"1221103121245UGCPQ","txnValue":"10.00","txnReferenceId":"230712773385","pool":{"account":"8292777339","openingBal":"531.89","mode":"DR","amount":"12.36","closingBal":"519.53"},"payer":{"account":"8292777339","name":"MORNING DIGITAL PRIVATE LIMITED"},"payee":{"account":"8745000100015076","name":"LAKSHYA GUJARATI S\/O"}},"timestamp":"2022-11-03 12:12:48","ipay_uuid":"h06897a786c7-60e5-4765-9966-8799534ec875","orderid":"1221103121245UGCPQ","environment":"LIVE"}*/
	}
	
	        
	        
	        //Account Verification
	        
	        
	        public function accountVerify()
	{
	   
							
		            $api_url = 'https://api.instantpay.in/identity/verifyBankAccount';
		            
		           	

		           	$request = array(
		                
		               
    		                
	                        'payee' => array(
	                            
	                            'accountNumber' => "8745000100015076",
	                            'bankIfsc' =>"PUNB0874500"
	                       ),
	                     
	                       'externalRef' => 'PPT2',
	                       'consent'    =>'Y',
	                       'isCached'  => 0,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       
		                //)
		            );




		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: YWY3OTAzYzNlM2ExZTJlOTiCwv/jys9zRoS1vFYAByc=',
		                'X-Ipay-Client-Secret: e14baa3fd2d6a8a7edafa9ac7afa16dfb47bec0833b9962d7cfab47c8521dba7',
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                
		                'content-type: application/json'
		                
		            );
		            
		           /* echo json_encode($header);
		            echo '<br />';
		           */ 
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
		           echo $output;
		            die;
		            
		            
			//ERROR RESPONSE
			/*{"statuscode":"ERR","actcode":null,"status":"Invalid Aadhaar Id #1","data":null,"timestamp":"2022-05-18 13:33:16","ipay_uuid":"h0689653ab3e-ca3e-45b9-8057-3ae9d7c47766","orderid":"1220518133314KDNBW","environment":"LIVE"}*/
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","actcode":null,"status":"Transaction Successful","data":{"externalRef":"IMPS1","poolReferenceId":"1221103121245UGCPQ","txnValue":"10.00","txnReferenceId":"230712773385","pool":{"account":"8292777339","openingBal":"531.89","mode":"DR","amount":"12.36","closingBal":"519.53"},"payer":{"account":"8292777339","name":"MORNING DIGITAL PRIVATE LIMITED"},"payee":{"account":"8745000100015076","name":"LAKSHYA GUJARATI S\/O"}},"timestamp":"2022-11-03 12:12:48","ipay_uuid":"h06897a786c7-60e5-4765-9966-8799534ec875","orderid":"1221103121245UGCPQ","environment":"LIVE"}*/
	}
	    
	        
	        
	        public function testSms()

	{
		$api_url = SMS_REGISTER_MSG_API_URL;
			$post['mobile'] = '8619651646';
			$accountData['sms_auth_key'] = '371145A86jrLni1TBJ6368d39aP1';

            $request = array(
                'flow_id' => '637208806f475c73cf5ac62b',
                'sender' => 'PURVEY',
                'mobiles' => '91'.$post['mobile'],
                'otp' => 8545,
                
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

            echo $output;
            die();
	}
	    
	    
	    //fetch beneficiary
	    
	     public function fetchBeneficiaryById()
    {
         $account_id = $this->User->get_domain_account();
	   $accountData = $this->User->get_account_data($account_id);
       	$key =PAYSPRINT_AEPS_KEY;
		$iv=  PAYSPRINT_AEPS_IV;
		
		$post_data = array(
            'mobile' => '8696627736',
            'beneid'=>'2938'
            
        );
		
		log_message('debug', 'Fetch Beneficary api call');
		echo json_encode($post_data);
		
		$cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
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
			    		'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY
			    	];

		$httpUrl = 'https://paysprint.in/service-api/api/v1/service/dmt/beneficiary/registerbeneficiary/fetchbeneficiarybybeneid';
		
		
			log_message('debug', ' Fetch Beneficary api url - '.$httpUrl);

			    	log_message('debug', 'Fetch Beneficiary api header data - '.json_encode($header));

			    	log_message('debug', ' Fetch Beneficiary api body data - '.$post_data);
			    	
			    	
		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_URL => $httpUrl,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 60,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    CURLOPT_POSTFIELDS => $post_data,   
		   // CURLOPT_POSTFIELDS => array('body'=>$body),
		    CURLOPT_HTTPHEADER => $header
		));

		$raw_response = curl_exec($curl);
		curl_close($curl);
			log_message('debug', 'Fetch Beneficiary api response data - '.$raw_response);
		echo $raw_response;
		
    }
	
	
	
	//check status dmt
	
	 public function checkStatus()
    {
         $account_id = $this->User->get_domain_account();
	   $accountData = $this->User->get_account_data($account_id);
       	$key =PAYSPRINT_AEPS_KEY;
		$iv=  PAYSPRINT_AEPS_IV;
		
		$post_data = array(
            'referenceid' => '16700714371368'
            
            
        );
		
		log_message('debug', 'Check Status api call');
		echo json_encode($post_data);
		
		$cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
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
			    		'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY
			    	];

		$httpUrl = 'https://paysprint.in/service-api/api/v1/service/dmt/transact/transact/querytransact';
		
		
			log_message('debug', ' Check Status api url - '.$httpUrl);

			    	log_message('debug', 'Check Status api header data - '.json_encode($header));

			    	log_message('debug', ' Check Status api body data - '.json_encode($post_data));
			    	
			    	
		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_URL => $httpUrl,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 60,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    CURLOPT_POSTFIELDS => $post_data,   
		   // CURLOPT_POSTFIELDS => array('body'=>$body),
		    CURLOPT_HTTPHEADER => $header
		));

		$raw_response = curl_exec($curl);
		curl_close($curl);
			log_message('debug', 'Check Status api response data - '.$raw_response);
		echo $raw_response;
		
    }
    
    
    // refund otp
    
    
     public function RefundOtp()
    {
         $account_id = $this->User->get_domain_account();
	   $accountData = $this->User->get_account_data($account_id);
       	$key =PAYSPRINT_AEPS_KEY;
		$iv=  PAYSPRINT_AEPS_IV;
		
		$post_data = array(
            'referenceid' => '16700714371368',
            'ackno' =>'47926'
            
            
        );
		
		log_message('debug', 'Refund Otp api call');
		echo json_encode($post_data);
		
		$cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
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
			    		'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY
			    	];

		$httpUrl = 'https://paysprint.in/service-api/api/v1/service/dmt/refund/refund/resendotp';
		
		
			log_message('debug', ' Refund Otp api url - '.$httpUrl);

			    	log_message('debug', 'Refund Otp api header data - '.json_encode($header));

			    	log_message('debug', ' Refund Otp api body data - '.json_encode($post_data));
			    	
			    	
		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_URL => $httpUrl,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 60,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    CURLOPT_POSTFIELDS => $post_data,   
		   // CURLOPT_POSTFIELDS => array('body'=>$body),
		    CURLOPT_HTTPHEADER => $header
		));

		$raw_response = curl_exec($curl);
		curl_close($curl);
			log_message('debug', 'Refund Otp api response data - '.$raw_response);
		echo $raw_response;
		
    }
    
    
    //claim refund otp
    
     public function ClaimRefundOtp()
    {
         $account_id = $this->User->get_domain_account();
	   $accountData = $this->User->get_account_data($account_id);
       	$key =PAYSPRINT_AEPS_KEY;
		$iv=  PAYSPRINT_AEPS_IV;
		
		$post_data = array(
            'referenceid' => '16700714371368',
            'ackno' =>'47926',
            'otp' =>'136047'
            
            
        );
		
		log_message('debug', ' Claim Refund  api call');
		echo json_encode($post_data);
		
		$cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
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
			    		'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY
			    	];

		$httpUrl = 'https://paysprint.in/service-api/api/v1/service/dmt/refund/refund/';
		
		
			log_message('debug', ' Claim Refund api url - '.$httpUrl);

			    	log_message('debug', 'Claim Refund api header data - '.json_encode($header));

			    	log_message('debug', ' Claim Refund api body data - '.json_encode($post_data));
			    	
			    	
		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_URL => $httpUrl,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 60,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    CURLOPT_POSTFIELDS => $post_data,   
		   // CURLOPT_POSTFIELDS => array('body'=>$body),
		    CURLOPT_HTTPHEADER => $header
		));

		$raw_response = curl_exec($curl);
		curl_close($curl);
			log_message('debug', 'Claim Refund api response data - '.$raw_response);
		echo $raw_response;
		
    }
    
    
    
    // Fino AEPS

    public function newAepsActiveAuth(){

    	$account_id = $this->User->get_domain_account();
	   $accountData = $this->User->get_account_data($account_id);
	   $user_ip_address = $this->User->get_user_ip();

		$response = array();

		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS Active Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }

        else

        {


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

					$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', ' New AEPS Active Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'NEW AEPS ACTIVE Check  Decrypt Token - '.json_encode($chk_user_token));

					if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
					{
						$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1 )
					{

						 // check user credential
					$chk_user_credential =$this->db->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->num_rows();
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
					if(in_array(17, $activeService)){
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
					//$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
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
					//$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
					$path = 'media/aeps_kyc_doc/';
					$targetDir = $path.$file_name;
					if(file_put_contents($targetDir, $profile)){
					$pancard_photo = $targetDir;
						}
								}

						$chk_already = $this->db->get_where('new_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$userID))->row_array();

						if(!$chk_already){

							$before_wallet_balance =  $this->User->getMemberWalletBalanceSP($loggedAccountID);

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
						else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}

					}
					else
					{
						$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);

					}
			       
					}
						log_message('debug', 'New AEPS Active Auth API Response - '.json_encode($response));    
						echo json_encode($response);

					}

}


		public function newAepsStatusActive(){
        
        $account_id = $this->User->get_domain_account();
	   $accountData = $this->User->get_account_data($account_id);

        $response = array();
        $post = $this->input->post();
        log_message('debug', 'newAepsStatusActive API Post Data - '.json_encode($post));  
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_id', 'userID', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE)
        {
            $response = array(
                'status' => 0,
                'message' => 'Sorry!! Details Not Valid.'
            );
        }
        else
        {
          
           $userID = $post['user_id'];

           $chk_user = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->row_array();

           if(!$chk_user){

                $response = array(

                  'status'  => 0,
                  'message' => 'Sorry!! user not valid.' 

                );
           }
           else{

               $kycData = $this->db->order_by('created','desc')->get_where('new_aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$userID,'status'=>0))->row_array();

               // update aeps status
               $this->db->where('account_id',$account_id);
                $this->db->where('id',$userID);
                $this->db->update('users',array('new_aeps_status'=>1));

                // update aeps status
                $this->db->where('id',$kycData['id']);
                $this->db->update('new_aeps_member_kyc',array('status'=>1,'clear_step'=>2));

                $response = array(

                  'status'  => 1,
                  'message' => 'Congratulations!! your aeps kyc completed successfully.' 

                );    

           }        
            
        }
        log_message('debug', 'newAepsStatusActive API Response - '.json_encode($response));   
        echo json_encode($response);
        
    }




    public function newAepsApiAuth()
					
	{   
		    
		$account_id = $this->User->get_domain_account();
	  	$accountData = $this->User->get_account_data($account_id);
	  	$user_ip_address = $this->User->get_user_ip();

	  	$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS api Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }

        else
        {

		$request = $_REQUEST['user_data'];
		$post =  json_decode($request,true);

		log_message('debug', 'New AEPS api Auth API Post Data - '.json_encode($post));  

		$memberID = $post['userID'];
		$loggedUser = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
	
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
					if(in_array(17, $activeService)){
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
														];

														if($account_id == 2)
						        {
						            $header = [
						            'Token:'.$token,
						            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
						        ];

						}



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
															'account_id'=>$account_id,
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
															$str = '';
															if($is_bal_info == 0)
															{
																$this->Newaeps_model->addStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID,$memberID);

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

														$key = $accountData['paysprint_aeps_key'];
														$iv = $accountData['paysprint_aeps_iv'];
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

														];

														if($account_id == 2)
													        {
													            $header = [
													            'Token:'.$token,
													            'Authorisedkey:'.$accountData['paysprint_authorized_key']           
													        ];

													}



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
															'account_id'=>$account_id,
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

															$this->Newaeps_model->addBalance($txnID,$aadharNumber,$iin,$amount,$memberID,$recordID,$serviceType);
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
					}

						log_message('debug', 'New AEPS api Auth API Response - '.json_encode($response));
						echo json_encode($response);
					}



		public function getNewAepsHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'ICICI AEPS History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else{

    	$post = $this->input->post();
		log_message('debug', 'New AEPS History API Post Data - '.json_encode($post));	
		$userID = isset($post['userID']) ? $post['userID'] : 0;
    	$response = array();
    	$fromDate = isset($post['fromDate']) ? $post['fromDate'] : '';
        $toDate = isset($post['toDate']) ? $post['toDate'] : '';
        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

    		$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'New Aeps History API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'New AEPS History Check Decrypt Token - '.json_encode($chk_user_token));
			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{

				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1 )
				{

						// check user valid or not
		$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
		if($chk_user)
		{
			$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_new_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$userID'";
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

					$data[$key]['invoiceUrl'] = base_url('newAepsinvoice/index/'.$list['id'].'');

					$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
					
				}
			}

			if($data)
			{
				$response = array(
					'status' => 1,
					'message' => 'Success',
					'data' => $data,
					'pages' => $pages
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

				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);

				}

			}

			else
			{
				$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
			}

	
	}
		log_message('debug', 'New AEPS History API Response - '.json_encode($response));	
		echo json_encode($response);
    }


    //icici aeps onboard

    public function iciciAepsActiveAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS Active Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
		$post = $this->input->post();
		log_message('debug', 'ICICI AEPS Active Auth API Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
			
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

			// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'AEPS Active Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'ICICI AEPS ACTIVE Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{

			// check user credential
			$chk_user_credential =$this->db->get_where('users',array('id'=>$userID,'account_id'=>$account_id))->num_rows();
			if(!$chk_user_credential)
            {
				$response = array(
					'status' => 0,
					'message' => 'User Id Not Valid'
				);
                
            }
			else
			{
				$activeService = $this->User->account_active_service($userID);
				$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($userID);
				if(!in_array(19, $activeService)){

					$response = array(
						'status' => 0,
						'message' => 'Sorry ! This service is not active in your account.'
					);
				}
				else
				{
					if($user_instantpay_aeps_status)
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
						//	$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
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
						//	$profile_img_name = AEPS_FILE_UPLOAD_SERVER_PATH.$file_name;
				            $path = 'media/aeps_kyc_doc/';
				            $targetDir = $path.$file_name;
				            if(file_put_contents($targetDir, $profile)){
				                $pancard_photo = $targetDir;
				            }
			        	}
			        	
				        	$api_response = $this->IciciAeps_model->activeAEPSMember($post,$aadhar_photo,$pancard_photo,$userID);
				        	$status = $api_response['status'];

							if($status == 1)
							{
								$encodeFPTxnId = $api_response['otpReferenceID'];
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
			 	else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
		}


			
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

			}

		}
		log_message('debug', 'ICICI AEPS Active Auth API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }



    public function iciciAepsOtpAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'ICICI AEPS OTP Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'ICICI AEPS OTP Auth API Post Data - '.json_encode($post));	
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
				$userID = $post['user_id'];
				$encodeFPTxnId = $post['encodeFPTxnId'];
				// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'ICICI AEPS OTP Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'ICICI AEPS OTP Check  Decrypt Token - '.json_encode($chk_user_token));

				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
						// check user credential
						$chk_user_credential =$this->db->get_where('users',array('id'=>$userID,'account_id'=>$account_id))->num_rows();
						if(!$chk_user_credential)
			            {
							$response = array(
								'status' => 0,
								'message' => 'User Id Not Valid'
							);
			                
			            }
						else
						{
								$activeService = $this->User->account_active_service($userID);
								$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($userID);
									if(!in_array(19, $activeService)){

										$response = array(
											'status' => 0,
											'message' => 'Sorry ! This service is not active in your account.'
										);
									}

							
							else
							{
								if($user_instantpay_aeps_status)
								{
									$response = array(
										'status' => 0,
										'message' => 'Sorry ! ICICI AEPS already actived in your account.'
									);
								}
								else
								{
									// check already kyc approved or not
									$chk_encode_id = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$userID,'otpReferenceID'=>$encodeFPTxnId,'status'=>0))->num_rows();
									if(!$chk_encode_id)
									{
										$response = array(
											'status' => 0,
											'message' => 'Sorry ! Encoded Transaction ID not valid.'
										);
									}
									else
									{
										$memberData = $this->db->get_where('users',array('id'=>$userID,'aeps_status'=>0))->row_array();

								        $get_data = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$userID,'otpReferenceID'=>$encodeFPTxnId))->row_array();
								        $hash_data = $get_data['hash'];
								        $aadhar = $get_data['aadhar'];

										$api_response = $this->IciciAeps_model->aepsOTPAuth($post,$userID,$encodeFPTxnId,$hash_data,$aadhar);
							        	$status = $api_response['status'];

										if($status == 1)
										{
											$response = array(
												'status' => 1,
												'message' => 'Congratulations ! OTP Verified successfully.'
												
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
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

			}
		}
		log_message('debug', 'ICICI AEPS OTP Auth API Response - '.json_encode($response));	
		echo json_encode($response);
		
    }


    //icici Aeps Api Auth


    public function iciciAepsApiAuth()				
	{   
		  
		    $account_id = $this->User->get_domain_account();
	  	    $accountData = $this->User->get_account_data($account_id);
	  	    $user_ip_address = $this->User->get_user_ip();

	  	    // get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'AEPS api Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }

        else{
		$request = $_REQUEST['user_data'];
		$post =  json_decode($request,true);

		log_message('debug', 'ICICI AEPS api Auth API Post Data - '.json_encode($post));  

		$memberID = $post['userID'];
		$loggedUser = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
		
		$get_outlet_id = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
	 	$outlet_id = $get_outlet_id['instantpay_outlet_id'];
	 	
	 	
	
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
					if(in_array(19, $activeService)){
									$is_apes_active = 1;
								}


								if(!$is_apes_active){

									$response = array(
										'status' => 0,
										'message' => 'Sorry!! AEPS not active.'
									);

								}
								else{

									$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($memberID);
									$response = array();
									if($user_instantpay_aeps_status)
									{
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
											$amount = $post['Amount'];
											$iin = $post['IIN'];
											//$txn_pin = $loggedUser['decoded_transaction_password'];

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
													$api_url = INSTANTPAY_AEPS_BALANCE_ENQUIRY;
													if($serviceType == 'ministatement')
													{
														$txnID = uniqid('MNST' . rand(11,99));
														$Servicestype = 'getministatment';
														$is_bal_info = 0;
														$txnType = 'MS';
														$remarks = 'Mini Statement';
														$api_url = INSTANTPAY_AEPS_MINI_STATEMENT_API_URL;
													}
													if($amount == 0)
													{

														$accessmodetype = 'APP';

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

					$output = curl_exec($curl);
					
					curl_close($curl);

					$responseData = json_decode($output,true);

					$apiData = array(
						'account_id'=>$account_id,
						'user_id' => $memberID,
						'api_url' => $api_url,
						'api_response' => $output,
						'post_data' => json_encode($request),
						'created' => date('Y-m-d H:i:s'),
						'created_by' => $memberID
						);
						$this->db->insert('aeps_api_response',$apiData);

						if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
						{   
					$statementList = isset($responseData['data']['miniStatement']) ? $responseData['data']['miniStatement'] : array();
					$balanceAmount = $responseData['data']['bankAccountBalance'];;
					$bankRRN = $responseData['data']['ipayId'];
					$recordID = $this->IciciAeps_model->saveAepsTxn($txnID,$serviceType,$post['AadharNumber'],$mobile,$amount,$iin,$api_url,$output,$responseData['status'],2,$statementList,$bankRRN,$balanceAmount,0,$memberID);
					
					$str = '';
					if($is_bal_info == 0)
					{
					
					$this->IciciAeps_model->addStatementCom($txnID,$post['AadharNumber'],$iin,$amount,$recordID,$memberID);
					if($statementList)
					
					{
						$str = array();
						foreach($statementList as $key => $list)
						{   
						$str[$key]['date'] = $list['date'];
						if($list['txnType'] == 'DR' || $list['txnType'] == 'D'){
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
					'message' => $responseData['status'],
					'balanceAmount' => $responseData['data']['bankAccountBalance'],
					'bankRRN' => $responseData['data']['ipayId'],
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
				
					$this->IciciAeps_model->saveAepsTxn($txnID,$serviceType,$post['AadharNumber'],$mobile,$amount,$iin,$api_url,$output,$responseData['status'],3,$api_response_data,$balanceAmount,$bankRRN,$transactionAmount,$memberID);
					
						$response = array(
							'status' => 0,
							'message' => $responseData['status'],
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
							$api_url = INSTANTPAY_AEPS_WITHDRAWAL_API_URL;
						if($serviceType == 'aadharpay')
							{
							$Servicestype = 'Aadharpay';
							$txnID = uniqid('APAY' . rand(11,99));
							$txnType = 'M';
							$remarks = 'Aadharpay';
							$api_url = INSTANTPAY_AEPS_AADHARPAY_API_URL;
							}

						if($amount >= 100 && $amount <= 10000)
						{
						$accessmodetype = 'APP';

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
							
							curl_close($curl);

								$responseData = json_decode($output,true);

								$apiData = array(
									'account_id'=>$account_id,
									'user_id' => $memberID,
									'api_url' => $api_url,
									'api_response' => $output,
									'post_data' => json_encode($request),
									'created' => date('Y-m-d H:i:s'),
									'created_by' => $memberID
									);
								$this->db->insert('aeps_api_response',$apiData);

							if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
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

							$balanceAmount = $responseData['data']['bankAccountBalance'];
							$bankRRN = $responseData['data']['ipayId'];

							$transactionAmount = $responseData['data']['transactionValue'];
							$statementList = array();
							$recordID = $this->IciciAeps_model->saveAepsTxn($txnID,$serviceType,$post['AadharNumber'],$mobile,$amount,$iin,$api_url,$output,$responseData['status'],2,$statementList,$balanceAmount,$bankRRN,$transactionAmount,$memberID);

							$this->IciciAeps_model->addBalance($txnID,$post['AadharNumber'],$iin,$amount,$memberID,$recordID,$serviceType);
							
								$str = array(
									'txnStatus'    => 'Successfull',
									'amount'       => $responseData['data']['transactionValue'],
									'balanceAmount'=> $responseData['data']['bankAccountBalance'],
									'bankRRN'      => $responseData['data']['ipayId']   
									);


									$response = array(
										'status' => 1,
										'message' => $responseData['status'],
									'balanceAmount' => $responseData['data']['bankAccountBalance'],
									'bankRRN' => $responseData['data']['ipayId'],
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

									$this->IciciAeps_model->saveAepsTxn($txnID,$serviceType,$post['AadharNumber'],$mobile,$amount,$iin,$api_url,$output,$responseData['status'],3,$api_response_data,$balanceAmount,$bankRRN,$transactionAmount,$memberID);

									$response = array(
										'status' => 0,
									'message' => $responseData['status'],
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
														'message' => 'Something Went Wrong  ! Please Try Again Later.'
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
												'message' => 'Something Went Wrong ! Please Try Again Later.'
											);
										}
									}
								
							}
						}

					}

						log_message('debug', 'ICICI AEPS api Auth API Response - '.json_encode($response));
						
						echo json_encode($response);
					}

		//icici aeps history

	  
    

      public function getIciciAepsHistory()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'ICICI AEPS History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
	    	$post = $this->input->post();
			log_message('debug', 'AEPS History API Post Data - '.json_encode($post));	
			$userID = isset($post['userID']) ? $post['userID'] : 0;
	    	$response = array();
	    	$fromDate = isset($post['fromDate']) ? $post['fromDate'] : '';
	        $toDate = isset($post['toDate']) ? $post['toDate'] : '';
	        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'verifyUserAuth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'ICICI AEPS History Check Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1 )
				{
					// check user valid or not
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
					if($chk_user)
					{
						$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$userID'";
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

								$data[$key]['invoiceUrl'] = base_url('iciciAepsinvoice/index/'.$list['id'].'');

								$data[$key]['datetime'] = date('d-M-Y h:i:s a',strtotime($list['created']));
								
							}
						}

						if($data)
						{
							$response = array(
								'status' => 1,
								'message' => 'Success',
								'data' => $data,
								'pages' => $pages
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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'ICICI AEPS History API Response - '.json_encode($response));	
		echo json_encode($response);
	   }



    
     public function nsdlPanStatus()
    {
         $account_id = $this->User->get_domain_account();
	   $accountData = $this->User->get_account_data($account_id);
       	$key =PAYSPRINT_AEPS_KEY;
		$iv=  PAYSPRINT_AEPS_IV;
		
		$post_data = array(
            'refid' => '16720591657433',
            
            
            
        );
		
		log_message('debug', ' Pan status  api call');
		echo json_encode($post_data);
		
		$cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
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
			    		'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY
			    	];

		$httpUrl = 'https://paysprint.in/service-api/api/v1/service/pan/V2/pan_status';
		
		
			log_message('debug', ' Pan Check api url - '.$httpUrl);

			    	log_message('debug', 'Pan api header data - '.json_encode($header));

			    	log_message('debug', 'Pan  api body data - '.json_encode($post_data));
			    	
			    	
		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_URL => $httpUrl,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 60,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    CURLOPT_POSTFIELDS => $post_data,   
		   // CURLOPT_POSTFIELDS => array('body'=>$body),
		    CURLOPT_HTTPHEADER => $header
		));

		$raw_response = curl_exec($curl);
		curl_close($curl);
			log_message('debug', 'Pan api response data - '.$raw_response);
		echo $raw_response;
		
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
        
        
        public function getIciciAepsBankList()
					{
		    // get state list
						$bankList = $this->db->get('instantpay_aeps_bank_list')->result_array();
						
					

						$data = array();
						if($bankList)
						{
							foreach($bankList as $key=>$list)
							{   
								$data[$key]['bank_id'] = $list['id'];
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
					
					
		
		//Paysprint Aeps Payout

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
                    $account_id = $this->User->get_domain_account();
					$accountData = $this->User->get_account_data($account_id);
					$user_ip_address = $this->User->get_user_ip();
					$response = array();
					$token = '';
			        $header_data = apache_request_headers();
			        if($header_data && isset($header_data['Token']))
					{
						$token = $header_data['Token'];
			        }
			        log_message('debug', 'Fino AEPS Payout API Header - '.json_encode($header_data));	

			        if($token == '')
			        {
			        	$response = array(
							'status' => 0,
							'message' => 'Session out.Please Login Again.',
							'is_login' => 0
						);
			        }

			        else
			        {

                    $post = $this->input->post();
                      
                    log_message('debug', 'newPayoutBeneAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));
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
                      
                       $userID = $post['userID'];

                       $decryptToken = $this->User->generateAppToken('decrypt',$token);
						log_message('debug', 'New Aeps Payout API Decrypt Token String - '.$decryptToken);
						$explodeToken = explode('|',$decryptToken);
						$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
						$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
						$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
						$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

						$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
						log_message('debug', 'New Aeps Payout Check Decrypt Token - '.json_encode($chk_user_token));
						if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
						
						{

							$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
							if($chk_token_user && $tokenUserID == $post['userID'] && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
							{

                       $chk_user = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->row_array();

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
                                

                                log_message('debug', 'Aeps Payout API Account ID - '.$account_id.' Post Data - '.$token);

                                
                                //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
                                
                                $header = [
                                    'Token:'.$token,
                                    
                                ];
                                
                                
                                $httpUrl = PAYSPRINT_ADD_BENEFICIARY_URL;

                                log_message('debug', 'Aeps Add Account API Account ID - '.$account_id.' Api Url - '.$httpUrl);


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


                                log_message('debug', 'Aeps Add Account API Account ID - '.$account_id.' Add account api final response - '.$raw_response);

                                    
                                    $responseData = json_decode($raw_response,true);
                                    
                                    $api_data = array(
                                    	'account_id' => $account_id,
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
                                        	'account_id' =>$account_id,
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
                                        	'account_id' =>$account_id,
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


					else
					{
						$response = array(
							  'status' => 0,
							  'message' => 'Session out.Please Login Again.',
							  'is_login'=>0	
							);
					}


						}

						else
						{
							$response = array(
							  'status' => 0,
							  'message' => 'Session out.Please Login Again.',
							  'is_login'=>0	
							);
						}	


     
                        
                    }

                  }
                     log_message('debug', 'newPayoutBeneAuth API Account ID - '.$account_id.' newPayoutBeneAuth API Response - '.json_encode($response));  

                    echo json_encode($response);
                    
                }
        
        
        
         function amountCheck($num)
    {
    	$this->load->library('form_validation');
        if ($num < 1)
        {
            $this->form_validation->set_message(
                            'requestAmountCheck',
                            'The %s field must be grater than 0'
                        );
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    



           public function newPayoutAuth(){

                $account_id = $this->User->get_domain_account();
				$accountData = $this->User->get_account_data($account_id);

                $response = array();
                $post = $this->input->post();
                
                log_message('debug', 'newPayoutAuth API Account ID - '.$account_id.' newPayoutAuth API Post Data - '.json_encode($post));  

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
                      
                       $userID = $post['userID'];

                       //$account_id = $userID;

                       $bene_id = $post['bene_id'];

                       $chk_user = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->row_array();

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

                            $benificaryData = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'user_id'=>$chk_user['id'],'bene_id'=>$bene_id,'is_verified'=>1))->row_array();

                            if(!$benificaryData){

                                $response = array(
                                    'status'  => 0,
                                    'message' => 'Sorry!! beneficiary not valid.' 
                                        );
                                }

                            
                        else{

                                   
                            $activeService = $this->User->account_active_service($post['userID']);
							if(!in_array(18, $activeService)){
										$response = array(
											'status' => 0,
											'message' => 'Sorry!! Payout not active.'
										);
									}
                                    
                                else{

                                            // get account detail
                                     $accountDetail = $this->User->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->row_array();
                                            $wallet_balance = $this->User->getMemberWalletBalanceSP($userID);
                                            
                                        if($accountDetail['transaction_password'] != do_hash($post['txn_pass'])){

                                                $response = array(
                                                  'status'  => 0,
                                                  'message' => 'Sorry!! transaction password is wrong.',
                                                  'bene_id' => $post['bene_id']
                                                );
                                            }
                                    

                                    else{

                                            $charge_amount = $this->User->get_dmr_surcharge($post['amount'],$userID);
                                            
                                            $min_wallet_balance = $accountDetail['min_wallet_balance'];

            
                                            //$total_wallet_deduct = $post['amount'] + $charge_amount;

                                            $total_wallet_deduct = $post['amount'] + $charge_amount + $min_wallet_balance;

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

                                            	$final_wallet_deduct = $post['amount'] + $charge_amount;
                                            	

                                                    $transaction_id = rand(111111,999999).time();

                                                    $after_balance =  - $final_wallet_deduct;    

                                                $wallet_data = array(
                                                	'account_id'=>$account_id,
                                                    'member_id'=>$userID,    
                                                    'before_balance'=> $wallet_balance,
                                                    'amount'=>$final_wallet_deduct,  
                                                    'after_balance'=>$after_balance,      
                                                    'status'              => 1,
                                                    'type'                => 2,
                                                    'wallet_type'         => 1,      
                                                    'created'             => date('Y-m-d H:i:s'),      
                                                    'description'         => 'Aeps Payout #'.$transaction_id.' Amount Deducted.'
                                                    );

                                                    $this->db->insert('member_wallet',$wallet_data);

                                                    log_message('debug', 'Fund transfer api called.');


                                                    $datapost = array();
                                                    $datapost['bene_id'] = $benificaryData['bene_id'];
                                                    $datapost['amount'] = $post['amount'];
                                                    $datapost['refid']  = $transaction_id;
                                                    $datapost['mode'] = 'IMPS';

                                                    

                                                    log_message('debug', 'newPayoutAuth API Account ID - '.$account_id.' Fund transfer api post request data - '.json_encode($datapost));  



                                                    
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


                                                log_message('debug', 'newPayoutAuth API Account ID - '.$account_id.' Fund transfer api final response - '.json_encode($raw_response));  



                                                    
                                                    $responseData = json_decode($raw_response,true);
                                                    
                                                    $api_data = array(
                                                      'account_id'=>$account_id,
                                                      'user_id' => $chk_user['id'],
                                                      'api_url' => $httpUrl,
                                                      'post_data' => json_encode($datapost),
                                                      'api_response' => $raw_response,
                                                      'created' => date('Y-m-d H:i:s')  
                                                    );
                                                    $this->db->insert('new_aeps_payout_api_response',$api_data);

                                                    

                                                    log_message('debug', 'newPayoutAuth API Account ID - '.$account_id.' Transfer Fund Final API Response - '.json_encode($raw_response)); 

                                                    $payoutData = array(
                                                       'account_id'=>$account_id,
                                                      'user_id' => $userID,
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
                                                        $this->db->where('account_id',$account_id);
                                                        $this->db->where('user_id',$userID);
                                                        $this->db->where('id',$transfer_id);
                                                        $this->db->update('new_aeps_payout',array('status'=>2,'ackno'=>$ackno,'updated'=>date('Y-m-d H:i:s')));

                                                        $response = array(
                                                          'status'  => 1,
                                                          'message' => 'Aeps payout successfully.' 
                                                        );
                                                    
                                                    }
                                                    else{

                                                        $accountDetail = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->row_array();

                                                        $wallet_balance = $this->User->getMemberWalletBalanceSP($userID);


                                                        $after_balance = $wallet_balance + $final_wallet_deduct;    

                                                        $wallet_data = array(
                                                        	'account_id' =>$account_id,
                                                            'member_id'           => $userID,    
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
                                                        $this->db->where('user_id',$userID);
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


			    
			    public function aepsPayoutCheckStatusAuth(){
			        
			        $account_id = $this->User->get_domain_account();
					$accountData = $this->User->get_account_data($account_id);

			        $response = array();
			        $post = $this->input->post();
			        
			         log_message('debug', 'newPayout Status Check API Account ID - '.$account_id.' aepsPayoutCheckStatusAuth API Post Data - '.json_encode($post));

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

			           //$account_id = $userID;

			           $ref_id = $post['ref_id'];

			           $chk_user = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->row_array();

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

			                        $chk_ref_id = $this->db->get_where('new_aeps_payout',array('account_id'=>$account_id,'user_id'=>$userID,'refid'=>$ref_id,'status < '=>3))->row_array();

			                        if(!$chk_ref_id){

			                            $response = array(
			                              'status'  => 0,
			                              'message' => 'Sorry!! refID not valid.' 
			                            );
			                        }
			                        else{

			                        
			                             log_message('debug', 'newPayoutCheckStatus API Account ID - '.$account_id.' Fund transfer check status api called.');

			                            $datapost = array();
			                            $datapost['refid'] = $ref_id;
			                            $datapost['ackno'] = $chk_ref_id['ackno'];

			                            
			                            log_message('debug', 'newPayoutCheckStatus API Account ID - '.$account_id.' Fund transfer check status api post request data - '.json_encode($datapost));



			                            
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
			                           	
			                            log_message('debug', 'newPayoutCheckStatus API Account ID - '.$account_id.' Fund transfer check status api final response - '.json_encode($raw_response));



			                            $responseData = json_decode($raw_response,true);
			                                
			                            $api_data = array(
			                            	'account_id' =>$account_id,
			                                'user_id' => $chk_user['id'],
			                                'api_url' => $httpUrl,
			                                'post_data' => json_encode($datapost),
			                                'api_response' => $raw_response,
			                                'created' => date('Y-m-d H:i:s')    
			                            );
			                            $this->db->insert('new_aeps_payout_api_response',$api_data);

			                            
			                            if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

			                                $acno = $responseData['data']['acno'];

			                                $utr = $responseData['data']['utr'];

			                                $this->db->where('account_id',$account_id);
			                                $this->db->where('user_id',$userID);
			                                $this->db->where('refid',$ref_id);
			                                $this->db->update('new_aeps_payout',array('status'=>2,'acno'=>$acno,'utr'=>$utr,'updated'=>date('Y-m-d H:i:s')));

			                                $response = array(
			                                   'status'  => 1,
			                                   'message' =>'Status checked successfully.'
			                                );
			                            }
			                            else{

			                                    $accountDetail = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->row_array();

			                                    $wallet_balance = $this->User->getMemberWalletBalanceSP($userID);

			                                    $after_balance = $wallet_balance + $chk_ref_id['total_wallet_deduct'];

			                                    $total_wallet_deduct = $chk_ref_id['total_wallet_deduct'];


			                                    $transaction_id = $refid;    

			                                    $wallet_data = array(
			                                    	'account_id'   =>$account_id,
			                                        'member_id'           => $userID,    
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
			                                    $this->db->where('user_id',$userID);
			                                    $this->db->where('refid',$ref_id);
			                                    $this->db->update('new_aeps_payout',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));

			                                    $error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! aeps payout failed.';

			                                    $response = array(
			                                       'status'  => 0,
			                                       'message' =>$error
			                                    );

			                                    
			                                }
			                        
			                       
			                    }
			                }
			           }        
			            
			        }
			        
			        log_message('debug', 'newPayoutCheckStatus API Account ID - '.$account_id.' aepsPayoutCheckStatusAuth API Response - '.json_encode($response));

			        echo json_encode($response);
			        
			    }


 				public function newPayoutBeneList()			    
			    {

			    	$account_id = $this->User->get_domain_account();
					$accountData = $this->User->get_account_data($account_id);


			        $post = $this->input->post();
			        

			         log_message('debug', 'newPayoutBeneList API Account ID - '.$account_id.' newPayoutBeneList API Post Data - '.json_encode($post));


			        $userID = isset($post['userID']) ? $post['userID'] : 0;
			        $response = array();
			        // check user valid or not
			        $chk_user = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->num_rows();
			        if($chk_user)
			        {
			            $historyList = $this->db->query("SELECT a.*,b.bank_name as bank_name FROM tbl_new_payout_beneficiary as a INNER JOIN tbl_new_payout_bank_list as b ON a.bank_id = b.id WHERE a.user_id = '$userID' AND a.account_id = '$account_id'")->result_array();

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
			       
			        log_message('debug', 'newPayoutBeneList API Account ID - '.$account_id.' newPayoutBeneList API Response - '.json_encode($response));

			        echo json_encode($response);
			    }




				public function newPayoutList()
			    {

			    	$account_id = $this->User->get_domain_account();
					$accountData = $this->User->get_account_data($account_id);
					$user_ip_address = $this->User->get_user_ip();

					$response = array();
					$token = '';
			        $header_data = apache_request_headers();
			        if($header_data && isset($header_data['Token']))
					{
						$token = $header_data['Token'];
			        }
			        log_message('debug', 'Get ICICI Payout Transfer History API Header - '.json_encode($header_data));	

			        if($token == '')
			        {
			        	$response = array(
							'status' => 0,
							'message' => 'Session out.Please Login Again.',
							'is_login' => 0
						);
			        }

			        else
			        {

			        $post = $this->input->post();
			        
			        log_message('debug', 'newPayoutList API Account ID - '.$account_id.' newPayoutList API Post Data - '.json_encode($post));

			        $userID = isset($post['userID']) ? $post['userID'] : 0;

			        // decrypt token
					$decryptToken = $this->User->generateAppToken('decrypt',$token);
					log_message('debug', 'Get newPayoutList Transfer History API Decrypt Token String - '.$decryptToken);
					$explodeToken = explode('|',$decryptToken);
					$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
					$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
					$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
							
					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
					log_message('debug', 'ICICI Payout History Decrypt Token - '.json_encode($chk_user_token));
					if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
					
					{

						$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{


					 // check user valid or not
			        $chk_user = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$userID))->num_rows();
			        if($chk_user)
			        {
			            $historyList = $this->db->query("SELECT a.*,b.account_holder_name,b.account_number FROM tbl_new_aeps_payout as a INNER JOIN tbl_new_payout_beneficiary as b ON a.bene_id = b.id WHERE a.user_id = '$userID' AND a.account_id = '$account_id' ORDER BY a.id DESC")->result_array();

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


				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
							);

				}


					}

					else
					{
						$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
							);
					}


			    }
		

			       log_message('debug', 'newPayoutList API Account ID - '.$account_id.' newPayoutList API Response - '.json_encode($response) );

			        echo json_encode($response);
			    }   
        
        
            
            
            public function iciciPayoutBeneficiaryAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Add Beneficiary Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }

        else
        {
		$post = $this->input->post();
		log_message('debug', 'iciciPayoutBeneficiaryAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bank_id', 'Bank ID', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter all  details.'
			);
		}
		else
		{	
			$userID = $post['userID'];

			// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Add Beneficiary Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'ICICI Beneficiary Auth Decrypt Token - '.json_encode($chk_user_token));
				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)

				{

					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $post['userID'] && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{
					
					$chk_beneficiary = $this->db->get_where('instantpay_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$userID))->row_array();

		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(20, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			elseif($chk_beneficiary == 3){

				$response = array(
					'status' => 0,
					'message' => 'Sorry!! you can add only three account.'
				);
			}
			else{

				$bene_data = array(
	        	 'account_id' => $account_id,	
	        	 'user_id' => $post['userID'],
	        	 'account_holder_name' => $post['account_holder_name'],
	        	 'bankID' => $post['bank_id'],
	        	 'account_no' => $post['account_number'],
	        	 'ifsc' => $post['ifsc'],
	        	 'encode_ban_id' => do_hash($post['account_number']),	
	        	 'status' => 1,
	        	 'created' => date('Y-m-d H:i:s')

	        	);
	        	
	        	$this->db->insert('instantpay_payout_user_benificary',$bene_data);

	        	$message = 'Congratulations!! beneficiary added successfully.';

		       

	        	$response = array(

	        	 'status'  => 1,
	        	 'message' => 'Congratulations!! beneficiary added successfully.'	

	        	);

	        }

				}

					else

					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);

					}

		    }	
	    	else
				{
					$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
				}
			}

	}
	    log_message('debug', 'ICICI Add Beneficiary Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function iciciPayoutAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$user_ip_address = $this->User->get_user_ip();
		$response = array();

		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Payout Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }

        else
        {

        	$post = $this->input->post();
		log_message('debug', 'ICICI Payout Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
		$this->form_validation->set_rules('beneID', 'beneID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|callback_maximumCheck|numeric');
		$this->form_validation->set_rules('txnType', 'Transaction Type', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(20, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! Payout not active.'
				);
			}
		  	else{

		  		$loggedAccountID = $post['userID'];
		  		// decrypt token
					$decryptToken = $this->User->generateAppToken('decrypt',$token);
					log_message('debug', 'Payout Auth API Decrypt Token String - '.$decryptToken);
					$explodeToken = explode('|',$decryptToken);
					$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
					$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
					$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
					$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

					$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
					log_message('debug', 'ICICI Payout Decrypt Token - '.json_encode($chk_user_token));

					if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
					{	
						$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();

						if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
						{
							$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

		  		$chk_beneficiary = $this->db->get_where('instantpay_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['beneID']))->row_array();

				if(!$chk_beneficiary){

					$response = array(

					  'status' => 0,
					  'message'=>'Sorry!! beneficiary not valid.'	

					);
				}	
				else{
				
					$memberID = $loggedUser['user_code'];
					$mobile = $loggedUser['mobile'];
					$account_holder_name = $chk_beneficiary['account_holder_name'];
					$account_no = $chk_beneficiary['account_no'];
					$ifsc = $chk_beneficiary['ifsc'];
					$bankID = $chk_beneficiary['bankID'];
					$amount = $post['amount'];
					$txnType = $post['txnType'];
					$transaction_id = time().rand(1111,9999);
					$receipt_id = rand(111111,999999);

					
					$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

					$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

		            
		            // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api Wallet Balance - '.$wallet_balance.'.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);

			        log_message('debug', 'ICICI Payout  API Account ID - '.$account_id.' AEPS Payout Api Wallet Balance - '.$chk_wallet_balance);	


		            // get dmr surcharge
		            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID,$txnType);
		            // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api - Payout Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
		            $this->User->generateLog($log_msg);

		            log_message('debug', 'ICICI Payout  API Account ID - '.$account_id.' AEPS Payout Api - Payout Surcharge Amount - '.$surcharge_amount);	


		            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            		$final_deduct_wallet_balance = $amount + $surcharge_amount + $min_wallet_balance;

		            $final_amount = $amount + $surcharge_amount;
		            $before_balance = $wallet_balance;

		            if($before_balance < $final_deduct_wallet_balance){
		                // save system log
		                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout Api - Insufficient Wallet Error]'.PHP_EOL;
		                $this->User->generateLog($log_msg);

		                log_message('debug', 'ICICI Payout  API Account ID - '.$account_id.' AEPS Payout Api - Insufficient Wallet Error');	


		                $response = array(

						  'status' => 0,
						  'message'=>'Sorry!! insufficient balance in your wallet.'	

						);
		            }
		            else{
		            	
		            	$chk_wallet_balance =$this->User->getMemberWalletBalanceSP($loggedAccountID);
		            	 $before_balance = $chk_wallet_balance;

		            
			            $after_wallet_balance = $before_balance - $final_amount;    

			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $loggedAccountID,    
			                'before_balance'      => $before_balance,
			                'amount'              => $final_amount,  
			                'after_balance'       => $after_wallet_balance,      
			                'status'              => 1,
			                'type'                => 2,   
			                'wallet_type'		  => 1,   
			                'created'             => date('Y-m-d H:i:s'),      
			                'description'         => 'ICICI Payout  #'.$transaction_id.' Amount Deducted.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			            // save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - AEPS Payout API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
			            $this->User->generateLog($log_msg);

			            log_message('debug', 'ICICI Payout  API Account ID - '.$account_id.' ICICI Payout Api - Member Wallet Deduction - Updated Balance - - '.$after_wallet_balance);	


						$data = array(
							'account_id' => $account_id,
							'user_id' => $loggedAccountID,
							'transfer_amount' => $amount,
							'transfer_charge_amount' => $surcharge_amount,
							'total_wallet_charge' => $final_amount,
							'after_wallet_balance' => $after_wallet_balance,
							'txnType' => $txnType,
							'transaction_id' => $transaction_id,
							'encode_transaction_id' => do_hash($transaction_id),
							'status' => 2,
							'wallet_type' => 1,
							'invoice_no' => $receipt_id,
							'memberID' => $memberID,
							'mobile' => $mobile,
							'account_holder_name' => $account_holder_name,
							'account_no' => $account_no,
							'ifsc' => $ifsc,
							'is_app' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_new_fund_transfer',$data); 
						$recordID = $this->db->insert_id();

						
						// save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - ICICI Payout Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
			            $this->User->generateLog($log_msg);

			            $api_url = INSTANTPAY_PAYOUT_API_URL;

			            log_message('debug', 'ICICI Payout  API Account ID - '.$account_id.' ICICI Payout Api - Call '.$api_url);	

			            $request = array(
		                
		                'payer' => array(
    		                'bankId' => '0',
    		                'bankProfileId' => 0,
    		                'accountNumber' => $accountData['instant_account_no'],
    		                ),
    		                
	                        'payee' => array(
	                            'name' => $account_holder_name,
	                            'accountNumber' => $account_no,
	                            'bankIfsc' =>$ifsc
	                       ),
	                       'transferMode' => $txnType,
	                       'transferAmount' => $amount,
	                       'externalRef' => $transaction_id,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       'remarks'  => 'Payout',
	                       'alertEmail' => $loggedUser['email'],
	                       'purpose' =>'REIMBURSEMENT'
		                
		            );



		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                
		                'content-type: application/json'
		                
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
	       	
	        		curl_close ($curl);

	        /*$output = '{"Error":"True","Message":"Invalid member or password and invalid request ip address please whitelist your ip","Data":null}';*/

	        $responseData = json_decode($output,true);
	        
	        
	         $apiData = array(
				'account_id' => $account_id,
				'user_id' => $loggedAccountID,
				'api_response' => $output,
				'api_url' => $api_url,
				'post_data'=>json_encode($request),
				'created' => date('Y-m-d H:i:s'),
				'created_by'=>$loggedAccountID
			);
			$this->db->insert('instantpay_api_response',$apiData);
			

	        	log_message('debug', 'ICICI Payout  API Account ID - '.$account_id.' ICICI Payout Api Response - '.json_encode($output));	

        				
				if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
						{

							$rrno = $responseData['data']['txnReferenceId'];
							$this->db->where('account_id',$account_id);
							$this->db->where('user_id',$loggedAccountID);
							$this->db->where('id',$recordID);
							$this->db->update('user_new_fund_transfer',array('api_response'=>$output,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));

							$response = array(

							 'status' => 1,
							 'message'=>'Congratulations!! transfered successfully.'	

							);
							
						}

						elseif(isset($responseData['statuscode']) && ($responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR'))
						{

							$api_msg = $responseData['status'];
    
    					$log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
    
    					$this->User->generateLog($log_msg);
    					            
    					$this->db->where('id',$recordID);
    					$this->db->where('account_id',$account_id);
    					$this->db->where('user_id',$loggedAccountID);
    					$this->db->update('user_new_fund_transfer',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

    					            //refund amount to wallet

    					            // get wallet balance
            				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
            			            $after_wallet_balance = $before_wallet_balance + $final_amount;    
            			            
            			            $wallet_data = array(
            	                        'account_id'          => $account_id,
            	                        'member_id'           => $loggedAccountID,    
            	                        'before_balance'      => $before_wallet_balance,
            	                        'amount'              => $final_amount,  
            	                        'after_balance'       => $after_wallet_balance,      
            	                        'status'              => 1,
            	                        'type'                => 1, 
            	                        'wallet_type'         => 1,      
            	                        'created'             => date('Y-m-d H:i:s'),      
            	                        'description'         => 'ICICI Payout #'.$transaction_id.' Amount Refund Credited.'
            	                    );
            
            	                    $this->db->insert('member_wallet',$wallet_data);
            						
									$response = array(

									 'status' => 0,
									 'message'=>'Sorry ! Transaction Failed Due to .'.$api_msg	

									);
							
						}
						else
						{
						   $response = array(

							 'status' => 1,
							 'message'=>'Your transaction is under processing, status will be updated soon.'	

							);	
							
						}

					}
				  }

				}

				else

						{
							$response = array(
							  'status' => 0,
							  'message' => 'Session out.Please Login Again.',
							  'is_login'=>0	
							);
						}

					}

					else
					{
						$response = array(
							  'status' => 0,
							  'message' => 'Session out.Please Login Again.',
							  'is_login'=>0	
							);

					}

			  	}
			}

        }
		
	    log_message('debug', 'ICICI Payout Auth API Response - '.json_encode($response));	
		echo json_encode($response);
	}


	//icici payout history

	public function getIciciPayoutTransferHistory(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get ICICI Payout Transfer History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {

			$post = $this->input->post();
			$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
			$fromDate = $post['fromDate'];
	        $toDate = $post['toDate'];

	    	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
	    	$limit = $page_no * 50;
	    	// decrypt token
			$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get ICICI Payout Transfer History API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
					
			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'ICICI Payout History Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{
				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $user_id && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
			    	if($fromDate && $toDate){

    		$count = $this->db->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

		    $limit_start = $limit - 50; 
		                     
		    $limit_end = $limit;

			$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();

		}	

		else{

			   $count = $this->db->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->num_rows();

			   $limit_start = $limit - 50; 
			                     
			   $limit_end = $limit;	
	    	  		
			   $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->result_array();

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
							elseif($list['status'] == 4 && $list['force_status'] == 1) {
								$data[$key]['status'] = 'Refund';
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
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
				  'status' => 0,
				  'message' => 'Session out.Please Login Again.',
				  'is_login'=>0	
				);
			}
		}
		log_message('debug', 'Get ICICI Payout List API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
		public function iciciPayoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
    	$user_ip_address = $this->User->get_user_ip();
    	// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Account Member List API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else

        {
        	$post = $this->input->post();
		log_message('debug', 'ICICI payoutBeneficiaryList List API Post Data - '.json_encode($post));	
		$userID = isset($post['userID']) ? $post['userID'] : 0;
    	$response = array();
    	
    	$activeService = $this->User->account_active_service($post['userID']);
		if(!in_array(20, $activeService)){
			$response = array(
				'status' => 0,
				'message' => 'Sorry!! this service is not active for you.'
			);
		}
		else{

				$decryptToken = $this->User->generateAppToken('decrypt',$token);
			log_message('debug', 'Get Payout Beneficiary List API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
					
			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'Payout Beneficary List Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
			{	

				$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{
					$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
			if($chk_user)
			{
				$benificaryList = $this->db->select('tbl_instantpay_payout_user_benificary.*,aeps_bank_list.bank_name')->join('aeps_bank_list','aeps_bank_list.id = tbl_instantpay_payout_user_benificary.bankID')->get_where('tbl_instantpay_payout_user_benificary',array('tbl_instantpay_payout_user_benificary.account_id'=>$account_id,'tbl_instantpay_payout_user_benificary.user_id'=>$userID))->result_array();

				$data = array();
				if($benificaryList)
				{
					foreach ($benificaryList as $key => $list) {
						
						$data[$key]['beneID'] = $list['id'];
						$data[$key]['account_holder_name'] = $list['account_holder_name'];
						$data[$key]['account_no'] = $list['account_no'];
						$data[$key]['bank_name'] = $list['bank_name'];
						$data[$key]['ifsc'] = $list['ifsc'];
						$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
						
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

				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);

				}

			}

			else

			{
				$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
			
			}
	    	
		}

      }
    	
		log_message('debug', 'ICICI Beneficiary List API Response - '.json_encode($response));	
		echo json_encode($response);
    }

	
	public function bankAccountVerifyAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'bankAccountVerifyAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));
		$this->User->generateLog($log_msg);	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');	
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('account_number', 'Account Number', 'required|xss_clean');
		$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{
		  	$loggedAccountID = $post['userID'];

		  	$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();


			$chk_wallet_balance = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$loggedAccountID))->row_array();

				$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

				$get_verification_charge = $this->db->get_where('dmr_account_verify_charge',array('account_id'=>$account_id,'package_id'=>$chk_wallet_balance['package_id']))->row_array();

				$verification_charge = 0;

				if($chk_wallet_balance['role_id'] == 3){

					$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 
				}
				elseif($chk_wallet_balance['role_id'] == 4){

					$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 
				}
				elseif($chk_wallet_balance['role_id'] == 5){

					$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 
				}
				elseif($chk_wallet_balance['role_id'] == 8){

					$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 
				}

				$admin_id = $this->User->get_admin_id($account_id);
			
				$admin_wallet_balance = $this->User->get_admin_wallet_balance($admin_id);
				
				//get admin verification charge

				$account_package_id = $this->User->get_account_package_id($account_id);

				$admin_charge = $this->db->get_where('account_verification_commision',array('package_id'=>$account_package_id))->row_array();

				$admin_verification_charge = isset($admin_charge['commision']) ? $admin_charge['commision'] : 0; 

				if($admin_wallet_balance < $admin_verification_charge){

					$response = array(

						'status' => 0,
						'message'=> 'Sorry!! insufficient balance in your admin wallet.'

					);

				}
				else{	

					if($wallet_balance < $verification_charge){

						$response = array(

							'status'  => 0,
							'message' => 'Sorry!! you have insufficient balance in your wallet.'

						);
					}
					else{

						$transid = rand(111111,999999).time();
			            $name = isset($post['name']) ? $post['name'] : '';
			            $account_number = isset($post['account_number']) ? $post['account_number'] : '';
			            $ifsc = isset($post['ifsc']) ? $post['ifsc'] : '';
			            $bank_verification_url = BANK_VERIFICATION_URL;

			            $response = array();

			            $request = array(
		                
		               
    		                
	                        'payee' => array(
	                            
	                            'accountNumber' =>$account_number,
	                            'bankIfsc' =>$ifsc
	                       ),
	                     
	                       'externalRef' => 'PPT223',
	                       'consent'    =>'Y',
	                       'isCached'  => 0,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       
		                
		            );




		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',		                
		                'content-type: application/json'
		                
		            );
		            
		         
		            $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $bank_verification_url);

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

		            $responseData = json_decode($output,true);
			          

			            $response_data = $responseData['data']['payee'];
			            

			            if(isset($responseData) && $responseData['statuscode'] == "TXN" && $responseData['status'] == "Transaction Successful"){

			            	$after_balance = $wallet_balance - $verification_charge;

			                $history = array(

			                   'account_id' => $account_id,
			                   'member_id'  => $loggedAccountID,
			                   'api_url'    => $bank_verification_url,
			                   'post_data'	=> json_encode($post),
			                   'api_response' => json_encode($responseData),
			                   'txn_id' => $transid,
			                   'before_balance' => $wallet_balance,
			                   'amount' => $verification_charge,
			                   'after_balance' => $after_balance, 
			                   'status' => 'Success',
			                   'created' => date('Y-m-d H:i:s') 
			                );

			                $this->db->insert('bank_verification',$history);

			                //wallet deduct

			                $wallet_data = array(
		                        'account_id'          => $account_id,
		                        'member_id'           => $loggedAccountID,    
		                        'before_balance'      => $wallet_balance,
		                        'amount'              => $verification_charge,  
		                        'after_balance'       => $after_balance,      
		                        'status'              => 1,
		                        'type'                => 2,      
		                        'created'             => date('Y-m-d H:i:s'),      
		                        'description'         => 'Bank Account Verification #'.$transid.' Amount Deducted.'
		                    );

		                    $this->db->insert('member_wallet',$wallet_data);

			                $response = array(
			                  'status' => 1,
			                  'message'=> 'Success',
			                  'account_number' => $response_data['account'],
			                  'account_holder_name' => $response_data['name'],
			                  'response_status'  => $responseData['status'],
			                  'response_message' => $responseData['status']
			                );
			            }
			            else{


			                $history = array(

			                   'account_id' => $account_id,
			                   'member_id'  => $loggedAccountID,
			                   'api_url'    => $bank_verification_url,
			                   'post_data'	=> json_encode($post),
			                   'api_response' => json_encode($responseData),
			                   'txn_id' => $transid,
			                   'before_balance' => $wallet_balance,
			                   'amount' => $verification_charge,
			                   'after_balance' => $after_balance, 
			                   'status' => 'Failed',
			                   'created' => date('Y-m-d H:i:s') 
			                );

			                $this->db->insert('bank_verification',$history);

			                $response = array(
			                 'status' => 0,
			                 'msg'=>$responseData['status']    
			                );
			            }

					}
				}
				

			}
		  	
	    //}

	    log_message('debug', 'bankAccountVerifyAuth Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
	
	
	public function upiAccountVerifyAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'upiAccountVerifyAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));
		$this->User->generateLog($log_msg);	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');	
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('account_number', 'Account Number', 'required|xss_clean');
		//$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{
		  	$loggedAccountID = $post['userID'];

		  	$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();


			$chk_wallet_balance = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$loggedAccountID))->row_array();

				$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

				$get_verification_charge = $this->db->get_where('account_verification_commision',array('account_id'=>$account_id,'package_id'=>$chk_wallet_balance['package_id']))->row_array();

				$verification_charge = 0;

				if($chk_wallet_balance['role_id'] == 3){

					$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 
				}
				elseif($chk_wallet_balance['role_id'] == 4){

					$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 
				}
				elseif($chk_wallet_balance['role_id'] == 5){

					$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 
				}
				elseif($chk_wallet_balance['role_id'] == 8){

					$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 
				}

				$admin_id = $this->User->get_admin_id($account_id);
			
				$admin_wallet_balance = $this->User->get_admin_wallet_balance($admin_id);
				
				//get admin verification charge

				$account_package_id = $this->User->get_account_package_id($account_id);

				$admin_charge = $this->db->get_where('account_verification_commision',array('package_id'=>$account_package_id))->row_array();

				$admin_verification_charge = isset($admin_charge['commision']) ? $admin_charge['commision'] : 0; 

				if($admin_wallet_balance < $admin_verification_charge){

					$response = array(

						'status' => 0,
						'message'=> 'Sorry!! insufficient balance in your admin wallet.'

					);

				}
				else{	

					if($wallet_balance < $verification_charge){

						$response = array(

							'status'  => 0,
							'message' => 'Sorry!! you have insufficient balance in your wallet.'

						);
					}
					else{

						$transid = rand(111111,999999).time();
			            $name = isset($post['name']) ? $post['name'] : '';
			            $account_number = isset($post['account_number']) ? $post['account_number'] : '';
			            //$ifsc = isset($post['ifsc']) ? $post['ifsc'] : '';
			            $bank_verification_url = BANK_VERIFICATION_URL;

			            $response = array();

			            $request = array(
		                
		               
    		                
	                        'payee' => array(
	                            
	                            'accountNumber' =>$account_number,
	                            'bankIfsc' =>$ifsc
	                       ),
	                     
	                       'externalRef' => 'PPT223',
	                       'consent'    =>'Y',
	                       'isCached'  => 0,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       
		                
		            );




		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',		                
		                'content-type: application/json'
		                
		            );
		            
		         
		            $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $bank_verification_url);

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

		            $responseData = json_decode($output,true);
			          

			            $response_data = $responseData['data']['payee'];
			            

			            if(isset($responseData) && $responseData['statuscode'] == "TXN" && $responseData['status'] == "Transaction Successful"){

			            	$after_balance = $wallet_balance - $verification_charge;

			                $history = array(

			                   'account_id' => $account_id,
			                   'member_id'  => $loggedAccountID,
			                   'api_url'    => $bank_verification_url,
			                   'post_data'	=> json_encode($post),
			                   'api_response' => json_encode($responseData),
			                   'txn_id' => $transid,
			                   'before_balance' => $wallet_balance,
			                   'amount' => $verification_charge,
			                   'after_balance' => $after_balance, 
			                   'status' => 'Success',
			                   'created' => date('Y-m-d H:i:s') 
			                );

			                $this->db->insert('bank_verification',$history);

			                //wallet deduct

			                $wallet_data = array(
		                        'account_id'          => $account_id,
		                        'member_id'           => $loggedAccountID,    
		                        'before_balance'      => $wallet_balance,
		                        'amount'              => $verification_charge,  
		                        'after_balance'       => $after_balance,      
		                        'status'              => 1,
		                        'type'                => 2,      
		                        'created'             => date('Y-m-d H:i:s'),      
		                        'description'         => 'Bank Account Verification #'.$transid.' Amount Deducted.'
		                    );

		                    $this->db->insert('member_wallet',$wallet_data);

			                $response = array(
			                  'status' => 1,
			                  'message'=> 'Success',
			                  'account_number' => $response_data['account'],
			                  'account_holder_name' => $response_data['name'],
			                  'response_status'  => $responseData['status'],
			                  'response_message' => $responseData['status']
			                );
			            }
			            else{


			                $history = array(

			                   'account_id' => $account_id,
			                   'member_id'  => $loggedAccountID,
			                   'api_url'    => $bank_verification_url,
			                   'post_data'	=> json_encode($post),
			                   'api_response' => json_encode($responseData),
			                   'txn_id' => $transid,
			                   'before_balance' => $wallet_balance,
			                   'amount' => $verification_charge,
			                   'after_balance' => $after_balance, 
			                   'status' => 'Failed',
			                   'created' => date('Y-m-d H:i:s') 
			                );

			                $this->db->insert('bank_verification',$history);

			                $response = array(
			                 'status' => 0,
			                 'msg'=>$responseData['status']    
			                );
			            }

					}
				}
				

			}
		  	
	    //}

	    log_message('debug', 'upiAccountVerifyAuth Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
	
	public function upiPayoutBeneficiaryAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'iciciPayoutBeneficiaryAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        //$this->form_validation->set_rules('bank_id', 'Bank ID', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        //$this->form_validation->set_rules('ifsc', 'IFSC', 'required');*/

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please all details.'
			);
		}
		else
		{	
			$userID = $post['userID'];

			$chk_beneficiary = $this->db->get_where('instantpay_upi_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$userID))->row_array();
            
            	$chk_upi_beneficiary = $this->db->get_where('instantpay_upi_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$userID))->num_rows();
            	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(20, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			elseif($chk_upi_beneficiary == 3){

				$response = array(
					'status' => 0,
					'message' => 'Sorry!! you can add only three account.'
				);
			}
			else{

				$bene_data = array(
	        	 'account_id' => $account_id,	
	        	 'user_id' => $post['userID'],
	        	 'account_holder_name' => $post['account_holder_name'],
	        	 //'bankID' => $post['bank_id'],
	        	 'account_no' => $post['account_number'],
	        	 //'ifsc' => $post['ifsc'],
	        	 'encode_ban_id' => do_hash($post['account_number']),	
	        	 'status' => 1,
	        	 'created' => date('Y-m-d H:i:s')

	        	);
	        	
	        	$this->db->insert('instantpay_upi_payout_user_benificary',$bene_data);

	        	$message = 'Congratulations!! beneficiary added successfully.';

		       

	        	$response = array(

	        	 'status'  => 1,
	        	 'message' => 'Congratulations!! beneficiary added successfully.'	

	        	);

	        }
	    }
	    log_message('debug', 'ICICI Add Beneficiary Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
	
	public function upiPayoutAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'Upi Payout Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
		$this->form_validation->set_rules('beneID', 'beneID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|callback_maximumCheck|numeric');
		$this->form_validation->set_rules('txnType', 'Transaction Type', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(20, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! Payout not active.'
				);
			}
		  	else{

		  		$loggedAccountID = $post['userID'];

		  		$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

		  		$chk_beneficiary = $this->db->get_where('instantpay_upi_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['beneID']))->row_array();

				if(!$chk_beneficiary){

					$response = array(

					  'status' => 0,
					  'message'=>'Sorry!! beneficiary not valid.'	

					);
				}	
				else{
				
					$memberID = $loggedUser['user_code'];
					$mobile = $loggedUser['mobile'];
					$account_holder_name = $chk_beneficiary['account_holder_name'];
					$account_no = $chk_beneficiary['account_no'];					
					$amount = $post['amount'];
					$txnType = $post['txnType'];
					$transaction_id = time().rand(1111,9999);
					$receipt_id = rand(111111,999999);

					
					$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

					$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

		            
		            // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Upi Payout Api Wallet Balance - '.$wallet_balance.'.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);

			        log_message('debug', 'ICICI UPI Payout  API Account ID - '.$account_id.' Upi Payout Api Wallet Balance - '.$chk_wallet_balance);	


		            // get dmr surcharge
		            $surcharge_amount = $this->User->get_dmr_surcharge($amount,$loggedAccountID,$txnType);
		            // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - UPI Payout Api - Payout Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
		            $this->User->generateLog($log_msg);

		            log_message('debug', 'Upi Payout  API Account ID - '.$account_id.' Upi Payout Api - Payout Surcharge Amount - '.$surcharge_amount);	


		            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            		$final_deduct_wallet_balance = $amount + $surcharge_amount + $min_wallet_balance;

		            $final_amount = $amount + $surcharge_amount;
		            $before_balance = $wallet_balance;

		            if($before_balance < $final_deduct_wallet_balance){
		                // save system log
		                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Upi Payout Api - Insufficient Wallet Error]'.PHP_EOL;
		                $this->User->generateLog($log_msg);

		                log_message('debug', 'Upi Payout  API Account ID - '.$account_id.' Upi Payout Api - Insufficient Wallet Error');	


		                $response = array(

						  'status' => 0,
						  'message'=>'Sorry!! insufficient balance in your wallet.'	

						);
		            }
		            else{
		            	

		            	$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);


			            $after_wallet_balance = $before_balance - $final_amount;    

			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $loggedAccountID,    
			                'before_balance'      => $before_balance,
			                'amount'              => $final_amount,  
			                'after_balance'       => $after_wallet_balance,      
			                'status'              => 1,
			                'type'                => 2,   
			                'wallet_type'		  => 1,   
			                'created'             => date('Y-m-d H:i:s'),      
			                'description'         => 'UPI Payout #'.$transaction_id.' Amount Deducted.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			            // save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Upi Payout API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
			            $this->User->generateLog($log_msg);

			            log_message('debug', 'ICICI Payout  API Account ID - '.$account_id.' Upi Payout Api - Member Wallet Deduction - Updated Balance - - '.$after_wallet_balance);	

						$data = array(
							'account_id' => $account_id,
							'user_id' => $loggedAccountID,
							'transfer_amount' => $amount,
							'transfer_charge_amount' => $surcharge_amount,
							'total_wallet_charge' => $final_amount,
							'after_wallet_balance' => $after_wallet_balance,
							'txnType' => $txnType,
							'transaction_id' => $transaction_id,
							'encode_transaction_id' => do_hash($transaction_id),
							'status' => 2,
							'wallet_type' => 1,
							'invoice_no' => $receipt_id,
							'memberID' => $memberID,
							'mobile' => $mobile,
							'account_holder_name' => $account_holder_name,
							'account_no' => $account_no,							
							'is_app' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_new_fund_transfer',$data); 
						$recordID = $this->db->insert_id();

						// save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - ICICI Payout Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
			            $this->User->generateLog($log_msg);

			            $api_url = INSTANTPAY_PAYOUT_API_URL;

			            log_message('debug', 'Upi Payout  API Account ID - '.$account_id.' Upi Payout Api - Call '.$api_url);	

			            $request = array(
		                
		                'payer' => array(
    		                'bankId' => '0',
    		                'bankProfileId' => 0,
    		                'accountNumber' => $accountData['instant_account_no'],
    		                ),
    		                
	                        'payee' => array(
	                            'name' => $account_holder_name,
	                            'accountNumber' => $account_no,
	                            'bankIfsc' =>$ifsc
	                       ),
	                       'transferMode' => $txnType,
	                       'transferAmount' => $amount,
	                       'externalRef' => $transaction_id,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       'remarks'  => 'Payout',
	                       'alertEmail' => $loggedUser['email'],
	                       'purpose' =>'REIMBURSEMENT'
		                
		            );



		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                
		                'content-type: application/json'
		                
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
	       	
	        		curl_close ($curl);

	        /*$output = '{"Error":"True","Message":"Invalid member or password and invalid request ip address please whitelist your ip","Data":null}';*/

	        $responseData = json_decode($output,true);
	        
	        
	         $apiData = array(
				'account_id' => $account_id,
				'user_id' => $loggedAccountID,
				'api_response' => $output,
				'api_url' => $api_url,
				'post_data'=>json_encode($request),
				'created' => date('Y-m-d H:i:s'),
				'created_by'=>$loggedAccountID
			);
			$this->db->insert('instantpay_api_response',$apiData);
			

	        	log_message('debug', 'Upi Payout  API Account ID - '.$account_id.' Upi Payout Api Response - '.json_encode($output));	

        				
				if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
						{

							$rrno = $responseData['data']['txnReferenceId'];
							$this->db->where('account_id',$account_id);
							$this->db->where('user_id',$loggedAccountID);
							$this->db->where('id',$recordID);
							$this->db->update('user_new_fund_transfer',array('api_response'=>$output,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));

							$response = array(

							 'status' => 1,
							 'message'=>'Congratulations!! transfered successfully.'	

							);
							
						}


						elseif(isset($responseData['statuscode']) && ($responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR'))
						{

							$api_msg = $responseData['status'];
    
			    			$log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
			    
			    			$this->User->generateLog($log_msg);
    					            
    					$this->db->where('id',$recordID);
    					$this->db->where('account_id',$account_id);
    					$this->db->where('user_id',$loggedAccountID);
    					$this->db->update('user_new_fund_transfer',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

    					            //refund amount to wallet

    					            // get wallet balance
            				        
            				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            			            $after_wallet_balance = $before_wallet_balance + $final_amount;    
            			            
            			            $wallet_data = array(
            	                        'account_id'          => $account_id,
            	                        'member_id'           => $loggedAccountID,    
            	                        'before_balance'      => $before_wallet_balance,
            	                        'amount'              => $final_amount,  
            	                        'after_balance'       => $after_wallet_balance,      
            	                        'status'              => 1,
            	                        'type'                => 1, 
            	                        'wallet_type'         => 1,      
            	                        'created'             => date('Y-m-d H:i:s'),      
            	                        'description'         => 'UPI Payout #'.$transaction_id.' Amount Refund Credited.'
            	                    );
            
            	                    $this->db->insert('member_wallet',$wallet_data);
            						
							$response = array(

							 'status' => 0,
							 'message'=>'Sorry ! Transaction Failed Due to'.$api_msg	

							);
							
						}
						
						else
						{
							
				            $response = array(

							 'status' => 1,
							 'message'=>'Your transaction is under processing, status will be updated soon.'	

							);	
							
						}
					}

					
			  	}
			}
		  	
	    }
	    log_message('debug', 'UPI Payout Auth API Response - '.json_encode($response));	
		echo json_encode($response);




	}
	
	
	public function upiPayoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
    	$post = $this->input->post();
		log_message('debug', 'UPI payoutBeneficiaryList List API Post Data - '.json_encode($post));	
		$userID = isset($post['userID']) ? $post['userID'] : 0;
    	$response = array();
    	
    	$activeService = $this->User->account_active_service($post['userID']);
		if(!in_array(20, $activeService)){
			$response = array(
				'status' => 0,
				'message' => 'Sorry!! this service is not active for you.'
			);
		}
		else{

	    	// check user valid or not
			$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
			if($chk_user)
			{

				$benificaryList = $this->db->get_where('tbl_instantpay_upi_payout_user_benificary',array('tbl_instantpay_upi_payout_user_benificary.account_id'=>$account_id,'tbl_instantpay_upi_payout_user_benificary.user_id'=>$userID))->result_array();

				$data = array();
				if($benificaryList)
				{
					foreach ($benificaryList as $key => $list) {
						
						$data[$key]['beneID'] = $list['id'];
						$data[$key]['account_holder_name'] = $list['account_holder_name'];
						$data[$key]['account_no'] = $list['account_no'];
						//$data[$key]['bank_name'] = $list['bank_name'];
						//$data[$key]['ifsc'] = $list['ifsc'];
						$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
						
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
		log_message('debug', 'Upi Beneficiary List API Response - '.json_encode($response));	
		echo json_encode($response);
    }
				    
		
		
	public function getUpiPayoutTransferHistory(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

    	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

    	if($fromDate && $toDate){

    		$count = $this->db->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'txnType'=>'UPI','user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

		    $limit_start = $limit - 50; 
		                     
		    $limit_end = $limit;

			$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'txnType'=>'UPI','user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();
			
		
			

		}	
		else{

		   $count = $this->db->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->num_rows();

		   $limit_start = $limit - 50; 
		                     
		   $limit_end = $limit;	
    	  		
		   $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,'txnType'=>'UPI'))->result_array();

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
				//$data[$key]['ifsc'] = $list['ifsc'];
				$data[$key]['transfer_amount'] = $list['transfer_amount'].' /-';
				$data[$key]['transaction_id'] = $list['transaction_id'];
				$data[$key]['rrn'] = $list['rrn'];
						
				if($list['status'] == 2 || $list['status'] == 0) {
					$data[$key]['status'] = 'Pending';
				}
				elseif($list['status'] == 3) {
					$data[$key]['status'] = 'Success';
				}
				elseif($list['status'] == 4) {
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
		log_message('debug', 'Upi Payout Transfer List API Response - '.json_encode($response));	
		echo json_encode($response);

	}	
    


    //open payout api



    public function iciciOpenPayoutBeneficiaryAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$user_ip_address = $this->User->get_user_ip();

		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'addPayoutBeneficiaryAuth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else

        {
        	$response = array();
		$post = $this->input->post();
		log_message('debug', 'openPayoutBeneficiaryAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bank_id', 'Bank ID', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please all details.'
			);
		}
		else
		{	

			$userID = $post['userID'];

			$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'openPayoutBeneficiaryAuth API Decrypt Token String - '.$decryptToken);
			$explodeToken = explode('|',$decryptToken);
			$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
			$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
			$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
			$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';
			$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
			log_message('debug', 'openPayoutBeneficiaryAuth Decrypt Token - '.json_encode($chk_user_token));

			if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{

					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $userID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
					{

					$chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$userID))->row_array();

		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(6, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			
			else{

				$bene_data = array(
	        	 'account_id' => $account_id,	
	        	 'user_id' => $post['userID'],
	        	 'account_holder_name' => $post['account_holder_name'],
	        	 'bankID' => $post['bank_id'],
	        	 'account_no' => $post['account_number'],
	        	 'ifsc' => $post['ifsc'],
	        	 'encode_ban_id' => do_hash($post['account_number']),	
	        	 'status' => 1,
	        	 'created' => date('Y-m-d H:i:s')

	        	);
	        	
	        	$this->db->insert('user_benificary',$bene_data);

	        	$message = 'Congratulations!! beneficiary added successfully.';

		       

	        	$response = array(

	        	 'status'  => 1,
	        	 'message' => 'Congratulations!! beneficiary added successfully.'	

	        	);

	        }
	    }
	    else
	    {
	    	$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
				}
	      }

	   

				else

				{
					$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
				}

				
	    }

        }


		
	    log_message('debug', 'Open Payout Add Beneficiary Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}


	public function iciciOpenPayoutAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$user_ip_address = $this->User->get_user_ip();
		$response = array();
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'User Detail API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }

        else

        {


		$post = $this->input->post();
		log_message('debug', 'Open Payout Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
		$this->form_validation->set_rules('beneID', 'beneID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|callback_maximumCheck|numeric');
		$this->form_validation->set_rules('txnType', 'Transaction Type', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(6, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! Payout not active.'
				);
			}
		  	else{

		  		$loggedAccountID = $post['userID'];
		  		// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'User Detail API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

				$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
				log_message('debug', 'User Detail Check User Decrypt Token - '.json_encode($chk_user_token));
				if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();

					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
						{

							$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

		  		$chk_beneficiary = $this->db->get_where('user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['beneID']))->row_array();

				if(!$chk_beneficiary){

					$response = array(

					  'status' => 0,
					  'message'=>'Sorry!! beneficiary not valid.'	

					);
				}	
				else{
				
					$memberID = $loggedUser['user_code'];
					$mobile = $loggedUser['mobile'];
					$account_holder_name = $chk_beneficiary['account_holder_name'];
					$account_no = $chk_beneficiary['account_no'];
					$ifsc = $chk_beneficiary['ifsc'];
					$bankID = $chk_beneficiary['bankID'];
					$amount = $post['amount'];
					$txnType = $post['txnType'];
					$transaction_id = time().rand(1111,9999);
					$receipt_id = rand(111111,999999);

					
					$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

					$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

		            
		            // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Open Payout Api Wallet Balance - '.$wallet_balance.'.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);

			        log_message('debug', 'Open Payout  API Account ID - '.$account_id.' Open Payout Api Wallet Balance - '.$chk_wallet_balance);	


		            // get dmr surcharge
		            $surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$loggedAccountID);
		            // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Open Payout Api - Payout Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
		            $this->User->generateLog($log_msg);

		            log_message('debug', 'Open Payout  API Account ID - '.$account_id.' Open Payout Api - Payout Surcharge Amount - '.$surcharge_amount);	


		            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            		$final_deduct_wallet_balance = $amount + $surcharge_amount + $min_wallet_balance;

		            $final_amount = $amount + $surcharge_amount;
		            $before_balance = $wallet_balance;

		            if($before_balance < $final_deduct_wallet_balance){
		                // save system log
		                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Open Payout Api - Insufficient Wallet Error]'.PHP_EOL;
		                $this->User->generateLog($log_msg);

		                log_message('debug', 'ICICI Payout  API Account ID - '.$account_id.' Open Payout Api - Insufficient Wallet Error');	


		                $response = array(

						  'status' => 0,
						  'message'=>'Sorry!! insufficient balance in your wallet.'	

						);
		            }
		            else{

		            	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

		            	$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		            
			            $after_wallet_balance = $before_balance - $final_amount;    

			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $loggedAccountID,    
			                'before_balance'      => $before_balance,
			                'amount'              => $final_amount,  
			                'after_balance'       => $after_wallet_balance,      
			                'status'              => 1,
			                'type'                => 2,   
			                'wallet_type'		  => 1,   
			                'created'             => date('Y-m-d H:i:s'),      
			                'description'         => 'Open Payout #'.$transaction_id.' Amount Deducted.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			            // save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Open Payout API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
			            $this->User->generateLog($log_msg);

			            log_message('debug', 'ICICI Payout  API Account ID - '.$account_id.' Open Payout Api - Member Wallet Deduction - Updated Balance - - '.$after_wallet_balance);	


						$data = array(
							'account_id' => $account_id,
							'user_id' => $loggedAccountID,
							'transfer_amount' => $amount,
							'transfer_charge_amount' => $surcharge_amount,
							'total_wallet_charge' => $final_amount,
							'after_wallet_balance' => $after_wallet_balance,
							'txnType' => $txnType,
							'transaction_id' => $transaction_id,
							'encode_transaction_id' => do_hash($transaction_id),
							'status' => 2,
							'wallet_type' => 1,
							'invoice_no' => $receipt_id,
							'memberID' => $memberID,
							'mobile' => $mobile,
							'account_holder_name' => $account_holder_name,
							'account_no' => $account_no,
							'ifsc' => $ifsc,
							'is_app' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_money_transfer',$data); 
						$recordID = $this->db->insert_id();

						

						// save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Open Payout Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
			            $this->User->generateLog($log_msg);

			            $api_url = INSTANTPAY_PAYOUT_API_URL;

			            log_message('debug', 'Open Payout  API Account ID - '.$account_id.' Open Payout Api - Call '.$api_url);	

			            $request = array(
		                
		                'payer' => array(
    		                'bankId' => '0',
    		                'bankProfileId' => 0,
    		                'accountNumber' => $accountData['instant_account_no'],
    		                ),
    		                
	                        'payee' => array(
	                            'name' => $account_holder_name,
	                            'accountNumber' => $account_no,
	                            'bankIfsc' =>$ifsc
	                       ),
	                       'transferMode' => $txnType,
	                       'transferAmount' => $amount,
	                       'externalRef' => $transaction_id,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       'remarks'  => 'Payout',
	                       'alertEmail' => $loggedUser['email'],
	                       'purpose' =>'REIMBURSEMENT'
		                
		            );



		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                
		                'content-type: application/json'
		                
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
	       	
	        		curl_close ($curl);

	        /*$output = '{"Error":"True","Message":"Invalid member or password and invalid request ip address please whitelist your ip","Data":null}';*/

	        $responseData = json_decode($output,true);
	        
	        
	         $apiData = array(
				'account_id' => $account_id,
				'user_id' => $loggedAccountID,
				'api_response' => $output,
				'api_url' => $api_url,
				'post_data'=>json_encode($request),
				'created' => date('Y-m-d H:i:s'),
				'created_by'=>$loggedAccountID
			);
			$this->db->insert('instantpay_api_response',$apiData);
			

	        	log_message('debug', 'Open Payout  API Account ID - '.$account_id.' Open Payout Api Response - '.json_encode($output));	

        				
				if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
						{

							$rrno = $responseData['data']['txnReferenceId'];
							$this->db->where('account_id',$account_id);
							$this->db->where('user_id',$loggedAccountID);
							$this->db->where('id',$recordID);
							$this->db->update('user_money_transfer',array('api_response'=>$output,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));

							$response = array(

							 'status' => 1,
							 'message'=>'Congratulations!! transfered successfully.'	

							);
							
						}
						elseif(isset($responseData['statuscode']) && ($responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR'))
						{

							$api_msg = $responseData['status'];
    
    						$log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
    
    							$this->User->generateLog($log_msg);
    					            
		    					$this->db->where('id',$recordID);
		    					$this->db->where('account_id',$account_id);
		    					$this->db->where('user_id',$loggedAccountID);
		    					$this->db->update('user_money_transfer',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

    					            //refund amount to wallet

    					            // get wallet balance
            				        
            				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            			            $after_wallet_balance = $before_wallet_balance + $final_amount;    
            			            
            			            $wallet_data = array(
            	                        'account_id'          => $account_id,
            	                        'member_id'           => $loggedAccountID,    
            	                        'before_balance'      => $before_wallet_balance,
            	                        'amount'              => $final_amount,  
            	                        'after_balance'       => $after_wallet_balance,      
            	                        'status'              => 1,
            	                        'type'                => 1, 
            	                        'wallet_type'         => 1,      
            	                        'created'             => date('Y-m-d H:i:s'),      
            	                        'description'         => 'Open Payout #'.$transaction_id.' Amount Refund Credited.'
            	                    );
            
            	                    $this->db->insert('member_wallet',$wallet_data);
            						

							$response = array(

							 'status' => 1,
							 'message'=>'Congratulations!! transfered successfully.'	

							);
							
						}

						
						else
						{
						
				            $response = array(

							 'status' => 1,
							 'message'=>'Your transaction is under processing, status will be updated soon.'	

							);	
							
						}
					}
			  	}


		 	}

						else

						{
							$response = array(
							  'status' => 0,
							  'message' => 'Session out.Please Login Again.',
							  'is_login'=>0	
							);


						}

				}

				else
				{
					$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
				}


		  		
			}
		  	
	    }
	}
	    log_message('debug', 'Open Payout Auth API Response - '.json_encode($response));	
		echo json_encode($response);


	}


	//icici payout history

	public function getOpenPayoutTransferHistory(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$response = array();

		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Get Open Payout Transfer History API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {


		$post = $this->input->post();
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

    	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

    	// decrypt token
		$decryptToken = $this->User->generateAppToken('decrypt',$token);
		log_message('debug', 'Open Payout API Decrypt Token String - '.$decryptToken);
		$explodeToken = explode('|',$decryptToken);
		$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
		$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
		$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
		$Deviceid = isset($header_data['Deviceid']) ? $header_data['Deviceid'] : '';

		$chk_user_token = $this->User->checkUserDecryptToken($decryptToken,$tokenUserID,$tokenPwd,$Deviceid);
		log_message('debug', 'User Detail Check User Decrypt Token - '.json_encode($chk_user_token));

		if($tokenUserID && $tokenPwd && $tokenIP && $chk_user_token){

			$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
				if($chk_token_user && $tokenUserID == $user_id && $tokenIP == $user_ip_address && $chk_user_token['status'] == 1 && $chk_user_token['is_login'] == 1)
				{

					if($fromDate && $toDate){

    		$count = $this->db->get_where('user_money_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

		    $limit_start = $limit - 50; 
		                     
		    $limit_end = $limit;

			$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_money_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();

		}	
		else{

		   $count = $this->db->get_where('user_money_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->num_rows();

		   $limit_start = $limit - 50; 
		                     
		   $limit_end = $limit;	
    	  		
		   $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_money_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->result_array();

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
						
				if($list['status'] == 2 || $list['status'] == 0) {
					$data[$key]['status'] = 'Pending';
				}
				elseif($list['status'] == 3) {
					$data[$key]['status'] = 'Success';
				}
				elseif($list['status'] == 4) {
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

				else

				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}

		}

		else
		{
			$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
		}




    	

	}
		log_message('debug', 'Get Open Payout List API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
		public function openPayoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
    	$post = $this->input->post();
		log_message('debug', 'Open payoutBeneficiaryList List API Post Data - '.json_encode($post));	
		$userID = isset($post['userID']) ? $post['userID'] : 0;
    	$response = array();
    	
    	$activeService = $this->User->account_active_service($post['userID']);
		if(!in_array(6, $activeService)){
			$response = array(
				'status' => 0,
				'message' => 'Sorry!! this service is not active for you.'
			);
		}
		else{

	    	// check user valid or not
			$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
			if($chk_user)
			{
				$benificaryList = $this->db->select('tbl_user_benificary.*,tbl_instantpay_aeps_bank_list.bank_name')->join('tbl_instantpay_aeps_bank_list','tbl_instantpay_aeps_bank_list.id = tbl_user_benificary.bankID')->get_where('tbl_user_benificary',array('tbl_user_benificary.account_id'=>$account_id,'tbl_user_benificary.user_id'=>$userID))->result_array();
                //$benificaryList = $this->db->select('tbl_instantpay_upi_open_payout_user_benificary.*,tbl_instantpay_aeps_bank_list.bank_name')->join('tbl_instantpay_aeps_bank_list','tbl_instantpay_aeps_bank_list.id = tbl_instantpay_upi_open_payout_user_benificary.bankID')->get_where('tbl_instantpay_upi_open_payout_user_benificary',array('tbl_instantpay_upi_open_payout_user_benificary.account_id'=>$account_id,'tbl_instantpay_upi_open_payout_user_benificary.user_id'=>$userID))->result_array();
				$data = array();
				if($benificaryList)
				{
					foreach ($benificaryList as $key => $list) {
						
						$data[$key]['beneID'] = $list['id'];
						$data[$key]['account_holder_name'] = $list['account_holder_name'];
						$data[$key]['account_no'] = $list['account_no'];
						$data[$key]['bank_name'] = $list['bank_name'];
						$data[$key]['ifsc'] = $list['ifsc'];
						$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
						
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
		log_message('debug', 'Open Payout Beneficiary List API Response - '.json_encode($response));	
		echo json_encode($response);
    }



    public function upiOpenPayoutBeneficiaryAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'openPayoutBeneficiaryAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please all details.'
			);
		}
		else
		{	
			$userID = $post['userID'];

			$chk_beneficiary = $this->db->get_where('instantpay_upi_open_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$userID))->row_array();
            
            	$chk_upi_beneficiary = $this->db->get_where('instantpay_upi_open_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$userID))->num_rows();
            	
		  	$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(6, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! this service is not active for you.'
				);
			}
			
			else{

				$bene_data = array(
	        	 'account_id' => $account_id,	
	        	 'user_id' => $post['userID'],
	        	 'account_holder_name' => $post['account_holder_name'],
	        	 
	        	 'account_no' => $post['account_number'],
	        	 
	        	 'encode_ban_id' => do_hash($post['account_number']),	
	        	 'status' => 1,
	        	 'created' => date('Y-m-d H:i:s')

	        	);
	        	
	        	$this->db->insert('instantpay_upi_open_payout_user_benificary',$bene_data);

	        	$message = 'Congratulations!! beneficiary added successfully.';

		       

	        	$response = array(

	        	 'status'  => 1,
	        	 'message' => 'Congratulations!! beneficiary added successfully.'	

	        	);

	        }
	    }
	    log_message('debug', 'ICICI Add Beneficiary Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
	
	public function upiOpenPayoutAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'Upi Open Payout Auth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userID', 'userID', 'required|xss_clean');
		$this->form_validation->set_rules('beneID', 'beneID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|callback_maximumCheck|numeric');
		$this->form_validation->set_rules('txnType', 'Transaction Type', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter Required Fields.'
			);
		}
		else
		{	
			$activeService = $this->User->account_active_service($post['userID']);
			if(!in_array(6, $activeService)){
				$response = array(
					'status' => 0,
					'message' => 'Sorry!! Payout not active.'
				);
			}
		  	else{

		  		$loggedAccountID = $post['userID'];

		  		$loggedUser = $this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

		  		$chk_beneficiary = $this->db->get_where('instantpay_upi_open_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'id'=>$post['beneID']))->row_array();

				if(!$chk_beneficiary){

					$response = array(

					  'status' => 0,
					  'message'=>'Sorry!! beneficiary not valid.'	

					);
				}	
				else{
				
					$memberID = $loggedUser['user_code'];
					$mobile = $loggedUser['mobile'];
					$account_holder_name = $chk_beneficiary['account_holder_name'];
					$account_no = $chk_beneficiary['account_no'];					
					$amount = $post['amount'];
					$txnType = $post['txnType'];
					$transaction_id = time().rand(1111,9999);
					$receipt_id = rand(111111,999999);

					
					$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
		            
		            $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		            // save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Upi Open Payout Api Wallet Balance - '.$wallet_balance.'.]'.PHP_EOL;
			        $this->User->generateLog($log_msg);

			        log_message('debug', 'Open UPI Payout  API Account ID - '.$account_id.' Upi Payout Api Wallet Balance - '.$wallet_balance);	


		            // get dmr surcharge
		            $surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$loggedAccountID);
		            // save system log
		            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - UPI Payout Api - Payout Surcharge Amount - '.$surcharge_amount.']'.PHP_EOL;
		            $this->User->generateLog($log_msg);

		            log_message('debug', 'Upi Payout  API Account ID - '.$account_id.' Upi Payout Api - Payout Surcharge Amount - '.$surcharge_amount);	


		            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            		$final_deduct_wallet_balance = $amount + $surcharge_amount + $min_wallet_balance;

		            $final_amount = $amount + $surcharge_amount;
		            $before_balance = $wallet_balance;

		            if($before_balance < $final_deduct_wallet_balance){
		                // save system log
		                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Upi Open Payout Api - Insufficient Wallet Error]'.PHP_EOL;
		                $this->User->generateLog($log_msg);

		                log_message('debug', 'Upi Open Payout  API Account ID - '.$account_id.' Upi Payout Api - Insufficient Wallet Error');	


		                $response = array(

						  'status' => 0,
						  'message'=>'Sorry!! insufficient balance in your wallet.'	

						);
		            }
		            else{

		            	
		            	$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

		            
			            $after_wallet_balance = $before_balance - $final_amount;    

			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $loggedAccountID,    
			                'before_balance'      => $before_balance,
			                'amount'              => $final_amount,  
			                'after_balance'       => $after_wallet_balance,      
			                'status'              => 1,
			                'type'                => 2,   
			                'wallet_type'		  => 1,   
			                'created'             => date('Y-m-d H:i:s'),      
			                'description'         => 'UPI OPEN PAYOUT #'.$transaction_id.' Amount Deducted.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			            // save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Upi Open Payout API - Member Wallet Deduction - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
			            $this->User->generateLog($log_msg);

			            log_message('debug', 'UPI Open Payout  API Account ID - '.$account_id.' Upi Open Payout Api - Member Wallet Deduction - Updated Balance - - '.$after_wallet_balance);	


						$data = array(
							'account_id' => $account_id,
							'user_id' => $loggedAccountID,
							'transfer_amount' => $amount,
							'transfer_charge_amount' => $surcharge_amount,
							'total_wallet_charge' => $final_amount,
							'after_wallet_balance' => $after_wallet_balance,
							'txnType' => $txnType,
							'transaction_id' => $transaction_id,
							'encode_transaction_id' => do_hash($transaction_id),
							'status' => 2,
							'wallet_type' => 1,
							'invoice_no' => $receipt_id,
							'memberID' => $memberID,
							'mobile' => $mobile,
							'account_holder_name' => $account_holder_name,
							'account_no' => $account_no,							
							'is_app' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_money_transfer',$data); 
						$recordID = $this->db->insert_id();

						
						// save system log
			            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - ICICI Payout Api - Final Response - '.json_encode($responseData).']'.PHP_EOL;
			            $this->User->generateLog($log_msg);

			            $api_url = INSTANTPAY_PAYOUT_API_URL;

			            log_message('debug', 'Upi Payout  API Account ID - '.$account_id.' Upi Payout Api - Call '.$api_url);	

			            $request = array(
		                
		                'payer' => array(
    		                'bankId' => '0',
    		                'bankProfileId' => 0,
    		                'accountNumber' => $accountData['instant_account_no'],
    		                ),
    		                
	                        'payee' => array(
	                            'name' => $account_holder_name,
	                            'accountNumber' => $account_no,
	                            'bankIfsc' =>$ifsc
	                       ),
	                       'transferMode' => $txnType,
	                       'transferAmount' => $amount,
	                       'externalRef' => $transaction_id,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       'remarks'  => 'Payout',
	                       'alertEmail' => $loggedUser['email'],
	                       'purpose' =>'REIMBURSEMENT'
		                
		            );



		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                
		                'content-type: application/json'
		                
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
	       	
	        		curl_close ($curl);

	        /*$output = '{"Error":"True","Message":"Invalid member or password and invalid request ip address please whitelist your ip","Data":null}';*/

	        $responseData = json_decode($output,true);
	        
	        
	         $apiData = array(
				'account_id' => $account_id,
				'user_id' => $loggedAccountID,
				'api_response' => $output,
				'api_url' => $api_url,
				'post_data'=>json_encode($request),
				'created' => date('Y-m-d H:i:s'),
				'created_by'=>$loggedAccountID
			);
			$this->db->insert('instantpay_api_response',$apiData);
			

	        	log_message('debug', 'Upi Payout  API Account ID - '.$account_id.' Upi Payout Api Response - '.json_encode($output));	

        				
				if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
						{

							$rrno = $responseData['data']['txnReferenceId'];
							$this->db->where('account_id',$account_id);
							$this->db->where('user_id',$loggedAccountID);
							$this->db->where('id',$recordID);
							$this->db->update('user_money_transfer',array('api_response'=>$output,'rrn'=>$rrno,'status'=>3,'updated'=>date('Y-m-d H:i:s')));

							$response = array(

							 'status' => 1,
							 'message'=>'Congratulations!! transfered successfully.'	

							);
							
						}

						elseif(isset($responseData['statuscode']) && ($responseData['statuscode'] == 'SPD' || $responseData['statuscode'] == 'IAN' || $responseData['statuscode'] == 'ISE' || $responseData['statuscode'] == 'SNA' || $responseData['statuscode'] == 'IAB' || $responseData['statuscode'] == 'ERR'))
						{

							$api_msg = $responseData['status'];
    
    						$log_msg = '['.date('d-m-Y H:i:s').' - UPI Payout Transfer API - Payout Transaction Failed.]'.PHP_EOL;
    
				    			$this->User->generateLog($log_msg);
				    					            
				    					$this->db->where('id',$recordID);
				    					$this->db->where('account_id',$account_id);
				    					$this->db->where('user_id',$loggedAccountID);
				    					$this->db->update('user_money_transfer',array('api_response'=>$output,'status'=>4,'updated'=>date('Y-m-d H:i:s')));

    					            //refund amount to wallet

    					            // get wallet balance
            				        
            				        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            			            $after_wallet_balance = $before_wallet_balance + $final_amount;    
            			            
            			            $wallet_data = array(
            	                        'account_id'          => $account_id,
            	                        'member_id'           => $loggedAccountID,    
            	                        'before_balance'      => $before_wallet_balance,
            	                        'amount'              => $final_amount,  
            	                        'after_balance'       => $after_wallet_balance,      
            	                        'status'              => 1,
            	                        'type'                => 1, 
            	                        'wallet_type'         => 1,      
            	                        'created'             => date('Y-m-d H:i:s'),      
            	                        'description'         => 'UPI Payout Transfer #'.$transaction_id.' Amount Refund Credited.'
            	                    );
            
            	                    $this->db->insert('member_wallet',$wallet_data);
            							
							$response = array(

							 'status' => 0,
							 'message'=>'Sorry ! Transcation Failed Due to'.$api_msg	

							);
							
						}
						
						else
						{
							
				            $response = array(

							 'status' => 1,
							 'message'=>'Your transaction is under processing, status will be updated soon.'	

							);	
							
						}
					}

					
			  	}
			}
		  	
	    }
	    log_message('debug', 'UPI Open Payout Auth API Response - '.json_encode($response));	
		echo json_encode($response);




	}
	
	
	public function upiOpenPayoutBeneficiaryList()
    {
    	$account_id = $this->User->get_domain_account();
    	$post = $this->input->post();
		log_message('debug', 'UPI Open payoutBeneficiaryList List API Post Data - '.json_encode($post));	
		$userID = isset($post['userID']) ? $post['userID'] : 0;
    	$response = array();
    	
    	$activeService = $this->User->account_active_service($post['userID']);
		if(!in_array(6, $activeService)){
			$response = array(
				'status' => 0,
				'message' => 'Sorry!! this service is not active for you.'
			);
		}
		else{

	    	// check user valid or not
			$chk_user = $this->db->get_where('users',array('id'=>$userID))->num_rows();
			if($chk_user)
			{

				$benificaryList = $this->db->get_where('tbl_instantpay_upi_open_payout_user_benificary',array('tbl_instantpay_upi_open_payout_user_benificary.account_id'=>$account_id,'tbl_instantpay_upi_open_payout_user_benificary.user_id'=>$userID))->result_array();

				$data = array();
				if($benificaryList)
				{
					foreach ($benificaryList as $key => $list) {
						
						$data[$key]['beneID'] = $list['id'];
						$data[$key]['account_holder_name'] = $list['account_holder_name'];
						$data[$key]['account_no'] = $list['account_no'];
						$data[$key]['date'] = date('d-m-Y',strtotime($list['created']));
						
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
		log_message('debug', 'Upi Open Payout Beneficiary List API Response - '.json_encode($response));	
		echo json_encode($response);
    }
				    
		
		
	public function getUpiOpenPayoutTransferHistory(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

    	$page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

    	if($fromDate && $toDate){

    		$count = $this->db->get_where('user_money_transfer',array('account_id'=>$account_id,'txnType'=>'UPI','user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->num_rows();

		    $limit_start = $limit - 50; 
		                     
		    $limit_end = $limit;

			$userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_money_transfer',array('account_id'=>$account_id,'txnType'=>'UPI','user_id'=>$user_id,"DATE(created) >=" => $fromDate,"DATE(created) <=" => $toDate))->result_array();
			
		
			

		}	
		else{

		   $count = $this->db->get_where('user_money_transfer',array('account_id'=>$account_id,'user_id'=>$user_id))->num_rows();

		   $limit_start = $limit - 50; 
		                     
		   $limit_end = $limit;	
    	  		
		   $userList = $this->db->order_by('created','desc')->limit($limit_end,$limit_start)->get_where('user_money_transfer',array('account_id'=>$account_id,'user_id'=>$user_id,'txnType'=>'UPI'))->result_array();

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
				//$data[$key]['ifsc'] = $list['ifsc'];
				$data[$key]['transfer_amount'] = $list['transfer_amount'].' /-';
				$data[$key]['transaction_id'] = $list['transaction_id'];
				$data[$key]['rrn'] = $list['rrn'];
						
				if($list['status'] == 2 || $list['status'] == 0) {
					$data[$key]['status'] = 'Pending';
				}
				elseif($list['status'] == 3) {
					$data[$key]['status'] = 'Success';
				}
				elseif($list['status'] == 4) {
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
		log_message('debug', 'Upi Open  Payout Transfer List API Response - '.json_encode($response));	
		echo json_encode($response);

	}	
    
    
    
    
    
    //morning pay nsdl
    
    public function morningpayNsdl()
	{
		
		$nsdl_uat_url = 'https://digipaydashboarduat.religaredigital.in/authenticate';
		$nsdl_token = 'd7ef9658-471d-4b68-a81d-c347099980db';

        $header = [
          'Content-type: application/json'      
          
        ];
        

		$requestData = array(
          'Token' => $nsdl_token,
          'RetailerID' =>'MPCNR703985',
          'LogoUrl' => 'https://www.morningpay.co.in/media/account/819113830.png',
          'Copyright' => 'Morningpay digital Private Limited',
          'FirmName' =>'Morningpay',
          'ServiceId' =>'154'
        );

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,        
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_URL => $nsdl_uat_url
        ));
        
        // Get response
        $output = curl_exec($curl);
        
        curl_close($curl);

        echo "<pre>";
        print_r(json_decode($output, true));
        die();

	}
        
        
          public function nsdlPanAuth()
	{
		

		$account_id = $this->User->get_domain_account();
		 $accountData = $this->User->get_account_data($account_id);
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

			$activeService = $this->User->account_active_service($memberID);
            if(!in_array(22, $activeService)){
               $response = array(
						'status' => 0,
						'message' => 'Sorry ! NSDL PAN Service Not Active.'
					);
            }


             $member_package_id = $this->User->getMemberPackageID($memberID);


              $get_com_data = $this->db->get_where('tbl_nsdl_pancard_charge',array('account_id'=>$account_id,'package_id'=>$member_package_id))->row_array();
             
          
            $charge = isset($get_com_data['surcharge']) ? $get_com_data['surcharge'] : 0 ;

            $user_before_balance = $this->User->getMemberWalletBalanceSP($memberID);;
            if($user_before_balance < $charge){
                
                $response = array(
						'status' => 0,
						'message' => 'Sorry ! Insufficient Wallet Balance.'
					);
                   

            }


            else


            {
            	$transaction_id = time().rand(1111,9999);
            
         $pan_redirect_url = 'https://www.'.$accountData['domain_url'].'/retailer/nsdl/index';
        
        $title = 0;
        if($post['title'] == 'Mr/Shri')
        {
            $title = 1;
        }
        else
        {
            $title = 2;
        }
        $key = $accountData['paysprint_aeps_key'];
        $iv = $accountData['paysprint_aeps_iv'];;
        $datapost =array();

                $datapost['refid'] = $transaction_id;
                $datapost['title'] = $title;
                $datapost['firstname'] = $post['first_name'];
                $datapost['lastname'] = $post['last_name'];
                $datapost['middlename'] = $post['middle_name'];
                $datapost['mode'] = $post['mode'];
                $datapost['gender'] = $post['gender'];
                $datapost['email'] =$post['email'];               
                $datapost['redirect_url'] = 'https://www.purveyindia.com/retailer/nsdl/index/';

        $cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
        $body=       base64_encode($cipher);
        $reqid = time().rand(1111,9999);

        log_message('debug', 'NSDL Api RequestID - '.$reqid);     

        $jwt_payload = array(
            'timestamp'=>time(),
            'partnerId'=>$accountData['paysprint_partner_id'],
            'reqid'=>$reqid
        );

        log_message('debug', 'NSDL Auth Api jwt payload - '.json_encode($jwt_payload));

        $secret = $accountData['paysprint_secret_key'];

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
            'account_id' =>$account_id,
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

            

            $before_balance = $this->User->getMemberWalletBalanceSP($memberID);

                    
                    $after_balance = $before_balance - $charge;    

                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $memberID,    
                        'before_balance'      => $before_balance,
                        'amount'              => $charge,  
                        'after_balance'       => $after_balance,      
                        'status'              => 1,
                        'type'                => 2,      
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'Nsdl Pan Card #'.$transaction_id.' Amount Debited.'
                    );

                    $this->db->insert('member_wallet',$wallet_data);

                    $pan_data = array(

            'account_id' =>$account_id,
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
					'message' => 'Something Went Wrong .',
					
						);

            }
		}

			
		  	
	    }

	     log_message('debug', 'Nsdl Auth API Response - '.json_encode($response));	
		echo json_encode($response);


	}
	
	
	public function mainWalletTransferAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);

		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Move Wallet Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Move Wallet API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('member_id', 'Member', 'required|xss_clean');
	        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
	        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please Enter Required field.'
				);
			}
			else
			{
			  	$loggedAccountID = $post['userID'];
			  	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Fund Transfer Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				if($tokenUserID && $tokenPwd && $tokenIP)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address)
					{
					  	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
					  	$member_code = $chk_wallet_balance['user_code'];
					  	log_message('debug', 'Move Wallet Auth API Member Code - '.$member_code.' Wallet Balance - '.$chk_wallet_balance['wallet_balance']);

					  //	$memberID = $post['member_id'];
						$amount = $post['amount'];
						
			            $final_amount = $amount;
			            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
			            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
						$final_deduct_wallet_balance = $min_wallet_balance + $final_amount;

			            if($before_balance < $final_amount){
			                log_message('debug', 'Move Wallet Auth API Member Code - '.$member_code.' Insufficient wallet error.');
			                $response = array(
								'status' => 0,
								'message' => 'Sorry ! Insufficient balance in account.'
							);
			            }
			            elseif($before_balance < $final_deduct_wallet_balance){
			                log_message('debug', 'Move Wallet Auth API Member Code - '.$member_code.' Minimum wallet error.');
			                $response = array(
								'status' => 0,
								'message' => 'Sorry ! You have to maintain minimum balance in account.'
							);
			            }
			            elseif($post['amount'] < 0){
			                log_message('debug', 'Move Wallet Valid Amount Error - '.$member_code.' Insufficient wallet error.');
			                $response = array(
								'status' => 0,
								'message' => 'Sorry ! Enter Valid Amount.'
							);
			            }
			            else
			            { 
                              
                              $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                                
			            	 $after_balance = $before_balance- $post['amount']; 

									$wallet_data = array(
						            'account_id'          => $account_id,
						            'member_id'           => $loggedAccountID,    
						            'before_balance'      => $before_balance,
						            'amount'              => $post['amount'],  
						            'after_balance'       => $after_balance,      
						            'status'              => 1,
						            'type'                => 2,      
						            'created'             => date('Y-m-d H:i:s'),      
						            'credited_by'         => $loggedAccountID,
						            'description'         => 'Move Wallet '.$post['description']            
						            );

					            $this->db->insert('member_wallet',$wallet_data);

			            //move to disstributor 

			             $distributor_before_balance = $this->db->get_where('users',array('user_code'=>$post['member_id'],'account_id'=>$account_id))->row_array();			             

			             $upline_id = $distributor_before_balance['id'];
			             $disributor_wallet_balance = $this->User->getMemberWalletBalanceSP($upline_id);

			              $distributor_after_balance = $disributor_wallet_balance + $post['amount'];

			             $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $upline_id,    
			            'before_balance'      => $disributor_wallet_balance,
			            'amount'              => $post['amount'],  
			            'after_balance'       => $distributor_after_balance,      
			            'status'              => 1,
			            'type'                => 1,      
			            'created'             => date('Y-m-d H:i:s'),      
			            'credited_by'         => $loggedAccountID,
			            'description'         => 'Recevied Balance from '.$member_code.' '.$post['description']           
			            );

			            $this->db->insert('member_wallet',$wallet_data);

						}

						$response = array(
						  'status' => 1,
						  'message' => 'Wallet Balance Transfer Successfully.',
						  	
						);

					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
			
		    }
		}
	    log_message('debug', 'Move Wallet Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}


		public function creditFundAuth(){
		
		$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
		$accountData = $this->User->get_account_data($account_id);

		$response = array();
		// get header data
		$token = '';
        $header_data = apache_request_headers();
        if($header_data && isset($header_data['Token']))
		{
			$token = $header_data['Token'];
        }
        log_message('debug', 'Credit fund Auth API Header - '.json_encode($header_data));	

        if($token == '')
        {
        	$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login' => 0
			);
        }
        else
        {
			$post = $this->input->post();
			log_message('debug', 'Credit Fund API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
			$this->load->library('form_validation');
			$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
			$this->form_validation->set_rules('member_id', 'Member', 'required|xss_clean');
	        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
	        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
			if ($this->form_validation->run() == FALSE)
			{
				$response = array(
					'status' => 0,
					'message' => 'Please Enter Required field.'
				);
			}
			else
			{
			  	$loggedAccountID = $post['userID'];
			  	// decrypt token
				$decryptToken = $this->User->generateAppToken('decrypt',$token);
				log_message('debug', 'Fund Transfer Auth API Decrypt Token String - '.$decryptToken);
				$explodeToken = explode('|',$decryptToken);
				$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
				$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
				$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
				if($tokenUserID && $tokenPwd && $tokenIP)
				{
					$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();
					if($chk_token_user && $tokenUserID == $loggedAccountID && $tokenIP == $user_ip_address)
					{
					  	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
					  	$member_code = $chk_wallet_balance['user_code'];
					  	log_message('debug', 'Credit fund Auth API Member Code - '.$member_code.' Wallet Balance - '.$chk_wallet_balance['wallet_balance']);

					  	$memberID = $post['member_id'];
						$amount = $post['amount'];
						
			            $final_amount = $amount;
			            $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
			            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
						$final_deduct_wallet_balance = $min_wallet_balance + $final_amount;

			            if($before_balance < $final_amount){
			                log_message('debug', 'Credit Fund Auth API Member Code - '.$member_code.' Insufficient wallet error.');
			                $response = array(
								'status' => 0,
								'message' => 'Sorry ! Insufficient balance in account.'
							);
			            }
			            elseif($before_balance < $final_deduct_wallet_balance){
			                log_message('debug', 'Credit Fund Auth API Member Code - '.$member_code.' Minimum wallet error.');
			                $response = array(
								'status' => 0,
								'message' => 'Sorry ! You have to maintain minimum balance in account.'
							);
			            }
			            elseif($post['amount'] < 0){
			                log_message('debug', 'Credit  Fund Valid Amount Error - '.$member_code.' Insufficient wallet error.');
			                $response = array(
								'status' => 0,
								'message' => 'Sorry ! Enter Valid Amount.'
							);
			            }
			            else
			            { 

			            	$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
			            	 $after_balance = $before_balance - $post['amount']; 

									$wallet_data = array(
						            'account_id'          => $account_id,
						            'member_id'           => $loggedAccountID,    
						            'before_balance'      => $before_balance,
						            'amount'              => $post['amount'],  
						            'after_balance'       => $after_balance,      
						            'status'              => 1,
						            'type'                => 2,      
						            'created'             => date('Y-m-d H:i:s'),      
						            'credited_by'         => $loggedAccountID,
						            'description'         => 'Move Wallet '.$post['description']            
						            );

					            $this->db->insert('member_wallet',$wallet_data);

			            //move to disstributor 

			             $distributor_before_balance = $this->db->get_where('users',array('user_code'=>$post['member'],'account_id'=>$account_id))->row_array();
			             $upline_id = $distributor_before_balance['id'];
			             $distributor_wallet_balance = $this->User->getMemberWalletBalanceSP($upline_id);
			              $distributor_after_balance = $distributor_wallet_balance + $post['amount'];

			            $member_code = $before_balance['user_code'];
			            $member_name = $before_balance['name'];

			             $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $upline_id,    
			            'before_balance'      => $distributor_before_balance['wallet_balance'],
			            'amount'              => $post['amount'],  
			            'after_balance'       => $distributor_after_balance,      
			            'status'              => 1,
			            'type'                => 1,      
			            'created'             => date('Y-m-d H:i:s'),      
			            'credited_by'         => $loggedAccountID,
			            'description'         => 'Recevied Balance from '.$member_code.' '.$post['description']           
			            );

			            $this->db->insert('member_wallet',$wallet_data);
			            
			          
							
						}

						$response = array(
						  'status' => 1,
						  'message' => 'Wallet Balance Transfer Successfully.',
						  	
						);

					}
					else
					{
						$response = array(
						  'status' => 0,
						  'message' => 'Session out.Please Login Again.',
						  'is_login'=>0	
						);
					}
				}
				else
				{
					$response = array(
					  'status' => 0,
					  'message' => 'Session out.Please Login Again.',
					  'is_login'=>0	
					);
				}
				
			
		    }
		}
	    log_message('debug', 'Credit Fund Auth API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
	
	public function memberIcici2FaLogin()				
	{   
		  
		    $account_id = $this->User->get_domain_account();
	  	    $accountData = $this->User->get_account_data($account_id);
	  	    $user_ip_address = $this->User->get_user_ip();

	

    
		$request = $_REQUEST['user_data'];
		$post =  json_decode($request,true);
        
        
		log_message('debug', 'ICICI 2FA AEPS api Auth API Post Data - '.json_encode($post));  

		$memberID = $post['userID'];
		$loggedUser = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
		
		$get_outlet_id = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
	 	//$outlet_id = $get_outlet_id['instantpay_outlet_id'];
	 	
	 	
	
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
					if(in_array(19, $activeService)){
									$is_apes_active = 1;
								}


								if(!$is_apes_active){

									$response = array(
										'status' => 0,
										'message' => 'Sorry!! AEPS not active.'
									);

								}
								else{

									$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($memberID);
									$response = array();
									if($user_instantpay_aeps_status)
									{
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
									'status' => 1,
									'icici_2fa_login_status'=>1,
									'msg' => $responseData['status']
								);
		        }
		        else
		        {
		        	$response = array(
						'status' => 0,
						'icici_2fa_login_status'=>0,
						'msg' => $responseData['status']
					);
		        }


										}
										else
										{
										    $response = array(
                    						'status' => 0,
                    						'msg' => 'Something Went wrong !'
                    					);
                    										    
										}

										
						}
						
						else
						
						{
						                $response = array(
                    						'status' => 0,
                    						'msg' => 'Something Went wrong !'
                    					);
						    
						}

					}

				}
       


						log_message('debug', 'ICICI AEPS api Auth API Response - '.json_encode($response));
						
						echo json_encode($response);
					}
					
					
					
					
					
						public function memberRegistration()
            	{
            		$account_id = $this->User->get_domain_account();
            		$accountData = $this->User->get_account_data($account_id);
            	
            		$response = array();

		    
		    $request = $_REQUEST['user_data'];
		    
	    	$post =  json_decode($request,true);
            
            log_message('debug', '2 Fa AEPS api Auth API Post Data - '.json_encode($post));	
            
			if($post)
			{
			    
			    $memberID = $post['userID'];
		        $loggedUser = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
		    
				//$encodeFPTxnId = $post['encodeFPTxnId'];
				$biometricData = $post['BiometricData'];
				$iin = '';
				$requestTime = date('Y-m-d H:i:s');
				$txnID = 'FIAK'.time();

				$memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        		$member_code = $memberData['user_code'];

				// check already kyc approved or not
				$get_kyc_data =$this->db->order_by('id','DESC')->get_where('new_aeps_member_kyc',array('member_id'=>$memberID,'account_id'=>$account_id,'clear_step'=>2))->row_array();
			
				$aadharNumber = isset($get_kyc_data['aadhar_no']) ? $get_kyc_data['aadhar_no'] : '';
				$mobile = isset($get_kyc_data['mobile']) ? $get_kyc_data['mobile'] : '';
				
				// echo "<pre>";
				// print_r($get_kyc_data);die;
			
				$api_url = PAYSPRINT_2FA_API_URL;
				$accessmodetype = 'APP';
								
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
				$datapost['data'] = $biometricData;				
				$datapost['timestamp'] = date('Y-m-d H:i:s');				
				$datapost['submerchantid'] = $member_code;

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
                                
                                log_message('debug', '2FA  AEPS api response - '.json_encode($output));
                                
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
				            		'merchant_code'=>$member_code,
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
						'status' => 1,
						'message' => $finalResponse['message']
					);
		        }
		        else
		        {
		        	$response = array(
						'status' => 0,
						'message' => $finalResponse['message']
					);
		        }
			}
			else
			{
				$response = array(
					'status' => 0,
					'message' => 'Somethis Wrong ! Please Try Again Later.'
				);
			}
		//}
        
		echo json_encode($response);
	}
	
	
	
	
		public function memberLogin()
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		    $request = $_REQUEST['user_data'];
	    	$post =  json_decode($request,true);
	    	$memberID = $post['userID'];
		$activeService = $this->User->account_active_service($memberID);
		$response = array();
		if(!in_array(17, $activeService)){
			$response = array(
				'status' => 0,
				'message' => 'Sorry ! AEPS Service Not Active.'
			);
		}
		else
		{
			$memberID = $post['userID'];
		    $loggedUser = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
		
			$request = $_REQUEST['user_data'];
	    	$post =  json_decode($request,true);
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
				$accessmodetype = 'APP';
								
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
						'status' => 1,
						'message' => $finalResponse['message']
					);
		        }
		        else
		        {
		        	$response = array(
						'status' => 0,
						'message' => $finalResponse['message']
					);
		        }
			}
			else
			{
				$response = array(
					'status' => 0,
					'message' => 'Somethis Wrong ! Please Try Again Later.'
				);
			}
		}

		echo json_encode($response);
	}
	
	
	public function upiAddMoney(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'upiAddMoney API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	
				$loggedAccountID = $post['userID'];
				
				$responseData = $this->Api_model->upiAddMoney($account_id,$loggedAccountID,$post);

				if($responseData['status'] == 1){

					$record_id = $responseData['record_id'];

					$qr_code = $responseData['qr'];

					$response = array(
						'status' => 1,
						'message'=> 'Success',
						'qr_code' => $qr_code,
						'is_api_error' => 0
					);
				}
				else
				{	

					$response = array(
					  'status' => 0,
					  'message'=>$responseData['message']	
					);
					
				}
			
	    }
	    log_message('debug', 'upi Add Money  API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
	
	
	public function upiMoneyRequestAuth(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'upiMoneyRequestAuth API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('userID', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('vpa_id', 'VPA ID', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
       
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  
				$loggedAccountID = $post['userID'];
				
				
				$user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
				
				if($user_before_balance < $post['amount']){
		  		$response = array(
					'status' => 0,
					'message' => 'Sorry ! Insufficient balance in your account.'
				);
		  	    }
		  	    
		  	    else
		  	    
		  	    {
		  	        
		  	        //check vpa id exist or not
				 //Check VPA API
		$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: D8cI6vS0yQ0cQ6eG5xG4rU3eE1vX7uT1sY0dE4kO3dB8xX0pC3',
		    'X-IBM-Client-ID: ccfcbdd3-8762-4329-9975-39acf5c8df50'
		];
		
		$enckey = '0eecc43f46ac1db51c40607cb355b22c';
		
		$requestStr = 'YES0000000065149|YESD04F53227F184F6C88741779F8C|7208865023@yesb|T|com.msg.app|0.0 ,0.0 |Mumbai|172.16.50.65|MOB|5200000200010004000639292929292|Android7.0|351898082074677|89914902900059967808|4e9389eadeea5b7c|02:00:00:00:00:00|02:00:00:00:00:00|||||||||NA|NA';
		
		$encryptData = $this->User->yesEncryptValue($requestStr,$enckey);
		
		
		$data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000000065149"}';
		
		
		$api_url = 'https://uatskyway.yesbank.in/app/uat/upi/CheckVirtualAddress';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        curl_close ($ch);

        $vpa_response =  $this->User->yesDecryptValue($response,$enckey);
        
        $api_data = array(
        	'account_id'=>$account_id,
        	'user_id'=>$loggedAccountID,
        	'api_url' =>$api_url,
        	'header_data'=>json_encode($header,true),
        	'request_data'=>json_encode($request,true),
        	'api_response'=>json_encode($vpa_response,true),
          'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('vpa_api_response',$api_data);
        $finalResponse = explode('|', $vpa_response);
        
        if($finalResponse[2])

        {
        	$vpa_name = $finalResponse[2];
        	$vpa_id = $finalResponse[1];

                
        	$responseData = $this->Api_model->upiMoneySendRequest($account_id,$loggedAccountID,$post,$vpa_id,$vpa_name);

				if($responseData['status'] == 1){

					$response = array(
						'status' => 1,
						//'txnid' => $responseData['merchantTranId'],
						'message'=> 'Transaction Success.',
						'is_api_error' => 0
					);
				}
				else
				{
					
					$response = array(
						'status' => 0,
						'message'=> $responseData['message'],
						'is_api_error' => 1
					);
				}



        }

        else

        {

        	$response = array(
						'status' => 0,
						'message'=> 'Vpa Not Valid.',
						'is_api_error' => 1
					);


        }
		  	        
		  	    }
				
	    }
	    log_message('debug', 'upiRequestAuth API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	public function getUpiTransferHistory(){
		
		$account_id = $this->User->get_domain_account();
		$response = array();

		$post = $this->input->post();
		$user_id = isset($post['user_id']) ? $post['user_id'] : 0;
		$fromDate = $post['fromDate'];
        $toDate = $post['toDate'];

        $page_no =  isset($post['page_no']) ? $post['page_no'] : 1;
    	$limit = $page_no * 50;

    	$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$user_id'";

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

		$userList = $this->db->query($sql)->result_array();

		$pages = ceil($count / 50);

		$data = array();
		if($userList)
		{
			foreach($userList as $key=>$list)
			{
				$data[$key]['user_name'] = $list['user_code'].' ('.$list['name'].')';
				$data[$key]['amount'] = $list['amount'].' /-';
				
				if($list['type_id'] == 1)
				{
					$data[$key]['type'] = 'UPI Request';
				}
				elseif($list['type_id'] == 2)
				{
					$data[$key]['type'] = 'Static QR';
				}
				elseif($list['type_id'] == 3)
				{
					$data[$key]['type'] = 'Dynamic QR';
				}
				else
				{
					$data[$key]['type'] = 'Not Available';
				}	


				$data[$key]['txnid'] = $list['txnid'];
				$data[$key]['bank_rrno'] = $list['bank_rrno'];
				$data[$key]['vpa_id'] = $list['vpa_id'];
				//$data[$key]['transaction_id'] = $list['transaction_id'];
				$data[$key]['description'] = $list['description'];
						
				if($list['status'] == 1) {
					$data[$key]['status'] = 'Pending';
				}
				elseif($list['status'] == 2) {
					$data[$key]['status'] = 'Success';
				}
				elseif($list['status'] == 3 || $list['status'] == 0) {
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
		log_message('debug', 'getUpiTransferHistory List API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
	public function upiTransferCheckStatus(){
		
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$response = array();
		$post = $this->input->post();
		log_message('debug', 'upiAddMoney API Account ID - '.$account_id.' Post Data - '.json_encode($post));	
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('user_id', 'User ID', 'required|xss_clean');
		$this->form_validation->set_rules('txn_id', 'Transcation ID', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response = array(
				'status' => 0,
				'message' => 'Please Enter valid details.'
			);
		}
		else
		{	
		  	
				$loggedAccountID = $post['user_id'];
				$txn_id = $post['txn_id'];
				   
				$chkData = $this->db->get_where('upi_transaction',array('account_id'=>$account_id,'txnid'=>$txn_id,'member_id'=>$loggedAccountID))->row_array();
				
				$transcation_id = isset($chkData['txnid']) ? $chkData['txnid'] : '' ;
			    
				$responseData = $this->Api_model->upiRequestCheckStatus($account_id,$loggedAccountID,$transcation_id);
				

				if($responseData['status'] == 1){
                        
                        // if($chkData['status'] == 1)
                        // {
                        //     $message = 'Transcation is Pending.';
                        // }
                        // elseif($chkData['status'] == 2)
                        // {
                        //     $message = 'Transcation Success.';
                        // }
                        // elseif($chkData['status'] == 3)
                        // {
                        //     $message = 'Transcation Failed.';
                        // }
                    
					$response = array(
						'status' => 1,
						'message'=> $responseData['message'],
						
					);
				}
				else
				{	

					$response = array(
					  'status' => 0,
					  'message'=>'Sorry No Transcation Found.'	
					);
					
				}
			
	    }
	    log_message('debug', 'Upi Transfer Check Status  API Response - '.json_encode($response));	
		echo json_encode($response);

	}
	
	
	
	
					


	

}


/* End of file login.php */
/* Location: ./application/controllers/login.php */