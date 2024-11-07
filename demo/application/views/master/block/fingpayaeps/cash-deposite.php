{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Cash Deposite</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('retailer/aeps/cashDepositeAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              
            <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Mobile No.*</b></label>
              <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile No.">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Account No*</b></label>
              <input type="text" class="form-control" name="account_no" id="account_no" placeholder="Account No.">
              <?php echo form_error('account_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
			         <div class="col-sm-3">
              <div class="form-group">
              <label><b>Amount*</b></label>
              <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount (In Numbers Only)" value="0">
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Remark*</b></label>
              <input type="text" class="form-control" name="remark" placeholder="Remark">
              <?php echo form_error('remark', '<div class="error">', '</div>'); ?>  
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

<!-- Modal -->
<div id="aepsResponseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">AEPS Response</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body aeps-response">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" width="100%" cellspacing="0">
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>CR/DR</th>
              <th>Amount</th>
              <th>Description</th>
            </tr>
          </table>
        </div>
      </div>
      
    </div>

  </div>
</div>




