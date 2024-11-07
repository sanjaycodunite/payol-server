<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Bill Pay Report</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control datepick" placeholder="Date" autocomplete="off" name="date" id="date" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="bbpsSearchBtn">Search</button>
                <a href="{site_url}admin/report/bbps" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="bbpsDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>RechargeID</th>
                      <th>MemberID</th>
                      <th>Operator</th>
                      <th>K/Consumer Number</th>
                      <th>Bill Unit/Customer</th>
                      <th>Amount</th>
                      <th>OB</th>
                      <th>CB</th>
                      <th>TxnID</th>
                      <th>Date Time</th>
                      <th>Status</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>RechargeID</th>
                      <th>MemberID</th>
                      <th>Operator</th>
                      <th>K/Consumer Number</th>
                      <th>Bill Unit/Customer</th>
                      <th>Amount</th>
                      <th>OB</th>
                      <th>CB</th>
                      <th>TxnID</th>
                      <th>Date Time</th>
                      <th>Status</th>
                      
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

