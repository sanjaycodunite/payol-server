<div class="container-fluid">
{system_message}    
{system_info}
<div class="row">
  <div class="col-sm-7">
<div class="card_form_section card shadow  mb-3">
            <div class="recent_card-header">
    <h3><b>Aadhaar Based Authentication</b></h3>
       </div>
           
            <div class="card-body">
            <div class="card_aeps_details text-center">
             <h6>As Per Regulatory Guidelines, This Is Mandatory For Aadhaar  Related Services, Confirm Your Identify Daily For Once.</h6> 
             <p>नियामक दिशानिर्देशों के अनुसार, आधार संबंधी सेवाओं के लिए यह अनिवार्य है, प्रतिदिन एक बार अपनी पहचान की पुष्टि करें।</p>
                </div>
                
                <div class="recent_card-header mb-3">
                <h5>Benefits</h5>
               </div>
               
               <div class="aeps_secure_list">
                   <div class="aeps_secure_box">
                  <div class="aeps_secure_colm">
                    <img src="{site_url}skin/admin/img/icons/increase-security.png" class="img-fluid"> 
                  </div>
                  <h5>Increased<br/> Security</h5></div>
                   <div class="aeps_secure_box">
                  <div class="aeps_secure_colm">
                    <img src="{site_url}skin/admin/img/icons/secured-transactions.png" class="img-fluid"> 
                  </div> 
                  <h5>Secured <br/>Transaction</h5></div>
                  <div class="aeps_secure_box">
                  <div class="aeps_secure_colm">
                    <img src="{site_url}skin/admin/img/icons/reduce-fraud.png" class="img-fluid"> 
                  </div> 
                  <h5>Reduced Fraud <br/>Cases</h5></div>
               </div>
                
            
            
          </div>
        </div>
        
        <div class="card_form_section card shadow  mb-3">
            <div class="card-body">
             <?php echo form_open_multipart('retailer/transfer/transferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="card_aeps_form">
              <div class="row">
              <div class="col-sm-8">
               <div class="form-group">
              <label class="aeps_aadhar_label"><b>Enter Aadhaar Number Of <span class="label_titile">Rajesh Kumar Saw</span></b></label>
              <input type="text" class="form-control" name="mobile" placeholder="Adhikari Aadhaar Number">
              </div>
              
               <div class="form-check_list">
              <label class="custom_check">
			 <input type="checkbox" name="rememberme" class="rememberme">
			<span class="checkmark"></span>I Accept the <a href="#">Aadhaar Consent</a> </label>    
              </div>
              
               <div class="form-list_grid">
              <div class="form_select_option">
               <div class="form-c">
               <select id="social" class="select-opt form-group"> 
               <option value="Mantra"> Mantra</option>  
               <option value="Morpho">Morpho</option>  
               </select> 
               </div>  
              </div>
              
               <div class="form_btn_option">
                    <button type="button" class="btn btn-success" onclick="CallCapture();">Scan Finger</button>
               </div>
              </div>
              
                </div>
                
                
                 <div class="col-sm-4">
                <div class="aeps_finger_colm mb-3">
                    <div class="card_finger_print card-body">
                      <img src="{site_url}skin/admin/img/icons/fingerprint.png" class="img-fluid">    
                    </div>
                </div>
                </div>
              
                
               
             </div>   </div>
         </div></div>
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



<style>
 

</style>


