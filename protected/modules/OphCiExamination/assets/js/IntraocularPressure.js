/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$(document).ready(function () {
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

  $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_right").on("click", "i.trash", null, deleteReading);
  $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_left").on("click", "i.trash", null, deleteReading);

  $('select.IOPinstrument').die('change').live('change', function (e) {
    e.preventDefault();

    var instrument_id = $(this).val();

    var scale_td = $(this).closest('tr').children('td.scale_values');
    var index = $(this).closest('tr').data('index');
    var side = $(this).closest('tr').data('side');

    getScaleDropdown(instrument_id, scale_td, index, side);
  });
});

function getScaleDropdown(instrument_id, scale_td, index, side){
    $.ajax({
        'type': 'GET',
        'url': baseUrl + '/OphCiExamination/default/getScaleForInstrument?name=OEModule_OphCiExamination_models_HistoryIOP' +
            '&instrument_id=' + instrument_id + '&side=' + side + '&index=' + index,
        'success': function (html) {
            if (html.length > 0) {
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
}
