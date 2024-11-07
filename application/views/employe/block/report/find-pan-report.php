<div class="card shadow mb-4">
    {system_message}
    {system_info}
    <div class="card-header py-3">
        <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
        <div class="row">
            <div class="col-sm-3">
                <h4><b>Find PAN Report</b></h4>
            </div>

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
                <button type="button" class="btn btn-success" id="findPanSearchBtn">Search</button>
                <a href="{site_url}employe/report/findPanReport" class="btn btn-secondary">View All</a>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="findPanDataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Txnid</th>
                        <th>Name</th>
                        <th>DOB</th>
                        <th>Aadhar Number</th>
                        <th>Image</th>
                        <th>Action</th>
                        <th>Datetime</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Txnid</th>
                        <th>Name</th>
                        <th>DOB</th>
                        <th>Aadhar Number</th>
                        <th>Image</th>
                        <th>Action</th>
                        <th>Datetime</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>



<div class="modal fade" id="findPanModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalTitle"
    aria-hidden="true">
    <?php echo form_open_multipart('employe/report/updateFindPanImage',array('method'=>'post','id'=>'updateStatusForm')); ?>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalTitle">Upload PAN Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="modalform">
                    <input type="hidden" value="0" name="aadharID" id="aadharID" />
                    <div class="form-group">
                        <label><b>Pan Image</b></label>
                        <input type="file" name="pan_photo" class="form-control" required>
                    </div>




                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>