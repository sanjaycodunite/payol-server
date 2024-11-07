<?php
  
  if($is_active_uti == 2){

?>


<div class="container-fluid">
<div class="col-sm-12">  
{system_message}    
{system_info}
</div>
<div class="col-sm-12">
<div class="card shadow ">

            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Purchase Token</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('distributor/pancard/purchaseCouponAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              
              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label><b>PSA LoginID*</b></label>
                  <input type="text" class="form-control" name="psa_login_id" id="psa_login_id" value="{psaLoginId}" placeholder="PSA LoginID">
                  <?php echo form_error('psa_login_id', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
              </div>

              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label><b>No. of Coupon*</b></label>
                  <input type="text" class="form-control" name="coupon" id="coupon" placeholder="No. of Coupon">
                  <?php echo form_error('coupon', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
              </div>


              

              
          </div>
        <div class="card-footer text-right">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>
</div>
</div>



<?php } else{ ?>

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
                <h4><b>Active UTI</b></h4>
                </div>

                <div class="col-sm-4 text-center">
                  <?php
                   if($is_active_uti == 1){
                  ?>
                  <h4><b>Status - <font color="orange">Pending</font></b></h4>
                <?php } elseif($is_active_uti == 3){ ?>
                  <h4><b>Status - <font color="red">Rejected</font></b></h4>
                  <p><?php echo $reason; ?></p>
                <?php } elseif($is_active_uti == 4){ ?>
                  <h4><b>Status - <font color="blue">Not Activated</font></b></h4>
                <?php } ?>

                </div>
                
                <div class="col-sm-4  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php if($is_active_uti != 1){ ?>
            <?php echo form_open_multipart('distributor/pancard/pancardActiveAuth', array('id' => 'pancard_active'),array('method'=>'post')); ?>
            <?php } ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              
              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label><b>Name*</b></label>
                  <input type="text" class="form-control" name="name" id="name" value="<?php echo $kycData['name']; ?>" placeholder="Name">
                  <?php echo form_error('name', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
              </div>

              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label><b>Email*</b></label>
                  <input type="text" class="form-control" name="email" id="name" value="<?php echo $kycData['email']; ?>" placeholder="Email">
                  <?php echo form_error('email', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
              </div>


              <div class="row">
               <div class="col-sm-12"> 
                <div class="form-group">
                  <label><b>Mobile*</b></label>
                  <input type="text" class="form-control" name="mobile" id="mobile" value="<?php echo $kycData['mobile']; ?>" placeholder="Mobile">
                  <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
              </div>

              <div class="row">
               <div class="col-sm-6"> 
                <div class="form-group">
                  <label><b>Aadhar Card*</b></label>
                  <input type="file" name="aadhar_card" class="form-control">
                  <?php echo form_error('aadhar_card', '<div class="error">', '</div>'); ?>
                  <p>Note:-Please upload aadhar front and back side photo.</p>
                  <br><?php
                   if($kycData['aadhar_card']){
                  ?>
                  <img src="<?php echo base_url($kycData['aadhar_card']); ?>" width="100">
                <?php } ?>
                </div>
               </div>


               <div class="col-sm-6"> 
                <div class="form-group">
                  <label><b>Pancard*</b></label>
                  <input type="file" name="pancard" class="form-control">
                  <?php echo form_error('pancard', '<div class="error">', '</div>'); ?>
                  <br><?php
                   if($kycData['pancard']){
                  ?>
                  <img src="<?php echo base_url($kycData['pancard']); ?>" width="100">
                <?php } ?>
                </div>
               </div> 
              </div>


              

               


              
          </div>
        <?php if($is_active_uti != 1){ ?>
        <div class="card-footer text-right">
        <button type="button" class="btn btn-success" id="pancard_submit" onclick="submitForm()">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        <?php } ?>
        </div>    
 <?php echo form_close(); ?>     
    </div>
</div>
</div>


<?php } ?>

<script type="text/javascript">
  function submitForm(){
   
    document.getElementById("pancard_active").submit();
    document.getElementById("pancard_submit").disabled = true;

  }
</script>



