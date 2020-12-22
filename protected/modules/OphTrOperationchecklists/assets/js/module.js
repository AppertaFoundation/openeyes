/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/* Module-specific javascript can be placed here */

$(document).ready(function () {
    document.querySelectorAll("#et_save_draft, #et_save_draft_footer")
        .forEach(button => button.addEventListener('click', function() {
            document.getElementById('isDraft').value = 1;
        }));

    handleButton($('#et_save'), function (e) {
        document.getElementById('isDraft').value = 0;
    });

    handleButton($('#et_cancel'), function (e) {
        if (m = window.location.href.match(/\/update\/[0-9]+/)) {
            window.location.href = window.location.href.replace('/update/', '/view/');
        } else {
            window.location.href = baseUrl + '/patient/summary/' + OE_patient_id;
        }
        e.preventDefault();
    });

    handleButton($('#et_deleteevent'));

    handleButton($('#et_canceldelete'));

    handleButton($('#et_print'), function (e) {
        printIFrameUrl(OE_print_url, null);
        enableButtons();
        e.preventDefault();
    });

    $('select.populate_textarea').unbind('change').change(function () {
        if ($(this).val() != '') {
            var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
            var el = $('#' + cLass + '_' + $(this).attr('id'));
            var currentText = el.text();
            var newText = $(this).children('option:selected').text();

            if (currentText.length == 0) {
                el.text(ucfirst(newText));
            } else {
                el.text(currentText + ', ' + newText);
            }
        }
    });
});

function ucfirst(str) {
    str += '';
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}

function eDparameterListener(_drawing) {
    if (_drawing.selectedDoodle != null) {
        // handle event
    }
}
