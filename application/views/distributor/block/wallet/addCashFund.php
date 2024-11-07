{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>UPI Cash</b></h4>
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
                <a href="{site_url}distributor/wallet/sendCashRequest"><i class="fa fa-sign-in-alt"></i>Send Request</a>
                <a href="{site_url}distributor/wallet/dynamicCashQr"><i class="fa fa-qrcode"></i>Dynamic QR</a>
                <?php if($chk_qr_status){ ?>
                <a href="{site_url}distributor/wallet/activeCashQr"><i class="fa fa-qrcode"></i>View QR</a>
                <?php } else { ?>
                <a href="{site_url}distributor/wallet/activeCashQr" onclick="return confirm('Are you sure you want to active QR?')"><i class="fa fa-qrcode"></i>Active QR</a>
                <a href="{site_url}distributor/wallet/mapCashQr"><i class="fa fa-qrcode"></i>Map QR</a>
                <?php } ?>
              </div></div>
			 
              </div>

              
              
          </div>
        </div>
        
    </div>




