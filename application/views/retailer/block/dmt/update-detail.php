{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Update Personal Detail</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
           
           <div class="row">
            <div class="col-lg-6 col-md-6">
          <div class="col-lg-12 col-md-12">
              <div class="beneficiary_form_section">
                <?php echo form_open_multipart('retailer/dmt/updatePersonalDetailAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $mobile;?>" name="accountMobile">
              <div class="row">
              <div class="col-lg-12">
                <h3>Update Personal Detail</h3>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>First Name*</b></label>
              <input type="text" class="form-control" name="first_name" value="<?php echo $member_dmt_data['first_name']; ?>" placeholder="First Name">
              <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Last Name*</b></label>
              <input type="text" class="form-control" name="last_name" value="<?php echo $member_dmt_data['last_name']; ?>" placeholder="Last Name">
              <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>DOB*</b></label>
              <input type="text" class="form-control" value="<?php echo $member_dmt_data['dob']; ?>" id="start_date" autocomplete="off" name="dob" placeholder="Date of Birth">
              <?php echo form_error('dob', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Address*</b></label>
              <textarea name="address" class="form-control" rows="2"><?php echo $member_dmt_data['address']; ?></textarea>
              <?php echo form_error('address', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Pin Code*</b></label>
              <input type="text" class="form-control" value="<?php echo $member_dmt_data['pin_code']; ?>" name="pin_code" placeholder="Pin Code">
              <?php echo form_error('pin_code', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
               <div class="col-lg-12">
              <div class="form-group">
               <button class="login_btn btn btn-success" type="submit">Submit </button> 
              </div></div>

             

            </div>
            <?php echo form_close(); ?>
          </div>
            </div> 
            </div> 
            
           </div>

          
         
              
              
          </div>
        </div>
        
 
    </div>




