function ajaxCall(id, url) {

	var div = document.getElementById(id);

	if (!div) {
		return;
	}

	new Ajax.Request(url, {
		method:'get',
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport){
			div.innerHTML = transport.responseText.evalJSON(true);
		}       
	});

}	

