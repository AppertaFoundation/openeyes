<?php

class m160811_133414_clinical_disorder_section_comments extends CDbMigration
{
    public function up()
    {
        $this->createTable('et_ophcocvi_clinicinfo_disorder_section_comment', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'element_id' => 'int(10) unsigned NOT NULL',
            'ophcocvi_clinicinfo_disorder_section_id' => 'int(10) unsigned NOT NULL',
            'comments' => 'text',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'deleted' => 'tinyint(1) unsigned not null',
            'PRIMARY KEY (`id`)',
            'KEY `et_ophcocvi_clinicinfo_disorder_section_comment_lmui_fk` (`last_modified_user_id`)',
            'KEY `et_ophcocvi_clinicinfo_disorder_section_comment_cui_fk` (`created_user_id`)',
            'KEY `et_ophcocvi_clinicinfo_disorder_section_comment_ele_fk` (`element_id`)',
            'KEY `et_ophcocvi_clinicinfo_disorder_section_comment_lku_fk` (`ophcocvi_clinicinfo_disorder_section_id`)',
            'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_section_comment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_section_comment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_section_comment_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcocvi_clinicinfo` (`id`)',
            'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_section_comment_lku_fk` FOREIGN KEY (`ophcocvi_clinicinfo_disorder_section_id`) REFERENCES `ophcocvi_clinicinfo_disorder_section` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $this->createTable('et_ophcocvi_clinicinfo_disorder_section_comment_version', array(
            'id' => 'int(10) unsigned NOT NULL',
            'element_id' => 'int(10) unsigned NOT NULL',
            'ophcocvi_clinicinfo_disorder_section_id' => 'int(10) unsigned NOT NULL',
            'comments' => 'text',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
            'version_id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'deleted' => 'tinyint(1) unsigned not null',
            'PRIMARY KEY (`version_id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'main_cause', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'main_cause', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'eye_id', 'int(10) unsigned NOT NULL');
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'affected', 'tinyint(1) unsigned NOT NULL DEFAULT 0');

    }

    public function down()
    {
        $this->dropTable('et_ophcocvi_clinicinfo_disorder_section_comment');
        $this->dropTable('et_ophcocvi_clinicinfo_disorder_section_comment_version');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'main_cause');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'main_cause');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'eye_id');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'affected');
    }

}
