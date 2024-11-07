<?php 
class Vanwallet extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkAdminPermission();
        
    }

	public function walletList(){

		$account_id = $this->User->get_domain_account();
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
            'content_block' => 'vanwallet/walletList'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}


	public function getWalletList()
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
			0 => 'id',	
			1 => 'user_code',
			2 => 'name',
			6 => 'id',
			7 => 'type',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.* FROM tbl_virtual_wallet as a where a.wallet_type = 1 AND a.account_id = '$account_id'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_virtual_wallet as a where a.wallet_type = 1 AND a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( a.description LIKE '%".$keyword."%' )";    
			}

			if($date != '') {   
				$sql.=" AND  DATE(a.created) = '".$date."' ";    
			}
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 1 : $requestData['order'][0]['column'] : 1;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY a.id DESC  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
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

	public function accountDetail(){

		$account_id = $this->User->get_domain_account();
		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		
		// get member mobile
		$get_member_mobile = $this->db->select('is_virtual_account,virtual_account_no,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();
		$is_virtual_account = isset($get_member_mobile['is_virtual_account']) ? $get_member_mobile['is_virtual_account'] : 0;
		$virtual_account_no = isset($get_member_mobile['virtual_account_no']) ? $get_member_mobile['virtual_account_no'] : 0;
		$member_mobile = isset($get_member_mobile['mobile']) ? $get_member_mobile['mobile'] : '';

		// get virtual account code
		$virtualData = $this->User->get_account_data($account_id);
		$virtual_prefix = isset($virtualData['van_prefix']) ? $virtualData['van_prefix'] : '';
		$virtual_ifsc = VIRTUAL_ACCOUNT_IFSC;

		if(!$is_virtual_account)
		{

			$is_virtual_account = 1;
			$virtual_account_no = VIRTUAL_ACCOUNT_CODE.$member_mobile;
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
            'content_block' => 'vanwallet/account-detail'
        );
        $this->parser->parse('admin/layout/column-1' , $data);
    
	
	}

	public function upgradeAccountAuth(){

		$account_id = $this->User->get_domain_account();
		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

		
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
		$virtual_account_no = VIRTUAL_ACCOUNT_CODE.$member_mobile;
		$this->db->where('id',$loggedAccountID);
		$this->db->where('account_id',$account_id);
		$this->db->update('users',array('is_virtual_account'=>1,'virtual_account_no'=>$virtual_account_no));
		$msg = '<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Virtual account details updated successfully.</div>';
		    $this->Az->redirect('admin/vanwallet/accountDetail', 'system_message_error',$msg);
		
    
	
	}

	
	
}