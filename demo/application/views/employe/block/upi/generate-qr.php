{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Generate QR for UPI Collection</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/upi/generateQrAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              
        <div class="col-sm-2">
              <div class="form-group">
              <label><b>No. of QR*</b></label>
              <input type="text" class="form-control" name="qr_count" id="qr_count" placeholder="QR No.">
              <?php echo form_error('qr_count', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              </div>

              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-left">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




