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

class OphCiExamination_VisualAcuityUnitFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(2),
            'is_va' => $this->faker->boolean(),
            'is_near' => $this->faker->boolean(),
            'complex_only' => $this->faker->boolean(),
            'active' => true,
        ];
    }

    /**
     * @return OphCiExamination_VisualAcuityUnitFactory
     */
    public function forVA(): self
    {
        return $this->state([
            'is_va' => true
        ]);
    }

    /**
     * @return OphCiExamination_VisualAcuityUnitFactory
     */
    public function notForVA(): self
    {
        return $this->state([
            'is_va' => false
        ]);
    }

    /**
     * @return OphCiExamination_VisualAcuityUnitFactory
     */
    public function forNear(): self
    {
        return $this->state([
            'is_near' => true
        ]);
    }

    /**
     * @return OphCiExamination_VisualAcuityUnitFactory
     */
    public function notForNear(): self
    {
        return $this->state([
            'is_near' => false
        ]);
    }

    /**
     * @return OphCiExamination_VisualAcuityUnitFactory
     */
    public function complexOnly(): self
    {
        return $this->state([
            'complex_only' => true
        ]);
    }

    /**
     * @return OphCiExamination_VisualAcuityUnitFactory
     */
    public function notComplexOnly(): self
    {
        return $this->state([
            'complex_only' => false
        ]);
    }
}
