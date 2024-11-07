<div class="container-fluid">
<?php echo form_open_multipart('retailer/newaeps/updateTransferOTPAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $decode_otp_code;?>" name="decode_otp_code">

<div class="card shadow ">
{system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>OTP Verification</b></h4>
                </div>

                
                <div class="col-sm-4  text-right">
                <button onclick="window.history.back()" type="button" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            
              <div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-8">
					<div class="row">
						<div class="col-sm-6">
						<div class="form-group">
						<label><b>OTP*</b></label>
						<input type="text" class="form-control" name="otp_code" id="otp_code" autocomplete="off" placeholder="Enter OTP Code">
						<?php echo form_error('otp_code', '<div class="error">', '</div>'); ?>  
						</div>
						</div>
					</div>
					<div class="row">	    
						<div class="col-sm-2">
						</div>
						<div class="col-sm-6">
						<div class="form-group">
							<button type="submit" class="btn btn-success">Verify OTP</button>
						</div>
						</div>
					</div>
				</div>
			
              
          </div>
        </div>
           
 <?php echo form_close(); ?>     
    </div>
</div>
</div>




