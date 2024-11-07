<?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
        $service = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array();  
    ?>
<footer class="main-footer">
        <div class="container">
            <div class="row">
                <div class="footer-detail">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="footer-link">
                            <h3>About Us</h3>
                            <p class="footer-about">
                                We Provide all online services like Mobile, DTH
                                and Data Card Recharges, All Bill Payment,
                                Travel Booking, Remittance / Money-transfers,
                                Aeps, Pancard,  White Label Recharge Websites
                                and Software, Recharge and DTH Direct Operator
                                API Provider and Many More.
                            </p>

                        </div>
                             <div class="top_header-pmr-social hidden-xs">
                        <ul class="social-nav-header">
                        <li><a href="<?php echo $contactDetail['facebook']; ?>" target="_blank" class="facebook"> <i class="fa fa-facebook"></i></a></li>
                        <li><a href="<?php echo $contactDetail['twitter']; ?>" target="_blank" class="twitter"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="<?php echo $contactDetail['linkedin']; ?>" target="_blank" class="linkedin"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="<?php echo $contactDetail['instagram']; ?>" target="_blank" class="linkedin"><i class="fa fa-instagram"></i></a></li>
                    </ul></div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                        <div class="footer-link">
                            <h3>Useful Links</h3>
                            <ul>
                                <li><a href="{site_url}"><i class="fa fa-angle-right" aria-hidden="true"></i> Home </a></li>
                                <li><a href="{site_url}about"><i class="fa fa-angle-right" aria-hidden="true"></i> About Us </a></li>
                                <li><a href="{site_url}contact"><i class="fa fa-angle-right" aria-hidden="true"></i> Contat Us </a></li>
                                <li><a href="{site_url}privacy"><i class="fa fa-angle-right" aria-hidden="true"></i> Privacy & Policy </a></li>
                                 <li><a href="{site_url}terms"><i class="fa fa-angle-right" aria-hidden="true"></i> Terms & Condition </a></li>

                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <div class="footer-link">
                            <h3>Services</h3>
                            <ul>
                                <?php
                                foreach($service as $list){
                                ?>
                                <li><a href="#"><i class="fa fa-angle-right" aria-hidden="true"></i><?php echo $list['title']; ?> </a></li>
                                <?php } ?>
                                
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <div class="footer-link">
                            <h3>Contact Us</h3>
                            <ul>
                                <li><i class="fa fa-location-arrow"></i>  <p><?php echo $contactDetail['address']; ?></p> </li>
                                <li><a href="#"><i class="fa fa-envelope"></i> <?php echo $contactDetail['email']; ?> </a></li>
                                <li><a href="#"> <i class="fa fa-phone"></i> +91-<?php echo $contactDetail['mobile']; ?></a></li>
                             </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- End Main footer -->
    <!-- Start Copyright -->
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12">

                    <p>Copyright © 2021 <?php echo $accountData['title']; ?> | All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- End Copyright -->


    <a href="#" id="scroll-to-top" title="Scroll to top"><i class="fa fa-arrow-up"></i></a>

    <!-- ALL JS FILES -->
    <script src="js/jquery.min.js"></script>



    
    <!-- ALL PLUGINS -->
    <script src="{site_url}skin/front/js/modernizer.js"></script>
    <script src="{site_url}skin/front/js/all.js"></script>
    <script src="{site_url}skin/front/js/custom.js"></script>
    <script src="{site_url}skin/front/js/validator.min.js"></script>
    <script src="{site_url}skin/front/js/jquery.nice-select.min.js"></script>
    <script src="{site_url}skin/front/js/form-scripts.js"></script>

    <script src="{site_url}skin/front/js/bootstrap-dropdownhover.min.js"></script>
    <!-- Bootstrap Dropdown Hover JS -->
    <script>
        jQuery(document).on('click', '.mega-dropdown', function (e) {
            e.stopPropagation()
        })
    </script>
    <script type="text/javascript">
          // Nice Select JS
    $('select').niceSelect();
    
    // Tabs Single Page
    $('.tab ul.tabs').addClass('active').find('> li:eq(0)').addClass('current');
    $('.tab ul.tabs li a').on('click', function (g) {
        var tab = $(this).closest('.tab'), 
        index = $(this).closest('li').index();
        tab.find('ul.tabs > li').removeClass('current');
        $(this).closest('li').addClass('current');
        tab.find('.tab_content').find('div.tabs_item').not('div.tabs_item:eq(' + index + ')').slideUp();
        tab.find('.tab_content').find('div.tabs_item:eq(' + index + ')').slideDown();
        g.preventDefault();
    }); 
    </script>
</body>
</html>