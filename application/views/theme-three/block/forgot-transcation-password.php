

 <div class="login_section_mt">
  <div class="container">
  <div class="row">
      <div class="col-lg-7 col-12">
       <div class="login_form_left_side">
      </div>
      <div class="login_left_side">
      <img class="img-fluid" src="{site_url}skin/images/OTP.png" alt=""></div>
      </div>

  <div class="col-lg-5 col-12">
     <div class="login_logo">
     <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>"></a>     
     </div> 
 <div class="login_colm-frm help_u_forms">

    <?php echo form_open('forgot/transcationAuth'); ?>
  <div class="loginform_head">
      {system_message}               
          {system_info}
          
  <h3>Forgot T. Pin</h3> 
  </div>  
  <div class="login_colm-frm_col">
      <div class="form-group">
                      <input class="form-control" type="text" name="username" id="username" placeholder="Username">
                      <?php echo form_error('username', '<div class="error">', '</div>'); ?>  
                    </div>
      
       <div class="form-group">
     <!--  <a href="{site_url}login" class="forgot_password_icon">Go to Login</a> -->
      </div>
     <div class="form-group text-center">
      <button class="btn_submit" type="submit">Proceed</button>
      </div>
</div>  
      </div>  


         <?php echo form_close(); ?>

  </div></div>
</div>  
  </div>      