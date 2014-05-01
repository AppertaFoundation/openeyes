<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class MigrateModulesCommand extends CConsoleCommand
{

	public $defaultAction='up';

	public function getHelp()
	{
		return <<<EOD
USAGE
  yiic migratemodules

DESCRIPTION
  This command runs the migrations for all configured modules.
  Note that is is not interactive, and so will not prompt you for each module
  individually. If you require more control then run the module migrations
  separately using the standard migrate command and --migrationPath

EOD;
	}

	public function actionUp($interactive = true, $connectionID = false, $testdata = false)
	{
		$commandPath = Yii::getPathOfAlias('application.commands');
		$modules = Yii::app()->modules;
		foreach ($modules as $module => $module_settings) {
			if (is_dir(Yii::getPathOfAlias('application.modules.'.$module.'.migrations'))) {
				echo "Migrating $module:\n";
				if(!$interactive) {
					$args = array('yiic', 'oemigrate', '--interactive=0', '--migrationPath=application.modules.'.$module.'.migrations');
				} else {
					$args = array('yiic', 'oemigrate', '--migrationPath=application.modules.'.$module.'.migrations');
				}
				if($connectionID){
					$args[] = '--connectionID=' . $connectionID;
				}
				if($testdata ){
					$args[] = '--testdata' ;
				}
				//echo "\nMigratemodules ARGS : " . var_export( $args, true );

				$runner = new CConsoleCommandRunner();
				$runner->addCommands($commandPath);
				$runner->run($args);
			}
		}
	}

}
