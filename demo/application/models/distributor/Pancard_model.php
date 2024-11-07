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

class Pancard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
		
    }

    public function activePancardMember($post,$aadhar_card,$pancard)
    {
    	 $account_id = $this->User->get_domain_account();
       $accountData = $this->User->get_account_data($account_id);
    	 $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
		   $memberID = $loggedUser['id'];
        
        $chk_kyc = $this->db->get_where('member_pancard_kyc',array('account_id'=>$account_id,'user_id'=>$memberID))->num_rows();

        if(!$chk_kyc){

            $kyc_data = array(
              'account_id' => $account_id,
              'user_id' => $memberID,
              'name' => $post['name'],
              'email' => $post['email'],
              'mobile'=> $post['mobile'],
              'aadhar_card' => $aadhar_card,
              'pancard' => $pancard,
              'status' => 0,
              'created' => date('Y-m-d H:i:s')
            );    

            $this->db->insert('member_pancard_kyc',$kyc_data);
        }
        else{


            $kyc_data = array(
              'name' => $post['name'],
              'email' => $post['email'],
              'mobile'=> $post['mobile'],
              'aadhar_card' => $aadhar_card,
              'pancard' => $pancard,
              'status' => 0,
              'created' => date('Y-m-d H:i:s')
            );    

            $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$memberID);
            $this->db->update('member_pancard_kyc',$kyc_data);

        }

        //Pancard Kyc Api Call

        /*$aadhar_card = 'https://www.maxpaymoney.com/media/pancard_kyc_doc/aadhar.JPEG';
        $pancard = 'https://www.maxpaymoney.com/media/pancard_kyc_doc/pan.JPEG';*/

        $pancard_kyc_url = PANCARD_KYC_URL;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$pancard_kyc_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            'Token: '.$accountData['dmt_token'],
            'name: '.trim($post['name']),
            'email: '.trim($post['email']),
            'mobile: '.trim($post['mobile']),
            'aadharurl: '.base_url($aadhar_card),
            'panurl: '.base_url($pancard),
            'Content-Type:application/x-www-form-urlencoded'
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec ($ch);
        curl_close ($ch);
        $responseData = json_decode($output,true);

        $post['aadhar_card'] = base_url($aadhar_card);
        $post['pancard']     = base_url($pancard);

        $api_response_data = array(

         'account_id' => $account_id,
         'user_id' => $memberID,
         'api_url' => $pancard_kyc_url,
         'post_data'=>json_encode($post),
         'api_response' =>$output,
         'created' => date('Y-m-d H:i:s')    
        );

        $this->db->insert('pancard_api_response',$api_response_data);


        if(isset($responseData) && $responseData['statuscode'] == "TXN" && $responseData['status'] == "Request Sent Successfully"){

            $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$memberID);
            $this->db->update('member_pancard_kyc',array('status'=>1));

            // update user pancard kyc status

            $this->db->where('id',$memberID);
            $this->db->update('users',array('pancard_status'=>1));

            return array('status'=>1,'msg'=>$responseData['status']);

        }
        else{

            $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$memberID);
            $this->db->update('member_pancard_kyc',array('status'=>0));

            // update user pancard kyc status

            $this->db->where('id',$memberID);
            $this->db->update('users',array('pancard_status'=>0));

            return array('status'=>0,'msg'=>$responseData['status']);

        }

        
    }



    public function purchaseCoupon($post,$transid)
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
        $memberID = $loggedUser['id'];
        
        $pancard_purchase_coupon_url = PANCARD_PURCHASE_COUPON__URL;

        $response = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$pancard_purchase_coupon_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            'Token: '.$accountData['dmt_token'],
            'coupon: '.$post['coupon'],
            'psaloginid: '.trim($post['psa_login_id']),
            'TransID: '.$transid,
            'Content-Type:application/x-www-form-urlencoded'
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec ($ch);
        curl_close ($ch);

        /*$output = '{"statuscode":"optx:9745466040","status":"Success"}';*/

        $responseData = json_decode($output,true);

        $api_response_data = array(

         'account_id' => $account_id,
         'user_id' => $memberID,
         'api_url' => $pancard_purchase_coupon_url,
         'post_data'=>json_encode($post),
         'api_response' =>$output,
         'created' => date('Y-m-d H:i:s')    
        );

        $this->db->insert('pancard_api_response',$api_response_data);

        if(isset($responseData['status']) && $responseData['status'] == "Success"){

            $get_token = explode("optx:",$responseData['statuscode']);

            $token = $get_token[1];

            $response = array(

              'status' => 1,
              'message'=>'Success',
              'token' => $token    
            );
        }
        elseif(!isset($responseData['status'])){

            $response = array(

              'status' => 1,
              'message'=>'Pending'   
            );
        }
        else{

            $response = array(

             'status' => 0,
             'message'=>'Failed'    
            
            );
        }
        
        return $response;
        
    }

   public function activeNsdlPancardMember($post,$transaction_id)
    {
       $account_id = $this->User->get_domain_account();
       $accountData = $this->User->get_account_data($account_id);
       $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
       $memberID = $loggedUser['id'];
        
        

        $kyc_data = array(
          'account_id' => $account_id,
          'txn_id'=>$transaction_id,
          'member_id' => $memberID,
          'firstname' => $post['firstname'],
          'middlename' => $post['middlename'],
          'lastname'=> $post['lastname'],
          'email'=> trim(strtolower($post['email'])),
          'mobile'=> $post['mobile'],
          'gender' => $post['gender'],
          'dob'=> date('Y-m-d',strtotime($post['dob'])),
          'pincode'=> $post['pincode'],
          'address'=> $post['address'],
          'shop_name'=> $post['shop_name'],
          'state_id'=> $post['state_id'],
          'district_id'=> $post['district_id'],
          'pan_number'=> $post['pannumber'],
          'aadhar_number'=> $post['aadharnumber'],
          'status' => 1,
          'created' => date('Y-m-d H:i:s'),
          'created_by' => $memberID
        );    

        $this->db->insert('nsdl_kyc',$kyc_data);
        $recordID = $this->db->insert_id();
          
        
        $com_amount = $this->User->get_pan_activation_charge($memberID);

        $this->User->distribute_pancard_commision($recordID,$transaction_id,$memberID,$com_amount);
        
        return true;
        
    }


    public function findPanNumber($post,$transaction_id)
    {
       $account_id = $this->User->get_domain_account();
       $accountData = $this->User->get_account_data($account_id);
       $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
       $memberID = $loggedUser['id'];
        
        

        $kyc_data = array(
          'account_id' => $account_id,
          'txn_id'=>$transaction_id,
          'member_id' => $memberID,
          'name' => $post['name'],          
          'dob'=> date('Y-m-d',strtotime($post['dob'])),          
          'aadhar_number'=> $post['aadharnumber'],
          'status' => 1,
          'created' => date('Y-m-d H:i:s'),
          'created_by' => $memberID
        );    

        $this->db->insert('find_pan_number',$kyc_data);
        $recordID = $this->db->insert_id();
          
        
        $com_amount = $this->User->get_find_pan_charge($memberID);

        $this->User->distribute_find_pan_commision($recordID,$transaction_id,$memberID,$com_amount);
        
        return true;
        
    }
    
    
    public function utiBalanceRequest($post,$transaction_id)
    {
       $account_id = $this->User->get_domain_account();
       $accountData = $this->User->get_account_data($account_id);
       $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
       $memberID = $loggedUser['id'];
        

        $kyc_data = array(
          'account_id' => $account_id,
          'txn_id'=>$transaction_id,
          'member_id' => $memberID,
          'uti_pan_id' => $post['uti_pan_id'],
          'coupon'    =>$post['coupon'],
          'status' => 1,
          'created' => date('Y-m-d H:i:s'),
          'created_by' => $memberID
        );    

        $this->db->insert('uti_balance_request',$kyc_data);
        $recordID = $this->db->insert_id();
         
        $com_amount = $this->User->get_uti_balance_charge($memberID);

        $this->User->distribute_uti_balance_commision($recordID,$transaction_id,$memberID,$com_amount);
        
        return true;
        
    }

    
	

}


/* end of file: az.php */
/* Location: ./application/models/az.php */