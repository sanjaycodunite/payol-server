<!DOCTYPE html>
<html lang="en">
 <head>
        <meta charset="UTF-8">
        <meta name="keywords" content="">
        <meta name="author" content="">
        <meta name="description" content="">
        <!-- For IE -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- For Resposive Device -->
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- For Window Tab Color -->
        <!-- Chrome, Firefox OS and Opera -->
        <meta name="theme-color" content="#3114ab">
        <!-- Windows Phone -->
        <meta name="msapplication-navbutton-color" content="#3114ab">
        <!-- iOS Safari -->
        <meta name="apple-mobile-web-app-status-bar-style" content="#3114ab">
        <title><?php  echo $accountData['title']?></title>
        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="56x56" href="{site_url}skin/main-front/images/fav-icon/icon.png">
        <!-- Main style sheet -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/main-front/css/style.css">
            <!-- style -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/main-front/css/flaticon.css">
        <!-- responsive style sheet -->
        <link rel="stylesheet" type="text/css" href="{site_url}skin/main-front/css/responsive.css">

        
    </head>

    <body>
        <div class="main-page-wrapper">

            <!-- ===================================================
                Loading Transition
            ==================================================== -->
            <!-- Preloader -->
            <section>
                <div id="preloader">
                    <div id="ctn-preloader" class="ctn-preloader">
                        <div class="animation-preloader">
                            <div class="spinner"></div>
                            <div class="logo-loader">
                            <img src="{site_url}<?php echo $accountData['image_path']; ?>">    
                            </div>
                        </div>  
                    </div>
                </div>
            </section>


            <!-- 
            =============================================
                Theme Main Menu
            ============================================== 
            -->
             <?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
        $service = $this->db->get_where('website_service',array('account_id'=>$account_id))->result_array(); 
    ?>

            <div class="top_header">
            <div class="container">
            <div class="row">
            <div class="col-lg-6 col-md-6">
            <div class="top_header_left_ph">
            <ul>
            <li><a href="#"><i class="fa fa-phone"></i> +91-<?php echo $contactDetail['mobile']; ?> </a></li>
            <li><a href="#"><i class="fa fa-envelope"></i> <?php echo $contactDetail['email']; ?></a></li>    
            </ul>   
            </div>  
            </div>  
            <div class="col-lg-6 col-md-6">
            <div class="top_h_login_right">
            <ul>
            <li><a href="{site_url}login">Retailer Login</a></li>   
            <li><a href="{site_url}login">Distributor Login</a></li> 
            </ul>   
            </div>  
            </div>  
            </div>  
            </div>  
            </div>
            <div class="theme-main-menu theme-menu-two">
            <div class="container"> 
            <div class="row">
            <div class="col-lg-3 col-md-3"> 
                <div class="logo"><a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>" alt=""></a></div></div>
                <div class="col-lg-9 col-md-9">
                <nav id="mega-menu-holder" class="navbar navbar-expand-lg">
                    <div  class="ml-auto nav-container">
                        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="fa fa-bars"></i>
                        </button>
                       <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a href="{site_url}" class="nav-link">Home</a>
                                </li>
                                <li class="nav-item">
                                <a href="{site_url}about" class="nav-link">About Us</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{site_url}Services">Services</a>
                                   
                                </li>
                               
                                <li class="nav-item">
                                <a href="{site_url}Blog" class="nav-link">Blog</a>
                                </li>

                                <?php
                                    $get_pages = $this->db->get_where('front_pages',array('account_id'=>$account_id,'status'=>1))->result_array();

                                    if($get_pages){
                                        foreach($get_pages as $pList){
                                    ?>
                                    <li class="nav-item">
                                        <a href="{site_url}page/<?=$pList['page_slug']?>"><?=$pList['page_title']?></a>
                                    </li>

                                <?php } } ?>

                                <li class="nav-item">
                                <a href="{site_url}contact" class="nav-link">Contact Us</a>
                                </li>
                           </ul>
                       </div>
                    </div> <!-- /.container -->
                </nav> <!-- /#mega-menu-holder -->
            </div></div></div>
            </div> <!-- /.theme-main-menu -->


