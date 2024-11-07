<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-9">
                <h4><b>UPI Cash QR List</b></h4>
                </div>
                <div class="col-sm-3">
                <a href="{site_url}employe/upi/generateCashQr" class="btn btn-primary">Generate QR</a>
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>
                
               </div>  
              
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="upiCashQrDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Txn ID</th>
                      <th>QR</th>
                      <th>Ref ID</th>
                      <th>QR Str</th>
                      <th>Is Map?</th>
                      <th>Map Member</th>
                      <th>Date Time</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Txn ID</th>
                      <th>QR</th>
                      <th>Ref ID</th>
                      <th>QR Str</th>
                      <th>Is Map?</th>
                      <th>Map Member</th>
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

