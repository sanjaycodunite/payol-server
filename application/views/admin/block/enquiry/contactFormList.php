<div class="card shadow mb-4">
    {system_message}
    {system_info}
    <div class="card-header py-3">
        <div class="row">
            <div class="col-sm-6">
                <h4><b>Contact Us Enquiry List</b></h4>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Message</th>
                        <th>Create On</th>
                        <th>From</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                      $i = 1;
                      foreach ($contactFormList as $value) {
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
                            <b>Name:</b> <?php echo $value['name']; ?><br>
                            <b>Email:</b> <?php echo $value['email']; ?><br>
                            <b>Contact:</b> <?php echo $value['phone']; ?><br>
                            <b>Date:</b> <?php echo $value['created_at']; ?>
                        </td>
                        <td><?php echo nl2br($value['message']); ?></td>
                        <td><?php echo $value['created_at']; ?></td>
                        <td><?php echo $value['from']; ?></td>
                        <td class="text-center">
                            <a title="Delete" class="btn btn-danger btn-sm" title="Delete"
                                href="{site_url}employe/employe/deleteEmploye/<?= $value['id'] ?>"
                                onclick="return confirm('Are you sure you want to delete?')">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                    <?php $i++; }
                    ?>
                </tbody>
                <tfoot class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Message</th>
                        <th>Create On</th>
                        <th>From</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>