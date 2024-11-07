<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-7">
                <h4><b>Complain List</b></h4>
                </div>
                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>
                <div class="col-sm-3">
                  <button type="button" class="btn btn-success" id="complainSearchBtn">Search</button>
                </div>
               </div>  
              
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="complainDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Type</th>
                      <th>ComplainID</th>
                      <th>Recharge ID</th>
                      <th>Mobile</th>
                      <th>Amount</th>
                      <th>Description</th>
                      <th>Datetime</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Type</th>
                      <th>ComplainID</th>
                      <th>Recharge ID</th>
                      <th>Mobile</th>
                      <th>Amount</th>
                      <th>Description</th>
                      <th>Datetime</th>
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

