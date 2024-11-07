{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Update Member</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/member/updateMember', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $id;?>" name="id">
              <div class="row">
                <div class="col-sm-3">
              <div class="form-group">
              <label><b>Member Type*</b></label>
              <select class="form-control" name="role_id">
              <option value="">Select Type</option>
              <?php if($roleList){ ?>
                <?php foreach($roleList as $list){ ?>
                  <option value="<?php echo $list['id']; ?>" <?php if($memberList['role_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                <?php } ?>
              <?php } ?>
              </select>
              <?php echo form_error('role_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Name*</b></label>
              <input type="text" class="form-control" name="name" value="<?php echo $memberList['name']; ?>" id="name" placeholder="Name">
              <?php echo form_error('name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Email</b></label>
              <input type="text" class="form-control" name="email" value="<?php echo $memberList['email']; ?>" id="email" placeholder="Email">
              <?php echo form_error('email', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-3">
                <div class="form-group">
              <label><b>Mobile*</b></label>
              <input type="text" class="form-control" name="mobile" value="<?php echo $memberList['mobile']; ?>" id="mobile" placeholder="Mobile">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              </div>

              <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Password</b></label>
              <input type="password" class="form-control" name="password" id="password" placeholder="Password">
              <?php echo form_error('password', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Transaction Password</b></label>
              <input type="password" class="form-control" name="transaction_password" id="transaction_password" placeholder="Transaction Password">
              <?php echo form_error('transaction_password', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Country*</b></label>
              <select class="form-control" name="country_id">
              <option value="">Select Country</option>
              <?php if($countryList){ ?>
                <?php foreach($countryList as $list){ ?>
                  <option value="<?php echo $list['id']; ?>" <?php if($memberList['country_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['name']; ?></option>
                <?php } ?>
              <?php } ?>
              </select>
              <?php echo form_error('country_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>State*</b></label>
              <select class="form-control" name="state_id">
              <option value="">Select State</option>
              <?php if($stateList){ ?>
                <?php foreach($stateList as $list){ ?>
                  <option value="<?php echo $list['id']; ?>" <?php if($memberList['state_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['name']; ?></option>
                <?php } ?>
              <?php } ?>
              </select>
              <?php echo form_error('state_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              
              
              
              </div>

              <div class="row">

               <div class="col-sm-3">
                <div class="form-group">
              <label><b>City*</b></label>
              <input type="text" class="form-control" name="city" value="<?php echo $memberList['city']; ?>" id="city" placeholder="City">
              <?php echo form_error('city', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


                  <div class="col-sm-3">
                <div class="form-group">
              <label><b>District*</b></label>
              <input type="text" class="form-control" name="district" value="<?php echo $memberList['district']; ?>" id="district" placeholder="District">
              <?php echo form_error('district', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


                  <div class="col-sm-3">
                <div class="form-group">
              <label><b>Block*</b></label>
              <input type="text" class="form-control" name="block" value="<?php echo $memberList['block']; ?>" id="block" placeholder="Block">
              <?php echo form_error('block', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


                  <div class="col-sm-3">
                <div class="form-group">
              <label><b>Village*</b></label>
              <input type="text" class="form-control" name="village" value="<?php echo $memberList['village']; ?>" id="village" placeholder="Village">
              <?php echo form_error('village', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


                <div class="col-sm-3">
                <div class="form-group">
              <label><b>Address*</b></label>
              <textarea name="address"class="form-control" placeholder="Please Enter Same As Aadhar Card  Back Address"> <?php echo $memberList['address']; ?></textarea>
              <?php echo form_error('address', '<div class="error">', '</div>'); ?>  
              </div>
              </div>


                  <div class="col-sm-3">
                <div class="form-group">
              <label><b>Pincode*</b></label>
              <input type="text" class="form-control" name="pincode" value="<?php echo $memberList['pincode']; ?>" id="pincode" placeholder="Pincode">
              <?php echo form_error('pincode', '<div class="error">', '</div>'); ?>  
              </div>
              </div>



                  <div class="col-sm-3">
                <div class="form-group">
              <label><b>Aadhar No*</b></label>
              <input type="text" class="form-control" name="aadhar_no" value="<?php echo $memberList['aadhar_no']; ?>" id="pincode" placeholder="Aadhar No">
              <?php echo form_error('aadhar_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>



                  <div class="col-sm-3">
                <div class="form-group">
              <label><b>Pan No*</b></label>
              <input type="text" class="form-control" name="pan_no" value="<?php echo $memberList['pan_no']; ?>" id="pan_no" placeholder="Pan No">
              <?php echo form_error('pan_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>







               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Package*</b></label>
              <select class="form-control" name="package_id">
              <option value="">Select Pacakge</option>
              <?php if($packageList){ ?>
                <?php foreach($packageList as $list){ ?>
                  <option value="<?php echo $list['id']; ?>" <?php if($memberList['package_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['package_name']; ?></option>
                <?php } ?>
              <?php } ?>
              </select>
              <?php echo form_error('package_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
              <label><b>Minimum Wallet</b></label>
              <input type="text" class="form-control" name="min_wallet_balance" id="min_wallet_balance" value="<?php echo $memberList['min_wallet_balance']; ?>" placeholder="Minimum Wallet">
              <?php echo form_error('min_wallet_balance', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

               <div class="col-sm-3">
               <div class="form-group">
                <label><b>Status</b></label>
                <select class="form-control" name="is_active">
                <option value="1" <?php if($memberList['is_active'] == 1){ ?> selected="selected" <?php } ?>>Active</option>
                <option value="0" <?php if($memberList['is_active'] == 0){ ?> selected="selected" <?php } ?>>Deactive</option>  
                </select>
                <?php echo form_error('is_active  ', '<div class="error">', '</div>'); ?>  
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




