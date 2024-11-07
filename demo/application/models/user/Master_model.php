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

class Master_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function saveWalletSetting($post)
    {       
        
        $this->db->where('id',1);
        $this->db->update('master_setting',array('min_transfer'=>$post['min_transfer'],'daily_transfer_limit'=>$post['daily_transfer_limit']));

        return true;

    }

    public function saveTransferCommision($post)
    {       
        $data = array(
          'from_1' => isset($post['from_1']) ? $post['from_1'] : 0,
          'to_1' => isset($post['to_1']) ? $post['to_1'] : 0,
          'flat_1' => isset($post['flat_1']) ? $post['flat_1'] : 0,
          'from_2' => isset($post['from_2']) ? $post['from_2'] : 0,
          'to_2' => isset($post['to_2']) ? $post['to_2'] : 0,
          'flat_2' => isset($post['flat_2']) ? $post['flat_2'] : 0,  
          'from_3' => isset($post['from_3']) ? $post['from_3'] : 0,
          'to_3' => isset($post['to_3']) ? $post['to_3'] : 0,
          'flat_3' => isset($post['flat_3']) ? $post['flat_3'] : 0,  
        
        );

        $this->db->where('id',1);
        $this->db->update('master_setting',$data);

        return true;

    }


    public function saveAEPSCommision($post)
    {       
        $data = array(
          'min_state_com' => isset($post['min_state_com']) ? $post['min_state_com'] : 0,
          'aeps_from_1' => isset($post['aeps_from_1']) ? $post['aeps_from_1'] : 0,
          'aeps_to_1' => isset($post['aeps_to_1']) ? $post['aeps_to_1'] : 0,
          'com_type_1' => isset($post['com_type_1']) ? $post['com_type_1'] : 1,
          'aeps_flat_1' => isset($post['aeps_flat_1']) ? $post['aeps_flat_1'] : 0,
          'aeps_percent_1' => isset($post['aeps_percent_1']) ? $post['aeps_percent_1'] : 0,
          'aeps_from_2' => isset($post['aeps_from_2']) ? $post['aeps_from_2'] : 0,
          'aeps_to_2' => isset($post['aeps_to_2']) ? $post['aeps_to_2'] : 0,
          'com_type_2' => isset($post['com_type_2']) ? $post['com_type_2'] : 1,
          'aeps_flat_2' => isset($post['aeps_flat_2']) ? $post['aeps_flat_2'] : 0,
          'aeps_percent_2' => isset($post['aeps_percent_2']) ? $post['aeps_percent_2'] : 0,
          'aeps_from_3' => isset($post['aeps_from_3']) ? $post['aeps_from_3'] : 0,
          'aeps_to_3' => isset($post['aeps_to_3']) ? $post['aeps_to_3'] : 0,
          'com_type_3' => isset($post['com_type_3']) ? $post['com_type_3'] : 1,
          'aeps_flat_3' => isset($post['aeps_flat_3']) ? $post['aeps_flat_3'] : 0,
          'aeps_percent_3' => isset($post['aeps_percent_3']) ? $post['aeps_percent_3'] : 0,
        );

        $this->db->where('id',1);
        $this->db->update('master_setting',$data);

        return true;

    }

    public function adminLogout()
    {
      $this->session->unset_userdata(ADMIN_SESSION_ID);
    }


}


/* end of file: az.php */
/* Location: ./application/models/az.php */