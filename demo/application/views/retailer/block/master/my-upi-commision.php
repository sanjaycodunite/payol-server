<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('admin/master/saveAEPSCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>My UPI Collection Commission</b></h4>
                </div>

                

              </div>
            </div>
            
            <div class="card-body">
              
              <div class="table-responsive" id="dmr-comm-block">
                <table class="table table-bordered table-striped"  width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Service</th>
                      <th>Commision</th>
                      <th>Is Flat ?</th>
                      <th>Is Surcharge ?</th>
                      
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
                    <td>UPI</td>
                    <td><?php echo $list['rt_commision']; ?></td>
                    <td><?php echo ($list['is_flat']) ? '<font color="green">Yes</font>' : '<font color="red">No</font>'; ?></td> 
                    <td><?php echo ($list['is_surcharge']) ? '<font color="green">Yes</font>' : '<font color="red">No</font>'; ?></td> 
                    
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
                      <th>Service</th>
                      <th>Commision</th>
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

