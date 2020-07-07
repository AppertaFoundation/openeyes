<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var Element_OphCiTheatreadmission_ProcedureList $element
 */

?>
    <div class="element-fields full-width">
        <table class="cols-full last-left">
            <colgroup>
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-2">
                <col class="cols-6">
            </colgroup>
            <tbody>
                <tr>
                    <td>
                        Eye
                    </td>
                    <td>
                        <?php echo $form->hiddenInput($element, 'booking_event_id') ?>
                        <?php echo $form->radioButtons(
                            $element,
                            'eye_id',
                            $element->getEyeOptions(),
                            ($element->eye ? (intval($element->eye->id) === Eye::BOTH ? EYE::RIGHT : $element->eye->id) : null),
                            null,
                            null,
                            null,
                            true,
                            array('nowrapper' => true),
                            array()
                        ) ?>
                    </td>
                    <td>
                        <div class="cols-2">
                            Procedure(s):
                        </div>
                    </td>
                    <td>
                        <?php $form->widget('application.widgets.ProcedureSelection', array(
                            'element' => $element,
                            'selected_procedures' => $element->procedures,
                            'newRecord' => true,
                            'last' => true,
                            'label' => '',
                        ));
?>
                        <style>
                            #typeProcedure {align-items: flex-start;}
                            #procedure-selector-container {padding-right: 28px;}
                            #procedure-selector-container fieldset{min-width: 100%}
                            #select_procedure_id_procs {min-width: 100%; max-width: 100%;}
                        </style>
                    </td>
                </tr>
                <tr>
                    <td>
                        Diagnosis
                    </td>
                    <td colspan="2">
                        <div class="panel diagnosis hide large-text" id="enteredDiagnosisText">
                            <?= isset($element->disorder) ? $element->disorder->term : 'Please use the + button to add a listing diagnosis'?>
                        </div>
                        <?php $form->hiddenInput($element, 'disorder_id');?>
                    </td>
                    <td>
                        <div class="add-data-actions flex-item-bottom" id="operation-booking-diagnoses-popup">
                            <button class="button hint green js-add-select-search" type="button" id="add-operation-booking-diagnosis">
                                <i class="oe-i plus pro-theme"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Priority
                    </td>
                    <td>
                        <?php echo $form->radioButtons(
                            $element,
                            'priority_id',
                            CHtml::listData(
                                OphTrOperationbooking_Operation_Priority::model()->notDeletedOrPk($element->priority_id)->findAll(array('order' => 'display_order asc')),
                                'id',
                                'name'
                            ),
                            ($element->priority ? ($element->priority->id) : null),
                            false,
                            false,
                            false,
                            false,
                            array('nowrapper' => true)
                        ) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

<?php $diagnoses = CommonOphthalmicDisorder::getList(
    Firm::model()->findByPk($this->selectedFirmId),
    false,
    true,
    $this->patient
); ?>
<script type="text/javascript">
    new OpenEyes.UI.AdderDialog({
        openButton: $('#add-operation-booking-diagnosis'),
        itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($id, $label) {
                return ['label' => $label, 'id' => $id];
            }, array_keys($diagnoses), $diagnoses)
        ) ?>)],
        onReturn: function (adderDialog, selectedItems) {
            $('#enteredDiagnosisText').html(selectedItems[0].label);
            $('[id$="disorder_id"]').val(selectedItems[0].id);
        },
        searchOptions: {
            searchSource: '/disorder/autocomplete'
        }
    });
</script>

