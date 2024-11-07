<div class="contact_section">
  <div class="container">
   <div class="row">
        <div class="col-lg-7 col-md-7">
               <div class="contact_forms">
                <form class="shake contact-section">
                    <h3 class="form-title-col"> Send Your Queries</h3>
                 <div class="row">
                     <div class="col-lg-12 col-md-12">
                        <!-- Subject -->
                        <div class="form-group label-floating">
                          <label>Why are you here ?</label>
                            <select ng-model="Subject" required="" class="form-control" style="display: none;">
                                <option value="" selected="">Select the reason</option>
                                <option value="Marketing">Marketing (For Products & Service Companies)</option>
                                <option value="Partner With Us">Partner With Us</option>
                                <option value="Tech Support">Tech Support</option>
                                <option value="Customer Support">Customer Support</option>
                                <option value="Others">Others</option>
                            </select><div class="nice-select form-control" tabindex="0"><span class="current">Select the reason</span><ul class="list"><li data-value="" class="option selected focus">Select the reason</li>
                                <li data-value="Mobile Recharge API" class="option">Marketing (For Products & Service Companies)</li>
                                <li data-value="Bus Booking API" class="option">Partner With Us</li>
                                <li data-value="Hotal Booking API" class="option">Tech Support</li>
                                <li data-value="Flight Booking API" class="option">Flight Booking API</li>
                                <li data-value="Cab Booking API" class="option">Customer Support</li>
                                <li data-value="All API Solution" class="option">Others</li></ul></div>

                        </div></div>
                <div class="col-lg-6 col-md-6">
                        <!-- Name -->
                        <div class="form-group label-floating has-error">
                             <label>Name</label>
                            <input class="form-control" id="name" type="text" name="name" ng-model="Name" placeholder="Name" required="" data-error="Please enter your name">
                        </div></div>
                        <div class="col-lg-6 col-md-6">
                        <!-- Mobile -->
                        <div class="form-group label-floating">
                            <label>Mobile Number</label>
                            <input class="form-control" id="mobile" type="tel" name="mobile" ng-model="Mobile_Number" placeholder="Mobile" required="" data-error="Please enter your mobile">
                        </div></div>
                        <div class="col-lg-12 col-md-12">
                        <div class="form-group label-floating">
                            <label>Email Id</label>
                            <input class="form-control" id="email" type="email" name="email" ng-model="Email" placeholder="Enter Your Email" required="" data-error="Please enter your Email">
                        </div></div>

                        <div class="col-lg-6 col-md-6">
                        <div class="form-group label-floating">
                            <label>State</label>
                            <input class="form-control" type="text" name="state" placeholder="Enter Your State" required="" data-error="Please enter State">
                        </div></div>

                        <div class="col-lg-6 col-md-6">
                        <div class="form-group label-floating">
                            <label>PinCode</label>
                            <input class="form-control" type="text" name="pincode" placeholder="Enter Your PinCode" required="" data-error="Please enter PinCode">
                        </div></div>
                       
                        <div class="col-lg-12 col-md-12">
                        <!-- Message -->
                        <div class="form-group label-floating">
                            <label>Message</label>
                            <textarea class="form-control" rows="2" name="message" required="" placeholder="Enter Your Message" style="min-height: 80px; padding: 15px 15px; resize: none;"></textarea>
                        </div></div>

                        

                        <div class="col-lg-12 col-md-12">
                        <!-- Form Submit -->
                        <div class="form-submit mt-5">
                            <button class="btn btn-primary pull-left disabled" type="submit" id="form-submit" ng-click="RegistorNow(Name,Email,Mobile_Number,Subject,massage)" style="pointer-events: all; cursor: pointer;">
                                <i class="material-icons mdi mdi-message-outline"></i> Send Message
                            </button>
                        </div></div></div>   
                </form>

               </div> 
              </div> 

             <div class="col-lg-5 col-md-5">
             <div class="contact_information">
              <h3>For Queries and Complaints</h3>   
              <ul>
              <li><span><i class="fa fa-phone"></i></span><a href="#">+91-<?php echo $contactDetail['mobile']; ?></a></li>  
              <li><span><i class="fa fa-envelope"></i></span><a href="#"><?php echo $contactDetail['email']; ?></a></li>  
              <li><span><i class="fa fa-location-arrow"></i></span> <address><?php echo $contactDetail['address']; ?></address></li>
              </ul>
             </div>    
             </div>


   </div> 

               <div class="row">
                <div class="col-lg-12 col-md-12">
               <div class="contact_map">
               <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d227748.99973450298!2d75.65047228361074!3d26.88514167956319!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x396c4adf4c57e281%3A0xce1c63a0cf22e09!2sJaipur%2C%20Rajasthan!5e0!3m2!1sen!2sin!4v1613689568405!5m2!1sen!2sin" width="100%" height="350" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>    
               </div></div>    
               </div>


  </div>
</div>