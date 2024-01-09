<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @covers UserIdentity
 */

class UserIdentityTest extends OEDbTestCase
{
    use WithTransactions;

    private function getUser(
        array $firms = [],
        bool $active_auth = true,
        bool $sso_auth = false,
    ): User {
        $user_factory = User::factory();

        if (!empty($firms)) {
            $user_factory->forSpecificFirms($firms);
        } else {
            $user_factory->withGlobalFirmRights();
        }

        if ($sso_auth) {
            $institution = Institution::factory()->withAuthenticationMethod("SSO")->withSite()->create();
            $user_factory->withSSOAuthForInstitution($institution, "password", $active_auth);
        } else {
            $institution = Institution::factory()->withAuthenticationMethod("LOCAL")->withSite()->create();
            $user_factory->withLocalAuthForInstitution($institution, "password", $active_auth);
        }

        $user_factory->withAuthItems(["User"]);
        Yii::app()->session['selected_institution_id'] = $institution->id;

        return $user_factory->create();
    }

    private function getUserIdentity($user)
    {
        $user_authentication = $user->authentications[0];

        return new UserIdentity(
            $user->authentications[0]->username,
            'password',
            $user_authentication->institutionAuthentication->institution->id,
            $user_authentication->institutionAuthentication->institution->sites[0]->id
        );
    }

    /** @test */
    public function authenticate_invalid_user()
    {
        Yii::app()->params['auth_source'] = 'BASIC';

        $user_identity = new UserIdentity(
            'wronguser',
            'password'
        );

        $this->assertFalse($user_identity->authenticate()[0]);
        $this->assertEquals(
            $user_identity->errorCode,
            UserIdentity::ERROR_USERNAME_INVALID
        );
    }

    /** @test */
    public function authenticate_invalid_password()
    {
        Yii::app()->params['auth_source'] = 'BASIC';

        $user = $this->getUser();
        $user_identity = $this->getUserIdentity($user);
        $user_identity->password = "wrongpassword";

        $this->assertFalse($user_identity->authenticate()[0]);
        $this->assertEquals(
            UserIdentity::ERROR_PASSWORD_INVALID,
            $user_identity->errorCode
        );
    }

    /** @test */
    public function authenticate_basic_login_with_global_firm_rights()
    {
        Yii::app()->params['auth_source'] = 'BASIC';

        $user = $this->getUser();
        $user_identity = $this->getUserIdentity($user);
        $response = $user_identity->authenticate();

        $this->assertTrue((bool)$user->global_firm_rights);
        $this->assertTrue($response[0]);
    }

    /** @test */
    public function authenticate_basic_login_without_global_firm_rights()
    {
        Yii::app()->params['auth_source'] = 'BASIC';

        $firms = Firm::factory()->count(2)->useExisting(["institution_id" => null, "runtime_selectable" => 1])->create();
        $user = $this->getUser($firms);
        $user_identity = $this->getUserIdentity($user);

        $this->assertFalse((bool) $user->global_firm_rights);
        $this->assertTrue($user_identity->authenticate()[0]);
    }

    /** @test */
    public function authenticate_saml_login()
    {
        Yii::app()->params['auth_source'] = 'SAML';

        $user = $this->getUser([], true, true);
        $user_identity = $this->getUserIdentity($user);
        $this->assertTrue($user_identity->authenticate()[0]);
    }

    /** @test */
    public function authenticate_oidc_login()
    {
        Yii::app()->params['auth_source'] = 'OIDC';

        $user = $this->getUser([], true, true);
        $user_identity = $this->getUserIdentity($user);
        $this->assertTrue($user_identity->authenticate()[0]);
    }

    /** @test */
    public function authenticate_expired_login()
    {
        Yii::app()->params['auth_source'] = 'BASIC';

        $user = $this->getUser();
        $expired_user_auth = UserAuthentication::factory()->create([
            'user_id' => $user->id,
            'institution_authentication_id' => $user->authentications[0]->institution_authentication_id,
            'password' => 'password',
            'password_repeat' => 'password',
            'password_status' => UserAuthentication::EXPIRED_PASSWORD,
            'active' => true
        ]);
        $user->authentications = [$expired_user_auth];
        $user_identity = $this->getUserIdentity($user);

        $response = $user_identity->authenticate();

        $this->assertEquals(UserAuthentication::EXPIRED_PASSWORD, $user->authentications[0]->password_status);
        $this->assertTrue($response[0]);
    }
}
