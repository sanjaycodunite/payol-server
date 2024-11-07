<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('admin/master/saveBBPSLiveCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>My BBPS Commssion</b></h4>
                </div>

                

              </div>
            </div>
            
            <div class="card-body">
              
              <div class="table-responsive" id="recharge-comm-block">
                <table class="table table-bordered table-striped"  width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Service</th>
                      <th>Commission</th>
                      <th>Is Flat ?</th>
                      <th>Is Surcharge ?</th>
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
                    <td><?php echo $list['commision']; ?></td>
                    <td><?php echo ($list['is_flat']) ? '<font color="green">Yes</font>' : '<font color="red">No</font>'; ?></td>
                    <td><?php echo ($list['is_surcharge']) ? '<font color="green">Yes</font>' : '<font color="red">No</font>'; ?></td>
                   </tr>
                   <?php $i++;}} ?> 
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Service</th>
                      <th>Commission</th>
                      <th>Is Flat ?</th>
                      <th>Is Surcharge ?</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

