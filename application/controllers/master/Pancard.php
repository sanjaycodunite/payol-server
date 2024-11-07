<?php 
class Pancard extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
       	$this->load->model('master/Pancard_model');
        $this->lang->load('master/wallet', 'english');
        
    }

	
	public function index()
    {
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(9, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

        $packageList = $this->db->get_where('package',array('account_id'=>$account_id,'created_by'=>$loggedUser['id']))->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'package/packageList',
            'packageList'   => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }



    // add member
	public function activeService()
    {
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$account_id = $this->User->get_domain_account();

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(9, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$kycData = $this->db->get_where('member_pancard_kyc',array('account_id'=>$account_id,'user_id'=>$loggedUser['id']))->row_array();

		$is_active_uti = 4;
		if($kycData){

			$check_active_uti = $this->User->utiKycStatus($kycData['mobile']);

			$psaLoginId = '';
			$reason = '';
			if($check_active_uti['statuscode'] == "PEN"){
		 	  
		 	  $is_active_uti = 1;

			}
			elseif(strpos($check_active_uti['status'], "Approved") !== false){
			
			  $is_active_uti = 2;	
			  $explode = explode("Approved and PSAId is : ",$check_active_uti['status']);
		 	  $psaLoginId = $explode[1];
		 	}
			else{

				$is_active_uti = 3;
				$reason = $check_active_uti['status'];
			}
		}
		

		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'pancard/activeService',
            'kycData' => $kycData,
            'is_active_uti' => $is_active_uti,
            'psaLoginId' => $psaLoginId,
            'reason' => $reason,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    // save member
	public function pancardActiveAuth()
	{
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|numeric|min_length[10]|max_length[10]|xss_clean');

		if(!isset($_FILES['aadhar_card']['name']) || $_FILES['aadhar_card']['name'] == ''){
			$this->form_validation->set_rules('aadhar_card', 'Aadhar Card', 'required|xss_clean');
		}
		if(!isset($_FILES['pancard']['name']) || $_FILES['pancard']['name'] == ''){
			$this->form_validation->set_rules('pancard', 'Pancard', 'required|xss_clean');
		}

        
        if ($this->form_validation->run() == FALSE) {
			
			$this->activeService();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

        	$activeService = $this->User->account_active_service($loggedUser['id']);
			if(!in_array(9, $activeService)){
				$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
			}

        	// upload front document
			$aadhar_card = '';
			if(isset($_FILES['aadhar_card']['name']) && $_FILES['aadhar_card']['name']){
				$config['upload_path'] = './media/pancard_kyc_doc/';
				$config['allowed_types'] = 'jpg|png|jpeg';
				$config['max_size'] = 2048;
				$fileName = time().rand(111111,999999);
				$config['file_name'] = $fileName;
				$this->load->library('upload', $config);
				$this->upload->do_upload('aadhar_card');		
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('master/pancard/activeService', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$aadhar_card = substr($config['upload_path'] . $fileData['file_name'], 2);
				}
			}
			
			
			// upload back document
			$pancard = '';
			if(isset($_FILES['pancard']['name']) && $_FILES['pancard']['name']){
				$config02['upload_path'] = './media/pancard_kyc_doc/';
				$config02['allowed_types'] = 'jpg|png|jpeg';
				$config02['max_size'] = 2048;
				$fileName = time().rand(111111,999999);
				$config02['file_name'] = $fileName;
				$this->load->library('upload', $config02);
				$this->upload->do_upload('pancard');		
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('master/pancard/activeService', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$pancard = substr($config02['upload_path'] . $fileData['file_name'], 2);
				}
			}
        	

        	$response = $this->Pancard_model->activePancardMember($post,$aadhar_card,$pancard);
			$status = $response['status'];

			if($status == 1)
			{
				$this->Az->redirect('master/pancard/activeService', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$response['msg'].'</div>');
			}
			else
			{
				$this->Az->redirect('master/pancard/activeService', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$response['msg'].'</div>');
			}
			
		}
	
	}

	public function purchaseCouponAuth()
	{
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');

		$this->form_validation->set_rules('psa_login_id', 'PSA LoginID', 'required|xss_clean');
		$this->form_validation->set_rules('coupon', 'Coupon', 'required|numeric|xss_clean');
		
        if($this->form_validation->run() == FALSE) {
			
			$this->activeService();
		}
		else
		{	
			
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

        	$activeService = $this->User->account_active_service($loggedUser['id']);
			if(!in_array(9, $activeService)){
				$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
			}
			if($post['coupon'] < 1){
				$this->Az->redirect('master/pancard/activeService', 'system_message_error',lang('AUTHORIZE_ERROR'));
			}

			$loggedAccountID = $loggedUser['id'];
			$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
			
        	// get commission
			$get_com_data = $this->db->get_where('uti_pancard_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->row_array();
			$charge = isset($get_com_data['md_commision']) ? $get_com_data['md_commision'] : 0 ;
			
			$charge_amount = $charge * $post['coupon'];
			
        	$chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
        	
        	$min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];
            $final_deduct_wallet_balance = $charge_amount + $min_wallet_balance;  

            $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

        	if($wallet_balance < $final_deduct_wallet_balance){
				$this->Az->redirect('master/pancard/activeService', 'system_message_error',lang('WALLET_ERROR'));   
			}
			else{

				// charges cut pancard
				$transid = rand(111111,999999).time();

				$after_balance = $wallet_balance - $charge_amount;

	            $wallet_data = array(
	                'account_id'          => $account_id,
	                'member_id'           => $loggedAccountID,    
	                'before_balance'      => $wallet_balance,
	                'amount'              => $charge_amount, 
	                'after_balance'       => $after_balance,      
	                'status'              => 1,
	                'type'                => 2, 
	                'wallet_type'         => 1,     
	                'created'             => date('Y-m-d H:i:s'),      
	                'description'         => 'UTI Pancard Coupon Txn#'.$transid.' Amount Deducted.'
	            );

	            $this->db->insert('member_wallet',$wallet_data);

	            
	            //save coupon

	            $coupon_data = array(

	             'account_id'  => $account_id,
	             'user_id'     => $loggedAccountID,
	             'txnid' => $transid,
	             'psa_login_id'=> $post['psa_login_id'],
	             'quantity'    => $post['coupon'],
	             'charge_amount' => $charge,
	             'total_wallet_charge' => $charge_amount,
	             'status' => 1,
	             'created'     => date('Y-m-d H:i:s')  	
	            );

	            $this->db->insert('uti_pancard_coupon',$coupon_data);
	            $couponId = $this->db->insert_id();

	        	$response = $this->Pancard_model->purchaseCoupon($post,$transid);

				$status = $response['status'];
				$message = $response['message'];

				if($status == 1 && $message == 'Success')
				{	
					$this->db->where('id',$couponId);
					$this->db->update('uti_pancard_coupon',array('status'=>2,'coupon'=>$response['token']));
					$this->Az->redirect('master/pancard/activeService', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations!! Coupon purchased successfully.</div>');
				}
				elseif($status == 1 && $message == 'Pending')
				{	
					$this->Az->redirect('master/pancard/activeService', 'system_message_error','<div class="alert alert-warning alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Coupon Purchase is under process, status will be updated soon.</div>');
				}
				elseif($status == 0 && $message == 'Failed')
				{	
					$this->db->where('id',$couponId);
					$this->db->update('uti_pancard_coupon',array('status'=>3));
					// refund wallet amount
					$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
					$after_balance = $wallet_balance + $charge_amount;

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $loggedAccountID,    
		                'before_balance'      => $wallet_balance,
		                'amount'              => $charge_amount, 
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1, 
		                'wallet_type'         => 1,     
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UTI Pancard Coupon Txn#'.$transid.' Amount Refund Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            
					$this->Az->redirect('master/pancard/activeService', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! Coupon purchase failed.</div>');
				}
				else
				{
					$this->Az->redirect('master/pancard/activeService', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! Coupon purchase failed.</div>');
				}
			}
			
		}
	
	}


	public function couponList()
    {
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$account_id = $this->User->get_domain_account();

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(9, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$couponList = $this->db->get_where('uti_pancard_coupon',array('account_id'=>$account_id,'user_id'=>$loggedUser['id']))->result_array();

		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'pancard/couponList',
            'couponList' => $couponList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    public function nsdlActive()
    {
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$account_id = $this->User->get_domain_account();

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(16, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$isNsdlActive = $this->User->get_nsdl_pancard_status($loggedUser['id']);
		if($isNsdlActive)
		{
			$this->Az->redirect('master/pancard/aaplyNsdl', 'system_message_error',lang('NSDL_ACTIVE_ALREADY_ERROR'));
		}

		// get nsdl state list
		$stateList = $this->db->get('nsdl_state')->result_array();

		
		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'pancard/activeNsdl',
            'stateList' => $stateList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    // save member
	public function nsdlActiveAuth()
	{
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');

		$this->form_validation->set_rules('firstname', 'First Name', 'required|xss_clean');		
		$this->form_validation->set_rules('lastname', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('dob', 'DOB', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|numeric|min_length[10]|max_length[10]|xss_clean');
		$this->form_validation->set_rules('pincode', 'Pincode', 'required|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
		$this->form_validation->set_rules('shop_name', 'Shop Name', 'required|xss_clean');
		$this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');
		$this->form_validation->set_rules('district_id', 'District', 'required|xss_clean');
		$this->form_validation->set_rules('pannumber', 'PAN Number', 'required|xss_clean');
		$this->form_validation->set_rules('aadharnumber', 'Aadhar Number', 'required|xss_clean');

		
        if ($this->form_validation->run() == FALSE) {
			
			$this->nsdlActive();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

        	$activeService = $this->User->account_active_service($loggedUser['id']);
			if(!in_array(16, $activeService)){
				$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
			}

			$isNsdlActive = $this->User->get_nsdl_pancard_status($loggedUser['id']);
			if($isNsdlActive)
			{
				$this->Az->redirect('master/pancard/nsdlActive', 'system_message_error',lang('NSDL_ACTIVE_ALREADY_ERROR'));
			}


			
			$com_amount = $this->User->get_pan_activation_charge($loggedUser['id']);
		    
		    $gst_amount = $com_amount*18/100;

		    $total_wallet_deduct = $com_amount + $gst_amount;


		     $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
            if($user_before_balance < $total_wallet_deduct){
                
                $this->Az->redirect('master/pancard/nsdlActive', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
                   

            }

            else

            {
            	 $transaction_id = time().rand(1111,9999);
		    
		    $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);

		    $after_balance = $user_before_balance - $total_wallet_deduct;  

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $loggedUser['id'],    
		                'before_balance'      => $user_before_balance,
		                'amount'              => $total_wallet_deduct,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 2,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'NSDL PAN ACTIVATION Txn #'.$transaction_id.' Charge Debited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);


        	$status = $this->Pancard_model->activeNsdlPancardMember($post,$transaction_id);
			
			if($status == 1)
			{
				//updtae kyc status
					$user_wallet = array(
			                'is_nsdl_active'=>1,        
			            );    
		            $this->db->where('id',$loggedUser['id']);
		            $this->db->update('users',$user_wallet);

				$this->Az->redirect('master/dashboard', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulation ! Your Pancard Service activate successfully.</div>');
			}
			else
			{
				$this->Az->redirect('master/pancard/nsdlActive', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Something Went Wrong.</div>');
			}


         }
         	
		}
	
	}

	public function nsdlProfile()
    {
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$account_id = $this->User->get_domain_account();

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(16, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$isNsdlActive = $this->User->get_nsdl_pancard_status($loggedUser['id']);
		if(!$isNsdlActive)
		{
			$this->Az->redirect('master/pancard/nsdlActive', 'system_message_error',lang('NSDL_ACTIVE_ERROR'));
		}

		// get nsdl state list
		$nsdlData = $this->db->get_where('nsdl_kyc',array('account_id'=>$account_id,'member_id'=>$loggedUser['id'],'status'=>2))->row_array();

		
		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'pancard/nsdlProfile',
            'nsdlData' => $nsdlData,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    public function aaplyNsdl()
    {
		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$account_id = $this->User->get_domain_account();

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(16, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		$isNsdlActive = $this->User->get_nsdl_pancard_status($loggedUser['id']);
		if(!$isNsdlActive)
		{
			$this->Az->redirect('master/pancard/nsdlActive', 'system_message_error',lang('NSDL_ACTIVE_ERROR'));
		}

		// get nsdl state list
		$nsdlData = $this->db->get_where('nsdl_kyc',array('account_id'=>$account_id,'member_id'=>$loggedUser['id'],'status'=>2))->row_array();

		$api_url = NSDL_INITIATE_URL;

        $header = [
          'Content-type: application/json',        
          'token: '.NSDL_TOKEN
        ];

        $requestData = array(
          'psaemailid' => $nsdlData['email'],
          'psamobile' => $nsdlData['mobile']
        );

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,        
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_URL => $api_url
        ));
        
        // Get response
        $output = curl_exec($curl);
        
        curl_close($curl);

        /*$output = '{
  "message": "PSA created successfull",
  "statuscode": "000",
  "usercode": "PSA0299310",
  "mobile": "8104758957",
  "emailid": "sonujangid2011@gmail.com"
}';*/

        $responseData = json_decode($output,true);

        $api_response_data = array(
         'account_id' => $account_id,
         'user_id' => $memberID,
         'api_url' => $api_url,
         'post_data'=>json_encode($requestData),
         'header_data' => json_encode($header),
         'api_response' =>$output,
         'created' => date('Y-m-d H:i:s')    
        );

        $this->db->insert('nsdl_api_response',$api_response_data);

        $responseData = json_decode($output,true);

        if(isset($responseData['statuscode']) && $responseData['statuscode'] == "000"){
        	redirect('http://web.gramsevak.com/location.aspx?text='.$responseData['text']);
        }
        else{

            $this->Az->redirect('master/pancard/nsdlProfile', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$responseData['message'].'</div>');
        }
    }

	public function getNsdlDistrictList($state_id = 0)
	{
		$str = '<option value="">Select District</option>';
		$districtList = $this->db->get_where('nsdl_district',array('state_id'=>$state_id))->result_array();
		if($districtList)
		{
			foreach($districtList as $list)
			{
				$str.='<option value="'.$list['id'].'">'.$list['title'].'</option>';
			}
		}
		echo $str;
	}

	public function nsdlList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
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
            'content_block' => 'pancard/nsdlList'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getNsdlList()
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
			0 => 'id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_nsdl_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_nsdl_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '".$keyword."%' ";
				$sql.=" OR a.type LIKE '".$keyword."%' ";
				$sql.=" OR a.order_id LIKE '".$keyword."%' ";
				$sql.=" OR a.psacode LIKE '".$keyword."%' ";
				$sql.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND  DATE(a.created) = '".$date."' ";    
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//$order_type = 'DESC';
			
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
				$nestedData[] = $list['user_code']."<br />".$list['name'];
				$nestedData[] = $list['type'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['order_id'];
				$nestedData[] = $list['psacode'];
				$nestedData[] = $list['pan_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['email'];
				$nestedData[] = $list['charge_amount'].' /-';
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				else
				{
					$nestedData[] = 'Proceed';
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


// 	public function nsdlPan()
// 	{
// 		$account_id = $this->User->get_domain_account();
// 	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
// 	 	$loggedAccountID = $loggedUser['id'];
	 	
// 	 	$activeService = $this->User->account_active_service($loggedUser['id']);
// 		if(!in_array(16, $activeService)){
// 			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
// 		}

// 		 $member_package_id = $this->User->getMemberPackageID($loggedAccountID);

// 		// get commission
//             $get_com_data = $this->db->get_where('nsdl_pancard_charge',array('account_id'=>$account_id,'package_id'=>$member_package_id))->row_array();
          	
//             $charge = isset($get_com_data['surcharge']) ? $get_com_data['surcharge'] : 0 ;

//             $chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();

//             $user_before_balance = $chk_wallet_balance['wallet_balance'];
//             if($chk_wallet_balance['wallet_balance'] < $charge){
                
//                 $this->Az->redirect('master/pancard/nsdlPan', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
                   
//             }

//             else

//             {
//             		$nsdl_uat_url = MORNINGPAY_NSDL_API_URL;
// 		$nsdl_token = MORNINGPAY_NSDL_API_TOKEN;

//         $header = [
//           'Content-type: application/json'      
          
//         ];
        	

// 		$requestData = array(
//           'Token' => $nsdl_token,
//           'RetailerID' =>$loggedUser['user_code'],
//           'LogoUrl' => MORNINGPAY_LOGO,
//           'Copyright' => MORNINGPAY_COPYRIGHT_MSG,
//           'FirmName' =>MORNINGPAY_FIRM_NAME,
//           'ServiceId' =>MORNINGPAY_SERVICE_ID
//         );

//         $curl = curl_init();
    
//         curl_setopt_array($curl, array(
//             CURLOPT_RETURNTRANSFER => true,        
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 120,
//             CURLOPT_CUSTOMREQUEST => "POST",
//             CURLOPT_POSTFIELDS => json_encode($requestData),
//             CURLOPT_HTTPHEADER => $header,
//             CURLOPT_URL => $nsdl_uat_url
//         ));
        
//         // Get response
//         $output = curl_exec($curl);
        
//         curl_close($curl);
        
//       echo $output;
//       die;
//         $responseData = json_decode($output,true);

//       $token = $responseData['Data']['Token'];



//         $apiData = array(
//             'account_id' =>$account_id,
//             'user_id' => $loggedAccountID,
//             'api_url' => $nsdl_uat_url,
//             'api_response' => $output,
//             'token'=>$token,            
//             'post_data' => json_encode($requestData),
//             'created' => date('Y-m-d H:i:s'),
//             'created_by' => 1
//         );
//         $this->db->insert('morningpay_nsdl_api_response',$apiData);

//         if(isset($responseData['StatusCode']) && $responseData['StatusCode'] == 1 && $responseData['Message'] =='Success')

//         {
//         	$token = $responseData['Data']['Token'];
        	
//         }
//         else

//         {
//         	$token  = 0;
//         }



// 	 	$siteUrl = base_url();		

// 		$data = array(
//             'meta_title' => lang('SITE_NAME'),
//             'meta_keywords' => lang('SITE_NAME'),
//             'meta_description' => lang('SITE_NAME'),
//             'site_url' => $siteUrl,
// 			'loggedUser'  => $loggedUser,
// 			'token'		=>$token,
// 			'system_message' => $this->Az->getSystemMessageError(),
//             'system_info' => $this->Az->getsystemMessageInfo(),
//             'system_warning' => $this->Az->getSystemMessageWarning(),
//             'content_block' => 'pancard/nsdl-pan-apply'
//         );
//         $this->parser->parse('master/layout/column-1' , $data);


//             }
// 	}


	public function nsdlPan()
	{
	    
		$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
 	 	$loggedAccountID = $loggedUser['id'];
	 	    
	 	    
	 	    
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(16, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
            
          
		 $member_package_id = $this->User->getMemberPackageID($loggedAccountID);
        
		// get commission
            	
            $charge = $this->User->get_pan_charge($loggedUser['id']);   
            
            

            $gst_amount = $charge*18/100;

		    $total_wallet_deduct = $charge + $gst_amount;
            
          

            $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
            
            if($user_before_balance < $total_wallet_deduct){
               
            $this->Az->redirect('master/dashboard', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
                   
            }

            else

            {
              
            	$nsdl_uat_url = MORNINGPAY_NSDL_API_URL;
		$nsdl_token = MORNINGPAY_NSDL_API_TOKEN;

        $header = [
          'Content-type: application/json'      
          
        ];
        	

		$requestData = array(
          'Token' => $nsdl_token,
          'RetailerID' =>$loggedUser['user_code'],
          'LogoUrl' => MORNINGPAY_LOGO,
          'Copyright' => MORNINGPAY_COPYRIGHT_MSG,
          'FirmName' =>MORNINGPAY_FIRM_NAME,
          'ServiceId' =>MORNINGPAY_SERVICE_ID
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
        
      
        $responseData = json_decode($output,true);

       $token = $responseData['Data']['Token'];



        $apiData = array(
            'account_id' =>$account_id,
            'user_id' => $loggedAccountID,
            'api_url' => $nsdl_uat_url,
            'api_response' => $output,
            'token'=>$token,            
            'post_data' => json_encode($requestData),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('morningpay_nsdl_api_response',$apiData);

        if(isset($responseData['StatusCode']) && $responseData['StatusCode'] == 1 && $responseData['Message'] =='Success')

        {
        	$token = $responseData['Data']['Token'];
        	
        }
        else

        {
        	$token  = 0;
        }



	 	$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'token'		=>$token,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'pancard/nsdl-pan-apply'
        );
        $this->parser->parse('master/layout/column-1' , $data);


            }
	}


	//find pan 

	public function findPan()
    {

		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$account_id = $this->User->get_domain_account();

		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'pancard/findPan',           
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);
        $this->parser->parse('master/layout/column-1', $data);
		
    }

    // save member
	public function findPanAuth()
	{
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');		
		$this->form_validation->set_rules('dob', 'DOB', 'required|xss_clean');		
		$this->form_validation->set_rules('aadharnumber', 'Aadhar Number', 'required|xss_clean');

		
        if ($this->form_validation->run() == FALSE) {
			
			$this->findPan();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);

        	
			$com_amount = $this->User->get_find_pan_charge($loggedUser['id']);
		    
		    $gst_amount = $com_amount*18/100;

		    $total_wallet_deduct = $com_amount + $gst_amount;

		     $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
            if($user_before_balance < $total_wallet_deduct){
                
                $this->Az->redirect('master/pancard/findPan', 'system_message_error',lang('WALLET_BALANCE_ERROR'));                
            }


		    $transaction_id = time().rand(1111,9999);
		    
		    $after_balance = $user_before_balance - $total_wallet_deduct;  

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $loggedUser['id'],    
		                'before_balance'      => $user_before_balance,
		                'amount'              => $total_wallet_deduct,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 2,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'PAN FIND Txn #'.$transaction_id.' Charge Debited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            //save gst data
			                 $gst_data = array(
                                'account_id'          => $account_id,
                                'member_id'           =>$loggedUser['id'],  
                                'txn_id'            =>$transaction_id,
                                'charge_amount'      => $com_amount,
                                'gst_charge'              =>$gst_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'), 
                                'service'				=>'PAN FIND',
                                'description'         => 'PAN FIND Txn  #'.$transaction_id.'  Charge Debited'
                            );

                        $this->db->insert('gst_report',$gst_data);

			        	$status = $this->Pancard_model->findPanNumber($post,$transaction_id);
						
						if($status == 1)
						{

							$this->Az->redirect('master/pancard/findPanList', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulation ! Your  Request  successfully submitted.</div>');
						}
						else
						{
							$this->Az->redirect('master/pancard/findPanList', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Something Went Wrong.</div>');
						}
			
		}
	
	}


	public function findPanList()
    {


		$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
		$account_id = $this->User->get_domain_account();

		$records = $this->db->get_where('find_pan_number',array('account_id'=>$account_id,'member_id'=>$loggedUser['id']))->result_array();
		

		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'pancard/findPanList',   
            'records'		=>$records,        
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);
        $this->parser->parse('master/layout/column-1', $data);
		
    }
    
    
    //uti pan balance

    public function utiBalanceRequest()
    {

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'pancard/uti-balance-request',           
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);
        $this->parser->parse('master/layout/column-1', $data);
		
    }

    // save member
	public function utiBalanceAuth()
	{
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');

		$this->form_validation->set_rules('uti_pan_id', 'UTI PAN ID', 'required|xss_clean');
		$this->form_validation->set_rules('coupon', 'Coupon', 'required|xss_clean');
		
        if ($this->form_validation->run() == FALSE) {
			
			$this->utiBalanceRequest();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
	        $accountData = $this->User->get_account_data($account_id);
	        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	        $loggedAccountID = $loggedUser['id'];

        	
			$com_amount = $this->User->get_uti_balance_charge($loggedUser['id']);
		    
		    $coupon_amount = $post['coupon'] * $com_amount;

		    $total_wallet_deduct = $coupon_amount;

		     
		     $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
            if($user_before_balance < $total_wallet_deduct){
                
                $this->Az->redirect('master/pancard/utiBalanceRequest', 'system_message_error',lang('WALLET_BALANCE_ERROR'));                
            }


            else

            {
            	$transaction_id = time().rand(1111,9999);
		    
		    $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);

		    $after_balance = $user_before_balance - $total_wallet_deduct;  

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $loggedUser['id'],    
		                'before_balance'      => $user_before_balance,
		                'amount'              => $total_wallet_deduct,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 2,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UTI PAN BALANCE Txn #'.$transaction_id.' Charge Debited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

			        $status = $this->Pancard_model->utiBalanceRequest($post,$transaction_id);
						
						if($status == 1)
						{

							$this->Az->redirect('master/pancard/utiBalanceRequest', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulation ! Your  Request  successfully submitted.</div>');
						}
						else
						{
							$this->Az->redirect('master/pancard/utiBalanceRequest', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Something Went Wrong.</div>');
						}

            	}
            
		}
	
	}


	public function utiBalanceList()
    {


		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

		
		$records = $this->db->get_where('uti_balance_request',array('account_id'=>$account_id,'member_id'=>$loggedUser['id']))->result_array();
		

		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'pancard/utiBalanceList',   
            'records'		=>$records,        
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);
        $this->parser->parse('master/layout/column-1', $data);
		
    }



     public function getCouponBalance($coupon = '')
    {
    	$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
	 	
	 	$com_amount = $this->User->get_uti_balance_charge($loggedAccountID);
		    
		$coupon_amount = $coupon* $com_amount;

	 	if($coupon_amount)
	 	{
		 		$response = array(
		 			'status' => 1,
		 			'amount' => '<font color="green">Amount : '.$coupon_amount.'</font>'
		 		);
	 	}
	 	else
	 	{
	 			$response = array(
		 			'status' => 0,
		 			'amount' => '<font color="red"> Amount : 0</font>'
		 		);
	 	}
	 		
	 	echo json_encode($response);
    }
    

	
}