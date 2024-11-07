{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Bank Transfer</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/cwallet/transferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Bank Account*</b></label>
              <select class="form-control selectpicker" name="bene_id" id="bene_id" data-live-search="true">
                <option value="">Select Bank Account</option>
                <option value="1"><?php echo $account_holder_name.' ('.$account_number.')'; ?></option>
              </select>
              <?php echo form_error('bene_id', '<div class="error">', '</div>'); ?>  
              <p>Please update your account detail in Setting - My Profile</p>
              </div>
              </div>
              
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Available Balance</b></label>
              <input type="text" class="form-control" value="<?php echo $availableBalance; ?>" readonly="readonly">
              </div>
              </div>

              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Transfer Limit Amount</b></label>
              <input type="text" class="form-control" value="<?php echo $tranferLimit; ?>" readonly="readonly">
              </div>
              </div>
              
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Transfer Amount*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('amount'); ?>" name="amount" id="amount" placeholder="Amount">
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Transaction Type*</b></label> <br />
              <input type="radio" name="txnType" value="RGS" id="txnType1"> <label for="txnType1">NEFT <font style="font-size: 12px;">*No Limit</font></label><br />
              <input type="radio" name="txnType" value="RTG" id="txnType2"> <label for="txnType2">RTGS <font style="font-size: 12px;">*Minimum 2 Lakh</font></label><br />
              <input type="radio" name="txnType" value="IFS" checked="checked" id="txnType3"> <label for="txnType3">IMPS <font style="font-size: 12px;">*Maximum 2 Lakh</font></label><br />
              <?php echo form_error('txnType', '<div class="error">', '</div>'); ?>  
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




