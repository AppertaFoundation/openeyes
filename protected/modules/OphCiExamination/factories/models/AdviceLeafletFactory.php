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

use Institution;
use OE\factories\ModelFactory;

use OEModule\OphCiExamination\models\{
    AdviceLeaflet,
    AdviceLeafletCategoryAssignment
};

class AdviceLeafletFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(10, true),
            'institution_id' => Institution::factory()->useExisting(),
            'active' => true
        ];
    }

    /**
     * @param Institution|InstitutionFactory|string|int $institution
     * @return AdviceLeafletFactory
     */
    public function forInstitution($institution): self
    {
        return $this->state([
            'institution_id' => $institution
        ]);
    }

    /**
     * @return AdviceLeafletFactory
     */
    public function active(): self
    {
        return $this->state([
            'active' => true
        ]);
    }

    /**
     * @return AdviceLeafletFactory
     */
    public function inactive(): self
    {
        return $this->state([
            'active' => false
        ]);
    }

    /**
     * @param AdviceLeafletCategory|array $categories
     * @return AdviceLeafletFactory
     */
    public function assignedToCategories($categories): self
    {
        $categories = is_array($categories) ? $categories : [$categories];

        return $this->afterCreating(static function (AdviceLeaflet $leaflet) use ($categories) {
            array_map(
                static function ($category) use ($leaflet) {
                    return AdviceLeafletCategoryAssignment::factory()
                        ->forCategory($category)
                        ->forLeaflet($leaflet)
                        ->create();
                },
                $categories
            );
        });
    }
}
