<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>API List</b></h4>
                </div>

                <div class="col-sm-2">
                
                </div>

                <div class="col-sm-4">
                
                </div>
                <?php if($accountData['is_api_active']){ ?>
                <div class="col-sm-2">
                <a href="{site_url}admin/api/addApi" class="btn btn-primary">+Add API</a>
                </div>
                <?php } ?>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="apiDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>API ID</th>
                      <th>Provider</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>API ID</th>
                      <th>Provider</th>
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

