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
      <h3><b>Beneficiary</b></h3>  
      </div>

      <?php echo form_open_multipart('retailer/transfer/benificaryAuth', array('id' => 'account_verify_form'),array('method'=>'post')); ?>
      <div class="card-body">

      <div class="row">

        <div class="col-sm-3">
         <div class="form-group">
          <label><b>Account Holder Name*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="account_holder_name" id="account_holder_name" placeholder="Holder Name">
          <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
          </div> 
        </div>

        <div class="col-sm-2">
        <div class="form-group">
        <label><b>Bank*</b></label>
        <select class="form-control selectpicker" name="bankID" id="bankID" data-live-search="true">
          <option value="">Select Bank</option>
          <?php if($bankList){ ?>
            <?php foreach($bankList as $list){ ?>
              <option value="<?php echo $list['id']; ?>"><?php echo $list['bank_name']; ?></option>
            <?php } ?>
          <?php } ?>
        </select>
        <?php echo form_error('bankID', '<div class="error">', '</div>'); ?>  
        </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
          <label><b>Account No.*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="account_number" id="account_number" placeholder="Account No.">
          <?php echo form_error('account_number', '<div class="error">', '</div>'); ?>  
          
          </div>
        </div>

        <div class="col-sm-2">
          <div class="form-group">
          <label><b>IFSC Code*</b></label>
          <input type="text" class="form-control" autocomplete="off" name="ifsc" id="ifsc" placeholder="IFSC Code">
          <?php echo form_error('ifsc', '<div class="error">', '</div>'); ?>  
          
          </div>
        </div>  


           <div class="col-sm-12">
                <div class="form-group ajaxx-loader">

                </div>
               </div>
        

      </div>  

        
      </div>

      <div class="card-footer">
         <button type="button" id="accountVerifyBtn" class="btn btn-success" name="verify" value="verify">Verify & Add</button>
       <button class="btn btn-primary" type="submit">Save New Beneficiary</button> 
      </div>


    </div>

  </div>  


  </div> 


  <div class="row">

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
      <h3><b>Beneficiary</b></h3>  
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="example">
            <thead>
            <tr>
              <th>#</th>
              <th>Beneficiary Name</th>
              <th>Account No.</th>
              <th>Bank</th>
              <th>IFSC</th>
              <th>Added On</th>
              <th>Fund</th>
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
              <td class="align-middle"><?php echo $list['account_holder_name']; ?></td>
              <td class="align-middle"><?php echo $list['account_no']; ?></td>
              <td class="align-middle"><?php echo $list['bank_name']; ?></td>
              <td class="align-middle"><?php echo $list['ifsc']; ?></td>
              <td class="align-middle"><?php echo date('d-m-Y',strtotime($list['created'])); ?></td>
              <td class="align-middle">
                <a href="{site_url}retailer/transfer/fundTransfer/<?php echo $list['id']; ?>"><button class="btn btn-primary" type="button">Transfer</button></a>
              </td>
              <td class="align-middle">
                <a title="edit" class="btn btn-primary btn-sm" href="#" onclick="updateBenModel(<?php echo $list['id']; ?>); return false;"><i class="fa fa-edit" aria-hidden="true"></i></a>
                 <a href="{site_url}retailer/transfer/deleteBeneficiary/<?php echo $list['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure want to delete?')"><i class="fa fa-trash"></i></a>
              </td>
              </tr>
            <?php $i++; }}else{  ?>  
            <tr>
            <th colspan="7" class="align-middle text-center">No Record Found</th>
            </tr>
            <?php } ?>
            </tbody>

            <tfoot>
              <tr>
              <th>#</th>
              <th>Beneficiary Name</th>
              <th>Account No.</th>
              <th>Bank</th>
              <th>IFSC</th>
              <th>Added On</th>
              <th>Fund</th>
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




 <div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Bank Verification</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="bankResponse">
              
            </div>
            <div class="card-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                  <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                </div>

                <div class="col-sm-6 text-right">
                  <button type="submit" class="btn btn-success btn-sm">Add Beneficiary</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      

<?php echo form_close(); ?>
<div id="updateDMRModel" class="modal fade" role="dialog">
  <div class="modal-dialog assign-modal">

  <!-- Modal content-->
  <div class="modal-content">
    <?php echo form_open_multipart('retailer/transfer/updateBenificaryAuth',array('method'=>'post')); ?>
    <input type="hidden" name="recordID" id="recordID" value="0">
    <div class="modal-header">
    <h4 class="modal-title">Update Beneficiary</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    
    
    </div>
    <div class="modal-body">
    
    <div class="modalform">
      <input type="hidden" value="0" name="taskID" id="taskID" />
      <div class="row">
        <div class="col-md-12" id="updateDMRBlock">

        </div>
      </div>
    </div>
    
    </div>
    <?php echo form_close(); ?>
    
  </div>

  </div>
</div>