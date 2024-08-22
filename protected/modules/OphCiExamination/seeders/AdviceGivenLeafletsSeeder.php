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

namespace OEModule\OphCiExamination\seeders;

use OE\seeders\BaseSeeder;
use OE\seeders\resources\SeededUserResource;

use Institution;
use Site;
use Firm;
use User;
use Patient;

use OEModule\OphCiExamination\models\{
    AdviceGiven,
    AdviceLeaflet,
    AdviceLeafletCategory,
    OphCiExamination_Workflow,
    OphCiExamination_Workflow_Rule,
    OphCiExamination_ElementSet
};

class AdviceGivenLeafletsSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $institution = Institution::factory()->create();
        $site = Site::factory()->create(['institution_id' => $institution]);
        $firm = Firm::factory()->canOwnEpisode()->create(['institution_id' => $institution]);

        $workflow = OphCiExamination_Workflow::factory()
                  ->forInstitution($institution)
                  ->create();

        $workflow_rule = OphCiExamination_Workflow_Rule::factory()
                       ->forWorkflow($workflow)
                       ->forFirm($firm)
                       ->create();

        $advice_given_element_set = OphCiExamination_ElementSet::factory()
                                  ->forWorkflow($workflow)
                                  ->forElementClasses(AdviceGiven::class)
                                  ->create();

        $patient = Patient::factory()->create();

        $user = User::factory()
              ->withAuthItems(['User', 'Edit', 'View clinical'])
              ->withLocalAuthForInstitution($institution)
              ->forSpecificFirms([$firm])
              ->create();

        list($first_category, $second_category) = AdviceLeafletCategory::factory()
                                                ->forInstitution($institution)
                                                ->forSubspecialty($firm->subspecialty)
                                                ->active()
                                                ->count(2)
                                                ->create();

        $first_category_leaflets = AdviceLeaflet::factory()
                                 ->forInstitution($institution)
                                 ->assignedToCategories($first_category)
                                 ->count(2)
                                 ->create();

        $second_category_leaflets = AdviceLeaflet::factory()
                                  ->forInstitution($institution)
                                  ->assignedToCategories($second_category)
                                  ->count(2)
                                  ->create();

        $both_categories_leaflet = AdviceLeaflet::factory()
                                 ->forInstitution($institution)
                                 ->assignedToCategories([$first_category, $second_category])
                                 ->create();

        $leafletSeeder = static function ($leaflet) {
            return ['id' => $leaflet->id, 'name' => $leaflet->name];
        };

        return [
            'patient' => ['id' => $patient->id],
            'institution' => ['id' => $institution->id],
            'site' => ['id' => $site->id],
            'firm' => ['id' => $firm->id],
            'user' => SeededUserResource::from($user)->toArray(),
            'firstCategory' => $leafletSeeder($first_category),
            'secondCategory' => $leafletSeeder($second_category),
            'firstCategoryLeaflets' => array_map($leafletSeeder, $first_category_leaflets),
            'secondCategoryLeaflets' => array_map($leafletSeeder, $second_category_leaflets),
            'bothCategoriesLeaflet' => $leafletSeeder($both_categories_leaflet),
        ];
    }
}
