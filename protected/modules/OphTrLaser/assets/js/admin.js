
$(document).ready(function() {
	$('#et_add_operator').click(function(e) {
		e.preventDefault();

		window.location.href = baseUrl+'/OphTrLaser/admin/addLaserOperator';
	});

	$('#et_delete_operator').click(function(e) {
		e.preventDefault();

		if ($('input[type="checkbox"][name="operators[]"]:checked').length == 0) {
			alert('Please select one or more operators to delete.');
			return;
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrLaser/admin/deleteOperators',
			'data': $('input[type="checkbox"][name="operators[]"]:checked').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					window.location.reload();
				} else {
					alert("Something went wrong trying to delete the operators.\n\nPlease try again or contact support for assistance.");
				}
			}
		});
	});
});
