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

class Ewallet_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveWallet($post)
    {       
            $account_id = $this->User->get_domain_account();
            $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
            $loggedAccountID = $loggedUser['id'];
    	    
            $before_balance = $this->db->get_where('users',array('id'=>$post['member']))->row_array();
            $member_code = $before_balance['user_code'];
            $member_name = $before_balance['name'];

            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($post['member'],2);
			
			$type = $post['type'];
			
			if($type == 1){
				$after_balance = $before_wallet_balance + $post['amount'];    
			}
			else
			{
				$after_balance = $before_wallet_balance - $post['amount'];    
			}

            $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $post['member'],    
            'before_balance'      => $before_wallet_balance,
            'amount'              => $post['amount'],  
            'after_balance'       => $after_balance,      
            'status'              => 1,
            'type'                => $type,    
            'wallet_type'         => 2,  
            'created'             => date('Y-m-d H:i:s'),      
            'credited_by'         => $loggedUser['id'],
            'description'         => $post['description']            
            );

            $this->db->insert('member_wallet',$wallet_data);

             

            if($type == 1)
            {
                // debit wallet
                
                $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID,2);
                
                
                $after_balance = $before_wallet_balance - $post['amount'];    
                

                $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $post['amount'],  
                'after_balance'       => $after_balance,      
                'status'              => 1,
                'type'                => 2,   
                'wallet_type'         => 2,   
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedUser['id'],
                'description'         => 'Credited into Member #'.$member_code.' ('.$member_name.')'
                );

                $this->db->insert('member_wallet',$wallet_data);


            }
            else
            {
                // debit wallet
                $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID,2);
                
                
                $after_balance = $before_wallet_balance + $post['amount'];    
                

                $wallet_data = array(
                'account_id'          => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $post['amount'],  
                'after_balance'       => $after_balance,      
                'status'              => 1,
                'type'                => 1,     
                'wallet_type'         => 2, 
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedUser['id'],
                'description'         => 'Debited from Member #'.$member_code.' ('.$member_name.')'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
            }

    	return true;
    }

    public function updateRequestAuth($requestID,$status)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $get_request_data = $this->db->get_where('member_fund_request',array('id'=>$requestID,'status'=>1))->row_array();
        $memberID = $get_request_data['member_id'];
        $amount = $get_request_data['request_amount'];
        $request_id = $get_request_data['request_id'];
        if($status == 1){
            // update request status
            $this->db->where('id',$requestID);
            $this->db->update('member_fund_request',array('status'=>2,'updated'=>date('Y-m-d H:i:s')));

            //get member wallet_balance
            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID,2);

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
                'wallet_type'         => 2,
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => 1,
                'description'         => 'Fund Request #'.$request_id.' Approved.' 
            );

            $this->db->insert('member_wallet',$wallet_data);
            
           
            //get member wallet_balance
            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($loggedAccountID,2);


            $after_wallet_balance = $before_wallet_balance - $amount;

            // update member wallet
            $wallet_data = array(
                'account_id' => $account_id,
                'member_id'           => $loggedAccountID,    
                'before_balance'      => $before_wallet_balance,
                'amount'              => $amount,  
                'after_balance'       => $after_wallet_balance,      
                'status'              => 1,
                'type'                => 2,      
                'wallet_type'         => 2,
                'created'             => date('Y-m-d H:i:s'),      
                'credited_by'         => $loggedAccountID,
                'description'         => 'Fund Request #'.$request_id.' Approved Deduction.' 
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

    public function generateFundRequest($post)
    {
        $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
        
        $amount = $post['amount'];
        
        // generate request id
        $request_id = time().rand(111,333);
        
        
        $tokenData = array(
            'account_id' => $account_id,
            'request_id' => $request_id,
            'member_id' => $loggedAccountID,
            'request_wallet_type' => 2,
            'request_amount' => $amount,
            'txnid' => isset($post['txnID']) ? $post['txnID'] : '',
            'status' => 1,
            'created' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('member_fund_request',$tokenData);

        return true;
    }

    

    
}


/* end of file: az.php */
/* Location: ./application/models/az.php */