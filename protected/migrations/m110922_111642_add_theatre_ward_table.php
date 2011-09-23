<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class m110922_111642_add_theatre_ward_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('theatre_ward_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'theatre_id' => 'int(10) unsigned NOT NULL',
			'ward_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `theatre_id` (`theatre_id`)',
			'KEY `ward_id` (`ward_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		
		$this->addForeignKey('theatre_ward_assignment_1','theatre_ward_assignment','theatre_id','theatre','id');
		$this->addForeignKey('theatre_ward_assignment_2','theatre_ward_assignment','ward_id','ward','id');	
	}

	public function down()
	{
		$this->dropForeignKey('theatre_ward_assignment_1','theatre_ward_assignment');
		$this->dropForeignKey('theatre_ward_assignment_2','theatre_ward_assignment');
		
		$this->dropTable('theatre_ward_assignment');
	}
}
