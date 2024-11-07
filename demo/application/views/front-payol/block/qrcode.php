<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Qr Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="assets/img/fv.png" type="image/x-icon">
    <link rel="icon" href="assets/img/fv.png" type="image/x-icon">
    <link rel="stylesheet" href="{site_url}skin/front/css/bootstrap.min.css">
    <link rel="stylesheet" href="{site_url}skin/front/css/fontawesome-all.css">
    
</head>
<body>        
        <section class="qr-section">
            <div class="container">

                <div class="dia-about-content" style="margin-top: 20px; margin-bottom: 20px;">
                    <div class="row">

                        <div class="col-lg-offset-2 col-lg-8 col-md-12 wow fadeFromLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                           <div class="qr_form_colm">
                            <div class="row">
                              <div class="col-lg-12 col-md-12 qr-scan">  
                              <div class="login_head"><img src="{site_url}<?php echo $accountData['image_path']; ?>" class="img-fluid">
                              </div>

                              <div class="scan_code">
                               <img src="<?php echo $qr_url; ?>" id="qr_code_d" class="img-fluid" width="500"> 
                               <p>Scan & Pay with any UPI App</p>
                              </div>

                              <div class="payment-method">
                              <ul>
                               <li><img src="{site_url}skin/qr-img/phonepe.png" class="img-fluid"></li> 
                               <li><img src="{site_url}skin/qr-img/gpay.png" class="img-fluid"></li> 
                               <li><img src="{site_url}skin/qr-img/amazon-pay.png" class="img-fluid"></li> 
                               <li><img src="{site_url}skin/qr-img/UPI.png" class="img-fluid"></li> 
                               <li><img src="{site_url}skin/qr-img/paytm.png" class="img-fluid"></li> 
                               <li><img src="{site_url}skin/qr-img/Bhim-Logo.png" class="img-fluid"></li> 
                              </ul>
                              </div>

                          </div>
                           </div>
                        </div>
                        
                    </div>
                </div>
            </div>


        </section>
    <!-- End of About section --> 

    </div> 


<style type="text/css">
@media screen and (max-width: 767px) {
  .scan_code img {
    width: 100%;
}
.payment-method ul li {
    list-style: none;
    display: inline-block;
    width: 60px !important;
    margin: 4px 16px !important;
}
.login_head img {width: 100%;}

}
  .qr_form_colm {padding: 5px 0px 15px;
    background: #fff;    position: relative;
}
.qr-scan {
    padding: 10px 50px;
}
.scan_code {
    position: relative;
    z-index: 99;    text-align: center;
}
.payment-method ul li img {
    width: 100%;
}
.login_head {
    text-align: center;
}
.payment-method {
    position: relative;
    z-index: 99;
}
.scan_code p {
    font-size: 25px;
    font-weight: 900;text-align: center;
}
.payment-method ul {
    margin: 0px;
    padding: 0px;
}
.payment-method ul li {
    list-style: none;
    display: inline-block;
    width: 140px;
    margin: 4px 38px;
}
.qr_form_colm:before {
    content: "";
    background-image: url({site_url}skin/qr-img/border.png);
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    background-size: 100%;
    z-index: 1;
    background-repeat: no-repeat;
}
.qr_form_colm:after{
    content: "";
    background-image: url({site_url}skin/qr-img/border2.png);
    width: 100%;
    height: 100%;
    position: absolute;
    bottom: 0;right: 0px;
    background-size: 100%;background-position: bottom;
    z-index: 1;
    background-repeat: no-repeat;
}
</style>

   </body>
</html>