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
		$this->User->checkEmployePermission();
        $this->load->model('employe/Master_model');       
        $this->lang->load('admin/dashboard', 'english');
        $this->lang->load('front_common' , 'english');
    }
	
	public function index($uname_prefix = '' , $username = ''){

        //get logged user info
        $domain_account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$account_id = $loggedUser['id'];
        $accountData = $this->User->get_account_data($domain_account_id);

        $user_ip_address = $this->User->get_user_ip();

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Admin Dashboard.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);
        
        // get total member
        $totalMember = $this->db->where_in('role_id',array(3,4,5,6))->get_where('users',array('account_id'=>$domain_account_id))->num_rows();

        // get total master distributor
        $totalMDMember = $this->db->where_in('role_id',array(3))->get_where('users',array('account_id'=>$domain_account_id))->num_rows();

        // get total distributor
        $totalDistributorMember = $this->db->where_in('role_id',array(4))->get_where('users',array('account_id'=>$domain_account_id))->num_rows();

        // get total retailer
        $totalRetailerMember = $this->db->where_in('role_id',array(5))->get_where('users',array('account_id'=>$domain_account_id))->num_rows();

        // get total api member
        $totalAPIMember = $this->db->where_in('role_id',array(6))->get_where('users',array('account_id'=>$domain_account_id))->num_rows();
        
        
        $totalUserMember = $this->db->where_in('role_id',array(8))->get_where('users',array('account_id'=>$domain_account_id))->num_rows();

        $master_distributor_total_wallet_balance = $this->User->getAccountWalletBalanceSP($domain_account_id,1,3);
        
        $distributor_total_wallet_balance = $this->User->getAccountWalletBalanceSP($domain_account_id,1,4);
        

        $retailer_total_wallet_balance = $this->User->getAccountWalletBalanceSP($domain_account_id,1,5);
        
        $api_user_total_wallet_balance = $this->User->getAccountWalletBalanceSP($domain_account_id,1,6);
        
        $user_total_wallet_balance = $this->User->getAccountWalletBalanceSP($domain_account_id,1,8);
        

        $total_wallet_balance = $master_distributor_total_wallet_balance + $distributor_total_wallet_balance + $retailer_total_wallet_balance + $api_user_total_wallet_balance + $user_total_wallet_balance;

        
        $today_date = date('Y-m-d');

        // get MD commission
        $get_md_commission = $this->db->query("SELECT SUM(commision_amount) as total_commission FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE b.role_id = 3 AND a.account_id = '$domain_account_id' AND DATE(a.created) = '$today_date' AND a.type = 'RECHARGE'")->row_array();
        $md_commission = isset($get_md_commission['total_commission']) ? $get_md_commission['total_commission'] : 0;

        // get D commission
        $get_d_commission = $this->db->query("SELECT SUM(commision_amount) as total_commission FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE b.role_id = 4 AND a.account_id = '$domain_account_id' AND DATE(a.created) = '$today_date' AND a.type = 'RECHARGE'")->row_array();
        $d_commission = isset($get_d_commission['total_commission']) ? $get_d_commission['total_commission'] : 0;

        // get R commission
        $get_r_commission = $this->db->query("SELECT SUM(commision_amount) as total_commission FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE b.role_id = 5 AND a.account_id = '$domain_account_id' AND DATE(a.created) = '$today_date' AND a.type = 'RECHARGE'")->row_array();
        $r_commission = isset($get_r_commission['total_commission']) ? $get_r_commission['total_commission'] : 0;

        // get api commission
        $get_api_commission = $this->db->query("SELECT SUM(commision_amount) as total_commission FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE b.role_id = 6 AND a.account_id = '$domain_account_id' AND DATE(a.created) = '$today_date' AND a.type = 'RECHARGE'")->row_array();
        $api_commission = isset($get_api_commission['total_commission']) ? $get_api_commission['total_commission'] : 0;

        // get api commission
        $get_user_commission = $this->db->query("SELECT SUM(commision_amount) as total_commission FROM tbl_user_commision as a INNER JOIN tbl_users as b ON b.id = a.member_id WHERE b.role_id = 8 AND a.account_id = '$domain_account_id' AND DATE(a.created) = '$today_date' AND a.type = 'RECHARGE'")->row_array();
        $user_commission = isset($get_user_commission['total_commission']) ? $get_user_commission['total_commission'] : 0;

        $total_distribute_commision = $md_commission + $d_commission + $r_commission + $api_commission + $user_commission;


        $get_aeps_success_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('member_aeps_transaction',array('account_id'=>$domain_account_id,'status'=>2,'DATE(created)'=>$today_date))->row_array();
        
        

        $get_icici_aeps_success_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('instantpay_aeps_transaction',array('account_id'=>$domain_account_id,'status'=>2,'DATE(created)'=>$today_date))->row_array();
                
                
       
        $successAepsAmount = isset($get_aeps_success_history['totalAmount']) ? number_format($get_aeps_success_history['totalAmount'],2) : '0.00';
    
       

        $successIciciAepsAmount = isset($get_icici_aeps_success_history['totalAmount']) ? number_format($get_icici_aeps_success_history['totalAmount'],2) : '0.00';

        $total_success_aeps = $successAepsAmount + $successIciciAepsAmount;
       
        

        $get_aeps_failed_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('member_aeps_transaction',array('account_id'=>$domain_account_id,'status'=>3,'DATE(created)'=>$today_date))->row_array();

        $get_icici_aeps_failed_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('instantpay_aeps_transaction',array('account_id'=>$domain_account_id,'status'=>3,'DATE(created)'=>$today_date))->row_array();

        
        $failedsAepsAmount = isset($get_aeps_failed_history['totalAmount']) ? number_format($get_aeps_failed_history['totalAmount'],2) : '0.00';


        $failedIciciAepsAmount = isset($get_icici_aeps_failed_history['totalAmount']) ? number_format($get_icici_aeps_failed_history['totalAmount'],2) : '0.00';

        $total_failed_aeps = $failedsAepsAmount + $failedIciciAepsAmount;





		$siteUrl = base_url();

        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,
            'totalMember' => $totalMember,
            'totalMDMember' => $totalMDMember,
            'totalDistributorMember' => $totalDistributorMember,
            'totalRetailerMember' => $totalRetailerMember,
            'totalAPIMember' => $totalAPIMember,
            'totalUserMember' =>$totalUserMember,
            'master_distributor_total_wallet_balance'=>$master_distributor_total_wallet_balance,
            'distributor_total_wallet_balance' => $distributor_total_wallet_balance,
            'retailer_total_wallet_balance' => $retailer_total_wallet_balance,
            'api_user_total_wallet_balance' => $api_user_total_wallet_balance,
            'user_total_wallet_balance' => $user_total_wallet_balance,
            'total_wallet_balance' => $total_wallet_balance,
            'md_commission' => $md_commission,
            'd_commission' => $d_commission,
            'r_commission' => $r_commission,
            'api_commission' => $api_commission,
            'user_commission' => $user_commission,
            'total_distribute_commision' => $total_distribute_commision,
            'accountData' => $accountData,
            'total_success_aeps'=>$total_success_aeps,
            'total_failed_aeps' =>$total_failed_aeps,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'dashboard'
        );

        $this->parser->parse('employe/layout/column-1' , $data);
    }
	
	
	public function logOut() {

        //get logged user info
        $domain_account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Logged Out From Admin Panel.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

        $this->session->sess_destroy();
        $this->session->unset_userdata(ADMIN_SESSION_ID);
        $this->Az->redirect('login', 'system_message_error', lang('LOGOUT_SUCCESS'));  
    }


    public function getAPIBalanceData()
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
                $today_date = date('Y-m-d');
                $balance = '0.00';
                $response = $this->User->generate_get_balance_api_url($list['id']);
                if($response['status'])
                {
                    $api_url = $response['api_url'];
                    $api_post_data = $response['post_data'];
                    $api_header_data = $response['header_data'];
                    $api_response = $this->User->call_get_balance_api($api_url,$api_post_data,$response['api_id'],$response['response_type'],$response['responsePara'],$response['seperator'],$api_header_data);
                    $balance = isset($api_response['balance']) ? number_format($api_response['balance'],2) : '0.00';
                }
                
                // get total success recharge
                $get_success_recharge = $this->db->select('SUM(amount) as totalAmount')->get_where('recharge_history',array('account_id'=>$account_id,'api_id'=>$list['id'],'status'=>2,'DATE(created)'=>$today_date))->row_array();
                $successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';

                // get total success recharge
                $get_failed_recharge = $this->db->select('SUM(amount) as totalAmount')->get_where('recharge_history',array('account_id'=>$account_id,'api_id'=>$list['id'],'status'=>3,'DATE(created)'=>$today_date))->row_array();
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

    public function getDashboardSummary()
    {
        $account_id = $this->User->get_domain_account();

        $today_date = date('Y-m-d');

        // get total success recharge
        $get_success_recharge = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('recharge_history',array('account_id'=>$account_id,'status'=>2,'DATE(created)'=>$today_date))->row_array();
        $successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;

        // get total success recharge
        $get_pending_recharge = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('recharge_history',array('account_id'=>$account_id,'status'=>1,'DATE(created)'=>$today_date))->row_array();
        $pendingAmount = isset($get_pending_recharge['totalAmount']) ? number_format($get_pending_recharge['totalAmount'],2) : '0.00';
        $pendingRecord = isset($get_pending_recharge['totalRecord']) ? $get_pending_recharge['totalRecord'] : 0;

        // get total success recharge
        $get_failed_recharge = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('recharge_history',array('account_id'=>$account_id,'status'=>3,'DATE(created)'=>$today_date))->row_array();
        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;

        // get total success recharge
        $getSuccessMoneyTransfer = $this->db->select('SUM(transfer_amount) as totalAmount,count(*) as totalRecord')->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'status'=>3,'DATE(created)'=>$today_date))->row_array();
        $successMoneyAmount = isset($getSuccessMoneyTransfer['totalAmount']) ? number_format($getSuccessMoneyTransfer['totalAmount'],2) : '0.00';
        $successMoneyRecord = isset($getSuccessMoneyTransfer['totalRecord']) ? $getSuccessMoneyTransfer['totalRecord'] : 0;

        // get total success recharge
        $getPendingMoneyTransfer = $this->db->select('SUM(transfer_amount) as totalAmount,count(*) as totalRecord')->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'status'=>2,'DATE(created)'=>$today_date))->row_array();
        $pendingMoneyAmount = isset($getPendingMoneyTransfer['totalAmount']) ? number_format($getPendingMoneyTransfer['totalAmount'],2) : '0.00';
        $pendingMoneyRecord = isset($getPendingMoneyTransfer['totalRecord']) ? $getPendingMoneyTransfer['totalRecord'] : 0;

        // get total success recharge
        $getFailedMoneyTransfer = $this->db->select('SUM(transfer_amount) as totalAmount,count(*) as totalRecord')->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'status'=>4,'DATE(created)'=>$today_date))->row_array();
        $failedMoneyAmount = isset($getFailedMoneyTransfer['totalAmount']) ? number_format($getFailedMoneyTransfer['totalAmount'],2) : '0.00';
        $failedMoneyRecord = isset($getFailedMoneyTransfer['totalRecord']) ? $getFailedMoneyTransfer['totalRecord'] : 0;

        // get aeps record
        $get_aeps_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('instantpay_aeps_transaction',array('account_id'=>$account_id,'status'=>2,'DATE(created)'=>$today_date))->row_array();
        
        $successAepsAmount = isset($get_aeps_history['totalAmount']) ? number_format($get_aeps_history['totalAmount'],2) : '0.00';
        
        
        $successAepsRecord = isset($get_aeps_history['totalRecord']) ? $get_aeps_history['totalRecord'] : 0;

        $get_fino_aeps_success_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('member_new_aeps_transaction',array('account_id'=>$account_id,'status'=>2,'DATE(created)'=>$today_date))->row_array();

         $successFinoAepsAmount = isset($get_fino_aeps_success_history['totalAmount']) ? number_format($get_fino_aeps_success_history['totalAmount']) : '0.00';
        
         $successFinoAepsRecord = isset($get_fino_aeps_success_history['totalRecord']) ? $get_fino_aeps_success_history['totalRecord'] : 0;



        $total_success_aeps_amount = number_format($get_aeps_history['totalAmount']+ $get_fino_aeps_success_history['totalAmount'],2);
        $total_success_aeps_record = $successAepsRecord + $successFinoAepsRecord;

        // failed aeps transacation

        $get_failed_aeps_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('instantpay_aeps_transaction',array('account_id'=>$account_id,'status'=>3,'DATE(created)'=>$today_date))->row_array();
        
        $failedAepsAmount = isset($get_failed_aeps_history['totalAmount']) ? number_format($get_failed_aeps_history['totalAmount'],2) : '0.00';

        $failedAepsRecord = isset($get_failed_aeps_history['totalRecord']) ? $get_failed_aeps_history['totalRecord'] : 0;
        
        $get_fino_aeps_failed_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('member_new_aeps_transaction',array('account_id'=>$account_id,'status'=>3,'DATE(created)'=>$today_date))->row_array();

        $failedFinoAepsAmount = isset($get_fino_aeps_failed_history['totalAmount']) ? number_format($get_fino_aeps_failed_history['totalAmount'],2) : '0.00';

        $failedFinoAepsRecord = isset($get_fino_aeps_failed_history['totalRecord']) ? $get_fino_aeps_failed_history['totalRecord'] : 0;
         
         $total_failed_aeps_amount = number_format($get_failed_aeps_history['totalAmount'] + $get_fino_aeps_failed_history['totalAmount'] ,2);


        $total_failed_aeps_record = $failedAepsRecord + $failedFinoAepsRecord;
            
        $data = array(
            'status' => 1,
            'successAmount' => '&#8377; '.$successAmount,
            'successRecord' => $successRecord,
            'pendingAmount' => '&#8377; '.$pendingAmount,
            'pendingRecord' => $pendingRecord,
            'failedAmount' => '&#8377; '.$failedAmount,
            'failedRecord' => $failedRecord,
            'successMoneyAmount' => '&#8377; '.$successMoneyAmount,
            'successMoneyRecord' => $successMoneyRecord,
            'pendingMoneyAmount' => '&#8377; '.$pendingMoneyAmount,
            'pendingMoneyRecord' => $pendingMoneyRecord,
            'failedMoneyAmount' => '&#8377; '.$failedMoneyAmount,
            'failedMoneyRecord' => $failedMoneyRecord,
            'successAepsAmount' => '&#8377; '.$total_success_aeps_amount,
            'failedAepsAmount' =>$total_failed_aeps_amount,
            'successAepsRecord' => $total_success_aeps_record,
            'failedAepsRecord' =>$total_failed_aeps_record,

        );
        
      
        
        echo json_encode($data);

    }





    public function sendNotification(){

        $domain_account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $account_id = $loggedUser['id'];
        $accountData = $this->User->get_account_data($domain_account_id);

        $user_ip_address = $this->User->get_user_ip();

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Open Send Notification Page.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

        if($accountData['is_app_notification'] == 0){
            
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Notification send not allowed redirect to dashboard.]'.PHP_EOL;
            $this->User->generateAccountActivityLog($log_msg);

            $this->Az->redirect('admin/dashboard', 'system_message_error',lang('DB_ERROR'));
        }

        $userList = $this->db->where_in('role_id',array(3,4,5,8))->get_where('users',array('account_id'=>$domain_account_id))->result_array();
        
        $siteUrl = base_url();

        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'userList' => $userList,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'sendNotification'
        );

        $this->parser->parse('admin/layout/column-1' , $data);
    }


    public function notificationAuth()
    {
        $account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $accountData = $this->User->get_account_data($account_id);
        $user_ip_address = $this->User->get_user_ip();
        //check for foem validation
        $post = $this->security->xss_clean($this->input->post());
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Notification Auth Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', 'Notification Title', 'required|xss_clean');
        $this->form_validation->set_rules('message', 'Notification Message ', 'xss_clean|required');
        if ($this->form_validation->run() == FALSE) {
            
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Notification Auth Validation Error.]'.PHP_EOL;
            $this->User->generateAccountActivityLog($log_msg);

            $this->sendNotification();
        }
        else
        {   
            
            if($accountData['is_app_notification'] == 0){

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Notification Auth - Notification send not allowed redirect back to dashboard.]'.PHP_EOL;
                $this->User->generateAccountActivityLog($log_msg);

                $this->Az->redirect('admin/dashboard', 'system_message_error',lang('DB_ERROR'));
            }
            

            if($accountData['notification_server_key'] == ''){

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Notification Auth - Notification server key not found redirect back to send notification page.]'.PHP_EOL;
                $this->User->generateAccountActivityLog($log_msg);

                $this->Az->redirect('admin/dashboard/sendNotification', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! your firebase notification account server key not avaialabel.</div>');
            }

            $userID = $post['user_id'];
            $title = isset($post['title']) ? $post['title'] : '';
            $message = isset($post['message']) ? $post['message'] : '';

            $status = $this->User->sendNotification($userID,$title,$message);
            
            if($status == true)
            {
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Notification Auth - Notification send successfully redirect back to send notification page.]'.PHP_EOL;
                $this->User->generateAccountActivityLog($log_msg);

                $this->Az->redirect('admin/dashboard/sendNotification', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Notification sent successfully.</div>');
            }
            else
            {
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Notification Auth - Notification send failed redirect back to send notification page.]'.PHP_EOL;
                $this->User->generateAccountActivityLog($log_msg);

                $this->Az->redirect('admin/dashboard/sendNotification', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! Notification failed due to some reason.</div>');
            }
            
        }
    
    }

    
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */