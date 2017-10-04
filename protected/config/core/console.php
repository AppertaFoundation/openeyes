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
$config = array(
    'name' => 'OpenEyes Console',
    'import' => array(
            'application.components.*',
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
