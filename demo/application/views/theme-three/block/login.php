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
  <div class="row">
       <div class="col-lg-7 col-md-6">
            <div class="owl-carousel aon-bnr1-carousal">
            <div class="item">
   <div class="login_lIMG">
     <img src="{site_url}/skin/images/login1.png" class="img-fluid">  
   </div> </div> 
   <div class="item">
   <div class="login_lIMG">
     <img src="{site_url}/skin/images/login2.png" class="img-fluid">  
   </div> </div>
   <div class="item">
   <div class="login_lIMG">
     <img src="{site_url}/skin/images/login3.png" class="img-fluid">  
   </div> </div>
  </div> </div>    
  <div class="col-lg-5 col-md-6">
     <div class="login_logo">
     <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>"></a>     
     </div> 
  <div class="login_colm-frm help_u_forms">
<div class="loginform_head">
 
      {system_message}               
          {system_info}
          
  <h3> Login</h3> 
  <h5>to Continue</h5>
  </div> 
  <div class="login_colm-frm_col">
    <?php echo form_open('login/auth'); ?>
   
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
        <div class="row">
        <div class="col-sm-6">
        <a href="{site_url}forgot" class="forgot_password_icon">Forgot Password</a>    
        </div>

        <div class="col-sm-6">
        <a href="{site_url}forgot/tPin" class="forgot_password_icon">Forgot T-PIN</a>    
        </div>
      </div>
      
      </div>
     <div class="form-group text-center">
      <button class="btn_submit" type="submit">Submit</button>
      </div>

      </div>  


         <?php echo form_close(); ?>
</div>
  </div></div>
</div>  
  </div>      