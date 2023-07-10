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

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;

use OEModule\OphCiExamination\models\{
    Element_OphCiExamination_NearVisualAcuity,
    OphCiExamination_NearVisualAcuity_Reading,
    OphCiExamination_VisualAcuity_Method,
    OphCiExamination_VisualAcuityUnit,
    OphCiExamination_VisualAcuityUnitValue
};

class OphCiExamination_NearVisualAcuity_ReadingFactory extends ModelFactory
{
    public function definition(): array
    {
        $side = $this->faker->randomElement([
            OphCiExamination_NearVisualAcuity_Reading::LEFT,
            OphCiExamination_NearVisualAcuity_Reading::RIGHT,
            OphCiExamination_NearVisualAcuity_Reading::BEO
        ]);

        $includes_complex = $side === OphCiExamination_NearVisualAcuity_Reading::BEO;

        $element_factory = $includes_complex
                         ? Element_OphCiExamination_NearVisualAcuity::factory()->complex()
                         : Element_OphCiExamination_NearVisualAcuity::factory()->simple();

        return [
            'element_id' => $element_factory,
            'side' => $side,
            'method_id' => OphCiExamination_VisualAcuity_Method::factory()->useExisting(['active' => true]),
            'unit_id' => OphCiExamination_VisualAcuityUnit::factory()->useExisting(['is_near' => true]),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(static function (OphCiExamination_VisualAcuity_Reading $reading) {
            $reading->value = $reading->value ?? OphCiExamination_VisualAcuityUnitValue::factory()->useExisting(['unit_id' => $reading->unit_id])->create()->base_value;
        });
    }

    /**
     * @param Element_OphCiExamination_NearVisualAcuity|Element_OphCiExamination_NearVisualAcuityFactory|int|string $element
     * @return OphCiExamination_NearVisualAcuity_ReadingFactory
     */
    public function forElement($element): self
    {
        return $this->state([
            'element_id' => $element
        ]);
    }

    /**
     * @param int $side
     * @return OphCiExamination_NearVisualAcuity_ReadingFactory
     */
    public function forSide($side): self
    {
        return $this->state([
            'side' => $side
        ]);
    }

    /**
     * @param OphCiExamination_VisualAcuityUnit $unit
     * @return OphCiExamination_NearVisualAcuity_ReadingFactory
     */
    public function forUnit(OphCiExamination_VisualAcuityUnit $unit): self
    {
        $value = $this->faker->randomElement($unit->selectableValues);

        return $this->state([
            'unit_id' => $unit,
            'value' => $value->base_value
        ]);
    }
}
