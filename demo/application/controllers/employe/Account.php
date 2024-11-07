<?php 
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

class Account extends CI_Controller {   



    public function __construct() 
    {
		parent::__construct();
		$this->User->checkEmployePermission();
		$this->load->model('employe/Account_model');	
		$this->lang->load('employe/dashboard', 'english');
		$this->lang->load('front_common', 'english');
		
    }				

	public function accountList()
	{

		if(!$this->User->admin_menu_permission(22,1) || !$this->User->admin_menu_permission(101,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$user_ip_address = $this->User->get_user_ip();
		$list= $this->db->select('account_request.*')->order_by('account_request.created','desc')->get_where('account_request',array('account_request.account_id'=>$account_id))->result_array();

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Whitelable Account List Page Open.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'list'  =>$list,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/accountList'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}


	public function addAccount()
	{

		if(!$this->User->admin_menu_permission(22,1) || !$this->User->admin_menu_permission(100,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}


		$account_id = $this->User->get_domain_account();

		$user_ip_address = $this->User->get_user_ip();

		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Whitelable Account Add Page Open.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

		// get role list
		$siteUrl = site_url();
		$data = array(
			'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'content_block' => 'account/addAccount',
			'manager_description' => lang('SITE_NAME'),
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getSystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning() 
		);
		$this->parser->parse('employe/layout/column-1', $data);
	}



	public function saveAccount()
	{

		if(!$this->User->admin_menu_permission(22,1) || !$this->User->admin_menu_permission(100,2)){

			$this->Az->redirect('employe/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized to access this page.</div>');
		}

		$domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $user_ip_address = $this->User->get_user_ip();
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Whitelable Account Save Post Data - '.json_encode($post).'.]'.PHP_EOL;
        $this->User->generateAccountActivityLog($log_msg);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('domain_name', 'Domain Name', 'required');
		$this->form_validation->set_rules('domain_url', 'Domain Url ', 'required|xss_clean');
		$this->form_validation->set_rules('account_code', 'Prefix ', 'required|xss_clean');
		$this->form_validation->set_rules('name', 'Name ', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email ', 'required|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile ', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {

			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Whitelable Account Save Validation Error.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->addAccount();

		}

		else{

			

			$filePath = '';
			if($_FILES['profile']['name'])
			{
				// save system log
		        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Whitelable Account Save Uploaded File Data - '.json_encode($_FILES).'.]'.PHP_EOL;
		        $this->User->generateAccountActivityLog($log_msg);
				//generate icon name randomly
				$fileName = time().rand(1111,9999);
				$config['upload_path'] = './media/account_request/';
				$config['allowed_types'] = 'gif|jpeg|JPEG|JPG|PNG|jpg|png';
				$config['file_name'] 		= $fileName;

				$this->load->library('upload', $config);
				$this->upload->do_upload('profile');
				$uploadError = $this->upload->display_errors();
				if($uploadError){
					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Whitelable Account Save Uploaded File Error - '.$uploadError.'.]'.PHP_EOL;
			        $this->User->generateAccountActivityLog($log_msg);

					$this->Az->redirect('employe/account/addAccount', 'system_message_error',$uploadError);
				}
				else
				{
					$fileData = $this->upload->data();
					//get uploaded file path
					$filePath = substr($config['upload_path'] . $fileData['file_name'], 2);

					// save system log
			        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Whitelable Account Save Uploaded File Path - '.$filePath.'.]'.PHP_EOL;
			        $this->User->generateAccountActivityLog($log_msg);

				}

			}

			$this->Account_model->save_account($post,$filePath);

			// save system log
	        $log_msg = '['.date('d-m-Y H:i:s').' - Account - '.$domain_account_id.' - IP - '.$user_ip_address.' - User('.$loggedUser['user_code'].') - Whitelable Account Save Successfully Redirect back to account list page.]'.PHP_EOL;
	        $this->User->generateAccountActivityLog($log_msg);

			$this->Az->redirect('employe/account/accountList', 'system_message_error',lang('ACCOUNT_SAVE_SUCCESS'));



		}



	}


	//invoice management


	public function dynamicInvoice()
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'list'  =>$list,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/dynamic-invoice'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}


