<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Forgot extends CI_Controller{

    public function __construct() {
        parent::__construct();
			
		$this->lang->load('front_login' , 'english');
        
    }
	
	public function index(){

		 $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
		
		    	
		$siteUrl = base_url();
		if($accountData['web_theme'] == 3)
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
	            'content_block' => 'forgot'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }
	      

	    else
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
	            'content_block' => 'forgot'
	        );
	        $this->parser->parse('front/layout/column-2' , $data);
    	}
    }

    public function auth(){

    	
		
		 $account_id = $this->User->get_domain_account();
         $accountData = $this->User->get_account_data($account_id);

    	$post = $this->security->xss_clean($this->input->post());

        $response = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username/Member ID', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			$this->index();
        }
        else
        {
			// check referral_id valid or not
			$username = trim($post['username']);
			// check user credentials
			//$chk_user_auth = $this->db->where_in('role_id',array(2,3,4,5))->get_where('users',array('mobile'=>$username))->num_rows();

			$chk_member_id = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'account_id'=>$account_id))->num_rows();

			$chk_email = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('email'=>$username,'account_id'=>$account_id))->num_rows();
			$chk_mobile = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'account_id'=>$account_id))->row_array();
				

			


			if(!$chk_member_id && !$chk_email && !$chk_mobile)
			{
				
				$this->Az->redirect('forgot', 'system_message_error', lang('LOGIN_FAILED'));
				
			}
			else{

				

				if($chk_member_id)
				{
					$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,mobile')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'account_id'=>$account_id))->row_array();
						
					
                }
				elseif($chk_email)
				{
					$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,mobile')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('email'=>$username,'account_id'=>$account_id))->row_array();
				}
				elseif($chk_mobile)
				{
					$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,mobile')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'account_id'=>$account_id))->row_array();
				}

				

				if($status['is_active'] == 1){
						
					$user_id = $status['id'];
					// check today total OTP sent
					
					if($user_id)
					{

						$otp_code = rand(111111,999999);

						$encode_opt_code = do_hash($otp_code);

						$mobile = '91'.$status['mobile'];


									$request = array(
			              
			                'OTP' => $otp_code
			            );
			            
			            $api_url = 'https://control.msg91.com/api/v5/otp?mobile='.$mobile.'&template_id=64bb9620d6fc05546a6f3463';

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
			        	 'user_id' => $user_id,
			        	 'api_url' => $api_url,
			        	 'api_response' => $output,
			        	 'created' => date('Y-m-d H:i:s'),
			        	 'created_by' => $user_id
						);
			        	
			        	$this->db->insert('sms_api_response',$smsLogData);

						$otpData = array(
							'member_id' => $user_id,
							'account_id'=>$account_id,
							'mobile'=>$mobile,
							'otp_code' => $otp_code,
							'encrypt_otp_code' => $encode_opt_code,
							'status' => 0,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('users_otp',$otpData);
						

						$this->Az->redirect('forgot/otp/'.$encode_opt_code, 'system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Otp sent to your registered mobile number. Please verify.</div>');
					}
					else
					{
						$this->Az->redirect('forgot', 'system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
					}
					
				}
				else
				{
					$this->Az->redirect('forgot', 'system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! Your account is not active.</div>');
					
				}
				
			}
			
			
		}
		
    }
	
	public function otp($encode_opt_code = ''){
		

		 $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

		// check OTP Code is valid or not
		$chk_otp = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>0,'account_id'=>$account_id))->num_rows();
		if(!$chk_otp)
		{
			$this->Az->redirect('forgot', 'system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
		}
		    	
		$siteUrl = base_url();
		if($accountData['web_theme'] == 3)
		{

	    	$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'forgot-otp'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }
	    else
	    {
			$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'forgot-otp'
	        );
	        $this->parser->parse('front/layout/column-2' , $data);
	    }
    }



    public function resendOtpAuth($encode_opt_code = ''){
		

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

		// check OTP Code is valid or not
		$get_otp_data = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>0,'account_id'=>$account_id))->row_array();

		if(!$get_otp_data)
		{
			$this->Az->redirect('forgot', 'system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
		}
			
						$otp_code = rand(111111,999999);

						$encode_opt_code = do_hash($otp_code);

						 $mobile = $get_otp_data['mobile'];
						$member_id = $get_otp_data['member_id'];

						$mobile = $get_otp_data['mobile'];

						$json_post_data = $get_otp_data['json_post_data'];


						$request = array(
			              
			                'OTP' => $otp_code
			            );
			            
			            $api_url = 'https://control.msg91.com/api/v5/otp?mobile='.$mobile.'&template_id=64bb9620d6fc05546a6f3463';

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
			        	 'created' => date('Y-m-d H:i:s'),
			        	 'created_by' => $user_id
						);
			        	
			        	$this->db->insert('sms_api_response',$smsLogData);

						$otpData = array(
							'member_id' => $member_id,
							'account_id'=>$account_id,
							'mobile'=>$mobile,
							'json_post_data'=>$json_post_data,
							'otp_code' => $otp_code,
							'encrypt_otp_code' => $encode_opt_code,
							'status' => 0,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('users_otp',$otpData);


			$this->Az->redirect('forgot/otp/'.$encode_opt_code, 'system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Otp sent to your registered mobile number. Please Verify.</div>');
		// }
		// else{

		// 	$error = $decodeResponse['message'];
		// 	$this->Az->redirect('forgot/otp/'.$encode_opt_code, 'system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$error.'</div>');

		// }	


    }

	
	public function otpauth(){


		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
		
    	$post = $this->security->xss_clean($this->input->post());
    	
    	

        $response = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('otp_code[]', 'OTP Code', 'required|xss_clean');
        $this->form_validation->set_rules('encode_opt_code', 'encode_opt_code', 'xss_clean');
        if ($this->form_validation->run() == FALSE) {
			$this->otp($post['encode_opt_code']);
        }
        else
        {
			$encode_opt_code = $post['encode_opt_code'];
			
			$otp_code = trim(implode('', $post['otp_code']));
			

			// check OTP Code is valid or not
			$get_otp_data = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>0,'account_id'=>$account_id))->row_array();
			
			
			if(!$get_otp_data)
			{
				$this->Az->redirect('forgot/otp/'.$encrypt_otp_code, 'system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
			}

			// update OTP status
			$this->db->where('account_id',$account_id);
			$this->db->where('otp_code',$otp_code);
			$this->db->where('encrypt_otp_code',$encode_opt_code);
			$this->db->update('users_otp',array('status'=>1));
			$this->Az->redirect('forgot/updatePassword/'.$encode_opt_code, 'system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations!! otp verified successfully.Update your new password.</div>');
			
			
		}
		
    }
	
	public function updatePassword($encode_opt_code = ''){


		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

		
		// check OTP Code is valid or not
		$chk_otp = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>1,'account_id'=>$account_id))->num_rows();
		if(!$chk_otp)
		{
			$this->Az->redirect('login', 'system_message_error', lang('DB_ERRROR'));
		}
		    	
		$siteUrl = base_url();
		if($accountData['web_theme'] == 3)
		{

	    	$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'update-password'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }
	    else
	    {
			$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'update-password'
	        );
	        $this->parser->parse('front/layout/column-2' , $data);
	    }
    }
	
	public function passwordAuth(){
		
    	$account_id = $this->User->get_domain_account();
		$post = $this->security->xss_clean($this->input->post());
		
        $response = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|xss_clean|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|xss_clean|matches[new_password]');
		$this->form_validation->set_rules('encode_opt_code', 'encode_opt_code', 'xss_clean');
        if ($this->form_validation->run() == FALSE) {
			$this->updatePassword($post['encode_opt_code']);
        }
        else
        {
			$encode_opt_code = $post['encode_opt_code'];
			// check OTP Code is valid or not
			$chk_otp = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>1,'account_id'=>$account_id))->num_rows();
			if(!$chk_otp)
			{
				$this->Az->redirect('login', 'system_message_error', lang('DB_ERRROR'));
			}
			
			// get user_id
			$get_user_id = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>1,'account_id'=>$account_id))->row_array();
			$user_id = isset($get_user_id['member_id']) ? $get_user_id['member_id'] : 0 ;
			
			// update user new password
			$userData = array(
				'password' => do_hash($post['new_password']),
				'decode_password' => $post['new_password'],
				'updated' => date('Y-m-d H:i:s')
			);
			$this->db->where('id',$user_id);
			$this->db->where('account_id',$account_id);
			$this->db->update('users',$userData);
			$this->Az->redirect('login', 'system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations!! password updated successfully.</div>');
		}
		
    }


    public function tPin(){

		 $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
		
		    	
		$siteUrl = base_url();
		if($accountData['web_theme'] == 3)
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
	            'content_block' => 'forgot-transcation-password'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }
	    elseif($accountData['web_theme'] == 4)
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
	            'content_block' => 'forgot-transcation-password'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }
	      

	    else
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
	            'content_block' => 'forgot-transcation-password'
	        );
	        $this->parser->parse('front/layout/column-2' , $data);
    	}
    }


     public function transcationAuth(){

    	
		
		 $account_id = $this->User->get_domain_account();
         $accountData = $this->User->get_account_data($account_id);

    	$post = $this->security->xss_clean($this->input->post());

        $response = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username/Member ID', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			$this->index();
        }
        else
        {
			// check referral_id valid or not
			$username = trim($post['username']);
			// check user credentials
			//$chk_user_auth = $this->db->where_in('role_id',array(2,3,4,5))->get_where('users',array('mobile'=>$username))->num_rows();

			$chk_member_id = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'account_id'=>$account_id))->num_rows();

			$chk_email = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('email'=>$username,'account_id'=>$account_id))->num_rows();
			$chk_mobile = $this->db->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'account_id'=>$account_id))->row_array();
				

			


			if(!$chk_member_id && !$chk_email && !$chk_mobile)
			{
				
				$this->Az->redirect('forgot/tPin', 'system_message_error', lang('LOGIN_FAILED'));
				
			}
			else{

				

				if($chk_member_id)
				{
					$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,mobile')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('username'=>$username,'account_id'=>$account_id))->row_array();
						
					
                }
				elseif($chk_email)
				{
					$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,mobile')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('email'=>$username,'account_id'=>$account_id))->row_array();
				}
				elseif($chk_mobile)
				{
					$status = $this->db->select('id,account_id,role_id,user_code,name,is_active,mobile')->where_in('role_id',array(2,3,4,5,6,7,8,9))->get_where('users',array('mobile'=>$username,'account_id'=>$account_id))->row_array();
				}

				

				if($status['is_active'] == 1){
						
					$user_id = $status['id'];
					// check today total OTP sent
					
					if($user_id)
					{

						$otp_code = rand(111111,999999);

						$encode_opt_code = do_hash($otp_code);

						$mobile = '91'.$status['mobile'];


									$request = array(
			              
			                'OTP' => $otp_code
			            );
			            
			            $api_url = 'https://control.msg91.com/api/v5/otp?mobile='.$mobile.'&template_id=64bb9620d6fc05546a6f3463';

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
			        	 'user_id' => $user_id,
			        	 'api_url' => $api_url,
			        	 'api_response' => $output,
			        	 'created' => date('Y-m-d H:i:s'),
			        	 'created_by' => $user_id
						);
			        	
			        	$this->db->insert('sms_api_response',$smsLogData);

						$otpData = array(
							'member_id' => $user_id,
							'account_id'=>$account_id,
							'mobile'=>$mobile,
							'otp_code' => $otp_code,
							'encrypt_otp_code' => $encode_opt_code,
							'status' => 0,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('users_otp',$otpData);
						

						$this->Az->redirect('forgot/transcationOtp/'.$encode_opt_code, 'system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Otp sent to your registered mobile number. Please verify.</div>');
					}
					else
					{
						$this->Az->redirect('forgot/tPin', 'system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
					}
					
				}
				else
				{
					$this->Az->redirect('forgot/tPin', 'system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! Your account is not active.</div>');
					
				}
				
			}
			
			
		}
		
    }


    public function transcationOtp($encode_opt_code = ''){
		

		 $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

		// check OTP Code is valid or not
		$chk_otp = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>0,'account_id'=>$account_id))->num_rows();
		if(!$chk_otp)
		{
			$this->Az->redirect('forgot/tPin', 'system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
		}
		    	
		$siteUrl = base_url();
		if($accountData['web_theme'] == 3)
		{

	    	$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'forgot-transcation-otp'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }
	    if($accountData['web_theme'] == 4)
		{

	    	$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'forgot-transcation-otp'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }

	    else
	    {
			$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'forgot-transcation-otp'
	        );
	        $this->parser->parse('front/layout/column-2' , $data);
	    }
    }



    public function transcationOtpAuth(){


		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
		
    	$post = $this->security->xss_clean($this->input->post());
        $response = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('otp_code[]', 'OTP Code', 'required|xss_clean');
        $this->form_validation->set_rules('encode_opt_code', 'encode_opt_code', 'xss_clean');
        if ($this->form_validation->run() == FALSE) {
			$this->otp($post['encode_opt_code']);
        }
        else
        {
			$encode_opt_code = $post['encode_opt_code'];
			$otp_code = trim(implode('', $post['otp_code']));
			
			// check OTP Code is valid or not
			$get_otp_data = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>0,'account_id'=>$account_id))->row_array();
			
			if(!$get_otp_data)
			{
				$this->Az->redirect('forgot/transcationOtp/'.$encrypt_otp_code, 'system_message_error', '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
			}

			// update OTP status
			$this->db->where('otp_code',$otp_code);
			$this->db->where('encrypt_otp_code',$encode_opt_code);
			$this->db->update('users_otp',array('status'=>1));
			$this->Az->redirect('forgot/updateTranscationPassword/'.$encode_opt_code, 'system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations!! otp verified successfully.Update your new password.</div>');
			
			
		}
		
    }


    public function updateTranscationPassword($encode_opt_code = ''){


		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

		
		// check OTP Code is valid or not
		$chk_otp = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>1,'account_id'=>$account_id))->num_rows();
		if(!$chk_otp)
		{
			$this->Az->redirect('login', 'system_message_error', lang('DB_ERRROR'));
		}
		    	
		$siteUrl = base_url();
		if($accountData['web_theme'] == 3)
		{

	    	$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'update-transcation-password'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }
	    elseif($accountData['web_theme'] == 4)
		{

	    	$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'update-transcation-password'
	        );
	        $this->parser->parse('theme-three/layout/column-2' , $data);

	    }

	    else
	    {
			$data = array(
	            'meta_title' => lang('SITE_NAME'),
	            'meta_keywords' => lang('SITE_NAME'),
	            'meta_description' => lang('SITE_NAME'),
	            'site_url' => $siteUrl,
	            'accountData' => $accountData,
	            'account_id'  => $account_id,
	            'encode_opt_code' => $encode_opt_code,
	            'system_message' => $this->Az->getSystemMessageError(),
	            'system_info' => $this->Az->getsystemMessageInfo(),
	            'system_warning' => $this->Az->getSystemMessageWarning(),
	            'content_block' => 'update-transcation-password'
	        );
	        $this->parser->parse('front/layout/column-2' , $data);
	    }
    }
	
	public function transcationPasswordAuth(){
		
    	$account_id = $this->User->get_domain_account();
		$post = $this->security->xss_clean($this->input->post());
		
        $response = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_password', 'New Transcation Password', 'required|xss_clean|min_length[4]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Transcation Password', 'required|xss_clean|matches[new_password]');
		$this->form_validation->set_rules('encode_opt_code', 'encode_opt_code', 'xss_clean');
        if ($this->form_validation->run() == FALSE) {
			$this->updatePassword($post['encode_opt_code']);
        }
        else
        {
			$encode_opt_code = $post['encode_opt_code'];
			// check OTP Code is valid or not
			$chk_otp = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>1,'account_id'=>$account_id))->num_rows();
			if(!$chk_otp)
			{
				$this->Az->redirect('login', 'system_message_error', lang('DB_ERRROR'));
			}
			
			// get user_id
			$get_user_id = $this->db->get_where('users_otp',array('encrypt_otp_code'=>$encode_opt_code,'status'=>1,'account_id'=>$account_id))->row_array();
			$user_id = isset($get_user_id['member_id']) ? $get_user_id['member_id'] : 0 ;
			
			// update user new password
			$userData = array(
				'transaction_password' => do_hash($post['new_password']),
				'decoded_transaction_password' => $post['new_password'],
				'updated' => date('Y-m-d H:i:s')
			);
			$this->db->where('id',$user_id);
			$this->db->where('account_id',$account_id);
			$this->db->update('users',$userData);
			$this->Az->redirect('login', 'system_message_error', '<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations!! Transcation password updated successfully.</div>');
		}
		
    }

	


	
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */