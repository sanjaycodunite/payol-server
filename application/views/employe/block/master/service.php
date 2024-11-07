<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('employe/master/saveServiceAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Manage Service</b></h4>
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
                  <label><b>Select Member</b></label>
                </div>
                <div class="col-sm-3">
                  <select class="form-control selectpicker" data-live-search="true" name="memberID" id="selMemberID">
                    <option value="0">My Service</option>
                    <?php if($userList){ ?>
                      <?php foreach($userList as $list){ ?>
                        <option value="<?php echo $list['id']; ?>"><?php echo $list['name'].' ('.$list['user_code'].')'; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-2">
                <button type="button" id="serviceSearchBtn" class="btn btn-success">Search</button>
                </div>
                <div class="col-sm-12 text-center recharge-comm-loader">
                </div>

              </div>
              <br />
              <div class="table-responsive" id="recharge-comm-block">
                <table class="table table-bordered table-striped"  width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Service</th>
                      <th>Active/Deactive</th>
                      </tr>
                  </thead>

                  <tbody>
                   <?php
                    if($operatorList){
                      $i=1;
                      foreach($operatorList as $key=>$list){
                   ?> 
                   <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $list['title']; ?></td>
                    <td><?php echo ($list['status'] == 1) ? '<font color="green">Active</font>' : '<font color="red">Deactive</font>'; ?></td>
                   </tr>
                   <?php $i++;}} ?> 
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Service</th>
                      <th>Active/Deactive</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

