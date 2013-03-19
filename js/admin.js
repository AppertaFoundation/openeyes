$(document).ready(function() {
	$('#admin_users li').click(function(e) {
		e.preventDefault();

		if ($(this).attr('data-attr-id')) {
			window.location.href = baseUrl+'/admin/users/edit/'+$(this).attr('data-attr-id');
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
				window.location.href = baseUrl+'/admin/'+e[parseInt(i)+1]+(page ? '/'+page : '');
			}
		}
	});
});
