<div class="container-fluid">
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-2 mt-2">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a
                                    href="{site_url}admin/report/recharge/2">Total Success</a></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessRechargeBlock">&#8377;
                                0.00 / 0</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-2 mt-2">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a
                                    href="{site_url}admin/report/recharge/1">Total Pending</a></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPendingRechargeBlock">&#8377;
                                0.00 / 0</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-2 mt-2">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a
                                    href="{site_url}admin/report/recharge/3">Total Failed</a></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFailedRechargeBlock">&#8377;
                                0.00 / 0</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        {system_message}
        {system_info}
        <div class="card-header py-3">
            <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
            <div class="row">
                <div class="col-sm-12">
                    <h4><b>Recharge Report</b></h4>
                </div>

                <div class="col-sm-2">
                    <select class="form-control" id="status">
                        <option value="0">All Status</option>
                        <option value="1" <?php if($status == 1){ ?> selected="selected" <?php } ?>>Pending</option>
                        <option value="2" <?php if($status == 2){ ?> selected="selected" <?php } ?>>Success</option>
                        <option value="3" <?php if($status == 3){ ?> selected="selected" <?php } ?>>Failed</option>
                    </select>
                </div>

                <div class="col-sm-2">
                    <select class="form-control" name="user_type" id="user_type">
                        <option value="">All User</option>
                        <?php
                    foreach($user_type as $list){
                    ?>
                        <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                        <?php } ?>
                    </select>
                </div>


                <div class="col-sm-2">
                    <select class="form-control selectpicker" data-live-search="true" name="operator" id="operator">
                        <option value="">All Operator</option>
                        <?php
                    foreach($operator as $list){
                    ?>
                        <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>


                <div class="col-sm-2">
                    <input type="text" class="form-control datepick" placeholder="Start Date" autocomplete="off"
                        name="from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-2">
                    <input type="text" class="form-control datepick" placeholder="End Date" autocomplete="off"
                        name="to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>



                <div class="col-sm-2">
                    <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>


                <div class="col-sm-12 mt-3 text-center">
                    <div class="form-group">
                        <button type="button" class="btn btn-success" id="rechargeSearchBtn">Search</button>
                        <a href="{site_url}admin/report/recharge" class="btn btn-secondary">View All</a>
                    </div>
                </div>

            </div>
            <?php echo form_close(); ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="rechargeDataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Txn ID</th>
                            <th>Operator</th>
                            <th>Recharge Type</th>
                            <th>Member</th>
                            <th>Mobile</th>
                            <th>API ID</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Operator ID</th>
                            <th>Date Time</th>
                            <th>Invoice</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>From</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Txn ID</th>
                            <th>Operator</th>
                            <th>Recharge Type</th>
                            <th>Member</th>
                            <th>Mobile</th>
                            <th>API ID</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Operator ID</th>
                            <th>Date Time</th>
                            <th>Invoice</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>From</th>
                        </tr>
                    </tfoot>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>