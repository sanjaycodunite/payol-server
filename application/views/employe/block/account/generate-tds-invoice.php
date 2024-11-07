
<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-2">
                <h4><b>Generate TDs Invoice</b></h4>
                </div>

                 


                <div class="col-sm-3">
                <?php echo form_open_multipart('employe/account/generateTdsInvoiceAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
                <label>Year</label>
                
                 <select name="year" class="form-control">
                  <option value="">Select</option>
                   <?php 
                 for ($i=2020; $i < 2050 ; $i++) {  
                 ?>
                  <option value="<?php echo $i; ?>"><?php  echo $i;?></option>
                      <?php } ?>
                 </select>
                  <?php echo form_error('year', '<div class="error">', '</div>'); ?>  
                </div>

                <div class="col-sm-3">
                    <label>Month</label>
                    <select name="month" class="form-control">
                       <option value="">Select</option>
                                 <?php  for ($m=1; $m<=12; $m++) {
                   $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                   ?> 
                  <option value="<?php echo $m; ?>"><?php  echo $month;?></option>
                      <?php } ?>
                 </select>

                 <?php echo form_error('month', '<div class="error">', '</div>'); ?> 
                </div>

                <div class="col-sm-2 mt-4">

                <button type="submit" name="submit" class="btn btn-success">Submit</button>
              
                
                </div>
               </div>  
              <?php echo form_close(); ?>
            </div>
            
          </div>
       

</div>