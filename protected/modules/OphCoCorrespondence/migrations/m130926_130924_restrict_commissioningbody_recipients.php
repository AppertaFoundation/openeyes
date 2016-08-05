<?php

class m130926_130924_restrict_commissioningbody_recipients extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophcocorrespondence_cbt_recipient', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'commissioning_body_type_id' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophcocorrespondence_cbt_recipient_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophcocorrespondence_cbt_recipient_cui_fk` (`created_user_id`)',
                'KEY `ophcocorrespondence_cbt_recipient_cbti_fk` (`commissioning_body_type_id`)',
                'CONSTRAINT `ophcocorrespondence_cbt_recipient_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcocorrespondence_cbt_recipient_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcocorrespondence_cbt_recipient_cbti_fk` FOREIGN KEY (`commissioning_body_type_id`) REFERENCES `commissioning_body_type` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down()
    {
        $this->dropTable('ophcocorrespondence_cbt_recipient');
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
