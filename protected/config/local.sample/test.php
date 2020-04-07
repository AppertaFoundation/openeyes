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


$db_test_host = getenv('DATABASE_TEST_HOST') ?? 'localhost';
$db_test_port = getenv('DATABASE_PORT') ?? '3306';
$db_test_name = getenv('DATABASE_NAME') ?? 'openeyes';
$db_test_user = getenv('DATABASE_USER') ?? rtrim(@file_get_contents("/run/secrets/DATABASE_USER")) ?? 'openeyes';
$db_test_pass = getenv('DATABASE_PASS') ?: rtrim(@file_get_contents("/run/secrets/DATABASE_PASS")) ?? 'openeyes';

return array(
    'components' => array(
        'db' => array(
            'connectionString' => "mysql:host=$db_test_host;port=$db_test_port;dbname=$db_test_name",
            'username' => $db_test_user,
            'password' => $db_test_pass,
        ),
    ),
);
