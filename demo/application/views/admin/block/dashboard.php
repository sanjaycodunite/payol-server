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
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}admin/member/mdMemberList">Total Master Distributor</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalMDMember}</div>
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
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}admin/member/distributorList">Total Distributor</a></div>
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
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><a href="{site_url}admin/member/retailerList">Total Retailer</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalRetailerMember}</div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
            
            <?php if($accountData['is_disable_user_role'] == 0){ ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning blw2 shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><a href="{site_url}admin/member/apiMemberList">Total User</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalUserMember}</div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>
            
            <?php if($accountData['is_disable_api_role'] == 0){ ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning blw2 shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><a href="{site_url}admin/member/apiMemberList">Total API Member</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalAPIMember}</div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>

             <?php if($accountData['is_employe_panel'] == 1){ ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning blw2 shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><a href="{site_url}admin/employe/employeList">Total Employe</a></div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{totalEmpMember}</div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>

          </div>
          
          <style type="text/css">
            #example1_filter{display: none;}
            #example1_length{display: none;}
          </style>
          <div class="card_tab_header">
          <div class="card_tabs_list nav nav-tabs" id="nav-tab" role="tablist">
    <button class="nav-link active" id="nav-balance-tab" data-toggle="tab" data-target="#nav-balance" type="button" role="tab" aria-controls="nav-balance" aria-selected="true">Balance Summary</button>
    <button class="nav-link" id="nav-commission-tab" data-toggle="tab" data-target="#nav-commission" type="button" role="tab" aria-controls="nav-commission" aria-selected="false">Commission Summary</button>
    <button class="nav-link" id="nav-api-tab" data-toggle="tab" data-target="#nav-api" type="button" role="tab" aria-controls="nav-api" aria-selected="false">API Summary</button>
  </div></div>
          <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-balance" role="tabpanel" aria-labelledby="nav-balance-tab">
          <div class="card shadow mb-4">
          <div class="card-body">
          <div class="row">
              <div class="col-md-6">
                <h1 class="h3 mb-0 text-gray-800">Wallet Balance Summary</h1>
              </div>
              <div class="col-md-12">
                <table class="table table-bordered table-striped" id="example1" width="100%" cellspacing="0">
                  <thead>
                  <tr>
                    <th>Member Type</th>
                    <th>Main Wallet Balance</th>
                  </tr>
                  </thead>

                  <tbody>
                    <tr>
                      <td>Master Distributor</td>
                      <td>₹ <?php echo number_format($master_distributor_total_wallet_balance,2); ?></td>
                    </tr>

                    <tr>
                      <td>Distributor</td>
                      <td>₹ <?php echo number_format($distributor_total_wallet_balance,2); ?></td>
                    </tr>

                    <tr>
                      <td>Retailer</td>
                      <td>₹ <?php echo number_format($retailer_total_wallet_balance,2); ?></td>
                    </tr>
                    <?php if($accountData['is_disable_api_role'] == 0){ ?>
                    <tr>
                      <td>API User</td>
                      <td>₹ <?php echo number_format($api_user_total_wallet_balance,2); ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($accountData['is_disable_user_role'] == 0){ ?>
                    <tr>
                      <td>User</td>
                      <td>₹ <?php echo number_format($user_total_wallet_balance,2); ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>

                  <tfoot>
                    <tr>
                     <td><b>Total</b></td>
                     <td><b>₹ <?php echo number_format($total_wallet_balance,2); ?></b></td>
                    </tr>

                  </tfoot>
                  
                </table>
              </div>
              
            </div>
          </div>
          </div></div>  
          <style type="text/css">
            #example_filter{display: none;}
            #example_length{display: none;}
          </style>
          
          <div class="tab-pane fade" id="nav-commission" role="tabpanel" aria-labelledby="nav-commission-tab">
           <div class="card shadow mb-4">
          <div class="card-body">
          <div class="row">
              <div class="col-md-12">
                <h1 class="h3 mb-0 text-gray-800">Today Distribute Commission Summary</h1>
              </div>
              <div class="col-md-12">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                  <tr>
                    <th>Member Type</th>
                    <th>Total Commission</th>
                  </tr>
                  </thead>

                  <tbody>
                    <tr>
                      <td>Master Distributor</td>
                      <td>₹ <?php echo number_format($md_commission,2); ?></td>
                    </tr>

                    <tr>
                      <td>Distributor</td>
                      <td>₹ <?php echo number_format($d_commission,2); ?></td>
                    </tr>

                    <tr>
                      <td>Retailer</td>
                      <td>₹ <?php echo number_format($r_commission,2); ?></td>
                    </tr>
                    <?php if($accountData['is_disable_api_role'] == 0){ ?>
                    <tr>
                      <td>API User</td>
                      <td>₹ <?php echo number_format($api_commission,2); ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($accountData['is_disable_user_role'] == 0){ ?>
                    <tr>
                      <td>User</td>
                      <td>₹ <?php echo number_format($user_commission,2); ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>

                  <tfoot>
                    <tr>
                     <td><b>Total</b></td>
                     <td><b>₹ <?php echo number_format($total_distribute_commision,2); ?></b></td>
                    </tr>

                  </tfoot>
                  
                </table>
              </div>
              
            </div>
          </div>
          </div></div> 
         
         <div class="tab-pane fade" id="nav-api" role="tabpanel" aria-labelledby="nav-api-tab">
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
          </div></div>
          
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
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}admin/report/recharge/2">Total Success</a></div>
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
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}admin/report/recharge/1">Total Pending</a></div>
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
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}admin/report/recharge/3">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFailedBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div><div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="Dispute-b card border-left-danger shadow h-100 py-2">
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
          <div class="pay_history cb_rh_body card-body">
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
                </div><div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="Dispute-b card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Dispute</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMoneyDisputeBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
              
            </div>
          </div>
          </div>
            


              <div class="cb_rh card shadow mb-4">
          <div class="pay_history cb_rh_body card-body">
          <div class="row">
              <div class="col-md-12">
                <h1 class="h3 mb-0 text-gray-800">AEPS History</h1>
              </div>
              
                
                <div class="col-xl-3 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Success</a></div>
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
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAepsFailedBlock">&#8377; 0.00 / 0</div>
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
