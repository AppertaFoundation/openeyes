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
    /**
     * @return OphCiExamination_API
     */
    protected function getApi()
    {
        return \Yii::app()->moduleAPI->get('OphCiExamination');
    }

    /**
     * @param $patient
     * @return mixed
     */
    protected function getLatestElement($patient)
    {
        $api = $this->getApi();
        return $api->getLatestElement('models\HistoryRisks', $patient);
    }

    /**
     * @param $tagged_list
     * @return array|mixed|null
     */
    protected function getRisksFromTagged($tagged_list) {
        $by_id = array();
        foreach ($tagged_list as $tagged) {
            $tag_ids = array_map(function($t) { return $t->id; }, $tagged->tags);
            $risks = OphCiExaminationRisk::findForTagIds($tag_ids);
            foreach ($risks as $risk) {
                if (!array_key_exists($risk->id, $by_id)) {
                    $by_id[$risk->id] = array('risk' => $risk, 'comments_list' => array());
                }
                $by_id[$risk->id]['comments_list'][] = (string)$tagged;
            }
        }
        $res = array();
        foreach (array_keys($by_id) as $id) {
            $res[] = array(
                'risk' => $by_id[$id]['risk'],
                'comments' => implode(', ', $by_id[$id]['comments_list'])
            );
        }
        return $res;
    }

    /**
     * @param \Patient $patient
     * @param array $risks - ['risk' => OphCiExaminationRisk, 'comments' => string][]
     */
    protected function addRisksToPatient(\Patient $patient, $risks = array())
    {
        if ($risks) {
            $element = $this->getLatestElement($patient);
            $present_ids = $element ? array_map(function($r) { return $r->id; }, $element->present) : array();
            $missing_risks = array_filter($risks, function($r) use ($present_ids) { return !in_array($r['risk']->id, $present_ids); });
            if ($missing_risks) {
                $this->createRiskEvent($patient, $element, $missing_risks);
            }
        }
    }

    /**
     * @param \Patient $patient
     * @return \Event
     */
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
     * @param $missing_risks - ['risk' => OphCiExaminationRisk, 'comments' => string][]
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
            $entry->risk_id = $risk['risk']->id;
            $entry->risk = $risk['risk'];
            $entry->has_risk = true;
            $entry->comments = $risk['comments'];
            $entries[] = $entry;
        }
        $element->entries = $entries;
        $element->save();
    }

    /**
     * A relatively simple handler for receiving notifications that drugs and/or medication drugs
     * have been added to the patient, so the relevant risks should be stored on the patient.
     *
     * @param $params (['patient' => \Patient, 'drugs' => \Drug[], 'medication_drugs' => \MedicationDrug[])
     * @throws \SystemException
     */
    public function addPatientMedicationRisks($params)
    {
        if (!array_key_exists('patient', $params)) {
            throw new \SystemException('Missing expected patient parameter for updating patient risks');
        }
        $risks = array();
        if (array_key_exists('drugs', $params)) {
            $risks = $this->getRisksFromTagged($params['drugs']);
        }
        if (array_key_exists('medication_drug_ids', $params)) {
            $risks = array_merge($risks, $this->getRisksFromTagged(['medication_drugs']));
        }

        $this->addRisksToPatient($params['patient'], $risks);
    }
}