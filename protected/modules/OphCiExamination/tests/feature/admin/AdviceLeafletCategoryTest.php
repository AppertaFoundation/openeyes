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

use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\AdviceLeafletCategory;

/**
 * @group sample-data
 * @group feature
 * @group profile
 */
class AdviceLeafletCategoryTest extends OEDbTestCase
{
    use \WithTransactions;
    use \MocksSession;
    use \MakesApplicationRequests;

    /** @test */
    public function both_active_and_inactive_categories_are_listed_for_edit()
    {
        list($user, $institution) = $this->createUserWithInstitution();

        $expected_active_leaflet_category = AdviceLeafletCategory::factory()->active()->create([
            'institution_id' => $institution->id
        ]);
        $expected_inactive_leaflet_category = AdviceLeafletCategory::factory()->inactive()->create([
            'institution_id' => $institution->id
        ]);
        $other_institution_leaflet_category = AdviceLeafletCategory::factory()->active()->create([
                'institution_id' => ModelFactory::factoryFor(Institution::class)
                                        ->withUserAsMember($user)
                                        ->create()->id
        ]);

        $response = $this->actingAs($user, $institution)
            ->get('/OphCiExamination/admin/adviceLeafletCategories')
            ->assertSuccessful()
            ->crawl();

        $this->assertCount(
            1,
            $response->filter('[data-test="advice-leaflet-categories"] input[value="' . $expected_active_leaflet_category->id . '"]'),
            'Could not find active category in options for selection in advice leaflet categories page'
        );
        $this->assertCount(
            1,
            $response->filter('[data-test="advice-leaflet-categories"] input[value="' . $expected_inactive_leaflet_category->id . '"]'),
            'Could not find inactive category in options for selection in advice leaflet categories page'
        );
        $this->assertCount(
            0,
            $response->filter('[data-test="advice-leaflet-categories"] input[value="' . $other_institution_leaflet_category->id . '"]'),
            'Should not find category for other institution in options for selection in advice leaflet categories page'
        );
    }
}
