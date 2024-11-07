

 <div class="login_section_mt">
  <div class="container">
  <div class="row">
    <div class="col-lg-12">
      {system_message}               
          {system_info}
    </div>
       <div class="col-lg-7 col-12">
       <div class="login_form_left_side">
      </div>
      <div class="login_left_side">
      <img class="img-fluid" src="{site_url}skin/images/OTP.png" alt=""></div>
      </div>


  <div class="col-lg-5 col-12">
      <div class="login_logo">
     <a href="{site_url}"><img src="{site_url}<?php echo $accountData['image_path']; ?>"></a></div> 


  <div class="login_colm-frm help_u_forms">

    <?php echo form_open('forgot/transcationOtpAuth'); ?>
    <input type="hidden" name="encode_opt_code" value="{encode_opt_code}" />
<div class="loginform_head">
  
          
  <h3>Otp Verification</h3> 
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
 </div>

         <?php echo form_close(); ?>

  </div></div>
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