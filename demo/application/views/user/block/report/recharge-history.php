<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Recharge Report</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control datepick" placeholder="Date" autocomplete="off" name="date" id="date" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="rechargeSearchBtn">Search</button>
                <a href="{site_url}user/report/recharge" class="btn btn-secondary">View All</a>
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
                      <th>RechargeID</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Operator</th>
                      <th>Amount</th>
                      <th>OB</th>
                      <th>CB</th>
                      <th>Date Time</th>
                      <th>Status</th>
                      <th>Complain</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>RechargeID</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Operator</th>
                      <th>Amount</th>
                      <th>OB</th>
                      <th>CB</th>
                      <th>Date Time</th>
                      <th>Status</th>
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
    <?php echo form_open_multipart('user/recharge/complainAuth',array('method'=>'post')); ?>
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
            <p><b>Recharge ID - </b></p>
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
