<!DOCTYPE html>
<html>
<head>
  <title>Receipt</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700" rel="stylesheet">
</head>
<body>

<div class="main">
<table>
      <tr><caption>
    <img src="{site_url}skin/front/assets/images/logo/logo.png" style="width: 200px;">
    </caption></tr>
    <thead>
    <tr>
        <th colspan="2" style="text-align: left;">RECEIPT # : <?php echo $rechargeData['invoice_no']; ?></th>
        <th colspan="2" style="text-align: right;"><?php echo date('d M Y',strtotime($rechargeData['created'])); ?></th>
      </tr>
        
          <tr style="border: 1px solid #000;background-color: #eee;">
      <th colspan="4" style="text-align: center;">TRANSACTION DETAILS</th>
      </tr>

      <tr>
        <td colspan="2" width="100">
          <h4>Name:</h4>
          <h4>Service Provider:</h4>
          <h4>Service Number:</h4>
          <h4>Transaction ID:</h4>
          <h4>Operator Reference Number:</h4>
          <h4>Time</h4>
          <h4>Status</h4>
          <h4>Recharge Amount:</h4>
        </td>
        <td colspan="2">
          <h4><?php echo $rechargeData['member_name']; ?></h4>
          <h4><?php echo $rechargeData['operator_name']; ?></h4>
          <h4><?php echo $rechargeData['mobile']; ?></h4>
          <h4><?php echo ($rechargeData['txid']) ? $rechargeData['txid'] : '&nbsp;'; ?></h4>
          <h4><?php echo ($rechargeData['operator_ref']) ? $rechargeData['operator_ref'] : '&nbsp;'; ?></h4>
          <h4><?php echo date('h:i A',strtotime($rechargeData['created'])); ?></h4>
          <?php
          if($rechargeData['status']==1){
           ?>
          <h4>Pending</h4> 
          <?php } ?>

          <?php
          if($rechargeData['status']==2){
           ?>
          <h4>Success</h4> 
          <?php } ?>

          <?php
          if($rechargeData['status']==3){
           ?>
          <h4>Failed</h4>
          <?php } ?>
          <h4>Rs: <?php echo number_format($rechargeData['amount'],2); ?></h4>
        </td>
      </tr>

       <tr style="border: 1px solid #000;background-color: #eee;">
      <th colspan="2" style="text-align: left;">Net Amount</th>
      <th colspan="2" style="text-align: left;">Rs. <?php echo number_format($rechargeData['amount'],2); ?></th>
      </tr>
    </thead>
    
    <tfoot style="border-top: 1px solid #000;">
      <tr>
        <th colspan="4"><h4 style=" margin:0 0 10px 0;">Marwar Care</h4>
          <p style=" margin:0px;">FA - 200, Sector 5, Chitarkoot Scheme, Hirapura, Jaipur, Rajasthan – 302021</p></th>
      </tr>
    </tfoot>
  </table>  
</div>
<style type="text/css">
body{margin: 0px; padding: 0px;}  

table{
  border-collapse:collapse;
  margin:0 auto;font-family: 'Open Sans', sans-serif;
  width:700px;    border: 1px solid #000;
}
td, tr, th{
  padding:12px;
}
</style>
</body>
</html>