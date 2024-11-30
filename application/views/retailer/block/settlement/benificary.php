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
                <h3><b>Money Transfer - Beneficiary</b></h3>
            </div>
            <div class="container">
                <br />
                <div class="row">
                    <!-- Loading Spinner -->
                    <div id="wait-loader" style="display:none;">
                        <img src="{site_url}skin/admin/images/loading-wait.gif" alt="Loading...">
                    </div>

                    <!-- Alert Message -->
                    <div class="alert alert-success alert-dismissible fade hide" role="alert" id="benAlert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Beneficiary Form -->
                    <form id="account_verify_form" method="post">
                        <input type="hidden" value="<?php echo $site_url; ?>" id="siteUrl">
                        <input type="hidden" id="dbTableName" name="dbTableName" value="settlement_user_benificary">
                        <div class="card-body">
                            <div class="row">
                                <!-- Bank Name -->
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label><b>Bank Name*</b></label>
                                        <select name="bankID" id="bankID" class="form-control bank">
                                            <option value="">Select Bank</option>
                                            <?php foreach ($bankList as $bank): ?>
                                            <option value="<?php echo html_escape($bank['bank_id']); ?>"
                                                <?php echo set_select('bank_name', html_escape($bank['bank_id'])); ?>
                                                data-global-ifsc="<?php echo html_escape($bank['ifsc_global']); ?>">
                                                <?php echo html_escape($bank['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="error" id="bankID_error"></div>
                                    </div>
                                </div>

                                <!-- IFSC Code -->
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label><b>Global/IFSC Code*</b></label>
                                        <input type="text" class="form-control" name="ifsc" id="ifsc"
                                            placeholder="IFSC Code" value="">
                                        <div class="error" id="ifsc_error"></div>
                                    </div>
                                </div>

                                <!-- Account Number -->
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label><b>Account No.*</b></label>
                                        <input type="text" class="form-control" name="ben_account_number"
                                            id="ben_account_number" placeholder="Account No." value="">
                                        <div class="error" id="ben_account_number_error"></div>
                                    </div>
                                </div>

                                <!-- Account Holder Name -->
                                <div class="col-sm-12 col-md-8">
                                    <div class="form-group">
                                        <label><b>Account Holder Name*</b></label>
                                        <input type="text" class="form-control" name="account_holder_name"
                                            id="account_holder_name" placeholder="Holder Name" value="">
                                        <div class="error" id="account_holder_name_error"></div>
                                    </div>
                                </div>

                                <!-- Mobile Number -->
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label><b>Mobile No*</b></label>
                                        <input type="text" class="form-control" name="mobile_no" id="mobile_no"
                                            placeholder="Mobile No" value="">
                                        <div class="error" id="mobile_no_error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="card-footer">
                            <button type="button" id="accountVerifyBtn" class="btn btn-success">Verify & Add</button>
                            <button type="button" class="btn btn-primary" id="saveBeneficiaryBtn">Save New
                                Beneficiary</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Beneficiary List Table -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Beneficiary Name</th>
                                <th>Mobile No</th>
                                <th>Account No.</th>
                                <th>Bank</th>
                                <th>Global/IFSC Code</th>
                                <th>Added On</th>
                                <th>Fund</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($benificaryList) {
                                $i = 1;
                                foreach ($benificaryList as $list): ?>
                            <tr>
                                <td class="align-middle"><?php echo $i; ?></td>
                                <td class="align-middle"><?php echo $list['account_holder_name']; ?></td>
                                <td class="align-middle"><?php echo $list['mobile']; ?></td>
                                <td class="align-middle"><?php echo $list['account_no']; ?></td>
                                <td class="align-middle"><?php echo $list['bank_name']; ?></td>
                                <td class="align-middle"><?php echo $list['ifsc']; ?></td>
                                <td class="align-middle">
                                    <?php echo date('d-m-Y <b>h:i A</b>', strtotime($list['created'])); ?>
                                </td>
                                <td class="align-middle">
                                    <a
                                        href="<?php echo site_url('retailer/settlement/fundTransfer/' . $list['ben_id']); ?>">
                                        <button class="btn btn-primary btn-sm" type="button">Transfer</button>
                                    </a>
                                </td>
                                <td class="align-middle">
                                    <a title="edit" class="btn btn-primary btn-sm" href="#"
                                        onclick="updateBenModel2(<?php echo $list['id']; ?>); return false;">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm benm2Deletebtn" data-toggle="modal"
                                        data-target="#confirmModal" benm2DeleteID="<?php echo $list['id']; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php $i++; endforeach;
                            } else { ?>
                            <tr>
                                <td colspan="9" class="align-middle text-center">No Record Found</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Beneficiary Name</th>
                                <th>Mobile No</th>
                                <th>Account No.</th>
                                <th>Bank</th>
                                <th>IFSC</th>
                                <th>Added On</th>
                                <th>Fund</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Bank Verification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bankResponse">
                <center><span class="benAddonMsg"></span></center>
                <!-- Bank verification details -->
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-sm-6 text-left">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-success btn-sm" id="addBeneficiaryBtn">
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

<!-- Bootstrap Modal for Update Beneficiary -->
<div id="updateBenModel2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateBenModelLabel"
    aria-hidden="true">
    <div class="modal-dialog assign-modal" role="document">
        <div class="modal-content">
            <form id="updateBenM2BankData" method="post">
                <input type="hidden" name="recordID" id="recordID" value="0">
                <div class="modal-header">
                    <h4 class="modal-title" id="updateBenModelLabel">Update Beneficiary</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success alert-dismissible fade hide" role="alert" id="updateBenAlert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="updatebenBlock2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBenM2Changes">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>