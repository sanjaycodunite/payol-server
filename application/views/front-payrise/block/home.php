
        <!-----main-slider-section---->

           <?php
if($slider){
?>

        <section class="main-slider  position-relative">
        <div id="main-servi-slider" class="dia-banner-section owl-carousel">
                 <?php
            $i = 0;
            foreach($slider as $list){
            ?>
         <div class="main-slider-img">
         <img src="{site_url}<?php echo $list['image']?>">
         </div>

            <?php } ?>
            
     </div>    
         </section>
     <?php } ?>


 <!-- Start of about section
        ============================================= -->
        <section id="dia-about" class="dia-about-section">
            <div class="container">
                <div class="dia-about-content">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-12 wow fadeFromLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <div class="dia-about-text-area">
                                <div class="dia-about-title-text">
                                    <div class="dia-section-title text-left text-capitalize dia-headline">
                                        <h6 class="title_heading">About Us</h6>
                                        <h2>Who <span>We Are</span></h2>
                                    </div>
                                    <div class="dia-about-text pt-0">
                                       <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                        <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. </p>
                                    </div>
                                    <div class="dia-exp-btn text-center">
                                    <a href="#">Read More</a>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 wow fadeFromRight" data-wow-delay="300ms" data-wow-duration="1500ms">
                            <div class="dia-about-img position-relative">
                                <img src="{site_url}skin/front-payrise/assets/img/about_vector.png" alt="">
                                <div class="ab-shape1 position-absolute" data-parallax='{"x" : -30}'> <img src="{site_url}skin/front-payrise/assets/img/d-agency/shape/as1.png" alt=""></div>
                                <div class="ab-shape2 position-absolute" data-parallax='{"x" : 30}'> <img src="{site_url}skin/front-payrise/assets/img/d-agency/shape/as1.png" alt=""></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- End of About section
        ============================================= -->  


         <!-- Start of Services section
        ============================================= -->
        <section class="dia-about-section">
            <div class="container">
                <div class="dia-about-content">
                    <div class="row align-items-center">
                       <div class="col-lg-6 col-md-12 wow fadeFromRight" data-wow-delay="300ms" data-wow-duration="1500ms">
                            <div class="dia-about-img position-relative">
                                <img src="{site_url}skin/front-payrise/assets/img/about_vector2.png" alt="">
                                <div class="ab-shape1 position-absolute" data-parallax='{"x" : -30}'> <img src="{site_url}skin/front-payrise/assets/img/d-agency/shape/as1.png" alt=""></div>
                                <div class="ab-shape2 position-absolute" data-parallax='{"x" : 30}'> <img src="{site_url}skin/front-payrise/assets/img/d-agency/shape/as1.png" alt=""></div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12 wow fadeFromLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <div class="dia-about-text-area">
                                <div class="dia-about-title-text">
                                    <div class="dia-section-title text-left text-capitalize dia-headline">
                                        <h6 class="title_heading">What We Do</h6>
                                        <h2>Services <span>We offer</span></h2>
                                    </div>
                                    <div class="dia-about-text pt-0">
                                       <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                        <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. </p>
                                    </div>

                                    <div class="services_list_colm">
                                     <div class="services_list_box"> 
                                      <i class="fa fa-headphones"></i> <h4>24 x7 Contact Support</h4>
                                    </div>
                                    <div class="services_list_box"> 
                                      <i class="fab fa-audible"></i>  <h4>Instant Commission Sattlement</h4>
                                    </div>
                                    <div class="services_list_box"> 
                                     <i class="fab fa-digital-ocean"></i>  <h4>Widest service portfolio</h4>
                                    </div>
                                    <div class="services_list_box"> 
                                      <i class="fab fa-medapps"></i> <h4>Zero-interest working capital loan</h4>
                                    </div>
                                  </div>
                                   
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </section>
    <!-- End of Services section
        ============================================= -->  



 
     

    <!-- Start of Fun fact section
        ============================================= -->           
        <section id="dia-fun-fact" class="dia-fun-fact-section" style="background-image: url({site_url}skin/front-payrise/assets/img/shape/dot-bg.svg);">
            <div class="container">
                <div class="dia-fun-fact-content">
                  <div class="dia-section-title text-center text-capitalize pera-content dia-headline">
                    <h2>Successfully & <span>counting</span></h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</p>
                </div>
                   
                    <div class="dia-fun-fact-counter">
                        <div class="row">
                            <div class="col-lg-4 col-md-4">
                                <div class="dia-fun-fact-item dia-headline pera-content text-center">
                                    <div class="fun-fact-number d-flex">
                                        <h3 class="odometer" data-count="28">0</h3><span>k</span>
                                    </div>
                                    <span class="fun-fact-tag">clients</span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="dia-fun-fact-item dia-headline pera-content text-center">
                                    <div class="fun-fact-number d-flex">
                                        <h3 class="odometer" data-count="220">0</h3><span>+</span>
                                    </div>
                                    <span class="fun-fact-tag">Happy Customer</span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="dia-fun-fact-item dia-headline pera-content text-center">
                                    <div class="fun-fact-number d-flex">
                                        <h3 class="odometer" data-count="54">0</h3><span>k</span>
                                    </div>
                                    <span class="fun-fact-tag">Completed Projects</span>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- End of Fun fact section
        ============================================= -->  

  

  <!-- Start of testimonial section
        ============================================= -->
        <section class="dia-testimonial-section position-relative">
           
            <div class="container">
                <div class="dia-section-title text-center text-capitalize dia-headline">
                    <h2>Client’s review for <br/><span>our work satisfaction</span></h2>
                </div>
                <div  class="dia-testimonial_slider-area position-relative">
                    
                 
                    <div id="dia-testimonial_slide" class="carousel slide" data-ride="carousel" >
                    <div class="carousel_preview">
                            <div class="carousel-inner relative-position">
                                <div class="carousel-item active">
                                    <div class="dia-testimonial_content">
                                       <div class="dia-testimonial_name_designation">
                                            <div class="dia-testimonial_meta dia-headline pera-content">
                                               <img class="testimonial_img" src="{site_url}skin/front-payrise/assets/img/d-agency/testimonial/tst1.png" alt="">
                                                <h4>Admin</h4>
                                                <div class="dia-testimonial_rating ul-li">
                                            <ul>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                            </ul>
                                        </div>
                                            </div>
                                        </div>
                                      <div class="dia-testimonial_text relative-position pera-content dia-headline">
                                            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,</p>
                                        </div>
                                       
                                    </div>
                                </div>
                                <!-- /slide item -->
                                <div class="carousel-item">
                                    <div class="dia-testimonial_content">
                                       <div class="dia-testimonial_name_designation">
                                            <div class="dia-testimonial_meta dia-headline pera-content">
                                               <img class="testimonial_img" src="{site_url}skin/front-payrise/assets/img/d-agency/testimonial/tst2.png" alt="">
                                                <h4>Admin</h4>
                                                <div class="dia-testimonial_rating ul-li">
                                            <ul>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                            </ul>
                                        </div>
                                            </div>
                                        </div>
                                      <div class="dia-testimonial_text relative-position pera-content dia-headline">
                                            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,</p>
                                        </div>
                                       
                                    </div>
                                </div>
                                <!-- /slide item -->
                                <div class="carousel-item">
                                   <div class="dia-testimonial_content">
                                    <div class="dia-testimonial_name_designation">
                                            <div class="dia-testimonial_meta dia-headline pera-content">
                                               <img class="testimonial_img" src="{site_url}skin/front-payrise/assets/img/d-agency/testimonial/tst3.png" alt="">
                                                <h4>Admin</h4>
                                                <div class="dia-testimonial_rating ul-li">
                                            <ul>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                            </ul>
                                        </div>
                                            </div>
                                        </div>
                                      <div class="dia-testimonial_text relative-position pera-content dia-headline">
                                            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,</p>
                                        </div>
                                        
                                    </div>
                                </div>



                                <div class="carousel-item">
                                  <div class="dia-testimonial_content">
                                    <div class="dia-testimonial_name_designation">
                                            <div class="dia-testimonial_meta dia-headline pera-content">
                                               <img class="testimonial_img" src="{site_url}skin/front-payrise/assets/img/d-agency/testimonial/tst1.png" alt="">
                                                <h4>Admin</h4>
                                                <div class="dia-testimonial_rating ul-li">
                                            <ul>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                            </ul>
                                        </div>
                                            </div>
                                        </div>
                                      <div class="dia-testimonial_text relative-position pera-content dia-headline">
                                            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,</p>
                                        </div>
                                        
                                    </div>
                                </div>

                                 <div class="carousel-item">
                                    <div class="dia-testimonial_content">
                                      <div class="dia-testimonial_name_designation">
                                            <div class="dia-testimonial_meta dia-headline pera-content">
                                               <img class="testimonial_img" src="{site_url}skin/front-payrise/assets/img/d-agency/testimonial/tst2.png" alt="">
                                                <h4>Admin</h4>
                                                <div class="dia-testimonial_rating ul-li">
                                            <ul>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                                <li><i class="fas fa-star"></i></li>
                                            </ul>
                                        </div>
                                            </div>
                                        </div>
                                         <div class="dia-testimonial_text relative-position pera-content dia-headline">
                                            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,</p>
                                        </div>
                                    </div>
                                </div>                                <!-- /slide item -->
                            </div>
                        </div>
                        <div class="dia-testimonial_indicator-dot">
                            <ol class="carousel-indicators2">
                                <li data-target="#dia-testimonial_slide" data-slide-to="0" class="active">
                                </li>
                                <li data-target="#dia-testimonial_slide" data-slide-to="1">
                                </li>
                                <li data-target="#dia-testimonial_slide" data-slide-to="2">
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- End of testimonial section
        ============================================= --> 



         <!-- Start of blog section
        ============================================= -->
        <section class="dia-blog-section">
            <div class="container">
                <div class="dia-section-title text-center text-capitalize pera-content dia-headline">
                    <h2>Our <span>Blog</span></h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</p>
                </div>
                <div class="dia-blog-content">
                    <div class="row">
                        <div class="col-lg-4 wow fadeFromUp" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <div class="dia-blog-img-text">
                                <div class="dia-blog-img">
                                    <img src="{site_url}skin/front-payrise/assets/img/d-agency/blog/b1.jpg" alt="">
                                </div>
                                <div class="dia-blog-text">
                                    <h3><a href="#">Simple Steps for Blogs Post.</a></h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</p>
                                </div>
                               
                            </div>
                        </div>
                        <div class="col-lg-4 wow fadeFromUp" data-wow-delay="300ms" data-wow-duration="1500ms">
                            <div class="dia-blog-img-text">
                                <div class="dia-blog-img">
                                    <img src="{site_url}skin/front-payrise/assets/img/d-agency/blog/b2.jpg" alt="">
                                </div>
                              
                               <div class="dia-blog-text">
                                    <h3><a href="#">Simple Steps for Blogs Post.</a></h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</p>
                                </div>
                               
                            </div>
                        </div>
                        <div class="col-lg-4 wow fadeFromUp" data-wow-delay="600ms" data-wow-duration="1500ms">
                            <div class="dia-blog-img-text">
                                <div class="dia-blog-img">
                                    <img src="{site_url}skin/front-payrise/assets/img/d-agency/blog/b3.jpg" alt="">
                                </div>
                               <div class="dia-blog-text">
                                    <h3><a href="#">Simple Steps for Blogs Post.</a></h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</p>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- End of Blog section
        ============================================= -->  


