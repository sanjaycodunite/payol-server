<!-- Banner Section Start -->
            <?php
            if($slider){
            ?>
            <div class="main_banner">
              <div class="main-carousel owl-carousel">
                        <?php
                        $i = 0;
                        foreach($slider as $list){
                        ?>  
                        <div class="team-item"> 
                        <img src="{site_url}<?php echo $list['image']; ?>"> 
                        </div>
                        <?php $i++;} ?>  

                      </div>
            </div>
           <?php } ?>
            <!-- Banner Section End -->

             <!-- About Section Start -->
            <div class="rs-about style3 pt-70 pb-70 md-pt-75 md-pb-80">
                <div class="container">
                    <div class="row align-items-center">
                      <div class="col-lg-7 md-pt-40">
                            <div class="rs-animation-image">
                                <div class="pattern-img">
                                   <img src="{site_url}skin/theme-three/assets/images/about/style2/round.png" alt=""> 
                                </div>
                                <div class="middle-img">
                                   <img class="dance3" src="{site_url}skin/theme-three/assets/images/about/style2/about1.png" alt="">
                                </div>
                            </div>
                        </div>

                          <div class="col-lg-5">
                            <div class="sec-title2 mb-30">
                                <h2 class=" title title3 pb-20">
                                    Welcome to <?php echo $accountData['title']; ?>
                                </h2>
                                <div class="desc desc2">
                                   <p>
                        <?php echo $accountData['title']; ?> is a  api provider solution offers his api user the facility of Money Transfer Api, Bus Booking Api, Flight Booking Api , Cab Booking Api And Hotel Booking Api. <br>
                    </p>
                        <p class="about-p">
                            <?php echo $accountData['title']; ?> api user will get the Commission/Profit/Share on the every Money transfer / Bus Booking / Flight Booking / Hotel Booking.
                            We have a special Money transfer / Bus Booking / Flight Booking / Hotel Booking option for the API user where they can do  their customers for all  Bus Booking , Flight Booking ,Cab Booking and Hotel Booking  For All Major Operators and Services . <br>
                            <?php echo $accountData['title']; ?> API  portal providing Business to Business services. Join us to earn additional benefits. We offer attractive margins all other booking services.
                        </p>
                                </div>
                                <div class="btn-part mt-40">
                                    <a class="readon discover more" href="#">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- About Section End -->

            <?php
            if($service){
            ?>
            <!-- Case Study Section Start -->
            <div class="rs-case-study primary-background pt-70 pb-70">
              <div class="container">  
                <div class="row margin-0 align-items-center">
                    <div class="col-lg-12">
                        <div class="case-study mod text-center">
                            <div class="sec-title2 mb-30">
                                <div class="sub-text white-color">Service</div>
                                <h2 class="title testi-title white-color pb-0">
                                 We Provider Best Service
                                </h2>
                                <div class="desc-big"> We offer high security instant Bus Booking API with free API integration.
                                </div>
                            </div>
                        </div>
                    </div>
                     <?php
                     foreach($service as $list){
                                    ?>
                    <div class="col-lg-4 col-md-4">
                    <div class="project_item_service">
                     <div class="project_service_img">
                     <a href="#"><img src="{site_url}<?php echo $list['image']; ?>" alt="images"></a>
                     </div>
                     <div class="project_sevice_content">
                     <h3 class="title"><a href="#"><?php echo $list['title']; ?></a></h3>
                     <p>Morning pay API portal providing Business to Business services. Join us to earn additional benefits. We offer attractive margins all other booking services.</p>
                      </div>
                      </div>    
                    </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
            <!-- Case Study Section Start -->
          <?php } ?>
           
           

            

            <!-- Services Section Start -->
            <div class="rs-services style4 modify1 gray-color pt-70 pb-70 md-pt-75 md-pb-40 sm-pb-70">
                <div class="container">
                    <div class="sec-title2 text-center mb-25">
                        <h2 class="title">Our Feature</h2>
                        <p>Company is a B2B Online Platform</p>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-20">
                            <div class="services-item">
                                <div class="services-icon">
                                    <img src="{site_url}skin/theme-three/assets/images/services/style5/1.png" alt="">
                                </div>
                                <div class="services-content">
                                    <h2 class="title"><a href="#">Fast & Secure Payments</a></h2>
                                    <p class="desc">We denounce with rightous indig nationand dislike men who are so beguiled demoralized
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="services-item">
                                <div class="services-icon">
                                    <img src="{site_url}skin/theme-three/assets/images/services/style5/2.png" alt="">
                                </div>
                                <div class="services-content">
                                    <h2 class="title"><a href="#">User Secure Data</a></h2>
                                    <p class="desc">We denounce with rightous indig nationand dislike men who are so beguiled demoralized
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="services-item">
                                <div class="services-icon">
                                    <img src="{site_url}skin/theme-three/assets/images/services/style5/3.png" alt="">
                                </div>
                                <div class="services-content">
                                    <h2 class="title"><a href="#">Instant Money Transfer</a></h2>
                                    <p class="desc"> We denounce with rightous indig nationand dislike men who are so beguiled demoralized
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="services-item">
                                <div class="services-icon">
                                    <img src="{site_url}skin/theme-three/assets/images/services/style5/4.png" alt="">
                                </div>
                                <div class="services-content">
                                    <h2 class="title"><a href="#">Device & Machine</a></h2>
                                    <p class="desc">We denounce with rightous indig nationand dislike men who are so beguiled demoralized
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="services-item">
                                <div class="services-icon">
                                    <img src="{site_url}skin/theme-three/assets/images/services/style5/5.png" alt="">
                                </div>
                                <div class="services-content">
                                    <h2 class="title"><a href="#">24/7 Support Team</a></h2>
                                    <p class="desc">We denounce with rightous indig nationand dislike men who are so beguiled demoralized
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="services-item">
                                <div class="services-icon">
                                    <img src="{site_url}skin/theme-three/assets/images/services/style5/6.png" alt="">
                                </div>
                                <div class="services-content">
                                    <h2 class="title"><a href="#">Fast Booking Facilites</a></h2>
                                    <p class="desc">We denounce with rightous indig nationand dislike men who are so beguiled demoralized
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Services Section End -->

           

            

           
            <?php
            if($testimonial){
            ?>
            <!-- Testimonial Section Start -->
            <div class="rs-testimonial pt-70 ">
                <div class="container">
                    <div class="testi-effects-layer bg10">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <div class="sec-title mb-60">
                                    <div class="sub-text new">Client's Review</div>
                                    <h2 class="title title4 white-color pb-20">
                                        What do people praise about braintech?
                                    </h2>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <!-- Testimonial Section Start -->
                                <div class="rs-testimonial style4">
                                    <div class="rs-carousel owl-carousel" data-loop="true" data-items="1" data-margin="20" data-autoplay="true" data-hoverpause="true" data-autoplay-timeout="5000" data-smart-speed="800" data-dots="false" data-nav="false" data-nav-speed="false" data-center-mode="false" data-mobile-device="1" data-mobile-device-nav="true" data-mobile-device-dots="false" data-ipad-device="1" data-ipad-device-nav="true" data-ipad-device-dots="false" data-ipad-device2="1" data-ipad-device-nav2="true" data-ipad-device-dots2="false" data-md-device="1" data-md-device-nav="true" data-md-device-dots="false">
                                        
                                        <?php 
                                          foreach($testimonial as $list){
                                        ?> 
                                        <div class="testi-item">
                                            
                                            
                                            <div class="testi-content">
                                                <div class="images-wrap">
                                                    <img src="{site_url}<?php echo $list['image']; ?>" alt="">
                                                </div>
                                                <div class="testi-information">
                                                    <div class="testi-name"><?php echo $list['name']; ?></div>
                                                </div>
                                            </div>


                                            <div class="item-content-basic">
                                                <div class="desc"><img class="quote" src="{site_url}skin/theme-three/assets/images/testimonial/main-home/quote3.png" alt=""><?php echo $list['description']; ?></div>
                                            </div>
                                        </div>
                                        <?php } ?>


                                    </div>
                                </div>
                                <!-- Testimonial Section End -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Testimonial Section End -->
            <?php } ?>


            
            
            <!-- Partner Start -->
         <!--    <div class="rs-partner pt-30 pb-30">
                <div class="container">
                    <div class="rs-carousel owl-carousel" data-loop="true" data-items="5" data-margin="30" data-autoplay="true" data-hoverpause="true" data-autoplay-timeout="5000" data-smart-speed="800" data-dots="false" data-nav="false" data-nav-speed="false" data-center-mode="false" data-mobile-device="2" data-mobile-device-nav="false" data-mobile-device-dots="false" data-ipad-device="3" data-ipad-device-nav="false" data-ipad-device-dots="false" data-ipad-device2="3" data-ipad-device-nav2="false" data-ipad-device-dots2="false" data-md-device="5" data-md-device-nav="false" data-md-device-dots="false">
                        <div class="partner-item">
                            <div class="logo-img">
                                <a href="#">
                                    <img class="hover-logo" src="{site_url}skin/theme-three/assets/images/partner/1.png" alt="">
                                    <img class="main-logo" src="{site_url}skin/theme-three/assets/images/partner/1.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="partner-item">
                            <div class="logo-img">
                                <a href="#">
                                    <img class="hover-logo" src="{site_url}skin/theme-three/assets/images/partner/2.png" alt="">
                                    <img class="main-logo" src="{site_url}skin/theme-three/assets/images/partner/2.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="partner-item">
                            <div class="logo-img">
                                <a href="#">
                                    <img class="hover-logo" src="{site_url}skin/theme-three/assets/images/partner/3.png" alt="">
                                    <img class="main-logo" src="{site_url}skin/theme-three/assets/images/partner/3.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="partner-item">
                            <div class="logo-img">
                                <a href="#">
                                    <img class="hover-logo" src="{site_url}skin/theme-three/assets/images/partner/4.png" alt="">
                                    <img class="main-logo" src="{site_url}skin/theme-three/assets/images/partner/4.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="partner-item">
                            <div class="logo-img">
                                <a href="#">
                                    <img class="hover-logo" src="{site_url}skin/theme-three/assets/images/partner/5.png" alt="">
                                    <img class="main-logo" src="{site_url}skin/theme-three/assets/images/partner/5.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="partner-item">
                            <div class="logo-img">
                                <a href="#">
                                    <img class="hover-logo" src="{site_url}skin/theme-three/assets/images/partner/6.png" alt="">
                                    <img class="main-logo" src="{site_url}skin/theme-three/assets/images/partner/6.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="partner-item">
                            <div class="logo-img">
                                <a href="#">
                                    <img class="hover-logo" src="{site_url}skin/theme-three/assets/images/partner/7.png" alt="">
                                    <img class="main-logo" src="{site_url}skin/theme-three/assets/images/partner/7.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="partner-item">
                            <div class="logo-img">
                                <a href="#">
                                    <img class="hover-logo" src="{site_url}skin/theme-three/assets/images/partner/8.png" alt="">
                                    <img class="main-logo" src="{site_url}skin/theme-three/assets/images/partner/8.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="partner-item">
                            <div class="logo-img">
                                <a href="#">
                                    <img class="hover-logo" src="{site_url}skin/theme-three/assets/images/partner/9.png" alt="">
                                    <img class="main-logo" src="{site_url}skin/theme-three/assets/images/partner/9.png" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- Partner End -->

        </div> 
        <!-- Main content End -->