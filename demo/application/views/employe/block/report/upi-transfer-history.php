<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Upi Payout Report</b></h4>
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

                <div class="col-sm-2">
                <button type="button" class="btn btn-success" id="upiTransferHistorySearchBtn">Search</button>
                <a href="{site_url}employe/report/upiTransferHistory" class="btn btn-secondary">View All</a>
                
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="upiTransferHistoryDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Beneficiary</th>
                      <th>Account</th>
                      <th>Amount</th>
                      <th>Txn Type</th>
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
                      <th>Account</th>
                      <th>Amount</th>
                      <th>Txn Type</th>
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
    <?php echo form_open_multipart('admin/report/successUpiPayout',array('method'=>'post')); ?>
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
            <label>Bank RRN*</label>
            <input type="text" name="bank_rrn" class="form-control">
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

