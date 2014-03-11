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

class OEMigrateCommand extends MigrateCommand
{
	public $testdata = false;
	public $args = null;

	public function actionUp($args, $testdata = false)
	{
		if($testdata)
			$this->testdata = true;

		parent::actionUp($args);
	}

	protected function migrateUp($class)
	{
		if($class===self::BASE_MIGRATION)
			return;

		echo "*** applying $class\n";
		$start=microtime(true);
		$migration=$this->instantiateMigration($class);

		if($this->testdata && $migration instanceof OEMigration){
			$migration->setTestData(true);
			echo "\nRunning in testdata mode";
		}

		if($migration->up()!==false)
		{
			$this->getDbConnection()->createCommand()->insert($this->migrationTable, array(
					'version'=>$class,
					'apply_time'=>time(),
			));
			$time=microtime(true)-$start;
			echo "*** applied $class (time: ".sprintf("%.3f",$time)."s)\n\n";
		}
		else
		{
			$time=microtime(true)-$start;
			echo "*** failed to apply $class (time: ".sprintf("%.3f",$time)."s)\n\n";
			return false;
		}
	}

	/**
	 * @description - Helper method to verify if a cli argument exists and, if it has a value assigned, return it
	 * @param $name - string
	 * @param null $argsInj - (array if provided only for injection purposes)
	 * @return bool|string
	 */
	public function getCliArg($name, $argsInj = null){
		if(!$argsInj){
			$args = $this->args;
		}
		else{
			$args = $argsInj;
		}
		if(!$name || !is_string($name)){
			return false;
		}

		foreach($args as $arg){
			if(strpos($name , $arg) == 0){
				if(strlen($name) == strlen($arg)){
					return true;
				}
				else if( $equalPos = strpos($arg, '=')){
					return substr($arg , $equalPos+1);
				}
			}
		}
		return false;
	}
}
