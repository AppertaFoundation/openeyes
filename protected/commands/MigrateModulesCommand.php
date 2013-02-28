<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class MigrateModulesCommand extends CConsoleCommand {

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
	
	public function run($args) {
		$commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
		$modules = Yii::app()->modules;
		foreach($modules as $module => $module_settings) {
			if(is_dir(Yii::getPathOfAlias($module.'.migrations'))) {
				echo "Migrating $module:\n";
				$args = array('yiic', 'migrate', '--migrationPath='.$module.'.migrations');
				$runner = new CConsoleCommandRunner();
				$runner->addCommands($commandPath);
				$runner->run($args);
			}
		}
	}
}
