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

class Ticket_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    
    public function saveTicketResponse($post,$filePath)
    {       
            $account_id = $this->User->get_domain_account();
            $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];
            $ticket_id = $post['ticket_id'];

            $ticketData = array(
                'account_id' => $account_id,
                'ticket_id' => $ticket_id,
                'message' => $post['message'],
                'attachment' => $filePath,
                'status' => $post['status'],
                'created'             => date('Y-m-d H:i:s'),      
                'created_by' => $loggedAccountID
            );
            $this->db->insert('ticket_reply',$ticketData);

            $this->db->where('id',$ticket_id);
            $this->db->where('account_id',$account_id);
            $this->db->update('ticket',array('status'=>$post['status'],'is_read'=>1,'updated'=>date('Y-m-d H:i:s')));

            

        return true;
    }

    
    
}


/* end of file: az.php */
/* Location: ./application/models/az.php */