{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Aadhar Pay Two Factor Authenticate</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                  
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('master/transfer/transferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" name="serviceType"  id="serviceType1"  value="2FAAuth">
              <input type="hidden" name="serviceType"  id="amount"  value="0">
              <div class="row">
             
			         
            </div>
             <div class="row">
              <div class="col-sm-12">
                <h5>Device</h5>
                <hr />
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="deviceType" id="deviceType1" value="MANTRA_PROTOBUF">
                  <label for="deviceType1"><b>Mantra</b></label>
                </div>
              </div>

           
              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" checked="checked" name="deviceType" id="deviceType2" value="MORPHO_PROTOBUF"  <?php if($account_id == 3) {?> checked="" <?php } ?>>
                  <label for="deviceType2"><b>Morpho</b></label>
                </div>
              </div>


              
            </div>
            <div class="row">
              <div class="col-sm-12">
                <h5>Personal Detail</h5>
                <hr />
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Mobile No.*</b></label>
              <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile No." maxlength="10" value="<?php echo $get_member_data['mobile'] ?>" readonly>
              
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Aadhar No*</b></label>
              <input type="text" class="form-control" name="aadhar_no" id="aadhar_no" placeholder="Aadhar No" maxlength="12" value="<?php echo set_value('aadhar_no') ?>">
              
              </div>
              </div>

              </div>

              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="button" class="btn btn-success" onclick="CallMemberCaptureAp();">Scan & Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>

<!-- Modal -->
<div id="aepsResponseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">AEPS Response</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body aeps-response">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" width="100%" cellspacing="0">
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>CR/DR</th>
              <th>Amount</th>
              <th>Description</th>
            </tr>
          </table>
        </div>
      </div>
      
    </div>

  </div>
</div>




