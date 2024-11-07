<div class="container-fluid">
{system_message}    
{system_info}
<div class="row">
  <div class="col-sm-7">
<div class="card_form_section card shadow  mb-3">
            
                <div class="recent_card-logo">
     <img src="https://www.payol.in/media/account/677363070.png" class="img-fluid logo_card_top">
       </div>
           
            <div class="card-body">
            <?php echo form_open_multipart('retailer/transfer/transferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="m-auto col-sm-6">
                  <div class="card_check_bank">
                  <div class="card_logo_bank text-center mb-3">
                  <img src="{site_url}skin/admin/img/bob_logo.png" class="img-fluid">    
                  </div>
                  
               <div class="form-group text-center">
              <label><b>Confirm Cash Withdrawal</b></label>
              <input type="text" class="form-control" name="mobile" placeholder="1000" value="1000">
              </div></div>
                </div>
                
                <div class="m-auto col-lg-9">
               <div class="card_list_content">
               <ul>
               <li>Mobile Number <span class="list_text">+91-9876543210</span></li>
               <li>Aadhaar Number <span class="list_text">12345678 9012</span></li>
                 </ul>  
               </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="card_formlist_btn text-center">
                    <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
                     <button type="button" class="btn btn-success" onclick="CallCapture();">Scan & Submit</button>
                </div></div>
             </div>
            
          </div>
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




