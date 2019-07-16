<?php

class m180315_152657_drop_fife_specific_tables extends OEMigration
{
    public function up()
    {

        $el_personnel_id = $this->dbConnection->createCommand()->select('id')
            ->from('element_type')->where('class_name = "Element_OphTrOperationnote_Personnel"')->queryScalar();

        $el_preparation_id = $this->dbConnection->createCommand()->select('id')
            ->from('element_type')->where('class_name = "Element_OphTrOperationnote_Preparation"')->queryScalar();

        $this->delete('setting_metadata', 'element_type_id IN (' . "$el_personnel_id, $el_preparation_id" . ')');
        $this->delete('setting_firm', 'element_type_id IN (' . "$el_personnel_id, $el_preparation_id" . ')');
        $this->delete('setting_installation', 'element_type_id IN (' . "$el_personnel_id, $el_preparation_id" . ')');
        $this->delete('setting_institution', 'element_type_id IN (' . "$el_personnel_id, $el_preparation_id" . ')');
        $this->delete('setting_internal_referral', 'element_type_id IN (' . "$el_personnel_id, $el_preparation_id" . ')');
        $this->delete('setting_site', 'element_type_id IN (' . "$el_personnel_id, $el_preparation_id" . ')');
        $this->delete('setting_specialty', 'element_type_id IN (' . "$el_personnel_id, $el_preparation_id" . ')');
        $this->delete('setting_subspecialty', 'element_type_id IN (' . "$el_personnel_id, $el_preparation_id" . ')');
        $this->delete('setting_user', 'element_type_id IN (' . "$el_personnel_id, $el_preparation_id" . ')');

        $this->dropOETable('et_ophtroperationnote_preparation', true);
        $this->dropOETable('et_ophtroperationnote_personnel', true);

        $this->dropOETable('ophtroperationnote_preparation_intraocular_solution', true);
        $this->dropOETable('ophtroperationnote_preparation_skin_preparation', true);

        $this->delete('element_type', 'class_name = :cn', array(':cn' => 'Element_OphTrOperationnote_Personnel'));
        $this->delete('element_type', 'class_name = :cn', array(':cn' => 'Element_OphTrOperationnote_Preparation'));
    }

    public function down()
    {
        $this->execute("CREATE TABLE `ophtroperationnote_preparation_intraocular_solution` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128)  DEFAULT NULL,
			  `display_order` tinyint(3) unsigned DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_pis_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_pis_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_pis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_pis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
        $this->versionExistingTable('ophtroperationnote_preparation_intraocular_solution');

        $this->execute("CREATE TABLE `ophtroperationnote_preparation_skin_preparation` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128)  DEFAULT NULL,
			  `display_order` tinyint(3) unsigned DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_psp_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_psp_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_psp_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_psp_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
        $this->versionExistingTable('ophtroperationnote_preparation_skin_preparation');

        $this->execute("CREATE TABLE `et_ophtroperationnote_preparation` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `spo2` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `oxygen` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `pulse` smallint(2) unsigned NOT NULL DEFAULT '0',
			  `skin_preparation_id` int(10) unsigned NOT NULL,
			  `intraocular_solution_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_preparation_event_id_fk` (`event_id`),
			  KEY `et_ophtroperationnote_preparation_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_preparation_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_preparation_skin_preparation_id_fk` (`skin_preparation_id`),
			  KEY `et_ophtroperationnote_preparation_intraocular_solution_id_fk` (`intraocular_solution_id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_skin_preparation_id_fk` FOREIGN KEY (`skin_preparation_id`) REFERENCES `ophtroperationnote_preparation_skin_preparation` (`id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_intraocular_solution_id_fk` FOREIGN KEY (`intraocular_solution_id`) REFERENCES `ophtroperationnote_preparation_intraocular_solution` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
        $this->versionExistingTable('et_ophtroperationnote_preparation');

        $this->execute("CREATE TABLE `et_ophtroperationnote_personnel` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `scrub_nurse_id` int(10) unsigned NOT NULL,
			  `floor_nurse_id` int(10) unsigned NOT NULL,
			  `accompanying_nurse_id` int(10) unsigned NOT NULL,
			  `operating_department_practitioner_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_p_event_id_fk` (`event_id`),
			  KEY `et_ophtroperationnote_p_scrub_nurse_id_fk` (`scrub_nurse_id`),
			  KEY `et_ophtroperationnote_p_floor_nurse_id_fk` (`floor_nurse_id`),
			  KEY `et_ophtroperationnote_p_accompanying_nurse_id_fk` (`accompanying_nurse_id`),
			  KEY `et_ophtroperationnote_p_operating_department_practitioner_id_fk` (`operating_department_practitioner_id`),
			  KEY `et_ophtroperationnote_p_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_p_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `et_ophtroperationnote_p_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_scrub_nurse_id_fk` FOREIGN KEY (`scrub_nurse_id`) REFERENCES `contact` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_floor_nurse_id_fk` FOREIGN KEY (`floor_nurse_id`) REFERENCES `contact` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_accompanying_nurse_id_fk` FOREIGN KEY (`accompanying_nurse_id`) REFERENCES `contact` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_operating_department_practitioner_id_fk` FOREIGN KEY (`operating_department_practitioner_id`) REFERENCES `contact` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
        $this->versionExistingTable('et_ophtroperationnote_personnel');

        $parent_element_type_id = $this->dbConnection->createCommand()->select('id')
            ->from('element_type')->where('class_name = "Element_OphTrOperationnote_ProcedureList"')->queryScalar();

        $event_type_id = $this->dbConnection->createCommand()->select('id')
            ->from('event_type')->where('name = "Operation Note"')->queryScalar();

        $this->insert('element_type',[
            'name' => 'Personnel',
            'class_name' => 'Element_OphTrOperationnote_Personnel',
            'event_type_id' => $event_type_id,
            'display_order' => 45,
            'default' => 0,
            'parent_element_type_id' => $parent_element_type_id
        ]);
        $this->insert('element_type',[
            'name' => 'Preparation',
            'class_name' => 'Element_OphTrOperationnote_Preparation',
            'event_type_id' => $event_type_id,
            'display_order' => 15,
            'default' => 1,
        ]);


    }
}