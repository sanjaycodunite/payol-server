    <div class="login_marwar">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 col-12">
      
     
      <div class="login_form_left_side">
      </div>
      <div class="login_left_side">
      <img class="img-fluid" src="{site_url}skin/front/login/img/banner/graphic2.svg" alt=""></div>
      </div>
      <div class="col-lg-6 col-12">
        <div class="form-bar">
        <div class="form_logo">
           <a href="{site_url}"> 
        <img class="img-fluid" src="{site_url}<?php echo $accountData['image_path']; ?>" alt=""></a>
        </div>
          <h2><b>Otp Verification</b></h2>
           {system_message}               
          {system_info}
          <?php echo form_open('forgot/otpauth',array('class'=>'login_form')); ?>
            
            <div class="messages"></div>
            <input type="hidden" name="encode_opt_code" value="{encode_opt_code}" />
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
             <div class="form-group_login">
            <button class="btn btn-primary btn-block" type="submit">Verify</button>
             </div>
           
          <?php echo form_close(); ?>
          
                
        
        
        </div>
      </div>
    </div>
  </div>
</div> 