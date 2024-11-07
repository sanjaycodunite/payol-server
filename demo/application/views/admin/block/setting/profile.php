{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Update Profile</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/setting/profileAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Name*</b></label>
              <input type="text" class="form-control" name="name" value="<?php echo $userData['name']; ?>" id="name" placeholder="Name">
              <?php echo form_error('name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Firm Name*</b></label>
              <input type="text" class="form-control" name="firm_name" value="<?php echo $accountData['title']; ?>" id="firm_name" placeholder="Firm Name">
              <?php echo form_error('firm_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Email</b></label>
              <input type="text" class="form-control" name="email" value="<?php echo $userData['email']; ?>" id="email" placeholder="Email">
              <?php echo form_error('email', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-3">
                <div class="form-group">
              <label><b>Mobile*</b></label>
              <input type="text" class="form-control" name="mobile" value="<?php echo $userData['mobile']; ?>" id="mobile" placeholder="Mobile">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-4">
                   <div class="form-group">
            <label>Logo</label>
                  <input type="file" name="profile">
            <?php echo form_error('profile', '<p class="reg_alert_error">', '</p>'); ?>
                  <p>Only PDF,JPG,PNG allowed</p>
            <?php if($accountData['image_path']){ ?>
              <img src="<?php echo base_url($accountData['image_path']); ?>" width="150" />
            <?php } ?>
          </div>
                  
                </div>
              </div>

              <div class="row">
                <div class="col-sm-12"><h3>Bank Account Detail</h3><hr /></div>
        <div class="col-sm-3">
         <div class="form-group">
          <label><b>Account Holder Name*</b></label>
          <input type="text" class="form-control" autocomplete="off" value="<?php echo $accountData['account_holder_name']; ?>" name="account_holder_name" id="account_holder_name" placeholder="Holder Name">
          <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
          </div> 
        </div>

        <div class="col-sm-3">
        <div class="form-group">
        <label><b>Bank*</b></label>
        <select class="form-control selectpicker" name="bankID" id="bankID" data-live-search="true">
          <option value="">Select Bank</option>
          <?php if($bankList){ ?>
            <?php foreach($bankList as $list){ ?>
              <option value="<?php echo $list['id']; ?>" <?php if($accountData['bankID'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['bank_name']; ?></option>
            <?php } ?>
          <?php } ?>
        </select>
        <?php echo form_error('bankID', '<div class="error">', '</div>'); ?>  
        </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
          <label><b>Account No.*</b></label>
          <input type="text" class="form-control" autocomplete="off" value="<?php echo $accountData['account_number']; ?>" name="account_number" id="account_number" placeholder="Account No.">
          <?php echo form_error('account_number', '<div class="error">', '</div>'); ?>  
          
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
          <label><b>IFSC Code*</b></label>
          <input type="text" class="form-control" autocomplete="off" value="<?php echo $accountData['ifsc']; ?>" name="ifsc" id="ifsc" placeholder="IFSC Code">
          <?php echo form_error('ifsc', '<div class="error">', '</div>'); ?>  
          
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




