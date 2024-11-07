<DOCTYPE html>
<html>
<head>
  <title>Aeps Receipt</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700" rel="stylesheet">
</head>
<body>

<div class="main invoice_">
<table>
      <tr><caption>
    <img src="{site_url}<?php echo $accountData['image_path']; ?>" style="width: 200px;">
    </caption></tr>
    <thead>
    <tr>
        <th colspan="2" style="text-align: left;">RECEIPT # : <?php echo $detail['receipt_id']; ?></th>
        <th colspan="2" style="text-align: right;"><?php echo date('d M Y',strtotime($detail['created'])); ?></th>
      </tr>
        
          <tr style="border: 1px solid #000;background-color: #eee;">
      <th colspan="4" style="text-align: center;">TRANSACTION DETAILS</th>
      </tr>

      <tr>
        <td colspan="2" width="100">
          <h4>Member ID :</h4>
          <h4>Name :</h4>
          <h4>Service:</h4>
          <h4>Aadhar No :</h4>
          <h4>Mobile</h4>
          <h4>Txn Amount</h4>
          <h4>Balance Amount</h4>
          <h4>Transaction ID :</h4>
          <h4>Bank RRN :</h4>
          <h4>Status :</h4>
          <h4>Time :</h4>
          
        </td>
        <td colspan="2" style="text-align: right;">
          <h4><?php echo $detail['member_code']; ?></h4>
          <h4><?php echo $detail['member_name']; ?></h4>
          <h4><?php echo $detail['service']; ?></h4>
          <h4><?php echo $detail['aadhar_no']; ?></h4>
          <h4><?php echo $detail['mobile']; ?></h4>
          <h4><?php echo $detail['amount']; ?></h4>
          
          <h4><?php echo ($detail['balance_amount']) ? $detail['balance_amount'] : '&nbsp;'; ?></h4>
          
          <h4><?php echo ($detail['txnID']) ? $detail['txnID'] : '&nbsp;'; ?></h4>
           <h4><?php echo ($detail['bank_rrno']) ? $detail['bank_rrno'] : '&nbsp;'; ?></h4>
          
           <?php
          if($detail['status']==1){
           ?>
          <h4 style="color: orange;font-weight: bold;">Pending</h4> 
          <?php } ?>

          <?php
          if($detail['status']==2){
           ?>
          <h4 style="color: green;font-weight: bold;">Success</h4>
          <?php } ?>


          <?php
          if($detail['status']==3){
           ?>
          <h4 style="color: red;font-weight: bold;">Failed</h4> 
          <?php } ?>

          <h4><?php echo date('h:i A',strtotime($detail['created'])); ?></h4>
         
          
        </td>
      </tr>

       <tr style="border: 1px solid #000;background-color: #eee;">
      
      </tr>
    </thead>
    
    <tfoot style="border-top: 1px solid #000;">
      <tr>
        <th colspan="4"><h4 style=" margin:0 0 10px 0;"><?php echo $accountData['title']; ?></h4>
          <p style=" margin:0px;"><?php echo $address['address']; ?></p></th>
         
      </tr>

    </tfoot>

  </table>
  <br>  
  
  <button onclick="window.print()" style="font-size: 10px; background-color: #03a9f4; padding: 8px 20px;    color: #fff;font-size: 15px; border: 0px;border-radius: 10px; margin-top: 20px;">Print</button>
   

<a href="{site_url}retailer/iciciaeps/transactionHistory" style="font-size: 10px; background-color: #03a9f4; padding: 8px 20px;    color: #fff;font-size: 15px; border: 0px;border-radius: 10px; margin-top: 20px;">Back</a>

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