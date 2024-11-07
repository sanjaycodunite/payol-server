<?php 
require XLSX_LIB_ROOT_PATH;
use \yidas\phpSpreadsheet\Helper;
class Report extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkPermission();
       	$this->lang->load('superadmin/dashboard', 'english');
        
    }

	public function recharge($status = 0){
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        	
        $user_type = $this->db->where_in('id',array(3,4,5,6))->get('user_roles')->result_array();
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    }

    public function getRechargeList()
	{	
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.recharge_type != 7) as x ";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
			if($accountData['account_type'] != 2)
			{
				$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7) as x WHERE x.id > 0";
			}
			else
			{
				$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7) as x WHERE x.id > 0";
			}

			if($keyword != '') {   
				$sql.=" AND ( user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR mobile LIKE '%".$keyword."%'";
				$sql.=" OR circle_code LIKE '%".$keyword."%'";
				$sql.=" OR operator_name LIKE '%".$keyword."%'";
				$sql.=" OR recharge_type LIKE '".$keyword."%'";
				$sql.=" OR recharge_display_id LIKE '%".$keyword."%'";
				$sql.=" OR operator_ref LIKE '%".$keyword."%'";
				$sql.=" OR name LIKE '".$keyword."%' )";
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


			$sql_summery = "SELECT x.*,SUM(amount) as totalAmount,count(*) as totalRecord FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7) as x WHERE x.id > 0";


			if($keyword != '') {   
				$sql_summery.=" AND ( user_code LIKE '%".$keyword."%' ";    
				$sql_summery.=" OR mobile LIKE '%".$keyword."%'";
				$sql_summery.=" OR circle_code LIKE '%".$keyword."%'";
				$sql_summery.=" OR operator_name LIKE '%".$keyword."%'";
				$sql_summery.=" OR recharge_type LIKE '".$keyword."%'";
				$sql_summery.=" OR recharge_display_id LIKE '%".$keyword."%'";
				$sql_summery.=" OR operator_ref LIKE '%".$keyword."%'";
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
				
				$nestedData[] = "<a href=".base_url('superadmin/report/rechargeInvoice/').$list['recharge_display_id']." style='text-decoration:none;' target='_blank'>Invoice</a>";	
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
					$nestedData[] = '<a href="'.base_url('superadmin/report/refundRecharge').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this recharge?\')" class="btn btn-sm btn-primary">Refund</a> <a href="'.base_url('superadmin/report/successRecharge').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to success this recharge?\')" class="btn btn-sm btn-primary">Success</a>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
					if($list['force_status'] == 1)
					{
						$nestedData[] = '<font color="red">Refund</font>';
					}
					else
					{
						$nestedData[] = '<a href="'.base_url('superadmin/report/refundRecharge').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this recharge?\')" class="btn btn-sm btn-primary">Refund</a>';
					}
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT x.* FROM (SELECT a.*, b.user_code as user_code, b.name as name,b.role_id,c.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as c ON c.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7) as x WHERE x.recharge_display_id = '$id'";

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
        $this->parser->parse('superadmin/layout/column-2' , $data);
    
	
	}

	public function refundRecharge($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('recharge_history',array('id'=>$recharge_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('superadmin/report/recharge', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(1,2))->get_where('recharge_history',array('id'=>$recharge_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('superadmin/report/recharge', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Recharge Already Refunded.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('recharge_history',array('id'=>$recharge_id))->row_array();
		
		$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
		$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;

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

        

        $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
        if($is_cogent_instantpay_api)
        {
        	$admin_id = $this->User->get_admin_id($account_id);
            $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
            $admin_after_wallet_balance = $admin_before_wallet_balance + $amount;

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $admin_id,    
                'before_balance'      => $admin_before_wallet_balance,
                'amount'              => $amount,  
                'after_balance'       => $admin_after_wallet_balance,      
                'status'              => 1,
                'type'                => 1,   
                'wallet_type'         => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'Recharge Refund #'.$recharge_unique_id.' Amount Deducted.'
            );

            $this->db->insert('virtual_wallet',$wallet_data);

           
        }

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

		$this->Az->redirect('superadmin/report/recharge', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Recharge refunded successfully.</div>');
	}

	public function successRecharge($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('recharge_history',array('id'=>$recharge_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('superadmin/report/recharge', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'status'=>1))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('superadmin/report/recharge', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Recharge Status Already Updated.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('recharge_history',array('id'=>$recharge_id))->row_array();
		
		$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
		$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;

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

		$this->Az->redirect('superadmin/report/recharge', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Recharge got success.</div>');
	}

	public function bbps(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getBBPSList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.recharge_type = 7";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0 AND a.recharge_type = 7";
			
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getPaymentList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
				$nestedData[] = $list['memberID'];
				$nestedData[] = $list['account_holder_name'];
				$nestedData[] = $list['mobile'];
				$nestedData[] = $list['account_no'];
				$nestedData[] = $list['ifsc'];
				$nestedData[] = 'Txn Amount - '.$list['transfer_amount'].'<br />Charge Amount - '.$list['transfer_charge_amount'];
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
				$nestedData[] = "<a href=".base_url('superadmin/report/moneyTransferInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";
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

	public function moneyTransferInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.name as user_name FROM tbl_user_fund_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.id = '$id'";
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
        $this->parser->parse('superadmin/layout/column-2' , $data);
    
	
	}


	public function moneyTransferHistory(){

		//get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
            'content_block' => 'report/money-transfer-history'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getMoneyTransferList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,c.name as sender_name,c.mobile as sender_mobile FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
				elseif($list['status'] == 4 || $list['status'] == 0) {
					$nestedData[] = '<font color="red">Failed</font>';
				}
				if($list['invoice_no']){
					
				 $nestedData[] = '<a href="'.base_url('superadmin/report/transferInvoice/'.$list['id'].'').'" target="_blank">'.$list['invoice_no'].'</a>';

				}
				else{

					$nestedData[] = 'Not Available';
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

	public function transferInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);
		$contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
		

		$sql = "SELECT a.*,b.name as member_name,c.name as sender_name FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_sender as c ON c.id = a.from_sender_id where a.id = '$id'";
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
        $this->parser->parse('superadmin/layout/column-2' , $data);
    
	
	}


	public function rechargeCommision(){

		//get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getRechargeCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.id > 0 AND a.type = 'RECHARGE'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.recharge_display_id FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_recharge_history as c ON c.id = a.record_id where a.id > 0 AND a.type = 'RECHARGE'";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getBBPSCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_history as c ON c.id = a.record_id where a.id > 0 AND a.type = 'BBPS'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.recharge_display_id FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_history as c ON c.id = a.record_id where a.id > 0 AND a.type = 'BBPS'";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getFundTransferCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_fund_transfer as c ON c.id = a.record_id where a.id > 0 AND a.type = 'PAYOUT'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.transaction_id,c.transfer_amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_user_fund_transfer as c ON c.id = a.record_id where a.id > 0 AND a.type = 'PAYOUT'";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();

        $fromDate = date('Y-m-d');
        $toDate = date('Y-m-d');

        $sql = "SELECT a.*, b.user_code as user_code, b.name as name,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."' ORDER BY a.created DESC";
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
        $this->parser->parse('superadmin/layout/column-3' , $data);
    }


    public function getLiveRechargeData(){
		
    	

        $response = array();
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

	 	$fromDate = date('Y-m-d');
        $toDate = date('Y-m-d');

        $sql = "SELECT a.*, b.user_code as user_code, b.name as name,d.operator_name FROM tbl_recharge_history as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_operator as d ON d.id = a.system_opt_id where a.id > 0 AND a.recharge_type != 7 AND DATE(a.created) >= '".$fromDate."' AND DATE(a.created) <= '".$toDate."' ORDER BY a.created DESC";

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
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        	
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    }

    public function getBalanceReport()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.title as role FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.title as role FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id where a.id > 0";

			if($keyword != '') {   
				$sql.=" AND ( b.title LIKE '".$keyword."%' ";    
				$sql.=" OR a.user_code LIKE '".$keyword."%'";
				$sql.=" OR a.name LIKE '".$keyword."%' )";
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

			
			$sql_summery = "SELECT a.*, SUM(a.wallet_balance) as totalWalletBalance,b.title as role FROM tbl_users as a INNER JOIN tbl_user_roles as b ON b.id = a.role_id  where a.role_id IN(3,4,5,6) and  a.account_id = '$account_id'";


			if($keyword != '') {   
				$sql_summery.=" AND ( b.title LIKE '".$keyword."%' ";    
				$sql_summery.=" OR a.user_code LIKE '".$keyword."%'";
				$sql_summery.=" OR a.name LIKE '".$keyword."%' )";
			}
			
			if($user_type != ''){
            
             $sql_summery.=" AND a.role_id = '$user_type'";	
            
            }

            $get_wallet_summery = $this->db->query($sql_summery)->row_array();

            $total_wallet_balance = isset($get_wallet_summery['totalWalletBalance']) ? $get_wallet_summery['totalWalletBalance'] : '0.00';
			

		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $this->User->get_account_name($list['account_id']);
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				$nestedData[] = $list['role'];
				$nestedData[] = $list['name'];
				$nestedData[] = number_format($list['wallet_balance'],2).' /-';
				
				
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
					
					);

		echo json_encode($json_data);  // send data as json format
	}

	public function bbpsHistory($status = 0){
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        	
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    }

    public function getBbpsHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name,b.user_code,c.title as service_name FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0";

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


            $sql_success_summery = "SELECT a.*,SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.status = 2";

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


	        $sql_pending_summery = "SELECT a.*,SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.status = 1";

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
	    	

	        $sql_failed_summery = "SELECT a.*,SUM(a.amount) as totalAmount,count(*) as totalRecord FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.id > 0 AND a.status = 3";

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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a><br />".$list['name'];
				$nestedData[] = $list['service_name'];
				$nestedData[] = $list['operator_code'];
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
				
				$nestedData[] = "<a href=".base_url('superadmin/report/bbpsLiveInvoice/').$list['recharge_display_id']." style='text-decoration:none;' target='_blank'>Invoice</a>";	
				
				if($list['status'] == 1) {
					$nestedData[] = '<font color="orange">Pending</font>';
					$nestedData[] = '<a href="'.base_url('superadmin/report/refundBbps').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this recharge?\')" class="btn btn-sm btn-primary">Refund</a> <a href="'.base_url('superadmin/report/successBbps').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to success this recharge?\')" class="btn btn-sm btn-primary">Success</a>';
				}
				elseif($list['status'] == 2) {
					$nestedData[] = '<font color="green">Success</font>';
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
						$nestedData[] = '<a href="'.base_url('superadmin/report/refundBbps').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to refund this recharge?\')" class="btn btn-sm btn-primary">Refund</a>';
					}
				}
				elseif($list['status'] == 3) {
					$nestedData[] = '<font color="red">Failed</font>';
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.name,b.user_code,c.title as service_name FROM tbl_bbps_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_bbps_service as c ON c.id = a.service_id WHERE a.recharge_display_id = '$id'";

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
        $this->parser->parse('superadmin/layout/column-2' , $data);
    
	
	}

	public function refundBbps($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('bbps_history',array('id'=>$recharge_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('superadmin/report/bbpsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->where_in('status',array(1,2))->get_where('bbps_history',array('id'=>$recharge_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('superadmin/report/bbpsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Bill Payment Already Refunded.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('bbps_history',array('id'=>$recharge_id))->row_array();
		
		$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
		$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;

		// update status
		$this->db->where('id',$recharge_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('bbps_history',array('status'=>4,'force_status'=>1));

		
		$get_before_balance = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();

		

    	$member_code = $get_before_balance['user_code'];    
    	$before_balance = $this->User->getMemberWalletBalanceSP($member_id,2); 
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


        $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
        if($is_cogent_instantpay_api)
        {
        	$admin_id = $this->User->get_admin_id($account_id);
            $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
            $admin_after_wallet_balance = $admin_before_wallet_balance + $amount;

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $admin_id,    
                'before_balance'      => $admin_before_wallet_balance,
                'amount'              => $amount,  
                'after_balance'       => $admin_after_wallet_balance,      
                'status'              => 1,
                'type'                => 1,   
                'wallet_type'         => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'BBPS Refund #'.$recharge_unique_id.' Amount Deducted.'
            );

            $this->db->insert('virtual_wallet',$wallet_data);

        }


		$this->Az->redirect('superadmin/report/bbpsHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Bill Payment refunded successfully.</div>');
	}

	public function successBbps($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('bbps_history',array('id'=>$recharge_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('superadmin/report/bbpsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		// check member
		$chkMember = $this->db->get_where('bbps_history',array('id'=>$recharge_id))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('superadmin/report/bbpsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Bill Payment Status Already Updated.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('bbps_history',array('id'=>$recharge_id))->row_array();
		
		$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
		$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;

		// update status
		$this->db->where('id',$recharge_id);
		$this->db->where('account_id',$account_id);
		$this->db->update('bbps_history',array('status'=>2,'force_status'=>2));

		
		$this->Az->redirect('superadmin/report/bbpsHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Bill Payment got success.</div>');
	}

	public function aepsKyc(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getAepsKycList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code FROM tbl_aeps_member_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,c.state as state_name,d.city_name FROM tbl_aeps_member_kyc as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_aeps_state as c ON c.id = a.state_id LEFT JOIN tbl_city as d ON d.city_id = a.city_id where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getAepsHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
				$nestedData[] = 'MemberID - '.$list['user_code'].'<br />Name - '.$list['user_name'];
				if($list['service'] == 'balinfo')
				{
					$service = 'Balance Info';
				}
				elseif($list['service'] == 'ministatement')
				{
					$service = 'Mini Statement';
				}
				elseif($list['service'] == 'balwithdraw')
				{
					$service = 'Account Withdrawal';
				}
				elseif($list['service'] == 'aadharpay')
				{
					$service = 'Aadhar Pay';
				}
				else
				{
					$service = 'Not Found';
				}
				$nestedData[] = $service.'<br />Aadhar - '.$list['aadhar_no'].'<br />Mobile - '.$list['mobile'];
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

				if($list['status'] == 3) {
					$nestedData[] = '<a href="'.base_url('superadmin/report/successAepsTxn').'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to success this transaction?\')" class="btn btn-sm btn-success">Success</a>';
				}
				else{
					$nestedData[] = 'Not Allowed';
				}

				$nestedData[] = "<a href=".base_url('superadmin/report/aepsInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";

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

	public function successAepsTxn($recharge_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// check member
		$chkMember = $this->db->get_where('member_aeps_transaction',array('id'=>$recharge_id,'status'=>3))->num_rows();
		if(!$chkMember)
		{
			$this->Az->redirect('superadmin/report/aepsHistory', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Transaction Status Already Updated.</div>');
		}

		// check recharge status
		$get_recharge_data = $this->db->get_where('member_aeps_transaction',array('id'=>$recharge_id))->row_array();
		
		$service_type = isset($get_recharge_data['service']) ? $get_recharge_data['service'] : '' ;
		$txnID = isset($get_recharge_data['txnID']) ? $get_recharge_data['txnID'] : '' ;
		$aadharNumber = isset($get_recharge_data['aadhar_no']) ? $get_recharge_data['aadhar_no'] : '' ;
		$iin = isset($get_recharge_data['iinno']) ? $get_recharge_data['iinno'] : '' ;
		$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
		$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
		$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
		$recordID = $recharge_id;
		
		// update status
		$this->db->where('id',$recharge_id);
		$this->db->update('member_aeps_transaction',array('status'=>2,'force_status'=>1));

		if($service_type == 'ministatement')
		{
			$this->User->forceAddStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID,$account_id,$member_id);
		}
		elseif($service_type == 'balwithdraw' || $service_type == 'aadharpay')
		{
			$this->User->forceAddBalance($txnID,$aadharNumber,$iin,$amount,$recordID,$service_type,$account_id,$member_id);
		}

		
		$this->Az->redirect('superadmin/report/aepsHistory', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Transaction got success.</div>');
	}



	public function getAepsData($recordID = 0)
	{
		$response = array();
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$chk_member = $this->db->get_where('member_aeps_transaction',array('id'=>$recordID))->num_rows();
 		if(!$chk_member)
 		{
 			$response = array(
 				'status' => 0,
 				'msg' => 'Something wrong ! Please try again.'
 			);
 			
 		}
 		else
 		{
 			$dmrData = $this->db->get_where('member_aeps_transaction',array('id'=>$recordID))->row_array();
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_member_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id = '$id'";


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
        $this->parser->parse('superadmin/layout/column-2' , $data);
    
	
	}



	public function walletDeductReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getWalletDeductList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
            'content_block' => 'report/upi-collection-list'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getUpiCollectionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as type,d.title as status_title FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON c.id = a.type_id  LEFT JOIN tbl_upi_transaction_status as d ON d.id = a.status where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as type,d.title as status_title FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON c.id = a.type_id  LEFT JOIN tbl_upi_transaction_status as d ON d.id = a.status where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['name'].'<br>('.$list['user_code'].')'."</a>";
				$nestedData[] = $list['type'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = isset($list['bank_rrno']) ? $list['bank_rrno'] : 'Not Available';
				$nestedData[] = $list['amount'].' /-';
				$nestedData[] = !empty($list['vpa_id']) ? $list['vpa_id'] : 'Not Available';
				$nestedData[] = !empty($list['description']) ? $list['description'] : 'Not Available';
				$nestedData[] = "<a href=".base_url('superadmin/report/upiTxnInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";	
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

	public function upiTxnInvoice($id = ''){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id = '$id'";


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
        $this->parser->parse('superadmin/layout/column-2' , $data);
    
	
	}


	//get  bebeficiary account change request


	public function changeAccountList(){
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        
       
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    }

    public function getAccountList()
    {   
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
            $sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_payout_user_request  as a INNER JOIN tbl_users as b ON b.id = a.user_id  WHERE a.id > 0 ";


            
            $totalData = $this->db->query($sql)->num_rows();
            $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
        
        
            $sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_payout_user_request as a INNER JOIN tbl_users as b ON b.id = a.user_id  WHERE a.id > 0";
            
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
                $nestedData[] = $this->User->get_account_name($list['account_id']);
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

                    $nestedData[] = '<a href="'.base_url('superadmin/report/approveRequest/'.$list['user_id']).'/'.$list['id'].'" onclick="return confirm(\'Are you sure you want to approve request?\')" title="Approve Request" class="btn btn-success btn-sm"><i class="fa fa-check" aria-hidden="true"></i></a>
                    <a href="'.base_url('superadmin/report/rejectRequest/'.$list['user_id']).'/'.$list['id'].'" title="Reject Request" onclick="return confirm(\'Are you sure you want to reject request?\')" class="btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i></a>';
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



     public function approveRequest($user_id = 0, $id=0){

        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $chk_request = $this->db->get_where('tbl_payout_user_request',array('account_id'=>$account_id,'user_id'=>$user_id,'id'=>$id))->row_array();


        

        if(!$chk_request){

            $this->Az->redirect('superadmin/report/changeAccountList', 'system_message_error',lang('MEMBER_ERROR'));

        }

       
        	  $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$user_id);
              $this->db->where('id',$id);
            $this->db->update('tbl_payout_user_request',array('status'=>2));
       
        	  $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$user_id);
            
            $this->db->update('payout_user_benificary',array('account_holder_name'=>$chk_request['account_holder_name'],'bank_name'=>$chk_request['bank_name'],'account_no'=>$chk_request['account_no'],'ifsc'=>$chk_request['ifsc']));

            $this->Az->redirect('superadmin/report/changeAccountList', 'system_message_error',lang('REQUEST_APPROVE_SUCCESS'));

        }



         public function rejectRequest($user_id = 0 , $id = 0){

        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $chk_request = $this->db->get_where('tbl_payout_user_request',array('account_id'=>$account_id,'user_id'=>$user_id,'id'=>$id))->row_array();
        

        if(!$chk_request){

            $this->Az->redirect('superadmin/report/changeAccountList', 'system_message_error',lang('MEMBER_ERROR'));

        }
       
       

             $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$user_id);
            $this->db->where('id',$id);
            $this->db->update('payout_user_request',array('status'=>3));

            $this->Az->redirect('superadmin/report/changeAccountList', 'system_message_error',lang('REQUEST_REJECT_SUCCESS'));

        }

    public function currentAccountReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
            'content_block' => 'report/current-account-report'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getCurrentAccountList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_current_account_list as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_current_account_list as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getCashDepositeList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
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
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_cash_deposite_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.status > 1";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code,b.name FROM tbl_cash_deposite_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0 AND a.status > 1";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.mobile LIKE '%".$keyword."%'";
				$sql.=" OR a.account_no LIKE '%".$keyword."%'";
				$sql.=" OR a.amount LIKE '%".$keyword."%'";
				$sql.=" OR a.remark LIKE '%".$keyword."%'";
				$sql.=" OR a.bank_rrn LIKE '%".$keyword."%'";
				$sql.=" OR a.txnid LIKE '%".$keyword."%' )";
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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

				$nestedData[] = "<a href=".base_url('superadmin/report/cashDepositeInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";

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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code,b.name FROM tbl_cash_deposite_history as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id = '$id'";


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
        $this->parser->parse('superadmin/layout/column-2' , $data);
    
	
	}

	public function moneyTransferCommision(){

		//get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getMoneyTransferCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,ac.title as account_name FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id INNER JOIN tbl_account as ac ON ac.id = a.account_id where a.id > 0";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR ac.title LIKE '%".$keyword."%'";
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
				$nestedData[] = $list['account_name'];
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getOpenPayoutCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,ac.title as account_name FROM tbl_user_money_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id INNER JOIN tbl_account as ac ON ac.id = a.account_id where a.id > 0";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR ac.title LIKE '%".$keyword."%'";
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
				$nestedData[] = $list['account_name'];
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getAepsCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_member_aeps_transaction as c ON c.id = a.record_id where a.id > 0 AND a.type = 'AEPS'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnID,c.amount,c.service FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_member_aeps_transaction as c ON c.id = a.record_id where a.id > 0 AND a.type = 'AEPS'";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getMyAepsCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code FROM tbl_member_aeps_comm as a INNER JOIN tbl_users as b ON b.id = a.created_by where a.account_id = '$account_id'";
			
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

	public function cashDepositeCommision(){

		//get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getCashDepositeCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_cash_deposite_history as c ON c.id = a.record_id where a.id > 0 AND a.type = 'CD'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_cash_deposite_history as c ON c.id = a.record_id where a.id > 0 AND a.type = 'CD'";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getUpiCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.id > 0 AND a.type = 'UPI'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.id > 0 AND a.type = 'UPI'";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
            'content_block' => 'report/upi-cash-list'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getUpiCashList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as type,d.title as status_title FROM tbl_upi_cash_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON c.id = a.type_id  LEFT JOIN tbl_upi_transaction_status as d ON d.id = a.status where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,c.title as type,d.title as status_title FROM tbl_upi_cash_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id LEFT JOIN tbl_upi_transaction_type as c ON c.id = a.type_id  LEFT JOIN tbl_upi_transaction_status as d ON d.id = a.status where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
				$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['name'].'<br>('.$list['user_code'].')'."</a>";
				$nestedData[] = $list['type'];
				$nestedData[] = $list['txnid'];
				$nestedData[] = isset($list['bank_rrno']) ? $list['bank_rrno'] : 'Not Available';
				$nestedData[] = $list['amount'].' /-';
				$nestedData[] = !empty($list['vpa_id']) ? $list['vpa_id'] : 'Not Available';
				$nestedData[] = !empty($list['description']) ? $list['description'] : 'Not Available';
				$nestedData[] = "<a href=".base_url('superadmin/report/upiCashTxnInvoice/').$list['id']." style='text-decoration:none;' target='_blank'>Invoice</a>";
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$accountData = $this->User->get_account_data($account_id);

		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		$sql = "SELECT a.*,b.user_code,b.name FROM tbl_upi_cash_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id = '$id'";


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
        $this->parser->parse('superadmin/layout/column-2' , $data);
    
	
	}	

	public function upiCashCommision(){

		//get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getUpiCashCommisionList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.id > 0 AND a.type = 'UPICASH'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.name as member_name,b.user_code,c.txnid,c.amount FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_upi_transaction as c ON c.id = a.record_id where a.id > 0 AND a.type = 'UPICASH'";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getTopupHistory()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_gateway_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_member_gateway_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getDmtHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.* FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_dmt_activation as c ON c.id = a.from_sender_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,c.name as sender_name,c.mobile as sender_mobile FROM tbl_user_dmt_transfer as a INNER JOIN tbl_users as b ON b.id = a.user_id LEFT JOIN tbl_user_dmt_activation as c ON c.id = a.from_sender_id where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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

	public function virtualHistory(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
            'content_block' => 'report/virtualHistory'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getVirtualHistory()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
	 	$loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_virtual_txn_history as a LEFT JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_virtual_txn_history as a LEFT JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.virtual_account_no LIKE '%".$keyword."%' ";
				$sql.=" OR a.utr LIKE '%".$keyword."%' ";
				$sql.=" OR a.client_account_no LIKE '%".$keyword."%' ";
				$sql.=" OR a.amount LIKE '%".$keyword."%' ";
				$sql.=" OR a.payer_account_no LIKE '%".$keyword."%' ";
				$sql.=" OR a.payer_bank_ifsc LIKE '%".$keyword."%' ";
				$sql.=" OR a.customer_code LIKE '%".$keyword."%' ";
				$sql.=" OR a.mode LIKE '%".$keyword."%' ";
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
				if($list['member_id'] == 0)
				{
					$nestedData[] = 'Not Found';
				}
				else
				{
					$nestedData[] = $this->User->get_account_name($list['account_id']);
				}
				if($list['member_id'] == 0)
				{
					$nestedData[] = 'Not Found';
				}
				else
				{
					$nestedData[] = "<a href='javascript:void(0)' style='text-decoration:none;'>".$list['user_code']."</a>";
				}
				
				$nestedData[] = $list['customer_code'];
				$nestedData[] = $list['virtual_account_no'];
				$nestedData[] = $list['mode'];
				$nestedData[] = $list['utr'];
				$nestedData[] = $list['client_account_no'];
				$nestedData[] = '&#8377; '.$list['amount'];
				$nestedData[] = 'Name - '.$list['payer_name'].'<br />A/c No. - '.$list['payer_account_no'].'<br /> IFSC - '.$list['payer_bank_ifsc'];
				
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

	public function utiPancardReport(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
            'content_block' => 'report/uti-pancard-list'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getUtiPancardList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_uti_pancard_coupon as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_uti_pancard_coupon as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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

	public function nsdlList(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
            'content_block' => 'report/nsdl-pancard-list'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getNsdlList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,ac.title as account_name FROM tbl_nsdl_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_account as ac ON ac.id = a.account_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name,ac.title as account_name FROM tbl_nsdl_history as a INNER JOIN tbl_users as b ON b.id = a.member_id INNER JOIN tbl_account as ac ON ac.id = a.account_id where a.id > 0";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR a.txnid LIKE '%".$keyword."%' ";
				$sql.=" OR a.type LIKE '%".$keyword."%' ";
				$sql.=" OR a.order_id LIKE '%".$keyword."%' ";
				$sql.=" OR a.psacode LIKE '%".$keyword."%' ";
				$sql.=" OR a.pan_name LIKE '%".$keyword."%' ";
				$sql.=" OR a.mobile LIKE '%".$keyword."%' ";
				$sql.=" OR a.email LIKE '%".$keyword."%' ";
				$sql.=" OR ac.title LIKE '%".$keyword."%' ";
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
				$nestedData[] = $list['account_name'];
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

	public function moveMemberReport(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getMoveMemberList()
	{	
		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
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
			4 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_move_member_history as a INNER JOIN tbl_users as b ON b.id = a.move_member_id INNER JOIN tbl_users as c ON c.id = a.last_sponser_id INNER JOIN tbl_users as d ON d.id = a.new_sponser_id INNER JOIN tbl_account as ac ON ac.id = a.account_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code,b.name,c.user_code as last_sponser_code,c.name as last_sponser_name,d.user_code as new_sponser_code,d.name as new_sponser_name,ac.title as account_name FROM tbl_move_member_history as a INNER JOIN tbl_users as b ON b.id = a.move_member_id INNER JOIN tbl_users as c ON c.id = a.last_sponser_id INNER JOIN tbl_users as d ON d.id = a.new_sponser_id INNER JOIN tbl_account as ac ON ac.id = a.account_id where a.id > 0";
			
			if($keyword != '') {   
				$sql.=" AND ( b.name LIKE '%".$keyword."%' ";    
				$sql.=" OR b.user_code LIKE '%".$keyword."%'";
				$sql.=" OR c.user_code LIKE '%".$keyword."%'";
				$sql.=" OR c.name LIKE '%".$keyword."%'";
				$sql.=" OR d.name LIKE '%".$keyword."%'";
				$sql.=" OR d.user_code LIKE '%".$keyword."%' )";
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
				$nestedData[] = $list['account_name'];
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

	public function dmtKycExport(){

		$account_id = $this->User->get_domain_account();
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
            'content_block' => 'report/dmt-kyc-export'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getDmtKycExportList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$account_id = 0;
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$account_id = isset($filterData[0]) ? trim($filterData[0]) : 0;
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_users as a where a.id > 0 AND role_id IN (3,4,5) AND aeps_status = 1";
			if($account_id)
			{
				$sql.=" AND a.account_id = '".$account_id."'";    
			}
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_users as a where a.id > 0 AND role_id IN (3,4,5) AND aeps_status = 1";
			if($account_id)
			{
				$sql.=" AND a.account_id = '".$account_id."'";    
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
				$nestedData[] = $list['user_code'];
				$nestedData[] = $list['name'];
				$nestedData[] = $list['mobile'];
				if($list['dmt_agent_id_status'] == 0) {
					$nestedData[] = '<font color="red">Not Generated</font>';
				}
				elseif($list['dmt_agent_id_status'] == 1) {
					$nestedData[] = '<font color="green">Approved</font>';
				}
				$nestedData[] = $list['dmt_agent_id'];
				
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

	public function dmtExportAuth()
	{
		$post = $this->input->post();
		$account_id = 0;
		if($post['member_id'] != '')
		{
			$account_id = $post['member_id'];
		}

		$exportAll = 0;
		if(isset($post['exportAll']))
		{
			$exportAll = 1;
		}

		if($exportAll)
		{
			$sql = "SELECT a.* FROM tbl_users as a where a.id > 0 AND role_id IN (3,4,5) AND aeps_status = 1";
			if($account_id)
			{
				$sql.=" AND a.account_id = '".$account_id."'";    
			}
		}
		else
		{
			$sql = "SELECT a.* FROM tbl_users as a where a.id > 0 AND role_id IN (3,4,5) AND dmt_agent_id_status = 0 AND aeps_status = 1";
			if($account_id)
			{
				$sql.=" AND a.account_id = '".$account_id."'";    
			}
		}

		$recordList = $this->db->query($sql)->result_array();
		$recordData = array();
		if($recordList)
		{
			foreach ($recordList as $key => $list) {

				// get aeps kyc detail
				$kycData = $this->db->select('aeps_member_kyc.*,city.city_name,city.state_name')->join('city','city.city_id = aeps_member_kyc.city_id')->get_where('aeps_member_kyc',array('account_id'=>$list['account_id'],'member_id'=>$list['id'],'status'=>1))->row_array();
				$shop_name = isset($kycData['shop_name']) ? $kycData['shop_name'] : '';
				$shop_address = isset($kycData['address']) ? $kycData['address'] : '';
				$city_name = isset($kycData['city_name']) ? $kycData['city_name'] : '';
				$state_name = isset($kycData['state_name']) ? $kycData['state_name'] : '';
				$pin_code = isset($kycData['pin_code']) ? $kycData['pin_code'] : '';
				$contact_no = $list['mobile'];
				$agent_code = $list['user_code'];

				$geo = 'Latitude'.$list['aeps_lat'].':Longitude'.$list['aeps_lng'];
				$recordData[$key] = array('',$list['name'],$geo,$shop_name,$contact_no,$shop_address,'','','','','',$city_name,$pin_code,$state_name,$agent_code);
			}
		}
		if($account_id)
		{
			$accountName = $this->User->get_account_name($account_id);
			$accountName = str_replace(' ', '-', $accountName);
			$file_name = $accountName.'-DMT-KYC-'.date('d-m-Y');
		}
		else
		{
			$file_name = 'DMT-ALL-ACCOUNT-KYC-'.date('d-m-Y');
		}
		$spreadsheet = Helper::newSpreadsheet()
			->addRow(['NA','Agent Name','Geo Code','Agent Shop Name','Contact Number','Shop Address','Flat /Floor / Building','Shop Number','Premise / Area Name','Colony / Street / Locality','Landmark','City Name','Area Pin Code','State','Agent Code / Account ID'])
			->addRows($recordData);
			
			Helper::output($file_name);
		
	}

	public function matmHistory(){

		//get logged user info
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getMatmHistoryList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_matm_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code,b.name as user_name FROM tbl_matm_history as a INNER JOIN tbl_users as b ON b.id = a.member_id  where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
            'content_block' => 'report/axis-account-report'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getAxisAccountList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
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
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_axis_account_api_response as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_axis_account_api_response as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.id > 0";
			
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
				$nestedData[] = $this->User->get_account_name($list['account_id']);
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

	public function dmtKycImport(){

		$account_id = $this->User->get_domain_account();
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
            'content_block' => 'report/dmt-kyc-import'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getDmtKycImportList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$account_id = 0;
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$account_id = isset($filterData[0]) ? trim($filterData[0]) : 0;
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_dmt_import_file as a where a.id > 0";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_dmt_import_file as a where a.id > 0";
			
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
				$nestedData[] = '<a href="'.base_url('superadmin/report/importFileDetail/'.$list['id']).'">'.$list['fileName'].'</a>';
				$nestedData[] = $list['totalRecord'];
				$nestedData[] = $list['matchRecord'];
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
					);

		echo json_encode($json_data);  // send data as json format
	}

	public function dmtImportFile(){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
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
            'content_block' => 'report/dmt-kyc-import-file'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function importFileAuth()
	{

		// check mobile already exits or not
		if($_FILES['profile']['name'] == ''){

			$this->Az->redirect('superadmin/report/dmtImportFile', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Please select file.</div>');	

		}

		$filePath = '';
		$fileName = '';
		if($_FILES['profile']['name'])
		{
			//generate icon name randomly
			$fileName = time().rand(1111,9999);
			$config['upload_path'] = './media/dmt/';
			$config['allowed_types'] = 'csv';
			$config['file_name'] 		= $fileName;

			$this->load->library('upload', $config);
			$this->upload->do_upload('profile');
			$uploadError = $this->upload->display_errors();
			if($uploadError){
				$this->Az->redirect('superadmin/report/dmtImportFile', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
			}
			else
			{
				$fileData = $this->upload->data();
				$fileName = $fileData['file_name'];
				//get uploaded file path
				$filePath = substr($config['upload_path'] . $fileData['file_name'], 2);

			}

		}

		$upload_full_path = DMT_IMPORT_FILE_PATH.$filePath;
		$fileData = Helper::newSpreadsheet($upload_full_path)->getRows();
		
		if(isset($fileData[0]))
		{
			unset($fileData[0]);
		}

		if($fileData)
		{
			// save file
			$saveFileData = array(
				'fileName' => $fileName,
				'filePath' => $filePath,
				'totalRecord' => count($fileData),
				'created' => date('Y-m-d H:i:s')
			);
			$this->db->insert('dmt_import_file',$saveFileData);
			$file_id = $this->db->insert_id();
			$total_match_record = 0;
			foreach($fileData as $list)
			{
				$agent_id = $list[0];
				$agent_name = $list[7];
				$agent_mobile = $list[8];

				$sys_msg = '';
				$user_id = 0;
				$account_id = 0;
				$is_match = 0;

				// check mobile no
				$chkMobile = $this->db->get_where('users',array('mobile'=>$agent_mobile))->num_rows();
				if($chkMobile)
				{
					$chkDmtStatus = $this->db->get_where('users',array('mobile'=>$agent_mobile,'dmt_agent_id_status'=>0))->num_rows();
					if($chkDmtStatus)
					{
						$getUserData = $this->db->get_where('users',array('mobile'=>$agent_mobile,'dmt_agent_id_status'=>0))->row_array();
						$user_id = $getUserData['id'];
						$account_id = $getUserData['account_id'];

						$this->db->where('id',$user_id);
						$this->db->update('users',array('dmt_agent_id_status'=>1,'dmt_agent_id'=>$agent_id));

						$sys_msg = 'Agent ID Updated Successfully';
						$total_match_record++;
						$is_match = 1;
					}
					else
					{
						$getUserData = $this->db->get_where('users',array('mobile'=>$agent_mobile))->row_array();
						$user_id = $getUserData['id'];
						$account_id = $getUserData['account_id'];

						$sys_msg = 'Agent ID Already Updated';
					}
				}
				else
				{
					$sys_msg = 'Agent Mobile No. Not Exits';
				}
				$saveFileData = array(
					'file_id' => $file_id,
					'agent_id' => $agent_id,
					'agent_name' => $agent_name,
					'is_match' => $is_match,
					'match_user_id' => $user_id,
					'account_id' => $account_id,
					'agent_mobile' => $agent_mobile,
					'sys_msg' => $sys_msg,
					'created' => date('Y-m-d H:i:s')
				);
				$this->db->insert('dmt_import_file_data',$saveFileData);

				$this->db->where('id',$file_id);
				$this->db->update('dmt_import_file',array('matchRecord'=>$total_match_record));

			}
			$this->Az->redirect('superadmin/report/dmtKycImport', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Records were imported successfully.</div>');
		}
		else
		{
			$this->Az->redirect('superadmin/report/dmtImportFile', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! No Record Found.</div>');
		}
	}

	public function importFileDetail($file_id = 0){

		$account_id = $this->User->get_domain_account();
	 	$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		
  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'file_id' => $file_id,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'report/dmt-kyc-import-detail'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}

	public function getDmtKycImportFileDataList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$file_id = 0;
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$file_id = isset($filterData[0]) ? trim($filterData[0]) : 0;
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'a.created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_dmt_import_file_data as a where a.file_id = '$file_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.user_code,b.name,ac.title as accountName FROM tbl_dmt_import_file_data as a LEFT JOIN tbl_users as b ON b.id = a.match_user_id LEFT JOIN tbl_account as ac ON ac.id = a.account_id where a.file_id = '$file_id'";
			
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
				$nestedData[] = $list['agent_id'];
				$nestedData[] = $list['agent_name'];
				$nestedData[] = $list['agent_mobile'];
				if($list['is_match'] == 1)
				{
					$nestedData[] = '<font color="green">Yes</font>';
				}
				else
				{
					$nestedData[] = '<font color="red">No</font>';
				}
				if($list['match_user_id'])
				{
					$nestedData[] = $list['user_code'].'<br />'.$list['name'];
				}
				else
				{
					$nestedData[] = 'Not Match';
				}
				if($list['account_id'])
				{
					$nestedData[] = $list['accountName'];
				}
				else
				{
					$nestedData[] = 'Not Match';
				}
				$nestedData[] = $list['sys_msg'];
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
					);

		echo json_encode($json_data);  // send data as json format
	}

    
}