<?php 
class Report extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkApiMemberPermission();
       	$this->load->model('portal/Complain_model');      
        $this->lang->load('portal/recharge', 'english');
        
    }

	public function recharge(){
		$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        
		$siteUrl = base_url();
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'recharge' => $recharge,
            'loggedUser'=>$loggedUser, 
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/recharge-history'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    }

    public function getRechargeList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
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
			0 => 'id',	
			1 => 'recharge_display_id',
			2 => 'user_code',
			3 => 'name',
			5 => 'created',
			9 => 'recharge_type',
		);
		
		
		
			
			$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')) as x";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
			$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')) as x WHERE x.id > 0";
			
			if($keyword != '') {   
				$sql.=" AND ( user_code LIKE '".$keyword."%' ";    
				$sql.=" OR mobile LIKE '".$keyword."%'";
				$sql.=" OR circle_code LIKE '".$keyword."%'";
				$sql.=" OR recharge_type LIKE '".$keyword."%'";
				$sql.=" OR recharge_display_id LIKE '".$keyword."%'";
				$sql.=" OR name LIKE '".$keyword."%' )";
			}

			

			if($date != '') {   
				$sql.=" AND ( Date(created) = '".$date."' )";    
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				$list['operator_name'] = $this->User->get_api_operator_name($list['api_id'],$list['operator_code'],$account_id);
				
				if($list['is_bbps_api'] == 1)
				{
					$list['operator_name'] = $list['operator_code'];
				}
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['recharge_display_id']."</a>";
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['operator_name'];
				$nestedData[] = $list['amount'].' /-';
				if($list['before_balance'])
				{
					$nestedData[] = $list['before_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				if($list['after_balance'])
				{
					$nestedData[] = $list['after_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				elseif($list['status'] == 4) {
					$nestedData[] = '<font color="red">Refund</font>';
				}

				$nestedData[] = '<i class="fa fa-comments" onclick="showComplainBox('.$list['id'].')" aria-hidden="true"></i>';

				
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

	public function bbps(){

		//get logged user info
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
            'content_block' => 'report/bbps-list'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}

	public function getBBPSList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
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
			0 => 'a.id',	
			1 => 'a.recharge_display_id',
			2 => 'b.user_code',
			3 => 'b.name',
			5 => 'a.created',
			9 => 'a.recharge_type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.recharge_type = 7 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.recharge_type = 7 AND a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.member_id = '$loggedAccountID')";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.mobile LIKE '".$keyword."%'";
				$sql.=" OR a.operator_code LIKE '".$keyword."%'";
				$sql.=" OR a.circle_code LIKE '".$keyword."%'";
				$sql.=" OR a.recharge_type LIKE '".$keyword."%'";
				$sql.=" OR a.recharge_display_id LIKE '".$keyword."%'";
				$sql.=" OR b.name LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( Date(a.created) = '".$date."' )";    
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$operator = $this->db->get_where('operator',array('operator_code'=>$list['operator_code']))->row_array();

				

				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['recharge_display_id']."</a>";
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $operator['operator_name'];
				$nestedData[] = $list['account_number'];
				$nestedData[] = $list['customer_name'];
				$nestedData[] = $list['amount'].' /-';
				if($list['before_balance'])
				{
					$nestedData[] = $list['before_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				if($list['after_balance'])
				{
					$nestedData[] = $list['after_balance'].' /-';
				}
				else
				{
					$nestedData[] = '0 /-';
				}
				$nestedData[] = $list['txid'];
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
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

	

	public function moneyTransfer(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		
		
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
            'content_block' => 'report/money-transfer-list'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
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
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID')";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID')";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '".$keyword."%'";
				$sql.=" OR a.account_no LIKE '".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '".$keyword."%' )";
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
				$nestedData[] = $list['memberID'];
				$nestedData[] = $list['account_holder_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['account_no'];
				$nestedData[] = $list['ifsc'];
				$nestedData[] = $list['transfer_amount'].' /-';
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = $list['rrn'];
				
				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4 || $list['status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
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

	public function rechargeCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		
		
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
            'content_block' => 'report/recharge-commission-list'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getRechargeCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
        $fromDate = '';
        $toDate = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
        }
		
		$columns = array( 
		// datatable column index  => database column name
			5 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'RECHARGE' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.recharge_display_id FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'RECHARGE' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR c.recharge_display_id LIKE '".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
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
				$nestedData[] = $list['user_code'];
				$nestedData[] = $list['member_name'];
				$nestedData[] = $list['recharge_display_id'];
				$nestedData[] = '&#8377; '.$list['commision_amount'];
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

	public function getRechargeData($recharge_id = 0)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);

        $response = array();
        if(!$recharge_id || $recharge_id == '')
        {
            $response = array(
                'status' => 0,
                'msg' => 'Sorry ! Something went wrong.'
            );
        }
        else
        {
            // check recharge is valid or not
            $chk_recharge = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->num_rows();
            if(!$chk_recharge)
            {
                $response = array(
                    'status' => 0,
                    'msg' => 'Sorry ! Something went wrong.'
                );
            }
            else
            {
                $chk_recharge = $this->db->select('recharge_display_id,amount')->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->row_array();

                
                $response = array(
                    'status' => 1,
                    'msg' => 'Success',
                    'txnid' => $chk_recharge['recharge_display_id'],
                    'amount' => $chk_recharge['amount'],
                );
                
            }
        }

        echo json_encode($response);
    }

    // save member
    public function complainAuth()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        //check for foem validation
        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('recordID', 'Member Type', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Name', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            
            $this->Az->redirect('portal/report/recharge', 'system_message_error',lang('FORM_ERROR'));
        }
        else
        {   
            $recharge_id = $post['recordID'];
            // check recharge is valid or not
            $chk_recharge = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->num_rows();
            if(!$chk_recharge)
            {

                $this->Az->redirect('portal/report/recharge', 'system_message_error',lang('AUTHORIZE_ERROR'));  

            }

            $status = $this->Complain_model->saveComplain($post);
            
            if($status == true)
            {
                $this->Az->redirect('portal/report/recharge', 'system_message_error',lang('COMPLAIN_SAVED'));
            }
            else
            {
                $this->Az->redirect('portal/report/recharge', 'system_message_error',lang('COMMON_ERROR'));
            }
            
        }
    
    }

    public function aepsKyc(){

		//get logged user info
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
            'content_block' => 'report/aeps-kyc'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}

	public function getAepsKycList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
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
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code FROM tbl_aeps_member_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,c.state as state_name,d.city_name FROM tbl_aeps_member_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_aeps_state as c ON c.id = a.state_id LEFT JOIN tbl_city as d ON d.city_id = a.city_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.mobile LIKE '".$keyword."%'";
				$sql.=" OR a.first_name LIKE '".$keyword."%'";
				$sql.=" OR a.last_name LIKE '".$keyword."%'";
				$sql.=" OR a.aadhar_no LIKE '".$keyword."%'";
				$sql.=" OR a.pancard_no LIKE '".$keyword."%'";
				$sql.=" OR a.member_code LIKE '".$keyword."%'";
				$sql.=" OR a.shop_name LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( Date(a.created) = '".$date."' )";    
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
				$nestedData[] = 'MemberID - '.$list['member_code'].'<br />First Name - '.$list['first_name'].'<br />Last Name - '.$list['last_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['shop_name'];
				$nestedData[] = 'State - '.$list['state_name'].'<br />City - '.$list['city_name'].'<br />Address - '.$list['address'].'<br />Pin Code - '.$list['pin_code'];
				$nestedData[] = 'Aadhar No. - '.$list['aadhar_no'].'<br />PAN No. - '.$list['pancard_no'];

				$aadhar_str = 'Aadhar - Not Found';
				if($list['aadhar_photo'])
				{
					$aadhar_str = 'Aadhar - <a href="'.base_url($list['aadhar_photo']).'">Download</a>';
				}
				$pancard_str = 'PAN Card - Not Found';
				if($list['pancard_photo'])
				{
					$pancard_str = 'PAN Card - <a href="'.base_url($list['pancard_photo']).'">Download</a>';
				}


				$nestedData[] = $aadhar_str.'<br />'.$pancard_str;

				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				else{
					$nestedData[] = '<font color="red">Deactive</font>';
				}
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


	public function aepsHistory(){

		//get logged user info
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
            'content_block' => 'report/aeps-list'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}

	public function getAepsHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
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
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.mobile LIKE '".$keyword."%'";
				$sql.=" OR a.aadhar_no LIKE '".$keyword."%'";
				$sql.=" OR a.amount LIKE '".$keyword."%'";
				$sql.=" OR a.txnID LIKE '".$keyword."%' )";
			}

			if($date != '') {   
				$sql.=" AND ( Date(a.created) = '".$date."' )";    
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
				$nestedData[] = $list['member_code'];
				if($list['service'] == 'balinfo')
				{
					$nestedData[] = 'Balance Info';
				}
				elseif($list['service'] == 'ministatement')
				{
					$nestedData[] = 'Mini Statement';
				}
				elseif($list['service'] == 'balwithdraw')
				{
					$nestedData[] = 'Account Withdrawal';
				}
				elseif($list['service'] == 'aadharpay')
				{
					$nestedData[] = 'Aadhar Pay';
				}
				else
				{
					$nestedData[] = 'Not Found';
				}
				$nestedData[] = $list['aadhar_no'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = '<a href="#" onclick="showAepsModal('.$list['id'].'); return false;">'.$list['txnID'].'</a>';
				$nestedData[] = $list['message'];
	
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
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

	public function getAepsData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('member_aeps_transaction',array('id'=>$recordID,'account_id'=>$account_id))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('member_aeps_transaction',array('id'=>$recordID,'account_id'=>$account_id))->row_array();
 			if($dmrData['service'] == 'balwithdraw')
 			{
 				$str = '';
	        	$str = '<div class="table-responsive">';
				$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
				$str.='<tr>';
				$str.='<td>Txn Type</td><td>Account Withdrawal</td>';
				$str.='</tr>';
				if($dmrData['status'] == 1) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="orange">Pending</font></td>';
					$str.='</tr>';
				}
				elseif($dmrData['status'] == 2) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="green">Successful</font></td>';
					$str.='</tr>';
				}
				elseif($dmrData['status'] == 3) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="red">Failed</font></td>';
					$str.='</tr>';
				}
				
				
				$str.='<tr>';
				$str.='<td>Transfer Amount</td><td>INR '.$dmrData['transactionAmount'].'/-</td>';
				$str.='</tr>';

				$str.='<tr>';
				$str.='<td>Balance Amount</td><td>INR '.$dmrData['balance_amount'].'/-</td>';
				$str.='</tr>';

				$str.='<tr>';
				$str.='<td>Bank RRN</td><td>'.$dmrData['bank_rrno'].'</td>';
				$str.='</tr>';

				$str.='</table>';
				$str.='</div>';
 			}
 			elseif($dmrData['service'] == 'aadharpay')
 			{
 				$str = '';
	        	$str = '<div class="table-responsive">';
				$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
				$str.='<tr>';
				$str.='<td>Txn Type</td><td>Aadhar Pay</td>';
				$str.='</tr>';
				if($dmrData['status'] == 1) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="orange">Pending</font></td>';
					$str.='</tr>';
				}
				elseif($dmrData['status'] == 2) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="green">Successful</font></td>';
					$str.='</tr>';
				}
				elseif($dmrData['status'] == 3) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="red">Failed</font></td>';
					$str.='</tr>';
				}
				
				$str.='<tr>';
				$str.='<td>Transfer Amount</td><td>INR '.$dmrData['transactionAmount'].'/-</td>';
				$str.='</tr>';

				$str.='<tr>';
				$str.='<td>Balance Amount</td><td>INR '.$dmrData['balance_amount'].'/-</td>';
				$str.='</tr>';

				$str.='<tr>';
				$str.='<td>Bank RRN</td><td>'.$dmrData['bank_rrno'].'</td>';
				$str.='</tr>';

				$str.='</table>';
				$str.='</div>';
 			}
 			elseif($dmrData['service'] == 'balinfo')
 			{
 				$str = '';
	        	$str = '<div class="table-responsive">';
				$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
				$str.='<tr>';
				$str.='<td>Txn Type</td><td>Balance Inquiry</td>';
				$str.='</tr>';
				if($dmrData['status'] == 1) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="orange">Pending</font></td>';
					$str.='</tr>';
				}
				elseif($dmrData['status'] == 2) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="green">Successful</font></td>';
					$str.='</tr>';
				}
				elseif($dmrData['status'] == 3) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="red">Failed</font></td>';
					$str.='</tr>';
				}
				
				$str.='<tr>';
				$str.='<td>Balance Amount</td><td>INR '.$dmrData['balance_amount'].'/-</td>';
				$str.='</tr>';

				$str.='<tr>';
				$str.='<td>Bank RRN</td><td>'.$dmrData['bank_rrno'].'</td>';
				$str.='</tr>';

				$str.='</table>';
				$str.='</div>';
 			}
 			elseif($dmrData['service'] == 'ministatement')
 			{
 				$statementList = json_decode($dmrData['json_data'],true);
 				$str = '';
	        	$str = '<div class="table-responsive">';
				$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
				$str.='<tr>';
				$str.='<td>Txn Type</td><td>Mini Statement</td>';
				$str.='</tr>';
				if($dmrData['status'] == 1) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="orange">Pending</font></td>';
					$str.='</tr>';
				}
				elseif($dmrData['status'] == 2) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="green">Successful</font></td>';
					$str.='</tr>';
				}
				elseif($dmrData['status'] == 3) {
					$str.='<tr>';
					$str.='<td>Txn Status</td><td><font color="red">Failed</font></td>';
					$str.='</tr>';
				}
				
				$str.='<tr>';
				$str.='<td>Balance Amount</td><td>INR '.$dmrData['balance_amount'].'/-</td>';
				$str.='</tr>';

				$str.='<tr>';
				$str.='<td>Bank RRN</td><td>'.$dmrData['bank_rrno'].'</td>';
				$str.='</tr>';
				$str.='<tr>';
				$str.='<td colspan="2">Statement</td>';
				$str.='</tr>';
				$str.='<tr>';
				$str.='<td colspan="2">';
				$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
				$str.='<tr>';
				$str.='<th>#</th>';
				$str.='<th>Date</th>';
				$str.='<th>CR/DR</th>';
				$str.='<th>Amount</th>';
				$str.='<th>Description</th>';
				$str.='</tr>';
				$i = 1;
				if($statementList)
				{
					foreach($statementList as $list)
					{
						$str.='<tr>';
						$str.='<td>'.$i.'</td>';
						$str.='<td>'.$list['date'].'</td>';
						if($list['txnType'] == 'Dr')
						{
							$str.='<td><font color="red">DR</font></td>';
						}
						else
						{
							$str.='<td><font color="green">CR</font></td>';
						}
						$str.='<td>INR '.$list['amount'].'/-</td>';
						$str.='<td>'.$list['narration'].'</td>';
						$str.='</tr>';
						$i++;
					}
				}
				else
				{
					$str.='<tr>';
					$str.='<td colspan="5">No Record Found.</td>';
					$str.='</tr>';
				}
				$str.='</table>';
				$str.='</td>';
				$str.='</tr>';

				$str.='</table>';
				$str.='</div>';
 			}
 			
 			$response = array(
 				'status' => 1,
 				'msg' => 'Success',
 				'str' => $str
 			);
 		}

 		echo json_encode($response);
	}

	public function myAepsCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		
		
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
            'content_block' => 'report/my-aeps-commission-list'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getMyAepsCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
        $fromDate = '';
        $toDate = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
        }
		
		$columns = array( 
		// datatable column index  => database column name
			5 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_member_aeps_comm as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code FROM tbl_member_aeps_comm as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.txnID LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
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
				$nestedData[] = $list['user_code'];
				$nestedData[] = $list['member_name'];
				$nestedData[] = $list['txnID'];
				if($list['type'] == 4)
				{
					$nestedData[] = 'Cash Deposite';
				}
				elseif($list['type'] == 2)
				{
					$nestedData[] = 'Mini Statement';
				}
				elseif($list['type'] == 1)
				{
					$nestedData[] = 'Withdrawal';
				}
				elseif($list['type'] == 3)
				{
					$nestedData[] = 'Aadhar Pay';
				}
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = '&#8377; '.$list['com_amount'];
				if($list['is_surcharge'] == 1)
				{
					$nestedData[] = '<font color="red">DR</font>';
				}
				else
				{
					$nestedData[] = '<font color="green">CR</font>';
				}
				if($list['is_paid'] == 1)
				{
					$nestedData[] = '<font color="green">Yes</font>';
				}
				else
				{
					$nestedData[] = '<font color="red">No</font>';
				}
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




	public function payoutReport(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		
		
		
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
            'content_block' => 'report/payout-list'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getNewPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';	   	
	   	$fromDate = '';
        $toDate = '';
        $status = 0;
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
		}
		
		$firstLoad = 0;

		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.is_payout_open = 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND (b.created_by = '$loggedAccountID' OR a.user_id = '$loggedAccountID') AND a.is_payout_open = 0";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '".$keyword."%'";
				$sql.=" OR a.account_no LIKE '".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '".$keyword."%' )";
			}

			if($status)
            {
                $sql.=" AND status = '$status'";
            }



			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
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
				$nestedData[] = $list['memberID'];
				$nestedData[] = $list['account_holder_name'];
			//	$nestedData[] = $list['mobile'];
				$nestedData[] = $list['account_no'].'<br />'.$list['ifsc'];
				$nestedData[] = 'Tran. Amount - '.$list['transfer_amount'].'<br />Charge - '.$list['transfer_charge_amount'];
				if($list['txnType'] == 'NEFT')
				{
					$nestedData[] = 'NEFT';
				}
				elseif($list['txnType'] == 'RTGS')
				{
					$nestedData[] = 'RTGS';
				}
				elseif($list['txnType'] == 'IMPS')
				{
					$nestedData[] = 'IMPS';
				}
					elseif($list['txnType'] == 'UPI')
				{
					$nestedData[] = 'UPI';
				}
				else{
					$nestedData[] = '';
				}
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = $list['rrn'];

				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4 || $list['status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
					if($list['transaction_id'])

				{
					$get_api_message = json_decode($list['api_response'], true);

					$nestedData[] = $get_api_message['status'];

				}
				
				
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

	public function upiCollectionReport(){

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
            'content_block' => 'report/upiCollectionReport'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getUpiTransactionList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
        $toDate = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as transaction_type FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON a.type_id = c.id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as transaction_type FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON a.type_id = c.id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR a.vpa_id LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.description LIKE '%".$keyword."%'";
				$sql.=" OR a.bank_rrno LIKE '%".$keyword."%' )";
			}

			
			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();

			$amountSql = "SELECT SUM(a.amount) as totalAmount, COUNT(*) as totalRecord FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON a.type_id = c.id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.status = 2";
			
			if($keyword != '') {   
				$amountSql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$amountSql.=" OR b.name LIKE '%".$keyword."%'";
				$amountSql.=" OR a.txnid LIKE '%".$keyword."%'";
				$amountSql.=" OR a.vpa_id LIKE '%".$keyword."%'";
				$amountSql.=" OR a.amount LIKE '%".$keyword."%'";
				$amountSql.=" OR a.description LIKE '%".$keyword."%'";
				$amountSql.=" OR a.bank_rrno LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $amountSql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $getTotalAmount = $this->db->query($amountSql)->row_array();
            $totalSuccessAmount = isset($getTotalAmount['totalAmount']) ? $getTotalAmount['totalAmount'] : 0 ;
            $totalSuccessRecord = isset($getTotalAmount['totalRecord']) ? $getTotalAmount['totalRecord'] : 0 ;

            $amountSql = "SELECT SUM(a.amount) as totalAmount, COUNT(*) as totalRecord FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON a.type_id = c.id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.status = 3";
			
			if($keyword != '') {   
				$amountSql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$amountSql.=" OR b.name LIKE '%".$keyword."%'";
				$amountSql.=" OR a.txnid LIKE '%".$keyword."%'";
				$amountSql.=" OR a.vpa_id LIKE '%".$keyword."%'";
				$amountSql.=" OR a.amount LIKE '%".$keyword."%'";
				$amountSql.=" OR a.description LIKE '%".$keyword."%'";
				$amountSql.=" OR a.bank_rrno LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $amountSql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $getTotalAmount = $this->db->query($amountSql)->row_array();
            $totalFailedAmount = isset($getTotalAmount['totalAmount']) ? $getTotalAmount['totalAmount'] : 0 ;
            $totalFailedRecord = isset($getTotalAmount['totalRecord']) ? $getTotalAmount['totalRecord'] : 0 ;

            $amountSql = "SELECT SUM(a.charge_amount) as totalAmount, COUNT(*) as totalRecord FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON a.type_id = c.id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.status = 2";
			
			if($keyword != '') {   
				$amountSql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$amountSql.=" OR b.name LIKE '%".$keyword."%'";
				$amountSql.=" OR a.txnid LIKE '%".$keyword."%'";
				$amountSql.=" OR a.vpa_id LIKE '%".$keyword."%'";
				$amountSql.=" OR a.amount LIKE '%".$keyword."%'";
				$amountSql.=" OR a.description LIKE '%".$keyword."%'";
				$amountSql.=" OR a.bank_rrno LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $amountSql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $getTotalAmount = $this->db->query($amountSql)->row_array();
            $totalChargeAmount = isset($getTotalAmount['totalAmount']) ? $getTotalAmount['totalAmount'] : 0 ;
            $totalChargeRecord = isset($getTotalAmount['totalRecord']) ? $getTotalAmount['totalRecord'] : 0 ;
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a> <br />".$list['name'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['bank_rrno'];
				$nestedData[] = number_format($list['amount'],2).'/-';
				$nestedData[] = number_format($list['charge_amount'],2).'/-';
				$nestedData[] = number_format($list['credit_amount'],2).'/-';
				$nestedData[] = $list['vpa_id'];
				$nestedData[] = $list['description'];
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,
					"totalSuccess" => "&#8377; ".number_format($totalSuccessAmount,2)." / ".$totalSuccessRecord,
					"totalFailed" => "&#8377; ".number_format($totalFailedAmount,2)." / ".$totalFailedRecord,
					"totalCharge" => "&#8377; ".number_format($totalChargeAmount,2)." / ".$totalChargeRecord
					);

		echo json_encode($json_data);  // send data as json format
	}



	public function upiChargebackReport(){

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
            'content_block' => 'report/upiChargebackReport'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getUpiChargebackList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
        $toDate = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as transaction_type FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON a.type_id = c.id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.status = 4";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as transaction_type FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON a.type_id = c.id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.status = 4";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR a.vpa_id LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.description LIKE '%".$keyword."%'";
				$sql.=" OR a.bank_rrno LIKE '%".$keyword."%' )";
			}

			
			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();

			$amountSql = "SELECT SUM(a.amount) as totalAmount, COUNT(*) as totalRecord FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON a.type_id = c.id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.status = 4";
			
			if($keyword != '') {   
				$amountSql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$amountSql.=" OR b.name LIKE '%".$keyword."%'";
				$amountSql.=" OR a.txnid LIKE '%".$keyword."%'";
				$amountSql.=" OR a.vpa_id LIKE '%".$keyword."%'";
				$amountSql.=" OR a.amount LIKE '%".$keyword."%'";
				$amountSql.=" OR a.description LIKE '%".$keyword."%'";
				$amountSql.=" OR a.bank_rrno LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $amountSql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $getTotalAmount = $this->db->query($amountSql)->row_array();
            $totalSuccessAmount = isset($getTotalAmount['totalAmount']) ? $getTotalAmount['totalAmount'] : 0 ;
            $totalSuccessRecord = isset($getTotalAmount['totalRecord']) ? $getTotalAmount['totalRecord'] : 0 ;

            $amountSql = "SELECT SUM(a.amount) as totalAmount, COUNT(*) as totalRecord FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON a.type_id = c.id where a.account_id = '$account_id' AND a.member_id = '$loggedAccountID' AND a.status = 4";
			
			if($keyword != '') {   
				$amountSql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$amountSql.=" OR b.name LIKE '%".$keyword."%'";
				$amountSql.=" OR a.txnid LIKE '%".$keyword."%'";
				$amountSql.=" OR a.vpa_id LIKE '%".$keyword."%'";
				$amountSql.=" OR a.amount LIKE '%".$keyword."%'";
				$amountSql.=" OR a.description LIKE '%".$keyword."%'";
				$amountSql.=" OR a.bank_rrno LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $amountSql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $getTotalAmount = $this->db->query($amountSql)->row_array();
            $totalFailedAmount = isset($getTotalAmount['totalAmount']) ? $getTotalAmount['totalAmount'] : 0 ;
            $totalFailedRecord = isset($getTotalAmount['totalRecord']) ? $getTotalAmount['totalRecord'] : 0 ;

		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a> <br />".$list['name'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['bank_rrno'];
				$nestedData[] = number_format($list['amount'],2).'/-';
				$nestedData[] = number_format($list['charge_amount'],2).'/-';
				$nestedData[] = number_format($list['credit_amount'],2).'/-';
				$nestedData[] = $list['vpa_id'];
				$nestedData[] = $list['description'];
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				elseif($list['status'] == 4) {
					$nestedData[] = '<font color="red">Chargeback</font>';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,
					"totalSuccess" => "&#8377; ".number_format($totalSuccessAmount,2)." / ".$totalSuccessRecord
					);

		echo json_encode($json_data);  // send data as json format
	}
	
	
	
	public function newPayoutReport($status = ''){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
		
		
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'status'=>$status,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/new-payout-list'
        );
        $this->parser->parse('portal/layout/column-1' , $data);
    
	
	}


	public function getNewPayoutList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';	   	
	   	$fromDate = '';
        $toDate = '';
        $status = 0;
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
		}
		
		$firstLoad = 0;

		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.account_holder_name,b.account_no,c.name,c.user_code FROM tbl_open_money_payout as a INNER JOIN tbl_open_money_payout_user_benificary as b ON a.ben_id = b.ben_id INNER JOIN tbl_users as c ON a.user_id = c.id  WHERE a.account_id = '$account_id' AND  a.id > 0 AND  a.txnType!= 'UPI' AND a.user_id ='$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.account_holder_name,b.account_no,c.name,c.user_code FROM tbl_open_money_payout as a INNER JOIN tbl_open_money_payout_user_benificary as b ON a.ben_id = b.ben_id INNER JOIN tbl_users as c ON a.user_id = c.id  WHERE a.account_id = '$account_id' AND  a.id > 0 AND  a.txnType!= 'UPI' AND a.user_id ='$loggedAccountID'";
			
			if($keyword != '') {   
					$sql.=" AND ( c.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.account_holder_name LIKE '%".$keyword."%'";
				$sql.=" OR b.account_no LIKE '%".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				//$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.txnType LIKE '%".$keyword."%'";
				$sql.=" OR a.optxid LIKE '%".$keyword."%'";
				$sql.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($status)
            {
                $sql.=" AND status = '$status'";
            }



			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
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
				$nestedData[] = $list['user_code'];
				$nestedData[] = $list['account_holder_name'];
			
				$nestedData[] = $list['account_no'].'<br />'.$list['ifsc'];
				$nestedData[] = 'Tran. Amount - '.$list['transfer_amount'].'<br />Charge - '.$list['transfer_charge_amount'];
				if($list['txnType'] == 'neft')
				{
					$nestedData[] = 'neft';
				}
				elseif($list['txnType'] == 'rtgs')
				{
					$nestedData[] = 'rtgs';
				}
				elseif($list['txnType'] == 'imps')
				{
					$nestedData[] = 'imps';
				}
					elseif($list['txnType'] == 'UPI')
				{
					$nestedData[] = 'UPI';
				}
				else{
					$nestedData[] = '';
				}
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = $list['rrn'];

				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4 || $list['status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				
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




	

    
}