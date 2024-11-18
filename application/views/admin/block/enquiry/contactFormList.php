<div class="card shadow mb-4">
    {system_message}
    {system_info}
    <div class="card-header py-3">
        <div class="row">
            <div class="col-sm-6">
                <h4><b>Web Enquiry List</b></h4>
            </div>

        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive position-relative">
            <table class="table table-bordered table-striped" id="example" cellspacing="0">
                <input type="hidden" enquiryType="webContactForm" class="webFormData">
                <input type="hidden" value="<?php echo $site_url; ?>" id="siteUrl">
                <input type="hidden" value="tbl_get_in_touch_contacts" id="enquiryTableName">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Enquiry Person</th>
                        <th>Message <div class="enquiryResponseText text-center"></div>
                        </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                      $i = 1;
                      foreach ($contactFormList as $value) {
                    ?>
                    <tr class="table-row">
                        <td class="table-index">
                            <?php echo $i; ?>
                        </td>
                        <td class="table-details">
                            <div class="details-container">
                                <p><b class="label">Name:</b> <span
                                        class="value"><?php echo htmlspecialchars($value['name']); ?></span></p>
                                <p><b class="label">Email:</b> <span
                                        class="value"><?php echo htmlspecialchars($value['email']); ?></span></p>
                                <p><b class="label">Contact:</b> <span
                                        class="value"><?php echo htmlspecialchars($value['phone']); ?></span></p>
                                <p><b class="label">Date:</b> <span
                                        class="value"><?php echo date('d-m-Y <b> h:i A</b>', strtotime($value['created_at'])); ?></span>
                                </p>
                            </div>
                        </td>
                        <td class="table-message">
                            <?php echo nl2br(htmlspecialchars($value['message'])); ?>
                        </td>
                        <td class="table-action text-center">
                            <button title="delete" class="btn btn-danger btn-sm" data-id="<?= $value['id'] ?>"
                                onclick="confirmEquirynDelete(this)">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
                <tfoot class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Enquiry Person</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>