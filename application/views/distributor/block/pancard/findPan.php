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
                <h4><b>Find PAN</b></h4>
                </div>


                 <div class="col-sm-6">
                <h4><b>Find Pan Charge - <?php echo $com_amount = $this->User->get_find_pan_charge($loggedUser['id']);?> +18% GST</b></h4>
                </div>
                         
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('distributor/pancard/findPanAuth', array('id' => 'pancard_active'),array('method'=>'post')); ?>
            
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              
              <div class="row">
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Name*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('name'); ?>" name="name" id="firstname" placeholder="Name">
                  <?php echo form_error('name', '<div class="error">', '</div>'); ?>
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


