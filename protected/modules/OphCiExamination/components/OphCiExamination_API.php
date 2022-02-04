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
use OEModule\OphCiExamination\widgets\HistoryMedications;
use OEModule\OphCiExamination\widgets\HistoryRisks;
use OEModule\OphCiExamination\widgets\Allergies;
use Patient;

class OphCiExamination_API extends \BaseAPI
{
    use traits\VisualAcuity_API;
    use traits\Refraction_API;
    use traits\AEShortcodes;

    const LEFT = 1;
    const RIGHT = 0;
    public static $UNAIDED_VA_TYPE = 'unaided';
    public static $AIDED_VA_TYPE = 'aided';
    protected $widget_cache = array();


    /**
     * Gets the Retinopathy Photocoagulation right side
     *
     * @param \Patient $patient
     * @return string
     */
    public function getLetterRetinopathyPhotocoagulationRight(\Patient $patient)
    {
        return $this->getLetterRetinopathyPhotocoagulationSide($patient, "right");
    }

    /**
     * Gets the Retinopathy Photocoagulation
     *
     * @param \Patient $patient
     * @param $side
     * @return string
     */
    public function getLetterRetinopathyPhotocoagulationSide(\Patient $patient, $side)
    {
        if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
            if ($dr = $this->getElementFromLatestEvent('models\Element_OphCiExamination_DRGrading', $patient)) {
                $rp = $dr->{"{$side}_nscretinopathy_photocoagulation"};
                $mp = $dr->{"{$side}_nscmaculopathy_photocoagulation"};

                if ($rp && $mp) {
                    return "peripherial and macular";
                }

                if ($rp) {
                    return "peripherial";
                }

                if ($mp) {
                    return "macular";
                }
            }
        }

        return "no";
    }

    /**
     * Gets the Retinopathy Photocoagulation left side
     *
     * @param \Patient $patient
     * @return string
     */
    public function getLetterRetinopathyPhotocoagulationLeft(\Patient $patient)
    {
        return $this->getLetterRetinopathyPhotocoagulationSide($patient, "left");
    }


