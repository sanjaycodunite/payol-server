<div class="container-fluid">

<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Account List</b></h4>
                </div>

                <!--<div class="col-sm-2">-->
                <!--<?php echo form_open('',array('id'=>'leadFilterForm')); ?>-->
                <!--<input type="text" class="form-control datepick" placeholder="Date" name="date" id="date" autocomplete="off" />-->
                <!--</div>-->

                <!--<div class="col-sm-2">-->
                <!--<input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />-->
                <!--</div>-->

                <!--<div class="col-sm-4">-->
                <!--<button type="button" class="btn btn-success" id="beneficiaryAccountSearchBtn">Search</button>-->
                <!--<a href="{site_url}admin/member/viewBeneficiary" class="btn btn-secondary">View All</a>-->
                
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example1" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                     
                      <th>Account Holder Name</th>
                      <th>Account Number</th>
                      <th>Bank Name</th>
                      <th>IFSC Code</th>
                      <th>Action</th>
                      
                    </tr>
                  </thead>
                  <tbody>
                      <?php $i=1; foreach($account_list as $list) { ?>
                <tr>
                  <th scope="row"><?php echo $i; ?></th>
                  <td><?php echo $list['account_holder_name'];?></td>
                  <td><?php echo $list['account_number'];?></td>
                  <?php $get_bank = $this->db->get_where('new_payout_bank_list',array('id'=>$list['bank_id']))->row_array();
                    $bank_name = $get_bank['bank_name'];
                  ?>
                  <td><?php echo $bank_name;?></td>
                  <td><?php echo $list['ifsc'];?></td>
                  <td><a title="edit" class="btn btn-danger btn-sm" href="{site_url}employe/member/deleteAccount/<?php echo $list['bene_id']?>">Delete</a></td>
                 
                </tr>
                
                <?php $i++; } ?>
                
  </tbody>
                  <tfoot>
                    <tr>
                   
                     <th>#</th>
                     
                      <th>Account Holder Name</th>
                      <th>Account Number</th>
                      <th>Bank Name</th>
                      <th>IFSC Code</th>
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

