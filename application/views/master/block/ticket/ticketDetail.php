<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-9">
                <h4><b>Ticket - <?php echo $ticketData['ticket_id']; ?></b></h4>
                <p style="margin: 0"><?php echo $ticketData['subject']; ?></p>
                <?php if($ticketData['status'] == 1){ ?>
                  <p><b><font color="green"><?php echo $ticketData['status_title']; ?></font></b></p>
                <?php } elseif($ticketData['status'] == 2){ ?>
                  <p><b><font color="green"><?php echo $ticketData['status_title']; ?></font></b></p>
                  <?php } elseif($ticketData['status'] == 3){ ?>
                    <p><b><font color="red"><?php echo $ticketData['status_title']; ?></font></b></p>
                  <?php } ?>
                </div>
                <div class="col-sm-3  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
               </div>  
              
            </div>
            <div class="card-body">
              <?php echo form_open_multipart('master/ticket/ticketResponseAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $ticketData['id'];?>" name="ticket_id">
              <div class="row">
              <div class="col-sm-12">
                <h4>Reply</h4>
                <hr />
              </div>  
              <div class="col-sm-6">
                <div class="form-group">
              <label><b>Message</b></label>
              <textarea class="form-control" rows="2" name="message" id="message"></textarea>
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
              <div class="col-sm-2 mt-4">
                <button type="submit" class="btn btn-success">Submit</button>
              </div>

              </div>
              <?php echo form_close(); ?>
              <?php if($replyList){ ?>
                <?php foreach($replyList as $list){ ?>
                  <div class="col-sm-12 ticket-block">
                    <div class="ticket-top-head">
                      <div class="row">
                        <div class="col-sm-9"><?php echo $list['member_name']; ?></div>
                        <div class="col-sm-3 text-right"><?php echo date('d-m-Y h:i:s A',strtotime($list['created'])); ?></div>
                      </div>
                      

                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                          <p><?php echo $list['message']; ?></p>
                          <p><b>Attachment -</b> 
                            <?php if($list['attachment']){ ?>
                              <a href="{site_url}<?php echo $list['attachment']; ?>" target="_blank">View Attachment</a>
                            <?php } else { ?>
                          No Attachment
                        <?php } ?>
                        </p>
                        </div>
                      </div>
                  </div>
                <?php } ?>
              <?php } ?>

              

            </div>
          </div>
        </div>

