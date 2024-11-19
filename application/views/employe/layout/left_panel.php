<?php
$account_id = $this->User->get_domain_account();
$accountData = $this->User->get_account_data($account_id);
$loggedUser = $this->User->getAdminLoggedUser(ADMIN_EMPLOYE_SESSION_ID);
$isInstantPayApiAllow = $this->User->get_admin_instant_cogent_api($account_id);
$activeGateway = $this->User->account_active_gateway();
?>
<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{site_url}employe/dashboard">
            <div class="sidebar-brand-icon">
                <img src="{site_url}<?php echo $accountData['image_path']; ?>" class="img-responsive">
            </div>
        </a>

        <?php if ($this->User->admin_menu_permission(1, 1)) { ?>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{site_url}employe/dashboard">
                <i class="fas fa-fw fa-tachometer-alt" style="color: #f3baba !important;"></i>
                <span>Dashboard</span></a>
        </li>

        <?php } ?>

        <?php if (IS_SOCIETY == 1) { ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClub"
                aria-expanded="true" aria-controls="collapseClub">
                <i class="fa fa-user" style="color: #f3baba !important;"></i>
                <span>Society</span>
            </a>
            <div id="collapseClub" <?php if ($content_block == 'society/clubCreate') { ?> class="collapse show"
                <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="headingThree"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Society:</h6>
                    <a class="collapse-item" href="{site_url}employe/society">Club List</a>
                    <a class="collapse-item" href="{site_url}employe/society/addClub">Create Club</a>

                </div>
            </div>
        </li>
        <?php } ?>

        <?php $activeService = $this->User->admin_active_service(); ?>

        <?php if ($this->User->admin_menu_permission(2, 1)) { ?>
        <hr class="sidebar-divider my-0">


        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
                aria-expanded="true" aria-controls="collapseThree">
                <i class="fa fa-users" style="color: #f3baba !important;"></i>
                <span>Member Management</span>
            </a>
            <div id="collapseThree" <?php if (
                    $content_block == 'member/memberList' ||
                    $content_block == 'member/addMember' ||
                    $content_block == 'member/editMember' ||
                    $content_block == 'member/mdMemberList' ||
                    $content_block == 'member/distributorList' ||
                    $content_block == 'member/retailerList' ||
                    $content_block == 'member/apiMemberList' ||
                    $content_block == 'member/userList'
                ) { ?> class="collapse show" <?php } else { ?> class="collapse" <?php } ?>
                aria-labelledby="headingThree" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Member Management:</h6>
                    <?php if ($this->User->admin_menu_permission(9, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/member/addMember">Add Member</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(46, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/service">Manage Service</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(11, 2)) { ?>

                    <a class="collapse-item" href="{site_url}employe/member/memberList">View All Member</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(12, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/member/mdMemberList">View Master Distributor</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(13, 2)) { ?>

                    <a class="collapse-item" href="{site_url}employe/member/distributorList">View Distributor</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(14, 2)) { ?>

                    <a class="collapse-item" href="{site_url}employe/member/retailerList">View Retailer</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(15, 2)) { ?>
                    <?php if ($accountData['is_disable_api_role'] == 0) { ?>

                    <a class="collapse-item" href="{site_url}employe/member/apiMemberList">API Member</a>
                    <?php }} ?>
                    <?php if ($this->User->admin_menu_permission(16, 2)) { ?>
                    <?php if ($accountData['is_disable_user_role'] == 0) { ?>
                    <a class="collapse-item" href="{site_url}employe/member/userList">Users</a>
                    <?php }} ?>
                    <a class="collapse-item" href="{site_url}employe/master/moveMember">Move Member</a>
                    <a class="collapse-item" href="{site_url}employe/report/moveMemberReport">Move Member Report</a>
                    <a class="collapse-item" href="{site_url}employe/report/changeAccountList">Account Request List</a>

                    <?php if ($this->User->admin_menu_permission(134, 2)) { ?>

                    <a class="collapse-item" href="{site_url}employe/member/deactiveMemberList">Deactive Member List</a>
                    <?php } ?>


                    <?php if (in_array(20, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/iciciChangeAccountList">ICICI Account
                        Request List</a>
                    <?php } ?>
                    <?php if (in_array(17, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/member/memberBeneficiaryList">Beneficiary List</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(134, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/member/portalKyc">Member KYC</a>
                    <?php } ?>
                </div>
            </div>
        </li>

        <?php } ?>

        <?php if ($this->User->admin_menu_permission(26, 1)) { ?>
        <?php if ($accountData['is_generate_invoice'] == 1) { ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInvoice"
                aria-expanded="true" aria-controls="collapseInvoice">
                <i class="far fa-file-powerpoint" style="color: #f3baba !important;"></i>
                <span>Invoice Management</span>
            </a>
            <div id="collapseInvoice"
                <?php if ($content_block == 'account/dynamicInvoice' || $content_block == 'account/dynamicInvoice') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="headingThree"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Invoice Management:</h6>
                    <?php if ($this->User->admin_menu_permission(122, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/account/dynamicInvoice">Dynamic Invoice</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(123, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/account/manualInvoiceList">Manual Invoice</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(124, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/account/tdsInvoice">TDS Invoice</a>
                    <?php } ?>




                </div>
            </div>
        </li>
        <?php }} ?>


        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsVanwallet"
                aria-expanded="true" aria-controls="collapsVanwallet">
                <i class="fas fa-wallet" style="color: #f3baba !important;"></i>
                <span>Virtual Wallet</span>
            </a>
            <div id="collapsVanwallet"
                <?php if ($content_block == 'vanwallet/walletList' || $content_block == 'vanwallet/account-detail') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Wallet Management:</h6>
                    <a class="collapse-item" href="{site_url}employe/vanwallet/accountDetail">Virtual Account</a>
                    <a class="collapse-item" href="{site_url}employe/vanwallet/walletList">Wallet History</a>

                </div>
            </div>
        </li>
        <?php if ($this->User->admin_menu_permission(26, 1)) { ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#commisionBalance"
                aria-expanded="true" aria-controls="commisionBalance">
                <i class="fas fa-wallet" style="color: #f3baba !important;"></i>
                <span>Commission Wallet</span>
            </a>
            <div id="commisionBalance"
                <?php if ($content_block == 'commission/walletList' || $content_block == 'commission/accountWiseBalance') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Commission Wallet:</h6>
                    <a class="collapse-item" href="{site_url}employe/commission/walletList">Commission History</a>
                    <!--<a class="collapse-item" href="{site_url}admin/commission/accountWalletList">Account Wise Commission</a>-->
                </div>
            </div>
        </li>

        <?php } ?>

        <?php if ($this->User->admin_menu_permission(6, 1)) { ?>

        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse8" aria-expanded="true"
                aria-controls="collapse8">
                <i class="fas fa-wallet" style="color: #f3baba !important;"></i>
                <span>Member Wallet </span>
                <span class="menu_notification"><?php echo $this->User->getTotalUnreadRequest(); ?></span>
            </a>
            <div id="collapse8" <?php if (
                    $content_block == 'wallet/walletList' ||
                    $content_block == 'wallet/addWallet' ||
                    $content_block == 'member/fundTransferList' ||
                    $content_block == 'wallet/requestList' ||
                    $content_block == 'wallet/creditList' ||
                    $content_block == 'wallet/debitList'
                ) { ?> class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Wallet Management:</h6>
                    <?php if ($this->User->admin_menu_permission(47, 2)) { ?>
                    <?php if (in_array(1, $activeGateway)) { ?>
                    <a class="collapse-item" href="{site_url}employe/wallet/topupHistory">Topup History</a>
                    <?php }} ?>
                    <?php if ($this->User->admin_menu_permission(49, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/wallet/walletList">Member Wallet</a>
                    <?php } ?>
                    <a class="collapse-item" href="{site_url}employe/report/balanceReport">Wallet Balance</a>
                    <?php if ($this->User->admin_menu_permission(50, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/wallet/creditList">Credit Fund</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(51, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/wallet/debitList">Debit Fund</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(94, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/wallet/requestList">Fund Request List</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(126, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/wallet/apiFundRequestList">Api Fund Request</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(135, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/wallet/oldWalletList">Old Wallet Report</a>
                    <?php } ?>

                </div>
            </div>
        </li>

        <?php } ?>

        <?php if ($this->User->admin_menu_permission(27, 1)) { ?>

        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUpiWallet"
                aria-expanded="true" aria-controls="collapseUpiWallet">
                <i class="fas fa-wallet" style="color: #f3baba !important;" style="color: #f3baba !important;"></i>
                <span>UPI Wallet </span>
            </a>
            <div id="collapseUpiWallet"
                <?php if ($content_block == 'wallet/upiWalletList' || $content_block == 'report/upi-balance-report') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Wallet Management:</h6>
                    <?php if ($this->User->admin_menu_permission(127, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/wallet/upiWalletList">Member Wallet</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(128, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/upiBalanceReport">Wallet Balance</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(129, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/wallet/upiWalletTransfer">Wallet Transfer</a>
                    <?php } ?>

                </div>
            </div>
        </li>

        <?php } ?>

        <?php if ($this->User->admin_menu_permission(3, 1)) { ?>

        <hr class="sidebar-divider my-0">

        <?php $activeService = $this->User->admin_active_service(); ?>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse10" aria-expanded="true"
                aria-controls="collapse10">
                <i class="fa fa-list" style="color: #f3baba !important;" style="color: #f3baba !important;"></i>
                <span>Transaction History</span>
            </a>
            <div id="collapse10" <?php if (
                    $content_block == 'report/recharge-history' ||
                    $content_block == 'payment/list' ||
                    $content_block == 'report/loan-list' ||
                    $content_block == 'report/loan-detail' ||
                    $content_block == 'report/bbps-list' ||
                    $content_block == 'report/money-transfer-list' ||
                    $content_block == 'report/recharge-commission-list' ||
                    $content_block == 'report/fund-transfer-commission-list'
                ) { ?> class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading10"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Transaction History:</h6>
                    <?php if ($this->User->admin_menu_permission(17, 2)) { ?>
                    <a class="collapse-item" target="_blank" href="{site_url}employe/report/liveRecharge">Live
                        Recharge</a>
                    <a class="collapse-item" href="{site_url}employe/report/recharge">Recharge History</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(19, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/bbpsHistory">BBPS History</a>
                    <?php } ?>
                    <?php if (in_array(8, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/dmtHistory">DMT Report</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(19, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/matmHistory">MATM Txn History</a>
                    <?php } ?>
                    <?php if (in_array(23, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/moneyTransfer">Payout History</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(20, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/moneyTransferHistory">Money Transfer
                        History</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(131, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/settlementMoneyTransferHistory">Money
                        Transfer 2 History</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(83, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/upiCollectionReport">UPI Collection
                        History</a>
                    <a class="collapse-item" href="{site_url}employe/report/upiChargebackReport">UPI Chargeback
                        History</a>
                    <a class="collapse-item" href="{site_url}employe/report/upiQrHistory">QR History</a>
                    <?php } ?>
                    <?php if (in_array(7, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/upiCashReport">UPI Cash History</a>
                    <?php } ?>
                    <?php if (in_array(9, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/utiPancardReport">UTI Pancard History</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(115, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/nsdlActivationList">NSDL PAN Activation</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(114, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/nsdlPanCardList">NSDL Pancard History</a>

                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(116, 2)) { ?>

                    <a class="collapse-item" href="{site_url}employe/report/findPanReport">Find PAN Report</a>

                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(117, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/newAepsKyc">NSDL BANK AEPS Kyc</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(118, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/newAepsHistory">NSDL BANK AEPS History</a>

                    <?php } ?>


                    <?php if ($this->User->admin_menu_permission(119, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/aepsKyc">AEPS 3 Kyc</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(120, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/fingpayAepsHistory"> AEPS 3 History</a>

                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(110, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/iciciAepsHistory"> ICICI AEPS History</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(111, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/iciciAepsKyc">ICICI AEPS Kyc</a>
                    <?php } ?>


                    <?php if ($this->User->admin_menu_permission(121, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/newPayoutTransfer">New Aeps Payout
                        History</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(109, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/newMoneyTransferHistory"> Settlement
                        <br />(instantpay/yes bank) <br /> History</a>
                    <a class="collapse-item" href="{site_url}employe/report/newMoneyTransferHistoryOld"> ICICI Payout
                        History Old</a>
                    <!--<a class="collapse-item" href="{site_url}employe/report/upiTransferHistory"> UPI Payout History</a>-->
                    <?php } ?>


                    <?php if ($this->User->admin_menu_permission(130, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/openMoneyTransferHistory"> Open Money
                        Payout</a>
                    <!-- <a class="collapse-item" href="{site_url}admin/report/upiTransferHistory"> UPI Payout History</a> -->
                    <?php } ?>


                    <?php if ($this->User->admin_menu_permission(132, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/scanPayTransferHistory">Scan & Pay Report
                    </a>

                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(114, 2)) { ?>
                    <!-- <a class="collapse-item" href="{site_url}employe/report/openMoneyUpiTransferHistory">Open UPI Payout
                        History</a> -->

                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(137, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/fingpayRecon">Aeps Recon Report</a>

                    <?php } ?>

                    <a class="collapse-item" href="{site_url}employe/report/referralComReport">Referral Commission</a>
                    <!-- <a class="collapse-item" href="{site_url}employe/report/balanceReport">Balance Report</a>
             <a class="collapse-item" href="{site_url}employe/report/commissionReport">Commission Report</a> -->
                    <?php if ($this->User->admin_menu_permission(112, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/tdsReport">TDS Report</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(113, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/gstReport">GST Report</a>
                    <?php } ?>

                    <!--   <a class="collapse-item" href="{site_url}employe/report/topupHistory">PG Txn History</a> -->
                    <?php if ($this->User->admin_menu_permission(125, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/utiBalanceReport">UTI Balance Request <span
                            class="menu_notification"><?php echo $this->User->getTotalUnreadTicket(); ?></span> </a>

                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(140, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/addFundReport">Add Fund Report</a>
                    <?php } ?>

                </div>
            </div>
        </li>
        <?php } ?>

        <?php if ($this->User->admin_menu_permission(23, 1)) { ?>

        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCommissionHistory"
                aria-expanded="true" aria-controls="collapseCommissionHistory">
                <i class="fa fa-list" style="color: #f3baba !important;" style="color: #f3baba !important;"></i>
                <span>Commission History</span>
            </a>
            <div id="collapseCommissionHistory" <?php if (
                    $content_block == 'report/recharge-history' ||
                    $content_block == 'payment/list' ||
                    $content_block == 'report/loan-list' ||
                    $content_block == 'report/loan-detail' ||
                    $content_block == 'report/bbps-list' ||
                    $content_block == 'report/money-transfer-list' ||
                    $content_block == 'report/recharge-commission-list' ||
                    $content_block == 'report/fund-transfer-commission-list'
                ) { ?> class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading10"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Commission History:</h6>
                    <?php if (in_array(1, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/rechargeCommision">Recharge Commission</a>
                    <?php } ?>
                    <?php if (in_array(4, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/bbpsCommision">BBPS Commission</a>
                    <?php } ?>
                    <?php if (in_array(8, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/moneyTransferCommision">DMT Charge</a>
                    <?php } ?>
                    <?php if (in_array(3, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/aepsCommision">AEPS Commission</a>
                    <?php } ?>
                    <?php if (in_array(2, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/fundTransferCommision">Payout Charge</a>
                    <?php } ?>
                    <?php if (in_array(6, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/openPayoutCommision">Open Payout Charge</a>
                    <?php } ?>

                    <?php if (in_array(5, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/upiCommision">UPI Collection Charge</a>
                    <?php } ?>
                    <?php if (in_array(7, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/upiCashCommision">UPI Cash Commission</a>
                    <?php } ?>
                    <?php if (in_array(15, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/report/cashDepositeReport">Cash Deposite Report</a>
                    <?php } ?>


                </div>
            </div>
        </li>
        <?php } ?>

        <?php if ($this->User->admin_menu_permission(10, 1)) { ?>


        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed menu_notification_li" href="#" data-toggle="collapse" data-target="#collapse9"
                aria-expanded="true" aria-controls="collapse9">
                <i class="fas fa-hands-helping" style="color: #f3baba !important;"
                    style="color: #f3baba !important;"></i>
                <span>Support Ticket</span>
                <span class="menu_notification"><?php echo $this->User->getTotalUnreadTicket(); ?></span>
            </a>
            <div id="collapse9"
                <?php if ($content_block == 'ticket/ticketList' || $content_block == 'ticket/create' || $content_block == 'ticket/ticketDetail') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Support Ticket:</h6>
                    <a class="collapse-item" href="{site_url}employe/ticket/ticketList">View Ticket</a>

                </div>
            </div>
        </li>
        <?php } ?>

        <?php if ($this->User->admin_menu_permission(25, 1)) { ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed menu_notification_li" href="#" data-toggle="collapse" data-target="#collapse99"
                aria-expanded="true" aria-controls="collapse99">
                <i class="fas fa-question" style="color: #f3baba !important;" style="color: #f3baba !important;"></i>
                <span>Complain</span>
                <span class="menu_notification">0</span>
            </a>
            <div id="collapse99" <?php if ($content_block == 'complain/list') { ?> class="collapse show"
                <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Complain:</h6>
                    <a class="collapse-item" href="{site_url}employe/complain">View Complain</a>

                </div>
            </div>
        </li>
        <?php } ?>
        <?php if ($this->User->admin_menu_permission(4, 1)) { ?>
        <hr class="sidebar-divider my-0">


        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse111"
                aria-expanded="true" aria-controls="collapse111">
                <i class="fa fa-list"></i>
                <span>My Commision</span>
            </a>
            <div id="collapse111" <?php if (
                    $content_block == 'master/my-commission' ||
                    $content_block == 'master/my-bbpsCommission' ||
                    $content_block == 'master/my-rechargeCommission' ||
                    $content_block == 'master/my-transfer-commision' ||
                    $content_block == 'master/my-aeps-commision'
                ) { ?> class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading10"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Master Setting:</h6>
                    <?php if ($accountData['account_type'] == 2) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/myRechargeCommission">Recharge
                        Commission</a>
                    <?php } ?>
                    <?php if ($isInstantPayApiAllow) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/myRechargeCommission">Recharge
                        Commission</a>
                    <?php } ?>
                    <?php if (in_array(4, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/myBbpsLiveCommission">BBPS Commission</a>
                    <?php } ?>
                    <?php if (in_array(8, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/myAccountVerifyCharge">Account Verify
                        Charge</a>
                    <a class="collapse-item" href="{site_url}employe/master/myDmtCharge">DMT Charge</a>
                    <?php } ?>
                    <?php if (in_array(3, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/myAepsCommision">AEPS Commission</a>
                    <?php } ?>
                    <?php if (in_array(16, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/myNsdlPancardCharge">NSDL Pancard Charge</a>

                    <?php } ?>



                </div>
            </div>
        </li>
        <?php } ?>
        <?php if ($this->User->admin_menu_permission(8, 1)) { ?>

        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse25" aria-expanded="true"
                aria-controls="collapse25">
                <i class="fas fa-box" style="color: #f3baba !important;"></i>
                <span>Package Management</span>
            </a>
            <div id="collapse25" class="collapse" aria-labelledby="heading25" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Package Management:</h6>
                    <a class="collapse-item" href="{site_url}employe/package/addPackage">Add Package</a>
                    <a class="collapse-item" href="{site_url}employe/package">View Package</a>
                </div>
            </div>
        </li>

        <?php } ?>
        <?php $activeGateway = $this->User->account_active_gateway(); ?>
        <?php if ($this->User->admin_menu_permission(5, 1)) { ?>
        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse11" aria-expanded="true"
                aria-controls="collapse11">
                <i class="fa fa-list"></i>
                <span>Master Setting</span>
            </a>
            <div id="collapse11" <?php if (
                    $content_block == 'master/commission' ||
                    $content_block == 'master/bbpsCommission' ||
                    $content_block == 'master/wallet' ||
                    $content_block == 'master/transfer-commision' ||
                    $content_block == 'master/aeps-commision' ||
                    $content_block == 'master/service'
                ) { ?> class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading10"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Master Setting:</h6>
                    <?php if ($this->User->admin_menu_permission(37, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/commission">Recharge Commission</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(39, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/bbpsLiveCommission">BBPS Commission</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(42, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/aepsCommision">AEPS Commission</a>
                    <?php } ?>

                    <a class="collapse-item" href="{site_url}employe/master/panActivationCharge">PAN Activation
                        Charge</a>
                    <a class="collapse-item" href="{site_url}employe/master/findPanCharge"> Find PAN Charge</a>
                    <a class="collapse-item" href="{site_url}employe/master/panCharge">NSDL Pancard Charge</a>


                    <a class="collapse-item" href="{site_url}employe/master/nsdlPancardCharge">NSDL Pancard Charge</a>


                    <a class="collapse-item" href="{site_url}employe/master/utiCommision">UTI Pancard Charge</a>

                    <?php if ($this->User->admin_menu_permission(40, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/transferCommision">Payout Charge</a>
                    <?php } ?>
                    <?php if (in_array(6, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/moneyTransferCommision">Open Payout
                        Charge</a>
                    <?php } ?>

                    <?php if (in_array(23, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/xpressPayoutCharge">Xpress Payout Charge</a>
                    <?php } ?>
                    <?php if ($this->User->admin_menu_permission(43, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/upiCommision">UPI Collection Charge</a>
                    <?php } ?>
                    <?php if (in_array(7, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/upiCashCommision">UPI Cash Commission</a>
                    <?php } ?>
                    <a class="collapse-item" href="{site_url}employe/master/gatewayCharge">PG Txn Charge</a>
                    <?php if (in_array(1, $activeGateway)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/payment">Payment Setting</a>
                    <?php } ?>

                    <a class="collapse-item" href="{site_url}employe/master/referralCommision">Referral Commission</a>
                    <a class="collapse-item" href="{site_url}employe/master/ipsetting">Manage IP</a>

                    <?php if ($accountData['is_payout_otp'] == 1) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/payoutOtpSetting">Payout Amount Setting</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(132, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/newMoneyTransferCharge">Money Transfer 2
                        Charge</a>
                    <?php } ?>


                    <?php if ($this->User->admin_menu_permission(139, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/upiApiSwitch">Payin Api Swtich</a>
                    <?php } ?>


                    <?php if ($this->User->admin_menu_permission(141, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/scanPayCommision">Scan & Pay Charge</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(142, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/openMoneyPayoutCharge">Open Money Payout
                        Charge</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(143, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/addFundCommision">Add Fund Charge</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(144, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/aepsTranscationCharge">AEPS 2 FA Charge</a>
                    <?php } ?>

                    <?php if ($this->User->admin_menu_permission(145, 2)) { ?>
                    <a class="collapse-item" href="{site_url}employe/master/accountVerifyCharge">Account Verify
                        Charge</a>
                    <?php } ?>


                </div>
            </div>
        </li>

        <?php } ?>

        <?php if ($this->User->admin_menu_permission(9, 1)) { ?>

        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse88" aria-expanded="true"
                aria-controls="collapse88">
                <i class="fas fa-server" style="color: #f3baba !important;"></i>
                <span>API Master</span>
            </a>
            <div id="collapse88" <?php if (
                    $content_block == 'api/addApi' ||
                    $content_block == 'api/apiList' ||
                    $content_block == 'api/operatorList' ||
                    $content_block == 'api/circleList' ||
                    $content_block == 'api/changeApi' ||
                    $content_block == 'api/amountFilter'
                ) { ?> class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">API Master:</h6>
                    <a class="collapse-item" href="{site_url}employe/api/apiList">Recharge API List</a>
                    <a class="collapse-item" href="{site_url}employe/api/changeApi">Change API</a>
                    <a class="collapse-item" href="{site_url}employe/api/amountFilter">Amount Filter</a>

                </div>
            </div>
        </li>

        <?php } ?>
        <!-- <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUpi" aria-expanded="true" aria-controls="collapseUpi">
          <i class="flaticon-life-insurance"></i>
          <span>UPI QR Code</span>
        </a>
        <div id="collapseUpi" <?php if ($content_block == '') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
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
        <div id="collapseLoanQR" <?php if ($content_block == '') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Loan QR Code :</h6>

                         <a class="collapse-item" href="{site_url}admin/member/instantLoan">Instant Loan</a>


                        </div>
        </div>
      </li>  -->
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomLink"
                aria-expanded="true" aria-controls="collapseThree">
                <i class="fa fa-link" style="color: #f3baba !important;"></i>
                <span>Custom Link</span>
            </a>
            <div id="collapseCustomLink" <?php if ($content_block == 'link/list' || $content_block == 'link/add') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="headingThree"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Custom Link:</h6>
                    <a class="collapse-item" href="{site_url}employe/link/add">Add Link</a>
                    <a class="collapse-item" href="{site_url}employe/link"> View Link</a>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider my-0">


        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse18" aria-expanded="true"
                aria-controls="collapse11">
                <i class="fas fa-globe" style="color: #f3baba !important;"></i>
                <span>Website</span>
            </a>
            <div id="collapse18" <?php if ($content_block == 'master/commission') { ?> class="collapse show"
                <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading10"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Website:</h6>
                    <a class="collapse-item" href="{site_url}employe/website/slider">Home Slider</a>
                    <a class="collapse-item" href="{site_url}employe/website/appSlider">App Slider</a>
                    <a class="collapse-item" href="{site_url}employe/website/contact">Contact Detail</a>
                    <a class="collapse-item" href="{site_url}employe/website/account">Account Detail</a>
                    <a class="collapse-item" href="{site_url}employe/website/service">Our Services</a>
                    <a class="collapse-item" href="{site_url}employe/website/testimonial">Testimonial</a>
                    <a class="collapse-item" href="{site_url}employe/website/news">News</a>
                    <?php if ($accountData['web_theme'] == 1) { ?>
                    <a class="collapse-item" href="{site_url}employe/website/blogList">Blog</a>
                    <a class="collapse-item" href="{site_url}employe/website/featureList">Feature</a>
                    <a class="collapse-item" href="{site_url}employe/website/enquiryList">Enquiry</a>
                    <?php } ?>
                    <a class="collapse-item" href="{site_url}employe/website/pages">Custom Pages</a>
                    <a class="collapse-item" href="{site_url}employe/website/privacy">Privacy Policy</a>
                    <a class="collapse-item" href="{site_url}employe/website/terms">Terms & Condition</a>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse13" aria-expanded="true"
                aria-controls="collapse13">
                <i class="fa fa-cog" style="color: #f3baba !important;"></i>
                <span>Setting</span>
            </a>
            <div id="collapse13"
                <?php if ($content_block == 'setting/profile' || $content_block == 'setting/change-password') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Setting:</h6>
                    <a class="collapse-item" href="{site_url}employe/setting/profile">My Profile</a>
                    <a class="collapse-item" href="{site_url}employe/setting/changePassword">Change Password</a>
                    <a class="collapse-item" href="{site_url}employe/setting/changeTranscationPassword">Change
                        Transcation Password</a>
                    <a class="collapse-item" href="{site_url}employe/setting/changeTheme">Theme Setting</a>
                    <a class="collapse-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>

                </div>
            </div>
        </li>

        <?php if ($accountData['account_type'] != 2) { ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse12" aria-expanded="true"
                aria-controls="collapse12">
                <i class="fas fa-laptop-code" style="color: #f3baba !important;"></i>
                <span>System</span>
            </a>
            <div id="collapse12"
                <?php if ($content_block == 'system/logList' || $content_block == 'system/callBackLogList') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">System:</h6>
                    <a class="collapse-item" href="{site_url}employe/system/logList">View Log</a>
                    <a class="collapse-item" href="{site_url}employe/system/callBackLogList">Callback Log</a>
                    <?php if (in_array(3, $activeService)) { ?>
                    <a class="collapse-item" href="{site_url}employe/system/aepsApiLogList">AEPS API Log</a>
                    <?php } ?>
                    <a class="collapse-item" href="{site_url}employe/system/settlementLogList">Settlement Log</a>
                    <a class="collapse-item" href="{site_url}employe/report/upiApiLog">UPI API Log</a>
                    <a class="collapse-item" href="{site_url}employe/report/payoutApiLog">Payout API Log</a>

                </div>
            </div>
        </li>
        <?php } ?>

        <?php if ($accountData['is_app_notification'] == 1) { ?>
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{site_url}employe/dashboard/sendNotification">
                <i class="fa fa fa-bell" style="color: #f3baba !important;"></i>
                <span>Send App Notification</span></a>
        </li>
        <?php } ?>
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
                        <i class="fa fa-bars" style="color: #f3baba !important;"></i>
                    </button>

                    <!-- Topbar Search -->
                    <h4><?php echo $accountData['title']; ?></h4>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown" style="padding-top: 25px; padding-right: 25px;">
                            <h6>
                                <b>Commision : &#8377;
                                    <?php echo number_format($this->User->getMemberAepsCommisionBlance($loggedUser['id']), 2); ?></b>
                            </h6>
                        </li>
                        <li class="nav-item dropdown" style="padding-top: 25px; padding-right: 25px;">
                            <h6>
                                <b>Virtual-Wallet : &#8377;
                                    <?php echo number_format($this->User->getMemberVirtualWalletBalance($loggedUser['id']), 2); ?></b>
                            </h6>
                        </li>
                        <li class="nav-item dropdown" style="padding-top: 25px; padding-right: 25px;">
                            <h6>
                                <b>S-Wallet : &#8377;
                                    <?php echo number_format($this->User->getMemberCollectionWalletBalance($loggedUser['id']), 2); ?></b>
                            </h6>
                        </li>


                        <li class="nav-item dropdown" style="padding-top: 25px;">
                            <h6>
                                <b>API Balance - <?php echo $this->User->getPayoutAPIBalance(); ?></b>/-
                            </h6>
                        </li>


                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">

                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php
                                    $data = $this->db->get_where('users', ['id' => $loggedUser['id']])->row_array();
                                    echo $data['name'] . '</br>( ' . $data['user_code'] . ' )';
                                    ?>

                                </span>
                                <img class="img-profile rounded-circle" src="{site_url}skin/admin/img/user.png">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{site_url}employe/setting/profile">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"
                                        style="color: #f3baba !important;"></i>
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
                $news = $this->db->get_where('website_news', ['account_id' => $account_id])->result_array();
                if ($news) { ?>
                <div class="news_toop">
                    <marquee>
                        <ol>
                            <?php
                            $i = 1;
                            foreach ($news as $list) { ?>
                            <li><?php echo $i; ?>. <?php echo $list['news']; ?></li>
                            <?php $i++;}
                            ?>
                        </ol>
                    </marquee>

                </div>
                <?php }
                ?>
            </div>
            <!-- End of Topbar -->