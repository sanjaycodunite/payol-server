<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <?php echo form_open('superadmin/report/dmtExportAuth',array('id'=>'leadFilterForm')); ?>
              <div class="row">
                <div class="col-sm-8">
                <h4><b>DMT KYC Import</b></h4>
                </div>

                

                <div class="col-sm-4 text-right">
                  <a href="{site_url}superadmin/report/dmtImportFile"><button type="button" class="btn btn-success">Import File</button></a>
                </div>
                

               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dmtKycImportDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>File</th>
                      <th>Total Record</th>
                      <th>Match Record</th>
                      <th>Datetime</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>File</th>
                      <th>Total Record</th>
                      <th>Match Record</th>
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

