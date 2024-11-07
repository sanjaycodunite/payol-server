$(document).ready(function() {	
	
	$("#submit-btn").click(function(){

			$("#admin_profile").submit();
		    $('#submit-btn').prop('disabled', true);

	});
	$("#serviceType1").click(function(){
	 	if($('#serviceType1').is(':checked')) { 
	 		$("#aepsAmountBlock").css('display','none');
	 	}
	});
	$("#serviceType2").click(function(){
	 	if($('#serviceType2').is(':checked')) { 
	 		$("#aepsAmountBlock").css('display','none');
	 	}
	});
	$("#serviceType3").click(function(){
	 	
	 	$("#aepsAmountBlock").css('display','block');
	 	
	});
	$("#serviceType4").click(function(){
	 	
	 	$("#aepsAmountBlock").css('display','block');
	 	
	});
	$("#rechargeComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		if(memberID != 0)
		{
			$.ajax({                
				url:siteUrl+'master/master/getRechargeCommData/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$(".recharge-comm-loader").html('');
						$("#recharge-comm-block").html(data['str']);
						
					}
					else
					{
						$(".recharge-comm-loader").html('<font color="red">'+data['msg']+'</font>');
					}
				}
			});
		}
		else
		{
			$(".recharge-comm-loader").html('<font color="red">Member Not Valid.</font>');
		}
		
		
	});




	$("#upi-topup-btn").click(function(){
		var siteUrl = $("#siteUrl").val();
		$(".upi_loader").show();
			var str = $("#upi_topup_form").serialize();
			$.ajax({
				type:'POST',                
				url:siteUrl+'master/wallet/upiRequestAuth',
				data:str,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 0){

						$(".upi_loader").hide();
						if(data['is_api_error'] == 0)
						{
							$("#amount_error").html(data['amount_error']);
							$("#vpa_error").html(data['vpa_error']);
							$("#description_error").html(data['description_error']);
						}
						else
						{
							$("#apiErrorResponse").html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['message']+'</div>');
						}
					}
					else
					{
						$(".upi_loader").hide();
						$(".upi_request_loader").show();
						setTimeout( getUpiCallback(data['txnid']), 3000 );
					}
				}
			});
		
		
	});


	function getUpiCallback(txnid = ''){
	    var siteUrl = $("#siteUrl").val();
	    $.ajax({                
	      url:siteUrl+'master/wallet/getUpiCallbackResponse/'+txnid,                        
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          window.location.href = siteUrl+'master/wallet/sendRequest';
	        }
	        else
	        {
	        	setTimeout( getUpiCallback(txnid), 3000 );
	        }
	        
	      }
	    });
	    
	};


	$("#upi-cash-topup-btn").click(function(){
		var siteUrl = $("#siteUrl").val();
		$(".upi_loader").show();
			var str = $("#upi_topup_form").serialize();
			$.ajax({
				type:'POST',                
				url:siteUrl+'master/wallet/upiCashRequestAuth',
				data:str,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 0){

						$(".upi_loader").hide();
						if(data['is_api_error'] == 0)
						{
							$("#amount_error").html(data['amount_error']);
							$("#vpa_error").html(data['vpa_error']);
							$("#description_error").html(data['description_error']);
						}
						else
						{
							$("#apiErrorResponse").html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['message']+'</div>');
						}
					}
					else
					{
						$(".upi_loader").hide();
						$(".upi_request_loader").show();
						setTimeout( getUpiCashCallback(data['txnid']), 3000 );
					}
				}
			});
		
		
	});


	function getUpiCashCallback(txnid = ''){
	    var siteUrl = $("#siteUrl").val();
	    $.ajax({                
	      url:siteUrl+'master/wallet/getUpiCashCallbackResponse/'+txnid,                        
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          window.location.href = siteUrl+'master/wallet/sendCashRequest';
	        }
	        else
	        {
	        	setTimeout( getUpiCashCallback(txnid), 3000 );
	        }
	        
	      }
	    });
	    
	};



	$("#qr-topup-btn").click(function(){
		var siteUrl = $("#siteUrl").val();
		$(".upi_loader").show();
			var str = $("#qr_topup_form").serialize();
			$.ajax({
				type:'POST',                
				url:siteUrl+'master/upi/qrGenerateAuth',
				data:str,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 0){

						$(".upi_loader").hide();
						if(data['is_api_error'] == 0)
						{
							$("#amount_error").html(data['amount_error']);
						}
						else
						{
							$("#apiErrorResponse").html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['message']+'</div>');
						}
					}
					else
					{	
						$(".upi_loader").hide();
						$("#qrModal").modal('show');
						$("#qr_div").html(data['qr_code']);
					}
				}
			});
		
		
	});



	$("#bbpsComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		if(memberID != 0)
		{
			$.ajax({                
				url:siteUrl+'master/master/getBBPSCommData/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$(".recharge-comm-loader").html('');
						$("#recharge-comm-block").html(data['str']);
						
					}
					else
					{
						$(".recharge-comm-loader").html('<font color="red">'+data['msg']+'</font>');
					}
				}
			});
		}
		else
		{
			$(".recharge-comm-loader").html('<font color="red">Member Not Valid.</font>');
		}
		
		
	});

	$("#bbpsLiveComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		if(memberID != 0)
		{
			$.ajax({                
				url:siteUrl+'master/master/getBBPSLiveCommData/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$(".recharge-comm-loader").html('');
						$("#recharge-comm-block").html(data['str']);
						
					}
					else
					{
						$(".recharge-comm-loader").html('<font color="red">'+data['msg']+'</font>');
					}
				}
			});
		}
		else
		{
			$(".recharge-comm-loader").html('<font color="red">Member Not Valid.</font>');
		}
		
		
	});

	$("#dmrComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		if(memberID != 0)
		{
			$.ajax({                
				url:siteUrl+'master/master/getMemberDMRCommData/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$(".recharge-comm-loader").html('');
						$("#dmr-comm-block").html(data['str']);
						
					}
					else
					{
						$(".recharge-comm-loader").html('<font color="red">'+data['msg']+'</font>');
					}
				}
			});
		}
		else
		{
			$(".recharge-comm-loader").html('<font color="red">Member Not Valid.</font>');
		}
		
		
	});

	$("#aepsComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'master/master/getMemberAEPSCommData/'+memberID,                        
			success:function(r){
				
				var data = JSON.parse($.trim(r));
				if(data["status"] == 1){
					$(".recharge-comm-loader").html('');
					$("#dmr-comm-block").html(data['str']);
					
				}
				else
				{
					$(".recharge-comm-loader").html('<font color="red">'+data['msg']+'</font>');
				}
			}
		});
		
		
	});

	$("#serviceSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'master/master/getServiceData/'+memberID,                        
			success:function(r){
				
				var data = JSON.parse($.trim(r));
				if(data["status"] == 1){
					$(".recharge-comm-loader").html('');
					$("#recharge-comm-block").html(data['str']);
					
				}
				else
				{
					$(".recharge-comm-loader").html('<font color="red">'+data['msg']+'</font>');
				}
			}
		});
		
		
	});

	$("#selState").change(function(){
		var siteUrl = $("#siteUrl").val();
		var stateID = $(this).val();
		if(stateID){
			$.ajax({                
				url:siteUrl+'master/aeps/getCityList/'+stateID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#selCity").html(data['str']);
						
					}
				}
			});
		}
		
	});


	$("#nsdlStateId").change(function(){
		var siteUrl = $("#siteUrl").val();
		var stateID = $(this).val();
		if(stateID){
			$.ajax({                
				url:siteUrl+'master/pancard/getNsdlDistrictList/'+stateID,                        
				success:function(r){
					
					
					$("#nsdlDistrictId").html(r);
						
					
				}
			});
		}
		
	});
	

	$("#electricityOperator").change(function(){
      var operator_id = $(this).val();
      $("#field-block").css('display','none');
      $("#name-field-block").css('display','none');
      $("#amount-field-block").css('display','none');
      if(operator_id == '')
      {
        $(".ajax-loader").html('<font color="red">Select a operator.</font>')
        $("#fetch_status").val(0);
        $("#fieldName").val('');
        $("#field-block").css('display','none');
        $("#name-field-block").css('display','none');
        $("#amount-field-block").css('display','none');
      }
      else
      {
        var siteUrl = $("#siteUrl").val();
        $(".ajax-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
        $.ajax({               
          type:'POST', 
          url:siteUrl+'master/recharge/fetchBiller/'+operator_id,                        
          success:function(r){
            var data = JSON.parse($.trim(r));
            if(data["status"] == 1){
              $(".ajax-loader").html('');
              $("#fetch_status").val(1);
              $("#fieldName").val(data['fieldName']);
              $("#account_number").attr("placeholder", "Enter "+data['fieldName']);
              $("#customer_name").attr("placeholder", "Enter "+data['fieldOther']);
              $("#field-block").css('display','block');
              $("#name-field-block").css('display','block');
              $("#amount-field-block").css('display','block');
            }
            else
            {
              $(".ajax-loader").html('<font color="red">'+data['msg']+'</font>');
              $("#fetch_status").val(0);
              $("#fieldName").val('');
              $("#field-block").css('display','none');
              $("#name-field-block").css('display','none');
              $("#amount-field-block").css('display','none');
            }
          }
        });
      }
    });
    
    $("#account_number").blur(function(){
      
      var operator_id = $("#electricityOperator").val();
      if(operator_id == '')
      {
        $(".ajax-loader").html('<font color="red">Select a operator.</font>')
        $("#fetch_status").val(0);
        $("#fieldName").val('');
        $("#field-block").css('display','none');
        $("#name-field-block").css('display','none');
        $("#amount-field-block").css('display','none');
      }
      else
      {
        var siteUrl = $("#siteUrl").val();
        $(".ajax-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
        var str = $("#electricity-form").serialize();
        $.ajax({               
          type:'POST', 
          url:siteUrl+'master/recharge/fetchBillerDetail/'+operator_id,                        
          data:str,
          success:function(r){
            var data = JSON.parse($.trim(r));
            if(data["status"] == 1){
              $(".ajax-loader").html('');
              $(".electricity-biller-name").html(data['customername']);
              $("#amount").val(data['amount']);
              $("#reference_id").val(data['reference_id']);
              
            }
            else
            {
              $(".ajax-loader").html('<font color="red">'+data['msg']+'</font>');
              
            }
          }
        });
      }
    });

    $("#payolmobile").blur(function(){
      
      var mobile = $(this).val();
      if(mobile != '')
      {
        	var siteUrl = $("#siteUrl").val();
	        $.ajax({               
	          type:'POST', 
	          url:siteUrl+'master/wallet/getMemberName/'+mobile,                        
	          success:function(r){
	            var data = JSON.parse($.trim(r));
	            $("#memberBlock").html(data['msg']);
	            
	          }
	        });
      }
      
    });
    
    $("#transfer_amount").keyup(function(){
      var transfer_amount = $(this).val();
      if($.isNumeric(transfer_amount))
      {
        var service_tax_percentage = parseFloat($("#service_tax_percentage").val());
        var service_amount = ((service_tax_percentage/100)*parseInt(transfer_amount)).toFixed(2);
        $("#service_tax").val(service_amount);
        $("#wallet_transfer_amount").val((transfer_amount - service_amount).toFixed(2));
      }
      else
      {
        $("#service_tax").val(0);
        $("#wallet_transfer_amount").val(0);
      }
    });

    $("#fund-transfer-amount").keyup(function(){
		var amount = parseFloat($(this).val());
		var balance = parseFloat($("#user-wallet-balance").val());
		var from_1 = parseFloat($("#fund-charge-from-1").val());
		var to_1 = parseFloat($("#fund-charge-to-1").val());
		var flat_1 = parseFloat($("#fund-charge-flat-1").val());

		var from_2 = parseFloat($("#fund-charge-from-2").val());
		var to_2 = parseFloat($("#fund-charge-to-2").val());
		var flat_2 = parseFloat($("#fund-charge-flat-2").val());

		var from_3 = parseFloat($("#fund-charge-from-3").val());
		var to_3 = parseFloat($("#fund-charge-to-3").val());
		var flat_3 = parseFloat($("#fund-charge-flat-3").val());


		if($.isNumeric(amount))
		{
			if(amount >= from_1 && amount <= to_1)
			{
				
				var charge_amount = flat_1;
				var total_amount = parseFloat(charge_amount) + parseFloat(amount);
				$("#total_wallet_deducation").val(total_amount);

			}
			else if(amount >= from_2 && amount <= to_2)
			{
				var charge_amount = flat_2;
				var total_amount = parseFloat(charge_amount) + parseFloat(amount);
				$("#total_wallet_deducation").val(total_amount);

			}
			else if(amount >= from_3 && amount <= to_3)
			{
				var charge_amount = flat_3;
				var total_amount = parseFloat(charge_amount) + parseFloat(amount);
				$("#total_wallet_deducation").val(total_amount);

			}
			else
			{
				$("#total_wallet_deducation").val(amount);
			}
		}
		else
		{
			$("#total_wallet_deducation").val(0);
		}
		
	});

	$("#selMember").change(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $(this).val();
		if(memberID){
			$.ajax({                
				url:siteUrl+'master/wallet/getMemberWalletBalance/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#balance").val(data['balance']);
						
					}
				}
			});
		}
		
	});

	$("#selEwalletMember").change(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $(this).val();
		if(memberID){
			$.ajax({                
				url:siteUrl+'master/ewallet/getMemberWalletBalance/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#balance").val(data['balance']);
						
					}
				}
			});
		}
		
	});

	$("#operator").change(function(){
		var op = $(this).val();
		$('#offerOperator option[value="'+op+'"]').prop('selected', true);
		$('#rofferOperator option[value="'+op+'"]').prop('selected', true);
	});

	$("#viewPlanSearchBtn").click(function(){

		var siteUrl = $("#siteUrl").val();
		$("#offerModal").modal('show');
		$("#offerLoader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='200' /></center>");
		var str = $("#offerFilterForm").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/recharge/getOperatorPlanList',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#offerLoader").html(data['str']);
	          
	        }
	        else
	        {
	          $("#offerLoader").html('<center><font color="red">'+data['msg']+'</font></center>');
	          
	        }
	      }
	    });

	});


	$("#dthViewPlanSearchBtn").click(function(){

		var siteUrl = $("#siteUrl").val();
		$("#offerModal").modal('show');
		$("#offerLoader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='200' /></center>");
		var str = $("#offerFilterForm").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/recharge/getDTHOperatorPlanList',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#offerLoader").html(data['str']);
	          
	        }
	        else
	        {
	          $("#offerLoader").html('<center><font color="red">'+data['msg']+'</font></center>');
	          
	        }
	      }
	    });

	});

	$("#offermobile").blur(function(){

		$("#roffermobile").val($(this).val());

		var siteUrl = $("#siteUrl").val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/recharge/getOperatorType/'+$(this).val(),
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $('#operator option[value="'+data['operator_id']+'"]').prop('selected', true)
	        }
	      }
	    });

	});

	$("#rofferSearchBtn").click(function(){

		var siteUrl = $("#siteUrl").val();
		$("#rofferModal").modal('show');
		$("#rofferLoader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='200' /></center>");
		var str = $("#rofferFilterForm").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/recharge/getRofferList',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#rofferLoader").html(data['str']);
	          
	        }
	        else
	        {
	          $("#rofferLoader").html('<center><font color="red">'+data['msg']+'</font></center>');
	          
	        }
	      }
	    });

	});

	$("#dthRofferSearchBtn").click(function(){

		var siteUrl = $("#siteUrl").val();
		$("#rofferModal").modal('show');
		$("#rofferLoader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='200' /></center>");
		var str = $("#rofferFilterForm").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/recharge/getDthOperatorPlanList',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#rofferLoader").html(data['str']);
	          
	        }
	        else
	        {
	          $("#rofferLoader").html('<center><font color="red">'+data['msg']+'</font></center>');
	          
	        }
	      }
	    });

	});

	$("#cardNumber").blur(function(){

		var siteUrl = $("#siteUrl").val();
		var cardNumber = $("#cardNumber").val();
		var operator = $("#operator").val();
		if(cardNumber != '' && operator != '')
		{
			
			$("#customerInfoLoader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
			var str = $("#admin_profile").serialize();
		    $.ajax({               
		      type:'POST', 
		      url:siteUrl+'master/recharge/getDTHCustomerInfo',
		      data:str,
		      success:function(r){
		        var data = JSON.parse($.trim(r));
		        if(data["status"] == 1){
		          $("#customerInfoLoader").html('');
		          $("#customerName").val(data['customerName']);
		          $("#amount").val(data['monthlyRechargeAmount']);
		          $("#balanceInfo").html('Available Balance - &#8377; '+data['balance']);
		        }
		        else
		        {
		          $("#customerInfoLoader").html('<font color="red">'+data['msg']+'</font>');
		          
		        }
		      }
		    });
		}

	});

	$("#bbps-mobile-prepaid-btn").click(function(){
		$(this).prop('disabled', true);
		var siteUrl = $("#siteUrl").val();
		$("#mobile-prepaid-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var str = $("#bbps-mobile-prepaid-form").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/mobilePrepaidAuth',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#bbps-mobile-prepaid-btn").prop('disabled', false);
	          $("#mobile-prepaid-loader").html(data['msg']);
	          document.getElementById("bbps-mobile-prepaid-form").reset();
	          
	        }
	        else
	        {
	        	$("#bbps-mobile-prepaid-btn").prop('disabled', false);
	          	$("#mobile-prepaid-loader").html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['msg']+'</div>');
	        }
	      }
	    });

	});

	$("#mobilePostpaidOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+3,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	        if(data['is_fetch'] == 1){
	          $("#mobile-postpaid-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#mobile-postpaid-fetch-block").css('display','none');
	        }
	      }
	    });
	});

	$("#bbps-mobile-postpaid-btn").click(function(){
		$(this).prop('disabled', true);
		var siteUrl = $("#siteUrl").val();
		$("#mobile-postpaid-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var str = $("#bbps-mobile-postpaid-form").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/mobilePostpaidAuth',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#bbps-mobile-postpaid-btn").prop('disabled', false);
	          $("#mobile-postpaid-loader").html(data['msg']);
	          document.getElementById("bbps-mobile-postpaid-form").reset();
	          
	        }
	        else
	        {
	        	$("#bbps-mobile-postpaid-btn").prop('disabled', false);
	          	$("#mobile-postpaid-loader").html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['msg']+'</div>');
	        }
	      }
	    });

	});


	$("#bbpsElectricityOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#electricity-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+4,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#electricity-loader").html('');
	        $("#electricity-form-block").html(data['str']);
	        $("#electricity-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#electricity-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#electricity-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});


	$("#bbps-electricity-btn").click(function(){
		$(this).prop('disabled', true);
		var siteUrl = $("#siteUrl").val();
		$("#electricity-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var str = $("#bbps-electricity-form").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/electricityAuth',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#bbps-electricity-btn").prop('disabled', false);
	          $("#electricity-loader").html(data['msg']);
	          document.getElementById("bbps-electricity-form").reset();
	          
	        }
	        else
	        {
	        	$("#bbps-electricity-btn").prop('disabled', false);
	          	$("#electricity-loader").html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['msg']+'</div>');
	        }
	      }
	    });

	});


	$("#bbpsDTHOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#dth-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+1,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#dth-loader").html('');
	        $("#dth-form-block").html(data['str']);
	        $("#dth-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#dth-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#dth-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbps-dth-btn").click(function(){
		$(this).prop('disabled', true);
		var siteUrl = $("#siteUrl").val();
		$("#dth-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var str = $("#bbps-dth-form").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/dthAuth',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#bbps-dth-btn").prop('disabled', false);
	          $("#dth-loader").html(data['msg']);
	          document.getElementById("bbps-dth-form").reset();
	          
	        }
	        else
	        {
	        	$("#bbps-dth-btn").prop('disabled', false);
	          	$("#dth-loader").html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['msg']+'</div>');
	        }
	      }
	    });

	});

	$("#bbpsBroadbandPostpaidOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#boradband-postpaid-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+19,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#boradband-postpaid-loader").html('');
	        $("#boradband-postpaid-form-block").html(data['str']);
	        $("#boradband-postpaid-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#boradband-postpaid-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#boradband-postpaid-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsLandlinePostpaidOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#landline-postpaid-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+2,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#landline-postpaid-loader").html('');
	        $("#landline-postpaid-form-block").html(data['str']);
	        $("#landline-postpaid-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#landline-postpaid-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#landline-postpaid-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsWaterOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#water-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+7,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#water-loader").html('');
	        $("#water-form-block").html(data['str']);
	        $("#water-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#water-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#water-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsGasOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#gas-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+6,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#gas-loader").html('');
	        $("#gas-form-block").html(data['str']);
	        $("#gas-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#gas-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#gas-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});


	$("#bbpsLPGGasOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#lpg-gas-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+11,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#lpg-gas-loader").html('');
	        $("#lpg-gas-form-block").html(data['str']);
	        $("#lpg-gas-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#lpg-gas-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#lpg-gas-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsLoanOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#loan-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+17,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#loan-loader").html('');
	        $("#loan-form-block").html(data['str']);
	        $("#loan-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#loan-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#loan-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsInsuranceOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#insurance-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+5,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#insurance-loader").html('');
	        $("#insurance-form-block").html(data['str']);
	        $("#insurance-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#insurance-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#insurance-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});


	$("#bbpsEmiPaymentOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#emi-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+10,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#emi-loader").html('');
	        $("#emi-form-block").html(data['str']);
	        $("#emi-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#emi-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#emi-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsFastagOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#fastag-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+12,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#fastag-loader").html('');
	        $("#fastag-form-block").html(data['str']);
	        $("#fastag-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#fastag-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#fastag-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsCableOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#cable-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+9,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#cable-loader").html('');
	        $("#cable-form-block").html(data['str']);
	        $("#cable-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#cable-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#cable-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsHousingSocietyOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#housing-society-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+17,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#housing-society-loader").html('');
	        $("#housing-society-form-block").html(data['str']);
	        $("#housing-society-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#housing-society-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#housing-society-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsMunicipalTaxesOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#municipal-taxes-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+18,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#municipal-taxes-loader").html('');
	        $("#municipal-taxes-form-block").html(data['str']);
	        $("#municipal-taxes-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#municipal-taxes-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#municipal-taxes-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsMunicipalServicesOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#municipal-services-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+13,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#municipal-services-loader").html('');
	        $("#municipal-services-form-block").html(data['str']);
	        $("#municipal-services-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#municipal-services-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#municipal-services-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsSubscriptionOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#subscription-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+20,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#subscription-loader").html('');
	        $("#subscription-form-block").html(data['str']);
	        $("#subscription-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#subscription-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#subscription-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsHospitalOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#hospital-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+19,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#hospital-loader").html('');
	        $("#hospital-form-block").html(data['str']);
	        $("#hospital-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#hospital-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#hospital-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});


	$("#bbpsCreditCardOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#credit-card-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+22,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#credit-card-loader").html('');
	        $("#credit-card-form-block").html(data['str']);
	        $("#credit-card-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#credit-card-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#credit-card-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsEntertainmentOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#entertainment-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+9,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#entertainment-loader").html('');
	        $("#entertainment-form-block").html(data['str']);
	        $("#entertainment-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#entertainment-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#entertainment-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsTravelOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#travel-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+21,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#travel-loader").html('');
	        $("#travel-form-block").html(data['str']);
	        $("#travel-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#travel-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#travel-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#bbpsClubOperator").change(function(){
		var siteUrl = $("#siteUrl").val();
		$("#club-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/checkOperatorFetchOption/'+billerID+'/'+24,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	      	$("#club-loader").html('');
	        $("#club-form-block").html(data['str']);
	        $("#club-submit-btn").css('display','block');
	        if(data['is_fetch'] == 1){
	          $("#club-fetch-block").css('display','block');
	        }
	        else
	        {
	        	$("#club-fetch-block").css('display','none');
	        }
	        
	      }
	    });
	});

	$("#selDmtBenId").change(function(){
		var siteUrl = $("#siteUrl").val();
		var stateID = $(this).val();
		if(stateID){
			$.ajax({                
				url:siteUrl+'master/dmt/getBenData/'+stateID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#ben_account_no").val(data['account_no']);
						$("#ben_ifsc").val(data['ifsc']);
						
					}
					else
					{
						$("#ben_account_no").val('');
						$("#ben_ifsc").val('');
					}
				}
			});
		}
		
	});
    

	
});


