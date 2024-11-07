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

class Login_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
	
	
	
}


/* end of file: az.php */
/* Location: ./application/models/az.php */