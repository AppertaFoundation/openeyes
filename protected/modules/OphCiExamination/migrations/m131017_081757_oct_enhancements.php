<?php

class m131017_081757_oct_enhancements extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophciexamination_oct_fluidstatus', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(128) NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'enabled' => 'boolean NOT NULL DEFAULT true',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophciexamination_oct_fluidstatus_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophciexamination_oct_fluidstatus_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophciexamination_oct_fluidstatus_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophciexamination_oct_fluidstatus_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophciexamination_oct_fluidstatus', array('name' => 'New', 'display_order' => '1'));
        $this->insert('ophciexamination_oct_fluidstatus', array('name' => 'Improving', 'display_order' => '2'));
        $this->insert('ophciexamination_oct_fluidstatus', array('name' => 'Persistent', 'display_order' => '3'));

        $this->createTable('ophciexamination_oct_fluidtype', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(128) NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'enabled' => 'boolean NOT NULL DEFAULT true',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophciexamination_oct_fluidtype_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophciexamination_oct_fluidtype_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophciexamination_oct_fluidtype_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophciexamination_oct_fluidtype_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophciexamination_oct_fluidtype', array('name' => 'IRF', 'display_order' => '1'));
        $this->insert('ophciexamination_oct_fluidtype', array('name' => 'SRF', 'display_order' => '2'));
        $this->insert('ophciexamination_oct_fluidtype', array('name' => 'PED', 'display_order' => '3'));

        $this->addColumn('et_ophciexamination_oct', 'left_thickness_increase', 'boolean');
        $this->addColumn('et_ophciexamination_oct', 'right_thickness_increase', 'boolean');
        $this->addColumn('et_ophciexamination_oct', 'left_dry', 'boolean');
        $this->addColumn('et_ophciexamination_oct', 'right_dry', 'boolean');
        $this->addColumn('et_ophciexamination_oct', 'left_fluidstatus_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophciexamination_oct_lfs_fk', 'et_ophciexamination_oct', 'left_fluidstatus_id', 'ophciexamination_oct_fluidstatus', 'id');
        $this->addColumn('et_ophciexamination_oct', 'right_fluidstatus_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophciexamination_oct_rfs_fk', 'et_ophciexamination_oct', 'right_fluidstatus_id', 'ophciexamination_oct_fluidstatus', 'id');
        $this->addColumn('et_ophciexamination_oct', 'left_comments', 'text');
        $this->addColumn('et_ophciexamination_oct', 'right_comments', 'text');

        $this->createTable('ophciexamination_oct_fluidtype_assignment', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'fluidtype_id' => 'int(10) unsigned NOT NULL',
                'element_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophciexamination_oct_fluidtype_assignment_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophciexamination_oct_fluidtype_assignment_cui_fk` (`created_user_id`)',
                'KEY `ophciexamination_oct_fluidtype_assignment_eye_id_fk` (`eye_id`)',
                'KEY `ophciexamination_oct_fluidtype_assignment_fti_fk` (`fluidtype_id`)',
                'KEY `ophciexamination_oct_fluidtype_assignment_ei_fk` (`element_id`)',
                'CONSTRAINT `ophciexamination_oct_fluidtype_assignment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophciexamination_oct_fluidtype_assignment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophciexamination_oct_fluidtype_assignment_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
                'CONSTRAINT `ophciexamination_oct_fluidtype_assignment_fti_fk` FOREIGN KEY (`fluidtype_id`) REFERENCES `ophciexamination_oct_fluidtype` (`id`)',
                'CONSTRAINT `ophciexamination_oct_fluidtype_assignment_ei_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophciexamination_oct` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down()
    {
        $this->dropTable('ophciexamination_oct_fluidtype_assignment');

        $this->dropColumn('et_ophciexamination_oct', 'right_comments');
        $this->dropColumn('et_ophciexamination_oct', 'left_comments');
        $this->dropForeignKey('et_ophciexamination_oct_rfs_fk', 'et_ophciexamination_oct');
        $this->dropColumn('et_ophciexamination_oct', 'right_fluidstatus_id');
        $this->dropForeignKey('et_ophciexamination_oct_lfs_fk', 'et_ophciexamination_oct');
        $this->dropColumn('et_ophciexamination_oct', 'left_fluidstatus_id');
        $this->dropColumn('et_ophciexamination_oct', 'right_dry');
        $this->dropColumn('et_ophciexamination_oct', 'left_dry');
        $this->dropColumn('et_ophciexamination_oct', 'right_thickness_increase');
        $this->dropColumn('et_ophciexamination_oct', 'left_thickness_increase');
        $this->dropTable('ophciexamination_oct_fluidtype');
        $this->dropTable('ophciexamination_oct_fluidstatus');
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
