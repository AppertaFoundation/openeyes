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

namespace OEModule\OphDrPrescription\seeders;

use OE\seeders\BaseSeeder;
use OphDrPrescription_DispenseCondition;

class DispenseConditionAndLocationSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $institution = $this->app_context->getSelectedInstitution();
        $dispense_condition = OphDrPrescription_DispenseCondition::factory()
        ->withInstitution($institution)
        ->withDispenseLocation($institution)
        ->create();

        $dispense_location = $dispense_condition->getLocationsForCurrentInstitution()[0];

        return [
            "dispense_condition_id" => $dispense_condition->id,
            "dispense_condition_name" => $dispense_condition->name,
            "dispense_location_id" => $dispense_location->id,
            "dispense_location_name" => $dispense_location->name
        ];
    }
}
