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
            $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
    	    
			$user_display_id = $this->User->generate_unique_member_id($post['role_id']);

            $account_id = $this->User->get_domain_account();
            
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
                'is_active'          =>  $post['is_active'],
                'wallet_balance'     =>  0,   
                'is_verified'        =>  1,   
                'created'            =>  date('Y-m-d H:i:s')
            );

            $this->db->insert('users',$data);
            $member_id = $this->db->insert_id();

            $data = array(
             'account_id' => $account_id,
             'member_id' => $member_id,
             'service_id' => 1,
             'status' => 1,
             'created_by' => $loggedUser['id']      
            );

            $this->db->insert('account_user_services',$data);
			 
        return true;

    }


   
    public function updateMember($post)
    {
        $loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $loggedAccountID = $loggedUser['id'];
        $memberID = $post['id'];
        // get member role id
        $getRole = $this->db->get_where('users',array('id'=>$memberID,'account_id'=>$account_id,'created_by'=>$loggedAccountID))->row_array();
        $role_id = $getRole['role_id'];

        $data = array(    
            'name'               =>  ucwords($post['name']),
            'email'              =>  trim(strtolower($post['email'])),
            'mobile'             =>  $post['mobile'],
            'country_id'         =>  $post['country_id'],
            'state_id'         =>  $post['state_id'],
            'city'         =>  $post['city'],
            'is_active'          =>  $post['is_active'],
            'updated'            =>  date('Y-m-d H:i:s')
        );

        if($role_id != $post['role_id'])
        {
            $user_display_id = $this->User->generate_unique_member_id($post['role_id']);
            $data['role_id'] = $post['role_id'];
            $data['user_code'] = $user_display_id;
            $data['username'] = $user_display_id;
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
        $this->db->where('created_by',$loggedAccountID);
        $this->db->update('users',$data);

        return true;
    
    }
    
   

}


/* end of file: az.php */
/* Location: ./application/models/az.php */