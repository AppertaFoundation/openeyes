<?php

class m160819_094559_optom_comment_alert extends CDbMigration
{
    public function up()
    {
        $setting_field_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('setting_field_type')
            ->where('name = :name', array(':name' => 'Text Field'))
            ->queryScalar();

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $setting_field_type_id,
            'key' => 'optom_comment_alert',
            'name' => 'Address For Optom Comment Alerts',
            'default_value' => '',
        ));
    }

    public function down()
    {
        echo "m160819_094559_optom_comment_alert does not support migration down.\n";
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
