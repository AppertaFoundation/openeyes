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

class m110317_105251_create_address_tables extends CDbMigration
{
    public function up()
    {
		$this->createTable('address', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'address1' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'address2' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'city' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'postcode' => 'varchar(10) COLLATE utf8_bin DEFAULT NULL',
			'county' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'country_id' => 'int(10) unsigned NOT NULL',
			'email' => 'varchar(60) COLLATE utf8_bin NOT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->addColumn('contact', 'address_id', 'int(10) unsigned NOT NULL');
		$this->addColumn('contact', 'primary_phone', 'varchar(20) DEFAULT NULL');

		$this->addColumn('patient', 'address_id', 'int(10) unsigned NOT NULL');
		$this->addColumn('patient', 'primary_phone', 'varchar(20) DEFAULT NULL');
    }

    public function down()
    {
		$this->dropColumn('patient', 'address_id');
		$this->dropColumn('patient', 'primary_phone');

		$this->dropColumn('contact', 'address_id');
		$this->dropColumn('contact', 'primary_phone');
		
		$this->dropTable('address');
    }
}
