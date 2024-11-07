<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-4">
                <h4><b>MATM Transaction Report</b></h4>
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
                <button type="button" class="btn btn-success" id="matmHistorySearchBtn">Search</button>
                <a href="{site_url}superadmin/report/matmHistory" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="matmHistoryDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>Member</th>
                      <th>Amount</th>
                      <th>TxnID</th>
                      <th>TxnType</th>
                      <th>BankRRN</th>
                      <th>MPOS</th>
                      <th>Card</th>
                      <th width="50">Status</th>
                      <th>Datetime</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>Member</th>
                      <th>Amount</th>
                      <th>TxnID</th>
                      <th>TxnType</th>
                      <th>BankRRN</th>
                      <th>MPOS</th>
                      <th>Card</th>
                      <th width="50">Status</th>
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


        <div id="updateDMRModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('admin/master/updateDMRCom',array('method'=>'post')); ?>
    <input type="hidden" name="recordID" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">Transaction Detail</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    
    
    </div>
    <div class="modal-body">
    
    <div class="modalform">
      <input type="hidden" value="0" name="taskID" id="taskID" />
      <div class="row">
        <div class="col-md-12" id="updateDMRBlock">

        </div>
      </div>
    </div>
    
    </div>
    <?php echo form_close(); ?>
    
  </div>

  </div>
</div>      

