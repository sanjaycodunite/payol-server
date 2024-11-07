{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Beneficiary</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
           
           <div class="row">
            <div class="col-lg-6 col-md-6">
              <div class="beneficiary_form_section">
                <?php echo form_open_multipart('retailer/dmt/beneficiaryAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $mobile;?>" name="accountMobile">
              <div class="row">
              
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Account Holder Name*</b></label>
              <input type="text" class="form-control" name="account_holder_name" placeholder="Account Holder Name">
              <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Beneficiary Mobile No.*</b></label>
              <input type="text" class="form-control" name="ben_mobile" placeholder="Beneficiary Mobile No.">
              <?php echo form_error('ben_mobile', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Account No.*</b></label>
              <input type="text" class="form-control" name="account_no" placeholder="Account No.">
              <?php echo form_error('account_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Bank*</b></label>
              <select class="form-control selectpicker" id="selDmtBankID" name="bankID" data-live-search="true">
                <option value="">Select Bank</option>
                <?php if($bankList){ ?>
                  <?php foreach($bankList as $list){ ?>
                    <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              <?php echo form_error('bankID', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
                <input type="hidden" id="defaultIfscTxt" value="">
             <!-- <input type="checkbox" name="is_default_ifsc" value="1" id="is_default_ifsc">
              <label for="is_default_ifsc">Don't know IFSC ?</label>-->
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>IFSC*</b></label>
              <input type="text" class="form-control" name="ifsc" id="ifsc" placeholder="IFSC">
              <?php echo form_error('ifsc', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
                <p style="color:red;">Note : Account verify will be chargeable, please <a href="{site_url}retailer/master/myAccountVerifyCharge">click here</a> to check your account verify charge.</p>
              </div>
               <div class="col-lg-12">
              <div class="form-group">
               <button class="login_btn btn btn-success" name="submit" type="submit">Submit </button>
               <button class="login_btn btn btn-success" name="verfiy" type="submit">Verify & Submit </button> 
              </div></div>

             

            </div>
            <?php echo form_close(); ?>
          </div>
          <hr />
          <div class="col-lg-12 col-md-12">
              <div class="beneficiary_form_section">
                <?php echo form_open_multipart('retailer/dmt/updateDetailAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $mobile;?>" name="accountMobile">
              <div class="row">
              <div class="col-lg-12">
                <h3>Update Personal Detail</h3>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>First Name*</b></label>
              <input type="text" class="form-control" name="first_name" value="<?php echo $member_dmt_data['first_name']; ?>" placeholder="First Name">
              <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Last Name*</b></label>
              <input type="text" class="form-control" name="last_name" value="<?php echo $member_dmt_data['last_name']; ?>" placeholder="Last Name">
              <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>DOB*</b></label>
              <input type="text" class="form-control" value="<?php echo $member_dmt_data['dob']; ?>" id="start_date" autocomplete="off" name="dob" placeholder="Date of Birth">
              <?php echo form_error('dob', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Address*</b></label>
              <textarea name="address" class="form-control" rows="2"><?php echo $member_dmt_data['address']; ?></textarea>
              <?php echo form_error('address', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Pin Code*</b></label>
              <input type="text" class="form-control" value="<?php echo $member_dmt_data['pin_code']; ?>" name="pin_code" placeholder="Pin Code">
              <?php echo form_error('pin_code', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
               <div class="col-lg-12">
              <div class="form-group">
               <button class="login_btn btn btn-success" type="submit">Submit </button> 
              </div></div>

             

            </div>
            <?php echo form_close(); ?>
          </div>
            </div> 
            </div> 
            <div class="col-lg-6 col-md-6">
              <div class="beneficiary_col_bt1">
                <?php if($beneficiaryList){ ?>
                  <?php foreach($beneficiaryList as $list){ ?>
              <div class="beneficiary_right_history_section d-flex">
               <div class="beneficiary_colms-details">
                <h4><?php echo $list['account_holder_name']; ?></h4> 
                <p>A/c: <?php echo $list['account_no']; ?></p>
                <p>IFSC: <?php echo $list['ifsc']; ?></p>
                <p>Bank: <?php echo $list['bank_name']; ?></p>
               </div> 
               <div class="beneficiary_colms-details_btns">
                 <a href="<?php echo base_url('retailer/dmt/moneyTransfer').'/'.$list['beneId']; ?>"><button class="btn-transfer" type="button">Transfer</button></a>
                 <a href="<?php echo base_url('retailer/dmt/deleteBen').'/'.$list['beneId'].'/'.$mobile; ?>" onclick="return confirm('Are you sure want to delete?')"><button class="btn btn-danger" style="padding: 13px 30px;" type="button">Delete</button></a>
               </div>
              </div>
              <?php } ?>
              <?php } else { ?>


              <div class="beneficiary_right_history_section d-flex">
               <div class="beneficiary_colms-details">
                No Beneficiary Registered
               </div> 
               <div class="beneficiary_colms-details_btns">
                 
               </div>
              </div>
              <?php } ?>
            </div>

            </div> 
           </div>

          
         
              
              
          </div>
        </div>
        
 
    </div>




