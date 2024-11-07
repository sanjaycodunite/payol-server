<!DOCTYPE html>
<html>
<head>
  <title>Receipt</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700" rel="stylesheet">
</head>
<body>

<div class="main invoice_">
<?php $chargeAmount = round((1/100)*$detail['transfer_amount']); ?>
<?php if($chargeAmount < 10){ $chargeAmount = 10; }?>
<table style="width: 40%;">
      <tr><caption>
    <img src="{site_url}<?php echo $accountData['image_path']; ?>" style="width: 200px;">
    </caption></tr>
    <thead>
    <tr>
        <th colspan="2" style="text-align: left; width: 50%;">RECEIPT # : <?php echo $detail['invoice_no']; ?></th>
        <th colspan="2" style="text-align: right; width: 50%;"><?php echo date('d M Y',strtotime($detail['created'])); ?></th>
      </tr>
        
          <tr style="border: 1px solid #000;background-color: #eee;">
      <th colspan="4" style="text-align: center;">TRANSACTION DETAILS</th>
      </tr>
      <tr>
        <td style="text-align: left; width: 73%;"><b>Member ID :</b></b></td>
        <td style="text-align: left;"><?php echo $detail['memberID']; ?></td>
      </tr>
      <tr>
        <td style="text-align: left;"><b>Beneficiary Name :</b></td>
        <?php if($detail['verified_name']){ ?>
        <td style="text-align: left; color: green;"><?php echo $detail['verified_name']; ?></td>
        <?php } else { ?>
        <td style="text-align: left;"><?php echo $detail['account_holder_name']; ?></td>
        <?php } ?>
      </tr>
      <tr>
        <td style="text-align: left;"><b>Account Number :</b></td>
        <td style="text-align: left;"><?php echo $detail['account_no']; ?></td>
      </tr>
      <tr>
        <td style="text-align: left;"><b>IFSC Code :</b></td>
        <td style="text-align: left;"><?php echo $detail['ifsc']; ?></td>
      </tr>
      <tr>
        <td style="text-align: left;"><b>Txn ID :</b></td>
        <td style="text-align: left;"><?php echo ($detail['transaction_id']) ? $detail['transaction_id'] : '&nbsp;'; ?></td>
      </tr>
      <tr>
        <td style="text-align: left;"><b>RRN :</b></td>
        <td style="text-align: left;"><?php echo ($detail['rrn']) ? $detail['rrn'] : '&nbsp;'; ?></td>
      </tr>
      <tr>
        <td style="text-align: left;"><b>Time :</b></td>
        <td style="text-align: left;"><?php echo date('h:i A',strtotime($detail['created'])); ?></td>
      </tr>
      <tr>
        <td style="text-align: left;"><b>Status :</b></td>
        <td style="text-align: left;">
          <?php
            if($detail['status']==1){
              echo 'Processing';
            }elseif($detail['status']==2){
              echo '<font color="#03a9f4">Pending</font>';
            }elseif($detail['status']==3){
              echo '<font color="green">Success</font>';
            }
            elseif($detail['status']==4){
              echo '<font color="red">Failed</font>';
            }
           ?>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b>Transfer Amount (Rs) :</b></td>
        <td style="text-align: left;"><?php echo number_format($detail['transfer_amount'],2); ?> /-</td>
      </tr>
      <tr>
        <td style="text-align: left;"><b>Charge Amount (Rs) :</b></td>
        <td style="text-align: left;"><?php echo $chargeAmount; ?> /-</td>
      </tr>
       <tr style="border: 1px solid #000;background-color: #eee;">
      <th colspan="2" style="text-align: left; width: 50%;">Net Amount (Rs) :</th>
      <th colspan="2" style="text-align: right; width: 50%;"><?php echo number_format($detail['transfer_amount'] + $chargeAmount,2); ?> /-</th>
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