	public function getDynamicInvoiceList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            
        }
		
		$columns = array( 
		// datatable column index  => database column name
			4 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql =  "SELECT a.* FROM tbl_account_invoice as a where a.account_id = '$account_id' AND a.type = 1";;
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_account_invoice as a where a.account_id = '$account_id' AND a.type = 1";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.year LIKE '%".$keyword."%'";				
				$sql.=" OR a.type LIKE '%".$keyword."%' )";
			}

			
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
			
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				
				if($list['type'] == 1)
				{
					$nestedData[] = 'Dynamic';
				}
				else
				{
					$nestedData[] = 'Manual';
				}
				$nestedData[] = $list['year'];
				//$nestedData[] = $list['month'];
				if($list['month'])
				{
				    $m = $list['month'];

		        $nestedData[] = date('F', mktime(0,0,0,$m, 1, date('Y')));
		
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				$nestedData[] ='<a title="Invoice" class="btn btn-primary btn-sm" href="'.base_url('employe/account/invoiceSummery').'/'.$list['id'].'">Invoice</a>';

				
				
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					);

		echo json_encode($json_data);  // send data as json format
	}



	public function generateDynamicInvoice()
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$user_type = $this->db->where_in('role_id',array(3,4,5,6))->get('users')->result_array();
		
		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'user_type' =>$user_type,
			'loggedUser'  => $loggedUser,			
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/generate-dynamic-invoice'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}



	public function generateDynamicInvoiceAuth()
	{
		$domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
       
		//check for foem validation
		$post = $this->input->post();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('month', 'Month', 'required');
		$this->form_validation->set_rules('year', 'Year', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE) {

			$this->generateDynamicInvoice();

		}

		else{


				$chk_invoice_alreay = $this->db->get_where('account_invoice',array('account_id'=>$domain_account_id,'year'=>$post['year'],'month'=>$post['month']))->num_rows();

				if($chk_invoice_alreay)

				{
					$this->Az->redirect('employe/account/dynamicInvoice', 'system_message_error',lang('ACCOUNT_INVOICE_FAILED'));
				}
				
				if($post)


				{
					$user_type = $this->db->where_in('role_id',array(3,4,5,6))->get_where('users',array('account_id'=>$domain_account_id))->result_array();


					$year = $post['year'];
            		$month = $post['month'];
            		$type = 1;


            		$data = array(
            			'account_id' =>$domain_account_id,
            			'type' =>$type,
            			'month' =>$month,
            			'year' =>$year,
            			'created' => date('Y-m-d H:i:s')

            		);

            		$this->db->insert('account_invoice',$data); 
					$recordID = $this->db->insert_id();

					
					foreach ($user_type as  $member_id) {
							
							$userID = $member_id['id'];

					$getTotalRecharge = $this->db->select('SUM(amount) as totalAmount')->get_where('recharge_history',array('account_id'=>$domain_account_id,'member_id'=>$userID,'status'=>2,'MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();

					
                    $totalRechargeAmount = isset($getTotalRecharge['totalAmount']) ? $getTotalRecharge['totalAmount'] : 0 ;

                    // total recharge Amount
                    $getTotalRechargeCom = $this->db->select('SUM(settlement_amount) as totalAmount')->get_where('user_commision',array('account_id'=>$domain_account_id,'member_id'=>$userID,'type'=>'RECHARGE','MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();
                    $totalRechargeCom = isset($getTotalRechargeCom['totalAmount']) ? $getTotalRechargeCom['totalAmount'] : 0 ;

                    $getTotalPayoutAmount = $this->db->select('SUM(transfer_amount) as totalAmount,SUM(transfer_charge_amount) as totalChargeAmount')->get_where('user_new_fund_transfer',array('account_id'=>$domain_account_id,'user_id'=>$userID,'status'=>3,'MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();
                    $totalPayoutAmount = isset($getTotalPayoutAmount['totalAmount']) ? $getTotalPayoutAmount['totalAmount'] : 0 ;
                    $totalPayoutChargeAmount = isset($getTotalPayoutAmount['totalChargeAmount']) ? $getTotalPayoutAmount['totalChargeAmount'] : 0 ;



                    $getTotalOpenPayoutAmount = $this->db->select('SUM(transfer_amount) as totalAmount,SUM(transfer_charge_amount) as totalChargeAmount')->get_where('user_money_transfer',array('account_id'=>$domain_account_id,'user_id'=>$userID,'status'=>3,'MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();
                    $totalOpenPayoutAmount = isset($getTotalOpenPayoutAmount['totalAmount']) ? $getTotalPayoutAmount['totalAmount'] : 0 ;
                    $totalOpenPayoutChargeAmount = isset($getTotalOpenPayoutAmount['totalChargeAmount']) ? $getTotalPayoutAmount['totalChargeAmount'] : 0 ;


                    $getTotalOtherPayoutAmount = $this->db->select('SUM(transfer_amount) as totalAmount,SUM(transfer_charge_amount) as totalChargeAmount')->get_where('user_fund_transfer',array('account_id'=>$domain_account_id,'user_id'=>$userID,'status'=>3,'MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();
                    $totalOtherPayoutAmount = isset($getTotalOtherPayoutAmount['totalAmount']) ? $getTotalPayoutAmount['totalAmount'] : 0 ;
                    $totalOtherPayoutChargeAmount = isset($getTotalOtherPayoutAmount['totalChargeAmount']) ? $getTotalPayoutAmount['totalChargeAmount'] : 0 ;



                    $getAadharPayCharge = $this->db->select('SUM(wallet_settle_amount) as totalChargeAmount')->get_where('member_aeps_comm',array('account_id'=>$domain_account_id,'member_id'=>$userID,'is_surcharge'=>1,'MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();

                    $aadharPayCharge =isset($getAadharPayCharge['totalChargeAmount']) ? $getAadharPayCharge['totalChargeAmount'] : 0 ;


                    $final_payout_charge_amount = $totalPayoutChargeAmount + $totalOpenPayoutChargeAmount + $totalOtherPayoutChargeAmount + $aadharPayCharge;



                    $getTotalBbps = $this->db->select('SUM(amount) as totalAmount')->get_where('bbps_history',array('account_id'=>$domain_account_id,'member_id'=>$userID,'status'=>2,'MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();
					
					
                    $totalBbpsAmount = isset($getTotalBbps['totalAmount']) ? $getTotalBbps['totalAmount'] : 0 ;

                    // total recharge Amount
                    $getTotalBbpsCom = $this->db->select('SUM(settlement_amount) as totalAmount')->get_where('user_commision',array('account_id'=>$domain_account_id,'member_id'=>$userID,'type'=>'ELECTRICITY','MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();
                    $totalBbpsCom = isset($getTotalBbpsCom['totalAmount']) ? $getTotalBbpsCom['totalAmount'] : 0 ;



                    $getTotalAepsAmount = $this->db->select('SUM(amount) as totalAmount,SUM(wallet_settle_amount) as totalChargeAmount')->get_where('member_aeps_comm',array('account_id'=>$domain_account_id,'member_id'=>$userID,'is_surcharge'=>0,'MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();

                    $totalAepsAmount = isset($getTotalAepsAmount['totalAmount']) ? $getTotalAepsAmount['totalAmount'] : 0 ;
                    $totalAepsChargeAmount = isset($getTotalAepsAmount['totalChargeAmount']) ? $getTotalAepsAmount['totalChargeAmount'] : 0 ;


                    $user_invoice = 'MPINV'.rand(1111,9999);

                    	$data = array(	
                    	'account_id' =>$domain_account_id,
						'invoice_id' =>$recordID,
						'user_invoice' =>$user_invoice,
						'user_id' => $userID,
						'recharge_commission' => $totalRechargeCom,
						'recharge_amount' => $totalRechargeAmount,
						'payout_charge_amount'=>$final_payout_charge_amount,
						'bbps_amount' =>$totalBbpsAmount,
						'bbps_commission' =>$totalBbpsCom,
						'aeps_amount' =>$totalAepsAmount,
						'aeps_commission'=>$totalAepsChargeAmount,
												
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('account_invoice_summery',$data);

					}

					

				}


				$this->Az->redirect('employe/account/dynamicInvoice', 'system_message_error',lang('ACCOUNT_INVOICE_SUCCESS'));

		}



	}



	public function invoiceSummery($invoice_id = '')
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$chk_invoice = $this->db->get_where('account_invoice',array('account_id'=>$account_id,'id'=>$invoice_id))->num_rows();
		
		if(!$chk_invoice )

		{
			$this->Az->redirect('employe/account/dynamicInvoice', 'system_message_error',lang('INVOICE_ERROR'));
		}

		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'invoice_id'  => $invoice_id,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/invoice-summery'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}



	public function getDynamicInvoiceSummeryList()
	{	
		
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();


		$extra_search = $requestData['extra_search'];	

	   	$keyword = '';
	   	$invoice_id = '';
	   	
	   	
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
           	$invoice_id = isset($filterData[1]) ? trim($filterData[1]) : 0;

        }

        $firstLoad = 0;
		
		$columns = array( 
		// datatable column index  => database column name
			3 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql =  "SELECT a.* , b.user_code as user_code, b.name as name FROM tbl_account_invoice_summery as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.invoice_id = '$invoice_id'";


			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* , b.user_code as user_code, b.name as name FROM tbl_account_invoice_summery as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.invoice_id = '$invoice_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
					$sql.=" OR b.name LIKE '%".$keyword."%'";
							
				$sql.=" OR a.invoice_id LIKE '%".$keyword."%' )";
			}

			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
			

		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['user_invoice'];
				$nestedData[] = $list['user_code'].'<br>'.$list['name'];						
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				$nestedData[] ='<a title="Invoice" class="btn btn-primary btn-sm" href="'.base_url('employe/account/memberInvoice').'/'.$list['id'].'">Invoice</a>';

				
				
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					);

		echo json_encode($json_data);  // send data as json format
	}




	public function memberInvoice($id = '')
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);

		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$chk_invoice = $this->db->get_where('account_invoice_summery',array('account_id'=>$account_id,'id'=>$id))->num_rows();
		
		if(!$chk_invoice )

		{
			$this->Az->redirect('employe/account/dynamicInvoice', 'system_message_error',lang('INVOICE_ERROR'));
		}


		$invoice_data = $this->db->get_where('account_invoice_summery',array('account_id'=>$account_id,'id'=>$id))->row_array();

		$account_invoice_data = $this->db->get_where('account_invoice',array('account_id'=>$account_id,'id'=>$invoice_data['invoice_id']))->row_array();

		$m = $account_invoice_data['month'];

		$invoice_month = date('F', mktime(0,0,0,$m, 1, date('Y')));
		$invoice_year = $account_invoice_data['year'];

		



		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		

		$company_name = $accountData['title'];
		$company_email = $accountData['email'];
		$company_mobile = $accountData['mobile'];
		$company_logo = $accountData['image_path'];
		$company_address = $address['address'];


		$get_user_detail = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$invoice_data['user_id']))->row_array();

		$user_name = $get_user_detail['name'];
		$user_code = $get_user_detail['user_code'];
		$user_email = $get_user_detail['email'];
		$user_mobile = $get_user_detail['mobile'];
		$invoice_id = $invoice_data['user_invoice'];
		$invoice_date = $invoice_data['created'];

		//recharge
		$recharge_amount = isset($invoice_data['recharge_amount']) ? $invoice_data['recharge_amount'] : 0 ;
		$recharge_commission = isset($invoice_data['recharge_commission']) ? $invoice_data['recharge_commission'] : 0 ;
		$get_recharge_tax_amount = $recharge_amount - $recharge_commission;
		$recharge_taxable_amount =$get_recharge_tax_amount/1.18;
		$recharge_tax_amount = $get_recharge_tax_amount - $recharge_taxable_amount;

		//bbps

		$bbps_amount = isset($invoice_data['bbps_amount']) ? $invoice_data['bbps_amount'] : 0 ;
		$bbps_commission = isset($invoice_data['bbps_commission']) ? $invoice_data['bbps_commission'] : 0 ;
		$get_bbps_tax_amount = $bbps_amount - $bbps_commission;
		$bbps_taxable_amount =$get_bbps_tax_amount/1.18;
		$bbps_tax_amount = $get_bbps_tax_amount - $bbps_taxable_amount;

		//
		
		$payout_commission = isset($invoice_data['payout_charge_amount']) ? $invoice_data['payout_charge_amount'] : 0 ;
		//$get_recharge_tax_amount = $recharge_amount - $recharge_commission;
		$payout_taxable_amount =$payout_commission/1.18;
		$payout_tax_amount = $payout_commission - $payout_taxable_amount;


		$aeps_commission = isset($invoice_data['aeps_commission']) ? $invoice_data['aeps_commission'] : 0 ;
		
		$aeps_taxable_amount =$aeps_commission/1.18;
		$aeps_tax_amount = $aeps_commission - $aeps_taxable_amount;





		$total_taxable_amount = $recharge_taxable_amount + $bbps_taxable_amount + $payout_taxable_amount + $aeps_taxable_amount;

		$total_tax_amount = $recharge_tax_amount + $bbps_tax_amount + $payout_tax_amount + $aeps_tax_amount;


		$final_amount = $total_taxable_amount+ $total_tax_amount;



		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'company_name' =>$company_name,
			'company_email' =>$company_email,
			'company_mobile' =>$company_mobile,
			'company_logo' =>$company_logo,
			'user_name' =>$user_name,
			'user_email' =>$user_email,
			'user_mobile' =>$user_mobile,
			'invoice_id' =>$invoice_id,
			'invoice_date' =>$invoice_date,
			'company_address' =>$company_address,
			'invoice_month'	=>$invoice_month,
			'invoice_year'	=>$invoice_year,
			'user_code'		=>$user_code,
			'recharge_amount' =>$recharge_amount,
			'recharge_commission' =>$recharge_commission,
			'recharge_taxable_amount'=>$recharge_taxable_amount,
			'recharge_tax_amount' =>$recharge_tax_amount,
			'payout_commission' =>$payout_commission,
			'payout_taxable_amount' =>$payout_taxable_amount,
			'payout_tax_amount' =>$payout_tax_amount,
			'bbps_amount' =>$bbps_amount,
			'bbps_commission' =>$bbps_commission,
			'bbps_taxable_amount'=>$bbps_taxable_amount,
			'bbps_tax_amount' =>$bbps_tax_amount,
			'total_taxable_amount'=>$total_taxable_amount,
			'total_tax_amount' =>$total_tax_amount,
			'aeps_commission' =>$aeps_commission,
			'aeps_taxable_amount' =>$aeps_taxable_amount,
			'aeps_tax_amount' =>$aeps_tax_amount,
			'final_amount'  =>$final_amount,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/member-invoice'
		);
		$this->parser->parse('employe/layout/column-2' , $data);
	}


	public function generateTdsInvoice()
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$user_type = $this->db->where_in('role_id',array(3,4,5,6))->get('users')->result_array();
		
		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'user_type' =>$user_type,
			'loggedUser'  => $loggedUser,			
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/generate-tds-invoice'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}



	public function generateTdsInvoiceAuth()
	{
		$domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
       
		//check for foem validation
		$post = $this->input->post();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('month', 'Month', 'required');
		$this->form_validation->set_rules('year', 'Year', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE) {

			$this->generateTdsInvoice();

		}

		else{


				$chk_invoice_alreay = $this->db->get_where('tds_invoice',array('account_id'=>$domain_account_id,'year'=>$post['year'],'month'=>$post['month']))->num_rows();

				if($chk_invoice_alreay)

				{
					$this->Az->redirect('employe/account/tdsInvoice', 'system_message_error',lang('ACCOUNT_INVOICE_FAILED'));
				}
				
				if($post)


				{
					$user_type = $this->db->where_in('role_id',array(3,4,5,6))->get_where('users',array('account_id'=>$domain_account_id,'is_active'=>1))->result_array();


					$year = $post['year'];
            		$month = $post['month'];
            		$type = 1;


            		$data = array(
            			'account_id' =>$domain_account_id,
            			'type' =>$type,
            			'month' =>$month,
            			'year' =>$year,
            			'created' => date('Y-m-d H:i:s')

            		);

            		$this->db->insert('tds_invoice',$data); 
					$recordID = $this->db->insert_id();


					foreach ($user_type as  $member_id) {
							
							$userID = $member_id['id'];

					$getTotalTds = $this->db->select('SUM(tds_amount) as tdsAmount,SUM(com_amount) as commissionAmount')->get_where('tds_report',array('account_id'=>$domain_account_id,'member_id'=>$userID,'MONTH(created) ='=>$month,'YEAR(created)'=>$year))->row_array();
						

                    $totalTdsAmount = isset($getTotalTds['tdsAmount']) ? $getTotalTds['tdsAmount'] : 0 ;

                     $totalComAmount = isset($getTotalTds['tdsAmount']) ? $getTotalTds['commissionAmount'] : 0 ;

                    	 $user_invoice = 'PAYLTDS'.rand(1111,9999);

                    	$data = array(	
                    	'account_id' =>$domain_account_id,				
						'invoice_id' =>$recordID,
						'user_invoice' =>$user_invoice,
						'user_id' => $userID,
						'total_tds_amount' =>$totalTdsAmount,
						'total_com_amount' =>$totalComAmount,						
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('tds_invoice_summery',$data);
					}

				}

				$this->Az->redirect('employe/account/tdsInvoice', 'system_message_error',lang('ACCOUNT_INVOICE_SUCCESS'));

		}



	}



	public function tdsInvoice()
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'list'  =>$list,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/tds-invoice'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}


	public function getTdsInvoiceList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            
        }
		
		$columns = array( 
		// datatable column index  => database column name
			4 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql =  "SELECT a.* FROM tbl_tds_invoice as a where a.account_id = '$account_id'";;
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_tds_invoice as a where a.account_id = '$account_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.year LIKE '%".$keyword."%'";				
				$sql.=" OR a.type LIKE '%".$keyword."%' )";
			}

			
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
			
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
					
				$nestedData[] = $list['year'];
				//$nestedData[] = $list['month'];
				if($list['month'])
				{
				    $m = $list['month'];

		        $nestedData[] = date('F', mktime(0,0,0,$m, 1, date('Y')));
		
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				$nestedData[] ='<a title="Invoice" class="btn btn-primary btn-sm" href="'.base_url('employe/account/tdsInvoiceSummery').'/'.$list['id'].'">Invoice</a>';

				
				
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					);

		echo json_encode($json_data);  // send data as json format
	}



	public function tdsInvoiceSummery($invoice_id = '')
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$chk_invoice = $this->db->get_where('tds_invoice',array('account_id'=>$account_id,'id'=>$invoice_id))->num_rows();


		
		if(!$chk_invoice )

		{
			$this->Az->redirect('employe/account/tdsInvoice', 'system_message_error',lang('INVOICE_ERROR'));
		}

		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'invoice_id'  => $invoice_id,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/tds-invoice-summery'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}



		public function getTdsInvoiceSummeryList()
	{	
		
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();


		$extra_search = $requestData['extra_search'];	

	   	$keyword = '';
	   	$invoice_id = '';
	   	
	   	
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
           	$invoice_id = isset($filterData[1]) ? trim($filterData[1]) : 0;

        }

        $firstLoad = 0;
		
		$columns = array( 
		// datatable column index  => database column name
			3 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql =  "SELECT a.* , b.user_code as user_code, b.name as name FROM tbl_tds_invoice_summery as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.invoice_id = '$invoice_id'";


			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* , b.user_code as user_code, b.name as name FROM tbl_tds_invoice_summery as a INNER JOIN tbl_users as b ON b.id = a.user_id where a.account_id = '$account_id' AND a.invoice_id = '$invoice_id'";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
					$sql.=" OR b.name LIKE '%".$keyword."%'";
							
				$sql.=" OR a.invoice_id LIKE '%".$keyword."%' )";
			}

			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
			

		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				$nestedData[] = $list['user_invoice'];
				$nestedData[] = $list['user_code'].'<br>'.$list['name'];						
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				$nestedData[] ='<a title="Invoice" class="btn btn-primary btn-sm" href="'.base_url('employe/account/memberTdsInvoice').'/'.$list['id'].'">Invoice</a>';

				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function memberTdsInvoice($id = '')
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);

		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$chk_invoice = $this->db->get_where('tds_invoice_summery',array('account_id'=>$account_id,'id'=>$id))->num_rows();
		
		
		if(!$chk_invoice )

		{
			$this->Az->redirect('employe/account/tdsInvoice', 'system_message_error',lang('INVOICE_ERROR'));
		}


		$invoice_data = $this->db->get_where('tds_invoice_summery',array('account_id'=>$account_id,'id'=>$id))->row_array();

		$account_invoice_data = $this->db->get_where('tds_invoice',array('account_id'=>$account_id,'id'=>$invoice_data['invoice_id']))->row_array();

		$m = $account_invoice_data['month'];

		$invoice_month = date('F', mktime(0,0,0,$m, 1, date('Y')));
		$invoice_year = $account_invoice_data['year'];

		



		$address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

		

		$company_name = $accountData['title'];
		$company_email = $accountData['email'];
		$company_mobile = $accountData['mobile'];
		$company_logo = $accountData['image_path'];
		$company_address = $address['address'];


		$get_user_detail = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$invoice_data['user_id']))->row_array();

		$user_name = $get_user_detail['name'];
		$user_code = $get_user_detail['user_code'];
		$user_email = $get_user_detail['email'];
		$user_mobile = $get_user_detail['mobile'];
		$invoice_id = $invoice_data['user_invoice'];
		$invoice_date = $invoice_data['created'];

		$total_tds_amount = $invoice_data['total_tds_amount'];
		$total_com_amount = $invoice_data['total_com_amount'];



		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'company_name' =>$company_name,
			'company_email' =>$company_email,
			'company_mobile' =>$company_mobile,
			'company_logo' =>$company_logo,
			'user_name' =>$user_name,
			'user_email' =>$user_email,
			'user_mobile' =>$user_mobile,
			'invoice_id' =>$invoice_id,
			'invoice_date' =>$invoice_date,
			'company_address' =>$company_address,
			'invoice_month'	=>$invoice_month,
			'invoice_year'	=>$invoice_year,
			'user_code'		=>$user_code,
			'total_tds_amount' =>$total_tds_amount,
			'total_com_amount' =>$total_com_amount,			
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/member-tds-invoice'
		);
		$this->parser->parse('employe/layout/column-2' , $data);
	}



		public function downloadTds()
	{
		$account_id = $this->User->get_domain_account();
		$siteUrl = base_url();		
		$post = $this->input->post();

		$keyword = isset($post['keyword']) ? trim($post['keyword']) : '';
		$invoice_id = isset($post['invoice_id']) ? trim($post['invoice_id']) : '';
		//$fromDate = isset($post['from_date']) ? trim($post['from_date']) : '';
        //$toDate = isset($post['to_date']) ? trim($post['to_date']) : '';

        $sql = "SELECT a.*, b.user_code as user_code, b.name as name , b.pan_no FROM tbl_tds_invoice_summery as a INNER JOIN tbl_users as b ON b.id = a.user_id  where  a.account_id = '$account_id' AND b.role_id > 2 AND a.invoice_id = '$invoice_id'";
			
		if($keyword != '') {   
			$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
			$sql.=" OR b.mobile LIKE '%".$keyword."%'";
			$sql.=" OR a.description LIKE '%".$keyword."%'";
			$sql.=" OR a.amount LIKE '%".$keyword."%'";
			$sql.=" OR b.name LIKE '%".$keyword."%' )";
		}

			
		$sql.=" ORDER BY a.created DESC";
	
	
	
		$get_filter_data = $this->db->query($sql)->result_array();

        $fileName = 'member_tds_report.csv';
        header('Content-type: text/csv');
        header('Content-disposition: attachment;filename='.$fileName);
        header("Refresh:0; url=".$siteUrl."employe/account/tdsInvoice");
        echo "#,Member ID,Name,Pan NO,Total Commission,Total TDS,Datetime,".PHP_EOL;
        if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				echo "$i,$list[user_code],$list[name],$list[pan_no],$list[total_com_amount],$list[total_tds_amount],".date('d-M-Y H:i:s',strtotime($list['created'])).",".PHP_EOL;
				
				$i++;
			}
		}
	}



		public function generateManualInvoice()
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$user_type = $this->db->where_in('role_id',array(3,4,5,6))->get('users')->result_array();
		
		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'user_type' =>$user_type,
			'loggedUser'  => $loggedUser,			
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/generate-manual-invoice'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}



	public function generateManualInvoiceAuth()
	{
		$domain_account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
       
		//check for foem validation
		$post = $this->input->post();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('invoice', 'Invoice', 'required');
		$this->form_validation->set_rules('invoice_year', 'Invoice Year', 'required|xss_clean');
		$this->form_validation->set_rules('issue_date', 'Issue Date', 'required|xss_clean');
		
		if ($this->form_validation->run() == FALSE) {

			$this->generateManualInvoice();

		}

		else{


				if($post)
				{
					$user_type = $post['user'];
					$year = $post['invoice_year'];
            		$type = 2;


            		$data = array(
            			'account_id' =>$domain_account_id,
            			'type' =>$type, 
            			'year' =>$year,           			
            			'created' => date('Y-m-d H:i:s')

            		);

            		$this->db->insert('account_invoice',$data); 
					$recordID = $this->db->insert_id();

                    $totalRechargeCom = $post['recharge_commission_amount'];
                    $totalRechargeAmount = $post['recharge_amount'];
                    $final_payout_charge_amount = $post['payout_charge_amount'];
                    $totalBbpsAmount = $post['bbps_amount'];
                    $totalBbpsCom = $post['bbps_commission_amount'];
                    $totalAepsAmount = $post['aeps_amount'];
                    $totalAepsChargeAmount = $post['aeps_commission_amount'];
                    $serviceAmount = $post['service_amount'];
                    $serviceChargeAmount = $post['service_charge_amount'];
                    $rechargeHsnCode = $post['recharge_hsn_code'];
                    $bbpsHsnCode = $post['bbps_hsn_code'];
                    $payoutHsnCode = $post['payout_hsn_code'];
                    $AepsHsnCode = $post['aeps_hsn_code'];
                    $serviceHsnCode = $post['service_hsn_code'];
                    $rechargeDiscount = $post['recharge_discount'];
                    $rechargeChargeAmount = $post['recharge_charge_amount'];
                    $rechargeTaxableAmount = $post['recharge_taxable_amount'];
                    $rechargeTaxAmount = $post['recharge_tax_amount'];
                    $bbpsDiscount = $post['bbps_discount'];
                    $bbpsChargeAmount = $post['bbps_charge_amount'];
                    $bbpsTaxableAmount = $post['bbps_taxable_amount'];
                    $bbpsTaxAmount = $post['bbps_tax_amount'];
                    $payoutDiscount = $post['payout_discount'];
                    $payoutChargeAmount = $post['payout_charge_amount'];
                    $payoutTaxableAmount = $post['payout_taxable_amount'];
                    $payoutTaxAmount = $post['payout_tax_amount'];
                    $aepsDiscount = $post['aeps_discount'];
                    $aepsChargeAmount = $post['aeps_charge_amount'];
                    $aepsTaxableAmount = $post['aeps_taxable_amount'];
                    $aepsTaxAmount = $post['aeps_tax_amount'];
                    $serviceDiscount = $post['service_discount'];
                    $serviceChargeAmount = $post['service_charge_amount'];
                    $serviceTaxableAmount = $post['service_taxable_amount'];
                    $serviceTaxAmount = $post['service_tax_amount'];
                    $totalTaxableAmount = $post['total_taxable_amount'];
                    $totalTaxAmount = $post['total_tax_amount'];
                    $totalAmount = $post['total_amount'];

                    	$data = array(	
                    	'account_id' =>$domain_account_id,
						'invoice_id' =>$recordID,
						'user_invoice' =>$post['invoice'],
						'user_id' => $user_type,
						'recharge_commission' => $totalRechargeCom,
						'recharge_amount' => $totalRechargeAmount,
						'payout_charge_amount'=>$final_payout_charge_amount,
						'bbps_amount' =>$totalBbpsAmount,
						'bbps_commission' =>$totalBbpsCom,
						'aeps_amount' =>$totalAepsAmount,
						'aeps_commission'=>$totalAepsChargeAmount,
						'service_amount' =>$serviceAmount,
						'service_charge_amount' =>$serviceChargeAmount,
						'recharge_hsn_code' =>$rechargeHsnCode,
						'bbps_hsn_code' =>$bbpsHsnCode,
						'payout_hsn_code' =>$payoutHsnCode,
						'aeps_hsn_code' =>$AepsHsnCode,
						'service_hsn_code' =>$serviceHsnCode,
						'recharge_discount' =>$rechargeDiscount,
						'recharge_charge_amount'=>$rechargeChargeAmount,
						'recharge_taxable_amount'=>$rechargeTaxableAmount,
						'recharge_tax_amount' => $rechargeTaxAmount,
						'bbps_discount' =>$bbpsDiscount,
						'bbps_charge_amount'=>$bbpsChargeAmount,
						'bbps_taxable_amount'=>$bbpsTaxableAmount,
						'bbps_tax_amount' => $bbpsTaxAmount,
						'payout_discount' =>$payoutDiscount,
						'payout_charge_amount'=>$payoutChargeAmount,
						'payout_taxable_amount'=>$payoutTaxableAmount,
						'payout_tax_amount' => $payoutTaxAmount,
						'aeps_discount' =>$aepsDiscount,
						'aeps_charge_amount'=>$aepsChargeAmount,
						'aeps_taxable_amount'=>$aepsTaxableAmount,
						'aeps_tax_amount' => $aepsTaxAmount,
						'service_discount' =>$serviceDiscount,
						'service_charge_amount'=>$serviceChargeAmount,
						'service_taxable_amount'=>$serviceTaxableAmount,
						'service_tax_amount' => $serviceTaxAmount,
						'total_taxable_amount' =>$totalTaxableAmount,
						'total_tax_amount' =>$totalTaxAmount,
						'total_amount' =>$totalAmount,
						'issue_date'=>$post['issue_date'],
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('account_invoice_summery',$data);

					}

				}


				$this->Az->redirect('employe/account/manualInvoiceList', 'system_message_error',lang('ACCOUNT_INVOICE_SUCCESS'));

		}


		public function manualInvoiceList()
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'list'  =>$list,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/manual-invoice-list'
		);
		$this->parser->parse('employe/layout/column-1' , $data);
	}


	public function getManualInvoiceList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];
		$requestData= $this->input->get();
		$extra_search = $requestData['extra_search'];	
	   	$keyword = '';
	   	
        if($extra_search)
        {
            $filterData = explode('|',$extra_search);
            $keyword = isset($filterData[0]) ? trim($filterData[0]) : '';
            
        }
		
		$columns = array( 
		// datatable column index  => database column name
			4 => 'a.created',	
		);
		
		
		
			// getting total number records without any search
			$sql =  "SELECT a.* FROM tbl_account_invoice as a where a.account_id = '$account_id' AND a.type = 2";;
			
			$totalData = $this->db->query($sql)->num_rows();
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
			$sql = "SELECT a.* FROM tbl_account_invoice as a where a.account_id = '$account_id' AND a.type = 2";
			
			if($keyword != '') {   
				$sql.=" AND ( b.user_code LIKE '%".$keyword."%' ";    
				$sql.=" OR b.name LIKE '%".$keyword."%'";
				$sql.=" OR a.year LIKE '%".$keyword."%'";				
				$sql.=" OR a.type LIKE '%".$keyword."%' )";
			}

			
			
			$order_type = $requestData['order'][0]['dir'];
			//if($requestData['draw'] == 1)
			//	$order_type = 'DESC';
			
			$order_no = isset($requestData['order'][0]['column']) ? ($requestData['order'][0]['column'] == 0) ? 0 : $requestData['order'][0]['column'] : 0;
			$totalFiltered = $this->db->query($sql)->num_rows();
			$sql.=" ORDER BY ". $columns[$order_no]."   ".$order_type."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		
		
			$get_filter_data = $this->db->query($sql)->result_array();
			
		
		$data = array();
		$totalrecord = 0;
		if($get_filter_data){
			$i=1;
			foreach($get_filter_data as $list){
				
				
				
				$nestedData=array(); 
				$nestedData[] = $i;
				
				if($list['type'] == 1)
				{
					$nestedData[] = 'Dynamic';
				}
				else
				{
					$nestedData[] = 'Manual';
				}
				$nestedData[] = $list['year'];
				
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				$nestedData[] ='<a title="Invoice" class="btn btn-primary btn-sm" href="'.base_url('employe/account/manualInvoiceSummery').'/'.$list['id'].'">Invoice</a>';

				
				
				$data[] = $nestedData;
				
				
				
			$i++;}
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data,   // total data array
					//"total_selected_students" => $total_selected_students
					);

		echo json_encode($json_data);  // send data as json format
	}


	public function manualInvoiceSummery($invoice_id = '')
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$chk_invoice = $this->db->get_where('account_invoice_summery',array('account_id'=>$account_id,'invoice_id'=>$invoice_id))->num_rows();


		
		if(!$chk_invoice )

		{
			$this->Az->redirect('employe/account/manualInvoiceList', 'system_message_error',lang('INVOICE_ERROR'));
		}


		$invoice_data = $this->db->get_where('account_invoice_summery',array('account_id'=>$account_id,'invoice_id'=>$invoice_id))->row_array();

		
		$user_data = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$invoice_data['user_id']))->row_array();

					$recharge_commission = $invoice_data['recharge_commission'];
                    $recharge_amount = $invoice_data['recharge_amount'];
                    $payout_charge_amount = $invoice_data['payout_charge_amount'];
                    $bbps_amount = $invoice_data['bbps_amount'];
                    $bbps_commission_amount = $invoice_data['bbps_commission_amount'];
                    $aeps_amount = $invoice_data['aeps_amount'];
                    $aeps_commission_amount = $invoice_data['aeps_commission_amount'];
                    $service_amount = $invoice_data['service_amount'];
                    $service_charge_amount = $invoice_data['service_charge_amount'];
                    $recharge_hsn_code = $invoice_data['recharge_hsn_code'];
                    $bbps_hsn_code = $invoice_data['bbps_hsn_code'];
                    $payout_hsn_code = $invoice_data['payout_hsn_code'];
                    $aeps_hsn_code = $invoice_data['aeps_hsn_code'];
                    $service_hsn_code = $invoice_data['service_hsn_code'];
                    $recharge_discount = $invoice_data['recharge_discount'];
                    $recharge_charge_amount = $invoice_data['recharge_charge_amount'];
                    $recharge_taxable_amount = $invoice_data['recharge_taxable_amount'];
                    $recharge_tax_amount = $invoice_data['recharge_tax_amount'];
                    $bbps_discount = $post['bbps_discount'];
                    $bbps_charge_amount = $invoice_data['bbps_charge_amount'];
                    $bbps_taxable_amount = $invoice_data['bbps_taxable_amount'];
                    $bbps_tax_amount = $invoice_data['bbps_tax_amount'];
                    $payout_discount = $invoice_data['payout_discount'];
                    $payout_charge_amount = $invoice_data['payout_charge_amount'];
                    $payout_taxable_amount = $invoice_data['payout_taxable_amount'];
                    $payout_tax_amount = $invoice_data['payout_tax_amount'];
                    $aeps_discount = $invoice_data['aeps_discount'];
                    $aeps_charge_amount = $invoice_data['aeps_charge_amount'];
                    $aeps_taxable_amount = $invoice_data['aeps_taxable_amount'];
                    $aeps_tax_amount = $invoice_data['aeps_tax_amount'];
                    $service_discount = $invoice_data['service_discount'];
                    $service_charge_amount = $invoice_data['service_charge_amount'];
                    $service_taxable_amount = $invoice_data['service_taxable_amount'];
                    $service_tax_amount = $invoice_data['service_tax_amount'];
                    $total_taxable_amount = $invoice_data['total_taxable_amount'];
                    $total_tax_amount = $invoice_data['total_tax_amount'];
                    $total_amount = $invoice_data['total_amount'];
                   	$issue_date = $invoice_data['issue_date'];
                   	$user_invoice = $invoice_data['user_invoice'];

		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,
			'invoice_id'  => $invoice_id,
			'recharge_commission'=>$recharge_commission,
			'recharge_amount' =>$recharge_amount,
			'payout_charge_amount' =>$payout_charge_amount,
			'bbps_amount'	=>$bbps_amount,
			'bbps_commission_amount' =>$bbps_commission_amount,
			'aeps_amount' =>$aeps_amount,
			'aeps_commission_amount' => $aeps_commission_amount,
			'service_amount' =>$service_amount,
			'service_charge_amount' =>$service_charge_amount,
			'recharge_hsn_code' =>$recharge_hsn_code,
			'bbps_hsn_code' =>$bbps_hsn_code,
			'payout_hsn_code'=>$payout_hsn_code,
			'aeps_hsn_code'=>$aeps_hsn_code,
			'service_hsn_code' =>$service_hsn_code,
			'recharge_discount' =>$recharge_discount,
			'recharge_charge_amount' =>$recharge_charge_amount,
			'recharge_taxable_amount' =>$recharge_taxable_amount,
			'recharge_tax_amount' =>$recharge_tax_amount,
			'bbps_discount'=>$bbps_discount,
			'bbps_charge_amount' =>$bbps_charge_amount,
			'bbps_taxable_amount' =>$bbps_taxable_amount,
			'bbps_tax_amount' =>$bbps_tax_amount,
			'payout_discount' =>$payout_discount,
			'payout_charge_amount' =>$payout_charge_amount,
			'payout_taxable_amount' =>$payout_taxable_amount,
			'payout_tax_amount' =>$payout_tax_amount,
			'aeps_discount' =>$aeps_discount,
			'aeps_charge_amount' =>$aeps_charge_amount,
			'aeps_taxable_amount' =>$aeps_taxable_amount,
			'aeps_tax_amount' =>$aeps_tax_amount,
			'service_discount' =>$service_discount,
			'service_taxable_amount' =>$service_taxable_amount,
			'service_tax_amount' =>$service_tax_amount,
			'total_taxable_amount' =>$total_taxable_amount,
			'total_tax_amount' =>$total_tax_amount,
			'total_amount' =>$total_amount,
			'user_data'=>$user_data,
			'issue_date' =>$issue_date,
			'user_invoice' =>$user_invoice,
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/manual-invoice-summery'
		);
		$this->parser->parse('employe/layout/column-2' , $data);
	}
	

}



?>