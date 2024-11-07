<?php 
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

class Employe extends CI_Controller {   



    public function __construct() 
    {
		parent::__construct();
		$this->User->checkPermission();
		$this->lang->load('superadmin/dashboard', 'english');
		$this->lang->load('front_common', 'english');
		
    }				

	public function employeList()
	{
		//get logged user info
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$list= $this->db->get_where('users',array('role_id'=>7))->result_array();

		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'list'  =>$list,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'employe/employeList'
		);
		$this->parser->parse('superadmin/layout/column-1' , $data);
	}


	public function addEmploye()
	{

		$account_id = SUPERADMIN_ACCOUNT_ID;

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

		$role = $this->db->get_where('superadmin_employe_role',array('status'=>1))->result_array();

		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'employe/addEmploye',
			'role' => $role,
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('superadmin/layout/column-1', $data);
	}



	public function saveEmploye()
	{

		//check for foem validation
		$post = $this->input->post();


		$this->load->library('form_validation');
		$this->form_validation->set_rules('role', 'Role', 'required');
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('mobile', 'Mobile ', 'required|xss_clean');
		$this->form_validation->set_rules('password', 'Password ', 'required|xss_clean');

		if ($this->form_validation->run() == FALSE) {

			$this->addEmploye();

		}

		else{

			 $account_id = SUPERADMIN_ACCOUNT_ID;

			 $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);	
				
			 $employe_id = EMPLOYE_DISPLAY_ID.rand(111111,999999);
			 
			 $data = array(

			  'role_id' => 7,
			  'employe_role' => $post['role'],
			  'account_id' => $account_id,
			  'user_code' => $employe_id,
			  'name'  => $post['name'],
			  'username' => $employe_id,
			  'password' => do_hash($post['password']),
			  'decode_password' => $post['password'],
			  'transaction_password' => do_hash($post['password']),
			  'decoded_transaction_password' => $post['password'], 	
			  'email' => $post['email'],
			  'mobile' => $post['mobile'],
			  'is_active' => $post['status'],
			  'is_verified' => 1,
			  'created' => date('Y-m-d H:i:s')

			 );	

			 $this->db->insert('users',$data);

			$this->Az->redirect('superadmin/employe/employeList', 'system_message_error',lang('ACCOUNT_SAVE_SUCCESS'));



		}



	}




	public function editEmploye($id)
	{    

		$account_id = SUPERADMIN_ACCOUNT_ID;

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		//get member list
		$list= $this->db->get_where('users',array('id'=>$id))->row_array();


		$accountID=$loggedUser['id'];   
		$siteUrl = site_url();

		$role = $this->db->get_where('superadmin_employe_role',array('status'=>1))->result_array();
		
		$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
			'site_url' => $siteUrl,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'employe/editEmploye',
			'manager_description' => lang('SITE_NAME'),
			'List' => $list,							           
			'accountID'=>$accountID,
			'id'=>$id,
			'role' => $role,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning()
		);
		$this->parser->parse('superadmin/layout/column-1', $data);

	}


	public function updateEmploye(){

		//check for foem validation
		$post = $this->input->post();


		$this->load->library('form_validation');
		$this->form_validation->set_rules('role', 'Role', 'required');
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('mobile', 'Mobile ', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE) {

			$this->editEmploye($post['id']);

		}

		else{

			 $account_id = SUPERADMIN_ACCOUNT_ID;

			 $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);	
				
			 
			 $data = array(
			  'employe_role' => $post['role'],	
			  'name'  => $post['name'],
			  'email' => $post['email'],
			  'mobile' => $post['mobile'],
			  'is_active' => $post['status'],
			  'is_verified' => 1,
			  'created' => date('Y-m-d H:i:s')

			 );

			 if($post['password']){

			  $data['password'] = do_hash($post['password']);
			  $data['decode_password'] = $post['password'];
			  $data['transaction_password'] = do_hash($post['password']);
			  $data['decoded_transaction_password'] = $post['password']; 	
			  
			 }	

			 $this->db->where('id',$post['id']);
			 $this->db->update('users',$data);

			$this->Az->redirect('superadmin/employe/employeList', 'system_message_error',lang('ACCOUNT_SAVE_SUCCESS'));



		}

	}


	public function deleteEmploye($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('users');    
		$this->Az->redirect('superadmin/employe/employeList', 'system_message_error',lang('ACCOUNT_DELETE_SUCCESS'));
	}





	public function roleList()
	{
		//get logged user info
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$list= $this->db->get('superadmin_employe_role')->result_array();

		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'list'  =>$list,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'employe/roleList'
		);
		$this->parser->parse('superadmin/layout/column-1' , $data);
	}


	public function addRole()
	{

		$account_id = SUPERADMIN_ACCOUNT_ID;

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

		// get mode list
		$menuList = $this->db->get('superadmin_menu')->result_array();
		if($menuList)
		{
			foreach($menuList as $key=>$list)
			{
				$subMenu = $this->db->order_by('title','ASC')->get_where('superadmin_sub_menu',array('menu_id'=>$list['id']))->result_array();
				$menuList[$key]['subMenu'] = $subMenu;
			}
		}

		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'employe/addRole',
			'menuList' => $menuList,
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('superadmin/layout/column-1', $data);
	}



	public function saveRole()
	{

		//check for foem validation
		$post = $this->input->post();


		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'required|xss_clean');

		if ($this->form_validation->run() == FALSE) {

			$this->addRole();

		}

		else{

			 $account_id = SUPERADMIN_ACCOUNT_ID;

			 $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);	
				
			$data = array(    
				'title'		  =>  $post['title'],      
				'status'      =>  $post['status'],
				'created'     =>  date('Y-m-d H:i:s'),
			);
			$this->db->insert('superadmin_employe_role',$data);
			$role_id = $this->db->insert_id();

			//save role permission
			if(isset($post['menu_id']))
			{
				foreach($post['menu_id'] as $menu_id)
				{
					$menuData = array(    
						'role_id'	=>  $role_id,      
						'menu_id'   =>  $menu_id,
					);
					$this->db->insert('superadmin_role_permission',$menuData);
				}
			}

			//save role permission
			if(isset($post['sub_menu_id']))
			{
				foreach($post['sub_menu_id'] as $menu_id)
				{
					$menuData = array(    
						'role_id'	=>  $role_id,      
						'sub_menu_id'   =>  $menu_id,
					);
					$this->db->insert('superadmin_role_permission',$menuData);
				}
			}

			$this->Az->redirect('superadmin/employe/roleList', 'system_message_error',lang('ACCOUNT_SAVE_SUCCESS'));

		}



	}




	public function editRole($id)
	{    

		$account_id = SUPERADMIN_ACCOUNT_ID;

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		//get member list
		$list= $this->db->get_where('users',array('id'=>$id))->row_array();


		$accountID=$loggedUser['id'];   
		$siteUrl = site_url();

		// get mode list
		$roleData = $this->db->get_where('superadmin_employe_role',array('id'=>$id))->row_array();

		// get role menu permission
		$roleMenuPermission = $this->db->get_where('superadmin_role_permission',array('role_id'=>$id))->result_array();

		$menu_array = array();
		$sub_menu_array = array();
		if($roleMenuPermission)
		{
			foreach($roleMenuPermission as $key=>$list)
			{
				if($list['menu_id'])
				{
					$menu_array[$key] = $list['menu_id'];
				}
				else
				{
					$sub_menu_array[$key] = $list['sub_menu_id'];
				}
			}
		}

		// get mode list
		$menuList = $this->db->get('superadmin_menu')->result_array();
		if($menuList)
		{
			foreach($menuList as $key=>$list)
			{
				$subMenu = $this->db->order_by('title','ASC')->get_where('superadmin_sub_menu',array('menu_id'=>$list['id']))->result_array();
				$menuList[$key]['subMenu'] = $subMenu;
			}
		}
		
		$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
			'site_url' => $siteUrl,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'employe/editRole',
			'manager_description' => lang('SITE_NAME'),
			'List' => $list,							           
			'accountID'=>$accountID,
			'id'=>$id,
			'roleData' => $roleData,
			'menu_array' => $menu_array,
			'sub_menu_array' => $sub_menu_array,
			'menuList' => $menuList,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning()
		);
		$this->parser->parse('superadmin/layout/column-1', $data);

	}


	public function updateRole(){

		//check for foem validation
		$post = $this->input->post();


		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'required');
		
		if ($this->form_validation->run() == FALSE) {

			$this->editRole($post['id']);

		}

		else{

			$account_id = SUPERADMIN_ACCOUNT_ID;

			$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);	
			
			$catID = $post['id'];	
			 
			$data = array(    
			'title'			  =>  $post['title'],      
			'status'          =>  $post['status'],
			);

			$this->db->where('id',$catID);
			$this->db->update('superadmin_employe_role',$data);
			$role_id = $catID;

			// delete old permission
			$this->db->where('role_id',$catID);
			$this->db->delete('superadmin_role_permission');
			//save role permission
			if(isset($post['menu_id']))
			{
				foreach($post['menu_id'] as $menu_id)
				{
					$menuData = array(    
						'role_id'	=>  $role_id,      
						'menu_id'   =>  $menu_id,
					);
					$this->db->insert('superadmin_role_permission',$menuData);
				}
			}

			//save role permission
			if(isset($post['sub_menu_id']))
			{
				foreach($post['sub_menu_id'] as $menu_id)
				{
					$menuData = array(    
						'role_id'	=>  $role_id,      
						'sub_menu_id'   =>  $menu_id,
					);
					$this->db->insert('superadmin_role_permission',$menuData);
				}
			}

			$this->Az->redirect('superadmin/employe/roleList', 'system_message_error',lang('ACCOUNT_SAVE_SUCCESS'));



		}

	}


	public function deleteRole($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('superadmin_employe_role');

		$this->db->where('role_id',$id);
		$this->db->delete('superadmin_role_permission');

		$this->Az->redirect('superadmin/employe/roleList', 'system_message_error',lang('ACCOUNT_DELETE_SUCCESS'));
	}





}



?>