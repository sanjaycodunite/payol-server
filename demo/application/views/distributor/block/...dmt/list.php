<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Transfer Report</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control datepick" placeholder="Date" name="date" autocomplete="off" id="date" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="dmtSearchBtn">Search</button>
                <a href="{site_url}distributor/dmt" class="btn btn-secondary">View All</a>
                <a href="{site_url}distributor/dmt/moneyTransfer" class="btn btn-success">Transfer Now</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dmtDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Beneficiary</th>
                      <th>Amount</th>
                      <th>Charge</th>
                      <th>Txn ID</th>
                      <th>RRN</th>
                      <th>Status</th>
                      <th>Invoice</th>
                      <th>Date Time</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Beneficiary</th>
                      <th>Amount</th>
                      <th>Charge</th>
                      <th>Txn ID</th>
                      <th>RRN</th>
                      <th>Status</th>
                      <th>Invoice</th>
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

