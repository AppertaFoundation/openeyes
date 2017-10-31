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

	var searchLoadingMsg = $('#search-loading-msg');
	var searchResults = $('#searchResults');

	handleButton($('#waitingList-filter button[type="submit"]'),function(e) {
		e.preventDefault();

		if (!validateHosNum()) {
			//using timeout to mirror the chrome fix in buttons.js for disableButtons()
			setTimeout(function() {
				enableButtons();
			});
			return;
		}

		searchLoadingMsg.show();
		searchResults.empty();

		$.ajax({
			'url': baseUrl+'/OphTrOperationbooking/waitingList/search',
			'type': 'POST',
			'data': $('#waitingList-filter').serialize(),
			'success': function(data) {
				searchResults.html(data);
			},
			complete: function() {
				searchLoadingMsg.hide();
				enableButtons();
			}
		});
	});

	handleButton($('#btn_print'),function() {
		print_items_from_selector('input[id^="operation"]:checked',false);
		enableButtons();
	});

	handleButton($('#btn_print_all'),function() {
		print_items_from_selector('input[id^="operation"]:enabled',true);
		enableButtons();
	});

	handleButton($('#btn_confirm_selected'),function(e) {
		var data = '';
		var operations = 0;
		data += "adminconfirmto=" + $('#adminconfirmto').val() + "&adminconfirmdate=" + $('#adminconfirmdate').val();
		$('input[id^="operation"]:checked').map(function() {
			if (data.length >0) {
				data += '&';
			}
			data += "operations[]=" + $(this).attr('id').replace(/operation/,'');
			operations += 1;
		});

		if (operations == 0) {
			new OpenEyes.UI.Dialog.Alert({
				content: 'No items selected.',
				onClose: function() {
					enableButtons();
				}
			}).open();
		} else {
			disableButtons();

			data += '&YII_CSRF_TOKEN='+YII_CSRF_TOKEN;

			$.ajax({
				url: baseUrl+'/OphTrOperationbooking/waitingList/confirmPrinted',
				type: "POST",
				data: data,
				success: function(html) {
					enableButtons();
					$('#waitingList-filter button[type="submit"]').click();
				}
			});
		}

		e.preventDefault();
	});

	$('#hos_num').focus();

	if ($('#subspecialty-id').length) {
		if ($('#subspecialty-id').val() != '') {
			var firm_id = $('#firm-id').val();
			$.ajax({
				url: baseUrl+'/OphTrOperationbooking/waitingList/filterFirms',
				type: "POST",
				data: "subspecialty_id="+$('#subspecialty-id').val()+'&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
				success: function(data) {
					$('#firm-id').attr('disabled', false);
					$('#firm-id').html(data);
					$('#firm-id').val(firm_id);
					$('#waitingList-filter button[type="submit"]').click();
				}
			});
		} else {
			$('#waitingList-filter button[type="submit"]').click();
		}
	}

	$('#firm-id').bind('change',function() {
		$.ajax({
			url: baseUrl+'/OphTrOperationbooking/waitingList/filterSetFirm',
			type: "POST",
			data: "firm_id="+$('#firm-id').val()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			success: function(data) {
			}
		});
	});

	$('#status').bind('change',function() {
		$.ajax({
			url: baseUrl+'/OphTrOperationbooking/waitingList/filterSetStatus',
			type: "POST",
			data: "status="+$('#status').val()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			success: function(data) {
			}
		});
	});

	$('#site_id').bind('change',function() {
		$.ajax({
			url: baseUrl+'/OphTrOperationbooking/waitingList/filterSetSiteId',
			type: "POST",
			data: "site_id="+$('#site_id').val()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			success: function(data) {
			}
		});
	});

	$('#hos_num').bind('keyup',function(e) {
		if (validateHosNum()) {
			$.ajax({
				url: baseUrl+'/OphTrOperationbooking/waitingList/filterSetHosNum',
				type: "POST",
				data: "hos_num="+$('#hos_num').val()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				success: function(data) {
				}
			});
		}
	});

	new OpenEyes.UI.StickyElement('.panel.actions', {
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

function print_items_from_selector(sel,all) {
	var operations = new Array();

	var nogp = 0;

	var operations = $(sel).map(function(i,n) {
		var no_gp = $(n).parent().parent().hasClass('waitinglistOrange') && $(n).parent().html().match(/>NO GP</)

		if (no_gp) nogp += 1;

		if (!no_gp) {
			return $(n).attr('id').replace(/operation/,'');
		}
	}).get();

	if (operations.length == 0) {
		if (nogp == 0) {
			new OpenEyes.UI.Dialog.Alert({
				content: "No items selected for printing."
			}).open();
		} else {
			show_letter_warnings(nogp);
		}
	} else {
		show_letter_warnings(nogp);
		printIFrameUrl(baseUrl+'/OphTrOperationbooking/waitingList/printLetters', {'operations': operations, 'all': all});
	}
}

function show_letter_warnings(nogp) {
	var msg = '';

	if (nogp >0) {
		msg += nogp+" item"+(nogp == 1 ? '' : 's')+" could not be printed as the patient has no GP practice.";
	}

	if (msg.length >0) {
		new OpenEyes.UI.Dialog.Alert({
			content: msg
		}).open();
	}
}
