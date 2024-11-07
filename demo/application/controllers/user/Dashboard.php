<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Dashboard extends CI_Controller{

    public function __construct() {
        parent::__construct();


        //load language
		$this->User->checkUserPermission();

        $this->load->model('admin/Master_model');       
		$this->lang->load('admin/dashboard', 'english');
        $this->lang->load('front_common' , 'english');

    }
	
	public function index($uname_prefix = '' , $username = ''){


		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        
		$account_id = $loggedUser['id'];
        
		$siteUrl = base_url();

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'dashboard'
        );
        
        $this->parser->parse('user/layout/column-1' , $data);
    }
	
	public function edit_admin($id = '') {
		$this->load->library('template');
		
        //verify id is avaialabel or not
		$verify_admin = $this->db->select('*')
                        ->where('id', $id)
                        ->get('user_det')->row_array();
		
        if (!$verify_admin) {
            $this->Az->redirect('user/dashboard/index', 'system_message_error', lang('CANOT_EDIT_ADMIN'));
        }

		$siteUrl = site_url();

        //get logged user info
        $loggedUser = $this->User->getLoggedUser('marwarcare_admin');

		$data = array(
            'site_url' => $siteUrl,
            'meta_title' => 'Edit User',
            'meta_keywords' => 'Edit User',
            'meta_description' => 'Edit User',
            'content_block' => 'edit_admin',
            'page_title' => 'Edit User',
            'manager_description' => 'Create User',
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'pagination' => $this->pagination->create_links(),
            'admin_info' => $verify_admin,
			'loggedUser' => $loggedUser,
            'title' => 'Edit User'
        );
        $this->parser->parse('user/layout/column-1', $data);
    }
	
	public function chkoldpw() {
   
        //chekchk eneterd old pw is correct or not       
        if ($_POST['opw']) {
            $chk = $this->db->select('password')
                            ->where('password', do_hash($_POST['opw']))
                            ->get('user_det')->row_array();

            if (!$chk) {
                echo 'Please enter correct password';
            } else {
                echo 'Password matched';
            }
        }
    }
	
	public function update_admin() {
        
		$this->load->library('template');
        $siteUrl = site_url();
		$post = $this->input->post();
		
        //get logged user info
        $loggedUser = $this->User->getLoggedUser('marwarcare_admin');

        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('opw', 'Old Password', 'required|xss_clean');		
        

        if ($this->form_validation->run() == FALSE) {
			
			$this->edit_admin($post['admin_id']);
        } 
		else {
			
			$this->Login_model->updateUser($post);
			
			$this->Az->redirect('user/Dashboard/', 'system_message_error',lang('USER_UPDATE_SUCCESSFULLY'));
			
			 
		}
		
    }
	
	public function logOut() {
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $this->User->saveMemberLiveStatus($loggedAccountID, 0);
        
        $this->session->sess_destroy();
        $this->session->unset_userdata(USER_SESSION_ID);
        $this->Az->redirect('login', 'system_message_error', lang('LOGOUT_SUCCESS'));  
    }

    
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */