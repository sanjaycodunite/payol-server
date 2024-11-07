<div class="container-fluid">
<div class="row">
<div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Success</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessIciciBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>



                <div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Pending</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPendingIciciBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>


               
                <div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFailedIciciBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
</div>
</div>
  
<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-12">
                <h4><b>ICICI AEPS Payout Report</b></h4>
                </div>

                   <div class="col-sm-2">
                <select class="form-control" id="status">
                  <option value="0">All Status</option>
                  
                  <option value="2" <?php if($status == 2){ ?> selected="selected" <?php } ?>>Pending</option>
                  <option value="3" <?php if($status == 3){ ?> selected="selected" <?php } ?>>Success</option>
                  <option value="4" <?php if($status == 4){ ?> selected="selected" <?php } ?>>Failed</option>
                </select>
                </div>

               
                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="Start Date" autocomplete="off" name="from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="End Date" autocomplete="off" name="to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>



                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="newTransferSearchBtn">Search</button>
                <a href="{site_url}distributor/transfer/newPayoutReport" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="newMoneyTransferDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Account</th>
                      <th>Amount</th>
                      <th>Txn Type</th>
                      <th>Txn ID</th>
                      <th>RRN</th>
                      <th>Status</th>
                      <th>Date Time</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Account</th>
                      <th>Amount</th>
                      <th>Txn Type</th>
                      <th>Txn ID</th>
                      <th>RRN</th>
                      <th>Status</th>
                      <th>Date Time</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
