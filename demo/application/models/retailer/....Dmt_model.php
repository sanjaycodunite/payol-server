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
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $memberData = $this->db->select('user_code,name,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $token = do_hash(time().rand(1111,9999));

        // save activation data
        $data = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'name' => $post['name'],
            'user_code' => $memberData['user_code'],
            'mobile' => $post['mobile'],
            'pin_code' => $post['pin_code'],
            'token' => $token,
            'status' => 0,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $loggedAccountID
        );
        $this->db->insert('user_dmt_activation',$data);

        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Sender Register API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtServiceRequest>
<requestType>SenderRegister</requestType>
<senderMobileNumber>'.$post['mobile'].'</senderMobileNumber>
<txnType>IMPS</txnType>
<senderName>'.$post['name'].'</senderName>
<senderPin>'.$post['pin_code'].'</senderPin>
</dmtServiceRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Register Sender API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        $requestId = $this->generateRandomString();

        $data = array();

        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_FETCH_SENDER_DETAIL_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtServiceResponse>
    <additionalRegData>30da1eab-eefc-4c8d-864e-e417985f31fd</additionalRegData>
    <respDesc>OTP has been sent successfully</respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <senderMobileNumber>9289210048</senderMobileNumber>
</dmtServiceResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Register Sender API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        // 0 = Error
        // 1 = Success
        
        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            
            // update status
            $this->db->where('token',$token);
            $this->db->update('user_dmt_activation',array('stateresp'=>$responseData['additionalRegData']));
            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['respDesc']) ? $responseData['respDesc'] : '',
                'token' => $token,
                'stateresp' => isset($responseData['additionalRegData']) ? $responseData['additionalRegData'] : ''
            );
            
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['errorInfo']['error']['errorMessage']) ? $responseData['errorInfo']['error']['errorMessage'] : ''
            );
        }

        return $api_response;
    }

    public function memberActivationOtpAuth($post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $token = $post['token'];
        $memberData = $this->db->select('mobile,name,dob,address,pin_code,stateresp')->get_where('user_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'token'=>$token))->row_array();
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Sender Verify OTP API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtServiceRequest>
<requestType>VerifySender</requestType>
<senderMobileNumber>'.$memberData['mobile'].'</senderMobileNumber>
<txnType>IMPS</txnType>
<otp>'.$post['otp_code'].'</otp>
<additionalRegData>'.$memberData['stateresp'].'</additionalRegData>
</dmtServiceRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Sender Verify OTP API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        $requestId = $this->generateRandomString();

        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_FETCH_SENDER_DETAIL_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtServiceResponse>
    <respDesc>Customer Ajay Yadav has been updated</respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <senderMobileNumber>9289210048</senderMobileNumber>
</dmtServiceResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Sender Verify API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        // 0 = Error
        // 1 = Success
        
        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            
            // update status
            $this->db->where('token',$token);
            $this->db->update('user_dmt_activation',array('status'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedAccountID));

            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['respDesc']) ? $responseData['respDesc'] : '',
                'mobile' => isset($responseData['senderMobileNumber']) ? $responseData['senderMobileNumber'] : ''
            );
            
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['errorInfo']['error']['errorMessage']) ? $responseData['errorInfo']['error']['errorMessage'] : ''
            );
        }

        return $api_response;
    }

    public function memberActivationResendOtpAuth($token)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        
        $memberData = $this->db->select('mobile,name,dob,address,pin_code,stateresp')->get_where('user_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'token'=>$token))->row_array();
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Sender Resend OTP API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtServiceRequest>
<requestType>ResendSenderOtp</requestType>
<senderMobileNumber>'.$memberData['mobile'].'</senderMobileNumber>
<txnType>IMPS</txnType>
</dmtServiceRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Sender Resend OTP API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        $requestId = $this->generateRandomString();

        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_FETCH_SENDER_DETAIL_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtServiceResponse>
    <additionalRegData>30da1eab-eefc-4c8d-864e-e417985f31fd</additionalRegData>
    <respDesc>OTP has been sent successfully</respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <senderMobileNumber>9289210048</senderMobileNumber>
</dmtServiceResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Sender Resend OTP API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        // 0 = Error
        // 1 = Success
        
        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            
            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['respDesc']) ? $responseData['respDesc'] : '',
                'mobile' => isset($responseData['senderMobileNumber']) ? $responseData['senderMobileNumber'] : ''
            );
            
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['errorInfo']['error']['errorMessage']) ? $responseData['errorInfo']['error']['errorMessage'] : ''
            );
        }

        return $api_response;
    }

    public function verifyIfscCode($ifsc)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
         
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT IFSC Verify API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtServiceRequest>
<requestType>IfscDetails</requestType>
<ifsc>'.$ifsc.'</ifsc>
</dmtServiceRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT IFSC Verify API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        $requestId = $this->generateRandomString();

        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_FETCH_SENDER_DETAIL_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtServiceResponse>
    <address>NEAR JAIN TRADING CORPORATION</address>
    <bankName>PUNJAB NATIONAL BANK</bankName>
    <branchName>KUNKURI</branchName>
    <city>KUNKURI</city>
    <district>MAIN ROAD ,KUNKURI</district>
    <ifscDetails>PUNB0724600</ifscDetails>
    <respDesc>Success</respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <state>CHHATTISGARH</state>
</dmtServiceResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Sender Resend OTP API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        // 0 = Error
        // 1 = Success
        
        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            
            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['respDesc']) ? $responseData['respDesc'] : '',
                'address' => isset($responseData['address']) ? $responseData['address'] : '',
                'bankName' => isset($responseData['bankName']) ? $responseData['bankName'] : '',
                'branchName' => isset($responseData['branchName']) ? $responseData['branchName'] : '',
                'city' => isset($responseData['city']) ? $responseData['city'] : '',
                'district' => isset($responseData['district']) ? $responseData['district'] : '',
                'ifscDetails' => isset($responseData['ifscDetails']) ? $responseData['ifscDetails'] : '',
                'state' => isset($responseData['state']) ? $responseData['state'] : ''
            );
            
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['errorInfo']['error']['errorMessage']) ? $responseData['errorInfo']['error']['errorMessage'] : ''
            );
        }

        return $api_response;
    }

    public function memberFetchDetail($post)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Get Sender API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtServiceRequest>
        <requestType>SenderDetails</requestType>
        <senderMobileNumber>'.$post['mobile'].'</senderMobileNumber>
        <txnType>IMPS</txnType>
        </dmtServiceRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Get Sender API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        $requestId = $this->generateRandomString();

        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_FETCH_SENDER_DETAIL_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtServiceResponse>
    <additionalLimitAvailable>false</additionalLimitAvailable>
    <availableLimit>25000.0</availableLimit>
    <availableLimitBreakup>
        <amtValue>25000.0</amtValue>
        <amtValue>25000.0</amtValue>
    </availableLimitBreakup>
    <mobileVerified>true</mobileVerified>
    <respDesc>Success</respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <senderCity>Kunkuri</senderCity>
    <senderMobileNumber>9289210048</senderMobileNumber>
    <senderName>Ajay Yadav</senderName>
    <totalLimit>25000.0</totalLimit>
    <usedLimit>0.0</usedLimit>
</dmtServiceResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Get Sender API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        // 0 = Error
        // 1 = Success
        
        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            $memberData = $this->db->select('user_code,name,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();

            $token = do_hash(time().rand(1111,9999));

            // save activation data
            $data = array(
                'account_id' => $account_id,
                'member_id' => $loggedAccountID,
                'user_code' => $memberData['user_code'],
                'name' => $responseData['senderName'],
                'mobile' => $responseData['senderMobileNumber'],
                'address' => $responseData['senderCity'],
                'token' => $token,
                'status' => 1,
                'created' => date('Y-m-d H:i:s'),
                'created_by' => $loggedAccountID
            );
            $this->db->insert('user_dmt_activation',$data);

            $fetchBenResponse = $this->memberFetchBen($responseData['senderMobileNumber']);
            $benList = isset($fetchBenResponse['benList']) ? $fetchBenResponse['benList'] : array();
            if($benList)
            {
                foreach($benList as $blist)
                {
                    // get bank id
                    $get_bank_id = $this->db->query("SELECT * FROM tbl_dmt_bank_list WHERE title LIKE '".$blist['bankName']."'")->row_array();
                    $bank_id = isset($get_bank_id['id']) ? $get_bank_id['id'] : 0 ;

                    $is_verify = 0;
                    if($blist['isVerified'] == 'Y')
                        $is_verify = 1;
                    // save activation data
                    $benData = array(
                        'account_id' => $account_id,
                        'member_id' => $loggedAccountID,
                        'register_mobile' => $responseData['senderMobileNumber'],
                        'account_holder_name' => $blist['recipientName'],
                        'ben_mobile' => $blist['mobileNumber'],
                        'account_no' => $blist['bankAccountNumber'],
                        'ifsc' => $blist['ifsc'],
                        'bank_id' => $bank_id,
                        'bank_name' => $blist['bankName'],
                        'status' => 1,
                        'is_otp_verify' => $is_verify,
                        'is_verify' => $is_verify,
                        'beneId' => $blist['recipientId'],
                        'created' => date('Y-m-d H:i:s'),
                        'created_by' => $loggedAccountID
                    );
                    $this->db->insert('user_dmt_beneficiary',$benData);
                }
            }

            $api_response = array(
                'status' => 1,
                'availableLimit' => isset($responseData['availableLimit']) ? $responseData['availableLimit'] : '',
                'mobileVerified' => isset($responseData['mobileVerified']) ? $responseData['mobileVerified'] : '',
                'senderCity' => isset($responseData['senderCity']) ? $responseData['senderCity'] : '',
                'senderMobileNumber' => isset($responseData['senderMobileNumber']) ? $responseData['senderMobileNumber'] : '',
                'senderName' => isset($responseData['senderName']) ? $responseData['senderName'] : '',
                'totalLimit' => isset($responseData['totalLimit']) ? $responseData['totalLimit'] : '',
                'usedLimit' => isset($responseData['usedLimit']) ? $responseData['usedLimit'] : ''
            );
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['errorInfo']['error']['errorMessage']) ? $responseData['errorInfo']['error']['errorMessage'] : ''
            );
        }
        
        return $api_response;
    }

    public function memberFetchBen($mobile = '')
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Fetch Ben API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtServiceRequest>
<requestType>AllRecipient</requestType>
<senderMobileNumber>'.$mobile.'</senderMobileNumber>
<txnType>IMPS</txnType>
</dmtServiceRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Fetch Ben API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        $requestId = $this->generateRandomString();

        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_FETCH_SENDER_DETAIL_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtServiceResponse>
    <recipientList>
        <dmtRecipient>
            <bankAccountNumber>042601507937</bankAccountNumber>
            <bankCode>ICIC</bankCode>
            <bankName>ICICI Bank</bankName>
            <ifsc>ICIC0000426</ifsc>
            <isVerified>Y</isVerified>
            <mobileNumber>9289210012</mobileNumber>
            <recipientId>17942761</recipientId>
            <recipientName>MANISH   </recipientName>
            <recipientStatus>E</recipientStatus>
            <verifiedName>MANISH   </verifiedName>
        </dmtRecipient>
    </recipientList>
    <respDesc>Success</respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <senderMobileNumber>9289210048</senderMobileNumber>
</dmtServiceResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Fetch Ben API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            if(isset($responseData['recipientList']['dmtRecipient']['bankAccountNumber']))
            {
                $benList = array();
                $benList[0]['bankAccountNumber'] = isset($responseData['recipientList']['dmtRecipient']['bankAccountNumber']) ? $responseData['recipientList']['dmtRecipient']['bankAccountNumber'] : '';
                $benList[0]['bankName'] = isset($responseData['recipientList']['dmtRecipient']['bankName']) ? $responseData['recipientList']['dmtRecipient']['bankName'] : '';
                $benList[0]['ifsc'] = isset($responseData['recipientList']['dmtRecipient']['ifsc']) ? $responseData['recipientList']['dmtRecipient']['ifsc'] : '';
                $benList[0]['isVerified'] = isset($responseData['recipientList']['dmtRecipient']['isVerified']) ? $responseData['recipientList']['dmtRecipient']['isVerified'] : '';
                $benList[0]['mobileNumber'] = isset($responseData['recipientList']['dmtRecipient']['mobileNumber']) ? $responseData['recipientList']['dmtRecipient']['mobileNumber'] : '';
                $benList[0]['recipientId'] = isset($responseData['recipientList']['dmtRecipient']['recipientId']) ? $responseData['recipientList']['dmtRecipient']['recipientId'] : '';
                $benList[0]['recipientName'] = isset($responseData['recipientList']['dmtRecipient']['recipientName']) ? $responseData['recipientList']['dmtRecipient']['recipientName'] : '';
                $benList[0]['verifiedName'] = isset($responseData['recipientList']['dmtRecipient']['verifiedName']) ? $responseData['recipientList']['dmtRecipient']['verifiedName'] : '';
            }
            else
            {
                $benList = isset($responseData['recipientList']['dmtRecipient']) ? $responseData['recipientList']['dmtRecipient'] : array();
            }
            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['respDesc']) ? $responseData['respDesc'] : '',
                'benList' => $benList
            );
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['errorInfo']['error']['errorMessage']) ? $responseData['errorInfo']['error']['errorMessage'] : ''
            );
        }
        
        return $api_response;
    }

    public function addBeneficiary($post,$mobile)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $memberData = $this->db->select('user_code,name,mobile')->get_where('users',array('id'=>$loggedAccountID))->row_array();
        
        $addressData = $this->db->select('dob,address,pin_code')->get_where('user_dmt_activation',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'status'=>1))->row_array();

        $getBankName = $this->db->select('title,bank_code')->get_where('dmt_bank_list',array('id'=>$post['bankID']))->row_array();
        $bankname = isset($getBankName['title']) ? $getBankName['title'] : '';
        $bank_code = isset($getBankName['bank_code']) ? $getBankName['bank_code'] : '';

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
            'is_verify' => 0,
            'created' => date('Y-m-d H:i:s'),
            'created_by' => $loggedAccountID
        );
        $this->db->insert('user_dmt_beneficiary',$data);
        $sys_ben_id = $this->db->insert_id();

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Register Ben API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtServiceRequest>
<requestType>RegRecipient</requestType>
<senderMobileNumber>'.$mobile.'</senderMobileNumber>
<txnType>IMPS</txnType>
<recipientName>'.$post['account_holder_name'].'</recipientName>
<recipientMobileNumber>'.$post['ben_mobile'].'</recipientMobileNumber>
<bankCode>'.$bank_code.'</bankCode>
<bankAccountNumber>'.$post['account_no'].'</bankAccountNumber>
<ifsc>'.$post['ifsc'].'</ifsc>
</dmtServiceRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Register Ben API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        $requestId = $this->generateRandomString();

        $data = array();
        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_FETCH_SENDER_DETAIL_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtServiceResponse>
    <recipientList>
        <dmtRecipient>
            <bankAccountNumber>042601507937</bankAccountNumber>
            <bankCode>ICIC</bankCode>
            <bankName>ICICI Bank</bankName>
            <ifsc>ICIC0000426</ifsc>
            <isVerified>N</isVerified>
            <recipientId>17942761</recipientId>
            <recipientName>Manish Kumar</recipientName>
            <recipientStatus>E</recipientStatus>
            <verifiedName></verifiedName>
        </dmtRecipient>
    </recipientList>
    <respDesc>Recipient added with recipient ID: 17942761</respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <senderMobileNumber>9289210048</senderMobileNumber>
</dmtServiceResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Register Ben API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            $beneId = isset($responseData['recipientList']['dmtRecipient']['recipientId']) ? $responseData['recipientList']['dmtRecipient']['recipientId'] : '';
            $isVerified = isset($responseData['recipientList']['dmtRecipient']['isVerified']) ? $responseData['recipientList']['dmtRecipient']['isVerified'] : '';
            $is_verify = 0;
            if($isVerified == 'Y')
            {
                $is_verify = 1;
            }
            // update status
            $this->db->where('id',$sys_ben_id);
            $this->db->update('user_dmt_beneficiary',array('status'=>1,'beneId'=>$beneId,'is_otp_verify'=>$is_verify,'is_verify'=>$is_verify));
            
            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['respDesc']) ? $responseData['respDesc'] : '',
                'mobile' => $mobile
            );
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['errorInfo']['error']['errorMessage']) ? $responseData['errorInfo']['error']['errorMessage'] : ''
            );
        }

        return $api_response;
    }

    public function verifyBen($benId,$mobile,$total_wallet_charge,$admin_total_wallet_charge,$admin_id)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $memberData = $this->db->select('user_code,name,mobile,dmt_agent_id')->get_where('users',array('id'=>$loggedAccountID))->row_array();

        $dmt_agent_id = ($memberData['dmt_agent_id']) ? $memberData['dmt_agent_id'] : 'CC01BA19AGTU00000001';
        
        $addressData = $this->db->get_where('user_dmt_beneficiary',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'beneId'=>$benId))->row_array();
        $bank_id = $addressData['bank_id'];

        // Deduct member wallet
        if($total_wallet_charge)
        {
            //get member wallet_balance
            $get_member_status = $this->db->select('wallet_balance')->get_where('users',array('id'=>$loggedAccountID))->row_array();
            $before_wallet_balance = isset($get_member_status['wallet_balance']) ? $get_member_status['wallet_balance'] : 0 ;

            $after_wallet_balance = $before_wallet_balance - $total_wallet_charge;

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $total_wallet_charge,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'         => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'DMT Account #'.$addressData['account_no'].' Verify Charge Debited.'
            );

            $this->db->insert('member_wallet',$wallet_data);

            $user_wallet = array(
                'wallet_balance'=>$after_wallet_balance,        
            );    
            $this->db->where('id',$loggedAccountID);
            $this->db->where('account_id',$account_id);
            $this->db->update('users',$user_wallet);
        }


        // Deduct admin wallet
        if($admin_total_wallet_charge)
        {
            //get member wallet_balance
            $get_member_status = $this->db->select('virtual_wallet_balance')->get_where('users',array('id'=>$admin_id))->row_array();
            $admin_before_wallet_balance = isset($get_member_status['virtual_wallet_balance']) ? $get_member_status['virtual_wallet_balance'] : 0 ;

            $admin_after_wallet_balance = $admin_before_wallet_balance - $admin_total_wallet_charge;

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $admin_id,    
                'before_balance'      => $admin_before_wallet_balance,
                'amount'              => $admin_total_wallet_charge,  
                'after_balance'       => $admin_after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'         => 1,   
                'created'             => date('Y-m-d H:i:s'),      
                'description'         => 'DMT Account #'.$addressData['account_no'].' Verify Charge Debited.'
            );

            $this->db->insert('virtual_wallet',$wallet_data);

            $user_wallet = array(
                'virtual_wallet_balance'=>$admin_after_wallet_balance,        
            );    
            $this->db->where('id',$admin_id);
            $this->db->where('account_id',$account_id);
            $this->db->update('users',$user_wallet);
        }

        

        $getBankName = $this->db->select('title,bank_code')->get_where('dmt_bank_list',array('id'=>$bank_id))->row_array();
        $bankname = isset($getBankName['title']) ? $getBankName['title'] : '';
        $bank_code = isset($getBankName['bank_code']) ? $getBankName['bank_code'] : '';

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Verify Ben API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $requestId = $this->generateRandomString();

        //Sender Details
        $plainText = '<dmtServiceRequest>
