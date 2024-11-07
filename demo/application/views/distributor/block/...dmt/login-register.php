{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Sender</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
           
           <div class="transfer_form_tabs">
             <nav class="forms_tabs">
                            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Register</a>
                                <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Login</a>
                            </div>
                        </nav>

                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                             <div class="form_colms">
                                <?php echo form_open_multipart('distributor/dmt/registerAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              
              <div class="col-lg-3">
              <div class="form-group">
              <label><b>Name*</b></label>
              <input type="text" class="form-control" name="name" placeholder="Name">
              <?php echo form_error('name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-lg-3">
              <div class="form-group">
              <label><b>Mobile</b></label>
              <input type="text" class="form-control" name="mobile" placeholder="Mobile">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-4">
              <div class="form-group">
              <label><b>Pin Code*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('pin_code'); ?>" name="pin_code" placeholder="Pin Code">
              <?php echo form_error('pin_code', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-5"></div>
              <div class="col-lg-4">
              <div class="form-group">
              <button class="login_btn btn btn-success" type="submit"> Submit</button> 
             </div>
           </div>

             

            </div>
            <?php echo form_close(); ?>     
                             </div> 
                            </div>

                            <div class="tab-pane" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                             <?php echo form_open_multipart('distributor/dmt/loginAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
                                <div class="row">
              
              <div class="m-auto col-lg-6">
                 <div class="login_tr_form">
              <div class="form-group">
              <label><b>Enter Your Mobile*</b></label>
              <input type="text" class="form-control" name="mobile" placeholder="Mobile">
              <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
              </div>

             <div class="form-group">
              <button class="login_btn btn btn-success" type="submit"> Submit</button> 
             </div>

              </div></div> 
                              </div>
                              <?php echo form_close(); ?>     
                            </div>

                          </div>

           </div>

           
         
              
              
          </div>
        </div>
        
    </div>




