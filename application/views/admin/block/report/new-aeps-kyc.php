<div class="card shadow mb-4">
  <!-- {system_message} and {system_info} should be rendered by the server-side code -->
  <div class="card-header py-3">
  <div class="row">
  <div class="col-sm-4">
        <h4><b>NSDL AEPS KYC Report</b></h4>
      </div>
  </div>
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
        <?php echo form_open('', array('id' => 'leadFilterForm')); ?>
        <input type="text" class="form-control datepick" placeholder="Date" autocomplete="off" name="date" id="date" />
      </div>

      <div class="col-sm-2">
        <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
      </div>

      <div class="col-sm-4">
        <button type="button" class="btn btn-success" id="newAepsKycSearchBtn">Search</button>
        <a href="<?php echo site_url('admin/report/newAepsKyc'); ?>" class="btn btn-secondary">View All</a>
      </div>
    </div>
    <?php echo form_close(); ?> 
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="newAepsKycDataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>#</th>
            <th>Member</th>
            <th>Mobile</th>
            <th>Shop</th>
            <th>Address</th>
            <th>Document</th>
            <th>Attachment</th>
            <th>Status</th>
            <th>Datetime</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>#</th>
            <th>Member</th>
            <th>Mobile</th>
            <th>Shop</th>
            <th>Address</th>
            <th>Document</th>
            <th>Attachment</th>
            <th>Status</th>
            <th>Datetime</th>
          </tr>
        </tfoot>
        <tbody>
          <!-- Data will be inserted here dynamically -->
        </tbody>
      </table>
    </div>
  </div>
</div>
