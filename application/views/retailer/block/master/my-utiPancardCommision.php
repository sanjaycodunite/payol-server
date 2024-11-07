<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('master/master/saveBBPSCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>My UTI Pancard Charge</b></h4>
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
                      <th>Charge Amount</th>
                      </tr>
                  </thead>

                  <tbody>
                 
                   <tr>
                    <td>1</td>
                    <td>UTI Pancard</td>
                    <td><?php echo $commision; ?></td>
                    
                   </tr>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Service</th>
                      <th>Charge Amount</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

