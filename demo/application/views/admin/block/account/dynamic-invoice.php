
<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Dynamic Invoice</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo $invoice_id; ?>">
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-4">
                <button type="button" class="btn btn-success" id="invoiceSearchBtn">Search</button>
              
                <a href="{site_url}admin/account/generateDynamicInvoice" class="btn btn-primary">+Generate Invoice</a>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dynamicInvoiceDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Type</th>
                      <th>Year</th>
                      <th>Month</th>
                      <th>Issue Date</th>  
                      <th>View Invoice</th>                    
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Type</th>
                      <th>Year</th>
                      <th>Month</th>
                      <th>Issue Date</th>
                      <th>View Invoice</th>  
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
       

