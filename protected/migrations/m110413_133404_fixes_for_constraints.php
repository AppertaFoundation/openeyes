<?php

class m110413_133404_fixes_for_constraints extends CDbMigration
{
	public function up()
	{
		$this->dropTable('element_anterior_segment_drawing');
		$this->dropTable('element_posterior_segment_drawing');

                $this->insert('address', array(
                        'address1' => 'flat 1',
                        'address2' => 'bleakley creek',
                        'city' => 'flitchley',
                        'postcode' => 'ec1v 0dx',
                        'county' => 'london',
                        'country_id' => 1,
                        'email' => 'bleakley1@bleakley1.com'
                ));
                $this->insert('address', array(
                        'address1' => 'flat 2',
                        'address2' => 'bleakley creek',
                        'city' => 'flitchley',
                        'postcode' => 'ec1v 0dx',
                        'county' => 'london',
                        'country_id' => 1,
                        'email' => 'bleakley2@bleakley2.com'
                ));
                $this->insert('address', array(
                        'address1' => 'flat 3',
                        'address2' => 'bleakley creek',
                        'city' => 'flitchley',
                        'postcode' => 'ec1v 0dx',
                        'county' => 'london',
                        'country_id' => 1,
                        'email' => 'bleakley3@bleakley3.com'
                ));

		$address1 = $this->dbConnection->createCommand()->select('id')->from('address')->where('email=:email', array(':email'=>'bleakley1@bleakley1.com'))->queryRow();
		$address2 = $this->dbConnection->createCommand()->select('id')->from('address')->where('email=:email', array(':email'=>'bleakley2@bleakley2.com'))->queryRow();
		$address3 = $this->dbConnection->createCommand()->select('id')->from('address')->where('email=:email', array(':email'=>'bleakley3@bleakley3.com'))->queryRow();

		$this->update('patient', array('address_id'=>$address1['id']), "id=1");
		$this->update('patient', array('address_id'=>$address2['id']), "id=2");
		$this->update('patient', array('address_id'=>$address3['id']), "id=3");

		$this->addForeignKey('address_country_id_fk', 'address', 'country_id', 'country', 'id');
		$this->addForeignKey('patient_address_id_fk', 'patient', 'address_id', 'address', 'id');

		$this->addForeignKey('element_nsc_grade_event_fk', 'element_nsc_grade', 'event_id', 'event', 'id');
		$this->addForeignKey('element_nsc_grade_retinopathy_grade_id_fk', 'element_nsc_grade', 'retinopathy_grade_id', 'nsc_grade', 'id');
		$this->addForeignKey('element_nsc_grade_maculopathy_grade_id_fk', 'element_nsc_grade', 'maculopathy_grade_id', 'nsc_grade', 'id');

		$this->addForeignKey('element_anterior_segment_event_fk', 'element_anterior_segment', 'event_id', 'event', 'id');
		$this->addForeignKey('element_cranial_nerves_event_fk', 'element_cranial_nerves', 'event_id', 'event', 'id');
		$this->addForeignKey('element_diabetes_type_event_fk', 'element_diabetes_type', 'event_id', 'event', 'id');
		$this->addForeignKey('element_extraocular_movements_event_fk', 'element_extraocular_movements', 'event_id', 'event', 'id');
		$this->addForeignKey('element_gonioscopy_event_fk', 'element_gonioscopy', 'event_id', 'event', 'id');
		$this->addForeignKey('element_intraocular_pressure_event_fk', 'element_intraocular_pressure', 'event_id', 'event', 'id');
		$this->addForeignKey('element_mini_refraction_event_fk', 'element_mini_refraction', 'event_id', 'event', 'id');
		$this->addForeignKey('element_orbital_examination_event_fk', 'element_orbital_examination', 'event_id', 'event', 'id');
		$this->addForeignKey('element_past_history_event_fk', 'element_past_history', 'event_id', 'event', 'id');
		$this->addForeignKey('element_posterior_segment_event_fk', 'element_posterior_segment', 'event_id', 'event', 'id');
		$this->addForeignKey('element_referred_from_screening_event_fk', 'element_referred_from_screening', 'event_id', 'event', 'id');
		$this->addForeignKey('element_registered_blind_event_fk', 'element_registered_blind', 'event_id', 'event', 'id');
		$this->addForeignKey('element_visual_fields_event_fk', 'element_visual_fields', 'event_id', 'event', 'id');
		$this->addForeignKey('element_visual_function_event_fk', 'element_visual_function', 'event_id', 'event', 'id');
	}

	public function down()
	{
		$this->createTable('element_anterior_segment_drawing', array(
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

		$this->dropForeignKey('element_anterior_segment_event_fk','element_anterior_segment');
		$this->dropForeignKey('element_cranial_nerves_event_fk','element_cranial_nerves');
		$this->dropForeignKey('element_diabetes_type_event_fk','element_diabetes_type');
		$this->dropForeignKey('element_extraocular_movements_event_fk','element_extraocular_movements');
		$this->dropForeignKey('element_gonioscopy_event_fk','element_gonioscopy');
		$this->dropForeignKey('element_intraocular_pressure_event_fk','element_intraocular_pressure');
		$this->dropForeignKey('element_mini_refraction_event_fk','element_mini_refraction');
		$this->dropForeignKey('element_orbital_examination_event_fk','element_orbital_examination');
		$this->dropForeignKey('element_past_history_event_fk','element_past_history');
		$this->dropForeignKey('element_posterior_segment_event_fk','element_posterior_segment');
		$this->dropForeignKey('element_referred_from_screening_event_fk','element_referred_from_screening');
		$this->dropForeignKey('element_registered_blind_event_fk','element_registered_blind');
		$this->dropForeignKey('element_visual_fields_event_fk','element_visual_fields');
		$this->dropForeignKey('element_visual_function_event_fk','element_visual_function');

		$this->dropForeignKey('address_country_id_fk','address');
		$this->dropForeignKey('patient_address_id_fk','patient');

		$this->dropForeignKey('element_nsc_grade_event_fk','element_nsc_grade');
		$this->dropForeignKey('element_nsc_grade_retinopathy_grade_id_fk','element_nsc_grade');
		$this->dropForeignKey('element_nsc_grade_maculopathy_grade_id_fk','element_nsc_grade');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
