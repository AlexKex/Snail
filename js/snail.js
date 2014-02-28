$(document).ready(function(){
	$("[name=submit]").on("click", function(){
		var params = {
			length : parseInt($("input[name=length]").val())
		};
		
		$("#snail-result").hide(500);
		$("#snail-result").empty();
		
		$.post("snail.php", params, function(answer){
			if(answer.ok == 0)
			{
				alert(answer.error);
			}
			else{
				$("#snail-result").append(answer.code);
				$("#snail-result").show(500);
			}
		}, "json");
	});
});
