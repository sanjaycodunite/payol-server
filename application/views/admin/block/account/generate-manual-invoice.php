{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Generate Manual Invoice</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/account/generateManualInvoiceAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
               <div class="col-lg-12">
                <div class="form-group" style="border-bottom: 1px solid #8474e5;"><h5>Buyer Informaon</h5></div> 
               </div> 
                <div class="col-sm-3">
              <div class="form-group">
              <label><b>User List*</b></label>
              <select class="form-control" name="user">
              <option value="">Select Type</option>
              <?php if($user_type){ ?>
                <?php foreach($user_type as $list){ ?>
                  <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?>(<?php echo $list['user_code']; ?>)</option>
                <?php } ?>
              <?php } ?>
              </select>
              <?php echo form_error('role_id', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
                </div>
              <div class="row">
                <div class="col-lg-12">
                <div class="form-group" style="border-bottom: 1px solid #8474e5;"><h5>Invoice</h5></div> 
               </div>

               <div class="col-sm-3">
              <div class="form-group">
              <label><b>Invoice Year*</b></label>
              <input type="text" class="form-control" name="invoice_year" id="invoiceID">
              <?php echo form_error('invoice_year', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Invoice Number*</b></label>
              <input type="text" class="form-control" name="invoice" id="invoiceID">
              <?php echo form_error('password', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Issue Date*</b></label>
              <input type="date" class="form-control" name="issue_date" id="issueDate" placeholder="Issue Date">
              <?php echo form_error('issue_date', '<div class="error">', '</div>'); ?>  
              </div>
              </div>

        </div>

              <div class="row">
             <div class="col-lg-12">
                <div class="form-group" style="border-bottom: 1px solid #8474e5;"><h5>Tax Data </h5></div> 
               </div>
       
       <div class="col-lg-12">
         <div class="table-responsive">
          <table class="table border" id="myTable">
           <thead>
             <tr>
              <th>S.No.</th>
              <th>ServiceTypeName</th>
              <th>HSN Code</th>
              <th>Transcation Amount</th>
              <th>Discount</th>
              <th>Charge Amount </th>
              <th>Commission</th>
              <th>Taxable Amount</th>
              <th>TaxAmount</th> 
             </tr>
           </thead> 
           <tbody>
             <tr>
              <td>1</td>
              <td>Recharge Services</td>
              <td><div class="form-group">
                <input type="text" name="recharge_hsn_code" class="form-control" placeholder="HSN Code"></div></td>
              <td><div class="form-group">
                <input type="text" name="recharge_amount" class="form-control amount" placeholder="Recharge Amount" id="amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="recharge_discount" class="form-control" placeholder="Recharge Discount"></div></td>

              <td><div class="form-group">
                <input type="text" name="recharge_charge_amount" class="form-control" placeholder="Recharge Charge Amount"></div></td>
             <td><div class="form-group">
                <input type="text" name="recharge_commission_amount" class="form-control" placeholder="Recharge Commission Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="recharge_taxable_amount" class="form-control taxable_amount" placeholder="Recharge Taxable Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="recharge_tax_amount" class="form-control tax_amount" placeholder="Recharge Tax Amount"></div></td>
             </tr>

             <tr>
              <td>2</td>
              <td>BBPS Services</td>
              <td><div class="form-group">
                <input type="text" name="bbps_hsn_code" class="form-control" placeholder="HSN Code"></div></td>
              <td><div class="form-group">
                <input type="text" name="bbps_amount" class="form-control amount" placeholder="BBPS Amount" id="amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="bbps_discount" class="form-control" placeholder="BBPS Discount"></div></td>

              <td><div class="form-group">
                <input type="text" name="bbps_charge_amount" class="form-control" placeholder="BBPS Charge Amount"></div></td>
             <td><div class="form-group">
                <input type="text" name="bbps_commission_amount" class="form-control" placeholder="BBPS Commission Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="bbps_taxable_amount" class="form-control taxable_amount" placeholder="BBPS Taxable Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="bbps_tax_amount" class="form-control tax_amount" placeholder="BBPS Tax Amount"></div></td>
             </tr>

              <tr>
              <td>3</td>
              <td>Payout Services</td>
              <td><div class="form-group">
                <input type="text" name="payout_hsn_code" class="form-control" placeholder="HSN Code"></div></td>
              <td><div class="form-group">
                <input type="text" name="payout_amount" class="form-control amount" placeholder="Payout Amount" id="amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="payout_discount" class="form-control" placeholder="Payout Discount"></div></td>

              <td><div class="form-group">
                <input type="text" name="payout_charge_amount" class="form-control" placeholder="Payout Charge Amount"></div></td>
             <td><div class="form-group">
                <input type="text" name="payout_commission_amount" class="form-control" placeholder="Payout Commission Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="payout_taxable_amount" class="form-control taxable_amount" placeholder="Payout Taxable Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="payout_tax_amount" class="form-control tax_amount" placeholder="Payout Tax Amount"></div></td>
             </tr>

             <tr>
              <td>4</td>
              <td>AEPS Collection Services</td>
              <td><div class="form-group">
                <input type="text" name="aeps_hsn_code" class="form-control" placeholder="HSN Code"></div></td>
              <td><div class="form-group">
                <input type="text" name="aeps_amount" class="form-control amount" placeholder="AEPS Amount" id="amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="aeps_discount" class="form-control" placeholder="AEPS Discount"></div></td>

              <td><div class="form-group">
                <input type="text" name="aeps_charge_amount" class="form-control" placeholder="AEPS Charge Amount"></div></td>
             <td><div class="form-group">
                <input type="text" name="aeps_commission_amount" class="form-control" placeholder="AEPS Commission Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="aeps_taxable_amount" class="form-control taxable_amount" placeholder="AEPS Taxable Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="aeps_tax_amount" class="form-control tax_amount" placeholder="AEPS Tax Amount"></div></td>
             </tr>


              <tr>
              <td>5</td>
              <td>Service Activation Charge</td>
              <td><div class="form-group">
                <input type="text" name="service_hsn_code" class="form-control" placeholder="HSN Code"></div></td>
              <td><div class="form-group">
                <input type="text" name="service_amount" class="form-control amount" placeholder="Service Amount" id="amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="service_discount" class="form-control" placeholder="Service Discount"></div></td>

              <td><div class="form-group">
                <input type="text" name="service_charge_amount" class="form-control" placeholder="Service Charge Amount"></div></td>
             <td><div class="form-group">
                <input type="text" name="service_commission_amount" class="form-control" placeholder="Service Commission Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="service_taxable_amount" class="form-control taxable_amount" placeholder="Service Taxable Amount"></div></td>
              <td><div class="form-group">
                <input type="text" name="service_tax_amount" class="form-control tax_amount" placeholder="Service Tax Amount"></div></td>
             </tr>

             <tr>
              <td class="text-right" colspan="8"><b>Taxable Amount:</b></td>
               <td class="text-right"><input type="text" name="total_taxable_amount" class="form-control" id="total_taxable_amount" readonly=""></td> 
             </tr>
             <tr>
              <td class="text-right" colspan="8"><b>Total Tax Amount:</b></td>
               <td class="text-right"><input type="text" name="total_tax_amount" class="form-control" id="total_tax_amount" readonly=""></td> 
             </tr>
             <tr>
              <td class="text-right" colspan="8"><b>Total Amount:</b></td>
               <td class="text-right"><input type="text" id="total_sum_amount" name="total_amount" class="form-control" readonly=""></td> 
             </tr>
           </tbody>
          </table> 
         </div> 
       </div>


              </div>  




              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




