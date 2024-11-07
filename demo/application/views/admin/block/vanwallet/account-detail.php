<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Virtual Account Detail</b></h4>
                </div>
                <div class="col-sm-4 text-right">
                  <a href="{site_url}admin/vanwallet/upgradeAccountAuth" onclick="return confirm('Are you sure you want to regenerate Virtual Account Detail?')"><button type="button" class="btn btn-primary">Regenerate Account</button></a>
                </div>

               </div>  

            </div>
            <div class="card-body">
              <div class="alert alert-danger alert-dismissable">Please add a minimum amount of INR 10000. Amount less than the prescribed value is not allowed.</div>
              <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                  
                    <tr>
                      <th>Status</th>
                      <th><?php echo ($is_virtual_account) ? '<font color="green">Active</font>' : '<font color="red">Not Activated</font>'; ?></th>
                    </tr>
                    <tr>
                      <th>Virtual Account No.</th>
                      <th><?php echo ($virtual_account_no) ? $virtual_account_no : '<font color="red">Not Available</font>'; ?></th>
                      
                    </tr>
                    <tr>
                      <th>IFSC</th>
                      <th><?php echo $virtual_ifsc; ?></th>
                      
                    </tr>
                 
                </table>
              </div>
            </div>
          </div>
        </div>

