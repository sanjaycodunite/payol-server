<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Home extends CI_Controller{

    public function __construct() {
        parent::__construct();
		$this->lang->load('front_login' , 'english');
         $this->load->model('admin/User_model'); 
        
    }

    public function index(){

		// get account id
        $account_id = $this->User->get_domain_account();
        
        //$account_id = 2;
        $accountData = $this->User->get_account_data($account_id);


        $slider = $this->db->get_where('website_slider',array('account_id'=>$account_id))->result_array();

        $service = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array();

         $blog = $this->db->get_where('website_blog',array('account_id'=>$account_id))->result_array();

        $testimonial = $this->db->get_where('website_testimonial',array('account_id'=>$account_id))->result_array();

		$siteUrl = base_url();
        if($accountData['web_theme'] == 1){

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'slider'   => $slider,
            'service'  => $service,
            'blog' => $blog,
            'testimonial' => $testimonial,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'home'
        );
            
        $this->parser->parse('main-front/layout/column-1' , $data);

        }
        elseif($accountData['web_theme'] == 2 ){

            $data = array(
                'meta_title' => lang('SITE_NAME'),
                'meta_keywords' => lang('SITE_NAME'),
                'meta_description' => lang('SITE_NAME'),
                'site_url' => $siteUrl,
                'accountData' => $accountData,
                'slider'   => $slider,
                'service'  => $service,
                'testimonial' => $testimonial,
                'system_message' => $this->Az->getSystemMessageError(),
                'system_info' => $this->Az->getsystemMessageInfo(),
                'system_warning' => $this->Az->getSystemMessageWarning(),
                'content_block' => 'home'
            );
                
            $this->parser->parse('front/layout/column-1' , $data);

        }
        elseif($accountData['web_theme'] == 3){


            $data = array(
                'meta_title' => lang('SITE_NAME'),
                'meta_keywords' => lang('SITE_NAME'),
                'meta_description' => lang('SITE_NAME'),
                'site_url' => $siteUrl,
                'accountData' => $accountData,
                'slider'   => $slider,
                'service'  => $service,
                'testimonial' => $testimonial,
                'system_message' => $this->Az->getSystemMessageError(),
                'system_info' => $this->Az->getsystemMessageInfo(),
                'system_warning' => $this->Az->getSystemMessageWarning(),
                'content_block' => 'home'
            );
                
            $this->parser->parse('theme-three/layout/column-1' , $data);

        }

         elseif($accountData['web_theme'] == 4){
            
            $data = array(
                'meta_title' => lang('SITE_NAME'),
                'meta_keywords' => lang('SITE_NAME'),
                'meta_description' => lang('SITE_NAME'),
                'site_url' => $siteUrl,
                'accountData' => $accountData,
                'slider'   => $slider,
                'service'  => $service,
                'testimonial' => $testimonial,
                'system_message' => $this->Az->getSystemMessageError(),
                'system_info' => $this->Az->getsystemMessageInfo(),
                'system_warning' => $this->Az->getSystemMessageWarning(),
                'content_block' => 'home'
            );
                
            $this->parser->parse('front-payol/layout/column-1' , $data);

        }

        elseif($accountData['web_theme'] == 5){

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'slider'   => $slider,
            'service'  => $service,
            'blog' => $blog,
            'testimonial' => $testimonial,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'home'
        );
            
        $this->parser->parse('front-morningpay/layout/column-1' , $data);

        }
        
        elseif($accountData['web_theme'] == 6){

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'slider'   => $slider,
            'service'  => $service,
            'blog' => $blog,
            'testimonial' => $testimonial,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'home'
        );
            
        $this->parser->parse('front-payrise/layout/column-1' , $data);

        }

		
    }
    
    public function packageID()
    {
        $loggedAccountID = 110;
        $member_package_id = $this->User->getMemberPackageID($loggedAccountID);
        echo $member_package_id;
        echo "<br>";
        
        $md_surcharge_amount =  $this->User->get_uti_balance_charge($loggedAccountID);
        echo $md_surcharge_amount;
        die;
        
    }
    
    public function fetchCCBill()
    {
        
        log_message('debug', 'Service Bill Fetch Bill post Data - '.json_encode($post_data));
		log_message('debug', 'Service Bill Fetch Bill Biller ID - '.$biller_id);
		$account_id = 1;
		
				
			$header = [
			    'Content-Type:application/json',
			    'X-MClient:14',
			    'Checksum:+8aUoqoan3uf97WELlmPPRQPipapE1wKrLUyYK8bNIQ='
			];
			


			$canumber = '4541982335380231';

			$last4last4 =substr($canumber, - 4 );
			
			$customer_mobile = '8104758957';
			$public_key = file_get_contents('public_key.txt');
            
	        openssl_public_encrypt($canumber, $encrypted_data, $public_key, OPENSSL_PKCS1_PADDING);
	        
	        $canumber = urlencode(base64_encode($encrypted_data));

			$additionalParamsStr='';

			$body = '{"uid":"'.MOBIKWIK_USER_ID.'","pswd":"'.MOBIKWIK_USER_PWD.'","mobile": "'.$customer_mobile.'","encrypted_card": "'.$canumber.'","last4": "'.$last4last4.'"}';
            
			$httpUrl = MOBIKWIK_CC_BILL_FETCH_API;

			log_message('debug', 'Bill fetch api url - '.$httpUrl);

			log_message('debug', 'Bill fetch api header data - '.json_encode($header));

			log_message('debug', 'Bill fetch api body data - '.json_encode($body));
			
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
			
			// save api response 
	        $api_data = array(
	        	'account_id' => $account_id,
	        	'user_id' => $member_id,
	        	'api_response' => $output,
	        	'api_url' => $httpUrl,
	        	'api_post_data' => $body,
	        	'status' => 1,
	        	'created' => date('Y-m-d H:i:s')
	        );
	        $this->db->insert('bbps_api_response',$api_data);
	        
			
			echo $output;
        
    }
    
    public function payoutResponse(){
        
        $recordList = $this->db->query("SELECT * FROM `tbl_open_money_api_response` WHERE DATE(created) = '".date('Y-m-d')."' AND api_url NOT LIKE '%beneficiaries%' ORDER BY id DESC")->result_array();
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'recordList' => $recordList,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'payout-response'
        );
            
        $this->parser->parse('front/layout/column-3' , $data);

        

		
    }
    
    
    



    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */