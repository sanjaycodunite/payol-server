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
          <h2><b>Update Password</b></h2>
           {system_message}               
          {system_info}
          <?php echo form_open('forgot/passwordAuth',array('class'=>'login_form')); ?>
            <input type="hidden" name="encode_opt_code" value="{encode_opt_code}" />
            <div class="messages"></div>
            <div class="form-group">
                      <input class="form-control" type="password" name="new_password" id="new_password" placeholder="New Password">
                      <?php echo form_error('new_password', '<div class="error">', '</div>'); ?>  
                    </div>
                    <div class="form-group forget-block">
                      <input class="form-control" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                      <?php echo form_error('password', '<div class="error">', '</div>'); ?>  
                    </div>
                 
            <div class="form-group mt-4">
              <div class="remember-checkbox">
                <div class="checkbox">
                  
                  
                </div>
                 <a href="{site_url}login">Go to Login</a>
              </div>
            </div> 
             <div class="form-group_login">
            <button class="btn btn-primary btn-block" type="submit">Update Password</button>
             </div>
           
          <?php echo form_close(); ?>
          
                
        
        
        </div>
      </div>
    </div>
  </div>
</div>