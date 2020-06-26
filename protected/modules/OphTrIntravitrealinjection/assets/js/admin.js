$(document).ready(function() {
	if (typeof (OphTrIntravitrealinjection_sort_url) !== 'undefined') {
		$('.sortable').sortable({
			update: function (event, ui) {
				var ids = [];
				$('div.sortable').children('li').map(function () {
					ids.push($(this).attr('data-attr-id'));
				});
				$.ajax({
					'type': 'POST',
					'url': OphTrIntravitrealinjection_sort_url,
					'data': {order: ids},
					'success': function (data) {
					}
				});
			}
		});
	}

	$('.addUser').click(function(e) {
		e.preventDefault();

		if ($('#user_id').val() == '') {
			alert('Please select a user.');
		} else {
			$.ajax({
				'type': 'POST',
				'url': baseUrl + '/OphTrIntravitrealinjection/admin/addInjectionUser',
				'data': 'user_id=' + $('#user_id').val() + '&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN,
				'success': function(resp) {
					if (resp != "1") {
						alert("Something went wrong trying to add the user. Please try again or contact support for assistance.");
					} else {
						window.location.reload();
					}
				}
			});
		}
	});

	$('.deleteUser').click(function(e) {
		e.preventDefault();

		var users = {"user_id": []};

		$('#admin_injection_users').find('input[name="injection_users[]"]:checked').map(function() {
			users["user_id"].push($(this).val());
		});

		if (users.length == 0) {
			alert("Please select one or more users to delete.");
			return;
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl + '/OphTrIntravitrealinjection/admin/deleteInjectionUsers',
			'data': $.param(users) + '&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp != "1") {
					alert("Something went wrong trying to delete the user(s). Please try again or contact support for assistance.");
				} else {
					window.location.reload();
				}
			}
		});
	});
});
