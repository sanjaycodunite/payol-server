<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class StaticQr extends CI_Controller{

    public function __construct() {
        parent::__construct();
		
    }

    public function index($qr_id = ''){

        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $qr_url = '';
        if($qr_id){

            $get_qr_url = $this->db->select('qr_url')->get_where('users',array('is_upi_qr_active'=>1,'id'=>$qr_id))->row_array();

            $qr_url = $get_qr_url['qr_url'];
            
        }

		$siteUrl = base_url();
        
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'qr_url'   => $qr_url,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'qrcode'
        );
            
        $this->parser->parse('front/layout/column-3' , $data);


    }

}


/* End of file login.php */
/* Location: ./application/controllers/login.php */