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

use Disorder;
use OE\factories\ModelFactory;
use OE\factories\models\traits\LooksUpExistingModels;
use Subspecialty;
use CommonOphthalmicDisorder;
use CommonOphthalmicDisorderGroup;
use ReferenceData;

class CommonOphthalmicDisorderFactory extends ModelFactory
{
    use LooksUpExistingModels;

    public function definition(): array
    {
        return [
            'disorder_id' => ModelFactory::factoryFor(Disorder::class)->create(['specialty_id' => '109']),
            'subspecialty_id' => ModelFactory::factoryFor(Subspecialty::class)->useExisting()
        ];
    }

    public function withInstitution($institution)
    {
        return $this->state([
            'institution_id' => $institution
        ]);
    }

    public function forSubspecialty($subspecialty): self
    {
        return $this->state([
            'subspecialty_id' => $subspecialty
        ]);
    }

    public function forGroup($group_id)
    {
        return $this->state(function () use ($group_id) {
            return [
                'group_id' => $group_id
            ];
        });
    }

    public function forKnownGroupName($group_name)
    {
        return $this->state(function () use ($group_name) {
            return [
                'group_id' => ModelFactory::factoryFor(CommonOphthalmicDisorderGroup::class)->useExisting(['name' => $group_name])->create()
            ];
        });
    }

    public function forKnownDisorderTerm($disorder_term)
    {
        return $this->state(function () use ($disorder_term) {
            return [
                'disorder_id' => ModelFactory::factoryFor(Disorder::class)->useExisting(['term' => $disorder_term])->create(['specialty_id' => '109'])
            ];
        });
    }

    public function forDisplayOrder($display_order)
    {
        return $this->state(function () use ($display_order) {
            return [
                'display_order' => $display_order
            ];
        });
    }
}
