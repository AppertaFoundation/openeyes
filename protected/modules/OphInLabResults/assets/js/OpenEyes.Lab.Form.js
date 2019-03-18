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
OpenEyes.Lab = OpenEyes.Lab || {};

(function (exports) {
    var Form = {};
    var ajaxElementUri = '/OphInLabResults/Default/elementForm';
    var $resultTypeSelect;

    /**
     * Reset the dropdown when an element is removed.
     */
    function removeResultElement(event) {
        let section = $(event.target).closest('section');
        if (section.length && section.find('[name^="[element_dirty]"]').val() === '1') {
            $(document).one("element_removed", function () {
                $resultTypeSelect.val('');
            });
        }
    }

    /**
     * Load the apropriate form for the lab result type.
     *
     * @param e
     * @returns {boolean}
     */
    function loadResultElement(e) {
        var option = e.target.options[e.target.selectedIndex];

        if (!option.dataset.elementId) {
            removeResultElement($('#result-output').parent());
            return false;
        }
        disableButtons();
        $.ajax({
            url: ajaxElementUri,
            data: {
                patient_id: OE_patient_id,
                id: option.dataset.elementId
            },
            dataType: 'html',
            success: function (data) {
                var $dataElement = $('<section></section>').html(data);
                $dataElement.find('.js-remove-element').on('click', removeResultElement);
                $('.lab-results-type').parent().after($dataElement);
                enableButtons();
                $('textarea').autosize();
            }
        });
    }

    Form.init = function ($resultType) {
        $resultTypeSelect = $resultType;
        $resultTypeSelect.on('change', loadResultElement);
        $('.js-remove-element').on('click', removeResultElement);
    };

    exports.Form = Form;
}(this.OpenEyes.Lab));
