

 <div class="login_section_mt">
  <div class="container">
  <div class="row">
  <div class="m-auto col-lg-6 col-md-6">
  <div class="login_colm-frm help_u_forms">

    <?php echo form_open('forgot/passwordAuth'); ?>
    <input type="hidden" name="encode_opt_code" value="{encode_opt_code}" />
  <div class="form_head">
  <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>"></a>
      {system_message}               
          {system_info}
          
  <h3>Update Password</h3> 
  </div>  
      <div class="form-group">
                      <input class="form-control" type="password" name="new_password" id="new_password" placeholder="New Password">
                      <?php echo form_error('new_password', '<div class="error">', '</div>'); ?>  
                    </div>
                    <div class="form-group forget-block">
                      <input class="form-control" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                      <?php echo form_error('password', '<div class="error">', '</div>'); ?>  
                    </div>
       <div class="form-group">
      <a href="{site_url}login">Go to Login</a>
      </div>
     <div class="form-group">
      <button class="btn_submit" type="submit">Submit</button>
      </div>

      </div>  


         <?php echo form_close(); ?>

  </div></div>
</div>  
  </div>      