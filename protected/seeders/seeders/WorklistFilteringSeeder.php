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
use OE\seeders\resources\SeededUserResource;

use Institution;
use Site;
use Firm;

use User;
use SettingUser;

use Worklist;
use WorklistDefinition;
use WorklistPatient;
use Patient;

class WorklistFilteringSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $institution = Institution::factory()
                     ->isTenanted()
                     ->create();

        $site = Site::factory()
              ->forInstitution($institution)
              ->create();

        $firm = Firm::factory()
              ->forInstitution($institution)
              ->create();

        $user = User::factory()
              ->withAuthItems(['User'])
              ->withLocalAuthForInstitution($institution)
              ->withDefaultWorklistFilter()
              ->create();

        SettingUser::factory()
          ->forUser($user)
          ->forKey('worklist_auto_sync_interval')
          ->forValue('off')
          ->create();

        $definitions = WorklistDefinition::factory()
                     ->forInstitution($institution)
                     ->count(4)
                     ->create();

        // 8 days instead of 7 to accomodate the system clock potentially crossing over a day
        // between the seeder running and the test running
        $range = new \DatePeriod(new \DateTime('now'), new \DateInterval('P1D'), 8);

        $patient = Patient::factory()->create();

        foreach ($definitions as $definition) {
            foreach ($range as $day) {
                $worklist = Worklist::factory()
                    ->forDefinition($definition)
                    ->create([
                        'name' => $definition->name,
                        'start' => $day->format('Y-m-d 00:00:00'),
                        'end' => $day->format('Y-m-d 23:59:59'),
                        'scheduled' => true
                    ]);

                WorklistPatient::factory()
                  ->forWorklist($worklist)
                  ->forPatient($patient)
                  ->create();
            }
        }

        return [
            'institution' => ['id' => $institution->id],
            'site' => ['id' => $site->id, 'name' => $site->name],
            'user' => SeededUserResource::from($user)->toArray(),
            'definitions' => array_map(
              static function ($definition) { return ['id' => $definition->id, 'name' => $definition->name]; },
              $definitions
            )
        ];
    }
}
