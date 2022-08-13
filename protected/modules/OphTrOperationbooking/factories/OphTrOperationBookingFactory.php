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

use OE\factories\models\EventFactory;
use OE\factories\ModelFactory;

class OphTrOperationBookingFactory extends EventFactory
{
    protected static $requiredElements = [
        Element_OphTrOperationbooking_ContactDetails::class,
        Element_OphTrOperationbooking_Diagnosis::class,
        Element_OphTrOperationbooking_Operation::class,
        Element_OphTrOperationbooking_PreAssessment::class,
        Element_OphTrOperationbooking_ScheduleOperation::class
    ];

    protected array $elementStates = [];

    public function definition(): array
    {
        return array_merge(
            parent::definition(),
            [
                'event_type_id' => $this->getEventTypeByName('Operation booking')
            ]
        );
    }

    public function configure()
    {
        parent::configure();

        foreach (static::$requiredElements as $requiredElementClass) {
            $this->afterCreating(function ($event) use ($requiredElementClass) {
                ModelFactory::factoryFor($requiredElementClass)
                    ->applyStates($this->elementStates[$requiredElementClass] ?? [])
                    ->create([
                        'event_id' => $event->id
                    ]);
            });
        }

        return $this;
    }

    public function bookedWithStates($states = [])
    {
        return $this->applyElementStates(Element_OphTrOperationbooking_Operation::class, $states);
    }

    public function applyElementStates(string $element_cls, array $states)
    {
        $this->elementStates[$element_cls] = array_merge(
            $this->elementStates[$element_cls] ?? [],
            $states
        );
    }
}