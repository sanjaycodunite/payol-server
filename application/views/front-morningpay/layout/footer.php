<?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
        $service = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array();  
    ?>


<footer class="site-footer">
           <!-- FOOTER BLOCKES START -->  
            <div class="footer-top-2">
                <div class="container">
                    <div class="row wow fadeInDown" data-wow-duration="2000ms">
                        <!-- COLUMNS 1 -->
                        <div class="col-lg-3 col-md-6">
                         <div class="sf-widget-link f-margin">
                         <div class="aon-footer-logo-2"><img src="{site_url}<?php echo $accountData['image_path'];?>" alt="Image"></div>
                         <div class="footer_about">
                         <p><?php echo $accountData['title'];?> is driving India's fintech revolution with advanced payment solutions
and digital transactions. We aim to bridge the technology gap between rural and
urban areas, making financial services accessible to everyone. Join us in
changing India's financial landscape.Join us in transforming India's financial
landscape</p>    
                         </div>

                         <ul class="aon-social-icon-2 d-flex">
                                    <li><a href="javascript:void(0);"><i class="feather-facebook"></i></a></li>
                                    <li><a href="javascript:void(0);"><i class="feather-twitter"></i></a></li>
                                    <li><a href="javascript:void(0);"><i class="feather-linkedin"></i></a></li>
                                    <li><a href="javascript:void(0);"><i class="feather-instagram"></i></a></li>
                                </ul>
                          </div>
                        </div>
                        <!-- COLUMNS 2 -->
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="aon-widget-link f-margin">
                                <h4 class="aon-f-title-2">Quick links</h4>
                                <ul class="aon-widget-foo-list-2">
                                    <li><a href="{site_url}">About Us</a></li>
                                    <li><a href="#">Services</a></li>
                                    <li><a href="{site_url}contact">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- COLUMNS 3 -->
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="aon-widget-link f-margin">
                                <h4 class="aon-f-title-2">Important links</h4>
                                <ul class="aon-widget-foo-list-2">
                                    <li><a href="{site_url}login">Login</a></li>
                                    <li><a href="{site_url}terms">Terms & Conditions</a></li>
                                    <li><a href="{site_url}privacy">Privacy Policy</a></li>
                                    <li><a href="{site_url}refund">Refund Policy</a></li>
                                    <li><a href="#">Sitemap</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- COLUMNS 4 -->
                        <div class="col-lg-3 col-md-6">
                            <div class="aon-ftr-info-wrap  f-margin">
                                <h4 class="aon-f-title-2">Contact Us</h4>
                                <ul class="aon-ftr-info">
                                    <li>
                                    <div class="footer_list_icons"><i class="flaticon-093-phone-call"></i></div>
                                     <div class="footer_list_text">
                                    <h4 class="aon-f-title-2">Phone</h4>
                                    <a href="tel:+91-<?php echo $contactDetail['mobile'] ?>" target="_blank">+91-<?php echo $contactDetail['mobile'] ?></a></div>   
                                    </li>
                                    <li>
                                     <div class="footer_list_icons"><i class="flaticon-095-mail"></i></div>
                                     <div class="footer_list_text">
                                     <h4 class="aon-f-title-2">Email</h4>
                                     <a href="mailto:<?php echo $accountData['email'] ?>" target="_blank"><?php echo $accountData['email'] ?></a></div>
                                    </li>
                                    <li>
                                     <div class="footer_list_icons"><i class="flaticon-015-location"></i></div>
                                     <div class="footer_list_text">
                                     <h4 class="aon-f-title-2">Location</h4>
                                     <p><?php echo $contactDetail['address']; ?></p></div>
                                    </li>
                                 </ul>

                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER COPYRIGHT -->
            <div class="footer-bottom">
                <div class="container">
                    <div class="aon-footer-bottom-area-2 wow fadeInUp" data-wow-duration="2000ms">
                        <div class="aon-foo-copyright-2 text-center">
                            © 2024 <?php echo $accountData['title']; ?>. All right reserved. 
                        </div>
                            
                    </div>
                </div>   
            </div>

            <!-- Footer Vverlay -->
            <div class="footer-overlay"></div>

        </footer>
        <!-- FOOTER END -->     
        
        <!-- BUTTON TOP START -->
        <button class="scroltop"><span class="fa fa-angle-up  relative" id="btn-vibrate"></span></button>   
                

    </div>

        </div>
    
<!-- JAVASCRIPT  FILES ========================================= --> 
<script  src="{site_url}skin/front-morningpay/js/jquery-3.6.1.min.js"></script><!-- JQUERY.MIN JS -->
<script  src="{site_url}skin/front-morningpay/js/popper.min.js"></script><!-- POPPER.MIN JS -->
<script  src="{site_url}skin/front-morningpay/js/bootstrap.min.js"></script><!-- BOOTSTRAP.MIN JS -->
<script  src="{site_url}skin/front-morningpay/js/wow.js"></script><!-- WOW JS -->
<script  src="{site_url}skin/front-morningpay/js/jquery.bootstrap-touchspin.js"></script><!-- FORM JS -->
<script  src="{site_url}skin/front-morningpay/js/magnific-popup.min.js"></script><!-- MAGNIFIC-POPUP JS -->
<script  src="{site_url}skin/front-morningpay/js/isotope.pkgd.min.js"></script><!-- isotope-pkgd JS --> 
<script  src="{site_url}skin/front-morningpay/js/imagesloaded.pkgd.js"></script><!-- isotope-pkgd JS -->        
<script  src="{site_url}skin/front-morningpay/js/waypoints.min.js"></script><!-- WAYPOINTS JS -->
<script  src="{site_url}skin/front-morningpay/js/counterup.min.js"></script><!-- COUNTERUP JS -->
<script  src="{site_url}skin/front-morningpay/js/waypoints-sticky.min.js"></script><!-- STICKY HEADER -->
<script  src="{site_url}skin/front-morningpay/js/owl.carousel.min.js"></script><!-- OWL  SLIDER  -->
<script  src="{site_url}skin/front-morningpay/js/theia-sticky-sidebar.js"></script><!-- STICKY SIDEBAR  -->
<script  src="{site_url}skin/front-morningpay/js/datepicker.min.js"></script><!-- DATE PICKER  -->      
<script  src="{site_url}skin/front-morningpay/js/lc_lightbox.lite.js" ></script><!-- IMAGE POPUP -->
<script  src="{site_url}skin/front-morningpay/js/custom.js"></script><!-- CUSTOM FUCTIONS  -->

    
</body>
</html>





