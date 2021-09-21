<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\components;


use OEModule\OphCiExamination\models\HistoryRisksEntry;
use OEModule\OphCiExamination\models\OphCiExaminationRisk;
use OEModule\OphCiExamination\models\HistoryRisks;

class HistoryRisksManager
{
    protected $api;
    /**
     * @return OphCiExamination_API
     */
    protected function getApi()
    {
        if (!$this->api) {
            $this->api = \Yii::app()->moduleAPI->get('OphCiExamination');
        }
        return $this->api;
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
     * @param \Medication[] $tagged_list
     * @return array|mixed|null
     */
    protected function getRisksFromTagged($tagged_list)
    {
        $by_id = array();
        foreach ($tagged_list as $tagged) {
            $medication_set_ids = array_map(function ($e) {
                return $e->id;
            }, $tagged->medicationSets);
            $risks = OphCiExaminationRisk::findForMedicationSetIds($medication_set_ids);
            foreach ($risks as $risk) {
                if (!array_key_exists($risk->id, $by_id)) {
                    $by_id[$risk->id] = array('risk' => $risk, 'comments_list' => array());
                }
                $by_id[$risk->id]['comments_list'][] = (string)$tagged;
            }
        }
        return array_values($by_id);
    }

    /**
     * @param \Patient $patient
     * @param array $risks - ['risk' => OphCiExaminationRisk, 'comments_list' => string[]][]
     */
    protected function addRisksToPatient(\Patient $patient, $risks = array())
    {
        if ($risks) {
            $element = $this->getLatestElement($patient);
            $present_risks = $element ? $element->present : array();
            $missing_risks = array_filter(
                $risks,
                function ($r) use ($present_risks) {
                    foreach ($present_risks as $present) {
                        if ($r['risk']->id === $present->risk_id) {
                            // for the matching risk, we filter it out if
                            // all the required comments are already on the present entry
                            return (bool) array_filter(
                                $r['comments_list'],
                                function ($c) use ($present) {
                                    return strpos(strtolower($present->comments), strtolower($c)) < 0;
                                }
                            );
                        }
                    }
                    return true;
                }
            );

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
        $event->save();

        return $event;
    }

    /**
     * @param \Patient $patient
     * @param HistoryRisks $current
     * @param $missing_risks - ['risk' => OphCiExaminationRisk, 'comments_list' => string][]
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
            foreach ($entries as $current) {
                if ($current->risk_id === $risk['risk']->id) {
                    // there's an entry for the risk, we just need to update
                    // the comment appropriately.
                    $this->updateEntryComments($current, $risk['comments_list']);
                    // and set it to being present
                    $current->has_risk = true;
                    continue 2;
                }
            }
            // got this far there is no matching risk and we need to
            // create a whole new entry
            $entry = new HistoryRisksEntry('auto');
            $entry->risk_id = $risk['risk']->id;
            $entry->risk = $risk['risk'];
            $entry->has_risk = true;
            $entry->comments = implode(', ', $risk['comments_list']);
            $entries[] = $entry;
        }

        if ($element->save()) {
            foreach ($entries as $entry) {
                $entry->element_id = $element->id;
                $entry->element = $element;
                $entry->save();
            }
        }
    }

    /**
     * Add any missing comments from the given list to the entry comments attribute
     *
     * @param $entry
     * @param array $comments_list
     */
    private function updateEntryComments($entry, $comments_list = array())
    {

        if (strlen($entry->comments)) {
            $entry_comments = array($entry->comments);
            foreach ($comments_list as $c) {
                if (!strpos($entry->comments, $c)) {
                    $entry_comments[] = $c;
                }
            }
            $entry->comments = implode(', ', $entry_comments);
        } else {
            $entry->comments = implode(', ', $comments_list);
        }
    }

    /**
     * A relatively simple handler for receiving notifications that drugs and/or medication drugs
     * have been added to the patient, so the relevant risks should be stored on the patient.
     *
     * @param $params (['patient' => \Patient, 'medications' => \Medication[])
     * @throws \SystemException
     */
    public function addPatientMedicationRisks($params)
    {
        if (!array_key_exists('patient', $params)) {
            throw new \SystemException('Missing expected patient parameter for updating patient risks');
        }
        $risks = array();
        if (array_key_exists('medications', $params)) {
            $risks = $this->getRisksFromTagged($params['medications']);
        }

        $this->addRisksToPatient($params['patient'], $risks);
    }
}
