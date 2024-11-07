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

class Account_model extends CI_Model {
 				
  public function __construct() {
        parent::__construct();
    }

  public function save_account($post,$filePath)
  {
    $user_display_id = $this->User->generate_unique_admin_id($post['account_code']);


     $paysprint_callback_url = '';

    if($post['is_paysprint_aeps'] == 1)

    {
      $paysprint_callback_url = 'https://www.'.$post['domain_url'].'/syscallback/aepsOnBoardCallback';
    }


    $data = array(
      'account_type' => $post['account_type'],
      'image_path' => $filePath,                             
      'title'		=>$post['domain_name'],
      'domain_url'=>$post['domain_url'],
      'email'=>strtolower($post['email']),
      'name'=>$post['name'],
      'mobile'=>$post['mobile'],
      'is_api_active' => isset($post['is_api_active']) ? $post['is_api_active'] : 0,
      'package_id' => $post['package_id'],
      'dmt_username' => $post['dmt_username'],
      'dmt_password' => $post['dmt_password'],
      'dmt_pin' => $post['dmt_pin'],
      'dmt_token' => $post['dmt_token'],
      'dmt_key' => $post['dmt_key'],
      'dmt_access_code' => $post['dmt_access_code'],
      'dmt_institute_id' => $post['dmt_institute_id'],
      'van_prefix' => $post['van_prefix'],
      'van_ifsc' => $post['van_ifsc'],
      'aeps_username' => $post['aeps_username'],
      'aeps_password' => $post['aeps_password'],
      'aeps_supermerchant_id' => $post['aeps_supermerchant_id'],
      'aeps_secret_key' => $post['aeps_secret_key'],
      'aeps_certificate' => $post['aeps_certificate'],
      'cib_email' => $post['cib_email'],
      'cib_password' => $post['cib_password'],
      'cib_aggrid' => $post['cib_aggrid'],
      'cib_aggrname' => $post['cib_aggrname'],
      'cib_corpid' => $post['cib_corpid'],
      'cib_userid' => $post['cib_userid'],
      'cib_urn' => $post['cib_urn'],
      'cib_debitacc' => $post['cib_debitacc'],
      'cib_encryption_key' => $post['cib_encryption_key'],
      'cib_security_key' => $post['cib_security_key'],
      'cib_bank_certificate' => $post['cib_bank_certificate'],
      'cib_private_certificate' => $post['cib_private_certificate'],
      'upi_email' => $post['upi_email'],
      'upi_password' => $post['upi_password'],
      'upi_merchant_id' => $post['upi_merchant_id'],
      'upi_merchant_name' => $post['upi_merchant_name'],
      'upi_terminal_id' => $post['upi_terminal_id'],
      'upi_encryption_key' => $post['upi_encryption_key'],
      'upi_security_key' => $post['upi_security_key'],
      'upi_bank_certificate' => $post['upi_bank_certificate'],
      'upi_private_certificate' => $post['upi_private_certificate'],
      'upi_cash_email' => $post['upi_cash_email'],
      'upi_cash_password' => $post['upi_cash_password'],
      'upi_cash_merchant_id' => $post['upi_cash_merchant_id'],
      'upi_cash_merchant_name' => $post['upi_cash_merchant_name'],
      'upi_cash_terminal_id' => $post['upi_cash_terminal_id'],
      'upi_cash_encryption_key' => $post['upi_cash_encryption_key'],
      'upi_cash_security_key' => $post['upi_cash_security_key'],
      'upi_cash_bank_certificate' => $post['upi_cash_bank_certificate'],
      'upi_cash_private_certificate' => $post['upi_cash_private_certificate'],
      'current_account_user' => $post['current_account_user'],
      'current_account_passcode' => $post['current_account_passcode'],
      'is_default_api' => isset($post['is_default_api']) ? $post['is_default_api'] : 0,
      'is_wallet_deduction' => isset($post['is_wallet_deduction']) ? $post['is_wallet_deduction'] : 0,
      'is_disable_api_role' => isset($post['is_disable_api_role']) ? $post['is_disable_api_role'] : 0,
      'is_disable_user_role' => isset($post['is_disable_user_role']) ? $post['is_disable_user_role'] : 0,
      'is_app_notification' => isset($post['is_app_notification']) ? $post['is_app_notification'] : 0,
      'is_employe_panel' => isset($post['is_employe_panel']) ? $post['is_employe_panel'] : 0,
      'is_paysprint_aeps' => isset($post['is_paysprint_aeps']) ? $post['is_paysprint_aeps'] : 0,
      'is_tds_amount' => isset($post['is_tds_amount']) ? $post['is_tds_amount'] : 0,
      'is_payout_otp' => isset($post['is_payout_otp']) ? $post['is_payout_otp'] : 0,
      'is_move_wallet' => isset($post['is_move_wallet']) ? $post['is_move_wallet'] : 0,
      'notification_server_key' => isset($post['notification_server_key']) ? $post['notification_server_key'] : '',
      'account_code' => $post['account_code'],
      'sms_auth_key' => $post['sms_auth_key'],
      'sms_template_id' => $post['sms_template_id'],
      'sms_flow_id' => $post['sms_flow_id'],
      'sms_sender' => $post['sms_sender'],
      'sms_otp_template_id' => $post['sms_otp_template_id'],
      'sms_otp_flow_id' => $post['sms_otp_flow_id'],
      'instant_encryption_key' => $post['instant_encryption_key'],
      'instant_auth_code' => $post['instant_auth_code'],
      'instant_token' => $post['instant_token'],
      'instant_client_id' => $post['instant_client_id'],
      'instant_client_secret' => $post['instant_client_secret'],
      'instant_client_secret' => $post['instant_client_secret'],
      'instant_account_no' => $post['instant_account_no'],
      'is_cogent_instant_api' => isset($post['is_cogent_instant_api']) ? 1 : 0,
      'paysprint_partner_id' => $post['paysprint_partner_id'],
      'paysprint_aeps_key' => $post['paysprint_aeps_key'],
      'paysprint_aeps_iv' => $post['paysprint_aeps_iv'],
      'paysprint_secret_key' => $post['paysprint_secret_key'],
      'paysprint_authorized_key' => $post['paysprint_authorized_key'],
      'status'=>$post['is_active'],
      'created' => date('Y-m-d H:i:s'),
      'web_theme'=>$post['web_theme'],
      'is_generate_invoice' =>$post['is_generate_invoice'],
      'paysprint_callback_url' => $paysprint_callback_url

       

    );
    $this->db->insert('account',$data);
    $account_id = $this->db->insert_id();

    // save user credentials
    $userData = array(
      'account_id' => $account_id,
      'role_id' => 2,
      'user_code' => $user_display_id,
      'name' => $post['name'],
      'username' => trim($post['username']),
      'password' => do_hash($post['password']),
      'decode_password' => $post['password'],
      'transaction_password' => do_hash($post['password']),
      'decoded_transaction_password' => $post['password'],
      'email'=>strtolower($post['email']),
      'mobile'=>$post['mobile'],
      'is_active'=>$post['is_active'],
      'is_verified' => 1,
      'created_by' => 1,
      'creator_id' =>  1,   
      'created' => date('Y-m-d H:i:s')
    );
    $this->db->insert('users',$userData);


    // save account services
    if(isset($post['service_id']) && $post['service_id'])
    {
       foreach($post['service_id'] as $sid)
       {
          $serviceData = array(
            'account_id' => $account_id,
            'service_id' => $sid,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
          );
          $this->db->insert('account_services',$serviceData);
       }
    }

    // save account custom api
    if(isset($post['custom_api_id']) && $post['custom_api_id'])
    {
       
       foreach($post['custom_api_id'] as $sid)
       {
          
            $serviceData = array(
              'account_id' => $account_id,
              'api_id' => $sid,
              'status' => 1,
              'created' => date('Y-m-d H:i:s')
            );
            $this->db->insert('account_custom_api_permission',$serviceData);
       }
       
    }
    

    // save payment gateway
    if(isset($post['gateway_id']) && $post['gateway_id'])
    {
       foreach($post['gateway_id'] as $skey=>$sid)
       {
          $serviceData = array(
            'account_id' => $account_id,
            'gateway_id' => $sid,
            'gateway_key' => isset($post['gateway_key'][$skey]) ? $post['gateway_key'][$skey] : '',
            'gateway_secret' => isset($post['gateway_secret'][$skey]) ? $post['gateway_secret'][$skey] : '',
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
          );
          $this->db->insert('account_payment_gateway',$serviceData);
       }
    }


    // save default api
    $is_default_api = isset($post['is_default_api']) ? $post['is_default_api'] : 0;
    if($is_default_api)
    {
      // generate callback code
      $callbackCode = rand(111111,999999);
      
        $apiData = array(
          'account_id' => $account_id,
          'provider' => 'Codunite',
          'access_key' => '',
          'username' => $post['dmt_username'],
          'password' => $post['dmt_pin'],
          'request_base_url' => 'http://paymyrecharge.in/api/recharge.aspx?',
          'request_type' => 1,
          'response_type' => 1,
          'response_seperator' => ',',
          'get_balance_base_url' => 'http://paymyrecharge.in/api/balance.aspx?',
          'get_balance_request_type' => 1,
          'get_balance_response_type' => 1,
          'get_balance_response_seperator' => ',',
          'check_status_base_url' => '',
          'check_status_request_type' => 0,
          'check_status_response_type' => 0,
          'check_status_response_seperator' => '',
          'callback_base_url' => 'https://www.'.$post['domain_url'].'/syscallback/rechargeCallback/'.$callbackCode.'/?',
          'callback_response_type' => 0,
          'call_back_id' => $callbackCode,
          'status' => 1,
          'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('api',$apiData);
        $api_id = $this->db->insert_id();

        
        
        $paraData = array(
          'account_id' => $account_id,
          'api_id' => $api_id,
          'type' => 1,
          'is_access_key' => 0,
          'access_key' => '',
          'is_username' => 1,
          'username_key' => 'memberid',
          'is_password' => 1,
          'password_key' => 'pin',
        );
        $this->db->insert('api_parameter',$paraData);
        
        
        $keyValArray = array('number','operator','circle','amount','usertx');
        $keyValIdArray = array('5','2','3','1','4');
        
          
        foreach($keyValArray as $key=>$keyVal)
        {
           if($keyVal != '')
           {
             $getParaData = array(
              'account_id' => $account_id,
              'api_id' => $api_id,
              'para_key' => $keyVal,
              'value' => '#',
              'value_id' => isset($keyValIdArray[$key]) ? $keyValIdArray[$key] : '',
             );
             $this->db->insert('api_get_parameter',$getParaData);
           }
        }
          
           
        
        
        
       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 1,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 2,
        'success_val' => 'success,Success',
        'failed_val' => 'failure,Failure,failed,Failed',
        'pending_val' => 'pending,Pending',
       );
       $this->db->insert('api_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 3,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 4,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 5,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_str_response',$getParaData);
                 


        
        $paraData = array(
          'account_id' => $account_id,
          'api_id' => $api_id,
          'type' => 1,
          'is_access_key' => 0,
          'access_key' => '',
          'is_username' => 1,
          'username_key' => 'memberid',
          'is_password' => 1,
          'password_key' => 'pin',
        );
        $this->db->insert('api_get_balance_parameter',$paraData);
        

        
       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 7,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_get_balance_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 2,
        'success_val' => 'Success',
        'failed_val' => 'Failed',
        'pending_val' => 'Pending',
       );
       $this->db->insert('api_get_balance_str_response',$getParaData);
                 
      // save operator
      $operatorList = $this->db->order_by('type','asc')->get('operator')->result_array();
      if($operatorList)
      {
        foreach($operatorList as $key=>$list)
        {
           $optData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'opt_id' => $list['id'],
            'opt_code' => $list['operator_code']
          );
          $this->db->insert('api_operator',$optData);
        }
      }
        
    }

    return true;

  }



  public function updateAccount($post,$filePath)
  { 


    $paysprint_callback_url = '';

    if($post['is_paysprint_aeps'] == 1)

    {
      $paysprint_callback_url = 'https://www.'.$post['domain_url'].'/syscallback/aepsOnBoardCallback';
    }

   
    $data = array(
      'account_type' => $post['account_type'],
      'title'   =>$post['domain_name'],
      'domain_url'=>$post['domain_url'],
      'email'=>strtolower($post['email']),
      'name'=>$post['name'],
      'mobile'=>$post['mobile'],
      'is_api_active' => isset($post['is_api_active']) ? $post['is_api_active'] : 0,
      'is_wallet_deduction' => isset($post['is_wallet_deduction']) ? $post['is_wallet_deduction'] : 0,
      'is_disable_api_role' => isset($post['is_disable_api_role']) ? $post['is_disable_api_role'] : 0,
      'is_disable_user_role' => isset($post['is_disable_user_role']) ? $post['is_disable_user_role'] : 0,
      'is_app_notification' => isset($post['is_app_notification']) ? $post['is_app_notification'] : 0,
      'is_employe_panel' => isset($post['is_employe_panel']) ? $post['is_employe_panel'] : 0,
      'is_paysprint_aeps' => isset($post['is_paysprint_aeps']) ? $post['is_paysprint_aeps'] : 0,
      'is_tds_amount' => isset($post['is_tds_amount']) ? $post['is_tds_amount'] : 0,
      'is_payout_otp' => isset($post['is_payout_otp']) ? $post['is_payout_otp'] : 0,
      'is_move_wallet' => isset($post['is_move_wallet']) ? $post['is_move_wallet'] : 0,
      'is_auto_bank_settlement' => isset($post['is_auto_bank_settlement']) ? $post['is_auto_bank_settlement'] : 0,
      'notification_server_key' => isset($post['notification_server_key']) ? $post['notification_server_key'] : '',
      'package_id' => $post['package_id'],
      'dmt_username' => $post['dmt_username'],
      'dmt_password' => $post['dmt_password'],
      'dmt_pin' => $post['dmt_pin'],
      'dmt_token' => $post['dmt_token'],
      'dmt_key' => $post['dmt_key'],
      'dmt_access_code' => $post['dmt_access_code'],
      'dmt_institute_id' => $post['dmt_institute_id'],
      'van_prefix' => $post['van_prefix'],
      'van_ifsc' => $post['van_ifsc'],
      'aeps_username' => $post['aeps_username'],
      'aeps_password' => $post['aeps_password'],
      'aeps_supermerchant_id' => $post['aeps_supermerchant_id'],
      'aeps_secret_key' => $post['aeps_secret_key'],
      'aeps_certificate' => $post['aeps_certificate'],
      'cib_email' => $post['cib_email'],
      'cib_password' => $post['cib_password'],
      'cib_aggrid' => $post['cib_aggrid'],
      'cib_aggrname' => $post['cib_aggrname'],
      'cib_corpid' => $post['cib_corpid'],
      'cib_userid' => $post['cib_userid'],
      'cib_urn' => $post['cib_urn'],
      'cib_debitacc' => $post['cib_debitacc'],
      'cib_encryption_key' => $post['cib_encryption_key'],
      'cib_security_key' => $post['cib_security_key'],
      'cib_bank_certificate' => $post['cib_bank_certificate'],
      'cib_private_certificate' => $post['cib_private_certificate'],
      'upi_email' => $post['upi_email'],
      'upi_password' => $post['upi_password'],
      'upi_merchant_id' => $post['upi_merchant_id'],
      'upi_merchant_name' => $post['upi_merchant_name'],
      'upi_terminal_id' => $post['upi_terminal_id'],
      'upi_encryption_key' => $post['upi_encryption_key'],
      'upi_security_key' => $post['upi_security_key'],
      'upi_bank_certificate' => $post['upi_bank_certificate'],
      'upi_private_certificate' => $post['upi_private_certificate'],
      'upi_cash_email' => $post['upi_cash_email'],
      'upi_cash_password' => $post['upi_cash_password'],
      'upi_cash_merchant_id' => $post['upi_cash_merchant_id'],
      'upi_cash_merchant_name' => $post['upi_cash_merchant_name'],
      'upi_cash_terminal_id' => $post['upi_cash_terminal_id'],
      'upi_cash_encryption_key' => $post['upi_cash_encryption_key'],
      'upi_cash_security_key' => $post['upi_cash_security_key'],
      'upi_cash_bank_certificate' => $post['upi_cash_bank_certificate'],
      'upi_cash_private_certificate' => $post['upi_cash_private_certificate'],
      'current_account_user' => $post['current_account_user'],
      'current_account_passcode' => $post['current_account_passcode'],
      'account_code' => $post['account_code'],
      'sms_auth_key' => $post['sms_auth_key'],
      'sms_template_id' => $post['sms_template_id'],
      'sms_flow_id' => $post['sms_flow_id'],
      'sms_sender' => $post['sms_sender'],
      'sms_otp_template_id' => $post['sms_otp_template_id'],
      'sms_otp_flow_id' => $post['sms_otp_flow_id'],
      'instant_encryption_key' => $post['instant_encryption_key'],
      'instant_auth_code' => $post['instant_auth_code'],
      'instant_token' => $post['instant_token'],
      'instant_client_id' => $post['instant_client_id'],
      'instant_client_secret' => $post['instant_client_secret'],
      'instant_account_no' => $post['instant_account_no'],
      'is_cogent_instant_api' => isset($post['is_cogent_instant_api']) ? 1 : 0,
      'paysprint_partner_id' => $post['paysprint_partner_id'],
      'paysprint_aeps_key' => $post['paysprint_aeps_key'],
      'paysprint_aeps_iv' => $post['paysprint_aeps_iv'],
      'paysprint_secret_key' => $post['paysprint_secret_key'],
      'paysprint_authorized_key' => $post['paysprint_authorized_key'],
      'status'=>$post['is_active'],
      'updated' => date('Y-m-d H:i:s'),
      'web_theme'=>$post['web_theme'],
      'is_generate_invoice' =>$post['is_generate_invoice'],
       'is_otp_login' =>$post['is_otp_login'],
      'paysprint_callback_url' => $paysprint_callback_url
    );
    if($filePath)
    {
      $data['image_path'] = $filePath;
    }
    $this->db->where('id',$post['id']);
    $this->db->update('account',$data);
    $account_id = $post['id'];

    // save user credentials
    $userData = array(
      'username' => trim($post['username']),
      'is_active'=>$post['is_active'],
      'updated' => date('Y-m-d H:i:s')
    );
    if($post['password'])
    {
      $userData['password'] = do_hash($post['password']);
      $userData['decode_password'] = $post['password'];
      $userData['transaction_password'] = do_hash($post['password']);
      $userData['decoded_transaction_password'] = $post['password'];
    }
    $this->db->where('account_id',$account_id);
    $this->db->where('role_id',2);
    $this->db->update('users',$userData);

    // save account services
    if(isset($post['service_id']) && $post['service_id'])
    {
       $this->db->where('account_id',$account_id);
       $this->db->update('account_services',array('status'=>0));

       

       foreach($post['service_id'] as $sid)
       {
          // check service id exits or not
          $chk_data = $this->db->get_where('account_services',array('account_id'=>$account_id,'service_id'=>$sid))->num_rows();
          if($chk_data)
          {
            $this->db->where('account_id',$account_id);
            $this->db->where('service_id',$sid);
            $this->db->update('account_services',array('status'=>1));
          }
          else
          {
            $serviceData = array(
              'account_id' => $account_id,
              'service_id' => $sid,
              'status' => 1,
              'created' => date('Y-m-d H:i:s')
            );
            $this->db->insert('account_services',$serviceData);
          }

          
       }
       $this->db->where('account_id',$account_id);
       $this->db->where_not_in('service_id',$post['service_id']);
       $this->db->update('account_user_services',array('status'=>0));
    }
    else
    {
       $this->db->where('account_id',$account_id);
       $this->db->update('account_services',array('status'=>0));

       $this->db->where('account_id',$account_id);
       $this->db->update('account_user_services',array('status'=>0));
    }

    // save account custom api
    if(isset($post['custom_api_id']) && $post['custom_api_id'])
    {
       $this->db->where('account_id',$account_id);
       $this->db->update('account_custom_api_permission',array('status'=>0));

       

       foreach($post['custom_api_id'] as $sid)
       {
          // check service id exits or not
          $chk_data = $this->db->get_where('account_custom_api_permission',array('account_id'=>$account_id,'api_id'=>$sid))->num_rows();
          if($chk_data)
          {
            $this->db->where('account_id',$account_id);
            $this->db->where('api_id',$sid);
            $this->db->update('account_custom_api_permission',array('status'=>1));
          }
          else
          {
            $serviceData = array(
              'account_id' => $account_id,
              'api_id' => $sid,
              'status' => 1,
              'created' => date('Y-m-d H:i:s')
            );
            $this->db->insert('account_custom_api_permission',$serviceData);
          }

          
       }
       $this->db->where('account_id',$account_id);
       $this->db->where_not_in('api_id',$post['custom_api_id']);
       $this->db->update('account_custom_api_permission',array('status'=>0));
    }
    else
    {
       $this->db->where('account_id',$account_id);
       $this->db->update('account_custom_api_permission',array('status'=>0));
    }

    $this->db->where('account_id',$account_id);
    $this->db->update('account_payment_gateway',array('status'=>0));
    // save payment gateway
    if(isset($post['gateway_id']) && $post['gateway_id'])
    {

       foreach($post['gateway_id'] as $skey=>$sid)
       {
        // check service id exits or not
          $chk_data = $this->db->get_where('account_payment_gateway',array('account_id'=>$account_id,'gateway_id'=>$sid))->num_rows();
          if($chk_data)
          {
            $gateway_key = isset($post['gateway_key'][$skey]) ? $post['gateway_key'][$skey] : '';
            $gateway_secret = isset($post['gateway_secret'][$skey]) ? $post['gateway_secret'][$skey] : '';
            $this->db->where('account_id',$account_id);
            $this->db->where('gateway_id',$sid);
            $this->db->update('account_payment_gateway',array('status'=>1,'gateway_key'=>$gateway_key,'gateway_secret'=>$gateway_secret));
          }
          else
          {
          $serviceData = array(
            'account_id' => $account_id,
            'gateway_id' => $sid,
            'gateway_key' => isset($post['gateway_key'][$skey]) ? $post['gateway_key'][$skey] : '',
            'gateway_secret' => isset($post['gateway_secret'][$skey]) ? $post['gateway_secret'][$skey] : '',
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
          );
          $this->db->insert('account_payment_gateway',$serviceData);
          }
       }
    }

    return true;
  }



}




?>