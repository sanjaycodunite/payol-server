<?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
      
    ?>
    


        <!-----main-slider-section---->
        <section class="inner_slide  position-relative">
         <div class="container">
           <div class="row">
            <div class="m-auto col-lg-6 col-md-12">
             <div class="inner_slide_content text-center">
              <h2>Contact Us</h2> 
              <ul>
               <li>Home</li>
               <li>/</li>
               <li><a href="#">Contact Us</a></li> 
              </ul>
             </div> 
            </div> 
           </div>
         </div>
         </section>



 <!-- Start of about section -->
        <section class="dia-about-section">
            <div class="container">
              <div class="row">
                <div class="col-lg-12 col-md-12">
                 <div class="contact_details">
                 <ul>
                 <li><i class="fas fa-mobile-alt"></i>Call Us: <span>(+91) <?php echo $accountData['mobile'] ?></span></li>
                 <li><i class="fas fa-envelope"></i>Mail ID: <span><?php echo $accountData['email'] ?></span></li> 
                 <li><i class="fas fa-map-marker-alt"></i> Address: <span><?php echo $contactDetail['address'] ?></span></li>     
                     </ul>   
                   </div> 
                </div>
              </div>
                <div class="dia-contact-content mt-5">
                    <div class="row">
                      <div class="col-lg-5 col-md-12 wow fadeFromLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                       <div class="contact_colm_img">
                        <img src="{site_url}skin/front-payrise/assets/img/contact_vector.png" class="img-fluid"> 
                       </div> 
                      </div>
                        <div class="col-lg-6 col-md-12 wow fadeFromLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                           <div class="dia-about-text-area">
                           <div class="dia-about-title-text">
                            <div class="dia-section-title text-left text-capitalize dia-headline">
                            <h2>Get in <span>touch</span></h2>
                            </div>
                            <div class="contact_form">
                            <form class="contact_us-form">
                            <div class="row">
                            <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                              <label>Name*</label>
                            <input type="text" name="Name" class="form-control" placeholder="Enter Your Name"> </div> 
                                 </div>  

                            <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                              <label>Email ID*</label>
                            <input type="text" name="Phone" class="form-control" placeholder="Enter Your Email ID"> </div> 
                                 </div>  

                              <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                              <label>Phone</label>
                            <input type="text" name="Phone" class="form-control" placeholder="Enter Your Mobile"> </div> 
                                 </div> 

                                  <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                              <label>Message*</label>
                            <textarea name="message" class="form-control" rows="3" placeholder="Enter type your message... "></textarea> </div> 
                                 </div> 

                                  <div class="col-lg-12 col-md-12">  
                              <div class="form-group">
                               <button class="hover-btn" type="submit" value="Submit"> Submit</button>
                              </div> </div>

                                      </div>   
                                     </form>  
                                   </div>
                                </div>
                            </div>
                        </div>
                      
                    </div>
                </div>
            </div>
        </section>
    <!-- End of About section -->  


      

      


    

