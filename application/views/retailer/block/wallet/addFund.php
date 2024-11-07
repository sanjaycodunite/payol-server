<!-- {system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add Fund</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            
            
              <div class="row">
              <div class="col-sm-12">
                <div class="add_fund_btn">
                <a href="{site_url}retailer/wallet/sendRequest"><i class="fa fa-sign-in-alt"></i>Send Request</a>
                <a href="{site_url}retailer/wallet/dynamicQr"><i class="fa fa-qrcode"></i>Dynamic QR</a>
                <?php if($chk_qr_status){ ?>
                <a href="{site_url}retailer/wallet/activeQr"><i class="fa fa-qrcode"></i>View QR</a>
                <?php } else { ?>
                <a href="{site_url}retailer/wallet/activeQr" onclick="return confirm('Are you sure you want to active QR?')"><i class="fa fa-qrcode"></i>Active QR</a>
                <a href="{site_url}retailer/wallet/mapQr"><i class="fa fa-qrcode"></i>Map QR</a>
                <?php } ?>
              </div></div>
			 
              </div>

              
              
          </div>
        </div>
        
    </div>




 -->


 {system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add Fund QR</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('retailer/wallet/upiAddFundAuth',array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
           <div class="row">
              
              <div class="col-sm-4">
                <div class="form-group">
                <label><b>Amount*</b></label>
                <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount">
                <?php echo form_error('amount', '<div class="error">', '</div>'); ?>  
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




