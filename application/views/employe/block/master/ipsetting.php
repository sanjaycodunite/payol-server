<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('employe/master/saveIpAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Manage IP</b></h4>
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
                      <th>Member</th>
                      <th>IP</th>
                      </tr>
                  </thead>

                  <tbody>
                   <?php
                    if($userList){
                      $i=1;
                      foreach($userList as $key=>$list){
                   ?> 
                   <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $list['name']; ?> (<?php echo $list['user_code']; ?>)</td>
                    <td><input type="text" name="ipaddress[<?php echo $list['id']; ?>]" value="<?php echo $list['whitelist_ip']; ?>"><p style="font-size: 12px; color: red;">Put * for allow all ip.</p></td>
                   </tr>
                   <?php $i++;}} ?> 
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Member</th>
                      <th>IP</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

