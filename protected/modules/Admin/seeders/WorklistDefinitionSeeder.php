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

class WorklistDefinitionSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $worklists = \Worklist::factory()->count(5)->create();

        $sites = \Site::factory()->count(2)->create();

        for ($i = 0; $i < count($worklists); $i++) {
            \WorklistPatient::factory()->count(5)->create(['worklist_id' => $worklists[$i]]);

            if ($i == 0 || $i == 1) {
                $site = $sites[0];
            } else {
                $site = $sites[1];
            }
            \WorklistDefinitionDisplayContext::factory()->forWorklistDefinition($worklists[$i]->worklist_definition_id)->forSite($site)->create();
        }

        $worklists_for_sites = [
            'worklists_for_sites' => [
                'site' => array_map(
                    function ($site) {
                        return [
                            'id' => $site->id,
                            'short_name' => $site->short_name,
                        ];
                    },
                    $sites
                )
            ]
        ];

        $worklists_for_sites['worklists_for_sites']['site'][0]['worklists'] = [
            $worklists[0]->id => $worklists[0]->name,
            $worklists[1]->id => $worklists[1]->name,
        ];

        $worklists_for_sites['worklists_for_sites']['site'][0]['is_combined'] = true;

        $worklists_for_sites['worklists_for_sites']['site'][1]['worklists'] = [
            $worklists[2]->id => $worklists[2]->name,
            $worklists[3]->id => $worklists[3]->name,
            $worklists[4]->id => $worklists[4]->name,
        ];

        $worklists_for_sites['worklists_for_sites']['site'][1]['is_combined'] = false;

        return $worklists_for_sites;
    }
}
