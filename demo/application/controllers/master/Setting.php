<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Setting extends CI_Controller{

    public function __construct() {
        parent::__construct();
        //load language
		$this->User->checkMasterPermission();
		$this->load->model('master/Setting_model');
        $this->lang->load('master/setting', 'english');
        $this->lang->load('front_common' , 'english');
    }


    public function profile($uname_prefix = '' , $username = ''){
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        // get user data
        $userData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();

        
        
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,
            'page_title' => 'Profile',
            'userData' => $userData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'setting/profile'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    }


	
	


    public function profileAuth() {
        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->input->post();
        
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');     
        $this->form_validation->set_rules('mobile', 'Phone No.', 'required|xss_clean');     
        
        if ($this->form_validation->run() == FALSE) {
            
            $this->profile();
        } 
        else {

            // save user credentials
            $userData = array(
              'email'=>strtolower($post['email']),
              'name'=>$post['name'],
              'mobile'=>$post['mobile'],
              'updated' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',$loggedAccountID);
            $this->db->where('account_id',$account_id);
            $this->db->update('users',$userData);  
            
            $this->Az->redirect('master/setting/profile', 'system_message_error',lang('PROFILE_UPDATE_SUCCESSFULLY'));
            
             
        }
        
    }
	
    public function changePassword(){
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        
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
            'content_block' => 'setting/change-password'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    }
	
	public function passwordAuth() {
        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->input->post();
        
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('opw', 'Old Password', 'required|xss_clean|min_length[4]');     
        $this->form_validation->set_rules('npw', 'New Password', 'required|xss_clean|min_length[4]');     
        $this->form_validation->set_rules('cpw', 'Confirm New Password', 'required|xss_clean|matches[npw]');     
        if ($this->form_validation->run() == FALSE) {
            
            $this->changePassword();
            
        } 
        else {
            
            // check old password valid or not
            $chk_old_pwd = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id,'password'=>do_hash($post['opw'])))->num_rows();
            if(!$chk_old_pwd)
            {
                $this->Az->redirect('master/setting/changePassword', 'system_message_error',lang('OLD_PASSWORD_FAILED'));   
            }

            $this->Setting_model->updateAdminPassword($post);
            
            $this->Az->redirect('master/setting/changePassword', 'system_message_error',lang('PASSWORD_UPDATE_SUCCESSFULLY'));
            
             
        }
        
    }


     public function changeTranscationPassword(){
        //get logged user info
          $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
          $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        
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
            'content_block' => 'setting/change-transcation-password'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    }
    
    public function transcationPasswordAuth() {
        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->security->xss_clean($this->input->post());
        
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('opw', 'Old Password', 'required|xss_clean|min_length[4]');     
        $this->form_validation->set_rules('npw', 'New Password', 'required|xss_clean|min_length[4]');     
        $this->form_validation->set_rules('cpw', 'Confirm New Password', 'required|xss_clean|matches[npw]');     
        if ($this->form_validation->run() == FALSE) {
            
            $this->changeTranscationPassword();
            
        } 
        else {
            
            // check old password valid or not
            $chk_old_pwd = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id,'transaction_password'=>do_hash($post['opw'])))->num_rows();
            if(!$chk_old_pwd)
            {
                $this->Az->redirect('master/setting/changeTranscationPassword', 'system_message_error',lang('OLD_PASSWORD_FAILED'));   
            }

            $this->Setting_model->updateAdminTranscationPassword($post);
            
            $this->Az->redirect('master/setting/changeTranscationPassword', 'system_message_error',lang('PASSWORD_UPDATE_SUCCESSFULLY'));
            
             
        }
        
    }
	
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */