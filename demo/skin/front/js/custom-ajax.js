$(document).ready(function() {	

	
	
	$("#register-referral-id").blur(function(){
		var siteUrl = $("#siteUrl").val();
		var referral_id = $(this).val();
		
		if(referral_id)
		{
			$.ajax({
				 type: 'POST',
				 url: siteUrl+"register/getReferralData/"+referral_id,
				 success:function(Path)
				 {
					var data = JSON.parse(Path);
					if(data['status'] == 1)
					{
						$("#register-referral_id-error").html('');
						$("#referralName").val(data['name']);
					}
					else
					{
						
						$("#register-referral_id-error").html('<font color="red">'+data['msg']+'</font>');
					}
					
				 }
			
			
			
			});
		}
	});

	
});








