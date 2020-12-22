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
 * @var Element_OphTrOperationchecklists_ClinicalAssessment $element
 */

$questions = OphTrOperationchecklists_Questions::model()->findAll(array('order' => 'display_order', 'condition' => 'element_type_id = :element_type_id', 'params' =>  array(':element_type_id' => $element->getElementType()->id)));
$name_stub = CHtml::modelName($element) . '[checklistResults]';

// Render the view
$results = Yii::app()->db->createCommand()
    ->select('count(*) as count, set_id')
    ->from('ophtroperationchecklists_clinical_results')
    ->where('element_id = :element_id', array(':element_id' => $element->id))
    ->group('set_id')
    ->queryAll();

// check if the event is in draft mode.
$eventDraft = OphTrOperationchecklists_Event::model()->find('event_id = :event_id', array(':event_id' => $this->event->id))->draft;

if ($eventDraft) {
    if (count($results) > 1 ) {
        $this->renderPartial('view_' . CHtml::modelName($element), array(
            'element' => $element,
            'isCollapsable' => $isCollapsable,
            'questions' => $questions,
            'removeLast' => true,
        ));
        echo '<br>';
        echo '<br>';
    }
} else {
    if (count($results) > 0 ) {
        $this->renderPartial('view_' . CHtml::modelName($element), array(
            'element' => $element,
            'isCollapsable' => $isCollapsable,
            'questions' => $questions,
        ));
        echo '<br>';
        echo '<br>';
    }
}

$response = OphTrOperationchecklists_Questions::model()->getSavedResponses(get_class($element), $this->event->id, $this->getNextStep());

$this->renderPartial('checklist_edit', array(
    'response' => $response,
    'results' => $element->checklistResults,
    'questions' => $questions,
    'name_stub' => $name_stub,
    'model_relation' => 'Element_OphTrOperationchecklists_ClinicalAssessment_checklistResults_',
    'result_model' => OphTrOperationchecklists_ClinicalResults::model(),
    'starting_index' => $questions[0]->id,
));
