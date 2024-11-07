<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Wallet Deduct Report</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                 <select class="form-control" name="wallet_type" id="wallet_type">
                   <option value="">Wallet Type</option>
                   <option value="1">R-Wallet</option>
                   <option value="2">E-Wallet</option>
                 </select> 
                </div>

                <div class="col-sm-2">
                 <select class="form-control" name="user_type" id="user_type">
                   <option value="">User Type</option>
                   <option value="0">All</option>
                   <option value="3">Master Distributor</option>
                   <option value="4">Distributor</option>
                   <option value="5">Retailer</option>
                   <option value="8">User</option>
                   <option value="6">API User</option>
                 </select> 
                </div>


                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="Date" autocomplete="off" name="date" id="date" />
                </div>


                <div class="col-sm-2">
                <button type="button" class="btn btn-success btn-sm" id="walletDeductSearchBtn" style="height: 38px;">Search</button>
                <a href="{site_url}superadmin/report/walletDeductReport" class="btn btn-secondary btn-sm" style="height: 38px; padding-top: 8px;">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="walletDeductDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>User Type</th>
                      <th>Wallet Type</th>
                      <th>Amount</th>
                      <th>Description</th>
                      <th>Total User</th>
                      <th>Total Deduct User</th>
                      <th>Total Deduct Amount</th>
                      <th>Datetime</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>User Type</th>
                      <th>Wallet Type</th>
                      <th>Amount</th>
                      <th>Description</th>
                      <th>Total User</th>
                      <th>Total Deduct User</th>
                      <th>Total Deduct Amount</th>
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

