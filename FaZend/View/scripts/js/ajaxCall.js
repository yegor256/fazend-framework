function ajaxCall(id, url) {

	var div = document.getElementById(id).value;

	if (!div) {
		return;
	}

	new Ajax.Request(url, {
		method:'post',
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport){
			div.innerHTML = transport.responseText.evalJSON(true);
		}       
	});

}	

