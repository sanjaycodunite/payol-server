<?php echo form_open_multipart('member/token/requestTokenAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
<div class="card shadow ">
{system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Role List</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <a href="{site_url}employe/role/addRole" class="btn btn-primary">+ Add Role</a>
                <button onclick="window.history.back()" type="button" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            
              
			<div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Action</th>
                      
                    </tr>
                  </thead>
                  
                  <tbody>
                    <?php 
                    if($roleList){
                      $i=1;
                      foreach($roleList as $list){
                    ?>
                    <tr>
                      <td><?php echo $i; ?></td>
                      <td><?php echo $list['title']; ?></td>
                      <td><?php if($list['status'] == 1){ ?> <font color="green">Active</font>  <?php } else{ ?> <font color="red">Deactive</font> <?php } ?></td>
                      <td><a title="edit" class="btn btn-primary btn-sm" href="{site_url}admin/role/editRole/<?php echo $list['id']; ?>"><i class="fa fa-edit" aria-hidden="true"></i></a> <a title="delete" class="btn btn-danger btn-sm" href="{site_url}admin/role/deleteRole/<?php echo $list['id']; ?>" onclick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>
                  <?php $i++;}} ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Role</th>
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
           
 <?php echo form_close(); ?>     
    </div>




