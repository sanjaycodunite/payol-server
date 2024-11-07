 <div class="row">
    <div class="col-sm-12">
      {system_message}    
      {system_info}
    </div>
  </div>

  <?php echo form_open_multipart('master/aeps/walletTransferAuth', array('id' => 'admin_profile')); ?>
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3><b>Transfer AEPS to Payout Wallet</b></h3>
        </div>

        <div class="card-body">
         <div class="row">
         
         <div class="col-sm-3">
          <div class="form-group">
            <label><b>Current AEPS Wallet Balance</b></label>
           <input type="text" class="form-control" name="current_wallet_balance" value="<?php echo $memberDetail['aeps_wallet_balance']; ?>" readonly="">
           <?php echo form_error('current_wallet_balance', '<div class="error">', '</div>'); ?>  
          </div>
         </div>

        

         <div class="col-sm-3">
         <div class="form-group">
            <label><b>Transfer Amount*</b></label>
           <input type="text" class="form-control" name="amount" placeholder="Amount">
           <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
          </div>
         </div>

         <div class="col-sm-3">
         <div class="form-group">
            <label><b>Transaction Password*</b></label>
           <input type="password" class="form-control" name="transaction_password" placeholder="Transaction Password">
           <?php echo form_error('transaction_password', '<div class="error">', '</div>'); ?>  
          </div>
         </div> 

         </div> 
        </div>
        
        <div class="card-footer">
         <button class="btn btn-primary" type="submit">Proceed</button> 
        </div>  

      </div>  
    </div>  
  </div>
  <?php echo form_close(); ?>
</div>