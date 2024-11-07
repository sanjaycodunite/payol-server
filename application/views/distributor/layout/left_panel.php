<?php 
$account_id = $this->User->get_domain_account();
$accountData = $this->User->get_account_data($account_id);
$loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);
$user_aeps_status = $this->User->get_member_aeps_status($loggedUser['id']);
$user_new_aeps_status = $this->User->get_member_new_aeps_status($loggedUser['id']);
$user_instantpay_aeps_status = $this->User->get_member_instantpay_aeps_status($loggedUser['id']);
$user_aeps3_status = $this->User->get_member_fingpay_aeps_status($loggedUser['id']);
$customLinkList = $this->db->get_where('custom_link',array('account_id'=>$account_id,'status'=>1))->result_array();
$member_dmt_status = $this->User->getMemberDMTStatus($loggedUser['id']);
$isInstantPayApiAllow = $this->User->get_account_instantpay_api_status($account_id);
$isNsdlActive = $this->User->get_nsdl_pancard_status($loggedUser['id']);
$activeGateway = $this->User->account_active_gateway();
?>
<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center"
            href="{site_url}distributor/dashboard">
            <div class="sidebar-brand-icon">
                <img src="{site_url}<?php echo $accountData['image_path']; ?>" class="img-responsive">
            </div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">
        <!-- Nav Item - Dashboard  -->
        <li class="nav-item">
            <a class="nav-link" href="{site_url}distributor/home">
                <i class="fas fa-fw fa-home"></i>
                <span>Home</span></a>
        </li>

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{site_url}distributor/dashboard">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <hr class="sidebar-divider my-0">


        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
                aria-expanded="true" aria-controls="collapseThree">
                <i class="fa fa-users"></i>
                <span>Members</span>
            </a>
            <div id="collapseThree"
                <?php if($content_block == 'member/memberList' || $content_block == 'member/addMember'  || $content_block == 'member/editMember'  || $content_block == 'member/mdMemberList' || $content_block == 'member/distributorList' || $content_block == 'member/retailerList' || $content_block == 'member/apiMemberList') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="headingThree"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Member Management:</h6>
                    <a class="collapse-item" href="{site_url}distributor/member/addMember">Add Member</a>
                    <a class="collapse-item" href="{site_url}distributor/member/memberList">View All Member</a>
                    <a class="collapse-item" href="{site_url}distributor/member/retailerList">Retailer</a>
                    <a class="collapse-item" href="{site_url}distributor/member/userList">Users</a>


                </div>
            </div>
        </li>
        <?php $activeService = $this->User->account_active_service($loggedUser['id']); ?>
        <?php $adminActiveService = $this->User->admin_active_service(); ?>
        <?php if(in_array(1, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse5" aria-expanded="true"
                aria-controls="collapseThree">
                <i class="fa fa-mobile"></i>
                <span>Recharge</span>
            </a>
            <div id="collapse5"
                <?php if($content_block == 'recharge/mobile-prepaid' || $content_block == 'recharge/mobile-postpaid' || $content_block == 'recharge/dth' || $content_block == 'recharge/electricity' || $content_block == 'recharge/datacard' || $content_block == 'recharge/landline' || $content_block == 'recharge/broadband' ) { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading5"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Recharge:</h6>
                    <?php if($isInstantPayApiAllow){ ?>
                    <a class="collapse-item <?php if($content_block == 'recharge/ekyc'){ ?> active <?php } ?> "
                        href="{site_url}distributor/recharge/ekyc">Merchant eKyc</a>
                    <?php } ?>
                    <a class="collapse-item <?php if($content_block == 'recharge/mobile-prepaid'){ ?> active <?php } ?> "
                        href="{site_url}distributor/recharge/mobileprepaid">Mobile</a>

                    <a class="collapse-item <?php if($content_block == 'recharge/dth'){ ?> active <?php } ?> "
                        href="{site_url}distributor/recharge/dth">DTH</a>


                </div>
            </div>
        </li>
        <?php } ?>
        <?php if(in_array(4, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link" href="{site_url}distributor/bbps">
                <i class="fa fa-tv"></i>
                <span>BBPS Live</span></a>
        </li>
        <?php } ?>
        <?php if(in_array(8, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDmt"
                aria-expanded="true" aria-controls="collapseDmt">
                <i class="fa fa-mobile"></i>
                <span>DMT</span>
            </a>
            <div id="collapseDmt" class="collapse" aria-labelledby="heading5" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">DMT:</h6>
                    <a class="collapse-item" href="{site_url}distributor/dmt/transferNow">Remitter</a>
                    <a class="collapse-item" href="{site_url}distributor/dmt">Transfer Report</a>
                </div>
            </div>
        </li>
        <?php } ?>
        <?php if(in_array(3, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse16" aria-expanded="true"
                aria-controls="collapse16">
                <i class="fa fa-bullhorn"></i>
                <span>AEPS1 Service</span>
            </a>
            <div id="collapse16" <?php if($content_block == 'aeps/list' || $content_block == 'aeps/transfer') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading11"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">AEPS1 Service:</h6>
                    <?php if(!$user_aeps_status){ ?>
                    <a class="collapse-item" href="{site_url}distributor/aeps/activeAeps">Active AEPS</a>
                    <?php } else { ?>
                    <a class="collapse-item" href="{site_url}distributor/aeps">AEPS Now</a>
                    <a class="collapse-item" href="{site_url}distributor/aeps/transactionHistory">Transaction
                        History</a>
                    <?php } ?>
                </div>
            </div>
        </li>

        <?php } ?>
        <?php if(in_array(2, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse88" aria-expanded="true"
                aria-controls="collapse88">
                <i class="fa fa-mobile"></i>
                <span>AEPS Payout</span>
            </a>
            <div id="collapse88"
                <?php if($content_block == 'transfer/list' || $content_block == 'transfer/transfer') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading5"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">AEPS Payout:</h6>
                    <a class="collapse-item" href="{site_url}distributor/transfer/payoutBeneficiaryList">Beneficiary
                        List</a>

                    <a class="collapse-item" href="{site_url}distributor/transfer/payoutFundTransfer">Transfer Now</a>

                    <a class="collapse-item" href="{site_url}distributor/transfer">Payout Report</a>


                    <a class="collapse-item" href="{site_url}distributor/transfer/benificaryAccountList"> New Account
                        Request</a>


                </div>
            </div>
        </li>
        <?php } ?>


        <?php if(in_array(17, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAEPS"
                aria-expanded="true" aria-controls="collapseAEPS">
                <i class="fa fa-bullhorn"></i>
                <span><?php echo $this->User->getAepsTitle(); ?> Service</span>
            </a>
            <div id="collapseAEPS"
                <?php if($content_block == 'newaeps/list'  || $content_block == 'newaeps/member-activation' || $content_block == 'newaeps/transfer') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading11"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">AEPS2 Service:</h6>
                    <?php if(!$user_new_aeps_status){ ?>
                    <a class="collapse-item" href="{site_url}distributor/newaeps/activeAeps">Active AEPS</a>
                    <?php } else { ?>
                    <a class="collapse-item" href="{site_url}distributor/newaeps">AEPS Now</a>
                    <a class="collapse-item" href="{site_url}distributor/newaeps/transactionHistory">Transaction
                        History</a>
                    <?php } ?>
                </div>
            </div>
        </li>

        <?php } ?>



        <?php if(in_array(18, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse898"
                aria-expanded="true" aria-controls="collapse898">
                <i class="fa fa-mobile"></i>
                <span> <?php echo $this->User->getAepsTitle(); ?> AEPS Payout</span>
            </a>
            <div id="collapse898"
                <?php if($content_block == 'newaeps/list' || $content_block == 'newaeps/transfer') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading5"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header"> <?php echo $this->User->getAepsTitle(); ?> AEPS Payout:</h6>
                    <a class="collapse-item" href="{site_url}distributor/newaeps/payout">Transfer Now</a>

                </div>
            </div>
        </li>
        <?php } ?>






        <?php if(in_array(19, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInstant"
                aria-expanded="true" aria-controls="collapseInstant">
                <i class="fa fa-bullhorn"></i>
                <span>ICICI AEPS</span>
            </a>
            <div id="collapseInstant"
                <?php if($content_block == 'iciciaeps/list' || $content_block == 'aeps/transfer') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading11"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">ICICI AEPS:</h6>
                    <?php if(!$user_instantpay_aeps_status){ ?>
                    <a class="collapse-item" href="{site_url}distributor/iciciaeps/activeAeps">Active AEPS</a>
                    <?php } else { ?>
                    <a class="collapse-item" href="{site_url}distributor/iciciaeps">AEPS Now</a>

                    <?php } ?>
                </div>
            </div>
        </li>

        <?php } ?>




        <?php if(in_array(20, $activeService)){ 
          
        ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseinst"
                aria-expanded="true" aria-controls="collapseinst">
                <i class="fa fa-mobile"></i>
                <span>Settlement</span>
            </a>
            <div id="collapseinst"
                <?php if($content_block == 'transfer/newPayoutlist' || $content_block == 'transfer/transfer') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading5"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Settlement:</h6>
                    <a class="collapse-item" href="{site_url}distributor/transfer/settlement">Settlement </a>

                    <a class="collapse-item" href="{site_url}distributor/transfer/addBankAccount">Add Account</a>

                    <a class="collapse-item" href="{site_url}distributor/transfer/newPayoutReport">Settlement Report</a>


                </div>
            </div>
        </li>
        <?php   } ?>



        <?php if(in_array(30, $activeService)){ 
          
        ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsesettlement"
                aria-expanded="true" aria-controls="collapsesettlement">
                <i class="fa fa-mobile"></i>
                <span>Settlement 2 </span>
            </a>
            <div id="collapsesettlement"
                <?php if($content_block == 'settlement/newPayoutlist' || $content_block == 'settlement/transfer') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading5"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Settlement:</h6>

                    <a class="collapse-item" href="{site_url}distributor/settlement">Settlement </a>

                    <a class="collapse-item" href="{site_url}distributor/settlement/addBankAccount">Add Account</a>

                    <a class="collapse-item" href="{site_url}distributor/settlement/newPayoutReport">Settlement
                        Report</a>

                </div>
            </div>
        </li>
        <?php   } ?>




        <!--  <?php if(in_array(20, $activeService)){ 
          if($user_instantpay_aeps_status){
        ?>
      <hr class="sidebar-divider my-0">
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseupi" aria-expanded="true" aria-controls="collapseupi">
          <i class="fa fa-mobile"></i>
          <span>UPI Payout</span>
        </a>
        <div id="collapseupi" <?php if($content_block == 'transfer/upiPayoutlist' || $content_block == 'transfer/transfer') { ?> class="collapse show" <?php } else { ?> class="collapse"<?php } ?> aria-labelledby="heading5" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">UPI Payout:</h6>
            <a class="collapse-item" href="{site_url}distributor/transfer/upiPayoutBeneficiaryList">Beneficiary List</a>
              
              <a class="collapse-item" href="{site_url}distributor/transfer/upiPayoutFundTransfer">Transfer Now</a>
              
              <a class="collapse-item" href="{site_url}distributor/transfer/upiPayoutReport">Payout Report</a>


             
               
            </div>
        </div>
      </li>
      <?php  } } ?> -->



        <?php if(in_array(25, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse16" aria-expanded="true"
                aria-controls="collapse16">
                <i class="fa fa-bullhorn"></i>
                <span>AEPS3 Service</span>
            </a>
            <div id="collapse16"
                <?php if($content_block == 'fingpayaeps/list' || $content_block == 'fingpayaeps/transfer') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading11"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">AEPS3 Service:</h6>
                    <?php if(!$user_aeps3_status){ ?>
                    <a class="collapse-item" href="{site_url}distributor/fingpayAeps/activeAeps">Active AEPS</a>
                    <?php } else { ?>
                    <a class="collapse-item" href="{site_url}distributor/fingpayAeps">AEPS Now</a>
                    <a class="collapse-item" href="{site_url}distributor/fingpayAeps/transactionHistory">Transaction
                        History</a>
                    <?php } ?>
                </div>
            </div>
        </li>
        <?php } ?>







        <?php if(in_array(6, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMoney"
                aria-expanded="true" aria-controls="collapseMoney">
                <i class="fa fa-mobile"></i>
                <span>Money Transfer</span>
            </a>
            <div id="collapseMoney" class="collapse" aria-labelledby="heading5" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Money Transfer:</h6>

                    <a class="collapse-item" href="{site_url}distributor/transfer/openPayout">Money Transfers</a>
                    <a class="collapse-item" href="{site_url}distributor/report/moneyTransferHistory">Transfer
                        Report</a>
                </div>
            </div>
        </li>
        <?php } ?>


        <?php if(in_array(30, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMoney2"
                aria-expanded="true" aria-controls="collapseMoney2">
                <i class="fa fa-mobile"></i>
                <span>Money Transfer 2</span>
            </a>
            <div id="collapseMoney2" class="collapse" aria-labelledby="heading5" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Money Transfer 2:</h6>
                    <!--  <a class="collapse-item" href="{site_url}retailer/transfer/beneficiaryList">Beneficiary List</a>
            <a class="collapse-item" href="{site_url}retailer/transfer/fundTransfer">Transfer Now</a>-->
                    <a class="collapse-item" href="{site_url}distributor/settlement/openPayout">Money Transfer</a>
                    <a class="collapse-item" href="{site_url}distributor/settlement/settlementTransferReport">Transfer
                        Report</a>
                </div>
            </div>
        </li>
        <?php } ?>


        <?php if(in_array(9, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsepan"
                aria-expanded="true" aria-controls="collapsepan">
                <i class="fa fa-credit-card"></i>
                <span>UTI Pancard</span>
            </a>
            <div id="collapsepan" class="collapse" aria-labelledby="heading5" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">UTI Pancard:</h6>
                    <a class="collapse-item" href="{site_url}distributor/pancard/activeService">Activate UTI</a>
                    <a class="collapse-item" href="{site_url}distributor/pancard/couponList">Coupon List</a>
                </div>
            </div>
        </li>
        <?php } ?>


        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsensdlpan"
                aria-expanded="true" aria-controls="collapsensdlpan">
                <i class="fa fa-credit-card"></i>
                <span>NSDL Pancard</span>
            </a>
            <div id="collapsensdlpan" class="collapse" aria-labelledby="heading5" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">NSDL Pancard:</h6>
                    <?php if(in_array(16, $activeService)){ ?>
                    <?php if(!$isNsdlActive){ ?>
                    <a class="collapse-item" href="{site_url}distributor/pancard/nsdlActive">Activate</a>
                    <?php }  else { ?>
                    <a class="collapse-item" href="{site_url}distributor/pancard/nsdlPan">New PAN</a>
                    <a class="collapse-item" href="{site_url}distributor/report/nsdlPanCardReport">PAN Report</a>
                    <?php } ?>
                    <?php } ?>
                    <a class="collapse-item" href="{site_url}distributor/pancard/findPanList">Find PAN No</a>
                    <a class="collapse-item" href="{site_url}distributor/pancard/utiBalanceRequest">UTI Balance
                        Request</a>

                </div>
            </div>
        </li>



        <hr class="sidebar-divider my-0">

        <?php if(in_array(22, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link" href="{site_url}distributor/nsdl" target="_blank">
                <i class="flaticon-life-insurance"></i>
                <span>NSDL Pan</span></a>
        </li>
        <?php } ?>


        <?php if(in_array(10, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAccount"
                aria-expanded="true" aria-controls="collapseAccount">
                <i class="fa fa-home"></i>
                <span>Account Management </span>
            </a>
            <div id="collapseAccount" <?php if($content_block == '') { ?> class="collapse show" <?php } else { ?>
                class="collapse" <?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Account Management:</h6>

                    <a class="collapse-item" href="{site_url}distributor/current/accountDetail">Virtual Account</a>
                    <a class="collapse-item" href="https://buy.icicibank.com/savings-account/product"
                        target="_blank">Saving Account - ICICI</a>
                    <a class="collapse-item" href="{site_url}distributor/current/axisAccount">Saving Account - Axis</a>
                    <a class="collapse-item" href="{site_url}distributor/pancard/aaplyNsdl">Saving Account - BOM</a>
                    <a class="collapse-item" href="{site_url}distributor/current">Current Account</a>


                </div>
            </div>
        </li>
        <?php } ?>

        <?php if(in_array(5, $activeService)){ ?>

        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUpi"
                aria-expanded="true" aria-controls="collapseUpi">
                <i class="flaticon-life-insurance"></i>
                <span>UPI Transaction</span>
            </a>
            <div id="collapseUpi" <?php if($content_block == '') { ?> class="collapse show" <?php } else { ?>
                class="collapse" <?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">UPI Transaction:</h6>

                    <a class="collapse-item" href="{site_url}distributor/wallet/dynamicQr">UPI Collection</a>
                </div>
            </div>
        </li>
        <?php  } ?>

        <?php if(in_array(13, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTravel"
                aria-expanded="true" aria-controls="collapseTravel">
                <i class="fa fa-bus"></i>
                <span>Travel </span>
            </a>
            <div id="collapseTravel" <?php if($content_block == '') { ?> class="collapse show" <?php } else { ?>
                class="collapse" <?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Travel :</h6>

                    <a class="collapse-item" href="#">Bus Booking</a>
                    <a class="collapse-item" href="#">Flight Booking</a>
                    <a class="collapse-item" href="#">Hotel Booking</a>



                </div>
            </div>
        </li>
        <?php } ?>


        <?php if(in_array(14, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInsurance"
                aria-expanded="true" aria-controls="collapseInsurance">
                <i class="flaticon-life-insurance"></i>
                <span>Insurance </span>
            </a>
            <div id="collapseInsurance" <?php if($content_block == '') { ?> class="collapse show" <?php } else { ?>
                class="collapse" <?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Insurance :</h6>

                    <a class="collapse-item" href="#">Motor Insurance</a>



                </div>
            </div>
        </li>
        <?php } ?>

        <?php if(in_array(11, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link" href="https://www.capricorn.cash/login" target="_blank">
                <i class="fa fa-file-signature"></i>
                <span>Digital Signature</span></a>
        </li>
        <?php } ?>
        <?php if(in_array(12, $activeService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="modal" data-target="#instantLoanModal">
                <i class="fa fa-credit-card"></i>
                <span>Instant Loan</span></a>
        </li>
        <?php } ?>


        <hr class="sidebar-divider my-0">


        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse10" aria-expanded="true"
                aria-controls="collapse10">
                <i class="fa fa-list"></i>
                <span>Reports</span>
            </a>
            <div id="collapse10"
                <?php if($content_block == 'report/recharge-history' || $content_block == 'payment/list' || $content_block == 'report/loan-list' || $content_block == 'report/loan-detail' || $content_block == 'report/bbps-list' || $content_block == 'report/money-transfer-list' || $content_block == 'report/recharge-commission-list' || $content_block == 'report/fund-transfer-commission-list') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading10"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Reports:</h6>
                    <a class="collapse-item" href="{site_url}distributor/report/recharge">Recharge History</a>
                    <?php if(in_array(4, $activeService)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/report/bbpsHistory">BBPS History</a>
                    <?php } ?>

                    <a class="collapse-item" href="{site_url}distributor/report/moneyTransferHistory">Money Transfer
                        Report</a>
                    <?php if(in_array(17, $activeService)){ ?>
                    <a class="collapse-item"
                        href="{site_url}distributor/newaeps/transactionHistory"><?php echo $this->User->getAepsTitle(); ?>
                        Txn History</a>

                    <a class="collapse-item"
                        href="{site_url}distributor/newaeps/transferReport"><?php echo $this->User->getAepsTitle(); ?>
                        Payout History</a>

                    <?php } ?>
                    <?php if(in_array(17, $activeService)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/iciciaeps/transactionHistory">ICICI AEPS Txn
                        History</a>
                    <a class="collapse-item" href="{site_url}distributor/transfer/newPayoutReport"> ICICI Payout
                        Report</a>
                    <?php } ?>

                    <?php if(in_array(16, $activeService)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/report/nsdlPanCardReport"> NSDL PAN Report</a>

                    <?php } ?>


                    <?php if(in_array(1, $activeGateway)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/wallet/topupHistory">PG History</a>
                    <?php } ?>

                    <?php if(in_array(22, $activeService)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/report/nsdlPanReport">NSDL Pan History</a>
                    <?php } ?>

                    <?php if(in_array(26, $activeService)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/report/utiBalanceReport">UTI Balance
                        History</a>
                    <?php } ?>

                    <?php if(in_array(5, $activeService)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/report/upiQrHistory">Qr History</a>
                    <?php } ?>

                </div>
            </div>
        </li>

        <?php $activeGateway = $this->User->account_active_gateway(); ?>
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{site_url}distributor/wallet/payolTransfer">
                <i class="fa fa-dollar"></i>
                <span>Payol Transfer</span></a>
        </li>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse8" aria-expanded="true"
                aria-controls="collapse8">
                <i class="fa fa-file"></i>
                <span>Main Wallet </span>
            </a>
            <div id="collapse8"
                <?php if($content_block == 'wallet/walletList' || $content_block == 'wallet/addWallet' || $content_block == 'member/fundTransferList' || $content_block == 'wallet/fundRequest' || $content_block == 'wallet/requestList' || $content_block == 'wallet/myRequestList' || $content_block == 'wallet/creditList' || $content_block == 'wallet/debitList' || $content_block == 'wallet/myWalletList' ) { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Main Wallet :</h6>
                    <?php if(in_array(1, $activeGateway)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/wallet/topup">Add Fund PG</a>
                    <?php } ?>


                    <?php if(in_array(5, $activeService)){
                         ?>
                    <a class="collapse-item" href="{site_url}distributor/wallet/addFund">Add Fund</a>
                    <?php } ?>

                    <a class="collapse-item" href="{site_url}distributor/wallet/myWalletList">My Wallet</a>
                    <a class="collapse-item" href="{site_url}distributor/wallet/walletList">Member Wallet</a>

                    <a class="collapse-item" href="{site_url}distributor/wallet/creditList">Credit Fund</a>
                    <a class="collapse-item" href="{site_url}distributor/wallet/myRequestList">My Fund Request</a>
                    <a class="collapse-item" href="{site_url}distributor/wallet/requestList">Member Request List</a>

                    <?php 

                          $get_upline_member = $this->db->get_where('users',array('id' =>$loggedUser['id']))->row_array();
    
                         $get_upline_member_id = $get_upline_member['created_by'];

                         $get_member_list = $this->db->get_where('users',array('id'=>$get_upline_member_id))->row_array();


                          if($accountData['is_move_wallet'] == 1 && $get_member_list['role_id'] != 2 )  {?>
                    <a class="collapse-item" href="{site_url}distributor/wallet/moveWalletBalance">Wallet Transfer</a>

                    <?php } ?>


                </div>
            </div>
        </li>

        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSaving"
                aria-expanded="true" aria-controls="collapseSaving">
                <i class="fa fa-file"></i>
                <span>Saving</span>
            </a>
            <div id="collapseSaving" class="collapse" aria-labelledby="heading8" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Saving:</h6>

                    <a class="collapse-item" href="#">Recurring Deposit</a>
                    <a class="collapse-item" href="#">Fixed Deposit</a>
                    <a class="collapse-item" href="{site_url}distributor/saving/clubList">Club</a>

                </div>
            </div>
        </li>
        <hr class="sidebar-divider my-0">


        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse111"
                aria-expanded="true" aria-controls="collapse111">
                <i class="fa fa-list"></i>
                <span>My Commision</span>
            </a>
            <div id="collapse111"
                <?php if($content_block == 'master/my-commission' || $content_block == 'master/my-bbpsCommission' || $content_block == 'master/my-transfer-commision' || $content_block == 'master/my-aeps-commision') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading10"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Master Setting:</h6>
                    <a class="collapse-item" href="{site_url}distributor/master/myCommission">Recharge Commission</a>
                    <?php if(in_array(4, $activeService)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/master/myBbpsLiveCommission">BBPS
                        Commission</a>
                    <?php } ?>
                    <a class="collapse-item" href="{site_url}distributor/master/myAccountVerifyCharge">Account Verify
                        Charge</a>
                    <a class="collapse-item" href="{site_url}distributor/master/myDmtCharge">DMT Charge</a>
                    <a class="collapse-item" href="{site_url}distributor/master/myAepsCommision">AEPS Commission</a>
                    <a class="collapse-item" href="{site_url}distributor/master/myTransferCommision">AEPS Payout
                        Charge</a>
                    <a class="collapse-item" href="{site_url}distributor/master/myMoneyTransferCommision">Money Transfer
                        Charge</a>
                    <?php if(in_array(9, $activeService)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/master/myUtiPancardCommission">UTI Pancard
                        Charge</a>
                    <?php } ?>
                    <?php if(in_array(16, $activeService)){ ?>
                    <a class="collapse-item" href="{site_url}distributor/master/myNsdlPancardCharge">NSDL Pancard
                        Charge</a>
                    <?php } ?>

                    <a class="collapse-item" href="{site_url}distributor/master/myUpiCommision">UPI Collection
                        Charge</a>
                    <a class="collapse-item" href="{site_url}distributor/master/myUpiCashCommision">UPI Cash
                        Commission</a>



                </div>
            </div>
        </li>



        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse9" aria-expanded="true"
                aria-controls="collapse9">
                <i class="fa fa-life-ring"></i>
                <span>Support Ticket</span>
            </a>
            <div id="collapse9"
                <?php if($content_block == 'ticket/ticketList' || $content_block == 'ticket/create' || $content_block == 'ticket/ticketDetail') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Support Ticket:</h6>
                    <a class="collapse-item" href="{site_url}distributor/ticket/create">Create Ticket</a>
                    <a class="collapse-item" href="{site_url}distributor/ticket/ticketList">View Ticket</a>

                </div>
            </div>
        </li>

        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse99" aria-expanded="true"
                aria-controls="collapse99">
                <i class="fa fa-life-ring"></i>
                <span>Complain</span>
            </a>
            <div id="collapse99" <?php if($content_block == 'complain/list') { ?> class="collapse show"
                <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Complain:</h6>
                    <a class="collapse-item" href="{site_url}distributor/complain">View Complain</a>

                </div>
            </div>
        </li>






        <?php if(in_array(15, $adminActiveService)){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDeposit"
                aria-expanded="true" aria-controls="collapseDeposit">
                <i class="fa fa-money-bill-alt"></i>
                <span>Cash Deposite </span>
            </a>
            <div id="collapseDeposit" <?php if($content_block == '') { ?> class="collapse show" <?php } else { ?>
                class="collapse" <?php } ?> aria-labelledby="heading8" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Cash Deposite :</h6>

                    <a class="collapse-item" href="{site_url}distributor/aeps/cashDeposite">Cash Deposite</a>



                </div>
            </div>
        </li>
        <?php } ?>







        <?php if($customLinkList){ ?>
        <?php foreach($customLinkList as $linkList){ ?>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $linkList['url']; ?>" target="_blank">
                <i class="fa fa-link"></i>
                <span><?php echo $linkList['title']; ?></span></a>
        </li>
        <?php } ?>
        <?php } ?>



        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse13" aria-expanded="true"
                aria-controls="collapse13">
                <i class="fa fa-cog"></i>
                <span>Setting</span>
            </a>
            <div id="collapse13"
                <?php if($content_block == 'setting/profile' || $content_block == 'setting/change-password') { ?>
                class="collapse show" <?php } else { ?> class="collapse" <?php } ?> aria-labelledby="heading8"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Setting:</h6>
                    <a class="collapse-item" href="{site_url}distributor/setting/profile">My Profile</a>
                    <a class="collapse-item" href="{site_url}distributor/setting/changePassword">Change Password</a>
                    <a class="collapse-item" href="{site_url}distributor/setting/changeTranscationPassword">Change
                        Transcation Password</a>
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
                                <b>Main Wallet Balance - &#8377;
                                    <?php echo number_format($this->User->getMemberWalletBalanceSP($loggedUser['id']),2); ?></b>
                            </h6>
                        </li>



                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">

                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php
                $data=$this->db->get_where('users',array('id'=>$loggedUser['id']))->row_array();
                echo $data['name'].'</br>( '.$data['user_code'].' )';
                ?>

                                </span>
                                <img class="img-profile rounded-circle" src="{site_url}skin/admin/img/user.png">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{site_url}distributor/setting/profile">
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

            </div>
            <?php $notificationList = $this->User->getClubNotification($loggedUser['id']); ?>
            <?php if($notificationList){ ?>
            <div class="container-fluid">
                <?php foreach($notificationList as $nlist){ ?>
                <?php if($nlist['to_member_id'] == 0){ ?>
                <div class="alert alert-success alert-dismissable"><?php echo $nlist['msg']; ?> <a
                        href="{site_url}distributor/saving/clubList">Accept</a></div>
                <?php } else { ?>
                <div class="alert alert-success alert-dismissable"> <button type="button" class="close"
                        data-dismiss="alert" aria-hidden="true"
                        onclick="closeClubNoti(<?php echo $nlist['id']; ?>);">&times;</button><?php echo $nlist['msg']; ?>
                </div>
                <?php } ?>
                <?php } ?>

            </div>
            <?php } ?>
            <!-- End of Topbar -->

            <div class="nav_dashboard_top">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="nav_top_news">
                                <div>
                                    <h4><i class="fa fa-microphone"></i>Updates</h4>
                                </div>
                                <div style="display: flex;">
                                    <?php  $news = $this->db->get_where('website_news',array('account_id'=>$account_id))->result_array();
                    if($news){
                   ?>

                                    <marquee>
                                        <?php
         $i=1; 
         foreach($news as $list){
         ?>
                                        <p> <?php echo $i; ?>. <?php echo $list['news']; ?> </p>
                                        <?php } ?>
                                    </marquee>



                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>