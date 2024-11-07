<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">

                <div class="col-sm-6">
                <h4><b>Link List </b></h4>
                </div>
                


                <div class="col-sm-6  text-right">
               
                <a href="{site_url}employe/link/add" class="btn btn-primary">+ Add Link</a>
                </div> 
                
               </div>  
              <?php echo form_close(); ?>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Title</th>
                      <th>Url</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php $i=1; foreach ($list as  $value) {

                  ?>
                    <td><?php echo $i; ?></td>
                    
					         <td><?php echo  $value['title'];?></td>
                   <td><?php echo $value['url'] ?></td>
                   <td><?php if($value['status']) { echo '<font color="green">Active</font>'; } else { echo '<font color="red">Deactive</font>'; } ?>
                   </td>
                   
                   <td>
                     <a title="edit" class="btn btn-success btn-sm" href="{site_url}employe/link/edit/<?=$value['id']?>"><i class="fa fa-edit" aria-hidden="true"></i></a>

                     <a title="delete" class="btn btn-danger btn-sm" href="{site_url}employe/link/deleteLink/<?=$value['id']?>" onclick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                      
                    </td>
                    </tr>
                  <?php $i++;} ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
