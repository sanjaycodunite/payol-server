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

class Member_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveMember($post)
    {       
            $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
    	    
			$user_display_id = $this->User->generate_unique_member_id($post['role_id']);

            $account_id = $this->User->get_domain_account();
            $accountData = $this->User->get_account_data($account_id);
            
            $data = array(   
                'account_id'         =>  $account_id, 
                'role_id'            =>  $post['role_id'],      
                'user_code'          =>  $user_display_id,      
                'name'               =>  ucwords($post['name']),
                'username'           =>  $user_display_id,
                'password'           =>  do_hash($post['password']),
                'decode_password'    =>  $post['password'],
                'transaction_password'=>  do_hash($post['transaction_password']),
                'decoded_transaction_password'=>  $post['transaction_password'],
                'email'              =>  trim(strtolower($post['email'])),
                'mobile'             =>  $post['mobile'],
                'country_id'         =>  $post['country_id'],
                'state_id'         =>  $post['state_id'],
                'city'         =>  $post['city'],
                'created_by'         =>  $loggedUser['id'],
                'creator_id'         =>  $loggedUser['id'],      
                'is_active'          =>  $post['is_active'],
                'wallet_balance'     =>  0,   
                'min_wallet_balance' =>  $post['min_wallet_balance'],
                'is_verified'        =>  1,   
                'created'            =>  date('Y-m-d H:i:s'),
                'district'              =>$post['district'],
                'block'                  =>$post['block'],
                'village'              =>$post['village'],
                'address'              =>$post['address'],
                'pincode'              =>$post['pincode'],
                'aadhar_no'              =>$post['aadhar_no'],
                'pan_no'              =>$post['pan_no'],
                'package_id'         =>  $post['package_id']
            );

            $this->db->insert('users',$data);
            $member_id = $this->db->insert_id();

            $data = array(
             'account_id' => $account_id,
             'member_id' => $member_id,
             'service_id' => 1,
             'status' => 1,
             'created'  =>  date('Y-m-d H:i:s'),
             'created_by' => $loggedUser['id']      
            );

            $this->db->insert('account_user_services',$data);

            // SEND SMS
            $api_url = SMS_REGISTER_MSG_API_URL;
            
            
           $request = array(
                'template_id' =>$accountData['sms_template_id'],
                'short_url' =>'1',
                'recipients' =>array(
                    array(
                    'mobiles' => '91'.$post['mobile'],
                    'name'  =>$post['name'],
                    'username' => $user_display_id,
                    'password' => $post['password'],
                    'pin' =>$post['transaction_password']
                    )
                )
                );

            $header = array(
                'content-type: application/JSON',
                'authkey: '.$accountData['sms_auth_key']
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

            $smsLogData = array(
             'account_id' => $account_id,   
             'user_id' => $member_id,
             'api_url' => $api_url,
             'api_response' => $output,
             'post_data' => json_encode($request),
             'header_data' => json_encode($header),
             'created' => date('Y-m-d H:i:s'),
             'created_by' => $loggedUser['id']
            );
            
            $this->db->insert('sms_api_response',$smsLogData);
			 
        return true;

    }


   
    public function updateMember($post)
    {
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $memberID = $post['id'];
        // get member role id
        $getRole = $this->db->get_where('users',array('id'=>$memberID,'account_id'=>$account_id))->row_array();
        $role_id = $getRole['role_id'];

        $data = array(    
            'name'               =>  ucwords($post['name']),
            'email'              =>  trim(strtolower($post['email'])),
            'mobile'             =>  $post['mobile'],
            'country_id'         =>  $post['country_id'],
            'state_id'         =>  $post['state_id'],
            'city'         =>  $post['city'],
            'min_wallet_balance' =>  $post['min_wallet_balance'],
            'is_active'          =>  $post['is_active'],
            'updated'            =>  date('Y-m-d H:i:s'),
            'package_id'         =>  $post['package_id'],
            'district'              =>$post['district'],
                'block'                  =>$post['block'],
                'village'              =>$post['village'],
                'address'              =>$post['address'],
                'pincode'              =>$post['pincode'],
                'aadhar_no'              =>$post['aadhar_no'],
                'pan_no'              =>$post['pan_no'],
        );

        if($role_id != $post['role_id'])
        {
            /*$user_display_id = $this->User->generate_unique_member_id($post['role_id']);*/
            $data['role_id'] = $post['role_id'];
            /*$data['user_code'] = $user_display_id;
            $data['username'] = $user_display_id;*/
        }




        if($post['password'])
        {
            $data['password'] = do_hash($post['password']);
            $data['decode_password'] = $post['password'];
        }

        if($post['transaction_password'])
        {
            $data['transaction_password'] = do_hash($post['transaction_password']);
            $data['decoded_transaction_password'] = $post['transaction_password'];
        }

        $this->db->where('id',$post['id']);
        $this->db->where('account_id',$account_id);
        $this->db->update('users',$data);

        return true;
    
    }
    
   

}


/* end of file: az.php */
/* Location: ./application/models/az.php */