<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('superadmin/master/saveAEPSCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>AEPS Commission</b></h4>
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
                <button type="button" id="aepsComSearchBtn" class="btn btn-success">Search</button>
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
                <div class="col-sm-2">
                  <div class="form-group">
                    <label><b>Commission*</b></label>
                    <input type="text" class="form-control" name="commision" id="commision" placeholder="Commission">
                    <?php echo form_error('commision', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label><b>Commission Type*</b></label>
                    <select class="form-control" name="com_type">
                        <option value="">Select</option>
                        <option value="1">Account Withdrawal</option>
                        <option value="2">Mini Statement</option>
                        <option value="3">Aadhar Pay</option>
                        <option value="4">Cash Deposite</option>
                        <option value="5">MATM</option>
                    </select>
                    <?php echo form_error('com_type', '<div class="error">', '</div>'); ?>  
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
                <div class="col-sm-2">
                  <div class="form-group">
                    <br />
                    <button type="submit" class="btn btn-success">Submit</button>
                    
                  </div>
                </div>
                
              </div>
              <br />
              <div class="table-responsive" id="dmr-comm-block">
                <table class="table table-bordered table-striped"  width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Start Range</th>
                      <th>End Range</th>
                      <th>Commision</th>
                      <th>Type</th>
                      <th>Is Flat ?</th>
                      <th>Is Surcharge ?</th>
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
                    <td><?php echo $list['commission']; ?></td>
                    <td>
                      <?php if($list['com_type'] == 1){ ?>
                      Account Withdrawal
                      <?php } elseif($list['com_type'] == 2){ ?>
                        Mini Statement
                        <?php } elseif($list['com_type'] == 3){ ?>
                        Aadhar Pay
                        <?php } elseif($list['com_type'] == 4){ ?>
                        Cash Deposite
                        <?php } elseif($list['com_type'] == 5){ ?>
                        MATM
                      <?php } ?>
                    </td> 
                    <td><?php echo ($list['is_flat']) ? '<font color="green">Yes</font>' : '<font color="red">No</font>'; ?></td> 
                    <td><?php echo ($list['is_surcharge']) ? '<font color="green">Yes</font>' : '<font color="red">No</font>'; ?></td> 
                    <td><a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateaepsModel(<?php echo $list['id']; ?>); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="{site_url}superadmin/master/deleteAEPSCom/<?php echo $list['id']; ?>" onclick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                   </tr>
                   <?php $i++;}} else { ?> 
                    <tr>
                      <td colspan="7" align="center">No Record Found.</td>
                    </tr>
                    <?php } ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Start Range</th>
                      <th>End Range</th>
                      <th>Commision</th>
                      <th>Type</th>
                      <th>Is Flat ?</th>
                      <th>Is Surcharge ?</th>
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
    <?php echo form_open_multipart('superadmin/master/updateAEPSCom',array('method'=>'post')); ?>
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
