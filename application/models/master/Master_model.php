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

class Master_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveWalletSetting($post)
    {       
        
        $this->db->where('id',1);
        $this->db->update('master_setting',array('min_transfer'=>$post['min_transfer'],'daily_transfer_limit'=>$post['daily_transfer_limit']));

        return true;

    }

    public function saveTransferCommision($post)
    {       
        $data = array(
          'from_1' => isset($post['from_1']) ? $post['from_1'] : 0,
          'to_1' => isset($post['to_1']) ? $post['to_1'] : 0,
          'flat_1' => isset($post['flat_1']) ? $post['flat_1'] : 0,
          'from_2' => isset($post['from_2']) ? $post['from_2'] : 0,
          'to_2' => isset($post['to_2']) ? $post['to_2'] : 0,
          'flat_2' => isset($post['flat_2']) ? $post['flat_2'] : 0,  
          'from_3' => isset($post['from_3']) ? $post['from_3'] : 0,
          'to_3' => isset($post['to_3']) ? $post['to_3'] : 0,
          'flat_3' => isset($post['flat_3']) ? $post['flat_3'] : 0,  
        
        );

        $this->db->where('id',1);
        $this->db->update('master_setting',$data);

        return true;

    }


    public function saveAEPSCommision($post)
    {       
        $data = array(
          'min_state_com' => isset($post['min_state_com']) ? $post['min_state_com'] : 0,
          'aeps_from_1' => isset($post['aeps_from_1']) ? $post['aeps_from_1'] : 0,
          'aeps_to_1' => isset($post['aeps_to_1']) ? $post['aeps_to_1'] : 0,
          'com_type_1' => isset($post['com_type_1']) ? $post['com_type_1'] : 1,
          'aeps_flat_1' => isset($post['aeps_flat_1']) ? $post['aeps_flat_1'] : 0,
          'aeps_percent_1' => isset($post['aeps_percent_1']) ? $post['aeps_percent_1'] : 0,
          'aeps_from_2' => isset($post['aeps_from_2']) ? $post['aeps_from_2'] : 0,
          'aeps_to_2' => isset($post['aeps_to_2']) ? $post['aeps_to_2'] : 0,
          'com_type_2' => isset($post['com_type_2']) ? $post['com_type_2'] : 1,
          'aeps_flat_2' => isset($post['aeps_flat_2']) ? $post['aeps_flat_2'] : 0,
          'aeps_percent_2' => isset($post['aeps_percent_2']) ? $post['aeps_percent_2'] : 0,
          'aeps_from_3' => isset($post['aeps_from_3']) ? $post['aeps_from_3'] : 0,
          'aeps_to_3' => isset($post['aeps_to_3']) ? $post['aeps_to_3'] : 0,
          'com_type_3' => isset($post['com_type_3']) ? $post['com_type_3'] : 1,
          'aeps_flat_3' => isset($post['aeps_flat_3']) ? $post['aeps_flat_3'] : 0,
          'aeps_percent_3' => isset($post['aeps_percent_3']) ? $post['aeps_percent_3'] : 0,
        );

        $this->db->where('id',1);
        $this->db->update('master_setting',$data);

        return true;

    }

    public function adminLogout()
    {
      $this->session->unset_userdata(ADMIN_SESSION_ID);
    }

    public function saveeKycData($post)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $accountData = $this->User->get_account_data($account_id);

        //save kyc data
        $data = array(
          'account_id' => $account_id,
          'member_id' => $loggedUser['id'],
          'mobile' => $post['mobile'],
          'email' => trim(strtolower($post['email'])),
          'pancard' => $post['pancard'],
          'aadhar' => $post['aadhar'],
          'status' => 0,
          'created' => date('Y-m-d H:i:s'),
          'created_by' => $loggedUser['id']
        );
        $this->db->insert('instantpay_ekyc',$data);
        $recordID = $this->db->insert_id();

        $api_url = INSTANTPAY_EKYC_URL;
        $aadhar = $post['aadhar'];
        
        $ivlen = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($aadhar,'aes-256-cbc', $accountData['instant_encryption_key'], OPENSSL_RAW_DATA, $iv);
        $encryptedData = base64_encode($iv . $ciphertext);
            
        $request = array(
            'mobile' => $post['mobile'],
            'pan' => $post['pancard'],
            'email' => $post['email'],
            'aadhaar' => $encryptedData,
            "latitude"=>"22.9734229",
            "longitude"=>"78.6568942",
            'consent' => "Y"
        );
        
        $header = array(
            'X-Ipay-Auth-Code: '.$accountData['instant_auth_code'],
            'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
            'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
            'X-Ipay-Endpoint-Ip: 164.52.219.77',
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$output = '{"statuscode":"TXN","actcode":null,"status":"Transaction Successful","data":{"aadhaar":"xxxxxxxx0626","otpReferenceID":"M2Q2NDhmYjktMmMyNi00NmM5LTgxN2UtOGY5N2Q0MWUyMTI2","hash":"7oqgzixSJU0wWmjLIS7wKMNP10GqVBPWF6z\/Mi2preY8jvEMXB3aogVVqzvqW7oNmUeiMHliCVPxh011e6+n9YMskxYTyK4wss3xN1S8jOmu4+R8qWWOlwFue9NW4E+IKahw2\/HtDNbqzgwHftiqHGa3tZITGCgTqu47vRYmgZVBBIt29ra5\/zO9OQzjp48mAmtHJwD+q4CvPrB\/vhFp7rM8jgHyB6QMaT4LRo8FG91I7frRhKw2rkpEPJLM1+ZX"},"timestamp":"2022-05-18 15:50:08","ipay_uuid":"h0689653dc27-dd88-4453-9ef8-ebf04f49893a","orderid":"1220518155000GXSUW","environment":"LIVE"}';*/

        $this->db->where('id',$recordID);
        $this->db->update('instantpay_ekyc',array('api_response'=>$output));

        //save api data
        $apiData = array(
          'account_id' => $account_id,
          'user_id' => $loggedUser['id'],
          'api_url' => $api_url,
          'api_response' => $output,
          'post_data' => json_encode($request),
          'header_data' => json_encode($header),
          'created' => date('Y-m-d H:i:s'),
          'created_by' => $loggedUser['id']
        );
        $this->db->insert('instantpay_api_response',$apiData);

        $decodeResponse = json_decode($output,true);

        $response = array();

        if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
        {
            $otpReferenceID = isset($decodeResponse['data']['otpReferenceID']) ? $decodeResponse['data']['otpReferenceID'] : '';
            $hash = isset($decodeResponse['data']['hash']) ? $decodeResponse['data']['hash'] : '';

            $this->db->where('id',$recordID);
            $this->db->update('instantpay_ekyc',array('status'=>1,'otpReferenceID'=>$otpReferenceID,'hash'=>$hash,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedUser['id']));

            $response = array(
              'status' => 1,
              'msg' => 'OTP sent on your registered mobile with aadhar card.',
              'otpReferenceID' => $otpReferenceID
            );
        }
        else
        {
            $response = array(
              'status' => 0,
              'msg' => isset($decodeResponse['status']) ? $decodeResponse['status'] : 'Sorry ! Something wrong from server side, please try again later.'
            );
        }

        return $response;

    }

    public function verifyeKyc($post)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $accountData = $this->User->get_account_data($account_id);
        $otpReferenceID = $post['otpReferenceID'];
        $chkRefID = $this->db->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$loggedUser['id'],'otpReferenceID'=>$otpReferenceID,'status'=>1))->row_array();
        $recordID = $chkRefID['id'];
        $hash = $chkRefID['hash'];

        $api_url = INSTANTPAY_EKYC_VERIFY_URL;
        
        $request = array(
            'otpReferenceID' => $otpReferenceID,
            'hash' => $hash,
            'otp' => $post['otp_code']
        );    
        
        $header = array(
            'X-Ipay-Auth-Code: '.$accountData['instant_auth_code'],
            'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
            'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
            'X-Ipay-Endpoint-Ip: 164.52.219.77',
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$output = '{"statuscode":"ERR","actcode":"EXPIRED","status":"otpReferenceID is expired","data":null,"timestamp":"2022-05-18 15:57:35","ipay_uuid":"h0689653ded7-7727-4eff-b0c8-0799b53cbb09","orderid":null,"environment":"LIVE"}';*/

        //save api data
        $apiData = array(
          'account_id' => $account_id,
          'user_id' => $loggedUser['id'],
          'api_url' => $api_url,
          'api_response' => $output,
          'post_data' => json_encode($request),
          'header_data' => json_encode($header),
          'created' => date('Y-m-d H:i:s'),
          'created_by' => $loggedUser['id']
        );
        $this->db->insert('instantpay_api_response',$apiData);

        $decodeResponse = json_decode($output,true);

        $response = array();

        if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
        {
            $outletId = isset($decodeResponse['data']['outletId']) ? $decodeResponse['data']['outletId'] : '';
            $aadharData = isset($decodeResponse['data']) ? json_encode($decodeResponse['data']) : '';

            $this->db->where('id',$recordID);
            $this->db->update('instantpay_ekyc',array('status'=>2,'aadhar_data'=>$aadharData,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedUser['id']));

            $this->db->where('id',$loggedUser['id']);
            $this->db->update('users',array('is_instantpay_ekyc'=>1,'instantpay_outlet_id'=>$outletId));

            $response = array(
              'status' => 1,
              'msg' => 'Congratulations ! Your eKyc has been completed.'
            );
        }
        else
        {
            $response = array(
              'status' => 0,
              'msg' => isset($decodeResponse['status']) ? $decodeResponse['status'] : 'Sorry ! Something wrong from server side, please try again later.'
            );
        }

        return $response;

    }


}


/* end of file: az.php */
/* Location: ./application/models/az.php */