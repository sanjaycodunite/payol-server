{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add Link</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('employe/link/save', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row"  id="before_user_row">




                


                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Title*</b></label>
                <input type="text" class="form-control" name="title" placeholder="Title" value="<?php echo set_value('title') ?>">
                <?php echo form_error('title', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Url*</b></label>
                <input type="url" class="form-control" name="url" placeholder="Url (Eg:- http://example.com)" value="<?php echo set_value('url') ?>">
                <?php echo form_error('url', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Status</b></label>
                <select class="form-control" name="status">
                  <option value="1">Active</option>
                  <option value="0">Deactive</option>
                </select>
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




