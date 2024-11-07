{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Update Account </b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('superadmin/account/updateAccount', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
               <input type="hidden" value="<?php echo $id;?>" name="id">

              <div class="row"  id="before_user_row">




                

                  <div class="col-sm-2">
                <div class="form-group">
                  <label><b>Account Type*</b></label>
                  <select class="form-control" name="account_type">
                    <?php if($accountTypeList){ ?>
                      <?php foreach($accountTypeList as $alist){ ?>
                        <option value="<?php echo $alist['id']; ?>" <?php if($alist['id'] == $List['account_type']){ ?> selected="selected" <?php } ?>><?php echo $alist['title']; ?></option>  
                      <?php } ?>
                    <?php } ?>
                </select>
                <?php echo form_error('account_type', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                
           <div class="col-sm-2">
                <div class="form-group">
                  <label><b>Package*</b></label>
                  <select class="form-control" name="package_id">
                    <option value="">Select Package</option>
                    <?php if($packageList){ ?>
                      <?php foreach($packageList as $alist){ ?>
                        <option value="<?php echo $alist['id']; ?>" <?php if($alist['id'] == $List['package_id']){ ?> selected="selected" <?php } ?>><?php echo $alist['package_name']; ?></option>  
                      <?php } ?>
                    <?php } ?>
                </select>
                <?php echo form_error('package_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

              <div class="col-sm-2">
                <div class="form-group">
                <label><b>Domain Name</b></label>
                <input type="text" class="form-control" name="domain_name" value="<?php echo $List['title'] ?>">
                <?php echo form_error('domain_name', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Domain Url</b></label>
                <input type="text" class="form-control" name="domain_url" value="<?php echo $List['domain_url'] ?>">
                <?php echo form_error('title', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Member ID Prefix*</b></label>
                <input type="text" class="form-control" name="account_code" value="<?php echo $List['account_code'] ?>">
                <?php echo form_error('account_code', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-2">
                   <div class="form-group">
            <label>Image</label>
                  <input type="file" name="profile">
            <?php echo form_error('profile', '<p class="reg_alert_error">', '</p>'); ?>
                  <p>Only PDF,JPG,PNG allowed</p>
            <?php if($List['image_path']){ ?>
              <img src="<?php echo base_url($List['image_path']); ?>" width="150" />
            <?php } ?>
          </div>
                  
                </div>



                  <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Contact Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Name*</b></label>
                <input type="text" class="form-control" name="name" value="<?php echo $List['name'] ?>">
                <?php echo form_error('name', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
                <div class="form-group">
                <label><b>Email*</b></label>
                <input type="text" class="form-control" name="email" value="<?php echo $List['email'] ?>">
                <?php echo form_error('email', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
                <div class="form-group">
                <label><b>Mobile*</b></label>
                <input type="text" class="form-control" name="mobile" value="<?php echo $List['mobile'] ?>">
                <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Login Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Username*</b></label>
                <input type="text" class="form-control" name="username" value="<?php echo $List['username'] ?>">
                <?php echo form_error('username', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
                <div class="form-group">
                <label><b>Password</b></label>
                <input type="password" class="form-control" name="password" value="<?php echo set_value('password') ?>">
                <?php echo form_error('password', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
               <div class="form-group">
                <label><b>Status</b></label>
                <select class="form-control" name="is_active">
                <option value="1" <?php if($List['status'] == 1){ ?> selected="selected" <?php } ?>>Active</option>
                <option value="0" <?php if($List['status'] == 0){ ?> selected="selected" <?php } ?>>Deactive</option>  
                </select>
                <?php echo form_error('is_active  ', '<div class="error">', '</div>'); ?>  
               </div>
               </div> 

               <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Instantpay Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Encryption Key</b></label>
                <input type="text" class="form-control" name="instant_encryption_key" value="<?php echo $List['instant_encryption_key'] ?>">
                <?php echo form_error('instant_encryption_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Auth Code</b></label>
                <input type="text" class="form-control" name="instant_auth_code" value="<?php echo $List['instant_auth_code'] ?>">
                <?php echo form_error('instant_auth_code', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Token</b></label>
                <input type="text" class="form-control" name="instant_token" value="<?php echo $List['instant_token'] ?>">
                <?php echo form_error('instant_token', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Client ID</b></label>
                <input type="text" class="form-control" name="instant_client_id" value="<?php echo $List['instant_client_id'] ?>">
                <?php echo form_error('instant_client_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Client Secret</b></label>
                <input type="text" class="form-control" name="instant_client_secret" value="<?php echo $List['instant_client_secret'] ?>">
                <?php echo form_error('instant_client_secret', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Account No (For Payout API)</b></label>
                <input type="text" class="form-control" name="instant_account_no" value="<?php echo $List['instant_account_no'] ?>">
                <?php echo form_error('instant_account_no', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                

               <div class="col-sm-4">
                    <div class="form-group">
                      <label><h4>Services</h4></label>
                      
                    </div>
                    
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label><h4>Permission</h4></label>
                      
                    </div>
                    
                  </div>

                  <div class="col-sm-4">
                    <div class="form-group">
                      <label><h4>Custom API Permission</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-4">
                <div class="form-group">
                  <?php if($serviceList){ ?>
                    <?php foreach($serviceList as $list){ ?>
                      <input type="checkbox" name="service_id[]" <?php if(in_array($list['id'],$accountServiceID)){ ?> checked="checked" <?php } ?> value="<?php echo $list['id']; ?>" id="service<?php echo $list['id']; ?>">
                      <label for="service<?php echo $list['id']; ?>"><?php echo $list['title']; ?></label> <br />
                    <?php } ?>
                <?php } ?>
                </div>
                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                  
                  <input type="checkbox" <?php if($List['is_api_active'] == 1){ ?> checked="checked" <?php } ?> name="is_api_active" value="1" id="api1">
                  <label for="api1">API Add/edit</label> <br />

                  <input type="checkbox" <?php if($List['is_wallet_deduction'] == 1){ ?> checked="checked" <?php } ?> name="is_wallet_deduction" value="1" id="api2">
                  <label for="api2">Wallet Deduction</label> <br />

                  <input type="checkbox" <?php if($List['is_disable_api_role'] == 1){ ?> checked="checked" <?php } ?> name="is_disable_api_role" value="1" id="api3">
                  <label for="api3">Hide API User Role</label> <br />

                  <input type="checkbox" <?php if($List['is_disable_user_role'] == 1){ ?> checked="checked" <?php } ?> name="is_disable_user_role" value="1" id="api4">
                  <label for="api4">Hide User Role</label> <br />

                  <input type="checkbox" name="is_employe_panel" <?php if($List['is_employe_panel'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_employe_panel">
                  <label for="is_employe_panel">Is Create Employe ?</label> <br />

                  <input type="checkbox" name="is_auto_bank_settlement" <?php if($List['is_auto_bank_settlement'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_auto_bank_settlement">
                  <label for="is_auto_bank_settlement">Is Allow Bank Settlement ?</label> <br />
                  
                  <input type="checkbox" name="is_paysprint_aeps" <?php if($List['is_paysprint_aeps'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_paysprint_aeps">
                  <label for="is_paysprint_aeps">Is Active Paysprint AEPS ?</label> <br />

                    <input type="checkbox" name="is_tds_amount" <?php if($List['is_tds_amount'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_tds_amount">
                  <label for="is_tds_amount">Is  TDS Amount Deduct  ?</label> <br />

                  <input type="checkbox" name="is_payout_otp" <?php if($List['is_payout_otp'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_payout_otp">
                  <label for="is_payout_otp">Is  Payout Otp  ?</label> <br />
                    
                    
                     <input type="checkbox" name="is_move_wallet" <?php if($List['is_move_wallet'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_move_wallet">
                  <label for="is_move_wallet">Is  Move Wallet  ?</label> <br />

                  <input type="checkbox" name="is_generate_invoice" <?php if($List['is_generate_invoice'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_generate_invoice">
                  <label for="is_generate_invoice">Is Generate Invoice  ?</label> <br />
                  
                   <input type="checkbox" name="is_otp_login" <?php if($List['is_otp_login'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_otp_login">
                  <label for="is_otp_login">Is Otp Login  ?</label> <br />
                  



                  
                </div>
                  
                </div>
                <div class="col-sm-4">
                <div class="form-group">
                  <input type="checkbox" name="is_cogent_instant_api" <?php if($List['is_cogent_instant_api'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_cogent_instant_api">
                      <label for="is_cogent_instant_api">Is Cogent Instant API ?</label> <br />
                  <?php if($customApiList){ ?>
                    <?php foreach($customApiList as $list){ ?>
                      <input type="checkbox" name="custom_api_id[]" <?php if(in_array($list['id'],$accountCustomApiID)){ ?> checked="checked" <?php } ?> value="<?php echo $list['id']; ?>" id="customapi<?php echo $list['id']; ?>">
                      <label for="customapi<?php echo $list['id']; ?>"><?php echo $list['title']; ?></label> <br />
                    <?php } ?>
                <?php } ?>
                </div>
                  
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>SMS API Detail( MSG 91)</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Auth Key</b></label>
                <input type="text" class="form-control" name="sms_auth_key" value="<?php echo $List['sms_auth_key'] ?>">
                <?php echo form_error('sms_auth_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-3">
                <div class="form-group">
                <label><b>Template ID</b></label>
                <input type="text" class="form-control" name="sms_template_id" value="<?php echo $List['sms_template_id'] ?>">
                <?php echo form_error('sms_template_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Flow ID</b></label>
                <input type="text" class="form-control" name="sms_flow_id" value="<?php echo $List['sms_flow_id'] ?>">
                <?php echo form_error('sms_flow_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Sender</b></label>
                <input type="text" class="form-control" name="sms_sender" value="<?php echo $List['sms_sender'] ?>">
                <?php echo form_error('sms_sender', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>


                 <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>SMS OTP API Detail(MSG 91)</h4></label>
                      
                    </div>
                    
                  </div>



                 <div class="col-sm-3">
                <div class="form-group">
                <label><b>Template ID</b></label>
                <input type="text" class="form-control" name="sms_otp_template_id" value="<?php echo $List['sms_otp_template_id'] ?>">
                <?php echo form_error('sms_otp_template_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Flow ID</b></label>
                <input type="text" class="form-control" name="sms_otp_flow_id" value="<?php echo $List['sms_otp_flow_id'] ?>">
                <?php echo form_error('sms_otp_flow_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                


                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Recharge/BBPS API Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Username</b></label>
                <input type="text" class="form-control" name="dmt_username" value="<?php echo $List['dmt_username'] ?>">
                <?php echo form_error('dmt_username', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-2">
                <div class="form-group">
                <label><b>Password</b></label>
                <input type="text" class="form-control" name="dmt_password" value="<?php echo $List['dmt_password'] ?>">
                <?php echo form_error('dmt_password', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-2">
                <div class="form-group">
                <label><b>PIN</b></label>
                <input type="text" class="form-control" name="dmt_pin" value="<?php echo $List['dmt_pin'] ?>">
                <?php echo form_error('dmt_pin', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Token</b></label>
                <input type="text" class="form-control" name="dmt_token" value="<?php echo $List['dmt_token'] ?>">
                <?php echo form_error('dmt_token', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>DMT API Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Key</b></label>
                <input type="text" class="form-control" name="dmt_key" value="<?php echo $List['dmt_key'] ?>">
                <?php echo form_error('dmt_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
                <div class="form-group">
                <label><b>Access Code</b></label>
                <input type="text" class="form-control" name="dmt_access_code" value="<?php echo $List['dmt_access_code'] ?>">
                <?php echo form_error('dmt_access_code', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Institute Id</b></label>
                <input type="text" class="form-control" name="dmt_institute_id" value="<?php echo $List['dmt_institute_id'] ?>">
                <?php echo form_error('dmt_institute_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Virtual Account Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Prefix</b></label>
                <input type="text" class="form-control" name="van_prefix" value="<?php echo $List['van_prefix'] ?>">
                <?php echo form_error('van_prefix', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
                <div class="form-group">
                <label><b>IFSC</b></label>
                <input type="text" class="form-control" name="van_ifsc" value="<?php echo $List['van_ifsc'] ?>">
                <?php echo form_error('van_ifsc', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>


                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>AEPS API Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Username</b></label>
                <input type="text" class="form-control" name="aeps_username" value="<?php echo $List['aeps_username'] ?>">
                <?php echo form_error('aeps_username', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
                <div class="form-group">
                <label><b>Password</b></label>
                <input type="text" class="form-control" name="aeps_password" value="<?php echo $List['aeps_password'] ?>">
                <?php echo form_error('aeps_password', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-2">
                <div class="form-group">
                <label><b>PIN</b></label>
                <input type="text" class="form-control" name="aeps_supermerchant_id" value="<?php echo $List['aeps_supermerchant_id'] ?>">
                <?php echo form_error('aeps_supermerchant_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-4">
                <div class="form-group">
                <label><b>Secret Key</b></label>
                <input type="text" class="form-control" name="aeps_secret_key" value="<?php echo $List['aeps_secret_key'] ?>">
                <?php echo form_error('aeps_secret_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-12">
                <div class="form-group">
                <label><b>Production Public Certificate</b></label>
                <textarea class="form-control" name="aeps_certificate" rows="35"><?php echo $List['aeps_certificate'] ?></textarea>
                <?php echo form_error('aeps_certificate', '<div class="error">', '</div>'); ?>  
                </div>                


              

                </div>

                 <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4> Paysprint AEPS API Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Partner ID</b></label>
                <input type="text" class="form-control" name="paysprint_partner_id" value="<?php echo $List['paysprint_partner_id'] ?>">
                <?php echo form_error('paysprint_partner_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                 <div class="col-sm-4">
                <div class="form-group">
                <label><b>AEPS KEY</b></label>
                <input type="text" class="form-control" name="paysprint_aeps_key" value="<?php echo $List['paysprint_aeps_key'] ?>">
                <?php echo form_error('paysprint_aeps_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                  <div class="col-sm-4">
                <div class="form-group">
                <label><b>AEPS IV</b></label>
                <input type="text" class="form-control" name="paysprint_aeps_iv" value="<?php echo $List['paysprint_aeps_iv'] ?>">
                <?php echo form_error('paysprint_aeps_iv', '<div class="error">', '</div>'); ?>  
                </div>
              </div>



                <div class="col-sm-6">
                <div class="form-group">
                <label><b> Paysprint Secret Key</b></label>
                <input type="text" class="form-control" name="paysprint_secret_key" value="<?php echo $List['paysprint_secret_key'] ?>">
                <?php echo form_error('paysprint_secret_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                  <div class="col-sm-6">
                <div class="form-group">
                <label><b> Paysprint Authorized Key (Authorized key is required to pass in UAT but not in Live environment)</b></label>
                <input type="text" class="form-control" name="paysprint_authorized_key" value="<?php echo $List['paysprint_authorized_key'] ?>">
                <?php echo form_error('paysprint_authorized_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>



                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>CIB API Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Email</b></label>
                <input type="text" class="form-control" name="cib_email" value="<?php echo $List['cib_email'] ?>">
                <?php echo form_error('cib_email', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Password</b></label>
                <input type="text" class="form-control" name="cib_password" value="<?php echo $List['cib_password'] ?>">
                <?php echo form_error('cib_password', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Aggrid</b></label>
                <input type="text" class="form-control" name="cib_aggrid" value="<?php echo $List['cib_aggrid'] ?>">
                <?php echo form_error('cib_aggrid', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Aggrname</b></label>
                <input type="text" class="form-control" name="cib_aggrname" value="<?php echo $List['cib_aggrname'] ?>">
                <?php echo form_error('cib_aggrname', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Corp ID</b></label>
                <input type="text" class="form-control" name="cib_corpid" value="<?php echo $List['cib_corpid'] ?>">
                <?php echo form_error('cib_corpid', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>User ID</b></label>
                <input type="text" class="form-control" name="cib_userid" value="<?php echo $List['cib_userid'] ?>">
                <?php echo form_error('cib_userid', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>URN</b></label>
                <input type="text" class="form-control" name="cib_urn" value="<?php echo $List['cib_urn'] ?>">
                <?php echo form_error('cib_urn', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Debit Account</b></label>
                <input type="text" class="form-control" name="cib_debitacc" value="<?php echo $List['cib_debitacc'] ?>">
                <?php echo form_error('cib_debitacc', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Encryption Key</b></label>
                <input type="text" class="form-control" name="cib_encryption_key" value="<?php echo $List['cib_encryption_key'] ?>">
                <?php echo form_error('cib_encryption_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Security Key</b></label>
                <input type="text" class="form-control" name="cib_security_key" value="<?php echo $List['cib_security_key'] ?>">
                <?php echo form_error('cib_security_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-6">
                <div class="form-group">
                <label><b>Bank Public Certificate</b></label>
                <textarea class="form-control" name="cib_bank_certificate" rows="35"><?php echo $List['cib_bank_certificate'] ?></textarea>
                <?php echo form_error('cib_bank_certificate', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-6">
                <div class="form-group">
                <label><b>Private Certificate</b></label>
                <textarea class="form-control" name="cib_private_certificate" rows="35"><?php echo $List['cib_private_certificate'] ?></textarea>
                <?php echo form_error('cib_private_certificate', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>UPI Collection API Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Email</b></label>
                <input type="text" class="form-control" name="upi_email" value="<?php echo $List['upi_email'] ?>">
                <?php echo form_error('upi_email', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Password</b></label>
                <input type="text" class="form-control" name="upi_password" value="<?php echo $List['upi_password'] ?>">
                <?php echo form_error('upi_password', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Merchant ID</b></label>
                <input type="text" class="form-control" name="upi_merchant_id" value="<?php echo $List['upi_merchant_id'] ?>">
                <?php echo form_error('upi_merchant_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Merchant Name</b></label>
                <input type="text" class="form-control" name="upi_merchant_name" value="<?php echo $List['upi_merchant_name'] ?>">
                <?php echo form_error('upi_merchant_name', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Terminal ID</b></label>
                <input type="text" class="form-control" name="upi_terminal_id" value="<?php echo $List['upi_terminal_id'] ?>">
                <?php echo form_error('upi_terminal_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Encryption Key</b></label>
                <input type="text" class="form-control" name="upi_encryption_key" value="<?php echo $List['upi_encryption_key'] ?>">
                <?php echo form_error('upi_encryption_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Security Key</b></label>
                <input type="text" class="form-control" name="upi_security_key" value="<?php echo $List['upi_security_key'] ?>">
                <?php echo form_error('upi_security_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-6"></div>

                <div class="col-sm-6">
                <div class="form-group">
                <label><b>Bank Public Certificate</b></label>
                <textarea class="form-control" name="upi_bank_certificate" rows="15"><?php echo $List['upi_bank_certificate'] ?></textarea>
                <?php echo form_error('upi_bank_certificate', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-6">
                <div class="form-group">
                <label><b>Private Certificate</b></label>
                <textarea class="form-control" name="upi_private_certificate" rows="15"><?php echo $List['upi_private_certificate'] ?></textarea>
                <?php echo form_error('upi_private_certificate', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>UPI Cash API Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Email</b></label>
                <input type="text" class="form-control" name="upi_cash_email" value="<?php echo $List['upi_cash_email'] ?>">
                <?php echo form_error('upi_cash_email', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Password</b></label>
                <input type="text" class="form-control" name="upi_cash_password" value="<?php echo $List['upi_cash_password'] ?>">
                <?php echo form_error('upi_cash_password', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Merchant ID</b></label>
                <input type="text" class="form-control" name="upi_cash_merchant_id" value="<?php echo $List['upi_cash_merchant_id'] ?>">
                <?php echo form_error('upi_cash_merchant_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Merchant Name</b></label>
                <input type="text" class="form-control" name="upi_cash_merchant_name" value="<?php echo $List['upi_cash_merchant_name'] ?>">
                <?php echo form_error('upi_cash_merchant_name', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-2">
                <div class="form-group">
                <label><b>Terminal ID</b></label>
                <input type="text" class="form-control" name="upi_cash_terminal_id" value="<?php echo $List['upi_cash_terminal_id'] ?>">
                <?php echo form_error('upi_cash_terminal_id', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Encryption Key</b></label>
                <input type="text" class="form-control" name="upi_cash_encryption_key" value="<?php echo $List['upi_cash_encryption_key'] ?>">
                <?php echo form_error('upi_cash_encryption_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Security Key</b></label>
                <input type="text" class="form-control" name="upi_cash_security_key" value="<?php echo $List['upi_cash_security_key'] ?>">
                <?php echo form_error('upi_cash_security_key', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                <div class="col-sm-6"></div>

                <div class="col-sm-6">
                <div class="form-group">
                <label><b>Bank Public Certificate</b></label>
                <textarea class="form-control" name="upi_cash_bank_certificate" rows="15"><?php echo $List['upi_cash_bank_certificate'] ?></textarea>
                <?php echo form_error('upi_cash_bank_certificate', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-6">
                <div class="form-group">
                <label><b>Private Certificate</b></label>
                <textarea class="form-control" name="upi_cash_private_certificate" rows="15"><?php echo $List['upi_cash_private_certificate'] ?></textarea>
                <?php echo form_error('upi_cash_private_certificate', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>
                
                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Current Account API Detail</h4></label>
                      
                    </div>
                    
                  </div>


                <div class="col-sm-3">
                <div class="form-group">
                <label><b>User</b></label>
                <input type="text" class="form-control" name="current_account_user" value="<?php echo $List['current_account_user'] ?>">
                <?php echo form_error('current_account_user', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-3">
                <div class="form-group">
                <label><b>Passcode</b></label>
                <input type="text" class="form-control" name="current_account_passcode" value="<?php echo $List['current_account_passcode'] ?>">
                <?php echo form_error('current_account_passcode', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Payment Gateway</h4></label>
                      
                    </div>
                    
                  </div>

                <div class="col-sm-12">

                  <table class="table table-bordered table-striped">
                    <tr>
                      <th>#</th>
                      <th>Payment Gateway</th>
                      <th>Key</th>
                      <th>Salt</th>
                    </tr>
                    <?php if($gatewayList){ ?>
                      <?php foreach($gatewayList as $gkey=>$glist){ ?>
                        <tr>
                          <td><input type="checkbox" <?php if($glist['status'] == 1){ ?> checked="checked" <?php } ?> name="gateway_id[<?php echo $gkey; ?>]" value="<?php echo $glist['id']; ?>" /></td>
                          <td><?php echo $glist['title']; ?></td>
                          <td><input type="text" class="form-control" value="<?php echo $glist['gateway_key']; ?>" name="gateway_key[<?php echo $gkey; ?>]"></td>
                          <td><input type="text" class="form-control" value="<?php echo $glist['gateway_secret']; ?>" name="gateway_secret[<?php echo $gkey; ?>]"></td>
                        </tr>
                      <?php } ?>
                    <?php } ?>
                  </table>

                </div>



                <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Firebase Application Notification</h4></label>
                      
                    </div>
                    
                  </div>

                <div class="col-sm-12">

                  <table class="table table-bordered table-striped">
                    <tr>
                      <th>#</th>
                      <th width="200">Application Notification</th>
                      <th>Server Key</th>
                    </tr>
                        <tr>
                          <td><input type="checkbox" <?php if($List['is_app_notification'] == 1){ ?> checked="checked" <?php } ?> name="is_app_notification" value="1" /></td>
                          <td width="200">App Notification</td>
                          <td><input type="text" class="form-control" value="<?php echo $List['notification_server_key']; ?>" name="notification_server_key"></td>
                          
                        </tr>
                    
                    </table>

                </div>


                   <div class="col-sm-12">
                    <div class="form-group">
                      <label><h4>Website Theme Setting</h4></label>
                      
                    </div>
                    
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group">

                      <select name="web_theme" class="form-control">
                        <option value=" ">Select Theme</option>
                        
                        <option value="1" <?php if($List['web_theme'] == 1){ ?> selected="selected" <?php } ?>>Main Theme</option>
                        
                        <option value="2" <?php if($List['web_theme'] == 2){ ?> selected="selected" <?php } ?>>Whitelabel Theme</option>

                        <option value="3" <?php if($List['web_theme'] == 3){ ?> selected="selected" <?php } ?>>Theme Three</option>
                        
                        <option value="4" <?php if($List['web_theme'] == 4){ ?> selected="selected" <?php } ?>>Payol Theme</option>
                        
                        <?php echo form_error('web_theme', '<div class="error">', '</div>'); ?>  
                        
                      </select>
                      
                    </div>
                    
                  </div>

			   
			  
			   
              </div>
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




