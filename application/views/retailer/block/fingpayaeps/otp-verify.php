{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>OTP Verification</b></h4>
                </div>
                
                <div class="col-sm-4  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('retailer/fingpayAeps/otpAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="text" value="<?php echo $memberID;?>" name="memberID">
              <input type="text" value="<?php echo $encodeFPTxnId;?>" name="encodeFPTxnId">
              
            <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Enter OTP*</b></label>
              <input type="text" class="form-control" id="otp_code" maxlength="8" name="otp_code" placeholder="Enter OTP">
              <?php echo form_error('otp_code', '<div class="error">', '</div>'); ?>  
              <br />
              <a href="{site_url}retailer/fingpayAeps/resendOtp/{encodeFPTxnId}">Resend OTP</a>
              </div>
              </div>
              

           
              
              </div>

              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




