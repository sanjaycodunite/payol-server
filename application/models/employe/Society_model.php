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

class Society_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveClub($post)
    {       
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	    $account_id = $this->User->get_domain_account();
        
        $data = array(   
            'account_id'         =>  $account_id, 
            'club_name'          =>  $post['club_name'],      
            'member_limit'          =>  $post['member_limit'],      
            'total_amount'          =>  $post['total_amount'],      
            'per_member_amount'          =>  round(($post['total_amount']/$post['member_limit']),2),      
            'commission'          =>  $post['commission'],      
            'is_flat'          =>  isset($post['is_flat']) ? 1 : 0,      
            'tenure_type'          =>  $post['tenure_type'],      
            'min_bid_amount'          =>  $post['min_bid_amount'],      
            'bid_diff_amount'          =>  $post['diff_amount'],      
            'start_date'          =>  $post['start_date'],      
            'state_time'          =>  $post['auction_hour'].':'.$post['auction_min'].':'.$post['auction_sec'],      
            'duration'          =>  $post['auction_duration'],      
            'payment_debit_duration'          =>  $post['payment_debit_duration'],      
            'reserve_no'          =>  $post['reserve_no'],      
            'status'          =>  $post['is_active'],      
            'created'            =>  date('Y-m-d H:i:s'),
            'created_by'         =>  $loggedUser['id']
        );

        $this->db->insert('club_list',$data);
        $club_id = $this->db->insert_id();

        if($post['is_active'] == 1)
        {
        	// save notification
        	$notificationData = array(
        		'account_id' => $account_id,
        		'club_id' => $club_id,
        		'msg' => 'Dear Partners, We have launched a saving plan ('.$post['club_name'].') under Payol Club, Please read carefully and You can join the club by clicking on Accept.',
        		'created' =>  date('Y-m-d H:i:s'),
        	);
        	$this->db->insert('club_notification',$notificationData);
        }

        // save club rounds
        for($i = 1; $i <= $post['member_limit']; $i++)
        {
            if($i == 1)
            {
                $start_datetime = $post['start_date'].' '.$post['auction_hour'].':'.$post['auction_min'].':'.$post['auction_sec'];
                $end_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' + '.$post['auction_duration'].' minute'));
                $payment_debit_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' - '.$post['payment_debit_duration'].' minute'));
            }
            else
            {
                if($post['tenure_type'] == 1)
                {
                    $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 1 days'));
                }
                elseif($post['tenure_type'] == 2)
                {
                    $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 7 days'));
                }
                elseif($post['tenure_type'] == 3)
                {
                    $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 15 days'));
                }
                elseif($post['tenure_type'] == 4)
                {
                    $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 1 months'));
                }
                $end_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' + '.$post['auction_duration'].' minute'));
                $payment_debit_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' - '.$post['payment_debit_duration'].' minute'));
            }
            $roundData = array(
                'account_id' => $account_id,
                'club_id' => $club_id,
                'round_no' => $i,
                'start_datetime' => $start_datetime,
                'end_datetime' => $end_datetime,
                'payment_debit_datetime' => $payment_debit_datetime,
                'status' => 1,
                'created'            =>  date('Y-m-d H:i:s'),
                'created_by'         =>  $loggedUser['id']
            );
            $this->db->insert('club_rounds',$roundData);
        }    
		return true;
        
    }

    public function updateClub($post)
    {       
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
	    $account_id = $this->User->get_domain_account();
        
        $data = array(   
            'club_name'          =>  $post['club_name'],      
            'member_limit'          =>  $post['member_limit'],      
            'total_amount'          =>  $post['total_amount'],      
            'per_member_amount'          =>  round(($post['total_amount']/$post['member_limit']),2),      
            'commission'          =>  $post['commission'],      
            'is_flat'          =>  isset($post['is_flat']) ? 1 : 0,      
            'tenure_type'          =>  $post['tenure_type'],      
            'min_bid_amount'          =>  $post['min_bid_amount'],      
            'bid_diff_amount'          =>  $post['diff_amount'],      
            'start_date'          =>  $post['start_date'],      
            'state_time'          =>  $post['auction_hour'].':'.$post['auction_min'].':'.$post['auction_sec'],      
            'duration'          =>  $post['auction_duration'],      
            'payment_debit_duration'          =>  $post['payment_debit_duration'],      
            'reserve_no'          =>  $post['reserve_no'],      
            'status'          =>  $post['is_active'],      
            'updated'            =>  date('Y-m-d H:i:s'),
            'updated_by'         =>  $loggedUser['id']
        );

        $this->db->where('id',$post['id']);
        $this->db->where('account_id',$account_id);
        $this->db->update('club_list',$data);

        $club_id = $post['id'];
        // check notification saved or not
        $chkClub = $this->db->get_where('club_notification',array('account_id'=>$account_id,'club_id'=>$club_id))->num_rows();
        if($post['is_active'] == 1 && !$chkClub)
        {
        	$club_id = $post['id'];
        	// save notification
        	$notificationData = array(
        		'account_id' => $account_id,
        		'club_id' => $club_id,
        		'msg' => 'Dear Partners, We have launched a saving plan ('.$post['club_name'].') under Payol Club, Please read carefully and You can join the club by clicking on Accept.',
        		'created' =>  date('Y-m-d H:i:s'),
        	);
        	$this->db->insert('club_notification',$notificationData);
        }

        $duration = $post['auction_duration'];

        $this->db->where('account_id',$account_id);
        $this->db->where('club_id',$club_id);
        $this->db->delete('club_rounds');
        // save club rounds
        for($i = 1; $i <= $post['member_limit']; $i++)
        {
            if($i == 1)
            {
                $start_datetime = $post['start_date'].' '.$post['auction_hour'].':'.$post['auction_min'].':'.$post['auction_sec'];
                $end_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' + '.$duration.' minute'));
                $payment_debit_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' - '.$post['payment_debit_duration'].' minute'));

            }
            else
            {
                if($post['tenure_type'] == 1)
                {
                    $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 1 days'));
                }
                elseif($post['tenure_type'] == 2)
                {
                    $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 7 days'));
                }
                elseif($post['tenure_type'] == 3)
                {
                    $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 15 days'));
                }
                elseif($post['tenure_type'] == 4)
                {
                    $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 1 months'));
                }
                $end_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' + '.$duration.' minute'));
                $payment_debit_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' - '.$post['payment_debit_duration'].' minute'));
            }
            $roundData = array(
                'account_id' => $account_id,
                'club_id' => $club_id,
                'round_no' => $i,
                'start_datetime' => $start_datetime,
                'end_datetime' => $end_datetime,
                'payment_debit_datetime' => $payment_debit_datetime,
                'status' => 1,
                'created'            =>  date('Y-m-d H:i:s'),
                'created_by'         =>  $loggedUser['id']
            );
            $this->db->insert('club_rounds',$roundData);
        }   
            
			 
        return true;

    }

    public function updateClubRound($post)
    {       
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $account_id = $this->User->get_domain_account();

        $roundID = $post['roundID'];
        $club_id = $post['club_id'];

        //get member list
        $clubData = $this->db->get_where('club_list',array('id'=>$club_id,'account_id'=>$account_id))->row_array();
        $tenure_type = $clubData['tenure_type'];
        $duration = $clubData['duration'];
        $payment_debit_duration = $clubData['payment_debit_duration'];
        //get member list
        $roundData = $this->db->get_where('club_rounds',array('id'=>$roundID,'account_id'=>$account_id))->row_array();
        $round_no = $roundData['round_no'];
        $start_datetime = $post['start_date'].' '.$post['auction_hour'].':'.$post['auction_min'].':'.$post['auction_sec'];
        $end_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' + '.$duration.' minute'));
        $payment_debit_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' - '.$payment_debit_duration.' minute'));
        $this->db->where('id',$roundID);
        $this->db->update('club_rounds',array('start_datetime'=>$start_datetime,'end_datetime'=>$end_datetime,'payment_debit_datetime'=>$payment_debit_datetime,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedUser['id']));
        if(isset($post['all']))
        {
            $nextRoundList = $this->db->order_by('round_no','ASC')->get_where('club_rounds',array('club_id'=>$club_id,'round_no >'=>$round_no))->result_array();
            if($nextRoundList)
            {
                foreach($nextRoundList as $list)
                {
                    $recordID = $list['id'];

                    if($tenure_type == 1)
                    {
                        $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 1 days'));
                    }
                    elseif($tenure_type == 2)
                    {
                        $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 7 days'));
                    }
                    elseif($tenure_type == 3)
                    {
                        $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 15 days'));
                    }
                    elseif($tenure_type == 4)
                    {
                        $start_datetime = date('Y-m-d H:i:s', strtotime($start_datetime. ' + 1 months'));
                    }

                    $end_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' + '.$duration.' minute'));
                    $payment_debit_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' - '.$payment_debit_duration.' minute'));

                    $this->db->where('id',$recordID);
                    $this->db->update('club_rounds',array('start_datetime'=>$start_datetime,'end_datetime'=>$end_datetime,'payment_debit_datetime'=>$payment_debit_datetime,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>$loggedUser['id']));

                }
            }
        }
             
        return true;

    }

    

}


/* end of file: az.php */
/* Location: ./application/models/az.php */