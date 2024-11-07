<?php 
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

class Account extends CI_Controller {   



    public function __construct() 
    {
		parent::__construct();
		$this->User->checkPermission();
		$this->load->model('superadmin/Account_model');	
		$this->lang->load('superadmin/dashboard', 'english');
		$this->lang->load('front_common', 'english');
		
    }				

	public function accountList()
	{
		//get logged user info
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$list= $this->db->select('account.*,users.username,users.decode_password,users.user_code,account_type.title as account_type_title,package.package_name')->order_by('account.created','desc')->join('users','users.account_id = account.id')->join('account_type','account_type.id = account.account_type')->join('package','package.id = account.package_id','left')->get_where('account',array('users.role_id'=>2))->result_array();

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
			'content_block' => 'account/accountList'
		);
		$this->parser->parse('superadmin/layout/column-1' , $data);
	}

	public function requestList()
	{
		//get logged user info
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$list= $this->db->select('account_request.*,account.name as account_name')->order_by('account_request.created','desc')->join('account','account.id = account_request.account_id')->get('account_request')->result_array();

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
			'content_block' => 'account/requestList'
		);
		$this->parser->parse('superadmin/layout/column-1' , $data);
	}


	public function addAccount()
	{

		$account_id = SUPERADMIN_ACCOUNT_ID;

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

		// get services list
		$serviceList = $this->db->get('services')->result_array();

		// get account type list
		$accountTypeList = $this->db->get('account_type')->result_array();

		// get geteway list
		$gatewayList  = $this->db->get('payment_gateway_type')->result_array();

		// get package list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id))->result_array();

		// get custom api permission
		$customApiList = $this->db->get('account_custom_api')->result_array();

		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'account/addAccount',
			'serviceList' => $serviceList,
			'gatewayList' => $gatewayList,
			'accountTypeList' => $accountTypeList,
			'packageList' => $packageList,
			'customApiList' => $customApiList,
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('superadmin/layout/column-1', $data);
	}



	public function saveAccount()
	{

		//check for foem validation
		$post = $this->input->post();


		$this->load->library('form_validation');
		$this->form_validation->set_rules('account_type', 'Account Type', 'required');
		$this->form_validation->set_rules('package_id', 'Package', 'required');
		$this->form_validation->set_rules('domain_name', 'Domain Name', 'required');
		$this->form_validation->set_rules('domain_url', 'Domain Url ', 'required|xss_clean');
		$this->form_validation->set_rules('account_code', 'Prefix ', 'required|xss_clean');
		$this->form_validation->set_rules('name', 'Name ', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email ', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile ', 'required|xss_clean');
		$this->form_validation->set_rules('username', 'Username ', 'required|xss_clean');
		$this->form_validation->set_rules('web_theme', 'Website Theme ', 'required');
		$this->form_validation->set_rules('password', 'Password ', 'required|xss_clean');

		if ($this->form_validation->run() == FALSE) {

			$this->addAccount();

		}

		else{

			$chk_mobile_email =$this->db->query("SELECT * FROM tbl_users WHERE username = '$post[username]' AND role_id = 2")->num_rows();
            
			if($chk_mobile_email){

				$this->Az->redirect('superadmin/account/addAccount', 'system_message_error',lang('EMAIL_MOBILE_ERROR'));	

			}

			// check mobile already exits or not
			$chk_user_mobile = $this->db->get_where('users',array('mobile'=>$post['mobile'],'role_id'=>2))->num_rows();
			if($chk_user_mobile){

				$this->Az->redirect('superadmin/account/addAccount', 'system_message_error',lang('MOBILE_ERROR'));	

			}

			$filePath = '';
			if($_FILES['profile']['name'])
			{
				//generate icon name randomly
				$fileName = time().rand(1111,9999);
				$config['upload_path'] = './media/account/';
				$config['allowed_types'] = 'gif|jpeg|JPEG|JPG|PNG|jpg|png';
				$config['file_name'] 		= $fileName;

				$this->load->library('upload', $config);
				$this->upload->do_upload('profile');
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('superadmin/account/addAccount', 'system_message_error',$uploadError);
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$filePath = substr($config['upload_path'] . $fileData['file_name'], 2);

				}

			}

			$this->Account_model->save_account($post,$filePath);
			$this->Az->redirect('superadmin/account/accountList', 'system_message_error',lang('ACCOUNT_SAVE_SUCCESS'));



		}



	}




	public function editAccount($id)
	{    

		$account_id = SUPERADMIN_ACCOUNT_ID;

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		//get member list
		$list= $this->db->select('account.*,users.username,users.decode_password')->order_by('account.created','desc')->join('users','users.account_id = account.id')->get_where('account',array('users.role_id'=>2,'account.id'=>$id))->row_array();



		$accountID=$loggedUser['id'];   
		$siteUrl = site_url();

		// get services list
		$serviceList = $this->db->get('services')->result_array();

		// get account type list
		$accountTypeList = $this->db->get('account_type')->result_array();

		$accountServiceList = $this->db->get_where('account_services',array('account_id'=>$id,'status'=>1))->result_array();
		$accountServiceID = array();
		if($accountServiceList)
		{
			foreach($accountServiceList as $key=>$listt)
			{
				$accountServiceID[$key] = $listt['service_id'];
			}
		}

		// get geteway list
		$gatewayList  = $this->db->get('payment_gateway_type')->result_array();
		if($gatewayList)
		{
			foreach($gatewayList as $key=>$glist)
			{
				$accountGatewayList = $this->db->get_where('account_payment_gateway',array('account_id'=>$id,'gateway_id'=>$glist['id']))->row_array();
				$gatewayList[$key]['status'] = isset($accountGatewayList['status']) ? $accountGatewayList['status'] : 0 ;
				$gatewayList[$key]['gateway_key'] = isset($accountGatewayList['gateway_key']) ? $accountGatewayList['gateway_key'] : '' ;
				$gatewayList[$key]['gateway_secret'] = isset($accountGatewayList['gateway_secret']) ? $accountGatewayList['gateway_secret'] : '' ;
			}
		}

		// get package list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id))->result_array();

		// get custom api permission
		$customApiList = $this->db->get('account_custom_api')->result_array();

		$accountCustomApiList = $this->db->get_where('account_custom_api_permission',array('account_id'=>$id,'status'=>1))->result_array();
		$accountCustomApiID = array();
		if($accountCustomApiList)
		{
			foreach($accountCustomApiList as $key=>$listt)
			{
				$accountCustomApiID[$key] = $listt['api_id'];
			}
		}


		$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
			'site_url' => $siteUrl,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'account/editAccount',
			'manager_description' => lang('SITE_NAME'),
			'List' => $list,							           
			'accountID'=>$accountID,
			'id'=>$id,
			'serviceList' => $serviceList,
			'accountServiceID' => $accountServiceID,
			'gatewayList' => $gatewayList,
			'accountTypeList' => $accountTypeList,
			'packageList' => $packageList,
			'customApiList' => $customApiList,
			'accountCustomApiID' => $accountCustomApiID,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning()
		);
		$this->parser->parse('superadmin/layout/column-1', $data);

	}


	public function updateAccount(){

		$post = $this->input->post();
		$courseID = $post['id'];
		$this->load->library('form_validation');
		$this->form_validation->set_rules('account_type', 'Account Type', 'required');
		$this->form_validation->set_rules('package_id', 'Package', 'required');
		$this->form_validation->set_rules('domain_name', 'Domain Name', 'required');
		$this->form_validation->set_rules('domain_url', 'Domain Url ', 'required|xss_clean');
		$this->form_validation->set_rules('account_code', 'Prefix ', 'required|xss_clean');
		$this->form_validation->set_rules('name', 'Name ', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email ', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile ', 'required|xss_clean');
		$this->form_validation->set_rules('web_theme', 'Website Theme ', 'required');
		
		if ($this->form_validation->run() == FALSE) {

			$this->editAccount($courseID);

		}

		else{

			$chk_mobile_email =$this->db->query("SELECT * FROM tbl_users WHERE username = '$post[username]' AND role_id = 2 AND account_id != '$courseID'")->num_rows();
            
			if($chk_mobile_email){

				$this->Az->redirect('superadmin/account/editAccount/'.$courseID, 'system_message_error',lang('EMAIL_MOBILE_ERROR'));	

			}

			$chk_mobile =$this->db->query("SELECT * FROM tbl_users WHERE mobile = '$post[mobile]' AND role_id = 2 AND account_id != '$courseID'")->num_rows();
            
			if($chk_mobile){

				$this->Az->redirect('superadmin/account/editAccount/'.$courseID, 'system_message_error',lang('MOBILE_ERROR'));	

			}
			

			$filePath = '';
			if($_FILES['profile']['name'])
			{
				//generate icon name randomly
				$fileName = rand(1111,999999999);
				$config['upload_path'] = './media/account/';
				$config['allowed_types'] = 'gif|jpeg|JPEG|JPG|PNG|jpg|png';
				$config['file_name'] 		= $fileName;

				$this->load->library('upload', $config);
				$this->upload->do_upload('profile');
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('superadmin/account/editAccount/'.$courseID, 'system_message_error',$uploadError);
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$filePath = substr($config['upload_path'] . $fileData['file_name'], 2);

				}

			}
			// update organizer detail
			$this->Account_model->updateAccount($post,$filePath,$courseID);
			$this->Az->redirect('superadmin/account/accountList', 'system_message_error',lang('ACCOUNT_UPDATE_SUCCESS'));
		}

	}


	public function deleteAccount($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('account');    
		$this->Az->redirect('superadmin/account/accountList', 'system_message_error',lang('ACCOUNT_DELETE_SUCCESS'));
	}


}



?>