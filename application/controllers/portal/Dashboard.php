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
		$this->User->checkApiMemberPermission();

        $this->load->model('admin/Master_model');       
		$this->lang->load('admin/dashboard', 'english');
        $this->lang->load('front_common' , 'english');
        
    }
	
	public function index($uname_prefix = '' , $username = ''){


        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];


       // debit fund

       $get_total_debit_fund = $this->db->select('sum(amount) as total_amount, count(*) as totalRecord')->get_where('member_wallet',array('member_id'=>$loggedAccountID,'type'=>2,'account_id'=>$account_id))->row_array();


       $total_debit_fund = isset($get_total_debit_fund['total_amount']) ? $get_total_debit_fund['total_amount'] : 0 ;
       
       $total_debit_fund_record = isset($get_total_debit_fund['totalRecord']) ? $get_total_debit_fund['totalRecord'] : 0 ;


       // credit fund


       $get_total_credit_fund = $this->db->select('sum(amount) as total_amount, count(*) as totalRecord')->get_where('member_wallet',array('member_id'=>$loggedAccountID,'type'=>1,'account_id'=>$account_id))->row_array();


       $total_credit_fund = isset($get_total_credit_fund['total_amount']) ? $get_total_credit_fund['total_amount'] : 0 ;
       
       $total_credit_fund_record = isset($get_total_credit_fund['totalRecord']) ? $get_total_credit_fund['totalRecord'] : 0 ;

       //total success payout

       $get_total_success_fund = $this->db->select('sum(transfer_amount) as total_amount, count(*) as totalRecord')->get_where('user_new_fund_transfer',array('user_id'=>$loggedAccountID,'status'=>3,'account_id'=>$account_id))->row_array();

      

       $total_success_fund = isset($get_total_success_fund['total_amount']) ? $get_total_success_fund['total_amount'] : 0 ;
       
       $total_success_record = isset($get_total_success_fund['totalRecord']) ? $get_total_success_fund['totalRecord'] : 0 ;
       
       
       //open money success payout
       $get_total_open_money_success_fund = $this->db->select('sum(transfer_amount) as total_amount, count(*) as totalRecord')->get_where('open_money_payout',array('user_id'=>$loggedAccountID,'status'=>3,'account_id'=>$account_id))->row_array();

      

       $total_open_money_success_fund = isset($get_total_open_money_success_fund['total_amount']) ? $get_total_open_money_success_fund['total_amount'] : 0 ;
       
       $total_open_money_success_record = isset($get_total_open_money_success_fund['totalRecord']) ? $get_total_open_money_success_fund['totalRecord'] : 0 ;
       

       // Pending Payout

       $get_total_pending_fund = $this->db->select('sum(transfer_amount) as total_amount, count(*) as totalRecord')->get_where('user_new_fund_transfer',array('user_id'=>$loggedAccountID,'status'=>2,'account_id'=>$account_id))->row_array();

      

       $total_pending_fund = isset($get_total_pending_fund['total_amount']) ? $get_total_pending_fund['total_amount'] : 0 ;
       
       $total_pending_record = isset($get_total_pending_fund['totalRecord']) ? $get_total_pending_fund['totalRecord'] : 0 ;

        // open money Pending Payout
         
       $get_total_open_money_pending_fund = $this->db->select('sum(transfer_amount) as total_amount, count(*) as totalRecord')->get_where('open_money_payout',array('user_id'=>$loggedAccountID,'status'=>2,'account_id'=>$account_id))->row_array();

      

       $total_open_money_pending_fund = isset($get_total_open_money_pending_fund['total_amount']) ? $get_total_open_money_pending_fund['total_amount'] : 0 ;
       
       $total_open_money_pending_record = isset($get_total_open_money_pending_fund['totalRecord']) ? $get_total_open_money_pending_fund['totalRecord'] : 0 ;
       
       
       //failed amount


        $get_total_failed_fund = $this->db->select('sum(transfer_amount) as total_amount, count(*) as totalRecord')->get_where('user_new_fund_transfer',array('user_id'=>$loggedAccountID,'status'=>4,'account_id'=>$account_id))->row_array();

      

       $total_failed_fund = isset($get_total_failed_fund['total_amount']) ? $get_total_failed_fund['total_amount'] : 0 ;
       
       $total_failed_record = isset($get_total_failed_fund['totalRecord']) ? $get_total_failed_fund['totalRecord'] : 0 ;
       
       
       //open money failed 
        
        $get_total_open_money_failed_fund = $this->db->select('sum(transfer_amount) as total_amount, count(*) as totalRecord')->get_where('open_money_payout',array('user_id'=>$loggedAccountID,'status'=>4,'account_id'=>$account_id))->row_array();

      

       $total_open_money_failed_fund = isset($get_total_open_money_failed_fund['total_amount']) ? $get_total_open_money_failed_fund['total_amount'] : 0 ;
       
       $total_open_money_failed_record = isset($get_total_open_money_failed_fund['totalRecord']) ? $get_total_open_money_failed_fund['totalRecord'] : 0 ;
       
       


       $today_date = date('Y-m-d');

        $getOpeningBal = $this->db->order_by('id','asc')->get_where('member_wallet',array('member_id'=>$loggedAccountID,'wallet_type'=>1,'DATE(created)'=>$today_date))->row_array();

       
       $today_opening_balance = isset($getOpeningBal['before_balance']) ? $getOpeningBal['before_balance'] : 0 ;

        // TODAY RECORD

       $get_today_credit_fund = $this->db->select('sum((CASE WHEN type = 1 THEN amount ELSE -1*amount END)) as total_amount, count(*) as totalRecord')->get_where('member_wallet',array('member_id'=>$loggedAccountID,'account_id'=>$account_id,'credited_by' =>3,'DATE(created)' =>$today_date))->row_array();

       $today_credit_fund = isset($get_today_credit_fund['total_amount']) ? $get_today_credit_fund['total_amount'] : 0 ;

       
       $today_credit_fund_record = isset($get_today_credit_fund['totalRecord']) ? $get_today_credit_fund['totalRecord'] : 0 ;




        $get_today_success_fund = $this->db->select('sum(total_wallet_charge) as total_amount, count(*) as totalRecord')->get_where('user_new_fund_transfer',array('user_id'=>$loggedAccountID,'status'=>3,'account_id'=>$account_id,'DATE(created)' => $today_date))->row_array();

      

       $today_success_fund = isset($get_today_success_fund['total_amount']) ? $get_today_success_fund['total_amount'] : 0 ;
       
       $today_success_record = isset($get_today_success_fund['totalRecord']) ? $get_today_success_fund['totalRecord'] : 0 ;
       
       
       //open money Today Fund
        $get_today_open_money_success_fund = $this->db->select('sum(total_wallet_charge) as total_amount, count(*) as totalRecord')->get_where('open_money_payout',array('user_id'=>$loggedAccountID,'status'=>3,'account_id'=>$account_id,'DATE(created)' => $today_date))->row_array();

      

       $today_open_money_success_fund = isset($get_today_open_money_success_fund['total_amount']) ? $get_today_open_money_success_fund['total_amount'] : 0 ;
       
       $today_open_money_success_record = isset($get_today_open_money_success_fund['totalRecord']) ? $get_today_open_money_success_fund['totalRecord'] : 0 ;
       

       // Pending Payout

       $get_today_pending_fund = $this->db->select('sum(total_wallet_charge) as total_amount, count(*) as totalRecord')->get_where('user_new_fund_transfer',array('user_id'=>$loggedAccountID,'status'=>2,'account_id'=>$account_id,'DATE(created)' =>$today_date))->row_array();

      

       $today_pending_fund = isset($get_today_pending_fund['total_amount']) ? $get_today_pending_fund['total_amount'] : 0 ;
       
       $today_pending_record = isset($get_today_pending_fund['totalRecord']) ? $get_today_pending_fund['totalRecord'] : 0 ;
       
       // today open money Pending
       
        $get_today_open_money_pending_fund = $this->db->select('sum(total_wallet_charge) as total_amount, count(*) as totalRecord')->get_where('open_money_payout',array('user_id'=>$loggedAccountID,'status'=>2,'account_id'=>$account_id,'DATE(created)' =>$today_date))->row_array();

      

       $today_open_money_pending_fund = isset($get_today_open_money_pending_fund['total_amount']) ? $get_today_open_money_pending_fund['total_amount'] : 0 ;
       
       $today_open_money_pending_record = isset($get_today_open_money_pending_fund['totalRecord']) ? $get_today_open_money_pending_fund['totalRecord'] : 0 ;
       




       $get_today_failed_fund = $this->db->select('sum(total_wallet_charge) as total_amount, count(*) as totalRecord')->get_where('user_new_fund_transfer',array('user_id'=>$loggedAccountID,'account_id'=>$account_id, 'DATE(created)' =>$today_date,'force_status' =>1))->row_array();

      

       $today_failed_fund = isset($get_today_failed_fund['total_amount']) ? $get_today_failed_fund['total_amount'] : 0  ;
       
       $today_failed_record = isset($get_today_failed_fund['totalRecord']) ? $get_today_failed_fund['totalRecord'] : 0 ;
       
        
          //open money failed
       $get_today_open_money_failed_fund = $this->db->select('sum(total_wallet_charge) as total_amount, count(*) as totalRecord')->get_where('open_money_payout',array('user_id'=>$loggedAccountID,'account_id'=>$account_id, 'DATE(created)' =>$today_date,'status' =>4))->row_array();

      

       $today_open_money_failed_fund = isset($get_today_open_money_failed_fund['total_amount']) ? $get_today_open_money_failed_fund['total_amount'] : 0 ;
       
       $today_open_money_failed_record = isset($get_today_open_money_failed_fund['totalRecord']) ? $get_today_open_money_failed_fund['totalRecord'] : 0 ;
       
       
     //today summary
     
     $today_all_success  = $today_success_fund +  $today_open_money_success_fund;
     $today_all_success_record =  $today_success_record +  $today_open_money_success_record;
     
     $today_all_pending  = $today_pending_fund +  $today_open_money_pending_fund;
     $today_all_pending_record =  $today_pending_record +  $today_open_money_pending_record;
     
     $today_all_failed  = $today_failed_fund +  $today_open_money_failed_fund;
     $today_all_failed_record =  $today_failed_record +  $today_open_money_failed_record;
     
     
     //total Summary 
      $total_all_success  = $total_success_fund +  $total_open_money_success_fund;
     $total_all_success_record =  $total_success_record +  $total_open_money_success_record;
     
     $total_all_pending  = $total_pending_fund +  $total_open_money_pending_fund;
     $total_all_pending_record =  $total_pending_record +  $total_open_money_pending_record;
     
     $total_all_failed  = $total_failed_fund +  $total_open_money_failed_fund;
     $total_all_failed_record =  $total_failed_record +  $total_open_money_failed_record;
     
     
        
        
        


		//get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        
		$account_id = $loggedUser['id'];

		$siteUrl = base_url();

		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,
            'total_debit_fund' =>$total_debit_fund,
            'total_debit_fund_record' =>$total_debit_fund_record,
            'total_credit_fund' =>$total_credit_fund,
            'total_credit_fund_record' =>$total_credit_fund_record,
            'total_success_fund' =>$total_all_success,
            'total_success_record' =>$total_all_success_record,
            'total_pending_fund' =>$total_all_pending,
            'total_pending_record' =>$total_all_pending_record,
            'total_failed_fund' =>$total_all_failed,
            'total_failed_record' =>$total_all_failed_record,
            'today_date' =>$today_date,
            'today_credit_fund' =>$today_credit_fund,
            'today_credit_fund_record' =>$today_credit_fund_record,  
            'today_opening_balance' =>$today_opening_balance,  
            'today_success_fund' =>$today_all_success,
            'today_success_record' =>$today_all_success_record,
            'today_pending_fund' =>$today_all_pending,
            'today_pending_record' =>$today_all_pending_record,
            'today_failed_fund' =>$today_all_failed,
            'today_failed_record' =>$today_all_failed_record,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'dashboard'
        );
        
        $this->parser->parse('portal/layout/column-1' , $data);
    }
	
	public function logOut() {
        $this->session->sess_destroy();
        $this->session->unset_userdata(API_MEMBER_SESSION_ID);
        $this->Az->redirect('login', 'system_message_error', lang('LOGOUT_SUCCESS'));  
    }

    
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */