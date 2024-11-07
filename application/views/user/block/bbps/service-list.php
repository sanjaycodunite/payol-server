  <div class="row">
    <div class="col-sm-6">
      {system_message}    
      {system_info}
    </div>
  </div>
  <div class="row">

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
      <h3><b>BBPS Live</b></h3>  
      </div>
      
      <div class="card-body">
       
       <div class="row">
<div class="recharge_services_items">
<ul>
    <li><a href="{site_url}user/bbps/prepaidRecharge"><span class="recharge_icons"><i class="flaticon-mobile-phone"></i></span>Mobile Prepaid</a></li>
<li><a href="#" data-toggle="modal" data-target="#mobile-postpaid"><span class="recharge_icons"><i class="flaticon-mobile-phone"></i></span>Mobile Postpaid</a></li>
<li><a href="#" data-toggle="modal" data-target="#electricity"><span class="recharge_icons"><i class="flaticon-plug"></i></span>Electricity</a></li>
<li><a href="#" data-toggle="modal" data-target="#dth"><span class="recharge_icons"><i class="flaticon-satellite-dish"></i></span>DTH</a></li>
<li><a href="#" data-toggle="modal" data-target="#boradband-postpaid"><span class="recharge_icons"><i class="flaticon-wifi-signal"></i></span>Broadband Postpaid</a></li>
<li><a href="#" data-toggle="modal" data-target="#landline-postpaid"><span class="recharge_icons"><i class="flaticon-telephone"></i></span>Landline Postpaid</a></li>
<li><a href="#" data-toggle="modal" data-target="#water"><span class="recharge_icons"><i class="flaticon-water-drop"></i></span>Water </a></li>
<li><a href="#" data-toggle="modal" data-target="#gas"><span class="recharge_icons"><i class="flaticon-fire"></i></span>Gas </a></li>
<li><a href="#" data-toggle="modal" data-target="#lpg-gas"><span class="recharge_icons"><i class="flaticon-gas"></i></span>LPG Gas</a></li>
<li><a href="#" data-toggle="modal" data-target="#loan"><span class="recharge_icons"><i class="flaticon-save-money"></i></span>Loan</a></li>
<li><a href="#" data-toggle="modal" data-target="#insurance"><span class="recharge_icons"><i class="flaticon-life-insurance"></i></span>Insurance</a></li>
<li><a href="#" data-toggle="modal" data-target="#fastag"><span class="recharge_icons"><i class="flaticon-toll-road"></i></span>Fastag</a></li>
<li><a href="#" data-toggle="modal" data-target="#cable"><span class="recharge_icons"><i class="flaticon-tv-screen"></i></span>Cable TV</a></li>
<!--<li><a href="#"><span class="recharge_icons"><i class="flaticon-life-insurance-1"></i></span>Health Insurance</a></li>-->
<li><a href="#" data-toggle="modal" data-target="#emi-payment"><span class="recharge_icons"><i class="flaticon-save-money"></i></span>EMI Payment</a></li>
<li><a href="#" data-toggle="modal" data-target="#municipal-taxes"><span class="recharge_icons"><i class="flaticon-tax"></i></span>Municipal Taxes</a></li>
<li><a href="#" data-toggle="modal" data-target="#municipal-services"><span class="recharge_icons"><i class="flaticon-apartment"></i></span>Municipal Services </a></li>
<!-- <li><a href="#" data-toggle="modal" data-target="#subscription"><span class="recharge_icons"><i class="flaticon-subscription"></i></span>Subscription</a></li> -->
<!-- <li><a href="#" data-toggle="modal" data-target="#hospital"><span class="recharge_icons"><i class="flaticon-hospital"></i></span>Hospital </a></li> -->
<li><a href="#" data-toggle="modal" data-target="#credit-card-mobi"><span class="recharge_icons"><i class="flaticon-credit-card"></i></span>Credit Card </a></li>
<!-- <li><a href="#" data-toggle="modal" data-target="#entertainment"><span class="recharge_icons"><i class="flaticon-cinema"></i></span>Entertainment</a></li>
<li><a href="#" data-toggle="modal" data-target="#travel"><span class="recharge_icons"><i class="flaticon-luggage"></i></span>Travel</a></li>
<li><a href="#" data-toggle="modal" data-target="#club"><span class="recharge_icons"><i class="flaticon-entrance"></i></span>Clubs And Associations</a></li> -->
</ul>
</div>
</div>
      </div>

      
      

    </div>

  </div>  


  </div> 

</div>


