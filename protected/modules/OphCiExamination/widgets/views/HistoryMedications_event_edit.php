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
    <div class="field-row flex-layout">
        <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
        <input type="hidden" name="<?= $model_name ?>[do_not_save_entries]" class="do_not_save_entries" value="<?php echo (int)$element->do_not_save_entries; ?>"/>
        <table class="cols-full entries" id="<?= $model_name ?>_entry_table">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-4">
                    <col width="cols-3">
                    <col width="cols-3">
                    <col width="90">
                </colgroup>
                <thead>
                <tr>
                    <th>Drug</th>
                    <th>Dose/frequency/route</th>
                    <th><i class="oe-i start small pad"></i>Started</th>
                    <th><i class="oe-i stop small pad"></i>Stopped</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $row_count = 0;

                $entries = array_filter($element->entries, function($e){ return $e->end_date === null || $e->end_date === ''; });
                $total_count = count($entries);

                foreach ($entries as $entry) {
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
                            'is_last' => ($row_count == $total_count - 1)
                        )
                    );
                    $row_count++;
                }

                $closed_entries = array_filter($element->entries, function($e){ return $e->end_date !== null && $e->end_date !== ''; });

                ?>
                <?php if(!empty($closed_entries)): ?>
                    <tr class="ignore">
                        <td colspan="6" class="align-left">
                            <a href="javascript:void(0);" class="hide-stopped" style="display: none;">Hide Stopped / Changed</a>
                            <a href="javascript:void(0);" class="show-stopped">Show Stopped / Changed</a>
                        </td>
					</tr>
                <?php endif; ?>
                <?php
                foreach ($closed_entries as $entry) {
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
                            'removable' => false,
                            'direct_edit' => false,
                            'usage_type' => 'OphCiExamination',
                            'row_type' => 'closed',
                            'is_last' => false
                        )
                    );
                    $row_count++;
                }

                ?>
                </tbody>
            </table>
            <div class="flex-layout flex-right">
                <div class="flex-item-bottom" id="medication-history-popup">
                    <?php $this->widget('MedicationBrowser', [
                            'fnOnSelected' => 'function(medication){window.HMController.addEntry(medication);}',
                            'usage_code'=>'DrugHistory'
                    ]); ?>
                </div>
            </div>
    </div>
    
    <script type="text/template" class="entry-template hidden">
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
    $(document).ready(function () {
        new OpenEyes.OphCiExamination.HistoryMedicationsController({
            element: $('#<?=$model_name?>_element'),
            onInit: function(controller) {
                registerElementController(controller, "HMController", "MMController");
            },
            onAddedEntry: function($row, controller) {
                if(typeof controller.MMController !== "undefined") {
                    $new_row = controller.MMController.addEntry($row.data("medication_data"), false);
                    controller.bindEntries($row, $new_row);
                }
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
            $wrapper.closest(".alternative-display").next(".alt-display-trigger").hide();
        };
    });
</script>
