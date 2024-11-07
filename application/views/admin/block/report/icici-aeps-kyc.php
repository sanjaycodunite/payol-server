<div class="card shadow mb-4">
    {system_message}
    {system_info}
    <div class="card-header py-3">
        <div class="row">
            <div class="col-sm-4">
                <h4><b>ICICI AEPS KYC Report</b></h4>
            </div>
        </div>
        <?php echo form_open('', array('id' => 'leadFilterForm')); ?>
        <div class="row">
            <div class="col-sm-2">
                <select class="form-control selectpicker" data-live-search="true" name="user" id="user">
                    <option value="">All User</option>
                    <?php
                      foreach($user as $list){
                      ?>
                    <option value="<?php echo $list['id']; ?>">
                        <?php echo $list['name']; ?>(<?php echo $list['user_code']; ?>)</option>
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

            <div class="col-sm-3">
                <button type="button" class="btn btn-success" id="iciciAepsKycSearchBtn">Search</button>
                <a href="{site_url}admin/report/iciciAepsKyc" class="btn btn-secondary">View All</a>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="iciciAepsKycDataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member Details</th>
                        <th>Mobile</th>
                        <th>Document</th>
                        <th>Address Detail</th>
                        <th>Document Photo</th>
                        <th>Status</th>
                        <th>Datetime</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Member Details</th>
                        <th>Mobile</th>
                        <th>Document</th>
                        <th>Address Detail</th>
                        <th>Document Photo</th>
                        <th>Status</th>
                        <th>Datetime</th>
                    </tr>
                </tfoot>
                <tbody>
                    <!-- Data rows will be populated here via JavaScript/AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>