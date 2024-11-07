<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Kyc extends CI_Controller{

    public function __construct() {
        parent::__construct();

        //load language
		$this->User->checkApiMemberPermission();

        $this->load->model('portal/Kyc_model');       
		$this->lang->load('kyc', 'english');
        $this->lang->load('front_common' , 'english');
    }
	

    public function index($uname_prefix = '' , $username = ''){

        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $chk_status=$this->db->get_where('portal_kyc',array('user_id'=>$loggedAccountID))->row_array();

        $countryList = $this->db->order_by('name','asc')->get('countries')->result_array();

        $stateList = $this->db->order_by('name','asc')->get_where('states',array('country_code_char2'=>'IN'))->result_array();


        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'account_id'=>$account_id,    
            'countryList' => $countryList,
            'stateList' => $stateList,
            'chk_status' =>$chk_status,        
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'kyc/kyc'
        );

        $this->parser->parse('portal/layout/column-1' , $data);
    }


    
        public function updateKyc()
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
          $chk_status=$this->db->get_where('portal_kyc',array('user_id'=>$loggedAccountID))->row_array();
        //check for foem validation
        $post = $this->input->post();
        $this->load->library('form_validation'); 
         
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email ', 'required|xss_clean|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|xss_clean|numeric|max_length[12]');
        $this->form_validation->set_rules('address', 'Address', 'required|xss_clean');
        $this->form_validation->set_rules('pincode', 'Name', 'required|xss_clean');
        // $this->form_validation->set_rules('aadhar_number', 'Aadhar Number', 'required|xss_clean');
        // $this->form_validation->set_rules('pancard_number', 'Pancard Number', 'required|xss_clean');
        // $this->form_validation->set_rules('bank_account_number', 'Bank Account Number', 'required|xss_clean');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
        $this->form_validation->set_rules('ifsc_code', 'IFSC Code', 'required|xss_clean');
        $this->form_validation->set_rules('bank_name', 'Bank Name', 'required|xss_clean');
        $this->form_validation->set_rules('state_id', 'State', 'required|xss_clean');
        $this->form_validation->set_rules('city', 'City', 'required|xss_clean');
        $this->form_validation->set_rules('block', 'Block', 'required|xss_clean');
        $this->form_validation->set_rules('village', 'Village', 'required|xss_clean');
        $this->form_validation->set_rules('district', 'District', 'required|xss_clean');
        $this->form_validation->set_rules('business_type', 'Business Type', 'required|xss_clean');
        $this->form_validation->set_rules('business_industry', 'Business Industry', 'required|xss_clean');
        $this->form_validation->set_rules('business_address', 'Business Address', 'required|xss_clean');
        $this->form_validation->set_rules('business_website', 'Business Website', 'required|xss_clean');
        $this->form_validation->set_rules('business_email', 'Business Email', 'required|xss_clean');
        $this->form_validation->set_rules('service_name', 'Service Name', 'required|xss_clean');
        $this->form_validation->set_rules('use_case', 'Use Case', 'required|xss_clean');
        $this->form_validation->set_rules('business_proof', 'Business Proof', 'required|xss_clean');
        $this->form_validation->set_rules('signatory_name', 'Signatory Name', 'required|xss_clean');
        $this->form_validation->set_rules('signatory_mobile', 'Signatory Mobile', 'required|xss_clean');
        $this->form_validation->set_rules('signatory_aadhar', 'Signatory Aadhar', 'required|xss_clean');
        $this->form_validation->set_rules('signatory_mobile', 'Signatory Mobile', 'required|xss_clean');
        $this->form_validation->set_rules('branch', 'Branch', 'required|xss_clean');
        $this->form_validation->set_rules('signatory_mobile', 'Signatory Mobile', 'required|xss_clean');
        if(!$chk_status['signatory_aadhar_image'])
        {
            if(!isset($_FILES['profile']['name']) || !$_FILES['profile']['name'])
            $this->form_validation->set_rules('profile', 'Signatory Aadhar Card', 'required|xss_clean');
        }
        
         if(!$chk_status['signatory_pan_image'])
        {
        if(!isset($_FILES['profile2']['name']) || !$_FILES['profile2']['name'])
            $this->form_validation->set_rules('profile2', 'Signatory Pan Card', 'required|xss_clean');
        }

        if(!$chk_status['signatory_live_image'])
        {

        if(!isset($_FILES['profile3']['name']) || !$_FILES['profile3']['name'])
            $this->form_validation->set_rules('profile3', 'Signatory Live Photo Captured by GPS Camera', 'required|xss_clean');
        }

         if(!$chk_status['application_form'])
        {

        if(!isset($_FILES['profile4']['name']) || !$_FILES['profile4']['name'])
            $this->form_validation->set_rules('profile4', 'Application Form', 'required|xss_clean');
        } 

         if(!$chk_status['company_pan_card'])
        {

        if(!isset($_FILES['profile5']['name']) || !$_FILES['profile5']['name'])
            $this->form_validation->set_rules('profile5', 'Company Pan Card', 'required|xss_clean');

        }

         if(!$chk_status['business_photo'])
        {

        if(!isset($_FILES['profile6']['name']) || !$_FILES['profile6']['name'])
            $this->form_validation->set_rules('profile6', 'Business Photo', 'required|xss_clean');

        }


        if ($this->form_validation->run() == FALSE) {
            
            $this->index();
        }
        else
        {   


             $filePath = '';
            if($_FILES['profile']['name'])
            {
                //generate icon name randomly
                $fileName = rand(1111,999999999);
                $config['upload_path'] = './media/kyc_document/';
                $config['allowed_types'] = 'jpeg|JPEG|JPG|PNG|jpg|png';
                $config['file_name']        = $fileName;
                    
                $this->load->library('upload', $config);
                $this->upload->do_upload('profile');
                $uploadError = $this->upload->display_errors();
                if($uploadError){
                    $this->Az->redirect('portal/kyc', 'system_message_error',$uploadError);
                }
                else
                {
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $filePath = substr($config['upload_path'] . $fileData['file_name'], 2);
                        
                }
                
            }


            

             $filePath2 = '';
            if($_FILES['profile2']['name'])
            {
                //generate icon name randomly
                $fileName = rand(1111,999999999);
                $config['upload_path'] = './media/kyc_document/';
                $config['allowed_types'] = 'jpeg|JPEG|JPG|PNG|jpg|png';                
                $config['file_name']        = $fileName;
                    
                $this->load->library('upload', $config);
                $this->upload->do_upload('profile2');
                $uploadError = $this->upload->display_errors();
                if($uploadError){
                    $this->Az->redirect('portal/kyc', 'system_message_error',$uploadError);
                }
                else
                {
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $filePath2 = substr($config['upload_path'] . $fileData['file_name'], 2);
                        
                }
                
            }

             $filePath3 = '';
            if($_FILES['profile3']['name'])
            {
                //generate icon name randomly
                $fileName = rand(1111,999999999);
                $config['upload_path'] = './media/kyc_document/';
                $config['allowed_types'] = 'jpeg|JPEG|JPG|PNG|jpg|png';                
                $config['file_name']        = $fileName;
                    
                $this->load->library('upload', $config);
                $this->upload->do_upload('profile3');
                $uploadError = $this->upload->display_errors();
                if($uploadError){
                    $this->Az->redirect('portal/kyc', 'system_message_error',$uploadError);
                }
                else
                {
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $filePath3 = substr($config['upload_path'] . $fileData['file_name'], 2);
                        
                }
                
            }

             $filePath4 = '';
            if($_FILES['profile4']['name'])
            {
                //generate icon name randomly
                $fileName = rand(1111,999999999);
                $config['upload_path'] = './media/kyc_document/';
                $config['allowed_types'] = 'jpeg|JPEG|JPG|PNG|jpg|png';                
                $config['file_name']        = $fileName;
                    
                $this->load->library('upload', $config);
                $this->upload->do_upload('profile4');
                $uploadError = $this->upload->display_errors();
                if($uploadError){
                    $this->Az->redirect('portal/kyc', 'system_message_error',$uploadError);
                }
                else
                {
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $filePath4 = substr($config['upload_path'] . $fileData['file_name'], 2);
                        
                }
                
            }

            $filePath5 = '';
            if($_FILES['profile5']['name'])
            {
                //generate icon name randomly
                $fileName = rand(1111,999999999);
                $config['upload_path'] = './media/kyc_document/';
                $config['allowed_types'] = 'jpeg|JPEG|JPG|PNG|jpg|png';                
                $config['file_name']        = $fileName;
                    
                $this->load->library('upload', $config);
                $this->upload->do_upload('profile5');
                $uploadError = $this->upload->display_errors();
                if($uploadError){
                    $this->Az->redirect('portal/kyc', 'system_message_error',$uploadError);
                }
                else
                {
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $filePath5 = substr($config['upload_path'] . $fileData['file_name'], 2);
                        
                }
                
            }

             $filePath6 = '';
            if($_FILES['profile6']['name'])
            {
                //generate icon name randomly
                $fileName = rand(1111,999999999);
                $config['upload_path'] = './media/kyc_document/';
                $config['allowed_types'] = 'jpeg|JPEG|JPG|PNG|jpg|png';                
                $config['file_name']        = $fileName;
                    
                $this->load->library('upload', $config);
                $this->upload->do_upload('profile6');
                $uploadError = $this->upload->display_errors();
                if($uploadError){
                    $this->Az->redirect('portal/kyc', 'system_message_error',$uploadError);
                }
                else
                {
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $filePath6 = substr($config['upload_path'] . $fileData['file_name'], 2);
                        
                }
                
            }



    // update organizer detail
            $this->Kyc_model->updateKyc($post,$filePath,$filePath2,$filePath3,$filePath4,$filePath5,$filePath6);
            $this->Az->redirect('portal/kyc', 'system_message_error',lang('KYC_UPDATED_SAVED'));
        
            
            
            
        }
    
    }


    public function aadharVerify($aadhar_no = ''){

        $response = array();
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $is_error = 0;
        // check member id valid or not
        if($aadhar_no == '')
        {
            $is_error = 1;
        }

        if($is_error)
        {
            $response = array(
                    'status' => 0,
                    'msg' => 'Aadhar Number not valid.'
                );
        }
        else
        {

                $post_data = array(

                    'aadhaarNumber'=> $aadhar_no

                    );

                $api_url = AADHAR_KYC_API_URL;

                $header_data = array(

                    'Authorization:'.AADHAR_API_TOKEN,
                    'Content-Type: application/json'

                );


                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $api_url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>json_encode($post_data),
                  CURLOPT_HTTPHEADER =>$header_data ,
                ));

                $response = curl_exec($curl);

                curl_close($curl);


                $apiData = array(
                'account_id' => $account_id,
                'user_id' => $loggedAccountID,
                'api_response' => $response,
                'api_url' => $api_url,
                'post_data'=>json_encode($post_data),
                'header_data' => json_encode($header_data),
                'created' => date('Y-m-d H:i:s'),
                'created_by'=>$loggedAccountID
            );
            $this->db->insert('aadhar_api_response',$apiData);

            $responseData = json_decode($response, true);

            if(isset($responseData['statusCode']) && $responseData['statusCode'] == 200 && $responseData['message'] == 'OTP Sent.')
            {

                    $requestId = $responseData['data']['requestId'];

                 $response = array(
                    'status' => 1,
                    'msg' => 'Otp Sent Successfully',
                    'request_id' => $requestId
                );

            }


            else
            {
                  $response = array(
                    'status' => 0,
                    'msg' => $responseData['error']['message'],
                   
                );

            }
            


        }

        echo json_encode($response);


    }


    public function aadharVerifyOtp($aadhar_request_id = '',$aadhar_otp=''){

        $response = array();
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $is_error = 0;
        // check member id valid or not
        if($aadhar_request_id == '' && $aadhar_otp = '' )
        {
            $is_error = 1;
        }

        if($is_error)
        {
            $response = array(
                    'status' => 0,
                    'msg' => 'Otp not valid.'
                );
        }
        else
        {

                $post_data = array(

                    'requestId'=> $aadhar_request_id,
                    'otp' =>$aadhar_otp

                    );

                $api_url = AADHAR_KYC_OTP_VERIFY_API_URL;

                $header_data = array(

                    'Authorization:'.AADHAR_API_TOKEN,
                    'Content-Type: application/json'

                );


                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $api_url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>json_encode($post_data),
                  CURLOPT_HTTPHEADER =>$header_data ,
                ));

                $response = curl_exec($curl);

                curl_close($curl);


                $apiData = array(
                'account_id' => $account_id,
                'user_id' => $loggedAccountID,
                'api_response' => $response,
                'api_url' => $api_url,
                'post_data'=>json_encode($post_data),
                'header_data' => json_encode($header_data),
                'created' => date('Y-m-d H:i:s'),
                'created_by'=>$loggedAccountID
            );
            $this->db->insert('aadhar_api_response',$apiData);

            $responseData = json_decode($response, true);

            if(isset($responseData['statusCode']) && $responseData['statusCode'] == 200)
            {

                    $requestId = $responseData['data']['requestId'];

                 $response = array(
                    'status' => 1,
                    'msg' => 'Aadhar Verified Successfully',
                    
                );

            }


            else
            {
                  $response = array(
                    'status' => 0,
                    'msg' => $responseData['message'],
                   
                );

            }
            


        }

        echo json_encode($response);


    }


    public function panVerify($pan_no = '',$signatory_name=''){

        $response = array();
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $is_error = 0;
        // check member id valid or not
        if($pan_no == '' && $signatory_name == '')
        {
            $is_error = 1;
        }

        if($is_error)
        {
            $response = array(
                    'status' => 0,
                    'msg' => 'Pan Number not valid.'
                );
        }
        else
        {

                $post_data = array(

                    'name'=> $signatory_name,
                    'panNumber' =>$pan_no,
                    'fuzzy' => 'True'

                    );

                $api_url = 'https://api.signzy.app/api/v3/individualPanVerification';

                $header_data = array(

                    'Authorization:'.AADHAR_API_TOKEN,
                    'Content-Type: application/json'

                );


                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $api_url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>json_encode($post_data),
                  CURLOPT_HTTPHEADER =>$header_data ,
                ));

                $response = curl_exec($curl);

                curl_close($curl);


                $apiData = array(
                'account_id' => $account_id,
                'user_id' => $loggedAccountID,
                'api_response' => $response,
                'api_url' => $api_url,
                'post_data'=>json_encode($post_data),
                'header_data' => json_encode($header_data),
                'created' => date('Y-m-d H:i:s'),
                'created_by'=>$loggedAccountID
            );
            $this->db->insert('aadhar_api_response',$apiData);

            $responseData = json_decode($response, true);

            // echo "<pre>";
            // print_r($responseData['result']);
            // die;
            /*


            {
    "result": {
        "verified": false,
        "message": "Verification completed with negative result",
        "upstreamName": "LAKSHYA GUJRATI"
    }
}
*/
            if(isset($responseData['result']) && $responseData['result'])
            {

                 

                 $response = array(
                    'status' => 1,                    
                    'msg' => $responseData['result']['upstreamName']
                    
                );

            }


            else
            {
                  $response = array(
                    'status' => 0,
                    'msg' => $responseData['error']['message'],
                   
                );

            }
            


        }

        echo json_encode($response);


    }


	
	
	
	

    
    
	
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */