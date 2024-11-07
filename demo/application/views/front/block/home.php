<?php
if($slider){
?>
<div id="bootstrap-touch-slider" class="carousel bs-slider fade  control-round indicators-line" data-ride="carousel" data-pause="hover" data-interval="false">
        <div class="carousel-inner" role="listbox">
            <?php
            $i = 0;
            foreach($slider as $list){
            ?>
            <div class="item <?php if($i == 0){ ?>active <?php } ?>">
                <img src="{site_url}<?php echo $list['image']; ?>" alt="Recharge api provider" class="slide-image" />
                <div class="bs-slider-overlay"></div>
            </div>    
            <?php $i++;} ?>    
            
            </div>
        
        <a class="left carousel-control" href="#bootstrap-touch-slider" role="button" data-slide="prev"> <span class="fa fa-angle-left" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a> <a class="right carousel-control" href="#bootstrap-touch-slider" role="button" data-slide="next"> <span class="fa fa-angle-right" aria-hidden="true"></span> <span class="sr-only">Next</span> </a>
    </div>
    </div>
  <?php } ?>
    <!-- End  bootstrap touch slider -->

  
</div>

  


    <div class="who-main wow  fadeIn animated" style="visibility: visible; animation-name: fadeIn;">
        <div class="container">
           
            <div class="row">
             <div class="col-lg-4 col-md-4">
                <div class="ab_left_slide">
                 <img src="{site_url}skin/front/images/api.jpg" class="img-responsive">     
                </div>
                </div>

               <div class="col-lg-8 col-md-8">
                    <div class="why-box omega_Ab-box">
                          <h2>Welcome to <?php echo $accountData['title']; ?></h2>
                    <p>
                        <?php echo $accountData['title']; ?> is a  api provider solution offers his api user the facility of Online Recharge Api, Money Transfer Api, Bus Booking Api, Flight Booking Api , Cab Booking Api And Hotel Booking Api. <br>
                    </p>
                        <p class="about-p">
                            <?php echo $accountData['title']; ?> api user will get the Commission/Profit/Share on the every Recharge / Money transfer / Bus Booking / Flight Booking / Hotel Booking.
                            We have a special Recharge / Money transfer / Bus Booking / Flight Booking / Hotel Booking option for the API user where they can do Recharge Mobile, DTh, Data card or bill payments of their customers for all Postpaid/Prepaid Mobile, DTH, Datacard, Bill Payment Demontly , Bus Booking , Flight Booking ,Cab Booking and Hotel Booking  For All Major Operators and Services . <br>
                            <?php echo $accountData['title']; ?> API  portal providing Business to Business services. Join us to earn additional benefits. We offer attractive margins for all prepaid/postpaid operator including Airtel, Vodafone, Videocon, Aircel, Tata Docomo, Idea, BSNL, Reliance, MTS and leading DTH operator which includes Dish TV, TATA Sky, Sun Direct, Videocon D2h, Reliance Digital TV, Airtel Digital TV and all other booking services.
                        </p>
                        
                    </div></div>
                </div>


        </div>
    </div>


      

   
    <?php
    if($service){
    ?>
 <!-- Start Services -->
    <div class="services-page-in wow  fadeIn animated" style="visibility: visible; animation-name: fadeIn;">
        <div class="container">
            <div class="row">
              <div class="col-lg-12 col-md-12">
                  <div class="section-title text-center wow  fadeIn animated" data-wow-duration="1.5s" data-wow-delay="0s" style="visibility: visible; animation-duration: 1.5s; animation-delay: 0s; animation-name: fadeIn;">
                    <h2 class="service-heading">Our Services</h2>
                    <p>We offer high security instant Bus Booking API with free API integration.</p>
               </div>
              </div>

              <?php
              foreach($service as $list){
              ?>
               <div class="col-lg-4 col-md-4">
                 <div class="service-item wow  fadeIn" data-wow-duration="1.5s" data-wow-delay=".1s" style="visibility: visible; animation-duration: 1.5s; animation-delay: 0.1s; animation-name: fadeIn;">
                  <div class="services-icon">
                  <img src="{site_url}<?php echo $list['image']; ?>"></div>
                  <div class="services_item_content">
                  <h5><?php echo $list['title']; ?></h5>
                  <p><?php echo $list['description']; ?></p>
                        </div></div>
               </div>
             <?php } ?>
                
               </div></div>
            </div>

          <?php } ?>
    </div>
    <!-- End Services -->
 
<!----help-you---->
<div class="contact-section-front">
 <div class="container">
  <div class="row">
     <div class="col-lg-6 col-md-6">
        <div class="why-box omega_Ab-box">
                    <div class="section-title wow  fadeIn animated" data-wow-duration="1.5s" data-wow-delay="0s" style="visibility: visible; animation-duration: 1.5s; animation-delay: 0s; animation-name: fadeIn;">
                    <h2 class="service-heading">We Provider Best Service</h2>
               </div>
                    <p>
                        <?php echo $accountData['title']; ?> is a  api provider solution offers his api user the facility of Online Recharge Api, Money Transfer Api, Bus Booking Api, Flight Booking Api , Cab Booking Api And Hotel Booking Api. <br>
                    </p>
                  <ul class="services_list">
                   <li>24/7 Hours Support</li>
                   <li>Support on Call</li>
                   <li>Security</li> 
                   <li>Availability</li> 
                  </ul>

                    <div class="we-services_btn">
                     <a href="#">Join Now</a> 
                    </div>
                  </div>
   </div>
   <div class="col-lg-6 col-md-6">
        <form class="shake contact-section" role="form" name="contact-form" data-toggle="validator" ng-app="coduniteprivate" ng-controller="CntrlProductManage">
            <h1 class="form-title-col"> Happy To Help You</h1>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                        <!-- Name -->
                        <div class="form-group label-floating">
                            <input class="form-control" id="name" type="text" name="name" ng-model="Name" placeholder="Name" required data-error="Please enter your name">
                        </div></div>
                        <div class="col-lg-6 col-md-6">
                        <!-- Mobile -->
                        <div class="form-group label-floating">
                            <input class="form-control" id="mobile" type="tel" name="mobile" ng-model="Mobile_Number" placeholder="Mobile" required data-error="Please enter your mobile">
                        </div></div>
                        <div class="col-lg-12 col-md-12">
                        <!-- email -->
                        <div class="form-group label-floating">
                            <input class="form-control" id="email" type="email" name="email" ng-model="Email" placeholder="Enter Your Email" required data-error="Please enter your Email">
                        </div></div>
                        <div class="col-lg-12 col-md-12">
                        <!-- Subject -->
                        <div class="form-group label-floating">

                            <select ng-model="Subject" required="" class="form-control">
                                <option value="" selected>Choose subject</option>
                                <option value="Mobile Recharge API" >Mobile Recharge API</option>
                                <option value="Bus Booking API" >Bus Booking API</option>
                                <option value="Hotal Booking API" >Hotal Booking API</option>
                                <option value="Flight Booking API" >Flight Booking API</option>
                                <option value="Cab Booking API" >Cab Booking API</option>
                                <option value="All API Solution" >All API Solution</option>
                            </select>

                        </div></div>
                        <div class="col-lg-12 col-md-12">
                        <!-- Message -->
                        <div class="form-group label-floating">
                            <textarea class="form-control" rows="3" id="message" name="message" ng-model="massage" required data-error="Write your message" placeholder="Enter Your Message" style="min-height: 120px; padding: 15px 15px; resize: none;"></textarea>
                        </div></div>
                        <div class="col-lg-12 col-md-12">
                        <!-- Form Submit -->
                        <div class="form-submit mt-5">
                            <button class="btn btn-primary pull-left" type="submit" id="form-submit" ng-click="RegistorNow(Name,Email,Mobile_Number,Subject,massage)">
                                <i class="material-icons mdi mdi-message-outline"></i> Send Message
                            </button>
                        </div></div></div>
                    </form>
   </div>  

  

  </div>   
 </div>   
</div>
<!----//help-you---->


<div class="our-person-section">
 <div class="container">
  <div class="row">
    <div class="col-lg-5 col-md-5">
      <div class="person_pik-for_map">
       <img src="{site_url}skin/front/images/service/map.png" class="img-responsive"> 
      </div>
    </div>
   <div class="col-lg-7 col-md-7">
     <div class="person-contents">
      <h2 class="service-heading">Our Presence</h2>
      <p><?php echo $accountData['title']; ?> is a api provider solution offers his api user the facility of Online Recharge Api, Money Transfer Api, Bus Booking Api, Flight Booking Api , Cab Booking Api And Hotel Booking Api.</p>
      <p><?php echo $accountData['title']; ?> API portal providing Business to Business services. Join us to earn additional benefits. We offer attractive margins for all prepaid/postpaid operator including Airtel, Vodafone, Videocon, Aircel, Tata Docomo, Idea, BSNL, Reliance, MTS and leading DTH operator which includes Dish TV, TATA Sky, Sun Direct, Videocon D2h, Reliance Digital TV, Airtel Digital TV and all other booking services.</p> 
     </div>
   </div> 
  </div> 
 </div> 
</div>


<div class="Distributor_section">
 <div class="container">
  <div class="row">
   <div class="col-lg-8 col-md-8">
    <div class="distubutor_services_area">
     <h3>Already in Distributor Business? We have exciting services for you on our Distributor1 application</h3> 
    </div> 
   </div> 

<div class="col-lg-4 col-md-4">
 <div class="distubutor_joinus">
  <a href="#"> Create Account</a>
  <a href="#"> View More</a>
 </div> 
</div>

  </div> 
 </div> 
</div>


<?php
if($testimonial){
?>
<div class="testimonials-section">
 <div class="container">
  <div class="row">
    <div class="col-lg-12 col-md-12">
                  <div class="section-title text-center wow  fadeIn animated" data-wow-duration="1.5s" data-wow-delay="0s" style="visibility: visible; animation-duration: 1.5s; animation-delay: 0s; animation-name: fadeIn;">
                    <h2 class="service-heading">Testimonial</h2>
               </div>
              </div>
   <div class="col-lg-12 col-md-12">
   <div id="testimonial-slider" class="owl-carousel owl-theme">
     
     <?php 
     foreach($testimonial as $list){
     ?> 
     <div class="owl-item">
       <div class="testimonial-slider-box">
        <div class="testimonial-avtar">
         <img src="{site_url}<?php echo $list['image']; ?>"> 
        </div> 
         <h3><?php echo $list['name']; ?></h3>
        <p><?php echo $list['description']; ?></p>   
        
       </div>
     </div>
    <?php } ?>
     
   </div>  
   </div> 
  </div> 
 </div> 
</div>

<?php } ?>
   
    <!-- Start Main footer -->