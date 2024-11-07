<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('admin/master/saveReferralCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Refferal Commission</b></h4>
                </div>

                <div class="col-sm-4 text-right">
                <button type="submit" class="btn btn-primary">Save</button>
                </div>

              </div>
            </div>
            
            <div class="card-body">
              
              <div class="row">
                
                <div class="col-sm-2">
                  <div class="form-group">
                    <label><b>Service*</b></label>
                    <select class="form-control selectpicker" data-live-search="true" name="serviceID">
                      <option value="0">Select Service</option>
                      <option value="5">UPI Collection</option>
                      <option value="23">Payout</option>
                    </select>
                    <?php echo form_error('serviceID', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label><b>From Member*</b></label>
                    <select class="form-control selectpicker" data-live-search="true" name="fromMemberID">
                      <option value="0">Select Member</option>
                      <?php if($memberList){ ?>
                        <?php foreach($memberList as $list){ ?>
                          <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <?php echo form_error('fromMemberID', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label><b>To Member*</b></label>
                    <select class="form-control selectpicker" data-live-search="true" name="toMemberID">
                      <option value="0">Select Member</option>
                      <?php if($memberList){ ?>
                        <?php foreach($memberList as $list){ ?>
                          <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <?php echo form_error('toMemberID', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
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
                
                
                <div class="col-sm-1">
                  <div class="form-group">
                    <br />
                    <button type="submit" class="btn btn-success">Submit</button>
                    
                  </div>
                </div>
                <br />
              <div class="table-responsive" id="dmr-comm-block">
                {str}
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
    <?php echo form_open_multipart('admin/master/updateUpiQrCom',array('method'=>'post')); ?>
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
