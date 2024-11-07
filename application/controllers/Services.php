<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Services extends CI_Controller{

    public function __construct() {
        parent::__construct();
		$this->lang->load('front_login' , 'english');
        
    }
	
	public function index(){
		
		// get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
		
		$siteUrl = base_url();
 $service = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array();

         if($accountData['web_theme'] == 1){
		      
              $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('main-front/layout/column-1' , $data);
        }

        else if($accountData['web_theme'] ==2)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('front/layout/column-1' , $data);

        }

        else if($accountData['web_theme'] ==3)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'service' => $service,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('theme-three/layout/column-1' , $data);

        }


         else if($accountData['web_theme'] ==4)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('front-payol/layout/column-1' , $data);

        }

         else if($accountData['web_theme'] ==5)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('front-morningpay/layout/column-1' , $data);

        }

         else if($accountData['web_theme'] ==6)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('front-payrise/layout/column-1' , $data);

        }





    }

     public function billPayment(){
         
        // get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

     
     
        $siteUrl = base_url();

        
            
        if($accountData['web_theme'] == 1){
              
              $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('main-front/layout/column-1' , $data);
        }

        else if($accountData['web_theme'] ==2)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('front/layout/column-1' , $data);

        }

        else if($accountData['web_theme'] ==3)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'service' => $service,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('theme-three/layout/column-1' , $data);

        }


         else if($accountData['web_theme'] ==4)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'bill-payment'
        );
        $this->parser->parse('front-payol/layout/column-1' , $data);

        }

         else if($accountData['web_theme'] ==5)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'bill-payment'
        );
        $this->parser->parse('front-morningpay/layout/column-1' , $data);

        }
        
    }
    
    
    public function paymentGateway(){
         
        // get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

     
     
        $siteUrl = base_url();

        
           if($accountData['web_theme'] == 1){
              
              $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('main-front/layout/column-1' , $data);
        }

        else if($accountData['web_theme'] ==2)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('front/layout/column-1' , $data);

        }

        else if($accountData['web_theme'] ==3)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'service' => $service,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('theme-three/layout/column-1' , $data);

        }


         else if($accountData['web_theme'] ==4)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'payment-gateway'
        );
        $this->parser->parse('front-payol/layout/column-1' , $data);

        }

         else if($accountData['web_theme'] ==5)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'payment-gateway'
        );
        $this->parser->parse('front-morningpay/layout/column-1' , $data);

        }

        
    }
    


    public function insurance(){
         
        // get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

     
     
        $siteUrl = base_url();

        if($accountData['web_theme'] == 1){
              
              $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('main-front/layout/column-1' , $data);
        }

        else if($accountData['web_theme'] ==2)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('front/layout/column-1' , $data);

        }

        else if($accountData['web_theme'] ==3)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'service' => $service,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('theme-three/layout/column-1' , $data);

        }


         else if($accountData['web_theme'] ==4)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'insurance'
        );
        $this->parser->parse('front-payol/layout/column-1' , $data);

        }

         else if($accountData['web_theme'] ==5)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'insurance'
        );
        $this->parser->parse('front-morningpay/layout/column-1' , $data);

        }
        
    }
    
    
      public function director(){
         
        // get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

     
     
        $siteUrl = base_url();

        if($accountData['web_theme'] == 4){
            $data = array(
                'meta_title' => lang('SITE_NAME'),
                'meta_keywords' => lang('SITE_NAME'),
                'meta_description' => lang('SITE_NAME'),
                'site_url' => $siteUrl,
                'accountData' => $accountData,
                'pageData' => $pageData,
                'system_message' => $this->Az->getSystemMessageError(),
                'system_info' => $this->Az->getsystemMessageInfo(),
                'system_warning' => $this->Az->getSystemMessageWarning(),
                'content_block' => 'director'
            );
            $this->parser->parse('front-payol/layout/column-1' , $data);
        }
        
    }
    

    public function tour(){
         
        // get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

     
     
        $siteUrl = base_url();

         if($accountData['web_theme'] == 1){
              
              $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('main-front/layout/column-1' , $data);
        }

        else if($accountData['web_theme'] ==2)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('front/layout/column-1' , $data);

        }

        else if($accountData['web_theme'] ==3)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'service' => $service,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'service'
        );
        $this->parser->parse('theme-three/layout/column-1' , $data);

        }


         else if($accountData['web_theme'] ==4)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'tour'
        );
        $this->parser->parse('front-payol/layout/column-1' , $data);

        }

         else if($accountData['web_theme'] ==5)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'tour'
        );
        $this->parser->parse('front-morningpay/layout/column-1' , $data);

        }

          else if($accountData['web_theme'] ==6)
        {

            $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'tour'
        );
        $this->parser->parse('front-morningpay/layout/column-1' , $data);

        }
        
    }


     public function vendor(){
         
        // get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

     
     
        $siteUrl = base_url();

        if($accountData['web_theme'] == 4){
            $data = array(
                'meta_title' => lang('SITE_NAME'),
                'meta_keywords' => lang('SITE_NAME'),
                'meta_description' => lang('SITE_NAME'),
                'site_url' => $siteUrl,
                'accountData' => $accountData,
                'pageData' => $pageData,
                'system_message' => $this->Az->getSystemMessageError(),
                'system_info' => $this->Az->getsystemMessageInfo(),
                'system_warning' => $this->Az->getSystemMessageWarning(),
                'content_block' => 'vendor-payment'
            );
            $this->parser->parse('front-payol/layout/column-1' , $data);
        }
        
    }


    public function ecommerce(){
         
        // get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

     
     
        $siteUrl = base_url();

        if($accountData['web_theme'] == 4){
            $data = array(
                'meta_title' => lang('SITE_NAME'),
                'meta_keywords' => lang('SITE_NAME'),
                'meta_description' => lang('SITE_NAME'),
                'site_url' => $siteUrl,
                'accountData' => $accountData,
                'pageData' => $pageData,
                'system_message' => $this->Az->getSystemMessageError(),
                'system_info' => $this->Az->getsystemMessageInfo(),
                'system_warning' => $this->Az->getSystemMessageWarning(),
                'content_block' => 'ecommerce'
            );
            $this->parser->parse('front-payol/layout/column-1' , $data);
        }
        
    }
    


    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */