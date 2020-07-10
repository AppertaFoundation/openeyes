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
 * @var Element_OphCiTheatreadmission_ClinicalAssessment $element
 */
?>
<?php
$questions = OphcitheatreadmissionChecklistQuestions::model()->findAll(array('order' => 'display_order', 'condition' => 'element_type_id = :element_type_id', 'params' =>  array(':element_type_id' => $element->getElementType()->id)));
$name_stub = CHtml::modelName($element) . '[checklistResults]';
// Render the view
$this->renderPartial('view_' . CHtml::modelName($element), array(
        'element' => $element,
        'isCollapsable' => $isCollapsable,
        'removeLast' => true,
    ));
?>
<?php $this->renderPartial('checklist_edit', array(
    'results' => $element->checklistResults,
    'questions' => $questions,
    'name_stub' => $name_stub,
    'model_relation' => 'Element_OphCiTheatreadmission_ClinicalAssessment_checklistResults_',
    'result_model' => OphCiTheatreadmission_ClinicalChecklistResults::model(),
    'starting_index' => $questions[0]->id,
));
