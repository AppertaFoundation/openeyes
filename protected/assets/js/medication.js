/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$(document).ready(function () {
	$('#btn-add_medication').click(function() {
		$('#add_medication #route_id').val('');
		$('#add_medication #drug_id').val('');
		$('#add_medication #frequency_id').val('');
		$('#add_medication #start_date').val('');
		$('div.routeOption .date').html('');
		$('div.routeOption').hide();

		$('#add_medication').slideToggle('fast');
		$('#btn-add_medication').attr('disabled',true);
		$('#btn-add_medication').addClass('disabled');
	});
	$('button.btn_cancel_medication').click(function() {
		$('#add_medication').slideToggle('fast');
		$('#btn-add_medication').attr('disabled',false);
		$('#btn-add_medication').removeClass('disabled');
		$('div.medication_form_errors').html('').hide();
		return false;
	});
	$('#drug_id').change(function() {
		if ($(this).val() != '') {
			selectMedication($(this).val(),$(this).children('option:selected').text());
			$('#drug_id').val('');
		}
	});

	function selectMedication(id, name)
	{
		$('#selectedMedicationName').text(name).show();
		$('#selectedMedicationID').val(id);

		$.ajax({
			'type': 'GET',
			'dataType': 'json',
			'url': baseUrl+'/patient/DrugDefaults?drug_id='+id,
			'success': function(data) {
				if (data['route_id']) {
					$('#route_id').val(data['route_id']);
					$('#route_id').change();
				}
				if (data['frequency_id']) {
					$('#frequency_id').val(data['frequency_id']);
				}
			}
		});
	}

	$('button.btn_save_medication').click(function(e) {
		disableButtons('.btn_save_medication,.btn_cancel_medication');

		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'data': $('#add-medication').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'dataType': 'json',
			'url': baseUrl+'/patient/validateAddMedication',
			'success': function(data) {
				$('div.medication_form_errors').html('').hide();

				if (data.length == 0) {
					$('#add-medication').submit();
					return;
				}

				enableButtons('.btn_save_medication,.btn_cancel_medication');

				for (var i in data) {
					$('div.medication_form_errors').show().append('<div>'+data[i]+'</div>');
				}
			}
		});
	});
	$('.editMedication').click(function(e) {
		var medication_id = $(this).attr('rel');

		$('#edit_medication_id').val(medication_id);

		$.ajax({
			'type': 'GET',
			'dataType': 'json',
			'url': baseUrl+'/patient/getMedication?medication_id='+medication_id,
			'success': function(data) {
				$('#add_medication #route_id').val(data['route_id']);
				$('#selectedMedicationID').val(data['drug_id']);
				$('#selectedMedicationName').text(data['drug_name']).show();
				$('#add_medication #frequency_id').val(data['frequency_id']);
				$('#add_medication #start_date').val(data['start_date']);
				$('div.routeOption .data').html(data['route_options']);
				$('div.routeOption').show();
				$('#add_medication #option_id').val(data['option_id']);
			}
		});

		$('#add_medication').slideToggle('fast');
		$('#btn-add_medication').attr('disabled',true);
		$('#btn-add_medication').addClass('disabled');

		e.preventDefault();
	});
	$('#route_id').change(function() {
		var route_id = $(this).val();

		if (route_id == '') {
			$('div.routeOption').hide();
			$('div.routeOption .data').html('');
		} else {
			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/patient/getDrugRouteOptions?route_id='+route_id,
				'success': function(html) {
					$('div.routeOption .data').html(html);
					if (html.length >0) {
						$('div.routeOption').show();
					} else {
						$('div.routeOption').hide();
					}
				}
			});
		}
	});

	$('.removeMedication').live('click',function() {
		$('#medication_id').val($(this).attr('rel'));

		$('#confirm_remove_medication_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});

		return false;
	});

	$('button.btn_remove_medication').click(function() {
		$("#confirm_remove_medication_dialog").dialog("close");

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removeMedication?patient_id='+OE_patient_id+'&medication_id='+$('#medication_id').val(),
			'success': function(html) {
				if (html == 'success') {
					$('a.removeMedication[rel="'+$('#medication_id').val()+'"]').parent().parent().remove();
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "Sorry, an internal error occurred and we were unable to remove the medication.\n\nPlease contact support for assistance."
					}).open();
				}
			},
			'error': function() {
				new OpenEyes.UI.Dialog.Alert({
					content: "Sorry, an internal error occurred and we were unable to remove the medication.\n\nPlease contact support for assistance."
				}).open();
			}
		});

		return false;
	});

	$('button.btn_cancel_remove_medication').click(function() {
		$("#confirm_remove_medication_dialog").dialog("close");
		return false;
	});
});

