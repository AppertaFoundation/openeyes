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
 * @var Element_OphTrOperationchecklists_Admission $element
 */
?>
<?php $questions = OphTrOperationchecklists_Questions::model()->findAll(array('order' => 'display_order', 'condition' => 'element_type_id = :element_type_id', 'params' =>  array(':element_type_id' => $element->getElementType()->id)));
$name_stub = CHtml::modelName($element) . '[checklistResults]';
$model_relation = 'Element_OphTrOperationchecklists_Admission_checklistResults_';

$api = Yii::app()->moduleAPI->get('OphInLabResults');
$eventId = $this->event->id ?? null;
$covid19Result = $api->getLabResultTypeResult($this->patient->id, $eventId, "COVID-19");
?>
<div class="element-fields full-width">
    <?php $this->renderPartial('checklist_edit', array(
        'results' => $element->checklistResults,
        'questions' => $questions,
        'name_stub' => $name_stub,
        'model_relation' => $model_relation,
        'result_model' => OphTrOperationchecklists_AdmissionResults::model(),
        'starting_index' => $questions[0]->id,
    ));
?>
</div>
<script>
    $(document).ready(function() {
        let patientMedicationQuestionId = "<?= OphTrOperationchecklists_Questions::model()->find('question="Are there any changes to the patient\'s medication?"')->id; ?>";
        let yesAnswerId = "<?= OphTrOperationchecklists_Answers::model()->find('answer="Yes"')->id; ?>";
        let noAnswerId = "<?= OphTrOperationchecklists_Answers::model()->find('answer="No"')->id; ?>";
        let nAAnswerId = "<?= OphTrOperationchecklists_Answers::model()->find('answer="N/A"')->id; ?>";
        let htmlToAppend = '<tr><td colspan="12" id="flash-draft" class="alert-box alert">Please record changes under medication history in an examination event.</td></tr>';

        let $yes = $(`#<?=$model_relation?>${patientMedicationQuestionId}_answer_id_${yesAnswerId}`);
        if ($yes.is(':checked')) {
            let $tr = $(`input:radio[name="<?=$name_stub?>[${patientMedicationQuestionId}][answer_id]"]`).closest("tr");
            $tr.before(htmlToAppend);
            $tr.addClass('no-line');
        }

        $(`input:radio[name="<?=$name_stub?>[${patientMedicationQuestionId}][answer_id]"]`).change(
            function(){
                let $tr = $(this).closest("tr");
                if ($(this).val() === yesAnswerId) {
                    $tr.before(htmlToAppend);
                    $tr.addClass('no-line');
                }
                else if (($(this).val() === noAnswerId) || ($(this).val() === nAAnswerId)){
                    $('#flash-draft').remove();
                    $tr.removeClass('no-line');
                }
            }
        );

        // COVID-19 Question
        let covid19LabResult = <?= json_encode($covid19Result); ?>;
        if (covid19LabResult) {
            const commentHtml = `<td>${covid19LabResult.comment}</td>`
            $("td:contains(COVID-19)").next().append(covid19LabResult.result);
            $("td:contains(COVID-19)").closest("tr").append(commentHtml);
        } else {
            const htmlString = `Not recorded
                                <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="COVID-19
                                result is not recorded for this patient. This can be recorded in the Lab Results
                                event."></i>`;
            const commentHtml = `<td></td>`
            $("td:contains(COVID-19)").next().append(htmlString);
            $("td:contains(COVID-19)").closest("tr").append(commentHtml);
        }
    });
</script>