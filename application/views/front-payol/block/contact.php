<?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
      
    ?>
    
 <!-- CONTENT START -->
        <div class="page-content">
 
      <!-- SLIDER START --> 
      <div class="inner_Banner_area p-t50 p-b50">
       <div class="container">
        <div class="row">
         <div class="col-lg-12 col-md-12">  
          <div class="inner_banner_content">
           <h3>Contact Us</h3>
          </div>  
        </div></div>  
       </div> 
      </div>
      <!-- SLIDER END -->

      <!-- about Area Section -->
      <div class="services_category_Section p-t90 p-b90">
        <div class="container">
          <div class="row">
          <!-- TAB SECTION START -->  
          <div class="col-lg-7 col-md-6"> 
            <div class="Contact_us_form">
                     
                       <!--Contact Form Start-->  
            <form class="contact-form" method="post">
              <div class="row">
                                <!-- COLUMNS 1 -->
                <div class="col-md-6">
                  <div class="form-group aon-form-label">
                    <label>Full Name</label>
                    <input type="text" name="fullname" placeholder="Your Name" class="form-control gradi-line-1" required="">
                  </div>
                </div>
                <!-- COLUMNS 2 -->
                <div class="col-md-6">
                  <div class="form-group aon-form-label">
                    <label>Your Email</label>
                    <input type="text" name="phone" placeholder="Your Mail" class="form-control gradi-line-1">
                  </div>
                </div>
                <!-- COLUMNS 3 -->
                
                <!-- COLUMNS 4 -->
                <div class="col-md-12">
                  <div class="form-group aon-form-label">
                    <label>Phone Number</label>
                    <input type="text" name="subject" placeholder="+91-" class="form-control gradi-line-1" required="">
                  </div>
                </div>
                <!-- COLUMNS 5 -->
                <div class="col-md-12">
                  <div class="form-group aon-form-label">
                    <label>Message</label>
                    <textarea name="message" placeholder="Message" class="form-control gradi-line-1" rows="4" required=""></textarea>
                  </div>
                </div>                               

              </div>
              <div class="sf-contact-submit-btn text-center">
                <button class="site-button btn-animate-one">Submit Now </button>
              </div>
            </form>
            <!--Contact Form End-->
                                </div>
                        </div>
         <!-- TAB SECTION END -->
         <div class="col-lg-5 col-md-6">
         <div class="contact_information">
          <h3>Contact Info</h3> 
          <ul class="contact-info">
           <li><div class="icon-box"><i class="flaticon-093-phone-call"></i></div><h4>Phone</h4>+91 <?php echo $contactDetail['mobile'] ?></li>
                   <li><div class="icon-box"><i class="flaticon-095-mail"></i></div><h4>E-mail</h4><?php echo $accountData['email'] ?>
                   </li>
                   <li><div class="icon-box"><i class="flaticon-015-location"></i></div><h4>Address</h4>NEAR HEAD POST OFFICE,Hazaribagh,Hazaribagh,Hazaribag-825301,Jharkhand</li>
                  </ul>
         </div> 
         </div>

        </div>
        </div>
      </div>
      <!-- about Area Section End -->

      
