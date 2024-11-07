

 <div class="login_section_mt">
  <div class="container">
  <div class="row">
  <div class="m-auto col-lg-6 col-md-6">
  <div class="login_colm-frm help_u_forms">

    <?php echo form_open('forgot/transcationAuth'); ?>
  <div class="form_head">
  <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>"></a>
      {system_message}               
          {system_info}
          
  <h3>Forgot Password</h3> 
  </div>  
      <div class="form-group">
                      <input class="form-control" type="text" name="username" id="username" placeholder="Username">
                      <?php echo form_error('username', '<div class="error">', '</div>'); ?>  
                    </div>
      
       <div class="form-group">
     <!--  <a href="{site_url}login" class="forgot_password_icon">Go to Login</a> -->
      </div>
     <div class="form-group">
      <button class="btn_submit" type="submit">Proceed</button>
      </div>

      </div>  


         <?php echo form_close(); ?>

  </div></div>
</div>  
  </div>      