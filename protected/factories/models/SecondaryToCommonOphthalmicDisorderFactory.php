<?php

/**
 * (C) Copyright Apperta Foundation 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use CommonOphthalmicDisorder;
use Disorder;
use Finding;
use OE\factories\ModelFactory;

class SecondaryToCommonOphthalmicDisorderFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'disorder_id' => Disorder::factory(),
            'parent_id' => CommonOphthalmicDisorder::factory()->useExisting(),
            'letter_macro_text' => $this->faker->words(10, true),
        ];
    }

    public function withFinding(): self {
        return $this->state([
            'finding_id' => Finding::factory()->useExisting()
        ]);
    }
}
