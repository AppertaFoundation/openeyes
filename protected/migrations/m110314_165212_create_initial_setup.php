<?php
class m110314_165212_create_initial_setup extends CDbMigration
{
	public function up()
	{
		$this->createTable('authassignment', array(
			'itemname' => 'varchar(64)',
			'userid' => 'varchar(64)',
			'bizrule' => 'text',
			'data' => 'text',
			'PRIMARY KEY (`itemname`,`userid`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('authitem', array(
			'name' => 'varchar(64) NOT NULL',
			'type' => 'int(11) NOT NULL',
			'description' => 'text',
			'bizrule' => 'text',
			'data' => 'text',
			'PRIMARY KEY (`name`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;'
		);

		$this->createTable('authitemchild', array(
			'parent' => 'varchar(64) NOT NULL',
			'child' => 'varchar(64) NOT NULL',
			'PRIMARY KEY (`parent`,`child`)',
			'KEY `child` (`child`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('contact', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'nick_name' => 'varchar(80) DEFAULT NULL',
			'consultant' => "tinyint(1) NOT NULL DEFAULT '0'",
			'contact_type_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `contact_type_id` (`contact_type_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('contact_type', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) NOT NULL',
			'macro_only' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `name` (`name`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('country', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'code' => 'char(2) DEFAULT NULL',
			'name' => 'varchar(50) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `code` (`code`)',
			'UNIQUE KEY `name` (`name`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('diagnosis', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'patient_id' => 'int(10) unsigned NOT NULL',
			'user_id' => 'int(10) unsigned NOT NULL',
			'disorder_id' => 'int(10) unsigned NOT NULL',
			'datetime' => 'datetime NOT NULL',
			'site' => "tinyint(1) unsigned DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'KEY `patient_id` (`patient_id`)',
			'KEY `user_id` (`user_id`)',
			'KEY `disorder_id` (`disorder_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('disorder', array(
			'id' => 'int(10) unsigned NOT NULL',
			'fully_specified_name' => 'char(255) CHARACTER SET latin1 NOT NULL',
			'term' => 'char(255) CHARACTER SET latin1 NOT NULL',
			'systemic' => "tinyint(1) unsigned DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'KEY `term` (`term`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_anterior_segment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_anterior_segment_drawing', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_conclusion', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'value' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_cranial_nerves', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_extraocular_movements', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_gonioscopy', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_history', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'description' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_intraocular_pressure', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_mini_refraction', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_orbital_examination', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_past_history', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_posterior_segment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_posterior_segment_drawing', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_type', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
			'class_name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `name` (`name`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_visual_acuity', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_visual_fields', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_visual_function', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_poh', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'value' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_foh', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'value' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_pmh', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'value' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_allergies', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'value' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_social_history', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'value' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_medication', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'value' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('element_hpc', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_id' => 'int(10) unsigned NOT NULL',
			'value' => 'text',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `event_id` (`event_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('episode', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'patient_id' => 'int(10) unsigned NOT NULL',
			'firm_id' => 'int(10) unsigned DEFAULT NULL',
			'start_date' => 'datetime NOT NULL',
			'end_date' => 'datetime DEFAULT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `episode_1` (`patient_id`)',
			'KEY `episode_2` (`firm_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('event', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'episode_id' => 'int(10) unsigned NOT NULL',
			'user_id' => 'int(10) unsigned NOT NULL',
			'event_type_id' => 'int(10) unsigned NOT NULL',
			'datetime' => 'datetime NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `event_1` (`episode_id`)',
			'KEY `event_2` (`user_id`)',
			'KEY `event_3` (`event_type_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8'
		);

		$this->createTable('event_type', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'first_in_episode_possible' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `name` (`name`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('exam_phrase', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'part' => "int(10) DEFAULT '0'",
			'phrase' => 'varchar(80) COLLATE utf8_bin NOT NULL',
			'order' => "int(10) unsigned DEFAULT '0'",
			'PRIMARY KEY (`id`)',
			'KEY `specialty_id` (`specialty_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('firm', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'service_specialty_assignment_id' => 'int(10) unsigned NOT NULL',
			'pas_code' => 'char(4) COLLATE utf8_bin DEFAULT NULL',
			'name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `service_specialty_assignment_id` (`service_specialty_assignment_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('firm_user_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'firm_id' => 'int(10) unsigned NOT NULL',
			'user_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `firm_id` (`firm_id`)',
			'KEY `user_id` (`user_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8'
		);

		$this->createTable('letter_phrase', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'firm_id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(64) COLLATE utf8_bin DEFAULT NULL',
			'phrase' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
			'order' => "int(10) unsigned DEFAULT '0'",
			'section' => 'int(10) DEFAULT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `firm_id` (`firm_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('letter_template', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'name' => 'varchar(64) COLLATE utf8_bin DEFAULT NULL',
			'contact_type_id' => 'int(10) unsigned NOT NULL',
			'text' => 'text COLLATE utf8_bin',
			'cc' => 'varchar(128) COLLATE utf8_bin DEFAULT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `specialty_id` (`specialty_id`)',
			'KEY `contact_type_id` (`contact_type_id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('patient', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'pas_key' => 'int(10) unsigned DEFAULT NULL',
			'title' => 'varchar(8) COLLATE utf8_bin DEFAULT NULL',
			'first_name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'last_name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'dob' => 'date DEFAULT NULL',
			'gender' => 'char(1) COLLATE utf8_bin DEFAULT NULL',
			'hos_num' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'nhs_num' => 'varchar(40) COLLATE utf8_bin DEFAULT NULL',
			'PRIMARY KEY (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('possible_element_type', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'event_type_id' => 'int(10) unsigned NOT NULL',
			'element_type_id' => 'int(10) unsigned NOT NULL',
			'num_views' => "int(10) unsigned NOT NULL DEFAULT '1'",
			'order' => 'int(10) NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `event_type_id` (`event_type_id`)',
			'KEY `element_type_id` (`element_type_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8'
		);

		$this->createTable('service', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('service_specialty_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'service_id' => 'int(10) unsigned NOT NULL',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `service_id` (`service_id`)',
			'KEY `specialty_id` (`specialty_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8'
		);

		$this->createTable('site_element_type', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'possible_element_type_id' => 'int(10) unsigned NOT NULL',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'view_number' => 'int(10) unsigned NOT NULL',
			'required' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
			'first_in_episode' => "tinyint(1) unsigned DEFAULT '1'",
			'PRIMARY KEY (`id`)',
			'KEY `possible_element_type_id` (`possible_element_type_id`)',
			'KEY `specialty_id` (`specialty_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8'
		);

		$this->createTable('specialty', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('user', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'username' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'first_name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'last_name' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'email' => 'varchar(80) COLLATE utf8_bin NOT NULL',
			'active' => 'tinyint(1) NOT NULL',
			'password' => 'varchar(40) COLLATE utf8_bin NOT NULL',
			'salt' => 'varchar(10) COLLATE utf8_bin NOT NULL',
			'PRIMARY KEY (`id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->createTable('user_contact_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'user_id' => 'int(10) unsigned NOT NULL',
			'contact_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `user_id` (`user_id`)',
			'UNIQUE KEY `contact_id` (`contact_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8'
		);

		$this->addForeignKey(
			'contact_type_fk','contact','contact_type_id','contact_type','id');

		$this->addForeignKey(
			'patient_fk','diagnosis','patient_id','patient','id');
		$this->addForeignKey(
			'user_fk','diagnosis','user_id','user','id');
		$this->addForeignKey(
			'disorder_fk','diagnosis','disorder_id','disorder','id');

		$this->addForeignKey(
			'event_fk','element_history','event_id','event','id');

		$this->addForeignKey(
			'episode_1','episode','patient_id','patient','id');
		$this->addForeignKey(
			'episode_2','episode','firm_id','firm','id');

		$this->addForeignKey(
			'event_1','event','episode_id','episode','id');
		$this->addForeignKey(
			'event_2','event','user_id','user','id');
		$this->addForeignKey(
			'event_3','event','event_type_id','event_type','id');

		$this->addForeignKey(
			'specialty_id','exam_phrase','specialty_id','specialty','id');

		$this->addForeignKey(
			'service_specialty_assignment_id','firm','service_specialty_assignment_id','service_specialty_assignment','id');

		$this->addForeignKey(
			'firm_id','firm_user_assignment','firm_id','firm','id');
		$this->addForeignKey(
			'user_id','firm_user_assignment','user_id','user','id');

		$this->addForeignKey(
			'letter_phrase_ibfk_1','letter_phrase','firm_id','firm','id');

		$this->addForeignKey(
			'letter_template_ibfk_1','letter_template','specialty_id','specialty','id');
		$this->addForeignKey(
			'letter_template_ibfk_2','letter_template','contact_type_id','contact_type','id');

		$this->addForeignKey(
			'possible_element_type_ibfk_1','possible_element_type','event_type_id','event_type','id');
		$this->addForeignKey(
			'possible_element_type_ibfk_2','possible_element_type','element_type_id','element_type','id');

		$this->addForeignKey(
			'service_specialty_assignment_ibfk_1','service_specialty_assignment','service_id','service','id');
		$this->addForeignKey(
			'service_specialty_assignment_ibfk_2','service_specialty_assignment','specialty_id','specialty','id');

		$this->addForeignKey(
			'site_element_type_ibfk_1','site_element_type','possible_element_type_id','possible_element_type','id');
		$this->addForeignKey(
			'site_element_type_ibfk_2','site_element_type','specialty_id','specialty','id');

		$this->addForeignKey(
			'user_contact_assignment_ibfk_1','user_contact_assignment','user_id','user','id');
		$this->addForeignKey(
			'user_contact_assignment_ibfk_2','user_contact_assignment','contact_id','contact','id');

		$this->addForeignKey(
			'element_poh_1','element_poh','event_id','event','id');
		$this->addForeignKey(
			'element_foh_1','element_foh','event_id','event','id');
		$this->addForeignKey(
			'element_pmh_1','element_pmh','event_id','event','id');
		$this->addForeignKey(
			'element_allergies_1','element_allergies','event_id','event','id');
		$this->addForeignKey(
			'element_social_history_1','element_social_history','event_id','event','id');
		$this->addForeignKey(
			'element_medication_1','element_medication','event_id','event','id');
		$this->addForeignKey(
			'element_hpc_1','element_hpc','event_id','event','id');
		$this->addForeignKey(
			'element_conclusion_1','element_conclusion','event_id','event','id');
	}

	public function down()
	{
		$this->dropForeignKey('contact_type_fk','contact');

		$this->dropForeignKey('patient_fk','diagnosis');
		$this->dropForeignKey('user_fk','diagnosis');
		$this->dropForeignKey('disorder_fk','diagnosis');

		$this->dropForeignKey('event_fk','element_history');

		$this->dropForeignKey('episode_1','episode');
		$this->dropForeignKey('episode_2','episode');

		$this->dropForeignKey('event_1','event');
		$this->dropForeignKey('event_2','event');
		$this->dropForeignKey('event_3','event');

		$this->dropForeignKey('specialty_id','exam_phrase');

		$this->dropForeignKey('service_specialty_assignment_id','firm');

		$this->dropForeignKey('firm_id','firm_user_assignment');
		$this->dropForeignKey('user_id','firm_user_assignment');

		$this->dropForeignKey('letter_phrase_ibfk_1','letter_phrase');

		$this->dropForeignKey('letter_template_ibfk_1','letter_template');
		$this->dropForeignKey('letter_template_ibfk_2','letter_template');

		$this->dropForeignKey('possible_element_type_ibfk_1','possible_element_type');
		$this->dropForeignKey('possible_element_type_ibfk_2','possible_element_type');

		$this->dropForeignKey('service_specialty_assignment_ibfk_1','service_specialty_assignment');
		$this->dropForeignKey('service_specialty_assignment_ibfk_2','service_specialty_assignment');

		$this->dropForeignKey('site_element_type_ibfk_1','site_element_type');
		$this->dropForeignKey('site_element_type_ibfk_2','site_element_type');

		$this->dropForeignKey('user_contact_assignment_ibfk_1','user_contact_assignment');
		$this->dropForeignKey('user_contact_assignment_ibfk_2','user_contact_assignment');

		$this->dropForeignKey('element_poh_1','element_poh');
		$this->dropForeignKey('element_foh_1','element_foh');
		$this->dropForeignKey('element_pmh_1','element_pmh');
		$this->dropForeignKey('element_allergies_1','element_allergies');
		$this->dropForeignKey('element_social_history_1','element_social_history');
		$this->dropForeignKey('element_medication_1','element_medication');
		$this->dropForeignKey('element_hpc_1','element_medication');

		$this->dropTable('authassignment');
		$this->dropTable('authitem');
		$this->dropTable('authitemchild');
		$this->dropTable('contact');
		$this->dropTable('contact_type');
		$this->dropTable('country');
		$this->dropTable('diagnosis');
		$this->dropTable('disorder');
		$this->dropTable('element_anterior_segment');
		$this->dropTable('element_anterior_segment_drawing');
		$this->dropTable('element_conclusion');
		$this->dropTable('element_cranial_nerves');
		$this->dropTable('element_extraocular_movements');
		$this->dropTable('element_gonioscopy');
		$this->dropTable('element_history');
		$this->dropTable('element_intraocular_pressure');
		$this->dropTable('element_mini_refraction');
		$this->dropTable('element_orbital_examination');
		$this->dropTable('element_past_history');
		$this->dropTable('element_posterior_segment');
		$this->dropTable('element_posterior_segment_drawing');
		$this->dropTable('element_type');
		$this->dropTable('element_visual_acuity');
		$this->dropTable('element_visual_fields');
		$this->dropTable('element_visual_function');
		$this->dropTable('element_poh');
		$this->dropTable('element_foh');
		$this->dropTable('element_pmh');
		$this->dropTable('element_allergies');
		$this->dropTable('element_social_history');
		$this->dropTable('element_medication');
		$this->dropTable('element_hpc');
		$this->dropTable('episode');
		$this->dropTable('event');
		$this->dropTable('event_type');
		$this->dropTable('exam_phrase');
		$this->dropTable('firm');
		$this->dropTable('firm_user_assignment');
		$this->dropTable('letter_phrase');
		$this->dropTable('letter_template');
		$this->dropTable('patient');
		$this->dropTable('possible_element_type');
		$this->dropTable('service');
		$this->dropTable('service_specialty_assignment');
		$this->dropTable('site_element_type');
		$this->dropTable('specialty');
		$this->dropTable('user');
		$this->dropTable('user_contact_assignment');
	}
}