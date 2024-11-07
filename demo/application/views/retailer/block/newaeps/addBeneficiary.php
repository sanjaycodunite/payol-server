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
      <h3><b>Beneficiary</b></h3>  
      </div>

      <?php echo form_open_multipart('retailer/newaeps/benificaryAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
      <div class="card-body">

      <div class="row">

        <div class="col-sm-3">
         <div class="form-group">
          <label><b>Account Holder Name*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="account_holder_name" id="account_holder_name" placeholder="Account Holder Name">
          <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
          </div> 
        </div>

        <div class="col-sm-3">
          <div class="form-group">
          <label><b>Bank*</b></label>
          <select class="form-control selectpicker" data-live-search="true" name="bank">
            <option value="">Select Bank</option>
            <?php
             foreach($bankList as $list){
            ?>
            <option value="<?=$list['id']?>"><?=$list['bank_name']?></option>
            <?php } ?>
          </select>
          <?php echo form_error('bank', '<div class="error">', '</div>'); ?>  
          
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
       <button class="btn btn-primary" type="submit">Proceed</button> 
      </div>


    </div>

  </div>  


  </div> 

</div>

<?php echo form_close(); ?>