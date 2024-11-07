{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Edit Feature</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/website/updateFeature', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $id;?>" name="id">
              <div class="row">
              <div class="col-sm-12">
              <div class="form-group">
              <label><b>Image*</b></label><br>
              <input type="file" name="image" id="image" placeholder="Email">
              <?php echo form_error('image', '<div class="error">', '</div>'); ?>
              <?php
              if($featureData['image']){
              ?>
              <img src="{site_url}<?php echo $featureData['image']; ?>" width="100">
              <?php } ?>  
              </div>
              </div>
              
              </div>

              <div class="row">
              <div class="col-sm-12">
              <div class="form-group">
              <label><b>Title*</b></label>
              <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="<?php echo $featureData['title']; ?>">
              <?php echo form_error('title', '<div class="error">', '</div>'); ?>  
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




