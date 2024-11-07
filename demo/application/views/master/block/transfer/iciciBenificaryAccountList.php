 <div class="row">
    <div class="col-sm-12">
     {system_message}    
     {system_info} 
    </div>  
  </div>
  <div class="row">
<?php 
    $already_request_pending = $this->db->get_where('icici_payout_user_request',array('account_id'=>$account_id,'user_id'=>$loggedAccountID,'status'=>1))->num_rows();

   if(!$already_request_pending) {
     ?>

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
      <h3><b>Payout Account Change Request</b></h3>  
      </div>

      <?php echo form_open_multipart('master/transfer/iciciBenificaryAccountAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
      <div class="card-body">

      <div class="row">

        <div class="col-sm-3">
         <div class="form-group">
          <label><b>Account Holder Name*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="account_holder_name" id="account_holder_name" placeholder="Holder Name">
          <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
          </div> 
        </div>

      <!--   <div class="col-sm-3">
          <div class="form-group">
          <label><b>Bank Name*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="bank_name" id="bank_name" placeholder="Bank Name">
          <?php echo form_error('bank_name', '<div class="error">', '</div>'); ?>  
          
          </div>
        </div>  -->
         <div class="col-sm-3">
          <div class="form-group">
            <label><b>Bank Name</b></label>
              
              <select name="bank_id" class="form-control">
                  <option>Select Bank</option>

                  <?php foreach ($bankList as  $value) { ?>

                    <option value="<?php  echo $value['id']?>"><?php echo $value['bank_name']; ?></option>
                  <?php } ?>
              </select>
          </div>
          
        </div>

        <div class="col-sm-3">
          <div class="form-group">
          <label><b>Account No.*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="account_number" id="account_number" placeholder="Account No.">
          <?php echo form_error('account_number', '<div class="error">', '</div>'); ?>  
          
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
          <label><b>IFSC Code*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="ifsc" id="ifsc" placeholder="IFSC Code">
          <?php echo form_error('ifsc', '<div class="error">', '</div>'); ?>  
          
          </div>
        </div>

      </div>  

        
      </div>

      <div class="card-footer">
       <button class="btn btn-primary" type="submit">Send Request</button> 
      </div>


    </div>

  </div>  
<?php } ?>

  </div> 


  <div class="row">

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
      <h3><b>Request List</b></h3>  
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="example">
            <thead>
            <tr>
              <th>#</th>
              <th>Beneficiary Name</th>
              <th>Account No.</th>
              <th>Bank</th>
              <th>IFSC</th>
              <th>Added On</th>
              <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($benificaryList){
                $i=1;
              foreach($benificaryList as $list){
             ?>
            <tr>
              <td class="align-middle"><?php echo $i; ?></td>
              <td class="align-middle"><?php echo $list['account_holder_name']; ?></td>
              <td class="align-middle"><?php echo $list['account_no']; ?></td>
              <?php $get_bank_name = $this->db->get_where('aeps_bank_list',array('id'=>$list['bank_id']))->row_array();
                $bank_name = $get_bank_name['bank_name'];
               ?>
              <td class="align-middle"><?php echo $bank_name; ?></td>
              <td class="align-middle"><?php echo $list['ifsc']; ?></td>
              <td class="align-middle"><?php echo date('d-m-Y',strtotime($list['created'])); ?></td>
              <td class="align-middle">
                <?php if($list['status']==1) {?>
                 <span class="text-warning">Pending</span>
                <?php } elseif($list['status']==2){ ?>
                  <span class="text-success">Approved</span>

                <?php } elseif($list['status']==3){ ?>
                 <span class="text-danger">Rejected</span>
                                    <?php }?>
              </td>
              </tr>
            <?php $i++; }}else{  ?>  
            <tr>
            <th colspan="7" class="align-middle text-center">No Record Found</th>
            </tr>
            <?php } ?>
            </tbody>

            <tfoot>
              <tr>
              <th>#</th>
              <th>Beneficiary Name</th>
              <th>Account No.</th>
              <th>Bank</th>
              <th>IFSC</th>
              <th>Added On</th>
              <th>Status</th>
            </tr> 
            </tfoot>

          </table>
          </div>
      </div>

    

    </div>

  </div>  


  </div> 
</div>

<?php echo form_close(); ?>