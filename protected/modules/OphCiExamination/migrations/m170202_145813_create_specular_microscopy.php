<?php

class m170202_145813_create_specular_microscopy extends CDbMigration
{

    public function up()
    {

        // TODO: All datatypes below need to be checked.

        $this->insert('element_type', array('name' => 'Konan', 'display_order' => '1'));


        $this->createTable('et_ophciexamination_specular_microscopy', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
            'specular_microscope_id' => 'int(10) unsigned NOT NULL',
            'scan_quality_id' => 'int(10) unsigned NOT NULL',
            'endothelial_cell_density_value' => 'int(10) unsigned NOT NULL',
            'coefficient_variation_value' => 'decimal(5,2) NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `et_ophciexamination_specular_microscopy_lmui_fk` (`last_modified_user_id`)',
            'KEY `et_ophciexamination_specular_microscopy_cui_fk` (`created_user_id`)',
            'CONSTRAINT `et_ophciexamination_specular_microscopy_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophciexamination_specular_microscopy_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('ophciexamination_specular_microscope', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'name' => 'varchar NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `et_ophciexamination_specular_microscope_lmui_fk` (`last_modified_user_id`)',
            'KEY `et_ophciexamination_specular_microscope_cui_fk` (`created_user_id`)',
            'CONSTRAINT `et_ophciexamination_specular_microscope_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophciexamination_specular_microscope_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophciexamination_specular_microscope', array('name' => 'Konan', 'display_order' => '1'));
        $this->insert('ophciexamination_specular_microscope', array('name' => 'Topcon', 'display_order' => '2'));

        $this->createTable('ophciexamination_scan_quality', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'name' => 'varchar NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `ophciexamination_scan_quality_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophciexamination_scan_quality_cui_fk` (`created_user_id`)',
            'CONSTRAINT `ophciexamination_scan_quality_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophciexamination_scan_quality_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophciexamination_scan_quality', array('name' => 'Good', 'display_order' => '1'));
        $this->insert('ophciexamination_scan_quality', array('name' => 'Poor', 'display_order' => '2'));
        $this->insert('ophciexamination_scan_quality', array('name' => 'Failed', 'display_order' => '3'));
        $this->insert('ophciexamination_scan_quality', array('name' => 'Unknown', 'display_order' => '4'));

        $this->createTable('ophciexamination_topographer_device', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'name' => 'varchar NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `ophciexamination_topographer_device_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophciexamination_topographer_device_cui_fk` (`created_user_id`)',
            'CONSTRAINT `ophciexamination_topographer_device_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophciexamination_topographer_device_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophciexamination_topographer_device', array('name' => 'Pentacam', 'display_order' => '1'));
        $this->insert('ophciexamination_topographer_device', array('name' => 'Topcon', 'display_order' => '2'));
        $this->insert('ophciexamination_topographer_device', array('name' => 'Opticon', 'display_order' => '3'));
        $this->insert('ophciexamination_topographer_device', array('name' => 'Zeiss', 'display_order' => '4'));

        $this->createTable('ophciexamination_tomographer_device', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'name' => 'varchar NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `ophciexamination_tomographer_device_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophciexamination_tomographer_device_cui_fk` (`created_user_id`)',
            'CONSTRAINT `ophciexamination_tomographer_device_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophciexamination_tomographer_device_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophciexamination_tomographer_device', array('name' => 'Pentacam', 'display_order' => '1'));
        $this->insert('ophciexamination_tomographer_device', array('name' => 'RTVue', 'display_order' => '2'));
        $this->insert('ophciexamination_tomographer_device', array('name' => 'Visante', 'display_order' => '3'));
        $this->insert('ophciexamination_tomographer_device', array('name' => 'Casia', 'display_order' => '4'));


        $this->createTable('ophciexamination_keratoconus_stage', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'name' => 'varchar NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `ophciexamination_keratoconus_stage_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophciexamination_keratoconus_stage_cui_fk` (`created_user_id`)',
            'CONSTRAINT `ophciexamination_keratoconus_stage_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophciexamination_keratoconus_stage_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophciexamination_keratoconus_stage', array('name' => 'I', 'display_order' => '1'));
        $this->insert('ophciexamination_keratoconus_stage', array('name' => 'II', 'display_order' => '2'));
        $this->insert('ophciexamination_keratoconus_stage', array('name' => 'III', 'display_order' => '3'));
        $this->insert('ophciexamination_keratoconus_stage', array('name' => 'IV', 'display_order' => '4'));


        $this->createTable('et_ophciexamination_keratometry', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
            'topographer_id' => 'int(10) unsigned NOT NULL',
            'topographer_scan_quality_id' => 'int(10) unsigned NOT NULL',
            'anterior_k1_value' => 'int(10) unsigned NOT NULL',
            'axis_anterior_k1_value' => 'decimal(5,2) NOT NULL',
            'anterior_k2_value' => 'int(10) unsigned NOT NULL',
            'axis_anterior_k2_value' => 'int(10) unsigned NOT NULL',
            'kmax_value' => 'int(10) unsigned NOT NULL',
            'tomographer_id' => 'decimal(5,2) NOT NULL',
            'tomographer_scan_quality_id' => 'int(10) unsigned NOT NULL',
            'posterior_k2_value' => 'int(10) unsigned NOT NULL',
            'thinnest_point_pachymetry_value' => 'int(10) unsigned NOT NULL',
            'b-a_index_value' => 'decimal(5,2) NOT NULL',
            'keratoconus_stage_id' => 'int(10) unsigned NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `et_ophciexamination_keratometry_lmui_fk` (`last_modified_user_id`)',
            'KEY `et_ophciexamination_keratometry_cui_fk` (`created_user_id`)',
            'CONSTRAINT `et_ophciexamination_keratometry_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophciexamination_keratometry_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');


//        $this->insert('ophciexamination_oct_fluidstatus', array('name' => 'New', 'display_order' => '1'));
//        $this->insert('ophciexamination_oct_fluidstatus', array('name' => 'Improving', 'display_order' => '2'));
//        $this->insert('ophciexamination_oct_fluidstatus', array('name' => 'Persistent', 'display_order' => '3'));


    }

    public function down()
    {
        $this->dropTable('et_ophciexamination_specular_microscopy');

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