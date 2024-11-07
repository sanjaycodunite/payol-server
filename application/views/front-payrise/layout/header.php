<?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
      
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $accountData['title']; ?> || Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{site_url}skin/front-payrise/assets/img/fv.png" type="image/x-icon">
    <link rel="icon" href="{site_url}skin/front-payrise/assets/img/fv.png" type="image/x-icon">
    <link rel="stylesheet" href="{site_url}skin/front-payrise/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{site_url}skin/front-payrise/assets/css/fontawesome-all.css">
    <link rel="stylesheet" href="{site_url}skin/front-payrise/assets/css/animate.css">
    <link rel="stylesheet" href="{site_url}skin/front-payrise/assets/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="{site_url}skin/front-payrise/assets/css/odometer-theme-default.css">
    <link rel="stylesheet" href="{site_url}skin/front-payrise/assets/css/owl.carousel.css">
    <link rel="stylesheet" href="{site_url}skin/front-payrise/assets/css/style.css">
</head>
<body class="dia-home">

<div class="loader-bg" id="preloader">
  <div class="loader-p"  id="ctn-preloader">
  <img src="{site_url}<?php echo $accountData['image_path'];?>">   
  </div>
</div>
    
    <!-- Start of header section
        ============================================= -->
        <div class="header-top">
        <div class="container">
        <div class="row">
        <div class="col-lg-6 col-md-6">
        <div class="top_header-area">
         <ul>
          <li><a href="#"><i class="fa fa-mobile-alt"></i> +91-<?php echo $accountData['mobile']; ?></a></li>
          <li><a href="#"><i class="fa fa-envelope-open"></i> <?php echo $accountData['mobile']; ?></a></li>   
         </ul>   
        </div>    
        </div> 
       
       <div class="col-lg-6 col-md-6">
        <div class="top_header-social">
         <ul>
          <li><a href="#"><i class="fab fa-facebook-f "></i></a></li>
          <li><a href="#"><i class="fab fa-twitter"></i></a></li> 
          <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
          <li><a href="#"><i class="fab fa-youtube"></i></a></li>   
         </ul>   
        </div>    
        </div> 

        </div>    
        </div>    
        </div>
        <header id="dia-header" class="dia-main-header">
            <div class="container">
                <div class="dia-main-header-content clearfix">
                    <div class="dia-logo float-left">
                        <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path'];?>" alt=""></a>
                    </div>
                    <div class="dia-main-menu-item float-right">
                        <nav class="dia-main-navigation  clearfix ul-li">
                            <ul id="main-nav" class="navbar-nav text-capitalize clearfix">
                              <li> <a class="active" href="{site_url}">Home</a></li>
                              <li><a href="{site_url}about">About Us</a></li>
                               <li><a href="{site_url}service">Service</a> </li>
                              <li><a href="{site_url}contact">Contact</a> </li>

                              <li class="nav_header_btn"><a href="{site_url}login">Login</a> </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- /desktop menu -->
                <div class="dia-mobile_menu relative-position">
                    <div class="dia-mobile_menu_button dia-open_mobile_menu">
                        <i class="fas fa-bars"></i>
                    </div>
                    <div class="dia-mobile_menu_wrap">
                        <div class="mobile_menu_overlay dia-open_mobile_menu"></div>
                        <div class="dia-mobile_menu_content">
                            <div class="dia-mobile_menu_close dia-open_mobile_menu">
                                <i class="far fa-times-circle"></i>
                            </div>
                            <div class="m-brand-logo text-center">
                                <a href="{site_url}"><img src="{site_url}skin/front-payrise/assets/img/d-agency/logo/logo.png" alt=""></a>
                            </div>
                            <nav class="dia-mobile-main-navigation  clearfix ul-li">
                             <ul id="m-main-nav" class="navbar-nav text-capitalize clearfix">
                                <li> <a class="active" href="{site_url}">Home</a></li>
                              <li><a href="{site_url}about">About Us</a></li>
                               <li><a href="{site_url}services">Service</a> </li>
                              <li><a href="{site_url}contact">Contact</a> </li>
                              <li class="nav_header_btn"><a href="{site_url}login">Login</a> </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- /mobile-menu -->
            </div>
        </div>
    </header>
    <!-- End of header section
        ============================================= -->