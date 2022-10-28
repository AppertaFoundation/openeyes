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
Yii::setPathOfAlias('yiitests', Yii::getPathOfAlias('system').'/../tests/framework');

/**
* Obtain db access credentials.
* - If old db.conf file exists (old method from OpenEyes v2.x) then use it.
*   - Else chack for docker secrets
*     - Else test environment variables
*       - Else use default values
**/
if (file_exists('/etc/openeyes/db.conf')) {
    $db = parse_ini_file('/etc/openeyes/db.conf');
} else {
    $db = array(
        'host' => getenv('DATABASE_TEST_HOST') ?: (getenv('DATABASE_HOST') ?: 'localhost'),
        'port' => getenv('DATABASE_TEST_PORT') ?: (getenv('DATABASE_PORT') ?: '3306'),
        'dbname' => getenv('DATABASE_TEST_NAME') ?: (getenv('DATABASE_NAME') ?: 'openeyes_test'),
        'username' => rtrim(@file_get_contents("/run/secrets/DATABASE_TEST_USER")) ?: (getenv('DATABASE_TEST_USER') ?: (rtrim(@file_get_contents("/run/secrets/DATABASE_USER")) ?: (getenv('DATABASE_USER') ?: 'openeyes'))),
        'password' => rtrim(@file_get_contents("/run/secrets/DATABASE_TEST_PASS")) ?: (getenv('DATABASE_TEST_PASS') ?: (rtrim(@file_get_contents("/run/secrets/DATABASE_PASS")) ?: (getenv('DATABASE_PASS') ?: 'openeyes'))),
    );
}

return array(
    'name' => 'OpenEyes Test',
    'basePath' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..',
    'import' => array(
        'application.modules.admin.controllers.*',
        'application.components.*',
        'system.cli.commands.*',
        'system..db.schema.*',
        'system.test.CDbFixtureManager',
        'yiitests.validators.*',
    ),
    'components' => array(
        'assetManager' => array(
            'class' => 'AssetManager',
            'basePath' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets',
        ),
        'fixture' => array(
            'class' => 'DbFixtureManager',
        ),
        'db' => array(
            'class' => 'OEDbConnection',
            'connectionString' => "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}",
            'username' => $db['username'],
            'password' => $db['password'],
        ),
        'dbTestNotConnecting' => array(
            'class' => 'OEDbConnection',
            'connectionString' => 'mysql:host=notArealDB;dbname=openeyestest',
            'username' => 'oe',
            'password' => '_OE_TESTDB_PASSWORD_',
        ),
    ),
    'params' => array(
        'rest_test_base_url' => 'http://localhost/api',
    ),
);
