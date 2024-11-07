<div class="container-fluid">
<div class="row">

<div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Current Balance</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalActualBalanceBlock">&#8377; 0.00</div>
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
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-4">
                <h4><b>UPI Balance Report</b></h4>
                </div>
                
                
                <div class="col-sm-2">
                  <select class="form-control" name="user_type" id="user_type">
                    <option value="">All User</option>
                    <?php
                    foreach($user_type as $list){
                    ?>
                    <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                  <?php } ?>
                  </select>
                </div>

                

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                
                <div class="col-sm-4">
                  <div class="form-group">
                  <button type="button" class="btn btn-success" id="upiBalanceReportSearchBtn">Search</button>
                  <a href="{site_url}employe/report/upiBalanceReport" class="btn btn-secondary">View All</a>
                 </div>
                </div>

               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="upiBalanceReportDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>User Type</th>
                      <th>Name</th>
                      <th>Current Balance</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>User Type</th>
                      <th>Name</th>
                      <th>Current Balance</th>
                      
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
</div>
