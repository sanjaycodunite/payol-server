<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Front Pages</b></h4>
                </div>
                <div class="col-sm-6 text-right">
                 <a href="{site_url}admin/website/addPage" class="btn btn-primary">Add Page</a> 
                </div>
               </div>  
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Page Title</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    foreach($pageList as $list){
                    ?>
                    
                    <tr>
                    <td><?php echo $i; ?></td>
                    <td><?=$list['page_title']?></td>
                    <td>
                      <?php
                       if($list['status'] == 1){
                      ?>
                        <font color="green">Active</font>
                      <?php } else{ ?>
                        <font color="red">Deactive</font>
                      <?php } ?>

                    </td>  
                    <td>
                      <a href="{site_url}admin/website/editPage/<?php echo $list['id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>

                      <a href="{site_url}admin/website/deletePage/<?php echo $list['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure want to delete?')"><i class="fa fa-trash"></i></a></td>
                    </tr> 

                   <?php $i++;} ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Page Title</th>
                      <th>Status</th>
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

