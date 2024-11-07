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

    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */