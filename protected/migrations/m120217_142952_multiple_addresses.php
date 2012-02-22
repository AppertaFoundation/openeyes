<?php

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