<div class="modal fade recharge_view" id="mobile-postpaid">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-mobile-postpaid-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Mobile Postpaid</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="mobilePostpaidOperator">
                       <option value="">Select Operator</option>
                       <?php if($mobilePostpaidBillerList){ ?>
                         <?php foreach($mobilePostpaidBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
               <!--  <div class="form-group">
                    <label>Mobile Number*</label>
                    <input class="form-control" name="number" id="mobile-postpaid-number" placeholder="Enter Mobile Number" type="text" />
                </div> -->
                 <!-- <div class="form-group" id="mobile-postpaid-fetch-block" style="display: none;"> -->
                  <div id="mobilepostpaid-form-block"></div>
                <div class="form-group" id="mobile-postpaid-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMobilePostpaidBill(); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="mobile-postpaid-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="mobile-postpaid-loader"></div>
                <div class="form-group">
                    <button class="procced-btn btn-primary" type="button" id="bbps-mobile-postpaid-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="electricity">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-electricity-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Electricity</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsElectricityOperator">
                       <option value="">Select Operator</option>
                       <?php if($electricityBillerList){ ?>
                         <?php foreach($electricityBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="electricity-form-block"></div>
                <div class="form-group" id="electricity-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchElectricityBill(); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="electricity-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="electricity-account-holder-name"></div>
                <div class="form-group" id="electricity-loader"></div>
                <div class="form-group" style="display: none;" id="electricity-submit-btn">
                    <button class="procced-btn btn-primary" type="button" id="bbps-electricity-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="dth">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-dth-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">DTH</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsDTHOperator">
                       <option value="">Select Operator</option>
                       <?php if($dthBillerList){ ?>
                         <?php foreach($dthBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="dth-form-block"></div>
                <div class="form-group" id="dth-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchDTHBill(); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="dth-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="dth-account-holder-name"></div>
                <div class="form-group" id="dth-loader"></div>
                <div class="form-group" style="display: none;" id="dth-submit-btn">
                    <button class="procced-btn btn-primary" type="button" id="bbps-dth-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="boradband-postpaid">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-boradband-postpaid-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Broadband Postpaid</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsBroadbandPostpaidOperator">
                       <option value="">Select Operator</option>
                       <?php if($boradbandPostpaidBillerList){ ?>
                         <?php foreach($boradbandPostpaidBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="boradband-postpaid-form-block"></div>
                <div class="form-group" id="boradband-postpaid-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(19); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="boradband-postpaid-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="boradband-postpaid-account-holder-name"></div>
                <div class="form-group" id="boradband-postpaid-loader"></div>
                <div class="form-group" style="display: none;" id="boradband-postpaid-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(19); return false;" type="button" id="bbps-boradband-postpaid-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>


<div class="modal fade recharge_view" id="landline-postpaid">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-landline-postpaid-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Landline Postpaid</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsLandlinePostpaidOperator">
                       <option value="">Select Operator</option>
                       <?php if($landlinePostpaidBillerList){ ?>
                         <?php foreach($landlinePostpaidBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="landline-postpaid-form-block"></div>
                <div class="form-group" id="landline-postpaid-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(2); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="landline-postpaid-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="landline-postpaid-account-holder-name"></div>
                <div class="form-group" id="landline-postpaid-loader"></div>
                <div class="form-group" style="display: none;" id="landline-postpaid-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(2); return false;" type="button" id="bbps-landline-postpaid-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="water">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-water-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Water</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsWaterOperator">
                       <option value="">Select Operator</option>
                       <?php if($waterBillerList){ ?>
                         <?php foreach($waterBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="water-form-block"></div>
                <div class="form-group" id="water-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(7); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="water-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="water-account-holder-name"></div>
                <div class="form-group" id="water-loader"></div>
                <div class="form-group" style="display: none;" id="water-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(7); return false;" type="button" id="bbps-water-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="gas">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-gas-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Gas</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsGasOperator">
                       <option value="">Select Operator</option>
                       <?php if($gasBillerList){ ?>
                         <?php foreach($gasBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="gas-form-block"></div>
                <div class="form-group" id="gas-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(6); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="gas-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="gas-account-holder-name"></div>
                <div class="form-group" id="gas-loader"></div>
                <div class="form-group" style="display: none;" id="gas-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(6); return false;" type="button" id="bbps-gas-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="lpg-gas">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-lpg-gas-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">LPG Gas</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsLPGGasOperator">
                       <option value="">Select Operator</option>
                       <?php if($lpgGasBillerList){ ?>
                         <?php foreach($lpgGasBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="lpg-gas-form-block"></div>
                <div class="form-group" id="lpg-gas-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(11); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="lpg-gas-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="lpg-gas-account-holder-name"></div>
                <div class="form-group" id="lpg-gas-loader"></div>
                <div class="form-group" style="display: none;" id="lpg-gas-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(11); return false;" type="button" id="bbps-lpg-gas-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="loan">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-loan-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Loan</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsLoanOperator">
                       <option value="">Select Operator</option>
                       <?php if($loanBillerList){ ?>
                         <?php foreach($loanBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="loan-form-block"></div>
                <div class="form-group" id="loan-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(17); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="loan-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="loan-account-holder-name"></div>
                <div class="form-group" id="loan-loader"></div>
                <div class="form-group" style="display: none;" id="loan-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(17); return false;" type="button" id="bbps-loan-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="insurance">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-insurance-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Insurance</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsInsuranceOperator">
                       <option value="">Select Operator</option>
                       <?php if($insuranceBillerList){ ?>
                         <?php foreach($insuranceBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="insurance-form-block"></div>
                <div class="form-group" id="insurance-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(5); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="insurance-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="insurance-account-holder-name"></div>
                <div class="form-group" id="insurance-loader"></div>
                <div class="form-group" style="display: none;" id="insurance-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(5); return false;" type="button" id="bbps-insurance-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="fastag">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-fastag-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Fastag</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsFastagOperator">
                       <option value="">Select Operator</option>
                       <?php if($fastagBillerList){ ?>
                         <?php foreach($fastagBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="fastag-form-block"></div>
                <div class="form-group" id="fastag-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(12); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="fastag-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="fastag-account-holder-name"></div>
                <div class="form-group" id="fastag-loader"></div>
                <div class="form-group" style="display: none;" id="fastag-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(12); return false;" type="button" id="bbps-fastag-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="cable">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-cable-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cable TV</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsCableOperator">
                       <option value="">Select Operator</option>
                       <?php if($cableBillerList){ ?>
                         <?php foreach($cableBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="cable-form-block"></div>
                <div class="form-group" id="cable-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(9); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="cable-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="cable-account-holder-name"></div>
                <div class="form-group" id="cable-loader"></div>
                <div class="form-group" style="display: none;" id="cable-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(9); return false;" type="button" id="bbps-cable-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="emi-payment">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-emi-payment-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">EMI PAYMENT</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsEmiPaymentOperator">
                       <option value="">Select Operator</option>
                       <?php if($emiPaymentBillerList){ ?>
                         <?php foreach($emiPaymentBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="emi-form-block"></div>
                <div class="form-group" id="emi-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(10); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="emi-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="emi-account-holder-name"></div>
                <div class="form-group" id="emi-loader"></div>
                <div class="form-group" style="display: none;" id="emi-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(10); return false;" type="button" id="emi-payment-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="municipal-taxes">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-municipal-taxes-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Municipal Taxes</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsMunicipalTaxesOperator">
                       <option value="">Select Operator</option>
                       <?php if($municipalTaxesBillerList){ ?>
                         <?php foreach($municipalTaxesBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="municipal-taxes-form-block"></div>
                <div class="form-group" id="municipal-taxes-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(18); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="municipal-taxes-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="municipal-taxes-account-holder-name"></div>
                <div class="form-group" id="municipal-taxes-loader"></div>
                <div class="form-group" style="display: none;" id="municipal-taxes-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(18); return false;" type="button" id="bbps-municipal-taxes-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="municipal-services">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-municipal-services-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Municipal Services</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsMunicipalServicesOperator">
                       <option value="">Select Operator</option>
                       <?php if($municipalServicesBillerList){ ?>
                         <?php foreach($municipalServicesBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="municipal-services-form-block"></div>
                <div class="form-group" id="municipal-services-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(13); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="municipal-services-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="municipal-services-account-holder-name"></div>
                <div class="form-group" id="municipal-services-loader"></div>
                <div class="form-group" style="display: none;" id="municipal-services-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(13); return false;" type="button" id="bbps-municipal-services-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="subscription">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-subscription-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Subscription</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsSubscriptionOperator">
                       <option value="">Select Operator</option>
                       <?php if($subscriptionBillerList){ ?>
                         <?php foreach($subscriptionBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="subscription-form-block"></div>
                <div class="form-group" id="subscription-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(20); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="subscription-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="subscription-account-holder-name"></div>
                <div class="form-group" id="subscription-loader"></div>
                <div class="form-group" style="display: none;" id="subscription-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(20); return false;" type="button" id="bbps-subscription-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="hospital">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-hospital-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Hospital</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsHospitalOperator">
                       <option value="">Select Operator</option>
                       <?php if($hospitalBillerList){ ?>
                         <?php foreach($hospitalBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="hospital-form-block"></div>
                <div class="form-group" id="hospital-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(19); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="hospital-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="hospital-account-holder-name"></div>
                <div class="form-group" id="hospital-loader"></div>
                <div class="form-group" style="display: none;" id="hospital-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(19); return false;" type="button" id="bbps-hospital-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- <div class="modal fade recharge_view" id="credit-card">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-credit-card-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Credit Card</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsCreditCardOperator">
                       <option value="">Select Operator</option>
                       <?php if($creditCardBillerList){ ?>
                         <?php foreach($creditCardBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="credit-card-form-block"></div>
                <div class="form-group" id="credit-card-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(22); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="credit-card-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="credit-card-account-holder-name"></div>
                <div class="form-group" id="credit-card-loader"></div>
                <div class="form-group" style="display: none;" id="credit-card-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(22); return false;" type="button" id="bbps-credit-card-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div> -->
  

  <div class="modal fade recharge_view" id="credit-card-mobi">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-credit-card-mobi-form')); ?>
       <input class="form-control" name="operator_id" type="hidden" value="604" />
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Credit Card</h4>
                <!-- <img src="{site_url}skin/images/BBPS_Logo.png" class="Dashboard_bbps"> -->
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               
               <div class="form-group">
                <input type="text" name="canumber" class="form-control" placeholder="Credit Card Number">
               </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount"placeholder="Enter Amount (Min - 100)" type="text" />
                </div>
                <div class="form-group" id="mobi-credit-card-loader"></div>
                <div class="form-group"id="credit-card-submit-btn">
                    <button class="btn btn-primary" onclick="payCreditCardBill(22); return false;" type="button" id="bbps-credit-card-mobi-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
    
<div class="modal fade recharge_view" id="entertainment">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-entertainment-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Entertainment</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsEntertainmentOperator">
                       <option value="">Select Operator</option>
                       <?php if($entertainmentBillerList){ ?>
                         <?php foreach($entertainmentBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="entertainment-form-block"></div>
                <div class="form-group" id="entertainment-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(9); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="entertainment-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="entertainment-account-holder-name"></div>
                <div class="form-group" id="entertainment-loader"></div>
                <div class="form-group" style="display: none;" id="entertainment-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(9); return false;" type="button" id="bbps-entertainment-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="travel">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-travel-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Travel</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsTravelOperator">
                       <option value="">Select Operator</option>
                       <?php if($travelBillerList){ ?>
                         <?php foreach($travelBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="travel-form-block"></div>
                <div class="form-group" id="travel-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(21); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="travel-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="travel-account-holder-name"></div>
                <div class="form-group" id="travel-loader"></div>
                <div class="form-group" style="display: none;" id="travel-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(21); return false;" type="button" id="bbps-travel-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade recharge_view" id="club">
    <div class="modal-dialog">
      <?php echo form_open('#',array('id'=>'bbps-club-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Club & Associations</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
               <div class="recharge_form">
                <div class="form-group">
                    <label>Operator*</label>
                    <select class="form-control" name="billerID" id="bbpsClubOperator">
                       <option value="">Select Operator</option>
                       <?php if($clubBillerList){ ?>
                         <?php foreach($clubBillerList as $bList){ ?>
                          <option value="<?php echo $bList['id']; ?>"><?php echo $bList['billerName']; ?></option>
                         <?php } ?>
                       <?php } ?>
                       
                   </select>
                </div>
                <div id="club-form-block"></div>
                <div class="form-group" id="club-fetch-block" style="display: none;">
                  <a href="#" onclick="fetchMasterBill(24); return false;">Fetch & View Bill</a>
                </div>
                <div class="form-group">
                    <label>Amount*</label>
                     <input class="form-control" name="amount" id="club-amount" placeholder="Enter Amount" type="text" />
                </div>
                <div class="form-group" id="club-account-holder-name"></div>
                <div class="form-group" id="club-loader"></div>
                <div class="form-group" style="display: none;" id="club-submit-btn">
                    <button class="procced-btn btn-primary" onclick="payMasterBill(24); return false;" type="button" id="bbps-club-btn"> Proceed Recharge</button>
                </div>
            
               </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>