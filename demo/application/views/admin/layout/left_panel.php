<?php 
$account_id = $this->User->get_domain_account();
$accountData = $this->User->get_account_data($account_id);
$loggedUser = $this->User->getAdminLoggedUser(ADMIN_SESSION_ID);
$isInstantPayApiAllow = $this->User->get_admin_instant_cogent_api($account_id);
$activeGateway = $this->User->account_active_gateway();
?>
 <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{site_url}admin/dashboard">
        <div class="sidebar-brand-icon">
        <img src="{site_url}<?php echo $accountData['image_path']; ?>" class="img-responsive">
        </div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{site_url}admin/dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>
    
      
      
      <?php
       if(IS_SOCIETY== 1){
      ?>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClub" aria-expanded="true" aria-controls="collapseClub">
          <i class="fa fa-user"></i>
          <span>Society</span>
        </a>
        <div id="collapseClub" <?php if($content_block == 'society/clubCreate') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="headingThree" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Society:</h6>
            <a class="collapse-item" href="{site_url}admin/society">Club List</a>
            <a class="collapse-item" href="{site_url}admin/society/addClub">Create Club</a>
            
          </div>
        </div>
      </li>
     <?php } ?>

      <!-- <hr class="sidebar-divider my-0">
    
<li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSix" aria-expanded="true" aria-controls="collapseThree">
          <i class="fa fa-users"></i>
          <span>Whitelabel Account</span>
        </a>
        <div id="collapseSix" <?php if($content_block == 'account/accountList' || $content_block == 'account/addAccount' || $content_block == 'account/editAccount' ) { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="headingThree" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Whitelabel Account:</h6>
            <a class="collapse-item" href="{site_url}admin/account/addAccount">Create Account</a>
          <a class="collapse-item" href="{site_url}admin/account/accountList"> View Account</a>
          </div>
        </div>
      </li> -->

      <hr class="sidebar-divider my-0">

        <?php $activeService = $this->User->admin_active_service(); ?>
     <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
          <i class="fa fa-users"></i>
          <span>Member Management</span>
        </a>
        <div id="collapseThree" <?php if($content_block == 'member/memberList' || $content_block == 'member/addMember'  || $content_block == 'member/editMember'  || $content_block == 'member/mdMemberList' || $content_block == 'member/distributorList' || $content_block == 'member/retailerList' || $content_block == 'member/apiMemberList' || $content_block == 'member/userList') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="headingThree" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Member Management:</h6>
            <a class="collapse-item" href="{site_url}admin/member/addMember">Add Member</a>
            <a class="collapse-item" href="{site_url}admin/master/service">Manage Service</a>
            <a class="collapse-item" href="{site_url}admin/member/memberList">View All Member</a>
            <a class="collapse-item" href="{site_url}admin/member/mdMemberList">View Master Distributor</a>
            <a class="collapse-item" href="{site_url}admin/member/distributorList">View Distributor</a>
            <a class="collapse-item" href="{site_url}admin/member/retailerList">View Retailer</a>
            <?php if($accountData['is_disable_api_role'] == 0){ ?>
            <a class="collapse-item" href="{site_url}admin/member/apiMemberList">API Member</a>
            <?php } ?>
            <?php if($accountData['is_disable_user_role'] == 0){ ?>
            <a class="collapse-item" href="{site_url}admin/member/userList">Users</a>
            <?php } ?>
            <a class="collapse-item" href="{site_url}admin/master/moveMember">Move Member</a>
            <a class="collapse-item" href="{site_url}admin/report/moveMemberReport">Move Member Report</a>
            <a class="collapse-item" href="{site_url}admin/report/changeAccountList">Account Request List</a>

                <?php if(in_array(20, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/iciciChangeAccountList">ICICI Account Request List</a>
                <?php } ?>
                  <?php if(in_array(17, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/member/memberBeneficiaryList">Beneficiary List</a>
                <?php } ?>
            </div>
        </div>
      </li>


      <?php
       if($accountData['is_employe_panel'] == 1){
      ?>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEmploye" aria-expanded="true" aria-controls="collapseThree">
          <i class="fa fa-user"></i>
          <span>Employee Management</span>
        </a>
        <div id="collapseEmploye" <?php if($content_block == 'employe/employeList' || $content_block == 'employe/addEmploye' || $content_block == 'employe/editEmploye' || $content_block == 'employe/addRole' || $content_block == 'employe/editRole' || $content_block == 'employe/roleList') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="headingThree" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Employee Management:</h6>
            <a class="collapse-item" href="{site_url}admin/employe/addRole">Add Role</a>
            <a class="collapse-item" href="{site_url}admin/employe/roleList">View Role</a>
            <a class="collapse-item" href="{site_url}admin/employe/addEmploye">Add Employee</a>
            <a class="collapse-item" href="{site_url}admin/employe/employeList">View Employee</a>
            
          </div>
        </div>
      </li>
     <?php } ?>

    <!-- <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapscwallet" aria-expanded="true" aria-controls="collapscwallet">
          <i class="fa fa-file"></i>
          <span>Settlement Wallet</span>
        </a>
        <div id="collapscwallet" <?php if($content_block == 'cwallet/walletList') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Wallet Management:</h6>
              <a class="collapse-item" href="{site_url}admin/cwallet/walletList">Wallet History</a>
              <?php
               if($accountData['is_auto_bank_settlement'] == 1){
                ?>
                <a class="collapse-item" href="{site_url}admin/cwallet/bankTransfer">Bank Transfer</a>
                <a class="collapse-item" href="{site_url}admin/cwallet/bankTransferReport">Transfer Report</a>
                <?php
               }
              ?>    
            </div>
        </div>
      </li>  -->
      
      
       <?php
       if($accountData['is_generate_invoice'] == 1){
      ?>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInvoice" aria-expanded="true" aria-controls="collapseInvoice">
          <i class="fa fa-user"></i>
          <span>Invoice Management</span>
        </a>
        <div id="collapseInvoice" <?php if($content_block == 'account/dynamicInvoice' || $content_block == 'account/dynamicInvoice') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="headingThree" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Invoice Management:</h6>
            <a class="collapse-item" href="{site_url}admin/account/dynamicInvoice">Dynamic Invoice</a>
            <a class="collapse-item" href="{site_url}admin/account/manualInvoiceList">Manual Invoice</a>

            <a class="collapse-item" href="{site_url}admin/account/tdsInvoice">TDS Invoice</a>
            
          </div>
        </div>
      </li>
     <?php } ?>
     

       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsVanwallet" aria-expanded="true" aria-controls="collapsVanwallet">
          <i class="fa fa-file"></i>
          <span>Virtual Wallet</span>
        </a>
        <div id="collapsVanwallet" <?php if($content_block == 'vanwallet/walletList' || $content_block == 'vanwallet/account-detail') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Wallet Management:</h6>
              <a class="collapse-item" href="{site_url}admin/vanwallet/accountDetail">Virtual Account</a>
              <a class="collapse-item" href="{site_url}admin/vanwallet/walletList">Wallet History</a>
                         
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
              <a class="collapse-item" href="{site_url}admin/commission/walletList">Commission History</a>
              <!--<a class="collapse-item" href="{site_url}admin/commission/accountWalletList">Account Wise Commission</a>-->
            </div>
        </div>
      </li>

      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse8" aria-expanded="true" aria-controls="collapse8">
          <i class="fa fa-file"></i>
          <span>Member Wallet </span>
          <span class="menu_notification"><?php echo $this->User->getTotalUnreadRequest(); ?></span>
        </a>
        <div id="collapse8" <?php if($content_block == 'wallet/walletList' || $content_block == 'wallet/addWallet' || $content_block == 'member/fundTransferList' || $content_block == 'wallet/requestList' || $content_block == 'wallet/creditList' || $content_block == 'wallet/debitList' ) { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Wallet Management:</h6>
                         <?php if(in_array(1, $activeGateway)){ ?>
                         <a class="collapse-item" href="{site_url}admin/wallet/topupHistory">Topup History</a>
                         <?php } ?>
                         <a class="collapse-item" href="{site_url}admin/wallet/walletList">Member Wallet</a>
                         <a class="collapse-item" href="{site_url}admin/report/balanceReport">Wallet Balance</a>
                         <a class="collapse-item" href="{site_url}admin/wallet/creditList">Credit Fund</a>
                         <a class="collapse-item" href="{site_url}admin/wallet/debitList">Debit Fund</a>
                         <a class="collapse-item" href="{site_url}admin/wallet/oldWalletList">Old Wallet Report</a>

                         <a class="collapse-item" href="{site_url}admin/wallet/requestList">Fund Request List</a>
                         <a class="collapse-item" href="{site_url}admin/wallet/apiFundRequestList">Api Fund Request</a>
                         
                        </div>
        </div>
      </li> 

      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUpiWallet" aria-expanded="true" aria-controls="collapseUpiWallet">
          <i class="fa fa-file"></i>
          <span>UPI Wallet </span>
        </a>
        <div id="collapseUpiWallet" <?php if($content_block == 'wallet/upiWalletList' || $content_block == 'report/upi-balance-report') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Wallet Management:</h6>
                         <a class="collapse-item" href="{site_url}admin/wallet/upiWalletList">Member Wallet</a>
                         <a class="collapse-item" href="{site_url}admin/report/upiBalanceReport">Wallet Balance</a>
                         
                        </div>
        </div>
      </li> 
      
      <hr class="sidebar-divider my-0">

      <?php $activeService = $this->User->admin_active_service(); ?>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse10" aria-expanded="true" aria-controls="collapse10">
          <i class="fa fa-list"></i>
          <span>Transaction History</span>
        </a>
        <div id="collapse10" <?php if($content_block == 'report/recharge-history' || $content_block == 'payment/list' || $content_block == 'report/loan-list' || $content_block == 'report/loan-detail' || $content_block == 'report/bbps-list' || $content_block == 'report/money-transfer-list' || $content_block == 'report/recharge-commission-list' || $content_block == 'report/fund-transfer-commission-list') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Transaction History:</h6>
            <?php if(in_array(1, $activeService)){ ?>
            <a class="collapse-item" target="_blank" href="{site_url}admin/report/liveRecharge">Live Recharge</a>
            <a class="collapse-item" href="{site_url}admin/report/recharge">Recharge History</a>
            <?php } ?>
            <?php if(in_array(4, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/bbpsHistory">BBPS History</a>
            <?php } ?>
            <?php if(in_array(8, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/dmtHistory">DMT Report</a>
            <?php } ?>
            <?php if(in_array(3, $activeService)){ ?>
            <!-- <a class="collapse-item" href="{site_url}admin/report/aepsKyc">AEPS KYC History</a>
            <a class="collapse-item" href="{site_url}admin/report/aepsHistory">AEPS Txn History</a> -->
            <a class="collapse-item" href="{site_url}admin/report/matmHistory">MATM Txn History</a>
            <?php } ?>
            <?php if(in_array(23, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/moneyTransfer">Payout History</a>
            <?php } ?>
            <?php if(in_array(6, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/moneyTransferHistory">Money Transfer History</a>
            <?php } ?>
            <?php if(in_array(5, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/upiCollectionReport">UPI Collection History</a> 
            <a class="collapse-item" href="{site_url}admin/report/upiQrHistory">QR History</a> 
            <?php } ?>
            <?php if(in_array(7, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/upiCashReport">UPI Cash History</a> 
            <?php } ?>
            <?php if(in_array(9, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/utiPancardReport">UTI Pancard History</a> 
            <?php } ?>
            <?php if(in_array(16, $activeService)){ ?>
           <a class="collapse-item" href="{site_url}admin/report/nsdlActivationList">NSDL PAN Activation</a>
              <a class="collapse-item" href="{site_url}admin/report/nsdlPanCardList">NSDL Pancard History</a>

               <a class="collapse-item" href="{site_url}admin/report/findPanReport">Find PAN Report</a>

            <?php } ?>
            <?php if(in_array(10, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/currentAccountReport">Current Account History</a> 
            <a class="collapse-item" href="{site_url}admin/report/axisAccountReport">Axis Account History</a> 
            <?php } ?>
            <?php if(in_array(16, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/bomList">BOM Account History</a>
            <?php } ?>
            <?php if(in_array(17, $activeService)){ ?>
             <a class="collapse-item" href="{site_url}admin/report/newAepsKyc">New AEPS Kyc</a>
            <a class="collapse-item" href="{site_url}admin/report/newAepsHistory">New AEPS History</a>



            
            <?php } ?>


             <?php if(in_array(25, $activeService)){ ?>
             <a class="collapse-item" href="{site_url}admin/report/aepsKyc">AEPS Kyc</a>
            <a class="collapse-item" href="{site_url}admin/report/fingpayAepsHistory"> AEPS History</a>

            <?php } ?>
            
            <?php if(in_array(19, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/iciciAepsHistory"> ICICI AEPS History</a>
            <a class="collapse-item" href="{site_url}admin/report/iciciAepsKyc">ICICI AEPS Kyc</a>
            <?php } ?>


             <?php if(in_array(18, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/newPayoutTransfer">New Aeps Payout History</a>
            <?php } ?>

             <?php if(in_array(20, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/newMoneyTransferHistory"> ICICI Payout History</a>
              <a class="collapse-item" href="{site_url}admin/report/upiTransferHistory"> UPI Payout History</a>
            <?php } ?>
            
            
            <?php if(in_array(22, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/nsdlPanList">NSDL Pan History</a>

             <?php } ?>
             <a class="collapse-item" href="{site_url}admin/report/referralComReport">Referral Commission</a> 
             <a class="collapse-item" href="{site_url}admin/report/balanceReport">Balance Report</a>
             <a class="collapse-item" href="{site_url}admin/report/commissionReport">Commission Report</a>
            
              <a class="collapse-item" href="{site_url}admin/report/tdsReport">TDS Report</a>

              <a class="collapse-item" href="{site_url}admin/report/gstReport">GST Report</a>
            
            <a class="collapse-item" href="{site_url}admin/report/topupHistory">PG Txn History</a>

             <?php if(in_array(26, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/utiBalanceReport">UTI Balance Request <span class="menu_notification"><?php echo $this->User->getTotalUnreadTicket(); ?></span> </a>
            
          <?php } ?>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider my-0">

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCommissionHistory" aria-expanded="true" aria-controls="collapseCommissionHistory">
          <i class="fa fa-list"></i>
          <span>Commission History</span>
        </a>
        <div id="collapseCommissionHistory" <?php if($content_block == 'report/recharge-history' || $content_block == 'payment/list' || $content_block == 'report/loan-list' || $content_block == 'report/loan-detail' || $content_block == 'report/bbps-list' || $content_block == 'report/money-transfer-list' || $content_block == 'report/recharge-commission-list' || $content_block == 'report/fund-transfer-commission-list') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Commission History:</h6>
            <?php if(in_array(1, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/rechargeCommision">Recharge Commission</a>
            <?php } ?>
            <?php if(in_array(4, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/bbpsCommision">BBPS Commission</a>
            <?php } ?>
            <?php if(in_array(8, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/moneyTransferCommision">DMT Charge</a>
            <?php } ?>
            <?php if(in_array(3, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/aepsCommision">AEPS Commission</a>
            <?php } ?>
            <?php if(in_array(2, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/fundTransferCommision">Payout Charge</a>
            <?php } ?>
            <?php if(in_array(6, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/openPayoutCommision">Open Payout Charge</a>
            <?php } ?>

            <?php if(in_array(5, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/upiCommision">UPI Collection Charge</a>
            <?php } ?>
            <?php if(in_array(7, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/upiCashCommision">UPI Cash Commission</a>
            <?php } ?>
            <?php if(in_array(15, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/report/cashDepositeReport">Cash Deposite Report</a> 
            <?php } ?>
            
            
          </div>
        </div>
      </li>

      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed menu_notification_li" href="#" data-toggle="collapse" data-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
          <i class="fa fa-life-ring"></i>
          <span>Support Ticket</span>
          <span class="menu_notification"><?php echo $this->User->getTotalUnreadTicket(); ?></span>
        </a>
        <div id="collapse9" <?php if($content_block == 'ticket/ticketList' || $content_block == 'ticket/create' || $content_block == 'ticket/ticketDetail') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Support Ticket:</h6>
                         <a class="collapse-item" href="{site_url}admin/ticket/ticketList">View Ticket</a>
                         
                        </div>
        </div>
      </li> 
    

       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed menu_notification_li" href="#" data-toggle="collapse" data-target="#collapse99" aria-expanded="true" aria-controls="collapse99">
          <i class="fa fa-life-ring"></i>
          <span>Complain</span>
          <span class="menu_notification">0</span>
        </a>
        <div id="collapse99" <?php if($content_block == 'complain/list') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Complain:</h6>
                         <a class="collapse-item" href="{site_url}admin/complain">View Complain</a>
                         
                        </div>
        </div>
      </li>
      
      <hr class="sidebar-divider my-0">


      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse111" aria-expanded="true" aria-controls="collapse111">
          <i class="fa fa-list"></i>
          <span>My Commision</span>
        </a>
        <div id="collapse111" <?php if($content_block == 'master/my-commission' || $content_block == 'master/my-bbpsCommission' || $content_block == 'master/my-rechargeCommission' || $content_block == 'master/my-transfer-commision' || $content_block == 'master/my-aeps-commision') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Master Setting:</h6>
            <?php if($accountData['account_type'] == 2){ ?>
              <a class="collapse-item" href="{site_url}admin/master/myRechargeCommission">Recharge Commission</a>
            <?php } ?>
            <?php if($isInstantPayApiAllow){ ?>
            <a class="collapse-item" href="{site_url}admin/master/myRechargeCommission">Recharge Commission</a>
            <?php } ?>
            <?php if(in_array(4, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/myBbpsLiveCommission">BBPS Commission</a>
            <?php } ?>
            <?php if(in_array(8, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/myAccountVerifyCharge">Account Verify Charge</a>
            <a class="collapse-item" href="{site_url}admin/master/myDmtCharge">DMT Charge</a>
            <?php } ?>
            <?php if(in_array(3, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/myAepsCommision">AEPS Commission</a>
            <?php } ?>
            <?php if(in_array(16, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/myNsdlPancardCharge">NSDL Pancard Charge</a>

            <?php } ?>

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
                           <a class="collapse-item" href="{site_url}admin/package/addPackage">Add Package</a>
                           <a class="collapse-item" href="{site_url}admin/package">View Package</a>
                           </div>
          </div>
        </li> 
      <?php $activeGateway = $this->User->account_active_gateway(); ?>
      <hr class="sidebar-divider my-0">


      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse11" aria-expanded="true" aria-controls="collapse11">
          <i class="fa fa-list"></i>
          <span>Master Setting</span>
        </a>
        <div id="collapse11" <?php if($content_block == 'master/commission' || $content_block == 'master/bbpsCommission' || $content_block == 'master/wallet' || $content_block == 'master/transfer-commision' || $content_block == 'master/aeps-commision' || $content_block == 'master/service') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Master Setting:</h6>
            <?php if(in_array(1, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/commission">Recharge Commission</a>
            <?php } ?>
            <?php if(in_array(4, $activeService)){ ?>
              <a class="collapse-item" href="{site_url}admin/master/bbpsLiveCommission">BBPS Commission</a>
            <?php } ?>
            <?php if(in_array(8, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/accountVerifyCharge">Account Verify Charge</a>
            <a class="collapse-item" href="{site_url}admin/master/dmtCharge">DMT Charge</a>
            <?php } ?>
            
            <a class="collapse-item" href="{site_url}admin/master/aepsCommision">AEPS Commission</a>
          
            <?php if(in_array(16, $activeService)){ ?>
             <a class="collapse-item" href="{site_url}admin/master/panActivationCharge">PAN Activation Charge</a>
               <a class="collapse-item" href="{site_url}admin/master/findPanCharge"> Find PAN Charge</a>
            <a class="collapse-item" href="{site_url}admin/master/panCharge">NSDL Pancard Charge</a>
            <?php } ?>
             <?php if(in_array(22, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/nsdlPancardCharge">NSDL Pancard Charge</a>
            <?php } ?>
            <?php if(in_array(9, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/utiCommision">UTI Pancard Charge</a>
            <?php } ?>
            <?php if(in_array(20, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/transferCommision">Payout Charge</a>
            <?php } ?>
            <?php if(in_array(6, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/moneyTransferCommision">Open Payout Charge</a>
            <?php } ?>
            <?php if(in_array(23, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/xpressPayoutCharge">Xpress Payout Charge</a>
            <?php } ?>
            <?php if(in_array(5, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/upiCommision">UPI Collection Charge</a>
            <?php } ?>
            <?php if(in_array(7, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/upiCashCommision">UPI Cash Commission</a>
            <?php } ?>
            <a class="collapse-item" href="{site_url}admin/master/gatewayCharge">PG Txn Charge</a>
            <?php if(in_array(1, $activeGateway)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/payment">Payment Setting</a>
            <?php } ?>


            <?php if(in_array(26, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}admin/master/utiBalanceCharge">UTI Balance Request</a>
            <?php } ?>


            <a class="collapse-item" href="{site_url}admin/master/referralCommision">Referral Commission</a>
            <a class="collapse-item" href="{site_url}admin/master/ipsetting">Manage IP</a>
            
             <?php if($accountData['is_payout_otp'] == 1)  {?>
                <a class="collapse-item" href="{site_url}admin/master/payoutOtpSetting">Payout Amount Setting</a>
            <?php } ?>
            
            
                     
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
                         <a class="collapse-item" href="{site_url}admin/api/apiList">Recharge API List</a>
                         <a class="collapse-item" href="{site_url}admin/api/changeApi">Change API</a>
                         <a class="collapse-item" href="{site_url}admin/api/amountFilter">Amount Filter</a>
                         
                        </div>
        </div>
      </li>
      <!-- <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUpi" aria-expanded="true" aria-controls="collapseUpi">
          <i class="flaticon-life-insurance"></i>
          <span>UPI QR Code</span>
        </a>
        <div id="collapseUpi" <?php if($content_block == '') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">UPI :</h6>
                         
                         <a class="collapse-item" href="{site_url}admin/upi/collection">UPI Collection</a>
                         <a class="collapse-item" href="{site_url}admin/upi/cash">UPI Cash</a>
                         
                         
                       
                        </div>
        </div>
      </li> --> 
      <!-- <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLoanQR" aria-expanded="true" aria-controls="collapseLoanQR">
          <i class="flaticon-life-insurance"></i>
          <span>Loan QR Code</span>
        </a>
        <div id="collapseLoanQR" <?php if($content_block == '') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Loan QR Code :</h6>
                         
                         <a class="collapse-item" href="{site_url}admin/member/instantLoan">Instant Loan</a>
                         
                       
                        </div>
        </div>
      </li>  -->
     <hr class="sidebar-divider my-0">
     <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomLink" aria-expanded="true" aria-controls="collapseThree">
          <i class="fa fa-link"></i>
          <span>Custom Link</span>
        </a>
        <div id="collapseCustomLink" <?php if($content_block == 'link/list' || $content_block == 'link/add') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="headingThree" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Link:</h6>
            <a class="collapse-item" href="{site_url}admin/link/add">Add Link</a>
          <a class="collapse-item" href="{site_url}admin/link"> View Link</a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider my-0">


      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse18" aria-expanded="true" aria-controls="collapse11">
          <i class="fa fa-list"></i>
          <span>Website</span>
        </a>
        <div id="collapse18" <?php if($content_block == 'master/commission') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Website:</h6>
            <a class="collapse-item" href="{site_url}admin/website/slider">Home Slider</a>
            <a class="collapse-item" href="{site_url}admin/website/appSlider">App Slider</a>
            <a class="collapse-item" href="{site_url}admin/website/contact">Contact Detail</a>
            <a class="collapse-item" href="{site_url}admin/website/account">Account Detail</a>
            <a class="collapse-item" href="{site_url}admin/website/service">Our Services</a>
            <a class="collapse-item" href="{site_url}admin/website/testimonial">Testimonial</a>
            <a class="collapse-item" href="{site_url}admin/website/news">News</a> 
            <?php if($accountData['web_theme'] == 1) { ?>        
            <a class="collapse-item" href="{site_url}admin/website/blogList">Blog</a>
             <a class="collapse-item" href="{site_url}admin/website/featureList">Feature</a>  
              <a class="collapse-item" href="{site_url}admin/website/enquiryList">Enquiry</a>         
            <?php } ?>
            <a class="collapse-item" href="{site_url}admin/website/pages">Custom Pages</a>
            <a class="collapse-item" href="{site_url}admin/website/privacy">Privacy Policy</a>
             <a class="collapse-item" href="{site_url}admin/website/terms">Terms & Condition</a>         
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
                         <a class="collapse-item" href="{site_url}admin/setting/profile">My Profile</a>
                         <a class="collapse-item" href="{site_url}admin/setting/changePassword">Change Password</a>
                         <a class="collapse-item" href="{site_url}admin/setting/changeTranscationPassword">Change  Transcation Password</a>
                         <a class="collapse-item" href="{site_url}admin/setting/changeTheme">Theme Setting</a>
                         <a class="collapse-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
                         
                        </div>
        </div>
      </li> 
       
      <?php if($accountData['account_type'] != 2){ ?>
      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse12" aria-expanded="true" aria-controls="collapse12">
          <i class="fa fa-history"></i>
          <span>System</span>
        </a>
        <div id="collapse12" <?php if($content_block == 'system/logList' || $content_block == 'system/callBackLogList') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">System:</h6>
                         <a class="collapse-item" href="{site_url}admin/system/logList">View Log</a>
                         <a class="collapse-item" href="{site_url}admin/system/callBackLogList">Callback Log</a>
                         <?php if(in_array(3, $activeService)){ ?>
                         <a class="collapse-item" href="{site_url}admin/system/aepsApiLogList">AEPS API Log</a>
                        <?php } ?>
                         <a class="collapse-item" href="{site_url}admin/system/settlementLogList">Settlement Log</a>
                         <a class="collapse-item" href="{site_url}admin/report/upiApiLog">UPI API Log</a> 
                         <a class="collapse-item" href="{site_url}admin/report/payoutApiLog">Payout API Log</a> 
                         
                        </div>
        </div>
      </li> 
      <?php } ?>
       
      <?php if($accountData['is_app_notification'] == 1){ ?>
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{site_url}admin/dashboard/sendNotification">
          <i class="fa fa fa-bell"></i>
          <span>Send App Notification</span></a>
      </li>
      <?php } ?>
       <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{site_url}admin/dashboard/sendWebNotification">
          <i class="fa fa fa-bell"></i>
          <span>Send  Notification</span></a>
      </li>
      
      <hr class="sidebar-divider my-0">

      
      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline mt-4">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>

      <!-- End of Sidebar -->

          <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">
       <div class="nav_header bg-white static-top shadow mb-4">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light topbar">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Search -->
          <h4><?php echo $accountData['title']; ?></h4>
          
          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown" style="padding-top: 10px; padding-right: 25px;">
            <h6><span class="nav_icons"><img src="{site_url}skin/admin/img/wallet_icon.png" class="img-fluid"></span>
            <b>FD Balance : &#8377; <?php echo number_format($this->User->getMemberAepsCommisionBlance($loggedUser['id']),2); ?></b>
            </h6>  
            </li>
            <li class="nav-item dropdown" style="padding-top: 10px; padding-right: 25px;">
            <h6><span class="nav_icons"><img src="{site_url}skin/admin/img/wallet_icon.png" class="img-fluid"></span>
            <b>Virtual-Wallet : &#8377; <?php echo number_format($this->User->getMemberVirtualWalletBalance($loggedUser['id']),2); ?></b>
            </h6>  
            </li>
            <li class="nav-item dropdown" style="padding-top: 10px; padding-right: 25px;">
            <h6><span class="nav_icons"><img src="{site_url}skin/admin/img/wallet_icon.png" class="img-fluid"></span>
            <b>S-Wallet : &#8377; <?php echo number_format($this->User->getMemberCollectionWalletBalance($loggedUser['id']),2); ?></b>
            </h6>  
            </li>
            
            
            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                  <?php
                $data=$this->db->get_where('users',array('id'=>$loggedUser['id']))->row_array();
                echo $data['name'].'</br>( '.$data['user_code'].' )';
                ?> 

                </span>
                <img class="img-profile rounded-circle" src="{site_url}skin/admin/img/user.png">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{site_url}admin/setting/profile">
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
        <?php
         $news = $this->db->get_where('website_news',array('account_id'=>$account_id))->result_array(); 
        if($news){
        ?>  
         <div class="news_toop">
          <marquee>
         <ol>
         <?php
         $i=1; 
         foreach($news as $list){
         ?> 
          <li><?php echo $i; ?>. <?php echo $list['news']; ?></li>
         <?php $i++;} ?>
         </ol>
          </marquee>  
          
          </div>
        <?php } ?>
</div>
        <!-- End of Topbar -->
        
        
        
   <div class="nav_dashboard_top">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12 col-md-12">
             <div class="nav_top_news">
               <div><h4><i class="fa fa-microphone"></i>Updates</h4></div>    
               <div style="display: flex;"><marquee><p>Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p></marquee></div>
             </div>      
          </div>
          
        </div>  
      </div> 
   </div>