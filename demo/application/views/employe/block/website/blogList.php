<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Our Blog</b></h4>
                </div>
                <div class="col-sm-6 text-right">
                 <a href="{site_url}employe/website/addBlog" class="btn btn-primary">Add Blog</a> 
                </div>
               </div>  
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Image</th>
                      <th>Title</th>
                      <th width="50%">Description</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    foreach($blogList as $list){
                    ?>
                    
                    <tr>
                    <td><?php echo $i; ?></td>
                    <td><img src="{site_url}<?php echo $list['image']; ?>" width="70"></td>
                    <td><?php echo $list['title']; ?></td>
                    <td width="50%"><?php echo $list['description']; ?></td>
                    <td><a href="{site_url}admin/website/editBlog/<?php echo $list['id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                    <a href="{site_url}admin/website/deleteBlog/<?php echo $list['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure want to delete?')"><i class="fa fa-trash"></i></a></td>
                    </tr> 

                   <?php $i++;} ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Image</th>
                      <th>Title</th>
                      <th width="50%">Description</th>
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

