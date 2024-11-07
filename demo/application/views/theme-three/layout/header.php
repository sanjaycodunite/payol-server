<!DOCTYPE html>
<html lang="zxx">  
    
<head>
        <!-- meta tag -->
        <meta charset="utf-8">
        <title><?php  echo $accountData['title']?></title>
        <meta name="description" content="">
        <!-- responsive tag -->
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- favicon -->
        <link rel="apple-touch-icon" href="apple-touch-icon.html">
        <link rel="shortcut icon" type="image/x-icon" href="assets/images/fav.png">
        
        <!-- Bootstrap v4.4.1 css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/css/bootstrap.min.css">
        <!-- font-awesome css -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
        <!-- flaticon css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/fonts/flaticon.css">
        <!-- animate css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/css/animate.css">
        <!-- owl.carousel css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/css/owl.carousel.css">
        <!-- slick css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/css/slick.css">
        <!-- off canvas css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/css/off-canvas.css">
        <!-- magnific popup css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/css/magnific-popup.css">
        <!-- Main Menu css -->
        <link rel="stylesheet" href="{site_url}skin/theme-three/assets/css/rsmenu-main.css">
        <!-- spacing css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/css/rs-spacing.css">
        <!-- style css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/css/style.css"> <!-- This stylesheet dynamically changed from style.less -->
        <!-- responsive css -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/theme-three/assets/css/responsive.css">
       
    </head>
    <body class="defult-home">
        <input type="hidden" id="siteUrl" value="<?php echo base_url(); ?>">
        <!--Preloader area start here-->
        <div id="loader" class="loader">
            <div class="loader-container"></div>
        </div>
        <!--Preloader area End here--> 

        <?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
        $service = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array(); 
        ?>
     
        <!-- Main content Start -->
        <div class="main-content">
            
            <!--Full width header Start-->
            <div class="full-width-header">
                <!--Header Start-->
                <header id="rs-header" class="rs-header style2 btn-style">
                    <!-- Topbar Area Start -->
                    <div class="topbar-area style2">
                       <div class="container">
                           <div class="row y-middle">
                               <div class="col-lg-8">
                                   <ul class="topbar-contact">
                                       <li>
                                        <i class="flaticon-call"></i>
                                           <a href="tel:+91-0122456789"> +91-<?php echo $contactDetail['mobile']; ?></a>
                                          
                                       </li>
                                       <li>
                                            <i class="flaticon-email"></i>
                                           <a href="#"><?php echo $contactDetail['email']; ?></a>
                                       </li>
                                      
                                   </ul>
                               </div>
                               <div class="col-lg-4 text-right">
                                   <div class="toolbar-sl-share">
                                       <ul>
                                           
                                            <li><a href="<?php echo $contactDetail['facebook']; ?>" target="_blank" class="facebook"> <i class="fa fa-facebook"></i></a></li>
                                            <li><a href="<?php echo $contactDetail['twitter']; ?>" target="_blank" class="twitter"><i class="fa fa-twitter"></i></a></li>
                                            <li><a href="<?php echo $contactDetail['linkedin']; ?>" target="_blank" class="linkedin"><i class="fa fa-linkedin"></i></a></li>
                                            <li><a href="<?php echo $contactDetail['instagram']; ?>" target="_blank" class="linkedin"><i class="fa fa-instagram"></i></a></li>
                                       </ul>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
                    <!-- Topbar Area End -->
                    <!-- Menu Start -->
                    <div class="menu-area menu-sticky">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-3">
                                    <div class="logo-part">
                                        <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>" alt=""></a>
                                    </div>
                                    <div class="mobile-menu">
                                        <a href="#" class="rs-menu-toggle rs-menu-toggle-close secondary">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-9 text-right">
                                    <div class="rs-menu-area">
                                        <div class="main-menu">
                                            <nav class="rs-menu md-pr-0">
                                               <ul class="nav-menu">
                                                    <li> <a href="{site_url}">Home</a></li>
                                                   <li>
                                                       <a href="{site_url}about">About</a>
                                                   </li>
                                                   <li>
                                                       <a href="{site_url}services">Services</a>
                                                   </li>

                                                   <?php

                                                    $get_pages = $this->db->get_where('front_pages',array('account_id'=>$account_id,'status'=>1))->result_array();

                                                    if($get_pages){

                                                      foreach($get_pages as $pList){
                                                   ?>

                                                    <li>
                                                      <a href="{site_url}page/<?=$pList['page_slug']?>"><?=$pList['page_title']?></a>
                                                   </li>

                                                   <?php } } ?>
                                                   <li>
                                                      <a href="{site_url}contact">Contact</a>
                                                   </li>
                                                   
                                <li class="quote-btn"><a href="{site_url}login">Login</a></li>
                                <?php if($account_id == 2) { ?>
                                <li class="quote-btn"><a href="{site_url}app/morningpay.apk" target="_blank" class="app_btn_download"><div><i class="fa fa-play"></i></div> <div><h4><span>Download On The</span> App</h4></div></a></li>
                                
                                     <?php } elseif($account_id == 3) { ?>
                                            <li class="quote-btn"><a href="{site_url}app/purveyindia.apk" target="_blank" class="app_btn_download"><div><i class="fa fa-play"></i></div> <div><h4><span>Download On The</span> App</h4></div></a></li>
                                            
                                            <?php } ?>
                                               </ul> <!-- //.nav-menu -->
                                            </nav>                                         
                                        </div> <!-- //.main-menu -->
                                      <!--  <div class="expand-btn-inner search-icon hidden-md">
                                            <ul>
                                              <li><a class="quote-btn" href="{site_url}login">Login</a></li>
                                            
                                            </ul>
                                        </div>-->                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Menu End --> 
                </header>
                <!--Header End-->
                
            </div>
            <!--Full width header End-->


