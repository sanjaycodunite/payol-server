<div class="container-fluid">
  {system_message}               
  {system_info}
<div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Package</b></h4>
                </div>
                <div class="col-sm-6 text-right">
                 <a href="{site_url}superadmin/package/addPackage" class="btn btn-primary">Add Package</a> 
                </div>
               </div>  
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th width="5%">#</th>
                      <th>Package Name</th>
                      <th>Status</th>
                      <th>Created</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    foreach($packageList as $list){
                    ?>
                    
                    <tr>
                    <td width="5%"><?php echo $i; ?></td>
                    <td><?php echo $list['package_name']; ?></td>
                    <td>
                     <?php
                     if($list['status'] == 1){
                     ?>
                     <font color="green">Active</font>
                     <?php } else{ ?>
                     <font color="red">Deactive</font> 
                     <?php } ?> 

                    </td>
                    <td><?php echo date('d-M-Y',strtotime($list['created'])); ?></td>
                    <td><a href="{site_url}superadmin/package/editPackage/<?php echo $list['id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                    <a href="{site_url}superadmin/package/deletePackage/<?php echo $list['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure want to delete?')"><i class="fa fa-trash"></i></a></td>
                    </tr> 

                   <?php $i++;} ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th width="5%">#</th>
                      <th>Package Name</th>
                      <th>Status</th>
                      <th>Created</th>
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
      </div>

