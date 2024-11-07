<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class PanRedirect extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        //load language
        //$this->lang->load('front/message', 'english');
        $this->load->model('admin/Jwt_model');
    }
	
	public function index($record_id = 0){

        $pan_data = $this->db->get_where('nsdl_api_response',array('id'=>$record_id))->row_array();

        $encode = $pan_data['encode'];
        $redirect_url = $pan_data['redirect_url'];

		
        $siteUrl = base_url();
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'encode' => $encode,
            'redirect_url' =>$redirect_url,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'pan-redirect'
        );
        $this->parser->parse('front/layout/column-3' , $data);
    }

	
	
	
}
