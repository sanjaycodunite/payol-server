 <div class="row">
    <div class="col-sm-12">
     {system_message}    
     {system_info} 
    </div>  
  </div>
  <div class="row">



  <div class="col-sm-12">
    
    <div class="card">

      <div class="card-header">
      <h3><b>Beneficiary List </b></h3>  
      </div>

      <?php echo form_open_multipart('retailer/settlement/upiOpenPayoutBenificaryAuth', array('id' => 'upi_verify_form'),array('method'=>'post')); ?>
      <div class="card-body">

      <div class="row">

        <div class="col-sm-3">
         <div class="form-group">
          <label><b>Account Holder Name*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="account_holder_name" id="upi_account_holder_name" placeholder="Holder Name">
          <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
          </div> 
        </div>


        <div class="col-sm-3">
          <div class="form-group">
          <label><b>UPI ID.*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="account_number" id="account_number" placeholder="UPI ID.">
          <?php echo form_error('account_number', '<div class="error">', '</div>'); ?>  
          
          </div>
        </div>


         <div class="col-sm-12">
                <div class="form-group ajaxx-loader">

                </div>
               </div>


     

      </div>  

        
      </div>

      <div class="card-footer">
        <button type="button" id="settlementUpiVerifyBtn" class="btn btn-success" name="verify" value="verify">Verify & Add</button>
       <button class="btn btn-primary" type="submit">Save New Beneficiary</button> 
      </div>


    </div>

  

  </div>  


  </div> 


  <div class="row">

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
      <h3><b>Beneficiary List</b></h3>  
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="example">
            <thead>
            <tr>
              <th>#</th>
              <th>Beneficiary Name</th>
              <th>Account No.</th>
              
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
              
              <td class="align-middle"><?php echo date('d-m-Y',strtotime($list['created'])); ?></td>
              <td class="align-middle">
                <a href="{site_url}retailer/settlement/upiOpenPayoutFundTransfer/<?php echo $list['ben_id']; ?>"><button class="btn btn-primary" type="button">Transfer</button></a>
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

<div class="modal fade" id="bankUpiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">UPI Verification</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="bankUpiResponse">
              
            </div>
            <div class="card-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                  <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                </div>

                <div class="col-sm-6 text-right">
                  <button type="submit" class="btn btn-success btn-sm">Add Beneficiary</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

<?php echo form_close(); ?>