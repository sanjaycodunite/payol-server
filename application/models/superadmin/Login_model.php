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

class Login_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function adminLoginAuthentication($post) {
		//check username and password
        $response = array();
        $this->db->group_start();
        $this->db->where('role_id', 1);
        $this->db->group_end();
        $this->db->select('id,account_id,user_code,name,password,is_active,is_verified,role_id');
        $this->db->where('username', $post['username']);
        $this->db->where('password', do_hash($post['password']));
        $query = $this->db->get('users');
		if($query->num_rows() > 0){
            $response = $query->row_array();
        }
        return $response;
    }

    

	public function adminLogout()
	{
		$this->session->unset_userdata(SUPERADMIN_SESSION_ID);
	}
	
	
	
}


/* end of file: az.php */
/* Location: ./application/models/az.php */