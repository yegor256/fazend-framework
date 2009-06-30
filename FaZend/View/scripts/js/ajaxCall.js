function ajaxCall(div, url) {

	div.css('cursor:wait');

	$.ajax({
		url: url,
		type: "GET",
		dataType: "json",
		success: function(json){
			div.html(json);
			div.css('cursor:default');
		}       
	});

}	

