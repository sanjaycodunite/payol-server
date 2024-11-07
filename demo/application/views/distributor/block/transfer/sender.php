 <div class="row">
    <div class="col-sm-12">
     {system_message}    
     {system_info} 
    </div>  
  </div>
  <div class="row">

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
      <h3><b>Sender</b></h3>  
      </div>

      <?php echo form_open_multipart('distributor/transfer/senderAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
      <div class="card-body">

      <div class="row">

        <div class="col-sm-3">
          <div class="form-group">
          <label><b>Name*</b></label>
          <input type="text" class="form-control" name="name" id="name" placeholder="Name">
          <?php echo form_error('name', '<div class="error">', '</div>'); ?>  
          </div>
          </div>
          
          <div class="col-sm-3">
            <div class="form-group">
          <label><b>Mobile*</b></label>
          <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Mobile">
          <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>  
          </div>
          </div>
          <div class="col-sm-3">
              <div class="form-group">
              <label><b>State*</b></label>
              <select class="form-control" name="state_id">
              <option value="">Select State</option>
              <?php if($stateList){ ?>
                <?php foreach($stateList as $list){ ?>
                  <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option>
                <?php } ?>
              <?php } ?>
              </select>
              <?php echo form_error('state_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
              <label><b>City*</b></label>
              <input type="text" class="form-control" name="city" id="city" placeholder="City">
              <?php echo form_error('city', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
              <label><b>Address*</b></label>
              <input type="text" class="form-control" name="address" id="address" placeholder="Address">
              <?php echo form_error('address', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
              <label><b>Pincode*</b></label>
              <input type="text" class="form-control" name="pincode" id="pincode" placeholder="Pincode">
              <?php echo form_error('pincode', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

      </div>  

        
      </div>

      <div class="card-footer">
       <button class="btn btn-primary" type="submit">Submit</button> 
      </div>


    </div>

  </div>  


  </div> 


  <div class="row">

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
      <h3><b>Sender List</b></h3>  
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="example">
            <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Mobile</th>
              <th>State</th>
              <th>City</th>
              <th>Address</th>
              <th>Pincode</th>
              <th>Status</th>
              <th>Datetime</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($benificaryList){
                $i=1;
              foreach($benificaryList as $list){
             ?>
            <tr>
              <td class="align-middle"><?php echo $i; ?></td>
              <td class="align-middle"><?php echo $list['name']; ?></td>
              <td class="align-middle"><?php echo $list['mobile']; ?></td>
              <td class="align-middle"><?php echo $list['state_name']; ?></td>
              <td class="align-middle"><?php echo $list['city']; ?></td>
              <td class="align-middle"><?php echo $list['address']; ?></td>
              <td class="align-middle"><?php echo $list['pincode']; ?></td>
              <td class="align-middle"><?php echo ($list['status'] == 1) ? '<font color="green">Active</font>' : '<font color="red">Deactive</font><br /><a href="{site_url}distributor/transfer/activeSender/'.$list['encodeTxnId'].'">Active Sender</a>' ; ?></td>
              <td class="align-middle"><?php echo date('d-m-Y H:i:s',strtotime($list['created'])); ?></td>
              <td class="align-middle">
                <a href="{site_url}distributor/transfer/fundTransfer/0/<?php echo $list['id']; ?>"><button class="btn btn-primary" type="button">Transfer</button></a>
              </td>
              </tr>
            <?php $i++; }}else{  ?>  
            <tr>
            <th colspan="10" class="align-middle text-center">No Record Found</th>
            </tr>
            <?php } ?>
            </tbody>

            <tfoot>
              <tr>
              <th>#</th>
              <th>Name</th>
              <th>Mobile</th>
              <th>State</th>
              <th>City</th>
              <th>Address</th>
              <th>Pincode</th>
              <th>Status</th>
              <th>Datetime</th>
              <th>Action</th>
            </tr> 
            </tfoot>

          </table>
          </div>
      </div>

    

    </div>

  </div>  


  </div> 
</div>

<?php echo form_close(); ?>