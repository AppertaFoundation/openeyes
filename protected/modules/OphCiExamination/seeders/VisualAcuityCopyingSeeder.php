<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\seeders;

use OEModule\CypressHelper\resources\SeededEventResource;

use OE\factories\models\EventFactory;

use Patient;

use OEModule\OphCiExamination\models\{
    Element_OphCiExamination_VisualAcuity,
    Element_OphCiExamination_NearVisualAcuity,
    OphCiExamination_VisualAcuity_Reading,
    OphCiExamination_NearVisualAcuity_Reading,
    OphCiExamination_VisualAcuityUnit
};

class VisualAcuityCopyingSeeder
{
    public function __construct(\DataContext $context)
    {
        $this->context = $context;
    }

    public function __invoke()
    {
        $patient = Patient::factory()->create();

        $existing_event = EventFactory::forModule('OphCiExamination')
                   ->forPatient($patient)
                   ->create();

        if ($this->context->additional_data['type'] === 'visual-acuity') {
            $existing_va_element = Element_OphCiExamination_VisualAcuity::factory()
                                 ->forEvent($existing_event)
                                 ->bothEyes()
                                 ->create();

            // TODO Choose a random unit when the bug with ElementForm, where it always maps
            // the previous element's reading values through the default unit, has been resolved.
            // In the the meantime, getUnit will return the default unit, so the units will
            // match when the mapping takes place in form_Element_OphCiExamination_VisualAcuity_Reading
            $chosen_unit = Element_OphCiExamination_VisualAcuity::model()->getUnit();

            $lhs_reading = OphCiExamination_VisualAcuity_Reading::factory()
                         ->forElement($existing_va_element)
                         ->forSide(OphCiExamination_VisualAcuity_Reading::LEFT)
                         ->forUnit($chosen_unit)
                         ->create();

            $rhs_reading = OphCiExamination_VisualAcuity_Reading::factory()
                         ->forElement($existing_va_element)
                         ->forSide(OphCiExamination_VisualAcuity_Reading::RIGHT)
                         ->forUnit($chosen_unit)
                         ->create();

            $existing_va_element->refresh();

            $lhs_combined = $existing_va_element->getCombined('left');
            $rhs_combined = $existing_va_element->getCombined('right');
        } else if ($this->context->additional_data['type'] === 'near-visual-acuity') {
            $existing_nva_element = Element_OphCiExamination_NearVisualAcuity::factory()
                                 ->forEvent($existing_event)
                                 ->bothEyes()
                                 ->create();

            // TODO Near Visual Acuity also has issues with the default unit so as above
            // the default unit is used to test the copying functionality currently.
            $chosen_unit = Element_OphCiExamination_NearVisualAcuity::model()->getUnit();

            $lhs_reading = OphCiExamination_NearVisualAcuity_Reading::factory()
                         ->forElement($existing_nva_element)
                         ->forSide(OphCiExamination_VisualAcuity_Reading::LEFT)
                         ->forUnit($chosen_unit)
                         ->create();

            $rhs_reading = OphCiExamination_NearVisualAcuity_Reading::factory()
                         ->forElement($existing_nva_element)
                         ->forSide(OphCiExamination_VisualAcuity_Reading::RIGHT)
                         ->forUnit($chosen_unit)
                         ->create();

            $existing_nva_element->refresh();

            $lhs_combined = $existing_nva_element->getCombined('left');
            $rhs_combined = $existing_nva_element->getCombined('right');
        } else {
            throw new \Exception('Invalid "type" argument to VisualAcuityCopyingSeeder');
        }

        return [
            'previousEvent' => SeededEventResource::from($existing_event)->toArray(),
            'leftEyeCombined' => $lhs_combined,
            'rightEyeCombined' => $rhs_combined
        ];
    }
}
