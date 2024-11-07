<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('admin/master/saveDMRCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>My NSDL Pancard Charge</b></h4>
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
                      <th>Charge</th>
                      
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
                    <td>NSDL Pancard</td>
                    <td><?php echo $list['surcharge']; ?></td>
                    
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
                      <th>Service</th>
                      <th>Charge</th>
                      
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>
