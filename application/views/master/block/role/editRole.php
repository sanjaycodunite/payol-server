{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Edit Role</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/role/updateRole', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $role_id;?>" name="role_id" value="{role_id}">
              <div class="row">
              <div class="col-sm-6">
              <div class="form-group">
              <label><b>Role Title*</b></label>
              <input type="text" class="form-control" name="role_title" id="role_title" value="<?php echo $roleList['title']; ?>" placeholder="Role Title">
              <?php echo form_error('role_title', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-6">
              <div class="form-group">
              <label><b>Status</b></label>
              <select class="form-control" name="status">
              <option value="1" <?php if($roleList['status'] == 1){ ?> selected="" <?php } ?> >Active</option>
              <option value="0" <?php if($roleList['status'] == 0){ ?> selected="" <?php } ?> >Deactive</option>  
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




