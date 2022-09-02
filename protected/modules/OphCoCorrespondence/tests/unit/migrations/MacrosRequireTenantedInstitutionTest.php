<?php
/**
 * (C) Copyright Apperta Foundation 2022
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

namespace OEModule\OphCoCorrespondence\tests\unit\migrations;

use LetterMacro;
use OE\factories\ModelFactory;
use Institution;
use Firm;
use InstitutionAuthentication;
use Site;

/**
 * Class MacrosRequireTenantedInstitutionTest
 *
 * Note, this test will only remain valid whilst the underlying database/model structure for LetterMacros and MappedReferenceData
 * remain consistent with the expectations of the migration under test.
 *
 * If this fundamentally changes, this test will no longer work and will need to be excluded from ongoing CI:
 * --exclude-group migration-6.x
 *
 * or removed from the project entirely
 *
 * @package OEModule\OphCoCorrespondence\tests\unit\migrations
 * @covers \OEModule\OphCoCorrespondence\tests\unit\migrations\m220729_141915_macros_require_tenanted_institution
 * @group sample-data
 * @group migration
 * @group ophcocorrespondence
 * @group migration-6.x
 */
class MacrosRequireTenantedInstitutionTest extends \OEDbTestCase
{
    use \WithTransactions;
    use \HasModelAssertions;

    protected $db_connection;

    public function setUp()
    {
        parent::setUp();
        $this->db_connection = \Yii::app()->db;
    }

    /** @test */
    public function migration_correct_when_macro_has_consistent_institution_from_firm_and_site()
    {
        $institution = ModelFactory::factoryFor(Institution::class)->create();
        $letter_macro = ModelFactory::factoryFor(LetterMacro::class)
            ->withFirm(ModelFactory::factoryFor(Firm::class)->create(['institution_id' => $institution->id]))
            ->withSite(ModelFactory::factoryFor(Site::class)->create(['institution_id' => $institution->id]))
            ->create();

        // sanity check for test
        $this->assertEmpty($letter_macro->institutions);

        $this->doUpMigration('m220729_141915_macros_require_tenanted_institution');

        $letter_macro->refresh();
        $this->assertModelArraysMatch([$institution], $letter_macro->institutions);
    }

    /** @test */
    public function migration_correct_when_macro_has_institution_from_firm()
    {
        $institution = ModelFactory::factoryFor(Institution::class)->create();
        $letter_macro = ModelFactory::factoryFor(LetterMacro::class)
            ->withFirm(ModelFactory::factoryFor(Firm::class)->create(['institution_id' => $institution->id]))
            ->create();

        // sanity check for test
        $this->assertEmpty($letter_macro->institutions);

        $this->doUpMigration('m220729_141915_macros_require_tenanted_institution');

        $letter_macro->refresh();
        $this->assertModelArraysMatch([$institution], $letter_macro->institutions);
    }

    /** @test */
    public function migration_correct_when_macro_has_institution_from_site()
    {
        $institution = ModelFactory::factoryFor(Institution::class)->create();
        $letter_macro = ModelFactory::factoryFor(LetterMacro::class)
            ->withSite(ModelFactory::factoryFor(Site::class)->create(['institution_id' => $institution->id]))
            ->create();

        // sanity check for test
        $this->assertEmpty($letter_macro->institutions);

        $this->doUpMigration('m220729_141915_macros_require_tenanted_institution');

        $letter_macro->refresh();
        $this->assertModelArraysMatch([$institution], $letter_macro->institutions);
    }

    /** @test */
    public function migration_correctly_duplicates_macro_to_all_tenanted_institutions_when_macro_has_no_insitution_set()
    {
        $duplicate_name = 'This macro will be duplicated';

        // get or create at least 2 tenanted
        $tenanted_institutions = $this->getOrCreateTenantedInstitutionsMinimumCount(rand(2, 4));
        // untenanted institution
        $unexpected_institution = ModelFactory::factoryFor(Institution::class)->create();

        $letter_macro = ModelFactory::factoryFor(LetterMacro::class)->create([
            'name' => $duplicate_name
        ]);

        // sanity check for test
        $this->assertEmpty($letter_macro->institutions);

        $this->doUpMigration('m220729_141915_macros_require_tenanted_institution');

        $duplicate_macros = LetterMacro::model()->findAllByAttributes(['name' => $duplicate_name]);

        // check there are as many macros created as tenanted institutions
        $this->assertCount(count($tenanted_institutions), $duplicate_macros);

        $institution_ids = [];
        foreach ($duplicate_macros as $duplicate_macro) {
            // ensure no duplicate of institution ids
            $this->assertCount(1, $duplicate_macro->institutions);
            $this->assertNotContains($duplicate_macro->institutions[0]->id, $institution_ids);
            $institution_ids[] = $duplicate_macro->institutions[0]->id;

            // iterate through duplicate properties to ensure match
            foreach (['name', 'use_nickname', 'body', 'cc_patient', 'cc_doctor'] as $attr) {
                $this->assertEquals($letter_macro->$attr, $duplicate_macro->$attr);
            }
        }

        $this->assertNotContains($unexpected_institution->id, $institution_ids);
    }

    /** @test */
    public function migration_throws_exception_when_macro_has_inconsistent_institution_from_firm_and_site()
    {
        $institution = ModelFactory::factoryFor(Institution::class)->create();
        $institution2 = ModelFactory::factoryFor(Institution::class)->create();
        $letter_macro = ModelFactory::factoryFor(LetterMacro::class)
            ->withSite(ModelFactory::factoryFor(Site::class)->create(['institution_id' => $institution->id]))
            ->withFirm(ModelFactory::factoryFor(Firm::class)->create(['institution_id' => $institution2->id]))
            ->create();

        // sanity check for test
        $this->assertEmpty($letter_macro->institutions);

        $this->expectException(\LogicException::class);

        $migration = $this->instantiateMigration('m220729_141915_macros_require_tenanted_institution');
        $migration->safeUp();
    }

    protected function doUpMigration(string $migration_name)
    {
        $migration = $this->instantiateMigration($migration_name);
        ob_start();
        $this->assertNotFalse($migration->safeUp());
        ob_get_clean();
    }

    protected function instantiateMigration(string $migration_name)
    {
        $path = \Yii::getPathOfAlias('application.modules.OphCoCorrespondence.migrations') . DIRECTORY_SEPARATOR . $migration_name . '.php';
        require_once($path);
        $instance = new $migration_name();
        $instance->setDbConnection($this->db_connection);
        return $instance;
    }

    private function getOrCreateTenantedInstitutionsMinimumCount(int $min_requried = 2)
    {
        $tenanted_institutions = Institution::model()->getTenanted();

        while (count($tenanted_institutions) < $min_requried) {
            $tenanted_institutions[] = ModelFactory::factoryFor(Institution::class)->isTenanted()->create();
        }

        return $tenanted_institutions;
    }
}
