<?php 
class Cwallet extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkPermission();
        $this->load->model('superadmin/Ewallet_model');		
        $this->lang->load('superadmin/ewallet', 'english');
        
    }

	public function walletList(){

		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		
		// get users list
		$memberList = $this->db->where_in('role_id',array(2))->get('users')->result_array();

  		$siteUrl = base_url();		

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'memberList' => $memberList,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'cwallet/walletList'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function getWalletList()
	{	
		
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	$member_id = '';
	   	$date = '';
		if($extra_search)
		{
			$filterData = explode('|',$extra_search);
			$keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
			$member_id = isset($filterData[1]) ? trim($filterData[1]) : '';
			$date = isset($filterData[2]) ? trim($filterData[2]) : '';
		}
		
		$columns = array( 
		// datatable column index  => database column name
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'id',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_collection_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where b.role_id = 2 AND  a.wallet_type = 1";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*, b.user_code as user_code, b.name as name FROM tbl_collection_wallet as a INNER JOIN tbl_users as b ON b.id = a.member_id  where b.role_id = 2 AND  a.wallet_type = 1";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '".$keyword."%' ";    
				$sql.=" OR b.name LIKE '".$keyword."%' ";
				$sql.=" OR a.description LIKE '%".$keyword."%' )";
			}

			if($member_id != '') {   
				$sql.=" AND  b.id = $member_id ";    
			}

			if($date != '') {   
				$sql.=" AND  DATE(a.created) = '".$date."' ";    
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
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					);

		echo json_encode($json_data);  // send data as json format
	}


    // save member
	public function saveWallet()
	{
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('member', 'Member', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->addWallet();
		}
		else
		{	
			// check member is valid or not
			$chk_member = $this->db->get_where('users',array('id'=>$post['member'],'role_id'=>2))->num_rows();
			if(!$chk_member)
			{
				$this->Az->redirect('superadmin/ewallet/walletList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
			$status = $this->Ewallet_model->saveWallet($post);
			
			if($status == true)
			{
				$this->Az->redirect('superadmin/ewallet/walletList', 'system_message_error',lang('WALLET_SAVED'));
			}
			else
			{
				$this->Az->redirect('superadmin/ewallet/walletList', 'system_message_error',lang('WALLET_ERROR'));
			}
			
		}
	
	}
	
	public function getMemberWalletBalance($memberID = 0)
	{
		$admin_id = $this->User->get_admin_id($memberID);
		
		echo json_encode(array(
			'status' => 1,
			'balance' => $this->User->getMemberCollectionWalletBalanceSP($admin_id)
		));
	}

	

	public function accountWalletList(){

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
            'content_block' => 'cwallet/accountWiseBalance'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function walletTransfer(){

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
            'content_block' => 'cwallet/walletTransfer'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function walletTransferAuth()
	{
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('account_id', 'Account', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->walletTransfer();
		}
		else
		{	
			$account_id = $post['account_id'];
			$admin_id = $this->User->get_admin_id($post['account_id']);
			// get member available balance
			
			$before_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

			if($before_balance < $post['amount'] || $post['amount'] < 0){

				$this->Az->redirect('superadmin/cwallet/walletTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Insufficient balance in account.</div>');
			}

			$amount = isset($post['amount']) ? $post['amount'] : 0;

			//get member wallet_balance
	        
	        $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

	        $after_wallet_balance = $before_wallet_balance - $amount;

	        $wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $admin_id,    
	            'before_balance'      => $before_wallet_balance,
	            'amount'              => $amount,  
	            'after_balance'       => $after_wallet_balance,      
	            'status'              => 1,
	            'type'                => 2,   
	            'wallet_type'         => 1,   
	            'created'             => date('Y-m-d H:i:s'),      
	            'description'         => $post['description']
	        );

	        $this->db->insert('collection_wallet',$wallet_data);

	        
	        //get member wallet_balance
	        
	        $before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

	        $after_balance = $before_wallet_balance + $amount;

	        $wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $admin_id,    
	            'before_balance'      => $before_wallet_balance,
	            'amount'              => $amount, 
	            'after_balance'       => $after_balance,      
	            'status'              => 1,
	            'type'                => 1,      
	            'wallet_type'         => 1,
	            'created'             => date('Y-m-d H:i:s'),      
	            'credited_by'         => 1,
	            'description'         => 'S-Wallet Settlement Credited.'
            );

            $this->db->insert('virtual_wallet',$wallet_data);

            
			$this->Az->redirect('superadmin/cwallet/walletList', 'system_message_error','<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Amount transfered to Virtual Wallet Successfully.</div>');
		}
	
	}

	public function mainWalletTransfer(){

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
            'content_block' => 'cwallet/mainWalletTransfer'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function mainWalletTransferAuth()
	{
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('account_id', 'Account', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->mainWalletTransfer();
		}
		else
		{	
			$account_id = $post['account_id'];
			$admin_id = $this->User->get_admin_id($post['account_id']);
			// get member available balance
			
			$before_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

			if($before_balance < $post['amount'] || $post['amount'] < 0){

				$this->Az->redirect('superadmin/cwallet/mainWalletTransfer', 'system_message_error','<div class="alert alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Insufficient balance in account.</div>');
			}

			$amount = isset($post['amount']) ? $post['amount'] : 0;

			//get member wallet_balance
	        $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

	        $after_wallet_balance = $before_wallet_balance - $amount;

	        $wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $admin_id,    
	            'before_balance'      => $before_wallet_balance,
	            'amount'              => $amount,  
	            'after_balance'       => $after_wallet_balance,      
	            'status'              => 1,
	            'type'                => 2,   
	            'wallet_type'         => 1,   
	            'created'             => date('Y-m-d H:i:s'),      
	            'description'         => $post['description']
	        );

	        $this->db->insert('collection_wallet',$wallet_data);

	        
	        //get member wallet_balance
	        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);

	        $after_balance = $before_wallet_balance + $amount;

	        $wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $admin_id,    
	            'before_balance'      => $before_wallet_balance,
	            'amount'              => $amount, 
	            'after_balance'       => $after_balance,      
	            'status'              => 1,
	            'type'                => 1,      
	            'wallet_type'         => 1,
	            'created'             => date('Y-m-d H:i:s'),      
	            'credited_by'         => 1,
	            'description'         => 'C-Wallet Settlement Credited.'
            );

            $this->db->insert('member_wallet',$wallet_data);

			$this->Az->redirect('superadmin/cwallet/walletList', 'system_message_error','<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Amount transfered to Main Wallet Successfully.</div>');
		}
	
	}


	public function debitWallet(){

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
            'content_block' => 'cwallet/debitWallet'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function debitWalletAuth()
	{
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('account_id', 'Account', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->debitWallet();
		}
		else
		{	
			$account_id = $post['account_id'];
			$admin_id = $this->User->get_admin_id($post['account_id']);
			// get member available balance
			$before_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

			if($before_balance < $post['amount'] || $post['amount'] < 0){

				$this->Az->redirect('superadmin/cwallet/debitWallet', 'system_message_error','<div class="alert alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Insufficient balance in account.</div>');
			}

			$amount = isset($post['amount']) ? $post['amount'] : 0;

			//get member wallet_balance
	        $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

	        $after_wallet_balance = $before_wallet_balance - $amount;

	        $wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $admin_id,    
	            'before_balance'      => $before_wallet_balance,
	            'amount'              => $amount,  
	            'after_balance'       => $after_wallet_balance,      
	            'status'              => 1,
	            'type'                => 2,   
	            'wallet_type'         => 1,   
	            'created'             => date('Y-m-d H:i:s'),      
	            'description'         => $post['description'],
	            'is_manual' => 1
	        );

	        $this->db->insert('collection_wallet',$wallet_data);

			$this->Az->redirect('superadmin/cwallet/walletList', 'system_message_error','<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Amount debited from C-Wallet Successfully.</div>');
		}
	
	}

	public function creditWallet(){

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
            'content_block' => 'cwallet/creditWallet'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    
	
	}


	public function creditWalletAuth()
	{
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('account_id', 'Account', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount ', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->creditWallet();
		}
		else
		{	
			$account_id = $post['account_id'];
			$admin_id = $this->User->get_admin_id($post['account_id']);
			
			if($post['amount'] < 0){

				$this->Az->redirect('superadmin/cwallet/creditWallet', 'system_message_error','<div class="alert alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Amount should be grater than 0.</div>');
			}

			$amount = isset($post['amount']) ? $post['amount'] : 0;

			//get member wallet_balance
	        $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

	        $after_wallet_balance = $before_wallet_balance + $amount;

	        $wallet_data = array(
	            'account_id'          => $account_id,
	            'member_id'           => $admin_id,    
	            'before_balance'      => $before_wallet_balance,
	            'amount'              => $amount,  
	            'after_balance'       => $after_wallet_balance,      
	            'status'              => 1,
	            'type'                => 1,   
	            'wallet_type'         => 1,   
	            'created'             => date('Y-m-d H:i:s'),      
	            'description'         => $post['description'],
	            'is_manual' => 1
	        );

	        $this->db->insert('collection_wallet',$wallet_data);

	        
			$this->Az->redirect('superadmin/cwallet/walletList', 'system_message_error','<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Amount credited to S-Wallet Successfully.</div>');
		}
	
	}
	
	
	
}