<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Dashboard extends CI_Controller{

    public function __construct() {
        parent::__construct();
        //load language
		$this->User->checkPermission();
		$this->load->model('superadmin/Login_model');
        $this->lang->load('superadmin/dashboard', 'english');
        $this->lang->load('front_common' , 'english');
    }
	
	public function index($uname_prefix = '' , $username = ''){

        //get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
		$account_id = $loggedUser['id'];

        // get total account
        $totalAccount = $this->db->get('account')->num_rows();

        // get total active account
        $totalActiveAccount = $this->db->get_where('account',array('status'=>1))->num_rows();

       
		$siteUrl = base_url();
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,
            'totalAccount' =>$totalAccount,
            'totalActiveAccount' => $totalActiveAccount,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'dashboard'
        );
        $this->parser->parse('superadmin/layout/column-1' , $data);
    }
	
	public function edit_admin($id = '') {
		$this->load->library('template');
		
        //verify id is avaialabel or not
		$verify_admin = $this->db->select('*')
                        ->where('id', $id)
                        ->get('user_det')->row_array();
		
        if (!$verify_admin) {
            $this->Az->redirect('admin/Dashboard/index', 'system_message_error', lang('CANOT_EDIT_ADMIN'));
        }

		$siteUrl = site_url();

        //get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

		$data = array(
            'site_url' => $siteUrl,
            'meta_title' => 'Edit User',
            'meta_keywords' => 'Edit User',
            'meta_description' => 'Edit User',
            'content_block' => 'edit_admin',
            'page_title' => 'Edit User',
            'manager_description' => 'Create User',
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'pagination' => $this->pagination->create_links(),
            'admin_info' => $verify_admin,
			'loggedUser' => $loggedUser,
            'title' => 'Edit User'
        );
        $this->parser->parse('superadmin/layout/column-1', $data);
    }
	
	public function chkoldpw() {
   
        //chekchk eneterd old pw is correct or not       
        if ($_POST['opw']) {
            $chk = $this->db->select('password')
                            ->where('password', do_hash($_POST['opw']))
                            ->get('user_det')->row_array();

            if (!$chk) {
                echo 'Please enter correct password';
            } else {
                echo 'Password matched';
            }
        }
    }
	
	public function update_admin() {
        
		$this->load->library('template');
        $siteUrl = site_url();
		$post = $this->input->post();
		
        //get logged user info
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);

        //check for foem validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('opw', 'Old Password', 'required|xss_clean');		
        

        if ($this->form_validation->run() == FALSE) {
			
			$this->edit_admin($post['admin_id']);
        } 
		else {
			
			$this->Login_model->updateUser($post);
			
			$this->Az->redirect('superadmin/dashboard/', 'system_message_error',lang('USER_UPDATE_SUCCESSFULLY'));
			
			 
		}
		
    }
	
	public function logOut() {
        $this->session->sess_destroy();
        $this->Login_model->adminLogout();
        $this->Az->redirect('superadmin/Login', 'system_message_error', lang('LOGOUT_SUCCESS'));
    }

    public function getAPIBalanceData()
    {   
        $account_id = SUPERADMIN_ACCOUNT_ID;
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

            
            $order_type = $requestData['order'][0]['dir'];
            //if($requestData['draw'] == 1)
            //  $order_type = 'DESC';
            
            $order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
            $totalFiltered = $this->db->query($sql)->num_rows();
            $sql.=" ORDER BY a.id asc";
        
        
        
            $get_filter_data = $this->db->query($sql)->result_array();
        
        $data = array();
        $totalrecord = 0;
        if($get_filter_data){
            $i=1;
            foreach($get_filter_data as $list){
                
                $balance = '0.00';
                $response = $this->User->generate_get_balance_api_url($list['id'],$account_id);
                if($response['status'])
                {
                    $api_url = $response['api_url'];
                    $api_post_data = $response['post_data'];
                    $api_header_data = $response['header_data'];
                    $api_response = $this->User->call_get_balance_api($api_url,$api_post_data,$response['api_id'],$response['response_type'],$response['responsePara'],$response['seperator'],$api_header_data);
                    $balance = isset($api_response['balance']) ? number_format($api_response['balance'],2) : '0.00';
                }
                
                // get total success recharge
                $get_success_recharge = $this->db->select('SUM(amount) as totalAmount')->get_where('recharge_history',array('account_id'=>$account_id,'api_id'=>$list['id'],'status'=>2))->row_array();
                $successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';

                // get total success recharge
                $get_failed_recharge = $this->db->select('SUM(amount) as totalAmount')->get_where('recharge_history',array('account_id'=>$account_id,'api_id'=>$list['id'],'status'=>3))->row_array();
                $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
                
                $nestedData=array(); 
                $nestedData[] = $list['id'];
                $nestedData[] = $list['provider'];
                $nestedData[] = '&#8377; '.$balance;
                $nestedData[] = '&#8377; '.$successAmount;
                $nestedData[] = '&#8377; '.$failedAmount;
                
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


/* End of file login.php */
/* Location: ./application/controllers/login.php */