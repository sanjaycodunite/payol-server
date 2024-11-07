<?php 
class Member extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkRetailerPermission();
        $this->load->model('retailer/Member_model');		
        $this->lang->load('retailer/member', 'english');
        
    }

	
	public function userList(){

		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		
		
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
            'content_block' => 'member/userList'
        );
        $this->parser->parse('retailer/layout/column-1' , $data);
    
	
	}


	public function getUserList()
	{	
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$account_id = $this->User->get_domain_account();
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
			1 => 'user_code',
			2 => 'name',
			4 => 'wallet_balance',
			5 => 'created',
		);
		
		
		
			// getting total number records without any search
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (8) AND a.account_id = '$account_id' AND a.created_by = '$loggedAccountID'";
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.*,b.package_name FROM tbl_users as a LEFT JOIN tbl_package as b ON b.id = a.package_id where a.role_id IN (8) AND a.account_id = '$account_id' AND a.created_by = '$loggedAccountID'";

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
				
				
				$str = '<table class="table">';
				$str.='<tr><td><b>Name </b></td><td>'.$list['name'].'</td></tr>';
				$str.='<tr><td><b>Email </b></td><td>'.$list['email'].'</td></tr>';
				$str.='<tr><td><b>Mobile </b></td><td>'.$list['mobile'].'</td></tr>';
				if($list['package_name']){
					$str.='<tr><td><b>Pacakge </b></td><td>'.$list['package_name'].'</td></tr>';
				}
				else
				{
					$str.='<tr><td><b>Pacakge </b></td><td>N/A</td></tr>';
				}
				$str.='</table>';
				$nestedData[] = $str;


				$str = '<table class="table">';
				if($list['state_id'])
				{
					$get_state_name = $stateList = $this->db->get_where('states',array('id'=>$list['state_id']))->row_array();

					$str.='<tr><td><b>State </b></td><td>'.$get_state_name['name'].'</td></tr>';
				}

				$str.='<tr><td><b>District </b></td><td>'.$list['district'].'</td></tr>';
				$str.='<tr><td><b>Block </b></td><td>'.$list['block'].'</td></tr>';
				$str.='<tr><td><b>Village </b></td><td>'.$list['village'].'</td></tr>';
				$str.='<tr><td><b>Aadhar No  </b></td><td>'.$list['aadhar_no'].'</td></tr>';
				$str.='<tr><td><b>Pan No </b></td><td>'.$list['pan_no'].'</td></tr>';
				
				$str.='<tr><td><b>Pincode </b></td><td>'.$list['pincode'].'</td></tr>';
				$str.='<tr><td><b>Address </b></td><td>'.$list['address'].'</td></tr>';
				
				$str.='</table>';
				$nestedData[] = $str;

				$nestedData[]=$this->User->getMemberWalletBalanceSP($list['id']).' /- ';

				$nestedData[] = date('d-M-Y',strtotime($list['created']));
				if($list['is_active'] == 1) {
					$nestedData[] = '<font color="green">Active</font>';
				}
				elseif($list['is_active'] == 0) {
					$nestedData[] = '<font color="red">Deactive</font>';
				}
				
				/*$nestedData[] ='<a title="edit" class="btn btn-primary btn-sm" href="'.base_url('master/member/editMember').'/'.$list['id'].'"><i class="fa fa-edit" aria-hidden="true"></i></a>';*/

				$nestedData[] = '<font color="red">Not Allowed</font>';
				
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
	
	// add member
	public function addMember()
    {
    	$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);

		// get role list
		$roleList = $this->db->where_in('id',array(8))->get('user_roles')->result_array();

		// get country list
		$countryList = $this->db->order_by('name','asc')->get('countries')->result_array();

		$stateList = $this->db->order_by('name','asc')->get_where('states',array('country_code_char2'=>'IN'))->result_array();


   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'member/addMember',
            'roleList' => $roleList,
            'countryList' => $countryList,
            'stateList' => $stateList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('retailer/layout/column-1', $data);
		
    }

    // save member
	public function saveMember()
	{
		$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		//check for foem validation
		$post = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('role_id', 'Member Type', 'required|xss_clean');
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email ', 'xss_clean|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]');
        
        $this->form_validation->set_rules('country_id', 'Country', 'required|xss_clean');
        $this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');
        $this->form_validation->set_rules('city', 'City', 'required|xss_clean');
        $this->form_validation->set_rules('district', 'District', 'required|xss_clean');
        $this->form_validation->set_rules('block', 'Block', 'required|xss_clean');
        $this->form_validation->set_rules('village', 'Village', 'required|xss_clean');
        $this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
        $this->form_validation->set_rules('pincode', 'Pincode', 'required|xss_clean');
        $this->form_validation->set_rules('aadhar_no', 'Aadhar No', 'required|xss_clean');
        $this->form_validation->set_rules('pan_no', 'Pan No', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
			
			$this->addMember();
		}
		else
		{	
			
			if($post['role_id'] == 1 || $post['role_id'] == 2 || $post['role_id'] == 3 || $post['role_id'] == 4 || $post['role_id'] == 5 || $post['role_id'] == 6  || $post['role_id'] == 7){

				$this->Az->redirect('retailer/member/addMember', 'system_message_error',lang('ROLE_ERROR'));	

			}

			// check mobile already exits or not
			$chk_user_mobile = $this->db->get_where('users',array('account_id'=>$account_id,'mobile'=>$post['mobile']))->num_rows();
			if($chk_user_mobile){

				$this->Az->redirect('retailer/member/addMember', 'system_message_error',lang('MOBILE_ERROR'));	

			}


			$status = $this->Member_model->saveMember($post);
			
			if($status == true)
			{
				$this->Az->redirect('retailer/member/addMember', 'system_message_error',lang('MEMBER_SAVED'));
			}
			else
			{
				$this->Az->redirect('retailer/member/addMember', 'system_message_error',lang('MEMBER_ERROR'));
			}
			
		}
	
	}

	
	
}