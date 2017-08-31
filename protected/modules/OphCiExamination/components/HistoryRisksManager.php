<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\components;


use OEModule\OphCiExamination\models\HistoryRisksEntry;
use OEModule\OphCiExamination\models\OphCiExaminationRisk;
use OEModule\OphCiExamination\models\HistoryRisks;

class HistoryRisksManager
{
    private function getApi()
    {
        return \Yii::app()->moduleAPI->get('OphCiExamination');
    }

    private function getLatestElement($patient)
    {
        $api = $this->getApi();
        return $api->getLatestElement('OEModule\OphCiExamination\models\HistoryRisks', $patient);
    }

    protected function getRisksFromTagged($model, $ids) {
        $tag_ids = array();
        foreach ($model->with('tags')->findAllByPk($ids) as $obj) {
            $tag_ids = array_merge($tag_ids, array_map(function($t) { return $t->id; }, $obj->tags));
        }
        return OphCiExaminationRisk::findForTagIds($tag_ids);
    }

    protected function addRisksToPatient(\Patient $patient, $risks = array())
    {
        if ($risks) {
            $element = $this->getLatestElement($patient);
            $present_ids = $element ? array_map(function($r) { return $r->id; }, $element->present) : array();
            $missing_risks = array_filter($risks, function($r) use ($present_ids) { return !in_array($r->id, $present_ids); });
            if ($missing_risks) {
                $this->createRiskEvent($patient, $element, $missing_risks);
            }
        }
    }

    protected function getChangeEvent(\Patient $patient)
    {
        $episode = \Episode::getChangeEpisode($patient);

        if ($episode->isNewRecord) {
            $episode->save();
        }

        $event = new \Event();
        $event->episode_id = $episode->id;
        $event->event_type_id = $this->getApi()->getEventType()->id;
        $event->event_date = date('Y-m-d 00:00:00');
        $event->save();

        return $event;
    }

    /**
     * @param \Patient $patient
     * @param $current
     * @param $missing_risks
     */
    protected function createRiskEvent(\Patient $patient, $current, $missing_risks)
    {
        $event = $this->getChangeEvent($patient);
        $element = new HistoryRisks('auto');
        if ($current) {
            $element->loadFromExisting($current);
        }
        $entries = $element->entries;
        $element->event_id = $event->id;
        foreach ($missing_risks as $risk) {
            $entry = new HistoryRisksEntry('auto');
            $entry->risk_id = $risk->id;
            $entry->risk = $risk;
            $entry->has_risk = true;
            $entries[] = $entry;
        }
        $element->entries = $entries;
        $element->save();
    }

    /**
     * A relatively simple handler for receiving notifications that drugs and/or medication drugs
     * have been added to the patient, so the relevant risks should be stored on the patient.
     *
     * @param $params (['patient' => \Patient, 'drug_ids' => array(), 'medication_drug_ids' => array())
     * @throws \SystemException
     */
    public function addPatientMedicationRisks($params)
    {
        if (!array_key_exists('patient', $params)) {
            throw new \SystemException('Missing expected patient parameter for updating patient risks');
        }
        $risks = array();
        if (array_key_exists('drug_ids', $params)) {
            $risks = $this->getRisksFromTagged(\Drug::model(), $params['drug_ids']);
        }
        if (array_key_exists('medication_drug_ids', $params)) {
            $risks = array_merge($risks, $this->getRisksFromTagged(\MedicationDrug::model(), $params['medication_drug_ids']));
        }

        $this->addRisksToPatient($params['patient'], $risks);
    }
}