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
 * @var Element_OphTrOperationchecklists_ProcedureList $element
 */

//adding Anaestethic JS
$url = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.OphTrOperationnote.assets.js'), true);
Yii::app()->clientScript->registerScriptFile($url . '/OpenEyes.UI.OphTrOperationnote.Anaesthetic.js');
Yii::app()->clientScript->registerScript(
    'AnaestheticController',
    'new OpenEyes.OphTrOperationnote.AnaestheticController({ typeSelector: \'#Element_OphTrOperationchecklists_ProcedureList_AnaestheticType\'});',
    CClientScript::POS_END
);

?>
    <div class="element-fields full-width">
        <table class="cols-full last-left">
            <colgroup>
                <col class="cols-2">
                <col class="cols-3">
                <col class="cols-2">
                <col class="cols-5">
            </colgroup>
            <tbody>
                <tr>
                    <td>
                        <?php echo $form->hiddenInput($element, 'booking_event_id') ?>
                        <?php echo $form->radioButtons(
                            $element,
                            'eye_id',
                            $element->getEyeOptions(),
                            ($element->eye ? (intval($element->eye->id) === Eye::BOTH ? Eye::RIGHT : $element->eye->id) : null),
                            null,
                            null,
                            null,
                            true,
                            array('nowrapper' => true),
                            array()
                        ) ?>
                    </td>
                    <td>
                        Procedure(s):
                    </td>
                    <td colspan="4">
                        <?php $form->widget('application.widgets.ProcedureSelection', array(
                            'element' => $element,
                            'selected_procedures' => $element->procedures,
                            'newRecord' => true,
                            'durations' => true,
                            'showEstimatedDuration' => false,
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
                <tr>
                    <td>
                        Anaesthetic Type
                    </td>
                    <td colspan="2">
                        <?php echo $form->checkBoxes(
                            $element,
                            'AnaestheticType',
                            'anaesthetic_type',
                            null,
                            false,
                            false,
                            false,
                            false,
                            array('label-class' => $element->getError('anaesthetic_type') ? 'error' : ''),
                            array('field' => 12)
                        ); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

<?php $diagnoses = CommonOSELECT * FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_NAME LIKE '%%'phthalmicDisorder::getList(
    Firm::model()->findByPk($this->selectedFirmId),
    false,
    true,
    $this->patient
); ?>
<script type="text/javascript">
    let elementClass = '<?= get_class($element) ?>';
    let $anaestheticTypeElementId = $('#' + elementClass + '_AnaestheticType');
    let anaestheticTypeQuestionRelationIds;

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

    function hideQuestions(clearPostData) {
       anaestheticTypeQuestionRelationIds.forEach(function(id) {
           $('[name *= "Element_OphTrOperationchecklists_Admission[checklistResults][' + id + '][answer"][type!="hidden"]').closest('tr').hide();
           $('[name = "Element_OphTrOperationchecklists_Admission[checklistResults][' + id + '][answer_id]"]').prop('checked', false);
           $('[name = "Element_OphTrOperationchecklists_Admission[checklistResults][' + id + '][answer]"]').val('');
           if (clearPostData) {
               $('[name = "Element_OphTrOperationchecklists_Admission[checklistResults][' + id + '][answer_id]"][type="hidden"]').val('');
           }
       });
    }

    function showQuestions() {
        anaestheticTypeQuestionRelationIds.forEach(function(id) {
            $('[name *= "Element_OphTrOperationchecklists_Admission[checklistResults][' + id + '][answer"][type!="hidden"]').closest('tr').show();
        });
    }

    function toggleAdmissionChecklistQuestions(clearPostData, element) {
        let $fieldset = $anaestheticTypeElementId;

        let $LA = $fieldset.find('.LA'),
            $sedation = $fieldset.find('.Sedation'),
            $GA = $fieldset.find('.GA'),
            $no_anaesthetic = $fieldset.find('.NoAnaesthetic');

        if (element) {
            if( $(element).hasClass('NoAnaesthetic')){
                $(element).prop('checked', true);
                $($LA).prop('checked', false);
                $($sedation).prop('checked', false);
                $($GA).prop('checked', false);
            }
        }

        if (($LA.is(':checked') || $no_anaesthetic.is(':checked')) && (!$GA.is(':checked') && !$sedation.is(':checked'))) {
            hideQuestions(clearPostData);
        } else {
            if (($GA.is(':checked') || $sedation.is(':checked'))) {
                showQuestions();
            } else {
                hideQuestions(clearPostData);
            }
        }
    }

    $anaestheticTypeElementId.on('change', 'input', function () {
        toggleAdmissionChecklistQuestions(true, this);
    });

    $(document).ready(function () {
        anaestheticTypeQuestionRelationIds = <?= json_encode(OphTrOperationchecklists_Questions::getAnaestheticTypeQuestionRelationIds()); ?>;
        toggleAdmissionChecklistQuestions(false);
    });
</script>

