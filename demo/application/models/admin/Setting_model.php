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

class Setting_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function updateAdminPassword($post)
    {
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

    	$data = array(
    		'password' => do_hash($post['npw']),
            'decode_password' =>$post['npw'],
    		'updated' => date('Y-m-d h:i:s')
    	);

    	$this->db->where('id',$loggedAccountID);
        $this->db->where('account_id',$account_id);
    	$this->db->update('users',$data);

    	return true;
    }



    public function updateAdminTranscationPassword($post)
    {
        //get logged user info
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();

        $data = array(
            'transaction_password' => do_hash($post['npw']),
            'decoded_transaction_password' =>$post['npw'],
            'updated' => date('Y-m-d h:i:s')
        );

        $this->db->where('id',$loggedAccountID);
        $this->db->where('account_id',$account_id);
        $this->db->update('users',$data);

        return true;
    }




}


/* end of file: az.php */
/* Location: ./application/models/az.php */