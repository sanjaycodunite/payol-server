   <?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
        $service = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array(); 
    ?>


            <!-- Contact -->
            <div class="seo-contact-banner">
                <div class="round-shape-one"></div>
                <div class="round-shape-two"></div>
                <div class="container">
                    <h2 class="title">Already in Distributor Business? We have exciting services for you on our Distributor application</h2>
                    <a href="#" class="contact-button"> Create Account</a>

                </div> <!-- /.contianer -->
            </div> <!-- /.seo-contact-banner -->


            <!--Footer-->
            <footer class="theme-footer-one">
                <div class="shape-one" data-aos="zoom-in-right"></div>
                <img src="images/shape/shape-67.svg" alt="" class="shape-two">
                <div class="top-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 col-12 about-widget">
                                <a href="{site_url}" class="logo"><img src="{site_url}<?php echo $accountData['image_path'] ?>" alt=""></a>
                                <p>We Provide all online services like Mobile, DTH and Data Card Recharges, All Bill Payment, Travel Booking, Remittance / Money-transfers, Aeps, Pancard, White Label Recharge Websites and Software, Recharge and DTH Direct Operator API Provider and Many More.</p>
                                
                            </div> <!-- /.about-widget -->
                            <div class="col-lg-2 col-sm-6 col-12 footer-list" data-aos="fade-up">
                                <h5 class="title">Useful Links</h5>
                                <ul>
                                    <li><a href="#">Home</a></li>
                                    <li><a href="#">About us</a></li>
                                    <li><a href="#">Service</a></li>
                                    <li><a href="#"> Partner with us</a></li>
                                    <li><a href="#">News</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                </ul>
                            </div> <!-- /.footer-list -->
                            <div class="col-lg-3 col-sm-6 col-12 footer-list" data-aos="fade-up">
                                <h5 class="title">Services</h5>
                                <ul>
                                    <li><a href="#">Bill Payments & Recharge </a></li>
                                    <li><a href="#">Banking Services </a></li>
                                    <li><a href="#">Payment Services </a></li>
                                    <li><a href="#">Travel & E-Governance Services </a></li>
                                    <li><a href="#">Cashdrop Services </a></li>
                                </ul>
                            </div> <!-- /.footer-recent-post -->

                              <?php
                        $account_id = $this->User->get_domain_account();
                        $accountData = $this->User->get_account_data($account_id);
                        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
    ?>
                            
                            <div class="col-lg-3 col-sm-6 col-12 footer-information" data-aos="fade-up">
                                <h5 class="title">Contact Us</h5>
                                <a href="#" class="email"><?php  echo $contactDetail['email'];?></a>
                                <a href="#" class="phone">+91-<?php  echo $contactDetail['mobile'];?></a>
                                <p><?php  echo $contactDetail['address'];?></p>
                                <ul>
                                    <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>
                        </div> <!-- /.row -->
                    </div> <!-- /.container -->


                </div> <!-- /.top-footer -->
                <div class="footer-map"><img src="{site_url}skin/main-front/images/resourse/footer-map.png" alt="image"></div>
                <div class="container">
                    <div class="bottom-footer">
                        <div class="clearfix">
                            <p>&copy; 2021 copyright all right reserved</p>
                            <ul>
                                <li><a href="#">Privace & Policy.</a></li>
                                <li><a href="faq.html">Faq.</a></li>
                                <li><a href="#">Terms.</a></li>
                            </ul>
                        </div>
                    </div> <!-- /.bottom-footer -->
                </div>
            </footer> <!-- /.theme-footer-one -->
            

            

            <!-- Scroll Top Button -->
            <button class="scroll-top tran3s">
                <i class="fa fa-angle-up" aria-hidden="true"></i>
            </button>
            

        <!-- jQuery -->
        <script src="{site_url}skin/main-front/vendor/jquery.2.2.3.min.js"></script>
        <!-- Popper js -->
        <script src="{site_url}skin/main-front/vendor/popper.js/popper.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="{site_url}skin/main-front/vendor/bootstrap/js/bootstrap.min.js"></script>
        <!-- menu  -->
        <script src="{site_url}skin/main-front/vendor/mega-menu/assets/js/custom.js"></script>
        <!-- AOS js -->
        <script src="{site_url}skin/main-front/vendor/aos-next/dist/aos.js"></script>
        <!-- WOW js -->
        <script src="{site_url}skin/main-front/vendor/WOW-master/dist/wow.min.js"></script>
        <!-- owl.carousel -->
        <script src="{site_url}skin/main-front/vendor/owl-carousel/owl.carousel.min.js"></script>
        <!-- js count to -->
        <script src="{site_url}skin/main-front/vendor/jquery.appear.js"></script>
        <script src="{site_url}skin/main-front/vendor/jquery.countTo.js"></script>
        <!-- Fancybox -->
        <script src="{site_url}skin/main-front/vendor/fancybox/dist/jquery.fancybox.min.js"></script>


        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


        <!-- Theme js -->
        <script src="{site_url}skin/main-front/js/theme.js"></script>
        </div> <!-- /.main-page-wrapper -->

        <script type="text/javascript">
            // -------------------------------- Customer Slider 
        var csSlider = $ (".banner-one");
          if(csSlider.length) {
              csSlider.owlCarousel({
                loop:true,
                nav:true,
                navText: ["<i class='fa fa-chevron-left'></i>" , "<i class='fa fa-chevron-right'></i>"],
                dots:false,
                autoplay:true,
                autoHeight:true,
                margin:0,
                autoplayTimeout:4500,
                autoplaySpeed:1000,
                lazyLoad:true,
                singleItem:true,
                responsive:{
                    0:{
                        items:1
                    },
                    768:{
                        items:1
                    },
                    992:{
                        items:1
                    }
                }
            });
          }

        </script>

        <script type="text/javascript">
            // -------------------------------- Customer Slider 
        var csSlider = $ (".customer-slider");
          if(csSlider.length) {
              csSlider.owlCarousel({
                loop:true,
                nav:false,
                navText: ["<i class='fa fa-chevron-left'></i>" , "<i class='fa fa-chevron-right'></i>"],
                dots:true,
                autoplay:true,
                margin:0,
                autoplayTimeout:1000,
                autoplaySpeed:1000,
                lazyLoad:true,
                singleItem:true,
                responsive:{
                    0:{
                        items:1
                    },
                    768:{
                        items:2
                    },
                    992:{
                        items:2
                    }
                }
            });
          }

        </script>
        <script type="text/javascript">
            // -------------------------------- Customer Slider 
        var csSlider = $ (".service-slider");
          if(csSlider.length) {
              csSlider.owlCarousel({
                loop:true,
                nav:true,
                navText: ["<i class='fa fa-chevron-left'></i>" , "<i class='fa fa-chevron-right'></i>"],
                dots:false,
                autoplay:true,
                autoHeight:true,
                margin:10,
                autoplayTimeout:4500,
                autoplaySpeed:1000,
                lazyLoad:true,
                singleItem:true,
                responsive:{
                    0:{
                        items:4
                    },
                    768:{
                        items:6
                    },
                    992:{
                        items:8
                    }
                }
            });
          }

        </script>

        <script type="text/javascript">
            // -------------------------------- Customer Slider 
        var csSlider = $ (".our-service-slider");
          if(csSlider.length) {
              csSlider.owlCarousel({
                loop:true,
                nav:false,
                navText: ["<i class='fa fa-chevron-left'></i>" , "<i class='fa fa-chevron-right'></i>"],
                dots:true,
                autoplay:true,
                autoHeight:true,
                margin:10,
                autoplayTimeout:4500,
                autoplaySpeed:1000,
                lazyLoad:true,
                singleItem:true,
                responsive:{
                    0:{
                        items:1
                    },
                    768:{
                        items:2
                    },
                    992:{
                        items:2
                    }
                }
            });
          }

        </script>

            <script type="text/javascript">
            // -------------------------------- Customer Slider 
        var csSlider = $ (".provide-service-slider");
          if(csSlider.length) {
              csSlider.owlCarousel({
                loop:true,
                nav:false,
                navText: ["<i class='fa fa-chevron-left'></i>" , "<i class='fa fa-chevron-right'></i>"],
                dots:true,
                autoplay:true,
                margin:40,
                autoplayTimeout:4500,
                autoplaySpeed:1000,
                lazyLoad:true,
                singleItem:true,
                responsive:{
                    0:{
                        items:1
                    },
                    768:{
                        items:2
                    },
                    992:{
                        items:2
                    }
                }
            });
          }

        </script>

        <script type="text/javascript">
            // ----------------------------- Counter Function
        var timer = $('.timer');
        if(timer.length) {
            timer.appear(function () {
              timer.countTo();
          });
        }
        </script>


<?php 


 $account_id = $this->User->get_domain_account();
        //$account_id = 2;
        $accountData = $this->User->get_account_data($account_id);

        $title = $accountData['title'];

?>

<script type="text/javascript">
    jQuery('#enquiry').submit(function(event) {
            event.preventDefault();

            
        var siteUrl = jQuery("#siteUrl").val();
          

        jQuery.ajax({
            url:"<?php echo base_url('Home/auth') ?>",
            data: jQuery("#enquiry").serialize(),
            type : 'post',
            async: false,
            dataType : 'json',
            success: function(response)
            {
             
              jQuery("#enquiry")[0].reset();
             swal({
          title: "<?php echo $title; ?>",
          text: "Thank you for connecting with us.Our team will get back to you soon",
          icon: "success",
          
        });

            },
            error : function(){
              alert("Something Went Wrong");
            }
        });


     


   
});

 
</script>

        
    </body>
</html>