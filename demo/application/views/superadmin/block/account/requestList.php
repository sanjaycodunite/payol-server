<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">

                <div class="col-sm-6">
                <h4><b> Request Account List </b></h4>
                </div>
                


                <div class="col-sm-6  text-right">
               
                
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
                      <th>Account</th>
                      <th>Account Type</th>
                      <th>Prefix</th>
                      <th>Domain</th>
                      <th>Logo</th>
                      <th>Contact</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php $i=1; foreach ($list as  $value) {

                  ?>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $value['account_name']; ?></td>
                    <td><?php echo  'Virtual';?></td>
                    <td><?php echo $value['account_code']; ?></td>
                    <td>
                    <table class="table table-striped">
                      <tr>
                        <td><b>Name</b></td>
                        <td><?php echo $value['title']; ?></td>
                      </tr>
                      <tr>
                        <td><b>URL</b></td>
                        <td><?php echo $value['domain_url']; ?></td>
                      </tr>
                      
                    </table></td>
                   
                   <td> <img src="{site_url}<?php echo $value['image_path']; ?>" width="100" /></td>
                  <td>
                    <table class="table table-striped">
                      <tr>
                        <td><b>Name</b></td>
                        <td><?php echo $value['name']; ?></td>
                      </tr>
                      <tr>
                        <td><b>Email</b></td>
                        <td><?php echo $value['email']; ?></td>
                      </tr>
                      <tr>
                        <td><b>Mobile</b></td>
                        <td><?php echo $value['mobile']; ?></td>
                      </tr>
                    </table>
                  </td>

                   <td>
                      <?php if($value['status'] == 0) { 
                          echo '<font color="orange">Pending</font>'; 
                        } else { 
                          echo '<font color="green">Active</font>'; 
                        } ?>
                          
                          </td>
                      
                    </tr>
                  <?php $i++;} ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

