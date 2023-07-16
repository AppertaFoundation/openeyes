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
    OphCiExamination_VisualAcuityUnit,
    OphCiExamination_VisualAcuityUnitValue
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

        if (!isset($this->context->additional_data['complex'])) {
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
        } else {
            list(
                $existing_element,
                $lhs_reading, $rhs_reading, $beo_reading,
                $lhs_details, $rhs_details, $beo_details
            ) = $this->createComplexElement($existing_event, $this->context->additional_data['type'] === 'near-visual-acuity');

            return [
                'previousEvent' => SeededEventResource::from($existing_event)->toArray(),
                'lhsDetails' => $lhs_details,
                'rhsDetails' => $rhs_details,
                'beoDetails' => $beo_details,
            ];
        }
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

        list($chosen_unit, $alternative_unit) = $this->createUnits();

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

    protected function createComplexElement($existing_event, $is_near)
    {
        $element_factory = $is_near ?
                         Element_OphCiExamination_NearVisualAcuity::factory() :
                         Element_OphCiExamination_VisualAcuity::factory();

        $reading_factory = $is_near ?
                     OphCiExamination_NearVisualAcuity_Reading::factory() :
                     OphCiExamination_VisualAcuity_Reading::factory();

        $existing_element = $element_factory
                             ->forEvent($existing_event)
                             ->complex()
                             ->bothEyesAndBEO()
                             ->create();

        list($lhs_unit, $rhs_unit, $beo_unit) = $is_near
                                              ? OphCiExamination_VisualAcuityUnit::factory()->useExisting(['active' => true, 'is_near' => true])->count(3)->create()
                                              : OphCiExamination_VisualAcuityUnit::factory()->useExisting(['active' => true, 'is_va' => true])->count(3)->create();

        $lhs_reading = $reading_factory
                     ->forElement($existing_element)
                     ->forSide(OphCiExamination_VisualAcuity_Reading::LEFT)
                     ->forUnit($lhs_unit)
                     ->create();

        $rhs_reading = $reading_factory
                     ->forElement($existing_element)
                     ->forSide(OphCiExamination_VisualAcuity_Reading::RIGHT)
                     ->forUnit($rhs_unit)
                     ->create();

        $beo_reading = $reading_factory
                     ->forElement($existing_element)
                     ->forSide(OphCiExamination_VisualAcuity_Reading::BEO)
                     ->forUnit($beo_unit)
                     ->create();

        return [
            $existing_element, $lhs_reading, $rhs_reading, $beo_reading,
            $this->complexReadingDetails($lhs_reading),
            $this->complexReadingDetails($rhs_reading),
            $this->complexReadingDetails($beo_reading)
        ];
    }

    protected function complexReadingDetails($reading)
    {
        return [
            'method' => $reading->method->name,
            'unit' => $reading->unit->name,
            'value' => $reading->display_value
        ];
    }

    protected function createUnits()
    {
        $chosen_unit = OphCiExamination_VisualAcuityUnit::factory()->withUniquePostfix((string) microtime())->withDBUniqueAttribute('name')->forVA()->forNear()->notComplexOnly()->create();
        $alternative_unit = OphCiExamination_VisualAcuityUnit::factory()->withUniquePostfix((string) microtime())->withDBUniqueAttribute('name')->forVA()->forNear()->notComplexOnly()->create();

        // The alternative unit values are unselectable in order to test what happens when the readings are changed from the chosen
        // to the alternative unit in the unit selector on the form. Being unselectable should only affect the adders for readings.
        // At the creation of this seeder the existing behaviour was for readings to disappear if the target value was not selectable,
        // which is not correct.
        $chosen_unit_value = OphCiExamination_VisualAcuityUnitValue::factory()
                           ->withUniquePostfix((string) microtime())
                           ->withDBUniqueAttribute('value')
                           ->forUnit($chosen_unit)
                           ->selectable()
                           ->count(4)
                           ->create();

        $alternative_unit_value = OphCiExamination_VisualAcuityUnitValue::factory()
                                ->withUniquePostfix((string) microtime())
                                ->withDBUniqueAttribute('value')
                                ->forUnit($alternative_unit)
                                ->unselectable()
                                ->count(4)
                                ->create();

        return [$chosen_unit, $alternative_unit];
    }
}
