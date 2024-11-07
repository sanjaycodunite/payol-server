{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Transaction Verification</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('user/wallet/payolWalletPinAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="{encode_txnid}" name="encode_txnid">
              <div class="row">
              
			        
			        <div class="col-sm-2">
              <div class="form-group">
              <label><b>Pin*</b></label>
              <input type="text" class="form-control" name="txnpin" id="txnpin" placeholder="Transaction Pin">
              <?php echo form_error('txnpin', '<div class="error">', '</div>'); ?>  
              
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




