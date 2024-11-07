<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- For Resposive Device -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>Purvey India</title>
    
      <link rel="shortcut icon" href="{site_url}skin/front/assets/images/logo/fav.png" />
    <meta name="title" content="">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Main style sheet -->
      <!-- responsive style sheet -->
    <link rel="stylesheet" type="text/css" href="{site_url}skin/front/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="{site_url}skin/front/css/style.css">
  
    
</head>

<body>
    <style type="text/css">
    .error{
     color: red; 
    }  
  </style>
<!-- page wrapper start -->

<div class="page-wrapper"> 
<input type="hidden" id="siteUrl" value="<?php echo base_url(); ?>">
  <div class="login_section">
    <div class="container">
       <div class="row mt-5">
        <div class="col-sm-12">
         {system_message}    
         {system_info} 
        </div>  
      </div>
      <div class="row">

      <div class="col-sm-12">
<style type="text/css">
.booking_view {
    position: absolute;
    top: 0%;
    left: 0;
    right: 0;
    width: 100%;
    text-align: center;
    z-index: 9;
    background: rgb(255, 255, 255, 0.7);
    height: 100%;
}
.loader1 {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
}
.loader1 span {
   vertical-align:middle;
   border-radius:100%;
   
   display:inline-block;
   width:20px;
   height:20px;
   margin:3px 2px;
   -webkit-animation:loader1 0.8s linear infinite alternate;
   animation:loader1 0.8s linear infinite alternate;
}
.loader1 span:nth-child(1) {
   -webkit-animation-delay:-1s;
   animation-delay:-1s;
  background:rgba(245, 103, 115,0.6);
}
.loader1 span:nth-child(2) {
   -webkit-animation-delay:-0.8s;
   animation-delay:-0.8s;
  background:rgba(245, 103, 115,0.8);
}
.loader1 span:nth-child(3) {
   -webkit-animation-delay:-0.26666s;
   animation-delay:-0.26666s;
  background:rgb(235 81 30);
}
.loader1 span:nth-child(4) {
   -webkit-animation-delay:-0.8s;
   animation-delay:-0.8s;
  background:rgba(245, 103, 115,0.8);
  
}
.loader1 span:nth-child(5) {
   -webkit-animation-delay:-1s;
   animation-delay:-1s;
  background:rgba(245, 103, 115,0.4);
}

@keyframes loader1 {
   from {transform: scale(0, 0);}
   to {transform: scale(1, 1);}
}
@-webkit-keyframes loader1 {
   from {-webkit-transform: scale(0, 0);}
   to {-webkit-transform: scale(1, 1);}
}
</style>
        <div class="card">
          <div class="booking_view" id="flight_view" style="display: none;">
              <div class="loader1">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
</div>
          </div>  
          <?php echo form_open_multipart('VerifyBeneDocument/uploadDocumentAuth', array('id' => 'documentVerifyForm'),array('method'=>'post')); ?>
          <div class="card-body">

          <div class="row">

            <input type="hidden" name="bene_id" value="{bene_id}">
            <div class="col-sm-12">
              <div class="form-group">
                <label><b><span style="font-size: 14px;">Document Type*</span></b></label><br>
                <label><input type="radio" name="document_type" value="PAN" checked="" onclick="checkUpload(this.value)">&nbsp;&nbsp;<span style="font-size: 14px;">Pancard</span></label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <label><input type="radio" name="document_type" value="AADHAAR" onclick="checkUpload(this.value)">&nbsp;&nbsp;<span style="font-size: 14px;">Aadhar Card</span></label>

              </div>  
            </div>


            <div class="col-sm-3">
             <div class="form-group">
              <label><b><span style="font-size: 14px;">Upload Bank Passbook Photo*</span></b></label>
              <input type="file" class="form-control" autocomplete="off" name="passbook" id="passbook">
              <?php echo form_error('passbook', '<div class="error">', '</div>'); ?>  
              </div> 
            </div>

            <div class="col-sm-3" id="pan_div">
              <div class="form-group">
              <label><b><span style="font-size: 14px;">Upload Pancard Image*</span></b></label>
              <input type="file" class="form-control" autocomplete="off" name="panimage" id="panimage">
              <?php echo form_error('panimage', '<div class="error">', '</div>'); ?>   
              </div>
            </div>

            <div class="col-sm-3"  id="aadhar_front_div" style="display: none;">
              <div class="form-group">
              <label><b><span style="font-size: 14px;">Upload Aadhar Front Image*</span></b></label>
              <input type="file" class="form-control" name="aadhar_front" id="aadhar_front">
              <?php echo form_error('aadhar_front', '<div class="error">', '</div>'); ?>  
              
              </div>
            </div>

            <div class="col-sm-3" id="aadhar_back_div" style="display: none;">
              <div class="form-group">
              <label><b><span style="font-size: 14px;">Upload Aadhar Back Image*</span></b></label>
              <input type="file" class="form-control" name="aadhar_back" id="aadhar_back">
              <?php echo form_error('aadhar_back', '<div class="error">', '</div>'); ?>  
              
              </div>
            </div>

          </div>  

            
          </div>

          <div class="card-footer">
           <button class="btn btn-primary" type="submit" style="background-color: #eb511e; border-color: #eb511e;">Proceed</button> 
          </div>


        </div>

      </div>  


      </div> 

    </div>

    <?php echo form_close(); ?>
    </div>
  </div>
</div>

<script src="{site_url}skin/front/js/jquery.2.2.3.min.js"></script>
    <script src="{site_url}skin/front/js/bootstrap.min.js"></script>
    <script src="{site_url}skin/front/js/jquery.appear.js"></script> 
    <script src="{site_url}skin/front/js/jquery.countTo.js"></script> 
    <script src="{site_url}skin/front/js/aos.js"></script>
  <script src="{site_url}skin/front/js/wow.min.js"></script>
    <!-- owl.carousel -->
  <script src="{site_url}skin/front/js/owl.carousel.min.js"></script>
  
    <script src="{site_url}skin/front/js/header.js"></script>
    
<script src="{site_url}skin/front/js/main.js"></script>
<script src="{site_url}skin/front/js/custom.js"></script>


<script type="text/javascript">
  
  function checkUpload(val){

  $("#pan_div").hide();
  $("#aadhar_front_div").hide();
  $("#aadhar_back_div").hide();

  if(val == 'PAN'){

    $("#pan_div").show();
  }
  else if(val == 'AADHAAR'){

    $("#aadhar_front_div").show();
      $("#aadhar_back_div").show();
  }
}  


$( "#documentVerifyForm" ).submit(function( event ) {
  
  $("#flight_view").show();

});

</script> 
   
</body>
</html> 