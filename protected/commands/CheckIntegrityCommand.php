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

class CheckIntegrityCommand extends CConsoleCommand {

	var $db_name = 'oedevelopment';

	public function getName() {
		return 'Check Integrity Command.';
	}

	public function getHelp() {
		return "Checks the referential integrity of the database.\n";
	}

	public function run($args) {

		echo "Integrity report\n";
		echo "----------------\n\n";
		
		// Initialise db
		$connection = Yii::app()->db;

		// Get foreign keys
		$query = "SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = :db_name AND REFERENCED_TABLE_NAME IS NOT NULL";
		$keys = $connection->createCommand($query)->query(array(':db_name' => $this->db_name));

		foreach($keys as $key) {
			$command = $connection->createCommand();
			$command->select('REFERRING.*');
			$command->from("{$key['TABLE_NAME']} REFERRING");
			$command->leftJoin("{$key['REFERENCED_TABLE_NAME']} REFERENCED", "REFERRING.{$key['COLUMN_NAME']} = REFERENCED.{$key['REFERENCED_COLUMN_NAME']}");
			$command->where("REFERRING.{$key['COLUMN_NAME']} IS NOT NULL AND REFERENCED.{$key['REFERENCED_COLUMN_NAME']} IS NULL");
			$broken = $command->queryAll();
			if($count = count($broken)) {
				echo "{$key['TABLE_NAME']}.{$key['COLUMN_NAME']} contains $count broken keys\n";
				foreach($broken as $key => $line) {
					echo implode(',',$line)."\n";
				}
			}
		}
		
		echo "\n";
	}

}
