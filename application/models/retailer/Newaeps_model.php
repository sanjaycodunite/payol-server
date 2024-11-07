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

class Newaeps_model extends CI_Model {

    public function __construct() {
        parent::__construct();

    }

    public function activeAEPSMember($post,$aadhar_photo,$pancard_photo)
    {
    	$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $memberID = $loggedUser['id'];
        
        
        $memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $member_code = $memberData['user_code'];
        $member_pin = $memberData['decoded_transaction_password'];
        $member_email = $memberData['email'];
        $user_before_balance = $memberData['wallet_balance'];
        
        $wallet_data = array(
            'account_id' =>$account_id,
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
            'member_code' => $member_code,
            'status'         => 0,
            'charge_amount' => 0,
            'is_surcharge' => 0,
            'admin_charge_amount' => 0,
            'admin_is_surcharge' => 0,
            'created'             => date('Y-m-d H:i:s'),      
            'created_by'         => $loggedUser['id'],
        );

        $this->db->insert('new_aeps_member_kyc',$wallet_data);
        $recordID = $this->db->insert_id();

        $key =$accountData['paysprint_aeps_key'];
        $iv=  $accountData['paysprint_aeps_iv'];
        $datapost = array();
        $datapost['merchantcode'] = $member_code;
        $datapost['mobile'] = $post['mobile'];
        $datapost['email'] = $member_email;
        $datapost['is_new'] = '1';
        $datapost['firm'] = $post['shop_name'];
        $datapost['callback'] = $accountData['paysprint_callback_url'];

        $cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
        $body=       base64_encode($cipher);
        $reqid = time().rand(1111,9999);

        log_message('debug', 'AEPS On Board Auth Api RequestID - '.$reqid);     

        $jwt_payload = array(
            'timestamp'=>time(),
            'partnerId'=>$accountData['paysprint_partner_id'],
            'reqid'=>$reqid
        );

        log_message('debug', 'AEPS On Board Auth Api jwt payload - '.json_encode($jwt_payload));

        $secret = $accountData['paysprint_secret_key'];

        $token = $this->Jwt_model->encode($jwt_payload,$secret);

            //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

        $header = [
            'Token:'.$token           
        ];
        
         if($account_id == 2)
		{
		$header = [
		'Token:'.$token,
		'Authorisedkey:'.$accountData['paysprint_authorized_key']           
		];
        
		    
		}



        $httpUrl = PAYSPRINT_AEPS_NEW_ONBOARD_API_URL;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $httpUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $datapost,
            CURLOPT_HTTPHEADER => $header
        ));

        $output = curl_exec($curl);
        curl_close($curl);


        

        $responseData = json_decode($output,true);

        $apiData = array(
            'account_id' =>$account_id,
            'user_id' => $memberID,
            'api_url' => $httpUrl,
            'api_response' => $output,
            'post_data' => json_encode($datapost),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('aeps_api_response',$apiData);

        if(isset($responseData['response_code']) && $responseData['response_code'] == 1)
        {
            $this->db->where('id',$recordID);
            $this->db->where('account_id',$account_id);
            $this->db->update('new_aeps_member_kyc',array('reqid'=>$responseData['reqid'],'redirecturl'=>$responseData['redirecturl'],'clear_step'=>1));
            return array('status'=>1,'msg'=>'success','redirecturl'=>$responseData['redirecturl']);
            
        }
        else
        {
            $this->db->where('id',$recordID);
            $this->db->where('account_id',$account_id);
            $this->db->update('new_aeps_member_kyc',array('status'=>2));
            return array('status'=>0,'msg'=>$responseData['message']);
        }
    }


  

   public function addBalance($txnID,$aadharNumber,$iin,$amount,$recordID,$serviceType = '')
    {       
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
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


         $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - New AEPS - Distribute Commision/Surcharge Start]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        //$this->User->distribute_aeps_commision($recordID,$txnID,$account_id,$amount,$memberID,$com_amount,$is_surcharge,$com_type,'DT',$loggedUser['user_code']);

        $this->User->distribute_aeps_commision($recordID,$txnID,$memberID,$amount,$com_amount,$is_surcharge,$com_type,'DT',$loggedUser['user_code']);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - New AEPS - Distribute Commision/Surcharge End]'.PHP_EOL;
        $this->User->generateLog($log_msg);


        
        return true;
    }

  public function addStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID)
    {       
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
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


         $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - New AEPS - Distribute Commision/Surcharge Start]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        //$this->User->distribute_aeps_commision($recordID,$txnID,$account_id,$amount,$memberID,$com_amount,$is_surcharge,2,'DT',$loggedUser['user_code']);

        $this->User->distribute_aeps_commision($recordID,$txnID,$memberID,$amount,$com_amount,$is_surcharge,2,'DT',$loggedUser['user_code']);


        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - New AEPS - Distribute Commision/Surcharge End]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        
        
        
        return true;
    }


public function saveAepsTxn($txnID,$service,$aadhar_no,$mobile,$amount,$iinno,$api_url,$api_response,$message,$status,$api_response_data = array(),$balanceAmount = '',$bankRRN = '',$transactionAmount = '')
{
    $account_id = $this->User->get_domain_account();
     $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);

  $memberID = $loggedUser['id'];

  $receipt_id = rand(111111,999999);

  $txnData = array(
    'account_id' =>$account_id,
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
  $this->db->insert('member_new_aeps_transaction',$txnData);
  $recharge_id = $this->db->insert_id();
  
return true;
}


public function debitBalance($txnID,$aadharNumber,$iin,$amount,$recordID,$serviceType = '',$status)
    {       
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
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
        elseif($serviceType == 'Cash Deposite')
        {
            $com_type = 4;
        }

       
        $commisionData = $this->User->get_aeps_commission($amount,$loggedUser['id'],$com_type);
        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

        //get member wallet_balance
        
        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

        // update member wallet
        $after_balance = $before_wallet_balance - $amount;
        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $memberID,    
            'before_balance'      => $before_wallet_balance,
            'amount'              => $amount,  
            'after_balance'       => $after_balance,      
            'status'              => 1,
            'type'                => 2,   
            'wallet_type'         => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'Cash Deposite Txn #'.$txnID.' Amount Debited.'
        );

        $this->db->insert('member_wallet',$wallet_data);
        
        if($status == 2)
        {
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
                    'description'         => 'Cash Deposite Txn #'.$txnID.' Charge Amount Debited.'
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
                    'description'         => 'Cash Deposite Txn #'.$txnID.' Commission Amount Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                if($accountData['is_tds_amount'] == 1)
                            {

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
                                'description'         => 'Cash Deposite Txn  #'.$txnID.'  Commision tds amount deducted'
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
                                'description'         => 'Cash Deposite Txn  #'.$txnID.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

                            }


            }
        }

        //  $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - New AEPS - Distribute Commision/Surcharge Start]'.PHP_EOL;
        // $this->User->generateLog($log_msg);

        //$this->User->distribute_aeps_commision($recordID,$txnID,$account_id,$amount,$memberID,$com_amount,$is_surcharge,$com_type,'DT',$loggedUser['user_code']);

        //$this->User->distribute_aeps_commision($recordID,$txnID,$memberID,$amount,$com_amount,$is_surcharge,$com_type,'DT',$loggedUser['user_code']);

        // save system log
        // $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - New AEPS - Distribute Commision/Surcharge End]'.PHP_EOL;
        // $this->User->generateLog($log_msg);
            
        }
        


        
        return true;
    }



}


/* end of file: az.php */
/* Location: ./application/models/az.php */