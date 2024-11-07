 <div class="row">
    <div class="col-sm-12">
     {system_message}    
     {system_info} 
    </div>  
  </div>
  <div class="row">

  <div class="col-sm-12">

    <div class="card">

      <div class="card-header">
       <div class="row">
        <div class="col-sm-6">  
         <h3><b>Beneficiary</b></h3>
        </div>
        
         <?php $get_no_account = $this->db->get_where('new_payout_beneficiary',array('user_id'=>$loggedUser['id']))->num_rows(); ?>
          <?php if($get_no_account < 5) { ?>
        <div class="col-sm-6 text-right">
          <a href="{site_url}master/newaeps/addBeneficiary" class="btn btn-primary">+Add Beneficiary</a>
        </div> 
        <?php } ?>
        </div>  
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="example">
            <thead>
            <tr>
              <th>#</th>
              <th>Beneficiary Name</th>
              <th>Account No.</th>
              <th>Bank</th>
              <th>IFSC</th>
              <th>Is Document Verified?</th>
              <th>Check Status</th>
              <th>Added On</th>
              <th>Fund</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($benificaryList){
                $i=1;
              foreach($benificaryList as $list){
             ?>
            <tr>
              <td class="align-middle"><?php echo $i; ?></td>
              <td class="align-middle"><?php echo $list['account_holder_name']; ?></td>
              <td class="align-middle"><?php echo $list['account_number']; ?></td>
              <td class="align-middle"><?php echo $list['bank_name']; ?></td>
              <td class="align-middle"><?php echo $list['ifsc']; ?></td>
              <td class="align-middle">
                <?php
                 if($list['is_verified'] == 1){
                ?>
                 <font color="green">Verified</font>
                <?php } else{ ?>
                  <a href="{site_url}master/newaeps/uploadDocument/<?=$list['bene_id']?>">Verify Document</a>
                <?php } ?>
              </td>
              
              <td class="align-middle">
                <?php
                 if($list['is_verified'] != 1){
                ?>
                <a href="{site_url}master/newaeps/checkAccountStatus/<?php echo $list['id']; ?>"><button class="btn btn-primary" type="button">Check Status</button></a>
               <?php } else{ ?>
                 Not Allowed
               <?php } ?>
              </td>
              
              
              <td class="align-middle"><?php echo date('d-m-Y',strtotime($list['created'])); ?></td>
              <td class="align-middle">
                <?php
                 if($list['is_verified'] == 1){
                ?>
                <a href="{site_url}master/newaeps/fundTransfer/<?php echo $list['id']; ?>"><button class="btn btn-primary" type="button">Transfer</button></a>
               <?php } else{ ?>
                 Not Allowed
               <?php } ?>
              </td>
              

              </tr>
            <?php $i++; }}else{  ?>  
            <tr>
            <th colspan="7" class="align-middle text-center">No Record Found</th>
            </tr>
            <?php } ?>
            </tbody>

            <tfoot>
              <tr>
              <th>#</th>
              <th>Beneficiary Name</th>
              <th>Account No.</th>
              <th>Bank</th>
              <th>IFSC</th>
              <th>Is Document Verified?</th>
              <th>Check Status</th>
              <th>Added On</th>
              <th>Fund</th>
              
            </tr> 
            </tfoot>

          </table>
          </div>
      </div>

    

    </div>

  </div>  


  </div> 
</div>

<?php echo form_close(); ?>