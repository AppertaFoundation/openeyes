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

require_once __DIR__.'/PASAPI_BaseTest.php';

class PASAPI_Test extends PASAPI_BaseTest
{
    protected function initialiseClient($options = array())
    {
        if (!isset($options[Client::REQUEST_OPTIONS])) {
            $options[Client::REQUEST_OPTIONS] = array(
                'auth' => array($this->user->username, 'password'),
                'headers' => array(
                    'Accept' => 'application/xml',
                ),
            );
        }

        $this->client = new Client(
            Yii::app()->params['pas_api_test_base_url'],
            $options
        );
    }

    public function setUp()
    {
        $this->cleanUpTestUser();
        $this->createTestUser();
        $this->initialiseClient();
    }

    /**
     * Check that without being logged in we don't have access.
     */
    public function testAuthRequired()
    {
        $this->initialiseClient(array(
            Client::REQUEST_OPTIONS => array(
                'headers' => array(
                    'Accept' => 'application/xml',
                ),
            ),
        ));

        $this->setExpectedHttpError(401);
        $this->put('Patient/XYZ', '<Patient />');
    }

    /**
     * Check that just logging in with any user doesn't give us access.
     */
    public function testAuthNeedsAccess()
    {

        //strip the API role from the test user
        $this->user->saveRoles(array('User'));

        $this->setExpectedHttpError(403);
        $this->put('Patient/XYZ', '<Patient />');
    }

    /**
     * Get accepts error for wrong format type.
     */
    public function testErrorForJsonAccept()
    {
        $this->initialiseClient(array(
            Client::REQUEST_OPTIONS => array(
                'auth' => array('level1', 'password'),
                'headers' => array(
                    'Accept' => 'application/json',
                ),
            ),
        ));
        $this->setExpectedHttpError(406);
        $this->put('Patient/XYZ', '<Patient />');
    }
}
