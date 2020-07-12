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
$results = Yii::app()->db->createCommand()
    ->select('count(*) as count, set_id')
    ->from('ophcitheatreadmission_clinical_checklist_results')
    ->where('element_id = :element_id', array(':element_id' => $element->id))
    ->group('set_id')
    ->queryAll();
?>

<?php if (isset($results)) {
    foreach ($results as $result) {
        if ($result['count'] > 0) {
            if (($this->action->id === 'update') && (isset($removeLast) && $removeLast)) {
                array_pop($results);
            }
            $element1 = Element_OphCiTheatreadmission_ClinicalAssessment::model()
                ->with("checklistResults")
                ->find(array(
                        "condition" => "element_id = :element_id AND checklistResults.set_id = :set_id",
                        "params" => array(':element_id' => $element->id, ':set_id' => $result['set_id']),
                    ));
            if (isset($element1)) {
                $checklistResults = $element1->checklistResults;

                $this->renderPartial('checklist_view', array(
                        'element' => $element1,
                        'checklistResults' => $checklistResults,
                        'isCollapsable' => $isCollapsable ?? null,
                        'resultModel' => OphCiTheatreadmission_ClinicalChecklistResults::model(),
                        'setId' => $result['set_id'] ?? null,
                        'setIdName' => (isset($result['set_id']) ? ' (' . OphCiTheatreadmission_ElementSet::model()->findByPk($result['set_id'])->name . ')' : ''),
                    ));
                ?>
                <br/>
                <br/>
                <?php
            }
        }
    }
} ?>