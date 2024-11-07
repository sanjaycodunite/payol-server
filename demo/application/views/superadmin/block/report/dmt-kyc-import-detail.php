<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              
              <div class="row">
                <div class="col-sm-8">
                <h4><b>DMT KYC Import File Data</b></h4>
                </div>

                

                <div class="col-sm-4 text-right">
                  <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>
                

               </div>  
              
            </div>
            <div class="card-body">
              <input type="hidden" id="file_id" value="{file_id}">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dmtKycImportFileDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Agent ID</th>
                      <th>Agent Name</th>
                      <th>Agent Mobile</th>
                      <th>Is Match?</th>
                      <th>Member</th>
                      <th>Account</th>
                      <th>System Message</th>
                      <th>Datetime</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Agent ID</th>
                      <th>Agent Name</th>
                      <th>Agent Mobile</th>
                      <th>Is Match?</th>
                      <th>Member</th>
                      <th>Account</th>
                      <th>System Message</th>
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

