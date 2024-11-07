        <section class="qr-section">
            <div class="container">

                <div class="row">
                  <div class="col-sm-12">
                    {system_message}    
                    {system_info}
                  </div>
                </div>

                <div class="dia-about-content" id="contenttt">
                    <div class="row">

                        <div class="m-auto col-lg-5 col-md-12 wow fadeFromLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                           <div class="qr_form_colm">
                            <div class="row">
                              <div class="col-lg-12 col-md-12 qr-scan">  
                              <div class="login_head"><img src="{site_url}<?php echo $accountData['image_path']; ?>" class="img-fluid">
                              </div>

                              <div class="scan_code">
                               <img src="{qr}" id="qr_code_d" class="img-fluid"> 
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
  .qr_form_colm {padding: 5px 0px 15px;
    background: #fff;    position: relative;
}
.qr-scan {
    padding: 10px 50px;
}
.scan_code {
    position: relative;
    z-index: 99;
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
    width: 100px;
    margin: 0 8px;
}
.qr_form_colm:before {
    content: "";
    background-image: url(../../skin/qr-img/border.png);
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
    background-image: url(../../skin/qr-img/border2.png);
    width: 100%;
    height: 100%;
    position: absolute;
    bottom: 0;right: 0px;
    background-size: 100%;background-position: bottom;
    z-index: 1;
    background-repeat: no-repeat;
}
</style>
