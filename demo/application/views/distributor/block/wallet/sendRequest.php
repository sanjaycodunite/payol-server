{system_message}    
{system_info}<div id="apiErrorResponse"></div>
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Send UPI Request</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('#', array('id' => 'upi_topup_form'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
           <div class="row">
              
              <div class="col-sm-4">
                <div class="form-group">
                <label><b>Amount*</b></label>
                <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount">
                <div id="amount_error"></div> 
                </div>
              </div>
            

              <div class="col-sm-4">
                <div class="form-group">
                <label><b>VPA ID*</b></label>
                <input type="text" class="form-control" name="vpa_id" id="vpa_id" placeholder="VPA ID">
                <div id="vpa_error"></div>   
                </div>
              </div>


              <div class="col-sm-4">
                <div class="form-group">
                  <label><b>Description*</b></label>
                  <input type="text" class="form-control" name="description" id="description" placeholder="Description" />
                  <div id="description_error"></div> 
                </div>
              </div>
            

            </div>

             

              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="button" class="btn btn-success" id="upi-topup-btn">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?> 
    
    </div>




