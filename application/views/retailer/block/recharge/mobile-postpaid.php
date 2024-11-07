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
      <h3><b>Postpaid Mobile Recharge</b></h3>  
      </div>
      <?php echo form_open_multipart('retailer/recharge/mobileRecharge', array('id' => 'admin_profile','method'=>'post')); ?>
      <div class="card-body">
       <input type="hidden" name="recharge_type" value="2"> 
       <div class="form-group">
        <label><b>Mobile Number*</b></label>
        <input type="text" class="form-control" name="mobile" data-bv-field="number" id="offermobile" placeholder="Enter Mobile Number">
        <samp class="bar"></samp>
        <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
       </div>

       
       <div class="form-group">
        <label><b>Operator*</b></label>
        <select name="operator" class="form-control" id="operator">
         <option value="">Select Operator</option>
         <?php
         if($postpaid_operator){
         foreach($postpaid_operator as $list){
         ?>  
         <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
         <?php }} ?>
         </select>
         <?php echo form_error('operator', '<div class="error">', '</div>'); ?>
       </div>

       

       <div class="form-group">
        <label><b>Amount*</b></label>
        <input class="form-control" name="amount" id="amount" placeholder="Amount" type="text">
        <samp class="bar"></samp>
        <?php echo form_error('amount', '<div class="error">', '</div>'); ?>
       </div> 
       <div class="row">
         <div class="col-sm-6">
          <a href="#" onclick="showOfferModal(); return false;">View Plan</a>
         </div> 
         <div class="col-sm-6 text-right">
          <a href="#" onclick="showROfferModal(); return false;">ROFFER</a>
         </div> 
        </div> 
      
        
      </div>

      <div class="card-footer">
       <button class="btn btn-primary" id="submit-btn" type="submit">Proceed Recharge</button> 
      </div>
      <?php echo form_close(); ?>

    </div>

  </div>  


  </div> 

</div>
<!-- Modal -->
<div id="offerModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="max-width: 80%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View Plan</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body">
        <?php echo form_open('#',array('id'=>'offerFilterForm')); ?>
        <div class="row">
          <div class="col-sm-2"></div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select Operator</label>
              <select class="form-control" id="offerOperator" name="offerOperator">
                <option value="">Select Operator</option>
                <?php
               if($postpaid_operator){
               foreach($postpaid_operator as $list){
               ?>  
               <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
               <?php }} ?>
              </select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select Circle</label>
              <select class="form-control" id="offerCircle" name="offerCircle">
                <option value="">Select Circle</option>
                <?php
               if($circle){
               foreach($circle as $list){
               ?>  
               <option value="<?php echo $list['id']; ?>" <?php if($list['id'] == 19){ ?> selected="selected" <?php } ?>><?php echo $list['circle_name']; ?></option>
               <?php }} ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <button class="btn btn-primary mt-4" id="viewPlanSearchBtn" type="button">Search</button> 
          </div>
          <div class="col-sm-12"><hr /></div>
        </div>
        <?php echo form_close(); ?>
        <div class="row">
          <div class="col-sm-12" id="offerLoader">
            
          </div>
        </div>
      </div>
      
    </div>

  </div>
</div>

<!-- Modal -->
<div id="rofferModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="max-width: 80%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View Plan</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body">
        <?php echo form_open('#',array('id'=>'rofferFilterForm')); ?>
        <div class="row">
          <div class="col-sm-2"></div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Mobile</label>
              <input type="text" class="form-control" name="roffermobile" id="roffermobile">
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select Operator</label>
              <select class="form-control" id="rofferOperator" name="rofferOperator">
                <option value="">Select Operator</option>
                <?php
               if($postpaid_operator){
               foreach($postpaid_operator as $list){
               ?>  
               <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
               <?php }} ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <button class="btn btn-primary mt-4" id="rofferSearchBtn" type="button">Search</button> 
          </div>
          <div class="col-sm-12"><hr /></div>
        </div>
        <?php echo form_close(); ?>
        <div class="row">
          <div class="col-sm-12" id="rofferLoader">
            
          </div>
        </div>
      </div>
      
    </div>

  </div>
</div>

