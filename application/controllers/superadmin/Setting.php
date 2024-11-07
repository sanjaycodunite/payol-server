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
		$this->User->checkPermission();
		$this->load->model('superadmin/Setting_model');
        $this->lang->load('superadmin/setting', 'english');
        $this->lang->load('front_common' , 'english');
    }


    public function profile($uname_prefix = '' , $username = ''){
        //get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        // get user data
        $userData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();

        // get account data
        $accountData = $this->db->get_where('account',array('id'=>$account_id))->row_array();
        
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
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'setting/profile'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    }


	
	


    public function profileAuth() {
        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->input->post();
        
        //get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');     
        $this->form_validation->set_rules('firm_name', 'Firm Name', 'required|xss_clean');
        $this->form_validation->set_rules('mobile', 'Phone No.', 'required|xss_clean');     
        
        if ($this->form_validation->run() == FALSE) {
            
            $this->profile();
        } 
        else {

            $filePath = '';
            if($_FILES['profile']['name'])
            {
                //generate icon name randomly
                $fileName = rand(1111,999999999);
                $config['upload_path'] = './media/account/';
                $config['allowed_types'] = 'gif|jpeg|JPEG|JPG|PNG|jpg|png';
                $config['file_name']        = $fileName;

                $this->load->library('upload', $config);
                $this->upload->do_upload('profile');
                $uploadError = $this->upload->display_errors();
                if($uploadError){
                    $this->Az->redirect('superadmin/setting/profile', 'system_message_error',$uploadError);
                }
                else
                {
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $filePath = substr($config['upload_path'] . $fileData['file_name'], 2);

                }

            }
            
            $data = array(
              'title'   =>$post['firm_name'],
              'email'=>strtolower($post['email']),
              'name'=>$post['name'],
              'mobile'=>$post['mobile'],
              'updated' => date('Y-m-d H:i:s')
            );
            if($filePath)
            {
              $data['image_path'] = $filePath;
            }
            $this->db->where('id',$account_id);
            $this->db->update('account',$data);
            
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
            
            $this->Az->redirect('superadmin/setting/profile', 'system_message_error',lang('PROFILE_UPDATE_SUCCESSFULLY'));
            
             
        }
        
    }
	
    public function changePassword(){
        //get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    }
	
	public function passwordAuth() {
        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->input->post();
        
        //get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
            $chk_old_pwd = $this->db->get_where('users',array('id'=>$loggedAccountID,'role_id'=>1,'password'=>do_hash($post['opw'])))->num_rows();
            if(!$chk_old_pwd)
            {
                $this->Az->redirect('superadmin/setting/changePassword', 'system_message_error',lang('OLD_PASSWORD_FAILED'));   
            }

            $this->Setting_model->updateAdminPassword($post);
            
            $this->Az->redirect('superadmin/setting/changePassword', 'system_message_error',lang('PASSWORD_UPDATE_SUCCESSFULLY'));
            
             
        }
        
    }


   
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */