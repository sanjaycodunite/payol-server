<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Profile extends CI_Controller{

    public function __construct() {
        parent::__construct();
        //load language
		$this->User->checkPermission();
		$this->load->model('admin/Profile_model');
        $this->lang->load('admin/profile', 'english');
        $this->lang->load('front_common' , 'english');
    }


    public function profile($uname_prefix = '' , $username = ''){
        //get logged user info
        $loggedUser = $this->User->getLoggedUser("freeshopify_admin");
        $account_id = $loggedUser['id'];
        
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,
            'page_title' => 'Profile',
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'profile/myProfile'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }


	
	public function update() {
        
		$this->load->library('template');
        $siteUrl = site_url();
		$post = $this->input->post();
		
        //get logged user info
        $loggedUser = $this->User->getLoggedUser('freeshopify_admin');
        $account_id = $loggedUser['id'];

        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('opw', 'Old Password', 'required|xss_clean');		
        $this->form_validation->set_rules('npw', 'New Password', 'required|xss_clean');     
        $this->form_validation->set_rules('cpw', 'Confirm New Password', 'required|xss_clean|matches[npw]');     
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('admin/profile/profile', 'system_message_error',lang('CHK_ALL_FEILDS'));
            
        } 
		else {
			
            // check old password valid or not
            $chk_old_pwd = $this->db->get_where('users',array('id'=>$account_id,'password'=>do_hash($post['opw'])))->num_rows();
            if(!$chk_old_pwd)
            {
                $this->Az->redirect('admin/profile/profile', 'system_message_error',lang('OLD_PASSWORD_FAILED'));   
            }

			$this->Profile_model->updateAdminPassword($post,$account_id);
			
			$this->Az->redirect('admin/profile/profile', 'system_message_error',lang('PASSWORD_UPDATE_SUCCESSFULLY'));
			
			 
		}
		
    }


    public function updateProfile() {
        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->input->post();
        
        //get logged user info
        $loggedUser = $this->User->getLoggedUser('freeshopify_admin');
        $account_id = $loggedUser['id'];

        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');     
        $this->form_validation->set_rules('mobile', 'Phone No.', 'required|xss_clean');     
        $this->form_validation->set_rules('email', 'Email', 'required|xss_clean|valid_email');
        if ($this->form_validation->run() == FALSE) {
            
            $this->Az->redirect('admin/profile/profile', 'system_message_error',lang('REQUIRED_ALL_FEILDS'));
        } 
        else {
            
            $data = array(
            'name' => $post['name'],
            'mobile' => $post['mobile'],
            'email' => $post['email'],
            'updated' => date('Y-m-d h:i:s')
            );
             
            $this->db->where('id', $account_id);
            $this->db->update('users',$data);   
            
            $this->Az->redirect('admin/profile/profile', 'system_message_error',lang('PROFILE_UPDATE_SUCCESSFULLY'));
            
             
        }
        
    }
	
	
	
	
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */