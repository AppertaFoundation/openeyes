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

class m120217_142952_multiple_addresses extends CDbMigration {
	
	public function up() {
		$this->addColumn('address', 'parent_class', 'varchar(40) NOT NULL');
		$this->addColumn('address', 'parent_id', 'int(10) unsigned NOT NULL');
		$this->addColumn('address', 'type', 'char(1) NOT NULL');
		$this->addColumn('address', 'date_start', 'datetime');
		$this->addColumn('address', 'date_end', 'datetime');
		
		// Disable audit trail for address migration
		$audit_trail = Yii::app()->params['audit_trail'];
		Yii::app()->params['audit_trail'] = false;
		
		// Migrate existing patient address relationships
		echo "Migrating patient addresses...";
		$patients = Patient::model()->noPas()->findAll();
		foreach($patients as $patient) {
			$address = Address::model()->findByPk($patient->address_id);
			if($address) {
				$address->parent_class = 'Patient';
				$address->parent_id = $patient->id;
				$address->type = 'H';
				$address->save();
			}
		}
		echo "done.\n";
		$this->dropForeignKey('patient_address_id_fk', 'patient');
		$this->dropColumn('patient', 'address_id');

		// Migrate existing contact address relationships
		echo "Migrating contact addresses...";
		$contacts = Contact::model()->findAll();
		foreach($contacts as $contact) {
			$address = Address::model()->findByPk($contact->address_id);
			if($address) {
				$address->parent_class = 'Contact';
				$address->parent_id = $contact->id;
				$address->type = 'H';
				$address->save();
			}
		}
		echo "done.\n";
		$this->dropColumn('contact', 'address_id');
		
		Yii::app()->params['audit_trail'] = $audit_trail;
	}

	public function down() {
		echo "m120217_142952_multiple_addresses does not support migration down.\n";
		return false;
	}

	public function safeUp() {
		$this->up();
	}

	public function safeDown() {
		$this->down();
	}

}