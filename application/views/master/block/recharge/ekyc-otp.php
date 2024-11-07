  <div class="row">
    <div class="col-sm-6">
      {system_message}    
      {system_info}
    </div>
  </div>
  <div class="row">

  <div class="col-sm-6">

    <div class="card">

      <div class="card-header">
      <h3><b>eKyc OTP Verification</b></h3>  
      </div>
      <?php echo form_open_multipart('master/recharge/ekycOtpAuth', array('id' => 'admin_profile','method'=>'post')); ?>
      <input type="hidden" name="otpReferenceID" value="{otpReferenceID}">
      <div class="card-body">
      
      <div class="form-group">
        <label><b>OTP (One Time Password)*</b></label>
        <input type="text" class="form-control" name="otp_code" placeholder="Enter One Time Password">
        <?php echo form_error('otp_code', '<div class="error">', '</div>'); ?>
       </div>
       
      </div>

      <div class="card-footer">
       <button class="btn btn-primary" id="submit-btn" type="submit">Submit</button> 
      </div>
      <?php echo form_close(); ?>

    </div>

  </div>  


  </div> 

</div>

