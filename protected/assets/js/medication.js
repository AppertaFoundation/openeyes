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
	function loadForm(medication_id) {
		disableButtons('#medication .button');

		$.get(
			baseUrl + "/medication/form",
			{ patient_id: OE_patient_id, medication_id: medication_id},
			function (form) {
				$("#medication_form").html(form).slideDown('fast');
				enableButtons('#medication .button');
				$('#medication_add').attr('disabled',true).addClass('disabled');
			}
		);
	}

	function closeForm() {
		$('#medication_form').html('').slideUp('fast');
		$('#medication_add').attr('disabled',false).removeClass('disabled');
	}

	function selectMedication(id, name) {
		$('#medication_form input[name=drug_id]').val(id);
		$('#medication_drug_name').text(name).show();

		disableButtons('#medication .button');
		$.getJSON(baseUrl + '/medication/drugdefaults', { drug_id: id }, function (res) {
			for (var name in res) {
				$('#medication_form [name=' + name + ']').val(res[name]).change();
			}
			enableButtons('#medication .button');
		});
	}

	function getFuzzyDate(name) {
		var fieldset = $('#medication .' + name);
		return fieldset.find('[name=fuzzy_year]').val() + '-' +
			fieldset.find('[name=fuzzy_month]').val() + '-' +
			fieldset.find('[name=fuzzy_day]').val();
	}

	$('#medication')
		.on('click', '#medication_add', function () {
			loadForm();
		})

		.on('click', '.medication_edit', function () {
			loadForm($(this).data('id'));
			return false;
		})

		.on('click', '#medication_cancel', closeForm)

		.on('change', '[name=drug_select]', function () {
			if ($(this).val()) {
				selectMedication($(this).val(), $(this).find('option:selected').text());
			}
			$(this).val(null);
		})

		.on('autocompleteselect', '[name=drug_autocomplete]', function (e, ui) {
			selectMedication(ui.item.id, ui.item.value);
			$(this).val('');
			return false;
		})

		.on('change', '[name=route_id]', function () {
			var route_id = $(this).val(), option_div = $('#medication_route_option');

			if (route_id) {
				$.get(
					baseUrl + "/medication/drugrouteoptions",
					{route_id: route_id},
					function (res) { option_div.html(res); }
				);
			} else {
				option_div.html('');
			}
		})

		.on('click', '[name=current]', function () {
			if ($(this).val()) {
				$('#medication_end').slideUp();
			} else {
				$('#medication_end').slideDown();
			}
		})

		.on('click', '#medication_save', function (e) {
			var form = $('#medication_form form');

			form.find('[name=start_date]').val(getFuzzyDate('medication_start_date'));
			if (!form.find('[name=current]:checked').val()) {
				form.find('[name=end_date]').val(getFuzzyDate('medication_end_date'));
			}

			disableButtons('#medication .button');
			$.ajax(baseUrl + "/medication/save", {
				type: 'POST', data: form.serialize(),
				success: function (res) {
					$('#medication_list').html(res);
					closeForm();
				},
				error: function (xhr) {
					if (xhr.status != 422) return;

					var errors = $.parseJSON(xhr.responseText), error_div = $('#medication_form_errors');
					error_div.html('');
					for (var field in errors) {
						for (var i in errors[field]) {
							error_div.append('<div>' + errors[field][i] + '</div>');
						}
					}
					error_div.show();
				},
				complete: function () {
					enableButtons('#medication .button');
				},
			});
		});
});
