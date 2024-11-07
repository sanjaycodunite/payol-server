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
    $account_id = $this->User->get_domain_account();
    $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
    $loggedAccountID = $loggedUser['id'];
    $data = array(
      'account_id' => $account_id,
      'account_type' => 1,
      'image_path' => $filePath,                             
      'title'		=>$post['domain_name'],
      'domain_url'=>$post['domain_url'],
      'email'=>strtolower($post['email']),
      'name'=>$post['name'],
      'mobile'=>$post['mobile'],
      'account_code'=>$post['account_code'],
      'status'=>0,
      'created' => date('Y-m-d H:i:s')
    );
    $this->db->insert('account_request',$data);
    $account_id = $this->db->insert_id();

    

    return true;

  }





}




?>