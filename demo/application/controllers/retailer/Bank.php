<?php 
class Bank extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkRetailerPermission();
        $this->lang->load('master/package', 'english');
        
    }

	
	public function verify()
    {
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'bank/verify',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('retailer/layout/column-1', $data);
		
    }

    

    // save member
	public function verifyAuth()
	{	
		$response = array();

		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
		//check for foem validation
		$post = $this->input->post();
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
		$this->form_validation->set_rules('account_number', 'Account No', 'required|xss_clean');
		$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
			
			$response = array(

				'status' => 0,
				'msg'    => validation_errors()

			);
		}
		else
		{	
			$chk_wallet_balance = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$loggedUser['id']))->row_array();

			$wallet_balance = isset($chk_wallet_balance['wallet_balance']) ? $chk_wallet_balance['wallet_balance'] : 0;

			$get_verification_charge = $this->db->get_where('tbl_dmr_account_verify_charge',array('account_id'=>$account_id,'package_id'=>$chk_wallet_balance['package_id']))->row_array();

			$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 

			$admin_id = $this->User->get_admin_id($account_id);
			
			$admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);
			

			//get admin verification charge

			$account_package_id = $this->User->get_account_package_id($account_id);

			$admin_charge = $this->db->get_where('tbl_dmr_account_verify_charge',array('package_id'=>$account_package_id))->row_array();

			$admin_verification_charge = isset($admin_charge['commision']) ? $admin_charge['commision'] : 0; 

			if($admin_wallet_balance < $admin_verification_charge){

				$response = array(

					'status' => 0,
					'msg'    => 'Sorry!! insufficient balance in your admin wallet.'

				);

			}
			else{

				$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
				if($wallet_balance < $verification_charge){

					$response = array(

						'status' => 0,
						'msg'    => 'Sorry!! you have insufficient balance in your wallet.'

					);
				}
				else{

					$transid = rand(111111,999999).time();
		            $name = isset($post['account_holder_name']) ? $post['account_holder_name'] : '';
		            $account_number = isset($post['account_number']) ? $post['account_number'] : '';
		            $ifsc = isset($post['ifsc']) ? $post['ifsc'] : '';
		            $bank_verification_url = BANK_VERIFICATION_URL;

		            $response = array();

		           $request = array(
		                
		               
    		                
	                        'payee' => array(
	                            
	                            'accountNumber' =>$account_number,
	                            'bankIfsc' =>$ifsc
	                       ),
	                     
	                       'externalRef' => $transid,
	                       'consent'    =>'Y',
	                       'isCached'  => 0,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       
		                
		            );




		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',		                
		                'content-type: application/json'
		                
		            );
		            
		         
		            $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $bank_verification_url);

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

		            $responseData = json_decode($output,true);

		            $response_data = $responseData['data']['payee'];
		            

		            if(isset($responseData) && $responseData['statuscode'] == "TXN" && $responseData['status'] == "Transaction Successful"){

		            	$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
		            	$after_balance = $wallet_balance - $verification_charge;

		                $history = array(

		                   'account_id' => $account_id,
		                   'member_id'  => $loggedUser['id'],
		                   'api_url'    => $bank_verification_url,
		                   'post_data'	=> json_encode($post),
		                   'api_response' => json_encode($responseData),
		                   'txn_id' => $transid,
		                   'before_balance' => $wallet_balance,
		                   'amount' => $verification_charge,
		                   'after_balance' => $after_balance, 
		                   'status' => 'Success',
		                   'created' => date('Y-m-d H:i:s') 
		                );

		                $this->db->insert('bank_verification',$history);

		                //wallet deduct

		                $wallet_data = array(
	                        'account_id'          => $account_id,
	                        'member_id'           => $loggedUser['id'],    
	                        'before_balance'      => $wallet_balance,
	                        'amount'              => $verification_charge,  
	                        'after_balance'       => $after_balance,      
	                        'status'              => 1,
	                        'type'                => 2,      
	                        'created'             => date('Y-m-d H:i:s'),      
	                        'description'         => 'Bank Account Verification #'.$transid.' Amount Deducted.'
	                    );

	                    $this->db->insert('member_wallet',$wallet_data);

	                   

	                    $str = '<table class="table table-bordered table-striped">
				          <tbody>
				            <tr>
				              <th>Account No.</th>
				              <td>'.$response_data['account'].'</td>
				            </tr>

				            <tr>
				              <th>Account Holder Name</th>
				              <td>'.$response_data['name'].'</td>
				            </tr>

				            <tr>
				              <th>Status</th>
				              <td><font color="green">'.$responseData['status'].'</font></td>
				            </tr>
				            
				          </tbody>
				        </table>';


		                $response = array(
		                  'status' => 1,
		                  'msg'=>$str,
		                  'account_holder_name'=>$response_data['name']
		                );
		            }
		            else{


		                $history = array(

		                   'account_id' => $account_id,
		                   'member_id'  => $loggedUser['id'],
		                   'api_url'    => $bank_verification_url,
		                   'post_data'	=> json_encode($post),
		                   'api_response' => json_encode($responseData),
		                   'txn_id' => $transid,
		                   'before_balance' => $wallet_balance,
		                   'amount' => 0,
		                   'after_balance' => $wallet_balance, 
		                   'status' => 'Failed',
		                   'created' => date('Y-m-d H:i:s') 
		                );

		                $this->db->insert('bank_verification',$history);

		                $response = array(
		                 'status' => 0,
		                 'msg'=>$response_data['status']    
		                );
		            }
		        }

			}
		}

		echo json_encode($response);
	
	}

	

	public function upiVerifyAuth()
	{	
		$response = array();

	    $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
		//check for foem validation
		$post = $this->input->post();
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
		$this->form_validation->set_rules('account_number', 'Account No', 'required|xss_clean');
		//$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
			
			$response = array(

				'status' => 0,
				'msg'    => validation_errors()

			);
		}
		else
		{	
			$chk_wallet_balance = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$loggedUser['id']))->row_array();

			$wallet_balance = isset($chk_wallet_balance['wallet_balance']) ? $chk_wallet_balance['wallet_balance'] : 0;

			$get_verification_charge = $this->db->get_where('tbl_dmr_account_verify_charge',array('account_id'=>$account_id,'package_id'=>$chk_wallet_balance['package_id']))->row_array();

			$verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0; 

			$admin_id = $this->User->get_admin_id($account_id);
			
			$admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);
			

			//get admin verification charge

			$account_package_id = $this->User->get_account_package_id($account_id);

			$admin_charge = $this->db->get_where('tbl_dmr_account_verify_charge',array('package_id'=>$account_package_id))->row_array();

			$admin_verification_charge = isset($admin_charge['commision']) ? $admin_charge['commision'] : 0; 

			if($admin_wallet_balance < $admin_verification_charge){

				$response = array(

					'status' => 0,
					'msg'    => 'Sorry!! insufficient balance in your admin wallet.'

				);

			}
			else{

				$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
				if($wallet_balance < $verification_charge){

					$response = array(

						'status' => 0,
						'msg'    => 'Sorry!! you have insufficient balance in your wallet.'

					);
				}
				else{

					$transid = rand(111111,999999).time();
		            $name = isset($post['account_holder_name']) ? $post['account_holder_name'] : '';
		            $account_number = isset($post['account_number']) ? $post['account_number'] : '';
		            //$ifsc = isset($post['ifsc']) ? $post['ifsc'] : '';
		            $bank_verification_url = BANK_VERIFICATION_URL;

		            $response = array();

		           $request = array(
		                
		               
    		                
	                        'payee' => array(
	                            
	                            'accountNumber' =>$account_number,
	                            'bankIfsc' =>0
	                       ),
	                     
	                       'externalRef' => 'PPT223',
	                       'consent'    =>'Y',
	                       'isCached'  => 0,
	                       'latitude' =>'22.9734229',
	                       'longitude' => '78.6568942',
	                       
		                
		            );




		            $header = array(
		                'X-Ipay-Auth-Code: 1',
		                'X-Ipay-Client-Id: '.$accountData['instant_client_id'],
		                'X-Ipay-Client-Secret: '.$accountData['instant_client_secret'],
		                'X-Ipay-Endpoint-Ip: 103.129.97.70',		                
		                'content-type: application/json'
		                
		            );
		            
		         
		            $curl = curl_init();
		            // URL
		            curl_setopt($curl, CURLOPT_URL, $bank_verification_url);

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

		            $responseData = json_decode($output,true);

		            $response_data = $responseData['data']['payee'];
		            

		            if(isset($responseData) && $responseData['statuscode'] == "TXN" && $responseData['status'] == "Transaction Successful"){

		            	$wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
		            	$after_balance = $wallet_balance - $verification_charge;

		                $history = array(

		                   'account_id' => $account_id,
		                   'member_id'  => $loggedUser['id'],
		                   'api_url'    => $bank_verification_url,
		                   'post_data'	=> json_encode($post),
		                   'api_response' => json_encode($responseData),
		                   'txn_id' => $transid,
		                   'before_balance' => $wallet_balance,
		                   'amount' => $verification_charge,
		                   'after_balance' => $after_balance, 
		                   'status' => 'Success',
		                   'created' => date('Y-m-d H:i:s') 
		                );

		                $this->db->insert('bank_verification',$history);

		                //wallet deduct

		                $wallet_data = array(
	                        'account_id'          => $account_id,
	                        'member_id'           => $loggedUser['id'],    
	                        'before_balance'      => $wallet_balance,
	                        'amount'              => $verification_charge,  
	                        'after_balance'       => $after_balance,      
	                        'status'              => 1,
	                        'type'                => 2,      
	                        'created'             => date('Y-m-d H:i:s'),      
	                        'description'         => 'UPI Account Verification #'.$transid.' Amount Deducted.'
	                    );

	                    $this->db->insert('member_wallet',$wallet_data);

	                    
	                    $str = '<table class="table table-bordered table-striped">
				          <tbody>
				            <tr>
				              <th>Account No.</th>
				              <td>'.$response_data['account'].'</td>
				            </tr>

				            <tr>
				              <th>Account Holder Name</th>
				              <td>'.$response_data['name'].'</td>
				            </tr>

				            <tr>
				              <th>Status</th>
				              <td><font color="green">'.$responseData['status'].'</font></td>
				            </tr>
				            
				          </tbody>
				        </table>';


		                $response = array(
		                  'status' => 1,
		                  'msg'=>$str,
		                  'account_holder_name'=>$response_data['name']
		                );
		            }
		            else{


		                $history = array(

		                   'account_id' => $account_id,
		                   'member_id'  => $loggedUser['id'],
		                   'api_url'    => $bank_verification_url,
		                   'post_data'	=> json_encode($post),
		                   'api_response' => json_encode($responseData),
		                   'txn_id' => $transid,
		                   'before_balance' => $wallet_balance,
		                   'amount' => 0,
		                   'after_balance' => $wallet_balance, 
		                   'status' => 'Failed',
		                   'created' => date('Y-m-d H:i:s') 
		                );

		                $this->db->insert('bank_verification',$history);

		                $response = array(
		                 'status' => 0,
		                 'msg'=>$responseData['status']    
		                );
		            }
		        }

			}
		}

		echo json_encode($response);
	
	}
	
	
}