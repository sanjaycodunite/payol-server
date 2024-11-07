
      
      <!-- 
      =============================================
        Theme Main Banner Two
      ============================================== 
      -->
      <div class="inner_banner">
        <div class="container">
        <div class="row">
        <div class="m-auto col-lg-6 col-md-6">  
          <div class="inner_slide_content text-center">
              <h2>Contact Us</h2> 
              <ul>
               <li><a href="index.html">Home</a></li>
               <li>/</li>
               <li>Contact Us</li> 
              </ul>
             </div>
          </div> 
          </div> <!-- /.main-wrapper -->
        </div> <!-- /.container -->
      </div> <!-- /#theme-banner-two -->
      
      


    <div class="contact-us-section">
      
    <div class="container">
  <div class="row">
    <div class="col-lg-6 col-md-6">
    <div class="contact_form">
      <form id="enquiry">
     <div class="form-group">
      <i class="fa fa-user"></i>
      <input class="form-control" name="name" type="text" placeholder="Enter Your Name" required>
      </div>
      <div class="form-group">
      <i class="fa fa-phone"></i>
      <input class="form-control" name="mobile" type="text" placeholder="Phone Number" required>
      </div>
       <div class="form-group">
      <i class="fa fa-envelope"></i>
      <input class="form-control" name="email" type="Email" placeholder="Enter Your Email ID" required>
      </div>

       <div class="form-group">
                           <select ng-model="Subject"  class="form-control select_mt" name="service" required>
                                <option value=" " selected="">Choose Service</option>
                                <option value="Mobile Recharge API">Mobile Recharge API</option>
                                <option value="Bus Booking API">Bus Booking API</option>
                                <option value="Hotal Booking API">Hotal Booking API</option>
                                <option value="Flight Booking API">Flight Booking API</option>
                                <option value="Cab Booking API">Cab Booking API</option>
                                <option value="All API Solution">All API Solution</option>
                            </select>
                        </div>

     <div class="form-group">
     <i class="fa fa-comments"></i>
     <textarea class="form-control" name="message" placeholder="Enter Your Message" required></textarea>
     </div>
     <div class="form-group">
     <button class="btn_submit" type="submit">Submit</button>
        </div>
        </form>    
    </div>  
    </div>  
  <div class="col-lg-6 col-md-6">
  <div class="contact_map">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3558.0530009308077!2d75.74194731463591!3d26.901812983132203!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x396db490a0d7eb11%3A0xfc74b5b9dcc2554a!2scodunite!5e0!3m2!1sen!2sin!4v1515630882699" width="100%" height="350" frameborder="0" style="border: 0" allowfullscreen></iframe>
  </div>  
  </div>  


    </div>

      <div class="row contact_address_section">
    <div class="col-lg-4 col-md-4">
    <div class="contact_group_address text-center" data-aos="fade-up">
    <div class="c-icon">
    <i class="fa fa-phone"></i> 
    </div>  
    <h5>+91-<?php echo $contactDetail['mobile']; ?></h5>
    </div>  
    </div>
    
    <div class="col-lg-4 col-md-4">
    <div class="contact_group_address text-center" data-aos="fade-up">
    <div class="c-icon">
    <i class="fa fa-envelope"></i>  
    </div>  
    <h5> <?php echo $contactDetail['email']; ?></h5>
    </div>  
    </div>

     <div class="col-lg-4 col-md-4">
    <div class="contact_group_address text-center" data-aos="fade-up">
    <div class="c-icon">
    <i class="fa fa-map"></i> 
    </div>  
    <h5><?php echo $contactDetail['address']; ?></h5>
    </div>  
    </div>

    </div>  

    </div>  
    </div>
 



