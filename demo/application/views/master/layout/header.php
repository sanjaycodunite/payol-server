<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="{meta_description}">
  <meta name="author" content="">

  <?php
    $account_id = $this->User->get_domain_account();
    $accountData = $this->User->get_account_data($account_id);
    $get_theme = $this->db->get_where('theme',array('account_id'=>$account_id))->row_array();
  ?>
  <title><?php echo $accountData['title']; ?></title>

  <!-- Favicon Icon -->
  <link rel="shortcut icon" href="{site_url}skin/front/assets/images/logo/marwarcare icon.png" />
  <!-- Custom fonts for this template-->
  <link href="{site_url}skin/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{site_url}skin/admin/css/sb-admin-2.css" rel="stylesheet">
  
  <link href="{site_url}skin/admin/css/theme1.css" rel="stylesheet">
  
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
  <link href="{site_url}skin/admin/css/flaticon.css" rel="stylesheet">
  
  <link href="{site_url}skin/admin/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{site_url}skin/admin/vendor/datatables/buttons.dataTables.min.css">
  <link href="{site_url}skin/admin/css/jquery.datetimepicker.css" rel="stylesheet">
  <link href="{site_url}skin/admin/css/bootstrap-select.css" rel="stylesheet">

  <?php
  if($accountData['panel_theme_id'] == 1){
 ?> 
 <link href="{site_url}skin/admin/css/new-theme.css" rel="stylesheet">

 <?php } ?>
    

  <?php if(isset($content_block) && ($content_block == 'member/viewTree')){ ?>
  <link href="{site_url}skin/admin/css/hierarchy-view.css" rel="stylesheet">
  <?php } ?>


</head>

<body id="page-top">
<input type="hidden" id="siteUrl" value="<?php echo base_url(); ?>" />
<div class="large-loader">
  <img src="{site_url}skin/admin/images/large-loading.gif">
</div>


<div class="upi_loader">
  <div class="loader_box">
        <div class="load-wrapp">
      <div class="load-3">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
      </div>
    </div>
  </div> 
</div>


<div class="upi_request_loader">
  <div class="loader_box">
        <div class="load-wrapp">
      <div class="load-3">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
      </div>
    </div>
     <p style="font-size:15px;"><b>Request sent to your VPA ID Please accept.</b></p>
     <p><b>Please do not press the back button or referesh the page.</b></p>
  </div> 
</div>
