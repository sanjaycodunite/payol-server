<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Virtual Account Detail</b></h4>
                </div>

               </div>  

            </div>
            <div class="card-body">
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
                      <th><?php echo VIRTUAL_ACCOUNT_IFSC; ?></th>
                      
                    </tr>
                 
                </table>
              </div>
            </div>
          </div>
        </div>

