<?php 
class Website extends CI_Controller {    
    
    
    public function __construct() 
    {
        parent::__construct();
       	$this->User->checkEmployePermission();
        $this->lang->load('employe/website', 'english');
        
    }

	
	// add member
	public function contact()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $contactData = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addContact',
            'contactData'  => $contactData,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveContact()
	{
		$post = $this->security->xss_clean($this->input->post());
		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $data = array(

          'email'    => $post['email'],
          'mobile'   => $post['mobile'],
          'facebook' => $post['facebook'],
          'twitter'  => $post['twitter'],
          'linkedin' => $post['linkedin'],
          'instagram'=> $post['instagram'],
          'support_working_time' => $post['support_working_time'],
          'address'  => $post['address']	
        );	
		
		$chk_data = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->num_rows();
		if($chk_data){

		 $this->db->where('account_id',$account_id);
		 $status = $this->db->update('website_contact_detail',$data);	

		}
		else{

		 $data['account_id'] = $account_id;

		 $status = $this->db->insert('website_contact_detail',$data);	

		}
			
		if($status == true)
		{
			$this->Az->redirect('employe/website/contact', 'system_message_error',lang('SAVE_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('employe/website/contact', 'system_message_error',lang('DB_ERROR'));
		}
		
	}



	// add member
	public function account()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $accountData = $this->db->get_where('website_account_detail',array('account_id'=>$account_id))->row_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addAccount',
            'accountData'  => $accountData,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveAccount()
	{
		$post = $this->security->xss_clean($this->input->post());
		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $data = array(

          'bank_name'   => $post['bank_name'],
          'branch' => $post['branch'],
          'account_holder_name'  => $post['account_holder_name'],
          'account_no' => $post['account_no'],
          'ifsc'=> $post['ifsc'],
          'phonepe'=> $post['phonepe'],
          'google_pay'=> $post['google_pay']
        );	
		
		$chk_data = $this->db->get_where('website_account_detail',array('account_id'=>$account_id))->num_rows();
		if($chk_data){

		 $this->db->where('account_id',$account_id);
		 $status = $this->db->update('website_account_detail',$data);	

		}
		else{

		 $data['account_id'] = $account_id;

		 $status = $this->db->insert('website_account_detail',$data);	

		}
			
		if($status == true)
		{
			$this->Az->redirect('employe/website/account', 'system_message_error',lang('SAVE_SUCCESS'));
		}
		else
		{
			$this->Az->redirect('employe/website/account', 'system_message_error',lang('DB_ERROR'));
		}
		
	}


	public function privacy()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $contactData = $this->db->get_where('page_content',array('account_id'=>$account_id,'page_id'=>1))->row_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/privacy',
            'contactData'  => $contactData,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function savePrivacy()
	{
		$post = $this->security->xss_clean($this->input->post());
		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        
		
		$chk_data = $this->db->get_where('page_content',array('account_id'=>$account_id,'page_id'=>1))->num_rows();
		if($chk_data){

		 $this->db->where('account_id',$account_id);
		 $this->db->where('page_id',1);
		 $status = $this->db->update('page_content',array('description'=>$post['description']));	

		}
		else{

		 $data = array(
		  'account_id' => $account_id,
		  'page_id'    => 1,
          'description'   => $post['description'],
         );	

		 $status = $this->db->insert('page_content',$data);	

		}
		
		$this->Az->redirect('employe/website/privacy', 'system_message_error',lang('SAVE_SUCCESS'));
		
		
	}

	//terms and condition


	public function terms()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $contactData = $this->db->get_where('page_content',array('account_id'=>$account_id,'page_id'=>2))->row_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/terms',
            'contactData'  => $contactData,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveTerms()
	{
		$post = $this->security->xss_clean($this->input->post());
		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        
		
		$chk_data = $this->db->get_where('page_content',array('account_id'=>$account_id,'page_id'=>2))->num_rows();
		if($chk_data){

		 $this->db->where('account_id',$account_id);
		 $this->db->where('page_id',2);
		 $status = $this->db->update('page_content',array('description'=>$post['description']));	

		}
		else{

		 $data = array(
		  'account_id' => $account_id,
		  'page_id'    => 2,
          'description'   => $post['description'],
         );	

		 $status = $this->db->insert('page_content',$data);	

		}
		
		$this->Az->redirect('employe/website/terms', 'system_message_error',lang('SAVE_SUCCESS'));
		
		
	}

	
	public function service()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $serviceList = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array();	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/serviceList',
            'serviceList'   => $serviceList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }



    // add member
	public function addService()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);


		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addService',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveService()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');

		if(!isset($_FILES['image']['name']) || !$_FILES['image']['name'])
            $this->form_validation->set_rules('image', 'Image', 'required|xss_clean');
		$this->form_validation->set_rules('title', 'Title', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Description ', 'required');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->addService();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        
			
			$image = '';
            if ($_FILES['image']['name'] != '') {
                //generate logo name randomly
                $fileName = rand(1111, 999999999);
                $config['upload_path'] = './media/service_image/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = $fileName;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('image');
                $uploadError = $this->upload->display_errors();
                if ($uploadError) {
                    $this->Az->redirect('employe/website/addService', 'system_message_error', $uploadError);
                } else {
                   
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $image = substr($config['upload_path'] . $fileData['file_name'], 2);
                }
            }	


			$data = array(
			 'account_id' => $account_id,
			 'image'      => $image,
			 'title'      => $post['title'],
			 'description'=> $post['description']  	
			);
			
			$status = $this->db->insert('website_service',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/website/addService', 'system_message_error',lang('SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/addService', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}

	// edit employe
	public function editService($id)
    {    

    	$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$serviceData = $this->db->get_where('website_service',array('account_id'=>$account_id,'id'=>$id))->row_array();

		$siteUrl = site_url();
    	$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/editService',
            'manager_description' => lang('SITE_NAME'),
			'serviceData'=>$serviceData,
			'id'=>$id,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    //update member
	public function updateService()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$id = $post['id'];
		$account_id = $this->User->get_domain_account();
    	// check member
		$chkService = $this->db->get_where('website_service',array('account_id'=>$account_id,'id'=>$id))->num_rows();
		if(!$chkService)
		{
			$this->Az->redirect('employe/website/serviceList', 'system_message_error',lang('DB_ERROR'));
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'required');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->editService($post['id']);
		}
		else
		{	
			
			$image = '';
            if ($_FILES['image']['name'] != '') {
                //generate logo name randomly
                $fileName = rand(1111, 999999999);
                $config['upload_path'] = './media/service_image/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = $fileName;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('image');
                $uploadError = $this->upload->display_errors();
                if ($uploadError) {
                    $this->Az->redirect('employe/website/editService/'.$post['id'], 'system_message_error', $uploadError);
                } else {
                   
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $image = substr($config['upload_path'] . $fileData['file_name'], 2);
                }
            }

			$data = array(

			 'title' => $post['title'],
			 'description'=> $post['description'],
			);

			if($image != ''){

			$data['image'] = $image;	 

			}

			$this->db->where('account_id',$account_id);
			$this->db->where('id',$id);
			$status = $this->db->update('website_service',$data);
			
			if($status == true)
			{
				$this->Az->redirect('employe/website/service', 'system_message_error',lang('UPDATE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/service', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}
	
	
	//delete member
	public function deleteService($id)
	{	
		$account_id = $this->User->get_domain_account();
		
		$this->db->where('account_id',$account_id);
		$this->db->where('id',$id);
		$this->db->delete('website_service');
		
		$this->Az->redirect('employe/website/service', 'system_message_error',lang('DELETE_SUCCESS'));
	}
	
	

	public function slider()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $sliderList = $this->db->get_where('website_slider',array('account_id'=>$account_id))->result_array();	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/sliderList',
            'sliderList'   => $sliderList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

	
	public function addSlider()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);


		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addSlider',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveSlider()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
			
		if(!isset($_FILES['image']['name']) || !$_FILES['image']['name']){
         
            $this->Az->redirect('employe/website/addSlider', 'system_message_error',lang('IMAGE_ERROR'));
		
		}	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        
			
			$image = '';
            if ($_FILES['image']['name'] != '') {
                //generate logo name randomly
                $fileName = rand(1111, 999999999);
                $config['upload_path'] = './media/service_image/';
                $config['allowed_types'] = 'jpg|png|jpeg|JPG|PNG|JPEG';
                $config['file_name'] = $fileName;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('image');
                $uploadError = $this->upload->display_errors();
                if ($uploadError) {
                    $this->Az->redirect('employe/website/slider', 'system_message_error', $uploadError);
                } else {
                   
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $image = substr($config['upload_path'] . $fileData['file_name'], 2);
                }
            }	


			$data = array(
			 'account_id' => $account_id,
			 'image'      => $image,
			);
			
			$status = $this->db->insert('website_slider',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/website/slider', 'system_message_error',lang('SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/slider', 'system_message_error',lang('DB_ERROR'));
			}
			
		
	}


	//delete member
	public function deleteSlider($id)
	{	
		$account_id = $this->User->get_domain_account();
		
		$this->db->where('account_id',$account_id);
		$this->db->where('id',$id);
		$this->db->delete('website_slider');
		
		$this->Az->redirect('employe/website/slider', 'system_message_error',lang('DELETE_SUCCESS'));
	}


	public function testimonial()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $testimonialList = $this->db->get_where('website_testimonial',array('account_id'=>$account_id))->result_array();	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/testimonialList',
            'testimonialList'   => $testimonialList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

	
	public function addTestimonial()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);


		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addTestimonial',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveTestimonial()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');

		if(!isset($_FILES['image']['name']) || !$_FILES['image']['name'])
            $this->form_validation->set_rules('image', 'Image', 'required|xss_clean');
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Description ', 'required');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->addService();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        
			
			$image = '';
            if ($_FILES['image']['name'] != '') {
                //generate logo name randomly
                $fileName = rand(1111, 999999999);
                $config['upload_path'] = './media/service_image/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = $fileName;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('image');
                $uploadError = $this->upload->display_errors();
                if ($uploadError) {
                    $this->Az->redirect('admin/website/addTestimonial', 'system_message_error', $uploadError);
                } else {
                   
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $image = substr($config['upload_path'] . $fileData['file_name'], 2);
                }
            }	


			$data = array(
			 'account_id' => $account_id,
			 'image'      => $image,
			 'name'      => $post['name'],
			 'description'=> $post['description']  	
			);
			
			$status = $this->db->insert('website_testimonial',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/website/testimonial', 'system_message_error',lang('SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/testimonial', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}


	//delete member
	public function deleteTestimonial($id)
	{	
		$account_id = $this->User->get_domain_account();
		
		$this->db->where('account_id',$account_id);
		$this->db->where('id',$id);
		$this->db->delete('website_testimonial');
		
		$this->Az->redirect('employe/website/testimonial', 'system_message_error',lang('DELETE_SUCCESS'));
	}



	public function news()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $newsList = $this->db->get_where('website_news',array('account_id'=>$account_id))->result_array();	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/newsList',
            'newsList'   => $newsList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }



    // add member
	public function addNews()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);


		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addNews',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveNews()
	{
		//check for foem validation
		$post = $this->input->post();
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('news', 'News ', 'required');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->addNews();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        
			
			$data = array(
			 'account_id' => $account_id,
			 'news'=> $post['news']  	
			);
			
			$status = $this->db->insert('website_news',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/website/news', 'system_message_error',lang('SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/news', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}

	// edit employe
	public function editNews($id)
    {    

    	$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$newsData = $this->db->get_where('website_news',array('account_id'=>$account_id,'id'=>$id))->row_array();

		$siteUrl = site_url();
    	$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/editNews',
            'manager_description' => lang('SITE_NAME'),
			'newsData'=>$newsData,
			'id'=>$id,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    //update member
	public function updateNews()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$id = $post['id'];
		$account_id = $this->User->get_domain_account();
    	$this->load->library('form_validation');
		
		$this->form_validation->set_rules('news', 'News', 'required');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->editNews($post['id']);
		}
		else
		{	
			
			
			$data = array(

			 'news'=> $post['news'],
			);

			
			$this->db->where('account_id',$account_id);
			$this->db->where('id',$id);
			$status = $this->db->update('website_news',$data);
			
			if($status == true)
			{
				$this->Az->redirect('employe/website/news', 'system_message_error',lang('UPDATE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/news', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}
	
	
	//delete member
	public function deleteNews($id)
	{	
		$account_id = $this->User->get_domain_account();
		
		$this->db->where('account_id',$account_id);
		$this->db->where('id',$id);
		$this->db->delete('website_news');
		
		$this->Az->redirect('employe/website/news', 'system_message_error',lang('DELETE_SUCCESS'));
	}

	//Blog


	public function blogList()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $blogList = $this->db->get_where('website_blog',array('account_id'=>$account_id))->result_array();	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/blogList',
            'blogList'   => $blogList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }



    // add Blog
	public function addBlog()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);


		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addBlog',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save Blog
	public function saveBlog()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');

		if(!isset($_FILES['image']['name']) || !$_FILES['image']['name'])
            $this->form_validation->set_rules('image', 'Image', 'required|xss_clean');
		$this->form_validation->set_rules('title', 'Title', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Description ', 'required');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->addBlog();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        
			
			$image = '';
            if ($_FILES['image']['name'] != '') {
                //generate logo name randomly
                $fileName = rand(1111, 999999999);
                $config['upload_path'] = './media/blog_image/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = $fileName;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('image');
                $uploadError = $this->upload->display_errors();
                if ($uploadError) {
                    $this->Az->redirect('employe/website/addBlog', 'system_message_error', $uploadError);
                } else {
                   
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $image = substr($config['upload_path'] . $fileData['file_name'], 2);
                }
            }	


			$data = array(
			 'account_id' => $account_id,
			 'image'      => $image,
			 'title'      => $post['title'],
			 'description'=> $post['description'],
			 'created' =>date('Y-m-d H:i:s'), 
			);
			
			$status = $this->db->insert('website_blog',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/website/blogList', 'system_message_error',lang('BLOG_SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/addBlog', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}

	// edit employe
	public function editBlog($id)
    {    

    	$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$blogData = $this->db->get_where('website_blog',array('account_id'=>$account_id,'id'=>$id))->row_array();

		$siteUrl = site_url();
    	$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/editBlog',
            'manager_description' => lang('SITE_NAME'),
			'blogData'=>$blogData,
			'id'=>$id,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    //update Blog
	public function updateBlog()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$id = $post['id'];
		$account_id = $this->User->get_domain_account();
    	// check member
		$chkBlog = $this->db->get_where('website_blog',array('account_id'=>$account_id,'id'=>$id))->num_rows();
		if(!$chkBlog)
		{
			$this->Az->redirect('employe/website/blogList', 'system_message_error',lang('DB_ERROR'));
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'required');
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->editBlog($post['id']);
		}
		else
		{	
			
			$image = '';
            if ($_FILES['image']['name'] != '') {
                //generate logo name randomly
                $fileName = rand(1111, 999999999);
                $config['upload_path'] = './media/blog_image/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = $fileName;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('image');
                $uploadError = $this->upload->display_errors();
                if ($uploadError) {
                    $this->Az->redirect('admin/website/editBlog/'.$post['id'], 'system_message_error', $uploadError);
                } else {
                   
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $image = substr($config['upload_path'] . $fileData['file_name'], 2);
                }
            }

			$data = array(

			 'title' => $post['title'],
			 'description'=> $post['description'],
			 'updated' => date('Y-m-d H:i:s'), 
			);

			if($image != ''){

			$data['image'] = $image;	 

			}

			$this->db->where('account_id',$account_id);
			$this->db->where('id',$id);
			$status = $this->db->update('website_blog',$data);
			
			if($status == true)
			{
				$this->Az->redirect('employe/website/blogList', 'system_message_error',lang('BLOG_UPDATE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/editBlog', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}
	
	
	//delete member
	public function deleteBlog($id)
	{	
		$account_id = $this->User->get_domain_account();
		
		$this->db->where('account_id',$account_id);
		$this->db->where('id',$id);
		$this->db->delete('website_blog');
		
		$this->Az->redirect('employe/website/blogList', 'system_message_error',lang('BLOG_DELETE_SUCCESS'));
	}


	//Feature Section

	public function featureList()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $featureList = $this->db->get_where('website_feature',array('account_id'=>$account_id))->result_array();	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/featureList',
            'featureList'   => $featureList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }



    // add Feature
	public function addFeature()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);


		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addFeature',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save Feature
	public function saveFeature()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');

		if(!isset($_FILES['image']['name']) || !$_FILES['image']['name'])
            $this->form_validation->set_rules('image', 'Image', 'required|xss_clean');
		$this->form_validation->set_rules('title', 'Title', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->addFeature();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        
			
			$image = '';
            if ($_FILES['image']['name'] != '') {
                //generate logo name randomly
                $fileName = rand(1111, 999999999);
                $config['upload_path'] = './media/service_image/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = $fileName;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('image');
                $uploadError = $this->upload->display_errors();
                if ($uploadError) {
                    $this->Az->redirect('employe/website/addFeature', 'system_message_error', $uploadError);
                } else {
                   
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $image = substr($config['upload_path'] . $fileData['file_name'], 2);
                }
            }	


			$data = array(
			 'account_id' => $account_id,
			 'image'      => $image,
			 'title'      => $post['title'],			
			 'created' =>date('Y-m-d H:i:s'), 
			);
			
			$status = $this->db->insert('website_feature',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/website/featureList', 'system_message_error',lang('FEATURE_SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/addFeature', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}

	// edit Feature
	public function editFeature($id)
    {    

    	$account_id = $this->User->get_domain_account();
    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		
		$featureData = $this->db->get_where('website_feature',array('account_id'=>$account_id,'id'=>$id))->row_array();

		$siteUrl = site_url();
    	$id=$id;
		$data = array(
			'loggedUser' => $loggedUser,
            'site_url' => $siteUrl,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/editFeature',
            'manager_description' => lang('SITE_NAME'),
			'featureData'=>$featureData,
			'id'=>$id,
			'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning()
        );
        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    //update Feature
	public function updateFeature()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$id = $post['id'];
		$account_id = $this->User->get_domain_account();
    	// check member
		$chkFeature = $this->db->get_where('website_feature',array('account_id'=>$account_id,'id'=>$id))->num_rows();
		if(!$chkFeature)
		{
			$this->Az->redirect('employe/website/featureList', 'system_message_error',lang('DB_ERROR'));
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'required|xss_clean');
        
        
		if ($this->form_validation->run() == FALSE) {
			
			$this->editFeature($post['id']);
		}
		else
		{	
			
			$image = '';
            if ($_FILES['image']['name'] != '') {
                //generate logo name randomly
                $fileName = rand(1111, 999999999);
                $config['upload_path'] = './media/service_image/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = $fileName;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('image');
                $uploadError = $this->upload->display_errors();
                if ($uploadError) {
                    $this->Az->redirect('employe/website/editFeature/'.$post['id'], 'system_message_error', $uploadError);
                } else {
                   
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $image = substr($config['upload_path'] . $fileData['file_name'], 2);
                }
            }

			$data = array(

			 'title' => $post['title'],			 
			 'updated' => date('Y-m-d H:i:s'), 
			);

			if($image != ''){

			$data['image'] = $image;	 

			}

			$this->db->where('account_id',$account_id);
			$this->db->where('id',$id);
			$status = $this->db->update('website_feature',$data);
			
			if($status == true)
			{
				$this->Az->redirect('employe/website/featureList', 'system_message_error',lang('FEATURE_UPDATE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/editFeature', 'system_message_error',lang('DB_ERROR'));
			}
			
		}
	
	}
	
	
	//delete feature
	public function deleteFeature($id)
	{	
		$account_id = $this->User->get_domain_account();
		
		$this->db->where('account_id',$account_id);
		$this->db->where('id',$id);
		$this->db->delete('website_feature');
		
		$this->Az->redirect('employe/website/featureList', 'system_message_error',lang('FEATURE_DELETE_SUCCESS'));
	}

	public function enquiryList()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $enquiryList = $this->db->get_where('website_enquiry',array('account_id'=>$account_id))->result_array();	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/enquiryList',
            'enquiryList'   => $enquiryList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    	public function deleteEnquiry($id)
	{	
		$account_id = $this->User->get_domain_account();
		
		$this->db->where('account_id',$account_id);
		$this->db->where('id',$id);
		$this->db->delete('website_enquiry');
		
		$this->Az->redirect('employe/website/enquiryList', 'system_message_error',lang('ENQUIRY_DELETE_SUCCESS'));
	}


	public function appSlider()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $sliderList = $this->db->get_where('website_slider',array('account_id'=>$account_id,'is_app'=>1))->result_array();	
   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/appSliderList',
            'sliderList'   => $sliderList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

	
	public function addAppSlider()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);


		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addAppSlider',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    // save member
	public function saveAppSlider()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
			
		if(!isset($_FILES['image']['name']) || !$_FILES['image']['name']){
         
            $this->Az->redirect('employe/website/addAppSlider', 'system_message_error',lang('IMAGE_ERROR'));
		
		}	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        
			
			$image = '';
            if ($_FILES['image']['name'] != '') {
                //generate logo name randomly
                $fileName = rand(1111, 999999999);
                $config['upload_path'] = './media/service_image/';
                $config['allowed_types'] = 'jpg|png|gif';
                $config['file_name'] = $fileName;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('image');
                $uploadError = $this->upload->display_errors();
                if ($uploadError) {
                    $this->Az->redirect('employe/website/appSlider', 'system_message_error', $uploadError);
                } else {
                   
                    $fileData = $this->upload->data();
                    //get uploaded file path
                    $image = substr($config['upload_path'] . $fileData['file_name'], 2);
                }
            }	


			$data = array(
			 'account_id' => $account_id,
			 'image'      => $image,
			 'link'		  => $post['link'],
			 'is_app'	  => 1
			);
			
			$status = $this->db->insert('website_slider',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/website/appSlider', 'system_message_error',lang('SAVE_SUCCESS'));
			}
			else
			{
				$this->Az->redirect('employe/website/appSlider', 'system_message_error',lang('DB_ERROR'));
			}
			
		
	}

	public function editAppSlider($sliderID = 0)
    {
    	$account_id = $this->User->get_domain_account();
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
		// get slider data
		$sliderData = $this->db->get_where('website_slider',array('account_id'=>$account_id,'id'=>$sliderID))->row_array();

		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/editAppSlider',
            'sliderData' => $sliderData,
            'sliderID' => $sliderID,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }

    public function updateAppSlider()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$sliderID = $post['sliderID'];
			
		
		$account_id = $this->User->get_domain_account();
    	$accountData = $this->User->get_account_data($account_id);
    
		
		$image = '';
        if ($_FILES['image']['name'] != '') {
            //generate logo name randomly
            $fileName = rand(1111, 999999999);
            $config['upload_path'] = './media/service_image/';
            $config['allowed_types'] = 'jpg|png|gif';
            $config['file_name'] = $fileName;
            $this->load->library('upload');
            $this->upload->initialize($config);
            $this->upload->do_upload('image');
            $uploadError = $this->upload->display_errors();
            if ($uploadError) {
                $this->Az->redirect('employe/website/editAppSlider/'.$sliderID, 'system_message_error', $uploadError);
            } else {
               
                $fileData = $this->upload->data();
                //get uploaded file path
                $image = substr($config['upload_path'] . $fileData['file_name'], 2);
            }
        }	


		$data = array(
		 'link'		  => $post['link']
		);
		if($image)
		{
			$data['image'] = $image;
		}
		
		$this->db->where('id',$sliderID);
		$this->db->where('account_id',$account_id);
		$this->db->update('website_slider',$data);
		
		$this->Az->redirect('employe/website/appSlider', 'system_message_error',lang('SAVE_SUCCESS'));
		
			
		
	}


	//delete member
	public function deleteAppSlider($id)
	{	
		$account_id = $this->User->get_domain_account();

		// get slider data
		$sliderData = $this->db->get_where('website_slider',array('account_id'=>$account_id,'id'=>$id))->row_array();

		$image_url = isset($sliderData['image']) ? $sliderData['image'] : '';
		if($image_url)
		{
			if (file_exists($image_url)) 
			{
			    unlink(str_replace('system/', '', BASEPATH . $image_url));
			}
		}
		
		$this->db->where('account_id',$account_id);
		$this->db->where('id',$id);
		$this->db->delete('website_slider');
		
		$this->Az->redirect('employe/website/appSlider', 'system_message_error',lang('DELETE_SUCCESS'));
	}
	
	

	public function pages()
    {	

    	$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $pageList = $this->db->get_where('front_pages',array('account_id'=>$account_id))->result_array();

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/pages',
            'pageList'  => $pageList,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }


    public function addPage()
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/addPage',
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }


    public function savePage()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');

		$this->form_validation->set_rules('page_title', 'Page Title', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->addPage();
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        	
        	$page_slug = url_title($post['page_title'], 'dash', true);

			$data = array(
			 'account_id'   => $account_id,
			 'page_title'   => $post['page_title'],
			 'page_slug'    => $page_slug,
			 'page_content' => $post['page_content'],
			 'status'       => $post['status'],			
			 'created' =>date('Y-m-d H:i:s'), 
			);
			
			$status = $this->db->insert('front_pages',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/website/pages', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Page added successfully.</div>');
			}
			else
			{
				$this->Az->redirect('employe/website/pages', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
			}
			
		}
	
	}




	public function editPage($page_id = 0)
    {
		$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $pageData = $this->db->get_where('front_pages',array('id'=>$page_id,'account_id'=>$account_id))->row_array();

        if(!$pageData){

        	$this->Az->redirect('employe/website/pages', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
        }

   		$siteUrl = site_url();
        $data = array(
            'site_url' => $siteUrl,
			'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'website/editPage',
            'pageData' => $pageData,
            'page_id' => $page_id,
            'manager_description' => lang('SITE_NAME'),
          	'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning() 
		);

        $this->parser->parse('employe/layout/column-1', $data);
		
    }


    public function updatePage()
	{
		//check for foem validation
		$post = $this->security->xss_clean($this->input->post());
		$this->load->library('form_validation');

		$this->form_validation->set_rules('page_title', 'Page Title', 'required|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
			
			$this->editPage($post['page_id']);
		}
		else
		{	
			$account_id = $this->User->get_domain_account();
        	$accountData = $this->User->get_account_data($account_id);
        	$page_slug = url_title($post['page_title'], 'dash', true);

			$data = array(
			 'page_title'   => $post['page_title'],
			 'page_slug'    => $page_slug,
			 'page_content' => $post['page_content'],
			 'status'       => $post['status'],			
			);
			
			$this->db->where('account_id',$account_id);
			$this->db->where('id',$post['page_id']);	
			$status = $this->db->update('front_pages',$data);
			if($status == true)
			{
				$this->Az->redirect('employe/website/pages', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Page updated successfully.</div>');
			}
			else
			{
				$this->Az->redirect('employe/website/pages', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
			}
			
		}
	
	}


	public function deletePage($page_id = 0){

		$account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $chk_page = $this->db->get_where('front_pages',array('id'=>$page_id,'account_id'=>$account_id))->row_array();

        if(!$chk_page){

        	$this->Az->redirect('employe/website/pages', 'system_message_error','<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Sorry!! something went wrong.</div>');
        }
        else{

        	$this->db->where('id',$page_id);
        	$this->db->where('account_id',$account_id);
        	$this->db->delete('front_pages');

        	$this->Az->redirect('employe/website/pages', 'system_message_error','<div class="alert alert-success alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Page deleted successfully.</div>');

        }

	}

    
	
}