<?php

class m140203_150454_event_type_OphInBiometry extends CDbMigration
{
    public function up()
    {
        if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBiometry'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $this->insert('event_type', array('class_name' => 'OphInBiometry', 'name' => 'Biometry', 'event_group_id' => $group['id']));
        }
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBiometry'))->queryRow();

        if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name' => 'Biometry Data', ':eventTypeId' => $event_type['id']))->queryRow()) {
            $this->insert('element_type', array('name' => 'Biometry Data', 'class_name' => 'Element_OphInBiometry_BiometryData', 'event_type_id' => $event_type['id'], 'display_order' => 1));
        }

        $element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and name=:name', array(':eventTypeId' => $event_type['id'], ':name' => 'Biometry Data'))->queryRow();
        if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name' => 'Lens Type', ':eventTypeId' => $event_type['id']))->queryRow()) {
            $this->insert('element_type', array('name' => 'Lens Type', 'class_name' => 'Element_OphInBiometry_LensType', 'event_type_id' => $event_type['id'], 'display_order' => 1));
        }

        $element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and name=:name', array(':eventTypeId' => $event_type['id'], ':name' => 'Lens Type'))->queryRow();
        if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name' => 'Calculation', ':eventTypeId' => $event_type['id']))->queryRow()) {
            $this->insert('element_type', array('name' => 'Calculation', 'class_name' => 'Element_OphInBiometry_Calculation', 'event_type_id' => $event_type['id'], 'display_order' => 1));
        }

        $element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and name=:name', array(':eventTypeId' => $event_type['id'], ':name' => 'Calculation'))->queryRow();
        if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name' => 'Lens Selection', ':eventTypeId' => $event_type['id']))->queryRow()) {
            $this->insert('element_type', array('name' => 'Selection', 'class_name' => 'Element_OphInBiometry_Selection', 'event_type_id' => $event_type['id'], 'display_order' => 1));
        }

        $element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and name=:name', array(':eventTypeId' => $event_type['id'], ':name' => 'Lens Selection'))->queryRow();

        $this->createTable('ophinbiometry_measurement', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',

                'last_name' => 'varchar(200)',
                'first_name' => 'varchar(200)',
                'middle_name' => 'varchar(200)',
                'name_prefix' => 'varchar(200)',
                'name_suffix' => 'varchar(200)',
                'patient_id' => 'varchar(200)',
                'patients_birth_date' => 'varchar(200)',
                'patients_comment' => 'varchar(200)',
                'patients_priv_id' => 'varchar(200)',
                'measurement_date' => 'varchar(200)',
                'r_sphere' => 'varchar(200)',
                'r_cylinder' => 'varchar(200)',
                'r_axis' => 'varchar(200)',
                'r_visual_acuity' => 'varchar(200)',
                'r_eye_state' => 'varchar(200)',
                'r_axial_length_mean' => 'varchar(200)',
                'r_axial_length_cnt' => 'varchar(200)',
                'r_axial_length_std' => 'varchar(200)',
                'r_axial_length_changed' => 'varchar(200)',
                'r_radius_se_mean' => 'varchar(200)',
                'r_radius_se_cnt' => 'varchar(200)',
                'r_radius_se_std' => 'varchar(200)',
                'r_radius_r1' => 'varchar(200)',
                'r_radius_r2' => 'varchar(200)',
                'r_radius_r1_axis' => 'varchar(200)',
                'r_radius_r2_axis' => 'varchar(200)',
                'r_acd_mean' => 'varchar(200)',
                'r_acd_cnt' => 'varchar(200)',
                'r_acd_std' => 'varchar(200)',
                'r_wtw_mean' => 'varchar(200)',
                'r_wtw_cnt' => 'varchar(200)',
                'r_wtw_std' => 'varchar(200)',
                'l_sphere' => 'varchar(200)',
                'l_cylinder' => 'varchar(200)',
                'l_axis' => 'varchar(200)',
                'l_visual_acuity' => 'varchar(200)',
                'l_eye_state' => 'varchar(200)',
                'l_axial_length_mean' => 'varchar(200)',
                'l_axial_length_cnt' => 'varchar(200)',
                'l_axial_length_std' => 'varchar(200)',
                'l_axial_length_changed' => 'varchar(200)',
                'l_radius_se_mean' => 'varchar(200)',
                'l_radius_se_cnt' => 'varchar(200)',
                'l_radius_se_std' => 'varchar(200)',
                'l_radius_r1' => 'varchar(200)',
                'l_radius_r2' => 'varchar(200)',
                'l_radius_r1_axis' => 'varchar(200)',
                'l_radius_r2_axis' => 'varchar(200)',
                'l_acd_mean' => 'varchar(200)',
                'l_acd_cnt' => 'varchar(200)',
                'l_acd_std' => 'varchar(200)',
                'l_wtw_mean' => 'varchar(200)',
                'l_wtw_cnt' => 'varchar(200)',
                'l_wtw_std' => 'varchar(200)',
                'refractive_index' => 'varchar(200)',
                'iol_machine_id' => 'varchar(200)',
                'iol_poll_id' => 'varchar(200)',

                'PRIMARY KEY (`id`)',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'KEY `ophinbiometry_measurementt_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophinbiometry_measurement_fk` (`created_user_id`)',
                'CONSTRAINT `ophinbiometry_measurementt_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophinbiometry_measurement_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('et_ophinbiometry_biometrydat', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT \'3\'',

                'axial_length_left' => 'decimal (5, 2) NOT NULL', // Axial Length
                'r1_left' => 'decimal (5, 2) NOT NULL', // R1
                'r2_left' => 'decimal (5, 2) NOT NULL', // R2
                'r1_axis_left' => 'int(10) unsigned',
                'r2_axis_left' => 'int(10) unsigned',
                'acd_left' => 'decimal (5, 2) NOT NULL',
                'scleral_thickness_left' => 'decimal (5, 2) NOT NULL',

                'axial_length_right' => 'decimal (5, 2) NOT NULL', // Axial Length
                'r1_right' => 'decimal (5, 2) NOT NULL', // R1
                'r2_right' => 'decimal (5, 2) NOT NULL', // R2
                'r1_axis_right' => 'int(10) unsigned',
                'r2_axis_right' => 'int(10) unsigned',
                'acd_right' => 'decimal (5, 2) NOT NULL',
                'scleral_thickness_right' => 'decimal (5, 2) NOT NULL',

                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophinbiometry_biometrydat_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophinbiometry_biometrydat_cui_fk` (`created_user_id`)',
                'KEY `et_ophinbiometry_biometrydat_ev_fk` (`event_id`)',
                'CONSTRAINT `et_ophinbiometry_biometrydat_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbiometry_biometrydat_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbiometry_biometrydat_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `et_ophinbiometry_biometrydat_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('ophinbiometry_lenstype_lens', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(128) NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'PRIMARY KEY (`id`)',
                'KEY `ophinbiometry_lenstype_lens_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophinbiometry_lenstype_lens_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophinbiometry_lenstype_lens_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophinbiometry_lenstype_lens_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophinbiometry_lenstype_lens', array('name' => 'MA60AC', 'display_order' => 1));
        $this->insert('ophinbiometry_lenstype_lens', array('name' => 'SN60WF', 'display_order' => 2));

        $this->createTable('et_ophinbiometry_lenstype', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT \'3\'',

                'lens_id_left' => 'int(10) unsigned NOT NULL DEFAULT 1', // Len
                'lens_id_right' => 'int(10) unsigned NOT NULL DEFAULT 1', // Len

                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophinbiometry_lenstype_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophinbiometry_lenstype_cui_fk` (`created_user_id`)',
                'KEY `et_ophinbiometry_lenstype_ev_fk` (`event_id`)',
                'KEY `ophinbiometry_lenstype_lens_l_fk` (`lens_id_left`)',
                'KEY `ophinbiometry_lenstype_lens_r_fk` (`lens_id_right`)',
                'CONSTRAINT `et_ophinbiometry_lenstype_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbiometry_lenstype_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbiometry_lenstype_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `ophinbiometry_lenstype_lens_l_fk` FOREIGN KEY (`lens_id_left`) REFERENCES `ophinbiometry_lenstype_lens` (`id`)',
                'CONSTRAINT `ophinbiometry_lenstype_lens_r_fk` FOREIGN KEY (`lens_id_right`) REFERENCES `ophinbiometry_lenstype_lens` (`id`)',
                'CONSTRAINT `et_ophinbiometry_lenstype_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('ophinbiometry_calculation_formula', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(128) NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'PRIMARY KEY (`id`)',
                'KEY `ophinbiometry_calculation_formula_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophinbiometry_calculation_formula_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophinbiometry_calculation_formula_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophinbiometry_calculation_formula_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophinbiometry_calculation_formula', array('name' => 'SRK/T', 'display_order' => 1));
        $this->insert('ophinbiometry_calculation_formula', array('name' => 'Holladay 1', 'display_order' => 2));

        $this->createTable('et_ophinbiometry_calculation', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT \'3\'',

                'target_refraction_left' => 'varchar(255) DEFAULT \'\'', // Target Refraction
                'target_refraction_right' => 'varchar(255) DEFAULT \'\'', // Target Refraction
                'formula_id_left' => 'int(10) unsigned NOT NULL DEFAULT 1', // formula
                'formula_id_right' => 'int(10) unsigned NOT NULL DEFAULT 1', // formula

                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophinbiometry_calculation_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophinbiometry_calculation_cui_fk` (`created_user_id`)',
                'KEY `et_ophinbiometry_calculation_ev_fk` (`event_id`)',
                'KEY `ophinbiometry_calculation_formula_l_fk` (`formula_id_left`)',
                'KEY `ophinbiometry_calculation_formula_r_fk` (`formula_id_right`)',
                'CONSTRAINT `et_ophinbiometry_calculation_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbiometry_calculation_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbiometry_calculation_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `ophinbiometry_calculation_formula_l_fk` FOREIGN KEY (`formula_id_left`) REFERENCES `ophinbiometry_calculation_formula` (`id`)',
                'CONSTRAINT `ophinbiometry_calculation_formula_r_fk` FOREIGN KEY (`formula_id_right`) REFERENCES `ophinbiometry_calculation_formula` (`id`)',
                'CONSTRAINT `et_ophinbiometry_calculation_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('et_ophinbiometry_calculation_version', array(
                'id' => 'int(10) unsigned NOT NULL',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT \'3\'',

                'target_refraction_left' => 'varchar(255) DEFAULT \'\'', // Target Refraction
                'target_refraction_right' => 'varchar(255) DEFAULT \'\'', // Target Refraction
                'formula_id_left' => 'int(10) unsigned NOT NULL DEFAULT 1', // formula
                'formula_id_right' => 'int(10) unsigned NOT NULL DEFAULT 1', // formula

                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
                'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'PRIMARY KEY (`version_id`)',
                'KEY `acv_et_ophinbiometry_calculation_lmui_fk` (`last_modified_user_id`)',
                'KEY `acv_et_ophinbiometry_calculation_cui_fk` (`created_user_id`)',
                'KEY `acv_et_ophinbiometry_calculation_ev_fk` (`event_id`)',
                'KEY `et_ophinbiometry_calculation_aid_fk` (`id`)',
                'KEY `acv_ophinbiometry_calculation_formula_l_fk` (`formula_id_left`)',
                'KEY `acv_ophinbiometry_calculation_formula_r_fk` (`formula_id_right`)',
                'CONSTRAINT `acv_et_ophinbiometry_calculation_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `acv_et_ophinbiometry_calculation_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `acv_et_ophinbiometry_calculation_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `et_ophinbiometry_calculation_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_ophinbiometry_calculation` (`id`)',
                'CONSTRAINT `acv_ophinbiometry_calculation_formula_l_fk` FOREIGN KEY (`formula_id_left`) REFERENCES `ophinbiometry_calculation_formula` (`id`)',
                'CONSTRAINT `acv_ophinbiometry_calculation_formula_r_fk` FOREIGN KEY (`formula_id_right`) REFERENCES `ophinbiometry_calculation_formula` (`id`)',
                'CONSTRAINT `avc_ophinbiometry_calculation_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('et_ophinbiometry_selection', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT \'3\'',

                'iol_power_left' => 'varchar(255) DEFAULT \'\'', // IOL Power
                'predicted_refraction_left' => 'varchar(255) DEFAULT \'\'', // Predicted Refraction
                'iol_power_right' => 'varchar(255) DEFAULT \'\'', // IOL Power
                'predicted_refraction_right' => 'varchar(255) DEFAULT \'\'', // Predicted Refraction

                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophinbiometry_selection_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophinbiometry_selection_cui_fk` (`created_user_id`)',
                'KEY `et_ophinbiometry_selection_ev_fk` (`event_id`)',
                'CONSTRAINT `et_ophinbiometry_selection_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbiometry_selection_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbiometry_selection_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `et_ophinbiometry_selection_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('et_ophinbiometry_selection_version', array(
                'id' => 'int(10) unsigned NOT NULL',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT \'3\'',

                'iol_power_left' => 'varchar(255) DEFAULT \'\'', // IOL Power
                'predicted_refraction_left' => 'varchar(255) DEFAULT \'\'', // Predicted Refraction
                'iol_power_right' => 'varchar(255) DEFAULT \'\'', // IOL Power
                'predicted_refraction_right' => 'varchar(255) DEFAULT \'\'', // Predicted Refraction

                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'deleted' => 'tinyint(1) unsigned not null',
                'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
                'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'PRIMARY KEY (`version_id`)',
                'KEY `acv_et_ophinbiometry_selection_lmui_fk` (`last_modified_user_id`)',
                'KEY `acv_et_ophinbiometry_selection_cui_fk` (`created_user_id`)',
                'KEY `acv_et_ophinbiometry_selection_ev_fk` (`event_id`)',
                'KEY `et_ophinbiometry_selection_aid_fk` (`id`)',
                'CONSTRAINT `acv_et_ophinbiometry_selection_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `acv_et_ophinbiometry_selection_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `acv_et_ophinbiometry_selection_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `avc_et_ophinbiometry_selection_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
                'CONSTRAINT `et_ophinbiometry_selection_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_ophinbiometry_selection` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down()
    {
        $this->dropTable('et_ophinbiometry_biometrydat_version');
        $this->dropTable('et_ophinbiometry_biometrydat');

        $this->dropTable('et_ophinbiometry_lenstype_version');
        $this->dropTable('et_ophinbiometry_lenstype');

        $this->dropTable('ophinbiometry_lenstype_lens_version');
        $this->dropTable('ophinbiometry_lenstype_lens');

        $this->dropTable('et_ophinbiometry_calculation_version');
        $this->dropTable('et_ophinbiometry_calculation');

        $this->dropTable('ophinbiometry_calculation_formula_version');
        $this->dropTable('ophinbiometry_calculation_formula');

        $this->dropTable('et_ophinbiometry_selection_version');
        $this->dropTable('et_ophinbiometry_selection');

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBiometry'))->queryRow();

        foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id' => $event_type['id']))->queryAll() as $row) {
            $this->delete('audit', 'event_id='.$row['id']);
            $this->delete('event', 'id='.$row['id']);
        }

        $this->delete('element_type', 'event_type_id='.$event_type['id']);
        $this->delete('event_type', 'id='.$event_type['id']);
    }
}
