<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('superadmin/master/autoSettlementAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>On/Off Auto Settlement</b></h4>
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
                      <th><input type="checkbox" id="account_check_all"></th>
                      <th>Account</th>
                      <th>Percentage(%)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if($accountList){ ?>
                      <?php $i = 1; foreach($accountList as $list){ ?>
                        <tr>
                          <td><input type="checkbox" name="account_id[<?php echo $i; ?>]" <?php if($list['is_on'] == 1){ ?> checked="checked" <?php } ?> value="<?php echo $list['id']; ?>"></td>
                          <td><?php echo $list['title']; ?></td>
                          <td>
                            <input type="text" name="percentage[<?php echo $i; ?>]" class="form-control" value="<?php echo $list['percentage']; ?>">
                          </td>
                        </tr>
                      <?php $i++; } ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>Percentage(%)</th>
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

