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
        
      
    ?>
  <title><?php echo $accountData['title']; ?></title>

  <!-- Custom fonts for this template-->
  <link href="{site_url}skin/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{site_url}skin/admin/css/sb-admin-2.css" rel="stylesheet">
  <link href="{site_url}skin/admin/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{site_url}skin/admin/vendor/datatables/buttons.dataTables.min.css">
  <link href="{site_url}skin/admin/css/jquery.datetimepicker.css" rel="stylesheet">
  <link href="{site_url}skin/admin/css/bootstrap-select.css" rel="stylesheet">
  
 <link href="{site_url}skin/admin/css/new-theme.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body id="page-top">
<input type="hidden" id="siteUrl" value="<?php echo base_url(); ?>" />
