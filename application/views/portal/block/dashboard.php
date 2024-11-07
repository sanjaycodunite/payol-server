        <!-- Begin Page Content -->
        <div class="container-fluid">
{system_message}               
              {system_info}
          <!-- Page Heading -->
          <div class="row" style="margin-bottom: 20px;">

                <!-- <?php if($total_direct < 3){ ?> -->
              

              <!-- <?php } ?> -->
              <div class="col-md-12">
                 <?php 

                  $get_kyc_status = $this->db->where_in('status',array(1,2,3))->get_where('portal_kyc',array('user_id'=>$loggedUser['id'],'account_id'=>$account_id))->row_array();

                  ?>
                  <?php if(!$get_kyc_status) { ?>

                 <div class="alert alert-danger alert-dismissable">Kindly complete your KYC to use the services. <a href="{site_url}portal/kyc" class="text-primary"> Do Kyc Now</a></div>
               <?php } elseif($get_kyc_status['status'] == 1) {?>

                <div class="alert alert-warning alert-dismissable">Kyc Is Pending Please Wait.</div>

              <?php } elseif($get_kyc_status['status'] == 3) {?>
                  <div class="alert alert-danger alert-dismissable">Your Kyc Was Rejected Please Do Again . <a href="{site_url}portal/kyc" class="text-primary"> Do Kyc Again</a></div>
                <?php } ?>
                <h1 class="h3 mb-0 text-gray-800">Welcome to API Panel </h1>
              </div>
              
            </div>
        <!--Start-History1-->
         <div class="dash_card card shadow">
          <div class="card_heading">
           <h1 class="h3 mb-0 text-gray-800">Total Summary</h1>
         </div>
        <div class="row card_row_col">
        <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL DEBIT FUND</a></h5>  
         <h3>₹ <?php echo number_format($total_debit_fund,2); ?> / <?php echo $total_debit_fund_record; ?></h3> 
        </div>  
       </div> 
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL CREDIT FUND</a></h5>  
         <h3>₹ <?php echo number_format($total_credit_fund,2); ?> / <?php echo $total_credit_fund_record; ?></h3> 
        </div>  
       </div>
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL SUCCESS PAYOUT</a></h5>  
          <h3>₹ <?php echo number_format($total_success_fund,2); ?> / <?php echo $total_success_record; ?></h3> 
        </div>  
       </div>
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL PENDING PAYOUT</a></h5>  
         <h3>₹ <?php echo number_format($total_pending_fund,2); ?> / <?php echo $total_pending_record; ?></h3> 
        </div>  
       </div> 
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL FAILED PAYOUT</a></h5>  
        <h3>₹ <?php echo number_format($total_failed_fund,2); ?> / <?php echo $total_failed_record; ?></h3> 
        </div>  
       </div> 
        </div>

         </div>


         <div class="dash_card card shadow">
          <div class="card_heading">
           <h1 class="h3 mb-0 text-gray-800">Today Summary</h1>
         </div>
        <div class="row card_row_col">
        <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">OPENING BALANCE</a></h5>  
         <h3>₹ <?php echo number_format($today_opening_balance,2); ?></h3> 
        </div>  
       </div> 
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">PURCHASE BALANCE</a></h5>  
         <h3>₹ <?php echo number_format($today_credit_fund,2); ?> / <?php echo $today_credit_fund_record; ?></h3> 
        </div>  
       </div>
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="{site_url}portal/report/newPayoutReport/3">SUCCESS PAYOUT</a></h5>  
          <h3>₹ <?php echo number_format($today_success_fund,2); ?> / <?php echo $today_success_record; ?></h3> 
        </div>  
       </div>
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="{site_url}portal/report/newPayoutReport/2">PENDING PAYOUT</a></h5>  
         <h3>₹ <?php echo number_format($today_pending_fund,2); ?> / <?php echo $today_pending_record; ?></h3> 
        </div>  
       </div> 
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">REFUND PAYOUT</a></h5>  
        <h3>₹ <?php echo number_format($today_failed_fund,2); ?> / <?php echo $today_failed_record; ?></h3> 
        </div>  
       </div> 

        

        </div>

         </div>


         <!--END-History1-->
        
 <!--Start-History2-->
        <!--  <div class="dash_card card shadow">
          <div class="card_heading">
           <h1 class="h3 mb-0 text-gray-800">Today History</h1>
         </div>
        <div class="row card_row_col">
        <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL SUCCESS</a></h5>  
         <h3>₹ 433.00 / 3</h3> 
        </div>  
       </div> 
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL PENDING</a></h5>  
         <h3>₹ 433.00 / 3</h3> 
        </div>  
       </div>
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL SUCCESS</a></h5>  
         <h3>₹ 433.00 / 3</h3> 
        </div>  
       </div>
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL SUCCESS</a></h5>  
         <h3>₹ 433.00 / 3</h3> 
        </div>  
       </div> 
       <div class="col service_col">
        <div class="card_body_dash">
         <h5><a href="#">TOTAL Failed</a></h5>  
         <h3>₹ 433.00 / 3</h3> 
        </div>  
       </div> 
        </div>

         </div> -->
         <!--END-History2-->

      </div>
      <!-- End of Main Content -->

    </div>