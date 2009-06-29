function ajaxCall(id, url) {

	var div = $(id);
	div.style.cursor = 'wait';

	if (!div) {
		return;
	}

	new Ajax.Request(url, {
		method:'get',
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport){
			div.innerHTML = transport.responseText.evalJSON(true);
			div.style.cursor = 'default';
		}       
	});

}	

