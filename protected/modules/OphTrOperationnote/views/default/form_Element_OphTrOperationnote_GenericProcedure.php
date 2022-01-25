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

<?php
$layoutColumns = array(
    'label' => 2,
    'field' => 10,
);
// As this view can be used for loading in multiple elements, we'll get conflicts
// of form control id's, so we generate a number hash of the element name to ensure
// the ids are unique.
$numHash = crc32($element->getElementTypeName());

/** @var OphTrOperationnote_Attribute[] $attributes */
$attributes = $this->getAttributesForProcedure($element->proc_id);
$itemSets = [];
foreach ($attributes as $attribute) {
    $items = array();

    foreach ($attribute->options as $option) {
        $items[] = ['label' => (string)$option->value . ", "];
    }

    $itemSets[] = ['items' => $items ,
        'header' => $attribute->label ,
        'multiSelect' => $attribute->is_multiselect
    ];
}
?>

<section
    class="edit element full on-demand sub-element
        <?php echo $element->elementType->class_name ?>
        <?php if (@$ondemand) {
            ?>hidden<?php
        } ?>
        <?php if ($this->action->id === 'update' && !$element->event_id) {
            ?>missing<?php
        } ?>"
    data-element-type-id="<?php echo $element->elementType->id ?>"
    data-element-type-class="<?php echo $element->elementType->class_name ?>"
    data-element-type-name="<?php echo $element->elementType->name ?>"
    data-element-display-order="<?php echo $element->elementType->display_order ?>">

  <header class="element-header">
    <h3 class="element-title"><?php echo $element->getElementTypeName() ?></h3>
  </header>

    <?php if ($this->action->id == 'update' && !$element->event_id) { ?>
      <div class="alert-box alert">This element is missing and needs to be completed</div>
    <?php } ?>

    <div class="element-actions">
        <!-- order is important for layout because of Flex -->
        <!-- remove MUST be last element -->
        <span class="disabled" title="To delete this element, you must remove the procedure from the Procedures element">
                      <i class="oe-i trash-blue disabled"></i>
        </span>
    </div>

  <div class="element-fields full-width flex-layout" id="OphTrOperationnote_GenericProcedure_comments">
    <div class="data-group cols-11">

      <div>
        <div id="div_Element_OphTrOperationnote_GenericProcedure_comments" class="data-group flex-layout" style="">
            
        <div class="cols-2 column">
              <label for="Element_OphTrOperationnote_GenericProcedure_comments">
                  Comments:          </label>
        </div>
        <div class="cols-full column">
          <?=\CHtml::textArea(
              get_class($element) . '[' . $element->proc_id . '][comments]',
              $element->comments,
              array('cols' => 30, 'class' => 'cols-full autosize', 'id' => get_class($element) . '_comments_' . $numHash)
          ) ?>
        </div>
      </div>
      </div>
    </div>

        <?php if (!empty($attributes)) : ?>
      <div class="add-data-actions flex-item-bottom">
          <button class="button hint green js-add-select-search" type="button" id="add_attribute_<?=$numHash?>">
              <i class="oe-i plus pro-theme"></i>
          </button>
      </div>
        <?php endif; ?>

  </div>
  <input type="hidden" name="<?php echo get_class($element) ?>[<?php echo $element->proc_id ?>][proc_id]"
         value="<?=\CHtml::encode($element->proc_id) ?>"/>
  <input type="hidden" name="<?php echo get_class($element) ?>[<?php echo $element->proc_id ?>][id]"
         value="<?=\CHtml::encode($element->id) ?>"/>
</section>
<script type="text/javascript" id="history-add-to-dialog">
    $(function () {
        var inputText = $('#Element_OphTrOperationnote_GenericProcedure_comments_<?=$numHash?>');

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add_attribute_<?=$numHash?>'),
            itemSets: $.map(<?= CJSON::encode($itemSets) ?>, function ($itemSet) {
                return new OpenEyes.UI.AdderDialog.ItemSet($itemSet.items, {'header': $itemSet.header,'multiSelect': $itemSet.multiSelect });
            }),
            liClass: 'restrict-width',
            onReturn: function (adderDialog, selectedItems) {
                inputText.val(formatStringToEndWithCommaAndWhitespace(inputText.val()) + concatenateArrayItemLabels(selectedItems));
                autosize.update(inputText);
                inputText.trigger('oninput');
                return true;
            }
        });

    });

</script>
