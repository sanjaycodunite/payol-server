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

    public function checkAepsStatusLive($memberID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        
        $memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $member_code = $memberData['user_code'];
        $api_url = AEPS_EKYC_STATUS_CHECK_API_URL;

        $postdata = array 
        (
            "merchantLoginId"=>$member_code,
            "superMerchantId"=>$accountData['aeps_supermerchant_id']
        );

        // Generate JSON
        $json = json_encode($postdata);

        $hash_string = $json.'10748ce3f36701228278836f842567100dd514c476806e68d20e4ae310aeac27'. date('d/m/Y H:i:s');


        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'trnTimestamp: ' . date('d/m/Y H:i:s'),
            'hash: ' . base64_encode(hash('sha256', $hash_string, true))
        );

        // Initialize
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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

        // Set Options - Close

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
            'post_data' => json_encode($postdata),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('aeps_api_response',$apiData);

        if(isset($responseData['message']) && $responseData['message'] == "Ekyc Done Successfully")
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function activeAEPSMember($post,$aadhar_photo,$pancard_photo,$memberID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        //get latitute longtitude
        $googleResponse = $this->User->get_lat_lon($post['pin_code']);
        $lat = isset($googleResponse['lat']) ? $googleResponse['lat'] : '';
        $lng = isset($googleResponse['lng']) ? $googleResponse['lng'] : '';

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
            'lat' => $lat,
            'lng' => $lng,
            'created'             => date('Y-m-d H:i:s'),      
            'created_by'         => $memberID
        );

        $this->db->insert('aeps_member_kyc',$wallet_data);
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

        $api_url = AEPS_ONBOARD_API_URL;
        
        $postdata = array 
        (
            "username"=>$accountData['aeps_username'],
            "password"=>$accountData['aeps_password'],
            "latitude"=>$lat,
            "longitude"=>$lng,
            "supermerchantId"=>$accountData['aeps_supermerchant_id'],    
            "merchants"=>array(array
            (
                "merchantLoginId"=>$member_code, 
                "merchantLoginPin"=>$member_pin,
                "merchantName"=>$post['first_name'].' '.$post['last_name'],
                "merchantAddress"=>array
                (
                    "merchantAddress"=>$post['address'],
                    "merchantState"=>$post['state_id']
                ),        
                "merchantPhoneNumber"=>$post['mobile'],
                "companyLegalName"=>$post['shop_name'],
                "companyMarketingName"=>"",
                "kyc"=>array
                (
                    "userPan"=>$post['pancard_no'],
                    "aadhaarNumber"=>$post['aadhar_no'],
                    "gstInNumber"=>"",
                    "companyOrShopPan"=>""
                ),
                "settlement"=>array
                (
                        "companyBankAccountNumber"=>"",
                        "bankIfscCode"=>"",
                        "companyBankName"=>"",
                        "bankBranchName"=>"",
                        "bankAccountName"=>""
                ),
                "emailId"=>$member_email,
                "shopAndPanImage"=>"",        
                "cancellationCheckImages"=>"",        
                "ekycDocuments"=>"",
                "merchantPinCode"=>$post['pin_code'],
                "tan"=>"",
                "merchantCityName"=>$city_name,
                "merchantDistrictName"=>$city_name,
            ))
        );

        // Generate JSON
        $json = json_encode($postdata);

        // Generate Session Key
        $key = '';
        $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        foreach ($mt_rand as $chr)
        {             $key .= chr($chr);         }

        // Read Public Key
        $pub_key_string = $accountData['aeps_certificate'];
        
        // Encrypt using Public Key
        openssl_public_encrypt($key, $crypttext, $pub_key_string);

        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'trnTimestamp: ' . date('d/m/Y H:i:s'),
            'hash: ' . base64_encode(hash('sha256', $json, true)),
            'eskey: ' . base64_encode($crypttext)
        );

        // Initialization Vector
        $iv =   '06f2f04cc530364f';

        // Encrypt using AES-128
        $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

        // Create Body
        $request = base64_encode($ciphertext_raw);

        // Initialize
        $curl = curl_init();

        //Set Options - Open

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        // Set Options - Close

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$output = '{"status":true,"message":"successful","data":{"username":"cogentd","password":"796c3ee556ac31f3754a38cfd15b8044","ipAddress":null,"latitude":22.9734229,"longitude":78.6568942,"supermerchantId":781,"timestamp":null,"merchants":[{"merchantLoginId":"PAMD826031","merchantLoginPin":"1234","merchantName":"Sonu Jangeed","merchantAddress":{"merchantAddress":"Samode, Jaipur","merchantState":"22"},"merchantBranch":null,"merchantPhoneNumber":"8104758957","companyLegalName":"","companyMarketingName":"","kyc":{"userPan":"AWRPJ3682J","aadhaarNumber":"385347960626","gstinNumber":null,"companyOrShopPan":""},"settlement":{"companyBankAccountNumber":"","bankIfscCode":"","companyBankName":"","bankBranchName":"","bankAccountName":""},"emailId":"sonujangid2011@gmail.com","shopAndPanImage":"","cancellationCheckImages":"","ekycDocuments":"","merchantPinCode":"303806","tan":"","supermerchantId":0,"activeFlag":0,"status":"Successfully Created,Please do EKYC to do success Transactions","remarks":null,"flag":null,"merchantId":2353953,"merchantCityName":"Jaipur","merchantDistrictName":"Jaipur"}]},"statusCode":0}';*/

        $responseData = json_decode($output,true);

        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $memberID,
            'api_url' => $api_url,
            'api_response' => $output,
            'post_data' => json_encode($postdata),
            'is_from_app' => 1,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('aeps_api_response',$apiData);
        if(isset($responseData['status']) && $responseData['status'] == true)
        {
            // update aeps status
            $this->db->where('id',$recordID);
            $this->db->update('aeps_member_kyc',array('clear_step'=>1));

               // send OTP API

                $otp_api_url = AEPS_EKYC_SEND_OTP_API_URL;

                $otpPostData = array 
            (
                "superMerchantId"=>$accountData['aeps_supermerchant_id'],    
                "merchantLoginId" => $member_code, 
                "transactionType" => "EKY",
                "mobileNumber" => $post['mobile'],
                "aadharNumber" => $post['aadhar_no'],
                "panNumber" => $post['pancard_no'],
                "matmSerialNumber" => "",
                "latitude"=>$lat,
                "longitude"=>$lng,
            );

            // Generate JSON
            $json = json_encode($otpPostData);

            // Generate Session Key
            $key = '';
            $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
            foreach ($mt_rand as $chr)
            {             $key .= chr($chr);         }

            // Read Public Key
            $pub_key_string = $accountData['aeps_certificate'];

            // Encrypt using Public Key
            openssl_public_encrypt($key, $crypttext, $pub_key_string);

            // Create Header
            $header = array
            (
                'Content-type: application/json',
                'trnTimestamp: ' . date('d/m/Y H:i:s'),
                'hash: ' . base64_encode(hash('sha256', $json, true)),
                'eskey: ' . base64_encode($crypttext),
                'deviceIMEI:352801082418919'
            );

            // Initialization Vector
            $iv =   '06f2f04cc530364f';

            // Encrypt using AES-128
            $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

            // Create Body
            $request = base64_encode($ciphertext_raw);

            // Initialize
            $curl = curl_init();

            //Set Options - Open

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

            // Request Header
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

            // Request Body
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

            // Set Options - Close

            // Execute
            $otp_output = curl_exec($curl);

            // Close
            curl_close ($curl);

            /*$otp_output = '{"status":true,"message":"Request Completed","data":{"primaryKeyId":649432,"encodeFPTxnId":"EKYKF2353953011121143457030I"},"statusCode":10000}';*/

            $otpResponseData = json_decode($otp_output,true);

                $apiData = array(
                    'account_id' => $account_id,
                    'user_id' => $memberID,
                    'api_url' => $otp_api_url,
                    'api_response' => $otp_output,
                    'post_data' => json_encode($otpPostData),
                    'is_from_app' => 1,
                    'created' => date('Y-m-d H:i:s'),
                    'created_by' => 1
                );
                $this->db->insert('aeps_api_response',$apiData);
                if(isset($otpResponseData['message']) && $otpResponseData['message'] == "Request Completed")
                {
                    $primaryKeyId = isset($otpResponseData['data']['primaryKeyId']) ? $otpResponseData['data']['primaryKeyId'] : '';
                    $encodeFPTxnId = isset($otpResponseData['data']['encodeFPTxnId']) ? $otpResponseData['data']['encodeFPTxnId'] : '';
                    // update aeps status
                    $this->db->where('id',$recordID);
                    $this->db->update('aeps_member_kyc',array('clear_step'=>2,'primaryKeyId'=>$primaryKeyId,'encodeFPTxnId'=>$encodeFPTxnId));
                    return array('status'=>1,'msg'=>'success','primaryKeyId'=>$primaryKeyId,'encodeFPTxnId'=>$encodeFPTxnId);
                }
                else
                {
                    return array('status'=>0,'msg'=>$otpResponseData['message']);
                }
            
        }
        else
        {
            return array('status'=>0,'msg'=>$responseData['message']);
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

        $otpPostData = array 
        (
            "superMerchantId"=>$accountData['aeps_supermerchant_id'],    
            "merchantLoginId" => $member_code, 
            "primaryKeyId" => $primaryKeyId,
            "encodeFPTxnId" => $encodeFPTxnId,
        );

        // Generate JSON
        $json = json_encode($otpPostData);

        // Generate Session Key
        $key = '';
        $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        foreach ($mt_rand as $chr)
        {             $key .= chr($chr);         }

        // Read Public Key
        $pub_key_string = $accountData['aeps_certificate'];

        // Encrypt using Public Key
        openssl_public_encrypt($key, $crypttext, $pub_key_string);

        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'trnTimestamp: ' . date('d/m/Y H:i:s'),
            'hash: ' . base64_encode(hash('sha256', $json, true)),
            'eskey: ' . base64_encode($crypttext),
            'deviceIMEI:352801082418919'
        );

        // Initialization Vector
        $iv =   '06f2f04cc530364f';

        // Encrypt using AES-128
        $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

        // Create Body
        $request = base64_encode($ciphertext_raw);

        // Initialize
        $curl = curl_init();

        //Set Options - Open

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        // Set Options - Close

        // Execute
        $otp_output = curl_exec($curl);

        // Close
        curl_close ($curl);

        $otpResponseData = json_decode($otp_output,true);

        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $memberID,
            'api_url' => $otp_api_url,
            'api_response' => $otp_output,
            'post_data' => json_encode($otpPostData),
            'is_from_app' => 1,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('aeps_api_response',$apiData);
        if(isset($otpResponseData['message']) && $otpResponseData['message'] == 'Request Completed')
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
            return array('status'=>0,'msg'=>$otpResponseData['message']);
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

        $otpPostData = array 
        (
            "superMerchantId"=>$accountData['aeps_supermerchant_id'],    
            "merchantLoginId" => $member_code, 
            "otp" => $otp_code,
            "primaryKeyId" => $primaryKeyId,
            "encodeFPTxnId" => $encodeFPTxnId,
        );

        // Generate JSON
        $json = json_encode($otpPostData);

        // Generate Session Key
        $key = '';
        $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        foreach ($mt_rand as $chr)
        {             $key .= chr($chr);         }

        // Read Public Key
        $pub_key_string = $accountData['aeps_certificate'];

        // Encrypt using Public Key
        openssl_public_encrypt($key, $crypttext, $pub_key_string);

        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'trnTimestamp: ' . date('d/m/Y H:i:s'),
            'hash: ' . base64_encode(hash('sha256', $json, true)),
            'eskey: ' . base64_encode($crypttext),
            'deviceIMEI:352801082418919'
        );

        // Initialization Vector
        $iv =   '06f2f04cc530364f';

        // Encrypt using AES-128
        $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

        // Create Body
        $request = base64_encode($ciphertext_raw);

        // Initialize
        $curl = curl_init();

        //Set Options - Open

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        // Set Options - Close

        // Execute
        $otp_output = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$otp_output = '{"status":true,"message":"Request Completed","data":{"primaryKeyId":649432,"encodeFPTxnId":"EKYKF2353953011121143457030I"},"statusCode":10000}';*/

        $otpResponseData = json_decode($otp_output,true);


        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $memberID,
            'api_url' => $otp_api_url,
            'api_response' => $otp_output,
            'post_data' => json_encode($otpPostData),
            'is_from_app' => 1,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('aeps_api_response',$apiData);
        if(isset($otpResponseData['message']) && $otpResponseData['message'] == 'Request Completed')
        {
            /*$primaryKeyId = isset($otpResponseData['Data'][0]['primaryKeyId']) ? $otpResponseData['Data'][0]['primaryKeyId'] : '';
            $encodeFPTxnId = isset($otpResponseData['Data'][0]['encodeFPTxnId']) ? $otpResponseData['Data'][0]['encodeFPTxnId'] : '';*/
            // update aeps status
            $this->db->where('id',$recordID);
            $this->db->update('aeps_member_kyc',array('clear_step'=>3));
            return array('status'=>1,'msg'=>'success');
        }
        else
        {
            return array('status'=>0,'msg'=>$otpResponseData['message']);
        }
    }

    public function addBalance($txnID,$aadharNumber,$iin,$amount,$memberID,$recordID,$serviceType = '')
    {       
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        
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

        if($loggedUser['role_id'] == 4)
        {
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - AEPS - Distribute Commision/Surcharge Start]'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $this->User->distribute_aeps_commision($recordID,$txnID,$amount,$memberID,$com_amount,$is_surcharge,$com_type,'DT',$loggedUser['user_code']);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - AEPS - Distribute Commision/Surcharge End]'.PHP_EOL;
            $this->User->generateLog($log_msg);
        }
        elseif($loggedUser['role_id'] == 5)
        {
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - AEPS - Distribute Commision/Surcharge Start]'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $this->User->distribute_aeps_commision($recordID,$txnID,$amount,$memberID,$com_amount,$is_surcharge,$com_type,'RT',$loggedUser['user_code']);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - AEPS - Distribute Commision/Surcharge End]'.PHP_EOL;
            $this->User->generateLog($log_msg);
        }
        
        return true;
    }

    public function addStatementCom($txnID,$aadharNumber,$iin,$amount,$memberID,$recordID)
    {       
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        
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

        if($loggedUser['role_id'] == 4)
        {
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - AEPS - Distribute Commision/Surcharge Start]'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $this->User->distribute_aeps_commision($recordID,$txnID,$amount,$memberID,$com_amount,$is_surcharge,2,'DT',$loggedUser['user_code']);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - DT('.$loggedUser['user_code'].') - AEPS - Distribute Commision/Surcharge End]'.PHP_EOL;
            $this->User->generateLog($log_msg);
        }
        elseif($loggedUser['role_id'] == 5)
        {
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - AEPS - Distribute Commision/Surcharge Start]'.PHP_EOL;
            $this->User->generateLog($log_msg);

            $this->User->distribute_aeps_commision($recordID,$txnID,$amount,$memberID,$com_amount,$is_surcharge,2,'RT',$loggedUser['user_code']);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - AEPS - Distribute Commision/Surcharge End]'.PHP_EOL;
            $this->User->generateLog($log_msg);
        }
        
        
        return true;
    }

   

    public function saveAepsTxn($txnID,$service,$aadhar_no,$mobile,$amount,$iinno,$api_url,$api_response,$message,$status,$memberID)
    {
        $account_id = $this->User->get_domain_account();
        
        $receipt_id = rand(111111,999999);
        $txnData = array(
            'account_id' => $account_id,
            'receipt_id' => $receipt_id,
            'member_id' => $memberID,
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
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $memberID
        );
        $this->db->insert('member_aeps_transaction',$txnData);
        $recordID = $this->db->insert_id();
        return $recordID;
    }
    
    public function sendCashDepositeOtp($post,$memberID)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $member_code = $memberData['user_code'];
        $member_pin = $memberData['decoded_transaction_password'];
        $lat = $memberData['aeps_lat'];
        $lng = $memberData['aeps_lng'];

        $commisionData = $this->User->get_aeps_commission($post['amount'],$memberID,4);
        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

        $total_wallet_charge = $post['amount'];
        if($is_surcharge)
        {
            $total_wallet_charge = $post['amount'] + $com_amount;
        }

        $adminCommisionData = $this->User->get_admin_aeps_commission($post['amount'],$account_id,4);
        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;
        $admin_total_wallet_charge = $post['amount'];
        if($admin_is_surcharge)
        {
            $admin_total_wallet_charge = $post['amount'] + $admin_com_amount;
        }
        // send OTP API

        $api_url = CASH_DEPOSITE_SEND_OTP;

        $txnID = time().rand(1111,9999);

        $txnData = array(
            'account_id' => $account_id,
            'member_id' => $memberID,
            'mobile' => $post['mobile'],
            'account_no' => $post['account_no'],
            'amount' => $post['amount'],
            'commission' => $com_amount,
            'is_surcharge' => $is_surcharge,
            'total_wallet_charge' => $total_wallet_charge,
            'admin_commission' => $admin_com_amount,
            'admin_is_surcharge' => $admin_is_surcharge,
            'admin_total_wallet_charge' => $admin_total_wallet_charge,
            'remark' => $post['remark'],
            'txnid' => $txnID,
            'status' => 1,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $memberID
        );
        $this->db->insert('cash_deposite_history',$txnData);
        $recordID = $this->db->insert_id();

        $data = array 
        (
            "superMerchantId" => $accountData['aeps_supermerchant_id'],
            "merchantUserName" => $member_code,
            "merchantPin" => md5($member_pin),
            "subMerchantId" => "",
            "secretKey" => $accountData['aeps_secret_key'],
            "mobileNumber" => $post['mobile'],
            "iin" => "508534",
            "transactionType" => "CDO",
            "latitude"=>$lat,
            "longitude"=>$lng,
            "requestRemarks" => $post['remark'],
            "merchantTranId"=>$txnID,    
            "accountNumber" => $post['account_no'],
            "amount" => $post['amount'],
            "fingpayTransactionId" => "",
            "otp" => "",
            "cdPkId" => "0",
            "paymentType" => "B"
        );

        // Generate JSON
        $json = json_encode($data);
        

        // Generate Session Key
        $key = '';
        $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        foreach ($mt_rand as $chr)
        {             $key .= chr($chr);         }

        // Read Public Key
        $pub_key_string = $accountData['aeps_certificate'];

        // Encrypt using Public Key
        openssl_public_encrypt($key, $crypttext, $pub_key_string);

        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'trnTimestamp: ' . date('d/m/Y H:i:s'),
            'hash: ' . base64_encode(hash('sha256', $json.$accountData['aeps_secret_key'], true)),
            'eskey: ' . base64_encode($crypttext),
            'deviceIMEI:352801082418919'
        );

        // Initialization Vector
        $iv =   '06f2f04cc530364f';

        // Encrypt using AES-128
        $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

        // Create Body
        $request = base64_encode($ciphertext_raw);

        // Initialize
        $curl = curl_init();

        //Set Options - Open

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        // Set Options - Close

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$output = '{"status":true,"message":"Message successfully sent to the entered mobile number.","data":{"fingpayTransactionId":"CDOBT2353953301121173243658I","cdPkId":1046688,"bankRrn":"133417386274","fpRrn":"133417846688","stan":"846688","merchantTranId":"'.$txnID.'","responseCode":"00","responseMessage":"Message successfully sent to the entered mobile number.","accountNumber":"023501546776","mobileNumber":"8104758957","beneficiaryName":null,"transactionTimestamp":"Tue Nov 30 17:32:44 IST 2021"},"statusCode":10000}';*/

        $responseData = json_decode($output,true);

        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $memberID,
            'txnid' => $txnID,
            'api_url' => $api_url,
            'api_response' => $output,
            'post_data' => $json,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('cash_deposite_api_response',$apiData);
        if(isset($responseData['status']) && $responseData['status'] == true)
        {
            // update data
            $this->db->where('id',$recordID);
            $this->db->update('cash_deposite_history',array('fingpay_txnid'=>$responseData['data']['fingpayTransactionId'],'cdpkid'=>$responseData['data']['cdPkId'],'bank_rrn'=>$responseData['data']['bankRrn'],'fp_rrn'=>$responseData['data']['fpRrn']));
            return array('status'=>1,'msg'=>$responseData['message'],'txnID'=>$txnID);
        }
        else
        {
            return array('status'=>0,'msg'=>$responseData['message']);
        }
    }

    public function verifyCashDepositeOtp($post,$memberID)
    {
        $account_id = $this->User->get_domain_account();
        $admin_id = $this->User->get_admin_id($account_id);
        $accountData = $this->User->get_account_data($account_id);
        $memberData = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $member_code = $memberData['user_code'];
        $member_pin = $memberData['decoded_transaction_password'];
        $lat = $memberData['aeps_lat'];
        $lng = $memberData['aeps_lng'];
        $txnid = $post['txnid'];
        $txnData = $this->db->get_where('cash_deposite_history',array('account_id'=>$account_id,'member_id'=>$memberID,'txnid'=>$txnid,'status'=>1))->row_array();
        $fingpay_txnid = $txnData['fingpay_txnid'];
        $cdpkid = $txnData['cdpkid'];
        $recordID = $txnData['id'];
        $mobile = $txnData['mobile'];
        $remark = $txnData['remark'];
        $account_no = $txnData['account_no'];
        $amount = $txnData['amount'];
        $is_surcharge = $txnData['is_surcharge'];
        $commission = $txnData['commission'];
        $total_wallet_charge = $txnData['total_wallet_charge'];
        $admin_is_surcharge = $txnData['admin_is_surcharge'];
        $admin_commission = $txnData['admin_commission'];
        $admin_total_wallet_charge = $txnData['admin_total_wallet_charge'];
        // send OTP API

        $api_url = CASH_DEPOSITE_OTP_AUTH;

        $data = array 
        (
            "superMerchantId" => $accountData['aeps_supermerchant_id'],
            "merchantUserName" => $member_code,
            "merchantPin" => md5($member_pin),
            "subMerchantId" => "",
            "secretKey" => $accountData['aeps_secret_key'],
            "mobileNumber" => $mobile,
            "iin" => "508534",
            "transactionType" => "CDO",
            "latitude"=>$lat,
            "longitude"=>$lng,
            "requestRemarks" => $remark,
            "merchantTranId"=>$txnid,    
            "accountNumber" => $account_no,
            "amount" => $amount,
            "fingpayTransactionId" => $fingpay_txnid,
            "otp" => $post['otp_code'],
            "cdPkId" => $cdpkid,
            "paymentType" => "B"
        );

        // Generate JSON
        $json = json_encode($data);
        

        // Generate Session Key
        $key = '';
        $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        foreach ($mt_rand as $chr)
        {             $key .= chr($chr);         }

        // Read Public Key
        $pub_key_string = $accountData['aeps_certificate'];

        // Encrypt using Public Key
        openssl_public_encrypt($key, $crypttext, $pub_key_string);

        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'trnTimestamp: ' . date('d/m/Y H:i:s'),
            'hash: ' . base64_encode(hash('sha256', $json.$accountData['aeps_secret_key'], true)),
            'eskey: ' . base64_encode($crypttext),
            'deviceIMEI:352801082418919'
        );

        // Initialization Vector
        $iv =   '06f2f04cc530364f';

        // Encrypt using AES-128
        $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

        // Create Body
        $request = base64_encode($ciphertext_raw);

        // Initialize
        $curl = curl_init();

        //Set Options - Open

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

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        // Set Options - Close

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$output = '{"status":true,"message":"Request Completed","data":{"fingpayTransactionId":"CDOBT2353953301121174934403I","cdPkId":1046742,"bankRrn":"133417407618","fpRrn":"133417846742","stan":"846742","merchantTranId":"16382820358246","responseCode":"00","responseMessage":"Beneficiary data fetched successfully.","accountNumber":"023501546776","mobileNumber":"8104758957","beneficiaryName":"SONU JANGID","transactionTimestamp":"Tue Nov 30 17:50:34 IST 2021"},"statusCode":10000}';*/

        $responseData = json_decode($output,true);

        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $memberID,
            'txnid' => $txnid,
            'api_url' => $api_url,
            'api_response' => $output,
            'post_data' => $json,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('cash_deposite_api_response',$apiData);
        if(isset($responseData['status']) && $responseData['status'] == true)
        {
            // update data
            $this->db->where('id',$recordID);
            $this->db->update('cash_deposite_history',array('status'=>2,'otp_verify'=>1));

            // Deduct member wallet
            //get member wallet_balance
            
            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

            $after_wallet_balance = $before_wallet_balance - $total_wallet_charge;

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $memberID,    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $total_wallet_charge,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'         => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'Cash Deposite Txn #'.$txnid.' Amount Debited.'
            );

            $this->db->insert('member_wallet',$wallet_data);


            // Deduct admin wallet
            //get member wallet_balance
            
            $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

            $after_wallet_balance = $before_wallet_balance - $admin_total_wallet_charge;

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $admin_id,    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $admin_total_wallet_charge,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'         => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'Cash Deposite Txn #'.$txnid.' Amount Debited.'
            );

            $this->db->insert('collection_wallet',$wallet_data);

           

            // MAKE TXN
            $api_url = CASH_DEPOSITE_TXN_API;

            $data = array 
            (
                "superMerchantId" => $accountData['aeps_supermerchant_id'],
                "merchantUserName" => $member_code,
                "merchantPin" => md5($member_pin),
                "subMerchantId" => "",
                "secretKey" => $accountData['aeps_secret_key'],
                "mobileNumber" => $mobile,
                "iin" => "508534",
                "transactionType" => "CDO",
                "latitude"=>$lat,
                "longitude"=>$lng,
                "requestRemarks" => $remark,
                "merchantTranId"=>$txnid,    
                "accountNumber" => $account_no,
                "amount" => $amount,
                "fingpayTransactionId" => $fingpay_txnid,
                "otp" => $post['otp_code'],
                "cdPkId" => $cdpkid,
                "paymentType" => "B"
            );

            // Generate JSON
            $json = json_encode($data);
            

            // Generate Session Key
            $key = '';
            $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
            foreach ($mt_rand as $chr)
            {             $key .= chr($chr);         }

            // Read Public Key
            $pub_key_string = $accountData['aeps_certificate'];

            // Encrypt using Public Key
            openssl_public_encrypt($key, $crypttext, $pub_key_string);

            // Create Header
            $header = array
            (
                'Content-type: application/json',
                'trnTimestamp: ' . date('d/m/Y H:i:s'),
                'hash: ' . base64_encode(hash('sha256', $json.$accountData['aeps_secret_key'], true)),
                'eskey: ' . base64_encode($crypttext),
                'deviceIMEI:352801082418919'
            );

            // Initialization Vector
            $iv =   '06f2f04cc530364f';

            // Encrypt using AES-128
            $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

            // Create Body
            $request = base64_encode($ciphertext_raw);

            // Initialize
            $curl = curl_init();

            //Set Options - Open

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

            // Request Header
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

            // Request Body
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

            // Set Options - Close

            // Execute
            $output = curl_exec($curl);

            // Close
            curl_close ($curl);

            /*$output = '{
    "status": true,
    "message": "Request Completed",
    "data": {
        "fingpayTransactionId": "CDOBA0000001070720224315449A",
        "cdPkId": 6391,
        "bankRrn": "018922375143",
        "fpRrn": "018922806391",
        "stan": "806391",
        "merchantTranId": "124411588",
        "responseCode": "00",
        "responseMessage": "Transaction successfully completed.",
        "accountNumber": "100501512871",
        "mobileNumber": "9560620395",
        "beneficiaryName": null,
        "transactionTimestamp": "Tue Jul 07 22:43:40 IST 2020"
    },
    "statusCode": 10000
}';*/

            $responseData = json_decode($output,true);

            $apiData = array(
                'account_id' => $account_id,
                'user_id' => $memberID,
                'txnid' => $txnid,
                'api_url' => $api_url,
                'api_response' => $output,
                'post_data' => $json,
                'created' => date('Y-m-d H:i:s'),
                'created_by' => 1
            );
            $this->db->insert('cash_deposite_api_response',$apiData);
            if(isset($responseData['message']) && $responseData['message'] == 'Request Completed')
            {
                $this->db->where('id',$recordID);
                $this->db->update('cash_deposite_history',array('bank_rrn'=>$responseData['data']['bankRrn'],'fp_rrn'=>$responseData['data']['fpRrn'],'status'=>3));

                if(!$is_surcharge)
                {
                    // Add Member Commision
                    //get member wallet_balance
                    
                    $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

                    $after_wallet_balance = $before_wallet_balance + $commission;

                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $memberID,    
                        'before_balance'      => $before_wallet_balance,
                        'amount'              => $commission,  
                        'after_balance'       => $after_wallet_balance,      
                        'status'              => 1,
                        'type'                => 1,   
                        'wallet_type'         => 1,   
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'Cash Deposite Txn #'.$txnid.' Commision Credited.'
                    );

                    $this->db->insert('member_wallet',$wallet_data);

                }

                if(!$admin_is_surcharge)
                {
                    $commData = array(
                        'account_id' => $account_id,
                        'member_id' => $admin_id,
                        'type' => 4,
                        'txnID' => $txnid,
                        'amount' => $amount,
                        'com_amount' => $admin_commission,
                        'is_surcharge' => $admin_is_surcharge,
                        'wallet_settle_amount' => $admin_commission,
                        'is_paid' => 0,
                        'status' => 1,
                        'created'             => date('Y-m-d H:i:s'),      
                        'created_by'         => $memberID,
                    );
                    $this->db->insert('member_aeps_comm',$commData);
                }
                
                return array('status'=>1,'msg'=>$responseData['message']);
            }
            else
            {
                $this->db->where('id',$recordID);
                $this->db->update('cash_deposite_history',array('status'=>4));


                // Refund member wallet
                //get member wallet_balance
                $get_member_status = $this->db->select('wallet_balance')->get_where('users',array('id'=>$memberID))->row_array();
                $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

                $after_wallet_balance = $before_wallet_balance + $total_wallet_charge;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $total_wallet_charge,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'Cash Deposite Txn #'.$txnid.' Amount Refund Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
                // Refund admin wallet
                //get member wallet_balance
               
                $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

                $after_wallet_balance = $before_wallet_balance + $admin_total_wallet_charge;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $admin_id,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $admin_total_wallet_charge,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'Cash Deposite Txn #'.$txnid.' Amount Refund Credited.'
                );

                $this->db->insert('collection_wallet',$wallet_data);
                
                return array('status'=>0,'msg'=>$responseData['message']);
            }

        }
        else
        {
            return array('status'=>0,'msg'=>$responseData['message']);
        }
    }

}


/* end of file: az.php */
/* Location: ./application/models/az.php */