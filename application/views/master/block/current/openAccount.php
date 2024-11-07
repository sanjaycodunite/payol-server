{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Open New Current Account</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('master/current/accountAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              
			        <div class="col-sm-3">
              <div class="form-group">
              <label><b>First Name*</b></label>
              <input type="text" class="form-control" name="first_name" placeholder="First Name">
              <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Last Name*</b></label>
              <input type="text" class="form-control" name="last_name" placeholder="Last Name">
              <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Mobile*</b></label>
              <input type="text" class="form-control" name="mobile" placeholder="Mobile">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Email*</b></label>
              <input type="text" class="form-control" name="email" placeholder="Email">
              <?php echo form_error('email', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Account Type*</b></label>
              <select class="form-control" name="account_type">
                <option value="">Select Account Type</option>
                <option value="Individual">Individual</option>
                <option value="Propietorship">Propietorship</option>
                <option value="Partnership">Partnership</option>
                <option value="Private Ltd">Private Ltd</option>
                <option value="Public Ltd">Public Ltd</option>
                <option value="LLP">LLP</option>
                <option value="HUF">HUF</option>
                <option value="OPC">OPC</option>
              </select>
              <?php echo form_error('account_type', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Pincode/Zipcode*</b></label>
              <input type="text" class="form-control" name="pincode" placeholder="Pincode/Zipcode">
              <?php echo form_error('pincode', '<div class="error">', '</div>'); ?>  
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




