<?php 
$account_id = $this->User->get_domain_account();
$accountData = $this->User->get_account_data($account_id);
$loggedUser = $this->User->getAdminLoggedUser(USER_SESSION_ID);
$user_aeps_status = $this->User->get_member_aeps_status($loggedUser['id']);
$activeGateway = $this->User->account_active_gateway();
?>
 <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{site_url}user/dashboard">
        <div class="sidebar-brand-icon">
        <img src="{site_url}<?php echo $accountData['image_path']; ?>" class="img-responsive">
        </div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="{site_url}user/dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

     
      <?php $activeService = $this->User->account_active_service($loggedUser['id']); ?>
      <?php if(in_array(1, $activeService)){ ?>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse5" aria-expanded="true" aria-controls="collapseThree">
          <i class="fa fa-mobile"></i>
          <span>Recharge</span>
        </a>
        <div id="collapse5" <?php if($content_block == 'recharge/mobile-prepaid' || $content_block == 'recharge/mobile-postpaid' || $content_block == 'recharge/dth' || $content_block == 'recharge/electricity' || $content_block == 'recharge/datacard' || $content_block == 'recharge/landline' || $content_block == 'recharge/broadband' ) { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading5" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Recharge:</h6>
            <a class="collapse-item <?php if($content_block == 'recharge/mobile-prepaid'){ ?> active <?php } ?> " href="{site_url}user/recharge/mobileprepaid">Mobile</a>
            
            <a class="collapse-item <?php if($content_block == 'recharge/dth'){ ?> active <?php } ?> " href="{site_url}user/recharge/dth">DTH</a>

            
            </div>
        </div>
      </li>
      <?php } ?>
      <?php if(in_array(4, $activeService)){ ?>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link" href="{site_url}user/bbps">
          <i class="fa fa-tv"></i>
          <span>BBPS Live</span></a>
      </li>
      <?php } ?>
      <?php if(in_array(2, $activeService)){ ?>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse88" aria-expanded="true" aria-controls="collapse88">
          <i class="fa fa-mobile"></i>
          <span>AEPS Payout</span>
        </a>
        <div id="collapse88" <?php if($content_block == 'transfer/list' || $content_block == 'transfer/transfer') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading5" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">AEPS Payout:</h6>
            <a class="collapse-item" href="{site_url}user/transfer/payoutBeneficiaryList">Beneficiary</a>
              
              <a class="collapse-item" href="{site_url}user/transfer/payoutFundTransfer">Transfer Now</a>
              
              <a class="collapse-item" href="{site_url}user/transfer">Payout Report</a>


               <a class="collapse-item" href="{site_url}user/transfer/benificaryAccountList"> New Account Request</a>

               
            </div>
        </div>
      </li>
      <?php } ?>



      <?php if(in_array(6, $activeService)){ ?>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMoney" aria-expanded="true" aria-controls="collapseMoney">
          <i class="fa fa-mobile"></i>
          <span>Money Transfer</span>
        </a>
        <div id="collapseMoney" class="collapse" aria-labelledby="heading5" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Money Transfer:</h6>
            <a class="collapse-item" href="{site_url}user/transfer/senderList">Sender</a>
            <a class="collapse-item" href="{site_url}user/transfer/beneficiaryList">Beneficiary</a>
            <a class="collapse-item" href="{site_url}user/transfer/fundTransfer">Transfer Now</a>
              <a class="collapse-item" href="{site_url}user/report/moneyTransferHistory">Transfer Report</a>
            </div>
        </div>
      </li>
      <?php } ?>

       <?php if(in_array(3, $activeService)){ ?>
      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse16" aria-expanded="true" aria-controls="collapse16">
          <i class="fa fa-bullhorn"></i>
          <span>AEPS1 Service</span>
        </a>
        <div id="collapse16" <?php if($content_block == 'aeps/list' || $content_block == 'aeps/transfer') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading11" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">AEPS1 Service:</h6>
             <?php if(!$user_aeps_status){ ?>
              <a class="collapse-item" href="{site_url}user/aeps/activeAeps">Active AEPS</a>
              <?php } else { ?>
              <a class="collapse-item" href="{site_url}user/aeps">AEPS Now</a>
              <a class="collapse-item" href="{site_url}user/aeps/transactionHistory">Transaction History</a>
            <?php } ?>
          </div>
        </div>
      </li> 

      <?php } ?>

       <?php if(in_array(3, $activeService)){ ?>
      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDeposit" aria-expanded="true" aria-controls="collapseDeposit">
          <i class="fa fa-money-bill-alt"></i>
          <span>Cash Deposite </span>
        </a>
        <div id="collapseDeposit" <?php if($content_block == '') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Cash Deposite :</h6>
                         
                         <a class="collapse-item" href="{site_url}user/aeps/cashDeposite">Cash Deposite</a>

                         
                       
                        </div>
        </div>
      </li>
      <?php } ?> 
      
      <hr class="sidebar-divider my-0">


      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse10" aria-expanded="true" aria-controls="collapse10">
          <i class="fa fa-list"></i>
          <span>Reports</span>
        </a>
        <div id="collapse10" <?php if($content_block == 'report/recharge-history' || $content_block == 'payment/list' || $content_block == 'report/loan-list' || $content_block == 'report/loan-detail' || $content_block == 'report/bbps-list' || $content_block == 'report/money-transfer-list' || $content_block == 'report/recharge-commission-list') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Reports:</h6>
            <a class="collapse-item" href="{site_url}user/report/recharge">Recharge History</a>
            <a class="collapse-item" href="{site_url}user/report/bbps">Bill Pay History</a>
            <?php if(in_array(4, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}user/report/bbpsHistory">BBPS History</a>
            <?php } ?>
            <a class="collapse-item" href="{site_url}user/report/moneyTransfer">Payout Report</a>
            <a class="collapse-item" href="{site_url}user/report/moneyTransferHistory">Money Trans. Report</a>
            <?php if(in_array(5, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}user/report/upiTxnReport">UPI Transaction</a> 
            <?php } ?>
            <?php if(in_array(3, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}user/report/cashDepositeReport">Cash Deposite History</a> 
            <?php } ?>
            <a class="collapse-item" href="{site_url}user/report/rechargeCommision">Recharge Commission</a>
            <a class="collapse-item" href="{site_url}user/report/upiCommision">UPI Commission</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider my-0">


      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse111" aria-expanded="true" aria-controls="collapse111">
          <i class="fa fa-list"></i>
          <span>My Commision</span>
        </a>
        <div id="collapse111" <?php if($content_block == 'master/my-commission' || $content_block == 'master/my-bbpsCommission' || $content_block == 'master/my-transfer-commision' || $content_block == 'master/my-aeps-commision') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Master Setting:</h6>
            <a class="collapse-item" href="{site_url}user/master/myCommission">Recharge Commission</a>
            <a class="collapse-item" href="{site_url}user/master/myBbpsCommission">Bill Pay Commission</a>
            <?php if(in_array(4, $activeService)){ ?>
              <a class="collapse-item" href="{site_url}user/master/myBbpsLiveCommission">BBPS Commission</a>
            <?php } ?>
            <a class="collapse-item" href="{site_url}user/master/myTransferCommision">Payout Commission</a>
            <a class="collapse-item" href="{site_url}user/master/myMoneyTransferCommision">Money Trans. Commission</a>
            <a class="collapse-item" href="{site_url}user/master/myAepsCommision">AEPS Commission</a>
            <a class="collapse-item" href="{site_url}user/master/myUpiCommision">UPI Collection Com.</a>
            
                     
          </div>
        </div>
      </li>

      
    <?php $activeGateway = $this->User->account_active_gateway(); ?>
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{site_url}user/wallet/payolTransfer">
          <i class="fa fa-dollar"></i>
          <span>Payol Transfer</span></a>
      </li>
    <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse8" aria-expanded="true" aria-controls="collapse8">
          <i class="fa fa-file"></i>
          <span>Main Wallet</span>
        </a>
        <div id="collapse8" <?php if($content_block == 'wallet/walletList' || $content_block == 'wallet/addWallet' || $content_block == 'member/fundTransferList' || $content_block == 'wallet/fundRequest' || $content_block == 'wallet/requestList' || $content_block == 'wallet/myRequestList' || $content_block == 'wallet/creditList' || $content_block == 'wallet/debitList' || $content_block == 'wallet/myWalletList' ) { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Main Wallet:</h6>
                         <?php if(in_array(1, $activeGateway)){ ?>
                         <a class="collapse-item" href="{site_url}user/wallet/topup">Topup Wallet Gateway</a>

                         <a class="collapse-item" href="{site_url}user/wallet/topupHistory">Topup History</a>
                         <?php } ?>

                         <?php if(in_array(5, $activeService)){
                         ?>
                         <a class="collapse-item" href="{site_url}user/wallet/addFund">Add Fund</a> 
                         <?php } ?>

                         <a class="collapse-item" href="{site_url}user/wallet/myWalletList">My Wallet</a>
                         <a class="collapse-item" href="{site_url}user/wallet/myRequestList">My Fund Request</a>
                         <?php
                          if($account_id == 7){
                         ?>
                          <a class="collapse-item" href="{site_url}user/wallet/walletTransfer">Wallet Transfer</a>
                         <?php } ?>
                         
                        </div>
        </div>
      </li> 


       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSaving" aria-expanded="true" aria-controls="collapseSaving">
          <i class="fa fa-file"></i>
          <span>Saving</span>
        </a>
        <div id="collapseSaving" class="collapse" aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Saving:</h6>
                         
                         <a class="collapse-item" href="#">Recurring Deposit</a>
                         <a class="collapse-item" href="#">Fixed Deposit</a>
                         <a class="collapse-item" href="{site_url}user/saving/clubList">Club</a>
                         
                        </div>
        </div>
      </li>

       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
          <i class="fa fa-life-ring"></i>
          <span>Support Ticket</span>
        </a>
        <div id="collapse9" <?php if($content_block == 'ticket/ticketList' || $content_block == 'ticket/create' || $content_block == 'ticket/ticketDetail') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Support Ticket:</h6>
                         <a class="collapse-item" href="{site_url}user/ticket/create">Create Ticket</a>
                         <a class="collapse-item" href="{site_url}user/ticket/ticketList">View Ticket</a>
                         
                        </div>
        </div>
      </li> 

       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse99" aria-expanded="true" aria-controls="collapse99">
          <i class="fa fa-life-ring"></i>
          <span>Complain</span>
        </a>
        <div id="collapse99" <?php if($content_block == 'complain/list') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Complain:</h6>
                         <a class="collapse-item" href="{site_url}user/complain">View Complain</a>
                         
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
                         <a class="collapse-item" href="{site_url}user/setting/profile">My Profile</a>
                         <a class="collapse-item" href="{site_url}user/setting/changePassword">Change Password</a>
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
        <div class="nav_header bg-white static-top shadow mb-4">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light  topbar">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Search -->
          <h4><?php echo $accountData['title']; ?></h4>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

           
            <li class="nav-item dropdown" style="padding-top: 25px; padding-right: 25px;">
            <h6>
            <b>Main-Wallet Balance - &#8377; <?php echo number_format($this->User->getMemberWalletBalanceSP($loggedUser['id']),2); ?></b>
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
                <a class="dropdown-item" href="{site_url}user/setting/profile">
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
      <?php $notificationList = $this->User->getClubNotification($loggedUser['id']); ?>
        <?php if($notificationList){ ?>
        <div class="container-fluid">
          <?php foreach($notificationList as $nlist){ ?>
            <?php if($nlist['to_member_id'] == 0){ ?>
            <div class="alert alert-success alert-dismissable"><?php echo $nlist['msg']; ?> <a href="{site_url}user/saving/clubList">Accept</a></div>
          <?php } else { ?>
            <div class="alert alert-success alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true" onclick="closeClubNoti(<?php echo $nlist['id']; ?>);">&times;</button><?php echo $nlist['msg']; ?></div>
          <?php } ?>
        <?php } ?>
        
        </div>
        <?php } ?>
        <!-- End of Topbar -->