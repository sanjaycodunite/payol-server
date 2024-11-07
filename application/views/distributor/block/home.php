<div class="container-fluid">
    <div class="row myflex">
        <div class="col-xl-3 col-md-3 mb-2 mt-2">
            <div class="container">
                <input type="text" class="form-control home_search" placeholder="Search...">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-md-12 mb-2 mt-2">
            <!-- Modal moneyTransfer Model -->

            <div class="modal fade" id="moneyTransferModel" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title MoneyTransferModalLongTitle" id="exampleModalCenterTitle">Money Transfer</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card text-center">
                                <div class="card-header">
                                    Choose the desire money transfer mode
                                </div>
                                <div class="card-body">
                                    <a href="{site_url}distributor/settlement/openPayout" class="btn btn-primary btn-sm text-center">Money Transfer 1</a>
                                    <a href="{site_url}distributor/settlement/openPayout" class="btn btn-primary btn-sm text-center">Money Transfer 2</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-----  Settlement Model ---->
            <div class="modal fade" id="settlementMoneyTransferModel" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title settelmentModalLongTitle">Settlement</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card text-center">
                                <div class="card-header">
                                    Choose the desire settlement mode
                                </div>
                                <div class="card-body">
                                    <a href="{site_url}/distributor/settlement"
                                        class="btn btn-primary btn-sm text-center">Settlement 1</a>
                                    <a href="{site_url}/distributor/settlement"
                                        class="btn btn-primary btn-sm text-center">Settlement 2</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!------ E-Kyc Model ----->
            <div class="modal fade" id="eKycModel" tabindex="-1" role="dialog" aria-labelledby="ekycModalCenterTitle"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title ekycModalCenterTitle">E-KYC</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card text-center">
                                <div class="card-header">
                                    Choose the desire E-Kyc mode
                                </div>
                                <div class="card-body">
                                    <a href="{site_url}distributor/report/iciciAepsKyc"
                                        class="btn btn-primary btn-sm text-center">ICICI AEPS Kyc
                                    </a>
                                    <a href="{site_url}distributor/report/aepsKyc"
                                        class="btn btn-primary btn-sm text-center">AEPS 3 Kyc
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="master_list_card">
                <div class="master_card_body">
                    <div class="master_list_col">
                        <a href="{site_url}distributor/report/addFundReport">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon01.png"
                                    class="dash_icon">
                            </div>
                            <h4>Add Fund</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="{site+url}distributor/wallet/payolTransfer">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon02.png"
                                    class="dash_icon"></div>
                            <h4>Payol Transfer</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon03.png"
                                    class="dash_icon"></div>
                            <h4>Balance Enquiry</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon04.png"
                                    class="dash_icon"></div>
                            <h4>Instant PAN Card</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="{site_url}distributor/pancard/findPanList">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon05.png"
                                    class="dash_icon"></div>
                            <h4>Find PAN Card</h4>
                        </a>
                    </div>

                    <div class="master_list_col moneyTransferdiv">
                        <a href="{site_url}distributor/transfer/openPayout">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon06.png"
                                    class="dash_icon"></div>
                            <h4>Money Transfer</h4>
                        </a>
                    </div>
                    <div class="master_list_col settlementMoneyTransferdiv">
                        <a href="{site_url}distributor/transfer/settlement">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon07.png"
                                    class="dash_icon"></div>
                            <h4>Settlement</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/recharge/mobileprepaid">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon09.png"
                                    class="dash_icon"></div>
                            <h4>Mobile Recharge</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/recharge/dth">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon010.png"
                                    class="dash_icon"></div>
                            <h4>DTH Recharge</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon011.png"
                                    class="dash_icon"></div>
                            <h4>Electricity Bill Payment</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon012.png"
                                    class="dash_icon"></div>
                            <h4>FasTag Recharge</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon013.png"
                                    class="dash_icon"></div>
                            <h4>Loan/EMI Payment</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon014.png"
                                    class="dash_icon"></div>
                            <h4>Credit Card Bill</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon015.png"
                                    class="dash_icon"></div>
                            <h4>Education Fees</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon016.png"
                                    class="dash_icon"></div>
                            <h4>Life Insurance</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon017.png"
                                    class="dash_icon"></div>
                            <h4>Landline</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon018.png"
                                    class="dash_icon"></div>
                            <h4>Broadband</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon019.png"
                                    class="dash_icon"></div>
                            <h4>Cable TV</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon020.png"
                                    class="dash_icon"></div>
                            <h4>Water</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon021.png"
                                    class="dash_icon"></div>
                            <h4>Insurance</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon022.png"
                                    class="dash_icon"></div>
                            <h4>Piped Gas</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon023.png"
                                    class="dash_icon"></div>
                            <h4>Hospital</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon024.png"
                                    class="dash_icon"></div>
                            <h4>Subscription</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon025.png"
                                    class="dash_icon"></div>
                            <h4>Housing Society</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon026.png"
                                    class="dash_icon"></div>
                            <h4>Municipal Taxes</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon027.png"
                                    class="dash_icon"></div>
                            <h4>Municipal Services</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/bbps">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon028.png"
                                    class="dash_icon"></div>
                            <h4>Clubs And Associations</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon029.png"
                                    class="dash_icon"></div>
                            <h4>Cash Withdrawal</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="{site_url}distributor/newaeps/cashDeposite">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon030.png"
                                    class="dash_icon"></div>
                            <h4>Cash Deposit</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon031.png"
                                    class="dash_icon"></div>
                            <h4>Mini Statement</h4>
                        </a>
                    </div>
                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon032.png"
                                    class="dash_icon"></div>
                            <h4>Micro ATM</h4>
                        </a>
                    </div>

                    <div class="master_list_col eKycModeldiv">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon1.png" class="dash_icon">
                            </div>
                            <h4>E-KYC</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon2.png" class="dash_icon">
                            </div>
                            <h4>Fixed Deposit</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/icon3.png" class="dash_icon">
                            </div>
                            <h4>Account Opening</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="https://fly24hrs.com/B2BLogin">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/flight_ticket.png"
                                    class="dash_icon"></div>
                            <h4>Flight Booking</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/train_ticket.png"
                                    class="dash_icon"></div>
                            <h4>Train Ticket</h4>
                        </a>
                    </div>


                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/bus_ticket.png"
                                    class="dash_icon"></div>
                            <h4>Bus Booking</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/hotel.png" class="dash_icon">
                            </div>
                            <h4>Hotel Booking</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="{site_url}distributor/pancard/utiBalanceRequest">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/uti_card.png"
                                    class="dash_icon"></div>
                            <h4>UTI PAN Balance</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="https://www.psaonline.utiitsl.com/psaonline/showLogin">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/uti_pan_i.png"
                                    class="dash_icon"></div>
                            <h4>UTI PAN</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/deposit.png"
                                    class="dash_icon"></div>
                            <h4>Recurring Deposit</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/club.png" class="dash_icon">
                            </div>
                            <h4>Payol Club</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/mis_date.png"
                                    class="dash_icon"></div>
                            <h4>MIS</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/pl_icon.png"
                                    class="dash_icon"></div>
                            <h4>Personal Loan</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/business_loan.png"
                                    class="dash_icon"></div>
                            <h4>Business Loan</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/home_loan.png"
                                    class="dash_icon"></div>
                            <h4>Home Loan</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/mortgage_loan.png"
                                    class="dash_icon"></div>
                            <h4>Mortgage Loan</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/dr.png" class="dash_icon">
                            </div>
                            <h4>Doctor Appointment</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/school.png"
                                    class="dash_icon"></div>
                            <h4>School Fee</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/itr.png" class="dash_icon">
                            </div>
                            <h4>ITR Filling</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/gst_file.png"
                                    class="dash_icon"></div>
                            <h4>GST Billing</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/gst.png" class="dash_icon">
                            </div>
                            <h4>GST Registration</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/school_college.png"
                                    class="dash_icon"></div>
                            <h4>School & College Addmission</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/vechile_tax.png"
                                    class="dash_icon"></div>
                            <h4>Vehicle Tax</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/vehicle.png"
                                    class="dash_icon"></div>
                            <h4>Vehicle Insurance</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/msme.png" class="dash_icon">
                            </div>
                            <h4>MSME Registration</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/visa.png" class="dash_icon">
                            </div>
                            <h4>VISA Apply</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/dth.png" class="dash_icon">
                            </div>
                            <h4>DTH Sale</h4>
                        </a>
                    </div>

                    <div class="master_list_col">
                        <a href="#">
                            <div class="mlc_icon"><img src="{site_url}skin/admin/img/icons/cms_i.png" class="dash_icon">
                            </div>
                            <h4>CMS</h4>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>