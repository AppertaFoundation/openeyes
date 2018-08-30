<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
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
/** @var \OEModule\OphCiExamination\models\MedicationManagement $element */
$model_name = CHtml::modelName($element);


$route_options = CHtml::listData($element->getRouteOptions(), 'id', 'term');
$frequency_options = array();
foreach ($element->getFrequencyOptions() as $k=>$v) {
    $frequency_options[$v->id] = $v->term." (".$v->code.")";
}
$stop_reason_options = CHtml::listData($element->getStopReasonOptions(), 'id', 'name');

$laterality_options = Chtml::listData($element->getLateralityOptions(), 'id', 'name');


$element_errors = $element->getErrors();
?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
    <div class="field-row flex-layout">
        <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
        <table class="cols-full entries" id="<?= $model_name ?>_entry_table">
            <colgroup>
                <col class="cols-2">
                <col class="cols-4">
                <col>
                <col>
                <col>
                <col>
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>Drug</th>
                <th>Dose/frequency/route</th>
                <th>Started<span class="has-tooltip fa fa-info-circle right" style="margin-top:3px"  data-tooltip-content="Day, Month and Year fields are optional."></span></th>
                <th>Stopped<span class="has-tooltip fa fa-info-circle right" style="margin-top:3px"  data-tooltip-content="Day, Month and Year fields are optional."></span></th>
                <th>Cnt</th>
                <th>Px</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <?php if($this->isPostedEntries() || !empty($element->entries)) {
                    $row_count = 0;
                    $total_count = count($element->entries);
                    foreach ($element->entries as $key=>$entry) {

                            $this->render(
                                'MedicationManagementEntry_event_edit',
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
                                    'direct_edit' => false,
                                    'usage_type' => /* $entry->usage_type */ 'UTYPE',
                                    'row_type' => /*$entry->group */ 'group',
                                    'removable' => /* $entry->group === "new" */ "old",
                                    'is_last' => ($row_count == $total_count - 1),
                                    'prescribe_access' => $prescribe_access
                                )
                            );
                            $row_count++;
                    }
                } ?>

            </tbody>
        </table>

    </div>
    <div class="flex-layout flex-right">
        <div class="flex-item-bottom">
            <button class="button hint small primary js-add-select-search pull-right" type="button" id="MedicationManagemenet_open_btn">
                Add
            </button>
        </div>
    </div>
    <script type="text/template" class="entry-template hidden">
        <?php
        $empty_entry = new \OEModule\OphCiExamination\models\MedicationManagementEntry();

        $this->render(
            'MedicationManagementEntry_event_edit',
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
                'is_new' => true,
                'prescribe_access' => $prescribe_access
            )
        );
        ?>
    </script>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        $(".OEModule_OphCiExamination_models_MedicationManagement .js-remove-element").click(function(){
            // History Medications now should save entries
            window.HMController.setDoNotSaveEntries(false);
            // When element is removed, unbind connections to History Meds
            unregisterElementController(window.MMController, "MMController", "HMController");
        });

        window.MMController = new OpenEyes.OphCiExamination.HistoryMedicationsController({
            element: $('#<?=$model_name?>_element'),
            modelName: "<?=$model_name?>",
            onInit: function(controller) {
                registerElementController(controller, "MMController", "HMController");
            },
            onControllerBinded: function(controller, name) {
                if(name === "HMController") {
                    this.initRowsFromHistoryElement();
                }
            },
            initRowsFromHistoryElement: function() {
                <?php if(!$this->isPostedEntries() && $this->element->getIsNewRecord()): ?>
                $.each(window.HMController.$table.find("tbody").find("tr").not(".ignore-for-real"), function(i, e){
                    var $row = $(e);
                    var data = window.HMController.getRowData($row);

                    var hidden = $row.hasClass("ignore");
                    data.hidden = hidden ? 1 : 0;

                    data.is_new = 0;
                    var newrow = window.MMController.createRow(data);
                    var $newrow = $(newrow);

                    $newrow.removeClass("new");

                    if(hidden) {
                        $newrow.addClass("hidden");
                    }

                    $newrow.find(".trash").remove();
                    $newrow.appendTo(window.MMController.$table.find("tbody"));
                    window.HMController.bindEntries($row, $newrow);

                    window.MMController.setRowData($newrow, data);

                    $newrow.find(".rgroup").val("inherited");
                    window.MMController.initialiseRow($newrow);
                    window.MMController.switchRowToTextualDisplay($newrow);
                });
                <?php endif; ?>
                window.HMController.setDoNotSaveEntries(true);
                //this.onAddedEntry();
            },
            onAddedEntry: function($row, controller) {
                /*
                if(typeof controller.HMController !== "undefined") {
                    $new_row = controller.HMController.addEntry($row.data("medication_data"), false);
                    controller.bindEntries($row, $new_row);
                }
                */
            },
            onRemovedEntry: function($row, controller) {
                /*
                $tbody = window.MMController.$table.find("tbody");
                $tbody.find("tr.divide").removeClass("divider");
                $tbody.find("tr.new").last().addClass("divider");
                */
            }
        });
    });
</script>
