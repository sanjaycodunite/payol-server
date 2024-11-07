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
      <h3><b>Verify Documents</b></h3>  
      </div>

      <?php echo form_open_multipart('master/newaeps/uploadDocumentAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
      <div class="card-body">

      <div class="row">

        <input type="hidden" name="bene_id" value="{bene_id}">
        <div class="col-sm-12">
          <div class="form-group">
            <label><b>Document Type*</b></label><br>
            <label><input type="radio" name="document_type" value="PAN" checked="" onclick="checkUpload(this.value)">&nbsp;&nbsp;Pancard</label>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <label><input type="radio" name="document_type" value="AADHAAR" onclick="checkUpload(this.value)">&nbsp;&nbsp;Aadhar Card</label>

          </div>  
        </div>


        <div class="col-sm-3">
         <div class="form-group">
          <label><b>Upload Bank Passbook Photo*</b></label>
          <input type="file" class="form-control" autocomplete="off" name="passbook" id="passbook">
          <?php echo form_error('passbook', '<div class="error">', '</div>'); ?>  
          </div> 
        </div>

        <div class="col-sm-3" id="pan_div">
          <div class="form-group">
          <label><b>Upload Pancard Image*</b></label>
          <input type="file" class="form-control" autocomplete="off" name="panimage" id="panimage">
          <?php echo form_error('panimage', '<div class="error">', '</div>'); ?>   
          </div>
        </div>

        <div class="col-sm-3"  id="aadhar_front_div" style="display: none;">
          <div class="form-group">
          <label><b>Upload Aadhar Front Image*</b></label>
          <input type="file" class="form-control" name="aadhar_front" id="aadhar_front">
          <?php echo form_error('aadhar_front', '<div class="error">', '</div>'); ?>  
          
          </div>
        </div>

        <div class="col-sm-3" id="aadhar_back_div" style="display: none;">
          <div class="form-group">
          <label><b>Upload Aadhar Back Image*</b></label>
          <input type="file" class="form-control" name="aadhar_back" id="aadhar_back">
          <?php echo form_error('aadhar_back', '<div class="error">', '</div>'); ?>  
          
          </div>
        </div>

      </div>  

        
      </div>

      <div class="card-footer">
       <button class="btn btn-primary" type="submit">Proceed</button> 
      </div>


    </div>

  </div>  


  </div> 

</div>

<?php echo form_close(); ?>