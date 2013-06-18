$(document).ready(function() {
	handleButton($('#et_save'),function(e) {
		e.preventDefault();
		$('#profile-form').submit();
	});
});
