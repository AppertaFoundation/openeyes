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
foreach ($element->getFrequencyOptions() as $k => $v) {
    $frequency_options[$v->id] = $v->term." (".$v->code.")";
}
$stop_reason_options = CHtml::listData($element->getStopReasonOptions(), 'id', 'name');

$laterality_options = Chtml::listData($element->getLateralityOptions(), 'id', 'name');
$unit_options = CHtml::listData(MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")->medicationAttributeOptions, 'description', 'description');

$element_errors = $element->getErrors();
?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
    <div class="field-row flex-layout full">
        <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
        <table class="cols-full entries js-entry-table <?php echo $element_errors ? 'highlighted-error error' : '' ?>"
                             id="<?= $model_name ?>_entry_table cols-full">
            <colgroup>
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>
            <thead>
            <tr>
                <th>Drug</th>
                <th>Dose/frequency/route</th>
                <th>Started</th>
                <th>Stopped</th>
                <th>Reason</th>
                <th>Duration</th>
                <th>Disp. cond.</th>
                <th>Disp. loc.</th>
                <th>
                    <i class="oe-i drug-rx small no-click"></i>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <?php if ($this->isPostedEntries() || !empty($element->entries)) {
                    $row_count = 0;
                    $total_count = count($element->entries);
                    foreach ($element->entries as $key => $entry) {
                        if ($prescribe_access || $entry->prescribe == 0 ) {
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
                                                        'is_last' => ($row_count == $total_count - 1),
                                                        'prescribe_access' => $prescribe_access,
                                                        'patient' => $this->patient,
                                                        'locked' => $entry->locked,
                                                        'unit_options' => $unit_options,
                                                    )
                                                );
                        } else {
                            $this->render(
                            'MedicationManagementEntry_event_edit_read_only',
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
                                    'is_last' => ($row_count == $total_count - 1),
                                    'prescribe_access' => $prescribe_access,
                                    'patient' => $this->patient,
                                    'locked' => $entry->locked,
                                    'unit_options' => $unit_options,
                                )
                            );
                        }
                            $row_count++;
                    }
                } ?>

            </tbody>
        </table>

    </div>
    <div class="flex-layout flex-right">
        <div class="add-data-actions flex-item-bottom" id="medication-history-popup">
            <button class="button hint green js-add-select-search" id="mm-add-medication-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
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
                'direct_edit' => true,
                'route_options' => $route_options,
                'frequency_options' => $frequency_options,
                'stop_reason_options' => $stop_reason_options,
                'laterality_options' => $laterality_options,
                'usage_type' => 'OphCiExamination',
                'row_type' => 'new',
                'is_last' => false,
                'is_new' => true,
                'prescribe_access' => $prescribe_access,
                'patient' => $this->patient,
                'locked' => '{{locked}}{{^locked}}0{{/locked}}',
                'unit_options' => $unit_options,
            )
        );
        ?>
    </script>
    <script type="text/template" class="taper-template hidden">
        <?php
            $empty_entry = new OphDrPrescription_ItemTaper();

            $this->render(
                "MedicationManagementEntryTaper_event_edit",
                array(
                    "element" => $element,
                    "entry" => $empty_entry,
                    "row_count" => "{{row_count}}",
                    "taper_count" => "{{taper_count}}",
                    "field_prefix" => $model_name."[entries][{{row_count}}][taper][{{taper_count}}]"
                )
            );
            ?>
    </script>
</div>
<script type="text/javascript">

    $(document).ready(function() {

        $('#<?= $model_name ?>_element').closest('section').on('element_removed', function() {
            $('.js-change-event-date').removeClass('disabled');
        });

        window.MMController =new OpenEyes.OphCiExamination.HistoryMedicationsController({
            element: $('#<?=$model_name?>_element'),
            modelName: '<?=$model_name?>',
            patientAllergies: <?= CJSON::encode($this->patient->getAllergiesId()) ?>,
            allAllergies: <?= CJSON::encode(CHtml::listData(\OEModule\OphCiExamination\models\OphCiExaminationAllergy::model()->findAll(), 'id', 'name')) ?>,

            onInit: function(controller) {
                registerElementController(controller, "MMController", "HMController");
                $('section[data-element-type-class="OEModule_OphCiExamination_models_MedicationManagement"]').data("controller", controller);
            },
            onControllerBound: function(controller, name) {
                if(name === "HMController") {
                    this.initRowsFromHistoryElement();
                }
            },
            initRowsFromHistoryElement: function() {

                <?php if (!$this->isPostedEntries() && $this->element->getIsNewRecord()) : ?>
                    $.each(window.HMController.$table.children("tbody").children("tr"), function(i, e){
                        var $newrow = window.HMController.copyRow($(e), window.MMController.$table.children("tbody"));
                        window.HMController.bindEntries($(e), $newrow);

                        var hidden = ($(e).find(".js-to-be-copied").val() == 0);
                        if(hidden) {
                            $newrow.addClass("hidden");
                            $newrow.find(".js-hidden").val("1");
                        }
                    });
                <?php else : ?>
                $.each(window.HMController.$table.children("tbody").children("tr"), function(i, historyMedicationRow){
                    let medicationHistoryBindedKey = $(historyMedicationRow).find('.js-binded-key').val();
                    $.each(window.MMController.$table.children("tbody").children("tr"), function(index, medicationManagementRow) {
                        if($(medicationManagementRow).find('.js-binded-key').val() === medicationHistoryBindedKey) {
                            window.HMController.bindEntries($(historyMedicationRow), $(medicationManagementRow), false);
                            window.MMController.disableRemoveButton($(medicationManagementRow));
                                                }
                    });
                });
                <?php endif; ?>

                window.HMController.setDoNotSaveEntries(true);
                //this.onAddedEntry();
            }
        });

        <?php
            $site_id = $this->getApp()->session->get('selected_site_id');
            $medications = Medication::model()->listBySubspecialtyWithCommonMedications($this->getFirm()->getSubspecialtyID(), true, $site_id);
        foreach ($medications as &$medication) {
            $medication['prepended_markup'] = $this->widget('MedicationInfoBox', array('medication_id' => $medication['id']), true);
        }
        ?>

        new OpenEyes.UI.AdderDialog({
            openButton: $('#mm-add-medication-btn'),
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($medications) ?>, {'multiSelect': true})],
            onReturn: function (adderDialog, selectedItems) {
                window.MMController.addEntriesWithAllergyCheck(selectedItems);
                return true;
            },
            searchOptions: {
                searchSource:  window.MMController.options.searchSource,
            },
            booleanSearchFilterEnabled: true,
            booleanSearchFilterLabel: 'Include branded',
            booleanSearchFilterURLparam: 'include_branded'
        });

        let $changeEventDate = $('.js-change-event-date');
        $changeEventDate.addClass('disabled');
        if($changeEventDate.is(":hidden")) {
            $('.js-event-date-input').hide();
            $changeEventDate.show();
            $('.js-event-date').show();
                }
    });
</script>