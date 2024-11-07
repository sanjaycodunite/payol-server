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
            $accountData = $this->User->get_account_data($account_id);
            $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];
    	    
            $before_balance = $this->db->get_where('users',array('id'=>$post['member']))->row_array();
            $member_code = $before_balance['user_code'];
            $member_name = $before_balance['name'];

            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($post['member']);
			
			$type = $post['type'];
			
			if($type == 1){
				$after_balance = $before_wallet_balance + $post['amount'];    
			}
			else
			{
				$after_balance = $before_wallet_balance - $post['amount'];    
			}

            $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $post['member'],    
            'before_balance'      => $before_wallet_balance,
            'amount'              => $post['amount'],  
            'after_balance'       => $after_balance,      
            'status'              => 1,
            'type'                => $type,      
            'created'             => date('Y-m-d H:i:s'),      
            'credited_by'         => $loggedUser['id'],
            'description'         => 'By EMP '.$loggedUser['user_code'].' '.$post['description'],
            'is_manual' => 1            
            );

            $this->db->insert('member_wallet',$wallet_data);

            if($accountData['account_type'] == 2){
                if($type == 1)
                {
                    $account_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
                    
                    
                    $after_balance = $account_wallet_balance - $post['amount'];    
                    

                    $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedAccountID,    
                    'before_balance'      => $account_wallet_balance,
                    'amount'              => $post['amount'],  
                    'after_balance'       => $after_balance,      
                    'status'              => 1,
                    'type'                => 2,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'credited_by'         => $loggedUser['id'],
                    'description'         => 'Credited into Member #'.$member_code.' ('.$member_name.')',
                    'is_manual' => 1
                    );

                    $this->db->insert('member_wallet',$wallet_data);


                }
                else
                {
                    // debit wallet
                    $account_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
                    
                    $after_balance = $account_wallet_balance + $post['amount'];    
                    

                    $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedAccountID,    
                    'before_balance'      => $account_wallet_balance,
                    'amount'              => $post['amount'],  
                    'after_balance'       => $after_balance,      
                    'status'              => 1,
                    'type'                => 1,     
                    'wallet_type'         => 1, 
                    'created'             => date('Y-m-d H:i:s'),      
                    'credited_by'         => $loggedUser['id'],
                    'description'         => 'Debited from Member #'.$member_code.' ('.$member_name.')',
                    'is_manual' => 1
                    );

                    $this->db->insert('member_wallet',$wallet_data);

                }
            }

    	return true;
    }


     public function transferUpiWallet($post)
    {       
            $account_id = $this->User->get_domain_account();
            $accountData = $this->User->get_account_data($account_id);
            $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];
            
            $before_balance = $this->db->get_where('users',array('id'=>$post['member']))->row_array();
            $member_code = $before_balance['user_code'];
            $member_name = $before_balance['name'];

            $before_wallet_balance = $this->User->getMemberUpiWalletBalanceSP($post['member']);
            
            $type = 2;
            
            
            $after_balance = $before_wallet_balance - $post['amount'];    
            

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $post['member'],    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $post['amount'],  
                'after_balance'       => $after_balance,      
                'status'              => 1,
                'type'                => $type,      
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedUser['id'],
                'description'         => 'By EMP '.$loggedUser['user_code'].' '.$post['description'],
                'is_manual' => 1            
            );

            $this->db->insert('member_upi_wallet',$wallet_data);

            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($post['member']);
            
            $type = 1;
            $after_balance = $before_wallet_balance + $post['amount'];    
            

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $post['member'],    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $post['amount'],  
                'after_balance'       => $after_balance,      
                'status'              => 1,
                'type'                => $type,      
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedUser['id'],
                'description'         => 'By EMP '.$loggedUser['user_code'].' '.$post['description'],
                'is_manual' => 1            
            );

            $this->db->insert('member_wallet',$wallet_data);

        return true;
    }


    public function updateRequestAuth($requestID,$status)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $get_request_data = $this->db->get_where('member_fund_request',array('id'=>$requestID,'status'=>1))->row_array();
        $memberID = $get_request_data['member_id'];
        $amount = $get_request_data['request_amount'];
        $request_id = $get_request_data['request_id'];
        if($status == 1){
            // update request status
            $this->db->where('id',$requestID);
            $this->db->update('member_fund_request',array('status'=>2,'is_read'=>1,'updated'=>date('Y-m-d H:i:s')));

            //get member wallet_balance
            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

            $after_wallet_balance = $before_wallet_balance + $amount;
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
                'credited_by'         => $loggedUser['id'],
                'description'         => 'Fund Request #'.$request_id.' Approved By Emp - '.$loggedUser['user_code'] 
            );

            $this->db->insert('member_wallet',$wallet_data);
            

            if($accountData['account_type'] == 2){
                //get member wallet_balance
                $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);


                $after_wallet_balance = $before_wallet_balance - $amount;

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
                    'description'         => 'Fund Request #'.$request_id.' Approved Deduction By Emp -'.$loggedUser['user_code'] 
                );

                $this->db->insert('member_wallet',$wallet_data);
                
            }
            
        }
        else
        {
            // update request status
            $this->db->where('id',$requestID);
            $this->db->update('member_fund_request',array('status'=>3,'is_read'=>1,'updated'=>date('Y-m-d H:i:s')));
            

            

        }   
        
        
        return true;
    }

    public function generateFundRequest($post)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        
        $amount = $post['amount'];
        
        // generate request id
        $request_id = time().rand(111,333);
        
        
        $tokenData = array(
            'account_id' => $account_id,
            'request_id' => $request_id,
            'member_id' => $loggedAccountID,
            'request_wallet_type' => 1,
            'request_amount' => $amount,
            'txnid' => isset($post['txnID']) ? $post['txnID'] : '',
            'status' => 1,
            'created' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('member_fund_request',$tokenData);

        return true;
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

    public function upiGenerateStaticQr($account_id,$loggedAccountID,$qr_count)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $tokenData = $this->upiGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        $txnid = time().rand(1111,9999);
        $data = [
            "qrCount" => $qr_count
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
        openssl_private_decrypt($decryptData,$response,$private_key);*/
        
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

                    $txnid = time().rand(1111,9999).rand(1111,9999);

                    $qrData = array(
                        'account_id' => $account_id,
                        'member_id' => $loggedAccountID,
                        'txnid' => $txnid,
                        'qr_image' => $qr_code,
                        'ref_id' => $ref_id,
                        'qr_str' => isset($explode_str[1]) ? $explode_str[1] : '',
                        'status' => 1,
                        'created' => date('Y-m-d H:i:s'),
                        'created_by' => $loggedAccountID
                    );
                    $this->db->insert('upi_collection_qr',$qrData);
                }
            }
            return array(
                'status' => 1,
                'message' => $finalResponse['message']
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

    public function upiCashGenerateStaticQr($account_id,$loggedAccountID,$qr_count)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $tokenData = $this->upiCashGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        $txnid = time().rand(1111,9999);
        $data = [
            "qrCount" => $qr_count
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
        openssl_private_decrypt($decryptData,$response,$private_key);*/
        
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
            if($finalResponse['data'])
            {
                foreach($finalResponse['data'] as $qrcode)
                {
                    $explode_str = explode('#', $qrcode);
                    $qr_code = $explode_str[0];
                    $ref_id = str_replace(UPI_CASH_STATIC_QR_REPLACE_STR, '', $qr_code);
                    $ref_id = str_replace('.png', '', $ref_id);

                    $txnid = time().rand(1111,9999).rand(1111,9999);

                    $qrData = array(
                        'account_id' => $account_id,
                        'member_id' => $loggedAccountID,
                        'txnid' => $txnid,
                        'qr_image' => $qr_code,
                        'ref_id' => $ref_id,
                        'qr_str' => isset($explode_str[1]) ? $explode_str[1] : '',
                        'status' => 1,
                        'created' => date('Y-m-d H:i:s'),
                        'created_by' => $loggedAccountID
                    );
                    $this->db->insert('upi_cash_qr',$qrData);

                }
            }
            return array(
                'status' => 1,
                'message' => $finalResponse['message']
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


    public function saveApiFundWallet($post,$requestData)
    {       
        
       
            $account_id = $this->User->get_domain_account();
            $accountData = $this->User->get_account_data($account_id);
            $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];
            
            $before_balance = $this->db->get_where('users',array('id'=>$post['member']))->row_array();
            $member_code = $before_balance['user_code'];
            $member_name = $before_balance['name'];

            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($post['member']);
            
            $post['type'] = 1;

            $type = $post['type'];
            
            if($type == 1){
                $after_balance = $before_wallet_balance + $post['final_amount'];    
            }
            else
            {
                $after_balance = $before_wallet_balance - $post['final_amount'];    
            }

            $txnid =  isset($requestData['txn_id']) ? $requestData['txn_id'] : 0 ;
            $ref_no = isset($requestData['ref_no']) ? $requestData['ref_no'] : 0 ;
            $request_amount = isset($requestData['amount']) ? $requestData['amount'] : 0 ;
            $charge_amount = $requestData['amount'] -  $post['final_amount'];
            $utr_no = isset($post['utr_no']) ? $post['utr_no'] : 0 ;
            
            $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $post['member'],    
            'before_balance'      => $before_wallet_balance,
            'amount'              => $post['final_amount'],  
            'after_balance'       => $after_balance,      
            'status'              => 1,
            'type'                => $type,      
            'created'             => date('Y-m-d H:i:s'),      
            'credited_by'         => $loggedUser['id'],
            'description'         => 'By Emp - '.$loggedUser['user_code'].' '.$post['description'].' | '.$txnid.' | '.$ref_no.' | '.$post['utr_no'].' | Amount :'.$request_amount.' | Charge : '.number_format($charge_amount,2),
            'is_manual' => 1,
            'transaction_id' =>   $txnid    
            );

            $this->db->insert('member_wallet',$wallet_data);
            $record_id = $this->db->insert_id();

            if ($post['surcharge'] > 0) {

                $surcharge_wallet_data = array(

                    'account_id'          => $account_id,
                    'member_id'           => $post['member'],
                    'transaction_id'      => $txnid,    
                    'record_id'           => $record_id,
                    'amount'              => $post['amount'],  
                    'surcharge'           => $post['surcharge'],  
                    'surcharge_type'      => $post['surcharge_type'],      
                    'final_amount'        => $post['final_amount'],             
                    'created'             => date('Y-m-d H:i:s'),      
                    'credited_by'         => $loggedUser['id']
                    );
    
                $this->db->insert('member_surcharge_wallet',$surcharge_wallet_data);
               
            }
            

            $data = array(

                'utr_no' =>$post['utr_no'],
                'status' =>2
            );
            
            $this->db->where('account_id',$account_id);
            $this->db->where('id',$post['request_id']);
            $this->db->where('status',1);
            $this->db->update('api_member_fund_request',$data);
            
            $callback_url ='https://paymyrecharge.in/myapi/dmr/MorningwalletRequest.aspx';
            //$api_url = '?transcionid='.$txnid.'&utrno='.$post['utr_no'].'&status=SUCCESS&memberID='.$post['member'].'&Amount='.$post['amount'].'&reason='.$post['description'].'';
            $user_callback_data_url  = $callback_url.'?transcionid='.$txnid.'&utrno='.$utr_no.'&status=SUCCESS&memberID='.$post['member'].'&Amount='.$request_amount.'&reason='.urlencode($post['description']);
                    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            //curl_setopt($ch, CURLOPT_POST, true);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);     
            $output = curl_exec($ch); 
            curl_close($ch);
                                    
        log_message('debug', 'PMR Request Data response url - '.json_encode($callback_url));
        log_message('debug', 'PMR Request Auth Api response - '.json_encode($output));   
        
        $log_msg = '['.date('d-m-Y H:i:s').' -  FUND APPROVED Call Back Data send to API Member - '.$post['member'].' - Call Back Data - '.$user_callback_data_url.'.]'.PHP_EOL;
        $this->User->generateCallbackLog($log_msg);
        
        $log_msg = '['.date('d-m-Y H:i:s').' -  FUND APPROVED Call Back Data send to API Member - '.$post['member'].' - Received Response - '.$output.'.]'.PHP_EOL;
        $this->User->generateCallbackLog($log_msg);
                                
        $log_msg = '['.date('d-m-Y H:i:s').' - PMR FUND APPROVED Call Back Send Successfully.]'.PHP_EOL;
        $this->User->generateCallbackLog($log_msg);
                                



            if($accountData['account_type'] == 2){
                if($type == 1)
                {
                    $account_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
                    
                    
                    $after_balance = $account_wallet_balance - $post['final_amount'];    
                    

                    $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedAccountID,    
                    'before_balance'      => $account_wallet_balance,
                    'amount'              => $post['final_amount'],  
                    'after_balance'       => $after_balance,      
                    'status'              => 1,
                    'type'                => 2,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'credited_by'         => $loggedUser['id'],
                    'description'         => 'Credited into Member #'.$member_code.' ('.$member_name.')',
                    'is_manual' => 1,
                    'transaction_id' => $txnid   
                    );

                    $this->db->insert('member_wallet',$wallet_data);


                }
                else
                {
                    // debit wallet
                    $account_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
                    
                    $after_balance = $account_wallet_balance + $post['amount'];    
                    

                    $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedAccountID,    
                    'before_balance'      => $account_wallet_balance,
                    'amount'              => $post['final_amount'],  
                    'after_balance'       => $after_balance,      
                    'status'              => 1,
                    'type'                => 1,     
                    'wallet_type'         => 1, 
                    'created'             => date('Y-m-d H:i:s'),      
                    'credited_by'         => $loggedUser['id'],
                    'description'         => 'Debited from Member #'.$member_code.' ('.$member_name.')',
                    'is_manual' => 1,
                    'transaction_id' => $txnid 
                    );

                    $this->db->insert('member_wallet',$wallet_data);

                }
            }

        return true;
    }


    

    
}


/* end of file: az.php */
/* Location: ./application/models/az.php */