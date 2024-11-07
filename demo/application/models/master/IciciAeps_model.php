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

class IciciAeps_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        
    }

    public function activeAEPSMember($post,$aadhar_photo,$aadhar_back_photo,$pancard_photo)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $memberID = $loggedUser['id'];
        
        
        $wallet_data = array(
           'account_id' => $account_id,
            'member_id'           => $memberID, 
            'first_name'      => $post['first_name'],
            'middle_name'      => $post['middle_name'],
            'last_name'              => $post['last_name'],  
            'mobile'       => $post['mobile'],      
            'shop_name'              => $post['shop_name'],      
            'state_id'                => $post['state_id'],       
            'city_id'             => $post['city_id'],       
            'address'         => $post['address'],
            'pin_code'         => $post['pin_code'],
            'aadhar'         => $post['aadhar_no'],
            'pancard'         => $post['pancard_no'],
            'aadhar_photo'         => $aadhar_photo,
            'aadhar_back_photo'   =>$aadhar_back_photo,
            'pancard_photo'         => $pancard_photo,
            'is_otp_verify'  => 0,
            'status'         => 0,
            'father_name' =>$post['father_name'],
            'mother_name'  =>$post['mother_name'],
            'village'       =>$post['village'],
            'post'              =>$post['post'],
            'police_station'     =>$post['police_station'],
            'block'                =>$post['block'],
            'district'              =>$post['district'],
            'created'             => date('Y-m-d H:i:s'),      
            'created_by'         => $loggedUser['id'],
        );

        $this->db->insert('instantpay_ekyc',$wallet_data);
        $recordID = $this->db->insert_id();

        $memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $member_code = $memberData['user_code'];
        $member_pin = $memberData['decoded_transaction_password'];
        $member_email = $memberData['email'];
        // get state name
        $get_state_name = $this->db->get_where('aeps_state',array('id'=>$post['state_id']))->row_array();
        $state_name = isset($get_state_name['state']) ? $get_state_name['state'] : '';

        // get city name
        $get_city_name = $this->db->get_where('city',array('city_id'=>$post['city_id']))->row_array();
        $city_name = isset($get_city_name['city_name']) ? $get_city_name['city_name'] : '';

          $api_url = INSTANTPAY_EKYC_URL;


                    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
                    $iv = openssl_random_pseudo_bytes($ivlen);
                    $ciphertext = openssl_encrypt($post['aadhar_no'],'aes-256-cbc', $accountData['instant_encryption_key'], OPENSSL_RAW_DATA, $iv);
                    $encryptedData = base64_encode($iv . $ciphertext);


        $postData = array(
            
                        'mobile' => $post['mobile'],
                        'pan' => $post['pancard_no'],
                        'email' => $member_email,
                        'aadhaar' => $encryptedData,
                        "latitude"=>"22.9734229",
                        "longitude"=>"78.6568942",
                        'consent' => "Y"
        );

             $header = array(
                        'X-Ipay-Auth-Code: 1',
                        'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
                        'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
                        'X-Ipay-Endpoint-Ip: 103.129.97.70',
                        'content-type: application/json'
                    );


                    $curl = curl_init();
                    // URL
                    curl_setopt($curl, CURLOPT_URL, $api_url);

                    // Return Transfer
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    // SSL Verify Peer
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

                    // SSL Verify Host
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

                    // Timeout
                    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

                    // HTTP Version
                    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                    // Request Method
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

                    // Request Body
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));

                    // Execute
                   $output = curl_exec($curl);

                    // Close
                    curl_close ($curl);

        $responseData = json_decode($output,true);

        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $memberID,
            'api_url' => $api_url,
            'api_response' => $output,
            'header_data' =>json_encode($header),
            'post_data' => json_encode($postData),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('instantpay_api_response',$apiData);

        if(isset($responseData['statuscode']) && $responseData['statuscode'] == 'TXN' && $responseData['status'] == 'Transaction Successful')
        {

            $otpReferenceID =  $responseData['data']['otpReferenceID'];
            $hash = $responseData['data']['hash'];
            // update aeps status
            $this->db->where('id',$recordID);
            $this->db->update('instantpay_ekyc',array('is_otp_verify'=>0,'otpReferenceID'=>$otpReferenceID,'hash'=>$hash));

            return array('status'=>1,'msg'=>$responseData['status'],'otpReferenceID'=>$otpReferenceID);
        }
        else
        {
            return array('status'=>0,'msg'=>$responseData['status']);
        }
    }

    public function aepsResendOtp($memberID,$encodeFPTxnId)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $member_code = $memberData['user_code'];

        // check already kyc approved or not
        $get_kyc_data = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->row_array();
        $primaryKeyId = isset($get_kyc_data['primaryKeyId']) ? $get_kyc_data['primaryKeyId'] : '';

        // send OTP API

        $otp_api_url = AEPS_EKYC_RESEND_OTP_API_URL;

        $otpPostData = array(
            'merchantLoginId' => $member_code,
            'primaryKeyId' => $primaryKeyId,
            'encodeFPTxnId' => $encodeFPTxnId
        );

        $api_otp_post_data = http_build_query($otpPostData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$otp_api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$api_otp_post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $headers = [
            'username: '.$accountData['aeps_username'],
            'password: '.$accountData['aeps_password'],
            'Content-Type:application/x-www-form-urlencoded'
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $otp_output = curl_exec ($ch);
        curl_close ($ch);
        $otpResponseData = json_decode($otp_output,true);

        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $memberID,
            'api_url' => $otp_api_url,
            'api_response' => $otp_output,
            'post_data' => json_encode($otpPostData),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('aeps_api_response',$apiData);
        if(isset($otpResponseData['Error']) && $otpResponseData['Error'] == 'False')
        {
            /*$primaryKeyId = isset($otpResponseData['Data'][0]['primaryKeyId']) ? $otpResponseData['Data'][0]['primaryKeyId'] : '';
            $encodeFPTxnId = isset($otpResponseData['Data'][0]['encodeFPTxnId']) ? $otpResponseData['Data'][0]['encodeFPTxnId'] : '';
            // update aeps status
            $this->db->where('id',$recordID);
            $this->db->update('aeps_member_kyc',array('clear_step'=>3,'primaryKeyId'=>$primaryKeyId,'encodeFPTxnId'=>$encodeFPTxnId));*/
            return array('status'=>1,'msg'=>'success','primaryKeyId'=>$primaryKeyId,'encodeFPTxnId'=>$encodeFPTxnId);
        }
        else
        {
            return array('status'=>0,'msg'=>$otpResponseData['Message']);
        }
    }

    public function aepsOTPAuth($post,$memberID,$otpReferenceID,$hash,$aadhar)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $member_code = $memberData['user_code'];

        $otp_code = $post['otp_code'];

        // check already kyc approved or not
        $get_kyc_data = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$memberID,'otpReferenceID'=>$otpReferenceID,'is_otp_verify'=>0))->row_array();
        
        $recordID = isset($get_kyc_data['id']) ? $get_kyc_data['id'] : '';

        // send OTP API

                     $otp_api_url = INSTANTPAY_EKYC_VERIFY_URL;

                    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
                    $iv = openssl_random_pseudo_bytes($ivlen);
                    $ciphertext = openssl_encrypt($aadhar,'aes-256-cbc', $accountData['instant_encryption_key'], OPENSSL_RAW_DATA, $iv);
                    $encryptedData = base64_encode($iv . $ciphertext);
                    
                    $request = array(
                        'otpReferenceID' => $otpReferenceID,
                        'hash' => $hash,
                        'otp' => $otp_code
                    );

         

         $header = array(
                        'X-Ipay-Auth-Code: 1',
                        'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
                        'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
                        'X-Ipay-Endpoint-Ip: 164.52.219.77',
                        'content-type: application/json'
                    );
                    


                     $curl = curl_init();
                    // URL
                    curl_setopt($curl, CURLOPT_URL, $otp_api_url);

                    // Return Transfer
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    // SSL Verify Peer
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

                    // SSL Verify Host
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

                    // Timeout
                    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

                    // HTTP Version
                    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                    // Request Method
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

                    // Request Body
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

                    // Execute
                     $output = curl_exec($curl);

                    // Close
                    curl_close ($curl);
                    


        
       
       
        $otpResponseData = json_decode($output,true);

        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $memberID,
            'api_url' => $otp_api_url,
            'api_response' => $output,
            'post_data' => json_encode($request),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('instantpay_api_response',$apiData);
        if(isset($otpResponseData['statuscode']) && $otpResponseData['statuscode'] == 'TXN' && $otpResponseData['status'] == 'Transaction Successful')
        {
             $outled_id = $otpResponseData['data']['outletId'];
            /*$primaryKeyId = isset($otpResponseData['Data'][0]['primaryKeyId']) ? $otpResponseData['Data'][0]['primaryKeyId'] : '';
            $encodeFPTxnId = isset($otpResponseData['Data'][0]['encodeFPTxnId']) ? $otpResponseData['Data'][0]['encodeFPTxnId'] : '';*/
            // update aeps status
            $this->db->where('id',$recordID);
            $this->db->update('instantpay_ekyc',array('status'=>1,'is_otp_verify'=>1));

            $this->db->where('id',$memberID);
            $this->db->update('users',array('instantpay_aeps_status'=>1,'instantpay_outlet_id'=>$outled_id));

            return array('status'=>1,'msg'=>'success');
        }
        else
        {
            return array('status'=>0,'msg'=>$otpResponseData['Message']);
        }
    }

    public function addBalance($txnID,$aadharNumber,$iin,$amount,$recordID,$serviceType = '')
    {       
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $memberID = $loggedUser['id'];

        $com_type = 0;
        if($serviceType == 'balwithdraw')
        {
            $com_type = 1;
        }
        elseif($serviceType == 'aadharpay')
        {
            $com_type = 3;
        }

        $admin_id = $this->User->get_admin_id();
        $adminCommisionData = $this->User->get_admin_aeps_commission($amount,$account_id,$com_type);
        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

        $commisionData = $this->User->get_aeps_commission($amount,$loggedUser['id'],$com_type);
        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

        //get member wallet_balance
        
        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

        // update member wallet
        $after_balance = $before_wallet_balance + $amount;
        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $memberID,    
            'before_balance'      => $before_wallet_balance,
            'amount'              => $amount,  
            'after_balance'       => $after_balance,      
            'status'              => 1,
            'type'                => 1,   
            'wallet_type'         => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'AEPS Txn #'.$txnID.' Amount Credited.'
        );

        $this->db->insert('member_wallet',$wallet_data);
        
        // calculate aeps commision
        if($com_amount)
        {
            $commData = array(
                'account_id' => $account_id,
                'member_id' => $memberID,
                'type' => $com_type,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $com_amount,
                'is_surcharge' => $is_surcharge,
                'wallet_settle_amount' => $com_amount,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
            );
            $this->db->insert('member_aeps_comm',$commData);

            //get member wallet_balance
            
            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

            if($is_surcharge)
            {
                $after_wallet_balance = $before_wallet_balance - $com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 2,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

            }
            else
            {
                $after_wallet_balance = $before_wallet_balance + $com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Commission Amount Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                if($accountData['is_tds_amount'] == 1)
                            {

                                $accountBalanceData = $this->db->get_where('users',array('id'=>$memberID,'account_id'=>$account_id))->row_array();

                            $before_balance = $this->User->getMemberWalletBalanceSP($memberID);

                            $tds_amount = $com_amount*5/100;

                            $after_balance = $before_balance - $tds_amount;

                            $wallet_data = array(
                                'account_id'          => $account_id,
                                'member_id'           => $memberID,    
                                'before_balance'      => $before_balance,
                                'amount'              => $tds_amount,  
                                'after_balance'       => $after_balance,      
                                'status'              => 1,
                                'type'                => 2,  
                                'wallet_type'         => 1,      
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $memberID,
                                'description'         => 'AEPS Txn  #'.$txnID.'  Commision tds amount deducted'
                            );

                            $this->db->insert('member_wallet',$wallet_data);

                            //save tds entry 


                            $wallet_data = array(
                                'account_id'          => $account_id,
                                'member_id'           => $memberID,  
                                'record_id'            =>$recordID,
                                'com_amount'      => $com_amount,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $memberID,
                                'description'         => 'AEPS Txn  #'.$txnID.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

                            }


            }
        }

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
            'description'         => 'AEPS Txn #'.$txnID.' Amount Credited.'
        );

        $this->db->insert('collection_wallet',$wallet_data);

        if($admin_com_amount)
        {
            $is_paid = 0;
            if($admin_is_surcharge)
            {
                $is_paid = 1;
            }
            $commData = array(
                'account_id' => $account_id,
                'member_id' => $admin_id,
                'type' => $com_type,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $admin_com_amount,
                'is_surcharge' => $admin_is_surcharge,
                'wallet_settle_amount' => $admin_com_amount,
                'is_paid' => $is_paid,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
            );
            $this->db->insert('member_aeps_comm',$commData);

            //get member wallet_balance
           
            $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
            if($admin_is_surcharge)
            {
                $after_wallet_balance = $before_wallet_balance - $admin_com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $admin_id,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $admin_com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 2,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('collection_wallet',$wallet_data);

            }
            
        }



        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - ICICI AEPS - Distribute Commision/Surcharge Start]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        //$this->User->distribute_aeps_commision($recordID,$txnID,$account_id,$amount,$memberID,$com_amount,$is_surcharge,$com_type,'DT',$loggedUser['user_code']);


        $this->User->distribute_aeps_commision($recordID,$txnID,$memberID,$amount,$com_amount,$is_surcharge,$com_type,'DT',$loggedUser['user_code']);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - ICICI AEPS - Distribute Commision/Surcharge End]'.PHP_EOL;
        $this->User->generateLog($log_msg);


        
        return true;
    }

    public function addStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID)
    {       
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $memberID = $loggedUser['id'];

        $admin_id = $this->User->get_admin_id();
        $adminCommisionData = $this->User->get_admin_aeps_commission($amount,$account_id,2);
        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

        $commisionData = $this->User->get_aeps_commission($amount,$loggedUser['id'],2);
        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

        if($com_amount)
        {
            $commData = array(
                'account_id' => $account_id,
                'member_id' => $memberID,
                'type' => 2,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $com_amount,
                'is_surcharge' => $is_surcharge,
                'wallet_settle_amount' => $com_amount,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
            );
            $this->db->insert('member_aeps_comm',$commData);

            //get member wallet_balance
           
            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

            if($is_surcharge)
            {
                $after_wallet_balance = $before_wallet_balance - $com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 2,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

            }
            else
            {
                $after_wallet_balance = $before_wallet_balance + $com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Commission Amount Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                if($accountData['is_tds_amount'] == 1)
                            {

                                $accountBalanceData = $this->db->get_where('users',array('id'=>$memberID,'account_id'=>$account_id))->row_array();

                            $before_balance = $this->User->getMemberWalletBalanceSP($memberID);

                            $tds_amount = $com_amount*5/100;

                            $after_balance = $before_balance - $tds_amount;

                            $wallet_data = array(
                                'account_id'          => $account_id,
                                'member_id'           => $memberID,    
                                'before_balance'      => $before_balance,
                                'amount'              => $tds_amount,  
                                'after_balance'       => $after_balance,      
                                'status'              => 1,
                                'type'                => 2,  
                                'wallet_type'         => 1,      
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $memberID,
                                'description'         => 'AEPS Txn  #'.$txnID.'  Commision tds amount deducted'
                            );

                            $this->db->insert('member_wallet',$wallet_data);

                            //save tds entry 
                            $wallet_data = array(
                                'account_id'          => $account_id,
                                'member_id'           => $memberID,  
                                'record_id'            =>$recordID,
                                'com_amount'      => $com_amount,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $memberID,
                                'description'         => 'AEPS Txn  #'.$txnID.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

                            }

                            
            }
        }

        if($admin_com_amount)
        {
            $is_paid = 0;
            if($admin_is_surcharge)
            {
                $is_paid = 1;
            }
            $commData = array(
                'account_id' => $account_id,
                'member_id' => $admin_id,
                'type' => 2,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $admin_com_amount,
                'is_surcharge' => $admin_is_surcharge,
                'wallet_settle_amount' => $admin_com_amount,
                'is_paid' => $is_paid,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
            );
            $this->db->insert('member_aeps_comm',$commData);

            //get member wallet_balance
           
            $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);;

            if($admin_is_surcharge)
            {
                $after_wallet_balance = $before_wallet_balance - $admin_com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $admin_id,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $admin_com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 2,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('collection_wallet',$wallet_data);

            }
            
        }



        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - ICICI AEPS - Distribute Commision/Surcharge Start]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        //$this->User->distribute_aeps_commision($recordID,$txnID,$account_id,$amount,$memberID,$com_amount,$is_surcharge,2,'DT',$loggedUser['user_code']);

        $this->User->distribute_aeps_commision($recordID,$txnID,$memberID,$amount,$com_amount,$is_surcharge,2,'DT',$loggedUser['user_code']);
                

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - ICICI AEPS - Distribute Commision/Surcharge End]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        
        
        
        return true;
    }

  
    public function saveAepsTxn($txnID,$service,$aadhar_no,$mobile,$amount,$iinno,$api_url,$api_response,$message,$status,$api_response_data = array(),$balanceAmount = '',$bankRRN = '',$transactionAmount = '')
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $memberID = $loggedUser['id'];

        $receipt_id = rand(111111,999999);

        $txnData = array(
            'account_id' => $account_id,
            'member_id'  => $memberID,
            'receipt_id' => $receipt_id,
            'service' => $service,
            'aadhar_no' => $aadhar_no,
            'mobile' => $mobile,
            'amount' =>$amount,
            'iinno' => $iinno,
            'txnID' => $txnID,
            'api_url' => $api_url,
            'api_response' => $api_response,
            'message' => $message,
            'status' => $status,
            'json_data' => json_encode($api_response_data),
            'balance_amount' => $balanceAmount,
            'bank_rrno' => $bankRRN,
            'transactionAmount' => $transactionAmount,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $memberID
        );
        $this->db->insert('instantpay_aeps_transaction',$txnData);
        $recordID = $this->db->insert_id();
        return $recordID;
    }
    
    

}


/* end of file: az.php */
/* Location: ./application/models/az.php */