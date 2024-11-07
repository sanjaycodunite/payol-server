<div class="container-fluid">
<div class="row">
<div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Credit Fund</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCrBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>


               
                <div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Debit Fund</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalDrBlock">&#8377; 0.00 / 0</div>
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
              <?php echo form_open('portal/wallet/downloadExcel',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-12">
                <h4><b>Old Wallet Report</b></h4>
                </div>


                <div class="col-sm-2">
                <select class="form-control" id="status" name="status">
                  <option value="0">All Status</option>
                  
                  <option value="1" <?php if(isset($status) && $status == 1){ ?> selected="selected" <?php } ?>>CR</option>
                  <option value="2" <?php if(isset($status) && $status == 2){ ?> selected="selected" <?php } ?>>DR</option>
                </select>
                </div>


                <div class="col-sm-2">
                <select class="form-control" id="by" name="by">
                  <option value="0">By</option>
                  
                  <option value="1" <?php if($status == 1){ ?> selected="selected" <?php } ?>> By Manual</option>
                  <option value="2" <?php if($status == 2){ ?> selected="selected" <?php } ?>>By Payout</option>
                </select>
                </div>
                

                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="Start Date" autocomplete="off" name="from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="End Date" autocomplete="off" name="to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-2">
                <button type="button" class="btn btn-success" id="oldWalletSearchBtn">Search</button>
                <button type="submit" class="btn btn-success" style="background-color: green !important;border-color: green !important;" id="walletSearchBtn">Export</button>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="oldWalletDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Before Amount</th>
                      <th>Cr/Dr Amount</th>
                      <th>After Amount</th>
                      <th>Date Time</th>
                      <th>Type</th>
                      <th>Description</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Before Amount</th>
                      <th>Cr/Dr Amount</th>
                      <th>After Amount</th>
                      <th>Date Time</th>
                      <th>Type</th>
                      <th>Description</th>
                      <th>Status</th>
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
    
    
    
    
    
<div id="updateComplainModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('portal/wallet/updateNarration',array('method'=>'post')); ?>
    <input type="hidden" name="recordID" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">Narration</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    
    
    </div>
    <div class="modal-body">
    
    <div class="modalform">
      <div class="row">
        <div class="col-md-12">
          
          <div class="form-group" id="complainAmount">
            <p><b>Narration - </b></p>
          </div>
          <div class="form-group">
            <label>Update Narration*</label>
            <input type="text" name="narration" class="form-control">
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
