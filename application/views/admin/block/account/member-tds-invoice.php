<!DOCTYPE html>
<html lang="en" >

<head>

  <meta charset="UTF-8">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;1,400&display=swap" rel="stylesheet">


  <title>Invoice</title>

<style>
body{padding: 0px; margin: 0px;background: #eff1ff;}    
@media print {
.page-break { display: block; page-break-before: always; }
}
.recipet_bm {margin: 30px auto;width:80%;background: #FFF;padding: 10px;}
.recipet_bm h2 {font-size: 1.5em;font-family: 'Open Sans', sans-serif;}
.recipet_bm p {font-size: 12px;color: #666;font-family: 'Open Sans', sans-serif;line-height: 1.2em;font-weight: 600;}
.recipet_bm .top .logo img {width: 200px;}
.recipet_bm .title p {text-align: right;font-family: 'Open Sans', sans-serif;}
.recipet_bm table {width: 100%;border-collapse: collapse;}
td.tableitem p.itemtext {line-height: 0px;}
.recipet_bm .service {border-bottom: 1px solid #dedede;}
tr.thead_service th.thead_head {border: 1px solid #dedede;padding: 5px 20px;font-size: 14px;}
.recipet_bm .itemtext {font-size: 15px;font-family: 'Open Sans', sans-serif;}
.recipet_bm .recipet_footer {margin-top: 5mm;text-align: center;font-family: 'Open Sans', sans-serif;}
td.tableitem {border: 1px solid #dedede; padding: 5px 20px;}
.info {text-align: center;}
.clearfix {position: relative;clear: both;}
.passenger_details thead tr th {color: #666;padding-top: 10px;
font-size: 13px;font-weight: 300;}
.passenger_details tbody tr td p {font-size: 13px;color: #000;}
table.table tbody tr td {padding: 0 10px;font-family: 'Open Sans', sans-serif;}
table.table tbody tr th {border: 1px solid #dedede;font-size: 12px;}
.recipet_bm table thead {
    background: #eff1ff;
    height: 30px;
}
</style>

</head>
<body>
<div class="recipet_bm">
 <table class="table table-bordered">
      <tr>
        <td colspan="2">
          <div class="tb_logo_col">
           <div style="float: left;"><img src="{site_url}<?php echo $company_logo ?>" width="150"></div> 
        </div>
        </td>
     
        <td style="text-align: right;">
          <p><span>Email:</span> <a href="#" style="color: #000;text-decoration: none;">
          info.support@payol.in</a></p>
          <p><span>Phone No. : </span><a href="#" style="color: #000;text-decoration: none;">+91-8277998846</a></p>
         </td>
         </tr>
         <tr style="height: 50px;background: #3f6ec2;color:#fff;text-align: center;">
         <td colspan="4" style="font-size: 20px; font-weight: 600;">Tax Invoice</td> 
        </tr>  
       </table>

<table class="table table-bordered">
         <tbody> 
       <tr style="height: 50px;">
         <td style="padding: 20px 20px;">
        <p style="margin: 5px 0; color: #000;font-size: 15px;font-weight: 600;">Payol Digital Technologies Pvt Ltd</p>
        <p style="margin: 5px 0;font-size: 11px;"><?php echo $company_address; ?></p>
        <p style="margin: 5px 0;font-size: 11px;"><b>Email :</b> info.support@payol.in</p>
        <p style="margin: 5px 0;font-size: 11px;"><b>Phone No. :</b> +91-8277998846</p>
       <p style="margin: 5px 0;font-size: 11px;"><b>GSTIN : </b> 20AANCP7738A1ZL</p>
        <p style="margin: 5px 0;font-size: 11px;"><b>Pan No :</b> AANCP7738A</p>
       
        </td>
        <td colspan="3" rowspan="2" style="border-left: 1px solid #dedede;">
       <p style="margin: 5px 0; color: #000;font-size: 15px;font-weight: 600;">Invoice</p>
       <p style="margin: 5px 0;font-size: 11px;"><b> Invoice #:</b> <?php echo $invoice_id; ?></p>
       <p style="margin: 5px 0;font-size: 11px;"><b>Issue Date : </b> <?php echo date('d-M-Y',strtotime($invoice_date)); ?></p>
      <!--  <p style="margin: 5px 0;font-size: 11px;"><b>Billing Cycle : </b>  01 Jan 2023</p>
       <p style="margin: 5px 0;font-size: 11px;">TO 31 Jan 2023</p> -->
       <p style="margin: 5px 0;font-size: 11px;"><b>For Month : </b> <?php echo $invoice_month ?> <?php echo $invoice_year; ?></p>
        </td>
      
        </tr>
          <tr style="height: 50px;">
        <td style="padding: 20px 20px;border-top: 1px solid #dedede;">
        <p style="margin: 5px 0; color: #000;font-size: 12px;font-weight: 600; margin-bottom: 10px;">Buyer Informaon</p>
        <p style="margin: 5px 0; color: #000;font-size: 12px;font-weight: 600; margin-bottom: 10px;"><?php echo $user_name ?></p>
        <p style="margin: 5px 0;font-size: 11px;"></p>
        <p style="margin: 5px 0;font-size: 11px;"><b>Member ID:</b> <?php echo $user_code ?></p>
        <!-- <p style="margin: 5px 0;font-size: 11px;"><b>GSTIN:</b> </p> -->
        <p style="margin: 5px 0;font-size: 11px;"><b>Contact Person : </b> <?php echo $user_email ?></p>
        <p style="margin: 5px 0;font-size: 11px;"><b>Mobile No.:</b> <?php echo $user_mobile ?></p>
        </td>
      </tr>
  </tbody>  
  </table>

  <table class="table table-bordered">
  <tbody> 
<tr style="height: 40px;background: #3f6ec2; text-align: center;">
         <td colspan="4" style="font-size: 15px; font-weight: 600;color: #fff;">Tax Data</td> 
        </tr>
  </tbody>  
  </table>


 <table class="table table-bordered">
          <thead>
           <tr>
           <th style="font-size: 12px;font-family: 'Open Sans', sans-serif;border: 1px solid #dedede;">Sno</th>
           <th style="font-size: 12px;font-family: 'Open Sans', sans-serif;border: 1px solid #dedede;">ServiceTypeName</th>
           <th style="font-size: 12px;font-family: 'Open Sans', sans-serif;border: 1px solid #dedede;" >Commission Amount</th> 
            <th style="font-size: 12px;font-family: 'Open Sans', sans-serif;border: 1px solid #dedede;">Tds Amount</th>
           
           </tr>
          </thead>
         <tbody> 
       <tr>
        <td style="font-size: 10px;border: 1px solid #dedede;height: 30px;">1</td>
        <td style="font-size: 11px;border: 1px solid #dedede;height: 30px;text-align: center;    font-weight: 600;">Service Commission </td>
         <td style="font-size: 10px;border: 1px solid #dedede;height: 30px;text-align: center;font-weight: 600;"><?php echo number_format($total_com_amount,2).' /-'; ?></td>
        <td style="font-size: 11px;border: 1px solid #dedede;height: 30px;text-align: center;    font-weight: 600;"><?php echo number_format($total_tds_amount,2).' /-'; ?></td>
        
        </tr>
     
        </tr>
       </tbody>  
       </table>


       <table class="table table-bordered">
         <tbody> 
       <tr style="height: 50px;">
        <td colspan="2">
        <p style="margin: 0px; text-align: center; color: #000; font-weight: 600;">NOTE: This is Computer Generated Invoice. No Need of Seal and Signature.</p>
        </td>
         <!-- <td colspan="2">
        <p style="margin: 0px; text-align: right; color: #000; font-weight: 600;"><img src="{site_url}skin/images/seal.png" style="width:130px;"></p>
        </td> -->
        </tr>
        </tbody>  
       </table>



</div><!--End -->

  




</body>

</html>