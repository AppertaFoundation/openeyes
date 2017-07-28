<?php

class m170711_132400_add_system_setting_therapy_sender extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array('display_order' => 0, 'field_type_id' => 3, 'key' => 'OphCoTherapyapplication_sender_email', 'name' => 'Therapy Application Sender Email'));
        $this->insert('setting_installation', array('key' => 'OphCoTherapyapplication_sender_email', 'value' => 'therapyapps@openeyes'));
    }

    public function down()
    {
        $this->delete('setting_installation', '`key`="OphCoTherapyapplication_sender_email"');
        $this->delete('setting_metadata', '`key`="OphCoTherapyapplication_sender_email"');
    }
}
