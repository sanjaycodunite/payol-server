<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('employe/master/saveGatewayCharge', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Gateway Charge</b></h4>
                </div>

                <div class="col-sm-4 text-right">
                
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
                <button type="button" id="gatewayChargeSearchBtn" class="btn btn-success">Search</button>
                </div>
                <div class="col-sm-12 text-center recharge-comm-loader">
                </div>

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
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>MD</b></label>
                    <input type="text" class="form-control" name="md_commision" id="md_commision">
                    <?php echo form_error('md_commision', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>DT</b></label>
                    <input type="text" class="form-control" name="dt_commision" id="dt_commision">
                    <?php echo form_error('dt_commision', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>RT</b></label>
                    <input type="text" class="form-control" name="rt_commision" id="rt_commision">
                    <?php echo form_error('rt_commision', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <?php 
                if($accountData['is_disable_user_role'] != 1)
                {
                ?>
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>User</b></label>
                    <input type="text" class="form-control" name="user_commision" id="user_commision">
                    <?php echo form_error('user_commision', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <?php } ?>
                <?php 
                if($accountData['is_disable_api_role'] != 1)
                {
                ?>
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>API</b></label>
                    <input type="text" class="form-control" name="api_commision" id="api_commision">
                    <?php echo form_error('api_commision', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <?php } ?>
                <div class="col-sm-2">
                  <div class="form-group">
                    <br />
                    <input type="checkbox" name="is_flat" id="is_flatt" value="1">
                    <label for="is_flatt"><b>Is Flat</b></label>
                    
                  </div>
                </div>
                
                <div class="col-sm-1">
                  <div class="form-group">
                    <br />
                    <button type="submit" class="btn btn-success">Submit</button>
                    
                  </div>
                </div>
                
              </div>
              <br />
              <div class="table-responsive" id="dmr-comm-block">
                
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>
<div id="updateDMRModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('employe/master/updateGatewayCharge',array('method'=>'post')); ?>
    <input type="hidden" name="recordID" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">Update Gateway Charge</h4>
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
