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

/**
 * Close the dialog window
 */
function hide_dialog() {
    $('#blackout-box').hide();
    $('#dialog-msg').hide();
}

/**
 * Cancel booking, and redirect to cancel url
 */
function cancelBookingEvent() {
    window.location.href = cancel_url;
}

/**
 * Cancel booking, and redirect to examination event
 */
function goToExaminationEvent() {
    window.location.href = create_examination_url;
}

/**
 * Open alert dialog, if examination event is missing
 */
function AlertDialogIfExaminationEventIsMissing(){
    if(typeof examination_events_count == 'undefined' || typeof require_exam_before_booking == 'undefined'){
        return true;
    }
    
	if(examination_events_count < 1 && require_exam_before_booking) {
        var warning_message = "You didn't create examination yet.";
        
        var p = $('#event-content');
        var position = p.position();
        // alert ('L->'+position.left+ ' T '+position.top);
        var topdist = position.left + 400;
        var leftdist = position.top + 500;
        
        

        var dialog_msg = '<div class="ui-dialog ui-widget ui-widget-content ui-corner-all dialog" id="dialog-msg" tabindex="-1" role="dialog" aria-labelledby="ui-id-1" style="outline: 0px; z-index: 10002; height: auto; width: 600px;  position: fixed; top: 50%; left: 50%; margin-top: -110px; margin-left: -200px;">' +
          '<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">' +
          '<span id="ui-id-1" class="ui-dialog-title">Confirm booking</span>' +
          '</div><div id="site-and-firm-dialog" class="ui-dialog-content ui-widget-content" scrolltop="0" scrollleft="0" style="display: block; width: auto; min-height: 0px; height: auto;">' +
          '<div class="alert-box alert with-icon"> <strong>WARNING: ' + warning_message + ' </strong></div>' +
          '<p>For the purposes of providing data to the RCOphth National Ophthalmology Dataset (NOD), an Examination event with pre-surgery details must be created before a patient can be listed for surgery.</p>' +
          //'<p>Do you want to continue without examination?</p>' +
          '<div style = "margin-top:20px; float:right">' +
          //'<input class="secondary small" id="operationbooking-yes" type="submit" name="yt0" style = "margin-right:10px" value="Yes" onclick="hide_dialog();;">' +
          '<input class=" cancel event-action button small" id="operationbooking-cancel" type="button" name="yt1" style = "margin-right:10px" value="Cancel" onclick="cancelBookingEvent();">' +
          '<input class="event-action small" id="operationbooking-create-_examination" type="submit" name="yt2" value="Create Examination Event" onclick="goToExaminationEvent()">' +
          '</div>';

        var blackout_box = '<div id="blackout-box" style="position:fixed;top:0;left:0;width:100%;height:100%;background-color:black;opacity:0.6;z-index:10000">';


        $(dialog_msg).prependTo("body");
        $(blackout_box).prependTo("body");
        $('div#blackout_box').css.opacity = 0.6;

        $(document).keyup(function (e) {
            var keyCode = (event.keyCode ? event.keyCode : event.which);   
            if(keyCode == 13){
                e.preventDefault();
                $("input#operationbooking-create-_examination").trigger('click');
            }
        });
        
        $(document).keyup(function (e) {
            var keyCode = (event.keyCode ? event.keyCode : event.which);   
            if(keyCode == 27){
                e.preventDefault();
                $("input#operationbooking-cancel").trigger('click');
            }
        });
    }    
}


