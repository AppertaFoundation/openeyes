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

use OE\factories\ModelFactory;

class Element_OphTrOperationbooking_OperationFactory extends ModelFactory
{
    /**
	 * @return array
	 */
	public function definition(): array 
    {
        return [
            'event_id' => EventFactory::forModule('OphTrOperationbooking'),
            'eye_id' => ModelFactory::factoryFor(\Eye::class)->useExisting(),
            'priority_id' => ModelFactory::factoryFor(OphTrOperationbooking_Operation_Priority::class)->useExisting(),
            'site_id' => ModelFactory::factoryFor(Site::class)->useExisting(),
            'total_duration' => $this->faker->numberBetween(10, 150),
            'status_id' => ModelFactory::factoryFor(OphTrOperationbooking_Operation_Status::class)->useExisting(),
        ];
    }
}