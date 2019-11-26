<?php

class m191126_032957_rename_set_auto_increment_setting extends CDbMigration
{
    public function up()
    {
        $set_auto_increment = SettingMetadata::model()->find('`key` = "set_auto_increment"');

        $this->update('setting_metadata',
            ['key' => 'set_auto_increment_hospital_no'],
            'id = :id', [':id' => $set_auto_increment->id]
        );
    }

    public function down()
    {
        echo "m191126_032957_rename_set_auto_increment_setting does not support migration down.\n";
        return false;
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