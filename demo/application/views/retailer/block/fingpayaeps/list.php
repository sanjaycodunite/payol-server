<div class="container-fluid">
{system_message}    
{system_info}
<div class="row">
  <div class="col-sm-7">
<div class="card_form_section card shadow  mb-3">
            
                <div class="recent_card-header">
      <h3><b>AEPS</b></h3>  
       </div>
           
            <div class="card-body">
            <?php echo form_open_multipart('retailer/transfer/transferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-12">
                  <div class="recent_card-header mb-3">
                <h5>Service Type</h5>
               </div>
              </div>
              
              <div class="col-lg-12">
                  <div class="aeps_list_colms card_listCol">
                <div class="list_card_li">
                <input type="radio" class="input_List" name="serviceType" id="serviceType1" value="balinfo" checked>
              <label for="serviceType1" class="list_label">
             <img src="https://www.payol.in/demo/skin/admin/img/icons/balance-enquiry.png" class="img-fluid"> 
             </label>
            <h5>Balance Enquiry</h5>
              </div>
              
                 <div class="list_card_li">
                <input type="radio" class="input_List" name="serviceType" id="Statement" value="balinfo">
              <label for="Statement" class="list_label">
             <img src="https://www.payol.in/demo/skin/admin/img/icons/mini_statement.png" class="img-fluid"> 
            </label>
             <h5>Mini Statement</h5>
            </div>
              
                 <div class="list_card_li">
                <input type="radio" class="input_List" name="serviceType" id="Withdrawal" value="balinfo">
              <label for="Withdrawal" class="list_label">
             <img src="https://www.payol.in/demo/skin/admin/img/icons/withdrawal.png" class="img-fluid"> 
             </label>
              <h5>Withdrawal</h5>
           </div>
           
             <div class="list_card_li">
                <input type="radio" class="input_List" name="serviceType" id="aadhar" value="balinfo">
              <label for="aadhar" class="list_label">
             <img src="https://www.payol.in/demo/skin/admin/img/icons/aadhar-pay.png" class="img-fluid"> 
             </label>
              <h5>Aadhar Pay</h5>
           </div>
              
                  </div>  
              </div>
              
			 <!--    <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="serviceType" id="serviceType1" value="balinfo">
                  <label for="serviceType1"><b>Balance Enquiry</b></label>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="serviceType" id="serviceType2" value="ministatement">
                  <label for="serviceType2"><b>Mini Statement</b></label>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="serviceType" id="serviceType3" value="balwithdraw">
                  <label for="serviceType3"><b>Withdrawal</b></label>
                </div>
              </div> -->

              <!--<div class="col-sm-3">-->
              <!--  <div class="form-group">-->
              <!--    <input type="radio" name="serviceType" id="serviceType4" value="aadharpay">-->
              <!--    <label for="serviceType4"><b>Aadhar Pay</b></label>-->
              <!--  </div>-->
              <!--</div>-->
              
            </div>
            
          

              
              
          </div>
        </div>
        
        
        <div class="card_form_section card shadow mb-3">
            <div class="card-body">
             <div class="row">
                  <div class="col-sm-12">
                  <div class="recent_card-header mb-4">
                <h5>Select Device</h5>
               </div>
              </div>
                <div class="col-sm-12">
               <div class="card_body_colm">
               <div class="card_listCol">
                <div class="list_card_li">
                <input type="radio" class="input_List" name="serviceTypes" id="deviceType1" value="MANTRA_PROTOBUF" checked="">
              <label for="deviceType1" class="list_label">
             <img src="{site_url}skin/admin/img/icons/devise2.png" class="img-fluid"> 
             </label>
            <h5>Mantra</h5>
              </div>
              
                 <div class="list_card_li">
                <input type="radio" class="input_List" name="serviceTypes" id="deviceType2" value="MORPHO_PROTOBUF">
              <label for="deviceType2" class="list_label">
             <img src="{site_url}skin/admin/img/icons/device1.png" class="img-fluid"> 
            </label>
             <h5>Morpho</h5>
            </div>
              
               </div> </div>  
            </div>  
              

              </div>
            </div>
            
        </div>
        
        <div class="card_form_section card shadow">
            <div class="card-body ">
                <div class="card_aeps_form">
              <div class="row">
            
              <div class="col-sm-6">
              <div class="form-group">
              <label><b>Mobile No.*</b></label>
              <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile No.">
              
              </div>
              </div>
              <div class="col-sm-6">
              <div class="form-group">
              <label><b>Aadhar No*</b></label>
              <input type="text" class="form-control" name="aadhar_no" id="aadhar_no" placeholder="Aadhar No">
              
              </div>
              </div>
			         <div class="col-sm-6">
              <div class="form-group">
              <label><b>Amount*</b></label>
              <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount (In Numbers Only)" value="0">
              
              </div>
              </div>

              <div class="col-sm-6">
              <div class="form-group">
              <label><b>Bank*</b></label>
              <select class="form-control selectpicker" name="bankID" id="bankID" data-live-search="true">
                <option value="">Select Bank</option>
                <?php if($bankList){ ?>
                  <?php foreach($bankList as $list){ ?>
                    <option value="<?php echo $list['iinno']; ?>"><?php echo $list['bank_name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              
              </div>
              </div>
              
               <div class="col-sm-12">
              <div class="form-check_list">
              <label class="custom_check">
			 <input type="checkbox" name="rememberme" class="rememberme">
			<span class="checkmark"></span>I Accept the <a href="#">Aadhaar Consent</a> | Read <a href="#">AEPS Advisory</a></label>    
              </div>
              </div> 
              
               <div class="col-sm-12">
              <div class="form-check_lista">
               <a href="#"><i class="fa fa-download"></i> Download Divyangan Declaration Form</a>
               </div></div>
              
               <div class="m-auto col-sm-6">
                  <div class="card_formlist_btn form-group text-center mt-3">
                  <button type="button" class="btn btn-success" onclick="CallCapture();">Submit</button>
              </div> </div>

              </div>
              </div>    
            </div>
            
      <!-- <div class="card-header py-3 text-right">
        <button type="button" class="btn btn-success" onclick="CallCapture();">Scan & Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div> -->   
        </div>    
 <?php echo form_close(); ?>     
    </div>
    
<div class="col-lg-5 col-md-6">
     <div class="card shadow mb-3">
       <div class="card-body text-center">
           <div class="card_services_img"><img src="{site_url}skin/admin/img/transaction.png" class="img-fluid"></div>
       </div>  
     </div>  
     
     <div class="card_service_bg card shadow mb-3">
       <div class="card-header_top"><h4>Important Note</h4></div>
       <div class="card-body">
           <div class="card_services_list">
              <ol class="list_service_note">
                <li>.INR 5.00 will be charged for IMPS Transactions (up to 25000).</li>
                <li>.INR 10.00 will be charged for IMPS Transactions (above 25000).</li>
                <li>.INR 3.54 will be charged for NEFT Transactions (any amount).</li>
                <li>.INR x.xx will be charged for XXXX Transactions (any amount).</li>
                <li>.INR x.xx will be charged for XXXX Transactions (any amount).</li>
              </ol> 
           </div>
       </div>  
     </div>  
     
    </div>  
    
</div> </div> </div>
<!-- Modal -->
<div id="aepsResponseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">AEPS Response</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body aeps-response">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" width="100%" cellspacing="0">
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>CR/DR</th>
              <th>Amount</th>
              <th>Description</th>
            </tr>
          </table>
        </div>
      </div>
      
    </div>

  </div>
</div>




