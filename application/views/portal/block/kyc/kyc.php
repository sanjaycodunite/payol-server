<div class="container-fluid">
  {system_message}    
{system_info}

  <div class="row">
<style type="text/css">
   .nav.nav-pills.Card_nav_tabs .nav-link {
    background: #fff6f6;
    border-radius: 5px;
    border: 1px solid #f6b3b3;
    margin: 0 5px;
    padding: 5px 15px;
}
.nav.nav-pills.Card_nav_tabs .nav-link.active {
    background: linear-gradient(86deg, #ff1616, #000);
    color: #fff;
}
.nav.nav-pills.Card_nav_tabs {
    border-bottom: 1px solid #f6b3b3;
    padding-bottom: 10px;
    margin: 10px 0;
}

.kyc_verify {
    position: absolute;
    right: 6px;
    top: 6px;
}
.kyc_verify button.btn.btn-success {
    padding: 5px 10px;
    font-size: 10px;
}
</style>
  <div class="col-sm-12">
     <?php echo form_open_multipart('portal/kyc/updateKyc', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
 <!-- Start KYC Details -->   
<div class="flight_card_col card mb-3">
 <div class="card-header">
   <ul class="nav nav-pills Card_nav_tabs" id="pills-tab" role="tablist">
    <li  class="active" class="nav-item">
      <a class="nav-link active" id="pills1" data-toggle="pill" href="#basicTab" role="tab" aria-controls="basicTab" aria-selected="true">Basic Details</a></li>
      <li class="nav-item">
      <a class="nav-link" id="pills2" data-toggle="pill" href="#businessTab" role="tab" aria-controls="businessTab" aria-selected="true">Business Details</a></li>
      <li class="nav-item">
     <a class="nav-link" id="pills3" data-toggle="pill" href="#SignatoryTab" role="tab" aria-controls="SignatoryTab" aria-selected="true">Signatory Details</a></li>
     <li class="nav-item">
       <a class="nav-link" id="pills4" data-toggle="pill" href="#bankTab" role="tab" aria-controls="bankTab"  aria-selected="true">Bank Details</a></li>
       <li class="nav-item">
       <a class="nav-link" id="pills5" data-toggle="pill" href="#kycTab" role="tab" aria-controls="kycTab"  aria-selected="true">Upload Document</a></li>
     </ul>
 </div> 
<div class="card-body">
<div class="tab-content" id="pills-tabContent">
 <div id="basicTab" class="tab-pane fade show active" role="tabpanel" labelledby="pills1">
<div class="row">
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>User ID:</label>
      <input type="text" name="userid" value="<?php echo $loggedUser['user_code'] ?>" class="form-control" placeholder="Enter Your User ID" readonly>
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Name:</label>
      <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo set_value('name') ?><?php echo isset($chk_status['name']) ? $chk_status['name'] : ''; ?>">
              <?php echo form_error('name', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Mobile Number:</label>
       <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Mobile" value="<?php echo set_value('mobile') ?><?php echo isset($chk_status['mobile']) ? $chk_status['mobile'] : ''; ?>">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Email ID:</label>
       <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo set_value('email') ?><?php echo isset($chk_status['email']) ? $chk_status['email'] : ''; ?>">
              <?php echo form_error('email', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>PIN Code:</label>
       <input type="text" class="form-control" name="pincode"  placeholder="Pincode" value="<?php echo set_value('pincode') ?><?php echo isset($chk_status['pincode']) ? $chk_status['pincode'] : ''; ?>">
              <?php echo form_error('pincode', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Block:</label>
       <input type="text" class="form-control" name="block"  placeholder="Block" value="<?php echo set_value('block') ?><?php echo isset($chk_status['block']) ? $chk_status['block'] : ''; ?>">
              <?php echo form_error('block', '<div class="error">', '</div>'); ?> 

    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Village:</label>
       <input type="text" class="form-control" name="village"  placeholder="Village" value="<?php echo set_value('village') ?><?php echo isset($chk_status['village']) ? $chk_status['village'] : ''; ?>">
              <?php echo form_error('village', '<div class="error">', '</div>'); ?> 
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      
     <div class="form-group">
              <label>State*</label>
              <select class="form-control" name="state_id">
              <option value="">Select State</option>
              <?php if($stateList){ ?>
                <?php foreach($stateList as $list){ ?>
                  <option value="<?php echo $list['id']; ?>" <?php if($chk_status['state'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['name']; ?></option>
                <?php } ?>
              <?php } ?>
              </select>
              <?php echo form_error('state_id', '<div class="error">', '</div>'); ?>  
              </div>

    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>City:</label>
       <input type="text" class="form-control" name="city"  placeholder="City" value="<?php echo set_value('city') ?><?php echo isset($chk_status['city']) ? $chk_status['city'] : ''; ?>">
              <?php echo form_error('city', '<div class="error">', '</div>'); ?> 
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>District:</label>
       <input type="text" class="form-control" name="district"  placeholder="District" value="<?php echo set_value('district') ?><?php echo isset($chk_status['district']) ? $chk_status['district'] : ''; ?>">
              <?php echo form_error('district', '<div class="error">', '</div>'); ?> 
    </div>
  </div>
   

  <div class="col-lg-12 col-md-12">
    <div class="form-group text-right">
     <button type="button" class="btn btn-secondary btn-next" data-to="#pills2">Next</button>
    </div>
  </div>

</div>
</div>



<div id="businessTab" class="tab-pane fade" role="tabpanel"  labelledby="pills2">
<div class="row">
 <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Business Type:</label>
     <select class="form-control" name="business_type">
      <option value="">Select Business Type</option>
        <option value="1" <?php if($chk_status['business_type'] ==1) {?> selected="selected" <?php } ?>>Proprietor</option>
        <option value="2" <?php if($chk_status['business_type'] ==2) {?> selected="selected" <?php } ?>>Partnership</option>
        <option value="3" <?php if($chk_status['business_type'] ==3) {?> selected="selected" <?php } ?>>Private Limited</option>
        <option value="4" <?php if($chk_status['business_type'] ==4) {?> selected="selected" <?php } ?>>Public Limited</option>
       </select>
       <?php echo form_error('business_type', '<div class="error">', '</div>'); ?>  
    </div>
  </div>

   <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Business Industry:</label>
     <input type="text" class="form-control" name="business_industry"  placeholder="Business Industry" value="<?php echo set_value('business_industry') ?><?php echo isset($chk_status['business_industry']) ? $chk_status['business_industry'] : ''; ?>">
              <?php echo form_error('business_industry', '<div class="error">', '</div>'); ?> 
    </div>
  </div>

  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Business Address:</label>
     <input type="text" class="form-control" name="business_address"  placeholder="Business Address" value="<?php echo set_value('business_address') ?><?php echo isset($chk_status['business_address']) ? $chk_status['business_address'] : ''; ?>">
              <?php echo form_error('business_address', '<div class="error">', '</div>'); ?> 
    </div>
  </div>


  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Business Website:</label>
      <input type="text" class="form-control" name="business_website"  placeholder="Business Website" value="<?php echo set_value('business_website') ?><?php echo isset($chk_status['business_website']) ? $chk_status['business_website'] : ''; ?>">
        <?php echo form_error('business_website', '<div class="error">', '</div>'); ?> 
    </div>
  </div>

   <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Business Email:</label>
      <input type="text" class="form-control" name="business_email"  placeholder="Business Email" value="<?php echo set_value('business_email') ?><?php echo isset($chk_status['business_email']) ? $chk_status['business_email'] : ''; ?>">
              <?php echo form_error('business_email', '<div class="error">', '</div>'); ?> 
    </div>
  </div>

  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Service Name:</label>
      <input type="text" class="form-control" name="service_name"  placeholder="Service Name" value="<?php echo set_value('service_name') ?><?php echo isset($chk_status['service_name']) ? $chk_status['service_name'] : ''; ?>">
              <?php echo form_error('service_name', '<div class="error">', '</div>'); ?> 
    </div>
  </div>

  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Use Case:</label>
      <input type="text" class="form-control" name="use_case"  placeholder="Use Case" value="<?php echo set_value('use_case') ?><?php echo isset($chk_status['use_case']) ? $chk_status['use_case'] : ''; ?>">
              <?php echo form_error('use_case', '<div class="error">', '</div>'); ?> 
    </div>
  </div>


  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Business Proof:</label>
     <select class="form-control" name="business_proof">
      <option value="">Select Business Proof</option>
        <option value="1" <?php if($chk_status['business_proof'] ==1) {?> selected="selected" <?php } ?>>FSSAI Certificate</option>
        <option value="2" <?php if($chk_status['business_proof'] ==2) {?> selected="selected" <?php } ?>>Udhyam Certificate</option>
        <option value="3" <?php if($chk_status['business_proof'] ==3) {?> selected="selected" <?php } ?>>Pan Card</option>
        <option value="4" <?php if($chk_status['business_proof'] ==4) {?> selected="selected" <?php } ?>>Certificate of Incorporation</option>
        <option value="5" <?php if($chk_status['business_proof'] ==5) {?> selected="selected" <?php } ?>>GST Certificate</option>
       </select>
    </div>
  </div>

  

  <div class="col-lg-12 col-md-12">
    <div class="form-group text-right">
     <button type="button" class="btn btn-secondary btn-next" data-to="#pills3">Next</button>
    </div>
  </div>

</div>
</div>


<div id="SignatoryTab" class="tab-pane fade" role="tabpanel"  labelledby="pills3">
<div class="row">
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Signatory Full Name:</label>
       <input type="text" class="form-control" name="signatory_name"  placeholder="Signatory Name" value="<?php echo set_value('signatory_name') ?><?php echo isset($chk_status['signatory_name']) ? $chk_status['signatory_name'] : ''; ?>">
              <?php echo form_error('signatory_name', '<div class="error">', '</div>'); ?> 
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Mobile No.:</label>
       <input type="text" class="form-control" name="signatory_mobile"  placeholder="Signatory Mobile" value="<?php echo set_value('signatory_mobile') ?><?php echo isset($chk_status['signatory_mobile']) ? $chk_status['signatory_mobile'] : ''; ?>">
              <?php echo form_error('signatory_mobile', '<div class="error">', '</div>'); ?> 
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Aadhar No.:</label>
      <div class="position-relative">
     <input type="text" class="form-control" name="signatory_aadhar"  placeholder="Signatory Aadhar" value="<?php echo set_value('signatory_aadhar') ?><?php echo isset($chk_status['signatory_aadhar']) ? $chk_status['signatory_aadhar'] : ''; ?>" id="signatory_aadhar">
              <?php echo form_error('signatory_aadhar', '<div class="error">', '</div>'); ?> 
    <div class="kyc_verify"><button class="btn btn-success" id="aadhar_verify"  type="button">Verify</button></div>
  </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Pan No.:</label>
      <div class="position-relative">    
     <input type="text" class="form-control" name="pan_no"  placeholder="Pan Number" value="<?php echo set_value('pancard_number') ?><?php echo isset($chk_status['pancard_number']) ? $chk_status['pancard_number'] : ''; ?>" id="pan_no">
              <?php echo form_error('pancard_number', '<div class="error">', '</div>'); ?> 
    <div class="kyc_verify"><button class="btn btn-success" id="pan_verify" type="button">Veriy</button></div>
  </div>
  </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Full Address:</label>
       <textarea name="address" class="form-control" rows="4" placeholder="Please Enter Same As Aadhar Card  Back Address"><?php echo set_value('address') ?><?php echo isset($chk_status['address']) ? $chk_status['address'] : ''; ?></textarea>
                  <?php echo form_error('address', '<div class="error">', '</div>'); ?>
    </div>
  </div>
 
 
  <div class="col-lg-12 col-md-12">
    <div class="form-group text-right">
     <button type="button" class="btn btn-secondary btn-next" data-to="#pills4">Next</button>
    </div>
  </div>

</div>
</div> 



<div id="bankTab" class="tab-pane fade" role="tabpanel"  labelledby="pills4">
<div class="row">
    <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Bank Name:</label>
      <input type="text" class="form-control" name="bank_name"  placeholder="Bank Name" value="<?php echo set_value('bank_name') ?> <?php echo isset($chk_status['bank_name']) ? $chk_status['bank_name'] : ''; ?>">
              <?php echo form_error('bank_name', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
   <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Account Holder Name:</label>
      <input type="text" class="form-control" name="account_holder_name"  placeholder="Account Holder Name" value="<?php echo set_value('account_holder_name') ?><?php echo isset($chk_status['account_holder_name']) ? $chk_status['account_holder_name'] : ''; ?>">
              <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
 <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Account Number:</label>
     <input type="text" class="form-control" name="bank_account_number"  placeholder="Bank Account Number" value="<?php echo set_value('bank_account_number') ?><?php echo isset($chk_status['bank_account_number']) ? $chk_status['bank_account_number'] : ''; ?>">
              <?php echo form_error('bank_account_number', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
   <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>IFSC Code:</label>
      <input type="text" class="form-control" name="ifsc_code"  placeholder="IFSC Code" value="<?php echo set_value('ifsc_code') ?><?php echo isset($chk_status['ifsc_code']) ? $chk_status['ifsc_code'] : ''; ?>">
              <?php echo form_error('ifsc_code', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Branch Name:</label>     
     <input type="text" class="form-control" name="branch"  placeholder="Branch" value="<?php echo set_value('branch') ?><?php echo isset($chk_status['branch']) ? $chk_status['branch'] : ''; ?>">
              <?php echo form_error('branch', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
  
 
  <div class="col-lg-12 col-md-12">
    <div class="form-group text-right">
     <button type="button" class="btn btn-secondary btn-next" data-to="#pills5">Next</button>
    </div>
  </div>

</div>
</div>


<div id="kycTab" class="tab-pane fade" role="tabpanel"  labelledby="pills5">
<div class="row">
 <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Signatory Aadhar Card:</label>
         <input type="file" name="profile" id="profile">
            <?php echo form_error('profile', '<div class="error">', '</div>'); ?>  
                  <p>Only PDF,JPG,PNG allowed</p>
                  <?php if($chk_status['signatory_aadhar_image']){ ?>
              <img src="<?php echo base_url($chk_status['signatory_aadhar_image']); ?>" width="100" />
              <?php } ?>

   </div>
  </div>

  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Signatory Pan Card:</label>
      <input type="file" name="profile2">
            <?php echo form_error('profile2', '<div class="error">', '</div>'); ?> 
                  <p>Only PDF,JPG,PNG allowed</p>
                  <?php if($chk_status['signatory_pan_image']){ ?>
              <img src="<?php echo base_url($chk_status['signatory_pan_image']); ?>" width="100" />
              <?php } ?>
    </div>
  </div>

  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Signatory Live Photo Captured by GPS Camera:</label>
      <input type="file" name="profile3">
             <?php echo form_error('profile3', '<div class="error">', '</div>'); ?> 
                  <p>Only PDF,JPG,PNG allowed</p>
                  <?php if($chk_status['signatory_live_image']){ ?>
              <img src="<?php echo base_url($chk_status['signatory_live_image']); ?>" width="100" />
              <?php } ?>
    </div>
  </div>

   <div class="col-lg-6 col-md-6">
    <div class="form-group">
      <label>Application Form:</label>
       <input type="file" name="profile4">
             <?php echo form_error('profile4', '<div class="error">', '</div>'); ?> 
                  <p>Only PDF,JPG,PNG allowed</p>
                  <?php if($chk_status['application_form']){ ?>
              <img src="<?php echo base_url($chk_status['application_form']); ?>" width="100" />
              <?php } ?>
    </div>
    </div>

    <div class="col-lg-6 col-md-6">
    <div class="form-group">
      <label>Company Pan Card:</label>
      <input type="file" name="profile5">
             <?php echo form_error('profile5', '<div class="error">', '</div>'); ?> 
                  <p>Only PDF,JPG,PNG allowed</p>
                  <?php if($chk_status['company_pan_card']){ ?>
              <img src="<?php echo base_url($chk_status['company_pan_card']); ?>" width="100" />
              <?php } ?>
    </div>
  </div>

  <div class="col-lg-6 col-md-6">
    <div class="form-group">
      <label>GST:</label>
     <input type="text" class="form-control" name="gst_number"  placeholder="GST Number" value="<?php echo set_value('gst_number') ?><?php echo isset($chk_status['gst_number']) ? $chk_status['gst_number'] : ''; ?>">
              <?php echo form_error('gst_number', '<div class="error">', '</div>'); ?>  
    </div>
  </div>
 
 <!-- <div class="col-lg-6 col-md-6">
    <div class="form-group">
      <label>Udhyam:</label>
    <input type="text" name="address" class="form-control">
    </div>
  </div>  -->

  <div class="col-lg-4 col-md-4">
    <div class="form-group">
      <label>Business Photo Captured by Camera:</label>
    <input type="file" name="profile6">
             <?php echo form_error('profile6', '<div class="error">', '</div>'); ?> 
                  <p>Only PDF,JPG,PNG allowed</p>
                  <?php if($chk_status['business_photo']){ ?>
              <img src="<?php echo base_url($chk_status['business_photo']); ?>" width="100" />
            <?php } ?>
    </div>
  </div>
   <?php if($chk_status['status']!=1 && $chk_status['status']!=2) { ?> 

  <div class="col-lg-12 col-md-12">
    <div class="form-group text-right">
     <button type="submit" class="btn btn-secondary">Submit</button>
     <button onclick="window.history.back()" type="button" class="btn btn-danger">Cancel</button>
    </div>
  </div>

   <?php }?> 

</div>
</div>


  </div>

</div>
</div>

<?php echo form_close(); ?>     
<!-- END KYC Details -->
 
</div>



<div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Aadhar Otp Verification</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <!-- <div class="modal-body" id="bankResponse" class="text-center">
              
            </div> -->
            <input type="hidden" name="aadhar_request_id" id="aadhar_request_id">
             <div class="col-lg-12 col-md-12">
              <div class="form-group">
                <label>Enter OTP:</label>
                <input type="text" class="form-control" name="aadhar_otp" id="aadhar_otp" placeholder="Enter OTP" value="<?php echo set_value('aadhar_otp') ?>" required="">
              </div>
            </div>


            <div class="card-footer">
              <div class="row">

                <div class="col-sm-12 text-right">
                  <button type="button" class="btn btn-success btn-sm" id="aadhar_otp_verify">Verify</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

 


  </div></div> 





