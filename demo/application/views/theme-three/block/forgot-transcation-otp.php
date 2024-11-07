

 <div class="login_section_mt">
  <div class="container">
  <div class="row">
  <div class="m-auto col-lg-6 col-md-6">
  <div class="login_colm-frm help_u_forms">

    <?php echo form_open('forgot/transcationOtpAuth'); ?>
    <input type="hidden" name="encode_opt_code" value="{encode_opt_code}" />
  <div class="form_head">
  <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>"></a>
      {system_message}               
          {system_info}
          
  <h3>Otp Verification</h3> 
  </div>  
      <div class="form-group">
                      <input class="form-control" type="password" name="otp_code" id="otp_code" placeholder="OTP CODE">
                      <?php echo form_error('otp_code', '<div class="error">', '</div>'); ?>  
                    </div>
      
       <div class="form-group mt-4">
              <div class="remember-checkbox d-flex align-items-center justify-content-between">
                <div class="checkbox">
                  <a href="{site_url}forgot/resendOtpAuth/{encode_opt_code}">Resent Otp</a>
                </div>
                 <a href="{site_url}login">Go to Login</a>
              </div>
            </div> 
     <div class="form-group">
      <button class="btn_submit" type="submit">Submit</button>
      </div>

      </div>  


         <?php echo form_close(); ?>

  </div></div>
</div>  
  </div>      