{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b> Apply NSDL Pan Card</b></h4>
                </div>
                
                <div class="col-sm-4  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('retailer/nsdl/nsdlActiveAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $memberID;?>" name="memberID">
              
            <div class="row">
              <div class="col-sm-12">
                <h5>Personal Detail</h5>
                <hr />
              </div>
              
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Title*</b></label>
              <select name="title" class="form-control selectpicker" data-live-search="true">
                <option value=""></option>
                <option value="Mr/Shri">Mr/Shri</option>
                <option value="Mrs/Shrimati">Mrs/Shrimati</option>

              </select>
              <?php echo form_error('title', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>


              <div class="col-sm-3">
              <div class="form-group">
              <label><b>PAN Mode*</b></label>
              <select name="mode" class="form-control selectpicker" data-live-search="true">
                <option value=""></option>
                <option value="P">Physical Pan</option>
                <option value="E">Electronic Pan</option>

              </select>
              <?php echo form_error('mode', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>


              <div class="col-sm-3">
              <div class="form-group">
              <label><b>First Name*</b></label>
              <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="<?php echo $memberData['name']; ?>">
              <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>

               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Middle Name*</b></label>
              <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Middle Name" value="<?php echo set_value('middle_name'); ?>">
               
              
              </div>
              </div>


              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Last Name*</b></label>
              <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="<?php echo set_value('last_name'); ?>">
              <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>


               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Email*</b></label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Email ID" value="<?php echo $memberData['email']; ?>">
             
              
              </div>
              </div>


               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Gender*</b></label>
              <select name="gender" class="form-control selectpicker" data-live-search="true">
                <option value=""></option>
                <option value="male">Male</option>
                <option value="female">Female</option>

              </select>
              <?php echo form_error('gender', '<div class="error">', '</div>'); ?>  
              
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




