<div class="container-fluid">
<div class="row">
<div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Success</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessIciciBlock">&#8377; 0.00 / 0</div>
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
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Pending</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPendingIciciBlock">&#8377; 0.00 / 0</div>
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
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFailedIciciBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
</div>

 <div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Success Charge</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessChargeBlock">&#8377; 0.00</div>
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
             <?php echo form_open('admin/report/downloadPayoutExcel',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-12">
                <h4><b>ICICI PAYOUT Report</b></h4>
                </div>


                <div class="col-sm-2">
                <select class="form-control" id="status" name="status">
                  <option value="0">All Status</option>
                  
                  <option value="2" <?php if($status == 2){ ?> selected="selected" <?php } ?>>Pending</option>
                  <option value="3" <?php if($status == 3){ ?> selected="selected" <?php } ?>>Success</option>
                  <option value="4" <?php if($status == 4){ ?> selected="selected" <?php } ?>>Failed</option>
                </select>
                </div>

                <div class="col-sm-2">
                  <select class="form-control selectpicker" data-live-search="true" name="user" id="user">
                    <option value="">All User</option>
                    <?php
                    foreach($user as $list){
                    ?>
                    <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?>(<?php echo $list['user_code']; ?>)</option>
                  <?php } ?>
                  </select>
                </div>


                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="Start Date" autocomplete="off" name="from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="End Date" autocomplete="off" name="to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-12 mt-3 text-center">
                <button type="button" class="btn btn-success" id="newMoneyTransferHistoryOldSearchBtn">Search</button>
                <a href="{site_url}admin/report/newMoneyTransferHistoryOld" class="btn btn-secondary">View All</a>
                 <button type="submit" class="btn btn-success" style="background-color: green !important;border-color: green !important;" id="newMoneyTransferHistoryOldSearchBtn">Export</button>
                
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="newMoneyTransferHistoryOldDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Beneficiary</th>
                      <th>Txn Amount</th>
                      <th>Charge</th>
                      <th>Txn ID</th>
                      <th>RRN</th>
                      <th>Status</th>
                      <th>Invoice</th>
                      <th>Date Time</th>
                      <th>Action</th>
                     
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Beneficiary</th>
                      <th>Txn Amount</th>
                      <th>Charge</th>
                      <th>Txn ID</th>
                      <th>RRN</th>
                      <th>Status</th>
                      <th>Invoice</th>
                      <th>Date Time</th>
                      <th>Action</th>
                      
                      
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

<div id="updateComplainModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('admin/report/successNewPayout',array('method'=>'post')); ?>
    <input type="hidden" name="recordID" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">Success Record</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    
    
    </div>
    <div class="modal-body">
    
    <div class="modalform">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group" id="complainRchgID">
            <p><b>Txn ID - </b></p>
          </div>
          <div class="form-group" id="complainAmount">
            <p><b>Amount - </b></p>
          </div>

           <div class="form-group">
            <label>IPAY OrderID*</label>
            <input type="text" name="optxid" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Bank RRN*</label>
            <input type="text" name="bank_rrn" class="form-control" required>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
        <div class="col-md-12" id="complainMsgBlock"></div>
      </div>
    </div>
    
    </div>
    <?php echo form_close(); ?>
    
  </div>

  </div>
</div>             

</div>
