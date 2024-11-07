{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Beneficiary</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
           
           <div class="row">
            <div class="col-lg-6 col-md-6">
              <div class="beneficiary_form_section">
                <?php echo form_open_multipart('distributor/dmt/beneficiaryAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $mobile;?>" name="accountMobile">
              <div class="row">
              
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Account Holder Name*</b></label>
              <input type="text" class="form-control" name="account_holder_name" placeholder="Account Holder Name">
              <?php echo form_error('account_holder_name', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Beneficiary Mobile No.*</b></label>
              <input type="text" class="form-control" name="ben_mobile" placeholder="Beneficiary Mobile No.">
              <?php echo form_error('ben_mobile', '<div class="error">', '</div>'); ?>  
              
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Account No.*</b></label>
              <input type="text" class="form-control" name="account_no" placeholder="Account No.">
              <?php echo form_error('account_no', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>IFSC*</b></label>
              <input type="text" class="form-control" name="ifsc" id="ifsc" placeholder="IFSC">
              <?php echo form_error('ifsc', '<div class="error">', '</div>'); ?>  
              <br />
              <a href="#" onclick="dmtVerifyIfsc()">Verify IFSC</a>
              <div class="ifsc-vefify-loader"></div>
              </div>
              </div>
              <div class="col-lg-12">
              <div class="form-group">
              <label><b>Bank*</b></label>
              <select class="form-control selectpicker" name="bankID" data-live-search="true">
                <option value="">Select Bank</option>
                <?php if($bankList){ ?>
                  <?php foreach($bankList as $list){ ?>
                    <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              <?php echo form_error('bankID', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
               <div class="col-lg-12">
              <div class="form-group">
               <button class="login_btn btn btn-success" name="submit" type="submit">Submit </button>
              </div></div>

             

            </div>
            <?php echo form_close(); ?>
          </div>
          
           </div> 
            <div class="col-lg-6 col-md-6">
              <div class="beneficiary_col_bt1">
                <?php if($beneficiaryList){ ?>
                  <?php foreach($beneficiaryList as $list){ ?>
              <div class="beneficiary_right_history_section d-flex">
               <div class="beneficiary_colms-details">
                <h4><?php echo $list['account_holder_name']; ?></h4> 
                <?php if($list['verified_name']){ ?>
                <p><font color="green">Verified Name: <?php echo $list['verified_name']; ?></font></p>
                <?php } ?>
                <h5><?php echo $list['ben_mobile']; ?></h5> 
                <p>A/c: <?php echo $list['account_no']; ?></p>
                <p>IFSC: <?php echo $list['ifsc']; ?></p>
                <p>Bank: <?php echo $list['bank_name']; ?></p>
               </div> 
               <div class="beneficiary_colms-details_btns">
                <?php if($list['is_verify']){ ?>
                 <a href="<?php echo base_url('distributor/dmt/moneyTransfer').'/'.$list['beneId']; ?>"><button class="btn-transfer" type="button">Transfer</button></a>
               <?php } else { ?>
                 <a href="<?php echo base_url('distributor/dmt/verifyBen').'/'.$list['beneId'].'/'.$mobile; ?>"><button class="btn btn-success" style="padding: 13px 30px;" type="button">Verify</button></a>
               <?php } ?>
                 <a href="<?php echo base_url('distributor/dmt/deleteBen').'/'.$list['beneId'].'/'.$mobile; ?>" onclick="return confirm('Are you sure want to delete?')"><button class="btn btn-danger" style="padding: 13px 30px;" type="button">Delete</button></a>
               </div>
              </div>
              <?php } ?>
              <?php } else { ?>


              <div class="beneficiary_right_history_section d-flex">
               <div class="beneficiary_colms-details">
                No Beneficiary Registered
               </div> 
               <div class="beneficiary_colms-details_btns">
                 
               </div>
              </div>
              <?php } ?>
            </div>

            </div> 
           </div>

          
         
              
              
          </div>
        </div>
        
 
    </div>