function fetchMobilePostpaidBill()
{
	var mobile = $("#mobile-postpaid-number").val();
	if(mobile == '')
	{
		$("#mobile-postpaid-number").focus();
		return false;
	}
	else
	{
		var siteUrl = $("#siteUrl").val();
		$("#mobile-postpaid-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var str = $("#bbps-mobile-postpaid-form").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/bbps/fetchMobilePostpaidBill',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#mobile-postpaid-amount").val(data['amount']);
	          $("#mobile-postpaid-loader").html('');
	          
	        }
	        else
	        {
	        	$("#mobile-postpaid-amount").val(data['amount']);
	        	$("#mobile-postpaid-loader").html('');
	        }
	      }
	    });
	}
}

function fetchElectricityBill()
{
	
	var siteUrl = $("#siteUrl").val();
	$("#electricity-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
	var str = $("#bbps-electricity-form").serialize();
    $.ajax({               
      type:'POST', 
      url:siteUrl+'master/bbps/fetchElectricityBill',
      data:str,
      success:function(r){
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          $("#electricity-amount").val(data['amount']);
          $("#electricity-loader").html('');
          if(data["accountHolderName"] != ''){
          	$("#electricity-account-holder-name").html('<b>Account Holder Name - '+data['accountHolderName']+'</b>');
          }
          else
          {
          	$("#electricity-account-holder-name").html('');
          }
          
        }
        else
        {
        	$("#electricity-amount").val(data['amount']);
        	$("#electricity-loader").html('');
        	$("#electricity-account-holder-name").html('');
        }
      }
    });
	
}

function fetchDTHBill()
{
	
	var siteUrl = $("#siteUrl").val();
	$("#dth-loader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
	var str = $("#bbps-dth-form").serialize();
    $.ajax({               
      type:'POST', 
      url:siteUrl+'master/bbps/fetchDTHBill',
      data:str,
      success:function(r){
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          $("#dth-amount").val(data['amount']);
          $("#dth-loader").html('');
          if(data["accountHolderName"] != ''){
          	$("#dth-account-holder-name").html('<b>Account Holder Name - '+data['accountHolderName']+'</b>');
          }
          else
          {
          	$("#dth-account-holder-name").html('');
          }
          
        }
        else
        {
        	$("#dth-amount").val(data['amount']);
        	$("#dth-loader").html('');
        	$("#dth-account-holder-name").html('');
        }
      }
    });
	
}

function fetchMasterBill(service_id)
{
	var loaderID = '';
	var formID = '';
	var amountID = '';
	var accountHolderName = '';
	if(service_id == 19)
	{
		var loaderID = 'boradband-postpaid-loader';
		var formID = 'bbps-boradband-postpaid-form';
		var amountID = 'boradband-postpaid-amount';
		var accountHolderName = 'boradband-postpaid-account-holder-name';
	}
	else if(service_id == 2)
	{
		var loaderID = 'landline-postpaid-loader';
		var formID = 'bbps-landline-postpaid-form';
		var amountID = 'landline-postpaid-amount';
		var accountHolderName = 'landline-postpaid-account-holder-name';
	}
	else if(service_id == 7)
	{
		var loaderID = 'water-loader';
		var formID = 'bbps-water-form';
		var amountID = 'water-amount';
		var accountHolderName = 'water-account-holder-name';
	}
	else if(service_id == 10)
	{
		var loaderID = 'emi-loader';
		var formID = 'bbps-emi-payment-form';
		var amountID = 'emi-amount';
		var accountHolderName = 'emi-account-holder-name';
	}
	else if(service_id == 6)
	{
		var loaderID = 'gas-loader';
		var formID = 'bbps-gas-form';
		var amountID = 'gas-amount';
		var accountHolderName = 'gas-account-holder-name';
	}
	else if(service_id == 11)
	{
		var loaderID = 'lpg-gas-loader';
		var formID = 'bbps-lpg-gas-form';
		var amountID = 'lpg-gas-amount';
		var accountHolderName = 'lpg-gas-account-holder-name';
	}
	else if(service_id == 17)
	{
		var loaderID = 'loan-loader';
		var formID = 'bbps-loan-form';
		var amountID = 'loan-amount';
		var accountHolderName = 'loan-account-holder-name';
	}
	else if(service_id == 5)
	{
		var loaderID = 'insurance-loader';
		var formID = 'bbps-insurance-form';
		var amountID = 'insurance-amount';
		var accountHolderName = 'insurance-account-holder-name';
	}
	else if(service_id == 12)
	{
		var loaderID = 'fastag-loader';
		var formID = 'bbps-fastag-form';
		var amountID = 'fastag-amount';
		var accountHolderName = 'fastag-account-holder-name';
	}
	else if(service_id == 9)
	{
		var loaderID = 'cable-loader';
		var formID = 'bbps-cable-form';
		var amountID = 'cable-amount';
		var accountHolderName = 'cable-account-holder-name';
	}
	// else if(service_id == 17)
	// {
	// 	var loaderID = 'housing-society-loader';
	// 	var formID = 'bbps-housing-society-form';
	// 	var amountID = 'housing-society-amount';
	// 	var accountHolderName = 'housing-society-account-holder-name';
	// }
	else if(service_id == 18)
	{
		var loaderID = 'municipal-taxes-loader';
		var formID = 'bbps-municipal-taxes-form';
		var amountID = 'municipal-taxes-amount';
		var accountHolderName = 'municipal-taxes-account-holder-name';
	}
	else if(service_id == 13)
	{
		var loaderID = 'municipal-services-loader';
		var formID = 'bbps-municipal-services-form';
		var amountID = 'municipal-services-amount';
		var accountHolderName = 'municipal-services-account-holder-name';
	}
	// else if(service_id == 20)
	// {
	// 	var loaderID = 'subscription-loader';
	// 	var formID = 'bbps-subscription-form';
	// 	var amountID = 'subscription-amount';
	// 	var accountHolderName = 'subscription-account-holder-name';
	// }
	// else if(service_id == 19)
	// {
	// 	var loaderID = 'hospital-loader';
	// 	var formID = 'bbps-hospital-form';
	// 	var amountID = 'hospital-amount';
	// 	var accountHolderName = 'hospital-account-holder-name';
	// }
	// else if(service_id == 22)
	// {
	// 	var loaderID = 'credit-card-loader';
	// 	var formID = 'bbps-credit-card-form';
	// 	var amountID = 'credit-card-amount';
	// 	var accountHolderName = 'credit-card-account-holder-name';
	// }
	// else if(service_id == 9)
	// {
	// 	var loaderID = 'entertainment-loader';
	// 	var formID = 'bbps-entertainment-form';
	// 	var amountID = 'entertainment-amount';
	// 	var accountHolderName = 'entertainment-account-holder-name';
	// }
	// else if(service_id == 21)
	// {
	// 	var loaderID = 'travel-loader';
	// 	var formID = 'bbps-travel-form';
	// 	var amountID = 'travel-amount';
	// 	var accountHolderName = 'travel-account-holder-name';
	// }
	// else if(service_id == 24)
	// {
	// 	var loaderID = 'club-loader';
	// 	var formID = 'bbps-club-form';
	// 	var amountID = 'club-amount';
	// 	var accountHolderName = 'club-account-holder-name';
	// }
	var siteUrl = $("#siteUrl").val();
	$("#"+loaderID).html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
	var str = $("#"+formID).serialize();
    $.ajax({               
      type:'POST', 
      url:siteUrl+'master/bbps/fetchMasterBill/'+service_id,
      data:str,
      success:function(r){
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          $("#"+amountID).val(data['amount']);
          $("#"+loaderID).html('');
          if(data["accountHolderName"] != ''){
          	$("#"+accountHolderName).html('<b>Account Holder Name - '+data['accountHolderName']+'</b>');
          }
          else
          {
          	$("#"+accountHolderName).html('');
          }
          
        }
        else
        {
        	$("#"+amountID).val(data['amount']);
        	$("#"+loaderID).html('');
        	$("#"+accountHolderName).html('');
        }
      }
    });
	
}

