<div class="container-fluid">
<div class="row">
<div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Success</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessRechargeBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Success Charge</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessChargeBlock">&#8377; 0.00</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Failed</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFailedRechargeBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Chargeback</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalChargebackRechargeBlock">&#8377; 0.00 / 0</div>
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
                <div class="col-sm-12 mb-3">
                <h4><b>UPI Collection Report</b></h4>
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

                 <!-- <div class="col-sm-2">
                  <select class="form-control selectpicker" data-live-search="true" name="user" id="user">
                    <option value="">All User</option>
                  </select>
                </div> -->

                <div class="col-sm-3">
              <div class="form-group">             
              <select class="form-control" name="user" id="user">
                <option value="">All User</option>
                
              </select>
              
              
              </div>
              </div>

               <div class="col-sm-3">
                  <select class="form-control" name="api_type" id="api_type">
                    <option value="">All Api</option>
                    <?php
                    foreach($upi_api as $list){
                    ?>
                    <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                  <?php } ?>
                  </select>
                </div>


                
                <div class="col-sm-3">
                  <select class="form-control selectpicker" data-live-search="true" name="type" id="type">
                     <option value="0">All Status</option>
                  
                  <option value="2" <?php if($status == 2){ ?> selected="selected" <?php } ?>>Success</option>
                   <option value="4" <?php if($status == 4){ ?> selected="selected" <?php } ?>>Chargeback</option>
                  </select>
                </div>


                <div class="col-sm-3">
                <input type="text" class="form-control datepick" placeholder="Start Date" autocomplete="off" name="from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-3">
                <input type="text" class="form-control datepick" placeholder="End Date" autocomplete="off" name="to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-3">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-12 mt-3 text-center">
                <button type="button" class="btn btn-success" id="upiSearchBtn">Search</button>
                
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="upiDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>txnID</th>
                      <th>Bank RRN</th>
                      <th>Amount</th>
                      <th>Charge</th>
                      <th>Credit</th>
                      <th>VPA ID</th>
                      <th>Description</th>
                      <th>Date Time</th>
                      <th>Status</th>
                      <th>Action</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>txnID</th>
                      <th>Bank RRN</th>
                      <th>Amount</th>
                      <th>Charge</th>
                      <th>Credit</th>
                      <th>VPA ID</th>
                      <th>Description</th>
                      <th>Date Time</th>
                      <th>Status</th>
                      <th>Action</th>
                      
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
