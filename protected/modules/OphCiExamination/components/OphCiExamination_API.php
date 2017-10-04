<?php

namespace OEModule\OphCiExamination\components;

/*
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models;
use OEModule\OphCiExamination\widgets\HistoryRisks;
use Patient;

class OphCiExamination_API extends \BaseAPI
{

    const LEFT = 1;
    const RIGHT = 0;

    /**
     * Ensure namespace prepended appropriately if necessary
     *
     * @param $element
     * @return string
     */
    private function namespaceElementName($element)
    {
        if (strpos($element, 'models') == 0) {
            $element = 'OEModule\OphCiExamination\\' . $element;
        }
        return $element;
    }

    /**
     * @inheritdoc
     */
    public function getElementFromLatestEvent($element, Patient $patient, $use_context = false, $before = null)
    {
        return parent::getElementFromLatestEvent(
            $this->namespaceElementName($element), $patient, $use_context, $before);
    }

    /**
     * @inheritdoc
     */
    public function getElementFromLatestVisibleEvent($element, Patient $patient, $use_context = false, $before = null)
    {
        return parent::getElementFromLatestVisibleEvent(
            $this->namespaceElementName($element), $patient, $use_context, $before);
    }

    /**
     * @inheritdoc
     */
    public function getLatestElement($element, Patient $patient, $use_context = false, $before = null, $after = null)
    {
        return parent::getLatestElement(
            $this->namespaceElementName($element), $patient, $use_context, $before, $after);
    }

    /**
     * @inheritdoc
     */
    public function getElements($element, Patient $patient, $use_context = false, $before = null, $criteria = null)
    {
        return parent::getElements($this->namespaceElementName($element), $patient, $use_context, $before, $criteria);
    }

    /**
     * Extends parent method to prepend model namespace.
     *
     * @param \Episode $episode
     * @param string $kls
     *
     * @return \BaseEventTypeElement
     * @deprecated - since 2.0
     */
    public function getElementForLatestEventInEpisode($episode, $kls, $later_than = null)
    {
        if (strpos($kls, 'models') == 0) {
            $kls = 'OEModule\OphCiExamination\\' . $kls;
        }

        return parent::getElementForLatestEventInEpisode($episode, $kls, $later_than);
    }

    /**
     * Extends parent method to prepend model namespace.
     *
     * @param $episode_id
     * @param $event_type_id
     * @param $model
     * @param $before_date
     * @return \BaseEventTypeElement
     * @deprecated since 2.0
     */
    public function getMostRecentElementInEpisode($episode_id, $event_type_id, $model, $before_date = '')
    {
        if (strpos($model, 'models') == 0) {
            $model = 'OEModule\OphCiExamination\\' . $model;
        }

        return parent::getMostRecentElementInEpisode($episode_id, $event_type_id, $model, $before_date);
    }

    /**
     * Simple abstraction to support generic calls to functions based on the
     * principal eye from the current context (methods will be called with
     * the given $use_context value).
     *
     * @param $prefix
     * @param $patient
     * @param bool $use_context defaults to false
     * @return mixed
     * @throws \CException
     */
    protected function getMethodForPrincipalEye($prefix, $patient, $use_context = false)
    {
        if ($method = $this->getEyeMethod(
            $prefix,
            $this->getPrincipalEye($patient, true))
        ) {
            return $this->{$method}($patient, $use_context);
        }
    }

    /**
     * Determines if the given eye cares about left properties
     *
     * @param \Eye $eye
     * @return bool
     */
    protected function needsLeft(\Eye $eye)
    {
        return in_array($eye->id, array(\Eye::LEFT, \Eye::BOTH), false);
    }

    /**
     * Determines if the given eye cares about left properties
     *
     * @param \Eye $eye
     * @return bool
     */
    protected function needsRight(\Eye $eye)
    {
        return in_array($eye->id, array(\Eye::RIGHT, \Eye::BOTH), false);
    }

    /**
     * @param $element
     * @param $side
     * @param null $prefix
     * @param string $separator
     * @return string
     */
    protected function getEyedrawDescriptionForSide($element, $side, $prefix = null, $separator = "\n")
    {
        $res = array();
        if ($prefix) {
            $res[] = $prefix;
        }
        if (isset($element->{$side . '_ed_report'})) {
            $res[] = $element->{$side . '_ed_report'};
        }
        if (isset($element->{$side . '_description'})) {
            $res[] = trim($element->{$side . '_description'});
        }
        return implode($separator, $res);
    }

    /**
     * @param $element
     * @param $eye
     * @return string
     */
    protected function getEyedrawDescription($element, $eye, $separator = "\n")
    {
        $res = array();
        if ($element->hasLeft() && $this->needsLeft($eye)) {
            $res[] = $this->getEyedrawDescriptionForSide($element, 'left', 'Left Eye:', $separator);
        }
        if ($element->hasRight() && $this->needsRight($eye)) {
            $res[] = $this->getEyedrawDescriptionForSide($element, 'right', 'Right Eye:', $separator);
        }
        return implode($separator, $res);
    }

    /**
     * Get the patient history description field from the latest Examination event.
     * Limited to current data context by default.
     * Returns nothing if the latest Examination does not contain History.
     *
     * @param Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string|void
     */
    public function getLetterHistory(\Patient $patient, $use_context = false)
    {
        if ($history = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_History',
            $patient,
            $use_context)
        ) {
            return strtolower($history->description);
        }
    }

    /**
     * Get the Intraocular Pressure reading for both eyes from the most recent Examination event.
     * Limited to current data context by default.
     * Will return the average for multiple readings on either eye.
     * Returns nothing if the latest Examination does not contain IOP.
     *
     * @param Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string
     */
    public function getLetterIOPReadingBoth(\Patient $patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ) {
            return $iop->getLetter_reading('right') . ' on the right, and ' . $iop->getLetter_reading('left') . ' on the left';
        }
    }

    /**
     * Get the Intraocular Pressure reading for both eyes from the most recent Examination event.
     * Limited to current data context by default.
     * Will only return the first reading for either eye if there is more than one.
     * Returns nothing if the latest Examination does not contain IOP.
     *
     * @param Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string
     */
    public function getLetterIOPReadingBothFirst(\Patient $patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ) {
            return $iop->getLetter_reading_first('right') . ' on the right, and ' . $iop->getLetter_reading_first('left') . ' on the left';
        }
    }

    /**
     * Get the Intraocular Pressure reading for the left eye from the most recent Examination event.
     * Limited to current data context by default.
     * Will return the average for multiple readings.
     * Returns nothing if the latest Examination does not contain IOP.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return mixed
     */
    public function getLetterIOPReadingLeft(\Patient $patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ) {
            return $iop->getLetter_reading('left');
        }

    }

    /**
     * Get the Intraocular Pressure reading for the right eye from the most recent Examination event.
     * Limited to current data context by default.
     * Will return the average for multiple readings.
     * Returns nothing if the latest Examination does not contain IOP.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return mixed
     */
    public function getLetterIOPReadingRight(\Patient $patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ) {
            return $iop->getLetter_reading('right');
        }
    }

    /**
     * Get an abbreviated form of the IOP readings for both eyes from the most recent Examination.
     * Limited to current data context by default.
     * Will return the average for multiple readings.
     * Returns nothing if the latest Examination does not contain IOP.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string|null
     */
    public function getLetterIOPReadingAbbr(\Patient $patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ) {
            $readings = array();
            if (($reading = $iop->getReading('right'))) {
                $readings[] = "r:{$reading}" . ($iop->isReadingAverage('right') ? ' (avg)' : '');
            }
            if (($reading = $iop->getReading('left'))) {
                $readings[] = "l:{$reading}" . ($iop->isReadingAverage('left') ? ' (avg)' : '');
            }

            return implode(', ', $readings);
        }
    }

    /**
     * Gets IOP reading for the left eye from the latest Examination.
     * Limited to current data context by default.
     * Will return the average for multiple readings.
     * Returns nothing if the latest Examination does not contain IOP.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return mixed
     */
    public function getIOPReadingLeft(\Patient $patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ) {
            return $iop->getReading('left');
        }
    }

    /**
     * Gets IOP reading for the right eye from the latest Examination.
     * Limited to current data context by default.
     * Will return the average for multiple readings.
     * Returns nothing if the latest Examination does not contain IOP.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return mixed
     */
    public function getIOPReadingRight(\Patient $patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ) {
            return $iop->getReading('right');
        }
    }

    /**
     * Gets the last IOP reading for the left eye, regardless of whether it is in the most recent Examination or not.
     * Limited to current data context.
     * Will return the average for multiple readings.
     * Returns nothing if no IOP has been recorded.
     *
     * @todo verify if in use
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string|void
     */
    public function getLastIOPReadingLeft(\Patient $patient, $use_context = false)
    {
        if ($iop = $this->getLatestElement('models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ) {
            return $iop->getReading('left');
        }
    }

    /**
     * Gets the last IOP reading for the right eye, regardless of whether it is in the most recent Examination or not.
     * Limited to current data context.
     * Will return the average for multiple readings.
     * Returns nothing if no IOP has been recorded.
     *
     * @todo verify if in use
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string|void
     */
    public function getLastIOPReadingRight(\Patient $patient, $use_context = false)
    {
        if ($iop = $this->getLatestElement(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ) {
            return $iop->getReading('right');
        }
    }

    /**
     * Get the Intraocular Pressure reading for the principal eye from the most recent Examination event.
     * Will return the average for multiple readings.
     * Returns nothing if the latest Examination does not contain IOP.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return mixed
     * @throws \CException
     */
    public function getLetterIOPReadingPrincipal(\Patient $patient, $use_context = false)
    {
        return $this->getMethodForPrincipalEye('getLetterIOPReading', $patient, $use_context);
    }

    /**
     * Return the anterior segment description for the given eye. This is from the most recent
     * examination that has an anterior segment element.
     *
     * @param Patient $patient
     * @param $use_context
     * @return string
     */
    public function getLetterAnteriorSegmentLeft($patient, $use_context = false)
    {
        if ($as = $this->getElementFromLatestVisibleEvent('models\Element_OphCiExamination_AnteriorSegment', $patient, $use_context)){
            return $this->getEyedrawDescriptionForSide($as, 'left');
        }
    }

    /**
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getLetterAnteriorSegmentRight($patient, $use_context = false)
    {
        if ($as = $this->getElementFromLatestVisibleEvent('models\Element_OphCiExamination_AnteriorSegment', $patient, $use_context)){
            return $this->getEyedrawDescriptionForSide($as, 'right');
        }
    }

    /**
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getLetterAnteriorSegmentBoth($patient, $use_context = false)
    {
        if ($as = $this->getElementFromLatestVisibleEvent('models\Element_OphCiExamination_AnteriorSegment', $patient, $use_context)) {
            return $this->getEyedrawDescription($as, \Eye::model()->findByPk(\Eye::BOTH));
        }
    }

    /**
     * Anterior segment findings in the principal eye
     *
     * @param \Patient $patient
     * @param bool $use_context
     * @return mixed
     * @throws \CException
     */
    public function getLetterAnteriorSegmentPrincipal($patient, $use_context = false)
    {
        return $this->getMethodForPrincipalEye('getLetterAnteriorSegment', $patient, $use_context);
    }

    /**
     * Return the posterior pole description for the given eye. This is from the most recent
     * examination that has a posterior pole element.
     *
     * @param Patient $patient
     * @param $use_context
     * @return string
     */
    public function getLetterPosteriorPoleLeft($patient, $use_context = false)
    {

        if ($element = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_PosteriorPole',
            $patient,
            $use_context)
        ){
            return $this->getEyedrawDescriptionForSide($element, 'left');
        }

    }

    /**
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getLetterPosteriorPoleRight($patient, $use_context = false)
    {
        if ($element = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_PosteriorPole',
            $patient,
            $use_context)
        ){
            return $this->getEyedrawDescriptionForSide($element, 'right');
        }
    }

    /**
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getLetterPosteriorPoleBoth($patient, $use_context = false)
    {
        if ($element = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_PosteriorPole',
            $patient,
            $use_context)
        ) {
            return $this->getEyedrawDescription($element, \Eye::model()->findByPk(\Eye::BOTH));
        }
    }

    /**
     * @param $patient
     * @param bool $use_context
     * @return mixed
     * @throws \CException
     */
    public function getLetterPosteriorPolePrincipal($patient, $use_context = false)
    {
        return $this->getMethodForPrincipalEye('getLetterPosteriorPole', $patient, $use_context);
    }

    /**
     * @param $eventid
     */
    public function getRefractionValues($eventid)
    {
        if ($unit = models\Element_OphCiExamination_Refraction::model()->find('event_id = ' . $eventid)) {
            return $unit;
        }
    }

    /**
     * @param \Event $event
     * @return string
     */
    public function getRefractionTextFromEvent(\Event $event)
    {
        if ($refract_element = models\Element_OphCiExamination_Refraction::model()->findByAttributes(array('event_id' => $event->id))) {
            $right_spherical = number_format($refract_element->{'right_sphere'} + 0.5 * $refract_element->{'right_cylinder'}, 2);
            $left_spherical = number_format($refract_element->{'left_sphere'} + 0.5 * $refract_element->{'left_cylinder'}, 2);
            return $right_spherical . " Right Eye" . ", " . $left_spherical . " Left Eye";
        }


    }

    public function getMostRecentVA($eventid)
    {
        if($vaevents = models\Element_OphCiExamination_VisualAcuity::model()->findAll('event_id = ' . $eventid)) {
            for ($i = 0; $i < count($vaevents); ++$i) {
                if($vaevents){
                    return $vaevents[$i];
                }
            }
        }
    }


    public function getMostRecentVAData($id)
    {
        if ($unit = models\OphCiExamination_VisualAcuity_Reading::model()->findAll('element_id = ' . $id)) {
            return $unit;
        }
    }


    public function getMostRecentNearVA($eventid)
    {
        if($vaevents = models\Element_OphCiExamination_NearVisualAcuity::model()->findAll('event_id = ' . $eventid)) {
            for ($i = 0; $i < count($vaevents); ++$i) {
                return $vaevents[$i];
            }
        }
    }


    public function getMostRecentNearVAData($id)
    {
        // Then findAll data from va_reading for that element_id. Most recent.
        if ($unit = models\OphCiExamination_NearVisualAcuity_Reading::model()->findAll('element_id = ' . $id)) {
            return $unit;
            }
    }

    /**
     * Returns the best visual acuity for the specified side in the given
     * episode for the patient. This is from the most recent  examination
     * that has a visual acuity element. And will be empty if the specified
     * side was not recorded.
     *
     * @param Patient $patient
     * @param string $side
     * @param boolean $use_context
     * @return models\OphCiExamination_VisualAcuity_Reading
     */
    public function getBestVisualAcuity($patient, $side, $use_context = false)
    {
        $va = $this->getLatestElement(
            'models\Element_OphCiExamination_VisualAcuity',
            $patient,
            $use_context);
        if ($va) {
            return $va->getBestReading($side);
        }
    }

    /**
     * @param \Event $event
     * @return string
     */
    public function getBestVisualAcuityFromEvent(\Event $event)
    {
        if ($va = models\Element_OphCiExamination_VisualAcuity::model()->findByAttributes(array('event_id' => $event->id))) {
            return $va->getBest('right') . ' Right Eye ' . $va->getBest('left') . ' Left Eye';
        }
    }

    /**
     * @param $vareading
     * @param $unitId
     */
    public function getVAvalue($vareading, $unitId)
    {
        if ($unit = models\OphCiExamination_VisualAcuityUnitValue::model()->find('base_value = ' . $vareading . ' AND unit_id = ' . $unitId)) {
            return $unit->value;
        }
        return;
    }

    public function getVARight($vaid)
    {
        if ($unit = models\OphCiExamination_VisualAcuity_Reading::model()->findAll('element_id = '
            . $vaid . ' AND side = ' . self::RIGHT)) {
            return $unit;
        }
        return;
    }

    public function getVALeft($vaid)
    {
        if ($unit = models\OphCiExamination_VisualAcuity_Reading::model()->findAll('element_id = '
            . $vaid . ' AND side = ' . self::LEFT)) {
            return $unit;
        }
        return;
    }


    public function getNearVARight($vaid)
    {
        if ($unit = models\OphCiExamination_NearVisualAcuity_Reading::model()->findAll('element_id = '
            . $vaid . ' AND side = ' . self::RIGHT)) {
            return $unit;
        }
        return;
    }

    public function getNearVALeft($vaid)
    {
        if ($unit = models\OphCiExamination_NearVisualAcuity_Reading::model()->findAll('element_id = '
            . $vaid . ' AND side = ' . self::LEFT)) {
            return $unit;
        }
        return;
    }


    public function getMethodIdRight($vaid, $episode)
    {
        if ($unit = models\OphCiExamination_VisualAcuity_Reading::model()->findAll('element_id = '
            . $vaid . ' AND side = ' . self::RIGHT)) {
            return $unit;
        }
        return;
    }

    public function getMethodIdNearRight($vaid)
    {
        if ($unit = models\OphCiExamination_NearVisualAcuity_Reading::model()->findAll('element_id = ' . $vaid
            . ' AND side = ' . self::RIGHT)) {
            return $unit;
        }
        return;
    }

    public function getMethodIdLeft($vaid, $episode)
    {
        if ($unit = models\OphCiExamination_VisualAcuity_Reading::model()->findAll('element_id = ' . $vaid
            . ' AND side = ' . self::LEFT)) {
            return $unit;
        }
        return;
    }

    public function getMethodIdNearLeft($vaid)
    {
        if ($unit = models\OphCiExamination_NearVisualAcuity_Reading::model()->findAll('element_id = ' . $vaid
            . ' AND side = ' . self::LEFT)) {
            return $unit;
        }
        return;
    }


    public function getUnitId($vaid, $episode)
    {
        if ($unit = models\Element_OphCiExamination_VisualAcuity::model()->find('id = ?', array($vaid))) {
            return $unit->unit_id;
        }
        return;
    }

    public function getNearUnitId($vaid, $episode)
    {
        if ($unit = models\Element_OphCiExamination_NearVisualAcuity::model()->find('id = ?', array($vaid))) {
            return $unit->unit_id;
        }
        return;
    }

    /**
     * gets the id for the Snellen Metre unit type for VA.
     *
     * @return int|null
     */
    protected function getSnellenUnitId()
    {
        if ($unit = models\OphCiExamination_VisualAcuityUnit::model()->find('name = ?', array('Snellen Metre'))) {
            return $unit->id;
        }

        return;
    }

    public function getUnitName($unitId)
    {
        if ($unit = models\OphCiExamination_VisualAcuityUnit::model()->find('id = ?', array($unitId))) {
            return $unit->name;
        }
        return;
    }

    public function getMethodName($methodId)
    {
        if ($unit = models\OphCiExamination_VisualAcuity_Method::model()->find('id = ?', array($methodId))) {
            return $unit->name;
        }
        return;
    }

    /**
     * Returns single (best) VA reading from most recent examination event
     * containing a VA element for the left eye.
     *
     * @param $patient
     * @param bool $use_context
     * @return null|string
     */
    public function getLetterVisualAcuityLeft($patient, $use_context = false)
    {
        return ($best = $this->getBestVisualAcuity($patient, 'left', $use_context)) ? $best->convertTo($best->value, $this->getSnellenUnitId()) : null;
    }

    /**
     * Abstraction for getting VA from last 3 weeks used for several letter string methods
     *
     * @param $patient
     * @param $use_context
     * @return \BaseEventTypeElement[]
     */
    protected function getVisualAcuityLast3Weeks($patient, $use_context)
    {
        $after = date('Y-m-d 00:00:00', strtotime('-3 weeks'));
        $criteria = new \CDbCriteria();
        $criteria->compare('event.event_date', '>='.$after);

        return $this->getElements(
            'models\Element_OphCiExamination_VisualAcuity',
            $patient,
            $use_context,
            null,
            $criteria);
    }

    /**
     * Get the latest VA for the Left eye form examination event, if the VA is not recorded, take the value from the latest available event within a period of 3 weeks.
     *
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getLetterVisualAcuityLeftLast3weeks($patient, $use_context = false)
    {
        foreach ($this->getVisualAcuityLast3Weeks($patient, $use_context) as $element) {
            if ($best_reading = $element->getBestReading('left')) {
                return $best_reading->convertTo($best_reading->value, $this->getSnellenUnitId()) . " (recorded on " . \Helper::convertMySQL2NHS($element->event->event_date) . ")";
            }
        }
    }


    public function getLetterVisualAcuityRight($patient, $use_context = false)
    {
        return ($best = $this->getBestVisualAcuity($patient,'right', $use_context)) ? $best->convertTo($best->value, $this->getSnellenUnitId()) : null;
    }

    /**
     * Get the latest VA for the Right eye form examination event, if the VA is not recorded, take the value from the latest available event within a period of 3 weeks.
     *
     * @param $patient
     * @param bool $use_context
     * @return string - 6/24 (recorded on 7 Jun 2017)
     */
    public function getLetterVisualAcuityRightLast3weeks($patient, $use_context = false)
    {
        foreach ($this->getVisualAcuityLast3Weeks($patient, $use_context) as $element) {
            if ($best_reading = $element->getBestReading('right')) {
                return $best_reading->convertTo($best_reading->value, $this->getSnellenUnitId()) . " (recorded on " . \Helper::convertMySQL2NHS($element->event->event_date) . ")";
            }
        }
    }


    /**
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getLetterVisualAcuityBoth($patient, $use_context = false)
    {
        $left = $this->getBestVisualAcuity($patient,'left', $use_context);
        $right = $this->getBestVisualAcuity($patient, 'right', $use_context);

        return ($right ? $right->convertTo($right->value,
            $this->getSnellenUnitId()) : 'not recorded') . ' on the right and ' . ($left ? $left->convertTo($left->value,
            $this->getSnellenUnitId()) : 'not recorded') . ' on the left';
    }

    /**
     * Get the latest VA for both eyes from examination event, if the VA is not recorded,
     * take the value from the latest available event within a period of 3 weeks.
     *
     * @param $patient
     * @param bool $use_context
     * @return string - 6/24 (at 7 Jun 2017)
     */
    public function getLetterVisualAcuityBothLast3weeks($patient, $use_context = false)
    {
        $left = null;
        $right = null;

        $right = $this->getLetterVisualAcuityRightLast3weeks($patient, $use_context) ? : 'not recorded';
        $left = $this->getLetterVisualAcuityLeftLast3weeks($patient, $use_context) ? : 'not recorded';
        return $right . ' on the right and ' . $left . ' on the left';
    }

    /**
     * Gets VA for the principal eye in the current context.
     *
     * @param $patient
     * @param bool $use_context
     * @return mixed
     */
    public function getLetterVisualAcuityPrincipal($patient, $use_context = true)
    {
        return $this->getMethodForPrincipalEye('getLetterVisualAcuity', $patient, $use_context);
    }

    /**
     * Get the latest VA for Principal eye form examination event, if the VA is not recorded, take the value from the latest available event within a period of 3 weeks.
     *
     * @param $patient
     * @param $use_context
     * @return string - 6/24 (at 7 Jun 2017)
     */
    public function getLetterVisualAcuityPrincipalLast3weeks($patient, $use_context = false)
    {
        if ($principal_eye = $this->getPrincipalEye($patient, true)) {
            $method = 'getLetterVisualAcuity' . $principal_eye->name . 'Last3weeks';
            return $this->{$method}($patient, $use_context);
        }
    }

    /**
     * Get a combined string of the different readings. If a unit_id is given, the readings will
     * be converted to unit type of that id.
     *
     * @param string $side
     * @param null   $unit_id
     *
     * @return string
     */
    public function getCombined($side, $unit_id = null)
    {
        $combined = array();
        foreach ($this->{$side.'_readings'} as $reading) {
            $combined[] = $reading->convertTo($reading->value, $unit_id).' '.$reading->method->name;
        }

        return implode(', ', $combined);
    }

    /**
     * Get the default findings string from VA in te latest examination event (if it exists).
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getLetterVisualAcuityFindings($patient, $use_context = false)
    {
        if ($va = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_VisualAcuity',
            $patient,
            $use_context)
        ){
            return $va->getLetter_string();
        }
    }

    /**
     * get the va from the given episode for the left side of the episode patient.
     * @param Pateint $patient
     * @param bool $include_nr_values
     * @param string $before_date
     * @param bool $use_context
     * @return OphCiExamination_VisualAcuity_Reading
     */
    public function getLetterVisualAcuityForEpisodeLeft($patient, $include_nr_values = false, $before_date = NULL, $use_context = false)
    {

        if ($va = $this->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity',
            $patient,
            $use_context,
            $before_date)
        ){
            if ($va->hasLeft()) {
                if ($best = $va->getBestReading('left')) {
                    return $best->convertTo($best->value, $this->getSnellenUnitId());
                } elseif ($include_nr_values) {
                    return $va->getTextForSide('left');
                }
            }
        }
    }

    /**
     * get the va from the given episode for the right side of the episode patient.
     * @param Pateint $patient
     * @param bool    $include_nr_values
     * @param string $before_date
     * @param bool $use_context
     * @return OphCiExamination_VisualAcuity_Reading
     */
    public function getLetterVisualAcuityForEpisodeRight($patient, $include_nr_values = false, $before_date = NULL, $use_context = false)
    {
        if ($va = $this->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity',
            $patient,
            $use_context,
            $before_date)
        ){
            if ($va->hasRight()) {
                if ($best = $va->getBestReading('right')) {
                    return $best->convertTo($best->value, $this->getSnellenUnitId());
                } elseif ($include_nr_values) {
                    return $va->getTextForSide('right');
                }
            }
        }
    }

    /**
     * Get the VA string for both sides.
     *
     * @param $episode
     * @param bool $include_nr_values flag to indicate whether NR flag values should be used for the text
     *
     * @return string
     */
    public function getLetterVisualAcuityForEpisodeBoth($episode, $include_nr_values = false)
    {
        $left = $this->getLetterVisualAcuityForEpisodeLeft($episode->patient, $include_nr_values);
        $right = $this->getLetterVisualAcuityForEpisodeRight($episode->patient, $include_nr_values);

        return ($right ? $right : 'not recorded') . ' on the right and ' . ($left ? $left : 'not recorded') . ' on the left';
    }

    /**
     * get the list of possible unit values for Visual Acuity.
     *
     * currently operates on the assumption there is always Snellen Metre available as a VA unit, and provides this
     * exclusively.
     */
    public function getVAList()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('name = :nm');
        $criteria->params = array(':nm' => 'Snellen Metre');

        $unit = models\OphCiExamination_VisualAcuityUnit::model()->find($criteria);
        $res = array();
        foreach ($unit->selectableValues as $uv) {
            $res[$uv->base_value] = $uv->value;
        }

        return $res;
    }

    /**
     * get the conclusion text from the most recent examination in the patient examination that has a conclusion element.
     *
     * @param \Patient $patient
     * @param $use_context
     *
     * @return string
     */
    public function getLetterConclusion($patient, $use_context = false)
    {
        if ($conclusion = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_Conclusion',
            $patient,
            $use_context)
        ){
            return $conclusion->description;
        }
    }

    /**
     * get the letter txt from the management element for the given patient and episode. This is from the most recent
     * examination that has a management element.
     *
     * @param \Patient $patient
     * @param $use_context
     *
     * @return string
     */
    public function getLetterManagement($patient, $use_context = false)
    {
        if ($management = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_Management',
            $patient,
            $use_context)
        ){
            return $management->comments;
        }
    }

    /**
     * return the adnexal comorbidity for the patient episode on the given side. This is from the most recent examination that
     * has an adnexal comorbidity element.
     *
     * @param Patient $patient
     * @param $use_context
     *
     * @return string
     */
    public function getLetterAdnexalComorbidityRight($patient, $use_context = false)
    {
        if ($ac = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_AdnexalComorbidity',
            $patient,
            $use_context)
        ){
            return $ac->right_description;
        }
    }

    /**
     * Adnexal comorbidity in the left eye
     *
     * @param Patient $patient
     * @param $use_context
     *
     * @return string
     */
    public function getLetterAdnexalComorbidityLeft($patient, $use_context = false)
    {
        if ($ac = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_AdnexalComorbidity',
            $patient,
            $use_context)
        ){
            return $ac->left_description;
        }
    }

    /**
     * Get the NSC Retinopathy grade.
     *
     * @param Patient $patient
     * @param string $side 'left' or 'right'
     * @param $use_context
     *
     * @return string
     */
    public function getLetterDRRetinopathy($patient, $side, $use_context = false)
    {
        if ($dr = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_DRGrading',
            $patient,
            $use_context)
        ){
            $res = $dr->{$side . '_nscretinopathy'};
            if ($dr->{$side . '_nscretinopathy_photocoagulation'}) {
                $res .= ' and evidence of photocoagulation';
            } else {
                $res .= ' and no evidence of photocoagulation';
            }

            return $res;
        }
    }

    public function getLetterDRRetinopathyLeft($patient, $use_context =false)
    {
        return $this->getLetterDRRetinopathy($patient,'left', $use_context);
    }

    public function getLetterDRRetinopathyRight($patient, $use_context = false)
    {
        return $this->getLetterDRRetinopathy($patient,'right', $use_context);
    }

    /**
     * Get the NSC Maculopathy grade.
     *
     * @param Patient $patient
     * @param string $side 'left' or 'right'
     * @param $use_context
     *
     * @return string
     */
    public function getDRMaculopathy($patient, $side, $use_context = false)
    {
        if ($dr = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_DRGrading',
            $patient,
            $use_context)
        ){
            $res = $dr->{$side . '_nscmaculopathy'};
            if ($dr->{$side . '_nscmaculopathy_photocoagulation'}) {
                $res .= ' and evidence of photocoagulation';
            } else {
                $res .= ' and no evidence of photocoagulation';
            }

            return $res;
        }
    }

    public function getLetterDRMaculopathyLeft($patient, $use_context = false)
    {
        return $this->getDRMaculopathy($patient, 'left', $use_context);
    }

    public function getLetterDRMaculopathyRight($patient, $use_context = false)
    {
        return $this->getDRMaculopathy($patient, 'right', $use_context);
    }

    /**
     * Get the clinical diabetic retinopathy grade.
     *
     * @param Patient $patient
     * @param string $side 'left' or 'right'
     * @param $use_context
     *
     * @return string
     */
    public function getDRClinicalRet($patient, $side, $use_context = false)
    {
        if ($dr = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_DRGrading',
            $patient,
            $use_context)
        ){
            if ($ret = $dr->{$side . '_clinicalret'}) {
                return $ret->name;
            };
        }
    }

    public function getLetterDRClinicalRetLeft($patient, $use_context = false)
    {
        return $this->getDRClinicalRet($patient,'left', $use_context);
    }

    public function getLetterDRClinicalRetRight($patient, $use_context = false)
    {
        return $this->getDRClinicalRet($patient,'right', $use_context);
    }

    /**
     * Get the clinical diabetic maculopathy grade.
     *
     * @param Patient $patient
     * @param $use_context
     * @param string $side 'left' or 'right'
     *
     * @return string
     */
    public function getDRClinicalMac($patient, $side, $use_context = false)
    {
        if ($dr = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_DRGrading',
            $patient,
            $use_context)
        ){
            if ($mac = $dr->{$side . '_clinicalmac'}) {
                return $mac->name;
            }
        }
    }

    /**
     * wrapper function for getDRClinicalMac
     *
     * @param $patient
     * @param bool $use_context
     * @return $mixed
     */

    public function getLetterDRClinicalMacLeft($patient, $use_context = false)
    {
        return $this->getDRClinicalMac($patient,'left', $use_context);
    }

    /**
     * wrapper function for getDRClinicalMac
     *
     * @param $patient
     * @param bool $use_context
     * @return $mixed
     */

    public function getLetterDRClinicalMacRight($patient, $use_context = false)
    {
        return $this->getDRClinicalMac($patient,'right', $use_context);
    }

    /**
     * Get the default findings string from laser management in the latest examination
     * event (if the latest examination has laser management).
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getLetterLaserManagementFindings($patient, $use_context = false)
    {
        if ($va = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_LaserManagement',
            $patient,
            $use_context)
        ){
            return $va->getLetter_string();
        }
    }

    /**
     * Get comments from Laser Management if the latest examination event contains
     * laser management. Will concatenate the parent management comments as well.
     *
     * @param Patient $patient
     * @param $use_context
     * @return string
     */
    public function getLetterLaserManagementComments($patient, $use_context = false)
    {
        $result = '';
        if ($lm = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_LaserManagement',
            $patient,
            $use_context)
        ) {
            $result = $lm->comments;
            if ($m = $this->getElementFromLatestVisibleEvent(
                'models\Element_OphCiExamination_Management',
                $patient,
                $use_context)
            ) {
                if (strlen($m->comments)) {
                    $result .= ' (' . $m->comments . ')';
                }
            }
        }
        return $result;
    }

    /**
     * get follow up period from clinical outcome.
     *
     * @param Patient $patient
     * @param $use_context
     * @return string
     */
    public function getLetterOutcomeFollowUpPeriod($patient, $use_context = false)
    {
        if ($api = \Yii::app()->moduleAPI->get('PatientTicketing')) {
            if ($patient_ticket_followup = $api->getLatestFollowUp($patient)) {
                if (@$patient_ticket_followup['followup_quantity'] == 1 && @$patient_ticket_followup['followup_period']) {
                    $patient_ticket_followup['followup_period'] = rtrim($patient_ticket_followup['followup_period'],
                        's');
                }

                return $patient_ticket_followup['followup_quantity'] . ' ' . $patient_ticket_followup['followup_period'] . ' in the ' . $patient_ticket_followup['clinic_location'];
            }
        }
        if ($o = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_ClinicOutcome',
            $patient,
            $use_context)
        ){
            if ($o->followup_quantity) {
                return $o->followup_quantity . ' ' . $o->followup_period;
            }
        }
    }

    /**
     * gets a list of disorders diagnosed for the patient within the current episode, ordered by event creation date.
     *
     * @param Patient $patient
     *
     * @return array() - list of associative arrays with disorder_id and eye_id defined
     */
    public function getOrderedDisorders($patient, $use_context = false)
    {
        $events = $this->getEvents($patient, $use_context);
        $disorders = array();

        if ($events) {
            foreach (@$events as $event) {
                $criteria = new \CDbCriteria();
                $criteria->compare('event_id', $event->id);

                $diagnoses_el = models\Element_OphCiExamination_Diagnoses::model()->find($criteria);
                if ($diagnoses_el) {
                    foreach ($diagnoses_el->diagnoses as $diagnosis) {
                        $disorders[] = array('disorder_id' => $diagnosis->disorder_id, 'eye_id' => $diagnosis->eye_id);
                    }
                }
            }
        }

        return $disorders;
    }

    /**
     * @param $patient
     * @param $element_type_id
     * @param bool $use_context
     * @return mixed
     */
    public function getLetterStringForModel($patient, $element_type_id, $use_context = false)
    {
        if (!$element_type = \ElementType::model()->findByPk($element_type_id)) {
            throw new Exception("Unknown element type: $element_type_id");
        }
        // with introduction of change tracking episode, need to ensure we are retrieving
        // letter strings from the visible events.
        // note that if elements with letter strings start to track in the change episode
        // this will need to be revisited.
        if ($element = $this->getElementFromLatestVisibleEvent(
            $element_type->class_name,
            $patient,
            $use_context)
        ) {
            return $element->letter_string;
        }

    }

    /**
     * returns all the elements from the most recent examination of the patient in the given episode.
     *
     * @param \Patient $patient
     * @param $use_context
     *
     * @return \ElementType[] - array of various different element type objects
     */
    public function getElementsForLatestVisibleEvent($patient, $use_context = false)
    {
        $element_types = array();

        if($event = $this->getLatestVisibleEvent($patient, $use_context)){
            $criteria = new \CDbCriteria();
            $criteria->compare('event_type_id', $event->event_type_id);
            $criteria->order = 'display_order';

            foreach (\ElementType::model()->findAll($criteria) as $element_type) {
                $class = $element_type->class_name;

                if ($element = $class::model()->find('event_id=?', array($event->id))) {
                    // need to check for element behaviour for eyedraw elements
                    if (method_exists($element, 'getLetter_string') || $element->asa('EyedrawElementBehavior')) {
                        $element_types[] = $element_type;
                    }
                }
            }
        }
        return $element_types;
    }

    /**
     * Get the most recent InjectionManagementComplex element in this episode for the given side.
     *
     * N.B. This is different from letter functions as it will return the most recent Injection Management Complex
     * element, regardless of whether it is part of the most recent examination event, or an earlier one.
     *
     * @param Patient $patient
     * @param Episode $episode
     * @param string $side
     *
     * @return models\Element_OphCiExamination_InjectionManagementComplex
     */
    public function getInjectionManagementComplexInEpisodeForSide($patient, $side, $use_context = false)
    {
        $events = $this->getEvents($patient , $use_context);

        $eye_vals = array(\Eye::BOTH);
        if ($side == 'left') {
            $eye_vals[] = \Eye::LEFT;
        } else {
            $eye_vals[] = \Eye::RIGHT;
        }
        foreach (@$events as $event) {
            $criteria = new \CDbCriteria();
            $criteria->compare('event_id', $event->id);
            $criteria->addInCondition('eye_id', $eye_vals);

            if ($el = models\Element_OphCiExamination_InjectionManagementComplex::model()->find($criteria)) {
                return $el;
            }
        }
    }

    /**
     * Get the most recent InjectionManagementComplex element in this episode for the given side and disorder.
     *
     * N.B. This is different from letter functions as it will return the most recent Injection Management Complex element,
     * regardless of whether it is part of the most recent examination event, or an earlier one.
     *
     * @param Patient $patient
     * @param boole $use_context
     * @param string $side
     * @param int $disorder1_id
     * @param int $disorder2_id
     *
     * @return models\Element_OphCiExamination_InjectionManagementComplex
     */
    public function getInjectionManagementComplexInEpisodeForDisorder(
        $patient,
        $use_context = false,
        $side,
        $disorder1_id,
        $disorder2_id
    ) {
        $events = $this->getEvents($patient, $use_context);
        $elements = array();

        if ($events) {
            foreach ($events as $event) {
                $criteria = new \CDbCriteria();
                $criteria->compare('event_id', $event->id);
                $criteria->compare($side . '_diagnosis1_id', $disorder1_id);
                if ($disorder2_id) {
                    $criteria->compare($side . '_diagnosis2_id', $disorder2_id);
                } else {
                    $criteria->addCondition($side . '_diagnosis2_id IS NULL OR ' . $side . '_diagnosis2_id = 0');
                }

                if ($el = models\Element_OphCiExamination_InjectionManagementComplex::model()->find($criteria)) {
                    return $el;
                }
            }
        }
    }

    /**
     * wrapper to retrieve question objects for a given disorder id.
     *
     * @param int $disorder_id
     *
     * @return models\OphCiExamination_InjectionMangementComplex_Question[]
     */
    public function getInjectionManagementQuestionsForDisorder($disorder_id)
    {
        try {
            models\Element_OphCiExamination_InjectionManagementComplex::model()->getInjectionQuestionsForDisorderId($disorder_id);
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * return the most recent Injection Management Complex examination element in the given episode.
     *
     * @param Episode $episode
     * @param DateTime $after
     *
     * @return OphCiExamination_InjectionManagementComplex|null
     */
    public function getLatestInjectionManagementComplex($patient, $after = null, $use_context = false)
    {
        $events = $this->getEvents($patient, $use_context);

        foreach ($events as $event) {
            $criteria = new \CDbCriteria();
            $criteria->addCondition('event_id = ?');
            $criteria->params = array($event->id);
            if ($after) {
                $criteria->addCondition('created_date > ?');
                $criteria->params[] = $after->format('Y-m-d H:i:s');
            }
            if ($el = models\Element_OphCiExamination_InjectionManagementComplex::model()->find($criteria)) {
                return $el;
            }
        }
    }

    /**
     * retrieve OCT measurements for the given side for the patient in the given episode.
     *
     * N.B. This is different from letter functions as it will return the most recent OCT element, regardless of whether
     * it is part of the most recent examination event, or an earlier one.
     *
     * @param \Patient $patient
     * @param string $side - 'left' or 'right'
     * @param boolean $use_context
     *
     * @return array(maximum_CRT, central_SFT) or null
     */
    public function getOCTForSide($patient, $side , $use_context = false)
    {
        $checker = ($side === 'left') ? 'hasLeft' : 'hasRight';
        foreach ($this->getElements('models\Element_OphCiExamination_OCT', $patient, $use_context) as $el) {
            if ($el->$checker()) {
                return array($el->{$side . '_crt'}, $el->{$side . '_sft'});
            }
        }
    }

    /**
     * Get previous SFT values for the given epsiode and side. Before $before, or all available.
     *
     * @param \Episode $episode
     * @param string $side
     * @param date $before
     *
     * @return array
     */
    public function getOCTSFTHistoryForSide($patient, $side, $before = null, $use_context = false)
    {
         if($events = $this->getEvents( $patient , $use_context )){
            if ($side == 'left') {
                $side_list = array(\Eye::LEFT, \Eye::BOTH);
            } else {
                $side_list = array(\Eye::RIGHT, \Eye::BOTH);
            }
            $res = array();
            foreach ($events as $event) {
                $criteria = new \CDbCriteria();
                $criteria->compare('event_id', $event->id);
                $criteria->addInCondition('eye_id', $side_list);
                if ($before) {
                    $criteria->addCondition('event.created_date < :edt');
                    $criteria->params[':edt'] = $before;
                }

                if ($el = models\Element_OphCiExamination_OCT::model()->with('event')->find($criteria)) {
                    $res[] = array('date' => $event->created_date, 'sft' => $el->{$side . '_sft'});
                }
            }

            return $res;
        }
    }

    /**
     * retrieve the Investigation Description for the given patient.
     *
     * @param $patient
     * @param $$use_context
     * @return mixed
     */
    public function getLetterInvestigationDescription($patient , $use_context = false)
    {

            if ($el = $this->getElementFromLatestVisibleEvent(
                'models\Element_OphCiExamination_Investigation',
                $patient,
                $use_context)
            ) {
                return $el->description;
            }
    }

    /**
     * get the maximum CRT for the patient for the given side.
     *
     * @param $patient
     * @param $side
     * @param $use_context
     * @return mixed
     */
    public function getLetterMaxCRTForSide($patient, $side, $use_context = false)
    {
        if ($el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_OCT',
            $patient,
            $use_context)
        ){
            return $el->{$side . '_crt'} . 'um';
        }
    }

    /**
     * wrapper function to get the Maximum CRT for the left side of the patient.
     *
     * @param $patient
     * @param bool $use_context
     *
     * @return mixed
     */
    public function getLetterMaxCRTLeft($patient, $use_context = false)
    {
        return $this->getLetterMaxCRTForSide($patient, 'left', $use_context);
    }

    /**
     * wrapper function to get the Maximum CRT for the right side of the patient.
     *
     * @param $patient
     * @param bool $use_context
     *
     * @return mixed
     */
    public function getLetterMaxCRTRight($patient, $use_context = false)
    {
        return $this->getLetterMaxCRTForSide($patient, 'right', $use_context);
    }

    /**
     * Get the central SFT for the given patient for the given side.
     *
     * @param $patient
     * @param $side
     * @param $use_context
     * @return mixed
     */
    public function getLetterCentralSFTForSide($patient, $side, $use_context = false)
    {
        if ($el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_OCT',
            $patient,
            $use_context)
        ){
            return $el->{$side . '_sft'} . 'um';
        }
    }

    /**
     * wrapper function to get the Central SFT for the left side of the patient.
     *
     * @param $patient
     * @param bool $use_context
     *
     * @return mixed
     */
    public function getLetterCentralSFTLeft($patient, $use_context = false)
    {
        return $this->getLetterCentralSFTForSide($patient, 'left', $use_context);
    }

    /**
     * wrapper function to get the Central SFT for the right side of the patient.
     *
     * @param $patient
     * @param bool $use_context
     *
     * @return mixed
     */
    public function getLetterCentralSFTRight($patient, $use_context=false)
    {
        return $this->getLetterCentralSFTForSide($patient, 'right', $use_context);
    }

    /**
     * get the diagnosis description for the patient on the given side from the injection management complex element in the most
     * recent examination, if there is one.
     *
     * @param $patient
     * @param $side
     * @param $use_context
     * @return string
     */
    public function getLetterInjectionManagementComplexDiagnosisForSide($patient, $side, $use_context = false)
    {
        if ($el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_InjectionManagementComplex',
            $patient,
            $use_context)
        ){
            if ($d = $el->{$side . '_diagnosis1'}) {
                $res = $d->term;
                if ($d2 = $el->{$side . '_diagnosis2'}) {
                    $res .= ' associated with ' . $d2->term;
                }

                return $res;
            }
        }
    }

    /**
     * get the diagnosis description for the patient on the left.
     *
     * @param $patient
     * @param bool $use_context
     *
     * @return string
     *
     * @see getLetterInjectionManagementComplexDiagnosisForSide
     */
    public function getLetterInjectionManagementComplexDiagnosisLeft($patient, $use_context = false)
    {
        return $this->getLetterInjectionManagementComplexDiagnosisForSide($patient, 'left', $use_context);
    }

    /**
     * get the diagnosis description for the patient on the right.
     *
     * @param $patient
     * @param bool $use_context
     *
     * @return string
     *
     * @see getLetterInjectionManagementComplexDiagnosisForSide
     */
    public function getLetterInjectionManagementComplexDiagnosisRight($patient, $use_context = false)
    {
        return $this->getLetterInjectionManagementComplexDiagnosisForSide($patient, 'right', $use_context);
    }

    /**
     * Get the default findings string from Injection Management complex in the latest examination event (if it exists).
     *
     * @TODO: make this work with both injection management elements (i.e. if complex not being used, use basic)
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getLetterInjectionManagementComplexFindings($patient, $use_context = false)
    {
        if ($el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_InjectionManagementComplex',
            $patient,
            $use_context)
        ){
            return $el->getLetter_string();
        }
    }

    /**
     * get the combined string for both eyes injection management complex diagnosis.
     *
     * @param $patient
     * @param bool $use_context
     *
     * @return string
     */
    public function getLetterInjectionManagementComplexDiagnosisBoth($patient, $use_context = false)
    {
        $right = $this->getLetterInjectionManagementComplexDiagnosisRight($patient, $use_context);
        $left = $this->getLetterInjectionManagementComplexDiagnosisLeft($patient, $use_context);
        if ($right || $left) {
            $res = '';
            if ($right) {
                $res = 'Right Eye: ' . $right;
            }
            if ($left) {
                if ($right) {
                    $res .= "\n";
                }
                $res .= 'Left Eye: ' . $left;
            }

            return $res;
        }
    }

    /**
     * Get principal eye CCT values for current episode, examination event.
     *
     * @param $patient
     * @param $use_context
     * @return string
     * @throws \CException
     */
    public function getPrincipalCCT($patient, $use_context = false)
    {
        $str = '';
        if (($principal_eye = $this->getPrincipalEye($patient)) &&
            ($el = $this->getElementFromLatestVisibleEvent(
                'models\Element_OphCiExamination_AnteriorSegment_CCT',
                $patient,
                $use_context
        ))) {
            if ($this->needsLeft($principal_eye) && $el->hasLeft()) {
                $str .= 'Left Eye: ' . $el->left_value . ' µm using ' . $el->left_method->name . '. ';
            }
            if ($this->needsRight($principal_eye) && $el->hasRight()) {
                $str .= 'Right Eye: ' . $el->right_value . ' µm using ' . $el->right_method->name . '. ';
            }
        }
        return $str;
    }

    /**
     * Get principal eye Gonioscopy Van Herick values for current episode, examination event.
     *
     * @param $patient
     * @param $use_context
     * @return string
     * @throws \CException
     */
    public function getPrincipalVanHerick($patient, $use_context = false)
    {
        $str = '';

        if (($principal_eye = $this->getPrincipalEye($patient, true)) &&
            ($el = $this->getElementFromLatestVisibleEvent(
                'models\Element_OphCiExamination_Gonioscopy',
                $patient,
                $use_context
        ))) {
            if (isset($el->left_van_herick) && $this->needsLeft($principal_eye)) {
                $str .= 'Left Eye: Van Herick grade is ' . $el->left_van_herick->name . '. ';
            }
            if (isset($el->right_van_herick) && $this->needsRight($principal_eye)) {
                $str .= 'Right Eye: Van Herick grade is ' . $el->right_van_herick->name . '. ';
            }
        }

        return $str;
    }

    /**
     * get principal eye Optic Disc description for current episode, examination event.
     *
     * @param $patient
     * @param $use_context
     * @return string
     */
    public function getPrincipalOpticDiscDescription($patient, $use_context = false)
    {
        $str = '';
        if (($principal_eye = $this->getPrincipalEye($patient, true)) &&
            ($el = $this->getElementFromLatestVisibleEvent(
                'models\Element_OphCiExamination_OpticDisc',
                $patient,
                $use_context
        ))) {
            return $this->getEyedrawDescription($el, $principal_eye);
        }
        return $str;
    }

    /**
     * Get the latest left CCT measurement.
     *
     * @param \Patient $patient
     * @param $use_context
     * @return string
     */
    public function getCCTLeft($patient, $use_context = false)
    {
        if ($el = $this->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context)
        ) {
            if ($el->hasLeft()) {
                return $el->left_value . ' µm';
            }
        }
    }

   /*
    * Central corneal thickness, left eye reading no units
    * @param $patient
    * @param $use_context
    */
    public function getCCTLeftNoUnits($patient, $use_context = false)
    {
        if ($el = $this->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context)
        ){
            if ($el->hasLeft()) {
                return $el->left_value;
            }
        }

        return 'NR';
    }

    /**
     * Get the latest right CCT measurement.
     *
     * @param \Patient $patient
     * @param $use_context
     * @return string
     */
    public function getCCTRight($patient, $use_context = false)
    {
        if ($el = $this->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context)
        ){
            if ($el->hasRight()) {
                return $el->right_value . ' µm';
            }
        }
    }

    /*
     * Central corneal thickness, right eye reading no units
     * @param $patient
     * @param $use_context
     */
    public function getCCTRightNoUnits($patient, $use_context = false)
    {
        if ($el = $this->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context)
        ){
            if ($el->hasRight()) {
                return $el->right_value;
            }
        }

        return 'NR';
    }

    /**
     * @param Patient $patient
     * @param $use_context
     * @return string|null;
     */
    public function getCCTAbbr(\Patient $patient, $use_context = false)
    {
         if ($cct = $this->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
             $patient,
             $use_context)
         ){
            $readings = array();
            if ($cct->hasRight()) {
                $readings[] = 'r:' . $cct->right_value;
            }
            if ($cct->hasLeft()) {
                $readings[] = 'l:' . $cct->left_value;
            }

             return implode(', ', $readings);
        } else {
            return;
         }
    }

    /**
     * Get the glaucoma risk as a string for the patient - we get this from the most recent examination that has a glaucoma risk recording
     * as it's possible that it's not going to be recorded each time.
     *
     * @param \Patient $patient
     * @param $use_context
     * @return mixed
     */
    public function getGlaucomaRisk($patient, $use_context = false)
    {
        if ($el = $this->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_GlaucomaRisk',
            $patient,
            $use_context)
        ){
            return $el->risk->name;
        }
    }

    /*
     * Intraocular pressure, left eye reading no units
     * @param $patient
     * @param $use_context
     */
    public function getIOPReadingLeftNoUnits($patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ){
            if ($reading = $iop->getReading('left')) {
                return $reading;
            }
        }
        return 'NR';
    }

    /*
     * Intraocular pressure, right eye reading no units
     * @param $patient
     * @param $use_context
     */
    public function getIOPReadingRightNoUnits($patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ){
            if ($reading = $iop->getReading('right')) {
                return $reading;
            }
        }
        return 'NR';
    }

    public function getIOPValuesAsTable($patient, $use_context = false)
    {
        if ($iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context)
        ){
            $iopVals = $iop->getValues();
            $i = 0;
            $output = '<table>';
            while (isset($iopVals['right'][$i]) || isset($iopVals['left'][$i])) {
                if ($i === 0) {
                    $lCCT = $this->getCCTLeftNoUnits($patient);
                    $rCCT = $this->getCCTRightNoUnits($patient);
                    $output .= '<tr><th class="large-6">RE [' . $rCCT . ']</th><th class="large-6">LE [' . $lCCT . ']</th></tr>';
                }

                $output .= '<tr>';
                if (isset($iopVals['right'][$i])) {
                    $right = $iopVals['right'][$i];
                    $instr = (isset($right->instrument->short_name) && strlen($right->instrument->short_name) > 0) ?
                        $right->instrument->short_name : $right->instrument->name;
                    $readingNameRight = $right->instrument->scale ? $right->qualitative_reading->name : $right->reading->name;
                    $output .= '<td>' . $readingNameRight . ':' . $instr . '</td>';
                } else {
                    $output .= '<td>&nbsp;</td>';
                }
                if (isset($iopVals['left'][$i])) {
                    $left = $iopVals['left'][$i];
                    $instr = (isset($left->instrument->short_name) && strlen($left->instrument->short_name) > 0) ?
                        $left->instrument->short_name : $left->instrument->name;
                    $readingNameLeft = $left->instrument->scale ? $left->qualitative_reading->name : $left->reading->name;
                    $output .= '<td>' . $readingNameLeft . ':' . $instr . '</td>';
                } else {
                    $output .= '<td>&nbsp;</td>';
                }
                $output .= '</tr>';
                ++$i;
            }
            $output .= '</table>';

            return $output;
        }
        return '';
    }

    public function getTargetIOP($patient, $use_context = false)
    {
        if ($oManPlan = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_OverallManagementPlan',
            $patient,
            $use_context)
        ){
            return array(
                'left' => ($oManPlan->left_target_iop ? $oManPlan->left_target_iop->name : null),
                'right' => ($oManPlan->right_target_iop ? $oManPlan->right_target_iop->name : null),
            );
        }

        return;
    }

    /*
     * Examination diagnoses and findings
     * @param $patient
     * @param $use_context
     */
    public function getLetterDiagnosesAndFindings($patient, $use_context = false)
    {
        if ($diag = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_Diagnoses',
            $patient,
            $use_context)
        ) {
            return $diag->letter_string;
        }

        return;
    }

    /**
     * @param Patient $patient
     * @param bool $use_context
     * @return mixed|null
     */
    public function getNoAllergiesDate(\Patient $patient, $use_context = false)
    {
        if ($element = $this->getLatestElement('models\Allergies', $patient, $use_context)) {
            return $element->no_allergies_date;
        }
        return null;
    }

    /**
     * Return list of allergies belonging to a patient.
     *
     * @param \Patient $patient
     * @todo: update to reflect change to allergies
     * @return string
     */
    public function getAllergies(\Patient $patient)
    {
        if (count($patient->allergies)) {
            return $patient->getAllergiesSeparatedString($prefix='', $separator=', ', $lastSeparatorNeeded=false);
        }

        return 'none';
    }

    /**
     * To get the most recent VA element for the Patient
     *
     * @param \Patient $patient
     * @return array|bool
     */
    public function getMostRecentVAElementForPatient(\Patient $patient)
    {
        $event_type = $this->getEventType();
        $criteria = new \CDbCriteria;
        $criteria->select = '*';
        $criteria->join = 'join episode on t.episode_id = episode.id and patient_id = :patient_id and event_type_id = :event_type_id';
        $criteria->order = 't.event_date desc';
        $criteria->condition = 't.deleted != 1';
        $criteria->params = array(':patient_id' => $patient->id, ':event_type_id' => $event_type->id);
        foreach (\Event::model()->findAll($criteria) as $event) {
            $result_element = models\Element_OphCiExamination_VisualAcuity::model()
                ->with('event')
                ->find('event_id=?', array($event->id));
            if ($result_element !== null) {
                return (array('element' => $result_element, 'event_date' => date($event->created_date)));
            }
        }

        return false;
    }

    public static $UNAIDED_VA_TYPE = 'unaided';
    public static $AIDED_VA_TYPE = 'aided';

    /**
     * To get the visual acuity from the element based on the all episodes for the patient
     * @param \Patient $patient
     * @param $side
     * @param $type
     * @param $element
     * @return null
     * @throws \Exception
     */
    public function getMostRecentVAForPatient(\Patient $patient, $side, $type,$element)
    {
        if (!in_array($type, array(static::$AIDED_VA_TYPE, static::$UNAIDED_VA_TYPE))) {
            throw new \Exception("Invalid type for VA {$type}");
        }
        $checkFunc = 'has' . ucfirst($side);
        if (!$element->$checkFunc()) {
            return null;
        }

        $method_type = $type == static::$AIDED_VA_TYPE
            ? models\OphCiExamination_VisualAcuity_Method::$AIDED_FLAG_TYPE
            : models\OphCiExamination_VisualAcuity_Method::$UNAIDED_FLAG_TYPE;

        $methods = models\OphCiExamination_VisualAcuity_Method::model()->findAll('type=?', array($method_type));
        $best_reading = $element->getBestReadingByMethods($side, $methods);
        return $best_reading;
    }

    /**
     * Get clinic outcome comments from the most recent Examination.
     * Limited to current data context by default.
     * Returns nothing if the latest Examination does not contain the clinic outcome element (or the comments are empty)
     *
     * @param Patient $patient
     * @param bool $use_context
     * @return string|null
     */
    public function getLetterClinicOutcomeComments(\Patient $patient, $use_context = false)
    {
        if ($outcome = $this->getElementFromLatestVisibleEvent('models\Element_OphCiExamination_ClinicOutcome',
            $patient,
            $use_context)
        ) {
            return $outcome->description;
        }
    }

    /**
     * Get clinic outcome details from the most recent Examination.
     * Limited to the current data context by default.
     * Returns nothing if the latest Examination does not contain the clinic outcome element.
     *
     * @param \Patient $patient
     * @param bool $use_context
     * @returns string
     */
    public function getLatestOutcomeDetails(\Patient $patient, $use_context = false)
    {
        $str = '';
        if($element = $this->getElementFromLatestVisibleEvent('models\Element_OphCiExamination_ClinicOutcome',
            $patient,
            $use_context))
        {
            $str = $element->status->name;
            if($element->status->followup)
            {
                $str .= " in {$element->followup_quantity} {$element->followup_period} by {$element->role->name}";
                if ($element->role_comments != '')
                {
                    $str .= " ({$element->role_comments})";
                }
            }
        }
        return $str;
    }

    /**
     * Letter string for the latest Cataract Surgical Management element.
     * Limited to the current data context by default.
     * Returns nothing if the latest Examination does not contain the Cataract Surgical Management element.
     *
     * @param Patient $patient
     * @param bool $use_context
     * @return string
     */
    public function getCataractSurgicalManagement(\Patient $patient, $use_context = false)
    {
        $str = '';
        if ($element = $this->getElementFromLatestVisibleEvent('models\Element_OphCiExamination_CataractSurgicalManagement',
            $patient,
            $use_context))
        {
            $str = "Eye: {$element->eye()->name}".PHP_EOL;
            $str .= "Straight Forward: ".($element->fast_track == 1 ? 'Yes' : 'No').PHP_EOL;
            $str .= "Post Operative Target: {$element->target_postop_refraction}D".PHP_EOL;
            $str .= "Suitable for: {$element->suitable_for_surgeon->name}".($element->supervised == 1 ? " (supervised)" : "").PHP_EOL;
            $str .= ($element->previous_refractive_surgery == 1 ? "Patient has had previous refractive surgery".PHP_EOL : "");
            $str .= ($element->vitrectomised_eye == 1 ? "Vitrectomised eye".PHP_EOL : "");
            $reasons = [];
            foreach ($element->reasonForSurgery as $reason) {
                $reasons[]=$reason->name;
            }

            if (!empty($reasons)) {
                $str.= "Primary reason for surgery: ".implode(", ", $reasons).PHP_EOL;
            }
        }
        return $str;
    }

    protected $widget_cache = array();

    /**
     * NB. caching on this needs to be enhanced to index by data parameters.
     *
     * @param $class_name
     * @param $data
     * @return mixed
     */
    protected function getWidget($class_name, $data)
    {
        if (!array_key_exists($class_name, $this->widget_cache)) {
            $this->widget_cache[$class_name] = $this->yii->getWidgetFactory()
                ->createWidget($this, $class_name, $data);
            $this->widget_cache[$class_name]->init();
        }
        return $this->widget_cache[$class_name];
    }

    /**
     *
     * @param $patient
     * @param $risk_name
     * @return mixed
     */
    public function getRiskByName($patient, $risk_name) {
        $widget = $this->getWidget(
            'OEModule\OphCiExamination\widgets\HistoryRisks',
            array('mode' => HistoryRisks::$DATA_MODE, 'patient' => $patient));
        if ($entry = $widget->element->getRiskEntryByName($risk_name)) {
            $status = null;
            switch ($entry->has_risk) {
                case (models\HistoryRisksEntry::$PRESENT):
                    $status = true;
                    break;
                case (models\HistoryRisksEntry::$NOT_PRESENT):
                    $status = false;
                    break;
            }

            return array(
                'name' => (string)$entry->risk,
                'status' => $status,
                'comments' => $entry->comments,
                'date' => $entry->element->event->event_date
            );
        }
    }

    /*
     * Glaucoma Overall Management Plan from latest Examination
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getGlaucomaManagement(\Patient $patient, $use_context = false)
    {
        $result = '';

        if($el = $this->getLatestElement(
            'models\Element_OphCiExamination_OverallManagementPlan',
            $patient, $use_context))
        {

            $result .= 'Clinic Interval: ' . ($el->clinic_interval ? : 'NR') . "\n";
            $result .= 'Photo: ' . ($el->photo ? : 'NR') . "\n";
            $result .= 'OCT: ' . ($el->oct ? : 'NR') . "\n";
            $result .= 'Visual Fields: ' . ($el->hfa ? : 'NR') . "\n";
            $result .= 'Gonioscopy: ' . ($el->gonio ? : 'NR') . "\n";
            $result .= 'HRT: ' . ($el->hrt ? : 'NR') . "\n";

            if(!empty($el->comments)){
                $result .= 'Glaucoma Management comments: '.$el->comments."\n";
            }

            $result .= "\n";
            if(isset($el->right_target_iop->name)){
                $result .= 'Target IOP Right Eye: '.$el->right_target_iop->name." mmHg\n";
            }
            if(isset($el->left_target_iop->name)){
                $result .= 'Target IOP Left Eye: '.$el->left_target_iop->name." mmHg\n";
            }



        }
        return $result;
    }
}
