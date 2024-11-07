<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('superadmin/master/saveAEPSCommission', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Disable Cash QR</b></h4>
                </div>

                <div class="col-sm-4 text-right">
                <button type="submit" class="btn btn-primary">Save</button>
                </div>

              </div>
            </div>
            
            <div class="card-body">
              <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-2 text-right">
                  <label><b>Select Account</b></label>
                </div>
                <div class="col-sm-3">
                  <select class="form-control selectpicker" data-live-search="true" name="memberID" id="selMemberID">
                    <option value="0">Select Account</option>
                    <?php if($accountList){ ?>
                      <?php foreach($accountList as $list){ ?>
                        <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-2">
                <button type="button" id="cashQrSearchBtn" class="btn btn-success">Search</button>
                </div>
                <div class="col-sm-12 text-center recharge-comm-loader">
                </div>

              </div>
              
              <div class="table-responsive" id="dmr-comm-block">
                
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

