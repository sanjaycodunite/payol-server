<div class="container-fluid">
<div class="col-sm-12">  
{system_message}    
{system_info}
</div>
<div class="col-sm-12">
<div class="card shadow ">

            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Active NSDL Pancard</b></h4>
                </div>

                 <div class="col-sm-6">
                <h4><b>Active NSDL Pancard Charge - <?php echo $com_amount = $this->User->get_pan_activation_charge($loggedUser['id']);?></b></h4>
                </div>
                
                         
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('master/pancard/nsdlActiveAuth', array('id' => 'pancard_active'),array('method'=>'post')); ?>
            
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              
              <div class="row">
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>First Name*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('firstname'); ?>" name="firstname" id="firstname" placeholder="First Name">
                  <?php echo form_error('firstname', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Middle Name</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('middlename'); ?>" name="middlename" id="middlename" placeholder="Middle Name">
                  <?php echo form_error('middlename', '<div class="error">', '</div>'); ?>
                </div>
               </div>
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Last Name*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('lastname'); ?>" name="lastname" id="lastname" placeholder="Last Name">
                  <?php echo form_error('lastname', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Email*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('email'); ?>" name="email" id="email" placeholder="Email">
                  <?php echo form_error('email', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Date of Birth*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('dob'); ?>" name="dob" id="special_price_from" placeholder="Date of Birth">
                  <?php echo form_error('dob', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Mobile*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('mobile'); ?>" name="mobile" id="mobile" placeholder="Mobile">
                  <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Pincode*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('pincode'); ?>" name="pincode" id="pincode" placeholder="Pincode">
                  <?php echo form_error('pincode', '<div class="error">', '</div>'); ?>
                </div>
               </div>
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Gender*</b></label>
                  <select class="form-control" name="gender">
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                  </select>
                  <?php echo form_error('gender', '<div class="error">', '</div>'); ?>
                </div>
               </div>
               <div class="col-sm-6"> 
                <div class="form-group">
                  <label><b>Address*</b></label>
                  <textarea class="form-control" name="address" placeholder="Address" rows="2"><?php echo set_value('address'); ?></textarea>
                  <?php echo form_error('address', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
               <div class="col-sm-6"> 
                <div class="form-group">
                  <label><b>Shop Name*</b></label>
                  <textarea class="form-control" name="shop_name" placeholder="Shop Name" rows="2"><?php echo set_value('shop_name'); ?></textarea>
                  <?php echo form_error('shop_name', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>State*</b></label>
                  <select class="form-control" name="state_id" id="nsdlStateId">
                    <option value="">Select State</option>
                    <?php if($stateList){ ?>
                      <?php foreach($stateList as $list){ ?>
                      <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                  <?php echo form_error('state_id', '<div class="error">', '</div>'); ?>
                </div>
               </div>
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>District*</b></label>
                  <select class="form-control" name="district_id" id="nsdlDistrictId">
                    <option value="">Select District</option>
                  </select>
                  <?php echo form_error('district_id', '<div class="error">', '</div>'); ?>
                </div>
               </div>
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>PAN Number*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('pannumber'); ?>" name="pannumber" id="pannumber" placeholder="PAN Number">
                  <?php echo form_error('pannumber', '<div class="error">', '</div>'); ?>
                </div>
               </div>
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Aadhar Number*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('aadharnumber'); ?>" name="aadharnumber" id="aadharnumber" placeholder="Aadhar Number">
                  <?php echo form_error('aadharnumber', '<div class="error">', '</div>'); ?>
                </div>
               </div>

              </div>


              
          </div>
        
        <div class="card-footer text-right">
        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to  this transaction?')">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        
        </div>    
 <?php echo form_close(); ?>     
    </div>
</div>
</div>




<script type="text/javascript">
  function submitForm(){
   
    document.getElementById("pancard_active").submit();
    document.getElementById("pancard_submit").disabled = true;

  }
</script>



