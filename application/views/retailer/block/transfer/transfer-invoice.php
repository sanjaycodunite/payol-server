<!DOCTYPE html>
<html>
<head>
  <title>Receipt</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700" rel="stylesheet">
</head>
<body>

<div class="main invoice_">
<table>
      <tr><caption>
        <?php 
      $accountData = $this->User->get_account_data($detail['account_id']);
    ?> 
    <img src="{site_url}<?php echo $accountData['image_path']; ?>" style="width: 200px;">
    </caption></tr>
    <thead>
    <tr>
        <th colspan="2" style="text-align: left;">RECEIPT # : <?php echo $detail['transcation_id']; ?></th>
        <th colspan="2" style="text-align: right;"><?php echo date('d M Y',strtotime($detail['created'])); ?></th>
      </tr>
        
          <tr style="border: 1px solid #000;background-color: #eee;">
      <th colspan="4" style="text-align: center;">TRANSACTION DETAILS</th>
      </tr>

      <tr style="text-align: left;">
        <td colspan="2" width="100">
          <h4> Member ID :</h4>
          <h4>Beneficiary Name :</h4>
          <h4>Account Number:</h4>
          <h4>IFSC Code :</h4>
          <h4>Transaction ID :</h4>
          <h4>RRN :</h4>
          <h4>Time :</h4>
          <h4>Status :</h4>
          <h4>Transfer Amount (Rs):</h4>
          <h4>Charge Amount (Rs):</h4>
        </td>
        <td colspan="2">
          <h4><?php echo $detail['memberID']; ?></h4>

          <?php if($detail['txnType'] == 'UPI') {?>
          <h4><?php echo $detail['holder_name']; ?></h4>
          <?php }  else { ?>  
            <h4><?php echo $detail['account_holder_name']; ?></h4>
          <?php } ?>
          <?php if($detail['txnType'] == 'UPI') {?>
          <h4><?php echo $detail['holder_account']; ?></h4>
          <?php }  else { ?>  
            <h4><?php echo $detail['account_no']; ?></h4>
          <?php } ?>

          <?php if($detail['txnType'] != 'UPI'){ ?>
          <h4><?php echo $detail['ifsc']; ?></h4>
          
          <?php } else {?>
             <h4>Not Available</h4>
             
             <?php } ?>  
          <h4><?php echo ($detail['transaction_id']) ? $detail['transaction_id'] : '&nbsp;'; ?></h4>
          <h4><?php echo ($detail['rrn']) ? $detail['rrn'] : '&nbsp;'; ?></h4>
          <h4><?php echo date('h:i A',strtotime($detail['created'])); ?></h4>
          <?php
          if($detail['status']==1){
           ?>
          <h4>Processing</h4> 
          <?php } ?>

          <?php
          if($detail['status']==2){
           ?>
          <h4 style="color: #03a9f4;font-weight: bold;">Pending</h4> 
          <?php } ?>

          <?php
          if($detail['status']==3){
           ?>
          <h4 style="color: green;font-weight: bold;">Success</h4>
          <?php } ?>
          <?php
          if($detail['status']==4){
           ?>
          <h4 style="color: red;font-weight: bold;">Failed</h4>
          <?php } ?>
          <h4> <?php echo number_format($detail['transfer_amount'],2); ?> /-</h4>
          <h4> <?php echo number_format($detail['transfer_charge_amount'],2); ?> /-</h4>
          
        </td>
      </tr>

       <tr style="border: 1px solid #000;background-color: #eee;">
      <th colspan="2" style="text-align: left;">Net Amount (Rs) :</th>
      <th colspan="2" style="text-align: right;"><?php echo number_format($detail['total_wallet_charge'],2); ?> /-</th>
      </tr>
    </thead>
    
    <tfoot style="border-top: 1px solid #000;">
      <tr>
        <th colspan="4"><h4 style=" margin:0 0 10px 0;"><?php echo $accountData['title']; ?></h4>
          <p style=" margin:0px;"><?php echo $address['address']; ?></p></th>
      </tr>
    </tfoot>
  </table>  
  <button onclick="window.print()" style="font-size: 10px; background-color: #03a9f4; padding: 8px 20px;    color: #fff;font-size: 15px; border: 0px;border-radius: 10px; margin-top: 20px;">Print</button>

  <a href="javascript:void(0)" onclick="window.close()" style="font-size: 10px; background-color: #03a9f4; padding: 8px 20px;    color: #fff;font-size: 15px; border: 0px;border-radius: 10px; margin-top: 20px;">Back</a>
</div>
<style type="text/css">
body{margin: 0px; padding: 0px;}  

table{
  border-collapse:collapse;font-size: 12px;
  margin:0 auto;font-family: 'Open Sans', sans-serif;
  width:350px;    border: 1px solid #000;
}
td, tr, th{
  padding:12px;
}
h4 {
    margin: 0;
    margin-bottom: 13px;
    font-weight: 400;
}
.main.invoice_ {text-align: center;}
</style>
</body>
</html>