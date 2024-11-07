
<div class="login_marwar">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 col-12">
      
     
      <div class="login_form_left_side">
      <h4>Simple, secure & Fast Platform Now Login to access Your Panel</h4>
      </div>
      <div class="login_left_side">
      <img class="img-fluid" src="{site_url}skin/front/login/img/banner/graphic2.svg" alt=""></div>
      </div>
      <div class="col-lg-6 col-12">
        <div class="form-bar">
        <div class="form_logo">
           <a href="{site_url}"> 
        <img class="img-fluid" src="{site_url}<?php echo $accountData['image_path']; ?>" alt=""></a>
        <br>
        <h2><b>Verify OTP</b></h2 >
        </div>
           {system_message}               
          {system_info}
          <?php echo form_open('login/otpAuth'); ?>
            
            <div class="messages"></div>
                    <input type="hidden" name="encoded_otp_code" value="{encoded_otp_code}">
                    <div class="form-group forget-block">
                      <input class="form-control" type="password" name="otp_code" id="otp_code" placeholder="OTP Code">
                      <?php echo form_error('otp_code', '<div class="error">', '</div>'); ?>  
                    </div>
                 
            <div class="form-group mt-4">
              <div class="remember-checkbox d-flex align-items-center justify-content-between">
                <div class="checkbox">
                  <input type="checkbox" id="check2" name="check2">
                  <label for="check2">Remember me</label>
                </div>
                 <a href="#">Forgot Password?</a>
              </div>
            </div> 
             <div class="form-group_login">
            <button class="btn btn-primary btn-block" type="submit">Login Now</button>
             </div>
           
          <?php echo form_close(); ?>
          
                
        
        
        </div>
      </div>
    </div>
  </div>
</div>