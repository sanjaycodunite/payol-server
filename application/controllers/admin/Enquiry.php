<?php
if (!defined('BASEPATH')) {
    exit('No direct scrip access allowed');
}

class Enquiry extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->User->checkAdminPermission();
        $this->lang->load('superadmin/dashboard', 'english');
        $this->lang->load('front_common', 'english');
    }

    public function contactFormEnquiryList()
    {

		//get logged user info
        $domain_account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();
        $list = $this->db->get_where('tbl_get_in_touch_contacts')->result_array();

        // save system log
        $log_msg = '[' . date('d-m-Y H:i:s') . ' - Account - ' . $domain_account_id . ' - IP - ' . $user_ip_address . ' - User(' . $loggedUser['user_code'] . ') - Open Payol Contact Us Form Enquiry List Page.]' . PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

        $siteUrl = base_url();
        $data = [
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser' => $loggedUser,
            'contactFormList' => $list,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'enquiry/contactFormList',
        ];
		$this->parser->parse('admin/layout/column-1', $data);
    }
}