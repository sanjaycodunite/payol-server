<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-3">
                <h4><b>BBPS Wallet History</b></h4>
                </div>

                <div class="col-sm-2">
                 <input type="text" class="form-control datepick" name="date" id="date" placeholder="Date" autocomplete="off"> 
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-3">
                <button type="button" class="btn btn-success" id="bbpsWalletSearchBtn">Search</button>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="bbpsWalletDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>MemberID</th>
                      <th>Type</th>
                      <th>Before Amount</th>
                      <th>Cr/Dr Amount</th>
                      <th>After Amount</th>
                      <th>Description</th>
                      <th>Date Time</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>MemberID</th>
                      <th>Type</th>
                      <th>Before Amount</th>
                      <th>Cr/Dr Amount</th>
                      <th>After Amount</th>
                      <th>Description</th>
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

