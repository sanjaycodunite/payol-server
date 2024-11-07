<div class="container-fluid">
<div class="row">
              
</div>
<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-3">
                <h4><b>Members Service Request List</b></h4>
                </div>

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control datepick" placeholder="Date" name="date" id="date" autocomplete="off" />
                </div>

                <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-5">
                <button type="button" class="btn btn-success" id="memberSearchBtn">Search</button>
                <a href="{site_url}admin/member/memberRequestList" class="btn btn-secondary">View All</a>
                
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="memberRequestDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Member Name</th>                      
                      <th>Email</th>
                      <th>Mobile</th>
                      <th>Partner Type</th>
                      <th>Product Interest</th>
                      <th>Member Type</th>
                      <th>Status</th>
                      <th>Message</th>
                      <th>Created</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Member Name</th>                      
                      <th>Email</th>
                      <th>Mobile</th>
                      <th>Partner Type</th>
                      <th>Product Interest</th>
                      <th>Member Type</th>
                      <th>Status</th>
                      <th>Message</th>
                      <th>Created</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>




<div id="updateComplainModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('admin/member/saveRequestMember',array('method'=>'post')); ?>
    <input type="hidden" name="member_id" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">approved Member</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      
    </div>
    <div class="modal-body">
    
    <div class="modalform">
      <div class="row">
        <div class="col-md-12">
          <p><b>Member Type </b></p>
          <div class="col-md-12" id="complainMsgBlock"></div>

        </div>
        <!--   <div class="form-group mt-5 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div> -->
     
       
      </div>
    </div>
    
    </div>
    <?php echo form_close(); ?>
    
  </div>

  </div>
</div>             

