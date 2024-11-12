<div class="row">
    <div class="col-sm-12">
        {system_message}
        {system_info}
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h3><b>Money Transfer Beneficiary</b></h3>
            </div>
            <div class="container">
                <div class="ajaxx-loader"></div>
                <div class="alert alert-success alert-dismissible fade hide" role="alert" id="benAlert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <form id="upi_verify_form" method="post">
                <input type="hidden" value="<?php echo $site_url; ?>" id="siteUrl">
                <input type="hidden" id="dbTableName" name="dbTableName"
                    value="instantpay_upi_open_payout_user_benificary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label><b>Account Holder Name*</b></label>
                                <input type="text" class="form-control" name="account_holder_name"
                                    id="account_holder_name" placeholder="Holder Name" value="">
                                <div class="error" id="account_holder_name_error"></div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label><b>UPI ID*</b></label>
                                <input type="text" class="form-control" name="ben_upi_id_account_number"
                                    id="ben_upi_id_account_number" placeholder="UPI ID" value="">
                                <div class="error" id="ben_upi_id_account_number_error"></div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label><b>Mobile No*</b></label>
                                <input type="text" class="form-control" name="mobile_no" id="mobile_no"
                                    placeholder="Mobile No" value="">
                                <div class="error" id="mobile_no_error"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer justify-content-between">
                    <button type="button" class="btn btn-warning btn-sm" id="accountUpiVerifyBtn"
                        class="btn btn-success">Verify &
                        Add</button>
                    <button type="button" class="btn btn-primary btn-sm" id="saveMT1UpiBeneficiaryBtn">Save New
                        Beneficiary</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h3><b>Beneficiary List</b></h3>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Beneficiary Name</th>
                                <th>Account No.</th>
                                <th>Mobile No</th>
                                <th>Added On</th>
                                <th width="5%">Fund</th>
                                <th>Action</th>
                        </thead>
                        <tbody>
                            <?php if ($benificaryList) {
                                $i = 1;
                                foreach ($benificaryList as $list) { ?>
                            <tr>
                                <td class="align-middle"><?php echo $i; ?></td>
                                <td class="align-middle"><?php echo $list['account_holder_name']; ?></td>
                                <td class="align-middle"><?php echo $list['account_no']; ?></td>
                                <td class="align-middle"><?php echo $list['mobile']; ?></td>
                                <td class="align-middle">
                                    <?php echo date('d-m-Y <b>h:i A<b/>', strtotime($list['created'])); ?>
                                </td>
                                <td class="align-middle">
                                    <a
                                        href="<?php echo site_url('retailer/transfer/upiOpenPayoutFundTransfer/' . $list['id']); ?>">
                                        <button class="btn btn-primary btn-sm" type="button">Transfer</button>
                                    </a>
                                </td>
                                <td class="align-middle">
                                    <a title="edit" class="btn btn-primary btn-sm" href="#"
                                        onclick="updateUpiBenModel1(<?php echo $list['id']; ?>); return false;">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm benm1UpiDeletebtn" data-toggle="modal"
                                        data-target="#confirmUpiModal" benm1UpiDeleteID="<?= $list['id'] ?>">
                                        <i class=" fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php $i++; }
                            } else { ?>
                            <tr>
                                <td colspan="5" class="align-middle text-center">No Record Found</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Beneficiary Name</th>
                                <th>Account No.</th>
                                <th>Mobile No</th>
                                <th>Added On</th>
                                <th width="5%">Fund</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Confirmation Modal -->
<div class="modal fade" id="confirmUpiModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUpiModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-secondary" id="confirmUpiDeletem1">Yes</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Bank Verification -->
<div class="modal fade" id="bankUpiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Bank Verification</h5>
                <button type="button" class="close btnDisabled" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bankUpiResponse">
                <input type="hidden" value="<?php echo $site_url; ?>" id="siteUrl">
                <input type="hidden" id="dbTableName" name="dbTableName" value="user_benificary">
                <center><span class="benAddonMsg"></span></center>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-6 text-left">
                        <button type="button" class="btn btn-danger btn-sm btnDisabled"
                            data-dismiss="modal">Cancel</button>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-success btn-sm" id="saveMT1UpiBeneficiaryBtn">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display: none;"></span>
                            Add Beneficiary
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Update Beneficiary -->
<div id="updateupiBenModel1" class="modal fade" role="dialog">
    <div class="modal-dialog assign-modal">
        <div class="modal-content">
            <form id="updateBenM1UpiData" method="post">
                <input type="hidden" name="recordID" id="recordID" value="0">
                <div class="modal-header">
                    <h4 class="modal-title">Update Beneficiary</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success alert-dismissible fade hide" role="alert" id="updateBenUpiAlert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="updatebenUpiBlock1"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBenM1UpiChanges">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>