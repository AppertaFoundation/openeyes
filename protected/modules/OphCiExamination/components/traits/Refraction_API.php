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


use OEModule\OphCiExamination\models\CorrectionGiven;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading;
use OEModule\OphCiExamination\models\Retinoscopy;

trait Refraction_API
{
    use APICache;

    protected static $refraction_property_by_class = [
        CorrectionGiven::class => 'refraction',
        Element_OphCiExamination_Refraction::class => 'priority_reading',
        Retinoscopy::class => 'refraction'
    ];

    /**
     * Returns the latest refraction from the different elements
     * that can record it in examination. Where multiple elements may have recorded refraction,
     * precedence is defined as per the keys of static::$refraction_property_by_class
     *
     * @param \Patient $patient
     * @return array
     */
    public function getLatestRefractionReadingFromAnyElementType(\Patient $patient)
    {
        return $this->getCachedData(
            "getLatestRefractionReadingFromAnyElementType-{$patient->id}",
            function () use ($patient) {
                $refraction_element = $this->getMostRecentOfElementTypes(
                    array_keys(static::$refraction_property_by_class),
                    $patient
                );

                if (!$refraction_element) {
                    return null;
                }

                $refraction_attribute = static::$refraction_property_by_class[get_class($refraction_element)];
                return [
                    'right' => $refraction_element->hasRight() ? (string) $refraction_element->{"right_{$refraction_attribute}"} : null,
                    'left' => $refraction_element->hasLeft() ? (string) $refraction_element->{"left_{$refraction_attribute}"} : null,
                    'event_date' => $refraction_element->event->event_date
                ];
            }
        );
    }

    /**
     * @param \Event $event
     * @return string
     */
    public function getRefractionTextFromEvent(\Event $event)
    {
        $refract_element = Element_OphCiExamination_Refraction::model()->findByAttributes(array('event_id' => $event->id));
        if (!$refract_element) {
            return null;
        }

        $right_spherical = $refract_element->right_priority_reading
            ? $refract_element->right_priority_reading->getSphericalEquivalent()
            : 'Not Recorded';
        $left_spherical = $refract_element->left_priority_reading
            ? $refract_element->left_priority_reading->getSphericalEquivalent()
            : 'Not Recorded';

        return '<table class="VA-tbl">
                    <thead>
                    <tr>
                       <th class="VA-tbl-head">Right Eye</th>
                       <th class="VA-tbl-head">Left Eye</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td class="VA-tbl-td">' . $right_spherical . '</td>
                          <td class="VA-tbl-td">' . $left_spherical . '</td>
                        </tr>
                    </tbody>
                </table>';
    }

    /**
     * @param \Patient $patient
     * @return array|null
     */
    public function getMostRecentRefractionData(\Patient $patient)
    {
        $element = $this->getLatestElement(Element_OphCiExamination_Refraction::class, $patient);
        if (!$element) {
            return null;
        }

        return $this->formatRefractionElementForExternalUse($element);
    }

    /**
     * Given a list of element type classes, get the most recent for the patient
     * per the filtering criteria provided. For element types on the same event date, it will
     * return the earliest in the list of element types.
     *
     * @param array $element_types
     * @param \Patient $patient
     * @param bool $use_context
     * @param null $before
     * @return \BaseEventTypeElement|null
     */
    protected function getMostRecentOfElementTypes(array $element_types, \Patient $patient, $use_context = false, $before = null)
    {
        return array_reduce(
            $element_types,
            function ($most_recent, $element_type) use ($patient, $use_context, $before) {
                return $this->getLatestElement(
                    $element_type,
                    $patient,
                    $use_context,
                    $before,
                    $most_recent ? $most_recent->event->event_date : null
                ) ?? $most_recent;
            }
        );
    }

    /**
     * @param Element_OphCiExamination_Refraction $element
     * @return array
     */
    private function formatRefractionElementForExternalUse(Element_OphCiExamination_Refraction $element)
    {
        return [
            'event_date' => $element->event->event_date,
            'right_comments' => $element->right_notes,
            'left_comments' => $element->left_notes,
            'right_priority_reading' => $element->right_priority_reading
                ? $this->formatSingleRefractionReadingforExternalUse($element->right_priority_reading)
                : null,
            'right_readings' => $this->formatRefractionReadingsForExternalUse($element->right_readings),
            'left_priority_reading' => $element->left_priority_reading
                ? $this->formatSingleRefractionReadingforExternalUse($element->left_priority_reading)
                : null,
            'left_readings' => $this->formatRefractionReadingsForExternalUse($element->left_readings),
        ];
    }

    /**
     * @param OphCiExamination_Refraction_Reading[] $readings
     * @return array
     */
    private function formatRefractionReadingsForExternalUse(array $readings): array
    {
        return array_map(function ($reading) {
            return $this->formatSingleRefractionReadingforExternalUse($reading);
        }, $readings);
    }

    /**
     * @param OphCiExamination_Refraction_Reading $reading
     * @return array
     */
    private function formatSingleRefractionReadingforExternalUse(OphCiExamination_Refraction_Reading $reading): array
    {
        return [
            'type_name' => (string) $reading->type_display,
            'refraction' => $reading->refraction_display,
            'spherical_equivalent' => $reading->getSphericalEquivalent()
        ];
    }
}