function payMasterBill(service_id)
{
	var btnID = '';
	var loaderID = '';
	var formID = '';
	if(service_id == 19)
	{
		var loaderID = 'boradband-postpaid-loader';
		var formID = 'bbps-boradband-postpaid-form';
		var btnID = 'bbps-boradband-postpaid-btn';
	}
	else if(service_id == 2)
	{
		var loaderID = 'landline-postpaid-loader';
		var formID = 'bbps-landline-postpaid-form';
		var btnID = 'bbps-landline-postpaid-btn';
	}
	else if(service_id == 7)
	{
		var loaderID = 'water-loader';
		var formID = 'bbps-water-form';
		var btnID = 'bbps-water-btn';
	}
	else if(service_id == 10)
	{
		var loaderID = 'emi-loader';
		var formID = 'bbps-emi-payment-form';
		var btnID = 'emi-payment-btn';
	}

	else if(service_id == 6)
	{
		var loaderID = 'gas-loader';
		var formID = 'bbps-gas-form';
		var btnID = 'bbps-gas-btn';
	}
	else if(service_id == 11)
	{
		var loaderID = 'lpg-gas-loader';
		var formID = 'bbps-lpg-gas-form';
		var btnID = 'bbps-lpg-gas-btn';
	}
	else if(service_id == 17)
	{
		var loaderID = 'loan-loader';
		var formID = 'bbps-loan-form';
		var btnID = 'bbps-loan-btn';
	}
	else if(service_id == 5)
	{
		var loaderID = 'insurance-loader';
		var formID = 'bbps-insurance-form';
		var btnID = 'bbps-insurance-btn';
	}
	else if(service_id == 12)
	{
		var loaderID = 'fastag-loader';
		var formID = 'bbps-fastag-form';
		var btnID = 'bbps-fastag-btn';
	}
	else if(service_id == 9)
	{
		var loaderID = 'cable-loader';
		var formID = 'bbps-cable-form';
		var btnID = 'bbps-cable-btn';
	}
	// else if(service_id == 17)
	// {
	// 	var loaderID = 'housing-society-loader';
	// 	var formID = 'bbps-housing-society-form';
	// 	var btnID = 'bbps-housing-society-btn';
	// }
	else if(service_id == 18)
	{
		var loaderID = 'municipal-taxes-loader';
		var formID = 'bbps-municipal-taxes-form';
		var btnID = 'bbps-municipal-taxes-btn';
	}
	else if(service_id == 13)
	{
		var loaderID = 'municipal-services-loader';
		var formID = 'bbps-municipal-services-form';
		var btnID = 'bbps-municipal-services-btn';
	}
	// else if(service_id == 20)
	// {
	// 	var loaderID = 'subscription-loader';
	// 	var formID = 'bbps-subscription-form';
	// 	var btnID = 'bbps-subscription-btn';
	// }
	// else if(service_id == 19)
	// {
	// 	var loaderID = 'hospital-loader';
	// 	var formID = 'bbps-hospital-form';
	// 	var btnID = 'bbps-hospital-btn';
	// }
	// else if(service_id == 22)
	// {
	// 	var loaderID = 'credit-card-loader';
	// 	var formID = 'bbps-credit-card-form';
	// 	var btnID = 'bbps-credit-card-btn';
	// }
	// else if(service_id == 9)
	// {
	// 	var loaderID = 'entertainment-loader';
	// 	var formID = 'bbps-entertainment-form';
	// 	var btnID = 'bbps-entertainment-btn';
	// }
	// else if(service_id == 21)
	// {
	// 	var loaderID = 'travel-loader';
	// 	var formID = 'bbps-travel-form';
	// 	var btnID = 'bbps-travel-btn';
	// }
	// else if(service_id == 24)
	// {
	// 	var loaderID = 'club-loader';
	// 	var formID = 'bbps-club-form';
	// 	var btnID = 'bbps-club-btn';
	// }


	$("#"+btnID).prop('disabled', true);
	var siteUrl = $("#siteUrl").val();
	$("#"+loaderID).html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
	var str = $("#"+formID).serialize();
    $.ajax({               
      type:'POST', 
      url:siteUrl+'master/bbps/payMasterBillAuth/'+service_id,
      data:str,
      success:function(r){
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          $("#"+btnID).prop('disabled', false);
          $("#"+loaderID).html(data['msg']);
          document.getElementById(formID).reset();
          
        }
        else
        {
        	$("#"+btnID).prop('disabled', false);
          	$("#"+loaderID).html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['msg']+'</div>');
        }
      }
    });
}

function updatedmrModel(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'master/master/getDMRCommData/'+id,                        
		success:function(r){
			
			var data = JSON.parse($.trim(r));
			if(data["status"] == 1){
				$("#recordID").val(id);
				$("#updateDMRModel").modal('show');
				$("#updateDMRBlock").html(data['str']);
				
			}
			else
			{
				$("#updateDMRBlock").html('<font color="red">'+data['msg']+'</font>');
			}
		}
	});
	
}

function updateaepsModel(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'master/master/getAEPSCommData/'+id,                        
		success:function(r){
			
			var data = JSON.parse($.trim(r));
			if(data["status"] == 1){
				$("#recordID").val(id);
				$("#updateDMRModel").modal('show');
				$("#updateDMRBlock").html(data['str']);
				
			}
			else
			{
				$("#updateDMRBlock").html('<font color="red">'+data['msg']+'</font>');
			}
		}
	});
	
}

function showOfferModal()
{
	var siteUrl = $("#siteUrl").val();
	$("#offerModal").modal('show');
	$("#offerLoader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='200' /></center>");
	var str = $("#offerFilterForm").serialize();
    $.ajax({               
      type:'POST', 
      url:siteUrl+'master/recharge/getOperatorPlanList',
      data:str,
      success:function(r){
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          $("#offerLoader").html(data['str']);
          
        }
        else
        {
          $("#offerLoader").html('<center><font color="red">'+data['msg']+'</font></center>');
          
        }
      }
    });
}

