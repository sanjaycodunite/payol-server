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

class Employe_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveEmploye($post)
    {       
            $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
    	    
			$user_display_id = $this->User->generate_unique_member_id(7);

            $account_id = $this->User->get_domain_account();
            
            $data = array(   
                'account_id'         =>  $account_id, 
                'role_id'            =>  7,
                'employe_designation'=>  $post['employe_designation'],      
                'user_code'          =>  $user_display_id,      
                'name'               =>  ucwords($post['name']),
                'username'           =>  $user_display_id,
                'password'           =>  do_hash($post['password']),
                'decode_password'    =>  $post['password'],
                'email'              =>  trim(strtolower($post['email'])),
                'mobile'             =>  $post['mobile'],
                'created_by'         =>  $loggedUser['id'],   
                'is_active'          =>  $post['is_active'],
                'is_verified'        =>  1,   
                'created'            =>  date('Y-m-d H:i:s'),
            );

            $this->db->insert('users',$data);
            $member_id = $this->db->insert_id();

        return true;

    }


   
    public function updateEmploye($post)
    {
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $memberID = $post['id'];
        
        $data = array(    
            'name'               =>  ucwords($post['name']),
            'employe_designation'=>  $post['employe_designation'],
            'email'              =>  trim(strtolower($post['email'])),
            'mobile'             =>  $post['mobile'],
            'is_active'          =>  $post['is_active'],
            'updated'            =>  date('Y-m-d H:i:s'),
        );

        if($post['password'])
        {
            $data['password'] = do_hash($post['password']);
            $data['decode_password'] = $post['password'];
        }

        
        $this->db->where('id',$post['id']);
        $this->db->where('account_id',$account_id);
        $this->db->update('users',$data);

        return true;
    
    }
    


    public function saveRole($post)
    {   
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        
        $data = array(    
            'account_id'     => $account_id,    
            'title'          =>  $post['role'],      
            'status'         =>  $post['status'],
        );
        $this->db->insert('roles',$data);
        $role_id = $this->db->insert_id();

        //save role permission
        if(isset($post['menu_id']))
        {
            foreach($post['menu_id'] as $menu_id)
            {
                $menuData = array(    
                    'account_id'     => $account_id,
                    'role_id'   =>  $role_id,      
                    'menu_id'   =>  $menu_id,
                );
                $this->db->insert('role_permission',$menuData);
            }
        }

        //save role permission
        if(isset($post['sub_menu_id']))
        {
            foreach($post['sub_menu_id'] as $menu_id)
            {
                $menuData = array(
                    'account_id'     => $account_id,    
                    'role_id'   =>  $role_id,      
                    'sub_menu_id'   =>  $menu_id,
                );
                $this->db->insert('role_permission',$menuData);
            }
        }
        
        return true;
    }

    public function updateRole($post,$catID)
    {   
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        
        $data = array(    
            'title'              =>  $post['title'],      
            'status'         =>  $post['status'],
        );

        $this->db->where('id',$catID);
        $this->db->update('roles',$data);
        $role_id = $catID;

        // delete old permission
        $this->db->where('role_id',$catID);
        $this->db->delete('role_permission');
        //save role permission
        if(isset($post['menu_id']))
        {
            foreach($post['menu_id'] as $menu_id)
            {
                $menuData = array(
                    'account_id'     => $account_id,    
                    'role_id'   =>  $role_id,      
                    'menu_id'   =>  $menu_id,
                );
                $this->db->insert('role_permission',$menuData);
            }
        }

        //save role permission
        if(isset($post['sub_menu_id']))
        {
            foreach($post['sub_menu_id'] as $menu_id)
            {
                $menuData = array(
                    'account_id'     => $account_id,      
                    'role_id'   =>  $role_id,      
                    'sub_menu_id'   =>  $menu_id,
                );
                $this->db->insert('role_permission',$menuData);
            }
        }
        
        return true;
    }
   

}


/* end of file: az.php */
/* Location: ./application/models/az.php */