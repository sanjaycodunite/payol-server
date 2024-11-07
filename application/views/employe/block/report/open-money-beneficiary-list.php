<div class="container-fluid">

<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Open Money Beneficiary List</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control datepick" placeholder="Date" name="date" id="date" autocomplete="off" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="openMemberSearchBtn">Search</button>
                <a href="{site_url}employe/report/openBeneficiaryList" class="btn btn-secondary">View All</a>
                
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="openBeneficiaryDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Account Holder Name</th>
                      <th>Email</th>
                      <th>Mobile</th>
                      <th>Account No</th>
                      <th>IFSC</th>
                      <th>Beneficiary ID</th>
                      <th>Status</th>
                      <th>Date Time</th>


                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                       <th>#</th>
                      <th>MemberID</th>
                      <th>Account Holder Name</th>
                      <th>Email</th>
                      <th>Mobile</th>
                      <th>Account No</th>
                      <th>IFSC</th>
                      <th>Beneficiary ID</th>
                      <th>Status</th>
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

