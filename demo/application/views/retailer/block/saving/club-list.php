<div class="container-fluid">
  {system_message}               
  {system_info}
<div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Payol Club</b></h4>
                </div>
                
               </div>  
            </div>
            <div class="card-body">
              
              <div class="row">
                <?php if($clubList){ ?>
                  <?php foreach($clubList as $list){ ?>
                  
                  <div class="col-sm-6 club-block">
                    <?php echo form_open_multipart('retailer/saving/clubRequestAuth'); ?>
                    <input type="hidden" name="club_id" value="<?php echo $list['id']; ?>">
                    <h1><?php echo $list['club_name']; ?></h1>
                    <table class="table table-bordered table-striped">
                      <tr>
                        <td><b>Club Amount</b></td>
                        <td>&#8377; <?php echo $list['total_amount']; ?></td>
                        <td><b>Total Member</b></td>
                        <td><?php echo $list['member_limit']; ?></td>
                      </tr>
                      
                      <tr>
                        <td><b>Contribution Amount</b></td>
                        <td>&#8377; <?php echo $list['per_member_amount']; ?></td>
                        <td><b>Commission</b></td>
                        <?php if($list['is_flat']){ ?>
                        <td>&#8377; <?php echo $list['commission']; ?></td>
                        <?php } else { ?>
                        <td><?php echo $list['commission']; ?>%</td>
                        <?php } ?>
                      </tr>
                      
                      <tr>
                        <td><b>Tenure</b></td>
                        <?php if($list['tenure_type'] == 1){ ?>
                        <td>Daily</td>
                        <?php } elseif($list['tenure_type'] == 2){ ?>
                        <td>Weekly</td>
                        <?php } elseif($list['tenure_type'] == 3){ ?>
                        <td>Half Monthly</td>
                      <?php } elseif($list['tenure_type'] == 4){ ?>
                        <td>Monthly</td>
                        <?php } ?>
                        <td><b>Minimum Bid Amount</b></td>
                        <td>&#8377; <?php echo $list['min_bid_amount']; ?></td>
                      </tr>
                      
                      <tr>
                        <td><b>Difference Bid Amount</b></td>
                        <td>&#8377; <?php echo $list['bid_diff_amount']; ?></td>
                        <td><b>Start Date</b></td>
                        <td><?php echo date('d-M-Y',strtotime($list['start_date'])); ?></td>
                      </tr>
                      
                      <tr>
                        <td><b>Bid Timing</b></td>
                        <td><?php echo $list['state_time']; ?></td>
                        <td><b>Bid Duration</b></td>
                        <td><?php echo $list['duration']; ?> Min</td>
                      </tr>
                      <tr>
                        <td><b>Status</b></td>
                        <?php if($list['status'] == 1) { ?>
                        <td colspan="3"><font color="green">Active</font></td>
                        <?php } elseif($list['status'] == 2) { ?>
                        <td colspan="3"><font color="red">Close</font></td>
                        <?php } ?>
                        
                      </tr>
                      <?php if($list['member_status'] != 2 && $list['status'] != 2 && !$list['isClubFull']){ ?>
                      <tr>
                        <td colspan="4">
                          <label>1. If club amount is more than &#8377; 10,000, You need to send physical documents with signature to registered company address with a blank cheque. If you are agree with this terms Please check the box.</label><br />
                          <input type="checkbox" name="is_agree" value="1" id="is_agree">
                          <label for="is_agree">I agree with all Terms & Conditions</label>
                        </td>
                      </tr>
                      <?php } ?>
                      <?php if($list['member_action'] == 0 && $list['status'] != 2 && !$list['isClubFull']){ ?>
                      <tr>
                        <td colspan="2" align="center"><button type="submit" name="accept" class="btn btn-success" onclick="return confirm('Are you sure want to accept this club?')">Accept</button></td>
                        <td colspan="2" align="center"><button type="submit" name="decline" class="btn btn-danger" onclick="return confirm('Are you sure want to decline this club?')">Decline</button></td>
                      </tr>
                      <?php } elseif($list['member_action'] == 0 && $list['status'] != 2 && $list['isClubFull']){ ?>
                        <tr>
                          <td colspan="4" align="center">
                            <font color="red">You are not authorized for any activity in this club, Club is already full.</font>
                          </td>
                        </tr>
                      <?php } elseif($list['member_action'] == 1 && $list['member_status'] == 1){ ?>
                        <tr>
                          <td colspan="4" align="center">
                            <font style="color: green; font-size: 20px;">Request Sent</font>
                          </td>
                        </tr>
                      <?php } elseif($list['member_action'] == 1 && $list['member_status'] == 2){ ?>
                        <tr>
                          <td colspan="4" align="center">
                            <a href="{site_url}retailer/saving/clubLiveAuth/<?php echo $list['id']; ?>/<?php echo $list['requestID']; ?>"><button type="button" class="btn btn-success">View LIVE</button></a>
                          </td>
                        </tr>
                      <?php } elseif($list['member_action'] == 1 && $list['member_status'] == 3){ ?>
                        <tr>
                          <td colspan="4" align="center">
                            <font style="color: red; font-size: 20px;">Declined</font>
                          </td>
                        </tr>
                      <?php } elseif($list['member_action'] == 2){ ?>
                        <tr>
                          <td colspan="4" align="center">
                            <font style="color: red; font-size: 20px;">Declined</font>
                          </td>
                        </tr>
                      <?php } ?>
                    </table>
                    <?php echo form_close(); ?>
                  </div>
                  
              <?php } ?>
              <?php } else { ?>
                <div class="col-sm-12"><p>Sorry ! No Morning Club Found.</p></div>
              <?php } ?>
              </div>
              <hr />
              <h3>Close Club List</h3>
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="closeClubDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Club Name</th>
                      <th>Club Amount</th>
                      <th>Per Member Amount</th>
                      <th>Tenure</th>
                      <th>Start Date</th>
                      <th>Start Time</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Club Name</th>
                      <th>Club Amount</th>
                      <th>Per Member Amount</th>
                      <th>Tenure</th>
                      <th>Start Date</th>
                      <th>Start Time</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>

