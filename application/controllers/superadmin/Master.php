<?php 
class Master extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkPermission();
        $this->load->model('superadmin/Master_model');		
        $this->lang->load('superadmin/master', 'english');
        
    }

    public function commission(){

		
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();


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

        $this->parser->parse('superadmin/layout/column-1', $data);	




	}

	public function getRechargeCommData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

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
				}
			}


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Operator</th>';
			$str.='<th>Code</th>';
			$str.='<th>Type</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
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
                	$str.='<td><input type="hidden" name="op_id['.$key.']" class="form-control" value="'.$list['id'].'"><input type="text" name="commission['.$key.']" class="form-control" value="'.$list['commision'].'"></td> ';
                	if($list['is_flat'] == 1){
                		$str.='<td><input type="checkbox" checked="checked" name="is_flat['.$key.']" value="1"></td>';
                	}
                	else
                	{
                		$str.='<td><input type="checkbox" name="is_flat['.$key.']" value="1"></td>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<td><input type="checkbox" checked="checked" name="is_surcharge['.$key.']" value="1"></td>';
                	}
                	else
                	{
                		$str.='<td><input type="checkbox" name="is_surcharge['.$key.']" value="1"></td>';
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
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
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

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	
 	 	$memberID = $post['memberID'];
 	 	if($memberID == 0)
	 	{
	 		$this->Az->redirect('superadmin/master/commission', 'system_message_error',lang('MEMBER_ERROR'));
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
			 	  	 'created_by' => $loggedUser['id']
			 	  	);

			 	  	$this->db->insert('recharge_commision',$data);
		 	  	}
				
		 	}
	 	}

	 	$this->Az->redirect('superadmin/master/commission', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}

	
	public function bbpsCommission(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();


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

        $this->parser->parse('superadmin/layout/column-1', $data);	




	}

	public function getBBPSCommData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
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
				}
			}


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Operator</th>';
			$str.='<th>Code</th>';
			$str.='<th>Type</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
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
                	$str.='<td><input type="hidden" name="op_id['.$key.']" class="form-control" value="'.$list['id'].'"><input type="text" name="commission['.$key.']" class="form-control" value="'.$list['commision'].'"></td> ';
                	if($list['is_flat'] == 1){
                		$str.='<td><input type="checkbox" checked="checked" name="is_flat['.$key.']" value="1"></td>';
                	}
                	else
                	{
                		$str.='<td><input type="checkbox" name="is_flat['.$key.']" value="1"></td>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<td><input type="checkbox" checked="checked" name="is_surcharge['.$key.']" value="1"></td>';
                	}
                	else
                	{
                		$str.='<td><input type="checkbox" name="is_surcharge['.$key.']" value="1"></td>';
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
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
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

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	
 	 	$memberID = $post['memberID'];
 	 	
 		if($memberID == 0)
	 	{
	 		$this->Az->redirect('superadmin/master/commission', 'system_message_error',lang('MEMBER_ERROR'));
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
			 	  	 'created_by' => $loggedUser['id']
			 	  	);

			 	  	$this->db->insert('recharge_commision',$data);
		 	  	}
				
		 	}
	 	}

	 	$this->Az->redirect('superadmin/master/bbpsCommission', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}

	public function bbpsLiveCommission(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();


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

        $this->parser->parse('superadmin/layout/column-1', $data);	




	}

	public function getBBPSLiveCommData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
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
				}
			}


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($operatorList){
                $i=1;
                foreach($operatorList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['title'].'</td>';
                	$str.='<td><input type="hidden" name="op_id['.$key.']" class="form-control" value="'.$list['id'].'"><input type="text" name="commission['.$key.']" class="form-control" value="'.$list['commision'].'"></td> ';
                	if($list['is_flat'] == 1){
                		$str.='<td><input type="checkbox" checked="checked" name="is_flat['.$key.']" value="1"></td>';
                	}
                	else
                	{
                		$str.='<td><input type="checkbox" name="is_flat['.$key.']" value="1"></td>';
                	}
                	if($list['is_surcharge'] == 1){
                		$str.='<td><input type="checkbox" checked="checked" name="is_surcharge['.$key.']" value="1"></td>';
                	}
                	else
                	{
                		$str.='<td><input type="checkbox" name="is_surcharge['.$key.']" value="1"></td>';
                	}
                	$str.='</tr>';
                	$i++;
                }
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Service</th>';
			$str.='<th>Commission</th>';
			$str.='<th>Is Flat ?</th>';
			$str.='<th>Is Surcharge ?</th>';
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

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	
 	 	$memberID = $post['memberID'];
 	 	
 		if($memberID == 0)
	 	{
	 		$this->Az->redirect('superadmin/master/commission', 'system_message_error',lang('MEMBER_ERROR'));
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
			 	  	 'created_by' => $loggedUser['id']
			 	  	);

			 	  	$this->db->insert('bbps_commision',$data);
		 	  	}
				
		 	}
	 	}

	 	$this->Az->redirect('superadmin/master/bbpsLiveCommission', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}


	public function transferCommision(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
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
            'content_block' => 'master/transfer-commision'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveDMRCommission(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->transferCommision();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	
	 		if($memberID == 0)
		 	{
		 		$this->Az->redirect('superadmin/master/transferCommision', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
		 	
		 	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'surcharge' => $post['surcharge'],	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);
	 	  	$this->db->insert('dmr_commision',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/transferCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberDMRCommData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
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
			$str.='<th>Surcharge</th>';
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
                	$str.='<td>'.$list['surcharge'].'</td>';
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updatedmrModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'superadmin/master/deleteDMRCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="6" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Surcharge</th>';
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
	public function updateDMRCom(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('superadmin/master/transferCommision', 'system_message_error',lang('FORM_ERROR'));
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
	 			$this->Az->redirect('admin/master/transferCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 			
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
	 	  	$this->db->update('dmr_commision',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/transferCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteDMRCom($recordID = 0)
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('dmr_commision',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('superadmin/master/transferCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->delete('dmr_commision');
 		$this->Az->redirect('superadmin/master/transferCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getDMRCommData($recordID = 0)
	{
		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('dmr_commision',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('dmr_commision',array('id'=>$recordID))->row_array();
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

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
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
            'content_block' => 'master/aeps-commision'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveAEPSCommission(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('commision', 'Surcharge', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'Commission Type', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->aepsCommision();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	
	 		if($memberID == 0)
		 	{
		 		$this->Az->redirect('superadmin/master/aepsCommision', 'system_message_error',lang('MEMBER_ERROR'));
		 	}
	 	 	
			$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'commission' => $post['commision'],	
	 	  	 'com_type' => $post['com_type'],	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'created_by' => $loggedUser['id']	
	 	  	);

	 	  	$this->db->insert('aeps_commision',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/aepsCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberAEPSCommData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
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
			$str.='<th>Commision</th>';
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
                	$str.='<td>'.$list['commission'].'</td>';
                	if($list['com_type'] == 1){
                		$str.='<td>Account Withdrawal</td>';
                	}
                	elseif($list['com_type'] == 2){
                		$str.='<td>Mini Statement</td>';
                	}
                	elseif($list['com_type'] == 3){
                		$str.='<td>Aadhar Pay</td>';
                	}
                	elseif($list['com_type'] == 4){
                		$str.='<td>Cash Deposite</td>';
                	}
                	else
                	{
                		$str.='<td>MATM</td>';
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
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateaepsModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'superadmin/master/deleteAEPSCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
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
			$str.='<th>Commision</th>';
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

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('commision', 'Surcharge', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('com_type', 'Commission Type', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('superadmin/master/aepsCommision', 'system_message_error',lang('FORM_ERROR'));
		}
		else
		{	
 	 	
	 	 	$recordID = $post['recordID'];
	 	 	
	 		$chk_member = $this->db->get_where('aeps_commision',array('id'=>$recordID))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('superadmin/master/aepsCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
		 	
	 	 	
			$data = array(
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'commission' => $post['commision'],	
	 	  	 'com_type' => $post['com_type'],	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,
	 	  	 'is_surcharge' => isset($post['is_surcharge']) ? $post['is_surcharge'] : 0,	
	 	  	 'updated_by' => $loggedUser['id']	
	 	  	);
			$this->db->where('id',$recordID);
			$this->db->update('aeps_commision',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/aepsCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteAEPSCom($recordID = 0)
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('aeps_commision',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('superadmin/master/aepsCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->delete('aeps_commision');
 		$this->Az->redirect('superadmin/master/aepsCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getAEPSCommData($recordID = 0)
	{
		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('aeps_commision',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('aeps_commision',array('id'=>$recordID))->row_array();
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






	public function moneyTransferCommision(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
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
            'content_block' => 'master/money-transfer-commision'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveMoneyTransferCommission(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->moneyTransferCommision();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	
	 		if($memberID == 0)
		 	{
		 		$this->Az->redirect('superadmin/master/moneyTransferCommision', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
		 	
		 	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'surcharge' => $post['surcharge'],	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);
	 	  	$this->db->insert('money_transfer_commision',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/moneyTransferCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberMoneyTransferCommData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
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
			$str.='<th>Surcharge</th>';
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
                	$str.='<td>'.$list['surcharge'].'</td>';
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateMoneyTransferModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'superadmin/master/deleteMoneyTransferCom/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="6" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Surcharge</th>';
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

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('superadmin/master/moneyTransferCommision', 'system_message_error',lang('FORM_ERROR'));
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
	 			$this->Az->redirect('admin/master/moneyTransferCommision', 'system_message_error',lang('MEMBER_ERROR'));
	 			
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
	 	  	$this->db->update('money_transfer_commision',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/moneyTransferCommision', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteMoneyTransferCom($recordID = 0)
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('money_transfer_commision',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('superadmin/master/moneyTransferCommision', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->delete('money_transfer_commision');
 		$this->Az->redirect('superadmin/master/moneyTransferCommision', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getMoneyTransferCommData($recordID = 0)
	{
		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('money_transfer_commision',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('money_transfer_commision',array('id'=>$recordID))->row_array();
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

	public function autoSettlement(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$masterData = $this->db->get_where('master_setting',array('id'=>1))->row_array();
	 	$is_on_auto_settlement = isset($masterData['is_on_auto_settlement']) ? $masterData['is_on_auto_settlement'] : 0 ;

	 	// get users list
		$accountList = $this->db->get('account')->result_array();

		if($accountList)
		{
			foreach($accountList as $key=>$list)
			{
				$recordList = $this->db->get_where('account_settlement',array('account_id'=>$list['id']))->row_array();
				$accountList[$key]['is_on'] = isset($recordList['is_on']) ? $recordList['is_on'] : 0 ;
				$accountList[$key]['percentage'] = isset($recordList['percentage']) ? $recordList['percentage'] : 0 ;
			}
		}

		

		
		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'is_on_auto_settlement' => $is_on_auto_settlement,
			'accountList' => $accountList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/auto-settlement'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


    // save member
	public function autoSettlementAuth(){

		$post = $this->input->post();
		
		$this->db->where('id >',0);
		$this->db->update('account_settlement',array('is_on'=>0));
		if(isset($post['account_id']))
		{
			foreach($post['account_id'] as $key=>$account_id)
			{
				$percentage = isset($post['percentage'][$key]) ? $post['percentage'][$key] : 0;
				// check data saved or not
				$chk_data = $this->db->get_where('account_settlement',array('account_id'=>$account_id))->num_rows();
				if($chk_data)
				{
					$this->db->where('account_id',$account_id);
					$this->db->update('account_settlement',array('is_on'=>1,'percentage'=>$percentage));
				}
				else
				{
					$data = array(
						'account_id' => $account_id,
						'is_on' => 1,
						'percentage' => $percentage
					);
					$this->db->insert('account_settlement',$data);
				}
			}
		}
		
		$this->Az->redirect('superadmin/master/autoSettlement', 'system_message_error',lang('SETTING_SAVE_SUCCESS'));
		

	}

	public function dmtCharge(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
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
            'content_block' => 'master/dmt-charge'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveDmtCharge(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->dmtCharge();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	
	 		if($memberID == 0)
		 	{
		 		$this->Az->redirect('superadmin/master/dmtCharge', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
		 	
		 	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'start_range' => $post['startRange'],
		  	 'end_range' => $post['endRange'],
	 	  	 'surcharge' => $post['surcharge'],	
	 	  	 'is_flat' => isset($post['is_flat']) ? $post['is_flat'] : 0,	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);
	 	  	$this->db->insert('dmt_commision',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/dmtCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberDmtChargeData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
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
			$str.='<th>Surcharge</th>';
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
                	$str.='<td>'.$list['surcharge'].'</td>';
                	if($list['is_flat'] == 1){
                		$str.='<td><font color="green">Yes</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">No</font></td>';
                	}
                	$str.='<td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateDmtChargeModel('.$list['id'].'); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'superadmin/master/deleteDmtCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="6" align="center">No Record Found.</td></tr>';
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Start Range</th>';
			$str.='<th>End Range</th>';
			$str.='<th>Surcharge</th>';
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

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->Az->redirect('superadmin/master/dmtCharge', 'system_message_error',lang('FORM_ERROR'));
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
	 			$this->Az->redirect('admin/master/dmtCharge', 'system_message_error',lang('MEMBER_ERROR'));
	 			
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
	 	  	$this->db->update('dmt_commision',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/dmtCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function deleteDmtCharge($recordID = 0)
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('dmt_commision',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('superadmin/master/dmtCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->delete('dmt_commision');
 		$this->Az->redirect('superadmin/master/dmtCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getDmtChargeData($recordID = 0)
	{
		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('dmt_commision',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('dmt_commision',array('id'=>$recordID))->row_array();
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

	public function accountVerfiyCharge(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
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
            'content_block' => 'master/account-verify-charge'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveAccountVerifyCharge(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('surcharge', 'Surcharge', 'required|xss_clean|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->accountVerfiyCharge();
		}
		else
		{	
 	 	
	 	 	$memberID = $post['memberID'];
	 	 	
	 		if($memberID == 0)
		 	{
		 		$this->Az->redirect('superadmin/master/accountVerfiyCharge', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
		 	
		 	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'surcharge' => $post['surcharge'],	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);
	 	  	$this->db->insert('dmr_account_verify_charge',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/accountVerfiyCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberAccountVerifyChargeData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
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
                	$str.='<td><a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'superadmin/master/deleteAccountVerifyCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="6" align="center">No Record Found.</td></tr>';
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

	public function deleteAccountVerifyCharge($recordID = 0)
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('dmr_account_verify_charge',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('superadmin/master/accountVerfiyCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->delete('dmr_account_verify_charge');
 		$this->Az->redirect('superadmin/master/accountVerfiyCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getAccountVerifyChargeData($recordID = 0)
	{
		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('dmr_account_verify_charge',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('dmr_account_verify_charge',array('id'=>$recordID))->row_array();
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

	public function autoSettlementTime(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$timeList = $this->db->get('account_settlement_time')->result_array();

	 	$recordList = array();
	 	if($timeList)
	 	{
	 		$i = 1;
	 		foreach ($timeList as $key => $value) {
	 			$recordList[$i]['hour'] = $value['hour'];
				$recordList[$i]['min'] = $value['min'];
				$recordList[$i]['percentage'] = $value['percentage'];
				$recordList[$i]['status'] = $value['status'];
	 			$i++;
	 		}
	 	}

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
            'content_block' => 'master/auto-settlement-time'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


    // save member
	public function autoSettlementTimeAuth(){

		$post = $this->input->post();

		for($i = 1; $i<= 15; $i++)
		{
			$hour = $post['hour'][$i];
			$min = $post['min'][$i];
			$status = $post['status'][$i];
			
			if($hour)
			{
				// check data saved or not
				$chk_data = $this->db->get_where('account_settlement_time',array('id'=>$i))->num_rows();
				if($chk_data)
				{
					$this->db->where('id',$i);
					$this->db->update('account_settlement_time',array('hour'=>$hour,'min'=>$min,'status'=>$status));
				}
				else
				{
					$data = array(
						'hour' => $hour,
						'min' => $min,
						'status' => $status
					);
					$this->db->insert('account_settlement_time',$data);
				}
			}
		}

		$this->Az->redirect('superadmin/master/autoSettlementTime', 'system_message_error',lang('SETTING_SAVE_SUCCESS'));
		

	}

	public function disableCollectionQr(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		// get users list
		$accountList = $this->db->get('account')->result_array();

		
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountList' => $accountList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/disable-collection-qr'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getCollectionQrUserList($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Account is not valid.'
	 			);
	 	}
	 	else
	 	{
	 		// get account name
	 		$getAccountName = $this->db->select('title')->get_where('account',array('id'=>$member_id))->row_array();
	 		$accountName = isset($getAccountName['title']) ? $getAccountName['title'] : '';
			$recordList = $this->db->order_by('name','ASC')->where_in('role_id',array(3,4,5))->get_where('users',array('account_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Account</th>';
			$str.='<th>Member</th>';
			$str.='<th>Status</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$accountName.'</td>';
                	$str.='<td>'.$list['user_code'].'<br />'.$list['name'].'</td>';
                	if($list['is_upi_qr_active'] == 1){
                		$str.='<td><font color="green">QR Active</font></td>';
                	}
                	elseif($list['is_upi_qr_active'] == 0){
                		$str.='<td><font color="red">QR Not Generated</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">QR Not Generated</font></td>';
                	}

                	if($list['is_upi_qr_active'] == 1){
                		$str.='<td><a title="Disable QR" class="btn btn-danger btn-sm" href="'.base_url().'superadmin/master/disableCollectionQrAuth/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to disable QR?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">QR Not Generated</font></td>';
                	}
                	
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
			$str.='<th>Account</th>';
			$str.='<th>Member</th>';
			$str.='<th>Status</th>';
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

	public function disableCollectionQrAuth($recordID = 0)
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('users',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('superadmin/master/disableCollectionQr', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->update('users',array('is_upi_qr_active'=>0,'qr_url'=>'','upi_qr_ref_id'=>''));
 		$this->Az->redirect('superadmin/master/disableCollectionQr', 'system_message_error',lang('QR_DISABLE_SUCCESS'));
	}

	public function disableCashQr(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		// get users list
		$accountList = $this->db->get('account')->result_array();

		
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountList' => $accountList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'master/disable-cash-qr'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getCashQrUserList($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
	 	if($member_id == 0)
	 	{
	 		$is_error = 1;
	 	}
	 	
	 	if($is_error)
	 	{
	 		$response = array(
	 				'status' => 0,
	 				'msg' => 'Account is not valid.'
	 			);
	 	}
	 	else
	 	{
	 		// get account name
	 		$getAccountName = $this->db->select('title')->get_where('account',array('id'=>$member_id))->row_array();
	 		$accountName = isset($getAccountName['title']) ? $getAccountName['title'] : '';
			$recordList = $this->db->order_by('name','ASC')->where_in('role_id',array(3,4,5))->get_where('users',array('account_id'=>$member_id))->result_array();


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Account</th>';
			$str.='<th>Member</th>';
			$str.='<th>Status</th>';
			$str.='<th>Action</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($recordList){
                $i=1;
                foreach($recordList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$accountName.'</td>';
                	$str.='<td>'.$list['user_code'].'<br />'.$list['name'].'</td>';
                	if($list['is_upi_cash_qr_active'] == 1){
                		$str.='<td><font color="green">QR Active</font></td>';
                	}
                	elseif($list['is_upi_cash_qr_active'] == 0){
                		$str.='<td><font color="red">QR Not Generated</font></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">QR Not Generated</font></td>';
                	}

                	if($list['is_upi_cash_qr_active'] == 1){
                		$str.='<td><a title="Disable QR" class="btn btn-danger btn-sm" href="'.base_url().'superadmin/master/disableCashQrAuth/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to disable QR?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	}
                	else
                	{
                		$str.='<td><font color="red">QR Not Generated</font></td>';
                	}
                	
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
			$str.='<th>Account</th>';
			$str.='<th>Member</th>';
			$str.='<th>Status</th>';
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

	public function disableCashQrAuth($recordID = 0)
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('users',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('superadmin/master/disableCashQr', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->update('users',array('is_upi_cash_qr_active'=>0,'cash_qr_url'=>'','upi_cash_qr_ref_id'=>''));
 		$this->Az->redirect('superadmin/master/disableCashQr', 'system_message_error',lang('QR_DISABLE_SUCCESS'));
	}

	public function nsdlPancardCharge(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


    // save member
	public function saveNsdlPancardCharge(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	
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
		 		$this->Az->redirect('superadmin/master/nsdlPancardCharge', 'system_message_error',lang('PACKAGE_ERROR'));
		 	}
		 	
		 	$data = array(
		  	 'account_id' => $account_id,
		  	 'package_id' => $memberID,
		  	 'surcharge' => $post['surcharge'],	
	 	  	 'created_by' => $loggedUser['id']
	 	  	);
	 	  	$this->db->insert('nsdl_pancard_charge',$data);
	 	  	
			

		 	$this->Az->redirect('superadmin/master/nsdlPancardCharge', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));
		 }

	}

	public function getMemberNsdlPancardChargeData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
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
                	$str.='<td><a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'superadmin/master/deleteNsdlPancardCharge/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                	
                	$str.='</tr>';
                	$i++;
                }
            }
            else
            {
            	$str.='<tr><td colspan="6" align="center">No Record Found.</td></tr>';
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

	public function deleteNsdlPancardCharge($recordID = 0)
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('nsdl_pancard_charge',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$this->Az->redirect('superadmin/master/nsdlPancardCharge', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->delete('nsdl_pancard_charge');
 		$this->Az->redirect('superadmin/master/nsdlPancardCharge', 'system_message_error',lang('DMR_COM_DELETE_SUCCESS'));
	}


	public function getNsdlPancardChargeData($recordID = 0)
	{
		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$chk_member = $this->db->get_where('nsdl_pancard_charge',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('nsdl_pancard_charge',array('id'=>$recordID))->row_array();
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

	public function bbpsOperator(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		// get users list
		$packageList = $this->db->get('bbps_service')->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/bbps-operator',
            'packageList' => $packageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('superadmin/layout/column-1', $data);	




	}

	public function getBBPSOperatorData($member_id = 0){

		$response = array();
		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$is_error = 0;
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
	 		$operatorList = $this->db->get_where('bbps_service_category',array('service_id'=>$member_id))->result_array();

			
			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Biller</th>';
			$str.='<th>Biller ID</th>';
			$str.='<th>Status</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($operatorList){
                $i=1;
                foreach($operatorList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td>'.$i.'</td>';
                	$str.='<td>'.$list['billerName'].'</td>';
                	$str.='<td>'.$list['biller_id'].'</td>';
                	$str.='<td>';
                	$str.='<select class="form-control" name="biller_id['.$list['id'].']">';
                	if($list['status'] == 1)
                	{
                    	$str.='<option value="1" selected="selected">Active</option>';
                    	$str.='<option value="0">Deactive</option>';
                	}
                	else
                	{
                		$str.='<option value="1">Active</option>';
                    	$str.='<option value="0" selected="selected">Deactive</option>';
                	}
                	$str.='</td>';
                	$str.='</tr>';
                	$i++;
                }
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Biller</th>';
			$str.='<th>Biller ID</th>';
			$str.='<th>Status</th>';
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


	public function saveBBPSOperator(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	

 	 	
 	 	$memberID = $post['memberID'];
 	 	
 		if($memberID == 0)
	 	{
	 		$this->Az->redirect('superadmin/master/bbpsOperator', 'system_message_error',lang('MEMBER_ERROR'));
	 	}
	 	

	 	if($post['biller_id'])
	 	{
		 	foreach($post['biller_id'] as $recordID => $status){

	 			$data = array(
		 	  	 'status' => $status
		 	  	);

		 	  	$this->db->where('id',$recordID);
		 	  	$this->db->update('bbps_service_category',$data);
		 		
				
		 	}
	 	}

	 	$this->Az->redirect('superadmin/master/bbpsOperator', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}

	public function prepaidOperator(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$operatorList = $this->db->query("SELECT * FROM tbl_operator WHERE type = 'Prepaid' OR type = 'DTH' ORDER BY type ASC")->result_array();
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'master/prepaid-operator',
            'operatorList' => $operatorList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('superadmin/layout/column-1', $data);	




	}

	

	public function savePrepaidOperator(){

		$account_id = SUPERADMIN_ACCOUNT_ID;
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
 	 	$post = $this->input->post();	

 	 	
	 	

	 	if($post['biller_id'])
	 	{
		 	foreach($post['biller_id'] as $recordID => $status){

	 			$data = array(
		 	  	 'status' => $status
		 	  	);

		 	  	$this->db->where('id',$recordID);
		 	  	$this->db->update('operator',$data);
		 		
				
		 	}
	 	}

	 	$this->Az->redirect('superadmin/master/prepaidOperator', 'system_message_error',lang('COMMISSION_SAVE_SUCCESS'));

	}
	
	
	
}