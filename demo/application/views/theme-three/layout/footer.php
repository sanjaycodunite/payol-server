<?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
        $service = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array();  
    ?>
<!-- Footer Start -->
        <footer id="rs-footer" class="rs-footer">
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-md-12 col-sm-12 footer-widget">
                            <div class="footer-logo mb-20">
                                <a href="#"><img src="{site_url}<?php echo $accountData['image_path']; ?>" alt=""></a>
                            </div>
                              <div class="textwidget pb-10"><p>We Provide all online services like
                                Travel Booking, Remittance / Money-transfers,
                                Aeps, Pancard,  White Label Websites
                                and Software, 
                                API Provider and Many More.</p>
                              </div>
                              <ul class="footer-social md-mb-30">  
                                  <li> 
                                      <a href="<?php echo $contactDetail['facebook']; ?>" target="_blank"><span><i class="fa fa-facebook"></i></span></a> 
                                  </li>
                                  <li> 
                                      <a href="<?php echo $contactDetail['twitter']; ?>" target="_blank"><span><i class="fa fa-twitter"></i></span></a> 
                                  </li>

                                  <li> 
                                      <a href="<?php echo $contactDetail['instagram']; ?>" target="_blank"><span><i class="fa fa-instagram"></i></span></a> 
                                  </li>
                                  <li> 
                                      <a href="<?php echo $contactDetail['linkedin']; ?>" target="_blank"><span><i class="fa fa-linkedin"></i></span></a> 
                                  </li>
                                                                           
                              </ul>
                        </div>
                        <div class="col-lg-4 col-md-12 col-sm-12 pl-45 md-pl-15 md-mb-30">
                            <h3 class="widget-title">Useful Link</h3>
                            <ul class="site-map footer_list-menu">
                                <li><a href="{site_url}">Home</a></li>
                                <li><a href="{site_url}about">About Us</a></li>
                                <li><a href="{site_url}login">Login</a></li>
                                <li><a href="{site_url}contact">Contact Us</a></li>
                                <li><a href="{site_url}privacy">Privacy Policy</a></li>
                                <li><a href="{site_url}terms">Terms & Conditions</a></li>
                                
                                <?php if($account_id == 2 ) { ?>
                                    <li><a href="{site_url}app/morningpay.apk">Download App</a></li>
                                    
                                    
                                    <?php } else { ?>
                                    
                                    <li><a href="#">Download App</a></li>
                                    
                                    
                                    <?php } ?>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-12 col-sm-12 md-mb-30">
                            <h3 class="widget-title">Contact Info</h3>
                            <ul class="address-widget">
                                <li>
                                    <i class="flaticon-location"></i>
                                    <div class="desc"><?php echo $contactDetail['address']; ?></div>
                                </li>
                                <li>
                                    <i class="flaticon-call"></i>
                                    <div class="desc">
                                       <a href="#">+91-<?php echo $contactDetail['mobile']; ?></a>
                                    </div>
                                </li>
                                <li>
                                    <i class="flaticon-email"></i>
                                    <div class="desc">
                                        <a href="#"><?php echo $contactDetail['email']; ?></a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">                    
                    <div class="row y-middle">
                        <div class="col-lg-12">
                            <div class="copyright text-center">
                                <p>Copyright © 2021 <?php echo $accountData['title']; ?> | All rights reserved.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer End -->

        <!-- start scrollUp  -->
        <div id="scrollUp" class="orange-color">
            <i class="fa fa-angle-up"></i>
        </div>
        <!-- End scrollUp  -->

        
         <!-- modernizr js -->
        <script src="{site_url}skin/theme-three/assets/js/modernizr-2.8.3.min.js"></script>
        <!-- jquery latest version -->
        <script src="{site_url}skin/theme-three/assets/js/jquery.min.js"></script>
        <!-- Bootstrap v4.4.1 js -->
        <script src="{site_url}skin/theme-three/assets/js/bootstrap.min.js"></script>
        <!-- Menu js -->
        <script src="{site_url}skin/theme-three/assets/js/rsmenu-main.js"></script> 
        <!-- op nav js -->
        <script src="{site_url}skin/theme-three/assets/js/jquery.nav.js"></script>
        <!-- Time Circle js -->
        <script src="{site_url}skin/theme-three/assets/js/time-circle.js"></script>
        <!-- owl.carousel js -->
        <script src="{site_url}skin/theme-three/assets/js/owl.carousel.min.js"></script>
        <!-- wow js -->
        <script src="{site_url}skin/theme-three/assets/js/wow.min.js"></script>
        <!-- Skill bar js -->
        <script src="{site_url}skin/theme-three/assets/js/skill.bars.jquery.js"></script>
        <script src="{site_url}skin/theme-three/assets/js/jquery.counterup.min.js"></script> 
         <!-- counter top js -->
        <script src="{site_url}skin/theme-three/assets/js/waypoints.min.js"></script>
        <!-- swiper js -->
        <script src="{site_url}skin/theme-three/assets/js/swiper.min.js"></script>   
        <!-- particles js -->
        <script src="{site_url}skin/theme-three/assets/js/particles.min.js"></script>  
        <!-- magnific popup js -->
        <script src="{site_url}skin/theme-three/assets/js/jquery.magnific-popup.min.js"></script>      
        <script src="{site_url}skin/theme-three/assets/js/jquery.easypiechart.min.js"></script>      
        <!-- plugins js -->
        <script src="{site_url}skin/theme-three/assets/js/plugins.js"></script>
        <!-- pointer js -->
        <script src="{site_url}skin/theme-three/assets/js/pointer.js"></script>
        <!-- main js -->
        <script src="{site_url}skin/theme-three/assets/js/main.js"></script>
    </body>

</html>