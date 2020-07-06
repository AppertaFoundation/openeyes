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
 * @var Element_OphCiTheatreadmission_Discharge $element
 */
?>
<?php
$questions = OphcitheatreadmissionChecklistQuestions::model()->findAll(array('order' => 'display_order', 'condition' => 'element_type_id = :element_type_id', 'params' =>  array(':element_type_id' => $element->getElementType()->id)));
$name_stub = CHtml::modelName($element) . '[dischargeChecklistResults]';
?>
<?php $this->renderPartial('checklist_edit', array(
    'results' => $element->dischargeChecklistResults,
    'questions' => $questions,
    'name_stub' => $name_stub,
    'model_relation' => 'Element_OphCiTheatreadmission_Discharge_dischargeChecklistResults_',
    'result_model' => OphCiTheatreadmission_DischargeChecklistResults::model(),
    'starting_index' => $questions[0]->id,
));
?>
<script type="text/javascript">

    // get the id of the first question
    let firstQuestionId = <?php echo json_encode($questions[0]->id); ?>;
    // Now get the answers containing the response.
    let yesResponseElementId = "Element_OphCiTheatreadmission_Discharge_dischargeChecklistResults_" + firstQuestionId + "_answer_id_1";
    let noResponseElementId = "Element_OphCiTheatreadmission_Discharge_dischargeChecklistResults_" + firstQuestionId + "_answer_id_2";

    let $hideQuestionElementClass = '.js-hide-question';

    $(document).ready(function() {

        $("#" + yesResponseElementId).change(function(){
            hideQuestions(this);
        });

        $("#" + noResponseElementId).change(function(){
            showQuestions(this);
        });

        hideQuestions($("#" + yesResponseElementId));
        showQuestions($("#" + noResponseElementId));

    });

    function hideQuestions($element) {
        if ($($element).prop("checked")) {
            $($hideQuestionElementClass).hide();
        }
    }

    function showQuestions($element) {
        if ($($element).prop("checked")) {
            $($hideQuestionElementClass).show();
        }
    }
</script>
