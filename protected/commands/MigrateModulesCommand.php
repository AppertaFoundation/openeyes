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
class MigrateModulesCommand extends CConsoleCommand
{
    public $defaultAction = 'up';

    public function getHelp()
    {
        return <<<EOD
USAGE
  yiic migratemodules
  OR
  yiic migratemodules down [--level=N]  (default level is 1)

DESCRIPTION
  This command runs the migrations for all configured modules.
  Note that is is not interactive, and so will not prompt you for each module
  individually. If you require more control then run the module migrations
  separately using the standard migrate command and --migrationPath

EOD;
    }

    public function actionUp($interactive = true, $connectionID = false, $testdata = false, $composer = false)
    {
        $commandPath = Yii::getPathOfAlias('application.commands');
        $modules = Yii::app()->modules;
        $moduleDir = ($composer) ? 'composer.openeyes.' : 'application.modules.';
        // default modules in $modules array: gii, oldadmin
        if (count($modules) <= 2) {
            echo "No modules installed, please check your configuration \n";
        } else {
            foreach ($modules as $module => $module_settings) {
                if (is_dir(Yii::getPathOfAlias($moduleDir.$module.'.migrations'))) {
                    echo "Migrating $module:\n";
                    if (!$interactive) {
                        $args = array(
                            'yiic',
                            'oemigrate',
                            '--interactive=0',
                            '--migrationPath='.$moduleDir.$module.'.migrations',
                        );
                    } else {
                        $args = array('yiic', 'oemigrate', '--migrationPath='.$moduleDir.$module.'.migrations');
                    }
                    if ($connectionID) {
                        $args[] = '--connectionID='.$connectionID;
                    }
                    if ($testdata) {
                        $args[] = '--testdata';
                    }
                    //echo "\nMigratemodules ARGS : " . var_export( $args, true );

                    $runner = new CConsoleCommandRunner();
                    $runner->addCommands($commandPath);
                    $result = $runner->run($args);

                    // If a module fails to migrate, stop and do not continue migrating others
                    if (!empty($result) && $result > 1){
                        echo "\n*****".str_repeat('*', strlen($module))."****************************************\n";
                        echo "**      ". $module . " FAILED TO MIGRATE. EXITING        **\n";
                        echo "*******".str_repeat('*', strlen($module))."**************************************\n";
                        return(1);
                    }

                    
                }
            }
        }
    }

    public function actionDown($level = 1, $connectionID = 'db')
    {
        /* @var CDbConnection $db */
        $db = Yii::app()->getComponent($connectionID);

        $commandPath = Yii::getPathOfAlias('application.commands');
        $modules = Yii::app()->modules;
        $moduleDir = 'application.modules.';
        $migrationNames = $db->createCommand()->select('version')->from('tbl_migration')->order('version DESC')->limit($level)->queryAll();

        $moduleFile = false;

        foreach ($migrationNames as $migrationFile) {
            foreach ($modules as $module => $module_settings) {
                if (is_file(Yii::getPathOfAlias($moduleDir.$module.'.migrations').'/'.$migrationFile['version'].'.php')) {
                    $moduleFile = true;
                    echo $migrationFile['version'].' is in module '.$module."\n";
                    $args = array(
                        'yiic',
                        'oemigrate',
                        'down',
                        '--migrationPath='.$moduleDir.$module.'.migrations',
                    );

                    if ($connectionID) {
                        $args[] = '--connectionID='.$connectionID;
                    }
                }
            }
            // migration was not found in the modules
            if ($moduleFile === false) {
                echo $migrationFile['version']." is not a module migration!\n";
                $args = array(
                    'yiic',
                    'oemigrate',
                    'down',
                );
            }
            $runner = new CConsoleCommandRunner();
            $runner->addCommands($commandPath);
            $runner->run($args);

            $moduleFile = false;
            unset($args);
        }
    }
}
