<?php 
class Package extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       $this->User->checkEmployePermission();
        $this->lang->load('employe/package', 'english');
        
        
    }

	
	public function index()
    {

    	if(!$this->User->admin_menu_permission(8,1) || !$this->User->admin_menu_permission(61,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $packageList = $this->db->get_where('package',array('account_id'=>$account_id,'created_by'=>$loggedUser['id']))->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'package/packageList',
            'packageList'   => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }



    // add member
	public function addPackage()
    {

    	if(!$this->User->admin_menu_permission(8,1) || !$this->User->admin_menu_permission(58,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);


		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'package/addPackage',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function savePackage()
	{

		if(!$this->User->admin_menu_permission(8,1) || !$this->User->admin_menu_permission(58,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');

		$this->form_validation->set_rules('package_name', 'Package Name', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->addPackage();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        	
        	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
			
			$data = array(
			 'account_id' => $account_id,
			 'created_by'    => $loggedUser['id'],
 			 'package_name'  => $post['package_name'],
 			 'status'    => $post['status'],
 			 'is_default'    => isset($post['is_default']) ? 1 : 0,
 			 'created'  => date('Y-m-d h:i:s')
			);
			
			$status = $this->db->insert('package',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/package', 'system_message_error',lang('SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/package', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}

	// edit employe
	public function editPackage($id)
    {    

    	if(!$this->User->admin_menu_permission(8,1) || !$this->User->admin_menu_permission(59,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


    	$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$packageData = $this->db->get_where('package',array('account_id'=>$account_id,'created_by'=>$loggedUser['id'],'id'=>$id))->row_array();

		$siteUrl = site_url();
    	$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'package/editPackage',
            'manager_description' => lang('SITE_NAME'),
			'packageData'=>$packageData,
			'id'=>$id,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    //update member
	public function updatePackage()
	{

		if(!$this->User->admin_menu_permission(8,1) || !$this->User->admin_menu_permission(59,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$id = $post['id'];
		$account_id = $this->User->get_domain_account();
    	
    	$this->load->library('form_validation');
		$this->form_validation->set_rules('package_name', 'Package Name', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->editPackage($post['id']);
		}
		else
		{	
			$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
			
			$data = array(

			 'package_name' => $post['package_name'],
			 'status'=> $post['status'],
			 'is_default'    => isset($post['is_default']) ? 1 : 0,
			);

			$this->db->where('account_id',$account_id);
			$this->db->where('created_by',$loggedUser['id']);
			$this->db->where('id',$id);
			$status = $this->db->update('package',$data);
			
			if($status == true)
			{
				$this->Az->redirect('employe/package', 'system_message_error',lang('UPDATE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/package', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}
	
	
	//delete member
	public function deletePackage($id)
	{	

		if(!$this->User->admin_menu_permission(8,1) || !$this->User->admin_menu_permission(60,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$this->db->where('account_id',$account_id);
		$this->db->where('created_by',$loggedUser['id']);
		$this->db->where('id',$id);
		$this->db->delete('package');
		
		$this->Az->redirect('employe/package', 'system_message_error',lang('DELETE_SUCCESS'));
	}
	
}