<div class="container-fluid">
    <div class="row">
{system_message}    
{system_info}

<div class="col-lg-7 col-md-6">
<div class="card shadow">
            <div class="recent_card-header">
                 <h3><b>AEPS Two Factor Registration</b></h3>
              </div>
              
            <div class="card-body">
            <?php echo form_open_multipart('retailer/transfer/transferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
            
             <div class="row">
            <div class="col-lg-12">
                 <div class="card_body_colm text-center">
                     <h5>Device</h5> 
                 
                 <div class="card_listCol">
                <div class="list_card_li">
                <input type="radio" class="input_List" name="serviceType" id="deviceType1" value="MANTRA_PROTOBUF" checked="">
              <label for="deviceType1" class="list_label">
             <img src="{site_url}skin/admin/img/icons/devise2.png" class="img-fluid"> 
             </label>
            <h5>Mantra</h5>
              </div>
              
                 <div class="list_card_li">
                <input type="radio" class="input_List" name="serviceType" id="deviceType2" value="MORPHO_PROTOBUF">
              <label for="deviceType2" class="list_label">
             <img src="{site_url}skin/admin/img/icons/device1.png" class="img-fluid"> 
            </label>
             <h5>Morpho</h5>
            </div>
              
               </div> 
              </div></div>
              
             <!--    
              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="deviceType" id="deviceType1" value="MANTRA_PROTOBUF">
                  <label for="deviceType1"><b>Mantra</b></label>
                </div>
              </div>

           
          <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" checked="checked" name="deviceType" id="deviceType2" value="MORPHO_PROTOBUF"  <?php if($account_id == 3) {?> checked="" <?php } ?>>
                  <label for="deviceType2"><b>Morpho</b></label>
                </div>
              </div> -->


              
            </div>
            
            <div class="row">
            <div class="m-auto col-lg-9">
            <div class="card_aeps_form">
            <div class="row">
             <div class="col-sm-12">
              <div class="form-group">
              <label><b>Mobile No.*</b></label>
              <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile No." maxlength="10" value="<?php echo $get_member_data['mobile'] ?>" readonly>
              
              </div>
              </div>
              <div class="col-sm-12">
              <div class="form-group">
              <label><b>Aadhar No*</b></label>
              <input type="text" class="form-control" name="aadhar_no" id="aadhar_no" placeholder="Aadhar No" maxlength="12" value="<?php echo $get_member_data['aadhar_no'] ?>" readonly>
              
              </div>
              </div>
              
               <div class="m-auto col-sm-6">
                    <div class="card_formlist_btn form-group text-center">
                    <button type="button" class="btn btn-secondary" onclick="CallMemberCapture();">Scan & Submit</button>
              </div></div>
              
              </div>
              </div>

             </div> </div>
              
          </div>
        </div>
         
 <?php echo form_close(); ?>     
    </div>
    
    
    <div class="col-lg-5 col-md-6">
     <div class="card shadow mb-3">
       <div class="card-body text-center">
           <div class="card_services_img"><img src="{site_url}skin/admin/img/icons/device1.png" class="img-fluid"></div>
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
    
    
    </div>
    
    </div>
  </div>
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




