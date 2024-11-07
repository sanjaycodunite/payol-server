<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class iciciAepsinvoice extends CI_Controller{

    public function __construct() {
        parent::__construct();
		
    }

    public function index($id = ''){

        //get logged user info
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

        $sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


        $detail = $this->db->query($sql)->row_array();
        

        
        $siteUrl = base_url();      

        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'detail' => $detail,
            'address'=>$address,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'icici-aeps-invoice'
        );
        $this->parser->parse('front/layout/column-3' , $data);
    
    
    }   
    
    public function miniStatment($id = ''){

        //get logged user info
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $address = $this->db->get_where('tbl_website_contact_detail',array('account_id'=>$account_id))->row_array();

        $sql = "SELECT a.*,b.user_code as member_code,b.name as member_name FROM tbl_instantpay_aeps_transaction as a INNER JOIN tbl_users as b ON b.id = a.member_id where a.account_id = '$account_id' AND a.id = '$id'";


        $detail = $this->db->query($sql)->row_array();
        
        $dmrData = $this->db->get_where('instantpay_aeps_transaction',array('id'=>$id,'account_id'=>$account_id))->row_array();
        
        $statementList = json_decode($dmrData['json_data'],true);
 				$str = '';
	        	$str = '<div class="table-responsive">';
				 $str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
			    
			
				$str.='<td colspan="6">Statement</td>';
				//$str.='</tr>';
				$str.='<tr>';
				$str.='<td colspan="2">';
				//$str.='<table class="table table-bordered table-striped" width="100%" cellspacing="0">';
				$str.='<tr>';
				$str.='<th>#</th>';
				$str.='<th>Date</th>';
				$str.='<th>CR/DR</th>';
				$str.='<th>Amount</th>';
				$str.='<th>Description</th>';
				$str.='</tr>';
				$i = 1;
				if($statementList)
				{
					foreach($statementList as $list)
					{
						$str.='<tr>';
						$str.='<td>'.$i.'</td>';
						$str.='<td>'.$list['date'].'</td>';
						if($list['txnType'] == 'Dr')
						{
							$str.='<td><font color="red">DR</font></td>';
						}
						else
						{
							$str.='<td><font color="green">CR</font></td>';
						}
						$str.='<td>INR '.$list['amount'].'/-</td>';
						$str.='<td>'.$list['narration'].'</td>';
						$str.='</tr>';
						$i++;
					}
				}
				else
				{
					$str.='<tr>';
					$str.='<td colspan="5">No Record Found.</td>';
					$str.='</tr>';
				}
				//$str.='</table>';
				$str.='</td>';
				$str.='</tr>';

				 $str.='</table>';
				$str.='</div>';
				
				
        

        
        $siteUrl = base_url();      

        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,
            'accountData' => $accountData,
            'detail' => $detail,
            'address'=>$address,
            'str' =>$str,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'icici-aeps-mini-statment'
        );
        $this->parser->parse('front/layout/column-3' , $data);
    
    
    }   

}


/* End of file login.php */
/* Location: ./application/controllers/login.php */