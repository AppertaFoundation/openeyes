<?php

class m130604_093335_patient_shortcode_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('patient_shortcode', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_type_id' => 'int(10) unsigned NULL',
				'default_code' => 'varchar(3) COLLATE utf8_bin NOT NULL',
				'code' => 'varchar(3) COLLATE utf8_bin NOT NULL',
				'method' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'description' => 'varchar(1024) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `patient_shortcode_event_type_id_fk` (`event_type_id`)',
				'KEY `patient_shortcode_lmui_fk` (`last_modified_user_id`)',
				'KEY `patient_shortcode_cui_fk` (`created_user_id`)',
				'CONSTRAINT `patient_shortcode_event_type_id_fk` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`)',
				'CONSTRAINT `patient_shortcode_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `patient_shortcode_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'age','code'=>'age','description'=>'Patient age'));
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'sub','code'=>'sub','description'=>'Patient as subject'));
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'pro','code'=>'pro','description'=>'Patient pronoun'));
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'epd','code'=>'epd','description'=>'Principal diagnosis for episode'));
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'eps','code'=>'eps','description'=>'Principal side for episode'));
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'obj','code'=>'obj','description'=>'Patient as object'));
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'pos','code'=>'pos','description'=>'Patient possessive'));
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'sdl','code'=>'sdl','description'=>'List of secondary diagnoses for patient'));
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'epc','code'=>'epc','description'=>'Consultant for the episode of the current subspecialty'));
		$this->insert('patient_shortcode',array('event_type_id'=>null,'default_code'=>'epv','code'=>'epv','description'=>'Service for the episode of the current subspecialty'));
	}

	public function down()
	{
		$this->dropTable('patient_shortcode');
	}
}
