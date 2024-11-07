<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('employe/master/saveMoneyTransferCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Open Payout Charge</b></h4>
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
                <button type="button" id="moneyTransferComSearchBtn" class="btn btn-success">Search</button>
                </div>
                <div class="col-sm-12 text-center recharge-comm-loader">
                </div>

              </div>
              <div class="row">
                <div class="col-sm-12"><hr /></div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <br />
                    <label><b>Start Range*</b></label>
                    <input type="text" class="form-control" name="startRange" id="startRange" placeholder="Start Range">
                    <?php echo form_error('startRange', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <br />
                    <label><b>End Range*</b></label>
                    <input type="text" class="form-control" name="endRange" id="endRange" placeholder="End Range">
                    <?php echo form_error('endRange', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>MD Charge*</b></label>
                    <input type="text" class="form-control" name="md_charge" id="md_charge">
                    <?php echo form_error('md_charge', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>DT Charge*</b></label>
                    <input type="text" class="form-control" name="dt_charge" id="dt_charge">
                    <?php echo form_error('dt_charge', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>RT Charge*</b></label>
                    <input type="text" class="form-control" name="rt_charge" id="rt_charge">
                    <?php echo form_error('rt_charge', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <?php 
                if($accountData['is_disable_user_role'] != 1)
                {
                ?>
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>User Charge*</b></label>
                    <input type="text" class="form-control" name="user_charge" id="user_charge">
                    <?php echo form_error('user_charge', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <?php } ?>
                <?php 
                if($accountData['is_disable_api_role'] != 1)
                {
                ?>
                <div class="col-sm-1">
                  <div class="form-group">
                    <label><b>API Charge*</b></label>
                    <input type="text" class="form-control" name="api_charge" id="api_charge">
                    <?php echo form_error('api_charge', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <?php } ?>
                <div class="col-sm-1">
                  <div class="form-group">
                    <br /><br /><br />
                    <input type="checkbox" name="is_flat" id="is_flatt" value="1">
                    <label for="is_flatt"><b>Is Flat</b></label>
                    
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="form-group">
                    <br /><br />
                    <button type="submit" class="btn btn-success">Submit</button>
                    
                  </div>
                </div>
                <div class="col-sm-1"></div>
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
    <?php echo form_open_multipart('employe/master/updateMoneyTransferCom',array('method'=>'post')); ?>
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
