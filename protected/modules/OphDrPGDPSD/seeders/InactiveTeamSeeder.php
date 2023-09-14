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

namespace OEModule\OphDrPGDPSD\seeders;

use OE\seeders\resources\{
    SeededUserResource,
    SeededEventResource
};

use OE\seeders\BaseSeeder;

use User;
use Team;

use OEModule\OphDrPGDPSD\models\{
    Element_DrugAdministration,
    OphDrPGDPSD_PGDPSD,
    OphDrPGDPSD_PGDPSDMeds,
    OphDrPGDPSD_Assignment
};

class InactiveTeamSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $institution = $this->app_context->getSelectedInstitution();

        $user = User::factory()
            ->withLocalAuthForInstitution($institution)
            ->withAuthItems([
                    'User',
                    'Edit',
                    'View clinical',
                ])
            ->create();

        $team = Team::factory()->inactive()->withUsers([$user])->withTasks([$user->id => Team::DEFAULT_TASK])->create();

        $psd = OphDrPGDPSD_PGDPSD::factory()->psd()->forInstitution($institution)->forTeams([$team])->withMeds(1)->create();

        $assignment = OphDrPGDPSD_Assignment::factory()
                    ->forInstitution($institution)
                    ->forPGDPSD($psd)
                    ->create();

        $element = Element_DrugAdministration::factory()->forAssignments([$assignment])->create();

        $event = $element->event;
        $patient = $event->episode->patient;

        return [
            'user' => SeededUserResource::from($user)->toArray(),
            'event' => SeededEventResource::from($event)->toArray(),
        ];
    }
}
