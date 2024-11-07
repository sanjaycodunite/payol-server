
 {system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add Fund QR</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('distributor/wallet/upiAddFundAuth',array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
           <div class="row">
              
              <div class="col-sm-4">
                <div class="form-group">
                <label><b>Amount*</b></label>
                <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount">
                <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
                </div>
              </div>
            

            </div>

             

              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-left">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?> 
    
    </div>




