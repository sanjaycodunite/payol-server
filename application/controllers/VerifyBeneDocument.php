<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class VerifyBeneDocument extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        //load language
        //$this->lang->load('front/message', 'english');
        $this->load->model('admin/Jwt_model');
    }
	
	public function index($bene_id = 0){
	     $account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);

        $chk_bene = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$account_id,'bene_id'=>$bene_id))->row_array();
        if(!$chk_bene){

            $this->Az->redirect('home', 'system_message_error',lang('LOGIN_ACTIVE_FAILED'));
        }
		
        $siteUrl = base_url();
		$data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'bene_id' => $bene_id,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'VerifyBeneDocument'
        );
        $this->parser->parse('front/layout/column-3' , $data);
    }

	public function uploadDocumentAuth(){
        
         $domain_account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($domain_account_id);
					
        $post = $this->input->post();
        //check for foem validation

        $bene_id = $post['bene_id'];
        $chk_bene = $this->db->get_where('new_payout_beneficiary',array('account_id'=>$domain_account_id,'bene_id'=>$bene_id,'is_verified'=>0))->row_array(); 

        $account_id = $chk_bene['user_id'];
        
        
        $this->load->library('form_validation');


        if (!$post) {
            
            $this->index($post['bene_id']);
        }
        else
        {   

            log_message('debug', 'Upload document api called.');

            $bene_id = $post['bene_id'];
            
            $datapost = array();
            $datapost['doctype'] = $post['document_type'];
            
            $datapost['passbook'] = new CURLFile($_FILES['passbook']['tmp_name'], $_FILES['passbook']['type'], $_FILES['passbook']['name']);
            
            if($post['document_type'] == 'PAN'){

                $datapost['panimage'] = new CURLFile($_FILES['panimage']['tmp_name'], $_FILES['panimage']['type'], $_FILES['panimage']['name']);
            }

            if($post['document_type'] == 'AADHAAR'){

                $datapost['front_image'] = new CURLFile($_FILES['aadhar_front']['tmp_name'], $_FILES['aadhar_front']['type'], $_FILES['aadhar_front']['name']);

                $datapost['back_image'] = new CURLFile($_FILES['aadhar_back']['tmp_name'], $_FILES['aadhar_back']['type'], $_FILES['aadhar_back']['name']);

            }
            
            $datapost['bene_id'] = $bene_id;


            log_message('debug', 'Upload document api post request data - '.json_encode($datapost));

            
            $key = $accountData['paysprint_aeps_key'];
             $iv = $accountData['paysprint_aeps_iv'];

            
            $cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
            $body=       base64_encode($cipher);
            $jwt_payload = array(
                'timestamp'=>time(),
               'partnerId'=>$accountData['paysprint_partner_id'],
                'reqid'=>time().rand(1111,9999)
            );
            
             $secret = $accountData['paysprint_secret_key'];

            $token = $this->Jwt_model->encode($jwt_payload,$secret);
            
            //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));
            
            $header = [
                'Token:'.$token,
                
            ];
            
            
            $httpUrl = PAYSPRINT_BENEFICIARY_UPLOAD_DOCUMENT_URL;
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

            $raw_response = curl_exec($curl);
            curl_close($curl);

            log_message('debug', 'Upload document api final response - '.$raw_response);
            
            $responseData = json_decode($raw_response,true);
            
            $api_data = array(
              'user_id' => $account_id,
              'api_url' => $httpUrl,
              'post_data' => json_encode($datapost),
              'api_response' => $raw_response,
              'created' => date('Y-m-d H:i:s')  
            );
            $this->db->insert('new_aeps_payout_api_response',$api_data);

            if(isset($responseData) && $responseData['response_code'] == 1 && $responseData['status'] == true){

                $passbook = '';
                if(isset($_FILES['passbook']['name']) && $_FILES['passbook']['name']){
                    $config['upload_path'] = './media/kyc_document/';
                    $config['allowed_types'] = 'jpg|png|jpeg|pdf|PDF';
                    $config['max_size'] = 2048;
                    $fileName = time().rand(111111,999999);
                    $config['file_name'] = $fileName;
                    $this->load->library('upload', $config);
                    $this->upload->do_upload('passbook');       
                    $uploadError = $this->upload->display_errors();
                    if($uploadError){
                        $this->Az->redirect('VerifyBeneDocument/index/'.$bene_id, 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
                    }
                    else
                    {
                        $fileData = $this->upload->data();
                        //get uploaded file path
                        $passbook = substr($config['upload_path'] . $fileData['file_name'], 2);
                    }
                }


                $pancard = '';
                if(isset($_FILES['panimage']['name']) && $_FILES['panimage']['name']){
                    $config['upload_path'] = './media/kyc_document/';
                    $config['allowed_types'] = 'jpg|png|jpeg|pdf|PDF';
                    $config['max_size'] = 2048;
                    $fileName = time().rand(111111,999999);
                    $config['file_name'] = $fileName;
                    $this->load->library('upload', $config);
                    $this->upload->do_upload('panimage');       
                    $uploadError = $this->upload->display_errors();
                    if($uploadError){
                        $this->Az->redirect('VerifyBeneDocument/index/'.$bene_id, 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
                    }
                    else
                    {
                        $fileData = $this->upload->data();
                        //get uploaded file path
                        $pancard = substr($config['upload_path'] . $fileData['file_name'], 2);
                    }
                }


                $aadhar_front = '';
                if(isset($_FILES['aadhar_front']['name']) && $_FILES['aadhar_front']['name']){
                    $config['upload_path'] = './media/kyc_document/';
                    $config['allowed_types'] = 'jpg|png|jpeg|pdf|PDF';
                    $config['max_size'] = 2048;
                    $fileName = time().rand(111111,999999);
                    $config['file_name'] = $fileName;
                    $this->load->library('upload', $config);
                    $this->upload->do_upload('aadhar_front');       
                    $uploadError = $this->upload->display_errors();
                    if($uploadError){
                        $this->Az->redirect('VerifyBeneDocument/index/'.$bene_id, 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
                    }
                    else
                    {
                        $fileData = $this->upload->data();
                        //get uploaded file path
                        $aadhar_front = substr($config['upload_path'] . $fileData['file_name'], 2);
                    }
                }


                $aadhar_back = '';
                if(isset($_FILES['aadhar_back']['name']) && $_FILES['aadhar_back']['name']){
                    $config['upload_path'] = './media/kyc_document/';
                    $config['allowed_types'] = 'jpg|png|jpeg|pdf|PDF';
                    $config['max_size'] = 2048;
                    $fileName = time().rand(111111,999999);
                    $config['file_name'] = $fileName;
                    $this->load->library('upload', $config);
                    $this->upload->do_upload('aadhar_back');        
                    $uploadError = $this->upload->display_errors();
                    if($uploadError){
                        $this->Az->redirect('VerifyBeneDocument/index/'.$bene_id, 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$uploadError.'</div>');
                    }
                    else
                    {
                        $fileData = $this->upload->data();
                        //get uploaded file path
                        $aadhar_back = substr($config['upload_path'] . $fileData['file_name'], 2);
                    }
                }

                $this->db->where('account_id',$domain_account_id);
                $this->db->where('user_id',$account_id);
                $this->db->where('bene_id',$bene_id);
                $this->db->update('new_payout_beneficiary',array('is_verified'=>1,'document_type'=>$post['document_type'],'passbook'=>$passbook,'pancard'=>$pancard,'aadhar_front'=>$aadhar_front,'aadhar_back'=>$aadhar_back));

                $this->Az->redirect('VerifyBeneDocument/index/'.$bene_id, 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Account Documet upload successfully and verified</div>');
            }
            else{

                $error = isset($responseData['message']) ? $responseData['message'] : 'Sorry!! something went wrong. Please try again.';

                $this->Az->redirect('VerifyBeneDocument/index/'.$bene_id, 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$error.'</div>');
            }
            

        }

    }
	
	
}
