

  <div class="bg-round-one wow zoomIn animated" data-wow-duration="5s"></div>
  <div class="bg-round-two wow zoomIn animated" data-wow-duration="5s"></div>
  <div class="login_section_mt">
  <div class="container">
  <div class="row">
  
  <div class="m-auto col-lg-6 col-md-6">
  <div class="login_colm-frm help_u_forms">
  <div class="inner-container">
  <div class="main-content">

   
  <div class="contact-form-wrapper">

    <?php echo form_open('login/auth'); ?>
  <div class="form_head">
  <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>"></a>
      {system_message}               
          {system_info}
          
  <h3> Login</h3> 
  </div>  
      <div class="form-group">
      <i class="fa fa-user"></i>
      <input class="form-control"  name ="username" id="username" type="text" placeholder="User ID">
      <?php echo form_error('username', '<div class="error">', '</div>'); ?>  
      </div>
      <div class="form-group">
      <i class="fa fa-lock"></i>
      <input class="form-control" name="password" id="password" type="password" placeholder="Password">
      <?php echo form_error('password', '<div class="error">', '</div>'); ?> 
      </div>
       <div class="form-group">
      <a href="{site_url}forgot" class="forgot_password_icon">Forgot Password</a>
      </div>
     <div class="form-group">
      <button class="btn_submit" type="submit">Submit</button>
      </div>

      </div>  


         <?php echo form_close(); ?>

  </div></div>
</div>  
  </div>  
  </div>  
  </div>  
  </div>      