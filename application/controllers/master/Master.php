<?php 
class Master extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
        $this->load->model('master/Master_model');		
        $this->lang->load('master/master', 'english');
        
    }

	public function myCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		
		$operatorList = $this->db->get_where('operator',array('type !='=>'Electricity'))->result_array();

		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				// get commission
				$get_com_data = $this->db->get_where('recharge_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id,'op_id'=>$list['id']))->row_array();
				$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
				$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
				$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;
			}
		}


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/my-commission',
            'operatorList'	=> $operatorList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);	




	}

	public function myBbpsCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$operatorList = $this->db->get_where('operator',array('type'=>'Electricity'))->result_array();

		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				// get commission
				$get_com_data = $this->db->get_where('recharge_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id,'op_id'=>$list['id']))->row_array();
				$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
				$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
				$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;
			}
		}


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/my-bbpsCommission',
            'operatorList'	=> $operatorList,
            'userList' => $userList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);	




	}

	public function myBbpsLiveCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$operatorList = $this->db->get_where('bbps_service')->result_array();

		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				// get commission
				$get_com_data = $this->db->get_where('bbps_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id,'service_id'=>$list['id']))->row_array();
				$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
				$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
				$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;
			}
		}


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/my-bbpsLiveCommission',
            'operatorList'	=> $operatorList,
            'userList' => $userList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);	




	}

	public function myTransferCommision(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$recordList = $this->db->get_where('dmr_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'recordList' => $recordList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/my-transfer-commision'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function myMoneyTransferCommision(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$recordList = $this->db->get_where('money_transfer_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'recordList' => $recordList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/my-money-transfer-commision'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function myDmtCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$recordList = $this->db->get_where('dmt_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'recordList' => $recordList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/my-dmt-charge'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function myAepsCommision(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$recordList = $this->db->get_where('aeps_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'recordList' => $recordList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/my-aeps-commision'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function myUpiCommision(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$recordList = $this->db->get_where('upi_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'recordList' => $recordList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/my-upi-commision'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function myUpiCashCommision(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$recordList = $this->db->get_where('upi_cash_commision',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'recordList' => $recordList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/my-upi-cash-commision'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function myUtiPancardCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
	 	
		// get commission
		$get_com_data = $this->db->get_where('uti_pancard_commision',array('package_id'=>$member_package_id))->row_array();
		$commision = isset($get_com_data['md_commision']) ? $get_com_data['md_commision'] : 0 ;
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/my-utiPancardCommision',
            'commision' => $commision,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);	




	}

	public function myAccountVerifyCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$recordList = $this->db->get_where('dmr_account_verify_charge',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'recordList' => $recordList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/my-account-verify-charge'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function myNsdlPancardCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);

		$recordList = $this->db->get_where('nsdl_pancard_charge',array('account_id'=>$account_id,'package_id'=>$member_package_id))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'recordList' => $recordList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/my-nsdl-pancard-charge'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	
		
	
}