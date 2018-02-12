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
  <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <table id="<?= $model_name ?>_operation_table" class="cols-10 <?= $model_name ?>_Operation">
        <thead>
        <tr>
            <th class="cols-3">Procedures</th>
            <th class="cols-3">Diagnoses</th>
            <th class="cols-1">Right</th>
            <th class="cols-1">Left</th>
            <th class="cols-1">Both</th>
            <th class="cols-1">None</th>
            <th>Date</th>
            <th>Notes</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $row_count = 0;

        // these are the missing but required to collect operations
        foreach ($this->getMissingRequiredOperation() as $i => $op) {
            $this->render(
                'PastSurgery_OperationEntry_event_edit',
                array(
                    'op' => $op,
                    'form' => $form,
                    'row_count' => ($row_count),
                    'field_prefix' => $model_name . '[operation][' . ($row_count) . ']',
                    'model_name' => CHtml::modelName($element),
                    //hack here: removable set to true as we need to edit the fields, 'required' introduced as we need to hide the remove btn.
                    'removable' => true,
                    'required' => true,
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count)
                )
            );
            $row_count++;
        }

        //$operations : operations that have been recorded as entries in this element + operations from op note
        foreach ($this->getOperationsArray() as $i => $op) {
            $this->render(
                'PastSurgery_OperationEntry_event_edit',
                array(
                    'op' => $op['op'],
                    'form' => $form,
                    'row_count' => ($row_count),
                    'field_prefix' => $model_name . '[operation][' . ($row_count) . ']',
                    'model_name' => CHtml::modelName($element),
                    'removable' => true,
                    //hack here: removable set to true as we need to edit the fields, 'required' introduced as we need to hide the remove btn.
                    'required' => $op['required'],
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count)
                )
            );
            $row_count++;
        }
        ?>
        </tbody>
    </table>
  <div class="flex-item-bottom" id="add-to-past-surgery" >
    <button class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>

    <div  class="oe-add-select-search auto-width" style="bottom: 61px; display: none;">
      <div class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
      <div class="select-icon-btn"><i class="oe-i menu selected"></i></div>
      <button class="button hint green add-icon-btn"><i class="oe-i plus pro-theme"></i></button>
      <table class="select-options">
        <tr>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul id="past-surgery-option" class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                      $op_list = CommonPreviousOperation::model()->findAll(array('order' => 'display_order asc'));
                      foreach ($op_list as $op_item) {
                      ?>
                          <li data-str="<?php echo $op_item->name; ?>" data-id="<?php echo $op_item->id; ?>">
                            <span class="restrict-width"><?php echo $op_item->name; ?></span>
                          </li>
                      <?php } ?>
              </ul>
            </div>
          <!-- flex layout -->
          </td>
        </tr>
      </table>
      <div class="search-icon-btn"><i class="oe-i search"></i></div>
      <div class="search-options" style="display:none;">
        <input type="text" class="cols-full js-search-autocomplete" placeholder="search for option (type 'auto-complete' to demo)">
        <!-- ajax auto-complete results, height is limited -->
      </div>
    </div>
  </div>
</div>
<script type="text/template" id="<?= CHtml::modelName($element).'_operation_template' ?>" class="hidden">
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
