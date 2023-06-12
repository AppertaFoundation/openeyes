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
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\Admin\seeders;

use OE\seeders\BaseSeeder;
use OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications;

/**
 * PostOpComplicationSeeder is a seeder for generating data for use in testing the admin screen for post-op complications
 */
class PostOpComplicationSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $faker = $this->getApp()->dataGenerator->faker();

        $add_complication_name = $faker->words(5, true);

        $delete_complication = OphCiExamination_PostOpComplications::factory()->create();
        $delete_complication_name = $delete_complication->name;

        $assigned_complication = OphCiExamination_PostOpComplications::factory()->create();
        $assigned_complication_name = $assigned_complication->name;

        OphCiExamination_PostOpComplications::model()->assign(
            [$assigned_complication->id],
            $this->app_context->getSelectedInstitution()->id,
            $this->app_context->getSelectedFirm()->serviceSubspecialtyAssignment->subspecialty_id
        );

        return [
            'add_complication_name' => $add_complication_name,
            'delete_complication_name' => $delete_complication_name,
            'assigned_complication_name' => $assigned_complication_name,
        ];
    }
}
