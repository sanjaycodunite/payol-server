<?php 
class Master extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkEmployePermission();
        $this->load->model('employe/Master_model');		
        $this->lang->load('employe/master', 'english');
        
    }

	
	public function myRechargeCommission(){


		if(!$this->User->admin_menu_permission(4,1) || !$this->User->admin_menu_permission(104,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$account_package_id = $this->User->get_account_package_id($account_id);


		$operatorList = $this->db->get_where('operator',array('type !='=>'Electricity'))->result_array();

		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				// get commission
				$get_com_data = $this->db->get_where('recharge_commision',array('package_id'=>$account_package_id,'op_id'=>$list['id']))->row_array();
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
            'content_block' => 'master/my-rechargeCommission',
            'operatorList'	=> $operatorList,
            'userList' => $userList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function myBbpsCommission(){


		if(!$this->User->admin_menu_permission(4,1) || !$this->User->admin_menu_permission(29,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];


	 	$account_package_id = $this->User->get_account_package_id($account_id);

		$operatorList = $this->db->get_where('operator',array('type'=>'Electricity'))->result_array();

		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				// get commission
				$get_com_data = $this->db->get_where('recharge_commision',array('package_id'=>$account_package_id,'op_id'=>$list['id']))->row_array();
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

        $this->parser->parse('employe/layout/column-1', $data);	

	}

	public function myBbpsLiveCommission(){

		if(!$this->User->admin_menu_permission(4,1) || !$this->User->admin_menu_permission(30,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$account_package_id = $this->User->get_account_package_id($account_id);

		$operatorList = $this->db->get_where('bbps_service')->result_array();

		if($operatorList)
		{
			foreach($operatorList as $key=>$list)
			{
				// get commission
				$get_com_data = $this->db->get_where('bbps_commision',array('package_id'=>$account_package_id,'service_id'=>$list['id']))->row_array();
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

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function myTransferCommision(){

		if(!$this->User->admin_menu_permission(4,1) || !$this->User->admin_menu_permission(31,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$account_package_id = $this->User->get_account_package_id($account_id);

		$recordList = $this->db->get_where('dmr_commision',array('package_id'=>$account_package_id))->result_array();

		
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
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


	public function myMoneyTransferCommision(){

		if(!$this->User->admin_menu_permission(4,1) || !$this->User->admin_menu_permission(32,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$account_package_id = $this->User->get_account_package_id($account_id);

		$recordList = $this->db->get_where('money_transfer_commision',array('package_id'=>$account_package_id))->result_array();

		
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
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}



	public function myPayoutCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$account_package_id = $this->User->get_account_package_id($account_id);

		$recordList = $this->db->get_where('payout_commision',array('package_id'=>$account_package_id))->result_array();

		
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
            'content_block' => 'master/my-payout-commision'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function myAepsCommision(){

		if(!$this->User->admin_menu_permission(4,1) || !$this->User->admin_menu_permission(33,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$account_package_id = $this->User->get_account_package_id($account_id);

		$recordList = $this->db->get_where('aeps_commision',array('package_id'=>$account_package_id))->result_array();

		
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
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}

	public function myAccountVerifyCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$account_package_id = $this->User->get_account_package_id($account_id);

		$recordList = $this->db->get_where('dmr_account_verify_charge',array('package_id'=>$account_package_id))->result_array();

		
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
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}

	public function myNsdlPancardCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$account_package_id = $this->User->get_account_package_id($account_id);

		$recordList = $this->db->get_where('nsdl_pancard_charge',array('package_id'=>$account_package_id))->result_array();

		
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
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}

	public function commission(){


		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(37,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/commission',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getRechargeCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$operatorList = $this->db->order_by('position','asc')->get_where('operator',array('type !='=>'Electricity'))->result_array();

			if($operatorList)
			{
				foreach($operatorList as $key=>$list)
				{
					// get commission
					$get_com_data = $this->db->get_where('recharge_commision',array('account_id'=>$account_id,'package_id'=>$member_id,'op_id'=>$list['id']))->row_array();
					$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;

					$operatorList[$key]['dt_commision'] = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
					$operatorList[$key]['dt_is_flat'] = isset($get_com_data['dt_is_flat']) ? $get_com_data['dt_is_flat'] : 0 ;
					$operatorList[$key]['dt_is_surcharge'] = isset($get_com_data['dt_is_surcharge']) ? $get_com_data['dt_is_surcharge'] : 0 ;

					$operatorList[$key]['rt_commision'] = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
					$operatorList[$key]['rt_is_flat'] = isset($get_com_data['rt_is_flat']) ? $get_com_data['rt_is_flat'] : 0 ;
					$operatorList[$key]['rt_is_surcharge'] = isset($get_com_data['rt_is_surcharge']) ? $get_com_data['rt_is_surcharge'] : 0 ;

					$operatorList[$key]['user_commision'] = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
					$operatorList[$key]['user_is_flat'] = isset($get_com_data['user_is_flat']) ? $get_com_data['user_is_flat'] : 0 ;
					$operatorList[$key]['user_is_surcharge'] = isset($get_com_data['user_is_surcharge']) ? $get_com_data['user_is_surcharge'] : 0 ;

					$operatorList[$key]['api_commision'] = isset($get_com_data['api_commision']) ? $get_com_data['api_commision'] : 0 ;
					$operatorList[$key]['api_is_flat'] = isset($get_com_data['api_is_flat']) ? $get_com_data['api_is_flat'] : 0 ;
					$operatorList[$key]['api_is_surcharge'] = isset($get_com_data['api_is_surcharge']) ? $get_com_data['api_is_surcharge'] : 0 ;
				}
			}


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th colspan="4"></th>';
			$str.='<th colspan="5">Commission</th>';
			$str.='</tr>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Operator</th>';
			$str.='<th>Code</th>';
			$str.='<th>Type</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($operatorList){
                $i=1;
                foreach($operatorList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['operator_name'].'</td>';
                	$str.='<td>'.$list['operator_code'].'</td>';
                	$str.='<td>'.$list['type'].'</td>';
                	$str.='<td><input type="hidden" name="op_id['.$key.']" class="form-control" value="'.$list['id'].'"><input type="text" name="commission['.$key.']" class="form-control" value="'.$list['commision'].'" style="margin-bottom:10px;">';
                	if($list['is_flat'] == 1){
                		$str.='<input type="checkbox" id="is_flat_'.$i.'" checked="checked" name="is_flat['.$key.']" value="1"><label for="is_flat_'.$i.'">Is Flat?</label>';
                	}
                	else
                	{
                		$str.='<input type="checkbox" id="is_flat_'.$i.'" name="is_flat['.$key.']" value="1"><label for="is_flat_'.$i.'">Is Flat?</label>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<br /><input type="checkbox" id="is_surcharge_'.$i.'" checked="checked" name="is_surcharge['.$key.']" value="1"><label for="is_surcharge_'.$i.'">Is Surcharge?</label>';
                	}
                	else
                	{
                		$str.='<br /><input type="checkbox" id="is_surcharge_'.$i.'" name="is_surcharge['.$key.']" value="1"><label for="is_surcharge_'.$i.'">Is Surcharge?</label>';
                	}
                	$str.='</td>';


                	$str.='<td><input type="text" name="dt_commision['.$key.']" class="form-control" value="'.$list['dt_commision'].'" style="margin-bottom:10px;">';
                	if($list['dt_is_flat'] == 1){
                		$str.='<input type="checkbox" id="dt_is_flat'.$i.'" checked="checked" name="dt_is_flat['.$key.']" value="1"><label for="dt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	else
                	{
                		$str.='<input type="checkbox" id="dt_is_flat'.$i.'" name="dt_is_flat['.$key.']" value="1"><label for="dt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	if($list['dt_is_surcharge'] == 1){
                		$str.='<br /><input type="checkbox" id="dt_is_surcharge'.$i.'" checked="checked" name="dt_is_surcharge['.$key.']" value="1"><label for="dt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	else
                	{
                		$str.='<br /><input type="checkbox" id="dt_is_surcharge'.$i.'" name="dt_is_surcharge['.$key.']" value="1"><label for="dt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	$str.='</td>';

                	$str.='<td><input type="text" name="rt_commision['.$key.']" class="form-control" value="'.$list['rt_commision'].'" style="margin-bottom:10px;">';
                	if($list['rt_is_flat'] == 1){
                		$str.='<input type="checkbox" id="rt_is_flat'.$i.'" checked="checked" name="rt_is_flat['.$key.']" value="1"><label for="rt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	else
                	{
                		$str.='<input type="checkbox" id="rt_is_flat'.$i.'" name="rt_is_flat['.$key.']" value="1"><label for="rt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	if($list['rt_is_surcharge'] == 1){
                		$str.='<br /><input type="checkbox" id="rt_is_surcharge'.$i.'" checked="checked" name="rt_is_surcharge['.$key.']" value="1"><label for="rt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	else
                	{
                		$str.='<br /><input type="checkbox" id="rt_is_surcharge'.$i.'" name="rt_is_surcharge['.$key.']" value="1"><label for="rt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	$str.='</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
	                	$str.='<td><input type="text" name="user_commision['.$key.']" class="form-control" value="'.$list['user_commision'].'" style="margin-bottom:10px;">';
	                	if($list['user_is_flat'] == 1){
	                		$str.='<input type="checkbox" id="user_is_flat'.$i.'" checked="checked" name="user_is_flat['.$key.']" value="1"><label for="user_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	else
	                	{
	                		$str.='<input type="checkbox" id="user_is_flat'.$i.'" name="user_is_flat['.$key.']" value="1"><label for="user_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	if($list['user_is_surcharge'] == 1){
	                		$str.='<br /><input type="checkbox" id="user_is_surcharge'.$i.'" checked="checked" name="user_is_surcharge['.$key.']" value="1"><label for="user_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	else
	                	{
	                		$str.='<br /><input type="checkbox" id="user_is_surcharge'.$i.'" name="user_is_surcharge['.$key.']" value="1"><label for="user_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	$str.='</td>';
	                }
	                if($accountData['is_disable_api_role'] != 1)
					{
	                	$str.='<td><input type="text" name="api_commision['.$key.']" class="form-control" value="'.$list['api_commision'].'" style="margin-bottom:10px;">';
	                	if($list['api_is_flat'] == 1){
	                		$str.='<input type="checkbox" id="api_is_flat'.$i.'" checked="checked" name="api_is_flat['.$key.']" value="1"><label for="api_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	else
	                	{
	                		$str.='<input type="checkbox" id="api_is_flat'.$i.'" name="api_is_flat['.$key.']" value="1"><label for="api_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	if($list['api_is_surcharge'] == 1){
	                		$str.='<br /><input type="checkbox" id="api_is_surcharge'.$i.'" checked="checked" name="api_is_surcharge['.$key.']" value="1"><label for="api_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	else
	                	{
	                		$str.='<br /><input type="checkbox" id="api_is_surcharge'.$i.'" name="api_is_surcharge['.$key.']" value="1"><label for="api_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	$str.='</td>';
	                }

                	$str.='</tr>';
                	$i++;
                }
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Operator</th>';
			$str.='<th>Code</th>';
			$str.='<th>Type</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}


	public function saveCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/commission', 'system_message_error',lang('MEMBER_ERROR'));
	 	}

 	 	
	 	if($post['op_id'])
	 	{
		 	foreach($post['op_id'] as $key => $op_id){

		 		// check commision saved or not
		 		$chk_data = $this->db->get_where('recharge_commision',array('account_id'=>$account_id,'package_id'=>$memberID,'op_id'=>$op_id))->num_rows();
		 		if($chk_data)
		 		{
		 			$data = array(
			 	  	 'commision' => isset($post['commission'][$key]) ? $post['commission'][$key] : 0,	
			 	  	 'is_flat' => isset($post['is_flat'][$key]) ? $post['is_flat'][$key] : 0,	
			 	  	 'is_surcharge' => isset($post['is_surcharge'][$key]) ? $post['is_surcharge'][$key] : 0,	
			 	  	 'dt_commision' => isset($post['dt_commision'][$key]) ? $post['dt_commision'][$key] : 0,	
			 	  	 'dt_is_flat' => isset($post['dt_is_flat'][$key]) ? $post['dt_is_flat'][$key] : 0,	
			 	  	 'dt_is_surcharge' => isset($post['dt_is_surcharge'][$key]) ? $post['dt_is_surcharge'][$key] : 0,	
			 	  	 'rt_commision' => isset($post['rt_commision'][$key]) ? $post['rt_commision'][$key] : 0,	
			 	  	 'rt_is_flat' => isset($post['rt_is_flat'][$key]) ? $post['rt_is_flat'][$key] : 0,	
			 	  	 'rt_is_surcharge' => isset($post['rt_is_surcharge'][$key]) ? $post['rt_is_surcharge'][$key] : 0,	
			 	  	 'user_commision' => isset($post['user_commision'][$key]) ? $post['user_commision'][$key] : 0,	
			 	  	 'user_is_flat' => isset($post['user_is_flat'][$key]) ? $post['user_is_flat'][$key] : 0,	
			 	  	 'user_is_surcharge' => isset($post['user_is_surcharge'][$key]) ? $post['user_is_surcharge'][$key] : 0,	
			 	  	 'api_commision' => isset($post['api_commision'][$key]) ? $post['api_commision'][$key] : 0,	
			 	  	 'api_is_flat' => isset($post['api_is_flat'][$key]) ? $post['api_is_flat'][$key] : 0,	
			 	  	 'api_is_surcharge' => isset($post['api_is_surcharge'][$key]) ? $post['api_is_surcharge'][$key] : 0,	
			 	  	 'updated_by' => $loggedUser['id']
			 	  	);

			 	  	$this->db->where('account_id',$account_id);
			 	  	$this->db->where('package_id',$memberID);
			 	  	$this->db->where('op_id',$op_id);
			 	  	$this->db->update('recharge_commision',$data);
		 		}
		 		else
		 		{
				  	$data = array(
				  	 'account_id' => $account_id,
				  	 'package_id' => $memberID,
				  	 'op_id' => $op_id,
			 	  	 'commision' => isset($post['commission'][$key]) ? $post['commission'][$key] : 0,	
			 	  	 'is_flat' => isset($post['is_flat'][$key]) ? $post['is_flat'][$key] : 0,	
			 	  	 'is_surcharge' => isset($post['is_surcharge'][$key]) ? $post['is_surcharge'][$key] : 0,	
			 	  	 'dt_commision' => isset($post['dt_commision'][$key]) ? $post['dt_commision'][$key] : 0,	
			 	  	 'dt_is_flat' => isset($post['dt_is_flat'][$key]) ? $post['dt_is_flat'][$key] : 0,	
			 	  	 'dt_is_surcharge' => isset($post['dt_is_surcharge'][$key]) ? $post['dt_is_surcharge'][$key] : 0,	
			 	  	 'rt_commision' => isset($post['rt_commision'][$key]) ? $post['rt_commision'][$key] : 0,	
			 	  	 'rt_is_flat' => isset($post['rt_is_flat'][$key]) ? $post['rt_is_flat'][$key] : 0,	
			 	  	 'rt_is_surcharge' => isset($post['rt_is_surcharge'][$key]) ? $post['rt_is_surcharge'][$key] : 0,	
			 	  	 'user_commision' => isset($post['user_commision'][$key]) ? $post['user_commision'][$key] : 0,	
			 	  	 'user_is_flat' => isset($post['user_is_flat'][$key]) ? $post['user_is_flat'][$key] : 0,	
			 	  	 'user_is_surcharge' => isset($post['user_is_surcharge'][$key]) ? $post['user_is_surcharge'][$key] : 0,	
			 	  	 'api_commision' => isset($post['api_commision'][$key]) ? $post['api_commision'][$key] : 0,	
			 	  	 'api_is_flat' => isset($post['api_is_flat'][$key]) ? $post['api_is_flat'][$key] : 0,	
			 	  	 'api_is_surcharge' => isset($post['api_is_surcharge'][$key]) ? $post['api_is_surcharge'][$key] : 0,	
			 	  	 'created_by' => $loggedUser['id']
			 	  	);

			 	  	$this->db->insert('recharge_commision',$data);
		 	  	}
				
		 	}
	 	}

	 	$this->Az->redirect('employe/master/commission', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}




	public function bbpsCommission(){

		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(38,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/bbpsCommission',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getBBPSCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	
 	 	
	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$operatorList = $this->db->get_where('operator',array('type'=>'Electricity'))->result_array();

			if($operatorList)
			{
				foreach($operatorList as $key=>$list)
				{
					// get commission
					$get_com_data = $this->db->get_where('recharge_commision',array('account_id'=>$account_id,'package_id'=>$member_id,'op_id'=>$list['id']))->row_array();
					$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;

					$operatorList[$key]['dt_commision'] = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
					$operatorList[$key]['dt_is_flat'] = isset($get_com_data['dt_is_flat']) ? $get_com_data['dt_is_flat'] : 0 ;
					$operatorList[$key]['dt_is_surcharge'] = isset($get_com_data['dt_is_surcharge']) ? $get_com_data['dt_is_surcharge'] : 0 ;

					$operatorList[$key]['rt_commision'] = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
					$operatorList[$key]['rt_is_flat'] = isset($get_com_data['rt_is_flat']) ? $get_com_data['rt_is_flat'] : 0 ;
					$operatorList[$key]['rt_is_surcharge'] = isset($get_com_data['rt_is_surcharge']) ? $get_com_data['rt_is_surcharge'] : 0 ;

					$operatorList[$key]['user_commision'] = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
					$operatorList[$key]['user_is_flat'] = isset($get_com_data['user_is_flat']) ? $get_com_data['user_is_flat'] : 0 ;
					$operatorList[$key]['user_is_surcharge'] = isset($get_com_data['user_is_surcharge']) ? $get_com_data['user_is_surcharge'] : 0 ;

					$operatorList[$key]['api_commision'] = isset($get_com_data['api_commision']) ? $get_com_data['api_commision'] : 0 ;
					$operatorList[$key]['api_is_flat'] = isset($get_com_data['api_is_flat']) ? $get_com_data['api_is_flat'] : 0 ;
					$operatorList[$key]['api_is_surcharge'] = isset($get_com_data['api_is_surcharge']) ? $get_com_data['api_is_surcharge'] : 0 ;
				}
			}


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th colspan="4"></th>';
			$str.='<th colspan="5">Commission</th>';
			$str.='</tr>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Operator</th>';
			$str.='<th>Code</th>';
			$str.='<th>Type</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($operatorList){
                $i=1;
                foreach($operatorList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['operator_name'].'</td>';
                	$str.='<td>'.$list['operator_code'].'</td>';
                	$str.='<td>'.$list['type'].'</td>';
                	$str.='<td><input type="hidden" name="op_id['.$key.']" class="form-control" value="'.$list['id'].'"><input type="text" name="commission['.$key.']" class="form-control" value="'.$list['commision'].'" style="margin-bottom:10px;">';
                	if($list['is_flat'] == 1){
                		$str.='<input type="checkbox" id="is_flat_'.$i.'" checked="checked" name="is_flat['.$key.']" value="1"><label for="is_flat_'.$i.'">Is Flat?</label>';
                	}
                	else
                	{
                		$str.='<input type="checkbox" id="is_flat_'.$i.'" name="is_flat['.$key.']" value="1"><label for="is_flat_'.$i.'">Is Flat?</label>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<br /><input type="checkbox" id="is_surcharge_'.$i.'" checked="checked" name="is_surcharge['.$key.']" value="1"><label for="is_surcharge_'.$i.'">Is Surcharge?</label>';
                	}
                	else
                	{
                		$str.='<br /><input type="checkbox" id="is_surcharge_'.$i.'" name="is_surcharge['.$key.']" value="1"><label for="is_surcharge_'.$i.'">Is Surcharge?</label>';
                	}
                	$str.='</td>';

                	$str.='<td><input type="text" name="dt_commision['.$key.']" class="form-control" value="'.$list['dt_commision'].'" style="margin-bottom:10px;">';
                	if($list['dt_is_flat'] == 1){
                		$str.='<input type="checkbox" id="dt_is_flat'.$i.'" checked="checked" name="dt_is_flat['.$key.']" value="1"><label for="dt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	else
                	{
                		$str.='<input type="checkbox" id="dt_is_flat'.$i.'" name="dt_is_flat['.$key.']" value="1"><label for="dt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	if($list['dt_is_surcharge'] == 1){
                		$str.='<br /><input type="checkbox" id="dt_is_surcharge'.$i.'" checked="checked" name="dt_is_surcharge['.$key.']" value="1"><label for="dt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	else
                	{
                		$str.='<br /><input type="checkbox" id="dt_is_surcharge'.$i.'" name="dt_is_surcharge['.$key.']" value="1"><label for="dt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	$str.='</td>';

                	$str.='<td><input type="text" name="rt_commision['.$key.']" class="form-control" value="'.$list['rt_commision'].'" style="margin-bottom:10px;">';
                	if($list['rt_is_flat'] == 1){
                		$str.='<input type="checkbox" id="rt_is_flat'.$i.'" checked="checked" name="rt_is_flat['.$key.']" value="1"><label for="rt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	else
                	{
                		$str.='<input type="checkbox" id="rt_is_flat'.$i.'" name="rt_is_flat['.$key.']" value="1"><label for="rt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	if($list['rt_is_surcharge'] == 1){
                		$str.='<br /><input type="checkbox" id="rt_is_surcharge'.$i.'" checked="checked" name="rt_is_surcharge['.$key.']" value="1"><label for="rt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	else
                	{
                		$str.='<br /><input type="checkbox" id="rt_is_surcharge'.$i.'" name="rt_is_surcharge['.$key.']" value="1"><label for="rt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	$str.='</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
	                	$str.='<td><input type="text" name="user_commision['.$key.']" class="form-control" value="'.$list['user_commision'].'" style="margin-bottom:10px;">';
	                	if($list['user_is_flat'] == 1){
	                		$str.='<input type="checkbox" id="user_is_flat'.$i.'" checked="checked" name="user_is_flat['.$key.']" value="1"><label for="user_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	else
	                	{
	                		$str.='<input type="checkbox" id="user_is_flat'.$i.'" name="user_is_flat['.$key.']" value="1"><label for="user_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	if($list['user_is_surcharge'] == 1){
	                		$str.='<br /><input type="checkbox" id="user_is_surcharge'.$i.'" checked="checked" name="user_is_surcharge['.$key.']" value="1"><label for="user_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	else
	                	{
	                		$str.='<br /><input type="checkbox" id="user_is_surcharge'.$i.'" name="user_is_surcharge['.$key.']" value="1"><label for="user_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	$str.='</td>';
	                }
	                if($accountData['is_disable_api_role'] != 1)
					{
	                	$str.='<td><input type="text" name="api_commision['.$key.']" class="form-control" value="'.$list['api_commision'].'" style="margin-bottom:10px;">';
	                	if($list['api_is_flat'] == 1){
	                		$str.='<input type="checkbox" id="api_is_flat'.$i.'" checked="checked" name="api_is_flat['.$key.']" value="1"><label for="api_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	else
	                	{
	                		$str.='<input type="checkbox" id="api_is_flat'.$i.'" name="api_is_flat['.$key.']" value="1"><label for="api_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	if($list['api_is_surcharge'] == 1){
	                		$str.='<br /><input type="checkbox" id="api_is_surcharge'.$i.'" checked="checked" name="api_is_surcharge['.$key.']" value="1"><label for="api_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	else
	                	{
	                		$str.='<br /><input type="checkbox" id="api_is_surcharge'.$i.'" name="api_is_surcharge['.$key.']" value="1"><label for="api_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	$str.='</td>';
	                }

                	$str.='</tr>';
                	$i++;
                }
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Operator</th>';
			$str.='<th>Code</th>';
			$str.='<th>Type</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}


	public function saveBBPSCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/bbpsCommission', 'system_message_error',lang('MEMBER_ERROR'));
	 	}

	 	
	 	if($post['op_id'])
	 	{
		 	foreach($post['op_id'] as $key => $op_id){

		 		// check commision saved or not
		 		$chk_data = $this->db->get_where('recharge_commision',array('account_id'=>$account_id,'package_id'=>$memberID,'op_id'=>$op_id))->num_rows();
		 		if($chk_data)
		 		{
		 			$data = array(
			 	  	 'commision' => isset($post['commission'][$key]) ? $post['commission'][$key] : 0,	
			 	  	 'is_flat' => isset($post['is_flat'][$key]) ? $post['is_flat'][$key] : 0,	
			 	  	 'is_surcharge' => isset($post['is_surcharge'][$key]) ? $post['is_surcharge'][$key] : 0,	
			 	  	 'dt_commision' => isset($post['dt_commision'][$key]) ? $post['dt_commision'][$key] : 0,	
			 	  	 'dt_is_flat' => isset($post['dt_is_flat'][$key]) ? $post['dt_is_flat'][$key] : 0,	
			 	  	 'dt_is_surcharge' => isset($post['dt_is_surcharge'][$key]) ? $post['dt_is_surcharge'][$key] : 0,	
			 	  	 'rt_commision' => isset($post['rt_commision'][$key]) ? $post['rt_commision'][$key] : 0,	
			 	  	 'rt_is_flat' => isset($post['rt_is_flat'][$key]) ? $post['rt_is_flat'][$key] : 0,	
			 	  	 'rt_is_surcharge' => isset($post['rt_is_surcharge'][$key]) ? $post['rt_is_surcharge'][$key] : 0,	
			 	  	 'user_commision' => isset($post['user_commision'][$key]) ? $post['user_commision'][$key] : 0,	
			 	  	 'user_is_flat' => isset($post['user_is_flat'][$key]) ? $post['user_is_flat'][$key] : 0,	
			 	  	 'user_is_surcharge' => isset($post['user_is_surcharge'][$key]) ? $post['user_is_surcharge'][$key] : 0,	
			 	  	 'api_commision' => isset($post['api_commision'][$key]) ? $post['api_commision'][$key] : 0,	
			 	  	 'api_is_flat' => isset($post['api_is_flat'][$key]) ? $post['api_is_flat'][$key] : 0,	
			 	  	 'api_is_surcharge' => isset($post['api_is_surcharge'][$key]) ? $post['api_is_surcharge'][$key] : 0,
			 	  	 'updated_by' => $loggedUser['id']
			 	  	);

			 	  	$this->db->where('account_id',$account_id);
			 	  	$this->db->where('package_id',$memberID);
			 	  	$this->db->where('op_id',$op_id);
			 	  	$this->db->update('recharge_commision',$data);
		 		}
		 		else
		 		{
				  	$data = array(
				  	 'account_id' => $account_id,
				  	 'package_id' => $memberID,
				  	 'op_id' => $op_id,
			 	  	 'commision' => isset($post['commission'][$key]) ? $post['commission'][$key] : 0,	
			 	  	 'is_flat' => isset($post['is_flat'][$key]) ? $post['is_flat'][$key] : 0,	
			 	  	 'is_surcharge' => isset($post['is_surcharge'][$key]) ? $post['is_surcharge'][$key] : 0,	
			 	  	 'dt_commision' => isset($post['dt_commision'][$key]) ? $post['dt_commision'][$key] : 0,	
			 	  	 'dt_is_flat' => isset($post['dt_is_flat'][$key]) ? $post['dt_is_flat'][$key] : 0,	
			 	  	 'dt_is_surcharge' => isset($post['dt_is_surcharge'][$key]) ? $post['dt_is_surcharge'][$key] : 0,	
			 	  	 'rt_commision' => isset($post['rt_commision'][$key]) ? $post['rt_commision'][$key] : 0,	
			 	  	 'rt_is_flat' => isset($post['rt_is_flat'][$key]) ? $post['rt_is_flat'][$key] : 0,	
			 	  	 'rt_is_surcharge' => isset($post['rt_is_surcharge'][$key]) ? $post['rt_is_surcharge'][$key] : 0,	
			 	  	 'user_commision' => isset($post['user_commision'][$key]) ? $post['user_commision'][$key] : 0,	
			 	  	 'user_is_flat' => isset($post['user_is_flat'][$key]) ? $post['user_is_flat'][$key] : 0,	
			 	  	 'user_is_surcharge' => isset($post['user_is_surcharge'][$key]) ? $post['user_is_surcharge'][$key] : 0,	
			 	  	 'api_commision' => isset($post['api_commision'][$key]) ? $post['api_commision'][$key] : 0,	
			 	  	 'api_is_flat' => isset($post['api_is_flat'][$key]) ? $post['api_is_flat'][$key] : 0,	
			 	  	 'api_is_surcharge' => isset($post['api_is_surcharge'][$key]) ? $post['api_is_surcharge'][$key] : 0,
			 	  	 'created_by' => $loggedUser['id']
			 	  	);

			 	  	$this->db->insert('recharge_commision',$data);
		 	  	}
				
		 	}
	 	}

	 	$this->Az->redirect('employe/master/bbpsCommission', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}


	public function transferCommision(){

		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(40,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$accountData = $this->User->get_account_data($account_id);
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'accountData' => $accountData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/transfer-commision'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveDMRCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'Commission Type', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			
			$this->transferCommision();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/transferCommision', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_user' => isset($post['is_user']) ? $post['is_user'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'com_type' => $post['com_type'],
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('dmr_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/transferCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberDMRCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('dmr_commision',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['md_charge'].'</td>';
                	$str.='<td>'.$list['dt_charge'].'</td>';
                	$str.='<td>'.$list['rt_charge'].'</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
                		$str.='<td>'.$list['user_charge'].'</td>';
                	}
                	if($accountData['is_disable_api_role'] != 1)
					{
                		$str.='<td>'.$list['api_charge'].'</td>';
                	}
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}

                	if($list['com_type'] == 'RGS')
					{
                		$str.='<td>NEFT</td>';
                	}
                	elseif($list['com_type'] == 'RTG')
					{
                		$str.='<td>RTGS</td>';
                	}
                	elseif($list['com_type'] == 'IFS')
					{
                		$str.='<td>IMPS</td>';
                	}
                	else
					{
                		$str.='<td>Not Available</td>';
                	}
                	
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updatedmrModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'admin/master/deleteDMRCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="7" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateDMRCom(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'End Range', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/transferCommision', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('dmr_commision',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('dmr_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/transferCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'com_type' => $post['com_type'],
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('dmr_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/transferCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteDMRCom($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('dmr_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/transferCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('dmr_commision',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('dmr_commision');
 		$this->Az->redirect('employe/master/transferCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getDMRCommData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('dmr_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('dmr_commision',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>MD Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="md_charge" class="form-control" value="'.$dmrData['md_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>DT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="dt_charge" class="form-control" value="'.$dmrData['dt_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>RT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="rt_charge" class="form-control" value="'.$dmrData['rt_charge'].'">';
	        $str.='</div>';
	        if($accountData['is_disable_user_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>User Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="user_charge" class="form-control" value="'.$dmrData['user_charge'].'">';
		        $str.='</div>';
		    }
		    if($accountData['is_disable_api_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>API Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="api_charge" class="form-control" value="'.$dmrData['api_charge'].'">';
		        $str.='</div>';
		    }
		    $str.='<div class="form-group">';
	        $str.='<label><b>Commission Type*</b></label>';
	        $str.='<select class="form-control" name="com_type">';
	        $str.='<option value="">Select</option>';
	        if($dmrData['com_type'] == 'RGS')
	        {
	        	$str.='<option value="RGS" selected="selected">NEFT</option>';
	        	$str.='<option value="RTG">RTGS</option>';
	        	$str.='<option value="IFS">IMPS</option>';
	        }
	    	elseif($dmrData['com_type'] == 'RTG')
	    	{
	    		$str.='<option value="RGS">NEFT</option>';
	        	$str.='<option value="RTG" selected="selected">RTGS</option>';
	        	$str.='<option value="IFS">IMPS</option>';
	    	}
	    	elseif($dmrData['com_type'] == 'IFS')
	    	{
	    		$str.='<option value="RGS">NEFT</option>';
	        	$str.='<option value="RTG">RTGS</option>';
	        	$str.='<option value="IFS" selected="selected">IMPS</option>';
	    	}
	    	else
	    	{
	    		$str.='<option value="RGS">NEFT</option>';
	        	$str.='<option value="RTG">RTGS</option>';
	        	$str.='<option value="IFS">IMPS</option>';
	    	}
	        $str.='</select>';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}


	public function payoutCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/payout-commision'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function savePayoutCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->payoutCommission();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/payoutCommission', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}

		 	$recordList = $this->db->get_where('payout_commision',array('start_range <='=>$post['startRange'],'end_range >='=>$post['endRange'],'package_id'=>$account_package_id))->row_array();
		 	$admin_surcharge = isset($recordList['surcharge']) ? $recordList['surcharge'] : 0 ;
		 	if($post['surcharge'] < $admin_surcharge)
		 	{
		 		$this->Az->redirect('employe/master/payoutCommission', 'system_message_error',lang('MEMBER_SURCHARGE_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'surcharge' => $post['surcharge'],	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_user' => isset($post['is_user']) ? $post['is_user'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('payout_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/payoutCommission', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberPayoutCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('payout_commision',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Surcharge</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>User Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['surcharge'].'</td>';
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	$str2 = '';
                	$is_val = 0;
                	if($list['is_md'])
                	{
                		$str2.='MD';
                		$is_val = 1;
                	}
                	if($list['is_dt'])
                	{
                		$str2.='DT';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	if($list['is_rt'])
                	{
                		$str2.='RT';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	if($list['is_user'])
                	{
                		$str2.='User';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	if($list['is_api'])
                	{
                		$str2.='API';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}

                	$str.='<td>'.$str2.'</td>';

                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updatePayoutModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'admin/master/deletePayoutCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="7" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Surcharge</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>User Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updatePayoutCom(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/payoutCommission', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('payout_commision',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('payout_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/payoutCommission', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'surcharge' => $post['surcharge'],	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('payout_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/payoutCommission', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deletePayoutCom($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('payout_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/payoutCommission', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('payout_commision',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('payout_commision');
 		$this->Az->redirect('employe/master/payoutCommission', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getPayoutCommData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('payout_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('payout_commision',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>Surcharge*</label>';
	        $str.='<input type="text" autocomplete="off" name="surcharge" class="form-control" value="'.$dmrData['surcharge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}


	public function aepsCommision(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$accountData = $this->User->get_account_data($account_id);
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'accountData' => $accountData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/aeps-commision'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveAEPSCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'Commission Type', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->aepsCommision();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/aepsCommision', 'system_message_error',lang('MEMBER_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_commision' => $post['md_commision'],	
	 	  	 'dt_commision' => $post['dt_commision'],	
	 	  	 'rt_commision' => $post['rt_commision'],	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'com_type' => $post['com_type'],	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'created_by' => $loggedUser['id']	
	 	  	);

	 	  	$this->db->insert('aeps_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/aepsCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberAEPSCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('aeps_commision',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Type</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['md_commision'].'</td>';
                	$str.='<td>'.$list['dt_commision'].'</td>';
                	$str.='<td>'.$list['rt_commision'].'</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
                		$str.='<td>'.$list['user_commision'].'</td>';
                	}
                	if($accountData['is_disable_api_role'] != 1)
					{
                		$str.='<td>'.$list['api_commision'].'</td>';
                	}
                	if($list['com_type'] == 1){
                		$str.='<td>Account Withdrawal</td>';
                	}
                	elseif($list['com_type'] == 2)
                	{
                		$str.='<td>Mini Statement</td>';
                	}
                	elseif($list['com_type'] == 3)
                	{
                		$str.='<td>Aadhar Pay</td>';
                	}
                	elseif($list['com_type'] == 4)
                	{
                		$str.='<td>Cash Deposite</td>';
                	}
                	elseif($list['com_type'] == 5)
                	{
                		$str.='<td>MATM</td>';
                	}
                	else
                	{
                		$str.='<td>Not Selected</td>';
                	}

                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateaepsModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'admin/master/deleteAEPSCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="12" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Type</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateAEPSCom(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'Commission Type', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/aepsCommision', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];
	 	 	
	 		$chk_member = $this->db->get_where('aeps_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/aepsCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
	 		
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_commision' => $post['md_commision'],	
	 	  	 'dt_commision' => $post['dt_commision'],	
	 	  	 'rt_commision' => $post['rt_commision'],	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'com_type' => $post['com_type'],	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('aeps_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/aepsCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteAEPSCom($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('aeps_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/aepsCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		
 		$this->db->where('id',$recordID);
 		$this->db->delete('aeps_commision');
 		$this->Az->redirect('employe/master/aepsCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getAEPSCommData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('aeps_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('aeps_commision',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>MD</label>';
	        $str.='<input type="text" autocomplete="off" name="md_commision" class="form-control" value="'.$dmrData['md_commision'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>DT</label>';
	        $str.='<input type="text" autocomplete="off" name="dt_commision" class="form-control" value="'.$dmrData['dt_commision'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>RT</label>';
	        $str.='<input type="text" autocomplete="off" name="rt_commision" class="form-control" value="'.$dmrData['rt_commision'].'">';
	        $str.='</div>';
	        if($accountData['is_disable_user_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>User</label>';
		        $str.='<input type="text" autocomplete="off" name="user_commision" class="form-control" value="'.$dmrData['user_commision'].'">';
		        $str.='</div>';
		    }
		    if($accountData['is_disable_api_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>API</label>';
		        $str.='<input type="text" autocomplete="off" name="api_commision" class="form-control" value="'.$dmrData['api_commision'].'">';
		        $str.='</div>';
		    }
	        $str.='<div class="form-group">';
	        $str.='<label><b>Commission Type*</b></label>';
	        $str.='<select class="form-control" name="com_type">';
	        $str.='<option value="">Select</option>';
	        if($dmrData['com_type'] == 1)
	        {
	        	$str.='<option value="1" selected="selected">Account Withdrawal</option>';
	        	$str.='<option value="2">Mini Statement</option>';
	        	$str.='<option value="3">Aadhar Pay</option>';
	        	$str.='<option value="4">Cash Deposite</option>';
	        	$str.='<option value="5">MATM</option>';
	    	}
	    	elseif($dmrData['com_type'] == 2)
	    	{
	    		$str.='<option value="1">Account Withdrawal</option>';
	        	$str.='<option value="2" selected="selected">Mini Statement</option>';
	        	$str.='<option value="3">Aadhar Pay</option>';
	        	$str.='<option value="4">Cash Deposite</option>';
	        	$str.='<option value="5">MATM</option>';
	    	}
	    	elseif($dmrData['com_type'] == 3)
	    	{
	    		$str.='<option value="1">Account Withdrawal</option>';
	        	$str.='<option value="2">Mini Statement</option>';
	        	$str.='<option value="3" selected="selected">Aadhar Pay</option>';
	        	$str.='<option value="4">Cash Deposite</option>';
	        	$str.='<option value="5">MATM</option>';
	    	}
	    	elseif($dmrData['com_type'] == 4)
	    	{
	    		$str.='<option value="1">Account Withdrawal</option>';
	        	$str.='<option value="2">Mini Statement</option>';
	        	$str.='<option value="3">Aadhar Pay</option>';
	        	$str.='<option value="4" selected="selected">Cash Deposite</option>';
	        	$str.='<option value="5">MATM</option>';
	    	}
	    	elseif($dmrData['com_type'] == 5)
	    	{
	    		$str.='<option value="1">Account Withdrawal</option>';
	        	$str.='<option value="2">Mini Statement</option>';
	        	$str.='<option value="3">Aadhar Pay</option>';
	        	$str.='<option value="4">Cash Deposite</option>';
	        	$str.='<option value="5" selected="selected">MATM</option>';
	    	}
	    	else
	    	{
	    		$str.='<option value="1">Account Withdrawal</option>';
	        	$str.='<option value="2">Mini Statement</option>';
	        	$str.='<option value="3">Aadhar Pay</option>';
	        	$str.='<option value="4">Cash Deposite</option>';
	        	$str.='<option value="5">MATM</option>';
	    	}
	        $str.='</select>';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_surcharge'])
	        {
	        	$str.='<input type="checkbox" name="is_surcharge" checked="checked" id="is_surchargee" value="1"> <label for="is_surchargee"><b>Is Surcharge</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_surcharge" id="is_surchargee" value="1"> <label for="is_surchargee"><b>Is Surcharge</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}

	public function service(){


		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(46,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}



		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$operatorList = $this->db->select('account_services.*,services.title')->join('services','services.id = account_services.service_id')->get_where('account_services',array('account_id'=>$account_id))->result_array();

		

		// get users list
		$userList = $this->db->where_in('role_id',array(3,4,5,6,8))->get_where('users',array('account_id'=>$account_id))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/service',
            'operatorList'	=> $operatorList,
            'userList' => $userList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	

	}

	public function getServiceData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id > 0)
	 	{
	 		$chk_member = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$is_error = 1;
	 			
	 		}
	 	}

	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Member is not valid.'
	 			);
	 	}
	 	else
	 	{
			$operatorList = $this->db->select('account_services.*,services.title')->join('services','services.id = account_services.service_id')->get_where('account_services',array('account_id'=>$account_id,'status'=>1))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>Active/Deactive</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($operatorList){
                $i=1;
                foreach($operatorList as $key=>$list){

                	$is_active = $this->db->get_where('account_user_services',array('account_id'=>$account_id,'member_id'=>$member_id,'service_id'=>$list['service_id'],'status'=>1))->num_rows();

                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['title'].'</td>';
                	if($is_active){
                		$str.='<td><input type="checkbox" checked="checked" name="service_id['.$key.']" value="'.$list['service_id'].'"></td>';
                	}
                	else
                	{
                		$str.='<td><input type="checkbox" name="service_id['.$key.']" value="'.$list['service_id'].'"></td>';
                	}
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>Active/Deactive</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	public function saveServiceAuth(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	

 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/service', 'system_message_error',lang('MEMBER_ERROR'));
	 	}
 	 	if($memberID > 0)
	 	{
	 		$chk_member = $this->db->get_where('users',array('id'=>$memberID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/service', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
	 	}

	 	$activeService = $this->db->select('account_services.*')->get_where('account_services',array('account_id'=>$account_id,'status'=>1))->result_array();

	 	$activeServiceID = array();
	 	if($activeService)
	 	{
	 		foreach($activeService as $key=>$list)
	 		{
	 			$activeServiceID[$key] = $list['service_id'];
	 		}
	 	}

	 	if(isset($post['service_id']))
	 	{
	 		foreach ($post['service_id'] as $value) {
	 			if(!in_array($value, $activeServiceID))
	 			{
	 				$this->Az->redirect('employe/master/service', 'system_message_error',lang('MEMBER_ERROR'));
	 			}
	 		}

	 		foreach ($post['service_id'] as $value) {
	 			// check commision saved or not
		 		$chk_data = $this->db->get_where('account_user_services',array('account_id'=>$account_id,'member_id'=>$memberID,'service_id'=>$value))->num_rows();
		 		if($chk_data)
		 		{
		 			$data = array(
			 	  	 'status' => 1,
			 	  	 'updated_by' => $loggedUser['id']	
			 	  	);

			 	  	$this->db->where('account_id',$account_id);
			 	  	$this->db->where('member_id',$memberID);
			 	  	$this->db->where('service_id',$value);
			 	  	$this->db->update('account_user_services',$data);
		 		}
		 		else
		 		{
				  	$data = array(
				  	 'account_id' => $account_id,
				  	 'member_id' => $memberID,
				  	 'service_id' => $value,
			 	  	 'status' => 1,
			 	  	 'created_by' => $loggedUser['id']		
			 	  	);

			 	  	$this->db->insert('account_user_services',$data);
		 	  	}
	 		}
	 		$this->db->where('account_id',$account_id);
	 	  	$this->db->where('member_id',$memberID);
	 	  	$this->db->where_not_in('service_id',$post['service_id']);
	 	  	$this->db->update('account_user_services',array('status'=>0));
	 	}
	 	else
	 	{
	 		$this->db->where('account_id',$account_id);
	 	  	$this->db->where('member_id',$memberID);
	 	  	$this->db->update('account_user_services',array('status'=>0));
	 	}
 	 	
	 	
	 	$this->Az->redirect('employe/master/service', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}

	public function bbpsLiveCommission(){

		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(39,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/bbpsLiveCommission',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getBBPSLiveCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	
 	 	
	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$operatorList = $this->db->get_where('bbps_service')->result_array();

			if($operatorList)
			{
				foreach($operatorList as $key=>$list)
				{
					// get commission
					$get_com_data = $this->db->get_where('bbps_commision',array('account_id'=>$account_id,'package_id'=>$member_id,'service_id'=>$list['id']))->row_array();
					$operatorList[$key]['commision'] = isset($get_com_data['commision']) ? $get_com_data['commision'] : 0 ;
					$operatorList[$key]['is_flat'] = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
					$operatorList[$key]['is_surcharge'] = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;

					$operatorList[$key]['dt_commision'] = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
					$operatorList[$key]['dt_is_flat'] = isset($get_com_data['dt_is_flat']) ? $get_com_data['dt_is_flat'] : 0 ;
					$operatorList[$key]['dt_is_surcharge'] = isset($get_com_data['dt_is_surcharge']) ? $get_com_data['dt_is_surcharge'] : 0 ;

					$operatorList[$key]['rt_commision'] = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
					$operatorList[$key]['rt_is_flat'] = isset($get_com_data['rt_is_flat']) ? $get_com_data['rt_is_flat'] : 0 ;
					$operatorList[$key]['rt_is_surcharge'] = isset($get_com_data['rt_is_surcharge']) ? $get_com_data['rt_is_surcharge'] : 0 ;

					$operatorList[$key]['user_commision'] = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
					$operatorList[$key]['user_is_flat'] = isset($get_com_data['user_is_flat']) ? $get_com_data['user_is_flat'] : 0 ;
					$operatorList[$key]['user_is_surcharge'] = isset($get_com_data['user_is_surcharge']) ? $get_com_data['user_is_surcharge'] : 0 ;

					$operatorList[$key]['api_commision'] = isset($get_com_data['api_commision']) ? $get_com_data['api_commision'] : 0 ;
					$operatorList[$key]['api_is_flat'] = isset($get_com_data['api_is_flat']) ? $get_com_data['api_is_flat'] : 0 ;
					$operatorList[$key]['api_is_surcharge'] = isset($get_com_data['api_is_surcharge']) ? $get_com_data['api_is_surcharge'] : 0 ;
				}
			}


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th colspan="2"></th>';
			$str.='<th colspan="5">Commission</th>';
			$str.='</tr>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($operatorList){
                $i=1;
                foreach($operatorList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['title'].'</td>';
                	$str.='<td><input type="hidden" name="op_id['.$key.']" class="form-control" value="'.$list['id'].'"><input type="text" name="commission['.$key.']" class="form-control" value="'.$list['commision'].'" style="margin-bottom:10px;">';
                	if($list['is_flat'] == 1){
                		$str.='<input type="checkbox" id="is_flat_'.$i.'" checked="checked" name="is_flat['.$key.']" value="1"><label for="is_flat_'.$i.'">Is Flat?</label>';
                	}
                	else
                	{
                		$str.='<input type="checkbox" id="is_flat_'.$i.'" name="is_flat['.$key.']" value="1"><label for="is_flat_'.$i.'">Is Flat?</label>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<br /><input type="checkbox" id="is_surcharge_'.$i.'" checked="checked" name="is_surcharge['.$key.']" value="1"><label for="is_surcharge_'.$i.'">Is Surcharge?</label>';
                	}
                	else
                	{
                		$str.='<br /><input type="checkbox" id="is_surcharge_'.$i.'" name="is_surcharge['.$key.']" value="1"><label for="is_surcharge_'.$i.'">Is Surcharge?</label>';
                	}
                	$str.='</td>';


                	$str.='<td><input type="text" name="dt_commision['.$key.']" class="form-control" value="'.$list['dt_commision'].'" style="margin-bottom:10px;">';
                	if($list['dt_is_flat'] == 1){
                		$str.='<input type="checkbox" id="dt_is_flat'.$i.'" checked="checked" name="dt_is_flat['.$key.']" value="1"><label for="dt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	else
                	{
                		$str.='<input type="checkbox" id="dt_is_flat'.$i.'" name="dt_is_flat['.$key.']" value="1"><label for="dt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	if($list['dt_is_surcharge'] == 1){
                		$str.='<br /><input type="checkbox" id="dt_is_surcharge'.$i.'" checked="checked" name="dt_is_surcharge['.$key.']" value="1"><label for="dt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	else
                	{
                		$str.='<br /><input type="checkbox" id="dt_is_surcharge'.$i.'" name="dt_is_surcharge['.$key.']" value="1"><label for="dt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	$str.='</td>';

                	$str.='<td><input type="text" name="rt_commision['.$key.']" class="form-control" value="'.$list['rt_commision'].'" style="margin-bottom:10px;">';
                	if($list['rt_is_flat'] == 1){
                		$str.='<input type="checkbox" id="rt_is_flat'.$i.'" checked="checked" name="rt_is_flat['.$key.']" value="1"><label for="rt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	else
                	{
                		$str.='<input type="checkbox" id="rt_is_flat'.$i.'" name="rt_is_flat['.$key.']" value="1"><label for="rt_is_flat'.$i.'">Is Flat?</label>';
                	}
                	if($list['rt_is_surcharge'] == 1){
                		$str.='<br /><input type="checkbox" id="rt_is_surcharge'.$i.'" checked="checked" name="rt_is_surcharge['.$key.']" value="1"><label for="rt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	else
                	{
                		$str.='<br /><input type="checkbox" id="rt_is_surcharge'.$i.'" name="rt_is_surcharge['.$key.']" value="1"><label for="rt_is_surcharge'.$i.'">Is Surcharge?</label>';
                	}
                	$str.='</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
	                	$str.='<td><input type="text" name="user_commision['.$key.']" class="form-control" value="'.$list['user_commision'].'" style="margin-bottom:10px;">';
	                	if($list['user_is_flat'] == 1){
	                		$str.='<input type="checkbox" id="user_is_flat'.$i.'" checked="checked" name="user_is_flat['.$key.']" value="1"><label for="user_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	else
	                	{
	                		$str.='<input type="checkbox" id="user_is_flat'.$i.'" name="user_is_flat['.$key.']" value="1"><label for="user_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	if($list['user_is_surcharge'] == 1){
	                		$str.='<br /><input type="checkbox" id="user_is_surcharge'.$i.'" checked="checked" name="user_is_surcharge['.$key.']" value="1"><label for="user_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	else
	                	{
	                		$str.='<br /><input type="checkbox" id="user_is_surcharge'.$i.'" name="user_is_surcharge['.$key.']" value="1"><label for="user_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	$str.='</td>';
	                }
	                if($accountData['is_disable_api_role'] != 1)
					{
	                	$str.='<td><input type="text" name="api_commision['.$key.']" class="form-control" value="'.$list['api_commision'].'" style="margin-bottom:10px;">';
	                	if($list['api_is_flat'] == 1){
	                		$str.='<input type="checkbox" id="api_is_flat'.$i.'" checked="checked" name="api_is_flat['.$key.']" value="1"><label for="api_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	else
	                	{
	                		$str.='<input type="checkbox" id="api_is_flat'.$i.'" name="api_is_flat['.$key.']" value="1"><label for="api_is_flat'.$i.'">Is Flat?</label>';
	                	}
	                	if($list['api_is_surcharge'] == 1){
	                		$str.='<br /><input type="checkbox" id="api_is_surcharge'.$i.'" checked="checked" name="api_is_surcharge['.$key.']" value="1"><label for="api_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	else
	                	{
	                		$str.='<br /><input type="checkbox" id="api_is_surcharge'.$i.'" name="api_is_surcharge['.$key.']" value="1"><label for="api_is_surcharge'.$i.'">Is Surcharge?</label>';
	                	}
	                	$str.='</td>';
	                }
                	$str.='</tr>';
                	$i++;
                }
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}


	public function saveBBPSLiveCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/bbpsLiveCommission', 'system_message_error',lang('MEMBER_ERROR'));
	 	}

	 	if($post['op_id'])
	 	{
		 	foreach($post['op_id'] as $key => $op_id){

		 		// check commision saved or not
		 		$chk_data = $this->db->get_where('bbps_commision',array('account_id'=>$account_id,'package_id'=>$memberID,'service_id'=>$op_id))->num_rows();
		 		if($chk_data)
		 		{
		 			$data = array(
			 	  	 'commision' => isset($post['commission'][$key]) ? $post['commission'][$key] : 0,	
			 	  	 'is_flat' => isset($post['is_flat'][$key]) ? $post['is_flat'][$key] : 0,	
			 	  	 'is_surcharge' => isset($post['is_surcharge'][$key]) ? $post['is_surcharge'][$key] : 0,	
			 	  	 'dt_commision' => isset($post['dt_commision'][$key]) ? $post['dt_commision'][$key] : 0,	
			 	  	 'dt_is_flat' => isset($post['dt_is_flat'][$key]) ? $post['dt_is_flat'][$key] : 0,	
			 	  	 'dt_is_surcharge' => isset($post['dt_is_surcharge'][$key]) ? $post['dt_is_surcharge'][$key] : 0,	
			 	  	 'rt_commision' => isset($post['rt_commision'][$key]) ? $post['rt_commision'][$key] : 0,	
			 	  	 'rt_is_flat' => isset($post['rt_is_flat'][$key]) ? $post['rt_is_flat'][$key] : 0,	
			 	  	 'rt_is_surcharge' => isset($post['rt_is_surcharge'][$key]) ? $post['rt_is_surcharge'][$key] : 0,	
			 	  	 'user_commision' => isset($post['user_commision'][$key]) ? $post['user_commision'][$key] : 0,	
			 	  	 'user_is_flat' => isset($post['user_is_flat'][$key]) ? $post['user_is_flat'][$key] : 0,	
			 	  	 'user_is_surcharge' => isset($post['user_is_surcharge'][$key]) ? $post['user_is_surcharge'][$key] : 0,	
			 	  	 'api_commision' => isset($post['api_commision'][$key]) ? $post['api_commision'][$key] : 0,	
			 	  	 'api_is_flat' => isset($post['api_is_flat'][$key]) ? $post['api_is_flat'][$key] : 0,	
			 	  	 'api_is_surcharge' => isset($post['api_is_surcharge'][$key]) ? $post['api_is_surcharge'][$key] : 0,
			 	  	 'updated_by' => $loggedUser['id']
			 	  	);

			 	  	$this->db->where('account_id',$account_id);
			 	  	$this->db->where('package_id',$memberID);
			 	  	$this->db->where('service_id',$op_id);
			 	  	$this->db->update('bbps_commision',$data);
		 		}
		 		else
		 		{
				  	$data = array(
				  	 'account_id' => $account_id,
				  	 'package_id' => $memberID,
				  	 'service_id' => $op_id,
			 	  	 'commision' => isset($post['commission'][$key]) ? $post['commission'][$key] : 0,	
			 	  	 'is_flat' => isset($post['is_flat'][$key]) ? $post['is_flat'][$key] : 0,	
			 	  	 'is_surcharge' => isset($post['is_surcharge'][$key]) ? $post['is_surcharge'][$key] : 0,	
			 	  	 'dt_commision' => isset($post['dt_commision'][$key]) ? $post['dt_commision'][$key] : 0,	
			 	  	 'dt_is_flat' => isset($post['dt_is_flat'][$key]) ? $post['dt_is_flat'][$key] : 0,	
			 	  	 'dt_is_surcharge' => isset($post['dt_is_surcharge'][$key]) ? $post['dt_is_surcharge'][$key] : 0,	
			 	  	 'rt_commision' => isset($post['rt_commision'][$key]) ? $post['rt_commision'][$key] : 0,	
			 	  	 'rt_is_flat' => isset($post['rt_is_flat'][$key]) ? $post['rt_is_flat'][$key] : 0,	
			 	  	 'rt_is_surcharge' => isset($post['rt_is_surcharge'][$key]) ? $post['rt_is_surcharge'][$key] : 0,	
			 	  	 'user_commision' => isset($post['user_commision'][$key]) ? $post['user_commision'][$key] : 0,	
			 	  	 'user_is_flat' => isset($post['user_is_flat'][$key]) ? $post['user_is_flat'][$key] : 0,	
			 	  	 'user_is_surcharge' => isset($post['user_is_surcharge'][$key]) ? $post['user_is_surcharge'][$key] : 0,	
			 	  	 'api_commision' => isset($post['api_commision'][$key]) ? $post['api_commision'][$key] : 0,	
			 	  	 'api_is_flat' => isset($post['api_is_flat'][$key]) ? $post['api_is_flat'][$key] : 0,	
			 	  	 'api_is_surcharge' => isset($post['api_is_surcharge'][$key]) ? $post['api_is_surcharge'][$key] : 0,
			 	  	 'created_by' => $loggedUser['id']
			 	  	);

			 	  	$this->db->insert('bbps_commision',$data);
		 	  	}
				
		 	}
	 	}

	 	$this->Az->redirect('employe/master/bbpsLiveCommission', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}







	public function moneyTransferCommision(){

		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(41,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$accountData = $this->User->get_account_data($account_id);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'accountData' => $accountData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/money-transfer-commision'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveMoneyTransferCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE) {
			
			$this->moneyTransferCommision();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/moneyTransferCommision', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_user' => isset($post['is_user']) ? $post['is_user'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('money_transfer_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/moneyTransferCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberMoneyTransferCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('money_transfer_commision',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['md_charge'].'</td>';
                	$str.='<td>'.$list['dt_charge'].'</td>';
                	$str.='<td>'.$list['rt_charge'].'</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
                		$str.='<td>'.$list['user_charge'].'</td>';
                	}
                	if($accountData['is_disable_api_role'] != 1)
					{
                		$str.='<td>'.$list['api_charge'].'</td>';
                	}
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateMoneyTransferModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'admin/master/deleteMoneyTransferCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="7" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateMoneyTransferCom(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/moneyTransferCommision', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('money_transfer_commision',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('money_transfer_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/moneyTransferCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('money_transfer_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/moneyTransferCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteMoneyTransferCom($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('money_transfer_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/moneyTransferCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('money_transfer_commision',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('money_transfer_commision');
 		$this->Az->redirect('employe/master/moneyTransferCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getMoneyTransferCommData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('money_transfer_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('money_transfer_commision',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>MD Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="md_charge" class="form-control" value="'.$dmrData['md_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>DT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="dt_charge" class="form-control" value="'.$dmrData['dt_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>RT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="rt_charge" class="form-control" value="'.$dmrData['rt_charge'].'">';
	        $str.='</div>';
	        if($accountData['is_disable_user_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>User Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="user_charge" class="form-control" value="'.$dmrData['user_charge'].'">';
		        $str.='</div>';
		    }
		    if($accountData['is_disable_api_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>API Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="api_charge" class="form-control" value="'.$dmrData['api_charge'].'">';
		        $str.='</div>';
		    }
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}

	public function upiCommision(){


		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(43,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/upi-commission',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getUpiCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('upi_commision',array('upi_commision.account_id'=>$account_id,'upi_commision.package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>User Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['commission'].'</td>';
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	$str2 = '';
                	$is_val = 0;
                	if($list['is_md'])
                	{
                		$str2.='MD';
                		$is_val = 1;
                	}
                	if($list['is_dt'])
                	{
                		$str2.='DT';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	if($list['is_rt'])
                	{
                		$str2.='RT';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	if($list['is_api'])
                	{
                		$str2.='API';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	

                	$str.='<td>'.$str2.'</td>';

                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateUpiQrModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteUpiQrCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="9" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>User Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	public function deleteUpiQrCom($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('upi_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/upiCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('upi_commision',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('upi_commision');
 		$this->Az->redirect('employe/master/upiCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}

	public function deleteRefferalCom($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('referral_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/referralCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->delete('referral_commision');
 		$this->Az->redirect('employe/master/referralCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}

	public function getUpiQrCommData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('upi_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			
 			$dmrData = $this->db->get_where('upi_commision',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>Commission*</label>';
	        $str.='<input type="text" autocomplete="off" name="commision" class="form-control" value="'.$dmrData['commission'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_surcharge'])
	        {
	        	$str.='<input type="checkbox" name="is_surcharge" checked="checked" id="is_surchargee" value="1"> <label for="is_surchargee"><b>Is Surcharge</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_surcharge" id="is_surchargee" value="1"> <label for="is_surchargee"><b>Is Surcharge</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}

	public function updateUpiQrCom(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('commision', 'Commission', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/upiCommision', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('upi_commision',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('upi_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/upiCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'commission' => $post['commision'],	
			 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('upi_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/upiCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}


	public function saveUpiCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('commision', 'Commission', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->upiCommision();
		}
		else
		{
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/upiCommision', 'system_message_error',lang('MEMBER_ERROR'));
		 	}


		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
		  	 'commission' => $post['commision'],	
		  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('upi_commision',$data);
	 	  	
					
			 	

		 	$this->Az->redirect('employe/master/upiCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}


	public function upiCashCommision(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/upi-cash-commission',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getUpiCashCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
	 		// get commission
			$get_com_data = $this->db->get_where('upi_cash_commision',array('account_id'=>$account_id,'package_id'=>$member_id))->row_array();
			
			$md_commision = isset($get_com_data['md_commision']) ? $get_com_data['md_commision'] : 0 ;
			$dt_commision = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
			$rt_commision = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
			$user_commision = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
			$api_commision = isset($get_com_data['api_commision']) ? $get_com_data['api_commision'] : 0 ;
			$is_flat = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
			$is_surcharge = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;

			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th colspan="4"></th>';
			$str.='<th colspan="5">Commission</th>';
			$str.='</tr>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			
        	$str.='<tr>';
        	$str.='<td>1</td>';
        	$str.='<td>UPI Cash</td>';
        	$str.='<td><input type="text" name="md_commision" class="form-control" value="'.$md_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="dt_commision" class="form-control" value="'.$dt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="rt_commision" class="form-control" value="'.$rt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	if($accountData['is_disable_user_role'] != 1)
			{
        		$str.='<td><input type="text" name="user_commision" class="form-control" value="'.$user_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}
        	if($accountData['is_disable_api_role'] != 1)
			{
        		$str.='<td><input type="text" name="api_commision" class="form-control" value="'.$api_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}

        	$str.='<td>';
        	if($is_flat == 1){
        		$str.='<input type="checkbox" id="is_flat_1" checked="checked" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	else
        	{
        		$str.='<input type="checkbox" id="is_flat_1" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	
        	if($is_surcharge == 1){
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" checked="checked" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	else
        	{
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	$str.='</td>';
        	

        	$str.='</tr>';
                	
                
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}


	public function saveUpiCashCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/upiCashCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 	}



 		// check commision saved or not
 		$chk_data = $this->db->get_where('upi_cash_commision',array('account_id'=>$account_id,'package_id'=>$memberID))->num_rows();
 		if($chk_data)
 		{
 			$data = array(
	 	  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'updated_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->where('account_id',$account_id);
	 	  	$this->db->where('package_id',$memberID);
	 	  	$this->db->update('upi_cash_commision',$data);
 		}
 		else
 		{
		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('upi_cash_commision',$data);
 	  	}
				
		 	

	 	$this->Az->redirect('employe/master/upiCashCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}

	public function gatewayCharge(){

		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(45,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}
		

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$accountData = $this->User->get_account_data($account_id);
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'accountData' => $accountData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/gateway-charge'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveGatewayCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE) {
			
			$this->gatewayCharge();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/gatewayCharge', 'system_message_error',lang('MEMBER_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_commision' => $post['md_commision'],	
	 	  	 'dt_commision' => $post['dt_commision'],	
	 	  	 'rt_commision' => $post['rt_commision'],	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => 1,	
	 	  	 'created_by' => $loggedUser['id']	
	 	  	);

	 	  	$this->db->insert('gateway_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/gatewayCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberGatewayChargeData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('gateway_charge',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['md_commision'].'</td>';
                	$str.='<td>'.$list['dt_commision'].'</td>';
                	$str.='<td>'.$list['rt_commision'].'</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
                		$str.='<td>'.$list['user_commision'].'</td>';
                	}
                	if($accountData['is_disable_api_role'] != 1)
					{
                		$str.='<td>'.$list['api_commision'].'</td>';
                	}
                	
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateGatewayModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteGatewayCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="12" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateGatewayCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/gatewayCharge', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];
	 	 	
	 		$chk_member = $this->db->get_where('gateway_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/gatewayCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
	 		
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_commision' => $post['md_commision'],	
	 	  	 'dt_commision' => $post['dt_commision'],	
	 	  	 'rt_commision' => $post['rt_commision'],	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('gateway_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/gatewayCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteGatewayCharge($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('gateway_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/gatewayCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		
 		$this->db->where('id',$recordID);
 		$this->db->delete('gateway_charge');
 		$this->Az->redirect('employe/master/gatewayCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getGatewayChargeData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('gateway_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('gateway_charge',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>MD</label>';
	        $str.='<input type="text" autocomplete="off" name="md_commision" class="form-control" value="'.$dmrData['md_commision'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>DT</label>';
	        $str.='<input type="text" autocomplete="off" name="dt_commision" class="form-control" value="'.$dmrData['dt_commision'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>RT</label>';
	        $str.='<input type="text" autocomplete="off" name="rt_commision" class="form-control" value="'.$dmrData['rt_commision'].'">';
	        $str.='</div>';
	        if($accountData['is_disable_user_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>User</label>';
		        $str.='<input type="text" autocomplete="off" name="user_commision" class="form-control" value="'.$dmrData['user_commision'].'">';
		        $str.='</div>';
		    }
		    if($accountData['is_disable_api_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>API</label>';
		        $str.='<input type="text" autocomplete="off" name="api_commision" class="form-control" value="'.$dmrData['api_commision'].'">';
		        $str.='</div>';
		    }
	        
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}


	public function dmtCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$accountData = $this->User->get_account_data($account_id);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'accountData' => $accountData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/dmt-charge'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveDmtCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE) {
			
			$this->dmtCharge();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/dmtCharge', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_user' => isset($post['is_user']) ? $post['is_user'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('dmt_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/dmtCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberDmtChargeData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('dmt_commision',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['md_charge'].'</td>';
                	$str.='<td>'.$list['dt_charge'].'</td>';
                	$str.='<td>'.$list['rt_charge'].'</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
                		$str.='<td>'.$list['user_charge'].'</td>';
                	}
                	if($accountData['is_disable_api_role'] != 1)
					{
                		$str.='<td>'.$list['api_charge'].'</td>';
                	}
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateDmtChargeModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteDmtCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="9" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateDmtCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/dmtCharge', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('dmt_commision',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('dmt_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/dmtCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('dmt_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/dmtCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteDmtCharge($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('dmt_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/dmtCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('dmt_commision',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('dmt_commision');
 		$this->Az->redirect('employe/master/dmtCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getDmtChargeData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('dmt_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('dmt_commision',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>MD Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="md_charge" class="form-control" value="'.$dmrData['md_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>DT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="dt_charge" class="form-control" value="'.$dmrData['dt_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>RT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="rt_charge" class="form-control" value="'.$dmrData['rt_charge'].'">';
	        $str.='</div>';
	        if($accountData['is_disable_user_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>User Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="user_charge" class="form-control" value="'.$dmrData['user_charge'].'">';
		        $str.='</div>';
		    }
		    if($accountData['is_disable_api_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>API Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="api_charge" class="form-control" value="'.$dmrData['api_charge'].'">';
		        $str.='</div>';
		    }
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}

	public function utiCommision(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/utiCommision',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getUtiCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$accountData = $this->User->get_account_data($account_id);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			
			// get commission
			$get_com_data = $this->db->get_where('uti_pancard_commision',array('account_id'=>$account_id,'package_id'=>$member_id))->row_array();
			$md_commision = isset($get_com_data['md_commision']) ? $get_com_data['md_commision'] : 0 ;
			$dt_commision = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
			$rt_commision = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
			$api_commision = isset($get_com_data['api_commision']) ? $get_com_data['api_commision'] : 0 ;
			$user_commision = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
			$is_flat = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='</tr></thead>';
			$str.='<tbody>';
			    $i=1;
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>Uti Pancard</td>';
                	$str.='<td><input type="text" name="md_commission" class="form-control" value="'.$md_commision.'"></td> ';
                	$str.='<td><input type="text" name="dt_commission" class="form-control" value="'.$dt_commision.'"></td> ';
                	$str.='<td><input type="text" name="rt_commission" class="form-control" value="'.$rt_commision.'"></td> ';
                	if($accountData['is_disable_user_role'] != 1)
					{
                		$str.='<td><input type="text" name="user_commission" class="form-control" value="'.$user_commision.'"></td> ';
                	}
                	if($accountData['is_disable_api_role'] != 1)
					{
                		$str.='<td><input type="text" name="api_commission" class="form-control" value="'.$api_commision.'"></td> ';
                	}
                	
                	$str.='</tr>';
                	$i++;
                
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}


	public function saveUtiCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());

 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/utiCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 	}



 		// check commision saved or not
 		$chk_data = $this->db->get_where('uti_pancard_commision',array('account_id'=>$account_id,'package_id'=>$memberID))->num_rows();
 		if($chk_data)
 		{
 			$data = array(
	 	  	 'md_commision' => isset($post['md_commission']) ? $post['md_commission'] : 0,
	 	  	 'dt_commision' => isset($post['dt_commission']) ? $post['dt_commission'] : 0,
	 	  	 'rt_commision' => isset($post['rt_commission']) ? $post['rt_commission'] : 0,
	 	  	 'api_commision' => isset($post['api_commission']) ? $post['api_commission'] : 0,
	 	  	 'user_commision' => isset($post['user_commission']) ? $post['user_commission'] : 0,	
	 	  	 'is_flat' => 1,	
	 	  	 'is_surcharge' => 0,	
	 	  	 'updated_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->where('account_id',$account_id);
	 	  	$this->db->where('package_id',$memberID);
	 	  	$this->db->update('uti_pancard_commision',$data);
 		}
 		else
 		{
		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'md_commision' => isset($post['md_commission']) ? $post['md_commission'] : 0,
	 	  	 'dt_commision' => isset($post['dt_commission']) ? $post['dt_commission'] : 0,
	 	  	 'rt_commision' => isset($post['rt_commission']) ? $post['rt_commission'] : 0,
	 	  	 'api_commision' => isset($post['api_commission']) ? $post['api_commission'] : 0,
	 	  	 'user_commision' => isset($post['user_commission']) ? $post['user_commission'] : 0,	
	 	  	 'is_flat' => 1,	
	 	  	 'is_surcharge' => 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('uti_pancard_commision',$data);
 	  	}
				
		 	

	 	$this->Az->redirect('employe/master/utiCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}

	public function moveMember(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/moveMember',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getMemberTypeList($memberType = 0)
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$str = '<option value="0">Select Member</option>';
	 	if($memberType == 4 || $memberType == 5 || $memberType == 3)
	 	{
	 		// get member list
	 		$memberList = $this->db->select('id,user_code,name')->get_where('users',array('account_id'=>$account_id,'role_id'=>$memberType))->result_array();
	 		if($memberList)
	 		{
	 			foreach($memberList as $list)
	 			{
	 				$str.='<option value="'.$list['id'].'">'.$list['name'].' ('.$list['user_code'].')</option>';
	 			}
	 		}
	 	}
	 	echo $str;
	}

	public function moveMemberAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$post = $this->security->xss_clean($this->input->post());
	 	
	 	$memberType = isset($post['memberType']) ? $post['memberType'] : 0;
	 	$memberID = isset($post['memberID']) ? $post['memberID'] : 0;
	 	$sponserType = isset($post['sponserType']) ? $post['sponserType'] : 0;
	 	$sponserID = isset($post['sponserID']) ? $post['sponserID'] : 0;
	 	if($memberType != 4 && $memberType != 5)
	 	{
	 		$this->Az->redirect('employe/master/moveMember', 'system_message_error',lang('MOVE_MEMBER_TYPE_ERROR'));
	 	}

	 	// check member valid or not
	 	$chk_member = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID,'role_id'=>$memberType))->num_rows();
	 	if(!$chk_member)
	 	{
	 		$this->Az->redirect('employe/master/moveMember', 'system_message_error',lang('MOVE_MEMBER_ID_ERROR'));
	 	}

	 	if($memberType == 4 && $sponserType != 3)
	 	{
	 		$this->Az->redirect('employe/master/moveMember', 'system_message_error',lang('MOVE_MEMBER_SPONSER_TYPE_ERROR'));
	 	}


	 	if($memberType == 5 && $sponserType != 3 && $sponserType != 4)
	 	{

	 		$this->Az->redirect('employe/master/moveMember', 'system_message_error',lang('MOVE_MEMBER_SPONSER_TYPE_ERROR'));
	 	}

	 	// check sponser valid or not
	 	$chk_sponser = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$sponserID,'role_id'=>$sponserType))->num_rows();
	 	if(!$chk_sponser)
	 	{
	 		$this->Az->redirect('employe/master/moveMember', 'system_message_error',lang('MOVE_MEMBER_SPONSER_ID_ERROR'));
	 	}

	 	// check member valid or not
	 	$getMemberSponser = $this->db->select('created_by')->get_where('users',array('account_id'=>$account_id,'id'=>$memberID,'role_id'=>$memberType))->row_array();
	 	$last_sponser_id = isset($getMemberSponser['created_by']) ? $getMemberSponser['created_by'] : 0;

	 	if($last_sponser_id == $sponserID)
	 	{
	 		$this->Az->redirect('employe/master/moveMember', 'system_message_error',lang('MOVE_MEMBER_SAME_SPONSER_ERROR'));
	 	}

	 	// get last sponser role id
	 	$getLastSponser = $this->db->select('role_id')->get_where('users',array('account_id'=>$account_id,'id'=>$last_sponser_id))->row_array();
	 	$last_sponser_role_id = isset($getLastSponser['role_id']) ? $getLastSponser['role_id'] : 0;

	 	$data = array(
	 		'account_id' => $account_id,
	 		'move_member_id' => $memberID,
	 		'last_sponser_role_id' => $last_sponser_role_id,
	 		'last_sponser_id' => $last_sponser_id,
	 		'new_sponser_role_id' => $sponserType,
	 		'new_sponser_id' => $sponserID,
	 		'created' => date('Y-m-d H:i:s')
	 	);
	 	$this->db->insert('move_member_history',$data);

	 	// update member sponser
	 	$this->db->where('id',$memberID);
	 	$this->db->where('account_id',$account_id);
	 	$this->db->update('users',array('created_by'=>$sponserID));

	 	$this->Az->redirect('employe/master/moveMember', 'system_message_error',lang('MOVE_MEMBER_SUCCESS'));
	}

	public function accountVerifyCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/account-verify-charge'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveAccountVerifyCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->accountVerifyCharge();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/accountVerifyCharge', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}

		 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'surcharge' => $post['surcharge'],	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('dmr_account_verify_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/accountVerifyCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberAccountVerifyChargeData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('dmr_account_verify_charge',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>Charge</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>Account Verify Charge</td>';
                	$str.='<td>'.$list['surcharge'].'</td>';
                	
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateAccountVerifyChargeModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteAccountVerifyCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="7" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>Charge</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateAccountVerifyCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/accountVerifyCharge', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('dmr_account_verify_charge',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('dmr_account_verify_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/accountVerifyCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'surcharge' => $post['surcharge'],	
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('dmr_account_verify_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/accountVerifyCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteAccountVerifyCharge($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('dmr_account_verify_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/accountVerifyCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('dmr_account_verify_charge',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('dmr_account_verify_charge');
 		$this->Az->redirect('employe/master/accountVerifyCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getAccountVerifyChargeData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('dmr_account_verify_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('dmr_account_verify_charge',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str.='<div class="form-group">';
	        $str.='<label>Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="surcharge" class="form-control" value="'.$dmrData['surcharge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'surcharge' => $dmrData['surcharge'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}

	public function nsdlPancardCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/nsdl-pancard-charge'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveNsdlPancardCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->nsdlPancardCharge();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/nsdlPancardCharge', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}

		 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'surcharge' => $post['surcharge'],	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('nsdl_pancard_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/nsdlPancardCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberNsdlPancardChargeData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('nsdl_pancard_charge',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>Charge</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>NSDL Pancard Charge</td>';
                	$str.='<td>'.$list['surcharge'].'</td>';
                	
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateNsdlPancardChargeModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteNsdlPancardCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="7" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>Charge</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateNsdlPancardCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/nsdlPancardCharge', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('nsdl_pancard_charge',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('nsdl_pancard_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/nsdlPancardCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'surcharge' => $post['surcharge'],	
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('nsdl_pancard_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/nsdlPancardCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteNsdlPancardCharge($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('nsdl_pancard_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/nsdlPancardCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('nsdl_pancard_charge',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('nsdl_pancard_charge');
 		$this->Az->redirect('employe/master/nsdlPancardCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getNsdlPancardChargeData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('nsdl_pancard_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('nsdl_pancard_charge',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str.='<div class="form-group">';
	        $str.='<label>Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="surcharge" class="form-control" value="'.$dmrData['surcharge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'surcharge' => $dmrData['surcharge'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}


	public function payoutOtpSetting(){

		//get logged user info
       	$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];


        $walletData = $this->db->get_where('payout_master_setting',array('id'=>1))->row_array();
		
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'walletData' => $walletData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/payout-amount-setting'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function payoutSaveOtp()
	{
		//check for foem validation
		$post = $this->input->post();
		
			
		$this->Master_model->savePayoutOtpSetting($post);
		$this->Az->redirect('employe/master/payoutOtpSetting', 'system_message_error',lang('WALLET_SETTING_SUCCESS'));
		
	
	}
	
	public function panActivationCharge(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/pan-activation-charge',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getPanActivationCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
	 		// get commission
			$get_com_data = $this->db->get_where('pan_activation_charge',array('account_id'=>$account_id,'package_id'=>$member_id))->row_array();
			
			$md_commision = isset($get_com_data['md_commision']) ? $get_com_data['md_commision'] : 0 ;
			$dt_commision = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
			$rt_commision = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
			$user_commision = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
			$api_commision = isset($get_com_data['api_commision']) ? $get_com_data['api_commision'] : 0 ;
			$is_flat = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
			$is_surcharge = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;

			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th colspan="4"></th>';
			$str.='<th colspan="5">Commission</th>';
			$str.='</tr>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			
        	$str.='<tr>';
        	$str.='<td>1</td>';
        	$str.='<td>PAN ID ACTIVATION</td>';
        	$str.='<td><input type="text" name="md_commision" class="form-control" value="'.$md_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="dt_commision" class="form-control" value="'.$dt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="rt_commision" class="form-control" value="'.$rt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	if($accountData['is_disable_user_role'] != 1)
			{
        		$str.='<td><input type="text" name="user_commision" class="form-control" value="'.$user_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}
        	if($accountData['is_disable_api_role'] != 1)
			{
        		$str.='<td><input type="text" name="api_commision" class="form-control" value="'.$api_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}

        	$str.='<td>';
        	if($is_flat == 1){
        		$str.='<input type="checkbox" id="is_flat_1" checked="checked" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	else
        	{
        		$str.='<input type="checkbox" id="is_flat_1" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	
        	if($is_surcharge == 1){
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" checked="checked" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	else
        	{
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	$str.='</td>';
        	

        	$str.='</tr>';
                	
                
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}


	public function savePanActivationCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/panActivationCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 	}



 		// check commision saved or not
 		$chk_data = $this->db->get_where('pan_activation_charge',array('account_id'=>$account_id,'package_id'=>$memberID))->num_rows();
 		if($chk_data)
 		{
 			$data = array(
	 	  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'updated_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->where('account_id',$account_id);
	 	  	$this->db->where('package_id',$memberID);
	 	  	$this->db->update('pan_activation_charge',$data);
 		}
 		else
 		{
		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('pan_activation_charge',$data);
 	  	}
				
		 	

	 	$this->Az->redirect('employe/master/panActivationCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}


	public function findPanCharge(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/find-pan-charge',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getFindPanCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
	 		// get commission
			$get_com_data = $this->db->get_where('find_pan_charge',array('account_id'=>$account_id,'package_id'=>$member_id))->row_array();
			
			$md_commision = isset($get_com_data['md_commision']) ? $get_com_data['md_commision'] : 0 ;
			$dt_commision = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
			$rt_commision = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
			$user_commision = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
			$api_commision = isset($get_com_data['api_commision']) ? $get_com_data['api_commision'] : 0 ;
			$is_flat = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
			$is_surcharge = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;

			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th colspan="4"></th>';
			$str.='<th colspan="5">Commission</th>';
			$str.='</tr>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			
        	$str.='<tr>';
        	$str.='<td>1</td>';
        	$str.='<td>FIND PAN</td>';
        	$str.='<td><input type="text" name="md_commision" class="form-control" value="'.$md_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="dt_commision" class="form-control" value="'.$dt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="rt_commision" class="form-control" value="'.$rt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	if($accountData['is_disable_user_role'] != 1)
			{
        		$str.='<td><input type="text" name="user_commision" class="form-control" value="'.$user_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}
        	if($accountData['is_disable_api_role'] != 1)
			{
        		$str.='<td><input type="text" name="api_commision" class="form-control" value="'.$api_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}

        	$str.='<td>';
        	if($is_flat == 1){
        		$str.='<input type="checkbox" id="is_flat_1" checked="checked" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	else
        	{
        		$str.='<input type="checkbox" id="is_flat_1" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	
        	if($is_surcharge == 1){
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" checked="checked" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	else
        	{
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	$str.='</td>';
        	

        	$str.='</tr>';
                	
                
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}


	public function saveFindPanCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/findPanCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 	}



 		// check commision saved or not
 		$chk_data = $this->db->get_where('find_pan_charge',array('account_id'=>$account_id,'package_id'=>$memberID))->num_rows();
 		if($chk_data)
 		{
 			$data = array(
	 	  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'updated_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->where('account_id',$account_id);
	 	  	$this->db->where('package_id',$memberID);
	 	  	$this->db->update('find_pan_charge',$data);
 		}
 		else
 		{
		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('find_pan_charge',$data);
 	  	}
				
		 	

	 	$this->Az->redirect('admin/master/findPanCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}

	//morningpay nsdl pan charge

	public function panCharge(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/pan-charge',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getPanCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
	 		// get commission
			$get_com_data = $this->db->get_where('pan_charge',array('account_id'=>$account_id,'package_id'=>$member_id))->row_array();
			
			$md_commision = isset($get_com_data['md_commision']) ? $get_com_data['md_commision'] : 0 ;
			$dt_commision = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
			$rt_commision = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
			$user_commision = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
			$api_commision = isset($get_com_data['api_commision']) ? $get_com_data['api_commision'] : 0 ;
			$is_flat = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
			$is_surcharge = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;

			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th colspan="4"></th>';
			$str.='<th colspan="5">Commission</th>';
			$str.='</tr>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			
        	$str.='<tr>';
        	$str.='<td>1</td>';
        	$str.='<td>PAN Card Charge</td>';
        	$str.='<td><input type="text" name="md_commision" class="form-control" value="'.$md_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="dt_commision" class="form-control" value="'.$dt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="rt_commision" class="form-control" value="'.$rt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	if($accountData['is_disable_user_role'] != 1)
			{
        		$str.='<td><input type="text" name="user_commision" class="form-control" value="'.$user_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}
        	if($accountData['is_disable_api_role'] != 1)
			{
        		$str.='<td><input type="text" name="api_commision" class="form-control" value="'.$api_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}

        	$str.='<td>';
        	if($is_flat == 1){
        		$str.='<input type="checkbox" id="is_flat_1" checked="checked" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	else
        	{
        		$str.='<input type="checkbox" id="is_flat_1" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	
        	if($is_surcharge == 1){
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" checked="checked" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	else
        	{
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	$str.='</td>';
        	

        	$str.='</tr>';
                	
                
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}


	public function savePanCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/panCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 	}



 		// check commision saved or not
 		$chk_data = $this->db->get_where('pan_charge',array('account_id'=>$account_id,'package_id'=>$memberID))->num_rows();
 		if($chk_data)
 		{
 			$data = array(
	 	  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'updated_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->where('account_id',$account_id);
	 	  	$this->db->where('package_id',$memberID);
	 	  	$this->db->update('find_pan_charge',$data);
 		}
 		else
 		{
		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('pan_charge',$data);
 	  	}
				
		 	

	 	$this->Az->redirect('employe/master/panCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}


	public function xpressPayoutCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	 	$accountData = $this->User->get_account_data($account_id);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'accountData' => $accountData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/xpress-payout-charge'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveXpressPayoutCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE) {
			
			$this->xpressPayoutCharge();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('admin/master/xpressPayoutCharge', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_user' => isset($post['is_user']) ? $post['is_user'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('xpress_payout_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/xpressPayoutCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberXpressPayoutChargeData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('xpress_payout_charge',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['md_charge'].'</td>';
                	$str.='<td>'.$list['dt_charge'].'</td>';
                	$str.='<td>'.$list['rt_charge'].'</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
                		$str.='<td>'.$list['user_charge'].'</td>';
                	}
                	if($accountData['is_disable_api_role'] != 1)
					{
                		$str.='<td>'.$list['api_charge'].'</td>';
                	}
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateXpressPayoutChargeModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteXpressPayoutCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="7" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateXpressPayoutCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/xpressPayoutCharge', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('xpress_payout_charge',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('xpress_payout_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/xpressPayoutCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('xpress_payout_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/xpressPayoutCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteXpressPayoutCharge($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('xpress_payout_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/xpressPayoutCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('xpress_payout_charge',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('xpress_payout_charge');
 		$this->Az->redirect('employe/master/xpressPayoutCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getXpressPayoutChargeData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('xpress_payout_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('xpress_payout_charge',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>MD Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="md_charge" class="form-control" value="'.$dmrData['md_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>DT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="dt_charge" class="form-control" value="'.$dmrData['dt_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>RT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="rt_charge" class="form-control" value="'.$dmrData['rt_charge'].'">';
	        $str.='</div>';
	        if($accountData['is_disable_user_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>User Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="user_charge" class="form-control" value="'.$dmrData['user_charge'].'">';
		        $str.='</div>';
		    }
		    if($accountData['is_disable_api_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>API Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="api_charge" class="form-control" value="'.$dmrData['api_charge'].'">';
		        $str.='</div>';
		    }
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}


	public function referralCommision(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$memberList = $this->db->get_where('users',array('account_id'=>$account_id,'role_id'=>6))->result_array();

		$recordList = $this->db->select('referral_commision.*,a.name as fromMemberName,b.name as toMemberName')->join('users as a','a.id = referral_commision.from_member_id')->join('users as b','b.id = referral_commision.to_member_id')->get_where('referral_commision',array('referral_commision.account_id'=>$account_id))->result_array();


		$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
		$str.='<thead>';
		$str.='<tr>';
		$str.='<th>#</th>';
		$str.='<th>Service</th>';
		$str.='<th>From Member</th>';
		$str.='<th>To Member</th>';
		$str.='<th>Start Range</th>';
		$str.='<th>End Range</th>';
		$str.='<th>Commission</th>';
		$str.='<th>Is Flat ?</th>';
		$str.='<th>Is Surcharge ?</th>';
		$str.='<th>Action</th>';
		$str.='</tr></thead>';
		$str.='<tbody>';
		if($recordList){
            $i=1;
            foreach($recordList as $key=>$list){
            	$str.='<tr>';
            	$str.='<td>'.$i.'</td>';
            	if($list['service_id'] == 5)
            	{
            		$str.='<td>UPI Collection</td>';
            	}
            	else
            	{
            		$str.='<td>Payout</td>';
            	}
            	$str.='<td>'.$list['fromMemberName'].'</td>';
            	$str.='<td>'.$list['toMemberName'].'</td>';
            	$str.='<td>'.$list['start_range'].'</td>';
            	$str.='<td>'.$list['end_range'].'</td>';
            	$str.='<td>'.$list['commission'].'</td>';
            	if($list['is_flat'] == 1){
            		$str.='<td><font color="green">Yes</font></td>';
            	}
            	else
            	{
            		$str.='<td><font color="red">No</font></td>';
            	}
            	if($list['is_surcharge'] == 1){
            		$str.='<td><font color="green">Yes</font></td>';
            	}
            	else
            	{
            		$str.='<td><font color="red">No</font></td>';
            	}
            	$str.='<td><a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteRefferalCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
            	
            	$str.='</tr>';
            	$i++;
            }
        }
        else
        {
        	$str.='<tr><td colspan="9" align="center">No Record Found.</td></tr>';
        }
        $str.='</tbody><tfoot>';
        $str.='<tr>';
		$str.='<th>#</th>';
		$str.='<th>Service</th>';
		$str.='<th>From Member</th>';
		$str.='<th>To Member</th>';
		$str.='<th>Start Range</th>';
		$str.='<th>End Range</th>';
		$str.='<th>Commission</th>';
		$str.='<th>Is Flat ?</th>';
		$str.='<th>Is Surcharge ?</th>';
		$str.='<th>Action</th>';
		$str.='</tr></tfoot>';
		$str.='</table>';

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/referral-commission',
            'memberList' => $memberList,
            'str' => $str,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function saveReferralCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('serviceID', 'Service', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('fromMemberID', 'From Member', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('toMemberID', 'To Member', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('commision', 'Commission', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->referralCommision();
		}
		else
		{
	 	 	
		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'service_id' => $post['serviceID'],
		  	 'from_member_id' => $post['fromMemberID'],
		  	 'to_member_id' => $post['toMemberID'],
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
		  	 'commission' => $post['commision'],	
		  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('referral_commision',$data);
	 	  	
					
			 	

		 	$this->Az->redirect('employe/master/referralCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function ipsetting(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$userList = $this->db->where_in('role_id',array(6))->get_where('users',array('account_id'=>$account_id))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/ipsetting',
            'operatorList'	=> $operatorList,
            'userList' => $userList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	

	}

	public function saveIpAuth(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->input->post();	

 	 	$ipaddress = $post['ipaddress'];
 	 	if(!$ipaddress)
	 	{
	 		$this->Az->redirect('employe/master/ipsetting', 'system_message_error',lang('MEMBER_ERROR'));
	 	}
 	 	

 		foreach ($ipaddress as $memberID=>$value) {
 			
			$this->db->where('account_id',$account_id);
	 	  	$this->db->where('id',$memberID);
	 	  	$this->db->update('users',array('whitelist_ip'=>trim($value)));
	 		
 		}
	 		
	 	
	 	$this->Az->redirect('employe/master/ipsetting', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}



	//money transfer 2 Charge

	public function newMoneyTransferCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$accountData = $this->User->get_account_data($account_id);
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();


		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(132,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'accountData' => $accountData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/new-money-transfer-charge'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveNewMoneyTransferCharge(){

		if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(132,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'Commission Type', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			
			$this->newMoneyTransferCharge();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/newMoneyTransferCharge', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_user' => isset($post['is_user']) ? $post['is_user'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'com_type' => $post['com_type'],
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('new_money_transfer_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/newMoneyTransferCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberNewMoneyTransferCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('new_money_transfer_charge',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();

			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['md_charge'].'</td>';
                	$str.='<td>'.$list['dt_charge'].'</td>';
                	$str.='<td>'.$list['rt_charge'].'</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
                		$str.='<td>'.$list['user_charge'].'</td>';
                	}
                	if($accountData['is_disable_api_role'] != 1)
					{
                		$str.='<td>'.$list['api_charge'].'</td>';
                	}
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}

                	if($list['com_type'] == 'NEFT')
					{
                		$str.='<td>NEFT</td>';
                	}
                	elseif($list['com_type'] == 'RTGS')
					{
                		$str.='<td>RTGS</td>';
                	}
                	elseif($list['com_type'] == 'IMPS')
					{
                		$str.='<td>IMPS</td>';
                	}
                	elseif($list['com_type'] == 'UPI')
					{
                		$str.='<td>UPI</td>';
                	}
                	else
					{
                		$str.='<td>Not Available</td>';
                	}
                	
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateNewMoneyTransferCharge('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteNewMoneyTransferCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="7" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateNewMoneyTransferCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'End Range', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/newMoneyTransferCharge', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('new_money_transfer_charge',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('new_money_transfer_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/newMoneyTransferCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'com_type' => $post['com_type'],
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('new_money_transfer_charge',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/newMoneyTransferCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteNewMoneyTransferCharge($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('new_money_transfer_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/newMoneyTransferCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('new_money_transfer_charge',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('new_money_transfer_charge');
 		$this->Az->redirect('employe/master/newMoneyTransferCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getNewMoneyTransferCommData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('new_money_transfer_charge',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('new_money_transfer_charge',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>MD Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="md_charge" class="form-control" value="'.$dmrData['md_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>DT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="dt_charge" class="form-control" value="'.$dmrData['dt_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>RT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="rt_charge" class="form-control" value="'.$dmrData['rt_charge'].'">';
	        $str.='</div>';
	        if($accountData['is_disable_user_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>User Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="user_charge" class="form-control" value="'.$dmrData['user_charge'].'">';
		        $str.='</div>';
		    }
		    if($accountData['is_disable_api_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>API Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="api_charge" class="form-control" value="'.$dmrData['api_charge'].'">';
		        $str.='</div>';
		    }
		    $str.='<div class="form-group">';
	        $str.='<label><b>Commission Type*</b></label>';
	        $str.='<select class="form-control" name="com_type">';
	        $str.='<option value="">Select</option>';
	        if($dmrData['com_type'] == 'NEFT')
	        {
	        	$str.='<option value="NEFT" selected="selected">NEFT</option>';
	        	$str.='<option value="RTGS">RTGS</option>';
	        	$str.='<option value="IMPS">IMPS</option>';
	        	$str.='<option value="UPI">UPI</option>';
	        }
	    	elseif($dmrData['com_type'] == 'RTGS')
	    	{
	    		$str.='<option value="NEFT">NEFT</option>';
	        	$str.='<option value="RTGS" selected="selected">RTGS</option>';
	        	$str.='<option value="IMPS">IMPS</option>';
	        	$str.='<option value="UPI">UPI</option>';
	    	}
	    	elseif($dmrData['com_type'] == 'IMPS')
	    	{
	    		$str.='<option value="NEFT">NEFT</option>';
	        	$str.='<option value="RTGS">RTGS</option>';
	        	$str.='<option value="IMPS" selected="selected">IMPS</option>';
	        	$str.='<option value="UPI">UPI</option>';
	    	}
	    	elseif($dmrData['com_type'] == 'UPI')
	    	{
	    		$str.='<option value="NEFT">NEFT</option>';
	        	$str.='<option value="RTGS">RTGS</option>';
	        	$str.='<option value="IMPS">IMPS</option>';
	        	$str.='<option value="UPI" selected="selected">UPI</option>';
	    	}
	    	else
	    	{
	    		$str.='<option value="NEFT">NEFT</option>';
	        	$str.='<option value="RTGS">RTGS</option>';
	        	$str.='<option value="IMPS">IMPS</option>';
	        	$str.='<option value="UPI">UPI</option>';
	    	}
	        $str.='</select>';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}
	
	
	
	//upi api swtiching

	public function upiApiSwitch(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);


	 	if(!$this->User->admin_menu_permission(5,1) || !$this->User->admin_menu_permission(139,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		
		$userList = $this->db->where_in('role_id',array(3,4,5,6,8))->get_where('users',array('account_id'=>$account_id))->result_array();

		$apiList = $this->db->get('upi_api')->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/upiApiSwitch',
            'userList' => $userList,
            'apiList' => $apiList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	

	}



	public function getUpiApiData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id > 0)
	 	{
	 		$chk_member = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$is_error = 1;
	 			
	 		}
	 	}

	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Member is not valid.'
	 			);
	 	}
	 	else
	 	{
			$apiList = $this->db->get('upi_api')->result_array();


			$str = '<div class="table-responsive">';
			$str.= '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Api</th>';
			$str.='<th>Active/Deactive</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($apiList){
                $i=1;
                foreach($apiList as $key=>$list){

                	$is_active = $this->db->get_where('member_upi_active_api',array('account_id'=>$account_id,'user_id'=>$member_id,'api_id'=>$list['id']))->num_rows();

                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['title'].'</td>';
                	if($is_active){

                		$str.='<td><input type="radio" checked="checked" name="service_id" value="'.$list['id'].'"></td>';
                	}
                	else
                	{
                		$str.='<td><input type="radio" name="service_id" value="'.$list['id'].'"></td>';
                	}
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Api</th>';
			$str.='<th>Active/Deactive</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';
			$str.='</div>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	public function saveUpiApiAuth(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->input->post();	

 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('employe/master/upiApiSwitch', 'system_message_error',lang('MEMBER_ERROR'));
	 	}
 	 	if($memberID > 0)
	 	{
	 		$chk_member = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/upiApiSwitch', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
	 	}

	 	

	 	if(isset($post['service_id']))
	 	{	
	 		$this->db->where('account_id',$account_id);
	 		$this->db->where('user_id',$memberID);
	 		$this->db->delete('member_upi_active_api');

	 		$data = array(
	 			'account_id' =>$account_id,
	 			'user_id' => $memberID,
	 			'api_id' => $post['service_id'],
	 			'created' => date('Y-m-d h:i:s')
	 		);

	 		$this->db->insert('member_upi_active_api',$data);		

	 	}
	 	
	 	
	 	$this->Az->redirect('employe/master/upiApiSwitch', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}
	

	//scan and pay charge setting

	public function scanPayCommision(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/scan-pay-commission',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getScanPayCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('scan_pay_commision',array('scan_pay_commision.account_id'=>$account_id,'scan_pay_commision.package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>User Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['commission'].'</td>';
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	$str2 = '';
                	$is_val = 0;
                	if($list['is_md'])
                	{
                		$str2.='MD';
                		$is_val = 1;
                	}
                	if($list['is_dt'])
                	{
                		$str2.='DT';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	if($list['is_rt'])
                	{
                		$str2.='RT';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	if($list['is_api'])
                	{
                		$str2.='API';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	

                	$str.='<td>'.$str2.'</td>';

                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateScanPayModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteScanPayCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="9" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>User Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);
	}



	public function saveScanPayCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	$post = $this->input->post();	
 	 	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('commision', 'Commission', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->upiCommision();
		}
		else
		{
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/scanPayCommision', 'system_message_error',lang('MEMBER_ERROR'));
		 	}


		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
		  	 'commission' => $post['commision'],	
		  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('scan_pay_commision',$data);
	 	  	
		 	$this->Az->redirect('employe/master/scanPayCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function updateScanPayCom(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('commision', 'Commission', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/scanPayCommision', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('scan_pay_commision',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('scan_pay_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/scanPayCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'commission' => $post['commision'],	
			 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('scan_pay_commision',$data);
	 	  	
		 	$this->Az->redirect('employe/master/scanPayCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}


	public function getUpiScanPayCommData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('scan_pay_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			
 			$dmrData = $this->db->get_where('scan_pay_commision',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>Commission*</label>';
	        $str.='<input type="text" autocomplete="off" name="commision" class="form-control" value="'.$dmrData['commission'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_surcharge'])
	        {
	        	$str.='<input type="checkbox" name="is_surcharge" checked="checked" id="is_surchargee" value="1"> <label for="is_surchargee"><b>Is Surcharge</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_surcharge" id="is_surchargee" value="1"> <label for="is_surchargee"><b>Is Surcharge</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}


	public function deleteScanPayCom($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('scan_pay_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/scanPayCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('scan_pay_commision',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('scan_pay_commision');
 		$this->Az->redirect('employe/master/scanPayCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}



	//open money payout surcharge

	public function openMoneyPayoutCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$accountData = $this->User->get_account_data($account_id);
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'accountData' => $accountData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/open-money-payout-charge'
        );
        $this->parser->parse('employe/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveOpenMoneyPayoutCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$account_package_id = $this->User->get_account_package_id($account_id);	

 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'Commission Type', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			
			$this->openMoneyPayoutCharge();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/openMoneyPayoutCharge', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_user' => isset($post['is_user']) ? $post['is_user'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'com_type' => $post['com_type'],
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('open_money_payout_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/openMoneyPayoutCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberOpenMoneyCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('open_money_payout_commision',array('account_id'=>$account_id,'package_id'=>$member_id))->result_array();



			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['md_charge'].'</td>';
                	$str.='<td>'.$list['dt_charge'].'</td>';
                	$str.='<td>'.$list['rt_charge'].'</td>';
                	if($accountData['is_disable_user_role'] != 1)
					{
                		$str.='<td>'.$list['user_charge'].'</td>';
                	}
                	if($accountData['is_disable_api_role'] != 1)
					{
                		$str.='<td>'.$list['api_charge'].'</td>';
                	}
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}

                	if($list['com_type'] == 'NEFT')
					{
                		$str.='<td>NEFT</td>';
                	}
                	elseif($list['com_type'] == 'RTGS')
					{
                		$str.='<td>RTGS</td>';
                	}
                	elseif($list['com_type'] == 'IMPS')
					{
                		$str.='<td>IMPS</td>';
                	}
                	elseif($list['com_type'] == 'UPI')
					{
                		$str.='<td>UPI</td>';
                	}
                	else
					{
                		$str.='<td>Not Available</td>';
                	}
                	
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateOpenMoneyPayout('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteOpenMoneyPayoutCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="7" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>MD Charge</th>';
			$str.='<th>DT Charge</th>';
			$str.='<th>RT Charge</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User Charge</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API Charge</th>';
			}
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	// save member
	public function updateOpenMoneyPayoutCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'End Range', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/openMoneyPayoutCharge', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('open_money_payout_commision',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('open_money_payout_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/openMoneyPayoutCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'md_charge' => $post['md_charge'],	
	 	  	 'dt_charge' => $post['dt_charge'],	
	 	  	 'rt_charge' => $post['rt_charge'],	
	 	  	 'user_charge' => isset($post['user_charge']) ? $post['user_charge'] : 0,	
	 	  	 'api_charge' => isset($post['api_charge']) ? $post['api_charge'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'com_type' => $post['com_type'],
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('open_money_payout_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/openMoneyPayoutCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteOpenMoneyPayoutCharge($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('open_money_payout_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/openMoneyPayoutCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('open_money_payout_commision',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('open_money_payout_commision');
 		$this->Az->redirect('employe/master/openMoneyPayoutCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getOpenMoneyPayoutCommData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('open_money_payout_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('open_money_payout_commision',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>MD Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="md_charge" class="form-control" value="'.$dmrData['md_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>DT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="dt_charge" class="form-control" value="'.$dmrData['dt_charge'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>RT Charge*</label>';
	        $str.='<input type="text" autocomplete="off" name="rt_charge" class="form-control" value="'.$dmrData['rt_charge'].'">';
	        $str.='</div>';
	        if($accountData['is_disable_user_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>User Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="user_charge" class="form-control" value="'.$dmrData['user_charge'].'">';
		        $str.='</div>';
		    }
		    if($accountData['is_disable_api_role'] != 1)
			{
		        $str.='<div class="form-group">';
		        $str.='<label>API Charge*</label>';
		        $str.='<input type="text" autocomplete="off" name="api_charge" class="form-control" value="'.$dmrData['api_charge'].'">';
		        $str.='</div>';
		    }
		    $str.='<div class="form-group">';
	        $str.='<label><b>Commission Type*</b></label>';
	        $str.='<select class="form-control" name="com_type">';
	        $str.='<option value="">Select</option>';
	        if($dmrData['com_type'] == 'NEFT')
	        {
	        	$str.='<option value="NEFT" selected="selected">NEFT</option>';
	        	$str.='<option value="RTGS">RTGS</option>';
	        	$str.='<option value="IMPS">IMPS</option>';
	        	$str.='<option value="UPI">UPI</option>';
	        }
	    	elseif($dmrData['com_type'] == 'RTGS')
	    	{
	    		$str.='<option value="NEFT">NEFT</option>';
	        	$str.='<option value="RTGS" selected="selected">RTGS</option>';
	        	$str.='<option value="IMPS">IMPS</option>';
	        	$str.='<option value="UPI">UPI</option>';
	    	}
	    	elseif($dmrData['com_type'] == 'IMPS')
	    	{
	    		$str.='<option value="NEFT">NEFT</option>';
	        	$str.='<option value="RTGS">RTGS</option>';
	        	$str.='<option value="IMPS" selected="selected">IMPS</option>';
	        	$str.='<option value="UPI">UPI</option>';
	    	}
	    	elseif($dmrData['com_type'] == 'UPI')
	    	{
	    		$str.='<option value="NEFT">NEFT</option>';
	        	$str.='<option value="RTGS">RTGS</option>';
	        	$str.='<option value="IMPS">IMPS</option>';
	        	$str.='<option value="UPI" selected="selected">UPI</option>';
	    	}
	    	else
	    	{
	    		$str.='<option value="NEFT">NEFT</option>';
	        	$str.='<option value="RTGS">RTGS</option>';
	        	$str.='<option value="IMPS">IMPS</option>';
	        	$str.='<option value="UPI">UPI</option>';
	    	}
	        $str.='</select>';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}


	// add fund commission

	public function addFundCommision(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/add-fund-commission',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getAddFundCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
			$recordList = $this->db->get_where('add_fund_commision',array('add_fund_commision.account_id'=>$account_id,'add_fund_commision.package_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>User Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['start_range'].'</td>';
                	$str.='<td>'.$list['end_range'].'</td>';
                	$str.='<td>'.$list['commission'].'</td>';
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	$str2 = '';
                	$is_val = 0;
                	if($list['is_md'])
                	{
                		$str2.='MD';
                		$is_val = 1;
                	}
                	if($list['is_dt'])
                	{
                		$str2.='DT';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	if($list['is_rt'])
                	{
                		$str2.='RT';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	if($list['is_api'])
                	{
                		$str2.='API';
                		$is_val = 1;
                		if($is_val)
                		{
                			$str2.=', ';
                		}
                	}
                	

                	$str.='<td>'.$str2.'</td>';

                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateAddFundModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'employe/master/deleteAddFundCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="9" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='<th>User Type</th>';
			$str.='<th>Action</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}

	public function deleteAddFundCom($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('add_fund_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('employe/master/addFundCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		// get member id
 	 	$getMemberID = $this->db->get_where('add_fund_commision',array('id'=>$recordID))->row_array();
 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
 	 	
 		$this->db->where('id',$recordID);
 		$this->db->delete('add_fund_commision');
 		$this->Az->redirect('employe/master/addFundCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}

	

	public function getAddFundMemberCommData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('add_fund_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			
 			$dmrData = $this->db->get_where('add_fund_commision',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>Commission*</label>';
	        $str.='<input type="text" autocomplete="off" name="commision" class="form-control" value="'.$dmrData['commission'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_flat'])
	        {
	        	$str.='<input type="checkbox" name="is_flat" checked="checked" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_flat" id="is_flattt" value="1"> <label for="is_flattt"><b>Is Flat</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        if($dmrData['is_surcharge'])
	        {
	        	$str.='<input type="checkbox" name="is_surcharge" checked="checked" id="is_surchargee" value="1"> <label for="is_surchargee"><b>Is Surcharge</b></label>';
	    	}
	    	else
	    	{
	    		$str.='<input type="checkbox" name="is_surcharge" id="is_surchargee" value="1"> <label for="is_surchargee"><b>Is Surcharge</b></label>';
	    	}
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<button type="submit" class="btn btn-primary">Update</button>';
	        $str.='</div>';
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'start_range' => $dmrData['start_range'],
 				'end_range' => $dmrData['end_range'],
 				'surcharge' => $dmrData['surcharge'],
 				'is_flat' => $dmrData['is_flat'],
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}

	public function updateAddFundCom(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('commision', 'Commission', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('employe/master/addFundCommision', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];

	 	 	// get member id
	 	 	$getMemberID = $this->db->get_where('add_fund_commision',array('id'=>$recordID))->row_array();
	 	 	$memberID = isset($getMemberID['member_id']) ? $getMemberID['member_id'] : 0 ;
	 	 	
	 		$chk_member = $this->db->get_where('add_fund_commision',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('employe/master/addFundCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'commission' => $post['commision'],	
			 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->where('account_id',$account_id);
	 	  	$this->db->update('add_fund_commision',$data);
	 	  	
			

		 	$this->Az->redirect('employe/master/addFundCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}


	public function saveAddFundCommission(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	$post = $this->input->post();	
 	 	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('commision', 'Commission', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->addFundCommision();
		}
		else
		{
	 	 	$memberID = $post['memberID'];
	 	 	if($memberID == 0)
		 	{
		 		$this->Az->redirect('employe/master/addFundCommision', 'system_message_error',lang('MEMBER_ERROR'));
		 	}


		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
		  	 'commission' => $post['commision'],	
		  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'is_md' => isset($post['is_md']) ? $post['is_md'] : 0,	
	 	  	 'is_dt' => isset($post['is_dt']) ? $post['is_dt'] : 0,	
	 	  	 'is_rt' => isset($post['is_rt']) ? $post['is_rt'] : 0,	
	 	  	 'is_api' => isset($post['is_api']) ? $post['is_api'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('add_fund_commision',$data);
	 	  	
					
			 	

		 	$this->Az->redirect('employe/master/addFundCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}


	// 2fa charge for fingpay aeps

	public function aepsTranscationCharge(){

		
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/aeps-transcation-charge',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);	




	}

	public function getAepsTranscationCommData($member_id = 0){

		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}

	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Package is not valid.'
	 			);
	 	}
	 	else
	 	{
	 		// get commission
			$get_com_data = $this->db->get_where('aeps_transcation_charge',array('account_id'=>$account_id,'package_id'=>$member_id))->row_array();
			
			$md_commision = isset($get_com_data['md_commision']) ? $get_com_data['md_commision'] : 0 ;
			$dt_commision = isset($get_com_data['dt_commision']) ? $get_com_data['dt_commision'] : 0 ;
			$rt_commision = isset($get_com_data['rt_commision']) ? $get_com_data['rt_commision'] : 0 ;
			$user_commision = isset($get_com_data['user_commision']) ? $get_com_data['user_commision'] : 0 ;
			$api_commision = isset($get_com_data['api_commision']) ? $get_com_data['api_commision'] : 0 ;
			$is_flat = isset($get_com_data['is_flat']) ? $get_com_data['is_flat'] : 0 ;
			$is_surcharge = isset($get_com_data['is_surcharge']) ? $get_com_data['is_surcharge'] : 0 ;

			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th colspan="2">Service</th>';
			$str.='<th colspan="6">Commission</th>';
			$str.='</tr>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			
        	$str.='<tr>';
        	$str.='<td>1</td>';
        	$str.='<td>AEPS 2FA CHARGE</td>';
        	$str.='<td><input type="text" name="md_commision" class="form-control" value="'.$md_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="dt_commision" class="form-control" value="'.$dt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	$str.='<td><input type="text" name="rt_commision" class="form-control" value="'.$rt_commision.'" style="margin-bottom:10px;">';
        	$str.='</td>';
        	if($accountData['is_disable_user_role'] != 1)
			{
        		$str.='<td><input type="text" name="user_commision" class="form-control" value="'.$user_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}
        	if($accountData['is_disable_api_role'] != 1)
			{
        		$str.='<td><input type="text" name="api_commision" class="form-control" value="'.$api_commision.'" style="margin-bottom:10px;">';
        		$str.='</td>';
        	}

        	$str.='<td>';
        	if($is_flat == 1){
        		$str.='<input type="checkbox" id="is_flat_1" checked="checked" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	else
        	{
        		$str.='<input type="checkbox" id="is_flat_1" name="is_flat" value="1"><label for="is_flat_1">Is Flat?</label>';
        	}
        	
        	if($is_surcharge == 1){
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" checked="checked" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	else
        	{
        		$str.='<br /><input type="checkbox" id="is_surcharge_1" name="is_surcharge" value="1"><label for="is_surcharge_1">Is Surcharge?</label>';
        	}
        	$str.='</td>';
        	

        	$str.='</tr>';
                	
                
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>MD</th>';
			$str.='<th>DT</th>';
			$str.='<th>RT</th>';
			if($accountData['is_disable_user_role'] != 1)
			{
				$str.='<th>User</th>';
			}
			if($accountData['is_disable_api_role'] != 1)
			{
				$str.='<th>API</th>';
			}
			$str.='<th>Commission Type</th>';
			$str.='</tr></tfoot>';
			$str.='</table>';

			$response = array(
	 				'status' => 1,
	 				'msg' => 'Success',
	 				'str' => $str
	 			);


    	}

    	echo json_encode($response);


	}


	public function saveAepsTranscationCharge(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
 	 	$post = $this->security->xss_clean($this->input->post());


 	 	$account_package_id = $this->User->get_account_package_id($account_id);	
 	 	$accountData = $this->User->get_account_data($account_id);
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('admin/master/aepsTranscationCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 	}



 		// check commision saved or not
 		$chk_data = $this->db->get_where('aeps_transcation_charge',array('account_id'=>$account_id,'package_id'=>$memberID))->num_rows();
 		if($chk_data)
 		{
 			$data = array(
	 	  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'updated_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->where('account_id',$account_id);
	 	  	$this->db->where('package_id',$memberID);
	 	  	$this->db->update('aeps_transcation_charge',$data);
 		}
 		else
 		{
		  	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'md_commision' => isset($post['md_commision']) ? $post['md_commision'] : 0,	
	 	  	 'dt_commision' => isset($post['dt_commision']) ? $post['dt_commision'] : 0,	
	 	  	 'rt_commision' => isset($post['rt_commision']) ? $post['rt_commision'] : 0,	
	 	  	 'user_commision' => isset($post['user_commision']) ? $post['user_commision'] : 0,	
	 	  	 'api_commision' => isset($post['api_commision']) ? $post['api_commision'] : 0,	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);

	 	  	$this->db->insert('aeps_transcation_charge',$data);
 	  	}
			

	 	$this->Az->redirect('employe/master/aepsTranscationCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}



	
		
	
}