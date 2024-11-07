<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>MATM Transaction Report</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control datepick" placeholder="Date" autocomplete="off" name="date" id="date" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="matmHistorySearchBtn">Search</button>
                <a href="{site_url}master/report/matmHistory" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="matmHistoryDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Member</th>
                      <th>Amount</th>
                      <th>TxnID</th>
                      <th>TxnType</th>
                      <th>BankRRN</th>
                      <th>MPOS</th>
                      <th>Card</th>
                      <th width="50">Status</th>
                      <th>Datetime</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Member</th>
                      <th>Amount</th>
                      <th>TxnID</th>
                      <th>TxnType</th>
                      <th>BankRRN</th>
                      <th>MPOS</th>
                      <th>Card</th>
                      <th width="50">Status</th>
                      <th>Datetime</th>
                      
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

