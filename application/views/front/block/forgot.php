<div class="login_header_section">
    <div class="container">
  <div class="row align-items-center">
     <div class="col-lg-3 col-md-3">
      <div class="Login_logo"><a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>" class="img-fluid"></a></div>   
     </div>
     <div class="col-lg-6 col-md-6">
      <div class="login_top_Details">
          <ul>
          <li><span>Helpline Number:</span> <i class="fa fa-phone"></i><a href="tel:8277998846" target="_blank">+91-8277998846</a></li>
         <li><i class="fa fa-envelope"></i><a href="mailto:payoldigital2023@gmail.com" target="_blank">support@payol.in</a></li>
         </ul></div></div>
         
          <div class="col-lg-3 col-md-3">
           <div class="login_social">
               <ul class="top-social-icon-2">
                <li><a href="javascript:void(0);"><i class="fa fa-facebook"></i></a></li>
                <li><a href="javascript:void(0);"><i class="fa fa-twitter"></i></a></li>
                <li><a href="javascript:void(0);"><i class="fa fa-linkedin"></i></a></li>
                <li><a href="javascript:void(0);"><i class="fa fa-instagram"></i></a></li>
            </ul>
           </div>      
          </div>
         
  </div></div>
</div>

<div class="login_section_mt">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7 col-12">
       <div class="login_form_left_side">
      </div>
      <div class="login_left_side">
      <img class="img-fluid" src="{site_url}skin/images/forgot.webp" alt=""></div>
      </div>
      <div class="col-lg-5 col-12">
           <div class="login_logo">
     <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>"></a>     
     </div> 
     
     <div class="login_colm-frm help_u_forms">
        <div class="loginform_head">
       
          <h3><b>Forgot Password</b></h3>
           {system_message}               
          {system_info}
           </div> 
           <div class="login_colm-frm_col">
          <?php echo form_open('forgot/auth',array('class'=>'login_form')); ?>
            
            <div class="messages"></div>
            <div class="form-group">
                 <i class="fa fa-user"></i>
                      <input class="form-control" type="text" name="username" id="username" placeholder="Username">
                      <?php echo form_error('username', '<div class="error">', '</div>'); ?>  
                    </div>
                 
            <div class="form-group mt-4">
              <div class="remember-checkbox">
                <div class="checkbox">
                 </div>
                 <a href="{site_url}login"  class="forgot_password_icon">Go to Login</a>
              </div>
            </div> 
             <div class="form-group text-center">
            <button class="btn_submit" type="submit">Proceed</button>
             </div>
           
          <?php echo form_close(); ?>
          
                </div>
        
        
        </div>
      </div>
    </div>
  </div>
</div>