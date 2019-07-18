<?php

class m180529_132013_create_correspondence_letter_settings_table extends OEMigration
{

    public function up()
    {
        $this->createOETable('ophcocorrespondence_letter_settings', array(
            'id' => 'pk',
            'display_order' => "tinyint(3) unsigned DEFAULT '0'",
            'field_type_id' => 'int(10) unsigned NOT NULL',
            'key' =>  'varchar(64) NOT NULL',
            'name' => 'varchar(64) NOT NULL',
            'data' =>  'varchar(4096) NOT NULL',
            'default_value' =>  'varchar(64) NOT NULL',
        ), $versioned = true);

        $this->addForeignKey('ophcocorrespondence_letter_set_field_type_id_fk', 'ophcocorrespondence_letter_settings', 'field_type_id', 'setting_field_type', 'id');
        $this->addForeignKey('ophcocorrespondence_letter_set_created_user_id_fk', 'ophcocorrespondence_letter_settings', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophcocorrespondence_letter_set_last_modified_user_id_fk', 'ophcocorrespondence_letter_settings', 'last_modified_user_id', 'user', 'id');

        $this->createOETable('ophcocorrespondence_letter_setting_value', array(
            'id' => 'pk',
            'element_type_id' => 'int(10) unsigned DEFAULT NULL',
            'key' =>  'varchar(64) NOT NULL',
            'value' => 'varchar(255) COLLATE utf8_bin NOT NULL',
        ), $versioned = true);

        $this->insert('ophcocorrespondence_letter_settings', array(
            'field_type_id' => 4,
            'key' => 'letter_footer_blank_line_count',
            'name' => 'Letter footer blank line count'
        ));

    }

    public function down()
    {
        $this->dropOETable('ophcocorrespondence_letter_settings', $versioned = true);
        $this->dropOETable('ophcocorrespondence_letter_setting_value', $versioned = true);
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