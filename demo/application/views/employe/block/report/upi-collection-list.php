<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>UPI Collection Report</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control datepick" placeholder="Date" autocomplete="off" name="date" value="<?=date('Y-m-d')?>" id="date" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="upiSearchBtn">Search</button>
                <a href="{site_url}employe/report/upiCollectionReport " class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="upiDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>txnID</th>
                      <th>Bank RRN</th>
                      <th>Amount</th>
                      <th>Charge</th>
                      <th>Credit</th>
                      <th>VPA ID</th>
                      <th>Description</th>
                      <th>Date Time</th>
                      <th>Status</th>
                      <th>Action</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>txnID</th>
                      <th>Bank RRN</th>
                      <th>Amount</th>
                      <th>Charge</th>
                      <th>Credit</th>
                      <th>VPA ID</th>
                      <th>Description</th>
                      <th>Date Time</th>
                      <th>Status</th>
                      <th>Action</th>
                      
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
