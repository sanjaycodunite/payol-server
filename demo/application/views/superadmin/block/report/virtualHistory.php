<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-4">
                <h4><b>VAN Transaction Report</b></h4>
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

                <div class="col-sm-2">
                <button type="button" class="btn btn-success" id="virtualHistorySearchBtn">Search</button>
                <a href="{site_url}superadmin/report/virtualHistory" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="virtualHistoryDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>MemberID</th>
                      <th>Customer Code</th>
                      <th>Virtual Account</th>
                      <th>Mode</th>
                      <th>UTR</th>
                      <th>Client Account</th>
                      <th>Amount</th>
                      <th>Payer</th>
                      <th>Is Paid?</th>
                      <th>Datetime</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>MemberID</th>
                      <th>Customer Code</th>
                      <th>Virtual Account</th>
                      <th>Mode</th>
                      <th>UTR</th>
                      <th>Client Account</th>
                      <th>Amount</th>
                      <th>Payer</th>
                      <th>Is Paid?</th>
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

