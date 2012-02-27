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

class m110628_150554_tidy_records extends CDbMigration
{
	public function up()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$this->truncateTable('theatre');
		$this->truncateTable('site');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();

		$this->addColumn('site', 'code', 'char(2) NOT NULL');
		$this->addColumn('site', 'short_name', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'address1', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'address2', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'address3', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'postcode', 'varchar(10) NOT NULL');
		$this->addColumn('site', 'telephopne', 'varchar(255) NOT NULL');
		$this->addColumn('site', 'fax', 'varchar(255) NOT NULL');

		$this->insert('site', array(
			'name' => 'Example site long name',
			'short_name' => 'Example site',
			'code' => 'A1'
		));

		$this->addColumn('theatre', 'code', 'varchar(4) NOT NULL');

		$this->insert('theatre', array(
			'name' => 'Example theatre',
			'code' => 'ABCD',
			'site_id' => 1
		));
	}

	public function down()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$this->truncateTable('theatre');
		$this->truncateTable('site');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();

		$this->dropColumn('site', 'code');
		$this->dropColumn('site', 'short_name');
		$this->dropColumn('site', 'address1');
		$this->dropColumn('site', 'address2');
		$this->dropColumn('site', 'address3');
		$this->dropColumn('site', 'postcode');
		$this->dropColumn('site', 'telephopne');
		$this->dropColumn('site', 'fax');

		$this->insert('site', array(
			'name' => 'Example site long name',
		));

		$this->dropColumn('theatre', 'code');

		$this->insert('theatre', array(
			'name' => 'Example theatre',
			'site_id' => 1
		));
	}
}
