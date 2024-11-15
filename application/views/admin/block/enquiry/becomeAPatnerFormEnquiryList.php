<div class="card shadow mb-4">
    {system_message}
    {system_info}
    <div class="card-header py-3">
        <div class="row">
            <div class="col-sm-6">
                <h4><b> Employe List </b></h4>
            </div>
            <div class="col-sm-6  text-right">
                <a href="{site_url}employe/employe/addEmploye" class="btn btn-primary">+ Add Employe</a>
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
                        <th>Employe ID</th>
                        <th>Role</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Password</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                      $i = 1;
                      foreach ($contactFormList as $value) {
                    ?>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $value['name']; ?></td>
                    <td><?php echo $role['email']; ?></td>
                    <td><?php echo $value['phone']; ?></td>
                    <td><?php echo $value['message']; ?></td>
                    <td><?php echo $value['created_at']; ?></td>
                    <td><?php echo $value['from']; ?></td>
                    <td>
                        <a title="edit" class="btn btn-primary btn-sm"
                            href="{site_url}employe/employe/editEmploye/<?= $value['id'] ?>"><i class="fa fa-edit"
                                aria-hidden="true"></i></a>
                        <a title="delete" class="btn btn-danger btn-sm" href="{site_url}employe/employe/deleteEmploye/<?= $value[
                          'id'
                      ] ?>" onclick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash"
                                aria-hidden="true"></i></a>
                    </td>
                    </tr>
                    <?php $i++;
                  }
                  ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>