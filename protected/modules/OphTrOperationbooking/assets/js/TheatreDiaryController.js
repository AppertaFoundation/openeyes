/**
 * OpenEyes
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/

$(document).ready(function() {
	$('#theatre-filter button[type="submit"]').click(function() {
		return getDiary();
	});

	$('#theatre-filter input[name=emergency_list]').change(function() {
		$('#site-id').attr("disabled", $(this).is(':checked'));
		$('#subspecialty-id').attr("disabled", $(this).is(':checked'));
		$('#theatre-id').attr("disabled", $(this).is(':checked'));
		$('#firm-id').attr("disabled", $(this).is(':checked'));
		$('#ward-id').attr("disabled", $(this).is(':checked'));
	});

	$('#date-filter_0').click(function() {
		today = new Date();

		clearBoundaries();


		$('#date-start').val(format_pickmeup_date(today));
		$('#date-end').val(format_pickmeup_date(today));

		setDiaryFilter({'date-filter':'today','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});

		return true;
	});

	$('#date-filter_1').click(function() {
		today = new Date();

		clearBoundaries();

		$('#date-start').val(format_pickmeup_date(today));
		$('#date-end').val(format_pickmeup_date(returnDateWithInterval(today, 6)));

		setDiaryFilter({'date-filter':'week','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});

		return true;
	});

	$('#date-filter_2').click(function() {
		today = new Date();

		clearBoundaries();

		$('#date-start').val(format_pickmeup_date(today));
		$('#date-end').val(format_pickmeup_date(returnDateWithInterval(today, 29)));

		setDiaryFilter({'date-filter':'month','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});

		return true;
	});

	$('#date-filter_3').click(function() {

		setDiaryFilter({'date-filter':'custom','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});

		return true;
	});

	$('#last_week').click(function() {
		sd = $('#date-start').val();

		clearBoundaries();

		if (sd == '') {
			today = new Date();
			$('#date-start').val(format_pickmeup_date(returnDateWithInterval(today, -8)));
			$('#date-end').val(format_pickmeup_date(returnDateWithInterval(today, -1)));
		} else {
			$('#date-end').val(format_pickmeup_date(returnDateWithInterval(new Date(sd), -1)));
			$('#date-start').val(format_pickmeup_date(returnDateWithInterval(new Date(sd), -7)));
		}

		setDiaryFilter({'date-filter':''});
		$('input[type="radio"]').attr('checked',true);
		$('#date-start').trigger('change');
		$('#date-end').trigger('change');
		return false;
	});

	$('#next_week').click(function() {
		ed = $('#date-end').val();

		clearBoundaries();

		if (ed == '') {
			today = new Date();

			$('#date-start').val(today);
			$('#date-end').val(format_pickmeup_date(returnDateWithInterval(today, 7)));
		} else {
			today = new Date();

			if (ed == format_pickmeup_date(today)) {
				$('#date-start').val(format_pickmeup_date(returnDateWithInterval(new Date(ed), 7)));
				$('#date-end').val(format_pickmeup_date(returnDateWithInterval(new Date(ed), 13)));
			} else {
				$('#date-start').val(format_pickmeup_date(returnDateWithInterval(new Date(ed), 1)));
				$('#date-end').val(format_pickmeup_date(returnDateWithInterval(new Date(ed), 7)));
			}
		}

		setDiaryFilter({'date-filter':''});
		$('input[type="radio"]').attr('checked',true);
		$('#date-start').trigger('change');
		$('#date-end').trigger('change');

		return false;
	});

	$('#theatre-filter select').change(function() {
		var hash = {};
		hash[$(this).attr('id')] = $(this).val();
		setDiaryFilter(hash);
	});

	$('#emergency_list').click(function() {
		if ($(this).is(':checked')) {
			setDiaryFilter({'emergency_list':1});
		} else {
			setDiaryFilter({'emergency_list':0});
		}
	});

	$('#date-start').change(function() {
		setDiaryFilter({'date-start':$(this).val()});
		$('#date-filter_3').attr('checked','checked');
	});

	$('#date-end').change(function() {
		setDiaryFilter({'date-end':$(this).val()});
		$('#date-filter_3').attr('checked','checked');
	});

	$("#btn_print_diary").click(function() {
		disableButtons();
		printElem('printDiary', {
			pageTitle:'openeyes printout',
			printBodyOptions:{styleToAdd:'width:auto !important; margin: 0.75em !important;',classNameToAdd:'openeyesPrintout'},
			//overrideElementCSS:['css/module.css',{href:'css/module.css',media:'print'}]
		}, enableButtons);
	});

	$('#btn_print_diary_list').click(function() {
		if ($('#site-id').val() == '' || $('#subspecialty-id').val() == '' || $('#date-start').val() == '' || $('#date-end').val() == '') {
			new OpenEyes.UI.Dialog.Alert({
				content: 'To print the booking list you must select a site, a subspecialty and a date range.',
				onClose: function() {
					scrollTo(0,0);
				}
			}).open();
			return false;
		}
		disableButtons();
		printElem('printList',{
			pageTitle:'openeyes printout',
			printBodyOptions:{
				styleToAdd:'width:auto !important; margin: 0.75em !important;',
				classNameToAdd:'openeyesPrintout'
			},
			//overrideElementCSS:['css/module.css',{href:'css/module.css',media:'print'}]
		}, enableButtons);
	});

	$(this).undelegate('.edit-session','click').delegate('.edit-session','click',function() {
		cancel_edit();

		disableButtons($('button,.button').not('.theatre'));
		$('.spinner').hide();

		theatre_edit_session_id = $(this).attr('rel');

		theatre_edit_session_data = {};

		if ($('div.purpleUser').length >0) {
			theatre_edit_session_data["purple_rinse"] = {
				"consultant": $('#consultant_'+theatre_edit_session_id).is(':checked'),
				"paediatric": $('#paediatric_'+theatre_edit_session_id).is(':checked'),
				"anaesthetist": $('#anaesthetist_'+theatre_edit_session_id).is(':checked'),
				"general_anaesthetic": $('#general_anaesthetic_'+theatre_edit_session_id).is(':checked'),
				"available": $('#available_'+theatre_edit_session_id).is(':checked')
			};
		}

		theatre_edit_session_data["row_order"] = [];
		theatre_edit_session_data["confirm"] = {};
		theatre_edit_session_data["comments"] = $('.comments p.comments[data-id="'+theatre_edit_session_id+'"]').text();

		$('#tbody_'+theatre_edit_session_id).children('tr').map(function(){
			theatre_edit_session_data["row_order"].push($(this).attr('id'));
			var id = $(this).attr('id').match(/[0-9]+/);
			theatre_edit_session_data["confirm"][id] = $('#confirm_'+id).is(':checked');
		});

		$('.diaryViewMode').hide();
		$('.diaryEditMode[data-id="'+theatre_edit_session_id+'"]').show();
		$('.action_options[data-id="'+theatre_edit_session_id+'"]').show();

		$("#tbody_"+theatre_edit_session_id).sortable({
			 helper: function(e, tr) {
				 var $originals = tr.children();
				 var $helper = tr.clone();
				 $helper.children().each(function(index) {
					 $(this).width($originals.eq(index).outerWidth())
				 });
				 return $helper;
			 },
			 placeholder: 'theatre-list-sort-placeholder'
		}).disableSelection();
		$("#theatre_list tbody").sortable('enable');

		$('tbody[id="tbody_'+theatre_edit_session_id+'"] td.confirm input[name^="confirm_"]').attr('disabled',false);
		$('th.footer').attr('colspan','10');

		return false;
	});

	$(this).undelegate('a.view-session','click').delegate('a.view-session','click',function() {
		cancel_edit();
		return false;
	});

	$('input[id^="consultant_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('consultant',session_id);
		}
	});

	$('input[id^="paediatric_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('paediatric',session_id);
		}
	});

	$('input[id^="anaesthetist_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('anaesthetist',session_id);
		}
	});

	$('input[id^="general_anaesthetic_"]').die('click').live('click',function() {
		if (!$(this).is(':checked')) {
			var session_id = $(this).attr('id').match(/[0-9]+/);
			checkRequired('general_anaesthetic',session_id);
		}
	});

	$('.session-available').die('click').live('click',function() {
		var reasons = $(this).parent().next().find('.unavailable-reasons');
		// if they are changing status back and forth, don't want to lose any reason they may have selected,
		// but don't want to submit it if they set it available
		if ($(this).is(':checked')) {
			reasons.parent().hide();
			reasons.data('orig', reasons.val());
			reasons.val('');
		}
		else {
			reasons.val(reasons.data('orig'));
			reasons.parent().show();
		}
	});

	$(this).undelegate('button[id^="btn_edit_session_save_"]','click').delegate('button[id^="btn_edit_session_save_"]','click',function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			$('.diaries-search .spinner').hide();

			var session_id = $(this).attr('id').match(/[0-9]+/);

			$('input[name^="admitTime_"]').map(function() {
				var m = $(this).val().match(/^([0-9]{1,2}).*?([0-9]{2})$/);
				if (m) {
					if (m[1].length == 1) {
						m[1] = '0'+m[1];
					}
					$(this).val(m[1]+':'+m[2]);
				}
			});

			$.ajax({
				type: "POST",
				data: $('#session_form'+session_id).serialize()+"&session_id="+session_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				dataType: 'json',
				url: baseUrl+'/OphTrOperationbooking/theatreDiary/saveSession',
				success: function(errors) {
					var opErrs = null;
					var nonOpErrs = '';
					$('#tbody_'+session_id).children('tr').attr('style','');

					for (var operation_id in errors) {
						if (!$('#oprow_'+operation_id).length) {
							nonOpErrs += '<li>' + operation_id + ': ' + errors[operation_id] + '</li>';
						}
						else {
							$('#oprow_'+operation_id).attr('style','background-color: #f00;');
							if (!opErrs) {
								$('input[name="admitTime_'+operation_id+'"]').select().focus();
								opErrs = "One or more admission times were entered incorrectly, please correct the entries highlighted in red.";
							}
						}
					}
					if (nonOpErrs) {
						if (opErrs) {
							nonOpErrs += "<li>" + opErrs + "</li>"
						}
						opErrs = "Please check the following errors:<ul>"+nonOpErrs+"</ul>";
					}

					if (opErrs) {
						new OpenEyes.UI.Dialog.Alert({
							content: opErrs
						}).open();
						enableButtons();
						return false;
					}

					var markSessionUnavailable = false;

					$('tr[id^="oprow_"]').attr('style','');

					$('#session_form'+session_id+' span.admitTime_ro').map(function() {
						$(this).text($('input[name="admitTime_'+$(this).attr('data-operation-id')+'"]').val());
					});

					$('.session-comments .comments[data-id="'+session_id+'"] .comment').text($('textarea[name="comments_'+session_id+'"]').val());

					function checkedOrOne(field) {
						if($(field).prop('type') == 'checkbox') {
							return $(field).is(':checked');
						} else if($(field).prop('type') == 'hidden') {
							return ($(field).val() == 1);
						}
					}

					if (checkedOrOne($('#available_'+session_id))) {
						$('#session_unavailable_'+session_id).hide();
					}
					else {
						// because the unavailable reason might not be set in sessions before reasons was an option, we have to put the
						// seperator in as well.
						$('#session_unavailablereason_' + session_id).html(" - " + $('#unavailablereason_id_' + session_id).children(':selected').text());
						$('#session_unavailable_'+session_id).show();
						markSessionUnavailable = true;
					}
					checkedOrOne($('#consultant_'+session_id)) ? $('#consultant_icon_'+session_id).show() : $('#consultant_icon_'+session_id).hide();
					checkedOrOne($('#anaesthetist_'+session_id)) ? $('#anaesthetist_icon_'+session_id).show() : $('#anaesthetist_icon_'+session_id).hide();
					$('#anaesthetist_icon_'+session_id).html(checkedOrOne($('#general_anaesthetic_'+session_id)) ? 'Anaesthetist (GA)' : 'Anaesthetist');
					checkedOrOne($('#paediatric_'+session_id)) ? $('#paediatric_icon_'+session_id).show() : $('#paediatric_icon_'+session_id).hide();
					if ($('#max_procedures_'+session_id).val()) {
						var overbooked = 0;
						var max = $('#max_procedures_'+session_id).val();
						$('#max_procedures_icon_'+session_id).find('.max-procedures-val').html(max);
						$('#max_procedures_icon_'+session_id).show();
						var avail = max - $('#procedure_count_'+session_id).data('currproccount');
						if (avail <= 0) {
							overbooked = Math.abs(avail);
							avail = 0;
							markSessionUnavailable = true;
						}
						$('#procedure_count_'+session_id).find('.available-val').html(avail);
						$('#procedure_count_'+session_id).show();
						if (overbooked > 0) {
							$('#procedure_count_'+session_id+' .overbooked').find('.overbooked-proc-val').html(overbooked);
							$('#procedure_count_'+session_id+' .overbooked').show();
						}
						else {
							$('#procedure_count_'+session_id+' .overbooked').hide();
						}
					}
					else {
						$('#max_procedures_icon_'+session_id).hide();
						$('#procedure_count_'+session_id).hide();
					}

					const maxComplexBookings = $('#max_complex_bookings_'+session_id).val();
					const $complexBookingCount = $('#complex_booking_count_'+session_id);
					const $maxComplexBookingsIcon = $('#max_complex_bookings_icon_'+session_id);
					if (maxComplexBookings) {
						let overBookedComplexBookings = 0;
						$maxComplexBookingsIcon.find('.max-complex-bookings-value').html(maxComplexBookings);
						$maxComplexBookingsIcon.show();
						let availableComplexBookings = maxComplexBookings - $complexBookingCount.data('current-complex-booking-count');
						if (availableComplexBookings <= 0) {
							overBookedComplexBookings = Math.abs(availableComplexBookings);
							availableComplexBookings = 0;
						}
						$complexBookingCount.find('.available-complex-booking-count').html(availableComplexBookings);
						$complexBookingCount.show();
						const $complexBookingOverbookedMessage = $complexBookingCount.find('.overbooked');
						if (overBookedComplexBookings > 0) {
							$complexBookingOverbookedMessage.find('.overbooked-complex-booking-count').html(overBookedComplexBookings);
							$complexBookingOverbookedMessage.show();
						}
						else {
							$complexBookingOverbookedMessage.hide();
						}
					}
					else {
						$maxComplexBookingsIcon.hide();
						$complexBookingCount.hide();
					}

					if (markSessionUnavailable) {
						$('#tfoot_'+session_id).find('td').removeClass('available');
					}
					else if (parseInt($('#tfoot_'+session_id).find('td').data('minutes-available')) > 0) {
						$('#tfoot_'+session_id).find('td').addClass('available');
					}

					cancel_edit(true);
					$('#infoBox_'+session_id).show();

					enableButtons();
				}
			});
		}

		return false;
	});

	$(this).undelegate('button[id^="btn_edit_session_cancel_"]','click').delegate('button[id^="btn_edit_session_cancel_"]','click',function() {
		cancel_edit();
		return false;
	});

	new OpenEyes.UI.StickyElement('.actions', {
		offset: -44,
		enableHandler: function(instance) {
			instance.element.width(instance.element.width());
			instance.enable();
		},
		disableHandler: function(instance) {
			instance.element.width('auto');
			instance.disable();
		}
	});
});

function getDiary() {

	var button = $('#theatre-filter button[type="submit"]');
	var loadingMessage = $('#theatre-search-loading');
	var noResultsMessage = $('#theatre-search-no-results');
	var theatreList = $('#theatreList');

	if (!button.hasClass('inactive')) {
		disableButtons();

		theatreList.empty();
		loadingMessage.show();
		noResultsMessage.hide();

		searchData = $('#theatre-filter').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN;

		$.ajax({
			'url': baseUrl+'/OphTrOperationbooking/theatreDiary/search',
			'type': 'POST',
			'dataType': 'json',
			'data': searchData,
			'success': function(data) {
				if (data['status'] == 'success') {
					theatreList.html(data['data']);
				} else {
					theatreList.html('<div class="large-12 column"><div class="alert-box"><strong>'+data['message']+'</strong></div></div>');
				}
				enableButtons();
				return false;
			},
			complete: function() {
				loadingMessage.hide();
			}
		});
	}

	return false;
}

function setDiaryFilter(values) {
	var data = '';
	var load_theatres_and_wards = false;

	for (var i in values) {
		if (data.length >0) {
			data += "&";
		}
		data += i + "=" + values[i];

		var field = i;
		var value = values[i];
	}

	$.ajax({
		'url': baseUrl+'/OphTrOperationbooking/theatreDiary/setDiaryFilter',
		'type': 'POST',
		'data': data+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
		'success': function(html) {
			if (field == 'site-id') {
				loadTheatresAndWards(value);
			} else if (field == 'subspecialty-id') {
				$.ajax({
					'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterFirms',
					'type': 'POST',
					'data': 'subspecialty_id='+$('#subspecialty-id').val()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
					'success': function(data) {
						if ($('#subspecialty-id').val() != '') {
							$('#firm-id').attr('disabled', false);
							$('#firm-id').html(data);
						} else {
							$('#firm-id').attr('disabled', true);
							$('#firm-id').html(data);
						}
					}
				});
			}
		}
	});
}

function loadTheatresAndWards(siteId) {
	$.ajax({
		'type': 'POST',
		'data': {'site_id': siteId, 'YII_CSRF_TOKEN': YII_CSRF_TOKEN},
		'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterTheatres',
		'success':function(data) {
			$('#theatre-id').html(data);
			$.ajax({
				'type': 'POST',
				'data': {'site_id': siteId, 'YII_CSRF_TOKEN': YII_CSRF_TOKEN},
				'url': baseUrl+'/OphTrOperationbooking/theatreDiary/filterWards',
				'success':function(data) {
					$('#ward-id').html(data);
				}
			});
		}
	});
}

function clearBoundaries() {
	// Not sure how to change pickmeup boundaries after initialisation
}

function returnDateWithInterval(d, interval) {
	return new Date(d.getTime() + (86400000 * interval));
}

function theatreDiaryIconHovers() {
	var offsetY = 28;
	var offsetX = 10;
	var tipWidth = 0;

	$('.alerts img').hover(function(e){

		var img = $(this);
		var titleText = $(this).attr('title');
		var tooltip = $('<div class="tooltip alerts"></div>').appendTo('body');

		img.data({
			'tipText': titleText,
			'tooltip': tooltip
		});
		img.removeAttr('title');

		tooltip.text(' ' + titleText);

		$('<img />').attr({
				width:'17',
				height:'17',
				src:img.attr('src')
		}).prependTo(tooltip);

		tipWidth = tooltip.outerWidth();
		tooltip.css('top', (e.pageY - offsetY) + 'px').css('left', (e.pageX - (tipWidth + offsetX)) + 'px').fadeIn('fast');

	},function(e){
		$(this).attr('title',$(this).data('tipText'));
		$(this).data('tooltip').remove();
	}).mousemove(function(e) {
		$(this).data('tooltip')
			.css('top', (e.pageY - offsetY) + 'px')
			.css('left', (e.pageX - (tipWidth + offsetX)) + 'px');
	});
}

function printElem(method,options, callback){
	$.ajax({
		'url': baseUrl+'/OphTrOperationbooking/theatreDiary/'+method,
		'type': 'POST',
		'data': searchData+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
		'success': function(data) {
			$('#printable').html(data);
			$('#printable').printElement(options);
			if ($.isFunction(callback)) {
				callback();
			}
			return false;
		}
	});
}

function cancel_edit(dont_reset_checkboxes) {
	enableButtons();
	if (!dont_reset_checkboxes && theatre_edit_session_id != null) {
		for (var i in theatre_edit_session_data.purple_rinse) {
			$('#'+i+'_'+theatre_edit_session_id).attr('checked',(theatre_edit_session_data.purple_rinse[i] ? 'checked' : false));
		}
	}

	if (theatre_edit_session_data) {
		if (!dont_reset_checkboxes) {
			var rows = '';

			for (var i in theatre_edit_session_data["row_order"]) {
				rows += '<tr id="'+theatre_edit_session_data["row_order"][i]+'">'+$('#'+theatre_edit_session_data["row_order"][i]).html()+'</tr>';
			}

			$('#tbody_'+theatre_edit_session_id).html(rows);

			for (var i in theatre_edit_session_data["row_order"]) {
				var id = theatre_edit_session_data["row_order"][i].match(/[0-9]+/);

				$('#confirm_'+id).attr('checked',(theatre_edit_session_data["confirm"][id] ? 'checked' : false));
			}

			$('textarea[name="comments_'+theatre_edit_session_id+'"]').val(theatre_edit_session_data['comments']);

		} else {
			for (var i in theatre_edit_session_data["row_order"]) {
				var id = theatre_edit_session_data["row_order"][i].match(/[0-9]+/);
				theatre_edit_session_data["confirm"][id] = $('#confirm_'+id).is(':checked');
			}
		}
	}

	$('.diaryViewMode').show();
	$('.diaryEditMode').hide();
	$('.infoBox').hide();
	$('tbody[id="tbody_'+theatre_edit_session_id+'"] td.confirm input[name^="confirm_"]').attr('disabled','disabled');
	$('th.footer').attr('colspan','9');

	theatre_edit_session_id = null;
}

var theatre_edit_session_id = null;
var theatre_edit_session_data = null;

function checkRequired(type, session_id) {
	$.ajax({
		type: "POST",
		data: 'type='+type+'&session_id='+session_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
		url: baseUrl+'/OphTrOperationbooking/theatreDiary/checkRequired',
		success: function(html) {
			if (html == "1") {
				$('#'+type+'_'+session_id).attr('checked',true);
				switch (type) {
					case 'consultant':
						new OpenEyes.UI.Dialog.Alert({
							content: "Sorry, you cannot remove the 'Consultant required' flag from this session because there are one or more patients booked into it who require a consultant."
						}).open();
						break;
					case 'paediatric':
						new OpenEyes.UI.Dialog.Alert({
							content: "Sorry, you cannot remove the 'Paediatric' flag from this session because there are one or more patients booked into it who are paediatric."
						}).open();
						break;
					case 'anaesthetist':
						new OpenEyes.UI.Dialog.Alert({
							content: "Sorry, you cannot remove the 'Anaesthetist required' flag from this session because there are one or more patients booked into it who require an anaesthetist."
						}).open();
						break;
					case 'general_anaesthetic':
						new OpenEyes.UI.Dialog.Alert({
							content: "Sorry, you cannot remove the 'General anaesthetic available' flag from this session because there are one or more patients booked into it who require a general anaesthetic."
						}).open();
						break;
				}

				return false;
			}
		}
	});
}
