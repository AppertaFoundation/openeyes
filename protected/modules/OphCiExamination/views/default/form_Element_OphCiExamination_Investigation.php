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
<div class="element-fields flex-layout full-width ">
    <div class="cols-7">
        <?php echo $form->textArea($element, 'description', array('class' => 'cols-full autosize', 'nowrapper' => true), false, array('rows' => 1, 'placeholder' => 'description', 'style' => 'overflow: hidden; overflow-wrap: break-word; height: 24px;')) ?>
    </div>
    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green js-add-select-search"
                id="add-investigation-btn" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>
<?php
$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
$itemSets = array();
foreach ($this->getAttributes($element, $firm->serviceSubspecialtyAssignment->subspecialty_id) as $attribute) {
    $items = array_map(function ($attr) {
        return ['label' => $attr['slug']];
    }, $attribute->getAttributeOptions());
    $itemSets[] = ['items' => $items,
        'header' => $attribute->label,
        'multiSelect' => $attribute->is_multiselect === '1' ? true : false
    ];
}
?>
<script type="text/javascript">
    $(function () {
        autosize($('.autosize'));
        var investigationDiv =
            $('section[data-element-type-class=\'OEModule_OphCiExamination_models_Element_OphCiExamination_Investigation\']');

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-investigation-btn'),
            itemSets: $.map(<?= CJSON::encode($itemSets) ?>, function ($itemSet) {
                return new OpenEyes.UI.AdderDialog.ItemSet($itemSet.items, {
                    'header': $itemSet.header,
                    'multiSelect': $itemSet.multiSelect
                })
            }),
            liClass: 'restrict-width',
            onReturn: function (adderDialog, selectedItems) {
                var inputText = investigationDiv.find(
                    '#OEModule_OphCiExamination_models_Element_OphCiExamination_Investigation_description'
                );
                inputText.val(formatStringToEndWithCommaAndWhitespace(inputText.val()) + concatenateArrayItemLabels(selectedItems));
                autosize.update(inputText);
                inputText.trigger('oninput');
                return true;
            }
        });
    });
</script>
