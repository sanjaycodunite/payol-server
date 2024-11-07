<div class="container-fluid">

<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b><?php echo $memberData['name'].' ('.$memberData['user_code'].')'; ?> Downline</b></h4>
                </div>

               </div>  

            </div>
            <div class="card-body">
              <div class="table-responsive">
                <input type="hidden" id="memberID" value="{id}">
                <table class="table table-bordered table-striped" id="downlineDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Details</th>
                      <th>Main Wallet</th>
                      <th>Created</th>
                      <th>Status</th>
                      
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
                      
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

