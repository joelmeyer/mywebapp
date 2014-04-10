<HTML!>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" ></script>

</head>
<body>
<?php

echo "<form name='input' action='' method='post'> 
Username: <input id='username' type='text' name='username'><br>
Password: <input id='password' type='password' name='password'><br>
</form>
<button id='submit'>Submit </button>";


?>
<script type="text/javascript">
$('#submit').click(function(){
	console.log($("#username").val());
	//console.log($("#password").val());
	//alert("ALART: " + $("#username").val());
	$.post('ajax.php?action=logIn', 
		{ un : $("#username").val(), pass : $("#password").val()} ,
		function(data) {
			console.log("data: " + data);
  			json = $.parseJSON(data);
  			for (var i = json.length - 1; i >= 0; i--) {
  				console.log(json[i]);
  			};
  			alert('Load was performed');
		}
	);
})


</script>
</body>
