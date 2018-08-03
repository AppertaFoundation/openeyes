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
	$(this).on('click','button.btn_transport_viewall',function(e) {
		$('#transport_date_from').val('');
		$('#transport_date_to').val('');
		$('#include_bookings').attr('checked','checked');
		$('#include_reschedules').attr('checked','checked');
		$('#include_cancellations').attr('checked','checked');
		$('#include_completed').attr('checked','checked');
		transport_load_tcis();
		e.preventDefault();
	});

  $(this).on('click','button.btn_transport_filter',function(e) {
		transport_load_tcis();
		e.preventDefault();
	});

  $(this).on('click','button.btn_transport_confirm',function(e) {
		$.ajax({
			type: "POST",
			url: baseUrl+"/OphTrOperationbooking/transport/confirm",
			data: $('input[name^="operations"]:checked').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			success: function(html) {
				if (html == "1") {
					$('input[name^="operations"]:checked').map(function() {
						$(this).parent().parent().attr('class','waitinglistGrey');
						$(this).attr('checked',false);
					});
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "Something went wrong trying to confirm the transport item.\n\nPlease try again or contact OpenEyes support."
					}).open();
				}
				enableButtons();
			}
		});
		e.preventDefault();
	});

  $(this).on('click','button.btn_transport_print',function(e) {
		var get = '';

		url = baseUrl+"/OphTrOperationbooking/transport/printList?";
		get = "page="+($(document).getUrlParam('page') || 1);
		if ($('#transport_date_from').val().length >0 && $('#transport_date_to').val().length >0) {
			get += "&date_from="+$('#transport_date_from').val()+"&date_to="+$('#transport_date_to').val();
		}

		if (!$('#include_bookings').is(':checked')) get += "&include_bookings=0";
		if (!$('#include_reschedules').is(':checked')) get += "&include_reschedules=0";
		if (!$('#include_cancellations').is(':checked')) get += "&include_cancellations=0";
		if ($('#include_completed').is(':checked')) get += "&include_completed=1";

		url += get;

		printIFrameUrl(url,null);
		setTimeout('enableButtons();',3000);
		e.preventDefault();
	});

	$('button.btn_transport_download').click(function(e) {
		e.preventDefault();

		$('#csvform input[name="date_from"]').val($('#transport_date_from').val());
		$('#csvform input[name="date_to"]').val($('#transport_date_to').val());
		$('#csvform input[name="include_bookings"]').val($('#include_bookings').is(':checked') ? 1 : 0);
		$('#csvform input[name="include_reschedules"]').val($('#include_reschedules').is(':checked') ? 1 : 0);
		$('#csvform input[name="include_cancellations"]').val($('#include_cancellations').is(':checked') ? 1 : 0);
		$('#csvform input[name="include_completed"]').val($('#include_completed').is(':checked') ? 1 : 0);
		$('#csvform').submit();
	});

	$('#transport_checkall').die('click').live('click',function() {
		$('input[name^="operations"]').attr('checked',$('#transport_checkall').is(':checked') ? 'checked' : false);
	});

	$('#transport_date_from').bind('change',function() {
		$('#transport_date_to').datepicker('option','minDate',$('#transport_date_from').datepicker('getDate'));
	});

	$('#transport_date_to').bind('change',function() {
		$('#transport_date_from').datepicker('option','maxDate',$('#transport_date_to').datepicker('getDate'));
	});
});

function initAjaxPagination() {
	$('.pagination').on('click', 'a', function(e) {
		e.preventDefault();
		var url = this.href.replace('/index', '/tcis');
		transport_load_tcis(url);
	});
}

function transport_load_tcis(url) {

	$('#transportList tbody').html('<tr><td colspan="12"><img src="'+baseUrl+OE_core_asset_path+'/img/ajax-loader.gif" class="loader" /> loading data ...</td></tr>');
	$('#transportList tfoot').hide();

	var get = '';

	if (!url) {
		url = baseUrl+"/OphTrOperationbooking/transport/tcis?";
		get = "page="+($(document).getUrlParam('page') || 1);
		if ($('#transport_date_from').val().length >0 && $('#transport_date_to').val().length >0) {
			get += "&date_from="+$('#transport_date_from').val()+"&date_to="+$('#transport_date_to').val();
		}
	}

	if (!$('#include_bookings').is(':checked')) get += "&include_bookings=0";
	if (!$('#include_reschedules').is(':checked')) get += "&include_reschedules=0";
	if (!$('#include_cancellations').is(':checked')) get += "&include_cancellations=0";
	if ($('#include_completed').is(':checked')) get += "&include_completed=1";

	url += get;

	$.ajax({
		type: "GET",
		url: url,
		success: function(html) {
			$('#transport_data').html(html);
			enableButtons();
			initAjaxPagination();
		}
	});
}
