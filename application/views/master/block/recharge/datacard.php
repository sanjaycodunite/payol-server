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
      <h3><b>Datacard Recharge</b></h3>  
      </div>
      <?php echo form_open_multipart('master/recharge/datacardRecharge', array('id' => 'admin_profile'),array('method'=>'post')); ?>
      <div class="card-body">
       
       <div class="form-group">
        <label><b>Operator*</b></label>
        <select name="operator" class="form-control" id="operator">
         <option value="">Select Operator</option>
         <?php
         if($datacard_operator){
         foreach($datacard_operator as $list){
         ?>  
         <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
         <?php }} ?>
         </select>
         <?php echo form_error('operator', '<div class="error">', '</div>'); ?>
       </div>

       
       <div class="form-group">
        <label><b>Data Card Number*</b></label>
        <input type="text" class="form-control" data-bv-field="number" id="cardNumber" name="cardNumber" placeholder="DataCard Number">
        <samp class="bar"></samp>
        <?php echo form_error('cardNumber', '<div class="error">', '</div>'); ?>   
       </div>

       <div class="form-group">
        <label><b>Amount*</b></label>
        <input class="form-control" name="amount" id="amount" placeholder="Amount" type="text">
        <samp class="bar"></samp>
        <?php echo form_error('amount', '<div class="error">', '</div>'); ?>
       </div> 
      
        
      </div>

      <div class="card-footer">
       <button class="btn btn-primary" type="submit">Proceed Recharge</button> 
      </div>
      <?php echo form_close(); ?>

    </div>

  </div>  


  </div> 

</div>

