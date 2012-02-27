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

class m120106_183243_last_modified_and_created_fields_for_date_letter_sent_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('date_letter_sent','last_modified_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('date_letter_sent','last_modified_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
		$this->addForeignKey('date_letter_sent_last_modified_user_id_fk','date_letter_sent','last_modified_user_id','user','id');
		$this->addColumn('date_letter_sent','created_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('date_letter_sent_created_user_id_fk','date_letter_sent','created_user_id','user','id');
		$this->addColumn('date_letter_sent','created_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
	}

	public function down()
	{
		$this->dropForeignKey('date_letter_sent_created_user_id_fk','date_letter_sent');
		$this->dropForeignKey('date_letter_sent_last_modified_user_id_fk','date_letter_sent');
		$this->dropColumn('date_letter_sent','created_date');
		$this->dropColumn('date_letter_sent','created_user_id');
		$this->dropColumn('date_letter_sent','last_modified_date');
		$this->dropColumn('date_letter_sent','last_modified_user_id');
	}
}
