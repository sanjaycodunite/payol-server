<?php 
class Nsdl extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
        $this->User->checkDistributorPermission();
        //$this->load->model('distributor/Cms_model');     
        //$this->lang->load('distributor/nsdl', 'english');
        $this->load->model('admin/Jwt_model');
        
    }

    public function index(){

        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        
        $siteUrl = base_url();      

        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'member_id' =>$loggedAccountID,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'nsdl/list'
        );
        $this->parser->parse('distributor/layout/column-1' , $data);
    
    
    }

    public function nsdlActiveAuth()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        //check for foem validation

        $activeService = $this->User->account_active_service($loggedUser['id']);
        if(!in_array(22, $activeService)){
            $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
        }


        $post = $this->input->post();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('title', 'Title', 'required|xss_clean');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('mode', 'Mode', 'required|xss_clean');
        $this->form_validation->set_rules('gender', 'Gender', 'required|xss_clean');
        //$this->form_validation->set_rules('email_id', 'Last Name', 'required|xss_clean');

        
        if ($this->form_validation->run() == FALSE) {
            
           $this->index();
        }

        else

        {

            $memberID = $loggedUser['id'];
        

            $activeService = $this->User->account_active_service($loggedUser['id']);
            if(!in_array(22, $activeService)){
                $this->Az->redirect('distributor/dashboard', 'system_message_error',lang('AUTHORIZE_ERROR'));
            }

            $member_package_id = $this->User->getMemberPackageID($memberID);
            
            // get commission
            $get_com_data = $this->db->get_where('tbl_nsdl_pancard_charge',array('account_id'=>$account_id,'package_id'=>$member_package_id))->row_array();
             
          
            $charge = isset($get_com_data['surcharge']) ? $get_com_data['surcharge'] : 0 ;

            

            
            $user_before_balance = $this->User->getMemberWalletBalanceSP($memberID);
            if($user_before_balance < $charge){
                
                $this->Az->redirect('distributor/nsdl', 'system_message_error',lang('WALLET_BALANCE_ERROR'));
                   

            }

            else

            {

                $transaction_id = time().rand(1111,9999);

                 $pan_redirect_url = 'https://www.'.$accountData['domain_url'].'/distributor/nsdl/index';
                
                $title = '';
                if($post['title'] == 'Mr/Shri')
                {
                    $title = 1;
                }
                else
                {
                    $title = 2;
                }
                $key = $accountData['paysprint_aeps_key'];
                $iv = $accountData['paysprint_aeps_iv'];;
                $datapost =array();

                $datapost['refid'] = $transaction_id;
                $datapost['title'] = $title;
                $datapost['firstname'] = $post['first_name'];
                $datapost['lastname'] = $post['last_name'];
                $datapost['middlename'] = $post['middle_name'];
                $datapost['mode'] = $post['mode'];
                $datapost['gender'] = $post['gender'];
                $datapost['email'] =$post['email'];               
                $datapost['redirect_url'] = 'https://www.purveyindia.com/distributor/nsdl/index/';

                $cipher  =   openssl_encrypt(json_encode($datapost,true), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
                $body=       base64_encode($cipher);
                $reqid = time().rand(1111,9999);

                log_message('debug', 'NSDL Api RequestID - '.$reqid);     

                $jwt_payload = array(
                    'timestamp'=>time(),
                    'partnerId'=>$accountData['paysprint_partner_id'],
                    'reqid'=>$reqid
                );

        log_message('debug', 'NSDL Auth Api jwt payload - '.json_encode($jwt_payload));

        $secret = $accountData['paysprint_secret_key'];

        $token = $this->Jwt_model->encode($jwt_payload,$secret);

            //$response = $this->Jwt_model->decode($token,$secret,array('HS256'));

        $header = [
            'Token:'.$token,
           // 'Authorisedkey:'.PAYSPRINT_AUTHORIZED_KEY            
        ];

         log_message('debug', 'NSDL  Api Header Data - '.json_encode($header));



         log_message('debug', 'NSDL  Api Body  Data - '.json_encode($datapost));



        $httpUrl = PAYSPRINT_NSDL_URL;
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
        

        log_message('debug', 'NSDL  Api Response  Data - '.json_encode($output));
      

        $responseData = json_decode($output,true);

        $apiData = array(
            'account_id' =>$account_id,
            'user_id' => $memberID,
            'api_url' => $httpUrl,
            'api_response' => $output,
            'redirect_url'=>$responseData['data']['url'],
            'encode' =>$responseData['data']['encdata'],
            'post_data' => json_encode($datapost),
            'created' => date('Y-m-d H:i:s'),
            'created_by' => 1
        );
        $this->db->insert('nsdl_api_response',$apiData);

        $record_id = $this->db->insert_id();

        if(isset($responseData['response_code']) && $responseData['response_code'] == 1)
        {

         $before_balance = $this->User->getMemberWalletBalanceSP($memberID);

                    
                    $after_balance = $before_balance - $charge;    

                    $wallet_data = array(
                        'account_id'          => $account_id,
                        'member_id'           => $memberID,    
                        'before_balance'      => $before_balance,
                        'amount'              => $charge,  
                        'after_balance'       => $after_balance,      
                        'status'              => 1,
                        'type'                => 2,      
                        'created'             => date('Y-m-d H:i:s'),      
                        'description'         => 'Nsdl Pan Card #'.$transaction_id.' Amount Debited.'
                    );

                    $this->db->insert('member_wallet',$wallet_data);
                    
                     $pan_data = array(
                        'account_id' =>$account_id,
                        'member_id' =>$memberID,
                        'first_name' =>$post['first_name'],
                        'last_name' =>$post['last_name'],
                        'middle_name'=>$post['middle_name'],
                        'mode' =>$post['mode'],
                        'gender' =>$post['gender'],
                        'email_id'=>$post['email'],    
                        'charge_amount' =>$charge,        
                        'transaction_id'=>$transaction_id,
                        'created'=>date('Y-m-d H:i:s'),
                        'created_by'=>$memberID
                      );

                     $this->db->insert('member_nsdl_transcation',$pan_data);
                     
       $this->Az->redirect('distributor/nsdl/redirect/'.$record_id, 'system_message_error',lang('PAN_SUCCESS'));
            
        }
        else
        {
           if(!in_array(22, $activeService)){
                $this->Az->redirect('distributor/nsdl', 'system_message_error',lang('MEMBER_ERROR'));
            }
        }

            }

        }
        
    }



    //redirect to pan site


     public function redirect($record_id = ''){

        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        
        $siteUrl = base_url();      

        $pan_data = $this->db->get_where('nsdl_api_response',array('id'=>$record_id))->row_array();



        $encode = $pan_data['encode'];
        $redirect_url = $pan_data['redirect_url'];

        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'loggedUser'  => $loggedUser,
            'member_id' =>$loggedAccountID,
            'encode' =>$encode,
            'redirect_url' =>$redirect_url,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'nsdl/nsdl-portal'
        );
        $this->parser->parse('distributor/layout/column-1' , $data);
    
    
    }


        







    
    
    
    
}