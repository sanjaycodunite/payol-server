 <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <?php
        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
       
      
    ?>
      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{site_url}superadmin/dashboard">
        <div class="sidebar-brand-icon">
        <img src="{site_url}<?php echo $accountData['image_path']; ?>" />
        </div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{site_url}superadmin/dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

  
      
      <hr class="sidebar-divider my-0">
	  
     




<li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSix" aria-expanded="true" aria-controls="collapseThree">
          <i class="fa fa-users"></i>
          <span>Account</span>
        </a>
        <div id="collapseSix" <?php if($content_block == 'account/accountList' || $content_block == 'account/addAccount' || $content_block == 'account/editAccount' ) { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="headingThree" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Account:</h6>
            <a class="collapse-item" href="{site_url}superadmin/account/addAccount">Create Account</a>
          <a class="collapse-item" href="{site_url}superadmin/account/accountList"> View Account</a>
          <a class="collapse-item" href="{site_url}superadmin/account/requestList"> Request Account</a>
          </div>
        </div>
      </li>


      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEmploye" aria-expanded="true" aria-controls="collapseThree">
          <i class="fa fa-user"></i>
          <span>Employe Management</span>
        </a>
        <div id="collapseEmploye" <?php if($content_block == 'employe/employeList' || $content_block == 'employe/addEmploye' || $content_block == 'employe/editEmploye' || $content_block == 'employe/addRole' || $content_block == 'employe/editRole' || $content_block == 'employe/roleList') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="headingThree" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Employe Management:</h6>
            <a class="collapse-item" href="{site_url}superadmin/employe/addEmploye">Add Employe</a>
            <a class="collapse-item" href="{site_url}superadmin/employe/employeList">View Employe</a>
            <a class="collapse-item" href="{site_url}superadmin/employe/addRole">Add Role</a>
            <a class="collapse-item" href="{site_url}superadmin/employe/roleList">View Role</a>
          </div>
        </div>
      </li>


      <hr class="sidebar-divider my-0">


      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse11" aria-expanded="true" aria-controls="collapse11">
          <i class="fa fa-list"></i>
          <span>Master Setting</span>
        </a>
        <div id="collapse11" <?php if($content_block == 'master/commission' || $content_block == 'master/bbpsCommission' || $content_block == 'master/wallet' || $content_block == 'master/transfer-commision' || $content_block == 'master/aeps-commision' || $content_block == 'master/service') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Master Setting:</h6>
            <a class="collapse-item" href="{site_url}superadmin/master/commission">Recharge Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/master/bbpsLiveCommission">BBPS Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/master/transferCommision">Aeps Payout Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/master/moneyTransferCommision">Open Payout Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/master/dmtCharge">DMT Charge</a>
            <a class="collapse-item" href="{site_url}superadmin/master/aepsCommision">AEPS Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/master/autoSettlement">On/Off Auto Settlement</a>
            <a class="collapse-item" href="{site_url}superadmin/master/autoSettlementTime">Settlement Timezone</a>
            <a class="collapse-item" href="{site_url}superadmin/master/accountVerfiyCharge">Account Verify Charge</a>
            <a class="collapse-item" href="{site_url}superadmin/master/nsdlPancardCharge">NSDL Pancard Charge</a>
            <a class="collapse-item" href="{site_url}superadmin/master/disableCollectionQr">Disable Collection QR</a>
            <a class="collapse-item" href="{site_url}superadmin/master/disableCashQr">Disable Cash QR</a>
            <a class="collapse-item" href="{site_url}superadmin/master/bbpsOperator">BBPS Operator</a>
            <a class="collapse-item" href="{site_url}superadmin/master/prepaidOperator">Prepaid Operator</a>
            
                     
          </div>
        </div>
      </li>
      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
          <i class="fa fa-file"></i>
          <span>Main Wallet </span>
        </a>
        <div id="collapse9" <?php if($content_block == 'wallet/walletList' || $content_block == 'wallet/addWallet' || $content_block == 'member/fundTransferList' || $content_block == 'wallet/requestList' || $content_block == 'wallet/creditList' || $content_block == 'wallet/debitList' ) { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Main Wallet:</h6>
                         <a class="collapse-item" href="{site_url}superadmin/wallet/walletList">Wallet</a>
                         <a class="collapse-item" href="{site_url}superadmin/wallet/creditList">Credit Fund</a>
                         <a class="collapse-item" href="{site_url}superadmin/wallet/debitList">Debit Fund</a>
                         <a class="collapse-item" href="{site_url}superadmin/wallet/requestList">Fund Request List</a>
                        </div>
        </div>
      </li>
     
      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#aeps_settlement" aria-expanded="true" aria-controls="aeps_settlement">
          <i class="fa fa-file"></i>
          <span>Settlement Wallet</span>
        </a>
        <div id="aeps_settlement" <?php if($content_block == 'cwallet/walletList' || $content_block == 'cwallet/addWallet' || $content_block == 'member/fundTransferList' || $content_block == 'cwallet/requestList' || $content_block == 'cwallet/creditList' || $content_block == 'cwallet/debitList' ) { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Settlement Wallet:</h6>
              <a class="collapse-item" href="{site_url}superadmin/cwallet/walletList">Wallet History</a>
              <a class="collapse-item" href="{site_url}superadmin/cwallet/accountWalletList">Account Wise Balance</a>
              <a class="collapse-item" href="{site_url}superadmin/cwallet/walletTransfer">VAN-Wallet Transfer</a>
              <a class="collapse-item" href="{site_url}superadmin/cwallet/debitWallet">Debit Wallet</a>
              <a class="collapse-item" href="{site_url}superadmin/cwallet/creditWallet">Credit Wallet</a>
            </div>
        </div>
      </li>
       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#virtualWallet" aria-expanded="true" aria-controls="virtualWallet">
          <i class="fa fa-file"></i>
          <span>Virtual Wallet</span>
        </a>
        <div id="virtualWallet" <?php if($content_block == 'vanwallet/walletList' || $content_block == 'vanwallet/addWallet') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Settlement Wallet:</h6>
              <a class="collapse-item" href="{site_url}superadmin/vanwallet/walletList">Wallet History</a>
              <a class="collapse-item" href="{site_url}superadmin/vanwallet/accountWalletList">Account Wise Balance</a>
              <a class="collapse-item" href="{site_url}superadmin/vanwallet/creditList">Credit Fund</a>
              <a class="collapse-item" href="{site_url}superadmin/vanwallet/debitList">Debit Fund</a>
              
            </div>
        </div>
      </li>
       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#commisionBalance" aria-expanded="true" aria-controls="commisionBalance">
          <i class="fa fa-file"></i>
          <span>Commission Wallet</span>
        </a>
        <div id="commisionBalance" <?php if($content_block == 'commission/walletList' || $content_block == 'commission/accountWiseBalance') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Commission Wallet:</h6>
              <a class="collapse-item" href="{site_url}superadmin/commission/walletList">Commission History</a>
              <a class="collapse-item" href="{site_url}superadmin/commission/accountWalletList">Account Wise Commission</a>
            </div>
        </div>
      </li>
      <hr class="sidebar-divider my-0">

      <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse25" aria-expanded="true" aria-controls="collapse25">
            <i class="fa fa-file"></i>
            <span>Package Management</span>
          </a>
          <div id="collapse25" class="collapse" aria-labelledby="heading25" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <h6 class="collapse-header">Package Management:</h6>
                           <a class="collapse-item" href="{site_url}superadmin/package/addPackage">Add Package</a>
                           <a class="collapse-item" href="{site_url}superadmin/package">View Package</a>
                           </div>
          </div>
        </li> 
        <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse88" aria-expanded="true" aria-controls="collapse88">
          <i class="fa fa-file"></i>
          <span>API Master</span>
        </a>
        <div id="collapse88" <?php if($content_block == 'api/addApi' || $content_block == 'api/apiList' || $content_block == 'api/operatorList' || $content_block == 'api/circleList' || $content_block == 'api/changeApi' || $content_block == 'api/amountFilter') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">API Master:</h6>
              <?php if($accountData['is_api_active']){ ?>
                         <a class="collapse-item" href="{site_url}superadmin/api/addApi">Add API</a>
                       <?php } ?>
                         <a class="collapse-item" href="{site_url}superadmin/api/apiList">API List</a>
                         <a class="collapse-item" href="{site_url}superadmin/api/changeApi">Change API</a>
                         
                        </div>
        </div>
      </li>
        <hr class="sidebar-divider my-0">

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse10" aria-expanded="true" aria-controls="collapse10">
          <i class="fa fa-list"></i>
          <span>Reports</span>
        </a>
        <div id="collapse10" <?php if($content_block == 'report/recharge-history' || $content_block == 'payment/list' || $content_block == 'report/loan-list' || $content_block == 'report/loan-detail' || $content_block == 'report/bbps-list' || $content_block == 'report/money-transfer-list' || $content_block == 'report/recharge-commission-list' || $content_block == 'report/fund-transfer-commission-list') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Reports:</h6>
            <a class="collapse-item" href="{site_url}superadmin/report/recharge">Recharge History</a>
            <a class="collapse-item" href="{site_url}superadmin/report/bbpsHistory">BBPS History</a>
            <a class="collapse-item" href="{site_url}superadmin/report/moneyTransfer">Payout Report</a>
            <a class="collapse-item" href="{site_url}superadmin/report/moneyTransferHistory">Open Payout Report</a>
            <a class="collapse-item" href="{site_url}superadmin/report/dmtHistory">DMT Report</a>
            <a class="collapse-item" href="{site_url}superadmin/report/aepsKyc">AEPS Kyc</a>
            <a class="collapse-item" href="{site_url}superadmin/report/aepsHistory">AEPS History</a>
            <a class="collapse-item" href="{site_url}superadmin/report/matmHistory">MATM History</a>
            <a class="collapse-item" href="{site_url}superadmin/report/topupHistory">PG History</a>
            <a class="collapse-item" href="{site_url}superadmin/report/virtualHistory">Virtual Acc. History</a>
            <a class="collapse-item" href="{site_url}superadmin/report/fundTransferCommision">Fund Transfer Commission</a>
            <a class="collapse-item" target="_blank" href="{site_url}superadmin/report/liveRecharge">Live Recharge</a>
            <a class="collapse-item" href="{site_url}superadmin/report/upiCollectionReport">UPI Collection Report</a> 
            <a class="collapse-item" href="{site_url}superadmin/report/upiCashReport">UPI Cash Report</a> 
            <a class="collapse-item" href="{site_url}superadmin/report/cashDepositeReport">Cash Deposite Report</a> 
            <a class="collapse-item" href="{site_url}superadmin/report/utiPancardReport">UTI Pancard Report</a> 
            <a class="collapse-item" href="{site_url}superadmin/report/nsdlList">NSDL Pancard Report</a> 
            <a class="collapse-item" href="{site_url}superadmin/report/currentAccountReport">Current Account Report</a> 
            <a class="collapse-item" href="{site_url}superadmin/report/axisAccountReport">Axis Account Report</a> 
            <a class="collapse-item" href="{site_url}superadmin/report/changeAccountList">Account Request List</a>
            <a class="collapse-item" href="{site_url}superadmin/report/rechargeCommision">Recharge Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/report/bbpsCommision">BBPS Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/report/openPayoutCommision">Payout Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/report/moneyTransferCommision">Money Trans. Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/report/aepsCommision">Member AEPS Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/report/cashDepositeCommision">Cash Deposite Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/report/upiCommision">UPI Collection Com.</a>
            <a class="collapse-item" href="{site_url}superadmin/report/upiCashCommision">UPI Cash Commission</a>
            <a class="collapse-item" href="{site_url}superadmin/report/moveMemberReport">Move Member Report</a>
            
            <a class="collapse-item" href="{site_url}superadmin/report/dmtKycImport">DMT Kyc Import</a>
            <a class="collapse-item" href="{site_url}superadmin/report/dmtKycExport">DMT Kyc Export</a>
          </div>
        </div>
      </li>
       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse12" aria-expanded="true" aria-controls="collapse12">
          <i class="fa fa-history"></i>
          <span>System</span>
        </a>
        <div id="collapse12" <?php if($content_block == 'system/logList' || $content_block == 'system/callBackLogList') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">System:</h6>
                         <a class="collapse-item" href="{site_url}superadmin/system/logList">View Log</a>
                         <a class="collapse-item" href="{site_url}superadmin/system/callBackLogList">Callback Log</a>
                         <a class="collapse-item" href="{site_url}superadmin/system/settlementLogList">Settlement Log</a>
                         <a class="collapse-item" href="{site_url}superadmin/system/dmtLogList">DMT Log</a>
                         <a class="collapse-item" href="{site_url}superadmin/system/vanLogList">VAN Log</a>
                         <a class="collapse-item" href="{site_url}superadmin/system/matmLogList">MATM Log</a>
                         <a class="collapse-item" href="{site_url}superadmin/system/nsdlLogList">NSDL Log</a>
                         
                        </div>
        </div>
      </li> 
       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse13" aria-expanded="true" aria-controls="collapse13">
          <i class="fa fa-cog"></i>
          <span>Setting</span>
        </a>
        <div id="collapse13" <?php if($content_block == 'setting/profile' || $content_block == 'setting/change-password') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Setting:</h6>
                         <a class="collapse-item" href="{site_url}superadmin/setting/profile">Profile</a>
                         <a class="collapse-item" href="{site_url}superadmin/setting/changePassword">Change Password</a>
                         <a class="collapse-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
                         
                        </div>
        </div>
      </li> 
     <hr class="sidebar-divider">




    









      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>

      <!-- End of Sidebar -->

          <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          
          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <li class="nav-item dropdown" style="padding-top: 25px; padding-right: 25px;">
            <h6>
            <b>Commision Balance : &#8377; <?php echo number_format($this->User->getSuperadminAepsCommisionBlance(),2); ?></b>
            </h6>  
            </li>
             <li class="nav-item dropdown" style="padding-top: 25px; padding-right: 25px;">
            <h6>
            <b>S-Wallet Balance : &#8377; <?php echo number_format($this->User->getAccountCollectionWalletBalanceSP($loggedUser['id'],1,2),2); ?></b>
            </h6>  
            </li>
            

            
            <div class="topbar-divider d-none d-sm-block"></div>
            
            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                  <?php echo $accountData['title']; ?>

                </span>
                <img class="img-profile rounded-circle" src="{site_url}skin/admin/img/user.png">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{site_url}superadmin/setting/profile">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                               
                  Logout
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->