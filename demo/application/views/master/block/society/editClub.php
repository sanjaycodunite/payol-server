{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Update Club</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('master/society/updateClub', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $id;?>" name="id">
              <div class="row">
                <div class="col-sm-3">
              <div class="form-group">
              <label><b>Club Name*</b></label>
              <input type="text" class="form-control" value="<?php echo $clubData['club_name']; ?>" name="club_name" id="club_name" placeholder="Club Name">
              <?php echo form_error('club_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Member Limit*</b></label>
              <input type="text" class="form-control" value="<?php echo $clubData['member_limit']; ?>" name="member_limit" id="member_limit" placeholder="Member Limit">
              <?php echo form_error('member_limit', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Total Amount*</b></label>
              <input type="text" class="form-control" value="<?php echo $clubData['total_amount']; ?>" name="total_amount" id="total_amount" placeholder="Total Amount">
              <?php echo form_error('total_amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-2">
                <div class="form-group">
              <label><b>Commission*</b></label>
              <input type="text" class="form-control" value="<?php echo $clubData['commission']; ?>" name="commission" id="commission" placeholder="Commission">
              <?php echo form_error('commission', '<div class="error">', '</div>'); ?>  
              </div>
              
              </div>

              <div class="col-sm-1">
                
                <div class="form-group" style="margin-top: 40px;">
                <input type="checkbox" name="is_flat" <?php if($clubData['is_flat'] == 1){ ?> checked="checked" <?php } ?> value="1" id="is_flat">
                <label for="is_flat">Is Flat?</label>
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Tenure Type*</b></label>
              <select class="form-control" name="tenure_type">
              <option value="">Select Type</option>
              <?php if($tenureType){ ?>
                <?php foreach($tenureType as $list){ ?>
                  <option value="<?php echo $list['id']; ?>" <?php if($clubData['tenure_type'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                <?php } ?>
              <?php } ?>
              </select>
              <?php echo form_error('tenure_type', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Min Bid Amount*</b></label>
              <input type="text" class="form-control" value="<?php echo $clubData['min_bid_amount']; ?>" name="min_bid_amount" id="min_bid_amount" placeholder="Min Bid Amount">
              <?php echo form_error('min_bid_amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Bid Difference Amount*</b></label>
              <input type="text" class="form-control" value="<?php echo $clubData['bid_diff_amount']; ?>" name="diff_amount" id="diff_amount" placeholder="Min Bid Difference Amount">
              <?php echo form_error('diff_amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Start Date*</b></label>
              <input type="text" class="selectpicker form-control" value="<?php echo $clubData['start_date']; ?>" name="start_date" id="start_date" placeholder="Start Date">
              <?php echo form_error('start_date', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <?php $timing = explode(':', $clubData['state_time']); ?>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Auction Start Timing*</b></label>
              <div class="row">
                <div class="col-sm-4">
              <select class="form-control" name="auction_hour">
                <option value="">Hour</option>
                <?php for($i = 0; $i <= 23; $i++){ ?>
                  <?php if($i < 10){ ?>
                  <option value="0<?php echo $i;?>" <?php if($timing[0] == '0'.$i){ ?> selected="selected" <?php } ?>>0<?php echo $i;?></option>
                  <?php } else { ?>
                  <option value="<?php echo $i;?>" <?php if($timing[0] == $i){ ?> selected="selected" <?php } ?>><?php echo $i;?></option>
                  <?php } ?>
                <?php } ?>
              </select>
                </div>
                <div class="col-sm-4">
              <select class="form-control" name="auction_min">
                <option value="">Min</option>
                <?php for($i = 0; $i <= 60; $i++){ ?>
                  <?php if($i < 10){ ?>
                  <option value="0<?php echo $i;?>" <?php if($timing[1] == '0'.$i){ ?> selected="selected" <?php } ?>>0<?php echo $i;?></option>
                  <?php } else { ?>
                  <option value="<?php echo $i;?>" <?php if($timing[1] == $i){ ?> selected="selected" <?php } ?>><?php echo $i;?></option>
                  <?php } ?>
                <?php } ?>
              </select>
            </div>
            <div class="col-sm-4">
              <select class="form-control" name="auction_sec">
                <option value="">Sec</option>
                <?php for($i = 0; $i <= 60; $i++){ ?>
                  <?php if($i < 10){ ?>
                  <option value="0<?php echo $i;?>" <?php if($timing[2] == '0'.$i){ ?> selected="selected" <?php } ?>>0<?php echo $i;?></option>
                  <?php } else { ?>
                  <option value="<?php echo $i;?>" <?php if($timing[2] == $i){ ?> selected="selected" <?php } ?>><?php echo $i;?></option>
                  <?php } ?>
                <?php } ?>
              </select>
            </div>
              
            </div>
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Auction Duration (Min)*</b></label>
              <div class="row">
                
                <div class="col-sm-12">
              <select class="form-control" name="auction_duration">
                <option value="">Min</option>
                <?php for($i = 0; $i <= 60; $i++){ ?>
                  <?php if($i < 10){ ?>
                  <option value="0<?php echo $i;?>" <?php if($clubData['duration'] == '0'.$i){ ?> selected="selected" <?php } ?>>0<?php echo $i;?></option>
                  <?php } else { ?>
                  <option value="<?php echo $i;?>" <?php if($clubData['duration'] == $i){ ?> selected="selected" <?php } ?>><?php echo $i;?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              <?php echo form_error('auction_duration', '<div class="error">', '</div>'); ?>  
            </div>
              
            </div>
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Payment Debit Duration Before Start Round (Min)*</b></label>
              <div class="row">
                
                <div class="col-sm-12">
              <select class="form-control" name="payment_debit_duration">
                <option value="">Min</option>
                <?php for($i = 0; $i <= 60; $i++){ ?>
                  <?php if($i < 10){ ?>
                  <option value="0<?php echo $i;?>" <?php if($clubData['payment_debit_duration'] == '0'.$i){ ?> selected="selected" <?php } ?>>0<?php echo $i;?></option>
                  <?php } else { ?>
                  <option value="<?php echo $i;?>" <?php if($clubData['payment_debit_duration'] == $i){ ?> selected="selected" <?php } ?>><?php echo $i;?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              <?php echo form_error('payment_debit_duration', '<div class="error">', '</div>'); ?>  
            </div>
              
            </div>
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Reserve No*</b></label>
              <input type="text" class="form-control" value="<?php echo $clubData['reserve_no']; ?>" name="reserve_no" id="reserve_no" placeholder="Reserve No">
              <?php echo form_error('reserve_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
               <div class="form-group">
                <label><b>Status</b></label>
                <select class="form-control" name="is_active">
                <option value="1" <?php if($clubData['status'] == 1){ ?> selected="selected" <?php } ?>>Active</option>
                <option value="0" <?php if($clubData['status'] == 0){ ?> selected="selected" <?php } ?>>Deactive</option>  
                <option value="2" <?php if($clubData['status'] == 2){ ?> selected="selected" <?php } ?>>Close</option>  
                </select>
                <?php echo form_error('is_active  ', '<div class="error">', '</div>'); ?>  
               </div>
               </div> 

               <div class="col-sm-12">
               <div class="form-group">
                <label><b>Terms & Conditions</b></label>
                <textarea class="form-control" rows="5" name="terms"><?php echo $clubData['terms']; ?></textarea>
                <?php echo form_error('terms  ', '<div class="error">', '</div>'); ?>  
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



