<div class="container-fluid">
{system_message}    
{system_info}
<div class="col-sm-6">
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Edit News</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('superadmin/package/updatePackage', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $id;?>" name="id">
              
              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label><b>Package Name*</b></label>
                  <input type="text" class="form-control" name="package_name" id="package_name" placeholder="Package Name" value="<?php echo $packageData['package_name']; ?>">
                  <?php echo form_error('package_name', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
              </div>

              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label><b>Status</b></label>
                  <select class="form-control" name="status" id="status">
                    <option value="1" <?php if($packageData['status'] == 1){ ?> selected="" <?php } ?>>Active</option>
                    <option value="0" <?php if($packageData['status'] == 0){ ?> selected="" <?php } ?>>Deactive</option>
                  </select>
                  <?php echo form_error('status', '<div class="error">', '</div>'); ?>
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
  </div>
</div>



