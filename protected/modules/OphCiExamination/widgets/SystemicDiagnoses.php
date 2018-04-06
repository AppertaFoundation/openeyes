<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
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

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\SystemicDiagnoses as SystemicDiagnosesElement;
use OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis;

class SystemicDiagnoses extends \BaseEventElementWidget
{
    /**
     * @var SystemicDiagnosesElement
     */
    public $element;

    /**
     * @return SystemicDiagnosesElement
     */
    protected function getNewElement()
    {
        return new SystemicDiagnosesElement();
    }

    /**
     * Pre-process to determine whether the element should be updating the patient level
     * information or not.
     *
     * @inheritdoc
     */
    protected function setElementFromDefaults()
    {
        $this->element->storePatientUpdateStatus();
        parent::setElementFromDefaults();
    }

    /**
     * Returns the required Disorders (Systemic Diagnoses)
     * @return array of Disorders
     */
    public function getRequiredSystemicDiagnoses()
    {
        $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
        return $exam_api->getRequiredSystemicDiagnoses($this->patient);
    }

    /**
     * Gets the required and missing Disorders(Systemic Diagnoses)
     * @return array of SystemicDiagnoses_Diagnosis
     */
    public function getMissingRequiredSystemicDiagnoses()
    {
        $current_ids = array_map(function ($e) { return $e->disorder_id; }, $this->element->diagnoses);
        $missing = array();

        foreach ($this->getRequiredSystemicDiagnoses() as $required) {
            if (!in_array($required->id, $current_ids)) {
                $entry = new SystemicDiagnoses_Diagnosis();
                $entry->disorder_id = $required->id;
                $missing[] = $entry;
            }
        }
        return $missing;
    }

    /**
     * @param SystemicDiagnosesElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if  (!is_a($element, 'OEModule\OphCiExamination\models\SystemicDiagnoses')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        // Ensure we track whether to update the secondary diagnoses for the patient
        // or not when we save this element.
        $element->storePatientUpdateStatus();

        // pre-cache current entries so any entries that remain in place will use the same db row
        $diagnoses_by_id = array();
        foreach ($element->diagnoses as $entry) {
            $diagnoses_by_id[$entry->id] = $entry;
        }

        if (array_key_exists('disorder_id', $data)) {
            $diagnoses = array();
            foreach ($data['disorder_id'] as $i => $disorder_id) {
                $diagnosis_entry = new SystemicDiagnoses_Diagnosis();
                $id = $data['id'][$i];
                if ($id && array_key_exists($id, $diagnoses_by_id)) {
                    $diagnosis_entry = $diagnoses_by_id[$id];
                }
                $diagnosis_entry->disorder_id = $disorder_id;
                $diagnosis_entry->side_id = $data['side_id'][$i];
                $diagnosis_entry->date = $data['date'][$i];
                $diagnosis_entry->has_disorder = $data['has_disorder'][$i];
                $diagnoses[] = $diagnosis_entry;
            }
            $element->diagnoses = $diagnoses;
        } else {
            $element->diagnoses = array();
        }
    }

    /**
     * Checks if there was a posted has_disoder value
     * @param $row
     * @return 0|1|-9 if posted oterwise NULL
     */
    public function getPostedCheckedStatus($row)
    {
        $value = \Helper::elementFinder(\CHtml::modelName($this->element) . ".has_disorder.$row", $_POST);
        return ( is_numeric($value) ? $value : null);
    }

}