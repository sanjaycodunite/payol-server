<div class="container-fluid">
<div class="row">
<div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Main Balance</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessRechargeBlock">&#8377; 0.00</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>

              
              
</div>
<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Retailer</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control datepick" placeholder="Date" name="date" id="date" autocomplete="off" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="retailerMemberSearchBtn">Search</button>
                <a href="{site_url}admin/member/retailerList" class="btn btn-secondary">View All</a>
                <a href="{site_url}admin/member/addMember" class="btn btn-primary">+Add Member</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="retailerMemberDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Details</th>
                      <th>Main Wallet</th>
                      <th>Created</th>
                      <th>Status</th>
                      <th>Action</th>
                      <th>NSDL AEPS</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Details</th>
                      <th>Main Wallet</th>
                      <th>Created</th>
                      <th>Status</th>
                      <th>Action</th>
                      <th>NSDL AEPS</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

