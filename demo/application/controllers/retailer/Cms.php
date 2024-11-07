<?php 
class Cms extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkRetailerPermission();
        $this->load->model('retailer/Cms_model');		
        $this->lang->load('retailer/cms', 'english');
        $this->load->model('admin/Jwt_model');
        
    }

    public function index(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'member_id' =>$loggedAccountID,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'cms/list'
        );
        $this->parser->parse('retailer/layout/column-1' , $data);
    
	
	}

	public function cmsActiveAuth()
	{
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		//check for foem validation
		$post = $this->input->post();
		
		$memberID = $loggedUser['id'];
		//	
			

        	$activeService = $this->User->account_active_service($loggedUser['id']);
			if(!in_array(21, $activeService)){
				$this->Az->redirect('retailer/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
			}

             $cms_amount = 100;
            
            $user_before_balance = $this->User->getMemberWalletBalanceSP($memberID);
            if($user_before_balance < $cms_amount){
                
                $this->Az->redirect('retailer/cms', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
                   

            }

        	// upload front document
			
			$transaction_id = time().rand(1111,9999);


			$cms_data = array(

          	'account_id' =>$account_id,
          	'member_id' =>$memberID,          	
          	'transaction_id'=>$transaction_id,
          	'created'=>date('Y-m-d H:i:s'),
          	'created_by'=>$memberID
          );

         $this->db->insert('member_cms_transcation',$cms_data);

         $cms_redirect_url = 'https://www.'.$accountData['domain_url'].'/retailer/cms/index';

        $key =PAYSPRINT_AEPS_KEY;
        $iv=  PAYSPRINT_AEPS_IV;
        $datapost =array();

               	$datapost['transaction_id'] = $transaction_id;
               	$datapost['redirect_url'] = $cms_redirect_url;

        $cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
        $body=       base64_encode($cipher);
        $reqid = time().rand(1111,9999);

        log_message('debug', 'CMS Api RequestID - '.$reqid);     

        $jwt_payload = array(
            'timestamp'=>time(),
            'partnerId'=>PAYSPRINT_PARTNER_ID,
            'reqid'=>$reqid
        );

        log_message('debug', 'CMS Auth Api jwt payload - '.json_encode($jwt_payload));

        $secret = PAYSPRINT_SECRET_KEY;

        $token = $this->Jwt_model->encode($jwt_payload,$secret);

            //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

        $header = [
            'Token:'.$token,
            'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY            
        ];



        $httpUrl = PAYSPRINT_CMS_URL;
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

        $apiData = array(
            'account_id' =>$account_id,
            'user_id' => $memberID,
            'api_url' => $httpUrl,
            'api_response' => $output,
            'post_data' => json_encode($datapost),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('cms_api_response',$apiData);

        if(isset($responseData['response_code']) && $responseData['response_code'] == 1)
        {
          
        $redirecturl = $responseData['redirecturl'];
				redirect($redirecturl); 
            
        }
        else
        {
           if(!in_array(21, $activeService)){
				$this->Az->redirect('retailer/cms', 'system_message_error',lang('MEMBER_ERROR'));
			}
        }

			
		//}
	
	}


	
	
	
	
}