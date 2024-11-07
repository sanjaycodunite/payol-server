<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('superadmin/master/autoSettlementTimeAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Auto Settlement Timezone</b></h4>
                </div>

                <div class="col-sm-4 text-right">
                <button type="submit" class="btn btn-primary">Save</button>
                </div>

              </div>
            </div>
            
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Hour</th>
                      <th>Min</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for($i = 1; $i <= 15; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td>
                            <input type="text" class="form-control" value="<?php echo isset($recordList[$i]['hour']) ? $recordList[$i]['hour'] : ''; ?>" name="hour[<?php echo $i; ?>]">
                          </td>
                          <td>
                            <input type="text" class="form-control" value="<?php echo isset($recordList[$i]['min']) ? $recordList[$i]['min'] : ''; ?>" name="min[<?php echo $i; ?>]">
                          </td>
                          
                          <td>
                            <select class="form-control" name="status[<?php echo $i; ?>]">
                              <option value="1" <?php if(isset($recordList[$i]['status']) && $recordList[$i]['status'] == 1){ ?> selected="selected" <?php } ?>>Active</option>
                              <option value="0" <?php if(isset($recordList[$i]['status']) && $recordList[$i]['status'] == 0){ ?> selected="selected" <?php } ?>>Deactive</option>
                            </select>
                          </td>
                        </tr>
                      <?php } ?>
                    
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Hour</th>
                      <th>Min</th>
                      <th>Status</th>
                      </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

