<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Page extends CI_Controller{

    public function __construct() {
        parent::__construct();
		$this->lang->load('front_login' , 'english');
        
    }
	
	public function index($page_slug = ''){
		 
        // get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $pageData = $this->db->get_where('front_pages',array('account_id'=>$account_id,'page_slug'=>$page_slug))->row_array();

        if(!$pageData){

            redirect('/');
        }
		
		$siteUrl = base_url();

        if($accountData['web_theme'] == 1){
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
                'content_block' => 'page'
            );
            $this->parser->parse('main-front/layout/column-1' , $data);
        }
        elseif($accountData['web_theme'] == 2){

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
                'content_block' => 'page'
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
                'pageData' => $pageData,
                'system_message' => $this->Az->getSystemMessageError(),
                'system_info' => $this->Az->getsystemMessageInfo(),
                'system_warning' => $this->Az->getSystemMessageWarning(),
                'content_block' => 'page'
            );
            $this->parser->parse('theme-three/layout/column-1' , $data);
            
        }
    }


    public function ourPartner(){
         
        // get account id
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

       
        
        $siteUrl = base_url();

      
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
                'content_block' => 'our-partner'
            );
            $this->parser->parse('front-payol/layout/column-1' , $data);
       
        
    }


    public function auth(){
        
        $post = $this->input->post();

      
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email ', 'required|xss_clean|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|numeric|min_length[10]|max_length[10]');
        //$this->form_validation->set_rules('partner_type', 'Partner Type', 'required|xss_clean');
        //$this->form_validation->set_rules('product_interest', 'Product Intrest', 'required|xss_clean');
       
        if ($this->form_validation->run() == FALSE) {
            $this->ourPartner();
            return false;
        }   
        else
        {
            $account_id = $this->User->get_domain_account();

            $services = implode(',',$post['product_interest']);

            
            $data = array(
                'account_id' => $account_id,
                
                'name' => $post['name'],
                'email' => $post['email'],
                'mobile' => $post['mobile'],
                'partner_type' =>$post['partner_type'],
                'product_intrest' =>$services,
                'status'            =>0,
                'message' =>$post['message'],
                'created' => date('Y-m-d H:i:s')
            );

            $this->db->insert('user_register_request',$data);
            
            $this->Az->redirect('Page/ourPartner', 'system_message_error','Your Request Was Submitted Successfully.');

        }
        
    }

    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */