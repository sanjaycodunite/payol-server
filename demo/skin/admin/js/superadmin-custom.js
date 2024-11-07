$(document).ready(function() {	
	
	$("#account_check_all").click(function(){
							
		$('input[type="checkbox"]').prop('checked', this.checked);
		
		
	});
	
	$("#changeAPISearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/api/getMemberActiveAPIData/'+memberID,                        
			success:function(r){
				
				var data = JSON.parse($.trim(r));
				if(data["status"] == 1){
					$(".recharge-comm-loader").html('');
					$("#recharge-comm-block").html(data['str']);

					$("#check_all").click(function(){
							
						$('input[type="checkbox"]').prop('checked', this.checked);
						
						
					});
					
				}
				else
				{
					$(".recharge-comm-loader").html('<font color="red">'+data['msg']+'</font>');
				}
			}
		});
		
		
	});

	$("#accountVerifyChargeSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getMemberAccountVerifyChargeData/'+memberID,                        
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

	$("#nsdlPancardChargeSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getMemberNsdlPancardChargeData/'+memberID,                        
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

	$("#bbpsLiveComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getBBPSLiveCommData/'+memberID,                        
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

	$("#selEwalletMember").change(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $(this).val();
		if(memberID){
			$.ajax({                
				url:siteUrl+'superadmin/ewallet/getMemberWalletBalance/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#balance").val(data['balance']);
						
					}
				}
			});
		}
		
	});

	$("#selWalletMember").change(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $(this).val();
		if(memberID){
			$.ajax({                
				url:siteUrl+'superadmin/wallet/getMemberWalletBalance/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#balance").val(data['balance']);
						
					}
				}
			});
		}
		
	});
	$("#selVirtualWalletMember").change(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $(this).val();
		if(memberID){
			$.ajax({                
				url:siteUrl+'superadmin/vanwallet/getMemberWalletBalance/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#balance").val(data['balance']);
						
					}
				}
			});
		}
		
	});
	$("#selCwalletMember").change(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $(this).val();
		if(memberID){
			$.ajax({                
				url:siteUrl+'superadmin/cwallet/getMemberWalletBalance/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#balance").val(data['balance']);
						
					}
				}
			});
		}
		
	});

	
	$("#rechargeComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getRechargeCommData/'+memberID,                        
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

	$("#bbpsComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getBBPSCommData/'+memberID,                        
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

	$("#bbpsOptSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getBBPSOperatorData/'+memberID,                        
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

	$("#dmrComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getMemberDMRCommData/'+memberID,                        
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


	$("#moneyTransferComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getMemberMoneyTransferCommData/'+memberID,                        
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

	$("#dmtChargeSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getMemberDmtChargeData/'+memberID,                        
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


	$("#aepsComSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getMemberAEPSCommData/'+memberID,                        
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

	$("#collectionQrSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getCollectionQrUserList/'+memberID,                        
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
	$("#cashQrSearchBtn").click(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $("#selMemberID").val();
		$(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
		$.ajax({                
			url:siteUrl+'superadmin/master/getCashQrUserList/'+memberID,                        
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
	$("#response_type").change(function(){
		var response_type = $(this).val();
		if(response_type == 1)
		{
			$("#seperator_block").css('display','block');
			$("#str_res_block").css('display','block');
			$("#xml_res_block").css('display','none');
			$("#json_res_block").css('display','none');
		}
		else if(response_type == 2)
		{
			$("#seperator_block").css('display','none');
			$("#str_res_block").css('display','none');
			$("#xml_res_block").css('display','block');
			$("#json_res_block").css('display','none');
		}
		else if(response_type == 3)
		{
			$("#seperator_block").css('display','none');
			$("#str_res_block").css('display','none');
			$("#xml_res_block").css('display','none');
			$("#json_res_block").css('display','block');
		}
		else
		{
			$("#seperator_block").css('display','none');
			$("#str_res_block").css('display','none');
			$("#xml_res_block").css('display','none');
			$("#json_res_block").css('display','none');
		}
	});

	$("#get_balance_response_type").change(function(){
		var response_type = $(this).val();
		if(response_type == 1)
		{
			$("#get_balance_seperator_block").css('display','block');
			$("#get_balance_str_res_block").css('display','block');
			$("#get_balance_xml_res_block").css('display','none');
			$("#get_balance_json_res_block").css('display','none');
		}
		else if(response_type == 2)
		{
			$("#get_balance_seperator_block").css('display','none');
			$("#get_balance_str_res_block").css('display','none');
			$("#get_balance_xml_res_block").css('display','block');
			$("#get_balance_json_res_block").css('display','none');
		}
		else if(response_type == 3)
		{
			$("#get_balance_seperator_block").css('display','none');
			$("#get_balance_str_res_block").css('display','none');
			$("#get_balance_xml_res_block").css('display','none');
			$("#get_balance_json_res_block").css('display','block');
		}
		else
		{
			$("#get_balance_seperator_block").css('display','none');
			$("#get_balance_str_res_block").css('display','none');
			$("#get_balance_xml_res_block").css('display','none');
			$("#get_balance_json_res_block").css('display','none');
		}
	});

	$("#check_status_response_type").change(function(){
		var response_type = $(this).val();
		if(response_type == 1)
		{
			$("#check_status_seperator_block").css('display','block');
			$("#check_status_str_res_block").css('display','block');
			$("#check_status_xml_res_block").css('display','none');
			$("#check_status_json_res_block").css('display','none');
		}
		else if(response_type == 2)
		{
			$("#check_status_seperator_block").css('display','none');
			$("#check_status_str_res_block").css('display','none');
			$("#check_status_xml_res_block").css('display','block');
			$("#check_status_json_res_block").css('display','none');
		}
		else if(response_type == 3)
		{
			$("#check_status_seperator_block").css('display','none');
			$("#check_status_str_res_block").css('display','none');
			$("#check_status_xml_res_block").css('display','none');
			$("#check_status_json_res_block").css('display','block');
		}
		else
		{
			$("#check_status_seperator_block").css('display','none');
			$("#check_status_str_res_block").css('display','none');
			$("#check_status_xml_res_block").css('display','none');
			$("#check_status_json_res_block").css('display','none');
		}
	});


	

	
});
function getBbspWalletBalance()
{
	$("#fetch-txt").html('Fetching....');
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'superadmin/wallet/getBbspWalletBalance',                        
		success:function(r){
			
			var data = JSON.parse($.trim(r));
			if(data["status"] == 1){
				$("#bbps-wallet-balance").html('<b>BBPS-Wallet - &#8377; '+data['balance']+'</b>');
				$("#fetch-txt").html('Fetch Balance');
				
			}
			else
			{
				$("#bbps-wallet-balance").html('<b>BBPS-Wallet - &#8377; 0</b>');
				$("#fetch-txt").html('Fetch Balance');
			}
		}
	});
}
function showStrResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#str_res_status_"+id).css('display','block');
	}
	else
	{
		$("#str_res_status_"+id).css('display','none');
	}
}
function showXMLResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#xml_res_status_"+id).css('display','block');
	}
	else
	{
		$("#xml_res_status_"+id).css('display','none');
	}
}
function showJsonResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#json_res_status_"+id).css('display','block');
	}
	else
	{
		$("#json_res_status_"+id).css('display','none');
	}
}


function showGetBalanceStrResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#get_balance_str_res_status_"+id).css('display','block');
	}
	else
	{
		$("#get_balance_str_res_status_"+id).css('display','none');
	}
}
function showGetBalanceXMLResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#get_balance_xml_res_status_"+id).css('display','block');
	}
	else
	{
		$("#get_balance_xml_res_status_"+id).css('display','none');
	}
}
function showGetBalanceJsonResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#get_balance_json_res_status_"+id).css('display','block');
	}
	else
	{
		$("#get_balance_json_res_status_"+id).css('display','none');
	}
}



function showCheckStatusStrResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#check_status_str_res_status_"+id).css('display','block');
	}
	else
	{
		$("#check_status_str_res_status_"+id).css('display','none');
	}
}
function showCheckStatusXMLResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#check_status_xml_res_status_"+id).css('display','block');
	}
	else
	{
		$("#check_status_xml_res_status_"+id).css('display','none');
	}
}
function showCheckStatusJsonResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#check_status_json_res_status_"+id).css('display','block');
	}
	else
	{
		$("#check_status_json_res_status_"+id).css('display','none');
	}
}


function showCallbackResponseStatus(id,val)
{
	if(val == 2)
	{
		$("#call_back_res_status_"+id).css('display','block');
	}
	else
	{
		$("#call_back_res_status_"+id).css('display','none');
	}
}
function updatedmrModel(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'superadmin/master/getDMRCommData/'+id,                        
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



function updateMoneyTransferModel(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'superadmin/master/getMoneyTransferCommData/'+id,                        
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

function updateDmtChargeModel(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'superadmin/master/getDmtChargeData/'+id,                        
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


function updateAmountFilterModel(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'superadmin/api/getAmountFilterData/'+id,                        
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
		url:siteUrl+'superadmin/master/getAEPSCommData/'+id,                        
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

function successBbps(id)
{
	$("#updateDMRModel").modal('show');
	$("#recordID").val(id);
}

function showAepsModal(id)
{
	var siteUrl = $("#siteUrl").val();
	$.ajax({                
		url:siteUrl+'superadmin/report/getAepsData/'+id,                        
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






