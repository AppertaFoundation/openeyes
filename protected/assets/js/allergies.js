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
	$('#no_allergies').bind('change', function() {
		if ($(this)[0].checked) {
			$('.allergy_field').hide().find('select').attr('disabled', 'disabled');
		}
		else {
			$('.allergy_field').show().find('select').removeAttr('disabled');
		}
	});

	$('#allergy_id').change(function () {
		if ($(this).find(':selected').text() == 'Other') {
			$('#allergy_other').slideDown('fast');
		} else {
			$('#allergy_other').slideUp('fast');
		}
	});

	$('#btn-add_allergy').click(function() {
		$('#add_allergy').slideToggle('fast');
		$('#btn-add_allergy').attr('disabled',true);
		$('#btn-add_allergy').addClass('disabled');
	});
	$('button.btn_cancel_allergy').click(function() {
		$('#add_allergy').slideToggle('fast');
		$('#btn-add_allergy').attr('disabled',false);
		$('#btn-add_allergy').removeClass('disabled');
		return false;
	});
	$('button.btn_save_allergy').click(function() {
		if ($('#allergy_id').val() == '' && !$('#no_allergies')[0].checked) {
			new OpenEyes.UI.Dialog.Alert({
				content: "Please select an allergy or confirm patient has no allergies"
			}).open();
			return false;
		}
		if ($('#allergy_id :selected').text() == 'Other' && $('#allergy_other input').val().trim() == '') {
			new OpenEyes.UI.Dialog.Alert({
				content: "Please enter an allergy"
			}).open();
			return false;
		}
		$('img.add_allergy_loader').show();
		return true;
	});


	$('.removeAllergy').live('click',function() {
		$('#remove_allergy_id').val($(this).attr('rel'));

		$('#confirm_remove_allergy_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});

		return false;
	});

	$('button.btn_remove_allergy').click(function() {
		$("#confirm_remove_allergy_dialog").dialog("close");

		var aa_id = $('#remove_allergy_id').val();

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removeAllergy?patient_id=' + OE_patient_id + '&assignment_id=' + aa_id,
			'success': function(html) {
				if (html == 'success') {
					var row = $('#currentAllergies tr[data-assignment-id="' + aa_id + '"]');
					var allergy_id = row.data('allergy-id');
					var allergy_name = row.data('allergy-name');
					row.remove();
					if($('.removeAllergy').length == 0) {
						$('#currentAllergies').hide();
						$('.allergy-status-unknown').show();
						$('.allergies_confirm_no').show();
					}
					if (allergy_name != "Other") {
						$('#allergy_id').append('<option value="'+allergy_id+'">'+allergy_name+'</option>');
						sort_selectbox($('#allergy_id'));
					}
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "Sorry, an internal error occurred and we were unable to remove the allergy.\n\nPlease contact support for assistance."
					}).open();
				}
			},
			'error': function() {
				new OpenEyes.UI.Dialog.Alert({
					content: "Sorry, an internal error occurred and we were unable to remove the allergy.\n\nPlease contact support for assistance."
				}).open();
			}
		});

		return false;
	});

	$('button.btn_cancel_remove_allergy').click(function() {
		$("#confirm_remove_allergy_dialog").dialog("close");
		return false;
	});
});
