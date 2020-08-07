<?php
/**
 * OpenEyes.
 *
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@apperta.org>
 * @copyright Copyright (c) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

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
        'host' => getenv('DATABASE_HOST') ?: 'localhost',
        'port' => getenv('DATABASE_PORT') ?: '3306',
        'dbname' => getenv('DATABASE_NAME') ?: 'openeyes',
        'username' => rtrim(@file_get_contents("/run/secrets/DATABASE_USER")) ?: (getenv('DATABASE_USER') ?: 'openeyes'),
        'password' => rtrim(@file_get_contents("/run/secrets/DATABASE_PASS")) ?: (getenv('DATABASE_PASS') ?: 'openeyes'),
    );
    $db_test = array(
        'host' => getenv('DATABASE_TEST_HOST') ?: (getenv('DATABASE_HOST') ?: 'localhost'),
        'port' => getenv('DATABASE_TEST_PORT') ?: (getenv('DATABASE_PORT') ?: '3306'),
        'dbname' => getenv('DATABASE_TEST_NAME') ?: (getenv('DATABASE_NAME') ?: 'openeyes_test'),
        'username' => rtrim(@file_get_contents("/run/secrets/DATABASE_TEST_USER")) ?: (getenv('DATABASE_TEST_USER') ?: (rtrim(@file_get_contents("/run/secrets/DATABASE_USER")) ?: (getenv('DATABASE_USER') ?: 'openeyes'))),
        'password' => rtrim(@file_get_contents("/run/secrets/DATABASE_TEST_PASS")) ?: (getenv('DATABASE_TEST_PASS') ?: (rtrim(@file_get_contents("/run/secrets/DATABASE_PASS")) ?: (getenv('DATABASE_PASS') ?: 'openeyes'))),
    );
}

$config = array(
    'name' => 'OpenEyes Console',
    'import' => array(
            'application.components.*',
            'application.modules.OphCoCorrespondence.components.*',
            'system.cli.commands.*',
    ),
    'commandMap' => array(
        'migrate' => array(
            'class' => 'application.commands.OEMigrateCommand',
            'migrationPath' => 'application.migrations',
            'migrationTable' => 'tbl_migration',
            'connectionID' => 'db',
        ),
    ),
    'components' => array(
        'db' => array(
            'class' => "OEDbConnection",
            'connectionString' => "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}",
            'username' => $db['username'],
            'password' => $db['password'],
        ),
        'testdb' => array(
            'class' => "OEDbConnection",
            'connectionString' => "mysql:host={$db_test['host']};port={$db_test['port']};dbname={$db_test['dbname']}",
            'username' => $db_test['username'],
            'password' => $db_test['password'],
        ),
        'mailer' => array(
            // Setting the mailer mode to null will suppress email
            //'mode' => null
            // Mail can be diverted by setting the divert array
            //'divert' => array('foo@example.org', 'bar@example.org')
        ),
    ),
);

if (preg_match('/\/protected\/modules\/deploy\/yiic$/', @$_SERVER['SCRIPT_FILENAME']) || preg_match('/\/protected\/modules\/deploy$/', @$_SERVER['PWD'])) {
    $config['commandMap']['migrate']['class'] = 'MigrateCommand';
    $config['commandMap']['migrate']['migrationPath'] = 'application.modules.deploy.migrations';
    $config['commandMap']['migrate']['migrationTable'] = 'tbl_migration_deploy';
}

//Module commands
$modulesDir = __DIR__.'/../../modules/';
$modules = opendir($modulesDir);
if ($modules) {
    while (false !== ($filename = readdir($modules))) {
        if (!in_array($filename, array('.', '..'), true) && is_dir($modulesDir.$filename)) {
            $module = opendir($modulesDir.$filename);
            while (false !== ($moduleSub = readdir($module))) {
                if ($moduleSub === 'commands' && is_dir($modulesDir.$filename.'/'.$moduleSub)) {
                    $commands = scandir($modulesDir.$filename.'/'.$moduleSub);
                    foreach ($commands as $command) {
                        if (strpos($command, 'Command.php')) {
                            $commandName = substr($command, 0, strpos($command, 'Command.php'));
                            $config['commandMap'][strtolower($commandName)] = array('class' => 'application.modules.'.$filename.'.commands.'.$commandName.'Command');
                        }
                    }
                }
            }
        }
    }
}

return $config;
