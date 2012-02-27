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

class m120106_171416_date_letter_sent_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('date_letter_sent',array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'element_operation_id' => 'int(10) unsigned NOT NULL',
			'date_invitation_letter_sent' => 'datetime NULL',
			'date_1st_reminder_letter_sent' => 'datetime NULL',
			'date_2nd_reminder_letter_sent' => 'datetime NULL',
			'date_gp_letter_sent' => 'datetime NULL',
			'date_scheduling_letter_sent' => 'datetime NULL',
			'PRIMARY KEY (`id`)',
			'KEY (`element_operation_id`)'
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->addForeignKey('date_letter_sent_element_operation_fk','date_letter_sent','element_operation_id','element_operation','id');
	}

	public function down()
	{
		$this->dropForeignKey('date_letter_sent_element_operation_fk','date_letter_sent');
		$this->dropTable('date_letter_sent');
	}
}
