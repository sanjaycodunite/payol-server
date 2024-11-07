<div class="container-fluid">
<div class="row">
<div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="walet_list_card card border-left-success shadow">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Credit</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSuccessIciciBlock">&#8377; 0.00 / 0</div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>



                <div class="col-xl-4 col-md-6 mb-2 mt-2">
                  <div class="walet_list_card card border-left-danger shadow">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><a href="#">Total Debit</a></div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPendingIciciBlock">&#8377; 0.00 / 0</div>
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
              <div class="row">
                <div class="col-sm-3">
                <h4><b>Wallet History</b></h4>
                </div>
                <div class="col-sm-12"></div>
                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="Start Date" autocomplete="off" name="from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control datepick" placeholder="End Date" autocomplete="off" name="to_date" id="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
                

                <div class="col-sm-2">
                 <select class="selectpicker form-control" name="member_id" id="member_id" data-live-search="true">

                <option value="">Select Member</option>
                <?php if($memberList){ ?>
                  <?php foreach($memberList as $list){ ?>
                    <option value="<?php echo $list['id']; ?>" <?php if(isset($id) && $id == $list['id']){ ?> selected="selected" <?php } ?>><?php echo ucwords($list['name']).' ('.$list['user_code'].')'; ?></option>  
                  <?php } ?>
                <?php } ?>
                </select>    
                </div>  
                 <div class="col-sm-2">
                <select class="form-control" id="status" name="status">
                  <option value="0">All Status</option>
                  
                  <option value="1" <?php if(isset($status) && $status == 1){ ?> selected="selected" <?php } ?>>CR</option>
                  <option value="2" <?php if(isset($status) && $status == 2){ ?> selected="selected" <?php } ?>>DR</option>
                </select>
                </div>
                
                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-3">
                <button type="button" class="btn btn-success" id="oldWalletSearchBtn">Search</button>
                <!-- <a href="{site_url}admin/wallet/addWallet" class="btn btn-primary">+Add Wallet</a> -->
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="oldWalletDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Before Amount</th>
                      <th>Cr/Dr Amount</th>
                      <th>After Amount</th>
                      <th>Date Time</th>
                      <th>Type</th>
                      <th>Description</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Before Amount</th>
                      <th>Cr/Dr Amount</th>
                      <th>After Amount</th>
                      <th>Date Time</th>
                      <th>Type</th>
                      <th>Description</th>
                      <th>Status</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

