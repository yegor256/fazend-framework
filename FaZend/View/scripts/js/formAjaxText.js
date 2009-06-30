//var ajax_WaitingTimer = false;

function ajax_UpdateList(field, list, url, next, hand, handUrl) {

	var mask = $(field).value;

	if (!mask) {
		$(list).hide();
		return;
	}

	// hide the error, if it is visible since last form submit
	if ($(field + 'Errors'))
		$(field + 'Errors').hide();

	$.ajax({
		type: "POST",
		url: url,
		data: {mask: mask},
		dataType: "json",
		success: function(data){

			var listDiv = $(list);
			listDiv.empty();
			
			data.each(function(line) {
				var keyword = document.createElement('li');
				keyword.innerHTML = line.replace(mask, '<b>'+mask+'</b>');
				keyword.onclick=function(){
					$(field).value = this.innerHTML.replace(/<\/?b>/gi, '');
					if ($(next))
						$(next).focus();
					ajax_CheckField(field, hand, handUrl);
				};
				listDiv.append(keyword);
			});

			if (listDiv.firstChild) {
				listDiv.show();
			} else {
				listDiv.hide();
			}	

		}       
	});

	ajax_CheckField(field, hand, handUrl);
}	

function ajax_LostFocus(list) {

	setTimeout(function() {$(list).hide();}, 200);

}

function ajax_CheckField(field, hand, handUrl) {
	
	var mask = $(field).value;

	var div = $(hand);
	if (!mask) {
		div.hide();
		return;
	}

	$.ajax({
		url: handUrl,
		type: "POST",
		data: {mask: mask},
		dataType: "json",
		success: function(data){

			if (data == true) {
				div.show();
			} else {
				div.hide();
			}	
		}      
	});

}	

