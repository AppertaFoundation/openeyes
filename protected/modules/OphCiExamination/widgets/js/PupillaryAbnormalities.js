/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

function resetSelector(side){
    $(side + ' li').each(function () {
        $(this).show();
    });
}

function dedupeAbnormalitiesSelector(side) {
    var abnormalitySelector = ' [name$="[abnormality_id]"]';
    var selectedAbnormalities = [];

    console.log(side + abnormalitySelector);
    $(side + abnormalitySelector).each(function () {
        var value = this.getAttribute('value');
        selectedAbnormalities.push(value);
    });


    $(side + ' li').each(function () {
        if (inArray(this.getAttribute('data-id'), selectedAbnormalities)) {
            $(this).hide();
            console.log('hide');
        } else {
            console.log('show');
            $(this).show();
        }
    });
}

function createRows(selectedItems, tableSelector)
{
    var newRows = [];
    var side = $(tableSelector.split(' ')[0]).attr('data-side');
    var eye_id = (side === "left") ? 1 : 2;
    var template = $('#OEModule_OphCiExamination_models_PupillaryAbnormalities_entry_template').text();

    $(selectedItems).each(function () {
        var data = {};
        data.side = side;
        data.row_count = OpenEyes.Util.getNextDataKey(tableSelector + ' tbody tr', 'key') + newRows.length;
        data.abnormality_id = this.id;
        data.abnormality_display = this.label;
        data.eye_id = eye_id;
        newRows.push(Mustache.render(template, data));
    });
    return newRows;
}

function addEntry(tableSelector, selectedItems)
{
    var side = tableSelector.split(' ')[0];

    $(tableSelector + ' tbody').append(this.createRows(selectedItems, tableSelector));
    $('.flex-item-bottom').find('.selected').removeClass('selected');

    this.dedupeAbnormalitiesSelector(side);
}

$(document).ready(function ()
{
    $('[name$="no_pupillaryabnormalities]"]').click(function (e)
    {
        var side = $(this).closest('.side').attr('data-side');

        if ($(this).prop('checked')) {
            resetSelector('.' + side + '-eye');
            $(this).closest('tr').siblings().remove();
            $('#add-abnormality-btn-' + side).hide();
        } else {
            $('#add-abnormality-btn-' + side).show();
        }
    });

    $('.pa-entry-table').on('click', 'i.trash', function (e)
    {
        var side = $(this).closest('.side').attr('data-side');

        $(this).closest('tr').remove();
        dedupeAbnormalitiesSelector('.' + side + '-eye');
        e.preventDefault();
    });
});