<div class="row">
    <div class="col-sm-12">
      {system_message}    
      {system_info}
    </div>
  </div>
  <div class="row mt-3">

    <div class="col-sm-12">

     <div class="card">
      
      <div class="card-header">
        <h3><b>Payout Report</b></h3>
      </div>  

      <div class="card-body">
      <div class="table-responsive">
               <table class="table table-bordered" id="example">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Benificary</th>
                      <th>Transfer Amount</th>
                      <th>Transfer Charge</th>
                      <th>Net Amount</th>
                      <th>Txn ID</th>
                      <th>RRN</th>
                      <th>Status</th>
                      <th>Check Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if($recharge){
                      foreach($recharge as $list){
                     ?>
                    <tr>
                      <td class="align-middle"><?php echo date('d-m-Y',strtotime($list['created'])); ?></td>
                      <td class="align-middle"><?php echo $list['account_holder_name'].' ('.$list['account_number'].')'; ?></td>
                      <td class="align-middle"><?php echo $list['transfer_amount'].' /-'; ?></td>
                      <td class="align-middle"><?php echo $list['transfer_charge_amount'].' /-'; ?></td>
                      <td class="align-middle"><?php echo $list['total_wallet_deduct'].' /-'; ?></td>
                      <td class="align-middle"><?php echo $list['refid']; ?></td>
                      <td class="align-middle"><?php echo $list['ackno']; ?></td>
                      
                      <td class="align-middle">
                      <?php if($list['status'] == 1) {
                        echo '<font color="orange">Pending</font>';
                      }
                      elseif($list['status'] == 2) {
                        echo '<font color="green">Success</font>';
                      }
                      else{
                        echo '<font color="red">Failed</font>';
                      }
                        ?>
                      </td>
              
                      <td class="align-middle"><a href="{site_url}retailer/newaeps/checkTransferStatus/<?=$list['refid']?>" class="btn btn-primary btn-sm">Check Status</a></td>
            
                      </tr>
                  <?php }}else{  ?>  
          <tr>
          <td colspan="8" class="align-middle text-center">No Record Found</td>
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
<!-- Page Header end --> 
