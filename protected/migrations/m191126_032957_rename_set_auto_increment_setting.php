<?php

class m191126_032957_rename_set_auto_increment_setting extends CDbMigration
{
    public function up()
    {
        $command = $this->dbConnection->createCommand('SELECT id FROM setting_metadata WHERE `key` = \'set_auto_increment\'');
        $command->execute();
        $id = $command->queryScalar();

        $this->update(
            'setting_metadata',
            ['key' => 'set_auto_increment_hospital_no'],
            'id = :id',
            [':id' => $id]
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
