{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Import DMT Kyc File</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('superadmin/report/importFileAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
               <div class="col-sm-3">
                <div class="form-group">
              <label><b>Upload File*</b></label>
              <input type="file" name="profile">
              <p>Note: Only CSV File Allowed.</p>
              <?php echo form_error('profile', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
             
              </div>

              
              
              


              
          </div>
        
        <div class="card shadow">
        <div class="card-header py-3 text-left">
        <button type="submit" class="btn btn-success">Submit</button>
        
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




