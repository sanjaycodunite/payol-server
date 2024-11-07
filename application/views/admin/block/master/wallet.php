{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Wallet Setting</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/master/walletAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Min. Transfer Wallet*</b></label>
              <input type="text" class="form-control" name="min_transfer" id="min_transfer" placeholder="Min. Transfer Wallet" value="<?php echo $walletData['min_transfer']; ?>">
              <?php echo form_error('min_transfer', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Daily Transfer Limit*</b></label>
              <input type="text" class="form-control" name="daily_transfer_limit" id="daily_transfer_limit" placeholder="Daily Transfer Limit" value="<?php echo $walletData['daily_transfer_limit']; ?>">
              <?php echo form_error('daily_transfer_limit', '<div class="error">', '</div>'); ?>  
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




