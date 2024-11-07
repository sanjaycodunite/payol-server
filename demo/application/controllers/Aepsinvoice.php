<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Aepsinvoice extends CI_Controller{

    public function __construct() {
        parent::__construct();
		
    }

    public function index($id = ''){

        //get logged user info
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

        $sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


        $detail = $this->db->query($sql)->row_array();
        

        
        $siteUrl = base_url();      

        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'detail' => $detail,
            'address'=>$address,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'aeps-invoice'
        );
        $this->parser->parse('front/layout/column-3' , $data);
    
    
    }   

}


/* End of file login.php */
/* Location: ./application/controllers/login.php */