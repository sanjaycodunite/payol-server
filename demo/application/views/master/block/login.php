<div class="container">
  <style type="text/css">
    .alert_error{
      color: red;
    }
  </style>
    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block text-center">
				<br><br><br><br>
				<img src="{site_url}skin/images/logo.png">
			  </div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center alert_error">
                    <h1 class="h4 text-gray-900 mb-4">Welcome !!</h1>
                    
                   
                    {system_message}
	  				        {system_info}
                  </div>
                  <?php   if($this->session->flashdata($Otp_Data)){ 
                   $action = "VerifyOtploginAuth";

                  } else {  $action= "loginAuth"; } ?>


                  <?php echo form_open("admin/login/$action",array('class'=>'user'));?>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="username" aria-describedby="emailHelp" name="username" placeholder="Enter Username" value="<?php  if ($this->session->userdata('username') !== FALSE) { echo $this->session->userdata('username'); }    ?>">
                      <?php echo form_error('username', '<p class="alert_error">', '</p>');?>
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="password" id="password" placeholder="Password"   value="<?php  if ($this->session->userdata('password') !== FALSE) { echo $this->session->userdata('password'); }    ?>">
                      <?php echo form_error('password', '<p class="alert_error">', '</p>');?>
                    </div>

                  <?php   if($this->session->flashdata('Otp')){ ?>
    

                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="otp" id="otp" placeholder="Enter OTP">
                      <?php echo form_error('otp', '<p class="alert_error">', '</p>');?>
                    </div>
                 <?php } ;?>

                  <?php    if($this->session->flashdata('Otp_error')){ ?>
                     <p>OTP Not Matched</p> <?php }  ?>


                 
                    <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck">
                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                    <hr>
                    
                  <?php echo form_close();?>
                 
                 
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>


