<div class="container-fluid">
  {system_message}               
  {system_info}
<div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-12">
                <h4><b>Coupon List</b></h4>
                </div>
                </div>  
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th width="5%">#</th>
                      <th>PSA Login ID</th>
                      <th>Txn ID</th>
                      <th>Coupon</th>
                      <th>Quantity</th>
                      <th>Charge Amount</th>
                      <th>Total Charge</th>
                      <th>Status</th>
                      <th>Datetime</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    foreach($couponList as $list){
                    ?>
                    
                    <tr>
                    <td width="5%"><?php echo $i; ?></td>
                    <td><?php echo $list['psa_login_id']; ?></td>
                    <td><?php echo $list['txnid']; ?></td>
                    <td><?php echo $list['coupon']; ?></td>
                    <td><?php echo $list['quantity']; ?></td>
                    <td>&#8377; <?php echo $list['charge_amount']; ?></td>
                    <td>&#8377; <?php echo $list['total_wallet_charge']; ?></td>
                    <td>
                      <?php if($list['status'] == 1){ ?>
                        <font color="orange">Pending</font>
                      <?php }elseif($list['status'] == 2){ ?>
                        <font color="green">Success</font>
                      <?php }elseif($list['status'] == 3){ ?>
                        <font color="red">Failed</font>
                      <?php } ?>
                    </td>
                    <td><?php echo date('d-M-Y',strtotime($list['created'])); ?></td>
                    
                    </tr> 

                   <?php $i++;} ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th width="5%">#</th>
                      <th>PSA Login ID</th>
                      <th>Txn ID</th>
                      <th>Coupon</th>
                      <th>Quantity</th>
                      <th>Charge Amount</th>
                      <th>Total Charge</th>
                      <th>Status</th>
                      <th>Datetime</th>
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




