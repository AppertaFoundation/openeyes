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

use OEModule\OphCiExamination\models\OphCiExamination_ElementSet;

/* @var $step OphCiExamination_ElementSet */

$items = $step->items;
if ($step->display_order_edited) {
    /* Move new items at the end of the list if there is an order already saved */
    for ($i = 0; $i < count($items); $i++) {
        if (empty($items[$i]->display_order)) {
            $temp = $items[$i];
            unset($items[$i]);
            $items[] = $temp;
        }
    }
}

?>

<div class="box admin">
    <div class="data-group">
        <div class="column cols-2">
            <?=\CHtml::textField('step_name', $step->name, array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')))?>
            <?=\CHtml::hiddenField('display_order_edited', $step->display_order_edited)?>
        </div>
        <div class="column cols-2 end">
            <?php echo EventAction::button('Save', 'save_step_name', null, array('class' => 'small'))->toHtml()?>
        </div>
  </div>
  <div id="workflow-flash" class="alert-box info" style="display: none">Workflow saved</div>
  <i class="spinner loader" style="display: none;"></i>
    <form id="admin_workflow_steps">
    <div class="data-group">
      <table class="standard"  id="et_sort" data-uri = "/OphCiExamination/admin/sortWorkflowElementSetItem">
        <thead>
        <tr>
          <th>Display Order</th>
          <th>Element type</th>
          <th>Hidden</th>
          <th>Mandatory</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody class="sortable ui-sortable">
        <?php foreach ($items as $i => $item) { ?>
          <tr class="clickable" data-id="<?php echo $item->id?>">
              <td class="reorder">
                  <span>&uarr;&darr;</span>
                  <input type="hidden" name="OphCiExamination_ElementSetItem[display_order][]" value="<?= $item->id ?>">
              </td>
            <td><?php echo $item->element_type->name?></td>
            <td><?=\CHtml::activeCheckBox($item, "[$i]is_hidden", array('class' => 'workflow-item-attr'))?></td>
            <td><?=\CHtml::activeCheckBox($item, "[$i]is_mandatory", array('class' => 'workflow-item-attr'))?></td>
            <td><a href="#" class="removeElementType" rel="<?php echo $item->id?>" data-element-type-id="<?php echo $item->element_type_id?>">Remove</a></td>
          </tr>
        <?php } ?>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
          <td colspan="5">
            <div class="grid-view">
              <div id="workflow-edit-controls" class="data-group">
                <div>
                    <?=\CHtml::dropDownList('element_type_id', '', CHtml::listData($element_types, 'id', 'name'), array('empty' => '- Select -'))?>
                    <?php echo EventAction::button('Add element type', 'add_element_type', null, array('class' => 'small'))->toHtml()?>
                    <button class="small button header-tab hint red" style="<?= ($step->display_order_edited == 0) ? 'display:none' : '' ?>" name="reset_workflow" data-element_set_id="<?=$step->id?>" type="submit" id="et_workflow_contextual_button">
                    <?= ($step->display_order_edited == 1) ? 'Reset element order to default' : '' ?>
              </button>
                </div>
              </div>
              <div>

              </div>
            </div>
          </td>
        </tr>
        </tfoot>
      </table>
    </div>
    </form>
</div>
