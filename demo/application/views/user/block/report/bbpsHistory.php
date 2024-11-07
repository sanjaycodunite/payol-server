<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
               <div class="row">
                <div class="col-sm-12">
                <h4><b>BBPS Report</b></h4>
                </div>
                
                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="From Date" autocomplete="off" name="from_date" id="from_date" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="To Date" autocomplete="off" name="to_date" id="to_date" />
                </div>

                <div class="col-sm-2">
                <select class="form-control" id="status">
                  <option value="0">All</option>
                  <option value="1" <?php if($status == 1){ ?> selected="selected" <?php } ?>>Pending</option>
                  <option value="2" <?php if($status == 2){ ?> selected="selected" <?php } ?>>Success</option>
                  <option value="3" <?php if($status == 3){ ?> selected="selected" <?php } ?>>Failed</option>
                </select>
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="bbpsHistorySearchBtn">Search</button>
                <a href="{site_url}user/report/bbpsHistory" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="bbpsHistoryDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>RechargeID</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Service</th>
                      <th>Operator</th>
                      <th>Number</th>
                      <th>Amount</th>
                      <th>OB</th>
                      <th>CB</th>
                      <th>Date Time</th>
                      <th>Status</th>
                      <th>Invoice</th>
                      <th>Complain</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>RechargeID</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Service</th>
                      <th>Operator</th>
                      <th>Number</th>
                      <th>Amount</th>
                      <th>OB</th>
                      <th>CB</th>
                      <th>Date Time</th>
                      <th>Status</th>
                      <th>Invoice</th>
                      <th>Complain</th>
                      
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
    <?php echo form_open_multipart('user/report/complainAuth',array('method'=>'post')); ?>
    <input type="hidden" name="recordID" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">Submit Complain</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    
    
    </div>
    <div class="modal-body">
    
    <div class="modalform">
      <input type="hidden" value="0" name="taskID" id="taskID" />
      <div class="row">
        <div class="col-md-12">
          <div class="form-group" id="complainRchgID">
            <p><b>Txn ID - </b></p>
          </div>
          <div class="form-group" id="complainAmount">
            <p><b>Amount - </b></p>
          </div>
          <div class="form-group">
            <label>Description*</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
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

