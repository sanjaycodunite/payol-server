{system_message}
{system_info}
<div class="card shadow ">
    <div class="card-header py-3">
        <div class="row">
            <div class="col-sm-6">
                <h4><b>Virtual Account Detail</b></h4>
            </div>

            <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left">
                        Back</i></button>
            </div>
        </div>

    </div>
    <div class="card-body">

        <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
        <div class="row">

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Virtual Account Numbe</th>
                        <th scope="col">IFSC Code</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
     $get_virtual_account_data = $this->db->get_where('users',array('is_virtual_account'=>1,'id'=>$loggedAccountID))->row_array();

        $virtual_account= isset($get_virtual_account_data['virtual_account_no']) ? $get_virtual_account_data['virtual_account_no'] : '' ;
      ?>
                    <tr>
                        <td><?php echo $virtual_account ?></td>
                        <td>UTIB0CCH274</td>
                        <td>
                            <?php if($chk_va_status == 1)
                  {?>
                            <span class="text-success"> Active</span>
                            <?php } else { ?>
                            <span class="text-danger"> Not Active</span>
                            <?php } ?>
                        </td>
                    </tr>

                </tbody>
            </table>


        </div>



    </div>
</div>

<?php echo form_close(); ?>
</div>