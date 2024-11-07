  <div class="container-fluid">
  <div class="row">
    <div class="col-sm-6">
      {system_message}    
      {system_info}
    </div>
  </div>
  <div class="row">

  <div class="col-sm-6">
<div class="card">
 <div class="recent_card-header">
      <h3><b>DTH Recharge</b></h3>  
      </div>
    
      <?php echo form_open_multipart('master/recharge/dthRecharge', array('id' => 'admin_profile','method'=>'post')); ?>
      <div class="card-body">
   <!--  <div class="card_listCol card_dth_list">
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="distv" checked>
            <label for="distv" class="list_label">
             <img src="{site_url}skin/admin/img/dishtv.png" class="img-fluid">    
            </label>
          </div>  
          
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="bigtv">
            <label for="bigtv" class="list_label">
             <img src="{site_url}skin/admin/img/bigtv.png" class="img-fluid">    
            </label>
          </div> 
          
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="videocon">
            <label for="videocon" class="list_label">
             <img src="{site_url}skin/admin/img/videocon_D2H.png" class="img-fluid">    
            </label>
          </div> 
          
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="sundirect">
            <label for="sundirect" class="list_label">
             <img src="{site_url}skin/admin/img/sundirect.png" class="img-fluid">    
            </label>
          </div> 
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="airteldish">
            <label for="airteldish" class="list_label">
             <img src="{site_url}skin/admin/img/airtel_logo.png" class="img-fluid">    
            </label>
          </div> 
          <div class="list_card_li">
            <input type="radio" class="input_List" name="list" id="tatasky">
            <label for="tatasky" class="list_label">
             <img src="{site_url}skin/admin/img/tatasky.png" class="img-fluid">    
            </label>
          </div> 
          
        </div> -->
        
 <div class="form-group">
        <label><b>Operator*</b></label>
        <select name="operator" class="form-control" id="operator">
         <option value="">Select Operator</option>
         <?php
         if($dth_operator){
         foreach($dth_operator as $list){
         ?>  
         <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
         <?php }} ?>
         </select>
         <?php echo form_error('operator', '<div class="error">', '</div>'); ?>
       </div>

       

       <div class="form-group">
        <label><b>Card Number*</b></label>
        <input type="text" class="form-control" data-bv-field="number" name="cardNumber" placeholder="Enter Card Number">
        <samp class="bar"></samp>
        <?php echo form_error('cardNumber', '<div class="error">', '</div>'); ?> 
       </div>

       <div class="form-group">
        <label><b>Amount*</b></label>
        <input class="form-control" name="amount" id="amount" placeholder="Amount" type="text">
        <samp class="bar"></samp>
        <?php echo form_error('amount', '<div class="error">', '</div>'); ?>
        <p id="balanceInfo" style="color: green; padding: 10px 0;"></p>
       </div> 

       <div class="row">
         <div class="col-sm-12 text-right">
          <a href="#" onclick="showOfferModal(); return false;">View Plan</a>
         </div> 
         
        </div> 

       <div class="mt-3 text-center">
       <button class="btn btn-secondary" type="submit">Proceed Recharge</button> 
      </div>
        
      </div>

      
      <?php echo form_close(); ?>

    </div></div>  
  
  
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
                        <th>Card Number.</th>
                        <th>Operator</th>
                        <th>Amount</th>
                        <th>TXN Status</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>000123456789</td>
                            <td>Dish TV <span style="display:block;font-size: 9px;">12:15:31 PM <span class="date_span">16-11-2023</span></span></td>
                            <td><i class="fa fa-inr" aria-hidden="true"></i> 549.00</td>
                            <td><font color="green">Success</font></td>
                            </tr>
                            <tr>
                           <td>000123456789</td>
                            <td>BIG TV <span style="display:block;font-size: 9px;">12:15:31 PM <span class="date_span">16-11-2023</span></span></td>
                            <td><i class="fa fa-inr" aria-hidden="true"></i> 649.00</td>
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
          <div class="col-sm-4"></div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select Operator</label>
              <select class="form-control" id="offerOperator" name="offerOperator">
                <option value="">Select Operator</option>
                <?php
               if($dth_operator){
               foreach($dth_operator as $list){
               ?>  
               <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
               <?php }} ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <button class="btn btn-primary mt-4" id="dthViewPlanSearchBtn" type="button">Search</button> 
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
        <h4 class="modal-title">Dth Plan</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body">
        <?php echo form_open('#',array('id'=>'rofferFilterForm')); ?>
        <div class="row">
          <div class="col-sm-2"></div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>DTH Card No.</label>
              <input type="text" class="form-control" name="roffermobile" id="roffermobile">
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select Operator</label>
              <select class="form-control" id="rofferOperator" name="rofferOperator">
                <option value="">Select Operator</option>
                <?php
               if($dth_operator){
               foreach($dth_operator as $list){
               ?>  
               <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
               <?php }} ?>
              </select>
            </div>
          </div>
          <div class="col-sm-2">
            <button class="btn btn-primary mt-4" id="dthRofferSearchBtn" type="button">Search</button> 
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
