$(document).ready(function() {	
	
	
	$("#rechargeComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		if(memberID != 0)
		{
			$.ajax({                
				url:siteUrl+'portal/master/getRechargeCommData/'+memberID,                        
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

	$("#bbpsComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		if(memberID != 0)
		{
			$.ajax({                
				url:siteUrl+'portal/master/getBBPSCommData/'+memberID,                        
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
				url:siteUrl+'portal/master/getMemberDMRCommData/'+memberID,                        
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
			url:siteUrl+'portal/master/getMemberAEPSCommData/'+memberID,                        
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
			url:siteUrl+'portal/master/getServiceData/'+memberID,                        
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
				url:siteUrl+'portal/aeps/getCityList/'+stateID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#selCity").html(data['str']);
						
					}
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
          url:siteUrl+'portal/recharge/fetchBiller/'+operator_id,                        
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
          url:siteUrl+'portal/recharge/fetchBillerDetail/'+operator_id,                        
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
				url:siteUrl+'portal/wallet/getMemberWalletBalance/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#balance").val(data['balance']);
						
					}
				}
			});
		}
		
	});
    

	
});
function updatedmrModel(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'portal/master/getDMRCommData/'+id,                        
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
		url:siteUrl+'portal/master/getAEPSCommData/'+id,                        
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



function showComplainBox(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'portal/report/getRechargeData/'+id,                        
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

function showAepsModal(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'portal/report/getAepsData/'+id,                        
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





function viewNarration(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'portal/wallet/getNarrationData/'+id,                        
		success:function(r){
			
			var data = JSON.parse($.trim(r));
			if(data["status"] == 1){
				$("#recordID").val(id);
				$("#updateComplainModel").modal('show');
				$("#complainRchgID").html('<p><b>Txn ID - '+data['txnid']+'</b></p>');
				$("#complainAmount").html('<p><b>Narration - '+data['narration']+'</b></p>');
				$("#complainMsgBlock").html('');
				
			}
			else
			{
				$("#recordID").val(id);
				$("#updateComplainModel").modal('show');
				$("#complainMsgBlock").html('<font color="red">'+data['msg']+'</font>');
			}
		}
	});
}
