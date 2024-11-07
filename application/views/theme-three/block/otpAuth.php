<?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $contactDetail = $this->db->get_where('website_contact_detail',array('account_id'=>$account_id))->row_array();
      
    ?>
    
<div class="login_header_section">
    <div class="container">
  <div class="row align-items-center">
     <div class="col-lg-3 col-md-3">
      <div class="Login_logo"><a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>" class="img-fluid"></a></div>   
     </div>
     <div class="col-lg-6 col-md-6">
      <div class="login_top_Details">
          <ul>
          <li><span>Helpline Number:</span> <i class="fa fa-phone"></i><a href="tel:<?php echo $contactDetail['mobile'] ?>" target="_blank">+91-<?php echo $contactDetail['mobile'] ?></a></li>
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
  
  <div class="login_colm-frm_col digit-group">
   <div id="inputs" class="digit-group-inner otpform-group form-group otp_grid">
     <input class="form-control otpcode" name ="otp_code[]" type="password" maxlength="1" autofocus>
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <span class="toggleOtp fa fa-fw fa-eye field-icon toggle-password"></span>
    
   </div>
  
  
  
<div class="timer_li d-flex align-items-center justify-content-between mb-4">
      <p style="color:#d83470;margin-bottom: 0px;" id="timer"></p> 
       <div class="checkbox">
                  <a href="{site_url}login/resendOtpAuth/{encoded_otp_code}" id="resendBtn" style="display:none;" onclick="resend();">Resent Otp</a>
                </div>
</div>

     
      
     
     <div class="form-group text-center">
      <button class="btn_submit" type="submit">Submit</button>
      </div>

      </div>  


         <?php echo form_close(); ?>

  </div></div></div>  
</div>  
  </div>      


  <script>
        let timerOn = true;
        
        function resend(){
            document.getElementById("resend-otp").submit();
        }

        function timer(remaining) {
            var m = Math.floor(remaining / 60);
            var s = remaining % 60;
          
            m = m < 10 ? '0' + m : m;
            s = s < 10 ? '0' + s : s;
            document.getElementById('timer').innerHTML = ' Resend Otp in'+m + ':' + s;
            remaining -= 1;
          
            if(remaining >= 0 && timerOn) {
                setTimeout(function() {
                    timer(remaining);
                }, 1000);
                return;
            }
        
            if(!timerOn) {
                // Do validate stuff here
                return;
            }
          
            if(remaining == -1){
                document.getElementById('resendBtn').style.display = 'inline-block';
            }
        }

    timer(45);
</script>

 <script>
        // script.js
        const inputs = document.getElementById("inputs");
        
        inputs.addEventListener("input", function (e) {
          const target = e.target;
          const val = target.value;
        
          if (isNaN(val)) {
            target.value = "";
            return;
          }
        
          if (val != "") {
            const next = target.nextElementSibling;
            if (next) {
              next.focus();
            }
          }
        });
        
        inputs.addEventListener("keyup", function (e) {
          const target = e.target;
          const key = e.key.toLowerCase();
        
          if (key == "backspace" || key == "delete") {
            target.value = "";
            const prev = target.previousElementSibling;
            if (prev) {
              prev.focus();
            }
            return;
          }
        });


   
    </script>