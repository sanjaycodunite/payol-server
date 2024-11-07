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

    public function saveApi($post)
    {       
        
        $account_id = SUPERADMIN_ACCOUNT_ID;
        $apiData = array(
          'account_id' => $account_id,
          'provider' => $post['provider'],
          'access_key' => $post['access_key'],
          'username' => $post['username'],
          'password' => $post['password'],
          'request_base_url' => $post['request_base_url'],
          'request_type' => $post['request_type'],
          'response_type' => $post['response_type'],
          'response_seperator' => $post['response_seperator'],
          'get_balance_base_url' => $post['get_balance_base_url'],
          'get_balance_request_type' => $post['get_balance_request_type'],
          'get_balance_response_type' => $post['get_balance_response_type'],
          'get_balance_response_seperator' => $post['get_balance_response_seperator'],
          'check_status_base_url' => $post['check_status_base_url'],
          'check_status_request_type' => $post['check_status_request_type'],
          'check_status_response_type' => $post['check_status_response_type'],
          'check_status_response_seperator' => $post['check_status_response_seperator'],
          'callback_base_url' => $post['callback_base_url'],
          'callback_response_type' => $post['callback_response_type'],
          'call_back_id' => $post['callbackCode'],
          'status' => 1,
          'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('api',$apiData);
        $api_id = $this->db->insert_id();

        //save header parameter
        $headerParaData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['header_is_access_key']) ? $post['header_is_access_key'] : 0,
            'access_key' => $post['header_access_key'],
            'is_username' => isset($post['header_is_username']) ? $post['header_is_username'] : 0,
            'username_key' => $post['header_username'],
            'is_password' => isset($post['header_is_password']) ? $post['header_is_password'] : 0,
            'password_key' => $post['header_password'],
          );
          $this->db->insert('api_header_parameter',$headerParaData);

        if($post['request_type'] == 1)
        {
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_is_access_key']) ? $post['get_is_access_key'] : 0,
            'access_key' => $post['get_access_key'],
            'is_username' => isset($post['get_is_username']) ? $post['get_is_username'] : 0,
            'username_key' => $post['get_username'],
            'is_password' => isset($post['get_is_password']) ? $post['get_is_password'] : 0,
            'password_key' => $post['get_password'],
          );
          $this->db->insert('api_parameter',$paraData);
        }
        elseif($post['request_type'] == 2)
        {
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['post_is_access_key']) ? $post['post_is_access_key'] : 0,
            'access_key' => $post['post_access_key'],
            'is_username' => isset($post['post_is_username']) ? $post['post_is_username'] : 0,
            'username_key' => $post['post_username'],
            'is_password' => isset($post['post_is_password']) ? $post['post_is_password'] : 0,
            'password_key' => $post['post_password'],
          );
          $this->db->insert('api_parameter',$paraData);
        }
        elseif($post['request_type'] == 3)
        {
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_is_access_key']) ? $post['get_is_access_key'] : 0,
            'access_key' => $post['get_access_key'],
            'is_username' => isset($post['get_is_username']) ? $post['get_is_username'] : 0,
            'username_key' => $post['get_username'],
            'is_password' => isset($post['get_is_password']) ? $post['get_is_password'] : 0,
            'password_key' => $post['get_password'],
          );
          $this->db->insert('api_parameter',$paraData);
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['post_is_access_key']) ? $post['post_is_access_key'] : 0,
            'access_key' => $post['post_access_key'],
            'is_username' => isset($post['post_is_username']) ? $post['post_is_username'] : 0,
            'username_key' => $post['post_username'],
            'is_password' => isset($post['post_is_password']) ? $post['post_is_password'] : 0,
            'password_key' => $post['post_password'],
          );
          $this->db->insert('api_parameter',$paraData);
        }

        if($post['request_type'] == 1)
        {
          if($post['get_para_key'])
          {
            foreach($post['get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_para_val'][$key]) ? $post['get_para_val'][$key] : '',
                  'value_id' => isset($post['get_para_key_val'][$key]) ? $post['get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['request_type'] == 2)
        {
          if($post['post_para_key'])
          {
            foreach($post['post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['post_para_val'][$key]) ? $post['post_para_val'][$key] : '',
                  'value_id' => isset($post['post_para_key_val'][$key]) ? $post['post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_post_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['request_type'] == 3)
        {
          if($post['get_para_key'])
          {
            foreach($post['get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_para_val'][$key]) ? $post['get_para_val'][$key] : '',
                  'value_id' => isset($post['get_para_key_val'][$key]) ? $post['get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_parameter',$getParaData);
               }
            }
          }
          if($post['post_para_key'])
          {
            foreach($post['post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['post_para_val'][$key]) ? $post['post_para_val'][$key] : '',
                  'value_id' => isset($post['post_para_key_val'][$key]) ? $post['post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_post_parameter',$getParaData);
               }
            }
          }
           
        }

        if($post['response_type'] == 1)
        {
           if($post['str_res_type'])
            {
              foreach($post['str_res_type'] as $key=>$keyVal)
              {
                 if($keyVal != 0)
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'value_id' => $keyVal,
                    'success_val' => isset($post['str_res_status_val'][$key][0]) ? $post['str_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['str_res_status_val'][$key][1]) ? $post['str_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['str_res_status_val'][$key][2]) ? $post['str_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_str_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['response_type'] == 2)
        {
           if($post['xml_res_key'])
            {
              foreach($post['xml_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['xml_res_type'][$key]) ? $post['xml_res_type'][$key] : 0,
                    'success_val' => isset($post['xml_res_status_val'][$key][0]) ? $post['xml_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['xml_res_status_val'][$key][1]) ? $post['xml_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['xml_res_status_val'][$key][2]) ? $post['xml_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_xml_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['response_type'] == 3)
        {
           if($post['json_res_key'])
            {
              foreach($post['json_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['json_res_type'][$key]) ? $post['json_res_type'][$key] : 0,
                    'success_val' => isset($post['json_res_status_val'][$key][0]) ? $post['json_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['json_res_status_val'][$key][1]) ? $post['json_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['json_res_status_val'][$key][2]) ? $post['json_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_json_response',$getParaData);
                 }
              }
            }
        }

        // save get balance api parameters
        //save header parameter
        $headerParaData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_balance_header_is_access_key']) ? $post['get_balance_header_is_access_key'] : 0,
            'access_key' => $post['get_balance_header_access_key'],
            'is_username' => isset($post['get_balance_header_is_username']) ? $post['get_balance_header_is_username'] : 0,
            'username_key' => $post['get_balance_header_username'],
            'is_password' => isset($post['get_balance_header_is_password']) ? $post['get_balance_header_is_password'] : 0,
            'password_key' => $post['get_balance_header_password'],
          );
          $this->db->insert('api_get_balance_header_parameter',$headerParaData);
        if($post['get_balance_request_type'] == 1)
        {
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_balance_get_is_access_key']) ? $post['get_balance_get_is_access_key'] : 0,
            'access_key' => $post['get_balance_get_access_key'],
            'is_username' => isset($post['get_balance_get_is_username']) ? $post['get_balance_get_is_username'] : 0,
            'username_key' => $post['get_balance_get_username'],
            'is_password' => isset($post['get_balance_get_is_password']) ? $post['get_balance_get_is_password'] : 0,
            'password_key' => $post['get_balance_get_password'],
          );
          $this->db->insert('api_get_balance_parameter',$paraData);
        }
        elseif($post['get_balance_request_type'] == 2)
        {
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['get_balance_get_is_access_key']) ? $post['get_balance_get_is_access_key'] : 0,
            'access_key' => $post['get_balance_get_access_key'],
            'is_username' => isset($post['get_balance_get_is_username']) ? $post['get_balance_get_is_username'] : 0,
            'username_key' => $post['get_balance_get_username'],
            'is_password' => isset($post['get_balance_get_is_password']) ? $post['get_balance_get_is_password'] : 0,
            'password_key' => $post['get_balance_get_password'],
          );
          $this->db->insert('api_get_balance_parameter',$paraData);
        }
        elseif($post['get_balance_request_type'] == 3)
        {
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_balance_get_is_access_key']) ? $post['get_balance_get_is_access_key'] : 0,
            'access_key' => $post['get_balance_get_access_key'],
            'is_username' => isset($post['get_balance_get_is_username']) ? $post['get_balance_get_is_username'] : 0,
            'username_key' => $post['get_balance_get_username'],
            'is_password' => isset($post['get_balance_get_is_password']) ? $post['get_balance_get_is_password'] : 0,
            'password_key' => $post['get_balance_get_password'],
          );
          $this->db->insert('api_get_balance_parameter',$paraData);
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['get_balance_get_is_access_key']) ? $post['get_balance_get_is_access_key'] : 0,
            'access_key' => $post['get_balance_get_access_key'],
            'is_username' => isset($post['get_balance_get_is_username']) ? $post['get_balance_get_is_username'] : 0,
            'username_key' => $post['get_balance_get_username'],
            'is_password' => isset($post['get_balance_get_is_password']) ? $post['get_balance_get_is_password'] : 0,
            'password_key' => $post['get_balance_get_password'],
          );
          $this->db->insert('api_get_balance_parameter',$paraData);
        }

        if($post['get_balance_request_type'] == 1)
        {
          
          if($post['get_balance_get_para_key'])
          {
            foreach($post['get_balance_get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_balance_get_para_val'][$key]) ? $post['get_balance_get_para_val'][$key] : '',
                  'value_id' => isset($post['get_balance_get_para_key_val'][$key]) ? $post['get_balance_get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_balance_get_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['get_balance_request_type'] == 2)
        {
          
          if($post['get_balance_post_para_key'])
          {
            foreach($post['get_balance_post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_balance_post_para_val'][$key]) ? $post['get_balance_post_para_val'][$key] : '',
                  'value_id' => isset($post['get_balance_post_para_key_val'][$key]) ? $post['get_balance_post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_balance_post_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['get_balance_request_type'] == 3)
        {
          
          if($post['get_balance_get_para_key'])
          {
            foreach($post['get_balance_get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_balance_get_para_val'][$key]) ? $post['get_balance_get_para_val'][$key] : '',
                  'value_id' => isset($post['get_balance_get_para_key_val'][$key]) ? $post['get_balance_get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_balance_get_parameter',$getParaData);
               }
            }
          }
          if($post['get_balance_post_para_key'])
          {
            foreach($post['get_balance_post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_balance_post_para_val'][$key]) ? $post['get_balance_post_para_val'][$key] : '',
                  'value_id' => isset($post['get_balance_post_para_key_val'][$key]) ? $post['get_balance_post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_balance_post_parameter',$getParaData);
               }
            }
          }
           
        }

        if($post['get_balance_response_type'] == 1)
        {
          
           if($post['get_balance_str_res_type'])
            {
              foreach($post['get_balance_str_res_type'] as $key=>$keyVal)
              {
                 if($keyVal != 0)
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'value_id' => $keyVal,
                    'success_val' => isset($post['get_balance_str_res_status_val'][$key][0]) ? $post['get_balance_str_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['get_balance_str_res_status_val'][$key][1]) ? $post['get_balance_str_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['get_balance_str_res_status_val'][$key][2]) ? $post['get_balance_str_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_get_balance_str_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['get_balance_response_type'] == 2)
        {
          
           if($post['get_balance_xml_res_key'])
            {
              foreach($post['get_balance_xml_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['get_balance_xml_res_type'][$key]) ? $post['get_balance_xml_res_type'][$key] : 0,
                    'success_val' => isset($post['get_balance_xml_res_status_val'][$key][0]) ? $post['get_balance_xml_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['get_balance_xml_res_status_val'][$key][1]) ? $post['get_balance_xml_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['get_balance_xml_res_status_val'][$key][2]) ? $post['get_balance_xml_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_get_balance_xml_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['get_balance_response_type'] == 3)
        {
          
           if($post['get_balance_json_res_key'])
            {
              foreach($post['get_balance_json_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['get_balance_json_res_type'][$key]) ? $post['get_balance_json_res_type'][$key] : 0,
                    'success_val' => isset($post['get_balance_json_res_status_val'][$key][0]) ? $post['get_balance_json_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['get_balance_json_res_status_val'][$key][1]) ? $post['get_balance_json_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['get_balance_json_res_status_val'][$key][2]) ? $post['get_balance_json_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_get_balance_json_response',$getParaData);
                 }
              }
            }
        }


        // save check status api parameters
        if($post['check_status_request_type'] == 1)
        {
          
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['check_status_get_is_access_key']) ? $post['check_status_get_is_access_key'] : 0,
            'access_key' => $post['check_status_get_access_key'],
            'is_username' => isset($post['check_status_get_is_username']) ? $post['check_status_get_is_username'] : 0,
            'username_key' => $post['check_status_get_username'],
            'is_password' => isset($post['check_status_get_is_password']) ? $post['check_status_get_is_password'] : 0,
            'password_key' => $post['check_status_get_password'],
          );
          $this->db->insert('api_check_status_parameter',$paraData);
        }
        elseif($post['check_status_request_type'] == 2)
        {
          
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['check_status_get_is_access_key']) ? $post['check_status_get_is_access_key'] : 0,
            'access_key' => $post['check_status_get_access_key'],
            'is_username' => isset($post['check_status_get_is_username']) ? $post['check_status_get_is_username'] : 0,
            'username_key' => $post['check_status_get_username'],
            'is_password' => isset($post['check_status_get_is_password']) ? $post['check_status_get_is_password'] : 0,
            'password_key' => $post['check_status_get_password'],
          );
          $this->db->insert('api_check_status_parameter',$paraData);
        }
        elseif($post['check_status_request_type'] == 3)
        {
          
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['check_status_get_is_access_key']) ? $post['check_status_get_is_access_key'] : 0,
            'access_key' => $post['check_status_get_access_key'],
            'is_username' => isset($post['check_status_get_is_username']) ? $post['check_status_get_is_username'] : 0,
            'username_key' => $post['check_status_get_username'],
            'is_password' => isset($post['check_status_get_is_password']) ? $post['check_status_get_is_password'] : 0,
            'password_key' => $post['check_status_get_password'],
          );
          $this->db->insert('api_check_status_parameter',$paraData);
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['check_status_get_is_access_key']) ? $post['check_status_get_is_access_key'] : 0,
            'access_key' => $post['check_status_get_access_key'],
            'is_username' => isset($post['check_status_get_is_username']) ? $post['check_status_get_is_username'] : 0,
            'username_key' => $post['check_status_get_username'],
            'is_password' => isset($post['check_status_get_is_password']) ? $post['check_status_get_is_password'] : 0,
            'password_key' => $post['check_status_get_password'],
          );
          $this->db->insert('api_check_status_parameter',$paraData);
        }

        if($post['check_status_request_type'] == 1)
        {
          

          if($post['check_status_get_para_key'])
          {
            foreach($post['check_status_get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['check_status_get_para_val'][$key]) ? $post['check_status_get_para_val'][$key] : '',
                  'value_id' => isset($post['check_status_get_para_key_val'][$key]) ? $post['check_status_get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_check_status_get_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['check_status_request_type'] == 2)
        {
          
          
          if($post['check_status_post_para_key'])
          {
            foreach($post['check_status_post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['check_status_post_para_val'][$key]) ? $post['check_status_post_para_val'][$key] : '',
                  'value_id' => isset($post['check_status_post_para_key_val'][$key]) ? $post['check_status_post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_check_status_post_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['check_status_request_type'] == 3)
        {
          
          if($post['get_balance_get_para_key'])
          {
            foreach($post['check_status_get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['check_status_get_para_val'][$key]) ? $post['check_status_get_para_val'][$key] : '',
                  'value_id' => isset($post['check_status_get_para_key_val'][$key]) ? $post['check_status_get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_check_status_get_parameter',$getParaData);
               }
            }
          }
          if($post['check_status_post_para_key'])
          {
            foreach($post['check_status_post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['check_status_post_para_val'][$key]) ? $post['check_status_post_para_val'][$key] : '',
                  'value_id' => isset($post['check_status_post_para_key_val'][$key]) ? $post['check_status_post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_check_status_post_parameter',$getParaData);
               }
            }
          }
           
        }

        if($post['check_status_response_type'] == 1)
        {
          
           if($post['check_status_str_res_type'])
            {
              foreach($post['check_status_str_res_type'] as $key=>$keyVal)
              {
                 if($keyVal != 0)
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'value_id' => $keyVal,
                    'success_val' => isset($post['check_status_str_res_status_val'][$key][0]) ? $post['check_status_str_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['check_status_str_res_status_val'][$key][1]) ? $post['check_status_str_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['check_status_str_res_status_val'][$key][2]) ? $post['check_status_str_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_check_status_str_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['check_status_response_type'] == 2)
        {
          
           if($post['check_status_xml_res_key'])
            {
              foreach($post['check_status_xml_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['check_status_xml_res_type'][$key]) ? $post['check_status_xml_res_type'][$key] : 0,
                    'success_val' => isset($post['check_status_xml_res_status_val'][$key][0]) ? $post['check_status_xml_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['check_status_xml_res_status_val'][$key][1]) ? $post['check_status_xml_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['check_status_xml_res_status_val'][$key][2]) ? $post['check_status_xml_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_check_status_xml_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['check_status_response_type'] == 3)
        {
          
           if($post['check_status_json_res_key'])
            {
              foreach($post['check_status_json_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['check_status_json_res_type'][$key]) ? $post['check_status_json_res_type'][$key] : 0,
                    'success_val' => isset($post['check_status_json_res_status_val'][$key][0]) ? $post['check_status_json_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['check_status_json_res_status_val'][$key][1]) ? $post['check_status_json_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['check_status_json_res_status_val'][$key][2]) ? $post['check_status_json_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_check_status_json_response',$getParaData);
                 }
              }
            }
        }

        
         if($post['call_back_res_key'])
          {
            foreach($post['call_back_res_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value_id' => isset($post['call_back_res_type'][$key]) ? $post['call_back_res_type'][$key] : 0,
                  'success_val' => isset($post['call_back_res_status_val'][$key][0]) ? $post['call_back_res_status_val'][$key][0] : '',
                  'failed_val' => isset($post['call_back_res_status_val'][$key][1]) ? $post['call_back_res_status_val'][$key][1] : '',
                  'pending_val' => isset($post['call_back_res_status_val'][$key][2]) ? $post['call_back_res_status_val'][$key][2] : '',
                 );
                 $this->db->insert('api_call_back_response',$getParaData);
               }
            }
          }

        return true;

    }


    public function updateApi($post)
    {       
        $api_id = $post['api_id'];
        $account_id = SUPERADMIN_ACCOUNT_ID;
        $apiData = array(
          'provider' => $post['provider'],
          'access_key' => $post['access_key'],
          'username' => $post['username'],
          'password' => $post['password'],
          'request_base_url' => $post['request_base_url'],
          'request_type' => $post['request_type'],
          'response_type' => $post['response_type'],
          'response_seperator' => $post['response_seperator'],
          'get_balance_base_url' => $post['get_balance_base_url'],
          'get_balance_request_type' => $post['get_balance_request_type'],
          'get_balance_response_type' => $post['get_balance_response_type'],
          'get_balance_response_seperator' => $post['get_balance_response_seperator'],
          'check_status_base_url' => $post['check_status_base_url'],
          'check_status_request_type' => $post['check_status_request_type'],
          'check_status_response_type' => $post['check_status_response_type'],
          'check_status_response_seperator' => $post['check_status_response_seperator'],
          'callback_base_url' => $post['callback_base_url'],
          'callback_response_type' => $post['callback_response_type'],
          'status' => 1,
          'updated' => date('Y-m-d H:i:s')
        );
        $this->db->where('id',$api_id);
        $this->db->where('account_id',$account_id);
        $this->db->update('api',$apiData);
        
        $this->db->where('api_id',$api_id);
        $this->db->where('account_id',$account_id);
        $this->db->delete('api_header_parameter');
        //save header parameter
        $headerParaData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['header_is_access_key']) ? $post['header_is_access_key'] : 0,
            'access_key' => $post['header_access_key'],
            'is_username' => isset($post['header_is_username']) ? $post['header_is_username'] : 0,
            'username_key' => $post['header_username'],
            'is_password' => isset($post['header_is_password']) ? $post['header_is_password'] : 0,
            'password_key' => $post['header_password'],
          );
          $this->db->insert('api_header_parameter',$headerParaData);

        if($post['request_type'] == 1)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_parameter');
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_is_access_key']) ? $post['get_is_access_key'] : 0,
            'access_key' => $post['get_access_key'],
            'is_username' => isset($post['get_is_username']) ? $post['get_is_username'] : 0,
            'username_key' => $post['get_username'],
            'is_password' => isset($post['get_is_password']) ? $post['get_is_password'] : 0,
            'password_key' => $post['get_password'],
          );
          $this->db->insert('api_parameter',$paraData);
        }
        elseif($post['request_type'] == 2)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_parameter');
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['post_is_access_key']) ? $post['post_is_access_key'] : 0,
            'access_key' => $post['post_access_key'],
            'is_username' => isset($post['post_is_username']) ? $post['post_is_username'] : 0,
            'username_key' => $post['post_username'],
            'is_password' => isset($post['post_is_password']) ? $post['post_is_password'] : 0,
            'password_key' => $post['post_password'],
          );
          $this->db->insert('api_parameter',$paraData);
        }
        elseif($post['request_type'] == 3)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_parameter');
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_is_access_key']) ? $post['get_is_access_key'] : 0,
            'access_key' => $post['get_access_key'],
            'is_username' => isset($post['get_is_username']) ? $post['get_is_username'] : 0,
            'username_key' => $post['get_username'],
            'is_password' => isset($post['get_is_password']) ? $post['get_is_password'] : 0,
            'password_key' => $post['get_password'],
          );
          $this->db->insert('api_parameter',$paraData);
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['post_is_access_key']) ? $post['post_is_access_key'] : 0,
            'access_key' => $post['post_access_key'],
            'is_username' => isset($post['post_is_username']) ? $post['post_is_username'] : 0,
            'username_key' => $post['post_username'],
            'is_password' => isset($post['post_is_password']) ? $post['post_is_password'] : 0,
            'password_key' => $post['post_password'],
          );
          $this->db->insert('api_parameter',$paraData);
        }

        if($post['request_type'] == 1)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_parameter');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_post_parameter');

          if($post['get_para_key'])
          {
            foreach($post['get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_para_val'][$key]) ? $post['get_para_val'][$key] : '',
                  'value_id' => isset($post['get_para_key_val'][$key]) ? $post['get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['request_type'] == 2)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_parameter');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_post_parameter');
          if($post['post_para_key'])
          {
            foreach($post['post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['post_para_val'][$key]) ? $post['post_para_val'][$key] : '',
                  'value_id' => isset($post['post_para_key_val'][$key]) ? $post['post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_post_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['request_type'] == 3)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_parameter');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_post_parameter');
          if($post['get_para_key'])
          {
            foreach($post['get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_para_val'][$key]) ? $post['get_para_val'][$key] : '',
                  'value_id' => isset($post['get_para_key_val'][$key]) ? $post['get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_parameter',$getParaData);
               }
            }
          }
          if($post['post_para_key'])
          {
            foreach($post['post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['post_para_val'][$key]) ? $post['post_para_val'][$key] : '',
                  'value_id' => isset($post['post_para_key_val'][$key]) ? $post['post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_post_parameter',$getParaData);
               }
            }
          }
           
        }

        if($post['response_type'] == 1)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_str_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_xml_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_json_response');
           if($post['str_res_type'])
            {
              foreach($post['str_res_type'] as $key=>$keyVal)
              {
                 if($keyVal != 0)
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'value_id' => $keyVal,
                    'success_val' => isset($post['str_res_status_val'][$key][0]) ? $post['str_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['str_res_status_val'][$key][1]) ? $post['str_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['str_res_status_val'][$key][2]) ? $post['str_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_str_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['response_type'] == 2)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_str_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_xml_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_json_response');
           if($post['xml_res_key'])
            {
              foreach($post['xml_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['xml_res_type'][$key]) ? $post['xml_res_type'][$key] : 0,
                    'success_val' => isset($post['xml_res_status_val'][$key][0]) ? $post['xml_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['xml_res_status_val'][$key][1]) ? $post['xml_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['xml_res_status_val'][$key][2]) ? $post['xml_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_xml_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['response_type'] == 3)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_str_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_xml_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_json_response');
           if($post['json_res_key'])
            {
              foreach($post['json_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['json_res_type'][$key]) ? $post['json_res_type'][$key] : 0,
                    'success_val' => isset($post['json_res_status_val'][$key][0]) ? $post['json_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['json_res_status_val'][$key][1]) ? $post['json_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['json_res_status_val'][$key][2]) ? $post['json_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_json_response',$getParaData);
                 }
              }
            }
        }


        // save get balance api parameters
        $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_header_parameter');
        //save header parameter
        $headerParaData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_balance_header_is_access_key']) ? $post['get_balance_header_is_access_key'] : 0,
            'access_key' => $post['get_balance_header_access_key'],
            'is_username' => isset($post['get_balance_header_is_username']) ? $post['get_balance_header_is_username'] : 0,
            'username_key' => $post['get_balance_header_username'],
            'is_password' => isset($post['get_balance_header_is_password']) ? $post['get_balance_header_is_password'] : 0,
            'password_key' => $post['get_balance_header_password'],
          );
          $this->db->insert('api_get_balance_header_parameter',$headerParaData);
        if($post['get_balance_request_type'] == 1)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_parameter');
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_balance_get_is_access_key']) ? $post['get_balance_get_is_access_key'] : 0,
            'access_key' => $post['get_balance_get_access_key'],
            'is_username' => isset($post['get_balance_get_is_username']) ? $post['get_balance_get_is_username'] : 0,
            'username_key' => $post['get_balance_get_username'],
            'is_password' => isset($post['get_balance_get_is_password']) ? $post['get_balance_get_is_password'] : 0,
            'password_key' => $post['get_balance_get_password'],
          );
          $this->db->insert('api_get_balance_parameter',$paraData);
        }
        elseif($post['get_balance_request_type'] == 2)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_parameter');
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['get_balance_get_is_access_key']) ? $post['get_balance_get_is_access_key'] : 0,
            'access_key' => $post['get_balance_get_access_key'],
            'is_username' => isset($post['get_balance_get_is_username']) ? $post['get_balance_get_is_username'] : 0,
            'username_key' => $post['get_balance_get_username'],
            'is_password' => isset($post['get_balance_get_is_password']) ? $post['get_balance_get_is_password'] : 0,
            'password_key' => $post['get_balance_get_password'],
          );
          $this->db->insert('api_get_balance_parameter',$paraData);
        }
        elseif($post['get_balance_request_type'] == 3)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_parameter');
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['get_balance_get_is_access_key']) ? $post['get_balance_get_is_access_key'] : 0,
            'access_key' => $post['get_balance_get_access_key'],
            'is_username' => isset($post['get_balance_get_is_username']) ? $post['get_balance_get_is_username'] : 0,
            'username_key' => $post['get_balance_get_username'],
            'is_password' => isset($post['get_balance_get_is_password']) ? $post['get_balance_get_is_password'] : 0,
            'password_key' => $post['get_balance_get_password'],
          );
          $this->db->insert('api_get_balance_parameter',$paraData);
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['get_balance_get_is_access_key']) ? $post['get_balance_get_is_access_key'] : 0,
            'access_key' => $post['get_balance_get_access_key'],
            'is_username' => isset($post['get_balance_get_is_username']) ? $post['get_balance_get_is_username'] : 0,
            'username_key' => $post['get_balance_get_username'],
            'is_password' => isset($post['get_balance_get_is_password']) ? $post['get_balance_get_is_password'] : 0,
            'password_key' => $post['get_balance_get_password'],
          );
          $this->db->insert('api_get_balance_parameter',$paraData);
        }

        if($post['get_balance_request_type'] == 1)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_get_parameter');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_post_parameter');

          if($post['get_balance_get_para_key'])
          {
            foreach($post['get_balance_get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_balance_get_para_val'][$key]) ? $post['get_balance_get_para_val'][$key] : '',
                  'value_id' => isset($post['get_balance_get_para_key_val'][$key]) ? $post['get_balance_get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_balance_get_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['get_balance_request_type'] == 2)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_get_parameter');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_post_parameter');
          if($post['get_balance_post_para_key'])
          {
            foreach($post['get_balance_post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_balance_post_para_val'][$key]) ? $post['get_balance_post_para_val'][$key] : '',
                  'value_id' => isset($post['get_balance_post_para_key_val'][$key]) ? $post['get_balance_post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_balance_post_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['get_balance_request_type'] == 3)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_get_parameter');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_post_parameter');
          if($post['get_balance_get_para_key'])
          {
            foreach($post['get_balance_get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_balance_get_para_val'][$key]) ? $post['get_balance_get_para_val'][$key] : '',
                  'value_id' => isset($post['get_balance_get_para_key_val'][$key]) ? $post['get_balance_get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_balance_get_parameter',$getParaData);
               }
            }
          }
          if($post['get_balance_post_para_key'])
          {
            foreach($post['get_balance_post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['get_balance_post_para_val'][$key]) ? $post['get_balance_post_para_val'][$key] : '',
                  'value_id' => isset($post['get_balance_post_para_key_val'][$key]) ? $post['get_balance_post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_get_balance_post_parameter',$getParaData);
               }
            }
          }
           
        }

        if($post['get_balance_response_type'] == 1)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_str_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_xml_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_json_response');
           if($post['get_balance_str_res_type'])
            {
              foreach($post['get_balance_str_res_type'] as $key=>$keyVal)
              {
                 if($keyVal != 0)
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'value_id' => $keyVal,
                    'success_val' => isset($post['get_balance_str_res_status_val'][$key][0]) ? $post['get_balance_str_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['get_balance_str_res_status_val'][$key][1]) ? $post['get_balance_str_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['get_balance_str_res_status_val'][$key][2]) ? $post['get_balance_str_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_get_balance_str_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['get_balance_response_type'] == 2)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_str_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_xml_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_json_response');
           if($post['get_balance_xml_res_key'])
            {
              foreach($post['get_balance_xml_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['get_balance_xml_res_type'][$key]) ? $post['get_balance_xml_res_type'][$key] : 0,
                    'success_val' => isset($post['get_balance_xml_res_status_val'][$key][0]) ? $post['get_balance_xml_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['get_balance_xml_res_status_val'][$key][1]) ? $post['get_balance_xml_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['get_balance_xml_res_status_val'][$key][2]) ? $post['get_balance_xml_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_get_balance_xml_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['get_balance_response_type'] == 3)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_str_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_xml_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_get_balance_json_response');
           if($post['get_balance_json_res_key'])
            {
              foreach($post['get_balance_json_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['get_balance_json_res_type'][$key]) ? $post['get_balance_json_res_type'][$key] : 0,
                    'success_val' => isset($post['get_balance_json_res_status_val'][$key][0]) ? $post['get_balance_json_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['get_balance_json_res_status_val'][$key][1]) ? $post['get_balance_json_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['get_balance_json_res_status_val'][$key][2]) ? $post['get_balance_json_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_get_balance_json_response',$getParaData);
                 }
              }
            }
        }


        // save check status api parameters
        if($post['check_status_request_type'] == 1)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_parameter');
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['check_status_get_is_access_key']) ? $post['check_status_get_is_access_key'] : 0,
            'access_key' => $post['check_status_get_access_key'],
            'is_username' => isset($post['check_status_get_is_username']) ? $post['check_status_get_is_username'] : 0,
            'username_key' => $post['check_status_get_username'],
            'is_password' => isset($post['check_status_get_is_password']) ? $post['check_status_get_is_password'] : 0,
            'password_key' => $post['check_status_get_password'],
          );
          $this->db->insert('api_check_status_parameter',$paraData);
        }
        elseif($post['check_status_request_type'] == 2)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_parameter');
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['check_status_get_is_access_key']) ? $post['check_status_get_is_access_key'] : 0,
            'access_key' => $post['check_status_get_access_key'],
            'is_username' => isset($post['check_status_get_is_username']) ? $post['check_status_get_is_username'] : 0,
            'username_key' => $post['check_status_get_username'],
            'is_password' => isset($post['check_status_get_is_password']) ? $post['check_status_get_is_password'] : 0,
            'password_key' => $post['check_status_get_password'],
          );
          $this->db->insert('api_check_status_parameter',$paraData);
        }
        elseif($post['check_status_request_type'] == 3)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_parameter');
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 1,
            'is_access_key' => isset($post['check_status_get_is_access_key']) ? $post['check_status_get_is_access_key'] : 0,
            'access_key' => $post['check_status_get_access_key'],
            'is_username' => isset($post['check_status_get_is_username']) ? $post['check_status_get_is_username'] : 0,
            'username_key' => $post['check_status_get_username'],
            'is_password' => isset($post['check_status_get_is_password']) ? $post['check_status_get_is_password'] : 0,
            'password_key' => $post['check_status_get_password'],
          );
          $this->db->insert('api_check_status_parameter',$paraData);
          $paraData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'type' => 2,
            'is_access_key' => isset($post['check_status_get_is_access_key']) ? $post['check_status_get_is_access_key'] : 0,
            'access_key' => $post['check_status_get_access_key'],
            'is_username' => isset($post['check_status_get_is_username']) ? $post['check_status_get_is_username'] : 0,
            'username_key' => $post['check_status_get_username'],
            'is_password' => isset($post['check_status_get_is_password']) ? $post['check_status_get_is_password'] : 0,
            'password_key' => $post['check_status_get_password'],
          );
          $this->db->insert('api_check_status_parameter',$paraData);
        }

        if($post['check_status_request_type'] == 1)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_get_parameter');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_post_parameter');

          if($post['check_status_get_para_key'])
          {
            foreach($post['check_status_get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['check_status_get_para_val'][$key]) ? $post['check_status_get_para_val'][$key] : '',
                  'value_id' => isset($post['check_status_get_para_key_val'][$key]) ? $post['check_status_get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_check_status_get_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['check_status_request_type'] == 2)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_get_parameter');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_post_parameter');
          if($post['check_status_post_para_key'])
          {
            foreach($post['check_status_post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['check_status_post_para_val'][$key]) ? $post['check_status_post_para_val'][$key] : '',
                  'value_id' => isset($post['check_status_post_para_key_val'][$key]) ? $post['check_status_post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_check_status_post_parameter',$getParaData);
               }
            }
          }
           
        }
        elseif($post['check_status_request_type'] == 3)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_get_parameter');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_post_parameter');
          if($post['get_balance_get_para_key'])
          {
            foreach($post['check_status_get_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['check_status_get_para_val'][$key]) ? $post['check_status_get_para_val'][$key] : '',
                  'value_id' => isset($post['check_status_get_para_key_val'][$key]) ? $post['check_status_get_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_check_status_get_parameter',$getParaData);
               }
            }
          }
          if($post['check_status_post_para_key'])
          {
            foreach($post['check_status_post_para_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value' => isset($post['check_status_post_para_val'][$key]) ? $post['check_status_post_para_val'][$key] : '',
                  'value_id' => isset($post['check_status_post_para_key_val'][$key]) ? $post['check_status_post_para_key_val'][$key] : '',
                 );
                 $this->db->insert('api_check_status_post_parameter',$getParaData);
               }
            }
          }
           
        }

        if($post['check_status_response_type'] == 1)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_str_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_xml_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_json_response');
           if($post['check_status_str_res_type'])
            {
              foreach($post['check_status_str_res_type'] as $key=>$keyVal)
              {
                 if($keyVal != 0)
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'value_id' => $keyVal,
                    'success_val' => isset($post['check_status_str_res_status_val'][$key][0]) ? $post['check_status_str_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['check_status_str_res_status_val'][$key][1]) ? $post['check_status_str_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['check_status_str_res_status_val'][$key][2]) ? $post['check_status_str_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_check_status_str_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['check_status_response_type'] == 2)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_str_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_xml_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_json_response');
           if($post['check_status_xml_res_key'])
            {
              foreach($post['check_status_xml_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['check_status_xml_res_type'][$key]) ? $post['check_status_xml_res_type'][$key] : 0,
                    'success_val' => isset($post['check_status_xml_res_status_val'][$key][0]) ? $post['check_status_xml_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['check_status_xml_res_status_val'][$key][1]) ? $post['check_status_xml_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['check_status_xml_res_status_val'][$key][2]) ? $post['check_status_xml_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_check_status_xml_response',$getParaData);
                 }
              }
            }
        }
        elseif($post['check_status_response_type'] == 3)
        {
          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_str_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_xml_response');

          $this->db->where('api_id',$api_id);
          $this->db->where('account_id',$account_id);
          $this->db->delete('api_check_status_json_response');
           if($post['check_status_json_res_key'])
            {
              foreach($post['check_status_json_res_key'] as $key=>$keyVal)
              {
                 if($keyVal != '')
                 {
                   $getParaData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'para_key' => $keyVal,
                    'value_id' => isset($post['check_status_json_res_type'][$key]) ? $post['check_status_json_res_type'][$key] : 0,
                    'success_val' => isset($post['check_status_json_res_status_val'][$key][0]) ? $post['check_status_json_res_status_val'][$key][0] : '',
                    'failed_val' => isset($post['check_status_json_res_status_val'][$key][1]) ? $post['check_status_json_res_status_val'][$key][1] : '',
                    'pending_val' => isset($post['check_status_json_res_status_val'][$key][2]) ? $post['check_status_json_res_status_val'][$key][2] : '',
                   );
                   $this->db->insert('api_check_status_json_response',$getParaData);
                 }
              }
            }
        }

        $this->db->where('api_id',$api_id);
        $this->db->where('account_id',$account_id);
        $this->db->delete('api_call_back_response');
         if($post['call_back_res_key'])
          {
            foreach($post['call_back_res_key'] as $key=>$keyVal)
            {
               if($keyVal != '')
               {
                 $getParaData = array(
                  'account_id' => $account_id,
                  'api_id' => $api_id,
                  'para_key' => $keyVal,
                  'value_id' => isset($post['call_back_res_type'][$key]) ? $post['call_back_res_type'][$key] : 0,
                  'success_val' => isset($post['call_back_res_status_val'][$key][0]) ? $post['call_back_res_status_val'][$key][0] : '',
                  'failed_val' => isset($post['call_back_res_status_val'][$key][1]) ? $post['call_back_res_status_val'][$key][1] : '',
                  'pending_val' => isset($post['call_back_res_status_val'][$key][2]) ? $post['call_back_res_status_val'][$key][2] : '',
                 );
                 $this->db->insert('api_call_back_response',$getParaData);
               }
            }
          }

        return true;

    }

    public function saveAmountFilterApi($post)
    {
        $account_id = $this->User->get_domain_account();
        $apiData = array(
          'account_id' => $account_id,
          'start_range' => $post['startRange'],
          'end_range' => $post['endRange'],
          'op_id' => $post['op_id'],
          'api_id' => $post['api_id'],
          'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('amount_active_api',$apiData);
    }

    public function updateAmountFilterApi($post,$recordID)
    {
        $account_id = $this->User->get_domain_account();
        $apiData = array(
          'start_range' => $post['startRange'],
          'end_range' => $post['endRange'],
          'op_id' => $post['op_id'],
          'api_id' => $post['api_id'],
          'updated' => date('Y-m-d H:i:s')
        );
        $this->db->where('id',$recordID);
        $this->db->where('account_id',$account_id);
        $this->db->update('amount_active_api',$apiData);
    }

    

}


/* end of file: az.php */
/* Location: ./application/models/az.php */