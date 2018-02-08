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
<div class="element-fields flex-layout full-width">
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

  <table id="<?= $model_name ?>_entry_table" class="cols-10 <?=$element->no_family_history_date ? 'hidden' :''?>">
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
  </table>
  <div class="flex-item-bottom" id="family-history-popup">
    <button class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
    <div id="add-to-family-history" class="oe-add-select-search auto-width" style="bottom: 61px; display: none;">
      <div class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
      <div class="select-icon-btn"><i class="oe-i menu selected"></i></div>
      <button class="button hint green add-icon-btn"><i class="oe-i plus pro-theme"></i></button>
      <table class="select-options">
        <tr>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul id='family-history-relative' class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  $relative_list = $element->getRelativeOptions();
                  foreach ($relative_list as $relative_item) {
                      ?>
                    <li data-str="<?php echo $relative_item->name; ?>" data-id="<?php echo $relative_item->id; ?>">
                      <span class="restrict-width"><?php echo $relative_item->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul id='family-history-side' class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  $side_options = $element->getSideOptions();
                  foreach ($side_options as $side_item) {
                      ?>
                    <li data-str="<?php echo $side_item->name; ?>" data-id="<?php echo $side_item->id; ?>">
                      <span class="restrict-width"><?php echo $side_item->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul id='family-history-condition' class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  $condition_list =$element->getConditionOptions();
                  foreach ($condition_list as $condition_item) {
                      ?>
                    <li data-str="<?php echo $condition_item->name; ?>" data-id="<?php echo $condition_item->id; ?>">
                      <span class="restrict-width"><?php echo $condition_item->name; ?></span>
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
        <input type="text" class="cols-full js-search-autocomplete" placeholder="search for option">
        <!-- ajax auto-complete results, height is limited -->
      </div>
    </div>
  </div>
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
