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

class Kyc_model extends CI_Model {

  public function __construct() {
      parent::__construct();
  }




  public function updateKyc($post,$filePath,$filePath2,$filePath3,$filePath4,$filePath5,$filePath6)
    {

          $account_id = $this->User->get_domain_account();
        $loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

    
        // check data already saved or not
        $chk_data = $this->db->get_where('portal_kyc',array('user_id'=>$loggedAccountID))->num_rows();

        if(!$chk_data)
        {
            $data = array(
                 'signatory_aadhar_image' => $filePath,
                 'signatory_pan_image' => $filePath2,
                 'signatory_live_image' => $filePath3,
                 'application_form' => $filePath4,
                 'company_pan_card' => $filePath5,
                 'business_photo' => $filePath6,
                 'account_id'          => $account_id,
                'user_id'           => $loggedAccountID,
                'name' => $post['name'],
                'email' => $post['email'],
                'mobile' => $post['mobile'],
                'address' => $post['address'],
                'pincode' => $post['pincode'],
                'block' => $post['block'],
                'village' => $post['village'],
                'district' =>$post['district'],
                'signatory_name'=>$post['signatory_name'],
                'signatory_mobile' =>$post['signatory_mobile'],                
                'signatory_aadhar' => $post['signatory_aadhar'],
                'pancard_number' => $post['pan_no'],
                'bank_account_number' => $post['bank_account_number'],
                'account_holder_name' => $post['account_holder_name'],
                'ifsc_code' => $post['ifsc_code'],
                'bank_name' => $post['bank_name'],
                'branch' =>$post['branch'],
                'gst_number' => $post['gst_number'],                
                'created'=> date('Y-m-d H:i:s'),                
                'status'=>1,               
                'state' =>$post['state_id'],
                'city' =>$post['city'],
                'business_type'=>$post['business_type'],
                'business_industry'=>$post['business_industry'],
                'business_address'=>$post['business_address'],
                'business_website'=>$post['business_website'],
                'business_email'=>$post['business_email'],
                'service_name' => $post['service_name'],
                'use_case' =>$post['use_case'],
                'business_proof' =>$post['business_proof']



            );
            
            $this->db->insert('portal_kyc',$data);
        }
        else
        {
            $data = array(
                'account_id'          => $account_id,
                'user_id'           => $loggedAccountID,
                 'name' => $post['name'],
                'email' => $post['email'],
                'mobile' => $post['mobile'],
                'address' => $post['address'],
                'pincode' => $post['pincode'],
                'block' => $post['block'],
                'village' => $post['village'],
                'district' =>$post['district'],
                'signatory_name'=>$post['signatory_name'],
                'signatory_mobile' =>$post['signatory_mobile'],                
                'signatory_aadhar' => $post['signatory_aadhar'],
                'pancard_number' => $post['pan_no'],
                'bank_account_number' => $post['bank_account_number'],
                'account_holder_name' => $post['account_holder_name'],
                'ifsc_code' => $post['ifsc_code'],
                'bank_name' => $post['bank_name'],
                'branch' =>$post['branch'],
                'gst_number' => $post['gst_number'],                
                'created'=> date('Y-m-d H:i:s'),
                'state' =>$post['state_id'],
                'city' =>$post['city'],                
                'business_type'=>$post['business_type'],
                'business_industry'=>$post['business_industry'],
                'business_address'=>$post['business_address'],
                'business_website'=>$post['business_website'],
                'business_email'=>$post['business_email'],
                'service_name' => $post['service_name'],
                'use_case' =>$post['use_case'],
                'business_proof' =>$post['business_proof'],
                'status'=>1,         
               
            );

             if($filePath)
            {
                $data['signatory_aadhar_image'] = $filePath;
            }

             if($filePath2)
            {
                $data['signatory_pan_image'] = $filePath2;
            }

            if($filePath3)
            {
                $data['signatory_pan_image'] = $filePath3;
            }

            if($filePath4)
            {
                $data['application_form'] = $filePath4;
            }

            if($filePath5)
            {
                $data['company_pan_card'] = $filePath5;
            }

            if($filePath6)
            {
                $data['business_photo'] = $filePath6;
            }
            
            $this->db->where('account_id',$account_id);
            $this->db->where('user_id',$loggedAccountID);
            $this->db->update('portal_kyc',$data);
        }
        return true;
    }   

 


}


/* end of file: az.php */
/* Location: ./application/models/az.php */