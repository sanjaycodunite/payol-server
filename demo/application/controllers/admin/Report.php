<?php 
class Report extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkAdminPermission();
       	$this->lang->load('admin/dashboard', 'english');
       	$this->load->model('master/Dmt_model');		
       	$this->load->model('master/Aeps_model');		
        
    }

	public function recharge($status = 0){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        	



        $operator = $this->db->get('operator')->result_array();	
		
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'recharge' => $recharge,
            'loggedUser'=>$loggedUser, 
            'status' => $status,
            'user_type'=>$user_type,
            'operator'=> $operator,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/recharge-history'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getRechargeList()
	{	
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
        $fromDate = '';
        $toDate = '';
        $status = 0;
        $user_type = '';
        $operator = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $user_type = isset($filterData[4]) ? trim($filterData[4]) : '';
            $operator = isset($filterData[5]) ? trim($filterData[5]) : 0;
            
        }

        $firstLoad = 0;
        
		$columns = array( 
		// datatable column index  => database column name
			0 => 'created',	
			1 => 'recharge_display_id',
			2 => 'user_code',
			3 => 'name',
			5 => 'created',
			9 => 'recharge_type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id') as x ";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
			if($accountData['account_type'] != 2)
			{
				$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id') as x WHERE x.id > 0";
			}
			else
			{
				$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id') as x WHERE x.id > 0";
			}

			if($keyword != '') {   
				$sql.=" AND ( user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR mobile LIKE '%".$keyword."%'";
				$sql.=" OR circle_code LIKE '%".$keyword."%'";
				$sql.=" OR operator_name LIKE '%".$keyword."%'";
				$sql.=" OR recharge_type LIKE '%".$keyword."%'";
				$sql.=" OR recharge_display_id LIKE '%".$keyword."%'";
				$sql.=" OR name LIKE '%".$keyword."%' )";
			}

			if($firstLoad == 1)
			{
				$sql.=" AND DATE(created) = '".date('Y-m-d')."'";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			if($status)
            {
                $sql.=" AND status = '$status'";
            }

            if($operator){
            
             $sql.=" AND system_opt_id = '$operator'";	
            
            }

			if($user_type != ''){
            
             $sql.=" AND x.role_id = '$user_type'";	
            
            }
	

			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$sql.=" GROUP BY id";
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY created DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			


			$get_filter_data = $this->db->query($sql)->result_array();


			$sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id') as x WHERE x.id > 0";


			if($keyword != '') {   
				$sql_summery.=" AND ( user_code LIKE '".$keyword."%' ";    
				$sql_summery.=" OR mobile LIKE '".$keyword."%'";
				$sql_summery.=" OR circle_code LIKE '".$keyword."%'";
				$sql_summery.=" OR operator_name LIKE '".$keyword."%'";
				$sql_summery.=" OR recharge_type LIKE '".$keyword."%'";
				$sql_summery.=" OR recharge_display_id LIKE '".$keyword."%'";
				$sql_summery.=" OR name LIKE '".$keyword."%' )";
			}
			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            if($firstLoad == 1)
			{
				$sql.=" AND DATE(created) = '".date('Y-m-d')."'";
			}
			
			
            if($operator){
            
             $sql_summery.=" AND system_opt_id = '$operator'";	
            
            }

			if($user_type != ''){
            
             $sql_summery.=" AND x.role_id = '$user_type'";	
            
            }

            
			
			 
			$sql_success_summery = $sql_summery;	
			$sql_success_summery.=" AND x.status = 2";

			
			$get_success_recharge = $this->db->query($sql_success_summery)->row_array();
			
			$successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;
	    	
			
			$sql_pending_summery = $sql_summery;	
			$sql_pending_summery.=" AND x.status = 1";	
			$get_pending_recharge = $this->db->query($sql_pending_summery)->row_array();
			
			$pendingAmount = isset($get_pending_recharge['totalAmount']) ? number_format($get_pending_recharge['totalAmount'],2) : '0.00';
	        $pendingRecord = isset($get_pending_recharge['totalRecord']) ? $get_pending_recharge['totalRecord'] : 0;
	    	

	        $sql_failed_summery = $sql_summery;
			$sql_failed_summery.=" AND x.status = 3";	
			$get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();
			
			
	        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
	        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;
	    	
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				/*$list['operator_name'] = $this->User->get_api_operator_name($list['api_id'],$list['operator_code'],$account_id);*/

				if($list['is_bbps_api'] == 1)
				{
					$list['operator_name'] = $list['operator_code'];
				}
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['recharge_display_id']."</a>";
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a> <br />".$list['name'];
				$nestedData[] = $list['mobile'].'<br />'.$list['operator_name'];
				$nestedData[] = $list['api_id'];
				$nestedData[] = $list['amount'].' /-';
				$balance_str = '';
				if($list['before_balance'])
				{
					$balance_str.='OB - '.$list['before_balance'].' /-<br />';
				}
				else
				{
					$balance_str.='OB - 0 /-<br />';
				}
				if($list['after_balance'])
				{
					$balance_str.='CB - '.$list['after_balance'].' /-<br />';
				}
				else
				{
					$balance_str.='CB - 0 /-<br />';
				}
				$nestedData[] = $balance_str;

				$nestedData[] = $list['operator_ref'];
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				$nestedData[] = "<a href=".base_url('admin/report/rechargeInvoice/').$list['recharge_display_id']." style='text-decoration:none;' target='_blank'>Invoice</a>";	
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
					if($is_cogent_instantpay_api)
					{
						$nestedData[] = 'Not Allowed';
					}
					else
					{
						$nestedData[] = '<a href="'.base_url('admin/report/refundRecharge').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this recharge?\')" class="btn btn-sm btn-primary">Refund</a> <a href="'.base_url('admin/report/successRecharge').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to success this recharge?\')" class="btn btn-sm btn-primary">Success</a>';
					}
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
					if($is_cogent_instantpay_api)
					{
						$nestedData[] = 'Not Allowed';
					}
					else
					{
						if($list['force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						else
						{
							$nestedData[] = '<a href="'.base_url('admin/report/refundRecharge').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this recharge?\')" class="btn btn-sm btn-primary">Refund</a>';
						}
					}
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
					if($is_cogent_instantpay_api)
					{
						$nestedData[] = 'Not Allowed';
					}
					else
					{
						if($list['force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						elseif($list['force_status'] == 2)
						{
							$nestedData[] = '<font color="green">Success</font>';
						}
						else
						{
							$nestedData[] = 'Not Allowed';
						}
					}
				}
				elseif($list['status'] == 4) {
					$nestedData[] = '<font color="red">Refund</font>';
					$nestedData[] = 'Not Allowed';
					
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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,
					"pendingAmount" => $pendingAmount,
					"pendingRecord" => $pendingRecord,
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,

					);

		echo json_encode($json_data);  // send data as json format
	}

	public function rechargeInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,c.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as c ON c.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id') as x WHERE x.recharge_display_id = '$id'";

		$detail = $this->db->query($sql)->row_array();

		$operator = isset($detail['operator_name']) ? $detail['operator_name'] : 'Not Available';

		
		
		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'operator' => $operator,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/recharge-invoice'
        );
        $this->parser->parse('admin/layout/column-2' , $data);
    
	
	}

	public function refundRecharge($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/recharge', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(1,2))->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/recharge', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Recharge Already Refunded.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;

		// update status
		$this->db->where('id',$recharge_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('recharge_history',array('status'=>4,'force_status'=>1));

		
		$get_before_balance = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();

		

    	$member_code = $get_before_balance['user_code'];    
    	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);    
    	$after_balance = $before_balance + $amount;

    	$wallet_data = array(
    		'account_id'          => $account_id,
			'member_id'           => $member_id,    
			'before_balance'      => $before_balance,
			'amount'              => $amount,  
			'after_balance'       => $after_balance,      
			'status'              => 1,
			'type'                => 1,      
			'created'             => date('Y-m-d H:i:s'),      
			'credited_by'         => 1,
			'description'         => 'Recharge Refund #'.$recharge_unique_id.' Credited'
        );

        $this->db->insert('member_wallet',$wallet_data);

        $user_wallet = array(
			'wallet_balance'=>$after_balance,        
        );    

		// get member role id
		// get account role id
		$get_role_id = $this->db->select('role_id,call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
		if($user_role_id == 6)
		{
			$user_call_back_url = isset($get_role_id['call_back_url']) ? $get_role_id['call_back_url'] : '' ;
			
    		$api_post_data = array();
    		$api_post_data['status'] = 'FAILED';
    		$api_post_data['txnid'] = $recharge_unique_id;
    		$api_post_data['operator_txnid'] = '';
    		$api_post_data['amount'] = $amount;

    		$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
			$output = curl_exec($ch); 
			curl_close($ch);

		}

		$this->Az->redirect('admin/report/recharge', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Recharge refunded successfully.</div>');
	}

	public function successRecharge($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/recharge', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/recharge', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Recharge Status Already Updated.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;

		// update status
		$this->db->where('id',$recharge_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('recharge_history',array('status'=>2,'force_status'=>2));

		// distribute commision
	    $this->User->distribute_recharge_commision($recharge_id,$recharge_unique_id,$amount,$member_id);
		
		// get member role id
		// get account role id
		$get_role_id = $this->db->select('role_id,call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
		if($user_role_id == 6)
		{
			$user_call_back_url = isset($get_role_id['call_back_url']) ? $get_role_id['call_back_url'] : '' ;
			
    		$api_post_data = array();
    		$api_post_data['status'] = 'SUCCESS';
    		$api_post_data['txnid'] = $recharge_unique_id;
    		$api_post_data['operator_txnid'] = $recharge_unique_id;
    		$api_post_data['amount'] = $amount;

    		$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
			$output = curl_exec($ch); 
			curl_close($ch);

		}

		$this->Az->redirect('admin/report/recharge', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Recharge got success.</div>');
	}

	public function bbps(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getBBPSList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.recharge_type = 7 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.recharge_type = 7 AND a.account_id = '$account_id'";
			
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
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '%".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$sql.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql.=" OR a.txnType LIKE '%".$keyword."%'";
				$sql.=" OR a.op_txn_id LIKE '%".$keyword."%'";
				$sql.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            if($status)
            {
            	$sql.=" AND a.status = '$status'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();

			$amountSql = "SELECT SUM(a.transfer_amount) as totalAmount, COUNT(*) as totalRecord FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.wallet_type = 1 AND a.status = 3";
			
			if($keyword != '') {   
				$amountSql.=" AND ( a.memberID LIKE '%".$keyword."%' ";    
				$amountSql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$amountSql.=" OR a.account_no LIKE '%".$keyword."%'";
				$amountSql.=" OR a.mobile LIKE '%".$keyword."%'";
				$amountSql.=" OR a.ifsc LIKE '%".$keyword."%'";
				$amountSql.=" OR b.user_code LIKE '%".$keyword."%'";
				$amountSql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$amountSql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $amountSql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $getTotalAmount = $this->db->query($amountSql)->row_array();
            $totalSuccessAmount = isset($getTotalAmount['totalAmount']) ? $getTotalAmount['totalAmount'] : 0 ;
            $totalSuccessRecord = isset($getTotalAmount['totalRecord']) ? $getTotalAmount['totalRecord'] : 0 ;

            $amountSql = "SELECT SUM(a.transfer_amount) as totalAmount, COUNT(*) as totalRecord FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.wallet_type = 1 AND a.status = 4";
			
			if($keyword != '') {   
				$amountSql.=" AND ( a.memberID LIKE '%".$keyword."%' ";    
				$amountSql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$amountSql.=" OR a.account_no LIKE '%".$keyword."%'";
				$amountSql.=" OR a.mobile LIKE '%".$keyword."%'";
				$amountSql.=" OR a.ifsc LIKE '%".$keyword."%'";
				$amountSql.=" OR b.user_code LIKE '%".$keyword."%'";
				$amountSql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$amountSql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $amountSql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $getTotalAmount = $this->db->query($amountSql)->row_array();
            $totalFailedAmount = isset($getTotalAmount['totalAmount']) ? $getTotalAmount['totalAmount'] : 0 ;
            $totalFailedRecord = isset($getTotalAmount['totalRecord']) ? $getTotalAmount['totalRecord'] : 0 ;

            $amountSql = "SELECT SUM(a.transfer_amount) as totalAmount, COUNT(*) as totalRecord FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.wallet_type = 1 AND a.status = 2";
			
			if($keyword != '') {   
				$amountSql.=" AND ( a.memberID LIKE '%".$keyword."%' ";    
				$amountSql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$amountSql.=" OR a.account_no LIKE '%".$keyword."%'";
				$amountSql.=" OR a.mobile LIKE '%".$keyword."%'";
				$amountSql.=" OR a.ifsc LIKE '%".$keyword."%'";
				$amountSql.=" OR b.user_code LIKE '%".$keyword."%'";
				$amountSql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$amountSql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $amountSql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $getTotalAmount = $this->db->query($amountSql)->row_array();
            $totalPendingAmount = isset($getTotalAmount['totalAmount']) ? $getTotalAmount['totalAmount'] : 0 ;
            $totalPendingRecord = isset($getTotalAmount['totalRecord']) ? $getTotalAmount['totalRecord'] : 0 ;

            $amountSql = "SELECT SUM(a.transfer_charge_amount) as totalAmount, COUNT(*) as totalRecord FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.wallet_type = 1 AND a.status = 3";
			
			if($keyword != '') {   
				$amountSql.=" AND ( a.memberID LIKE '%".$keyword."%' ";    
				$amountSql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$amountSql.=" OR a.account_no LIKE '%".$keyword."%'";
				$amountSql.=" OR a.mobile LIKE '%".$keyword."%'";
				$amountSql.=" OR a.ifsc LIKE '%".$keyword."%'";
				$amountSql.=" OR b.user_code LIKE '%".$keyword."%'";
				$amountSql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$amountSql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $amountSql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            $getTotalAmount = $this->db->query($amountSql)->row_array();
            $totalChargeAmount = isset($getTotalAmount['totalAmount']) ? $getTotalAmount['totalAmount'] : 0 ;
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['memberID'];
				$nestedData[] = $list['account_holder_name'].'<br />'.$list['mobile'];
				$nestedData[] = $list['account_no'].'<br />'.$list['ifsc'];
				$nestedData[] = 'Txn Amount : &#8377; '.$list['transfer_amount'].'<br />Charge Amount : &#8377; '.$list['transfer_charge_amount'];
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = $list['rrn'];
				
				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4 || $list['status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font><br />'.$list['api_response'];
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));

				if($list['status'] == 2) {
					/*$nestedData[] = '<a href="'.base_url('admin/report/refundPayout').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this transaction?\')" class="btn btn-sm btn-primary">Refund</a> <a href="#" onclick="successPayout('.$list['id'].'); return false;" class="btn btn-sm btn-success">Success</a>';*/
					$nestedData[] = '<button type="button" onclick="checkCibStatus('.$list['id'].'); return false;" class="btn btn-success btn-sm">Check Status</button>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = 'Not Allowed';
				}
				elseif($list['status'] == 4) {
					$nestedData[] = 'Not Allowed';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
				}
				
				
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
					"totalPending" => "&#8377; ".number_format($totalPendingAmount,2)." / ".$totalPendingRecord,
					"totalCharge" => "&#8377; ".number_format($totalChargeAmount,2)
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function checkCibStatus($recordID = 0)
	{

		$account_id = $this->User->get_domain_account();

		$getMemberID = $this->db->get_where('user_fund_transfer',array('id'=>$recordID))->row_array();
		$loggedAccountID = isset($getMemberID['user_id']) ? $getMemberID['user_id'] : 0 ;
		$transaction_id = isset($getMemberID['transaction_id']) ? $getMemberID['transaction_id'] : '' ;
		$amount = isset($getMemberID['transfer_amount']) ? $getMemberID['transfer_amount'] : 0 ;
		$final_amount = isset($getMemberID['total_wallet_charge']) ? $getMemberID['total_wallet_charge'] : 0 ;

		$output = $this->User->cibStatusCheck($transaction_id,$loggedAccountID);
		if($output['status'] == 2)
		{

			$api_utr_no = isset($output['rrno']) ? $output['rrno'] : '';

			$this->db->where('account_id',$account_id);
			$this->db->where('id',$recordID);
			$this->db->update('user_fund_transfer',array('rrn'=>$api_utr_no,'status'=>3,'updated'=>date('Y-m-d H:i:s')));	
			
			$procResponse = $this->db->select('role_id,user_code,call_back_url')->get_where('users',array('id'=>$loggedAccountID))->row_array();
			
			$user_role_id = isset($procResponse['role_id']) ? $procResponse['role_id'] : 0;
			$api_member_code = isset($procResponse['user_code']) ? $procResponse['user_code'] : '';
			
			$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API Member Role ID - '.$user_role_id.']'.PHP_EOL;
	        $this->User->generatePayoutLog($log_msg);

			if($user_role_id == 6)
			{
				$user_call_back_url = $procResponse['call_back_url'];
	            
	            if($user_call_back_url)
	            {
					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
		        	$this->User->generatePayoutLog($log_msg);

	        		$api_post_data = array();
	        		$api_post_data['status'] = 'SUCCESS';
	        		$api_post_data['txnID'] = $transaction_id;
	        		$api_post_data['BankRRN'] = $api_utr_no;
	        		
	        		$header = [
	                    'Content-type: application/json'
	                ];

	        		$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));		
					$output = curl_exec($ch); 
					$error_msg = '';
			        if (curl_errno($ch)) {
			            $error_msg = curl_error($ch);
			        }
					curl_close($ch);
					
					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
		        	$this->User->generatePayoutLog($log_msg);
					
					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
		        	$this->User->generatePayoutLog($log_msg);

		        	$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
		        	$this->User->generatePayoutLog($log_msg);
	            }
				
				
			}

			// distribut referral commision
            $recordList = $this->db->query("SELECT * FROM tbl_referral_commision WHERE from_member_id = '$loggedAccountID' AND account_id = '$account_id' AND service_id = 23 AND start_range <= $amount AND end_range >= $amount")->result_array();
            if($recordList)
            {
            	foreach($recordList as $rList)
            	{
            		$to_member_id = $rList['to_member_id'];
            		$commission = $rList['commission'];
            		$is_flat = $rList['is_flat'];
            		$is_surcharge = $rList['is_surcharge'];

            		$comission = round(($commission/100)*$amount,2);
            		if($is_flat)
            		{
            			$comission = $commission;
            		}

            		$referralData = array(
            			'account_id' => $account_id,
            			'from_member_id' => $loggedAccountID,
            			'to_member_id' => $to_member_id,
            			'txnid' => $transaction_id,
            			'service_id' => 23,
            			'amount' => $amount,
            			'comission' => $comission,
            			'created' => date('Y-m-d H:i:s')
            		);
            		$this->db->insert('member_referral_comission',$referralData);
            	}
            }
			
		}
		elseif($output['status'] == 3)
		{
			$api_message = $output['msg'];
			$this->db->where('account_id',$account_id);
			$this->db->where('id',$recordID);
			$this->db->update('user_fund_transfer',array('status'=>4,'api_response'=>$api_message,'updated'=>date('Y-m-d H:i:s')));

			$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
			$after_wallet_balance = $before_balance + $final_amount;    

	        $wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $loggedAccountID,    
	            'before_balance'      => $before_balance,
	            'amount'              => $final_amount,  
	            'after_balance'       => $after_wallet_balance,      
	            'status'              => 1,
	            'type'                => 1,   
	            'wallet_type'		  => 1,   
	            'created'             => date('Y-m-d H:i:s'),      
	            'description'         => 'Payout #'.$transaction_id.' Amount Refund.'
	        );

	        $this->db->insert('member_wallet',$wallet_data);

	        $procResponse = $this->db->select('role_id,user_code,call_back_url')->get_where('users',array('id'=>$loggedAccountID))->row_array();
				
			$user_role_id = isset($procResponse['role_id']) ? $procResponse['role_id'] : 0;
			$api_member_code = isset($procResponse['user_code']) ? $procResponse['user_code'] : '';
			
			$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API Member Role ID - '.$user_role_id.']'.PHP_EOL;
	        $this->User->generatePayoutLog($log_msg);

			if($user_role_id == 6)
			{
				$user_call_back_url = $procResponse['call_back_url'];
	            
	            if($user_call_back_url)
	            {
					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
		        	$this->User->generatePayoutLog($log_msg);

	        		$api_post_data = array();
	        		$api_post_data['status'] = 'FAILED';
	        		$api_post_data['txnID'] = $transaction_id;
	        		$api_post_data['BankRRN'] = '';
	        		
	        		$header = [
	                    'Content-type: application/json'
	                ];

	        		$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));		
					$output = curl_exec($ch); 
					$error_msg = '';
			        if (curl_errno($ch)) {
			            $error_msg = curl_error($ch);
			        }
					curl_close($ch);
					
					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
		        	$this->User->generatePayoutLog($log_msg);
					
					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
		        	$this->User->generatePayoutLog($log_msg);

		        	$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Check Status API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
		        	$this->User->generatePayoutLog($log_msg);
	            }
				
				
			}
			
		}
		
		echo 1;
	}

	public function getPayoutData($recharge_id = 0)
	{
		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $chk_txn_id = $this->db->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
        if($chk_txn_id)
        {

			// check recharge status
			$get_recharge_data = $this->db->get_where('user_fund_transfer',array('id'=>$recharge_id))->row_array();
			
			$recharge_unique_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
			$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
			
			$response = array(
				'status'=>1,
				'txnid' => $recharge_unique_id,
				'amount' => $amount
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

	public function refundPayout($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/moneyTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(2))->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/moneyTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Refunded/Success.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
		$final_amount = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedAccountID);
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('user_fund_transfer',array('status'=>4,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));

		$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		$after_wallet_balance = $before_balance + $final_amount;    

        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $loggedAccountID,    
            'before_balance'      => $before_balance,
            'amount'              => $final_amount,  
            'after_balance'       => $after_wallet_balance,      
            'status'              => 1,
            'type'                => 1,   
            'wallet_type'		  => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'Payout #'.$transaction_id.' Amount Refund.'
        );

        $this->db->insert('member_wallet',$wallet_data);

        $procResponse = $this->db->select('role_id,user_code,call_back_url')->get_where('users',array('id'=>$loggedAccountID))->row_array();
			
		$user_role_id = isset($procResponse['role_id']) ? $procResponse['role_id'] : 0;
		$api_member_code = isset($procResponse['user_code']) ? $procResponse['user_code'] : '';
		
		$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API Member Role ID - '.$user_role_id.']'.PHP_EOL;
        $this->User->generatePayoutLog($log_msg);

		if($user_role_id == 6)
		{
			$user_call_back_url = $procResponse['call_back_url'];
            
            if($user_call_back_url)
            {
				$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
	        	$this->User->generatePayoutLog($log_msg);

        		$api_post_data = array();
        		$api_post_data['status'] = 'FAILED';
        		$api_post_data['txnID'] = $transaction_id;
        		$api_post_data['BankRRN'] = '';
        		
        		$header = [
                    'Content-type: application/json'
                ];

        		$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));		
				$output = curl_exec($ch); 
				$error_msg = '';
		        if (curl_errno($ch)) {
		            $error_msg = curl_error($ch);
		        }
				curl_close($ch);
				
				$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
	        	$this->User->generatePayoutLog($log_msg);
				
				$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
	        	$this->User->generatePayoutLog($log_msg);

	        	$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
	        	$this->User->generatePayoutLog($log_msg);
            }
			
			
		}


		$this->Az->redirect('admin/report/moneyTransfer', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction refunded successfully.</div>');
	}

	public function successPayout()
	{
		$account_id = $this->User->get_domain_account();
		$post = $this->security->xss_clean($this->input->post());
		$recharge_id = isset($post['recordID']) ? $post['recordID'] : 0 ;
		$bank_rrn = isset($post['bank_rrn']) ? $post['bank_rrn'] : 0 ;
		if(!$bank_rrn)
		{
			$this->Az->redirect('admin/report/moneyTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Please enter Bank RRN.</div>');
		}
		// check member
		$chkMember = $this->db->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/moneyTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(2))->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/moneyTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Refunded/Success.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('user_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
		$surcharge_amount = isset($get_recharge_data['transfer_charge_amount']) ? $get_recharge_data['transfer_charge_amount'] : 0 ;
		$txnType = isset($get_recharge_data['txnType']) ? $get_recharge_data['txnType'] : '' ;

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedAccountID);
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('user_fund_transfer',array('op_txn_id'=>$transaction_id,'rrn'=>$bank_rrn,'status'=>3,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));

		$procResponse = $this->db->select('role_id,user_code,call_back_url')->get_where('users',array('id'=>$loggedAccountID))->row_array();
			
		$user_role_id = isset($procResponse['role_id']) ? $procResponse['role_id'] : 0;
		$api_member_code = isset($procResponse['user_code']) ? $procResponse['user_code'] : '';
		
		$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API Member Role ID - '.$user_role_id.']'.PHP_EOL;
        $this->User->generatePayoutLog($log_msg);

		if($user_role_id == 6)
		{
			$user_call_back_url = $procResponse['call_back_url'];
            
            if($user_call_back_url)
            {
				$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
	        	$this->User->generatePayoutLog($log_msg);

        		$api_post_data = array();
        		$api_post_data['status'] = 'SUCCESS';
        		$api_post_data['txnID'] = $transaction_id;
        		$api_post_data['BankRRN'] = $bank_rrn;
        		
        		$header = [
                    'Content-type: application/json'
                ];

        		$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));		
				$output = curl_exec($ch); 
				$error_msg = '';
		        if (curl_errno($ch)) {
		            $error_msg = curl_error($ch);
		        }
				curl_close($ch);
				
				$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
	        	$this->User->generatePayoutLog($log_msg);
				
				$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
	        	$this->User->generatePayoutLog($log_msg);

	        	$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$transaction_id.' - Payout Callback API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
	        	$this->User->generatePayoutLog($log_msg);
            }
			
			
		}
		

		$this->Az->redirect('admin/report/moneyTransfer', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction successfully Credited.</div>');
	}

	public function moneyTransferInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.name as user_name FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.id = '$id'";
		$detail = $this->db->query($sql)->row_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/moneytransfer-invoice'
        );
        $this->parser->parse('admin/layout/column-2' , $data);
    
	
	}


	public function moneyTransferHistory(){

		//get logged user info
       $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
			
  		$siteUrl = base_url();		
  		
  		

  		$user = $this->db->get_where('users',array('account_id'=>$account_id,'is_active'=>1,'role_id >'=>2))->result_array();

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'user' =>$user,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/money-transfer-history'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getMoneyTransferList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
	   	$toDate = '';
	   	$status = 0;
        $user = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $user = isset($filterData[4]) ? trim($filterData[4]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,c.name as sender_name,c.mobile as sender_mobile FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '%".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$sql.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql.=" OR c.name LIKE '%".$keyword."%'";
				$sql.=" OR c.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.txnType LIKE '%".$keyword."%'";
				$sql.=" OR a.op_txn_id LIKE '%".$keyword."%'";
				$sql.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			

				if($status)
            {
                $sql.=" AND a.status = '$status'";
            }

            if($user != ''){
            
             $sql.=" AND a.user_id = '$user'";	
            
            }


			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();


			$sql_summery = "SELECT SUM(COALESCE(CASE WHEN a.status = 3 THEN a.total_wallet_charge END,0)) totalSuccessAmount,count( case when a.status=3 then 1 else NULL end) totalSuccessRecord,SUM(COALESCE(CASE WHEN a.status = 4 THEN a.total_wallet_charge END,0)) totalFailedAmount,count( case when a.status=4 then 1 else NULL end) totalFailedRecord,SUM(COALESCE(CASE WHEN a.status = 2 THEN a.total_wallet_charge END,0)) totalPendingAmount,count( case when a.status=2 then 1 else NULL end) totalPendingRecord FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id WHERE a.account_id = '$account_id'";
			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql_summery.=" OR b.name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.txnType LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.op_txn_id LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($status)
            {
                $sql_summery.=" AND a.status = '$status'";
            }

            if($user != ''){
            
             $sql_summery.=" AND a.user_id = '$user'";	
            
            }

            $get_success_recharge = $this->db->query($sql_summery)->row_array();
			

			$successAmount = isset($get_success_recharge['totalSuccessAmount']) ? number_format($get_success_recharge['totalSuccessAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalSuccessRecord']) ? $get_success_recharge['totalSuccessRecord'] : 0;
	        $failedAmount = isset($get_success_recharge['totalFailedAmount']) ? number_format($get_success_recharge['totalFailedAmount'],2) : '0.00';
	        $failedRecord = isset($get_success_recharge['totalFailedRecord']) ? $get_success_recharge['totalFailedRecord'] : 0;
	        $pendingAmount = isset($get_success_recharge['totalPendingAmount']) ? number_format($get_success_recharge['totalPendingAmount'],2) : '0.00';
	        $pendingRecord = isset($get_success_recharge['totalPendingRecord']) ? $get_success_recharge['totalPendingRecord'] : 0;


		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['memberID'];
				$nestedData[] = $list['account_holder_name'].'<br />'.$list['mobile'];
				$nestedData[] = $list['account_no'].'<br />'.$list['ifsc'];
				$nestedData[] = 'Tran. Amount - '.$list['transfer_amount'].'<br />Charge - '.$list['transfer_charge_amount'];
				if($list['txnType'] == 'RGS')
				{
					$nestedData[] = 'NEFT';
				}
				elseif($list['txnType'] == 'RTG')
				{
					$nestedData[] = 'RTGS';
				}
				elseif($list['txnType'] == 'IFS')
				{
					$nestedData[] = 'IMPS';
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
				elseif($list['status'] == 4 && $list['force_status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				elseif($list['status'] == 4 && $list['force_status'] == 1) {
					$nestedData[] = '<font color="red">Refund</font>';
				}
				if($list['invoice_no']){
					
				 $nestedData[] = '<a href="'.base_url('admin/report/transferInvoice/'.$list['id'].'').'" target="_blank">'.$list['invoice_no'].'</a>';

				}
				else{

					$nestedData[] = 'Not Available';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));

				if($list['status'] == 2) {
					$nestedData[] = '<a href="'.base_url('admin/report/refundMoneyTransfer').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this transaction?\')" class="btn btn-sm btn-primary">Refund</a> <a href="#" onclick="successMoneyTranfer('.$list['id'].'); return false;" class="btn btn-sm btn-success">Success</a>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = 'Not Allowed';
				}
				elseif($list['status'] == 4) {
					$nestedData[] = 'Not Allowed';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,
					"pendingAmount"  => $pendingAmount,
					"pendingRecord"  => $pendingRecord,					
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}

	public function getMoneyTransferData($recharge_id = 0)
	{
		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $chk_txn_id = $this->db->get_where('user_money_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
        if($chk_txn_id)
        {

			// check recharge status
			$get_recharge_data = $this->db->get_where('user_money_transfer',array('id'=>$recharge_id))->row_array();
			
			$recharge_unique_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
			$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
			
			$response = array(
				'status'=>1,
				'txnid' => $recharge_unique_id,
				'amount' => $amount
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
	public function refundMoneyTransfer($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('user_money_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/moneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(2))->get_where('user_money_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/moneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Refunded/Success.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('user_money_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
		$final_amount = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedAccountID);
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('user_money_transfer',array('status'=>4,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));

		$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		$after_wallet_balance = $before_balance + $final_amount;    

        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $loggedAccountID,    
            'before_balance'      => $before_balance,
            'amount'              => $final_amount,  
            'after_balance'       => $after_wallet_balance,      
            'status'              => 1,
            'type'                => 1,   
            'wallet_type'		  => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'Fund Transfer #'.$transaction_id.' Amount Refund Manually.'
        );

        $this->db->insert('member_wallet',$wallet_data);


		$this->Az->redirect('admin/report/moneyTransferHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction refunded successfully.</div>');
	}

	public function successMoneyTransfer()
	{
		$account_id = $this->User->get_domain_account();
		$post = $this->security->xss_clean($this->input->post());
		$recharge_id = isset($post['recordID']) ? $post['recordID'] : 0 ;
		$bank_rrn = isset($post['bank_rrn']) ? $post['bank_rrn'] : 0 ;
		if(!$bank_rrn)
		{
			$this->Az->redirect('admin/report/moneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Please enter Bank RRN.</div>');
		}
		// check member
		$chkMember = $this->db->get_where('user_money_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/moneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(2))->get_where('user_money_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/moneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Refunded/Success.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('user_money_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
		$surcharge_amount = isset($get_recharge_data['transfer_charge_amount']) ? $get_recharge_data['transfer_charge_amount'] : 0 ;
		$txnType = isset($get_recharge_data['txnType']) ? $get_recharge_data['txnType'] : '' ;

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedAccountID);
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('user_money_transfer',array('op_txn_id'=>$transaction_id,'rrn'=>$bank_rrn,'status'=>3,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));
		
		$this->Az->redirect('admin/report/moneyTransferHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction successfully Credited.</div>');
	}

	public function transferInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);
		$contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
		

		$sql = "SELECT a.*,b.name as member_name,c.name as sender_name FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.account_id = '$account_id' AND a.id = '$id'";
		$detail = $this->db->query($sql)->row_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'contactDetail' => $contactDetail,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/transfer-invoice'
        );
        $this->parser->parse('admin/layout/column-2' , $data);
    
	
	}


	public function rechargeCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getRechargeCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'RECHARGE'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.recharge_display_id FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'RECHARGE'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR a.commision_amount LIKE '%".$keyword."%'";
				$sql.=" OR c.recharge_display_id LIKE '%".$keyword."%' )";
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

	public function bbpsCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/bbps-commission-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getBBPSCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'BBPS'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.recharge_display_id FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'BBPS'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR a.commision_amount LIKE '%".$keyword."%'";
				$sql.=" OR c.recharge_display_id LIKE '%".$keyword."%' )";
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


	public function fundTransferCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/fund-transfer-commission-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getFundTransferCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_fund_transfer as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'PAYOUT'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.transaction_id,c.transfer_amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_fund_transfer as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'PAYOUT'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR c.transfer_amount LIKE '%".$keyword."%'";
				$sql.=" OR c.transaction_id LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = '&#8377; '.$list['transfer_amount'];
				$nestedData[] = '&#8377; '.$list['commision_amount'];
				if($list['is_surcharge'] == 1)
				{
					$nestedData[] = '<font color="red">DR</font>';
				}
				else
				{
					$nestedData[] = '<font color="green">CR</font>';
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

	

	public function liveRecharge(){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();

        $fromDate = date('Y-m-d');
        $toDate = date('Y-m-d');

        $sql = "SELECT a.*, b.user_code as user_code, b.name as name,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id' AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."' ORDER BY a.created DESC";
        $totalRecord = $this->db->query($sql)->num_rows();
        $sql.=" LIMIT 50 ";
        $rechargeList = $this->db->query($sql)->result_array();

        $siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser,
            'rechargeList'=>$rechargeList, 
            'totalRecord' => $totalRecord,
        	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/live-recharge-history'
        );
        $this->parser->parse('admin/layout/column-3' , $data);
    }


    public function getLiveRechargeData(){
		
    	

        $response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);

	 	$fromDate = date('Y-m-d');
        $toDate = date('Y-m-d');

        $sql = "SELECT a.*, b.user_code as user_code, b.name as name,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND a.account_id = '$account_id' AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."' ORDER BY a.created DESC";

        $totalRecord = $this->db->query($sql)->num_rows();

        $sql.=" LIMIT 50 ";

		$rechargeList = $this->db->query($sql)->result_array();

        if($rechargeList){
			$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
				$str.='<thead>';
				$str.='<tr style="background: black;color: white;">';
				$str.='<th>#</th>';
				$str.='<th>RechargeID</th>';
				$str.='<th>MemberID</th>';
				$str.='<th>Name</th>';
				$str.='<th>Mobile</th>';
				$str.='<th>Operator</th>';
				$str.='<th>API ID</th>';
				$str.='<th>Amount</th>';
				$str.='<th>Date Time</th>';
				$str.='</tr></thead>';
				$str.='<tbody>';
				if($rechargeList){
	                $i=$totalRecord;
	                foreach($rechargeList as $key=>$list){

                    	if($list['status'] == 1){
                    	$str.='<tr style="background: #dc8f01;color: white;">';
	                    } elseif($list['status'] == 2){
	                    $str.='<tr style="background: green;color: white;">';
	                    } elseif($list['status'] == 3 || $list['status'] == 4){
	                    $str.='<tr style="background: #ca0303;color: white;">';	
	                    } else{ 
	                    $str.='<tr>';
						} 

	                	$str.='<td>'.$i.'</td>';
	                	$str.='<td>'.$list['recharge_display_id'].'</td>';
	                	$str.='<td>'.$list['user_code'].'</td>';
	                	$str.='<td>'.$list['name'].'</td>';
	                	$str.='<td>'.$list['mobile'].'</td> ';
	                	$str.='<td>'.$list['operator_name'].'</td> ';
	                	$str.='<td>'.$list['api_id'].'</td> ';
	                	$str.='<td>'.number_format($list['amount'],2).'</td> ';
	                	$str.='<td>'.date('d-M-Y h:i:s',strtotime($list['created'])).'</td> ';
	                	$str.='</tr>';
	                	$i--;
	                }
	                $str.='</tbody>';
	            }
	            
	            
				$response = array(
		 				'status' => 1,
		 				'msg' => 'Success',
		 				'str' => $str
		 		);
			}
			else{


				$str = '<table class="table table-bordered table-striped"  width="100%" cellspacing="0">';
				$str.='<thead>';
				$str.='<tr style="background: black;color: white;">';
				$str.='<th>#</th>';
				$str.='<th>RechargeID</th>';
				$str.='<th>MemberID</th>';
				$str.='<th>Name</th>';
				$str.='<th>Mobile</th>';
				$str.='<th>Operator</th>';
				$str.='<th>API ID</th>';
				$str.='<th>Amount</th>';
				$str.='<th>Date Time</th>';
				$str.='</tr></thead>';
				$str.='<tbody>';
				$str.='<td colspan="9" class="text-center">No Recharge Found.</td>';
				$str.='</tbody>';
				$response = array(
		 				'status' => 0,
		 				'msg' => 'Failed',
		 				'str' => $str
		 		);

			}


    	
    	echo json_encode($response);
    }



    public function balanceReport(){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        	
        $user_type = $this->db->where_in('id',array(3,4,5,6))->get('user_roles')->result_array();
        
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
            'user_type'=>$user_type,
        	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/balance-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getBalanceReport()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
        $user_type = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $user_type = isset($filterData[1]) ? trim($filterData[1]) : '';
            
        }
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.title as role FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.title as role,(SELECT ROUND(SUM((CASE WHEN type = 1 THEN amount ELSE CONCAT('-',amount) END)),2) as amount FROM tbl_member_wallet WHERE member_id = a.id and wallet_type = 1) as actualBalance FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( b.title LIKE '%".$keyword."%' ";    
				$sql.=" OR a.user_code LIKE '%".$keyword."%'";
				$sql.=" OR a.name LIKE '%".$keyword."%' )";
			}
			
			if($user_type != ''){
            
             $sql.=" AND a.role_id = '$user_type'";	
            
            }
	

			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();

			
			$sql_summery = "SELECT SUM(wallet_balance) as totalWBal,SUM(actualBalance) as totalABal FROM (SELECT a.wallet_balance,(SELECT ROUND(SUM((CASE WHEN type = 1 THEN amount ELSE CONCAT('-',amount) END)),2) as amount FROM tbl_member_wallet WHERE member_id = a.id and wallet_type = 1) as actualBalance FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.account_id = '$account_id'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.title LIKE '".$keyword."%' ";    
				$sql_summery.=" OR a.user_code LIKE '".$keyword."%'";
				$sql_summery.=" OR a.name LIKE '".$keyword."%' )";
			}
			
			if($user_type != ''){
            
             $sql_summery.=" AND a.role_id = '$user_type'";	
            
            }

            $sql_summery.=" ) as x";	

            $get_wallet_summery = $this->db->query($sql_summery)->row_array();

            $total_wallet_balance = isset($get_wallet_summery['totalWBal']) ? $get_wallet_summery['totalWBal'] : '0.00';
            $total_actual_balance = isset($get_wallet_summery['totalABal']) ? $get_wallet_summery['totalABal'] : '0.00';
			

		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['role'];
				$nestedData[] = $list['name'];
				$nestedData[] = number_format($list['actualBalance'],2).' /-';
				
				
				
				$data[] = $nestedData;

				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					"total_wallet_balance" => number_format($total_wallet_balance,2),
					"total_actual_balance" => number_format($total_actual_balance,2),
					
					);

		echo json_encode($json_data);  // send data as json format
	}

	public function upiBalanceReport(){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        	
        $user_type = $this->db->where_in('id',array(3,4,5,6))->get('user_roles')->result_array();
        
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
            'user_type'=>$user_type,
        	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/upi-balance-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getUpiBalanceReport()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
        $user_type = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $user_type = isset($filterData[1]) ? trim($filterData[1]) : '';
            
        }
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.title as role FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.title as role,(SELECT ROUND(SUM((CASE WHEN type = 1 THEN amount ELSE CONCAT('-',amount) END)),2) as amount FROM tbl_member_upi_wallet WHERE member_id = a.id and wallet_type = 1) as actualBalance FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( b.title LIKE '%".$keyword."%' ";    
				$sql.=" OR a.user_code LIKE '%".$keyword."%'";
				$sql.=" OR a.name LIKE '%".$keyword."%' )";
			}
			
			if($user_type != ''){
            
             $sql.=" AND a.role_id = '$user_type'";	
            
            }
	

			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();

			
			$sql_summery = "SELECT SUM(wallet_balance) as totalWBal,SUM(actualBalance) as totalABal FROM (SELECT a.wallet_balance,(SELECT ROUND(SUM((CASE WHEN type = 1 THEN amount ELSE CONCAT('-',amount) END)),2) as amount FROM tbl_member_upi_wallet WHERE member_id = a.id and wallet_type = 1) as actualBalance FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.account_id = '$account_id'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.title LIKE '".$keyword."%' ";    
				$sql_summery.=" OR a.user_code LIKE '".$keyword."%'";
				$sql_summery.=" OR a.name LIKE '".$keyword."%' )";
			}
			
			if($user_type != ''){
            
             $sql_summery.=" AND a.role_id = '$user_type'";	
            
            }

            $sql_summery.=" ) as x";	

            $get_wallet_summery = $this->db->query($sql_summery)->row_array();

            $total_wallet_balance = isset($get_wallet_summery['totalWBal']) ? $get_wallet_summery['totalWBal'] : '0.00';
            $total_actual_balance = isset($get_wallet_summery['totalABal']) ? $get_wallet_summery['totalABal'] : '0.00';
			

		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['role'];
				$nestedData[] = $list['name'];
				$nestedData[] = number_format($list['actualBalance'],2).' /-';
				
				
				
				$data[] = $nestedData;

				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					"total_wallet_balance" => number_format($total_wallet_balance,2),
					"total_actual_balance" => number_format($total_actual_balance,2),
					
					);

		echo json_encode($json_data);  // send data as json format
	}

	public function bbpsHistory($status = 0){

		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        	
        $user_type = $this->db->where_in('id',array(3,4,5,6))->get('user_roles')->result_array();
        
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'recharge' => $recharge,
            'loggedUser'=>$loggedUser, 
            'status' => $status,
            'user_type'=>$user_type,
        	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/bbpsHistory'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getBbpsHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
        $fromDate = '';
        $toDate = '';
        $status = 0;
        $user_type = '';
        $operator = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $user_type = isset($filterData[4]) ? trim($filterData[4]) : '';
            $operator = isset($filterData[5]) ? trim($filterData[5]) : '';
            
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
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_mobikwik_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name,b.user_code,c.title as service_name FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_mobikwik_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.operator_code LIKE '%".$keyword."%'";
				$sql.=" OR a.account_number LIKE '%".$keyword."%'";
				$sql.=" OR a.txid LIKE '%".$keyword."%'";
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.recharge_display_id LIKE '%".$keyword."%'";
				$sql.=" OR c.title LIKE '%".$keyword."%' )";
			}
			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			if($status)
            {
                $sql.=" AND a.status = '$status'";
            }

            
			if($user_type != ''){
            
             $sql.=" AND b.role_id = '$user_type'";	
            
            }
	

			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		

			$get_filter_data = $this->db->query($sql)->result_array();

            $sql_success_summery = "SELECT a.*,SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_mobikwik_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND a.status = 2";

			if($keyword != '') {   
				$sql_success_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_success_summery.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.operator_code LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.account_number LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.txid LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR a.recharge_display_id LIKE '%".$keyword."%'";
				$sql_success_summery.=" OR c.title LIKE '%".$keyword."%' )";
			}
			if($fromDate && $toDate)
            {
                $sql_success_summery.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
            
			if($user_type != ''){
            
             $sql_success_summery.=" AND b.role_id = '$user_type'";	
            
            }
			
			$get_success_recharge = $this->db->query($sql_success_summery)->row_array();
			
			$successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;


	        $sql_pending_summery = "SELECT a.*,SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_mobikwik_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND a.status = 1";

			if($keyword != '') {   
				$sql_pending_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_pending_summery.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.operator_code LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.account_number LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.txid LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR a.recharge_display_id LIKE '%".$keyword."%'";
				$sql_pending_summery.=" OR c.title LIKE '%".$keyword."%' )";
			}
			if($fromDate && $toDate)
            {
                $sql_pending_summery.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
            
			if($user_type != ''){
            
             $sql_pending_summery.=" AND b.role_id = '$user_type'";	
            
            }
	    	
			$get_pending_recharge = $this->db->query($sql_pending_summery)->row_array();
			
			$pendingAmount = isset($get_pending_recharge['totalAmount']) ? number_format($get_pending_recharge['totalAmount'],2) : '0.00';
	        $pendingRecord = isset($get_pending_recharge['totalRecord']) ? $get_pending_recharge['totalRecord'] : 0;
	    	

	        $sql_failed_summery = "SELECT a.*,SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_mobikwik_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.account_id = '$account_id' AND a.status = 3";

			if($keyword != '') {   
				$sql_failed_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_failed_summery.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.operator_code LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.account_number LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.txid LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR a.recharge_display_id LIKE '%".$keyword."%'";
				$sql_failed_summery.=" OR c.title LIKE '%".$keyword."%' )";
			}
			if($fromDate && $toDate)
            {
                $sql_failed_summery.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
            
			if($user_type != ''){
            
             $sql_failed_summery.=" AND b.role_id = '$user_type'";	
            
            }
	        $get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();
			
			
	        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
	        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;
	    	
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['recharge_display_id']."</a>";
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a><br />".$list['name'];
				$nestedData[] = $list['service_name'];
				if($list['service_name'] == 'Credit Card')
				{
					$nestedData[] = 'Credit Card';
				}
				else{
					$nestedData[] = $list['operator_code'];	
				}
				
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['account_number'];
				$nestedData[] = $list['amount'].' /-';
				$balance_str = '';
				if($list['before_balance'])
				{
					$balance_str.='OB - '.$list['before_balance'].' /-<br />';
				}
				else
				{
					$balance_str.='OB - 0 /-<br />';
				}
				if($list['after_balance'])
				{
					$balance_str.='CB - '.$list['after_balance'].' /-<br />';
				}
				else
				{
					$balance_str.='CB - 0 /-<br />';
				}
				$nestedData[] = $balance_str;
				$nestedData[] = $list['txid'];
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				$nestedData[] = "<a href=".base_url('admin/report/bbpsLiveInvoice/').$list['recharge_display_id']." style='text-decoration:none;' target='_blank'>Invoice</a>";	
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
					if($is_cogent_instantpay_api)
					{
						$nestedData[] = 'Not Allowed';
					}
					else
					{
						$nestedData[] = '<a href="'.base_url('admin/report/refundBbps').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this recharge?\')" class="btn btn-sm btn-primary">Refund</a> <a href="'.base_url('admin/report/successBbps').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to success this recharge?\')" class="btn btn-sm btn-primary">Success</a>';
					}
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
					if($is_cogent_instantpay_api)
					{
						$nestedData[] = 'Not Allowed';
					}
					else
					{
						if($list['force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						elseif($list['force_status'] == 2)
						{
							$nestedData[] = '<font color="green">Success</font>';
						}
						else
						{
							$nestedData[] = '<a href="'.base_url('admin/report/refundBbps').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this recharge?\')" class="btn btn-sm btn-primary">Refund</a>';
						}
					}
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
					if($is_cogent_instantpay_api)
					{
						$nestedData[] = 'Not Allowed';
					}
					else
					{
						if($list['force_status'] == 1)
						{
							$nestedData[] = '<font color="red">Refund</font>';
						}
						elseif($list['force_status'] == 2)
						{
							$nestedData[] = '<font color="green">Success</font>';
						}
						else
						{
							$nestedData[] = 'Not Allowed';
						}
					}
				}
				elseif($list['status'] == 4) {
					$nestedData[] = '<font color="red">Refund</font>';
					$nestedData[] = 'Not Allowed';
					
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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,
					"pendingAmount" => $pendingAmount,
					"pendingRecord" => $pendingRecord,
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,

					);

		echo json_encode($json_data);  // send data as json format
	}

	public function bbpsLiveInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.name,b.user_code,c.title as service_name FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.recharge_display_id = '$id' AND a.account_id = '$account_id'";

		$detail = $this->db->query($sql)->row_array();

		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'operator' => $operator,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/bbps-live-invoice'
        );
        $this->parser->parse('admin/layout/column-2' , $data);
    
	
	}

	public function refundBbps($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/bbpsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(1,2))->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/bbpsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Bill Payment Already Refunded.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;

		// update status
		$this->db->where('id',$recharge_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('bbps_history',array('status'=>4,'force_status'=>1));

		
		$get_before_balance = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();

		

    	$member_code = $get_before_balance['user_code'];    
    	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
    	$after_balance = $before_balance + $amount;

    	$wallet_data = array(
    		'account_id'          => $account_id,
			'member_id'           => $member_id,    
			'before_balance'      => $before_balance,
			'amount'              => $amount,  
			'after_balance'       => $after_balance,      
			'status'              => 1,
			'type'                => 1,    
			'wallet_type'         => 1,  
			'created'             => date('Y-m-d H:i:s'),      
			'credited_by'         => 1,
			'description'         => 'BBPS Refund #'.$recharge_unique_id.' Credited'
        );

        $this->db->insert('member_wallet',$wallet_data);

		$this->Az->redirect('admin/report/bbpsHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Bill Payment refunded successfully.</div>');
	}

	public function successBbps($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/bbpsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/bbpsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Bill Payment Status Already Updated.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;

		// update status
		$this->db->where('id',$recharge_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('bbps_history',array('status'=>2,'force_status'=>2));

		
		$this->Az->redirect('admin/report/bbpsHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Bill Payment got success.</div>');
	}

	public function aepsKyc(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getAepsKycList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code FROM tbl_fingpay_aeps_member_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,c.state as state_name,d.city_name FROM tbl_fingpay_aeps_member_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_aeps_state as c ON c.id = a.state_id LEFT JOIN tbl_city as d ON d.city_id = a.city_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.first_name LIKE '%".$keyword."%'";
				$sql.=" OR a.last_name LIKE '%".$keyword."%'";
				$sql.=" OR a.aadhar_no LIKE '%".$keyword."%'";
				$sql.=" OR a.pancard_no LIKE '%".$keyword."%'";
				$sql.=" OR a.pin_code LIKE '%".$keyword."%'";
				$sql.=" OR a.shop_name LIKE '%".$keyword."%' )";
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
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />First Name - '.$list['first_name'].'<br />Last Name - '.$list['last_name'];
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
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getAepsHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.aadhar_no LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.service LIKE '%".$keyword."%'";
				$sql.=" OR a.message LIKE '%".$keyword."%'";
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
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />Name - '.$list['user_name'];
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

				if($list['service'] == 'balwithdraw' && ($list['status'] == 1 || $list['status'] == 3) && date('Y-m-d',strtotime($list['created'])) == date('Y-m-d')) {
					$nestedData[] = '<a href="'.base_url('admin/report/checkAepsStatus').'/'.$list['id'].'" class="btn btn-sm btn-primary">Check Status</a>';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
				}

				$nestedData[] = "<a href=".base_url('admin/report/aepsInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";

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

	public function checkAepsStatus($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('member_aeps_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/aepsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(1,3))->get_where('member_aeps_transaction',array('id'=>$recharge_id,'account_id'=>$account_id,'DATE(created)'=>date('Y-m-d')))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/aepsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Status Already Updated.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('member_aeps_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$service_type = isset($get_recharge_data['service']) ? $get_recharge_data['service'] : '' ;
		$txnID = isset($get_recharge_data['txnID']) ? $get_recharge_data['txnID'] : '' ;
		$aadharNumber = isset($get_recharge_data['aadhar_no']) ? $get_recharge_data['aadhar_no'] : '' ;
		$iin = isset($get_recharge_data['iinno']) ? $get_recharge_data['iinno'] : '' ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
		$recordID = $recharge_id;

		$response = $this->Aeps_model->txnStatusCheckAuth($txnID,$recharge_id,$member_id,$aadharNumber,$iin,$amount,$service_type);

		if($response == true)
		{
			$this->Az->redirect('admin/report/aepsHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction Status Updated successfully.</div>');
		}
		else
		{
			$this->Az->redirect('admin/report/aepsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Your transaction got failed.</div>');
		}
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

	public function aepsInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


		$detail = $this->db->query($sql)->row_array();
		

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/aeps-invoice'
        );
        $this->parser->parse('admin/layout/column-2' , $data);
    
	
	}



	public function walletDeductReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
            'content_block' => 'report/wallet-deduct-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getWalletDeductList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$wallet_type = '';
	   	$user_type = '';
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$wallet_type = isset($filterData[0]) ? trim($filterData[0]) : '';
			$user_type = isset($filterData[1]) ? trim($filterData[1]) : '';
			$date = isset($filterData[2]) ? trim($filterData[2]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.title FROM tbl_wallet_deduction_history as a LEFT JOIN tbl_user_roles as b ON a.user_type = b.id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.title FROM tbl_wallet_deduction_history as a LEFT JOIN tbl_user_roles as b ON a.user_type = b.id where a.account_id = '$account_id'";
			
			if($wallet_type != '') {   
				
				$sql.=" AND ( a.wallet_type = '".$wallet_type."' )";    
			}

			if($user_type != '') {   
				
				$sql.=" AND ( a.user_type = '".$user_type."' )";    
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
				
				if($list['user_type'] == 0){

					$nestedData[] = 'All';
				}
				else{

					$nestedData[] = $list['title'];
				}

				
				if($list['wallet_type'] == 1)
				{
					$nestedData[] = 'R-Wallet';
				}
				elseif($list['wallet_type'] == 2)
				{
					$nestedData[] = 'E-Wallet';
				}
				else
				{
					$nestedData[] = 'Not Found';
				}
				
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = $list['description'];
				$nestedData[] = $list['total_user'];
				$nestedData[] = $list['total_deduct_user'];
				$nestedData[] = '&#8377; '.$list['total_deduct_amount'];
				
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

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(5, $activeService)){
			$this->Az->redirect('admin/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'report/upi-collection-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getUpiCollectionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as type,d.title as status_title FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON c.id = a.type_id  LEFT JOIN tbl_upi_transaction_status as d ON d.id = a.status where a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as type,d.title as status_title FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON c.id = a.type_id  LEFT JOIN tbl_upi_transaction_status as d ON d.id = a.status where a.id > 0 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   

				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR a.bank_rrno LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.vpa_id LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['name'].'<br>('.$list['user_code'].')'."</a>";
				$nestedData[] = $list['txnid'];
				$nestedData[] = isset($list['bank_rrno']) ? $list['bank_rrno'] : 'Not Available';
				$nestedData[] = $list['amount'].' /-';
				$nestedData[] = $list['charge_amount'].' /-';
				$nestedData[] = $list['credit_amount'].' /-';
				$nestedData[] = !empty($list['vpa_id']) ? $list['vpa_id'] : 'Not Available';
				$nestedData[] = !empty($list['description']) ? $list['description'] : 'Not Available';
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">'.$list['status_title'].'</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">'.$list['status_title'].'</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">'.$list['status_title'].'</font>';
				}

				if($list['status'] == 1) {
					$nestedData[] = '<a href="'.base_url('admin/report/checkUpiColStatus').'/'.$list['id'].'" class="btn btn-sm btn-primary">Check Status</a>';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
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

	public function checkUpiColStatus($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('upi_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/upiCollectionReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(1))->get_where('upi_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/upiCollectionReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Status Already Updated.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('upi_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['txnid']) ? $get_recharge_data['txnid'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
		$recordID = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
		
		$response = $this->Dmt_model->upiTxnStatusCheckAuth($transaction_id,$member_id,$recordID,$amount);

		if($response['status'] == 1)
		{
			$this->Az->redirect('admin/report/upiCollectionReport', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction Status Updated successfully.</div>');
		}
		else
		{
			$this->Az->redirect('admin/report/upiCollectionReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction failed due to '.$response['message'].'</div>');
		}
	}

	public function upiTxnInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


		$detail = $this->db->query($sql)->row_array();
		

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/upi-txn-invoice'
        );
        $this->parser->parse('admin/layout/column-2' , $data);
    
	
	}


	//get  bebeficiary account change request


	public function changeAccountList(){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        
       
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
           'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/account-change-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getAccountList()
    {   
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
            $sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_payout_user_request  as a INNER JOIN tbl_users as b ON b.id = a.user_id  WHERE a.account_id = '$account_id' ";


            
            $totalData = $this->db->query($sql)->num_rows();
            $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
        
        
            $sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_payout_user_request as a INNER JOIN tbl_users as b ON b.id = a.user_id  WHERE a.account_id = '$account_id'";
            
            if($keyword != '') {   
                $sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
                $sql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
                $sql.=" OR a.bank_name LIKE '%".$keyword."%'";
                $sql.=" OR a.account_no LIKE '%".$keyword."%'";
                $sql.=" OR a.ifsc LIKE '%".$keyword."%'";
                $sql.=" OR b.name LIKE '%".$keyword."%' )";
            }

            if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

           
            
            $order_type = $requestData['order'][0]['dir'];
            //if($requestData['draw'] == 1)
              //$order_type = 'DESC';
            
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
                $nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>".'<br>'.$list['name'];
                $nestedData[] = $list['account_holder_name'];
                $nestedData[] = $list['bank_name'];
                 $nestedData[] = $list['account_no'];
                  $nestedData[] = $list['ifsc'];
              
                
                if($list['status'] == 1) {
                    $nestedData[] = '<font color="orange">Pending</font>';
                }
                elseif($list['status'] == 2) {
                    $nestedData[] = '<font color="green">Approved</font>';
                }
                elseif($list['status'] == 3) {
                    $nestedData[] = '<font color="red">Rejected</font>';
                }
                
                $nestedData[] = date('d-M-Y',strtotime($list['created']));
                
                if($list['status'] == 1){

                    $nestedData[] = '<a href="'.base_url('admin/report/approveRequest/'.$list['user_id']).'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to approve request?\')" title="Approve Request" class="btn btn-success btn-sm"><i class="fa fa-check" aria-hidden="true"></i></a>
                    <a href="'.base_url('admin/report/rejectRequest/'.$list['user_id']).'/'.$list['id'].'" title="Reject Request" onclick="return confirm(\'Are you sure you want to reject request?\')" class="btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }   
                else{

                    $nestedData[]='Not Allowed';
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


    public function nsdlList(){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        
       
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
           'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/nsdl-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getNsdlList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_nsdl_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.type = 'PAN'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_nsdl_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.type = 'PAN'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%' ";
				$sql.=" OR a.type LIKE '%".$keyword."%' ";
				$sql.=" OR a.order_id LIKE '%".$keyword."%' ";
				$sql.=" OR a.psacode LIKE '%".$keyword."%' ";
				$sql.=" OR a.pan_name LIKE '%".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '%".$keyword."%' ";
				$sql.=" OR a.email LIKE '%".$keyword."%' ";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//$order_type = 'DESC';
			
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
				$nestedData[] = $list['user_code']."<br />".$list['name'];
				$nestedData[] = $list['type'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['order_id'];
				$nestedData[] = $list['psacode'];
				$nestedData[] = $list['pan_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['email'];
				$nestedData[] = $list['charge_amount'].' /-';
				$nestedData[] = $list['admin_charge'].' /-';
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				else
				{
					$nestedData[] = 'Proceed';
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


	public function bomList(){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        
       
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
           'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/bom-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getBomList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_nsdl_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.type = 'BOM'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_nsdl_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.type = 'BOM'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%' ";
				$sql.=" OR a.type LIKE '%".$keyword."%' ";
				$sql.=" OR a.order_id LIKE '%".$keyword."%' ";
				$sql.=" OR a.psacode LIKE '%".$keyword."%' ";
				$sql.=" OR a.pan_name LIKE '%".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '%".$keyword."%' ";
				$sql.=" OR a.email LIKE '%".$keyword."%' ";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//$order_type = 'DESC';
			
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
				$nestedData[] = $list['user_code']."<br />".$list['name'];
				$nestedData[] = $list['type'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['order_id'];
				$nestedData[] = $list['psacode'];
				$nestedData[] = $list['pan_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['email'];
				$nestedData[] = $list['charge_amount'].' /-';
				$nestedData[] = $list['admin_charge'].' /-';
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				else
				{
					$nestedData[] = 'Proceed';
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



     public function approveRequest($user_id = 0, $id=0){

        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $chk_request = $this->db->get_where('tbl_payout_user_request',array('account_id'=>$account_id,'user_id'=>$user_id,'id'=>$id))->row_array();


        

        if(!$chk_request){

            $this->Az->redirect('admin/report/changeAccountList', 'system_message_error',lang('MEMBER_ERROR'));

        }

       
        	  $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$user_id);
              $this->db->where('id',$id);
            $this->db->update('tbl_payout_user_request',array('status'=>2));
       
        	  $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$user_id);
            
            $this->db->update('payout_user_benificary',array('account_holder_name'=>$chk_request['account_holder_name'],'bank_name'=>$chk_request['bank_name'],'account_no'=>$chk_request['account_no'],'ifsc'=>$chk_request['ifsc']));

            $this->Az->redirect('admin/report/changeAccountList', 'system_message_error',lang('REQUEST_APPROVE_SUCCESS'));

        }



         public function rejectRequest($user_id = 0 , $id = 0){

        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $chk_request = $this->db->get_where('tbl_payout_user_request',array('account_id'=>$account_id,'user_id'=>$user_id,'id'=>$id))->row_array();
        

        if(!$chk_request){

            $this->Az->redirect('admin/report/changeAccountList', 'system_message_error',lang('MEMBER_ERROR'));

        }
       
       

             $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$user_id);
            $this->db->where('id',$id);
            $this->db->update('payout_user_request',array('status'=>3));

            $this->Az->redirect('admin/report/changeAccountList', 'system_message_error',lang('REQUEST_REJECT_SUCCESS'));

        }

    public function currentAccountReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(10, $activeService)){
			$this->Az->redirect('admin/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'report/current-account-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getCurrentAccountList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_current_account_list as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_current_account_list as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.first_name LIKE '%".$keyword."%' ";    
				$sql.=" OR a.last_name LIKE '%".$keyword."%' ";
				$sql.=" OR a.email LIKE '%".$keyword."%' ";
				$sql.=" OR a.pincode LIKE '%".$keyword."%' ";
				$sql.=" OR a.application_no LIKE '%".$keyword."%' ";
				$sql.=" OR a.tracker_id LIKE '%".$keyword."%' ";
				$sql.=" OR b.user_code LIKE '%".$keyword."%' ";
				$sql.=" OR b.name LIKE '%".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['user_code'].'<br />'.$list['name'];
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

	public function cashDepositeReport(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/cash-deposite-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getCashDepositeList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_cash_deposite_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.status > 1";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_cash_deposite_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.status > 1";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR b.name LIKE '".$keyword."%'";
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.remark LIKE '%".$keyword."%'";
				$sql.=" OR a.bank_rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.txnid LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['user_code'].'<br />'.$list['name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['account_no'];
				$nestedData[] = $list['amount'].' /-';
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['bank_rrn'];
				$nestedData[] = $list['remark'];

				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4) {
					$nestedData[] = '<font color="red">Failed</font>';
				}

				$nestedData[] = "<a href=".base_url('admin/report/cashDepositeInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";

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

	public function cashDepositeInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code,b.name FROM tbl_cash_deposite_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


		$detail = $this->db->query($sql)->row_array();
		

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/cash-deposite-invoice'
        );
        $this->parser->parse('admin/layout/column-2' , $data);
    
	
	}

	public function moneyTransferCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/money-transfer-commission-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getMoneyTransferCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '%".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = '&#8377; '.$list['transfer_amount'];
				$nestedData[] = '&#8377; '.$list['transfer_charge_amount'];
				$nestedData[] = '&#8377; '.$list['admin_charge_amount'];
				$nestedData[] = '<font color="red">DR</font>';
				
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

	public function openPayoutCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/open-payout-commission-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getOpenPayoutCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '%".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = '&#8377; '.$list['transfer_amount'];
				$nestedData[] = '&#8377; '.$list['transfer_charge_amount'];
				$nestedData[] = '<font color="red">DR</font>';
				
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

	public function aepsCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/aeps-commission-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getAepsCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_member_aeps_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'AEPS'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnID,c.amount,c.service FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_member_aeps_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'AEPS'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR c.service LIKE '%".$keyword."%'";
				$sql.=" OR c.amount LIKE '%".$keyword."%'";
				$sql.=" OR c.txnID LIKE '%".$keyword."%' )";
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
				if($list['service'] == 'balinfo')
				{
					$nestedData[] = 'Balance Inquiry';
				}
				elseif($list['service'] == 'ministatement')
				{
					$nestedData[] = 'Mini Statement';
				}
				elseif($list['service'] == 'balwithdraw')
				{
					$nestedData[] = 'Withdrawal';
				}
				elseif($list['service'] == 'aadharpay')
				{
					$nestedData[] = 'Aadhar Pay';
				}
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = '&#8377; '.$list['commision_amount'];
				if($list['is_surcharge'] == 1)
				{
					$nestedData[] = '<font color="red">DR</font>';
				}
				else
				{
					$nestedData[] = '<font color="green">CR</font>';
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

	public function myAepsCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getMyAepsCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_member_aeps_comm as a INNER JOIN tbl_users as b ON b.id = a.created_by where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code FROM tbl_member_aeps_comm as a INNER JOIN tbl_users as b ON b.id = a.created_by where a.account_id = '$account_id' AND a.type < 6";
			
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
				elseif($list['type'] == 5)
				{
					$nestedData[] = 'MATM';
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

	public function cashDepositeCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/cash-deposite-commission-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getCashDepositeCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_cash_deposite_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'CD'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_cash_deposite_history as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'CD'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR c.txnid LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['txnid'];
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = '&#8377; '.$list['commision_amount'];
				if($list['is_surcharge'] == 1)
				{
					$nestedData[] = '<font color="red">DR</font>';
				}
				else
				{
					$nestedData[] = '<font color="green">CR</font>';
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

	public function upiCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/upi-commission-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getUpiCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPI'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPI'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR c.amount LIKE '%".$keyword."%'";
				$sql.=" OR c.txnid LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['txnid'];
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = '&#8377; '.$list['commision_amount'];
				if($list['is_surcharge'] == 1)
				{
					$nestedData[] = '<font color="red">DR</font>';
				}
				else
				{
					$nestedData[] = '<font color="green">CR</font>';
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

	public function upiCashReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(7, $activeService)){
			$this->Az->redirect('admin/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'report/upi-cash-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getUpiCashList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.id',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as type,d.title as status_title FROM tbl_upi_cash_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON c.id = a.type_id  LEFT JOIN tbl_upi_transaction_status as d ON d.id = a.status where a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as type,d.title as status_title FROM tbl_upi_cash_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON c.id = a.type_id  LEFT JOIN tbl_upi_transaction_status as d ON d.id = a.status where a.id > 0 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   

				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR a.vpa_id LIKE '%".$keyword."%'";
				$sql.=" OR c.title LIKE '%".$keyword."%'";
				$sql.=" OR a.bank_rrno LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.description LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['name'].'<br>('.$list['user_code'].')'."</a>";
				$nestedData[] = $list['type'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = isset($list['bank_rrno']) ? $list['bank_rrno'] : 'Not Available';
				$nestedData[] = $list['amount'].' /-';
				$nestedData[] = !empty($list['vpa_id']) ? $list['vpa_id'] : 'Not Available';
				$nestedData[] = !empty($list['description']) ? $list['description'] : 'Not Available';
				$nestedData[] = "<a href=".base_url('admin/report/upiCashTxnInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">'.$list['status_title'].'</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">'.$list['status_title'].'</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">'.$list['status_title'].'</font>';
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

	public function upiCashTxnInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_cash_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


		$detail = $this->db->query($sql)->row_array();
		

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/upi-cash-txn-invoice'
        );
        $this->parser->parse('admin/layout/column-2' , $data);
    
	
	}	

	public function upiCashCommision(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/upi-cash-commission-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getUpiCashCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPICASH'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.account_id = '$account_id' AND a.type = 'UPICASH'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR c.amount LIKE '%".$keyword."%'";
				$sql.=" OR c.txnid LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['txnid'];
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = '&#8377; '.$list['commision_amount'];
				if($list['is_surcharge'] == 1)
				{
					$nestedData[] = '<font color="red">DR</font>';
				}
				else
				{
					$nestedData[] = '<font color="green">CR</font>';
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

	public function topupHistory(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
            'content_block' => 'report/topupHistory'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getTopupHistory()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_gateway_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_gateway_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.request_id LIKE '%".$keyword."%' ";
				$sql.=" OR a.gateway_txn_id LIKE '%".$keyword."%' ";
				$sql.=" OR a.request_amount LIKE '%".$keyword."%' ";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['request_id'];
				$nestedData[] = $list['gateway_txn_id'];
				$nestedData[] = '&#8377; '.$list['request_amount'];
				$nestedData[] = '&#8377; '.$list['charge_amount'];
				$nestedData[] = '&#8377; '.$list['wallet_settlement_amount'];
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Not Confirm</font>';
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

	public function dmtHistory(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/dmt-history'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getDmtHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_dmt_activation as c ON c.id = a.from_sender_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,c.name as sender_name,c.mobile as sender_mobile FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_dmt_activation as c ON c.id = a.from_sender_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '%".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$sql.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql.=" OR c.name LIKE '%".$keyword."%'";
				$sql.=" OR c.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.ifsc LIKE '%".$keyword."%'";
				$sql.=" OR a.op_txn_id LIKE '%".$keyword."%'";
				$sql.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['sender_name'].'<br />'.$list['sender_mobile'];
				$nestedData[] = $list['account_holder_name'].'<br />'.$list['mobile'];
				$nestedData[] = $list['account_no'].'<br />'.$list['ifsc'];
				$nestedData[] = 'Tran. Amount - '.$list['transfer_amount'].'<br />Charge - '.$list['transfer_charge_amount'];
				$nestedData[] = 'Tran. Amount - '.$list['admin_transfer_amount'].'<br />Charge - '.$list['admin_charge_amount'];
				
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

				if($list['status'] == 2) {
					$nestedData[] = '<a href="'.base_url('admin/report/checkDmtStatus').'/'.$list['id'].'" class="btn btn-sm btn-primary">Check Status</a>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = 'Not Allowed';
				}
				elseif($list['status'] == 4) {
					$nestedData[] = 'Not Allowed';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
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

	public function checkDmtStatus($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('user_dmt_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/dmtHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(2))->get_where('user_dmt_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/dmtHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Status Already Updated.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('user_dmt_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
		$total_wallet_charge = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
		$admin_total_wallet_charge = isset($get_recharge_data['admin_total_wallet_charge']) ? $get_recharge_data['admin_total_wallet_charge'] : 0 ;

		$response = $this->Dmt_model->txnStatusCheckAuth($transaction_id,$member_id,$total_wallet_charge,$admin_total_wallet_charge);

		$this->Az->redirect('admin/report/dmtHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction Status Updated successfully.</div>');
	}

	public function utiPancardReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(9, $activeService)){
			$this->Az->redirect('admin/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'report/uti-pancard-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getUtiPancardList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.id',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_uti_pancard_coupon as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_uti_pancard_coupon as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.id > 0 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   

				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR a.psa_login_id LIKE '%".$keyword."%'";
				$sql.=" OR a.coupon LIKE '%".$keyword."%'";
				$sql.=" OR a.quantity LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['name'].'<br>('.$list['user_code'].')'."</a>";
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['psa_login_id'];
				$nestedData[] = $list['coupon'];
				$nestedData[] = $list['quantity'];
				$nestedData[] = '&#8377; '.$list['charge_amount'];
				$nestedData[] = '&#8377; '.$list['total_wallet_charge'];
				
				
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

	public function moveMemberReport(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/move-member-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getMoveMemberList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            
        }
		
		$columns = array( 
		// datatable column index  => database column name
			4 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_move_member_history as a INNER JOIN tbl_users as b ON b.id = a.move_member_id INNER JOIN tbl_users as c ON c.id = a.last_sponser_id INNER JOIN tbl_users as d ON d.id = a.new_sponser_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code,b.name,c.user_code as last_sponser_code,c.name as last_sponser_name,d.user_code as new_sponser_code,d.name as new_sponser_name FROM tbl_move_member_history as a INNER JOIN tbl_users as b ON b.id = a.move_member_id INNER JOIN tbl_users as c ON c.id = a.last_sponser_id INNER JOIN tbl_users as d ON d.id = a.new_sponser_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '".$keyword."%'";
				$sql.=" OR d.user_code LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['user_code'].'<br />'.$list['name'];
				$nestedData[] = $list['last_sponser_code'].'<br />'.$list['last_sponser_name'];
				$nestedData[] = $list['new_sponser_code'].'<br />'.$list['new_sponser_name'];
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

	public function matmHistory(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
            'content_block' => 'report/matm-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getMatmHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_matm_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_matm_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.ref_no LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.txn_type LIKE '%".$keyword."%'";
				$sql.=" OR a.member_code LIKE '%".$keyword."%'";
				$sql.=" OR a.mpos_number LIKE '%".$keyword."%'";
				$sql.=" OR a.bank_rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.card_number LIKE '%".$keyword."%'";
				$sql.=" OR a.txn_id LIKE '%".$keyword."%' )";
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
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />Name - '.$list['user_name'];
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = $list['txn_id'];
				$nestedData[] = $list['txn_type'];
				$nestedData[] = $list['bank_rrn'];
				$nestedData[] = $list['mpos_number'];
				$nestedData[] = $list['card_number'].'<br />'.$list['name'].'<br />'.$list['mobile'];
	
				
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
					$nestedData[] = '<font color="blue">Hold</font>';
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

	public function axisAccountReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(10, $activeService)){
			$this->Az->redirect('admin/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'report/axis-account-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getAxisAccountList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_axis_account_api_response as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_axis_account_api_response as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.reqid LIKE '%".$keyword."%' ";
				$sql.=" OR b.name LIKE '%".$keyword."%' ";
				$sql.=" OR b.mobile LIKE '%".$keyword."%' )";
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
				
				
				$decodeResponse = json_decode($list['api_response'],true);
				$webUrl = isset($decodeResponse['data']) ? $decodeResponse['data'] : '';
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['user_code'].'<br />'.$list['name'];
				$nestedData[] = $list['reqid'];
				$nestedData[] = '<a href="'.$webUrl.'" target="_blank">Open URL</a>';
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


	//paysprint aeps


	public function newAepsKyc(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
            'content_block' => 'report/new-aeps-kyc'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getNewAepsKycList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code FROM tbl_new_aeps_member_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,c.state as state_name,d.city_name FROM tbl_new_aeps_member_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_aeps_state as c ON c.id = a.state_id LEFT JOIN tbl_city as d ON d.city_id = a.city_id where a.account_id = '$account_id' AND a.id > 0";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR a.mobile LIKE '".$keyword."%'";
				$sql.=" OR a.first_name LIKE '".$keyword."%'";
				$sql.=" OR a.last_name LIKE '".$keyword."%'";
				$sql.=" OR a.aadhar_no LIKE '".$keyword."%'";
				$sql.=" OR a.pancard_no LIKE '".$keyword."%'";
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
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />First Name - '.$list['first_name'].'<br />Last Name - '.$list['last_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['shop_name'];
				$nestedData[] = 'State - '.$list['state_name'].'<br />City - '.$list['city_name'].'<br />Address - '.$list['address'].'<br />Pin Code - '.$list['pin_code'];
				$nestedData[] = 'Aadhar No. - '.$list['aadhar_no'].'<br />PAN No. - '.$list['pancard_no'];

				$aadhar_str = 'Aadhar Front - Not Found';
				if($list['aadhar_photo'])
				{
					$aadhar_str = 'Aadhar Front - <a href="'.base_url($list['aadhar_photo']).'">Download</a>';
				}
				$pancard_str = 'PAN Card - Not Found';
				if($list['pancard_photo'])
				{
					$pancard_str = 'PAN Card - <a href="'.base_url($list['pancard_photo']).'">Download</a>';
				}


				$nestedData[] = $aadhar_str.'<br />'.$aadhar_back_str.'<br />'.$pancard_str;

				if($list['status'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				else{
					$nestedData[] = '<font color="red">Deactive</font>'.'<a title="delete" class="btn btn-danger btn-sm" href="'.base_url('admin/report/deleteKyc').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a>';
					//$nestedData[] ='<a title="delete" class="btn btn-danger btn-sm" href="'.base_url('admin/report/deleteKyc').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fa fa-trash" aria-hidden="true"></i></a>';
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


public function newAepsHistory(){

		//get logged user info
     	 $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$user = $this->db->get_where('users',array('account_id'=>$account_id,'is_active'=>1))->result_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'user' =>$user,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/new-aeps-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getNewAepsHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
        $toDate = '';
        $status = 0;
        $user = '';
        $service = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $user = isset($filterData[4]) ? trim($filterData[4]) : '';
            $service = isset($filterData[5]) ? trim($filterData[5]) : '';
		}
		
		$firstLoad = 0;

		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_new_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND  a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_new_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id' AND  a.id > 0";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.aadhar_no LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.service LIKE '%".$keyword."%'";
				$sql.=" OR a.message LIKE '%".$keyword."%'";
				$sql.=" OR a.txnID LIKE '%".$keyword."%' )";
			}

				if($firstLoad == 1)
			{
				$sql.=" AND DATE(created) = '".date('Y-m-d')."'";
			}
				if($status)
            {
                $sql.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql.=" AND a.member_id = '$user'";	
            
            }
             if($service != ''){
            
             $sql.=" AND service = '$service'";	
            
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


			$sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_new_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR b.name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.aadhar_no LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.amount LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.service LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.message LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.txnID LIKE '%".$keyword."%' )";
			}

			$sql_summery.=" ) as x WHERE x.id > 0";



		if($firstLoad == 1)
			{
				$sql_summery.=" AND DATE(created) = '".date('Y-m-d')."'";
			}


			if($status)
            {
                $sql_summery.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql_summery.=" AND x.member_id = '$user'";	
            
            }

              if($service != ''){
            
             $sql_summery.=" AND service = '$service'";	
            
            }

	


			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }


            $sql_success_summery = $sql_summery;	
			$sql_success_summery.=" AND x.status = 2";



			
			$get_success_recharge = $this->db->query($sql_success_summery)->row_array();
			


			$successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;
	    	
			
			
	    	

	        $sql_failed_summery = $sql_summery;
			$sql_failed_summery.=" AND x.status = 3";	
			$get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();
			
			
	        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
	        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;
	    	


		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				    $get_bank_name = $this->db->get_where('new_bank_list',array('iinno'=>$list['iinno']))->row_array();
				    $bank_name = $get_bank_name['bankName'];
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />Name - '.$list['user_name'];
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
				$nestedData[] = $bank_name;
				$nestedData[] = $list['aadhar_no'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = $list['txnID'];
				$nestedData[] = $list['message'];
				$nestedData[] = $list['is_from_app'] == 1 ? 'App' : 'Web';

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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,					
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}




	public function newPayoutTransfer(){

		//get logged user info
         $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$user = $this->db->get_where('users',array('account_id'=>$account_id,'is_active'=>1,'role_id >'=>2))->result_array();
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'user'			=>$user,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/new-payout-transfer-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getNewPayoutTransferList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
	   	$toDate = '';
	   	$status = 0;
        $user = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $user = isset($filterData[4]) ? trim($filterData[4]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			9 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.account_holder_name,b.account_number,c.name,c.user_code FROM tbl_new_aeps_payout as a INNER JOIN tbl_new_payout_beneficiary as b ON a.bene_id = b.id INNER JOIN tbl_users as c ON a.user_id = c.id  WHERE a.account_id = '$account_id' AND  a.id > 0 ";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.account_holder_name,b.account_number,c.name,c.user_code FROM tbl_new_aeps_payout as a INNER JOIN tbl_new_payout_beneficiary as b ON a.bene_id = b.id INNER JOIN tbl_users as c ON a.user_id = c.id  WHERE a.account_id = '$account_id' AND  a.id > 0 ";

			if($keyword != '') {   
				$sql.=" AND ( c.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR c.name LIKE '".$keyword."%' ";
				$sql.=" OR c.email LIKE '".$keyword."%' ";
				$sql.=" OR c.mobile LIKE '".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

            if($status)
            {
                $sql.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql.=" AND a.user_id = '$user'";	
            
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 7 : $requestData['order'][0]['column'] : 7;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();

			$sql_summery = "SELECT x.*,SUM(transfer_amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_new_aeps_payout as a INNER JOIN tbl_users as b ON b.id = a.user_id  where a.account_id = '$account_id'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql_summery.=" OR b.name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.txnType LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.op_txn_id LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			$sql_summery.=" ) as x WHERE x.id > 0";



		if($firstLoad == 1)
			{
				$sql_summery.=" AND DATE(created) = '".date('Y-m-d')."'";
			}


			if($status)
            {
                $sql_summery.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql_summery.=" AND x.user_id = '$user'";	
            
            }

              

			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }


            $sql_success_summery = $sql_summery;	
			$sql_success_summery.=" AND x.status = 3";

			
			$get_success_recharge = $this->db->query($sql_success_summery)->row_array();
			


			$successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;
	    	
			
			
	    	

	        $sql_failed_summery = $sql_summery;
			$sql_failed_summery.=" AND x.status = 4";	
			$get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();
			
			
	        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
	        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;


	        $sql_pending_summery = $sql_summery;
			$sql_pending_summery.=" AND x.status = 2";	
			$get_pending_recharge = $this->db->query($sql_pending_summery)->row_array();
			
			
	        $pendingAmount = isset($get_pending_recharge['totalAmount']) ? number_format($get_pending_recharge['totalAmount'],2) : '0.00';
	        $pendingRecord = isset($get_pending_recharge['totalRecord']) ? $get_pending_recharge['totalRecord'] : 0;

		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['name'];
				$nestedData[] = $list['account_holder_name'].' ('.$list['account_number'].')';
				$nestedData[] = 'INR '.$list['transfer_amount'];
				$nestedData[] = 'INR '.$list['transfer_charge_amount'];
				$nestedData[] = 'INR '.$list['total_wallet_deduct'];
				$nestedData[] = $list['refid'];
				if($list['status'] == 1)
				{
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2)
				{
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3)
				{
					$nestedData[] = '<font color="red">Failed</font>';
				}
				
				if($list['status'] < 3)

				{
					//$nestedData[] = '<a href="'.base_url('admin/report/refundNewPayoutTransfer').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this transaction?\')" class="btn btn-sm btn-primary">Refund</a>';
			        $nestedData[] = '<a href="#" onclick="payoutRefundBox('.$list['id'].'); return false;" class="btn btn-sm btn-danger">Refund</a>';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
				}
				
				$nestedData[] = date('d-m-Y H:i:s',strtotime($list['created']));
				
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,
					"pendingAmount"  => $pendingAmount,
					"pendingRecord"  => $pendingRecord,					
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}



    public function newMoneyTransferHistory(){
		$account_id = $this->User->get_domain_account();
		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		$user = $this->db->get_where('users',array('account_id'=>$account_id,'is_active'=>1,'role_id >'=>2))->result_array();
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'user'   =>$user,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/new-money-transfer-history'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getNewMoneyTransferList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
	   	$toDate = '';
	   	$status = 0;
        $user = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $user = isset($filterData[4]) ? trim($filterData[4]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			
			$sql = "SELECT a.* FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.txnType!= 'UPI'";

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$sql.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.txnType LIKE '%".$keyword."%'";
				$sql.=" OR a.op_txn_id LIKE '%".$keyword."%'";
				$sql.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($status)
            {
                $sql.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql.=" AND a.user_id = '$user'";	
            
            }

			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();

			$sql_summery = "SELECT SUM(COALESCE(CASE WHEN a.status = 3 THEN a.total_wallet_charge END,0)) totalSuccessAmount,count( case when a.status=3 then 1 else NULL end) totalSuccessRecord,SUM(COALESCE(CASE WHEN a.status = 4 THEN a.total_wallet_charge END,0)) totalFailedAmount,count( case when a.status=4 then 1 else NULL end) totalFailedRecord,SUM(COALESCE(CASE WHEN a.status = 2 THEN a.total_wallet_charge END,0)) totalPendingAmount,count( case when a.status=2 then 1 else NULL end) totalPendingRecord FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id WHERE a.account_id = '$account_id'";
			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql_summery.=" OR b.name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.txnType LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.op_txn_id LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
			}

			if($status)
            {
                $sql_summery.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql_summery.=" AND a.user_id = '$user'";	
            
            }

            $get_success_recharge = $this->db->query($sql_summery)->row_array();
			

			$successAmount = isset($get_success_recharge['totalSuccessAmount']) ? number_format($get_success_recharge['totalSuccessAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalSuccessRecord']) ? $get_success_recharge['totalSuccessRecord'] : 0;
	        $failedAmount = isset($get_success_recharge['totalFailedAmount']) ? number_format($get_success_recharge['totalFailedAmount'],2) : '0.00';
	        $failedRecord = isset($get_success_recharge['totalFailedRecord']) ? $get_success_recharge['totalFailedRecord'] : 0;
	        $pendingAmount = isset($get_success_recharge['totalPendingAmount']) ? number_format($get_success_recharge['totalPendingAmount'],2) : '0.00';
	        $pendingRecord = isset($get_success_recharge['totalPendingRecord']) ? $get_success_recharge['totalPendingRecord'] : 0;
	    	
			
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['memberID'];
				$nestedData[] = $list['account_holder_name'].'<br />'.$list['mobile'].'<br />'.$list['account_no'].'<br />'.$list['ifsc'];
				
				$nestedData[] = '&#8377; '.$list['transfer_amount'];
				$nestedData[] = '&#8377; '.$list['transfer_charge_amount'];
				
				$nestedData[] = $list['transaction_id'];
				$nestedData[] = $list['rrn'];

				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 4 && $list['force_status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				elseif($list['status'] == 4 && $list['force_status'] == 1) {
					$nestedData[] = '<font color="red">Refund</font>';
				}

				if($list['invoice_no']){
					
				 $nestedData[] = '<a href="'.base_url('admin/report/transferInvoice/'.$list['id'].'').'" target="_blank">'.$list['invoice_no'].'</a>';

				}
				else{

					$nestedData[] = 'Not Available';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));

				if($list['status'] == 2) {
					$nestedData[] = '<a href="'.base_url('admin/report/refundNewPayout').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this transaction?\')" class="btn btn-sm btn-primary">Refund</a> <a href="#" onclick="successNewPayout('.$list['id'].'); return false;" class="btn btn-sm btn-success">Success</a>';
				}
				elseif($list['status'] == 3 && $list['is_refund_by_callback'] == 0) {
					$nestedData[] = 'Not Allowed';
				}
				elseif($list['status'] == 3 && $list['is_refund_by_callback'] == 1) {
					$nestedData[] = '<a href="'.base_url('admin/report/refundNewPayout').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this transaction?\')" class="btn btn-sm btn-primary">Refund</a>';
				}
				elseif($list['status'] == 4) {
					$nestedData[] = 'Not Allowed';
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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,
					"pendingAmount"  => $pendingAmount,
					"pendingRecord"  => $pendingRecord,					
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}


	//instantpay aeps histor


	public function iciciAepsHistory($status = 0){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		
		$user = $this->db->get_where('users',array('account_id'=>$account_id,'is_active'=>1))->result_array();



  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'user' =>$user,
			'status' => $status,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/icici-aeps-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getIciciAepsHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
        $toDate = '';
        $status = 0;
        $user = '';
        $service = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $user = isset($filterData[4]) ? trim($filterData[4]) : '';
            $service = isset($filterData[5]) ? trim($filterData[5]) : '';
        }
		

		$firstLoad = 0;

		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.aadhar_no LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.service LIKE '%".$keyword."%'";
				$sql.=" OR a.message LIKE '%".$keyword."%'";
				$sql.=" OR a.txnID LIKE '%".$keyword."%' )";
			}


			if($firstLoad == 1)
			{
				$sql.=" AND DATE(created) = '".date('Y-m-d')."'";
			}


			if($status)
            {
                $sql.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql.=" AND a.member_id = '$user'";	
            
            }
             if($service != ''){
            
             $sql.=" AND service = '$service'";	
            
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
			



			$sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR b.name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.aadhar_no LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.amount LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.service LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.message LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.txnID LIKE '%".$keyword."%' )";
			}

			$sql_summery.=" ) as x WHERE x.id > 0";



		if($firstLoad == 1)
			{
				$sql_summery.=" AND DATE(created) = '".date('Y-m-d')."'";
			}


			if($status)
            {
                $sql_summery.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql_summery.=" AND x.member_id = '$user'";	
            
            }

              if($service != ''){
            
             $sql_summery.=" AND service = '$service'";	
            
            }

	


			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }


            $sql_success_summery = $sql_summery;	
			$sql_success_summery.=" AND x.status = 2";



			
			$get_success_recharge = $this->db->query($sql_success_summery)->row_array();
			


			$successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;
	    	
			
			
	    	

	        $sql_failed_summery = $sql_summery;
			$sql_failed_summery.=" AND x.status = 3";	
			$get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();
			
			
	        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
	        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;
	    	



		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				    
				    
				    $get_bank_name = $this->db->get_where('instantpay_aeps_bank_list',array('iinno'=>$list['iinno']))->row_array();
				    $bank_name = $get_bank_name['bank_name'];
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />Name - '.$list['user_name'];
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
					$nestedData[] = $bank_name;
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}

				/*if($list['service'] == 'balwithdraw' && ($list['status'] == 1 || $list['status'] == 3) && date('Y-m-d',strtotime($list['created'])) == date('Y-m-d')) {
					$nestedData[] = '<a href="'.base_url('admin/report/checkAepsStatus').'/'.$list['id'].'" class="btn btn-sm btn-primary">Check Status</a>';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
				}*/
				if($list['status'] == 3 && ($list['api_response'] == 0 || $list['api_response'] == NULL) && $list['force_status'] == 0) 
				{
					$nestedData[] = '<a href="'.base_url('admin/report/failedIciciAepsTxn').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to failed this transaction?\')" class="btn btn-sm btn-primary">Failed</a> <a href="'.base_url('admin/report/successIciciAepsTxn').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to success this transaction?\')" class="btn btn-sm btn-success">Success</a>';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
				}

				$nestedData[] = "<a href=".base_url('admin/report/iciciAepsInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";

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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,					
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}

	public function successIciciAepsTxn($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('instantpay_aeps_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/iciciAepsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(3))->get_where('instantpay_aeps_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/iciciAepsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Failed.</div>');
		}

		
		$this->db->where('id',$recharge_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('instantpay_aeps_transaction',array('status'=>2,'force_status'=>1,'message'=>'Manually Success','updated'=>date('Y-m-d H:i:s'),'updated_by'=>$account_id));

		// check recharge status
		$get_recharge_data = $this->db->get_where('instantpay_aeps_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$service = isset($get_recharge_data['service']) ? $get_recharge_data['service'] : '';
		$txnID = isset($get_recharge_data['txnID']) ? $get_recharge_data['txnID'] : '' ;
		$aadharNumber = isset($get_recharge_data['aadhar_no']) ? $get_recharge_data['aadhar_no'] : '' ;
		$iin = isset($get_recharge_data['iinno']) ? $get_recharge_data['iinno'] : '' ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : '' ;

		$loggedAccountID = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
		
		if($service == 'ministatement')
		{
			$this->User->forceAddStatementComIcici($txnID,$aadharNumber,$iin,$amount,$recharge_id,$account_id,$loggedAccountID);
		}
		elseif($service == 'balwithdraw' || $service == 'aadharpay')
		{
			$this->User->forceAddBalanceIcici($txnID,$aadharNumber,$iin,$amount,$recharge_id,$service,$account_id,$loggedAccountID);
		}
		$this->Az->redirect('admin/report/iciciAepsHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction successfully Credited.</div>');
	}

	public function failedIciciAepsTxn($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('instantpay_aeps_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/iciciAepsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(3))->get_where('instantpay_aeps_transaction',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/iciciAepsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Failed.</div>');
		}

		
		$this->db->where('id',$recharge_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('instantpay_aeps_transaction',array('status'=>3,'force_status'=>1,'message'=>'Manually Failed','updated'=>date('Y-m-d H:i:s'),'updated_by'=>$account_id));

		$this->Az->redirect('admin/report/iciciAepsHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction failed successfully.</div>');
	}


	//icici aeps kyc 

	public function iciciAepsKyc(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
            'content_block' => 'report/icici-aeps-kyc'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getIciciAepsKycList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code FROM tbl_instantpay_ekyc as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			/*$sql = "SELECT a.*, b.user_code as user_code FROM tbl_instantpay_ekyc as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";*/

			$sql = "SELECT a.*, b.user_code as user_code,c.state as state_name,d.city_name FROM tbl_instantpay_ekyc as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_aeps_state as c ON c.id = a.state_id LEFT JOIN tbl_city as d ON d.city_id = a.city_id where a.account_id = '$account_id'";

			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.first_name LIKE '%".$keyword."%'";
				$sql.=" OR a.last_name LIKE '%".$keyword."%'";
				$sql.=" OR a.aadhar LIKE '%".$keyword."%'";
				$sql.=" OR a.pancard LIKE '%".$keyword."%'";
				$sql.=" OR a.pin_code LIKE '%".$keyword."%'";
				$sql.=" OR a.shop_name LIKE '%".$keyword."%' )";
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
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />First Name - '.$list['first_name'].'<br />Middle Name - '.$list['middle_name'].'<br />Last Name - '.$list['last_name'].'<br />Father Name - '.$list['father_name'].'<br />Mother Name - '.$list['mother_name'];
				
				$nestedData[] = $list['mobile'];
				
				$nestedData[] = 'Aadhar No. - '.$list['aadhar'].'<br />PAN No. - '.$list['pancard'];


				$nestedData[] = 'Shop Name - '.$list['shop_name'].'<br />State - '.$list['state_name'].'<br />City - '.$list['city_name'].'<br />House No - '.$list['address'].'<br />Pincode - '.$list['pin_code'].'<br />Village - '.$list['village'].'<br />Post - '.$list['post'].'<br />Police Station - '.$list['police_station'].'<br />Block - '.$list['block'].'<br />District - '.$list['district'];

				$aadhar_str = 'Aadhar Front - Not Found';
				if($list['aadhar_photo'])
				{
					$aadhar_str = 'Aadhar Front - <a href="'.base_url($list['aadhar_photo']).'">Download</a>';
				}
				$aadhar_back_str = 'Aadhar Back - Not Found';
				if($list['aadhar_back_photo'])
				{
					$aadhar_back_str = 'Aadhar Back - <a href="'.base_url($list['aadhar_back_photo']).'">Download</a>';
				}

				$pancard_str = 'PAN Card - Not Found';
				if($list['pancard_photo'])
				{
					$pancard_str = 'PAN Card - <a href="'.base_url($list['pancard_photo']).'">Download</a>';
				}


				$nestedData[] = $aadhar_str.'<br />'.$aadhar_back_str.'<br/>'.$pancard_str;

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


	//icici account request list

	public function iciciChangeAccountList(){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        
       
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
           'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/icici-account-change-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getIciciAccountList()
    {   
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
            $sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_icici_payout_user_request  as a INNER JOIN tbl_users as b ON b.id = a.user_id  WHERE a.account_id = '$account_id' ";


            
            $totalData = $this->db->query($sql)->num_rows();
            $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
        
        
            $sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_icici_payout_user_request as a INNER JOIN tbl_users as b ON b.id = a.user_id  WHERE a.account_id = '$account_id'";
            
            if($keyword != '') {   
                $sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
                $sql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
                $sql.=" OR a.bank_name LIKE '%".$keyword."%'";
                $sql.=" OR a.account_no LIKE '%".$keyword."%'";
                $sql.=" OR a.ifsc LIKE '%".$keyword."%'";
                $sql.=" OR b.name LIKE '%".$keyword."%' )";
            }

            if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

           
            
            $order_type = $requestData['order'][0]['dir'];
            //if($requestData['draw'] == 1)
              //$order_type = 'DESC';
            
            $order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
            $totalFiltered = $this->db->query($sql)->num_rows();
            $sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        
               
        
            $get_filter_data = $this->db->query($sql)->result_array();
        
        $data = array();
        $totalrecord = 0;
        if($get_filter_data){
            $i=1;
            foreach($get_filter_data as $list){
                
                $get_bank_name = $this->db->get_where('aeps_bank_list',array('id'=>$list['bank_id']))->row_array();

                $bank_name = $get_bank_name['bank_name'];
                $nestedData=array(); 
                $nestedData[] = $i;
                $nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>".'<br>'.$list['name'];
                $nestedData[] = $list['account_holder_name'];
                $nestedData[] = $bank_name;
                 $nestedData[] = $list['account_no'];
                  $nestedData[] = $list['ifsc'];
              
                
                if($list['status'] == 1) {
                    $nestedData[] = '<font color="orange">Pending</font>';
                }
                elseif($list['status'] == 2) {
                    $nestedData[] = '<font color="green">Approved</font>';
                }
                elseif($list['status'] == 3) {
                    $nestedData[] = '<font color="red">Rejected</font>';
                }
                
                $nestedData[] = date('d-M-Y',strtotime($list['created']));
                
                if($list['status'] == 1){

                    $nestedData[] = '<a href="'.base_url('admin/report/approveIciciAccountRequest/'.$list['user_id']).'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to approve request?\')" title="Approve Request" class="btn btn-success btn-sm"><i class="fa fa-check" aria-hidden="true"></i></a>
                    <a href="'.base_url('admin/report/rejectIciciAccountRequest/'.$list['user_id']).'/'.$list['id'].'" title="Reject Request" onclick="return confirm(\'Are you sure you want to reject request?\')" class="btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }   
                else{

                    $nestedData[]='Not Allowed';
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



    public function approveIciciAccountRequest($user_id = 0, $id=0){

        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $chk_request = $this->db->get_where('tbl_icici_payout_user_request',array('account_id'=>$account_id,'user_id'=>$user_id,'id'=>$id))->row_array();


        

        if(!$chk_request){

            $this->Az->redirect('admin/report/iciciChangeAccountList', 'system_message_error',lang('MEMBER_ERROR'));

        }

       
        	  $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$user_id);
              $this->db->where('id',$id);
            $this->db->update('tbl_icici_payout_user_request',array('status'=>2));
       
        	  $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$user_id);
            
            $this->db->update('instantpay_payout_user_benificary',array('account_holder_name'=>$chk_request['account_holder_name'],'bankID'=>$chk_request['bank_id'],'account_no'=>$chk_request['account_no'],'ifsc'=>$chk_request['ifsc']));

            $this->Az->redirect('admin/report/iciciChangeAccountList', 'system_message_error',lang('REQUEST_APPROVE_SUCCESS'));

        }



         public function rejectIciciAccountRequest($user_id = 0 , $id = 0){

        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $chk_request = $this->db->get_where('tbl_icici_payout_user_request',array('account_id'=>$account_id,'user_id'=>$user_id,'id'=>$id))->row_array();
        

        if(!$chk_request){

            $this->Az->redirect('admin/report/iciciChangeAccountList', 'system_message_error',lang('MEMBER_ERROR'));

        }
       
       

             $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$user_id);
            $this->db->where('id',$id);
            $this->db->update('icici_payout_user_request',array('status'=>3));

            $this->Az->redirect('admin/report/iciciChangeAccountList', 'system_message_error',lang('REQUEST_REJECT_SUCCESS'));

        }



        //tds report


      public function tdsReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		
  		$siteUrl = base_url();		


  		$get_user_list = $this->db->get_where('users',array('account_id'=>$account_id,'role_id > '=>2))->result_array();

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'get_user_list'=>$get_user_list,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/tds-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

public function getTdsReportList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
        $toDate = '';
        $user = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $user = isset($filterData[3]) ? trim($filterData[3]) : '';
        }

        	$firstLoad = 0;
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code FROM tbl_tds_report as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as user_name FROM tbl_tds_report as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.description LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

             if($user != ''){
            
             $sql.=" AND a.member_id = '$user'";	
            
            }

			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
			
			$sql_summery = "SELECT x.*,SUM(tds_amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code FROM tbl_tds_report as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.description LIKE '%".$keyword."%'";
				$sql_summery.=" OR b.name LIKE '%".$keyword."%' )";
			}

			$sql_summery.=" ) as x WHERE x.id > 0";



		if($firstLoad == 1)
			{
				$sql_summery.=" AND DATE(created) = '".date('Y-m-d')."'";
			}


			

            if($user != ''){
            
             $sql_summery.=" AND x.member_id = '$user'";	
            
            }


			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }


            $sql_success_summery = $sql_summery;	
			



			
			$get_success_tds = $this->db->query($sql_success_summery)->row_array();
			

			$successAmount = isset($get_success_tds['totalAmount']) ? number_format($get_success_tds['totalAmount'],2) : '0.00';
	        
			
		$data = array();
		$totalrecord = 0;
		//$totalBalance = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = 'MemberID - '.$list['user_code'];
				$nestedData[] = $list['user_name'];
				$nestedData[] = '<font color="green">'.$list['com_amount'].'</font>';
				$nestedData[] = '<font color="red">'.$list['tds_amount'].'</font>';
				$nestedData[] = $list['description'];
				
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
					"successAmount" => $successAmount,
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function getNewPayoutData($recharge_id = 0)
	{
		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $chk_txn_id = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
        if($chk_txn_id)
        {

			// check recharge status
			$get_recharge_data = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id))->row_array();
			
			$recharge_unique_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
			$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
			
			$response = array(
				'status'=>1,
				'txnid' => $recharge_unique_id,
				'amount' => $amount
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




	public function refundNewPayout($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/newMoneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(2,3))->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/newMoneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Refunded/Success.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		

		$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
		$final_amount = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedAccountID);
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('user_new_fund_transfer',array('status'=>4,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));

		
		$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		$after_wallet_balance = $before_balance + $final_amount;    

        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $loggedAccountID,    
            'before_balance'      => $before_balance,
            'amount'              => $final_amount,  
            'after_balance'       => $after_wallet_balance,      
            'status'              => 1,
            'type'                => 1,   
            'wallet_type'		  => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'Payout Transfer #'.$transaction_id.' Amount Refund Manually.'
        );

        $this->db->insert('member_wallet',$wallet_data);

        //send call back to api user

        $get_role_id = $this->db->select('role_id,dmt_call_back_url,user_code')->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;

					if($user_role_id == 6)
							{
								$user_call_back_url = isset($get_role_id['dmt_call_back_url']) ? $get_role_id['dmt_call_back_url'] : '' ;
								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);

				        		/*$api_post_data = array();
				        		$api_post_data['status'] = 'FAILED';
				        		$api_post_data['txnid'] = $transaction_id;
				        		$api_post_data['optxid'] = '';
				        		$api_post_data['amount'] = $amount;
				        		$api_post_data['rrn'] = '';*/
                                
                                
                                $user_callback_data_url  = $user_call_back_url.'?status=FAILED&txnid='.$transaction_id.'&optxid=&amount='.$amount.'&rrn=';
                                
                                
				        		$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
								curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
								curl_setopt($ch, CURLOPT_TIMEOUT, 30);
								//curl_setopt($ch, CURLOPT_POST, true);
								//curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
								$output = curl_exec($ch); 
								curl_close($ch);

								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back Send Successfully.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);
								
							}
							
		$this->Az->redirect('admin/report/newMoneyTransferHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction refunded successfully.</div>');
	}

	public function successNewPayout()
	{
		$account_id = $this->User->get_domain_account();
		$post = $this->security->xss_clean($this->input->post());
		$recharge_id = isset($post['recordID']) ? $post['recordID'] : 0 ;
		$bank_rrn = isset($post['bank_rrn']) ? $post['bank_rrn'] : 0 ;
		$optxid = isset($post['optxid']) ? $post['optxid'] : 0 ;
		if(!$bank_rrn)
		{
			$this->Az->redirect('admin/report/newMoneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Please enter Bank RRN.</div>');
		}
		// check member
		$chkMember = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/newMoneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(2))->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/newMoneyTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Refunded/Success.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
		$surcharge_amount = isset($get_recharge_data['transfer_charge_amount']) ? $get_recharge_data['transfer_charge_amount'] : 0 ;
		$txnType = isset($get_recharge_data['txnType']) ? $get_recharge_data['txnType'] : '' ;

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedAccountID);
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('user_new_fund_transfer',array('op_txn_id'=>$optxid,'rrn'=>$bank_rrn,'status'=>3,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));
		

		$this->User->distribute_payout_commision($recharge_id,$transaction_id,$amount,$loggedAccountID,$surcharge_amount,'MD','ADMIN',$txnType);

		$get_role_id = $this->db->select('role_id,dmt_call_back_url,user_code')->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
						if($user_role_id == 6)
						{
							$user_call_back_url = isset($get_role_id['dmt_call_back_url']) ? $get_role_id['dmt_call_back_url'] : '' ;
							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);

			        		/*$api_post_data = array();
			        		$api_post_data['status'] = 'SUCCESS';
			        		$api_post_data['txnid'] = $transaction_id;
			        		$api_post_data['optxid'] = $optxid;
			        		$api_post_data['amount'] = $amount;
			        		$api_post_data['rrn'] = $bank_rrn;*/
			        		
			        		$user_callback_data_url  = $user_call_back_url.'?status=SUCCESS&txnid='.$transaction_id.'&optxid='.$optxid.'&amount='.$amount.'&rrn='.$bank_rrn;
			        		

			        		$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
							curl_setopt($ch, CURLOPT_TIMEOUT, 30);
							//curl_setopt($ch, CURLOPT_POST, true);
							//curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
							$output = curl_exec($ch); 
							curl_close($ch);

							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back Send Successfully.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);
							
						}


		$this->Az->redirect('admin/report/newMoneyTransferHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction successfully Credited.</div>');
	}
    
    
    
	//upi payout history


	public function upiTransferHistory(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/upi-transfer-history'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getUpiTransferList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.txnType= 'UPI'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_new_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.txnType= 'UPI'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.memberID LIKE '%".$keyword."%' ";    
				$sql.=" OR a.account_holder_name LIKE '%".$keyword."%'";
				$sql.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql.=" OR a.transaction_id LIKE '%".$keyword."%'";
				$sql.=" OR c.name LIKE '%".$keyword."%'";
				$sql.=" OR c.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.txnType LIKE '%".$keyword."%'";
				$sql.=" OR a.op_txn_id LIKE '%".$keyword."%'";
				$sql.=" OR a.rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.transfer_amount LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['account_holder_name'].'<br />'.$list['mobile'];
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
				elseif($list['status'] == 4 || $list['force_status'] == 1) {
					$nestedData[] = '<font color="red">Refund</font>';
				}
				
				if($list['invoice_no']){
					
				 $nestedData[] = '<a href="'.base_url('admin/report/transferInvoice/'.$list['id'].'').'" target="_blank">'.$list['invoice_no'].'</a>';

				}
				else{

					$nestedData[] = 'Not Available';
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));

				if($list['status'] == 2) {
					$nestedData[] = '<a href="'.base_url('admin/report/refundUpiPayout').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this transaction?\')" class="btn btn-sm btn-primary">Refund</a> <a href="#" onclick="successUpiPayout('.$list['id'].'); return false;" class="btn btn-sm btn-success">Success</a>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = 'Not Allowed';
				}
				elseif($list['status'] == 4) {
					$nestedData[] = 'Not Allowed';
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




	public function refundUpiPayout($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/upiTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(2))->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/upiTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Refunded/Success.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
		$final_amount = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedAccountID);
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('user_new_fund_transfer',array('status'=>4,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));

		
		$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		$after_wallet_balance = $before_balance + $final_amount;    

        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $loggedAccountID,    
            'before_balance'      => $before_balance,
            'amount'              => $final_amount,  
            'after_balance'       => $after_wallet_balance,      
            'status'              => 1,
            'type'                => 1,   
            'wallet_type'		  => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'Upi Transfer #'.$transaction_id.' Amount Refund.'
        );

        $this->db->insert('member_wallet',$wallet_data);

        
		$this->Az->redirect('admin/report/upiTransferHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction refunded successfully.</div>');
	}

	public function successUpiPayout()
	{
		$account_id = $this->User->get_domain_account();
		$post = $this->security->xss_clean($this->input->post());
		$recharge_id = isset($post['recordID']) ? $post['recordID'] : 0 ;
		$bank_rrn = isset($post['bank_rrn']) ? $post['bank_rrn'] : 0 ;
		if(!$bank_rrn)
		{
			$this->Az->redirect('admin/report/upiTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Please enter Bank RRN.</div>');
		}
		// check member
		$chkMember = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/upiTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(2))->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/upiTransferHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Refunded/Success.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
		$surcharge_amount = isset($get_recharge_data['transfer_charge_amount']) ? $get_recharge_data['transfer_charge_amount'] : 0 ;
		$txnType = isset($get_recharge_data['txnType']) ? $get_recharge_data['txnType'] : '' ;

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedAccountID);
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('user_new_fund_transfer',array('op_txn_id'=>$transaction_id,'rrn'=>$bank_rrn,'status'=>3,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));
		

		$this->User->distribute_payout_commision($recharge_id,$transaction_id,$amount,$loggedAccountID,$surcharge_amount,'MD','ADMIN',$txnType);

		
		$this->Az->redirect('admin/report/upiTransferHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction successfully Credited.</div>');
	}


    public function getUpiPayoutData($recharge_id = 0)
	{
		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $chk_txn_id = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
        if($chk_txn_id)
        {

			// check recharge status
			$get_recharge_data = $this->db->get_where('user_new_fund_transfer',array('id'=>$recharge_id))->row_array();
			
			$recharge_unique_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
			$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
			
			$response = array(
				'status'=>1,
				'txnid' => $recharge_unique_id,
				'amount' => $amount
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


public function iciciAepsInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


		$detail = $this->db->query($sql)->row_array();
		

		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'accountData' => $accountData,
			'detail' => $detail,
			'address'=>$address,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/icici-aeps-invoice'
        );
        $this->parser->parse('admin/layout/column-2' , $data);
    
	
	}



	//delete Kyc


	public function deleteKyc($id)
	{
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);
		// check user valid or not
		
		$get_image_path = $this->db->get_where('tbl_new_aeps_member_kyc',array('id' => $id))->row_array();

		$aadhar_image_path = isset($get_image_path['aadhar_photo']) ? $get_image_path['aadhar_photo'] : '';
		$pan_image_path = isset($get_image_path['pancard_photo']) ? $get_image_path['pancard_photo'] : '';



		if($aadhar_image_path)
		{
			if (file_exists($aadhar_image_path)) 
			{
			    unlink(str_replace('system/', '', BASEPATH . $aadhar_image_path));
			}
		}

		if($pan_image_path)
		{
			if (file_exists($pan_image_path)) 
			{
			    unlink(str_replace('system/', '', BASEPATH . $pan_image_path));
			}
		}


		
		$this->db->where('id',$id);
		$this->db->delete('new_aeps_member_kyc');
		
		$this->Az->redirect('admin/report/newAepsKyc', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Kyc delete successfully.</div>');
		
    }


    //nsdl pancard list



    public function nsdlPanList(){
		
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        
       
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
           'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/nsdl-pan-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getNsdlPanList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_nsdl_transcation as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_nsdl_transcation as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%' ";
				$sql.=" OR a.type LIKE '%".$keyword."%' ";
				$sql.=" OR a.order_id LIKE '%".$keyword."%' ";
				$sql.=" OR a.psacode LIKE '%".$keyword."%' ";
				$sql.=" OR a.pan_name LIKE '%".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '%".$keyword."%' ";
				$sql.=" OR a.email LIKE '%".$keyword."%' ";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//$order_type = 'DESC';
			
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
				$nestedData[] = $list['user_code']."<br />".$list['name'];
				//$nestedData[] = $list['first_name']."<br />".$list['middle_name']."<br>".$list['last_name'];
				$nestedData[] = 'First Name - '.$list['first_name'].'<br />Middle Name - '.$list['middle_name'].'<br /> Last Name - '.$list['last_name'];
				//$nestedData[] = $list['mode'];
				if($list['mode'] == 'E')
				{
					$nestedData[] = 'Electronic';
				}
				elseif($list['mode'] == 'P')
				{
					$nestedData[] = 'Physical';
				}
				else
				{
					$nestedData[] = ' Not Available';
				}
				$nestedData[] = $list['gender'];
				$nestedData[] = $list['email_id'];
				$nestedData[] = $list['transaction_id'];
				if($list['utr_no'])
				{
					$nestedData[] = $list['utr_no'];

				}
				else
				{
					$nestedData[] = 'Not Available';
				}
				if($list['ack_no'])
				{
					$nestedData[] = $list['ack_no'];

				}
				else
				{
					$nestedData[] = 'Not Available';
				}

				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				else
				{
					$nestedData[] = 'Proceed';
				}

				if($list['status'] == 1)
				{
					$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('admin/report/refundNsdlPan').'/'.$list['id'].'">Refund</a>';
				}
				else
				{
					$nestedData[] = 'Not Allowed';

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
    
    
    
    public function refundNsdlPan($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('member_nsdl_transcation',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/nsdlPanList', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(1))->get_where('member_nsdl_transcation',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('admin/report/nsdlPanList', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Already Refunded/Success.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('member_nsdl_transcation',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : 0 ;
		$amount = isset($get_recharge_data['charge_amount']) ? $get_recharge_data['charge_amount'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;

		$this->db->where('account_id',$account_id);
		$this->db->where('member_id',$loggedAccountID);
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('member_nsdl_transcation',array('status'=>3,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));

		
		$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		$after_wallet_balance = $before_balance + $amount;    

        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $loggedAccountID,    
            'before_balance'      => $before_balance,
            'amount'              => $amount,  
            'after_balance'       => $after_wallet_balance,      
            'status'              => 1,
            'type'                => 1,   
            'wallet_type'		  => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'NSDL #'.$transaction_id.' Amount Refund.'
        );

        $this->db->insert('member_wallet',$wallet_data);


		$this->Az->redirect('admin/report/nsdlPanList', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Transaction refunded successfully.</div>');
	}
    
    
    
    public function refundNewPayoutTransfer()
	{
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		
		$response = array();
		$post = $this->input->post();


		$recharge_id = isset($post['recordID']) ? $post['recordID'] : 0 ;
		//$txn_id = isset($post['txn_id']) ? $post['txn_id'] : '' ;
		$refund_password = isset($post['refund_password']) ? $post['refund_password'] : '' ;
		// check member
		$chkMember = $this->db->get_where('new_aeps_payout',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
		if(!$chkMember)
		{
			
			$response = array(
				'status' => 0,
				'msg' => 'Sorry ! You are not authorized to access this page.'
			);

		}

		else

		{
			//check transcation passwrd 
		 $chk_old_pwd = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id,'transaction_password'=>do_hash($refund_password)))->row_array();

		   if(!$chk_old_pwd)
	            {
	            	log_message('debug', 'Payout Refund Auth - IP : '.$user_ip_address.' - Refund Password is not valid.'); 
	                $response = array(
						'status' => 0,
						'msg' => 'Sorry ! Transaction Password is wrong.'
					);
	            }

	            else

	            {
	            	// check recharge status
		$get_recharge_data = $this->db->get_where('new_aeps_payout',array('id'=>$recharge_id,'account_id'=>$account_id))->row_array();
		
		$transaction_id = isset($get_recharge_data['refid']) ? $get_recharge_data['refid'] : 0 ;
		

		$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
		$final_amount = isset($get_recharge_data['total_wallet_deduct']) ? $get_recharge_data['total_wallet_deduct'] : 0 ;
		$loggedAccountID = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;

		$this->db->where('account_id',$account_id);
		$this->db->where('user_id',$loggedAccountID);
		$this->db->where('refid',$transaction_id);
		$this->db->update('new_aeps_payout',array('status'=>3,'force_status'=>1,'updated'=>date('Y-m-d H:i:s')));

		
		$before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
		$after_wallet_balance = $before_balance + $final_amount;    

        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $loggedAccountID,    
            'before_balance'      => $before_balance,
            'amount'              => $final_amount,  
            'after_balance'       => $after_wallet_balance,      
            'status'              => 1,
            'type'                => 1,   
            'wallet_type'		  => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'Payout Transfer #'.$transaction_id.' Amount Refund.'
        );

        $this->db->insert('member_wallet',$wallet_data);

        

				$response = array(
						'status' => 1,
						'msg' => 'Transaction refunded successfully.'
					);

	            }

		}

		echo json_encode($response);
		
	}
    
    
    
    public function nsdlPanCardList(){
		
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        
       
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
           'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/nsdl-pan-card-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getNsdlPanCardList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_morningpay_pancard_history as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_morningpay_pancard_history as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%' ";
				$sql.=" OR a.type LIKE '%".$keyword."%' ";
				$sql.=" OR a.order_id LIKE '%".$keyword."%' ";
				$sql.=" OR a.psacode LIKE '%".$keyword."%' ";
				$sql.=" OR a.pan_name LIKE '%".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '%".$keyword."%' ";
				$sql.=" OR a.email LIKE '%".$keyword."%' ";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//$order_type = 'DESC';
			
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
				$nestedData[] = $list['user_code']."<br />".$list['name'];
				if($list['type'] == 'Y')
				{
					$nestedData[] = 'Physical PAN';
				}
				elseif($list['type'] == 'N')
				{
					$nestedData[] = 'E-PAN';
				}
				else
				{
					$nestedData[] = ' Not Available';
				}
				$nestedData[] = $list['user_mobile'];
				//$nestedData[] = $list['email_id'];
				$nestedData[] = $list['txn_id'];
				if($list['order_id'])
				{
					$nestedData[] = $list['order_id'];

				}
				else
				{
					$nestedData[] = 'Not Available';
				}
				if($list['ackno'])
				{
					$nestedData[] = $list['ackno'];

				}
				else
				{
					$nestedData[] = 'Not Available';
				}

				if($list['pan_status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['pan_status'] ==1) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				else {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				$nestedData[] = $list['txn_date'];
				
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
	
	
	//pan activation list

	 public function nsdlActivationList(){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        
       
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
           'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/nsdl-activation-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getNsdlActivationList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_nsdl_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql ="SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_nsdl_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%' ";				
				$sql.=" OR a.mobile LIKE '%".$keyword."%' ";
				$sql.=" OR a.email LIKE '%".$keyword."%' ";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//$order_type = 'DESC';
			
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
				$nestedData[] = $list['user_code']."<br />".$list['name'];
				$nestedData[] = $list['txn_id'];
				$nestedData[] = 'First Name - '.$list['firstname'].'<br/>'.'Middle Name - '.$list['middlename'].'<br/>'.'Last Name - '.$list['lastname'];
				
				$nestedData[] = 'Aadhar No. - '.$list['aadhar_number'].'<br />PAN No. - '.$list['pan_number'];

				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['email'];	
				if($list['gender'] == 'M')
				{
					$nestedData[] = 'Male';
				}
				else{
					$nestedData[] = 'Female';
				}	
				$nestedData[] = date('d-M-Y',strtotime($list['dob']));
				$nestedData[] = "Address - ".$list['address']."<br />"."Shop Name - ".$list['shop_name'];
				
				if($list['state_id'])

				{
					$get_state = $this->db->get_where('nsdl_state',array('id'=>$list['state_id']))->row_array();
					$nestedData[] = $get_state['title'];
				}
				else
				{
				$nestedData[] = 'Not Available';
				}

				if($list['district_id'])

				{
					$get_state = $this->db->get_where('nsdl_district',array('id'=>$list['district_id']))->row_array();
					$nestedData[] = $get_state['title'];
				}
				else
				{
				$nestedData[] = 'Not Available';
				}
				$nestedData[] = $list['pan_number'];
				$nestedData[] = $list['aadhar_number'];

				if($list['status'] == 2) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 1) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				else
				{
					$nestedData[] = 'Proceed';
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

	//gst report

	public function gstReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		
  		$siteUrl = base_url();		


  		$get_user_list = $this->db->get_where('users',array('account_id'=>$account_id,'role_id > '=>2))->result_array();

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'get_user_list'=>$get_user_list,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/gst-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

public function getGstReportList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
        $toDate = '';
        $user = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $user = isset($filterData[3]) ? trim($filterData[3]) : '';
        }

        	$firstLoad = 0;
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code FROM tbl_gst_report as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as user_name FROM tbl_gst_report as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.txn_id LIKE '%".$keyword."%'";
				$sql.=" OR a.description LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }

             if($user != ''){
            
             $sql.=" AND a.member_id = '$user'";	
            
            }

			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
			
			$sql_summery = "SELECT x.*,SUM(gst_charge) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code FROM tbl_gst_report as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR b.mobile LIKE '%".$keyword."%'";				
				$sql_summery.=" OR a.txn_id LIKE '%".$keyword."%'";					
				$sql_summery.=" OR a.description LIKE '%".$keyword."%'";
				$sql_summery.=" OR b.name LIKE '%".$keyword."%' )";
			}

			$sql_summery.=" ) as x WHERE x.id > 0";



		if($firstLoad == 1)
			{
				$sql_summery.=" AND DATE(created) = '".date('Y-m-d')."'";
			}


			

            if($user != ''){
            
             $sql_summery.=" AND x.member_id = '$user'";	
            
            }


			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }


            $sql_success_summery = $sql_summery;	
			
			$get_success_tds = $this->db->query($sql_success_summery)->row_array();
			

			$successAmount = isset($get_success_tds['totalAmount']) ? number_format($get_success_tds['totalAmount'],2) : '0.00';
	        
			
		$data = array();
		$totalrecord = 0;
		
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = 'MemberID - '.$list['user_code'];
				$nestedData[] = $list['user_name'];
				$nestedData[] = $list['service'];
				$nestedData[] = $list['txn_id'];
				$nestedData[] = $list['charge_amount'];
				$nestedData[] = '<font color="red">'.$list['gst_charge'].'</font>';
				$nestedData[] = $list['description'];
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
					"successAmount" => $successAmount,
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function findPanReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'get_list'=>$get_list,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/find-pan-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getFindPanList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as retailer_name FROM tbl_find_pan_number as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql ="SELECT a.*, b.user_code as user_code, b.name as retailer_name FROM tbl_find_pan_number as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txn_id LIKE '%".$keyword."%' ";				
				$sql.=" OR b.mobile LIKE '%".$keyword."%' ";
				$sql.=" OR b.email LIKE '%".$keyword."%' ";
				$sql.=" OR b.name LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//$order_type = 'DESC';
			
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
				$nestedData[] = $list['user_code']."<br />".$list['retailer_name'];
				$nestedData[] = $list['txn_id'];
				$nestedData[] = $list['name'];
				$nestedData[] = date('d-M-Y',strtotime($list['dob']));
				$nestedData[] = $list['aadhar_number'];
				if($list['pan_img'])
				{
					$nestedData[] = '<a href="'.base_url($list['pan_img']).'">Download</a>';
				}
				else
				{
					$nestedData[] = 'Not Available';
				}
				if($list['pan_img'])
				{
					$nestedData[] = 'Not Available';
				}
				else
				{
					$nestedData[] = '<a href="#" onclick="uploadPanImg('.$list['id'].'); return false;" class="btn btn-sm btn-success">Upload Image</a>';
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



	public function updateFindPanImage(){

      $post = $this->input->post();



     	$pan_photo = '';
			if(isset($_FILES['pan_photo']['name']) && $_FILES['pan_photo']['name']){
				$config['upload_path'] = './media/aeps_kyc_doc/';
				$config['allowed_types'] = 'jpg|png|jpeg';
				$config['max_size'] = 2048;
				$fileName = time().rand(111111,999999);
				$config['file_name'] = $fileName;
				$this->load->library('upload', $config);
				$this->upload->do_upload('pan_photo');		
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					$this->Az->redirect('admin/report/findPanReport', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$pan_photo = substr($config['upload_path'] . $fileData['file_name'], 2);
				}
			}


			if($post){

        $data = array(
          'pan_img' => $pan_photo,          
        );

        $this->db->where('id',$post['aadharID']);
        $this->db->update('find_pan_number',$data);

      }

      $this->Az->redirect('admin/report/findPanReport', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>PAN Image Update successfully.</div>');
      
    }
    
    
    public function getNewAepsPayoutData($recharge_id = 0)
	{
		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $chk_txn_id = $this->db->get_where('new_aeps_payout',array('id'=>$recharge_id,'account_id'=>$account_id))->num_rows();
        if($chk_txn_id)
        {

			// check recharge status
			$get_recharge_data = $this->db->get_where('new_aeps_payout',array('id'=>$recharge_id))->row_array();
			
			$recharge_unique_id = isset($get_recharge_data['refid']) ? $get_recharge_data['refid'] : 0 ;
			$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
			$total_wallet_deduct = isset($get_recharge_data['total_wallet_deduct']) ? $get_recharge_data['total_wallet_deduct'] : 0 ;
			
			$response = array(
				'status'=>1,
				'txnid' => $recharge_unique_id,
				'amount' => $amount,
				'total_wallet_deduct'=>$total_wallet_deduct
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


	public function referralComReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(5, $activeService)){
			$this->Az->redirect('admin/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'report/referral-commission-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getReferralCommissionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			5 => 'a.id',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as from_user_code, b.name as from_name, c.user_code as to_user_code, c.name as to_name FROM tbl_member_referral_comission as a INNER JOIN tbl_users as b ON b.id = a.from_member_id INNER JOIN tbl_users as c ON c.id = a.to_member_id where a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as from_user_code, b.name as from_name, c.user_code as to_user_code, c.name as to_name FROM tbl_member_referral_comission as a INNER JOIN tbl_users as b ON b.id = a.from_member_id INNER JOIN tbl_users as c ON c.id = a.to_member_id where a.id > 0 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   

				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR c.name LIKE '%".$keyword."%'";
				$sql.=" OR c.user_code LIKE '%".$keyword."%' )";
			}

			if($fromDate != '' && $toDate != '') {   
				$sql.=" AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."' ";    
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
				$nestedData[] = $list['to_name'].'<br>('.$list['to_user_code'].')'."</a>";
				$nestedData[] = $list['from_name'].'<br>('.$list['from_user_code'].')'."</a>";
				if($list['service_id'] == 5)
				{
					$nestedData[] = 'Collection';
				}
				else
				{
					$nestedData[] = 'Payout';
				}
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['amount'].' /-';
				$nestedData[] = $list['comission'].' /-';
				
				
				if($list['is_paid'] == 1) {
					$nestedData[] = '<font color="green">Yes</font>';
				}
				else{
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


	public function commissionReport(){
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        	
        $user_type = $this->db->where_in('id',array(3,4,5,6))->get('user_roles')->result_array();
        
		$siteUrl = base_url();	
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'=>$loggedUser, 
            'user_type'=>$user_type,
        	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/commission-report'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    }

    public function getCommissionReport()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
        $user_type = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $user_type = isset($filterData[1]) ? trim($filterData[1]) : '';
            
        }
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.title as role FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.title as role FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.account_id = '$account_id'";

			if($keyword != '') {   
				$sql.=" AND ( b.title LIKE '%".$keyword."%' ";    
				$sql.=" OR a.user_code LIKE '%".$keyword."%'";
				$sql.=" OR a.name LIKE '%".$keyword."%' )";
			}
			
			if($user_type != ''){
            
             $sql.=" AND a.role_id = '$user_type'";	
            
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
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['role'];
				$nestedData[] = $list['name'];
				
				// get total commission
				$getTotalCommission = $this->db->select('SUM(comission) as totalCollection')->get_where('member_referral_comission',array('account_id'=>$account_id,'to_member_id'=>$list['id']))->row_array();
				$totalCommission = isset($getTotalCommission['totalCollection']) ? $getTotalCommission['totalCollection'] : 0 ;
				$nestedData[] = number_format($totalCommission,2).' /-';


				// get total commission
				$getTotalCommission = $this->db->select('SUM(comission) as totalCollection')->get_where('member_referral_comission',array('account_id'=>$account_id,'to_member_id'=>$list['id'],'is_paid'=>1))->row_array();
				$totalCommission = isset($getTotalCommission['totalCollection']) ? $getTotalCommission['totalCollection'] : 0 ;
				$nestedData[] = '<font color="green">'.number_format($totalCommission,2).' /-</font>';

				// get total commission
				$getTotalCommission = $this->db->select('SUM(comission) as totalCollection')->get_where('member_referral_comission',array('account_id'=>$account_id,'to_member_id'=>$list['id'],'is_paid'=>0))->row_array();
				$totalCommission = isset($getTotalCommission['totalCollection']) ? $getTotalCommission['totalCollection'] : 0 ;
				$nestedData[] = '<font color="red">'.number_format($totalCommission,2).' /-</font>';

				
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

	public function upiQrHistory(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(5, $activeService)){
			$this->Az->redirect('admin/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'report/upi-qr-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getUpiQrList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_upi_dynamic_qr as a LEFT JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_upi_dynamic_qr as a LEFT JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   

				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR a.refId LIKE '%".$keyword."%' )";
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
				if($list['name'])
				{
					$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['name'].'<br>('.$list['user_code'].')'."</a>";
				}
				else
				{
					$nestedData[] = 'Not Found';
				}
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['refId'];
				$nestedData[] = $list['amount'].' /-';
				$nestedData[] = $list['ip_address'];
				$nestedData[] = '<a href="'.$list['qr_image'].'" class="btn btn-sm btn-primary" target="_blank">View QR</a>';
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


	public function upiApiLog(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(5, $activeService)){
			$this->Az->redirect('admin/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'report/upi-api-log'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getUpiApiLogList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_upi_api_response as a LEFT JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_upi_api_response as a LEFT JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   

				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR a.post_data LIKE '%".$keyword."%' )";
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
				if($list['name'])
				{
					$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['name'].'<br>('.$list['user_code'].')'."</a>";
				}
				else
				{
					$nestedData[] = 'Not Found';
				}
				$nestedData[] = $list['txnid'];
				$nestedData[] = $list['post_data'];
				$nestedData[] = $list['response'];
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


	public function payoutApiLog(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		$activeService = $this->User->admin_active_service();
		if(!in_array(5, $activeService)){
			$this->Az->redirect('admin/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
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
            'content_block' => 'report/payout-api-log'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getPayoutApiLogList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_cib_api_response as a LEFT JOIN tbl_users as b ON b.id = a.user_id  where a.id > 0 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_cib_api_response as a LEFT JOIN tbl_users as b ON b.id = a.user_id  where a.id > 0 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   

				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%'";
				$sql.=" OR a.post_data LIKE '%".$keyword."%' )";
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
				if($list['name'])
				{
					$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['name'].'<br>('.$list['user_code'].')'."</a>";
				}
				else
				{
					$nestedData[] = 'Not Found';
				}
				$nestedData[] = $list['txnid'];

				$decodePostData = json_decode($list['post_data'],true);

				$nestedData[] = isset($decodePostData['AMOUNT']) ? $decodePostData['AMOUNT'] : '';
				$nestedData[] = 'A/C : '.$decodePostData['CREDITACC'].'<br />IFSC : '.$decodePostData['IFSC'].'<br />Name : '.$decodePostData['PAYEENAME'];

				$decodeResponseData = json_decode($list['api_response'],true);

				$nestedData[] = isset($decodeResponseData['STATUS']) ? $decodeResponseData['STATUS'] : '';
				$nestedData[] = isset($decodeResponseData['URN']) ? $decodeResponseData['URN'] : '';
				$nestedData[] = isset($decodeResponseData['UTRNUMBER']) ? $decodeResponseData['UTRNUMBER'] : '';
				$nestedData[] = isset($decodeResponseData['RESPONSE']) ? $decodeResponseData['RESPONSE'] : '';
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


	// fingpay aeps report

	public function fingpayAepsHistory($status = 0){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		
		$user = $this->db->get_where('users',array('account_id'=>$account_id,'is_active'=>1))->result_array();



  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'user' =>$user,
			'status' => $status,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/aeps-list'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function getFingpayAepsHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$fromDate = '';
        $toDate = '';
        $status = 0;
        $user = '';
        $service = '';
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            $fromDate = isset($filterData[1]) ? trim($filterData[1]) : '';
            $toDate = isset($filterData[2]) ? trim($filterData[2]) : '';
            $status = isset($filterData[3]) ? trim($filterData[3]) : 0;
            $user = isset($filterData[4]) ? trim($filterData[4]) : '';
            $service = isset($filterData[5]) ? trim($filterData[5]) : '';
        }
		

		$firstLoad = 0;

		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.aadhar_no LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.service LIKE '%".$keyword."%'";
				$sql.=" OR a.message LIKE '%".$keyword."%'";
				$sql.=" OR a.txnID LIKE '%".$keyword."%' )";
			}


			if($firstLoad == 1)
			{
				$sql.=" AND DATE(created) = '".date('Y-m-d')."'";
			}


			if($status)
            {
                $sql.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql.=" AND a.member_id = '$user'";	
            
            }
             if($service != ''){
            
             $sql.=" AND service = '$service'";	
            
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
			



			$sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.account_id = '$account_id'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR b.name LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.aadhar_no LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.amount LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.service LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.message LIKE '%".$keyword."%'";
				$sql_summery.=" OR a.txnID LIKE '%".$keyword."%' )";
			}

			$sql_summery.=" ) as x WHERE x.id > 0";



		if($firstLoad == 1)
			{
				$sql_summery.=" AND DATE(created) = '".date('Y-m-d')."'";
			}


			if($status)
            {
                $sql_summery.=" AND status = '$status'";
            }

            if($user != ''){
            
             $sql_summery.=" AND x.member_id = '$user'";	
            
            }

              if($service != ''){
            
             $sql_summery.=" AND service = '$service'";	
            
            }

	


			if($fromDate && $toDate)
            {
                $sql_summery.=" AND DATE(created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }


            $sql_success_summery = $sql_summery;	
			$sql_success_summery.=" AND x.status = 2";



			
			$get_success_recharge = $this->db->query($sql_success_summery)->row_array();
			


			$successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
	        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;
	    	
			
			
	    	

	        $sql_failed_summery = $sql_summery;
			$sql_failed_summery.=" AND x.status = 3";	
			$get_failed_recharge = $this->db->query($sql_failed_summery)->row_array();
			
			
	        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
	        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;
	    	



		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				    
				    
				    $get_bank_name = $this->db->get_where('instantpay_aeps_bank_list',array('iinno'=>$list['iinno']))->row_array();
				    $bank_name = $get_bank_name['bank_name'];
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />Name - '.$list['user_name'];
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
					$nestedData[] = $bank_name;
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
				}

				/*if($list['service'] == 'balwithdraw' && ($list['status'] == 1 || $list['status'] == 3) && date('Y-m-d',strtotime($list['created'])) == date('Y-m-d')) {
					$nestedData[] = '<a href="'.base_url('admin/report/checkAepsStatus').'/'.$list['id'].'" class="btn btn-sm btn-primary">Check Status</a>';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
				}*/
				if($list['status'] == 3 && ($list['api_response'] == 0 || $list['api_response'] == NULL) && $list['force_status'] == 0) 
				{
					//$nestedData[] = '<a href="'.base_url('admin/report/failedIciciAepsTxn').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to failed this transaction?\')" class="btn btn-sm btn-primary">Failed</a> <a href="'.base_url('admin/report/successIciciAepsTxn').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to success this transaction?\')" class="btn btn-sm btn-success">Success</a>';
					$nestedData[] = 'Not Allowed';
				}
				else
				{
					$nestedData[] = 'Not Allowed';
				}

				$nestedData[] = "<a href=".base_url('admin/report/iciciAepsInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";

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
					"successAmount" => $successAmount,
					"successRecord" => $successRecord,					
					"failedAmount"  => $failedAmount,
					"failedRecord"  => $failedRecord,
					);

		echo json_encode($json_data);  // send data as json format
	}




	public function utiBalanceReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'get_list'=>$get_list,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/uti-balance-request'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getUtiBalanceList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
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
			0 => 'id'	
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as retailer_name FROM tbl_uti_balance_request as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql ="SELECT a.*, b.user_code as user_code, b.name as retailer_name FROM tbl_uti_balance_request as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txn_id LIKE '%".$keyword."%' ";
				$sql.=" OR a.uti_pan_id LIKE '%".$keyword."%' )";
			}

			if($fromDate && $toDate)
            {
                $sql.=" AND DATE(a.created) >= '".date('Y-m-d',strtotime($fromDate))."' AND DATE(a.created) <= '".date('Y-m-d',strtotime($toDate))."'";
            }
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//$order_type = 'DESC';
			
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
				$nestedData[] = $list['user_code']."<br />".$list['retailer_name'];
				$nestedData[] = $list['txn_id'];
				$nestedData[] = $list['uti_pan_id'];
				$nestedData[] = $list['coupon'];	
				
				if($list['status'] == 1)
				{
					
					$nestedData[] ='<a title="Approve" class="btn btn-success btn-sm" href="'.base_url('admin/report/updateUtiBalanceAuth').'/'.$list['id'].'/1" onclick="return confirm(\'Are you sure you want to approve this request?\')"><i class="fa fa-check" aria-hidden="true"></i></a> <a title="Reject" class="btn btn-danger btn-sm" href="javascript:void(0)" onclick="utiBalanceBox('.$list['id'].');"><i class="fa fa-times" aria-hidden="true"></i></a>';
				}
				elseif($list['status'] == 2)
				{
					$nestedData[] = '<font color="green">Approved</font>';
					//$nestedData[] ='Updated';
				}
				elseif($list['status'] == 3)
				{
					$nestedData[] = '<font color="red">Rejected</font>';
					//$nestedData[] ='Updated';
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



	//update status

	public function updateUtiBalanceAuth($requestID = 0, $status = 0)
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		// check request id valid or not
		$chk_request_id = $this->db->get_where('uti_balance_request',array('id'=>$requestID,'status'=>1,'account_id'=>$account_id))->num_rows();
		if(!$chk_request_id)
		{
			$this->Az->redirect('admin/report/utiBalanceReport', 'system_message_error',lang('WALLET_ERROR'));
		}

		// get request member id
		$chk_request_id = $this->db->get_where('uti_balance_request',array('id'=>$requestID,'status'=>1,'account_id'=>$account_id))->num_rows();
		if(!$chk_request_id)
		{
			$this->Az->redirect('admin/report/utiBalanceReport', 'system_message_error',lang('WALLET_ERROR'));
		}

		//$chk_request_id = $this->db->get_where('uti_balance_request',array('id'=>$requestID,'status'=>1,'account_id'=>$account_id))->row_array();

		else
		{

			$this->db->where('id',$requestID);
            $this->db->update('uti_balance_request',array('status'=>2,'is_read'=>1));
            $this->Az->redirect('admin/report/utiBalanceReport', 'system_message_error',lang('REQUEST_APPROVE_SUCCESS'));
		}

	}



	public function rejectUtiBalance(){

      $post = $this->input->post();

      if($post){

        $data = array(
          'status' => 3,
          'remark' => $post['remark'],
          'is_read'=>1
        );

        $this->db->where('id',$post['requestID']);
        $this->db->update('uti_balance_request',$data);

      }

      $this->Az->redirect('admin/report/utiBalanceReport', 'system_message_error',lang('REQUEST_REJECT_SUCCESS'));

    }





    
}