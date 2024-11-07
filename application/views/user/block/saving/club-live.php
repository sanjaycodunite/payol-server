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
                  <?php if($totalAmount){ ?>
                <a href="{site_url}user/saving/clearDuePayment/{club_id}/{requestID}" onclick="return confirm('Are you sure you want to clear due payment for this club?')"><button type="button" class="btn btn-danger">Pay <i class="fa fa-rupee-sign"></i> {totalAmount}</button></a>
                <?php } ?>
                <a href="{site_url}user/saving/clubList"><button type="button" class="btn btn-primary">Back</button></a>
                </div>
                
               </div>  
            </div>
            <div class="card-body">
              <input type="hidden" id="club_id" value="{club_id}">
              <input type="hidden" id="requestID" value="{requestID}">
              <div class="row">
                <?php if($memberList){ ?>
                <div class="col-lg-12">
                <div class="user_club_mamber">
                 <ul>
                  <?php foreach($memberList as $mlist){ ?>
                 <li class="user_club_list">
                 <img class="img-profile rounded-circle" src="{site_url}skin/admin/img/user.png"> 
                 <div class="club_mamber_details">
                  <span class="mamber_id"><?php echo $mlist['user_code']; ?></span>
                  <h5><?php echo $mlist['name']; ?></h5> 
                 </div> 
                 </li> 
                  <?php } ?>
                </ul> 
                </div>  
                </div>
                <?php } ?>

                <div class="m-auto col-lg-8">
                 <div class="club_Amount_list">
                  <div class="amount_list">
                   <h3><i class="fa fa-rupee-sign"></i> <?php echo $clubData['total_amount']; ?></h3>
                   <p>Club Amount</p> 
                  </div> 
                  <div class="amount_list">
                   <h3><i class="fa fa-rupee-sign"></i> <?php echo $clubData['per_member_amount']; ?></h3>
                   <p>Daily</p> 
                  </div>
                 </div> 
                </div>

                <div class="col-lg-12">
                 <div class="card_round_section"> 
                  <div class="card_titles text-center">
                   <h1 style="color: #5a5c69">Rounds</h1> 
                  </div>
                 <div class="list_round_box">
                  <ul>
                    <?php for($i = 1; $i<=$clubData['member_limit']; $i++){ ?>
                   <li>
                    <?php if($clubRoundStatus[$i]['status'] == 1){ ?>
                    <span><?php echo $i; ?></span>
                    <?php }elseif($clubRoundStatus[$i]['status'] == 2){ ?>
                    <span style="background:green;"><?php echo $i; ?></span>
                    <?php }elseif($clubRoundStatus[$i]['status'] == 3){ ?>
                    <span style="background:red;"><?php echo $i; ?></span>
                    <div class="round_list_text">
                      <?php if($clubRoundStatus[$i]['winner_member_id'] == 1){ ?>
                      <h6>Payol</h6>
                      <?php } else { ?>
                        <h6><?php echo $clubRoundStatus[$i]['winner_name']; ?></h6>
                      <?php } ?>
                      <p><i class="fa fa-rupee-sign"></i> <?php echo $clubRoundStatus[$i]['bid_amount']; ?></p>
                    </div>
                    <?php } ?>
                    </li> 
                    <?php } ?>
                     
                  </ul> 
                 </div> 
                </div></div>


                <div class="m-auto col-lg-6 col-md-12">
                 <div class="club_drop">
                   <div class="text-center">
                   <h1 style="color: #5a5c69">Total Earning</h1> 
                  </div>
                  <div class="club_dropdropdown text-center">
               <a class="club_dropBtn dropdown-toggle" href="#" id="clubDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fa fa-rupee-sign"></i> {totalEarning}
              </a>
              <!-- Dropdown - User Information -->
              <?php if($totalRoundEarning){ ?>
              <div class="club_drop_box dropdown-menu dropdown-menu-right shadow " aria-labelledby="clubDropdown">
                <?php foreach($totalRoundEarning as $elist){ ?>
                <a class="dropdown-item" href="#">
                 <b>Round <?php echo $elist['round_no']; ?></b> : <i class="fa fa-rupee-sign"></i> <?php echo $elist['devided_amount']; ?> (Earned)</a>
               <?php } ?>
              </div>
              <?php } ?>
            </div> 
                 </div> 
                </div>


          

          <?php if($clubData['status'] == 2){ ?>
            <div class="col-lg-12 col-md-12">
           <div class="bid_Section">
            <div><h4>This club is closed now</h4></div> 
            
           </div> 
          </div>       
            <div class="m-auto col-lg-8 col-md-12">
             <div class="club_chat_now text-center" id="club-live-bid-btn">
            <a href="{site_url}user/saving/clubChatLiveAuth/{club_id}/{requestID}"><i class="fa fa-comment"></i>Club Chat</a>
             </div> 
            </div>
          <?php } else { ?>
            <div class="col-lg-12 col-md-12">
           <div class="bid_Section">
            <div><h4>Bid date for Next Round #<?php echo $clubRoundData['round_no']; ?></h4></div> 
            <div><h5><?php echo date('d M Y h:i:s A',strtotime($clubRoundData['start_datetime'])); ?></h5></div> 
           </div> 
          </div>       
            <div class="col-lg-12 col-md-12">
             <div class="bid_Section_time">
              <div><h4>Bidding Starts in <span data-countdown="<?php echo $clubRoundData['start_datetime']; ?>">00 h 00 m 00 s</span></h4></div> 
             </div> 
            </div>  
            <?php if($clubRoundData['start_datetime'] < date('Y-m-d H:i:s')){ ?>
            <div class="m-auto col-lg-8 col-md-12">
             <div class="club_chat_now text-center">
            <a class="blink_me" href="{site_url}user/saving/clubChatLiveAuth/{club_id}/{requestID}"><i class="fa fa-comment"></i>View Live Bidding</a>
             </div> 
            </div>  
          <?php } else { ?>
            <div class="m-auto col-lg-8 col-md-12">
             <div class="club_chat_now text-center" id="club-live-bid-btn">
            <a href="{site_url}user/saving/clubChatLiveAuth/{club_id}/{requestID}"><i class="fa fa-comment"></i>Club Chat</a>
             </div> 
            </div>
          <?php } ?>
        <?php } ?>

              </div>

            </div>
          </div>
        </div>
      </div>

