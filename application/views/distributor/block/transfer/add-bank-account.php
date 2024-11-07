<div class="container">
<div class="row">
<div class="col-lg-12"> 
  {system_message}    
     {system_info} 
<div class="card shadow mb-4">
<div class="card-body">
<h5 class="card-title">Add Account</h5>
<div class="open_payout_colm">
<ul>
<li class="preview-icon-item">
<div class="preview-icon-wrap">
<img alt="Bank Transfer" src="{site_url}skin/front/img/bank.png">
</div>
 <button class="btn btn-primary btn-sm mt-2" type="button" id="add_bank_account">Add Bank Account</button>
 <div class="col-sm-12 text-center recharge-comm-loader">
                </div>
</li>
<li class="preview-icon-item">

<div class="preview-icon-wrap">
<img alt="Bank Transfer" src="{site_url}skin/front/img/upi.png">
</div>
<button class="btn btn-primary btn-sm mt-2" type="button" id="add_upi_account">Add UPI Account</button>  
</li>
</ul> 
</div>  
</div>
</div>

</div>




</div>


    <div class="row">


        <div class="col-sm-12" style="display: none;" id="show_bank_account">
    <div class="alert alert-warning alert-dismissable">You Can Add Only 5 Beneficiary Account.</div>

    <?php 
      $check_beneficiary = $this->db->get_where('instantpay_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->num_rows();

      if($check_beneficiary !=5){
     ?>
    <div class="card">

      <div class="card-header">
      <h3><b>Beneficiary List </b></h3>  
      </div>

      <?php echo form_open_multipart('distributor/transfer/saveBenificaryBankAccount', array('id' => 'account_verify_form'),array('method'=>'post')); ?>
      <input type="hidden" name="type" value="1">
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
        
          <div class="col-sm-12">
                <div class="form-group ajaxx-loader">

                </div>
               </div>

      </div>  

        
      </div>

      <div class="card-footer">
        <button type="button" id="accountVerifyBtn" class="btn btn-success" name="verify" value="verify">Verify & Add</button>
       <button class="btn btn-primary" type="submit">Save New Beneficiary</button> 
      </div>


 
      

    </div>
    <div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Bank Verification</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="bankResponse">
              
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
  <?php  } ?>

  </div>  



  <div class="col-sm-12" style="display: none;" id="show_upi_account">
    <div class="alert alert-warning alert-dismissable">You Can Add Only five Beneficiary Account.</div>

    <?php 
      $check_beneficiary = $this->db->get_where('instantpay_upi_payout_user_benificary',array('account_id'=>$account_id,'user_id'=>$loggedAccountID))->num_rows();

      if($check_beneficiary !=5){
     ?>
    <div class="card">

      <div class="card-header">
      <h3><b>Beneficiary List </b></h3>  
      </div>

      <?php echo form_open_multipart('distributor/transfer/saveBenificaryBankAccount', array('id' => 'upi_verify_form'),array('method'=>'post')); ?>
      <input type="hidden" name="type" value="2">
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
           <button type="button" id="upiVerifyBtn" class="btn btn-success" name="verify" value="verify">Verify & Add</button>
       <button class="btn btn-primary" type="submit">Save New Beneficiary</button> 
      </div>
      
      
      


    </div>

      

  <?php  } ?>

  </div>  


      
    </div>




</div>


</div>
    
    
    <div class="modal fade" id="bankUpiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Bank Verification</h5>
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
<div id="updateDMRModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('distributor/transfer/updateBenificaryAuth',array('method'=>'post')); ?>
    <input type="hidden" name="recordID" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">Update Beneficiary</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    
    
    </div>
    <div class="modal-body">
    
    <div class="modalform">
      <input type="hidden" value="0" name="taskID" id="taskID" />
      <div class="row">
        <div class="col-md-12" id="updateDMRBlock">

        </div>
      </div>
    </div>
    
    </div>
    <?php echo form_close(); ?>
    
  </div>

  </div>
</div>
