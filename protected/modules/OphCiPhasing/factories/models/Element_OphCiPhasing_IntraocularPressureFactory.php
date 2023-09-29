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
use OE\factories\models\EventFactory;

use OEModule\OphCiPhasing\models\{
    Element_OphCiPhasing_IntraocularPressure,
    OphCiPhasing_Reading
};

class Element_OphCiPhasing_IntraocularPressureFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphCiPhasing'),
        ];
    }

    /**
     * @param OphCiPhasing_Instrument|OphCiPhasing_InstrumentFactory|string|int $instrument
     * @return Element_OphCiPhasing_IntraocularPressureFactory
     */
    public function forLeftInstrument($instrument): self
    {
        return $this->state([
            'left_instrument_id' => $instrument
        ]);
    }

    /**
     * @param OphCiPhasing_Instrument|OphCiPhasing_InstrumentFactory|string|int $instrument
     * @return Element_OphCiPhasing_IntraocularPressureFactory
     */
    public function forRightInstrument($instrument): self
    {
        return $this->state([
            'right_instrument_id' => $instrument
        ]);
    }

    public function withReadingsOnBothSides($count = 1): self
    {
        return $this->afterCreating(static function (Element_OphCiPhasing_IntraocularPressure $element) use ($count) {
            for ($index = 0; $index < $count; $index++) {
                OphCiPhasing_Reading::factory()
                    ->forElement($element)
                    ->leftSide()
                    ->create();

                OphCiPhasing_Reading::factory()
                    ->forElement($element)
                    ->rightSide()
                    ->create();
            }
        });
    }
}
