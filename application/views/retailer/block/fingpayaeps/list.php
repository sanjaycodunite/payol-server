{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>AEPS</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                    
                    
               <div id="timer"></div>


                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('retailer/transfer/transferAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $loggedUser['id'];?>" id="memberID">
              <div class="row">
              <div class="col-sm-12">
                <h5>Service Type</h5>
                <hr />
              </div>
			        <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="serviceType" id="serviceType1" value="balinfo">
                  <label for="serviceType1"><b>Balance Enquiry</b></label>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="serviceType" id="serviceType2" value="ministatement">
                  <label for="serviceType2"><b>Mini Statement</b></label>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="serviceType" id="serviceType3" value="balwithdraw">
                  <label for="serviceType3"><b>Withdrawal</b></label>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="serviceType" id="serviceType4" value="aadharpay">
                  <label for="serviceType4"><b>Aadhar Pay</b></label>
                </div>
              </div>
              
            </div>
             <div class="row">
              <div class="col-sm-12">
                <h5>Device</h5>
                <hr />
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="deviceType" id="deviceType1" value="MANTRA_PROTOBUF">
                  <label for="deviceType1"><b>Mantra</b></label>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <input type="radio" name="deviceType" id="deviceType2" value="MORPHO_PROTOBUF">
                  <label for="deviceType2"><b>Morpho</b></label>
                </div>
              </div>

              
            </div>
            <div class="row">
              <div class="col-sm-12">
                <h5>Personal Detail</h5>
                <hr />
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Mobile No.*</b></label>
              <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile No.">
              
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Aadhar No*</b></label>
              <input type="text" class="form-control" name="aadhar_no" id="aadhar_no" placeholder="Aadhar No">
              
              </div>
              </div>
			         <div class="col-sm-3">
              <div class="form-group">
              <label><b>Amount*</b></label>
              <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount (In Numbers Only)" value="0">
              
              </div>
              </div>

              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Bank*</b></label>
              <select class="form-control selectpicker" name="bankID" id="bankID" data-live-search="true">
                <option value="">Select Bank</option>
                <?php if($bankList){ ?>
                  <?php foreach($bankList as $list){ ?>
                    <option value="<?php echo $list['iinno']; ?>"><?php echo $list['bank_name']; ?></option>
                  <?php } ?>
                <?php } ?>
              </select>
              
              </div>
              </div>

              
              </div>

              
              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="button" class="btn btn-success" onclick="CallCapture();">Scan & Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>

<!-- Modal -->
<div id="aepsResponseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">AEPS Response</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body aeps-response">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" width="100%" cellspacing="0">
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>CR/DR</th>
              <th>Amount</th>
              <th>Description</th>
            </tr>
          </table>
        </div>
      </div>
      
    </div>

  </div>
</div>


 <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


<!--<script>-->
<!--        $(document).ready(function () {-->
            // Function to update the timer
<!--            function updateTimer() {-->
               
<!--                var memberID = $("#memberID").val();-->
<!--                $.ajax({                -->
<!--			url:siteUrl+'retailer/fingpayAeps/update2FaStatus/'+memberID,                        -->
<!--			success:function(r){-->
				
<!--				var data = JSON.parse($.trim(r));-->
<!--				if(data["status"] == 1){-->
					
					 
<!--					 window.location.replace(siteUrl+'retailer/fingpayAeps');-->
<!--				}-->
			
<!--			}-->
<!--		});-->
		
<!--            }-->

            // Initial call to update the timer
            //updateTimer();

            // Set interval to update the timer every 3 minutes
            setInterval(updateTimer, 3 * 60 * 1000); // 3 minutes in milliseconds
<!--        });-->
<!--    </script>-->

    

