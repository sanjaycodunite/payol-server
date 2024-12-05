<div class="card shadow mb-4">
    {system_message}
    {system_info}
    <div class="card-header py-3">
        <div class="row">
            <div class="col-sm-6">
                <h4><b>Become A Partner Enquiry List </b></h4>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="example" cellspacing="0">
                <input type="hidden" enquiryType="webBecomePartner" class="webFormData">
                <input type="hidden" value="<?php echo $site_url; ?>" id="siteUrl">
                <thead>
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
              foreach ($becomeAPatnerFormEnquiryList as $value) {
            ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
                            <strong>Name :</strong> <?php echo $value['name']; ?><br>
                            <strong>Email :</strong> <?php echo $value['email']; ?><br>
                            <strong>Mobile :</strong> <?php echo $value['mobile']; ?><br>
                            <strong>Partner Type :</strong> <?php echo $value['partner_type']; ?><br>
                            <strong>Product Interest :</strong> <?php echo $value['product_intrest']; ?> <br>
                            <strong>Created On :</strong>
                            <?php echo date('d-m-Y <b> h:i A</b>', strtotime($value['created'])); ?>
                        </td>
                        <td class="table-message">
                            <?php echo nl2br(htmlspecialchars(isset($value['message']) && $value['message'] ? $value['message'] : 'N/A')); ?>
                        </td>

                        <td>
                            <button title="delete" class="btn btn-danger btn-sm" data-id="<?= $value['id'] ?>"
                                onclick="confirmEquirynDelete(this)">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
                <tfoot>
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
</div>