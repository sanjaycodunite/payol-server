<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('employe/master/saveAEPSCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>My AEPS Commission</b></h4>
                </div>

                

              </div>
            </div>
            
            <div class="card-body">
              
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
                      
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

