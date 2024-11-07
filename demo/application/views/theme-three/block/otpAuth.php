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
       <div class="col-lg-7 col-12">
       <div class="login_form_left_side">
      </div>
      <div class="login_left_side">
      <img class="img-fluid" src="{site_url}skin/images/OTP.png" alt=""></div>
      </div>
      
  <div class="col-lg-5 col-md-6">
       <div class="login_logo">
     <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>"></a>     
     </div> 
   <div class="login_colm-frm help_u_forms">
        <div class="loginform_head">

     <?php echo form_open('login/otpAuth'); ?>
    <input type="hidden" name="encoded_otp_code" value="{encoded_otp_code}" />

      {system_message}               
          {system_info}
          
  <h3>Otp </h3> 
  <h5>Verification</h5>
  </div>  
  
  <div class="login_colm-frm_col">
      <div class="form-group">
          <i class="fa fa-lock"></i>
                      <input class="form-control" type="password" name="otp_code" id="otp_code" placeholder="OTP CODE">
                      <?php echo form_error('otp_code', '<div class="error">', '</div>'); ?>  
                    </div>
      
       <div class="form-group mt-4">
             
            </div> 
     <div class="form-group text-center">
      <button class="btn_submit" type="submit">Submit</button>
      </div>

      </div>  


         <?php echo form_close(); ?>

  </div></div></div>  
</div>  
  </div>      