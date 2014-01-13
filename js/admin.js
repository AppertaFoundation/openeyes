$(document).ready(function() {

	$('#selectall').click(function() {
		$('input[type="checkbox"]').attr('checked',this.checked);
	});

	$('table').on('click', 'tr.clickable', function(e) {

		var target = $(e.target);

		// If the user clicked on an input element, or if this cell contains an input
		// element then do nothing.
		if (target.is(':input') || (target.is('td') && target.find('input').length)) {
			return;
		}

		var uri = $(this).data('uri');

		if (uri) {
			var url = uri.split('/');
			url.unshift(baseUrl);
			window.location.href = url.join('/');
		}
	});

	handleButton($('#et_save'),function(e) {
		/*e.preventDefault();

		$('#adminform').submit();*/
	});

	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();

		if ($(e.target).data('uri')) {
			window.location.href = $(e.target).data('uri');
		} else {
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
		}
	});

	handleButton($('#et_contact_cancel'),function(e) {
		e.preventDefault();
		history.back();
	});

	handleButton($('#et_add'),function(e) {
		e.preventDefault();

		if ($(e.target).data('uri')) {
			window.location.href = baseUrl+$(e.target).data('uri');
		} else {
			var e = window.location.href.split('?')[0].split('/');

			for (var i in e) {
				if (e[i] == 'admin') {
					if (e[parseInt(i)+1].match(/ies$/)) {
						var object = ucfirst(e[parseInt(i)+1].replace(/ies$/,'y'));
					} else {
						var object = ucfirst(e[parseInt(i)+1].replace(/s$/,''));
					}
					window.location.href = baseUrl+'/admin/add'+object;
				}
			}
		}
	});

	handleButton($('#et_delete'),function(e) {
		e.preventDefault();

		if ($(e.target).data('object')) {
			var object = $(e.target).data('object');
			if (object.charAt(object.length-1) != 's') {
				object = object + 's';
			}
		} else {
			var x = window.location.href.split('?')[0].split('/');

			for (var i in x) {
				if (x[i] == 'admin') {
					var object = x[parseInt(i)+1].replace(/s$/,'');
				}
			}
		}

		if ($('#admin_'+object).serialize().length == 0) {
			alert("Please select one or more items to delete.");
			return;
		}

		if ($(e.target).data('uri')) {
			var uri = baseUrl+$(e.target).data('uri');
		} else {
			var uri = baseUrl+'/admin/delete'+ucfirst(object);
		}

		$.ajax({
			'type': 'POST',
			'url': uri,
			'data': $('#admin_'+object).serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(html) {
				if (html == '1') {
					window.location.reload();
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "One or more "+object+" could not be deleted as they are in use."
					}).open();
				}
			}
		});
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
					new OpenEyes.UI.Dialog.Alert({
						content: "User not found"
					}).open();
				}
			}
		});
	});

	handleButton($('#et_add_label'),function(e) {
		e.preventDefault();
		/* TODO */
	});


	// Custom episode summaries

	$('#episode-summary #subspecialty_id').change(
		function () { window.location.href = baseUrl + '/admin/episodeSummaries?subspecialty_id=' + this.value; }
	);

	var showHideEmpty = function (el, min) {
		if (el.find('.episode-summary-item').length > min) {
			el.find('.episode-summary-empty').hide();
		} else {
			el.find('.episode-summary-empty').show();
		}
	}

	var items_enabled = $('#episode-summary-items-enabled');
	var items_available = $('#episode-summary-items-available');

	var extractItemIds = function () {
		$('#episode-summary #item_ids').val(
			items_enabled.find('.episode-summary-item').map(
				function () { return $(this).data('item-id'); }
			).get().join(',')
		);
	}

	showHideEmpty(items_enabled, 0);
	showHideEmpty(items_available, 0);
	extractItemIds();

	var options = {
		containment: '#episode-summary-items',
		items: '.episode-summary-item',
		change: function (e, ui) {
			showHideEmpty($(this), 0);
			if (ui.sender) showHideEmpty(ui.sender, 1);
		},
	}

	items_enabled.sortable($.extend({connectWith: items_available}, options));
	items_available.sortable($.extend({connectWith: items_enabled}, options));

	$('#episode-summary form').submit(extractItemIds);
	$('#episode-summary-cancel').click(function () { location.reload(); });
});