<agentId>'.$dmt_agent_id.'</agentId>
<requestType>VerifyBankAcct</requestType>
<senderMobileNumber>'.$mobile.'</senderMobileNumber>
<bankCode>'.$bank_code.'</bankCode>
<bankAccountNumber>'.$addressData['account_no'].'</bankAccountNumber>
<ifsc>'.$addressData['ifsc'].'</ifsc>
<initChannel>AGT</initChannel>
</dmtServiceRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Verify Ben API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        

        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_FETCH_SENDER_DETAIL_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtServiceResponse>
    <impsName>MANISH   </impsName>
    <respDesc>success | Account verification  success</respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <senderMobileNumber>9289210048</senderMobileNumber>
    <txnId>10912265</txnId>
    <uniqueRefId>761U0WH8BD5MX5WC9N6HS7MFMB6MLJSAWOP</uniqueRefId>
</dmtServiceResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Verify Ben API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            // update status
            $this->db->where('account_id',$account_id);
            $this->db->where('member_id',$loggedAccountID);
            $this->db->where('beneId',$benId);
            $this->db->update('user_dmt_beneficiary',array('is_otp_verify'=>1,'is_verify'=>1,'verified_name'=>$responseData['impsName'],'ackno'=>$responseData['uniqueRefId'],'txnid'=>$responseData['txnId']));
            
            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['respDesc']) ? $responseData['respDesc'] : '',
                'mobile' => $mobile
            );
        }
        else
        {
            // refund wallet
            if($total_wallet_charge)
            {
                //get member wallet_balance
                $get_member_status = $this->db->select('wallet_balance')->get_where('users',array('id'=>$loggedAccountID))->row_array();
                $before_wallet_balance = isset($get_member_status['wallet_balance']) ? $get_member_status['wallet_balance'] : 0 ;

                $after_wallet_balance = $before_wallet_balance + $total_wallet_charge;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedAccountID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $total_wallet_charge,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'DMT Account #'.$addressData['account_no'].' Verify Charge Refunded.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                $user_wallet = array(
                    'wallet_balance'=>$after_wallet_balance,        
                );    
                $this->db->where('id',$loggedAccountID);
                $this->db->where('account_id',$account_id);
                $this->db->update('users',$user_wallet);
            }


            // Deduct admin wallet
            if($admin_total_wallet_charge)
            {
                //get member wallet_balance
                $get_member_status = $this->db->select('virtual_wallet_balance')->get_where('users',array('id'=>$admin_id))->row_array();
                $admin_before_wallet_balance = isset($get_member_status['virtual_wallet_balance']) ? $get_member_status['virtual_wallet_balance'] : 0 ;

                $admin_after_wallet_balance = $admin_before_wallet_balance + $admin_total_wallet_charge;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $admin_id,    
                    'before_balance'      => $admin_before_wallet_balance,
                    'amount'              => $admin_total_wallet_charge,  
                    'after_balance'       => $admin_after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'DMT Account #'.$addressData['account_no'].' Verify Charge Refunded.'
                );

                $this->db->insert('virtual_wallet',$wallet_data);

                $user_wallet = array(
                    'virtual_wallet_balance'=>$admin_after_wallet_balance,        
                );    
                $this->db->where('id',$admin_id);
                $this->db->where('account_id',$account_id);
                $this->db->update('users',$user_wallet);
            }
            
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['errorInfo']['error']['errorMessage']) ? $responseData['errorInfo']['error']['errorMessage'] : ''
            );
        }

        return $api_response;
    }

    public function deleteBen($benId,$mobile)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Delete Ben API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtServiceRequest>
