{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Create Ticket</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('portal/ticket/saveTicketAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-8">
              <div class="form-group">
              <label><b>Subject*</b></label>
              <input type="text" class="form-control" name="subject" placeholder="Subject">
              <?php echo form_error('subject', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
			  <div class="col-sm-2">
              <div class="form-group">
              <label><b>Related To</b></label>
              <select class="selectpicker form-control" name="related_to" id="related_to" data-live-search="true">
              <?php
              if($relatedList){
                foreach($relatedList as $list){
              ?>
              <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
              <?php }} ?>  
              </select>
              <?php echo form_error('related_to', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
			        
              <div class="col-sm-8">
                <div class="form-group">
              <label><b>Message</b></label>
              <textarea class="form-control" rows="8" name="message" id="message"></textarea>
              <?php echo form_error('message', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-4">
                <div class="form-group">
              <label><b>Attachment</b></label>
              <input type="file" name="attachment">
              <p>Allowed File Extenstions: .jpg, .png, .gif <br /> Max Size Should be : 2MB</p>
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




