<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("button").click(function(){
	var data = {biller_id:'JVVNL0000RAJ01',mobile:'8104758957',local_agent:'d1abdd6d1f056f26939eac794c70e970ba98f10faba423fe48cc10afeca69f9618a470ff99104ab66deb99e8f2f183a1aa8dd21351ce0420ae4846ecb7739bc1471iMz/zS1Mkp5NihsYdPwlUxy6qXUcecFEYwMhhlyByzOZThdSES8FXnOCCqjwYOz6hMM/v0s/h75J6iCsIHuFBqSb0SSNO6zL+0Pc+Kbv5SP6yeRFBk/QkLHlWu5Y6eJQDyFh1CBjCTK1EP+3rYGSPbuxGtXobtGyg1qPSssDQPvlWg6aohok4tFQql7ung3mn2t7D7gwHql/FUbqm+2lH9kmBD4haWcYJ8AU8PQ==',bbps_param_1:'210511003700',csrf_cscportal_token2:'f494e775a545ed30281d5ea5e4ab23f5'};
    $.ajax({
    url: 'https://digitalseva.csc.gov.in/newbbps/billenquiry',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
		'Cookie':'digitalseva=q5qco9vk2b2phkjj5cjv4grmm86om8j3; digitalsevacsrf_cookie_name2=f494e775a545ed30281d5ea5e4ab23f5',
		'Host':'digitalseva.csc.gov.in',
		'Origin':'https://digitalseva.csc.gov.in',
		'Referer':'https://digitalseva.csc.gov.in/services/electricity'
    },
    method: 'POST',
    data: JSON.stringify(data),
    success: function(data){
      console.log('succes: '+data);
    }
  });
  });
});
</script>
</head>
<body>

<div id="div1"><h2>Let jQuery AJAX Change This Text</h2></div>

<button>Get External Content</button>

</body>
</html>
