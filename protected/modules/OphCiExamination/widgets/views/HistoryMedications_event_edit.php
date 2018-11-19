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
/** @var \OEModule\OphCiExamination\models\HistoryMedications $element */
$model_name = CHtml::modelName($element);
$route_options = CHtml::listData($element->getRouteOptions(), 'id', 'term');
$frequency_options = array();
foreach ($element->getFrequencyOptions() as $k=>$v) {
    $frequency_options[$v->id] = $v->term." (".$v->code.")";
}
$stop_reason_options = CHtml::listData($element->getStopReasonOptions(), 'id', 'name');
$element_errors = $element->getErrors();
$laterality_options = Chtml::listData($element->getLateralityOptions(), 'id', 'name');

?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryRisks.js') ?>"></script>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
  <div class="data-group flex-layout cols-10">
    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <table id="<?= $model_name ?>_entry_table" class=" cols-full <?php echo $element_errors ? 'highlighted-error error' : '' ?>">
      <colgroup>
        <col class="cols-2">
        <col class="cols-4">
        <col>
        <col>
        <col class="cols-1">
      </colgroup>
        <thead style= <?php echo !sizeof($element->entries)?  'display:none': ''; ?> >
        <tr>
            <th>
              <button class="button small show-stopped" type="button">show stopped</button>
              <button class="button small hide-stopped" type="button" style="display: none;">Hide stopped</button>
            </th>
            <th></th>
            <th>Start</th>
            <th>Stopped(Optional)</th>
            <th>Reason</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $row_count = 0;
        $total_count = count($element->entries);
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
                        'stop_reason_options' => $stop_reason_options,
                        'usage_type' => 'OphCiExamination',
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
                        'stop_reason_options' => $stop_reason_options,
                        'laterality_options' => $laterality_options,
                        'route_options' => $route_options,
                        'frequency_options' => $frequency_options,
                        'removable' => true,
                        'direct_edit' => false,
                        'usage_type' => 'OphCiExamination',
                        'row_type' => '',
                        'is_last' => ($row_count == $total_count - 1),
                        'is_new' => $entry->getIsNewRecord()
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
    <div class="add-data-actions flex-item-bottom" id="medication-history-popup">
      <button class="button hint green js-add-select-search" id="add-medication-btn" type="button">
        <i class="oe-i plus pro-theme"></i>
      </button>
    </div>
  </div>
    <script type="text/template" class="entry-template hidden" id="<?= CHtml::modelName($element).'_entry_template' ?>">
        <?php
        $empty_entry = new EventMedicationUse();
        $this->render(
            'HistoryMedicationsEntry_event_edit',
            array(
                'entry' => $empty_entry,
                'form' => $form,
                'model_name' => $model_name,
                'field_prefix' => $model_name . '[entries][{{row_count}}]',
                'row_count' => '{{row_count}}',
                'removable' => true,
                'direct_edit' => true,
                'route_options' => $route_options,
                'frequency_options' => $frequency_options,
                'stop_reason_options' => $stop_reason_options,
                'laterality_options' => $laterality_options,
                'usage_type' => 'OphCiExamination',
                'row_type' => 'new',
                'is_last' => false,
                'is_new' => true
            )
        );
        ?>
    </script>
</div>
<script type="text/javascript">
  var medicationsController;$(document).ready(function() {
    medicationsController =new OpenEyes.OphCiExamination.HistoryMedicationsController({
      element: $('#<?=$model_name?>_element'),
    onInit: function(controller) {
                registerElementController(controller, "HMController", "MMController");
                /* Don't add automatically
                if(typeof controller.MMController === "undefined" && $("#OEModule_OphCiExamination_models_MedicationManagement_element").length === 0)  {
                    var sidebar = $('aside.episodes-and-events').data('patient-sidebar');
                    sidebar.addElementByTypeClass('OEModule_OphCiExamination_models_MedicationManagement');
                }
                */
            }
        });

        $(document).on("click", ".alt-display-trigger", function(e){
           e.preventDefault();
           $(e.target).prev(".alternative-display").find(".textual-display").trigger("click");
        });

        window.switch_alternative = function(anchor) {
            var $wrapper = $(anchor).closest(".alternative-display-element");
            $wrapper.hide();
            $wrapper.siblings(".alternative-display-element").show();
            $wrapper.closest(".alternative-display").next(".alt-display-trigger").hide();};


      <?php
       $medications = RefMedication::model()->listBySubspecialtyWithCommonMedications($this->getFirm()->getSubspecialtyID() , true);?>
    new OpenEyes.UI.AdderDialog({
      openButton: $('#add-medication-btn'),
      itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($medications) ?>, {'multiSelect': true})],
      onReturn: function (adderDialog, selectedItems) {
        medicationsController.addEntry(selectedItems);
        return true;
      },
      searchOptions: {
        searchSource: medicationsController.options.searchSource,
      }
    });


  });
</script>
