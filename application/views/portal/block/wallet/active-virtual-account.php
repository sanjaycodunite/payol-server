{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Activate Virtual Account</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('portal/wallet/topupAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
           <div class="row">
              <!--<div class="col-sm-3">-->
              <!--<div class="form-group">-->
              <!--<label><b>Amount*</b></label>-->
              <!--<input type="text" class="form-control" name="amount" id="amount" placeholder="Amount">-->
              <!--<?php echo form_error('amount', '<div class="error">', '</div>'); ?>  -->
              <!--</div>-->
              <!--</div>-->
              
              
              <table class="table">
  <thead>
    <tr>
      <th scope="col">Service</th>
      <th scope="col">Status</th>
      
    </tr>
  </thead>
  <tbody>
            <tr>
              <th scope="row">Virtual Account</th>
              <td><a class="btn btn-primary" href="{site_url}portal/wallet/virtualAccountAuth"> Active</a></td>
            </tr>
   
  </tbody>
</table>

          
              </div>

              
              
          </div>
        </div>
        
 <?php echo form_close(); ?>     
    </div>




