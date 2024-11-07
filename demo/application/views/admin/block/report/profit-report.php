<div class="container-fluid">
<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Profit Report</b></h4>
                </div>
                
                
                <div class="col-sm-2">
                  <select class="form-control" name="user_type" id="user_type">
                    <option value="">All User</option>
                    <?php
                    foreach($user_type as $list){
                    ?>
                    <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                  <?php } ?>
                  </select>
                </div>

                

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                
                <div class="col-sm-4">
                  <div class="form-group">
                  <button type="button" class="btn btn-success" id="profitReportSearchBtn">Search</button>
                  <a href="{site_url}admin/report/profitReport" class="btn btn-secondary">View All</a>
                 </div>
                </div>

               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="profitReportDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Total <br />Collection</th>
                      <th>Collection <br />Commision</th>
                      <th>Total <br />Payout</th>
                      <th>Payout <br />Commision</th>
                      <th>Total <br />Commision</th>
                      <th>Distribute <br />Commision</th>
                      <th>Profit <br />Balance</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Total <br />Collection</th>
                      <th>Collection <br />Commision</th>
                      <th>Total <br />Payout</th>
                      <th>Payout <br />Commision</th>
                      <th>Total <br />Commision</th>
                      <th>Distribute <br />Commision</th>
                      <th>Profit <br />Balance</th>
                      
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
</div>
