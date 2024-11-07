<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('employe/master/saveUpiCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>UPI Collection Commission</b></h4>
                </div>

                <div class="col-sm-4 text-right">
                <button type="submit" class="btn btn-primary">Save</button>
                </div>

              </div>
            </div>
            
            <div class="card-body">
              <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-2 text-right">
                  <label><b>Select Package</b></label>
                </div>
                <div class="col-sm-3">
                  <select class="form-control selectpicker" data-live-search="true" name="memberID" id="selMemberID">
                    <option value="0">Select Package</option>
                    <?php if($packageList){ ?>
                      <?php foreach($packageList as $list){ ?>
                        <option value="<?php echo $list['id']; ?>"><?php echo $list['package_name']; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-2">
                <button type="button" id="upiComSearchBtn" class="btn btn-success">Search</button>
                </div>
                <div class="col-sm-12 text-center recharge-comm-loader">
                </div>

              </div>
              <br />
              <div class="table-responsive" id="recharge-comm-block">
                
              </div>

              <div class="row">
                <div class="col-sm-12"><hr /></div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label><b>Start Range*</b></label>
                    <input type="text" class="form-control" name="startRange" id="startRange" placeholder="Start Range">
                    <?php echo form_error('startRange', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label><b>End Range*</b></label>
                    <input type="text" class="form-control" name="endRange" id="endRange" placeholder="End Range">
                    <?php echo form_error('endRange', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label><b>Commission*</b></label>
                    <input type="text" class="form-control" name="commision" id="commision" placeholder="Commision">
                    <?php echo form_error('commision', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <br />
                    <input type="checkbox" name="is_flat" id="is_flatt" value="1">
                    <label for="is_flatt"><b>Is Flat</b></label>
                    <br />
                    <input type="checkbox" name="is_surcharge" id="is_surcharge" value="1">
                    <label for="is_surcharge"><b>Is Surcharge</b></label>
                    
                  </div>
                </div>
                
                <div class="col-sm-3">
                  <div class="form-group">
                    <label><b>User Type</b></label>
                    <br />
                    <div style="margin-top: 10px;">
                      <input type="checkbox" name="is_md" id="is_md" value="1">
                      <label for="is_md"><b>MD</b></label> &nbsp;&nbsp;&nbsp;
                      <input type="checkbox" name="is_dt" id="is_dt" value="1">
                      <label for="is_dt"><b>DT</b></label> &nbsp;&nbsp;&nbsp;
                      <input type="checkbox" name="is_f" id="is_f" value="1">
                      <label for="is_rt"><b>RT</b></label> &nbsp;&nbsp;&nbsp;
                      <input type="checkbox" name="is_api" id="is_api" value="1">
                      <label for="is_api"><b>API</b></label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="form-group">
                    <br />
                    <button type="submit" class="btn btn-success">Submit</button>
                    
                  </div>
                </div>
                <br />
              <div class="table-responsive" id="dmr-comm-block">
                
              </div>
                
              </div>


            </div>
            
            <?php echo form_close(); ?>
          </div>
        </div>
<div id="updateDMRModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('employe/master/updateUpiQrCom',array('method'=>'post')); ?>
    <input type="hidden" name="recordID" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">Update Commission</h4>
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
