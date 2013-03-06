<?php

class m130306_105300_add_ethnic_group_to_patient extends OEMigration {
	public function up() {
		$this->addColumn('patient','ethnic_group','char(1)');
	}

	public function down() {
		$this->dropColumn('patient','ethnic_group');
	}
	
}
