<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Bbps extends CI_Controller{

    public function __construct() {
        parent::__construct();
        $this->User->checkDistributorPermission();
        $this->load->model('distributor/Bbps_model');      
        $this->lang->load('distributor/bbps', 'english');

    }
	
	public function index(){

        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(4, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }

        // get mobile prepaid biller list
        $mobilePrepaidBillerList = $this->User->get_bbps_biller_list(5);

        // get mobile postpaid biller list
        $mobilePostpaidBillerList = $this->User->get_bbps_biller_list(3);

        // get electricity biller list
        $electricityBillerList = $this->User->get_bbps_biller_list(4);

        // get dth biller list
        $dthBillerList = $this->User->get_bbps_biller_list(1);

        // get boradband postpaid biller list
        $boradbandPostpaidBillerList = $this->User->get_bbps_biller_list(19);

        // get landline postpaid biller list
        $landlinePostpaidBillerList = $this->User->get_bbps_biller_list(2);

        // get water biller list
        $waterBillerList = $this->User->get_bbps_biller_list(7);

        // get gas biller list
        $gasBillerList = $this->User->get_bbps_biller_list(6);

        // get LPG gas biller list
        $lpgGasBillerList = $this->User->get_bbps_biller_list(11);

        // get loan biller list
        $loanBillerList = $this->User->get_bbps_biller_list(17);

        // get insurance biller list
        $insuranceBillerList = $this->User->get_bbps_biller_list(5);

        // get fastag biller list
        $fastagBillerList = $this->User->get_bbps_biller_list(12);

        // get cable tv biller list
        $cableBillerList = $this->User->get_bbps_biller_list(9);

        // get housing society biller list
        $housingSocietyBillerList = $this->User->get_bbps_biller_list(17);

        // get municipal taxes biller list
        $municipalTaxesBillerList = $this->User->get_bbps_biller_list(18);

        // get municipal services biller list
        $municipalServicesBillerList = $this->User->get_bbps_biller_list(13);

        // get subscription biller list
        //$subscriptionBillerList = $this->User->get_bbps_biller_list(20);

        // get hospital biller list
        //$hospitalBillerList = $this->User->get_bbps_biller_list(19);

        // get credit card biller list
        $creditCardBillerList = $this->User->get_bbps_biller_list(22);

        // get entertainment biller list
        //$entertainmentBillerList = $this->User->get_bbps_biller_list(9);

        // get travel biller list
        //$travelBillerList = $this->User->get_bbps_biller_list(21);

        // get club biller list
        //$clubBillerList = $this->User->get_bbps_biller_list(24);

        $emiPaymentBillerList = $this->User->get_bbps_biller_list(10);


        $bbps_operator_list = $this->db->get('bbps_operator')->result_array();
        $bbps_circle_list = $this->db->get('bbps_circle')->result_array();
        
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'mobilePrepaidBillerList' => $mobilePrepaidBillerList,
            'mobilePostpaidBillerList' => $mobilePostpaidBillerList,
            'electricityBillerList' => $electricityBillerList,
            'bbps_operator_list' => $bbps_operator_list,
            'bbps_circle_list'         => $bbps_circle_list,
            'dthBillerList' => $dthBillerList,
            'boradbandPostpaidBillerList' => $boradbandPostpaidBillerList,
            'landlinePostpaidBillerList' => $landlinePostpaidBillerList,
            'waterBillerList' => $waterBillerList,
            'gasBillerList' => $gasBillerList,
            'lpgGasBillerList' => $lpgGasBillerList,
            'loanBillerList' => $loanBillerList,
            'insuranceBillerList' => $insuranceBillerList,
            'fastagBillerList' => $fastagBillerList,
            'cableBillerList' => $cableBillerList,
            'housingSocietyBillerList' => $housingSocietyBillerList,
            'municipalTaxesBillerList' => $municipalTaxesBillerList,
            'municipalServicesBillerList' => $municipalServicesBillerList,
            'subscriptionBillerList' => $subscriptionBillerList,
            'hospitalBillerList' => $hospitalBillerList,
            'creditCardBillerList' => $creditCardBillerList,
            'entertainmentBillerList' => $entertainmentBillerList,
            'travelBillerList' => $travelBillerList,
            'clubBillerList' => $clubBillerList,
            'emiPaymentBillerList' =>$emiPaymentBillerList,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'bbps/service-list'
        );

        $this->parser->parse('distributor/layout/column-1' , $data);
    }



    
    function maximumCheck($num)
    {
        if ($num < 1)
        {
            $this->form_validation->set_message(
                            'maximumCheck',
                            'The %s field must be grater than 10'
                        );
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

   

     public function checkOperatorFetchOption($billerID = 0,$service_id = 0)
    {
       
        $get_biller_id = $this->User->get_bbps_biller_id($billerID);
        $billerParams = $this->User->get_bbps_biller_param($service_id,$billerID);

        $str = '';
        if($billerParams)
        {
           foreach($billerParams as $pkey=>$plist)
           {
              $str.='<div class="form-group">';
              $str.='<label>'.$plist['paramName'].'*</label>';
              $str.='<input class="form-control" name="params[]" placeholder="Enter '.$plist['paramName'].'" type="text" />';
              $str.='</div>';
           }
        }
        $is_fetch = 1;
        $fetchOption = isset($get_biller_id['fetchOption']) ? $get_biller_id['fetchOption'] : '';
        if($fetchOption == 'MANDATORY')
        {
            $is_fetch = 1;
        }
        
        echo json_encode(array('status'=>1,'is_fetch'=>$is_fetch,'str'=>$str));
    }

    public function fetchMobilePostpaidBill()
    {
        $post = $this->input->post();
        $biller_id = isset($post['billerID']) ? $post['billerID'] : 0;
        $number = isset($post['number']) ? $post['number'] : '';
        $post['params'] = array(0=>$number);
        $service_id = 11;
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $member_id = $loggedUser['id'];

        

        $timeStamp = date('Y-m-d H:i:s');

        // generate recharge unique id
        $recharge_unique_id = rand(1111,9999).time();

         // get biller id
        $get_biller_id = $this->User->get_bbps_biller_id($biller_id);
        $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
        $service_id = isset($get_biller_id['service_id']) ? $get_biller_id['service_id'] : '';

        // get pmr service id
        $get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
        $pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

        $bill_fetch_respone = $this->User->call_mobikwik_bbps_electricity_bill_fetch_api($member_id,$biller_payu_id,$pmr_service_id,$post);
        $response = array();
        if($bill_fetch_respone['status'] == 1)
        {
            $response = array(
                'status' => 1,
                'msg' => 'Success',
                'amount' => $bill_fetch_respone['amount']
            );
        }
        else
        {
            $response = array(
                'status' => 0,
                'msg' => 'Server side error.',
                'amount' => 0
            );
        }

        echo json_encode($response);
    }

    public function mobilePostpaidAuth()
    {   
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $response = array();
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(4, $activeService)){
            $response = array(
                'status' => 0,
                'msg' => 'Sorry ! You are not authorized to access this service.'
            );
        }
        else
        {
            $loggedAccountID = $loggedUser['id'];
            //check for foem validation
            $post = $this->input->post();

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Mobile Postpaid Post Data - '.json_encode($post).']'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            $this->load->library('form_validation');
            $this->form_validation->set_rules('billerID', 'Operator', 'required');
            $this->form_validation->set_rules('number', 'Mobile Number', 'required|numeric|max_length[12]|xss_clean');
            $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
            
            if ($this->form_validation->run() == FALSE) {
                
                $response = array(
                    'status' => 0,
                    'msg' => validation_errors()
                );
            }
             else
            {   
                // get account balance
                $memberDetail =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                $min_wallet_balance = $memberDetail['min_wallet_balance'];
                $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - E-Wallet Balance - '.$user_before_balance.']'.PHP_EOL;
                $this->User->generateBBPSLog($log_msg);

                // check instantpay cogent api
                $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
                $admin_id = $this->User->get_admin_id();
                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

                if($user_before_balance < $final_deduct_wallet_balance){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in your account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                elseif($is_cogent_instantpay_api && $admin_wallet_balance < $post['amount']){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in admin account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Admin Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                else
                {
                    $response = $this->Bbps_model->bbpsMobilePostpaidAuth($post,$loggedAccountID);

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($response).']'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);

                }
            }
        }
        echo json_encode($response);
    
    }

    public function fetchElectricityBill()
    {
        $post = $this->input->post();

        $biller_id = isset($post['billerID']) ? $post['billerID'] : 0;
        $params = isset($post['params']) ? $post['params'] : array();
        $service_id = 4;
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $member_id = $loggedUser['id'];

        
        $timeStamp = date('Y-m-d H:i:s');

        // generate recharge unique id
        $recharge_unique_id = rand(1111,9999).time();

        // get biller id
        $get_biller_id = $this->User->get_bbps_biller_id($biller_id);
        $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
        $service_id = isset($get_biller_id['service_id']) ? $get_biller_id['service_id'] : '';

        // get pmr service id
        $get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
        $pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

        
        $bill_fetch_respone = $this->User->call_mobikwik_bbps_electricity_bill_fetch_api($member_id,$biller_payu_id,$pmr_service_id,$post);

        $response = array();
        if($bill_fetch_respone['status'] == 1)
        {
            $response = array(
                'status' => 1,
                'msg' => 'Success',
                'amount' => $bill_fetch_respone['amount'],
                'accountHolderName' => $bill_fetch_respone['accountHolderName']
            );
        }
        else
        {
            $response = array(
                'status' => 0,
                'msg' => 'Server side error.',
                'amount' => 0,
                'accountHolderName' => ''
            );
        }

        echo json_encode($response);
    }


    public function electricityAuth()
    {   
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $response = array();
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(4, $activeService)){
            $response = array(
                'status' => 0,
                'msg' => 'Sorry ! You are not authorized to access this service.'
            );
        }
        else
        {
            $loggedAccountID = $loggedUser['id'];
            //check for foem validation
            $post = $this->input->post();

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Electricity Post Data - '.json_encode($post).']'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            $this->load->library('form_validation');
            $this->form_validation->set_rules('billerID', 'Operator', 'required');
            $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
            if ($this->form_validation->run() == FALSE) {
                
                $response = array(
                    'status' => 0,
                    'msg' => validation_errors()
                );
            }
            else
            {   
                // get account balance
                $memberDetail =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                $min_wallet_balance = $memberDetail['min_wallet_balance'];
                $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - E-Wallet Balance - '.$user_before_balance.']'.PHP_EOL;
                $this->User->generateBBPSLog($log_msg);

                // check instantpay cogent api
                $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
                $admin_id = $this->User->get_admin_id();
                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

                if($user_before_balance < $final_deduct_wallet_balance){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in your account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);

                }
                elseif($is_cogent_instantpay_api && $admin_wallet_balance < $post['amount']){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in admin account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Admin Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                else
                {
                    $response = $this->Bbps_model->bbpsElectricityAuth($post,$loggedAccountID);

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($response).']'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);

                }
            }
        }
        echo json_encode($response);
    
    }


    public function fetchDTHBill()
    {
        $post = $this->input->post();

        $biller_id = isset($post['billerID']) ? $post['billerID'] : 0;
        $params = isset($post['params']) ? $post['params'] : array();
        $service_id = 13;
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $member_id = $loggedUser['id'];

        $timeStamp = date('Y-m-d H:i:s');

        // generate recharge unique id
        $recharge_unique_id = rand(1111,9999).time();

        // get biller id
        $get_biller_id = $this->User->get_bbps_biller_id($biller_id);
        $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
        $service_id = isset($get_biller_id['service_id']) ? $get_biller_id['service_id'] : '';

        // get pmr service id
        $get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
        $pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

        
        $bill_fetch_respone = $this->User->call_mobikwik_bbps_electricity_bill_fetch_api($member_id,$biller_payu_id,$pmr_service_id,$post);

        $response = array();
        if($bill_fetch_respone['status'] == 1)
        {
            $response = array(
                'status' => 1,
                'msg' => 'Success',
                'amount' => $bill_fetch_respone['amount'],
                'accountHolderName' => $bill_fetch_respone['accountHolderName']
            );
        }
        else
        {
            $response = array(
                'status' => 0,
                'msg' => 'Server side error.',
                'amount' => 0,
                'accountHolderName' => ''
            );
        }

        echo json_encode($response);
    }

    public function dthAuth()
    {   
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $response = array();
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(4, $activeService)){
            $response = array(
                'status' => 0,
                'msg' => 'Sorry ! You are not authorized to access this service.'
            );
        }
        else
        {
            $loggedAccountID = $loggedUser['id'];
            //check for foem validation
            $post = $this->input->post();

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - DTH Post Data - '.json_encode($post).']'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            $this->load->library('form_validation');
            $this->form_validation->set_rules('billerID', 'Operator', 'required');
            $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
            if ($this->form_validation->run() == FALSE) {
                
                $response = array(
                    'status' => 0,
                    'msg' => validation_errors()
                );
            }
            else
            {   
                // get account balance
                $memberDetail =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                $min_wallet_balance = $memberDetail['min_wallet_balance'];
                $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - E-Wallet Balance - '.$user_before_balance.']'.PHP_EOL;
                $this->User->generateBBPSLog($log_msg);

                // check instantpay cogent api
                $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
                $admin_id = $this->User->get_admin_id();
                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

                if($user_before_balance < $final_deduct_wallet_balance){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in your account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                elseif($is_cogent_instantpay_api && $admin_wallet_balance < $post['amount']){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in admin account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Admin Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                else
                {
                    $response = $this->Bbps_model->bbpsDTHAuth($post,$loggedAccountID);

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($response).']'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);

                }
            }
        }
        echo json_encode($response);
    
    }

    public function fetchMasterBill($service_id = 0)
    {
        $post = $this->input->post();

        $biller_id = isset($post['billerID']) ? $post['billerID'] : 0;
        $params = isset($post['params']) ? $post['params'] : array();
        $service_id = 13;
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $member_id = $loggedUser['id'];

        $timeStamp = date('Y-m-d H:i:s');

        // generate recharge unique id
        $recharge_unique_id = rand(1111,9999).time();

        // get biller id
        $get_biller_id = $this->User->get_bbps_biller_id($biller_id);
        $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
        $service_id = isset($get_biller_id['service_id']) ? $get_biller_id['service_id'] : '';

        // get pmr service id
        $get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
        $pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

        
        $bill_fetch_respone = $this->User->call_mobikwik_bbps_electricity_bill_fetch_api($member_id,$biller_payu_id,$pmr_service_id,$post);

        $response = array();
        if($bill_fetch_respone['status'] == 1)
        {
            $response = array(
                'status' => 1,
                'msg' => 'Success',
                'amount' => $bill_fetch_respone['amount'],
                'accountHolderName' => $bill_fetch_respone['accountHolderName']
            );
        }
        else
        {
            $response = array(
                'status' => 0,
                'msg' => 'Server side error.',
                'amount' => 0,
                'accountHolderName' => ''
            );
        }

        echo json_encode($response);
    }

    public function payMasterBillAuth($service_id = 0)
    {   
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $response = array();
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(4, $activeService)){
            $response = array(
                'status' => 0,
                'msg' => 'Sorry ! You are not authorized to access this service.'
            );
        }
        else
        {
            $loggedAccountID = $loggedUser['id'];
            //check for foem validation
            $post = $this->input->post();

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Master Post Data - '.json_encode($post).']'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            $this->load->library('form_validation');
            $this->form_validation->set_rules('billerID', 'Operator', 'required');
            $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
            if ($this->form_validation->run() == FALSE) {
                
                $response = array(
                    'status' => 0,
                    'msg' => validation_errors()
                );
            }
            else
            {   

                // get account balance
                $memberDetail =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                $min_wallet_balance = $memberDetail['min_wallet_balance'];
                $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - E-Wallet Balance - '.$user_before_balance.']'.PHP_EOL;
                $this->User->generateBBPSLog($log_msg);

                // check instantpay cogent api
                $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
                $admin_id = $this->User->get_admin_id();
                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

                if($user_before_balance < $final_deduct_wallet_balance){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in your account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                elseif($is_cogent_instantpay_api && $admin_wallet_balance < $post['amount']){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in admin account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Admin Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                else
                {
                    $response = $this->Bbps_model->bbpsMasterBillPayAuth($post,$loggedAccountID,$service_id);

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($response).']'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);

                }
            }
        }
        echo json_encode($response);
    
    }


    //credit card bill pay
     public function payCreditCardBillAuth($service_id = 0)
    {   
        
        
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $response = array();
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(4, $activeService)){
            $response = array(
                'status' => 0,
                'msg' => 'Sorry ! You are not authorized to access this service.'
            );
        }
        else
        {
            $loggedAccountID = $loggedUser['id'];
            //check for foem validation
            $post = $this->input->post();


            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - ('.$loggedUser['user_code'].') - Master Post Data - '.json_encode($post).']'.PHP_EOL;
            
            $this->load->library('form_validation');
            $this->form_validation->set_rules('canumber', 'Credit Card Number', 'required');
            $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
            if ($this->form_validation->run() == FALSE) {
                
                $response = array(
                    'status' => 0,
                    'msg' => validation_errors()
                );
            }
            else
            {   

                // get account balance
                $memberDetail =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
                $user_before_balance = $memberDetail['wallet_balance'];

                $min_wallet_balance = $memberDetail['min_wallet_balance'];
                $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - E-Wallet Balance - '.$user_before_balance.']'.PHP_EOL;
                $this->User->generateBBPSLog($log_msg);

                // check instantpay cogent api
                $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
                $admin_id = $this->User->get_admin_id();
                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                if($user_before_balance < $final_deduct_wallet_balance){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in your account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                elseif($is_cogent_instantpay_api && $admin_wallet_balance < $post['amount']){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in admin account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Admin Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                else
                {
                    $response = $this->Bbps_model->bbpsCreditBillPayAuth($post,$loggedAccountID,$service_id);

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($response).']'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);

                }
            }
        }
        echo json_encode($response);
        
    }


    public function prepaidRecharge(){

         $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(4, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }
        $prepaid_operator = $this->db->get_where('operator',array('type'=>'Prepaid','status'=>1))->result_array();
        $circle = $this->db->get('circle')->result_array(); 
        
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'prepaid_operator' => $prepaid_operator,
            'circle'         => $circle,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'bbps/mobile-prepaid'
        );

        $this->parser->parse('distributor/layout/column-1' , $data);
    }


    public function mobilePrepaidAuth()
    {   
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
         $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $response = array();
        

        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        
            if(!in_array(4, $activeService)){
            
            $response = array(
                'status'=>0,
                'message'=>'Service Not Activate.'

            );
        }
        
        else
        {
            $loggedAccountID = $loggedUser['id'];
            //check for foem validation
            $post = $this->input->post();
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Mobile Postpaid Post Data - '.json_encode($post).']'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            $this->load->library('form_validation');
            $this->form_validation->set_rules('operator', 'Operator', 'required');
            $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|max_length[12]|xss_clean');
            $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
            
            if ($this->form_validation->run() == FALSE) {
                
                $response = array(
                    'status' => 0,
                    'msg' => validation_errors()
                );
            }
            else
            {   
                // get account balance
                $memberDetail =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                $min_wallet_balance = $memberDetail['min_wallet_balance'];
                $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - E-Wallet Balance - '.$user_before_balance.']'.PHP_EOL;
                $this->User->generateBBPSLog($log_msg);

                // check instantpay cogent api
                $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
                $admin_id = $this->User->get_admin_id();
                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

                if($user_before_balance < $final_deduct_wallet_balance){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in your account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                elseif($is_cogent_instantpay_api && $admin_wallet_balance < $post['amount']){
                    $response = array(
                        'status' => 0,
                        'msg' => 'Sorry ! Insufficient balance in admin account.'
                    );

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Admin Wallet Insufficient Error.]'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);
                }
                else
                {
                    $response = $this->Bbps_model->bbpsMobilePrepaidAuth($post,$loggedAccountID);

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - R('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($response).']'.PHP_EOL;
                    $this->User->generateBBPSLog($log_msg);

                }
            }
        }
        
        echo json_encode($response);
    
    }


    




    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */