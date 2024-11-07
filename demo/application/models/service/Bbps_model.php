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

class Bbps_model extends CI_Model {

  public function __construct() {
      parent::__construct();
  }

  public function bbpsElectricityAuth($post,$member_id,$memberCode = '')
  {
      $service_id = 4;
      $account_id = $this->User->get_domain_account();
      $loggedUser = array();
      $loggedUser['user_code'] = $memberCode;
      $biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';
      // get biller system id
      $get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
      $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
      $operator_name = isset($get_biller_id['billerAliasName']) ? $get_biller_id['billerAliasName'] : '';
      $billerName = isset($get_biller_id['billerName']) ? $get_biller_id['billerName'] : '';

      // get pmr service id
      $get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
      $pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;
      
      // generate recharge unique id
      $recharge_unique_id = rand(1111,9999).time();

      // generate reference id
      $refId = $this->User->generate_bbps_reference_id($recharge_unique_id);

      // get account balance
      $memberDetail =$this->db->get_where('users',array('id'=>$member_id))->row_array();
      
      $memberName = $memberDetail['name'];
      $memberMobile = $memberDetail['mobile'];
      $memberEmail = $memberDetail['email'];

      $user_before_balance = $this->User->getMemberWalletBalanceSP($member_id);

      $user_after_balance = $user_before_balance - $post['amount'];

      $rechargeData = array(
          'account_id'         => $account_id,
          'member_id'          => $member_id,
          'api_id'             => BBPS_API_ID,
          'is_bbps_api'        => 1,
          'service_id'         => $service_id,
          'biller_id'          => $billerID,
          'recharge_display_id'=> $recharge_unique_id,
          'mobile'             => $memberMobile,
          'account_number'     => isset($post['para1']) ? $post['para1'] : '',
          'operator_code'      => $operator_name,
          'circle_code'        => '',
          'amount'             => $post['amount'],
          'before_balance'     => $user_before_balance,
          'after_balance'      => $user_after_balance,
          'status'             => 1,
          'reference_id'       => $refId,
          'is_from_app'        => 1,
          'created'            => date('Y-m-d H:i:s')                  
      );
      
      $this->db->insert('bbps_history',$rechargeData);
      $recharge_id = $this->db->insert_id();

      log_message('debug', 'User ('.$loggedUser['user_code'].') - Record Saved - Record ID - '.$recharge_id);

      $wallet_data = array(
          'account_id'          => $account_id,
          'member_id'           => $member_id,    
          'before_balance'      => $user_before_balance,
          'amount'              => $post['amount'],  
          'after_balance'       => $user_after_balance,      
          'status'              => 1,
          'type'                => 2, 
          'wallet_type'         => 1,    
          'created'             => date('Y-m-d H:i:s'),      
          'description'         => 'Bill #'.$recharge_unique_id.' Pay Amount Deducted.'
      );

      $this->db->insert('member_wallet',$wallet_data);

      $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
      if($is_cogent_instantpay_api)
      {
          $admin_id = $this->User->get_admin_id($account_id);
          $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
          $admin_after_wallet_balance = $admin_before_wallet_balance - $post['amount'];

          $wallet_data = array(
              'account_id'          => $account_id,
              'member_id'           => $admin_id,    
              'before_balance'      => $admin_before_wallet_balance,
              'amount'              => $post['amount'],  
              'after_balance'       => $admin_after_wallet_balance,      
              'status'              => 1,
              'type'                => 2,   
              'wallet_type'         => 1,   
              'created'             => date('Y-m-d H:i:s'),      
              'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Deducted.'
          );

          $this->db->insert('virtual_wallet',$wallet_data);

      }

      log_message('debug', 'User ('.$loggedUser['user_code'].') - R-Wallet Deduction - Updated Balance - '.$user_after_balance);

      $post['params'][0] = isset($post['para1']) ? $post['para1'] : '';
      $post['params'][1] = isset($post['para2']) ? $post['para2'] : '';
      $post['params'][2] = isset($post['para3']) ? $post['para3'] : '';
      $post['params'][3] = isset($post['para4']) ? $post['para4'] : '';
      $post['params'][4] = isset($post['para5']) ? $post['para5'] : '';

      $bill_pay_respone = $this->User->call_bbps_electricity_bill_pay_api($member_id,$biller_payu_id,$pmr_service_id,$post,$recharge_unique_id,$billerName);
      
      log_message('debug', 'User ('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($bill_pay_respone));

      if($bill_pay_respone['status'] == 1 || $bill_pay_respone['status'] == 2)
      {
          if($bill_pay_respone['status'] == 1)
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>2,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            log_message('debug', 'User ('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Success Status Updated.');

            log_message('debug', 'User ('.$loggedUser['user_code'].') - Distribute Commision Start');
            // distribute commision
            $this->User->distribute_bbps_commision($recharge_id,$recharge_unique_id,$post['amount'],$member_id);
            
            log_message('debug', 'User ('.$loggedUser['user_code'].') - Distribute Commision End');

          }
          else
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>1,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            
            log_message('debug', 'User ('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Pending Status Updated.');
          }

          if($bill_pay_respone['status'] == 1)
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment Successfully Credited.',
              'txnid' => $bill_pay_respone['txnid']
            );
          }
          else
          {
            $final_api_response = array(
              'status' => 2,
              'msg' => 'Congratulation ! Your Bill Payment is Proceeded, Status will be updated soon.',
              'txnid' => $bill_pay_respone['txnid']
            );
          }
      }
      else
      {
          // update recharge status
          $this->db->where('id',$recharge_id);
          $this->db->where('recharge_display_id',$recharge_unique_id);
          $this->db->update('bbps_history',array('status'=>3));

          
          log_message('debug', 'User ('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Failed Status Updated.');

          // get account balance
          $memberDetail =$this->db->get_where('users',array('id'=>$member_id))->row_array();
          $user_before_balance = $this->User->getMemberWalletBalanceSP($member_id);
          $user_after_balance = $user_before_balance + $post['amount'];
          $wallet_data = array(
              'account_id'          => $account_id,
              'member_id'           => $member_id,    
              'before_balance'      => $user_before_balance,
              'amount'              => $post['amount'],  
              'after_balance'       => $user_after_balance,      
              'status'              => 1,
              'type'                => 1, 
              'wallet_type'         => 1,       
              'created'             => date('Y-m-d H:i:s'),      
              'description'         => 'Bill #'.$recharge_unique_id.' Pay Amount Refund.'
          );

          $this->db->insert('member_wallet',$wallet_data);

          $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
          if($is_cogent_instantpay_api)
          {
              $admin_id = $this->User->get_admin_id($account_id);
              $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
              $admin_after_wallet_balance = $admin_before_wallet_balance + $post['amount'];

              $wallet_data = array(
                  'account_id'          => $account_id,
                  'member_id'           => $admin_id,    
                  'before_balance'      => $admin_before_wallet_balance,
                  'amount'              => $post['amount'],  
                  'after_balance'       => $admin_after_wallet_balance,      
                  'status'              => 1,
                  'type'                => 1,   
                  'wallet_type'         => 1,   
                  'created'             => date('Y-m-d H:i:s'),      
                  'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Refund.'
              );

              $this->db->insert('virtual_wallet',$wallet_data);

          }

          log_message('debug', 'User ('.$loggedUser['user_code'].') - E-Wallet Refunded - Updated Balance - '.$user_after_balance);

          $errors = $bill_pay_respone['errors'];
          
          $final_api_response = array(
            'status' => 0,
            'msg' => $errors
          );
      }

      return $final_api_response;
  }

  public function bbpsMasterBillPayAuth($post,$member_id,$service_id,$memberCode = '')
  {
      $account_id = $this->User->get_domain_account();
      $loggedUser = array();
      $loggedUser['user_code'] = $memberCode;
      $biller_id = isset($post['biller_id']) ? $post['biller_id'] : '';

      // get biller system id
      $get_biller_id = $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'biller_id'=>$biller_id))->row_array();
      $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
      $operator_name = isset($get_biller_id['billerAliasName']) ? $get_biller_id['billerAliasName'] : '';
      $billerName = isset($get_biller_id['billerName']) ? $get_biller_id['billerName'] : '';
      
      // get pmr service id
      $get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
      $pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

      // generate recharge unique id
      $recharge_unique_id = rand(1111,9999).time();

      // generate reference id
      $refId = $this->User->generate_bbps_reference_id($recharge_unique_id);

      // get account balance
      $memberDetail =$this->db->get_where('users',array('id'=>$member_id))->row_array();
      
      $memberName = $memberDetail['name'];
      $memberMobile = $memberDetail['mobile'];
      $memberEmail = $memberDetail['email'];

      $user_before_balance = $this->User->getMemberWalletBalanceSP($member_id);

      $user_after_balance = $user_before_balance - $post['amount'];

      $rechargeData = array(
          'account_id'         => $account_id,
          'member_id'          => $member_id,
          'api_id'             => BBPS_API_ID,
          'is_bbps_api'        => 1,
          'service_id'         => $service_id,
          'biller_id'          => $billerID,
          'recharge_display_id'=> $recharge_unique_id,
          'mobile'             => isset($post['para1']) ? $post['para1'] : '',
          'account_number'     => isset($post['para1']) ? $post['para1'] : '',
          'operator_code'      => $operator_name,
          'circle_code'        => '',
          'amount'             => $post['amount'],
          'before_balance'     => $user_before_balance,
          'after_balance'      => $user_after_balance,
          'status'             => 1,
          'reference_id'       => $refId,
          'is_from_app'        => 1,
          'created'            => date('Y-m-d H:i:s')                  
      );
      
      $this->db->insert('bbps_history',$rechargeData);
      $recharge_id = $this->db->insert_id();

      log_message('debug', 'User ('.$loggedUser['user_code'].') - Record Saved - Record ID - '.$recharge_id);


      $wallet_data = array(
          'account_id'          => $account_id,
          'member_id'           => $member_id,    
          'before_balance'      => $user_before_balance,
          'amount'              => $post['amount'],  
          'after_balance'       => $user_after_balance,      
          'status'              => 1,
          'type'                => 2, 
          'wallet_type'         => 1,     
          'created'             => date('Y-m-d H:i:s'),      
          'description'         => 'Bill #'.$recharge_unique_id.' Pay Amount Deducted.'
      );

      $this->db->insert('member_wallet',$wallet_data);

      $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
      if($is_cogent_instantpay_api)
      {
          $admin_id = $this->User->get_admin_id($account_id);
          $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
          $admin_after_wallet_balance = $admin_before_wallet_balance - $post['amount'];

          $wallet_data = array(
              'account_id'          => $account_id,
              'member_id'           => $admin_id,    
              'before_balance'      => $admin_before_wallet_balance,
              'amount'              => $post['amount'],  
              'after_balance'       => $admin_after_wallet_balance,      
              'status'              => 1,
              'type'                => 2,   
              'wallet_type'         => 1,   
              'created'             => date('Y-m-d H:i:s'),      
              'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Deducted.'
          );

          $this->db->insert('virtual_wallet',$wallet_data);

      }

      // save system log
      log_message('debug', 'User ('.$loggedUser['user_code'].') - R-Wallet Deduction - Updated Balance - '.$user_after_balance);

      if($pmr_service_id == 32)
      {
          $post['number'] = isset($post['para1']) ? $post['para1'] : '';
            
      }
      else
      {
          $post['params'][0] = isset($post['para1']) ? $post['para1'] : '';
          $post['params'][1] = isset($post['para2']) ? $post['para2'] : '';
          $post['params'][2] = isset($post['para3']) ? $post['para3'] : '';
          $post['params'][3] = isset($post['para4']) ? $post['para4'] : '';
          $post['params'][4] = isset($post['para5']) ? $post['para5'] : '';
      }

      $bill_pay_respone = $this->User->call_bbps_service_bill_pay_api($member_id,$biller_payu_id,$pmr_service_id,$post,$recharge_unique_id,$billerName);
      // save system log
      log_message('debug', 'User ('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($bill_pay_respone));

      if($bill_pay_respone['status'] == 1 || $bill_pay_respone['status'] == 2)
      {
          if($bill_pay_respone['status'] == 1)
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>2,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            log_message('debug', 'User ('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Success Status Updated.');

            // save system log
            log_message('debug', 'User ('.$loggedUser['user_code'].') - Distribute Commision Start');

            // distribute commision
            $this->User->distribute_bbps_commision($recharge_id,$recharge_unique_id,$post['amount'],$member_id);
            // save system log
            log_message('debug', 'User ('.$loggedUser['user_code'].') - Distribute Commision End');
          }
          else
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>1,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            log_message('debug', 'User ('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Pending Status Updated.');
          }

          if($bill_pay_respone['status'] == 1)
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment Successfully Credited.',
              'txnid' => $bill_pay_respone['txnid']
            );
          }
          else
          {
            $final_api_response = array(
              'status' => 2,
              'msg' => 'Congratulation ! Your Bill Payment is Proceeded, Status will be updated soon.',
              'txnid' => $bill_pay_respone['txnid']
            );
          }
      }
      else
      {
          // update recharge status
          $this->db->where('id',$recharge_id);
          $this->db->where('recharge_display_id',$recharge_unique_id);
          $this->db->update('bbps_history',array('status'=>3));

          // save system log
          log_message('debug', 'User ('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Failed Status Updated.');

          // get account balance
          $memberDetail =$this->db->get_where('users',array('id'=>$member_id))->row_array();
          $user_before_balance = $this->User->getMemberWalletBalanceSP($member_id);
          $user_after_balance = $user_before_balance + $post['amount'];
          $wallet_data = array(
              'account_id'          => $account_id,
              'member_id'           => $member_id,    
              'before_balance'      => $user_before_balance,
              'amount'              => $post['amount'],  
              'after_balance'       => $user_after_balance,      
              'status'              => 1,
              'type'                => 1, 
              'wallet_type'         => 1,     
              'created'             => date('Y-m-d H:i:s'),      
              'description'         => 'Bill #'.$recharge_unique_id.' Pay Amount Refund.'
          );

          $this->db->insert('member_wallet',$wallet_data);

          $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
          if($is_cogent_instantpay_api)
          {
              $admin_id = $this->User->get_admin_id($account_id);
              $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
              $admin_after_wallet_balance = $admin_before_wallet_balance + $post['amount'];

              $wallet_data = array(
                  'account_id'          => $account_id,
                  'member_id'           => $admin_id,    
                  'before_balance'      => $admin_before_wallet_balance,
                  'amount'              => $post['amount'],  
                  'after_balance'       => $admin_after_wallet_balance,      
                  'status'              => 1,
                  'type'                => 1,   
                  'wallet_type'         => 1,   
                  'created'             => date('Y-m-d H:i:s'),      
                  'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Refund.'
              );

              $this->db->insert('virtual_wallet',$wallet_data);
              
          }

          // save system log
          log_message('debug', 'User ('.$loggedUser['user_code'].') - E-Wallet Refunded - Updated Balance - '.$user_after_balance);

          $errors = $bill_pay_respone['errors'];
          $final_api_response = array(
            'status' => 0,
            'msg' => $errors
          );
      }

      return $final_api_response;
  }



}


/* end of file: az.php */
/* Location: ./application/models/az.php */