{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add Page</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/website/savePage', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              
              <div class="row">
              <div class="col-sm-12">
              <div class="form-group">
              <label><b>Page Title*</b></label>
              <input type="text" class="form-control" name="page_title" id="page_title" placeholder="Page Title">
              <?php echo form_error('page_title', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              </div>

              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label>Page Content*</label>
                  <textarea class="form-control" rows="5" name="page_content" id="page_content" placeholder="Page Content"></textarea>
                  <?php echo form_error('page_content', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
              </div>


              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label>Status*</label>
                  <select class="form-control" name="status" id="status">
                    <option value="1">Active</option>
                    <option value="2">Deactive</option>
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




