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

class HelpModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getHelpList($start, $length, $search, $order){
        $results = array();
        if (!empty($search['value'])) {
            $this->db->group_start();
            $this->db->like('contact_no', $search['value']);
            $this->db->or_like('email', $search['value']);
            $this->db->or_like('description', $search['value']);
            $this->db->or_like('created_at', $search['value']);
            $this->db->group_end();
        }
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('tbl_help_support', $length, $start);
        if($query->num_rows() > 0){
            foreach ($query->result() as $index => $key) {
                $file = '';
                if(!empty($key->file) && file_exists($key->file)){
                    $file = '<img src="'.base_url($key->file).'" class="img-responsive" style="height: 80px; border-radius: 50px;">';
                }
                $results[] = array(
                    $start + $index + 1,
                    $key->contact_no,
                    $key->email,
                    $key->description,
                    $file,
                    date('Y-m-d H:i:s', strtotime($key->created_at)),
                );
            }
        }
        return $results;
    }

    public function getHelpListCount($search, $order){
        $count = 0;
        if (!empty($search['value'])) {
            $this->db->group_start();
            $this->db->like('contact_no', $search['value']);
            $this->db->or_like('email', $search['value']);
            $this->db->or_like('description', $search['value']);
            $this->db->or_like('created_at', $search['value']);
            $this->db->group_end();
        }
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('tbl_help_support');
        if ($query->num_rows()) {
            $count = $query->num_rows();
        }
        return $count;
    }
}


/* end of file: az.php */
/* Location: ./application/models/az.php */