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

use Guzzle\Http\Client;

abstract class PASAPI_BaseTest extends RestTestCase
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
    protected $user_password = 'password';

    protected $additional_clean_up_models = array();

    protected function cleanUpTestUser()
    {
        if (!$this->user) {
            $this->user = User::model()->with('authentications')->findByAttributes(array('authentications.username' => 'autotestapi'));
            if (!$this->user) {
                return;
            }
        }

        // clear out all the data we've touched, and the user
        foreach (array_merge(
            array('Audit',
                         'AuditAction',
                         'AuditType',
                         'OEModule\\PASAPI\\models\\PasApiAssignment',
                         'Patient',
                         'Address',
                         'Contact', ),
            $this->additional_clean_up_models
        ) as $cls) {
            $cls::model()->deleteAllByAttributes(array('created_user_id' => $this->user->id));
        }

        Audit::model()->deleteAllByAttributes(array('user_id' => $this->user->id));
        $this->user->saveRoles(array());
        $this->user->delete();
    }

    protected function createTestUser()
    {
        $this->user = new User();
        $this->user->attributes = array(
            'active' => 1,
            'global_firm_rights' => 1,
            'first_name' => 'Auto-Test',
            'last_name' => 'API',
            'password' => $this->user_password,
            'password_repeat' => $this->user_password,
            'username' => 'autotestapi',
            'email' => 'auto@test.com',
        );
        $this->user->id = 99999;

        $this->user->noVersion()->save();
        $this->user->saveRoles(array('User', 'API access'));
    }

    protected $base_url_stub;

    public function setUp()
    {
        parent::setUp();

        // do this so if there was an error that prevented clean up in the last test run we can still test again.
        $this->cleanUpTestUser();
        $this->createTestUser();

        $this->client = new Client(
            Yii::app()->params['pas_api_test_base_url'].$this->base_url_stub,
            array(
                Client::REQUEST_OPTIONS => array(
                    'auth' => array($this->user->username, $this->user_password),
                    'headers' => array(
                        'Accept' => 'application/xml',
                    ),
                ),
            )
        );
    }

    public function tearDown()
    {
        $this->cleanUpTestUser();
        parent::tearDown();
    }
}
