<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Money Transfer Commission Report</b></h4>
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="From Date" autocomplete="off" name="from_date" id="from_date" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="To Date" autocomplete="off" name="to_date" id="to_date" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-2">
                <button type="button" class="btn btn-success" id="moneyTransferCommisionSearchBtn">Search</button>
                
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="moneyTransferCommisionDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Txn ID</th>
                      <th>Amount</th>
                      <th>Commision</th>
                      <th>Type</th>
                      <th>Date Time</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Txn ID</th>
                      <th>Amount</th>
                      <th>Commision</th>
                      <th>Type</th>
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

