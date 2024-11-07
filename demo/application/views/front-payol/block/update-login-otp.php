<section class="login_pg-section">
            <div class="container">
                <div class="dia-about-content">
                    <div class="row">

                        <div class="m-auto col-lg-5 col-md-12 wow fadeFromLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                           <div class="login_form_colm">
                            <div class="row">
                              <div class="col-lg-12 col-md-12">  
                              <div class="login_head">
                             <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>">
                               </a>   
                               <h3>Login</h3>
                              </div> </div>
  <div class="col-lg-12 col-md-12">  
                            {system_message}               
          {system_info}
          <?php echo form_open('login/updatePassOTPAuth',array('class'=>'login_form')); ?>
          <input type="hidden" value="<?php echo $decode_otp_code;?>" name="decode_otp_code">
                                    <div class="form-group">
                    <label><b>OTP*</b></label>
                    <input type="text" class="form-control" name="otp_code" id="otp_code" autocomplete="off" placeholder="Enter OTP Code">
                    <?php echo form_error('otp_code', '<div class="error">', '</div>'); ?>  
                    </div>
                  

                              <div class="form-group">
                               <button class="hover-btn" type="submit" value="Submit"> Verify OTP</button>
                              </div> 



                            <?php echo form_close(); ?>
                          </div>

                          </div>
                           </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </section>
    <!-- End of About section -->  