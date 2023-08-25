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

namespace OE\seeders\seeders;

use OE\seeders\BaseSeeder;

use Institution;
use Disorder;

class EditCommonSystemicDisordersSeeder extends BaseSeeder
{
    /**
    * Returns the data required to access the created settings and check their properties
    * Return data is:
    * - checkbox - array with elements key, name, started_checked
    * @return array
    */
    public function __invoke(): array
    {
        // An institution to store the mappings
        $institution = Institution::factory()->isTenanted()->create();

        // Pick two disorders
        $disorders = Disorder::model()->active()->findAll([
            'condition' => 'specialty_id IS NULL',
            'order' => 'RAND()',
            'limit' => 2
        ]);

        return [
            'institution' => [
                'id' => $institution->id,
                'name' => $institution->name
            ],
            'disorder1' => [
                'id' => $disorders[0]->id,
                'term' => $disorders[0]->term
            ],
            'disorder2' => [
                'id' => $disorders[1]->id,
                'term' => $disorders[1]->term
            ]
        ];
    }
}
