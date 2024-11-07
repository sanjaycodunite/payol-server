<?php 
class Wallet extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkApiMemberPermission();
        $this->load->model('portal/Wallet_model');		
        $this->lang->load('portal/wallet', 'english');
        
    }

    public function myWalletList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
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
            'content_block' => 'wallet/myWalletList'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getMyWalletList()
	{	
	    
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$status = 0;
	   	$by=0;
	   	$keyword = '';
	   	$member_id = '';
	   	$fromDate = '';
        $toDate = '';
		if($extra_search)
		{
	    	$filterData = explode('|',$extra_search);
			$status = isset($filterData[0]) ? trim($filterData[0]) : 0;
			$by = isset($filterData[1]) ? trim($filterData[1]) : 0;
			$keyword = isset($filterData[2]) ? trim($filterData[2]) : '';
			$member_id = isset($filterData[3]) ? trim($filterData[3]) : '';
			$fromDate = isset($filterData[4]) ? trim($filterData[4]) : '';
            $toDate = isset($filterData[5]) ? trim($filterData[5]) : '';
		}

		$firstLoad = 0;
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";

			$sql_new = $sql;
			
			$totalData = $this->db->query($sql)->result_array();
			$totalFiltered = count($totalData);  // when there is no search parameter then total number rows = total number filtered rows.
		    	
			if($keyword != '') {   
				$sql_new.=" AND ( b.user_code LIKE '".$keyword."%' ";  
				$sql_new.=" OR a.description LIKE '%".$keyword."%'";
				$sql_new.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($firstLoad == 1)
			{
				$sql_new.=" AND DATE(created) = '".date('Y-m-d')."'";
			}

			if($status)
            {
                $sql_new.=" AND type = '$status'";
                
            }
            
        
        if($by == 1) {   
				
			
				$sql_new.=" AND  a.credited_by in (3,329,344,415,462) ";  


			}
			
			if($by == 2) {   
				    
				$sql_new.=" AND  a.credited_by IS NULL ";  


			}
			
			
			if($member_id != '') {   
				$sql_new.=" AND  b.id = $member_id ";    
			}

			if($fromDate && $toDate)
            {
                $sql_new.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
               // $sql_new.=" AND type = '$status'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql_new)->num_rows();
			$sql_new.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		       
			$get_filter_data = $this->db->query($sql_new)->result_array();

            
		
		$data = array();
		$totalrecord = 0;
		$crAmount =0;
		$drAmount=0;
		$crRecord =0;
		$drRecord=0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
					
				if($list['type'] == 1)
					{
						
					$crAmount+= $list['amount'];
					$crRecord+= 1;
					
					}
					if($list['type'] ==2){
						$drAmount+= $list['amount'];
						$drRecord+= 1;
					}
					
				
					
					
					
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['before_balance'].' /-';
				if($list['type'] == 1) {
					$nestedData[] = '<font color="green">'.$list['amount'].' /-</font>';
				}
				elseif($list['type'] == 2) {
					$nestedData[] = '<font color="red">'.$list['amount'].' /-</font>';

				}
				$nestedData[] = $list['after_balance'].' /-';
				
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				if($list['type'] == 1) {
					$nestedData[] = '<font color="green">Cr.</font>';
				}
				elseif($list['type'] == 2) {
					$nestedData[] = '<font color="red">Dr.</font>';

				}

				
				//$nestedData[] = $list['description'];
				
				if($loggedAccountID == 242)
					{
						$nestedData[] = $list['description']."<br>".'<a href="#" onclick="viewNarration('.$list['id'].'); return false;" class="btn btn-sm btn-primary">Edit</a>';
					}
					else
					{

				$nestedData[] = $list['description'];
					 }

				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['status'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				

				$data[] = $nestedData;
				
				
				
			$i++;}
		}


        
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( count($totalData) ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					"crAmount" =>'&#8377;'.number_format($crAmount,2),
					"crRecord" =>$crRecord,
					"drAmount" =>'&#8377;'.number_format($drAmount,2),
					"drRecord" =>$drRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function myUpiWalletList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
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
            'content_block' => 'wallet/myUpiWalletList'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getMyUpiWalletList()
	{	
	    
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$status = 0;
	   	$by=0;
	   	$keyword = '';
	   	$member_id = '';
	   	$fromDate = '';
        $toDate = '';
		if($extra_search)
		{
	    	$filterData = explode('|',$extra_search);
			$status = isset($filterData[0]) ? trim($filterData[0]) : 0;
			$by = isset($filterData[1]) ? trim($filterData[1]) : 0;
			$keyword = isset($filterData[2]) ? trim($filterData[2]) : '';
			$member_id = isset($filterData[3]) ? trim($filterData[3]) : '';
			$fromDate = isset($filterData[4]) ? trim($filterData[4]) : '';
            $toDate = isset($filterData[5]) ? trim($filterData[5]) : '';
		}

		$firstLoad = 0;
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_upi_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";

			$sql_new = $sql;
			
			$totalData = $this->db->query($sql)->result_array();
			$totalFiltered = count($totalData);  // when there is no search parameter then total number rows = total number filtered rows.
		    	
			if($keyword != '') {   
				$sql_new.=" AND ( b.user_code LIKE '".$keyword."%' ";  
				$sql_new.=" OR a.description LIKE '%".$keyword."%'";
				$sql_new.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($firstLoad == 1)
			{
				$sql_new.=" AND DATE(created) = '".date('Y-m-d')."'";
			}

			if($status)
            {
                $sql_new.=" AND type = '$status'";
                
            }
            
        
        if($by == 1) {   
				
			
				$sql_new.=" AND  a.credited_by in (3,329,344,415) ";  


			}
			
			if($by == 2) {   
				    
				$sql_new.=" AND  a.credited_by IS NULL ";  


			}
			
			
			if($member_id != '') {   
				$sql_new.=" AND  b.id = $member_id ";    
			}

			if($fromDate && $toDate)
            {
                $sql_new.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
               // $sql_new.=" AND type = '$status'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql_new)->num_rows();
			$sql_new.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		       
			$get_filter_data = $this->db->query($sql_new)->result_array();

            
		
		$data = array();
		$totalrecord = 0;
		$crAmount =0;
		$drAmount=0;
		$crRecord =0;
		$drRecord=0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
					
				if($list['type'] == 1)
					{
						
					$crAmount+= $list['amount'];
					$crRecord+= 1;
					
					}
					if($list['type'] ==2){
						$drAmount+= $list['amount'];
						$drRecord+= 1;
					}
					
				
					
					
					
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['before_balance'].' /-';
				if($list['type'] == 1) {
					$nestedData[] = '<font color="green">'.$list['amount'].' /-</font>';
				}
				elseif($list['type'] == 2) {
					$nestedData[] = '<font color="red">'.$list['amount'].' /-</font>';

				}
				$nestedData[] = $list['after_balance'].' /-';
				
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				if($list['type'] == 1) {
					$nestedData[] = '<font color="green">Cr.</font>';
				}
				elseif($list['type'] == 2) {
					$nestedData[] = '<font color="red">Dr.</font>';

				}

				
				

				$nestedData[] = $list['description'];
					

				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['status'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				

				$data[] = $nestedData;
				
				
				
			$i++;}
		}


        
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( count($totalData) ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					"crAmount" =>'&#8377;'.number_format($crAmount,2),
					"crRecord" =>$crRecord,
					"drAmount" =>'&#8377;'.number_format($drAmount,2),
					"drRecord" =>$drRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}

	

	public function myRequestList()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		$siteUrl = site_url();

		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/myRequestList',
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('portal/layout/column-1', $data);
		
    }
	
	public function getMyRequestList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		$accountID = $loggedUser['id'];
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
			5 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_member_fund_request as a LEFT JOIN tbl_users as b ON b.id = a.member_id WHERE a.request_wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id = '$accountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name, b.user_code as member_code FROM tbl_member_fund_request as a LEFT JOIN tbl_users as b ON b.id = a.member_id WHERE a.request_wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id = '$accountID'";	

			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR a.request_id LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( DATE(a.created) = '".$date."%' )";
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 6 : $requestData['order'][0]['column'] : 6;
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
				$nestedData[] = $list['member_name'].' ('.$list['member_code'].')';
				$nestedData[] = $list['request_id'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = 'INR '.number_format($list['request_amount'],2);
				$nestedData[] = date('d-m-Y',strtotime($list['created']));
				
				if($list['status'] == 1)
				{
					$nestedData[] = '<font color="black">Pending</font>';
				}
				elseif($list['status'] == 2)
				{
					$nestedData[] = '<font color="green">Approved</font>';
					
				}
				elseif($list['status'] == 3)
				{
					$nestedData[] = '<font color="red">Rejected</font>';
				}
				
				
				
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

	public function fundRequest()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		$siteUrl = site_url();

		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/fundRequest',
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('portal/layout/column-1', $data);
		
    }

    public function requestAuth()
	{
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->fundRequest();
		}
		else
		{
			
			$status = $this->Wallet_model->generateFundRequest($post);
			
			if($status == true)
			{
				$this->Az->redirect('portal/wallet/myRequestList', 'system_message_error',lang('REQUEST_GENERATE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('portal/wallet/myRequestList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
		}
	
	}
	
	
	
	
	
	
	public function getNarrationData($recharge_id = 0)
	{
		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $chk_txn_id = $this->db->get_where('member_wallet',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
        if($chk_txn_id)
        {

			// check recharge status
			$get_recharge_data = $this->db->get_where('member_wallet',array('id'=>$recharge_id))->row_array();
			
			$recharge_unique_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
			$narration = isset($get_recharge_data['description']) ? $get_recharge_data['description'] : 0 ;

				
			$response = array(
				'status'=>1,
				'txnid' => $recharge_unique_id,
				'narration' => $narration
			);
		}
		else
		{
			$response = array(
				'status'=>0,
				'msg' => 'Sorry ! You are not authorized to access this page.'
			);
		}
		echo json_encode($response);
	}



	public function updateNarration()
	{
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];


		$post = $this->security->xss_clean($this->input->post());

		$recharge_id = isset($post['recordID']) ? $post['recordID'] : 0 ;
		$bank_rrn = isset($post['narration']) ? $post['narration'] : 0 ;
		if(!$bank_rrn)
		{
			$this->Az->redirect('portal/wallet/myWalletList', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Please enter Narration.</div>');
		}
		// check member
		$chkMember = $this->db->get_where('member_wallet',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('portal/wallet/myWalletList', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		

		// check recharge status
		$get_recharge_data = $this->db->get_where('member_wallet',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		
		$this->db->where('account_id',$account_id);
		$this->db->where('member_id',$loggedAccountID);
		$this->db->where('id',$recharge_id);
		$this->db->update('member_wallet',array('description'=>$bank_rrn,'updated'=>date('Y-m-d H:i:s')));
		
		$this->Az->redirect('portal/wallet/myWalletList', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Narration successfully Updated.</div>');
	}
    
    
    
    public function downloadExcel()
	{
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$siteUrl = base_url();		
		$post = $this->input->post();
		$keyword = isset($post['keyword']) ? trim($post['keyword']) : '';
		$fromDate = isset($post['from_date']) ? trim($post['from_date']) : '';
        $toDate = isset($post['to_date']) ? trim($post['to_date']) : '';
        $status = isset($post['status']) ? trim($post['status']) : '';
        $by = isset($post['by']) ? trim($post['by']) : '';

        $sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id ='$loggedAccountID'";
			
		if($keyword != '') {   
			$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
			$sql.=" OR b.mobile LIKE '%".$keyword."%'";
			$sql.=" OR a.description LIKE '%".$keyword."%'";
			$sql.=" OR a.amount LIKE '%".$keyword."%'";
			$sql.=" OR b.name LIKE '%".$keyword."%' )";
		}

		if($fromDate && $toDate)
        {
            $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
        }

        if($status)
        {
                $sql.=" AND type = '$status'";
        
        }

         if($by == 1) {   
				
			
				$sql.=" AND  a.credited_by in (3,329,344,415) ";  


			}
			
			if($by == 2) {   
				    
				$sql.=" AND  a.credited_by IS NULL ";  


			}


		
		$sql.=" ORDER BY a.created DESC";
	
	
	
		$get_filter_data = $this->db->query($sql)->result_array();

        $fileName = 'api_member_wallet_history.csv';
        header('Content-type: text/csv');
        header('Content-disposition: attachment;filename='.$fileName);
        header("Refresh:0; url=".$siteUrl."portal/wallet/myWalletList");
        echo "#,Member ID,Name,Before Amount,CR/DR Amount,After Amount,Datetime,Type,Description,".PHP_EOL;
        if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				if($list['type'] == 1) {
					$type = 'CR';
				}
				elseif($list['type'] == 2) {
					$type = 'DR';

				}
				echo "$i,$list[user_code],$list[name],$list[before_balance],$list[amount],$list[after_balance],".date('d-M-Y H:i:s',strtotime($list['created'])).",$type,$list[description],".PHP_EOL;
				
				$i++;
			}
		}
	}

	public function downloadUpiExcel()
	{
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$siteUrl = base_url();		
		$post = $this->input->post();
		$keyword = isset($post['keyword']) ? trim($post['keyword']) : '';
		$fromDate = isset($post['from_date']) ? trim($post['from_date']) : '';
        $toDate = isset($post['to_date']) ? trim($post['to_date']) : '';
        $status = isset($post['status']) ? trim($post['status']) : '';
        $by = isset($post['by']) ? trim($post['by']) : '';

        $sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_upi_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id ='$loggedAccountID'";
			
		if($keyword != '') {   
			$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
			$sql.=" OR b.mobile LIKE '%".$keyword."%'";
			$sql.=" OR a.description LIKE '%".$keyword."%'";
			$sql.=" OR a.amount LIKE '%".$keyword."%'";
			$sql.=" OR b.name LIKE '%".$keyword."%' )";
		}

		if($fromDate && $toDate)
        {
            $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
        }

        if($status)
        {
                $sql.=" AND type = '$status'";
        
        }

         if($by == 1) {   
				
			
				$sql.=" AND  a.credited_by in (3,329,344,415) ";  


			}
			
			if($by == 2) {   
				    
				$sql.=" AND  a.credited_by IS NULL ";  


			}


		
		$sql.=" ORDER BY a.created DESC";
	
	
	
		$get_filter_data = $this->db->query($sql)->result_array();

        $fileName = 'api_member_upi_wallet_history.csv';
        header('Content-type: text/csv');
        header('Content-disposition: attachment;filename='.$fileName);
        header("Refresh:0; url=".$siteUrl."portal/wallet/myWalletList");
        echo "#,Member ID,Name,Before Amount,CR/DR Amount,After Amount,Datetime,Type,Description,".PHP_EOL;
        if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				if($list['type'] == 1) {
					$type = 'CR';
				}
				elseif($list['type'] == 2) {
					$type = 'DR';

				}
				echo "$i,$list[user_code],$list[name],$list[before_balance],$list[amount],$list[after_balance],".date('d-M-Y H:i:s',strtotime($list['created'])).",$type,$list[description],".PHP_EOL;
				
				$i++;
			}
		}
	}



	public function apiRequestList()
    {    

    	$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		$siteUrl = site_url();

		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/apiRequestList',
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('portal/layout/column-1', $data);
		
    }
	
	public function getApiRequestList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		$accountID = $loggedUser['id'];
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
			8 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.name as member_name, b.user_code as member_code FROM tbl_api_member_fund_request as a LEFT JOIN tbl_users as b ON b.id = a.member_id WHERE  a.account_id = '$account_id' AND a.member_id = '$accountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name, b.user_code as member_code FROM tbl_api_member_fund_request as a LEFT JOIN tbl_users as b ON b.id = a.member_id WHERE a.account_id = '$account_id' AND a.member_id = '$accountID'";	

			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR a.request_id LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( DATE(a.created) = '".$date."%' )";
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 6 : $requestData['order'][0]['column'] : 6;
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
				$nestedData[] = $list['member_name'].' ('.$list['member_code'].')';
				$nestedData[] = $list['txn_id'];
				$nestedData[] = $list['ref_no'];
				$nestedData[] = $list['utr_no'];
				$nestedData[] = 'INR '.number_format($list['amount'],2);
				$nestedData[] = $list['remark'];
				$nestedData[] = date('d-m-Y',strtotime($list['created']));
				
				if($list['status'] == 1)
				{
					$nestedData[] = '<font color="black">Pending</font>';
				}
				elseif($list['status'] == 2)
				{
					$nestedData[] = '<font color="green">Approved</font>';
					
				}
				elseif($list['status'] == 3)
				{
					$nestedData[] = '<font color="red">Rejected</font>';
				}
				
				
				
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



	 public function oldWalletList(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
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
            'content_block' => 'wallet/oldWalletList'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getOldWalletList()
	{	
	    
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$status = 0;
	   	$by=0;
	   	$keyword = '';
	   	$member_id = '';
	   	$fromDate = '';
        $toDate = '';
		if($extra_search)
		{
	    	$filterData = explode('|',$extra_search);
			$status = isset($filterData[0]) ? trim($filterData[0]) : 0;
			$by = isset($filterData[1]) ? trim($filterData[1]) : 0;
			$keyword = isset($filterData[2]) ? trim($filterData[2]) : '';
			$member_id = isset($filterData[3]) ? trim($filterData[3]) : '';
			$fromDate = isset($filterData[4]) ? trim($filterData[4]) : '';
            $toDate = isset($filterData[5]) ? trim($filterData[5]) : '';
		}

		$firstLoad = 0;
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'created',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_wallet_till_oct_2023 as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.wallet_type = 1 AND a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";

			$sql_new = $sql;
			
			$totalData = $this->db->query($sql)->result_array();
			$totalFiltered = count($totalData);  // when there is no search parameter then total number rows = total number filtered rows.
		    	
			if($keyword != '') {   
				$sql_new.=" AND ( b.user_code LIKE '".$keyword."%' ";  
				$sql_new.=" OR a.description LIKE '%".$keyword."%'";
				$sql_new.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($firstLoad == 1)
			{
				$sql_new.=" AND DATE(created) = '".date('Y-m-d')."'";
			}

			if($status)
            {
                $sql_new.=" AND type = '$status'";
                
            }
            
        
        if($by == 1) {   
				
			
				$sql_new.=" AND  a.credited_by in (3,329,344,415) ";  


			}
			
			if($by == 2) {   
				    
				$sql_new.=" AND  a.credited_by IS NULL ";  


			}
			
			
			if($member_id != '') {   
				$sql_new.=" AND  b.id = $member_id ";    
			}

			if($fromDate && $toDate)
            {
                $sql_new.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
               // $sql_new.=" AND type = '$status'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql_new)->num_rows();
			$sql_new.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		       
			$get_filter_data = $this->db->query($sql_new)->result_array();

            
		
		$data = array();
		$totalrecord = 0;
		$crAmount =0;
		$drAmount=0;
		$crRecord =0;
		$drRecord=0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
					
				if($list['type'] == 1)
					{
						
					$crAmount+= $list['amount'];
					$crRecord+= 1;
					
					}
					if($list['type'] ==2){
						$drAmount+= $list['amount'];
						$drRecord+= 1;
					}
					
				
					
					
					
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['before_balance'].' /-';
				if($list['type'] == 1) {
					$nestedData[] = '<font color="green">'.$list['amount'].' /-</font>';
				}
				elseif($list['type'] == 2) {
					$nestedData[] = '<font color="red">'.$list['amount'].' /-</font>';

				}
				$nestedData[] = $list['after_balance'].' /-';
				
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				if($list['type'] == 1) {
					$nestedData[] = '<font color="green">Cr.</font>';
				}
				elseif($list['type'] == 2) {
					$nestedData[] = '<font color="red">Dr.</font>';

				}

				
				//$nestedData[] = $list['description'];
				
				if($loggedAccountID == 242)
					{
						$nestedData[] = $list['description']."<br>".'<a href="#" onclick="viewNarration('.$list['id'].'); return false;" class="btn btn-sm btn-primary">Edit</a>';
					}
					else
					{

				$nestedData[] = $list['description'];
					 }

				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['status'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				

				$data[] = $nestedData;
				
				
				
			$i++;}
		}


        
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( count($totalData) ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					"crAmount" =>'&#8377;'.number_format($crAmount,2),
					"crRecord" =>$crRecord,
					"drAmount" =>'&#8377;'.number_format($drAmount,2),
					"drRecord" =>$drRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}



	public function mainwalletTransfer(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$upi_wallet_balance = $this->User->getMemberUpiWalletBalanceSP($loggedAccountID,1);

		$today_date =  date('Y-m-d');
			

			//today transfer_amount
		$get_today_transfer_amount = $this->db->select('SUM(transfer_amount) as transfer_amount')->get_where('upi_wallet_transfer',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'DATE(created)'=>$today_date,'credited_by'=>$loggedAccountID))->row_array();

        $today_transfer_amount = isset($get_today_transfer_amount['transfer_amount']) ? $get_today_transfer_amount['transfer_amount'] : 0 ;


        $get_release_amount = $this->db->select('SUM(release_amount) as release_amount')->get_where('upi_transaction',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'DATE(created)'=>$today_date))->row_array();

		$release_amount = isset($get_release_amount['release_amount']) ? $get_release_amount['release_amount'] : 0 ;

		$transfer_amount = $release_amount - $today_transfer_amount;



  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'upi_wallet_balance' => $upi_wallet_balance,
			'release_amount' =>$transfer_amount,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'wallet/mainwalletTransfer'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function mainwalletTransferAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        if ($this->form_validation->run() == FALSE) {
			
			$this->mainwalletTransfer();
		}
		else
		{
			if($post['amount'] < 0){
				
				$this->Az->redirect('portal/wallet/mainwalletTransferAuth', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Amount should be grater than 0.</div>');

			 }
			$upi_wallet_balance = $this->User->getMemberUpiWalletBalanceSP($loggedAccountID,1);

			$today_date =  date('Y-m-d');
			

			//today transfer_amount
			$get_today_transfer_amount = $this->db->select('SUM(transfer_amount) as transfer_amount')->get_where('upi_wallet_transfer',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'DATE(created)'=>$today_date,'credited_by'=>$loggedAccountID))->row_array();

        	$today_transfer_amount = isset($get_today_transfer_amount['transfer_amount']) ? $get_today_transfer_amount['transfer_amount'] : 0 ;


        	 $get_release_amount = $this->db->select('SUM(release_amount) as release_amount')->get_where('upi_transaction',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'DATE(created)'=>$today_date))->row_array();

		     $release_amount = isset($get_release_amount['release_amount']) ? $get_release_amount['release_amount'] : 0 ;


			$transfer_amount = $release_amount - $today_transfer_amount;

			if($transfer_amount < $post['amount']){
				
			$this->Az->redirect('portal/wallet/mainwalletTransferAuth', 'system_message_error',lang('UPI_WALLET_BALANCE_ERROR'));

			 }

			
			if($upi_wallet_balance < $post['amount']){

				$this->Az->redirect('portal/wallet/mainwalletTransferAuth', 'system_message_error',lang('UPI_WALLET_BALANCE_ERROR'));
			}
			else{

				//deduct upi-wallet amount 

				$before_upi_wallet_balance = $this->User->getMemberUpiWalletBalanceSP($loggedAccountID);
				$amount = isset($post['amount']) ? $post['amount'] : 0;

				$after_upi_wallet_balance = $before_upi_wallet_balance - $amount;

				$upi_wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $loggedAccountID,    
	            'before_balance'      => $before_upi_wallet_balance,
	            'amount'              => $amount,  
	            'after_balance'       => $after_upi_wallet_balance,      
	            'status'              => 1,
	            'type'                => 2,    
	            'wallet_type'         => 1,  
	            'created'             => date('Y-m-d H:i:s'),      
	            'credited_by'         => $loggedAccountID,
	            'description'         => $post['description']            
	            );

	            $this->db->insert('member_upi_wallet',$upi_wallet_data);

	            // credit to main wallet amount

	            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
				
				$after_wallet_balance = $before_wallet_balance + $amount;

				$rwallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $loggedAccountID,    
	            'before_balance'      => $before_wallet_balance,
	            'amount'              => $amount,  
	            'after_balance'       => $after_wallet_balance,      
	            'status'              => 1,
	            'type'                => 1,    
	            'wallet_type'         => 1,  
	            'created'             => date('Y-m-d H:i:s'),      
	            'credited_by'         => $loggedAccountID,
	            'description'         => $post['description']            
	            );

	            $this->db->insert('member_wallet',$rwallet_data);


	            //save transcation
	            $data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $loggedAccountID,
	            'before_balance'      => $before_upi_wallet_balance,
	            'transfer_amount'     => $amount,  
	            'after_balance'       => $after_upi_wallet_balance, 
	            'created'             => date('Y-m-d H:i:s'),      
	            'credited_by'         => $loggedAccountID,
	            'description'         => $post['description']            
	            );

	            $this->db->insert('upi_wallet_transfer',$data);

	            $this->Az->redirect('portal/wallet/mainwalletTransfer', 'system_message_error',lang('WALLET_TRANSFER_SUCCESS'));
				
			}
			
		}
	
	}
	
	public function virtualAccount()
    {    

    	$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$siteUrl = site_url();
		

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(32, $activeService)){
			$this->Az->redirect('portal/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check qr is active or not
	 	$chk_va_status = $this->db->get_where('users',array('is_virtual_account'=>1,'id'=>$loggedAccountID))->num_rows();
		if(!$chk_va_status)
		{
		    $this->Az->redirect('portal/wallet/activeVirtualAccount', 'system_message_error');
		}
		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/virtual-account',
            'chk_va_status' => $chk_va_status,
            'loggedAccountID'=>$loggedAccountID,
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('portal/layout/column-1', $data);
		
    }
    
	
	public function activeVirtualAccount()
    {    

    	$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$siteUrl = site_url();
		

		$activeService = $this->User->account_active_service($loggedUser['id']);
		if(!in_array(32, $activeService)){
			$this->Az->redirect('portal/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
		}

		// check qr is active or not
	 	$chk_va_status = $this->db->get_where('users',array('is_virtual_account'=>1,'id'=>$loggedAccountID))->num_rows();
		
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'wallet/active-virtual-account',
            'chk_va_status' => $chk_va_status,
            'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('portal/layout/column-1', $data);
		
    }
    
    
    public function virtualAccountAuth()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		//check for foem validation
		$post = $this->input->post();

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - VAN  Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        
        $van_prefix ='9990';
        $virtual_account_no = $van_prefix.$loggedUser['mobile'];
		 $data = array(
		            'account_id' => $account_id,
		            'member_id' => $loggedAccountID,
		            'mobile' => $loggedUser['mobile'],
		            'van_account_number'=>$virtual_account_no,
		            'van_ifsc' =>'UTIB0CCH274',
		            'status' => 1,
		            'created' => date('Y-m-d H:i:s'),
		            'created_by' => $loggedAccountID
		        );
		        
		        $this->db->insert('tbl_va_activation',$data);
		        
		        
		    $this->db->where('id',$loggedAccountID);
			$this->db->where('account_id',$account_id);
			$this->db->update('users',array('is_virtual_account'=>1,'virtual_account_no'=>$virtual_account_no));
		  
		  $msg = '<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Virtual Account  created successfully.</div>';
		  $this->Az->redirect('portal/wallet/virtualAccount', 'system_message_error',$msg);
		        
		
		
	
	}




	
	
}