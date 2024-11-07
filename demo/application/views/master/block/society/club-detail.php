<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-6">
                <h4><b><?php echo $clubData['club_name']; ?> Detail</b></h4>
                </div>

                <div class="col-sm-6 text-right">
                <a href="{site_url}master/society" class="btn btn-secondary">Back</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-12">
                  <h4>Request List</h4>
                  <hr />
                </div>
                <div class="col-sm-12">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Member</th>
                          <th>Request Type</th>
                          <th>Status</th>
                          <th>Datetime</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if($requestList){ ?>
                          <?php $i = 1; foreach($requestList as $list){ ?>
                            <tr>
                              <td><?php echo $i; ?>.</td>
                              <td><?php echo $list['user_code'].'<br />'.$list['name']; ?></td>
                              <?php if($list['action_type'] == 1){ ?>
                                <td><font color="green">Accepted</td>
                              <?php } else { ?>
                                <td><font color="red">Declined</td>
                              <?php } ?>
                              <?php if($list['status'] == 1){ ?>
                                <td><font color="black">Request Sent</td>
                              <?php } elseif($list['status'] == 2){ ?>
                                <td><font color="green">Accepted</td>
                              <?php } else { ?>
                                <td><font color="red">Declined</td>
                              <?php } ?>
                              <td><?php echo date('d-m-Y H:i:s',strtotime($list['created'])); ?></td>
                              <td>
                                <?php if($list['status'] == 1){ ?>
                                <a href="{site_url}master/society/requestAuth/<?php echo $list['id']; ?>/<?php echo $list['club_id']; ?>/1" class="btn btn-success btn-sm" onclick="return confirm('Are you sure want to approve this request?')"><i class="fa fa-check"></i></a>
                                <a href="{site_url}master/society/requestAuth/<?php echo $list['id']; ?>/<?php echo $list['club_id']; ?>/2" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure want to decline this request?')"><i class="fa fa-times"></i></a>
                                <?php } ?>
                                <a href="{site_url}master/society/requestAuth/<?php echo $list['id']; ?>/<?php echo $list['club_id']; ?>/3" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure want to delete?')"><i class="fa fa-trash"></i></a></td>
                            </tr>
                          <?php $i++; } ?>
                        <?php } else { ?>
                          <tr>
                            <td colspan="6" align="center">No Request Found.</td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="col-sm-12">
                  <h4>Club Rounds</h4>
                  <hr />
                </div>
                <div class="col-sm-12">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Round</th>
                          <th>Start Datetime</th>
                          <th>End Datetime</th>
                          <th>Status</th>
                          <th>Winner</th>
                          <th>Win Amount</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if($roundList){ ?>
                          <?php $i = 1; foreach($roundList as $list){ ?>
                            <tr>
                              <td><?php echo $i; ?>.</td>
                              <td><?php echo $list['round_no']; ?></td>
                              <td><?php echo date('d-m-Y H:i:s',strtotime($list['start_datetime'])); ?></td>
                              <?php if($list['close_datetime']){ ?>
                                <td><?php echo date('d-m-Y H:i:s',strtotime($list['close_datetime'])); ?></td>
                              <?php } else { ?>
                                <td>-</td>
                              <?php } ?>
                              
                              <?php if($list['status'] == 1){ ?>
                                <td><font color="black">Not Started</td>
                              <?php } elseif($list['status'] == 2){ ?>
                                <td><font color="green">LIVE</td>
                              <?php } else { ?>
                                <td><font color="red">Close</td>
                              <?php } ?>
                              <?php if($list['status'] == 3){ ?>
                                <?php if($list['winner_member_id'] == 1){ ?>
                                <td>Payol</td>
                                <?php } else { ?>
                                  <td><?php echo $list['name'].'<br />'.$list['user_code']; ?></td>
                                <?php } ?>
                              <?php } else { ?>
                                <td>Not Found</td>
                              <?php } ?>
                              <td>&#8377; <?php echo $list['win_amount']; ?>
                                <br />
                                <?php if($list['is_paid']){ ?>
                                  <font color="red">Already Paid</font>
                                <?php } elseif($list['winner_member_id'] != 1) { ?>
                                  <a href="{site_url}master/society/payWinAmountAuth/<?php echo $list['id']; ?>/{id}" onclick="return confirm('Are you sure you want to pay?')">Pay Now</a>
                                <?php } ?>
                              </td>
                              <td>
                                <?php if($list['status'] == 1){ ?>
                                <a href="{site_url}master/society/editClubRound/<?php echo $list['id']; ?>/<?php echo $list['club_id']; ?>" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                                <?php } elseif($list['status'] == 3){ ?>
                                  <a href="#" onclick="clubChatModal(<?php echo $list['round_no']; ?>,<?php echo $list['club_id']; ?>); return false;" class="btn btn-sm btn-success">View Chat</a>
                                <?php } ?>
                                </td>
                            </tr>
                          <?php $i++; } ?>
                        <?php } else { ?>
                          <tr>
                            <td colspan="6" align="center">No Request Found.</td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>


              </div>
            </div>
          </div>
        </div>

        <div id="updateComplainModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('#',array('method'=>'post')); ?>
    <div class="modal-header">
    <h4 class="modal-title">View Chat</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    
    
    </div>
    <div class="modal-body">
    
    <div class="modalform">
      <div class="row">
        <div class="col-lg-12">
          <div class="user_club_mamber" id="club-live-member-block">
           
          </div>  
        </div>
        <div class="col-md-12" id="complainMsgBlock"></div>
      </div>
    </div>
    
    </div>
    <?php echo form_close(); ?>
    
  </div>

  </div>
</div>  

