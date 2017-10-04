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

(function() {
	function updateRTTInfo(el) {
		var show = false;
		var cs, b;

		var selectedVal = $(el).val();
		$(el).find('option').each(function() {
			if ($(this).attr('value') == selectedVal) {
				cs = $(this).data('clock-start');
				b = $(this).data('breach');
				return false;
			}
		});

		if (cs) {
			$('#rtt-clock-start').html(cs);
			show = true;
		}
		else {
			$('#rtt-clock-start').html('');
		}
		if (b) {
			$('#rtt-breach').html(b);
			show = true;
		}
		else {
			$('#rtt-breach').html('');
		}
		if (show) {
			$('#rtt-info').show();
		}
		else {
			$('#rtt-info').hide();
		}
	}

	function ProcedureChecker(options) {
		this.options = $.extend(true, {}, ProcedureChecker._defaultOptions, options);
	}

	ProcedureChecker._defaultOptions = {
		'procCheckURI': '/OphTrOperationbooking/default/verifyProcedures'
	}

	ProcedureChecker.prototype.verifySubmit = function(form) {
		disableButtons();
		$.ajax({
			url: this.options.procCheckURI + '?patient_id=' + OE_patientId,
			data: $(form).serialize(),
			type: 'POST',
			dataType: 'JSON',
			success: function(response) {
				if (response.previousProcedures) {
					var dialog = new OpenEyes.UI.Dialog.Confirm({
						content: response.message,
						title: 'Duplicate procedure(s)'
					});
					dialog.on('ok', function() {
						$(form).trigger('submit');
					}.bind(this));

					dialog.on('cancel', function() {enableButtons(); });
					dialog.open();
				}
				else {
					$(form).trigger('submit');
				}
			}.bind(this),
			error: function(jqXHR, status, error) {
				var alert = new OpenEyes.UI.Dialog.Alert({
					content: 'An unexpected error has occurred, cannot save at this time.'
				});
				alert.on('close', function() { enableButtons(); });
				alert.open();

			}.bind(this)
		});

	}

	$(document).ready(function() {
		updateRTTInfo($('#Element_OphTrOperationbooking_Operation_referral_id'));
		$('#Element_OphTrOperationbooking_Operation_referral_id').on('change', function() {
			updateRTTInfo($(this));
		});

		var procChecker = new ProcedureChecker();

		$(this).on('click', '#et_save, #et_save_and_schedule', function(e) {
			if (OE_confirmProcedures) {
				e.preventDefault();
				procChecker.verifySubmit(e.target.form);
			}
		});

		function setDependentField(field, dependent_field_selector) {
			var input_selector = 'input[name=Element_OphTrOperationbooking_Operation\\[' + field + '\\]]';

			function showHide() {
				var div = $(dependent_field_selector);
				var option = $(input_selector + ':checked');

				div[parseInt(option.val(), 10) ? 'show' : 'hide']();
			}

			$(document).on('click', input_selector, showHide);
			showHide();
		}

		setDependentField('stop_medication', '#div_Element_OphTrOperationbooking_Operation_stop_medication_details');
		setDependentField('special_equipment', '#div_Element_OphTrOperationbooking_Operation_special_equipment_details');
		setDependentField('consultant_required', '#div_Element_OphTrOperationbooking_Operation_named_consultant_id');

		$('.remove_organising_admission_user').live('click',function(e) {
			e.preventDefault();

			$('#Element_OphTrOperationbooking_Operation_organising_admission_user_id').val('');
			$(this).parent().html('None');
		});

		handleButton($('#et_print_admission_form'),function() {
			printIFrameUrl(baseUrl + '/OphTrOperationbooking/default/admissionForm/' + OE_event_id);
		});

		$('input[name="Element_OphTrOperationbooking_Operation[priority_id]"]').click(function(e) {
			var priority_id = $(this).val();

			if (!priority_canschedule[priority_id]) {
				$('#et_save').show();
				$('#et_save_and_schedule_later').hide();
				$('#et_save_and_schedule').hide();
			} else {
				if ($('#et_save_and_schedule_later').length >0) {
					$('#et_save').hide();
					$('#et_save_and_schedule_later').show();
					$('#et_save_and_schedule').show();
				}
			}
		});
	});
}());
