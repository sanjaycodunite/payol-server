{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>S-Wallet to Main Wallet Transfer</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('superadmin/cwallet/mainWalletTransferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Account*</b></label>
              <select class="selectpicker form-control" name="account_id" id="selCwalletMember" data-live-search="true">
              <option value="">Select Account</option>
              <?php
              if($accountList){
                foreach($accountList as $list){
              ?>
              <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
              <?php }} ?>  
              </select>
              <?php echo form_error('account_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Available S-Wallet Balance*</b></label>
              <input type="text" class="form-control" id="balance" value="0" readonly="">
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
              <label><b>Description*</b></label>
              <input type="text" name="description" class="form-control" placeholder="Description" id="description" />
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




