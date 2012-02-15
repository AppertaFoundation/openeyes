var button_colours = ["red","blue","green"];
var button_cache = {};

function disableButtons() {
	for (var i in button_colours) {
		var col = button_colours[i];
		var selection = $('button.'+col);
		selection.removeClass(col).addClass('inactive');
		selection.children('span').removeClass('button-span-'+col).addClass('button-span-inactive');
		button_cache[col] = selection;
		$('.loader').show();
	}
}

function enableButtons() {
	for (var i in button_colours) {
		var col = button_colours[i];
		button_cache[col].removeClass('inactive').addClass(col);
		button_cache[col].children('span').removeClass('button-span-inactive').addClass('button-span-'+col);
		$('.loader').hide();
	}
}
