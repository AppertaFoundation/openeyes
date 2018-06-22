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
?>

<?php

$model_name = CHtml::modelName($element);
$route_options = CHtml::listData($element->getRouteOptions(), 'id', 'name');
$frequency_options = CHtml::listData($element->getFrequencyOptions(), 'id', 'name');
$stop_reason_options = CHtml::listData($element->getStopReasonOptions(), 'id', 'name');
$element_errors = $element->getErrors();
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryRisks.js') ?>"></script>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
  <div class="field-row flex-layout">
    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <table id="<?= $model_name ?>_entry_table" class=" cols-10 <?php echo $element_errors ? 'highlighted-error' : '' ?>">
        <thead class="row" style= <?php echo !sizeof($element->entries)?  'display:none': ''; ?> >
        <tr>
            <th class="cols-2">
              <button class="button small show-stopped" type="button">show stopped</button>
              <button class="button small hide-stopped" type="button" style="display: none;">Hide stopped</button>
            </th>
            <th class="cols-5"></th>
            <th>Start</th>
            <th>Stopped(Optional)</th>
            <th class="cols-1">Reason</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $row_count = 0;
        foreach ($element->entries as $entry) {
            if ($entry->prescription_item_id) {
                $this->render(
                    'HistoryMedicationsEntry_prescription_event_edit',
                    array(
                        'entry' => $entry,
                        'form' => $form,
                        'model_name' => $model_name,
                        'field_prefix' => $model_name . '[entries][' . $row_count . ']',
                        'row_count' => $row_count,
                        'stop_reason_options' => $stop_reason_options
                    )
                );
            } else {
                $this->render(
                    'HistoryMedicationsEntry_event_edit',
                    array(
                        'entry' => $entry,
                        'form' => $form,
                        'model_name' => $model_name,
                        'field_prefix' => $model_name . '[entries][' . $row_count . ']',
                        'row_count' => $row_count,
                        'removable' => true,
                        'route_options' => $route_options,
                        'frequency_options' => $frequency_options,
                        'stop_reason_options' => $stop_reason_options
                    )
                );
            }
            $row_count++;
        }
        ?>
        </tbody>
    </table>
  </div>
  <div class="flex-layout flex-right">
    <div class="flex-item-bottom" id="medication-history-popup">
      <button class="button hint green js-add-select-search" id="add-medication-btn" type="button">
        <i class="oe-i plus pro-theme"></i>
      </button>

      <div id="add-to-medication" class="oe-add-select-search" style="display: none;">
        <!-- icon btns -->
        <div class="close-icon-btn" id="history-medication-close-popup" type="button"><i class="oe-i remove-circle medium"></i></div>
        <div class="select-icon-btn" type="button"><i id="history-medication-select-btn" class="oe-i menu"></i></div>
        <button class="button hint green add-icon-btn" type="button">
          <i class="oe-i plus pro-theme"></i>
        </button>
        <!-- select (and search) options for element -->
        <table class="select-options" id="history-medication-select-options">
          <tbody>
          <tr>
            <td>
              <div class="flex-layout flex-top flex-left">
                <ul class="add-options" data-multi="true" data-clickadd="false" id="history-medication-option">
                    <?php $medications = Drug::model()->listBySubspecialtyWithCommonMedications($this->getFirm()->getSubspecialtyID());
                    foreach ($medications as $id=>$medication) { ?>
                      <li data-str="<?php echo $medication ?>" data-id="<?php echo $id?>">
                        <span class="auto-width">
                          <?php echo $medication; ?>
                        </span>
                      </li>
                    <?php } ?>
                </ul>
              </div>
              <!-- flex-layout -->
            </td>
          </tr>
          </tbody>
        </table>
        <div class="search-icon-btn"><i id="history-medication-search-btn" class="oe-i search"></i></div>
        <div class="history-medication-search-options" style="display: none;">
          <table class="cols-full last-left">
            <thead>
            <tr>
              <th>
                <input id="history-medication-search-field"
                       class="search"
                       placeholder="Search for Drug"
                       type="text">
              </th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>
                <ul id="history-medication-search-results" class="add-options" data-multi="true" style="width: 100%;">
                </ul>
                <span id="history-medication-search-no-results">No results found</span>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div><!-- oe-add-select-search -->

    </div>
  </div>
    <script type="text/template" class="entry-template hidden">
        <?php
        $empty_entry = new \OEModule\OphCiExamination\models\HistoryMedicationsEntry();
        $this->render(
            'HistoryMedicationsEntry_event_edit',
            array(
                'entry' => $empty_entry,
                'form' => $form,
                'model_name' => $model_name,
                'field_prefix' => $model_name . '[entries][{{row_count}}]',
                'row_count' => '{{row_count}}',
                'removable' => true,
                'route_options' => $route_options,
                'frequency_options' => $frequency_options,
                'stop_reason_options' => $stop_reason_options,
                'values' => array(
                    'id' => '',
                    'drug_id' => '{{drug_id}}',
                    'medication_drug_id' => '{{medication_drug_id}}' ,
                    'medication_name' => '{{medication_name}}',
                    )
            )
        );
        ?>
    </script>
</div>


<script type="text/javascript">
  var medicationsController;
  $(document).ready(function() {
    medicationsController = new OpenEyes.OphCiExamination.HistoryMedicationsController({
      element: $('#<?=$model_name?>_element')
    });
  });

  var popup = $('#add-to-medication');

  function addMedication() {
    medicationsController.addEntry();
  }

  setUpAdder(
    popup,
    'multi',
    addMedication,
    $('#add-medication-btn'),
    popup.find('.add-icon-btn'),
    $('#history-medication-close-popup, .add-icon-btn')
  );
</script>