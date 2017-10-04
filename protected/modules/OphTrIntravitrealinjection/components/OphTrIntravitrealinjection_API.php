<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrIntravitrealinjection_API extends BaseAPI
{

    private $legacy_api;

    /**
     * cache and return a legacy injection api instance.
     *
     * @return mixed
     */
    protected function getLegacyAPI()
    {
        if (!$this->legacy_api) {
            $this->legacy_api = Yii::app()->moduleAPI->get('OphLeIntravitrealinjection');
        }

        return $this->legacy_api;
    }

    /**
     * return only previous injections given a starting event id.
     */
    public function previousInjectionsByEvent($event_id, $side, $drug)
    {
        $event = Event::model()->find('id = :id', array(':id' => $event_id));
        $episode = $event->episode;
        $patient = $event->episode->patient;

        return $this->previousInjections($patient, $episode, $side, $drug, $event->event_date);
    }

    /**
     * return the set of treatment elements from previous injection events in descending order.
     *
     * @param Patient                                   $patient
     * @param Episode                                   $episode
     * @param string                                    $side
     * @param OphTrIntravitrealinjection_Treatment_Drug $drug
     * @param string                                    $since
     *
     * @throws Exception
     *
     * @return array {$side . '_drug_id' => integer, $side . '_number' => integer, 'date' => datetime}[] - array of treatment elements for the eye and optional drug
     */
    public function previousInjections($patient, $episode, $side, $drug = null, $since = 'now')
    {

        $res = array();
        // NOTE: we assume that all legacy injections would be from before any injections in
        // this module. Should this prove not to be the case, we would need to sort the result
        // data structure by date
        if ($legacy_api = $this->getLegacyAPI()) {
            foreach ($legacy_api->previousInjections($patient, $episode, $side, $drug) as $legacy) {
                $res[] = $legacy;
            }
        }

        if (!$drug || get_class($drug) !== 'OphTrIntravitrealinjection_Treatment_Drug') {
            $drug = new OphTrIntravitrealinjection_Treatment_Drug();
        }

        $injections = $this->injectionsSinceByEpisodeSideAndDrug($episode, $side, $drug, $since);

        foreach ($injections as $injection) {
            $res[] = array(
                $side . '_drug_id' => $injection->{$side . '_drug_id'},
                $side . '_drug' => $injection->{$side . '_drug'}->name,
                $side . '_number' => $injection->{$side . '_number'},
                'date' => $injection->event->event_date,
                'event_id' => $injection->event_id,
            );
        }
        return $res;
    }


    /**
     * @param Episode                                   $episode
     * @param string                                    $side
     * @param OphTrIntravitrealinjection_Treatment_Drug $drug
     * @param string                                    $since
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function injectionsSinceByEpisodeSideAndDrug(Episode $episode, $side, OphTrIntravitrealinjection_Treatment_Drug $drug, $since = 'now')
    {

        switch ($side) {
            case 'left':
                $eye_id = SplitEventTypeElement::LEFT;
                break;
            case 'right':
                $eye_id = SplitEventTypeElement::RIGHT;
                break;
            default:
                throw new Exception('invalid side value provided: ' . $side);
                break;
        }

        $sinceDate = new DateTime($since);

        $criteria = new CDbCriteria();
        $criteria->alias = 'treatment';
        $criteria->addCondition(array(
            'event.episode_id = :episode_id',
            'event.deleted = 0',
            'treatment.eye_id in (:eye_id,'.SplitEventTypeElement::BOTH.')',
            'event_date <= :since',
            'event.deleted = 0',
            )
        );
        $criteria->join = 'JOIN event ON treatment.event_id = event.id';
        $criteria->order = 'event.event_date ASC';
        $criteria->params = array(
            'episode_id' => $episode->id,
            'eye_id' => $eye_id,
            'since' => $sinceDate->format('Y-m-d'),
        );

        if ($drug->id) {
            $criteria->addCondition('treatment.' . $side . '_drug_id = :drug_id');
            $criteria->params['drug_id'] = $drug->id;
        }

        return Element_OphTrIntravitrealinjection_Treatment::model()->findAll($criteria);
    }

    /**
     * get the most recent treatment element that has data for the given eye side.
     *
     * @param $patient
     * @param $side
     * @param $use_context
     *
     * @return Element_OphTrIntravitrealinjection_Treatment
     */
    protected function getPreviousTreatmentForSide($patient, $side, $use_context = false)
    {
        $checker = ($side === 'left') ? 'hasLeft' : 'hasRight';
        $treatment = $this->getElementFromLatestEvent('Element_OphTrIntravitrealinjection_Treatment', $patient, $use_context);
        if ($treatment && $treatment->$checker()) {
            return $treatment;
        }
    }

    /**
     * get the drug name for the patient, episode and side from the most recent injection event, if it exists.
     *
     * @param $patient
     * @param $side
     * @param $use_content
     * @return mixed
     */
    public function getLetterTreatmentDrugForSide($patient, $side, $use_content = false)
    {
        if ($injection = $this->getPreviousTreatmentForSide($patient, $side, $use_content)) {
            return $injection->{$side . '_drug'}->name;
        }
    }

    /**
     * get the most recent drug for the left side in the current subspecialty episode for the patient.
     *
     * @param $patient
     * @param $use_content
     * @return mixed
     */
    public function getLetterTreatmentDrugLeft($patient, $use_content = false)
    {
        return $this->getLetterTreatmentDrugForSide($patient, 'left', $use_content);
    }

    /**
     * get the most recent drug for the right side in the current subspecialty episode for the patient.
     *
     * @param $patient
     * @param $use_content
     * @return mixed
     */
    public function getLetterTreatmentDrugRight($patient, $use_content = false)
    {
        return $this->getLetterTreatmentDrugForSide($patient, 'right', $use_content);
    }

    /**
     * get the most recent drug for both sides in the current subspecialty episode for the patient.
     *
     * @param $patient
     * @param $use_content
     * @return string
     */
    public function getLetterTreatmentDrugBoth($patient, $use_content = false)
    {

        $res = '';
        $right = $this->getLetterTreatmentDrugForSide($patient, 'right', $use_content);
        $left = $this->getLetterTreatmentDrugForSide($patient, 'left', $use_content);
        if ($right) {
            $res = $right . ' injection to the right eye';
            if ($left) {
                $res .= ', and ' . $left . ' injection to the left eye';
            }
        } elseif ($left) {
            $res = $left . ' injection on the left eye';
        }

        return $res;

    }

    /**
     * get the most recent treatment number for the patient, episode and side.
     *
     * @param $patient
     * @param $side
     * @param $use_content
     * @return mixed
     */
    public function getLetterTreatmentNumberForSide($patient, $side, $use_content = false)
    {
        if ($injection = $this->getPreviousTreatmentForSide($patient, $side, $use_content)) {
            return $injection->{$side . '_number'};
        }
    }

    /**
     * get the most recent treatment number for the left side in the current subspecialty episode for the patient.
     *
     * @param $patient
     * @param $use_content
     * @return mixed
     */
    public function getLetterTreatmentNumberLeft($patient, $use_content = false)
    {
        return $this->getLetterTreatmentNumberForSide($patient, 'left', $use_content);
    }

    /**
     * get the most recent treatment number for the right side in the current subspecialty episode for the patient.
     *
     * @param $patient
     * @param $use_content
     * @return mixed
     */
    public function getLetterTreatmentNumberRight($patient, $use_content = false)
    {
        return $this->getLetterTreatmentNumberForSide($patient, 'right', $use_content);
    }

    /**
     * get the most recent treatment number for both eyes in the current subspecialty episode for the patient.
     *
     * @param Patient $patient
     * @param $use_content
     */
    public function getLetterTreatmentNumberBoth($patient, $use_content = false)
    {
        $right = $this->getLetterTreatmentNumberRight($patient, $use_content);
        $left = $this->getLetterTreatmentNumberLeft($patient, $use_content);
        $res = '';
        if ($right) {
            $res = $right . ' on the right eye';
            if ($left) {
                $res .= ', and ' . $left . ' on the left eye';
            }
        } elseif ($left) {
            $res = $left . ' on the left eye';
        }

        return $res;
    }

    /**
     * get the text string describing the post injection drops needed for the last injection event in the episode.
     *
     * @param $patient
     * @param $use_context
     * @return string
     */
    public function getLetterPostInjectionDrops($patient, $use_context = false)
    {
        if ($el = $this->getElementFromLatestEvent(
            'Element_OphTrIntravitrealinjection_PostInjectionExamination',
            $patient,
            $use_context)
        ){
            $drops = array();
            if ($el->hasRight()) {
                $drops[] = $el->right_drops->name . ' to the right eye';
            }
            if ($el->hasLeft()) {
                $drops[] = $el->left_drops->name . ' to the left eye';
            }

            return implode(', and ', $drops);
        }
    }
}
