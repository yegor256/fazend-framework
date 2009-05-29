//var ajax_WaitingTimer = false;

function ajax_UpdateList(field, list, url, next, hand, handUrl) {

	var mask = document.getElementById(field).value;

	if (!mask) {
		document.getElementById(list).style.display = 'none';
		return;
	}

	// hide the error, if it is visible since last form submit
	if (document.getElementById(field + 'Errors'))
		document.getElementById(field + 'Errors').style.display = 'none';

	new Ajax.Request(url, {
		method:'post',
		parameters: {mask: mask},
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport){

			var listDiv = document.getElementById(list);
			while (listDiv.hasChildNodes()) {
				listDiv.removeChild(listDiv.firstChild);
			}	
			
			transport.responseText.evalJSON(true).each(function(line) {
				var keyword = document.createElement('li');
				keyword.innerHTML = line.replace(mask, '<b>'+mask+'</b>');
				keyword.onclick=function(){
					document.getElementById(field).value = this.innerHTML.replace(/<\/?b>/g, '');
					if (document.getElementById(next))
						document.getElementById(next).focus();
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
		document.getElementById(list).style.display = 'none';
	}, 200);

}

function ajax_CheckField(field, hand, handUrl) {
	
	var mask = document.getElementById(field).value;

	var div = document.getElementById(hand);
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

