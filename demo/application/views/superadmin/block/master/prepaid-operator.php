<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('superadmin/master/savePrepaidOperator', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Active/Deactive Prepaid Operator</b></h4>
                </div>

                <div class="col-sm-4 text-right">
                <button type="submit" class="btn btn-primary">Save</button>
                </div>

              </div>
            </div>
            
            <div class="card-body">
              
              <div class="table-responsive" id="recharge-comm-block">
                <table class="table table-bordered table-striped"  width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Operator</th>
                      <th>Type</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if($operatorList){ 
                      $i=1;
                      foreach($operatorList as $key=>$list){
                        ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $list['operator_name']; ?></td>
                          <td><?php echo $list['type']; ?></td>
                          <td>
                            <select class="form-control" name="biller_id[<?php echo $list['id']; ?>]">
                              <?php if($list['status'] == 1){ ?>
                                <option value="1" selected="selected">Active</option>
                                <option value="0">Deactive</option>
                              <?php } else { ?>
                                <option value="1">Active</option>
                                <option value="0" selected="selected">Deactive</option>
                              <?php } ?>
                            </select>
                          </td>
                        </tr>
                        <?php
                        $i++;
                      }
                    } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Operator</th>
                      <th>Type</th>
                      <th>Status</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

