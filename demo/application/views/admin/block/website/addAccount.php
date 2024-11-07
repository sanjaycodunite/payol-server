{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add/Update Account Detail</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/website/saveAccount', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Bank Name</b></label>
              <input type="text" class="form-control" name="bank_name" id="bank_name" value="<?php echo $accountData['bank_name']; ?>" placeholder="Bank Name">
              <?php echo form_error('bank_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Branch</b></label>
              <input type="text" class="form-control" value="<?php echo $accountData['branch']; ?>" name="branch" id="branch" placeholder="Branch Address">
              <?php echo form_error('branch', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Account Holder Name</b></label>
              <input type="text" class="form-control" value="<?php echo $accountData['account_holder_name']; ?>" name="account_holder_name" id="account_holder_name" placeholder="Account Holder Name">
              <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              </div>

              <div class="row">
              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Account No.</b></label>
              <input type="text" class="form-control" name="account_no" value="<?php echo $accountData['account_no']; ?>" id="account_no" placeholder="Account No.">
              <?php echo form_error('account_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-4">
              <div class="form-group">
              <label><b>IFSC</b></label>
              <input type="text" class="form-control" value="<?php echo $accountData['ifsc']; ?>" name="ifsc" id="ifsc" placeholder="IFSC">
              <?php echo form_error('ifsc', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Phonepe Number</b></label>
              <input type="text" class="form-control" value="<?php echo $accountData['phonepe']; ?>" name="phonepe" id="phonepe" placeholder="Phonpe Mobile Number">
              <?php echo form_error('phonepe', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


              <div class="col-sm-4">
              <div class="form-group">
              <label><b>Google Pay Number</b></label>
              <input type="text" class="form-control" value="<?php echo $accountData['google_pay']; ?>" name="google_pay" id="google_pay" placeholder="Google Pay Mobile Number">
              <?php echo form_error('google_pay', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              </div>
              
              
              
              
              </div>

               


              
        <div class="card-footer py-3 text-right">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




