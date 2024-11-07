<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Model used for setup default message and resize image
 * 
 * This one used for defined some methods accross all site.
 * this one used for show system message, errors.
 * this one used for image resizing
 * @author trilok
 */

require_once BASEPATH . '/core/Model.php';

class Dmt_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function memberActivation($post)
    {
        $account_id = $this->User->get_domain_account();
         $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $memberData = $this->db->select('user_code,name,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $token = do_hash(time().rand(1111,9999));

        // save activation data
        $data = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'first_name' => $post['first_name'],
            'last_name' => $post['last_name'],
            'user_code' => $memberData['user_code'],
            'mobile' => $post['mobile'],
            'dob' => $post['dob'],
            'address' => $post['address'],
            'pin_code' => $post['pin_code'],
            'token' => $token,
            'status' => 0,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $loggedAccountID
        );
        $this->db->insert('user_paysprint_dmt_activation',$data);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - DMT Record Saved.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);
        
        $post_data = array(
            'mobile' => $post['mobile'],
            'bank3_flag' => "no"
        );

                                $key = $accountData['PAYSPRINT_AEPS_KEY'];
                                $iv = $accountData['PAYSPRINT_AEPS_IV'];
                                $cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                                $body=       base64_encode($cipher);
                                $jwt_payload = array(
                                    'timestamp'=>time(),
                                    'partnerId'=>$accountData['PAYSPRINT_PARTNER_ID'],
                                    'reqid'=>time().rand(1111,9999)
                                );
                                
                                $secret = $accountData['PAYSPRINT_SECRET_KEY'];

                                $paysprint_token = $this->Jwt_model->encode($jwt_payload,$secret);
                                
                                //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
                                
                                $header = [
                                    'Token:'.$paysprint_token, 
                                    //'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY                              
                                ];



             $api_url = PAYSPRINT_DMT_REMITTER_CHECK_API_URL;
            
            log_message('debug', 'Remiter Check  api API Url - '.$api_url);

            log_message('debug', 'Remiter Check api post data - '.json_encode($post_data));

            log_message('debug', 'Remiter Check api header data - '.json_encode($header));


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  
        curl_setopt($ch,CURLOPT_HTTPHEADER , $header);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);


        log_message('debug', 'Remiter Check api response - '.json_encode($output));

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - Paysprint DMT Check Remitter API Response - '.$output.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        



        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $output,
            'api_url' => $api_url,
            'api_post_data' => json_encode($post_data),
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($output,true);

        // 0 = Error
        // 1 = Success
        
        $status = 0;
        $api_response = array();

        if(isset($responseData['response_code']) && $responseData['response_code'] == 1)
        {
            
                // update status
                /*$this->db->where('token',$token);
                $this->db->update('user_paysprint_dmt_activation',array('stateresp'=>$responseData['stateresp']));*/
                $api_response = array(
                    'status' => 2,
                    'message' => isset($responseData['message']) ? $responseData['message'] : '',
                    'token' => $token,
                    'firstName' => $responseData['data']['fname'],
                    'lastName' => $responseData['data']['lname'],
                    'mobile' => $responseData['data']['mobile'],                    
                    'bank3_limit' => $responseData['data']['bank3_limit'],
                    'bank2_limit' => $responseData['data']['bank2_limit'],
                    'bank1_limit' => $responseData['data']['bank1_limit']
                );
            /*            else
            {
                // update status
                $this->db->where('token',$token);
                $this->db->update('user_paysprint_dmt_activation',array('status'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));
                $api_response = array(
                    'status' => 2,
                    'message' => isset($responseData['status_msg']) ? $responseData['status_msg'] : '',
                    'token' => $token
                );
            }*/
        }
        elseif(isset($responseData['message']) && $responseData['message'] == 'Remitter not registered OTP sent for new registration.')
            {
                 $this->db->where('token',$token);
                $this->db->update('user_paysprint_dmt_activation',array('stateresp'=>$responseData['stateresp']));

                $api_response = array(
                    'status'=>1,                    
                    'status_msg' => $responseData['message'],
                    'stateresp' => $responseData['stateresp'],
                    'token' =>$token
                    
                );
            }

        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['message']) ? $responseData['message'] : ''
            );
        }
        
        return $api_response;
    }

    public function memberFetchDetail($post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $post_data = array(
            'mobile' => $post['mobile'],
            'bank3_flag' => "no"
        );

                                $key = $accountData['PAYSPRINT_AEPS_KEY'];
                                $iv = $accountData['PAYSPRINT_AEPS_IV'];
                                $cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                                $body=       base64_encode($cipher);
                                $jwt_payload = array(
                                    'timestamp'=>time(),
                                    'partnerId'=>$accountData['PAYSPRINT_PARTNER_ID'],
                                    'reqid'=>time().rand(1111,9999)
                                );
                                
                                $secret = $accountData['PAYSPRINT_SECRET_KEY'];

                                $paysprint_token = $this->Jwt_model->encode($jwt_payload,$secret);
                                
                                //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
                                
                                $header = [
                                    'Token:'.$paysprint_token, 
                                    //'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY                              
                                ];

                                $api_url = PAYSPRINT_DMT_REMITTER_CHECK_API_URL;

                                log_message('debug', 'Remiter Check  api API Url - '.$api_url);

                                log_message('debug', 'Remiter Check api post data - '.json_encode($post_data));

                                log_message('debug', 'Remiter Check api header data - '.json_encode($header));


                                
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL,$api_url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  
                                curl_setopt($ch,CURLOPT_HTTPHEADER , $header);  
                                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                                $output = curl_exec ($ch);
                                curl_close ($ch);

                                  log_message('debug', 'Remiter Check api output - '.json_encode($output));


        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - Paysprint DMT Check Remitter API Response - '.$output.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $output,
            'api_url' => $api_url,
            'api_post_data' => json_encode($post_data),
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($output,true);

        // 0 = Error
        // 1 = Success
        
        $status = 0;
        $api_response = array();

        if(isset($responseData['response_code']) && $responseData['response_code'] == 0)
        {
            if(isset($responseData['stateresp']))
            {
                $api_response = array(
                    'status' => 0,
                    'message' => isset($responseData['message']) ? $responseData['message'] : ''
                );
            }
            else
            {
                $memberData = $this->db->select('user_code,name,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();

                $token = do_hash(time().rand(1111,9999));

                // save activation data
                $data = array(
                    'account_id' => $account_id,
                    'member_id' => $loggedAccountID,
                    'user_code' => $memberData['user_code'],
                    'first_name' => $responseData['firstName'],
                    'last_name' => $responseData['lastName'],
                    'mobile' => $responseData['mobile'],
                    'token' => $token,
                    'status' => 1,
                    'created' => date('Y-m-d H:i:s'),
                    'created_by' => $loggedAccountID
                );
                $this->db->insert('user_paysprint_dmt_activation',$data);

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Login Auth Remitter saved in system.]'.PHP_EOL;
                $this->User->generateDMTLog($log_msg);
                //fetch beneficiary
                $post_data = array(
                    'mobile' => $post['mobile']
                    
                );


                                $key = $accountData['PAYSPRINT_AEPS_KEY'];
                                $iv = $accountData['PAYSPRINT_AEPS_IV'];
                                $cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                                $body=       base64_encode($cipher);
                                $jwt_payload = array(
                                    'timestamp'=>time(),
                                    'partnerId'=>$accountData['PAYSPRINT_PARTNER_ID'],
                                    'reqid'=>time().rand(1111,9999)
                                );
                                
                                $secret = $accountData['PAYSPRINT_SECRET_KEY'];

                                $paysprint_token = $this->Jwt_model->encode($jwt_payload,$secret);
                                
                                //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
                                
                                $header = [
                                    'Token:'.$paysprint_token, 
                                    //'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY                              
                                ];



                $api_url = PAYSPRINT_DMT_FETCH_BEN_API_URL;

                log_message('debug', 'Fetch Beneficiary  api API Url - '.$api_url);

                log_message('debug', 'Fetch Beneficiary api post data - '.json_encode($post_data));

                log_message('debug', 'Fetch Beneficiary api header data - '.json_encode($header));


                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);    
                curl_setopt($ch,CURLOPT_HTTPHEADER , $header);  
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $output = curl_exec ($ch);
                curl_close ($ch);

                log_message('debug', 'Fetch Beneficiary api response - '.json_encode($output));

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - Paysprint DMT Fetch Beneficiary API Response - '.$output.'.]'.PHP_EOL;
                $this->User->generateDMTLog($log_msg);

                // save api response 
                $api_data = array(
                    'account_id' => $account_id,
                    'user_id' => $loggedAccountID,
                    'api_response' => $output,
                    'api_url' => $api_url,
                    'api_post_data' => json_encode($post_data),
                    'status' => 1,
                    'created' => date('Y-m-d H:i:s')
                );
                $this->db->insert('dmt_api_response',$api_data);

                $ben_api_response = json_decode($output,true);
                if(isset($ben_api_response['response_code']) && $ben_api_response['response_code'] == 1)
                {
                    $benList = $ben_api_response['data'];
                    if($benList)
                    {
                        foreach($benList as $blist)
                        {
                            // save activation data
                            $benData = array(
                                'account_id' => $account_id,
                                'member_id' => $loggedAccountID,
                                'register_mobile' => $post['mobile'],
                                'account_holder_name' => $blist['name'],
                                'account_no' => $blist['accno'],
                                'ifsc' => $blist['ifsc'],
                                'bank_id' => $blist['bankid'],
                                'bank_name' => $blist['bankname'],
                                'status' => 1,
                                'is_otp_verify' => $blist['verified'],
                                'beneId' => $blist['bene_id'],
                                'created' => date('Y-m-d H:i:s'),
                                'created_by' => $loggedAccountID
                            );
                            $this->db->insert('user_dmt_beneficiary',$benData);
                        }
                    }
                }

                $api_response = array(
                    'status' => 1,
                    'message' => isset($responseData['status_msg']) ? $responseData['status_msg'] : '',
                    'token' => $token
                );
            }
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['status_msg']) ? $responseData['status_msg'] : ''
            );
        }
        
        return $api_response;
    }

    public function memberActivationOtpAuth($post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $token = $post['token'];
        $memberData = $this->db->select('mobile,first_name,last_name,dob,address,pin_code,stateresp')->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'token'=>$token))->row_array();
        
        $post_data = array(
            'mobile' => $memberData['mobile'],
            'firstname' => $memberData['first_name'],
            'lastname' => $memberData['last_name'],
            'address' => $memberData['address'],
            'otp' => $post['otp_code'],
            'pincode' => $memberData['pin_code'],
            'stateresp' => $memberData['stateresp'],
            'dob' => $memberData['dob'],
            'bank3_flag'=> 'yes'
        );


                                $key = $accountData['PAYSPRINT_AEPS_KEY'];
                                $iv = $accountData['PAYSPRINT_AEPS_IV'];
                                $cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                                $body=       base64_encode($cipher);
                                $jwt_payload = array(
                                    'timestamp'=>time(),
                                    'partnerId'=>$accountData['PAYSPRINT_PARTNER_ID'],
                                    'reqid'=>time().rand(1111,9999)
                                );
                                
                                $secret = $accountData['PAYSPRINT_SECRET_KEY'];

                                $paysprint_token = $this->Jwt_model->encode($jwt_payload,$secret);
                                
                                //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
                                
                                $header = [
                                    'Token:'.$paysprint_token, 
                                   // 'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY                              
                                ];


        $api_url = PAYSPRINT_DMT_REGISTER_REMITTER_API_URL;

        log_message('debug', 'Remitter Register  api API Url - '.$api_url);

        log_message('debug', 'Remitter Register api post data - '.json_encode($post_data));

        log_message('debug', 'Remitter Register api header data - '.json_encode($header));
    

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);    
         curl_setopt($ch,CURLOPT_HTTPHEADER , $header);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        log_message('debug', 'Remitter Register api Response - '.json_encode($output));

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - Paysprint DMT Register Remitter API Response - '.$output.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $output,
            'api_url' => $api_url,
            'api_post_data' => json_encode($post_data),
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($output,true);

        if(isset($responseData['response_code']) && $responseData['response_code'] == 1)
        {
            // update status
            $this->db->where('token',$token);
            $this->db->update('user_paysprint_dmt_activation',array('status'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));

            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['message']) ? $responseData['message'] : '',
                'mobile' => $memberData['mobile']
            );
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['message']) ? $responseData['message'] : ''
            );
        }
        
        return $api_response;
    }

    public function addBeneficiary($post,$mobile,$is_admin_surcharge,$surcharge_amount,$admin_surcharge_amount)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $memberData = $this->db->select('user_code,name,mobile,wallet_balance')->get_where('users',array('id'=>$loggedAccountID))->row_array();
        $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

        if(isset($post['verfiy']))
        {
            if($surcharge_amount)
            {
                $after_wallet_balance = $before_balance - $surcharge_amount;
                // deduct member wallet
                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedAccountID,    
                    'before_balance'      => $before_balance,
                    'amount'              => $surcharge_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 2,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'Account Verification Ac#'.$post['account_no'].' Charge Amount Deducted.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Member Wallet Updated - Updated Balance - '.$after_wallet_balance.']'.PHP_EOL;
                $this->User->generateDMTLog($log_msg);
            }

            if($is_admin_surcharge && $admin_surcharge_amount)
            {
                // get admin data
                $admin_id = $this->User->get_admin_id();
                $admin_wallet_balance = $this->User->get_admin_ewallet_balance($admin_id);

                $admin_after_wallet_balance = $admin_wallet_balance - $admin_surcharge_amount;

                // deduct member wallet
                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $admin_id,    
                    'before_balance'      => $admin_wallet_balance,
                    'amount'              => $admin_surcharge_amount,  
                    'after_balance'       => $admin_after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 2,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'Account Verification Ac#'.$post['account_no'].' Charge Amount Deducted.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                $user_wallet = array(
                    'aeps_wallet_balance'=>$admin_after_wallet_balance,        
                );    
                $this->db->where('id',$admin_id);
                $this->db->where('account_id',$account_id);
                $this->db->update('users',$user_wallet);

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Admin Wallet Updated - Updated Balance - '.$admin_after_wallet_balance.']'.PHP_EOL;
                $this->User->generateDMTLog($log_msg);
            }
        }

        

        $addressData = $this->db->select('dob,address,pin_code')->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'mobile'=>$mobile,'status'=>1))->row_array();

        $getBankName = $this->db->select('title')->get_where('paysprint_dmt_bank_list',array('id'=>$post['bankID']))->row_array();
        $bankname = isset($getBankName['title']) ? $getBankName['title'] : '';

        // save activation data
        $data = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'register_mobile' => $mobile,
            'account_holder_name' => $post['account_holder_name'],
            'ben_mobile' => $post['ben_mobile'],
            'account_no' => $post['account_no'],
            'ifsc' => $post['ifsc'],
            'bank_id' => $post['bankID'],
            'bank_name' => $bankname,
            'status' => 0,
            'is_otp_verify' => 0,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $loggedAccountID
        );
        $this->db->insert('user_dmt_beneficiary',$data);
        $sys_ben_id = $this->db->insert_id();

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Beneficiary Saved into System.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $post_data = array(
            'mobile' => $mobile,
            'benename' => $post['account_holder_name'],
            'bankid' => $post['bankID'],
            'accno' => $post['account_no'],
            'ifsccode' => $post['ifsc'],
            'verified' => 1,
            'address' => $addressData['address'],
            'pincode' => $addressData['pin_code'],
            'dob' => $addressData['dob']
        );

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Paysprint Post Data - '.json_encode($post_data).'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);


                                $key = $accountData['PAYSPRINT_AEPS_KEY'];
                                $iv = $accountData['PAYSPRINT_AEPS_IV'];
                                $cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                                $body=       base64_encode($cipher);
                                $jwt_payload = array(
                                    'timestamp'=>time(),
                                    'partnerId'=>$accountData['PAYSPRINT_PARTNER_ID'],
                                    'reqid'=>time().rand(1111,9999)
                                );
                                
                                $secret = $accountData['PAYSPRINT_SECRET_KEY'];

                                $paysprint_token = $this->Jwt_model->encode($jwt_payload,$secret);
                                
                                //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
                                
                                $header = [
                                    'Token:'.$paysprint_token, 
                                   // 'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY                              
                                ];



        $api_url = PAYSPRINT_DMT_REGISTER_BEN_API_URL;


        log_message('debug', 'Beneficiary Register  api API Url - '.$api_url);

        log_message('debug', 'Beneficiary Register api post data - '.json_encode($post_data));

        log_message('debug', 'Beneficiary Register api header data - '.json_encode($header));


        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);   
        curl_setopt($ch,CURLOPT_HTTPHEADER , $header);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        log_message('debug', 'Beneficiary Register api Response - '.json_encode($output));

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Paysprint Register Beneficiary API Response - '.$output.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $output,
            'api_url' => $api_url,
            'api_post_data' => json_encode($post_data),
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($output,true);

        /*echo "<pre>";
        print_r($responseData['data']['bene_id']);
        die;*/
        $api_response = array();

        if(isset($responseData['response_code']) && $responseData['response_code'] == 1)
        {
            // update status
            $this->db->where('id',$sys_ben_id);
            $this->db->update('user_dmt_beneficiary',array('status'=>1,'beneId'=>$responseData['data']['bene_id']));

            if(isset($post['verfiy']))
            {
                $transaction_id = time().rand(1111,9999);
                $post_data = array(
                    'mobile' => $mobile,
                    'benename' => $post['account_holder_name'],
                    'bankid' => $post['bankID'],
                    'accno' => $post['account_no'],
                    'referenceid' => $transaction_id,
                    'address' => $addressData['address'],
                    'pincode' => $addressData['pin_code'],
                    'dob' => $addressData['dob'],
                    'bene_id' => $responseData['bene_id']
                );
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Paysprint Account Verify Post Data - '.json_encode($post_data).'.]'.PHP_EOL;
                $this->User->generateDMTLog($log_msg);


                                 $key = $accountData['PAYSPRINT_AEPS_KEY'];
                                $iv = $accountData['PAYSPRINT_AEPS_IV'];
                                $cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                                $body=       base64_encode($cipher);
                                $jwt_payload = array(
                                    'timestamp'=>time(),
                                    'partnerId'=>$accountData['PAYSPRINT_PARTNER_ID'],
                                    'reqid'=>time().rand(1111,9999)
                                );
                                
                                $secret = $accountData['PAYSPRINT_SECRET_KEY'];

                                $paysprint_token = $this->Jwt_model->encode($jwt_payload,$secret);
                                
                                //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
                                
                                $header = [
                                    'Token:'.$paysprint_token, 
                                  //  'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY                              
                                ];



                $api_url = PAYSPRINT_DMT_VERIFY_BEN_API_URL;

        log_message('debug', 'Account Verification  api API Url - '.$api_url);

        log_message('debug', 'Account Verification api post data - '.json_encode($post_data));

        log_message('debug', 'Account Verification api header data - '.json_encode($header));
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 
                curl_setopt($ch,CURLOPT_HTTPHEADER , $header);  
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $output = curl_exec ($ch);
                curl_close ($ch);


                 log_message('debug', 'Account Verification api Response - '.json_encode($output));

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Beneficiary Auth Paysprint Account Verify API Response - '.$output.'.]'.PHP_EOL;
                $this->User->generateDMTLog($log_msg);

                // save api response 
                $api_data = array(
                    'account_id' => $account_id,
                    'user_id' => $loggedAccountID,
                    'api_response' => $output,
                    'api_url' => $api_url,
                    'api_post_data' => json_encode($post_data),
                    'status' => 1,
                    'created' => date('Y-m-d H:i:s')
                );
                $this->db->insert('dmt_api_response',$api_data);

                $apiResponseData = json_decode($output,true);

                if(isset($apiResponseData['response_code']) && $apiResponseData['response_code'] == 1)
                {
                    if(isset($apiResponseData['txn_status']) && $apiResponseData['txn_status'] == 1)
                    {
                        // update status
                        $this->db->where('id',$sys_ben_id);
                        $this->db->update('user_dmt_beneficiary',array('is_verify'=>1,'bank_utr'=>$apiResponseData['utr'],'ackno'=>$apiResponseData['ackno'],'txnid'=>$apiResponseData['refid'],'account_holder_name'=>$apiResponseData['benename']));
                    }
                }
            }

            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['message']) ? $responseData['message'] : '',
                'mobile' => $memberData['mobile']
            );
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['message']) ? $responseData['message'] : ''
            );
        }
        
        return $api_response;
    }

    public function deleteBen($benId,$mobile)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        
        $post_data = array(
            'mobile' => $mobile,
            'bene_id' => $benId
        );

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Delete Ben Auth Paysprint Post Data - '.json_encode($post_data).'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);


                                $key = $accountData['PAYSPRINT_AEPS_KEY'];
                                $iv = $accountData['PAYSPRINT_AEPS_IV'];
                                $cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                                $body=       base64_encode($cipher);
                                $jwt_payload = array(
                                    'timestamp'=>time(),
                                    'partnerId'=>$accountData['PAYSPRINT_PARTNER_ID'],
                                    'reqid'=>time().rand(1111,9999)
                                );
                                
                                $secret = $accountData['PAYSPRINT_SECRET_KEY'];

                                $paysprint_token = $this->Jwt_model->encode($jwt_payload,$secret);
                                
                                //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
                                
                                $header = [
                                    'Token:'.$paysprint_token, 
                                   // 'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY                              
                                ];



        $api_url = PAYSPRINT_DMT_DELETE_BEN_API_URL;

        log_message('debug', 'Delete Beneficiary  api API Url - '.$api_url);

        log_message('debug', 'Delete Beneficiary api post data - '.json_encode($post_data));

        log_message('debug', 'Delete Beneficiary api header data - '.json_encode($header));
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  
        curl_setopt($ch,CURLOPT_HTTPHEADER , $header);   
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);


        log_message('debug', 'Delete Beneficiary api Response - '.json_encode($output));

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Delete Ben Auth Paysprint Delete Ben API Response - '.$output.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $output,
            'api_url' => $api_url,
            'api_post_data' => json_encode($post_data),
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($output,true);

        if(isset($responseData['response_code']) && $responseData['response_code'] == 1)
        {
            // update status
            $this->db->where('account_id',$account_id);
            $this->db->where('member_id',$loggedAccountID);
            $this->db->where('beneId',$benId);
            $this->db->delete('user_dmt_beneficiary');

            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['message']) ? $responseData['message'] : ''
            );
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['message']) ? $responseData['message'] : ''
            );
        }
        
        return $api_response;
    }

    public function beneficiaryOtpAuth($post,$mobile)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $memberData = $this->db->select('user_code,name,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();
        $token = $post['token'];
        // generate api token
        //$access_token = $this->User->generate_dmt_token($loggedAccountID);
        $api_response = $this->User->call_dmt_beneficiary_otp_auth($loggedAccountID,$mobile,$post);
        if($api_response['status'] == 1)
        {
            // update status
            $this->db->where('beneId',$token);
            $this->db->update('user_beneficiary',array('status'=>1,'is_otp_verify'=>1));

        }
        return $api_response;
    }

    public function transferFund($post,$is_admin_surcharge,$surcharge_amount,$admin_surcharge_amount,$active_api_id)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $memberData = $this->db->select('user_code,name,mobile,wallet_balance')->get_where('users',array('id'=>$loggedAccountID))->row_array();
        $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

        

        $benId = $post['benId'];
        $amount = $post['amount'];
        // get beneficiary data
        $beneficiaryData = $this->db->get_where('user_dmt_beneficiary',array('beneId'=>$benId))->row_array();
        $register_mobile = $beneficiaryData['register_mobile'];

        // get member activation data
        $memberActivationData = $this->db->get_where('user_paysprint_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>1,'mobile'=>$register_mobile))->row_array();

        $final_amount = $amount + $surcharge_amount;

        $after_wallet_balance = $before_balance - $final_amount;

        $transaction_id = time().rand(1111,9999);

        $data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'to_ben_id' => $beneficiaryData['id'],
            'before_wallet_balance' => $before_balance,
            'transfer_amount' => $amount,
            //'is_admin_charge' => $is_admin_surcharge,
            'transfer_charge_amount' => $surcharge_amount,
            'total_wallet_charge' => $final_amount,
            'after_wallet_balance' => $after_wallet_balance,
            'transaction_id' => $transaction_id,
            'encode_transaction_id' => do_hash($transaction_id),
            'status' => 2,
            'wallet_type' => 1,
            'memberID' => $memberData['user_code'],
            'mobile' => ($beneficiaryData['ben_mobile']) ? $beneficiaryData['ben_mobile'] : $memberActivationData['mobile'],
            'account_holder_name' => $beneficiaryData['account_holder_name'],
            'account_no' => $beneficiaryData['account_no'],
            'ifsc' => $beneficiaryData['ifsc'],
            'api_id' => $active_api_id,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('user_dmt_transfer',$data);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Transfer Auth Transaction #'.$transaction_id.' Saved.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // deduct member wallet
        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $loggedAccountID,    
            'before_balance'      => $before_balance,
            'amount'              => $final_amount,  
            'after_balance'       => $after_wallet_balance,      
            'status'              => 1,
            'type'                => 2,   
            'wallet_type'         => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'DMT #'.$transaction_id.' Amount Deducted.'
        );

        $this->db->insert('member_wallet',$wallet_data);


        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Transfer Auth Member Wallet Deducation, Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        if($is_admin_surcharge && $admin_surcharge_amount)
        {
            // get admin data
            $admin_id = $this->User->get_admin_id();
            $admin_wallet_balance = $this->User->get_admin_ewallet_balance($admin_id);

            $admin_after_wallet_balance = $admin_wallet_balance - $admin_surcharge_amount;

            // deduct member wallet
            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $admin_id,    
                'before_balance'      => $admin_wallet_balance,
                'amount'              => $admin_surcharge_amount,  
                'after_balance'       => $admin_after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'         => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'DMT #'.$transaction_id.' Charge Amount Deducted.'
            );

            $this->db->insert('member_wallet',$wallet_data);

            $user_wallet = array(
                'aeps_wallet_balance'=>$admin_after_wallet_balance,        
            );    
            $this->db->where('id',$admin_id);
            $this->db->where('account_id',$account_id);
            $this->db->update('users',$user_wallet);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Transfer Auth Admin Charge Wallet Deducation, Updated Balance - '.$admin_after_wallet_balance.'.]'.PHP_EOL;
            $this->User->generateDMTLog($log_msg);
        }

        if($active_api_id == 2)
        {
            $post_data = array(
                'mobile' => $memberActivationData['mobile'],
                'amount' => $amount,
                'txntype' => 'IMPS',
                'referenceid' => $transaction_id,
                'address' => $memberActivationData['address'],
                'pincode' => $memberActivationData['pin_code'],
                'dob' => $memberActivationData['dob'],
                'bene_id' => $benId,
                'pipe'=>'bank1',
                'gst_state' =>'07'
            );


                                $key = $accountData['PAYSPRINT_AEPS_KEY'];
                                $iv = $accountData['PAYSPRINT_AEPS_IV'];
                                $cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                                $body=       base64_encode($cipher);
                                $jwt_payload = array(
                                    'timestamp'=>time(),
                                    'partnerId'=>$accountData['PAYSPRINT_PARTNER_ID'],
                                    'reqid'=>time().rand(1111,9999)
                                );
                                
                                $secret = $accountData['PAYSPRINT_SECRET_KEY'];

                                $paysprint_token = $this->Jwt_model->encode($jwt_payload,$secret);
                                
                                //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
                                
                                $header = [
                                    'Token:'.$paysprint_token, 
                                    //'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY                              
                                ];




            $api_url = PAYSPRINT_DMT_TXN_AUTH_API_URL;

        log_message('debug', 'DMT Transaction  api API Url - '.$api_url);

        log_message('debug', 'DMT Transaction api post data - '.json_encode($post_data));

        log_message('debug', 'DMT Transaction api header data - '.json_encode($header));
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
             curl_setopt($ch,CURLOPT_HTTPHEADER , $header);     
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $output = curl_exec ($ch);
            curl_close ($ch);

        log_message('debug', 'DMT Transaction api Response - '.json_encode($output));
        
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Transfer Auth Paysprint DMT Txn API Response - '.$output.'.]'.PHP_EOL;
            $this->User->generateDMTLog($log_msg);

            // save api response 
            $api_data = array(
                'account_id' => $account_id,
                'user_id' => $loggedAccountID,
                'api_response' => $output,
                'api_url' => $api_url,
                'api_post_data' => json_encode($post_data),
                'status' => 1,
                'created' => date('Y-m-d H:i:s')
            );
            $this->db->insert('dmt_api_response',$api_data);

            $responseData = json_decode($output,true);
        }
        else
        {
            $post_data = array(
                'agentId' => $memberData['user_code'],
                'amount' => $amount,
                'bankName' => $beneficiaryData['bank_name'],
                'beneAccNo' => $beneficiaryData['account_no'],
                'beneMobNo' => ($beneficiaryData['ben_mobile']) ? $beneficiaryData['ben_mobile'] : $memberActivationData['mobile'],
                'beneName' => $beneficiaryData['account_holder_name'],
                'customerMobile' => $memberActivationData['mobile'],
                'distTxnRefNo' => $transaction_id,
                'ifsc' => $beneficiaryData['ifsc'],
                'stateCode' => 20,
                'custFirstName' => $memberActivationData['first_name'],
                'custLastName' => $memberActivationData['last_name'],
                'custPincode' => $memberActivationData['pin_code'],
                'custAddress' => $memberActivationData['address'],
            );

            $api_response = $this->User->call_dmt_imps_transfer_api($loggedAccountID,$post_data,$memberData['mobile']);

            $responseData = array();
            $responseData['status_code'] = 200;
            if($api_response['status'] == 1)
            {
                $responseData['status'] = 'SUCCESS';
            }
            elseif($api_response['status'] == 3 || $api_response['status'] == 0)
            {
                $responseData['status'] = 'FAILED';
            }
            else
            {
                $responseData['status'] = 'PENDING';
            }
            $responseData['ackno'] = isset($api_response['payuTxnRefNo']) ? $api_response['payuTxnRefNo'] : '';
            $responseData['utr'] = isset($api_response['bankRefNo']) ? $api_response['bankRefNo'] : '';
            $responseData['status_msg'] = isset($api_response['message']) ? $api_response['message'] : '';

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Transfer Auth Payu DMT Txn API Response - '.json_encode($api_response).'.]'.PHP_EOL;
            $this->User->generateDMTLog($log_msg);
        }

        $api_response = array();

        if(isset($responseData['response_code']) && $responseData['response_code'] == 1)
        {
            if(isset($responseData['txn_status']) && $responseData['txn_status'] == 1)
            {
                // update status
                $this->db->where('transaction_id',$transaction_id);
                $this->db->update('user_dmt_transfer',array('status'=>3,'op_txn_id'=>$responseData['ackno'],'rrn'=>$responseData['utr']));

                $api_response = array(
                    'status' => 1,
                    'message' => isset($responseData['message']) ? $responseData['message'] : ''
                );
            }
            elseif(isset($responseData['txn_status']) && ($responseData['txn_status'] == 2 || $responseData['txn_status'] == 3 || $responseData['txn_status'] == 4))
            {
                // update status
                $this->db->where('transaction_id',$transaction_id);
                $this->db->update('user_dmt_transfer',array('op_txn_id'=>$responseData['ackno'],'rrn'=>$responseData['utr']));

                $api_response = array(
                    'status' => 2,
                    'message' => isset($responseData['message']) ? $responseData['message'] : ''
                );
            }
            elseif(isset($responseData['txn_status']) && $responseData['txn_status'] == 5)
            {
                // update status
                $this->db->where('transaction_id',$transaction_id);
                $this->db->update('user_dmt_transfer',array('status'=>4,'op_txn_id'=>$responseData['ackno'],'rrn'=>$responseData['utr']));

                // refund amount to member
                
                $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                $after_wallet_balance = $before_balance + $final_amount;

                // deduct member wallet
                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedAccountID,    
                    'before_balance'      => $before_balance,
                    'amount'              => $final_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'DMT #'.$transaction_id.' Amount Refund Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Transfer Auth Transaction #'.$transaction_id.' Member Wallet Reunded, Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
                $this->User->generateDMTLog($log_msg);

                if($is_admin_surcharge && $admin_surcharge_amount)
                {
                    // get admin data
                    $admin_id = $this->User->get_admin_id();
                    $admin_wallet_balance = $this->User->get_admin_ewallet_balance($admin_id);

                    $admin_after_wallet_balance = $admin_wallet_balance + $admin_surcharge_amount;

                    // deduct member wallet
                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $admin_id,    
                        'before_balance'      => $admin_wallet_balance,
                        'amount'              => $admin_surcharge_amount,  
                        'after_balance'       => $admin_after_wallet_balance,      
                        'status'              => 1,
                        'type'                => 1,   
                        'wallet_type'         => 2,   
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'DMT #'.$transaction_id.' Charge Amount Refund Credited.'
                    );

                    $this->db->insert('member_wallet',$wallet_data);

                    $user_wallet = array(
                        'aeps_wallet_balance'=>$admin_after_wallet_balance,        
                    );    
                    $this->db->where('id',$admin_id);
                    $this->db->where('account_id',$account_id);
                    $this->db->update('users',$user_wallet);

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Transfer Auth Transaction #'.$transaction_id.' Admin Charge Refund Credited, Updated Balance - '.$admin_after_wallet_balance.'.]'.PHP_EOL;
                    $this->User->generateDMTLog($log_msg);
                }

                $api_response = array(
                    'status' => 3,
                    'message' => isset($responseData['message']) ? $responseData['message'] : ''
                );
            }
        }
        elseif(!isset($responseData['response_code']))
        {
            $api_response = array(
                    'status' => 2,
                    'message' => isset($responseData['message']) ? $responseData['message'] : ''
                );
        }
        else
        {
            if($responseData['status'] == 'FAILED')
            {
                // update status
                $this->db->where('transaction_id',$transaction_id);
                $this->db->update('user_dmt_transfer',array('status'=>4));

                // refund amount to member
                
                $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                $after_wallet_balance = $before_balance + $final_amount;

                // deduct member wallet
                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedAccountID,    
                    'before_balance'      => $before_balance,
                    'amount'              => $final_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'DMT #'.$transaction_id.' Amount Refund Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);
                
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Transfer Auth Transaction #'.$transaction_id.' Member Wallet Reunded, Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
                $this->User->generateDMTLog($log_msg);

                if($is_admin_surcharge && $admin_surcharge_amount)
                {
                    // get admin data
                    $admin_id = $this->User->get_admin_id();
                    $admin_wallet_balance = $this->User->get_admin_ewallet_balance($admin_id);

                    $admin_after_wallet_balance = $admin_wallet_balance + $admin_surcharge_amount;

                    // deduct member wallet
                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $admin_id,    
                        'before_balance'      => $admin_wallet_balance,
                        'amount'              => $admin_surcharge_amount,  
                        'after_balance'       => $admin_after_wallet_balance,      
                        'status'              => 1,
                        'type'                => 1,   
                        'wallet_type'         => 2,   
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'DMT #'.$transaction_id.' Charge Amount Refund Credited.'
                    );

                    $this->db->insert('member_wallet',$wallet_data);

                    $user_wallet = array(
                        'aeps_wallet_balance'=>$admin_after_wallet_balance,        
                    );    
                    $this->db->where('id',$admin_id);
                    $this->db->where('account_id',$account_id);
                    $this->db->update('users',$user_wallet);

                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') -  DMT Transfer Auth Transaction #'.$transaction_id.' Admin Charge Refund Credited, Updated Balance - '.$admin_after_wallet_balance.'.]'.PHP_EOL;
                    $this->User->generateDMTLog($log_msg);
                }

            }
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['status_msg']) ? $responseData['status_msg'] : ''
            );
        }
        
        return $api_response;
    }
    

    
}


/* end of file: az.php */
/* Location: ./application/models/az.php */