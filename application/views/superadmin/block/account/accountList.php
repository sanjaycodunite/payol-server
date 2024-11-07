<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">

                <div class="col-sm-6">
                <h4><b> User List </b></h4>
                </div>
                


                <div class="col-sm-6  text-right">
               
                <a href="{site_url}superadmin/account/addAccount" class="btn btn-primary">+ Create Account</a>
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
                      <th>Account Type</th>
                      <th>Package</th>
                      <th>Domain</th>
                      <th>Logo</th>
                      <th>Contact</th>
                      <th>Login Detail</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php $i=1; foreach ($list as  $value) {

                    // get service list
                    $serviceList = $this->db->select('services.title,account_services.status')->join('services','services.id = account_services.service_id')->get_where('account_services',array('account_services.account_id'=>$value['id']))->result_array();

                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo  $value['account_type_title'];?></td>
                    <td><?php echo  $value['package_name'];?></td>
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
                    <table class="table table-striped">
                      <tr>
                        <td><b>MemberID</b></td>
                        <td><?php echo $value['user_code']; ?></td>
                      </tr>
                      <tr>
                        <td><b>Username</b></td>
                        <td><?php echo $value['username']; ?></td>
                      </tr>
                      <tr>
                        <td><b>Password</b></td>
                        <td><?php echo $value['decode_password']; ?></td>
                      </tr>
                      
                    </table>
                  </td>
                  
					 <td><?php if($value['status']) { echo '<font color="green">Active</font>'; } else { echo '<font color="red">Deactive</font>'; } ?></td>
                      <td>
                      <a title="edit" class="btn btn-primary btn-sm" href="{site_url}superadmin/account/editAccount/<?=$value['id']?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
                      <a title="delete" class="btn btn-danger btn-sm" href="{site_url}superadmin/account/deleteAccount/<?=$value['id']?>" onclick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                      
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td colspan="9"><b>Active Services</b></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td colspan="9">
                        <table class="table table-striped">
                          <tr>
                          <?php if($serviceList){ ?>
                              <?php foreach($serviceList as $list){ ?>
                              <?php if($list['status'] == 1){ ?>
                              <td><center><i class="fa fa-check" style="background: green;color: #fff;padding: 5px;border-radius: 15px;"></i><br /><b><?php echo $list['title']; ?></b></center></td>
                              <?php } else { ?>
                                <td><center><i class="fa fa-window-close" style="background: red;color: #fff;padding: 5px;border-radius: 15px;"></i><br /><b><?php echo $list['title']; ?></b></center></td>
                              <?php } ?>
                            
                          <?php } ?>
                          <?php } else { ?>
                            
                              <td>No Active Service</td>
                            
                          <?php } ?>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  <?php $i++;} ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

