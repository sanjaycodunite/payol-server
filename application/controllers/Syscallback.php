<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Syscallback extends CI_Controller
{

    public function __construct() {
        parent::__construct();
         $this->load->model('admin/Jwt_model');
    
    }
    
    public function updateWalletOpeningBalance()
    {
        $userList = $this->db->get_where('users',array('account_id'=>2,'role_id >'=>2))->result_array();
        if($userList)
        {
            foreach($userList as $list)
            {
                $memberID = $list['id'];
                $user_code = $list['user_code'];
                $name = $list['name'];
                $getOpeningBalance = $this->db->query("SELECT ROUND(SUM((CASE WHEN type = 1 THEN amount ELSE CONCAT('-',amount) END)),2) as amount FROM tbl_member_wallet WHERE member_id = ".$memberID." and wallet_type = 1 AND DATE(created) < '2023-11-01'")->row_array();
                $openingBal = isset($getOpeningBalance['amount']) ? $getOpeningBalance['amount'] : 0 ;
                echo $name.' ('.$user_code.') Member ID : '.$memberID.' Opening Bal : '.$openingBal.'<br />';
            }
        }
        die('SUCCESS');
    }
    
    public function testapi()
    {
        $txid = time().rand(1111,9999);
        $utr_no = rand(11111111111,99999999999);
        $callback_url ='https://paymyrecharge.in/myapi/dmr/MorningwalletRequest.aspx';
            //$api_url = '?transcionid='.$txnid.'&utrno='.$post['utr_no'].'&status=SUCCESS&memberID='.$post['member'].'&Amount='.$post['amount'].'&reason='.$post['description'].'';
            $user_callback_data_url  = 'https://paymyrecharge.in/myapi/dmr/MorningwalletRequest.aspx?transcionid=202310281055588&utrno=123456789&status=SUCCESS&memberID=242&Amount=20.00&reason=This+is+testing';
                    
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			
			//curl_setopt($ch, CURLOPT_POST, true);
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
			echo $output = curl_exec($ch); 
			curl_close($ch);
			die;
        //header("Host: https://digitalseva.csc.gov.in"); 
        $api_url = 'https://digitalseva.csc.gov.in/newbbps/billenquiry';
        #$api_url = 'https://www.bitraxcapital.com/cron/testapi';
#$_SERVER['SERVER_NAME'] = 'https://digitalseva.csc.gov.in';
#$_SERVER['HTTP_HOST'] = 'www.digitalseva.csc.gov.in';


$api_post_data = array(
	'biller_id' => 'JVVNL0000RAJ01',
	'mobile' => '8104758957',
	'local_agent' => '7e05884ce5894c80eaf65ae4baa8e4b6c1d8efeffa43f4d7f0eeaa4de7252a2d25977281dc3df3f7cecef628645c5bc735cc1d8b4f6f0d69d5d9c22d6f2fc16e+Ea7Pur2rcJE8EbMC0FTkMeY1k8o1lcMFcc8+CVd0jOtbuN4mx0KT0zhduI76O5kTcYZxHabKBrXslQVJ5YGLY7PK71n4xuIegjJ9tu3jh4zCo9vpGYx5jCi3hoLtbxKDz/YrDY5upfHl6cOblEaxzIyn61+gcXC/EqHtaQR+Wba6S/rPcXFqfo8WL5Wbl4WRsRmgx0fxqr4SOMQ2EVVKSLbmxZyfk2M8dIV9SafVA==',
	'bbps_param_1' => '210511003700',
	'csrf_cscportal_token2' => '28fa56f94eb35c6892debf19defe42b9'
);

$header = array(
	'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
	'Cookie:digitalseva=bm86g016j5kgbu4ru0c4dvscvmvf7qgu; digitalsevacsrf_cookie_name2=28fa56f94eb35c6892debf19defe42b9',
	'Host:digitalseva.csc.gov.in',
	'Origin:https://digitalseva.csc.gov.in',
	'Referer:https://digitalseva.csc.gov.in/services/electricity'
);

$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $api_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));		
	echo $output = curl_exec($ch); 
	curl_close($ch);
    }
    
    
    
    public function memberOpeningBal()
    {
        $current_date = date('Y-m-d');
        $balance_date = date('Y-m-d',strtotime("-1 days"));
        $memberList = $this->db->get_where('users',array('account_id'=>2,'role_id >'=>2))->result_array();
        if($memberList)
        {
            foreach($memberList as $list)
            {
                $memberID = $list['id'];
                $getOpenBal = $this->db->query("SELECT *  FROM `tbl_member_wallet` WHERE DATE(created) < '".$balance_date."' AND member_id = '".$memberID."' ORDER BY id DESC")->row_array();
                $openBalance = isset($getOpenBal['after_balance']) ? $getOpenBal['after_balance'] : 0 ;
                
                $getCloseBal = $this->db->query("SELECT *  FROM `tbl_member_wallet` WHERE DATE(created) < '".$current_date."' AND member_id = '".$memberID."' ORDER BY id DESC")->row_array();
                $closeBalance = isset($getCloseBal['after_balance']) ? $getCloseBal['after_balance'] : 0 ;
                
                $getManualCredit = $this->db->query("SELECT SUM(amount) as totalManualCredit FROM `tbl_member_wallet` WHERE `member_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND is_manual = 1 AND type = 1")->row_array();
                $manualCredit = isset($getManualCredit['totalManualCredit']) ? $getManualCredit['totalManualCredit'] : 0 ;
                
                $getManualDebit = $this->db->query("SELECT SUM(amount) as totalManualCredit FROM `tbl_member_wallet` WHERE `member_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND is_manual = 1 AND type = 2")->row_array();
                $manualDebit = isset($getManualDebit['totalManualCredit']) ? $getManualDebit['totalManualCredit'] : 0 ;
                
                $getTotalPending = $this->db->query("SELECT SUM(total_wallet_charge) as totalManualCredit FROM `tbl_user_new_fund_transfer` WHERE `user_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (2)")->row_array();
                $totalPending = isset($getTotalPending['totalManualCredit']) ? $getTotalPending['totalManualCredit'] : 0 ;
                
                $getTotalPending2 = $this->db->query("SELECT SUM(total_wallet_charge) as totalManualCredit FROM `tbl_user_money_transfer` WHERE `user_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (2)")->row_array();
                $totalPending2 = isset($getTotalPending2['totalManualCredit']) ? $getTotalPending2['totalManualCredit'] : 0 ;
                
                $totalPending = $totalPending + $totalPending2;
                
                $getTotalSuccess = $this->db->query("SELECT SUM(total_wallet_charge) as totalManualCredit FROM `tbl_user_new_fund_transfer` WHERE `user_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (3)")->row_array();
                $totalSuccess = isset($getTotalSuccess['totalManualCredit']) ? $getTotalSuccess['totalManualCredit'] : 0 ;
                
                $getTotalSuccess2 = $this->db->query("SELECT SUM(total_wallet_charge) as totalManualCredit FROM `tbl_user_money_transfer` WHERE `user_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (3)")->row_array();
                $totalSuccess2 = isset($getTotalSuccess2['totalManualCredit']) ? $getTotalSuccess2['totalManualCredit'] : 0 ;
                
                $totalSuccess = $totalSuccess + $totalSuccess2;
                
                $getTotalFailed = $this->db->query("SELECT SUM(total_wallet_charge) as totalManualCredit FROM `tbl_user_new_fund_transfer` WHERE `user_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (4)")->row_array();
                $totalFailed = isset($getTotalFailed['totalManualCredit']) ? $getTotalFailed['totalManualCredit'] : 0 ;
                
                $getTotalFailed2 = $this->db->query("SELECT SUM(total_wallet_charge) as totalManualCredit FROM `tbl_user_money_transfer` WHERE `user_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (4)")->row_array();
                $totalFailed2 = isset($getTotalFailed2['totalManualCredit']) ? $getTotalFailed2['totalManualCredit'] : 0 ;
                
                $totalFailed = $totalFailed + $totalFailed2;
                
                $getTotalRefund = $this->db->query("SELECT SUM(total_wallet_charge) as totalManualCredit FROM `tbl_user_new_fund_transfer` WHERE `user_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (4) AND force_status = 1")->row_array();
                $totalRefund = isset($getTotalRefund['totalManualCredit']) ? $getTotalRefund['totalManualCredit'] : 0 ;
                
                $getTotalRefund2 = $this->db->query("SELECT SUM(total_wallet_charge) as totalManualCredit FROM `tbl_user_money_transfer` WHERE `user_id` = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (4) AND force_status = 1")->row_array();
                $totalRefund2 = isset($getTotalRefund2['totalManualCredit']) ? $getTotalRefund2['totalManualCredit'] : 0 ;
                
                $totalRefund = $totalRefund + $totalRefund2;
                
                $getPreviousRefundPayout = $this->db->query("SELECT SUM(total_wallet_charge) as totalManualCredit FROM `tbl_user_new_fund_transfer` WHERE user_id = '".$memberID."' AND DATE(updated) = '".$balance_date."' AND DATE(created) != '".$balance_date."' AND status = 4")->row_array();
                $previousRefundPayout = isset($getPreviousRefundPayout['totalManualCredit']) ? $getPreviousRefundPayout['totalManualCredit'] : 0 ;
                
                $getTotalRecharge = $this->db->query("SELECT SUM(amount) as totalManualCredit FROM `tbl_recharge_history` WHERE member_id = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (1,2)")->row_array();
                $totalRecharge = isset($getTotalRecharge['totalManualCredit']) ? $getTotalRecharge['totalManualCredit'] : 0 ;
                
                $getTotalBbps = $this->db->query("SELECT SUM(amount) as totalManualCredit FROM `tbl_bbps_history` WHERE member_id = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (1,2)")->row_array();
                $totalBbps = isset($getTotalBbps['totalManualCredit']) ? $getTotalBbps['totalManualCredit'] : 0 ;
                
                $getTotalAeps1 = $this->db->query("SELECT SUM(amount) as totalManualCredit FROM `tbl_instantpay_aeps_transaction` WHERE member_id = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (2)")->row_array();
                $totalAeps1 = isset($getTotalAeps1['totalManualCredit']) ? $getTotalAeps1['totalManualCredit'] : 0 ;
                
                $getTotalAeps2 = $this->db->query("SELECT SUM(amount) as totalManualCredit FROM `tbl_member_aeps_transaction` WHERE member_id = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (2)")->row_array();
                $totalAeps2 = isset($getTotalAeps2['totalManualCredit']) ? $getTotalAeps2['totalManualCredit'] : 0 ;
                
                $getTotalAeps3 = $this->db->query("SELECT SUM(amount) as totalManualCredit FROM `tbl_member_new_aeps_transaction` WHERE member_id = '".$memberID."' AND DATE(created) = '".$balance_date."' AND status IN (2)")->row_array();
                $totalAeps3 = isset($getTotalAeps3['totalManualCredit']) ? $getTotalAeps3['totalManualCredit'] : 0 ;
                
                $totalAeps = $totalAeps1 + $totalAeps2 + $totalAeps3;
                
                $getTotalComm = $this->db->query("SELECT SUM(com_amount) as totalCommission,SUM(tds_amount) as totalTds FROM `tbl_tds_report` WHERE member_id = '".$memberID."' AND DATE(created) = '".$balance_date."'")->row_array();
                $totalCommission = isset($getTotalComm['totalCommission']) ? $getTotalComm['totalCommission'] : 0 ;
                $totalTds = isset($getTotalComm['totalTds']) ? $getTotalComm['totalTds'] : 0 ;
                
                $data = array(
                    'member_id' => $memberID,
                    'balance_date' => $balance_date,
                    'opening_amount' => $openBalance,
                    'closing_amount' => $closeBalance,
                    'manual_credit' => $manualCredit,
                    'manual_debit' => $manualDebit,
                    'success_payout' => $totalSuccess,
                    'pending_payout' => $totalPending,
                    'failed_payout' => $totalFailed,
                    'refund_payout' => $totalRefund,
                    'previous_refund_payout' => $previousRefundPayout,
                    'total_recharge' => $totalRecharge,
                    'total_bbps' => $totalBbps,
                    'total_aeps' => $totalAeps,
                    'total_commission' => $totalCommission,
                    'total_tds' => $totalTds,
                    'created' => date('Y-m-d H:i:s')
                );
                $this->db->insert('member_opening_balance',$data);
                
                /*$data = array(
                    'previous_refund_payout' => $previousRefundPayout
                );
                $this->db->where('member_id',$memberID);
                $this->db->where('balance_date','2023-07-12');
                $this->db->update('member_opening_balance',$data);*/
            }
        }
        die('succees');
    }

    public function rechargeCallback($api_callback_id = '')
    {
    	$post = $this->input->get();
    	$txid = isset($post['txid']) ? $post['txid'] : '';
    	$is_bbps = isset($post['is_bbps']) ? $post['is_bbps'] : 0;
    	
    	if($is_bbps)
    	{
    		$mytxid = $post['mytxid'];
			$txid = $post['txid'];
			$optxid = $post['optxid'];
			$mobileno = $post['mobileno'];
			$api_status = strtolower($post['status']);

    		$account_id = $this->User->get_domain_account();
	    	// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - BBPS Call Back Called]'.PHP_EOL;
	        $this->User->generateCallbackLog($log_msg);

	        // save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - BBPS Call Back Get Data - '.json_encode($post).']'.PHP_EOL;
	        $this->User->generateCallbackLog($log_msg);
	        // check recharge status
			$recharge_status = $this->db->get_where('bbps_history',array('recharge_display_id'=>$txid,'status'=>1))->num_rows();
			if($recharge_status)
			{
				$status = 0;
				if($api_status == 'success')
				{
					$status = 2;
				}
				elseif($api_status == 'failed')
				{
					$status = 3;
				}
				elseif($api_status == 'pending')
				{
					$status = 1;
				}
				
				if($txid)
				{
					// update status
					$this->db->where('recharge_display_id',$txid);
					$this->db->update('bbps_history',array('txid'=>$optxid,'operator_ref'=>$optxid,'status'=>$status));

					// refund payment into wallet
					if($status == 3)
					{
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - BBPS Call Back Bill Pay Failed and Refund into wallet.]'.PHP_EOL;
				        $this->User->generateCallbackLog($log_msg);
						// get member id and amount
						$get_recharge_data = $this->db->get_where('bbps_history',array('recharge_display_id'=>$txid))->row_array();

						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - BBPS Call Back Recharge Data - '.json_encode($get_recharge_data).'.]'.PHP_EOL;
				        $this->User->generateCallbackLog($log_msg);

						$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
						$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
						$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
						$recharge_display_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : '' ;
						if($member_id)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
							$type = 1;
							
							$after_balance = $before_balance + $amount;    
							
							$wallet_data = array(
								'account_id'           => $account_id,    
								'member_id'           => $member_id,    
								'before_balance'      => $before_balance,
								'amount'              => $amount,  
								'after_balance'       => $after_balance,      
								'status'              => 1,
								'type'                => $type,      
								'wallet_type'         => 1,     
								'created'             => date('Y-m-d H:i:s'),      
								'credited_by'         => 1,
								'description'         => 'Bill Pay Refund #'.$recharge_display_id.' Credited'
					        );

					        $this->db->insert('member_wallet',$wallet_data);

					        
						}
					}
					elseif($status == 2)
					{
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - BBPS Call Back Bill Pay Success and Distribute Commision.]'.PHP_EOL;
				        $this->User->generateCallbackLog($log_msg);
						// get member id and amount
						$get_recharge_data = $this->db->get_where('bbps_history',array('recharge_display_id'=>$txid))->row_array();
						// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - BBPS Call Back Recharge Data - '.json_encode($get_recharge_data).'.]'.PHP_EOL;
				        $this->User->generateCallbackLog($log_msg);
						$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
						$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
						$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
						$recharge_id = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
						$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;

						// distribute commision
                    	$this->User->distribute_bbps_commision($recharge_id,$recharge_unique_id,$amount,$member_id);

					}
				}
					
			}
			else
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - BBPS Call Back Txn id not valid or Status already updated.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);
			}
    	}
    	else
    	{
	    	$account_id = $this->User->get_domain_account();
	    	// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Call Back Called]'.PHP_EOL;
	        $this->User->generateCallbackLog($log_msg);

	        $api_post_method = $this->security->xss_clean($this->input->post());
	        $api_get_method = $this->security->xss_clean($this->input->get());
	        // save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - API Post Data - '.json_encode($api_post_method).' API Get Data - '.json_encode($api_get_method).']'.PHP_EOL;
	        $this->User->generateCallbackLog($log_msg);

	        // check callback id
	        $chk_call_back_id = $this->db->get_where('api',array('account_id'=>$account_id,'call_back_id'=>$api_callback_id))->num_rows();
	        if($chk_call_back_id)
	        {
	        	// get api id
	        	$get_api_id = $this->db->get_where('api',array('account_id'=>$account_id,'call_back_id'=>$api_callback_id))->row_array();
	        	$api_id = isset($get_api_id['id']) ? $get_api_id['id'] : 0 ;
	        	$callback_response_type = isset($get_api_id['callback_response_type']) ? $get_api_id['callback_response_type'] : 0 ;

	        	$callback_title = ($callback_response_type == 1) ? 'GET' : 'POST';

	        	// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Call back id verified API ID - '.$api_id.' - Call Back Response Type - '.$callback_title.'.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);

		        // get method all parameters
			 	$getParaList = $this->db->get_where('api_call_back_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
			 	// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Parameter List - '.json_encode($getParaList).'.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);
		        $responsePara = array();
			 	if($getParaList)
		 		{
		 			foreach($getParaList as $rkey=>$rlist)
		 			{
		 				
		 				
		 				$value_code = '';
	 					if($rlist['value_id'] == 1)
	 						$value_code = 'TXNID';
	 					elseif($rlist['value_id'] == 2)
	 						$value_code = 'STATUS';
	 					elseif($rlist['value_id'] == 3)
	 						$value_code = 'OPTMSG';
	 					elseif($rlist['value_id'] == 4)
	 						$value_code = 'OPTREFID';
	 					elseif($rlist['value_id'] == 5)
	 						$value_code = 'TIMESTAMP';
	 					elseif($rlist['value_id'] == 6)
	 						$value_code = 'MEMBERID';
	 					elseif($rlist['value_id'] == 7)
	 						$value_code = 'BALANCE';
	 					elseif($rlist['value_id'] == 8)
	 						$value_code = 'COMMISION';

			 			$responsePara[$rkey]['key'] = isset($rlist['para_key']) ? $rlist['para_key'] : '';
			 			$responsePara[$rkey]['value'] = $value_code;
			 			$responsePara[$rkey]['success'] = $rlist['success_val'];
			 			$responsePara[$rkey]['failed'] = $rlist['failed_val'];
			 			$responsePara[$rkey]['pending'] = $rlist['pending_val'];
		 					
		 				
		 			}
		 		}

		 		if($callback_response_type == 1)
		 		{
		 			$post = $this->input->get();
		 		}
		 		else
		 		{
		 			$post = $this->security->xss_clean($this->input->post());
		 		}

		 		$txid = '';
				$recharge_status = '';
				$success_value = array();
				$failed_value = array();
				$pending_value = array();
				$opt_msg = '';
				$opt_ref_id = '';
				
				if($responsePara)
				{
					foreach($responsePara as $rlist)
					{
						if($rlist['value'] == 'TXNID')
						{
							$txid = isset($post[$rlist['key']]) ? $post[$rlist['key']] : '';
						}
						elseif($rlist['value'] == 'OPTREFID')
						{
							$opt_ref_id = isset($post[$rlist['key']]) ? $post[$rlist['key']] : '';
						}
						elseif($rlist['value'] == 'STATUS')
						{
							$recharge_status = isset($post[$rlist['key']]) ? trim($post[$rlist['key']]) : '';
							$success_value = array_filter(explode(',', $rlist['success']),'strlen');
							$failed_value = array_filter(explode(',', $rlist['failed']),'strlen');
							$pending_value = array_filter(explode(',', $rlist['pending']),'strlen');
						}
					}
				}

				if($txid)
				{
					// save system log
		        	$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - TxnID - '.$txid.'.]'.PHP_EOL;
		        	$this->User->generateCallbackLog($log_msg);

		        	// check recharge status
					$chk_txn_id = $this->db->get_where('recharge_history',array('account_id'=>$account_id,'recharge_display_id'=>$txid,'status'=>1))->num_rows();
					if($chk_txn_id)
					{
						if(in_array($recharge_status, $success_value))
						{
							// check recharge status
							$get_recharge_data = $this->db->get_where('recharge_history',array('account_id'=>$account_id,'recharge_display_id'=>$txid,'status'=>1))->row_array();
							$recharge_id = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
							$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
							$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
							$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
							// update status
							$this->db->where('account_id',$account_id);
							$this->db->where('recharge_display_id',$txid);
							$this->db->where('status',1);
							$this->db->update('recharge_history',array('operator_ref'=>$opt_ref_id,'status'=>2));

							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Status Success Updated.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);

			        		// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Commision Distribute Start.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);
			        		// distribute commision
	                        $this->User->distribute_recharge_commision($recharge_id,$recharge_unique_id,$amount,$member_id);
	                        // save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Commision Distribute End.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);


			        		// get member role id
			        		// get account role id
							$get_role_id = $this->db->select('role_id,call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
							$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
							$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
							if($user_role_id == 6)
							{
								$user_call_back_url = isset($get_role_id['call_back_url']) ? $get_role_id['call_back_url'] : '' ;
								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);

				        		$api_post_data = array();
				        		$api_post_data['status'] = 'SUCCESS';
				        		$api_post_data['txnid'] = $txid;
				        		$api_post_data['operator_txnid'] = $opt_ref_id;
				        		$api_post_data['amount'] = $amount;

				        		$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
								curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
								curl_setopt($ch, CURLOPT_TIMEOUT, 30);
								curl_setopt($ch, CURLOPT_POST, true);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
								$output = curl_exec($ch); 
								curl_close($ch);

								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Call Back Send Successfully.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);
								
							}
						}
						elseif(in_array($recharge_status, $failed_value))
						{
							// check recharge status
							$get_recharge_data = $this->db->get_where('recharge_history',array('account_id'=>$account_id,'recharge_display_id'=>$txid,'status'=>1))->row_array();
							$recharge_id = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
							$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
							$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
							$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
							// update status
							$this->db->where('account_id',$account_id);
							$this->db->where('recharge_display_id',$txid);
							$this->db->where('status',1);
							$this->db->update('recharge_history',array('operator_ref'=>$opt_ref_id,'status'=>3));

							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Status Failed Updated.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);

			        		// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Refund Start.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);
			        		
			        		$get_before_balance = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();

			        		

	                    	$member_code = $get_before_balance['user_code'];    
	                    	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
	                    	$after_balance = $before_balance + $amount;

	                    	// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Member# '.$member_code.' - Before Balance - '.$before_balance.' - Recharge Amount - '.$amount.' - After Amount - '.$after_balance.' .]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);    

	                    	$wallet_data = array(
	                    		'account_id'          => $account_id,
								'member_id'           => $member_id,    
								'before_balance'      => $before_balance,
								'amount'              => $amount,  
								'after_balance'       => $after_balance,      
								'status'              => 1,
								'type'                => 1,      
								'created'             => date('Y-m-d H:i:s'),      
								'credited_by'         => 1,
								'description'         => 'Recharge Refund #'.$recharge_unique_id.' Credited'
					        );

					        $this->db->insert('member_wallet',$wallet_data);


					        $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
					        if($is_cogent_instantpay_api)
		                    {
		                        $admin_id = $this->User->get_admin_id($account_id);
		                        $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
		                        $admin_after_wallet_balance = $admin_before_wallet_balance + $amount;

		                        $wallet_data = array(
		                            'account_id'          => $account_id,
		                            'member_id'           => $admin_id,    
		                            'before_balance'      => $admin_before_wallet_balance,
		                            'amount'              => $amount,  
		                            'after_balance'       => $admin_after_wallet_balance,      
		                            'status'              => 1,
		                            'type'                => 1,   
		                            'wallet_type'         => 1,   
		                            'created'             => date('Y-m-d H:i:s'),      
		                            'description'         => 'Recharge #'.$recharge_unique_id.' Amount Refund Credited.'
		                        );

		                        $this->db->insert('virtual_wallet',$wallet_data);

		                       
		                    }

	                        // save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Refund End.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);

			        		// get member role id
			        		// get account role id
							$get_role_id = $this->db->select('role_id,call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
							$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
							$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
							if($user_role_id == 6)
							{
								$user_call_back_url = isset($get_role_id['call_back_url']) ? $get_role_id['call_back_url'] : '' ;
								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);

				        		$api_post_data = array();
				        		$api_post_data['status'] = 'FAILED';
				        		$api_post_data['txnid'] = $txid;
				        		$api_post_data['operator_txnid'] = $opt_ref_id;
				        		$api_post_data['amount'] = $amount;

				        		$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
								curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
								curl_setopt($ch, CURLOPT_TIMEOUT, 30);
								curl_setopt($ch, CURLOPT_POST, true);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
								$output = curl_exec($ch); 
								curl_close($ch);

								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Call Back Send Successfully.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);
								
							}
						}
					}
					else
					{
						// check recharge status
						$chk_failed_txn_id = $this->db->get_where('recharge_history',array('account_id'=>$account_id,'recharge_display_id'=>$txid,'status'=>3))->num_rows();
						if($chk_failed_txn_id)
						{
							$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Was Already Failed.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);
							if(in_array($recharge_status, $success_value))
							{

								// check recharge status
								$get_recharge_data = $this->db->get_where('recharge_history',array('account_id'=>$account_id,'recharge_display_id'=>$txid,'status'=>3))->row_array();
								$recharge_id = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
								$recharge_unique_id = isset($get_recharge_data['recharge_display_id']) ? $get_recharge_data['recharge_display_id'] : 0 ;
								$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
								$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
								// update status
								$this->db->where('account_id',$account_id);
								$this->db->where('recharge_display_id',$txid);
								$this->db->where('status',1);
								$this->db->update('recharge_history',array('operator_ref'=>$opt_ref_id,'status'=>2));

								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Status Success Updated.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);

				        		$before_balance = $this->User->getMemberWalletBalanceSP($member_id);

			                    $after_balance = $before_balance - $amount;    

			                    $wallet_data = array(
			                        'account_id'          => $account_id,
			                        'member_id'           => $member_id,    
			                        'before_balance'      => $before_balance,
			                        'amount'              => $amount,  
			                        'after_balance'       => $after_balance,      
			                        'status'              => 1,
			                        'type'                => 2,      
			                        'created'             => date('Y-m-d H:i:s'),      
			                        'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
			                    );

			                    $this->db->insert('member_wallet',$wallet_data);

			                    
			                    $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($account_id);
						        if($is_cogent_instantpay_api)
			                    {
			                        $admin_id = $this->User->get_admin_id($account_id);
			                        $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
			                        $admin_after_wallet_balance = $admin_before_wallet_balance - $amount;

			                        $wallet_data = array(
			                            'account_id'          => $account_id,
			                            'member_id'           => $admin_id,    
			                            'before_balance'      => $admin_before_wallet_balance,
			                            'amount'              => $amount,  
			                            'after_balance'       => $admin_after_wallet_balance,      
			                            'status'              => 1,
			                            'type'                => 1,   
			                            'wallet_type'         => 1,   
			                            'created'             => date('Y-m-d H:i:s'),      
			                            'description'         => 'Recharge #'.$recharge_unique_id.' Amount Deducted.'
			                        );

			                        $this->db->insert('virtual_wallet',$wallet_data);

			                    }
			                    
			                    // save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Amount Debited from Member Wallet.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);

				        		// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Commision Distribute Start.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);
				        		// distribute commision
		                        $this->User->distribute_recharge_commision($recharge_id,$recharge_unique_id,$amount,$member_id);
		                        // save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Recharge Commision Distribute End.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);


				        		// get member role id
				        		// get account role id
								$get_role_id = $this->db->select('role_id,call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
								$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
								$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
								if($user_role_id == 6)
								{
									$user_call_back_url = isset($get_role_id['call_back_url']) ? $get_role_id['call_back_url'] : '' ;
									// save system log
					        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
					        		$this->User->generateCallbackLog($log_msg);

					        		$api_post_data = array();
					        		$api_post_data['status'] = 'SUCCESS';
					        		$api_post_data['txnid'] = $txid;
					        		$api_post_data['operator_txnid'] = $opt_ref_id;
					        		$api_post_data['amount'] = $amount;

					        		$ch = curl_init();
									curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
									curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
									curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
									curl_setopt($ch, CURLOPT_TIMEOUT, 30);
									curl_setopt($ch, CURLOPT_POST, true);
									curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
									$output = curl_exec($ch); 
									curl_close($ch);

									// save system log
					        		$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Call Back Send Successfully.]'.PHP_EOL;
					        		$this->User->generateCallbackLog($log_msg);
									
								}
							}
						}
						else
						{
							// save system log
				        	$log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - TxnID not valid or status already updated.]'.PHP_EOL;
				        	$this->User->generateCallbackLog($log_msg);
			        	}
					}
				}
				else
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - TxnID not found.]'.PHP_EOL;
			        $this->User->generateCallbackLog($log_msg);
				}

	        }
	        else
	        {
	        	// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - Call Back ID - '.$api_callback_id.' - Call back id not valid.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);
	        }
    	}

    }
    
    public function dmtCallback()
    {
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback API Called.]'.PHP_EOL;
        $this->User->generateCallbackLog($log_msg);
        $post = $this->input->get();
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback Get Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateCallbackLog($log_msg);
        if($post)
		{
			$mytxid = $post['mytxid'];
			$txid = $post['txid'];
			$optxid = $post['optxid'];
			$rrn = isset($post['rrn']) ? $post['rrn'] : '';
			$mobileno = $post['mobileno'];
			$api_status = strtolower($post['status']);
			
			// check recharge status
			$recharge_status = $this->db->get_where('user_fund_transfer',array('transaction_id'=>$txid,'status'=>2))->num_rows();

			$dmt_status = $this->db->get_where('user_money_transfer',array('transaction_id'=>$txid,'status'=>2))->num_rows();

			if($recharge_status)
			{
				// get member id and amount
				$get_recharge_data = $this->db->get_where('user_fund_transfer',array('transaction_id'=>$txid,'status'=>2))->row_array();
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback Record Data - '.json_encode($get_recharge_data).'.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);
		        $dmt_id = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
				$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
				$member_id = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
				$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
				$total_wallet_charge = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
				$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : '' ;

				// get admin data
				$admin_id = $this->User->get_admin_id($account_id);
				$admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback Status - '.$api_status.'.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);
				$status = 0;
				if($api_status == 'success')
				{
					$status = 3;
				}
				elseif($api_status == 'failed' || $api_status == 'failure')
				{
					$status = 4;
				}
				elseif($api_status == 'pending')
				{
					$status = 2;
				}
				
				if($txid)
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback API Status Updated.]'.PHP_EOL;
			        $this->User->generateCallbackLog($log_msg);
					// update fund transfer status
					$fundData = array(
						'op_txn_id' => $optxid,
						'rrn' => $rrn,
						'status' => $status,
						'updated' => date('Y-m-d H:i:s')
					);
					$this->db->where('transaction_id',$txid);
					$this->db->update('user_fund_transfer',$fundData);

					// refund payment into wallet
					if($status == 4)
					{
						if($member_id)
						{
							$before_balance = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
							$member_code = $before_balance['user_code'];    
							$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
							$after_balance = $before_balance + $total_wallet_charge;    
							

							// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback API Refund to Member - '.$member_code.' - Before Balance - '.$before_balance.' - Refund Amount - '.$total_wallet_charge.' - After Balance - '.$after_balance.'.]'.PHP_EOL;
					        $this->User->generateCallbackLog($log_msg);
							
							$wallet_data = array(
								'account_id'          => $account_id,
								'member_id'           => $member_id,    
								'before_balance'      => $before_balance,
								'amount'              => $total_wallet_charge,  
								'after_balance'       => $after_balance,      
								'status'              => 1,
								'type'                => 1,  
								'wallet_type'		  => 1,     
								'created'             => date('Y-m-d H:i:s'),      
								'credited_by'         => 1,
								'description'         => 'Fund Request #'.$transaction_id.' Refund Credited'
					        );

					        $this->db->insert('member_wallet',$wallet_data);

					       
					        // get member role id
			        		// get account role id
							$get_role_id = $this->db->select('role_id,call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
							$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
							$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
							if($user_role_id == 6)
							{
								$user_call_back_url = isset($get_role_id['dmt_call_back_url']) ? $get_role_id['dmt_call_back_url'] : '' ;
								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);

				        		$api_post_data = array();
				        		$api_post_data['status'] = 'FAILED';
				        		$api_post_data['txnid'] = $txid;
				        		$api_post_data['optxid'] = $optxid;
				        		$api_post_data['amount'] = $amount;
				        		$api_post_data['rrn'] = $rrn;

				        		$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
								curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
								curl_setopt($ch, CURLOPT_TIMEOUT, 30);
								curl_setopt($ch, CURLOPT_POST, true);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
								$output = curl_exec($ch); 
								curl_close($ch);

								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Send Successfully.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);
								
							}
						}
					}
					elseif($status == 3)
					{
						// get dmr surcharge
            			$surcharge_amount = $this->User->get_dmr_surcharge($amount,$member_id);
            			// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
		        		$this->User->generateCallbackLog($log_msg);
						// save system log
		        		/*$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Commision Distribute Start.]'.PHP_EOL;
		        		$this->User->generateCallbackLog($log_msg);
						// distribute commision
                    	$this->User->distribute_dmt_commision($dmt_id,$transaction_id,$amount,$member_id,$surcharge_amount);
                    	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Commision Distribute End.]'.PHP_EOL;
		        		$this->User->generateCallbackLog($log_msg);*/
						// get member role id
		        		// get account role id
						$get_role_id = $this->db->select('role_id,call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
						$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
						$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
						if($user_role_id == 6)
						{
							$user_call_back_url = isset($get_role_id['dmt_call_back_url']) ? $get_role_id['dmt_call_back_url'] : '' ;
							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);

			        		$api_post_data = array();
			        		$api_post_data['status'] = 'SUCCESS';
			        		$api_post_data['txnid'] = $txid;
			        		$api_post_data['optxid'] = $optxid;
			        		$api_post_data['amount'] = $amount;
			        		$api_post_data['rrn'] = $rrn;

			        		$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
							curl_setopt($ch, CURLOPT_TIMEOUT, 30);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
							$output = curl_exec($ch); 
							curl_close($ch);

							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Send Successfully.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);
							
						}
					}
				}
				
			}
			elseif($dmt_status)
			{
				// get member id and amount
				$get_recharge_data = $this->db->get_where('user_money_transfer',array('transaction_id'=>$txid,'status'=>2))->row_array();
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback Record Data - '.json_encode($get_recharge_data).'.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);
		        $dmt_id = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
				$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
				$member_id = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
				$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
				$total_wallet_charge = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
				$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : '' ;

				// get admin data
				$admin_id = $this->User->get_admin_id($account_id);
				$admin_wallet_balance = $this->User->get_admin_wallet_balance($admin_id,$account_id);

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback Status - '.$api_status.'.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);
				$status = 0;
				if($api_status == 'success')
				{
					$status = 3;
				}
				elseif($api_status == 'failed' || $api_status == 'failure')
				{
					$status = 4;
				}
				elseif($api_status == 'pending')
				{
					$status = 2;
				}
				
				if($txid)
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback API Status Updated.]'.PHP_EOL;
			        $this->User->generateCallbackLog($log_msg);
					// update fund transfer status
					$fundData = array(
						'op_txn_id' => $optxid,
						'rrn' => $rrn,
						'status' => $status,
						'updated' => date('Y-m-d H:i:s')
					);
					$this->db->where('transaction_id',$txid);
					$this->db->update('user_money_transfer',$fundData);

					// refund payment into wallet
					if($status == 4)
					{
						if($member_id)
						{
							$before_balance = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
							$member_code = $before_balance['user_code'];    

							
							$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
							$after_balance = $before_balance + $total_wallet_charge;    

							// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback API Refund to Member - '.$member_code.' - Before Balance - '.$before_balance.' - Refund Amount - '.$total_wallet_charge.' - After Balance - '.$after_balance.'.]'.PHP_EOL;
					        $this->User->generateCallbackLog($log_msg);
							
							$wallet_data = array(
								'account_id'          => $account_id,
								'member_id'           => $member_id,    
								'before_balance'      => $before_balance,
								'amount'              => $total_wallet_charge,  
								'after_balance'       => $after_balance,      
								'status'              => 1,
								'type'                => 1,  
								'wallet_type'		  => 1,     
								'created'             => date('Y-m-d H:i:s'),      
								'credited_by'         => 1,
								'description'         => 'Fund Request #'.$transaction_id.' Refund Credited'
					        );

					        $this->db->insert('member_wallet',$wallet_data);

					        
					        // get member role id
			        		// get account role id
							$get_role_id = $this->db->select('role_id,call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
							$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
							$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
							if($user_role_id == 6)
							{
								$user_call_back_url = isset($get_role_id['dmt_call_back_url']) ? $get_role_id['dmt_call_back_url'] : '' ;
								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);

				        		$api_post_data = array();
				        		$api_post_data['status'] = 'FAILED';
				        		$api_post_data['txnid'] = $txid;
				        		$api_post_data['optxid'] = $optxid;
				        		$api_post_data['amount'] = $amount;
				        		$api_post_data['rrn'] = $rrn;

				        		$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
								curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
								curl_setopt($ch, CURLOPT_TIMEOUT, 30);
								curl_setopt($ch, CURLOPT_POST, true);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
								$output = curl_exec($ch); 
								curl_close($ch);

								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Send Successfully.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);
								
							}
						}
					}
					elseif($status == 3)
					{
						// get dmr surcharge
            			$surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$member_id);
            			// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
		        		$this->User->generateCallbackLog($log_msg);
						// save system log
		        		/*$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Commision Distribute Start.]'.PHP_EOL;
		        		$this->User->generateCallbackLog($log_msg);
						// distribute commision
                    	$this->User->distribute_dmt_commision($dmt_id,$transaction_id,$amount,$member_id,$surcharge_amount);
                    	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Commision Distribute End.]'.PHP_EOL;
		        		$this->User->generateCallbackLog($log_msg);*/
						// get member role id
		        		// get account role id
						$get_role_id = $this->db->select('role_id,call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
						$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
						$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
						if($user_role_id == 6)
						{
							$user_call_back_url = isset($get_role_id['dmt_call_back_url']) ? $get_role_id['dmt_call_back_url'] : '' ;
							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);

			        		$api_post_data = array();
			        		$api_post_data['status'] = 'SUCCESS';
			        		$api_post_data['txnid'] = $txid;
			        		$api_post_data['optxid'] = $optxid;
			        		$api_post_data['amount'] = $amount;
			        		$api_post_data['rrn'] = $rrn;

			        		$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
							curl_setopt($ch, CURLOPT_TIMEOUT, 30);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
							$output = curl_exec($ch); 
							curl_close($ch);

							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Send Successfully.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);
							
						}
					}
				}
				
			}
			else
			{
				// save system log
	        	$log_msg = '['.date('d-m-Y H:i:s').' -DMT Call Back - TxnID not valid or status already updated.]'.PHP_EOL;
	        	$this->User->generateCallbackLog($log_msg);
			}
		}
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - DMT Callback API Stop.]'.PHP_EOL;
        $this->User->generateCallbackLog($log_msg);
		echo json_encode(array('status'=>1,'msg'=>'success'));
		
    }

    public function addDefaultRechargeApi()
    {
    	$account_id = 7;
    	$post = array();
    	$post['dmt_username'] = 'API854358';
    	$post['dmt_pin'] = '9641';
    	$post['domain_url'] = 'royalpebanking.com';
    	// generate callback code
      $callbackCode = rand(111111,999999);
        $apiData = array(
          'account_id' => $account_id,
          'provider' => 'Codunite',
          'access_key' => '',
          'username' => $post['dmt_username'],
          'password' => $post['dmt_pin'],
          'request_base_url' => 'http://paymyrecharge.in/api/recharge.aspx?',
          'request_type' => 1,
          'response_type' => 1,
          'response_seperator' => ',',
          'get_balance_base_url' => 'http://paymyrecharge.in/api/balance.aspx?',
          'get_balance_request_type' => 1,
          'get_balance_response_type' => 1,
          'get_balance_response_seperator' => ',',
          'check_status_base_url' => '',
          'check_status_request_type' => 0,
          'check_status_response_type' => 0,
          'check_status_response_seperator' => '',
          'callback_base_url' => 'https://www.'.$post['domain_url'].'/cron/rechargeCallback/'.$callbackCode.'/?',
          'callback_response_type' => 0,
          'call_back_id' => $callbackCode,
          'status' => 1,
          'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('api',$apiData);
        $api_id = $this->db->insert_id();

        
        
        $paraData = array(
          'account_id' => $account_id,
          'api_id' => $api_id,
          'type' => 1,
          'is_access_key' => 0,
          'access_key' => '',
          'is_username' => 1,
          'username_key' => 'memberid',
          'is_password' => 1,
          'password_key' => 'pin',
        );
        $this->db->insert('api_parameter',$paraData);
        
        
        $keyValArray = array('number','operator','circle','amount','usertx');
        $keyValIdArray = array('5','2','3','1','4');
        
          
        foreach($keyValArray as $key=>$keyVal)
        {
           if($keyVal != '')
           {
             $getParaData = array(
              'account_id' => $account_id,
              'api_id' => $api_id,
              'para_key' => $keyVal,
              'value' => '#',
              'value_id' => isset($keyValIdArray[$key]) ? $keyValIdArray[$key] : '',
             );
             $this->db->insert('api_get_parameter',$getParaData);
           }
        }
          
           
        
        
        
       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 1,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 2,
        'success_val' => 'success,Success',
        'failed_val' => 'failure,Failure,failed,Failed',
        'pending_val' => 'pending,Pending',
       );
       $this->db->insert('api_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 3,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 4,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 5,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_str_response',$getParaData);
                 


        
        $paraData = array(
          'account_id' => $account_id,
          'api_id' => $api_id,
          'type' => 1,
          'is_access_key' => 0,
          'access_key' => '',
          'is_username' => 1,
          'username_key' => 'memberid',
          'is_password' => 1,
          'password_key' => 'pin',
        );
        $this->db->insert('api_get_balance_parameter',$paraData);
        

        
       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 7,
        'success_val' => '',
        'failed_val' => '',
        'pending_val' => '',
       );
       $this->db->insert('api_get_balance_str_response',$getParaData);

       $getParaData = array(
        'account_id' => $account_id,
        'api_id' => $api_id,
        'value_id' => 2,
        'success_val' => 'Success',
        'failed_val' => 'Failed',
        'pending_val' => 'Pending',
       );
       $this->db->insert('api_get_balance_str_response',$getParaData);
                 
      // save operator
      $operatorList = $this->db->order_by('type','asc')->get('operator')->result_array();
      if($operatorList)
      {
        foreach($operatorList as $key=>$list)
        {
           $optData = array(
            'account_id' => $account_id,
            'api_id' => $api_id,
            'opt_id' => $list['id'],
            'opt_code' => $list['operator_code']
          );
          $this->db->insert('api_operator',$optData);
        }
      }
      die('success');
    }

    public function bbpsTokenExpire()
    {
    	$current_datetime = date('Y-m-d H:i:s');

    	$this->db->where('end_datetime <',$current_datetime);
    	$this->db->where('status',1);
    	$this->db->update('bbps_token',array('status'=>0));
    }
	
	public function decrypt($encryptedText, $key)
	{
		$key = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$encryptedText = $this->hextobin($encryptedText);
		$decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
		return $decryptedText;
	} 
	
	public function hextobin($hexString)
	{
		$length = strlen($hexString);
		$binString = "";
		$count = 0;
		while ($count < $length)
		{
			$subString = substr($hexString, $count, 2);
			$packedString = pack("H*", $subString);
			if ($count == 0)
			{ $binString = $packedString; }
			else
			{ $binString .= $packedString; }
			$count += 2;
		}
		return $binString;
	} 

	public function gatewayCallBack()
    {
    	$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
    	log_message('debug', 'Gateway Callback Start.');
    	$callbackData = file_get_contents('php://input');   
    	log_message('debug', 'Gateway Callback Data - '.$callbackData);
    	// callback json response
    	/*{"entity":"event","account_id":"acc_A2ZKSnegDoTzFD","event":"payment.captured","contains":["payment"],"payload":{"payment":{"entity":{"id":"pay_IZlYcTI5RNOUJD","entity":"payment","amount":10000,"currency":"INR","status":"captured","order_id":"order_IZlYOCu0B05vCw","invoice_id":null,"international":false,"method":"wallet","amount_refunded":0,"refund_status":null,"captured":true,"description":"Topup Wallet","card_id":null,"bank":null,"wallet":"freecharge","vpa":null,"email":"sonujangid2011@gmail.com","contact":"+918104758957","notes":{"address":"","merchant_order_id":"30911640011329"},"fee":236,"tax":36,"error_code":null,"error_description":null,"error_source":null,"error_step":null,"error_reason":null,"acquirer_data":{"transaction_id":null},"created_at":1640011344}}},"created_at":1640011348}*/

    	/*$callbackData = '{"entity":"event","account_id":"acc_A2ZKSnegDoTzFD","event":"payment.captured","contains":["payment"],"payload":{"payment":{"entity":{"id":"pay_IZlYcTI5RNOUJD","entity":"payment","amount":10000,"currency":"INR","status":"captured","order_id":"order_IZlYOCu0B05vCw","invoice_id":null,"international":false,"method":"wallet","amount_refunded":0,"refund_status":null,"captured":true,"description":"Topup Wallet","card_id":null,"bank":null,"wallet":"freecharge","vpa":null,"email":"sonujangid2011@gmail.com","contact":"+918104758957","notes":{"address":"","merchant_order_id":"30911640011329"},"fee":236,"tax":36,"error_code":null,"error_description":null,"error_source":null,"error_step":null,"error_reason":null,"acquirer_data":{"transaction_id":null},"created_at":1640011344}}},"created_at":1640011348}';*/

    	$decodeResponse = json_decode($callbackData,true);

    	$request_id = isset($decodeResponse['payload']['payment']['entity']['notes']['merchant_order_id']) ? $decodeResponse['payload']['payment']['entity']['notes']['merchant_order_id'] : '';
    	$order_id = isset($decodeResponse['payload']['payment']['entity']['order_id']) ? $decodeResponse['payload']['payment']['entity']['order_id'] : '';
    	$gateway_txn_id = isset($decodeResponse['payload']['payment']['entity']['id']) ? $decodeResponse['payload']['payment']['entity']['id'] : '';
    	$amount = isset($decodeResponse['payload']['payment']['entity']['amount']) ? $decodeResponse['payload']['payment']['entity']['amount'] : 0;
    	if($request_id)
    	{
    		log_message('debug', 'Gateway Callback - Request ID Found - '.$request_id.' - Order ID - '.$order_id.' - Txn ID - '.$gateway_txn_id);
    		$status = isset($decodeResponse['payload']['payment']['entity']['status']) ? $decodeResponse['payload']['payment']['entity']['status'] : '';
    		if($status == 'captured' || $status == 'authorized')
    		{
    			log_message('debug', 'Gateway Callback - Status Success Found.');

    			// check txn
    			$chk_txn = $this->db->where_in('status',array(1,3))->get_where('member_gateway_history',array('request_id'=>$request_id))->num_rows();
    			if($chk_txn)
    			{
    				// check txn
    				$txnData = $this->db->where_in('status',array(1,3))->get_where('member_gateway_history',array('request_id'=>$request_id))->row_array();
    				$account_id = $txnData['account_id'];
    				$member_id = $txnData['member_id'];
    				$request_amount = $txnData['request_amount'];
    				$wallet_settlement_amount = $txnData['wallet_settlement_amount'];
    				$callbackAmount = round($amount/100,2);
    				/*if($request_amount == $callbackAmount)
    				{*/
    					// update request status
			            $this->db->where('account_id',$account_id);
			            $this->db->where('request_id',$request_id);
			            $this->db->where('member_id',$member_id);
			            $this->db->update('member_gateway_history',array('order_id'=>$order_id,'gateway_txn_id'=>$gateway_txn_id,'status'=>2,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>1));

			        	

						//get member wallet_balance
			            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($member_id);

			            $after_wallet_balance = $before_wallet_balance + $wallet_settlement_amount;
			            // update member wallet
			            $wallet_data = array(
			                'account_id' => $account_id,
			                'member_id'           => $member_id,    
			                'before_balance'      => $before_wallet_balance,
			                'amount'              => $wallet_settlement_amount,  
			                'after_balance'       => $after_wallet_balance,      
			                'status'              => 1,
			                'type'                => 1,      
			                'wallet_type'         => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => 1,
			                'description'         => 'Topup Request #'.$request_id.' Credited.' 
			            );

			            $this->db->insert('member_wallet',$wallet_data);
			            
			            log_message('debug', 'Gateway Callback - Wallet Updated Successfully.');
    				/*}
    				else
    				{
    					log_message('debug', 'Gateway Callback - Request Amount and Callback Amount Not Same.');
    				}*/
    			}
    			else
    			{

    				log_message('debug', 'Gateway Callback - Txn Not Found in System.');
    			}
    		}
    		elseif($status == 'failed')
    		{
    			log_message('debug', 'Gateway Callback - Status Failed Found.');
    			// check txn
    			$chk_txn = $this->db->where_in('status',array(1))->get_where('member_gateway_history',array('request_id'=>$request_id))->num_rows();
    			if($chk_txn)
    			{
    				// check txn
    				$txnData = $this->db->where_in('status',array(1,3))->get_where('member_gateway_history',array('request_id'=>$request_id))->row_array();
    				$account_id = $txnData['account_id'];
    				$member_id = $txnData['member_id'];
    				$request_amount = $txnData['request_amount'];
    				$wallet_settlement_amount = $txnData['wallet_settlement_amount'];

	    			// update request status
		            $this->db->where('account_id',$account_id);
		            $this->db->where('request_id',$request_id);
		            $this->db->where('member_id',$member_id);
		            $this->db->update('member_gateway_history',array('order_id'=>$order_id,'gateway_txn_id'=>$gateway_txn_id,'status'=>3,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>1));
	    		}
    			else
    			{

    				log_message('debug', 'Gateway Callback - Txn Not Found in System.');
    			}
    		}
    	}
    	else
    	{
    		log_message('debug', 'Gateway Callback - Request ID Blank.');
    	}

    	log_message('debug', 'Gateway Callback Stop.');

    	
    	
    }
    
    public function iciciPayoutCallBack()
    {
    	// save system log
        log_message('debug', 'ICICI Payout Callback Called.');
    	$callbackData = file_get_contents('php://input'); 
    	log_message('debug', 'ICICI Payout Callback Data - '.$callbackData);
    	$post = $this->input->post();
    	log_message('debug', 'ICICI Payout Post Data - '.json_encode($post));
    	$get = $this->input->get();
    	log_message('debug', 'ICICI Payout Get Data - '.json_encode($get));
    }
    
    public function yesValidateAuth()
    {
    	// save system log
        log_message('debug', 'Yes Validate Called.');
    	$callbackData = file_get_contents('php://input'); 
    	log_message('debug', 'Yes Validate Data - '.$callbackData);
    	$post = $this->input->post();
    	log_message('debug', 'Yes Validate Post Data - '.json_encode($post));
    	$get = $this->input->get();
    	log_message('debug', 'Yes Validate Get Data - '.json_encode($get));
    	$data = array('status'=>1,'msg'=>'SUCCESS');
    	echo json_encode($data);
    }
    
    public function yesNotifyAuth()
    {
    	// save system log
        log_message('debug', 'Yes Notify Called.');
    	$callbackData = file_get_contents('php://input'); 
    	log_message('debug', 'Yes Notify Data - '.$callbackData);
    	$post = $this->input->post();
    	log_message('debug', 'Yes Notify Post Data - '.json_encode($post));
    	$get = $this->input->get();
    	log_message('debug', 'Yes Notify Get Data - '.json_encode($get));
    	$data = array('status'=>1,'msg'=>'SUCCESS');
    	echo json_encode($data);
    }
    
    public function yesUpiCallBack()
    {
        $account_id = $this->User->get_domain_account();
         
        // save system log
        log_message('debug', 'Yes UPI Callback Called.');
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Yesbank UPI Callback API Called.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
    	$callbackData = file_get_contents('php://input'); 
    	#$callbackData = 'meRes=F2700285471A392A33CAB1379B9C49A87713104351FF66D761782505ED0157EEDCFA6FE74E0F6FD4CC671E9DF1CE2AAAE74D252B4E88B52D09349B855D811CB2CF00B62DE77282CD3A53BB2540D1CA133760EC6770C9B786DAC057CCE103C21A4DE3C65086ADAB7CB47352C5DE05CC176AE691E4032AC70065CE9725DB9FC8685EA2DE8C06050442A3BBD0E30167B8933AAEFBE30D34157CD7F2158B2345C7B9F19C719F037F199DE38B3EE599A24E8B4A0AC5281666E454000429445B3FCA9531C49D751EBEECCED1F09127E887F30E4C39BF6817759483DF40217ABB4CD202D978F87AC5B79FBFEDF22AC70CAF45D0203ADD1FE647279C41887091C72EB87504CB339D4F46E8597FD98B02F839525673C6959446B7A94FA8E6201ACCC7DCB916729A7DFD10362B802A1AC2F4C3634EBF15653A64928212A8554D54FECD0B2A';
    	log_message('debug', 'Yes UPI Callback Data - '.$callbackData);
    	
        $callbackDataExplode = explode('=',$callbackData);
        $callbackStr = isset($callbackDataExplode[1]) ? $callbackDataExplode[1] : '';        
        log_message('debug', 'Yes UPI Callback Str - '.$callbackStr);
        //UAT KEY
        #$enckey = '0eecc43f46ac1db51c40607cb355b22c';
        //LIVE KEY
        $enckey = '7153f272dbdc71b459c6b49551988767';
        $decodeResponse = $this->yesDecryptValue($callbackStr,$enckey);
        log_message('debug', 'Yes UPI Callback Json - '.$decodeResponse);
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Yesbank UPI Callback Decrypt Data - '.$decodeResponse.'.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
        
        
        /*6224691207|671617|PAYMENT_RECV|10.00|2023:11:28 12:46:09|S|Transaction success|00|NA|769316|8104758957@ybl|YBL9b03ba0951474548b574b09b1bdf04fe|NA|NA|NA|PAY TO FSS|333292502159|XXXXXX6776|ICIC0000235|SONU JANGID|payol@yesbank|YESB0000253|XXXXXX0220|NA|PAYOL DIGITAL TECHNOLOGIES PVT LTD|0|NA|SAVINGS|NA|NA|NA|NA|NA|NA|NA*/

        /*26132209|lksvgybhji|PAYMENT_RECV|10.00|2023:09:29 15:48:14|S|Transaction success|00|NA|693755|7208865023@yesb|YESB067DB704A76F6426E06400144FFB2B9|NA|NA|NA|PAY TO FSS|327210764662|XXXXXX4104|YESB0000419|MAHADEV NAVGIRE|payol@yesb|YESB0000007|XXXXXX0585|NA|Payol Digital|0|NA|SAVINGS|NA|NA|NA|NA|NA|NA|NA*/
        
        $decodeResponseData = explode('|', $decodeResponse);
        
        if(isset($decodeResponseData[6]) && $decodeResponseData[6] == 'Transaction success')
        {
            $txnid = $decodeResponseData[1];
            $bank_rrno = $decodeResponseData[16];
            $PayerAmount = $decodeResponseData[3];
            $PayerVA = $decodeResponseData[10];
            $PayerName = $decodeResponseData[19];

            $TxnInitDate = date('Y-m-d H:i:s');
            $TxnCompletionDate = date('Y-m-d H:i:s');
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Yesbank UPI Callback Success status found.]'.PHP_EOL;
            $this->User->generateUpiCollectionLog($log_msg);

            $chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->num_rows();
            if($chk_dynamic_qr)
            {
                
                $log_msg = '['.date('d-m-Y H:i:s').' - Yesbank UPI Callback Txn Found in Dynamic QR.]'.PHP_EOL;
                $this->User->generateUpiCollectionLog($log_msg);
                
                $chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->row_array();
                $member_id = isset($chk_dynamic_qr['member_id']) ? $chk_dynamic_qr['member_id'] : 0 ;
                $account_id = isset($chk_dynamic_qr['account_id']) ? $chk_dynamic_qr['account_id'] : 0 ;
                $is_add_fund = isset($chk_dynamic_qr['is_add_fund']) ? $chk_dynamic_qr['is_add_fund'] : 0 ;

                $member_role_id = $this->User->getMemberRoleID($member_id);
                
                $callStoreProc = "CALL Upicallback(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $queryData = array('account_id' => $account_id, 'member_id' => $member_id, 'txnid' => $txnid, 'amount'=>$PayerAmount, 'bankRRN' => $bank_rrno, 'payerVA' => $PayerVA, 'member_role_id'=>$member_role_id,'api_id'=>2,'is_callback'=>1,'is_add_fund'=>$is_add_fund);
                $procQuery = $this->db->query($callStoreProc, $queryData);
                $procResponse = $procQuery->row_array();

                //add this two line 
                $procQuery->next_result(); 
                $procQuery->free_result(); 

                $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API SP Response - '.json_encode($procResponse).'.]'.PHP_EOL;
                $this->User->generateUpiCollectionLog($log_msg);

                if(isset($procResponse['msg']) && $procResponse['msg'] == 'SUCCESS')
                {
                    $user_role_id = $procResponse['role_id'];
                    $api_member_code = $procResponse['user_code'];
                    $recordID = $procResponse['recordID'];
                    
                    $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API Member Role ID - '.$user_role_id.']'.PHP_EOL;
                    $this->User->generateUpiCollectionLog($log_msg);
    
                    if($user_role_id == 6)
                    {
                        $user_call_back_url = $procResponse['upi_call_back_url'];
    
                        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
                        $this->User->generateUpiCollectionLog($log_msg);
    
                        $api_post_data = array();
                        $api_post_data['status'] = 200;
                        $api_post_data['payerAmount'] = $PayerAmount;
                        $api_post_data['payerName'] = $PayerName;
                        $api_post_data['txnID'] = $txnid;
                        $api_post_data['BankRRN'] = $bank_rrno;
                        $api_post_data['payerVA'] = $PayerVA;
                        $api_post_data['TxnInitDate'] = $TxnInitDate;
                        $api_post_data['TxnCompletionDate'] = $TxnCompletionDate;
                        
                        
                        $header = [
                            'Content-type: application/json'
                        ];
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));      
                        $output = curl_exec($ch); 
                        $error_msg = '';
                        if (curl_errno($ch)) {
                            $error_msg = curl_error($ch);
                        }
                        curl_close($ch);
                        
                        
                        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Yesbank UPI Callback API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
                        $this->User->generateUpiCollectionLog($log_msg);
                        
                        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Yesbank UPI Callback API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
                        $this->User->generateUpiCollectionLog($log_msg);
    
                        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Yesbank UPI Callback API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
                        $this->User->generateUpiCollectionLog($log_msg);
                        
                    }
                    
                    // distribut referral commision
                    $recordList = $this->db->query("SELECT * FROM tbl_referral_commision WHERE from_member_id = '$member_id' AND account_id = '$account_id' AND service_id = 5 AND start_range <= $PayerAmount AND end_range >= $PayerAmount")->result_array();
                    if($recordList)
                    {
                        foreach($recordList as $rList)
                        {
                            $to_member_id = $rList['to_member_id'];
                            $commission = $rList['commission'];
                            $is_flat = $rList['is_flat'];
                            $is_surcharge = $rList['is_surcharge'];
    
                            $comission = round(($commission/100)*$PayerAmount,2);
                            if($is_flat)
                            {
                                $comission = $commission;
                            }
    
                            $referralData = array(
                                'account_id' => $account_id,
                                'from_member_id' => $member_id,
                                'to_member_id' => $to_member_id,
                                'record_id' => $recordID,
                                'txnid' => $txnid,
                                'service_id' => 5,
                                'amount' => $PayerAmount,
                                'comission' => $comission,
                                'created' => date('Y-m-d H:i:s')
                            );
                            $this->db->insert('member_referral_comission',$referralData);
                        }
                    }
                }
                
                
                
            }
            
        }
        else
        {
            $log_msg = '['.date('d-m-Y H:i:s').' - Yesbank UPI Callback API Failed Status Updated.]'.PHP_EOL;
            $this->User->generateUpiCollectionLog($log_msg);
            
        }    
        
    }
    
    public function yesEncryptValue($inputVal,$secureKey)
    {
    
    	   
    	$key='';
    	for ($i=0; $i < strlen($secureKey)-1; $i+=2)
    	{
    		$key .= chr(hexdec($secureKey[$i].$secureKey[$i+1]));
    	}
    	
        
    	$ivsize     	= openssl_cipher_iv_length('AES-128-ECB');
    	$ivsize = 1;
    	
    	$iv         	= openssl_random_pseudo_bytes($ivsize);
        
        $ciphertext 	= openssl_encrypt($inputVal, 'AES-128-ECB', $key, true, $iv);
        
    	$encrypted_text = bin2hex($ciphertext);
    
    
    	return $encrypted_text;
    
    }
    
    public function yesDecryptValue($inputVal,$secureKey)
    {
    
    	   
    	$key='';
    	for ($i=0; $i < strlen($secureKey)-1; $i+=2)
    	{
    	    $key .= chr(hexdec($secureKey[$i].$secureKey[$i+1]));
    	}
    		
    	$encblock='';
    	for ($i=0; $i < strlen($inputVal)-1; $i+=2)
    	{
    		$encblock .= chr(hexdec($inputVal[$i].$inputVal[$i+1]));
    	}
    
    
    	$ivsize     	= openssl_cipher_iv_length('AES-128-ECB');
    	$ivsize = 1;
        $iv         	= openssl_random_pseudo_bytes($ivsize);
        $decrypted_text = openssl_decrypt($encblock, 'AES-128-ECB', $key, true, $iv);
    
    
    	return $decrypted_text;
    
    }
    
    public function axisVanValidation()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Authorization");
        header("Access-Control-Allow-Methods: POST");
        $method = $_SERVER['REQUEST_METHOD'];
        log_message('debug', 'Axis VAN Validation Called.');
        $callbackData = file_get_contents('php://input'); 
        log_message('debug', 'Axis VAN Validation Data - '.$callbackData);        
        if($method != "POST") {
            die();
        } 
        // save system log
        
        $decodeResult = json_decode($callbackData,true);
        $vanNo = isset($decodeResult['Bene_acc_no']) ? $decodeResult['Bene_acc_no'] : '';
        $senderAcNo = isset($decodeResult['Sndr_acnt']) ? $decodeResult['Sndr_acnt'] : '';
        $corpCode = isset($decodeResult['Corp_code']) ? $decodeResult['Corp_code'] : '';
        
        $logData = array(
         	
         		'json_data' => $callbackData,
         		'created' => date('Y-m-d H:i:s'),
         		'van_number' => isset($decodeResult['Bene_acc_no']) ? $decodeResult['Bene_acc_no'] : '',
         	    'sender_account'=>isset($decodeResult['Sndr_acnt']) ? $decodeResult['Sndr_acnt'] : ''
         	);
         	$this->db->insert('axis_callback_data',$logData);
         	
         	$check_van_account = $this->db->get_where('tbl_va_activation',array('van_account_number'=>$vanNo,'status'=>1))->row_array();
         	$van_account_number = isset($check_van_account['van_account_number']) ? $check_van_account['van_account_number'] : '';
         	
        if($vanNo == '' || ($vanNo != '99908104758957' && $vanNo != '99908619651646' && $vanNo != '99907619733888' && $vanNo != '99907070091270' &&  $vanNo != 'PAYO223344' &&  $vanNo != 'PAYO334455' &&  $vanNo != 'PAYO445566' &&  $vanNo != 'PAYO556677' && $vanNo != $van_account_number ))
        {
            $response = array(
                'Stts_flg' => 'F',
                'Err_cd' => '002',
                'message' => 'Incorrect Van'
            );  
        }
        elseif($corpCode == '' || $corpCode != '9990')
        {
            $response = array(
                'Stts_flg' => 'F',
                'Err_cd' => '004',
                'message' => 'Invalid corp code'
            );  
        }
        else
        {
            $response = array(
                'Stts_flg' => 'S',
                'Err_cd' => '000',
                'message' => 'Success'
            );
        }
        /*$response = array(
            'Stts_flg' => 'F',
            'Err_cd' => '001',
            'message' => 'Incorrect Van'
        );*/
        log_message('debug', 'Axis VAN Validation Data Send Response - '.$response);        
        echo json_encode($response);
    }

    public function axisVanPostData()
    {
        $account_id = $this->User->get_domain_account();
        
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Authorization");
        header("Access-Control-Allow-Methods: POST");
        $method = $_SERVER['REQUEST_METHOD'];
        // save system log
        log_message('debug', 'Axis VAN Post Data Called.');
        $callbackData = file_get_contents('php://input'); 
        log_message('debug', 'Axis VAN Post Data Data - '.$callbackData);        
        if($method != "POST") {
            die('Method is not allowed');
        } 
        
        $decodeResult = json_decode($callbackData,true);
        $vanNo = isset($decodeResult['Bene_acc_no']) ? $decodeResult['Bene_acc_no'] : '';
        $utr = isset($decodeResult['UTR']) ? $decodeResult['UTR'] : '';
        $txnid = isset($decodeResult['Tran_id']) ? $decodeResult['Tran_id'] : '';
        $corpCode = isset($decodeResult['Corp_code']) ? $decodeResult['Corp_code'] : '';
        $mode =  isset($decodeResult['Pmode']) ? $decodeResult['Pmode'] : '';
        $payeer_account_no = isset($decodeResult['Sndr_acnt1']) ? $decodeResult['Sndr_acnt1'] : '';
        $txn_amount = isset($decodeResult['Txn_amnt']) ? $decodeResult['Txn_amnt'] : '';
        $payeer_name = isset($decodeResult['Sndr_nm1']) ? $decodeResult['Sndr_nm1'] : '';
        $payeer_ifsc = isset($decodeResult['Sndr_ifsc']) ? $decodeResult['Sndr_ifsc'] : '';
        $txn_time = isset($decodeResult['Req_dt_time']) ? $decodeResult['Req_dt_time'] : '';
        
          $logData = array(
         	
         		'json_data' => $callbackData,
         		'created' => date('Y-m-d H:i:s'),
         		'van_number' => isset($decodeResult['Bene_acc_no']) ? $decodeResult['Bene_acc_no'] : '',
         	    'sender_account'=>isset($decodeResult['Sndr_acnt']) ? $decodeResult['Sndr_acnt'] : ''
         	);
         	$this->db->insert('axis_callback_data',$logData);
         	
         	$check_van_account = $this->db->get_where('va_activation',array('van_account_number'=>$vanNo,'status'=>1))->row_array();
         	$van_account_number = isset($check_van_account['van_account_number']) ? $check_van_account['van_account_number'] : '';
         	$member_id = isset($check_van_account['member_id']) ? $check_van_account['member_id'] : '';
         	
        // check utr already extis or not
        $chkUtr = $this->db->get_where('axis_test_data',array('utr'=>$utr))->num_rows();
        
        if($vanNo == '' || ($vanNo != '99908104758957' && $vanNo != '99908619651646' && $vanNo != '99907619733888' && $vanNo != '99907070091270' &&  $vanNo != 'PAYO223344' &&  $vanNo != 'PAYO334455' &&  $vanNo != 'PAYO445566' &&  $vanNo != 'PAYO556677' && $vanNo != $van_account_number))
        {
            $response = array(
                'Stts_flg' => 'F',
                'Err_cd' => '002',
                'message' => 'Incorrect Van'
            );  
        }
        elseif($chkUtr)
        {
            $response = array(
                'Stts_flg' => 'F',
                'Err_cd' => '002',
                'message' => 'Duplicate UTR'
            ); 
        }
        elseif($txnid != '' && $txnid == 'ABC123456789')
        {
            $response = array(
                'Stts_flg' => 'F',
                'Err_cd' => '003',
                'message' => 'Duplicate Transaction Id'
            );  
        }
        elseif($corpCode == '' || $corpCode != '9990')
        {
            $response = array(
                'Stts_flg' => 'F',
                'Err_cd' => '004',
                'message' => 'Invalid corp code'
            );  
        }
        else
        {
            /*$axisData = array(
                'vanNo' => $vanNo,
                'utr' => $utr,
                'txnid' => $txnid,
                'corpCode' => $corpCode,
                'created' => date('Y-m-d H:i:s')
            );   
            $this->db->insert('axis_test_data',$axisData);*/
            
            $wallet_balance = $this->User->getMemberWalletBalanceSP($member_id);
            $after_wallet_balance = $wallet_balance + $txn_amount;

			        $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $member_id,    
			            'before_balance'      => $wallet_balance,
			            'amount'              => $txn_amount,  
			            'after_balance'       => $after_wallet_balance,      
			            'status'              => 1,
			            'type'                => 1,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'VAN Txn #'.$utr.' Amount Credited.'
			        );

			        $this->db->insert('member_wallet',$wallet_data);
			        
                    $txnData = array(
                		'account_id' => $account_id,
                		'member_id' => $member_id,
                		'virtual_account_no' => $vanNo,
                		'mode' => $mode,
                		'utr' => $utr,
                		'client_account_no' =>$client_account_no,
                		'amount' => $txn_amount,
                		'payer_name' => $payeer_name,
                		'payer_account_no' => $payeer_account_no,
                		'payer_bank_ifsc' => $payeer_ifsc,
                		'payment_date' => $txn_time,
                		'is_paid' =>1,
                		'created' => date('Y-m-d H:i:s')
                	);
                	$this->db->insert('virtual_txn_history',$txnData);
        	    
            
            $response = array(
                'Stts_flg' => 'S',
                'Err_cd' => '000',
                'message' => 'Success'
            );
        }
        /*$response = array(
            'Stts_flg' => 'F',
            'Err_cd' => '001',
            'message' => 'Incorrect Van'
        );*/
        echo json_encode($response);
    }
    
    public function cosmosUpiCallBack()
    {

        // save system log
        log_message('debug', 'Cosmos UPI Callback Called.');
        $callbackData = file_get_contents('php://input'); 
        log_message('debug', 'Cosmos UPI Callback Data - '.$callbackData);
        /*{"message":"kkK9UAYqdNI2F6IzWfsvEBiRmq1uzXo13+u+IJ3C14YTrwAfUbZStxbFWDnXxjLHAuthAxP0RmhAij3iXtX72uBq4NALjgfsabRPRq5ZnTiMNx9Vly5VKrp9sJG2XGAfUFM2jxbezGQqYadBLkIMRar6XDZoNG1TIUMPqSMvNGXZ2lZQ7B4Ar+NLTSub2f2sDYK3APSnibfZA6k9R/CRu/yXAQCgGAvMp66nuatyFSMv3imv5L3Z2kolSIP//dWcrnUayzcuGuskLPVCLEhC32jHuYeWUKA1AcGARWmkWY36yy4BwzxaM/g9dVaQyWpux8iBoTS9uP2ruUT/X0Vlz1V2vAmrUxOJRYVCybgNtbDQHkqOI8sNdqqdWMsJbUK+pf5JKqLbsLKOAKnu61J6DdK9hSAxgw0HIgQQQtXURccrz8idxxgxwhQlEjttzU6S0YtQVNO11RW7Vs8NroQCbXGZFRZ2QR3qruV3JMRoUUTj+pqOPb6KYxSNTVRdbdKyEf8X4FabHOyCh4ehRUzOQw=="}*/
        //$callbackData = '{"message":"6gbKhhFpJ833uA3e68hNvJmbhelGS6nxVSk3aAx7fx0BoGpDCM51MTZxJSjqHdbjHhmuqJC+6VtvMJSj6PZvbLqk3cZ7oJG2Rn9RbnFb3zT0oYlfjbmRx/zwrnICI/GG2ak5jXgevWcZg5PPNW71E13bhq+5FH42LfllhrqZcm0aKESI1FyZFYRxP4Bhv/UV/19xURcDBTp3D4IzHt0T32zFGzopx/BgGORFIVSHa1Igbwnpe3qHMrnnsigziQFeGbDQuBaeawTcNyaJh7RM49h437E11BpjRGfGVtA5v+1vi8V01spevt7PXIPkHvno6+ZtmlBLR4qXH0zrJzAW6k0/fMCxwVvqFCaFElbSa+y3F765OVDu8oF1uTCfboiAgWk6P5zq59Beugagj2DhQ2Efpg3vatomK0GpXWyqCN1DUgOYpCQgzIzDfcOOM8qps56bdT3dwq2J2XYsEKiAnPsPZ7NO67vA502+aYsMZHicidjhbvuEFbZFWKW5eN2qdx+oNoqOD6iBb+gBo85xCg=="}';
        $decodeResult = json_decode($callbackData,true);
        $result = isset($decodeResult['message']) ? $decodeResult['message'] : '';
        
        $cipher='AES-128-CBC';
        //UAT KEY
        #$key= '4bd754946cdc6e3803fb4ea271b100d3';
        //LIVE KEY
        $key= 'a82623486a0299efa1b48be02614218f';
        
        $iv = "NetworkPeopleVec";
        $keyBytes = utf8_encode($key);
        $ivBytes = utf8_encode($iv);
        $cipherTextBytes = base64_decode($result);

        $decryptedBytes = openssl_decrypt($cipherTextBytes, "aes-256-cbc", $keyBytes, OPENSSL_RAW_DATA, $ivBytes);

        // Remove the first 16 characters if needed
        if (strlen($decryptedBytes) > 16) {
            $decryptedBytes = substr($decryptedBytes, 16);
        }

        $decodeResponse = utf8_decode($decryptedBytes);
        log_message('debug', 'Cosmos UPI Callback Decrypted Data - '.$decodeResponse);
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Cosmos UPI Callback Decrypt Data - '.$decodeResponse.'.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
        $decodeResponseData = json_decode($decodeResponse,true);
        if(isset($decodeResponseData['status']) && $decodeResponseData['status'] == 'SUCCESS')
        {
            if(isset($decodeResponseData['merchant'][0]) && $decodeResponseData['merchant'][0] == 'qr.payo')
            {
                $txnid = str_replace('PAYOLDG','',$decodeResponseData['extTransactionId']);
                $bank_rrno = $decodeResponseData['rrn'];
                $PayerAmount = $decodeResponseData['amount'];
                $PayerVA = $decodeResponseData['customer_vpa'];
                $PayerName = $decodeResponseData['customerName'];

                $TxnInitDate = date('Y-m-d H:i:s');
                $TxnCompletionDate = date('Y-m-d H:i:s');
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback Success status found.]'.PHP_EOL;
                $this->User->generateUpiCollectionLog($log_msg);

                $chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->num_rows();
                if($chk_dynamic_qr)
                {
                    
                    $log_msg = '['.date('d-m-Y H:i:s').' - Cosmos UPI Callback Txn Found in Dynamic QR.]'.PHP_EOL;
                    $this->User->generateUpiCollectionLog($log_msg);
                    
                    $chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->row_array();
                    $member_id = isset($chk_dynamic_qr['member_id']) ? $chk_dynamic_qr['member_id'] : 0 ;
                    $account_id = isset($chk_dynamic_qr['account_id']) ? $chk_dynamic_qr['account_id'] : 0 ;
                    $is_add_fund = isset($chk_dynamic_qr['is_add_fund']) ? $chk_dynamic_qr['is_add_fund'] : 0 ;

                    $member_role_id = $this->User->getMemberRoleID($member_id);
                    
                    $api_id = 3;
                    if($decodeResponseData['merchant_vpa'] == 'payoldg.gelaxymanufactur@timecosmos')
                    {
                        $api_id = 4;
                    }
                    elseif($decodeResponseData['merchant_vpa'] == 'payoldg.jumbacart@timecosmos')
                    {
                        $api_id = 5;
                    }
                    elseif($decodeResponseData['merchant_vpa'] == 'payoldg.bilwapriyatech@timecosmos')
                    {
                        $api_id = 7;
                    }
                    elseif($decodeResponseData['merchant_vpa'] == 'payoldg.showdeal1@timecosmos')
                    {
                        $api_id = 8;
                    }
                    elseif($decodeResponseData['merchant_vpa'] == 'payoldg.satnaamenterp1@timecosmos')
                    {
                        $api_id = 9;
                    }
                    elseif($decodeResponseData['merchant_vpa'] == 'payoldg.shribalajitraders@timecosmos')
                    {
                        $api_id = 10;
                    }
                    elseif($decodeResponseData['merchant_vpa'] == 'payoldg.vishwahindu@timecosmos')
                    {
                        $api_id = 11;
                    }
                    
                    $callStoreProc = "CALL Upicallback(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $queryData = array('account_id' => $account_id, 'member_id' => $member_id, 'txnid' => $txnid, 'amount'=>$PayerAmount, 'bankRRN' => $bank_rrno, 'payerVA' => $PayerVA, 'member_role_id'=>$member_role_id,'api_id'=>$api_id,'is_callback'=>1,'is_add_fund'=>$is_add_fund);
                    $procQuery = $this->db->query($callStoreProc, $queryData);
                    $procResponse = $procQuery->row_array();

                    //add this two line 
                    $procQuery->next_result(); 
                    $procQuery->free_result(); 

                    $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API SP Response - '.json_encode($procResponse).'.]'.PHP_EOL;
                    $this->User->generateUpiCollectionLog($log_msg);

                    if(isset($procResponse['msg']) && $procResponse['msg'] == 'SUCCESS')
                    {
                        $user_role_id = $procResponse['role_id'];
                        $api_member_code = $procResponse['user_code'];
                        $recordID = $procResponse['recordID'];
                        
                        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API Member Role ID - '.$user_role_id.']'.PHP_EOL;
                        $this->User->generateUpiCollectionLog($log_msg);
        
                        if($user_role_id == 6)
                        {
                            $user_call_back_url = $procResponse['upi_call_back_url'];
        
                            $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
                            $this->User->generateUpiCollectionLog($log_msg);
        
                            $api_post_data = array();
                            $api_post_data['status'] = 200;
                            $api_post_data['payerAmount'] = $PayerAmount;
                            $api_post_data['payerName'] = $PayerName;
                            $api_post_data['txnID'] = $txnid;
                            $api_post_data['BankRRN'] = $bank_rrno;
                            $api_post_data['payerVA'] = $PayerVA;
                            $api_post_data['TxnInitDate'] = $TxnInitDate;
                            $api_post_data['TxnCompletionDate'] = $TxnCompletionDate;
                            
                            
                            $header = [
                                'Content-type: application/json'
                            ];
                            
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));      
                            $output = curl_exec($ch); 
                            $error_msg = '';
                            if (curl_errno($ch)) {
                                $error_msg = curl_error($ch);
                            }
                            curl_close($ch);
                            
                            
                            $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
                            $this->User->generateUpiCollectionLog($log_msg);
                            
                            $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
                            $this->User->generateUpiCollectionLog($log_msg);
        
                            $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
                            $this->User->generateUpiCollectionLog($log_msg);
                            
                        }
                        
                        
                    }
                    
                    
                    
                }
            }
            elseif(isset($decodeResponseData['merchant'][0]) && $decodeResponseData['merchant'][0] == 'payo')
            {
                $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API UPI Request Callback Found.]'.PHP_EOL;
                $this->User->generateUpiCollectionLog($log_msg);
            }
        }
        elseif(isset($decodeResponseData['status']) && $decodeResponseData['status'] == 'FAILURE')
        {
            $txnid = str_replace('PAYOLDG','',$decodeResponseData['extTransactionId']);
            $bank_rrno = $decodeResponseData['rrn'];
            $PayerAmount = $decodeResponseData['amount'];
            $PayerVA = $decodeResponseData['customer_vpa'];
            $PayerName = $decodeResponseData['customerName'];

            $TxnInitDate = date('Y-m-d H:i:s');
            $TxnCompletionDate = date('Y-m-d H:i:s');
            $chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->row_array();
            $member_id = isset($chk_dynamic_qr['member_id']) ? $chk_dynamic_qr['member_id'] : 0 ;
            $account_id = isset($chk_dynamic_qr['account_id']) ? $chk_dynamic_qr['account_id'] : 0 ;
            if($member_id)
            {
                $api_id = 3;
                if($decodeResponseData['merchant_vpa'] == 'payoldg.gelaxymanufactur@timecosmos')
                {
                    $api_id = 4;
                }
                elseif($decodeResponseData['merchant_vpa'] == 'payoldg.jumbacart@timecosmos')
                {
                    $api_id = 5;
                }
                elseif($decodeResponseData['merchant_vpa'] == 'payoldg.bilwapriyatech@timecosmos')
                {
                    $api_id = 7;
                }
                elseif($decodeResponseData['merchant_vpa'] == 'payoldg.showdeal1@timecosmos')
                {
                    $api_id = 8;
                }
                elseif($decodeResponseData['merchant_vpa'] == 'payoldg.satnaamenterp1@timecosmos')
                {
                    $api_id = 9;
                }
                elseif($decodeResponseData['merchant_vpa'] == 'payoldg.shribalajitraders@timecosmos')
                    {
                        $api_id = 10;
                    }
                $failedData = array(
                    'account_id' => $account_id,
                    'api_id' => $api_id,
                    'member_id' => $member_id,
                    'type_id' => 2,
                    'txnid' => $txnid,
                    'bank_rrno' => $bank_rrno,
                    'amount' => $PayerAmount,
                    'charge_amount' => 0,
                    'credit_amount' => 0,
                    'release_amount' => $PayerAmount,
                    'vpa_id' => $PayerVA,
                    'description' => 'QR Scan UTR #'.$bank_rrno.' Txn #'.$txnid.' Amount Failed.',
                    'status' => 3,
                    'created' => date('Y-m-d H:i:s'),
                    'created_by' => 1
                );
                $this->db->insert('upi_transaction',$failedData);
                
                $memberData = $this->db->select('role_id,user_code,upi_call_back_url')->get_where('users',array('id'=>$member_id))->row_array();
                $user_role_id = isset($memberData['role_id']) ? $memberData['role_id'] : 0;
                $api_member_code = isset($memberData['user_code']) ? $memberData['user_code'] : '';
                
                
                $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API Member Role ID - '.$user_role_id.']'.PHP_EOL;
                $this->User->generateUpiCollectionLog($log_msg);
    
                if($user_role_id == 6)
                {
                    $user_call_back_url = $memberData['upi_call_back_url'];
    
                    $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
                    $this->User->generateUpiCollectionLog($log_msg);
    
                    $api_post_data = array();
                    $api_post_data['status'] = 401;
                    $api_post_data['payerAmount'] = $PayerAmount;
                    $api_post_data['payerName'] = $PayerName;
                    $api_post_data['txnID'] = $txnid;
                    $api_post_data['BankRRN'] = $bank_rrno;
                    $api_post_data['payerVA'] = $PayerVA;
                    $api_post_data['TxnInitDate'] = $TxnInitDate;
                    $api_post_data['TxnCompletionDate'] = $TxnCompletionDate;
                    
                    
                    $header = [
                        'Content-type: application/json'
                    ];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));      
                    $output = curl_exec($ch); 
                    $error_msg = '';
                    if (curl_errno($ch)) {
                        $error_msg = curl_error($ch);
                    }
                    curl_close($ch);
                    
                    
                    $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
                    $this->User->generateUpiCollectionLog($log_msg);
                    
                    $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
                    $this->User->generateUpiCollectionLog($log_msg);
    
                    $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Cosmos UPI Callback API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
                    $this->User->generateUpiCollectionLog($log_msg);
                    
                }
            }
        }
        $data = array('status'=>1,'msg'=>'SUCCESS');
        echo json_encode($data);
        //SUCCESS UPI QR
        /*{"status":"SUCCESS","extTransactionId":"PAYOLDG24537592","checksum":"","amount":"5.00","responseTime":"Wed Oct 04 14:29:37 IST 2023","merchant_vpa":"payoldg.pay@cosb","customer_vpa":"9730024610@cosb","remark":"upiPayment","customerName":"PINGALE SANJAY JAGANNATH","errCode":null,"rrn":"327714290018","merchant":["qr.payo"],"txnId":"COBPI0WI7I9K1K477DEZ4EQ9R3S0OY77QC0"}*/
        /*{"extTransactionId":"PAYOLDG53149405","errCode":null,"status":"SUCCESS","merchant_vpa":"payoldg.stylecheck@timecosmos","txnId":"YBLd7369487493b42149c4c1835a8a65edc","responseTime":"Thu Dec 07 13:08:50 IST 2023","merchant":["qr.payo"],"rrn":"334156936819","customerName":"BAIJNATH SAW","remark":"Product Amount","customer_vpa":"8292777339@ybl","amount":"5.00","checksum":""}*/
        //SUCCESS UPI REQUEST
        /*{"source":"PAYOL1760","merchant":["payo"],"extTransactionId":"PAYOLDG96962308","data":[{"amount":"10.00","payeeVpa":"payoldg.stylecheck@timecosmos","respMessge":"SUCCESS","terminalId":"PAYOL-1760","txnTime":"Thu Dec 07 13:33:58 IST 2023","respCode":"0","custRefNo":"334113335614","customerName":"PINGALE SANJAY JAGANNATH","requestTime":"2023-12-07 13:33:41.0","upiTxnId":"COBF79168FF390646CFB0B35616248BA5F8","upiId":"8292777339@ybl","mcc":"0000"}],"channel":"api","status":"SUCCESS","checksum":""}*/
        //FAILED UPI QR
        /*{"customer_vpa":"8292777339@ybl","errCode":"U30","customerName":"BAIJNATH SAW","rrn":"334192043296","checksum":"","txnId":"YBLa3b237373af9412eaee96952813d5fa9","merchant_vpa":"payoldg.stylecheck@timecosmos","extTransactionId":"PAYOLDG53149405","status":"FAILURE","amount":"5.00","remark":"Product Amount","merchant":["qr.payo"],"responseTime":"Thu Dec 07 13:08:31 IST 2023"}*/
        //FAILED UPI REQUEST
        /*{"source":"PAYOL1760","checksum":"","channel":"api","data":[{"requestTime":"2023-12-07 13:24:06.0","txnTime":"Thu Dec 07 13:24:20 IST 2023","respCode":"U30","custRefNo":"334113245273","mcc":"0000","terminalId":"PAYOL-1760","customerName":"PINGALE SANJAY JAGANNATH","upiTxnId":"COBF65DAD15A2034B59951FAE1D84C15A69","respMessge":"FAIL","payeeVpa":"payoldg.stylecheck@timecosmos","amount":"10.00","upiId":"8292777339@ybl"}],"extTransactionId":"PAYOLDG41393661","status":"SUCCESS","merchant":["payo"]}*/
        
    }
    
    public function trustUpiCallBack()
    {
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Callback API Called.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
    	log_message('debug', 'Trust UPI Callback Called.');
    	$callbackData = file_get_contents('php://input');  
    	#$callbackData = 'I6q246aOlDea2ohxrJKvkirQvUDiw93EGRM+iWji74M6JUuKdl2+ytqbmIMqWEU4TPO5O4eUFuvYyrS2/3OIgqx/ZS2xtUkhM/r26a60BKQuvrQuPsgGKuR0AxzjZ3tYfsTwRKw/RM5AZHUmLHzRCzQi6z/VxDYt5xE5PA3yk8IcOjwsP8Gw20d0nqhhwtAFQ8iFfzcirw/WAC2kvmFfjAym5nOeNzn71SXaN8FW2jz0T2sYVej4OqYuqttPzmJtBSDNLbgtsrfjW31TwPrmajJHQl38dcpEjdUOeG/n47APnz2AfOaPRAYQkgNvp/FmIO/9hcEotT3BzJ3XTcmdhaNC3JFrk4pcbN4eMDIU6iwPSIhhREE+v5/87Cql1fi62eBaWasOpVojle4ZKUzA9vRm43KWAwxTSwibprLarrxlBifN/2668SNe8OQ+ROSs6UkGkMx0RAhzjmI8cYXNAhj3mypi/3vbTgvoUvjiDwioUjqYvgJeUNRl5NYh13EKghYyzQhYQsxiJXC7oHqYZj3aY2D3HNLJloEJFM0xai2LPglvwfpUFGZRWASR03ZowN7VhUsnJCsNiKELdeKg/qKQmCDkevEv2mF6/v06ad3LhXgErWqzwUzSdqoxd6vgy1D+PUpcFx8iNW/2v73bpZjXzDxR+5UkXuZ39e1NZVg=';

    	log_message('debug', 'Trust UPI Callback Data - '.$callbackData);

    	$response = $this->sslDecryptComposite(base64_decode($callbackData));
    	/*$response = '{"subMerchantId":"6260611","PayerMobile":"0000000000","TxnCompletionDate":"20230530113150","terminalId":"6012","PayerName":"UMER ZIA HUSAIN","PayerAmount":"10.00","PayerVA":"8800230744@upi","BankRRN":"315011400764","merchantId":"6260611","PayerAccountType":"SAVINGS","TxnInitDate":"20230530113149","TxnStatus":"SUCCESS","merchantTranId":"16853649759847"}';*/

    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Callback Decrypt Data - '.$response.'.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
    	
    	/*{"subMerchantId":"6260611","PayerMobile":"0000000000","TxnCompletionDate":"20230529181605","terminalId":"5411","PayerName":"SONU JANGID","PayerAmount":"1.00","PayerVA":"8104758957@ybl","BankRRN":"314933291023","merchantId":"6260611","PayerAccountType":"SAVINGS","TxnInitDate":"20230529181546","TxnStatus":"SUCCESS","merchantTranId":"16853643456184"}*/
    	/*{"subMerchantId":"6260611","PayerMobile":"0000000000","TxnCompletionDate":"20230530113150","terminalId":"6012","PayerName":"UMER ZIA HUSAIN","PayerAmount":"1.00","PayerVA":"8800230744@upi","BankRRN":"315011400764","merchantId":"6260611","PayerAccountType":"SAVINGS","TxnInitDate":"20230530113149","TxnStatus":"SUCCESS","merchantTranId":"16853649759847"}*/

    	$decodeResponse = json_decode($response,true);

        if(isset($decodeResponse['TxnStatus']) && $decodeResponse['TxnStatus'] == 'SUCCESS')
        {
        	
        	$txnid = $decodeResponse['merchantTranId'];
        	$bank_rrno = $decodeResponse['BankRRN'];
        	$PayerAmount = $decodeResponse['PayerAmount'];
        	$PayerVA = $decodeResponse['PayerVA'];
        	$PayerName = $decodeResponse['PayerName'];
        	$TxnInitDate = $decodeResponse['TxnInitDate'];
        	$TxnCompletionDate = $decodeResponse['TxnCompletionDate'];
        	// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback Success status found.]'.PHP_EOL;
	        $this->User->generateUpiCollectionLog($log_msg);

        	$chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->num_rows();
        	if($chk_dynamic_qr)
        	{
        		
        		$log_msg = '['.date('d-m-Y H:i:s').' - UPI Callback Txn Found in Dynamic QR.]'.PHP_EOL;
        		$this->User->generateUpiCollectionLog($log_msg);
        		
	        	$chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->row_array();
	        	$member_id = isset($chk_dynamic_qr['member_id']) ? $chk_dynamic_qr['member_id'] : 0 ;
	        	$account_id = isset($chk_dynamic_qr['account_id']) ? $chk_dynamic_qr['account_id'] : 0 ;

	        	$member_role_id = $this->User->getMemberRoleID($member_id);
	        	
        		$callStoreProc = "CALL Upicallback(?, ?, ?, ?, ?, ?, ?)";
	            $queryData = array('account_id' => $account_id, 'member_id' => $member_id, 'txnid' => $txnid, 'amount'=>$PayerAmount, 'bankRRN' => $bank_rrno, 'payerVA' => $PayerVA, 'member_role_id'=>$member_role_id);
	            $procQuery = $this->db->query($callStoreProc, $queryData);
	            $procResponse = $procQuery->row_array();

	            //add this two line 
	            $procQuery->next_result(); 
	            $procQuery->free_result(); 

	            $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API SP Response - '.json_encode($procResponse).'.]'.PHP_EOL;
				$this->User->generateUpiCollectionLog($log_msg);

				if(isset($procResponse['msg']) && $procResponse['msg'] == 'SUCCESS')
	            {
					$user_role_id = $procResponse['role_id'];
    				$api_member_code = $procResponse['user_code'];
    				
    				$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API Member Role ID - '.$user_role_id.']'.PHP_EOL;
    		        $this->User->generateUpiCollectionLog($log_msg);
    
    				if($user_role_id == 6)
    				{
    					$user_call_back_url = $procResponse['upi_call_back_url'];
    
    					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
    		        	$this->User->generateUpiCollectionLog($log_msg);
    
    	        		$api_post_data = array();
    	        		$api_post_data['status'] = $decodeResponse['TxnStatus'];
    	        		$api_post_data['payerAmount'] = $PayerAmount;
    	        		$api_post_data['payerName'] = $PayerName;
    	        		$api_post_data['txnID'] = $txnid;
    	        		$api_post_data['BankRRN'] = $bank_rrno;
    	        		$api_post_data['payerVA'] = $PayerVA;
    	        		$api_post_data['TxnInitDate'] = $TxnInitDate;
    	        		$api_post_data['TxnCompletionDate'] = $TxnCompletionDate;
    					

    	        		$header = [
                            'Content-type: application/json'
                        ];

    	        		$ch = curl_init();
    					curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
    					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    					curl_setopt($ch, CURLOPT_POST, true);
    					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));		
    					$output = curl_exec($ch); 
    					$error_msg = '';
    			        if (curl_errno($ch)) {
    			            $error_msg = curl_error($ch);
    			        }
    					curl_close($ch);
    					
    					
    					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
    		        	$this->User->generateUpiCollectionLog($log_msg);
    					
    					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
    		        	$this->User->generateUpiCollectionLog($log_msg);
    
    		        	$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
    		        	$this->User->generateUpiCollectionLog($log_msg);
    					
    				}
	            }

	            // distribut referral commision
	            $recordList = $this->db->query("SELECT * FROM tbl_referral_commision WHERE from_member_id = '$member_id' AND account_id = '$account_id' AND service_id = 5 AND start_range <= $PayerAmount AND end_range >= $PayerAmount")->result_array();
	            if($recordList)
	            {
	            	foreach($recordList as $rList)
	            	{
	            		$to_member_id = $rList['to_member_id'];
	            		$commission = $rList['commission'];
	            		$is_flat = $rList['is_flat'];
	            		$is_surcharge = $rList['is_surcharge'];

	            		$comission = round(($commission/100)*$PayerAmount,2);
	            		if($is_flat)
	            		{
	            			$comission = $commission;
	            		}

	            		$referralData = array(
	            			'account_id' => $account_id,
	            			'from_member_id' => $member_id,
	            			'to_member_id' => $to_member_id,
	            			'txnid' => $txnid,
	            			'service_id' => 5,
	            			'amount' => $PayerAmount,
	            			'comission' => $comission,
	            			'created' => date('Y-m-d H:i:s')
	            		);
	            		$this->db->insert('member_referral_comission',$referralData);
	            	}
	            }
	        	
        	}
        	
        }
        elseif(isset($decodeResponse['TxnStatus']) && $decodeResponse['TxnStatus'] == 'FAILURE')
        {
        	$log_msg = '['.date('d-m-Y H:i:s').' - UPI Callback API Failed Status Updated.]'.PHP_EOL;
		    $this->User->generateUpiCollectionLog($log_msg);
        	
        }
    	
    	
    }
    
    public function ezulixUpiCallBack()
    {
    	log_message('debug', 'Ezulix UPI Callback Called.');
    	$callbackData = file_get_contents('php://input');  
    	log_message('debug', 'Ezulix UPI Callback Data - '.$callbackData);
    	$post = $this->input->post();  
    	log_message('debug', 'Ezulix UPI Post Data - '.json_encode($post));
    	$get = $this->input->get();  
    	log_message('debug', 'Ezulix UPI Get Data - '.json_encode($get));
    	
    	
    }
    
    public function phonepeUpiCallBack()
    {
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Phonepe UPI Callback API Called.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
        log_message('debug', 'Phonepe UPI Callback Called.');
        $callbackData = file_get_contents('php://input');  
        /*$callbackData = '{"response":"eyJzdWNjZXNzIjp0cnVlLCJjb2RlIjoiUEFZTUVOVF9TVUNDRVNTIiwibWVzc2FnZSI6IllvdXIgcGF5bWVudCBpcyBzdWNjZXNzZnVsLiIsImRhdGEiOnsibWVyY2hhbnRJZCI6Ik0xVVZIQlpGRUE3UCIsIm1lcmNoYW50VHJhbnNhY3Rpb25JZCI6Ijk3NDMyODAzIiwidHJhbnNhY3Rpb25JZCI6IlQyMzExMjIxMzIyMDg4NDU4NDQ2Njg1IiwiYW1vdW50IjoxMDAwLCJzdGF0ZSI6IkNPTVBMRVRFRCIsInJlc3BvbnNlQ29kZSI6IlNVQ0NFU1MiLCJwYXltZW50SW5zdHJ1bWVudCI6eyJ0eXBlIjoiVVBJIiwidXRyIjoiMzY5MjIyNDcxOTIyIiwidXBpVHJhbnNhY3Rpb25JZCI6IllCTGJhZGM2Yjc4YmQ5MDRiZGRiNDg5ZDUyZmFhYTMzZTdmIiwiY2FyZE5ldHdvcmsiOm51bGwsImFjY291bnRUeXBlIjoiU0FWSU5HUyJ9fX0="}';*/

        log_message('debug', 'Phonepe UPI Callback Data - '.$callbackData);
        $decodeResponse = json_decode($callbackData,true);
        $response = isset($decodeResponse['response']) ? base64_decode($decodeResponse['response']) : '';
        log_message('debug', 'Phonepe UPI Callback Decode Data - '.$response);
        /*{"success":true,"code":"PAYMENT_SUCCESS","message":"Your payment is successful.","data":{"merchantId":"M1UVHBZFEA7P","merchantTransactionId":"97432803","transactionId":"T2311221322088458446685","amount":1000,"state":"COMPLETED","responseCode":"SUCCESS","paymentInstrument":{"type":"UPI","utr":"369222471922","upiTransactionId":"YBLbadc6b78bd904bddb489d52faaa33e7f","cardNetwork":null,"accountType":"SAVINGS"}}}*/
        /*$response = '{"success":true,"code":"PAYMENT_SUCCESS","message":"Your payment is successful.","data":{"merchantId":"M1UVHBZFEA7P","merchantTransactionId":"123456789997","transactionId":"T2311221322088458446685","amount":1000,"state":"COMPLETED","responseCode":"SUCCESS","paymentInstrument":{"type":"UPI","utr":"369222471922","upiTransactionId":"YBLbadc6b78bd904bddb489d52faaa33e7f","cardNetwork":null,"accountType":"SAVINGS"}}}';*/
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Callback Decrypt Data - '.$response.'.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
        
        $decodeResponse = json_decode($response,true);

        if(isset($decodeResponse['success']) && $decodeResponse['success'] == true && $decodeResponse['data']['responseCode'] == "SUCCESS")
        {
            
            $merchantId = $decodeResponse['data']['merchantId'];
            $api_id = 1;
            if($merchantId == 'M22UABDBKA8PP')
            {
                $api_id = 6;
            }
            $txnid = $decodeResponse['data']['merchantTransactionId'];
            $bank_rrno = $decodeResponse['data']['paymentInstrument']['utr'];
            $PayerAmount = ($decodeResponse['data']['amount']/100);
            $PayerVA = '';
            $PayerName = '';
            $TxnInitDate = date('Y-m-d H:i:s');
            $TxnCompletionDate = date('Y-m-d H:i:s');
            // save system log
            $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback Success status found.]'.PHP_EOL;
            $this->User->generateUpiCollectionLog($log_msg);

            $chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->num_rows();
            if($chk_dynamic_qr)
            {
                
                $log_msg = '['.date('d-m-Y H:i:s').' - UPI Callback Txn Found in Dynamic QR.]'.PHP_EOL;
                $this->User->generateUpiCollectionLog($log_msg);
                
                $chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->row_array();
                $member_id = isset($chk_dynamic_qr['member_id']) ? $chk_dynamic_qr['member_id'] : 0 ;
                $account_id = isset($chk_dynamic_qr['account_id']) ? $chk_dynamic_qr['account_id'] : 0 ;
                $is_add_fund = isset($chk_dynamic_qr['is_add_fund']) ? $chk_dynamic_qr['is_add_fund'] : 0 ;

                $member_role_id = $this->User->getMemberRoleID($member_id);
                
                $callStoreProc = "CALL Upicallback(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $queryData = array('account_id' => $account_id, 'member_id' => $member_id, 'txnid' => $txnid, 'amount'=>$PayerAmount, 'bankRRN' => $bank_rrno, 'payerVA' => $PayerVA, 'member_role_id'=>$member_role_id,'api_id'=>$api_id,'is_callback'=>1,'is_add_fund'=>$is_add_fund);
                $procQuery = $this->db->query($callStoreProc, $queryData);
                $procResponse = $procQuery->row_array();

                //add this two line 
                $procQuery->next_result(); 
                $procQuery->free_result(); 

                $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API SP Response - '.json_encode($procResponse).'.]'.PHP_EOL;
                $this->User->generateUpiCollectionLog($log_msg);

                if(isset($procResponse['msg']) && $procResponse['msg'] == 'SUCCESS')
                {
                    $user_role_id = $procResponse['role_id'];
                    $api_member_code = $procResponse['user_code'];
                    $recordID = $procResponse['recordID'];
                    
                    $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API Member Role ID - '.$user_role_id.']'.PHP_EOL;
                    $this->User->generateUpiCollectionLog($log_msg);
    
                    if($user_role_id == 6)
                    {
                        $user_call_back_url = $procResponse['upi_call_back_url'];
    
                        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
                        $this->User->generateUpiCollectionLog($log_msg);
    
                        $api_post_data = array();
                        $api_post_data['status'] = 200;
                        $api_post_data['payerAmount'] = $PayerAmount;
                        $api_post_data['payerName'] = $PayerName;
                        $api_post_data['txnID'] = $txnid;
                        $api_post_data['BankRRN'] = $bank_rrno;
                        $api_post_data['payerVA'] = $PayerVA;
                        $api_post_data['TxnInitDate'] = $TxnInitDate;
                        $api_post_data['TxnCompletionDate'] = $TxnCompletionDate;
                        
                        
                        $header = [
                            'Content-type: application/json'
                        ];
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));      
                        $output = curl_exec($ch); 
                        $error_msg = '';
                        if (curl_errno($ch)) {
                            $error_msg = curl_error($ch);
                        }
                        curl_close($ch);
                        
                        
                        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
                        $this->User->generateUpiCollectionLog($log_msg);
                        
                        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
                        $this->User->generateUpiCollectionLog($log_msg);
    
                        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
                        $this->User->generateUpiCollectionLog($log_msg);
                        
                    }
                    
                    // distribut referral commision
                    $recordList = $this->db->query("SELECT * FROM tbl_referral_commision WHERE from_member_id = '$member_id' AND account_id = '$account_id' AND service_id = 5 AND start_range <= $PayerAmount AND end_range >= $PayerAmount")->result_array();
                    if($recordList)
                    {
                        foreach($recordList as $rList)
                        {
                            $to_member_id = $rList['to_member_id'];
                            $commission = $rList['commission'];
                            $is_flat = $rList['is_flat'];
                            $is_surcharge = $rList['is_surcharge'];
    
                            $comission = round(($commission/100)*$PayerAmount,2);
                            if($is_flat)
                            {
                                $comission = $commission;
                            }
    
                            $referralData = array(
                                'account_id' => $account_id,
                                'from_member_id' => $member_id,
                                'to_member_id' => $to_member_id,
                                'record_id' => $recordID,
                                'txnid' => $txnid,
                                'service_id' => 5,
                                'amount' => $PayerAmount,
                                'comission' => $comission,
                                'created' => date('Y-m-d H:i:s')
                            );
                            $this->db->insert('member_referral_comission',$referralData);
                        }
                    }
                }
                
                
                
            }
            
        }
        else
        {
            $log_msg = '['.date('d-m-Y H:i:s').' - UPI Callback API Failed Status Updated.]'.PHP_EOL;
            $this->User->generateUpiCollectionLog($log_msg);
            
        }
        
        
    }

	public function upiCallBack()
    {
    	$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
    	$key = $accountData['upi_encryption_key'];
    	log_message('debug', 'UPI Callback Called.');
    	$callbackData = file_get_contents('php://input');   
    	log_message('debug', 'UPI Callback Data - '.$callbackData);
    	$decodeResult = str_replace('"','',$callbackData);
        //$decryptData = $decodeResult;
		$decryptData = $this->decrypt($decodeResult, $key);

		$private_key = $accountData['upi_private_certificate'];

		$private_key = openssl_get_privatekey($private_key, "");
		openssl_private_decrypt($decryptData,$response,$private_key);

		$response = json_decode($response);
        
        log_message('debug', 'UPI Callback RESPONSE DECRYPTED - '.$response);
        
        // SEND REQUEST RESPONSE
        //SUCCESS RESONSE
        /*{"status":200,"message":"SUCCESS","data":{"TxnStatus":"SUCCESS","subMerchantId":"8104758957","PayerAmount":"1.00","ResponseCode":"00","PayerName":"SONU JANGID","terminalId":"6012","TxnInitDate":"20211125173431","merchantTranId":"16378418691313","PayerMobile":"0000000000","BankRRN":"132912725881","merchantId":"420661","PayerVA":"8104758957@ybl","TxnCompletionDate":"20211125173453"}}*/

        //FAILER RESPONSE
        /*{"status":200,"message":"FAILURE","data":{"TxnStatus":"FAILURE","subMerchantId":"8104758957","PayerAmount":"1.00","ResponseCode":"U19","PayerName":"payer name not available","terminalId":"6012","TxnInitDate":"20211125174528","merchantTranId":"16378425272281","PayerMobile":"0000000000","BankRRN":"132912904615","merchantId":"420661","PayerVA":"8104758957@ybl","TxnCompletionDate":"20211125174554"}}*/

        //Dynamic QR SCAN RESPONSE
        //SUCCESS RESPONSE
        /*{"status":200,"message":"SUCCESS","data":{"TxnStatus":"SUCCESS","subMerchantId":"420661","PayerAmount":"1.00","ResponseCode":"00","PayerName":"SONU JANGID","terminalId":"6012","TxnInitDate":"20211125175353","merchantTranId":"16378429624651","PayerMobile":"0000000000","BankRRN":"132977938169","merchantId":"420661","PayerVA":"8104758957@ybl","TxnCompletionDate":"20211125175354"}}*/

        //Static QR SCAN RESPONSE
        //SUCCESS RESPONSE
        /*{"status":200,"message":"SUCCESS","data":{"subMerchantId":"420661","ResponseCode":"00","PayerMobile":"0000000000","TxnCompletionDate":"20211125180552","terminalId":"null","PayerName":"SONU JANGID","PayerAmount":"1.00","PayerVA":"8104758957@ybl","BankRRN":"132921053946","merchantId":"420661","TxnInitDate":"20211125180550","TxnStatus":"SUCCESS","merchantTranId":"Board My TripNU3zCL8X4Vwy"}}*/

        $decodeResponse = json_decode($response,true);

        if(isset($decodeResponse['message']) && $decodeResponse['message'] == 'SUCCESS')
        {
        	log_message('debug', 'UPI Callback Success Status Updated.');
        	$txnid = $decodeResponse['data']['merchantTranId'];
        	$bank_rrno = $decodeResponse['data']['BankRRN'];
        	$PayerAmount = $decodeResponse['data']['PayerAmount'];
        	$PayerVA = $decodeResponse['data']['PayerVA'];
        	
        	$chk_static_qr = $this->db->get_where('users',array('account_id'=>$account_id,'upi_qr_ref_id'=>$txnid))->num_rows();
        	
        	// check txn is from qr code
        	$chk_txn_qr = $this->db->get_where('upi_transaction',array('txnid'=>$txnid))->num_rows();

        	$chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->num_rows();
        	if($chk_static_qr)
        	{
        		log_message('debug', 'UPI Callback Txn Found from QR Scan.');
        		
	        	$chk_static_qr = $this->db->get_where('users',array('account_id'=>$account_id,'upi_qr_ref_id'=>$txnid))->row_array();
	        	$member_id = isset($chk_static_qr['id']) ? $chk_static_qr['id'] : 0 ;
	        	
	        	if($member_id){

		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
			                    
		            $after_balance = $before_balance + $PayerAmount;  

		            log_message('debug', 'UPI Callback Member Before Wallet Balance - '.$before_balance);  

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $PayerAmount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'QR Scan #'.$bank_rrno.' Amount Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            
		        }
		        
		        
		        // save transaction data
    			$txnData = array(
    				'account_id' => $account_id,
    				'member_id' => $member_id,
    				'type_id' => 2,
    				'txnid' => $txnid,
    				'amount' => $PayerAmount,
    				'vpa_id' => $PayerVA,
    				'bank_rrno' => $bank_rrno,
    				'description' => 'QR Scan #'.$bank_rrno.' Amount Received.',
    				'status'=>2,
    				'is_api_response' => 1,
    				'created' => date('Y-m-d H:i:s'),
    				'created_by' => $member_id
    			);
    			$this->db->insert('upi_transaction',$txnData);
    			$record_id = $this->db->insert_id();

    			$commisionData = $this->User->get_upi_commission($member_id,$PayerAmount);
		        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
		        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

		        log_message('debug', 'UPI Callback Member Commision Data - '.json_encode($commisionData));  

		        if($is_surcharge && $com_amount)
		        {
		        	
		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);

		        	$after_balance = $before_balance - $com_amount;  

		        	$commisionData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'type' => 'UPI',
						'record_id' => $record_id,
						'commision_amount' => $com_amount,
						'is_surcharge' => 1,
						'before_balance' => $before_balance,
						'after_balance' => $after_balance,
						'status' => 1,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_commision',$commisionData);
		            
		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $com_amount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 2,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Charge Debited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            
		        }
		        elseif(!$is_surcharge && $com_amount)
		        {
		        	
		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
		                    
		            $after_balance = $before_balance + $com_amount;  

		            $commisionData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'type' => 'UPI',
						'record_id' => $record_id,
						'commision_amount' => $com_amount,
						'before_balance' => $before_balance,
						'after_balance' => $after_balance,
						'status' => 1,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_commision',$commisionData);

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $com_amount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Commision Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		        }

	            // save system log
		        log_message('debug', 'UPI Callback - Distribute Commision/Surcharge Start.');

		        $this->User->distribute_upi_commision($record_id,$bank_rrno,$PayerAmount,$member_id,$com_amount,$is_surcharge);

		        log_message('debug', 'UPI Callback - Distribute Commision/Surcharge End.');

		        log_message('debug', 'UPI Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

		        log_message('debug', 'UPI Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	elseif($chk_txn_qr)
        	{
        		log_message('debug', 'UPI Callback Txn Found from VPA/Dynamic QR Scan.');
        		// get member id
        		$get_txn_qr = $this->db->get_where('upi_transaction',array('txnid'=>$txnid))->row_array();

        		$record_id = isset($get_txn_qr['id']) ? $get_txn_qr['id'] : 0 ;
        		$type_id = isset($get_txn_qr['type_id']) ? $get_txn_qr['type_id'] : 0 ;

	        	$get_member_data = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$get_txn_qr['member_id']))->row_array();
	        	$member_id = isset($get_member_data['id']) ? $get_member_data['id'] : 0 ;


	        	
	        	if($member_id){

	        		
	        		$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
			                    
		            $after_balance = $before_balance + $PayerAmount;  

		            log_message('debug', 'UPI Callback Member Before Wallet Balance - '.$before_balance);  

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $PayerAmount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Amount Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);


		            $commisionData = $this->User->get_upi_commission($member_id,$PayerAmount);
			        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
			        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

			        log_message('debug', 'UPI Callback Member Commision Data - '.json_encode($commisionData));  

			        if($is_surcharge && $com_amount)
			        {
			        	
			        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);

			        	$after_balance = $before_balance - $com_amount;  

			        	$commisionData = array(
							'account_id' => $account_id,
							'member_id' => $member_id,
							'type' => 'UPI',
							'record_id' => $record_id,
							'commision_amount' => $com_amount,
							'is_surcharge' => 1,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
			            
			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $member_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $com_amount,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2,      
			                'wallet_type'		  => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'description'         => 'UPI Txn #'.$bank_rrno.' Charge Debited.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			        }
			        elseif(!$is_surcharge && $com_amount)
			        {
			        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
			                    
			            $after_balance = $before_balance + $com_amount;  

			            $commisionData = array(
							'account_id' => $account_id,
							'member_id' => $member_id,
							'type' => 'UPI',
							'record_id' => $record_id,
							'commision_amount' => $com_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $member_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $com_amount,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1,      
			                'wallet_type'		  => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'description'         => 'UPI Txn #'.$bank_rrno.' Commision Credited.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			        }

		            // save system log
			        log_message('debug', 'UPI Callback - Distribute Commision/Surcharge Start.');

			        $this->User->distribute_upi_commision($record_id,$bank_rrno,$PayerAmount,$member_id,$com_amount,$is_surcharge);

			        log_message('debug', 'UPI Callback - Distribute Commision/Surcharge End.');

		        }

		        // update transaction status

		        $this->db->where('account_id',$account_id);
	        	$this->db->where('txnid',$txnid);
	        	$this->db->update('upi_transaction',array('bank_rrno'=>$bank_rrno,'status'=>2,'vpa_id' => $PayerVA,'description' => 'UPI Txn #'.$bank_rrno.' Amount Received.','is_api_response'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>1));	

	            log_message('debug', 'UPI Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	elseif($chk_dynamic_qr)
        	{
        		log_message('debug', 'UPI Callback Txn Found from QR Scan.');
        		
	        	$chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->row_array();
	        	$member_id = isset($chk_dynamic_qr['member_id']) ? $chk_dynamic_qr['member_id'] : 0 ;
	        	
	        	if($member_id){

		        	
		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
			                    
		            $after_balance = $before_balance + $PayerAmount;  

		            log_message('debug', 'UPI Callback Member Before Wallet Balance - '.$before_balance);  

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $PayerAmount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'QR Scan #'.$bank_rrno.' Amount Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);


		        }
		        
		        
		        // save transaction data
    			$txnData = array(
    				'account_id' => $account_id,
    				'member_id' => $member_id,
    				'type_id' => 3,
    				'txnid' => $txnid,
    				'amount' => $PayerAmount,
    				'vpa_id' => $PayerVA,
    				'bank_rrno' => $bank_rrno,
    				'description' => 'QR Scan #'.$bank_rrno.' Amount Received.',
    				'status'=>2,
    				'is_api_response' => 1,
    				'created' => date('Y-m-d H:i:s'),
    				'created_by' => $member_id
    			);
    			$this->db->insert('upi_transaction',$txnData);
    			$record_id = $this->db->insert_id();

    			$commisionData = $this->User->get_upi_commission($member_id,$PayerAmount);
		        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
		        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

		        log_message('debug', 'UPI Callback Member Commision Data - '.json_encode($commisionData));  

		        if($is_surcharge && $com_amount)
		        {
		        	
		        	
		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);

		        	$after_balance = $before_balance - $com_amount;  

		        	$commisionData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'type' => 'UPI',
						'record_id' => $record_id,
						'commision_amount' => $com_amount,
						'is_surcharge' => 1,
						'before_balance' => $before_balance,
						'after_balance' => $after_balance,
						'status' => 1,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_commision',$commisionData);
		            
		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $com_amount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 2,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Charge Debited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            
		        }
		        elseif(!$is_surcharge && $com_amount)
		        {
		        	
		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
		                    
		            $after_balance = $before_balance + $com_amount;  

		            $commisionData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'type' => 'UPI',
						'record_id' => $record_id,
						'commision_amount' => $com_amount,
						'before_balance' => $before_balance,
						'after_balance' => $after_balance,
						'status' => 1,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_commision',$commisionData);

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $com_amount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Commision Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		        }

	            // save system log
		        log_message('debug', 'UPI Callback - Distribute Commision/Surcharge Start.');

		        $this->User->distribute_upi_commision($record_id,$bank_rrno,$PayerAmount,$member_id,$com_amount,$is_surcharge);

		        log_message('debug', 'UPI Callback - Distribute Commision/Surcharge End.');

		        log_message('debug', 'UPI Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	
        	
        }
        elseif(isset($decodeResponse['message']) && $decodeResponse['message'] == 'FAILURE')
        {
        	log_message('debug', 'UPI Callback Failed Status Updated.');
        	$txnid = $decodeResponse['data']['merchantTranId'];
        	$bank_rrno = $decodeResponse['data']['BankRRN'];
        	$PayerAmount = $decodeResponse['data']['PayerAmount'];
        	$PayerVA = $decodeResponse['data']['PayerVA'];
        	
        	$chk_static_qr = $this->db->get_where('users',array('account_id'=>$account_id,'upi_qr_ref_id'=>$txnid))->num_rows();
        	
        	// check txn is from qr code
        	$chk_txn_qr = $this->db->get_where('upi_transaction',array('txnid'=>$txnid))->num_rows();
        	if($chk_static_qr)
        	{
        		log_message('debug', 'UPI Callback Txn Found from QR Scan.');
        		
		        // save transaction data
    			$txnData = array(
    				'account_id' => $account_id,
    				'member_id' => $member_id,
    				'type_id' => 3,
    				'txnid' => $txnid,
    				'amount' => $PayerAmount,
    				'vpa_id' => $PayerVA,
    				'bank_rrno' => $bank_rrno,
    				'description' => 'QR Scan #'.$bank_rrno.' Amount Failed.',
    				'status'=>2,
    				'is_api_response' => 1,
    				'created' => date('Y-m-d H:i:s'),
    				'created_by' => $member_id
    			);
    			$this->db->insert('upi_transaction',$txnData);

		        log_message('debug', 'UPI Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	elseif($chk_txn_qr)
        	{
        		log_message('debug', 'UPI Callback Txn Found from VPA/Dynamic QR Scan.');
        		
		        $this->db->where('account_id',$account_id);
	        	$this->db->where('txnid',$txnid);
	        	$this->db->update('upi_transaction',array('bank_rrno'=>$bank_rrno,'status'=>3,'vpa_id' => $PayerVA,'description' => 'UPI Txn #'.$bank_rrno.' Amount Failed.','is_api_response'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>1));	

	            log_message('debug', 'UPI Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	
        }
        
    }


    public function upiCashCallBack()
    {
    	$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
    	$key = $accountData['upi_cash_encryption_key'];
    	log_message('debug', 'UPI Cash Callback Called.');
    	$callbackData = file_get_contents('php://input');   
    	log_message('debug', 'UPI Cash Callback Data - '.$callbackData);
    	$decodeResult = str_replace('"','',$callbackData);
        //$decryptData = $decodeResult;
		$decryptData = $this->decrypt($decodeResult, $key);

		$private_key = $accountData['upi_cash_private_certificate'];

		$private_key = openssl_get_privatekey($private_key, "");
		openssl_private_decrypt($decryptData,$response,$private_key);

		$response = json_decode($response);
        
        log_message('debug', 'UPI Cash Callback RESPONSE DECRYPTED - '.$response);
        
        // SEND REQUEST RESPONSE
        //SUCCESS RESONSE
        /*{"status":200,"message":"SUCCESS","data":{"TxnStatus":"SUCCESS","subMerchantId":"8104758957","PayerAmount":"1.00","ResponseCode":"00","PayerName":"SONU JANGID","terminalId":"6012","TxnInitDate":"20211125173431","merchantTranId":"16378418691313","PayerMobile":"0000000000","BankRRN":"132912725881","merchantId":"420661","PayerVA":"8104758957@ybl","TxnCompletionDate":"20211125173453"}}*/

        //FAILER RESPONSE
        /*{"status":200,"message":"FAILURE","data":{"TxnStatus":"FAILURE","subMerchantId":"8104758957","PayerAmount":"1.00","ResponseCode":"U19","PayerName":"payer name not available","terminalId":"6012","TxnInitDate":"20211125174528","merchantTranId":"16378425272281","PayerMobile":"0000000000","BankRRN":"132912904615","merchantId":"420661","PayerVA":"8104758957@ybl","TxnCompletionDate":"20211125174554"}}*/

        //Dynamic QR SCAN RESPONSE
        //SUCCESS RESPONSE
        /*{"status":200,"message":"SUCCESS","data":{"TxnStatus":"SUCCESS","subMerchantId":"420661","PayerAmount":"1.00","ResponseCode":"00","PayerName":"SONU JANGID","terminalId":"6012","TxnInitDate":"20211125175353","merchantTranId":"16378429624651","PayerMobile":"0000000000","BankRRN":"132977938169","merchantId":"420661","PayerVA":"8104758957@ybl","TxnCompletionDate":"20211125175354"}}*/

        //Static QR SCAN RESPONSE
        //SUCCESS RESPONSE
        /*{"status":200,"message":"SUCCESS","data":{"subMerchantId":"420661","ResponseCode":"00","PayerMobile":"0000000000","TxnCompletionDate":"20211125180552","terminalId":"null","PayerName":"SONU JANGID","PayerAmount":"1.00","PayerVA":"8104758957@ybl","BankRRN":"132921053946","merchantId":"420661","TxnInitDate":"20211125180550","TxnStatus":"SUCCESS","merchantTranId":"Board My TripNU3zCL8X4Vwy"}}*/

        $decodeResponse = json_decode($response,true);

        if(isset($decodeResponse['message']) && $decodeResponse['message'] == 'SUCCESS')
        {
        	log_message('debug', 'UPI Cash Callback Success Status Updated.');
        	$txnid = $decodeResponse['data']['merchantTranId'];
        	$bank_rrno = $decodeResponse['data']['BankRRN'];
        	$PayerAmount = $decodeResponse['data']['PayerAmount'];
        	$PayerVA = $decodeResponse['data']['PayerVA'];
        	
        	$chk_static_qr = $this->db->get_where('users',array('account_id'=>$account_id,'upi_cash_qr_ref_id'=>$txnid))->num_rows();
        	
        	// check txn is from qr code
        	$chk_txn_qr = $this->db->get_where('upi_cash_transaction',array('txnid'=>$txnid))->num_rows();

        	$chk_dynamic_qr = $this->db->get_where('upi_cash_dynamic_qr',array('txnid'=>$txnid))->num_rows();

        	if($chk_static_qr)
        	{
        		log_message('debug', 'UPI Cash Callback Txn Found from QR Scan.');
        		
	        	$chk_static_qr = $this->db->get_where('users',array('account_id'=>$account_id,'upi_cash_qr_ref_id'=>$txnid))->row_array();
	        	$member_id = isset($chk_static_qr['id']) ? $chk_static_qr['id'] : 0 ;
	        	
	        	if($member_id){

		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
			                    
		            $after_balance = $before_balance + $PayerAmount;  

		            log_message('debug', 'UPI Cash Callback Member Before Wallet Balance - '.$before_balance);  

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $PayerAmount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'QR Scan #'.$bank_rrno.' Amount Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);


		        }
		        
		        
		        // save transaction data
    			$txnData = array(
    				'account_id' => $account_id,
    				'member_id' => $member_id,
    				'type_id' => 2,
    				'txnid' => $txnid,
    				'amount' => $PayerAmount,
    				'vpa_id' => $PayerVA,
    				'bank_rrno' => $bank_rrno,
    				'description' => 'QR Scan #'.$bank_rrno.' Amount Received.',
    				'status'=>2,
    				'is_api_response' => 1,
    				'created' => date('Y-m-d H:i:s'),
    				'created_by' => $member_id
    			);
    			$this->db->insert('upi_cash_transaction',$txnData);
    			$record_id = $this->db->insert_id();

    			$commisionData = $this->User->get_upi_cash_commission($member_id,$PayerAmount);
		        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
		        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

		        log_message('debug', 'UPI Cash Callback Member Commision Data - '.json_encode($commisionData));  

		        if($is_surcharge && $com_amount)
		        {
		        	
		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);

		        	$after_balance = $before_balance - $com_amount;  

		        	$commisionData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'type' => 'UPICASH',
						'record_id' => $record_id,
						'commision_amount' => $com_amount,
						'is_surcharge' => 1,
						'before_balance' => $before_balance,
						'after_balance' => $after_balance,
						'status' => 1,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_commision',$commisionData);
		            
		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $com_amount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 2,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Charge Debited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		        }
		        elseif(!$is_surcharge && $com_amount)
		        {
		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
		                    
		            $after_balance = $before_balance + $com_amount;  

		            $commisionData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'type' => 'UPICASH',
						'record_id' => $record_id,
						'commision_amount' => $com_amount,
						'before_balance' => $before_balance,
						'after_balance' => $after_balance,
						'status' => 1,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_commision',$commisionData);

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $com_amount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Commision Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            
		        }

	            // save system log
		        log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge Start.');

		        $this->User->distribute_upi_cash_commision($record_id,$bank_rrno,$PayerAmount,$member_id,$com_amount,$is_surcharge);

		        log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge End.');

		        log_message('debug', 'UPI Cash Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

		        log_message('debug', 'UPI Cash Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	elseif($chk_txn_qr)
        	{
        		log_message('debug', 'UPI Cash Callback Txn Found from VPA/Dynamic QR Scan.');
        		// get member id
        		$get_txn_qr = $this->db->get_where('upi_cash_transaction',array('txnid'=>$txnid))->row_array();

        		$record_id = isset($get_txn_qr['id']) ? $get_txn_qr['id'] : 0 ;
        		$type_id = isset($get_txn_qr['type_id']) ? $get_txn_qr['type_id'] : 0 ;

	        	$get_member_data = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$get_txn_qr['member_id']))->row_array();
	        	$member_id = isset($get_member_data['id']) ? $get_member_data['id'] : 0 ;


	        	
	        	if($member_id){

	        		$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
			                    
		            $after_balance = $before_balance + $PayerAmount;  

		            log_message('debug', 'UPI Cash Callback Member Before Wallet Balance - '.$before_balance);  

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $PayerAmount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Amount Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            
		            $commisionData = $this->User->get_upi_cash_commission($member_id,$PayerAmount);
			        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
			        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

			        log_message('debug', 'UPI Cash Callback Member Commision Data - '.json_encode($commisionData));  

			        if($is_surcharge && $com_amount)
			        {
			        	
			        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);

			        	$after_balance = $before_balance - $com_amount;  

			        	$commisionData = array(
							'account_id' => $account_id,
							'member_id' => $member_id,
							'type' => 'UPICASH',
							'record_id' => $record_id,
							'commision_amount' => $com_amount,
							'is_surcharge' => 1,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
			            
			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $member_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $com_amount,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2,      
			                'wallet_type'		  => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'description'         => 'UPI Txn #'.$bank_rrno.' Charge Debited.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			            
			        }
			        elseif(!$is_surcharge && $com_amount)
			        {
			        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
			                    
			            $after_balance = $before_balance + $com_amount;  

			            $commisionData = array(
							'account_id' => $account_id,
							'member_id' => $member_id,
							'type' => 'UPICASH',
							'record_id' => $record_id,
							'commision_amount' => $com_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

			            $wallet_data = array(
			                'account_id'          => $account_id,
			                'member_id'           => $member_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $com_amount,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1,      
			                'wallet_type'		  => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'description'         => 'UPI Txn #'.$bank_rrno.' Commision Credited.'
			            );

			            $this->db->insert('member_wallet',$wallet_data);

			        }

		            // save system log
			        log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge Start.');

			        $this->User->distribute_upi_cash_commision($record_id,$bank_rrno,$PayerAmount,$member_id,$com_amount,$is_surcharge);

			        log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge End.');

		        }

		        // update transaction status

		        $this->db->where('account_id',$account_id);
	        	$this->db->where('txnid',$txnid);
	        	$this->db->update('upi_cash_transaction',array('bank_rrno'=>$bank_rrno,'status'=>2,'vpa_id' => $PayerVA,'description' => 'UPI Txn #'.$bank_rrno.' Amount Received.','is_api_response'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>1));	

	            log_message('debug', 'UPI Cash Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	elseif($chk_dynamic_qr)
        	{
        		log_message('debug', 'UPI Cash Callback Txn Found from QR Scan.');
        		
	        	$chk_dynamic_qr = $this->db->get_where('upi_cash_dynamic_qr',array('txnid'=>$txnid))->row_array();
	        	$member_id = isset($chk_dynamic_qr['member_id']) ? $chk_dynamic_qr['member_id'] : 0 ;
	        	
	        	if($member_id){

		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
			                    
		            $after_balance = $before_balance + $PayerAmount;  

		            log_message('debug', 'UPI Cash Callback Member Before Wallet Balance - '.$before_balance);  

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $PayerAmount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'QR Scan #'.$bank_rrno.' Amount Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		            

		        }
		        
		        
		        // save transaction data
    			$txnData = array(
    				'account_id' => $account_id,
    				'member_id' => $member_id,
    				'type_id' => 3,
    				'txnid' => $txnid,
    				'amount' => $PayerAmount,
    				'vpa_id' => $PayerVA,
    				'bank_rrno' => $bank_rrno,
    				'description' => 'QR Scan #'.$bank_rrno.' Amount Received.',
    				'status'=>2,
    				'is_api_response' => 1,
    				'created' => date('Y-m-d H:i:s'),
    				'created_by' => $member_id
    			);
    			$this->db->insert('upi_cash_transaction',$txnData);
    			$record_id = $this->db->insert_id();

    			$commisionData = $this->User->get_upi_cash_commission($member_id,$PayerAmount);
		        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
		        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

		        log_message('debug', 'UPI Cash Callback Member Commision Data - '.json_encode($commisionData));  

		        if($is_surcharge && $com_amount)
		        {
		        	
		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);

		        	$after_balance = $before_balance - $com_amount;  

		        	$commisionData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'type' => 'UPICASH',
						'record_id' => $record_id,
						'commision_amount' => $com_amount,
						'is_surcharge' => 1,
						'before_balance' => $before_balance,
						'after_balance' => $after_balance,
						'status' => 1,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_commision',$commisionData);
		            
		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $com_amount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 2,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Charge Debited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		        }
		        elseif(!$is_surcharge && $com_amount)
		        {
		        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
		                    
		            $after_balance = $before_balance + $com_amount;  

		            $commisionData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'type' => 'UPICASH',
						'record_id' => $record_id,
						'commision_amount' => $com_amount,
						'before_balance' => $before_balance,
						'after_balance' => $after_balance,
						'status' => 1,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('user_commision',$commisionData);

		            $wallet_data = array(
		                'account_id'          => $account_id,
		                'member_id'           => $member_id,    
		                'before_balance'      => $before_balance,
		                'amount'              => $com_amount,  
		                'after_balance'       => $after_balance,      
		                'status'              => 1,
		                'type'                => 1,      
		                'wallet_type'		  => 1,
		                'created'             => date('Y-m-d H:i:s'),      
		                'description'         => 'UPI Txn #'.$bank_rrno.' Commision Credited.'
		            );

		            $this->db->insert('member_wallet',$wallet_data);

		        }

	            // save system log
		        log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge Start.');

		        $this->User->distribute_upi_cash_commision($record_id,$bank_rrno,$PayerAmount,$member_id,$com_amount,$is_surcharge);

		        log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge End.');

		        log_message('debug', 'UPI Cash Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	
        	
        }
        elseif(isset($decodeResponse['message']) && $decodeResponse['message'] == 'FAILURE')
        {
        	log_message('debug', 'UPI Cash Callback Failed Status Updated.');
        	$txnid = $decodeResponse['data']['merchantTranId'];
        	$bank_rrno = $decodeResponse['data']['BankRRN'];
        	$PayerAmount = $decodeResponse['data']['PayerAmount'];
        	$PayerVA = $decodeResponse['data']['PayerVA'];
        	
        	$chk_static_qr = $this->db->get_where('users',array('account_id'=>$account_id,'upi_cash_qr_ref_id'=>$txnid))->num_rows();
        	
        	// check txn is from qr code
        	$chk_txn_qr = $this->db->get_where('upi_cash_transaction',array('txnid'=>$txnid))->num_rows();
        	if($chk_static_qr)
        	{
        		log_message('debug', 'UPI Cash Callback Txn Found from QR Scan.');
        		
		        // save transaction data
    			$txnData = array(
    				'account_id' => $account_id,
    				'member_id' => $member_id,
    				'type_id' => 3,
    				'txnid' => $txnid,
    				'amount' => $PayerAmount,
    				'vpa_id' => $PayerVA,
    				'bank_rrno' => $bank_rrno,
    				'description' => 'QR Scan #'.$bank_rrno.' Amount Failed.',
    				'status'=>2,
    				'is_api_response' => 1,
    				'created' => date('Y-m-d H:i:s'),
    				'created_by' => $member_id
    			);
    			$this->db->insert('upi_cash_transaction',$txnData);

		        log_message('debug', 'UPI Cash Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	elseif($chk_txn_qr)
        	{
        		log_message('debug', 'UPI Cash Callback Txn Found from VPA/Dynamic QR Scan.');
        		
		        $this->db->where('account_id',$account_id);
	        	$this->db->where('txnid',$txnid);
	        	$this->db->update('upi_cash_transaction',array('bank_rrno'=>$bank_rrno,'status'=>3,'vpa_id' => $PayerVA,'description' => 'UPI Txn #'.$bank_rrno.' Amount Failed.','is_api_response'=>1,'updated'=>date('Y-m-d H:i:s'),'updated_by'=>1));	

	            log_message('debug', 'UPI Cash Callback Member UPI Wallet Updated After Wallet Balance - '.$after_balance);

	            
        	}
        	
        }
        
    }

    public function vanCallBack()
    {
    	$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API Called.]'.PHP_EOL;
        $this->User->generateVANLog($log_msg);
    	$callbackData = file_get_contents('php://input');   
        $log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API Get Data - '.$callbackData.'.]'.PHP_EOL;
        $this->User->generateVANLog($log_msg);
        $decodeResult = str_replace('"','',$callbackData);

        $key = '8Mvsje9yqjplP6x5All3l1INXSTCwVrZ2pN';
        //$decryptData = $decodeResult;
		$decryptData = $this->decrypt($decodeResult, $key);

		$private_key = $accountData['upi_private_certificate'];

		$private_key = openssl_get_privatekey($private_key, "");
		openssl_private_decrypt($decryptData,$response,$private_key);

		$response = json_decode($response);
		$log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API Decrypt Response - '.$response.'.]'.PHP_EOL;
        $this->User->generateVANLog($log_msg);

        /*$response = '{"status":200,"message":"SUCCESS","data":{"CustomerCode":"CPL7","VirtualACCode":"CPL78813983054","MODE":"FT","UTR":"025906564641","SENDER_REMARK":"","CustomerAccountNo":"721505000184","AMT":"500000","PayeeName":"AMNA  KHATOON","PayeeAccountNumber":"126505001748","PayeeBankIFSC":"ICIC0TREA00","PayeePaymentDate":"04\/01\/2022","BankInternalTransactionNumber":"F22148167460"}}';*/

        $decodeResponse = json_decode($response,true);

        if(isset($decodeResponse['message']) && $decodeResponse['message'] == 'SUCCESS')
        {
        	$log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API Txn Status Success Found.]'.PHP_EOL;
        	$this->User->generateVANLog($log_msg);
        	$data = $decodeResponse['data'];
        	// check virtual account is exits or not
            $chk_virtual_account = $this->db->get_where('users',array('is_virtual_account'=>1,'virtual_account_no'=>$data['VirtualACCode']))->num_rows();
            $is_paid = 0;
            if($chk_virtual_account)
            {
            	$log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API - Virtual Account #'.$data['VirtualACCode'].' Found.]'.PHP_EOL;
        		$this->User->generateVANLog($log_msg);
        		$get_virtual_account = $this->db->select('id,account_id,virtual_wallet_balance,user_code,role_id,wallet_balance')->get_where('users',array('is_virtual_account'=>1,'virtual_account_no'=>$data['VirtualACCode']))->row_array();
            	$member_id = $get_virtual_account['id'];
            	$account_id = $get_virtual_account['account_id'];
            	$member_code = $get_virtual_account['user_code'];
            	$role_id = $get_virtual_account['role_id'];
            	$virtual_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($member_id);
            	$wallet_balance = $this->User->getMemberWalletBalanceSP($member_id);
            	$amount = $data['AMT'];
            	if($role_id == 3 || $role_id == 4 || $role_id == 5)
            	{
	            	$log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API - Virtual Account #'.$data['VirtualACCode'].' Related to Member #'.$member_code.' - Wallet Balance - '.$wallet_balance.' - Txn Amount - '.$amount.'.]'.PHP_EOL;
	            	$this->User->generateVANLog($log_msg);

	            	$after_wallet_balance = $wallet_balance + $amount;

			        $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $member_id,    
			            'before_balance'      => $wallet_balance,
			            'amount'              => $amount,  
			            'after_balance'       => $after_wallet_balance,      
			            'status'              => 1,
			            'type'                => 1,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'VAN Txn #'.$data['UTR'].' Amount Credited.'
			        );

			        $this->db->insert('member_wallet',$wallet_data);

            	}
            	else
            	{
            		$log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API - Virtual Account #'.$data['VirtualACCode'].' Related to Member #'.$member_code.' - Virtual Wallet Balance - '.$virtual_wallet_balance.' - Txn Amount - '.$amount.'.]'.PHP_EOL;
	            	$this->User->generateVANLog($log_msg);

	            	$after_wallet_balance = $virtual_wallet_balance + $amount;

			        $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $member_id,    
			            'before_balance'      => $virtual_wallet_balance,
			            'amount'              => $amount,  
			            'after_balance'       => $after_wallet_balance,      
			            'status'              => 1,
			            'type'                => 1,   
			            'wallet_type'         => 1,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'VAN Txn #'.$data['UTR'].' Amount Credited.'
			        );

			        $this->db->insert('virtual_wallet',$wallet_data);

            	}

            	

		        $log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API - Member #'.$member_code.' - Virtual Updated Wallet Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
            	$this->User->generateVANLog($log_msg);
            	$is_paid = 1;
            }
            else
            {
            	$log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API - Virtual Account #'.$data['VirtualACCode'].' Not Found.]'.PHP_EOL;
        		$this->User->generateVANLog($log_msg);
        		$member_id = 0;
            }
        	// save transcation
        	$txnData = array(
        		'account_id' => $account_id,
        		'member_id' => $member_id,
        		'customer_code' => $data['CustomerCode'],
        		'virtual_account_no' => $data['VirtualACCode'],
        		'mode' => $data['MODE'],
        		'utr' => $data['UTR'],
        		'remark' => $data['SENDER_REMARK'],
        		'client_account_no' => $data['CustomerAccountNo'],
        		'amount' => $data['AMT'],
        		'payer_name' => $data['PayeeName'],
        		'payer_account_no' => $data['PayeeAccountNumber'],
        		'payer_bank_ifsc' => $data['PayeeBankIFSC'],
        		'bank_txn_no' => $data['BankInternalTransactionNumber'],
        		'is_paid' => $is_paid,
        		'payment_date' => $data['PayeePaymentDate'],
        		'created' => date('Y-m-d H:i:s')
        	);
        	$this->db->insert('virtual_txn_history',$txnData);
        	$log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API - Virtual Txn Saved.]'.PHP_EOL;
        	$this->User->generateVANLog($log_msg);
        		
        }
        else
        {
        	$log_msg = '['.date('d-m-Y H:i:s').' - VAN Callback API Txn Status SUCCESS Not Found.]'.PHP_EOL;
        	$this->User->generateVANLog($log_msg);
        }
    }

    public function matmCallBack()
    {
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API Called.]'.PHP_EOL;
        $this->User->generateMATMLog($log_msg);
    	$callbackData = file_get_contents('php://input');   
        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API Get Data - '.$callbackData.'.]'.PHP_EOL;
        $this->User->generateMATMLog($log_msg);

        /*$callbackData = '{"ipaddress":"172.20.3.94","amount":1.0,"transactionStatus":"S","merchantRefNo":"fingpay1645082712993","fpTransactionId":"MACA2455309170222125501835S","aadhaarNumber":null,"typeOfTransaction":"MATMCW","latitude":26.901893333333337,"longitude":75.744405,"mobile":"9109475326","errorMessage":"","bankRRN":null,"merchantName":"Vijay Patel","terminalID":"FAC05478","bankName":"Punjab National Bank","requestedTimestamp":"17/02/2022 12:55:01","merchantID":"PAR261735","deviceIMEI":"bfd0e496d299ad16","cardNumber":"512652******7312","cardType":"MasterCard","balance":0.0,"mposSerialNumber":"63210718914386"}';*/

        $decodeResponse = json_decode($callbackData,true);    

        if(isset($decodeResponse['transactionStatus']) && strtolower($decodeResponse['transactionStatus']) == 'i')
        {
        	// txn is pending
        	$userCode = $decodeResponse['merchantID'];

        	// get member id
        	$getMemberData = $this->db->select('id,account_id')->get_where('users',array('user_code'=>$userCode))->row_array();
        	$member_id = isset($getMemberData['id']) ? $getMemberData['id'] : 0 ;
        	$account_id = isset($getMemberData['account_id']) ? $getMemberData['account_id'] : 0 ;
        	if($member_id)
			{
				$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Found - Member ID - '.$member_id.' - Account ID - '.$account_id.'.]'.PHP_EOL;
				$this->User->generateMATMLog($log_msg);

				$matamData = array(
					'account_id' => $account_id,
					'member_id' => $member_id,
					'ip_address' => $decodeResponse['ipaddress'],
					'ref_no' => $decodeResponse['merchantRefNo'],
					'txn_id' => $decodeResponse['fpTransactionId'],
					'txn_type' => $decodeResponse['typeOfTransaction'],
					'lat' => $decodeResponse['latitude'],
					'lng' => $decodeResponse['longitude'],
					'mobile' => $decodeResponse['mobile'],
					'amount' => $decodeResponse['amount'],
					'bank_rrn' => $decodeResponse['bankRRN'],
					'name' => $decodeResponse['merchantName'],
					'terminal_id' => $decodeResponse['terminalID'],
					'bank_name' => $decodeResponse['bankName'],
					'request_timestamp' => $decodeResponse['requestedTimestamp'],
					'member_code' => $decodeResponse['merchantID'],
					'deviceIMEI' => $decodeResponse['deviceIMEI'],
					'card_number' => $decodeResponse['cardNumber'],
					'card_type' => $decodeResponse['cardType'],
					'mpos_number' => $decodeResponse['mposSerialNumber'],
					'status' => 1,
					'created' => date('Y-m-d H:i:s')
				);
				$this->db->insert('matm_history',$matamData);

				$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - MATM Record Saved.]'.PHP_EOL;
				$this->User->generateMATMLog($log_msg);
        	}
        	else
        	{
        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Not Found.]'.PHP_EOL;
        		$this->User->generateMATMLog($log_msg);
        	}
        }
        elseif(isset($decodeResponse['transactionStatus']) && strtolower($decodeResponse['transactionStatus']) == 's')
        {
        	// txn is pending
        	$userCode = $decodeResponse['merchantID'];
        	$txn_id = $decodeResponse['fpTransactionId'];
        	$amount = $decodeResponse['amount'];

        	//check txn id saved or not
        	$chkTxnId = $this->db->get_where('matm_history',array('txn_id'=>$txn_id,'status'=>1))->num_rows();
        	if($chkTxnId)
        	{
        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Status Already Updated in Pending TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

				$getRecordId = $this->db->get_where('matm_history',array('txn_id'=>$txn_id,'status'=>1))->row_array();
				$recordID = isset($getRecordId['id']) ? $getRecordId['id'] : 0 ;
				// get member id
	        	$getMemberData = $this->db->select('id,account_id,role_id')->get_where('users',array('user_code'=>$userCode))->row_array();
	        	$member_id = isset($getMemberData['id']) ? $getMemberData['id'] : 0 ;
	        	$account_id = isset($getMemberData['account_id']) ? $getMemberData['account_id'] : 0 ;
	        	$member_role_id = isset($getMemberData['role_id']) ? $getMemberData['role_id'] : 0 ;
	        	if($member_id)
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Found - Member ID - '.$member_id.' - Account ID - '.$account_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$matamData = array(
						'status' => 2,
						'updated' => date('Y-m-d H:i:s')
					);
					$this->db->where('account_id',$account_id);
					$this->db->where('member_id',$member_id);
					$this->db->where('txn_id',$txn_id);
					$this->db->update('matm_history',$matamData);

					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Status Updated Success TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$com_type = 5;
			        
			        $admin_id = $this->User->get_admin_id($account_id);
			        $adminCommisionData = $this->User->get_admin_aeps_commission($amount,$account_id,$com_type);
			        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
			        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

			        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Admin Commision/Surcharge - '.$admin_com_amount.' - Is Surcharge - '.$admin_is_surcharge.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

			        $commisionData = $this->User->get_aeps_commission($amount,$member_id,$com_type,$account_id);
			        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
			        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

			        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Member Commision/Surcharge - '.$com_amount.' - Is Surcharge - '.$is_surcharge.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					//get member wallet_balance
					$before_wallet_balance = $this->User->getMemberWalletBalanceSP($member_id);

					// update member wallet
					$after_balance = $before_wallet_balance + $amount;

					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Member Before Balance - '.$before_wallet_balance.' - Updated Balance - '.$after_balance.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $member_id,    
			            'before_balance'      => $before_wallet_balance,
			            'amount'              => $amount,  
			            'after_balance'       => $after_balance,      
			            'status'              => 1,
			            'type'                => 1,   
			            'wallet_type'         => 1,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'MATM Txn #'.$txn_id.' Amount Credited.'
			        );

			        $this->db->insert('member_wallet',$wallet_data);


					// calculate aeps commision
			        if($com_amount)
			        {
			            $commData = array(
			                'account_id' => $account_id,
			                'member_id' => $member_id,
			                'type' => $com_type,
			                'txnID' => $txn_id,
			                'amount' => $amount,
			                'com_amount' => $com_amount,
			                'is_surcharge' => $is_surcharge,
			                'wallet_settle_amount' => $com_amount,
			                'status' => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'created_by'         => $member_id,
			            );
			            $this->db->insert('member_aeps_comm',$commData);

			            //get member wallet_balance
			            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($member_id);
			            if($is_surcharge)
			            {
			                $after_wallet_balance = $before_wallet_balance - $com_amount;

			                $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Member Surcharge Before Balance - '.$before_wallet_balance.' - Surcharge Amount - '.$com_amount.' - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
							$this->User->generateMATMLog($log_msg);

			                $wallet_data = array(
			                    'account_id'          => $account_id,
			                    'member_id'           => $member_id,    
			                    'before_balance'      => $before_wallet_balance,
			                    'amount'              => $com_amount,  
			                    'after_balance'       => $after_wallet_balance,      
			                    'status'              => 1,
			                    'type'                => 2,   
			                    'wallet_type'         => 1,   
			                    'created'             => date('Y-m-d H:i:s'),      
			                    'description'         => 'MATM Txn #'.$txn_id.' Charge Amount Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			            }
			            else
			            {
			                $after_wallet_balance = $before_wallet_balance + $com_amount;

			                $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Member Commision Before Balance - '.$before_wallet_balance.' - Commision Amount - '.$com_amount.' - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
							$this->User->generateMATMLog($log_msg);

			                $wallet_data = array(
			                    'account_id'          => $account_id,
			                    'member_id'           => $member_id,    
			                    'before_balance'      => $before_wallet_balance,
			                    'amount'              => $com_amount,  
			                    'after_balance'       => $after_wallet_balance,      
			                    'status'              => 1,
			                    'type'                => 1,   
			                    'wallet_type'         => 1,   
			                    'created'             => date('Y-m-d H:i:s'),      
			                    'description'         => 'MATM Txn #'.$txn_id.' Commission Amount Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			            }
			        }

			        //get member wallet_balance
			        $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

			        $after_wallet_balance = $before_wallet_balance + $amount;

			        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Admin Before Balance - '.$before_wallet_balance.' - Amount - '.$amount.' - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
							$this->User->generateMATMLog($log_msg);

			        $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $admin_id,    
			            'before_balance'      => $before_wallet_balance,
			            'amount'              => $amount,  
			            'after_balance'       => $after_wallet_balance,      
			            'status'              => 1,
			            'type'                => 1,   
			            'wallet_type'         => 1,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'MATM Txn #'.$txn_id.' Amount Credited.'
			        );

			        $this->db->insert('collection_wallet',$wallet_data);


			        if($admin_com_amount)
			        {
			            $is_paid = 0;
			            if($admin_is_surcharge)
			            {
			                $is_paid = 1;
			            }

			            $commData = array(
			                'account_id' => $account_id,
			                'member_id' => $admin_id,
			                'type' => $com_type,
			                'txnID' => $txn_id,
			                'amount' => $amount,
			                'com_amount' => $admin_com_amount,
			                'is_surcharge' => $admin_is_surcharge,
			                'wallet_settle_amount' => $admin_com_amount,
			                'is_paid' => $is_paid,
			                'status' => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'created_by'         => $member_id,
			            );
			            $this->db->insert('member_aeps_comm',$commData);

			            //get member wallet_balance
			            $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
			            if($admin_is_surcharge)
			            {
			                $after_wallet_balance = $before_wallet_balance - $admin_com_amount;

			                $wallet_data = array(
			                    'account_id'          => $account_id,
			                    'member_id'           => $admin_id,    
			                    'before_balance'      => $before_wallet_balance,
			                    'amount'              => $admin_com_amount,  
			                    'after_balance'       => $after_wallet_balance,      
			                    'status'              => 1,
			                    'type'                => 2,   
			                    'wallet_type'         => 1,   
			                    'created'             => date('Y-m-d H:i:s'),      
			                    'description'         => 'MATM Txn #'.$txn_id.' Charge Amount Debited.'
			                );

			                $this->db->insert('collection_wallet',$wallet_data);

			            }
			            
			        }

			        if($member_role_id == 4 || $member_role_id == 5)
        			{
				        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Distribute Commision Start.]'.PHP_EOL;
						$this->User->generateMATMLog($log_msg);
						
						$this->User->distribute_aeps_commision($recordID,$txn_id,$amount,$member_id,$com_amount,$is_surcharge,$com_type,'DT',$userCode,$account_id,'MATM');
				    	

				        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Distribute Commision End.]'.PHP_EOL;
						$this->User->generateMATMLog($log_msg);
					}
	        	}
	        	else
	        	{
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Not Found.]'.PHP_EOL;
	        		$this->User->generateMATMLog($log_msg);
	        	}
        	}
        	else
        	{
        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Status Already Not Updated in Pending TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

				// get member id
	        	$getMemberData = $this->db->select('id,account_id,role_id')->get_where('users',array('user_code'=>$userCode))->row_array();
	        	$member_id = isset($getMemberData['id']) ? $getMemberData['id'] : 0 ;
	        	$account_id = isset($getMemberData['account_id']) ? $getMemberData['account_id'] : 0 ;
	        	$member_role_id = isset($getMemberData['role_id']) ? $getMemberData['role_id'] : 0 ;
	        	if($member_id)
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Found - Member ID - '.$member_id.' - Account ID - '.$account_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$matamData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'ip_address' => $decodeResponse['ipaddress'],
						'ref_no' => $decodeResponse['merchantRefNo'],
						'txn_id' => $decodeResponse['fpTransactionId'],
						'txn_type' => $decodeResponse['typeOfTransaction'],
						'lat' => $decodeResponse['latitude'],
						'lng' => $decodeResponse['longitude'],
						'mobile' => $decodeResponse['mobile'],
						'amount' => $decodeResponse['amount'],
						'bank_rrn' => $decodeResponse['bankRRN'],
						'name' => $decodeResponse['merchantName'],
						'terminal_id' => $decodeResponse['terminalID'],
						'bank_name' => $decodeResponse['bankName'],
						'request_timestamp' => $decodeResponse['requestedTimestamp'],
						'member_code' => $decodeResponse['merchantID'],
						'deviceIMEI' => $decodeResponse['deviceIMEI'],
						'card_number' => $decodeResponse['cardNumber'],
						'card_type' => $decodeResponse['cardType'],
						'mpos_number' => $decodeResponse['mposSerialNumber'],
						'status' => 2,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('matm_history',$matamData);
					$recordID = $this->db->insert_id();

					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Record Saved TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$com_type = 5;
			        
			        $admin_id = $this->User->get_admin_id($account_id);
			        $adminCommisionData = $this->User->get_admin_aeps_commission($amount,$account_id,$com_type);
			        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
			        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

			        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Admin Commision/Surcharge - '.$admin_com_amount.' - Is Surcharge - '.$admin_is_surcharge.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

			        $commisionData = $this->User->get_aeps_commission($amount,$member_id,$com_type,$account_id);
			        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
			        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

			        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Member Commision/Surcharge - '.$com_amount.' - Is Surcharge - '.$is_surcharge.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					//get member wallet_balance
					$before_wallet_balance = $this->User->getMemberWalletBalanceSP($member_id);

					// update member wallet
					$after_balance = $before_wallet_balance + $amount;

					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Member Before Balance - '.$before_wallet_balance.' - Updated Balance - '.$after_balance.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $member_id,    
			            'before_balance'      => $before_wallet_balance,
			            'amount'              => $amount,  
			            'after_balance'       => $after_balance,      
			            'status'              => 1,
			            'type'                => 1,   
			            'wallet_type'         => 1,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'MATM Txn #'.$txn_id.' Amount Credited.'
			        );

			        $this->db->insert('member_wallet',$wallet_data);

			        
					// calculate aeps commision
			        if($com_amount)
			        {
			            $commData = array(
			                'account_id' => $account_id,
			                'member_id' => $member_id,
			                'type' => $com_type,
			                'txnID' => $txn_id,
			                'amount' => $amount,
			                'com_amount' => $com_amount,
			                'is_surcharge' => $is_surcharge,
			                'wallet_settle_amount' => $com_amount,
			                'status' => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'created_by'         => $member_id,
			            );
			            $this->db->insert('member_aeps_comm',$commData);

			            //get member wallet_balance
			            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($member_id);
			            if($is_surcharge)
			            {
			                $after_wallet_balance = $before_wallet_balance - $com_amount;

			                $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Member Surcharge Before Balance - '.$before_wallet_balance.' - Surcharge Amount - '.$com_amount.' - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
							$this->User->generateMATMLog($log_msg);

			                $wallet_data = array(
			                    'account_id'          => $account_id,
			                    'member_id'           => $member_id,    
			                    'before_balance'      => $before_wallet_balance,
			                    'amount'              => $com_amount,  
			                    'after_balance'       => $after_wallet_balance,      
			                    'status'              => 1,
			                    'type'                => 2,   
			                    'wallet_type'         => 1,   
			                    'created'             => date('Y-m-d H:i:s'),      
			                    'description'         => 'MATM Txn #'.$txn_id.' Charge Amount Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               
			            }
			            else
			            {
			                $after_wallet_balance = $before_wallet_balance + $com_amount;

			                $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Member Commision Before Balance - '.$before_wallet_balance.' - Commision Amount - '.$com_amount.' - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
							$this->User->generateMATMLog($log_msg);

			                $wallet_data = array(
			                    'account_id'          => $account_id,
			                    'member_id'           => $member_id,    
			                    'before_balance'      => $before_wallet_balance,
			                    'amount'              => $com_amount,  
			                    'after_balance'       => $after_wallet_balance,      
			                    'status'              => 1,
			                    'type'                => 1,   
			                    'wallet_type'         => 1,   
			                    'created'             => date('Y-m-d H:i:s'),      
			                    'description'         => 'MATM Txn #'.$txn_id.' Commission Amount Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               
			            }
			        }

			        //get member wallet_balance
			        $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

			        $after_wallet_balance = $before_wallet_balance + $amount;

			        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Admin Before Balance - '.$before_wallet_balance.' - Amount - '.$amount.' - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
							$this->User->generateMATMLog($log_msg);

			        $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $admin_id,    
			            'before_balance'      => $before_wallet_balance,
			            'amount'              => $amount,  
			            'after_balance'       => $after_wallet_balance,      
			            'status'              => 1,
			            'type'                => 1,   
			            'wallet_type'         => 1,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'MATM Txn #'.$txn_id.' Amount Credited.'
			        );

			        $this->db->insert('collection_wallet',$wallet_data);

			        

			        if($admin_com_amount)
			        {
			            $is_paid = 0;
			            if($admin_is_surcharge)
			            {
			                $is_paid = 1;
			            }

			            $commData = array(
			                'account_id' => $account_id,
			                'member_id' => $admin_id,
			                'type' => $com_type,
			                'txnID' => $txn_id,
			                'amount' => $amount,
			                'com_amount' => $admin_com_amount,
			                'is_surcharge' => $admin_is_surcharge,
			                'wallet_settle_amount' => $admin_com_amount,
			                'is_paid' => $is_paid,
			                'status' => 1,
			                'created'             => date('Y-m-d H:i:s'),      
			                'created_by'         => $member_id,
			            );
			            $this->db->insert('member_aeps_comm',$commData);

			            //get member wallet_balance
			            $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
			            if($admin_is_surcharge)
			            {
			                $after_wallet_balance = $before_wallet_balance - $admin_com_amount;

			                $wallet_data = array(
			                    'account_id'          => $account_id,
			                    'member_id'           => $admin_id,    
			                    'before_balance'      => $before_wallet_balance,
			                    'amount'              => $admin_com_amount,  
			                    'after_balance'       => $after_wallet_balance,      
			                    'status'              => 1,
			                    'type'                => 2,   
			                    'wallet_type'         => 1,   
			                    'created'             => date('Y-m-d H:i:s'),      
			                    'description'         => 'MATM Txn #'.$txn_id.' Charge Amount Debited.'
			                );

			                $this->db->insert('collection_wallet',$wallet_data);


			            }
			            
			        }

			        if($member_role_id == 4 || $member_role_id == 5)
        			{
				        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Distribute Commision Start.]'.PHP_EOL;
						$this->User->generateMATMLog($log_msg);
						
						$this->User->distribute_aeps_commision($recordID,$txn_id,$amount,$member_id,$com_amount,$is_surcharge,$com_type,'DT',$userCode,$account_id,'MATM');
				    	

				        $log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Distribute Commision End.]'.PHP_EOL;
						$this->User->generateMATMLog($log_msg);
					}
	        	}
	        	else
	        	{
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Not Found.]'.PHP_EOL;
	        		$this->User->generateMATMLog($log_msg);
	        	}
        	}
        }
        elseif(isset($decodeResponse['transactionStatus']) && strtolower($decodeResponse['transactionStatus']) == 'f')
        {
        	// txn is pending
        	$userCode = $decodeResponse['merchantID'];
        	$txn_id = $decodeResponse['fpTransactionId'];
        	$amount = $decodeResponse['amount'];

        	//check txn id saved or not
        	$chkTxnId = $this->db->get_where('matm_history',array('txn_id'=>$txn_id,'status'=>1))->num_rows();
        	if($chkTxnId)
        	{
        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Status Already Updated in Pending TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

				// get member id
	        	$getMemberData = $this->db->select('id,account_id,role_id')->get_where('users',array('user_code'=>$userCode))->row_array();
	        	$member_id = isset($getMemberData['id']) ? $getMemberData['id'] : 0 ;
	        	$account_id = isset($getMemberData['account_id']) ? $getMemberData['account_id'] : 0 ;
	        	$member_role_id = isset($getMemberData['role_id']) ? $getMemberData['role_id'] : 0 ;
	        	if($member_id)
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Found - Member ID - '.$member_id.' - Account ID - '.$account_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$matamData = array(
						'status' => 3,
						'updated' => date('Y-m-d H:i:s')
					);
					$this->db->where('account_id',$account_id);
					$this->db->where('member_id',$member_id);
					$this->db->where('txn_id',$txn_id);
					$this->db->update('matm_history',$matamData);

					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Status Updated Failed TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

	        	}
	        	else
	        	{
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Not Found.]'.PHP_EOL;
	        		$this->User->generateMATMLog($log_msg);
	        	}
        	}
        	else
        	{
        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Status Already Not Updated in Pending TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

				// get member id
	        	$getMemberData = $this->db->select('id,account_id,role_id')->get_where('users',array('user_code'=>$userCode))->row_array();
	        	$member_id = isset($getMemberData['id']) ? $getMemberData['id'] : 0 ;
	        	$account_id = isset($getMemberData['account_id']) ? $getMemberData['account_id'] : 0 ;
	        	$member_role_id = isset($getMemberData['role_id']) ? $getMemberData['role_id'] : 0 ;
	        	if($member_id)
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Found - Member ID - '.$member_id.' - Account ID - '.$account_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$matamData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'ip_address' => $decodeResponse['ipaddress'],
						'ref_no' => $decodeResponse['merchantRefNo'],
						'txn_id' => $decodeResponse['fpTransactionId'],
						'txn_type' => $decodeResponse['typeOfTransaction'],
						'lat' => $decodeResponse['latitude'],
						'lng' => $decodeResponse['longitude'],
						'mobile' => $decodeResponse['mobile'],
						'amount' => $decodeResponse['amount'],
						'bank_rrn' => $decodeResponse['bankRRN'],
						'name' => $decodeResponse['merchantName'],
						'terminal_id' => $decodeResponse['terminalID'],
						'bank_name' => $decodeResponse['bankName'],
						'request_timestamp' => $decodeResponse['requestedTimestamp'],
						'member_code' => $decodeResponse['merchantID'],
						'deviceIMEI' => $decodeResponse['deviceIMEI'],
						'card_number' => $decodeResponse['cardNumber'],
						'card_type' => $decodeResponse['cardType'],
						'mpos_number' => $decodeResponse['mposSerialNumber'],
						'status' => 3,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('matm_history',$matamData);
					$recordID = $this->db->insert_id();

					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Record Saved TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

	        	}
	        	else
	        	{
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Not Found.]'.PHP_EOL;
	        		$this->User->generateMATMLog($log_msg);
	        	}
        	}
        }
        elseif(isset($decodeResponse['transactionStatus']) && strtolower($decodeResponse['transactionStatus']) != 'i' && strtolower($decodeResponse['transactionStatus']) != 's' && strtolower($decodeResponse['transactionStatus']) != 'f')
        {
        	// txn is pending
        	$userCode = $decodeResponse['merchantID'];
        	$txn_id = $decodeResponse['fpTransactionId'];
        	$amount = $decodeResponse['amount'];

        	//check txn id saved or not
        	$chkTxnId = $this->db->get_where('matm_history',array('txn_id'=>$txn_id,'status'=>1))->num_rows();
        	if($chkTxnId)
        	{
        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Status Already Updated in Pending TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

				// get member id
	        	$getMemberData = $this->db->select('id,account_id,role_id')->get_where('users',array('user_code'=>$userCode))->row_array();
	        	$member_id = isset($getMemberData['id']) ? $getMemberData['id'] : 0 ;
	        	$account_id = isset($getMemberData['account_id']) ? $getMemberData['account_id'] : 0 ;
	        	$member_role_id = isset($getMemberData['role_id']) ? $getMemberData['role_id'] : 0 ;
	        	if($member_id)
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Found - Member ID - '.$member_id.' - Account ID - '.$account_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$matamData = array(
						'status' => 4,
						'updated' => date('Y-m-d H:i:s')
					);
					$this->db->where('account_id',$account_id);
					$this->db->where('member_id',$member_id);
					$this->db->where('txn_id',$txn_id);
					$this->db->update('matm_history',$matamData);

					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Status Updated Failed TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

	        	}
	        	else
	        	{
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Not Found.]'.PHP_EOL;
	        		$this->User->generateMATMLog($log_msg);
	        	}
        	}
        	else
        	{
        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Status Already Not Updated in Pending TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

				// get member id
	        	$getMemberData = $this->db->select('id,account_id,role_id')->get_where('users',array('user_code'=>$userCode))->row_array();
	        	$member_id = isset($getMemberData['id']) ? $getMemberData['id'] : 0 ;
	        	$account_id = isset($getMemberData['account_id']) ? $getMemberData['account_id'] : 0 ;
	        	$member_role_id = isset($getMemberData['role_id']) ? $getMemberData['role_id'] : 0 ;
	        	if($member_id)
				{
					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Found - Member ID - '.$member_id.' - Account ID - '.$account_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

					$matamData = array(
						'account_id' => $account_id,
						'member_id' => $member_id,
						'ip_address' => $decodeResponse['ipaddress'],
						'ref_no' => $decodeResponse['merchantRefNo'],
						'txn_id' => $decodeResponse['fpTransactionId'],
						'txn_type' => $decodeResponse['typeOfTransaction'],
						'lat' => $decodeResponse['latitude'],
						'lng' => $decodeResponse['longitude'],
						'mobile' => $decodeResponse['mobile'],
						'amount' => $decodeResponse['amount'],
						'bank_rrn' => $decodeResponse['bankRRN'],
						'name' => $decodeResponse['merchantName'],
						'terminal_id' => $decodeResponse['terminalID'],
						'bank_name' => $decodeResponse['bankName'],
						'request_timestamp' => $decodeResponse['requestedTimestamp'],
						'member_code' => $decodeResponse['merchantID'],
						'deviceIMEI' => $decodeResponse['deviceIMEI'],
						'card_number' => $decodeResponse['cardNumber'],
						'card_type' => $decodeResponse['cardType'],
						'mpos_number' => $decodeResponse['mposSerialNumber'],
						'status' => 4,
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('matm_history',$matamData);
					$recordID = $this->db->insert_id();

					$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Record Saved TxnID#'.$txn_id.'.]'.PHP_EOL;
					$this->User->generateMATMLog($log_msg);

	        	}
	        	else
	        	{
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MATM Callback API - Merchant Not Found.]'.PHP_EOL;
	        		$this->User->generateMATMLog($log_msg);
	        	}
        	}
        }

    }

    
    public function autoSettlement()
    {
    	$currentHour = date('H');
    	$currentMin = date('i');
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron Start.]'.PHP_EOL;
        $this->User->generateSettlementLog($log_msg);

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Current Hour - '.$currentHour.' - Current Min - '.$currentMin.'.]'.PHP_EOL;
        $this->User->generateSettlementLog($log_msg);

        // get active timezone
        $activeTime = $this->db->get_where('account_settlement_time',array('hour'=>$currentHour,'min'=>$currentMin,'status'=>1))->num_rows();
        if($activeTime)
        {
        	// get active timezone
        	$activeTime = $this->db->get_where('account_settlement_time',array('hour'=>$currentHour,'min'=>$currentMin,'status'=>1))->row_array();
        	$percentage = $activeTime['percentage'];
        	
	        $recordList = $this->db->get_where('account_settlement',array('is_on'=>1))->result_array();

			$accountId = array();
			if($recordList)
			{
				foreach($recordList as $key=>$list)
				{
					$accountId[$key] = $list['account_id'];
				}
			}

	        // get users list
	        $userList = $this->db->get_where('users',array('role_id'=>2))->result_array();

	        // save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Total Account Found - '.count($userList).']'.PHP_EOL;
	        $this->User->generateSettlementLog($log_msg);

	        if($userList)
	        {
	        	foreach($userList as $list)
	        	{
	        		$admin_id = $list['id'];
	        		$account_id = $list['account_id'];
	        		if(in_array($account_id, $accountId))
	        		{
	        			$getPercentage = $this->db->get_where('account_settlement',array('account_id'=>$account_id))->row_array();
				
						$percentage = isset($getPercentage['percentage']) ? $getPercentage['percentage'] : 0 ;
						

		        		$account_name = $list['name'];
		        		$account_code = $list['user_code'];

		        		$collection_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
		        		
		        		// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Settlement Percentage - '.$percentage.'%.]'.PHP_EOL;
				        $this->User->generateSettlementLog($log_msg);

		        		// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Settlement Wallet Balance - '.$collection_wallet_balance.']'.PHP_EOL;
		        		$this->User->generateSettlementLog($log_msg);

		        		// get today aadhar pay balance
		        		$last_day_date = date('Y-m-d',strtotime("-1 days"));
		        		$today_date = date('Y-m-d');
		        		$get_aadhar_pay_balance = $this->db->select('SUM(amount) as totalBalance')->get_where('member_aeps_transaction',array('account_id'=>$account_id,'status'=>2,'service'=>'aadharpay','DATE(created) >='=>$last_day_date))->row_array();
		        		$totalAadharBalance = isset($get_aadhar_pay_balance['totalBalance']) ? $get_aadhar_pay_balance['totalBalance'] : 0 ;

		        		// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Today & Last Day Aadhar Balance - '.$totalAadharBalance.' - Last Date - '.$last_day_date.']'.PHP_EOL;
		        		$this->User->generateSettlementLog($log_msg);

		        		$remainingBalance = $collection_wallet_balance - $totalAadharBalance;

		        		// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Remaining Settlement Balance - '.$remainingBalance.']'.PHP_EOL;
		        		$this->User->generateSettlementLog($log_msg);

		        		$settlementAmount = round(($percentage/100)*$remainingBalance,2);

		        		// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Release Settlement Amount - '.$settlementAmount.']'.PHP_EOL;
		        		$this->User->generateSettlementLog($log_msg);

		        		$updatedSettlementBalance = $collection_wallet_balance - $settlementAmount;

		        		if($settlementAmount > 0)
		        		{
			        		// get account bank detail
			        		$accountData = $this->db->select('account_holder_name,account_number,ifsc,bankID')->get_where('account',array('id'=>$account_id))->row_array();
			        		$account_holder_name = isset($accountData['account_holder_name']) ? $accountData['account_holder_name'] : '';
			        		$account_number = isset($accountData['account_number']) ? $accountData['account_number'] : '';
			        		$ifsc = isset($accountData['ifsc']) ? $accountData['ifsc'] : '';
			        		$bankID = isset($accountData['bankID']) ? $accountData['bankID'] : 0;

			        		if($account_holder_name && $account_number && $ifsc)
			        		{
			        			// save system log
			        			$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Bank Account Found - Account Holder Name - '.$account_holder_name.' - Account No. - '.$account_number.' - IFSC - '.$ifsc.']'.PHP_EOL;
			        			$this->User->generateSettlementLog($log_msg);

			        			$transaction_id = time().rand(1111,9999);

						        $wallet_data = array(
						            'account_id'          => $account_id,
						            'member_id'           => $admin_id,    
						            'before_balance'      => $collection_wallet_balance,
						            'amount'              => $settlementAmount,  
						            'after_balance'       => $updatedSettlementBalance,      
						            'status'              => 1,
						            'type'                => 2,   
						            'wallet_type'         => 1,   
						            'created'             => date('Y-m-d H:i:s'),      
						            'description'         => 'Auto Bank Account#'.$account_number.' TxnID #'.$transaction_id.' Settlement'
						        );

						        $this->db->insert('collection_wallet',$wallet_data);


						        // save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Release Settlement Done Updated Balance - '.$updatedSettlementBalance.']'.PHP_EOL;
				        		$this->User->generateSettlementLog($log_msg);

						        // save system log
			        			$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - CIB Payout API Called - Transfer Amount - '.$settlementAmount.' - TxnID - '.$transaction_id.']'.PHP_EOL;
			        			$this->User->generateSettlementLog($log_msg);

						        $responseData = $this->User->cibAutoSettlement($account_holder_name,$account_number,$ifsc,$settlementAmount,$transaction_id,$bankID,'IFS',$account_code,$account_name,$account_id,$admin_id);

								// save system log
								$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - CIB Payout API - Final Response - '.json_encode($responseData).']'.PHP_EOL;
								$this->User->generateSettlementLog($log_msg);

					            if(isset($responseData['status']) && $responseData['status'] == 3)
								{
									$apiMsg = $responseData['msg'];

									$before_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
									$after_wallet_balance = $before_balance + $settlementAmount;    

						            $wallet_data = array(
						                'account_id'          => $account_id,
						                'member_id'           => $admin_id,    
						                'before_balance'      => $before_balance,
						                'amount'              => $settlementAmount,  
						                'after_balance'       => $after_wallet_balance,      
						                'status'              => 1,
						                'type'                => 1,   
						                'wallet_type'		  => 1,   
						                'created'             => date('Y-m-d H:i:s'),      
						                'description'         => 'Auto Bank Account#'.$account_number.' TxnID #'.$transaction_id.' Settlement Refund'
						            );

						            $this->db->insert('collection_wallet',$wallet_data);

						            
						            // save system log
					        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Settlement Refund Success - Refund Amount - '.$settlementAmount.' -  Updated Balance - '.$after_wallet_balance.']'.PHP_EOL;
					        		$this->User->generateSettlementLog($log_msg);
								}

						        
			        		}
			        		else
			        		{
			        			// save system log
			        			$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Bank Account Not Found.]'.PHP_EOL;
			        			$this->User->generateSettlementLog($log_msg);
			        		}
			        	}
			        	else
			        	{
			        		// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Account Not Settle Due to Negative Balance.]'.PHP_EOL;
			        		$this->User->generateSettlementLog($log_msg);
			        	}
			        }
			        else
				    {
				    	// save system log
				        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement is Off.]'.PHP_EOL;
				        $this->User->generateSettlementLog($log_msg);
				    }
	        	}
	        }
	    }
	    else
	    {
	    	// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Active Timezone Not Found.]'.PHP_EOL;
	        $this->User->generateSettlementLog($log_msg);
	    }
	    
	    

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron End.]'.PHP_EOL;
        $this->User->generateSettlementLog($log_msg);
    }

    public function coduniteAutoSettlement()
    {
    	$domain_account_id = 10;
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron Start.]'.PHP_EOL;
        $this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);

        

        // get users list
        $userList = $this->db->get_where('users',array('account_id'=>$domain_account_id,'role_id'=>6))->result_array();

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Total Account Found - '.count($userList).']'.PHP_EOL;
        $this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);

        if($userList)
        {
        	foreach($userList as $list)
        	{
        		$admin_id = $list['id'];
        		$account_id = $list['account_id'];
        		$account_name = $list['name'];
        		$account_code = $list['user_code'];
        		$wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);
        		$pmr_token = $list['pmr_token'];

        		// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Settlement Wallet Balance - '.$wallet_balance.' - PMR Token - '.$pmr_token.']'.PHP_EOL;
        		$this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);

        		$settlementAmount = $wallet_balance;

        		// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Release Settlement Amount - '.$settlementAmount.']'.PHP_EOL;
        		$this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);

        		$updatedSettlementBalance = $wallet_balance - $settlementAmount;

        		if($settlementAmount > 0)
        		{
	        		if($pmr_token)
	        		{
	        			
	        			$transaction_id = time().rand(1111,9999);

				        $wallet_data = array(
				            'account_id'          => $account_id,
				            'member_id'           => $admin_id,    
				            'before_balance'      => $wallet_balance,
				            'amount'              => $settlementAmount,  
				            'after_balance'       => $updatedSettlementBalance,      
				            'status'              => 1,
				            'type'                => 2,   
				            'wallet_type'         => 1,   
				            'created'             => date('Y-m-d H:i:s'),      
				            'description'         => 'Auto PMR Wallet TxnID #'.$transaction_id.' Settlement'
				        );

				        $this->db->insert('member_wallet',$wallet_data);


				        // save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Release Settlement Done Updated Balance - '.$updatedSettlementBalance.']'.PHP_EOL;
		        		$this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);

				        // save system log
	        			$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - PMR API Called - Transfer Amount - '.$settlementAmount.' - TxnID - '.$transaction_id.']'.PHP_EOL;
	        			$this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);

				        $responseData = $this->User->coduniteAutoSettlement($pmr_token,$settlementAmount,$transaction_id,$account_code,$account_name,$account_id,$admin_id);

						// save system log
						$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - PMR API - Final Response - '.json_encode($responseData).']'.PHP_EOL;
						$this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);

			            if(isset($responseData['status']) && $responseData['status'] == 3)
						{
							$apiMsg = $responseData['msg'];

							$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);
							$after_wallet_balance = $before_balance + $settlementAmount;    

				            $wallet_data = array(
				                'account_id'          => $account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $settlementAmount,  
				                'after_balance'       => $after_wallet_balance,      
				                'status'              => 1,
				                'type'                => 1,   
				                'wallet_type'		  => 1,   
				                'created'             => date('Y-m-d H:i:s'),      
				                'description'         => 'Auto PMR Wallet TxnID #'.$transaction_id.' Settlement Refund'
				            );

				            $this->db->insert('member_wallet',$wallet_data);

				            // save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Settlement Refund Success - Refund Amount - '.$settlementAmount.' -  Updated Balance - '.$after_wallet_balance.']'.PHP_EOL;
			        		$this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);
						}

				        
	        		}
	        		else
	        		{
	        			// save system log
	        			$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - PMR Token Not Found.]'.PHP_EOL;
	        			$this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);
	        		}
	        	}
	        	else
	        	{
	        		// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - Account Not Settle Due to Negative Balance.]'.PHP_EOL;
	        		$this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);
	        	}
        	}
        }
	    

        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron End.]'.PHP_EOL;
        $this->User->generateCoduniteSettlementLog($log_msg,$domain_account_id);
    }

	
	
	/*public function resetdb()
	{
		
		$this->db->query("TRUNCATE TABLE tbl_account_services");
		$this->db->query("TRUNCATE TABLE tbl_account_user_services");
		$this->db->query("TRUNCATE TABLE tbl_cib_api_response");
		$this->db->query("TRUNCATE TABLE tbl_ci_sessions");
		$this->db->query("TRUNCATE TABLE tbl_collection_wallet");
		$this->db->query("TRUNCATE TABLE tbl_login_log");
		$this->db->query("TRUNCATE TABLE tbl_member_wallet");
		$this->db->query("TRUNCATE TABLE tbl_package");
		$this->db->query("TRUNCATE TABLE tbl_upi_commision");
		$this->db->query("TRUNCATE TABLE tbl_upi_dynamic_qr");
		$this->db->query("TRUNCATE TABLE tbl_users");
		$this->db->query("TRUNCATE TABLE tbl_users_otp");
		$this->db->query("TRUNCATE TABLE tbl_user_fund_transfer");
		$this->db->query("TRUNCATE TABLE tbl_virtual_wallet");
		$this->db->query("TRUNCATE TABLE tbl_xpress_payout_charge");
		// put admin entry
		$data = array(
			'role_id' => 1,
			'user_code' => 'TPSA0001',
			'user_code_no' => '1',
			'name' => 'Trust Cart',
			'username' => 'superadmin',
			'password' => '7c4a8d09ca3762af61e59520943dc26494f8941b',
			'decode_password' => '123456',
			'transaction_password' => '7c4a8d09ca3762af61e59520943dc26494f8941b',
			'decoded_transaction_password' => '123456',
			'is_active' => 1,
			'is_verified' => 1,
			'created_by' => 1,
			'created' => date('Y-m-d H:i:s')
		);
		$this->db->insert('users',$data);

		$data = array(
			'account_id' => 1,
			'role_id' => 2,
			'user_code' => 'TPA0001',
			'user_code_no' => '1',
			'name' => 'Trust Cart',
			'username' => 'admin',
			'password' => '7c4a8d09ca3762af61e59520943dc26494f8941b',
			'decode_password' => '123456',
			'transaction_password' => '7c4a8d09ca3762af61e59520943dc26494f8941b',
			'decoded_transaction_password' => '123456',
			'is_active' => 1,
			'is_verified' => 1,
			'created_by' => 1,
			'created' => date('Y-m-d H:i:s')
		);
		$this->db->insert('users',$data);

		die('success');
	}*/


	public function nsdlVerifyCallback()
    {
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback Called.]'.PHP_EOL;
        $this->User->generateNSDLLog($log_msg);
    	
    	$callbackData = file_get_contents('php://input');   
    	
    	$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback - Data - '.$callbackData.']'.PHP_EOL;
        $this->User->generateNSDLLog($log_msg);

    	if(!isset($callbackData['routetype']))
    	{
    		$callbackData = json_decode($callbackData,true);
    	}
    	
		$orderid = $callbackData['orderid'];
		$psacode = $callbackData['psacode'];
		$nameonpan = $callbackData['nameonpan'];
		$psamobile = $callbackData['psamobile'];
		$psaemailid = $callbackData['psaemailid'];
		$status = $callbackData['status'];

		$account_id = 0;
		$member_id = 0;
		$before_balance = 0;
		$charge_amount = 0;
		$after_balance = 0;
		$txnid = time().rand(1111,9999);
		$surcharge_amount = 0;
		$before_balance = 0;
		$after_wallet_balance = 0;

		$admin_surcharge_amount = 0;
		$admin_before_wallet_balance = 0;
		$admin_after_wallet_balance = 0;

		$responseData = array();

		// check psacode is valid or not
		$chkPsaCode = $this->db->get_where('nsdl_kyc',array('status'=>2,'psaid'=>$psacode))->num_rows();
		if($chkPsaCode)
		{
			$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback - PSA Code Verified.]'.PHP_EOL;
        	$this->User->generateNSDLLog($log_msg);

			$getPsaCode = $this->db->get_where('nsdl_kyc',array('status'=>2,'psaid'=>$psacode))->row_array();
			$account_id = $getPsaCode['account_id'];
			$member_id = $getPsaCode['member_id'];

			if($callbackData['routetype'] == 'BOM')
			{
				// get dmr surcharge
        		$surcharge_amount = 0;
        	}
        	else
        	{
        		// get dmr surcharge
        		$surcharge_amount = $this->User->get_member_nsdl_surcharge($account_id,$member_id);
        	}
        	$getMemberWalletBal =$this->db->get_where('users',array('id'=>$member_id))->row_array();
        	$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
        	$member_code = $getMemberWalletBal['user_code'];

        	$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback - Member #'.$member_code.' - Charge Amount - '.$surcharge_amount.' - Before Balance - '.$before_balance.'.]'.PHP_EOL;
        	$this->User->generateNSDLLog($log_msg);

        	if($before_balance < $surcharge_amount)
        	{
        		$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback - Member #'.$member_code.' - Insufficient wallet balance error.]'.PHP_EOL;
        		$this->User->generateNSDLLog($log_msg);
        		$responseData = array(
					'message' => 'Member wallet balance error.',
					'statuscode' => '001',
					'txnid' => $txnid
				);
        	}
        	else
        	{
        		if($callbackData['routetype'] == 'BOM')
				{
					// get dmr surcharge
	        		$admin_surcharge_amount = 0;
	        	}
	        	else
	        	{
	        		// get dmr surcharge
	        		$admin_surcharge_amount = $this->User->get_admin_nsdl_surcharge($account_id,$member_id);
        		}
        		$admin_id = $this->User->get_admin_id($account_id);
        		//get member wallet_balance
		        $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);

		        $log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback - Member #'.$member_code.' - Admin Charge - '.$admin_surcharge_amount.' - Admin Before Wallet Balance - '.$admin_before_wallet_balance.'.]'.PHP_EOL;
        		$this->User->generateNSDLLog($log_msg);

		        if($admin_before_wallet_balance < $admin_surcharge_amount)
            	{
            		$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback - Member #'.$member_code.' - Admin Insufficient wallet balance error.]'.PHP_EOL;
        			$this->User->generateNSDLLog($log_msg);
            		$responseData = array(
						'message' => 'Admin wallet balance error.',
						'statuscode' => '001',
						'txnid' => $txnid
					);
            	}
            	else
            	{
            		if($surcharge_amount)
            		{
            			$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
	            		$after_wallet_balance = $before_balance - $surcharge_amount;

				        $wallet_data = array(
				            'account_id'          => $account_id,
				            'member_id'           => $member_id,    
				            'before_balance'      => $before_balance,
				            'amount'              => $surcharge_amount,  
				            'after_balance'       => $after_wallet_balance,      
				            'status'              => 1,
				            'type'                => 2,   
				            'wallet_type'         => 1,   
				            'created'             => date('Y-m-d H:i:s'),      
				            'description'         => 'NSDL Pan Txn #'.$txnid.' Amount Debited.'
				        );

				        $this->db->insert('member_wallet',$wallet_data);

				        $log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback - Member #'.$member_code.' - Wallet Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
        				$this->User->generateNSDLLog($log_msg);
				    }

				    if($admin_surcharge_amount)
				    {
				    	$admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
				        $admin_after_wallet_balance = $admin_before_wallet_balance - $admin_surcharge_amount;

				        $wallet_data = array(
				            'account_id'          => $account_id,
				            'member_id'           => $admin_id,    
				            'before_balance'      => $admin_before_wallet_balance,
				            'amount'              => $admin_surcharge_amount,  
				            'after_balance'       => $admin_after_wallet_balance,      
				            'status'              => 1,
				            'type'                => 2,   
				            'wallet_type'         => 1,   
				            'created'             => date('Y-m-d H:i:s'),      
				            'description'         => 'NSDL Pan Txn #'.$txnid.' Amount Debited.'
				        );

				        $this->db->insert('virtual_wallet',$wallet_data);


				        $log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback - Member #'.$member_code.' - Admin Wallet Updated Balance - '.$admin_after_wallet_balance.'.]'.PHP_EOL;
        				$this->User->generateNSDLLog($log_msg);
				    }

					$responseData = array(
						'message' => 'Success',
						'statuscode' => '000',
						'txnid' => $txnid
					);
				}
			}
		}
		else
		{
			$responseData = array(
				'message' => 'PSA Code not registered.',
				'statuscode' => '001',
				'txnid' => $txnid
			);
		}

		$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Verfiy Callback - Return Response - '.json_encode($responseData).'.]'.PHP_EOL;
        $this->User->generateNSDLLog($log_msg);

		$recordData = array(
			'account_id' => $account_id,
			'member_id' => $member_id,
			'type' => $callbackData['routetype'],
			'txnid' => $txnid,
			'order_id' => $orderid,
			'psacode' => $psacode,
			'pan_name' => $nameonpan,
			'mobile' => $psamobile,
			'email' => $psaemailid,
			'api_status' => $status,
			'before_balance' => $before_balance,
			'charge_amount' => $surcharge_amount,
			'after_balance' => $after_wallet_balance,
			'admin_before_balance' => $admin_before_wallet_balance,
			'admin_charge' => $admin_surcharge_amount,
			'admin_after_balance' => $admin_after_wallet_balance,
			'status' => 1,
			'response_json' => json_encode($responseData),
			'created' => date('Y-m-d H:i:s')
		);
		$this->db->insert('nsdl_history',$recordData);
    	
    	echo json_encode($responseData);
    }
    
    public function nsdlStatusCallback()
    {
    	$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Status Callback Called.]'.PHP_EOL;
        $this->User->generateNSDLLog($log_msg);
    	
    	$callbackData = file_get_contents('php://input');   
    	$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Status Callback - Data - '.$callbackData.'.]'.PHP_EOL;
        $this->User->generateNSDLLog($log_msg);
    	
    	if(!isset($callbackData['orderid']))
    	{
    		$callbackData = json_decode($callbackData,true);
    	}
    	
		$orderid = $callbackData['orderid'];
		$txnid = $callbackData['txnid'];
		$message = $callbackData['message'];
		$status = $callbackData['status'];
		$statuscode = $callbackData['statuscode'];

		$responseData = array();

		// check txnid valid or not
		$chk_txn_id = $this->db->get_where('nsdl_history',array('txnid'=>$txnid,'order_id'=>$orderid,'status'=>1))->num_rows();
		if($chk_txn_id)
		{
			$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Status Callback - Txnid verified.]'.PHP_EOL;
        	$this->User->generateNSDLLog($log_msg);
			if($status == 'S' && $statuscode == '000')
			{
				$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Status Callback - Status Success Updated.]'.PHP_EOL;
        		$this->User->generateNSDLLog($log_msg);

				$getRecordId = $this->db->get_where('nsdl_history',array('txnid'=>$txnid,'order_id'=>$orderid))->row_array();
				$recordID = isset($getRecordId['id']) ? $getRecordId['id'] : 0;

				$this->db->where('id',$recordID);
				$this->db->update('nsdl_history',array('api_status'=>$status,'status'=>2,'updated'=>date('Y-m-d H:i:s')));
				$responseData = array(
					'message' => 'Success',
					'statuscode' => '000'
				);
			}
			elseif($status == 'F' && $statuscode == '002')
			{
				$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Status Callback - Status Failed Updated.]'.PHP_EOL;
        		$this->User->generateNSDLLog($log_msg);
				$getRecordId = $this->db->get_where('nsdl_history',array('txnid'=>$txnid,'order_id'=>$orderid))->row_array();
				$recordID = isset($getRecordId['id']) ? $getRecordId['id'] : 0;
				$account_id = isset($getRecordId['account_id']) ? $getRecordId['account_id'] : 0;
				$member_id = isset($getRecordId['member_id']) ? $getRecordId['member_id'] : 0;
				$charge_amount = isset($getRecordId['charge_amount']) ? $getRecordId['charge_amount'] : 0;
				$admin_charge = isset($getRecordId['admin_charge']) ? $getRecordId['admin_charge'] : 0;

				$this->db->where('id',$recordID);
				$this->db->update('nsdl_history',array('api_status'=>$status,'status'=>3,'updated'=>date('Y-m-d H:i:s')));

				// refund to member
				if($charge_amount)
				{
					$getMemberWalletBal =$this->db->get_where('users',array('id'=>$member_id))->row_array();
        			$before_balance = $this->User->getMemberWalletBalanceSP($member_id);

        			$after_wallet_balance = $before_balance + $charge_amount;

			        $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $member_id,    
			            'before_balance'      => $before_balance,
			            'amount'              => $charge_amount,  
			            'after_balance'       => $after_wallet_balance,      
			            'status'              => 1,
			            'type'                => 1,   
			            'wallet_type'         => 1,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'NSDL Pan Txn #'.$txnid.' Refund Amount Credited.'
			        );

			        $this->db->insert('member_wallet',$wallet_data);

			        
			        $log_msg = '['.date('d-m-Y H:i:s').' - NSDL Status Callback - Member Wallet Refunded - Updated Balance - '.$after_wallet_balance.'.]'.PHP_EOL;
        			$this->User->generateNSDLLog($log_msg);
				}

				// refund to admin
				if($admin_charge)
				{
					$admin_id = $this->User->get_admin_id($account_id);
	        		//get member wallet_balance
			        $admin_before_wallet_balance = $this->User->getMemberVirtualWalletBalanceSP($admin_id);
					
        			$admin_after_wallet_balance = $admin_before_wallet_balance + $admin_charge;

			        $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $admin_id,    
			            'before_balance'      => $admin_before_wallet_balance,
			            'amount'              => $admin_charge,  
			            'after_balance'       => $admin_after_wallet_balance,      
			            'status'              => 1,
			            'type'                => 1,   
			            'wallet_type'         => 1,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'NSDL Pan Txn #'.$txnid.' Refund Amount Credited.'
			        );

			        $this->db->insert('virtual_wallet',$wallet_data);

			        $log_msg = '['.date('d-m-Y H:i:s').' - NSDL Status Callback - Admin Wallet Refunded - Updated Balance - '.$admin_after_wallet_balance.'.]'.PHP_EOL;
        			$this->User->generateNSDLLog($log_msg);
				}

				$responseData = array(
					'message' => 'Failed',
					'statuscode' => '002'
				);
			}
		}
		else
		{
			$responseData = array(
					'message' => 'Txnid not valid.',
					'statuscode' => '001'
				);
		}

		$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Status Callback - Return Response - '.json_encode($responseData).'.]'.PHP_EOL;
        $this->User->generateNSDLLog($log_msg);
		
		echo json_encode($responseData);

    }
    
    
	public function updateMemberLatLon()
	{
		$userList = $this->db->query("SELECT * FROM `tbl_users` WHERE `aeps_status` = 1 AND aeps_lat IS NULL")->result_array();
		if($userList)
		{
			foreach($userList as $list)
			{
				$memberID = $list['id'];

				// get pincode
				$getPincode = $this->db->select('pin_code')->get_where('aeps_member_kyc',array('member_id'=>$memberID))->row_array();
				$pin_code = isset($getPincode['pin_code']) ? $getPincode['pin_code'] : '';

				//get latitute longtitude
		        $googleResponse = $this->User->get_lat_lon($pin_code);
		        $lat = isset($googleResponse['lat']) ? $googleResponse['lat'] : '';
		        $lng = isset($googleResponse['lng']) ? $googleResponse['lng'] : '';
				
				// update aeps status
            	$this->db->where('id',$memberID);
            	$this->db->update('users',array('aeps_lat'=>$lat,'aeps_lng'=>$lng));
			}
		}
		die('success');
	}

	public function checkCibBalance()
	{
		echo $this->User->getCibBalance();
	}
    
	public function aepsOnBoardCallback()
    {
    	$account_id = $this->User->get_domain_account();
    	$accountData = $this->User->get_account_data($account_id);

    	log_message('debug', 'AEPS ONBOARD Callback API Called.Account -'.$account_id);	
    	$callbackData = file_get_contents('php://input');   
        log_message('debug', 'AEPS ONBOARD Json Data - '.$callbackData.'Account - '.$account_id);


         $cmsCallbackData = json_decode($callbackData,true);

          if($cmsCallbackData['event'] == "FINO_CMS_BALANCE_DEBIT"){

        	log_message('debug', 'Paysprint CMS callback Json Data - '.$callbackData);

        	$txid = $cmsCallbackData['param']['referenceid'];
        	$amount = 	$cmsCallbackData['param']['amount'];
        	$transcation_date = $cmsCallbackData['param']['datetime'];
        	$mobile = 	$cmsCallbackData['param']['mobile'];
        	$network = 	$cmsCallbackData['param']['network'];
        	$uniqueid = $cmsCallbackData['param']['uniqueid'];

        	log_message('debug', 'Paysprint CMS callback referenceid - '.$txid .' Amount - '.$amount);
        		
          	// get member id and amount
        	
        	if($txid)
        	{	

        		$this->db->where('transaction_id',$txid);        		
        		$this->db->update('member_cms_transcation',array('amount'=>$amount,'transcation_date'=>$transcation_date,'mobile'=>$mobile,'network'=>$network,'uniqueid'=>$uniqueid));

        		log_message('debug', 'Paysprint Debit Wallet status.');	

        		$get_member_id = $this->db->get_where('member_cms_transcation',array('transaction_id'=>$txid))->row_array();
        		$member_id = $get_member_id['member_id'];
        		$account_id = $get_member_id['account_id'];

        		$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
        		$type = 1;

        		log_message('debug', 'Paysprint CMS before  wallet balance - '.$before_balance);

        		log_message('debug', 'Paysprint CMS amount - '.$amount);

        		$after_balance = $before_balance - $amount;    

        		$wallet_data = array(
        			'account_id' =>$account_id,
        			'member_id'           => $member_id,    
        			'before_balance'      => $before_balance,
        			'amount'              => $amount,  
        			'after_balance'       => $after_balance,      
        			'status'              => 1,
        			'type'				  =>2, 
        			'wallet_type'		 =>1,     			      
        			'created'             => date('Y-m-d H:i:s'),      
        			'credited_by'         => 1,
        			'description'         => 'CMS Service Amount #'.$txid.' Debited'
        		);

        		$this->db->insert('member_wallet',$wallet_data);

        		log_message('debug', 'Paysprint CMS Wallet Debit successfully'); 
        	}		

        }

        if($cmsCallbackData['event'] == "FINO_CMS_TRANSACTION_FAILED"){

        	log_message('debug', 'Paysprint CMS callback Json Data - '.$callbackData);

        	$txid = $cmsCallbackData['param']['referenceid'];	
        	$status = $cmsCallbackData['param']['status'];	
        	log_message('debug', 'Paysprint cms callback referenceid - '.$txid);
        		
          	// get member id and amount
        	$get_recharge_data = $this->db->get_where('member_cms_transcation',array('account_id'=>$account_id,'transaction_id'=>$txid))->row_array();
        	$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
        	$amount = isset($get_recharge_data['amount']) ? $get_recharge_data['amount'] : 0 ;
        	$recharge_display_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : '' ;
        	if($member_id)
        	{	
        		$this->db->where('account_id',$account_id);
        		$this->db->where('member_id',$member_id);
        		$this->db->where('transaction_id',$txid);
        		$this->db->update('member_cms_transcation',array('status'=>$status));

        		log_message('debug', 'Paysprint cms callback cms failed status updated.');	

        		$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
        		$type = 1;

        		log_message('debug', 'Paysprint cms callback cms before refund wallet balance - '.$before_balance);

        		log_message('debug', 'Paysprint cms callback cms refund amount - '.$amount);

        		$after_balance = $before_balance + $amount;    

        		$wallet_data = array(
        			'account_id'		  =>$account_id,
        			'member_id'           => $member_id,    
        			'before_balance'      => $before_balance,
        			'amount'              => $amount,  
        			'after_balance'       => $after_balance,      
        			'status'              => 1,
        			'wallet_type'		 =>1,
        			'type'                => $type,      
        			'created'             => date('Y-m-d H:i:s'),      
        			'credited_by'         => 1,
        			'description'         => 'CMS Refund #'.$recharge_display_id.' Credited'
        		);

        		$this->db->insert('member_wallet',$wallet_data);


        		log_message('debug', 'Paysprint recharge callback recharge refund successfully'); 
        	}		

        }



        elseif($cmsCallbackData['event'] == "FINO_CMS_TRANSACTION_SUCCESS"){

        	log_message('debug', 'Paysprint Fino CMS  callback Json Data - '.$callbackData);

        	$txid = $cmsCallbackData['param']['referenceid'];
        	$FinoTransactionID = $cmsCallbackData['param']['FinoTransactionID'];
        	$remarks = $cmsCallbackData['param']['remarks'];
        	$ackno = $cmsCallbackData['param']['ackno'];

        	log_message('debug', 'Paysprint Fino CMS callback referenceid - '.$txid);
        		
          	// get member id and amount
        	$get_recharge_data = $this->db->get_where('member_cms_transcation',array('transaction_id'=>$txid))->row_array();
        	$member_id = isset($get_recharge_data['member_id']) ? $get_recharge_data['member_id'] : 0 ;
        	
        	if($member_id)
        	{	
        		$this->db->where('account_id',$account_id);
        		$this->db->where('member_id',$member_id);
        		$this->db->where('transaction_id',$txid);
        		$this->db->update('member_cms_transcation',array('status'=>1,'FinoTransactionID'=>$FinoTransactionID,'remarks'=>$remarks,'ackno'=>$ackno));

        		
        		log_message('debug', 'Paysprint Fino Cms callback cms success status updated.');

        	}		

        }



        
        $callbackStatusData = $this->input->get();
        
        $statusData = isset($callbackStatusData['data']) ? $callbackStatusData['data'] : '';
        
        log_message('debug', 'AEPS ONBOARD Status encoded Data - '.$statusData.'Account - '.$account_id);
        
        if($statusData){
            
            $param_enc = $statusData;
    		$secret = $accountData['paysprint_secret_key'];
    		$status_response = (array) $this->Jwt_model->decode($param_enc,$secret,array('HS256'));
    		
    		log_message('debug', 'AEPS ONBOARD Status decoded Data - '.json_encode($status_response).'Account - '.$account_id);
        }
        
        
        /*$callbackData = '{"event":"MERCHANT_ONBOARDING","param":{"merchant_id":"KMMD956043","partner_id":"PS00611","request_id":"16402650673633","amount":10},"param_enc":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZXJjaGFudF9pZCI6IktNTUQ5NTYwNDMiLCJwYXJ0bmVyX2lkIjoiUFMwMDYxMSIsInJlcXVlc3RfaWQiOiIxNjQwMjY1MDY3MzYzMyIsImFtb3VudCI6MTB9.iGfWhsAIANYXitgoHVNdlaP57oP5I8eHxxJ18-g2IoI"}';*/

        $response = array();
        
        // $response = array(
        //             'status' => '200',
        //             'message' => 'Transaction completed successfully'
        //         );

        $decodeResponse = json_decode($callbackData,true);
        if(isset($decodeResponse['event']) && $decodeResponse['event'] == 'MERCHANT_ONBOARDING')
        {
        	$merchant_id = $decodeResponse['param']['merchant_id'];
        	$request_id = $decodeResponse['param']['request_id'];

        	// check member is valid or not
        	$chk_kyc = $this->db->order_by('created','desc')->get_where('new_aeps_member_kyc',array('account_id'=>$account_id,'member_code'=>$merchant_id,'status'=>0,'clear_step'=>1))->num_rows();
        	if($chk_kyc)
        	{
        		$response = array(
                    'status' => '200',
                    'message' => 'Transaction completed successfully'
                );
        	}
        	else
        	{
        		$response = array(
                    'status' => '400',
                    'message' => 'Transaction Failed'
                );
        	}
        }
        
        if(isset($status_response) && $status_response['status'] == 1)
        {
        	$status = $status_response['status'];
        	$request_id = $status_response['refno'];
        	$merchant_id = $status_response['merchantcode'];
        	$jsonData = json_encode($status_response);

        	// check member is valid or not
        	$kycData = $this->db->order_by('created','desc')->get_where('new_aeps_member_kyc',array('account_id'=>$account_id,'member_code'=>$merchant_id,'status'=>0,'clear_step'=>1))->row_array();
        	
        	$memberID = isset($kycData['member_id']) ? $kycData['member_id'] : 0 ;
        	$recordID = isset($kycData['id']) ? $kycData['id'] : 0 ;
        	
        	if($status == 1)
        	{
	        	// update aeps status
	        	$this->db->where('id',$memberID);
	        	$this->db->where('account_id',$account_id);
	        	$this->db->update('users',array('new_aeps_status'=>1));

	        	// update aeps status
	            $this->db->where('id',$recordID);
	            $this->db->where('account_id',$account_id);
	            $this->db->update('new_aeps_member_kyc',array('status'=>1,'clear_step'=>2,'kyc_data'=>$jsonData));

	            	$get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
	            	$role_id = $get_user_role['role_id'];

	            	if($role_id == 5)
	            	{
	            		$this->Az->redirect('retailer/dashboard', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Your merchant onboard is activated.</div>');
	            	}
	            	elseif($role_id == 4)
	            	{
	            		$this->Az->redirect('distributor/dashboard', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Your merchant onboard is activated.</div>');
	            	}
	            	elseif($role_id == 3)
	            	{
	            		$this->Az->redirect('master/dashboard', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Congratulations ! Your merchant onboard is activated.</div>');
	            	}

	        }
	        else
	        {
	        	// update aeps status
	            $this->db->where('id',$recordID);
	            $this->db->where('account_id',$account_id);
	            $this->db->update('new_aeps_member_kyc',array('status'=>2,'kyc_data'=>$jsonData));

	           	 $get_user_role = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$memberID))->row_array();
	            	$role_id = $get_user_role['role_id'];

	            	if($role_id == 5)
	            	{
	            		$this->Az->redirect('retailer/dashboard', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Your Merchant KYC is failed.</div>');
	            	}
	            	elseif($role_id == 4)
	            	{
	            		$this->Az->redirect('distributor/dashboard', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Your Merchant KYC is failed.</div>');
	            	}
	            	elseif($role_id == 3)
	            	{
	            		$this->Az->redirect('master/dashboard', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! Your Merchant KYC is failed.</div>');
	            	}

	            
	            
	        }
        }
        
        
        echo json_encode($response);
    }


    public function jwtToken()
    
    {
        
        $post=array(
            
            'user_id' =>'ANP2589',
            //'password' =>'123456',
            'mobile' => '7877382309'
            
            );
            
             $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
            
            // Create token payload as a JSON string
            $payload = json_encode($post);
            
            // Encode Header to Base64Url String
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            
            // Encode Payload to Base64Url String
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
            
            // Create Signature Hash
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
            
            // Encode Signature to Base64Url String
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            
            // Create JWT
            $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
            
            echo $jwt;
            
    }
    
  
    public function instantPayoutCallBack()
    {
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Callback API Called.]'.PHP_EOL;
        $this->User->generateCallbackLog($log_msg);
        $post = $this->input->get();

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Callback Get Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateCallbackLog($log_msg);
        if($post)
		{
			
			

			$txid = $post['agent_id'];
			$optxid = $post['ipay_id'];
			$rrn = isset($post['opr_id']) ? $post['opr_id'] : '';			
			$api_status = strtolower($post['status']);
			$api_msg = $post['res_msg'];

			
			// check record

			$dmt_status = $this->db->get_where('user_new_fund_transfer',array('transaction_id'=>$txid,'status'=>2))->num_rows();
			$dmt_status2 = $this->db->get_where('user_new_fund_transfer',array('transaction_id'=>$txid,'status'=>3))->num_rows();

			if($dmt_status)
			{
				// get member id and amount
				$get_recharge_data = $this->db->get_where('user_new_fund_transfer',array('transaction_id'=>$txid,'status'=>2))->row_array();
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Callback Record Data - '.json_encode($get_recharge_data).'.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);
		        $dmt_id = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
				$account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
				$member_id = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
				$amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
				$total_wallet_charge = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
				$transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : '' ;

				

				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Callback Status - '.$api_status.'.]'.PHP_EOL;
		        $this->User->generateCallbackLog($log_msg);
				$status = 0;
				if($api_status == 'success')
				{
					$status = 3;
				}
				elseif($api_status == 'refund' || $api_status == 'refund')
				{
					$status = 4;
				}
				elseif($api_status == 'pending')
				{
					$status = 2;
				}
				
				if($txid)
				{
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Callback API Status Updated.]'.PHP_EOL;
			        $this->User->generateCallbackLog($log_msg);
					// update fund transfer status
					
					$force_status = 0;

					$fundData = array(
						'rrn' => $rrn,
						'status' => $status,
						'force_status' =>$force_status,
						'is_updated_by_callback' =>1,
						'updated' => date('Y-m-d H:i:s')
					);
					$this->db->where('transaction_id',$txid);
					$this->db->update('user_new_fund_transfer',$fundData);

					// refund payment into wallet
					if($status == 4)
					{
						if($member_id)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($member_id);
							$after_balance = $before_balance + $total_wallet_charge;    
							$member_code = $before_balance['user_code'];    

							// save system log
					        $log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Callback API Refund to Member - '.$member_code.' - Before Balance - '.$before_balance.' - Refund Amount - '.$total_wallet_charge.' - After Balance - '.$after_balance.'.]'.PHP_EOL;
					        $this->User->generateCallbackLog($log_msg);
							
							$wallet_data = array(
								'account_id'          => $account_id,
								'member_id'           => $member_id,    
								'before_balance'      => $before_balance,
								'amount'              => $total_wallet_charge,  
								'after_balance'       => $after_balance,      
								'status'              => 1,
								'type'                => 1,  
								'wallet_type'		  => 1,     
								'created'             => date('Y-m-d H:i:s'),      
								'credited_by'         => 1,
								'description'         => 'Payout #'.$transaction_id.' Refund Credited'
					        );

					        $this->db->insert('member_wallet',$wallet_data);
 
					        // get member role id
			        		// get account role id
							$get_role_id = $this->db->select('role_id,dmt_call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
							$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
							$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
							if($user_role_id == 6)
							{
								$user_call_back_url = isset($get_role_id['dmt_call_back_url']) ? $get_role_id['dmt_call_back_url'] : '' ;
								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);

				        	/*	$api_post_data = array();
				        		$api_post_data['status'] = 'FAILED';
				        		$api_post_data['txnid'] = $txid;
				        		$api_post_data['optxid'] = $optxid;
				        		$api_post_data['amount'] = $amount;
				        		$api_post_data['rrn'] = $rrn;*/
				        		
				        	    $user_callback_data_url  = $user_call_back_url.'?status=FAILED&txnid='.$txid.'&optxid='.$optxid.'&amount='.$amount.'&rrn='.$rrn;
				        	    
				        		
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back Data send to API Member - '.$api_member_code.' - Call Back URL - '.$user_callback_data_url.'.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);
				        		

				        		$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
								curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
								curl_setopt($ch, CURLOPT_TIMEOUT, 30);
								//curl_setopt($ch, CURLOPT_POST, true);
								//curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
								$output = curl_exec($ch); 
								curl_close($ch);

								// save system log
				        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back Send Successfully.]'.PHP_EOL;
				        		$this->User->generateCallbackLog($log_msg);
								
							}
						}
					}
					elseif($status == 3)
					{
						// get dmr surcharge
            			$surcharge_amount = $this->User->get_dmr_surcharge($amount,$member_id);
            			// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
		        		$this->User->generateCallbackLog($log_msg);
						// save system log
		        		/*$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Commision Distribute Start.]'.PHP_EOL;
		        		$this->User->generateCallbackLog($log_msg);
						// distribute commision
                    	$this->User->distribute_dmt_commision($dmt_id,$transaction_id,$amount,$member_id,$surcharge_amount);
                    	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - DMT Call Back Commision Distribute End.]'.PHP_EOL;
		        		$this->User->generateCallbackLog($log_msg);*/
						// get member role id
		        		// get account role id
						$get_role_id = $this->db->select('role_id,dmt_call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
						$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
						$api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
						if($user_role_id == 6)
						{
							$user_call_back_url = isset($get_role_id['dmt_call_back_url']) ? $get_role_id['dmt_call_back_url'] : '' ;
							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);

			        		/*$api_post_data = array();
			        		$api_post_data['status'] = 'SUCCESS';
			        		$api_post_data['txnid'] = $txid;
			        		$api_post_data['optxid'] = $optxid;
			        		$api_post_data['amount'] = $amount;
			        		$api_post_data['rrn'] = $rrn;*/
			        		
			        		  $user_callback_data_url  = $user_call_back_url.'?status=SUCCESS&txnid='.$txid.'&optxid='.$optxid.'&amount='.$amount.'&rrn='.$rrn;
			        		  
			        		  	$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back Data send to API Member - '.$api_member_code.' - Call Back URL - '.$user_callback_data_url.'.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);
			        		

			        		$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
							curl_setopt($ch, CURLOPT_TIMEOUT, 30);
							//curl_setopt($ch, CURLOPT_POST, true);
							//curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
							$output = curl_exec($ch); 
							curl_close($ch);

							// save system log
			        		$log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Call Back Send Successfully.]'.PHP_EOL;
			        		$this->User->generateCallbackLog($log_msg);
							
						}
					}
				}
				
			}
			elseif($dmt_status2)
			{
				// save system log
	        	$log_msg = '['.date('d-m-Y H:i:s').' -Instant Payout Call Back - TxnID already success found.]'.PHP_EOL;
	        	$this->User->generateCallbackLog($log_msg);
				$status = 0;
				if($api_status == 'refund')
				{
					$status = 4;
				}
				
				if($status)
				{
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' -Instant Payout Call Back - TxnID refunded status got.]'.PHP_EOL;
	        		$this->User->generateCallbackLog($log_msg);
	        		$fundData = array(
						'is_refund_by_callback' => 1,
						'updated' => date('Y-m-d H:i:s')
					);
					$this->db->where('transaction_id',$txid);
					$this->db->update('user_new_fund_transfer',$fundData);
				}
			}
			else
			{
				// save system log
	        	$log_msg = '['.date('d-m-Y H:i:s').' -Instant Payout Call Back - TxnID not valid or status already updated.]'.PHP_EOL;
	        	$this->User->generateCallbackLog($log_msg);
			}
		}
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Instant Payout Callback API Stop.]'.PHP_EOL;
        $this->User->generateCallbackLog($log_msg);
        
        $jsonData = '{
    "ipay_id": "'.$optxid.'",
    "success": true,
    "description": "callback called successfully"
}';
        
		echo $jsonData;
		
    }
    
    
   public function retailerPanBalance()
    {
    

    	//get logged user info
        $domain_account_id = $this->User->get_domain_account();
        $header_data = apache_request_headers();
       
         $post = file_get_contents('php://input');
           
        log_message('debug', 'NSDL API BALANCE Post  Data - '.json_encode($post));
         	       
		$request_data = json_decode($post, true);
		$request_id = $request_data['RequestID'];
        $session_id = $request_data['SessionID'];
        $retailer_id = $request_data['RetailerID'];
        $extra = $request_data['Extra1'];
        $type = $request_data['Type'];

        $get_wallet_balance = $this->db->get_where('users',array('user_code'=>$retailer_id))->row_array();
        $memberID = $get_wallet_balance['id'];
        $wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);
            
       
        $wallet_array = array(

        	'MainBalance' =>$wallet_balance
        );   
        
        if($wallet_balance){

		        $response = array(
						'StatusCode' => 1,
						'Message' => 'Success',
						'Transactions' => $wallet_array
				); 
				
			

			}
			else
			{
			
			 $response = array(
						'StatusCode' => 0,
						'Message' => 'Failure',
						'Transactions' => null
				); 
			}
		            
		    echo json_encode($response);
		        log_message('debug', 'NSDL API BALANCE Response  Data - '.json_encode($response));
    }
    
    
    
    public function nsdlPanCallBack(){

      	$account_id = $this->User->get_domain_account();

        $callbackData = file_get_contents('php://input');   
        //$callbackData = '"{\"StatusCode\":1,\"Message\":\"Success\",\"Transactions\":{\"Number\":\"8619651646\",\"Amount\":72.00,\"AckNo\":\"991239700028432\",\"OrderID\":\"188000168\",\"TxnDate\":\"2\/24\/2023 5:56:04 PM\",\"Status\":\"Success\",\"Type\":\"N\"},\"AgentID\":\"MPCNR703985\",\"TxnId\":\"100021105\"}"';
		


         log_message('debug', 'NSDL API Get Data - '.json_encode($callbackData).'Account ID - '.$account_id);
       	
	        $final_callback = json_decode($callbackData, true);
	        //$final_callback = json_decode($decode_callback, true);

       

       		$mobile = $final_callback['Transactions']['Number'];
        	$amount = $final_callback['Transactions']['Amount'];
        	$ackno = $final_callback['Transactions']['AckNo'];
        	$order_id = $final_callback['Transactions']['OrderID'];
        	$txn_date = $final_callback['Transactions']['TxnDate'];
        	$pan_status = $final_callback['Transactions']['Status'];
        	$type = $final_callback['Transactions']['Type'];
        	$member_id = $final_callback['AgentID'];
        	$callback_msg = $final_callback['Message'];        	
        	$txn_id = $final_callback['TxnId'];

        	$status = 0;

        	if($pan_status == 'Success')

			{
				$status = 1;

				

			}
			elseif($pan_status == 'Failure')

			{
				$status = 2;
			}

			elseif($pan_status == 'Pending')

			{
				$status = 3;
			}

			elseif($pan_status == 'Reversal')

			{
				$status = 4;
			}

			else

			{
				$status = 5;

			}


        	$chk_member = $this->db->get_where('users',array('account_id'=>$account_id,'user_code'=>$member_id))->num_rows();

        	$get_member_data = $this->db->get_where('users',array('account_id'=>$account_id,'user_code'=>$member_id))->row_array();


        	$memberID  = isset($get_member_data['id']) ? $get_member_data['id'] : 0 ;

        	if(isset($final_callback['StatusCode']) && $final_callback['StatusCode'] == 1)

        	{
        		

        	$log_msg = '['.date('d-m-Y H:i:s').' - NSDL Callback API Txn Status Success Found.'.$member_id.']'.PHP_EOL;
        	$this->User->generateCallbackLog($log_msg);

        	$member_package_id = $this->User->getMemberPackageID($memberID);
            
            // get commission
           $charge =  $this->User->get_pan_charge($memberID);

          // $gst_amount = $charge*18/100;

		  
		   
		   	

            
            $wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);
            
            $log_msg = '['.date('d-m-Y H:i:s').' - R('.$member_id.') - Wallet Balance - '.$wallet_balance.']'.PHP_EOL;
            $this->User->generateCallbackLog($log_msg);
            

            $after_wallet_balance = $wallet_balance - $charge;

			        $wallet_data = array(
			            'account_id'          => $account_id,
			            'member_id'           => $memberID,    
			            'before_balance'      => $wallet_balance,
			            'amount'              => $charge,  
			            'after_balance'       => $after_wallet_balance,      
			            'status'              => 1,
			            'wallet_type'		  =>1,
			            'type'                => 2,   
			            'created'             => date('Y-m-d H:i:s'),      
			            'description'         => 'NSDL PAN Txn #'.$txn_id.' Amount Debited.'
			        );

			        $this->db->insert('member_wallet',$wallet_data);
			        $recordID = $this->db->insert_id();

			        
                        
			     $com_amount = $this->User->get_pan_charge($memberID);
			     
			     
			      $log_msg = '['.date('d-m-Y H:i:s').' - ('.$member_id.') - Charge Amount -  '.$com_amount.']'.PHP_EOL;
                        $this->User->generateCallbackLog($log_msg);


			     
			      $log_msg = '['.date('d-m-Y H:i:s').' - R('.$member_id.') - Distribute Commision Start]'.PHP_EOL;
                        $this->User->generateCallbackLog($log_msg);


        		$this->User->distribute_pan_commision($recordID,$txn_id,$memberID,$com_amount);
        		
        		  $log_msg = '['.date('d-m-Y H:i:s').' - R('.$member_id.') - Distribute Commision End]'.PHP_EOL;
                        $this->User->generateCallbackLog($log_msg);



        	}

        		//save pan transcation

        		$data = array(
        		'account_id' =>$account_id,
        		'user_id' =>$memberID,
        		'user_mobile'=>$mobile,
        		'ackno' =>$ackno,
        		'order_id' =>$order_id,
        		'txn_date' =>$txn_date,
        		'pan_status'=>$status,
        		'type'	=>$type,
        		'txn_id' =>$txn_id,
        		'callback_msg' =>$callback_msg,
        		'callback_data' =>json_encode($final_callback),
        		'created' =>date('Y-m-d H:i:s')
        		);

        		$this->db->insert('morningpay_pancard_history',$data);
        		$log_msg = '['.date('d-m-Y H:i:s').' -  Morning pay NSDL Callback API - NSDL Txn Saved.]'.PHP_EOL;
        		$this->User->generateCallbackLog($log_msg);

    }


    /*public function walletReview()
	{
	   
	    $account_id = 2;
	    
	    // get admin id
	    $memberList = $this->db->query("SELECT * FROM tbl_users WHERE account_id = '$account_id'")->result_array();
	    if($memberList)
	    {
	        foreach($memberList as $getAdminID)
	        {
	            $member_id = isset($getAdminID['id']) ? $getAdminID['id'] : 0;
        	    $current_c_wallet_bal = isset($getAdminID['wallet_balance']) ? $getAdminID['wallet_balance'] : 0;
        	    
        		$walletList = $this->db->get_where('member_wallet',array('account_id'=>$account_id,'member_id'=>$member_id,'wallet_type'=>1))->result_array();
        		$closing_balance = 0;
        		$before_balance = 0;
        		if($walletList)
        		{
        			foreach($walletList as $list)
        			{
        				$record_id = $list['id'];
        				$amount = $list['amount'];
        				$type = $list['type'];
        				$after_balance = 0;
        				if($type == 1)
        				{
        					$after_balance = $before_balance + $amount;
        				}
        				elseif($type == 2)
        				{
        					$after_balance = $before_balance - $amount;
        				}
        				$closing_balance = $after_balance;
        				$this->db->where('id',$record_id);
        				$this->db->where('account_id',$account_id);
        				$this->db->where('member_id',$member_id);
        				$this->db->update('member_wallet',array('before_balance'=>$before_balance,'after_balance'=>$after_balance));
        				$before_balance = $after_balance;
        			}
        		}
        		
        		$this->db->where('id',$member_id);
        		$this->db->where('account_id',$account_id);
        		$this->db->update('users',array('wallet_balance'=>0));
        
        		echo 'Member : '.$getAdminID['user_code'].' Current Balance : '.$current_c_wallet_bal.' New Balance : '.$closing_balance.'<br />';
	        }
	    }
        	    
	        

		die('Done');
	}*/
	
	public function walletReview()
	{
	   
	    $account_id = 2;
	   
	            $member_id = 3;
        	    $current_c_wallet_bal = 44315575;
        	    
        		$walletList = $this->db->get_where('collection_wallet',array('account_id'=>$account_id,'member_id'=>$member_id,'wallet_type'=>1))->result_array();
        		$closing_balance = 0;
        		$before_balance = 0;
        		if($walletList)
        		{
        			foreach($walletList as $list)
        			{
        				$record_id = $list['id'];
        				$amount = $list['amount'];
        				$type = $list['type'];
        				$after_balance = 0;
        				if($type == 1)
        				{
        					$after_balance = $before_balance + $amount;
        				}
        				elseif($type == 2)
        				{
        					$after_balance = $before_balance - $amount;
        				}
        				$closing_balance = $after_balance;
        				$this->db->where('id',$record_id);
        				$this->db->where('account_id',$account_id);
        				$this->db->where('member_id',$member_id);
        				$this->db->update('collection_wallet',array('before_balance'=>$before_balance,'after_balance'=>$after_balance));
        				$before_balance = $after_balance;
        			}
        		}
        		
        		$this->db->where('id',$member_id);
        		$this->db->where('account_id',$account_id);
        		$this->db->update('users',array('collection_wallet_balance'=>0));
        
        		echo 'Member : '.$getAdminID['user_code'].' Current Balance : '.$current_c_wallet_bal.' New Balance : '.$closing_balance.'<br />';
	        

		die('Done');
	}
    
    
    public function cibTestUat()
    {
        
        $transaction_id = time().rand(1111,9999);
		
		
		$header = [
		    'Content-type: text/plain',
		    'apikey: 5VhMXeQ9y0uyB98BAQlx9VDlq9a0PYZj'
		];
		
		
		// Balance Inquiry
		/*$data = array 
		(
		    "AGGRID"=>"OTOE0622",    
		    "CORPID" => "PRACHICIB1", 
		    "USERID" => "USER3", 
		    "URN" => "SR234708898", 
		    "ACCOUNTNO" => "000451000301"
		);

		$plainText = json_encode($data);
		$payload = base64_encode($this->sslEncrypt($plainText));
		
        $api_url = 'https://apibankingonesandbox.icicibank.com/api/Corporate/CIB/v1/BalanceInquiry';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);*/
		
		
		
		// Generate OTP API
		
		
		/*$data = array 
		(
		    "AGGRID"=>"OTOE0622",    
		    "AGGRNAME" => "TRUSTNCART", 
		    "CORPID" => "PRACHICIB1", 
		    "USERID" => "USER3", 
		    "URN" => "SR234708898", 
		    "UNIQUEID" => $transaction_id, 
		    "AMOUNT" => "1"
		);

		/*echo $plainText = json_encode($data);
		echo '<br />';
		$payload = base64_encode($this->sslEncrypt($plainText));
		
        echo $api_url = 'https://apibankingonesandbox.icicibank.com/api/Corporate/CIB/v1/Create';
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);*/
		
		
		// Transaction API
		/*$data = array 
		(
		    "AGGRID"=>"OTOE0622",    
		    "AGGRNAME" => "TRUSTNCART", 
		    "CORPID" => "SESPRODUCT", 
		    "USERID" => "389018", 
		    "URN" => "SR234708898", 
		    "UNIQUEID" => $transaction_id, 
		    "DEBITACC" => "000405001257", 
		    "CREDITACC" => "000451000301", 
		    "IFSC" => "ICIC0000011", 
		    "TXNTYPE" => "TPA", 
		    "AMOUNT" => "100", 
		    "PAYEENAME" => "Test User", 
		    "REMARKS" => "Fund Transfer", 
		    "CURRENCY" => "INR"
		);

		echo $plainText = json_encode($data);
		$payload = base64_encode($this->sslEncrypt($plainText));
		echo '<br />';
        echo $api_url = 'https://apibankingonesandbox.icicibank.com/api/Corporate/CIB/v1/Transaction';
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);*/
		
		// Transaction Status API
		/*$data = array 
		(
		    "AGGRID"=>"OTOE0622",
		    "CORPID" => "SESPRODUCT", 
		    "USERID" => "389018", 
		    "URN" => "SR234708898", 
		    "UNIQUEID" => "16825794638604"
		);

		echo $plainText = json_encode($data);
		$payload = base64_encode($this->sslEncrypt($plainText));
		echo '<br />';
        echo $api_url = 'https://apibankingonesandbox.icicibank.com/api/Corporate/CIB/v1/TransactionInquiry';
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);*/
		
		
		// Account Statement API
		/*$data = array 
		(
		    "AGGRID"=>"OTOE0622",
		    "CORPID" => "SESPRODUCT", 
		    "USERID" => "389018", 
		    "ACCOUNTNO" => "000405001257",
		    "FROMDATE" => "20-04-2023", 
		    "TODATE" => "27-04-2023",
		    "URN" => "SR234708898"
		);

		echo $plainText = json_encode($data);
		$payload = base64_encode($this->sslEncrypt($plainText));
		
		$header = [
            'apikey:5VhMXeQ9y0uyB98BAQlx9VDlq9a0PYZj',
            'Content-type:text/plain'
        ];
		
		echo '<br />';
        echo $api_url = 'https://apibankingonesandbox.icicibank.com/api/Corporate/CIB/v1/AccountStatement';
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);
		
		echo '<br />Decode Result <br />';
		
        $priv_key = '-----BEGIN RSA PRIVATE KEY-----
MIIJKAIBAAKCAgEAsnNp1x6+9OMQLs+50IzLySezm9GWSc5C48XIRPaC+sW+d1Rw
1UTDisUUr4K0GHPr1/JL8LXXjJEv7mJ0RuGWX2F4EYdSXrtYKPLzYONbUdAZmZpN
cjd9xfXRTHEZ+5N0taVqDuVsI4cDlkbUdw0/q/5eiP7b471JiUgpe62DHaPC6xpA
MkbRrfszB8XGELfbDlNuMEK1METWA9b7DmYPeq81kbjVmiulyS1YoHczZmCavKRY
pP54BKrcOvx353zXube3r+09Ej64bSaaWaS0CzowHIJSx1V/w5RKFoF0PnzgAdu5
52Bpy++Ksi+kCJQHakx6d+8bj21EvnQVaXms5tK95cSrUU1W2zj8UAROjuwliZY9
BShQP8SZJIvmOtypogMgjDzZ+CaXN68waI/rKLXG4qE2TTyqpLWVKF8qrsJSJcVR
N5fTvsSPWVBw2ew193L4S4p75io6PkMBRPTVKaOfrcYloHlrPKCn5gHHaUjiGWZT
VZPkUvTigdoYacAGNtEgcAcsS6mdYADSHxsNRmya9o9CX+8mC3Qz9s1hJy5vzsRl
jtBSzr63V4gCpXtD3H6UtE3qXx4T44SHerRUeKmW2GenNUUQmUvmXWv42kh8kJNC
P6SC86PYMwbdTWjMiq9qT4NMxUHi6yPUpuywdU50fyniP68e15sRrc8jegMCAwEA
AQKCAgBQTyyUyZt6ri18Q7QGLTcRIjLsrxgJwy/LPhlxH9e2cAPVxES7ViUCcMts
aVAPqSu8laijfdKxyi1eBST7OU7pQf49NT9Wrs1wMFZjhi501UiQHic4fcy2qHg3
BLeCxsvBa94dMhbGrl5o5Rt9MJM1HlcBJGFlTqyngbhZlq7pSefQ0pGNjt2ShPhk
SRdoMrX87oMqaPsN7Ay80aVOx5OzzOI44IwQxA/qR+QY40xYiKVavEPAjV0KDLLs
QO7dWQvk4s9h90yCx4NMbBEOwtbcLqW0TtpeJxZGuJfXJQ9hh+VwMKirfnJee0Fa
C6Kw0Z28swpyq0Ml+zDy3V89hqrOvXNl56Jnq+mAYcVfT0ZoQf5d/klRU31phKU6
Xc1ciV+Zbjkdu7HDHHQaGzainDhHbCfAcUy9pCRr2vmadS1nGGJaUDalQ2Pxb5zD
E+hi5pvR2Lkk+zndBzkLUTk4TQ3M4GPdq2NzSnPnysXpwPWbG0BLRVqXXFUXS8B2
BhaeM0k1Pb3txQxFsr+7A1p/NEXB1nBeBAF0DgFsbNNzRPLAZw9GH1R8hLACNCjd
oji24007czbwEvId1DR53vYDUKHymZhZe2MLZybeps7GwEHfcD5ToBywRnyxY2yW
QsRvE9C9rJPNBvEotBszzNTpPOaYcD5X6Bw9+hvqGYFrHKTZcQKCAQEA6Qcau1qO
/VqiU47ZK/OwxqL7G48UCouiMNNgaQUYe9B6CQVn2KTlbFLY+XTZu7FOvXJ5WJ3w
leB+/SzdIo0KdvKhf6QL+N61ywkFZbVlVJ656QpEM02SvyRi+x3cXaznyBNJJzk5
tDSbACHAOznkGM56YjSFnopmhMvuBLtXZsJeUAHMOi6gX65W4JI6If3e4XHr0/Gs
AZnFlfH35WaWbuf4KlISKc2vqReEGIFplvSLih8yO+nnoM9g7opFfxzyGwGtjOOD
C/9gUDB15fY9LK3BDMI3l4qNSZruRNbQvZ6KjIYCUGeokmVP8m4W4RdMdnkccvsn
yBJEQKjaFd09DQKCAQEAxAr1FuNL2UVjzDlKKACsCyuV9JnXzJa5yS5dkU4qh+fn
nBCoTIiMPfIHd5jA9KnA7xWVCaO085HriWKjvvOsQ6cnNAN4BQU096pgnkS6ICJS
CR0t9k7NnpccRba2Sf5aJFAV9EUr/fftRhEAV00z8CWeTrKjqpj1gnBzCWJhk/9J
4101JJ8AYENRYk59KBiRviSuRZ9o/eezk+z330OeoysGOyHGawHXZ+GsEn1jTDSG
KGUpYb/iARNLnQVLgln0wkDpnranAcrZWk6ndUUwnGimvoQxwVgWFNGtaAchcUmW
0oWk0qt7UX1u9fSmNwh5YmHXAGbBDNMCgsZAFLlvTwKCAQEAuHNCKpiU5F/wa1l/
93VOMPzi7L6FK4+5UxKNlrNM3Px5DFj2CRsE6ohtbI+cpR/E5toMySNDQy9O9VGk
vGuNo/eL8//C5jxLA6phVk+OJLv7BkZ1E3LMvHWtz32kZ5WsZcc2OVDnpweYxTLx
+S9qqGQPpVpThdmhKm5NOfucRB+IDaZOpKMxmGrkI6A7WZqc6DCHbd02vJGeP4En
KrLYUnNVERKjg+lmqN6PVeJh1PY+2Za16YzNJpHf9REHz4T28n+Sgxm3KjD7aJ3j
RKJza8EhNNsqq84k5eU3ws+SrPUoT/DnNgPHABInhQq1G3iYspJM/Yplw80Jr3C4
J2RWpQKCAQBmLMPSewK0Kds6vH0u3jLM25mbU3dKtR/9f8Hakp/OF4r6JyBgSya0
vmkv5xhiK/tXYKs9y+nqrJnTD+sCAeQ9mmfvTwOFslIJ5u3Wb0GGr/yLrX6gCjBW
wLFGkFTvubZniKn4lvi3tDkhNIk19xHjzud0Yty0dGY45ry+Hl13Ei4DZzfkb051
3YAUOY43kJ6dOGbv+IZzFwjcRzxlS8vphOoJdbABY4NOLCtPs7RGKnXlpdvsi2KS
ZukY3IKfXJ0ZhVV9l/rxDzU7QRU8JKSSUGTflOyNtYhEr4euWVEPx2fpLyhZeHCc
Z0Cmxiy/MBZ7tTymg+eH9I4xdHw/kOo3AoIBAB6xGDS5G0vhUOQ5oNknAcq/Syx6
lNljvJrwx9ymFmPSmkzWJcmntdkH95NsyDCnGIEBmOXZXp75eTBGkejVvlqszDjI
MXYeuR2y4yjvy5vfYIP0h4ApCZzJdMfJqaEtc/JNdfDnm0l1bYIINJmwdhmJzLsq
CEMtEK/fn6rLeFx/0xagIEP+Fhl5fUeKUjo81SYY6AoWom4fB6cmMk3B2DabzIJs
2cFhTDuEo6iMC/oRlEtC/8vthUrRYXzNPHkXVGUap5r2OT8AbLaibMNQXa89OzyL
MSnE4Vqdli9KnPSXBaOB/FZN9+j+vuPdM9eNtxHTayFI+g3lrgpPlPbI4oI=
-----END RSA PRIVATE KEY-----';
        
        $res = openssl_get_privatekey($priv_key, "");
        $data = json_decode($result, true);
    
        $encryptedKey = base64_decode($data['encryptedKey']);
        $encryptedData = base64_decode($data['encryptedData']);
        openssl_private_decrypt($encryptedKey, $key, $priv_key);
        $encData = openssl_decrypt($encryptedData,"aes-128-cbc",$key,OPENSSL_PKCS1_PADDING);
        echo $newsource = substr($encData, 16); */
        
        
		die;
		
    }
    
    public function cibTestLive()
    {
        
        $transaction_id = time().rand(1111,9999);
		
		
		$header = [
		    'Content-type: text/plain',
		    'apikey: eASJqqNDQGnQsIb1FqMxYJAj4Dy9nZld'
		];
		
		
		// Balance Inquiry
		/*$data = array 
		(
		    "AGGRID"=>"OTOE0622",    
		    "CORPID" => "578854260", 
		    "USERID" => "SAMADNAI", 
		    "URN" => "SR234708898", 
		    "ACCOUNTNO" => "114705001499"
		);

		echo $plainText = json_encode($data);
		echo '<br />';
		$payload = base64_encode($this->sslEncrypt($plainText));
		
        echo $api_url = 'https://apibankingone.icicibank.com/api/Corporate/CIB/v1/BalanceInquiry';
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);
		
		echo '<br />';
		
		echo $response = $this->sslDecrypt(base64_decode($result)); 
		
		die;*/
		
		
		
		// Generate OTP API
		
		
		/*$data = array 
		(
		    "AGGRID"=>"OTOE0622",    
		    "AGGRNAME" => "TRUSTNCART", 
		    "CORPID" => "PRACHICIB1", 
		    "USERID" => "USER3", 
		    "URN" => "SR234708898", 
		    "UNIQUEID" => $transaction_id, 
		    "AMOUNT" => "1"
		);

		/*echo $plainText = json_encode($data);
		echo '<br />';
		$payload = base64_encode($this->sslEncrypt($plainText));
		
        echo $api_url = 'https://apibankingonesandbox.icicibank.com/api/Corporate/CIB/v1/Create';
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);*/
		
		
		// Transaction API
		/*$data = array 
		(
		    "AGGRID"=>"OTOE0622",    
		    "AGGRNAME" => "TRUSTNCART", 
		    "CORPID" => "578854260", 
		    "USERID" => "SAMADNAI", 
		    "URN" => "SR234708898", 
		    "UNIQUEID" => $transaction_id, 
		    "DEBITACC" => "114705001499", 
		    "CREDITACC" => "023501546776", 
		    "IFSC" => "ICIC0000011", 
		    "TXNTYPE" => "TPA", 
		    "AMOUNT" => "100", 
		    "PAYEENAME" => "Test User", 
		    "REMARKS" => "Fund Transfer", 
		    "CURRENCY" => "INR"
		);

		echo $plainText = json_encode($data);
		$payload = base64_encode($this->sslEncrypt($plainText));
		echo '<br />';
        echo $api_url = 'https://apibankingone.icicibank.com/api/Corporate/CIB/v1/Transaction';
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);
		
		echo '<br />';
		
		echo $response = $this->sslDecrypt(base64_decode($result)); 
		
		die;*/
		
		// Transaction Status API
		/*$data = array 
		(
		    "AGGRID"=>"OTOE0622",
		    "CORPID" => "578854260", 
		    "USERID" => "SAMADNAI", 
		    "URN" => "SR234708898", 
		    "UNIQUEID" => "16826864545174"
		);

		echo $plainText = json_encode($data);
		$payload = base64_encode($this->sslEncrypt($plainText));
		echo '<br />';
        echo $api_url = 'https://apibankingone.icicibank.com/api/Corporate/CIB/v1/TransactionInquiry';
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);
		
		echo '<br />';
		
		echo $response = $this->sslDecrypt(base64_decode($result)); 
		
		die;*/
		
		
		// Account Statement API
		$data = array 
		(
		    "AGGRID"=>"OTOE0622",
		    "CORPID" => "578854260", 
		    "USERID" => "SAMADNAI", 
		    "ACCOUNTNO" => "114705001499",
		    "FROMDATE" => "28-04-2023", 
		    "TODATE" => "28-04-2023",
		    "URN" => "SR234708898"
		);

		echo $plainText = json_encode($data);
		$payload = base64_encode($this->sslEncrypt($plainText));
		
		echo '<br />';
        echo $api_url = 'https://apibankingone.icicibank.com/api/Corporate/CIB/v1/AccountStatement';
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// SSL Verify Peer
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		// SSL Verify Host
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		// Timeout
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// HTTP Version
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);

		// Close
		curl_close ($curl);
		
		echo '<br />Decode Result <br />';
		
        $priv_key = '-----BEGIN RSA PRIVATE KEY-----
MIIJKAIBAAKCAgEAsnNp1x6+9OMQLs+50IzLySezm9GWSc5C48XIRPaC+sW+d1Rw
1UTDisUUr4K0GHPr1/JL8LXXjJEv7mJ0RuGWX2F4EYdSXrtYKPLzYONbUdAZmZpN
cjd9xfXRTHEZ+5N0taVqDuVsI4cDlkbUdw0/q/5eiP7b471JiUgpe62DHaPC6xpA
MkbRrfszB8XGELfbDlNuMEK1METWA9b7DmYPeq81kbjVmiulyS1YoHczZmCavKRY
pP54BKrcOvx353zXube3r+09Ej64bSaaWaS0CzowHIJSx1V/w5RKFoF0PnzgAdu5
52Bpy++Ksi+kCJQHakx6d+8bj21EvnQVaXms5tK95cSrUU1W2zj8UAROjuwliZY9
BShQP8SZJIvmOtypogMgjDzZ+CaXN68waI/rKLXG4qE2TTyqpLWVKF8qrsJSJcVR
N5fTvsSPWVBw2ew193L4S4p75io6PkMBRPTVKaOfrcYloHlrPKCn5gHHaUjiGWZT
VZPkUvTigdoYacAGNtEgcAcsS6mdYADSHxsNRmya9o9CX+8mC3Qz9s1hJy5vzsRl
jtBSzr63V4gCpXtD3H6UtE3qXx4T44SHerRUeKmW2GenNUUQmUvmXWv42kh8kJNC
P6SC86PYMwbdTWjMiq9qT4NMxUHi6yPUpuywdU50fyniP68e15sRrc8jegMCAwEA
AQKCAgBQTyyUyZt6ri18Q7QGLTcRIjLsrxgJwy/LPhlxH9e2cAPVxES7ViUCcMts
aVAPqSu8laijfdKxyi1eBST7OU7pQf49NT9Wrs1wMFZjhi501UiQHic4fcy2qHg3
BLeCxsvBa94dMhbGrl5o5Rt9MJM1HlcBJGFlTqyngbhZlq7pSefQ0pGNjt2ShPhk
SRdoMrX87oMqaPsN7Ay80aVOx5OzzOI44IwQxA/qR+QY40xYiKVavEPAjV0KDLLs
QO7dWQvk4s9h90yCx4NMbBEOwtbcLqW0TtpeJxZGuJfXJQ9hh+VwMKirfnJee0Fa
C6Kw0Z28swpyq0Ml+zDy3V89hqrOvXNl56Jnq+mAYcVfT0ZoQf5d/klRU31phKU6
Xc1ciV+Zbjkdu7HDHHQaGzainDhHbCfAcUy9pCRr2vmadS1nGGJaUDalQ2Pxb5zD
E+hi5pvR2Lkk+zndBzkLUTk4TQ3M4GPdq2NzSnPnysXpwPWbG0BLRVqXXFUXS8B2
BhaeM0k1Pb3txQxFsr+7A1p/NEXB1nBeBAF0DgFsbNNzRPLAZw9GH1R8hLACNCjd
oji24007czbwEvId1DR53vYDUKHymZhZe2MLZybeps7GwEHfcD5ToBywRnyxY2yW
QsRvE9C9rJPNBvEotBszzNTpPOaYcD5X6Bw9+hvqGYFrHKTZcQKCAQEA6Qcau1qO
/VqiU47ZK/OwxqL7G48UCouiMNNgaQUYe9B6CQVn2KTlbFLY+XTZu7FOvXJ5WJ3w
leB+/SzdIo0KdvKhf6QL+N61ywkFZbVlVJ656QpEM02SvyRi+x3cXaznyBNJJzk5
tDSbACHAOznkGM56YjSFnopmhMvuBLtXZsJeUAHMOi6gX65W4JI6If3e4XHr0/Gs
AZnFlfH35WaWbuf4KlISKc2vqReEGIFplvSLih8yO+nnoM9g7opFfxzyGwGtjOOD
C/9gUDB15fY9LK3BDMI3l4qNSZruRNbQvZ6KjIYCUGeokmVP8m4W4RdMdnkccvsn
yBJEQKjaFd09DQKCAQEAxAr1FuNL2UVjzDlKKACsCyuV9JnXzJa5yS5dkU4qh+fn
nBCoTIiMPfIHd5jA9KnA7xWVCaO085HriWKjvvOsQ6cnNAN4BQU096pgnkS6ICJS
CR0t9k7NnpccRba2Sf5aJFAV9EUr/fftRhEAV00z8CWeTrKjqpj1gnBzCWJhk/9J
4101JJ8AYENRYk59KBiRviSuRZ9o/eezk+z330OeoysGOyHGawHXZ+GsEn1jTDSG
KGUpYb/iARNLnQVLgln0wkDpnranAcrZWk6ndUUwnGimvoQxwVgWFNGtaAchcUmW
0oWk0qt7UX1u9fSmNwh5YmHXAGbBDNMCgsZAFLlvTwKCAQEAuHNCKpiU5F/wa1l/
93VOMPzi7L6FK4+5UxKNlrNM3Px5DFj2CRsE6ohtbI+cpR/E5toMySNDQy9O9VGk
vGuNo/eL8//C5jxLA6phVk+OJLv7BkZ1E3LMvHWtz32kZ5WsZcc2OVDnpweYxTLx
+S9qqGQPpVpThdmhKm5NOfucRB+IDaZOpKMxmGrkI6A7WZqc6DCHbd02vJGeP4En
KrLYUnNVERKjg+lmqN6PVeJh1PY+2Za16YzNJpHf9REHz4T28n+Sgxm3KjD7aJ3j
RKJza8EhNNsqq84k5eU3ws+SrPUoT/DnNgPHABInhQq1G3iYspJM/Yplw80Jr3C4
J2RWpQKCAQBmLMPSewK0Kds6vH0u3jLM25mbU3dKtR/9f8Hakp/OF4r6JyBgSya0
vmkv5xhiK/tXYKs9y+nqrJnTD+sCAeQ9mmfvTwOFslIJ5u3Wb0GGr/yLrX6gCjBW
wLFGkFTvubZniKn4lvi3tDkhNIk19xHjzud0Yty0dGY45ry+Hl13Ei4DZzfkb051
3YAUOY43kJ6dOGbv+IZzFwjcRzxlS8vphOoJdbABY4NOLCtPs7RGKnXlpdvsi2KS
ZukY3IKfXJ0ZhVV9l/rxDzU7QRU8JKSSUGTflOyNtYhEr4euWVEPx2fpLyhZeHCc
Z0Cmxiy/MBZ7tTymg+eH9I4xdHw/kOo3AoIBAB6xGDS5G0vhUOQ5oNknAcq/Syx6
lNljvJrwx9ymFmPSmkzWJcmntdkH95NsyDCnGIEBmOXZXp75eTBGkejVvlqszDjI
MXYeuR2y4yjvy5vfYIP0h4ApCZzJdMfJqaEtc/JNdfDnm0l1bYIINJmwdhmJzLsq
CEMtEK/fn6rLeFx/0xagIEP+Fhl5fUeKUjo81SYY6AoWom4fB6cmMk3B2DabzIJs
2cFhTDuEo6iMC/oRlEtC/8vthUrRYXzNPHkXVGUap5r2OT8AbLaibMNQXa89OzyL
MSnE4Vqdli9KnPSXBaOB/FZN9+j+vuPdM9eNtxHTayFI+g3lrgpPlPbI4oI=
-----END RSA PRIVATE KEY-----';
        
        $res = openssl_get_privatekey($priv_key, "");
        $data = json_decode($result, true);
    
        $encryptedKey = base64_decode($data['encryptedKey']);
        $encryptedData = base64_decode($data['encryptedData']);
        openssl_private_decrypt($encryptedKey, $key, $priv_key);
        $encData = openssl_decrypt($encryptedData,"aes-128-cbc",$key,OPENSSL_PKCS1_PADDING);
        echo $newsource = substr($encData, 16); 
        
        
		die;
		
    }
    
    
    public function sslEncrypt($dataToEncrypt)
	{
		
		//BANK PUBLIC KEY UAT
		/*$public_key = '-----BEGIN CERTIFICATE-----
MIIFhDCCA2wCCQCIqfcsomoC1jANBgkqhkiG9w0BAQUFADCBgzELMAkGA1UEBhMCSU4xFDASBgNV
BAgMC01BSEFSQVNIVFJBMQ8wDQYDVQQHDAZNVU1CQUkxGDAWBgNVBAoMD0lDSUNJIEJhbmsgTHRk
LjEMMAoGA1UECwwDQ0lCMSUwIwYDVQQDDBx3d3cuY2libmV4dGFwaS5pY2ljaWJhbmsuY29tMB4X
DTE3MDQxMzA3MTkyOVoXDTIyMDQxMjA3MTkyOVowgYMxCzAJBgNVBAYTAklOMRQwEgYDVQQIDAtN
QUhBUkFTSFRSQTEPMA0GA1UEBwwGTVVNQkFJMRgwFgYDVQQKDA9JQ0lDSSBCYW5rIEx0ZC4xDDAK
BgNVBAsMA0NJQjElMCMGA1UEAwwcd3d3LmNpYm5leHRhcGkuaWNpY2liYW5rLmNvbTCCAiIwDQYJ
KoZIhvcNAQEBBQADggIPADCCAgoCggIBAM+en2ErEsETmfoZJjf3I5DIc8KAt6dv/ZkKYHcpli1g
yLFjJNbZnyk4Um7UaKvU0fpqnsLboapXKR0iHFp4/7SR9kTh4FfvFrrp2pKmQd8f/Rf6OPk2/48i
X7sCs0nl8IZYMqe1Tt1YAMFPJjPIH/ERx3vnWYhgHUmRGJqjHfo4NeNR0IarF3HAYX4hh6K0LaAQ
hUoq6SuWyWf9m9qzHRHpWq4eJRsbhPYLaTtt8XS+vPpBjFjfQreDtgWdIXwKuHq8EOS/KxBifThC
tEBMGZUSYZBoldq1kdaakkt5FaXhe+g0FWrLcxalaSS4bHK0QCv1Lbh3tcPetCO3XyR1Jj28SL+5
gYm464jmjMGURJwocWUhuNd0qAKt8bMv9NCDgKiWSmAlzeznRYeNaay2ckg5aB5tNO5l/8pUh8Ew
qLyKECFnCoNvBlcaoJIvZ0sprQO+dHzggT/Q9wl0XRFUkPh4SFGHIiqldy6VgA6I7uVWb7ve1Y3P
4yhlfTDV/Hr4ZL4gTFVrorS1a4Tqap38iqHnfM3djwgwbnzv0TJZCywZ5ED8MRDmub6W4jNYMVar
uG1gLVf7gE2sUY/dgTRu1Hdw3/YlOY9XpQebBP37RD3+Up+oEYxjPe04Cy4rTFx9/8SluuBPvwNH
WVmHkv1ULNHum0VQ3kej17jbEeO+FftJAgMBAAEwDQYJKoZIhvcNAQEFBQADggIBAMGX2dKuXsGj
ujhKxZOFzo8A0QKu+nsw+pFtiJ5KjyOR1vW9pOdG7roJJGr6cU5fUDlUpYDDVIvPiVbPYgWLkVe3
7+tpM8T77ZYSXdO7G9hhU8uw2pcRHiQMlDotV/RcTGZHyVVaw7TJty3xMH2j0/FIHejcFaYXZYQB
A5+zKc7PBsvwn/KQgJ9R4BTqmdWeca1r0+iBXGq1iRg4IGePf0lIc+80AUneC1ceC07RfvI0PJpk
LVTkDCXdNK7QtG/cIqjdZ1jtB+ne7cwtksw1ewu5dE3BFNmqdT3DmKHAupTc2ILSup2w/JEEepMI
DHO8GvqR0dUXS5xCcXNKwXUMiLPYA56mRKoST5+e2RO5WtVQMHiizEF5iID+WjyXNlVtqMarEjih
Z0+/vkABp/Q3AfKs3rtaXxU4crt+RLaaldG/dBXOoUDTpaNR+ktUkNmEPTe9zc7pwwRDC2zNylt4
FnhNP2b2t+RLuP+smAROVaXA1owpte3zeh7aiUe02Y6udEzVrKCAvRUiCKoCDH9N101k3lzCFy80
rRquHZ7ZZmUrX4DksuPnSuLILR5ss6UkQTZbg7HXtMN2lDTgPjO2UMCjqI+5gPGTqdld4XWDTEW0
xdyhJiEgATeqQllbn47B7C7603ltWFpoInafn2NwxBW89wv938bMKpxFxmQcseGH
-----END CERTIFICATE-----
';*/

//BANK PUBLIC KEY LIVE

$public_key = '-----BEGIN CERTIFICATE-----
MIIFiTCCA3GgAwIBAgIJAPhKHX+xSWb7MA0GCSqGSIb3DQEBBQUAMFsxCzAJBgNVBAYTAklOMRQw
EgYDVQQIDAtNQUhBUkFTSFRSQTEPMA0GA1UEBwwGTVVNQkFJMRcwFQYDVQQKDA5JQ0lDSSBCYW5r
IEx0ZDEMMAoGA1UECwwDQlRHMB4XDTE3MDkyNTA4NTcwM1oXDTIwMDYyMDA4NTcwM1owWzELMAkG
A1UEBhMCSU4xFDASBgNVBAgMC01BSEFSQVNIVFJBMQ8wDQYDVQQHDAZNVU1CQUkxFzAVBgNVBAoM
DklDSUNJIEJhbmsgTHRkMQwwCgYDVQQLDANCVEcwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIK
AoICAQCpyw5vtvzONTBwIB89oI6tNmONluYlac/IGsOIJgz/NHUbvONTQasTEcFNAQLgGkljV3ZN
o2ld8Yl6njjAqd1RFfNLbcNDq5AzWRqHEvIfbdcna/wRCz1KUVS+GyZjjoDBovoAZFNo/jM6WU6D
bA4iDW7KaSkTgczt6/0vNo5/BpiDluFNLUUHtlM6D4l9ZFw/A9xoE7jms5saTCoYMz/3Vgpr6lmp
g7gckfHmHEfecSwT0N639+wGEAGdfxzAr3yEc6yCE9XjBIRiTFafBJO32SeO6LQsjl8YGa7mYsQN
Yj+Xt2+kztyq4/M5/I5En3rWVKhP6s4o7bB10uZPO2DHEo49OHnCr2MVq0lwco341xGKPaVwZ9oI
fZX6Jh7ca0y3hTXABZrA5sXfmYwaxYxz/4o1JYeiYjqSvYcKnNt7c7pcpYLKiBC/6RENxVgoNqnY
QJZj/mYkcmvNPFmHvnAGtmnRA+hm06we0dMUO0ZQJhSqP6sfM5oDeZqMAIy291YWW7Hpoimti8db
GD+pMFQxjzS5cuxPl/JjHfPRLUx/MSf26Xu1hhgfh4/9lseuNAjuHfqQS/KiT6BnpuqoMpXkx9K0
FPcfrd8TdHhuGGihuyEtEfj+3G2uMSYE4xEmDx5BQCTXA6x5I6IQyNUN+IorkbDTOJfB2tjxhbQz
rgITHQIDAQABo1AwTjAdBgNVHQ4EFgQUWI7/jLcNvrchEffA3NCjgmTDHSMwHwYDVR0jBBgwFoAU
WI7/jLcNvrchEffA3NCjgmTDHSMwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOCAgEAlfzy
H4x6x7QUtFuL8liD6WO6Vn8W9r/vuEH3YlpiWpnggzRPq2tnDZuJ+3ohB//PSBtCHKDu28NKJMLE
NqAVpgFtashkFlmMAXTpy4vYnTfj3MyAHYr9fwtvEmUKEfiIIC1WXDQzWWP4dFLdJ//jint9bdyM
Iqx+H5ddPXmfWXwAsCs3GlXGVwEmtcc9v7OliCHyyO2s++L+ATz5FoyxKCmZyn1GHD3gmvFjXicI
WB+Us1uRkrDFO8clS1hWvmvF/ghfGYmlKOqTzu/TCY4d9u/CciNesens3iSHEgs58r/9gaxwpiEs
tRolx9eVjkem1ZI5IUCUbRC40r8sL+eEObcwhVV87nrKH2l0BX8nM/ux0lqAkRO+Ek9tdP5TmHT0
XE2E/PMJO7/AlzYvN3oznT9ZeKfu6WbNIZrFCcO6GsoNi8+pKZsWuSePbrhRQC+d3whHS7tAanS8
+6gbPMMoAfkSKt0yaogld6RI2Af1C6QerxZR2LcJM5ni8eCz1cIvS3XSpkG5hcRMXHJAGkc5GAoE
Dj08gZbQVtE4FeJRfTJoX6cpXM6cBODsi8xKzpBCGNNcA/p4r/6XGg2csXyKCCLrVtk0VNKyr/Ba
6T5dfbbuzGcbL/dVd5d/7A9cGJTkk2gRxIL6bBMKn0Qm68mSDUhVFg001zi0JR3nOy9M6Hs=
-----END CERTIFICATE-----
';
        
        
		openssl_get_publickey($public_key);
		openssl_public_encrypt($dataToEncrypt,$encryptedText,$public_key);
		return $encryptedText;
	} 

	public function cibEncrypt($plainText, $key)
	{
		$secretKey = $this->cibhextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$openMode = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
		$encryptedText = bin2hex($openMode);
		return $encryptedText;
	} 

	public function cibDecrypt($encryptedText, $key)
	{
		$key = $this->cibhextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$encryptedText = $this->cibhextobin($encryptedText);
		$decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
		return $decryptedText;
	} 

	public function sslDecrypt($dataToDecrypt)
	{
		$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIIJKAIBAAKCAgEAsnNp1x6+9OMQLs+50IzLySezm9GWSc5C48XIRPaC+sW+d1Rw
1UTDisUUr4K0GHPr1/JL8LXXjJEv7mJ0RuGWX2F4EYdSXrtYKPLzYONbUdAZmZpN
cjd9xfXRTHEZ+5N0taVqDuVsI4cDlkbUdw0/q/5eiP7b471JiUgpe62DHaPC6xpA
MkbRrfszB8XGELfbDlNuMEK1METWA9b7DmYPeq81kbjVmiulyS1YoHczZmCavKRY
pP54BKrcOvx353zXube3r+09Ej64bSaaWaS0CzowHIJSx1V/w5RKFoF0PnzgAdu5
52Bpy++Ksi+kCJQHakx6d+8bj21EvnQVaXms5tK95cSrUU1W2zj8UAROjuwliZY9
BShQP8SZJIvmOtypogMgjDzZ+CaXN68waI/rKLXG4qE2TTyqpLWVKF8qrsJSJcVR
N5fTvsSPWVBw2ew193L4S4p75io6PkMBRPTVKaOfrcYloHlrPKCn5gHHaUjiGWZT
VZPkUvTigdoYacAGNtEgcAcsS6mdYADSHxsNRmya9o9CX+8mC3Qz9s1hJy5vzsRl
jtBSzr63V4gCpXtD3H6UtE3qXx4T44SHerRUeKmW2GenNUUQmUvmXWv42kh8kJNC
P6SC86PYMwbdTWjMiq9qT4NMxUHi6yPUpuywdU50fyniP68e15sRrc8jegMCAwEA
AQKCAgBQTyyUyZt6ri18Q7QGLTcRIjLsrxgJwy/LPhlxH9e2cAPVxES7ViUCcMts
aVAPqSu8laijfdKxyi1eBST7OU7pQf49NT9Wrs1wMFZjhi501UiQHic4fcy2qHg3
BLeCxsvBa94dMhbGrl5o5Rt9MJM1HlcBJGFlTqyngbhZlq7pSefQ0pGNjt2ShPhk
SRdoMrX87oMqaPsN7Ay80aVOx5OzzOI44IwQxA/qR+QY40xYiKVavEPAjV0KDLLs
QO7dWQvk4s9h90yCx4NMbBEOwtbcLqW0TtpeJxZGuJfXJQ9hh+VwMKirfnJee0Fa
C6Kw0Z28swpyq0Ml+zDy3V89hqrOvXNl56Jnq+mAYcVfT0ZoQf5d/klRU31phKU6
Xc1ciV+Zbjkdu7HDHHQaGzainDhHbCfAcUy9pCRr2vmadS1nGGJaUDalQ2Pxb5zD
E+hi5pvR2Lkk+zndBzkLUTk4TQ3M4GPdq2NzSnPnysXpwPWbG0BLRVqXXFUXS8B2
BhaeM0k1Pb3txQxFsr+7A1p/NEXB1nBeBAF0DgFsbNNzRPLAZw9GH1R8hLACNCjd
oji24007czbwEvId1DR53vYDUKHymZhZe2MLZybeps7GwEHfcD5ToBywRnyxY2yW
QsRvE9C9rJPNBvEotBszzNTpPOaYcD5X6Bw9+hvqGYFrHKTZcQKCAQEA6Qcau1qO
/VqiU47ZK/OwxqL7G48UCouiMNNgaQUYe9B6CQVn2KTlbFLY+XTZu7FOvXJ5WJ3w
leB+/SzdIo0KdvKhf6QL+N61ywkFZbVlVJ656QpEM02SvyRi+x3cXaznyBNJJzk5
tDSbACHAOznkGM56YjSFnopmhMvuBLtXZsJeUAHMOi6gX65W4JI6If3e4XHr0/Gs
AZnFlfH35WaWbuf4KlISKc2vqReEGIFplvSLih8yO+nnoM9g7opFfxzyGwGtjOOD
C/9gUDB15fY9LK3BDMI3l4qNSZruRNbQvZ6KjIYCUGeokmVP8m4W4RdMdnkccvsn
yBJEQKjaFd09DQKCAQEAxAr1FuNL2UVjzDlKKACsCyuV9JnXzJa5yS5dkU4qh+fn
nBCoTIiMPfIHd5jA9KnA7xWVCaO085HriWKjvvOsQ6cnNAN4BQU096pgnkS6ICJS
CR0t9k7NnpccRba2Sf5aJFAV9EUr/fftRhEAV00z8CWeTrKjqpj1gnBzCWJhk/9J
4101JJ8AYENRYk59KBiRviSuRZ9o/eezk+z330OeoysGOyHGawHXZ+GsEn1jTDSG
KGUpYb/iARNLnQVLgln0wkDpnranAcrZWk6ndUUwnGimvoQxwVgWFNGtaAchcUmW
0oWk0qt7UX1u9fSmNwh5YmHXAGbBDNMCgsZAFLlvTwKCAQEAuHNCKpiU5F/wa1l/
93VOMPzi7L6FK4+5UxKNlrNM3Px5DFj2CRsE6ohtbI+cpR/E5toMySNDQy9O9VGk
vGuNo/eL8//C5jxLA6phVk+OJLv7BkZ1E3LMvHWtz32kZ5WsZcc2OVDnpweYxTLx
+S9qqGQPpVpThdmhKm5NOfucRB+IDaZOpKMxmGrkI6A7WZqc6DCHbd02vJGeP4En
KrLYUnNVERKjg+lmqN6PVeJh1PY+2Za16YzNJpHf9REHz4T28n+Sgxm3KjD7aJ3j
RKJza8EhNNsqq84k5eU3ws+SrPUoT/DnNgPHABInhQq1G3iYspJM/Yplw80Jr3C4
J2RWpQKCAQBmLMPSewK0Kds6vH0u3jLM25mbU3dKtR/9f8Hakp/OF4r6JyBgSya0
vmkv5xhiK/tXYKs9y+nqrJnTD+sCAeQ9mmfvTwOFslIJ5u3Wb0GGr/yLrX6gCjBW
wLFGkFTvubZniKn4lvi3tDkhNIk19xHjzud0Yty0dGY45ry+Hl13Ei4DZzfkb051
3YAUOY43kJ6dOGbv+IZzFwjcRzxlS8vphOoJdbABY4NOLCtPs7RGKnXlpdvsi2KS
ZukY3IKfXJ0ZhVV9l/rxDzU7QRU8JKSSUGTflOyNtYhEr4euWVEPx2fpLyhZeHCc
Z0Cmxiy/MBZ7tTymg+eH9I4xdHw/kOo3AoIBAB6xGDS5G0vhUOQ5oNknAcq/Syx6
lNljvJrwx9ymFmPSmkzWJcmntdkH95NsyDCnGIEBmOXZXp75eTBGkejVvlqszDjI
MXYeuR2y4yjvy5vfYIP0h4ApCZzJdMfJqaEtc/JNdfDnm0l1bYIINJmwdhmJzLsq
CEMtEK/fn6rLeFx/0xagIEP+Fhl5fUeKUjo81SYY6AoWom4fB6cmMk3B2DabzIJs
2cFhTDuEo6iMC/oRlEtC/8vthUrRYXzNPHkXVGUap5r2OT8AbLaibMNQXa89OzyL
MSnE4Vqdli9KnPSXBaOB/FZN9+j+vuPdM9eNtxHTayFI+g3lrgpPlPbI4oI=
-----END RSA PRIVATE KEY-----';
		$private_key = openssl_get_privatekey($private_key, "");
		openssl_private_decrypt($dataToDecrypt,$decryptedText,$private_key);
		return $decryptedText;
	} 

	public function cibhextobin($hexString)
	{
		$length = strlen($hexString);
		$binString = "";
		$count = 0;
		while ($count < $length)
		{
			$subString = substr($hexString, $count, 2);
			$packedString = pack("H*", $subString);
			if ($count == 0)
			{ $binString = $packedString; }
			else
			{ $binString .= $packedString; }
			$count += 2;
		}
		return $binString;
	} 
	
	
	
	public function payoutBank()
	{
		
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.instantpay.in/payments/payout/banks',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'X-Ipay-Auth-Code: 1',
    'X-Ipay-Client-Id: YWY3OTAzYzNlM2ExZTJlOTiCwv/jys9zRoS1vFYAByc=',
    'X-Ipay-Client-Secret: e14baa3fd2d6a8a7edafa9ac7afa16dfb47bec0833b9962d7cfab47c8521dba7',
    'X-Ipay-Endpoint-Ip: 103.129.97.70',
    'Content-Type: application/json',
    'X-Ipay-Outlet-Id: 216754'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;


$data = json_encode($response,true);

echo "<pre>";
print_r($data);
die();


	}
	
	
	
	public function updateMemberAepsStatus()
	{
		$account_id = $this->User->get_domain_account();
		 $recordList = $this->db->get_where('aeps_member_login_status',array('account_id'=>$account_id,'status'=>1))->result_array();
        if($recordList)
        {
            foreach($recordList as $list)
            {
                $recordID = $list['id'];
                $member_id = $list['member_id'];
               	
                
                if($member_id)
                {
                    $Data = array(
                        'status' => 0,
                        
                    );
                    $this->db->where('id',$recordID);
                    $this->db->update('aeps_member_login_status',$Data);
                }
               
            }

        }
       
        die('done');
        log_message('debug', 'Member 2FA Login Status Update.');
		echo json_encode(array('status'=>1,'msg'=>'success'));
	}
	
	
	
		public function updateMember2FaAepsStatus()
	{
		$account_id = $this->User->get_domain_account();
		 $recordList = $this->db->get_where('instantpay_aeps_member_login_status',array('account_id'=>$account_id,'status'=>1))->result_array();
        if($recordList)
        {
            foreach($recordList as $list)
            {
                $recordID = $list['id'];
                $member_id = $list['member_id'];
               	
                
                if($member_id)
                {
                    $Data = array(
                        'status' => 0,
                        
                    );
                    $this->db->where('id',$recordID);
                    $this->db->update('instantpay_aeps_member_login_status',$Data);
                }
               
            }

        }
       
        die('done');
        log_message('debug', 'Member 2FA Login Status Update.');
		echo json_encode(array('status'=>1,'msg'=>'success'));
	}
	
	
	
	
	
	
	
	function testLoginSms()
	
	{
	    $post['mobile'] = '8619651646';
	    #$user_display_id = 'PAYL1256';
	    #$post['password'] = '123456';
	    #$post['name'] = 'Lakshya';
	    $accountData['sms_auth_key'] = '371145A86jrLni1TBJ6368d39aP1';
	    $request = array(
                //'flow_id' => $accountData['sms_flow_id'],
                //'template_id' =>'6218b6951d2b877327477656',
                //'sender' => 'PAOLDT',
                //'short_url' =>'1',
                //'mobiles' => '91'.$post['mobile'],
                //'name'  =>$post['name'],
                //'userid' => $user_display_id,
                'otp' => '123456'
            );
            
            $api_url = 'https://control.msg91.com/api/v5/otp?mobile=918619651646&template_id=6218b6951d2b877327477656';

            $header = array(
                'content-type: application/JSON',
                'authkey: '.$accountData['sms_auth_key']
            );
            
            $curl = curl_init();
            // URL
            curl_setopt($curl, CURLOPT_URL, $api_url);

            // Return Transfer
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            // SSL Verify Peer
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

            // SSL Verify Host
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

            // Timeout
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

            // HTTP Version
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            // Request Method
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

            // Request Body
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

            // Execute
            $output = curl_exec($curl);

            // Close
            curl_close ($curl);
            
            echo $output;
            die;
	}
	
	
	public function accountVerify()
	{
	   
							
		            $api_url = 'https://api.instantpay.in/identity/verifyBankAccount';
		            
		           	

		           	$request = array(
		                
		               
    		                
	                        'payee' => array(
	                            
	                            'accountNumber' => "8745000100015076",
	                            'bankIfsc' =>"PUNB0874500"
	                       ),
	                     
	                       'externalRef' => '125125122',
	                       'consent'    =>'Y',
	                       'isCached'  => 0,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       
		                //)
		            );




		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: YWY3OTAzYzNlM2ExZTJlOTiCwv/jys9zRoS1vFYAByc=',
		                'X-Ipay-Client-Secret: e14baa3fd2d6a8a7edafa9ac7afa16dfb47bec0833b9962d7cfab47c8521dba7',
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',
		                
		                'content-type: application/json'
		                
		            );
		            
		           /* echo json_encode($header);
		            echo '<br />';
		           */ 
		            $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $api_url);

		            // Return Transfer
		            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		            // SSL Verify Peer
		            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

		            // SSL Verify Host
		            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		            // Timeout
		            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		            // HTTP Version
		            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		            // Request Method
		            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		            // Request Body
		            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

		            // Execute
		           $output = curl_exec($curl);

		            // Close
		            curl_close ($curl);
		           echo $output;
		            die;
		            
		            
			//ERROR RESPONSE
			/*{"statuscode":"ERR","actcode":null,"status":"Invalid Aadhaar Id #1","data":null,"timestamp":"2022-05-18 13:33:16","ipay_uuid":"h0689653ab3e-ca3e-45b9-8057-3ae9d7c47766","orderid":"1220518133314KDNBW","environment":"LIVE"}*/
			//SUCCESS RESPONSE
			/*{"statuscode":"TXN","actcode":null,"status":"Transaction Successful","data":{"externalRef":"IMPS1","poolReferenceId":"1221103121245UGCPQ","txnValue":"10.00","txnReferenceId":"230712773385","pool":{"account":"8292777339","openingBal":"531.89","mode":"DR","amount":"12.36","closingBal":"519.53"},"payer":{"account":"8292777339","name":"MORNING DIGITAL PRIVATE LIMITED"},"payee":{"account":"8745000100015076","name":"LAKSHYA GUJARATI S\/O"}},"timestamp":"2022-11-03 12:12:48","ipay_uuid":"h06897a786c7-60e5-4765-9966-8799534ec875","orderid":"1221103121245UGCPQ","environment":"LIVE"}*/
	}
	
	
	        //fingpay api 
	        //merchant onboard api
	        
	        function merchantOnboarding(){
	            
	            
	            $str = '{"status":true,"message":"Request Completed","data":{"terminalId":"-","requestTransactionTime":"25/08/2023 10:53:03","transactionAmount":0.0,"transactionStatus":"successful","balanceAmount":20648.14,"strMiniStatementBalance":null,"bankRRN":"323710881251","transactionType":"MS","fpTransactionId":"MSBT5386579250823105303641I","merchantTxnId":"FIMS1692940983","errorCode":null,"errorMessage":null,"merchantTransactionId":null,"bankAccountNumber":null,"ifscCode":null,"bcName":null,"transactionTime":null,"agentId":0,"issuerBank":null,"customerAadhaarNumber":null,"customerName":null,"stan":null,"rrn":null,"uidaiAuthCode":null,"bcLocation":null,"demandSheetId":null,"mobileNumber":null,"urnId":null,"miniStatementStructureModel":[{"date":"24/08","txnType":"Dr","amount":"100.0","narration":" 46831388         "},{"date":"24/08","txnType":"Dr","amount":"95.0","narration":" 46025330         "},{"date":"24/08","txnType":"Dr","amount":"120.0","narration":" 44894584         "},{"date":"24/08","txnType":"Dr","amount":"62.0","narration":" 26163236         "},{"date":"24/08","txnType":"Dr","amount":"28.0","narration":" 23222673         "},{"date":"23/08","txnType":"Dr","amount":"180.5","narration":" 40002227         "},{"date":"23/08","txnType":"Dr","amount":"1.0","narration":" 28627512         "},{"date":"23/08","txnType":"Dr","amount":"20.0","narration":" 23596536         "},{"date":"23/08","txnType":"Dr","amount":"28.0","narration":" 23405597         "}],"miniOffusStatementStructureModel":null,"miniOffusFlag":false,"transactionRemark":null,"bankName":null,"prospectNumber":null,"internalReferenceNumber":null,"biTxnType":null,"subVillageName":null,"virtualId":null,"userProfileResponseModel":null,"hindiErrorMessage":null,"loanAccNo":null,"responseCode":"00","fpkAgentId":null,"additionalData":null},"statusCode":10000}';
	            
	            $data = json_decode($str,true);
	            echo "<pre>";
	            print_r($data['data']['miniStatementStructureModel']);
	            die;
	            
	            $account_id = $this->User->get_domain_account();
                $accountData = $this->User->get_account_data($account_id);
	            
	            
	            $api_url = 'https://fingpayap.tapits.in/fpaepsweb/api/onboarding/merchant/php/creation/v2';
	            
	            
	            // $api_url = AEPS_ONBOARD_API_URL;

        $postdata = array 
        (
            "username"=>'payold',
            "password"=>'1234d',
            "latitude"=>'22.44543',
            "longitude"=>'77.434',
            "supermerchantId"=>'1244',    
            "MerchantModelV1"=>array(array
            (
                "merchantLoginId"=>'MPCNR703985', 
                "merchantLoginPin"=>'1234',
                "merchantName"=>'Lakshya Gujrati',
                "merchantAddressV1"=>array
                (
                    "merchantAddress"=>'Vaishali Nagar Jaipur',
                    "merchantState"=>'Rajasthan'
                ),        
                "merchantPhoneNumber"=>'8619651646',
                "companyLegalName"=>'Codunite',
                "companyMarketingName"=>"",
                "kyc"=>array
                (
                    "userPan"=>'DJBPG3725F',
                    "aadhaarNumber"=>'496231127006',
                    "gstInNumber"=>"",
                    "companyOrShopPan"=>""
                ),
                "settlement"=>array
                (
                        "companyBankAccountNumber"=>"",
                        "bankIfscCode"=>"",
                        "companyBankName"=>"",
                        "bankBranchName"=>"",
                        "bankAccountName"=>""
                ),
                "emailId"=>'lakshyagujrati7@gmail.com',
                "shopAndPanImage"=>"",        
                "cancellationCheckImages"=>"",        
                "ekycDocuments"=>"",
                "merchantPinCode"=>'302021',
                "tan"=>"",
                "merchantCityName"=>'Jaipur',
                "merchantDistrictName"=>'Jaipur',
            ))
        );

        // Generate JSON
        $json = json_encode($postdata);

        // Generate Session Key
        $key = '';
        $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        foreach ($mt_rand as $chr)
        {             $key .= chr($chr);         }

        // Read Public Key
        $pub_key_string = $accountData['aeps_certificate'];
        
        // Encrypt using Public Key
        openssl_public_encrypt($key, $crypttext, $pub_key_string);

        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'trnTimestamp: ' . date('d/m/Y H:i:s'),
            'hash: ' . base64_encode(hash('sha256', $json, true)),
            'eskey: ' . base64_encode($crypttext)
        );

        // Initialization Vector
        //$iv =   '06f2f04cc530364f';

        // Encrypt using AES-128
        $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA);

        // Create Body
        $request = base64_encode($ciphertext_raw);

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, $api_url);

        // Return Transfer
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // SSL Verify Peer
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        // SSL Verify Host
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        // Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        // HTTP Version
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        // Request Method
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        // Set Options - Close

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);
        
        echo $output;
        die;
	        }
	        
	        
	        
	        function sendFingPayOtp()
	        
	        {
	            
	            
	            $accountData['aeps_supermerchant_id'] = '1244';
	            $member_code = 'MPCNR703985';
	            $post['mobile']='8619651646';
	            $post['aadhar_no'] = '496231127006';
	            $post['pancard_no'] = 'DJBPG3725F';
	             $lat = '22.44543';
                $lng = '77.434';
                
            $otpPostData = array 
        (   
             "superMerchantId" =>$accountData['aeps_supermerchant_id'],
             "merchantLoginId" =>$member_code,
             "transactionType" =>"EKY",
             "mobileNumber" => $post['mobile'],
             "aadharNumber" =>$post['aadhar_no'],
             "panNumber" =>$post['pancard_no'],
             "matmSerialNumber"=> "",
             "latitude" =>$lat,
             "longitude"=> $lng
            
        );



          log_message('debug', 'Fingpay Send Otp AEPS api API Call');
        
        $api_url = 'https://fpekyc.tapits.in/fpekyc/api/ekyc/merchant/php/sendotp';
        
        log_message('debug', 'Fingpay Send Otp AEPS api API Url - '.$api_url);


        // Generate JSON
        $json = json_encode($otpPostData);
        
        log_message('debug', 'Fingpay Send Otp api post data - '.$json);
        // Generate Session Key
        $key = '';
        $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        foreach ($mt_rand as $chr)
        {             $key .= chr($chr);         }

        // Read Public Key
        $pub_key_string = file_get_contents('fingpay_public_production.txt');;
        
       
        // Encrypt using Public Key
        openssl_public_encrypt($key, $crypttext, $pub_key_string);

        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'trnTimestamp: ' . date('d/m/Y H:i:s'),
            'hash: ' . base64_encode(hash('sha256', $json, true)),
            'eskey: ' . base64_encode($crypttext),
            'deviceIMEI:352801082418919'
        );

        // Initialization Vector
        $iv =   '06f2f04cc530364f';


        // Encrypt using AES-128
        $ciphertext_raw = openssl_encrypt($json, 'AES-128-CBC', $key, $options = OPENSSL_RAW_DATA, $iv);

        // Create Body
        $request = base64_encode($ciphertext_raw);
        
        log_message('debug', 'Fingpay Send Otp api encrypt data - '.$request);
        
        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, $api_url);

        // Return Transfer
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // SSL Verify Peer
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        // SSL Verify Host
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        // Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        // HTTP Version
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        // Request Method
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        // Set Options - Close

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);
        
        
        log_message('debug', 'Fingpay Semd Otp api response - '.json_encode($output));
        
        echo $output;
	}
	
	public function yesbankUatUpi()
    {
        
        $transaction_id = time().rand(1111,9999);
		
		$enckey = '0eecc43f46ac1db51c40607cb355b22c';
		//Status Check API
		/*$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: D8cI6vS0yQ0cQ6eG5xG4rU3eE1vX7uT1sY0dE4kO3dB8xX0pC3',
		    'X-IBM-Client-ID: ccfcbdd3-8762-4329-9975-39acf5c8df50'
		];
		
		echo json_encode($header);
		echo '<br />';
		
		echo $requestStr = 'YES0000000065149|16989251788350||||||||||||NA|NA';
		$encryptData = $this->yesEncryptValue($requestStr,$enckey);
		
		
		echo '<br />';
		
		echo $data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000000065149"}';
		
		echo '<br />';
		
		echo $api_url = 'https://uatskyway.yesbank.in/app/uat/upi/meTransStatusQuery';
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 

        echo $response = curl_exec($ch);

 

        if(curl_errno($ch)){

            $error_no = curl_errno($ch);

            echo $error = curl_error($ch);

            
        }
        
        curl_close ($ch);
        
        echo '<br />';
        
        echo $this->yesDecryptValue($response,$enckey);
        
        echo'<br/>';
        
        die;*/
        
        /*7F2DCD27955FF66841DAD08655099D4F4FCB78C1D4555DAC138DA8F8141EF49EE6F4E30C44ED3B855CF845723FE48BDC5A76C5B7E7FA473D11710FB2C2E2C0D197136D6C8EEA1EFED663832612EC260CBC61CCCE5F98F99358420EB2D8874A4ED7E4A93392BF7193204412D496F83C1F25756F55611937D523B84A13586162962A99488BE64B6AAFC633D78892A5FDB5CD76411E9FFF3B771B9739AEE402247C63A8A75D9B0D8593EBE906070E02BA02AA80EC63AF476FD364891B837B92B91CC672FCF0B028AF09A28AF75A3ED0EBFF71384CF894AE0595AA0B793814642BEFE7164EC3BF006DB5E6471B887882E19F21D4490FDEB40901F2298D2E77981446F7BAE49085818B56544EA59BD4919D0BD0539CEAD6AD17D409A0410404DE8670*/
        /*After Decrypt*/
        /*3595750935|lksbhji|10.0|2023:09:29 15:46:14|FAILED|Transaction fail|U67|NA|7208865023@yesb|YESB067DA1B8EE06618FE06400144FFB2B9|NA|327210764661|XXXXXX4104|YESB0000419|MAHADEV NAVGIRE|payol@yesb|YESB0000007|XXXXXX0585|NA|Payol Digital|NA|U67|PAY TO FSS|SAVINGS|NA|NA|NA|NA|NA|NA*/
		
		
		//Pay API
		/*$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: D8cI6vS0yQ0cQ6eG5xG4rU3eE1vX7uT1sY0dE4kO3dB8xX0pC3',
		    'X-IBM-Client-ID: ccfcbdd3-8762-4329-9975-39acf5c8df50'
		];
		
		echo json_encode($header);
		echo '<br />';
		
		#echo $requestStr = 'YES0000000065149|Test1270707775|test|10|INR|P2P|Pay|1520||||||aamir@yesb|||||UPI| ABC||||||VPA|||||||||NA|NA';
		echo $requestStr = 'YES0000000065149|PHIC0991212123123|test|10.00|INR|P2P|Pay|1520||||||aamir@yesb|||||UPI|Pushpender||||||VPA|||||||||NA|NA';
		$encryptData = $this->yesEncryptValue($requestStr,$enckey);
		
		
		echo '<br />';
		
		echo $data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000000065149"}';
		
		echo '<br />';
		
		echo $api_url = 'https://uatskyway.yesbank.in/app/uat/upi/mePayServerReqImps';
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 

        echo $response = curl_exec($ch);

 

        if(curl_errno($ch)){

            $error_no = curl_errno($ch);

            echo $error = curl_error($ch);

            
        }
        
        curl_close ($ch);
        
        echo '<br />';
        
        echo $this->yesDecryptValue($response,$enckey);
        
        die;*/
        
        //Check VPA API
		/*$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: D8cI6vS0yQ0cQ6eG5xG4rU3eE1vX7uT1sY0dE4kO3dB8xX0pC3',
		    'X-IBM-Client-ID: ccfcbdd3-8762-4329-9975-39acf5c8df50'
		];
		
		echo json_encode($header);
		echo '<br />';
		
		echo $requestStr = 'YES0000000065149|YESD04F53227F184F6C88741779F8C|7208865023@yesb|T|com.msg.app|0.0 ,0.0 |Mumbai|172.16.50.65|MOB|5200000200010004000639292929292|Android7.0|351898082074677|89914902900059967808|4e9389eadeea5b7c|02:00:00:00:00:00|02:00:00:00:00:00|||||||||NA|NA';
		$encryptData = $this->yesEncryptValue($requestStr,$enckey);
		
		
		echo '<br />';
		
		echo $data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000000065149"}';
		
		echo '<br />';
		
		echo $api_url = 'https://uatskyway.yesbank.in/app/uat/upi/CheckVirtualAddress';
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 

        echo $response = curl_exec($ch);

 

        if(curl_errno($ch)){

            $error_no = curl_errno($ch);

            echo $error = curl_error($ch);

            
        }
        
        curl_close ($ch);
        
        echo '<br />';
        
        echo $this->yesDecryptValue($response,$enckey);
        
        die;*/
        
        
        //Refund API
		$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: D8cI6vS0yQ0cQ6eG5xG4rU3eE1vX7uT1sY0dE4kO3dB8xX0pC3',
		    'X-IBM-Client-ID: ccfcbdd3-8762-4329-9975-39acf5c8df50'
		];
		
		echo json_encode($header);
		echo '<br />';
		
		echo $requestStr = 'YES0000000065149|81211123152362312|||327210764662|Refund|10.00|INR|P2P|PAY|||||||||NA|NA';
		$encryptData = $this->yesEncryptValue($requestStr,$enckey);
		
		
		echo '<br />';
		
		echo $data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000000065149"}';
		
		echo '<br />';
		
		echo $api_url = 'https://uatskyway.yesbank.in/app/uat/upi/meRefundServerReq';
		#echo $api_url = 'https://uatskyway.yesbank.in/app/uat/upi/meRefundServerReq/v2';
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 

        echo $response = curl_exec($ch);

 

        if(curl_errno($ch)){

            $error_no = curl_errno($ch);

            echo $error = curl_error($ch);

            
        }
        
        curl_close ($ch);
        
        echo '<br />';
        
        echo $this->yesDecryptValue($response,$enckey);
        
        die;
    }
    
    public function yesbankLiveUpi()
    {
        
        $transaction_id = time().rand(1111,9999);
		
		$enckey = '7153f272dbdc71b459c6b49551988767';
		//Status Check API
		$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: U0fW7iH6eH5lH1qS5wX0cQ2tC4hB8oH3lE8mV1pR7wM2dL1hF6',
		    'X-IBM-Client-ID: 0d38b91a-2b06-491c-8ef1-6da43d24dc89'
		];
		
		echo json_encode($header);
		echo '<br />';
		
		echo $requestStr = 'YES0000011547194|1723257127318919||||||||||||NA|NA';
		$encryptData = $this->yesEncryptValue($requestStr,$enckey);
		
		
		echo '<br />';
		
		echo $data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000011547194"}';
		
		echo '<br />';
		
		echo $api_url = 'https://skyway.yesbank.in:443/app/live/upi/meTransStatusQuery';
        echo '<br /><br/>';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 

        echo "Encrypt - ".$response = curl_exec($ch);

 

        if(curl_errno($ch)){

            $error_no = curl_errno($ch);

            echo $error = curl_error($ch);

            
        }
        
        curl_close ($ch);
        
        echo '<br /><br />';
        
        echo "Decrypt - ".$this->yesDecryptValue($response,$enckey);
        
        echo'<br/>';
        
        die;
        
        /*7F2DCD27955FF66841DAD08655099D4F4FCB78C1D4555DAC138DA8F8141EF49EE6F4E30C44ED3B855CF845723FE48BDC5A76C5B7E7FA473D11710FB2C2E2C0D197136D6C8EEA1EFED663832612EC260CBC61CCCE5F98F99358420EB2D8874A4ED7E4A93392BF7193204412D496F83C1F25756F55611937D523B84A13586162962A99488BE64B6AAFC633D78892A5FDB5CD76411E9FFF3B771B9739AEE402247C63A8A75D9B0D8593EBE906070E02BA02AA80EC63AF476FD364891B837B92B91CC672FCF0B028AF09A28AF75A3ED0EBFF71384CF894AE0595AA0B793814642BEFE7164EC3BF006DB5E6471B887882E19F21D4490FDEB40901F2298D2E77981446F7BAE49085818B56544EA59BD4919D0BD0539CEAD6AD17D409A0410404DE8670*/
        /*After Decrypt*/
        /*13958572007|671617|10.0|2023:11:28 12:46:09|SUCCESS|Transaction success|00|769316|8104758957@ybl|YBL9b03ba0951474548b574b09b1bdf04fe|NA|333292502159|XXXXXX6776|ICIC0000235|SONU JANGID|payol@yesbank|YESB0000253|XXXXXX0220|NA|PAYOL DIGITAL TECHNOLOGIES PVT LTD|NA|NA|PAY TO FSS|SAVINGS|NA|NA|NA|NA|NA|NA*/
		
		
		//Pay API
		/*$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: U0fW7iH6eH5lH1qS5wX0cQ2tC4hB8oH3lE8mV1pR7wM2dL1hF6',
		    'X-IBM-Client-ID: 0d38b91a-2b06-491c-8ef1-6da43d24dc89'
		];
		
		echo json_encode($header);
		echo '<br />';
		
		#echo $requestStr = 'YES0000000065149|Test1270707775|test|10|INR|P2P|Pay|1520||||||aamir@yesb|||||UPI| ABC||||||VPA|||||||||NA|NA';
		echo $requestStr = 'YES0000011547194|PHIC0991212123321|test|100.00|INR|P2P|Pay|1520||||||8104758957@ybl|||||UPI|Pushpender||||||VPA|||||||||NA|NA';
		$encryptData = $this->yesEncryptValue($requestStr,$enckey);
		
		
		echo '<br />';
		
		echo $data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000011547194"}';
		
		echo '<br />';
		
		echo $api_url = 'https://skyway.yesbank.in:443/app/live/upi/mePayServerReqImp';
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 

        echo $response = curl_exec($ch);

 

        if(curl_errno($ch)){

            $error_no = curl_errno($ch);

            echo $error = curl_error($ch);

            
        }
        
        curl_close ($ch);
        
        echo '<br />';
        
        echo $this->yesDecryptValue($response,$enckey);
        
        die;*/
        
        /*13959415188|PHIC0991212123123|10.00|2023:11:28 14:09:32|S|Transaction success|00|765675|payol@yesbank|YESB0AD73CF3384D1470E06400144FF80A1|333237513358|XXXXXX0220|YESB0000253|PAYOL DIGITAL TECHNOLOGIES PVT LTD|NA|NA|UPI|8104758957@ybl|ICIC0000235|XXXXXX6776|NA|NA|NA|NA|NA|NA|NA|NA|NA|NA|NA|NA*/
        
        //Check VPA API
		$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: U0fW7iH6eH5lH1qS5wX0cQ2tC4hB8oH3lE8mV1pR7wM2dL1hF6',
		    'X-IBM-Client-ID: 0d38b91a-2b06-491c-8ef1-6da43d24dc89'
		];
		
		echo json_encode($header);
		echo '<br />';
		
		echo $requestStr = 'YES0000011547194|YESD04F53227F184F6C88741779F8C|8104758957@ybl|T|com.msg.app|0.0 ,0.0 |Mumbai|172.16.50.65|MOB|5200000200010004000639292929292|Android7.0|351898082074677|89914902900059967808|4e9389eadeea5b7c|02:00:00:00:00:00|02:00:00:00:00:00|||||||||NA|NA';
		$encryptData = $this->yesEncryptValue($requestStr,$enckey);
		
		
		echo '<br />';
		
		echo $data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000011547194"}';
		
		echo '<br />';
		
		echo $api_url = 'https://skyway.yesbank.in:443/app/live/upi/checkVirtualAddressME';
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 

        echo $response = curl_exec($ch);

 

        if(curl_errno($ch)){

            $error_no = curl_errno($ch);

            echo $error = curl_error($ch);

            
        }
        
        curl_close ($ch);
        
        echo '<br />';
        
        echo $this->yesDecryptValue($response,$enckey);
        
        die;
        
        /*13960502075|8104758957@ybl|SONU JANGID|VE|Virtual Address exist for Transaction|NA|NA|ICIC0000235|NA|NA|NA|NA|NA|NA|NA*/
        /*13960497842|8104758957@yesb|NA|F|NA|NA|U17|NA|NA|NA|NA|NA|NA|NA|NA*/
        
        
        //Refund API
		$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: D8cI6vS0yQ0cQ6eG5xG4rU3eE1vX7uT1sY0dE4kO3dB8xX0pC3',
		    'X-IBM-Client-ID: ccfcbdd3-8762-4329-9975-39acf5c8df50'
		];
		
		echo json_encode($header);
		echo '<br />';
		
		echo $requestStr = 'YES0000000065149|81211123152362312|||327210764662|Refund|10.00|INR|P2P|PAY|||||||||NA|NA';
		$encryptData = $this->yesEncryptValue($requestStr,$enckey);
		
		
		echo '<br />';
		
		echo $data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000000065149"}';
		
		echo '<br />';
		
		echo $api_url = 'https://uatskyway.yesbank.in/app/uat/upi/meRefundServerReq';
		#echo $api_url = 'https://uatskyway.yesbank.in/app/uat/upi/meRefundServerReq/v2';
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 

        echo $response = curl_exec($ch);

 

        if(curl_errno($ch)){

            $error_no = curl_errno($ch);

            echo $error = curl_error($ch);

            
        }
        
        curl_close ($ch);
        
        echo '<br />';
        
        echo $this->yesDecryptValue($response,$enckey);
        
        die;
    }
    
    public function cosmosbankUatUpi()
    {
        
        
        $transaction_id = rand(1111,9999).rand(1111,9999);
		
		// Generate Static/Dynamic QR Intent
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
		$req = [
            'source' => 'PAYOL2517',
            'channel' => 'api',
            'extTransactionId' => "PAYOLDG".$transaction_id,
            'sid' => 'PAYOL-2517',
            'terminalId' => 'PAYOL-2517',
            'amount' => '5.00',
            'type' => 'D',
            'remark' => 'Product Amount',
            'requestTime' => date('Y-m-d H:i:s'),
            'minAmount' => '5.00',
            'receipt' => '',
            'param1' => '',
            'param2' => '',
            'param3' => ''
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/dqr';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
        echo '<pre>';
        print_r($decodeResult);
        
        //prepare qr intant
        $qr = isset($decodeResult['qrString']) ? $decodeResult['qrString'] : '';
        $qr = str_replace('%3A',':',$qr);
        $qr = str_replace('%2F','/',$qr);
        $qr = str_replace('%3F','?',$qr);
        $qr = str_replace('%3D','=',$qr);
        $qr = str_replace('%26','&',$qr);
        $qr = str_replace('%40','@',$qr);
        echo $qr;
        die;*/
		
		// ERROR MESSAGE
		/*{"pendingCollectCount":0,"msgId":"1","msg":"EXP1","countOfAccounts":0}*/
		
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","sid":"PAYOL-2517","terminalId":"PAYOL-2517","channel":"api","amount":"5.00","minAmount":"5.00","remark":"Product Amount","extTransactionId":"PAYOLDG57385060","reciept":null,"type":"D","qrString":"upi%3A%2F%2Fpay%3Fver%3D01%26mode%3D15%26am%3D5.00%26mam%3D5.00%26cu%3DINR%26pa%3Dpayoldg.pay%40cosb%26pn%3DPAYOL+DIGITAL+TECHNOLOGIES+PRIVATE+LIMITED%26mc%3D6012%26tr%3DPAYOLDG57385060%26tn%3DProduct+Amount%26mid%3DPAYOL2517%26msid%3DPAYOL-2517%26mtid%3DPAYOL-2517","status":"SUCCESS","param1":"","param2":"","param3":"","errorMsg":"","checksum":"dc9cb2f2a40f5d9d374c5ad9ad0d76b2e7c53d9a92119225c53870e0b2b59484"}*/
		
		// Verify VPA API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
		$req = [
            'source' => 'PAYOL2517',
            'channel' => 'api',
            'extTransactionId' => "PAYOLDG".$transaction_id,
            'upiId' => '9730024610@cosb',
            'terminalId' => 'PAYOL-2517',
            'sid' => 'PAYOL-2517'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/cm/v2/verifyVPA';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG68821781","upiId":"9730024610@cosb","checksum":"ef18d0c3d4e982193d991f68960313a1c24e32f8daa8ccb8f691bf6dba21002a","status":"SUCCESS","txnType":"VALADD","sid":"PAYOL-2517","data":[{"customerName":"PINGALE SANJAY JAGANNATH","respCode":"0","respMessge":"SUCCESS"}],"transactionList":[]}*/
        
        // UPI TRANSFER API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
		$req = [
            'source' => 'PAYOL2517',
            'channel' => 'api',
            'extTransactionId' => "PAYOLDG".$transaction_id,
            'upiId' => '9730024610@cosb',
            'terminalId' => 'PAYOL-2517',
            'amount' => '55.00',
            'customerName' => 'PINGALE SANJAY JAGANNATH',
            'statusKYC' => 'Y',
            'sid' => 'PAYOL-2517'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/cm/v2/transfer';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
        
        // TRANSACTION STATUS API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
		$req = [
            'source' => 'PAYOL2517',
            'channel' => 'api',
            'terminalId' => 'PAYOL-2517',
            'extTransactionId' => "PAYOLDG75024918"
            
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/cm/v2/status';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
        // TRANSACTION REPORT API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
		$req = [
            'source' => 'PAYOL2517',
            'channel' => 'api',
            'terminalId' => 'PAYOL-2517',
            'startDate' => '2023-10-01 00:00:00',
            'endDate' => '2023-10-05 19:20:00',
            'pageNo' => '0',
            'pageSize' => '10'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/cm/v2/report';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
        
        // QR STATUS API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
        $req = [
            'source' => 'PAYOL2517',
            'channel' => 'api',
            'terminalId' => 'PAYOL-2517',
            'extTransactionId' => "PAYOLDG94373076"
            
        ];
		
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
       
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/qrStatus';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
       // QR STATUS RRN API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
        $req = [
            'source' => 'PAYOL2517',
            'channel' => 'api',
            'terminalId' => 'PAYOL-2517',
            'extTransactionId' => "327811070008"
            
        ];
		
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
       
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/qrStatusRRN';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        // QR REPORT API
		$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
		$req = [
		    'source' => 'PAYOL2517',
            'channel' => 'api',
            'terminalId' => 'PAYOL-2517',
            'startDate' => '2023-10-01 00:00:00',
            'endDate' => '2023-10-05 19:20:00',
            'pageNo' => '0',
            'pageSize' => '10'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        $req['sid'] = 'PAYOL-2517';
        
       
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/qrreport';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
		
    }
    
    public function cosmosbankLiveUpi()
    {
        
        
        $transaction_id = rand(1111,9999).rand(1111,9999);
		
		$cid = '6d02d0b56ba2e170d38ee13e4e56dca0';
		$checksumkey = 'ca52c60cc5478bc01d5efe89885cf9cd';
		$key = 'a82623486a0299efa1b48be02614218f';
		
		// Generate Static/Dynamic QR Intent
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:'.$cid
        );
		$req = [
            'source' => 'PAYOL1760',
            'channel' => 'api',
            'extTransactionId' => "PAYOLDG".$transaction_id,
            'sid' => 'GELAX-0942',
            'terminalId' => 'GELAX-0942',
            'amount' => '10.00',
            'type' => 'D',
            'remark' => 'Product Amount',
            'requestTime' => date('Y-m-d H:i:s'),
            'minAmount' => '10.00',
            'receipt' => '',
            'param1' => '',
            'param2' => '',
            'param3' => ''
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.$checksumkey;
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        $api_url = 'https://merchantprod.timepayonline.com/evok/qr/v1/dqr';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        
        
        
        $decodeResult = json_decode($decrypted_string,true);
        echo '<pre>';
        print_r($decodeResult);
        
        //prepare qr intant
        $qr = isset($decodeResult['qrString']) ? $decodeResult['qrString'] : '';
        $qr = str_replace('%3A',':',$qr);
        $qr = str_replace('%2F','/',$qr);
        $qr = str_replace('%3F','?',$qr);
        $qr = str_replace('%3D','=',$qr);
        $qr = str_replace('%26','&',$qr);
        $qr = str_replace('%40','@',$qr);
        echo $qr;
        die;*/
		
		// ERROR MESSAGE
		/*{"pendingCollectCount":0,"msgId":"1","msg":"EXP1","countOfAccounts":0}*/
		
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","sid":"PAYOL-2517","terminalId":"PAYOL-2517","channel":"api","amount":"5.00","minAmount":"5.00","remark":"Product Amount","extTransactionId":"PAYOLDG57385060","reciept":null,"type":"D","qrString":"upi%3A%2F%2Fpay%3Fver%3D01%26mode%3D15%26am%3D5.00%26mam%3D5.00%26cu%3DINR%26pa%3Dpayoldg.pay%40cosb%26pn%3DPAYOL+DIGITAL+TECHNOLOGIES+PRIVATE+LIMITED%26mc%3D6012%26tr%3DPAYOLDG57385060%26tn%3DProduct+Amount%26mid%3DPAYOL2517%26msid%3DPAYOL-2517%26mtid%3DPAYOL-2517","status":"SUCCESS","param1":"","param2":"","param3":"","errorMsg":"","checksum":"dc9cb2f2a40f5d9d374c5ad9ad0d76b2e7c53d9a92119225c53870e0b2b59484"}*/
		
		// Verify VPA API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:'.$cid
        );
		$req = [
            'source' => 'PAYOL1760',
            'channel' => 'api',
            'extTransactionId' => "PAYOLDG".$transaction_id,
            'upiId' => '8104758957@ybl',
            'terminalId' => 'PAYOL-1760',
            'sid' => 'STYLE-5205'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.$checksumkey;
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantprod.timepayonline.com/evok/cm/v2/verifyVPA';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG68821781","upiId":"9730024610@cosb","checksum":"ef18d0c3d4e982193d991f68960313a1c24e32f8daa8ccb8f691bf6dba21002a","status":"SUCCESS","txnType":"VALADD","sid":"PAYOL-2517","data":[{"customerName":"PINGALE SANJAY JAGANNATH","respCode":"0","respMessge":"SUCCESS"}],"transactionList":[]}*/
        /*{"source":"PAYOL1760","channel":"api","terminalId":"PAYOL-1760","extTransactionId":"PAYOLDG87039491","upiId":"8104758957@ybl","checksum":"242c17f9b86a2724aa671e7daa2a6233f488bf07030f974841cb8f0c3be5519f","status":"SUCCESS","txnType":"VALADD","sid":"STYLE-5205","data":[{"customerName":"SONU JANGID","respCode":"0","respMessge":"SUCCESS"}],"transactionList":[]}*/
        
        // UPI REQUEST API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:'.$cid
        );
		$req = [
            'source' => 'PAYOL1760',
            'channel' => 'api',
            'extTransactionId' => "PAYOLDG".$transaction_id,
            'upiId' => '8292777339@ybl',
            'terminalId' => 'PAYOL-1760',
            'amount' => '10.00',
            'customerName' => 'PINGALE SANJAY JAGANNATH',
            'statusKYC' => 'Y',
            'sid' => 'STYLE-5205'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.$checksumkey;
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantprod.timepayonline.com/evok/cm/v2/transfer';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        /*{"source":"PAYOL1760","channel":"api","terminalId":"PAYOL-1760","extTransactionId":"PAYOLDG84256954","upiId":"8104758957@ybl","amount":"10.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"bb48a344607f913956114e7b00d3b6506218ba216a9e8a3e3784976aea4d7569","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB4C7ECDCBA5A54A3395E8417765612858","txnTime":"Thu Dec 07 13:20:40 IST 2023"}]}*/
        /*{"source":"PAYOL1760","channel":"api","terminalId":"PAYOL-1760","extTransactionId":"PAYOLDG41393661","upiId":"8292777339@ybl","amount":"10.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"bb48a344607f913956114e7b00d3b6506218ba216a9e8a3e3784976aea4d7569","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB0E1078D1D4894039850964A9EDCF77E5","txnTime":"Thu Dec 07 13:24:06 IST 2023"}]}*/
        /*{"source":"PAYOL1760","channel":"api","terminalId":"PAYOL-1760","extTransactionId":"PAYOLDG96962308","upiId":"8292777339@ybl","amount":"10.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"bb48a344607f913956114e7b00d3b6506218ba216a9e8a3e3784976aea4d7569","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB09EBD1262F9441709A85A0BF0FA6B2CA","txnTime":"Thu Dec 07 13:33:41 IST 2023"}]}*/
        
        // TRANSACTION STATUS API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:'.$cid
        );
		$req = [
            'source' => 'PAYOL1760',
            'channel' => 'api',
            'terminalId' => 'PAYOL-1760',
            'extTransactionId' => "PAYOLDGDF0DB74871EA15CF2X0324"
            
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.$checksumkey;
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantprod.timepayonline.com/evok/cm/v2/status';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL1760","channel":"api","terminalId":"PAYOL-1760","extTransactionId":"PAYOLDG41393661","checksum":"2059a94c203d8a0be7777519ddb119e9280e3040ae8d5eafa0e8c3221cb27667","status":"SUCCESS","txnType":"TXNSTATUS","data":[{"respCode":"U30","respMessge":"FAILURE","upiTxnId":"COBF65DAD15A2034B59951FAE1D84C15A69","txnTime":"2023-12-07 13:24:20.0","amount":"10.00","upiId":"8292777339@ybl","custRefNo":"334113245273"}],"transactionList":[]}*/
        
        // TRANSACTION REPORT API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
		$req = [
            'source' => 'PAYOL2517',
            'channel' => 'api',
            'terminalId' => 'PAYOL-2517',
            'startDate' => '2023-10-01 00:00:00',
            'endDate' => '2023-10-05 19:20:00',
            'pageNo' => '0',
            'pageSize' => '10'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/cm/v2/report';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
        
        // QR STATUS API
		$header = array
        (
            'Content-type: text/plain',
            'cid:'.$cid
        );
        $req = [
            'source' => 'PAYOL1760',
            'channel' => 'api',
            'terminalId' => 'PAYOL-1760',
            'extTransactionId' => "PAYOLDGGR96D882C5D68F771"
            
        ];
		
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.$checksumkey;
        
        $req['checksum']=hash('sha256',$checksum_string);
        
       
        
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		echo $api_url = 'https://merchantprod.timepayonline.com/evok/qr/v1/qrStatus';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;
        
       // QR STATUS RRN API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
        $req = [
            'source' => 'PAYOL2517',
            'channel' => 'api',
            'terminalId' => 'PAYOL-2517',
            'extTransactionId' => "327811070008"
            
        ];
		
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
       
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/qrStatusRRN';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        // QR REPORT API
		$header = array
        (
            'Content-type: text/plain',
            'cid:f26671bb3e3a9d9eaa553f81922d439c'
        );
		$req = [
		    'source' => 'PAYOL2517',
            'channel' => 'api',
            'terminalId' => 'PAYOL-2517',
            'startDate' => '2023-10-01 00:00:00',
            'endDate' => '2023-10-05 19:20:00',
            'pageNo' => '0',
            'pageSize' => '10'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'e325ded080b1d62f07c9e9fd406e180d';
        
        $req['checksum']=hash('sha256',$checksum_string);
        $req['sid'] = 'PAYOL-2517';
        
       
        $key= '4bd754946cdc6e3803fb4ea271b100d3';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/qrreport';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
		
    }
    
    public function morningpayCosmosbankUatUpi()
    {
        
        
        $transaction_id = rand(1111,9999).rand(1111,9999);
		
		// Generate Static/Dynamic QR Intent
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:92584d8329b8b1b55dc0484151a9262a'
        );
		$req = [
            'source' => 'MORNI4741',
            'channel' => 'api',
            'extTransactionId' => "MORNI".$transaction_id,
            'sid' => 'MORNI-4741',
            'terminalId' => 'MORNI-4741',
            'amount' => '5.00',
            'type' => 'D',
            'remark' => 'Product Amount',
            'requestTime' => date('Y-m-d H:i:s'),
            'minAmount' => '5.00',
            'receipt' => '',
            'param1' => '',
            'param2' => '',
            'param3' => ''
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'d12619e60215d326a56d8675c2d9cb55';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        $key= 'e0764fea7b6f8a7897e54e4a6e2127e7';
        $key=substr((hash('sha256',$key,true)),0,16);
        
        
        echo json_encode($req);
        echo '<br />';
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/dqr';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		$result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
        echo '<pre>';
        print_r($decodeResult);
        
        //prepare qr intant
        $qr = isset($decodeResult['qrString']) ? $decodeResult['qrString'] : '';
        $qr = str_replace('%3A',':',$qr);
        $qr = str_replace('%2F','/',$qr);
        $qr = str_replace('%3F','?',$qr);
        $qr = str_replace('%3D','=',$qr);
        $qr = str_replace('%26','&',$qr);
        $qr = str_replace('%40','@',$qr);
        echo $qr;
        die;*/
		
		// ERROR MESSAGE
		/*{"pendingCollectCount":0,"msgId":"1","msg":"EXP1","countOfAccounts":0}*/
		
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","sid":"PAYOL-2517","terminalId":"PAYOL-2517","channel":"api","amount":"5.00","minAmount":"5.00","remark":"Product Amount","extTransactionId":"PAYOLDG57385060","reciept":null,"type":"D","qrString":"upi%3A%2F%2Fpay%3Fver%3D01%26mode%3D15%26am%3D5.00%26mam%3D5.00%26cu%3DINR%26pa%3Dpayoldg.pay%40cosb%26pn%3DPAYOL+DIGITAL+TECHNOLOGIES+PRIVATE+LIMITED%26mc%3D6012%26tr%3DPAYOLDG57385060%26tn%3DProduct+Amount%26mid%3DPAYOL2517%26msid%3DPAYOL-2517%26mtid%3DPAYOL-2517","status":"SUCCESS","param1":"","param2":"","param3":"","errorMsg":"","checksum":"dc9cb2f2a40f5d9d374c5ad9ad0d76b2e7c53d9a92119225c53870e0b2b59484"}*/
		
		// Verify VPA API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:92584d8329b8b1b55dc0484151a9262a'
        );
		$req = [
            'source' => 'MORNI4741',
            'channel' => 'api',
            'extTransactionId' => "MORNI".$transaction_id,
            'upiId' => '9730024610@cosb',
            'terminalId' => 'MORNI-4741',
            'sid' => 'MORNI-4741'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'d12619e60215d326a56d8675c2d9cb55';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        $key= 'e0764fea7b6f8a7897e54e4a6e2127e7';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/cm/v2/verifyVPA';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG68821781","upiId":"9730024610@cosb","checksum":"ef18d0c3d4e982193d991f68960313a1c24e32f8daa8ccb8f691bf6dba21002a","status":"SUCCESS","txnType":"VALADD","sid":"PAYOL-2517","data":[{"customerName":"PINGALE SANJAY JAGANNATH","respCode":"0","respMessge":"SUCCESS"}],"transactionList":[]}*/
        
        // UPI TRANSFER API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:92584d8329b8b1b55dc0484151a9262a'
        );
		$req = [
            'source' => 'MORNI4741',
            'channel' => 'api',
            'extTransactionId' => "MORNI".$transaction_id,
            'upiId' => '9730024610@cosb',
            'terminalId' => 'MORNI-4741',
            'amount' => '55.00',
            'customerName' => 'PINGALE SANJAY JAGANNATH',
            'statusKYC' => 'Y',
            'sid' => 'MORNI-4741'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'d12619e60215d326a56d8675c2d9cb55';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        $key= 'e0764fea7b6f8a7897e54e4a6e2127e7';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/cm/v2/transfer';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
        
        // TRANSACTION STATUS API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:92584d8329b8b1b55dc0484151a9262a'
        );
		$req = [
            'source' => 'MORNI4741',
            'channel' => 'api',
            'terminalId' => 'MORNI-4741',
            'extTransactionId' => "MORNI47988613"
            
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'d12619e60215d326a56d8675c2d9cb55';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        $key= 'e0764fea7b6f8a7897e54e4a6e2127e7';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/cm/v2/status';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
        // TRANSACTION REPORT API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:92584d8329b8b1b55dc0484151a9262a'
        );
		$req = [
            'source' => 'MORNI4741',
            'channel' => 'api',
            'terminalId' => 'MORNI-4741',
            'startDate' => '2024-03-12 00:00:00',
            'endDate' => '2024-03-12 19:20:00',
            'pageNo' => '0',
            'pageSize' => '10'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'d12619e60215d326a56d8675c2d9cb55';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
        
        $key= 'e0764fea7b6f8a7897e54e4a6e2127e7';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/cm/v2/report';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
        
        // QR STATUS API
		$header = array
        (
            'Content-type: text/plain',
            'cid:92584d8329b8b1b55dc0484151a9262a'
        );
        $req = [
            'source' => 'MORNI4741',
            'channel' => 'api',
            'terminalId' => 'MORNI-4741',
            'extTransactionId' => "MORNI93932124"
            
        ];
		
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'d12619e60215d326a56d8675c2d9cb55';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
       
        $key= 'e0764fea7b6f8a7897e54e4a6e2127e7';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/qrStatus';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;
        
       // QR STATUS RRN API
		/*$header = array
        (
            'Content-type: text/plain',
            'cid:92584d8329b8b1b55dc0484151a9262a'
        );
        $req = [
            'source' => 'MORNI4741',
            'channel' => 'api',
            'terminalId' => 'MORNI-4741',
            'extTransactionId' => "407218040012"
            
        ];
		
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'d12619e60215d326a56d8675c2d9cb55';
        
        $req['checksum']=hash('sha256',$checksum_string);
        
       
        $key= 'e0764fea7b6f8a7897e54e4a6e2127e7';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/qrStatusRRN';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;*/
        
        // QR REPORT API
		$header = array
        (
            'Content-type: text/plain',
            'cid:92584d8329b8b1b55dc0484151a9262a'
        );
		$req = [
		    'source' => 'MORNI4741',
            'channel' => 'api',
            'terminalId' => 'MORNI-4741',
            'startDate' => '2024-03-12 00:00:00',
            'endDate' => '2024-03-12 19:20:00',
            'pageNo' => '0',
            'pageSize' => '10'
        ];
        
        $checksum='';
        foreach ($req as $val){
            $checksum.=$val;
        }
        $checksum_string=$checksum.'d12619e60215d326a56d8675c2d9cb55';
        
        $req['checksum']=hash('sha256',$checksum_string);
        $req['sid'] = 'MORNI-4741';
        
       
        $key= 'e0764fea7b6f8a7897e54e4a6e2127e7';
        $key=substr((hash('sha256',$key,true)),0,16);
    
        $cipher='AES-128-ECB';
        $data=openssl_encrypt(
            json_encode($req),
            $cipher,
            $key
        );
        
        
		
        echo $api_url = 'https://merchantuat.timepayonline.com/evok/qr/v1/qrreport';
        echo '<br />';
        echo json_encode($req);
        echo '<br />';
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		echo '<br />';
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		// TO decrypt
        echo $decrypted_string = openssl_decrypt(
            $result,
            $cipher,
            $key
        );
        
        $decodeResult = json_decode($decrypted_string,true);
       
        die;
        
        //ERROR MESSAGE
        /*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG60628036","upiId":"9730024610@cosb","checksum":"25381dffbba4cce54f89eb38349ec1f98d0d7ed9abe6ffb0d6348d6e2cc65cfd","status":"FAILURE","txnType":"VALADD","sid":"PAYOL-2517","data":[{"respCode":"E02","respMessge":"Checksum Error/Invalid input Parameter"}],"transactionList":[]}*/
		//SUCCESS MESSAGE
		/*{"source":"PAYOL2517","channel":"api","terminalId":"PAYOL-2517","extTransactionId":"PAYOLDG94743781","upiId":"9730024610@cosb","amount":"100.00","customerName":"PINGALE SANJAY JAGANNATH","status":"SUCCESS","checksum":"aa9560acc536649c8beb658f04f7f23c186f36520cdc637605c4b3dd91821e6c","data":[{"respCode":"0","respMessge":"SUCCESS","upiTxnId":"COB2FCBA0CC845E4DC99A3D6CB427070F09","txnTime":"Thu Oct 05 11:54:01 IST 2023"}]}*/
        
		
    }
    
    public function flightApiUat()
    {
        
        
        
		
		// Token Generate API
		/*$header = array
        (
            'Content-type: application/json'
        );
		$req = [
            'LoginId' => 'payoldigital2023@gmail.com',
            'Password' => 'apiUser',
            'APIKey' => "836B230B-53E7-4AC1-B738-A33EC4DCC217"
        ];
        
        $data = json_encode($req);
        
        
        
        echo $api_url = 'https://api.fly24hrs.com/api/Login/Token';
        echo '<br />';
        echo $data;
        echo '<br />';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		die;*/
		
		/*{"ApiToken":null,"ApiStatus":0,"ApiErro":{"ErrorCode":"02","ErrorDesc":"Invalid Request"}}*/
		/*{"ApiToken":"93aa9bd7-7d43-4f4b-97df-d351a59610a6","ApiStatus":0,"ApiErro":{"ErrorCode":"0","ErrorDesc":"Successfull"}}*/
        
        // Air Result API
		/*$header = array
        (
            'Content-type: application/json'
        );
		$req = [
            'AgencyKey' => 'C35785B1-2B76-40BF-B7AB-B7893EE74020',
            'APIToken' => '844d1076-0477-4425-8c38-f04d88d1e153',
            'AdultCount' => 1,
            'ChildCount' => 1,
            'InfantCount' => 1,
            'PreferredAirlines' => null,
            'Routs' => array(
                0 => array(
                    'Source' => 'DEL',
                    'Destination' => 'DXB',
                    'TravelDate' => '2023-12-15T00:00:00'
                ),
                1 => array(
                    'Source' => 'DXB',
                    'Destination' => 'DEL',
                    'TravelDate' => '2023-12-17T00:00:00'
                )
            ),
            'TripType' => 1,
            'TypeOfClass' => 0
        ];
        
        $data = json_encode($req);
        
        
        
        echo $api_url = 'https://api.fly24hrs.com/api/Air/AirResults';
        echo '<br />';
        echo $data;
        echo '<br />';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		die;*/
		
		// Get Flight Detail API
		/*$header = array
        (
            'Content-type: application/json'
        );
		$req = [
            'AgencyKey' => 'C35785B1-2B76-40BF-B7AB-B7893EE74020',
            'APIToken' => '844d1076-0477-4425-8c38-f04d88d1e153',
            'BookingKey' => 'a0f29bac-fa35-41a1-b03b-4ff23b933342',
            'ContractId' => 40
        ];
        
        $data = json_encode($req);
        
        
        
        echo $api_url = 'https://api.fly24hrs.com/api/Air/Sell';
        echo '<br />';
        echo $data;
        echo '<br />';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		die;*/
		
		/*{"APIToken":"93aa9bd7-7d43-4f4b-97df-d351a59610a6","BookingKey":"2b7a11b2-b43e-40b8-9bf3-01867c78364a","IsFareChange":false,"IsTimeCharge":false,"ResponseStatus":1,"Error":null,"Sell":{"Contracts":[{"ContractId":0,"ContractType":"Onword","GDSPnr":null,"AirlinePnr":null,"AirlineCode":null,"Engine":3,"Lcc":false,"IsUKSpecial":false,"IsGoAirSpecialV2":false,"Refundable":true,"AirlineRemark":null,"FareType":21,"AirlineFare":{"ContractType":null,"Currency":"INR","BaseFare":3384.0,"YQTx":0.0,"TaxFare":865.0,"TaxBreakup":[],"Discount":0.0,"GrossFare":4259.0,"Commission":337.0,"NetFare":3941.0,"ServiceCharge":10.0,"TDS":17.0,"GSTOnServiceCharge":{"SGST":0.0,"CGST":0.0,"IGST":2.0},"APIProductClass":null,"APIBrandName":null,"PexFareDetails":null},"AirlineFares":null,"PexFareDetails":[{"PaxType":1,"TotPax":1,"BaseFare":3384.0,"TaxFare":865.0,"YQTax":0.0,"GrossFare":4259.0,"NetFare":0.0,"ServiceCharge":10.0}],"AirSegments":[{"SegmentType":null,"BaggageAllowed":{"CheckInBaggage":"15 KG","HandBaggage":"7 KG"},"TypeOfClass":0,"AirlineCode":"SG","AirlineName":null,"FlightNumber":"293","AirCraftType":"7M8","FareClass":"W","Origen":"DEL","Destination":"AMD","OriginAirportTerminal":null,"DepartureDateTime":"2023-10-05T09:20:00","DestinationAirportTerminal":"T1","ArrivalDateTime":"2023-10-05T11:15:00","Duration":"1h : 55m","NumberofStops":0}],"TotalSeats":null}]},"BalanceFare":0.0}*/
		
		// SSR(Special Service Request) API
		/*$header = array
        (
            'Content-type: application/json'
        );
		$req = [
            'AgencyKey' => 'C35785B1-2B76-40BF-B7AB-B7893EE74020',
            'APIToken' => '2d22adf5-dd38-469e-a673-3c1a991251d9',
            'BookingKey' => 'eca52fac-4da3-4294-b775-f7f915edc22c',
            'ContractId' => 40
        ];
        
        $data = json_encode($req);
        
        
        
        echo $api_url = 'https://api.fly24hrs.com/api/Air/SSR';
        echo '<br />';
        echo $data;
        echo '<br />';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		die;*/
		
		
		// Fare Rules API
		/*$header = array
        (
            'Content-type: application/json'
        );
		$req = [
            'AgencyKey' => 'C35785B1-2B76-40BF-B7AB-B7893EE74020',
            'APIToken' => '599efc0f-7aa0-4041-9b52-8326432a39bb',
            'BookingKey' => '2563eff8-bcca-49b4-813d-4e9e7e8db959',
            'ContractId' => 4
        ];
        
        echo $data = json_encode($req);
        
        
        
        
        $api_url = 'https://api.fly24hrs.com/api/Air/FareRule';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		die;*/
		
		/*{
    "APIToken": "93aa9bd7-7d43-4f4b-97df-d351a59610a6",
    "Status": 1,
    "Error": {
        "ErrorCode": "0",
        "ErrorDesc": ""
    },
    "FareRule": "<div><h5>Fare Rules SpiceCorporate' </h5><span>Mentioned fees are Per Pax Per Sector</span><br /><span>Apart from airline charges, GST + RAF + applicable charges if any, will be charged.</span><table><thead><tr><th>Type</th><th>Cancellation Fee</th><th>Date Changes Fee</th><th>No Show</th><th>Seat Chargeable</th></tr></thead><tbody><tr><td>All</td><td><p><b>INR 199 + 59</b><br /><b>Cancellation permitted 03 Hrs before scheduled departure</p></td><td><p><b>Change permitted 03 Hrs before scheduled departure 149 + Fare Difference</b></p></td><td><p><b>If Cancelled within 3Hrs of scheduled departure & Only statutory taxes will be Refunded.</b></p></td><td><p><b>Standard seat free</b></p></td></tr></tbody></table></div>"
}*/

    // Flight Booking API
        /*$transaction_id = rand(1111,9999).rand(1111,9999);
		$header = array
        (
            'Content-type: application/json'
        );
		$req = [
            'AgencyKey' => 'C35785B1-2B76-40BF-B7AB-B7893EE74020',
            'APIToken' => '844d1076-0477-4425-8c38-f04d88d1e153',
            'BookingKey' => 'a0f29bac-fa35-41a1-b03b-4ff23b933342',
            'ContractId' => 40,
            'TxnId' => $transaction_id,
            'Flightpassenger' => array(
                0 => array(
                    'Title' => 'Mr',
                    'FirstName' => 'Ram',
                    'LastName' => 'Lal',
                    'PaxType' => 1,
                    'Gender' => 'M',
                    'DateOfBirth' => '08-07-1993',
                    'PassportNo' => null,
                    'PassportExpiry' => null,
                    'ContactNo' => '9879879877',
                    'Email' => 'testpayol@gmail.com',
                    'IsLeadPax' => true,
                    'MealCode' => null,
                    'BaggageCode' => null,
                    'SeatCode' => null,
                    'PaxFare' => array(
                        
                            'PaxType' => 1,
                            'TotPax' => 1,
                            'BaseFare' => 48079.0,
                            'TaxFare' => 7041.0,
                            'YQTax' => 0.0,
                            'NetFare' => 0.0,
                            'ServiceCharge' => 10.0,
                            'PriceBaggage' => 0.0,
                            'PriceMeal' => 0.0,
                            'PriceSeat' => 0.0
                        
                    ),
                    'TicketNumber' => null
                ),
                1 => array(
                    'Title' => 'Mr',
                    'FirstName' => 'Shyam',
                    'LastName' => 'Lal',
                    'PaxType' => 2,
                    'Gender' => 'M',
                    'DateOfBirth' => '08-07-1993',
                    'PassportNo' => null,
                    'PassportExpiry' => null,
                    'ContactNo' => '9879879877',
                    'Email' => 'testpayol@gmail.com',
                    'IsLeadPax' => true,
                    'MealCode' => null,
                    'BaggageCode' => null,
                    'SeatCode' => null,
                    'PaxFare' => array(
                        
                            'PaxType' => 2,
                            'TotPax' => 1,
                            'BaseFare' => 48079.0,
                            'TaxFare' => 7041.0,
                            'YQTax' => 0.0,
                            'NetFare' => 0.0,
                            'ServiceCharge' => 10.0,
                            'PriceBaggage' => 0.0,
                            'PriceMeal' => 0.0,
                            'PriceSeat' => 0.0
                        
                    ),
                    'TicketNumber' => null
                ),
                2 => array(
                    'Title' => 'Mr',
                    'FirstName' => 'Mohan',
                    'LastName' => 'Lal',
                    'PaxType' => 3,
                    'Gender' => 'M',
                    'DateOfBirth' => '08-07-1993',
                    'PassportNo' => null,
                    'PassportExpiry' => null,
                    'ContactNo' => '9879879877',
                    'Email' => 'testpayol@gmail.com',
                    'IsLeadPax' => true,
                    'MealCode' => null,
                    'BaggageCode' => null,
                    'SeatCode' => null,
                    'PaxFare' => array(
                        
                            'PaxType' => 3,
                            'TotPax' => 1,
                            'BaseFare' => 2381.0,
                            'TaxFare' => 120.0,
                            'YQTax' => 0.0,
                            'NetFare' => 0.0,
                            'ServiceCharge' => 0.0,
                            'PriceBaggage' => 0.0,
                            'PriceMeal' => 0.0,
                            'PriceSeat' => 0.0
                        
                    ),
                    'TicketNumber' => null
                )
            ),
            'IsHoldRequest' => false,
            'APIGst' => array(
                'GSTNNumber' => '08sdf5dfd48855',
                'GSTCompanyName' => 'Payol',
                'GSTCompanyContact' => '9879879877',
                'GSTCompanyEmailId' => 'testpayol@gmail.com',
                'GSTCompanyAddress' => 'Jaipur'
            ),
            'BookingId' => $transaction_id,
            'AccountNo' => 14
        ];
        
        $data = json_encode($req);
        
        
        
        echo $api_url = 'https://api.fly24hrs.com/api/Air/BookFlight';
        echo '<br />';
        echo $data;
        echo '<br />';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		die;*/
		
		/*{"APIToken":"91d6dd65-6845-4e9b-af33-8736377c89ff","BookingKey":null,"ResponseStatus":1,"Error":{"ErrorCode":"0","ErrorDesc":"Call GetBookingDetails"},"IsFareChange":false,"IsTimeCharge":false,"IsLowFare":false,"ContractId":null,"BookingId":"10884854","BookingStatus":1,"AirlinePnr":"Test","Contracts":null,"Flightpassenger":null,"BookingRemark":"Call GetBookingDetails"}*/
		
		
		
	    // Get Booking Detail API
        $transaction_id = rand(1111,9999).rand(1111,9999);
		$header = array
        (
            'Content-type: application/json'
        );
		$req = [
            'AgencyKey' => 'C35785B1-2B76-40BF-B7AB-B7893EE74020',
            'APIToken' => '844d1076-0477-4425-8c38-f04d88d1e153',
            'BookingId' => '12009733',
            'AirlinePnr' => 'Test',
            'AccountNo' => 14
        ];
        
        $data = json_encode($req);
        
        
        
        echo $api_url = 'https://api.fly24hrs.com/api/Air/GetBookingDetails';
        echo '<br />';
        echo $data;
        echo '<br />';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		die;
		
		/*{"APIToken":"91d6dd65-6845-4e9b-af33-8736377c89ff","ResponseStatus":1,"Error":{"ErrorCode":"0","ErrorDesc":""},"IsFareChange":false,"IsTimeCharge":false,"ContractId":null,"BookingId":"10884854","BookingStatus":1,"AirlinePnr":null,"Contracts":[{"ContractId":4,"ContractType":null,"GDSPnr":"Test","AirlinePnr":"Test","AirlineCode":null,"Engine":4,"Lcc":false,"IsUKSpecial":false,"IsGoAirSpecialV2":false,"Refundable":true,"AirlineRemark":null,"FareType":0,"AirlineFare":{"ContractType":null,"Currency":"INR","BaseFare":3471.0,"YQTx":0.0,"TaxFare":1097.0,"TaxBreakup":[],"Discount":0.0,"GrossFare":4568.0,"Commission":99.0,"NetFare":4474.0,"ServiceCharge":10.0,"TDS":0.0,"GSTOnServiceCharge":{"SGST":0.0,"CGST":0.0,"IGST":0.0},"APIProductClass":null,"APIBrandName":null,"PexFareDetails":null},"AirlineFares":null,"PexFareDetails":[{"PaxType":1,"TotPax":1,"BaseFare":3471.0,"TaxFare":1097.0,"YQTax":0.0,"GrossFare":4568.0,"NetFare":0.0,"ServiceCharge":0.0}],"AirSegments":[{"SegmentType":null,"BaggageAllowed":{"CheckInBaggage":"15 KG","HandBaggage":"7 KG"},"TypeOfClass":0,"AirlineCode":"Indigo","AirlineName":null,"FlightNumber":"6034","AirCraftType":"320","FareClass":"R","Origen":"DEL","Destination":"AMD","OriginAirportTerminal":null,"DepartureDateTime":"2023-10-10T04:55:00","DestinationAirportTerminal":"T1","ArrivalDateTime":"2023-10-10T06:30:00","Duration":null,"NumberofStops":0}],"TotalSeats":null}],"Flightpassenger":[{"PaxId":"0","Title":"Mr","FirstName":"RAM","LastName":"LAL","PaxType":1,"Gender":null,"DateOfBirth":"08-07-1993","PassportNo":"","PassportExpiry":"","ContryCode":null,"ContactNo":null,"Email":"testpayol@gmail.com","IsLeadPax":false,"MealCode":"","BaggageCode":"","SeatCode":"","FrequentFlNo":null,"PaxFare":{"BaseFare":0.0,"TaxFare":0.0,"YQTax":0.0,"PriceBaggage":0.0,"PriceMeal":0.0,"PriceSeat":0.0},"TicketNumber":"Test"}]}*/
    
        // Get Account Balance API
        $transaction_id = rand(1111,9999).rand(1111,9999);
		$header = array
        (
            'Content-type: application/json'
        );
		$req = [
            'AgencyKey' => 'C35785B1-2B76-40BF-B7AB-B7893EE74020',
            'APIToken' => '599efc0f-7aa0-4041-9b52-8326432a39bb',
        ];
        
        $data = json_encode($req);
        
        
        
        echo $api_url = 'https://api.fly24hrs.com/api/Accounts/Balance';
        echo '<br />';
        echo $data;
        echo '<br />';
        
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($curl, CURLOPT_POST, 1);
		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		die;
		/*{"APIToken":"91d6dd65-6845-4e9b-af33-8736377c89ff","ResponseStatus":1,"Error":{"ErrorCode":"0","ErrorDesc":""},"CrLimit":0.0,"AGLimit":0.0}*/
    }
    
    public function phonepeUatUpi()
    {
        
        $transaction_id = rand(1111,9999).rand(1111,9999);
		
		
		$req = '{
  "merchantId": "M1UVHBZFEA7P",
  "merchantTransactionId": "'.$transaction_id.'",
  "merchantUserId": "MU933037302229373",
  "amount": 1000,
  "callbackUrl": "https://www.payol.in/syscallback/phonepeUpiCallBack",
  "mobileNumber": "9999999999",
  "deviceContext": {
    "deviceOS": "ANDROID"
  },
  "paymentInstrument": {
    "type": "UPI_INTENT",
    "targetApp": "com.phonepe.app"
  }
}';
        $request = base64_encode($req);
        $data = array(
            'request' => $request
        );
        $data = json_encode($data);
        echo $xverify = hash('sha256', $request.'/pg/v1/payd5927758-e8fa-4e76-8e5c-432440220c8d').'###1';
        echo '<br />';
        
        /*echo $xverify = hash('sha256', 'ewogICJtZXJjaGFudElkIjogIlBHVEVTVFBBWVVBVCIsCiAgIm1lcmNoYW50VHJhbnNhY3Rpb25JZCI6ICJNVDc4NTA1OTAwNjgxODgxMDQiLAogICJtZXJjaGFudFVzZXJJZCI6ICJNVUlEMTIzIiwKICAiYW1vdW50IjogMTAwMDAsCiAgInJlZGlyZWN0VXJsIjogImh0dHBzOi8vd2ViaG9vay5zaXRlL3JlZGlyZWN0LXVybCIsCiAgInJlZGlyZWN0TW9kZSI6ICJSRURJUkVDVCIsCiAgImNhbGxiYWNrVXJsIjogImh0dHBzOi8vd2ViaG9vay5zaXRlL2NhbGxiYWNrLXVybCIsCiAgIm1vYmlsZU51bWJlciI6ICI5OTk5OTk5OTk5IiwKICAicGF5bWVudEluc3RydW1lbnQiOiB7CiAgICAidHlwZSI6ICJQQVlfUEFHRSIKICB9Cn0=/pg/v1/pay099eb0cd-02cf-4e2a-8aca-3e6c6aff0399').'###1';
        echo '<br />';*/
        
        $header = array
        (
            'Content-type: application/json',
            'X-VERIFY: '.$xverify
        );
        
        //echo $api_url = 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay';
        echo $api_url = 'https://api.phonepe.com/apis/hermes/pg/v1/pay';
        echo '<br />';
        echo json_encode($header);
        
        
        echo '<br />';
        echo $req;
        echo '<br />';
        echo $data;
		// Initialize
		$curl = curl_init();

		//Set Options - Open

		// URL
		curl_setopt($curl, CURLOPT_URL, $api_url);

		// Return Transfer
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		
		// Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		// Request Method
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

		// Request Header
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		// Request Body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		// Set Options - Close

		// Execute
		echo $result = curl_exec($curl);
		
		if(!$result)
        {
            echo "Curl Error : " . curl_error($curl);
        }
        

		// Close
		curl_close ($curl);
		
		/*{
  "success": false,
  "code": "INTERNAL_SERVER_ERROR",
  "message": "There is an error trying to process your transaction at the moment. Please try again in a while."
}*/
		/*
		{"success":true,"code":"PAYMENT_INITIATED","message":"Payment Initiated","data":{"merchantId":"PGTESTPAYUAT77","merchantTransactionId":"MT7850590068188104","instrumentResponse":{"type":"UPI_INTENT","intentUrl":"upi://pay?pa=PGTESTPAYUAT77@ybl&pn=MERCHANT&am=10&mam=10&tr=MT7850590068188104&tn=Payment%20for%20MT7850590068188104&mc=5311&mode=04&purpose=00&utm_campaign=B2B_PG&utm_medium=PGTESTPAYUAT77&utm_source=MT7850590068188104&mcbs="}}}
		*/
		
        
		die;
		
    }
	            
	            
	            
	  public function aepsWithdrawalThreeWay()
		            
		            {
		                
		                $account_id = $this->User->get_domain_account();
	                	$accountData = $this->User->get_account_data($account_id);
                       
		            	log_message('debug', 'Fingpay 3Way Recon AEPS api API Call');
        $api_url = 'https://fpanalytics.tapits.in/fpcollectservice/api/threeway/aggregators';
        log_message('debug', 'Fingpay 3Way Recon  AEPS api API Url - '.$api_url);
        
        $accountData['fingpay_aeps_username'] = 'payold';
        $accountData['fingpay_aeps_password'] = '1234d';
        $accountData['aeps_supermerchant_id'] = '1244';
        
        $txn_date = '06-09-2023';
        
        $postdata[0] = array 
        (
            "merchantTransactionId"=>'FIAP1694093673',
            "fingpayTransactionId"=>'MBB5386579070923190433100I',
            "transactionRrn" => '325019596837',
            "responseCode"=>0,
            "transactionDate"=>'06-09-2023',
            "serviceType"=>'M'
        );
        
        
        // Generate JSON
    $json = json_encode($postdata);
        
        $hash_string = $json.'payold'.'820a176061bdc289d6c35f2471caa2134ab923496b2dbcf334fa1d6ab607d3ae';
        
        log_message('debug', 'Fingpay 3Way Recon api post data - '.$json);
        // Generate Session Key
       
        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'txnDate:'.$txn_date,
            'hash:'.base64_encode(hash('sha256', $hash_string, true)),
            'superMerchantLoginId:'.$accountData['fingpay_aeps_username'] ,
            'superMerchantid:'.$accountData['aeps_supermerchant_id']

        );

        
        log_message('debug', 'Fingpay 3 way api header data - '.json_encode($header));
        
        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, $api_url);

        // Return Transfer
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // SSL Verify Peer
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        // SSL Verify Host
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        // Timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        // HTTP Version
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        // Request Method
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

        // Request Header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

        // Set Options - Close

        // Execute
        $output = curl_exec($curl);

        // Close
        curl_close ($curl);
        
        
        log_message('debug', 'Fingpay 3 Way  api response - '.json_encode($output));
		            
         echo $output;
         
         /*
         {"apiStatus":true,"apiStatusMessage":"Request Completed","data":[{"merchantTransactionId":"FIAW1692943957","fingpayTransactionId":"CWBT5386579250823114237361I","transactionRrn":"323711079121","responseCode":"0","referenceId":"1244060923180902","transactionDate":"06-09-2023","serviceType":"CW"}],"apiStatusCode":0}
         
         
         */
        die('success');

}


public function updateMemberFingpayAepsStatus()
	{
	    log_message('debug', 'Member 2FA Fingpay  Status Api call.');
		$account_id = $this->User->get_domain_account();
		 $recordList = $this->db->get_where('users',array('account_id'=>$account_id))->result_array();
        if($recordList)
        {
            foreach($recordList as $list)
            {
                $recordID = $list['id'];
                	
                
                if($recordID)
                {
                    $Data = array(
                        'fingpay_2fa_aeps_status' => 0,
                        'fingpay_2fa_ap_status' =>0,
                        'fingpay_2fa_charge' =>0
                        
                    );
                    $this->db->where('id',$recordID);
                    $this->db->update('users',$Data);
                }
               
            }

        }
       
        die('done');
        log_message('debug', 'Member 2FA Fingpay  Status Update.');
		echo json_encode(array('status'=>1,'msg'=>'success'));
	}
	
	
	public function SettlementAepsTranscation()
	{
	    $account_id = $this->User->get_domain_account();
	    $accountData = $this->User->get_account_data($account_id);
	                	
	    $recordList = $this->db->where_in('service',array('aadharpay','balwithdraw'))->get_where('member_aeps_transaction',array('account_id'=>$account_id,'is_settlement'=>0))->result_array();
	       
	    if($recordList)
        {
            foreach($recordList as $list)
            {
                $recordID = $list['id'];
               
                
                if($recordID)
                {   
                    $memberID = $list['member_id'];
                     $api_response = $list['api_response'];	
                    $decode_data = json_decode($api_response, true);
	            
	            $bank_rrn = $decode_data['data']['bankRRN'];
        	    $fp_txn_id = $decode_data['data']['fpTransactionId'];
        	    $merchant_txn_id = $decode_data['data']['merchantTransactionId'];
        	    if($list['status'] == 2)
        	    {
        	        
        	    $response_code = '00'; 
        	    
        	    }
        	    else{
        	        
        	        $response_code = 'FAILED';
        	        
        	    }
        	    
	            if($list['service'] == 'balwithdraw')
	            {
	                $txn_type = 'CW';
	            }
	            elseif($list['service'] == 'aadharpay')
	            {
	                $txn_type = 'M';
	            }
	            
	            
                    log_message('debug', 'Fingpay 3Way Recon AEPS api API Call');
                    $api_url = 'https://fpanalytics.tapits.in/fpcollectservice/api/threeway/aggregators';
                    //$api_url = 'https://fpuat.tapits.in/fpcollectservice_uat/api/threeway/aggregators';
                    log_message('debug', 'Fingpay 3Way Recon  AEPS api API Url - '.$api_url);
        
                    $accountData['fingpay_aeps_username'] = 'payold';
                    $accountData['fingpay_aeps_password'] = '1234d';
                    $accountData['aeps_supermerchant_id'] = '1244';
                    
                    $txn_date = date('d-m-Y');
                    
                    $postdata[0] = array 
                    (
                        "merchantTransactionId"=>$merchant_txn_id,
                        "fingpayTransactionId"=>$fp_txn_id,
                        "transactionRrn" => $bank_rrn,
                        "responseCode"=>$response_code,
                        "transactionDate"=>date('d-m-Y'),
                        "serviceType"=>$txn_type
                    );
                    
        
                    // Generate JSON
                $json = json_encode($postdata);
                    
                    $hash_string = $json.'payold'.'820a176061bdc289d6c35f2471caa2134ab923496b2dbcf334fa1d6ab607d3ae';
                    
                    log_message('debug', 'Fingpay 3Way Recon api post data - '.$json);
                    // Generate Session Key
                   
                    // Create Header
                    $header = array
                    (
                        'Content-type: application/json',
                        'txnDate:'.$txn_date,
                        'hash:'.base64_encode(hash('sha256', $hash_string, true)),
                        'superMerchantLoginId:'.$accountData['fingpay_aeps_username'] ,
                        'superMerchantid:'.$accountData['aeps_supermerchant_id']
            
                    );
            
                    
                    log_message('debug', 'Fingpay 3 way api header data - '.json_encode($header));
                    
                    // Initialize
                    $curl = curl_init();
            
                    //Set Options - Open
            
                    // URL
                    curl_setopt($curl, CURLOPT_URL, $api_url);
            
                    // Return Transfer
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
                    // SSL Verify Peer
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            
                    // SSL Verify Host
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            
                    // Timeout
                    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            
                    // HTTP Version
                    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            
                    // Request Method
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            
                    // Request Header
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            
                    // Request Body
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            
                    // Set Options - Close
            
                    // Execute
                    $output = curl_exec($curl);
            
                    // Close
                    curl_close ($curl);
                    
                    
                    log_message('debug', 'Fingpay 3 Way  api response - '.json_encode($output));
                    
                             $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'post_data'=>$json,
						        	'api_response' => $output,
						        	'created' => date('Y-m-d H:i:s'),
						        	'created_by' => $memberID
						        );
						        $this->db->insert('recon_aeps_api_response',$apiData);
						        
						        if(isset($responseData['apiStatusMessage']) && $responseData['apiStatusMessage'] == 'Request Completed')
						        {
						            
						            $this->db->where('account_id',$account_id);
						            $this->db->where('id',$recordID);
					                $this->db->update('member_aeps_transaction',array('is_settlement'=>1));
						            
						        }
						        
                }
               
            }

        }
       
        die('done');
        log_message('debug', '#way Recon Api  Status Update.');
		echo json_encode(array('status'=>1,'msg'=>'success'));
		 
		 
	   
	    die;
	}
	
	public function testUpiPayoutYes(){
	    
	        $enckey = '7153f272dbdc71b459c6b49551988767';
	    //Pay API
		$header = [
		    'Content-type: application/json',
		    'X-IBM-Client-Secret: U0fW7iH6eH5lH1qS5wX0cQ2tC4hB8oH3lE8mV1pR7wM2dL1hF6',
		    'X-IBM-Client-ID: 0d38b91a-2b06-491c-8ef1-6da43d24dc89'
		];
		
		
		#echo $requestStr = 'YES0000000065149|Test1270707775|test|10|INR|P2P|Pay|1520||||||aamir@yesb|||||UPI| ABC||||||VPA|||||||||NA|NA';
		$requestStr = 'YES0000011547194|1252125241525118|test|10.00|INR|P2P|Pay|1520||||||lakshyabob@punb|||||UPI|Pushpender||||||VPA|||||||||NA|NA';
		$encryptData = $this->yesEncryptValue($requestStr,$enckey);
		
		
			
		$data = '{"requestMsg":"'.$encryptData.'","pgMerchantId":"YES0000011547194"}';
		
		
		$api_url = 'https://skyway.yesbank.in:443/app/live/upi/mePayServerReqImp';
        
        $cert_path = getcwd().'/publiccrt.crt';

        $key_path = getcwd().'/privatekey.key';

        $cert_password = '';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 

        $response = curl_exec($ch);

 

        if(curl_errno($ch)){

            $error_no = curl_errno($ch);

            echo $error = curl_error($ch);

            
        }
        
        curl_close ($ch);
        
        echo $this->yesDecryptValue($response,$enckey);
        
        die;
        
	}
	
	
	public function checkVpaYes()
	{
	    $enckey = '7153f272dbdc71b459c6b49551988767';
	    
	    
	    $response = '72911D561E4826687EE58120C69980FF6D16097F6AF51DE6CAC4C39C1D53116B428EB29E8FEA24DD18BBFA556E0BABA2457B6F5281E557397483E440FD3D2B82D4AF71B55EEBF69CA079E2A053782E090383D35137FD0C583A16A1386A8958A5D003748A88CED26D81F08387E01E2659A946237924BC67548CD33D9C570051106061056D36E584DA9AAC4DF404C1E2E2455E9211A965F7B4C93D0DEF16AAB2B2714E6733E58A7F968FD2AD3AE60656F01063CDC0CA334FC3576F9CAD1CD92753258AD9C210A4EBD5EC96C82F8875CA38CE4E99D65117931F762A95280E63D2EEBAF8B85E92288A1BFAE50F6E99C1CF2766B4BCBCFE594C535CDD7351EA81FE9AC15E16A6FBCC3D0CDB52C27D10ADDD0D91178AA1C68EFFA42EA12F74A8727B13DB973003A18DBB8C08AE236837120EF7';
	    
        
       echo  $this->yesDecryptValue($response,$enckey);
        
        die;
        
	}
	

	
	
	public function getAepsCom()
	{
	    $amount=5000;
	    $account_id =2;
	    $com_type = 1;
	   $commisionData =  $this->get_aeps_commission($amount, $loggedAccountID = 8, $com_type = 1);
	    
	    //$commisionData = $this->User->get_aeps_commission($amount,$memberID,$com_type);
        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

       
        // calculate aeps commision
        if($com_amount)
        {
            $is_paid = 0;
            if($is_surcharge)
            {
                $is_paid = 1;
            }
            $commData = array(
                'account_id' => $account_id,
                'member_id' => $memberID,
                'type' => $com_type,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $com_amount,
                'is_surcharge' => $is_surcharge,
                'wallet_settle_amount' => $com_amount,
                'is_paid' => $is_paid,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
            );
            
            echo "<pre>";
            print_r($commData);die;
            $this->db->insert('member_aeps_comm',$commData);

            //get member wallet_balance
            
            $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);
            
            if($is_surcharge)
            {
                $after_wallet_balance = $before_wallet_balance - $com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 2,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS3 Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

            }
            
        }
        
	    
	}
	
	public function get_aeps_commission($amount = 0, $loggedAccountID = 0, $com_type = 0, $domain_account_id = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$member_role_id = $this->User->getMemberRoleID($loggedAccountID);
		$is_surcharge = 0;
		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}
		$commission_amount = 0;
		// get surcharge
		$getSurcharge = $this->db->get_where('aeps_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'start_range <='=>$amount,'end_range >='=>$amount,'com_type'=>$com_type))->row_array();
		
		if($getSurcharge)
		{
			$is_flat = $getSurcharge['is_flat'];
			$is_surcharge = $getSurcharge['is_surcharge'];
			if($member_role_id == 3)
			{
				$commission = $getSurcharge['md_commision'];
			}
			elseif($member_role_id == 4)
			{
				$commission = $getSurcharge['dt_commision'];
			}
			elseif($member_role_id == 5)
			{
				$commission = $getSurcharge['rt_commision'];
			}
			elseif($member_role_id == 6)
			{
				$commission = $getSurcharge['api_commision'];
			}
			elseif($member_role_id == 8)
			{
				$commission = $getSurcharge['user_commision'];
			}
			if($commission)
			{
				$commission_amount = round(($commission/100)*$amount,2);
				if($is_flat)
				{
					$commission_amount = $commission;
				}
			}
		}
		return array('commission_amount'=>$commission_amount,'is_surcharge'=>$is_surcharge);
	}
	 
	 
	 
	 
	 	public function SettlementAepsTranscationV1()
	{
	    $account_id = $this->User->get_domain_account();
	    $accountData = $this->User->get_account_data($account_id);
	                	
	    $recordList = $this->db->where_in('service',array('aadharpay','balwithdraw'))->get_where('member_aeps_transaction',array('account_id'=>$account_id,'txnID'=>'FIAW1704278705'))->result_array();
	       
	    if($recordList)
        {
            foreach($recordList as $list)
            {
                $recordID = $list['id'];
               
                
                if($recordID)
                {   
                    $memberID = $list['member_id'];
                     $api_response = $list['api_response'];	
                    $decode_data = json_decode($api_response, true);
	            
	            $bank_rrn = $decode_data['data']['bankRRN'];
        	    $fp_txn_id = $decode_data['data']['fpTransactionId'];
        	    $merchant_txn_id = $decode_data['data']['merchantTransactionId'];
        	    if($list['status'] == 2)
        	    {
        	        
        	    $response_code = '00'; 
        	    
        	    }
        	    else{
        	        
        	        $response_code = 'FAILED';
        	        
        	    }
        	    
	            if($list['service'] == 'balwithdraw')
	            {
	                $txn_type = 'CW';
	            }
	            elseif($list['service'] == 'aadharpay')
	            {
	                $txn_type = 'M';
	            }
	            
	            
                    log_message('debug', 'Fingpay 3Way Recon AEPS api API Call');
                    //$api_url = 'https://fpanalytics.tapits.in/fpcollectservice/api/threeway/aggregators';
                    $api_url = 'https://fpuat.tapits.in/fpcollectservice_uat/api/threeway/aggregators';
                    log_message('debug', 'Fingpay 3Way Recon  AEPS api API Url - '.$api_url);
        
                    $accountData['fingpay_aeps_username'] = 'payold';
                    $accountData['fingpay_aeps_password'] = '1234d';
                    $accountData['aeps_supermerchant_id'] = '1244';
                    
                    $txn_date = date('d-m-Y');
                    
                    $postdata[0] = array 
                    (
                        "merchantTransactionId"=>$merchant_txn_id,
                        "fingpayTransactionId"=>$fp_txn_id,
                        "transactionRrn" => $bank_rrn,
                        "responseCode"=>$response_code,
                        "transactionDate"=>date('d-m-Y'),
                        "serviceType"=>$txn_type
                    );
                    
        
                    // Generate JSON
                $json = json_encode($postdata);
                    
                    $hash_string = $json.'payold'.'820a176061bdc289d6c35f2471caa2134ab923496b2dbcf334fa1d6ab607d3ae';
                    
                    log_message('debug', 'Fingpay 3Way Recon api post data v1 - '.$json);
                    // Generate Session Key
                   
                    // Create Header
                    $header = array
                    (
                        'Content-type: application/json',
                        'txnDate:'.$txn_date,
                        'hash:'.base64_encode(hash('sha256', $hash_string, true)),
                        'superMerchantLoginId:'.$accountData['fingpay_aeps_username'] ,
                        'superMerchantid:'.$accountData['aeps_supermerchant_id']
            
                    );
            
                    
                    log_message('debug', 'Fingpay 3 way api header data - '.json_encode($header));
                    
                    // Initialize
                    $curl = curl_init();
            
                    //Set Options - Open
            
                    // URL
                    curl_setopt($curl, CURLOPT_URL, $api_url);
            
                    // Return Transfer
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
                    // SSL Verify Peer
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            
                    // SSL Verify Host
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            
                    // Timeout
                    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            
                    // HTTP Version
                    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            
                    // Request Method
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            
                    // Request Header
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            
                    // Request Body
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            
                    // Set Options - Close
            
                    // Execute
                    $output = curl_exec($curl);
            
                    // Close
                    curl_close ($curl);
                    
                    
                    echo $output;
                    die;
                    
                    log_message('debug', 'Fingpay 3 Way  api response - '.json_encode($output));
                    
                             $responseData = json_decode($output,true);

						        $apiData = array(
						        	'account_id' => $account_id,
            						'user_id' => $memberID,
						        	'api_url' => $api_url,
						        	'post_data'=>$json,
						        	'api_response' => $output,
						        	'created' => date('Y-m-d H:i:s'),
						        	'created_by' => $memberID
						        );
						        $this->db->insert('recon_aeps_api_response',$apiData);
						        
						        if(isset($responseData['apiStatusMessage']) && $responseData['apiStatusMessage'] == 'Request Completed')
						        {
						            
						            $this->db->where('account_id',$account_id);
						            $this->db->where('id',$recordID);
					                $this->db->update('member_aeps_transaction',array('is_settlement'=>1));
						            
						        }
						        
                }
               
            }

        }
       
        die('done');
        log_message('debug', '#way Recon Api v1  Status Update.');
		echo json_encode(array('status'=>1,'msg'=>'success'));
		 
		 
	   
	    die;
	}
	
	
	public function openMoneyCallback()
    {

         log_message('debug', 'Open Money Payout Callback Called.');
        $callbackData = file_get_contents('php://input'); 
        // $callbackData = '{"id":"evl_8V97pfHUdNi5oJk1IQ5FPWRmx","object":"event","name":"transfers.updated","is_sandbox":false,"data":{"object":{"id":"tr_gaReU9QjcfXXvwLcqKRYgAp1T","object":"transfer","type":"account_number","amount":50,"debit_account_id":"va_vbx85JXfZF2eYwJqySLvppVVL","beneficiary_id":"vab_cmVZbZO3i5duzWGNMHJxV5iQM","status":"pending","currency_code":"inr","payment_mode":"imps","payment_remark":"Vendor Payment","paid_to":"8745000100015076","beneficiary_name":"Lakshya Gujrati","beneficiary_ifsc":"PUNB0874500","otp_attempts":0,"merchant_reference_id":"10210212520218590","transacted_at":1706099802,"is_sandbox":false,"created_at":1706099802}}}';
        log_message('debug', 'Open Money Payout Callback Data - '.$callbackData);
        /*
        {"id":"evl_q8wzlcEIGhAOUOAQqg7mJovQT","object":"event","name":"transfers.updated","is_sandbox":false,"data":{"object":{"id":"tr_iAFX2t8OT3qe3T4aNiRblru8x","object":"transfer","type":"account_number","amount":10,"debit_account_id":"va_vbx85JXfZF2eYwJqySLvppVVL","beneficiary_id":"vab_eCPwSXV9bbKv3L9K48XJ7Codu","status":"success","bank_reference_number":"403216207372","currency_code":"inr","payment_mode":"imps","payment_remark":"payout","paid_to":"8745000100015076","beneficiary_name":"Lakshya","beneficiary_ifsc":"PUNB0874500","otp_attempts":0,"merchant_reference_id":"lop521252455284","transacted_at":1706784597,"is_sandbox":false,"created_at":1706784547}}}
        */

         $payoutCallbackData = json_decode($callbackData,true);

         if($payoutCallbackData['name'] == "transfers.updated"){

           
          $data = $payoutCallbackData['data']['object'];
            
            $txid = $data['merchant_reference_id'];
            $optxid = $data['id'];
            $rrn = isset($data['bank_reference_number']) ? $data['bank_reference_number'] : '';
            $api_status = strtolower($data['status']);

            $dmt_status = $this->db->get_where('open_money_payout',array('transaction_id'=>$txid,'status'=>2))->num_rows();
            $dmt_status_2 = $this->db->get_where('settlement_open_money_payout',array('transaction_id'=>$txid,'status'=>2))->num_rows();
            if($dmt_status)
            {
                // get member id and amount
                $get_recharge_data = $this->db->get_where('open_money_payout',array('transaction_id'=>$txid,'status'=>2))->row_array();
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Callback Record Data - '.json_encode($get_recharge_data).'.]'.PHP_EOL;
                #$this->User->generateCallbackLog($log_msg);
                $dmt_id = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
                $account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
                $member_id = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
                $amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
                $total_wallet_charge = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
                $transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : '' ;

                

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Callback Status - '.$api_status.'.]'.PHP_EOL;
                #$this->User->generateCallbackLog($log_msg);
                $status = 0;
                if($api_status == 'success')
                {
                    $status = 3;
                }
                elseif($api_status == 'failed' || $api_status == 'failed')
                {
                    $status = 4;
                }
                elseif($api_status == 'pending')
                {
                    $status = 2;
                }
                
                if($txid)
                {
                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Callback API Status Updated.]'.PHP_EOL;
                    #$this->User->generateCallbackLog($log_msg);
                    // update fund transfer status
                    
                    $force_status = 0;

                    $fundData = array(
                        'rrn' => $rrn,
                        'optxid'=>$optxid,
                        'status' => $status,
                        'force_status' =>$force_status,
                        'is_updated_by_callback' =>1,
                        'updated' => date('Y-m-d H:i:s')
                    );
                    $this->db->where('transaction_id',$txid);
                    $this->db->update('open_money_payout',$fundData);

                    // refund payment into wallet
                    if($status == 4)
                    {
                        if($member_id)
                        {
                            $before_balance = $this->User->getMemberWalletBalanceSP($member_id);
                            $after_balance = $before_balance + $total_wallet_charge;    
                            $member_code = $before_balance['user_code'];    

                            // save system log
                            $log_msg = '['.date('d-m-Y H:i:s').' - Open  Payout Callback API Refund to Member - '.$member_code.' - Before Balance - '.$before_balance.' - Refund Amount - '.$total_wallet_charge.' - After Balance - '.$after_balance.'.]'.PHP_EOL;
                            #$this->User->generateCallbackLog($log_msg);
                            
                            $wallet_data = array(
                                'account_id'          => $account_id,
                                'member_id'           => $member_id,    
                                'before_balance'      => $before_balance,
                                'amount'              => $total_wallet_charge,  
                                'after_balance'       => $after_balance,      
                                'status'              => 1,
                                'type'                => 1,  
                                'wallet_type'         => 1,     
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => 1,
                                'description'         => 'Payout #'.$transaction_id.' Refund Credited'
                            );

                            $this->db->insert('member_wallet',$wallet_data);
 
                            // get member role id
                            // get account role id
                            $get_role_id = $this->db->select('role_id,open_payout_call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
                            $user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
                            $api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
                            if($user_role_id == 6)
                            {
                                $user_call_back_url = isset($get_role_id['open_payout_call_back_url']) ? $get_role_id['open_payout_call_back_url'] : '' ;
                                // save system log
                                $log_msg = '['.date('d-m-Y H:i:s').' - Open  Payout Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
                                #$this->User->generateCallbackLog($log_msg);

                                $user_callback_data_url  = $user_call_back_url.'?status=FAILED&txnid='.$txid.'&optxid='.$rrn.'&amount='.$amount.'&rrn='.$rrn;
                                
                                
                                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Data send to API Member - '.$api_member_code.' - Call Back URL - '.$user_callback_data_url.'.]'.PHP_EOL;
                                $this->User->generateCallbackLog($log_msg);
                                

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                                $output = curl_exec($ch); 
                                curl_close($ch);

                                // save system log
                                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Send Successfully.]'.PHP_EOL;
                                #$this->User->generateCallbackLog($log_msg);
                                
                            }
                        }
                    }
                    elseif($status == 3)
                    {
                        // get dmr surcharge
                        $surcharge_amount = $this->User->get_dmr_surcharge($amount,$member_id);
                        // save system log
                        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
                        #$this->User->generateCallbackLog($log_msg);
                       
                        // get member role id
                        // get account role id
                        $get_role_id = $this->db->select('role_id,open_payout_call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
                        $user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
                        $api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
                        if($user_role_id == 6)
                        {
                            $user_call_back_url = isset($get_role_id['open_payout_call_back_url']) ? $get_role_id['open_payout_call_back_url'] : '' ;
                            // save system log
                            $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
                            #$this->User->generateCallbackLog($log_msg);

                            $user_callback_data_url  = $user_call_back_url.'?status=SUCCESS&txnid='.$txid.'&optxid='.$rrn.'&amount='.$amount.'&rrn='.$rrn;
                              
                                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Data send to API Member - '.$api_member_code.' - Call Back URL - '.$user_callback_data_url.'.]'.PHP_EOL;
                            $this->User->generateCallbackLog($log_msg);
                            

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                            //curl_setopt($ch, CURLOPT_POST, true);
                            //curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);     
                            $output = curl_exec($ch); 
                            curl_close($ch);

                            // save system log
                            $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Send Successfully.]'.PHP_EOL;
                            #$this->User->generateCallbackLog($log_msg);
                            
                        }
                    }
                }
                
            }
            elseif($dmt_status_2)
            {
                // get member id and amount
                $get_recharge_data = $this->db->get_where('settlement_open_money_payout',array('transaction_id'=>$txid,'status'=>2))->row_array();
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Callback Record Data - '.json_encode($get_recharge_data).'.]'.PHP_EOL;
                #$this->User->generateCallbackLog($log_msg);
                $dmt_id = isset($get_recharge_data['id']) ? $get_recharge_data['id'] : 0 ;
                $account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
                $member_id = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
                $amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
                $total_wallet_charge = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
                $transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : '' ;

                

                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Callback Status - '.$api_status.'.]'.PHP_EOL;
                #$this->User->generateCallbackLog($log_msg);
                $status = 0;
                if($api_status == 'success')
                {
                    $status = 3;
                }
                elseif($api_status == 'failed' || $api_status == 'failed')
                {
                    $status = 4;
                }
                elseif($api_status == 'pending')
                {
                    $status = 2;
                }
                
                if($txid)
                {
                    // save system log
                    $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Callback API Status Updated.]'.PHP_EOL;
                    #$this->User->generateCallbackLog($log_msg);
                    // update fund transfer status
                    
                    $force_status = 0;

                    $fundData = array(
                        'rrn' => $rrn,
                        'optxid'=>$optxid,
                        'status' => $status,
                        'force_status' =>$force_status,
                        'is_updated_by_callback' =>1,
                        'updated' => date('Y-m-d H:i:s')
                    );
                    $this->db->where('transaction_id',$txid);
                    $this->db->update('settlement_open_money_payout',$fundData);

                    // refund payment into wallet
                    if($status == 4)
                    {
                        if($member_id)
                        {
                            $before_balance = $this->User->getMemberWalletBalanceSP($member_id);
                            $after_balance = $before_balance + $total_wallet_charge;    
                            $member_code = $before_balance['user_code'];    

                            // save system log
                            $log_msg = '['.date('d-m-Y H:i:s').' - Open  Payout Callback API Refund to Member - '.$member_code.' - Before Balance - '.$before_balance.' - Refund Amount - '.$total_wallet_charge.' - After Balance - '.$after_balance.'.]'.PHP_EOL;
                            #$this->User->generateCallbackLog($log_msg);
                            
                            $wallet_data = array(
                                'account_id'          => $account_id,
                                'member_id'           => $member_id,    
                                'before_balance'      => $before_balance,
                                'amount'              => $total_wallet_charge,  
                                'after_balance'       => $after_balance,      
                                'status'              => 1,
                                'type'                => 1,  
                                'wallet_type'         => 1,     
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => 1,
                                'description'         => 'Payout #'.$transaction_id.' Refund Credited'
                            );

                            $this->db->insert('member_wallet',$wallet_data);
 
                            // get member role id
                            // get account role id
                            $get_role_id = $this->db->select('role_id,open_payout_call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
                            $user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
                            $api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
                            if($user_role_id == 6)
                            {
                                $user_call_back_url = isset($get_role_id['open_payout_call_back_url']) ? $get_role_id['open_payout_call_back_url'] : '' ;
                                // save system log
                                $log_msg = '['.date('d-m-Y H:i:s').' - Open  Payout Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
                                #$this->User->generateCallbackLog($log_msg);

                                $user_callback_data_url  = $user_call_back_url.'?status=FAILED&txnid='.$txid.'&optxid='.$rrn.'&amount='.$amount.'&rrn='.$rrn;
                                
                                
                                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Data send to API Member - '.$api_member_code.' - Call Back URL - '.$user_callback_data_url.'.]'.PHP_EOL;
                                $this->User->generateCallbackLog($log_msg);
                                

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                                $output = curl_exec($ch); 
                                curl_close($ch);

                                // save system log
                                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Send Successfully.]'.PHP_EOL;
                                #$this->User->generateCallbackLog($log_msg);
                                
                            }
                        }
                    }
                    elseif($status == 3)
                    {
                        // get dmr surcharge
                        $surcharge_amount = $this->User->get_dmr_surcharge($amount,$member_id);
                        // save system log
                        $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Surcharge Amount - '.$surcharge_amount.'.]'.PHP_EOL;
                        #$this->User->generateCallbackLog($log_msg);
                       
                        // get member role id
                        // get account role id
                        $get_role_id = $this->db->select('role_id,open_payout_call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
                        $user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
                        $api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
                        if($user_role_id == 6)
                        {
                            $user_call_back_url = isset($get_role_id['open_payout_call_back_url']) ? $get_role_id['open_payout_call_back_url'] : '' ;
                            // save system log
                            $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back send to API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.'.]'.PHP_EOL;
                            #$this->User->generateCallbackLog($log_msg);

                            $user_callback_data_url  = $user_call_back_url.'?status=SUCCESS&txnid='.$txid.'&optxid='.$rrn.'&amount='.$amount.'&rrn='.$rrn;
                              
                                $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Data send to API Member - '.$api_member_code.' - Call Back URL - '.$user_callback_data_url.'.]'.PHP_EOL;
                            $this->User->generateCallbackLog($log_msg);
                            

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                            //curl_setopt($ch, CURLOPT_POST, true);
                            //curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);     
                            $output = curl_exec($ch); 
                            curl_close($ch);

                            // save system log
                            $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Call Back Send Successfully.]'.PHP_EOL;
                            #$this->User->generateCallbackLog($log_msg);
                            
                        }
                    }
                }
                
            }
            else
            {
                // save system log
                $log_msg = '['.date('d-m-Y H:i:s').' -Open Payout Call Back - TxnID not valid or status already updated.]'.PHP_EOL;
                #$this->User->generateCallbackLog($log_msg);

            }


            $log_msg = '['.date('d-m-Y H:i:s').' - Open Payout Callback API Stop.]'.PHP_EOL;
            #$this->User->generateCallbackLog($log_msg);

            

         }


        echo json_encode(array('status'=>'SUCCESS','msg'=>'Callback received successfully'));
        
    }
    
    
    public function updateMemberTranscationAepsStatus()
	{
		$account_id = $this->User->get_domain_account();
		 $recordList = $this->db->get_where('users',array('account_id'=>$account_id))->result_array();
        if($recordList)
        {
            foreach($recordList as $list)
            {
                $recordID = $list['id'];
                	
                
                if($recordID)
                {
                    $Data = array(
                        'instantpay_every_aeps_status' => 0,
                        'fingpay_every_aeps_status' =>0
                        
                    );
                    $this->db->where('id',$recordID);
                    $this->db->update('users',$Data);
                }
               
            }

        }
       
        die('done');
        log_message('debug', 'Member 2FA  Transcation   Status Update.');
		echo json_encode(array('status'=>1,'msg'=>'success'));
	}
	
	public function rblbankPayoutUat()
    {
        // Payee Validation API
        echo $api_url = 'https://apideveloper.rblbank.com/test/sb/rbl/api/payee-validation/validate?client_id=d6eed61021fec17c213916228fb5a5f0&client_secret=12daacdbac81fdb01bf60885b1c7e71b';
        echo '<br />';
        $txnid = time().rand(1111,9999);
        $api_post_data = array();
        $api_post_data['beneficiaryAccValidationReq'] = array(
            'Header' => array(
                'TranID' => $txnid,
                'Corp_ID' => 'PAYOLDIGIT',
                'Maker_ID' => 'M001',
                'Checker_ID' => 'C001',
                'Approver_ID' => 'A001'
            ),
            'Body' => array(
                'beneficiaryName' => 'AritraSupport',
                'beneficiaryMobileNumber' => '9920433064',
                'accountNumber' => '123456041',
                'ifscCode' => 'NPCI0000001'
            ),
            'Signature' => array(
                'Signature' => ''
            )
        );
        
        echo $postData = json_encode($api_post_data);
        echo '<br />';
        
        $header = [
            'Content-type: application/json'
        ];
        echo json_encode($header);
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';
        #$cert_path = getcwd().'/payol.pfx';
        $key_path = getcwd().'/privatekey.key';
        $cert_password = '';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, 'PAYOLDIGIT:Welcome@123');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);
        #curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);      
        echo $output = curl_exec($ch); 
        $error_msg = '';
        if (curl_errno($ch)) {
            echo $error_msg = curl_error($ch);
        }
        curl_close($ch);
        die;
        
        // Account Statement API
        /*echo $api_url = 'https://apideveloper.rblbank.com/test/sb/rbl/v1/cas/statement?client_id=d6eed61021fec17c213916228fb5a5f0&client_secret=12daacdbac81fdb01bf60885b1c7e71b';
        echo '<br />';
        $txnid = time().rand(1111,9999);
        $api_post_data = array();
        $api_post_data['Acc_Stmt_DtRng_Req'] = array(
            'Header' => array(
                'TranID' => $txnid,
                'Corp_ID' => 'PAYOLDIGIT',
                'Maker_ID' => '',
                'Checker_ID' => '',
                'Approver_ID' => ''
            ),
            'Body' => array(
                'Acc_No' => '409001857215',
                'Tran_Type' => 'B',
                'From_Dt' => '2024-10-01',
                'Pagination_Details' => array(
                    'Last_Balance' => array(
                        'Amount_Value' => '',
                        'Currency_Code' => ''
                    ),
                    'Last_Pstd_Date' => '',
                    'Last_Txn_Date' => '',
                    'Last_Txn_Id' => '',
                    'Last_Txn_SrlNo' => '',
                ),
                'To_Dt' => '2024-10-15'
            ),
            'Signature' => array(
                'Signature' => ''
            )
        );
        
        echo $postData = json_encode($api_post_data);
        echo '<br />';
        
        $header = [
            'Content-type: application/json'
        ];
        echo json_encode($header);
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';
        #$cert_path = getcwd().'/payol.pfx';
        $key_path = getcwd().'/privatekey.key';
        $cert_password = '';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, 'PAYOLDIGIT:Welcome@123');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);
        #curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);      
        echo $output = curl_exec($ch); 
        $error_msg = '';
        if (curl_errno($ch)) {
            echo $error_msg = curl_error($ch);
        }
        curl_close($ch);
        die;*/
        
        // Balance API
        echo $api_url = 'https://apideveloper.rblbank.com/test/sb/rbl/v1/accounts/balance/query?client_id=d6eed61021fec17c213916228fb5a5f0&client_secret=12daacdbac81fdb01bf60885b1c7e71b';
        echo '<br />';
        $txnid = time().rand(1111,9999);
        $api_post_data = array();
        $api_post_data['getAccountBalanceReq'] = array(
            'Header' => array(
                'TranID' => $txnid,
                'Corp_ID' => 'PAYOLDIGIT',
                'Approver_ID' => ''
            ),
            'Body' => array(
                'AcctId' => '409001857215'
            ),
            'Signature' => array(
                'Signature' => '12345'
            )
        );
        
        echo $postData = json_encode($api_post_data);
        echo '<br />';
        
        $header = [
            'Content-type: application/json'
        ];
        echo json_encode($header);
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';
        #$cert_path = getcwd().'/payol.pfx';
        $key_path = getcwd().'/privatekey.key';
        $cert_password = '';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, 'PAYOLDIGIT:Welcome@123');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);
        #curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);      
        echo $output = curl_exec($ch); 
        $error_msg = '';
        if (curl_errno($ch)) {
            echo $error_msg = curl_error($ch);
        }
        curl_close($ch);
        die;
        
        
        // Payment API
        echo $api_url = 'https://apideveloper.rblbank.com/test/sb/rbl/v1/payments/corp/payment?client_id=d6eed61021fec17c213916228fb5a5f0&client_secret=12daacdbac81fdb01bf60885b1c7e71b';
        echo '<br />';
        $api_post_data = array();
        $txnid = time().rand(1111,9999);
        $api_post_data['Single_Payment_Corp_Req'] = array(
            'Header' => array(
                'TranID' => $txnid,
                'Corp_ID' => 'PAYOLDIGIT',
                'Maker_ID' => 'M001',
                'Checker_ID' => 'M002',
                'Approver_ID' => 'A001'
            ),
            'Body' => array(
                'Amount' => '20',
                'Debit_Acct_No' => '409001857215',
                'Debit_Acct_Name' => 'EFPITEC',
                'Debit_IFSC' => 'RATN0000001',
                'Debit_Mobile' => '1234567890',
                'Debit_TrnParticulars' => 'FARIDA',
                'Debit_PartTrnRmks' => '',
                'Ben_IFSC' => 'NPCI0000001',
                'Ben_Acct_No' => '123456073',
                'Ben_Name' => 'TEST PAYEE',
                'Ben_Address' => '',
                'Ben_BankName' => 'Punjab National Bank',
                'Ben_BankCd' => '',
                'Ben_BranchCd' => '',
                'Ben_Email' => '',
                'Ben_Mobile' => '',
                'Ben_TrnParticulars' => 'BENTEST',
                'Ben_PartTrnRmks' => 'Test IMPS',
                'Issue_BranchCd' => '',
                'Mode_of_Pay' => 'IMPS',
                'Remarks' => 'PAYEMNT QUEUE'
            ),
            'Signature' => array(
                'Signature' => ''
            )
        );
        
        echo $postData = json_encode($api_post_data);
        echo '<br />';
        
        $header = [
            'Content-type: application/json'
        ];
        echo json_encode($header);
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';
        #$cert_path = getcwd().'/payol.pfx';
        $key_path = getcwd().'/privatekey.key';
        $cert_password = '';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, 'PAYOLDIGIT:Welcome@123');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);
        #curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);      
        echo $output = curl_exec($ch); 
        $error_msg = '';
        if (curl_errno($ch)) {
            echo $error_msg = curl_error($ch);
        }
        curl_close($ch);
        die;
        
        // SUCCESS RESPONSE
        /*{"Single_Payment_Corp_Resp":{"Header":{"TranID":"45s4f5s4fs5f5","Corp_ID":"PAYOLDIGIT","Maker_ID":"","Checker_ID":"","Approver_ID":"","Status":"Initiated","Error_Cde":{},"Error_Desc":{}},"Body":{"RefNo":"SPPAYOLDIGIT45s4f5s4fs5f5","UTRNo":"RATNN24256595037","PONum":"000284623474","Ben_Acct_No":"023501546776","Amount":"10","BenIFSC":"ICIC0000235","Txn_Time":"2024-09-12 18:16:27.852362"},"Signature":{"Signature":""}}}*/
        // FAILED RESPONSE
        /*{"Single_Payment_Corp_Resp":{"Header":{"TranID":"45s4f5s4fs5f5","Corp_ID":"PAYOLDIGIT","Maker_ID":"","Checker_ID":"","Approver_ID":"","Status":"FAILED","Error_Cde":"ER013","Error_Desc":"Duplicate Transaction Id"},"Signature":{"Signature":""}}}*/
        
        // Payment Status API
        echo $api_url = 'https://apideveloper.rblbank.com/test/sb/rbl/v1/payments/corp/payment/query?client_id=d6eed61021fec17c213916228fb5a5f0&client_secret=12daacdbac81fdb01bf60885b1c7e71b';
        echo '<br />';
        $txnid = time().rand(1111,9999);
        $api_post_data = array();
        $api_post_data['get_Single_Payment_Status_Corp_Req'] = array(
            'Header' => array(
                'TranID' => $txnid,
                'Corp_ID' => 'PAYOLDIGIT',
                'Maker_ID' => '',
                'Checker_ID' => '',
                'Approver_ID' => ''
            ),
            'Body' => array(
                'OrgTransactionID' => '17273466372885'
            ),
            'Signature' => array(
                'Signature' => ''
            )
        );
        /*$api_post_data['get_Single_Payment_Status_Corp_Req'] = array(
            'Header' => array(
                'TranID' => '45s4f5s4fs5f5',
                'Corp_ID' => 'PAYOLDIGIT',
                'Maker_ID' => '',
                'Checker_ID' => '',
                'Approver_ID' => ''
            ),
            'Body' => array(
                'OrgTransactionID' => '45s4f5s4fs5f5'
            ),
            'Signature' => array(
                'Signature' => ''
            )
        );*/
        
        echo $postData = json_encode($api_post_data);
        echo '<br />';
        
        $header = [
            'Content-type: application/json'
        ];
        echo json_encode($header);
        echo '<br />';
        
        $cert_path = getcwd().'/publiccrt.crt';
        #$cert_path = getcwd().'/payol.pfx';
        $key_path = getcwd().'/privatekey.key';
        $cert_password = '';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, 'PAYOLDIGIT:Welcome@123');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        

        curl_setopt($ch, CURLOPT_SSLKEY, $key_path);
        #curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD , $cert_password);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);      
        echo $output = curl_exec($ch); 
        $error_msg = '';
        if (curl_errno($ch)) {
            echo $error_msg = curl_error($ch);
        }
        curl_close($ch);
        
        // SUCCESS RESPONSE
        /*{"get_Single_Payment_Status_Corp_Res":{"Header":{"TranID":"45s4f5s4fs5f5","Corp_ID":"PAYOLDIGIT","Maker_ID":"","Checker_ID":"","Approver_ID":"","Status":"SUCCESS","Error_Cde":"","Error_Desc":""},"Body":{"ORGTRANSACTIONID":"45s4f5s4fs5f5","AMOUNT":"10","REFNO":"SPPAYOLDIGIT45s4f5s4fs5f5","UTRNO":"RATNN24256595037","PONUM":"000284623474","BEN_ACCT_NO":"023501546776","BENIFSC":"ICIC0000235","TXNSTATUS":"Success","STATUSDESC":"","BEN_CONF_RECEIVED":"N","TXNTIME":"2024-09-12 18:16:26.25"},"Signature":{"Signature":""}}}*/
        // FAILED RESPONSE
        /*{"get_Single_Payment_Status_Corp_Res":{"Header":{"TranID":"45s4f5s4fs5f5","Corp_ID":"PAYOLDIGIT","Maker_ID":"","Checker_ID":"","Approver_ID":"","Status":"FAILED","Error_Cde":"ER010","Error_Desc":"UTRNo does not exist."},"Signature":{"Signature":""}}}*/
        
        
        
    }
    
    public function yesbankPayoutApiUat()
    {
        $partnerKey = 'M1hvNlZvZU';
        // Payment API
        echo $api_url = 'https://api.uatyespayhub.in/services/disbursement/pd';
        echo '<br />';
        $txid = time().rand(1111,9999);
        $api_post_data = array();
        $api_post_data['requestNumber'] = $txid;
        $api_post_data['partnerReferenceNo'] = $txid;
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['actionName'] = 'VA_FUND_TRANSFER';
        $api_post_data['p1'] = '132451132456';
        $api_post_data['p2'] = 'HDFC0123456';
        $api_post_data['p3'] = 'IMPS';
        $api_post_data['p4'] = '99';
        $api_post_data['p5'] = 'Testing';
        
        echo $postData = json_encode($api_post_data);
        echo '<br />';
        
        //$key = openssl_random_pseudo_bytes(32);
        $key = substr(str_shuffle(md5(time())), 0, 32);
        
    	$iv = substr(str_shuffle(md5(time())), 0, 16);
    	
    	#echo $encrypt_text 	= openssl_encrypt($postData, 'aes-256-gcm', $key, OPENSSL_PKCS5_PADDING, $iv,$tag);
    	$encrypt_text = openssl_encrypt($postData, 'aes-256-gcm', $key, OPENSSL_NO_PADDING, $iv, $tag); 
    	$encryptedBody = base64_encode($encrypt_text . $tag);
    	
        
        $bodyData = array();
        $bodyData['body'] = $encryptedBody;
        
        echo $bodyDataEncoded = json_encode($bodyData);
        echo '<br />';
        
        $headerToken = $this->yesBankPayoutTokenEncrypt($postData);
        
        $headerKey = $this->yesBankPayoutKeyEncrypt($key);
        
        $headerPartner = $this->yesBankPayoutKeyEncrypt($partnerKey);
        
        
        $header = [
            'token' => $headerToken,
            'key' => $headerKey,
            'partner' => $headerPartner,
            'iv' => $iv
        ];
        echo $headerDataEncoded = json_encode($header);
        echo '<br />';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyDataEncoded);      
        echo $output = curl_exec($ch); 
        $error_msg = '';
        if (curl_errno($ch)) {
            echo $error_msg = curl_error($ch);
        }
        curl_close($ch);
        
    }
    
    public function yesBankPayoutKeyEncrypt($dataToEncrypt)
    {
        
        //BANK PUBLIC KEY LIVE

$public_key = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtG/L62XUYsuRlh5n5yaY
wYf6aa4bUb/h+Xzstd00cifFT++MHYr5BQicNkNF2rYa86OsPJJDkPS/jht1syRE
RwN2S88cf4cWO9Tttx5FaQV+JgVTfNboaAyxnOkZebjSR9fA1Jo0t3phiDyDEJrW
wuwWMaxMzEH0iGL4KhratpZKYMeZv1eoVw7/+PWryn0im3jpL2ryYeho9LiYO1bV
qowuKcCfNmuvYgJDHJP+iEvNp5jYzVdQ9QnzyugU6l6iNliJjrljUVXJoYtVjeEY
ChupPfiYz/besrt3zNaFjD/J1zw+vuY3+kFt5St18bR2da5TGvlDiGTaWsHvAQGV
RQIDAQAB
-----END PUBLIC KEY-----
';

        openssl_public_encrypt($dataToEncrypt, $encrypted_data, $public_key, OPENSSL_PKCS1_PADDING);
        return base64_encode($encrypted_data);
    } 
    
    public function yesBankPayoutTokenEncrypt($data)
    {
        
        // private key for Payout UAT API
        /*$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEAk8ZOsRMR0iFpxKsbr7VtFMxbliOBB3YBd6aegC/d6xh64VL+
UGIpQ+D/qJnvBdzqAL7cAqJ63pj0ox8pYsEDil+SZZFmmTidBCZRnmZTuRwEg3iu
Wh0iIWNutqSssRPdhawtxqlcBcOkb3Uf7YxVPua3EtZJ2E4L2oIicEf75s0t4Lc+
K5cpPBU6DMeIwpv89MEI4Xwo094QLZ6Y0LaH2Wy6jYiKSDfGy74YD9tLQs3dWI8M
97PEHPfGeoXdQm48qcl9IzdekVc0ySSnXxVHPFF6HUD1d6kOKooSqRrJqTMp1f4f
elsuZzNUGPFZFWb5IbnleqiYl/Eo00HCi7p0OQIDAQABAoIBAHr/D/hquuZ/UR2U
ndK92zD4dzKcEoU3PzlrwXlIXhmTopNxFKOrUFVjLOgUV9I1cb91HIJE4qfr/5LW
GQhNRrkhmyRWxWK6sFYH3t9MpFrqawdSpyvyG7pWnIwAvIwW0Ma17NPxmtphYLg0
cIKzzzAvsClJmuUi8NLKhgeWoqASzxd6G/7ZLwu7Mw7l/SS49X/ndMwnANexQaA8
fPicgqZ2owhi4m1QEqyYz6zu/G99/XxNwrM9bwIRMR9obK536pTTA1CtqjlxRLj/
y1JqbaqduVyujEN8j4WGKsORVlzH4NotV3gY7HNXBDhpA9rr6vaf3owkKuY51r8l
u/fm1tECgYEA/+LIxZ/EMDDZtI3huS4Blfv/DKRNN5XD6Y+kxpuPl7c7IMR2KvJZ
fHCmT+lsru/jt8+USRVKqX/Bh00apHwDt2euHGeG36QhA7ChvUqrlXuX6MyAvNY8
mZjxpw4xfq7+EdDcDpYLKMCwntfacJuubzRjKBoDkUlO/8W1eJ8B9gMCgYEAk9ct
9jkp18ZYh9UYjXhNnMnNqJJuFpZN2BDtsD47JAG2G8VAgHyVjBgFdWlmU1f9lx3P
pkaKzwndrqBuL/nZjgCyO5CtWOEH3rYo+VmjManYF31ayghPxpNojJlVfP773iKK
CfVDJD+Qnte2wRwkJwWZK4P2L7aJGDWbLfIwZhMCgYAMs4ANtylwzuvvd19t5qez
fyegxAMFMEziKfwe05fkCvdHYBkRdqgUci7/JnH7mPKvrAELQ6BKG4pKofwhj/pL
Pz04MbdTIH13BmxwzeUIbXMT3hrBxMyLPzk3do/kXuFDlx9lJfN12WKq78Hq9v9I
i8wkMG5e5smiDnk8fIAHawKBgCuXexnq6QeSmHAUyVSNcHUWm+lko8lNME7RTwR0
lQOc7ZFqrAY2tfHE83cXSPCyNTfltmCU1EEP66md7F+BEEDqu6MMmdBnKeblV1eO
sqvmJK1obOi9YqhutpkebTKaDLIHBSTCiLWbeI0dlemC3rYS1IMcOe7/p5TX2ZXG
n7a5AoGBALDXPWKC7e5vv3XX2o5Gb1rTeFMmPZpaTGUsknNfMXpQt9jUtjfohiXd
TbbYLrKcZdELpWtKgfCsuudC9L1vw8Lkufi59QOSTTx82jQdiM2qQX88bn8Ya+Cs
O3mdGfCfRBCN90PfrOwLrE6/lmlBIiurmQnBwBTtzsOLVsxIZQ4J
-----END RSA PRIVATE KEY-----';*/

// private key for BAPA UAT API
$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIIEogIBAAKCAQB6WAoWuyXOYwHiLJC2kX+nLAGGqxHUNYO+OceoKv+dCb6MUCh8
oopqNH0PK48EvwqcCwfvdEXfkuisuKgGudOqkQ1wyTN6RWwDN4dF4OKTGdZGxTXg
jyoQNVqoFhD17G35/lULvaOdEGUzSTehiGm0g5z68nURiDeeJCGO6R8FJN9nDff6
K2H0IUofZsykU89vjlTW2A88DznSADg4M3ZlKZ9LTMfWQh2P7BxP2CC6yUYNbdDy
QLtFhNNDMa1rWe7w36WnxCrsQAcDZfi01Fh/TccvM6cacIpdf8Zi62iFr9OIMDW6
owx+7HjR2bpm3OnAdEuC5Rrk26FIjp0FQmZHAgMBAAECggEAMv3Mvqqj2EB1wYHX
kvBTpyZ1QF6+oJbpYPiOGa5KOXadP7gNF91bGWblFNSP2GeTkXQbi5cHpGaDqbGQ
/rSm3vl0A6UoWnbizPhw3hQb+zmSkI6TpROmcXj7XwFLcmRndgGmCq2wqr7xnw2O
LTpA7GbKn+E8H+GJZNUkU38gWbDtH5Y0NKFPKTG0MUyE7LJCqajAmSHR6lIPOfR6
W7GOesZkJXKNoEQcQr3Ys4UVNLgbxAGQoz8kxVNerxPfgPHF/DGutJckcpQChfpw
g8VmkJAzhtHXRaWno8Fzi7Up3UHmSucZBEBqKx67kkQj6hjVH14FyFlGmmhByQI0
9M8Y2QKBgQDYDhCCQQhTrhiBAb7cfselIIQrMpCqYZEXDG4neB87MWJL03smT8u7
tyIQ5bD4/rjf0O+A6KfSujpfJ08Z+A5aSvPfg8un4JTSBakYBHyCnVqN7JipfXDR
NbMPkyIKz6rG/wH9RnRY4pIqlIaWB9ZosEuq4Gqyo1QFm8xefiKPVQKBgQCQ9puP
eFLiPRECvr8+lubdj6DrpWZNhmDEjbJXE8XltNzAYhjZYog4yfO/GAlJ7RFql+DH
mYIwrVRBuFkEAIpH6zGPVGXkzxoKfRY3udZ/LmkDCsp4FXWwDSAt97IlWjZjXHjG
0p8Wn1WCjgpUBrub/MaX7xEVZl0Q78Kg+2UHKwKBgQCHT/+s5Dfn2LBGE6bKh6hX
1c6RE2EhJGvvKHzQwV2l/97VKIUHUJCDZ0WxmXlF6Jo2qq9lZ9C7nKC0OXVECWRL
zoeAJBXndTMfeiYWAbFNSGmPW2+dLw/6JwyXI4n9hWQqr0k6q+ZwSK5Mdqr3yb7R
4B/zzAtnB/22aYYMZCkW5QKBgHzrNhRFMM9qpi0CaNUrvphw+8/1ARqPUPYv0N3+
2QtgdtaF8jnqEc82IIis0txUoSyE4pu+KhV+V9wmWvb67C6be2Ky7PdmjF87eZjd
2PxR5wZLFzyx6W+jb/aMu4Q6oCGxfxZ+S7934H0Xn8jW4HbENfKZfdQ+GLbOlZ6Z
5G5vAoGAc/B3POByNl3tVHAwlI1njGzv+aogMrQX92b0vlNZcbM0t+3CF52T7QtG
UKi+CLySwkaGECpPoKvWKOlCJSBWxcz60QIupvxzVCYeIPWkq0TV93707MKSSdrV
hRYIh7EXh4wxSeR+GoNsmUlrL/YROQ2Y0wR/5JtMrxxhCpQIc0g=
-----END RSA PRIVATE KEY-----';

        
      openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA1);
      $signature = base64_encode($signature);
      return $signature;


    }
    
    
    public function yesbankBapaApiUat()
    {
        
        
        $partnerKey = 'M1hvNlZvZU';
        
        echo $api_url = 'https://api.uatyespayhub.in/services/seller/v2/ps';
        echo '<br />';
        $txid = time().rand(1111,9999);
        // Seller OnBoard API
        /*$api_post_data = array();
        $api_post_data['partnerReferenceNo'] = $txid;
        $api_post_data['actionName'] = 'ADD_PARTNER_SELLER';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'Payol Digital';
        $api_post_data['p2'] = 'KARZA TECHNOLOGIES PRIVATE LIMITED';
        $api_post_data['p3'] = 'PAYOL123';
        $api_post_data['p4'] = '8104758957';
        $api_post_data['p5'] = 'sonujangid2011@gmail.com';
        $api_post_data['p6'] = '1520';
        $api_post_data['p7'] = 'SMALL';
        $api_post_data['p8'] = 'ONLINE';
        $api_post_data['p9'] = 'PROPRIETARY';
        $api_post_data['p10'] = 'Jaipur';
        $api_post_data['p11'] = 'Jaipur';
        $api_post_data['p12'] = '14';
        $api_post_data['p13'] = '302021';
        $api_post_data['p14'] = 'AHPPN6562N';
        $api_post_data['p15'] = '27AHPPN6562N1Z7';
        $api_post_data['p16'] = '000590100021660';
        $api_post_data['p17'] = 'YESB0000782';
        $api_post_data['p18'] = '26.9124';
        $api_post_data['p19'] = '75.7873';
        $api_post_data['p20'] = 'Jaipur';
        $api_post_data['p21'] = 'Jaipur';
        $api_post_data['p22'] = '';
        $api_post_data['p23'] = '';
        $api_post_data['p24'] = '';
        $api_post_data['p25'] = '';
        $api_post_data['p26'] = '08/07/1993';
        $api_post_data['p27'] = '01/09/2024';
        $api_post_data['p28'] = 'https://www.payol.in';
        $api_post_data['p29'] = '';
        $api_post_data['p30'] = '';*/
        
        // Key Individual List API
        /*$api_post_data = array();
        $api_post_data['actionName'] = 'PS_FETCH_IND_LIST';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';*/
        
        // Due Diligence of Seller’s Key Individuals API
        /*$api_post_data = array();
        $api_post_data['actionName'] = 'PS_IND_DUE_DILIGENCE';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';
        $api_post_data['p2'] = '';
        $api_post_data['p3'] = 'rajesh ramdin gupta';
        $api_post_data['p4'] = '01/10/2000';
        $api_post_data['p5'] = 'M';
        $api_post_data['p6'] = 'AAAPA0039K';
        $api_post_data['p7'] = 'AADHAAR';
        $api_post_data['p8'] = '770101478520';
        $api_post_data['p9'] = '';*/
        
        // Complete Seller On-Boarding API
        /*$api_post_data = array();
        $api_post_data['actionName'] = 'COMPLETE_ONLINE_SELLER_DD';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';*/
        
        // Edit Seller API
        /*$api_post_data = array();
        $api_post_data['requestId'] = $txid;
        $api_post_data['actionName'] = 'EDIT_SELLER_DETAILS';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';
        $api_post_data['p2'] = '7619733999';
        $api_post_data['p3'] = 'payoldigital@gmail.com';
        $api_post_data['p4'] = 'SMALL';
        $api_post_data['p5'] = 'AHPPN6562N';
        $api_post_data['p6'] = '27AHPPN6562N1Z7';
        $api_post_data['p7'] = '000590100021660';
        $api_post_data['p8'] = 'YESB0000782';
        $api_post_data['p9'] = 'ACTIVE';
        $api_post_data['p10'] = '';
        $api_post_data['p11'] = '';
        $api_post_data['p12'] = '';
        $api_post_data['p13'] = '';
        $api_post_data['p14'] = '';*/
        
        // Fetch QR API
        /*$api_post_data = array();
        $api_post_data['requestId'] = '4f445d789df5d854';
        $api_post_data['actionName'] = 'FETCH_QR';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';*/
        
        // Create Seller User API
        /*$api_post_data = array();
        $api_post_data['partnerReferenceNo'] = $txid;
        $api_post_data['actionName'] = 'CREATE_SELLER_USER';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';
        $api_post_data['p2'] = 'Sonu Jangid';
        $api_post_data['p3'] = '8104758957';
        $api_post_data['p4'] = 'sonujangid2011@gmail.com';
        $api_post_data['p5'] = 'SELLER_USER_READ,SELLER_USER_WRITE';*/
        
        // Fetch Seller Balance API
        /*$api_post_data = array();
        $api_post_data['requestId'] = $txid;
        $api_post_data['actionName'] = 'FETCH_SELLER_BALANCE';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';*/
        
        // Fetch Seller Transactions API
        /*$api_post_data = array();
        $api_post_data['requestId'] = $txid;
        $api_post_data['actionName'] = 'FETCH_SELLER_TRANSACTIONS';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';
        $api_post_data['p2'] = '01102024';
        $api_post_data['p3'] = '10102024';
        $api_post_data['p4'] = '0';
        $api_post_data['p5'] = '';*/
        
        // Seller Settlement API
        /*$api_post_data = array();
        $api_post_data['partnerReferenceNo'] = $txid;
        $api_post_data['actionName'] = 'SELLER_SETTLEMENT';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';
        $api_post_data['p2'] = 'IMPS';*/
        
        
        // Raise Collect Request API
        /*$api_post_data = array();
        $api_post_data['requestReferenceNumber'] = $txid;
        $api_post_data['actionName'] = 'RAISE_COLL_REQ';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';
        $api_post_data['p2'] = 'test@ypay';
        $api_post_data['p3'] = '';
        $api_post_data['p4'] = '10';
        $api_post_data['p5'] = '20.00';
        $api_post_data['p6'] = 'Test';
        $api_post_data['p7'] = '';*/
        
        // Raise Collect Request Status API
        /*$api_post_data = array();
        $api_post_data['actionName'] = 'WEB_COLL_REQ_STATUS';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = '17295815453333';
        $api_post_data['p2'] = 'MERCHANT_CREDITED_VIA_COLLECT';*/
        
        // Validate VPA API
        /*$api_post_data = array();
        $api_post_data['actionName'] = 'VALIDATE_PAYER_VPA';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = '9967117192@ypay';*/
        
        // Register UPI Intent API
        /*$api_post_data = array();
        $api_post_data['orderId'] = $txid;
        $api_post_data['actionName'] = 'REGISTER_UPI_INTENT';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = 'PAYOL123';
        $api_post_data['p2'] = '10.00';
        $api_post_data['p3'] = '10';*/
        
        // UPI Intent Check Status API
        $api_post_data = array();
        $api_post_data['actionName'] = 'UPI_INTENT_STATUS';
        $api_post_data['partnerKey'] = $partnerKey;
        $api_post_data['p1'] = '17295825248674';
        
        
        echo $postData = json_encode($api_post_data);
        echo '<br />';
        
        //$key = openssl_random_pseudo_bytes(32);
        echo $key = substr(str_shuffle(md5(time())), 0, 32);
        
        echo '<br />';
        
        
    	$iv = substr(str_shuffle(md5(time())), 0, 16);
    	
    	$encrypt_text = openssl_encrypt($postData, 'aes-256-gcm', $key, OPENSSL_NO_PADDING, $iv, $tag); 
    	$encryptedBody = base64_encode($encrypt_text . $tag);
    	
        
        $bodyData = array();
        $bodyData['body'] = $encryptedBody;
        
        echo $bodyDataEncoded = json_encode($bodyData);
        echo '<br />';
        
        $headerToken = $this->yesBankPayoutTokenEncrypt($postData);
        
        $headerKey = $this->yesBankPayoutKeyEncrypt($key);
        
        $headerPartner = $this->yesBankPayoutKeyEncrypt($partnerKey);
        
        
        $header = [
            'Content-Type:application/json',
            'token:'.$headerToken,
            'key:'.$headerKey,
            'partner:'.$headerPartner,
            'iv:'.$iv
        ];
        echo $headerDataEncoded = json_encode($header);
        echo '<br />';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyDataEncoded);      
        $response = curl_exec($ch); 
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responseHeader = substr($response, 0, $header_size);
        $responseBody = substr($response, $header_size);
        $error_msg = '';
        if (curl_errno($ch)) {
            echo $error_msg = curl_error($ch);
        }
        curl_close($ch);
       
        $out = array();
$headers = true;
$lines = explode("\n",$responseHeader);

foreach ($lines as $l){
    $l = trim($l);

    if ($headers && !empty($l)){
        if (strpos($l,'HTTP') !== false){
            $p = explode(' ',$l);
            $out['Headers']['Status'] = trim($p[1]);
        } else {
            $p = explode(':',$l);
            $out['Headers'][$p[0]] = trim($p[1]);
        }
    } elseif (!empty($l)) {
        $out['Data'] = $l;
    }

    if (empty($l)){
        $headers = false;
    }
}

        
        echo $responseHeaderHash = $out['Headers']['hash'];
        echo '<br />';
        echo $responseHeaderKey = $out['Headers']['key'];
        echo '<br />';
        echo $responseHeaderIV = $out['Headers']['iv'];
        echo '<br />';
        
        $decodeOutput = json_decode($responseBody,true);
        $base64_text = isset($decodeOutput['body']) ? $decodeOutput['body'] : '';
        
        
        
        // decrypt process
        $private_key = '-----BEGIN RSA PRIVATE KEY-----
MIIEogIBAAKCAQB6WAoWuyXOYwHiLJC2kX+nLAGGqxHUNYO+OceoKv+dCb6MUCh8
oopqNH0PK48EvwqcCwfvdEXfkuisuKgGudOqkQ1wyTN6RWwDN4dF4OKTGdZGxTXg
jyoQNVqoFhD17G35/lULvaOdEGUzSTehiGm0g5z68nURiDeeJCGO6R8FJN9nDff6
K2H0IUofZsykU89vjlTW2A88DznSADg4M3ZlKZ9LTMfWQh2P7BxP2CC6yUYNbdDy
QLtFhNNDMa1rWe7w36WnxCrsQAcDZfi01Fh/TccvM6cacIpdf8Zi62iFr9OIMDW6
owx+7HjR2bpm3OnAdEuC5Rrk26FIjp0FQmZHAgMBAAECggEAMv3Mvqqj2EB1wYHX
kvBTpyZ1QF6+oJbpYPiOGa5KOXadP7gNF91bGWblFNSP2GeTkXQbi5cHpGaDqbGQ
/rSm3vl0A6UoWnbizPhw3hQb+zmSkI6TpROmcXj7XwFLcmRndgGmCq2wqr7xnw2O
LTpA7GbKn+E8H+GJZNUkU38gWbDtH5Y0NKFPKTG0MUyE7LJCqajAmSHR6lIPOfR6
W7GOesZkJXKNoEQcQr3Ys4UVNLgbxAGQoz8kxVNerxPfgPHF/DGutJckcpQChfpw
g8VmkJAzhtHXRaWno8Fzi7Up3UHmSucZBEBqKx67kkQj6hjVH14FyFlGmmhByQI0
9M8Y2QKBgQDYDhCCQQhTrhiBAb7cfselIIQrMpCqYZEXDG4neB87MWJL03smT8u7
tyIQ5bD4/rjf0O+A6KfSujpfJ08Z+A5aSvPfg8un4JTSBakYBHyCnVqN7JipfXDR
NbMPkyIKz6rG/wH9RnRY4pIqlIaWB9ZosEuq4Gqyo1QFm8xefiKPVQKBgQCQ9puP
eFLiPRECvr8+lubdj6DrpWZNhmDEjbJXE8XltNzAYhjZYog4yfO/GAlJ7RFql+DH
mYIwrVRBuFkEAIpH6zGPVGXkzxoKfRY3udZ/LmkDCsp4FXWwDSAt97IlWjZjXHjG
0p8Wn1WCjgpUBrub/MaX7xEVZl0Q78Kg+2UHKwKBgQCHT/+s5Dfn2LBGE6bKh6hX
1c6RE2EhJGvvKHzQwV2l/97VKIUHUJCDZ0WxmXlF6Jo2qq9lZ9C7nKC0OXVECWRL
zoeAJBXndTMfeiYWAbFNSGmPW2+dLw/6JwyXI4n9hWQqr0k6q+ZwSK5Mdqr3yb7R
4B/zzAtnB/22aYYMZCkW5QKBgHzrNhRFMM9qpi0CaNUrvphw+8/1ARqPUPYv0N3+
2QtgdtaF8jnqEc82IIis0txUoSyE4pu+KhV+V9wmWvb67C6be2Ky7PdmjF87eZjd
2PxR5wZLFzyx6W+jb/aMu4Q6oCGxfxZ+S7934H0Xn8jW4HbENfKZfdQ+GLbOlZ6Z
5G5vAoGAc/B3POByNl3tVHAwlI1njGzv+aogMrQX92b0vlNZcbM0t+3CF52T7QtG
UKi+CLySwkaGECpPoKvWKOlCJSBWxcz60QIupvxzVCYeIPWkq0TV93707MKSSdrV
hRYIh7EXh4wxSeR+GoNsmUlrL/YROQ2Y0wR/5JtMrxxhCpQIc0g=
-----END RSA PRIVATE KEY-----';
        
        $cipher_text = base64_decode($responseHeaderKey);
        openssl_private_decrypt($cipher_text, $symmetric_key, $private_key, OPENSSL_PKCS1_PADDING);
        
        
        
        $encrypt_text = base64_decode($base64_text); // Convert the base64 encoded ciphertext to binary
        $method = '';
        if (strlen($symmetric_key) == 16) {
          $method = 'aes-128-gcm';
        } else if (strlen($symmetric_key) == 24) {
          $method = 'aes-192-gcm';
        } else if (strlen($symmetric_key) == 32) {
          $method = 'aes-256-gcm';
        }
        
        //$method = 'aes-256-gcm';
        
        
        $plain_text = openssl_decrypt(substr($encrypt_text, 0, -16), $method, $symmetric_key, OPENSSL_NO_PADDING, $responseHeaderIV, substr($encrypt_text, -16), '');

        echo $plain_text;
        die;
        //ERROR RESPONSE
        /*{"status":"ERROR","responseCode":"PS017","responseMessage":"Settlement account name/PAN name doesn't match with PAN/TAN details"}*/
        //SUCCESS RESPONSE
        /*{"status":"SUCCESS","responseCode":"00","responseMessage":"Seller has been added successfully","partnerReferenceNumber":"17279480771718","ypHubUsername":"P11S.PAYOL123","sellerIdentifier":"PAYOL123","settlementAccountId":"40974","dueDiligenceStatus":"ODD","monthlyCollectionLimit":"66666.67","ecollectAccountNumber":"4352220400003122"}*/
        
    }
    
    public function yesBapaUpiCallBack()
    {
        $account_id = $this->User->get_domain_account();
         
        // save system log
        log_message('debug', 'Yes Bapa Callback Called.');
        // save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Yesbank Bapa Callback API Called.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
        // get header data
        $header_data = apache_request_headers();
        log_message('debug', 'Yes Bapa Callback Header Json Data - '.json_encode($header_data));
        
        #$header_data = '{"X-Https":"1","Accept-Encoding":"gzip,deflate","User-Agent":"Apache-HttpClient\/4.5.9 (Java\/11.0.9.1)","Connection":"Keep-Alive","Host":"www.payol.in","Content-Length":"703","Content-Type":"application\/json","Callbacktype":"Tmdfi9y7J9T2JBIZT7VED+xOKeTQDcNV3EG99zHuT8z1hRpcu3JRttp5QzDA6OmmD02DBpYe7mGGiMtfOGZQAPdvowsgjGwp+EOgIpwa4wyoO99WBQJL383sWjW78UVlKJIR0pS9bsxgfYK5Lrq6z9\/x8r3wCm\/SYzw\/baiOinYSfNygglTjv1cn4PSJLiTunQAzzUXgLfSx4uNBL5ELNdcM4tTgypTfp0uHGIBTllserkLDNzwac0cljie5AUOxXI1MxQSuaFP7DhGdc9q3Xv2QjjMnjVwWKgNoWwxEIbE9BftgVIagy6E4iW5RQv\/jQ+ME5DzBT08JCx9y5MUPHQ==","Hash":"hyUTIzWK2wrsJCqJlu2+geC2YJXcXoFbRn1A54OWeMHMS8WUO78OuUadYVthwMzBljJ7k437EqqQn4Szb8EpiLr8e2YIUfxKOkaaNKVA8AxSi+mNolIpRTZpLQHx3B9kKReXKRlHQWlVX0S5SV1oay6QAQFcdsPiykbhPIeYOFl\/ghxNd7\/yoyjNzxa5Ofh+qQMJg53hdGjVjN8BejnFktYVj7gtsiW6yW9zC64PEiyt1+HiO1Y+ujpavRyrLCXsuFBi5cFl\/PZ1Af8ls3Uc9vZ3Mny2mEDcWjx8pFGLqHp+HCMjPIZ+z72bslb428rZvtZ7x3ldKo6yhkkHq9G3ZA==","Key":"AmCI\/nQFYiE1dk0xe1ShRJccK26U9Fm90PU9fhp1XAsat0sMWmtWccEHLVlgCw1VdD26+1SOomlrUuq6ypYLL0aZeh2lFtPtPcqHE8H9\/+fTCeSASF3X8VnjrJruvC2WqiA47vOn4hepD5JdFeZe0F3sT4FPZncK4X62mnPM4TZ3F4yYUmEnGf7DgBuAF97DpoVLQ6eODFWaFwjPTQLQ2G74cVwamXD60tE09AMOhWTVbLW8wDsmYhW37Y1DZj1KV6PKezZaFmKpbcOfGjtkt7Z92N9CdVVN1DKh1F77ob1B357pLXQ1O5us87nRmR3Elo8gkYRwsdURLvmpYD2qBA==","Iv":"3773551590972474"}';
        $header_data = json_decode($header_data,true);
    	$callbackData = file_get_contents('php://input'); 
    	/*$callbackData = '{"body":"dirZB59KXxTgsDRmNd4S2Ajl9apcV3ma1U2pEynIin8Y6QvYeDJhVL/QIHbNKER/gb2MY4SDXhQofsQAKsIMDdJV2RD8YiPmqNJq3Yv3SErxHM9KiRQ3n+8okm2EoMF2wqM2aWKTO9AMMYzLCGdy/xlRIzfjLJLlr+n7AI6z3JkCmQ1I0SzeSydY0srH1PR1y/LGFjzp9/5DcK/aW/JFs6wpsFlMA3lYnQJX0I1xbX3oyuTmcmpCLESNfW7iC8q0fVPQFqCLkwuK6oFSoiytfpatU2lHh+XzWPcoDNkKUwSy+55H4aQRzw37F2KEGfCJ0XLLKqDA1h3O8j1jRMAelVdkDrcaH/nnv5X37aTCpoHFOZcYqTSwFMIheyWqPDSao0q9YfiLZlKxlZKN4lJSL4m46q6gXCnt6jqmCqdjY5skNvbU17F9AHEsmLARKheHTb+pnet8be/f7WyOP9Uc3FWKPsm2Fyo23Sbx3FMfSVz1Ig0pLMbLsuzeTlyB9Vl6sy9Ly1xRDISJfUbJpiaje8bp6UIQitMhiiMXH8K9BJxJRhGCNgxK+E+zQIryNgiyWKsaFXdRjzNuPOJert4l60wM5n0F+l4z5RsNp3wRat0rWVhDLRfh26KcFxYBeUVzNqYYLcGK+c7Gcf9nb1lihNTqlGbNzxgk2emlJHswdgk9KL1BNCk="}';*/
        log_message('debug', 'Yes Bapa Callback Data - '.$callbackData);
        $decodeResponse = json_decode($callbackData);
        
        $responseHeaderHash = $header_data['Hash'];
        $responseHeaderKey = $header_data['Key'];
        $responseHeaderIV = $header_data['Iv'];
        $responseHeaderCallbackType = $header_data['Callbacktype'];
        
        $decodeOutput = json_decode($callbackData,true);
        $base64_text = isset($decodeOutput['body']) ? $decodeOutput['body'] : '';
        
        
        
        // decrypt process
        $private_key = '-----BEGIN RSA PRIVATE KEY-----
MIIEogIBAAKCAQB6WAoWuyXOYwHiLJC2kX+nLAGGqxHUNYO+OceoKv+dCb6MUCh8
oopqNH0PK48EvwqcCwfvdEXfkuisuKgGudOqkQ1wyTN6RWwDN4dF4OKTGdZGxTXg
jyoQNVqoFhD17G35/lULvaOdEGUzSTehiGm0g5z68nURiDeeJCGO6R8FJN9nDff6
K2H0IUofZsykU89vjlTW2A88DznSADg4M3ZlKZ9LTMfWQh2P7BxP2CC6yUYNbdDy
QLtFhNNDMa1rWe7w36WnxCrsQAcDZfi01Fh/TccvM6cacIpdf8Zi62iFr9OIMDW6
owx+7HjR2bpm3OnAdEuC5Rrk26FIjp0FQmZHAgMBAAECggEAMv3Mvqqj2EB1wYHX
kvBTpyZ1QF6+oJbpYPiOGa5KOXadP7gNF91bGWblFNSP2GeTkXQbi5cHpGaDqbGQ
/rSm3vl0A6UoWnbizPhw3hQb+zmSkI6TpROmcXj7XwFLcmRndgGmCq2wqr7xnw2O
LTpA7GbKn+E8H+GJZNUkU38gWbDtH5Y0NKFPKTG0MUyE7LJCqajAmSHR6lIPOfR6
W7GOesZkJXKNoEQcQr3Ys4UVNLgbxAGQoz8kxVNerxPfgPHF/DGutJckcpQChfpw
g8VmkJAzhtHXRaWno8Fzi7Up3UHmSucZBEBqKx67kkQj6hjVH14FyFlGmmhByQI0
9M8Y2QKBgQDYDhCCQQhTrhiBAb7cfselIIQrMpCqYZEXDG4neB87MWJL03smT8u7
tyIQ5bD4/rjf0O+A6KfSujpfJ08Z+A5aSvPfg8un4JTSBakYBHyCnVqN7JipfXDR
NbMPkyIKz6rG/wH9RnRY4pIqlIaWB9ZosEuq4Gqyo1QFm8xefiKPVQKBgQCQ9puP
eFLiPRECvr8+lubdj6DrpWZNhmDEjbJXE8XltNzAYhjZYog4yfO/GAlJ7RFql+DH
mYIwrVRBuFkEAIpH6zGPVGXkzxoKfRY3udZ/LmkDCsp4FXWwDSAt97IlWjZjXHjG
0p8Wn1WCjgpUBrub/MaX7xEVZl0Q78Kg+2UHKwKBgQCHT/+s5Dfn2LBGE6bKh6hX
1c6RE2EhJGvvKHzQwV2l/97VKIUHUJCDZ0WxmXlF6Jo2qq9lZ9C7nKC0OXVECWRL
zoeAJBXndTMfeiYWAbFNSGmPW2+dLw/6JwyXI4n9hWQqr0k6q+ZwSK5Mdqr3yb7R
4B/zzAtnB/22aYYMZCkW5QKBgHzrNhRFMM9qpi0CaNUrvphw+8/1ARqPUPYv0N3+
2QtgdtaF8jnqEc82IIis0txUoSyE4pu+KhV+V9wmWvb67C6be2Ky7PdmjF87eZjd
2PxR5wZLFzyx6W+jb/aMu4Q6oCGxfxZ+S7934H0Xn8jW4HbENfKZfdQ+GLbOlZ6Z
5G5vAoGAc/B3POByNl3tVHAwlI1njGzv+aogMrQX92b0vlNZcbM0t+3CF52T7QtG
UKi+CLySwkaGECpPoKvWKOlCJSBWxcz60QIupvxzVCYeIPWkq0TV93707MKSSdrV
hRYIh7EXh4wxSeR+GoNsmUlrL/YROQ2Y0wR/5JtMrxxhCpQIc0g=
-----END RSA PRIVATE KEY-----';
        
        $cipher_text = base64_decode($responseHeaderKey);
        openssl_private_decrypt($cipher_text, $symmetric_key, $private_key, OPENSSL_PKCS1_PADDING);
        
        $cipher_text2 = base64_decode($responseHeaderCallbackType);
        openssl_private_decrypt($cipher_text2, $callbackTypeRes, $private_key, OPENSSL_PKCS1_PADDING);
        
        // decode callback type response
        //UPI_RESOLUTION
        
        
        
        $encrypt_text = base64_decode($base64_text); // Convert the base64 encoded ciphertext to binary
        $method = '';
        if (strlen($symmetric_key) == 16) {
          $method = 'aes-128-gcm';
        } else if (strlen($symmetric_key) == 24) {
          $method = 'aes-192-gcm';
        } else if (strlen($symmetric_key) == 32) {
          $method = 'aes-256-gcm';
        }
        
        //$method = 'aes-256-gcm';
        
        
        $plain_text = openssl_decrypt(substr($encrypt_text, 0, -16), $method, $symmetric_key, OPENSSL_NO_PADDING, $responseHeaderIV, substr($encrypt_text, -16), '');

        echo $plain_text;
        // SUCCESS RESPONSE
        /*{"gatewayResponseStatus":"SUCCESS","amount":30,"gatewayReferenceId":"428173254743","payeeVPA":"jptest.p11spayol123@yestransact","gatewayResponseCode":"00","payerVPA":"9967117192@ypay","type":"MERCHANT_CREDITED_VIA_PAY","transactionTimestamp":"2024-10-07T18:34:07","gatewayTransactionId":"MUL7159b0b2a8284fc0854bd28fa97c2329","yppReferenceNumber":"24100718PSJPY000267","payerName":"ABC","merchantRequestId":"MUL7159b0b2a8284fc0854bd28fa97c2329","gatewayResponseMessage":"Your transaction is successful"}*/
        die;
        
    }
	
	
// 	  public function yesManualUpiCallBack()
//     {
//         $account_id = $this->User->get_domain_account();
         
//         // save system log
//         log_message('debug', 'Yes UPI Callback Called.');
//         // save system log
//         $log_msg = '['.date('d-m-Y H:i:s').' - Yesbank UPI Callback API Called.]'.PHP_EOL;
//         $this->User->generateUpiCollectionLog($log_msg);
//     	//$callbackData = file_get_contents('php://input'); 
//         $callbackData = '1399379B13B142A431FDBB403C31D915DCBD2D0CA007E14B6017A2473C1F75119DBE61E76A2C5B39A992BA2A7589CD7AE5ECD088D7A5FCEF6AA8C18E2705F4545C72B2F83D5643AA1E14064A0152A0EE5B07DDAC741241341B710902B425F28DA7C7D1475F1E20A313339C6A5E4045FDC675AF3564C46E5EE0BDD6D47ACCB6280539F540E9DC0E4A1E0CF56132E23853B715B48C154E5A537B4B9A2379E847739504149CD7DA09E4632DD0D92F373370320AC330D77D3D7CA0222433C4139BECB8F6E87228081193708DFF968BC897EDC7B99493BE156334572387F1361CF08F2C1EC5483BB62F8A0B638C7059D9AA24037BED52F608509126DF61DEB49FBA894E46A0F8C72B876EC8FDF0AEC1659D527C09FFED92AE328F3BAB21483CB8AE51265C4740C200813CAF421AADEE5A9CB9BFEB2C5B1AF50DA5E2DDF2F491A1E085A30C348FEAE5F49D335753466E090B65';
//     	log_message('debug', 'Yes UPI Callback Data - '.$callbackData);
    	
    	
//         // $callbackDataExplode = explode('=',$callbackData);
//         // $callbackStr = isset($callbackDataExplode[1]) ? $callbackDataExplode[1] : '';        
//         // log_message('debug', 'Yes UPI Callback Str - '.$callbackStr);
//         //UAT KEY
//         #$enckey = '0eecc43f46ac1db51c40607cb355b22c';
//         //LIVE KEY
//         $enckey = '7153f272dbdc71b459c6b49551988767';
//         $decodeResponse = $this->yesDecryptValue($callbackData,$enckey);
        
        
//         log_message('debug', 'Yes UPI Callback Json - '.$decodeResponse);
//         // save system log
//         $log_msg = '['.date('d-m-Y H:i:s').' - Yesbank UPI Callback Decrypt Data - '.$decodeResponse.'.]'.PHP_EOL;
//         $this->User->generateUpiCollectionLog($log_msg);
        
        
//         /*6224691207|671617|PAYMENT_RECV|10.00|2023:11:28 12:46:09|S|Transaction success|00|NA|769316|8104758957@ybl|YBL9b03ba0951474548b574b09b1bdf04fe|NA|NA|NA|PAY TO FSS|333292502159|XXXXXX6776|ICIC0000235|SONU JANGID|payol@yesbank|YESB0000253|XXXXXX0220|NA|PAYOL DIGITAL TECHNOLOGIES PVT LTD|0|NA|SAVINGS|NA|NA|NA|NA|NA|NA|NA*/

//         /*26132209|lksvgybhji|PAYMENT_RECV|10.00|2023:09:29 15:48:14|S|Transaction success|00|NA|693755|7208865023@yesb|YESB067DB704A76F6426E06400144FFB2B9|NA|NA|NA|PAY TO FSS|327210764662|XXXXXX4104|YESB0000419|MAHADEV NAVGIRE|payol@yesb|YESB0000007|XXXXXX0585|NA|Payol Digital|0|NA|SAVINGS|NA|NA|NA|NA|NA|NA|NA*/
        
//         $decodeResponseData = explode('|', $decodeResponse);
        
        
        
//         if(isset($decodeResponseData[5]) && $decodeResponseData[5] == 'Transaction success')
//         {
//             $txnid = $decodeResponseData[1];
//             $bank_rrno = $decodeResponseData[11];
//             $PayerAmount = $decodeResponseData[2];
//             $PayerVA = $decodeResponseData[8];
//             $PayerName = $decodeResponseData[14];

//             $TxnInitDate = date('Y-m-d H:i:s');
//             $TxnCompletionDate = date('Y-m-d H:i:s');
//             // save system log
//             $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Yesbank UPI Callback Success status found.]'.PHP_EOL;
//             $this->User->generateUpiCollectionLog($log_msg);

//             $chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->num_rows();
//             if($chk_dynamic_qr)
//             {
                
//                 $log_msg = '['.date('d-m-Y H:i:s').' - Yesbank UPI Callback Txn Found in Dynamic QR.]'.PHP_EOL;
//                 $this->User->generateUpiCollectionLog($log_msg);
                
//                 $chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->row_array();
//                 $member_id = isset($chk_dynamic_qr['member_id']) ? $chk_dynamic_qr['member_id'] : 0 ;
//                 $account_id = isset($chk_dynamic_qr['account_id']) ? $chk_dynamic_qr['account_id'] : 0 ;
//                 $is_add_fund = isset($chk_dynamic_qr['is_add_fund']) ? $chk_dynamic_qr['is_add_fund'] : 0 ;

//                 $member_role_id = $this->User->getMemberRoleID($member_id);
                
//                 $callStoreProc = "CALL Upicallback(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//                 $queryData = array('account_id' => $account_id, 'member_id' => $member_id, 'txnid' => $txnid, 'amount'=>$PayerAmount, 'bankRRN' => $bank_rrno, 'payerVA' => $PayerVA, 'member_role_id'=>$member_role_id,'api_id'=>2,'is_callback'=>1,'is_add_fund'=>$is_add_fund);
//                 $procQuery = $this->db->query($callStoreProc, $queryData);
//                 $procResponse = $procQuery->row_array();

//                 //add this two line 
//                 $procQuery->next_result(); 
//                 $procQuery->free_result(); 

//                 $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API SP Response - '.json_encode($procResponse).'.]'.PHP_EOL;
//                 $this->User->generateUpiCollectionLog($log_msg);

//                 if(isset($procResponse['msg']) && $procResponse['msg'] == 'SUCCESS')
//                 {
//                     $user_role_id = $procResponse['role_id'];
//                     $api_member_code = $procResponse['user_code'];
//                     $recordID = $procResponse['recordID'];
                    
//                     $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API Member Role ID - '.$user_role_id.']'.PHP_EOL;
//                     $this->User->generateUpiCollectionLog($log_msg);
    
//                     if($user_role_id == 6)
//                     {
//                         $user_call_back_url = $procResponse['upi_call_back_url'];
    
//                         $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
//                         $this->User->generateUpiCollectionLog($log_msg);
    
//                         $api_post_data = array();
//                         $api_post_data['status'] = 200;
//                         $api_post_data['payerAmount'] = $PayerAmount;
//                         $api_post_data['payerName'] = $PayerName;
//                         $api_post_data['txnID'] = $txnid;
//                         $api_post_data['BankRRN'] = $bank_rrno;
//                         $api_post_data['payerVA'] = $PayerVA;
//                         $api_post_data['TxnInitDate'] = $TxnInitDate;
//                         $api_post_data['TxnCompletionDate'] = $TxnCompletionDate;
                        
                        
//                         $header = [
//                             'Content-type: application/json'
//                         ];
                        
//                         $ch = curl_init();
//                         curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
//                         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//                         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//                         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
//                         curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//                         curl_setopt($ch, CURLOPT_POST, true);
//                         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//                         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));      
//                         $output = curl_exec($ch); 
//                         $error_msg = '';
//                         if (curl_errno($ch)) {
//                             $error_msg = curl_error($ch);
//                         }
//                         curl_close($ch);
                        
                        
//                         $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Yesbank UPI Callback API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
//                         $this->User->generateUpiCollectionLog($log_msg);
                        
//                         $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Yesbank UPI Callback API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
//                         $this->User->generateUpiCollectionLog($log_msg);
    
//                         $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - Yesbank UPI Callback API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
//                         $this->User->generateUpiCollectionLog($log_msg);
                        
//                     }
                    
//                     // distribut referral commision
//                     $recordList = $this->db->query("SELECT * FROM tbl_referral_commision WHERE from_member_id = '$member_id' AND account_id = '$account_id' AND service_id = 5 AND start_range <= $PayerAmount AND end_range >= $PayerAmount")->result_array();
//                     if($recordList)
//                     {
//                         foreach($recordList as $rList)
//                         {
//                             $to_member_id = $rList['to_member_id'];
//                             $commission = $rList['commission'];
//                             $is_flat = $rList['is_flat'];
//                             $is_surcharge = $rList['is_surcharge'];
    
//                             $comission = round(($commission/100)*$PayerAmount,2);
//                             if($is_flat)
//                             {
//                                 $comission = $commission;
//                             }
    
//                             $referralData = array(
//                                 'account_id' => $account_id,
//                                 'from_member_id' => $member_id,
//                                 'to_member_id' => $to_member_id,
//                                 'record_id' => $recordID,
//                                 'txnid' => $txnid,
//                                 'service_id' => 5,
//                                 'amount' => $PayerAmount,
//                                 'comission' => $comission,
//                                 'created' => date('Y-m-d H:i:s')
//                             );
//                             $this->db->insert('member_referral_comission',$referralData);
//                         }
//                     }
//                 }
                
                
                
//             }
            
//         }
//         else
//         {
//             $log_msg = '['.date('d-m-Y H:i:s').' - Yesbank UPI Callback API Failed Status Updated.]'.PHP_EOL;
//             $this->User->generateUpiCollectionLog($log_msg);
            
//         }    
        
//     }


    public function manualPayoutClear()
    {
        $domain_account_id = $this->User->get_domain_account();
        
        $manual_list = "SELECT * FROM `tbl_open_money_api_response` WHERE api_response LIKE '%error code: 1015%' AND api_url LIKE'%https://api.zwitch.io/v1/transfers%' AND DATE(created) > '2024-10-01' AND is_manual_clear = 0 ORDER BY id ASC LIMIT 20";
        $txn_list = $this->db->query($manual_list)->result_array();
        
        // echo "<pre>";
        // print_r($txn_list);
        // die;
        if($txn_list)
        {
            foreach($txn_list as $list)
            {
                
                    $post_data = json_decode($list['post_data'],true) ;
                    $merchant_reference_id = $post_data['merchant_reference_id'];
                    
        
                $chk_txn = $this->db->get_where('open_money_payout',array('transaction_id'=>$merchant_reference_id,'status'=>2))->num_rows();
                
                if($chk_txn)
                {
                    $get_recharge_data = $this->db->get_where('open_money_payout',array('transaction_id'=>$merchant_reference_id,'status'=>2))->row_array();
                    $account_id = isset($get_recharge_data['account_id']) ? $get_recharge_data['account_id'] : 0 ;
                    $member_id = isset($get_recharge_data['user_id']) ? $get_recharge_data['user_id'] : 0 ;
                    $amount = isset($get_recharge_data['transfer_amount']) ? $get_recharge_data['transfer_amount'] : 0 ;
                    $total_wallet_charge = isset($get_recharge_data['total_wallet_charge']) ? $get_recharge_data['total_wallet_charge'] : 0 ;
                    $transaction_id = isset($get_recharge_data['transaction_id']) ? $get_recharge_data['transaction_id'] : '' ;
                    $fundData = array(
                        'is_manual_clear'=>1,
                        'status' => 4,
                        'updated' => date('Y-m-d H:i:s')
                    );
                    $this->db->where('transaction_id',$transaction_id);
                    $this->db->where('status',2);
                    $this->db->update('open_money_payout',$fundData);
                    
                    if($member_id)
                    {
                        $chk_before_balance = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
    
                        $before_balance = $this->User->getMemberWalletBalanceSP($member_id);
                        
                        $after_balance = $before_balance + $total_wallet_charge;    
                        $member_code = $chk_before_balance['user_code'];    
    
                        $wallet_data = array(
                            'account_id'          => $account_id,
                            'member_id'           => $member_id,    
                            'before_balance'      => $before_balance,
                            'amount'              => $total_wallet_charge,  
                            'after_balance'       => $after_balance,      
                            'status'              => 1,
                            'type'                => 1,  
                            'wallet_type'         => 1,          
                            'created'             => date('Y-m-d H:i:s'),      
                            'credited_by'         => 1,
                            'description'         => 'Payout #'.$transaction_id.' Refund Credited'
                        );
    
                        $this->db->insert('member_wallet',$wallet_data);
    
                        
                        // get account role id
                        $get_role_id = $this->db->select('role_id,open_payout_call_back_url,dmt_call_back_url,user_code')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
                        $user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
                        $api_member_code = isset($get_role_id['user_code']) ? $get_role_id['user_code'] : 0 ;
                        if($user_role_id == 6)
                        {
                            $user_call_back_url = isset($get_role_id['open_payout_call_back_url']) ? $get_role_id['open_payout_call_back_url'] : '' ;
                            $rrn = '';
                                
                              $user_callback_data_url  = $user_call_back_url.'?status=FAILED&txnid='.$transaction_id.'&optxid='.$rrn.'&amount='.$amount.'&rrn='.$rrn;
                                 
                          
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $user_callback_data_url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                                $output = curl_exec($ch); 
                                curl_close($ch);
    
                            
                            
                        }
                    }
                }
                $this->db->where('id',$list['id']);
                $this->db->update('open_money_api_response',array('is_manual_clear'=>1));
                
                
            }
        }
        
        die('success');
        
        
        
    }
    
    
    public function checkOpenPayoutStatus()
    {
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.zwitch.io/v1/transfers/tr_mtY4wooXhbGi7fVNRFKjPLOTU',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ak_live_bq0SO69ZdaATI2dabwpeuF7GPfWw09XAIsOP:sk_live_L0TiS0BSbJVeMR6oiEYJ16zc49bfQErxuMai'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        echo $response;
        
    }
    
     public function collectpayUpiCallBack()
    {
    	// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - UPI Callback API Called.]'.PHP_EOL;
        $this->User->generateUpiCollectionLog($log_msg);
    	log_message('debug', 'Collect UPI Callback Called.');
    	$callbackData = $this->input->get();
    	log_message('debug', 'Collect UPI Callback Data - '.json_encode($callbackData));

    	$decodeResponse = $callbackData;

        if(isset($decodeResponse['Status']) && $decodeResponse['Status'] == 'success')
        {
        	
        	$txnid = $decodeResponse['AgentTrasID'];
        	$bank_rrno = $decodeResponse['Bankrrn'];
        	$PayerAmount = $decodeResponse['Amount'];
        	$PayerVA = $decodeResponse['payerVA'];
        	$PayerName = $decodeResponse['AccountName'];
        	$TxnInitDate =  date('Y-m-d H:i:s');
        	$TxnCompletionDate =  date('Y-m-d H:i:s');
        	// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback Success status found.]'.PHP_EOL;
	        $this->User->generateUpiCollectionLog($log_msg);

        	$chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->num_rows();
        	if($chk_dynamic_qr)
        	{
        		
        		$log_msg = '['.date('d-m-Y H:i:s').' - UPI Callback Txn Found in Dynamic QR.]'.PHP_EOL;
        		$this->User->generateUpiCollectionLog($log_msg);
        		
	        	$chk_dynamic_qr = $this->db->get_where('upi_dynamic_qr',array('txnid'=>$txnid))->row_array();
	        	$member_id = isset($chk_dynamic_qr['member_id']) ? $chk_dynamic_qr['member_id'] : 0 ;
	        	$account_id = isset($chk_dynamic_qr['account_id']) ? $chk_dynamic_qr['account_id'] : 0 ;
                 $is_add_fund = isset($chk_dynamic_qr['is_add_fund']) ? $chk_dynamic_qr['is_add_fund'] : 0 ;

	        	$member_role_id = $this->User->getMemberRoleID($member_id);
	        	
        		$callStoreProc = "CALL Upicallback(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $queryData = array('account_id' => $account_id, 'member_id' => $member_id, 'txnid' => $txnid, 'amount'=>$PayerAmount, 'bankRRN' => $bank_rrno, 'payerVA' => $PayerVA, 'member_role_id'=>$member_role_id,'api_id'=>12,'is_callback'=>1,'is_add_fund'=>$is_add_fund);
                    $procQuery = $this->db->query($callStoreProc, $queryData);
                    $procResponse = $procQuery->row_array();

	            //add this two line 
	            $procQuery->next_result(); 
	            $procQuery->free_result(); 

	            $log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API SP Response - '.json_encode($procResponse).'.]'.PHP_EOL;
				$this->User->generateUpiCollectionLog($log_msg);

				if(isset($procResponse['msg']) && $procResponse['msg'] == 'SUCCESS')
	            {
					$user_role_id = $procResponse['role_id'];
    				$api_member_code = $procResponse['user_code'];
    				
    				$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API Member Role ID - '.$user_role_id.']'.PHP_EOL;
    		        $this->User->generateUpiCollectionLog($log_msg);
    
    				if($user_role_id == 6)
    				{
    					$user_call_back_url = $procResponse['upi_call_back_url'];
    
    					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back URL - '.$user_call_back_url.']'.PHP_EOL;
    		        	$this->User->generateUpiCollectionLog($log_msg);
    
    	        		$api_post_data = array();
    	        		$api_post_data['status'] = 200;
    	        		$api_post_data['payerAmount'] = $PayerAmount;
    	        		$api_post_data['payerName'] = $PayerName;
    	        		$api_post_data['txnID'] = $txnid;
    	        		$api_post_data['BankRRN'] = $bank_rrno;
    	        		$api_post_data['payerVA'] = $PayerVA;
    	        		$api_post_data['TxnInitDate'] = $TxnInitDate;
    	        		$api_post_data['TxnCompletionDate'] = $TxnCompletionDate;
    					

    	        		$header = [
                            'Content-type: application/json'
                        ];

    	        		$ch = curl_init();
    					curl_setopt($ch, CURLOPT_URL, $user_call_back_url);
    					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    					curl_setopt($ch, CURLOPT_POST, true);
    					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_post_data));		
    					$output = curl_exec($ch); 
    					$error_msg = '';
    			        if (curl_errno($ch)) {
    			            $error_msg = curl_error($ch);
    			        }
    					curl_close($ch);
    					
    					
    					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back cURL Error - '.$error_msg.']'.PHP_EOL;
    		        	$this->User->generateUpiCollectionLog($log_msg);
    					
    					$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back Post Data - '.json_encode($api_post_data).']'.PHP_EOL;
    		        	$this->User->generateUpiCollectionLog($log_msg);
    
    		        	$log_msg = '['.date('d-m-Y H:i:s').' - Txn ID #'.$txnid.' - UPI Callback API API Member - '.$api_member_code.' - Call Back Response - '.$output.']'.PHP_EOL;
    		        	$this->User->generateUpiCollectionLog($log_msg);
    					
    				}
	            }

	        	
        	}
        	
        }
      	
    }

    
    
   
	

}
