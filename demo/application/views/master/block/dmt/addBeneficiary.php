{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add New Beneficiary</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('master/transfer/beneficiaryAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $mobile;?>" name="accountMobile">
              <div class="row">
              
			        <div class="col-sm-3">
              <div class="form-group">
              <label><b>Account Holder Name*</b></label>
              <input type="text" class="form-control" name="account_holder_name" placeholder="Account Holder Name">
              <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Beneficiary Mobile No.*</b></label>
              <input type="text" class="form-control" name="ben_mobile" placeholder="Beneficiary Mobile No.">
              <?php echo form_error('ben_mobile', '<div class="error">', '</div>'); ?>  
              <p>Note: OTP will be send on beneficiary mobile no.</p>
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Account No.*</b></label>
              <input type="text" class="form-control" name="account_no" placeholder="Account No.">
              <?php echo form_error('account_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>IFSC*</b></label>
              <input type="text" class="form-control" name="ifsc" placeholder="IFSC">
              <?php echo form_error('ifsc', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Bank Name*</b></label>
              <input type="text" class="form-control" name="bank_name" placeholder="Bank Name">
              <?php echo form_error('bank_name', '<div class="error">', '</div>'); ?>  
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




