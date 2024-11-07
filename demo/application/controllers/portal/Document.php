<?php 
class Document extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkApiMemberPermission();
        $this->load->model('portal/Master_model');		
        $this->lang->load('portal/master', 'english');
        
    }

	
	public function recharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$operatorList = $this->db->get('operator')->result_array();
        $circle = $this->db->get('circle')->result_array(); 

        // get call back url
        $get_call_back = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();
        $call_back_url = isset($get_call_back['call_back_url']) ? $get_call_back['call_back_url'] : '';
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'document/recharge',
            'operatorList' => $operatorList,
            'circle' => $circle,
            'call_back_url' => $call_back_url,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('portal/layout/column-1', $data);	




	}

	public function moneyTransfer(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(20, $activeService)){
			$this->Az->redirect('portal/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		// get call back url
        $get_call_back = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();
        $call_back_url = isset($get_call_back['call_back_url']) ? $get_call_back['call_back_url'] : '';
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'document/moneyTransfer',
            'call_back_url' => $call_back_url,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('portal/layout/column-1', $data);	




	}

	public function callbackAuth()
	{
		$post = $this->input->post();
		$call_back_url = isset($post['call_back_url']) ? $post['call_back_url'] : '';
		if($call_back_url)
		{
			$account_id = $this->User->get_domain_account();
	    	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	    	$loggedAccountID = $loggedUser['id'];

	    	$this->db->where('id',$loggedAccountID);
	    	$this->db->where('account_id',$account_id);
	    	$this->db->update('users',array('call_back_url'=>$call_back_url));
		}
		$this->Az->redirect('portal/document/moneyTransfer', 'system_message_error',lang('CALLBACK_SAVE_SUCCESS'));
	}

	public function dmtCallbackAuth()
	{
		$post = $this->input->post();
		$call_back_url = isset($post['call_back_url']) ? $post['call_back_url'] : '';
		if($call_back_url)
		{
			$account_id = $this->User->get_domain_account();
	    	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	    	$loggedAccountID = $loggedUser['id'];

	    	$this->db->where('id',$loggedAccountID);
	    	$this->db->where('account_id',$account_id);
	    	$this->db->update('users',array('dmt_call_back_url'=>$call_back_url));
		}
		$this->Az->redirect('portal/document/moneyTransfer', 'system_message_error',lang('CALLBACK_SAVE_SUCCESS'));
	}

	public function aeps(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(3, $activeService)){
			$this->Az->redirect('portal/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'document/aeps',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('portal/layout/column-1', $data);	




	}

	public function upiQr(){

		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	// get call back url
        $get_call_back = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();
        $call_back_url = isset($get_call_back['upi_call_back_url']) ? $get_call_back['upi_call_back_url'] : '';

		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'document/upiQr',
            'manager_description' => lang('SITE_NAME'),
            'call_back_url' => $call_back_url,
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('portal/layout/column-1', $data);	




	}

	public function upiCallbackAuth()
	{
		$post = $this->input->post();
		$call_back_url = isset($post['call_back_url']) ? $post['call_back_url'] : '';
		if($call_back_url)
		{
			$account_id = $this->User->get_domain_account();
	    	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	    	$loggedAccountID = $loggedUser['id'];

	    	$this->db->where('id',$loggedAccountID);
	    	$this->db->where('account_id',$account_id);
	    	$this->db->update('users',array('upi_call_back_url'=>$call_back_url));
		}
		$this->Az->redirect('portal/document/upiQr', 'system_message_error',lang('CALLBACK_SAVE_SUCCESS'));
	}

	
}