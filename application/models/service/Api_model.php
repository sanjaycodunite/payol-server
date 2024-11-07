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

class Api_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        
    }

    public function cibPayout($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType,$loggedAccountID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

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


    public function cibMoneyTransfer($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType,$loggedAccountID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        
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


    public function cibPayoutOpen($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType,$loggedAccountID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        
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
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $public_key = $accountData['cib_bank_certificate'];
        
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
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $private_key = $accountData['cib_private_certificate'];

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

    // public function upiGenerateDynamicQr($account_id,$loggedAccountID,$post)
    // {
    //     $account_id = $this->User->get_domain_account();
    //     $accountData = $this->User->get_account_data($account_id);

    //     $tokenData = $this->upiGenerateToken();
    //     $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
    //     $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

    //     //get member wallet_balance
    //     $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

    //     $txnid = time().rand(1111,9999);
    //     $data = [
    //         "merchantId" => $accountData['upi_merchant_id'],
    //         "terminalId" => $accountData['upi_terminal_id'],
    //         "merchantTranId" => $txnid,
    //         "billNumber" => $txnid,
    //         "amount" => $post['amount']
    //     ];
        
    //     $plainText = json_encode($data);
        
    //     $pub_key_string = $accountData['upi_bank_certificate'];

    //     openssl_get_publickey($pub_key_string);
    //     openssl_public_encrypt($plainText,$crypttext,$pub_key_string);
    //     $key = $accountData['upi_encryption_key'];
    //     $hexString = md5($key);
    //     $length = strlen($hexString);
    //     $binString = "";
    //     $count = 0;
    //     while ($count < $length)
    //     {
    //         $subString = substr($hexString, $count, 2);
    //         $packedString = pack("H*", $subString);
    //         if ($count == 0)
    //         { $binString = $packedString; }
    //         else
    //         { $binString .= $packedString; }
    //         $count += 2;
    //     } 
    //     $secretKey = $binString;
    //     $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    //     $openMode = openssl_encrypt($crypttext, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
    //     $encryptedData = bin2hex($openMode);
    //     $payload = json_encode($encryptedData);
    //     $header = array(
    //      'Content-Type:application/json',
    //      'Key:'.$accountData['upi_security_key'],
    //      'X-Requested-With:XMLHttpRequest',
    //      'Authorization: '.$tokenType.' '.$accessToken
    //      ); 
        
    //     $httpUrl = UPI_QR_API_URL;
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => $httpUrl,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 60,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => $payload,
    //         CURLOPT_HTTPHEADER => $header
    //     ));

    //     $result = curl_exec($curl);
    //     curl_close($curl);
    //     $decodeResult = json_decode($result);
    //     $decryptData = $this->decrypt($decodeResult, $key);

    //     $private_key = $accountData['upi_private_certificate'];

    //     $private_key = openssl_get_privatekey($private_key, "");
    //     openssl_private_decrypt($decryptData,$response,$private_key);
    //     $response = json_decode($response);

    //     /*$response = '{"status":200,"message":"Transaction initiated","data":"https:\/\/cogentmind.tech\/api\/dynamicQRAPIWLCollection\/Board My TripEZV2021112517524300907870.png"}';*/

    //     // save upi api response
    //     $apiData = array(
    //         'account_id' => $account_id,
    //         'member_id' => $loggedAccountID,
    //         'txnid' => $txnid,
    //         'api_url' => $httpUrl,
    //         'post_data' => $plainText,
    //         'response' => $response,
    //         'created' => date('Y-m-d H:i:s')
    //     );
    //     $this->db->insert('upi_api_response',$apiData);

    //     $finalResponse = json_decode($response,true);
    //     if(isset($finalResponse['message']) && $finalResponse['message'] == 'Transaction initiated')
    //     {
    //         // save transaction data
    //         $txnData = array(
    //             'account_id' => $account_id,
    //             'member_id' => $loggedAccountID,
    //             'txnid' => $txnid,
    //             'amount' => $post['amount'],
    //             'qr_image' => $finalResponse['data'],
    //             'status' => 1,
    //             'created' => date('Y-m-d H:i:s'),
    //             'created_by' => $loggedAccountID
    //         );
    //         $this->db->insert('upi_dynamic_qr',$txnData);
    //         $record_id = $this->db->insert_id();
    //         return array(
    //             'status' => 1,
    //             'message' => $finalResponse['message'],
    //             'qr' => $finalResponse['data'],
    //             'record_id' => $record_id
    //         );
    //     }
    //     else
    //     {
    //         return array(
    //             'status' => 0,
    //             'message' => $finalResponse['message']
    //         );
    //     }

    // } 
    
    
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
                'qr' =>  $qr_image,
                'record_id' =>$record_id
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
                'qr' =>  $intent,
                'record_id' =>$record_id
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
                'qr' => $finalResponse['data'][0],
                'record_id' => $record_id,
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
                'qr' => $finalResponse['data'],
                'record_id' => $record_id,
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
            $record_id  = $this->db->insert_id();
            return array(
                'status' => 1,
                'message' => $finalResponse['message'],
                'qr' => $finalResponse['data'][0],
                'record_id' => $record_id,
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
        /*$decodeResult = json_decode($result);
        $decryptData = $this->decrypt($decodeResult, $key);

        $private_key = $accountData['upi_private_certificate'];

        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($decryptData,$response,$private_key);
        $response = json_decode($response);

        
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

        $finalResponse = json_decode($response,true);*/
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
        /*$decodeResult = json_decode($result);
        $decryptData = $this->decrypt($decodeResult, $key);

        $private_key = $accountData['upi_cash_private_certificate'];

        $private_key = openssl_get_privatekey($private_key, "");
        openssl_private_decrypt($decryptData,$response,$private_key);
        $response = json_decode($response);

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

        $finalResponse = json_decode($response,true);*/
        return array(
            'status' => 1,
            'message' => isset($finalResponse['message']) ? $finalResponse['message'] : ''
        );

    } 
    
    
    public function upiAddMoney($account_id,$loggedAccountID,$post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        
        //get member wallet_balance
        $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $txnid = time().rand(1111,9999);
        
        //$intent_url = 'upi://pay?pa=payol@yesb&pn=Payol%20Digital&mc=1520&tr='.$txnid.'&tn=Testing&am='.$post['amount'].'&mam='.$post['amount'].'&cu=INR';
        $intent_url ='upi://pay?pa=payol@yesb&pn=MATRE&tr='.$txnid.'&tn=PAY%20TO%20FSS&am='.$post['amount'].'&cu=INR&mam=1';

            // save transaction data
            $txnData = array(
                'account_id' => $account_id,
                'member_id' => $loggedAccountID,
                'txnid' => $txnid,
                'amount' => $post['amount'],
                'qr_image' => $intent_url,
                'status' => 1,
                'created' => date('Y-m-d H:i:s'),
                'created_by' => $loggedAccountID
            );
            $this->db->insert('upi_dynamic_qr',$txnData);
            $record_id = $this->db->insert_id();
            if($record_id)
            {
             return array(
                'status' => 1,
                'message' => 'QR Generate Successfully',
                'qr' => $intent_url,
                'record_id' => $record_id
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
    
    
    
    public function upiMoneySendRequest($account_id,$loggedAccountID,$post,$vpa_id,$vpa_name)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        //get member wallet_balance
        $memberData = $this->db->select('mobile,name')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $txnid = time().rand(1111,9999);
        $enckey = '0eecc43f46ac1db51c40607cb355b22c';


        //Pay API
        $header = [
            'Content-type: application/json',
            'X-IBM-Client-Secret: D8cI6vS0yQ0cQ6eG5xG4rU3eE1vX7uT1sY0dE4kO3dB8xX0pC3',
            'X-IBM-Client-ID: ccfcbdd3-8762-4329-9975-39acf5c8df50'
        ];
        
        $requestStr = 'YES0000000065149|'.$txnid.'|test|'.$post['amount'].'|INR|P2P|Pay|1520||||||'.$vpa_id.'|||||UPI|'.$vpa_name.'||||||VPA|||||||||NA|NA';
        $encryptData = $this->User->yesEncryptValue($requestStr,$enckey);
        
        
        
        $data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000000065149"}';
        
        
        $api_url = 'https://uatskyway.yesbank.in/app/uat/upi/mePayServerReqImps';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        curl_close ($ch);
        
        $finalResponse = $this->User->yesDecryptValue($response,$enckey);

        $finalResponseData = explode('|', $finalResponse);
        
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
            'api_url' => $api_url,
            'post_data' => json_encode($requestStr,true),
            'response' => json_encode($finalResponse,true),
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('upi_api_response',$apiData);

        
        if(isset($finalResponseData[5]) && $finalResponseData[5] == 'Transaction success')
        {
            $PayerAmount= $finalResponseData[2];
            $bank_rrno = $finalResponseData[10];
            $this->db->where('id',$record_id);
            $this->db->update('upi_transaction',array('status'=>2,'bank_rrno'=>$finalResponseData[10]));

                $before_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID);
                                
                    $after_balance = $before_balance - $PayerAmount;  

                    log_message('debug', 'UPI Callback Member Before Wallet Balance - '.$before_balance);  

                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $loggedAccountID,    
                        'before_balance'      => $before_balance,
                        'amount'              => $PayerAmount,  
                        'after_balance'       => $after_balance,      
                        'status'              => 1,
                        'type'                => 2,      
                        'wallet_type'         => 1,
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'UPI Request #'.$bank_rrno.' Amount Credited.'
                    );

                    $this->db->insert('member_wallet',$wallet_data);

            return array(
                'status' => 1,
                'message' => $finalResponseData[5],
                //'merchantTranId' => $finalResponse['data']['merchantTranId'],
                'BankRRN' => $finalResponseData[10]
            );
        }
        else
        {
            $this->db->where('id',$record_id);
            $this->db->update('upi_transaction',array('status'=>3));
            return array(
                'status' => 0,
                'message' => $finalResponseData[5]
            );
        }

    }
    
    
    public function upiRequestCheckStatus($account_id,$loggedAccountID,$txnid)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        
        //$txnid = time().rand(1111,9999);
        

        	//call status check api
				
		$enckey = '0eecc43f46ac1db51c40607cb355b22c';
		//Status Check API
		$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: D8cI6vS0yQ0cQ6eG5xG4rU3eE1vX7uT1sY0dE4kO3dB8xX0pC3',
		    'X-IBM-Client-ID: ccfcbdd3-8762-4329-9975-39acf5c8df50'
		];
	    
		$requestStr = 'YES0000000065149|'.$txnid.'||||||||||||NA|NA';
		$encryptData = $this->User->yesEncryptValue($requestStr,$enckey);
		$data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000000065149"}';
		
		$api_url = 'https://uatskyway.yesbank.in/app/uat/upi/meTransStatusQuery';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close ($ch);
        
        $finalResponse = $this->User->yesDecryptValue($response,$enckey);
        
        $finalResponseData = explode('|', $finalResponse);
        
        // save upi api response
        $apiData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'txnid' => $txnid,
            'api_url' => $api_url,
            'post_data' => json_encode($requestStr,true),
            'response' => json_encode($finalResponse,true),
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('upi_api_response',$apiData);

        
        if(isset($finalResponseData[4]) && $finalResponseData[4] == 'SUCCESS')
        {
            
            $this->db->where('txnid',$txnid);
            $this->db->update('upi_transaction',array('account_id'=>$account_id,'status'=>2,'bank_rrno'=>$finalResponseData[11]));
    
            return array(
                'status' => 1,
                'message' => $finalResponseData[5],
                
            );
        }
        elseif(isset($finalResponseData[4]) && $finalResponseData[4] == 'PENDING')
        {
            
            
    
            return array(
                'status' => 1,
                'message' => $finalResponseData[11],
                
            );
        }
       
        
        else
        {
            
            return array(
                'status' => 0,
                'message' => 'Transcation Failed'
            );
        }

    }
    


}


/* end of file: az.php */
/* Location: ./application/models/az.php */