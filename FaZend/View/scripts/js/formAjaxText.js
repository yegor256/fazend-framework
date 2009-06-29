//var ajax_WaitingTimer = false;

function ajax_UpdateList(field, list, url, next, hand, handUrl) {

	var mask = $(field).value;

	if (!mask) {
		$(list).style.display = 'none';
		return;
	}

	// hide the error, if it is visible since last form submit
	if ($(field + 'Errors'))
		$(field + 'Errors').style.display = 'none';

	new Ajax.Request(url, {
		method:'post',
		parameters: {mask: mask},
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport){

			var listDiv = $(list);
			while (listDiv.hasChildNodes()) {
				listDiv.removeChild(listDiv.firstChild);
			}	
			
			transport.responseText.evalJSON(true).each(function(line) {
				var keyword = document.createElement('li');
				keyword.innerHTML = line.replace(mask, '<b>'+mask+'</b>');
				keyword.onclick=function(){
					$(field).value = this.innerHTML.replace(/<\/?b>/gi, '');
					if ($(next))
						$(next).focus();
					ajax_CheckField(field, hand, handUrl);
				};
				listDiv.appendChild(keyword);
			});

			if (listDiv.firstChild) {
				listDiv.style.display = 'block';
			} else {
				listDiv.style.display = 'none';
			}	

		}       
	});

	ajax_CheckField(field, hand, handUrl);
}	

function ajax_LostFocus(list) {

	setTimeout(function(){
		$(list).style.display = 'none';
	}, 200);

}

function ajax_CheckField(field, hand, handUrl) {
	
	var mask = $(field).value;

	var div = $(hand);
	if (!mask) {
		div.style.visibility = 'hidden';
		return;
	}

	new Ajax.Request(handUrl, {
		method:'post',
		parameters: {mask: mask},
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport){

			var json = transport.responseText.evalJSON(true);

			if (json == true) {
				div.style.visibility = 'visible';
			} else {
				div.style.visibility = 'hidden';
			}	
		}      
	});

}	

