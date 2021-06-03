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
        <?php echo $form->textArea(
            $element,
            'comments',
            array('class' => 'cols-full', 'nowrapper' => true),
            false,
            array(
              'rows' => 1,
              'placeholder' => 'Management',
              'style' => 'overflow: hidden; overflow-wrap: break-word; height: 24px;',
            )
        ) ?>
  </div>
  <div class="add-data-actions flex-item-bottom">
    <button class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
  </div>
</div>
<?php
$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
$itemSets = [];
foreach ($this->getAttributes($element, $firm->serviceSubspecialtyAssignment->subspecialty_id) as $attribute) {
    $items = array_map(function ($attr) {
        return ['label' => $attr['slug']];
    }, $attribute->getAttributeOptions());
    $itemSets[] = ['items' => $items ,
        'header' => $attribute->label ,
        'multiSelect' => $attribute->is_multiselect === '1' ? true : false
    ];
}
?>

<script type="text/javascript">
  $(function () {
    var managementDiv = $('section[data-element-type-class=\'<?= CHtml::modelName($element) ?>\']');

    new OpenEyes.UI.AdderDialog({
      openButton: managementDiv.find('.js-add-select-search'),
      itemSets: $.map(<?= CJSON::encode($itemSets) ?>, function ($itemSet ) {
          return new OpenEyes.UI.AdderDialog.ItemSet($itemSet.items, {'header': $itemSet.header,'multiSelect': $itemSet.multiSelect });
      }),
      liClass: 'auto-width',
      onReturn: function (adderDialog, selectedItems) {
        var inputText = managementDiv.find(
          '#<?= CHtml::modelName($element) ?>_comments'
        );

        var text = $.map(selectedItems, function (item) {
          return item['label'];
        }).join(' ');

        inputText.val(inputText.val() ? inputText.val() + text : text);
        autosize.update(inputText);
        inputText.trigger('oninput');
        return true;
      },
    });
  });
</script>
