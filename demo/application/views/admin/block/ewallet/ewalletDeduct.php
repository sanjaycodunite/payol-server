{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>E-Wallet Deduct</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/ewallet/ewalletDeductAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label><b>User Type*</b></label>
                    <select class="form-control" name="user_type">
                      <option value="">Select User Type</option>
                      <option value="0">All</option>
                      <option value="3">Master Distributor</option>
                      <option value="4">Distributor</option>
                      <option value="5">Retailer</option>
                      <option value="8">User</option>
                      <option value="6">API User</option>
            				</select>
                    <?php echo form_error('user_type', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
			         
                <div class="col-sm-6">
                  <div class="form-group">
                    <label><b>Amount*</b></label>
                    <input type="text" class="form-control" name="amount" placeholder="Amount">
                    <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                </div>

                <div class="row">
                  <div class="col-sm-12">
                    <label><b>Description</b></label>
                    <textarea class="form-control" name="description" placeholder="Description" rows="4"></textarea>
                    <?php echo form_error('description', '<div class="error">', '</div>'); ?>
                  </div>
                </div>


            </div>
          <div class="card-footer py-3 text-right">
            <button type="submit" class="btn btn-success">Submit</button>
            <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
 <?php echo form_close(); ?>     
    </div>
  </div>




