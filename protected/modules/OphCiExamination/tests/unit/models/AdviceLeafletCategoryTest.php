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

namespace OEModule\OphCiExamination\tests\unit\models;

use Institution;
use ModelTestCase;
use OEModule\OphCiExamination\models\AdviceLeaflet;
use OEModule\OphCiExamination\models\AdviceLeafletCategory;
use OEModule\OphCiExamination\models\AdviceLeafletCategoryAssignment;
use Subspecialty;

/**
 * Class AdviceLeafletCategoryTest
 *
 * @group sample-data
 * @group examination
 * @group advice-given
 */
class AdviceLeafletCategoryTest extends ModelTestCase
{
    use \WithTransactions;

    protected $element_cls = AdviceLeafletCategory::class;
    protected $existing_ids;

    public function setUp(): void
    {
        parent::setUp();

        // make sure and previous Advice Leaflet Categories are set to be ignored before test
        $this->existing_ids = array_map(function ($existing) {
            return $existing->id;
        },
        AdviceLeafletCategory::model()->findAll()
        );
    }

    /** @test */
    public function active_scope_returns_only_active_results()
    {
        $expected_leaflet_categories = AdviceLeafletCategory::factory()->active()->count(rand(2, 4))->create();
        $unexpected_leaflet_category = AdviceLeafletCategory::factory()->inactive()->create();

        $this->assertModelArraysMatch($expected_leaflet_categories, AdviceLeafletCategory::model()->active()->findAll($this->getExcludeExistingCriteria()));
    }

    /** @test */
    public function institution_scope_only_returns_results_for_specified_institution()
    {
        $institution = Institution::factory()->create();
        $unexpected_institution = Institution::factory()->create();

        $expected_leaflet_categories = AdviceLeafletCategory::factory()->active()->count(rand(2, 4))->create([
            'institution_id' => $institution->id
        ]);
        $unexpected_leaflet_category = AdviceLeafletCategory::factory()->active()->create([
            'institution_id' => $unexpected_institution->id
        ]);

        $this->assertModelArraysMatch($expected_leaflet_categories, AdviceLeafletCategory::model()->forInstitution($institution)->findAll($this->getExcludeExistingCriteria()));
    }

    /** @test */
    public function subspecialty_scope_only_returns_results_for_specified_subspecialty()
    {
        $subspecialty = Subspecialty::factory()->create();
        $unexpected_subspecialty = Subspecialty::factory()->create();

        $expected_leaflet_categories = AdviceLeafletCategory::factory()
            ->forSubspecialty($subspecialty)
            ->active()
            ->count(rand(2, 4))
            ->create();

        $unexpected_leaflet_category = AdviceLeafletCategory::factory()
            ->forSubspecialty($unexpected_subspecialty)
            ->active()
            ->create();

        $this->assertModelArraysMatch($expected_leaflet_categories, AdviceLeafletCategory::model()->forSubspecialty($subspecialty)->findAll($this->getExcludeExistingCriteria()));
    }

    /** @test */
    public function chained_active_subspeciality_and_institution_scopes_work_return_expected_results()
    {
        $institution = Institution::factory()->create();
        $unexpected_institution = Institution::factory()->create();

        $subspecialty = Subspecialty::factory()->create();
        $unexpected_subspecialty = Subspecialty::factory()->create();

        // combined subspeciality, institution and active
        AdviceLeafletCategory::factory()
            ->forSubspecialty($subspecialty)
            ->active()
            ->create([
                'institution_id' => $institution->id
            ]);

        // unexpected
        AdviceLeafletCategory::factory()
            ->forSubspecialty($subspecialty)
            ->inactive()
            ->create([
                'institution_id' => $institution->id
            ]);
        AdviceLeafletCategory::factory()
            ->forSubspecialty($unexpected_subspecialty)
            ->active()
            ->create([
                'institution_id' => $institution->id
            ]);
        AdviceLeafletCategory::factory()
            ->forSubspecialty($subspecialty)
            ->active()
            ->create([
                'institution_id' => $unexpected_institution->id
            ]);

        $this->assertCount(1, AdviceLeafletCategory::model()
            ->forInstitution($institution)
            ->forSubspecialty($subspecialty)
            ->active()
            ->findAll($this->getExcludeExistingCriteria()));
    }

    protected function getExcludeExistingCriteria()
    {
        $criteria = new \CdbCriteria();
        $criteria->addNotInCondition('t.id', $this->existing_ids);
        return $criteria;
    }
}
