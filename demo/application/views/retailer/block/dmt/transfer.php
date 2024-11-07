{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Trasfer Money</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('retailer/dmt/transferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              
			        <div class="col-sm-3">
              <div class="form-group">
              <label><b>Beneficiary*</b></label>
              <select class="form-control selectpicker" data-live-search="true" name="benId" id="selDmtBenId">
                  <option value="0">Select Beneficiary</option>
                  <?php if($beneficiaryList){ ?>
                    <?php foreach($beneficiaryList as $list){ ?>
                      <option value="<?php echo $list['beneId']; ?>" <?php if($benId == $list['beneId']){ ?> selected="selected" <?php } ?>><?php echo $list['account_holder_name']; ?> (<?php echo $list['bank_name']; ?>)</option>
                    <?php } ?>
                  <?php } ?>
                </select>
              <?php echo form_error('benId', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Account No.*</b></label>
              <input type="text" class="form-control" readonly="readonly" value="{account_no}" id="ben_account_no" placeholder="Account No.">
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>IFSC*</b></label>
              <input type="text" class="form-control" readonly="readonly" value="{ifsc}" id="ben_ifsc" placeholder="IFSC">
              </div>
              </div>
        <div class="col-sm-2">
              <div class="form-group">
              <label><b>Amount*</b></label>
              <input type="text" class="form-control" value="<?php echo set_value('amount'); ?>" name="amount" id="amount" placeholder="Amount">
              <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
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