function showDTHOfferModal()
{

	var siteUrl = $("#siteUrl").val();
	var cardNumber = $("#cardNumber").val();
	var operator = $("#operator").val();
	if(cardNumber == '' || operator == '')
	{
		$("#customerInfoLoader").html('<font color="red">Please Select Operator and Card Number.</font>');
	}
	else
	{
		$("#customerInfoLoader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
		var str = $("#admin_profile").serialize();
	    $.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/recharge/getDTHOperatorPlanList',
	      data:str,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){
	          $("#customerInfoLoader").html('');
	          $("#customerName").val(data['customerName']);
	          $("#amount").val(data['monthlyRechargeAmount']);
	          $("#balanceInfo").html('Available Balance - &#8377; '+data['balance']);
	        }
	        else
	        {
	          $("#customerInfoLoader").html('<font color="red">'+data['msg']+'</font>');
	          
	        }
	      }
	    });
	}
}

function offerAmountPick(amount)
{
	$("#amount").val(amount);
	$("#offerModal").modal('hide');
}

function showROfferModal()
{
	var siteUrl = $("#siteUrl").val();
	$("#rofferModal").modal('show');
	$("#rofferLoader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='200' /></center>");
	var str = $("#rofferFilterForm").serialize();
    $.ajax({               
      type:'POST', 
      url:siteUrl+'master/recharge/getRofferList',
      data:str,
      success:function(r){
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          $("#rofferLoader").html(data['str']);
          
        }
        else
        {
          $("#rofferLoader").html('<center><font color="red">'+data['msg']+'</font></center>');
          
        }
      }
    });
}

