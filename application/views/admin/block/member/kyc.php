<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>KYC List</b></h4>
                </div>

                <!---
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
                --->   

                <div class="col-sm-2">
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-2">
                <button type="button" class="btn btn-success" id="kycSearchBtn">Search</button>
               
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="kycDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Member</th>
                      <th>Mobile</th>
                      <th>Address</th>
                      <th>Document</th>
                      <th>Attachment</th>
                      <th>Bank Details</th>
                      <th>Status</th>
                      <th>Datetime</th>                      
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Member</th>
                      <th>Mobile</th>
                      <th>Address</th>
                      <th>Document</th>
                      <th>Attachment</th>
                      <th>Bank Details</th>
                      <th>Status</th>
                      <th>Datetime</th>                      
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

