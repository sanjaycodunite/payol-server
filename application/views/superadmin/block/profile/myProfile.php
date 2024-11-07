<!-- Begin Page Content -->
        <div class="container-fluid" style="padding: 0; margin: 0">
        {system_message}    
        {system_info}  
          <!-- Page Heading -->
          <div class="row" style="padding: 0; margin: 0; width: 100%;">
          <img src="{site_url}skin/admin/img/myprofile-banner.jpg" style="width:100%;">
          </div>
          

          <div class="row" style="margin-top: 40px;" >
          <div class="col-sm-3 text-center" style="box-shadow: 0px 2px 3px; padding-top: 30px; margin-left: 30px; padding-bottom: 30px; height:330px;">
          <img src="{site_url}skin/admin/img/user.png" class="rounded-circle">
          <?php
          $data=$this->db->get_where('users',array('id'=>$loggedUser['id']))->row_array();
          ?>
          <h4 style="color: black; margin-top: 15px;"><b><?php echo $data['name']; ?></b></h4>
          <h6 style="color: black; margin-top: 15px;"><?php echo $data['email']; ?></h6>
          </br><button type="button" class="btn btn-primary" style="font-size: 14px;" data-toggle="modal" data-target="#changePassword">Change Password</button>
          </div>

          <div class="col-sm-5" style="position: relative; top: -100px; left: 20px;">
          <div class="card">
          <div class="card-header" style="padding-top: 25px; padding-bottom: 25px;">
            <div class="row">
            <div class="col-sm-6">
            <h5 style="color: black;"><b>User Info</b></h5>
          </div>
          <div class="col-sm-6 text-right"  id="editbtn">
            <button type="button" class="btn btn-info btn-sm" onclick="hide();" ><i class="fa fa-edit"></i> Edit</button>
          </div>
          </div>  
        </div>
          <div class="card-body">
           <div class="col-sm-12"  id="editbody"> 
           <?php
           $data=$this->db->get_where('users',array('id'=>$loggedUser['id']))->row_array();
           ?> 
          <span style="color: black;"><b><i class="fa fa-user"></i> Name</b></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  : <?php echo $data['name']; ?> 
      </br></br><span style="color: black;"><b><i class="fa fa-phone"></i> Phone No.</b></span>  : <?php echo $data['mobile']; ?>
      </br></br><span style="color: black;"><b><i class="fa fa-envelope"></i> Email</b></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  : <?php echo $data['email']; ?>
      </br></br><span style="color: black;"><b><i class="fa fa-globe mr-1"></i> Username</b></span>  : <?php echo $data['username']; ?>
          </div>
          <div class="col-sm-12" id="editform" style="display: none;">
            <?php echo form_open_multipart('admin/profile/updateProfile', array('id' =>'admin_profile')); ?>
           <?php
           $data=$this->db->get_where('users',array('id'=>$loggedUser['id']))->row_array();
           ?>

          <div class="form-group">
            <input type="hidden" name="accound_id" value="<?php echo $loggedUser['id']; ?>" >
          <label style=" color: black;"><b>Name*</b></label>      
          <input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo $data['name']; ?>">
          <?php echo form_error('name', '<div class="error">', '</div>'); ?>      
          </div>
          <div class="form-group">
          <label style=" color: black;"><b>Phone No.*</b></label>      
          <input type="text" class="form-control" name="mobile" placeholder="Phone No." value="<?php echo $data['mobile']; ?>">
          <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>      
          </div>
          <div class="form-group">
          <label style=" color: black;"><b>Email*</b></label>      
          <input type="text" class="form-control" name="email" placeholder="Email" value="<?php echo $data['email']; ?>"> 
          <?php echo form_error('email', '<div class="error">', '</div>'); ?>     
          </div>
          <div class="form-group text-right">
          <button type="submit" class="btn btn-primary btn-sm">Submit</button>
          <button type="button" class="btn btn-secondary btn-sm" id="cancelbtn">Cancel</button> <?php echo form_close(); ?>     
          </div>  
          </div>   
          </div>
          </div>
          </div>

          

        <div class="col-sm-2">
          </div>
  

      </div>

          <!-- Trigger the modal with a button -->

<!-- Modal -->
<div id="changePassword" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
  <div class="modal-dialog modal-md">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="color: black; font-size: 20px;"><b>Change Password</b></h4>
      </div>
      <div class="modal-body">
        <?php echo form_open_multipart('admin/profile/update', array('id' => 'admin_profile')); ?>
        <div class="col-sm-12">
        <div class="form-group">
        <label style="color: black"><b>Old Password*</b></label>
        <input type="text" autocomplete="off" class="form-control" placeholder="Old Password" name="opw" id="opw">
        <?php echo form_error('opw', '<div class="error">', '</div>'); ?>    
        </div>

        <div class="form-group">
        <label style="color: black"><b>New Password*</b></label>
        <input type="password" autocomplete="off" class="form-control" placeholder="New Password" name="npw" id="npw">
        <?php echo form_error('npw', '<div class="error">', '</div>'); ?>    
        </div>

        <div class="form-group">
        <label style="color: black"><b>Confirm New Password*</b></label>
        <input type="password" autocomplete="off" class="form-control" placeholder="Confirm New Password" name="cpw" id="cpw">
        <?php echo form_error('cpw', '<div class="error">', '</div>'); ?>
        <span style="font-size: 13px;">Note:-Confirm New Password Field Will Be same to New Password Field.</span>    
        </div>    
      </div>  
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-sm" style="">Submit</button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
     </div>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>

          

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->