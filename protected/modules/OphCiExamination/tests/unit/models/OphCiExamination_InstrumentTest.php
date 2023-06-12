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

use ModelTestCase;
use WithTransactions;
use WithFaker;
use MakesApplicationRequests;
use HasModelAssertions;

use Institution;
use User;

use OEModule\OphCiExamination\models\OphCiExamination_Instrument;

/**
 * Class OphCiExamination_InstrumentTest
 *
 * @group sample-data
 * @group examination
 * @group iop
 */
class OphCiExamination_InstrumentTest extends ModelTestCase
{
    use WithTransactions;
    use WithFaker;
    use MakesApplicationRequests;
    use HasModelAssertions;

    protected const IOP_INSTRUMENTS_VIEW_ADMIN_URL = '/OphCiExamination/admin/viewIOPInstruments';
    protected const IOP_INSTRUMENTS_ADD_ADMIN_URL = '/OphCiExamination/admin/addIOPInstrument';
    protected const IOP_INSTRUMENTS_EDIT_ADMIN_URL = '/OphCiExamination/admin/editIOPInstrument/';

    protected $element_cls = OphCiExamination_Instrument::class;

    /** @test */
    public function institution_level_admins_cannot_change_the_name_of_shared_instruments()
    {
        $institutions = Institution::factory()->count(2)->create();

        $unshared_instrument = OphCiExamination_Instrument::factory()
                             ->forInstitutions([$institutions[0]])
                             ->create();

        $shared_instrument = OphCiExamination_Instrument::factory()
                           ->forInstitutions($institutions)
                           ->create();

        $unshared_instrument->name = $this->faker->word(2);

        $this->assertAttributeValid($unshared_instrument, 'name');

        $shared_instrument->name = $this->faker->word(2);

        $this->assertAttributeInvalid($shared_instrument, 'name', 'Cannot change the name');
    }

    /** @test */
    public function installation_level_admins_can_change_the_name_of_shared_instruments()
    {
        $institutions = Institution::factory()->count(2)->create();

        $shared_instrument = OphCiExamination_Instrument::factory()
                           ->forInstitutions($institutions)
                           ->create();

        $shared_instrument->name = $this->faker->word(2);

        $shared_instrument->setScenario('installationAdminSave');

        $this->assertAttributeValid($shared_instrument, 'name');
    }

    /** @test */
    public function only_installation_level_admins_can_see_instruments_across_multiple_institutions()
    {
        $installation_admin = User::factory()
                            ->useExisting(['id' => '1'])
                            ->create();

        $institution_admin = User::factory()
                           ->withAuthItems([
                               'User',
                               'Institution Admin'
                           ])
                           ->create();

        $institution1 = Institution::factory()
                      ->withUserAsMember($installation_admin)
                      ->withUserAsMember($institution_admin)
                      ->create();

        $institution2 = Institution::factory()
                      ->create();

        $instrument1 = OphCiExamination_Instrument::factory()
                     ->forInstitutions([$institution1])
                     ->create();

        $instrument2 = OphCiExamination_Instrument::factory()
                     ->forInstitutions([$institution2])
                     ->create();

        $instrument3 = OphCiExamination_Instrument::factory()
                     ->forInstitutions([$institution1, $institution2])
                     ->create();

        $installation_response = $this->actingAs($installation_admin, $institution1)
            ->get(static::IOP_INSTRUMENTS_VIEW_ADMIN_URL);

        $installation_instruments = $installation_response->filter('[data-test="iop-instrument-name"]')->extract(['_text']);

        $this->assertContains($instrument1->name, $installation_instruments);
        $this->assertContains($instrument2->name, $installation_instruments);
        $this->assertContains($instrument3->name, $installation_instruments);

        $institution_response = $this->actingAs($institution_admin, $institution1)
            ->get(static::IOP_INSTRUMENTS_VIEW_ADMIN_URL);

        $institution_instruments = $institution_response->filter('[data-test="iop-instrument-name"]')->extract(['_text']);

        $this->assertContains($instrument1->name, $institution_instruments);
        $this->assertNotContains($instrument2->name, $institution_instruments);
        $this->assertContains($instrument3->name, $institution_instruments);
    }

    /** @test */
    public function installation_admin_can_choose_any_tenanted_instutions_when_adding_an_instrument()
    {
        $installation_admin = User::factory()
                                  ->useExisting(['id' => '1'])
                                  ->create();

        $installation_institution = Institution::factory()
                      ->withUserAsMember($installation_admin)
                      ->create();

        $tenanted_institutions = Institution::factory()
                               ->isTenanted()
                               ->count(3)
                               ->create();

        array_push($tenanted_institutions, $installation_institution);

        $response = $this->actingAs($installation_admin, $installation_institution)
                  ->get(static::IOP_INSTRUMENTS_ADD_ADMIN_URL);

        $options = $response->filter('[data-test="instrument-institutions-list"] option')->extract(['value']);

        // The current institution only entry should appear to institution admins but not installation admins
        $this->assertEquals(0, $response->filter('[data-test="instrument-current-institution"]')->count());

        $expected = Institution::model()->getTenanted();
        $received = Institution::model()->findAllByPk($options);

        $this->assertModelArraysMatch($expected, $received);
    }

    /** @test */
    public function institution_admin_must_use_their_institution_when_adding_an_instrument()
    {
        $institution_admin = User::factory()
                                 ->withAuthItems([
                                     'User',
                                     'Institution Admin'
                                 ])
                                 ->create();

        $institution = Institution::factory()
                      ->withUserAsMember($institution_admin)
                      ->create();

        Institution::factory()
            ->isTenanted()
            ->count(3)
            ->create();

        $response = $this->actingAs($institution_admin, $institution)
                  ->get(static::IOP_INSTRUMENTS_ADD_ADMIN_URL);

        $this->assertEquals(0, $response->filter('[data-test="instrument-institutions-list"]')->count());

        $field = $response->filter('[data-test="instrument-current-institution"]');

        $this->assertEquals(1, $field->count());
        $this->assertEquals($institution->id, $field->extract(['value'])[0]);
    }

    protected function admin_edit_url_for(OphCiExamination_Instrument $instrument): string
    {
        return IOP_INSTRUMENTS_EDIT_ADMIN_URL . $instrument->id;
    }
}
