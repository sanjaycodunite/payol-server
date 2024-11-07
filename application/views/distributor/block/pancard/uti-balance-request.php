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
                <h4><b>UIT PAN ID  Request</b></h4>
                </div>

              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('retailer/pancard/utiBalanceAuth', array('id' => 'pancard_active'),array('method'=>'post')); ?>
            
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              
              <div class="row">
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>UTI Register ID*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('uti_pan_id'); ?>" name="uti_pan_id" id="uti_pan_id" placeholder="UTI Register ID">
                  <?php echo form_error('uti_pan_id', '<div class="error">', '</div>'); ?>
                </div>
               </div> 
                
             
               
               
               <div class="col-sm-3"> 
                <div class="form-group">
                  <label><b>Number Of Coupon*</b></label>
                  <input type="text" class="form-control" value="<?php echo set_value('aadharnumber'); ?>" name="coupon" id="coupon" placeholder="Coupon">
                  <?php echo form_error('coupon', '<div class="error">', '</div>'); ?>
                  <div id="amount"> </div>
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



