<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Recharge extends CI_Controller{

    public function __construct() {
        parent::__construct();
        $this->User->checkDistributorPermission();
        $this->load->model('distributor/Master_model');  
        $this->load->model('distributor/Complain_model');          
        $this->lang->load('distributor/recharge', 'english');
    }
    
    public function mobilePrepaid(){

        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(1, $activeService)){
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
            'content_block' => 'recharge/mobile-prepaid'
        );

        $this->parser->parse('distributor/layout/column-1' , $data);
    }



    public function mobilePostpaid(){

        
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(1, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }
        $postpaid_operator = $this->db->get_where('operator',array('type'=>'Postpaid'))->result_array();
        $circle = $this->db->get('circle')->result_array(); 
        
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'postpaid_operator' => $postpaid_operator,
            'circle'         => $circle,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'recharge/mobile-postpaid'
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

    public function mobileRecharge()
    {   
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(1, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }
        $loggedAccountID = $loggedUser['id'];
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Recharge Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|max_length[12]|xss_clean');
        $this->form_validation->set_rules('operator', 'Operator', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
        
        if ($this->form_validation->run() == FALSE) {
            
            $this->mobilePrepaid();
            
        }
        else
        {   
            $chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedAccountID))->row_array();
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Wallet Balance - '.$chk_wallet_balance['wallet_balance'].']'.PHP_EOL;
            $this->User->generateLog($log_msg);

            
            $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
            if($user_before_balance < $post['amount']){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('distributor/recharge/mobilePrepaid', 'system_message_error',lang('WALLET_ERROR'));
                   

            } 

            // check instantpay cogent api
           /* $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
            if($is_cogent_instantpay_api)
            {
                $admin_id = $this->User->get_admin_id();
                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalance($admin_id);
                if($admin_wallet_balance < $post['amount']){
                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Insufficient Virtual Wallet Error]'.PHP_EOL;
                    $this->User->generateLog($log_msg);
                    
                    $this->Az->redirect('distributor/recharge/mobilePrepaid', 'system_message_error',lang('ADMIN_WALLET_BALANCE_ERROR'));
                    
                }
            }*/
            

            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];

            $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

            
            if($user_before_balance < $final_deduct_wallet_balance){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Minimum Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                
                $this->Az->redirect('distributor/recharge/mobilePrepaid', 'system_message_error',lang('MIN_WALLET_ERROR'));
                
            }
            
            
            $md_id = $this->User->get_master_distributor_id($loggedUser['id']);
            $response = $this->User->generate_api_url($md_id,$post['operator'],$post['amount'],$loggedUser['id']);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Rechage API Generate Response - '.json_encode($response).']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            if($response['status'] && $response['api_id'])
            {
                if($accountData['account_type'] == 2)
                {
                    // get operator code
                    $get_operator_code = $this->db->get_where('api_operator',array('account_id'=>SUPERADMIN_ACCOUNT_ID,'api_id'=>$response['api_id'],'opt_id'=>$post['operator']))->row_array();
                }
                else
                {
                    // get operator code
                    $get_operator_code = $this->db->get_where('api_operator',array('account_id'=>$account_id,'api_id'=>$response['api_id'],'opt_id'=>$post['operator']))->row_array();
                }
                $opt_code = isset($get_operator_code['opt_code']) ? $get_operator_code['opt_code'] : '';
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Rechage API Operator Code - '.$opt_code.']'.PHP_EOL;
                $this->User->generateLog($log_msg);

                // get system operator code
                $system_opt_id = $post['operator'];

                $circle_code = 19;
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Rechage API Circle Code - '.$circle_code.']'.PHP_EOL;
                $this->User->generateLog($log_msg);

                // generate recharge unique id
                $recharge_unique_id = rand(1111,9999).time();
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Rechage Unique ID - '.$recharge_unique_id.']'.PHP_EOL;
                $this->User->generateLog($log_msg);

                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                $user_after_balance = $user_before_balance - $post['amount'];

                if($response['is_instantpay_api'])
                {
                    if(!$chk_wallet_balance['is_instantpay_ekyc'])
                    {
                        $this->Az->redirect('distributor/recharge/mobilePrepaid', 'system_message_error',sprintf(lang('RECHARGE_FAILED'),'Sorry ! Your eKyc not approved yet, please submit your eKyc.'));
                    }
                }

                $data = array(
                    'account_id'         => $account_id,
                    'member_id'          => $loggedUser['id'],
                    'api_id'             => $response['api_id'],
                    'recharge_type'      => 1,
                    'recharge_subtype'   => $post['recharge_type'],
                    'recharge_display_id'=> $recharge_unique_id,
                    'mobile'             => $post['mobile'],
                    'account_number'     => isset($post['acnumber']) ? $post['acnumber'] : '',
                    'operator_code'      => $opt_code,
                    'system_opt_id'      => $system_opt_id,
                    'circle_code'        => $circle_code,
                    'amount'             => $post['amount'],
                    'before_balance'     => $user_before_balance,
                    'after_balance'      => $user_after_balance,
                    'status'             => 1,
                    'created'            => date('Y-m-d H:i:s')                  
                );


                $this->db->insert('recharge_history',$data);
                $recharge_id = $this->db->insert_id();
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Save Rechage Data System Recharge ID - '.$recharge_id.']'.PHP_EOL;
                $this->User->generateLog($log_msg);

                $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
                $after_balance = $user_before_balance - $post['amount'];    

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedUser['id'],    
                    'before_balance'      => $user_before_balance,
                    'amount'              => $post['amount'],  
                    'after_balance'       => $after_balance,      
                    'status'              => 1,
                    'type'                => 2,      
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
                if($is_cogent_instantpay_api)
                {
                    $admin_id = $this->User->get_admin_id();
                    $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
                    $admin_after_wallet_balance = $admin_before_wallet_balance - $post['amount'];

                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $admin_id,    
                        'before_balance'      => $admin_before_wallet_balance,
                        'amount'              => $post['amount'],  
                        'after_balance'       => $admin_after_wallet_balance,      
                        'status'              => 1,
                        'type'                => 2,   
                        'wallet_type'         => 1,   
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
                    );

                    $this->db->insert('virtual_wallet',$wallet_data);

                    
                }

                if($response['is_instantpay_api'])
                {
                    if($chk_wallet_balance['is_instantpay_ekyc'])
                    {
                        // call recharge API
                        $api_response = $this->User->instantpay_rechage_api($opt_code,$loggedUser['id'],$recharge_unique_id,$post['mobile'],$post['amount'],$response['api_id'],$loggedUser['user_code'],'D');
                    }
                    else
                    {
                        $api_response = array(
                            'status' => 3,
                            'opt_msg' => 'Sorry ! Your eKyc not approved yet, please submit your eKyc.'
                        );
                    }
                }
                else
                {

                    $api_url = $response['api_url'];
                    $api_post_data = $response['post_data'];
                    $api_url = str_replace('{AMOUNT}',$post['amount'],$api_url);
                    $api_url = str_replace('{OPERATOR}',$opt_code,$api_url);
                    $api_url = str_replace('{CIRCLE}',$circle_code,$api_url);
                    $api_url = str_replace('{TXNID}',$recharge_unique_id,$api_url);
                    $api_url = str_replace('{MOBILE}',$post['mobile'],$api_url);
                    $api_url = str_replace('{MEMBERID}',$loggedUser['user_code'],$api_url);

                    // replace post data
                    if($api_post_data)
                    {
                        foreach($api_post_data as $apikey=>$apival)
                        {
                            if($apival == '{AMOUNT}')
                            {
                                $api_post_data[$apikey] = $post['amount'];
                            }
                            elseif($apival == '{OPERATOR}')
                            {
                                $api_post_data[$apikey] = $opt_code;
                            }
                            elseif($apival == '{CIRCLE}')
                            {
                                $api_post_data[$apikey] = $circle_code;
                            }
                            elseif($apival == '{TXNID}')
                            {
                                $api_post_data[$apikey] = $recharge_unique_id;
                            }
                            elseif($apival == '{MOBILE}')
                            {
                                $api_post_data[$apikey] = $post['mobile'];
                            }
                            elseif($apival == '{MEMBERID}')
                            {
                                $api_post_data[$apikey] = $loggedUser['user_code'];
                            }
                        }
                    }

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Final API URL - '.$api_url.' - Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
                    $this->User->generateLog($log_msg);

                    // call recharge API
                    $api_response = $this->User->prepaid_rechage_api($api_url,$api_post_data,$loggedUser['id'],$recharge_unique_id,$response['api_id'],$response['response_type'],$response['responsePara'],$response['seperator'],$response['header_data'],$loggedUser['user_code'],'D');
                }

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - API Final Response - '.json_encode($api_response).']'.PHP_EOL;
                $this->User->generateLog($log_msg);
                
                if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
                {
                    
                    if($api_response['status'] == 1){
                        // update recharge status
                        $this->db->where('id',$recharge_id);
                        $this->db->where('recharge_display_id',$recharge_unique_id);
                        $this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
                        
                        
                        $this->Az->redirect('distributor/recharge/mobilePrepaid', 'system_message_error',lang('RECHARGE_PENDING'));
                          
                    }
                    elseif($api_response['status'] == 2)
                    {
                        // update recharge status
                        $this->db->where('id',$recharge_id);
                        $this->db->where('recharge_display_id',$recharge_unique_id);
                        $this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));

                        // save system log
                        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Distribute Commision Start]'.PHP_EOL;
                        $this->User->generateLog($log_msg);
                        // distribute commision
                        $this->User->distribute_recharge_commision($recharge_id,$recharge_unique_id,$post['amount'],$loggedUser['id']);
                        // save system log
                        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Distribute Commision End]'.PHP_EOL;
                        $this->User->generateLog($log_msg);

                        
                        
                        $this->Az->redirect('distributor/recharge/mobilePrepaid', 'system_message_error',lang('RECHARGE_SUCCESS'));
                          
                    }
                }
                else
                {
                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Recharge Failed]'.PHP_EOL;
                    $this->User->generateLog($log_msg);
                    // update recharge status
                    $this->db->where('id',$recharge_id);
                    $this->db->where('recharge_display_id',$recharge_unique_id);
                    $this->db->update('recharge_history',array('status'=>3));

                    $before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);

                    
                    $after_balance = $before_balance + $post['amount'];    

                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $loggedUser['id'],    
                        'before_balance'      => $before_balance,
                        'amount'              => $post['amount'],  
                        'after_balance'       => $after_balance,      
                        'status'              => 1,
                        'type'                => 1,      
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'Recharge #'.$recharge_unique_id.' Amount Refund Credited.'
                    );

                    $this->db->insert('member_wallet',$wallet_data);


                    if($is_cogent_instantpay_api)
                    {
                        $admin_id = $this->User->get_admin_id();
                        $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
                        $admin_after_wallet_balance = $admin_before_wallet_balance + $post['amount'];

                        $wallet_data = array(
                            'account_id'          => $account_id,
                            'member_id'           => $admin_id,    
                            'before_balance'      => $admin_before_wallet_balance,
                            'amount'              => $post['amount'],  
                            'after_balance'       => $admin_after_wallet_balance,      
                            'status'              => 1,
                            'type'                => 1,   
                            'wallet_type'         => 1,   
                            'created'             => date('Y-m-d H:i:s'),      
                            'description'         => 'Recharge #'.$recharge_unique_id.' Amount Refund Credited.'
                        );

                        $this->db->insert('virtual_wallet',$wallet_data);

                    }
                        
                    $this->Az->redirect('distributor/recharge/mobilePrepaid', 'system_message_error',sprintf(lang('RECHARGE_FAILED'),$api_response['opt_msg']));
                         
                }
            }
            else
            {
                
                $this->Az->redirect('distributor/recharge/mobilePrepaid', 'system_message_error',lang('API_ERROR'));
                
            }
            
            
            
        }
    
    }



    public function dth(){

        
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(1, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }
        $loggedAccountID = $loggedUser['id'];

        $dth_operator = $this->db->get_where('operator',array('type'=>'DTH','status'=>1))->result_array();
        $circle = $this->db->get('circle')->result_array(); 
        
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'dth_operator' => $dth_operator,
            'circle'         => $circle,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'recharge/dth'
        );

        $this->parser->parse('distributor/layout/column-1' , $data);
    }




    public function dthRecharge()
    {   
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $accountData = $this->User->get_account_data($account_id);
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(1, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - DTH Recharge Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('operator', 'Operator', 'required');
        $this->form_validation->set_rules('cardNumber', 'Card Number', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric|callback_maximumCheck');
        
        if ($this->form_validation->run() == FALSE) {
            
            $this->dth();
        }
        else
        {   
            $chk_wallet_balance =$this->db->get_where('users',array('id'=>$loggedUser['id']))->row_array();
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Wallet Balance - '.$chk_wallet_balance['wallet_balance'].']'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);

            if($before_balance < $post['amount']){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Insufficient Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('distributor/recharge/dth', 'system_message_error',lang('WALLET_ERROR'));   

            }  

            $user_before_balance =  $before_balance;

            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];

            $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

            if($before_balance < $final_deduct_wallet_balance){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Minimum Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                
                $this->Az->redirect('distributor/recharge/dth', 'system_message_error',lang('MIN_WALLET_ERROR'));
                
            } 

            // check instantpay cogent api
            $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
            if($is_cogent_instantpay_api)
            {
                $admin_id = $this->User->get_admin_id();
                $admin_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
                if($admin_wallet_balance < $post['amount']){
                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Insufficient Virtual Wallet Error]'.PHP_EOL;
                    $this->User->generateLog($log_msg);
                    
                    $this->Az->redirect('distributor/recharge/dth', 'system_message_error',lang('ADMIN_WALLET_BALANCE_ERROR'));
                    
                }
            } 
            
            $response = $this->User->generate_api_url($loggedUser['id'],$post['operator'],$post['amount'],$loggedUser['id']);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Rechage API Generate Response - '.json_encode($response).']'.PHP_EOL;
            $this->User->generateLog($log_msg);
            if($response['status'] && $response['api_id'])
            {
                if($accountData['account_type'] == 2)
                {
                    // get operator code
                    $get_operator_code = $this->db->get_where('api_operator',array('account_id'=>SUPERADMIN_ACCOUNT_ID,'api_id'=>$response['api_id'],'opt_id'=>$post['operator']))->row_array();
                }
                else
                {
                    // get operator code
                    $get_operator_code = $this->db->get_where('api_operator',array('account_id'=>$account_id,'api_id'=>$response['api_id'],'opt_id'=>$post['operator']))->row_array();
                }
                $opt_code = isset($get_operator_code['opt_code']) ? $get_operator_code['opt_code'] : '';
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Rechage API Operator Code - '.$opt_code.']'.PHP_EOL;
                $this->User->generateLog($log_msg);

                
                $circle_code = 19;
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Rechage API Circle Code - '.$circle_code.']'.PHP_EOL;
                $this->User->generateLog($log_msg);

                // generate recharge unique id
                $recharge_unique_id = rand(1111,9999).time();
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Rechage Unique ID - '.$recharge_unique_id.']'.PHP_EOL;
                $this->User->generateLog($log_msg);

                $user_after_balance = $user_before_balance - $post['amount'];

                if($response['is_instantpay_api'])
                {
                    if(!$chk_wallet_balance['is_instantpay_ekyc'])
                    {
                        $this->Az->redirect('master/recharge/dth', 'system_message_error',sprintf(lang('RECHARGE_FAILED'),'Sorry ! Your eKyc not approved yet, please submit your eKyc.'));
                    }
                }

                // get system operator code
                $system_opt_id = $post['operator'];
                
                $data = array(
                    'account_id'         => $account_id,
                    'member_id'          => $loggedUser['id'],
                    'api_id'             => $response['api_id'],
                    'recharge_type'      => 2,
                    'recharge_display_id'=> $recharge_unique_id,
                    'mobile'             => $post['cardNumber'],
                    'account_number'     => isset($post['acnumber']) ? $post['acnumber'] : '',
                    'operator_code'      => $opt_code,
                    'system_opt_id'      => $system_opt_id,
                    'circle_code'        => $circle_code,
                    'amount'             => $post['amount'],
                    'before_balance'     => $user_before_balance,
                    'after_balance'      => $user_after_balance,
                    'status'             => 1,
                    'created'            => date('Y-m-d H:i:s')                  
                );


                $this->db->insert('recharge_history',$data);
                $recharge_id = $this->db->insert_id();
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Save Rechage Data System Recharge ID - '.$recharge_id.']'.PHP_EOL;
                $this->User->generateLog($log_msg);

                $before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);

                    
                $after_balance = $before_balance - $post['amount'];    

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedUser['id'],    
                    'before_balance'      => $before_balance,
                    'amount'              => $post['amount'],  
                    'after_balance'       => $after_balance,      
                    'status'              => 1,
                    'type'                => 2,      
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
                if($is_cogent_instantpay_api)
                {
                    $admin_id = $this->User->get_admin_id();
                    $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
                    $admin_after_wallet_balance = $admin_before_wallet_balance - $post['amount'];

                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $admin_id,    
                        'before_balance'      => $admin_before_wallet_balance,
                        'amount'              => $post['amount'],  
                        'after_balance'       => $admin_after_wallet_balance,      
                        'status'              => 1,
                        'type'                => 2,   
                        'wallet_type'         => 1,   
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
                    );

                    $this->db->insert('virtual_wallet',$wallet_data);

                    
                }
                
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Recharge Wallet Amount Deducted]'.PHP_EOL;
                $this->User->generateLog($log_msg);

                if($response['is_instantpay_api'])
                {
                    if($chk_wallet_balance['is_instantpay_ekyc'])
                    {
                        // call recharge API
                        $api_response = $this->User->instantpay_rechage_api($opt_code,$loggedUser['id'],$recharge_unique_id,$post['cardNumber'],$post['amount'],$response['api_id'],$loggedUser['user_code'],'D');
                    }
                    else
                    {
                        $api_response = array(
                            'status' => 3,
                            'opt_msg' => 'Sorry ! Your eKyc not approved yet, please submit your eKyc.'
                        );
                    }
                }
                else
                {
                    $api_url = $response['api_url'];
                    $api_post_data = $response['post_data'];
                    $api_url = str_replace('{AMOUNT}',$post['amount'],$api_url);
                    $api_url = str_replace('{OPERATOR}',$opt_code,$api_url);
                    $api_url = str_replace('{CIRCLE}',$circle_code,$api_url);
                    $api_url = str_replace('{TXNID}',$recharge_unique_id,$api_url);
                    $api_url = str_replace('{MOBILE}',$post['cardNumber'],$api_url);
                    $api_url = str_replace('{MEMBERID}',$loggedUser['user_code'],$api_url);

                    // replace post data
                    if($api_post_data)
                    {
                        foreach($api_post_data as $apikey=>$apival)
                        {
                            if($apival == '{AMOUNT}')
                            {
                                $api_post_data[$apikey] = $post['amount'];
                            }
                            elseif($apival == '{OPERATOR}')
                            {
                                $api_post_data[$apikey] = $opt_code;
                            }
                            elseif($apival == '{CIRCLE}')
                            {
                                $api_post_data[$apikey] = $circle_code;
                            }
                            elseif($apival == '{TXNID}')
                            {
                                $api_post_data[$apikey] = $recharge_unique_id;
                            }
                            elseif($apival == '{MOBILE}')
                            {
                                $api_post_data[$apikey] = $post['cardNumber'];
                            }
                            elseif($apival == '{MEMBERID}')
                            {
                                $api_post_data[$apikey] = $loggedUser['user_code'];
                            }
                        }
                    }

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Final API URL - '.$api_url.' - Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
                    $this->User->generateLog($log_msg);

                    // call recharge API
                    $api_response = $this->User->prepaid_rechage_api($api_url,$api_post_data,$loggedUser['id'],$recharge_unique_id,$response['api_id'],$response['response_type'],$response['responsePara'],$response['seperator'],$response['header_data'],$loggedUser['user_code'],'D');
                }

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - API Final Response - '.json_encode($api_response).']'.PHP_EOL;
                $this->User->generateLog($log_msg);
                
                if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
                {
                    
                    if($api_response['status'] == 1){
                        // update recharge status
                        $this->db->where('id',$recharge_id);
                        $this->db->where('recharge_display_id',$recharge_unique_id);
                        $this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
                        
                        $this->Az->redirect('distributor/recharge/dth', 'system_message_error',lang('RECHARGE_PENDING'));   
                          
                    }
                    elseif($api_response['status'] == 2)
                    {
                        // update recharge status
                        $this->db->where('id',$recharge_id);
                        $this->db->where('recharge_display_id',$recharge_unique_id);
                        $this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));

                        // save system log
                        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Distribute Commision Start]'.PHP_EOL;
                        $this->User->generateLog($log_msg);
                        // distribute commision
                        $this->User->distribute_recharge_commision($recharge_id,$recharge_unique_id,$post['amount'],$loggedUser['id']);
                        // save system log
                        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Distribute Commision End]'.PHP_EOL;
                        $this->User->generateLog($log_msg);

                        $this->Az->redirect('distributor/recharge/dth', 'system_message_error',lang('RECHARGE_SUCCESS'));   
                          
                    }
                }
                else
                {
                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Recharge Failed]'.PHP_EOL;
                    $this->User->generateLog($log_msg);
                    // update recharge status
                    $this->db->where('id',$recharge_id);
                    $this->db->where('recharge_display_id',$recharge_unique_id);
                    $this->db->update('recharge_history',array('status'=>3));

                    $before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);

                    
                    $after_balance = $before_balance + $post['amount'];    

                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $loggedUser['id'],    
                        'before_balance'      => $before_balance,
                        'amount'              => $post['amount'],  
                        'after_balance'       => $after_balance,      
                        'status'              => 1,
                        'type'                => 1,      
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'Recharge #'.$recharge_unique_id.' Amount Refund Credited.'
                    );

                    $this->db->insert('member_wallet',$wallet_data);


                    if($is_cogent_instantpay_api)
                    {
                        $admin_id = $this->User->get_admin_id();
                        $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
                        $admin_after_wallet_balance = $admin_before_wallet_balance + $post['amount'];

                        $wallet_data = array(
                            'account_id'          => $account_id,
                            'member_id'           => $admin_id,    
                            'before_balance'      => $admin_before_wallet_balance,
                            'amount'              => $post['amount'],  
                            'after_balance'       => $admin_after_wallet_balance,      
                            'status'              => 1,
                            'type'                => 1,   
                            'wallet_type'         => 1,   
                            'created'             => date('Y-m-d H:i:s'),      
                            'description'         => 'Recharge #'.$recharge_unique_id.' Amount Refund Credited.'
                        );

                        $this->db->insert('virtual_wallet',$wallet_data);

                        
                    }
                        
                    $this->Az->redirect('distributor/recharge/dth', 'system_message_error',sprintf(lang('RECHARGE_FAILED'),$api_response['opt_msg']));
                         
                }
            }
            else
            {
                $this->Az->redirect('distributor/recharge/dth', 'system_message_error',lang('API_ERROR'));
            }
            
            

            
            
        }
    
    }



    public function electricity(){

        
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(1, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }
        $electricity_operator = $this->db->get_where('operator',array('type'=>'Electricity'))->result_array();
        $circle = $this->db->get('circle')->result_array(); 
        
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'electricity_operator' => $electricity_operator,
            'circle'         => $circle,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'recharge/electricity'
        );

        $this->parser->parse('distributor/layout/column-1' , $data);
    }



    public function fetchBiller($operator_id = '')
    {
        
        // get operator code
        $get_operator_code = $this->db->get_where('operator',array('id'=>$operator_id))->row_array();
        $operator_code = isset($get_operator_code['operator_code']) ? $get_operator_code['operator_code'] : '';
        $response = $this->User->getElectricityOperatorDetail($operator_code);
        
        echo json_encode($response);
    }
    
    public function fetchBillerDetail($operator_id = '')
    {
        // get operator code
        $get_operator_code = $this->db->get_where('operator',array('id'=>$operator_id))->row_array();
        $operator_code = isset($get_operator_code['operator_code']) ? $get_operator_code['operator_code'] : '';
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $post = $this->input->post();
        $account_number = isset($post['account_number']) ? $post['account_number'] : '';
        $response = $this->User->getElectricityOperatorBillerDetail($operator_code,$account_number,$loggedAccountID);
        
        echo json_encode($response);
    }
    
    
    
    public function electricityBill()
    {   
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(1, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }
        //check for foem validation
        $post = $this->input->post();
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Electicity Post Data - '.json_encode($post).']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('operator', 'Operator', 'required');
        $this->form_validation->set_rules('fetch_status', 'fetch_status', 'xss_clean');
        $this->form_validation->set_rules('fieldName', 'fieldName', 'xss_clean');
        if($post['fetch_status'])
        {
            $this->form_validation->set_rules('account_number', $post['fieldName'], 'required');
        }
        
        if ($this->form_validation->run() == FALSE) {
            
            $this->electricity();
        }
        else
        {
            if($post['fetch_status'] == 0)
            {
                $this->Az->redirect('distributor/recharge/electricity', 'system_message_error',lang('OPERATOR_VALID_ERROR'));   
            }

            // get admin data
            /*$admin_id = $this->User->get_admin_id();
            $admin_wallet_balance = $this->User->get_admin_ewallet_balance($admin_id);
            
            if($admin_wallet_balance < $post['amount']){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Insufficient Wallet Error in Admin Account.]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('distributor/recharge/electricity', 'system_message_error',lang('ADMIN_WALLET_BALANCE_ERROR'));
            } */
            
            $chk_wallet_balance =$this->db->select('min_wallet_balance')->get_where('users',array('id'=>$loggedUser['id']))->row_array();

            $user_before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
            if($user_before_balance < $post['amount']){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Insufficient wallet error.]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                $this->Az->redirect('distributor/recharge/electricity', 'system_message_error',lang('WALLET_ERROR'));   

            }  

            
            $min_wallet_balance = $chk_wallet_balance['min_wallet_balance'];

            $final_deduct_wallet_balance = $min_wallet_balance + $post['amount'];

            if($user_before_balance < $final_deduct_wallet_balance){
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Minimum Wallet Error]'.PHP_EOL;
                $this->User->generateLog($log_msg);
                
                $this->Az->redirect('distributor/recharge/electricity', 'system_message_error',lang('MIN_WALLET_ERROR'));
                
            } 
            
            #$this->Az->redirect('electricity', 'system_message_error',lang('RECHARGE_DOWN_ERROR'));   
            
            // generate recharge unique id
            $recharge_unique_id = rand(1111,9999).time();

            // get operator code
            $get_operator_code = $this->db->get_where('operator',array('id'=>$post['operator']))->row_array();
            $operator_code = isset($get_operator_code['operator_code']) ? $get_operator_code['operator_code'] : '';

            $user_after_balance = $user_before_balance - $post['amount'];

            // get system operator code
            $system_opt_id = $post['operator'];

            $data = array(
                'account_id' => $account_id,
                'member_id'          => $loggedAccountID,
                'recharge_type'      => 7,
                'recharge_display_id'=> $recharge_unique_id,
                'mobile'             => $loggedUser['mobile'],
                'account_number'     => isset($post['account_number']) ? $post['account_number'] : '',
                'operator_code'      => $operator_code,
                'system_opt_id'      => $system_opt_id,
                'amount'             => $post['amount'],
                'before_balance'     => $user_before_balance,
                'after_balance'      => $user_after_balance,
                'status'             => 1,
                'field_name'             => $post['fieldName'],
                'reference_id'             => $post['reference_id'],
                'customer_name'             => $post['customer_name'],
                'created'            => date('Y-m-d H:i:s')                  
            );

            $this->db->insert('recharge_history',$data);
            $recharge_id = $this->db->insert_id();

            $before_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);

            $after_balance = $before_balance - $post['amount'];    

            $wallet_data = array(
                'account_id' => $account_id,
                'member_id'           => $loggedUser['id'],    
                'before_balance'      => $before_balance,
                'amount'              => $post['amount'],  
                'after_balance'       => $after_balance,      
                'status'              => 1,
                'type'                => 2,  
                'wallet_type'         => 1,     
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Deducted.'
            );

            $this->db->insert('member_wallet',$wallet_data);

            
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Record Saved Successfully.]'.PHP_EOL;
            $this->User->generateLog($log_msg);
            
            $account_number = $post['account_number'];
            $amount = $post['amount'];
            $reference_id = $post['reference_id'];
            $customer_mobile = $post['customer_name'];
            // call recharge API
            $api_response = $this->User->electricity_rechage_api($account_number,$operator_code,$amount,$reference_id,$recharge_unique_id,$loggedUser['id'],$loggedUser['mobile'],$customer_mobile);
            

            if(isset($api_response['status']) && ($api_response['status'] == 1 || $api_response['status'] == 2))
            {
                
                if($api_response['status'] == 1){
                    // update recharge status
                    $this->db->where('id',$recharge_id);
                    $this->db->where('recharge_display_id',$recharge_unique_id);
                    $this->db->update('recharge_history',array('txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
                    $this->Az->redirect('distributor/recharge/electricity', 'system_message_error',lang('RECHARGE_PENDING'));
                }
                else
                {
                    // update recharge status
                    $this->db->where('id',$recharge_id);
                    $this->db->where('recharge_display_id',$recharge_unique_id);
                    $this->db->update('recharge_history',array('status'=>2,'txid'=>$api_response['txid'],'operator_ref'=>$api_response['operator_ref'],'api_response_id'=>$api_response['api_response_id']));
                    
                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Distribute Commision Start]'.PHP_EOL;
                    $this->User->generateLog($log_msg);
                    // distribute commision
                    $this->User->distribute_electricity_commision($recharge_id,$recharge_unique_id,$post['amount'],$loggedUser['id']);
                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - D('.$loggedUser['user_code'].') - Distribute Commision End]'.PHP_EOL;
                    $this->User->generateLog($log_msg);

                    $this->Az->redirect('distributor/recharge/electricity', 'system_message_error',lang('RECHARGE_SUCCESS'));
                }
            }
            else
            {
                // update recharge status
                $this->db->where('id',$recharge_id);
                $this->db->where('recharge_display_id',$recharge_unique_id);
                $this->db->update('recharge_history',array('status'=>3));
                $this->Az->redirect('distributor/recharge/electricity', 'system_message_error',lang('RECHARGE_FAILED'));
            }
            
            

            
            
        }
    
    }
    

    public function getOperatorPlanList()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $response = array();
        $post = $this->input->post();
        if(!isset($post['offerOperator']) || !$post['offerOperator'])
        {
            $response = array(
                'status' => 0,
                'msg' => 'Please select operator'
            );
        }
        else
        {
            if(!isset($post['offerCircle']) || !$post['offerCircle'])
            {
                $response = array(
                    'status' => 0,
                    'msg' => 'Please select circle'
                );
            }
            else
            {
                $op_id = $post['offerOperator'];
                $circle_id = $post['offerCircle'];

                // get operator name
                $get_operator_name = $this->db->select('operator_name')->get_where('operator',array('id'=>$op_id))->row_array();
                $operator_name = isset($get_operator_name['operator_name']) ? $get_operator_name['operator_name'] : '';

                if($op_id == 3 || $op_id == 4 || $op_id == 11)
                {
                    $operator_name = 'BSNL';
                }

                // get circle name
                $get_circle_name = $this->db->select('circle_name')->get_where('circle',array('id'=>$circle_id))->row_array();
                $circle_name = isset($get_circle_name['circle_name']) ? $get_circle_name['circle_name'] : '';

                $api_url = PLAN_FINDER_API_URL;
                
                $headers = [
                    'Token: '.$accountData['dmt_token'],
                    'Content-Type: application/x-www-form-urlencoded'
                ];

                $api_post_data = array();
                $api_post_data['oparetorName'] = $operator_name;
                $api_post_data['circleName'] = $circle_name;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));     
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $output = curl_exec ($ch);
                curl_close ($ch);

                $plan = json_decode($output,true);

                $str = '';
                $str2 = '';
                $is_error = 0;
                if(isset($plan['Error']) && $plan['Error'] == 'False')
                {
                    $records = isset($plan['Data']['records']) ? $plan['Data']['records'] : array();
                    if($records)
                    {
                        $str.='<ul class="nav nav-tabs" id="myTab" role="tablist">';
                        $str2.='<div class="tab-content apidoc" id="myTabContent">';
                        $i = 0;
                        foreach($records as $tabKey=>$tabData)
                        {
                            if($i == 0)
                            {
                                $str.='<li class="nav-item"><a class="nav-link active" id="tab-'.$i.'" data-toggle="tab" href="#tab'.$i.'" role="tab" aria-controls="operator" aria-selected="true">'.$tabKey.'</a></li>';
                                $str2.='<div class="tab-pane fade show active" id="tab'.$i.'" role="tabpanel" aria-labelledby="operator-tab">';
                            }
                            else
                            {
                                $str.='<li class="nav-item"><a class="nav-link" id="tab-'.$i.'" data-toggle="tab" href="#tab'.$i.'" role="tab" aria-controls="operator" aria-selected="true">'.$tabKey.'</a></li>';
                                $str2.='<div class="tab-pane fade" id="tab'.$i.'" role="tabpanel" aria-labelledby="operator-tab">';
                            }
                            $str2.='<div class="col-sm-12">';
                            $str2.='<br />';
                            $str2.='<table class="table table-bordered table-striped">';
                            $str2.='<tr>';
                            $str2.='<th>#</th>';
                            $str2.='<th>Amount</th>';
                            $str2.='<th>Description</th>';
                            $str2.='<th>Validity</th>';
                            $str2.='</tr>';
                            
                            if($tabData)
                            {
                                foreach($tabData as $planKey=>$planData)
                                {
                                    $str2.='<tr>';
                                    $str2.='<td><a href="#" class="btn btn-primary" onclick="offerAmountPick('.$planData['rs'].');">Select</a></td>';
                                    $str2.='<td>&#8377; '.$planData['rs'].'</td>';
                                    $str2.='<td>'.$planData['desc'].'</td>';
                                    $str2.='<td>'.$planData['validity'].'</td>';
                                    $str2.='</tr>';
                                }
                            }
                            $str2.='</table>';
                            $str2.='</div>';
                            $str2.='</div>';
                            $i++;
                        }
                        $str.='</ul>';
                        $str2.='</div>';
                    }
                }
                else
                {
                    $is_error = 1;
                }

                if($is_error)
                {
                    $response = array(
                        'status' => 0,
                        'msg' => 'Something went wrong, please try again later.'
                    );
                }
                else
                {
                    $response = array(
                        'status' => 1,
                        'msg' => 'Success',
                        'str' => $str.$str2
                    );
                }
            }
        }

        echo json_encode($response);
    }


    
    public function getRofferList()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $response = array();
        $post = $this->input->post();
        if(!isset($post['rofferOperator']) || !$post['rofferOperator'])
        {
            $response = array(
                'status' => 0,
                'msg' => 'Please select operator'
            );
        }
        else
        {
            if(!isset($post['roffermobile']) || !$post['roffermobile'])
            {
                $response = array(
                    'status' => 0,
                    'msg' => 'Please enter mobile no.'
                );
            }
            else
            {
                $op_id = $post['rofferOperator'];
                $mobile = $post['roffermobile'];

                // get operator name
                $get_operator_name = $this->db->select('operator_name')->get_where('operator',array('id'=>$op_id))->row_array();
                $operator_name = isset($get_operator_name['operator_name']) ? $get_operator_name['operator_name'] : '';

                if($op_id == 3 || $op_id == 4 || $op_id == 11)
                {
                    $operator_name = 'BSNL';
                }


                // get operator name
                $get_operator_name = $this->db->select('operator_name')->get_where('operator',array('id'=>$op_id))->row_array();
                $operator_name = isset($get_operator_name['operator_name']) ? $get_operator_name['operator_name'] : '';

                $api_url = ROFFER_API_URL;
                    
                $headers = [
                    'Token: '.$accountData['dmt_token'],
                    'Content-Type: application/x-www-form-urlencoded'
                ];

                $api_post_data = array();
                $api_post_data['operatorName'] = $operator_name;
                $api_post_data['Mobile'] = $mobile;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));     
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $output = curl_exec ($ch);
                curl_close ($ch);

                $plan = json_decode($output,true);

                $str = '';
                $str2 = '';
                $is_error = 0;
                if(isset($plan['Error']) && $plan['Error'] == 'False')
                {
                    $records = isset($plan['Data']['records']) ? $plan['Data']['records'] : array();
                    if($records)
                    {
                        $str2.='<div class="col-sm-12">';
                        $str2.='<br />';
                        $str2.='<table class="table table-bordered table-striped">';
                        $str2.='<tr>';
                        $str2.='<th>#</th>';
                        $str2.='<th>Amount</th>';
                        $str2.='<th>Description</th>';
                        $str2.='</tr>';
                        $i = 0;
                        foreach($records as $tabKey=>$planData)
                        {
                            
                            $str2.='<tr>';
                            $str2.='<td><a href="#" class="btn btn-primary" onclick="rofferAmountPick('.$planData['rs'].');">Select</a></td>';
                            $str2.='<td>&#8377; '.$planData['rs'].'</td>';
                            $str2.='<td>'.$planData['desc'].'</td>';
                            $str2.='</tr>';
                            
                            
                            $i++;
                        }
                        $str2.='</table>';
                        $str2.='</div>';
                    }
                }
                else
                {
                    $is_error = 1;
                }

                if($is_error)
                {
                    $response = array(
                        'status' => 0,
                        'msg' => 'Something went wrong, please try again later.'
                    );
                }
                else
                {
                    $response = array(
                        'status' => 1,
                        'msg' => 'Success',
                        'str' => $str2
                    );
                }
            }
        }

        echo json_encode($response);
    }

    public function getOperatorType($mobile = '')
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $response = array();
        $post = $this->input->post();
        if($mobile == '')
        {
            $response = array(
                'status' => 0,
                'msg' => 'Please enter mobile no.'
            );
        }
        else
        {
            
            
            $api_url = OPERATOR_FINDER_API_URL;
            
            $headers = [
                'Token: '.$accountData['dmt_token'],
                'Content-Type: application/x-www-form-urlencoded'
            ];

            $api_post_data = array();
            $api_post_data['Mobile'] = $mobile;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));     
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $output = curl_exec ($ch);
            curl_close ($ch);
            $plan = json_decode($output,true);

            $is_error = 0;
            $operator_id = 0;
            if(isset($plan['Error']) && $plan['Error'] == 'False')
            {
                $records = isset($plan['Data']['records']) ? $plan['Data']['records'] : array();
                if($records)
                {
                    $operator = $records['Operator'];
                    // get operator name
                    $get_operator_name = $this->db->select('id')->get_where('operator',array('operator_name'=>$operator))->row_array();
                    $operator_id = isset($get_operator_name['id']) ? $get_operator_name['id'] : '';
                }
            }
            else
            {
                $is_error = 1;
            }

            if($is_error)
            {
                $response = array(
                    'status' => 0,
                    'msg' => 'Something went wrong, please try again later.'
                );
            }
            else
            {
                $response = array(
                    'status' => 1,
                    'msg' => 'Success',
                    'operator_id' => $operator_id
                );
            }
            
        }

        echo json_encode($response);
    }

        


     public function getDthOperatorPlanList()
    {   
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $response = array();
        $post = $this->input->post();
        
        if(!isset($post['offerOperator']) || !$post['offerOperator'])
        {
            $response = array(
                'status' => 0,
                'msg' => 'Please select operator'
            );
        }
        else
        {
            
                $op_id = $post['offerOperator'];

                // get operator name
                $get_operator_name = $this->db->select('plan_code')->get_where('operator',array('id'=>$op_id))->row_array();
                $operator_name = isset($get_operator_name['plan_code']) ? $get_operator_name['plan_code'] : '';

               
                $api_url = DTH_PLAN_FINDER_API_URL;
                
                $headers = [
                    'Token: '.$accountData['dmt_token'],
                    'Content-Type: application/x-www-form-urlencoded'
                ];

                $api_post_data = array();
                $api_post_data['oparetorName'] = $operator_name;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));     
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $output = curl_exec ($ch);
                curl_close ($ch);
                $plan = json_decode($output,true);

                
                
                $str = '';
                $str2 = '';
                $str2.='<div class="col-sm-12">';
                $str2.='<br />';
                $str2.='<table class="table table-bordered table-striped">';
                $str2.='<tr>';
                $str2.='<th>#</th>';
                $str2.='<th>Amount</th>';
                $str2.='<th>Description</th>';
                $str2.='<th>Validity</th>';
                $str2.='</tr>';
                $is_error = 0;
                if(isset($plan['Error']) && $plan['Error'] == 'False')
                {
                    $records = isset($plan['Data']['records']['Plan']) ? $plan['Data']['records']['Plan'] : array();
                    if($records)
                    {
                        
                        $i = 0;
                        foreach($records as $tabKey=>$tabData)
                        {
                            if($tabData['rs'])
                            {
                                foreach($tabData['rs'] as $validity=>$amount)
                                {
                                    $str2.='<tr>';
                                    $str2.='<td><a href="#" class="btn btn-primary" onclick="offerAmountPick('.$amount.');">Select</a></td>';
                                    $str2.='<td>&#8377; '.$amount.'</td>';
                                    $str2.='<td>'.$tabData['desc'].'</td>';
                                    $str2.='<td>'.$validity.'</td>';
                                    $str2.='</tr>';
                                }
                             }  
                            
                            $i++;
                        }
                        $str2.='</table>';
                            $str2.='</div>';
                            $str2.='</div>';
                }
                else
                {
                    $is_error = 1;
                }

                if($is_error)
                {
                    $response = array(
                        'status' => 0,
                        'msg' => 'Something went wrong, please try again later.'
                    );
                }
                else
                {
                    $response = array(
                        'status' => 1,
                        'msg' => 'Success',
                        'str' => $str2
                    );
                }
            }
        }

        echo json_encode($response);
    }



    public function getDTHCustomerInfo()
    {   
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $response = array();
        $post = $this->input->post();

        if($post['operator'] == ''){

            $response = array(

               'status' => 0,
               'msg'    => 'Please select operator' 
            );
        }
        elseif($post['cardNumber'] == ''){

            $response = array(

               'status' => 0,
               'msg'    => 'Please enter card number.' 
            );

        }
        else{

            $url = DTH_BILLER_DETAIL_API_URL;

            $card_number = $post['cardNumber'];
            $operator_code = $post['operator'];

            $get_operator = $this->db->get_where('operator',array('id'=>$operator_code))->row_array();

            $operator = $get_operator['offer_code'];

            $ch = curl_init();

                $headers = array(
                   "Content-Type: application/x-www-form-urlencoded",
                   "Token : ".$accountData['dmt_token'], 
                );

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                'VCnumber='.$card_number.'&operatorName='.$operator.'');
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);  
                $output = curl_exec($ch); 
                curl_close($ch);
                
                    

                $api_response = json_decode($output,true);
                
                $response = array();
                $detail = (array) $api_response['Data']['records'][0];
                if($api_response['Error'] == "False" && $api_response['Message'] == "Success" && isset($detail['customerName'])){
                  
                    $response = array(

                        'status' => 1,
                        'msg' => 'success',
                        'customerName' => $detail['customerName'],
                        'monthlyRechargeAmount' => $detail['monthlyRechargeAmount'],
                        'balance' =>  $detail['Balance'] 
                    
                    );
                
                }
                else{

                    $response = array(

                      'status' => 0,
                      'msg'=> 'Sorry!! Biller is not valid.'        

                    );
                }
            }

            echo json_encode($response);
    }

    public function getRechargeData($recharge_id = 0)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);

        $response = array();
        if(!$recharge_id || $recharge_id == '')
        {
            $response = array(
                'status' => 0,
                'msg' => 'Sorry ! Something went wrong.'
            );
        }
        else
        {
            // check recharge is valid or not
            $chk_recharge = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->num_rows();
            if(!$chk_recharge)
            {
                $response = array(
                    'status' => 0,
                    'msg' => 'Sorry ! Something went wrong.'
                );
            }
            else
            {
                $chk_recharge = $this->db->select('recharge_display_id,amount')->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->row_array();

                
                $response = array(
                    'status' => 1,
                    'msg' => 'Success',
                    'txnid' => $chk_recharge['recharge_display_id'],
                    'amount' => $chk_recharge['amount'],
                );
                
            }
        }

        echo json_encode($response);
    }

    public function getBBPSData($recharge_id = 0)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);

        $response = array();
        if(!$recharge_id || $recharge_id == '')
        {
            $response = array(
                'status' => 0,
                'msg' => 'Sorry ! Something went wrong.'
            );
        }
        else
        {
            // check recharge is valid or not
            $chk_recharge = $this->db->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->num_rows();
            if(!$chk_recharge)
            {
                $response = array(
                    'status' => 0,
                    'msg' => 'Sorry ! Something went wrong.'
                );
            }
            else
            {
                $chk_recharge = $this->db->select('recharge_display_id,amount')->get_where('bbps_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->row_array();

                
                $response = array(
                    'status' => 1,
                    'msg' => 'Success',
                    'txnid' => $chk_recharge['recharge_display_id'],
                    'amount' => $chk_recharge['amount'],
                );
                
            }
        }

        echo json_encode($response);
    }

    // save member
    public function complainAuth()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //check for foem validation
        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('recordID', 'Member Type', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Name', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            
            $this->Az->redirect('distributor/report/recharge', 'system_message_error',lang('FORM_ERROR'));
        }
        else
        {   
            $recharge_id = $post['recordID'];
            // check recharge is valid or not
            $chk_recharge = $this->db->get_where('recharge_history',array('id'=>$recharge_id,'account_id'=>$account_id,'member_id'=>$loggedUser['id']))->num_rows();
            if(!$chk_recharge)
            {

                $this->Az->redirect('distributor/report/recharge', 'system_message_error',lang('AUTHORIZE_ERROR'));  

            }

            $status = $this->Complain_model->saveComplain($post);
            
            if($status == true)
            {
                $this->Az->redirect('distributor/report/recharge', 'system_message_error',lang('COMPLAIN_SAVED'));
            }
            else
            {
                $this->Az->redirect('distributor/report/recharge', 'system_message_error',lang('COMMON_ERROR'));
            }
            
        }
    
    }

    public function ekyc(){

        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(1, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }
        
        $isInstantPayApiAllow = $this->User->get_account_instantpay_api_status($account_id);
        if(!$isInstantPayApiAllow)
        {
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }

        $userData = $this->db->select('email,mobile,is_instantpay_ekyc')->get_where('users',array('id'=>$loggedUser['id'],'account_id'=>$account_id))->row_array();

        $getAadharData = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$loggedUser['id'],'status'=>2))->row_array();
        $aadhar_data = isset($getAadharData['aadhar_data']) ? json_decode($getAadharData['aadhar_data'],true) : array();

        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'userData' => $userData,
            'aadhar_data' => $aadhar_data,
            'getAadharData' => $getAadharData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'recharge/ekyc'
        );

        $this->parser->parse('distributor/layout/column-1' , $data);
    }

    public function ekycAuth()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //check for foem validation
        $post = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|xss_clean|numeric|min_length[10]');
        $this->form_validation->set_rules('email', 'Email', 'required|xss_clean|valid_email');
        $this->form_validation->set_rules('pancard', 'PAN Card', 'required|xss_clean|min_length[10]');
        $this->form_validation->set_rules('aadhar', 'Aadhar Card', 'required|xss_clean|min_length[12]');
        if ($this->form_validation->run() == FALSE) {
            
            $this->ekyc();
            return false;
        }
        else
        {   
            $isInstantPayApiAllow = $this->User->get_account_instantpay_api_status($account_id);
            if(!$isInstantPayApiAllow)
            {
                $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
            }
            

            $response = $this->Master_model->saveeKycData($post);
            
            if($response['status'] == 1)
            {
                $msg = '<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$response['msg'].'</div>';
                $this->Az->redirect('distributor/recharge/ekycOtp/'.$response['otpReferenceID'], 'system_message_error',$msg);
            }
            else
            {
                $msg = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$response['msg'].'</div>';
                $this->Az->redirect('distributor/recharge/ekyc', 'system_message_error',$msg);
            }
            
        }
    
    }

    public function ekycOtp($otpReferenceID = ''){

        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //get logged user info
        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(1, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }
        
        $isInstantPayApiAllow = $this->User->get_account_instantpay_api_status($account_id);
        if(!$isInstantPayApiAllow)
        {
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }

        $chkRefID = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$loggedUser['id'],'otpReferenceID'=>$otpReferenceID,'status'=>1))->num_rows();
        if(!$chkRefID)
        {
            $msg = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized.</div>';
            $this->Az->redirect('distributor/recharge/ekyc', 'system_message_error',$msg);
        }

        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'otpReferenceID' => $otpReferenceID,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'recharge/ekyc-otp'
        );

        $this->parser->parse('distributor/layout/column-1' , $data);
    }

    public function ekycOtpAuth()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //check for foem validation
        $post = $this->input->post();
        $otpReferenceID = $post['otpReferenceID'];
        $this->load->library('form_validation');
        $this->form_validation->set_rules('otp_code', 'OTP', 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            
            $this->ekycOtp($otpReferenceID);
            return false;
        }
        else
        {   
            $isInstantPayApiAllow = $this->User->get_account_instantpay_api_status($account_id);
            if(!$isInstantPayApiAllow)
            {
                $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
            }

            $chkRefID = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$loggedUser['id'],'otpReferenceID'=>$otpReferenceID,'status'=>1))->num_rows();
            if(!$chkRefID)
            {
                $msg = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized.</div>';
                $this->Az->redirect('distributor/recharge/ekyc', 'system_message_error',$msg);
            }
            

            $response = $this->Master_model->verifyeKyc($post);
            
            if($response['status'] == 1)
            {
                $msg = '<div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$response['msg'].'</div>';
                $this->Az->redirect('distributor/recharge/ekyc', 'system_message_error',$msg);
            }
            else
            {
                $msg = '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$response['msg'].'</div>';
                $this->Az->redirect('distributor/recharge/ekycOtp/'.$otpReferenceID, 'system_message_error',$msg);
            }
            
        }
    
    }

    
    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */