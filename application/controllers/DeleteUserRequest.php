<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class DeleteUserRequest extends CI_Controller{

    public function __construct() {
        parent::__construct();
        $this->lang->load('front_login' , 'english');
		
    }

    public function index(){

      

		$siteUrl = base_url();
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
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
            'content_block' => 'DeleteUserRequest'
        );
            
        $this->parser->parse('front/layout/column-3' , $data);


    }
    
    public function auth()
    {
        $account_id = $this->User->get_domain_account();
        $post = $this->input->post();
        $this->load->library('form_validation');
		$this->form_validation->set_rules('mobile', 'Mobile ', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE) {
			
			$this->DeleteUserRequest();
		}
		
		else
		{
		   $checkMobile = $this->db->get_where('users', ['account_id'=>$account_id,'mobile' => $post['mobile']])->num_rows();
        	if(!$checkMobile){
        		$this->Az->redirect('DeleteUserRequest', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry Mobile number not exists in our system.</div>');
        	}
		    
		    $data = array( 
		                'account_id'=>$account_id,
		                'mobile'=>$post['mobile']
		            );

		            $this->db->insert('user_delete_request',$data);
		            $this->Az->redirect('DeleteUserRequest', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Delete Request Sent Successfully.</div>');
		    
		}
		
    }

}


/* End of file login.php */
/* Location: ./application/controllers/login.php */