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
        <th colspan="2" style="text-align: left;">RECHARGE ID # : <?php echo $detail['recharge_display_id']; ?></th>
        <th colspan="2" style="text-align: right;"><?php echo date('d M Y',strtotime($detail['created'])); ?></th>
      </tr>
        
          <tr style="border: 1px solid #000;background-color: #eee;">
      <th colspan="4" style="text-align: center;">RECHARGE DETAILS</th>
      </tr>

      <tr style="text-align: left;">
        <td colspan="2" width="100">
          <h4>Member ID :</h4>
          <h4>Name :</h4>
          <h4>Service:</h4>
          <h4>Mobile</h4>
          <h4>Operator</h4>
          <h4>Amount</h4>
          <h4>Transaction ID :</h4>
          <h4>Status :</h4>
          <h4>Time :</h4>
          
        </td>
        <td colspan="2" style="text-align: left;">
          <h4><?php echo $detail['user_code']; ?></h4>
          <h4><?php echo $detail['name']; ?></h4>
          <h4>Recharge</h4>
          <h4><?php echo $detail['mobile']; ?></h4>
          <h4><?php echo $operator; ?></h4>
          <h4><?php echo $detail['amount']; ?></h4>
           
          <h4><?php echo ($detail['recharge_display_id']) ? $detail['recharge_display_id'] : '&nbsp;'; ?></h4>
          
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