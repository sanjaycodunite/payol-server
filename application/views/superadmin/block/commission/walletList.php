<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-3">
                <h4><b>Commission History</b></h4>
                </div>

                <div class="col-sm-2">
                 <input type="text" class="form-control datepick" name="date" id="date" placeholder="Date" autocomplete="off"> 
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
                <?php echo form_open('',array('id'=>'leadFilterForm')); ?>
                <input type="text" class="form-control" placeholder="Keyword" name="keyword" id="keyword" />
                </div>

                <div class="col-sm-3">
                <button type="button" class="btn btn-success" id="comWalletSearchBtn">Search</button>
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="comWalletDataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Txn ID</th>
                      <th>Txn Type</th>
                      <th>Amount</th>
                      <th>Commision</th>
                      <th>Type</th>
                      <th>Is Paid/Charge ?</th>
                      <th>Date Time</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Txn ID</th>
                      <th>Txn Type</th>
                      <th>Amount</th>
                      <th>Commision</th>
                      <th>Type</th>
                      <th>Is Paid/Charge ?</th>
                      <th>Date Time</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

