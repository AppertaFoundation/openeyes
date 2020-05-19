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

// FP10 settings
$fpten_setting = SettingMetadata::model()->getSetting('prescription_form_format');
$overprint_setting = SettingMetadata::model()->getSetting('enable_prescription_overprint');
$fpten_dispense_condition = OphDrPrescription_DispenseCondition::model()->findByAttributes(array('name' => 'Print to {form_type}'));

$dispense_condition_options = array(
    $fpten_dispense_condition->id => array('label' => "Print to $fpten_setting")
);
// End of FP10 settings

$route_options = CHtml::listData($element->getRouteOptions(), 'id', 'term');
$frequency_options = array();
foreach ($element->getFrequencyOptions() as $k => $v) {
    $frequency_options[$v->id] = $v->term." (".$v->code.")";
}
$stop_reason_options = CHtml::listData($element->getStopReasonOptions(), 'id', 'name');

$laterality_options = Chtml::listData($element->getLateralityOptions(), 'id', 'name');
$unit_options = CHtml::listData(MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")->medicationAttributeOptions, 'description', 'description');

$element_errors = $element->getErrors();
$read_only = $element->event ? date('Y-m-d', strtotime($element->event->event_date)) != date('Y-m-d') : false;
?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryRisks.js') ?>"></script>
<?php if ($read_only) {
    \Yii::app()->user->setFlash('alert.read_only', 'Medication Management cannot be edited for past events');
}?>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
    <div class="data-group">
        <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
        <table class="medications entries js-entry-table js-current-medications"
                             id="<?= $model_name ?>_entry_table">
            <colgroup>
                <col class="cols-2">
                <col class="cols-6">
                <col class="cols-3">
                <col class="cols-icon" span="2">
            </colgroup>
            <thead>
                <tr>
                    <th>Drug</th>
                    <th>Dose/frequency/route/start/stop</th>
                    <th>Duration/dispense/comments</th>
                    <th><i class="oe-i drug-rx small no-click"></i></th>
                    <th><!-- actions --></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($this->isPostedEntries() || !empty($element->entries)) {
                    $row_count = 0;
                    $total_count = count($element->entries);
                    foreach ($element->entries as $key => $entry) {
                        if (!$read_only) {
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
                                        'patient' => $this->patient,
                                        'locked' => $entry->locked,
                                        'unit_options' => $unit_options,
                                        'has_dose_unit_term' => '{{has_dose_unit_term}}',
                                        'is_template' => false,
                                        'fpten_setting' => $fpten_setting,
                                        'overprint_setting' => $overprint_setting,
                                        'fpten_dispense_condition' => $fpten_dispense_condition,
                                        'dispense_condition_options' => $dispense_condition_options
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
                                    'patient' => $this->patient,
                                    'locked' => $entry->locked,
                                    'unit_options' => $unit_options,
                                    'form_setting'=> $fpten_setting
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
            <button id="mm-add-standard-set-btn" class="button hint green <?php if ($read_only) {
                ?>disabled<?php
                                                                          } ?>" type="button">Add standard set</button>
            <button class="button hint green js-add-select-search <?php if ($read_only) {
                ?>disabled<?php
                                                                  } ?>" id="mm-add-medication-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button>
        </div>
    </div>
    <div class="oe-popup-wrap" id="js-save-mm-event" style="z-index:100; display: none">
        <div class="oe-popup">
            <div class="title">
                <i class="oe-i triangle large selected pro-theme"></i>
                Reason required
            </div>
            <div class="oe-popup-content">
                <div class="alert-box alert">
                    <strong>
                        Please select a reason for changing the prescription from the list below:
                    </strong>
                </div>
                <br>
                <?= CHtml::dropDownList($model_name . '[prescription_reason]', '', CHtml::listData(OphDrPrescriptionEditReasons::model()->findAll(['order' => 'display_order', 'condition' => 'active = 1']), 'id', 'caption'), array('empty' => '- Reason -', 'class' => 'cols-4')) ?>
                <input type="text" id="reason_other_text" name="<?= $model_name ?>[reason_other]" style="display: none"/>
                <button id="submit_reason">
                    <i class="oe-i tick large"></i>
                </button>
                <button id="cancel_reason">
                    <i class="oe-i remove large"></i>
                </button>
            </div>
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
                'patient' => $this->patient,
                'locked' => '{{locked}}{{^locked}}0{{/locked}}',
                'source_subtype' => '{{source_subtype}}',
                'unit_options' => $unit_options,
                'has_dose_unit_term' => '{{has_dose_unit_term}}',
                'is_template' => true,
                'fpten_setting' => $fpten_setting,
                'overprint_setting' => $overprint_setting,
                'fpten_dispense_condition' => $fpten_dispense_condition,
                'dispense_condition_options' => $dispense_condition_options
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
                    "model_name" => $model_name,
                    "row_count" => "{{row_count}}",
                    "taper_count" => "{{taper_count}}",
                    "field_prefix" => $model_name."[entries][{{row_count}}][taper][{{taper_count}}]"
                )
            );
            ?>
    </script>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        let prescribed_medications = [];
        let select_fields_selectors = ['.js-frequency', '.js-route', '.js-duration', '.js-dispense-condition', '.js-dispense-location'];
        let prescription_event_exists = false;

        $('.js-entry-table tr.js-first-row:not("new")').find('[name$="prescribe]"]').each(function () {
            if ($(this).prop('checked')) {
                prescribed_medications.push($(this).parents('tr.js-first-row'));
            }
        });

        prescribed_medications.forEach(function (medication) {
            if($(medication).find('[name*="prescription_item_id"]').val()){
                prescription_event_exists = true;
            }
        });

        if (prescription_event_exists) {
            let $save_button = $('#et_save');
            $save_button.before("<button class='button header-tab green' id='et_save_check_prescription_reason'>Save</button>");
            $save_button.hide();
        }


        $('#et_save_check_prescription_reason').on('click', function () {
            let prescription_modified = false;

            //check if old prescribed medications have been modified
            prescribed_medications.forEach(function (medication) {
                let $dose = $(medication).find('.js-dose');
                if ($dose.prop("defaultValue") !== $dose.val()) {
                    prescription_modified = true;
                }

                select_fields_selectors.forEach(function (selector) {
                    let $select_field = $(medication).find(selector);
                    let $previous_option;

                    $select_field.find('option').each(function () {
                        if (this.defaultSelected) {
                            $previous_option = $(this);
                        }
                    });

                    if(typeof $previous_option !== 'undefined' && $previous_option.val() !== $select_field.val()) {
                        prescription_modified = true;
                    }
                });

            });

            //check if new prescribed medications have been added
            let $new_prescribed_medications = [];
            $('.js-entry-table tr.js-first-row.new').find('[name$="prescribe]"]').each(function () {
                if ($(this).prop('checked')) {
                    $new_prescribed_medications.push($(this).parents('tr.js-first-row'));
                }
            });

            if ($new_prescribed_medications.length > 0) {
                prescription_modified = true;
            }

            //check if some old prescribed medication has been deleted
            let prescribed_medications_check = [];
            $('.js-entry-table tr.js-first-row:not("new")').find('[name$="prescription_item_id]"]').each(function () {
                if ($(this).val()) {
                    prescribed_medications_check.push($(this).parents('tr.js-first-row'));
                }
            });
            if (prescribed_medications_check.length !== prescribed_medications.length) {
                prescription_modified = true;
            }

            if (prescription_modified) {
                $('#js-save-mm-event').show();
            } else {
                $('#et_save').trigger('click');
            }

        });

        $('#submit_reason').on('click', function () {
            $('#js-save-mm-event').hide();
        });

        $('#cancel_reason').on('click', function (e) {
            // do not continue to save
            e.preventDefault();
            $('#js-save-mm-event').hide();
        });

        $('[name$="prescription_reason]"]').on('change', function () {
            let $reason_other_text = $('#reason_other_text');
            if ($(this).val() === "1") {
                $reason_other_text.show();
            } else {
                $reason_other_text.text('');
                $reason_other_text.hide();
            }
        });

        $('#<?= $model_name ?>_element').closest('section').on('element_removed', function() {
            $('.js-change-event-date').removeClass('disabled');
            if (typeof window.HMController !== "undefined") {
                window.HMController.$table.find('tr').each(function () {
                    if (typeof $(this).data('bound_entry') !== 'undefined') {
                        $(this).removeData('bound_entry');
                    }
                });
            }
            $('#et_save_check_prescription_reason').hide();
            $('#et_save').show();
            delete window.MMController;
        });

        window.MMController = new OpenEyes.OphCiExamination.HistoryMedicationsController({
            element: $('#<?=$model_name?>_element'),
            modelName: '<?=$model_name?>',
            patientAllergies: <?= CJSON::encode($this->patient->getAllergiesId()) ?>,
            allAllergies: <?= CJSON::encode(CHtml::listData(\OEModule\OphCiExamination\models\OphCiExaminationAllergy::model()->findAll(), 'id', 'name')) ?>,

            onInit: function (controller) {
                registerElementController(controller, "MMController", "HMController");
                $('section[data-element-type-class="OEModule_OphCiExamination_models_MedicationManagement"]').data("controller", controller);
            },
            onControllerBound: function (controller, name) {
                if (name === "HMController") {
                    this.initRowsFromHistoryElement();
                }
            },
            initRowsFromHistoryElement: function () {

                let copyFields = <?=!$this->isPostedEntries() && $this->element->getIsNewRecord() ? 'true' : 'false'?>;
                $.each(window.HMController.$table.children("tbody").children("tr.js-first-row"), function (i, historyMedicationRow) {
                    let medicationHistoryBoundKey = $(historyMedicationRow).find('.js-bound-key').val();
                    let rowNeedsCopying = true;
                    let $medicationManagementRow;

                    $.each(window.MMController.$table.children("tbody").children("tr.js-first-row"), function (index, medicationManagementRow) {
                        if (medicationHistoryBoundKey && $(medicationManagementRow).find('.js-bound-key').val() === medicationHistoryBoundKey) {
                            window.HMController.bindEntries($(historyMedicationRow), $(medicationManagementRow), false);
                            window.MMController.disableRemoveButton($(medicationManagementRow));
                            rowNeedsCopying = false;
                            $medicationManagementRow = $(medicationManagementRow);

                                                }
                    });

                    let historyMedicationKey = $(historyMedicationRow).data('key');
                    let $historyMedicationFullRow = window.HMController.$table.find('tr[data-key=' + historyMedicationKey + ']');


                    if (copyFields && rowNeedsCopying) {
                        $medicationManagementRow = window.HMController.copyRow($historyMedicationFullRow, window.MMController.$table.children("tbody"));
                        window.HMController.bindEntries($(historyMedicationRow), $medicationManagementRow);
                    }

                    let hidden = (
                        $(historyMedicationRow).find(".js-to-be-copied").val() === "false" ||
                                                $(historyMedicationRow).find(".js-to-be-copied").val() === "0"
                                        );
                    if (hidden) {
                        if(typeof $medicationManagementRow !== "undefined") {
                            $medicationManagementRow.addClass("hidden");
                            $medicationManagementRow.find(".js-hidden").val("1");
                        }
                    }
                });

                window.HMController.setDoNotSaveEntries(true);
                //this.onAddedEntry();
            }
        });

        <?php
        $common_systemic = Medication::model()->listCommonSystemicMedications(true);
        foreach ($common_systemic as &$medication) {
            $medication['prepended_markup'] = $this->widget('MedicationInfoBox', array('medication_id' => $medication['id']), true);
        }

        $firm_id = $this->getApp()->session->get('selected_firm_id');
        $site_id = $this->getApp()->session->get('selected_site_id');
        if ($firm_id) {
            /** @var Firm $firm */
            $firm = $firm_id ? Firm::model()->findByPk($firm_id) : null;
            $subspecialty_id = $firm->getSubspecialtyID();
            $common_ophthalmic = Medication::model()->listBySubspecialtyWithCommonMedications($subspecialty_id, true, $site_id);
            foreach ($common_ophthalmic as &$medication) {
                $medication['prepended_markup'] = $this->widget('MedicationInfoBox', array('medication_id' => $medication['id']), true);
            }
        } else {
            $common_ophthalmic = array();
        }

        ?>
        new OpenEyes.UI.AdderDialog({
            openButton: $('#mm-add-medication-btn'),
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode($common_systemic) ?>,
                    {'multiSelect': true, header: "Common Systemic"}
                )
                ,
                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode($common_ophthalmic) ?>,
                    {'multiSelect': true, header: "Common Ophthalmic"}
                    )
            ],
            onReturn: function (adderDialog, selectedItems) {
                window.MMController.addEntriesWithAllergyCheck(selectedItems);
                return true;
            },
            searchOptions: {
                searchSource: window.MMController.options.searchSource,
            },
            enableCustomSearchEntries: true,
            searchAsTypedItemProperties: {id: "<?php echo EventMedicationUse::USER_MEDICATION_ID ?>"},
            booleanSearchFilterEnabled: true,
            booleanSearchFilterLabel: 'Include brand names',
            booleanSearchFilterURLparam: 'include_branded'
        });

        new OpenEyes.UI.AdderDialog({
            openButton: $('#mm-add-standard-set-btn'),
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($drugSet) {
                    return [
                        'label' => $drugSet->name,
                        'id' => $drugSet->id
                    ];
                }, Element_OphDrPrescription_Details::model()->drugSets())
            ) ?>,{'header': 'Set name',})],
            onReturn: function (adderDialog, selectedItems) {
                selectedItems.forEach(function(item) {
                    window.MMController.processSetEntries(item.id);
                });
            }
        });

        let $changeEventDate = $('.js-change-event-date');
        $changeEventDate.addClass('disabled');
        if ($changeEventDate.is(":hidden")) {
            $('.js-event-date-input').hide();
            $changeEventDate.show();
            $('.js-event-date').show();
                }

        let elementHasRisks = <?= $element->hasRisks() ? 1 : 0 ?>;
        if (elementHasRisks && !$('.' + OE_MODEL_PREFIX + 'HistoryRisks').length) {
            $('#episodes-and-events').data('patient-sidebar').addElementByTypeClass(OE_MODEL_PREFIX + 'HistoryRisks', undefined);
        }
    });
</script>
