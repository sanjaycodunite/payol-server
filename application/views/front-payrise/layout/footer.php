<?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
        $service = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array();  
    ?>



    <!-- Start of Footer  section
        ============================================= -->
        <section class="dia-footer-section position-relative">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="dia-footer-widget pera-content dia-headline clearfix">
                            <div class="dia-footer-logo">
                                <img src="{site_url}skin/front-payrise/assets/img/d-agency/logo/footer-logo.png" alt="">
                            </div>
                            <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages.</p>
                           </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="dia-footer-widget dia-headline ul-li-block clearfix">
                            <h3 class="dia-widget-title">Useful <span>link:</span></h3>
                            <ul>
                                <li><a href="{site_url}">Home</a></li>
                                <li><a href="{site_url}about">About Us</a></li>
                                <li><a href="{site_url}services">Services</a></li>
                                <li><a href="{site_url}contact">Contact Us</a></li>
                                <li><a href="{site_url}login">Login</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                                <li><a href="#">Terms & Conditions  </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="dia-footer-widget dia-headline  ul-li-block clearfix">
                            <h3 class="dia-widget-title">Contact <span>Info:</span></h3>
                            <div class="contact_info">
                            <h4>
                                <i class="fas fa-mobile-alt"></i>
                                Call Us: <span>(+91) <?php echo $accountData['mobile'] ?></span></h4>
                            <h4>
                                <i class="fas fa-map-marker-alt"></i>
                               Address:<span><?php echo $contactDetail['address'] ?></span>
                            </h4>

                            <h4>
                                <i class="fas fa-envelope"></i>
                                Mail ID: <span><?php echo $accountData['email'] ?></span></h4>
                            </div>
                            <div class="dia-footer-social">
                                <a href="#"><i class="fab fa-facebook-f "></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dia-footer-copyright">
                <div class="container">
                    <div class="dia-footer-copyright-content">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="dia-copyright-text pera-content text-center">
                                    <p>© Copyright 2024. All Rights Reserved by Payrise</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dia-footer-shape3 position-absolute"><img src="{site_url}skin/front-payrise/assets/img/d-agency/shape/diamap.png" alt=""></div>
        </section>
      <!-- End of Footer  section
        ============================================= -->       


        <!-- JS library -->
        <script src="{site_url}skin/front-payrise/assets/js/jquery.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/popper.min.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/appear.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/bootstrap.min.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/wow.min.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/jquery.fancybox.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/tilt.jquery.min.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/owl.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/aos.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/slick.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/jquery.barfiller.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/typer-new.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/odometer.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/parallax-scroll.js"></script>
        <script src="{site_url}skin/front-payrise/assets/js/script.js"></script>
        
        <script>
        $(window).on ('load', function (){
        $('#ctn-preloader').delay(2000).fadeOut('slow');
        $('#preloader').delay(2000).fadeOut('slow'); 
        $('body').delay(2000).css({'overflow':'visible'});
        })
</script>
    </body>
</html>            