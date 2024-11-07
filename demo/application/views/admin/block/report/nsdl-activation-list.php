<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-3">
                <h4><b>NSDL PAN Activation Report</b></h4>
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

                <div class="col-sm-3">
                <button type="button" class="btn btn-success" id="nsdlActivationSearchBtn">Search</button>
                <a href="{site_url}admin/report/nsdlList" class="btn btn-secondary">View All</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="nsdlActivationDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Member</th>                     
                      <th>Txnid</th>
                      <th>Member Detail</th>    
                      <th>Document Detail</th>                  
                      <th>Mobile</th>
                      <th>Email</th>
                      <th>Gender</th>
                      <th>DOB</th>
                      <th>Address Detail</th>
                      <th>State</th>
                      <th>District</th>
                      <th>Aadhar No</th>
                      <th>Pan No</th>
                      <th>Status</th>
                      <th>Datetime</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                       <th>#</th>
                      <th>Member</th>                     
                      <th>Txnid</th>
                      <th>Member Detail</th>   
                      <th>Document Detail</th>                      
                      <th>Mobile</th>
                      <th>Email</th>
                      <th>Gender</th>
                      <th>DOB</th>
                      <th>Address Detail</th>
                      <th>State</th>
                      <th>District</th>
                      <th>Aadhar No</th>
                      <th>Pan No</th>
                      <th>Status</th>
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

