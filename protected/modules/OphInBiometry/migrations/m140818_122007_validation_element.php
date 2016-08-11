<?php

class m140818_122007_validation_element extends OEMigration
{
    public function up()
    {
        $et = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :cn', array(':cn' => 'OphInBiometry'))->queryRow();

        $this->insert('element_type', array(
            'event_type_id' => $et['id'],
            'name' => 'Sign off',
            'class_name' => 'Element_OphInBiometry_SignOff',
            'display_order' => 5,
            'default' => 1,
        ));

        $this->createTable('et_ophinbioemtry_signoff', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned not null',
                'user1_id' => 'int(10) unsigned null',
                'user2_id' => 'int(10) unsigned null',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophinbioemtry_signoff_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophinbioemtry_signoff_cui_fk` (`created_user_id`)',
                'KEY `et_ophinbioemtry_signoff_ev_fk` (`event_id`)',
                'KEY `et_ophinbioemtry_signoff_u1_fk` (`user1_id`)',
                'KEY `et_ophinbioemtry_signoff_u2_fk` (`user2_id`)',
                'CONSTRAINT `et_ophinbioemtry_signoff_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbioemtry_signoff_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbioemtry_signoff_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `et_ophinbioemtry_signoff_u1_fk` FOREIGN KEY (`user1_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbioemtry_signoff_u2_fk` FOREIGN KEY (`user2_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('et_ophinbioemtry_signoff');
    }

    public function down()
    {
        $this->dropTable('et_ophinbioemtry_signoff_version');
        $this->dropTable('et_ophinbioemtry_signoff');

        $et = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :cn', array(':cn' => 'OphInBiometry'))->queryRow();

        $this->delete('element_type', "event_type_id = {$et['id']} and class_name = 'Element_OphInBiometry_SignOff'");
    }
}
