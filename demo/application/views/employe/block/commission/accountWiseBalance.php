<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-12">
                <h4><b>Account Wise Commission Balance</b></h4>
                </div>
                </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>Commission Balance</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                     $i = 1;
                     foreach($accountList as $list){
                      $account_c_wallet_balance = $this->User->getAccountWiseAepsCommisionBlance($list['id']);

                    ?>
                    <tr>
                      <td><?=$i?></td>
                      <td><?=$list['name'];?></td>
                      <td>&#8377; <?=$account_c_wallet_balance?></td>
                      <td><a href="{site_url}employe/commission/release/<?php echo $list['id']; ?>" class="btn btn-primary">Release Commission</a></td>
                    </tr>

                  <?php $i++;} ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>Commission Balance</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

