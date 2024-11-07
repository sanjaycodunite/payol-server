{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Money Transfer</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('user/transfer/fundTransferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Sender*</b></label>
              <select class="form-control selectpicker" name="sender_id" id="sender_id" data-live-search="true">
                <option value="">Select Sender</option>
                <?php if($senderList){ ?>
                  <?php foreach($senderList as $list){ ?>
                    <option value="<?php echo $list['id']; ?>" <?php if($sender_id == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['name'].' ('.$list['mobile'].')'; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              <?php echo form_error('sender_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Beneficiary*</b></label>
              <select class="form-control selectpicker" name="bene_id" id="bene_id" data-live-search="true">
                <option value="">Select Beneficiary</option>
                <?php if($benList){ ?>
                  <?php foreach($benList as $list){ ?>
                    <option value="<?php echo $list['id']; ?>" <?php if($bene_id == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['account_holder_name'].' ('.$list['account_no'].')'; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              <?php echo form_error('bene_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Amount*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('amount'); ?>" name="amount" id="amount" placeholder="Amount">
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Transaction Type*</b></label> <br />
              <input type="radio" name="txnType" value="IFS" checked="checked" id="txnType3"> <label for="txnType3">IMPS <font style="font-size: 12px;">*Maximum 2 Lakh</font></label><br />
              <?php echo form_error('txnType', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

            </div>
         
              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to transfer this transaction?')">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>



