{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Payment Setting</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('employe/master/paymentAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Payment Gateway Charge</b></label>
              <input type="text" class="form-control" name="gateway_charge" id="gateway_charge" placeholder="Gateway Charge" value="<?php echo isset($walletData['surcharge']) ? $walletData['surcharge'] : 0 ; ?>">
              <?php echo form_error('gateway_charge', '<div class="error">', '</div>'); ?>  
              </div>
              <div class="form-group">
              <input type="checkbox" name="is_flat" <?php if(isset($walletData['is_flat']) && $walletData['is_flat'] == 1){ ?> checked="checked" <?php } ?> id="is_flatt" value="1">
              <label for="is_flatt"><b>Is Flat</b></label>

              </div>
              </div>
              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Default Status</b></label>
              <select class="form-control" name="status">
                <option value="1" <?php if(isset($walletData['default_status']) && $walletData['default_status'] == 1){ ?> selected="selected" <?php } ?>>Received From Gateway</option>
                <option value="2" <?php if(isset($walletData['default_status']) && $walletData['default_status'] == 2){ ?> selected="selected" <?php } ?>>Success</option>
                <option value="3" <?php if(isset($walletData['default_status']) && $walletData['default_status'] == 3){ ?> selected="selected" <?php } ?>>Failed</option>
                <option value="4" <?php if(isset($walletData['default_status']) && $walletData['default_status'] == 4){ ?> selected="selected" <?php } ?>>Pending</option>
              </select>
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




