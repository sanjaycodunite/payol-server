{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Update Password</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('superadmin/setting/passwordAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Old Password*</b></label>
              <input type="password" class="form-control" name="opw" id="opw" placeholder="Old Password">
              <?php echo form_error('opw', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>New Password*</b></label>
              <input type="password" class="form-control" name="npw" id="npw" placeholder="New Password">
              <?php echo form_error('npw', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Confirm New Password*</b></label>
              <input type="password" class="form-control" name="cpw" id="cpw" placeholder="Confirm New Password">
              <?php echo form_error('cpw', '<div class="error">', '</div>'); ?>  
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




