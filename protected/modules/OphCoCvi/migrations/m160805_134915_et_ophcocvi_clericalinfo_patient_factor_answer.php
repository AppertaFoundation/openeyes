<?php

class m160805_134915_et_ophcocvi_clericalinfo_patient_factor_answer extends CDbMigration
{
    public function up()
    {

        $this->createTable('et_ophcocvi_clericinfo_patient_factor_answer', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'element_id' => 'int(10) unsigned NOT NULL',
            'ophcocvi_clinicinfo_patient_factor_id' => 'int(10) unsigned NOT NULL',
            'is_factor' => 'varchar(20)',
            'comments' => 'text',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'deleted' => 'tinyint(1) unsigned not null',
            'PRIMARY KEY (`id`)',
            'KEY `et_ophcocvi_clericinfo_patient_factor_answer_lmui_fk` (`last_modified_user_id`)',
            'KEY `et_ophcocvi_clericinfo_patient_factor_answer_cui_fk` (`created_user_id`)',
            'KEY `et_ophcocvi_clericinfo_patient_factor_answer_ele_fk` (`element_id`)',
            'KEY `et_ophcocvi_clericinfo_patient_factor_answer_lku_fk` (`ophcocvi_clinicinfo_patient_factor_id`)',
            'CONSTRAINT `et_ophcocvi_clericinfo_patient_factor_answer_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophcocvi_clericinfo_patient_factor_answer_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophcocvi_clericinfo_patient_factor_answer_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcocvi_clericinfo` (`id`)',
            'CONSTRAINT `et_ophcocvi_clericinfo_patient_factor_answer_lku_fk` FOREIGN KEY (`ophcocvi_clinicinfo_patient_factor_id`) REFERENCES `ophcocvi_clinicinfo_patient_factor` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('et_ophcocvi_clericinfo_patient_factor_answer_version', array(
            'id' => 'int(10) unsigned NOT NULL',
            'element_id' => 'int(10) unsigned NOT NULL',
            'ophcocvi_clinicinfo_patient_factor_id' => 'int(10) unsigned NOT NULL',
            'is_factor' => 'varchar(20)',
            'comments' => 'text',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'deleted' => 'tinyint(1) unsigned not null',
            'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
            'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'PRIMARY KEY (`version_id`)',
            'KEY `acv_et_ophcocvi_clericinfo_patient_factor_answer_lmui_fk` (`last_modified_user_id`)',
            'KEY `acv_et_ophcocvi_clericinfo_patient_factor_answer_cui_fk` (`created_user_id`)',
            'KEY `acv_et_ophcocvi_clericinfo_patient_factor_answer_ele_fk` (`element_id`)',
            'KEY `acv_et_ophcocvi_clericinfo_patient_factor_answer_lku_fk` (`ophcocvi_clinicinfo_patient_factor_id`)',
            'KEY `et_ophcocvi_clericinfo_patient_factor_answer_aid_fk` (`id`)',
            'CONSTRAINT `acv_et_ophcocvi_clericinfo_patient_factor_answer_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `acv_et_ophcocvi_clericinfo_patient_factor_answer_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `acv_et_ophcocvi_clericinfo_patient_factor_answer_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcocvi_clericinfo` (`id`)',
            'CONSTRAINT `acv_et_ophcocvi_clericinfo_patient_factor_answer_lku_fk` FOREIGN KEY (`ophcocvi_clinicinfo_patient_factor_id`) REFERENCES `ophcocvi_clinicinfo_patient_factor` (`id`)',
            'CONSTRAINT `et_ophcocvi_clericinfo_patient_factor_answer_aid_fk` FOREIGN KEY (`id`) REFERENCES `et_ophcocvi_clericinfo_patient_factor_answer` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

    }

    public function down()
    {
        $this->dropTable('et_ophcocvi_clericalinfo_patient_factor_answer');
        $this->dropTable('et_ophcocvi_clericalinfo_patient_factor_answer_version');
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
