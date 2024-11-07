<div class="container-fluid">
<div class="row">
<div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total TDS Amount</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessTdsBlock">&#8377; 0.00</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>

               
              
</div>
<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Member TDS Report</b></h4>
                </div>


                    <div class="col-sm-2">
                  <select class="form-control selectpicker" data-live-search="true" name="user" id="user">
                    <option value="">All User</option>
                    <?php
                    foreach($get_user_list as $list){
                    ?>
                    <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?>(<?php echo $list['user_code']; ?>)</option>
                  <?php } ?>
                  </select>
                </div>


                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="Start Date" autocomplete="off" name="from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="End Date" autocomplete="off" name="to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-12 mt-5 text-center">
                <button type="button" class="btn btn-success" id="tdsSearchBtn">Search</button>
                <a href="{site_url}employe/report/tdsReport" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tdsDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Member ID</th>
                      <th>Member Name</th>
                      <th>Commision Amount</th>
                      <th>TDS Amount</th>
                      <th>Description</th>
                      <th>Datetime</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Member ID</th>
                      <th>Member Name</th>
                      <th>Commision Amount</th>
                      <th>TDS Amount</th>
                      <th>Description</th>
                      <th>Datetime</th>
                      
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