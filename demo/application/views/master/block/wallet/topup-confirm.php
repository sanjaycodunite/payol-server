{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Topup Confirmation</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            
           <div class="row">
              <div class="col-sm-4"></div>
              <div class="col-sm-4">
              <table class="table table-bordered table-striped">
                <tr>
                  <td><b>Name</b></td>
                  <td><?php echo $client_name; ?></td>
                </tr>
                <tr>
                  <td><b>Email</b></td>
                  <td><?php echo $client_email; ?></td>
                </tr>
                <tr>
                  <td><b>Mobile</b></td>
                  <td><?php echo $client_mobile; ?></td>
                </tr>
                <tr>
                  <td><b>Transaction Amount</b></td>
                  <td>&#8377; <?php echo $amount; ?></td>
                </tr>
                <?php if($is_surcharge){ ?>
                <tr>
                  <td><b>Charge Amount</b></td>
                  <td>&#8377; <?php echo $com_amount; ?></td>
                </tr>
                <?php } else { ?>
                <tr>
                  <td><b>Commission Amount</b></td>
                  <td>&#8377; <?php echo $com_amount; ?></td>
                </tr>
                <?php } ?>
                <?php if($is_surcharge){ ?>
                <tr>
                  <td><b>Credit Amount</b></td>
                  <td>&#8377; <?php echo $amount - $com_amount; ?></td>
                </tr>
                <?php } else { ?>
                <tr>
                  <td><b>Credit Amount</b></td>
                  <td>&#8377; <?php echo $amount + $com_amount; ?></td>
                </tr>
                <?php } ?>
                <tr>
                  <td colspan="2" align="center">
                    <button id="rzp-button1" class="btn btn-primary">Confirm</button>
                    
                    <form name='razorpayform' action="<?php echo base_url('master/wallet/paymentResponse'); ?>" method="POST">
                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                        <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
                        <input type="hidden" id="name" name="name" value="<?php echo $client_name; ?>">
                        <input type="hidden" id="email" name="email" value="<?php echo $client_email; ?>">
                        <input type="hidden" id="mobile" name="mobile" value="<?php echo $client_mobile; ?>">
                        <input type="hidden" id="shopping_order_id" name="shopping_order_id" value="<?php echo $request_id; ?>">
                        <input type="hidden" id="order_id" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" id="amount" name="amount" value="<?php echo $amount; ?>">
                        <input type="hidden" id="loggedAccountID" name="loggedAccountID" value="<?php echo $loggedAccountID; ?>">
                    </form>
                    <!-- Any extra fields to be submitted with the form but not sent to Razorpay -->
                    
                    
                  </form> 
                  </td>
                </tr>
              </table>
              </div>
             <div class="col-sm-4"></div>
          
              </div>

              
              
          </div>
        </div>
        <div class="card shadow">
        
        </div>    
 
    </div>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
// Checkout details as a json
var options = <?php echo $jsondata; ?>;

/**
 * The entire list of Checkout fields is available at
 * https://docs.razorpay.com/docs/checkout-form#checkout-fields
 */
options.handler = function (response){
    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
    document.getElementById('razorpay_signature').value = response.razorpay_signature;
    document.razorpayform.submit();
};

// Boolean whether to show image inside a white frame. (default: true)
options.theme.image_padding = false;

options.modal = {
    ondismiss: function() {
        console.log("This code runs when the popup is closed");
    },
    // Boolean indicating whether pressing escape key 
    // should close the checkout form. (default: true)
    escape: true,
    // Boolean indicating whether clicking translucent blank
    // space outside checkout form should close the form. (default: false)
    backdropclose: false
};

var rzp = new Razorpay(options);

document.getElementById('rzp-button1').onclick = function(e){
    rzp.open();
    e.preventDefault();
}
</script>





