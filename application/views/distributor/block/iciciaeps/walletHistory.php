<div class="row">
    <div class="col-sm-12">
     
     <div class="card">
      
      <div class="card-header">
      <h3><b>AEPS Wallet History</b></h3>
      </div>

      <div class="card-body">
       <div class="table-responsive">
                <table class="table table-bordered" id="example">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Type</th>
                      <th>Before Amount</th>
                      <th>Cr/Dr Amount</th>
                      <th>After Amount</th>
                      <th>Date Time</th>
                      
                      <th>Description</th>
                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if($recharge){
                      $i=1;
                      foreach($recharge as $list){
                     ?>
                    <tr>
                      <td><?php echo $i; ?></td>
                      <td class="align-middle">
            <?php if($list['type'] == 1) {
              echo '<font color="green">Cr.</font>';
            }
            elseif($list['type'] == 2) {
              echo '<font color="red">Dr.</font>';
            }
              ?>
            </td>
                      <td class="align-middle"><?php echo $list['before_balance'].' /-'; ?></td>
                      
                      
                      <td class="align-middle">
            <?php if($list['type'] == 1) {
              echo '<font color="green">'.$list['amount'].' /-</font>';
            }
            elseif($list['type'] == 2) {
              echo '<font color="red">'.$list['amount'].' /-</font>';
            }
              ?>
            </td>
            <td class="align-middle"><?php echo $list['after_balance'].' /-'; ?></td>
            <td class="align-middle"><?php echo date('d-M-Y H:i:s',strtotime($list['created'])); ?></td>
                       
            <td class="align-middle"><?php echo $list['description']; ?></td>
           
                      </tr>
                  <?php $i++;}}else{  ?>  
          <tr>
          <td colspan="6" class="align-middle text-center">No Record Found</td>
          </tr>
          <?php } ?>
                  </tbody>
                </table>
              </div> 
      </div>

     </div>

    </div> 
  </div>

  </div> 
