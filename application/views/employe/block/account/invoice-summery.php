<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-7">
                <h4><b>Invoice List</b></h4>
                </div>
                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                <input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo $invoice_id ?>">
                </div>
                <div class="col-sm-3">
                  <button type="button" class="btn btn-success" id="invoiceSummerySearchBtn">Search</button>
                </div>
               </div>  
              
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="invoiceSummeryDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Invoice ID</th>
                      <th>Member</th>
                      <th>Issue Date</th>
                      <th>Action</th>

                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Invoice ID</th>
                      <th>Member</th>
                      <th>Issue Date</th>
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