function showDTHROfferModal()
{
	var siteUrl = $("#siteUrl").val();
	$("#rofferModal").modal('show');
	var cardNumber = $("#cardNumber").val();
	$("#roffermobile").val(cardNumber);
	$("#rofferLoader").html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='200' /></center>");
	var str = $("#rofferFilterForm").serialize();
    $.ajax({               
      type:'POST', 
      url:siteUrl+'master/recharge/getDthOperatorPlanList',
      data:str,
      success:function(r){
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          $("#rofferLoader").html(data['str']);
          
        }
        else
        {
          $("#rofferLoader").html('<center><font color="red">'+data['msg']+'</font></center>');
          
        }
      }
    });
}

function offerAmountPick(amount)
{
	$("#amount").val(amount);
	$("#offerModal").modal('hide');
	$("#DthOfferModal").modal('hide');
	$("#rofferModal").modal('hide');
}
function showComplainBox(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'master/recharge/getRechargeData/'+id,                        
		success:function(r){
			
			var data = JSON.parse($.trim(r));
			if(data["status"] == 1){
				$("#recordID").val(id);
				$("#updateComplainModel").modal('show');
				$("#complainRchgID").html('<p><b>Recharge ID - '+data['txnid']+'</b></p>');
				$("#complainAmount").html('<p><b>Amount - '+data['amount']+'</b></p>');
				$("#complainMsgBlock").html('');
				
			}
			else
			{
				$("#complainMsgBlock").html('<font color="red">'+data['msg']+'</font>');
			}
		}
	});
	
}


function showBBPSComplainBox(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'master/recharge/getBBPSData/'+id,                        
		success:function(r){
			
			var data = JSON.parse($.trim(r));
			if(data["status"] == 1){
				$("#recordID").val(id);
				$("#updateComplainModel").modal('show');
				$("#complainRchgID").html('<p><b>Recharge ID - '+data['txnid']+'</b></p>');
				$("#complainAmount").html('<p><b>Amount - '+data['amount']+'</b></p>');
				$("#complainMsgBlock").html('');
				
			}
			else
			{
				$("#complainMsgBlock").html('<font color="red">'+data['msg']+'</font>');
			}
		}
	});
	
}

function updateBenModel(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'master/transfer/getBenData/'+id,                        
		success:function(r){
			
			var data = JSON.parse($.trim(r));
			if(data["status"] == 1){
				$("#recordID").val(id);
				$("#updateDMRModel").modal('show');
				$("#updateDMRBlock").html(data['str']);
				
			}
			else
			{
				$("#updateDMRBlock").html('<font color="red">'+data['msg']+'</font>');
			}
		}
	});
	
}




function showAepsModal(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'master/aeps/getAepsData/'+id,                        
		success:function(r){
			
			var data = JSON.parse($.trim(r));
			if(data["status"] == 1){
				$("#updateDMRModel").modal('show');
				$("#updateDMRBlock").html(data['str']);
				
			}
			else
			{
				$("#updateDMRBlock").html('<font color="red">'+data['msg']+'</font>');
			}
		}
	});
	
}

function dmtVerifyIfsc()
{
	var siteUrl = $("#siteUrl").val();
	var ifsc = $("#ifsc").val();
	if(ifsc == '')
	{
		$(".ifsc-vefify-loader").html('<font color="red">Please enter IFSC.</font>');
	}
	else
	{
		$(".ifsc-vefify-loader").html("<img src='"+siteUrl+"skin/admin/images/small-loading.gif' />");
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/dmt/verifyIfscCode/'+ifsc,
	      success:function(r){
	        var data = JSON.parse($.trim(r));
	        if(data["status"] == 1){

	         $(".ifsc-vefify-loader").html('<table class="table table-bordered"><tr><th colspan="5"><center>'+data['ifscDetails']+'</center></th></tr><tr><th>Bank</th><th>Branch</th><th>City</th><th>District</th><th>State</th></tr><tr><th>'+data['bankName']+'</th><th>'+data['branchName']+'</th><th>'+data['city']+'</th><th>'+data['district']+'</th><th>'+data['state']+'</th></tr><tr><th colspan="5">'+data['address']+'</th></tr></table>'); 
	          
	        }
	        else
	        {
	          $(".ifsc-vefify-loader").html('<font color="red">'+data['message']+'</font>');
	          
	        }
	      }
	    });
	}
}


$("#accountVerifyBtn").click(function(){
      
      var siteUrl = $("#siteUrl").val();
      $(".ajaxx-loader").html("<center><img src='"+siteUrl+"skin/images/large-loading.gif' alt='loading' width='100' /></center>");
      var str = $("#account_verify_form").serialize();
        $.ajax({               
          type:'POST', 
          url:siteUrl+'master/bank/verifyAuth',                        
          data:str,
          success:function(r){
            var data = JSON.parse($.trim(r));
            if(data["status"] == 1){
              $(".ajaxx-loader").html('');
              $("#account_holder_name").val(data['account_holder_name']);
              $("#bankModal").modal('show');
              $("#bankResponse").html(data['msg']);
            }
            else
            {
              $(".ajaxx-loader").html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['msg']+'</div>');
              
            }
          }
        });
    });



$("#upiVerifyBtn").click(function(){
      
      var siteUrl = $("#siteUrl").val();
      $(".ajaxx-loader").html("<center><img src='"+siteUrl+"skin/images/large-loading.gif' alt='loading' width='100' /></center>");
      var str = $("#upi_verify_form").serialize();
        $.ajax({               
          type:'POST', 
          url:siteUrl+'master/bank/upiVerifyAuth',                        
          data:str,
          success:function(r){
            var data = JSON.parse($.trim(r));
            if(data["status"] == 1){
              $(".ajaxx-loader").html('');
              $("#account_holder_name").val(data['account_holder_name']);
              $("#bankModal").modal('show');
              $("#bankResponse").html(data['msg']);
            }
            else
            {
              $(".ajaxx-loader").html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['msg']+'</div>');
              
            }
          }
        });
    });
    
    
    $("#selDmtBankID").change(function(){
		var siteUrl = $("#siteUrl").val();
		var billerID = $(this).val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/dmt/getBankDefaultIfsc/'+billerID,
	      success:function(r){
	      	var data = JSON.parse($.trim(r));
	        $("#defaultIfscTxt").val(data['ifsc']);
	        
	      }
	    });
	});

	$("#is_default_ifsc").click(function(){

		if($('#is_default_ifsc').is(':checked')) { 
			$("#ifsc").val($("#defaultIfscTxt").val());
		}
		else
		{
			$("#ifsc").val('');
		}

	});
	
	function closeClubNoti(recordID = 0)
	{
		var siteUrl = $("#siteUrl").val();
		$.ajax({               
	      type:'POST', 
	      url:siteUrl+'master/saving/closeClubNotification/'+recordID,
	      success:function(r){
	      	
	      }
	    });
	}

	$("#to_bank").click(function(){
		var siteUrl = $("#siteUrl").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'master/transfer/getBankBeneficiary',                        
			success:function(r){
				
				var data = JSON.parse($.trim(r));
				if(data["status"] == 1){
					$(".recharge-comm-loader").html('');
					$("#recharge-comm-block").html(data['str']);
					
				}
				else
				{
					$(".recharge-comm-loader").html('<font color="red">'+data['str']+'</font>');
				}
			}
		});
		
		
	});



	$("#to_upi").click(function(){
		var siteUrl = $("#siteUrl").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'master/transfer/getUpiBankBeneficiary',                        
			success:function(r){
				
				var data = JSON.parse($.trim(r));
				if(data["status"] == 1){
					$(".recharge-comm-loader").html('');
					$("#recharge-comm-block").html(data['str']);
					
				}
				else
				{
					$(".recharge-comm-loader").html('<font color="red">'+data['str']+'</font>');
				}
			}
		});
		
		
	});


		$("#add_bank_account").click(function(){
	 	
	 	$("#show_bank_account").css('display','block');
	 	$("#show_upi_account").css('display','none');
	 	
	});

	$("#add_upi_account").click(function(){
	 	
	 	$("#show_upi_account").css('display','block');
	 	$("#show_bank_account").css('display','none');
	 	
	});

	$("#coupon").keyup(function(){
  	var coupon = $("#coupon").val();

  	if(coupon){
			$.ajax({                
				url:siteUrl+'master/pancard/getCouponBalance/'+coupon,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#amount").html(data['amount']);
						
					}
					else
					{
						$("#amount").html(data['amount']);
					}
				}
			});
		}
    	});


		function payCreditCardBill(service_id)
		{
				var loaderID = 'mobi-credit-card-loader';
				var formID = 'bbps-credit-card-mobi-form';
				var btnID = 'bbps-credit-card-mobi-btn';


			$("#"+btnID).prop('disabled', true);
			var siteUrl = $("#siteUrl").val();
			$("#"+loaderID).html("<center><img src='"+siteUrl+"skin/admin/images/large-loading.gif' width='100' /></center>");
			var str = $("#"+formID).serialize();
		    $.ajax({               
		      type:'POST', 
		      url:siteUrl+'master/bbps/payCreditCardBillAuth/'+service_id,
		      data:str,
		      success:function(r){
		        var data = JSON.parse($.trim(r));
		        if(data["status"] == 1){
		          $("#"+btnID).prop('disabled', false);
		          $("#"+loaderID).html(data['msg']);
		          document.getElementById(formID).reset();
		          
		        }
		        else
		        {
		        	$("#"+btnID).prop('disabled', false);
		          	$("#"+loaderID).html('<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+data['msg']+'</div>');
		        }
		      }
		    });
		}


		function bbpsRecharge()
{
	var siteUrl = $("#siteUrl").val();
	var str = $("#bbps_recharge").serialize();
    $.ajax({               
      type:'POST', 
      url:siteUrl+'master/bbps/mobilePrepaidAuth',
      data:str,
      success:function(r){
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){

         
                 swal({
              title: data['msg'],           
              icon: "success",
              button: "OK!",
            });
            
             setTimeout(function() {
                              window.location.replace(siteUrl+'master/bbps');
                            }, 10000);
        }
        else
        {
        	 swal({
              title: data['msg'],           
              icon: "success",
              button: "OK!",
            });
            
             setTimeout(function() {
                              window.location.replace(siteUrl+'master/bbps');
                            }, 10000);
        	
        }
      }
    });
}

	

