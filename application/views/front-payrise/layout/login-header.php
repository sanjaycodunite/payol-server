<!doctype html>
<html class="no-js" lang=""> 
<head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo $accountData['title']; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
    
    <!-- Google Fonts
    ============================================ -->    
        <link href='https://fonts.googleapis.com/css?family=Lato:400,400italic,900,700,700italic,300' rel='stylesheet' type='text/css'>
     
    <!-- Bootstrap CSS
    ============================================ -->    
        <link rel="stylesheet" href="{site_url}skin/front/login/css/bootstrap.min.css">
        
        
        
    <!-- Fontawsome CSS
    ============================================ -->
        <link rel="stylesheet" href="{site_url}skin/front/login/css/font-awesome.min.css">
        
    <!-- jquery-ui CSS
    ============================================ -->
        <link rel="stylesheet" href="{site_url}skin/front/login/css/jquery-ui.css">
        
        
    <!-- animate CSS
    ============================================ -->
        <link rel="stylesheet" href="{site_url}skin/front/login/css/animate.css">
    
    <?php
    $get_theme = $this->db->get_where('theme',array('account_id'=>$account_id))->row_array();
    ?>    
    <!-- style CSS
    ============================================ -->
        <link rel="stylesheet" href="{site_url}skin/front/login/css/style.css">
       <?php
       if($get_theme['theme_id'] == 1){ 
      ?>

      <link href="{site_url}skin/admin/css/dark-black.css" rel="stylesheet">

      <?php } elseif($get_theme['theme_id'] == 2){ ?>

      <link href="{site_url}skin/admin/css/dark-blue.css" rel="stylesheet">
      
      <?php } elseif($get_theme['theme_id'] == 3){ ?>

      <link href="{site_url}skin/admin/css/dark-red.css" rel="stylesheet">

      <?php } elseif($get_theme['theme_id'] == 4){ ?>

      <link href="{site_url}skin/admin/css/gray.css" rel="stylesheet">

      <?php } elseif($get_theme['theme_id'] == 5){ ?>

      <link href="{site_url}skin/admin/css/red.css" rel="stylesheet">

      <?php } elseif($get_theme['theme_id'] == 6){ ?>

      <link href="{site_url}skin/admin/css/blue.css" rel="stylesheet">

      <?php } ?>
    <!-- responsive CSS
    ============================================ -->
        <link rel="stylesheet" href="{site_url}skin/front/login/css/responsive.css">
        
    <!-- modernizr JS
    ============================================ -->    
        <script src="{site_url}skin/front/login/js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
      <input type="hidden" id="siteUrl" value="<?php echo base_url(); ?>">