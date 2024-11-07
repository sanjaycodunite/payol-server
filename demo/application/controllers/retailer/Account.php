<?php 
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

class Account extends CI_Controller {   



    public function __construct() 
    {
		parent::__construct();
		$this->User->checkRetailerPermission();
			
    }				

	


	//invoice management


	public function tdsInvoice()
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		
		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,			
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/tds-invoice'
		);
		$this->parser->parse('retailer/layout/column-1' , $data);
	}


	public function getTdsInvoiceList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
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
			$sql =  "SELECT a.* FROM tbl_tds_invoice as a where a.account_id = '$account_id'";
			
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
				$nestedData[] ='<a title="Invoice" class="btn btn-primary btn-sm" href="'.base_url('retailer/account/memberInvoice').'/'.$list['id'].'">Invoice</a>';

				
				
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
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$loggedAccountID = $loggedUser['id'];


		$chk_invoice = $this->db->get_where('tds_invoice_summery',array('account_id'=>$account_id,'invoice_id'=>$id,'user_id'=>$loggedAccountID))->num_rows();
		
		if(!$chk_invoice )

		{
			$this->Az->redirect('retailer/account/tdsInvoice', 'system_message_error',lang('INVOICE_ERROR'));
		}


		$invoice_data = $this->db->get_where('tds_invoice_summery',array('account_id'=>$account_id,'invoice_id'=>$id,'user_id'=>$loggedAccountID))->row_array();

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
		$this->parser->parse('retailer/layout/column-2' , $data);
	}




	public function gstInvoice()
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		
		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,			
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/gst-invoice'
		);
		$this->parser->parse('retailer/layout/column-1' , $data);
	}


	public function getGstInvoiceList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
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
			$sql =  "SELECT a.* FROM tbl_account_invoice as a where a.account_id = '$account_id' AND a.type = 1";
			
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
					
				$nestedData[] = $list['year'];
				//$nestedData[] = $list['month'];
				if($list['month'])
				{
				    $m = $list['month'];

		        $nestedData[] = date('F', mktime(0,0,0,$m, 1, date('Y')));
		
				}
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				$nestedData[] ='<a title="Invoice" class="btn btn-primary btn-sm" href="'.base_url('retailer/account/memberGstInvoice').'/'.$list['id'].'">Invoice</a>';

				
				
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



	public function memberGstInvoice($id = '')
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);

		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		$loggedAccountID = $loggedUser['id'];


		$chk_invoice = $this->db->get_where('account_invoice_summery',array('account_id'=>$account_id,'invoice_id'=>$id,'user_id'=>$loggedAccountID))->num_rows();


		
		if(!$chk_invoice )

		{
			$this->Az->redirect('retailer/account/gstInvoice', 'system_message_error',lang('INVOICE_ERROR'));
		}


		$invoice_data = $this->db->get_where('account_invoice_summery',array('account_id'=>$account_id,'invoice_id'=>$id,'user_id'=>$loggedAccountID))->row_array();

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
			'content_block' => 'account/member-gst-invoice'
		);
		$this->parser->parse('retailer/layout/column-2' , $data);
	}



	public function manualInvoice()
	{
		$account_id = $this->User->get_domain_account();
		//get logged user info
		$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
		
		$siteUrl = base_url();		
		$data = array(
			'meta_title' => lang('SITE_NAME'),
			'meta_keywords' => lang('SITE_NAME'),
			'meta_description' => lang('SITE_NAME'),
			'site_url' => $siteUrl,
			'loggedUser'  => $loggedUser,			
			'system_message' => $this->Az->getSystemMessageError(),
			'system_info' => $this->Az->getsystemMessageInfo(),
			'system_warning' => $this->Az->getSystemMessageWarning(),
			'content_block' => 'account/manual-invoice'
		);
		$this->parser->parse('retailer/layout/column-1' , $data);
	}


	public function getManualInvoiceList()
	{	
		$account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
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
			$sql =  "SELECT a.* FROM tbl_account_invoice as a where a.account_id = '$account_id' AND a.type = 2";
			
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
				//$nestedData[] = $list['month'];
				
				$nestedData[] = date('d-M-Y H:i:s',strtotime($list['created']));
				$nestedData[] ='<a title="Invoice" class="btn btn-primary btn-sm" href="'.base_url('retailer/account/manualInvoiceSummery').'/'.$list['id'].'">Invoice</a>';

				
				
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
        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

		$chk_invoice = $this->db->get_where('account_invoice_summery',array('account_id'=>$account_id,'invoice_id'=>$invoice_id,'user_id'=>$loggedAccountID))->num_rows();


		
		if(!$chk_invoice )

		{
			$this->Az->redirect('retailer/account/manualInvoice', 'system_message_error',lang('INVOICE_ERROR'));
		}


		$invoice_data = $this->db->get_where('account_invoice_summery',array('account_id'=>$account_id,'invoice_id'=>$invoice_id,'user_id'=>$loggedAccountID))->row_array();

		
		$user_data = $this->db->get_where('users',array('account_id'=>$account_id,'id'=>$loggedAccountID))->row_array();

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
		$this->parser->parse('retailer/layout/column-2' , $data);
	}

}



?>