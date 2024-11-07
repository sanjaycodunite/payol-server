    <div class="login_marwar">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-12">
        {system_message}               
          {system_info}
      </div>
      <div class="col-lg-7 col-12">
      
     
      <div class="login_form_left_side">
      </div>
      <div class="login_left_side">
      <img class="img-fluid" src="{site_url}skin/front/login/img/banner/graphic2.svg" alt=""></div>
      </div>
      <div class="col-lg-5 col-12">
        <div class="login_logo">
           <a href="{site_url}"> 
        <img class="img-fluid" src="{site_url}<?php echo $accountData['image_path']; ?>" alt=""></a>
        </div>
        <div class="login_colm-frm help_u_forms">
         <div class="loginform_head">
          <h3><b>Otp Verification</b></h3>
         </div>
          
           
          <div class="login_colm-frm_col">
          <?php echo form_open('forgot/otpauth',array('class'=>'login_form')); ?>
            
            <div class="messages"></div>
            <input type="hidden" name="encode_opt_code" value="{encode_opt_code}" />
             <div id="inputs" class="digit-group-inner otpform-group form-group otp_grid">
     <input class="form-control otpcode" name ="otp_code[]" type="password" maxlength="1" autofocus>
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <input class="form-control otpcode"  name ="otp_code[]" type="password" maxlength="1">
   <span class="toggleOtp fa fa-fw fa-eye field-icon toggle-password"></span>
    
   </div>

                      
                 
            <div class="form-group mt-4">
              <p style="color:#d83470;" id="timer"></p>
              <div class="remember-checkbox d-flex align-items-center justify-content-between">
                 
                <div class="checkbox">
                  <a href="{site_url}forgot/resendOtpAuth/{encode_opt_code}" id="resendBtn" style="display:none;" onclick="resend();">Resent Otp</a>
                </div>

                 <a href="{site_url}login">Go to Login</a>
              </div>
            </div> 
             <div class="form-group_login">
            <button class="btn btn-primary btn-block" type="submit">Verify</button>
             </div>
           
          <?php echo form_close(); ?>
          
                
        
        
        </div> </div>
      </div>
    </div>
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
            document.getElementById('timer').innerHTML = 'Resend Otp in '+m + ':' + s;
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