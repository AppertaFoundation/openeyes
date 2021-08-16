$(document).ready(function() {
	handleButton($('#et_save'),function(e) {
		e.preventDefault();
		$('#profile-form').submit();
	});
	handleButton($('#et_get_signature'),function (e) {
		e.preventDefault();
		$.ajax({
			'type': 'GET',
			'url': baseUrl + '/profile/getSignatureFromPortal',
			'success': function (result) {
				window.location.reload();
			}
		});
	});
	handleButton($('#et_show_signature'),function (e) {
		e.preventDefault();
		$.ajax({
			'type': 'POST',
			'url': baseUrl + '/profile/showSignature',
			'dataType': 'text',
			'data': {
				'signaturePin': $('#signature_pin').val(),
				'YII_CSRF_TOKEN': $('#YII_CSRF_TOKEN').val()
			},
			'success': function (result) {
				if(result != false) {
					$('#signature_image').html('<img src="data:image/png;base64,' + result + '">');
				}else{
					$('#signature_image').html('<p>ERROR: Your PIN is not valid!</p>');
				}
				enableButtons();
			}
		});
	});
	handleButton($('#user-settings-save'),function(e) {
		e.preventDefault();
		$('#user-settings-form').submit();
	});
});
