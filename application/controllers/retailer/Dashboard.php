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
        $this->User->checkRetailerPermission();

        $this->load->model('admin/Master_model');       
        $this->lang->load('admin/dashboard', 'english');
        $this->lang->load('front_common' , 'english');

    }
    
    public function index($uname_prefix = '' , $username = ''){
            
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        
        $account_id = $loggedUser['id'];

        $siteUrl = base_url();

        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'dashboard'
        );
        
        $this->parser->parse('retailer/layout/column-1' , $data);
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
    
    
    
    public function logOut() {
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $this->User->saveMemberLiveStatus($loggedAccountID, 0);
        
        $this->session->sess_destroy();
        $this->session->unset_userdata(RETAILER_SESSION_ID);
        $this->Az->redirect('login', 'system_message_error', lang('LOGOUT_SUCCESS'));  
    }


    public function getDashboardSummary()
    {
        $account_id = $this->User->get_domain_account();
          $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $today_date = date('Y-m-d');

        // get total success recharge
        $get_success_recharge = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('recharge_history',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>2,'DATE(created)'=>$today_date))->row_array();
        $successAmount = isset($get_success_recharge['totalAmount']) ? number_format($get_success_recharge['totalAmount'],2) : '0.00';
        $successRecord = isset($get_success_recharge['totalRecord']) ? $get_success_recharge['totalRecord'] : 0;

        // get total success recharge
        $get_pending_recharge = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('recharge_history',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>1,'DATE(created)'=>$today_date))->row_array();
        $pendingAmount = isset($get_pending_recharge['totalAmount']) ? number_format($get_pending_recharge['totalAmount'],2) : '0.00';
        $pendingRecord = isset($get_pending_recharge['totalRecord']) ? $get_pending_recharge['totalRecord'] : 0;

        // get total success recharge
        $get_failed_recharge = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('recharge_history',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>3,'DATE(created)'=>$today_date))->row_array();
        $failedAmount = isset($get_failed_recharge['totalAmount']) ? number_format($get_failed_recharge['totalAmount'],2) : '0.00';
        $failedRecord = isset($get_failed_recharge['totalRecord']) ? $get_failed_recharge['totalRecord'] : 0;

        // get total success recharge
        $getSuccessMoneyTransfer = $this->db->select('SUM(transfer_amount) as totalAmount,count(*) as totalRecord')->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'status'=>3,'DATE(created)'=>$today_date))->row_array();
        $successMoneyAmount = isset($getSuccessMoneyTransfer['totalAmount']) ? number_format($getSuccessMoneyTransfer['totalAmount'],2) : '0.00';
        $successMoneyRecord = isset($getSuccessMoneyTransfer['totalRecord']) ? $getSuccessMoneyTransfer['totalRecord'] : 0;

        // get total success recharge
        $getPendingMoneyTransfer = $this->db->select('SUM(transfer_amount) as totalAmount,count(*) as totalRecord')->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'status'=>2,'DATE(created)'=>$today_date))->row_array();
        $pendingMoneyAmount = isset($getPendingMoneyTransfer['totalAmount']) ? number_format($getPendingMoneyTransfer['totalAmount'],2) : '0.00';
        $pendingMoneyRecord = isset($getPendingMoneyTransfer['totalRecord']) ? $getPendingMoneyTransfer['totalRecord'] : 0;

        // get total success recharge
        $getFailedMoneyTransfer = $this->db->select('SUM(transfer_amount) as totalAmount,count(*) as totalRecord')->get_where('user_new_fund_transfer',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'status'=>4,'DATE(created)'=>$today_date))->row_array();
        $failedMoneyAmount = isset($getFailedMoneyTransfer['totalAmount']) ? number_format($getFailedMoneyTransfer['totalAmount'],2) : '0.00';
        $failedMoneyRecord = isset($getFailedMoneyTransfer['totalRecord']) ? $getFailedMoneyTransfer['totalRecord'] : 0;

        $get_success_bbps = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('bbps_history',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>2,'DATE(created)'=>$today_date))->row_array();
        $successBbpsAmount = isset($get_success_bbps['totalAmount']) ? number_format($get_success_bbps['totalAmount'],2) : '0.00';
        $successBbpsRecord = isset($get_success_bbps['totalRecord']) ? $get_success_bbps['totalRecord'] : 0;

        // get total success recharge
        $get_pending_bbps = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('bbps_history',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>1,'DATE(created)'=>$today_date))->row_array();
        $pendingBbpsAmount = isset($get_pending_bbps['totalAmount']) ? number_format($get_pending_bbps['totalAmount'],2) : '0.00';
        $pendingBbpsRecord = isset($get_pending_bbps['totalRecord']) ? $get_pending_bbps['totalRecord'] : 0;

        // get total success recharge
        $get_failed_bbps = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('bbps_history',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>3,'DATE(created)'=>$today_date))->row_array();
        $failedBbpsAmount = isset($get_failed_bbps['totalAmount']) ? number_format($get_failed_bbps['totalAmount'],2) : '0.00';
        $failedBbpsRecord = isset($get_failed_bbps['totalRecord']) ? $get_failed_bbps['totalRecord'] : 0;

        $get_aeps_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('instantpay_aeps_transaction',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>2,'DATE(created)'=>$today_date))->row_array();
        
        $successAepsAmount = isset($get_aeps_history['totalAmount']) ? number_format($get_aeps_history['totalAmount'],2) : '0.00';
        
        
        $successAepsRecord = isset($get_aeps_history['totalRecord']) ? $get_aeps_history['totalRecord'] : 0;

        $get_fino_aeps_success_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('member_new_aeps_transaction',array('account_id'=>$account_id,'status'=>2,'member_id'=>$loggedAccountID,'DATE(created)'=>$today_date))->row_array();

         $successFinoAepsAmount = isset($get_fino_aeps_success_history['totalAmount']) ? number_format($get_fino_aeps_success_history['totalAmount']) : '0.00';
         
         $successFinoAepsRecord = isset($get_fino_aeps_success_history['totalRecord']) ? $get_fino_aeps_success_history['totalRecord'] : 0;



        $total_success_aeps_amount = isset($get_aeps_history['totalAmount']) ? number_format($get_aeps_history['totalAmount'],2) : '0.00'; + isset($get_fino_aeps_success_history['totalAmount']) ? number_format($get_fino_aeps_success_history['totalAmount'],2) : '0.00';
        $total_success_aeps_record = $successAepsRecord + $successFinoAepsRecord;

        // failed aeps transacation

        $get_failed_aeps_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('instantpay_aeps_transaction',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>3,'DATE(created)'=>$today_date))->row_array();
        
        $failedAepsAmount = isset($get_failed_aeps_history['totalAmount']) ? number_format($get_failed_aeps_history['totalAmount'],2) : '0.00';

        $failedAepsRecord = isset($get_failed_aeps_history['totalRecord']) ? $get_failed_aeps_history['totalRecord'] : 0;

        $get_fino_aeps_failed_history = $this->db->select('SUM(amount) as totalAmount,count(*) as totalRecord')->get_where('member_new_aeps_transaction',array('account_id'=>$account_id,'status'=>3,'member_id'=>$loggedAccountID,'DATE(created)'=>$today_date))->row_array();

        $failedFinoAepsAmount = isset($get_fino_aeps_failed_history['totalAmount']) ? number_format($get_fino_aeps_failed_history['totalAmount'],2) : '0.00';

        $failedFinoAepsRecord = isset($get_fino_aeps_failed_history['totalRecord']) ? $get_fino_aeps_failed_history['totalRecord'] : 0;

         $total_failed_aeps_amount = number_format($get_failed_aeps_history['totalAmount'] + $get_fino_aeps_failed_history['totalAmount'] ,2);


        $total_failed_aeps_record = $failedAepsRecord + $failedFinoAepsRecord;
         //Open Payout

         // get total success recharge
        $getSuccessOpenPayout = $this->db->select('SUM(transfer_amount) as totalAmount,count(*) as totalRecord')->get_where('user_money_transfer',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'status'=>3,'DATE(created)'=>$today_date))->row_array();
        $successMoneyTransferAmount = isset($getSuccessOpenPayout['totalAmount']) ? number_format($getSuccessOpenPayout['totalAmount'],2) : '0.00';
        $successMoneyTransferRecord = isset($getSuccessOpenPayout['totalRecord']) ? $getSuccessOpenPayout['totalRecord'] : 0;

        // get total success recharge
        $getPendingOpenPayout = $this->db->select('SUM(transfer_amount) as totalAmount,count(*) as totalRecord')->get_where('user_money_transfer',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'status'=>2,'DATE(created)'=>$today_date))->row_array();
        $pendingMoneyTransferAmount = isset($getPendingOpenPayout['totalAmount']) ? number_format($getPendingOpenPayout['totalAmount'],2) : '0.00';
        $pendingMoneyTransferRecord = isset($getPendingOpenPayout['totalRecord']) ? $getPendingOpenPayout['totalRecord'] : 0;

        // get total success recharge
        $getFailedOpenPayout = $this->db->select('SUM(transfer_amount) as totalAmount,count(*) as totalRecord')->get_where('user_money_transfer',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'status'=>4,'DATE(created)'=>$today_date))->row_array();
        $failedMoneyTransferAmount = isset($getFailedOpenPayout['totalAmount']) ? number_format($getFailedOpenPayout['totalAmount'],2) : '0.00';
        $failedMoneyTransferRecord = isset($getFailedOpenPayout['totalRecord']) ? $getFailedOpenPayout['totalRecord'] : 0;


        
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
            'successBbpsAmount' =>'&#8377;'.$successBbpsAmount,
            'successBbpsRecord' => $successBbpsRecord,
            'pendingBbpsAmount' => '&#8377;'.$pendingBbpsAmount,
            'pendingBbpsRecord' => $pendingBbpsRecord,
            'failedBbpsAmount' => '&#8377;'.$failedBbpsAmount,
            'failedBbpsRecord' => $failedBbpsRecord,
            'successAepsAmount' => '&#8377; '.$total_success_aeps_amount,
            'failedAepsAmount' =>$total_failed_aeps_amount,
            'successAepsRecord' => $total_success_aeps_record,
            'failedAepsRecord' =>$total_failed_aeps_record,
             'successMoneyTransferAmount' => '&#8377; '.$successMoneyTransferAmount,
            'successMoneyTransferRecord' => $successMoneyTransferRecord,
            'pendingMoneyTransferAmount' => '&#8377; '.$pendingMoneyTransferAmount,
            'pendingMoneyTransferRecord' => $pendingMoneyTransferRecord,
            'failedMoneyTransferAmount' => '&#8377; '.$failedMoneyTransferAmount,
            'failedMoneyTransferRecord' => $failedMoneyTransferRecord,

        );

        echo json_encode($data);

    }



    public function showNotification($id = ''){
            
        //get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        
        $account_id = $loggedUser['id'];

        $siteUrl = base_url();

        if($id)
        {
        $this->db->where('id',$id);
        $this->db->where('is_read',0);
        $this->db->update('web_notification',array('is_read'=>1));
        }
        else
        {
        $this->db->where('is_read',0);
        $this->db->update('web_notification',array('is_read'=>1));
        }
        

        $notification_list = $this->db->limit(10)->get_where('web_notification',array('account_id'=>$domain_account_id))->result_array();

        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,
            'notification_list'=>$notification_list,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'notification'
        );
        
        $this->parser->parse('retailer/layout/column-1' , $data);
    }

    
    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */