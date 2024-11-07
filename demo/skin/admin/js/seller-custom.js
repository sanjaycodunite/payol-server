$(document).ready(function() {	
	
	$("#addMoreDropdownBtn").click(function(){
		
		var totalDropdownRecord = parseInt($("#totalDropdownRecord").val()) + 1;
		$( "#dropdown-table" ).append('<tr id="dropdown_tr_'+totalDropdownRecord+'"><td><input type="radio" name="dropdown_is_default" value="'+totalDropdownRecord+'" /></td><td><input type="text" class="form-control" name="dropdown_label['+totalDropdownRecord+']" /></td><td><input type="text" class="form-control" name="dropdown_value['+totalDropdownRecord+']" /></td><td><i class="fa fa-trash" onclick="deleteDropdownRow('+totalDropdownRecord+')" aria-hidden="true"></i></td></tr>');
		$("#totalDropdownRecord").val(totalDropdownRecord);
	});
	
	$("#addMoreVisualSwatchBtn").click(function(){
		
		var totalDropdownRecord = parseInt($("#totalVisualSwatchRecord").val()) + 1;
		$( "#visual-swatch-table" ).append('<tr id="visual_swatch_tr_'+totalDropdownRecord+'"><td><input type="radio" name="dropdown_is_default" value="'+totalDropdownRecord+'" /></td><td><input type="text" class="form-control" name="dropdown_label['+totalDropdownRecord+']" /></td><td><input type="text" class="form-control" name="dropdown_value['+totalDropdownRecord+']" /></td><td><i class="fa fa-trash" onclick="deleteVisualSwatchRow('+totalDropdownRecord+')" aria-hidden="true"></i></td></tr>');
		$("#totalVisualSwatchRecord").val(totalDropdownRecord);
	});
	
	$("#addMoreTextSwatchBtn").click(function(){
		
		var totalDropdownRecord = parseInt($("#totalTextSwatchRecord").val()) + 1;
		$( "#text-swatch-table" ).append('<tr id="text_swatch_tr_'+totalDropdownRecord+'"><td><input type="radio" name="dropdown_is_default" value="'+totalDropdownRecord+'" /></td><td><input type="text" class="form-control" name="dropdown_label['+totalDropdownRecord+']" /></td><td><input type="text" class="form-control" name="dropdown_value['+totalDropdownRecord+']" /></td><td><i class="fa fa-trash" onclick="deleteTextSwatchRow('+totalDropdownRecord+')" aria-hidden="true"></i></td></tr>');
		$("#totalTextSwatchRecord").val(totalDropdownRecord);
	});
	
	$("#addMoreMultiselectBtn").click(function(){
		
		var totalDropdownRecord = parseInt($("#totalMultiselectRecord").val()) + 1;
		$( "#multi-visual-swatch-table" ).append('<tr id="multiselect_tr_'+totalDropdownRecord+'"><td><input type="checkbox" name="dropdown_is_default['+totalDropdownRecord+']" value="'+totalDropdownRecord+'" /></td><td><input type="text" class="form-control" name="dropdown_label['+totalDropdownRecord+']" /></td><td><input type="text" class="form-control" name="dropdown_value['+totalDropdownRecord+']" /></td><td><i class="fa fa-trash" onclick="deleteMultiselectRow('+totalDropdownRecord+')" aria-hidden="true"></i></td></tr>');
		$("#totalMultiselectRecord").val(totalDropdownRecord);
	});
	
	$("#addMoreMultiDropdownBtn").click(function(){
		
		var totalDropdownRecord = parseInt($("#totalMultiDropdownRecord").val()) + 1;
		$( "#multi-dropdown-table" ).append('<tr id="multi_dropdown_tr_'+totalDropdownRecord+'"><td><input type="checkbox" name="dropdown_is_default['+totalDropdownRecord+']" value="'+totalDropdownRecord+'" /></td><td><input type="text" class="form-control" name="dropdown_label['+totalDropdownRecord+']" /></td><td><input type="text" class="form-control" name="dropdown_value['+totalDropdownRecord+']" /></td><td><i class="fa fa-trash" onclick="deleteMultiDropdownRow('+totalDropdownRecord+')" aria-hidden="true"></i></td></tr>');
		$("#totalMultiDropdownRecord").val(totalDropdownRecord);
	});
	
	$("#selMember").change(function(){
		var siteUrl = $("#siteUrl").val();
		var memberID = $(this).val();
		if(memberID){
			$.ajax({                
				url:siteUrl+'seller-panel/wallet/getMemberWalletBalance/'+memberID,                        
				success:function(r){
					
					var data = JSON.parse($.trim(r));
					if(data["status"] == 1){
						$("#balance").val(data['balance']);
						
					}
				}
			});
		}
		
	});
	
	$("#selAttributeFormType").change(function(){
		var formID = $(this).val();
		if(formID == 1)
		{
			$("#dropdown-block").css('display','block');
			$("#visual-swatch-block").css('display','none');
			$("#text-swatch-block").css('display','none');
			$("#multi-dropdown-block").css('display','none');
			$("#multi-visual-swatch-block").css('display','none');
		}
		else if(formID == 2)
		{
			$("#dropdown-block").css('display','none');
			$("#visual-swatch-block").css('display','block');
			$("#text-swatch-block").css('display','none');
			$("#multi-dropdown-block").css('display','none');
			$("#multi-visual-swatch-block").css('display','none');
		}
		else if(formID == 3)
		{
			$("#dropdown-block").css('display','none');
			$("#visual-swatch-block").css('display','none');
			$("#text-swatch-block").css('display','block');
			$("#multi-dropdown-block").css('display','none');
			$("#multi-visual-swatch-block").css('display','none');
		}
		else if(formID == 4)
		{
			$("#dropdown-block").css('display','none');
			$("#visual-swatch-block").css('display','none');
			$("#text-swatch-block").css('display','none');
			$("#multi-visual-swatch-block").css('display','block');
			$("#multi-dropdown-block").css('display','none');
		}
		else if(formID == 5)
		{
			$("#dropdown-block").css('display','none');
			$("#visual-swatch-block").css('display','none');
			$("#text-swatch-block").css('display','none');
			$("#multi-visual-swatch-block").css('display','none');
			$("#multi-dropdown-block").css('display','block');
		}
		else
		{
			$("#dropdown-block").css('display','none');
			$("#visual-swatch-block").css('display','none');
			$("#text-swatch-block").css('display','none');
			$("#multi-visual-swatch-block").css('display','none');
			$("#multi-dropdown-block").css('display','none');
		}
	});
	
	$("#selAttributeSet").change(function(){
		var setID = $(this).val();
		var siteUrl = $("#siteUrl").val();
		
		$("#attribute-set-form-loader").html("<img src='"+siteUrl+"skin/admin/images/small-loading.gif' alt='loading' />");
		
		$.ajax({                
				url:siteUrl+'seller-panel/catalog/getAttributeSetForm/'+setID,                        
				success:function(r){
					var data = JSON.parse(r);
					$("#attribute-set-form-loader").html(data['str']);
					$("#selVariationTheme").html(data['variation_str']);
				    
				}
			});
	});
	
	$('#main-upload-btn').on('click', function() {
		$('#main_images').trigger('click');
	});
	
	$('#main-upload-btn2').on('click', function() {
		$('#main_images2').trigger('click');
	});
	$('#main-upload-btn3').on('click', function() {
		$('#main_images3').trigger('click');
	});
	$('#main-upload-btn4').on('click', function() {
		$('#main_images4').trigger('click');
	});
	$('#main-upload-btn5').on('click', function() {
		$('#main_images5').trigger('click');
	});
	$('#main-upload-btn6').on('click', function() {
		$('#main_images6').trigger('click');
	});
	$('#main-upload-btn7').on('click', function() {
		$('#main_images7').trigger('click');
	});
	
	$("#main_images").change(function(){
		var siteUrl = $("#siteUrl").val();
		$(".image-loader").html("<img src='"+siteUrl+"skin/admin/images/small-loading.gif' alt='loading' />");
		var filesLength = $("#main_images").prop("files").length;
		var file_data = $("#main_images").prop("files")[0];
		var form_data = new FormData();
		for(var i=0; i<filesLength; i++)
		{
			form_data.append("photos", $("#main_images").prop("files")[i]);
		}
		$.ajax({
			 url: siteUrl+"seller-panel/catalog/uploadGallery/"+$("#token").val()+"/1",
			 contentType: false,
			 processData: false,
			 data: form_data, // Setting the data attribute of ajax with file_data
			 type: 'post',
			 success:function(Path)
			 {
				var data = JSON.parse(Path);
				if(data['status'] == 1)
				{
					$(".image-loader").html('');
					$("#image-upload-block").html('<img src="'+siteUrl+data['filePath']+'" />');
					$("#main-image-delete-block").html('<i class="fa fa-trash" onClick="deleteImageRow('+data['file_id']+',1)" aria-hidden="true"></i>');
				}
				else
				{
					$(".image-loader").html("<font color='red'>"+data['msg']+"</font>");
				}
			 }
		
		
		
		});
	});
	
	$("#main_images2").change(function(){
		var siteUrl = $("#siteUrl").val();
		$(".image-loader").html("<img src='"+siteUrl+"skin/admin/images/small-loading.gif' alt='loading' />");
		var filesLength = $("#main_images2").prop("files").length;
		var file_data = $("#main_images2").prop("files")[0];
		var form_data = new FormData();
		for(var i=0; i<filesLength; i++)
		{
			form_data.append("photos", $("#main_images2").prop("files")[i]);
		}
		$.ajax({
			 url: siteUrl+"seller-panel/catalog/uploadGallery/"+$("#token").val(),
			 contentType: false,
			 processData: false,
			 data: form_data, // Setting the data attribute of ajax with file_data
			 type: 'post',
			 success:function(Path)
			 {
				var data = JSON.parse(Path);
				if(data['status'] == 1)
				{
					$(".image-loader").html('');
					$("#image-upload-block2").html('<img src="'+siteUrl+data['filePath']+'" />');
					$("#main-image-delete-block2").html('<i class="fa fa-trash" onClick="deleteImageRow('+data['file_id']+',2)" aria-hidden="true"></i>');
				}
				else
				{
					$(".image-loader").html("<font color='red'>"+data['msg']+"</font>");
				}
			 }
		
		
		
		});
	});
	$("#main_images3").change(function(){
		var siteUrl = $("#siteUrl").val();
		$(".image-loader").html("<img src='"+siteUrl+"skin/admin/images/small-loading.gif' alt='loading' />");
		var filesLength = $("#main_images3").prop("files").length;
		var file_data = $("#main_images3").prop("files")[0];
		var form_data = new FormData();
		for(var i=0; i<filesLength; i++)
		{
			form_data.append("photos", $("#main_images3").prop("files")[i]);
		}
		$.ajax({
			 url: siteUrl+"seller-panel/catalog/uploadGallery/"+$("#token").val(),
			 contentType: false,
			 processData: false,
			 data: form_data, // Setting the data attribute of ajax with file_data
			 type: 'post',
			 success:function(Path)
			 {
				var data = JSON.parse(Path);
				if(data['status'] == 1)
				{
					$(".image-loader").html('');
					$("#image-upload-block3").html('<img src="'+siteUrl+data['filePath']+'" />');
					$("#main-image-delete-block3").html('<i class="fa fa-trash" onClick="deleteImageRow('+data['file_id']+',3)" aria-hidden="true"></i>');
				}
				else
				{
					$(".image-loader").html("<font color='red'>"+data['msg']+"</font>");
				}
			 }
		
		
		
		});
	});
	
	$("#main_images4").change(function(){
		var siteUrl = $("#siteUrl").val();
		$(".image-loader").html("<img src='"+siteUrl+"skin/admin/images/small-loading.gif' alt='loading' />");
		var filesLength = $("#main_images4").prop("files").length;
		var file_data = $("#main_images4").prop("files")[0];
		var form_data = new FormData();
		for(var i=0; i<filesLength; i++)
		{
			form_data.append("photos", $("#main_images4").prop("files")[i]);
		}
		$.ajax({
			 url: siteUrl+"seller-panel/catalog/uploadGallery/"+$("#token").val(),
			 contentType: false,
			 processData: false,
			 data: form_data, // Setting the data attribute of ajax with file_data
			 type: 'post',
			 success:function(Path)
			 {
				var data = JSON.parse(Path);
				if(data['status'] == 1)
				{
					$(".image-loader").html('');
					$("#image-upload-block4").html('<img src="'+siteUrl+data['filePath']+'" />');
					$("#main-image-delete-block4").html('<i class="fa fa-trash" onClick="deleteImageRow('+data['file_id']+',4)" aria-hidden="true"></i>');
				}
				else
				{
					$(".image-loader").html("<font color='red'>"+data['msg']+"</font>");
				}
			 }
		
		
		
		});
	});
	
	$("#main_images5").change(function(){
		var siteUrl = $("#siteUrl").val();
		$(".image-loader").html("<img src='"+siteUrl+"skin/admin/images/small-loading.gif' alt='loading' />");
		var filesLength = $("#main_images5").prop("files").length;
		var file_data = $("#main_images5").prop("files")[0];
		var form_data = new FormData();
		for(var i=0; i<filesLength; i++)
		{
			form_data.append("photos", $("#main_images5").prop("files")[i]);
		}
		$.ajax({
			 url: siteUrl+"seller-panel/catalog/uploadGallery/"+$("#token").val(),
			 contentType: false,
			 processData: false,
			 data: form_data, // Setting the data attribute of ajax with file_data
			 type: 'post',
			 success:function(Path)
			 {
				var data = JSON.parse(Path);
				if(data['status'] == 1)
				{
					$(".image-loader").html('');
					$("#image-upload-block5").html('<img src="'+siteUrl+data['filePath']+'" />');
					$("#main-image-delete-block5").html('<i class="fa fa-trash" onClick="deleteImageRow('+data['file_id']+',5)" aria-hidden="true"></i>');
				}
				else
				{
					$(".image-loader").html("<font color='red'>"+data['msg']+"</font>");
				}
			 }
		
		
		
		});
	});
	
	$("#main_images6").change(function(){
		var siteUrl = $("#siteUrl").val();
		$(".image-loader").html("<img src='"+siteUrl+"skin/admin/images/small-loading.gif' alt='loading' />");
		var filesLength = $("#main_images6").prop("files").length;
		var file_data = $("#main_images6").prop("files")[0];
		var form_data = new FormData();
		for(var i=0; i<filesLength; i++)
		{
			form_data.append("photos", $("#main_images6").prop("files")[i]);
		}
		$.ajax({
			 url: siteUrl+"seller-panel/catalog/uploadGallery/"+$("#token").val(),
			 contentType: false,
			 processData: false,
			 data: form_data, // Setting the data attribute of ajax with file_data
			 type: 'post',
			 success:function(Path)
			 {
				var data = JSON.parse(Path);
				if(data['status'] == 1)
				{
					$(".image-loader").html('');
					$("#image-upload-block6").html('<img src="'+siteUrl+data['filePath']+'" />');
					$("#main-image-delete-block6").html('<i class="fa fa-trash" onClick="deleteImageRow('+data['file_id']+',6)" aria-hidden="true"></i>');
				}
				else
				{
					$(".image-loader").html("<font color='red'>"+data['msg']+"</font>");
				}
			 }
		
		
		
		});
	});
	
	$("#main_images7").change(function(){
		var siteUrl = $("#siteUrl").val();
		$(".image-loader").html("<img src='"+siteUrl+"skin/admin/images/small-loading.gif' alt='loading' />");
		var filesLength = $("#main_images7").prop("files").length;
		var file_data = $("#main_images7").prop("files")[0];
		var form_data = new FormData();
		for(var i=0; i<filesLength; i++)
		{
			form_data.append("photos", $("#main_images7").prop("files")[i]);
		}
		$.ajax({
			 url: siteUrl+"seller-panel/catalog/uploadGallery/"+$("#token").val(),
			 contentType: false,
			 processData: false,
			 data: form_data, // Setting the data attribute of ajax with file_data
			 type: 'post',
			 success:function(Path)
			 {
				var data = JSON.parse(Path);
				if(data['status'] == 1)
				{
					$(".image-loader").html('');
					$("#image-upload-block7").html('<img src="'+siteUrl+data['filePath']+'" />');
					$("#main-image-delete-block7").html('<i class="fa fa-trash" onClick="deleteImageRow('+data['file_id']+',7)" aria-hidden="true"></i>');
				}
				else
				{
					$(".image-loader").html("<font color='red'>"+data['msg']+"</font>");
				}
			 }
		
		
		
		});
	});
	
	$("#addMoreBtn").click(function(){
		
		var total_instruction = parseInt($("#total_instruction").val()) + 1;
		$( "#instructionTbl" ).append('<tr id="instruction_tr_'+total_instruction+'"><td>'+total_instruction+'</td><td><input type="text" class="form-control" name="instruction[]" /></td><td><i class="fa fa-minus" aria-hidden="true" onClick="deleteInsRow('+total_instruction+')"></i></td></tr>');
		$("#total_instruction").val(total_instruction);
	});
	
	$("#selSectionType").change(function(){
		var formID = $(this).val();
		if(formID == 1)
		{
			$("#product-section").css('display','block');
			$("#2-banner-section").css('display','none');
			$("#5-banner-section").css('display','none');
		}
		else if(formID == 2)
		{
			$("#product-section").css('display','none');
			$("#2-banner-section").css('display','block');
			$("#5-banner-section").css('display','none');
		}
		else if(formID == 3)
		{
			$("#product-section").css('display','none');
			$("#2-banner-section").css('display','none');
			$("#5-banner-section").css('display','block');
		}
		else
		{
			$("#product-section").css('display','none');
			$("#2-banner-section").css('display','none');
			$("#5-banner-section").css('display','none');
		}
	});

	
});


function deleteInsRow(rowID = 0)
{
	$('#instruction_tr_'+rowID).remove();
}

function deleteImageRow(rowNo = 0,imgNo = 1)
{
	$("#image_row_"+rowNo).remove();
	
		var siteUrl = $("#siteUrl").val();
		$.ajax({                
				url:siteUrl+'seller-panel/catalog/deleteImageTempData/'+rowNo,                        
				success:function(r){
					if(imgNo == 1){
						$("#image-upload-block").html('<i class="fa fa-camera" aria-hidden="true"></i>');
					}
					else
					{
						$("#image-upload-block"+imgNo).html('<i class="fa fa-camera" aria-hidden="true"></i>');
					}
				}
			});
}

function deleteDropdownRow(rowNo = 0)
{
	$("#dropdown_tr_"+rowNo).remove();
}
function deleteVisualSwatchRow(rowNo = 0)
{
	$("#visual_swatch_tr_"+rowNo).remove();
}

function deleteTextSwatchRow(rowNo = 0)
{
	$("#text_swatch_tr_"+rowNo).remove();
}

function deleteMultiselectRow(rowNo = 0)
{
	$("#multiselect_tr_"+rowNo).remove();
}

function deleteMultiDropdownRow(rowNo = 0)
{
	$("#multi_dropdown_tr_"+rowNo).remove();
}

function deleteProductImage(rowNo = 0,imgNo = 1)
{
	$("#image_row_"+rowNo).remove();
	
		var siteUrl = $("#siteUrl").val();
		$.ajax({                
				url:siteUrl+'seller-panel/catalog/deleteProductImage/'+rowNo,                        
				success:function(r){
					if(imgNo == 1){
						$("#image-upload-block").html('<i class="fa fa-camera" aria-hidden="true"></i>');
					}
					else
					{
						$("#image-upload-block"+imgNo).html('<i class="fa fa-camera" aria-hidden="true"></i>');
					}
				}
			});
}





