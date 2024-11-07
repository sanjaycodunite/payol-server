   <div class="container-fluid">
  <div class="row">
    <div class="col-sm-6">
      {system_message}    
      {system_info}
    </div>
  </div>
  <div class="row">

  <div class="col-sm-6">

    <div class="card recent_card">

      <div class="recent_card-header">
      <h3><b>Mobile Postpaid</b></h3>  
      <div class="recent_top_logo"><img src="{site_url}skin/admin/img/BBPS_Logo1.png"></div>
      </div>
      <?php echo form_open_multipart('master/recharge/mobileRecharge', array('id' => 'admin_profile','method'=>'post')); ?>
      <div class="card-body">
    <!--  <div class="card_listCol">
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="jio" checked>
            <label for="jio" class="list_label">
             <img src="{site_url}skin/admin/img/jio_logo.png" class="img-fluid">    
            </label>
          </div>  
          
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="airtel">
            <label for="airtel" class="list_label">
             <img src="{site_url}skin/admin/img/airtel_logo.png" class="img-fluid">    
            </label>
          </div> 
          
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="vi">
            <label for="vi" class="list_label">
             <img src="{site_url}skin/admin/img/vi_logo.png" class="img-fluid">    
            </label>
          </div> 
          
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="bsnl">
            <label for="bsnl" class="list_label">
             <img src="{site_url}skin/admin/img/bsnl_logo.png" class="img-fluid">    
            </label>
          </div> 
        </div>  -->
          
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
         <div class="m-auto col-sm-6">
          <a href="#" class="btn_plan_view" onclick="showOfferModal(); return false;">Fetch & View Bill</a>
         </div> 
     <div class="col-sm-6 text-right">
          <a href="#" onclick="showROfferModal(); return false;">ROFFER</a>
         </div>
        </div>
      
        
      </div>

     
      <?php echo form_close(); ?>

    </div>
    
    <div class="card mt-3">
      <div class="card-body">
          <div class="card_recharge_list">
            <div class="card_list_col">
             <h6>Bill Number</h6>  
             <h3>ABC1234567890</h3>
            </div>
            <div class="card_list_col text-right">
             <h6>Biller Name | Circle</h6>  
             <h3>Airtel Postpaid | 119</h3>
            </div> 
          </div>
          
          <div class="card_recharge_list">
            <div class="card_list_col">
             <h6>Due Date</h6>  
             <h3>15-11-2023</h3>
            </div>
            <div class="card_list_col text-right">
             <h6>Bill Date</h6>  
             <h3>25-11-2023</h3>
            </div> 
          </div>
          
          <div class="card_recharge_list">
            <div class="card_list_col">
             <h6>Mobile Number</h6>  
             <h3>0123456789</h3>
            </div>
            <div class="card_list_col text-right">
             <h6>Bill Period</h6>  
             <h3>4BH</h3>
            </div> 
          </div>
          <div class="card_recharge_list mt-3">
              <button class="btn_price_text btn btn-primary"><i class="fa fa-inr" aria-hidden="true"></i> 249.00</button> 
           <button class="btn btn-secondary" id="submit-btn" type="submit">Proceed Recharge</button> 
           </div>
      </div>  
    </div>

  </div>  
  
  <div class="col-sm-6">
    <div class="card recent_card">
      <div class="recent_card-header"> 
      <h3><b>Recent Transactions</b></h3>  
      </div>
      <div class="card-body">
        <div class="table-responsive" id="recharge-comm-block">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Mobile No.</th>
                        <th>Operator</th>
                        <th>Amount</th>
                        <th>TXN Status</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>0123456789</td>
                            <td>JIO <span style="display:block;font-size: 9px;">12:15:31 PM <span class="date_span">16-11-2023</span></span></td>
                            <td><i class="fa fa-inr" aria-hidden="true"></i> 249.00</td>
                            <td><font color="green">Success</font></td>
                            </tr>
                            <tr>
                           <td>0123456789</td>
                            <td>JIO <span style="display:block;font-size: 9px;">12:15:31 PM <span class="date_span">16-11-2023</span></span></td>
                            <td><i class="fa fa-inr" aria-hidden="true"></i> 249.00</td>
                            <td><font color="red">Refund</font></td>
                            </tr>
        </tbody></table></div>  
          </div> 
</div> </div> 

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
