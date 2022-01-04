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
* @copyright Copyright (c) 2021, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/
class DateTime {
	constructor(date = null) {

		if (!(date instanceof Date)) {
			date = new Date();
		}

		// clone
		this.originalData = new Date(date.getTime());

		// set internal date
		this.date = date;
	}
	// Getter
	get object() {
		return this.date;
	}

	/**
	 * Returns the display date
	 *
	 * @param date
	 * @returns {string}
	 */
	getDisplayDate(date = null) {
		const year = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(date ? date : this.date);
		const month = new Intl.DateTimeFormat('en', { month: 'short' }).format(date ? date : this.date);
		const day = new Intl.DateTimeFormat('en', { day: 'numeric' }).format(date ? date : this.date);

		return `${day} ${month} ${year}`;
	}

	/**
	 * Calculates the relative date/date ranges from different expressions like tomorrow/last-week etc
	 * @param relative
	 */
	getRelativeDateRange(relative, dateObject = null) {
		let dateFrom = new Date(dateObject instanceof Date ? dateObject.getTime() : this.date.getTime());
		let dateTo = new Date(dateObject instanceof Date ? dateObject.getTime() : this.date.getTime());
		const lastWeekDay = new Date(dateFrom.getFullYear(), dateFrom.getMonth(), dateFrom.getDate() - 7);
		const nextWeekDay = new Date(dateFrom.getFullYear(), dateFrom.getMonth(), dateFrom.getDate() + 7);

		switch (relative) {
			case "yesterday":
				dateFrom = (d => new Date(d.setDate(d.getDate() - 1)))(dateFrom);
				dateTo = (d => new Date(d.setDate(d.getDate() - 1)))(dateTo);
				break;
			case "today":
				break;
			case "tomorrow":
				dateFrom = (d => new Date(d.setDate(d.getDate() + 1)))(dateFrom);
				dateTo = (d => new Date(d.setDate(d.getDate() + 1)))(dateTo);
				break;
			case "last-week":
				const t = new Date().getDate() + (6 - new Date().getDay() - 1) - 7;
                dateFrom.setFullYear(lastWeekDay.getFullYear(), lastWeekDay.getMonth(), lastWeekDay.getDate() - ((lastWeekDay.getDay() + 6) % 7));
                dateTo.setDate(t);
				break;
			case "this-week":
				const mod = dateFrom.getDate() - (dateFrom.getDay() + 6) % 7;
				dateFrom.setDate(mod);
				dateTo.setDate(mod + 4);
				break;
			case "next-week":
				const d = new Date();
				dateFrom.setDate(d.getDate() + (1 + 7 - d.getDay()) % 7);
				dateTo.setDate(nextWeekDay.getDate() + (5 + 7 - nextWeekDay.getDay()) % 7);
				break;
			case "last-month":
				dateFrom = new Date(dateFrom.getFullYear(), dateFrom.getMonth() - 1, 1);
				dateTo.setDate(0); // set to last day of previous month
				break;
			case "this-month":
				dateFrom = new Date(dateFrom.getFullYear(), dateFrom.getMonth(), 1);
				dateTo = new Date(dateTo.getFullYear(), dateTo.getMonth() + 1, 0);
				break;
			case "next-month":
				dateFrom = new Date(dateFrom.getFullYear(), dateFrom.getMonth() + 1, 1);
				dateTo = new Date(dateTo.getFullYear(), dateTo.getMonth() + 2, 0);
				break;
			case "+4days":
				dateTo.setDate(dateFrom.getDate() + 4);
				break;
			case "+7days":
				dateTo.setDate(dateFrom.getDate() + 7);
				break;
			case "+12days":
				dateTo.setDate(dateFrom.getDate() + 12);
				break;

			default:
			// code block
		}

		return {
			from: dateFrom,
			to: dateTo
		};
	}
}

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

	const $ranges = document.getElementById('theatre-diaries-date-ranges');
	$ranges.addEventListener('click', function(e) {
		// loop parent nodes from the target to the delegation node
		for (let target = e.target; target && target !== this; target = target.parentNode) {
			if (target.matches('.btn')) {
				const $from = document.querySelector('.js-filter-date-from');
				const $to = document.querySelector('.js-filter-date-to');

				const dateTime = new DateTime();
				const dates = dateTime.getRelativeDateRange(target.dataset.range);

				// populate the visible input fields
				$from.value = dateTime.getDisplayDate(dates.from);
				$to.value = dateTime.getDisplayDate(dates.to);

				const dateFrom =  format_pickmeup_date(dates.from);
				const dateTo = format_pickmeup_date(dates.to);

				setDiaryFilter({'date-filter': target.dataset.range, 'date-start': dateFrom,'date-end':dateTo});
				break;
			}
		}
		return true;
	}, false);


	/*$('#date-filter_0').click(function() {
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

		setDiaryFilter({'date-filter':'','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});

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

		setDiaryFilter({'date-filter':'','date-start':$('#date-start').val(),'date-end':$('#date-end').val()});

		return false;
	});

	 */

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

	$('#date-start').on('pickmeup-change change', function() {
		setDiaryFilter({'date-start':$(this).val()});
		$('#date-filter_3').attr('checked','checked');
	});

	$('#date-end').on('pickmeup-change change', function() {
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

	function editList(e) {
		e.preventDefault();

		cancel_edit();
		const $form = this.closest('form');
		const $table = $form.querySelector('table.theatre-bookings');

		disableButtons($('button,.button').not('.theatre'));
		$('.spinner').hide();
		theatre_edit_session_id = $form.getAttribute('id');

		theatre_edit_session_data = {};

		// what is purpleUser ??
		// if ($('div.purpleUser').length >0) {
		// 	theatre_edit_session_data["purple_rinse"] = {
		// 		"consultant": $('#consultant_'+theatre_edit_session_id).is(':checked'),
		// 		"paediatric": $('#paediatric_'+theatre_edit_session_id).is(':checked'),
		// 		"anaesthetist": $('#anaesthetist_'+theatre_edit_session_id).is(':checked'),
		// 		"general_anaesthetic": $('#general_anaesthetic_'+theatre_edit_session_id).is(':checked'),
		// 		"available": $('#available_'+theatre_edit_session_id).is(':checked')
		// 	};
		// }

		theatre_edit_session_data.row_order = [];
		theatre_edit_session_data.confirm = {};
		theatre_edit_session_data.comments = $table.querySelector('.js-comments-edit').innerText;

		$table.querySelectorAll('tbody tr').forEach(($tr) => {
			const id = $tr.getAttribute('id');
			theatre_edit_session_data.row_order.push(id);
			const $confirmed = $tr.querySelector('.js-confirmed');
			theatre_edit_session_data.confirm[id] = $confirmed ? $confirmed.checked : false;
		});

		// buttons
		$form.querySelector('.js-update-session').style.display = 'inline-flex';
		$form.querySelector('.js-cancel-update').style.display = 'inline-flex';
		$form.querySelector('.js-edit-session').style.display = 'none';

		$('.js-diaryViewMode').hide();

		// hide session features and show checkboxes
		Array.from($form.querySelector('.js-session-features').children).forEach($el => {
			$el.style.display = $el.tagName === 'LI' ? 'none' : 'inline-flex';
		});

		$form.querySelectorAll('.js-diaryEditMode').forEach(($el) => {
			$el.style.display = 'inline-flex';
		});

		// now lets handle "session unavailable" part differently ofc
		$form.querySelector('.session-unavailable').style.display = 'block';

		// max patients and max compex procedures
		$form.querySelectorAll('.max-limit.js-diaryEditMode').forEach($el => {
			$el.style.display = 'inline-block';
		});

		$($table.querySelector('tbody')).sortable({
			 helper: function(e, tr) {
				 var $originals = tr.children();
				 var $helper = tr.clone();
				 $helper.children().each(function(index) {
					 $(this).width($originals.eq(index).outerWidth());
				 });
				 return $helper;
			 },
			 placeholder: 'theatre-list-sort-placeholder'
		}).disableSelection();
		$("#theatre_list tbody").sortable('enable');

		$('tbody[id="tbody_'+theatre_edit_session_id+'"] input[name^="confirm_"]').attr('disabled',false);

		return false;
	}

	const $theatreList = document.getElementById('theatreList');

	$theatreList.addEventListener('click', function(e) {
		// loop parent nodes from the target to the delegation node
		for (let target = e.target; target && target !== this; target = target.parentNode) {
			if (target.matches('.js-edit-session')) {
				editList.call(target, e);
				break;
			}
		}
	}, false);

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

	$('#theatreList').on('click', '.session-available', function() {
		const $form = $(this).closest('form');
		$form.find('.unavailable-reasons').toggle(!!$(this).is(':checked'));

		$form.find('.js-hidden-available').attr('disabled', $(this).is(':checked'));
		$form.find('.js-hidden-unavailable').attr('disabled', !$(this).is(':checked'));
	});

	$(document).on('click', '.js-update-session' ,function() {

		const $form = $(this).closest('form');

		if (!$(this).hasClass('inactive')) {
			disableButtons();
			$('.diaries-search .spinner').hide();

			const session_id = $form.attr('id');

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
				data: $form.serialize()+"&session_id="+session_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				dataType: 'json',
				url: baseUrl+'/OphTrOperationbooking/theatreDiary/saveSession',
				success: function(errors) {
					var opErrs = null;
					var nonOpErrs = '';
					const $tbody = $(`tbody_${session_id}`);
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
							nonOpErrs += "<li>" + opErrs + "</li>";
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

					$($tbody).find('div.op-time').map(function() {
						const time = $(this).parent().find('input[name^="admitTime"]').val();
						$(this).text(time);
					});

					$.each($('.session-comments'), function(i, $div) {
						const comment = $($div).find('textarea').val();
						const $span = $($div).find('span');
						$($div).find('i').remove();
						const $icon = '<i class="oe-i comments-who small pad-right js-has-tooltip" data-tt-type="basic" data-tooltip-content="User comment"> </i>';
						$span.html(comment ? ($icon + comment) : '<span class="user-comment fade">No session list comments</span>');
						if (comment) {
							$span.removeClass('fade');
						} else {
							$span.addClass('fade');
						}
					});

					function checkedOrOne(field) {
						if($(field).prop('type') === 'checkbox') {
							return $(field).is(':checked');
						} else if($(field).prop('type') === 'hidden') {
							return ($(field).val() == 1);
						}
					}

					if ($form.find(`input[name="available_${session_id}"]:enabled`).val() === "1") {
						const $minutes = $form.find('.minutes');
						$('#session_unavailable_'+session_id).hide();
						$form.find('.theatre-overview').removeClass('full').addClass('available');
						$minutes.text(`${$minutes.data('available-minutes')} mins available`);
						$form.find('.session-unavailable-reason').hide();
						$form.find('.js-session-features').show();
					}
					else {
						// because the unavailable reason might not be set in sessions before reasons was an option, we have to put the
						// seperator in as well.
						$('#session_unavailablereason_' + session_id).html(" - " + $('#unavailablereason_id_' + session_id).children(':selected').text());
						$('#session_unavailable_'+session_id).show();
						markSessionUnavailable = true;

						$('.theatre-overview').removeClass('available').addClass('full');
						$form.find('.minutes').text('Session unavailable');
						$form.find('.session-unavailable-reason').text($(`#unavailablereason_id_${session_id} option:selected`).text()).show();
						$form.find('.js-session-features').hide();
					}

					$('tbody[id="tbody_'+session_id+'"] input[name^="confirm_"]').attr('disabled','disabled');

					checkedOrOne($('#consultant_'+session_id)) ? $('#consultant_icon_'+session_id).show() : $('#consultant_icon_'+session_id).hide();
					checkedOrOne($('#anaesthetist_'+session_id)) ? $('#anaesthetist_icon_'+session_id).show() : $('#anaesthetist_icon_'+session_id).hide();
					checkedOrOne($('#general_anaesthetic_'+session_id)) ? $('#general_anaesthetic_icon_'+session_id).show() : $('#general_anaesthetic_icon_'+session_id).hide();
					checkedOrOne($('#paediatric_'+session_id)) ? $('#paediatric_icon_'+session_id).show() : $('#paediatric_icon_'+session_id).hide();

					const $inputFieldForMaxProcedures = $('#max_procedures_'+session_id);
					const maxProcedures = $inputFieldForMaxProcedures.val();

					if (maxProcedures) {
						const $maxProcVal = $form.find('.js-max-procedures-val');
						let overbooked = 0;
						$maxProcVal.html(`Max ${maxProcedures} patients`).show();
						let availableProcedureCount = maxProcedures - $maxProcVal.data('current-procedure-count');

						if (availableProcedureCount <= 0) {
							overbooked = Math.abs(availableProcedureCount);
							availableProcedureCount = 0;
							markSessionUnavailable = true;
						}

						if (overbooked > 0) {
							const $msg = `<span class="complex-bookings-num highlighter warning">${overbooked}</span>`;
							$form.find(".overbooked.js-max-patients").html(`Overbooked by ${$msg}`);

						}
						else {
							const $html = `<span class="bookings-num">${availableProcedureCount}</span> available`;
							$form.find(".overbooked.js-max-patients").html($html);
						}
					}

					const $inputFieldForMaxComplexBookings = $('#max_complex_bookings_'+session_id);
					const maxComplexBookings = $inputFieldForMaxComplexBookings.val();

					if (maxComplexBookings) {
						const $maxComplecBookingVal = $form.find('.js-max-complex-bookings-value');
						let overBookedComplexBookings = 0;
						$maxComplecBookingVal.html(`Max ${maxComplexBookings} complex bookings`).show();
						let availableComplexBookingCount = maxComplexBookings - $maxComplecBookingVal.data('current-max-complex-count');

						if (availableComplexBookingCount <= 0) {
							overBookedComplexBookings = Math.abs(availableComplexBookingCount);
						}

						if (overBookedComplexBookings > 0) {
							const $msg = `<span class="complex-bookings-num highlighter warning">${overBookedComplexBookings}</span>`;
							$form.find(".overbooked.js-max-patients").html(`Overbooked by ${$msg}`);

						}
						else {
							const $html = `<span class="bookings-num">${availableComplexBookingCount}</span> available`;
							$form.find(".overbooked.js-max-complex-booking").html($html);
						}
					}

					cancel_edit(true);
					$('#infoBox_'+session_id).show();

					enableButtons();

					// Update session times
					const inputs = $form[0].querySelectorAll('.js-admit-time');
					if (inputs) {
						inputs.forEach(input => {
							if (input.value) {
								input.previousElementSibling.innerHTML = input.value;
							}
						});
					}
				}
			});
		}

		return false;
	});

	$(document).on('click', '.js-cancel-update', function(e) {
		e.preventDefault();
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

	// 'a-A-b-B-C-d-e-H-I-j-k-l-m-M-p-P-s-S-u-w-y-Y'
	// Thu-Thursday-Dec-December-21-12-12-00-12-346-0-12-12-00-AM-AM-1576108800-00-5-4-19-2019
	// Mon-Monday-Dec-  December-21-02-2-00-12-336-0-12-12-00-AM-AM-1575244800-00-2-1-19-2019
	pickmeup('#date-start', {
		format: 'e b Y',
		hide_on_select: true,
		date: $('#date-start').val()
	});
	pickmeup('#date-end', {
		format: 'e b Y',
		hide_on_select: true,
		date: $('#date-end').val()
	});

	return getDiary();
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
	
	const $tbody = $(`#tbody_${theatre_edit_session_id}`);
	const $form = $tbody.closest('form');

	enableButtons();
	// what is purple_rinse ?
	// if (!dont_reset_checkboxes && theatre_edit_session_id != null) {
	// 	for (let i in theatre_edit_session_data.purple_rinse) {
	// 		$('#'+i+'_'+theatre_edit_session_id).attr('checked',(theatre_edit_session_data.purple_rinse[i] ? 'checked' : false));
	// 	}
	// }

	if (theatre_edit_session_data) {
		if (!dont_reset_checkboxes) {
			let rows = '';

			for (let id in theatre_edit_session_data.row_order) {
				rows += '<tr id="'+theatre_edit_session_data.row_order[id]+'">'+$('#'+theatre_edit_session_data.row_order[id]).html()+'</tr>';
			}

			$tbody.html(rows);

			for (let id in theatre_edit_session_data.row_order) {
				$('#confirm_'+id).attr('checked',(theatre_edit_session_data.confirm[id] ? 'checked' : false));
			}

			$('textarea[name="comments_'+theatre_edit_session_id+'"]').val(theatre_edit_session_data.comments);

		} else {
			for (let id in theatre_edit_session_data.row_order) {
				theatre_edit_session_data.confirm[id] = $('#confirm_'+id).is(':checked');
			}
		}
	}

	$tbody.sortable( "destroy" );

	// buttons
	$('.js-update-session, .js-cancel-update').hide();
	$('.js-edit-session').show();

	$('.js-diaryViewMode').show();
	$('.js-diaryEditMode').hide();

	// oh yeah the "session unavailable" part
	$('.session-unavailable').hide();

	$('.infoBox').hide();
	$('tbody[id="tbody_'+theatre_edit_session_id+'"] input[name^="confirm_"]').attr('disabled','disabled');

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
