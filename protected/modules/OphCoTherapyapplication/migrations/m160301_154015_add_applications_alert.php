<?php

class m160301_154015_add_applications_alert extends CDbMigration
{
    public function up()
    {
        $fieldType = $this->getDbConnection()->createCommand()->select('id')->from('setting_field_type')->where('name = "Text Field"')->queryRow();

        $this->insert('setting_metadata', array(
            'field_type_id' => $fieldType['id'],
            'key' => 'applications_alert_recipients',
            'name' => 'Therapy Applications Alert Recipients',
        ));

        $this->insert('setting_installation', array(
            'key' => 'applications_alert_recipients',
            'value' => 'email@example.com',
        ));
    }

    public function down()
    {
        $this->delete('setting_installation', '`key` = :key', array('key' => 'applications_alert_recipients'));
        $this->delete('setting_metadata', '`key` = :key', array('key' => 'applications_alert_recipients'));
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
