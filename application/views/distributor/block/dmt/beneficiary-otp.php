{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>OTP Verification</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('distributor/transfer/beneficiaryOtpAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $token;?>" name="token">
              <input type="hidden" value="<?php echo $mobile;?>" name="mobile">
              <div class="row">
              
			        <div class="col-sm-2">
              <div class="form-group">
              <label><b>OTP Code*</b></label>
              <input type="text" class="form-control" name="otp_code" placeholder="OTP Code">
              <?php echo form_error('otp_code', '<div class="error">', '</div>'); ?>  
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