$(document).ready(function() {
	handleButton($('#et_schedulelater'),function() {
		$('#schedule_now').val(0);
	});

	handleButton($('#et_save_and_schedule'),function() {
		$('#schedule_now').val(1);
	});

	handleButton($('#et_cancel'),function(e) {
		if (m = window.location.href.match(/\/update\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/update/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/episodes/'+OE_patient_id;
		}
		e.preventDefault();
	});

	handleButton($('#et_deleteevent'));

	handleButton($('#btn_reschedule-now'));

	handleButton($('#btn_cancel-operation'));

	handleButton($('#et_canceldelete'));

	$(this).delegate('.addUnavailable', 'click', function(e) {
		OphTrOperationbooking_PatientUnavailable_add();
		e.preventDefault();
	});

	$('select.populate_textarea').unbind('change').change(function() {
		if ($(this).val() != '') {
			var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
			var el = $('#'+cLass+'_'+$(this).attr('id'));
			var currentText = el.text();
			var newText = $(this).children('option:selected').text();

			if (currentText.length == 0) {
				el.text(ucfirst(newText));
			} else {
				el.text(currentText+', '+newText);
			}
		}
	});

	handleButton($('#cancel'),function(e) {
		e.preventDefault();

		$.ajax({
			type: 'POST',
			url: window.location.href,
			data: $('#cancelForm').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			dataType: 'json',
			success: function(data) {
				var n=0;
				var html = '';
				$.each(data, function(key, value) {
					html += '<ul><li>'+value+'</li></ul>';
					n += 1;
				});

				if (n == 0) {
					$(window).off('beforeunload');
					window.location.href = window.location.href.replace(/\/cancel\//,'/view/');
				} else {
					$('#cancelForm .alert-box').show();
					$('#cancelForm .alert-box').html(html);
					enableButtons();
				}


			}
		});
	});

	$('#calendar table td').click(function() {
		var search = {'day' : $(this).text().match(/[0-9]+/)};
		if (search.day == null) return false;
		if ($('#Element_OphTrOperationbooking_Operation_referral_id').length) {
			search['referral_id'] = $('#Element_OphTrOperationbooking_Operation_referral_id').val();
		}
		if (!$(this).hasClass('patient-unavailable')) {
			$(window).off('beforeunload');
			window.location.href = URI(window.location.href).setSearch(search).removeSearch('session_id');
		}
		return false;
	});

	$('button#cancel_scheduling').on('click', function(e) {
		document.location.href = baseUrl + '/OphTrOperationbooking/default/view/' + OE_event_id;
		e.preventDefault();
	});

	handleButton($('#bookingForm button#confirm_slot'),function() {
		$('#bookingForm').submit();
	});

	$(this).undelegate('#Element_OphTrOperationbooking_Operation_referral_id', 'change').delegate('#Element_OphTrOperationbooking_Operation_referral_id', 'change', function() {
		// maintain the POST value if the drop down is altered.
		if ($('#Operation_referral_id').length) {
			$('#Operation_referral_id').val($(this).val());
		}
	});

	$(this).undelegate('#firmSelect #firm_id','change').delegate('#firmSelect #firm_id','change',function() {
		var search = {'firm_id': $(this).val()};
		if ($('#Element_OphTrOperationbooking_Operation_referral_id').length) {
			search['referral_id'] = $('#Element_OphTrOperationbooking_Operation_referral_id').val();
		}
		$(window).off('beforeunload');
		window.location.href = URI(window.location.href).setSearch(search).removeSearch(['session_id', 'day']);
	});

	handleButton($('#btn_print-letter'),function() {
		var m = window.location.href.match(/\/view\/([0-9]+)$/);
		printIFrameUrl(baseUrl+'/OphTrOperationbooking/waitingList/printLetters',{'event_id': m[1]});
	});

	handleButton($('#btn_print-admissionletter'),function() {
		var m = window.location.href.match(/\/view\/([0-9]+)$/);
		printIFrameUrl(baseUrl+'/OphTrOperationbooking/default/admissionLetter/'+m[1]);
	});

	$('input[name="Element_OphTrOperationbooking_Diagnosis[eye_id]"]').change(function() {
		switch (parseInt($(this).val())) {
			case 2:
				$('#Element_OphTrOperationbooking_Operation_eye_id_2').click();
				break;
			case 1:
				$('#Element_OphTrOperationbooking_Operation_eye_id_1').click();
				break;
			case 3:
				if (!$('#Element_OphTrOperationbooking_Operation_eye_id_3').is(':checked')) {
					$('#Element_OphTrOperationbooking_Operation_eye_id_2').attr('checked',false);
					$('#Element_OphTrOperationbooking_Operation_eye_id_1').attr('checked',false);
					$('#Element_OphTrOperationbooking_Operation_eye_id_3').attr('checked',false);
				}
				break;
		}
	});

	$(this).delegate('.remove-unavailable', 'click', function(e) {
		$(this).closest('tr').remove();
		e.preventDefault();
	});

	$(this).delegate('.unavailable-start-date', 'change', function(e) {
		var end = $(this).closest('tr').find('.unavailable-end-date');
		if ($(this).datepicker('getDate') > end.datepicker('getDate')) {
			end.val($(this).val());
		}
	});

	$(this).delegate('.unavailable-end-date', 'change', function(e) {
		var start = $(this).closest('tr').find('.unavailable-start-date');
		if ($(this).datepicker('getDate') < start.datepicker('getDate')) {
			start.val($(this).val());
		}
	});
	
	AlertDialogIfExaminationEventIsMissing();
	
});

function OphTrOperationbooking_PatientUnavailable_getNextKey() {
	var keys = $('#event-content .Element_OphTrOperationbooking_ScheduleOperation .patient-unavailable').map(function(index, el) {
		return parseInt($(el).attr('data-key'));
	}).get();
	var v = Math.max.apply(null, keys);
	if (v >= 0) {
		return v+1;
	}
	return 0;
}

function OphTrOperationbooking_PatientUnavailable_add() {
	var template = $('#intraocularpressure_reading_template').html();
	var data = {
		"key" : OphTrOperationbooking_PatientUnavailable_getNextKey()
	};
	var form = Mustache.render(template, data);
	$('.unavailables').append(form);
	$('.unavailables').find('[id$="date"]').each(function() {
		$(this).datepicker({
			'showAnim': 'fold',
			'dateFormat': nhs_date_format});
	});
}
