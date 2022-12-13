<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use OE\factories\ModelFactory;
use Procedure;
use ProcedureSubspecialtyAssignment;

class ProcedureFactory extends ModelFactory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'term' => $this->faker->words(3, true),
            'short_format' => $this->faker->lexify("???"),
            'default_duration' => $this->faker->randomElement([10, 20, 30, 45, 60, 90]),
            'snomed_code' => $this->faker->numerify('#########')
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Procedure $procedure) {
            if (!isset($procedure->snomed_term)) {
                $procedure->snomed_term = $procedure->term;
            }
        });
    }

    public function forSubspecialtyIds(array $subspecialty_ids, $institution_id)
    {
        return $this->afterCreating(function (Procedure $procedure) use ($subspecialty_ids, $institution_id) {
            // the relation is currently misnamed in the procedure model
            foreach ($subspecialty_ids as $subspecialty_id) {
                ProcedureSubspecialtyAssignment::factory()->create([
                    'proc_id' => $procedure->id,
                    'subspecialty_id' => $subspecialty_id,
                    'institution_id' => $institution_id
                ]);
            }
        });
    }
}
