{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add Employe</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('superadmin/employe/saveEmploye', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row"  id="before_user_row">

                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Select Role*</b></label>
                <select class="form-control" name="role">
                  <option value="">Select Role</option>
                  <?php
                   foreach($role as $list){
                  ?>
                  <option value="<?=$list['id']?>"><?=$list['title']?></option>
                  <?php } ?>      
                </select>
                <?php echo form_error('role', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Name*</b></label>
                <input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo set_value('name') ?>">
                <?php echo form_error('name', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Email*</b></label>
                <input type="text" class="form-control" name="email" placeholder="Email" value="<?php echo set_value('email') ?>">
                <?php echo form_error('email', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Mobile*</b></label>
                <input type="text" class="form-control" name="mobile" placeholder="Mobile" value="<?php echo set_value('mobile') ?>">
                <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>


                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Password*</b></label>
                <input type="password" class="form-control" name="password" placeholder="Password" value="<?php echo set_value('password') ?>">
                <?php echo form_error('password', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>


                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Status*</b></label>
                <select class="form-control" name="status">
                  <option value="1">Active</option>
                  <option value="0">Deactive</option>
                </select>
                <?php echo form_error('status', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                  

			  
			   
              </div>
          </div>
        <div class="card-footer py-3 text-right">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