<requestType>DelRecipient</requestType>
<senderMobileNumber>'.$mobile.'</senderMobileNumber>
<txnType>IMPS</txnType>
<recipientId>'.$benId.'</recipientId>
</dmtServiceRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Delete Ben API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        $requestId = $this->generateRandomString();

        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_FETCH_SENDER_DETAIL_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtServiceResponse>
    <impsName>MANISH   </impsName>
    <respDesc>success | Account verification  success</respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <senderMobileNumber>9289210048</senderMobileNumber>
    <txnId>10912265</txnId>
    <uniqueRefId>761U0WH8BD5MX5WC9N6HS7MFMB6MLJSAWOP</uniqueRefId>
</dmtServiceResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Register Ben API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            // update status
            $this->db->where('account_id',$account_id);
            $this->db->where('member_id',$loggedAccountID);
            $this->db->where('beneId',$benId);
            $this->db->delete('user_dmt_beneficiary');
            
            $api_response = array(
                'status' => 1,
                'message' => isset($responseData['respDesc']) ? $responseData['respDesc'] : '',
                'mobile' => $mobile
            );
        }
        else
        {
            $api_response = array(
                'status' => 0,
                'message' => isset($responseData['errorInfo']['error']['errorMessage']) ? $responseData['errorInfo']['error']['errorMessage'] : ''
            );
        }

        return $api_response;
    }

    public function transferFund($post,$total_wallet_charge,$admin_total_wallet_charge,$surcharge_amount,$admin_surcharge_amount)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        $admin_id = $this->User->get_admin_id($account_id);

        //$txnid = time().rand(1111,9999).rand(1111,9999);
        $txnid = $this->generateRandomString();

        $amount = $post['amount'];

        // Deduct member wallet
        //get member wallet_balance
        $get_member_status = $this->db->select('wallet_balance,dmt_agent_id')->get_where('users',array('id'=>$loggedAccountID))->row_array();
        $dmt_agent_id = ($get_member_status['dmt_agent_id']) ? $get_member_status['dmt_agent_id'] : 'CC01BA19AGTU00000001';
        $before_wallet_balance = isset($get_member_status['wallet_balance']) ? $get_member_status['wallet_balance'] : 0 ;

        $after_wallet_balance = $before_wallet_balance - $total_wallet_charge;

        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $loggedAccountID,    
            'before_balance'      => $before_wallet_balance,
            'amount'              => $total_wallet_charge,  
            'after_balance'       => $after_wallet_balance,      
            'status'              => 1,
            'type'                => 2,   
            'wallet_type'         => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'DMT Txn #'.$txnid.' Amount Debited.'
        );

        $this->db->insert('member_wallet',$wallet_data);

        $user_wallet = array(
            'wallet_balance'=>$after_wallet_balance,        
        );    
        $this->db->where('id',$loggedAccountID);
        $this->db->where('account_id',$account_id);
        $this->db->update('users',$user_wallet);


        // Deduct admin wallet
        //get member wallet_balance
        $get_member_status = $this->db->select('virtual_wallet_balance')->get_where('users',array('id'=>$admin_id))->row_array();
        $admin_before_wallet_balance = isset($get_member_status['virtual_wallet_balance']) ? $get_member_status['virtual_wallet_balance'] : 0 ;

        $admin_after_wallet_balance = $admin_before_wallet_balance - $admin_total_wallet_charge;

        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $admin_id,    
            'before_balance'      => $admin_before_wallet_balance,
            'amount'              => $admin_total_wallet_charge,  
            'after_balance'       => $admin_after_wallet_balance,      
            'status'              => 1,
            'type'                => 2,   
            'wallet_type'         => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'DMT Txn #'.$txnid.' Amount Debited.'
        );

        $this->db->insert('virtual_wallet',$wallet_data);

        $user_wallet = array(
            'virtual_wallet_balance'=>$admin_after_wallet_balance,        
        );    
        $this->db->where('id',$admin_id);
        $this->db->where('account_id',$account_id);
        $this->db->update('users',$user_wallet);

        $memberData = $this->db->select('user_code,name,mobile,aeps_wallet_balance')->get_where('users',array('id'=>$loggedAccountID))->row_array();
        $benId = $post['benId'];
        // get beneficiary data
        $beneficiaryData = $this->db->get_where('user_dmt_beneficiary',array('beneId'=>$benId))->row_array();
        $register_mobile = $beneficiaryData['register_mobile'];

        // get beneficiary data
        $getSenderId = $this->db->get_where('user_dmt_activation',array('mobile'=>$register_mobile,'status'=>1))->row_array();
        $from_sender_id = isset($getSenderId['id']) ? $getSenderId['id'] : 0 ;

        $data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'to_ben_id' => $beneficiaryData['id'],
            'from_sender_id' => $from_sender_id,
            'before_wallet_balance' => $before_wallet_balance,
            'transfer_amount' => $amount,
            'transfer_charge_amount' => $surcharge_amount,
            'total_wallet_charge' => $total_wallet_charge,
            'after_wallet_balance' => $after_wallet_balance,
            'admin_before_balance' => $admin_before_wallet_balance,
            'admin_transfer_amount' => $amount,
            'admin_charge_amount' => $admin_surcharge_amount,
            'admin_total_wallet_charge' => $admin_total_wallet_charge,
            'admin_after_balance' => $admin_after_wallet_balance,
            'transaction_id' => $txnid,
            'encode_transaction_id' => do_hash($txnid),
            'status' => 2,
            'wallet_type' => 1,
            'memberID' => $memberData['user_code'],
            'mobile' => $beneficiaryData['ben_mobile'],
            'account_holder_name' => $beneficiaryData['account_holder_name'],
            'account_no' => $beneficiaryData['account_no'],
            'ifsc' => $beneficiaryData['ifsc'],
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('user_dmt_transfer',$data);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Transaction #'.$txnid.' Saved.]'.PHP_EOL;
        $this->User->generateLog($log_msg);

        // start convinence fees api
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Transaction Conv Fees API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtTransactionRequest>
<requestType>GetCCFFee</requestType>
<agentId>'.$dmt_agent_id.'</agentId>
<txnAmount>'.($amount*100).'</txnAmount>
</dmtTransactionRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Transaction Conv Fees API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        $requestId = $this->generateRandomString();

        $data = array();
        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_TRANSACTION_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Transaction Conv Fees API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        $convFee = 0;
        
        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            $convFee = isset($responseData['custConvFee']) ? $responseData['custConvFee'] : 0;
        }
        // end convience fees api
        
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Transaction API Called.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        //Sender Details
        $plainText = '<dmtTransactionRequest>
