<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\factories\models;

use OE\factories\ModelFactory;
use OEModule\PatientTicketing\models\QueueSet;
use OEModule\PatientTicketing\models\QueueSetCategory;
use OEModule\PatientTicketing\models\QueueSetCategory_Institution;

class QueueSetCategoryFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            "name" => $this->faker->words(3, true),
        ];
    }

    public function withInstitution($institution): self
    {
        return $this->afterCreating(function (QueueSetCategory $queueset_category) use ($institution) {
            ModelFactory::factoryFor(QueueSetCategory_Institution::class)
                ->forCategory($queueset_category)
                ->forInstitution($institution)
                ->create();
        });
    }

    /**
     * @param $institution
     * @return $this
     */
    public function withQueueSet($institution = null): self
    {
        if ($institution) {
            return $this->afterCreating(function (QueueSetCategory $queueset_category) use ($institution) {
                ModelFactory::factoryFor(QueueSet::class)
                    ->forCategory($queueset_category)
                    ->withInstitution($institution)
                    ->create();
            });
        }

        return $this->afterCreating(function (QueueSetCategory $queueset_category) use ($institution) {
            ModelFactory::factoryFor(QueueSet::class)
                ->forCategory($queueset_category)
                ->create();
        });
    }
}
