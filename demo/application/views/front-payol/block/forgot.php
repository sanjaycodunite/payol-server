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
          <h2><b>Forgot Password</b></h2>
           {system_message}               
          {system_info}
          <?php echo form_open('forgot/auth',array('class'=>'login_form')); ?>
            
            <div class="messages"></div>
            <div class="form-group">
                      <input class="form-control" type="text" name="username" id="username" placeholder="Username">
                      <?php echo form_error('username', '<div class="error">', '</div>'); ?>  
                    </div>
                 
            <div class="form-group mt-4">
              <div class="remember-checkbox">
                <div class="checkbox">
                  
                  
                </div>
                 <a href="{site_url}login">Go to Login</a>
              </div>
            </div> 
             <div class="form-group_login">
            <button class="btn btn-primary btn-block" type="submit">Proceed</button>
             </div>
           
          <?php echo form_close(); ?>
          
                
        
        
        </div>
      </div>
    </div>
  </div>
</div>