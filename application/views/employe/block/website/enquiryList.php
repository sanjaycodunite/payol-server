<div class="card shadow mb-4">
              {system_message}               
              {system_info}
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Enquiry List</b></h4>
                </div>
                
               </div>  
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Service</th>
                      <th>Mobile</th>
                      <th width="20%">Message</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    foreach($enquiryList as $list){
                    ?>
                    
                    <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php  echo $list['name'];?></td>
                    <td><?php echo $list['email']; ?></td>
                    <td><?php echo $list['service']; ?></td>
                    <td><?php echo $list['mobile']; ?></td>
                    <td><?php echo $list['message']; ?></td>
                    <td>
                    <a href="{site_url}employe/website/deleteEnquiry/<?php echo $list['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure want to delete?')"><i class="fa fa-trash"></i></a></td>
                    </tr> 

                   <?php $i++;} ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Service</th>
                      <th>Mobile</th>
                      <th width="20%">Message</th>
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

