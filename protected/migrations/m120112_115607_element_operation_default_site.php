<?php

class m120112_115607_element_operation_default_site extends CDbMigration {
	
	public function up() {
		$this->alterColumn('element_operation','site_id','int(10) unsigned NOT NULL DEFAULT 0');
	}

	public function down() {
		$this->alterColumn('element_operation','site_id','int(10) unsigned NOT NULL DEFAULT 1');
	}

}