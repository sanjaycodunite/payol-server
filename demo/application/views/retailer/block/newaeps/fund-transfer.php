   <div class="row">
    <div class="col-sm-12">
     {system_message}    
     {system_info} 
    </div>  
  </div>
  <div class="row">

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
      <div class="row">
      <div class="col-sm-8">
	    <h5>Payout</h5>
	    </div>
      <div class="col-sm-4 text-right">
      <!-- <h5>AEPS Wallet Balance - INR <?php echo number_format($accountDetail['aeps_wallet_balance'],2); ?></h5> -->
      </div>
	</div>
      </div>

      <?php echo form_open_multipart('retailer/newaeps/fundTransferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
			
      <input type="hidden" name="bene_id" value="<?=$bene_id?>">

      <input type="hidden" id="user-wallet-balance" vlaue="<?php echo $accountDetail['wallet_balance']; ?>" />

      <div class="card-body">

        <div class="row">
              
               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Account Holder Name*</b></label>
              <input type="text" class="form-control" readonly="" value="<?php echo set_value('account_holder_name') ? set_value('account_holder_name') : $benificaryData['account_holder_name']; ?>" name="account_holder_name" placeholder="Account Holder Name">
              <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Account No.*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('account_no') ? set_value('account_no') : $benificaryData['account_number']; ?>" readonly="" name="account_no" placeholder="Account No.">
              <?php echo form_error('account_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
             
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>IFSC*</b></label>
              <input type="text" class="form-control" readonly="" value="<?php echo set_value('ifsc') ? set_value('ifsc') : $benificaryData['ifsc']; ?>" name="ifsc" placeholder="IFSC">
              <?php echo form_error('ifsc', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Amount*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('amount'); ?>" name="amount" id="amount" placeholder="Amount">
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Transaction Password*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('txn_pass'); ?>" name="txn_pass" id="txn_pass" placeholder="Transaction Password">
              <?php echo form_error('txn_pass', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

            </div>

        
      </div>
       	  <div class="card-footer">
       <button class="btn btn-primary" type="submit">Transfer</button> 
      </div>
       


    </div>

  </div>  


  </div> 


</div>

<?php echo form_close(); ?>
