<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Login extends CI_Controller{

    public function __construct() {
        parent::__construct();
		$this->lang->load('front_login' , 'english');
        
    }
	
	public function index(){
		
		// get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

		$siteUrl = base_url();
		if($accountData['web_theme'] == 1) {
			$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'login'
	        );
	        $this->parser->parse('main-front/layout/column-2' , $data);

	    }
	    else if($accountData['web_theme'] == 2)
		{

	    	$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'login'
	        );
	        $this->parser->parse('front/layout/column-2' , $data);

	    }
	    else if($accountData['web_theme'] == 3)
		{

	    	$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'login'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }

	    else if($accountData['web_theme'] == 4)
		{

	    	$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'login'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }

    }

    public function auth(){

    	$account_id = $this->User->get_domain_account();
		$user_ip_address = $this->User->get_user_ip();
    	$post = $this->security->xss_clean($this->input->post());

    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth Called]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

        $response = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
        	// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth Form Validation Error.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->index();
        }
        else
        {
        	
        	// check referral_id valid or not
			$username = trim($post['username']);
			$password = do_hash($post['password']);
			
			$chk_employe = $this->db->where_in('role_id',array(7))->get_where('users',array('username'=>$username,'password'=>$password))->num_rows();
			
			if($chk_employe){

				// check user credentials
				$chk_user_auth = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'password'=>$password))->num_rows();
			}
			else{

				// check user credentials
				$chk_user_auth = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'password'=>$password,'account_id'=>$account_id))->num_rows();

			}

			if($chk_employe){

				$chk_user_mobile_auth = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'password'=>$password))->num_rows();
			}
			else{
				// check user credentials
				$chk_user_mobile_auth = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'password'=>$password,'account_id'=>$account_id))->num_rows();
			}


			if(!$chk_user_auth && !$chk_user_mobile_auth)
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth Username & Password is Wrong.]'.PHP_EOL;
		        $this->User->generateAccountActivityLog($log_msg);

				$this->Az->redirect('login', 'system_message_error', lang('LOGIN_FAILED'));
				
			}
			else{
				
				if($chk_employe){

					if($chk_user_auth)
					{
						$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,password')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'password'=>$password))->row_array();
					}
					else
					{
						$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,password')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'password'=>$password))->row_array();
					}

				}	
				else{

					if($chk_user_auth)
					{
						$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,password')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'password'=>$password,'account_id'=>$account_id))->row_array();
					}
					else
					{
						$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,password')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'password'=>$password,'account_id'=>$account_id))->row_array();
					}
				}



				if($status['is_active'] == 1){
					$accountData['is_otp_login'] = 1;
					if($accountData['is_otp_login'] == 1){

					
						$redirect_url = '';
						if($status['role_id'] == 2){

						

							  $otp_code = rand(111111,999999);

							  $encode_otp_code = do_hash($otp_code);

							  $this->User->sendOtp($status['id'],$otp_code,$post);

							  $this->Az->redirect('login/otp/'.$encode_otp_code, 'system_message_error', lang('OTP_SEND_SUCCESS'));
								
						}

						elseif($status['role_id'] == 3){

							

							  $otp_code = rand(111111,999999);

							  $encode_otp_code = do_hash($otp_code);

							  $this->User->sendOtp($status['id'],$otp_code,$post);

							  $this->Az->redirect('login/otp/'.$encode_otp_code, 'system_message_error', lang('OTP_SEND_SUCCESS'));
								
						}
						elseif($status['role_id'] == 4){

							

							  $otp_code = rand(111111,999999);

							  $encode_otp_code = do_hash($otp_code);

							  $this->User->sendOtp($status['id'],$otp_code,$post);

							  $this->Az->redirect('login/otp/'.$encode_otp_code, 'system_message_error', lang('OTP_SEND_SUCCESS'));
								
						}
						elseif($status['role_id'] == 5){

							

							  $otp_code = rand(111111,999999);

							  $encode_otp_code = do_hash($otp_code);

							  $this->User->sendOtp($status['id'],$otp_code,$post);

							  $this->Az->redirect('login/otp/'.$encode_otp_code, 'system_message_error', lang('OTP_SEND_SUCCESS'));
								
						}
						elseif($status['role_id'] == 6){
							$this->session->set_userdata(API_MEMBER_SESSION_ID,$status);
							$this->Az->redirect('portal/dashboard', 'system_message_error', '');
							
						}
						elseif($status['role_id'] == 7){

							$this->session->set_userdata(SUPERADMIN_EMPLOYE_SESSION_ID,$status);
							$this->Az->redirect('superemploye/dashboard', 'system_message_error', '');
							
						}
						elseif($status['role_id'] == 9){

							
							 $otp_code = rand(111111,999999);

							  $encode_otp_code = do_hash($otp_code);

							  $this->User->sendOtp($status['id'],$otp_code,$post);

							  $this->Az->redirect('login/otp/'.$encode_otp_code, 'system_message_error', lang('OTP_SEND_SUCCESS'));

							// $this->session->set_userdata(ADMIN_EMPLOYE_SESSION_ID,$status);
							// $this->Az->redirect('employe/dashboard', 'system_message_error', '');
							
						}
						elseif($status['role_id'] == 8){

							if($chk_otp_permission['is_user_otp'] == 1){

							  $otp_code = rand(111111,999999);

							  $encode_otp_code = do_hash($otp_code);

							 $this->User->sendOtp($status['id'],$otp_code,$post);

							  $this->Az->redirect('login/otp/'.$encode_otp_code, 'system_message_error', lang('OTP_SEND_SUCCESS'));
								
							}
							else{

								$this->session->set_userdata(USER_SESSION_ID,$status);
								$this->Az->redirect('user/dashboard', 'system_message_error', '');
							}
							
						}
						else{
							$this->Az->redirect('login', 'system_message_error', lang('LOGIN_ACCOUNT_ACTIVE_ERROR'));
						}

					}


					else

					{

					$redirect_url = '';
					if($status['role_id'] == 2){

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth Admin Role Found and Redirect to Admin Panel.]'.PHP_EOL;
				        $this->User->generateAccountActivityLog($log_msg);

				        $this->User->saveUserLoginLog($account_id,$user_ip_address,$status,$post);

						$this->session->set_userdata(ADMIN_SESSION_ID,$status);
						$this->Az->redirect('admin/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 3){

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth MD Role Found and Redirect to MD Panel.]'.PHP_EOL;
				        $this->User->generateAccountActivityLog($log_msg);

				        $this->User->saveUserLoginLog($account_id,$user_ip_address,$status,$post);

						$this->session->set_userdata(MASTER_DIST_SESSION_ID,$status);
						$this->Az->redirect('master/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 4){

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth DT Role Found and Redirect to DT Panel.]'.PHP_EOL;
				        $this->User->generateAccountActivityLog($log_msg);

				        $this->User->saveUserLoginLog($account_id,$user_ip_address,$status,$post);

						$this->session->set_userdata(DISTRIBUTOR_SESSION_ID,$status);
						$this->Az->redirect('distributor/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 5){

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth RT Role Found and Redirect to RT Panel.]'.PHP_EOL;
				        $this->User->generateAccountActivityLog($log_msg);

				        $this->User->saveUserLoginLog($account_id,$user_ip_address,$status,$post);

						$this->session->set_userdata(RETAILER_SESSION_ID,$status);
						$this->Az->redirect('retailer/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 6){

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth API Role Found and Redirect to API Panel.]'.PHP_EOL;
				        $this->User->generateAccountActivityLog($log_msg);

				        $this->User->saveUserLoginLog($account_id,$user_ip_address,$status,$post);

						$this->session->set_userdata(API_MEMBER_SESSION_ID,$status);
						$this->Az->redirect('portal/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 7){

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth Superadmin Employe Role Found and Redirect to Superadmin Employe Panel.]'.PHP_EOL;
				        $this->User->generateAccountActivityLog($log_msg);

				        $this->User->saveUserLoginLog($account_id,$user_ip_address,$status,$post);

						$this->session->set_userdata(SUPERADMIN_EMPLOYE_SESSION_ID,$status);
						$this->Az->redirect('superemploye/dashboard', 'system_message_error', '');
							
					}
					elseif($status['role_id'] == 8){

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth User Role Found and Redirect to User Panel.]'.PHP_EOL;
				        $this->User->generateAccountActivityLog($log_msg);

				        $this->User->saveUserLoginLog($account_id,$user_ip_address,$status,$post);

						$this->session->set_userdata(USER_SESSION_ID,$status);
						$this->Az->redirect('user/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 9){

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth Admin Employe Role Found and Redirect to Admin Employe Panel.]'.PHP_EOL;
				        $this->User->generateAccountActivityLog($log_msg);

				        $this->User->saveUserLoginLog($account_id,$user_ip_address,$status,$post);

						$this->session->set_userdata(ADMIN_EMPLOYE_SESSION_ID,$status);
						$this->Az->redirect('employe/dashboard', 'system_message_error', '');
							
					}
					else{

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth Role Not Found and Redirect back to login page.]'.PHP_EOL;
				        $this->User->generateAccountActivityLog($log_msg);

						$this->Az->redirect('login', 'system_message_error', lang('LOGIN_ACCOUNT_ACTIVE_ERROR'));
					}

				}
					
				}
				else
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - Login Auth Account is not activated.]'.PHP_EOL;
			        $this->User->generateAccountActivityLog($log_msg);

					$this->Az->redirect('login', 'system_message_error', lang('LOGIN_ACCOUNT_ACTIVE_ERROR'));
					
				}
				
			}
			
			
		}
		
    }


      public function otp($encoded_otp_code = ''){
		
		// get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $chk_otp = $this->db->get_where('users_otp',array('account_id'=>$account_id,'encrypt_otp_code'=>$encoded_otp_code,'status'=>0))->num_rows();

        if(!$chk_otp){

        	$this->Az->redirect('login', 'system_message_error', lang('DB_ERROR'));
        }
		    	

		$siteUrl = base_url();
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'account_id'  => $account_id,
            'encoded_otp_code' => $encoded_otp_code,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'otpAuth'
        );
        $this->parser->parse('theme-three/layout/column-2' , $data);
    }
	


	public function otpAuth(){
		
    	$post = $this->input->post();

        $response = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('otp_code', 'OTP Code', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			$this->otp($post['encoded_otp_code']);
        }
        else
        {
        	$account_id = $this->User->get_domain_account();
        	// check referral_id valid or not
			$otp_code = trim($post['otp_code']);

			$chk_otp = $this->db->get_where('users_otp',array('account_id'=>$account_id,'otp_code'=>$otp_code,'status'=>0))->row_array();


			$encrypt_otp_code = $post['encoded_otp_code'];

	        if(!$chk_otp){

	        	$this->Az->redirect('login/otp/'.$encrypt_otp_code, 'system_message_error', lang('OTP_LOGIN_FAILED'));
	        }	


	        $json_data = json_decode($chk_otp['json_post_data']);


	        $post_data = (array)$json_data;
	        
	        $username = $post_data['username'];
	        $password = do_hash($post_data['password']);

	        $this->db->where('encrypt_otp_code',$encrypt_otp_code);
	        $this->db->update('users_otp',array('status'=>1));


	        // check user credentials
			$chk_user_auth = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'password'=>$password,'account_id'=>$account_id))->num_rows();
			
			$chk_user_mobile = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'password'=>$password,'account_id'=>$account_id))->num_rows();



			if(!$chk_user_auth &&  !$chk_user_mobile)
			{

				$this->Az->redirect('login', 'system_message_error', lang('LOGIN_FAILED'));
				
			}

			else{



					if($chk_user_auth)
					{
						$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,password')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'password'=>$password))->row_array();


					}

					elseif($chk_user_mobile)
				{
					$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,password')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'password'=>$password))->row_array();

				}

				

				if($status['is_active'] == 1){
					
					$redirect_url = '';
					if($status['role_id'] == 2){
						$this->session->set_userdata(ADMIN_SESSION_ID,$status);
						$this->Az->redirect('admin/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 3){
						$this->session->set_userdata(MASTER_DIST_SESSION_ID,$status);
						$this->Az->redirect('master/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 4){
						$this->session->set_userdata(DISTRIBUTOR_SESSION_ID,$status);
						$this->Az->redirect('distributor/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 5){
						
						$this->session->set_userdata(RETAILER_SESSION_ID,$status);
						$this->Az->redirect('retailer/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 6){
						$this->session->set_userdata(API_MEMBER_SESSION_ID,$status);
						$this->Az->redirect('portal/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 8){
						$this->session->set_userdata(USER_SESSION_ID,$status);
						$this->Az->redirect('user/dashboard', 'system_message_error', '');
						
					}
					elseif($status['role_id'] == 9){
						$this->session->set_userdata(ADMIN_EMPLOYE_SESSION_ID,$status);
						$this->Az->redirect('employe/dashboard', 'system_message_error', '');
						
					}
					else{
						
						$this->Az->redirect('login', 'system_message_error', lang('LOGIN_ACCOUNT_ACTIVE_ERROR'));
					}
					
				}
				else
				{
					
					$this->Az->redirect('login', 'system_message_error', lang('LOGIN_ACCOUNT_ACTIVE_ERROR'));
					
				}
				
			}
			
			
		}
		
    }

	
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */