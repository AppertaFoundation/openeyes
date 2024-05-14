<?php

class m201126_104956_add_default_tech_support_provider extends CDbMigration
{
    public function safeUp()
    {

        // add a unique index that should have already existed
        $this->createIndex('setting_metadata_key_IDX', 'setting_metadata', array('element_type_id', 'key'), true);

        $this->update('setting_metadata', array('default_value' => 'Apperta Foundation'), '`key` = :key_val', array('key_val' => 'tech_support_provider'));
        $this->update('setting_metadata', array('default_value' => 'http://www.apperta.org'), '`key` = :key_val', array('key_val' => 'tech_support_url'));
    }

    public function safeDown()
    {
        $this->update('setting_metadata', array('default_value' => null), '`key` = :key_val', array('key_val' => 'tech_support_provider'));
        $this->update('setting_metadata', array('default_value' => null), '`key` = :key_val', array('key_val' => 'tech_support_url'));
    }
}
