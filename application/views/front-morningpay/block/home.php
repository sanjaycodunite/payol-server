   <!-- CONTENT START -->
        <div class="page-content">
 
      <!-- SLIDER START --> 

        <?php
if($slider){
?>
      <div class="aon-banner1-area">
        <div class="aon-banner1-wrap">
          
          <div class="owl-carousel aon-bnr1-carousal">
            <!--block 1-->
             <?php
            $i = 0;
            foreach($slider as $list){
            ?>

            <div class="item">
              <div class="bnr-bnr1-wrap">
              <img src="{site_url}<?php echo $list['image']?>" class="img-fluid">  
              </div>
            </div>

             <?php $i++;} ?>   
            
          </div>
        </div>
      </div>
       <?php } ?>
      <!-- SLIDER END -->

      <!-- about Area Section -->
      <div class="about_area_Section p-t90 p-b0">
        <div class="container">
          <div class="row">
          <div class="col-lg-6 col-md-6"> 
          <div class="about_left_wrap">
          <figure class="mb-0 about_img position-relative">
          <img src="{site_url}skin/front-morningpay/images/about.jpg" class="img-fluid"></figure>
          <figure class="mb-0 shape_1 position-absolute">
          <div class="shape_box"></div></figure>
          <figure class="mb-0 shape_2 position-absolute">
          <div class="shape_box"><img src="{site_url}skin/front-morningpay/images/aboutus_image_shape2.png" class="img-fluid"></div></figure>
          </div></div>

          <div class="col-lg-6 col-md-6"> 
          <div class="about_content_wrap p-l30">
          <h6>About Us</h6>
          <h2>Who We Are</h2>
          <p>India is leading the world in fintech with advanced payment products and
digital transactions. However, many still struggle with basic banking services.
At Payrise, we are committed to making a real difference by bridging the tech
gap between rural and urban areas. We want everyone in India to access
modern financial services and promote inclusivity for sustainable growth.
Together, we can build a fairer financial future.
</p>
<p>
    We believe true progress means improving people's lives with technology. Our
user-friendly solutions meet the needs of both urban professionals and rural
entrepreneurs. We provide secure, reliable services and focus on financial
literacy by offering educational resources to help people make informed
decisions
</p>
<p>
    We collaborate with government and other partners to align our efforts with
