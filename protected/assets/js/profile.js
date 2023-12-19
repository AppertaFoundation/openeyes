$(document).ready(function() {
	handleButton($('#et_save'),function(e) {
		e.preventDefault();
		$('#profile-form').submit();
	});
	handleButton($('#user-settings-save'),function(e) {
		e.preventDefault();
		$('#user-settings-form').submit();
	});
});
