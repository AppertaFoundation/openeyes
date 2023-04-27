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

/**
 * @group sample-data
 * @group diagnoses
 * @group disorders
 */
class CommonOphthalmicDisorderGroupTest extends OEDbTestCase
{
    use WithTransactions;
    use WithFaker;

    /** @test */
    public function fully_qualified_name_defaults_to_all()
    {
        $group = CommonOphthalmicDisorderGroup::factory()->create();
        $this->assertStringContainsString('All', $group->fully_qualified_name);
    }

    /** @test */
    public function fully_qualified_name_contains_institution_short_name()
    {
        $short_name = $this->faker->unique()->words(2, true) . ' foo';
        $institution = Institution::factory()->create(['short_name' => $short_name]);
        $group = CommonOphthalmicDisorderGroup::factory()->withInstitution($institution)->create();
        $this->assertStringNotContainsString('All', $group->fully_qualified_name);
        $this->assertStringContainsString($short_name, $group->fully_qualified_name);
    }
}
