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

  
  public function bbpsMobilePrepaidAuth($post,$member_id)
  {

    
      $service_id = 23;
      $account_id = $this->User->get_domain_account();
      $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
      $billerID = $post['billerID'];
      $params = isset($post['params']) ? $post['params'] : array();
      // get biller id
      $get_biller_id = $this->User->get_bbps_biller_id($billerID);
      $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
      $operator_name = isset($get_biller_id['billerAliasName']) ? $get_biller_id['billerAliasName'] : '';
      $billerName = isset($get_biller_id['billerName']) ? $get_biller_id['billerName'] : '';
      
      // get pmr service id
      //$get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
      //$pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

      // generate recharge unique id
      $recharge_unique_id = rand(1111,9999).time();

      // generate reference id
      $refId = $this->User->generate_bbps_reference_id($recharge_unique_id);

      // get account balance
      $memberDetail =$this->db->get_where('users',array('id'=>$member_id))->row_array();
      
      $memberName = $memberDetail['name'];
      $memberMobile = $memberDetail['mobile'];
      $memberEmail = $memberDetail['email'];
      

      if($post['operator'] == 31)

      {
        $op_name = 'JIO';
      }
      elseif($post['operator'] == 2)

      {
        $op_name = 'VODAFONE';
      }
      if($post['operator'] == 1)

      {
        $op_name = 'AIRTEL';
      }
      elseif($post['operator'] == 3 || $post['operator'] == 4 )
      {
        $op_name = 'BSNL';
      }
      elseif($post['operator'] == 22)
      {
        $op_name = 'MTNL';
      }


      $user_before_balance = $this->User->getMemberWalletBalanceSP($member_id);

      $user_after_balance = $user_before_balance - $post['amount'];

      $rechargeData = array(
          'account_id'         => $account_id,
          'member_id'          => $member_id,
          'api_id'             => BBPS_API_ID,
          'is_bbps_api'        => 1,
          'service_id'         => $service_id,
          'biller_id'          => $op_name,
          'recharge_display_id'=> $recharge_unique_id,
          'mobile'             => isset($post['mobile']) ? $post['mobile'] : '',
          'account_number'     => isset($post['mobile']) ? $post['mobile'] : '',
          'operator_code'      => $op_name,
          'circle_code'        => '',
          'amount'             => $post['amount'],
          'before_balance'     => $user_before_balance,
          'after_balance'      => $user_after_balance,
          'status'             => 1,
          'reference_id'       => $refId,
          'created'            => date('Y-m-d H:i:s')                  
      );
      
      $this->db->insert('bbps_history',$rechargeData);
      $recharge_id = $this->db->insert_id();

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Record Saved - Record ID - '.$recharge_id.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

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
          'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Deducted.'
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
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Deduction - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      $bill_pay_respone = $this->User->call_mobikwik_recharge_electricity_bill_pay_api($member_id,$post,$recharge_unique_id);

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($bill_pay_respone).'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);


      if($bill_pay_respone['status'] == 1 || $bill_pay_respone['status'] == 2)
      {
          if($bill_pay_respone['status'] == 1)
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>2,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Success Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision Start]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
            // distribute commision
            $this->User->distribute_bbps_commision($recharge_id,$recharge_unique_id,$post['amount'],$member_id);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision End]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }
          else
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>1,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Pending Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }

          if($bill_pay_respone['status'] == 1)
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment Successfully Credited.'
            );
          }
          else
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment is Proceeded, Status will be updated soon.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Failed Status Updated.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

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
              'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Refund.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Refunded - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

          $errors = $bill_pay_respone['errors'];
          
          $final_api_response = array(
            'status' => 0,
            'msg' => $errors
          );
      }

      return $final_api_response;
  }


  public function bbpsMobilePostpaidAuth($post,$member_id)
  {

    
      $service_id = 3;
      $account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
      $billerID = $post['billerID'];
      $params = isset($post['params']) ? $post['params'] : array();
      // get biller id
      $get_biller_id = $this->User->get_bbps_biller_id($billerID);
      $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
      $operator_name = isset($get_biller_id['billerAliasName']) ? $get_biller_id['billerAliasName'] : '';
      $billerName = isset($get_biller_id['billerName']) ? $get_biller_id['billerName'] : '';
      
      // get pmr service id
      //$get_pmr_service_id = $this->User->get_bbps_pmr_service_id($service_id);
      //$pmr_service_id = isset($get_pmr_service_id['service_id']) ? $get_pmr_service_id['service_id'] : 0 ;

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
          'mobile'             => isset($params[0]) ? $params[0] : '',
          'account_number'     => isset($params[0]) ? $params[0] : '',
          'operator_code'      => $operator_name,
          'circle_code'        => '',
          'amount'             => $post['amount'],
          'before_balance'     => $user_before_balance,
          'after_balance'      => $user_after_balance,
          'status'             => 1,
          'reference_id'       => $refId,
          'created'            => date('Y-m-d H:i:s')                  
      );
      
      $this->db->insert('bbps_history',$rechargeData);
      $recharge_id = $this->db->insert_id();

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Record Saved - Record ID - '.$recharge_id.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

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
          'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Deducted.'
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
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Deduction - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      $bill_pay_respone = $this->User->call_mobikwik_bbps_electricity_bill_pay_api($member_id,$biller_payu_id,$pmr_service_id,$post,$recharge_unique_id,$billerName);

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($bill_pay_respone).'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      if($bill_pay_respone['status'] == 1 || $bill_pay_respone['status'] == 2)
      {
          if($bill_pay_respone['status'] == 1)
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>2,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Success Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision Start]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
            // distribute commision
            $this->User->distribute_bbps_commision($recharge_id,$recharge_unique_id,$post['amount'],$member_id);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision End]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }
          else
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>1,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Pending Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }

          if($bill_pay_respone['status'] == 1)
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment Successfully Credited.'
            );
          }
          else
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment is Proceeded, Status will be updated soon.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Failed Status Updated.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

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
              'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Refund.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Refunded - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

          $errors = $bill_pay_respone['errors'];
          
          $final_api_response = array(
            'status' => 0,
            'msg' => $errors
          );
      }

      return $final_api_response;
  }


  public function bbpsElectricityAuth($post,$member_id)
  {
      $service_id = 4;
      $account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
      $billerID = $post['billerID'];
      $params = isset($post['params']) ? $post['params'] : array();
      // get biller id
      $get_biller_id = $this->User->get_bbps_biller_id($billerID);
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
          'account_number'     => isset($params[0]) ? $params[0] : '',
          'operator_code'      => $operator_name,
          'circle_code'        => '',
          'amount'             => $post['amount'],
          'before_balance'     => $user_before_balance,
          'after_balance'      => $user_after_balance,
          'status'             => 1,
          'reference_id'       => $refId,
          'json_params'        => json_encode($customerParams),
          'created'            => date('Y-m-d H:i:s')                  
      );
      
      $this->db->insert('bbps_history',$rechargeData);
      $recharge_id = $this->db->insert_id();

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Record Saved - Record ID - '.$recharge_id.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

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
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Deduction - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);


      $bill_pay_respone = $this->User->call_mobikwik_bbps_electricity_bill_pay_api($member_id,$biller_payu_id,$pmr_service_id,$post,$recharge_unique_id,$billerName);

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($bill_pay_respone).'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      if($bill_pay_respone['status'] == 1 || $bill_pay_respone['status'] == 2)
      {
          if($bill_pay_respone['status'] == 1)
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>2,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Success Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision Start]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
            // distribute commision
            $this->User->distribute_bbps_commision($recharge_id,$recharge_unique_id,$post['amount'],$member_id);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision End]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

          }
          else
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>1,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Pending Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }

          if($bill_pay_respone['status'] == 1)
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment Successfully Credited.'
            );
          }
          else
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment is Proceeded, Status will be updated soon.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Failed Status Updated.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - E-Wallet Refunded - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

          $errors = $bill_pay_respone['errors'];
          $final_api_response = array(
            'status' => 0,
            'msg' => $errors
          );
      }

      return $final_api_response;
  }


  public function bbpsDTHAuth($post,$member_id)
  {
      $service_id = 13;
      $account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
      $billerID = $post['billerID'];
      $params = isset($post['params']) ? $post['params'] : array();
      // get biller id
      $get_biller_id = $this->User->get_bbps_biller_id($billerID);
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
          'mobile'             => isset($params[0]) ? $params[0] : '',
          'account_number'     => isset($params[0]) ? $params[0] : '',
          'operator_code'      => $operator_name,
          'circle_code'        => '',
          'amount'             => $post['amount'],
          'before_balance'     => $user_before_balance,
          'after_balance'      => $user_after_balance,
          'status'             => 1,
          'reference_id'       => $refId,
          'created'            => date('Y-m-d H:i:s')                  
      );
      
      $this->db->insert('bbps_history',$rechargeData);
      $recharge_id = $this->db->insert_id();

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Record Saved - Record ID - '.$recharge_id.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

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
          'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Deducted.'
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
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Deduction - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      $bill_pay_respone = $this->User->call_mobikwik_bbps_electricity_bill_pay_api($member_id,$biller_payu_id,$pmr_service_id,$post,$recharge_unique_id,$billerName);

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($bill_pay_respone).'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      if($bill_pay_respone['status'] == 1 || $bill_pay_respone['status'] == 2)
      {
          if($bill_pay_respone['status'] == 1)
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>2,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Success Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision Start]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
            // distribute commision
            $this->User->distribute_bbps_commision($recharge_id,$recharge_unique_id,$post['amount'],$member_id);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision End]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }
          else
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>1,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Pending Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }

          if($bill_pay_respone['status'] == 1)
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment Successfully Credited.'
            );
          }
          else
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment is Proceeded, Status will be updated soon.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Failed Status Updated.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

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
              'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Refund.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Refunded - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

          $errors = $bill_pay_respone['errors'];
          
          $final_api_response = array(
            'status' => 0,
            'msg' => $errors
          );
      }

      return $final_api_response;
  }

  public function bbpsMasterBillPayAuth($post,$member_id,$service_id)
  {
      $account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
      $billerID = $post['billerID'];
      $params = isset($post['params']) ? $post['params'] : array();
      // get biller id
      $get_biller_id = $this->User->get_bbps_biller_id($billerID);
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
          'mobile'             => isset($params[0]) ? $params[0] : '',
          'account_number'     => isset($params[0]) ? $params[0] : '',
          'operator_code'      => $operator_name,
          'circle_code'        => '',
          'amount'             => $post['amount'],
          'before_balance'     => $user_before_balance,
          'after_balance'      => $user_after_balance,
          'status'             => 1,
          'reference_id'       => $refId,
          'created'            => date('Y-m-d H:i:s')                  
      );
      
      $this->db->insert('bbps_history',$rechargeData);
      $recharge_id = $this->db->insert_id();

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Record Saved - Record ID - '.$recharge_id.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

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
          'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Deducted.'
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
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Deduction - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      $bill_pay_respone = $this->User->call_mobikwik_bbps_electricity_bill_pay_api($member_id,$biller_payu_id,$pmr_service_id,$post,$recharge_unique_id,$billerName);

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($bill_pay_respone).'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      if($bill_pay_respone['status'] == 1 || $bill_pay_respone['status'] == 2)
      {
          if($bill_pay_respone['status'] == 1)
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>2,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Success Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision Start]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
            // distribute commision
            $this->User->distribute_bbps_commision($recharge_id,$recharge_unique_id,$post['amount'],$member_id);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision End]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }
          else
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>1,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Pending Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }

          if($bill_pay_respone['status'] == 1)
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment Successfully Credited.'
            );
          }
          else
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment is Proceeded, Status will be updated soon.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Failed Status Updated.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

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
              'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Refund.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Refunded - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

          $errors = $bill_pay_respone['errors'];
          
          $final_api_response = array(
            'status' => 0,
            'msg' => $errors
          );
      }

      return $final_api_response;
  }


  //credit card bill payment

  public function bbpsCreditBillPayAuth($post,$member_id,$service_id)
  {
      $account_id = $this->User->get_domain_account();
       $loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
      $billerID = 604;
     
      // get biller id
      $get_biller_id = $this->User->get_bbps_biller_id($billerID);
      $biller_payu_id = isset($get_biller_id['biller_id']) ? $get_biller_id['biller_id'] : '';
      $operator_name = isset($get_biller_id['billerAliasName']) ? $get_biller_id['billerAliasName'] : '';
      $billerName = isset($get_biller_id['billerName']) ? $get_biller_id['billerName'] : '';
      
      // get pmr service id
     
      $pmr_service_id = $service_id;

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
          'mobile'             => $post['canumber'],
          'account_number'     => $post['canumber'],
          'operator_code'      => $operator_name,
          'circle_code'        => '',
          'amount'             => $post['amount'],
          'before_balance'     => $user_before_balance,
          'after_balance'      => $user_after_balance,
          'status'             => 1,
          'reference_id'       => $refId,
          'created'            => date('Y-m-d H:i:s')                  
      );
      
      $this->db->insert('bbps_history',$rechargeData);
      $recharge_id = $this->db->insert_id();

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Record Saved - Record ID - '.$recharge_id.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

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
          'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Deducted.'
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
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Deduction - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      $bill_pay_respone = $this->User->call_mobikwik_bbps_electricity_bill_pay_api($member_id,$biller_payu_id,$pmr_service_id,$post,$recharge_unique_id,$billerName);

      // save system log
      $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay Final Response - '.json_encode($bill_pay_respone).'.]'.PHP_EOL;
      $this->User->generateBBPSLog($log_msg);

      if($bill_pay_respone['status'] == 1 || $bill_pay_respone['status'] == 2)
      {
          if($bill_pay_respone['status'] == 1)
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>2,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Success Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision Start]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
            // distribute commision
            $this->User->distribute_bbps_commision($recharge_id,$recharge_unique_id,$post['amount'],$member_id);
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Distribute Commision End]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }
          else
          {
            // update recharge status
            $this->db->where('id',$recharge_id);
            $this->db->where('recharge_display_id',$recharge_unique_id);
            $this->db->update('bbps_history',array('status'=>1,'txid'=>$bill_pay_respone['txnid'],'operator_ref'=>$bill_pay_respone['txnid'],'api_response_id'=>$bill_pay_respone['response_id']));

            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Pending Status Updated.]'.PHP_EOL;
            $this->User->generateBBPSLog($log_msg);
          }

          if($bill_pay_respone['status'] == 1)
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment Successfully Credited.'
            );
          }
          else
          {
            $final_api_response = array(
              'status' => 1,
              'msg' => 'Congratulation ! Your Bill Payment is Proceeded, Status will be updated soon.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - Bill Pay #'.$recharge_unique_id.' Failed Status Updated.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

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
              'description'         => 'Bill Pay #'.$recharge_unique_id.' Amount Refund.'
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
          $log_msg = '['.date('d-m-Y H:i:s').' - MD('.$loggedUser['user_code'].') - R-Wallet Refunded - Updated Balance - '.$user_after_balance.'.]'.PHP_EOL;
          $this->User->generateBBPSLog($log_msg);

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