{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Update Club (<?php echo $clubData['club_name'];?>) Round #<?php echo $roundData['round_no'];?></b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('employe/society/updateClubRoundAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $roundID;?>" name="roundID">
              <input type="hidden" value="<?php echo $club_id;?>" name="club_id">
              <div class="row">
               

              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Start Date*</b></label>
              <input type="text" class="selectpicker form-control" value="<?php echo date('Y-m-d',strtotime($roundData['start_datetime'])); ?>" name="start_date" id="start_date" placeholder="Start Date">
              <?php echo form_error('start_date', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <?php $timing = explode(':', date('H:i:s',strtotime($roundData['start_datetime']))); ?>
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
              
             
              
              </div>





              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="submit" class="btn btn-success">Submit for This Round</button>
        <button type="submit" name="all" class="btn btn-success">Submit for all Round</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




