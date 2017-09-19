<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath('FamilyHistory.js') ?>"></script>
<div class="element-fields">
  <?php $model_name = CHtml::modelName($element); ?>
    <div class="field-row row<?= count($element->entries) ? ' hidden' : ''?>" id="<?=$model_name?>_no_family_history_wrapper">
        <div class="large-3 column">
            <label for="<?=$model_name?>_no_family_history">Confirm patient has no family history:</label>
        </div>
        <div class="large-2 column end">
            <?php echo CHtml::checkBox($model_name .'[no_family_history]', $element->no_family_history_date ? true : false); ?>
        </div>
    </div>

  <input type="hidden" name="<?= $model_name ?>[present]" value="1" />

  <table id="<?= $model_name ?>_entry_table" class="<?=$element->no_family_history_date ? 'hidden' :''?>">
      <thead>
      <tr>
          <th>Relative</th>
          <th>Side</th>
          <th>Condition</th>
          <th>Comments</th>
          <th>Action</th>
      </tr>
      </thead>
      <tbody>
          <?php
          $row_count = 0;
          foreach ($element->entries as $entry) {
              $this->render(
                  'FamilyHistory_Entry_event_edit',
                  array(
                      'entry' => $entry,
                      'form' => $form,
                      'model_name' => $model_name,
                      'editable' => true,
                      'relative_options' => $element->getRelativeOptions(),
                      'side_options' => $element->getSideOptions(),
                      'condition_options' => $element->getConditionOptions(),
                      'field_prefix' => $model_name . '[entries][' . ($row_count) . ']',
                      'row_count' => $row_count,
                  )
              );
              $row_count++;
          }
          ?>
      </tbody>
      <tfoot>
          <tr>
              <td colspan="4"></td>
              <td class="text-right"><button class="button small primary" id="<?= $model_name ?>_add_entry">Add</button></td>
          </tr>
      </tfoot>
  </table>
</div>

<script type="text/template" id="<?= CHtml::modelName($element).'_entry_template' ?>" class="hidden">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\FamilyHistory_Entry();
    $this->render(
        'FamilyHistory_Entry_event_edit',
        array(
            'entry' => $empty_entry,
            'form' => $form,
            'model_name' => $model_name,
            'editable' => true,
            'relative_options' => $element->getRelativeOptions(),
            'side_options' => $element->getSideOptions(),
            'condition_options' => $element->getConditionOptions(),
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'row_count' => '{{row_count}}',
            'values' => array(
                'id' => '',
                'relative_id' => '{{relative_id}}',
                'relative_display' => '{{relative_display}}',
                'other_relative' => '{{other_relative}}',
                'side_id' => '{{side_id}}',
                'side_display' => '{{side_display}}',
                'condition_id' => '{{condition_id}}',
                'condition_display' => '{{condition_display}}',
                'other_condition' => '{{other_condition}}',
                'comments' => '{{comments}}',
            )
        )
    );
    ?>
</script>
<script type="text/javascript">
    $(document).ready(function() {
        new OpenEyes.OphCiExamination.FamilyHistoryController();
    });
</script>
