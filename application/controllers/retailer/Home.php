<?php
if(!defined('BASEPATH')) exit('No direct scrip access allowed');
class Home extends CI_Controller{

    public function __construct() {
        parent::__construct();
		$this->lang->load('front_login' , 'english');
        
    }

    public function index(){
        
        // get account id
        $domain_account_id = $this->User->get_domain_account();
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        
        $account_id = $loggedUser['id'];
		$siteUrl = base_url();
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'home'
        );
        $this->parser->parse('retailer/layout/column-1' , $data);
    }
}