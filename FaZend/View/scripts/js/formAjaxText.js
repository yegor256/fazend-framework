function ajax_UpdateList(field, list, url, next, hand, handUrl) {

	var mask = field.val();

	if (!mask) {
		list.hide();
		return;
	}

	// hide the error, if it is visible since last form submit
	//if ($('#' + field + 'Errors'))
	//	$('#' + field + 'Errors').hide();

	$.ajax({
		type: "POST",
		url: url,
		data: {mask: mask},
		dataType: "json",
		success: function(json){

			list.empty();
			$.each(json, function(i, line) {
				var keyword = document.createElement('li');
				keyword.innerHTML = line.replace(mask, '<b>'+mask+'</b>');
				keyword.onclick=function(){
					field.val(this.innerHTML.replace(/<\/?b>/gi, ''));
					if (next)
						next.focus();
					ajax_CheckField(field, hand, handUrl);
				};
				list.append(keyword);
			});

			if (json.length > 0) {
				list.show();
			} else {
				list.hide();
			}	

		}       
	});

	ajax_CheckField(field, hand, handUrl);
}	

function ajax_LostFocus(list) {

	setTimeout(function() {list.hide();}, 200);

}

function ajax_CheckField(field, hand, handUrl) {
	
	var mask = field.val();

	if (!mask) {
		hand.hide();
		return;
	}

	$.ajax({
		url: handUrl,
		type: "POST",
		data: {mask: mask},
		dataType: "json",
		success: function(data){

			if (data == true) {
				hand.show();
			} else {
				hand.hide();
			}	
		}      
	});

}	

