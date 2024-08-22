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

/**
 * @group sample-data
 */
class UserAuthenticationTest extends OEDbTestCase
{
    use WithTransactions;

    /** @test */
    public function exception_thrown_when_institution_authentication_not_defined()
    {
        $institution = Institution::factory()->create();
        $user = User::factory()->create();

        $this->expectException(\RuntimeException::class);
        UserAuthentication::model()->findOrCreateSSOAuthentication($user->id, 'foobar', $institution->id, null);
    }

    /** @test */
    public function user_auth_created_when_authentication_available_for_institution()
    {
        $institution_auth = InstitutionAuthentication::factory()
            ->forSSO()
            ->create();

        $user = User::factory()->create();
        $created = UserAuthentication::model()
            ->findOrCreateSSOAuthentication($user->id, 'foobar', $institution_auth->institution_id, $institution_auth->site_id);

        $this->assertEquals($user->id, $created->user_id);
        $this->assertEquals('foobar', $created->username);
        $this->assertNotNull($created->getPrimaryKey());
    }

    /** @test */
    public function existing_instance_returned_when_already_defined()
    {
        $institution_auth = InstitutionAuthentication::factory()
            ->forSSO()
            ->create();

        $user_auth = UserAuthentication::factory()
            ->create([
                'institution_authentication_id' => $institution_auth->id,
                'username' => 'foobar'
            ]);

        $found = UserAuthentication::model()
            ->findOrCreateSSOAuthentication($user_auth->user_id, 'foobar', $institution_auth->institution_id, $institution_auth->site_id);

        $this->assertEquals($user_auth->id, $found->id);
    }

    /**
     * Multiple instances of user authentication indicates an issue in the database
     *
     * @test
     */
    public function exception_thrown_for_bad_data_state()
    {
        $institution_auth = InstitutionAuthentication::factory()
            ->forSSO()
            ->create();
        $user = User::factory()->create();

        UserAuthentication::factory()
            ->count(2)
            ->create([
                'institution_authentication_id' => $institution_auth->id,
                'user_id' => $user->id,
                'username' => 'foobar'
            ]);

        $this->expectException(Exception::class);

        UserAuthentication::model()
            ->findOrCreateSSOAuthentication($user->id, 'foobar', $institution_auth->institution_id, $institution_auth->site_id);
    }
}
