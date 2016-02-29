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
	function addReading(e) {
		var side = e.data.side,
		    table = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_" + side),
			indices = table.find('tr').map(function () { return $(this).data('index'); });

		table.find("tbody").append(
			Mustache.render(
				template = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_reading_template_" + side).text(),
				{
					index: indices.length ? Math.max.apply(null, indices) + 1 : 0,
					time: (new Date).toTimeString().substr(0, 5)
				}
			)
		);

		table.show();
	}

	function deleteReading(e) {
		var table = $(this).closest('table');
		if (table.find('tbody tr').length <= 1) table.hide();

		if ($(this).closest('tr').data('side') == 'left') {
			setCurrentManagementIOP('left');
		} else {
			setCurrentManagementIOP('right');
		}

		$(this).closest('tr').remove();

		return false;
	}

	$("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_add_right").click({side: "right"}, addReading);
	$("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_add_left").click({side: "left"}, addReading);


	$("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_right").on("click", "a.delete", null, deleteReading);
	$("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_left").on("click", "a.delete", null, deleteReading);

	$('select.IOPinstrument').die('change').live('change',function(e) {
		e.preventDefault();

		var instrument_id = $(this).val();

		var scale_td = $(this).closest('tr').children('td.scale_values');
		var index = $(this).closest('tr').data('index');
		var side = $(this).closest('tr').data('side');

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/OphCiExamination/default/getScaleForInstrument?instrument_id=' + instrument_id + '&side=' + side + '&index=' + index,
			'success': function(html) {
				if (html.length >0) {
					scale_td.html(html);
					scale_td.show();
					scale_td.prev('td').hide();
				} else {
					scale_td.html('');
					scale_td.hide();
					scale_td.prev('td').show();
				}
			}
		});
	});
});
