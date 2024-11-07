<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Api extends CI_Controller{

    public function __construct() {
        parent::__construct();
		$this->lang->load('front_login' , 'english');
		$this->load->model('admin/Jwt_model');
        
    }
	


    //open money payout 


    public function getAccount()
    {
        
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.zwitch.io/v1/accounts?results_per_page=10',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ak_test_biErAwKPcdyzRzckjmRsZJaWxrmu3onyuqwd:sk_test_jiWEP63Jo4aiAcf0E5lulnk56IUVuADb8rdS'
              ),
            ));

            $response = curl_exec($curl);

            //success rsponse
            /*
            {
    "object": "list",
    "has_more": false,
    "data": [
        {
            "id": "va_KubwGGQd4m1oa7JH1oindSgpC",
            "object": "account.virtual",
            "type": "virtual",
            "used_as": "primary",
            "bank_name": "axis_bank",
            "name": "Ramnaresh Saw",
            "mobile_number": "7070091271",
            "email": "morningdigital2503@gmail.com",
            "account_number": "95151563961697",
            "ifsc_code": "UTIB0CCH274",
            "vpa": null,
            "additional_vpa": [],
            "whitelisted_remitters": [],
            "whitelisted_beneficiaries": [],
            "kyc": {
                "city": null,
                "postal_code": null,
                "state_code": null,
                "pan": null,
                "business_type": null,
                "business_category": null,
                "contact_person": null
            },
            "customer": {
                "id": "cus_9oPtUmRq44k96OSDQqHrDiF6T",
                "mobile_number": "7070091271"
            },
            "metadata": {},
            "status": "active",
            "created_at": 1705907342,
            "is_sandbox": true,
            "settlement_account_id": null
        },
        {
            "id": "va_z8HE3dYDmYiGky0s3cMaP9Wus",
            "object": "account.virtual",
            "type": "virtual",
            "used_as": "verification",
            "bank_name": "yes_bank",
            "name": "Ramnaresh Saw",
            "mobile_number": "7070091271",
            "email": "morningdigital2503@gmail.com",
            "account_number": "36363652008515036",
            "ifsc_code": "YESB0CMSNOC",
            "vpa": null,
            "additional_vpa": [],
            "whitelisted_remitters": [],
            "whitelisted_beneficiaries": [],
            "kyc": {
                "city": null,
                "postal_code": null,
                "state_code": null,
                "pan": null,
                "business_type": null,
                "business_category": null,
                "contact_person": null
            },
            "customer": {
                "id": "cus_9oPtUmRq44k96OSDQqHrDiF6T",
                "mobile_number": "7070091271"
            },
            "metadata": {},
            "status": "active",
            "created_at": 1705672082,
            "is_sandbox": true,
            "settlement_account_id": null
        },
        {
            "id": "va_dWHQYxqEQdIKFnslFPVqoX0DW",
            "object": "account.virtual",
            "type": "virtual",
            "used_as": "primary",
            "bank_name": "yes_bank",
            "name": "Ramnaresh Saw",
            "mobile_number": "7070091271",
            "email": "morningdigital2503@gmail.com",
            "account_number": "36363651522331603",
            "ifsc_code": "YESB0CMSNOC",
            "vpa": null,
            "additional_vpa": [],
            "whitelisted_remitters": [],
            "whitelisted_beneficiaries": [],
            "kyc": {
                "city": null,
                "postal_code": null,
                "state_code": null,
                "pan": null,
                "business_type": null,
                "business_category": null,
                "contact_person": null
            },
            "customer": {
                "id": "cus_9oPtUmRq44k96OSDQqHrDiF6T",
                "mobile_number": "7070091271"
            },
            "metadata": {},
            "status": "active",
            "created_at": 1705672082,
            "is_sandbox": true,
            "settlement_account_id": null
        }
    ]
}
             */


    }
    public function AddBeneficiary()
    {
       

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.zwitch.io/v1/accounts/va_KubwGGQd4m1oa7JH1oindSgpC/beneficiaries',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
   
    "type": "account_number",
  "metadata": {
    "key_1": "Vendor Payment"
  },
  "name_of_account_holder": "Lakshya Gujrati",
  "email": "lakshya@gmail.com",
  "phone": "8619651646",
  "bank_account_number": "8745000100015076",
  "bank_ifsc_code": "PUNB0874500"
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ak_test_biErAwKPcdyzRzckjmRsZJaWxrmu3onyuqwd:sk_test_jiWEP63Jo4aiAcf0E5lulnk56IUVuADb8rdS',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;


//success response

/*{
    "id": "vab_AEVrEDgozr5TDvCvmk2MFr9a1",
    "object": "beneficiary.account_number",
    "type": "account_number",
    "name_of_account_holder": "Lakshya Gujrati",
    "email": "lakshya@gmail.com",
    "phone": "8619651646",
    "bank_account_number": "8745000100015076",
    "bank_ifsc_code": "PUNB0874500",
    "bank_name": null,
    "metadata": {
        "key_1": "Vendor Payment"
    },
    "created_at": 1705995948,
    "status": "active",
    "is_sandbox": true,
    "account_id": "va_KubwGGQd4m1oa7JH1oindSgpC"
}
*/


    }

    public function getAllBeneficiary()
    {

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.zwitch.io/v1/accounts/va_KubwGGQd4m1oa7JH1oindSgpC/beneficiaries?results_per_page=10',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ak_test_biErAwKPcdyzRzckjmRsZJaWxrmu3onyuqwd:sk_test_jiWEP63Jo4aiAcf0E5lulnk56IUVuADb8rdS'
              ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;


        //success response
        /*
        {
    "object": "list",
    "has_more": false,
    "data": [
        {
            "id": "vab_AEVrEDgozr5TDvCvmk2MFr9a1",
            "object": "beneficiary.account_number",
            "type": "account_number",
            "name_of_account_holder": "Lakshya Gujrati",
            "email": "lakshya@gmail.com",
            "phone": "8619651646",
            "bank_account_number": "8745000100015076",
            "bank_ifsc_code": "PUNB0874500",
            "bank_name": null,
            "metadata": {
                "key_1": "Vendor Payment"
            },
            "created_at": 1705995948,
            "status": "active",
            "is_sandbox": true,
            "account_id": "va_KubwGGQd4m1oa7JH1oindSgpC"
        },
        {
            "id": "vab_PHdRY95abUcLOoEZcsizNOpbc",
            "object": "beneficiary.account_number",
            "type": "account_number",
            "name_of_account_holder": "Lakshya Gujrati",
            "email": "lakshya@gmail.com",
            "phone": "8619651646",
            "bank_account_number": "8745000100015076",
            "bank_ifsc_code": "PUNB0874500",
            "bank_name": null,
            "metadata": {
                "key_1": "Vendor Payment"
            },
            "created_at": 1705992359,
            "status": "active",
            "is_sandbox": true,
            "account_id": "va_KubwGGQd4m1oa7JH1oindSgpC"
        },
        {
            "id": "vab_1lxYZi1a1le1eY4fChwUf4DDU",
            "object": "beneficiary.account_number",
            "type": "account_number",
            "name_of_account_holder": "Lakshya",
            "email": null,
            "phone": "8619651646",
            "bank_account_number": "8745000100015076",
            "bank_ifsc_code": "PUNB0874500",
            "bank_name": null,
            "metadata": {
                "key_1": "Vendor Payment"
            },
            "created_at": 1705991874,
            "status": "active",
            "is_sandbox": true,
            "account_id": "va_KubwGGQd4m1oa7JH1oindSgpC"
        }
    ]
}*/



    }


    public function AccountVerify()
    {
        
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.zwitch.io/v1/verifications/bank-account',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'
{
  "force_penny_drop": false,
  "bank_account_number": "8745000100015076",
  "bank_ifsc_code": "PUNB0874500",
  "force_penny_drop_amount": 1,
  "merchant_reference_id": "lassl5454545445"
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ak_live_LElPYoDkk9uCjMFy2B34oodOG5VJJlwjJTYR:sk_live_jUaNDUcCfb7xCq1nPTWyLlKKxM3ith3e5tGr',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

//success response

/*
{
    "id": "accver_llTps3o1Hf7ewHN0GDKTN4fsP",
    "object": "bank_account_verification",
    "bank_account_number": "8745000100015076",
    "bank_ifsc_code": "PUNB0874500",
    "name_as_per_bank": "LAKSHYA GUJARATI S/O",
    "force_penny_drop": false,
    "force_penny_drop_amount": 1,
    "status": "success",
    "message": "success",
    "last_verified_at": "2024-01-23",
    "merchant_reference_id": "lassl5454545445",
    "created_at": 1706012313,
    "is_sandbox": false
}


*/
    }


    public function vpaVerify()
    {
        
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.zwitch.io/v1/verifications/vpa',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'
{
  "vpa": "lakshyabob@ybl",
  "merchant_reference_id": "PAYOL5212524152"
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ak_live_LElPYoDkk9uCjMFy2B34oodOG5VJJlwjJTYR:sk_live_jUaNDUcCfb7xCq1nPTWyLlKKxM3ith3e5tGr',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

//success response
/*


{
    "id": "accver_TSgYKW3argTDPz5xeOEhdzi6O",
    "object": "vpa_verification",
    "vpa": "lakshyabob@ybl",
    "merchant_reference_id": "PAYOL5212524152",
    "name_as_per_bank": "LAKSHYA GUJRATI",
    "status": "success",
    "message": "VPA is available for transaction",
    "bank_ifsc_code": "BARB0SAWARX",
    "bank_account_type": "savings",
    "created_at": 1706012481,
    "is_sandbox": false,
    "metadata": {}
}

*/

    }
    
    public function testDecode()
    {
        $str = '{"error":{"type":"invalid_request_error","message":"insufficient balance in account"}}';

$data = json_decode($str, true);

echo "<pre>";
print_r($data['error']['message']);
die;
    }
    
    
    public function getAccountBalance()
    {
        
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.zwitch.io/v1/accounts/va_vbx85JXfZF2eYwJqySLvppVVL/balance',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ak_live_LElPYoDkk9uCjMFy2B34oodOG5VJJlwjJTYR:sk_live_jUaNDUcCfb7xCq1nPTWyLlKKxM3ith3e5tGr'
              ),
            ));

            $response = curl_exec($curl);
            
            echo "<pre>";
            print_r($response);
            die;
            //success rsponse
            /*
            {
    "object": "list",
    "has_more": false,
    "data": [
        {
            "id": "va_KubwGGQd4m1oa7JH1oindSgpC",
            "object": "account.virtual",
            "type": "virtual",
            "used_as": "primary",
            "bank_name": "axis_bank",
            "name": "Ramnaresh Saw",
            "mobile_number": "7070091271",
            "email": "morningdigital2503@gmail.com",
            "account_number": "95151563961697",
            "ifsc_code": "UTIB0CCH274",
            "vpa": null,
            "additional_vpa": [],
            "whitelisted_remitters": [],
            "whitelisted_beneficiaries": [],
            "kyc": {
                "city": null,
                "postal_code": null,
                "state_code": null,
                "pan": null,
                "business_type": null,
                "business_category": null,
                "contact_person": null
            },
            "customer": {
                "id": "cus_9oPtUmRq44k96OSDQqHrDiF6T",
                "mobile_number": "7070091271"
            },
            "metadata": {},
            "status": "active",
            "created_at": 1705907342,
            "is_sandbox": true,
            "settlement_account_id": null
        },
        {
            "id": "va_z8HE3dYDmYiGky0s3cMaP9Wus",
            "object": "account.virtual",
            "type": "virtual",
            "used_as": "verification",
            "bank_name": "yes_bank",
            "name": "Ramnaresh Saw",
            "mobile_number": "7070091271",
            "email": "morningdigital2503@gmail.com",
            "account_number": "36363652008515036",
            "ifsc_code": "YESB0CMSNOC",
            "vpa": null,
            "additional_vpa": [],
            "whitelisted_remitters": [],
            "whitelisted_beneficiaries": [],
            "kyc": {
                "city": null,
                "postal_code": null,
                "state_code": null,
                "pan": null,
                "business_type": null,
                "business_category": null,
                "contact_person": null
            },
            "customer": {
                "id": "cus_9oPtUmRq44k96OSDQqHrDiF6T",
                "mobile_number": "7070091271"
            },
            "metadata": {},
            "status": "active",
            "created_at": 1705672082,
            "is_sandbox": true,
            "settlement_account_id": null
        },
        {
            "id": "va_dWHQYxqEQdIKFnslFPVqoX0DW",
            "object": "account.virtual",
            "type": "virtual",
            "used_as": "primary",
            "bank_name": "yes_bank",
            "name": "Ramnaresh Saw",
            "mobile_number": "7070091271",
            "email": "morningdigital2503@gmail.com",
            "account_number": "36363651522331603",
            "ifsc_code": "YESB0CMSNOC",
            "vpa": null,
            "additional_vpa": [],
            "whitelisted_remitters": [],
            "whitelisted_beneficiaries": [],
            "kyc": {
                "city": null,
                "postal_code": null,
                "state_code": null,
                "pan": null,
                "business_type": null,
                "business_category": null,
                "contact_person": null
            },
            "customer": {
                "id": "cus_9oPtUmRq44k96OSDQqHrDiF6T",
                "mobile_number": "7070091271"
            },
            "metadata": {},
            "status": "active",
            "created_at": 1705672082,
            "is_sandbox": true,
            "settlement_account_id": null
        }
    ]
}
             */


    }
    
    
    public function checkAepsStatusLive()
    {
        $api_url = FINGPAY_CHECK_STATUS_API_URL;
        
        $member_code = 'PAOLR798201';
        $postdata = array 
        (
            "merchantLoginId"=>$member_code,
            "superMerchantId"=>'1244'
        );

        // Generate JSON
        $json = json_encode($postdata);

        $hash_string = $json.'820a176061bdc289d6c35f2471caa2134ab923496b2dbcf334fa1d6ab607d3ae'. date('d/m/Y H:i:s');


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
        
        echo "<pre>";
        print_r($responseData);
        die;

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
    
    
   
    
    
    public function cosmosCheckStatus()
    {
        $response = array();
        
        
        $post = $this->input->post();
        
        $txn_id = isset($post['txn_id']) ? $post['txn_id'] : '';
        
        
        $payol_txn_id = 'PAYOLDG'.$txn_id;
        
         	$header = array
        (
            'Content-type: text/plain',
            'cid:6d02d0b56ba2e170d38ee13e4e56dca0'
        );
        $req = [
            'source' => 'PAYOL1760',
            'channel' => 'api',
            'terminalId' => 'PAYOL-1760',
            'extTransactionId' => $payol_txn_id
            
        ];
            
		
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'ca52c60cc5478bc01d5efe89885cf9cd';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
       
        $key= 'a82623486a0299efa1b48be02614218f';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
         $api_url = 'https://merchantprod.timepayonline.com/evok/qr/v1/qrStatus';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		 $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
         $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
        
        echo json_encode($decodeResult);
        //die;
        
    }
    
    
    public function cosmosCheckStatusByUtr()
    {
        $response = array();
        
        $get_qr_list = $this->get_where('tbl_upi_dynamic_qr_till_March_25',array('member_id'=>'455'))->result_array();
        
       
        
        $post = $this->input->post();
        
        $txn_id = isset($post['txn_id']) ? $post['txn_id'] : '';
        
        
        $payol_txn_id = $txn_id;
        
         	$header = array
        (
            'Content-type: text/plain',
            'cid:6d02d0b56ba2e170d38ee13e4e56dca0'
        );
        $req = [
            'source' => 'PAYOL1760',
            'channel' => 'api',
            'terminalId' => 'PAYOL-1760',
            'extTransactionId' => $payol_txn_id
            
        ];
            
		
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'ca52c60cc5478bc01d5efe89885cf9cd';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
       
        $key= 'a82623486a0299efa1b48be02614218f';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
         $api_url = 'https://merchantprod.timepayonline.com/evok/qr/v1/qrStatusRRN';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		 $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
         $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
        
        echo json_encode($decodeResult);
        //die;
        
    }
    
    
    
     public function GetRrnNumber()
    {
        
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.zwitch.io/v1/transfers/tr_H0CipwOs2iaFyfbRW5Ub9jN9j',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ak_live_LElPYoDkk9uCjMFy2B34oodOG5VJJlwjJTYR:sk_live_jUaNDUcCfb7xCq1nPTWyLlKKxM3ith3e5tGr'
              ),
            ));

            $response = curl_exec($curl);
             echo $response;
             die;
            //success rsponse
            /*
            {
    "object": "list",
    "has_more": false,
    "data": [
        {
            "id": "va_KubwGGQd4m1oa7JH1oindSgpC",
            "object": "account.virtual",
            "type": "virtual",
            "used_as": "primary",
            "bank_name": "axis_bank",
            "name": "Ramnaresh Saw",
            "mobile_number": "7070091271",
            "email": "morningdigital2503@gmail.com",
            "account_number": "95151563961697",
            "ifsc_code": "UTIB0CCH274",
            "vpa": null,
            "additional_vpa": [],
            "whitelisted_remitters": [],
            "whitelisted_beneficiaries": [],
            "kyc": {
                "city": null,
                "postal_code": null,
                "state_code": null,
                "pan": null,
                "business_type": null,
                "business_category": null,
                "contact_person": null
            },
            "customer": {
                "id": "cus_9oPtUmRq44k96OSDQqHrDiF6T",
                "mobile_number": "7070091271"
            },
            "metadata": {},
            "status": "active",
            "created_at": 1705907342,
            "is_sandbox": true,
            "settlement_account_id": null
        },
        {
            "id": "va_z8HE3dYDmYiGky0s3cMaP9Wus",
            "object": "account.virtual",
            "type": "virtual",
            "used_as": "verification",
            "bank_name": "yes_bank",
            "name": "Ramnaresh Saw",
            "mobile_number": "7070091271",
            "email": "morningdigital2503@gmail.com",
            "account_number": "36363652008515036",
            "ifsc_code": "YESB0CMSNOC",
            "vpa": null,
            "additional_vpa": [],
            "whitelisted_remitters": [],
            "whitelisted_beneficiaries": [],
            "kyc": {
                "city": null,
                "postal_code": null,
                "state_code": null,
                "pan": null,
                "business_type": null,
                "business_category": null,
                "contact_person": null
            },
            "customer": {
                "id": "cus_9oPtUmRq44k96OSDQqHrDiF6T",
                "mobile_number": "7070091271"
            },
            "metadata": {},
            "status": "active",
            "created_at": 1705672082,
            "is_sandbox": true,
            "settlement_account_id": null
        },
        {
            "id": "va_dWHQYxqEQdIKFnslFPVqoX0DW",
            "object": "account.virtual",
            "type": "virtual",
            "used_as": "primary",
            "bank_name": "yes_bank",
            "name": "Ramnaresh Saw",
            "mobile_number": "7070091271",
            "email": "morningdigital2503@gmail.com",
            "account_number": "36363651522331603",
            "ifsc_code": "YESB0CMSNOC",
            "vpa": null,
            "additional_vpa": [],
            "whitelisted_remitters": [],
            "whitelisted_beneficiaries": [],
            "kyc": {
                "city": null,
                "postal_code": null,
                "state_code": null,
                "pan": null,
                "business_type": null,
                "business_category": null,
                "contact_person": null
            },
            "customer": {
                "id": "cus_9oPtUmRq44k96OSDQqHrDiF6T",
                "mobile_number": "7070091271"
            },
            "metadata": {},
            "status": "active",
            "created_at": 1705672082,
            "is_sandbox": true,
            "settlement_account_id": null
        }
    ]
}
             */


    }
    
    
     public function cashDepositeCheckStatus()
    {
         $account_id = $this->User->get_domain_account();
	   $accountData = $this->User->get_account_data($account_id);
       		$key = $accountData['paysprint_aeps_key'];
			$iv = $accountData['paysprint_aeps_iv'];
		
		$post_data = array(
            'reference' => 'CSDD466639ca8444f19'
            
            
        );
		
		log_message('debug', 'Check Status api call');
		//echo json_encode($post_data);
		
		$cipher  =   openssl_encrypt(json_encode($post_data,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
		$body=       base64_encode($cipher);
       
       	$jwt_payload = array(
			    		'timestamp'=>time(),
			    		'partnerId'=>$accountData['paysprint_partner_id'],
			    		'reqid'=>time().rand(1111,9999)
			    	);

			    	$secret = $accountData['paysprint_secret_key'];

			    	$token = $this->Jwt_model->encode($jwt_payload,$secret);
                    	
			    	$header = [
			    		'Token:'.$token,
			    		'Authorisedkey:'.$accountData['paysprint_authorized_key']
			    	];

		$httpUrl = 'https://api.paysprint.in/api/v1/service/cashdeposit/V2/Cashdeposit/query';
		
		
		
			log_message('debug', ' Check Status api url - '.$httpUrl);

			    	log_message('debug', 'Check Status api header data - '.json_encode($header));

			    	log_message('debug', ' Check Status api body data - '.json_encode($post_data));
			    	
			    	
		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_URL => $httpUrl,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 60,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    //CURLOPT_POSTFIELDS => $post_data,   
		   CURLOPT_POSTFIELDS => array('body'=>$body),
		    CURLOPT_HTTPHEADER => $header
		));

		$raw_response = curl_exec($curl);
		curl_close($curl);
			log_message('debug', 'Check Status api response data - '.$raw_response);
		echo $raw_response;
		
    }
    
    
     public function testQrApi()
    {
        
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.hypermartretails.com/portal/api/staticQrPaymentReqAuth?memberid=TPAPI821965&txnpwd=592024&refId=rQYIVS&amount=22.0&utr=854152458512',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

    }
    
    public function aadharVerify()
    {
        
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.signzy.app/api/v3/getOkycOtp',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'
{
    "aadhaarNumber" : "496231127006"
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: TUQ8bBk7dHTzCy27QsodExUDYjDRJ25f',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

/*
success response

{
    "data": {
        "requestId": "aadhaar_v2_AdyxroyufPphlBcLlXbw",
        "otpSentStatus": true,
        "if_number": true,
        "isValidAadhaar": true,
        "status": "generate_otp_success"
    },
    "statusCode": 200,
    "message": "OTP Sent."
}


*/
    }
    
    
    public function aadharOtpVerify()
    {
        
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.signzy.app/api/v3/fetchOkycData',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'
{
    "requestId" : "aadhaar_v2_BotHYrkjIRHjrfqQapno",
    "otp" : "422310"
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: TUQ8bBk7dHTzCy27QsodExUDYjDRJ25f',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

/*
{
    "data": {
        "client_id": "aadhaar_v2_BotHYrkjIRHjrfqQapno",
        "full_name": "Lakshya Gujrati",
        "aadhaar_number": "496231127006",
        "dob": "1999-09-11",
        "gender": "M",
        "address": {
            "country": "India",
            "dist": "Bhilwara",
            "state": "Rajasthan",
            "po": "Jahazpur",
            "loc": "",
            "vtc": "Jahazpur",
            "subdist": "Jahazpur",
            "street": "CHIPO KA MOHALLA",
            "house": "WARD NO 10",
            "landmark": ""
        },
        "face_status": false,
        "face_score": -1,
        "zip": "311201",
        "profile_image": "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",
        "has_image": true,
        "email_hash": "",
        "mobile_hash": "82259c0a0d0ab8573e630aac2ead72d6cbec3bc41877b5fb998bf253e1cfdb17",
        "raw_xml": "https://aadhaar-kyc-docs.s3.amazonaws.com/signzy/aadhaar_xml/700620240515145821052/700620240515145821052-2024-05-15-092821244393.xml?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAY5K3QRM5FYWPQJEB%2F20240515%2Fap-south-1%2Fs3%2Faws4_request&X-Amz-Date=20240515T092821Z&X-Amz-Expires=432000&X-Amz-SignedHeaders=host&X-Amz-Signature=a44b94ced42c1eb88ccaf3f4a8d1a0b2e0d2b14b8d02c4c106ed299410a1780e",
        "zip_data": "https://aadhaar-kyc-docs.s3.amazonaws.com/signzy/aadhaar_xml/700620240515145821052/700620240515145821052-2024-05-15-092821177506.zip?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAY5K3QRM5FYWPQJEB%2F20240515%2Fap-south-1%2Fs3%2Faws4_request&X-Amz-Date=20240515T092821Z&X-Amz-Expires=432000&X-Amz-SignedHeaders=host&X-Amz-Signature=c48e537c2e7cb5ddf2ee8aa45afe623f3913a2c9aabb9d4c289df8adc0fef695",
        "care_of": "S/O Anil Gujrati",
        "share_code": "5083",
        "mobile_verified": false,
        "reference_id": "700620240515145821052",
        "aadhaar_pdf": null,
        "status": "success_aadhaar",
        "uniqueness_id": "0ce1c7ce306e25dd0c404e137f6ad0ff67f5800e2986161091cc28a16a1a83e9"
    },
    "statusCode": 200,
    "message": null
}


*/
    }
    
    
    public function PanVerification()
    {
        
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.signzy.app/api/v3/panv2/fetch-premium',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'
{
    
    "panNumber" : "DJBPG3725F"
    
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: TUQ8bBk7dHTzCy27QsodExUDYjDRJ25f',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

/*
success Response

{
    "result": {
        "name": "LAKSHYA GUJRATI",
        "number": "DJBPG3725F",
        "typeOfHolder": "Individual or Person",
        "isIndividual": true,
        "isValid": true,
        "firstName": "LAKSHYA",
        "middleName": "",
        "lastName": "GUJRATI",
        "panStatus": "VALID",
        "title": "Shri",
        "panStatusCode": "E",
        "aadhaarSeedingStatus": "Successful",
        "aadhaarSeedingStatusCode": "Y",
        "lastUpdatedOn": ""
    }
}



*/
    }
    
    public function checkAepsStatus()
    {
        $api_url = FINGPAY_CHECK_STATUS_API_URL;

        $postdata = array 
        (
            "merchantLoginId"=>'MPCND467031',
            "superMerchantId"=>'1244'
        );

        // Generate JSON
        $json = json_encode($postdata);

        $hash_string = $json.'820a176061bdc289d6c35f2471caa2134ab923496b2dbcf334fa1d6ab607d3ae'. date('d/m/Y H:i:s');


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
        
        echo "<pre>";
        print_r($responseData);
        die;
    }
   
   
    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */