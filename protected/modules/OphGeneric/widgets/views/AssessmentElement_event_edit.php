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
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphGeneric\models\AssessmentEntry;

if (isset($_POST['OEModule_OphGeneric_models_Assessment']['entries'])) {
    foreach ($_POST['OEModule_OphGeneric_models_Assessment']['entries'] as $assessment_id => $assessment_entry_side) {
        foreach ($assessment_entry_side as $eye_side => $assessment_entry_data) {
            ${$eye_side . "_assesssment_id"} = $assessment_id;
            ${$eye_side . "_assesssment_entry"} = AssessmentEntry::model()->findByPk($assessment_entry_data['entry_id']);
            ${$eye_side . "_assesssment_entry"}->attributes = $assessment_entry_data;
        }
    }
}
?>

<?= \CHtml::hiddenField('element_id', $element->id, array('class' => 'element_id')); ?>

<div class="element-data element-eyes js-oct-container">
    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) : ?>
        <div class="js-element-eye <?= $eye_side ?>-eye">
            <?php if ($element->hasEye($eye_side)) : ?>
                <div class="data-value">

                    <?php
                    if (isset($_POST['OEModule_OphGeneric_models_Assessment']['entries'])) {
                        if (isset(${$eye_side . "_assesssment_entry"})) {
                            $entry = ${$eye_side . "_assesssment_entry"};
                            $assessment_id = ${$eye_side . "_assesssment_id"};
                        }
                    } else {
                        $assessment = $this->getAssessment($this->element->event->id, Eye::getIdFromName($eye_side));
                        $entry = $assessment->readings[0];
                        $assessment_id = $assessment->id;
                    }

                    if (isset($entry)) {
                        $this->widget('application.modules.OphGeneric.widgets.Assessment', [
                            'assessment' => $element,
                            'entry' => $entry,
                            'patient' => $this->patient,
                            'event_type' => $this->controller->event->eventType,
                            'key' => $assessment_id,
                            'side' => $eye_side
                        ]);
                    } ?>

                </div>
            <?php else : ?>
                <div class="data-value not-recorded">
                    Not recorded
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>