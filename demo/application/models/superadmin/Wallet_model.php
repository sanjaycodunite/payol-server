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

class Wallet_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveWallet($post)
    {       
            $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
    	    
            $getAccountID = $this->db->select('account_id')->get_where('users',array('id'=>$post['member']))->row_array();
            $account_id = $getAccountID['account_id'];

            $before_balance = $this->User->getMemberWalletBalanceSP($post['member'], $walletType = 1);
			
			$type = $post['type'];
			
			if($type == 1){
				$after_balance = $before_balance + $post['amount'];    
			}
			else
			{
				$after_balance = $before_balance - $post['amount'];    
			}

            $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $post['member'],    
                'before_balance'      => $before_balance,
                'amount'              => $post['amount'],  
                'after_balance'       => $after_balance,      
                'status'              => 1,
                'type'                => $type,      
                'wallet_type'         => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedUser['id'],
                'description'         => $post['description'],
                'is_manual' => 1            
            );

            $this->db->insert('member_wallet',$wallet_data);
  

    	return true;
    }

    public function updateRequestAuth($requestID,$status)
    {
        $loggedUser = $this->User->getLoggedUser(SUPERADMIN_SESSION_ID);
        $get_request_data = $this->db->get_where('member_fund_request',array('id'=>$requestID,'status'=>1))->row_array();
        $memberID = $get_request_data['member_id'];
        $amount = $get_request_data['request_amount'];
        $request_id = $get_request_data['request_id'];
        if($status == 1){
            // update request status
            $this->db->where('id',$requestID);
            $this->db->update('member_fund_request',array('status'=>2,'updated'=>date('Y-m-d H:i:s')));

            //get member wallet_balance
            $getAccountID = $this->db->select('account_id')->get_where('users',array('id'=>$memberID))->row_array();
            $account_id = $getAccountID['account_id'];

            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID, $walletType = 1);
            
            
            $after_wallet_balance = $before_wallet_balance + $amount;
            // update member wallet
            $wallet_data = array(
                'account_id' => $account_id,
                'member_id'           => $memberID,    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 1,      
                'wallet_type'         => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => 1,
                'description'         => 'Fund Request #'.$request_id.' Approved.' 
            );

            $this->db->insert('member_wallet',$wallet_data);
            
        }
        else
        {
            // update request status
            $this->db->where('id',$requestID);
            $this->db->update('member_fund_request',array('status'=>3,'updated'=>date('Y-m-d H:i:s')));
        }   
        
        
        return true;
    }

    

    
}


/* end of file: az.php */
/* Location: ./application/models/az.php */