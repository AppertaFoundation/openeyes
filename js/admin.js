$(document).ready(function() {
	$('#selectall').click(function() {
		$('input[type="checkbox"]').attr('checked',this.checked);
	});

	$('#admin_users li .column_id, #admin_users li .column_username, #admin_users li .column_title, #admin_users li .column_firstname, #admin_users li .column_lastname, #admin_users li .column_role, #admin_users li .column_doctor, #admin_users li .column_active').click(function(e) {
		e.preventDefault();

		if ($(this).parent().attr('data-attr-id')) {
			window.location.href = baseUrl+'/admin/editUser/'+$(this).parent().attr('data-attr-id');
		}
	});

	$('#admin_firms li .column_id, #admin_firms li .column_pas_code, #admin_firms li .column_name, #admin_firms li .column_subspecialty, #admin_firms li .column_consultant').click(function(e) {
		e.preventDefault();

		if ($(this).parent().attr('data-attr-id')) {
			window.location.href = baseUrl+'/admin/editFirm/'+$(this).parent().attr('data-attr-id');
		}
	});

	handleButton($('#et_save'),function(e) {
		e.preventDefault();

		$('#adminform').submit();
	});

	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();

		var e = window.location.href.split('/');

		var page = false;

		if (parseInt(e[e.length-1])) {
			page = Math.ceil(parseInt(e[e.length-1]) / items_per_page);
		}

		for (var i in e) {
			if (e[i] == 'admin') {
				var object = e[parseInt(i)+1].replace(/^[a-z]+/,'').toLowerCase()+'s';
				window.location.href = baseUrl+'/admin/'+object+(page ? '/'+page : '');
			}
		}
	});

	handleButton($('#et_add'),function(e) {
		e.preventDefault();

		var e = window.location.href.split('/');

		for (var i in e) {
			if (e[i] == 'admin') {
				var object = ucfirst(e[parseInt(i)+1].replace(/s$/,''));
				window.location.href = baseUrl+'/admin/add'+object;
			}
		}
	});

	handleButton($('#lookup_user'),function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/admin/lookupUser?username='+$('#User_username').val(),
			'success': function(resp) {
				m = resp.match(/[0-9]+/);
				if (m) {
					window.location.href = baseUrl+'/admin/editUser/'+m[0];
				} else {
					alert("User not found");
				}
			}
		});
	});
});
