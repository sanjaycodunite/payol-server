<div class="container-fluid">
  {system_message}               
  {system_info}
<div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b> <?php echo $clubData['club_name']; ?></b></h4>
                </div>
                <div class="col-sm-6 text-right">
                  <a href="{site_url}distributor/saving/clubLiveAuth/{club_id}/{requestID}"><button type="button" class="btn btn-primary">Back</button></a>
                </div>
                
               </div>  
            </div>
            <?php echo form_open_multipart('#',array('method'=>'post','id'=>'clubChatForm')); ?>
            <div class="card-body">
              <input type="hidden" id="club_id" value="{club_id}">
              <input type="hidden" id="requestID" value="{requestID}">
              <input type="hidden" id="roundNo" value="{roundNo}">
              <input type="hidden" id="isLive" value="{isLive}">
              <input type="hidden" id="lastChatDatetime" value="">
              <div class="row">
                
                <div class="col-lg-12">
                <div class="user_club_mamber" id="club-live-member-block">
                 
                </div>  
                </div>
                

                <div class="m-auto col-lg-8">
                  <h3 style="text-align: center;">Chat</h3>
                 <div class="chat_box_head" id="club-live-header" <?php if(!$isLive){ ?> style="display: none;" <?php } ?>>
                 <div>
                 <h4 id="club-last-member-name">{lastMemberName} Bid for </h4>  
                 </div> 
                 <div class="online_user_amount">
                 <h5 class="blink_price" id="club-total-round-amount"><i class="fa fa-rupee"></i> {totalBidAmount}</h5>  
                 </div> 
                 <div class="online_user_time">
                 <h5 id="chat-countdown" data-endtime="<?php echo $start_datetime; ?>"><i class="fa fa-clock"></i> 00:00</h5>  
                 </div>
                 </div> 
                 <div class="chat_area_box">
                 <div class="chat_body" id="club-chat-block">
                 


                  </div> 


                 <div class="chat_footer">
                 <div class="chat_taxtarea">
                  <input type="text" name="message" id="update-task-message" autocomplete="off" class="form-control">
                  <div class="form-group" id="update-task-comment-loader"></div>
                </div>  
                 <div class="chat_btn">
                 <button type="button" class="btn btn-send" id="update-task-comment-btn"><i class="fa fa-comment"></i></button>
                 <button type="button" class="btn btn-bid" id="club-bid-btn"><i class="fa fa-rupee"></i></button>   
                 </div>
                 </div>


                </div></div> 

                
  



         

              </div>

            </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>

