<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Errorpage extends CI_Controller{

    public function __construct() {
        parent::__construct();
		
    }
	
	public function index(){
		
		
		$siteUrl = base_url();
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'error'
        );
        $this->parser->parse('front/layout/column-3' , $data);
    }

    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */