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

$leaflets_by_category = array_reduce(
    $leaflet_categories,
    function ($by_category, $category) {
        $by_category[$category->id] = array_map(
            function ($leaflet) {
                return ['id' => $leaflet->id, 'label' => $leaflet->name];
            },
            $category->leaflets
        );
        return $by_category;
    }
);

$categories = array_map(
    static function ($item) {
        return array('id' => $item->id, 'label' => $item->name);
    },
    $leaflet_categories
);

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
            <ul class="oe-multi-select inline" id="js-leaflet-entries">
                <?php foreach ($element->leaflet_entries as $i => $entry) { ?>
                    <li data-id="<?= $entry->leaflet_id ?>" data-label="<?= $entry->leaflet->name ?>">
                        <input name="<?= CHtml::modelName($element) ?>[leaflet_entries][<?= $i ?>]" type="hidden" value="<?= $entry->leaflet_id ?>"/>
                        <?= $entry->leaflet->name ?><i class="oe-i remove-circle small-icon pad-left"></i>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green js-add-select-search"
                id="add-leaflet-btn" type="button">
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
    <li data-id="{{id}}" data-label="{{name}}">
        <input name="<?= CHtml::modelName($element) ?>[leaflet_entries][{{row_count}}]" type="hidden" value="{{id}}"/>
        {{name}}<i class="oe-i remove-circle small-icon pad-left"></i>
    </li>
</script>

<script type="text/javascript">
    $(function () {
        let leaflets = <?= json_encode($leaflets_by_category) ?>;
        $('#js-leaflet-entries').on('click', '.remove-circle', function() {
            $(this).parent().remove();
        });
        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-leaflet-btn'),
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(<?= json_encode($categories) ?>, {'id': 'leaflet-category', 'multiSelect': false}),
                new OpenEyes.UI.AdderDialog.ItemSet(<?= json_encode( (isset($categories[0]) ? $leaflets_by_category[$categories[0]['id']] : [])) ?>, {'id': 'leaflet', 'multiSelect': true})
            ],
            onSelect: function() {
                if ($(this).closest('td').data('adder-id') === 'leaflet-category') {
                    let leaflet_list = leaflets[$(this).data('id')];
                    $('#leaflet').find('ul').html('');
                    leaflet_list.forEach((leaflet) => {
                        $('#leaflet').find('ul').append(`<li data-label="${leaflet.label}" data-id="${leaflet.id}"><span class="auto-width">${leaflet.label}</span></li>`)
                    });
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
