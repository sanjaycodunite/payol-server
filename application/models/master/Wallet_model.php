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

class Wallet_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveWallet($post)
    {       
            $account_id = $this->User->get_domain_account();
            $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];
    	    
            $before_balance = $this->db->get_where('users',array('id'=>$post['member'],'account_id'=>$account_id))->row_array();
            $member_code = $before_balance['user_code'];
            $member_name = $before_balance['name'];

            $wallet_balance = $this->User->getMemberWalletBalanceSP($post['member']);

			
			$type = $post['type'];
			$type_title = '';
			if($type == 1){
				$after_balance = $wallet_balance + $post['amount'];    
                $type_title = 'Credited';
			}
			else
			{
				$after_balance = $wallet_balance - $post['amount'];    
                $type_title = 'Debited';
			}

            $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $post['member'],    
            'before_balance'      => $wallet_balance,
            'amount'              => $post['amount'],  
            'after_balance'       => $after_balance,      
            'status'              => 1,
            'type'                => $type,      
            'created'             => date('Y-m-d H:i:s'),      
            'credited_by'         => $loggedUser['id'],
            'description'         => $post['description']            
            );

            $this->db->insert('member_wallet',$wallet_data);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Credit/Debit Wallet Member #'.$member_code.' Amount '.$type_title.'.]'.PHP_EOL;
            $this->User->generateLog($log_msg);

            if($type == 1)
            {
                // debit wallet
                $accountBalanceData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();

                $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

                
                
                $after_balance = $wallet_balance - $post['amount'];    
                

                $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $wallet_balance,
                'amount'              => $post['amount'],  
                'after_balance'       => $after_balance,      
                'status'              => 1,
                'type'                => 2,      
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedUser['id'],
                'description'         => 'Credited into Member #'.$member_code.' ('.$member_name.')'
                );

                $this->db->insert('member_wallet',$wallet_data);

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Wallet Amount Debited from Master Distributor Account.]'.PHP_EOL;
                $this->User->generateLog($log_msg);
            }
            else
            {
                // debit wallet
                $accountBalanceData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();


                $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
                
                
                $after_balance = $wallet_balance + $post['amount'];    
                

                $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $wallet_balance,
                'amount'              => $post['amount'],  
                'after_balance'       => $after_balance,      
                'status'              => 1,
                'type'                => 1,      
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedUser['id'],
                'description'         => 'Debited from Member #'.$member_code.' ('.$member_name.')'
                );

                $this->db->insert('member_wallet',$wallet_data);


                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Wallet Amount Credited into Master Distributor Account.]'.PHP_EOL;
                $this->User->generateLog($log_msg);
            }


    	return true;
    }



    public function walletTransfer($post)
    {       
            $account_id = $this->User->get_domain_account();
            $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];
            
            $before_balance = $this->db->get_where('users',array('user_code'=>$post['member_id'],'account_id'=>$account_id))->row_array();
            $member_code = $before_balance['user_code'];
            $member_name = $before_balance['name'];
            $member_id = $before_balance['id'];
            
            $wallet_balance = $this->User->getMemberWalletBalanceSP($member_id);

            $type = 1; 
            $after_balance = $wallet_balance + $post['amount'];    
            $type_title = 'Credited';
            
            $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $member_id,    
            'before_balance'      => $wallet_balance,
            'amount'              => $post['amount'],  
            'after_balance'       => $after_balance,      
            'status'              => 1,
            'type'                => $type,      
            'created'             => date('Y-m-d H:i:s'),      
            'credited_by'         => $loggedUser['id'],
            'description'         => $post['description']            
            );

            $this->db->insert('member_wallet',$wallet_data);

            $user_wallet = array(
                'wallet_balance'=>$after_balance,        
            );    

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Credit/Debit Wallet Member #'.$member_code.' Amount '.$type_title.'.]'.PHP_EOL;
            $this->User->generateLog($log_msg);

           
            // debit wallet
            $accountBalanceData = $this->db->get_where('users',array('id'=>$loggedAccountID,'account_id'=>$account_id))->row_array();
            
            $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
                
            $after_balance = $wallet_balance - $post['amount'];    
                

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $wallet_balance,
                'amount'              => $post['amount'],  
                'after_balance'       => $after_balance,      
                'status'              => 1,
                'type'                => 2,      
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedUser['id'],
                'description'         => 'Credited into Member #'.$member_code.' ('.$member_name.')'
            );

            $this->db->insert('member_wallet',$wallet_data);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Wallet Amount Debited from Master Distributor Account.]'.PHP_EOL;
                $this->User->generateLog($log_msg);
            
        return true;
    }



    public function updateRequestAuth($requestID,$status)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $get_request_data = $this->db->get_where('member_fund_request',array('id'=>$requestID,'status'=>1))->row_array();
        $memberID = $get_request_data['member_id'];
        $amount = $get_request_data['request_amount'];
        $request_id = $get_request_data['request_id'];
        if($status == 1){
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Fund Request #'.$request_id.' Approved.]'.PHP_EOL;
            $this->User->generateLog($log_msg);
            // update request status
            $this->db->where('id',$requestID);
            $this->db->update('member_fund_request',array('status'=>2,'updated'=>date('Y-m-d H:i:s')));

            //get member wallet_balance
            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Fund Request #'.$request_id.' Member Wallet Balance - '.$before_wallet_balance.'.]'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $after_wallet_balance = $before_wallet_balance + $amount;

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Fund Request #'.$request_id.' Approved Amount - '.$amount.' Member Wallet Balance after Credit - '.$after_wallet_balance.'.]'.PHP_EOL;
            $this->User->generateLog($log_msg);
            // update member wallet
            $wallet_data = array(
                'account_id' => $account_id,
                'member_id'           => $memberID,    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 1,      
                'wallet_type'         => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedAccountID,
                'description'         => 'Fund Request #'.$request_id.' Approved.' 
            );

            $this->db->insert('member_wallet',$wallet_data);
            
            // update member current wallet balance
            
            //get member wallet_balance

            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Fund Request #'.$request_id.' MD Wallet Balance - '.$before_wallet_balance.'.]'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $after_wallet_balance = $before_wallet_balance - $amount;

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Fund Request #'.$request_id.' MD Wallet Balance after approved - '.$after_wallet_balance.'.]'.PHP_EOL;
            $this->User->generateLog($log_msg);
            // update member wallet
            $wallet_data = array(
                'account_id' => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,      
                'wallet_type'         => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedAccountID,
                'description'         => 'Fund Request #'.$request_id.' Approved Deduction.' 
            );

            $this->db->insert('member_wallet',$wallet_data);
            
        }
        else
        {
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Fund Request #'.$request_id.' Rejected.]'.PHP_EOL;
            $this->User->generateLog($log_msg);
            // update request status
            $this->db->where('id',$requestID);
            $this->db->update('member_fund_request',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));
                

        }   
        
        
        return true;
    }

    public function generateFundRequest($post)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        
        $amount = $post['amount'];
        
        // generate request id
        $request_id = time().rand(111,333);
        
        
        $tokenData = array(
            'account_id' => $account_id,
            'request_id' => $request_id,
            'member_id' => $loggedAccountID,
            'request_amount' => $amount,
            'txnid' => isset($post['txnID']) ? $post['txnID'] : '',
            'status' => 1,
            'created' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('member_fund_request',$tokenData);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Add Fund Request #'.$request_id.' Submitted.]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        return true;
    }

    public function cibPayout($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $ifsc_code = $ifsc;
        if($bankID == 35)
        {
            $ifsc_code = 'ICIC0000011';
            $txnType = 'TPA';
        }

        // Create Data
        $data = array 
        (
            "AGGRID"=>$accountData['cib_aggrid'],    
            "AGGRNAME" => $accountData['cib_aggrname'], 
            "CORPID" => $accountData['cib_corpid'], 
            "USERID" => $accountData['cib_userid'], 
            "URN" => $accountData['cib_urn'], 
            "UNIQUEID" => $transaction_id, 
            "DEBITACC" => $accountData['cib_debitacc'], 
            "CREDITACC" => $account_no, 
            "IFSC" => $ifsc_code, 
            "TXNTYPE" => $txnType, 
            "AMOUNT" => $amount, 
            "PAYEENAME" => $account_holder_name, 
            "REMARKS" => "Fund Transfer", 
            "CURRENCY" => "INR", 
            "CUSTOMERINDUCED" => "N", 
        );

        $plainText = json_encode($data);
        $sslEncrypt = $this->sslEncrypt($plainText);
        $key = $accountData['cib_encryption_key'];
        $encryptedData = $this->encrypt($sslEncrypt, $key);
        $payload = json_encode($encryptedData); 

        $tokenData = $this->cibGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout API Post Data - '.$plainText.']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        
        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'key: '.$accountData['cib_security_key'],
            'X-Requested-With: XMLHttpRequest',
            'Authorization: '.$tokenType.' '.$accessToken
        );

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, CIB_TXN_API_URL);

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

        // Set Options - Close

        // Execute
        $result = curl_exec($curl);

        // Close
        curl_close ($curl);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout API Encode Response - '.$result.']'.PHP_EOL;
        $this->User->generateLog($log_msg);

        /*$result = '"ed1aa071eb77be4d4f5d699b335b5b33c0c3c47124f61d28efe2124569aca054d9ed6632cedb18915117988cfb08df812bc0a16361f642abc20d14582f3c3253dbe4b9b9853ab9ca3234c368c211a792023a458e9fb7c1972e6e0f55ad081bba4ab1a8d71d746b013d22363b087713bcba8c1adf127348bdd4618fcd2082b42a5179aadd190fcce21a26c12e7c1934a80aa946abb7c921e474880e2cea78d4c8faffb026b396c8dc49ba2fba7bdb010e2aa075cff825cc5cdc0aa4f390bd21aca51cf013a8a3558ebc6c6f8e2fed3a90814de798fd8b639599613d39ef3de79aa0e5fb8c047c80cf7df3f7f978c91501e7fabd8600cd7224d2f9f10b3406a5e229b23a8a44cbbd1fb55d88dbe9a6f69c6999fd47d44aca102fc2d28328b826d28a1edc5748243c8b072204a69c977828455302b723e32ef2f705868674c11abec0fce54105b6e729a8797c19a2ecd52c3a73f42e178d007e381fd259d4793b63db532a7e717e70677d54df6c40083742b43c16758c99ecf5b9afeb7ceb4089bcd8c837bc4fa88539b53a3d7adb690f244c7a6e996e25ec6ed3c0ea5ca4e4a3d3bd643fd0dabfd4384d41de40d14c5491e514308fd4b4381db040abe942ce48d22804e9b6c4da939bf3437b5328632b5eb8d586a95c7082a5c46c7d01441b90e3cdc885410ed307a1a039eafc2681097521b44256ed463648b6ca443379936524c5d3e74f8ad9b81d4796ba32ca2e0960"';*/

        $result = str_replace('"','',$result);

        $response = $this->sslDecrypt($this->decrypt($result, $key)); 
        $finalResponse = json_decode($response);

        /*$finalResponse = '{"status":200,"message":"SUCCESS","data":{"CORP_ID":"568464421","USER_ID":"USER1","AGGR_ID":"BAAS0007","AGGR_NAME":"COGENT","REQID":"654930416","STATUS":"SUCCESS","UNIQUEID":"'.$transaction_id.'","URN":"URN568464421","UTRNUMBER":"025450073981","RESPONSE":"SUCCESS"}}';*/

        $decodeResponse = json_decode($finalResponse,true);
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout API Response - '.$finalResponse.']'.PHP_EOL;
        $this->User->generateLog($log_msg);

        // save api response
        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'txnid' => $transaction_id,
            'token_type' => $tokenType,
            'access_token' => $accessToken,
            'post_data' => $plainText,
            'api_response' => $finalResponse,
            'api_url' => CIB_TXN_API_URL,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('cib_api_response',$apiData);

        if(isset($decodeResponse['status']) && $decodeResponse['status'] == 200)
        {
            if(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'SUCCESS' && $decodeResponse['data']['STATUS'] == 'SUCCESS')
            {
                // SUCCESS RESPONSE
                return $finalResponse = array(
                    'status' => 2,
                    'msg' => 'Transaction successfully proceed.',
                    'requestID' => $decodeResponse['data']['REQID'],
                    'txnID' => $decodeResponse['data']['UNIQUEID'],
                    'rrno' => $decodeResponse['data']['UTRNUMBER']
                );
            }
            elseif(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'SUCCESS' && $decodeResponse['data']['STATUS'] == 'PENDING')
            {
                // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
            }
            elseif(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'FAILURE' && $decodeResponse['data']['STATUS'] == 'PENDING')
            {
                // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
                
            }
            elseif(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'FAILURE' && $decodeResponse['data']['STATUS'] == 'FAILURE')
            {
                // SUCCESS RESPONSE
                return $finalResponse = array(
                    'status' => 3,
                    'msg' => $decodeResponse['message'],
                    'txnID' => $transaction_id
                );
            }
            else
            {
                // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
            }
        }
        elseif(!isset($decodeResponse['status']))
        {
            // PENDING RESPONSE
            return $finalResponse = array(
                'status' => 1,
                'msg' => 'Transaction is under process, status will be updated soon.',
                'txnID' => $transaction_id,
            );
        }
        else
        {
            // FAILED RESPONSE
            return $finalResponse = array(
                'status' => 3,
                'msg' => $decodeResponse['message'],
                'txnID' => $transaction_id,
            );
        }


    }

    public function cibMoneyTransfer($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $ifsc_code = $ifsc;
        if($bankID == 35)
        {
            $ifsc_code = 'ICIC0000011';
            $txnType = 'TPA';
        }

        // Create Data
        $data = array 
        (
            "AGGRID"=>$accountData['cib_aggrid'],    
            "AGGRNAME" => $accountData['cib_aggrname'], 
            "CORPID" => $accountData['cib_corpid'], 
            "USERID" => $accountData['cib_userid'], 
            "URN" => $accountData['cib_urn'], 
            "UNIQUEID" => $transaction_id, 
            "DEBITACC" => $accountData['cib_debitacc'], 
            "CREDITACC" => $account_no, 
            "IFSC" => $ifsc_code, 
            "TXNTYPE" => $txnType, 
            "AMOUNT" => $amount, 
            "PAYEENAME" => $account_holder_name, 
            "REMARKS" => "Fund Transfer", 
            "CURRENCY" => "INR", 
            "CUSTOMERINDUCED" => "N", 
        );

        $plainText = json_encode($data);
        $sslEncrypt = $this->sslEncrypt($plainText);
        $key = $accountData['cib_encryption_key'];
        $encryptedData = $this->encrypt($sslEncrypt, $key);
        $payload = json_encode($encryptedData); 

        $tokenData = $this->cibGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API Post Data - '.$plainText.']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        
        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'key: '.$accountData['cib_security_key'],
            'X-Requested-With: XMLHttpRequest',
            'Authorization: '.$tokenType.' '.$accessToken
        );

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, CIB_TXN_API_URL);

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

        // Set Options - Close

        // Execute
        $result = curl_exec($curl);

        // Close
        curl_close ($curl);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API Encode Response - '.$result.']'.PHP_EOL;
        $this->User->generateLog($log_msg);

        /*$result = '"ed1aa071eb77be4d4f5d699b335b5b33c0c3c47124f61d28efe2124569aca054d9ed6632cedb18915117988cfb08df812bc0a16361f642abc20d14582f3c3253dbe4b9b9853ab9ca3234c368c211a792023a458e9fb7c1972e6e0f55ad081bba4ab1a8d71d746b013d22363b087713bcba8c1adf127348bdd4618fcd2082b42a5179aadd190fcce21a26c12e7c1934a80aa946abb7c921e474880e2cea78d4c8faffb026b396c8dc49ba2fba7bdb010e2aa075cff825cc5cdc0aa4f390bd21aca51cf013a8a3558ebc6c6f8e2fed3a90814de798fd8b639599613d39ef3de79aa0e5fb8c047c80cf7df3f7f978c91501e7fabd8600cd7224d2f9f10b3406a5e229b23a8a44cbbd1fb55d88dbe9a6f69c6999fd47d44aca102fc2d28328b826d28a1edc5748243c8b072204a69c977828455302b723e32ef2f705868674c11abec0fce54105b6e729a8797c19a2ecd52c3a73f42e178d007e381fd259d4793b63db532a7e717e70677d54df6c40083742b43c16758c99ecf5b9afeb7ceb4089bcd8c837bc4fa88539b53a3d7adb690f244c7a6e996e25ec6ed3c0ea5ca4e4a3d3bd643fd0dabfd4384d41de40d14c5491e514308fd4b4381db040abe942ce48d22804e9b6c4da939bf3437b5328632b5eb8d586a95c7082a5c46c7d01441b90e3cdc885410ed307a1a039eafc2681097521b44256ed463648b6ca443379936524c5d3e74f8ad9b81d4796ba32ca2e0960"';*/

        $result = str_replace('"','',$result);

        $response = $this->sslDecrypt($this->decrypt($result, $key)); 
        $finalResponse = json_decode($response);
        $decodeResponse = json_decode($finalResponse,true);
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - DMT API Response - '.$finalResponse.']'.PHP_EOL;
        $this->User->generateLog($log_msg);

        // save api response
        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'txnid' => $transaction_id,
            'token_type' => $tokenType,
            'access_token' => $accessToken,
            'post_data' => $plainText,
            'api_response' => $finalResponse,
            'api_url' => CIB_TXN_API_URL,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('cib_api_response',$apiData);

        if(isset($decodeResponse['status']) && $decodeResponse['status'] == 200)
        {
            if(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'SUCCESS' && $decodeResponse['data']['STATUS'] == 'SUCCESS')
            {
                // SUCCESS RESPONSE
                return $finalResponse = array(
                    'status' => 2,
                    'msg' => 'Transaction successfully proceed.',
                    'requestID' => $decodeResponse['data']['REQID'],
                    'txnID' => $decodeResponse['data']['UNIQUEID'],
                    'rrno' => $decodeResponse['data']['UTRNUMBER']
                );
            }
            elseif(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'SUCCESS' && $decodeResponse['data']['STATUS'] == 'PENDING')
            {
                // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
            }
            elseif(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'FAILURE' && $decodeResponse['data']['STATUS'] == 'PENDING')
            {
                // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
                
            }
            elseif(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'FAILURE' && $decodeResponse['data']['STATUS'] == 'FAILURE')
            {
                // SUCCESS RESPONSE
                return $finalResponse = array(
                    'status' => 3,
                    'msg' => $decodeResponse['message'],
                    'txnID' => $transaction_id
                );
            }
            else
            {
                // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
            }
        }
        elseif(!isset($decodeResponse['status']))
        {
            // PENDING RESPONSE
            return $finalResponse = array(
                'status' => 1,
                'msg' => 'Transaction is under process, status will be updated soon.',
                'txnID' => $transaction_id,
            );
        }
        else
        {
            // FAILED RESPONSE
            return $finalResponse = array(
                'status' => 3,
                'msg' => $decodeResponse['message'],
                'txnID' => $transaction_id,
            );
        }


    }

    public function cibGenerateToken()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        // Create Data
        $data = array 
        (
            "email"=>$accountData['cib_email'],    
            "password" => $accountData['cib_password'], 
        );

        // Generate JSON
        $json = json_encode($data);
        
        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'X-Requested-With: XMLHttpRequest'
        );

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, CIB_TOKEN_API_URL);

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

        // Set Options - Close

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$output = '{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZGFiNzI2YjllYmU3OTZiMThlYWZlYTZjMzc3Y2ZkMzVmOTRiOWIwNWU3YWM4NmEzZTkwN2M1NjM5OGE5Yjc2ZWQ2ODkyNmEwNTFmZTQwYmUiLCJpYXQiOjE2MzYzNzM1MjQsIm5iZiI6MTYzNjM3MzUyNCwiZXhwIjoxNjY3OTA5NTI0LCJzdWIiOiIxNSIsInNjb3BlcyI6W119.lQke9YX4iq0agg_g1g3N6Q4_DbGkZs-qKXQ4UQ0UVkFAVJZLdk-XCDlK_yKMyPSjXKtBji_P31Z4zL2t2nQUCduq7KzN9SH798fwLMp6IAqsuPBkrISnx5zSWVz10mpi5eXFnYKf27diH8FlZ_PiJAKZJ69GJeVF7Ir6L4X_vaTxLOu9ZGBQHK07qi4g6nCcPe6JGzKUD0V6AXG85AYDv6ztcBVqNAcydgUKWhmPiLxgDx851IlhUTLomhQ593f1BNzVR9_xyZynwTJdELTufe3QVn9aYi3fTPQI77T7Y5jZhVAqbWsP_vewggYP4_eSEDeaeU5PQkyZNZj1Ne9uQ0aZG1R4oisZE9Ecy2cTQdYW_1kvzVkwXak8KFS4IaH_u7VkfayUoaJ8pY0wm4UuFBh-8b-D9E1ajGPmpIx_GyOm0wvemr280xezuFFAWQdmdP6U9wfXKclMAwj9DwEyuEpITyzP_XdWFuuYqHES0IikfvV6whcfLaI2Xfg1LMdre4kyZxhjB3oCagBg_3Veu3W-1OCeoTJdPcWt4zXINBEhOPL651zwWhra6Btzt-Kz5zJMGIKdzgfxOX4A88CydL-Eje2u9fIxyFEiXYe647_rok2Dpo-gM4KU8a1ycP8ND0UmopT47W6xh8vqSAD6M3IwD8c0OAgMVOA1XQMlwkg","token_type":"Bearer","expires_at":"2022-11-08 12:12:04"}';*/

        $result = json_decode($output,true);

        return $result;

    }

    public function sslEncrypt($dataToEncrypt)
    {
        
        //BANK PUBLIC KEY UAT
        /*$public_key = '-----BEGIN CERTIFICATE-----
MIIFhDCCA2wCCQCIqfcsomoC1jANBgkqhkiG9w0BAQUFADCBgzELMAkGA1UEBhMCSU4xFDASBgNV
BAgMC01BSEFSQVNIVFJBMQ8wDQYDVQQHDAZNVU1CQUkxGDAWBgNVBAoMD0lDSUNJIEJhbmsgTHRk
LjEMMAoGA1UECwwDQ0lCMSUwIwYDVQQDDBx3d3cuY2libmV4dGFwaS5pY2ljaWJhbmsuY29tMB4X
DTE3MDQxMzA3MTkyOVoXDTIyMDQxMjA3MTkyOVowgYMxCzAJBgNVBAYTAklOMRQwEgYDVQQIDAtN
QUhBUkFTSFRSQTEPMA0GA1UEBwwGTVVNQkFJMRgwFgYDVQQKDA9JQ0lDSSBCYW5rIEx0ZC4xDDAK
BgNVBAsMA0NJQjElMCMGA1UEAwwcd3d3LmNpYm5leHRhcGkuaWNpY2liYW5rLmNvbTCCAiIwDQYJ
KoZIhvcNAQEBBQADggIPADCCAgoCggIBAM+en2ErEsETmfoZJjf3I5DIc8KAt6dv/ZkKYHcpli1g
yLFjJNbZnyk4Um7UaKvU0fpqnsLboapXKR0iHFp4/7SR9kTh4FfvFrrp2pKmQd8f/Rf6OPk2/48i
X7sCs0nl8IZYMqe1Tt1YAMFPJjPIH/ERx3vnWYhgHUmRGJqjHfo4NeNR0IarF3HAYX4hh6K0LaAQ
hUoq6SuWyWf9m9qzHRHpWq4eJRsbhPYLaTtt8XS+vPpBjFjfQreDtgWdIXwKuHq8EOS/KxBifThC
tEBMGZUSYZBoldq1kdaakkt5FaXhe+g0FWrLcxalaSS4bHK0QCv1Lbh3tcPetCO3XyR1Jj28SL+5
gYm464jmjMGURJwocWUhuNd0qAKt8bMv9NCDgKiWSmAlzeznRYeNaay2ckg5aB5tNO5l/8pUh8Ew
qLyKECFnCoNvBlcaoJIvZ0sprQO+dHzggT/Q9wl0XRFUkPh4SFGHIiqldy6VgA6I7uVWb7ve1Y3P
4yhlfTDV/Hr4ZL4gTFVrorS1a4Tqap38iqHnfM3djwgwbnzv0TJZCywZ5ED8MRDmub6W4jNYMVar
uG1gLVf7gE2sUY/dgTRu1Hdw3/YlOY9XpQebBP37RD3+Up+oEYxjPe04Cy4rTFx9/8SluuBPvwNH
WVmHkv1ULNHum0VQ3kej17jbEeO+FftJAgMBAAEwDQYJKoZIhvcNAQEFBQADggIBAMGX2dKuXsGj
ujhKxZOFzo8A0QKu+nsw+pFtiJ5KjyOR1vW9pOdG7roJJGr6cU5fUDlUpYDDVIvPiVbPYgWLkVe3
7+tpM8T77ZYSXdO7G9hhU8uw2pcRHiQMlDotV/RcTGZHyVVaw7TJty3xMH2j0/FIHejcFaYXZYQB
A5+zKc7PBsvwn/KQgJ9R4BTqmdWeca1r0+iBXGq1iRg4IGePf0lIc+80AUneC1ceC07RfvI0PJpk
LVTkDCXdNK7QtG/cIqjdZ1jtB+ne7cwtksw1ewu5dE3BFNmqdT3DmKHAupTc2ILSup2w/JEEepMI
DHO8GvqR0dUXS5xCcXNKwXUMiLPYA56mRKoST5+e2RO5WtVQMHiizEF5iID+WjyXNlVtqMarEjih
Z0+/vkABp/Q3AfKs3rtaXxU4crt+RLaaldG/dBXOoUDTpaNR+ktUkNmEPTe9zc7pwwRDC2zNylt4
FnhNP2b2t+RLuP+smAROVaXA1owpte3zeh7aiUe02Y6udEzVrKCAvRUiCKoCDH9N101k3lzCFy80
rRquHZ7ZZmUrX4DksuPnSuLILR5ss6UkQTZbg7HXtMN2lDTgPjO2UMCjqI+5gPGTqdld4XWDTEW0
xdyhJiEgATeqQllbn47B7C7603ltWFpoInafn2NwxBW89wv938bMKpxFxmQcseGH
-----END CERTIFICATE-----
';*/

//BANK PUBLIC KEY LIVE

$public_key = '-----BEGIN CERTIFICATE-----
MIIFiTCCA3GgAwIBAgIJAPhKHX+xSWb7MA0GCSqGSIb3DQEBBQUAMFsxCzAJBgNVBAYTAklOMRQw
EgYDVQQIDAtNQUhBUkFTSFRSQTEPMA0GA1UEBwwGTVVNQkFJMRcwFQYDVQQKDA5JQ0lDSSBCYW5r
IEx0ZDEMMAoGA1UECwwDQlRHMB4XDTE3MDkyNTA4NTcwM1oXDTIwMDYyMDA4NTcwM1owWzELMAkG
A1UEBhMCSU4xFDASBgNVBAgMC01BSEFSQVNIVFJBMQ8wDQYDVQQHDAZNVU1CQUkxFzAVBgNVBAoM
DklDSUNJIEJhbmsgTHRkMQwwCgYDVQQLDANCVEcwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIK
AoICAQCpyw5vtvzONTBwIB89oI6tNmONluYlac/IGsOIJgz/NHUbvONTQasTEcFNAQLgGkljV3ZN
o2ld8Yl6njjAqd1RFfNLbcNDq5AzWRqHEvIfbdcna/wRCz1KUVS+GyZjjoDBovoAZFNo/jM6WU6D
bA4iDW7KaSkTgczt6/0vNo5/BpiDluFNLUUHtlM6D4l9ZFw/A9xoE7jms5saTCoYMz/3Vgpr6lmp
g7gckfHmHEfecSwT0N639+wGEAGdfxzAr3yEc6yCE9XjBIRiTFafBJO32SeO6LQsjl8YGa7mYsQN
Yj+Xt2+kztyq4/M5/I5En3rWVKhP6s4o7bB10uZPO2DHEo49OHnCr2MVq0lwco341xGKPaVwZ9oI
fZX6Jh7ca0y3hTXABZrA5sXfmYwaxYxz/4o1JYeiYjqSvYcKnNt7c7pcpYLKiBC/6RENxVgoNqnY
QJZj/mYkcmvNPFmHvnAGtmnRA+hm06we0dMUO0ZQJhSqP6sfM5oDeZqMAIy291YWW7Hpoimti8db
GD+pMFQxjzS5cuxPl/JjHfPRLUx/MSf26Xu1hhgfh4/9lseuNAjuHfqQS/KiT6BnpuqoMpXkx9K0
FPcfrd8TdHhuGGihuyEtEfj+3G2uMSYE4xEmDx5BQCTXA6x5I6IQyNUN+IorkbDTOJfB2tjxhbQz
rgITHQIDAQABo1AwTjAdBgNVHQ4EFgQUWI7/jLcNvrchEffA3NCjgmTDHSMwHwYDVR0jBBgwFoAU
WI7/jLcNvrchEffA3NCjgmTDHSMwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOCAgEAlfzy
H4x6x7QUtFuL8liD6WO6Vn8W9r/vuEH3YlpiWpnggzRPq2tnDZuJ+3ohB//PSBtCHKDu28NKJMLE
NqAVpgFtashkFlmMAXTpy4vYnTfj3MyAHYr9fwtvEmUKEfiIIC1WXDQzWWP4dFLdJ//jint9bdyM
Iqx+H5ddPXmfWXwAsCs3GlXGVwEmtcc9v7OliCHyyO2s++L+ATz5FoyxKCmZyn1GHD3gmvFjXicI
WB+Us1uRkrDFO8clS1hWvmvF/ghfGYmlKOqTzu/TCY4d9u/CciNesens3iSHEgs58r/9gaxwpiEs
tRolx9eVjkem1ZI5IUCUbRC40r8sL+eEObcwhVV87nrKH2l0BX8nM/ux0lqAkRO+Ek9tdP5TmHT0
XE2E/PMJO7/AlzYvN3oznT9ZeKfu6WbNIZrFCcO6GsoNi8+pKZsWuSePbrhRQC+d3whHS7tAanS8
+6gbPMMoAfkSKt0yaogld6RI2Af1C6QerxZR2LcJM5ni8eCz1cIvS3XSpkG5hcRMXHJAGkc5GAoE
Dj08gZbQVtE4FeJRfTJoX6cpXM6cBODsi8xKzpBCGNNcA/p4r/6XGg2csXyKCCLrVtk0VNKyr/Ba
6T5dfbbuzGcbL/dVd5d/7A9cGJTkk2gRxIL6bBMKn0Qm68mSDUhVFg001zi0JR3nOy9M6Hs=
-----END CERTIFICATE-----
';
        
        
        openssl_get_publickey($public_key);
        openssl_public_encrypt($dataToEncrypt,$encryptedText,$public_key);
        return $encryptedText;
    } 

    public function encrypt($plainText, $key)
    {
        $secretKey = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    } 

    public function decrypt($encryptedText, $key)
    {
        $key = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = $this->hextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    } 

    public function sslDecrypt($dataToDecrypt)
    {
        $private_key = '-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----';
        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($dataToDecrypt,$decryptedText,$private_key);
        return $decryptedText;
    } 

    public function hextobin($hexString)
    {
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length)
        {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0)
            { $binString = $packedString; }
            else
            { $binString .= $packedString; }
            $count += 2;
        }
        return $binString;
    }

    public function upiGenerateToken()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        // Create Data
        $data = array 
        (
            "email"=>$accountData['upi_email'],    
            "password" => $accountData['upi_password'], 
        );

        // Generate JSON
        $json = json_encode($data);
        
        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'X-Requested-With: XMLHttpRequest'
        );

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, CIB_TOKEN_API_URL);

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

        // Set Options - Close

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$output = '{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZGFiNzI2YjllYmU3OTZiMThlYWZlYTZjMzc3Y2ZkMzVmOTRiOWIwNWU3YWM4NmEzZTkwN2M1NjM5OGE5Yjc2ZWQ2ODkyNmEwNTFmZTQwYmUiLCJpYXQiOjE2MzYzNzM1MjQsIm5iZiI6MTYzNjM3MzUyNCwiZXhwIjoxNjY3OTA5NTI0LCJzdWIiOiIxNSIsInNjb3BlcyI6W119.lQke9YX4iq0agg_g1g3N6Q4_DbGkZs-qKXQ4UQ0UVkFAVJZLdk-XCDlK_yKMyPSjXKtBji_P31Z4zL2t2nQUCduq7KzN9SH798fwLMp6IAqsuPBkrISnx5zSWVz10mpi5eXFnYKf27diH8FlZ_PiJAKZJ69GJeVF7Ir6L4X_vaTxLOu9ZGBQHK07qi4g6nCcPe6JGzKUD0V6AXG85AYDv6ztcBVqNAcydgUKWhmPiLxgDx851IlhUTLomhQ593f1BNzVR9_xyZynwTJdELTufe3QVn9aYi3fTPQI77T7Y5jZhVAqbWsP_vewggYP4_eSEDeaeU5PQkyZNZj1Ne9uQ0aZG1R4oisZE9Ecy2cTQdYW_1kvzVkwXak8KFS4IaH_u7VkfayUoaJ8pY0wm4UuFBh-8b-D9E1ajGPmpIx_GyOm0wvemr280xezuFFAWQdmdP6U9wfXKclMAwj9DwEyuEpITyzP_XdWFuuYqHES0IikfvV6whcfLaI2Xfg1LMdre4kyZxhjB3oCagBg_3Veu3W-1OCeoTJdPcWt4zXINBEhOPL651zwWhra6Btzt-Kz5zJMGIKdzgfxOX4A88CydL-Eje2u9fIxyFEiXYe647_rok2Dpo-gM4KU8a1ycP8ND0UmopT47W6xh8vqSAD6M3IwD8c0OAgMVOA1XQMlwkg","token_type":"Bearer","expires_at":"2022-11-08 12:12:04"}';*/

        $result = json_decode($output,true);

        return $result;

    }

    public function upiSendRequest($account_id,$loggedAccountID,$post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $tokenData = $this->upiGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        //get member wallet_balance
        $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $txnid = time().rand(1111,9999);
        $data = [
            "merchantId" => $accountData['upi_merchant_id'],
            "merchantName" => $accountData['upi_merchant_name'],
            "terminalId" => $accountData['upi_terminal_id'],
            "subMerchantId" => $memberData['mobile'],
            "subMerchantName" => $memberData['name'],
            "merchantTranId" => $txnid,
            "billNumber" => $txnid,
            "payerVa" => $post['vpa_id'],
            "amount" => $post['amount'],
            "note" => $post['description'],
            "collectByDate" => date('d/m/Y H:i A', strtotime('+1 day'))
        ];
        
        $plainText = json_encode($data);
        
        $pub_key_string = $accountData['upi_bank_certificate'];

        openssl_get_publickey($pub_key_string);
        openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
        $key = $accountData['upi_encryption_key'];
        $hexString = md5($key);
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length)
        {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0)
            { $binString = $packedString; }
            else
            { $binString .= $packedString; }
            $count += 2;
        } 
        $secretKey = $binString;
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedData = bin2hex($openMode);
        $payload = json_encode($encryptedData);
        $header = array(
         'Content-Type:application/json',
         'Key:'.$accountData['upi_security_key'],
         'X-Requested-With:XMLHttpRequest',
         'Authorization: '.$tokenType.' '.$accessToken
         ); 
        
        $httpUrl = UPI_REQUEST_API_URL;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $httpUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $header
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $decodeResult = json_decode($result);
        $decryptData = $this->decrypt($decodeResult, $key);

        $private_key = $accountData['upi_private_certificate'];

        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($decryptData,$response,$private_key);
        $response = json_decode($response);

        /*$response = '{"status":200,"message":"Transaction initiated","data":{"response":"92","merchantId":"420661","subMerchantId":"8104758957","terminalId":"6012","success":"true","message":"Transaction initiated","merchantTranId":"'.$txnid.'","BankRRN":"132785139496"}}';*/

        // save transaction data
        $txnData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'type_id' => 1,
            'txnid' => $txnid,
            'amount' => $post['amount'],
            'vpa_id' => $post['vpa_id'],
            'description' => $post['description'],
            'status' => 1,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $loggedAccountID
        );
        $this->db->insert('upi_transaction',$txnData);
        $record_id = $this->db->insert_id();

        // save upi api response
        $apiData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'api_url' => $httpUrl,
            'post_data' => $plainText,
            'response' => $response,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('upi_api_response',$apiData);

        $finalResponse = json_decode($response,true);
        if(isset($finalResponse['message']) && $finalResponse['message'] == 'Transaction initiated')
        {
            $this->db->where('id',$record_id);
            $this->db->update('upi_transaction',array('bank_rrno'=>$finalResponse['data']['BankRRN']));
            return array(
                'status' => 1,
                'message' => $finalResponse['message'],
                'merchantTranId' => $finalResponse['data']['merchantTranId'],
                'BankRRN' => $finalResponse['data']['BankRRN']
            );
        }
        else
        {
            $this->db->where('id',$record_id);
            $this->db->update('upi_transaction',array('status'=>3));
            return array(
                'status' => 0,
                'message' => $finalResponse['message']
            );
        }

    }

     public function upiGenerateDynamicQr($account_id,$loggedAccountID,$post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        
        //get member wallet_balance
        $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $txnid = time().rand(1111,9999);
        $amount = $post['amount'];

        $intent = 'upi://pay?pa=payol@yesbank&pn=MATRE&tr='.$txnid.'&tn='.urlencode('Pay to merchant').'&am='.$amount.'&cu=INR&mam=1';
        //$qr_image = 'https://chart.googleapis.com/chart?cht=qr&chs=500x500&chl='.urlencode($intent);
        $qr_image = 'https://quickchart.io/qr?text='.urlencode($intent).'&size=500';
        // save transaction data
        if($intent)
        {
            $user_ip_address = $this->User->get_user_ip();

        $txnData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'refId' => $txnid,
            'amount' => $amount,
            'qr_image' => $qr_image,
            'status' => 1,
            'ip_address' => $user_ip_address,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $loggedAccountID
        );
        $this->db->insert('upi_dynamic_qr',$txnData);
        $record_id = $this->db->insert_id();
            return array(
                'status' => 1,
                'message' => ' Qr Generate Success',
                'qr' => $qr_image
            );

        }
        else
        {
            return array(
                'status' => 0,
                'message' => 'Server Side Error'
            );
        }

    } 



    public function addFund($account_id,$loggedAccountID,$post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        
        //get member wallet_balance
        $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $txnid = time().rand(1111,9999);
        $amount = $post['amount'];

        $intent = 'upi://pay?pa=payol@yesbank&pn=MATRE&tr='.$txnid.'&tn='.urlencode('Pay to merchant').'&am='.$amount.'&cu=INR&mam=1';
        //$qr_image = 'https://chart.googleapis.com/chart?cht=qr&chs=500x500&chl='.urlencode($intent);
        $qr_image = 'https://quickchart.io/qr?text='.urlencode($intent).'&size=500';
        // save transaction data
        if($intent)
        {
            $user_ip_address = $this->User->get_user_ip();

        $txnData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'refId' => $txnid,
            'amount' => $amount,
            'qr_image' => $qr_image,
            'status' => 1,
            'ip_address' => $user_ip_address,
            'created' => date('Y-m-d H:i:s'),
            'is_add_fund'=>1,
            'created_by' => $loggedAccountID
        );
        $this->db->insert('upi_dynamic_qr',$txnData);
        $record_id = $this->db->insert_id();
            return array(
                'status' => 1,
                'message' => ' Qr Generate Success',
                'qr' => $qr_image
            );

        }
        else
        {
            return array(
                'status' => 0,
                'message' => 'Server Side Error'
            );
        }

    } 
    

    public function upiGenerateStaticQr($account_id,$loggedAccountID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $tokenData = $this->upiGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        //get member wallet_balance
        $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $txnid = time().rand(1111,9999);
        $data = [
            "qrCount" => 1
        ];
        
        $plainText = json_encode($data);
        
        $pub_key_string = $accountData['upi_bank_certificate'];

        openssl_get_publickey($pub_key_string);
        openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
        $key = $accountData['upi_encryption_key'];
        $hexString = md5($key);
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length)
        {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0)
            { $binString = $packedString; }
            else
            { $binString .= $packedString; }
            $count += 2;
        } 
        $secretKey = $binString;
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedData = bin2hex($openMode);
        $payload = json_encode($encryptedData);
        $header = array(
         'Content-Type:application/json',
         'Key:'.$accountData['upi_security_key'],
         'X-Requested-With:XMLHttpRequest',
         'Authorization: '.$tokenType.' '.$accessToken
         ); 
        
        $httpUrl = UPI_STATIC_QR_API_URL2;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $httpUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $header
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $decodeResult = json_decode($result);
        $response = $this->decrypt($decodeResult, $key);

        /*$private_key = $accountData['upi_private_certificate'];

        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($decryptData,$response,$private_key);
        $response = json_decode($response);*/

        /*$response = '{"status":200,"message":"Static QR generated.","data":["https:\/\/cogentmind.tech\/api\/staticQRAPIWLCollection\/Board My TripNU3zCL8X4Vwy.png","https:\/\/cogentmind.tech\/api\/staticQRAPIWLCollection\/Board My TripZRb8YBodMYae.png"]}';*/

        // save upi api response
        $apiData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'api_url' => $httpUrl,
            'post_data' => $plainText,
            'response' => $response,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('upi_api_response',$apiData);

        $finalResponse = json_decode($response,true);
        if(isset($finalResponse['status']) && $finalResponse['status'] == 200)
        {
            if($finalResponse['data'])
            {
                foreach($finalResponse['data'] as $qrcode)
                {
                    $explode_str = explode('#', $qrcode);
                    $qr_code = $explode_str[0];

                    $ref_id = str_replace(UPI_STATIC_QR_REPLACE_STR, '', $qr_code);
                    $ref_id = str_replace('.png', '', $ref_id);

                    $this->db->where('account_id',$account_id);
                    $this->db->where('id',$loggedAccountID);
                    $this->db->update('users',array('qr_url'=>$qr_code,'is_upi_qr_active'=>1,'upi_qr_ref_id'=>$ref_id));

                    $txnid = time().rand(1111,9999).rand(1111,9999);

                    $qrData = array(
                        'account_id' => $account_id,
                        'member_id' => $loggedAccountID,
                        'txnid' => $txnid,
                        'qr_image' => $qr_code,
                        'ref_id' => $ref_id,
                        'qr_str' => isset($explode_str[1]) ? $explode_str[1] : '',
                        'is_map' => 1,
                        'map_member_id' => $loggedAccountID,
                        'status' => 1,
                        'created' => date('Y-m-d H:i:s'),
                        'created_by' => $loggedAccountID
                    );
                    $this->db->insert('upi_collection_qr',$qrData);

                    $this->mapQrName($ref_id,$loggedAccountID);

                }
            }

            return array(
                'status' => 1,
                'message' => $finalResponse['message'],
                'qr' => $finalResponse['data'][0]
            );
        }
        else
        {
            return array(
                'status' => 0,
                'message' => $finalResponse['message']
            );
        }

    } 

    public function cibPayoutOpen($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $ifsc_code = $ifsc;
        if($bankID == 35)
        {
            $ifsc_code = 'ICIC0000011';
            $txnType = 'TPA';
        }

        // Create Data
        $data = array 
        (
            "AGGRID"=>"OTOE0622",    
            "AGGRNAME" => "TRUSTNCART", 
            "CORPID" => "578854260", 
            "USERID" => "SAMADNAI", 
            "URN" => "SR234708898", 
            "UNIQUEID" => $transaction_id, 
            "DEBITACC" => "114705001499", 
            "CREDITACC" => $account_no, 
            "IFSC" => $ifsc_code, 
            "TXNTYPE" => $txnType, 
            "AMOUNT" => $amount, 
            "PAYEENAME" => $account_holder_name, 
            "REMARKS" => "Fund Transfer", 
            "CURRENCY" => "INR"
        );
        
        $plainText = json_encode($data);
        $payload = base64_encode($this->sslEncrypt($plainText));
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout API Post Data - '.$plainText.']'.PHP_EOL;
        $this->User->generateLog($log_msg);
        
        // Create Header
        $header = [
            'Content-type: text/plain',
            'apikey: eASJqqNDQGnQsIb1FqMxYJAj4Dy9nZld'
        ];

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, CIB_TXN_API_URL);

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

        // Set Options - Close

        // Execute
        $result = curl_exec($curl);

        // Close
        curl_close ($curl);



        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout API Encode Response - '.$result.']'.PHP_EOL;
        $this->User->generateLog($log_msg);

        $response = $this->sslDecrypt(base64_decode($result));

        /*$response = '{"CORP_ID":"578854260","USER_ID":"SAMADNAI","AGGR_ID":"OTOE0622","AGGR_NAME":"TRUSTNCART","REQID":"1153971281","STATUS":"SUCCESS","UNIQUEID":"16826864545174","URN":"SR234708898","UTRNUMBER":"032008953851","RESPONSE":"SUCCESS"}';*/

        /*{"CORP_ID":"578854260","USER_ID":"SAMADNAI","AGGR_ID":"OTOE0622","AGGR_NAME":"TRUSTNCART","REQID":"1153971281","STATUS":"SUCCESS","UNIQUEID":"16826864545174","URN":"SR234708898","UTRNUMBER":"032008953851","RESPONSE":"SUCCESS"}*/

        $decodeResponse = json_decode($response,true);
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Payout API Response - '.$response.']'.PHP_EOL;
        $this->User->generateLog($log_msg);

        // save api response
        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'txnid' => $transaction_id,
            'token_type' => $tokenType,
            'post_data' => $plainText,
            'api_response' => $response,
            'api_url' => CIB_TXN_API_URL,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('cib_api_response',$apiData);

        
        if(isset($decodeResponse['RESPONSE']) && $decodeResponse['RESPONSE'] == 'SUCCESS' && $decodeResponse['STATUS'] == 'SUCCESS')
        {
            // SUCCESS RESPONSE
            return $finalResponse = array(
                'status' => 2,
                'msg' => 'Transaction successfully proceed.',
                'requestID' => $decodeResponse['REQID'],
                'txnID' => $decodeResponse['UNIQUEID'],
                'rrno' => $decodeResponse['UTRNUMBER']
            );
        }
        elseif(isset($decodeResponse['RESPONSE']) && $decodeResponse['RESPONSE'] == 'SUCCESS' && $decodeResponse['STATUS'] == 'PENDING')
        {
            // PENDING RESPONSE
            return $finalResponse = array(
                'status' => 1,
                'msg' => 'Transaction is under process, status will be updated soon.',
                'txnID' => $transaction_id,
            );
        }
        elseif(isset($decodeResponse['RESPONSE']) && $decodeResponse['RESPONSE'] == 'FAILURE' && $decodeResponse['STATUS'] == 'PENDING')
        {
            // PENDING RESPONSE
            return $finalResponse = array(
                'status' => 1,
                'msg' => 'Transaction is under process, status will be updated soon.',
                'txnID' => $transaction_id,
            );
            
        }
        elseif(isset($decodeResponse['RESPONSE']) && $decodeResponse['RESPONSE'] == 'FAILURE' && $decodeResponse['STATUS'] == 'FAILURE')
        {
            // SUCCESS RESPONSE
            return $finalResponse = array(
                'status' => 3,
                'msg' => $decodeResponse['MESSAGE'],
                'txnID' => $transaction_id
            );
        }
        else
        {
            // PENDING RESPONSE
            return $finalResponse = array(
                'status' => 1,
                'msg' => 'Transaction is under process, status will be updated soon.',
                'txnID' => $transaction_id,
            );
        }
        

    }

    public function upiCashGenerateToken()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        // Create Data
        $data = array 
        (
            "email"=>$accountData['upi_cash_email'],    
            "password" => $accountData['upi_cash_password'], 
        );

        // Generate JSON
        $json = json_encode($data);
        
        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'X-Requested-With: XMLHttpRequest'
        );

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, CIB_TOKEN_API_URL);

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

        // Set Options - Close

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$output = '{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZGFiNzI2YjllYmU3OTZiMThlYWZlYTZjMzc3Y2ZkMzVmOTRiOWIwNWU3YWM4NmEzZTkwN2M1NjM5OGE5Yjc2ZWQ2ODkyNmEwNTFmZTQwYmUiLCJpYXQiOjE2MzYzNzM1MjQsIm5iZiI6MTYzNjM3MzUyNCwiZXhwIjoxNjY3OTA5NTI0LCJzdWIiOiIxNSIsInNjb3BlcyI6W119.lQke9YX4iq0agg_g1g3N6Q4_DbGkZs-qKXQ4UQ0UVkFAVJZLdk-XCDlK_yKMyPSjXKtBji_P31Z4zL2t2nQUCduq7KzN9SH798fwLMp6IAqsuPBkrISnx5zSWVz10mpi5eXFnYKf27diH8FlZ_PiJAKZJ69GJeVF7Ir6L4X_vaTxLOu9ZGBQHK07qi4g6nCcPe6JGzKUD0V6AXG85AYDv6ztcBVqNAcydgUKWhmPiLxgDx851IlhUTLomhQ593f1BNzVR9_xyZynwTJdELTufe3QVn9aYi3fTPQI77T7Y5jZhVAqbWsP_vewggYP4_eSEDeaeU5PQkyZNZj1Ne9uQ0aZG1R4oisZE9Ecy2cTQdYW_1kvzVkwXak8KFS4IaH_u7VkfayUoaJ8pY0wm4UuFBh-8b-D9E1ajGPmpIx_GyOm0wvemr280xezuFFAWQdmdP6U9wfXKclMAwj9DwEyuEpITyzP_XdWFuuYqHES0IikfvV6whcfLaI2Xfg1LMdre4kyZxhjB3oCagBg_3Veu3W-1OCeoTJdPcWt4zXINBEhOPL651zwWhra6Btzt-Kz5zJMGIKdzgfxOX4A88CydL-Eje2u9fIxyFEiXYe647_rok2Dpo-gM4KU8a1ycP8ND0UmopT47W6xh8vqSAD6M3IwD8c0OAgMVOA1XQMlwkg","token_type":"Bearer","expires_at":"2022-11-08 12:12:04"}';*/

        $result = json_decode($output,true);

        return $result;

    }

    public function upiSendCashRequest($account_id,$loggedAccountID,$post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $tokenData = $this->upiCashGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        //get member wallet_balance
        $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $txnid = time().rand(1111,9999);
        $data = [
            "merchantId" => $accountData['upi_cash_merchant_id'],
            "merchantName" => $accountData['upi_cash_merchant_name'],
            "terminalId" => $accountData['upi_cash_terminal_id'],
            "subMerchantId" => $memberData['mobile'],
            "subMerchantName" => $memberData['name'],
            "merchantTranId" => $txnid,
            "billNumber" => $txnid,
            "payerVa" => $post['vpa_id'],
            "amount" => $post['amount'],
            "note" => $post['description'],
            "collectByDate" => date('d/m/Y H:i A', strtotime('+1 day'))
        ];
        
        $plainText = json_encode($data);
        
        $pub_key_string = $accountData['upi_cash_bank_certificate'];

        openssl_get_publickey($pub_key_string);
        openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
        $key = $accountData['upi_cash_encryption_key'];
        $hexString = md5($key);
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length)
        {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0)
            { $binString = $packedString; }
            else
            { $binString .= $packedString; }
            $count += 2;
        } 
        $secretKey = $binString;
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedData = bin2hex($openMode);
        $payload = json_encode($encryptedData);
        $header = array(
         'Content-Type:application/json',
         'Key:'.$accountData['upi_cash_security_key'],
         'X-Requested-With:XMLHttpRequest',
         'Authorization: '.$tokenType.' '.$accessToken
         ); 
        
        $httpUrl = UPI_CASH_REQUEST_API_URL;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $httpUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $header
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $decodeResult = json_decode($result);
        $decryptData = $this->decrypt($decodeResult, $key);

        $private_key = $accountData['upi_cash_private_certificate'];

        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($decryptData,$response,$private_key);
        $response = json_decode($response);

        /*$response = '{"status":200,"message":"Transaction initiated","data":{"response":"92","merchantId":"420661","subMerchantId":"8104758957","terminalId":"6012","success":"true","message":"Transaction initiated","merchantTranId":"'.$txnid.'","BankRRN":"132785139496"}}';*/

        // save transaction data
        $txnData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'type_id' => 1,
            'txnid' => $txnid,
            'amount' => $post['amount'],
            'vpa_id' => $post['vpa_id'],
            'description' => $post['description'],
            'status' => 1,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $loggedAccountID
        );
        $this->db->insert('upi_cash_transaction',$txnData);
        $record_id = $this->db->insert_id();

        // save upi api response
        $apiData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'api_url' => $httpUrl,
            'post_data' => $plainText,
            'response' => $response,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('upi_cash_api_response',$apiData);

        $finalResponse = json_decode($response,true);
        if(isset($finalResponse['message']) && $finalResponse['message'] == 'Transaction initiated')
        {
            $this->db->where('id',$record_id);
            $this->db->update('upi_cash_transaction',array('bank_rrno'=>$finalResponse['data']['BankRRN']));
            return array(
                'status' => 1,
                'message' => $finalResponse['message'],
                'merchantTranId' => $finalResponse['data']['merchantTranId'],
                'BankRRN' => $finalResponse['data']['BankRRN']
            );
        }
        else
        {
            $this->db->where('id',$record_id);
            $this->db->update('upi_cash_transaction',array('status'=>3));
            return array(
                'status' => 0,
                'message' => $finalResponse['message']
            );
        }

    }

    public function upiCashGenerateDynamicQr($account_id,$loggedAccountID,$post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $tokenData = $this->upiCashGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        //get member wallet_balance
        $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $txnid = time().rand(1111,9999);
        $data = [
            "merchantId" => $accountData['upi_cash_merchant_id'],
            "terminalId" => $accountData['upi_cash_terminal_id'],
            "merchantTranId" => $txnid,
            "billNumber" => $txnid,
            "amount" => $post['amount']
        ];
        
        $plainText = json_encode($data);
        
        $pub_key_string = $accountData['upi_cash_bank_certificate'];

        openssl_get_publickey($pub_key_string);
        openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
        $key = $accountData['upi_cash_encryption_key'];
        $hexString = md5($key);
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length)
        {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0)
            { $binString = $packedString; }
            else
            { $binString .= $packedString; }
            $count += 2;
        } 
        $secretKey = $binString;
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedData = bin2hex($openMode);
        $payload = json_encode($encryptedData);
        $header = array(
         'Content-Type:application/json',
         'Key:'.$accountData['upi_cash_security_key'],
         'X-Requested-With:XMLHttpRequest',
         'Authorization: '.$tokenType.' '.$accessToken
         ); 
        
        $httpUrl = UPI_CASH_QR_API_URL;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $httpUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $header
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $decodeResult = json_decode($result);
        $decryptData = $this->decrypt($decodeResult, $key);

        $private_key = $accountData['upi_cash_private_certificate'];

        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($decryptData,$response,$private_key);
        $response = json_decode($response);

        /*$response = '{"status":200,"message":"Transaction initiated","data":"https:\/\/cogentmind.tech\/api\/dynamicQRAPIWLCollection\/Board My TripEZV2021112517524300907870.png"}';*/

        // save upi api response
        $apiData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'api_url' => $httpUrl,
            'post_data' => $plainText,
            'response' => $response,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('upi_cash_api_response',$apiData);

        $finalResponse = json_decode($response,true);
        if(isset($finalResponse['message']) && $finalResponse['message'] == 'Transaction initiated')
        {
            // save transaction data
            $txnData = array(
                'account_id' => $account_id,
                'member_id' => $loggedAccountID,
                'txnid' => $txnid,
                'amount' => $post['amount'],
                'qr_image' => $finalResponse['data'],
                'status' => 1,
                'created' => date('Y-m-d H:i:s'),
                'created_by' => $loggedAccountID
            );
            $this->db->insert('upi_cash_dynamic_qr',$txnData);
            $record_id = $this->db->insert_id();
            return array(
                'status' => 1,
                'message' => $finalResponse['message'],
                'qr' => $finalResponse['data']
            );
        }
        else
        {
            return array(
                'status' => 0,
                'message' => $finalResponse['message']
            );
        }

    } 

    public function upiCashGenerateStaticQr($account_id,$loggedAccountID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $tokenData = $this->upiCashGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        //get member wallet_balance
        $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $txnid = time().rand(1111,9999);
        $data = [
            "qrCount" => 1
        ];
        
        $plainText = json_encode($data);
        
        $pub_key_string = $accountData['upi_cash_bank_certificate'];

        openssl_get_publickey($pub_key_string);
        openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
        $key = $accountData['upi_cash_encryption_key'];
        $hexString = md5($key);
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length)
        {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0)
            { $binString = $packedString; }
            else
            { $binString .= $packedString; }
            $count += 2;
        } 
        $secretKey = $binString;
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedData = bin2hex($openMode);
        $payload = json_encode($encryptedData);
        $header = array(
         'Content-Type:application/json',
         'Key:'.$accountData['upi_cash_security_key'],
         'X-Requested-With:XMLHttpRequest',
         'Authorization: '.$tokenType.' '.$accessToken
         ); 
        
        $httpUrl = UPI_CASH_STATIC_QR_API_URL;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $httpUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $header
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $decodeResult = json_decode($result);
        $response = $this->decrypt($decodeResult, $key);

        /*$private_key = $accountData['upi_cash_private_certificate'];

        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($decryptData,$response,$private_key);
        $response = json_decode($response);*/

        /*$response = '{"status":200,"message":"Static QR generated.","data":["https:\/\/cogentmind.tech\/api\/staticQRAPIWLCollection\/Board My TripNU3zCL8X4Vwy.png","https:\/\/cogentmind.tech\/api\/staticQRAPIWLCollection\/Board My TripZRb8YBodMYae.png"]}';*/

        // save upi api response
        $apiData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'api_url' => $httpUrl,
            'post_data' => $plainText,
            'response' => $response,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('upi_cash_api_response',$apiData);

        $finalResponse = json_decode($response,true);
        if(isset($finalResponse['status']) && $finalResponse['status'] == 200)
        {
            $qrcode = $finalResponse['data'][0];
            $explode_str = explode('#', $qrcode);
            $qr_code = $explode_str[0];
            $ref_id = str_replace(UPI_CASH_STATIC_QR_REPLACE_STR, '', $qr_code);
            $ref_id = str_replace('.png', '', $ref_id);
            $this->db->where('account_id',$account_id);
            $this->db->where('id',$loggedAccountID);
            $this->db->update('users',array('cash_qr_url'=>$qr_code,'is_upi_cash_qr_active'=>1,'upi_cash_qr_ref_id'=>$ref_id));

            $txnid = time().rand(1111,9999).rand(1111,9999);

            $qrData = array(
                'account_id' => $account_id,
                'member_id' => $loggedAccountID,
                'txnid' => $txnid,
                'qr_image' => $qr_code,
                'ref_id' => $ref_id,
                'qr_str' => isset($explode_str[1]) ? $explode_str[1] : '',
                'is_map' => 1,
                'map_member_id' => $loggedAccountID,
                'status' => 1,
                'created' => date('Y-m-d H:i:s'),
                'created_by' => $loggedAccountID
            );
            $this->db->insert('upi_cash_qr',$qrData);

            $this->mapCashQrName($ref_id,$loggedAccountID);

            return array(
                'status' => 1,
                'message' => $finalResponse['message'],
                'qr' => $finalResponse['data'][0]
            );
        }
        else
        {
            return array(
                'status' => 0,
                'message' => $finalResponse['message']
            );
        }

    } 

    public function mapQrName($ref_id,$loggedAccountID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $tokenData = $this->upiGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        //get member wallet_balance
        $memberData = $this->db->select('name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $data = [
            "referenceID" => $ref_id,
            "displayName" => $memberData['name']
        ];
        
        $plainText = json_encode($data);
        
        $pub_key_string = $accountData['upi_bank_certificate'];

        openssl_get_publickey($pub_key_string);
        openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
        $key = $accountData['upi_encryption_key'];
        $hexString = md5($key);
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length)
        {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0)
            { $binString = $packedString; }
            else
            { $binString .= $packedString; }
            $count += 2;
        } 
        $secretKey = $binString;
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedData = bin2hex($openMode);
        $payload = json_encode($encryptedData);
        $header = array(
         'Content-Type:application/json',
         'Key:'.$accountData['upi_security_key'],
         'X-Requested-With:XMLHttpRequest',
         'Authorization: '.$tokenType.' '.$accessToken
         ); 
        
        $httpUrl = UPI_QR_MAP_NAME;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $httpUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $header
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $decodeResult = json_decode($result);
        $decryptData = $this->decrypt($decodeResult, $key);

        $private_key = $accountData['upi_private_certificate'];

        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($decryptData,$response,$private_key);
        $response = json_decode($response);

        /*$response = '{"status":200,"message":"Transaction initiated","data":"https:\/\/cogentmind.tech\/api\/dynamicQRAPIWLCollection\/Board My TripEZV2021112517524300907870.png"}';*/

        // save upi api response
        $apiData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'api_url' => $httpUrl,
            'post_data' => $plainText,
            'response' => $response,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('upi_api_response',$apiData);

        $finalResponse = json_decode($response,true);
        return array(
            'status' => 1,
            'message' => isset($finalResponse['message']) ? $finalResponse['message'] : ''
        );

    } 

    public function mapCashQrName($ref_id,$loggedAccountID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $tokenData = $this->upiCashGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        //get member wallet_balance
        $memberData = $this->db->select('name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $data = [
            "referenceID" => $ref_id,
            "displayName" => $memberData['name']
        ];
        
        $plainText = json_encode($data);
        
        $pub_key_string = $accountData['upi_cash_bank_certificate'];

        openssl_get_publickey($pub_key_string);
        openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
        $key = $accountData['upi_cash_encryption_key'];
        $hexString = md5($key);
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length)
        {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0)
            { $binString = $packedString; }
            else
            { $binString .= $packedString; }
            $count += 2;
        } 
        $secretKey = $binString;
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedData = bin2hex($openMode);
        $payload = json_encode($encryptedData);
        $header = array(
         'Content-Type:application/json',
         'Key:'.$accountData['upi_cash_security_key'],
         'X-Requested-With:XMLHttpRequest',
         'Authorization: '.$tokenType.' '.$accessToken
         ); 
        
        $httpUrl = UPI_CASH_QR_MAP_NAME;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $httpUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $header
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $decodeResult = json_decode($result);
        $decryptData = $this->decrypt($decodeResult, $key);

        $private_key = $accountData['upi_cash_private_certificate'];

        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($decryptData,$response,$private_key);
        $response = json_decode($response);

        /*$response = '{"status":200,"message":"Transaction initiated","data":"https:\/\/cogentmind.tech\/api\/dynamicQRAPIWLCollection\/Board My TripEZV2021112517524300907870.png"}';*/

        // save upi api response
        $apiData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'api_url' => $httpUrl,
            'post_data' => $plainText,
            'response' => $response,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('upi_cash_api_response',$apiData);

        $finalResponse = json_decode($response,true);
        return array(
            'status' => 1,
            'message' => isset($finalResponse['message']) ? $finalResponse['message'] : ''
        );

    } 


    public function sslEncryptCollectpay($dataToEncrypt)
    {

        
//BANK PUBLIC KEY UAT

       /* $public_key = '-----BEGIN CERTIFICATE-----
MIIE7jCCAtagAwIBAgIIWmFBujLqylAwDQYJKoZIhvcNAQEMBQAwFTETMBEGA1UEAwwKcnNhX2Fw
aWtleTAeFw0xODEwMzAwNDQ3MThaFw0yMzEwMjkwNDQ3MThaMBUxEzARBgNVBAMMCnJzYV9hcGlr
ZXkwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIKAoICAQCwjBVK1CLppIwsFm7e+Fp85Hk1Mw2n
5Nc/DKT/pWhpJB8OdlpJA9iF23hrxfbXkrBfCkgvV4Ek4fY1byOnkA7hZq4dYTASCAm89oLwWDNm
0OGNh7E6T7/JoNtjtT0Gh8lJTvpUgHFGg3tiYCScAqul+fS6Rc8+5THk3L9zLzme6eqjkzwBx/ZV
XBIZlAwFkVKbfLFg51LiVoOUz6zXD7nAsMyNhKAgybvqulV07eGzafZ1IBgzpcw5qo0PAd1mTqfy
U+CK9hVeNPPspT16qQWd5xa+fa6BEjuGCumVnFLTbSTRAF5h3QAfvMlkpLdejlXJwvTVQ79Zg5C8
Hu/yWB7tOJBncIKue7KSpwn+vkMws79wpAB5mL4tD3kVCDf2Og7wbtt87v5rcazxF7eZFbsADzHV
oSftdkw5S7iXgh82/CHbRXhzPfG8Zd2v1ksW+Bfnn3czEIMGOSJrKfMbyCYtVMihoi0/L6SHA7++
N9aRrQvfK9PeXnlHgf8pErGUdpjnwdV0tu5atSgf/iBuRgVgUL6t6MFbnBsTQUmZYiQRcsqxOVdy
yfp4DOLgFHGJ1D/isgR/ypalIXMmhuK8GdZ7hukEDX2Dc3js8OkPnFLq6Ps4NIGESfbZSeyINoZX
5GGxdgD/GpokKMHr5bsI3TQujCvzuxShPhUArzCs6TgPmwIDAQABo0IwQDAdBgNVHQ4EFgQUyNoW
eeLVSzVybz7gcZnZlj01cv4wHwYDVR0jBBgwFoAUyNoWeeLVSzVybz7gcZnZlj01cv4wDQYJKoZI
hvcNAQEMBQADggIBADuwEh31OI66oSMB6a79Pd6WSqiyD2NBskdRF7st7CRP5vqeH4P/4srNFAqC
9CjsOmXmSpZFckYQ4zgtqnVQBY7jQlCuSHmg8/Lr1qIzRsMvQmhvp6DJ+bEfQgqcJ+a6tR9cH6hD
VahoMZDEpt3J0fIp30z+O7wJ03K6q5Di/rNey6Ac3GoZwlCi8OFCTmwihcn56I+ssxAqzlq53hzO
iBLLmcMTrWSJWePPkYEhrbBxywg1qJRRGWwkfr1dbRZ22umLHU0R/QdK+jQtqyzghqJpd3T/lHzK
uzAsa0s1R+qMqurKu6mulcLp/XmZpY+Fm4T0WRXzcZBf9trkCSO2Z3VvkCTeGu/WAi3UQpx4HfGr
x02m/h8CHCPPO+PKYthpvSR+0jmiVBaaBo029UG0i2oYBTckng2sy0fx0E+rHnR7pk5Worv8BMm5
sewPUkDDJMZhLtm/bd/VxlI/b56vEA7HvupSWzc7xXV8lZOHVEUAotrlXz+Je2MkEEQIDnYUOYhw
78yFMJJddK9tJVRy8tr8I2j6Zi62jQp/Zltq5JOwpOw/9poovd9wgeRBjuFnscoR/YWrNdPjsjpJ
g/CCb6mthz4R2Mu4enD1YghW7w5darrlUHaYAk+SnwWhMwDwZWWfrVNeEaNq/t/gRm/Ljy+Of3lA
nztA1PrT4bk1KvZX
-----END CERTIFICATE-----';*/


//BANK PUBLIC KEY LIVE

        $public_key = '-----BEGIN CERTIFICATE-----
MIIGPjCCBSagAwIBAgIRAJig5hCghJQ8AAAAAFDbeaEwDQYJKoZIhvcNAQELBQAwgboxCzAJBgNV
BAYTAlVTMRYwFAYDVQQKEw1FbnRydXN0LCBJbmMuMSgwJgYDVQQLEx9TZWUgd3d3LmVudHJ1c3Qu
bmV0L2xlZ2FsLXRlcm1zMTkwNwYDVQQLEzAoYykgMjAxMiBFbnRydXN0LCBJbmMuIC0gZm9yIGF1
dGhvcml6ZWQgdXNlIG9ubHkxLjAsBgNVBAMTJUVudHJ1c3QgQ2VydGlmaWNhdGlvbiBBdXRob3Jp
dHkgLSBMMUswHhcNMTcwMjE1MDYyMzM5WhcNMTgwMjE0MDY1MzM3WjBxMQswCQYDVQQGEwJJTjEO
MAwGA1UECBMFVGhhbmUxEjAQBgNVBAcTCVBhdGxpcGFkYTEbMBkGA1UEChMSSUNJQ0kgQmFuayBM
aW1pdGVkMSEwHwYDVQQDExhlYXp5cGF5YXBpLmljaWNpYmFuay5jb20wggIiMA0GCSqGSIb3DQEB
AQUAA4ICDwAwggIKAoICAQDMzgMIqYh4HJScGoIguQFDg7+dcNY7V9BJRWdxE0L5BVHf83vGi36k
9jXXBFB7n16opD4QBEUV4uRrOisZeWA6cMGG7NTqwx1sCXxVdz/rXNqiAiWUXa+p7SsRqnbroK4k
st0mvLRI0bWvBDLw6AHhVF7+xdFRrR+d3zChM8Y3n58ZHiTMgeFf5gBFNC36frdwGR/Fp/naAu/G
1ntRa7rHLS/wuMjNg+j10ka8jfrkRf6Uxi3ogt/FjnEE0/k+xVqvMp2tlPi1mZlUb08CT2/ulfEb
lg6wBoWvipabnp8plK05L+vt1E4MXLkIbdu2WXuUNGSY5AREbWQRO6zmS12i2i3kdQHgq9bIsvMu
FzIbWuvG19btL+Vs/UtBa6FoHyLrbT+h3UDt4insSwxd0Lsxze/G91wFR8w9xGrcmGv5m2yCQuhz
6bDREiV0u6xNMn8FpTph63zU39OcdE0RQXpkAVRy6c/A2YKlAFLkaeOODDTfMbSohOQLV3DT/2Kr
wQ6o0QkT+WAC4z7RCnbhPujhop5mIzyMWtSIVx/+50fmJOQvF+QqifXOb0/XzJnhtNy8vgw8C7k7
xRMGwcgHEtxVJCU162UU3rjVtA7/DKFDoK/P47DLf4c2VT5OY98jLgz5Ez+GDCquQjY/zocYp4bA
sG/I+LCqnEAxQA2S2lNg4wIDAQABo4IBhTCCAYEwDgYDVR0PAQH/BAQDAgWgMBMGA1UdJQQMMAoG
CCsGAQUFBwMCMDMGA1UdHwQsMCowKKAmoCSGImh0dHA6Ly9jcmwuZW50cnVzdC5uZXQvbGV2ZWwx
ay5jcmwwSwYDVR0gBEQwQjA2BgpghkgBhvpsCgEFMCgwJgYIKwYBBQUHAgEWGmh0dHA6Ly93d3cu
ZW50cnVzdC5uZXQvcnBhMAgGBmeBDAECAjBoBggrBgEFBQcBAQRcMFowIwYIKwYBBQUHMAGGF2h0
dHA6Ly9vY3NwLmVudHJ1c3QubmV0MDMGCCsGAQUFBzAChidodHRwOi8vYWlhLmVudHJ1c3QubmV0
L2wxay1jaGFpbjI1Ni5jZXIwIwYDVR0RBBwwGoIYZWF6eXBheWFwaS5pY2ljaWJhbmsuY29tMB8G
A1UdIwQYMBaAFIKicHTdvFM/z3vU981/p2DGCky/MB0GA1UdDgQWBBSG2RKU6li1ezRacfLkf5Tr
kECMPjAJBgNVHRMEAjAAMA0GCSqGSIb3DQEBCwUAA4IBAQBvVGUQGLBhs9GNpNjLTVdc3WY5pYQE
5fP/otaX7GBx4bRrRZlPPh/vzIg79Ry9vs/GdvZIiyVczKZeB2ih9PKJySWEPXPgnR+aroHnQMVK
hOhBoKcohtpUmjnQLgL400h6NkQ9GS0yDebLlbJxicGIAhq+OSJhUXeYKLIk38ngPCYwL+PjHPn9
1ds0ehCuOFMuKKaY4e+hsKzc8KZPyTM7hbtw86kbheOizTGQ8M9s8ZTRnTYblSPk5w5A3fqaikG7
bAYKNWcdBGgdOCnHHCDPSP0ghtf4klR1tT99PSW1HHZ/VL8tmvw+/YNXzIdNxB+MPm3OM/A8Dz6i
khpZKNeP
-----END CERTIFICATE-----
';
        
        
        openssl_get_publickey($public_key);
        openssl_public_encrypt($dataToEncrypt,$encryptedText,$public_key,OPENSSL_PKCS1_PADDING);
        return $encryptedText;
    } 


    public function sslDecryptComposite($dataToDecrypt)
    {
        $private_key = '-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----';
        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($dataToDecrypt,$decryptedText,$private_key);
        return $decryptedText;
    } 

    
    

    
}


/* end of file: az.php */
/* Location: ./application/models/az.php */