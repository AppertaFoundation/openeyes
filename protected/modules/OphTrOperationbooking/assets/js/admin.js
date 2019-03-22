$(document).ready(function() {

	$('#et_add_erod_rule').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/addERODRule';
	});

	$('#et_delete_erod_rule').click(function(e) {
		e.preventDefault();
		if ($('input.erod_rules[type="checkbox"]:checked').length == 0) {
			new OpenEyes.UI.Dialog.Alert({
				content: "You haven't selected any rules to delete."
			}).open();
		} else {
			$.ajax({
				'type': 'POST',
				'url': baseUrl+'/OphTrOperationbooking/admin/deleteERODRules',
				'data': $('#erod_rules').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(resp) {
					if (resp == "1") {
						window.location.reload();
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Something went wrong trying to delete the rules. Please try again or contact support for assistance.\n\n" + resp
						}).open();
					}
				},
				'error': function(resp, status, error) {
					new OpenEyes.UI.Dialog.Alert({
						content: "Something went wrong " + status + ": " + error}).open();
				}
			});
		}
	});

    $('#OphTrOperationbooking_Operation_Theatre_site_id').change(function() {
        var siteId = $(this).val();

        $.ajax({
            'url': baseUrl+'/OphTrOperationbooking/admin/getWardOptions?siteId='+siteId,
            'type': 'GET',
            'success': function(data) {
                $('#OphTrOperationbooking_Operation_Theatre_ward_id').html(data);
            }
        });
    });

	$('#lcr_site_id').change(function() {
		var siteId = $(this).val();

		$('#rules li').children('a').removeAttr('style');

		if ($('#lcr_theatre_id').length >0) {
			$.ajax({
				'type': 'POST',
				'data': {'site_id': siteId, 'YII_CSRF_TOKEN': YII_CSRF_TOKEN, 'empty': 1},
				'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterTheatres',
				'success':function(data) {
					$('#lcr_theatre_id').html(data);
				}
			});
		} else {
			OphTrOperationbooking_showMatchingRule();
		}
	});

	$('#lcr_subspecialty_id').change(function() {
		var subspecialtyId = $(this).val();

		$('#rules li').children('a').removeAttr('style');

		$.ajax({
			'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterFirms',
			'type': 'POST',
			'data': 'subspecialty_id='+subspecialtyId+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN+"&empty=1",
			'success': function(data) {
				$('#lcr_firm_id').html(data);
			}
		});
	});

	$('#lcr_service_id').change(function() {
		var service_id = $(this).val();

		$('#rules li').children('a').removeAttr('style');

		$.ajax({
			'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterFirms',
			'type': 'POST',
			'data': 'service_id='+service_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN+"&empty=1",
			'success': function(data) {
				$('#lcr_firm_id').html(data);
			}
		});
	});

	$('#lcr_firm_id, #lcr_theatre_id, #lcr_is_child, #lcr_rule_type_id').change(function() {
		$('#rules li').children('a').removeAttr('style');

		OphTrOperationbooking_showMatchingRule();
	});

	$('#rules a.treenode').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/edit'+OE_rule_model+'/'+id;
	});

	$('#et_add_letter_contact_rule').click(function() {
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/add'+OE_rule_model;
	});

	$('#et_add_operation_name_rule').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/add'+OE_rule_model;
	});

	$('#rules a.addTreeItemHere').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/add'+OE_rule_model+'?parent_rule_id='+$(this).attr('rel');
	});

	$('#operation_name_rules li .column_theatre, #operation_name_rules li .column_name').click(function(e) {
		e.preventDefault();

		if ($(this).parent().attr('data-attr-id')) {
			window.location.href = baseUrl+'/OphTrOperationbooking/admin/editOperationNameRule/'+$(this).parent().attr('data-attr-id');
		}
	});

	$('#et_delete_operation_name_rule').click(function() {
		if ($('input.operation_name_rules[type="checkbox"]:checked').length == 0) {
			new OpenEyes.UI.Dialog.Alert({
				content: "You haven't selected any rules to delete."
			}).open();
		} else {
			$.ajax({
				'type': 'POST',
				'url': baseUrl+'/OphTrOperationbooking/admin/deleteOperationNameRules',
				'data': $('#operation_name_rules').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(resp) {
					if (resp == "1") {
						window.location.reload();
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Something went wrong trying to delete the rules. Please try again or contact support for assistance."
						}).open();
					}
				}
			});
		}
	});

	$(this).on('click','#et_add_sequence',function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/addSequence';
	});

	$('#admin_sessions li.even .column_firm, #admin_sessions li.even .column_theatre, #admin_sessions li.even .column_date, #admin_sessions li.even .column_time, #admin_sessions li.even .column_weekday, #admin_sessions li.even .column_available, #admin_sessions li.even .column_attributes, #admin_sessions li.odd .column_firm, #admin_sessions li.odd .column_theatre, #admin_sessions li.odd .column_date, #admin_sessions li.odd .column_time, #admin_sessions li.odd .column_weekday, #admin_sessions li.odd column_available, #admin_sessions li.odd .column_attributes').click(function(e) {
		e.preventDefault();

		if ($(this).parent().attr('data-attr-id')) {
			window.location.href = baseUrl+'/OphTrOperationbooking/admin/editSession/'+$(this).parent().attr('data-attr-id');
		}
	});

	$('#OphTrOperationbooking_Operation_Session_max_procedures').on('keyup', function(e) {
		e.preventDefault();
		var limit = parseInt($('#current-proc-count').html());
		if (limit > 0 && parseInt($(this).val()) < limit) {
			$('#current-proc-count').parent().parent().addClass('alert-box alert');
		}
		else {
			$('#current-proc-count').parent().parent().removeClass('alert-box alert');
		}
	});

	$(this).on('click','#et_add_session',function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/addSession';
	});

	$('#et_add_theatre').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/addTheatre';
	});

	$('#et_add_ward').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/addWard';
	});

	$('#admin_schedulingoptions li.even .column_name, #admin_schedulingoptions li.odd .column_name').click(function(e) {
		e.preventDefault();

		if ($(this).parent().attr('data-attr-id')) {
			window.location.href = baseUrl+'/OphTrOperationbooking/admin/editSchedulingOption/'+$(this).parent().attr('data-attr-id');
		}
	});

	$('#et_add_scheduleoption').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/addSchedulingOption';
	});

	var fixHelperModified = function(e, tr) {
		var $originals = tr.children();
		var $helper = tr.clone();
		$helper.children().each(function(index)
		{
			$(this).width($originals.eq(index).width())
		});
		return $helper;
	};

	$('.sortable').sortable({
		helper: fixHelperModified,
		update: function (event, ui) {
			var ids = [];
			$('tbody.sortable').children('tr').map(function () {
				ids.push($(this).attr('data-attr-id'));
			});
			$.ajax({
				'type': 'POST',
				'url': $('tbody.sortable').data('sort-uri'),
				'data': {order: ids, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
				'success': function (data) {
					new OpenEyes.UI.Dialog.Alert({
						content: 'Re-ordered'
					}).open();
				}
			});

		}
	}).disableSelection();
});

function OphTrOperationbooking_showMatchingRule() {
	if ($('#lcr_site_id').val() !== '' && $('#lcr_firm_id').val() !== '') {

		// only require these if they're in the dom
		if ($('#lcr_is_child').length && $('#lcr_is_child').val() === ''){
            return;
		}
		if ($('#lcr_rule_type_id') && $('#lcr_rule_type_id').val() === '') {
			return;
		}
		if ($('#lcr_subspecialty_id') && $('#lcr_subspecialty_id').val() === '') {
			return;
		}
		if ($('#lcr_theatre_id') && $('#lcr_theatre_id').val() === ''){
            return;
		}
		if ($('#lcr_service_id') && $('#lcr_service_id').val() === ''){
            return;
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/test'+OE_rule_model+'s',
			'data': $('#rulestest').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'dataType': 'json',
			'success': function(resp) {
				var count = 0;
				for (var i in resp) {
					$('#rules li[id="'+resp[i]+'"]').children('a').attr('style','color: #f00');
					count += 1;
					$('#nomatch').hide();
				}

				if (count <1) {
					$('#nomatch').show();
				}
			}
		});
	}
}
