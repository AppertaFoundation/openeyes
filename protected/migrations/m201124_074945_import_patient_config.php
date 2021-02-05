<?php

class m201124_074945_import_patient_config extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 0,
            'field_type_id' => $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name = "Radio buttons"')->queryScalar(),
            'key' => 'enable_patient_import',
            'name' => 'Enable Users to Import Patients using a CSV',
            'data' => serialize(array('on' => 'On', 'off' => 'Off')),
            'default_value' => 'off',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'enable_patient_import\'');
    }
}
