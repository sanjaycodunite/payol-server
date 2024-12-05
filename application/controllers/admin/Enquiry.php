<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enquiry extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User'); // Ensure User model is loaded
        $this->User->checkAdminPermission();
        $this->lang->load('superadmin/dashboard', 'english');
        $this->lang->load('front_common', 'english');
    }

    public function contactFormEnquiryList()
    {
        $domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();

        $list = $this->db->get_where('tbl_get_in_touch_contacts',array('is_delete'=>0))->result_array();

        // Save system log
        $log_msg = sprintf(
            '[%s - Account - %s - IP - %s - User(%s) - Opened Contact Us Form Enquiry List Page.]',
            date('d-m-Y H:i:s'),
            $domain_account_id,
            $user_ip_address,
            $loggedUser['user_code']
        );
        $this->User->generateAccountActivityLog($log_msg);

        $data = [
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => base_url(),
            'loggedUser' => $loggedUser,
            'contactFormList' => $list,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'enquiry/contactFormList',
        ];

        $this->parser->parse('admin/layout/column-1', $data);
    }


    public function becomeAPatnerFormEnquiryList()
    {
        $domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();

			$list = $this->db->get_where('tbl_user_register_request',array('is_delete'=>0))->result_array();
        // Save system log
        $log_msg = sprintf(
            '[%s - Account - %s - IP - %s - User(%s) - Opened Become A Partner Form Enquiry List Page.]',
            date('d-m-Y H:i:s'),
            $domain_account_id,
            $user_ip_address,
            $loggedUser['user_code']
        );
        $this->User->generateAccountActivityLog($log_msg);

        $data = [
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => base_url(),
            'loggedUser' => $loggedUser,
            'becomeAPatnerFormEnquiryList' => $list,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'enquiry/becomeAPatnerFormEnquiryList',
        ];

        $this->parser->parse('admin/layout/column-1', $data);
    }

	public function deleteEnquiry()
	{
		$response = [];
		$tableName  = $this->input->post('tableName');
		$enquiryId  = $this->input->post('enquiryId');
		$enquiryType = ($this->input->post('enquiryType') == 'webContactForm')
					? "Contact Enquiry"
					: "Become A Partner Enquiry";
		$account_id = $this->User->get_domain_account();

		if (empty($tableName) || empty($enquiryId)) {
			$response = [
				'error' => true,
				'dataval' => 'Invalid data provided.'
			];
			echo json_encode($response);
			return;
		}

		$this->db->where([
			'id' => $enquiryId
		]);
		$updated = $this->db->update($tableName, ['is_delete'=>1,'deleted_at' => date('Y-m-d H:i:s')]);

		if ($updated) {
			$response = [
				'error' => false,
				'dataval' => "$enquiryType deleted successfully."
			];
		} else {
			log_message('error', "Failed to soft delete $enquiryType: " . $this->db->last_query());
			$response = [
				'error' => true,
				'dataval' => "Failed to delete $enquiryType. Please try again."
			];
		}

		echo json_encode($response);
	}


}