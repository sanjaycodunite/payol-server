{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Main Wallet Transfer</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('portal/wallet/mainwalletTransferAuth', array('id' => 'admin_profile','method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>UPI Wallet Balance</b></label>
              <input type="text" readonly="readonly" class="form-control" name="upi_wallet_balance" value="{upi_wallet_balance}" placeholder="UPI Wallet Balance">
              <?php echo form_error('upi_wallet_balance', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Available Balance</b></label>
              <input type="text" readonly="readonly" class="form-control" name="release_amount" value="{release_amount}" placeholder="UPI Wallet Balance">
              <?php echo form_error('release_amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

			         
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Amount*</b></label>
              <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount">
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-3">
                <div class="form-group">
              <label><b>Description</b></label>
              <input type="text" class="form-control" name="description" id="description" placeholder="Description" value="Transfer To Main Wallet" readonly>
              <?php echo form_error('description', '<div class="error">', '</div>'); ?>   
              </div>
              </div>
              </div>

              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="submit" id="submit-btn" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




