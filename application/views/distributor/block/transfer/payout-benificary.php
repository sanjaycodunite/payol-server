 <div class="row">
    <div class="col-sm-12">
     {system_message}    
     {system_info} 
    </div>  
  </div>
  <div class="row">



  <div class="col-sm-12">
    <div class="alert alert-warning alert-dismissable">You Can Add Only One Beneficiary Account.</div>

    <?php 
      $check_beneficiary = $this->db->get_where('payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->num_rows();

      if($check_beneficiary !=1){
     ?>
    <div class="card">

      <div class="card-header">
      <h3><b>Beneficiary</b></h3>  
      </div>

      <?php echo form_open_multipart('distributor/transfer/payoutBenificaryAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
      <div class="card-body">

      <div class="row">

        <div class="col-sm-3">
         <div class="form-group">
          <label><b>Account Holder Name*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="account_holder_name" id="account_holder_name" placeholder="Holder Name">
          <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
          </div> 
        </div>

        <div class="col-sm-3">
        <div class="form-group">
        <label><b>Bank*</b></label>
        <select class="form-control selectpicker" name="bankID" id="bankID" data-live-search="true">
          <option value="">Select Bank</option>
          <?php if($bankList){ ?>
            <?php foreach($bankList as $list){ ?>
              <option value="<?php echo $list['id']; ?>"><?php echo $list['bank_name']; ?></option>
            <?php } ?>
          <?php } ?>
        </select>
        <?php echo form_error('bankID', '<div class="error">', '</div>'); ?>  
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
       <button class="btn btn-primary" type="submit">Save New Beneficiary</button> 
      </div>


    </div>

  <?php  } ?>

  </div>  


  </div> 


  <div class="row">

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
      <h3><b>Beneficiary</b></h3>  
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
              <th>Fund</th>
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
              <td class="align-middle"><?php echo $list['bank_name']; ?></td>
              <td class="align-middle"><?php echo $list['ifsc']; ?></td>
              <td class="align-middle"><?php echo date('d-m-Y',strtotime($list['created'])); ?></td>
              <td class="align-middle">
                <a href="{site_url}distributor/transfer/payoutFundTransfer/<?php echo $list['id']; ?>"><button class="btn btn-primary" type="button">Transfer</button></a>
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
              <th>Fund</th>
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