<?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
      
    ?>

<!DOCTYPE html>
<html lang="zxx">
<head>

    <!-- META -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="<?php echo $accountData['title']; ?>">
    <meta name="author" content="<?php echo $accountData['title']; ?>">
    <meta name="robots" content="<?php echo $accountData['title']; ?>">    
    <meta name="description" content="<?php echo $accountData['title']; ?>">
    
    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
    
    <!-- PAGE TITLE HERE -->
    <title><?php echo $accountData['title']; ?> || Home</title>
    
    <!-- MOBILE SPECIFIC -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">   
    
    <!-- BOOTSTRAP STYLE SHEET -->
    <link  href="{site_url}skin/front-morningpay/css/bootstrap.min.css" rel="stylesheet">
    <!-- FONTAWESOME STYLE SHEET -->
    <link  href="{site_url}skin/front-morningpay/css/font-awesome.min.css" rel="stylesheet">
    <!-- Feather STYLE SHEET -->
    <link href="{site_url}skin/front-morningpay/css/feather.css" rel="stylesheet">
    <!-- FLATICON STYLE SHEET -->
    <link href="{site_url}skin/front-morningpay/css/flaticon.min.css" rel="stylesheet">
    <!-- WOW ANIMATE STYLE SHEET -->
    <link href="{site_url}skin/front-morningpay/css/animate.css" rel="stylesheet">
    <!-- OWL CAROUSEL STYLE SHEET -->
    <link href="{site_url}skin/front-morningpay/css/owl.carousel.min.css" rel="stylesheet">
    <!-- MAGNIFIC POPUP STYLE SHEET -->
    <link href="{site_url}skin/front-morningpay/css/magnific-popup.min.css" rel="stylesheet">
    <!-- DATE PICKER STYLE SHEET -->
    <link href="{site_url}skin/front-morningpay/css/date-picker.css" rel="stylesheet">     
    <!-- LC LIGHT BOX STYLE SHEET -->
    <link href="{site_url}skin/front-morningpay/css/lc_lightbox.css" rel="stylesheet">  
    <!-- MAIN STYLE SHEET -->
    <link href="{site_url}skin/front-morningpay/css/style.css" rel="stylesheet">

</head>

<body class="aon-body-bg aon-bg-light-red">
    
  <div class="page-wraper">
     
        <!-- HEADER START -->
        <header class="site-header header-style-2 mobile-sider-drawer-menu">
            <div class="top_header">
            <div class="container">
            <div class="row">
            <div class="col-lg-6 col-md-6">
             <div class="top_left_LG">
             <ul>
             <li><span><i class="flaticon-093-phone-call"></i></span>
                <a href="tel:<?php echo $accountData['mobile'] ?>" target="_blank">+91-<?php echo $accountData['mobile'] ?></a></li>    
                <li><span><i class="flaticon-095-mail"></i></span>
                <a href="mailto:<?php echo $accountData['email'] ?>" target="_blank"><?php echo $accountData['email'] ?></a></li>
             </ul>  
             </div> 
            </div>  
            <div class="col-lg-6 col-md-6">
                <ul class="top-social-icon-2">
                <li><a href="javascript:void(0);"><i class="feather-facebook"></i></a></li>
                <li><a href="javascript:void(0);"><i class="feather-twitter"></i></a></li>
                <li><a href="javascript:void(0);"><i class="feather-linkedin"></i></a></li>
                <li><a href="javascript:void(0);"><i class="feather-instagram"></i></a></li>
            </ul>
            </div>
            </div>  
            </div>  
            </div>
            <!--Top bar section End-->
            <div class="sticky-header main-bar-wraper  navbar-expand-lg">
                <div class="main-bar">  

                    <div class="container clearfix"> 
                        <!--Logo section start-->
                        <div class="logo-header">
                            <div class="logo-header-inner logo-header-one">
                                <a href="{site_url}">
                                    <img  src="{site_url}<?php echo $accountData['image_path'];?>" alt="">
                                </a>
                            </div>
                        </div>  
                        <!--Logo section End-->

                        <!-- NAV Toggle Button -->
                        <button id="mobile-side-drawer" data-target=".header-nav" data-toggle="collapse" type="button" class="navbar-toggler collapsed">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar icon-bar-first"></span>
                            <span class="icon-bar icon-bar-two"></span>
                            <span class="icon-bar icon-bar-three"></span>
                        </button> 

                        <!-- MAIN Nav -->
                        <div class="nav-animation header-nav navbar-collapse collapse d-flex justify-content-end">

                            <ul class=" nav navbar-nav">
                                 <li><a href="{site_url}">Home</a></li>
                                <li><a href="{site_url}about">About Us</a></li>
                                <li class="has-child"><a href="#">Services</a>
                                <ul class="sub-menu">
                                  <li><a href="{site_url}services">Banking Services</a></li>
                                 <li><a href="{site_url}tour">Tour & Travel</a></li>
                                  <li><a href="{site_url}bill-payment">Bill Payment</a></li>
                                  <li><a href="{site_url}insurance">Insurance</a></li>
                                   <!--<li><a href="{site_url}payment-gateway">Payment Gateway</a></li>-->
                                </ul>   
                                </li>
                                <li><a href="{site_url}contact">Contact Us</a></li>
                                <li><a href="{site_url}login">Login</a></li>
                            </ul>

                        </div>

                                                 
                    </div>    

                </div>
            </div>
        </header>
        <!-- HEADER END -->
    