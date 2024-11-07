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
              <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><a href="{site_url}master/member/memberList">Total Member</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalMember}</div>
                    </div>
                    
                    
                  </div>
                </div>
              </div>
            </div>

            
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}master/member/distributorList">Total Distributor</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalDistributorMember}</div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><a href="{site_url}master/member/retailerList">Total Retailer</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalRetailerMember}</div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="cb_rh card shadow mb-4">
          <div class="cb_rh_body card-body">
          <div class="row">
              <div class="col-md-12">
                <h1 class="h3 mb-0 text-gray-800">Recharge History</h1>
              </div>
              
                
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}master/report/recharge/2">Total Success</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}master/report/recharge/1">Total Pending</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPendingBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}master/report/recharge/3">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFailedBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div><div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Dispute</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalDisputeBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
              
            </div>
          </div>
          </div>


          <div class="cb_rh card shadow mb-4">
          <div class="cb_rh_body card-body">
          <div class="row">
              <div class="col-md-12">
                <h1 class="h3 mb-0 text-gray-800">BBPS History</h1>
              </div>
              
                
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}retailer/report/bbpsHistory">Total Success</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBbpsSuccessBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}retailer/report/bbpsHistory">Total Pending</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBbpsPendingBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}retailer/report/bbpsHistory">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBbpsFailedBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
              
            </div>
          </div>
          </div>


             <div class="cb_rh card shadow mb-4">
          <div class="cb_rh_body card-body">
          <div class="row">
              <div class="col-md-12">
                <h1 class="h3 mb-0 text-gray-800">AEPS History</h1>
              </div>
             
                
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}retailer/aeps/transactionHistory">Today Success Aeps</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAepsSuccessBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>

                 <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-danger text-uppercase mb-1"><a href="{site_url}retailer/aeps/transactionHistory">Today Failed Aeps</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAepsFailedBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>


               
               
              
            </div>
          </div>
          </div>


          <div class="cb_rh card shadow mb-4">
          <div class="cb_rh_body card-body">
          <div class="row">
              <div class="col-md-12">
                <h1 class="h3 mb-0 text-gray-800">Payout History</h1>
              </div>
              
                
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Success</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMoneySuccessBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Pending</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMoneyPendingBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMoneyFailedBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
              
            </div>
          </div>
          </div>



           <div class="cb_rh card shadow mb-4">
          <div class="cb_rh_body card-body">
          <div class="row">
              <div class="col-md-12">
                <h1 class="h3 mb-0 text-gray-800">Money Transfer History</h1>
              </div>
              
                
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Success</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMoneyTransferSuccessBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Pending</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMoneyTransferPendingBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMoneyTransferFailedBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
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
