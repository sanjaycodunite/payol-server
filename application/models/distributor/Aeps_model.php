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

class Aeps_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        
    }

    public function activeAEPSMember($post,$aadhar_photo,$pancard_photo)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $memberID = $loggedUser['id'];
        
        
        $wallet_data = array(
            'account_id' => $account_id,
            'member_id'           => $memberID, 
            'first_name'      => $post['first_name'],
            'last_name'              => $post['last_name'],  
            'mobile'       => $post['mobile'],      
            'shop_name'              => $post['shop_name'],      
            'state_id'                => $post['state_id'],       
            'city_id'             => $post['city_id'],       
            'address'         => $post['address'],
            'pin_code'         => $post['pin_code'],
            'aadhar_no'         => $post['aadhar_no'],
            'pancard_no'         => $post['pancard_no'],
            'aadhar_photo'         => $aadhar_photo,
            'pancard_photo'         => $pancard_photo,
            'status'         => 0,
            'created'             => date('Y-m-d H:i:s'),      
            'created_by'         => $loggedUser['id'],
        );

        $this->db->insert('aeps_member_kyc',$wallet_data);
        $recordID = $this->db->insert_id();

        $memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $member_code = $memberData['user_code'];
        $member_pin = $memberData['decoded_transaction_password'];

        // get state name
        $get_state_name = $this->db->get_where('aeps_state',array('id'=>$post['state_id']))->row_array();
        $state_name = isset($get_state_name['state']) ? $get_state_name['state'] : '';

        // get city name
        $get_city_name = $this->db->get_where('city',array('city_id'=>$post['city_id']))->row_array();
        $city_name = isset($get_city_name['city_name']) ? $get_city_name['city_name'] : '';

        $api_url = AEPS_KYC_API_URL;

        $postData = array(
            'FirstName' => $post['first_name'],
            'LastName' => $post['last_name'],
            'ShopName' => $post['shop_name'],
            'PanNumber' => $post['pancard_no'],
            'MobileNumber' => $post['mobile'],
            'ParmamentState' => $state_name,
            'ParmamentCity' => $city_name,
            'ParmamentAddress' => $post['address'],
            'ParmamentPin' => $post['pin_code'],
            'AddProofUrl' => base_url($aadhar_photo),
            'AddProofNumber' => $post['aadhar_no'],
            'SelfDeclNumber' => $post['pancard_no'],
            'SelfDeclUrl' => base_url($pancard_photo),
            'MemberId' => $member_code
        );

        $api_post_data = http_build_query($postData);
        
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$api_post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $headers = [
            'username: '.$accountData['aeps_username'],
            'password: '.$accountData['aeps_password'],
            'Content-Type:application/x-www-form-urlencoded'
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $responseData = json_decode($output,true);

        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $memberID,
            'api_url' => $api_url,
            'api_response' => $output,
            'post_data' => json_encode($postData),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('aeps_api_response',$apiData);
        if(isset($responseData['Error']) && $responseData['Error'] == 'False')
        {
            // update aeps status
            $this->db->where('id',$recordID);
            $this->db->update('aeps_member_kyc',array('clear_step'=>1));

            $api_url = AEPS_ONBOARD_API_URL;

            $postData = array(
                'merchantLoginId' => $member_code,
                'merchantLoginPin' => $member_pin,
                'merchantName' => $post['first_name'].' '.$post['last_name'],
                'merchantPhoneNumber' => $post['mobile'],
                'merchantPinCode' => $post['pin_code'],
                'merchantAddress' => $post['address'],
                'MerchantCity' => $city_name,
                'merchantState' => $post['state_id'],
                'userPan' => $post['pancard_no'],
            );

            $api_post_data = http_build_query($postData);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$api_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$api_post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $headers = [
                'username: '.$accountData['aeps_username'],
                'password: '.$accountData['aeps_password'],
                'Content-Type:application/x-www-form-urlencoded'
            ];
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $output = curl_exec ($ch);
            curl_close ($ch);

            $responseData = json_decode($output,true);

            $apiData = array(
                'account_id' => $account_id,
                'user_id' => $memberID,
                'api_url' => $api_url,
                'api_response' => $output,
                'post_data' => json_encode($postData),
                'created' => date('Y-m-d H:i:s'),
                'created_by' => 1
            );
            $this->db->insert('aeps_api_response',$apiData);
            if(isset($responseData['Error']) && $responseData['Error'] == 'False')
            {
                $supermerchantId = isset($responseData['Data'][0]['supermerchantId']) ? $responseData['Data'][0]['supermerchantId'] : '';

                // update aeps status
                /*$this->db->where('id',$memberID);
                $this->db->update('users',array('aeps_status'=>1,'super_merchant_id'=>$supermerchantId));*/

                // update aeps status
                $this->db->where('id',$recordID);
                $this->db->update('aeps_member_kyc',array('clear_step'=>2));

                // send OTP API

                $otp_api_url = AEPS_EKYC_SEND_OTP_API_URL;

                $otpPostData = array(
                    'merchantLoginId' => $member_code,
                    'aadharNumber' => $post['aadhar_no'],
                    'merchantPhoneNumber' => $post['mobile'],
                    'pannumber' => $post['pancard_no'],
                    'MATMSerial' => ''
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
                    $primaryKeyId = isset($otpResponseData['Data'][0]['primaryKeyId']) ? $otpResponseData['Data'][0]['primaryKeyId'] : '';
                    $encodeFPTxnId = isset($otpResponseData['Data'][0]['encodeFPTxnId']) ? $otpResponseData['Data'][0]['encodeFPTxnId'] : '';
                    // update aeps status
                    $this->db->where('id',$recordID);
                    $this->db->update('aeps_member_kyc',array('clear_step'=>3,'primaryKeyId'=>$primaryKeyId,'encodeFPTxnId'=>$encodeFPTxnId));
                    return array('status'=>1,'msg'=>'success','primaryKeyId'=>$primaryKeyId,'encodeFPTxnId'=>$encodeFPTxnId);
                }
                else
                {
                    return array('status'=>0,'msg'=>$otpResponseData['Message']);
                }
            }
            else
            {
                return array('status'=>0,'msg'=>$responseData['Message']);
                
            }
        }
        else
        {
            return array('status'=>0,'msg'=>$responseData['Message']);
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

    public function aepsOTPAuth($post,$memberID,$encodeFPTxnId)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $member_code = $memberData['user_code'];

        $otp_code = $post['otp_code'];

        // check already kyc approved or not
        $get_kyc_data = $this->db->get_where('aeps_member_kyc',array('account_id'=>$account_id,'member_id'=>$memberID,'encodeFPTxnId'=>$encodeFPTxnId,'status'=>0))->row_array();
        $primaryKeyId = isset($get_kyc_data['primaryKeyId']) ? $get_kyc_data['primaryKeyId'] : '';
        $recordID = isset($get_kyc_data['id']) ? $get_kyc_data['id'] : '';

        // send OTP API

        $otp_api_url = AEPS_EKYC_VERIFY_API_URL;

        $otpPostData = array(
            'merchantLoginId' => $member_code,
            'otp' => $otp_code,
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
            $encodeFPTxnId = isset($otpResponseData['Data'][0]['encodeFPTxnId']) ? $otpResponseData['Data'][0]['encodeFPTxnId'] : '';*/
            // update aeps status
            $this->db->where('id',$recordID);
            $this->db->update('aeps_member_kyc',array('clear_step'=>4));
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
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
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
        
        return true;
    }

    public function addStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID)
    {       
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
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
        
        
        return true;
    }

    
    public function saveAepsTxn($txnID,$service,$aadhar_no,$mobile,$amount,$iinno,$api_url,$api_response,$message,$status,$api_response_data = array(),$balanceAmount = '',$bankRRN = '',$transactionAmount = '')
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
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
        $this->db->insert('member_aeps_transaction',$txnData);
        $recordID = $this->db->insert_id();
        return $recordID;
    }
    
    

}


/* end of file: az.php */
/* Location: ./application/models/az.php */