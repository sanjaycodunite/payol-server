<div class="container-fluid">
{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Send Application Notification</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/dashboard/notificationAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              
              <div class="row">

                <div class="col-sm-12">
                  <div class="form-group">
                    <label><b>Select User*</b></label>
                    <select class="form-control selectpicker" data-live-search="true" name='user_id'>
                      <option value="0">All Users</option>
                      <?php 
                       foreach($userList as $list){
                      ?>
                       <option value="<?=$list['id']?>"><?=$list['name']?> (<?=$list['user_code']?>)</option>
                      <?php } ?>
                    </select>
                  </div>
                </div>


                <div class="col-sm-12">
                  <div class="form-group">
                    <label><b>Notification Title*</b></label>
                    <input type="text" class="form-control" name="title" placeholder="Notification Title">
                    <?php echo form_error('title', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>


                <div class="col-sm-12">
                  <div class="form-group">
                    <label><b>Notification Message*</b></label>
                    <textarea class="form-control" name="message" rows="4" placeholder="Notification Message"></textarea>
                    <?php echo form_error('message', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>

              </div>



              
          </div>
        <div class="card-footer py-3 text-right">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>
  </div>




