<!DOCTYPE html>
<html lang="en" >

<head>

  <meta charset="UTF-8">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;1,400&display=swap" rel="stylesheet">


  <title>Receipt</title>

  <style>
@media print {
    .page-break { display: block; page-break-before: always; }
}
.recipet_bm {
    border: 1px solid #eee;
  padding: 5mm;
  margin: 0 auto;
  width: 230mm;
  background: #FFF;
}


.recipet_bm p {
  font-size: .7em;
  color: #666;font-family: sans-serif;
line-height: 1.2em;
}

.recipet_bm .top {
  min-height: 100px;
}
.recipet_bm .date_time {
  min-height: 55px;
}
.recipet_bm .tr_box {
  min-height: 50px;
}
.recipet_bm .top .logo img {
    width: 200px;
}


.recipet_bm .title {
  float: right;
}
.recipet_bm .title p {
  text-align: right;font-family: 'Open Sans', sans-serif;
}
.recipet_bm table {
  width: 100%;
  border-collapse: collapse;
}

td.tableitem p.itemtext {
    line-height: 0px;
}
.recipet_bm .service {
  border-bottom: 1px solid #EEE;
}

.recipet_bm .itemtext {
  font-size: 15px;font-family: 'Open Sans', sans-serif;
}
.recipet_bm .recipet_footer {
  margin-top: 5mm;    text-align: center;font-family: 'Open Sans', sans-serif;
}


.recipet_download .btn.btn-download {
    background: #104382;    cursor: pointer;
    color: #fff;
    padding: 9px 25px;
    border-radius: 5px;
}
   </style>

 


</head>

<body>


  <div class="recipet_bm">
                  
    <div class="top">
       <table class="table">
 <tr class="service">
      <td class="logo"><img src="https://www.trustcart.tech/media/account/916295005.png" style="width: 190px;"></td>
       <td class="top_details" style="text-align: right;"><h4 style="margin: 0px;font-family: 'Open Sans', sans-serif;font-weight: 900;text-transform: uppercase;">Trustncart Private Limited</h4>  
      <p>2nd floor, elegance tower,  Jasola, Okhla New Delhi 110025</p>
      <p>9990000229</p>
     <p>GST No: 07AAICT6601M1Z1</p></td>
       </tr>
         </table> 
  
     
    </div><!--End Top-->

    <div class="date_time">
         <table class="table">
          <thead>
          <tr><td style="text-align: center;background: #eee;padding: 8px;font-family: 'Open Sans';">Invoice</td></tr></thead>
        </table>
    
    <table class="table">
    <tbody>
 <tr>
  <td style="font-size: 12px;font-family: 'Open Sans', sans-serif;font-weight: 600;">Bill To: <span><?php echo $userData['name']; ?></span></td>
  <td style="font-size: 12px;font-family: 'Open Sans', sans-serif;font-weight: 600;">Invoice Number: <span>TCART-<?php echo $txnData['id']; ?></span></td>
 </tr>
  <tr>
  <td style="font-size: 12px;font-family: 'Open Sans', sans-serif;font-weight: 600;padding: 10px 0;">Address: <span><?php echo $userData['address']; ?></span></td>
  <td style="font-size: 12px;font-family: 'Open Sans', sans-serif;font-weight: 600;">Invoice Date: <span><?php echo date('d-m-Y h:i:s a',strtotime($txnData['created'])); ?></span></td>
 </tr>
  <tr>
  <td style="font-size: 12px;font-family: 'Open Sans', sans-serif;font-weight: 600;">Mobile No: <span><?php echo $userData['mobile']; ?></span></td>
  <td></td>
 </tr>
<tr>
  <td style="font-size: 12px;font-family: 'Open Sans', sans-serif;font-weight: 600;">GST No: <span><?php echo $userData['gst_no']; ?></span></td>
  <td></td>
 </tr>
</tbody>  
    </table>
    </div><!--End -->

    <div class="tr_box" style="margin-top: 20px;">
  <table class="table table-bordered" style="border: 1px solid #eee;">
   <thead>
     <tr style="background: #104382;font-family: 'Open Sans', sans-serif;font-weight: 600; font-size: 14px;">
     <th style="padding: 7px 0;color: #fff;">Sr.No.</th>  
     <th style="padding: 7px 0;color: #fff;">Service Name</th> 
     <th style="padding: 7px 0;color: #fff;">Bank RRN</th> 
     <th style="padding: 7px 0;color: #fff;">Transfer Amount</th> 
     
     </tr>
   </thead>
   <tbody>
    <tr style="text-align: center;font-family: 'Open Sans', sans-serif;font-weight: 600;font-size: 14px;">
     <td style="padding: 7px 0;">1</td> 
     <td style="padding: 7px 0;">Merchant Payout</td>
     <td style="padding: 7px 0;"><?php echo $txnData['rrn']; ?></td>
     <td style="padding: 7px 0;"><?php echo $txnData['transfer_amount']; ?> /-</td>
     
    </tr> 
   </tbody> 
  </table>
                       
              <div class="recipet_footer">
                       <div class="recipet_download">
                        <button class="btn btn-download" onclick="window.print()" style="font-size: 10px; background-color: #03a9f4; padding: 8px 20px;    color: #fff;font-size: 15px; border: 0px;border-radius: 10px; margin-top: 20px;">Print</button>
                       </div>
                    </div>

                </div><!--End -->
  </div><!--End -->






</body>

</html>