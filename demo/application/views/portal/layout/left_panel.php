<?php 
$account_id = $this->User->get_domain_account();
$accountData = $this->User->get_account_data($account_id);
$loggedUser = $this->User->getAdminLoggedUser(API_MEMBER_SESSION_ID);
?>
 <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{site_url}portal/dashboard">
        <div class="sidebar-brand-icon">
        <img src="{site_url}<?php echo $accountData['image_path']; ?>" class="img-responsive">
        </div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{site_url}portal/dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

     
      <?php $activeService = $this->User->account_active_service($loggedUser['id']); ?>
      
      
      <hr class="sidebar-divider my-0">


      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse10" aria-expanded="true" aria-controls="collapse10">
          <i class="fa fa-list"></i>
          <span>Reports</span>
        </a>
        <div id="collapse10" <?php if($content_block == 'report/recharge-history' || $content_block == 'payment/list' || $content_block == 'report/loan-list' || $content_block == 'report/loan-detail' || $content_block == 'report/bbps-list' || $content_block == 'report/money-transfer-list' || $content_block == 'report/recharge-commission-list') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Reports:</h6>
            <?php if(in_array(1, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/report/recharge">Recharge History</a>
            <?php } ?>
            <?php if(in_array(4, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/report/bbps">BBPS History</a>
            <?php } ?>
            <?php if(in_array(20, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/report/payoutReport">Payout Report</a>
            <?php } ?>
            <?php if(in_array(3, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/report/aepsKyc">AEPS Kyc</a>
            <a class="collapse-item" href="{site_url}portal/report/aepsHistory">AEPS History</a>
            <a class="collapse-item" href="{site_url}portal/report/myAepsCommision">My AEPS Commission</a>
            <?php } ?>
            <?php if(in_array(1, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/report/rechargeCommision">Recharge Commission</a>
            <?php } ?>
            <?php if(in_array(5, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/report/upiCollectionReport">UPI Collection History</a> 
            <?php } ?>
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
            <?php if(in_array(1, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/master/myCommission">Recharge Commission</a>
            <?php } ?>
            <?php if(in_array(4, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/master/myBbpsCommission">Bill Pay Commission</a>
            <?php } ?>
            <?php if(in_array(23, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/master/myTransferCommision">Payout Charge</a>
            <?php } ?>
            <?php if(in_array(5, $activeService)){ ?>
            <a class="collapse-item" href="{site_url}portal/master/myUpiCommision">UPI Collection Charge</a>
            <?php } ?>
            
            
                     
          </div>
        </div>
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
                         <a class="collapse-item" href="{site_url}portal/wallet/myWalletList">My Wallet</a>
                           <a class="collapse-item" href="{site_url}portal/wallet/oldWalletList">Old Wallet Report</a>
                         <a class="collapse-item" href="{site_url}portal/wallet/myRequestList">My Fund Request</a>
                         <a class="collapse-item" href="{site_url}portal/wallet/apiRequestList">Api Fund Request</a>
                         
                        </div>
        </div>
      </li> 

      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUpiWallet" aria-expanded="true" aria-controls="collapseUpiWallet">
          <i class="fa fa-file"></i>
          <span>UPI Wallet</span>
        </a>
        <div id="collapseUpiWallet" <?php if($content_block == 'wallet/myUpiWalletList') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Main Wallet:</h6>
                         <a class="collapse-item" href="{site_url}portal/wallet/myUpiWalletList">My Wallet</a>
                         
                        </div>
        </div>
      </li> 

     
      <?php if(in_array(23, $activeService)){ ?>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse88" aria-expanded="true" aria-controls="collapse88">
          <i class="fa fa-mobile"></i>
          <span>Xpress Payout</span>
        </a>
        <div id="collapse88" <?php if($content_block == 'payout/list' || $content_block == 'payout/transfer') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading5" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">DMT:</h6>
              <a class="collapse-item" href="{site_url}portal/payout/payoutFundTransfer">Transfer Now</a>
              <a class="collapse-item" href="{site_url}portal/payout">Transfer Report</a>
            
            </div>
        </div>
      </li>
      <?php } ?>
     


       <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
          <i class="fa fa-life-ring"></i>
          <span>Support Ticket</span>
        </a>
        <div id="collapse9" <?php if($content_block == 'ticket/ticketList' || $content_block == 'ticket/create' || $content_block == 'ticket/ticketDetail') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Support Ticket:</h6>
                         <a class="collapse-item" href="{site_url}portal/ticket/create">Create Ticket</a>
                         <a class="collapse-item" href="{site_url}portal/ticket/ticketList">View Ticket</a>
                         
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
                         <a class="collapse-item" href="{site_url}portal/complain">View Complain</a>
                         
                        </div>
        </div>
      </li> 
<?php $activeService = $this->User->account_active_service($loggedUser['id']); ?>
      <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse11" aria-expanded="true" aria-controls="collapse11">
          <i class="fa fa-file"></i>
          <span>API Document</span>
        </a>
        <div id="collapse11" <?php if($content_block == 'document/recharge' || $content_block == 'document/moneyTransfer') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">API Document:</h6>
                         <?php if(in_array(1, $activeService)){ ?>
                         <a class="collapse-item" href="{site_url}portal/document/recharge">Recharge</a>
                         <?php } ?>
                         
                         <a class="collapse-item" href="{site_url}portal/document/moneyTransfer">Payout</a>
                       
                        <?php if(in_array(5, $activeService)){ ?>
                         <a class="collapse-item" href="{site_url}portal/document/upiQr">UPI QR Only</a>
                        <?php } ?>
                        <?php if(in_array(3, $activeService)){ ?>
                         <a class="collapse-item" href="{site_url}portal/document/aeps">AEPS</a>
                        <?php } ?>
                         
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
                         <a class="collapse-item" href="{site_url}portal/setting/profile">My Profile</a>
                         <a class="collapse-item" href="{site_url}portal/setting/changePassword">Change Password</a>
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
        <nav class="navbar navbar-expand navbar-light topbar">

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
            <b>Main Wallet : &#8377; <?php echo number_format($this->User->getMemberWalletBalanceSP($loggedUser['id']),2); ?></b>
            </h6>  
            </li>
            <li class="nav-item dropdown" style="padding-top: 25px; padding-right: 25px;">
            <h6>
            <b>UPI Wallet : &#8377; <?php echo number_format($this->User->getMemberUpiWalletBalanceSP($loggedUser['id']),2); ?></b>
            </h6>  
            </li>
            <!--<li class="nav-item dropdown" style="padding-top: 25px; padding-right: 25px;">-->
            <!--<h6>-->
            <!--<b>Commision Balance : &#8377; <?php echo number_format($this->User->getMemberAepsCommisionBlance($loggedUser['id']),2); ?></b>-->
            <!--</h6>  -->
            <!--</li>-->
            <li class="nav-item dropdown" style="padding-top: 25px; padding-right: 25px;">
            <h6>
              <?php 
              $get_hold_amount = $this->db->select('min_wallet_balance')->get_where('users',array('account_id'=>$account_id,'id'=>$loggedUser['id']))->row_array();
                
               $hold_amount =  isset($get_hold_amount['min_wallet_balance']) ? $get_hold_amount['min_wallet_balance'] : 0;

              ?>
            <b>Hold Amount : &#8377; <?php echo number_format($hold_amount,2); ?></b>
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
                <a class="dropdown-item" href="{site_url}portal/setting/profile">
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