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

namespace OEModule\OphCiPhasing\factories\models;

use OE\factories\ModelFactory;

use OEModule\OphCiPhasing\models\{
    Element_OphCiPhasing_IntraocularPressure,
    OphCiPhasing_Reading
};

class OphCiPhasing_ReadingFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'element_id' => Element_OphCiPhasing_IntraocularPressure::factory(),
            'side' => $this->faker->randomElement([OphCiPhasing_Reading::RIGHT, OphCiPhasing_Reading::LEFT]),
            'value' => $this->faker->numberBetween(1, 100),
            'measurement_timestamp' => $this->faker->time()
        ];
    }

    /**
     * @param Element_OphCiPhasing_IntraocularPressure|Element_OphCiPhasing_IntraocularPressureFactory|string|int $element
     * @return OphCiPhasing_ReadingFactory
     */
    public function forElement($element): self
    {
        return $this->state([
            'element_id' => $element
        ]);
    }

    /**
     * @return OphCiPhasing_ReadingFactory
     */
    public function leftSide(): self
    {
        return $this->state([
            'side' => OphCiPhasing_Reading::LEFT
        ]);
    }

    /**
     * @return OphCiPhasing_ReadingFactory
     */
    public function rightSide(): self
    {
        return $this->state([
            'side' => OphCiPhasing_Reading::RIGHT
        ]);
    }
}
