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
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\factories\models;

use OE\factories\ModelFactory;
use Institution;
use Subspecialty;
use OE\factories\models\{InstitutionFactory, SubspecialtyFactory};
use OEModule\OphCiExamination\models\AdviceLeafletCategorySubspecialty;

class AdviceLeafletCategoryFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(10, true),
            'institution_id' => Institution::factory()->create(),
            'active' => $this->faker->boolean()
        ];
    }

    public function active()
    {
        return $this->state([
            'active' => true
        ]);
    }

    public function inactive()
    {
        return $this->state([
            'active' => false
        ]);
    }

    public function forInstitution(Institution|InstitutionFactory|string|int $institution): self
    {
        return $this->state([
            'institution_id' => $institution
        ]);
    }

    public function forSubspecialty(Subspecialty|SubspecialtyFactory|null $subspecialty = null): self
    {
        return $this->afterCreating(function ($category) use ($subspecialty) {
            if ($subspecialty === null) {
                $subspecialty  = Subspecialty::factory()->useExisting()->create();
            }
            AdviceLeafletCategorySubspecialty::factory()->create([
                'category_id' => $category,
                'subspecialty_id' => $subspecialty
            ]);
        });
    }
}
