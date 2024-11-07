<?php echo form_open_multipart('member/token/requestTokenAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
<div class="card shadow ">
{system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-4">
                <h4><b>Fund Request List</b></h4>
                </div>
                
                <div class="col-sm-2">
                 <input type="text" class="form-control datepick" name="date" placeholder="Date" autocomplete="off" id="date"> 
                </div>
                <div class="col-sm-2">
                  <input type="text" class="form-control" name="keyword" id="keyword" placeholder="Keyword">
                </div>  
                <div class="col-sm-4">
                <button type="button" class="btn btn-primary" id="searchRequestBtn">Search</i></button>
                <a href="{site_url}master/wallet/requestList" class="btn btn-secondary">View All</a>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            
              
			<div class="table-responsive">
                <table class="table table-bordered table-striped" id="fundRequestDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Member</th>
                      <th>Request ID</th>
                      <th>Is UPI ?</th>
                      <th>UPI ID</th>
                      <th>Txn ID</th>
                      <th>Amount</th>
                      <th>Datetime</th>
                      <th>Status</th>
                      <th>Action</th>
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Member</th>
                      <th>Request ID</th>
                      <th>Is UPI ?</th>
                      <th>UPI ID</th>
                      <th>Txn ID</th>
                      <th>Amount</th>
                      <th>Datetime</th>
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
           
 <?php echo form_close(); ?>     
    </div>




