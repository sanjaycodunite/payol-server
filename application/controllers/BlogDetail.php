<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class BlogDetail extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -  
     *      http://example.com/index.php/welcome/index
     *  - or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct() {
        parent::__construct();
        //check admin permissions 
        $this->lang->load('front', 'english');
        
    }
    
    public function index($blogID=0) {
     
          $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

      $blogData=$this->db->get_where('website_blog',array('id'=>$blogID,'account_id'=>$account_id))->row_array();

     
      
        $siteUrl = base_url();
        $data = array(
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'site_url' => $siteUrl,  
            'blogData'=>$blogData,
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getsystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
            'content_block' => 'blog-detail'
        );
        $this->parser->parse('main-front/layout/column-1' , $data);
        
         
    }
    
}
 

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */   
?>