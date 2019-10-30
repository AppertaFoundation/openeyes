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
    let table = $(this).closest('table');
    const $tr = $(this).closest('tr');

    if (table.find('tbody tr').length <= 1) table.hide();

    if ($tr.data('side') == 'left') {
      setCurrentManagementIOP('left');
    } else {
      setCurrentManagementIOP('right');
    }

    $tr.remove();

    return false;
  }

  // Set Intraocular reading attribute field validation error
  let readings = $('.js-reading-time');
  readings.each((i,reading) => {
    if (!$(reading).val().match('^[0-9]{2}:[0-9]{2}$')) {
      $(reading).addClass('highlighted-error error');   
    }
  });

  $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_right").on("click", "i.trash", null, deleteReading);
  $("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_left").on("click", "i.trash", null, deleteReading);
});
