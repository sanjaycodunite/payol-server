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
		$this->User->checkEmployePermission();
		$this->load->model('employe/Setting_model');
        $this->lang->load('employe/setting', 'english');
        $this->lang->load('front_common' , 'english');
    }


    public function profile($uname_prefix = '' , $username = ''){
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        // get user data
        $userData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();

        // get account data
        $accountData = $this->db->get_where('account',array('id'=>$account_id))->row_array();

        // get bank list
        $bankList = $this->db->get('aeps_bank_list')->result_array();
        
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
            'bankList' => $bankList,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'setting/profile'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    }


	
	


    public function profileAuth() {
        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->security->xss_clean($this->input->post());
        
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');     
        $this->form_validation->set_rules('firm_name', 'Firm Name', 'required|xss_clean');
        $this->form_validation->set_rules('mobile', 'Phone No.', 'required|xss_clean');     
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required');
        $this->form_validation->set_rules('bankID', 'Bank', 'required');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required');
        
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
                    $this->Az->redirect('employe/setting/profile', 'system_message_error',$uploadError);
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
              'account_holder_name'=>$post['account_holder_name'],
              'bankID'=>$post['bankID'],
              'account_number'=>$post['account_number'],
              'ifsc'=>$post['ifsc'],
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
            
            $this->Az->redirect('employe/setting/profile', 'system_message_error',lang('PROFILE_UPDATE_SUCCESSFULLY'));
            
             
        }
        
    }
	
    public function changePassword(){
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
        $this->parser->parse('employe/layout/column-1' , $data);
    }
	
	public function passwordAuth() {
        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->security->xss_clean($this->input->post());
        
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
                $this->Az->redirect('employe/setting/changePassword', 'system_message_error',lang('OLD_PASSWORD_FAILED'));   
            }

            $this->Setting_model->updateAdminPassword($post);
            
            $this->Az->redirect('employe/setting/changePassword', 'system_message_error',lang('PASSWORD_UPDATE_SUCCESSFULLY'));
            
             
        }
        
    }


    public function changeTheme(){
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        $accountData = $this->User->get_account_data($account_id);

        $themeData = $this->db->get_where('theme',array('account_id'=>$account_id))->row_array();

        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,
            'page_title' => 'Theme Setting',
            'themeData'  => $themeData,
            'accountData' => $accountData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'setting/change-theme'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    }


    public function updateTheme($id) {
        
        $siteUrl = site_url();
        
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        $chk_theme = $this->db->get_where('theme',array('account_id'=>$account_id))->num_rows();

        $data = array(
         'theme_id' => $id,   
        );

        if($chk_theme){

          $this->db->where('account_id',$account_id);
          $this->db->update('theme',$data);  

        }
        else{

            $data['account_id'] = $account_id;

            $this->db->insert('theme',$data);
        }

        $this->Az->redirect('employe/setting/changeTheme', 'system_message_error',lang('THEME_UPDATE_SUCCESS'));
        
    }



    public function updatePanelTheme($id) {
        
        $siteUrl = site_url();
        
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        $this->db->where('id',$account_id);
        $this->db->update('account',array('panel_theme_id'=>$id));
        
        $this->Az->redirect('employe/setting/changeTheme', 'system_message_error',lang('THEME_UPDATE_SUCCESS'));
        
    }


    //change tpin


    public function changeTranscationPassword(){
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
        $this->parser->parse('employe/layout/column-1' , $data);
    }
    
    public function transcationPasswordAuth() {
        
        $this->load->library('template');
        $siteUrl = site_url();
        $post = $this->security->xss_clean($this->input->post());
        
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
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
                $this->Az->redirect('employe/setting/changeTranscationPassword', 'system_message_error',lang('OLD_PASSWORD_FAILED'));   
            }

            $this->Setting_model->updateAdminTranscationPassword($post);
            
            $this->Az->redirect('employe/setting/changeTranscationPassword', 'system_message_error',lang('PASSWORD_UPDATE_SUCCESSFULLY'));
            
             
        }
        
    }

	
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */