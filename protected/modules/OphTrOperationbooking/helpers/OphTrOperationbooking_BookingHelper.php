<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrOperationbooking_BookingHelper
{
    const ANAESTHETIST_REQUIRED = 'ANAESTHETIST_REQUIRED';
    const CONSULTANT_REQUIRED = 'CONSULTANT_REQUIRED';
    const PAEDIATRIC_SESSION_REQUIRED = 'PAEDIATRIC_SESSION_REQUIRED';
    const GENERAL_ANAESTHETIC_REQUIRED = 'GENERAL_ANAESTHETIC_REQUIRED';

    /**
     * @param OphTrOperationbooking_Operation_Session $sess
     * @param Element_OphTrOperationbooking_Operation $op
     *
     * @return array Array of error codes, empty if no errors
     */
    public function checkSessionCompatibleWithOperation(OphTrOperationbooking_Operation_Session $session, Element_OphTrOperationbooking_Operation $op)
    {
        $errors = array();

        if ($op->anaesthetist_required && !$session->anaesthetist) {
            $errors[] = self::ANAESTHETIST_REQUIRED;
        }

        if ($op->consultant_required && !$session->consultant) {
            $errors[] = self::CONSULTANT_REQUIRED;
        }

        if ($op->event->episode->patient->isChild($session->date) && !$session->paediatric) {
            $errors[] = self::PAEDIATRIC_SESSION_REQUIRED;
        }

        if ($op->hasAnaestheticTypeByCode('GA') && !$session->general_anaesthetic) {
            $errors[] = self::GENERAL_ANAESTHETIC_REQUIRED;
        }

        return $errors;
    }

    /**
     * Perform event level validation based on the elements passed in.
     *
     * @param $elements
     *
     * @return array
     */
    public static function validateElementsForEvent($elements)
    {
        $errors = array();
        foreach ($elements as $element) {
            $cls = get_class($element);
            if ($cls == 'Element_OphTrOperationbooking_Operation') {
                $op_el = $element;
            } elseif ($cls == 'Element_OphTrOperationbooking_Diagnosis') {
                $diag_el = $element;
            }
        }

        if ($diag_el->eye_id != Eye::BOTH && $op_el->eye_id != $diag_el->eye_id) {
            $errors[] = 'Operation eye must match diagnosis eye!';
        }

        return $errors;
    }
}
