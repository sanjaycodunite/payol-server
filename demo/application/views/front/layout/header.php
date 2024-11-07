<!doctype html>
<html lang="en">

<head>
    <!-- Basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Site Metas -->
    <?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
      
    ?>
    <title><?php echo $accountData['title']; ?></title>
    <meta name="title" content="#">
    <meta name="keywords" content="#">
    <meta name="description" content="#">
    <meta name="author" content="#">

    <meta name="language" content="english" />
    <meta name="robots" content="index, follow" />
    <meta name="robots" content="noodp, noydir" />
    <meta name="classification" content="Recharge api provider , money transfer api provider , bus booking api provider , flight booking api provider , hotel booking api provider , multi recharge solution , best recharge commission , recharge and booking solution" />
    <meta name="publisher" content="Pay My Recharge" />
    <link rel="canonical" href="{site_url}" />


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{site_url}skin/front/css/bootstrap.min.css">

    <!-- Site CSS -->
    <link rel="stylesheet" href="{site_url}skin/front/css/style.css">

    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{site_url}skin/front/css/responsive.css">

    <!-- RESPONSIVE.CSS ONLY FOR MOBILE AND TABLET VIEWS -->
    <link href="{site_url}skin/front/css/style-mob.css" rel="stylesheet" />
    <link href="{site_url}skin/front/css/nice-select.min.css" rel="stylesheet" />
    <!-- Modernizer -->


<!-- Google Analytics -->


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-113321364-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-113321364-1');
</script>


</head>

<body class="multiple">
    <!-- Start Preloader  -->
    <div id="preloader">
  <div class="spinner">
  <div class="inner one"></div>
  <div class="inner two"></div>
  <div class="inner three"></div>
  </div>
    </div>
    <!-- Start Preloader  -->
    
    
 <!--HEADER SECTION-->
    <section class="omega_head">
    <div class="top_area">
       <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="top_header-pmr">
                        <a class="nav-contant" href="#"><i class="fa fa-phone"></i> +91-<?php echo $contactDetail['mobile']; ?> </a></div></div>
                   <div class="col-lg-5 col-md-5">
                    <div class="top_head-btns">
                     <a href="{site_url}login">Retailer Login</a> 
                     <a href="{site_url}login">Distributor Login</a>
                    </div> 
                   </div>
                    <div class="col-lg-3 col-md-3 col-sm-4">
                         <div class="top_header-pmr-social hidden-xs">
                        <ul class="social-nav-header">
                        <li><a href="<?php echo $contactDetail['facebook']; ?>" target="_blank" class="facebook"> <i class="fa fa-facebook"></i></a></li>
                        <li><a href="<?php echo $contactDetail['twitter']; ?>" target="_blank" class="twitter"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="<?php echo $contactDetail['linkedin']; ?>" target="_blank" class="linkedin"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="<?php echo $contactDetail['instagram']; ?>" target="_blank" class="linkedin"><i class="fa fa-instagram"></i></a></li>
                    </ul></div>
                    </div>
                </div>
            </div>
    </div>

    <!-- LOGO AND MENU SECTION -->

    
    <!--END HEADER SECTION-->
    <!-- Start header  -->
    <header class="top-head top-logo" data-spy="affix" data-offset-top="250">
        <div class="sticky header">
            <div class="container">
                <div class="scrollbtn-nav row">
                    <nav class="navbar navbar-default" id="mainNav">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle x collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-animations"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                              <div class="logo"> <a class="navbar-brand" href="{site_url}"> <img src="{site_url}<?php echo $accountData['image_path']; ?>" alt="<?php echo $accountData['title']; ?>"/> </a> </div>  
                        </div>
                       
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-animations" data-hover="dropdown" data-animations="fadeInDown fadeInRight fadeInUp fadeInLeft">
                            <ul class="nav navbar-nav navbar-right">
                                <li class="active"><a href="{site_url}">Home</a></li>
                                <li><a href="{site_url}about">About Us</a></li>


                                <li class="dropdown mega-dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Services <i class="fa fa-chevron-down pull-right"></i></a>
                                    <ul class="dropdown-menu mega-dropdown-menu">
                                    <?php
                                    foreach($service as $list){
                                    ?>
                                        <li><a href="#"><?php echo$list['title']; ?></a></li>
                                    <?php } ?>
                                    </ul>

                                </li>
                                 <li class="dropdown mega-dropdown">
                                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Login with us <i class="fa fa-chevron-down pull-right"></i></a>
                                    <ul class="dropdown-menu mega-dropdown-menu">
                                      <li><a href="{site_url}login">Retailer Login</a></li>
                                      <li><a href="{site_url}login">Distributor Login</a></li>
                                      <li><a href="{site_url}login">Master Distributor Login</a></li>
                                      <li><a href="{site_url}login">Developer Console Login</a></li>  
                                </ul>

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

                                <li><a href="{site_url}contact">Contact</a></li>
                            </ul>

                        </div>
                        <!-- navbar-collapse -->
                    </nav>
                </div>
            </div>
        </div>
        <!-- container-fluid -->
    </header>
    <!-- End header  --></section>
    <!-- MOBILE MENU -->
    <!-- Start  bootstrap touch slider -->