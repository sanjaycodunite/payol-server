{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Wallet Transfer</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('retailer/wallet/walletTransferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              
			         <div class="col-sm-3">
              <div class="form-group">
              <label><b>MemberID*</b></label>
              <input type="text" class="form-control" name="member_id" id="member_id" placeholder="MemberID">
              <?php echo form_error('member_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
             
			        <div class="col-sm-3">
              <div class="form-group">
              <label><b>Amount*</b></label>
              <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount (In Numbers Only)">
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Transaction Password*</b></label>
              <input type="text" class="form-control" name="transaction_password" id="transaction_password" placeholder="Transaction Password">
              <?php echo form_error('transaction_password', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Description*</b></label>
              <input type="text" class="form-control" name="description" id="description" placeholder="Description" />
              <?php echo form_error('description', '<div class="error">', '</div>'); ?>  
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