<requestType>FundTransfer</requestType>
<senderMobileNo>'.$register_mobile.'</senderMobileNo>
<agentId>'.$dmt_agent_id.'</agentId>
<initChannel>AGT</initChannel>
<recipientId>'.$benId.'</recipientId>
<txnAmount>'.($amount*100).'</txnAmount>
<convFee>'.$convFee.'</convFee>
<txnType>IMPS</txnType>
</dmtTransactionRequest>';

        // Convert xml string into an object
        $postArray = simplexml_load_string($plainText);
          
        // Convert into json
        $postJson = json_encode($postArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Transaction API Post Data - '.$postJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        $key = $accountData['dmt_key'];
        $encrypt_xml_data = $this->encrypt($plainText, $key);

        //$requestId = $this->generateRandomString();

        $data = array();
        $data['accessCode'] = $accountData['dmt_access_code'];
        $data['requestId'] = $txnid;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = $accountData['dmt_institute_id'];

        $parameters = http_build_query($data);

        $api_url = DMT_TRANSACTION_API;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $response = $this->decrypt($output, $key);

        /*$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<dmtTransactionResponse>
    <fundTransferDetails>
        <fundDetail>
            <uniqueRefId>PGMIZPKHIVERA2ETS5ZF5E2LDMI4XXIU46U</uniqueRefId>
            <bankTxnId>201813107243</bankTxnId>
            <custConvFee>1000</custConvFee>
            <DmtTxnId>39385175</DmtTxnId>
            <impsName>MANISH   </impsName>
            <refId>CCA0A942641D55F494FAB9CF25C66C5672E</refId>
            <txnAmount>10000</txnAmount>
            <txnStatus>C</txnStatus>
        </fundDetail>
    </fundTransferDetails>
    <respDesc>Rs. 100.00 is debited from you wallet.  </respDesc>
    <responseCode>000</responseCode>
    <responseReason>Successful</responseReason>
    <senderMobileNo>9289210048</senderMobileNo>
    <uniqueRefId>PGMIZPKHIVERA2ETS5ZF5E2LDMI4XXIU46U</uniqueRefId>
</dmtTransactionResponse>';*/

        // Convert xml string into an object
        $responseArray = simplexml_load_string($response);
          
        // Convert into json
        $responseJson = json_encode($responseArray);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - RT('.$loggedUser['user_code'].') - DMT Transaction API Response - '.$responseJson.'.]'.PHP_EOL;
        $this->User->generateDMTLog($log_msg);

        // save api response 
        $api_data = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'api_response' => $response,
            'api_url' => $api_url,
            'api_post_data' => $postJson,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('dmt_api_response',$api_data);

        $responseData = json_decode($responseJson,true);

        $status = 0;
        $api_response = array();

        if(isset($responseData['responseReason']) && $responseData['responseReason'] == 'Successful')
        {
            $txnStatus = $responseData['fundTransferDetails']['fundDetail']['txnStatus'];
            $uniqueRefId = $responseData['fundTransferDetails']['fundDetail']['uniqueRefId'];
            $bankTxnId = $responseData['fundTransferDetails']['fundDetail']['bankTxnId'];
            $refId = $responseData['fundTransferDetails']['fundDetail']['refId'];
            $DmtTxnId = $responseData['fundTransferDetails']['fundDetail']['DmtTxnId'];
            if($txnStatus == 'C')
            {
                // update status
                $this->db->where('transaction_id',$txnid);
                $this->db->update('user_dmt_transfer',array('status'=>3,'op_txn_id'=>$uniqueRefId,'rrn'=>$bankTxnId,'ref_id'=>$refId,'dmt_txn_id'=>$DmtTxnId));

                $api_response = array(
                    'status' => 1,
                    'message' => 'SUCCESS'
                );
            }
            elseif($txnStatus == 'P' || $txnStatus == 'Q' || $txnStatus == 'R' || $txnStatus == 'T' || $txnStatus == 'S')
            {
                // update status
                $this->db->where('transaction_id',$txnid);
                $this->db->update('user_dmt_transfer',array('status'=>2,'op_txn_id'=>$uniqueRefId,'rrn'=>$bankTxnId,'ref_id'=>$refId,'dmt_txn_id'=>$DmtTxnId));

                $api_response = array(
                    'status' => 1,
                    'message' => 'PENDING'
                );
            }
            elseif($txnStatus == 'F')
            {
                // update status
                $this->db->where('transaction_id',$txnid);
                $this->db->update('user_dmt_transfer',array('status'=>4,'op_txn_id'=>$uniqueRefId,'rrn'=>$bankTxnId,'ref_id'=>$refId,'dmt_txn_id'=>$DmtTxnId));

                // Refund member wallet
                //get member wallet_balance
                $get_member_status = $this->db->select('wallet_balance')->get_where('users',array('id'=>$loggedAccountID))->row_array();
                $before_wallet_balance = isset($get_member_status['wallet_balance']) ? $get_member_status['wallet_balance'] : 0 ;

                $after_wallet_balance = $before_wallet_balance + $total_wallet_charge;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $loggedAccountID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $total_wallet_charge,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'DMT Txn #'.$txnid.' Amount Refund Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                $user_wallet = array(
                    'wallet_balance'=>$after_wallet_balance,        
                );    
                $this->db->where('id',$loggedAccountID);
                $this->db->where('account_id',$account_id);
                $this->db->update('users',$user_wallet);


                // Refund admin wallet
                //get member wallet_balance
                $get_member_status = $this->db->select('virtual_wallet_balance')->get_where('users',array('id'=>$admin_id))->row_array();
                $before_wallet_balance = isset($get_member_status['virtual_wallet_balance']) ? $get_member_status['virtual_wallet_balance'] : 0 ;

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
                    'description'         => 'DMT Txn #'.$txnid.' Amount Refund Credited.'
                );

                $this->db->insert('virtual_wallet',$wallet_data);

                $user_wallet = array(
                    'virtual_wallet_balance'=>$after_wallet_balance,        
                );    
                $this->db->where('id',$admin_id);
                $this->db->where('account_id',$account_id);
                $this->db->update('users',$user_wallet);

                $api_response = array(
                    'status' => 1,
                    'message' => 'FAILED'
                );
            }
            
        }
        else
        {
            $api_response = array(
                'status' => 1,
                'message' => 'PENDING'
            );
        }

        return $api_response;
    }

    public function hextobin($hexString) {
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length) {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0) {
                $binString = $packedString;
            } else {
                $binString .= $packedString;
            }

            $count += 2;
        }
        return $binString;
    }

    //*********** Encryption Function *********************
    public function encrypt($plainText, $key) {
        $secretKey = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }

    //*********** Decryption Function *********************
    public function decrypt($encryptedText, $key) {
        $key = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = $this->hextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }

    //********** Generate Random String ********
    public function generateRandomString($length = 35) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    

    
}


/* end of file: az.php */
/* Location: ./application/models/az.php */