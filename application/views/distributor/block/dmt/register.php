{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Register User</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('distributor/transfer/registerAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              
			        <div class="col-sm-2">
              <div class="form-group">
              <label><b>First Name*</b></label>
              <input type="text" class="form-control" name="first_name" placeholder="First Name">
              <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Last Name*</b></label>
              <input type="text" class="form-control" name="last_name" placeholder="Last Name">
              <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Mobile</b></label>
              <input type="text" class="form-control" name="mobile" placeholder="Mobile">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>DOB*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('dob'); ?>" id="start_date" autocomplete="off" name="dob" placeholder="Date of Birth">
              <?php echo form_error('dob', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-6">
              <div class="form-group">
              <label><b>Address*</b></label>
              <textarea name="address" class="form-control" rows="2"></textarea>
              <?php echo form_error('address', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Pin Code*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('pin_code'); ?>" name="pin_code" placeholder="Pin Code">
              <?php echo form_error('pin_code', '<div class="error">', '</div>'); ?>  
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




