<?php 
class Api extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkAdminPermission();
        $this->load->model('admin/Api_model');		
        $this->lang->load('admin/api', 'english');
        
    }

    public function apiList(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		$account_id = $this->User->get_domain_account();
	 	$accountData = $this->User->get_account_data($account_id);
	 	$user_ip_address = $this->User->get_user_ip();

	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Api List Page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'api/apiList'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getAPIList()
	{	
		$account_id = $this->User->get_domain_account();
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$date = isset($filterData[1]) ? trim($filterData[1]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.id',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT * FROM tbl_api as a where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT * FROM tbl_api as a where a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( a.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
			
				$sql.=" AND ( DATE(a.created) = '".$date."' )";    
			
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['id'];
				$nestedData[] = $list['provider'];
				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['status'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('admin/api/editApi').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="'.base_url().'admin/api/deleteApi/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a> <a href="'.base_url().'admin/api/addApiOprator/'.$list['id'].'" class="btn btn-primary" style="padding: 7px 8px;font-size: 12px;">Add Operator</a> <a href="'.base_url().'admin/api/addApiCircle/'.$list['id'].'" class="btn btn-primary" style="padding: 7px 8px;font-size: 12px;">Add Circle</a>';
				
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					);

		echo json_encode($json_data);  // send data as json format
	}

	
	public function addApi(){


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);

	 	$accountData = $this->User->get_account_data($account_id);
	 	$user_ip_address = $this->User->get_user_ip();

	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Add Api Page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

	 	if($accountData['is_api_active'] == 0){
	 		// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Add Api is not allowed redirect back to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

	 		$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('MEMBER_ERROR'));
	 	}

	 	// get request type
	 	$requestTypeList = $this->db->get('api_request_type')->result_array();

	 	// get parameter value
	 	$paraValueList = $this->db->get('api_parameter_value')->result_array();

	 	// get api response type
	 	$responseTypeList = $this->db->get('api_response_type')->result_array();

	 	// get response value
	 	$resValueList = $this->db->get('api_response_value')->result_array();

	 	// generate callback code
	 	$callbackCode = rand(111111,999999);

	 	// check instant pay api is active or not
	 	$isInstantPayApiAllow = $this->db->get_where('account_custom_api_permission',array('account_id'=>$account_id,'api_id'=>1,'status'=>1))->num_rows();
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'api/addApi',
            'requestTypeList' => $requestTypeList,
            'paraValueList' => $paraValueList,
            'responseTypeList' => $responseTypeList,
            'resValueList' => $resValueList,
            'callbackCode' => $callbackCode,
            'isInstantPayApiAllow' => $isInstantPayApiAllow,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('admin/layout/column-1', $data);	




	}

    // save member
	public function saveApiAuth(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();
 	 	$post = $this->security->xss_clean($this->input->post());	

 	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Save Api Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('provider', 'Provider', 'required|xss_clean');
		$this->form_validation->set_rules('request_base_url', 'Request Base URL', 'required|xss_clean');
		$this->form_validation->set_rules('request_type', 'Request Type', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Save Api Validation Error.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->addApi();
		}
		else
		{	
 	 		$this->Api_model->saveApi($post);
 	 		// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Api Saved Successfully and redirect to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);
		 	$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('API_SAVE_SUCCESS'));
		 }

	}

	public function editApi($api_id = 0){


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();

	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Admin Open Edit API Page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

	 	// check member
		$chkMember = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - API ID - '.$api_id.' - API ID is not associated with this account redirect back to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('MEMBER_ERROR'));
		}
	 	// get request type
	 	$requestTypeList = $this->db->get('api_request_type')->result_array();

	 	// get parameter value
	 	$paraValueList = $this->db->get('api_parameter_value')->result_array();

	 	// get api response type
	 	$responseTypeList = $this->db->get('api_response_type')->result_array();

	 	// get response value
	 	$resValueList = $this->db->get('api_response_value')->result_array();

	 	// get api data
	 	$apiData = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->row_array();

	 	// get method data
	 	$getMethodData = $this->db->get_where('api_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>1))->row_array();

	 	// get method data
	 	$getHeaderData = $this->db->get_where('api_header_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>1))->row_array();

	 	// get method data
	 	$getBalanceGetHeaderData = $this->db->get_where('api_get_balance_header_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>1))->row_array();

	 	// get balance get method data
	 	$getBalanceGetMethodData = $this->db->get_where('api_get_balance_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>1))->row_array();

	 	// check status get method data
	 	$checkStatusGetMethodData = $this->db->get_where('api_check_status_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>1))->row_array();

	 	// get method all parameters
	 	$getParaList = $this->db->get_where('api_get_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

	 	// get balance get method all parameters
	 	$getBalanceGetParaList = $this->db->get_where('api_get_balance_get_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

	 	// check status get method all parameters
	 	$checkStatusGetParaList = $this->db->get_where('api_check_status_get_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

	 	// post method data
	 	$postMethodData = $this->db->get_where('api_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>2))->row_array();

	 	// get balance post method data
	 	$getBalancePostMethodData = $this->db->get_where('api_get_balance_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>2))->row_array();

	 	// check status post method data
	 	$checkStatusPostMethodData = $this->db->get_where('api_check_status_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>2))->row_array();

	 	// get method all parameters
	 	$postParaList = $this->db->get_where('api_post_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

	 	// get balance post method all parameters
	 	$getBalancePostParaList = $this->db->get_where('api_get_balance_post_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

	 	// check status post method all parameters
	 	$checkStatusPostParaList = $this->db->get_where('api_check_status_post_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

	 	$responseParaList = array();
	 	if($apiData['response_type'] == 1)
	 	{
	 		$responseParaList = $this->db->get_where('api_str_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
	 	}
	 	elseif($apiData['response_type'] == 2)
	 	{
	 		$responseParaList = $this->db->get_where('api_xml_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
	 	}
	 	elseif($apiData['response_type'] == 3)
	 	{
	 		$responseParaList = $this->db->get_where('api_json_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
	 	}


	 	$getBalanceResponseParaList = array();
	 	if($apiData['get_balance_response_type'] == 1)
	 	{
	 		$getBalanceResponseParaList = $this->db->get_where('api_get_balance_str_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
	 	}
	 	elseif($apiData['get_balance_response_type'] == 2)
	 	{
	 		$getBalanceResponseParaList = $this->db->get_where('api_get_balance_xml_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
	 	}
	 	elseif($apiData['get_balance_response_type'] == 3)
	 	{
	 		$getBalanceResponseParaList = $this->db->get_where('api_get_balance_json_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
	 	}

	 	$checkStatusResponseParaList = array();
	 	if($apiData['check_status_response_type'] == 1)
	 	{
	 		$checkStatusResponseParaList = $this->db->get_where('api_check_status_str_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
	 	}
	 	elseif($apiData['check_status_response_type'] == 2)
	 	{
	 		$checkStatusResponseParaList = $this->db->get_where('api_check_status_xml_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
	 	}
	 	elseif($apiData['check_status_response_type'] == 3)
	 	{
	 		$checkStatusResponseParaList = $this->db->get_where('api_check_status_json_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
	 	}

	 	$callBackResponseParaList = $this->db->get_where('api_call_back_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

	 	// check instant pay api is active or not
	 	$isInstantPayApiAllow = $this->db->get_where('account_custom_api_permission',array('account_id'=>$account_id,'api_id'=>1,'status'=>1))->num_rows();
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'api/editApi',
            'requestTypeList' => $requestTypeList,
            'paraValueList' => $paraValueList,
            'responseTypeList' => $responseTypeList,
            'resValueList' => $resValueList,
            'api_id' => $api_id,
            'apiData' => $apiData,
            'getMethodData' => $getMethodData,
            'getBalanceGetMethodData' => $getBalanceGetMethodData,
            'checkStatusGetMethodData' => $checkStatusGetMethodData,
            'getParaList' => $getParaList,
            'getBalanceGetParaList' => $getBalanceGetParaList,
            'checkStatusGetParaList' => $checkStatusGetParaList,
            'postMethodData' => $postMethodData,
            'getBalancePostMethodData' => $getBalancePostMethodData,
            'checkStatusPostMethodData' => $checkStatusPostMethodData,
            'postParaList' => $postParaList,
            'getBalancePostParaList' => $getBalancePostParaList,
            'checkStatusPostParaList' => $checkStatusPostParaList,
            'responseParaList' => $responseParaList,
            'getBalanceResponseParaList' => $getBalanceResponseParaList,
            'checkStatusResponseParaList' => $checkStatusResponseParaList,
            'callBackResponseParaList' => $callBackResponseParaList,
            'getHeaderData' => $getHeaderData,
            'getBalanceGetHeaderData' => $getBalanceGetHeaderData,
            'isInstantPayApiAllow' => $isInstantPayApiAllow,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('admin/layout/column-1', $data);	




	}

	public function updateApiAuth(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();
 	 	$post = $this->security->xss_clean($this->input->post());
 	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Update Api Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
 	 	$api_id = $post['api_id'];

 	 	// check member
		$chkMember = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Update Api - API ID not associated with this account redirect back to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('MEMBER_ERROR'));
		}

 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('provider', 'Provider', 'required|xss_clean');
		$this->form_validation->set_rules('request_base_url', 'Request Base URL', 'required|xss_clean');
		$this->form_validation->set_rules('request_type', 'Request Type', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Update Api - Validation Error.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->editApi($api_id);
		}
		else
		{	
 	 		$this->Api_model->updateApi($post);
 	 		// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Update Api - Api updated successfully redirect back to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

		 	$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('API_SAVE_SUCCESS'));
		 }

	}

	public function deleteApi($api_id = 0){


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();

	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Delete Api Page Open.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

	 	// check member
		$chkMember = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Delete Api - API ID - '.$api_id.' not associated with this account redirect back to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('MEMBER_ERROR'));
		}

		$this->db->where('id',$api_id);
        $this->db->where('account_id',$account_id);
        $this->db->delete('api');

        $this->db->where('api_id',$api_id);
      	$this->db->where('account_id',$account_id);
      	$this->db->delete('api_parameter');

      	$this->db->where('api_id',$api_id);
      	$this->db->where('account_id',$account_id);
      	$this->db->delete('api_get_parameter');

      	$this->db->where('api_id',$api_id);
      	$this->db->where('account_id',$account_id);
      	$this->db->delete('api_post_parameter');

      	$this->db->where('api_id',$api_id);
      	$this->db->where('account_id',$account_id);
      	$this->db->delete('api_str_response');

      	$this->db->where('api_id',$api_id);
      	$this->db->where('account_id',$account_id);
      	$this->db->delete('api_xml_response');

      	$this->db->where('api_id',$api_id);
      	$this->db->where('account_id',$account_id);
      	$this->db->delete('api_json_response');

      	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Delete Api - Api deleted successfully redirect back to api list page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

      	$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('API_DELETE_SUCCESS'));
	}

	public function addApiOprator($api_id = 0){


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();
	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Add Api Operator Page Open.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
	 	// check member
		$chkMember = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Add Api Operator - API ID - '.$api_id.' not associated with this account redirect back to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('MEMBER_ERROR'));
		}
	 	
	 	// get operator list
	 	$operatorList = $this->db->order_by('type','asc')->get('operator')->result_array();
	 	if($operatorList)
	 	{
	 		foreach($operatorList as $key=>$list)
	 		{
	 			$getCode = $this->db->get_where('api_operator',array('account_id'=>$account_id,'api_id'=>$api_id,'opt_id'=>$list['id']))->row_array();		
	 			$operatorList[$key]['code'] = isset($getCode['opt_code']) ? $getCode['opt_code'] : '';
	 		}
	 	}

	 	
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'api/operatorList',
            'operatorList' => $operatorList,
            'api_id' => $api_id,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('admin/layout/column-1', $data);	




	}

	public function saveApiOprator(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Save Api Operator - Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
 	 	$api_id = $post['api_id'];
 	 	// check member
		$chkMember = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Save Api Operator - API ID - '.$api_id.' not associated with this account redirect back to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('MEMBER_ERROR'));
		}


		if(isset($post['oprator_id']) && $post['oprator_id'])
		{
			foreach($post['oprator_id'] as $key=>$opt_id)
			{
				// check operator already saved or not
				$chk_operator = $this->db->get_where('api_operator',array('account_id'=>$account_id,'api_id'=>$api_id,'opt_id'=>$opt_id))->num_rows();
				if($chk_operator)
				{
					$optData = array(
						'opt_code' => isset($post['oprator_code'][$key]) ? $post['oprator_code'][$key] : ''
					);
					$this->db->where('account_id',$account_id);
					$this->db->where('api_id',$api_id);
					$this->db->where('opt_id',$opt_id);
					$this->db->update('api_operator',$optData);
				}
				else
				{
					$optData = array(
						'account_id' => $account_id,
						'api_id' => $api_id,
						'opt_id' => $opt_id,
						'opt_code' => isset($post['oprator_code'][$key]) ? $post['oprator_code'][$key] : ''
					);
					$this->db->insert('api_operator',$optData);
				}
			}
		}

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Save Api Operator updated successfully redirect back to api list page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
 	 	
		$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('API_OPERATOR_SAVE_SUCCESS'));
		

	}


	public function addApiCircle($api_id = 0){


		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();

	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Add Api Circle Page Open.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

	 	// check member
		$chkMember = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Add Api Circle - API ID - '.$api_id.' not associated with this account redirect back to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('MEMBER_ERROR'));
		}
	 	
	 	// get operator list
	 	$operatorList = $this->db->get('circle')->result_array();
	 	if($operatorList)
	 	{
	 		foreach($operatorList as $key=>$list)
	 		{
	 			$getCode = $this->db->get_where('api_circle',array('account_id'=>$account_id,'api_id'=>$api_id,'circle_id'=>$list['id']))->row_array();		
	 			$operatorList[$key]['code'] = isset($getCode['circle_code']) ? $getCode['circle_code'] : '';
	 		}
	 	}

	 	
		
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'api/circleList',
            'operatorList' => $operatorList,
            'api_id' => $api_id,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('admin/layout/column-1', $data);	




	}

	public function saveApiCircle(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Save Api Circle - Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
 	 	$api_id = $post['api_id'];
 	 	// check member
		$chkMember = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Save Api Circle - API ID - '.$api_id.' not associated with this account redirect back to api list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('MEMBER_ERROR'));
		}


		if(isset($post['oprator_id']) && $post['oprator_id'])
		{
			foreach($post['oprator_id'] as $key=>$opt_id)
			{
				// check operator already saved or not
				$chk_operator = $this->db->get_where('api_circle',array('account_id'=>$account_id,'api_id'=>$api_id,'circle_id'=>$opt_id))->num_rows();
				if($chk_operator)
				{
					$optData = array(
						'circle_code' => isset($post['oprator_code'][$key]) ? $post['oprator_code'][$key] : ''
					);
					$this->db->where('account_id',$account_id);
					$this->db->where('api_id',$api_id);
					$this->db->where('circle_id',$opt_id);
					$this->db->update('api_circle',$optData);
				}
				else
				{
					$optData = array(
						'account_id' => $account_id,
						'api_id' => $api_id,
						'circle_id' => $opt_id,
						'circle_code' => isset($post['oprator_code'][$key]) ? $post['oprator_code'][$key] : ''
					);
					$this->db->insert('api_circle',$optData);
				}
			}
		}

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Save Api Circle - Circle updated redirect back to api list page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
 	 	
		$this->Az->redirect('admin/api/apiList', 'system_message_error',lang('API_CIRCLE_SAVE_SUCCESS'));
		

	}


	public function changeApi(){

		$account_id = $this->User->get_domain_account();
		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Change Api Page Open.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
		
		// get package list
		$packageList = $this->db->get_where('package',array('account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();

		// get api list
		$apiList = $this->db->get_where('api',array('account_id'=>$account_id))->result_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'packageList' => $packageList,
			'apiList' => $apiList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'api/changeApi'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getMemberActiveAPIData($member_id = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);

	 	$is_error = 0;
	 	// check member id valid or not
	 	// get package list
		$chk_member = $this->db->get_where('package',array('id'=>$member_id,'account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();
 		if(!$chk_member)
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
			$operatorList = $this->db->get('operator')->result_array();

			if($operatorList)
			{
				foreach($operatorList as $key=>$list)
				{
					// get commission
					$get_com_data = $this->db->select('api.provider')->join('api','api.id = member_active_api.api_id')->get_where('member_active_api',array('member_active_api.account_id'=>$account_id,'member_active_api.package_id'=>$member_id,'member_active_api.op_id'=>$list['id']))->row_array();
					$operatorList[$key]['active_api'] = isset($get_com_data['provider']) ? $get_com_data['provider'] : 'No Active API' ;
				}
			}


			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
			$str.='<thead>';
			$str.='<tr>';
			$str.='<th><input type="checkbox" id="check_all"></th>';
			$str.='<th>Operator ID</th>';
			$str.='<th>Operator Name</th>';
			$str.='<th>Service Type</th>';
			$str.='<th>Active API</th>';
			$str.='</tr></thead>';
			$str.='<tbody>';
			if($operatorList){
                $i=1;
                foreach($operatorList as $key=>$list){
                	$str.='<tr>';
                	$str.='<td><input type="checkbox" name="optID[]" value="'.$list['id'].'" /></td>';
                	$str.='<td>'.$list['id'].'</td>';
                	$str.='<td>'.$list['operator_name'].'</td>';
                	$str.='<td>'.$list['type'].'</td>';
                	$str.='<td>'.$list['active_api'].'</td>';
                	$i++;
                }
            }
            $str.='</tbody><tfoot>';
            $str.='<tr>';
			$str.='<th>#</th>';
			$str.='<th>Operator ID</th>';
			$str.='<th>Operator Name</th>';
			$str.='<th>Service Type</th>';
			$str.='<th>Active API</th>';
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

	public function changeApiAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Change Api Auth - Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
 	 	if(!$post['api_id'])
 	 	{
 	 		$this->Az->redirect('admin/api/changeApi', 'system_message_error',lang('API_ERROR'));
 	 	}

 	 	if(!isset($post['optID']) || !$post['optID'])
 	 	{
 	 		$this->Az->redirect('admin/api/changeApi', 'system_message_error',lang('OPT_ERROR'));
 	 	}

 	 	$api_id = $post['api_id'];
 	 	// check member
		$chkMember = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Change Api Auth - API ID - '.$api_id.' not associated with this account redirect back to change api page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('admin/api/changeApi', 'system_message_error',lang('API_VALID_ERROR'));
		}

		$member_id = $post['memberID'];
		// get package list
		$chk_member = $this->db->get_where('package',array('id'=>$member_id,'account_id'=>$account_id,'status'=>1,'created_by'=>$loggedUser['id']))->result_array();
		if(!$chk_member)
 		{
 			$this->Az->redirect('admin/api/changeApi', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		if($post['optID'])
 		{
 			foreach($post['optID'] as $op_id)
 			{
 				// check operator already saved or not
				$chk_operator = $this->db->get_where('member_active_api',array('account_id'=>$account_id,'package_id'=>$member_id,'op_id'=>$op_id))->num_rows();
				if($chk_operator)
				{
					$optData = array(
						'api_id' => $api_id,
						'updated' => date('Y-m-d H:i:s')
					);
					$this->db->where('account_id',$account_id);
					$this->db->where('package_id',$member_id);
					$this->db->where('op_id',$op_id);
					$this->db->update('member_active_api',$optData);
				}
				else
				{
					$optData = array(
						'account_id' => $account_id,
						'package_id' => $member_id,
						'op_id' => $op_id,
						'api_id' => $api_id,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('member_active_api',$optData);
				}
 			}
 		}

 		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Change Api Auth - Api Changed successfully redirect back to change api page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

 		$this->Az->redirect('admin/api/changeApi', 'system_message_error',lang('API_CHANGE_SUCCESS'));
	}

	public function amountFilter(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		$account_id = $this->User->get_domain_account();
	 	$accountData = $this->User->get_account_data($account_id);
	 	$user_ip_address = $this->User->get_user_ip();

	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Amount Filter API Page Open.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

	 	$operatorList = $this->db->get_where('operator',array('type'=>'Prepaid'))->result_array();

	 	// get api list
		$apiList = $this->db->get_where('api',array('account_id'=>$account_id))->result_array();

		// get api list
		$recordList = $this->db->select('amount_active_api.*,operator.operator_name,api.provider')->join('operator','operator.id = amount_active_api.op_id')->join('api','api.id = amount_active_api.api_id')->get_where('amount_active_api',array('amount_active_api.account_id'=>$account_id))->result_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'operatorList' => $operatorList,
			'apiList' => $apiList,
			'recordList' => $recordList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'api/amountFilter'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	// save member
	public function amountFilterAuth(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Amount Filter API - Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('op_id', 'Operator', 'required|xss_clean');
		$this->form_validation->set_rules('api_id', 'API', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Amount Filter API - Validation Error.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->amountFilter();
		}
		else
		{	

 	 		$this->Api_model->saveAmountFilterApi($post);
 	 		// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Amount Filter API Save successfully redirect back to amount filter api page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);
		 	$this->Az->redirect('admin/api/amountFilter', 'system_message_error',lang('AMOUNT_FILTER_SAVE'));
		 }

	}

	public function getAmountFilterData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('amount_active_api',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$operatorList = $this->db->get_where('operator',array('type'=>'Prepaid'))->result_array();

	 		// get api list
			$apiList = $this->db->get_where('api',array('account_id'=>$account_id))->result_array();

 			$dmrData = $this->db->get_where('amount_active_api',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			$str = '<div class="form-group">';
	        $str.='<label>Start Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="startRange" class="form-control" value="'.$dmrData['start_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>End Range*</label>';
	        $str.='<input type="text" autocomplete="off" name="endRange" class="form-control" value="'.$dmrData['end_range'].'">';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>Operator*</label>';
	        $str.='<select class="form-control" name="op_id">';
	        $str.='<option value="">Select Operator</option>';
	        if($operatorList){
	        	foreach($operatorList as $list){
	        		if($list['id'] == $dmrData['op_id'])
	        		{
	        			$str.='<option value="'.$list['id'].'" selected="selected">'.$list['operator_name'].'</option>';
	        		}
	        		else
	        		{
	        			$str.='<option value="'.$list['id'].'">'.$list['operator_name'].'</option>';
	        		}
	        	}
	        }
	        $str.='</select>';
	        $str.='</div>';
	        $str.='<div class="form-group">';
	        $str.='<label>Active API*</label>';
	        $str.='<select class="form-control" name="api_id">';
	        $str.='<option value="">Select API</option>';
	        if($apiList){
	        	foreach($apiList as $list){
	        		if($list['id'] == $dmrData['api_id'])
	        		{
	        			$str.='<option value="'.$list['id'].'" selected="selected">'.$list['provider'].'</option>';
	        		}
	        		else
	        		{
	        			$str.='<option value="'.$list['id'].'">'.$list['provider'].'</option>';
	        		}
	        	}
	        }
	        $str.='</select>';
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
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}

	// save member
	public function updateAmountFilter(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$user_ip_address = $this->User->get_user_ip();
 	 	$post = $this->security->xss_clean($this->input->post());	
 	 	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Update Amount Filter API - Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

 	 	$recordID = $post['recordID'];
 	 	$this->load->library('form_validation');
		$this->form_validation->set_rules('startRange', 'Start Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('endRange', 'End Range', 'required|xss_clean|numeric');
		$this->form_validation->set_rules('op_id', 'Operator', 'required|xss_clean');
		$this->form_validation->set_rules('api_id', 'API', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			
			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Update Amount Filter API - Validation Error.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);
			$this->Az->redirect('admin/api/amountFilter', 'system_message_error',lang('MEMBER_ERROR'));
		}
		else
		{	
			$chk_member = $this->db->get_where('amount_active_api',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
	 		if(!$chk_member)
	 		{
	 			$this->Az->redirect('admin/api/amountFilter', 'system_message_error',lang('MEMBER_ERROR'));
	 			
	 		}
 	 		$this->Api_model->updateAmountFilterApi($post,$recordID);
 	 		// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Update Amount Filter API - Save successfully redirect back to amount filter page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);
		 	$this->Az->redirect('admin/api/amountFilter', 'system_message_error',lang('AMOUNT_FILTER_SAVE'));
		 }

	}

	public function deleteAmountFilter($recordID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		$user_ip_address = $this->User->get_user_ip();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Delete Amount Filter API Page Open Record ID - '.$recordID.'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
		$chk_member = $this->db->get_where('amount_active_api',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Delete Amount Filter API Record ID - '.$recordID.' not associated with this account redirect back to amount filter page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

 			$this->Az->redirect('admin/api/amountFilter', 'system_message_error',lang('MEMBER_ERROR'));
 			
 		}

 		$this->db->where('id',$recordID);
 		$this->db->where('account_id',$account_id);
 		$this->db->delete('amount_active_api');

 		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Delete Amount Filter API Record deleted successfully redirect back to amount filter page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

 		$this->Az->redirect('admin/api/amountFilter', 'system_message_error',lang('AMOUNT_FILTER_DELETE_SUCCESS'));
	}

		
	
}