national goals. Our team is dedicated to innovation, using the latest
technologies like AI and blockchain to enhance our services. At Payrise, we are
committed to continuous improvement and creating a better financial
experience for everyone
</p>
          <div class="about_more_Btn">
          <a href="{site_url}about" class="btn btn-default">
          <span class="bp-shape"></span>
          <span class="bp-shape"></span>
          <span class="bp-shape"></span>
          <span class="bp-shape"></span>
          <span class="bp-text">Read More <i class="flaticon-030-arrow"></i></span></a> 
          </div>
          </div></div>
        </div>
        </div>
      </div>
      <!-- about Area Section End -->

       <!-- Services Section -->
      <section class="Services_section p-t90 p-b0">
       <div class="container">  
       <div class="row align-items-center">
       <div class="col-lg-12">
       <div class="client_heading text-center">
       <h6>WHAT WE DO</h6>  
       <h2>Services We offer</h2>
       <!--<p>Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>-->
       
        </div></div>  
              
              <div class="col-lg-12 col-md-12">
                <div class="row justify-content-center">
              <div class="col-lg-4">
             <div class="services_colm">
              <a href="banking-services.html">   
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/aadhar.png" class="img-fluid">
             </div>
             <div class="services_colm_content">
             <h3>Aadhaar Banking</h3>
             <p>Aadhaar Banking Services simplify financial access in India by leveraging the Aadhaar identification number. With Aadhaar linked to their bank accounts, individuals can open accounts seamlessly, </p> 
             </div></a> 
             </div>
             </div>

             <div class="col-lg-4">
             <div class="services_colm">
                  <a href="banking-services.html">  
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/MiniATM.png" class="img-fluid">  
             </div>
             <div class="services_colm_content">
             <h3>Mini ATM</h3>
             <p>Mini ATM service offers convenient banking solutions that can be accessed anywhere. With mini ATMs, individuals can withdraw cash, check balances, transfer funds, and make bill payments, </p> 
             </div></a> 
             </div>
             </div>
           
           <div class="col-lg-4">
             <div class="services_colm">
              <a href="banking-services.html">      
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/Travel_services.png" class="img-fluid">  
             </div>
             <div class="services_colm_content">
             <h3>Travel Services</h3>
             <p>Travel services offer convenient solutions for all your travel needs. From flight and hotel bookings to transportation arrangements and tour packages, these services ensure a seamless travel experience. With expert assistance, </p> 
             </div> </a>
             </div>
             </div>

              <div class="col-lg-4">
             <div class="services_colm">
              <a href="banking-services.html">      
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/card.png" class="img-fluid">     
             </div>
             <div class="services_colm_content">
             <h3>PAN Card</h3>
             <p>PAN Card service provides a hassle-free solution for obtaining and managing Permanent Account Number (PAN) cards in India. With this service, individuals can apply for new PAN cards,</p>  
             </div></a> 
             </div>
             </div>

             <div class="col-lg-4">
             <div class="services_colm">
             <a href="banking-services.html">       
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/Digital.png" class="img-fluid">    
             </div>
             <div class="services_colm_content">
             <h3>Digital Payment</h3>
             <p>Digital payment services provide a secure and convenient way to handle financial transactions. With these services, users can make online payments, transfer funds, and conduct cashless transactions effortlessly. </p>  
             </div> </a>
             </div>
             </div>

            </div>
            </div>

           </div>
       </div>
       </section>
      <!-- Services Section End -->


       <!-- JOIN  Section -->
      <section class="join_Section_2 Join_section p-t90 p-b90">
       <div class="container">  
       <div class="row align-items-center">
        <div class="col-lg-12">
       <div class="client_heading text-center">
       <h2>Fintech Network</h2>
       <!--<p>Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>-->
       
        </div></div>  
       <div class="col-lg-12 col-md-12">
                <div class="row">
             <div class="col-lg-4">
             <div class="services_colm">
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/portfolio_1.png" class="img-fluid">  
             </div>
             <div class="services_colm_content">
             <h3>Widest service portfolio</h3>
             <p> Diverse services: finance, e-commerce, digital marketing, IT consulting. Tailored solutions for success. Trust our expertise.</p>  
             </div> 
             </div>
             </div>

             <div class="col-lg-4">
             <div class="services_colm">
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/portal.png" class="img-fluid">
             </div>
             <div class="services_colm_content">
             <h3>Multilingual service portal</h3>
             <p>Language-inclusive service portal: Access services, assistance, and interactions in your preferred language. Break language barriers effortlessly.</p>  
             </div> 
             </div>
             </div>

             <div class="col-lg-4">
             <div class="services_colm">
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/Support.png" class="img-fluid">  
             </div>
             <div class="services_colm_content">
             <h3>Multiple channels of customer support</h3>
             <p>Convenient customer support: Phone, email, live chat, social media. Get assistance with ease. </p>  
             </div> 
             </div>
             </div>
           
           <div class="col-lg-4">
             <div class="services_colm">
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/interest.png" class="img-fluid">   
             </div>
             <div class="services_colm_content">
             <h3>Zero-interest working capital loan to channel partners</h3>
             <p>Zero-interest working capital loan for partners. Grow your business hassle-free with no interest charges.</p> 
             </div> 
             </div>
             </div>

              <div class="col-lg-4">
             <div class="services_colm">
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/national.png" class="img-fluid">     
             </div>
             <div class="services_colm_content">
             <h3>National presence</h3>
             <p>Nationwide services, bridging gaps. Equal access from cities to rural areas. Positive impact for individuals, businesses.</p> 
             </div> 
             </div>
             </div>

             <div class="col-lg-4">
             <div class="services_colm">
             <div class="services_icons">
             <img src="{site_url}skin/front-morningpay/images/service_icon/7hrs.png" class="img-fluid">     
             </div>
             <div class="services_colm_content">
             <h3>Best-in-industry customer support, 7 days a week</h3>
             <p>24/7 customer support. Expert assistance for all your needs. Timely and reliable service. Trust us for exceptional support.</p> 
             </div> 
             </div>
             </div>

            </div>
            </div>

        

           </div>
       </div>
       </section>
      <!-- JOIN  Section End -->

      <!-- Discover Section Start -->
            <section class="client_section p-t90 p-b90">
            <div class="client_heading text-center">
            <h2>Our Partners</h2> 
            </div>  
           <div class="client_slide owl-carousel">
            <div class="item">
            <div class="client_slide_item">
            <img src="{site_url}skin/front-morningpay/images/client/airtel-payment-bank.png" class="img-fluid"> 
            </div></div>
            <div class="item">
            <div class="client_slide_item">
            <img src="{site_url}skin/front-morningpay/images/client/au-bank-logo.png" class="img-fluid">  
            </div></div>
            <div class="item">
            <div class="client_slide_item">
            <img src="{site_url}skin/front-morningpay/images/client/bank-india-logo.png" class="img-fluid"> 
            </div></div>
            <div class="item">
            <div class="client_slide_item">
            <img src="{site_url}skin/front-morningpay/images/client/bharat-billpay.png" class="img-fluid">  
            </div></div>
            <div class="item">
            <div class="client_slide_item">
            <img src="{site_url}skin/front-morningpay/images/client/blinkit-logo.png" class="img-fluid">  
            </div></div>
            <div class="item">
            <div class="client_slide_item">
            <img src="{site_url}skin/front-morningpay/images/client/irctc-logo.png" class="img-fluid">  
            </div></div>
            <div class="item">
            <div class="client_slide_item">
            <img src="{site_url}skin/front-morningpay/images/client/mobikwik.png" class="img-fluid">  
            </div></div>
           </div>
            </section>
      <!-- Discover Section End -->

      


      <!-- Testimonials -->
      <div class="aon-testmo-area p-t90 p-b90">
        <div class="container">

          <!--Title Section Start-->
          <div class="section-head center">
            <h2 class="aon-title">What Client Says?</h2>
          </div>
          <!--Title Section End-->

          <div class="section-content">
            <div class="owl-carousel aon-testi-two-carousel aon-owl-arrow">

                <!-- COLUMNS 1 -->
                <div class="item">
                  <div class="aon-testmo-wrap wow fadeInDown" data-wow-duration="2000ms">
                    
                    <div class="aon-testmo-text">As Manish Sharma, I am thrilled to share my experience with Morning Pay. This platform has truly empowered my digital transactions in India. With its user-friendly interface and innovative solutions, Morning Pay has made managing my finances convenient and hassle-free. I appreciate their commitment to bridging the gap between rural and urban India, ensuring access to basic banking facilities for all. The 24/7 customer support is exceptional, providing prompt assistance whenever needed. I highly recommend Morning Pay to anyone seeking a secure and efficient digital payment solution. It has simplified my financial life, and I am proud to be a part of the digital transformation with Morning Pay.</div>
                    <div class="testimonials_row_list">
                    <div class="aon-testmo-pic"><img src="{site_url}skin/front-morningpay/images/testimonials/img1.png" alt="Image"></div>
                    <div>
                      <div class="aon-testmo-name">Manish Sharma</div>
                    <div class="aon-testmo-rating">
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                    </div>
                  </div></div>
                  </div>
                </div>
                <!-- COLUMNS 2 -->
                <div class="item">
                  <div class="aon-testmo-wrap wow fadeInDown" data-wow-duration="2000ms">
                    
                    <div class="aon-testmo-text">As Rahul Verma, I cannot express enough how impressed I am with Morning Pay. This platform has revolutionized digital transactions in India, making it incredibly convenient and efficient. Managing my finances has never been easier, thanks to Morning Pay user-friendly interface and advanced solutions. I am particularly grateful for their commitment to bridging the gap between rural and urban India, ensuring financial inclusion for all. The round-the-clock customer support is exceptional, providing timely assistance whenever I need it. If you're looking for a secure and seamless digital payment solution, I highly recommend Morning Pay. Join me in embracing the digital revolution with Morning Pay and experience the convenience it brings to your financial life.</div>
                    <div class="testimonials_row_list">
                    <div class="aon-testmo-pic"><img src="{site_url}skin/front-morningpay/images/testimonials/img2.png" alt="Image"></div>
                    <div>
                      <div class="aon-testmo-name">Rahul Verma</div>
                    <div class="aon-testmo-rating">
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                    </div>
                    </div>
                  </div>
                </div>
                </div>
                <!-- COLUMNS 3 -->
                <div class="item">
                  <div class="aon-testmo-wrap wow fadeInDown" data-wow-duration="2000ms">
                    
                    <div class="aon-testmo-text">Vijay Sharma here, and I can't stop raving about Morning Pay. This platform has truly empowered India's digital economy, revolutionizing the way we conduct financial transactions. With Morning Pay user-friendly interface and innovative features, managing my finances has become a breeze. I am impressed by their commitment to financial inclusion, bridging the gap between rural and urban India. The 24/7 customer support is exceptional, providing quick and efficient assistance whenever needed. I confidently recommend Morning Pay to anyone looking for a secure and seamless digital payment experience. Join me in embracing the future of digital finance with Morning Pay and be a part of India's thriving digital economy.</div>
                    <div class="testimonials_row_list">
                    <div class="aon-testmo-pic"><img src="{site_url}skin/front-morningpay/images/testimonials/img3.png" alt="Image"></div>
                    <div>
                    <div class="aon-testmo-name">Vijay Sharma</div>
                    <div class="aon-testmo-rating">
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                    </div>
                    </div>
                  </div></div>
                </div>
                <!-- COLUMNS 1 -->
                <div class="item">
                  <div class="aon-testmo-wrap wow fadeInDown" data-wow-duration="2000ms">
                    
                    <div class="aon-testmo-text">As Manish Sharma, I am thrilled to share my experience with Morning Pay. This platform has truly empowered my digital transactions in India. With its user-friendly interface and innovative solutions, Morning Pay has made managing my finances convenient and hassle-free. I appreciate their commitment to bridging the gap between rural and urban India, ensuring access to basic banking facilities for all. The 24/7 customer support is exceptional, providing prompt assistance whenever needed. I highly recommend Morning Pay to anyone seeking a secure and efficient digital payment solution. It has simplified my financial life, and I am proud to be a part of the digital transformation with Morning Pay.</div>
                    <div class="testimonials_row_list">
                    <div class="aon-testmo-pic"><img src="{site_url}skin/front-morningpay/images/testimonials/img1.png" alt="Image"></div>
                    <div>
                    <div class="aon-testmo-name">Manish Sharma</div>
                    <div class="aon-testmo-rating">
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                    </div>
                  </div></div>
                  </div>
                </div>
                <!-- COLUMNS 2 -->
                <div class="item">
                  <div class="aon-testmo-wrap wow fadeInDown" data-wow-duration="2000ms">
                    
                    <div class="aon-testmo-text">As Rahul Verma, I cannot express enough how impressed I am with Morning Pay. This platform has revolutionized digital transactions in India, making it incredibly convenient and efficient. Managing my finances has never been easier, thanks to Morning Pay user-friendly interface and advanced solutions. I am particularly grateful for their commitment to bridging the gap between rural and urban India, ensuring financial inclusion for all. The round-the-clock customer support is exceptional, providing timely assistance whenever I need it. If you're looking for a secure and seamless digital payment solution, I highly recommend Morning Pay. Join me in embracing the digital revolution with Morning Pay and experience the convenience it brings to your financial life.</div>
                    <div class="testimonials_row_list">
                    <div class="aon-testmo-pic"><img src="{site_url}skin/front-morningpay/images/testimonials/img2.png" alt="Image"></div>
                    <div>
                      <div class="aon-testmo-name">Rahul Verma</div>
                    <div class="aon-testmo-rating">
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                    </div>
                    </div>
                  </div></div>
                </div>
                <!-- COLUMNS 3 -->
                <div class="item">
                  <div class="aon-testmo-wrap wow fadeInDown" data-wow-duration="2000ms">
                    
                    <div class="aon-testmo-text"> Vijay Sharma here, and I can't stop raving about Morning Pay. This platform has truly empowered India's digital economy, revolutionizing the way we conduct financial transactions. With Morning Pay user-friendly interface and innovative features, managing my finances has become a breeze. I am impressed by their commitment to financial inclusion, bridging the gap between rural and urban India. The 24/7 customer support is exceptional, providing quick and efficient assistance whenever needed. I confidently recommend Morning Pay to anyone looking for a secure and seamless digital payment experience. Join me in embracing the future of digital finance with Morning Pay and be a part of India's thriving digital economy. </div>
                    <div class="testimonials_row_list">
                    <div class="aon-testmo-pic"><img src="{site_url}skin/front-morningpay/images/testimonials/img3.png" alt="Image"></div>
                    <div>
                    <div class="aon-testmo-name">Vijay Sharma</div>
                    <div class="aon-testmo-rating">
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                      <span class="fa fa-star"></span>
                    </div></div>
                  </div>
                  </div>
                </div>               
              </div>
          </div>

        </div>
      </div>
      <!-- Testimonials End -->     

      