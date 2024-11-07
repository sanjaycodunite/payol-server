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

class Saving_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function sendRequest($post)
    {
        $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
        $account_id = $this->User->get_domain_account();
        $loggedAccountID = $loggedUser['id'];
        $club_id = $post['club_id'];

        $requestData = array(
            'account_id' => $account_id,
            'member_id' => $loggedAccountID,
            'club_id' => $club_id,
            'action_type' => isset($post['accept']) ? 1 : 2,
            'is_agree' => isset($post['is_agree']) ? 1 : 0,
            'status' => isset($post['accept']) ? 1 : 3,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('club_member_request',$requestData);
        return true;
    }
}


/* end of file: az.php */
/* Location: ./application/models/az.php */