/************************************************************************************** */
    /**
     * @inheritdoc
     */
    public function getElementFromLatestEvent($element, Patient $patient, $use_context = false, $before = null)
    {
        return parent::getElementFromLatestEvent(
            $this->namespaceElementName($element),
            $patient,
            $use_context,
            $before
        );
    }

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
        $history = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_History',
            $patient,
            $use_context
        );

        if ($history) {
            return $history->description;
        }
    }

    /**
     * @inheritdoc
     */
    public function getElementFromLatestVisibleEvent($element, Patient $patient, $use_context = false, $before = null)
    {
        return parent::getElementFromLatestVisibleEvent(
            $this->namespaceElementName($element),
            $patient,
            $use_context,
            $before
        );
    }

    /**
     * Get the most recent Intraocular Pressure reading for both eyes from the Examination event within the last 6 weeks
     * Limited to current data context by default.
     * Will return the average for multiple readings on either eye.
     * @param Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string|null
     */
    public function getLetterIOPReadingBothLast6weeks(\Patient $patient, $use_context = false)
    {
        $iop = $this->getIntraocularPressureElement($patient, false, '-6 weeks');
        if ($iop) {
            return $iop->getLetter_reading('right') . ' on the right, and ' . $iop->getLetter_reading('left') . ' on the left' .
                ' (recorded on ' . \Helper::convertMySQL2NHS($iop->event->event_date) . ')';
        }

        return null;
    }

    /**
     * Returns the most recent Element_OphCiExamination_IntraocularPressure
     *
     * @param Patient $patient
     * @param bool $use_context
     * @param string $after - time limit, default to -3 weeks
     */
    private function getIntraocularPressureElement(\Patient $patient, $use_context = false, $after = '-3 weeks')
    {
        $after_date = date('Y-m-d 00:00:00', strtotime($after));
        $criteria = new \CDbCriteria();
        $criteria->compare('event.event_date', '>=' . $after_date);
        $criteria->limit = 1;


        $iop = $this->getElements(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context,
            null,
            $criteria
        );

        return isset($iop[0]) ? $iop[0] : null;
    }

    /**
     * @inheritdoc
     */
    public function getElements($element, Patient $patient, $use_context = false, $before = null, $criteria = null): array
    {
        return parent::getElements($this->namespaceElementName($element), $patient, $use_context, $before, $criteria);
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
    public function getLetterIOPReadingBothFirstLast6weeks(\Patient $patient, $use_context = false)
    {
        $iop = $this->getIntraocularPressureElement($patient, false, '-6 weeks');

        if ($iop) {
            return $iop->getLetter_reading_first('right') . ' on the right, and ' . $iop->getLetter_reading_first('left') . ' on the left' .
                ' (recorded on ' . \Helper::convertMySQL2NHS($iop->event->event_date) . ')';
        }
    }

    /**
     * Get the most recent Intraocular Pressure reading for the left eyes from the Examination event within the last 6 weeks
     * Limited to current data context by default.
     * Will return the average for multiple readings.
     * Returns nothing if the Examination from the last 6 weeks does not contain IOP.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return mixed
     */
    public function getLetterIOPReadingLeftLast6weeks(\Patient $patient, $use_context = false)
    {
        $iop = $this->getIntraocularPressureElement($patient, false, '-6 weeks');
        if ($iop) {
            return $iop->getLetter_reading('left') . ' (recorded on ' . \Helper::convertMySQL2NHS($iop->event->event_date) . ')';
        }
        return null;
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
    public function getLetterIOPReadingRightLast6weeks(\Patient $patient, $use_context = false)
    {
        $iop = $this->getIntraocularPressureElement($patient, false, '-6 weeks');

        if ($iop) {
            return $iop->getLetter_reading('right') .
                ' (recorded on ' . \Helper::convertMySQL2NHS($iop->event->event_date) . ')';
        }
        return null;
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
    public function getLetterIOPReadingAbbrLast6weeks(\Patient $patient, $use_context = false)
    {
        $iop = $this->getIntraocularPressureElement($patient, false, '-6 weeks');

        if ($iop) {
            $readings = array();
            $reading = $iop->getReading('right');
            if ($reading) {
                $readings[] = "r:{$reading}" . ($iop->isReadingAverage('right') ? ' (avg)' : '');
            }
            $reading = $iop->getReading('left');
            if ($reading) {
                $readings[] = "l:{$reading}" . ($iop->isReadingAverage('left') ? ' (avg)' : '');
            }

            return implode(', ', $readings) . ' (recorded on ' . \Helper::convertMySQL2NHS($iop->event->event_date) . ')';
        }

        return null;
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
        $iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context
        );

        if ($iop) {
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
        $iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context
        );

        if ($iop) {
            return $iop->getReading('right');
        }
    }

    /**
     * Gets the last IOP reading for the left eye, regardless of whether it is in the most recent Examination or not.
     * Limited to current data context.
     * Will return the average for multiple readings.
     * Returns nothing if no IOP has been recorded.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string|void
     */
    public function getLastIOPReadingLeft(\Patient $patient, $use_context = false)
    {
        $iop = $this->getLatestElement(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context
        );

        if ($iop) {
            return $iop->getReading('left');
        }
    }

    /**
     * @inheritdoc
     */
    public function getLatestElement($element, Patient $patient, $use_context = false, $before = null, $after = null)
    {
        return parent::getLatestElement(
            $this->namespaceElementName($element),
            $patient,
            $use_context,
            $before,
            $after
        );
    }

    /**
     * Gets the last IOP reading for the right eye, regardless of whether it is in the most recent Examination or not.
     * Limited to current data context.
     * Will return the average for multiple readings.
     * Returns nothing if no IOP has been recorded.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string|void
     */
    public function getLastIOPReadingRight(\Patient $patient, $use_context = false)
    {
        $iop = $this->getLatestElement(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context
        );

        if ($iop) {
            return $iop->getReading('right');
        }
    }

    /**
     * Get the Intraocular Pressure reading for the principal eye from the Examination event within the last 6 weeks.
     * Returns nothing if the Examination withing last 6 weeks does not contain IOP.
     *
     * @param \Patient $patient
     * @param boolean $use_context - defaults to false
     * @return mixed
     * @throws \CException
     */
    public function getLetterIOPReadingPrincipalLast6weeks(\Patient $patient, $use_context = false)
    {
        $principal_eye = $this->getPrincipalEye($patient, true);
        $method = "getLetterIOPReading{$principal_eye}Last6weeks";

        if (method_exists($this, $method)) {
            return $this->{$method}($patient, $use_context);
        }
    }

    public function getBaseIOPValues(Patient $patient)
    {
        //init output array
        $base_values = [
            'right' => null,
            'left' => null,
            'date' => null,
        ];
        //init our date temporary value
        $first_date = null;
        //get all of the events for our patient
        $events = $patient->getEvents();
        //for each of the events that we care about
        foreach ($events as $event) {
            if (($event->getEventName()=='Phasing')||($event->getEventName()=='Examination')) {
                //get the elements (phasing still works here)
                $elements = $event->getElements('models\Element_OphCiExamination_IntraocularPressure', $patient);

                //for each element that we care about
                foreach ($elements as $iop) {
                    if ($iop->getElementTypeName()=='Intraocular Pressure' || $iop->getElementTypeName()=='Intraocular Pressure Phasing') {
                        //if the date is null then this is the min value, otherwise check if this is the min value and not null
                        if ( $base_values['date'] ==null||($first_date > $iop->event->event_date) && $iop->event->event_date != null) {
                            //set the date to this date
                            $first_date = $iop->event->event_date;
                            $base_values['date'] = \Helper::convertMySQL2NHS($iop->event->event_date);
                            //for each side set the reading value average for this element
                            $sum_values = [
                                'right' => null,
                                'left' => null,
                            ];
                            foreach (['left', 'right'] as $side) {
                                $readings = $iop->getReadings($side);
                                if (count($readings) > 0) {
                                    //Get the average for all the values
                                    foreach ($readings as $reading) {
                                            $sum_values[$side] += (float)$reading;
                                    }
                                    $base_values[$side] = ($sum_values[$side])/count($readings);
                                } else {
                                    $base_values[$side] = null;
                                }
                            }
                        }
                    }
                }
            }
        }
        //return the base values
        return $base_values;
    }

    public function getMaxIOPValues(Patient $patient)
    {
        //init output array
        $max_values = [
            'right' => null,
            'left' => null,
        ];
        //get all of the events for our patient
        $events = $patient->getEvents();
        //for each of the events that we care about
        foreach ($events as $event) {
            if (($event->getEventName()=='Phasing')||($event->getEventName()=='Examination')) {
                //get the elements (phasing still works here)
                $iops = $event->getElements('models\Element_OphCiExamination_IntraocularPressure', $patient);

                //for each element that we care about
                foreach ($iops as $iop) {
                    if ($iop->getElementTypeName()=='Intraocular Pressure' || $iop->getElementTypeName()=='Intraocular Pressure Phasing') {
                        foreach (['left', 'right'] as $side) {
                            //get all readings for each side separately
                            $readings = $iop->getReadings($side);
                            //if we have readings to check
                            if (count($readings) > 0) {
                                foreach ($readings as $reading) {
                                    //if the readinf is larger than our Max value, save that value
                                    if ((float)$reading > $max_values[$side]) {
                                        $max_values[$side] = (float)$reading;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //return the max values
        return $max_values;
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
        $as = $this->getElementFromLatestVisibleEvent('models\Element_OphCiExamination_AnteriorSegment', $patient, $use_context);
        if ($as) {
            return $this->getEyedrawDescriptionForSide($as, 'left');
        }
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
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getLetterAnteriorSegmentRight($patient, $use_context = false)
    {
        $as = $this->getElementFromLatestVisibleEvent('models\Element_OphCiExamination_AnteriorSegment', $patient, $use_context);
        if ($as) {
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
        $as = $this->getElementFromLatestVisibleEvent('models\Element_OphCiExamination_AnteriorSegment', $patient, $use_context);
        if ($as) {
            return $this->getEyedrawDescription($as, \Eye::model()->findByPk(\Eye::BOTH));
        }
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
        $element = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_PosteriorPole',
            $patient,
            $use_context
        );

        if ($element) {
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
        $element = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_PosteriorPole',
            $patient,
            $use_context
        );

        if ($element) {
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
        $element = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_PosteriorPole',
            $patient,
            $use_context
        );

        if ($element) {
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
     * Get the VA string for both sides.
     *
     * @param $episode
     * @param bool $include_nr_values flag to indicate whether NR flag values should be used for the text
     *
     * @return string
     * @deprecated since v4.0 use getSnellenVisualAcuityForBoth instead
     */
    public function getLetterVisualAcuityForEpisodeBoth($episode, $include_nr_values = false)
    {
        trigger_error("getLetterVisualAcuityForEpisodeBoth is deprecated, use getSnellenVisualAcuityForBoth", E_USER_DEPRECATED);
        return $this->getSnellenVisualAcuityForBoth($episode->patient, $include_nr_values);
    }

    /**
     * get the va from the given episode for the left side of the episode patient.
     * @param Patient $patient
     * @param bool $include_nr_values
     * @param string $before_date
     * @param bool $use_context
     * @return OphCiExamination_VisualAcuity_Reading
     * @deprecated since v4.0 use getSnellenVisualAcuityForLeft instead
     */
    public function getLetterVisualAcuityForEpisodeLeft(
        $patient,
        $include_nr_values = false,
        $before_date = null,
        $use_context = false
    ) {
        trigger_error("getLetterVisualAcuityForEpisodeLeft is deprecated, use getSnellenVisualAcuityForLeft", E_USER_DEPRECATED);
        return $this->getSnellenVisualAcuityForLeft($patient, $include_nr_values, $before_date, $use_context);
    }

    /**
     * get the va from the given episode for the right side of the episode patient.
     * @param Pateint $patient
     * @param bool $include_nr_values
     * @param string $before_date
     * @param bool $use_context
     * @return OphCiExamination_VisualAcuity_Reading
     * @deprecated since v4.0 use getSnellenVisualAcuityForRight instead
     */
    public function getLetterVisualAcuityForEpisodeRight(
        $patient,
        $include_nr_values = false,
        $before_date = null,
        $use_context = false
    ) {
        trigger_error("getLetterVisualAcuityForEpisodeRight is deprecated, use getSnellenVisualAcuityForRight", E_USER_DEPRECATED);
        return $this->getSnellenVisualAcuityForRight($patient, $include_nr_values, $before_date, $use_context);
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
        $conclusion = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_Conclusion',
            $patient,
            $use_context
        );

        if ($conclusion) {
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
        $management = $this->getLatestElement(
            'models\Element_OphCiExamination_Management',
            $patient,
            $use_context
        );

        if ($management) {
            return $management->comments;
        }
    }

    /**
     * retrieves all the patient summary comments
     *
     * @param \Patient $patient
     * @param $use_context
     *
     * @return array
     */
    public function getManagementSummaries($patient, $use_context = false)
    {
        $management_summaries = $this->getElements(
            'models\Element_OphCiExamination_Management',
            $patient,
            $use_context
        );
        if ($management_summaries) {
            $summary = [];
            $managment_summaries = [];
            foreach ($management_summaries as $summaries) {
                $service = $summaries->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->short_name;
                $user = \User::model()->findByPk($summaries->event->last_modified_user_id);
                $user_name = $user->first_name . ' ' . $user->last_name;
                $summary_obj = new \stdClass();
                $created_date = \Helper::convertDate2NHS($summaries->event->event_date);
                if (!array_key_exists($service, $summary)) {
                    $summary[$service] = $summaries->comments;
                    $summary_obj->service = $service;
                    $summary_obj->comments = $summaries->comments ?: $summaries->getSiblingString();
                    $summary_obj->comments = \OELinebreakReplacer::replace($summary_obj->comments);
                    $date_parts = explode(' ', $created_date);
                    $summary_obj->date = $date_parts;
                    $summary_obj->user = $user_name;
                    array_push($managment_summaries, $summary_obj);
                }
            }
            return $managment_summaries;
        }
        $summary = [];
        return $summary;
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
        $ac = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_AdnexalComorbidity',
            $patient,
            $use_context
        );

        if ($ac) {
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
        $ac = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_AdnexalComorbidity',
            $patient,
            $use_context
        );

        if ($ac) {
            return $ac->left_description;
        }
    }

    public function getLetterDRRetinopathyLeft($patient, $use_context = false)
    {
        return $this->getLetterDRRetinopathy($patient, 'left', $use_context);
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
        $dr = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_DRGrading',
            $patient,
            $use_context
        );

        if ($dr) {
            $res = $dr->{$side . '_nscretinopathy'};
            if ($dr->{$side . '_nscretinopathy_photocoagulation'}) {
                $res .= ' and evidence of photocoagulation';
            } else {
                $res .= ' and no evidence of photocoagulation';
            }

            return $res;
        }
    }

    public function getLetterDRRetinopathyRight($patient, $use_context = false)
    {
        return $this->getLetterDRRetinopathy($patient, 'right', $use_context);
    }

    public function getLetterDRMaculopathyLeft($patient, $use_context = false)
    {
        return $this->getDRMaculopathy($patient, 'left', $use_context);
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
        $dr = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_DRGrading',
            $patient,
            $use_context
        );

        if ($dr) {
            $res = $dr->{$side . '_nscmaculopathy'};
            if ($dr->{$side . '_nscmaculopathy_photocoagulation'}) {
                $res .= ' and evidence of photocoagulation';
            } else {
                $res .= ' and no evidence of photocoagulation';
            }

            return $res;
        }
    }

    public function getLetterDRMaculopathyRight($patient, $use_context = false)
    {
        return $this->getDRMaculopathy($patient, 'right', $use_context);
    }

    public function getLetterDRClinicalRetLeft($patient, $use_context = false)
    {
        return $this->getDRClinicalRet($patient, 'left', $use_context);
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
        $dr = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_DRGrading',
            $patient,
            $use_context
        );

        if ($dr) {
            $ret = $dr->{$side . '_clinicalret'};
            if ($ret) {
                return $ret->name;
            };
        }
    }

    public function getLetterDRClinicalRetRight($patient, $use_context = false)
    {
        return $this->getDRClinicalRet($patient, 'right', $use_context);
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
        return $this->getDRClinicalMac($patient, 'left', $use_context);
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
        $dr = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_DRGrading',
            $patient,
            $use_context
        );

        if ($dr) {
            $mac = $dr->{$side . '_clinicalmac'};
            if ($mac) {
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

    public function getLetterDRClinicalMacRight($patient, $use_context = false)
    {
        return $this->getDRClinicalMac($patient, 'right', $use_context);
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
        $va = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_LaserManagement',
            $patient,
            $use_context
        );

        if ($va) {
            return $va->getLetter_string();
        }
    }

    /**
     * @param $patient
     * @param false $use_context
     * @return string|null
     */
    public function getLetterAdviceGiven($patient, $use_context = false): ?string
    {
        $adg = $this->getElementFromLatestVisibleEvent(
            'models\AdviceGiven',
            $patient,
            $use_context
        );

        if ($adg) {
            return $adg->getLetter_string();
        }
        return null;
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
        $lm = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_LaserManagement',
            $patient,
            $use_context
        );

        if ($lm) {
            $result = $lm->comments;
            $m = $this->getElementFromLatestVisibleEvent(
                'models\Element_OphCiExamination_Management',
                $patient,
                $use_context
            );

            if ($m) {
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
        $follow_up_text = '';

        $element = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_ClinicOutcome',
            $patient,
            $use_context
        );

        if ($element) {
            $index = 0;
            foreach ($element->entries as $entry) {
                if ($entry->followup_quantity) {
                    if ($index > 0) {
                        $follow_up_text .= ' AND ';
                    }
                    $follow_up_text .= $entry->followup_quantity . ' ' . $entry->getPeriodLabel();
                    $index++;
                }
            }
        }

        $api = \Yii::app()->moduleAPI->get('PatientTicketing');
        if ($api) {
            $patient_ticket_followup = $api->getLatestFollowUp($patient);
            if ($patient_ticket_followup && isset($patient_ticket_followup['followup_quantity']) && isset($patient_ticket_followup['followup_period'])) {
                if ($patient_ticket_followup['followup_quantity'] === "1") {
                    $patient_ticket_followup['followup_period'] = rtrim($patient_ticket_followup['followup_period'], 's');
                }

                if (!isset($patient_ticket_followup['assignment_date']) || !isset($element->event->event_date) || ($element->event->event_date < $patient_ticket_followup['assignment_date'])) {
                    if (!empty($follow_up_text)) {
                        $follow_up_text .= ' AND ';
                    }

                    if (!empty($patient_ticket_followup['followup_quantity']) && !empty($patient_ticket_followup['followup_period'])) {
                        $follow_up_text .= $patient_ticket_followup['followup_quantity'] . ' ' . $patient_ticket_followup['followup_period'];
                        if (isset($patient_ticket_followup['clinic_location']) && !empty($patient_ticket_followup['clinic_location'])) {
                            $follow_up_text .= ' in the ' . $patient_ticket_followup['clinic_location'];
                        }
                    }
                }
            }
        }

        return $follow_up_text;
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
        $element_type = \ElementType::model()->findByPk($element_type_id);
        if (!$element_type) {
            throw new Exception("Unknown element type: $element_type_id");
        }
        // with introduction of change tracking episode, need to ensure we are retrieving
        // letter strings from the visible events.
        // note that if elements with letter strings start to track in the change episode
        // this will need to be revisited.
        $element = $this->getElementFromLatestVisibleEvent(
            $element_type->class_name,
            $patient,
            $use_context
        );

        if ($element) {
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

        $event = $this->getLatestVisibleEvent($patient, $use_context);
        if ($event) {
            $criteria = new \CDbCriteria();
            $criteria->compare('event_type_id', $event->event_type_id);
            $criteria->order = 'display_order';

            foreach (\ElementType::model()->findAll($criteria) as $element_type) {
                $class = $element_type->class_name;

                if (!class_exists($class)) {
                    continue;
                }

                $element = $class::model()->find('event_id=?', array($event->id));
                if ($element) {
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
        $events = $this->getEvents($patient, $use_context);

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

            $el = models\Element_OphCiExamination_InjectionManagementComplex::model()->find($criteria);
            if ($el) {
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

                $el = models\Element_OphCiExamination_InjectionManagementComplex::model()->find($criteria);
                if ($el) {
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

            $el = models\Element_OphCiExamination_InjectionManagementComplex::model()->find($criteria);
            if ($el) {
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
    public function getOCTForSide($patient, $side, $use_context = false)
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
        $events = $this->getEvents($patient, $use_context);
        if ($events) {
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

                $el = models\Element_OphCiExamination_OCT::model()->with('event')->find($criteria);
                if ($el) {
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
    public function getLetterInvestigationDescription($patient, $use_context = false)
    {

        $el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_Investigation',
            $patient,
            $use_context
        );

        if ($el) {
            return $el->description;
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
     * get the maximum CRT for the patient for the given side.
     *
     * @param $patient
     * @param $side
     * @param $use_context
     * @return mixed
     */
    public function getLetterMaxCRTForSide($patient, $side, $use_context = false)
    {
        $el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_OCT',
            $patient,
            $use_context
        );

        if ($el) {
            return $el->{$side . '_crt'} . 'um';
        }
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
     * Get the central SFT for the given patient for the given side.
     *
     * @param $patient
     * @param $side
     * @param $use_context
     * @return mixed
     */
    public function getLetterCentralSFTForSide($patient, $side, $use_context = false)
    {
        $el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_OCT',
            $patient,
            $use_context
        );

        if ($el) {
            return $el->{$side . '_sft'} . 'um';
        }
    }

    /**
     * wrapper function to get the Central SFT for the right side of the patient.
     *
     * @param $patient
     * @param bool $use_context
     *
     * @return mixed
     */
    public function getLetterCentralSFTRight($patient, $use_context = false)
    {
        return $this->getLetterCentralSFTForSide($patient, 'right', $use_context);
    }

    /**
     * Get the default findings string from Injection Management complex in the latest examination event (if it exists).
     *
     * make this work with both injection management elements (i.e. if complex not being used, use basic)
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getLetterInjectionManagementComplexFindings($patient, $use_context = false)
    {
        $el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_InjectionManagementComplex',
            $patient,
            $use_context
        );

        if ($el) {
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
        $el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_InjectionManagementComplex',
            $patient,
            $use_context
        );

        if ($el) {
            $d = $el->{$side . '_diagnosis1'};
            if ($d) {
                $res = $d->term;
                $d2 = $el->{$side . '_diagnosis2'};
                if ($d2) {
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
        $principal_eye = $this->getPrincipalEye($patient);
        $el = $this->getElementFromLatestVisibleEvent(
            models\Element_OphCiExamination_AnteriorSegment_CCT::class,
            $patient,
            $use_context
        );
        if ($principal_eye && $el) {
            if ($this->needsLeft($principal_eye) && $el->hasLeft()) {
                $str .= 'Left Eye: ' . $el->left_value . ' µm using ' . $el->left_method->name . '. ';
            }
            if ($this->needsRight($principal_eye) && $el->hasRight()) {
                $str .= 'Right Eye: ' . $el->right_value . ' µm using ' . $el->right_method->name . '. ';
            }
        }
        return $str;
    }

    /*
     * Central corneal thickness, left eye reading no units
     * @param $patient
     * @param $use_context
     */

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
        $principal_eye = $this->getPrincipalEye($patient, true);
        $el = $this->getElementFromLatestVisibleEvent(
            'models\VanHerick',
            $patient,
            $use_context
        );

        if ($principal_eye && $el) {
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
        $principal_eye = $this->getPrincipalEye($patient, true);
        $el = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_OpticDisc',
            $patient,
            $use_context
        );
        if ($principal_eye && $el) {
            return $this->getEyedrawDescription($el, $principal_eye);
        }
        return $str;
    }

    /*
     * Central corneal thickness, right eye reading no units
     * @param $patient
     * @param $use_context
     */

    /**
     * Get the latest left CCT measurement.
     *
     * @param \Patient $patient
     * @param $use_context
     * @return string
     */
    public function getCCTLeft($patient, $use_context = false)
    {
        $el = $this->getLatestElement(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context
        );

        if ($el) {
            if ($el->hasLeft()) {
                return $el->left_value . ' µm';
            }
        }
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
        $el = $this->getLatestElement(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context
        );

        if ($el) {
            if ($el->hasRight()) {
                return $el->right_value . ' µm';
            }
        }
    }

    /**
     * @param Patient $patient
     * @param $use_context
     * @return string|null;
     */
    public function getCCTAbbr(\Patient $patient, $use_context = false)
    {
        $cct = $this->getLatestElement(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context
        );

        if ($cct) {
            $readings = array();
            if ($cct->hasRight()) {
                $readings[] = 'r:' . $cct->right_value;
            }
            if ($cct->hasLeft()) {
                $readings[] = 'l:' . $cct->left_value;
            }

            return implode(', ', $readings);
        }
    }

    /*
     * Intraocular pressure, left eye reading no units
     * @param $patient
     * @param $use_context
     */

    /**
     * Get the latest CCT Date - both eyes.
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getCCTDate($patient, $use_context = false)
    {
        $cct = $this->getLatestElement(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context
        );
        if (isset($cct) && isset($cct->event)) {
            return $cct->event->event_date;
        }
        return null;
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
        $el = $this->getLatestElement(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_GlaucomaRisk',
            $patient,
            $use_context
        );

        if ($el) {
            return $el->risk->name;
        }
    }

    /*
     * Intraocular pressure, right eye reading no units
     * @param $patient
     * @param $use_context
     */

    public function getLetterIOPReadingLeftNoUnitsLast6weeks($patient, $use_context = false)
    {
        $iop = $this->getIntraocularPressureElement($patient, false, '-6 weeks');

        if ($iop) {
            $reading = $iop->getReading('left');
            if ($reading) {
                return $reading;
            }
        }
        return 'NR';
    }

    public function getLetterIOPReadingRightNoUnitsLast6weeks($patient, $use_context = false)
    {
        $iop = $this->getIntraocularPressureElement($patient, false, '-6 weeks');

        if ($iop) {
            $reading = $iop->getReading('right');
            if ($reading) {
                return $reading;
            }
        }
        return 'NR';
    }

    public function getIOPValuesAsTable($patient, $use_context = false)
    {
        $iop = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_IntraocularPressure',
            $patient,
            $use_context
        );
        if ($iop) {
            $iopVals = $iop->getValues();
            $i = 0;
            $output = '<table class="borders">';
            while (isset($iopVals['right'][$i]) || isset($iopVals['left'][$i])) {
                if ($i === 0) {
                    $lCCT = $this->getCCTLeftNoUnits($patient);
                    $rCCT = $this->getCCTRightNoUnits($patient);
                    $output .= '<colgroup><col class="cols-6"><col class="cols-6"></colgroup><tr><td>RE [' . $rCCT . ']</td><td>LE [' . $lCCT . ']</td></tr>';
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

    public function getCCTLeftNoUnits($patient, $use_context = false)
    {
        $el = $this->getLatestElement(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context
        );

        if ($el) {
            if ($el->hasLeft()) {
                return $el->left_value;
            }
        }

        return 'NR';
    }

    public function getCCTRightNoUnits($patient, $use_context = false)
    {
        $el = $this->getLatestElement(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
            $patient,
            $use_context
        );

        if ($el) {
            if ($el->hasRight()) {
                return $el->right_value;
            }
        }

        return 'NR';
    }

    public function getTargetIOP($patient, $use_context = false)
    {
        $oManPlan = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_OverallManagementPlan',
            $patient,
            $use_context
        );

        if ($oManPlan) {
            return array(
                'left' => ($oManPlan->left_target_iop ? $oManPlan->left_target_iop->name : null),
                'right' => ($oManPlan->right_target_iop ? $oManPlan->right_target_iop->name : null),
            );
        }
    }

    /**
     * Examination diagnoses and findings
     * @param $patient
     * @param $use_context
     * @var $diag models\Element_OphCiExamination_Diagnoses
     */
    public function getLetterDiagnosesAndFindings($patient, $use_context = false)
    {
        $diag = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_Diagnoses',
            $patient,
            $use_context
        );

        if ($diag) {
            return $diag->letter_string;
        }

        return '[No diagnoses in latest event]';
    }

    /**
     * Return no_risks_date of last HistoryRisk of a Patient
     * @param Patient $patient
     * @param bool $use_context
     * @return mixed|null
     */
    public function getNoAllergiesDate(\Patient $patient, $use_context = false)
    {
        $element = $this->getLatestElement('models\Allergies', $patient, $use_context);
        if ($element) {
            return $element->no_allergies_date;
        }
        return null;
    }

    /**
     * @param Patient $patient
     * @param bool $use_context
     * @return mixed|null
     */
    public function getNoRisksDate(\Patient $patient, $use_context = false)
    {
        $element = $this->getLatestElement('models\HistoryRisks', $patient, $use_context);
        if ($element) {
            return $element->no_risks_date;
        }
        return null;
    }

    /**
     * @param Patient $patient
     * @param bool $use_context
     * @return mixed|null
     */
    public function getNoSystemicDiagnosesDate(\Patient $patient, $use_context = false)
    {
        $element = $this->getLatestElement('models\SystemicDiagnoses', $patient, $use_context);
        if ($element) {
            return $element->no_systemic_diagnoses_date;
        }
        return null;
    }

    /**
     * @param Patient $patient
     * @param bool $use_context
     * @return mixed|null
     */
    public function getNoOphthalmicDiagnosesDate(\Patient $patient, $use_context = false)
    {
        $element = $this->getLatestElement('models\Element_OphCiExamination_Diagnoses', $patient, $use_context);
        if ($element) {
            return $element->no_ophthalmic_diagnoses_date;
        }
        return null;
    }

    /**
     * Return list of allergies belonging to a patient.
     *
     * @param \Patient $patient
     * ? update to reflect change to allergies
     * @return string
     */
    public function getAllergies(\Patient $patient)
    {
        if (count($patient->allergies)) {
            return $patient->getAllergiesSeparatedString($prefix = '', $separator = ', ', $lastSeparatorNeeded = false);
        }

        return 'none';
    }

    /**
     * To get the visual acuity from the element based on the all episodes for the patient
     * @param \Patient $patient
     * @param $side
     * @param $type
     * @param $element
     * @return null
     * @throws \Exception
     */
    public function getMostRecentVAForPatient(\Patient $patient, $side, $type, $element)
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
        $outcome = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_ClinicOutcome',
            $patient,
            $use_context
        );

        if ($outcome) {
            return $outcome->comments;
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
        $element = $this->getLatestElement(
            'models\Element_OphCiExamination_ClinicOutcome',
            $patient,
            $use_context
        );

        if ($element) {
            $str .= '<table><tbody>';
            foreach ($element->entries as $index => $entry) {
                if ($index > 0) {
                    $str .= '<tr><td>AND</td>';
                } else {
                    $str .= '<tr><td></td>';
                }
                $str .= '<td style="text-align:left">' . $entry->status->name;
                if ($entry->status->followup) {
                    $str .= " in {$entry->followup_quantity} {$entry->getPeriodLabel()} ";

                    if (!empty($entry->followup_comments)) {
                        $str .= "({$entry->followup_comments})";
                    }
                }
                $str .= "</td></tr>";
            }
            $str .= '</tbody></table>';

            if ($element->comments) {
                $str .= "<strong>Comments:</strong> {$element->comments}";
            }
        }
        return $str;
    }

    public function getLatestTriagePriority(\Patient $patient, $worklist, $use_context = false)
    {
        $ret = '';
        $triage_elements = $this->getElements(
            'models\Element_OphCiExamination_Triage',
            $patient,
            $use_context,
        );
        if (count($triage_elements) > 0) {
            $priority = $triage_elements[0]->triage->priority;
            $ret = "<i class=\"circle-{$priority->label_colour}\" data-tt-type=\"basic\" data-tooltip-content=\"Priority: {$priority->description}\"></i>";
        }
        return $ret;
    }

    public function getLatestOutcomeRiskStatus(\Patient $patient, $worklist, $use_context = false)
    {
        $ret = "<i class='oe-i triangle-grey js-has-tooltip' data-tooltip-content='No Risk Status Recorded'></i>";

        $discharge_elements_criteria = new \CDbCriteria();
        $discharge_elements_criteria->join = "JOIN ophciexamination_clinicoutcome_entry oce ON oce.element_id = t.id";
        $discharge_elements_criteria->join .= " JOIN ophciexamination_clinicoutcome_status ocs ON oce.status_id = ocs.id";
        $discharge_elements_criteria->addCondition("LOWER(ocs.name) = 'discharge'");
        $discharge_elements = $this->getElements(
            'models\Element_OphCiExamination_ClinicOutcome',
            $patient,
            $use_context,
            null,
            $discharge_elements_criteria,
        );

        $episode_latest_discharges = array();
        foreach ($discharge_elements as $discharge_element) {
            $episode_id = $discharge_element->event->episode->id;
            if (!isset($episode_latest_discharges[$episode_id])) {
                $episode_latest_discharges[$episode_id] = $discharge_element->getLatestEntry('discharge');
            }
        }

        $elements = array();
        // find the latest follow up element with risk status entry for each none discharged episode
        foreach ($patient->episodes as $episode) {
            // get discharged episode status object
            $discharge_status = \EpisodeStatus::model()->find('`key` = :key', array(':key' => 'discharged'));
            $latest_risk_status_entry_criteria = new \CDbCriteria();
            $latest_risk_status_entry_criteria->join = "JOIN ophciexamination_clinicoutcome_entry oce ON oce.element_id = t.id";
            $latest_risk_status_entry_criteria->addCondition("oce.risk_status_id IS NOT NULL");
            $latest_risk_status_entry_criteria->compare('episode.id', $episode->id);
            if ($discharge_status) {
                $latest_risk_status_entry_criteria->addCondition("episode.episode_status_id != :ep_status_id");
                $latest_risk_status_entry_criteria->params[':ep_status_id'] = $discharge_status->id;
            }
            if (isset($episode_latest_discharges[$episode->id])) {
                $latest_risk_status_entry_criteria->addCondition("oce.created_date > :discharge_date");
                $latest_risk_status_entry_criteria->params[':discharge_date'] = $episode_latest_discharges[$episode->id]->created_date;
            }
            $latest_risk_status_entry_criteria->limit = 1;
            // get all follow up elements with risk status entry in none discharged episodes
            $risk_status_element = $this->getElements(
                'models\Element_OphCiExamination_ClinicOutcome',
                $patient,
                $use_context,
                null,
                $latest_risk_status_entry_criteria,
            );
            if (count($risk_status_element)) {
                $elements[] = $risk_status_element[0];
            }
        }

        $entries = array();
        foreach ($elements as $element) {
            $ele_entries = array_filter($element->entries, function ($entry) {
                if ($entry->risk_status) {
                    return $entry;
                }
            });
            $entries = array_merge($entries, $ele_entries);
        }

        if (count($entries) > 1) {
            $ret = models\Element_OphCiExamination_ClinicOutcome::getRiskStatuses($entries);
        } elseif (count($entries) === 1) {
            $entry = $entries[0];
            $ret = $entry->getRiskStatusLabel(true)['icon'];
        }
        return $ret;
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
    public function getCataractSurgicalManagementAsText(\Patient $patient, $use_context = false)
    {
        $str = '';
        $element = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_CataractSurgicalManagement',
            $patient,
            $use_context
        );

        if ($element) {
            if ($element->eye_id === (string) \Eye::BOTH) {
                $first_eye_id = (string)\OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye::FIRST_EYE;
                if ($element->right_eye_id === $first_eye_id) {
                    $first_eye = 'right';
                    $second_eye = 'left';
                } else {
                    $first_eye = 'left';
                    $second_eye = 'right';
                }

                $str = "Listed for {$first_eye} cataract surgery followed by {$second_eye} cataract surgery.";
            } else {
                $str = "Listed for {$element->eye->name} cataract surgery.";
            }

            $str .= PHP_EOL;
        }

        return $str;
    }

    public function getCataractSurgicalManagementAsTable(Patient $patient, $use_context = false)
    {
        $element = $this->getElementFromLatestVisibleEvent(
            'models\Element_OphCiExamination_CataractSurgicalManagement',
            $patient,
            $use_context
        );

        $first_eye_id = (string)\OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye::FIRST_EYE;
        $refractive_categories = [0 => 'Emmetropia', 1 => 'Myopia', 2 => 'Other'];

        if ($element) {
            $is_both_eyes = $element->eye_id === (string) \Eye::BOTH;
            $eye_sides = $is_both_eyes ? ($element->left_eye_id === $first_eye_id ? ['1st' => 'left', '2nd' => 'right'] : ['1st' => 'right', '2nd' => 'left']) : ($element->eye_id === (string)\Eye::RIGHT ? ['right'] : ['left']);
            ob_start(); ?>
            <table style="border-collapse: collapse; width: 80%;" border="1">
                <tbody>
                <?php foreach ($eye_sides as $key => $side) { ?>
                    <tr>
                        <th style="width: 10%;">
                            <strong>
                                <?= ucfirst($side) ?> eye <?= $is_both_eyes ? "($key)" : '' ?>
                            </strong>
                        </th>
                        <th style="width: 30%;"></th>
                    </tr>
                    <tr>
                        <th style="width: 10%;">Reason for surgery:</th>
                        <th style="width: 30%;"><?= ($element->{$side . 'ReasonForSurgery'} ? $element->{$side . 'ReasonForSurgery'}->name : 'N/A') ?></th>
                    </tr>
                    <tr>
                        <th style="width: 10%;">Guarded prognosis:</th>
                        <th style="width: 30%;"><?= (string)$element->{$side . '_guarded_prognosis'} === '1' ? 'Yes' : 'No' ?></th>
                    </tr>
                    <tr>
                        <th style="width: 10%;">Refractive target:</th>
                        <?php $refractive_target = 'Not recorded';
                        if ($element->{$side . '_target_postop_refraction'}) {
                            $refractive_target = $element->{$side . '_target_postop_refraction'} . 'D' . " ({$refractive_categories[$element->{$side . '_refraction_category'}]})";
                        } ?>
                        <th style="width: 30%;"><?= $refractive_target ?></th>
                    </tr>

                    <tr>
                        <th>Comments:</th>
                        <th><?=!empty($element->{$side.'_notes'})?$element->{$side.'_notes'}:'No comments'?></th>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php }
        return str_replace(array("\r","\n"), '', trim(ob_get_clean()));
    }

    /**
     *
     * @param $patient
     * @param $risk_name
     * @return array|null
     */
    public function getRiskByName($patient, $risk_name)
    {
        $widget = $this->getWidget(
            'OEModule\OphCiExamination\widgets\HistoryRisks',
            array('mode' => HistoryRisks::$DATA_MODE, 'patient' => $patient)
        );
        $entry = $widget->element->getRiskEntryByName($risk_name);
        if ($entry) {
            $status = null;
            switch ($entry->has_risk) {
                case (models\HistoryRisksEntry::$PRESENT):
                    $status = true;
                    break;
                case (models\HistoryRisksEntry::$NOT_PRESENT):
                    $status = false;
                    break;
                default:
                    $status = null;
                    break;
            }

            return array(
                'name' => (string)$entry->risk,
                'status' => $status,
                'comments' => $entry->comments,
                'date' => $entry->element->event->event_date
            );
        }
        return null;
    }

    /**
     *
     * @param $patient
     * @param $allergy_name
     * @return mixed
     */
    public function getAllergyByName($patient, $allergy_name)
    {
        $widget = $this->getWidget(
            'OEModule\OphCiExamination\widgets\Allergies',
            array('mode' => Allergies::$DATA_MODE, 'patient' => $patient)
        );
        $entry = $widget->element->getAllergyEntryByName($allergy_name);
        if ($entry) {
            $status = null;
            switch ($entry->has_risk) {
                case (models\AllergyEntry::$PRESENT):
                    $status = true;
                    break;
                case (models\AllergyEntry::$NOT_PRESENT):
                    $status = false;
                    break;
                default:
                    $status = null;
                    break;
            }

            return array(
                'name' => (string)$entry->allergy,
                'status' => $status,
                'comments' => $entry->comments,
                'date' => $entry->element->event->event_date
            );
        }
    }

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

    public function getRequiredRisks(\Patient $patient, $firm_id = null)
    {
        $firm_id = $firm_id ? $firm_id : \Yii::app()->session['selected_firm_id'];
        $firm = \Firm::model()->findByPk($firm_id);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        $criteria = new \CDbCriteria();
        $criteria->addCondition("(t.subspecialty_id = :subspecialty_id OR t.subspecialty_id IS NULL)");
        $criteria->addCondition("(t.firm_id = :firm_id OR t.firm_id IS NULL)");
        $criteria->with = array(
            'entries' => array(
                'condition' =>
                    '((age_min <= :age OR age_min IS NULL) AND' .
                    '(age_max >= :age OR age_max IS NULL)) AND' .
                    '(gender = :gender OR gender IS NULL)'
            ),
        );

        $criteria->params['subspecialty_id'] = $subspecialty_id;
        $criteria->params['firm_id'] = $firm->id;
        $criteria->params['age'] = $patient->age;
        $criteria->params['gender'] = $patient->gender;

        $sets = models\OphCiExaminationRiskSet::model()->findAll($criteria);

        $required = array();
        foreach ($sets as $set) {
            if ($set->entries) {
                foreach ($set->entries as $ophciexamination_risks) {
                    $risk = $ophciexamination_risks->ophciexamination_risk;
                    if (isset($risk) && isset($risk->id)) {
                        $required[$risk->id] = $risk;
                    }
                }
            }
        }

        return $required;
    }

    /**
     * Get required allergies
     * @param Patient $patient
     * @param null $firm_id
     * @return array
     */
    public function getRequiredAllergies(\Patient $patient, $firm_id = null)
    {
        $firm_id = $firm_id ? $firm_id : \Yii::app()->session['selected_firm_id'];
        $firm = \Firm::model()->findByPk($firm_id);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        $criteria = new \CDbCriteria();
        $criteria->addCondition("(t.subspecialty_id = :subspecialty_id OR t.subspecialty_id IS NULL)");
        $criteria->addCondition("(t.firm_id = :firm_id OR t.firm_id IS NULL)");
        $criteria->with = array(
            'entries' => array(
                'condition' =>
                    '((age_min <= :age OR age_min IS NULL) AND' .
                    '(age_max >= :age OR age_max IS NULL)) AND' .
                    '(gender = :gender OR gender IS NULL)'
            ),
        );

        $criteria->params['subspecialty_id'] = $subspecialty_id;
        $criteria->params['firm_id'] = $firm->id;
        $criteria->params['age'] = $patient->age;
        $criteria->params['gender'] = $patient->gender;

        $sets = models\OphCiExaminationAllergySet::model()->findAll($criteria);

        $required = array();
        foreach ($sets as $set) {
            if ($set->entries) {
                foreach ($set->entries as $allergy_entry) {
                    $allergy = $allergy_entry->ophciexaminationAllergy;
                    if (isset($allergy) && isset($allergy->id)) {
                        $required[$allergy->id] = $allergy;
                    }
                }
            }
        }

        return $required;
    }

    /**
     * Get required pupillary abnormalities
     * @param Patient $patient
     * @param null $firm_id
     * @return array
     */
    public function getRequiredAbnormalities(\Patient $patient, $firm_id = null)
    {
        $firm_id = $firm_id ? $firm_id : \Yii::app()->session['selected_firm_id'];
        $firm = \Firm::model()->findByPk($firm_id);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        $criteria = new \CDbCriteria();
        $criteria->addCondition("(t.subspecialty_id = :subspecialty_id OR t.subspecialty_id IS NULL)");
        $criteria->addCondition("(t.firm_id = :firm_id OR t.firm_id IS NULL)");
        $criteria->with = array(
            'entries' => array(
                'condition' =>
                    '((age_min <= :age OR age_min IS NULL) AND' .
                    '(age_max >= :age OR age_max IS NULL)) AND' .
                    '(gender = :gender OR gender IS NULL)'
            ),
        );

        $criteria->params['subspecialty_id'] = $subspecialty_id;
        $criteria->params['firm_id'] = $firm->id;
        $criteria->params['age'] = $patient->age;
        $criteria->params['gender'] = $patient->gender;

        $sets = models\OphCiExaminationPupillaryAbnormalitySet::model()->findAll($criteria);

        $required = array();
        foreach ($sets as $set) {
            if ($set->entries) {
                foreach ($set->entries as $abnormality_entry) {
                    $abnormality = $abnormality_entry->ophciexaminationAbnormality;
                    if ($abnormality && $abnormality->id) {
                        $required[$abnormality->id] = $abnormality;
                    }
                }
            }
        }

        return $required;
    }

    /**
     * Returns the required Disorders (Systemic Diagnoses)
     *
     * @param Patient $patient
     * @param null $firm_id
     * @return array of Disorders
     */
    public function getRequiredSystemicDiagnoses(\Patient $patient, $firm_id = null)
    {
        $firm_id = $firm_id ? $firm_id : \Yii::app()->session['selected_firm_id'];
        $firm = \Firm::model()->findByPk($firm_id);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

        $criteria = new \CDbCriteria();
        $criteria->addCondition("(t.subspecialty_id = :subspecialty_id OR t.subspecialty_id IS NULL)");
        $criteria->addCondition("(t.firm_id = :firm_id OR t.firm_id IS NULL)");
        $criteria->with = array(
            'entries' => array(
                'condition' =>
                    '((age_min <= :age OR age_min IS NULL) AND' .
                    '(age_max >= :age OR age_max IS NULL)) AND' .
                    '(gender = :gender OR gender IS NULL)'
            ),
        );

        $criteria->params['subspecialty_id'] = $subspecialty_id;
        $criteria->params['firm_id'] = $firm->id;
        $criteria->params['age'] = $patient->age;
        $criteria->params['gender'] = $patient->gender;

        $sets = models\OphCiExaminationSystemicDiagnosesSet::model()->findAll($criteria);

        $required = array();
        foreach ($sets as $set) {
            if ($set->entries) {
                foreach ($set->entries as $entry) {
                    $disorder = $entry->disorder;
                    if (isset($disorder) && isset($disorder->id)) {
                        $required[$disorder->id] = $disorder;
                    }
                }
            }
        }

        return $required;
    }

    /**
     * Returns the required Operations for Ophthalmic Surgical History Element
     *
     * @param Patient $patient
     * @param null|int $firm_id
     * @return array of operations
     */
    public function getRequiredOphthalmicSurgicalHistory(\Patient $patient, $firm_id = null)
    {
        $firm_id = $firm_id ? $firm_id : \Yii::app()->session['selected_firm_id'];
        $firm = \Firm::model()->findByPk($firm_id);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;
        $criteria = new \CDbCriteria();
        $criteria->addCondition("(t.subspecialty_id = :subspecialty_id OR t.subspecialty_id IS NULL)");
        $criteria->addCondition("(t.firm_id = :firm_id OR t.firm_id IS NULL)");
        $criteria->with = array(
            'entries' => array(
                'condition' =>
                    '((age_min <= :age OR age_min IS NULL) AND' .
                    '(age_max >= :age OR age_max IS NULL)) AND' .
                    '(gender = :gender OR gender IS NULL)'
            ),
        );
        $criteria->params['subspecialty_id'] = $subspecialty_id;
        $criteria->params['firm_id'] = $firm->id;
        $criteria->params['age'] = $patient->age;
        $criteria->params['gender'] = $patient->gender;
        $sets = models\SurgicalHistorySet::model()->findAll($criteria);
        $required = array();
        foreach ($sets as $set) {
            if ($set->entries) {
                foreach ($set->entries as $entry) {
                    if (isset($entry) && isset($entry->operation)) {
                        $required[] = $entry->operation;
                    }
                }
            }
        }
        return $required;
    }
    /**
     * Returns the required Operations for Systemic Surgical History Element
     *
     * @param Patient $patient
     * @param null|int $firm_id
     * @return array of operations
     */
    public function getRequiredSystemicSurgeries(\Patient $patient, $firm_id = null)
    {
        $firm_id = $firm_id ? $firm_id : \Yii::app()->session['selected_firm_id'];
        $firm = \Firm::model()->findByPk($firm_id);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;
        $criteria = new \CDbCriteria();
        $criteria->addCondition("(t.subspecialty_id = :subspecialty_id OR t.subspecialty_id IS NULL)");
        $criteria->addCondition("(t.firm_id = :firm_id OR t.firm_id IS NULL)");
        $criteria->with = [
            'entries' => [
                'condition' =>
                    '((age_min <= :age OR age_min IS NULL) AND' .
                    '(age_max >= :age OR age_max IS NULL)) AND' .
                    '(gender = :gender OR gender IS NULL)'
            ],
        ];
        $criteria->params['subspecialty_id'] = $subspecialty_id;
        $criteria->params['firm_id'] = $firm->id;
        $criteria->params['age'] = $patient->age;
        $criteria->params['gender'] = $patient->gender;
        $sets = models\SystemicSurgerySet::model()->findAll($criteria);
        $required = [];
        foreach ($sets as $set) {
            foreach ($set->entries as $entry) {
                if (isset($entry) && isset($entry->operation) && array_search($entry->operation, $required) === false) {
                    $required[] = $entry->operation;
                }
            }
        }
        return $required;
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
        $el = $this->getLatestElement(
            'models\Element_OphCiExamination_OverallManagementPlan',
            $patient,
            $use_context
        );

        if ($el) {
            $result .= 'Clinic Interval: ' . ($el->clinic_interval ?: 'NR') . "\n";
            $result .= 'Photo: ' . ($el->photo ?: 'NR') . "\n";
            $result .= 'OCT: ' . ($el->oct ?: 'NR') . "\n";
            $result .= 'Visual Fields: ' . ($el->hfa ?: 'NR') . "\n";
            $result .= 'Gonioscopy: ' . ($el->gonio ?: 'NR') . "\n";
            $result .= 'HRT: ' . ($el->hrt ?: 'NR') . "\n";

            if (!empty($el->comments)) {
                $result .= 'Glaucoma Management comments: ' . $el->comments . "\n";
            }

            $result .= "\n";
            if (isset($el->right_target_iop->name)) {
                $result .= 'Target IOP Right Eye: ' . $el->right_target_iop->name . " mmHg\n";
            }
            if (isset($el->left_target_iop->name)) {
                $result .= 'Target IOP Left Eye: ' . $el->left_target_iop->name . " mmHg\n";
            }
        }
        return $result;
    }

    /**
     * Handler for EIT shortcode
     *
     * @param Patient $patient
     * @param bool $use_context
     * @returns string
     */

    public function getReasonForNoTreatmentFromLastExamination(\Patient $patient, $use_context = false)
    {
        $result = "";
        $el = $this->getLatestElement('models\Element_OphCiExamination_InjectionManagementComplex', $patient, $use_context);

        if ($el) {
            /** @var models\Element_OphCiExamination_InjectionManagementComplex $el */

            if ($el->left_no_treatment) {
                $left_reason_text = $el->left_no_treatment_reason->other ? $el->left_no_treatment_reason_other : $el->left_no_treatment_reason->name;
                $result .= "Left Eye: $left_reason_text\n";
            }

            if ($el->right_no_treatment) {
                $right_reason_text = $el->right_no_treatment_reason->other ? $el->right_no_treatment_reason_other : $el->right_no_treatment_reason->name;
                $result .= "Right Eye: $right_reason_text\n";
            }
        }

        return $result;
    }

    /*
     * Last Blood Pressure (returned as systolic / diastolic - e.g, 100/80)
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLastBloodPressure(\Patient $patient, $use_context = true)
    {
        $bp = $this->getLatestElement(
            'models\Element_OphCiExamination_Observations',
            $patient,
            $use_context
        );

        if ($bp) {
            return $bp->blood_pressure_systolic . '/' . $bp->blood_pressure_diastolic;
        }
    }

    /*
     * Last O2 Stat
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLastO2Stat(\Patient $patient, $use_context = true)
    {
        $bp = $this->getLatestElement(
            'models\Element_OphCiExamination_Observations',
            $patient,
            $use_context
        );

        if ($bp) {
            return $bp->o2_sat;
        }
    }

    /*
     * Last Blood Glucose
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLastBloodGlucose(\Patient $patient, $use_context = true)
    {
        $bp = $this->getLatestElement(
            'models\Element_OphCiExamination_Observations',
            $patient,
            $use_context
        );

        if ($bp) {
            return $bp->blood_glucose;
        }
    }

    /*
     * Last HbA1c
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLastHbA1c(\Patient $patient, $use_context = true)
    {
        $bp = $this->getLatestElement(
            'models\Element_OphCiExamination_Observations',
            $patient,
            $use_context
        );

        if ($bp) {
            return $bp->hba1c;
        }
    }

    /*
     * Last height
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLastHeight(\Patient $patient, $use_context = true)
    {
        $bp = $this->getLatestElement(
            'models\Element_OphCiExamination_Observations',
            $patient,
            $use_context
        );
        if ($bp) {
            return $bp->height;
        }
    }

    /*
     * Last weight
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLastWeight(\Patient $patient, $use_context = true)
    {
        $bp = $this->getLatestElement(
            'models\Element_OphCiExamination_Observations',
            $patient,
            $use_context
        );

        if ($bp) {
            return $bp->weight;
        }
    }

    /*
     * Last BMI
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLastBMI(\Patient $patient, $use_context = true)
    {
        $bp = $this->getLatestElement(
            'models\Element_OphCiExamination_Observations',
            $patient,
            $use_context
        );

        if ($bp) {
            if (ceil($bp->weight) > 0 && ceil($bp->height) > 0) {
                $result = $bp->bmiCalculator($bp->weight, $bp->height);
                return $result;
            } else {
                return 'N/A';
            }
        }
    }

    /*
     * Last Pulse Measurement
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLastPulseMeasurement(\Patient $patient, $use_context = true)
    {
        $bp = $this->getLatestElement(
            'models\Element_OphCiExamination_Observations',
            $patient,
            $use_context
        );

        if ($bp) {
            return $bp->pulse;
        }
    }

    /*
     * Last Temperature Measurement
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getLastTemperatureMeasurement(\Patient $patient, $use_context = true)
    {
        $bp = $this->getLatestElement(
            'models\Element_OphCiExamination_Observations',
            $patient,
            $use_context
        );

        if ($bp) {
            return $bp->temperature;
        }
    }

    public function getPrincipalOphtalmicDiagnosis(\Episode $episode, $disorder_id = null)
    {
        $criteria = new \CDbCriteria();
        $criteria->join = 'JOIN et_ophciexamination_diagnoses et ON t.element_diagnoses_id = et.`id`';
        $criteria->join .= ' JOIN event ON event.id = et.`event_id`';
        $criteria->join .= ' JOIN episode ON event.`episode_id` = episode.id';
        $criteria->join .= ' JOIN patient ON episode.`patient_id` = patient.`id`';
        $criteria->addCondition("patient_id = :patient_id");
        $criteria->addCondition("episode_id = :episode_id");
        $criteria->addCondition("principal = 1");
        $criteria->params = [':patient_id' => $episode->patient_id, ':episode_id' => $episode->id];

        if ($disorder_id) {
            $criteria->addCondition("t.disorder_id = :disorder_id");
            $criteria->params[':disorder_id'] = $disorder_id;
        }

        $criteria->order = "t.created_date desc";
        $value = models\OphCiExamination_Diagnosis::model()->find($criteria);
        return $value;
    }

    /**
     * @param Patient $patient
     * @param array $exclude_ids
     * @param bool $exclude_medication_management_entries
     * @return \CActiveRecord[]
     */
    public function getEventMedicationUseItemsForPatient(Patient $patient, $exclude_ids = array(), $exclude_medication_management_entries = false) : array
    {
        $criteria = new \CDbCriteria(array('order' => 'event_date DESC'));
        $criteria->compare('event.deleted', 0);
        $criteria->addCondition('episode.patient_id = :id');
        $criteria->addNotInCondition('t.id', $exclude_ids);
        if ($exclude_medication_management_entries) {
            $criteria->addNotInCondition('t.usage_subtype', ['Management']);
        }
        $criteria->params = array_merge($criteria->params, array(':id' => $patient->id));

        return \EventMedicationUse::model()->with('event', 'event.episode')->findAll($criteria);
    }

    /**
     * Handler routine for DST shortcode
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLetterDrugsStartedToday($patient, $use_context = false)
    {
        $element = $this->getLatestElement('models\MedicationManagement', $patient, $use_context);
        if (!is_null($element)) {
            /** @var models\MedicationManagement $element */

            $entries = $element->getEntriesStartedToday();

            if ($entries) {
                $viewparams = array(
                    'entries' => $entries,
                    'table_class' => 'drugs-stopped-today'
                );

                return \Yii::app()->controller->renderPartial("_druglist", $viewparams, true);
            }
        }

        return "(no drugs started today)";
    }

    /**
     * Handler routine for DSP shortcode
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLetterDrugsStoppedToday($patient, $use_context = false)
    {
        $element = $this->getLatestElement('models\MedicationManagement', $patient, $use_context);
        if (!is_null($element)) {
            /** @var models\MedicationManagement $element */
            $entries = $element->getEntriesStoppedToday();
            if ($entries) {
                $viewparams = array(
                    'entries' => $entries,
                    'table_class' => 'drugs-stopped-today'
                );

                return \Yii::app()->controller->renderPartial("_druglist", $viewparams, true);
            }
        }

        return "(no drugs stopped today)";
    }

    /**
     * Handler routine for DCT shortcode
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getLetterDrugsContinuedToday($patient, $use_context = false)
    {
        $element = $this->getLatestElement('models\MedicationManagement', $patient, $use_context);
        if (!is_null($element)) {
            /** @var models\MedicationManagement $element */
            $entries = $element->getContinuedEntries();
            if ($entries) {
                $viewparams = array(
                    'entries' => $entries,
                    'table_class' => 'drugs-continued-today'
                );

                return \Yii::app()->controller->renderPartial("_druglist", $viewparams, true);
            }
        }

        return "(no drugs continued today)";
    }

    /**
     * Handler routine for DCC shortcode
     * @param $patient
     * @param bool $use_context
     * @return string
     * @throws \CException
     */
    public function getLetterDrugsChangedToday(Patient $patient, bool $use_context = false)
    {
        $element = $this->getLatestElement('models\MedicationManagement', $patient, $use_context);
        if (!is_null($element)) {
            /** @var models\MedicationManagement $element */
            $entries = $element->getChangedEntries();
            if ($entries) {
                $viewparams = array(
                    'entries' => $entries,
                    'table_class' => 'drugs-continued-today'
                );

                return \Yii::app()->controller->renderPartial("_druglist", $viewparams, true);
            }
        }

        return "(no drugs changed today)";
    }

    /**
     * Handler routine for MMS shortcode
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getMedicationManagementSummary($patient, $use_context = false)
    {
        $element = $this->getLatestElement('models\MedicationManagement', $patient, $use_context);

        if (!is_null($element)) {
            /** @var models\MedicationManagement $element */
            $viewparams = array(
                'started' => $element->getEntriesStartedToday(),
                'changed' => $element->getChangedEntries(),
                'stopped' => $element->getEntriesStoppedToday(),
                'continued' => $element->getContinuedEntries(),
                'table_class' => 'medication-management-summary'
            );

            return \Yii::app()->controller->renderPartial("_druglist_all", $viewparams, true);
        }

        return "(medication management summary not available)";
    }

    /*
     * Glaucoma Current Management Plan from latest Examination
     * @param $patient
     * @param bool $use_context
     * @return string
     */

    public function getGlaucomaCurrentPlan(\Patient $patient, $use_context = false)
    {
        $result = '';
        $el = $this->getLatestElement(
            'models\Element_OphCiExamination_CurrentManagementPlan',
            $patient,
            $use_context
        );

        if ($el) {
            $IOP = $el->getLatestIOP($patient);
            $result = '
                <table style="margin: 0 !important; height: 100%;">
                    <thead>
                        <tr>
                            <th colspan="2">Glaucoma Current Management Plan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="cols-6">
                                <table style="margin: 0 !important; height: 100%;">
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="text-align: center;">Right Eye</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>IOP:</td>
                                            <td>' . ($IOP ? $IOP["rightIOP"] . ' mmHg' : 'N/A') . '</td>
                                        </tr>
                                        <tr>
                                            <td>Glaucoma status:</td>
                                            <td>' . $el->right_glaucoma_status->name . '</td>
                                        </tr>
                                        <tr>
                                            <td>Drop-related problems:</td>
                                            <td>' . $el->{'right_drop-related_prob'}->name . '</td>
                                        </tr>
                                        <tr>
                                            <td>Drops:</td>
                                            <td>' . $el->right_drops->name . '</td>
                                        </tr>
                                        <tr>
                                            <td>Surgery:</td>
                                            <td>' . ($el->right_surgery ? $el->right_surgery->name : 'N/A') . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td class="cols-6">
                                <table style="margin: 0 !important; height: 100%;">
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="text-align: center;">Left Eye</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>IOP:</td>
                                            <td>' . ($IOP ? $IOP["leftIOP"] . ' mmHg' : 'N/A') . '</td>
                                        </tr>
                                        <tr>
                                            <td>Glaucoma status:</td>
                                            <td>' . $el->left_glaucoma_status->name . '</td>
                                        </tr>
                                        <tr>
                                            <td>Drop-related problems:</td>
                                            <td>' . $el->{'left_drop-related_prob'}->name . '</td>
                                        </tr>
                                        <tr>
                                            <td>Drops:</td>
                                            <td>' . $el->left_drops->name . '</td>
                                        </tr>
                                        <tr>
                                            <td>Surgery:</td>
                                            <td>' . ($el->left_surgery ? $el->left_surgery->name : 'N/A') . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            ';
        }
        return $result;
    }

    public function getCurrentOphthalmicDrugs(\Patient $patient, $use_context = false)
    {
        $element = $this->getElementFromLatestVisibleEvent(
            'models\HistoryMedications',
            $patient,
            $use_context
        );

        $widget = $this->getWidget(
            'OEModule\OphCiExamination\widgets\HistoryMedications',
            array('mode' => HistoryMedications::$DATA_MODE, 'patient' => $patient)
        );

        if (!$element) {
            $element = $widget->element;
        }

        $entries = $widget->getMergedEntries();

        $route_filter = function ($entry) {
            /** @var \EventMedicationUse $entry */
            return $entry->route_id && $entry->route->isEyeRoute();
        };

        $current_eye_meds = array_filter($entries['current'], $route_filter);
        $current_eye_meds = $element->mergeMedicationEntries($current_eye_meds, $widget);

        if (!$current_eye_meds) {
            return "
                <table class='standard'>
                    <tbody>
                        <tr>
                            <td>(no current eye medications)</td>
                        </tr>
                    </tbody>
                </table>
                    ";
        }

        /** @var \EventMedicationUse[] $current_eye_meds */

        ob_start();
        ?>
        <table class="standard borders current-ophtalmic-drugs">
            <colgroup>
                <col class="cols-5">
            </colgroup>
            <thead>
            <tr>
                <th class="empty"></th>
                <th>Dose (unit)</th>
                <th>Eye</th>
                <th>Frequency</th>
                <th>Until</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($current_eye_meds as $entry) { ?>
                    <?php
                        $tapers = [];
                        $comments = $entry->comments ?: null;
                        $stop_display_date = $entry->end_date ? \Helper::convertDate2NHS($entry->end_date): 'Ongoing';
                    if ($entry->prescription_item_id) {
                        $tapers = $entry->prescriptionItem->tapers;
                        $stop_date = $entry->prescriptionItem->stopDateFromDuration(false);
                        $stop_display_date = $stop_date ? \Helper::convertDate2NHS($stop_date->format('Y-m-d')) : $entry->prescriptionItem->medicationDuration->name;
                        $comments = $entry->prescriptionItem->comments ?: ($entry->comments ?: null);
                    }
                    ?>
                    <tr>
                    <td>
                        <?= $entry->getMedicationDisplay(true) ?>
                        <?= $comments ? ('<br /><br /><i>Comment: </i>' . $comments) : '' ?>
                    </td>
                    <td><?= $entry->dose . ($entry->dose_unit_term ? (' (' . $entry->dose_unit_term. ')') : '') ?></td>
                        <td>
                            <?=$entry->getLateralityDisplay(true)?>
                        </td>
                        <td><?= $entry->frequency ? $entry->frequency : ''; ?></td>
                        <td><?= $stop_display_date ?: 'Ongoing' ?></td>
                    </tr>
                    <?php if ($tapers) {
                        $taper_date = $stop_date;
                        foreach ($tapers as $taper) {
                            $taper_display_date = $taper->stopDateFromDuration($taper_date); ?>
                            <tr>
                                <td>
                                    <div class="oe-i child-arrow small no-click"></div>
                                    <i> then</i>
                                </td>
                                <td><?=$taper->dose . ($entry->dose_unit_term ? (' (' . $entry->dose_unit_term. ')') : '')?></td>
                                <td></td>
                                <td><?= $taper->frequency ? $taper->frequency->term : '' ?></td>
                                <td><?= $taper_display_date ? \Helper::convertDate2NHS($taper_display_date->format('Y-m-d')) : $taper->duration->name; ?></td>
                            </tr>
                            <?php $taper_date = $taper_display_date ?? $taper_date; ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>

        <?php return ob_get_clean();
    }

    public function getCurrentSystemicDrugs(\Patient $patient, $use_context = false)
    {
        $element = $this->getElementFromLatestVisibleEvent(
            'models\HistoryMedications',
            $patient,
            $use_context
        );

        $widget = $this->getWidget(
            'OEModule\OphCiExamination\widgets\HistoryMedications',
            array('mode' => HistoryMedications::$DATA_MODE, 'patient' => $patient)
        );

        if (!$element) {
            $element = $widget->element;
        }

        $entries = $widget->getMergedEntries();

        $route_filter = function ($entry) {
            // route should be different than eye
            /** @var \EventMedicationUse $entry */
            return  !$entry->route_id || !$entry->route->isEyeRoute();
        };
        $current_systemic_meds = array_filter($entries['current'], $route_filter);
        $current_systemic_meds = $element->mergeMedicationEntries($current_systemic_meds, $widget);

        if (!$current_systemic_meds) {
            return "
                <table class='standard'>
                    <tbody>
                        <tr>
                            <td>(no current systemic medications)</td>
                        </tr>
                    </tbody>
                </table>
                    ";
        }

        /** @var \EventMedicationUse[] $current_systemic_meds */

        ob_start();
        ?>
        <table class="standard borders current-ophtalmic-drugs">
            <colgroup>
                <col class="cols-5">
            </colgroup>
            <thead>
            <tr>
                <th class="empty"></th>
                <th>Dose (unit)</th>
                <th>Frequency</th>
                <th>Until</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($current_systemic_meds as $entry) { ?>
                <?php
                $tapers = [];
                $stop_display_date = $entry->end_date ? \Helper::convertDate2NHS($entry->end_date): 'Ongoing';
                $comments = $entry->comments ?: null;
                if ($entry->prescription_item_id) {
                    $tapers = $entry->prescriptionItem->tapers;
                    $stop_date = $entry->prescriptionItem->stopDateFromDuration(false);
                    $stop_display_date = $stop_date ? \Helper::convertDate2NHS($stop_date->format('Y-m-d')) : $entry->prescriptionItem->medicationDuration->name;
                    $comments = $entry->prescriptionItem->comments ?: ($entry->comments ?: null);
                } ?>
                <tr>
                    <td><?= $entry->getMedicationDisplay(true) ?>
                    <?= $comments ? ('<br /><br /><i>Comment: </i>' . $comments) : '' ?></td>
                    <td><?= $entry->dose . ($entry->dose_unit_term ? (' (' . $entry->dose_unit_term . ')') : '') ?></td>
                    <td><?= $entry->frequency ? $entry->frequency : ''; ?></td>
                    <td><?= $stop_display_date ?></td>
                </tr>
                <?php if ($tapers) {
                    $taper_date = $stop_date;
                    foreach ($tapers as $taper) {
                        $taper_display_date = $taper->stopDateFromDuration($taper_date); ?>
                    <tr>
                        <td>
                            <div class="oe-i child-arrow small no-click"></div>
                            <i> then</i>
                        </td>
                        <td><?=$taper->dose . ($entry->dose_unit_term ? (' (' . $entry->dose_unit_term . ')') : '')?></td>
                        <td><?= $taper->frequency ? $taper->frequency->term : '' ?></td>
                        <td><?= $taper_display_date ? \Helper::convertDate2NHS($taper_display_date->format('Y-m-d')) : $taper->duration->name;?></td>
                    </tr>
                        <?php $taper_date = $taper_display_date ?? $taper_date; ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>

        <?php return ob_get_clean();
    }

    /**
     * Gets current (not stopped) medications
     *
     * Shortcode: mec
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getCurrentMedications(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElement('models\MedicationManagement', $patient);
        $entries = $element ? $element->getNotStoppedEntries() : [];

        $text = '';
        foreach ($entries as $entry) {
            $text .= $entry->medication->preferred_term . ", started at " . \Helper::convertMySQL2NHS($entry->start_date) . "<br>";
        }

        return $text;
    }

    /**
     * Gets all previous (stopped) medications
     * Shortcode: mep
     *
     * @param Patient $patient
     * @param false $use_context
     * @return string
     */
    public function getPreviousMedications(\Patient $patient, $use_context = false): string
    {
        $element = $this->getLatestElement('models\MedicationManagement', $patient);
        $entries = $element ? $element->getStoppedEntries() : [];

        $text = '';
        foreach ($entries as $entry) {
            $text .= $entry->medication->preferred_term .
                ", started at " . \Helper::convertMySQL2NHS($entry->start_date) .
                " and ended at " .  \Helper::convertMySQL2NHS($entry->end_date) ."<br>";
        }

        return $text;
    }
}
