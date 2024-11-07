<?php 
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

class Link extends CI_Controller {   



    public function __construct() 
    {
		parent::__construct();
		$this->User->checkEmployePermission();
		$this->lang->load('employe/dashboard', 'english');
		$this->lang->load('front_common', 'english');
		
    }				

	public function index()
	{
		$domain_account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$list= $this->db->get_where('custom_link',array('account_id'=>$domain_account_id))->result_array();

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
			'content_block' => 'link/list'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}


	public function add()
	{
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'link/add',
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('employe/layout/column-1', $data);
	}




	public function save()
	{

		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());


		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('url', 'Url ', 'required');
		
		if ($this->form_validation->run() == FALSE) {

			$this->add();

		}

		else{

			$domain_account_id = $this->User->get_domain_account();
			$data = array(
			 'account_id' => $domain_account_id,
			 'title'  => $post['title'],
			 'url'    => $post['url'],
			 'status' => $post['status'],	
			);

			$this->db->insert('custom_link',$data);
			$this->Az->redirect('employe/link', 'system_message_error',lang('LINK_SAVE_SUCCESS'));

		}



	}




	public function edit($id = 0)
	{
		$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
    	
		// check member
		$chkMember = $this->db->get_where('custom_link',array('id'=>$id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('employe/link', 'system_message_error',lang('MEMBER_ERROR'));
		}

		$data = $this->db->get_where('custom_link',array('id'=>$id))->row_array();

		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'link/edit',
			'id' => $id,
			'data' => $data,
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('employe/layout/column-1', $data);
	}




	public function update()
	{
		$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$id = $post['id'];
		// check member
		$chkMember = $this->db->get_where('custom_link',array('id'=>$id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('employe/link', 'system_message_error',lang('MEMBER_ERROR'));
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('url', 'Url ', 'required');
		
		if ($this->form_validation->run() == FALSE) {

			$this->edit($post['id']);

		}

		else{

			
			$data = array(

			 'title'  => $post['title'],
			 'url'    => $post['url'],
			 'status' => $post['status'],	
			);

			$this->db->where('id',$post['id']);
			$this->db->update('custom_link',$data);
			$this->Az->redirect('employe/link', 'system_message_error',lang('LINK_SAVE_SUCCESS'));

		}



	}



	public function deleteLink($id)
	{
		$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
    	
		// check member
		$chkMember = $this->db->get_where('custom_link',array('id'=>$id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('employe/link', 'system_message_error',lang('MEMBER_ERROR'));
		}
		$this->db->where('id',$id);
		$this->db->delete('custom_link');    
		$this->Az->redirect('employe/link', 'system_message_error',lang('LINK_DELETE_SUCCESS'));
	}


}



?>