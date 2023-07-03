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

        list(
            $existing_element,
            $lhs_reading, $rhs_reading,
            $chosen_unit, $alternative_unit
        ) = $this->createSimpleElement($existing_event, $this->context->additional_data['type'] === 'near-visual-acuity');

        $lhs_combined = $existing_element->getCombined('left');
        $rhs_combined = $existing_element->getCombined('right');

        return [
            'previousEvent' => SeededEventResource::from($existing_event)->toArray(),
            'leftEyeCombined' => $lhs_combined,
            'rightEyeCombined' => $rhs_combined,
            'chosenUnitId' => $chosen_unit->id,
            'alternativeUnitName' => $alternative_unit->name
        ];
    }

    protected function createSimpleElement($existing_event, $is_near)
    {
        $element_factory = $is_near ?
                         Element_OphCiExamination_NearVisualAcuity::factory() :
                         Element_OphCiExamination_VisualAcuity::factory();

        $reading_factory = $is_near ?
                     OphCiExamination_NearVisualAcuity_Reading::factory() :
                     OphCiExamination_VisualAcuity_Reading::factory();

        $existing_element = $element_factory
                             ->forEvent($existing_event)
                             ->bothEyes()
                             ->create();

        list($chosen_unit, $alternative_unit) = $this->selectUnits($is_near, false);

        $lhs_reading = $reading_factory
                     ->forElement($existing_element)
                     ->forSide(OphCiExamination_VisualAcuity_Reading::LEFT)
                     ->forUnit($chosen_unit)
                     ->create();

        $rhs_reading = $reading_factory
                     ->forElement($existing_element)
                     ->forSide(OphCiExamination_VisualAcuity_Reading::RIGHT)
                     ->forUnit($chosen_unit)
                     ->create();

        $existing_element->refresh();

        return [$existing_element, $lhs_reading, $rhs_reading, $chosen_unit, $alternative_unit];
    }

    protected function selectUnits($is_near, $is_complex)
    {
        $conditions = '';

        if ($is_near) {
            $conditions = 'is_near <> 0';
        } else {
            $conditions = 'is_va <> 0';
        }

        if (!$is_complex) {
            $conditions = $conditions . ' AND complex_only = 0';
        }

        $units = OphCiExamination_VisualAcuityUnit::model()->active()->findAll(
            ['condition' => $conditions, 'order' => 'RAND()']
        );

        return array_slice($units, 0, 2);
    }
}
