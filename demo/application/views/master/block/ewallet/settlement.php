{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Collection Wallet Transfer</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('master/ewallet/settlementAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              
			        <div class="col-sm-2">
              <div class="form-group">
              <label><b>Available Balance</b></label>
              <input type="text" readonly="readonly" class="form-control" name="balance" value="{before_balance}" id="balance" placeholder="Available Balance">
              <?php echo form_error('balance', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
        <div class="col-sm-3">
              <div class="form-group">
              <label><b>Settlement Amount*</b></label>
              <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount (In Numbers Only)">
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-4">
                <div class="form-group">
              <label><b>Description*</b></label>
              <textarea class="form-control" name="description" id="description"></textarea>
              <?php echo form_error('description', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

            </div>
         
              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to settle this transaction?')">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




