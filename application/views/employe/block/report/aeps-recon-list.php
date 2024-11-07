<div class="container-fluid">
<div class="row">
<div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}employe/report/fingpayAepsHistory/2">Total Success</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessAepsBlock">&#8377; 0.00 / 0</div>
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
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="{site_url}employe/report/fingpayAepsHistory/3">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFailedAepsBlock">&#8377; 0.00 / 0</div>
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
                <h4><b>AEPS 3 Transaction Report</b></h4>
                </div>
               
                 <div class="col-sm-2">
                <select class="form-control" id="status">
                  <option value="0">All Status</option>
                  
                  <option value="2" <?php if($status == 2){ ?> selected="selected" <?php } ?>>Success</option>
                  <option value="3" <?php if($status == 3){ ?> selected="selected" <?php } ?>>Failed</option>
                </select>
                </div>

                 <div class="col-sm-2">
                <select class="form-control" id="service">
                  <option value="">All Service</option>
                  
                  <option value="balinfo" <?php if($service == 'balinfo'){ ?> selected="selected" <?php } ?>>Balance Enquiry</option>
                  <option value="ministatement" <?php if($service == 'ministatement'){ ?> selected="selected" <?php } ?>>Mini Statment</option>
                  <option value="balwithdraw" <?php if($service == 'balwithdraw'){ ?> selected="selected" <?php } ?>>Cash Withdrawal</option>
                  <option value="aadharpay" <?php if($service == 'aadharpay'){ ?> selected="selected" <?php } ?>>Aadhar Pay</option>
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
                <button type="button" class="btn btn-success" id="aepsReconSearchBtn">Search</button>
                <a href="{site_url}employe/report/fingpayRecon" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="aepsReconDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Member</th>
                      <th>Service</th>
                      <th>Aadhar No.</th>
                      <th>Mobile</th>
                      <th>Amount</th>
                      <th>TxnID</th>
                      <th>API Message</th>
                      <th>Bank Name</th>
                      <th width="50">Status</th>
                      <th>Action</th>
                      <th>Recon Status</th>
                      <th>Recon Api Response</th>
                      <th>Invoice</th>
                      <th>Datetime</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Member</th>
                      <th>Service</th>
                      <th>Aadhar No.</th>
                      <th>Mobile</th>
                      <th>Amount</th>
                      <th>TxnID</th>
                      <th>API Message</th>
                       <th>Bank Name</th>
                      <th width="50">Status</th>
                      <th>Action</th>
                       <th>Recon Status</th>
                      <th>Recon Api Response</th>
                      <th>Invoice</th>
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

</div>