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
    <div class= row<?= count($element->entries) ? ' hidden' : ''?>" id="<?=$model_name?>_no_family_history_wrapper">
      <label for="<?=$model_name?>_no_family_history">Confirm patient has no family history:</label>
        <?php echo CHtml::checkBox($model_name .'[no_family_history]', $element->no_family_history_date ? true : false); ?>
    </div>

  <input type="hidden" name="<?= $model_name ?>[present]" value="1" />

  <table id="<?= $model_name ?>_entry_table" class="cols-10 <?= !count($element->entries) ? 'hidden' :''?>">
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

  <div class="add-data-actions flex-item-bottom" id="add-family-history-popup" style="display: <?= $element->no_family_history_date ? 'none' : 'block' ?>">
    <button class="button hint green js-add-new-row" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
    <div id="add-family-history" class="oe-add-select-search auto-width" style="display:none;">
      <!-- icon btns -->
      <div class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
      <button class="button hint green add-icon-btn" type="button">
        <i class="oe-i plus pro-theme"></i>
      </button><!-- select (and search) options for element -->
      <table class="select-options">
        <thead>
        <tr>
          <th>Relative</th>
          <th>Side</th>
          <th>Condition</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <?php
          $relative_options = $element->getRelativeOptions();
          $side_options = $element->getSideOptions();
          $condition_options = $element->getConditionOptions();
          $options_list = array('relative'=>$relative_options, 'side'=>$side_options, 'condition'=>$condition_options);
          foreach ($options_list as $key=>$options) { ?>
            <td><!-- flex layout only required IF I have more than 1 <ul> list (see Refraction in Examination) -->
              <div class="flex-layout flex-top flex-left">
                <div>
                  <ul class="add-options  <?= $key ?>" data-multi="false" data-clickadd="false">
                      <?php foreach ($options as $option_item) { ?>
                        <li data-str="<?= $option_item->name ?>" data-id="<?= $option_item->id?>">
                          <span class="auto-width"><?= $option_item->name ?></span>
                        </li>
                      <?php } ?>
                  </ul>
                </div>
              </div> <!-- flex-layout -->
            </td>
          <?php } ?>
        </tr>
        </tbody>
      </table>
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
                'other_relative' => null,
                'side_id' => '{{side_id}}',
                'side_display' => '{{side_display}}',
                'condition_id' => '{{condition_id}}',
                'condition_display' => '{{condition_display}}',
                'other_condition' => null,
                'comments' => '{{comments}}',
            )
        )
    );
    ?>
</script>
<script type="text/javascript">
    $(document).ready(function() {
       var controller =  new OpenEyes.OphCiExamination.FamilyHistoryController();

       var adder = $('#add-family-history-popup');
       var popup = adder.find('#add-family-history');

       function addFamilyHistory() {
         controller.addEntry();
       }

       setUpAdder(
         popup,
         'single',
         addFamilyHistory,
         adder.find('.js-add-new-row'),
         popup.find('.add-icon-btn'),
         adder.find('.close-icon-btn')
       );
    });
</script>
