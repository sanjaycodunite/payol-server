<?php
if(!defined('BASEPATH'))
    exit('No direct scrip access allowed');

/*
 * login Register controller for Frontend
 * 
 * this controller user for login, register, logout, forgot password, reset password
 * @author trilok
 */

class Test extends CI_Controller{

    public function __construct() {
        parent::__construct();
        
    }
	
	public function index(){
		
		echo 'hook called';
        
    }


    
}


/* End of file login.php */
/* Location: ./application/controllers/login.php */