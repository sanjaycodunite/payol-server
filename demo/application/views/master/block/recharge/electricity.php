  <div class="row">
    <div class="col-sm-6">
      {system_message}    
      {system_info}
    </div>
  </div>
  <div class="row">

  <div class="col-sm-6">

    <div class="card">

      <div class="card-header">
      <h3><b>Electricity Bill Payment</b></h3>  
      </div>
      <?php echo form_open_multipart('master/recharge/electricityBill', array('id' => 'electricity-form')); ?>
      <div class="card-body">
       <input type="hidden" name="site_url" id="siteUrl" value="{site_url}">
       <input type="hidden" value="<?php echo set_value('fetch_status'); ?>" id="fetch_status" name="fetch_status" />
       <input type="hidden" value="<?php echo set_value('fieldName'); ?>" id="fieldName" name="fieldName" />
       <input type="hidden" value="<?php echo set_value('fieldOther'); ?>" id="fieldOther" name="fieldOther" />
       <input type="hidden" value="<?php echo set_value('reference_id'); ?>" id="reference_id" name="reference_id" />
       <div class="form-group">
        <label><b>Operator*</b></label>
        <select name="operator" class="form-control" id="electricityOperator">
         <option value="">Select Operator</option>
         <?php
         if($electricity_operator){
         foreach($electricity_operator as $list){
         ?>  
         <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
         <?php }} ?>
         </select>
         <?php echo form_error('operator', '<div class="error">', '</div>'); ?>
         
       </div>

       <div class="form-group" id="field-block" <?php if(set_value('fetch_status') == 1){ ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
                <input type="text" class="form-control" data-bv-field="number" id="account_number" name="account_number" placeholder="<?php echo 'Enter '.set_value('fieldName'); ?>">
        <?php echo form_error('account_number', '<div class="error">', '</div>'); ?> 
              </div>

        <div class="form-group" id="name-field-block" <?php if(set_value('fetch_status') == 1){ ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
                <input type="text" class="form-control" data-bv-field="number" id="customer_name" name="customer_name" placeholder="<?php echo 'Enter '.set_value('fieldOther'); ?>">
        <?php echo form_error('customer_name', '<div class="error">', '</div>'); ?> 
              </div>
              <div class="electricity-biller-name"></div>
        <div id="amount-field-block" <?php if(set_value('fetch_status') == 1){ ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
        <div class="form-group input-group">
                <div class="input-group-prepend"> <span class="input-group-text"><b>&#8377;</b></span> </div>
                <input class="form-control" name="amount" id="amount" placeholder="Enter Amount*" type="text">
              </div>
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?> 
        </div>
        <div class="ajax-loader"></div>
        
      
        
      </div>

      <div class="card-footer">
       <button class="btn btn-primary" type="submit">Proceed Payment</button> 
      </div>
      <?php echo form_close(); ?>

    </div>

  </div>  


  </div> 

</div>

