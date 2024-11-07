<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-12">
                <h4><b> Open Money Payout Report</b></h4>
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
                <button type="button" class="btn btn-success" id="newPayoutSearchBtn">Search</button>
                <a href="{site_url}portal/report/newPayoutReport" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="newPayoutTransferDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
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

