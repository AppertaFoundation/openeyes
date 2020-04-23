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
class OphInBiometry_API extends BaseAPI
{
    /**
     * Last Biometry target refraction
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */

    public function getLastBiometryTargetRefraction(\Patient $patient, $use_context = false)
    {
        $biometry_left = 'LEFT: Not Recorded ';
        $biometry_right = 'RIGHT: Not Recorded';
        $event = $this->getLatestEvent($patient, $use_context);

        if ($event) {
            $biometry_element = $this->getLatestElement('Element_OphInBiometry_Calculation', $patient, $use_context);

            if ($biometry_element) {
                if ($biometry_element->hasLeft() && !empty($biometry_element->target_refraction_left)) {
                    $biometry_left = 'LEFT: ' . $biometry_element->target_refraction_left;
                }
                if ($biometry_element->hasRight() && !empty($biometry_element->target_refraction_right)) {
                    $biometry_right = 'RIGHT: ' . $biometry_element->target_refraction_right;
                }
            }
        }

        return $biometry_left . ' ' . $biometry_right;
    }
}
