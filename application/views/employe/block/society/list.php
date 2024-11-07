<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Manage Club</b></h4>
                </div>

                <div class="col-sm-2">
                
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="clubSearchBtn">Search</button>
                <a href="{site_url}employe/society" class="btn btn-secondary">View All</a>
                <a href="{site_url}employe/society/addClub" class="btn btn-primary">+Add Club</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="clubDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Club Name</th>
                      <th>Member Limit</th>
                      <th>Club Amount</th>
                      <th>Per Member Amount</th>
                      <th>Commission</th>
                      <th>Tenure</th>
                      <th>Min Bid Amount</th>
                      <th>Start Date</th>
                      <th>Start Time</th>
                      <th>Reserve No</th>
                      <th>Status</th>
                      <th>Created</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Club Name</th>
                      <th>Member Limit</th>
                      <th>Club Amount</th>
                      <th>Per Member Amount</th>
                      <th>Commission</th>
                      <th>Tenure</th>
                      <th>Min Bid Amount</th>
                      <th>Start Date</th>
                      <th>Start Time</th>
                      <th>Reserve No</th>
                      <th>Status</th>
                      <th>Created</th>
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

