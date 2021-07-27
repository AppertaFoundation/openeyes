<?php

class m210504_234000_pas_appt_arrival_admin_settings extends OEMigration
{

    public function safeUp()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryRow();
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $id['id'],
            'key' => 'pas_appt_patient_arrival_status_name',
            'name' => 'PAS Appointment Patient Arrival Status Name',
            'data' => '',
            'default_value' => 'Status',
        ));

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => $id['id'],
            'key' => 'pas_appt_patient_arrival_status_text',
            'name' => 'PAS Appointment Patient Arrival Status Match Text',
            'data' => '',
            'default_value' => 'Arrived',
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "pas_appt_patient_arrival_status_name"');
        $this->delete('setting_metadata', '`key` = "pas_appt_patient_arrival_status_text"');
    }
}
