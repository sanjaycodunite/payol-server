<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6">
      {system_message}    
      {system_info}
    </div>
  </div>
  <div class="row">
   <div class="m-auto col-lg-4 col-md-4">
 <div class="recharge_alrt_area">
   <a href="#"  data-toggle="modal" data-target="#comisionModal"><h5><i class="fa fa-percent"></i> Get Up to 4% Commision On Recharge</h5></a>
 </div> 
</div> 
  </div>
<!--popup-->
<div id="comisionModal" class="modal fade" role="dialog" aria-modal="true">
  <div class="modal-dialog" style="max-width: 70%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">MY Commision</h4>
        <button type="button" class="close" data-dismiss="modal">×</button>
        
      </div>
      <div class="modal-body">
        <div class="table-responsive" id="recharge-comm-block">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Operator</th>
                      <th>Code</th>
                      <th>Type</th>
                      <th>Commission</th>
                      <th>Is Flat ?</th>
                      <th>Is Surcharge ?</th>
                      </tr>
                  </thead>

                 <tbody>
                   <?php
                    if($operatorList){
                      $i=1;
                      foreach($operatorList as $key=>$list){
                   ?> 
                   <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $list['operator_name']; ?></td>
                    <td><?php echo $list['operator_code']; ?></td>
                    <td><?php echo $list['type']; ?></td>
                    <td><?php echo $list['commision']; ?></td>
                    <td><?php echo ($list['is_flat']) ? '<font color="green">Yes</font>' : '<font color="red">No</font>'; ?></td>
                    <td><?php echo ($list['is_surcharge']) ? '<font color="green">Yes</font>' : '<font color="red">No</font>'; ?></td>
                    
                   </tr>
                   <?php $i++;}} ?> 
                  </tbody>


                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Operator</th>
                      <th>Code</th>
                      <th>Type</th>
                      <th>Commission</th>
                      <th>Is Flat ?</th>
                      <th>Is Surcharge ?</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
        </div>
      </div>
    </div></div>

  <div class="row">

  <div class="col-sm-6">

    <div class="card">
<div class="recent_card-header">
      <h3><b>Prepaid Mobile Recharge</b></h3>  
      
      </div>
      
      <?php echo form_open_multipart('retailer/recharge/mobileRecharge', array('id' => 'admin_profile','method'=>'post')); ?>
      <div class="card-body card_aeps_form">
          <div class="card_listCol">
               <?php
         if($prepaid_operator){
         foreach($prepaid_operator as $list){
         ?> 
          <div class="list_card_li">
            <input type="radio" class="input_List" name="operator" id="<?php echo $list['id'] ?>" value="<?php echo $list['id'] ?>">
            <!--<input type="radio" id="html" name="fav_language" value="<?php echo $list['id'] ?>">-->
            <label for="<?php echo $list['id'] ?>" class="list_label">
             <img src="{site_url}<?php echo $list['icon'] ?>" class="img-fluid">    
            </label>
          </div>
          <?php } } ?>
          
          <!--<div class="list_card_li">-->
          <!--  <input type="radio" class="input_List" name="operator" id="airtel">-->
          <!--  <label for="airtel" class="list_label">-->
          <!--   <img src="{site_url}skin/admin/img/airtel_logo.png" class="img-fluid">    -->
          <!--  </label>-->
          <!--</div> -->
          
          <!--<div class="list_card_li">-->
          <!--  <input type="radio" class="input_List" name="operator" id="vi">-->
          <!--  <label for="vi" class="list_label">-->
          <!--   <img src="{site_url}skin/admin/img/vi_logo.png" class="img-fluid">    -->
          <!--  </label>-->
          <!--</div> -->
          
          <!--<div class="list_card_li">-->
          <!--  <input type="radio" class="input_List" name="operator" id="bsnl">-->
          <!--  <label for="bsnl" class="list_label">-->
          <!--   <img src="{site_url}skin/admin/img/bsnl_logo.png" class="img-fluid">    -->
          <!--  </label>-->
          <!--</div> -->
        </div>
       
       <div class="form-group">
        <label><b>Mobile Number*</b></label>
        <input type="text" class="form-control" name="mobile" data-bv-field="number" id="offermobile" placeholder="Enter Mobile Number">
        <samp class="bar"></samp>
        <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
       </div>

  <!-- <div class="form-group">
        <label><b>Operator*</b></label>
        <select name="operator" class="form-control" id="operator">
         <option value="">Select Operator</option>
         <?php
         if($prepaid_operator){
         foreach($prepaid_operator as $list){
         ?>  
         <option value="<?php echo $list['id']; ?>"><?php echo $list['operator_name']; ?></option>
         <?php }} ?>
         </select>
         <?php echo form_error('operator', '<div class="error">', '</div>'); ?>
       </div>  -->

       

       <div class="form-group">
        <label><b>Amount*</b></label>
        <input class="form-control" name="amount" id="amount" placeholder="Amount" type="text">
        <samp class="bar"></samp>
        <?php echo form_error('amount', '<div class="error">', '</div>'); ?>
       </div> 

       <div class="form-group">
        <label><b>Transcation Pin*</b></label>
        <input class="form-control" name="txn_pass" id="txn_pass" placeholder="Enter Transcation Pin" type="password" maxlength="4">
        <?php echo form_error('txn_pass', '<div class="error">', '</div>'); ?>
       </div> 

       <div class="row">
         <div class="col-sm-12 text-right">
          <a href="#" class="form_btn" onclick="showOfferModal(); return false;"><font color="red">View Plan</font></a>
         </div> 
        <div class="col-lg-12 col-md-12">
           <div class="text-center">
       <button class="btn btn-success" id="submit-btn" type="submit">Recharge Now</button> 
      </div>
         </div>

        </div> 
      
        
      </div>

      
      <?php echo form_close(); ?>

    </div>

  </div>  
  
  
  <div class="col-sm-6">
    <div class="card recent_card">
      <div class="recent_card-header"> 
      <h3><b>Recent Transactions</b></h3>  
      </div>
      <div class="card-body">
        <div class="card_recharge_overflow">
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
                            <?php  
                                $i=1;
                            foreach($recent_recharge_list as $list){?>
                            <tr>
                            <td><?php echo $list['mobile'] ?></td>
                            <td><?php echo $list['operator_code'] ?> <span style="display:block;font-size: 9px;"><span class="date_span"><?php echo date('d-M-Y H:i:s',strtotime($list['created'])); ?></span></span></td>
                            <td><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $list['amount'] ?></td>
                            <td>
                                <?php if($list['status'] == 1) { ?>
                                <font color="orange">Pending</font>
                                <?php } elseif($list['status'] == 2){ ?>
                                    <font color="green">Success</font>
                                    <?php } elseif($list['status'] == 3) { ?>
                                    <font color="red">Failed</font>
                                    <?php } elseif($list['status'] == 4) { ?>
                                    <font color="red">Refund</font>
                                    <?php } ?>
                                </td>
                            </tr>
                            
                            <?php  $i++; }
                           ?>
        </tbody></table></div>  
          </div> </div>  
</div> </div> 


  </div></div>  

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
               if($prepaid_operator){
               foreach($prepaid_operator as $list){
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
               if($prepaid_operator){
               foreach($prepaid_operator as $list){
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
