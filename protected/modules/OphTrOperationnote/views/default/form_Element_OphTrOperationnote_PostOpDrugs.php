<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<div class="element-fields full-width flex-layout">
    <ul class="oe-multi-select inline cols-8" id="postop-drugs">
        <input type="hidden" name="<?= CHtml::modelName($element) ?>[MultiSelectList_Drug[drugs]]"
               class="multi-select-list-name">
        <?php foreach ($element->drugs as $drug) : ?>
            <li>
                <?= $drug->name ?>
                <i class="oe-i remove-circle small-icon pad-left"></i>
                <input type="hidden" name="Drug[]" value=<?= $drug->id ?>>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="add-data-actions flex-item-bottom " id="add-postop-drugs-popup">
        <button class="button hint green js-add-select-search" id="add-postop-drugs-btn" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button><!-- popup to add data to element -->
    </div>
</div>
<?php $drugs = $this->getPostOpDrugList($element); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#postop-drugs').on('click', '.oe-i.remove-circle.small-icon.pad-left', function (e) {
            e.preventDefault();
            $(e.target).closest('li').remove();
        });

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-postop-drugs-btn'),
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($key, $item) {
                            return ['label' => $item, 'id' => $key];
                    }, array_keys($drugs), $drugs)
                )?>,
                    {'multiSelect': true})],
            onReturn: function (adderDialog, selectedItems) {
                selectedItems.forEach(function (item) {
                    // Check if the drug exists in list
                    if ($('#postop-drugs').find(':input[value="' + item.id + '"]').length === 0) {
                        // Add drug to the list
                        $('#postop-drugs').append('<li>' +
                            item.label +
                            '<i class="oe-i remove-circle small-icon pad-left"></i>' +
                            '<input type="hidden" name="Drug[]" value="' + item.id + '">') +
                        '</li>';
                    }
                });
                return true;
            }
        });
    });
</script>
