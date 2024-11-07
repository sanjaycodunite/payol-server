{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b> ICICI AEPS Payout</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('distributor/transfer/newPayoutTransferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <input type="hidden" name="bene_id" value="{bene_id}"> 
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Mobile*</b></label>
              <input type="text" class="form-control" name="mobile" value="<?php echo set_value('mobile') ? set_value('mobile') : $mobile; ?>" placeholder="Mobile">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Account Holder Name*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('account_holder_name') ? set_value('account_holder_name') : $account_holder_name; ?>" name="account_holder_name" placeholder="Account Holder Name" readonly = "">
              <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Account No.*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('account_no') ? set_value('account_no') : $account_no; ?>" readonly="" name="account_no" placeholder="Account No.">
              <?php echo form_error('account_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Confirm Account No.*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('confirm_account_no') ? set_value('confirm_account_no') : $account_no; ?>" readonly="" name="confirm_account_no" placeholder="Confirm Account No.">
              <?php echo form_error('confirm_account_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>IFSC*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('ifsc') ? set_value('ifsc') : $ifsc; ?>" readonly="" name="ifsc" placeholder="IFSC">
              <?php echo form_error('ifsc', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
        <div class="col-sm-2">
              <div class="form-group">
              <label><b>Amount*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('amount'); ?>" name="amount" id="amount" placeholder="Amount">
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


              <div class="col-sm-2">
                <div class="form-group">
                  <label><b>Transfer Mode</b></label>
                  <select name="mode" class="form-control selectpicker" data-live-search="true">
                    <option value="IMPS">IMPS</option>
                    <option value="RTGS">RTGS</option>
                    <option value="NEFT">NEFT</option>
                  </select>
                </div>
              </div>


            </div>
         
              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to transfer this transaction?')">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




