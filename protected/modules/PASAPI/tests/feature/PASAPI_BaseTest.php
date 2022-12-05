<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PASAPI\tests\feature;

use GuzzleHttp\Client;
use OE\factories\models\UserAuthenticationFactory;
use OEModule\PASAPI\models\PasApiAssignment;
use User;

abstract class PASAPI_BaseTest extends \RestTestCase
{
    protected static $namespaces = array(
        'atom' => 'http://www.w3.org/2005/Atom',
    );

    /**
     * @var Client
     */
    protected $client;
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $username = 'api';

    /**
     * @var string
     */
    protected $user_password = 'password';

    protected $additional_clean_up_models = [];

    protected $capture_error_responses = true;

    protected function createTestUser()
    {
        if (PASAPITestState::$apiUserSetup) {
            return;
        }

        // we cannot create a test user because API authentication requires the username
        // to be in the "special_usernames" configuration.

        // ordinarily we would put this into the configuration to force it for the created user
        // but the requests are served by a separate process and therefore we cannot alter the
        // system configuration.

        // instead, we validate that the api user is in the environment (so we know it can authenticate)
        // and then setup its authentication to work correctly.
        if (!in_array($this->username, (array) \Yii::app()->params['special_usernames'])) {
            $this->fail("API user with username {$this->username} not configured for 'special' treatment in authentication");
        }

        $user_authentication = \UserAuthentication::model()
            ->findByAttributes(['username' => $this->username]);

        $user_authentication->password = $this->user_password;
        $user_authentication->password_repeat = $this->user_password;
        if (!$user_authentication->institution_authentication_id) {
            // because we are resetting the password, we have to set this property for validation
            $user_authentication->institution_authentication_id = \InstitutionAuthentication::model()->findall()[0]->id;
        }
        $password_updated = UserAuthenticationFactory::disablePasswordRestrictionsFor(
            function ($instance) {
                return $instance->save(true);
            },
            [$user_authentication]
        );

        if (!$password_updated) {
            $this->fail("Could not set user auth password:" . print_r($user_authentication->getErrors(), true));
        }
        $user_authentication->institution_authentication_id = null;
        $user_authentication->save(false);

        // ensure API roles are set
        $user_authentication->user->saveRoles(['API access', 'User']);

        PASAPITestState::$apiUserSetup = true;
        PASAPITestState::$user = $user_authentication->user;
    }

    protected $base_url_stub;

    public function setUp()
    {
        parent::setUp();

        $this->cleanUpTestModels();
        $this->createTestUser();

        $this->client = new Client(
            [
                'base_uri' => \Yii::app()->params['pas_api_test_base_url'] . $this->base_url_stub,
                'auth' => [$this->username, $this->user_password],
                'headers' => [
                    'Accept' => 'application/xml',
                ]
            ]
        );
    }

    public function tearDown()
    {
        $this->cleanUpTestModels();
        parent::tearDown();
    }

    protected function cleanUpTestModels(): void
    {
        $user = \User::model()->with([
                'authentications' => [
                    'condition' => 'authentications.username = :username',
                    'params' => [':username' => $this->username]
                ]
            ])
            ->find();

        if (!$user) {
            return;
        }

        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(['created_user_id' => $user->id]);

        // arbitrary date that can be changed to suit when running locally
        $criteria->compare('created_date', '>2022-11-28');
        foreach (
            array_merge(
                $this->additional_clean_up_models,
                [
                    \Audit::class,
                    PasApiAssignment::class,
                    \PatientIdentifier::class,
                    \Patient::class,
                    \Address::class,
                    \Contact::class
                ]
            ) as $cls
        ) {
            $cls::model()->deleteAll($criteria);
        }
    }
}
