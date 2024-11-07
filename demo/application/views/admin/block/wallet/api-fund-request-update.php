{system_message}
{system_info}
<div class="card shadow ">
  <div class="card-header py-3">
    <div class="row">
      <div class="col-sm-6">
        <h4><b>Update Fund Request</b></h4>
      </div>
      <div class="col-sm-6  text-right">
        <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
      </div>
    </div>
  </div>

  <div class="card-body">
    <?php echo form_open_multipart('admin/wallet/saveApiFundRequest', array('id' => 'admin_profile'), array('method' => 'post')); ?>

    <input type="hidden" value="<?php echo $site_url; ?>" id="siteUrl">
    <input type="hidden" name="request_id" value="<?php echo $requestID; ?>" id="siteUrl">
    <input type="hidden" name="member" value="<?php echo $member_id;?>">

    <div class="row">
     
      <div class="col-sm-2">
        <div class="form-group">
          <label><b>Available Balance</b></label>
          <input type="text" readonly="readonly" class="form-control" name="balance" value="{member_wallet_balance}"  placeholder="Available Balance">

        </div>
      </div>

      <div class="col-sm-2">
        <div class="form-group">
          <label><b>Amount*</b></label>
          <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount (In Numbers Only)" value="<?php echo $request_data['amount']; ?>" readonly>
          <?php echo form_error('amount', '<div class="error">', '</div>'); ?>
        </div>
      </div>


      <div class="col-sm-2">
        <div class="form-group">
          <label><b>Surcharge Type</b></label>
          <select class="selectpicker form-control" name="surcharge_type" id="surcharge_type" data-live-search="true">
            <option value="">Select Surcharge Type</option>
            <option value="0">Flate</option>
            <option value="1">Percentage (%)</option>
          </select>
          <?php echo form_error('surcharge_type', '<div class="error">', '</div>'); ?>
        </div>
      </div>

      
      <div class="col-sm-2">
        <div class="form-group">
          <label><b>Surcharge*</b></label>
          <input type="text" class="form-control" name="surcharge" id="surcharge" placeholder="surcharge (In Numbers Only)">
          <?php echo form_error('surcharge', '<div class="error">', '</div>'); ?>
          
        </div>
      </div>

      <div class="col-sm-2">
        <div class="form-group">
          <label><b>Final Amount*</b></label>
          <input type="text" class="form-control" name="final_amount" id="final_amount" placeholder="Final Amount (In Numbers Only)" readonly>
          <?php echo form_error('final_amount', '<div class="error">', '</div>'); ?>
        </div>
      </div>

       <div class="col-sm-4">
        <div class="form-group">
          <label><b>UTR No*</b></label>
          <input type="text" class="form-control" name="utr_no" id="utr_no" placeholder="UTR No" >
          <?php echo form_error('utr_no', '<div class="error">', '</div>'); ?>
        </div>
      </div>


      <div class="col-sm-4">
        <div class="form-group">
          <label><b>Description*</b></label>
          <textarea class="form-control" name="description" id="description"></textarea>
          <?php echo form_error('description', '<div class="error">', '</div>'); ?>
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