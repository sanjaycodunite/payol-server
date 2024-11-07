<?php 
class System extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkAdminPermission();
        
        
    }

    public function logList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$siteUrl = base_url();	
		$logData = '';
		$fileName = ACCOUNT_LOG_PATH.'Account-'.$account_id.'-'.date('M').'-'.date('Y').'.php';	
		if(file_exists($fileName))
		{
			$logData = str_replace(']',']<br /><div class="logBreak"><hr /></div>',file_get_contents($fileName));
		}


		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'logData' => $logData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'system/logList'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function callBackLogList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$siteUrl = base_url();	
		$logData = '';
		$fileName = ACCOUNT_LOG_PATH.'Callback-Account-'.$account_id.'-'.date('M').'-'.date('Y').'.php';	
		if(file_exists($fileName))
		{
			$logData = str_replace(']',']<br /><div class="logBreak"><hr /></div>',file_get_contents($fileName));
		}


		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'logData' => $logData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'system/callBackLogList'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function aepsApiLogList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$siteUrl = base_url();	
		$logData = '';
		$fileName = ACCOUNT_LOG_PATH.'Aeps-'.$account_id.'-'.date('M').'-'.date('Y').'.php';	
		if(file_exists($fileName))
		{
			$logData = str_replace('[break]','<br /><div class="logBreak"><hr /></div>',file_get_contents($fileName));
		}


		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'logData' => $logData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'system/aepsApiLogList'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function settlementLogList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$siteUrl = base_url();	
		$logData = '';
		$fileName = ACCOUNT_LOG_PATH.'Settlement-'.$account_id.'-'.date('M').'-'.date('Y').'.php';	
		if(file_exists($fileName))
		{
			$logData = str_replace('[break]','<br /><div class="logBreak"><hr /></div>',file_get_contents($fileName));
		}


		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'logData' => $logData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'system/settlementLogList'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	
}