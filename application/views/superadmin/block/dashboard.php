        <!-- Begin Page Content -->
        <div class="container-fluid">
{system_message}               
              {system_info}
          <!-- Page Heading -->
          <div class="row" style="margin-bottom: 20px;">
              <div class="col-md-6">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
              </div>
              
            </div>

          <!-- Content Row -->
          <div class="row">

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="{site_url}superadmin/account/accountList">Total Account</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalAccount}</div>
                    </div>
                    
                    
                  </div>
                </div>
              </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}superadmin/account/accountList">Total Active Account</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalActiveAccount}</div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="card shadow mb-4">
          <div class="card-body">
          <div class="row">
              <div class="col-md-6">
                <h1 class="h3 mb-0 text-gray-800">API Summary</h1>
              </div>
              <div class="col-md-12">
                <table class="table table-bordered table-striped" id="dashboardApiDataTable" width="100%" cellspacing="0">
                  <thead>
                  <tr>
                    <th>API ID</th>
                    <th>API Name</th>
                    <th>Current Balance</th>
                    <th>Success Recharge</th>
                    <th>Failed Recharge</th>
                  </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>API ID</th>
                      <th>API Name</th>
                      <th>Current Balance</th>
                      <th>Success Recharge</th>
                      <th>Failed Recharge</th>
                    </tr>
                  </tfoot>
                  
                </table>
              </div>
              
            </div>
          </div>
          </div>

         
          <!-- Content Row -->

          

          <!-- Content Row -->
          

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
