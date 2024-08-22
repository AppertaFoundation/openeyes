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
 *
 * @var $element \OEModule\OphCiExamination\models\AdviceGiven
 */

use OEModule\OphCiExamination\models\AdviceLeafletCategory;

$model_name = CHtml::modelName($element);
$current_firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

$leaflet_categories = AdviceLeafletCategory::model()
    ->active()
    ->forInstitution(Yii::app()->session->getSelectedInstitution())
    ->forSubspecialty(Yii::app()->session->getSelectedFirm()->subspecialty)
    ->with(['leaflets:active'])
    ->findAll();

$categories = [];
$categorised_leaflets = [];
$leaflets = [];

foreach ($leaflet_categories as $category) {
    $mapped_leaflets = \CHtml::listData($category->leaflets, 'id', 'name');

    $categories[] = ['id' => $category->id, 'label' => $category->name];
    $categorised_leaflets[$category->id] = array_keys($mapped_leaflets);
    $leaflets = $leaflets + $mapped_leaflets;
}

$itemSets = [];

foreach ($this->controller->getAttributes($element, $current_firm->getSubspecialtyID()) as $attribute) {
    $items = array();

    foreach ($attribute->getAttributeOptions() as $option) {
        $items[] = ['label' => (string)$option->slug];
    }

    $itemSets[] = ['items' => $items ,
        'header' => $attribute->label ,
        'multiSelect' => $attribute->is_multiselect === '1'
    ];
}
?>

<div class="element-fields full-width flex-layout">
    <div class="flex-t cols-10">
        <div class="cols-6">
            <?= $form->textArea(
                    $element,
                    'comments',
                    array('nowrapper' => true),
                    false,
                    array('id' => 'js-advice-comment', 'class' => 'cols-full js-input-comments', 'rows' => 2, 'placeholder' => 'Advice given to patient (optional)', 'style' => 'overflow: hidden; overflow-wrap: break-word; height: 24px;')
                ) ?>
        </div>
        <div class="cols-5">
            <ul class="oe-multi-select inline" id="js-leaflet-entries" data-test="leaflet-entries">
                <?php foreach ($element->leaflet_entries as $i => $entry) { ?>
                    <li data-id="<?= $entry->leaflet_id ?>" data-label="<?= $entry->leaflet->name ?>" data-test="leaflet-entry">
                        <input name="<?= CHtml::modelName($element) ?>[leaflet_entries][<?= $i ?>]" type="hidden" value="<?= $entry->leaflet_id ?>"/>
                        <?= $entry->leaflet->name ?><i class="oe-i remove-circle small-icon pad-left"></i>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green js-add-select-search"
                id="add-leaflet-btn" type="button" data-test="add-leaflet-btn">
            Leaflets
        </button>
        <button id="advice-comment-btn"
                class="button hint green"
                type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>

<script type="text/template" id="AdviceGiven_entry_template" style="display:none">
    <li data-id="{{id}}" data-label="{{name}}" data-test="leaflet-entry">
        <input name="<?= CHtml::modelName($element) ?>[leaflet_entries][{{row_count}}]" type="hidden" value="{{id}}"/>
        {{name}}<i class="oe-i remove-circle small-icon pad-left"></i>
    </li>
</script>

<script type="text/javascript">
    $(function () {
        const categorised_leaflets = <?= json_encode($categorised_leaflets) ?>;
        const leaflets = <?= json_encode($leaflets) ?>;

        function createLeafletAdderItems(leafletsIds) {
            return leafletsIds.map((id) => {
                    const label = leaflets[id];
                    let item = document.createElement('li');

                    item.innerHTML = `<span class="auto-width">${label}</span>`;
                    item.dataset.id = id;
                    item.dataset.label = label;

                    return item;
                });
        }

        // Pass null for all
        function setLeafletsForCategory(categoryId) {
            let replacements = [];

            if (categoryId === null) {
                replacements = createLeafletAdderItems(Object.keys(leaflets));
            } else {
                replacements = createLeafletAdderItems(categorised_leaflets[categoryId]);
            }

            document.querySelector('#leaflet ul').replaceChildren(...replacements);
        }

        $('#js-leaflet-entries').on('click', '.remove-circle', function() {
            $(this).parent().remove();
        });

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-leaflet-btn'),
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(<?= json_encode($categories) ?>, {'id': 'leaflet-category', 'multiSelect': false}),
                // Use a dummy entry to ensure the leaflet column is set up correctly
                new OpenEyes.UI.AdderDialog.ItemSet([{id: '', label: ''}], {'id': 'leaflet', 'multiSelect': true})
            ],
            onSelect: function() {
                if ($(this).closest('td').data('adder-id') === 'leaflet-category') {
                    if (!$(this).hasClass('selected')) {
                        setLeafletsForCategory($(this).data('id'));
                    } else {
                        setLeafletsForCategory(null);
                    }
                }
            },
            onReturn: function (adderDialog, selectedItems) {
                // Only get the selectedItems that are not categories.
                let leaflets = selectedItems.filter(selectedItem => {
                    return !selectedItem.hasOwnProperty('itemSet');
                });
                let existing = [];

                // Get any existing entries, then remove them from the DOM (they will be reinserted below).
                $('#js-leaflet-entries li').each(function () {
                    // Remove any selected leaflets from the list that are already attached to the element.
                    // They will be re-added in the correct order.
                    const id = $(this).data('id');
                    const foundIndex = leaflets.findIndex(item => item.id == id);
                    if (foundIndex > -1) {
                        leaflets.splice(foundIndex, 1);
                    }
                    existing.push({
                        label: $(this).data('label'),
                        id: id,
                    });
                });
                // Add all of the existing leaflets to the start of the leaflets array (maintaining the existing order).
                leaflets = existing.concat(leaflets);
                $('#js-leaflet-entries').html('');

                leaflets.forEach((item, index) => {
                    const data = {
                        id: item.id,
                        name: item.label,
                        row_count: index
                    };
                    const html = Mustache.render($('#AdviceGiven_entry_template').html(), data);
                    $('#js-leaflet-entries').append(html);
                });
                return true;
            }
        });

        setLeafletsForCategory(null); // Replaces the dummy entry (mentioned above) with all the available leaflets

        new OpenEyes.UI.AdderDialog({
            openButton: $('#advice-comment-btn'),
            itemSets: $.map(<?= CJSON::encode($itemSets) ?>, function ($itemSet) {
                return new OpenEyes.UI.AdderDialog.ItemSet($itemSet.items, {'header': $itemSet.header,'multiSelect': $itemSet.multiSelect });
            }),
            onReturn: function (adderDialog, selectedItems) {
                let comments = '';
                for (let item in selectedItems) {
                    comments = comments + selectedItems[item].label;
                }
                $('.js-input-comments').val(comments);
                return true;
            }
        });
    });
</script>
