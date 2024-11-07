{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Capture Finger Print</b></h4>
                </div>
                
                <div class="col-sm-4  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('distributor/iciciaeps/otpAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $memberID;?>" name="memberID">
              <input type="hidden" value="<?php echo $encodeFPTxnId;?>" name="encodeFPTxnId" id="encodeFPTxnId">
            <div class="row">
              <div class="col-sm-12">
                <h5>Device</h5>
                <hr />
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="deviceType" id="deviceType1" checked="checked" value="MANTRA_PROTOBUF">
                  <label for="deviceType1"><b>Mantra</b></label>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="deviceType" id="deviceType2" value="MORPHO_PROTOBUF">
                  <label for="deviceType2"><b>Morpho</b></label>
                </div>
              </div>

              
            </div>
            <div class="row">
              <div class="col-sm-3">
              <button type="button" class="btn btn-success" onclick="KycCapture();">Scan & Submit</button>
              </div>

              <div class="col-sm-12 aeps-response">
              </div>
              

           
              
              </div>

              
              
          </div>
        </div>
        
 <?php echo form_close(); ?>     
    </div>




