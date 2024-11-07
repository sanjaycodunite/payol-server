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
      <h3><b>Merchant eKyc</b></h3>  
      </div>
      <?php if($userData['is_instantpay_ekyc'] == 1){ ?>
        <div class="card-body">
          <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                  
                    <tr>
                      <th>Status</th>
                      <th><font color="green">Approved</font></th>
                    </tr>
                    <tr>
                      <th>Aadhar No.</th>
                      <th><?php echo isset($getAadharData['aadhar']) ? $getAadharData['aadhar'] : ''; ?></th>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <th><?php echo isset($aadhar_data['name']) ? $aadhar_data['name'] : ''; ?></th>
                    </tr>
                    <tr>
                      <th>Date of Birth</th>
                      <th><?php echo isset($aadhar_data['dateOfBirth']) ? $aadhar_data['dateOfBirth'] : ''; ?></th>
                    </tr>
                    <tr>
                      <th>Pincode</th>
                      <th><?php echo isset($aadhar_data['pincode']) ? $aadhar_data['pincode'] : ''; ?></th>
                    </tr>
                    <tr>
                      <th>State</th>
                      <th><?php echo isset($aadhar_data['state']) ? $aadhar_data['state'] : ''; ?></th>
                    </tr>
                    <tr>
                      <th>District</th>
                      <th><?php echo isset($aadhar_data['districtName']) ? $aadhar_data['districtName'] : ''; ?></th>
                    </tr>
                    <tr>
                      <th>Address</th>
                      <th><?php echo isset($aadhar_data['address']) ? $aadhar_data['address'] : ''; ?></th>
                    </tr>
                  
                </table>
              </div>
        </div>
      <?php } else { ?>
        <?php echo form_open_multipart('distributor/recharge/ekycAuth', array('id' => 'admin_profile','method'=>'post')); ?>
        <div class="card-body">
        
        <div class="form-group">
          <label><b>Mobile Number*</b></label>
          <input type="text" class="form-control" name="mobile" value="<?php echo $userData['mobile']; ?>" data-bv-field="number" id="offermobile" placeholder="Enter Mobile Number">
          <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
         </div>
         <div class="form-group">
          <label><b>Email*</b></label>
          <input type="text" class="form-control" name="email" value="<?php echo $userData['email']; ?>" placeholder="Enter Email">
          <?php echo form_error('email', '<div class="error">', '</div>'); ?>
         </div>
         <div class="form-group">
          <label><b>PAN Card Number*</b></label>
          <input type="text" class="form-control" name="pancard" placeholder="Enter Your PAN Number">
          <?php echo form_error('pancard', '<div class="error">', '</div>'); ?>
         </div>
         <div class="form-group">
          <label><b>Aadhar Number*</b></label>
          <input type="text" class="form-control" name="aadhar" placeholder="Enter Your Aadhar Number">
          <?php echo form_error('aadhar', '<div class="error">', '</div>'); ?>
          <p>Note: OTP will be sent on linked mobile to aadhar card.</p>
         </div>

          
        </div>

        <div class="card-footer">
         <button class="btn btn-primary" id="submit-btn" type="submit">Submit</button> 
        </div>
        <?php echo form_close(); ?>
      <?php } ?>

    </div>

  </div>  


  </div> 

</div>

