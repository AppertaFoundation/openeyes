<?php
/**
 * (C) Apperta Foundation, 2020
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

namespace OEModule\OphCiExamination\components\traits;

use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;

trait VisualAcuity_API
{
    use APICache;

    /**
     * Returns the best visual acuity for the specified side for the patient.
     * This is from the most recent examination that has a visual acuity element.
     * And will be empty if the specified side was not recorded.
     *
     * @param Patient $patient
     * @param string $side
     * @param boolean $use_context
     * @return OphCiExamination_VisualAcuity_Reading|null
     */
    public function getBestVisualAcuity(\Patient $patient, $side, $use_context = false)
    {
        return $this->getCachedData(
            "getBestVisualAcuity-{$patient->id}-{$side}-" . ($use_context ? "1" : "0"),
            function () use ($patient, $side, $use_context) {
                $va = $this->getCachedLatestElement(
                    Element_OphCiExamination_VisualAcuity::class,
                    $patient,
                    $use_context
                );
                if ($va) {
                    return $va->getBestReading($side);
                }
            }
        );
    }

    /**
     * Returns the best reading for each available side from the most recently recorded VA.
     * [
     *  has_beo => bool,
     *  has_right => bool,
     *  has_left => bool,
     *  event_date => datetime
     * ];
     *
     * For each side present, following is included:
     *
     * [
     *  side_result => string,
     *  side_method => string,
     *  side_method_abbr => string,
     * ]
     *
     * @param \Patient $patient
     * @return mixed
     */
    public function getMostRecentVADataStandardised(\Patient $patient)
    {
        return $this->getCachedData(
            "getMostRecentVADataStandardised-{$patient->id}",
            function () use ($patient) {
                $element = $this->getCachedLatestElement(Element_OphCiExamination_VisualAcuity::class, $patient);
                return $element
                    ? $this->formatVAElementToBestForExternalUse($element)
                    : null;
            }
        );
    }

    public function getMostRecentVAData(\Patient $patient)
    {
        $element = $this->getCachedLatestElement(Element_OphCiExamination_VisualAcuity::class, $patient);
        if (!$element) {
            return null;
        }

        return $this->formatVAElementForExternalUse($element);
    }

    public function getMostRecentNearVAData(\Patient $patient)
    {
        $element = $this->getLatestElement(Element_OphCiExamination_NearVisualAcuity::class, $patient);
        if (!$element) {
            return null;
        }

        return $this->formatVAElementForExternalUse($element);
    }

    /**
     * Get the default findings string from VA in the latest examination event (if it exists).
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getLetterVisualAcuityFindings($patient, $use_context = false)
    {
        $va = $this->getLatestElement(
            Element_OphCiExamination_VisualAcuity::class,
            $patient,
            $use_context
        );

        if ($va) {
            return $va->getLetter_string();
        }
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
        return ($best = $this->getBestVisualAcuity($patient, 'left', $use_context)) ? $best->convertTo($best->value) : "Not Recorded";
    }

    /**
     * Returns single (best) VA reading from most recent examination event
     * containing a VA element for the right eye.
     *
     * @param $patient
     * @param bool $use_context
     * @return null|string
     */
    public function getLetterVisualAcuityRight($patient, $use_context = false)
    {
        return ($best = $this->getBestVisualAcuity($patient, 'right', $use_context)) ? $best->convertTo($best->value) : "Not Recorded";
    }

    /**
     * Returns the best visual acuity reading from the most recent examination
     * event containing a VA element for the right and left eyes
     *
     * @param $patient
     * @param bool $use_context
     * @return string
     */
    public function getLetterVisualAcuityBoth($patient, $use_context = false)
    {
        $left = $this->getBestVisualAcuity($patient, 'left', $use_context);
        $right = $this->getBestVisualAcuity($patient, 'right', $use_context);

        return sprintf(
            "%s on the right and %s on the left",
            $right ? $right->display_value : "not recorded",
            $left ? $left->display_value : "not recorded"
        );
    }


    /**
     * Get the default findings string from Near VA in the latest examination event (if it exists).
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getLetterNearVisualAcuityFindings($patient, $use_context = false)
    {
        $va = $this->getLatestElement(
            Element_OphCiExamination_NearVisualAcuity::class,
            $patient,
            $use_context
        );

        if ($va) {
            return $va->getLetter_string();
        }
    }

    /**
     * Get the latest VA for both eyes from examination event, if the VA is not recorded,
     * take the value from the latest available event within a period of 6 weeks.
     *
     * @param $patient
     * @param bool $use_context
     * @return string - 6/24 (at 7 Jun 2017)
     */
    public function getLetterVisualAcuityBothLast6weeks($patient, $use_context = false)
    {
        $left = null;
        $right = null;

        $right = $this->getLetterVisualAcuityRightLast6weeks($patient, $use_context) ?: 'not recorded';
        $left = $this->getLetterVisualAcuityLeftLast6weeks($patient, $use_context) ?: 'not recorded';
        return $right . ' on the right and ' . $left . ' on the left';
    }

    /**
     * Get the best VA for the right eye from the most recent examination event that has recorded right eye
     * VA in the past six weeks.
     *
     * @param $patient
     * @param bool $use_context
     * @return string - [VA] (recorded on j m Y)
     */
    public function getLetterVisualAcuityRightLast6weeks($patient, $use_context = false)
    {
        foreach ($this->getVisualAcuityLast6Weeks($patient, $use_context) as $element) {
            $best_reading = $element->getBestReading('right');
            if ($best_reading) {
                return $best_reading->convertTo($best_reading->value) . " (recorded on " . \Helper::convertMySQL2NHS($element->event->event_date) . ")";
            }
        }
    }

    /**
     * Get the best VA for the left eye from the most recent examination event that has recorded left eye
     * VA in the past six weeks.
     *
     * @param $patient
     * @param bool $use_context
     * @return string - [VA] (recorded on j m Y)
     */
    public function getLetterVisualAcuityLeftLast6weeks($patient, $use_context = false)
    {
        foreach ($this->getVisualAcuityLast6Weeks($patient, $use_context) as $element) {
            $best_reading = $element->getBestReading('left');
            if ($best_reading) {
                return $best_reading->convertTo($best_reading->value) . " (recorded on " . \Helper::convertMySQL2NHS($element->event->event_date) . ")";
            }
        }
    }

    /**
     * @param \Event $event
     * @return string
     */
    public function getBestVisualAcuityFromEvent(\Event $event)
    {
        $va = Element_OphCiExamination_VisualAcuity::model()->findByAttributes(['event_id' => $event->id]);
        if ($va) {
            return $va->getBest('right') . ' Right Eye ' . $va->getBest('left') . ' Left Eye';
        }
    }

    /**
     * Get the VA string for both sides for the patient in Snellen
     *
     * @param \Patient $patient
     * @param bool $include_nr_values flag to indicate whether NR flag values should be used for the text
     *
     * @return string
     */
    public function getSnellenVisualAcuityForBoth(\Patient $patient, $include_nr_values = false)
    {
        $left = $this->getSnellenVisualAcuityForLeft($patient, $include_nr_values);
        $right = $this->getSnellenVisualAcuityForRight($patient, $include_nr_values);

        return ($right ? $right : 'not recorded') . ' on the right and ' . ($left ? $left : 'not recorded') . ' on the left';
    }

    /**
     * Get the VA for the left eye of the given patient in Snellen.
     *
     * @param \Patient $patient
     * @param bool $include_nr_values
     * @param string $before_date
     * @param bool $use_context
     * @return OphCiExamination_VisualAcuity_Reading
     */
    public function getSnellenVisualAcuityForLeft(
        $patient,
        $include_nr_values = false,
        $before_date = null,
        $use_context = false
    ) {
        return $this->getSnellenVisualAcuityForSide($patient, 'left', $include_nr_values, $before_date, $use_context);
    }

    /**
     * Get the va for the right side of the given patient in Snellen.
     *
     * @param \Patient $patient
     * @param bool $include_nr_values
     * @param string $before_date
     * @param bool $use_context
     * @return OphCiExamination_VisualAcuity_Reading
     */
    public function getSnellenVisualAcuityForRight(
        $patient,
        $include_nr_values = false,
        $before_date = null,
        $use_context = false
    ) {
        return $this->getSnellenVisualAcuityForSide($patient, 'right', $include_nr_values, $before_date, $use_context);
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
     * Get the latest VA for Principal eye form examination event,
     * if the VA is not recorded, take the value from the latest available event within a period of 6 weeks.
     * @param $patient
     * @param bool $use_context
     * @return string - [VA] (at j M Y)
     * @throws \CException
     */
    public function getLetterVisualAcuityPrincipalLast6weeks($patient, $use_context = false)
    {
        $principal_eye = $this->getPrincipalEye($patient, true);
        if ($principal_eye) {
            $method = 'getLetterVisualAcuity' . $principal_eye->name . 'Last6weeks';
            return $this->{$method}($patient, $use_context);
        }
    }

    /**
     * get the list of possible unit values for Visual Acuity.
     *
     */
    public function getVAList()
    {
        $unit = $this->getSnellenUnit();

        return array_reduce(
            $unit ? $unit->selectableValues : [],
            function($result, $uv) {
                $result[$uv->base_value] = $uv->value;
                return $result;
            },
            []
        );
    }

    /**
     * Get the va for the given patient side in Snellen
     *
     * @param \Patient $patient
     * @param string $side
     * @param bool $include_nr_values
     * @param string $before_date
     * @param bool $use_context
     * @return OphCiExamination_VisualAcuity_Reading
     */
    protected function getSnellenVisualAcuityForSide(
        $patient,
        $side = 'left',
        $include_nr_values = false,
        $before_date = null,
        $use_context = false
    ) {
        $va = $this->getLatestElement(
            Element_OphCiExamination_VisualAcuity::class,
            $patient,
            $use_context,
            $before_date
        );

        if ($va && $va->hasEye($side)) {
            $best = $va->getBestReading($side);
            if ($best) {
                return $best->convertTo($best->value, $this->getSnellenUnitId());
            }
            if ($include_nr_values) {
                return $va->getNotRecordedTextForSide($side);
            }
        }
    }

    /**
     * Gets the id for the Snellen Metre unit type for VA.
     *
     * @TODO: switch to using a system wide setting for what unit is being used.
     * @return int|null
     */
    protected function getSnellenUnitId()
    {
        $unit = $this->getSnellenUnit();

        if ($unit) {
            return $unit->id;
        }
    }

    protected function getSnellenUnit()
    {
        return OphCiExamination_VisualAcuityUnit::model()->find('name = ?', array('Snellen Metre'));
    }

    /**
     * Abstraction for getting VA from last 6 weeks used for several letter string methods
     *
     * @param $patient
     * @param $use_context
     * @return Element_OphCiExamination_VisualAcuity[]
     */
    private function getVisualAcuityLast6Weeks($patient, $use_context)
    {
        $after = date('Y-m-d 00:00:00', strtotime('-6 weeks'));
        $criteria = new \CDbCriteria();
        $criteria->compare('event.event_date', '>=' . $after);

        return $this->getElements(
            Element_OphCiExamination_VisualAcuity::class,
            $patient,
            $use_context,
            null,
            $criteria
        );
    }

    private function formatVAElementForExternalUse($element)
    {
        return [
            'event_date' => $element->event->event_date,
            'right_unable_to_assess' => (string) $element->right_unable_to_assess === '1',
            'right_eye_missing' => (string) $element->right_eye_missing === '1',
            'right_comments' => $element->right_notes,
            'left_unable_to_assess' => (string) $element->left_unable_to_assess === '1',
            'left_eye_missing' => (string) $element->left_eye_missing === '1',
            'left_comments' => $element->left_notes,
            'beo_unable_to_assess' => (string) $element->beo_unable_to_assess === '1',
            'beo_comments' => $element->beo_notes,
            'right_readings' => $this->formatVAReadingsForExternalUse($element->right_readings),
            'left_readings' => $this->formatVAReadingsForExternalUse($element->left_readings),
            'beo_readings' => $this->formatVAReadingsForExternalUse($element->beo_readings),
        ];
    }

    private function formatVAReadingsForExternalUse(array $readings): array
    {
        return array_map(function ($reading) {
            return [
                'method_name' => (string) $reading->method,
                'value' => $reading->display_value,
                'unit' => (string) $reading->unit
            ];
        }, $readings);
    }

    private function formatVAElementToBestForExternalUse(Element_OphCiExamination_VisualAcuity $element, $unit_id = null)
    {
        return array_reduce(
            ['beo', 'right', 'left'],
            function ($result, $side) use ($element, $unit_id) {
                $reading = $element->getBestReading($side);
                if (!$reading) {
                    $result["has_{$side}"] = false;
                    return $result;
                }
                $result["has_{$side}"] = true;

                $result["{$side}_result"] = $unit_id !== null
                    ? $reading->convertTo($reading->value, $unit_id)
                    : $reading->display_value;
                $result["{$side}_method"] = $reading->method;
                $result["{$side}_method_abbr"] = [
                    "Unaided" => "ua",
                    "Pinhole" => "ph"
                ][(string) $reading->method] ?? "rx";

                return $result;
            },
            [
                'event_date' => $element->event->event_date
            ]
        );
    }
}
