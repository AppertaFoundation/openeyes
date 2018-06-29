<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\PastSurgery_Operation;

?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('PastSurgery.js') ?>"></script>
<?php
$model_name = CHtml::modelName($element);
?>
<div class="element-fields flex-layout full-width">
  <div class="data-group cols-10">
  <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <table id="<?= $model_name ?>_operation_table" class="cols-full <?= $model_name ?>_Operation">
      <thead>
      <tr>
        <th class="cols-3">Procedures</th>
        <th>Right</th>
        <th>Left</th>
        <th>Both</th>
        <th>None</th>
        <th>Date</th>
        <th>Notes</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <?php
      $row_count = 0;
      foreach ($element->operations as $i => $op) {
          $this->render(
              'PastSurgery_OperationEntry_event_edit',
              array(
                  'op' => $op,
                  'form' => $form,
                  'row_count' => ($row_count),
                  'field_prefix' => $model_name . '[operation][' . ($row_count) . ']',
                  'model_name' => CHtml::modelName($element),
                  'removable' => true,
              )
          );
          $row_count++;
      }
      foreach ($operations as $i => $op) {
          if (!array_key_exists('object', $op)) {
              $this->render(
                  'PastSurgery_OperationEntry_event_edit',
                  array(
                      'values' => array(
                          'op' => $op,
                          'operation' => $op['operation'],
                          'form' => $form,
                          'model_name' => CHtml::modelName($element),
                          'side' => $op['side'],
                          'date' => $op['date'],
                      ),
                      'removable' => false,
                      'row_count' => ($row_count),
                      'field_prefix' => $model_name . '[operation][' . ($row_count) . ']',
                      'model_name' => CHtml::modelName($element),
                  )
              );
              $row_count++;
          }
      }
      ?>
      </tbody>
    </table>
    <div id="<?= $model_name ?>-comments" class="field-row-pad-top js-comment-container flex-layout flex-left"
         style="<?= $element->comments ? '' : 'display: none;' ?>" data-comment-button="#<?= $model_name ?>-comment-button">
      <br/>
        <?php echo $form->textArea(
            $element,
            'comments',
            array('nowrapper' => true),
            false,
            array(
                'class' => 'autosize js-comment-field',
                'placeholder' => $element->getAttributeLabel('comments'),
            )
        )
        ?>
      <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
    </div>
  </div>

  <div class="flex-item-bottom" id="add-to-past-surgery">
    <button id="<?= $model_name ?>-comment-button" class="button js-add-comments"
            data-comment-container="#<?= $model_name ?>-comments"
            style="<?= $element->comments ? 'display: none;' : '' ?>" type="button">
      <i class="oe-i comments small-icon"></i>
    </button>

    <button id="show-add-popup" class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>

    <div id="add-prev-surgery" class="oe-add-select-search auto-width" style="bottom: 61px; display: none;">
      <div id="close-btn" class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
      <button class="button hint green add-icon-btn"><i class="oe-i plus pro-theme"></i></button>
      <div class="flex-layout flex-top flex-left">
        <ul id="past-surgery-option" class="add-options cols-full" data-multi="true" data-clickadd="false">
            <?php
            $op_list = CommonPreviousOperation::model()->findAll(array('order' => 'display_order asc'));
            foreach ($op_list as $op_item) { ?>
              <li data-str="<?php echo $op_item->name; ?>" data-id="<?php echo $op_item->id; ?>">
                <span class="restrict-width"><?php echo $op_item->name; ?></span>
              </li>
            <?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<script type="text/template" id="<?= CHtml::modelName($element) . '_operation_template' ?>" class="hidden">
    <?php
    $empty_operation = new \OEModule\OphCiExamination\models\PastSurgery_Operation();
    $this->render(
        'PastSurgery_OperationEntry_event_edit',
        array(
            'op' => $empty_operation,
            'form' => $form,
            'model_name' => CHtml::modelName($element),
            'row_count' => '{{row_count}}',
            'field_prefix' => $model_name . '[operation][{{row_count}}]',
            'removable' => true,
            'values' => array(
                'id' => '',
                'previous_operation_id' => '',
                'operation' => '{{operation}}',
                'side_id' => '{{side_id}}',
                'side_display' => '{{side_display}}',
                'date' => '{{date}}',
                'date_display' => '{{date_display}}',
                'had_operation' => (string) PastSurgery_Operation::$PRESENT,
            ),
            'posted_not_checked' => false,

        )
    );
    ?>
</script>
<script type="text/javascript">
  $(function () {
    var controller;
    $(document).ready(function () {
      controller = new OpenEyes.OphCiExamination.PreviousSurgeryController();
    });


    var adder = $('#add-to-past-surgery');
    var popup = adder.find('#add-prev-surgery');

    function addSurgery(selection) {
      controller.addEntry();
    }

    setUpAdder(
      popup,
      'multi',
      addSurgery,
      adder.find('#show-add-popup'),
      popup.find('.add-icon-btn'),
      adder.find('#close-btn, .add-icon-btn')
    );
  });

</script>
