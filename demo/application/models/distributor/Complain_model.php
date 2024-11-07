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

class Complain_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveComplain($post)
    {       
            $account_id = $this->User->get_domain_account();
            $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];

            // generate ticket id
            $complain_id = rand(111111,999999).'-'.date('Y').'-'.date('m').'-'.date('d');
    	    
            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'complain_id' => $complain_id,
                'complain_type' => 1,
                'record_id' => $post['recordID'],
                'description'      => $post['description'],
                'status'              => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'             => $loggedAccountID
            );

            $this->db->insert('complain',$wallet_data);
            $system_ticket_id = $this->db->insert_id();

    	return true;
    }

    public function saveTicketResponse($post,$filePath)
    {       
            $account_id = $this->User->get_domain_account();
            $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];
            $ticket_id = $post['ticket_id'];

            $ticketData = array(
                'account_id' => $account_id,
                'ticket_id' => $ticket_id,
                'message' => $post['message'],
                'attachment' => $filePath,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by' => $loggedAccountID
            );
            $this->db->insert('ticket_reply',$ticketData);

            

        return true;
    }

    public function saveBBPSComplain($post)
    {       
            $account_id = $this->User->get_domain_account();
            $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];

            // generate ticket id
            $complain_id = rand(111111,999999).'-'.date('Y').'-'.date('m').'-'.date('d');
            
            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'complain_id' => $complain_id,
                'complain_type' => 5,
                'record_id' => $post['recordID'],
                'description'      => $post['description'],
                'status'              => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'             => $loggedAccountID
            );

            $this->db->insert('complain',$wallet_data);
            $system_ticket_id = $this->db->insert_id();

        return true;
    }

    
    
}


/* end of file: az.php */
/* Location: ./application/models/az.php */