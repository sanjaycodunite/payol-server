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
          <h4>Amount</h4>
          <h4>Transaction ID :</h4>
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
          
          <h4><?php echo ($detail['txnID']) ? $detail['txnID'] : '&nbsp;'; ?></h4>
          
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
    
    <tbody>
        <tr>
        <td colspan="4">
  <!--          <table class="table table-bordered table-striped" width="100%" cellspacing="0">-->
  <!--              <tbody>-->
  <!--                  <tr>-->
  <!--                      <th>#</th>-->
  <!--                      <th>Date</th>-->
  <!--                      <th>CR/DR</th>-->
  <!--                      <th>Amount</th>-->
  <!--                      <th>Description</th>-->
  <!--                      </tr>-->
  <!--                      <tr>-->
  <!--                          <td>1</td>-->
  <!--                          <td>03/26</td>-->
  <!--                          <td><font color="red">DR</font></td>-->
  <!--                          <td>INR 450.85/-</td>-->
  <!--                          <td> UPI/Ovi Hosting  </td>-->
  <!--                          </tr>-->
  <!--                          <tr>-->
  <!--                              <td>2</td>-->
  <!--                              <td>03/25</td>-->
  <!--                              <td><font color="red">DR</font></td>-->
  <!--                              <td>INR 302.0/-</td>-->
  <!--                              <td> UPI/JIOIN APP DI </td>-->
  <!--                              </tr>-->
  <!--                              <tr>-->
  <!--                                  <td>3</td>-->
  <!--                                  <td>03/25</td>-->
  <!--                                  <td><font color="red">DR</font></td>-->
  <!--                                  <td>INR 302.0/-</td>-->
  <!--                                  <td> UPI/JIOIN APP DI </td>-->
  <!--                                  </tr>-->
  <!--                                  <tr>-->
  <!--                                      <td>4</td>-->
  <!--                                      <td>03/25</td>-->
  <!--                                      <td><font color="red">DR</font></td>-->
  <!--                                      <td>INR 100.0/-</td>-->
  <!--                                      <td> UPI/Shrikar  Sha </td>-->
  <!--                                      </tr>-->
  <!--                                      <tr>-->
  <!--                                          <td>5</td>-->
  <!--                                          <td>03/25</td>-->
  <!--                                          <td><font color="red">DR</font></td>-->
  <!--                                          <td>INR 154.0/-</td>-->
  <!--                                          <td> UPI/SHRIKAR SHAR </td>-->
  <!--                                          </tr>-->
  <!--                                          <tr>-->
  <!--                                              <td>6</td>-->
  <!--                                              <td>03/25</td>-->
  <!--                                              <td><font color="green">CR</font></td>-->
  <!--                                              <td>INR 564.0/-</td>-->
  <!--                                              <td> UPI/Swiggy/40854 </td></tr>-->
  <!--                                              <tr><td>7</td><td>03/25</td><td><font color="red">DR</font></td><td>INR 564.0/-</td><td> UPI/Swiggy/40853 </td></tr><tr><td>8</td><td>03/25</td><td><font color="green">CR</font></td><td>INR 573.44/-</td><td> UPI/ZOMATO LIMIT </td></tr></tbody></table></td>    -->
  <!--      </tr>-->
  <!--  </tbody>-->
    
  <!--  <tfoot style="border-top: 1px solid #000;">-->
  <!--    <tr>-->
  <!--      <th colspan="4"><h4 style=" margin:0 0 10px 0;"><?php echo $accountData['title']; ?></h4>-->
  <!--        <p style=" margin:0px;"><?php echo $address['address']; ?></p></th>-->
         
  <!--    </tr>-->

  <!--  </tfoot>-->

  <!--</table>-->
  <?php echo $str; ?>
  <br>  
  <img src="{site_url}skin/images/icici logo.png" style="width: 200px;"><br>

   <button onclick="window.print()" style="font-size: 10px; background-color: #03a9f4; padding: 8px 20px;    color: #fff;font-size: 15px; border: 0px;border-radius: 10px; margin-top: 20px;">Print</button>


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