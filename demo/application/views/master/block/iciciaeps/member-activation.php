{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Activate ICICI AEPS</b></h4>
                </div>
                
                <div class="col-sm-4  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('master/iciciaeps/activeAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $memberID;?>" name="memberID">
              
            <div class="row">
              <div class="col-sm-12">
                <h5>Personal Detail</h5>
                <hr />
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
              <input type="text" class="form-control" id="last_name" name="middle_name" placeholder="Middle Name" value="<?php echo set_value('middle_name'); ?>">
                
              
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
              <label><b>Father Name.*</b></label>
              <input type="text" class="form-control" name="father_name" id="father_name" placeholder="Father Name." value="<?php echo set_value('father_name'); ?>">
              <?php echo form_error('father_name', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>

               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Mother Name.*</b></label>
              <input type="text" class="form-control" name="mother_name" id="mother_name" placeholder="Mother Name." value="<?php echo set_value('mother_name'); ?>">
              <?php echo form_error('mother_name', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>



              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Mobile No.*</b></label>
              <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile No." value="<?php echo $memberData['mobile']; ?>">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>

               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Aadhar No.*</b></label>
              <input type="text" class="form-control" name="aadhar_no" id="aadhar_no" placeholder="Aadhar No." value="<?php echo set_value('aadhar_no'); ?>">
              <?php echo form_error('aadhar_no', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Pancard No.*</b></label>
              <input type="text" class="form-control" name="pancard_no" id="pancard_no" placeholder="Pancard No." value="<?php echo set_value('pancard_no'); ?>">
              <?php echo form_error('pancard_no', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>



              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Shop Name*</b></label>
              <input type="text" class="form-control" name="shop_name" id="shop_name" placeholder="Shop Name" value="<?php echo set_value('shop_name'); ?>">
              <?php echo form_error('shop_name', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>
               
              <div class="col-sm-12">
                <h5>Address</h5>
                <hr />
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>State*</b></label>
              <select class="form-control" name="state_id" id="selState">
                <option value="">Select State</option>
                <?php if($stateList){ ?>
                  <?php foreach($stateList as $list){ ?>
                    <option value="<?php echo $list['id']; ?>"><?php echo $list['state']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              <?php echo form_error('state_id', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>City*</b></label>
              <select class="form-control" name="city_id" id="selCity">
                <option value="">Select City</option>
                
              </select>
              <?php echo form_error('city_id', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Address*</b></label>
              <textarea class="form-control" name="address"><?php echo set_value('address'); ?></textarea>
              <?php echo form_error('address', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>PIN Code*</b></label>
              <input type="text" class="form-control" name="pin_code" id="pin_code" placeholder="PIN Code" value="<?php echo set_value('pin_code'); ?>">
              <?php echo form_error('pin_code', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>

               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Village*</b></label>
              <input type="text" class="form-control" name="village" id="village" placeholder="Village" value="<?php echo set_value('village'); ?>">
              <?php echo form_error('village', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>


              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Post Office*</b></label>
              <input type="text" class="form-control" name="post" id="post" placeholder="Post Office" value="<?php echo set_value('post'); ?>">
              <?php echo form_error('post', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>

               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Police Station*</b></label>
              <input type="text" class="form-control" name="police_station" id="police_station" placeholder="Police Station" value="<?php echo set_value('police_station'); ?>">
              <?php echo form_error('police_station', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>

               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Block*</b></label>
              <input type="text" class="form-control" name="block" id="block" placeholder="Block" value="<?php echo set_value('block'); ?>">
              <?php echo form_error('block', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>

               <div class="col-sm-3">
              <div class="form-group">
              <label><b>District*</b></label>
              <input type="text" class="form-control" name="district" id="district" placeholder="District" value="<?php echo set_value('district'); ?>">
              <?php echo form_error('district', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>

          


              <div class="col-sm-12">
                <h5>Document</h5>
                <hr />
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Aadhar Front Photo*</b></label>
              <input type="file" name="aadhar_photo">
              <p>Note: Only jpg,png allowed, max size 2MB.</p>
              <?php echo form_error('aadhar_photo', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Aadhar Back Photo*</b></label>
              <input type="file" name="aadhar_back_photo">
              <p>Note: Only jpg,png allowed, max size 2MB.</p>
              <?php echo form_error('aadhar_back_photo', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Pancard Photo*</b></label>
              <input type="file" name="pancard_photo">
              <p>Note: Only jpg,png allowed, max size 2MB.</p>
              <?php echo form_error('pancard_photo', '<div class="error">', '</div>'); ?>  
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



