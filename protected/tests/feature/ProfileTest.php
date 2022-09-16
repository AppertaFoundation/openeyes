<?php
/**
 * (C) Apperta Foundation, 2022
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

namespace tests\feature;

use Firm;
use Institution;
use ModelCollection;
use OEDbTestCase;
use OE\factories\ModelFactory;
use User;
use UserAuthentication;

/**
 * @group sample-data
 * @group feature
 * @group profile
 */
class ProfileTest extends OEDbTestCase
{
    use \WithTransactions;
    use \MocksSession;
    use \MakesApplicationRequests;

    /** @test */
    public function only_firms_with_runtime_selectable_true_are_available_to_add_to_user_contexts()
    {
        list($user, $institution) = $this->createUserWithInstitution();

        $expected_firm = ModelFactory::factoryFor(Firm::class)->create([
            'runtime_selectable' => 1,
            'name' => 'Expected Foo'
        ]);
        $unexpected_firm = ModelFactory::factoryFor(Firm::class)->create([
            'runtime_selectable' => 0,
            'name' => 'Unexpected Bar'
        ]);

        $response = $this->actingAs($user, $institution)
            ->get('/profile/firms');

        $this->assertCount(
            1,
            $response->filter('[data-test="unselected_firms"] select option[value="' . $expected_firm->id . '"]'),
            'Could not find firm in options for selection in firm profile page'
        );
        $this->assertCount(
            0,
            $response->filter('[data-test="unselected_firms"] select option[value="' . $unexpected_firm->id . '"]'),
            'Found unexpected firm in options for selection in firm profile page'
        );
    }

    // TODO: Set up
    protected function createUserWithInstitution()
    {
        $user = User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = ModelFactory::factoryFor(Institution::class)
            ->withUserAsMember($user)
            ->create();

        return [$user, $institution];
    }
}