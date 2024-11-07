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

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveUser($post)
    {       
            $loggedUser = $this->User->getLoggedUser("marwarcare_admin");
    	    
			$user_display_id = $this->User->generate_unique_user_id();
            
            $data = array(    
                'role_id'            =>  4,      
                'user_code'          =>  $user_display_id,      
                'name'               =>  $post['name'],
                'username'           =>  $user_display_id,
                'password'           =>  do_hash($post['password']),
                'decode_password'    =>  $post['password'],
                'email'              =>  trim(strtolower($post['email'])),
                'mobile'             =>  $post['mobile'],
                'created_by'         =>  $loggedUser['id'],   
                'is_active'          =>  $post['is_active'],
                'wallet_balance'     =>  0,   
                'is_verified'        =>  1,   
                'created'            =>  date('Y-m-d H:i:s')
            );

            $this->db->insert('users',$data);
            $employe_id = $this->db->insert_id();


            if($post['is_view']){

             foreach($post['is_view'] as $key=>$list){

               $menu_data = array(
               
                'employe_id'  => $employe_id,
                'menu_id' => $post['is_view'][$key],
                'is_view' => isset($post['is_view'][$key]) ? 1 : 0,
                'is_add' => isset($post['is_add'][$key]) ? 1 : 0,   
                'is_edit' => isset($post['is_edit'][$key]) ? 1 : 0,
                'is_delete' => isset($post['is_delete'][$key]) ? 1 : 0,
               );

               $this->db->insert('employe_menu_access',$menu_data);
             }   

            }




    	return true;
    }

    public function updateUser($post)
        {

            $data = array(    
				'name'               =>  $post['name'],
                'email'              =>  trim(strtolower($post['email'])),
                'mobile'             =>  $post['mobile'],
                'is_active'          =>  $post['is_active'],
                'updated'            =>  date('Y-m-d H:i:s')
            );

            if($post['password'])
            {
                $data['password'] = do_hash($post['password']);
                $data['decode_password'] = $post['password'];
            }

            $this->db->where('id',$post['id']);
            $this->db->update('users',$data);


            $this->db->where('employe_id',$post['id']);
            $this->db->delete('employe_menu_access');

             foreach($post['is_view'] as $key=>$list){

               $menu_data = array(
                'employe_id'  => $post['id'],
                'menu_id' => $post['is_view'][$key],
                'is_view' => !empty($post['is_view'][$key])?1:0,
                'is_add' => !empty($post['is_add'][$key])?1:0,   
                'is_edit' => !empty($post['is_edit'][$key])?1:0,
                'is_delete' => !empty($post['is_delete'][$key])?1:0,
               );

              $this->db->insert('employe_menu_access',$menu_data);
             }   

             
            return true;
        
        }


          public function save_enquiry($data){

                       $query= $this->db->insert('website_enquiry',$data);
                        return $query;
                      


                      }

}


/* end of file: az.php */
/* Location: ./application/models/az.php */