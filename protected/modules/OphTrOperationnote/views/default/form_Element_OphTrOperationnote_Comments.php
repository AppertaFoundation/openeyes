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

$is_outpatient_minor_op = isset($data['outpatient_minor_op']) && $data['outpatient_minor_op'];
?>

<div class="element-fields full-width flex-layout" id="OphTrOperationnote_Comments" data-outpatient-minor-op="<?= $is_outpatient_minor_op ? 'yes' : 'no' ?>">
  <div class="data-group cols-11">
    <div>
        <?php echo $form->textArea(
            $element,
            'comments',
            array(),
            false,
            ['cols' => 30, 'class' => 'autosize cols-full'],
            array('label' => 2, 'field' => 'full')
        ) ?>
    </div>
      <div>
            <?php echo $form->textArea(
                $element,
                'postop_instructions',
                array(),
                false,
                ['cols' => 30, 'class' => 'autosize cols-full'],
                array('label' => 2, 'field' => 'full')
            ) ?>
      </div>
  </div>
  <div class="add-data-actions flex-item-bottom">
    <button class="button hint green js-add-select-search" id="add-postop-instruction-btn" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button><!-- popup to add data to element -->
  </div>
</div>

<?php
$is_outpatient_minor_op = isset($data['outpatient_minor_op']) && $data['outpatient_minor_op'];

if ($this->action->id == 'create') {
    $this->widget('EventAutoGenerateCheckboxesWidget', [
        'suffix' => strtolower($this->event->eventType->class_name),
        'disable_auto_generate_for' => $is_outpatient_minor_op ? ['prescription', 'gp_letter', 'optom'] : ['optom'],
    ]);
}

$instru_list = $element->postop_instructions_list;

?>
<script>
    $(document).ready(function () {
    var inputText = $('#Element_OphTrOperationnote_Comments_postop_instructions');

    new OpenEyes.UI.AdderDialog({
      openButton: $('#add-postop-instruction-btn'),
      itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
          array_map(function ($key, $item) {
              return ['label' => $item, 'id' => $key,];
          },
            array_keys($instru_list),
            $instru_list)
      ) ?>, {'multiSelect': true})
      ],
      onReturn: function (adderDialog, selectedItems) {
                inputText.val(formatStringToEndWithCommaAndWhitespace(inputText.val()) + concatenateArrayItemLabels(selectedItems));
        inputText.trigger('oninput');
        return true;
      }
    });
  });
</script>
