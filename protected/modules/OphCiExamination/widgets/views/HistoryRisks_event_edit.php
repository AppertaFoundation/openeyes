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

use OEModule\OphCiExamination\models\HistoryRisksEntry;

?>

<?php
$model_name = CHtml::modelName($element);
$risks_options = $this->getRiskOptions();
$missing_req_risks = $this->getMissingRequiredRisks();
$required_risk_ids = array_map(function ($r) {
    return $r->id;
}, $this->getRequiredRisks());
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryRisks.js') ?>"></script>

<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_element">
  <div class="data-group cols-10">
    <div
        class="cols-full <?= (count($element->entries) + count($missing_req_risks)) ? ' hidden' : '' ?> <?= $model_name ?>_no_risks_wrapper">
      <label for="<?= $model_name ?>_no_risks">Confirm patient has no risks:</label>
        <?=\CHtml::checkBox($model_name . '[no_risks]', $element->no_risks_date ? true : false,
            array('class' => $model_name . '_no_risks')); ?>
    </div>

    <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>

    <table
        class="<?= $model_name ?>_entry_table cols-full <?= !count($element->entries) && !count($missing_req_risks) ? 'hidden' : '' ?>">
			<colgroup>
				<col class="cols-3">
				<col class="cols-4">
				<col class="cols-4">
				<col class="cols-1">
			</colgroup>
      <tbody>
      <?php
      $row_count = 0;
      foreach ($missing_req_risks as $entry) {
          $this->render(
              'HistoryRisksEntry_event_edit',
              array(
                  'entry' => $entry,
                  'form' => $form,
                  'model_name' => $model_name,
                  'field_prefix' => $model_name . '[entries][' . $row_count . ']',
                  'row_count' => $row_count,
                  'removable' => false,
                  'posted_not_checked' => $element->widget->postedNotChecked($row_count),
              )
          );
          $row_count++;
      } ?>
      <?php
      foreach ($element->entries as $entry) {
          $this->render(
              'HistoryRisksEntry_event_edit',
              array(
                  'entry' => $entry,
                  'form' => $form,
                  'model_name' => $model_name,
                  'field_prefix' => $model_name . '[entries][' . $row_count . ']',
                  'row_count' => $row_count,
                  'removable' => !in_array($entry->risk_id, $required_risk_ids),
                  'risks' => $risks_options,
                  'posted_not_checked' => $element->widget->postedNotChecked($row_count),
              )
          );
          $row_count++;
      }
      ?>
      </tbody>
    </table>
  </div>
  <div class="add-data-actions flex-item-bottom" id="add-history-risk-popup"
       style="visibility: <?php echo $element->no_risks_date ? 'hidden' : ''; ?>">
    <button id="show-add-risk-popup" class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>

    <div id="add-history-risks" class="oe-add-select-search auto-width" style="bottom: 61px; display: none;">
      <div id="close-btn" class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
      <button class="button hint green add-icon-btn" type="button"><i class="oe-i plus pro-theme"></i></button>
      <div class="flex-layout flex-top flex-left">
        <ul id="history-risks-option" class="add-options cols-full" data-multi="true" data-clickadd="false">
            <?php
            $exist_risks = array();
            foreach ($element->entries as $entry) {
                array_push($exist_risks, $entry->risk_id);
            }
            foreach ($risks_options as $risk_item) {
                if (!in_array($risk_item->id, $exist_risks)) {
                    ?>
                  <li data-str="<?php echo $risk_item->name; ?>" data-id="<?php echo $risk_item->id; ?>">
                    <span class="restrict-width"><?php echo $risk_item->name; ?></span>
                  </li>
                <?php }
            } ?>
        </ul>
      </div>
    </div>
  </div>
  <script type="text/template" class="<?= CHtml::modelName($element) . '_entry_template' ?> hidden">
      <?php
      $empty_entry = new \OEModule\OphCiExamination\models\HistoryRisksEntry();
      $this->render(
          'HistoryRisksEntry_event_edit',
          array(
              'entry' => $empty_entry,
              'form' => $form,
              'model_name' => $model_name,
              'field_prefix' => $model_name . '[entries][{{row_count}}]',
              'row_count' => '{{row_count}}',
              'removable' => true,
              'risks' => $risks_options,
              'posted_not_checked' => false,
              'values' => array(
                  'id' => '',
                  'risk_id' => '{{risk_id}}',
                  'risk_display' => '{{risk_display}}',
                  'other' => null,
                  'comments' => '{{comments}}',
                  'has_risk' => (string)HistoryRisksEntry::$PRESENT,
              ),
          )
      );
      ?>
  </script>
</div>


<script type="text/javascript">
  var controller;
  $(document).ready(function () {
    controller = new OpenEyes.OphCiExamination.HistoryRisksController({
      element: $('#<?=$model_name?>_element')
    });
  });

  var adder = $('#add-history-risk-popup');
  var popup = adder.find('#add-history-risks');

  function addRisks(selection) {
    controller.addEntry();
  }

  setUpAdder(
    popup,
    'multi',
    addRisks,
    adder.find('#show-add-risk-popup'),
    popup.find('.add-icon-btn'),
    adder.find('#close-btn, .add-icon-btn')
  );
</script>
