{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Update App Slider</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/website/updateAppSlider', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $sliderID;?>" name="sliderID">
              <div class="row">
              <div class="col-sm-12">
              <div class="form-group">
              <label><b>Image</b></label><br>
              <input type="file" name="image" id="image">
              <?php echo form_error('image', '<div class="error">', '</div>'); ?>  
              <?php if($sliderData['image']){ ?>
                <img src="{site_url}<?php echo $sliderData['image']; ?>" width="100">
              <?php } ?>
              </div>
              <div class="form-group">
              <label><b>Link</b></label><br>
              <input type="text" name="link" class="form-control" value="<?php echo $sliderData['link']; ?>">
              <?php echo form_error('link', '<div class="error">', '</div>'); ?>  
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



