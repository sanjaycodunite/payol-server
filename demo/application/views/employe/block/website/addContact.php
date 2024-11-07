{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add/Update Contact Detail</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('employe/website/saveContact', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-6">
              <div class="form-group">
              <label><b>Email</b></label>
              <input type="text" class="form-control" name="email" id="email" value="<?php echo $contactData['email']; ?>" placeholder="Email">
              <?php echo form_error('email', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-6">
              <div class="form-group">
              <label><b>Mobile</b></label>
              <input type="text" class="form-control" value="<?php echo $contactData['mobile']; ?>" name="mobile" id="mobile" placeholder="Mobile">
              <?php echo form_error('email', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              </div>

              <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Facebook Link</b></label>
              <input type="text" class="form-control" name="facebook" value="<?php echo $contactData['facebook']; ?>" id="facebook" placeholder="Facebook Link">
              <?php echo form_error('facebook', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Twitter Link</b></label>
              <input type="text" class="form-control" value="<?php echo $contactData['twitter']; ?>" name="twitter" id="twitter" placeholder="Twitter Link">
              <?php echo form_error('twitter', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Linkedin Link</b></label>
              <input type="text" class="form-control" name="linkedin" value="<?php echo $contactData['linkedin']; ?>" id="linkedin" placeholder="Linkedin Link">
              <?php echo form_error('linkedin', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Instagram Link</b></label>
              <input type="text" class="form-control" value="<?php echo $contactData['instagram']; ?>" name="instagram" id="instagram" placeholder="Instagram Link">
              <?php echo form_error('instagram', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              
              
              
              </div>


              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label><b>Support Working Time</b></label>
                  <input type="text" class="form-control" value="<?php echo $contactData['support_working_time']; ?>" name="support_working_time" id="support_working_time" placeholder="Support Working Time" />
                </div>
               </div> 
              </div> 

              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label><b>Address</b></label>
                  <textarea class="form-control" rows="5" name="address" id="address" placeholder="Address"><?php echo $contactData['address']; ?></textarea>
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




