<?php

class m191127_005343_add_side_overview_settings extends CDbMigration
{
    public function safeUp()
    {
        $field_type = SettingFieldType::model()->find('name = ?', ["Radio buttons"]);
        $this->insert('setting_metadata', array(
            'key' => 'patient_overview_popup_mode',
            'name' => 'Patient overview popup display mode',
            'data' => serialize(['side' => 'Side', 'float' => 'Float']),
            'field_type_id' => $field_type->id,
            'default_value' => 'side',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "patient_overview_popup_mode"');
    }
}
