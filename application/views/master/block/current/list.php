<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-5">
                <h4><b>Current Account List</b></h4>
                </div>

                <div class="col-sm-2">
                 <input type="text" class="form-control datepick" name="date" id="date" placeholder="Date" autocomplete="off"> 
                </div>
                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-3">
                <button type="button" class="btn btn-success" id="currentAccountSearchBtn">Search</button>
                <a href="{site_url}master/current/openAccount" class="btn btn-primary">Open Account</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="currentAccountDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Email</th>
                      <th>Account Type</th>
                      <th>Pincode</th>
                      <th>Application No.</th>
                      <th>Tracker ID</th>
                      <th>Action</th>
                      <th>Datetime</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Email</th>
                      <th>Account Type</th>
                      <th>Pincode</th>
                      <th>Application No.</th>
                      <th>Tracker ID</th>
                      <th>Action</th>
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

