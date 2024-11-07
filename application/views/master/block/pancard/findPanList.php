<div class="container-fluid">
  {system_message}               
  {system_info}
<div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Find Pan No </b></h4>
                </div>
                <div class="col-sm-6 text-right">
                 <a href="{site_url}master/pancard/findPan" class="btn btn-primary">Find PAN No </a> 
                </div>
               </div>  
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th width="5%">#</th>
                      <th>Name</th>                      
                      <th>Aadhar No</th>
                      <th>DOB</th>
                      <th>Created</th>
                      <th>Pan Image</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    foreach($records as $list){
                    ?>
                    
                    <tr>
                    <td width="5%"><?php echo $i; ?></td>
                    <td><?php echo $list['name']; ?></td>   
                     <td><?php echo $list['aadhar_number']; ?></td>                 
                    <td><?php echo date('d-M-Y',strtotime($list['dob'])); ?></td>
                     <td><?php echo date('d-M-Y',strtotime($list['created'])); ?></td>
                    <td><?php  if($list['pan_img']) {?>
                        
                        <a href="{site_url}<?php echo $list['pan_img'];?>"> view & download</a>
                      <?php } else { ?>
                        <p>Not Available
                        </p>
                      <?php } ?>

                    </td>
                    </tr> 

                   <?php $i++;} ?>
                  </tbody>

                  <tfoot>
                    <tr>
                    <th width="5%">#</th>
                      <th>Name</th>                      
                      <th>Aadhar No</th>
                      <th>DOB</th>
                      <th>Created</th>
                      <th>Pan Image</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

