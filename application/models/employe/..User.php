<?php
if(!defined('BASEPATH'))
    exit('No direct script access allowed.');

/*
 * Model for manage users information.
 * 
 * This model used for manage user data.
 * this one used for authenticate users, get informations about users
 * @author trilok
 */


class User extends CI_Model{

    public function checkPermission($mode = SUPERADMIN_SESSION_ID, $is_front = false) {
        
        $user = $this->session->userdata($mode);
		$this->lang->load('front', 'english');
        if (!$user) {
            // Load language
            $currLang = $this->session->userdata('language');
            $this->lang->load('superadmin/dashboard', $currLang);
            $this->load->helper('language');

            if ($is_front === false) {
                $this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('superadmin/login');
            } else {
                $this->session->set_flashdata('system_message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('superadmin/login');
            }
        }
        $loggedUser = $this->session->userdata(SUPERADMIN_SESSION_ID);
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id'],'is_active'=>1,'password'=>$loggedUser['password']))->num_rows();
		if(!$memberDetail)
		{
			$this->session->unset_userdata(SUPERADMIN_SESSION_ID);
			$currLang = $this->session->userdata('language');
            $this->lang->load('superadmin/dashboard', $currLang);
			$this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('superadmin/login');
		}

		$domain_account_id = $this->User->get_domain_account();
        if($domain_account_id != SUPERADMIN_ACCESS_ACCOUNT)
        {
        	redirect('errorpage');
        }


    }
	
	public function checkAdminPermission($mode = ADMIN_SESSION_ID, $is_front = false) {
        
        $user = $this->session->userdata($mode);
		$this->lang->load('front', 'english');
        if (!$user) {
            // Load language
            $currLang = $this->session->userdata('language');
            $this->lang->load('superadmin/dashboard', $currLang);
            $this->load->helper('language');

            if ($is_front === false) {
                $this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            } else {
                $this->session->set_flashdata('system_message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            }
        }
        $loggedUser = $this->session->userdata(ADMIN_SESSION_ID);
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id'],'is_active'=>1,'password'=>$loggedUser['password']))->num_rows();
		if(!$memberDetail)
		{
			$this->session->unset_userdata(ADMIN_SESSION_ID);
			$currLang = $this->session->userdata('language');
            $this->lang->load('superadmin/dashboard', $currLang);
			$this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
		}
 

    }
	
	public function checkMasterPermission($mode = MASTER_DIST_SESSION_ID, $is_front = false) {
        
        $user = $this->session->userdata($mode);
        
		$this->lang->load('front', 'english');
        if (!$user) {
            // Load language
            
            $this->load->helper('language');

            if ($is_front === false) {
                $this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            } else {
                $this->session->set_flashdata('system_message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            }
        }
        $loggedUser = $this->session->userdata(MASTER_DIST_SESSION_ID);
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id'],'is_active'=>1,'password'=>$loggedUser['password']))->num_rows();
		if(!$memberDetail)
		{
			$this->session->unset_userdata(MASTER_DIST_SESSION_ID);
			$this->load->helper('language');
			$this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
		}

    }

    public function checkDistributorPermission($mode = DISTRIBUTOR_SESSION_ID, $is_front = false) {
        
        $user = $this->session->userdata($mode);
        
		$this->lang->load('front', 'english');
        if (!$user) {
            // Load language
            
            $this->load->helper('language');

            if ($is_front === false) {
                $this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            } else {
                $this->session->set_flashdata('system_message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            }
        }
        $loggedUser = $this->session->userdata(DISTRIBUTOR_SESSION_ID);
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id'],'is_active'=>1,'password'=>$loggedUser['password']))->num_rows();
		if(!$memberDetail)
		{
			$this->session->unset_userdata(DISTRIBUTOR_SESSION_ID);
			$this->load->helper('language');
			$this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
		}

    }

    public function checkRetailerPermission($mode = RETAILER_SESSION_ID, $is_front = false) {
        
        $user = $this->session->userdata($mode);
        
		$this->lang->load('front', 'english');
        if (!$user) {
            // Load language
            
            $this->load->helper('language');

            if ($is_front === false) {
                $this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            } else {
                $this->session->set_flashdata('system_message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            }
        }
        $loggedUser = $this->session->userdata(RETAILER_SESSION_ID);
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id'],'is_active'=>1,'password'=>$loggedUser['password']))->num_rows();
		if(!$memberDetail)
		{
			$this->session->unset_userdata(RETAILER_SESSION_ID);
			$this->load->helper('language');
			$this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
		}

    }


    public function checkUserPermission($mode = USER_SESSION_ID, $is_front = false) {
        
        $user = $this->session->userdata($mode);
        
		$this->lang->load('front', 'english');
        if (!$user) {
            // Load language
            
            $this->load->helper('language');

            if ($is_front === false) {
                $this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            } else {
                $this->session->set_flashdata('system_message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            }
        }
        $loggedUser = $this->session->userdata(USER_SESSION_ID);
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id'],'is_active'=>1,'password'=>$loggedUser['password']))->num_rows();
		if(!$memberDetail)
		{
			$this->session->unset_userdata(USER_SESSION_ID);
			$this->load->helper('language');
			$this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
		}

    }


    public function checkApiMemberPermission($mode = API_MEMBER_SESSION_ID, $is_front = false) {
        
        $user = $this->session->userdata($mode);
        
		$this->lang->load('front', 'english');
        if (!$user) {
            // Load language
            
            $this->load->helper('language');

            if ($is_front === false) {
                $this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            } else {
                $this->session->set_flashdata('system_message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            }
        }
        $loggedUser = $this->session->userdata(API_MEMBER_SESSION_ID);
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id'],'is_active'=>1,'password'=>$loggedUser['password']))->num_rows();
		if(!$memberDetail)
		{
			$this->session->unset_userdata(API_MEMBER_SESSION_ID);
			$this->load->helper('language');
			$this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
		}

    }


    public function checkEmployePermission($mode = ADMIN_EMPLOYE_SESSION_ID, $is_front = false) {
        
        $user = $this->session->userdata($mode);
		$this->lang->load('front', 'english');
        if (!$user) {
            // Load language
            $currLang = $this->session->userdata('language');
            $this->lang->load('employe/dashboard', $currLang);
            $this->load->helper('language');

            if ($is_front === false) {
                $this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            } else {
                $this->session->set_flashdata('system_message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            }
        }
        $loggedUser = $this->session->userdata(ADMIN_EMPLOYE_SESSION_ID);
		$memberDetail = $this->db->get_where('users',array('id'=>$loggedUser['id'],'is_active'=>1))->num_rows();
		if(!$memberDetail)
		{
			$this->session->unset_userdata(ADMIN_EMPLOYE_SESSION_ID);
			$currLang = $this->session->userdata('language');
            $this->lang->load('employe/dashboard', $currLang);
			$this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
		}
 

    }


    
    public function getLoggedUser($sessionID = ''){
        
		$user = $this->session->userdata($sessionID);
		
        if(!$user){
	   		 redirect('superadmin/Login');
            return false;
        }
       if($user){
            $user = $this->db->get_where('users',array('id'=>$user['id']))->row_array();
            return $user;
        }

        

    }

    public function getAdminLoggedUser($sessionID = ''){
        
		$user = $this->session->userdata($sessionID);
		
        if(!$user){
	   		 redirect('login');
            return false;
        }
       if($user){
            $user = $this->db->select('id,account_id,role_id,user_code,name,is_active,mobile')->get_where('users',array('id'=>$user['id']))->row_array();
            return $user;
        }

        

    }
	
	public function generate_unique_member_id($role_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$account_code = $accountData['account_code'];
		if($role_id == 3)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'MD';;
			}
			else
			{
				$user_display_id = MASTER_DIST_DISPLAY_ID;
			}
		}
		elseif($role_id == 4)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'D';;
			}
			else
			{
				$user_display_id = DISTRIBUTOR_DISPLAY_ID;
			}
		}
		elseif($role_id == 5)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'R';;
			}
			else
			{
				$user_display_id = RETAILER_DISPLAY_ID;
			}
		}
		elseif($role_id == 6)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'API';;
			}
			else
			{
				$user_display_id = API_DISPLAY_ID;
			}
		}
		elseif($role_id == 8)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'U';;
			}
			else
			{
				$user_display_id = USER_DISPLAY_ID;
			}
		}
		elseif($role_id == 9)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'EMP';;
			}
			else
			{
				$user_display_id = USER_DISPLAY_ID;
			}
		}
		$this->load->helper('string');
		$user_display_number = random_string('numeric',6);
		$user_display_id.=$user_display_number;
		
		// check member id already registered or not
		$chk_member_id = $this->db->get_where('users',array('account_id'=>$account_id,'user_code'=>$user_display_id))->num_rows();
		if($chk_member_id)
		{
			$user_display_id = $this->generate_new_unique_member_id($role_id);
		}
		return $user_display_id;
	}
	
	public function generate_new_unique_member_id($role_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$account_code = $accountData['account_code'];
		if($role_id == 3)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'MD';;
			}
			else
			{
				$user_display_id = MASTER_DIST_DISPLAY_ID;
			}
		}
		elseif($role_id == 4)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'D';;
			}
			else
			{
				$user_display_id = DISTRIBUTOR_DISPLAY_ID;
			}
		}
		elseif($role_id == 5)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'R';;
			}
			else
			{
				$user_display_id = RETAILER_DISPLAY_ID;
			}
		}
		elseif($role_id == 6)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'API';;
			}
			else
			{
				$user_display_id = API_DISPLAY_ID;
			}
		}
		elseif($role_id == 8)
		{
			if($account_code)
			{
				$user_display_id = $account_code.'U';;
			}
			else
			{
				$user_display_id = USER_DISPLAY_ID;
			}
		}
		
		$this->load->helper('string');
		$user_display_number = random_string('numeric',6);
		$user_display_id.=$user_display_number;
		
		// check member id already registered or not
		$chk_member_id = $this->db->get_where('users',array('account_id'=>$account_id,'user_code'=>$user_display_id))->num_rows();
		if($chk_member_id)
		{
			$user_display_id = $this->generate_new_unique_member_id($role_id);
		}
		return $user_display_id;
	}
	
	public function generate_unique_admin_id($account_code = '')
	{
		if($account_code)
		{
			$user_display_id = $account_code.'A';
		}
		else
		{
			$user_display_id = ADMIN_DISPLAY_ID;
		}
		$this->load->helper('string');
		$user_display_number = random_string('numeric',6);
		$user_display_id.=$user_display_number;
		
		// check member id already registered or not
		$chk_member_id = $this->db->get_where('users',array('user_code'=>$user_display_id))->num_rows();
		if($chk_member_id)
		{
			$user_display_id = $this->generate_new_unique_admin_id($account_code);
		}
		return $user_display_id;
	}
	
	public function generate_new_unique_admin_id($account_code = '')
	{
		if($account_code)
		{
			$user_display_id = $account_code.'A';
		}
		else
		{
			$user_display_id = ADMIN_DISPLAY_ID;
		}
		$this->load->helper('string');
		$user_display_number = random_string('numeric',6);
		$user_display_id.=$user_display_number;
		
		// check member id already registered or not
		$chk_member_id = $this->db->get_where('users',array('user_code'=>$user_display_id))->num_rows();
		if($chk_member_id)
		{
			$user_display_id = $this->generate_new_unique_admin_id($account_code);
		}
		return $user_display_id;
	}

	public function get_domain_account()
	{
		$domain_name = str_replace('www.','',$_SERVER['SERVER_NAME']);
		
		// get domain id
		$check_domain = $this->db->get_where('account',array('domain_url'=>$domain_name,'status'=>1))->num_rows();
		if(!$check_domain)
		{
			redirect('errorpage');
		}

		// get domain id
		$get_domain_id = $this->db->get_where('account',array('domain_url'=>$domain_name,'status'=>1))->row_array();

		return isset($get_domain_id['id']) ? $get_domain_id['id'] : 1;
		
	}

	public function get_account_data($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->get_where('account',array('id'=>$account_id))->row_array();
		return $get_domain_id;
	}

	public function get_account_instantpay_api_status($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->get_where('account_custom_api_permission',array('account_id'=>$account_id,'api_id'=>1,'status'=>1))->num_rows();
		return $get_domain_id;
	}

	public function get_nsdl_pancard_status($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->get_where('users',array('id'=>$account_id,'is_nsdl_active'=>1))->num_rows();
		return $get_domain_id;
	}

	public function get_account_instantpay_bbps_api_status($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->get_where('account_custom_api_permission',array('account_id'=>$account_id,'api_id'=>2,'status'=>1))->num_rows();
		return $get_domain_id;
	}

	public function get_account_name($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->get_where('account',array('id'=>$account_id))->row_array();
		return $get_domain_id['title'];
	}

	public function get_account_copyright_msg()
	{
		$account_id = $this->User->get_domain_account();
		// get domain id
		$get_domain_id = $this->db->get_where('account',array('id'=>$account_id))->row_array();
		return $get_domain_id['title'].' 2021-2022';
	}

	public function get_account_package_id($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('package_id')->get_where('account',array('id'=>$account_id))->row_array();
		return ($get_domain_id['package_id']) ? $get_domain_id['package_id'] : 0;
	}

	public function getMemberWalletBalanceSP($account_id = 0, $walletType = 1)
	{
		$callStoreProc = "CALL getWalletBalance(?,?)";
        $queryData = array('member_id' => $account_id,'walletType'=>$walletType);
        $procQuery = $this->db->query($callStoreProc, $queryData);
        $procResponse = $procQuery->row_array();
        //add this two line 
        $procQuery->next_result(); 
        $procQuery->free_result(); 
        $before_wallet_balance = isset($procResponse['actualBalance']) ? $procResponse['actualBalance'] : 0 ;
		return $before_wallet_balance;
	}

	public function getAccountWalletBalanceSP($account_id = 0, $walletType = 1, $roleID = 0)
	{
		$callStoreProc = "CALL getAccountWalletBalance(?,?,?)";
        $queryData = array('member_id' => $account_id,'walletType'=>$walletType,'roleID'=>$roleID);
        $procQuery = $this->db->query($callStoreProc, $queryData);
        $procResponse = $procQuery->row_array();
        //add this two line 
        $procQuery->next_result(); 
        $procQuery->free_result(); 
        $before_wallet_balance = isset($procResponse['actualBalance']) ? $procResponse['actualBalance'] : 0 ;
		return $before_wallet_balance;
	}

	public function getMemberWalletBalance($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('wallet_balance')->get_where('users',array('id'=>$account_id))->row_array();
		return isset($get_domain_id['wallet_balance']) ? $get_domain_id['wallet_balance'] : 0;
	}

	public function getMemberEwalletBalance($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('aeps_wallet_balance')->get_where('users',array('id'=>$account_id))->row_array();
		return isset($get_domain_id['aeps_wallet_balance']) ? $get_domain_id['aeps_wallet_balance'] : 0;
	}

	public function getMemberCollectionWalletBalanceSP($account_id = 0, $walletType = 1)
	{
		$callStoreProc = "CALL getCollectionWalletBalance(?,?)";
        $queryData = array('member_id' => $account_id,'walletType'=>$walletType);
        $procQuery = $this->db->query($callStoreProc, $queryData);
        $procResponse = $procQuery->row_array();
        //add this two line 
        $procQuery->next_result(); 
        $procQuery->free_result(); 
        $before_wallet_balance = isset($procResponse['actualBalance']) ? $procResponse['actualBalance'] : 0 ;
		return $before_wallet_balance;
	}

	public function getMemberCollectionWalletBalance($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('collection_wallet_balance')->get_where('users',array('id'=>$account_id))->row_array();
		return isset($get_domain_id['collection_wallet_balance']) ? $get_domain_id['collection_wallet_balance'] : 0;
	}

	public function getMemberVirtualWalletBalanceSP($account_id = 0, $walletType = 1)
	{
		$callStoreProc = "CALL getVirtualWalletBalance(?,?)";
        $queryData = array('member_id' => $account_id,'walletType'=>$walletType);
        $procQuery = $this->db->query($callStoreProc, $queryData);
        $procResponse = $procQuery->row_array();
        //add this two line 
        $procQuery->next_result(); 
        $procQuery->free_result(); 
        $before_wallet_balance = isset($procResponse['actualBalance']) ? $procResponse['actualBalance'] : 0 ;
		return $before_wallet_balance;
	}

	public function getMemberVirtualWalletBalance($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('virtual_wallet_balance')->get_where('users',array('id'=>$account_id))->row_array();
		return isset($get_domain_id['virtual_wallet_balance']) ? $get_domain_id['virtual_wallet_balance'] : 0;
	}

	public function getAdminAepsCommisionBlance($domain_account_id = 0)
	{
		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}
		// get domain id
		$get_domain_id = $this->db->select('SUM(wallet_settle_amount) as totalBalance')->get_where('member_aeps_comm',array('account_id'=>$domain_account_id,'is_paid'=>0))->row_array();
		return isset($get_domain_id['totalBalance']) ? $get_domain_id['totalBalance'] : 0;
	}

	public function getAccountWiseAepsCommisionBlance($domain_account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('SUM(wallet_settle_amount) as totalBalance')->get_where('member_aeps_comm',array('member_id'=>$domain_account_id,'is_paid'=>0))->row_array();
		return isset($get_domain_id['totalBalance']) ? $get_domain_id['totalBalance'] : 0;
	}

	public function getSuperadminAepsCommisionBlance()
	{
		$domain_account_id = $this->User->get_domain_account();
		// get domain id
		$get_domain_id = $this->db->select('SUM(wallet_settle_amount) as totalBalance')->get_where('member_aeps_comm',array('is_paid'=>0))->row_array();
		return isset($get_domain_id['totalBalance']) ? $get_domain_id['totalBalance'] : 0;
	}

	public function getMemberAepsCommisionBlance($account_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		// get domain id
		$get_domain_id = $this->db->select('SUM(wallet_settle_amount) as totalBalance')->get_where('member_aeps_comm',array('account_id'=>$domain_account_id,'member_id'=>$account_id,'is_paid'=>0))->row_array();
		return isset($get_domain_id['totalBalance']) ? $get_domain_id['totalBalance'] : 0;
	}

	public function getAccountCollectionWalletBalanceSP($account_id = 0, $walletType = 1, $roleID = 0)
	{
		$callStoreProc = "CALL getAccountCollectionWalletBalance(?,?,?)";
        $queryData = array('member_id' => $account_id,'walletType'=>$walletType,'roleID'=>$roleID);
        $procQuery = $this->db->query($callStoreProc, $queryData);
        $procResponse = $procQuery->row_array();
        //add this two line 
        $procQuery->next_result(); 
        $procQuery->free_result(); 
        $before_wallet_balance = isset($procResponse['actualBalance']) ? $procResponse['actualBalance'] : 0 ;
		return $before_wallet_balance;
	}

	public function getSuperadminCollectionWalletBalance($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('SUM(collection_wallet_balance) as totalBalance')->get_where('users',array('role_id'=>2))->row_array();
		return isset($get_domain_id['totalBalance']) ? $get_domain_id['totalBalance'] : 0;
	}

	public function getMasterBBPSWalletBalance($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('bbps_balance')->get_where('users',array('id'=>1))->row_array();
		return isset($get_domain_id['bbps_balance']) ? $get_domain_id['bbps_balance'] : 0;
	}

	public function getMemberPackageID($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('package_id')->get_where('users',array('id'=>$account_id))->row_array();
		return isset($get_domain_id['package_id']) ? $get_domain_id['package_id'] : 0;
	}

	public function getMemberRoleID($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id))->row_array();
		return isset($get_domain_id['role_id']) ? $get_domain_id['role_id'] : 0;
	}

	public function getMemberUPIID($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('upi_id')->get_where('users',array('id'=>$account_id))->row_array();
		return isset($get_domain_id['upi_id']) ? $get_domain_id['upi_id'] : '';
	}

	public function getMemberVirtualAccountStatus($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('is_virtual_account')->get_where('users',array('id'=>$account_id))->row_array();
		return isset($get_domain_id['is_virtual_account']) ? $get_domain_id['is_virtual_account'] : 0;
	}

	public function get_distributor_id($user_id = 0, $domain_account_id = 0)
	{
		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}
		// get account role id
		$get_role_id = $this->db->select('role_id,created_by')->get_where('users',array('id'=>$user_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		$md_id = isset($get_role_id['created_by']) ? $get_role_id['created_by'] : 0 ;
		
		// check return id rold
		$get_return_id_role = $this->db->select('role_id')->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
		$return_id_role = isset($get_return_id_role['role_id']) ? $get_return_id_role['role_id'] : 0 ;
		if($return_id_role == 2)
		{
			$md_id = 0;
		}

		return $md_id;


	}

	public function get_member_downline_id($user_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		// get account role id
		$get_role_id = $this->db->select('GROUP_CONCAT(id) as member_id_str')->get_where('users',array('created_by'=>$user_id,'account_id'=>$domain_account_id))->row_array();
		$md_id = isset($get_role_id['member_id_str']) ? explode(',',$get_role_id['member_id_str']) : array() ;
		$md_idd = array();
		$md_iddd = array();
		if($md_id)
		{
			// get account role id
			$get_role_idd = $this->db->select('GROUP_CONCAT(id) as member_id_str')->where_in('created_by',$md_id)->get_where('users',array('account_id'=>$domain_account_id))->row_array();
			$md_idd = isset($get_role_idd['member_id_str']) ? explode(',',$get_role_idd['member_id_str']) : array() ;
			if($md_idd)
			{
				// get account role id
				$get_role_iddd = $this->db->select('GROUP_CONCAT(id) as member_id_str')->where_in('created_by',$md_idd)->get_where('users',array('account_id'=>$domain_account_id))->row_array();
				$md_iddd = isset($get_role_iddd['member_id_str']) ? explode(',',$get_role_iddd['member_id_str']) : array() ;
			}
		}

		$member_id_str = implode(',', $md_id).','.implode(',', $md_idd).','.implode(',', $md_iddd);

		return array_filter(explode(',', $member_id_str));


	}

	public function get_member_sponser_data($sponser_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		// get account role id
		$get_role_id = $this->db->select('id,role_id,user_code,name')->get_where('users',array('id'=>$sponser_id,'account_id'=>$domain_account_id))->row_array();
		return $get_role_id;


	}

	public function get_master_distributor_id($user_id = 0,$domain_account_id = 0)
	{
		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}
		// get account role id
		$get_role_id = $this->db->select('role_id,created_by')->get_where('users',array('id'=>$user_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		$created_by = isset($get_role_id['created_by']) ? $get_role_id['created_by'] : 0 ;
		$md_id = 0;
		if($user_role_id == 4)
		{
			$md_id = $created_by;
		}
		elseif($user_role_id == 5)
		{
			// get account role id
			$get_id = $this->db->select('role_id,created_by')->get_where('users',array('id'=>$created_by,'account_id'=>$domain_account_id))->row_array();
			$created_by = isset($get_id['created_by']) ? $get_id['created_by'] : 0 ;
			$md_id = $created_by;
		}

		// check return id rold
		$get_return_id_role = $this->db->select('role_id')->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
		$return_id_role = isset($get_return_id_role['role_id']) ? $get_return_id_role['role_id'] : 0 ;
		if($return_id_role == 2)
		{
			$md_id = 0;
		}
		
		return $md_id;


	}

	public function get_admin_id($domain_account_id = 0)
	{
		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}
		// get account role id
		$get_role_id = $this->db->select('id')->get_where('users',array('role_id'=>2,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['id']) ? $get_role_id['id'] : 0 ;
		return $user_role_id;


	}

	public function get_admin_wallet_balance($admin_id = 0,$domain_account_id = 0)
	{
		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}
		// get account role id
		$get_role_id = $this->db->select('wallet_balance')->get_where('users',array('id'=>$admin_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['wallet_balance']) ? $get_role_id['wallet_balance'] : 0 ;
		return $user_role_id;


	}

	public function get_admin_ewallet_balance($admin_id = 0,$domain_account_id = 0)
	{
		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}
		// get account role id
		$get_role_id = $this->db->select('aeps_wallet_balance')->get_where('users',array('id'=>$admin_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['aeps_wallet_balance']) ? $get_role_id['aeps_wallet_balance'] : 0 ;
		return $user_role_id;


	}


	public function account_active_service($account_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		$activeService = $this->db->select('account_user_services.*')->get_where('account_user_services',array('account_id'=>$domain_account_id,'member_id'=>$account_id,'status'=>1))->result_array();

	 	$activeServiceID = array();
	 	if($activeService)
	 	{
	 		foreach($activeService as $key=>$list)
	 		{
	 			$activeServiceID[$key] = $list['service_id'];
	 		}
	 	}
	 	return $activeServiceID;
	}

	public function admin_active_service($account_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		$activeService = $this->db->select('account_services.*')->get_where('account_services',array('account_id'=>$domain_account_id,'status'=>1))->result_array();
		$activeServiceID = array();
	 	if($activeService)
	 	{
	 		foreach($activeService as $key=>$list)
	 		{
	 			$activeServiceID[$key] = $list['service_id'];
	 		}
	 	}
	 	return $activeServiceID;
	}

	public function account_active_gateway()
	{
		$domain_account_id = $this->User->get_domain_account();
		$activeService = $this->db->select('account_payment_gateway.*')->get_where('account_payment_gateway',array('account_id'=>$domain_account_id,'status'=>1))->result_array();

	 	$activeServiceID = array();
	 	if($activeService)
	 	{
	 		foreach($activeService as $key=>$list)
	 		{
	 			$activeServiceID[$key] = $list['gateway_id'];
	 		}
	 	}
	 	return $activeServiceID;
	}

	public function get_member_aeps_status($member_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$domain_account_id,'aeps_status'=>1))->num_rows();
		return $chk_member;
	}

	public function get_member_new_aeps_status($member_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$domain_account_id,'new_aeps_status'=>1))->num_rows();
		return $chk_member;
	}
	
		public function get_member_2fa_aeps_status($member_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('aeps_member_registration',array('member_id'=>$member_id,'account_id'=>$domain_account_id,'status'=>1))->num_rows();
		return $chk_member;
	}
	
	public function get_member_2fa_aeps_login_status($member_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('aeps_member_login_status',array('member_id'=>$member_id,'account_id'=>$domain_account_id,'status'=>1))->num_rows();
		return $chk_member;
	}



	public function get_member_instantpay_aeps_status($member_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();
		$chk_member = $this->db->get_where('users',array('id'=>$member_id,'account_id'=>$domain_account_id,'instantpay_aeps_status'=>1))->num_rows();
		return $chk_member;
	}

	


	public function account_razorpay_key()
	{
		$domain_account_id = $this->User->get_domain_account();
		$activeService = $this->db->select('account_payment_gateway.*')->get_where('account_payment_gateway',array('account_id'=>$domain_account_id,'gateway_id'=>1,'status'=>1))->row_array();
		
		$key = isset($activeService['gateway_key']) ? $activeService['gateway_key'] : '';
		$secret = isset($activeService['gateway_secret']) ? $activeService['gateway_secret'] : '';

		return array('key'=>$key,'secret'=>$secret);
	}

	public function generateLog($log_msg = '')
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		if($accountData['account_type'] == 2)
		{
			$account_id = SUPERADMIN_ACCOUNT_ID;
		}
		$log_file = 'Account-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg,'a+');
		return true;
	}

	public function generateAepsLog($log_msg = '')
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		if($accountData['account_type'] == 2)
		{
			$account_id = SUPERADMIN_ACCOUNT_ID;
		}
		$log_file = 'Aeps-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg.'[break]','a+');
		return true;
	}

	public function generateDMTLog($log_msg = '')
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
		$log_file = 'DMT-Account-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg.'[break]','a+');
		return true;
	}

	public function generateNSDLLog($log_msg = '')
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
		$log_file = 'NSDL-Account-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg.'[break]','a+');
		return true;
	}

	public function generateVANLog($log_msg = '')
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
		$log_file = 'VAN-Account-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg.'[break]','a+');
		return true;
	}

	public function generateMATMLog($log_msg = '')
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
		$log_file = 'MATM-Account-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg.'[break]','a+');
		return true;
	}

	public function generateCallbackLog($log_msg = '')
	{
		$account_id = $this->User->get_domain_account();
		$log_file = 'Callback-Account-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg,'a+');
		return true;
	}

	public function generateBBPSLog($log_msg = '')
	{
		$account_id = $this->User->get_domain_account();
		$log_file = 'BBPS-Account-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg,'a+');
		return true;
	}

	public function generateSettlementLog($log_msg = '')
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
		$log_file = 'Settlement-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg.'[break]','a+');
		return true;
	}

	public function generateCoduniteSettlementLog($log_msg = '',$account_id = 0)
	{
		$log_file = 'Settlement-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg.'[break]','a+');
		return true;
	}

	public function generateAccountActivityLog($log_msg = '')
	{
		$account_id = $this->User->get_domain_account();
		$log_file = 'Activity-Account-'.$account_id.'-'.date('d-M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg,'a+');
		return true;
	}

	public function generateUpiCollectionLog($log_msg = '')
	{
		$account_id = $this->User->get_domain_account();
		$log_file = 'UPI-Account-'.$account_id.'-'.date('d-M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg.'[break]','a+');
		return true;
	}

	public function generatePayoutLog($log_msg = '')
	{
		$account_id = $this->User->get_domain_account();
		$log_file = 'Payout-Account-'.$account_id.'-'.date('d-M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg.'[break]','a+');
		return true;
	}

	public function generateAPIUserLog($log_msg = '')
	{
		$account_id = SUPERADMIN_ACCOUNT_ID;
		$log_file = 'API-Account-'.$account_id.'-'.date('M-Y').'.php';
		write_file(ACCOUNT_LOG_PATH.$log_file,$log_msg,'a+');
		return true;
	}

	public function saveUserLoginLog($account_id = 0, $ip_address = '', $sessionData = array(), $postData = array())
	{
		$loginData = array(
			'account_id' => $account_id,
			'ip_address' => $ip_address,
			'post_json' => json_encode($postData),
			'session_json' => json_encode($sessionData),
			'created' => date('Y-m-d H:i:s')
		);
		$this->db->insert('login_log',$loginData);
		return true;
	}

	public function get_user_ip()
	{
		return $_SERVER['REMOTE_ADDR'];
	}
	

	public function prepaid_rechage_api($api_url,$api_post_data,$account_id,$recharge_unique_id,$api_id,$response_type = 0,$responsePara = array(),$seperator = '',$api_header_data = array(),$user_code = '',$user_type = '')
	{
		$domain_account_id = $this->User->get_domain_account();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		if($api_post_data && !$api_header_data)
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));		
		}
		if($api_header_data)
		{
			
            $headers = array();
            $h = 0;
            foreach($api_header_data as $hkey=>$hval)
            {
                $headers[$h] = $hkey.':'.$hval;
                $h++;
            }
            if($headers)
            {
	        	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    	}
	    	if($api_post_data)
			{
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
			}
		}
		$output = curl_exec($ch); 
		curl_close($ch);

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - API Response - '.$output.']'.PHP_EOL;
        $this->User->generateLog($log_msg);

        log_message('debug', 'Recharge Auth API - API Response - '.$output);
		
		// save api response
		$apiData = array(
			'account_id' => $domain_account_id,
			'user_id' => $account_id,
			'recharge_id' => $recharge_unique_id,
			'api_response' => $output,
			'api_url' => $api_url,
			'api_id' => $api_id,
			'status' => 1,
			'created' => date('Y-m-d H:i:s')
		);
		$this->db->insert('api_response',$apiData);
		$api_response_id = $this->db->insert_id();
		$txid = '';
		$recharge_status = '';
		$success_value = array();
		$failed_value = array();
		$pending_value = array();
		$opt_msg = '';
		$opt_ref_id = '';
		$timestamp = '';
		$memberid = '';
		$balance = '';
		$commision = '';
		if($response_type == 1)
		{
			$api_response = explode($seperator,$output);
			if($responsePara)
			{
				foreach($responsePara as $key=>$val)
				{
					if($val['value'] == 'TXNID')
					{
						$txid = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'STATUS')
					{
						$recharge_status = isset($api_response[$key]) ? trim(strtolower($api_response[$key])) : '';
						$success_value = array_filter(explode(',', $val['success']),'strlen');
						$failed_value = array_filter(explode(',', $val['failed']),'strlen');
						$pending_value = array_filter(explode(',', $val['pending']),'strlen');
					}
					elseif($val['value'] == 'OPTMSG')
					{
						$opt_msg = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'OPTREFID')
					{
						$opt_ref_id = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'TIMESTAMP')
					{
						$timestamp = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'MEMBERID')
					{
						$memberid = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'BALANCE')
					{
						$balance = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'COMMISION')
					{
						$commision = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
				}
			}

		}
		elseif($response_type == 2)
		{
			$api_response = (array) simplexml_load_string($output);
			if($responsePara)
			{
				foreach($responsePara as $key=>$val)
				{
					if($val['value'] == 'TXNID')
					{
						$txid = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'STATUS')
					{
						$recharge_status = isset($api_response[$val['key']]) ? trim(strtolower($api_response[$val['key']])) : '';
						$success_value = array_filter(explode(',', $val['success']),'strlen');
						$failed_value = array_filter(explode(',', $val['failed']),'strlen');
						$pending_value = array_filter(explode(',', $val['pending']),'strlen');
					}
					elseif($val['value'] == 'OPTMSG')
					{
						$opt_msg = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'OPTREFID')
					{
						$opt_ref_id = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'TIMESTAMP')
					{
						$timestamp = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'MEMBERID')
					{
						$memberid = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'BALANCE')
					{
						$balance = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'COMMISION')
					{
						$commision = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
				}
			}

		}
		elseif($response_type == 3)
		{
			$api_response = json_decode($output,true);
			if($responsePara)
			{
				foreach($responsePara as $key=>$val)
				{
					if($val['value'] == 'TXNID')
					{
						$txid = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'STATUS')
					{
						$recharge_status = isset($api_response[$val['key']]) ? trim(strtolower($api_response[$val['key']])) : '';
						
						$success_value = array_filter(explode(',', $val['success']),'strlen');
						$failed_value = array_filter(explode(',', $val['failed']),'strlen');
						$pending_value = array_filter(explode(',', $val['pending']),'strlen');
					}
					elseif($val['value'] == 'OPTMSG')
					{
						$opt_msg = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'OPTREFID')
					{
						$opt_ref_id = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'TIMESTAMP')
					{
						$timestamp = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'MEMBERID')
					{
						$memberid = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'BALANCE')
					{
						$balance = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'COMMISION')
					{
						$commision = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
				}
			}

		}

		
		
		$status = 0;
		if(in_array($recharge_status, $failed_value))
		{
			$status = 3;
		}
		elseif(in_array($recharge_status, $pending_value) || $recharge_status == '')
		{
			$status = 1;
		}
		elseif(in_array($recharge_status, $success_value))
		{
			$status = 2;
		}
		else
		{
			$status = 1;
		}
		
		
		return array(
			'status' => $status,
			'txid' => $txid,
			'operator_ref' => $opt_ref_id,
			'api_timestamp' => $timestamp,
			'opt_msg' => $opt_msg,
			'memberid' => $memberid,
			'balance' => $balance,
			'commision' => $commision,
			'api_response_id' => $api_response_id,
		);
		
	}

	public function instantpay_rechage_api($opt_code,$account_id,$recharge_unique_id,$mobile,$amount,$api_id,$user_code = '',$user_type = '')
	{
		$domain_account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($domain_account_id);

		$userData = $this->db->select('instantpay_outlet_id,mobile')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$outlet_id = $userData['instantpay_outlet_id'];
		$memberMobile = $userData['mobile'];

		$getAadharData = $this->db->select('mobile,aadhar_data')->get_where('instantpay_ekyc',array('account_id'=>$domain_account_id,'member_id'=>$account_id,'status'=>2))->row_array();
		$agentMobile = $getAadharData['mobile'];
		$aadhar_data = isset($getAadharData['aadhar_data']) ? json_decode($getAadharData['aadhar_data'],true) : array();
		$pincode = isset($aadhar_data['pincode']) ? $aadhar_data['pincode'] : '';
		
		$api_url = INSTANTPAY_TXN_API;

        $request = array(
            'token' => $accountData['instant_token'],
            'request' => array(
                'request_type' => 'PAYMENT',
                'outlet_id' => $outlet_id,
                'biller_id' => $opt_code,
                'reference_txnid' => array(
                    'agent_external' => $recharge_unique_id,
                    'billfetch_internal' => "",
                    'validate_internal' => ""
                ),
                'params' => array(
                    'param1' => $mobile,
                    'param2' => ""
               ),
               'payment_channel' => 'AGT',
               'payment_mode' => 'Cash',
               'payment_info' => 'bill',
               'device_info' => array(
                   'TERMINAL_ID' => '12813923',
                   'MOBILE' => $agentMobile,
                   'GEOCODE' => '12.1234,12.1234',
                   'POSTAL_CODE' => $pincode,
               ),
               'remarks' => array(
                   'param1' => $memberMobile,
                   'param2' => ""
               ),
               'amount' => $amount
                
            )
        );

        $header = array(
            'content-type: application/json'
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
        
         
        $bmPIData   = simplexml_load_string($output);
		$jsonResponse = json_encode((array) $bmPIData);

		/*$jsonResponse = '{"statuscode":"TXN","status":"Transaction Successful","data":{"response_type":"PAYMENT","ipay_id":"1220518155944GGNWT","biller":"Airtel","biller_refid":"91164209","value_order":"10.00","value_commercial":"0.0590","type_pricing":"MARGIN","value_tds":"0.0030","convenience_fee":"0.00","value_transaction":"9.94","transaction_mode":"DR","params":{"param1":"8104758957"}},"timestamp":"2022-05-18 15:59:49","ipay_uuid":"C5691EFCA4E98F2BF65B","orderid":"1220518155944GGNWT","environment":"PRODUCTION"}';*/

		$decodeResponse = json_decode($jsonResponse,true);

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Final API URL - '.$api_url.' - Post Data - '.json_encode($request).']'.PHP_EOL;
        $this->User->generateLog($log_msg);

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - API Response - '.$jsonResponse.']'.PHP_EOL;
        $this->User->generateLog($log_msg);

		// save api response
		$apiData = array(
			'account_id' => $domain_account_id,
			'user_id' => $account_id,
			'recharge_id' => $recharge_unique_id,
			'api_response' => $output,
			'api_url' => $api_url,
			'api_id' => $api_id,
			'status' => 1,
			'created' => date('Y-m-d H:i:s')
		);
		$this->db->insert('api_response',$apiData);
		$api_response_id = $this->db->insert_id();

		//save api data
        $apiData = array(
          'account_id' => $domain_account_id,
          'user_id' => $account_id,
          'api_url' => $api_url,
          'api_response' => $output,
          'post_data' => json_encode($request),
          'header_data' => json_encode($header),
          'created' => date('Y-m-d H:i:s'),
          'created_by' => $account_id
        );
        $this->db->insert('instantpay_api_response',$apiData);


		$txid = '';
		$recharge_status = '';
		$success_value = array();
		$failed_value = array();
		$pending_value = array();
		$opt_msg = '';
		$opt_ref_id = '';
		$timestamp = '';
		$memberid = '';
		$balance = '';
		$commision = '';
		
		$status = 0;
		if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
		{
			$status = 2;
			$txid = $recharge_unique_id;
			$opt_ref_id = $decodeResponse['orderid'];
			$opt_msg = $decodeResponse['status'];
		}
		elseif(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TUP')
		{
			$status = 1;
			$txid = $recharge_unique_id;
			$opt_ref_id = $decodeResponse['orderid'];
			$opt_msg = $decodeResponse['status'];
		}
		elseif(!isset($decodeResponse['statuscode']))
		{
			$status = 1;
			$txid = $recharge_unique_id;
			$opt_msg = isset($decodeResponse['status']) ? $decodeResponse['status'] : '';
		}
		else
		{
			$status = 3;
			$txid = $recharge_unique_id;
			$opt_msg = isset($decodeResponse['status']) ? $decodeResponse['status'] : '';
		}
		
		
		return array(
			'status' => $status,
			'txid' => $txid,
			'operator_ref' => $opt_ref_id,
			'api_timestamp' => $timestamp,
			'opt_msg' => $opt_msg,
			'memberid' => $memberid,
			'balance' => $balance,
			'commision' => $commision,
			'api_response_id' => $api_response_id,
		);
		
	}
	
	public function postpaid_rechage_api($mobile,$operator_code,$circle_code,$amount,$recharge_unique_id,$account_id = 0)
	{
		$domain_account_id = $this->User->get_domain_account();

		$accountData = $this->User->get_account_data($domain_account_id);

		$api_member_id = $accountData['dmt_username'];
		$api_member_pin = $accountData['dmt_pin'];	

		$api_url = RECHARGE_API_URL.'memberid='.$api_member_id.'&pin='.$api_member_pin.'&number='.$mobile.'&operator='.$operator_code.'&circle='.$circle_code.'&amount='.$amount.'&account='.$mobile.'&usertx='.$recharge_unique_id;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);	
		$output = curl_exec($ch); 
		curl_close($ch);
		
		// save api response
		$apiData = array(
			'account_id' => $domain_account_id,
			'user_id' => $account_id,
			'mobile' => $mobile,
			'operator' => $operator_code,
			'circle' => $circle_code,
			'amount' => $amount,
			'recharge_id' => $recharge_unique_id,
			'account_no' => $account,
			'api_response' => $output,
			'status' => 1,
			'created' => date('Y-m-d H:i:s')
		);
		$this->db->insert('api_response',$apiData);
		$api_response_id = $this->db->insert_id();
		
		$api_response = explode(',',$output);
		$recharge_status = isset($api_response[1]) ? strtolower($api_response[1]) : '';
		$txid = isset($api_response[0]) ? strtolower($api_response[0]) : '';
		$operator_ref = isset($api_response[3]) ? strtolower($api_response[3]) : '';
		$api_timestamp = isset($api_response[4]) ? strtolower($api_response[4]) : '';
		if($api_timestamp){
			$position = strpos($api_timestamp, 'M');
			$api_timestamp = substr($api_timestamp,0,$position+1);
		}
		$status = 0;
		if($recharge_status == 'failure')
		{
			$status = 3;
		}
		elseif($recharge_status == 'pending')
		{
			$status = 1;
		}
		elseif($recharge_status == 'success' || $recharge_status == '' )
		{
			$status = 2;
		}
		
		return array(
			'status' => $status,
			'txid' => $txid,
			'operator_ref' => $operator_ref,
			'api_timestamp' => $api_timestamp,
			'api_response_id' => $api_response_id
		);
	}
	
	
	public function getElectricityOperatorDetail($operator_code = '')
	{
		$domain_account_id = $this->User->get_domain_account();
		
		$accountData = $this->User->get_account_data($domain_account_id);

		$api_member_id = $accountData['dmt_username'];
		$api_member_pin = $accountData['dmt_pin'];		

		$url = ELECTRICITY_RECHARGE_FETCH_API_URL.'memberid='.$api_member_id.'&pin='.$api_member_pin.'&sp_key='.$operator_code;


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);		
		$output = curl_exec($ch); 
		curl_close($ch);
		
		$api_response = explode('#',$output);
		
		$response = array();
		
		if(isset($api_response[0]) && $api_response[0])
		{
			$status_response = explode(',',$api_response[0]);
			
			if(isset($status_response[1]) && $status_response[1] == 'Success')
			{
				$billDetail = (array) json_decode($api_response[1]);
				if(isset($billDetail['data'][0]->params))
				{
					$billFieldDetail = (array) json_decode($billDetail['data'][0]->params);
					$response = array(
						'status' => 1,
						'msg' => 'Success',
						'fieldName' => $billFieldDetail[0]->name,
						'fieldOther' => $billFieldDetail[0]->Other,
						'minLength' => $billFieldDetail[0]->MinLength,
						'maxLength' => $billFieldDetail[0]->MaxLength,
					);
				}
				else
				{
					$response = array(
						'status' => 0,
						'msg' => 'Operator is not activated.'
					);
				}
				
			}
			else
			{
				$response = array(
					'status' => 0,
					'msg' => 'Operator is not activated.'
				);
			}
			
		}
		else
		{
			$response = array(
				'status' => 0,
				'msg' => 'Operator is not activated.'
			);
		}
		
		return $response;
		
		
	}
	
	public function getElectricityOperatorBillerDetail($operator_code = '',$account_number = '',$account_id = 0)
	{
		
		if($account_id){
			// get user mobile
			$get_user_mobile = $this->db->select('mobile')->get_where('users',array('id'=>$account_id))->row_array();
			$mobile = isset($get_user_mobile['mobile']) ? $get_user_mobile['mobile'] : '';
			

			$url = ELECTRICITY_RECHARGE_FETCH_CUSTOMER_API_URL.'memberid='.RECHARGE_MEMBERID.'&pin='.RECHARGE_API_PIN.'&sp_key='.$operator_code.'&agentid='.$account_id.'&customer_mobile='.$mobile.'&servicenum='.$account_number;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);	
			$output = curl_exec($ch); 
			curl_close($ch);
			
			$api_response = explode('#',$output);
		
			
			$response = array();
			
			if(isset($api_response[0]) && $api_response[0])
			{
				$status_response = explode(',',$api_response[0]);
				
				
				if(isset($status_response[1]) && $status_response[1] == 'Success')
				{
					$billDetail = (array) json_decode($api_response[1]);
					
					if(isset($billDetail['data']->dueamount))
					{
						$response = array(
							'status' => 1,
							'msg' => 'Success',
							'amount' => $billDetail['data']->dueamount,
							'customername' => $billDetail['data']->customername,
							'reference_id' => $billDetail['data']->reference_id,
						);
					}
					else
					{
						$response = array(
							'status' => 0,
							'msg' => 'Biller is not valid.'
						);
					}
					
				}
				else
				{
					$response = array(
						'status' => 0,
						'msg' => 'Biller is not valid.'
					);
				}
				
			}
			else
			{
				$response = array(
					'status' => 0,
					'msg' => 'Biller is not valid.'
				);
			}
		}
		else
		{
			$response = array(
				'status' => 0,
				'msg' => 'Please login for getting the biller detail.'
			);
		}
		
		return $response;
		
		
	}
	
	public function electricity_rechage_api($account_number,$operator_code,$amount,$reference_id,$recharge_unique_id,$account_id = 0,$mobile,$customer_mobile = '')
	{
		$domain_account_id = $this->User->get_domain_account();
		/*$api_url = ELECTRICITY_RECHARGE_API_URL.'memberid='.RECHARGE_MEMBERID.'&pin='.RECHARGE_API_PIN.'&number='.$account_number.'&operator='.$operator_code.'&circle=1&amount='.$amount.'&account='.$reference_id.'&usertx='.$recharge_unique_id.'&format=csv&CustomerMobile='.$customer_mobile;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);		
		$output = curl_exec($ch); 
		curl_close($ch);
		
		// save api response
		$apiData = array(
			'account_id' => $domain_account_id,
			'user_id' => $account_id,
			'recharge_id' => $recharge_unique_id,
			'api_response' => $output,
			'api_url' => $api_url,
			'status' => 1,
			'created' => date('Y-m-d H:i:s')
		);
		$this->db->insert('api_response',$apiData);
		$api_response_id = $this->db->insert_id();
		
		$api_response = explode(',',$output);
		$recharge_status = isset($api_response[1]) ? strtolower($api_response[1]) : '';
		$txid = isset($api_response[0]) ? strtolower($api_response[0]) : '';
		$operator_ref = isset($api_response[3]) ? strtolower($api_response[3]) : '';
		$api_timestamp = isset($api_response[4]) ? strtolower($api_response[4]) : '';
		
		$status = 0;
		if($recharge_status == '' || $recharge_status == 'failed')
		{
			$status = 3;
		}
		elseif($recharge_status == 'pending')
		{
			$status = 1;
		}
		elseif($recharge_status == 'success')
		{
			$status = 2;
		}*/

		$status = 1;
		$txid = '';
		$operator_ref = '';
		$api_timestamp = '';
		$api_response_id = 0;
		
		return array(
			'status' => $status,
			'txid' => $txid,
			'operator_ref' => $operator_ref,
			'api_timestamp' => $api_timestamp,
			'api_response_id' => $api_response_id
		);
		
	}

	public function distribute_recharge_commision($recharge_id, $recharge_unique_id, $amount, $account_id, $domain_account_id = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($account_id);
		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}
		$accountData = $this->User->get_account_data($domain_account_id);

		// check instantpay cogent api
        $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($domain_account_id);

		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account Role ID - '.$user_role_id.']'.PHP_EOL;
        $this->User->generateLog($log_msg);
		if($user_role_id == 3)
		{
			// get operator id
			$get_recharge_data = $this->db->select('recharge_history.api_id,recharge_history.operator_code')->get_where('recharge_history',array('recharge_history.id'=>$recharge_id,'recharge_history.account_id'=>$domain_account_id))->row_array();
			$api_id = isset($get_recharge_data['api_id']) ? $get_recharge_data['api_id'] : 0 ;
			$operator_code = isset($get_recharge_data['operator_code']) ? $get_recharge_data['operator_code'] : '' ;

			$op_id = 0;
			if($api_id && $operator_code)
			{
				if($accountData['account_type'] == 2)
                {
					// get operator id
					$get_op_id = $this->db->select('api_operator.opt_id')->get_where('api_operator',array('api_operator.account_id'=>SUPERADMIN_ACCOUNT_ID,'api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
				}
				else
				{
					// get operator id
					$get_op_id = $this->db->select('api_operator.opt_id')->get_where('api_operator',array('api_operator.account_id'=>$domain_account_id,'api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
				}
				$op_id = isset($get_op_id['opt_id']) ? $get_op_id['opt_id'] : 0 ;
			}



			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Operator ID - '.$op_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($op_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'op_id'=>$op_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['commision'];
					$is_flat = $get_comm['is_flat'];
					$is_surcharge = $get_comm['is_surcharge'];
					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'RECHARGE',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'settlement_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount

			                if($accountData['is_tds_amount'] == 1)
			                {

			                	
                			$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $account_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      => $charge_amount,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'Recharge #'.$recharge_unique_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);




			                }
			                
						}
					}
				}
			}
			if($is_cogent_instantpay_api)
			{
				$admin_id = $this->User->get_admin_id($domain_account_id);
				$admin_package_id = $this->User->get_account_package_id($domain_account_id);

				if($op_id)
				{
					// get recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>SUPERADMIN_ACCOUNT_ID,'package_id'=>$admin_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						
						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$is_surcharge = $get_comm['is_surcharge'];
						if($is_surcharge)
						{
							$charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$charge_amount = $commision;
							}

						}
						else
						{
							$charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$charge_amount = $commision;
							}

						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Charge Amount - '.$charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($charge_amount)
						{
							$is_paid = 0;
				            if($is_surcharge)
				            {
				                $is_paid = 1;
				            }

				            $commData = array(
				                'account_id' => $domain_account_id,
				                'member_id' => $admin_id,
				                'type' => 6,
				                'txnID' => $recharge_unique_id,
				                'amount' => $amount,
				                'com_amount' => $charge_amount,
				                'is_surcharge' => $is_surcharge,
				                'wallet_settle_amount' => $charge_amount,
				                'is_paid' => $is_paid,
				                'status' => 1,
				                'created'             => date('Y-m-d H:i:s'),      
				                'created_by'         => $account_id,
				            );
				            $this->db->insert('member_aeps_comm',$commData);
							
							//get member wallet_balance
				            $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
				            if($is_surcharge)
				            {
				                $after_wallet_balance = $before_wallet_balance - $charge_amount;

				                $wallet_data = array(
				                    'account_id'          => $domain_account_id,
				                    'member_id'           => $admin_id,    
				                    'before_balance'      => $before_wallet_balance,
				                    'amount'              => $charge_amount,  
				                    'after_balance'       => $after_wallet_balance,      
				                    'status'              => 1,
				                    'type'                => 2,   
				                    'wallet_type'         => 1,   
				                    'created'             => date('Y-m-d H:i:s'),      
				                    'description'         => 'Recharge Txn #'.$recharge_unique_id.' Charge Amount Debited.'
				                );

				                $this->db->insert('collection_wallet',$wallet_data);

				                
				            }
						}
					}
				}
			}
		}
		elseif($user_role_id == 6)
		{
			// get operator id
			$get_recharge_data = $this->db->select('recharge_history.api_id,recharge_history.operator_code')->get_where('recharge_history',array('recharge_history.id'=>$recharge_id,'recharge_history.account_id'=>$domain_account_id))->row_array();
			$api_id = isset($get_recharge_data['api_id']) ? $get_recharge_data['api_id'] : 0 ;
			$operator_code = isset($get_recharge_data['operator_code']) ? $get_recharge_data['operator_code'] : '' ;

			$op_id = 0;
			if($api_id && $operator_code)
			{
				if($accountData['account_type'] == 2)
                {
					// get operator id
					$get_op_id = $this->db->select('api_operator.opt_id')->get_where('api_operator',array('api_operator.account_id'=>SUPERADMIN_ACCOUNT_ID,'api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
				}
				else
				{
					// get operator id
					$get_op_id = $this->db->select('api_operator.opt_id')->get_where('api_operator',array('api_operator.account_id'=>$domain_account_id,'api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
				}
				$op_id = isset($get_op_id['opt_id']) ? $get_op_id['opt_id'] : 0 ;
			}

			

			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Operator ID - '.$op_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($op_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'op_id'=>$op_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['api_commision'];
					$is_flat = $get_comm['api_is_flat'];
					$is_surcharge = $get_comm['api_is_surcharge'];
					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'RECHARGE',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'settlement_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               

			                //deduct tds amount
			                 if($accountData['is_tds_amount'] == 1)
			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			                $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $account_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      => $charge_amount,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'Recharge #'.$recharge_unique_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

			            }

						}
					}
				}

				

			}
			if($is_cogent_instantpay_api)
			{
				$admin_id = $this->User->get_admin_id($domain_account_id);
				$admin_package_id = $this->User->get_account_package_id($domain_account_id);

				if($op_id)
				{
					// get recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>SUPERADMIN_ACCOUNT_ID,'package_id'=>$admin_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						
						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$is_surcharge = $get_comm['is_surcharge'];
						if($is_surcharge)
						{
							$charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$charge_amount = $commision;
							}

						}
						else
						{
							$charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$charge_amount = $commision;
							}

						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Charge Amount - '.$charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($charge_amount)
						{
							$is_paid = 0;
				            if($is_surcharge)
				            {
				                $is_paid = 1;
				            }

				            $commData = array(
				                'account_id' => $domain_account_id,
				                'member_id' => $admin_id,
				                'type' => 6,
				                'txnID' => $recharge_unique_id,
				                'amount' => $amount,
				                'com_amount' => $charge_amount,
				                'is_surcharge' => $is_surcharge,
				                'wallet_settle_amount' => $charge_amount,
				                'is_paid' => $is_paid,
				                'status' => 1,
				                'created'             => date('Y-m-d H:i:s'),      
				                'created_by'         => $account_id,
				            );
				            $this->db->insert('member_aeps_comm',$commData);
							
							//get member wallet_balance
				            $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
				            if($is_surcharge)
				            {
				                $after_wallet_balance = $before_wallet_balance - $charge_amount;

				                $wallet_data = array(
				                    'account_id'          => $domain_account_id,
				                    'member_id'           => $admin_id,    
				                    'before_balance'      => $before_wallet_balance,
				                    'amount'              => $charge_amount,  
				                    'after_balance'       => $after_wallet_balance,      
				                    'status'              => 1,
				                    'type'                => 2,   
				                    'wallet_type'         => 1,   
				                    'created'             => date('Y-m-d H:i:s'),      
				                    'description'         => 'Recharge Txn #'.$recharge_unique_id.' Charge Amount Debited.'
				                );

				                $this->db->insert('collection_wallet',$wallet_data);

				            }
						}
					}
				}
			}
		}
		elseif($user_role_id == 4)
		{
			// get operator id
			$get_recharge_data = $this->db->select('recharge_history.api_id,recharge_history.operator_code')->get_where('recharge_history',array('recharge_history.id'=>$recharge_id,'recharge_history.account_id'=>$domain_account_id))->row_array();
			$api_id = isset($get_recharge_data['api_id']) ? $get_recharge_data['api_id'] : 0 ;
			$operator_code = isset($get_recharge_data['operator_code']) ? $get_recharge_data['operator_code'] : '' ;

			$op_id = 0;
			if($api_id && $operator_code)
			{
				if($accountData['account_type'] == 2)
                {
					// get operator id
					$get_op_id = $this->db->select('api_operator.opt_id')->get_where('api_operator',array('api_operator.account_id'=>SUPERADMIN_ACCOUNT_ID,'api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
				}
				else
				{
					// get operator id
					$get_op_id = $this->db->select('api_operator.opt_id')->get_where('api_operator',array('api_operator.account_id'=>$domain_account_id,'api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
				}
				$op_id = isset($get_op_id['opt_id']) ? $get_op_id['opt_id'] : 0 ;
			}

			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Operator ID - '.$op_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($op_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'op_id'=>$op_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['dt_commision'];
					$is_flat = $get_comm['dt_is_flat'];
					$is_surcharge = $get_comm['dt_is_surcharge'];

					$total_distribute_amount = 0;

					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
						$total_distribute_amount+=$charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'RECHARGE',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'settlement_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount

			                 if($accountData['is_tds_amount'] == 1)
			                {

			                $before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Pay Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			                $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $account_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      => $charge_amount,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'Recharge #'.$recharge_unique_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);



			            	}
						}
					}
				}

				
				$md_id = $this->User->get_master_distributor_id($account_id);
				if($md_id)
				{
					$md_package_id = $this->User->getMemberPackageID($md_id);

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Master Distributor ID - '.$md_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$md_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						// debit wallet
	                	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$md_is_surcharge = $get_comm['is_surcharge'];
						if($md_is_surcharge)
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}

						}
						else
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}
							
						}
						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && !$md_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount + $md_charge_amount;
						}
						elseif($is_surcharge && $md_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $md_charge_amount;
							if($charge_amount < $md_charge_amount)
							{
								$deduct_charge_amount = $md_charge_amount - $charge_amount;
							}
							$total_distribute_amount+=$remaining_charge_amount;
						}
						else
						{
							if($md_charge_amount >= $charge_amount)
							{
								$remaining_charge_amount = $md_charge_amount - $charge_amount;
								$total_distribute_amount+=$remaining_charge_amount;
							}
							else
							{
								$remaining_charge_amount = $charge_amount - $md_charge_amount;
								$deduct_charge_amount = $charge_amount - $md_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Charge Amount - '.$md_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'RECHARGE',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $md_is_surcharge,
								'commision_amount' => $md_charge_amount,
								'settlement_amount' => $remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount

			                 if($accountData['is_tds_amount'] == 1)
			                {
			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($md_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.'Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			                $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      => $charge_amount,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'Recharge #'.$recharge_unique_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);


			            }
							
						}
						elseif($remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'RECHARGE',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $md_is_surcharge,
								'commision_amount' => $md_charge_amount,
								'settlement_amount' => $deduct_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
							
						}
					}
				}

				
			}
			if($is_cogent_instantpay_api)
			{
				$admin_id = $this->User->get_admin_id($domain_account_id);
				$admin_package_id = $this->User->get_account_package_id($domain_account_id);

				if($op_id)
				{
					// get recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>SUPERADMIN_ACCOUNT_ID,'package_id'=>$admin_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						
						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$is_surcharge = $get_comm['is_surcharge'];
						if($is_surcharge)
						{
							$charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$charge_amount = $commision;
							}

						}
						else
						{
							$charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$charge_amount = $commision;
							}

						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Charge Amount - '.$charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($charge_amount)
						{
							$is_paid = 0;
				            if($is_surcharge)
				            {
				                $is_paid = 1;
				            }

				            $commData = array(
				                'account_id' => $domain_account_id,
				                'member_id' => $admin_id,
				                'type' => 6,
				                'txnID' => $recharge_unique_id,
				                'amount' => $amount,
				                'com_amount' => $charge_amount,
				                'is_surcharge' => $is_surcharge,
				                'wallet_settle_amount' => $charge_amount,
				                'is_paid' => $is_paid,
				                'status' => 1,
				                'created'             => date('Y-m-d H:i:s'),      
				                'created_by'         => $account_id,
				            );
				            $this->db->insert('member_aeps_comm',$commData);
							
							//get member wallet_balance
				            $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
				            if($is_surcharge)
				            {
				                $after_wallet_balance = $before_wallet_balance - $charge_amount;

				                $wallet_data = array(
				                    'account_id'          => $domain_account_id,
				                    'member_id'           => $admin_id,    
				                    'before_balance'      => $before_wallet_balance,
				                    'amount'              => $charge_amount,  
				                    'after_balance'       => $after_wallet_balance,      
				                    'status'              => 1,
				                    'type'                => 2,   
				                    'wallet_type'         => 1,   
				                    'created'             => date('Y-m-d H:i:s'),      
				                    'description'         => 'Recharge Txn #'.$recharge_unique_id.' Charge Amount Debited.'
				                );

				                $this->db->insert('collection_wallet',$wallet_data);

				            }
						}
					}
				}
			}
		}
		elseif($user_role_id == 5)
		{
			// get operator id
			$get_recharge_data = $this->db->select('recharge_history.api_id,recharge_history.operator_code')->get_where('recharge_history',array('recharge_history.id'=>$recharge_id,'recharge_history.account_id'=>$domain_account_id))->row_array();
			$api_id = isset($get_recharge_data['api_id']) ? $get_recharge_data['api_id'] : 0 ;
			$operator_code = isset($get_recharge_data['operator_code']) ? $get_recharge_data['operator_code'] : '' ;

			$op_id = 0;
			if($api_id && $operator_code)
			{
				if($accountData['account_type'] == 2)
                {
					// get operator id
					$get_op_id = $this->db->select('api_operator.opt_id')->get_where('api_operator',array('api_operator.account_id'=>SUPERADMIN_ACCOUNT_ID,'api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
				}
				else
				{
					// get operator id
					$get_op_id = $this->db->select('api_operator.opt_id')->get_where('api_operator',array('api_operator.account_id'=>$domain_account_id,'api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
				}
				$op_id = isset($get_op_id['opt_id']) ? $get_op_id['opt_id'] : 0 ;
			}

			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Operator ID - '.$op_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($op_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'op_id'=>$op_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);
                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['rt_commision'];
					$is_flat = $get_comm['rt_is_flat'];
					$is_surcharge = $get_comm['rt_is_surcharge'];

					$total_distribute_amount = 0;

					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
						$total_distribute_amount+=$charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'RECHARGE',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'settlement_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount

			                 if($accountData['is_tds_amount'] == 1)
			                {

			                $before_tds_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_tds_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_tds_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.'Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);


			                $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $account_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      => $charge_amount,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'Recharge #'.$recharge_unique_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);



			            	}
						}
					}
				}

				
				$distributor_id = $this->User->get_distributor_id($account_id,$domain_account_id);

				if($distributor_id)
				{
					$distributor_package_id = $this->User->getMemberPackageID($distributor_id);

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor ID - '.$distributor_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					// get master distribution recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$distributor_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($get_comm)
					{
						// debit wallet
	                	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);
						$commision = $get_comm['dt_commision'];
						$is_flat = $get_comm['dt_is_flat'];
						$dist_is_surcharge = $get_comm['dt_is_surcharge'];
						if($dist_is_surcharge)
						{
							$dist_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$dist_charge_amount = $commision;
							}

						}
						else
						{
							$dist_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$dist_charge_amount = $commision;
							}
							
						}
						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && $dist_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $dist_charge_amount;
							if($charge_amount < $dist_charge_amount)
							{
								$deduct_charge_amount = $dist_charge_amount - $charge_amount;
							}
							$total_distribute_amount+=$remaining_charge_amount;
						}
						else
						{
							if($dist_charge_amount >= $charge_amount)
							{
								$remaining_charge_amount = $dist_charge_amount - $charge_amount;
								$total_distribute_amount+=$remaining_charge_amount;
							}
							else
							{
								$remaining_charge_amount = $charge_amount - $dist_charge_amount;
								$deduct_charge_amount = $charge_amount - $dist_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor Charge Amount - '.$dist_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);
						

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'RECHARGE',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $remaining_charge_amount,
								'commision_amount' => $dist_charge_amount,
								'settlement_amount' => $remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount
			                 if($accountData['is_tds_amount'] == 1)
			                {
			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.'Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $distributor_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      => $remaining_charge_amount,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'Recharge #'.$recharge_unique_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);


							
							}
						}
						elseif($remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'RECHARGE',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $deduct_charge_amount,
								'commision_amount' => $dist_charge_amount,
								'settlement_amount' => $deduct_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
							
						}
					}

					$total_downline_com = $charge_amount + $remaining_charge_amount;
					$md_id = $this->User->get_master_distributor_id($distributor_id,$domain_account_id);

					$md_package_id = $this->User->getMemberPackageID($md_id);

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Master Distributor ID - '.$md_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$md_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						// debit wallet
	                	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);
						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$md_is_surcharge = $get_comm['is_surcharge'];
						if($md_is_surcharge)
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}

						}
						else
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}
							
						}
						$md_remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && $md_is_surcharge && $dist_is_surcharge)
						{
							$md_remaining_charge_amount = $dist_charge_amount - $md_charge_amount;
							if($dist_charge_amount < $md_charge_amount)
							{
								$deduct_charge_amount = $md_charge_amount - $dist_charge_amount;
							}
							$total_distribute_amount+=$md_remaining_charge_amount;
						}
						else
						{
							if($md_charge_amount >= $total_downline_com)
							{
								$md_remaining_charge_amount = $md_charge_amount - $total_downline_com;
								$total_distribute_amount+=$md_remaining_charge_amount;
							}
							else
							{
								$md_remaining_charge_amount = $total_downline_com - $md_charge_amount;
								$deduct_charge_amount = $total_downline_com - $md_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Charge Amount - '.$md_charge_amount.' - Credit Amount - '.$md_remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						
						if($md_remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $md_remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'RECHARGE',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $md_is_surcharge,
								'commision_amount' => $md_charge_amount,
								'settlement_amount' => $md_remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $md_remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               


			                //deduct tds amount

			                 if($accountData['is_tds_amount'] == 1)
			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($md_id);

			                $tds_amount = $md_remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.'Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      => $md_remaining_charge_amount,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'Recharge #'.$recharge_unique_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

			            }
							
						}
						elseif($md_remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'RECHARGE',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $md_is_surcharge,
								'commision_amount' => $md_charge_amount,
								'settlement_amount' => $deduct_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Recharge #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
					}
				}

				

			}
			if($is_cogent_instantpay_api)
			{
				$admin_id = $this->User->get_admin_id($domain_account_id);
				$admin_package_id = $this->User->get_account_package_id($domain_account_id);

				if($op_id)
				{
					// get recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>SUPERADMIN_ACCOUNT_ID,'package_id'=>$admin_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						
						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$is_surcharge = $get_comm['is_surcharge'];
						if($is_surcharge)
						{
							$charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$charge_amount = $commision;
							}

						}
						else
						{
							$charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$charge_amount = $commision;
							}

						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Charge Amount - '.$charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($charge_amount)
						{
							$is_paid = 0;
				            if($is_surcharge)
				            {
				                $is_paid = 1;
				            }

				            $commData = array(
				                'account_id' => $domain_account_id,
				                'member_id' => $admin_id,
				                'type' => 6,
				                'txnID' => $recharge_unique_id,
				                'amount' => $amount,
				                'com_amount' => $charge_amount,
				                'is_surcharge' => $is_surcharge,
				                'wallet_settle_amount' => $charge_amount,
				                'is_paid' => $is_paid,
				                'status' => 1,
				                'created'             => date('Y-m-d H:i:s'),      
				                'created_by'         => $account_id,
				            );
				            $this->db->insert('member_aeps_comm',$commData);
							
							//get member wallet_balance
				            $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
				            if($is_surcharge)
				            {
				                $after_wallet_balance = $before_wallet_balance - $charge_amount;

				                $wallet_data = array(
				                    'account_id'          => $domain_account_id,
				                    'member_id'           => $admin_id,    
				                    'before_balance'      => $before_wallet_balance,
				                    'amount'              => $charge_amount,  
				                    'after_balance'       => $after_wallet_balance,      
				                    'status'              => 1,
				                    'type'                => 2,   
				                    'wallet_type'         => 1,   
				                    'created'             => date('Y-m-d H:i:s'),      
				                    'description'         => 'Recharge Txn #'.$recharge_unique_id.' Charge Amount Debited.'
				                );

				                $this->db->insert('collection_wallet',$wallet_data);

				                
				            }
						}
					}
				}
			}
		}
		
	}

	public function distribute_electricity_commision($recharge_id, $recharge_unique_id, $amount, $account_id, $domain_account_id = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($account_id);
		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}

		$accountData = $this->User->get_account_data($domain_account_id);
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account Role ID - '.$user_role_id.']'.PHP_EOL;
        $this->User->generateLog($log_msg);
		if($user_role_id == 3 || $user_role_id == 6)
		{
			// get operator id
			$get_recharge_data = $this->db->select('recharge_history.operator_code')->get_where('recharge_history',array('recharge_history.id'=>$recharge_id,'recharge_history.account_id'=>$domain_account_id))->row_array();
			$operator_code = isset($get_recharge_data['operator_code']) ? $get_recharge_data['operator_code'] : '' ;

			$op_id = 0;
			if($operator_code)
			{
				// get operator id
				$get_op_id = $this->db->select('operator.id')->get_where('operator',array('operator.operator_code'=>$operator_code))->row_array();
				$op_id = isset($get_op_id['id']) ? $get_op_id['id'] : 0 ;
			}

			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Operator ID - '.$op_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($op_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'op_id'=>$op_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['commision'];
					$is_flat = $get_comm['is_flat'];
					$is_surcharge = $get_comm['is_surcharge'];
					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'ELECTRICITY',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                 

			                //deduct tds amount

			                if($accountData['is_tds_amount'] == 1)

			                {
			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);


			               }
						}
					}
				}
			}
			
		}
		elseif($user_role_id == 4)
		{
			// get operator id
			$get_recharge_data = $this->db->select('recharge_history.api_id,recharge_history.operator_code')->get_where('recharge_history',array('recharge_history.id'=>$recharge_id,'recharge_history.account_id'=>$domain_account_id))->row_array();
			$api_id = isset($get_recharge_data['api_id']) ? $get_recharge_data['api_id'] : 0 ;
			$operator_code = isset($get_recharge_data['operator_code']) ? $get_recharge_data['operator_code'] : '' ;

			$op_id = 0;
			if($operator_code)
			{
				// get operator id
				$get_op_id = $this->db->select('operator.id')->get_where('operator',array('operator.operator_code'=>$operator_code))->row_array();
				$op_id = isset($get_op_id['id']) ? $get_op_id['id'] : 0 ;
			}

			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Operator ID - '.$op_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($op_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'op_id'=>$op_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['commision'];
					$is_flat = $get_comm['is_flat'];
					$is_surcharge = $get_comm['is_surcharge'];
					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'ELECTRICITY',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount

			                if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			            	}

						}
					}
				}

				
				$md_id = $this->User->get_master_distributor_id($account_id);
				if($md_id)
				{
					$md_package_id = $this->User->getMemberPackageID($md_id);

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Master Distributor ID - '.$md_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$md_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						// debit wallet
	                	
	                	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$md_is_surcharge = $get_comm['is_surcharge'];
						if($md_is_surcharge)
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}

						}
						else
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}
							
						}
						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && !$md_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount + $md_charge_amount;
						}
						elseif($is_surcharge && $md_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $md_charge_amount;
							if($charge_amount < $md_charge_amount)
							{
								$deduct_charge_amount = $md_charge_amount - $charge_amount;
							}
						}
						else
						{
							if($md_charge_amount > $charge_amount)
							{
								$remaining_charge_amount = $md_charge_amount - $charge_amount;
							}
							else
							{
								$remaining_charge_amount = $charge_amount - $md_charge_amount;
								$deduct_charge_amount = $charge_amount - $md_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Charge Amount - '.$md_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'ELECTRICITY',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $remaining_charge_amount,
								'commision_amount' => $md_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount


			                if($accountData['is_tds_amount'] == 1)

			                {

			                $before_balance = $this->User->getMemberWalletBalanceSP($md_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               

			                 } 
							
						}
						elseif($remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'ELECTRICITY',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $deduct_charge_amount,
								'commision_amount' => $md_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

							
						}
					}
				}
			}
			
		}
		elseif($user_role_id == 5)
		{
			// get operator id
			$get_recharge_data = $this->db->select('recharge_history.api_id,recharge_history.operator_code')->get_where('recharge_history',array('recharge_history.id'=>$recharge_id,'recharge_history.account_id'=>$domain_account_id))->row_array();
			$api_id = isset($get_recharge_data['api_id']) ? $get_recharge_data['api_id'] : 0 ;
			$operator_code = isset($get_recharge_data['operator_code']) ? $get_recharge_data['operator_code'] : '' ;

			$op_id = 0;
			if($operator_code)
			{
				// get operator id
				$get_op_id = $this->db->select('operator.id')->get_where('operator',array('operator.operator_code'=>$operator_code))->row_array();
				$op_id = isset($get_op_id['id']) ? $get_op_id['id'] : 0 ;
			}

			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Operator ID - '.$op_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($op_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'op_id'=>$op_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);
                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['commision'];
					$is_flat = $get_comm['is_flat'];
					$is_surcharge = $get_comm['is_surcharge'];
					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'ELECTRICITY',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount

			                	if($accountData['is_tds_amount'] == 1)

			                {

			                $before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                 } 
						}
					}
				}

				
				$distributor_id = $this->User->get_distributor_id($account_id);
				if($distributor_id)
				{

					$distributor_package_id = $this->User->getMemberPackageID($distributor_id);

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor ID - '.$distributor_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					// get master distribution recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$distributor_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($get_comm)
					{
						// debit wallet
	                	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);
						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$dist_is_surcharge = $get_comm['is_surcharge'];
						if($dist_is_surcharge)
						{
							$dist_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$dist_charge_amount = $commision;
							}

						}
						else
						{
							$dist_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$dist_charge_amount = $commision;
							}
							
						}
						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && $dist_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $dist_charge_amount;
							if($charge_amount < $dist_charge_amount)
							{
								$deduct_charge_amount = $dist_charge_amount - $charge_amount;
							}
						}
						else
						{
							if($dist_charge_amount > $charge_amount)
							{
								$remaining_charge_amount = $dist_charge_amount - $charge_amount;
							}
							else
							{
								$remaining_charge_amount = $charge_amount - $dist_charge_amount;
								$deduct_charge_amount = $charge_amount - $dist_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor Charge Amount - '.$dist_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);
						

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'ELECTRICITY',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $remaining_charge_amount,
								'commision_amount' => $dist_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);


			                //deduct tds amount


			                if($accountData['is_tds_amount'] == 1)

			                {


			                $before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);


			            	}
							
						}
						elseif($remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'ELECTRICITY',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $deduct_charge_amount,
								'commision_amount' => $dist_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
							
						}
					}

					$total_downline_com = $charge_amount + $remaining_charge_amount;
					$md_id = $this->User->get_master_distributor_id($distributor_id);

					$md_package_id = $this->User->getMemberPackageID($md_id);

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Master Distributor ID - '.$md_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('recharge_commision',array('account_id'=>$domain_account_id,'package_id'=>$md_package_id,'op_id'=>$op_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						// debit wallet
	                	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);
						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$md_is_surcharge = $get_comm['is_surcharge'];
						if($md_is_surcharge)
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}

						}
						else
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}
							
						}
						$md_remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && $md_is_surcharge && $dist_is_surcharge)
						{
							$md_remaining_charge_amount = $dist_charge_amount - $md_charge_amount;
							if($dist_charge_amount < $md_charge_amount)
							{
								$deduct_charge_amount = $md_charge_amount - $dist_charge_amount;
							}
						}
						else
						{
							if($md_charge_amount > $total_downline_com)
							{
								$md_remaining_charge_amount = $md_charge_amount - $total_downline_com;
							}
							else
							{
								$md_remaining_charge_amount = $total_downline_com - $md_charge_amount;
								$deduct_charge_amount = $total_downline_com - $md_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Charge Amount - '.$md_charge_amount.' - Credit Amount - '.$md_remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						
						if($md_remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $md_remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'ELECTRICITY',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $md_remaining_charge_amount,
								'commision_amount' => $md_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $md_remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                


			                //deduct tds amount


			                	if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($md_id);

			                $tds_amount = $md_remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                 } 
							
						}
						elseif($md_remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'ELECTRICITY',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $deduct_charge_amount,
								'commision_amount' => $md_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.' Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
					}
				}
			}
		}
		
	}

	public function distribute_bbps_commision($recharge_id, $recharge_unique_id, $amount, $account_id)
	{

		$member_package_id = $this->User->getMemberPackageID($account_id);

		$domain_account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($domain_account_id);
		// check instantpay cogent api
        $is_cogent_instantpay_api = $this->User->get_admin_instant_cogent_api($domain_account_id);

		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;

		// save system log
        $log_msg = '['.date('d-m-Y H:i:s').' - Account Role ID - '.$user_role_id.']'.PHP_EOL;
        $this->User->generateLog($log_msg);
		if($user_role_id == 3)
		{
			// get operator id
			$get_recharge_data = $this->db->select('bbps_history.service_id')->get_where('bbps_history',array('bbps_history.id'=>$recharge_id,'bbps_history.account_id'=>$domain_account_id))->row_array();
			$service_id = isset($get_recharge_data['service_id']) ? $get_recharge_data['service_id'] : 0 ;
			
			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Service ID - '.$service_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($service_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('bbps_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'service_id'=>$service_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['commision'];
					$is_flat = $get_comm['is_flat'];
					$is_surcharge = $get_comm['is_surcharge'];
					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'BBPS',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'settlement_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                 

			                //deduct tds amount
			                if($accountData['is_tds_amount'] == 1)

			                {

			                $before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill  #'.$recharge_unique_id.'  Pay  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                 } 
						}
					}
				}

				if($accountData['account_type'] == 2){

					$admin_id = $this->User->get_admin_id($domain_account_id);
					$admin_package_id = $this->User->get_account_package_id($domain_account_id);
					
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin ID - '.$admin_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('bbps_commision',array('package_id'=>$admin_package_id,'service_id'=>$service_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
	        		if($get_comm)
					{
						// debit wallet
	                	
	                	$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$admin_is_surcharge = $get_comm['is_surcharge'];
						if($admin_is_surcharge)
						{
							$admin_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$admin_charge_amount = $commision;
							}

						}
						else
						{
							$admin_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$admin_charge_amount = $commision;
							}
							
						}
						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && !$admin_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount + $admin_charge_amount;
						}
						elseif($is_surcharge && $admin_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $admin_charge_amount;
							if($charge_amount < $admin_charge_amount)
							{
								$deduct_charge_amount = $admin_charge_amount - $charge_amount;
							}
						}
						else
						{
							if($admin_charge_amount > $charge_amount)
							{
								$remaining_charge_amount = $admin_charge_amount - $charge_amount;
							}
							else
							{
								$remaining_charge_amount = $charge_amount - $admin_charge_amount;
								$deduct_charge_amount = $charge_amount - $admin_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Charge Amount - '.$admin_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $admin_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $admin_is_surcharge,
								'commision_amount' => $admin_charge_amount,
								'settlement_amount' => $remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,     
				                'wallet_type'         => 1,   
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount

			                if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.'  Pay  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			            	}
							
						}
						elseif($remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $admin_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $admin_is_surcharge,
								'commision_amount' => $admin_charge_amount,
								'settlement_amount' => $deduct_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'wallet_type'         => 1,  
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

							
						}
					}
				}

			}
			
		}
		elseif($user_role_id == 6)
		{
			// get operator id
			$get_recharge_data = $this->db->select('bbps_history.service_id')->get_where('bbps_history',array('bbps_history.id'=>$recharge_id,'bbps_history.account_id'=>$domain_account_id))->row_array();
			$service_id = isset($get_recharge_data['service_id']) ? $get_recharge_data['service_id'] : 0 ;
			
			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Service ID - '.$service_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($service_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('bbps_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'service_id'=>$service_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['api_commision'];
					$is_flat = $get_comm['api_is_flat'];
					$is_surcharge = $get_comm['api_is_surcharge'];
					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'BBPS',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'settlement_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,   
				                'wallet_type'         => 1,         
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                


			                //deduct tds amount
			                if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.'  Pay Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               


			                	}

						}
					}
				}

				if($accountData['account_type'] == 2){

					$admin_id = $this->User->get_admin_id($domain_account_id);
					$admin_package_id = $this->User->get_account_package_id($domain_account_id);
					
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin ID - '.$admin_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('bbps_commision',array('package_id'=>$admin_package_id,'service_id'=>$service_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
	        		if($get_comm)
					{
						// debit wallet
	                	$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$admin_is_surcharge = $get_comm['is_surcharge'];
						if($admin_is_surcharge)
						{
							$admin_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$admin_charge_amount = $commision;
							}

						}
						else
						{
							$admin_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$admin_charge_amount = $commision;
							}
							
						}
						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && !$admin_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount + $admin_charge_amount;
						}
						elseif($is_surcharge && $admin_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $admin_charge_amount;
							if($charge_amount < $admin_charge_amount)
							{
								$deduct_charge_amount = $admin_charge_amount - $charge_amount;
							}
						}
						else
						{
							if($admin_charge_amount > $charge_amount)
							{
								$remaining_charge_amount = $admin_charge_amount - $charge_amount;
							}
							else
							{
								$remaining_charge_amount = $charge_amount - $admin_charge_amount;
								$deduct_charge_amount = $charge_amount - $admin_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Charge Amount - '.$admin_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $admin_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $admin_is_surcharge,
								'commision_amount' => $admin_charge_amount,
								'settlement_amount' => $remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,     
				                'wallet_type'         => 1,    
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			                //deduct tds amount

			                	if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                


			            		}
							
						}
						elseif($remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $admin_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $admin_is_surcharge,
								'commision_amount' => $admin_charge_amount,
								'settlement_amount' => $deduct_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,    
				                'wallet_type'         => 1,    
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
							
						}
					}
				}

			}
			
		}
		elseif($user_role_id == 4)
		{
			// get operator id
			$get_recharge_data = $this->db->select('bbps_history.service_id')->get_where('bbps_history',array('bbps_history.id'=>$recharge_id,'bbps_history.account_id'=>$domain_account_id))->row_array();
			$service_id = isset($get_recharge_data['service_id']) ? $get_recharge_data['service_id'] : 0 ;
			
			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Service ID - '.$service_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($service_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('bbps_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'service_id'=>$service_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['dt_commision'];
					$is_flat = $get_comm['dt_is_flat'];
					$is_surcharge = $get_comm['dt_is_surcharge'];

					$total_distribute_amount = 0;

					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
						$total_distribute_amount+=$charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'BBPS',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'settlement_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'wallet_type'         => 1,  
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'         => 1,        
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount


			                	if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                 } 
						}
					}
				}

				
				$md_id = $this->User->get_master_distributor_id($account_id);
				if($md_id)
				{
					$md_package_id = $this->User->getMemberPackageID($md_id);

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Master Distributor ID - '.$md_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('bbps_commision',array('account_id'=>$domain_account_id,'package_id'=>$md_package_id,'service_id'=>$service_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						// debit wallet
	                	
	                	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$md_is_surcharge = $get_comm['is_surcharge'];
						if($md_is_surcharge)
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}

						}
						else
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}
							
						}
						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && !$md_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount + $md_charge_amount;
						}
						elseif($is_surcharge && $md_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $md_charge_amount;
							if($charge_amount < $md_charge_amount)
							{
								$deduct_charge_amount = $md_charge_amount - $charge_amount;
							}
							$total_distribute_amount+=$remaining_charge_amount;
						}
						else
						{
							if($md_charge_amount >= $charge_amount)
							{
								$remaining_charge_amount = $md_charge_amount - $charge_amount;
								$total_distribute_amount+=$remaining_charge_amount;
							}
							else
							{
								$remaining_charge_amount = $charge_amount - $md_charge_amount;
								$deduct_charge_amount = $charge_amount - $md_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Charge Amount - '.$md_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $md_is_surcharge,
								'commision_amount' => $md_charge_amount,
								'settlement_amount' => $remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,   
				                'wallet_type'         => 1,     
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount
			                if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($md_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
								

								}
						}
						elseif($remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $md_is_surcharge,
								'commision_amount' => $md_charge_amount,
								'settlement_amount' => $deduct_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'wallet_type'         => 1,  
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
							
						}
					}
				}

				if($accountData['account_type'] == 2){

					$admin_id = $this->User->get_admin_id($domain_account_id);
					$admin_package_id = $this->User->get_account_package_id($domain_account_id);
					
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin ID - '.$admin_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('bbps_commision',array('package_id'=>$admin_package_id,'service_id'=>$service_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
	        		if($get_comm)
					{
						// debit wallet
	                	
	                	$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$admin_is_surcharge = $get_comm['is_surcharge'];
						if($admin_is_surcharge)
						{
							$admin_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$admin_charge_amount = $commision;
							}

						}
						else
						{
							$admin_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$admin_charge_amount = $commision;
							}
							
						}


						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && $admin_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $admin_charge_amount;
							$remaining_charge_amount = $remaining_charge_amount - $total_distribute_amount;
						}
						else
						{
							if($admin_charge_amount >= $total_distribute_amount)
							{
								$remaining_charge_amount = $admin_charge_amount - $total_distribute_amount;
							}
							else
							{
								$remaining_charge_amount = $total_distribute_amount - $admin_charge_amount;
								$deduct_charge_amount = $total_distribute_amount - $admin_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Charge Amount - '.$admin_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $admin_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $admin_is_surcharge,
								'commision_amount' => $admin_charge_amount,
								'settlement_amount' => $remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,   
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount

			                if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Electricity #'.$recharge_unique_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			                  }
							
						}
						
					}
				}
			}
			
		}
		elseif($user_role_id == 5)
		{
			// get operator id
			$get_recharge_data = $this->db->select('bbps_history.service_id')->get_where('bbps_history',array('bbps_history.id'=>$recharge_id,'bbps_history.account_id'=>$domain_account_id))->row_array();
			$service_id = isset($get_recharge_data['service_id']) ? $get_recharge_data['service_id'] : 0 ;
			
			// save system log
        	$log_msg = '['.date('d-m-Y H:i:s').' - Service ID - '.$service_id.']'.PHP_EOL;
        	$this->User->generateLog($log_msg);

			if($service_id)
			{
				// get recharge commision
				$get_comm = $this->db->get_where('bbps_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'service_id'=>$service_id))->row_array();
				// save system log
        		$log_msg = '['.date('d-m-Y H:i:s').' - Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
        		$this->User->generateLog($log_msg);
				if($get_comm)
				{
					// debit wallet
                	
                	$before_balance = $this->User->getMemberWalletBalanceSP($account_id);
                	// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Wallet Balance - '.$before_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					$commision = $get_comm['rt_commision'];
					$is_flat = $get_comm['rt_is_flat'];
					$is_surcharge = $get_comm['rt_is_surcharge'];

					$total_distribute_amount = 0;

					if($is_surcharge)
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance - $charge_amount;

					}
					else
					{
						$charge_amount = round(($commision/100)*$amount,2);
						if($is_flat)
						{
							$charge_amount = $commision;
						}

						$after_balance = $before_balance + $charge_amount;
					}

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Charge Amount - '.$charge_amount.' - After Wallet - '.$after_balance.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($charge_amount)
					{

						$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $account_id,
							'type' => 'BBPS',
							'record_id' => $recharge_id,
							'commision' => $commision,
							'is_flat' => $is_flat,
							'is_surcharge' => $is_surcharge,
							'commision_amount' => $charge_amount,
							'settlement_amount' => $charge_amount,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);
						if($is_surcharge)
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,      
				                'wallet_type'         => 1,  
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
						}
						else
						{
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,      
				                'wallet_type'         => 1,        
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //deduct tds amount


			                if($accountData['is_tds_amount'] == 1)

			                {


                			$before_balance = $this->User->getMemberWalletBalanceSP($account_id);

			                $tds_amount = $charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $account_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.'  Pay  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);


			            	}
						}
					}
				}

				
				$distributor_id = $this->User->get_distributor_id($account_id);
				if($distributor_id)
				{
					$distributor_package_id = $this->User->getMemberPackageID($distributor_id);

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor ID - '.$distributor_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					// get master distribution recharge commision
					$get_comm = $this->db->get_where('bbps_commision',array('account_id'=>$domain_account_id,'package_id'=>$distributor_package_id,'service_id'=>$service_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);

					if($get_comm)
					{
						// debit wallet
	                	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);
						$commision = $get_comm['dt_commision'];
						$is_flat = $get_comm['dt_is_flat'];
						$dist_is_surcharge = $get_comm['dt_is_surcharge'];
						if($dist_is_surcharge)
						{
							$dist_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$dist_charge_amount = $commision;
							}

						}
						else
						{
							$dist_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$dist_charge_amount = $commision;
							}
							
						}
						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && $dist_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $dist_charge_amount;
							if($charge_amount < $dist_charge_amount)
							{
								$deduct_charge_amount = $dist_charge_amount - $charge_amount;
							}
							$total_distribute_amount+=$remaining_charge_amount;
						}
						else
						{
							if($dist_charge_amount > $charge_amount)
							{
								$remaining_charge_amount = $dist_charge_amount - $charge_amount;
								$total_distribute_amount+=$remaining_charge_amount;
							}
							else
							{
								$remaining_charge_amount = $charge_amount - $dist_charge_amount;
								$deduct_charge_amount = $charge_amount - $dist_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Distributor Charge Amount - '.$dist_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);
						

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $dist_is_surcharge,
								'commision_amount' => $dist_charge_amount,
								'settlement_amount' => $remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,    
				                'wallet_type'         => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			                //deduct tds amount


			                if($accountData['is_tds_amount'] == 1)

			                {
			                
			                $before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay   Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			            }
							
						}
						elseif($remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'RECHARGE',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $dist_is_surcharge,
								'commision_amount' => $dist_charge_amount,
								'settlement_amount' => $deduct_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,   
				                'wallet_type'         => 1,     
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
							
						}
					}

					$total_downline_com = $charge_amount + $remaining_charge_amount;
					$md_id = $this->User->get_master_distributor_id($distributor_id);

					$md_package_id = $this->User->getMemberPackageID($md_id);

					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Master Distributor ID - '.$md_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('bbps_commision',array('account_id'=>$domain_account_id,'package_id'=>$md_package_id,'service_id'=>$service_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					if($get_comm)
					{
						// debit wallet
	                	
	                	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);
						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$md_is_surcharge = $get_comm['is_surcharge'];
						if($md_is_surcharge)
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}

						}
						else
						{
							$md_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$md_charge_amount = $commision;
							}
							
						}
						$md_remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && $md_is_surcharge && $dist_is_surcharge)
						{
							$md_remaining_charge_amount = $dist_charge_amount - $md_charge_amount;
							if($dist_charge_amount < $md_charge_amount)
							{
								$deduct_charge_amount = $md_charge_amount - $dist_charge_amount;
							}
							$total_distribute_amount+=$md_remaining_charge_amount;
						}
						else
						{
							if($md_charge_amount > $total_downline_com)
							{
								$md_remaining_charge_amount = $md_charge_amount - $total_downline_com;
								$total_distribute_amount+=$md_remaining_charge_amount;
							}
							else
							{
								$md_remaining_charge_amount = $total_downline_com - $md_charge_amount;
								$deduct_charge_amount = $total_downline_com - $md_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - MD Charge Amount - '.$md_charge_amount.' - Credit Amount - '.$md_remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						
						if($md_remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $md_remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $md_is_surcharge,
								'commision_amount' => $md_charge_amount,
								'settlement_amount' => $remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $md_remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,   
				                'wallet_type'         => 1,        
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                 

			                //deduct tds amount

			                if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($md_id);

			                $tds_amount = $md_remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.'  Pay Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
                   			
                   			} 
							
						}
						elseif($md_remaining_charge_amount < 0)
						{
							$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $deduct_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $md_is_surcharge,
								'commision_amount' => $md_charge_amount,
								'settlement_amount' => $deduct_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $deduct_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,   
				                'wallet_type'         => 1,     
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Deduction.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
							
						}
					}
				}

				if($accountData['account_type'] == 2){

					$admin_id = $this->User->get_admin_id($domain_account_id);
					$admin_package_id = $this->User->get_account_package_id($domain_account_id);
					
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin ID - '.$admin_id.']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
					// get master distribution recharge commision
					$get_comm = $this->db->get_where('bbps_commision',array('package_id'=>$admin_package_id,'service_id'=>$service_id))->row_array();
					// save system log
	        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Commision Data - '.json_encode($get_comm).']'.PHP_EOL;
	        		$this->User->generateLog($log_msg);
	        		if($get_comm)
					{
						// debit wallet
	                	$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);
	                	// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Wallet Balance - '.$before_balance.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						$commision = $get_comm['commision'];
						$is_flat = $get_comm['is_flat'];
						$admin_is_surcharge = $get_comm['is_surcharge'];
						if($admin_is_surcharge)
						{
							$admin_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$admin_charge_amount = $commision;
							}

						}
						else
						{
							$admin_charge_amount = round(($commision/100)*$amount,2);
							if($is_flat)
							{
								$admin_charge_amount = $commision;
							}
							
						}


						$remaining_charge_amount = 0;
						$deduct_charge_amount = 0;
						if($is_surcharge && $admin_is_surcharge)
						{
							$remaining_charge_amount = $charge_amount - $admin_charge_amount;
							$remaining_charge_amount = $remaining_charge_amount - $total_distribute_amount;
						}
						else
						{
							if($admin_charge_amount >= $total_distribute_amount)
							{
								$remaining_charge_amount = $admin_charge_amount - $total_distribute_amount;
							}
							else
							{
								$remaining_charge_amount = $total_distribute_amount - $admin_charge_amount;
								$deduct_charge_amount = $total_distribute_amount - $admin_charge_amount;
							}
						}

						// save system log
		        		$log_msg = '['.date('d-m-Y H:i:s').' - Admin Charge Amount - '.$admin_charge_amount.' - Credit Amount - '.$remaining_charge_amount.' - Deduct Amount - '.$deduct_charge_amount.']'.PHP_EOL;
		        		$this->User->generateLog($log_msg);

						if($remaining_charge_amount > 0)
						{
							$after_balance = $before_balance + $remaining_charge_amount;
							$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $admin_id,
								'type' => 'BBPS',
								'record_id' => $recharge_id,
								'commision' => $commision,
								'is_flat' => $is_flat,
								'is_surcharge' => $admin_is_surcharge,
								'commision_amount' => $admin_charge_amount,
								'settlement_amount' => $remaining_charge_amount,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);
							
							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $remaining_charge_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1,   
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			                //deduct tds amount

			                if($accountData['is_tds_amount'] == 1)

			                {

			                
                			$before_balance = $this->User->getMemberWalletBalanceSP($admin_id);

			                $tds_amount = $remaining_charge_amount*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $admin_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Bill #'.$recharge_unique_id.' Pay Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               
			            	}
							
						}
						
					}
				}
			}
			
		}
		
	}

	public function get_admin_dmr_surcharge($amount = 0, $loggedAccountID = 0)
	{
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		// get surcharge
		$getSurcharge = $this->db->get_where('dmr_commision',array('account_id'=>$domain_account_id,'member_id'=>$loggedAccountID,'start_range <='=>$amount,'end_range >='=>$amount))->row_array();
		if($getSurcharge)
		{
			$surcharge = $getSurcharge['surcharge'];
			$is_flat = $getSurcharge['is_flat'];

			$surcarge_amount = round(($surcharge/100)*$amount,2);

			if($is_flat)
			{
				$surcarge_amount = $surcharge;
			}
		}
		return $surcarge_amount;
	}

	public function get_dmr_surcharge($amount = 0, $loggedAccountID = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$member_role_id = $this->User->getMemberRoleID($loggedAccountID);
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		// get surcharge
		$getSurcharge = $this->db->get_where('dmr_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'start_range <='=>$amount,'end_range >='=>$amount))->row_array();
		
		if($getSurcharge)
		{
			$is_flat = $getSurcharge['is_flat'];
			if($member_role_id == 3)
			{
				$surcharge = $getSurcharge['md_charge'];
			}
			elseif($member_role_id == 4)
			{
				$surcharge = $getSurcharge['dt_charge'];
			}
			elseif($member_role_id == 5)
			{
				$surcharge = $getSurcharge['rt_charge'];
			}
			elseif($member_role_id == 6)
			{
				$surcharge = $getSurcharge['api_charge'];
			}
			elseif($member_role_id == 8)
			{
				$surcharge = $getSurcharge['user_charge'];
			}
			

			$surcarge_amount = round(($surcharge/100)*$amount,2);

			if($is_flat)
			{
				$surcarge_amount = $surcharge;
			}
		}
		return $surcarge_amount;
	}

	public function get_xpress_payout_surcharge($amount = 0, $loggedAccountID = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$member_role_id = $this->User->getMemberRoleID($loggedAccountID);
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		// get surcharge
		$getSurcharge = $this->db->get_where('xpress_payout_charge',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'start_range <='=>$amount,'end_range >='=>$amount))->row_array();
		
		if($getSurcharge)
		{
			$is_flat = $getSurcharge['is_flat'];
			if($member_role_id == 3)
			{
				$surcharge = $getSurcharge['md_charge'];
			}
			elseif($member_role_id == 4)
			{
				$surcharge = $getSurcharge['dt_charge'];
			}
			elseif($member_role_id == 5)
			{
				$surcharge = $getSurcharge['rt_charge'];
			}
			elseif($member_role_id == 6)
			{
				$surcharge = $getSurcharge['api_charge'];
			}
			elseif($member_role_id == 8)
			{
				$surcharge = $getSurcharge['user_charge'];
			}
			

			$surcarge_amount = round(($surcharge/100)*$amount,2);

			if($is_flat)
			{
				$surcarge_amount = $surcharge;
			}
		}
		return $surcarge_amount;
	}

	public function get_member_dmt_surcharge($amount = 0, $loggedAccountID = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$member_role_id = $this->User->getMemberRoleID($loggedAccountID);
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		// get surcharge
		$getSurcharge = $this->db->get_where('dmt_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'start_range <='=>$amount,'end_range >='=>$amount))->row_array();
		
		if($getSurcharge)
		{
			$is_flat = $getSurcharge['is_flat'];
			if($member_role_id == 3)
			{
				$surcharge = $getSurcharge['md_charge'];
			}
			elseif($member_role_id == 4)
			{
				$surcharge = $getSurcharge['dt_charge'];
			}
			elseif($member_role_id == 5)
			{
				$surcharge = $getSurcharge['rt_charge'];
			}
			elseif($member_role_id == 6)
			{
				$surcharge = $getSurcharge['api_charge'];
			}
			elseif($member_role_id == 8)
			{
				$surcharge = $getSurcharge['user_charge'];
			}
			

			$surcarge_amount = round(($surcharge/100)*$amount,2);

			if($is_flat)
			{
				$surcarge_amount = $surcharge;
			}
		}
		return $surcarge_amount;
	}

	public function get_member_nsdl_surcharge($domain_account_id = 0, $loggedAccountID = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$surcarge_amount = 0;
		// get surcharge
		$getSurcharge = $this->db->get_where('nsdl_pancard_charge',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id))->row_array();
		
		if($getSurcharge)
		{
			$surcharge = $getSurcharge['surcharge'];
			$surcarge_amount = $surcharge;
		}
		return $surcarge_amount;
	}

	public function get_admin_nsdl_surcharge($domain_account_id = 0, $loggedAccountID = 0)
	{
		$admin_package_id = $this->User->get_account_package_id($domain_account_id);
		$surcarge_amount = 0;
		// get surcharge
		$getSurcharge = $this->db->get_where('nsdl_pancard_charge',array('account_id'=>10000,'package_id'=>$admin_package_id))->row_array();
		
		if($getSurcharge)
		{
			$surcharge = $getSurcharge['surcharge'];
			$surcarge_amount = $surcharge;
		}
		return $surcarge_amount;
	}

	public function get_money_transfer_surcharge($amount = 0, $loggedAccountID = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$member_role_id = $this->User->getMemberRoleID($loggedAccountID);
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		// get surcharge
		$getSurcharge = $this->db->get_where('money_transfer_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'start_range <='=>$amount,'end_range >='=>$amount))->row_array();
		
		if($getSurcharge)
		{
			$is_flat = $getSurcharge['is_flat'];
			if($member_role_id == 3)
			{
				$surcharge = $getSurcharge['md_charge'];
			}
			elseif($member_role_id == 4)
			{
				$surcharge = $getSurcharge['dt_charge'];
			}
			elseif($member_role_id == 5)
			{
				$surcharge = $getSurcharge['rt_charge'];
			}
			elseif($member_role_id == 6)
			{
				$surcharge = $getSurcharge['api_charge'];
			}
			elseif($member_role_id == 8)
			{
				$surcharge = $getSurcharge['user_charge'];
			}
			

			$surcarge_amount = round(($surcharge/100)*$amount,2);

			if($is_flat)
			{
				$surcarge_amount = $surcharge;
			}
		}
		return $surcarge_amount;
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

	public function get_gateway_charge($amount = 0, $loggedAccountID = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$member_role_id = $this->User->getMemberRoleID($loggedAccountID);
		$is_surcharge = 0;
		$domain_account_id = $this->User->get_domain_account();
		$commission_amount = 0;
		// get surcharge
		$getSurcharge = $this->db->get_where('gateway_charge',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'start_range <='=>$amount,'end_range >='=>$amount))->row_array();
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

	public function get_upi_commission($loggedAccountID = 0, $amount = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$member_role_id = $this->User->getMemberRoleID($loggedAccountID);
		$is_surcharge = 0;
		$domain_account_id = $this->User->get_domain_account();
		$commission_amount = 0;
		// get surcharge
		$getSurcharge = $this->db->get_where('upi_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id))->row_array();
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

	public function get_upi_cash_commission($loggedAccountID = 0, $amount = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$member_role_id = $this->User->getMemberRoleID($loggedAccountID);
		$is_surcharge = 0;
		$domain_account_id = $this->User->get_domain_account();
		$commission_amount = 0;
		// get surcharge
		$getSurcharge = $this->db->get_where('upi_cash_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id))->row_array();
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

	public function get_admin_aeps_commission($amount = 0, $loggedAccountID = 0, $com_type = 0)
	{
		$member_package_id = $this->User->get_account_package_id($loggedAccountID);
		$surcarge_amount = 0;
		$is_surcharge = 0;
		$domain_account_id = 10000;
		$commission_amount = 0;
		// get surcharge
		$getSurcharge = $this->db->get_where('aeps_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'start_range <='=>$amount,'end_range >='=>$amount,'com_type'=>$com_type))->row_array();
		
		if($getSurcharge)
		{
			$commission = $getSurcharge['commission'];
			$is_flat = $getSurcharge['is_flat'];
			$is_surcharge = $getSurcharge['is_surcharge'];

			$commission_amount = round(($commission/100)*$amount,2);
			if($is_flat)
			{
				$commission_amount = $commission;
			}
		}
		return array('commission_amount'=>$commission_amount,'is_surcharge'=>$is_surcharge);
	}

	public function get_account_verify_surcharge($loggedAccountID = 0)
	{
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		
		// get surcharge
		$getSurcharge = $this->db->get_where('dmr_account_verify_charge',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id))->row_array();
		
		if($getSurcharge)
		{
			$surcarge_amount = $getSurcharge['surcharge'];
		}
		return $surcarge_amount;
	}

	public function get_admin_account_verify_surcharge($loggedAccountID = 0)
	{
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		$account_package_id = $this->User->get_account_package_id($domain_account_id);
		// get surcharge
		$getSurcharge = $this->db->get_where('dmr_account_verify_charge',array('package_id'=>$account_package_id))->row_array();
		if($getSurcharge)
		{
			$surcarge_amount = $getSurcharge['surcharge'];
		}
		return $surcarge_amount;
	}

	public function get_admin_dmt_commission($amount = 0, $loggedAccountID = 0)
	{
		$member_package_id = $this->User->get_account_package_id($loggedAccountID);
		$surcarge_amount = 0;
		$is_surcharge = 0;
		$domain_account_id = 10000;
		$commission_amount = 0;
		// get surcharge
		$getSurcharge = $this->db->get_where('dmt_commision',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id,'start_range <='=>$amount,'end_range >='=>$amount))->row_array();
		
		if($getSurcharge)
		{
			$commission = $getSurcharge['surcharge'];
			$is_flat = $getSurcharge['is_flat'];
			$commission_amount = round(($commission/100)*$amount,2);
			if($is_flat)
			{
				$commission_amount = $commission;
			}
		}
		return $commission_amount;
	}

	public function distribute_payout_commision($dmt_id, $transaction_id, $amount, $account_id, $surcharge_amount,$user_type = '',$user_code = '', $txnType = '')
	{

		$domain_account_id = $this->User->get_domain_account();
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge - User Role - '.$user_role_id.' transaction_id - '.$transaction_id.' - Txn Amount - '.$amount.' - Charge Amount - '.$surcharge_amount.' - TxnType - '.$txnType.']'.PHP_EOL;
	    $this->User->generateLog($log_msg);
        if($user_role_id == 4)
		{
			$md_id = $this->User->get_master_distributor_id($account_id);
			if($md_id)
			{
				// get dmr surcharge
	            $md_surcharge_amount = $this->User->get_dmr_surcharge($amount,$md_id,$txnType);
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge- MD ID - '.$md_id.' - MD Charge - '.$md_surcharge_amount.' transaction_id - '.$transaction_id.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $surcharge_amount - $md_surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge - MD Commision - '.$commision.' transaction_id - '.$transaction_id.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// credit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAYOUT',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}
		    	elseif($surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge - MD Charge - '.$commision.' transaction_id - '.$transaction_id.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// debit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAYOUT',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}
		    }
            
		}
		elseif($user_role_id == 5)
		{
			$distributor_id = $this->User->get_distributor_id($account_id);

			if($distributor_id)
			{
				// get dmr surcharge
	            $dist_surcharge_amount = $this->User->get_dmr_surcharge($amount,$distributor_id,$txnType);
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge - DT Charge - '.$dist_surcharge_amount.' transaction_id - '.$transaction_id.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($surcharge_amount > $dist_surcharge_amount)
		    	{
		    		$commision = $surcharge_amount - $dist_surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge - DT Commision - '.$commision.' transaction_id - '.$transaction_id.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// credit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'PAYOUT',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}
		    	elseif($surcharge_amount < $dist_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge - DT Charge - '.$commision.' transaction_id - '.$transaction_id.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// debit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'PAYOUT',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}

	            $md_id = $this->User->get_master_distributor_id($distributor_id);

				// get dmr surcharge
	            $md_surcharge_amount = $this->User->get_dmr_surcharge($amount,$md_id,$txnType);
	            
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge - MD Charge - '.$md_surcharge_amount.' transaction_id - '.$transaction_id.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($dist_surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $md_surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge - MD Commision - '.$commision.' transaction_id - '.$transaction_id.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// credit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAYOUT',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}
		    	elseif($dist_surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $dist_surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - AEPS Payout Api - Distribute Commision/Surcharge - MD Charge - '.$commision.' transaction_id - '.$transaction_id.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// debit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAYOUT',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}



		    }
		}
	}

	public function distribute_money_transfer_commision($dmt_id, $transaction_id, $amount, $account_id, $surcharge_amount,$user_type = '',$user_code = '')
	{

		$domain_account_id = $this->User->get_domain_account();
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - User Role - '.$user_role_id.']'.PHP_EOL;
	    $this->User->generateLog($log_msg);
        if($user_role_id == 4)
		{
			$md_id = $this->User->get_master_distributor_id($account_id);
			if($md_id)
			{
				// get dmr surcharge
	            $md_surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$md_id);
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - MD Charge - '.$md_surcharge_amount.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $surcharge_amount - $md_surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// credit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'DMT',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}
		    	elseif($surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// debit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'DMT',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}
		    }
            
		}
		elseif($user_role_id == 5)
		{
			$distributor_id = $this->User->get_distributor_id($account_id);
			if($distributor_id)
			{
				// get dmr surcharge
	            $dist_surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$distributor_id);
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - DT Charge - '.$dist_surcharge_amount.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($surcharge_amount > $dist_surcharge_amount)
		    	{
		    		$commision = $surcharge_amount - $dist_surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - DT Commision - '.$commision.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// credit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'DMT',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}
		    	elseif($surcharge_amount < $dist_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - DT Charge - '.$commision.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// debit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'DMT',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}

	            $md_id = $this->User->get_master_distributor_id($distributor_id);

				// get dmr surcharge
	            $md_surcharge_amount = $this->User->get_money_transfer_surcharge($amount,$md_id);
	            
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - MD Charge - '.$md_surcharge_amount.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($dist_surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $md_surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// credit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'DMT',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}
		    	elseif($dist_surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $dist_surcharge_amount;
		    		// save system log
		    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - DMT API - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
		    		$this->User->generateLog($log_msg);
		    		if($commision)
		    		{
		    			// debit wallet
		            	
						$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'DMT',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'Fund Transfer #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                
		    		}
		    	}
		    }
		}
	}

	public function distribute_aeps_commision($dmt_id, $transaction_id,$account_id,$amount,$com_amount,$is_surcharge,$com_type,$user_type = '',$user_code = '',$domain_account_id = 0,$txnType = 'AEPS')
	{



		if($domain_account_id == 0)
		{
			$domain_account_id = $this->User->get_domain_account();
		}

		$accountData = $this->User->get_account_data($domain_account_id);
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;


		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - User Role - '.$user_role_id.' - Commision Amount - '.$com_amount.' - Is Surcharge - '.$is_surcharge.']'.PHP_EOL;
	    $this->User->generateLog($log_msg);
        if($user_role_id == 4)
		{
			$md_id = $this->User->get_master_distributor_id($account_id);
			if($md_id)
			{
				// get dmr surcharge
	            $commisionData = $this->User->get_aeps_commission($amount,$md_id,$com_type,$domain_account_id);
	            $md_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
	        	$md_is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Amount - '.$md_amount.' - MD Is Surcharge - '.$md_is_surcharge.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($is_surcharge)
		    	{
		    		if($com_amount > $md_amount)
		    		{
		    			$commision = $com_amount - $md_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	
							$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			                //deduct tds amount

			                 

			            	if($accountData['is_tds_amount'] == 1)
                            {

                                
                            $before_balance = $this->User->getMemberWalletBalanceSP($md_id);

                            $tds_amount = $commision*5/100;

                            $after_balance = $before_balance - $tds_amount;

                            $wallet_data = array(
                                 'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Tds Amount Debited.'
                            );

                            $this->db->insert('member_wallet',$wallet_data);

                            

                            //save tds entry 


                            $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                'record_id'            =>$dmt_id,
                                'com_amount'      => $commision,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $md_id,
                                'description'         => 'AEPS Txn  #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

                            }



			    		}
		    		}
		    		elseif($com_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $com_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($com_amount > $md_amount)
		    		{
		    			$commision = $com_amount - $md_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($com_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $com_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                 //deduct tds amount
			             		
			             		if($accountData['is_tds_amount'] == 1)
                            {

                                
                            $before_balance = $this->User->getMemberWalletBalanceSP($md_id);

                            $tds_amount = $commision*5/100;

                            $after_balance = $before_balance - $tds_amount;

                            $wallet_data = array(
                                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Tds Amount Debited.'
                            );

                            $this->db->insert('member_wallet',$wallet_data);

                            
                            //save tds entry 


                            $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                'record_id'            =>$dmt_id,
                                'com_amount'      => $commision,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $md_id,
                                'description'         => 'AEPS Txn  #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

                            }



			               
			    		}
		    		}
		    	}
		    }

		}
		elseif($user_role_id == 5)
		{
			$distributor_id = $this->User->get_distributor_id($account_id);
				
			if($distributor_id)
			{
				// get dmr surcharge
	            $dtCommisionData = $this->User->get_aeps_commission($amount,$distributor_id,$com_type,$domain_account_id);
	            $dt_amount = isset($dtCommisionData['commission_amount']) ? $dtCommisionData['commission_amount'] : 0 ;
	        	$dt_is_surcharge = isset($dtCommisionData['is_surcharge']) ? $dtCommisionData['is_surcharge'] : 0 ;
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - DT Amount - '.$dt_amount.' - DT Is Surcharge - '.$dt_is_surcharge.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($is_surcharge)
		    	{
		    		if($com_amount > $dt_amount)
		    		{
		    			$commision = $com_amount - $dt_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - DT Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                //tds deduction

			                if($accountData['is_tds_amount'] == 1)
                            {

                                
                            $before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);

                            $tds_amount = $commision*5/100;

                            $after_balance = $before_balance - $tds_amount;

                            $wallet_data = array(
                                 'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Tds Amount Debited.'
                            );

                            $this->db->insert('member_wallet',$wallet_data);

                            

                            //save tds entry 


                            $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $distributor_id,  
                                'record_id'            =>$dmt_id,
                                'com_amount'      => $commision,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $distributor_id,
                                'description'         => 'AEPS Txn  #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

                            }

			               
			    		}
		    		}
		    		elseif($com_amount < $dt_amount)
		    		{
		    			$commision = $dt_amount - $com_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - DT Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($com_amount > $dt_amount)
		    		{
		    			$commision = $com_amount - $dt_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - DT Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($com_amount < $dt_amount)
		    		{
		    			$commision = $dt_amount - $com_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - DT Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                if($accountData['is_tds_amount'] == 1)
                            {

                                
                            $before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);

                            $tds_amount = $commision*5/100;

                            $after_balance = $before_balance - $tds_amount;

                            $wallet_data = array(
                                 'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Tds Amount Debited.'
                            );

                            $this->db->insert('member_wallet',$wallet_data);

                            

                            //save tds entry 


                            $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $distributor_id,  
                                'record_id'            =>$dmt_id,
                                'com_amount'      => $commision,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $distributor_id,
                                'description'         => 'AEPS Txn  #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

                            }
			               

			               
			    		}
		    		}
		    	}
		    

	            $md_id = $this->User->get_master_distributor_id($distributor_id);

				// get dmr surcharge
	            $commisionData = $this->User->get_aeps_commission($amount,$md_id,$com_type,$domain_account_id);
	            $md_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
	        	$md_is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Amount - '.$md_amount.' - MD Is Surcharge - '.$md_is_surcharge.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($is_surcharge)
		    	{
		    		if($dt_amount > $md_amount)
		    		{
		    			$commision = $dt_amount - $md_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			                //tds deduction

			                if($accountData['is_tds_amount'] == 1)
                            {

                                
                            $before_balance = $this->User->getMemberWalletBalanceSP($md_id);

                            $tds_amount = $commision*5/100;

                            $after_balance = $before_balance - $tds_amount;

                            $wallet_data = array(
                                 'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Tds Amount Debited.'
                            );

                            $this->db->insert('member_wallet',$wallet_data);

                            
                            //save tds entry 


                            $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                'record_id'            =>$dmt_id,
                                'com_amount'      => $commision,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $md_id,
                                'description'         => 'AEPS Txn  #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

                            }


			                
			    		}
		    		}
		    		elseif($dt_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $dt_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($dt_amount > $md_amount)
		    		{
		    			$commision = $dt_amount - $md_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($dt_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $dt_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - '.$txnType.' - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => $txnType,
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                

			                if($accountData['is_tds_amount'] == 1)
                            {

                                
                            $before_balance = $this->User->getMemberWalletBalanceSP($md_id);

                            $tds_amount = $commision*5/100;

                            $after_balance = $before_balance - $tds_amount;

                            $wallet_data = array(
                                 'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => $txnType.' Txn #'.$transaction_id.' Commision Tds Amount Debited.'
                            );

                            $this->db->insert('member_wallet',$wallet_data);

                            

                            //save tds entry 


                            $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                'record_id'            =>$dmt_id,
                                'com_amount'      => $commision,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $md_id,
                                'description'         => 'AEPS Txn  #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

                            }
                            
			    		}
		    		}
		    	}
		    }
		}
	}


	public function distribute_cash_deposite_commision($dmt_id, $transaction_id, $amount, $account_id, $com_amount,$is_surcharge,$com_type,$user_type = '',$user_code = '')
	{	



		$domain_account_id = $this->User->get_domain_account();
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		// save system log
	    $log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - User Role - '.$user_role_id.']'.PHP_EOL;
	    $this->User->generateLog($log_msg);
        if($user_role_id == 4)
		{
			$md_id = $this->User->get_master_distributor_id($account_id);
			if($md_id)
			{
				// get dmr surcharge
	            $commisionData = $this->User->get_aeps_commission($amount,$md_id,$com_type);
	            $md_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
	        	$md_is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Amount - '.$md_amount.' - MD Is Surcharge - '.$md_is_surcharge.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($is_surcharge)
		    	{
		    		if($com_amount > $md_amount)
		    		{
		    			$commision = $com_amount - $md_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    		elseif($com_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $com_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($com_amount > $md_amount)
		    		{
		    			$commision = $com_amount - $md_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    		elseif($com_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $com_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    	}
		    }

		}
		elseif($user_role_id == 5)
		{
			$distributor_id = $this->User->get_distributor_id($account_id);
			if($distributor_id)
			{
				// get dmr surcharge
	            $dtCommisionData = $this->User->get_aeps_commission($amount,$distributor_id,$com_type);
	            $dt_amount = isset($dtCommisionData['commission_amount']) ? $dtCommisionData['commission_amount'] : 0 ;
	        	$dt_is_surcharge = isset($dtCommisionData['is_surcharge']) ? $dtCommisionData['is_surcharge'] : 0 ;
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - DT Amount - '.$md_amount.' - DT Is Surcharge - '.$md_is_surcharge.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($is_surcharge)
		    	{
		    		if($com_amount > $dt_amount)
		    		{
		    			$commision = $com_amount - $dt_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - DT Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$distributor_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    		elseif($com_amount < $dt_amount)
		    		{
		    			$commision = $dt_amount - $com_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - DT Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$distributor_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($com_amount > $dt_amount)
		    		{
		    			$commision = $com_amount - $dt_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - DT Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$distributor_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    		elseif($com_amount < $dt_amount)
		    		{
		    			$commision = $dt_amount - $com_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - DT Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$distributor_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    	}

	            $md_id = $this->User->get_master_distributor_id($distributor_id);

				// get dmr surcharge
	            $commisionData = $this->User->get_aeps_commission($amount,$md_id,$com_type);
	            $md_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
	        	$md_is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;
	            // save system log
		    	$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Amount - '.$md_amount.' - MD Is Surcharge - '.$md_is_surcharge.']'.PHP_EOL;
		    	$this->User->generateLog($log_msg);

		    	if($is_surcharge)
		    	{
		    		if($dt_amount > $md_amount)
		    		{
		    			$commision = $dt_amount - $md_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    		elseif($dt_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $dt_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($dt_amount > $md_amount)
		    		{
		    			$commision = $dt_amount - $md_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Charge - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    		elseif($dt_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $dt_amount;
			    		// save system log
			    		$log_msg = '['.date('d-m-Y H:i:s').' - '.$user_type.'('.$user_code.') - Cash Deposite - Distribute Commision/Surcharge - MD Commision - '.$commision.']'.PHP_EOL;
			    		$this->User->generateLog($log_msg);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
							$before_balance = $accountBalanceData['wallet_balance'];
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'CD',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'Cash Deposite Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet); 
			    		}
		    		}
		    	}
		    }
		}
	}

	public function distribute_upi_commision($dmt_id, $transaction_id, $amount, $account_id, $com_amount,$is_surcharge)
	{

		$domain_account_id = $this->User->get_domain_account();
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - User Role - '.$user_role_id.'.');
        if($user_role_id == 4)
		{
			$md_id = $this->User->get_master_distributor_id($account_id);
			if($md_id)
			{
				// get dmr surcharge
	            $commisionData = $this->User->get_upi_commission($md_id,$amount);
	            $md_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
	        	$md_is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;
	        	log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Commision Data - '.json_encode($commisionData));  
	            
		    	if($is_surcharge)
		    	{
		    		if($com_amount > $md_amount)
		    		{
		    			$commision = $com_amount - $md_amount;
			    		// save system log
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Commision - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($com_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $com_amount;
			    		// save system log
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Charge - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($com_amount > $md_amount)
		    		{
		    			$commision = $com_amount - $md_amount;
		    			log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Charge - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                 
			    		}
		    		}
		    		elseif($com_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $com_amount;
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Commision - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    }

		}
		elseif($user_role_id == 5)
		{
			$distributor_id = $this->User->get_distributor_id($account_id);
			if($distributor_id)
			{
				// get dmr surcharge
	            $dtCommisionData = $this->User->get_upi_commission($distributor_id,$amount);
	            $dt_amount = isset($dtCommisionData['commission_amount']) ? $dtCommisionData['commission_amount'] : 0 ;
	        	$dt_is_surcharge = isset($dtCommisionData['is_surcharge']) ? $dtCommisionData['is_surcharge'] : 0 ;
	            // save system log
		    	log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - DT Commision Data - '.json_encode($dtCommisionData));  

		    	if($is_surcharge)
		    	{
		    		if($com_amount > $dt_amount)
		    		{
		    			$commision = $com_amount - $dt_amount;
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - DT Commision - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
							$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($com_amount < $dt_amount)
		    		{
		    			$commision = $dt_amount - $com_amount;
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - DT Charge - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($com_amount > $dt_amount)
		    		{
		    			$commision = $com_amount - $dt_amount;
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - DT Charge - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($com_amount < $dt_amount)
		    		{
		    			$commision = $dt_amount - $com_amount;
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - DT Commision - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}

	            $md_id = $this->User->get_master_distributor_id($distributor_id);

				// get dmr surcharge
	            $commisionData = $this->User->get_upi_commission($md_id,$amount);
	            $md_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
	        	$md_is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;
	        	log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Commision Data - '.json_encode($commisionData));

		    	if($is_surcharge)
		    	{
		    		if($dt_amount > $md_amount)
		    		{
		    			$commision = $dt_amount - $md_amount;
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Commision - '.$commision);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($dt_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $dt_amount;
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Charge - '.$commision);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($dt_amount > $md_amount)
		    		{
		    			$commision = $dt_amount - $md_amount;
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Charge - '.$commision);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			               
			    		}
		    		}
		    		elseif($dt_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $dt_amount;
			    		log_message('debug', 'UPI Callback - Distribute Commision/Surcharge - MD Commision - '.$commision);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPI',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    }
		}
	}


	public function distribute_upi_cash_commision($dmt_id, $transaction_id, $amount, $account_id, $com_amount,$is_surcharge)
	{

		$domain_account_id = $this->User->get_domain_account();
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - User Role - '.$user_role_id.'.');
        if($user_role_id == 4)
		{
			$md_id = $this->User->get_master_distributor_id($account_id);
			if($md_id)
			{
				// get dmr surcharge
	            $commisionData = $this->User->get_upi_cash_commission($md_id,$amount);
	            $md_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
	        	$md_is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;
	        	log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Commision Data - '.json_encode($commisionData));  
	            
		    	if($is_surcharge)
		    	{
		    		if($com_amount > $md_amount)
		    		{
		    			$commision = $com_amount - $md_amount;
			    		// save system log
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Commision - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($com_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $com_amount;
			    		// save system log
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Charge - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($com_amount > $md_amount)
		    		{
		    			$commision = $com_amount - $md_amount;
		    			log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Charge - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                 
			    		}
		    		}
		    		elseif($com_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $com_amount;
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Commision - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    }

		}
		elseif($user_role_id == 5)
		{
			$distributor_id = $this->User->get_distributor_id($account_id);
			if($distributor_id)
			{
				// get dmr surcharge
	            $dtCommisionData = $this->User->get_upi_cash_commission($distributor_id,$amount);
	            $dt_amount = isset($dtCommisionData['commission_amount']) ? $dtCommisionData['commission_amount'] : 0 ;
	        	$dt_is_surcharge = isset($dtCommisionData['is_surcharge']) ? $dtCommisionData['is_surcharge'] : 0 ;
	            // save system log
		    	log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - DT Commision Data - '.json_encode($dtCommisionData));  

		    	if($is_surcharge)
		    	{
		    		if($com_amount > $dt_amount)
		    		{
		    			$commision = $com_amount - $dt_amount;
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - DT Commision - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($com_amount < $dt_amount)
		    		{
		    			$commision = $dt_amount - $com_amount;
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - DT Charge - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($com_amount > $dt_amount)
		    		{
		    			$commision = $com_amount - $dt_amount;
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - DT Charge - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($com_amount < $dt_amount)
		    		{
		    			$commision = $dt_amount - $com_amount;
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - DT Commision - '.$commision);  
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($distributor_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $distributor_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}

	            $md_id = $this->User->get_master_distributor_id($distributor_id);

				// get dmr surcharge
	            $commisionData = $this->User->get_upi_cash_commission($md_id,$amount);
	            $md_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
	        	$md_is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;
	        	log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Commision Data - '.json_encode($commisionData));

		    	if($is_surcharge)
		    	{
		    		if($dt_amount > $md_amount)
		    		{
		    			$commision = $dt_amount - $md_amount;
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Commision - '.$commision);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($dt_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $dt_amount;
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Charge - '.$commision);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    	else
		    	{
		    		if($dt_amount > $md_amount)
		    		{
		    			$commision = $dt_amount - $md_amount;
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Charge - '.$commision);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance - $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_surcharge' => 1,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Charge Debited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    		elseif($dt_amount < $md_amount)
		    		{
		    			$commision = $md_amount - $dt_amount;
			    		log_message('debug', 'UPI Cash Callback - Distribute Commision/Surcharge - MD Commision - '.$commision);
			    		if($commision)
			    		{
			    			// credit wallet
			            	$before_balance = $this->User->getMemberWalletBalanceSP($md_id);
							$after_balance = $before_balance + $commision;

			            	$commisionData = array(
								'account_id' => $domain_account_id,
								'member_id' => $md_id,
								'type' => 'UPICASH',
								'record_id' => $dmt_id,
								'commision_amount' => $commision,
								'is_downline' => 1,
								'downline_id' => $account_id,
								'before_balance' => $before_balance,
								'after_balance' => $after_balance,
								'status' => 1,
								'created' => date('Y-m-d H:i:s')
							);
							$this->db->insert('user_commision',$commisionData);

							$wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $commision,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 1, 
				                'wallet_type'		  => 1,       
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'UPI Txn #'.$transaction_id.' Commision Credited.'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                
			    		}
		    		}
		    	}
		    }
		}
	}


	public function generate_api_url($member_id = 0,$opt_id = 0,$amount = 0,$loggedAccountID = 0)
	{
		$account_id = $this->User->get_domain_account();
		$account_package_id = $this->User->get_account_package_id($account_id);	
		$accountData = $this->User->get_account_data($account_id);
		$upper_package_id = $this->User->getMemberPackageID($member_id);
		$member_package_id = $this->User->getMemberPackageID($loggedAccountID);
		
		$api_id = 0;
		if($accountData['account_type'] == 2)
		{
			$account_id = SUPERADMIN_ACCOUNT_ID;
			// get active api id
			$get_api_id = $this->db->get_where('member_active_api',array('account_id'=>$account_id,'package_id'=>$account_package_id,'op_id'=>$opt_id))->row_array();
			$api_id = isset($get_api_id['api_id']) ? $get_api_id['api_id'] : 0;
		}
		else
		{
			if($amount == 0)
			{
				// get active api id
				$get_api_id = $this->db->get_where('member_active_api',array('account_id'=>$account_id,'package_id'=>$upper_package_id,'op_id'=>$opt_id))->row_array();
				$api_id = isset($get_api_id['api_id']) ? $get_api_id['api_id'] : 0;
				if(!$api_id)
				{
					// get active api id
					$get_api_id = $this->db->get_where('member_active_api',array('account_id'=>$account_id,'member_id'=>$member_id,'op_id'=>$opt_id))->row_array();
					$api_id = isset($get_api_id['api_id']) ? $get_api_id['api_id'] : 0;
				}
			}
			else
			{
				// get active api id
				$get_amount_api_id = $this->db->get_where('amount_active_api',array('account_id'=>$account_id,'start_range <='=>$amount,'end_range >='=>$amount,'op_id'=>$opt_id))->row_array();
				$api_id = isset($get_amount_api_id['api_id']) ? $get_amount_api_id['api_id'] : 0;
				if(!$api_id)
				{
					// get active api id
					$get_api_id = $this->db->get_where('member_active_api',array('account_id'=>$account_id,'package_id'=>$member_package_id,'op_id'=>$opt_id))->row_array();
					$api_id = isset($get_api_id['api_id']) ? $get_api_id['api_id'] : 0;
					if(!$api_id)
					{
						// get active api id
						$get_api_id = $this->db->get_where('member_active_api',array('account_id'=>$account_id,'member_id'=>$loggedAccountID,'op_id'=>$opt_id))->row_array();
						$api_id = isset($get_api_id['api_id']) ? $get_api_id['api_id'] : 0;
						if(!$api_id)
						{
							// get active api id
							$get_api_id = $this->db->get_where('member_active_api',array('account_id'=>$account_id,'package_id'=>$upper_package_id,'op_id'=>$opt_id))->row_array();
							$api_id = isset($get_api_id['api_id']) ? $get_api_id['api_id'] : 0;
							if(!$api_id)
							{
								// get active api id
								$get_api_id = $this->db->get_where('member_active_api',array('account_id'=>$account_id,'member_id'=>$member_id,'op_id'=>$opt_id))->row_array();
								$api_id = isset($get_api_id['api_id']) ? $get_api_id['api_id'] : 0;
							}
						}
					}
				}
			}
		}
		if($api_id)
		{
			// get api data
		 	$apiData = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->row_array();

		 	// get method data
		 	$getMethodData = $this->db->get_where('api_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>1))->row_array();

		 	// get header data
		 	$getHeaderData = $this->db->get_where('api_header_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>1))->row_array();

		 	// get method all parameters
		 	$getParaList = $this->db->get_where('api_get_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

		 	// post method data
		 	$postMethodData = $this->db->get_where('api_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>2))->row_array();

		 	// get method all parameters
		 	$postParaList = $this->db->get_where('api_post_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

		 	$responseParaList = array();
		 	if($apiData['response_type'] == 1)
		 	{
		 		$responseParaList = $this->db->get_where('api_str_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
		 	}
		 	elseif($apiData['response_type'] == 2)
		 	{
		 		$responseParaList = $this->db->get_where('api_xml_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
		 	}
		 	elseif($apiData['response_type'] == 3)
		 	{
		 		$responseParaList = $this->db->get_where('api_json_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
		 	}

		 	$api_url = $apiData['request_base_url'];
		 	$post_data = array();
		 	$header_data = array();
		 	if(isset($getHeaderData['is_access_key']) && $getHeaderData['is_access_key'])
	 		{
	 			$header_data[$getHeaderData['access_key']] = $apiData['access_key'];
	 		}
	 		if(isset($getHeaderData['is_username']) && $getHeaderData['is_username'])
	 		{
	 			$header_data[$getHeaderData['username_key']] = $apiData['username'];
	 		}
	 		if(isset($getHeaderData['is_password']) && $getHeaderData['is_password'])
	 		{
	 			$header_data[$getHeaderData['password_key']] = $apiData['password'];
	 		}
		 	if($apiData['request_type'] == 1)
		 	{
		 		if(isset($getMethodData['is_access_key']) && $getMethodData['is_access_key'])
		 		{
		 			$api_url.='&'.$getMethodData['access_key'].'='.$apiData['access_key'];
		 		}
		 		if(isset($getMethodData['is_username']) && $getMethodData['is_username'])
		 		{
		 			$api_url.='&'.$getMethodData['username_key'].'='.$apiData['username'];
		 		}
		 		if(isset($getMethodData['is_password']) && $getMethodData['is_password'])
		 		{
		 			$api_url.='&'.$getMethodData['password_key'].'='.$apiData['password'];
		 		}

		 		if($getParaList)
		 		{
		 			foreach($getParaList as $getList)
		 			{
		 				if($getList['value'] == '#')
		 				{
		 					$value_code = '';
		 					if($getList['value_id'] == 1)
		 						$value_code = '{AMOUNT}';
		 					elseif($getList['value_id'] == 2)
		 						$value_code = '{OPERATOR}';
		 					elseif($getList['value_id'] == 3)
		 						$value_code = '{CIRCLE}';
		 					elseif($getList['value_id'] == 4)
		 						$value_code = '{TXNID}';
		 					elseif($getList['value_id'] == 5)
		 						$value_code = '{MOBILE}';
		 					elseif($getList['value_id'] == 6)
		 						$value_code = '{MEMBERID}';

		 					$api_url.='&'.$getList['para_key'].'='.$value_code;
		 				}
		 				elseif($getList['value_id'] == 7)
		 				{
		 					$explode_str = explode('|', $getList['value']);
		 					// get operator type
		 					$get_opt_type = $this->db->select('type,operator_code')->get_where('operator',array('id'=>$opt_id))->row_array();
		 					$opt_type = isset($get_opt_type['type']) ? $get_opt_type['type'] : '';
		 					$opt_code = isset($get_opt_type['operator_code']) ? $get_opt_type['operator_code'] : '';
		 					if($opt_type == 'Prepaid' && $opt_code != 'BV')
		 					{
		 						$api_url.='&'.$getList['para_key'].'='.$explode_str[0];
		 					}
		 					elseif($opt_type == 'Prepaid' && $opt_code == 'BV')
		 					{
		 						$api_url.='&'.$getList['para_key'].'='.$explode_str[1];
		 					}
		 					elseif($opt_type == 'Postpaid')
		 					{
		 						$api_url.='&'.$getList['para_key'].'='.$explode_str[3];
		 					}
		 					elseif($opt_type == 'DTH')
		 					{
		 						$api_url.='&'.$getList['para_key'].'='.$explode_str[2];
		 					}
		 				}
		 				else
		 				{
		 					$api_url.='&'.$getList['para_key'].'='.$getList['value'];
		 				}
		 			}
		 		}
		 	}
		 	elseif($apiData['request_type'] == 2)
		 	{
		 		if(isset($postMethodData['is_access_key']) && $postMethodData['is_access_key'])
		 		{
		 			$post_data[$postMethodData['access_key']] = $apiData['access_key'];
		 		}
		 		if(isset($postMethodData['is_username']) && $postMethodData['is_username'])
		 		{
		 			$post_data[$postMethodData['username_key']] = $apiData['username'];
		 		}
		 		if(isset($postMethodData['is_password']) && $postMethodData['is_password'])
		 		{
		 			$post_data[$postMethodData['password_key']] = $apiData['password'];
		 		}

		 		if($postParaList)
		 		{
		 			foreach($postParaList as $getList)
		 			{
		 				if($getList['value'] == '#')
		 				{
		 					$value_code = '';
		 					if($getList['value_id'] == 1)
		 						$value_code = '{AMOUNT}';
		 					elseif($getList['value_id'] == 2)
		 						$value_code = '{OPERATOR}';
		 					elseif($getList['value_id'] == 3)
		 						$value_code = '{CIRCLE}';
		 					elseif($getList['value_id'] == 4)
		 						$value_code = '{TXNID}';
		 					elseif($getList['value_id'] == 5)
		 						$value_code = '{MOBILE}';
		 					elseif($getList['value_id'] == 6)
		 						$value_code = '{MEMBERID}';

		 					$post_data[$getList['para_key']] = $value_code;
		 				}
		 				elseif($getList['value_id'] == 7)
		 				{
		 					$explode_str = explode('|', $getList['value']);
		 					// get operator type
		 					$get_opt_type = $this->db->select('type,operator_code')->get_where('operator',array('id'=>$opt_id))->row_array();
		 					$opt_type = isset($get_opt_type['type']) ? $get_opt_type['type'] : '';
		 					$opt_code = isset($get_opt_type['operator_code']) ? $get_opt_type['operator_code'] : '';
		 					if($opt_type == 'Prepaid' && $opt_code != 'BV')
		 					{
		 						$post_data[$getList['para_key']] = $explode_str[0];
		 					}
		 					elseif($opt_type == 'Prepaid' && $opt_code == 'BV')
		 					{
		 						$post_data[$getList['para_key']] = $explode_str[1];
		 					}
		 					elseif($opt_type == 'Postpaid')
		 					{
		 						$post_data[$getList['para_key']] = $explode_str[3];
		 					}
		 					elseif($opt_type == 'DTH')
		 					{
		 						$post_data[$getList['para_key']] = $explode_str[2];
		 					}
		 				}
		 				else
		 				{
		 					if($getList['para_key'] == 'is_stv' && $opt_id == 4)
		 					{
		 						$post_data[$getList['para_key']] = 'true';
		 					}
		 					else
		 					{
		 						$post_data[$getList['para_key']] = $getList['value'];
		 					}
		 				}
		 			}
		 		}
		 	}
		 	elseif($apiData['request_type'] == 3)
		 	{
		 		if(isset($getMethodData['is_access_key']) && $getMethodData['is_access_key'])
		 		{
		 			$api_url.='&'.$getMethodData['access_key'].'='.$apiData['access_key'];
		 		}
		 		if(isset($getMethodData['is_username']) && $getMethodData['is_username'])
		 		{
		 			$api_url.='&'.$getMethodData['username_key'].'='.$apiData['username'];
		 		}
		 		if(isset($getMethodData['is_password']) && $getMethodData['is_password'])
		 		{
		 			$api_url.='&'.$getMethodData['password_key'].'='.$apiData['password'];
		 		}

		 		if($getParaList)
		 		{
		 			foreach($getParaList as $getList)
		 			{
		 				if($getList['value'] == '#')
		 				{
		 					$value_code = '';
		 					if($getList['value_id'] == 1)
		 						$value_code = '{AMOUNT}';
		 					elseif($getList['value_id'] == 2)
		 						$value_code = '{OPERATOR}';
		 					elseif($getList['value_id'] == 3)
		 						$value_code = '{CIRCLE}';
		 					elseif($getList['value_id'] == 4)
		 						$value_code = '{TXNID}';
		 					elseif($getList['value_id'] == 5)
		 						$value_code = '{MOBILE}';
		 					elseif($getList['value_id'] == 6)
		 						$value_code = '{MEMBERID}';

		 					$api_url.='&'.$getList['para_key'].'='.$value_code;
		 				}
		 				elseif($getList['value_id'] == 7)
		 				{
		 					$explode_str = explode('|', $getList['value']);
		 					// get operator type
		 					$get_opt_type = $this->db->select('type,operator_code')->get_where('operator',array('id'=>$opt_id))->row_array();
		 					$opt_type = isset($get_opt_type['type']) ? $get_opt_type['type'] : '';
		 					$opt_code = isset($get_opt_type['operator_code']) ? $get_opt_type['operator_code'] : '';
		 					if($opt_type == 'Prepaid' && $opt_code != 'BV')
		 					{
		 						$api_url.='&'.$getList['para_key'].'='.$explode_str[0];
		 					}
		 					elseif($opt_type == 'Prepaid' && $opt_code == 'BV')
		 					{
		 						$api_url.='&'.$getList['para_key'].'='.$explode_str[1];
		 					}
		 					elseif($opt_type == 'Postpaid')
		 					{
		 						$api_url.='&'.$getList['para_key'].'='.$explode_str[3];
		 					}
		 					elseif($opt_type == 'DTH')
		 					{
		 						$api_url.='&'.$getList['para_key'].'='.$explode_str[2];
		 					}
		 				}
		 				else
		 				{
		 					$api_url.='&'.$getList['para_key'].'='.$getList['value'];
		 				}
		 			}
		 		}

		 		if(isset($postMethodData['is_access_key']) && $postMethodData['is_access_key'])
		 		{
		 			$post_data[$postMethodData['access_key']] = $apiData['access_key'];
		 		}
		 		if(isset($postMethodData['is_username']) && $postMethodData['is_username'])
		 		{
		 			$post_data[$postMethodData['username_key']] = $apiData['username'];
		 		}
		 		if(isset($postMethodData['is_password']) && $postMethodData['is_password'])
		 		{
		 			$post_data[$postMethodData['password_key']] = $apiData['password'];
		 		}

		 		if($postParaList)
		 		{
		 			foreach($postParaList as $getList)
		 			{
		 				if($getList['value'] == '#')
		 				{
		 					$value_code = '';
		 					if($getList['value_id'] == 1)
		 						$value_code = '{AMOUNT}';
		 					elseif($getList['value_id'] == 2)
		 						$value_code = '{OPERATOR}';
		 					elseif($getList['value_id'] == 3)
		 						$value_code = '{CIRCLE}';
		 					elseif($getList['value_id'] == 4)
		 						$value_code = '{TXNID}';
		 					elseif($getList['value_id'] == 5)
		 						$value_code = '{MOBILE}';
		 					elseif($getList['value_id'] == 6)
		 						$value_code = '{MEMBERID}';

		 					$post_data[$getList['para_key']] = $value_code;
		 				}
		 				elseif($getList['value_id'] == 7)
		 				{
		 					$explode_str = explode('|', $getList['value']);
		 					// get operator type
		 					$get_opt_type = $this->db->select('type,operator_code')->get_where('operator',array('id'=>$opt_id))->row_array();
		 					$opt_type = isset($get_opt_type['type']) ? $get_opt_type['type'] : '';
		 					$opt_code = isset($get_opt_type['operator_code']) ? $get_opt_type['operator_code'] : '';
		 					if($opt_type == 'Prepaid' && $opt_code != 'BV')
		 					{
		 						$post_data[$getList['para_key']] = $explode_str[0];
		 					}
		 					elseif($opt_type == 'Prepaid' && $opt_code == 'BV')
		 					{
		 						$post_data[$getList['para_key']] = $explode_str[1];
		 					}
		 					elseif($opt_type == 'Postpaid')
		 					{
		 						$post_data[$getList['para_key']] = $explode_str[3];
		 					}
		 					elseif($opt_type == 'DTH')
		 					{
		 						$post_data[$getList['para_key']] = $explode_str[2];
		 					}
		 				}
		 				else
		 				{
		 					if($getList['para_key'] == 'is_stv' && $opt_id == 4)
		 					{
		 						$post_data[$getList['para_key']] = 'true';
		 					}
		 					else
		 					{
		 						$post_data[$getList['para_key']] = $getList['value'];
		 					}
		 				}
		 			}
		 		}
		 	}

		 	$responsePara = array();
		 	if($responseParaList)
		 	{
		 		foreach($responseParaList as $rkey=>$rlist)
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

		 	return array('status'=>1,'msg'=>'Success','api_url'=>$api_url,'api_id'=>$api_id,'post_data'=>$post_data,'header_data'=>$header_data,'response_type'=>$apiData['response_type'],'responsePara'=>$responsePara,'seperator'=>$apiData['response_seperator'],'is_instantpay_api'=>$apiData['is_instantpay_api']);

		}
		else
		{
			return array('status'=>0,'msg'=>'API Not Found.');
		}
	}


	public function generate_get_balance_api_url($api_id = 0, $account_id = 0)
	{
		if($account_id == 0)
		{
			$account_id = $this->User->get_domain_account();
		}
		
		// get api data
	 	$apiData = $this->db->get_where('api',array('id'=>$api_id,'account_id'=>$account_id))->row_array();

	 	$get_balance_base_url = $apiData['get_balance_base_url'];
	 	if($get_balance_base_url)
	 	{

		 	// get method data
		 	$getMethodData = $this->db->get_where('api_get_balance_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>1))->row_array();

		 	// get header data
		 	$getHeaderData = $this->db->get_where('api_get_balance_header_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>1))->row_array();

		 	// get method all parameters
		 	$getParaList = $this->db->get_where('api_get_balance_get_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

		 	// post method data
		 	$postMethodData = $this->db->get_where('api_get_balance_parameter',array('account_id'=>$account_id,'api_id'=>$api_id,'type'=>2))->row_array();

		 	// get method all parameters
		 	$postParaList = $this->db->get_where('api_get_balance_post_parameter',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();

		 	$responseParaList = array();
		 	if($apiData['get_balance_response_type'] == 1)
		 	{
		 		$responseParaList = $this->db->get_where('api_get_balance_str_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
		 	}
		 	elseif($apiData['get_balance_response_type'] == 2)
		 	{
		 		$responseParaList = $this->db->get_where('api_get_balance_xml_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
		 	}
		 	elseif($apiData['get_balance_response_type'] == 3)
		 	{
		 		$responseParaList = $this->db->get_where('api_get_balance_json_response',array('account_id'=>$account_id,'api_id'=>$api_id))->result_array();
		 	}

		 	$api_url = $apiData['get_balance_base_url'];
		 	$post_data = array();
		 	$header_data = array();

		 	if(isset($getHeaderData['is_access_key']) && $getHeaderData['is_access_key'])
	 		{
	 			$header_data[$getHeaderData['access_key']] = $apiData['access_key'];
	 		}
	 		if(isset($getHeaderData['is_username']) && $getHeaderData['is_username'])
	 		{
	 			$header_data[$getHeaderData['username_key']] = $apiData['username'];
	 		}
	 		if(isset($getHeaderData['is_password']) && $getHeaderData['is_password'])
	 		{
	 			$header_data[$getHeaderData['password_key']] = $apiData['password'];
	 		}

		 	if($apiData['get_balance_request_type'] == 1)
		 	{
		 		if(isset($getMethodData['is_access_key']) && $getMethodData['is_access_key'])
		 		{
		 			$api_url.='&'.$getMethodData['access_key'].'='.$apiData['access_key'];
		 		}
		 		if(isset($getMethodData['is_username']) && $getMethodData['is_username'])
		 		{
		 			$api_url.='&'.$getMethodData['username_key'].'='.$apiData['username'];
		 		}
		 		if(isset($getMethodData['is_password']) && $getMethodData['is_password'])
		 		{
		 			$api_url.='&'.$getMethodData['password_key'].'='.$apiData['password'];
		 		}

		 		if($getParaList)
		 		{
		 			foreach($getParaList as $getList)
		 			{
		 				if($getList['value'] == '#')
		 				{
		 					$value_code = '';
		 					if($getList['value_id'] == 1)
		 						$value_code = '{AMOUNT}';
		 					elseif($getList['value_id'] == 2)
		 						$value_code = '{OPERATOR}';
		 					elseif($getList['value_id'] == 3)
		 						$value_code = '{CIRCLE}';
		 					elseif($getList['value_id'] == 4)
		 						$value_code = '{TXNID}';
		 					elseif($getList['value_id'] == 5)
		 						$value_code = '{MOBILE}';
		 					elseif($getList['value_id'] == 6)
		 						$value_code = '{MEMBERID}';

		 					$api_url.='&'.$getList['para_key'].'='.$value_code;
		 				}
		 				else
		 				{
		 					$api_url.='&'.$getList['para_key'].'='.$getList['value'];
		 				}
		 			}
		 		}
		 	}
		 	elseif($apiData['get_balance_request_type'] == 2)
		 	{
		 		if(isset($postMethodData['is_access_key']) && $postMethodData['is_access_key'])
		 		{
		 			$post_data[$postMethodData['access_key']] = $apiData['access_key'];
		 		}
		 		if(isset($postMethodData['is_username']) && $postMethodData['is_username'])
		 		{
		 			$post_data[$postMethodData['username_key']] = $apiData['username'];
		 		}
		 		if(isset($postMethodData['is_password']) && $postMethodData['is_password'])
		 		{
		 			$post_data[$postMethodData['password_key']] = $apiData['password'];
		 		}

		 		if($postParaList)
		 		{
		 			foreach($postParaList as $getList)
		 			{
		 				if($getList['value'] == '#')
		 				{
		 					$value_code = '';
		 					if($getList['value_id'] == 1)
		 						$value_code = '{AMOUNT}';
		 					elseif($getList['value_id'] == 2)
		 						$value_code = '{OPERATOR}';
		 					elseif($getList['value_id'] == 3)
		 						$value_code = '{CIRCLE}';
		 					elseif($getList['value_id'] == 4)
		 						$value_code = '{TXNID}';
		 					elseif($getList['value_id'] == 5)
		 						$value_code = '{MOBILE}';
		 					elseif($getList['value_id'] == 6)
		 						$value_code = '{MEMBERID}';

		 					$post_data[$getList['para_key']] = $value_code;
		 				}
		 				else
		 				{
		 					$post_data[$getList['para_key']] = $getList['value'];
		 				}
		 			}
		 		}
		 	}
		 	elseif($apiData['get_balance_request_type'] == 3)
		 	{
		 		if(isset($getMethodData['is_access_key']) && $getMethodData['is_access_key'])
		 		{
		 			$api_url.='&'.$getMethodData['access_key'].'='.$apiData['access_key'];
		 		}
		 		if(isset($getMethodData['is_username']) && $getMethodData['is_username'])
		 		{
		 			$api_url.='&'.$getMethodData['username_key'].'='.$apiData['username'];
		 		}
		 		if(isset($getMethodData['is_password']) && $getMethodData['is_password'])
		 		{
		 			$api_url.='&'.$getMethodData['password_key'].'='.$apiData['password'];
		 		}

		 		if($getParaList)
		 		{
		 			foreach($getParaList as $getList)
		 			{
		 				if($getList['value'] == '#')
		 				{
		 					$value_code = '';
		 					if($getList['value_id'] == 1)
		 						$value_code = '{AMOUNT}';
		 					elseif($getList['value_id'] == 2)
		 						$value_code = '{OPERATOR}';
		 					elseif($getList['value_id'] == 3)
		 						$value_code = '{CIRCLE}';
		 					elseif($getList['value_id'] == 4)
		 						$value_code = '{TXNID}';
		 					elseif($getList['value_id'] == 5)
		 						$value_code = '{MOBILE}';
		 					elseif($getList['value_id'] == 6)
		 						$value_code = '{MEMBERID}';

		 					$api_url.='&'.$getList['para_key'].'='.$value_code;
		 				}
		 				else
		 				{
		 					$api_url.='&'.$getList['para_key'].'='.$getList['value'];
		 				}
		 			}
		 		}

		 		if(isset($postMethodData['is_access_key']) && $postMethodData['is_access_key'])
		 		{
		 			$post_data[$postMethodData['access_key']] = $apiData['access_key'];
		 		}
		 		if(isset($postMethodData['is_username']) && $postMethodData['is_username'])
		 		{
		 			$post_data[$postMethodData['username_key']] = $apiData['username'];
		 		}
		 		if(isset($postMethodData['is_password']) && $postMethodData['is_password'])
		 		{
		 			$post_data[$postMethodData['password_key']] = $apiData['password'];
		 		}

		 		if($postParaList)
		 		{
		 			foreach($postParaList as $getList)
		 			{
		 				if($getList['value'] == '#')
		 				{
		 					$value_code = '';
		 					if($getList['value_id'] == 1)
		 						$value_code = '{AMOUNT}';
		 					elseif($getList['value_id'] == 2)
		 						$value_code = '{OPERATOR}';
		 					elseif($getList['value_id'] == 3)
		 						$value_code = '{CIRCLE}';
		 					elseif($getList['value_id'] == 4)
		 						$value_code = '{TXNID}';
		 					elseif($getList['value_id'] == 5)
		 						$value_code = '{MOBILE}';
		 					elseif($getList['value_id'] == 6)
		 						$value_code = '{MEMBERID}';

		 					$post_data[$getList['para_key']] = $value_code;
		 				}
		 				else
		 				{
		 					$post_data[$getList['para_key']] = $getList['value'];
		 				}
		 			}
		 		}
		 	}

		 	$responsePara = array();
		 	if($responseParaList)
		 	{
		 		foreach($responseParaList as $rkey=>$rlist)
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

		 	return array('status'=>1,'msg'=>'Success','api_url'=>$api_url,'api_id'=>$api_id,'post_data'=>$post_data,'header_data'=>$header_data,'response_type'=>$apiData['get_balance_response_type'],'responsePara'=>$responsePara,'seperator'=>$apiData['get_balance_response_seperator']);
	 	}
	 	else
	 	{
	 		return array('status'=>0,'msg'=>'No API Found.');
	 	}

		
	}


	public function call_get_balance_api($api_url,$api_post_data,$api_id = 0,$response_type = 0,$responsePara = array(),$seperator = '',$api_header_data = array())
	{
		$domain_account_id = $this->User->get_domain_account();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		if($api_header_data)
		{
			
            $headers = array();
            $h = 0;
            foreach($api_header_data as $hkey=>$hval)
            {
                $headers[$h] = $hkey.':'.$hval;
                $h++;
            }
            if($headers)
            {
	        	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    	}
		}
		if($api_post_data)
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post_data);		
		}
		$output = curl_exec($ch); 
		curl_close($ch);

		$txid = '';
		$recharge_status = '';
		$success_value = '';
		$failed_value = '';
		$pending_value = '';
		$opt_msg = '';
		$opt_ref_id = '';
		$timestamp = '';
		$memberid = '';
		$balance = '';
		$commision = '';
		if($response_type == 1)
		{
			$api_response = explode($seperator,$output);
			if($responsePara)
			{
				foreach($responsePara as $key=>$val)
				{
					if($val['value'] == 'TXNID')
					{
						$txid = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'STATUS')
					{
						$recharge_status = isset($api_response[$key]) ? trim(strtolower($api_response[$key])) : '';
						$success_value = strtolower($val['success']);
						$failed_value = strtolower($val['failed']);
						$pending_value = strtolower($val['pending']);
					}
					elseif($val['value'] == 'OPTMSG')
					{
						$opt_msg = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'OPTREFID')
					{
						$opt_ref_id = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'TIMESTAMP')
					{
						$timestamp = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'MEMBERID')
					{
						$memberid = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'BALANCE')
					{
						$balance = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
					elseif($val['value'] == 'COMMISION')
					{
						$commision = isset($api_response[$key]) ? trim($api_response[$key]) : '';
					}
				}
			}

		}
		elseif($response_type == 2)
		{
			$api_response = (array) simplexml_load_string($output);
			if($responsePara)
			{
				foreach($responsePara as $key=>$val)
				{
					if($val['value'] == 'TXNID')
					{
						$txid = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'STATUS')
					{
						$recharge_status = isset($api_response[$val['key']]) ? trim(strtolower($api_response[$val['key']])) : '';
						$success_value = strtolower($val['success']);
						$failed_value = strtolower($val['failed']);
						$pending_value = strtolower($val['pending']);
					}
					elseif($val['value'] == 'OPTMSG')
					{
						$opt_msg = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'OPTREFID')
					{
						$opt_ref_id = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'TIMESTAMP')
					{
						$timestamp = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'MEMBERID')
					{
						$memberid = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'BALANCE')
					{
						$balance = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'COMMISION')
					{
						$commision = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
				}
			}

		}
		elseif($response_type == 3)
		{
			$api_response = json_decode($output,true);
			if($responsePara)
			{
				foreach($responsePara as $key=>$val)
				{
					if($val['value'] == 'TXNID')
					{
						$txid = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'STATUS')
					{
						$recharge_status = isset($api_response[$val['key']]) ? trim(strtolower($api_response[$val['key']])) : '';
						$success_value = strtolower($val['success']);
						$failed_value = strtolower($val['failed']);
						$pending_value = strtolower($val['pending']);
					}
					elseif($val['value'] == 'OPTMSG')
					{
						$opt_msg = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'OPTREFID')
					{
						$opt_ref_id = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'TIMESTAMP')
					{
						$timestamp = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'MEMBERID')
					{
						$memberid = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'BALANCE')
					{
						$balance = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
					elseif($val['value'] == 'COMMISION')
					{
						$commision = isset($api_response[$val['key']]) ? trim($api_response[$val['key']]) : '';
					}
				}
			}

		}

		$status = 0;
		if($recharge_status == '' || $recharge_status == $failed_value)
		{
			$status = 3;
		}
		elseif($recharge_status == $pending_value)
		{
			$status = 1;
		}
		elseif($recharge_status == $success_value)
		{
			$status = 2;
		}
		
		return array(
			'status' => $status,
			'txid' => $txid,
			'operator_ref' => $opt_ref_id,
			'api_timestamp' => $timestamp,
			'opt_msg' => $opt_msg,
			'memberid' => $memberid,
			'balance' => $balance,
			'commision' => $commision,
			'api_response_id' => $api_response_id,
		);
		
	}

	public function get_bbps_biller_list($service_id = 0)
	{
		return $this->db->get_where('bbps_service_category',array('service_id'=>$service_id,'status'=>1))->result_array();
	}

	public function get_bbps_biller_id($biller_id = 0)
	{
		return $this->db->get_where('bbps_service_category',array('id'=>$biller_id))->row_array();
	}

	public function get_bbps_pmr_service_id($biller_id = 0)
	{
		return $this->db->get_where('bbps_service',array('id'=>$biller_id))->row_array();
	}

	public function get_bbps_biller_param($service_id = 0,$biller_id = 0)
	{
		return $this->db->get_where('bbps_category_params',array('service_id'=>$service_id,'cat_id'=>$biller_id))->result_array();
	}

	public function generate_imei_number()
	{
		$this->load->helper('string');
		$num1 = random_string('numeric', 2);
		$num2 = random_string('alpha', 3);
		$num3 = random_string('numeric', 1);
		$num4 = random_string('alpha', 2);
		$num5 = random_string('numeric', 2);
		$num6 = random_string('alpha', 2);
		$num7 = random_string('numeric', 4);
		return $num1.$num2.$num3.$num4.$num5.$num6.$num7;
	}


	public function generate_bbps_reference_id($recharge_unique_id = '')
	{
		$this->load->helper('string');
		$num1 = date('dMY');
		$num2 = random_string('alpha', 5);
		if($recharge_unique_id)
		{
			$num3 = $recharge_unique_id;
		}
		else
		{
			$num3 = rand(1111,9999).time();
		}
		$num4 = random_string('numeric', 7);
		return $num1.$num2.$num3.$num4;
	}


	public function generate_bbps_token($member_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		// get active token
		$get_token = $this->db->get_where('bbps_token',array('status'=>1))->row_array();
		$access_token = isset($get_token['access_token']) ? $get_token['access_token'] : '';
		if($access_token == '')
		{
			$api_url = BBPS_TOKEN_CREATE_URL;
		 	$headers = [
	            'Content-Type: application/x-www-form-urlencoded'
	        ];

	        $api_post_data = array();
	        $api_post_data['client_id'] = BBPS_CLIENT_ID;
	        $api_post_data['client_secret'] = BBPS_CLIENT_SECRET;
	        $api_post_data['grant_type'] = 'client_credentials';
	        $api_post_data['scope'] = 'read_biller_categories read_billers read_regions read_bills create_transactions read_transactions read_plans register_complain check_complain_status read_operators read_circles read_operator_circle read_packs read_agent_balance';
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL,$api_url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch, CURLOPT_POST, true);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_post_data));     
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	        $output = curl_exec ($ch);
	        curl_close ($ch);

	        // save api response 
	        $api_data = array(
	        	'account_id' => $account_id,
	        	'user_id' => $member_id,
	        	'api_response' => $output,
	        	'api_url' => $api_url,
	        	'api_post_data' => json_encode($api_post_data),
	        	'status' => 1,
	        	'created' => date('Y-m-d H:i:s')
	        );
	        $this->db->insert('bbps_api_response',$api_data);

	        $responseData = json_decode($output,true);

	        if(isset($responseData['access_token']) && $responseData['access_token'])
	        {
	        	$access_token = $responseData['access_token'];
	        	$expires_in = $responseData['expires_in'];
	        	$start_datetime = date('Y-m-d H:i:s');
	        	$end_datetime = date('Y-m-d H:i:s',strtotime($start_datetime.' +'.$expires_in.' second'));
	        	// save token data
		        $token_data = array(
		        	'access_token' => $access_token,
		        	'expires_in' => $expires_in,
		        	'start_datetime' => $start_datetime,
		        	'end_datetime' => $end_datetime,
		        	'created_by_account_id' => $account_id,
		        	'created_by_member_id' => $member_id,
		        	'status' => 1,
		        	'api_response' => $output
		        );
		        $this->db->insert('bbps_token',$token_data);
	        }

		}
		return $access_token;
	}

	public function call_bbps_service_bill_fetch_api($member_id = 0,$biller_payu_id = '',$pmr_service_id = 0,$post_data = array())
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$isInstantPayApiAllow = $this->User->get_account_instantpay_bbps_api_status($account_id);
		$status = 0;
	    $final_response = array();
		if($isInstantPayApiAllow)
		{
			$isMemberKyc =$this->db->get_where('users',array('id'=>$member_id,'is_instantpay_ekyc'=>1))->num_rows();
			if($isMemberKyc)
			{
				// generate recharge unique id
                $recharge_unique_id = rand(1111,9999).time();

				$userData = $this->db->select('instantpay_outlet_id')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
				$outlet_id = $userData['instantpay_outlet_id'];

				$getAadharData = $this->db->select('mobile,aadhar_data')->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$member_id,'status'=>2))->row_array();
				$agentMobile = $getAadharData['mobile'];
				$aadhar_data = isset($getAadharData['aadhar_data']) ? json_decode($getAadharData['aadhar_data'],true) : array();
				$pincode = isset($aadhar_data['pincode']) ? $aadhar_data['pincode'] : '';
				
				$api_url = INSTANTPAY_TXN_API;

				$params = isset($post_data['params']) ? $post_data['params'] : array();
				$paramsArray = array();
	    		if($params)
		        {
		        	$k = 1;
		        	foreach($params as $key=>$val)
		        	{
		        		$paramsArray['param'.$k] = $val;
		        		$k++;
		        	}
		        }

		        $request = array(
		            'token' => $accountData['instant_token'],
		            'request' => array(
		                'request_type' => 'BILLFETCH',
		                'outlet_id' => $outlet_id,
		                'biller_id' => $biller_payu_id,
		                'reference_txnid' => array(
		                    'agent_external' => $recharge_unique_id,
		                    'billfetch_internal' => "",
		                    'validate_internal' => ""
		                ),
		                'params' => $paramsArray,
		               'payment_channel' => 'AGT',
		               'payment_mode' => 'Cash',
		               'payment_info' => 'bill',
		               'device_info' => array(
		                   'TERMINAL_ID' => '12813923',
		                   'MOBILE' => $agentMobile,
		                   'GEOCODE' => '12.1234,12.1234',
		                   'POSTAL_CODE' => $pincode,
		               ),
		               'remarks' => array(
		                   'param1' => $agentMobile,
		                   'param2' => ""
		               )
		                
		            )
		        );

		        $header = array(
		            'content-type: application/json'
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
		        
		         
		        $bmPIData   = simplexml_load_string($output);
				$jsonResponse = json_encode((array) $bmPIData);

				/*$jsonResponse = '{"statuscode":"TXN","status":"Transaction Successful","data":{"response_type":"PAYMENT","ipay_id":"1220518155944GGNWT","biller":"Airtel","biller_refid":"91164209","value_order":"10.00","value_commercial":"0.0590","type_pricing":"MARGIN","value_tds":"0.0030","convenience_fee":"0.00","value_transaction":"9.94","transaction_mode":"DR","params":{"param1":"8104758957"}},"timestamp":"2022-05-18 15:59:49","ipay_uuid":"C5691EFCA4E98F2BF65B","orderid":"1220518155944GGNWT","environment":"PRODUCTION"}';*/

				$decodeResponse = json_decode($jsonResponse,true);

				$api_data = array(
		        	'account_id' => $account_id,
		        	'user_id' => $member_id,
		        	'api_response' => $output,
		        	'api_url' => $api_url,
		        	'api_post_data' => json_encode($request),
		        	'status' => 1,
		        	'created' => date('Y-m-d H:i:s')
		        );
		        $this->db->insert('bbps_api_response',$api_data);
		        $response_id = $this->db->insert_id();

				//save api data
		        $apiData = array(
		          'account_id' => $account_id,
		          'user_id' => $member_id,
		          'api_url' => $api_url,
		          'api_response' => $output,
		          'post_data' => json_encode($request),
		          'header_data' => json_encode($header),
		          'created' => date('Y-m-d H:i:s'),
		          'created_by' => $account_id
		        );
		        $this->db->insert('instantpay_api_response',$apiData);

		        if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
				{
					$final_response = array(
		        		'status' => 1,
		        		'amount' => isset($decodeResponse['data']['billamount']) ? $decodeResponse['data']['billamount'] : 0,
		        		'accountHolderName' => isset($decodeResponse['data']['customername']) ? $decodeResponse['data']['customername'] : '',
		        		'dueDate' => isset($decodeResponse['data']['billduedate']) ? $decodeResponse['data']['billduedate'] : ''
		        	);
				}
				else
				{
					$final_response = array(
		        		'status' => 0,
		        		'errors' => isset($decodeResponse['status']) ? $decodeResponse['status'] : '',
		        	);
				}
			}
			else
			{
				$final_response = array(
	        		'status' => 0,
	        		'errors' => 'Sorry ! Your eKyc not approved yet, please submit your eKyc.'
	        	);
			}
		}
		else
		{
			$api_url = BBPS_SERVICE_BILL_FETCH_URL;
			if($pmr_service_id == 32)
			{
				$number = isset($post_data['number']) ? $post_data['number'] : '';
				$headers = [
		            'Token:'.$accountData['dmt_token'],
		            'ServiceID:'.$pmr_service_id,
		            'biller_id:'.$biller_payu_id,
		            'BillerNumber:'.$number,
		            'BillerNumber2:'
		        ];
	    	}
	    	else
	    	{
	    		$headers = [
		            'Token:'.$accountData['dmt_token'],
		            'ServiceID:'.$pmr_service_id,
		            'biller_id:'.$biller_payu_id
		        ];
	    		$params = isset($post_data['params']) ? $post_data['params'] : array();
	    		if($params)
		        {
		        	$i = 3;
		        	$k = 1;
		        	foreach($params as $key=>$val)
		        	{
		        		if($i == 3)
		        		{
		        			$headers[$i] = 'BillerNumber:'.$val;
		        		}
		        		else
		        		{
		        			$headers[$i] = 'BillerNumber'.$k.':'.$val;
		        		}
		        		$i++;
		        		$k++;
		        	}
		        }
	    	}

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL,$api_url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	        $output = curl_exec ($ch);
	        curl_close ($ch);

	        // save api response 
	        $api_data = array(
	        	'account_id' => $account_id,
	        	'user_id' => $member_id,
	        	'api_response' => $output,
	        	'api_url' => $api_url,
	        	'api_post_data' => json_encode($headers),
	        	'status' => 1,
	        	'created' => date('Y-m-d H:i:s')
	        );
	        $this->db->insert('bbps_api_response',$api_data);

	        $responseData = json_decode($output,true);

	        // 0 = Error
	        // 1 = Success
	        
	        

	        if(isset($responseData['status_code']) && $responseData['status_code'] == 200)
	        {
	        	$final_response = array(
	        		'status' => 1,
	        		'amount' => isset($responseData['amount']) ? round($responseData['amount'],2) : 0,
	        		'accountHolderName' => isset($responseData['accountHolderName']) ? $responseData['accountHolderName'] : ''
	        	);
	        }
	        else
	        {
	        	$errors = isset($responseData['status_msg']) ? $responseData['status_msg'] : '';
	        	$final_response = array(
	        		'status' => 0,
	        		'errors' => $errors
	        	);
	        }
	    }

		return $final_response;
	}

	public function call_bbps_service_bill_pay_api($member_id = 0,$biller_payu_id = '',$pmr_service_id = 0,$post_data = array(),$recharge_unique_id = '',$billerName = '')
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$isInstantPayApiAllow = $this->User->get_account_instantpay_bbps_api_status($account_id);
		$status = 0;
	    $final_response = array();
		if($isInstantPayApiAllow)
		{
			$isMemberKyc =$this->db->get_where('users',array('id'=>$member_id,'is_instantpay_ekyc'=>1))->num_rows();
			if($isMemberKyc)
			{
				$userData = $this->db->select('instantpay_outlet_id')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
				$outlet_id = $userData['instantpay_outlet_id'];

				$getAadharData = $this->db->select('mobile,aadhar_data')->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$member_id,'status'=>2))->row_array();
				$agentMobile = $getAadharData['mobile'];
				$aadhar_data = isset($getAadharData['aadhar_data']) ? json_decode($getAadharData['aadhar_data'],true) : array();
				$pincode = isset($aadhar_data['pincode']) ? $aadhar_data['pincode'] : '';
				
				$api_url = INSTANTPAY_TXN_API;

				
				$paramsArray = array();
				if($pmr_service_id == 32)
				{
					$paramsArray['param1'] = isset($post_data['number']) ? $post_data['number'] : '';
				}
				else
				{
					$params = isset($post_data['params']) ? $post_data['params'] : array();
					if($params)
			        {
			        	$k = 1;
			        	foreach($params as $key=>$val)
			        	{
			        		$paramsArray['param'.$k] = $val;
			        		$k++;
			        	}
			        }
				}
	    		
		        // Check bill is fetch require or not
		        $fetchResponse = $this->User->instantpay_biller_detail($outlet_id,$biller_payu_id,$recharge_unique_id,$paramsArray,$agentMobile,$pincode,$member_id);

		        $request = array(
		            'token' => $accountData['instant_token'],
		            'request' => array(
		                'request_type' => 'PAYMENT',
		                'outlet_id' => $outlet_id,
		                'biller_id' => $biller_payu_id,
		                'reference_txnid' => array(
		                    'agent_external' => $recharge_unique_id,
		                    'billfetch_internal' => $fetchResponse['fetchiPayID'],
		                    'validate_internal' => $fetchResponse['validateiPayID']
		                ),
		                'params' => $paramsArray,
		               'payment_channel' => 'AGT',
		               'payment_mode' => 'Cash',
		               'payment_info' => 'bill',
		               'device_info' => array(
		                   'TERMINAL_ID' => '12813923',
		                   'MOBILE' => $agentMobile,
		                   'GEOCODE' => '12.1234,12.1234',
		                   'POSTAL_CODE' => $pincode,
		               ),
		               'remarks' => array(
		                   'param1' => $agentMobile,
		                   'param2' => ""
		               ),
		               'amount' => $post_data['amount']
		                
		            )
		        );

		        $header = array(
		            'content-type: application/json'
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
		        
		         
		        $bmPIData   = simplexml_load_string($output);
				$jsonResponse = json_encode((array) $bmPIData);

				/*$jsonResponse = '{"statuscode":"TXN","status":"Transaction Successful","data":{"response_type":"PAYMENT","ipay_id":"1220518155944GGNWT","biller":"Airtel","biller_refid":"91164209","value_order":"10.00","value_commercial":"0.0590","type_pricing":"MARGIN","value_tds":"0.0030","convenience_fee":"0.00","value_transaction":"9.94","transaction_mode":"DR","params":{"param1":"8104758957"}},"timestamp":"2022-05-18 15:59:49","ipay_uuid":"C5691EFCA4E98F2BF65B","orderid":"1220518155944GGNWT","environment":"PRODUCTION"}';*/

				$decodeResponse = json_decode($jsonResponse,true);

				$api_data = array(
		        	'account_id' => $account_id,
		        	'user_id' => $member_id,
		        	'api_response' => $output,
		        	'api_url' => $api_url,
		        	'api_post_data' => json_encode($request),
		        	'status' => 1,
		        	'created' => date('Y-m-d H:i:s')
		        );
		        $this->db->insert('bbps_api_response',$api_data);
		        $response_id = $this->db->insert_id();

				//save api data
		        $apiData = array(
		          'account_id' => $account_id,
		          'user_id' => $member_id,
		          'api_url' => $api_url,
		          'api_response' => $output,
		          'post_data' => json_encode($request),
		          'header_data' => json_encode($header),
		          'created' => date('Y-m-d H:i:s'),
		          'created_by' => $account_id
		        );
		        $this->db->insert('instantpay_api_response',$apiData);

		        $status = 2;
		        $final_response = array();

		        if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
				{
					$final_response = array(
		        		'status' => 1,
		        		'txnid' => isset($decodeResponse['orderid']) ? $decodeResponse['orderid'] : '',
		        		'response_id' => $response_id
		        	);
				}
				elseif(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TUP')
				{
					$final_response = array(
		        		'status' => 2,
		        		'txnid' => isset($decodeResponse['orderid']) ? $decodeResponse['orderid'] : '',
		        		'response_id' => $response_id
		        	);
				}
				elseif(!isset($decodeResponse['statuscode']))
				{
					$final_response = array(
		        		'status' => 2,
		        		'txnid' => isset($decodeResponse['orderid']) ? $decodeResponse['orderid'] : '',
		        		'response_id' => $response_id
		        	);
				}
				else
				{
					$final_response = array(
		        		'status' => 0,
		        		'errors' => isset($decodeResponse['status']) ? $decodeResponse['status'] : '',
		        	);
				}
			}
			else
			{
				$final_response = array(
	        		'status' => 0,
	        		'errors' => 'Sorry ! Your eKyc not approved yet, please submit your eKyc.'
	        	);
			}
		}
		else
		{
			$api_url = BBPS_SERVICE_BILL_PAY_URL;
			if($pmr_service_id == 32)
			{
				$number = isset($post_data['number']) ? $post_data['number'] : '';
			 	$headers = [
		            'Token:'.$accountData['dmt_token'],
		            'ServiceID:'.$pmr_service_id,
		            'biller_id:'.$biller_payu_id,
		            'BillerNumber:'.$number,
		            'BillerNumber2:',
		            'BillerName:'.$billerName,
		            'BillAmount:'.$post_data['amount'],
		            'TransID:'.$recharge_unique_id
		        ];
		    }
		    else
		    {
		    	$params = isset($post_data['params']) ? $post_data['params'] : array();
		    	$headers = [
		            'Token:'.$accountData['dmt_token'],
		            'ServiceID:'.$pmr_service_id,
		            'biller_id:'.$biller_payu_id,
		            'BillerName:'.$billerName,
		            'BillAmount:'.$post_data['amount'],
		            'TransID:'.$recharge_unique_id
		        ];

		        if($params)
		        {
		        	$i = 6;
		        	$k = 1;
		        	foreach($params as $key=>$val)
		        	{
		        		if($i == 6)
		        		{
		        			$headers[$i] = 'BillerNumber:'.$val;
		        		}
		        		else
		        		{
		        			$headers[$i] = 'BillerNumber'.$k.':'.$val;
		        		}
		        		$i++;
		        		$k++;
		        	}
		        }
		    }

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL,$api_url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	        $output = curl_exec ($ch);
	        curl_close ($ch);

	        // save api response 
	        $api_data = array(
	        	'account_id' => $account_id,
	        	'user_id' => $member_id,
	        	'api_response' => $output,
	        	'api_url' => $api_url,
	        	'api_post_data' => json_encode($headers),
	        	'status' => 1,
	        	'created' => date('Y-m-d H:i:s')
	        );
	        $this->db->insert('bbps_api_response',$api_data);
	        $response_id = $this->db->insert_id();

	        
	        $responseData = json_decode($output,true);

	        // 0 = Error
	        // 1 = Success
	        // 2 = Pending

	        $status = 2;
	        $final_response = array();

	        if(isset($responseData['status_code']) && $responseData['status_code'] == 200)
	        {
	        	if($responseData['status'] == 'SUCCESS')
	        	{
		        	$final_response = array(
		        		'status' => 1,
		        		'txnid' => isset($responseData['optTxnID']) ? $responseData['optTxnID'] : '',
		        		'response_id' => $response_id
		        	);
	        	}
	        	elseif($responseData['status'] == 'PENDING')
	        	{
	        		$final_response = array(
		        		'status' => 2,
		        		'txnid' => isset($responseData['optTxnID']) ? $responseData['optTxnID'] : '',
		        		'response_id' => $response_id
		        	);
	        	}
	        	elseif($responseData['status'] == 'FAILED')
	        	{
	        		$errors = isset($responseData['optMsg']) ? $responseData['optMsg'] : '';
		        	$final_response = array(
		        		'status' => 0,
		        		'errors' => $errors
		        	);
	        	}

	        }
	        elseif(!$responseData)
	        {
	        	$final_response = array(
	        		'status' => 2,
	        		'txnid' => isset($responseData['optTxnID']) ? $responseData['optTxnID'] : '',
	        		'response_id' => $response_id
	        	);
	        }
	        else
	        {
	        	$errors = isset($responseData['status_msg']) ? $responseData['status_msg'] : '';
	        	$final_response = array(
	        		'status' => 0,
	        		'errors' => $errors
	        	);
	        }
	    }

		return $final_response;
	}

	public function call_bbps_electricity_bill_fetch_api($member_id = 0,$biller_payu_id = '',$pmr_service_id = 0,$post_data = array())
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$isInstantPayApiAllow = $this->User->get_account_instantpay_bbps_api_status($account_id);
		$status = 0;
	    $final_response = array();
		if($isInstantPayApiAllow)
		{
		    
			$isMemberKyc =$this->db->get_where('users',array('id'=>$member_id,'instantpay_aeps_status'=>1))->num_rows();
		   
			if($isMemberKyc)
			{
				// generate recharge unique id
                $recharge_unique_id = rand(1111,9999).time();

				$userData = $this->db->select('instantpay_outlet_id')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
				$outlet_id = $userData['instantpay_outlet_id'];

				$getAadharData = $this->db->select('mobile,aadhar_data')->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$member_id,'status'=>1))->row_array();
				
				$agentMobile = $getAadharData['mobile'];
				$aadhar_data = isset($getAadharData['aadhar_data']) ? json_decode($getAadharData['aadhar_data'],true) : array();
				//$pincode = isset($aadhar_data['pincode']) ? $aadhar_data['pincode'] : '';
				$pincode = 302021;
				$api_url = INSTANTPAY_TXN_API;

				$params = isset($post_data['params']) ? $post_data['params'] : array();
				
				$paramsArray = array();
	    		if($params)
		        {
		        	$k = 1;
		        	foreach($params as $key=>$val)
		        	{
		        		$paramsArray['param'.$k] = $val;
		        		$k++;
		        	}
		        }

		        $request = array(
		            'token' => $accountData['instant_token'],
		            'request' => array(
		                'request_type' => 'BILLFETCH',
		                'outlet_id' => $outlet_id,
		                'biller_id' => $biller_payu_id,
		                'reference_txnid' => array(
		                    'agent_external' => $recharge_unique_id,
		                    'billfetch_internal' => "",
		                    'validate_internal' => ""
		                ),
		                'params' => $paramsArray,
		               'payment_channel' => 'AGT',
		               'payment_mode' => 'Cash',
		               'payment_info' => 'bill',
		               'device_info' => array(
		                   'TERMINAL_ID' => '12813923',
		                   'MOBILE' => $agentMobile,
		                   'GEOCODE' => '12.1234,12.1234',
		                   'POSTAL_CODE' => $pincode,
		               ),
		               'remarks' => array(
		                   'param1' => $agentMobile,
		                   'param2' => ""
		               )
		                
		            )
		        );

		        $header = array(
		            'content-type: application/json'
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
		         
		        $bmPIData   = simplexml_load_string($output);
			    
			   
				$jsonResponse = json_encode((array) $bmPIData);
				echo $jsonResponse;

				/*$jsonResponse = '{"statuscode":"TXN","status":"Transaction Successful","data":{"response_type":"PAYMENT","ipay_id":"1220518155944GGNWT","biller":"Airtel","biller_refid":"91164209","value_order":"10.00","value_commercial":"0.0590","type_pricing":"MARGIN","value_tds":"0.0030","convenience_fee":"0.00","value_transaction":"9.94","transaction_mode":"DR","params":{"param1":"8104758957"}},"timestamp":"2022-05-18 15:59:49","ipay_uuid":"C5691EFCA4E98F2BF65B","orderid":"1220518155944GGNWT","environment":"PRODUCTION"}';*/

				$decodeResponse = json_decode($jsonResponse,true);

				$api_data = array(
		        	'account_id' => $account_id,
		        	'user_id' => $member_id,
		        	'api_response' => $output,
		        	'api_url' => $api_url,
		        	'api_post_data' => json_encode($request),
		        	'status' => 1,
		        	'created' => date('Y-m-d H:i:s')
		        );
		        $this->db->insert('bbps_api_response',$api_data);
		        $response_id = $this->db->insert_id();

				//save api data
		        $apiData = array(
		          'account_id' => $account_id,
		          'user_id' => $member_id,
		          'api_url' => $api_url,
		          'api_response' => $output,
		          'post_data' => json_encode($request),
		          'header_data' => json_encode($header),
		          'created' => date('Y-m-d H:i:s'),
		          'created_by' => $account_id
		        );
		        $this->db->insert('instantpay_api_response',$apiData);

		        if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
				{
					$final_response = array(
		        		'status' => 1,
		        		'amount' => isset($decodeResponse['data']['billamount']) ? $decodeResponse['data']['billamount'] : 0,
		        		'accountHolderName' => isset($decodeResponse['data']['customername']) ? $decodeResponse['data']['customername'] : '',
		        		'dueDate' => isset($decodeResponse['data']['billduedate']) ? $decodeResponse['data']['billduedate'] : ''
		        	);
				}
				else
				{
					$final_response = array(
		        		'status' => 0,
		        		'errors' => isset($decodeResponse['status']) ? $decodeResponse['status'] : '',
		        	);
				}
			}
			else
			{
				$final_response = array(
	        		'status' => 0,
	        		'errors' => 'Sorry ! Your eKyc not approved yet, please submit your eKyc.'
	        	);
			}
		}
		else
		{
			$params = isset($post_data['params']) ? $post_data['params'] : array();
			$api_url = BBPS_ELECTRICITY_BILL_FETCH_URL;
		 	$headers = [
	            'Token:'.$accountData['dmt_token'],
	            'ServiceID:'.$pmr_service_id,
	            'biller_id:'.$biller_payu_id
	        ];

	        if($params)
	        {
	        	$i = 3;
	        	$k = 1;
	        	foreach($params as $key=>$val)
	        	{
	        		if($i == 3)
	        		{
	        			$headers[$i] = 'BillerNumber:'.$val;
	        		}
	        		else
	        		{
	        			$headers[$i] = 'BillerNumber'.$k.':'.$val;
	        		}
	        		$i++;
	        		$k++;
	        	}
	        }

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL,$api_url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	        $output = curl_exec ($ch);
	        curl_close ($ch);

	        // save api response 
	        $api_data = array(
	        	'account_id' => $account_id,
	        	'user_id' => $member_id,
	        	'api_response' => $output,
	        	'api_url' => $api_url,
	        	'api_post_data' => json_encode($headers),
	        	'status' => 1,
	        	'created' => date('Y-m-d H:i:s')
	        );
	        $this->db->insert('bbps_api_response',$api_data);

	        $responseData = json_decode($output,true);

	        // 0 = Error
	        // 1 = Success
	        
	        if(isset($responseData['status_code']) && $responseData['status_code'] == 200)
	        {
	        	$final_response = array(
	        		'status' => 1,
	        		'amount' => isset($responseData['amount']) ? round($responseData['amount'],2) : 0,
	        		'accountHolderName' => isset($responseData['accountHolderName']) ? $responseData['accountHolderName'] : ''
	        	);
	        }
	        else
	        {
	        	$errors = isset($responseData['status_msg']) ? $responseData['status_msg'] : '';
	        	$final_response = array(
	        		'status' => 0,
	        		'errors' => $errors
	        	);
	        }
	    }

		return $final_response;
	}

	public function call_bbps_electricity_bill_pay_api($member_id = 0,$biller_payu_id = '',$pmr_service_id = 0,$post_data = array(),$recharge_unique_id = '',$billerName = '')
	{
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$isInstantPayApiAllow = $this->User->get_account_instantpay_bbps_api_status($account_id);
		$status = 0;
	    $final_response = array();
		if($isInstantPayApiAllow)
		{
			$isMemberKyc =$this->db->get_where('users',array('id'=>$member_id,'is_instantpay_ekyc'=>1))->num_rows();
			if($isMemberKyc)
			{
				$userData = $this->db->select('instantpay_outlet_id')->get_where('users',array('id'=>$member_id,'account_id'=>$account_id))->row_array();
				$outlet_id = $userData['instantpay_outlet_id'];

				$getAadharData = $this->db->select('mobile,aadhar_data')->get_where('instantpay_ekyc',array('account_id'=>$account_id,'member_id'=>$member_id,'status'=>2))->row_array();
				$agentMobile = $getAadharData['mobile'];
				$aadhar_data = isset($getAadharData['aadhar_data']) ? json_decode($getAadharData['aadhar_data'],true) : array();
				$pincode = isset($aadhar_data['pincode']) ? $aadhar_data['pincode'] : '';
				
				$api_url = INSTANTPAY_TXN_API;

				$params = isset($post_data['params']) ? $post_data['params'] : array();
				$paramsArray = array();
	    		if($params)
		        {
		        	$k = 1;
		        	foreach($params as $key=>$val)
		        	{
		        		$paramsArray['param'.$k] = $val;
		        		$k++;
		        	}
		        }

		        // Check bill is fetch require or not
		        $fetchResponse = $this->User->instantpay_biller_detail($outlet_id,$biller_payu_id,$recharge_unique_id,$paramsArray,$agentMobile,$pincode,$member_id);

		        $request = array(
		            'token' => $accountData['instant_token'],
		            'request' => array(
		                'request_type' => 'PAYMENT',
		                'outlet_id' => $outlet_id,
		                'biller_id' => $biller_payu_id,
		                'reference_txnid' => array(
		                    'agent_external' => $recharge_unique_id,
		                    'billfetch_internal' => $fetchResponse['fetchiPayID'],
		                    'validate_internal' => $fetchResponse['validateiPayID']
		                ),
		                'params' => $paramsArray,
		               'payment_channel' => 'AGT',
		               'payment_mode' => 'Cash',
		               'payment_info' => 'bill',
		               'device_info' => array(
		                   'TERMINAL_ID' => '12813923',
		                   'MOBILE' => $agentMobile,
		                   'GEOCODE' => '12.1234,12.1234',
		                   'POSTAL_CODE' => $pincode,
		               ),
		               'remarks' => array(
		                   'param1' => $agentMobile,
		                   'param2' => ""
		               ),
		               'amount' => $post_data['amount']
		                
		            )
		        );

		        $header = array(
		            'content-type: application/json'
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
		        
		         
		        $bmPIData   = simplexml_load_string($output);
				$jsonResponse = json_encode((array) $bmPIData);

				/*$jsonResponse = '{"statuscode":"TXN","status":"Transaction Successful","data":{"response_type":"PAYMENT","ipay_id":"1220518155944GGNWT","biller":"Airtel","biller_refid":"91164209","value_order":"10.00","value_commercial":"0.0590","type_pricing":"MARGIN","value_tds":"0.0030","convenience_fee":"0.00","value_transaction":"9.94","transaction_mode":"DR","params":{"param1":"8104758957"}},"timestamp":"2022-05-18 15:59:49","ipay_uuid":"C5691EFCA4E98F2BF65B","orderid":"1220518155944GGNWT","environment":"PRODUCTION"}';*/

				$decodeResponse = json_decode($jsonResponse,true);

				$api_data = array(
		        	'account_id' => $account_id,
		        	'user_id' => $member_id,
		        	'api_response' => $output,
		        	'api_url' => $api_url,
		        	'api_post_data' => json_encode($request),
		        	'status' => 1,
		        	'created' => date('Y-m-d H:i:s')
		        );
		        $this->db->insert('bbps_api_response',$api_data);
		        $response_id = $this->db->insert_id();

				//save api data
		        $apiData = array(
		          'account_id' => $account_id,
		          'user_id' => $member_id,
		          'api_url' => $api_url,
		          'api_response' => $output,
		          'post_data' => json_encode($request),
		          'header_data' => json_encode($header),
		          'created' => date('Y-m-d H:i:s'),
		          'created_by' => $account_id
		        );
		        $this->db->insert('instantpay_api_response',$apiData);

		        $status = 2;
		        $final_response = array();

		        if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
				{
					$final_response = array(
		        		'status' => 1,
		        		'txnid' => isset($decodeResponse['orderid']) ? $decodeResponse['orderid'] : '',
		        		'response_id' => $response_id
		        	);
				}
				elseif(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TUP')
				{
					$final_response = array(
		        		'status' => 2,
		        		'txnid' => isset($decodeResponse['orderid']) ? $decodeResponse['orderid'] : '',
		        		'response_id' => $response_id
		        	);
				}
				elseif(!isset($decodeResponse['statuscode']))
				{
					$final_response = array(
		        		'status' => 2,
		        		'txnid' => isset($decodeResponse['orderid']) ? $decodeResponse['orderid'] : '',
		        		'response_id' => $response_id
		        	);
				}
				else
				{
					$final_response = array(
		        		'status' => 0,
		        		'errors' => isset($decodeResponse['status']) ? $decodeResponse['status'] : '',
		        	);
				}
			}
			else
			{
				$final_response = array(
	        		'status' => 0,
	        		'errors' => 'Sorry ! Your eKyc not approved yet, please submit your eKyc.'
	        	);
			}
		}
		else
		{
			$params = isset($post_data['params']) ? $post_data['params'] : array();
			$api_url = BBPS_ELECTRICITY_BILL_PAY_URL;
		 	$headers = [
	            'Token:'.$accountData['dmt_token'],
	            'ServiceID:'.$pmr_service_id,
	            'biller_id:'.$biller_payu_id,
	            'BillerName:'.$billerName,
	            'BillAmount:'.$post_data['amount'],
	            'TransID:'.$recharge_unique_id
	        ];

	        if($params)
	        {
	        	$i = 6;
	        	$k = 1;
	        	foreach($params as $key=>$val)
	        	{
	        		if($i == 6)
	        		{
	        			$headers[$i] = 'BillerNumber:'.$val;
	        		}
	        		else
	        		{
	        			$headers[$i] = 'BillerNumber'.$k.':'.$val;
	        		}
	        		$i++;
	        		$k++;
	        	}
	        }

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL,$api_url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	        $output = curl_exec ($ch);
	        curl_close ($ch);

	        // save api response 
	        $api_data = array(
	        	'account_id' => $account_id,
	        	'user_id' => $member_id,
	        	'api_response' => $output,
	        	'api_url' => $api_url,
	        	'api_post_data' => json_encode($headers),
	        	'status' => 1,
	        	'created' => date('Y-m-d H:i:s')
	        );
	        $this->db->insert('bbps_api_response',$api_data);
	        $response_id = $this->db->insert_id();

	        
	        $responseData = json_decode($output,true);

	        // 0 = Error
	        // 1 = Success
	        // 2 = Pending

	        $status = 2;
	        $final_response = array();

	        if(isset($responseData['status_code']) && $responseData['status_code'] == 200)
	        {
	        	if($responseData['status'] == 'SUCCESS')
	        	{
		        	$final_response = array(
		        		'status' => 1,
		        		'txnid' => isset($responseData['optTxnID']) ? $responseData['optTxnID'] : '',
		        		'response_id' => $response_id
		        	);
	        	}
	        	elseif($responseData['status'] == 'PENDING')
	        	{
	        		$final_response = array(
		        		'status' => 2,
		        		'txnid' => isset($responseData['optTxnID']) ? $responseData['optTxnID'] : '',
		        		'response_id' => $response_id
		        	);
	        	}
	        	elseif($responseData['status'] == 'FAILED')
	        	{
	        		$errors = isset($responseData['optMsg']) ? $responseData['optMsg'] : '';
		        	$final_response = array(
		        		'status' => 0,
		        		'errors' => $errors
		        	);
	        	}

	        }
	        elseif(!$responseData)
	        {
	        	$final_response = array(
	        		'status' => 2,
	        		'txnid' => isset($responseData['optTxnID']) ? $responseData['optTxnID'] : '',
	        		'response_id' => $response_id
	        	);
	        }
	        else
	        {
	        	$errors = isset($responseData['status_msg']) ? $responseData['status_msg'] : '';
	        	$final_response = array(
	        		'status' => 0,
	        		'errors' => $errors
	        	);
	        }
	    }

		return $final_response;
	}

	public function instantpay_biller_detail($outlet_id,$biller_payu_id,$recharge_unique_id,$paramsArray,$agentMobile,$pincode,$member_id = 0)
	{
		// generate recharge unique id
      	$recharge_unique_id = rand(1111,9999).time();
      	
		$account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($account_id);
		$api_url = INSTANTPAY_BILLER_DETAIL_API;
		$request = array(
            'token' => $accountData['instant_token'],
            'request' => array(
                'biller_id' => $biller_payu_id
            )
        );

        $header = array(
            'content-type: application/json'
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
        
         
        $bmPIData   = simplexml_load_string($output);
		$jsonResponse = json_encode((array) $bmPIData);

		//save api data
        $apiData = array(
          'account_id' => $account_id,
          'user_id' => $member_id,
          'api_url' => $api_url,
          'api_response' => $output,
          'post_data' => json_encode($request),
          'header_data' => json_encode($header),
          'created' => date('Y-m-d H:i:s'),
          'created_by' => $account_id
        );
        $this->db->insert('instantpay_api_response',$apiData);

		/*$jsonResponse = '{"statuscode":"TXN","status":"Transaction Successful","data":{"response_type":"PAYMENT","ipay_id":"1220518155944GGNWT","biller":"Airtel","biller_refid":"91164209","value_order":"10.00","value_commercial":"0.0590","type_pricing":"MARGIN","value_tds":"0.0030","convenience_fee":"0.00","value_transaction":"9.94","transaction_mode":"DR","params":{"param1":"8104758957"}},"timestamp":"2022-05-18 15:59:49","ipay_uuid":"C5691EFCA4E98F2BF65B","orderid":"1220518155944GGNWT","environment":"PRODUCTION"}';*/

		$decodeResponse = json_decode($jsonResponse,true);

		$isFetchRequired = 0;
		$fetchiPayID = '';
		$isValidRequired = 0;
		$validateiPayID = '';

        if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
		{
			if($decodeResponse['data']['biller']['item']['fetch_requirement'] == 'MANDATORY')
			{
				$isFetchRequired = 1;
			}
			if($decodeResponse['data']['biller']['item']['support_bill_validation'] == 'MANDATORY')
			{
				$isValidRequired = 1;
			}
		}

		if($isFetchRequired)
		{
			$api_url = INSTANTPAY_TXN_API;
			$request = array(
	            'token' => $accountData['instant_token'],
	            'request' => array(
	                'request_type' => 'BILLFETCH',
	                'outlet_id' => $outlet_id,
	                'biller_id' => $biller_payu_id,
	                'reference_txnid' => array(
	                    'agent_external' => $recharge_unique_id,
	                    'billfetch_internal' => "",
	                    'validate_internal' => ""
	                ),
	                'params' => $paramsArray,
	               'payment_channel' => 'AGT',
	               'payment_mode' => 'Cash',
	               'payment_info' => 'bill',
	               'device_info' => array(
	                   'TERMINAL_ID' => '12813923',
	                   'MOBILE' => $agentMobile,
	                   'GEOCODE' => '12.1234,12.1234',
	                   'POSTAL_CODE' => $pincode,
	               ),
	               'remarks' => array(
	                   'param1' => $agentMobile,
	                   'param2' => ""
	               )
	                
	            )
	        );

	        $header = array(
	            'content-type: application/json'
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
	        
	         
	        $bmPIData   = simplexml_load_string($output);
			$jsonResponse = json_encode((array) $bmPIData);

			//save api data
	        $apiData = array(
	          'account_id' => $account_id,
	          'user_id' => $member_id,
	          'api_url' => $api_url,
	          'api_response' => $output,
	          'post_data' => json_encode($request),
	          'header_data' => json_encode($header),
	          'created' => date('Y-m-d H:i:s'),
	          'created_by' => $account_id
	        );
	        $this->db->insert('instantpay_api_response',$apiData);

			$decodeResponse = json_decode($jsonResponse,true);


	        if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
			{
				$fetchiPayID = isset($decodeResponse['data']['ipay_id']) ? $decodeResponse['data']['ipay_id'] : '';
			}
			
		}

		if($isValidRequired)
		{
			$api_url = INSTANTPAY_TXN_API;
			$request = array(
	            'token' => $accountData['instant_token'],
	            'request' => array(
	                'request_type' => 'VALIDATE',
	                'outlet_id' => $outlet_id,
	                'biller_id' => $biller_payu_id,
	                'reference_txnid' => array(
	                    'agent_external' => $recharge_unique_id,
	                    'billfetch_internal' => "",
	                    'validate_internal' => ""
	                ),
	                'params' => $paramsArray,
	               'payment_channel' => 'AGT',
	               'payment_mode' => 'Cash',
	               'payment_info' => 'bill',
	               'device_info' => array(
	                   'TERMINAL_ID' => '12813923',
	                   'MOBILE' => $agentMobile,
	                   'GEOCODE' => '12.1234,12.1234',
	                   'POSTAL_CODE' => $pincode,
	               ),
	               'remarks' => array(
	                   'param1' => $agentMobile,
	                   'param2' => ""
	               ),
	               'amount' => 0
	                
	            )
	        );

	        $header = array(
	            'content-type: application/json'
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
	        
	         
	        $bmPIData   = simplexml_load_string($output);
			$jsonResponse = json_encode((array) $bmPIData);

			//save api data
	        $apiData = array(
	          'account_id' => $account_id,
	          'user_id' => $member_id,
	          'api_url' => $api_url,
	          'api_response' => $output,
	          'post_data' => json_encode($request),
	          'header_data' => json_encode($header),
	          'created' => date('Y-m-d H:i:s'),
	          'created_by' => $account_id
	        );
	        $this->db->insert('instantpay_api_response',$apiData);

			$decodeResponse = json_decode($jsonResponse,true);


	        if(isset($decodeResponse['statuscode']) && $decodeResponse['statuscode'] == 'TXN')
			{
				$validateiPayID = isset($decodeResponse['data']['ipay_id']) ? $decodeResponse['data']['ipay_id'] : '';
			}
			
		}

		return array('fetchiPayID'=>$fetchiPayID,'validateiPayID'=>$validateiPayID);
		
	}


	public function get_bbps_api_balance($member_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		$access_token = $this->User->generate_bbps_token($member_id);
		
		$api_url = BBPS_NBC_API_URL.'getAgentBalance?agentId='.BBPS_AGENT_ID;
	 	$headers = [
            'Authorization:bearer '.$access_token
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        // save api response 
        $api_data = array(
        	'account_id' => $account_id,
        	'user_id' => $member_id,
        	'api_response' => $output,
        	'api_url' => $api_url,
        	'api_post_data' => '',
        	'status' => 1,
        	'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('bbps_api_response',$api_data);
        
        $responseData = json_decode($output,true);

        // 0 = Error
        // 1 = Success
        
        $balance = 0;
        
        if(isset($responseData['balance']))
        {
        	$balance = $responseData['balance'];
        }
        
		return $balance;
	}

	public function get_api_operator_name($api_id = 0,$operator_code = '',$account_id = 0)
	{
		$operator_name = '';
		if($api_id && $operator_code)
		{
			if($account_id)
			{
				$get_operator_name = $this->db->select('operator.operator_name')->join('operator','operator.id = api_operator.opt_id')->get_where('api_operator',array('api_operator.account_id'=>$account_id,'api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
			}
			else
			{
				$get_operator_name = $this->db->select('operator.operator_name')->join('operator','operator.id = api_operator.opt_id')->get_where('api_operator',array('api_operator.api_id'=>$api_id,'api_operator.opt_code'=>$operator_code))->row_array();
			}
			$operator_name = isset($get_operator_name['operator_name']) ? $get_operator_name['operator_name'] : '';
		}
		return $operator_name;
	}
	


	public function checkMasterUpiPermission(){

    	$loggedUser = $this->User->getAdminLoggedUser(MASTER_DIST_SESSION_ID);
    	$activeService = $this->User->account_active_service($loggedUser['id']);

    	if(!in_array(5, $activeService)){

    		$this->Az->redirect('master/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized for UPI service.</div>');
    	}

    }



    public function checkDistributorUpiPermission(){

    	$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
    	$activeService = $this->User->account_active_service($loggedUser['id']);

    	if(!in_array(5, $activeService)){

    		$this->Az->redirect('distributor/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized for UPI service.</div>');
    	}

    }


    public function checkRetailerUpiPermission(){

    	$loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
    	$activeService = $this->User->account_active_service($loggedUser['id']);

    	if(!in_array(5, $activeService)){

    		$this->Az->redirect('retailer/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized for UPI service.</div>');
    	}

    }



    public function checkUserUpiPermission(){

    	$loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
    	$activeService = $this->User->account_active_service($loggedUser['id']);

    	if(!in_array(5, $activeService)){

    		$this->Az->redirect('user/dashboard', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry ! You are not authorized for UPI service.</div>');
    	}

    }



    public function sendNotification($user_id,$title,$message){
    
    	$account_id = $this->User->get_domain_account();
    	$accountData = $this->User->get_account_data($account_id);
        
        if($accountData['notification_server_key']){

    		$server_key = isset($accountData['notification_server_key']) ? $accountData['notification_server_key'] : ''; 

    		if($user_id == 0){
                
             	// send notification to all users

    			$userList = $this->db->query("SELECT * FROM tbl_users where role_id IN (3,4,5,8)")->result_array();
    			
    			if($userList){
                    
                    foreach($userList as $key => $list){
    				    
    				 	   $user_device_key = isset($list['fcm_id']) ? $list['fcm_id'] : '';
                	        
		                   if($user_device_key){

			                   $body_msg = $message;
			                       $json_data = array(
			                    	'to' => $user_device_key,
			                    	'notification' => array(
			                    	'body' => $body_msg,
			                    	'title' => ucwords($title),
			                    	'icon' => 'ic_launcher',
			                    	'sound' => 'default'
			                    	)
			                	);
			                    $data = json_encode($json_data);
			                    //FCM API end-point
			            		$url = 'https://fcm.googleapis.com/fcm/send';
			            		//api_key in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
			            		
			            		//header with content_type api key
			            		$headers = array(
			            			'Content-Type:application/json',
			            			'Authorization:key='.$server_key
			            			);
			            			//CURL request to route notification to FCM connection server (provided by Google)
			            			$ch = curl_init();
			            			curl_setopt($ch, CURLOPT_URL, $url);
			            			curl_setopt($ch, CURLOPT_POST, true);
			            			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			            			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			            			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			            			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			            			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			            			$result = curl_exec($ch);
			            			curl_close($ch);
                                    
			            	}
    			
    				}
    			      return true;
    				
              	}
           	}
    		else{

               	//send perticular user notification

    			$userDetail = $this->db->get_where('users',array('id'=>$user_id))->row_array();
    			
    			if($userDetail){

    					   $user_device_key = isset($userDetail['fcm_id']) ? $userDetail['fcm_id'] : '';
                	
		                   if($user_device_key){

			                   $body_msg = $message;
			                       $json_data = array(
			                    	'to' => $user_device_key,
			                    	'notification' => array(
			                    	'body' => $body_msg,
			                    	'title' => ucwords($title),
			                    	'icon' => 'ic_launcher',
			                    	'sound' => 'default'
			                    	)
			                	);
			                    $data = json_encode($json_data);
			                    //FCM API end-point
			            		$url = 'https://fcm.googleapis.com/fcm/send';
			            		//api_key in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
			            		
			            		//header with content_type api key
			            		$headers = array(
			            			'Content-Type:application/json',
			            			'Authorization:key='.$server_key
			            			);
			            			//CURL request to route notification to FCM connection server (provided by Google)
			            			$ch = curl_init();
			            			curl_setopt($ch, CURLOPT_URL, $url);
			            			curl_setopt($ch, CURLOPT_POST, true);
			            			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			            			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			            			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			            			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			            			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			            			$result = curl_exec($ch);
			            			
			            			if ($result === FALSE) {
			            				log_message('debug', 'CURL API Response - FCM Response ERROR');
			            				return false;	
			            			}
			            			curl_close($ch);

			            			return true;
			            	}

    			}	

    		}

    	}
    	else{

    		return false;
    	}

    }


    public function cibAutoSettlement($account_holder_name,$account_no,$ifsc,$amount,$transaction_id,$bankID,$txnType,$account_code,$account_name,$account_id,$loggedAccountID)
    {
        

        $ifsc_code = $ifsc;
        if($bankID == 35)
        {
            $ifsc_code = 'ICIC0000011';
            $txnType = 'TPA';
        }

        // Create Data
        $data = array 
        (
            "AGGRID"=>"BAAS0007",    
            "AGGRNAME" => "COGENT", 
            "CORPID" => "569811103", 
            "USERID" => "PUSHPIND", 
            "URN" => "URN569811103", 
            "UNIQUEID" => $transaction_id, 
            "DEBITACC" => "344605000211", 
            "CREDITACC" => $account_no, 
            "IFSC" => $ifsc_code, 
            "TXNTYPE" => $txnType, 
            "AMOUNT" => (string) $amount, 
            "PAYEENAME" => $account_holder_name, 
            "REMARKS" => "AEPS Portal Settlement", 
            "CURRENCY" => "INR", 
            "CUSTOMERINDUCED" => "N", 
        );

        $plainText = json_encode($data);
        $sslEncrypt = $this->sslEncrypt($plainText);
        $key = 'ryi50jFMxPtaSzUNtxK56iQkcFWjJLrMeQM';
        $encryptedData = $this->encrypt($sslEncrypt, $key);
        $payload = json_encode($encryptedData); 

        $tokenData = $this->cibGenerateToken();
        $tokenType = isset($tokenData['token_type']) ? $tokenData['token_type'] : '';
        $accessToken = isset($tokenData['access_token']) ? $tokenData['access_token'] : '';

        // save system log
		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - CIB Payout API - Post Data - '.$plainText.']'.PHP_EOL;
		$this->User->generateSettlementLog($log_msg);
        
        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'key: ecCIpsMXYix7R0JDFl1DiNYRmJSdvcZiPkN',
            'X-Requested-With: XMLHttpRequest',
            'Authorization: '.$tokenType.' '.$accessToken
        );

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, CIB_TXN_API_URL);

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
        $result = curl_exec($curl);

        // Close
        curl_close ($curl);

        
        $result = str_replace('"','',$result);

        $response = $this->sslDecrypt($this->decrypt($result, $key)); 
        $finalResponse = json_decode($response);
        $decodeResponse = json_decode($finalResponse,true);

        // save system log
		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - CIB Payout API - Response - '.$finalResponse.']'.PHP_EOL;
		$this->User->generateSettlementLog($log_msg);
        

        // save api response
        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'txnid' => $transaction_id,
            'token_type' => $tokenType,
            'access_token' => $accessToken,
            'post_data' => $plainText,
            'api_response' => $finalResponse,
            'api_url' => CIB_TXN_API_URL,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('cib_api_response',$apiData);

        /*$decodeResponse = array();
        $decodeResponse['status'] = 200;
        $decodeResponse['data']['RESPONSE'] = 'SUCCESS';
        $decodeResponse['data']['STATUS'] = 'SUCCESS';
        $decodeResponse['data']['REQID'] = '123456789';
        $decodeResponse['data']['UNIQUEID'] = $transaction_id;
        $decodeResponse['data']['UTRNUMBER'] = '6543211233';*/

        if(isset($decodeResponse['status']) && $decodeResponse['status'] == 200)
        {
            if(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'SUCCESS' && $decodeResponse['data']['STATUS'] == 'SUCCESS')
            {
                // SUCCESS RESPONSE
                return $finalResponse = array(
                    'status' => 2,
                    'msg' => 'Transaction successfully proceed.',
                    'requestID' => $decodeResponse['data']['REQID'],
                    'txnID' => $decodeResponse['data']['UNIQUEID'],
                    'rrno' => $decodeResponse['data']['UTRNUMBER']
                );
            }
            elseif(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'SUCCESS' && $decodeResponse['data']['STATUS'] == 'PENDING')
            {
                // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
            }
            elseif(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'FAILURE' && $decodeResponse['data']['STATUS'] == 'PENDING')
            {
                // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
                
            }
            elseif(isset($decodeResponse['data']['RESPONSE']) && $decodeResponse['data']['RESPONSE'] == 'FAILURE' && $decodeResponse['data']['STATUS'] == 'FAILURE')
            {
                // SUCCESS RESPONSE
                return $finalResponse = array(
                    'status' => 3,
                    'msg' => $decodeResponse['message'],
                    'txnID' => $transaction_id
                );
            }
            else
            {
                // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
            }
        }
        elseif(!isset($decodeResponse['status']))
        {
            // PENDING RESPONSE
            return $finalResponse = array(
                'status' => 1,
                'msg' => 'Transaction is under process, status will be updated soon.',
                'txnID' => $transaction_id,
            );
        }
        else
        {
            // FAILED RESPONSE
            return $finalResponse = array(
                'status' => 3,
                'msg' => $decodeResponse['message'],
                'txnID' => $transaction_id,
            );
        }
        

    }

    public function cibGenerateToken()
    {
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        // Create Data
        $data = array 
        (
            "email"=>"sonu@softmatic.in",    
            "password" => "654321", 
        );

        // Generate JSON
        $json = json_encode($data);
        
        // Create Header
        $header = array
        (
            'Content-type: application/json',
            'X-Requested-With: XMLHttpRequest'
        );

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, CIB_TOKEN_API_URL);

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

        /*$output = '{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZGFiNzI2YjllYmU3OTZiMThlYWZlYTZjMzc3Y2ZkMzVmOTRiOWIwNWU3YWM4NmEzZTkwN2M1NjM5OGE5Yjc2ZWQ2ODkyNmEwNTFmZTQwYmUiLCJpYXQiOjE2MzYzNzM1MjQsIm5iZiI6MTYzNjM3MzUyNCwiZXhwIjoxNjY3OTA5NTI0LCJzdWIiOiIxNSIsInNjb3BlcyI6W119.lQke9YX4iq0agg_g1g3N6Q4_DbGkZs-qKXQ4UQ0UVkFAVJZLdk-XCDlK_yKMyPSjXKtBji_P31Z4zL2t2nQUCduq7KzN9SH798fwLMp6IAqsuPBkrISnx5zSWVz10mpi5eXFnYKf27diH8FlZ_PiJAKZJ69GJeVF7Ir6L4X_vaTxLOu9ZGBQHK07qi4g6nCcPe6JGzKUD0V6AXG85AYDv6ztcBVqNAcydgUKWhmPiLxgDx851IlhUTLomhQ593f1BNzVR9_xyZynwTJdELTufe3QVn9aYi3fTPQI77T7Y5jZhVAqbWsP_vewggYP4_eSEDeaeU5PQkyZNZj1Ne9uQ0aZG1R4oisZE9Ecy2cTQdYW_1kvzVkwXak8KFS4IaH_u7VkfayUoaJ8pY0wm4UuFBh-8b-D9E1ajGPmpIx_GyOm0wvemr280xezuFFAWQdmdP6U9wfXKclMAwj9DwEyuEpITyzP_XdWFuuYqHES0IikfvV6whcfLaI2Xfg1LMdre4kyZxhjB3oCagBg_3Veu3W-1OCeoTJdPcWt4zXINBEhOPL651zwWhra6Btzt-Kz5zJMGIKdzgfxOX4A88CydL-Eje2u9fIxyFEiXYe647_rok2Dpo-gM4KU8a1ycP8ND0UmopT47W6xh8vqSAD6M3IwD8c0OAgMVOA1XQMlwkg","token_type":"Bearer","expires_at":"2022-11-08 12:12:04"}';*/

        $result = json_decode($output,true);

        return $result;

    }

    public function sslEncrypt($dataToEncrypt)
    {
        
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

    public function encrypt($plainText, $key)
    {
        $secretKey = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    } 

    public function decrypt($encryptedText, $key)
    {
        $key = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = $this->hextobin($encryptedText);
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


    public function coduniteAutoSettlement($pmr_token,$amount,$transaction_id,$account_code,$account_name,$account_id,$loggedAccountID)
    {
        

        // Create Data
        $data = array 
        (
            "Token"=>$pmr_token,    
            "Amount" => $amount, 
            "AEPSAPIID" => $account_code, 
            "TransID" => $transaction_id
        );

        $plainText = json_encode($data);

        // save system log
		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - PMR API - Post Data - '.$plainText.']'.PHP_EOL;
		$this->User->generateCoduniteSettlementLog($log_msg,$account_id);
        

        // Initialize
        $curl = curl_init();

        //Set Options - Open

        // URL
        curl_setopt($curl, CURLOPT_URL, PMR_SETTLEMENT_API);

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

        // Request Body
        curl_setopt($curl, CURLOPT_POSTFIELDS, $plainText);

        // Set Options - Close

        // Execute
        $result = curl_exec($curl);

        // Close
        curl_close ($curl);

        /*$result = '{"statuscode":"200","status":"success","statusmessage":"Settlement add successfully"}';*/

        $decodeResponse = json_decode($result,true);

        // save system log
		$log_msg = '['.date('d-m-Y H:i:s').' - Auto Settlement Cron - Account #'.$account_code.' ('.$account_name.') - PMR API - Response - '.$result.']'.PHP_EOL;
		$this->User->generateCoduniteSettlementLog($log_msg,$account_id);
        

        // save api response
        $apiData = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'txnid' => $transaction_id,
            'post_data' => $plainText,
            'api_response' => $result,
            'api_url' => PMR_SETTLEMENT_API,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('pmr_api_response',$apiData);

        if(isset($decodeResponse['status']) && $decodeResponse['status'] == 'success')
        {
            // SUCCESS RESPONSE
            return $finalResponse = array(
                'status' => 2,
                'msg' => 'Transaction successfully proceed.'
            );
        }
        elseif(isset($decodeResponse['status']) && $decodeResponse['status'] == 'failed')
        {
        	// SUCCESS RESPONSE
                return $finalResponse = array(
                    'status' => 3,
                    'msg' => $decodeResponse['statusmessage'],
                    'txnID' => $transaction_id
                );
        }
        else
        {
            // PENDING RESPONSE
                return $finalResponse = array(
                    'status' => 1,
                    'msg' => 'Transaction is under process, status will be updated soon.',
                    'txnID' => $transaction_id,
                );
        }


    }


    public function checkSuperEmployePermission($mode = SUPERADMIN_EMPLOYE_SESSION_ID, $is_front = false) {
        
        $user = $this->session->userdata($mode);
		$this->lang->load('front', 'english');
        if (!$user) {
            // Load language
            $currLang = $this->session->userdata('language');
            $this->lang->load('superadmin/dashboard', $currLang);
            $this->load->helper('language');

            if ($is_front === false) {
                $this->session->set_flashdata('message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            } else {
                $this->session->set_flashdata('system_message_error', lang('COMMON_ACCESS_DENIED'));
                redirect('login');
            }
        }


    }
	

	public function menu_permission($menu_id = 0, $type = 1)
	{
		$loggedUser = $this->User->getLoggedUser(SUPERADMIN_EMPLOYE_SESSION_ID);
		$account_id = $loggedUser['id'];
		// get account access role
		$get_access_role_id = $this->db->select('employe_role')->get_where('users',array('id'=>$account_id))->row_array();
		$access_role_id = isset($get_access_role_id['employe_role']) ? $get_access_role_id['employe_role'] : 0 ;
		$column = 'menu_id';
		if($type == 2)
		{
			$column = 'sub_menu_id';
		}
		// check role id 
		$chk_role = $this->db->get_where('superadmin_role_permission',array('role_id'=>$access_role_id,$column=>$menu_id))->num_rows();
		return $chk_role;
	}


	public function admin_menu_permission($menu_id = 0, $type = 1)
	{
		$loggedUser = $this->User->getLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		$account_id = $loggedUser['id'];
		// get account access role
		$get_access_role_id = $this->db->select('employe_role')->get_where('users',array('id'=>$account_id))->row_array();
		$access_role_id = isset($get_access_role_id['employe_role']) ? $get_access_role_id['employe_role'] : 0 ;
		$column = 'menu_id';
		if($type == 2)
		{
			$column = 'sub_menu_id';
		}
		// check role id 
		$chk_role = $this->db->get_where('admin_role_permission',array('role_id'=>$access_role_id,$column=>$menu_id))->num_rows();
		return $chk_role;
	}

	public function check_domain_ssl($domain = ''){

		$ip = gethostbyname($domain);
        $url = "http://" . $domain;
        $orignal_parse = parse_url($url, PHP_URL_HOST);
        $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
        $read = stream_socket_client("ssl://" . $orignal_parse . ":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
        $cert = stream_context_get_params($read);
        
       return $result = (!empty($cert)) ? 1 : 0;

	}

	public function forceAddStatementCom($txnID,$aadharNumber,$iin,$amount,$recordID,$account_id,$memberID)
    {       
        
        $loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        
        $admin_id = $this->User->get_admin_id($account_id);
        $adminCommisionData = $this->User->get_admin_aeps_commission($amount,$account_id,2);
        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

        $commisionData = $this->User->get_aeps_commission($amount,$loggedUser['id'],2,$account_id);
        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

        if($com_amount)
        {
            $commData = array(
                'account_id' => $account_id,
                'member_id' => $memberID,
                'type' => 2,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $com_amount,
                'is_surcharge' => $is_surcharge,
                'wallet_settle_amount' => $com_amount,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
            );
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
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
            }
            else
            {
                $after_wallet_balance = $before_wallet_balance + $com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Commission Amount Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
            }
        }

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
                'type' => 2,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $admin_com_amount,
                'is_surcharge' => $admin_is_surcharge,
                'wallet_settle_amount' => $admin_com_amount,
                'is_paid' => $is_paid,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
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
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('collection_wallet',$wallet_data);

            }
            
        }

        if($loggedUser['role_id'] == 4)
        {
            
            $this->User->distribute_aeps_commision($recordID,$txnID,$amount,$memberID,$com_amount,$is_surcharge,2,'DT',$loggedUser['user_code'],$account_id);

        }
        elseif($loggedUser['role_id'] == 5)
        {
            
            $this->User->distribute_aeps_commision($recordID,$txnID,$amount,$memberID,$com_amount,$is_surcharge,2,'RT',$loggedUser['user_code'],$account_id);

        }
        
        
        return true;
    }


    public function forceAddBalance($txnID,$aadharNumber,$iin,$amount,$recordID,$serviceType = '',$account_id = 0,$memberID = 0)
    {       
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        
        $com_type = 0;
        if($serviceType == 'balwithdraw')
        {
            $com_type = 1;
        }
        elseif($serviceType == 'aadharpay')
        {
            $com_type = 3;
        }

        $admin_id = $this->User->get_admin_id($account_id);
        $adminCommisionData = $this->User->get_admin_aeps_commission($amount,$account_id,$com_type);
        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

        $commisionData = $this->User->get_aeps_commission($amount,$loggedUser['id'],$com_type,$account_id);
        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

        //get member wallet_balance
        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

        // update member wallet
        $after_balance = $before_wallet_balance + $amount;
        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $memberID,    
            'before_balance'      => $before_wallet_balance,
            'amount'              => $amount,  
            'after_balance'       => $after_balance,      
            'status'              => 1,
            'type'                => 1,   
            'wallet_type'         => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'AEPS Txn #'.$txnID.' Amount Credited.'
        );

        $this->db->insert('member_wallet',$wallet_data);

        
        // calculate aeps commision
        if($com_amount)
        {
            $commData = array(
                'account_id' => $account_id,
                'member_id' => $memberID,
                'type' => $com_type,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $com_amount,
                'is_surcharge' => $is_surcharge,
                'wallet_settle_amount' => $com_amount,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
            );
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
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
            }
            else
            {
                $after_wallet_balance = $before_wallet_balance + $com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Commission Amount Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
            }
        }

        //get member wallet_balance
        $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);
        $after_wallet_balance = $before_wallet_balance + $amount;

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
            'description'         => 'AEPS Txn #'.$txnID.' Amount Credited.'
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
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $admin_com_amount,
                'is_surcharge' => $admin_is_surcharge,
                'wallet_settle_amount' => $admin_com_amount,
                'is_paid' => $is_paid,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
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
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('collection_wallet',$wallet_data);

                
            }
            
        }

        if($loggedUser['role_id'] == 4)
        {
            
            $this->User->distribute_aeps_commision($recordID,$txnID,$amount,$memberID,$com_amount,$is_surcharge,$com_type,'DT',$loggedUser['user_code'],$account_id);

        }
        elseif($loggedUser['role_id'] == 5)
        {
            $this->User->distribute_aeps_commision($recordID,$txnID,$amount,$memberID,$com_amount,$is_surcharge,$com_type,'RT',$loggedUser['user_code'],$account_id);
		}
        
        return true;
    }

    public function forceAddStatementComIcici($txnID,$aadharNumber,$iin,$amount,$recordID,$account_id,$memberID)
    {       
        
        $loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        $accountData = $this->User->get_account_data($account_id);
        $admin_id = $this->User->get_admin_id($account_id);
        $adminCommisionData = $this->User->get_admin_aeps_commission($amount,$account_id,2);
        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

        $commisionData = $this->User->get_aeps_commission($amount,$loggedUser['id'],2,$account_id);
        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

        if($com_amount)
        {
            $commData = array(
                'account_id' => $account_id,
                'member_id' => $memberID,
                'type' => 2,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $com_amount,
                'is_surcharge' => $is_surcharge,
                'wallet_settle_amount' => $com_amount,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
            );
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
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
            }
            else
            {
                $after_wallet_balance = $before_wallet_balance + $com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Commission Amount Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                

                if($accountData['is_tds_amount'] == 1)
                {

                    $before_balance = $this->User->getMemberWalletBalanceSP($memberID);

	                $tds_amount = round(($com_amount*5)/100,2);

	                $after_balance = $before_balance - $tds_amount;

	                $wallet_data = array(
	                    'account_id'          => $account_id,
	                    'member_id'           => $memberID,    
	                    'before_balance'      => $before_balance,
	                    'amount'              => $tds_amount,  
	                    'after_balance'       => $after_balance,      
	                    'status'              => 1,
	                    'type'                => 2,  
	                    'wallet_type'         => 1,      
	                    'created'             => date('Y-m-d H:i:s'),      
	                    'credited_by'         => $memberID,
	                    'description'         => 'AEPS Txn  #'.$txnID.'  Commision tds amount deducted'
	                );

	                $this->db->insert('member_wallet',$wallet_data);

	                

	                //save tds entry 


	                $wallet_data = array(
	                    'account_id'          => $account_id,
	                    'member_id'           => $memberID,  
	                    'record_id'            =>$recordID,
	                    'com_amount'      => $com_amount,
	                    'tds_amount'              =>$tds_amount, 
	                    'status'              => 1,
	                    'type'                => 2,
	                    'created'             => date('Y-m-d H:i:s'),      
	                    'credited_by'         => $memberID,
	                    'description'         => 'AEPS Txn  #'.$txnID.'  Commision tds amount deducted'
	                );

	                $this->db->insert('tds_report',$wallet_data);
				}

            }
        }

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
                'type' => 2,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $admin_com_amount,
                'is_surcharge' => $admin_is_surcharge,
                'wallet_settle_amount' => $admin_com_amount,
                'is_paid' => $is_paid,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
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
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('collection_wallet',$wallet_data);

                
            }
            
        }

        if($loggedUser['role_id'] == 4)
        {
            
            $this->User->distribute_aeps_commision($recordID,$txnID,$memberID,$amount,$com_amount,$is_surcharge,2,'DT',$loggedUser['user_code']);

        }
        elseif($loggedUser['role_id'] == 5)
        {
            $this->User->distribute_aeps_commision($recordID,$txnID,$memberID,$amount,$com_amount,$is_surcharge,2,'RT',$loggedUser['user_code']);

        }
        
        
        return true;
    }

    public function forceAddBalanceIcici($txnID,$aadharNumber,$iin,$amount,$recordID,$serviceType = '',$account_id = 0,$memberID = 0)
    {       
        $accountData = $this->User->get_account_data($account_id);
        $loggedUser = $this->db->get_where('users',array('id'=>$memberID))->row_array();
        
        $com_type = 0;
        if($serviceType == 'balwithdraw')
        {
            $com_type = 1;
        }
        elseif($serviceType == 'aadharpay')
        {
            $com_type = 3;
        }

        $admin_id = $this->User->get_admin_id($account_id);
        $adminCommisionData = $this->User->get_admin_aeps_commission($amount,$account_id,$com_type);
        $admin_com_amount = isset($adminCommisionData['commission_amount']) ? $adminCommisionData['commission_amount'] : 0 ;
        $admin_is_surcharge = isset($adminCommisionData['is_surcharge']) ? $adminCommisionData['is_surcharge'] : 0 ;

        $commisionData = $this->User->get_aeps_commission($amount,$loggedUser['id'],$com_type,$account_id);
        $com_amount = isset($commisionData['commission_amount']) ? $commisionData['commission_amount'] : 0 ;
        $is_surcharge = isset($commisionData['is_surcharge']) ? $commisionData['is_surcharge'] : 0 ;

        //get member wallet_balance
        $before_wallet_balance = $this->User->getMemberWalletBalanceSP($memberID);

        // update member wallet
        $after_balance = $before_wallet_balance + $amount;
        $wallet_data = array(
            'account_id'          => $account_id,
            'member_id'           => $memberID,    
            'before_balance'      => $before_wallet_balance,
            'amount'              => $amount,  
            'after_balance'       => $after_balance,      
            'status'              => 1,
            'type'                => 1,   
            'wallet_type'         => 1,   
            'created'             => date('Y-m-d H:i:s'),      
            'description'         => 'AEPS Txn #'.$txnID.' Amount Credited.'
        );

        $this->db->insert('member_wallet',$wallet_data);

        

        // calculate aeps commision
        if($com_amount)
        {
            $commData = array(
                'account_id' => $account_id,
                'member_id' => $memberID,
                'type' => $com_type,
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $com_amount,
                'is_surcharge' => $is_surcharge,
                'wallet_settle_amount' => $com_amount,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
            );
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
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                
            }
            else
            {
                $after_wallet_balance = $before_wallet_balance + $com_amount;

                $wallet_data = array(
                    'account_id'          => $account_id,
                    'member_id'           => $memberID,    
                    'before_balance'      => $before_wallet_balance,
                    'amount'              => $com_amount,  
                    'after_balance'       => $after_wallet_balance,      
                    'status'              => 1,
                    'type'                => 1,   
                    'wallet_type'         => 1,   
                    'created'             => date('Y-m-d H:i:s'),      
                    'description'         => 'AEPS Txn #'.$txnID.' Commission Amount Credited.'
                );

                $this->db->insert('member_wallet',$wallet_data);

                

                if($accountData['is_tds_amount'] == 1)
                {

                    $before_balance = $this->User->getMemberWalletBalanceSP($memberID);

	                $tds_amount = $com_amount*5/100;

	                $after_balance = $before_balance - $tds_amount;

	                $wallet_data = array(
	                    'account_id'          => $account_id,
	                    'member_id'           => $memberID,    
	                    'before_balance'      => $before_balance,
	                    'amount'              => $tds_amount,  
	                    'after_balance'       => $after_balance,      
	                    'status'              => 1,
	                    'type'                => 2,  
	                    'wallet_type'         => 1,      
	                    'created'             => date('Y-m-d H:i:s'),      
	                    'credited_by'         => $memberID,
	                    'description'         => 'AEPS Txn  #'.$txnID.'  Commision tds amount deducted'
	                );

	                $this->db->insert('member_wallet',$wallet_data);

	                


	                //save tds entry 


	                $wallet_data = array(
	                    'account_id'          => $account_id,
	                    'member_id'           => $memberID,  
	                    'record_id'            =>$recordID,
	                    'com_amount'      => $com_amount,
	                    'tds_amount'              =>$tds_amount, 
	                    'status'              => 1,
	                    'type'                => 2,
	                    'created'             => date('Y-m-d H:i:s'),      
	                    'credited_by'         => $memberID,
	                    'description'         => 'AEPS Txn  #'.$txnID.'  Commision tds amount deducted'
	                );

	                $this->db->insert('tds_report',$wallet_data);




                }
            }
        }

        //get member wallet_balance
        $before_wallet_balance = $this->User->getMemberCollectionWalletBalanceSP($admin_id);

        $after_wallet_balance = $before_wallet_balance + $amount;

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
            'description'         => 'AEPS Txn #'.$txnID.' Amount Credited.'
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
                'txnID' => $txnID,
                'amount' => $amount,
                'com_amount' => $admin_com_amount,
                'is_surcharge' => $admin_is_surcharge,
                'wallet_settle_amount' => $admin_com_amount,
                'is_paid' => $is_paid,
                'status' => 1,
                'created'             => date('Y-m-d H:i:s'),      
                'created_by'         => $memberID,
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
                    'description'         => 'AEPS Txn #'.$txnID.' Charge Amount Debited.'
                );

                $this->db->insert('collection_wallet',$wallet_data);

            }
            
        }

        if($loggedUser['role_id'] == 4)
        {
            $this->User->distribute_aeps_commision($recordID,$txnID,$memberID,$amount,$com_amount,$is_surcharge,$com_type,'DT',$loggedUser['user_code']);

        }
        elseif($loggedUser['role_id'] == 5)
        {
        	$this->User->distribute_aeps_commision($recordID,$txnID,$memberID,$amount,$com_amount,$is_surcharge,$com_type,'RT',$loggedUser['user_code']);
        	
		}
        
        return true;
    }

    public function utiKycStatus($mobile = ''){

    	$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
		$pancard_kyc_status_url = PANCARD_KYC_STATUS_CHECK_URL;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$pancard_kyc_status_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            'Token: '.$accountData['dmt_token'],
            'mobile: '.$mobile,
            'Content-Type:application/x-www-form-urlencoded'
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec ($ch);
        curl_close ($ch);
        
        return $responseData = json_decode($output,true);
        
	}

	public function get_lat_lon($address = '')
	{
		$api_url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&key='.GOOGLE_GEOCODE_KEY;
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec ($ch);
        curl_close ($ch);

        $decodeResponse = json_decode($output,true);

        $lat = isset($decodeResponse['results'][0]['geometry']['location']['lat']) ? $decodeResponse['results'][0]['geometry']['location']['lat'] : '';
        $lng = isset($decodeResponse['results'][0]['geometry']['location']['lng']) ? $decodeResponse['results'][0]['geometry']['location']['lng'] : '';

        return array('lat'=>$lat,'lng'=>$lng);
	}

	public function getInstantLoanText($member_id = 0)
	{
		$account_id = $this->User->get_domain_account();
		$recordData = $this->db->get_where('account_instant_loan',array('account_id'=>$account_id,'member_id'=>$member_id))->row_array();
		$text_url = isset($recordData['text_url']) ? $recordData['text_url'] : '';
		return $text_url;
	}

	public function get_admin_instant_cogent_api($account_id)
	{
		$recordData = $this->db->get_where('account',array('id'=>$account_id,'is_cogent_instant_api'=>1))->num_rows();
		return $recordData;
	}

	public function getCibBalance()
	 {
	 	
	 	/*$header = [
		    'Content-type: text/plain',
		    'apikey: eASJqqNDQGnQsIb1FqMxYJAj4Dy9nZld'
		];
	 	$api_url = "https://apibankingone.icicibank.com/api/Corporate/CIB/v1/BalanceInquiry";
	    $data = array 
		(
		    "AGGRID"=>"OTOE0622",    
		    "CORPID" => "578854260", 
		    "USERID" => "SAMADNAI", 
		    "URN" => "SR234708898", 
		    "ACCOUNTNO" => "114705001499"
		);

		$plainText = json_encode($data);
		$payload = base64_encode($this->sslEncrypt($plainText));

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
		$result = curl_exec($curl);

		// Close
		curl_close ($curl);
		
		$response = $this->sslDecrypt(base64_decode($result)); 


		$decodeData = json_decode($response,true);*/
		
		return isset($decodeData['EFFECTIVEBAL']) ? $decodeData['EFFECTIVEBAL'] : 0;

	 }


	public function cibStatusCheck($transaction_id = '', $loggedAccountID = 0)
	{
		$account_id = $this->User->get_domain_account();
		
		$header = [
		    'Content-type: text/plain',
		    'apikey: eASJqqNDQGnQsIb1FqMxYJAj4Dy9nZld'
		];
	 	#$api_url = "https://apibankingone.icicibank.com/api/Corporate/CIB/v1/TransactionInquiry";
	 	$api_url = "";

	 	$data = array 
		(
		    "AGGRID"=>"OTOE0622",
		    "CORPID" => "578854260", 
		    "USERID" => "SAMADNAI", 
		    "URN" => "SR234708898", 
		    "UNIQUEID" => $transaction_id
		);

		$plainText = json_encode($data);
		$payload = base64_encode($this->sslEncrypt($plainText));

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
		$result = curl_exec($curl);

		// Close
		curl_close ($curl);
		
		$response = $this->sslDecrypt(base64_decode($result));

		/*$response = '{"STATUS":"SUCCESS","URN":"SR234708898","UNIQUEID":"'.$transaction_id.'","UTRNUMBER":"032008953851","RESPONSE":"SUCCESS"}';*/


		$decodeResponse = json_decode($response,true);


		// save api response
		$apiData = array(
            'account_id' => $account_id,
            'user_id' => $loggedAccountID,
            'txnid' => $transaction_id,
            'post_data' => $plainText,
            'api_response' => $response,
            'api_url' => $api_url,
            'status' => 1,
            'created' => date('Y-m-d H:i:s')
        );
        $this->db->insert('cib_api_response',$apiData);

        if(isset($decodeResponse['RESPONSE']) && $decodeResponse['RESPONSE'] == 'SUCCESS' && $decodeResponse['STATUS'] == 'SUCCESS')
        {
            // SUCCESS RESPONSE
            return $finalResponse = array(
                'status' => 2,
                'msg' => 'Transaction successfully proceed.',
                'requestID' => '',
                'txnID' => $decodeResponse['UNIQUEID'],
                'rrno' => $decodeResponse['UTRNUMBER']
            );
        }
        elseif(isset($decodeResponse['RESPONSE']) && $decodeResponse['RESPONSE'] == 'SUCCESS' && $decodeResponse['STATUS'] == 'PENDING')
        {
            // PENDING RESPONSE
            return $finalResponse = array(
                'status' => 1,
                'msg' => 'Transaction is under process, status will be updated soon.',
                'txnID' => $transaction_id,
            );
        }
        elseif(isset($decodeResponse['RESPONSE']) && $decodeResponse['RESPONSE'] == 'FAILURE' && $decodeResponse['STATUS'] == 'PENDING')
        {
            // PENDING RESPONSE
            return $finalResponse = array(
                'status' => 1,
                'msg' => 'Transaction is under process, status will be updated soon.',
                'txnID' => $transaction_id,
            );
            
        }
        elseif(isset($decodeResponse['RESPONSE']) && $decodeResponse['RESPONSE'] == 'FAILURE' && $decodeResponse['STATUS'] == 'FAILURE')
        {
            // SUCCESS RESPONSE
            return $finalResponse = array(
                'status' => 3,
                'msg' => $decodeResponse['MESSAGE'],
                'txnID' => $transaction_id
            );
        }
        elseif(isset($decodeResponse['RESPONSE']) && $decodeResponse['RESPONSE'] == 'SUCCESS' && $decodeResponse['STATUS'] == 'FAILURE')
        {
            // SUCCESS RESPONSE
            return $finalResponse = array(
                'status' => 3,
                'msg' => 'Failed from bank side',
                'txnID' => $transaction_id
            );
        }
        else
        {
            // PENDING RESPONSE
            return $finalResponse = array(
                'status' => 1,
                'msg' => 'Transaction is under process, status will be updated soon.',
                'txnID' => $transaction_id,
            );
        }

		

		
	}
	 
	 
	 public function getMemberDMTStatus($account_id = 0)
	{
		// get domain id
		$get_domain_id = $this->db->select('dmt_status')->get_where('users',array('id'=>$account_id))->row_array();
		return isset($get_domain_id['dmt_status']) ? $get_domain_id['dmt_status'] : 0;
	}
	
	
	
	public function get_pan_activation_charge($member_id = 0)
	{
		
		$member_package_id = $this->User->getMemberPackageID($member_id);
		$member_role_id = $this->User->getMemberRoleID($member_id);
		
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		// get surcharge
		$getSurcharge = $this->db->get_where('pan_activation_charge',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id))->row_array();

		if($getSurcharge)
		{
			$is_flat = $getSurcharge['is_flat'];
			if($member_role_id == 3)
			{
				$surcharge = $getSurcharge['md_commision'];
			}
			elseif($member_role_id == 4)
			{
				$surcharge = $getSurcharge['dt_commision'];
			}
			elseif($member_role_id == 5)
			{
				$surcharge = $getSurcharge['rt_commision'];
			}
			elseif($member_role_id == 6)
			{
				$surcharge = $getSurcharge['api_commision'];
			}
			elseif($member_role_id == 8)
			{
				$surcharge = $getSurcharge['user_commision'];
			}
			

			$surcarge_amount = round(($surcharge/100)*$amount,2);

			if($is_flat)
			{
				$surcarge_amount = $surcharge;
			}
		}
		return $surcarge_amount;
	}

	


	



public function distribute_pancard_commision($dmt_id, $transaction_id, $account_id, $surcharge_amount)
	{

		$domain_account_id = $this->User->get_domain_account();
		 $accountData = $this->User->get_account_data($domain_account_id);
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		// save system log
	   
        if($user_role_id == 4)
		{
			$md_id = $this->User->get_master_distributor_id($account_id);
			if($md_id)
			{
				// get dmr surcharge
	            $md_surcharge_amount = $this->User->get_pan_activation_charge($md_id);
	            

		    	if($surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $surcharge_amount - $md_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// credit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAN CARD',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN Card Activation #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 

		                if($accountData['is_tds_amount'] == 1)
			                {

			                	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();

                			$before_balance = $accountBalanceData['wallet_balance'];

			                $tds_amount = $commision*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'PAN Card Activation #'.$transaction_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet);

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      => $commision,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'PAN Card Activation #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);

			                }


		    		}
		    	}
		    	elseif($surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// debit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAN Card',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN Card Activation #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 
		    		}
		    	}
		    }
            
		}
		elseif($user_role_id == 5)
		{
			$distributor_id = $this->User->get_distributor_id($account_id);
			if($distributor_id)
			{
				// get dmr surcharge
	            $dist_surcharge_amount = $this->User->get_pan_activation_charge($distributor_id);
	            
		    	if($surcharge_amount > $dist_surcharge_amount)
		    	{
		    		$commision = $surcharge_amount - $dist_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// credit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'PAN Card',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN Card Activation #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$distributor_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 

		                if($accountData['is_tds_amount'] == 1)
			                {

			                	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();

                			$before_balance = $accountBalanceData['wallet_balance'];

			                $tds_amount = $commision*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'PAN Card Activation #'.$transaction_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$distributor_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet);

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $distributor_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      => $commision,
                                'tds_amount'              =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'PAN Card Activation #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);
                            
			                }

		    		}
		    	}
		    	elseif($surcharge_amount < $dist_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// debit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'PAN Card',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN Card Activation #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$distributor_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 
		    		}
		    	}

	            $md_id = $this->User->get_master_distributor_id($distributor_id);

	            // get dmr surcharge
	            $md_surcharge_amount = $this->User->get_pan_activation_charge($md_id);
	            
	           
	        
		    	if($dist_surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $md_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// credit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAN Card',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN Card Activation #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 


		                if($accountData['is_tds_amount'] == 1)
			                {

			                	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();

                			$before_balance = $accountBalanceData['wallet_balance'];

			                $tds_amount = $commision*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'PAN Card Activation #'.$transaction_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet);

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      	  => $commision,
                                'tds_amount'           =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'PAN Card Activation #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);
                            
			                }


		    		}
		    	}
		    	elseif($dist_surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $dist_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// debit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAN Card',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN Card Activation #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 
		    		}
		    	}
		    }
		}
	}

	// find pan

	public function get_find_pan_charge($member_id = 0)
	{
		
		$member_package_id = $this->User->getMemberPackageID($member_id);
		$member_role_id = $this->User->getMemberRoleID($member_id);
		
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		// get surcharge
		$getSurcharge = $this->db->get_where('find_pan_charge',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id))->row_array();



		if($getSurcharge)
		{
			$is_flat = $getSurcharge['is_flat'];
			if($member_role_id == 3)
			{
				$surcharge = $getSurcharge['md_commision'];
			}
			elseif($member_role_id == 4)
			{
				$surcharge = $getSurcharge['dt_commision'];
			}
			elseif($member_role_id == 5)
			{
				$surcharge = $getSurcharge['rt_commision'];
			}
			elseif($member_role_id == 6)
			{
				$surcharge = $getSurcharge['api_commision'];
			}
			elseif($member_role_id == 8)
			{
				$surcharge = $getSurcharge['user_commision'];
			}
			

			$surcarge_amount = round(($surcharge/100)*$amount,2);

			if($is_flat)
			{
				$surcarge_amount = $surcharge;
			}
		}
		return $surcarge_amount;
	}

	


	



	public function distribute_find_pan_commision($dmt_id, $transaction_id, $account_id, $surcharge_amount)
	{

		$domain_account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($domain_account_id);
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		// save system log
	   
        if($user_role_id == 4)
		{
			$md_id = $this->User->get_master_distributor_id($account_id);
			if($md_id)
			{
				// get dmr surcharge
	            $md_surcharge_amount = $this->User->get_find_pan_charge($md_id);
	            

		    	if($surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $surcharge_amount - $md_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// credit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'FIND PAN',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'FIND PAN #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 


		               if($accountData['is_tds_amount'] == 1)
			                {

			                	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();

                			$before_balance = $accountBalanceData['wallet_balance'];

			                $tds_amount = $commision*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'FIND PAN #'.$transaction_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet);

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      	  => $commision,
                                'tds_amount'           =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'FIND PAN #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);
                            
			                }

		    		}
		    	}
		    	elseif($surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// debit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'FIND PAN',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'FIND PAN #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 
		    		}
		    	}
		    }
            
		}
		elseif($user_role_id == 5)
		{
			$distributor_id = $this->User->get_distributor_id($account_id);
			if($distributor_id)
			{
				// get dmr surcharge
	            $dist_surcharge_amount = $this->User->get_find_pan_charge($distributor_id);
	            
		    	if($surcharge_amount > $dist_surcharge_amount)
		    	{	
		    		
		    		$commision = $surcharge_amount - $dist_surcharge_amount;
		    		
		    	
		    		if($commision)
		    		{
		    			// credit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'FIND PAN',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'FIND #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$distributor_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 


		                if($accountData['is_tds_amount'] == 1)
			                {

			                	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();

                			$before_balance = $accountBalanceData['wallet_balance'];

			                $tds_amount = $commision*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'FIND PAN #'.$transaction_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$distributor_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet);

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $distributor_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      	  => $commision,
                                'tds_amount'           =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'FIND PAN #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);
                            
			                }


		    		}
		    	}
		    	elseif($surcharge_amount < $dist_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// debit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'FIND PAN',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'FIND PAN #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$distributor_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 
		    		}
		    	}

	            $md_id = $this->User->get_master_distributor_id($distributor_id);

	            // get dmr surcharge
	            $md_surcharge_amount = $this->User->get_find_pan_charge($md_id);
	            
	           
	        
		    	if($dist_surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $md_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// credit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'FIND PAN',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'FIND PAN #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 


		                if($accountData['is_tds_amount'] == 1)
			                {

			                	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();

                			$before_balance = $accountBalanceData['wallet_balance'];

			                $tds_amount = $commision*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'FIND PAN #'.$transaction_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet);

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      	  => $commision,
                                'tds_amount'           =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'FIND PAN #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);
                            
			                }
			                


		    		}
		    	}
		    	elseif($dist_surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $dist_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// debit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'FIND PAN',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'FIND PAN #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 
		    		}
		    	}
		    }
		}
	}



	// pan charge


	public function get_pan_charge($member_id = 0)
	{
		
		$member_package_id = $this->User->getMemberPackageID($member_id);
		$member_role_id = $this->User->getMemberRoleID($member_id);
		
		$surcarge_amount = 0;
		$domain_account_id = $this->User->get_domain_account();
		// get surcharge
		$getSurcharge = $this->db->get_where('pan_charge',array('account_id'=>$domain_account_id,'package_id'=>$member_package_id))->row_array();
        


		if($getSurcharge)
		{
			$is_flat = $getSurcharge['is_flat'];
			if($member_role_id == 3)
			{
				$surcharge = $getSurcharge['md_commision'];
			}
			elseif($member_role_id == 4)
			{
				$surcharge = $getSurcharge['dt_commision'];
			}
			elseif($member_role_id == 5)
			{
				$surcharge = $getSurcharge['rt_commision'];
			}
			elseif($member_role_id == 6)
			{
				$surcharge = $getSurcharge['api_commision'];
			}
			elseif($member_role_id == 8)
			{
				$surcharge = $getSurcharge['user_commision'];
			}
			

			$surcarge_amount = round(($surcharge/100)*$amount,2);

			if($is_flat)
			{
				$surcarge_amount = $surcharge;
			}
		}
		return $surcarge_amount;
	}

	


	



	public function distribute_pan_commision($dmt_id, $transaction_id, $account_id, $surcharge_amount)
	{

		$domain_account_id = $this->User->get_domain_account();
		$accountData = $this->User->get_account_data($domain_account_id);
		// get account role id
		$get_role_id = $this->db->select('role_id')->get_where('users',array('id'=>$account_id,'account_id'=>$domain_account_id))->row_array();
		$user_role_id = isset($get_role_id['role_id']) ? $get_role_id['role_id'] : 0 ;
		// save system log
	   
        if($user_role_id == 4)
		{
			$md_id = $this->User->get_master_distributor_id($account_id);
			if($md_id)
			{
				// get dmr surcharge
	            $md_surcharge_amount = $this->User->get_pan_charge($md_id);
	            

		    	if($surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $surcharge_amount - $md_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// credit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAN CARD',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN CARD #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 


		               if($accountData['is_tds_amount'] == 1)
			                {

			                	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();

                			$before_balance = $accountBalanceData['wallet_balance'];

			                $tds_amount = $commision*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'PAN CARD #'.$transaction_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet);

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      	  => $commision,
                                'tds_amount'           =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'PAN CARD #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);
                            
			                }

		    		}
		    	}
		    	elseif($surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// debit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAN CARD',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN CARD #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 
		    		}
		    	}
		    }
            
		}
		elseif($user_role_id == 5)
		{
			$distributor_id = $this->User->get_distributor_id($account_id);
			if($distributor_id)
			{
				// get dmr surcharge
	            $dist_surcharge_amount = $this->User->get_pan_charge($distributor_id);
	            
		    	if($surcharge_amount > $dist_surcharge_amount)
		    	{	
		    		
		    		$commision = $surcharge_amount - $dist_surcharge_amount;
		    		
		    	
		    		if($commision)
		    		{
		    			// credit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'PAN CARD',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN CARD #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$distributor_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 


		                if($accountData['is_tds_amount'] == 1)
			                {

			                	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();

                			$before_balance = $accountBalanceData['wallet_balance'];

			                $tds_amount = $commision*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $distributor_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'PAN CARD #'.$transaction_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$distributor_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet);

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $distributor_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      	  => $commision,
                                'tds_amount'           =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'PAN CARD #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);
                            
			                }


		    		}
		    	}
		    	elseif($surcharge_amount < $dist_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// debit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$distributor_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $distributor_id,
							'type' => 'PAN CARD',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $distributor_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN CARD #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$distributor_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 
		    		}
		    	}

	            $md_id = $this->User->get_master_distributor_id($distributor_id);

	            // get dmr surcharge
	            $md_surcharge_amount = $this->User->get_pan_charge($md_id);
	            
	           
	        
		    	if($dist_surcharge_amount > $md_surcharge_amount)
		    	{
		    		$commision = $dist_surcharge_amount - $md_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// credit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance + $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAN CARD',
							'record_id' => $dmt_id,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 1, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN CARD #'.$transaction_id.' Commision Credited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 


		                if($accountData['is_tds_amount'] == 1)
			                {

			                	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();

                			$before_balance = $accountBalanceData['wallet_balance'];

			                $tds_amount = $commision*5/100;

			                $after_balance = $before_balance - $tds_amount;

			                $wallet_data = array(
				                'account_id'          => $domain_account_id,
				                'member_id'           => $md_id,    
				                'before_balance'      => $before_balance,
				                'amount'              => $tds_amount,  
				                'after_balance'       => $after_balance,      
				                'status'              => 1,
				                'type'                => 2,  
				                'wallet_type'         => 1,      
				                'created'             => date('Y-m-d H:i:s'),      
				                'credited_by'         => $account_id,
				                'description'         => 'PAN CARD #'.$transaction_id.'  Commision tds amount deducted'
			                );

			                $this->db->insert('member_wallet',$wallet_data);

			                $user_wallet = array(
			                    'wallet_balance'=>$after_balance,        
			                );    

			                $this->db->where('id',$md_id);
			                $this->db->where('account_id',$domain_account_id);
			                $this->db->update('users',$user_wallet);

			                //save tds data
			                 $wallet_data = array(
                                'account_id'          => $domain_account_id,
                                'member_id'           => $md_id,  
                                //'record_id'            =>$recordID,
                                'com_amount'      	  => $commision,
                                'tds_amount'           =>$tds_amount, 
                                'status'              => 1,
                                'type'                => 2,
                                'created'             => date('Y-m-d H:i:s'),      
                                'credited_by'         => $account_id,
                                'description'         => 'PAN CARD #'.$transaction_id.'  Commision tds amount deducted'
                            );

                            $this->db->insert('tds_report',$wallet_data);
                            
			                }
			                


		    		}
		    	}
		    	elseif($dist_surcharge_amount < $md_surcharge_amount)
		    	{
		    		$commision = $md_surcharge_amount - $dist_surcharge_amount;
		    		
		    		if($commision)
		    		{
		    			// debit wallet
		            	$accountBalanceData = $this->db->get_where('users',array('id'=>$md_id,'account_id'=>$domain_account_id))->row_array();
						$before_balance = $accountBalanceData['wallet_balance'];
						$after_balance = $before_balance - $commision;

		            	$commisionData = array(
							'account_id' => $domain_account_id,
							'member_id' => $md_id,
							'type' => 'PAN CARD',
							'record_id' => $dmt_id,
							'is_surcharge' => 1,
							'commision_amount' => $commision,
							'is_downline' => 1,
							'downline_id' => $account_id,
							'before_balance' => $before_balance,
							'after_balance' => $after_balance,
							'status' => 1,
							'created' => date('Y-m-d H:i:s')
						);
						$this->db->insert('user_commision',$commisionData);

						$wallet_data = array(
			                'account_id'          => $domain_account_id,
			                'member_id'           => $md_id,    
			                'before_balance'      => $before_balance,
			                'amount'              => $commision,  
			                'after_balance'       => $after_balance,      
			                'status'              => 1,
			                'type'                => 2, 
			                'wallet_type'		  => 1,       
			                'created'             => date('Y-m-d H:i:s'),      
			                'credited_by'         => $account_id,
			                'description'         => 'PAN CARD #'.$transaction_id.' Charge Debited.'
		                );

		                $this->db->insert('member_wallet',$wallet_data);

		                $user_wallet = array(
		                    'wallet_balance'=>$after_balance,        
		                );    

		                $this->db->where('id',$md_id);
		                $this->db->where('account_id',$domain_account_id);
		                $this->db->update('users',$user_wallet); 
		    		}
		    	}
		    }
		}
	}

	public function getTotalUnreadTicket()
	{
		$domain_account_id = $this->User->get_domain_account();
		$totalTicket = $this->db->get_where('ticket',array('is_read'=>0,'account_id'=>$domain_account_id))->num_rows();
		return $totalTicket;
	}
	
	//token encrytion and decryption

	public function generateAppToken($action, $string)
	 {
	    $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = '4sf84s5f84erf5sf854';
        $secret_iv = '6541254789564785';
        // hash
        $key = hash('sha256', $secret_key);
    
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
	}


	public function checkUserDecryptToken($decryptToken = "",$userID = "",$password="",$Deviceid = ""){

		$explodeToken = explode('|',$decryptToken);
		$tokenUserID = isset($explodeToken[0]) ? $explodeToken[0] : '';
		$tokenPwd = isset($explodeToken[1]) ? $explodeToken[1] : '';
		$tokenIP = isset($explodeToken[2]) ? $explodeToken[2] : '';
		if($tokenUserID && $tokenPwd && $tokenIP)
		{
			$chk_token_user = $this->db->get_where('users',array('id'=>$tokenUserID,'password'=>$tokenPwd,'is_active'=>1))->num_rows();

			$user_ip_address = $this->User->get_user_ip();

			if($chk_token_user && $tokenUserID == $userID)
			{

				$chk_user = $this->db->get_where('users',array('id'=>$userID,'password'=>$password,'device_id'=>$Deviceid,'is_active'=>1))->row_array();

				if($chk_user){

					$response = array(
						'status' => 1,
						'message' => 'Success',
						'is_login'=>1	
					);
				}else{

					$response = array(
						'status' => 0,
						'message' => 'Session out.Please Login Again.',
						'is_login'=>0	
					);
				}
			}
			else
			{
				$response = array(
					'status' => 0,
					'message' => 'Session out.Please Login Again.',
					'is_login'=>0	
				);
			}
		}
		else
		{
			$response = array(
				'status' => 0,
				'message' => 'Session out.Please Login Again.',
				'is_login'=>0	
			);
		}

		return $response;
	}


	public function getAepsTitle()
	{
		$domain_account_id = $this->User->get_domain_account();

		if($domain_account_id == 2)
		{
			$title = 'NSDL BANK';
		}
		else
		{
			$title = 'New';
		}
		return $title;
	}



}


/* end of file: user.php */
/* Location: ./application/models/admin/user.php */