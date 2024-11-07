<div class="container-fluid">
  {system_message}               
  {system_info}
<div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>UTI Balance Request </b></h4>
                </div>
               
               </div>  
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th width="5%">#</th>
                      <th>Txn ID</th>                      
                      <th>Register ID</th>
                      <th>Coupon</th>
                      <th>Status</th>
                      <th>Reason</th>
                      <th>Created</th>
                      
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    foreach($records as $list){
                    ?>
                    
                    <tr>
                    <td width="5%"><?php echo $i; ?></td>                    
                     <td><?php echo $list['txn_id']; ?></td> 
                     <td><?php echo $list['uti_pan_id']; ?></td>   
                      <td><?php echo $list['coupon']; ?></td>                     
                      <td>
                        <?php
                          if($list['status'] == 1)
                          {
                         ?>
                         <font color="orange"> Pending</font>
                       <?php } elseif($list['status'] == 2){ ?>
                        <font color="green"> Success</font>
                      <?php } elseif($list['status'] == 3){ ?>
                        <font color="red"> Reject</font>
                      <?php } ?>

                      </td>
                      <td>
                        <?php   if($list['status'] == 3){ ?>
                        <p> <?php echo $list['remark'] ?> </p> 
                      <?php } else  {?>
                        <p>  Not Found</p>
                      <?php } ?>
                      </td>
                     <td><?php echo date('d-M-Y',strtotime($list['created'])); ?></td>
                    
                    </tr> 

                   <?php $i++;} ?>
                  </tbody>

                  <tfoot>
                    <tr>
                     <th width="5%">#</th>
                      <th>Txn ID</th>                      
                      <th>Register ID</th>
                      <th>Coupon</th>
                      <th>Status</th>
                      <th>Reason</th>
                      <th>Created</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

