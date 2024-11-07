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

class Register_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->lang->load('front/message', 'english');
        $this->lang->load('email', 'english');
    }


    public function sendOTP($post)
    {
    	$otp_code = $this->User->generate_unique_otp();
        $encrypt_otp_code = do_hash($otp_code);
        $mobile = $post['mobile'];
		$output = '';
        $sms = sprintf(lang('REGISTER_OPT_SEND_SMS'),$otp_code);
        
        //$api_url = SMS_API_URL.'authkey='.SMS_API_AUTH_KEY.'&mobiles=91'.$mobile.'&message='.urlencode($sms).'&sender='.SMS_API_SENDERID.'&route=4&country=0';
        
        //$api_url = SMS_API_URL.'authkey='.SMS_AUTH_KEY.'&mobiles='.$mobile.'&message='.urlencode($sms).'&sender='.SMS_SENDER_ID.'&route=4&country=0';
        
        $api_url = SMS_API_URL.'receiver='.$mobile.'&sms='.urlencode($sms);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $output = curl_exec($ch); 
        curl_close($ch);
        $otp_data = array(
            'otp_code' => $otp_code,
            'encrypt_otp_code' => $encrypt_otp_code,
            'mobile' => $mobile,
            'status' => 0,
            'api_response' => $output,
            'json_post_data' => json_encode($post),
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('users_otp',$otp_data);
        
        // send OTP on Email
        $message = 'Dear User,
        You are trying to register on Marwar Care, Your OTP is : '.$otp_code.'
        If you have any issue please contact us.
        Thanks
        Marwarcare Team
        ';
        $subject = 'Marwarcare Registration OTP';
        $lang = 'OTP_EMAIL';
        $to = strtolower(trim($post['email']));
        $this->User->sendEmail($to,$message,$subject,$lang);
        
        return true;
    }
	
	public function referralRegisterSendOTP($post)
    {
    	$otp_code = $this->User->generate_unique_otp();
        $encrypt_otp_code = do_hash($otp_code);
        $mobile = $post['mobile'];
		$output = '';
        $sms = sprintf(lang('REGISTER_OPT_SEND_SMS'),$otp_code);
        
        //$api_url = SMS_API_URL.'authkey='.SMS_API_AUTH_KEY.'&mobiles=91'.$mobile.'&message='.urlencode($sms).'&sender='.SMS_API_SENDERID.'&route=4&country=0';
        
        //$api_url = SMS_API_URL.'authkey='.SMS_AUTH_KEY.'&mobiles='.$mobile.'&message='.urlencode($sms).'&sender='.SMS_SENDER_ID.'&route=4&country=0';
        
        $api_url = SMS_API_URL.'receiver='.$mobile.'&sms='.urlencode($sms);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $output = curl_exec($ch); 
        curl_close($ch);
        $otp_data = array(
            'otp_code' => $otp_code,
            'encrypt_otp_code' => $encrypt_otp_code,
            'mobile' => $mobile,
            'status' => 0,
            'api_response' => $output,
            'json_post_data' => json_encode($post),
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('users_otp',$otp_data);
        
        // send OTP on Email
        $message = 'Dear User,
        You are trying to register on Marwar Care, Your OTP is : '.$otp_code.'
        If you have any issue please contact us.
        Thanks
        Marwarcare Team
        ';
        $subject = 'Marwarcare Registration OTP';
        $lang = 'OTP_EMAIL';
        $to = strtolower(trim($post['email']));
        $this->User->sendEmail($to,$message,$subject,$lang);
        
        return $encrypt_otp_code;
    }

    
    public function registerMember($post)
    {
    	if($post)
    	{     
            
            $user_display_id = $this->User->generate_unique_member_id();
            
    		$data = array(
    			'role_id' => 2,
                'user_code'          =>  $user_display_id,      
                'name' => $post['name'],
    			'username' => $user_display_id,
    			'password' => do_hash($post['password']),
    			'decode_password' => $post['password'],
                'transaction_password' => do_hash($post['transaction_password']),
                'decoded_transaction_password' => $post['transaction_password'],
    			'email' => trim(strtolower($post['email'])),
    			'mobile' => $post['mobile'],
    			'is_active' => 1,
    			'is_verified'=>1,
                'wallet_balance'=>0,
                'created' => date('Y-m-d H:i:s')
    		);
    		$this->db->insert('users',$data);
            $member_id = $this->db->insert_id();
			
			
			
			$referral_id = trim($post['referral_id']);
			
			$parent_id = 1;
			$reffrel_id = 1;
			$referal_current_package_id = 1;
			
			if($referral_id)
			{
				// check member id valid or not
				$chk_member = $this->db->get_where('users',array('role_id'=>2,'user_code'=>$referral_id))->num_rows();
				if($chk_member)
				{
					$get_member_id = $this->db->select('id,current_package_id')->get_where('users',array('role_id'=>2,'user_code'=>$referral_id))->row_array();
					$parent_id = isset($get_member_id['id']) ? $get_member_id['id'] : 0 ;
					$reffrel_id = isset($get_member_id['id']) ? $get_member_id['id'] : 0 ;
					$referal_current_package_id = isset($get_member_id['current_package_id']) ? $get_member_id['current_package_id'] : 0 ;
				}
				
			}
			
			
            // check member position is blank or not
            $chk_member_position = $this->db->get_where('member_tree',array('parent_id'=>$reffrel_id,'position'=>$post['member_position']))->num_rows();
            if($chk_member_position)
            {
                $parent_id = $this->get_member_parent_id($reffrel_id,$post['member_position']);
            }

            
            
            $get_binary_downline_str = $this->db->get_where('member_tree',array('member_id'=>$parent_id))->row_array();
            $binary_downline_str = isset($get_binary_downline_str['binary_downline_str']) ? $get_binary_downline_str['binary_downline_str'].'-'.$post['member_position'].'-'.$member_id : '-'.$reffrel_id.'-'.$post['member_position'].'-'.$member_id;

            // get direct downline str
            $get_direct_downline_str = $this->db->get_where('member_tree',array('member_id'=>$referral_member_id))->row_array();
            $direct_downline_str = isset($get_direct_downline_str['direct_downline_str']) ? $get_direct_downline_str['direct_downline_str'].','.$member_id : $reffrel_id.','.$member_id;
            
            // save member tree
            $tree_data = array(
                'member_id' => $member_id,
                'parent_id' => $parent_id,
                'reffrel_id'=> $reffrel_id,
                'position'  => $post['member_position'],
                'binary_downline_str' => $binary_downline_str,
                'direct_downline_str' => $direct_downline_str,
                'created'   => date('Y-m-d H:i:s')     
            );
            $this->db->insert('member_tree',$tree_data);
		}



        $mobile = $post['mobile'];
        $output = '';

        
        $smsTemplateData = $this->db->get_where('sms_templates',array('id'=>1,'status'=>1))->row_array();
        $sms_message = isset($smsTemplateData['message']) ? $smsTemplateData['message'] : '' ;

        if($sms_message){
            
            $user_name = isset($post['name']) ? $post['name'] : '';
            $user_id   = isset($user_display_id) ? $user_display_id : '';
            $user_email = isset($post['email']) ? $post['email'] : '';
            $user_mobile = isset($post['mobile']) ? $post['mobile'] : '';
            $user_password = isset($post['password']) ? $post['password'] : '';
            $user_transaction_password = isset($post['transaction_password']) ? $post['transaction_password'] : '';
            
            
            $output = '';
            $sms = $sms_message;
            
            $sms = str_replace('{USER_NAME}',$user_name,$sms);
            $sms = str_replace('{USERID}',$user_id,$sms);
            $sms = str_replace('{USER_PASSWORD}',$user_password,$sms);
            $sms = str_replace('{USER_TRANSACTION_PASSWORD}',$user_transaction_password,$sms);    
        
            $api_url = SMS_API_URL.'authkey='.SMS_AUTH_KEY.'&mobiles='.'91'.$mobile.'&message='.urlencode($sms).'&sender='.SMS_SENDER_ID.'&route=4&country=0';
            
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            $output = curl_exec($ch); 
            curl_close($ch);

         }
        
        // send OTP on Email
        $message = 'Dear '.$post['name'].',
        Congratulations you are successfully regisetered on Marwar Care

        If you have any issue please contact us.
        Thanks
        Marwarcare Team
        ';
        $subject = 'Marwarcare Registration';
        $lang = 'OTP_EMAIL';
        $to = strtolower(trim($post['email']));
        $this->User->sendEmail($to,$message,$subject,$lang);

    	return true;
    }


    public function get_member_parent_id($parent_id = 0,$position = '')
    {
        
        // get member parent id
        $get_parent_id = $this->db->get_where('member_tree',array('parent_id'=>$parent_id,'position'=>$position))->row_array();
        if($get_parent_id){
            $parent_id = isset($get_parent_id['member_id']) ? $get_parent_id['member_id'] : 0 ;
            $parent_id = $this->get_member_parent_id($parent_id,$position);
        }
        
        return $parent_id;
        
    }
	
	
	public function sendForgotOTP($post)
    {   
        $otp_code = $this->User->generate_unique_otp();
        $encrypt_otp_code = do_hash($otp_code);
        $mobile = $post['mobile'];

        $get_user_name = $this->db->get_where('users',array('mobile'=>$mobile))->row_array();

        $username = isset($get_user_name['name'])?$get_user_name['name']:'';

		$output = '';
        $sms = sprintf(lang('FORGOT_OPT_SEND_SMS'),$otp_code);
        
        
        $smsTemplateData = $this->db->get_where('sms_templates',array('template_id'=>7,'status'=>1))->row_array();
        $sms_message = isset($smsTemplateData['message']) ? $smsTemplateData['message'] : '' ;


        if($sms_message){

            $otp   = isset($otp_code) ? $otp_code : '';
        
            $output = '';
            $sms = $sms_message;
            
            $sms = str_replace('{USER_NAME}',$username,$sms);
            $sms = str_replace('{OTP}',$otp,$sms);
            
            $api_url = SMS_API_URL.'authkey='.SMS_AUTH_KEY.'&mobiles='.'91'.$mobile.'&message='.urlencode($sms).'&sender='.SMS_SENDER_ID.'&route=4&country=0';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            $output = curl_exec($ch); 
            curl_close($ch);
            $otp_data = array(
                'otp_code' => $otp_code,
                'encrypt_otp_code' => $encrypt_otp_code,
                'mobile' => $mobile,
                'status' => 0,
                'api_response' => $output,
                'json_post_data' => json_encode($post),
                'created' => date('Y-m-d H:i:s')
            );
            $this->db->insert('users_otp',$otp_data);
        
        }


         // send OTP on Email
        $message = 'Dear User,
        You are trying to update your password on Marwar Care, Your OTP is : '.$otp_code.'
        If you have any issue please contact us.
        Thanks
        Marwarcare Team
        ';
        $subject = 'Marwarcare Registration OTP';
        $lang = 'OTP_EMAIL';
        $to = strtolower(trim($post['email']));
        $this->User->sendEmail($to,$message,$subject,$lang);
        
        return $encrypt_otp_code;
    }
	
	
	
}


/* end of file: az.php */
/* Location: ./application/models/az.php */