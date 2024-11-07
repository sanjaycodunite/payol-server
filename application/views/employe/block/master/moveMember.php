<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('employe/master/moveMemberAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Move Member</b></h4>
                </div>

                <div class="col-sm-4 text-right">
                <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to move member?')">Save</button>
                </div>

              </div>
            </div>
            
            <div class="card-body">
              <div class="row">
                <div class="col-sm-6">
                  <center><h4>Member</h4></center>
                  <div class="row">
                    <div class="col-sm-6">
                      <label><b>Member Type</b></label>
                      <select class="form-control selectpicker" data-live-search="true" name="memberType" id="selMemberType">
                        <option value="0">Select Member Type</option>
                        <option value="4">Distributor</option>
                        <option value="5">Retailer</option>
                      </select>
                    </div>
                    <div class="col-sm-6">
                      <label><b>Member</b></label>
                      <select class="form-control selectpicker" data-live-search="true" name="memberID" id="selMemberID">
                        <option value="0">Select Member</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <center><h4>Sponser</h4></center>
                  <div class="row">
                    <div class="col-sm-6">
                      <label><b>Sponser Type</b></label>
                      <select class="form-control selectpicker" data-live-search="true" name="sponserType" id="selSponserType">
                        <option value="0">Select Sponser Type</option>
                        
                      </select>
                    </div>
                    <div class="col-sm-6">
                      <label><b>Sponser</b></label>
                      <select class="form-control selectpicker" data-live-search="true" name="sponserID" id="selSponserID">
                        <option value="0">Select Sponser</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 text-center recharge-comm-loader">
                </div>

              </div>
              <br />
              <div class="table-responsive" id="recharge-comm-block">
                
              </div>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

