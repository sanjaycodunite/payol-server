<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('superadmin/report/dmtExportAuth',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-4">
                <h4><b>DMT KYC Export</b></h4>
                </div>

                <div class="col-sm-2">
                 <select class="selectpicker form-control" name="member_id" id="member_id" data-live-search="true">

                <option value="">Select Account</option>
                <?php if($accountList){ ?>
                  <?php foreach($accountList as $list){ ?>
                    <option value="<?php echo $list['id']; ?>"><?php echo ucwords($list['title']); ?></option>  
                  <?php } ?>
                <?php } ?>
                </select>    
                </div>  

                <div class="col-sm-6">
                  <button type="button" class="btn btn-primary" id="dmtKycExportSearchBtn">Search</button>
                  <button type="submit" name="exportAll" class="btn btn-success">Export All</button>
                  <button type="submit" name="exportNotGenerated" class="btn btn-danger">Export Not Generated</button>
                </div>
                

               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dmtKycExportDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>AgentId Status</th>
                      <th>AgentId</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Account</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>AgentId Status</th>
                      <th>AgentId</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

