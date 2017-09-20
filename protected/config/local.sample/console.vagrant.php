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

$db_name = getenv('DATABASE_NAME') ? getenv('DATABASE_NAME') : 'openeyes';
$db_host = getenv('DATABASE_HOST') ? getenv('DATABASE_HOST') : '127.0.0.1';
$db_port = getenv('DATABASE_PORT') ? getenv('DATABASE_PORT') : '3306';
$db_user = getenv('DATABASE_USER') ? getenv('DATABASE_USER') : 'openeyes';
$db_pass = getenv('DATABASE_PASS') ? getenv('DATABASE_PASS') : 'openeyes';

$db_test_name = getenv('DATABASE_TEST_NAME') ? getenv('DATABASE_TEST_NAME') : 'openeyes_test';
$db_test_host = getenv('DATABASE_TEST_HOST') ? getenv('DATABASE_TEST_HOST') : '127.0.0.1';
$db_test_port = getenv('DATABASE_TEST_PORT') ? getenv('DATABASE_TEST_PORT') : '3306';
$db_test_user = getenv('DATABASE_TEST_USER') ? getenv('DATABASE_TEST_USER') : 'openeyes';
$db_test_pass = getenv('DATABASE_TEST_PASS') ? getenv('DATABASE_TEST_PASS') : 'openeyes';

return array(
    'components' => array(
        'db' => array(
            'connectionString' => "mysql:host=$db_host;port=$db_port;dbname=$db_name",
            'username' => $db_user,
            'password' => $db_pass,
        ),
        'testdb' => array(
            'class' => 'CDbConnection',
            'connectionString' => "mysql:host=$db_test_host;port=$db_test_port;dbname=$db_test_name",
            'username' => $db_test_user,
            'password' => $db_test_pass,
        ),
    ),
);
