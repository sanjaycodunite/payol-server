{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Create Account </b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/account/saveAccount', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row"  id="before_user_row">




                


             

              

                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Domain Name*</b></label>
                <input type="text" class="form-control" name="domain_name" value="<?php echo set_value('domain_name') ?>">
                <?php echo form_error('domain_name', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Domain Url*</b></label>
                <input type="text" class="form-control" name="domain_url" value="<?php echo set_value('domain_url') ?>">
                <?php echo form_error('title', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Member ID Prefix*</b></label>
                <input type="text" class="form-control" name="account_code" value="<?php echo set_value('account_code') ?>">
                <?php echo form_error('account_code', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
            <label><b>Upload logo</b></label>
            <input type="file" name="profile" class="form-control">
            <?php echo form_error('profile', '<p class="reg_alert_error">', '</p>'); ?>
            <p>Only PDF,JPG,PNG allowed</p>
          </div>
                  
                </div>



                  <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Contact Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Name*</b></label>
                <input type="text" class="form-control" name="name" value="<?php echo set_value('name') ?>">
                <?php echo form_error('name', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
                <div class="form-group">
                <label><b>Email*</b></label>
                <input type="text" class="form-control" name="email" value="<?php echo set_value('email') ?>">
                <?php echo form_error('email', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
                <div class="form-group">
                <label><b>Mobile*</b></label>
                <input type="text" class="form-control" name="mobile" value="<?php echo set_value('mobile') ?>">
                <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
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




