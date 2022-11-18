<?php

/**
 * (C) Copyright Apperta Foundation 2021
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

use OE\factories\ModelFactory;
use OE\factories\models\traits\LooksUpExistingModels;
use Specialty;

class DisorderFactory extends ModelFactory
{
    use LooksUpExistingModels;

    protected ?int $currentMaxPrimaryKey = null;

    public function definition(): array
    {
        return [
            'fully_specified_name' => $this->faker->words(3, true),
            'term' => $this->faker->words(2, true)
        ];
    }

    public function forSystemic()
    {
        return $this->state(function () {
            return [
                'specialty_id' => null
            ];
        });
    }

    public function forOphthalmology()
    {
        return $this->state(function () {
            return [
                'specialty_id' => $this->mapToFactoryOrId(Specialty::class, 'Ophthalmology')
            ];
        });
    }

    public function existingForSystemic()
    {
        return $this->useExisting([
            'specialty_id' => null
        ]);
    }
    public function existingForOphthalmology()
    {
        return $this->useExisting([
            'specialty_id' => $this->mapToFactoryOrId(Specialty::class, 'Ophthalmology')
        ]);
    }

    public function withICD10()
    {
        return $this->state(function () {
            return [
                'icd10_code' => $this->faker->regexify('[A-Za-z0-9]{10}'),
                'icd10_term' => $this->faker->word()
            ];
        });
    }

    protected function persist($instances)
    {
        // Because the ID used for disorders is the SNOMED CT code
        // we have to define a value PK when persisting instances
        $instances = array_map(
            function ($instance) {
                if (!$instance->id) {
                    $instance->id = $this->getValidPrimaryKeyValue();
                }
                return $instance;
            },
            $instances
        );

        parent::persist($instances);
    }

    protected function getValidPrimaryKeyValue()
    {
        return $this->incrementCurrentMaxPrimaryKey();
    }

    protected function incrementCurrentMaxPrimaryKey()
    {
        if ($this->currentMaxPrimaryKey === null) {
            $this->currentMaxPrimaryKey = $this->app
                ->getComponent('db')
                ->createCommand()
                ->select('MAX(id)')
                ->from('disorder')
                ->queryScalar();
        }
        return ++$this->currentMaxPrimaryKey;
    }
}
