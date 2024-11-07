<?php 
require XLSX_LIB_ROOT_PATH;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class Current extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkMasterPermission();
        
    }

    public function index(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(10, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'current/list'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}


	public function getAccountList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
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
			0 => 'created'
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_current_account_list as a where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_current_account_list as a where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.first_name LIKE '".$keyword."%' ";    
				$sql.=" OR a.last_name LIKE '".$keyword."%' ";
				$sql.=" OR a.email LIKE '".$keyword."%' ";
				$sql.=" OR a.pincode LIKE '".$keyword."%' ";
				$sql.=" OR a.application_no LIKE '".$keyword."%' ";
				$sql.=" OR a.tracker_id LIKE '".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND  DATE(a.created) = '".$date."' ";    
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
				$nestedData[] = $list['first_name'].' '.$list['last_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['email'];
				$nestedData[] = $list['account_type'];
				$nestedData[] = $list['pincode'];
				$nestedData[] = $list['application_no'];
				$nestedData[] = $list['tracker_id'];
				$nestedData[] = '<a href="'.$list['web_url'].'" target="_blank">Open URL</a>';
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
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

	

	// topup upi wallet
	public function openAccount()
    {
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(10, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
	 	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'current/openAccount',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('master/layout/column-1', $data);
		
    }

    // upi wallet topup auth
	public function accountAuth()
	{	
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
	 	//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|min_length[10]');
		$this->form_validation->set_rules('email', 'Email', 'required|xss_clean|valid_email');
		$this->form_validation->set_rules('account_type', 'Account Type', 'required|xss_clean');
		$this->form_validation->set_rules('pincode', 'Pincode', 'required|xss_clean|min_length[6]');
		if($this->form_validation->run() == FALSE) {
			
			$this->openAccount();
		}
		else
		{	
			// Create request	
			$request = [
		        "user" => $accountData['current_account_user'],
		        "passCode" => $accountData['current_account_passcode'],
		        "mobileNo" => $post['mobile'],
		        "emailId" => $post['email'],
		        "p_CATEGORY_TYPE" => $post['account_type'],
		        "data"=>array
		        (
		            "p_FNAME" => $post['first_name'],
		            "p_LNAME" => $post['last_name']
		        )
		    ];

		    if($post['account_type'] == 'Individual')
		    {
		    	$request['data']['p_COMMPCODE'] = $post['pincode'];
		    }
		    else
		    {
		    	$request['data']['p_PERPCODE'] = $post['pincode'];
		    }

		    // Convert to json
		    $json_enc = json_encode($request);

		    // Create header
		    $header = [
		        'Content-type:application/json'
		    ];    
		    
		    // Create url
		    $url = CURRENT_ACCOUNT_OPEN_API_URL;
		    
		    $curl = curl_init();
		    
		    curl_setopt_array($curl, array(
		        CURLOPT_RETURNTRANSFER => true,        
		        CURLOPT_CUSTOMREQUEST => "POST",
		        CURLOPT_POSTFIELDS => $json_enc,
		        CURLOPT_HTTPHEADER => $header,
		        CURLOPT_URL => $url
		    ));
		    
		    // Get response
		    $response = curl_exec($curl);
		  	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$err = curl_error($curl);    
		    curl_close($curl);

		    /*$response = '{"status":"Success","message":"Your Application Number : 777-000471807 and Tracking Id : WEB111111111582327","errorCode":"0","p_APPLICATION_NO":"777-000471807","trackerId":"WEB111111111582327","webUrl":"https://cadigital.icicibank.com/SmartFormWeb/apps/services/www/SmartFormWeb/desktopbrowser/default/index.html?trackerId=WEB111111111582327#/login"}';*/

		    // save upi api response
	        $apiData = array(
	            'account_id' => $account_id,
	            'member_id' => $loggedAccountID,
	            'api_url' => $url,
	            'post_data' => $json_enc,
	            'response' => $response,
	            'created' => date('Y-m-d H:i:s')
	        );
	        $this->db->insert('current_account_api_response',$apiData);

	        $decodeResponse = json_decode($response,true);

	        if(isset($decodeResponse['status']) && $decodeResponse['status'] == 'Success')
	        {
	        	// save data
		        $data = array(
		            'account_id' => $account_id,
		            'member_id' => $loggedAccountID,
		            'first_name' => $post['first_name'],
		            'last_name' => $post['last_name'],
		            'mobile' => $post['mobile'],
		            'email' => $post['email'],
		            'account_type' => $post['account_type'],
		            'pincode' => $post['pincode'],
		            'application_no' => $decodeResponse['p_APPLICATION_NO'],
		            'tracker_id' => $decodeResponse['trackerId'],
		            'web_url' => $decodeResponse['webUrl'],
		            'status' => 1,
		            'created' => date('Y-m-d H:i:s'),
		            'created_by' => $loggedAccountID
		        );
		        $this->db->insert('current_account_list',$data);
		        $msg = '<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Application submitted successfully. '.$decodeResponse['message'].'</div>';
		        $this->Az->redirect('master/current', 'system_message_error',$msg);
	        }
	        else
	        {
	        	$msg = '<div class="alert alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Your application failed due to '.$decodeResponse['message'].'</div>';
		        $this->Az->redirect('master/current', 'system_message_error',$msg);
	        }

			
			
		}

	}

	public function axisAccount(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];

	 	$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(10, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		$key = AXIS_ACCOUNT_KEY;
 		$reqid = $this->generateRandomString();
		$datapost = array(
         	"timestamp" =>  time(),
         	"partnerId" =>  AXIS_ACCOUNT_PARTNER_ID,
         	"reqid"    =>  $reqid,
         );

		$body = array(
			'merchantcode' => $loggedUser['user_code'].$reqid,
			'type' => 1
		);

		$jwt = JWT::encode($datapost, $key, "HS256");

		$header = array(
		    'Token: '.$jwt,
		    'Accept: application/json',
		    'Content-Type: application/json',
		  );

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => AXIS_ACCOUNT_OPEN_API_URL,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>json_encode($body),
		  CURLOPT_HTTPHEADER => $header,
		));

		$output = curl_exec($curl);

		curl_close($curl);

		// save api response
		$apiData = array(
			'account_id' => $account_id,
			'member_id' => $loggedAccountID,
			'reqid' => $reqid,
			'api_response' => $output,
			'api_url' => AXIS_ACCOUNT_OPEN_API_URL,
			'request_body' => json_encode($body),
			'request_header' => json_encode($header),
			'created' => date('Y-m-d H:i:s')
		);
		$this->db->insert('axis_account_api_response',$apiData);

		$decodeResponse = json_decode($output,true);

		if(isset($decodeResponse['response_code']) && ($decodeResponse['response_code'] == 1 || $decodeResponse['response_code'] == 3))
		{
			redirect($decodeResponse['data']);
		}
		else
		{
			$msg = '<div class="alert alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Your application failed due to '.$decodeResponse['message'].'</div>';
		    $this->Az->redirect('master/dashboard', 'system_message_error',$msg);
		}
    
	
	}

	public function generateRandomString($length = 6) {

	    $characters = '012345678901234567890123456789';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	} 

	public function accountDetail(){

		$account_id = $this->User->get_domain_account();
		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(10, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		// get member mobile
		$get_member_mobile = $this->db->select('is_virtual_account,virtual_account_no,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$is_virtual_account = isset($get_member_mobile['is_virtual_account']) ? $get_member_mobile['is_virtual_account'] : 0;
		$virtual_account_no = isset($get_member_mobile['virtual_account_no']) ? $get_member_mobile['virtual_account_no'] : 0;
		$member_mobile = isset($get_member_mobile['mobile']) ? $get_member_mobile['mobile'] : '';

		// get virtual account code
		$virtualData = $this->User->get_account_data($account_id);
		$virtual_prefix = isset($virtualData['van_prefix']) ? $virtualData['van_prefix'] : '';
		$virtual_ifsc = isset($virtualData['van_ifsc']) ? $virtualData['van_ifsc'] : '';

		if(!$is_virtual_account)
		{

			$is_virtual_account = 1;
			$virtual_account_no = $virtual_prefix.$member_mobile;
			$this->db->where('id',$loggedAccountID);
			$this->db->where('account_id',$account_id);
			$this->db->update('users',array('is_virtual_account'=>1,'virtual_account_no'=>$virtual_account_no));
		}
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'is_virtual_account' => $is_virtual_account,
			'virtual_account_no' => $virtual_account_no,
			'virtual_ifsc' => $virtual_ifsc,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'current/van-account-detail'
        );
        $this->parser->parse('master/layout/column-1' , $data);
    
	
	}

	public function upgradeAccountAuth(){

		$account_id = $this->User->get_domain_account();
		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(10, $activeService)){
			$this->Az->redirect('master/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}
		
		// get member mobile
		$get_member_mobile = $this->db->select('is_virtual_account,virtual_account_no,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$is_virtual_account = isset($get_member_mobile['is_virtual_account']) ? $get_member_mobile['is_virtual_account'] : 0;
		$virtual_account_no = isset($get_member_mobile['virtual_account_no']) ? $get_member_mobile['virtual_account_no'] : 0;
		$member_mobile = isset($get_member_mobile['mobile']) ? $get_member_mobile['mobile'] : '';

		// get virtual account code
		$virtualData = $this->User->get_account_data($account_id);
		$virtual_prefix = isset($virtualData['van_prefix']) ? $virtualData['van_prefix'] : '';
		$virtual_ifsc = isset($virtualData['van_ifsc']) ? $virtualData['van_ifsc'] : '';

		$is_virtual_account = 1;
		$virtual_account_no = $virtual_prefix.$member_mobile;
		$this->db->where('id',$loggedAccountID);
		$this->db->where('account_id',$account_id);
		$this->db->update('users',array('is_virtual_account'=>1,'virtual_account_no'=>$virtual_account_no));
		$msg = '<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Virtual account details updated successfully.</div>';
		    $this->Az->redirect('master/current/accountDetail', 'system_message_error',$msg);
		
    
	
	}


	
}