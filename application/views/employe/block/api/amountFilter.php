<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('employe/api/amountFilterAuth', array('id' => 'employe_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Amount Filter</b></h4>
                </div>


              </div>
            </div>
            
            <div class="card-body">
             
              <div class="row">
                <div class="col-sm-12"><hr /></div>
                <div class="col-sm-1"></div>
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
                    <label><b>Operator*</b></label>
                    <select class="form-control" name="op_id">
                      <option value="">Select Operator</option>
                      <?php if($operatorList){ ?>
                        <?php foreach($operatorList as $list){ ?>
                          <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <?php echo form_error('op_id', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label><b>Active API*</b></label>
                    <select class="form-control" name="api_id">
                      <option value="">Select API</option>
                      <?php if($apiList){ ?>
                        <?php foreach($apiList as $list){ ?>
                          <option value="<?php echo $list['id']; ?>"><?php echo $list['provider']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                    <?php echo form_error('api_id', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <button type="submit" class="btn btn-success mt-4">Submit</button>
                    
                  </div>
                </div>
                <div class="col-sm-1"></div>
              </div>
              <br />
              <div class="table-responsive" id="dmr-comm-block">
                <table class="table table-bordered table-striped"  width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Start Range</th>
                      <th>End Range</th>
                      <th>Operator</th>
                      <th>Active API</th>
                      <th>Action</th>
                      </tr>
                  </thead>

                  <tbody>
                    <?php
                    if($recordList){
                      $i=1;
                      foreach($recordList as $key=>$list){
                   ?> 
                   <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $list['start_range']; ?></td>
                    <td><?php echo $list['end_range']; ?></td>
                    <td><?php echo $list['operator_name']; ?></td>
                    <td><?php echo $list['provider']; ?></td>
                    <td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateAmountFilterModel(<?php echo $list['id']; ?>); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="{site_url}employe/api/deleteAmountFilter/<?php echo $list['id']; ?>" onclick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                   </tr>
                   <?php $i++;}} else { ?> 
                    <tr>
                      <td colspan="6" align="center">No Record Found.</td>
                    </tr>
                    <?php } ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Start Range</th>
                      <th>End Range</th>
                      <th>Operator</th>
                      <th>Active API</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>
<div id="updateDMRModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('employe/api/updateAmountFilter',array('method'=>'post')); ?>
    <input type="hidden" name="recordID" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">Update Amount Filter</h4>
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
