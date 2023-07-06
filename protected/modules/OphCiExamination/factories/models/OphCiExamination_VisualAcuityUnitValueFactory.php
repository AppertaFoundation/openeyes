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

use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;

class OphCiExamination_VisualAcuityUnitValueFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'unit_id' => OphCiExamination_VisualAcuityUnit::factory(),
            'value' => $this->faker->word(),
            'base_value' => $this->faker->numberBetween(1, 120),
            'selectable' => true,
        ];
    }

    /**
     * @param OphCiExamination_VisualAcuityUnit|OphCiExamination_VisualAcuityUnitFactory|string|int $unit
     * @return OphCiExamination_VisualAcuityUnitValueFactory
     */
    public function forUnit($unit): self
    {
        return $this->state([
            'unit_id' => $unit
        ]);
    }

    /**
     * @return OphCiExamination_VisualAcuityUnitValueFactory
     */
    public function selectable(): self
    {
        return $this->state([
            'selectable' => true
        ]);
    }

    /**
     * @return OphCiExamination_VisualAcuityUnitValueFactory
     */
    public function unselectable(): self
    {
        return $this->state([
            'selectable' => false
        ]);
    }
}
