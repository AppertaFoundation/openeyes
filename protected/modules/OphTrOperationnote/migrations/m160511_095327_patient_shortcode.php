<?php

class m160511_095327_patient_shortcode extends CDbMigration
{
    public function up()
    {
        $this->insert('patient_shortcode', array('event_type_id' => '4', 'method' => 'getPatientUniqueCode', 'default_code' => 'puc', 'code' => 'puc', 'description' => 'Patient Unique Code'));
    }

    public function down()
    {
        $this->delete('patient_shortcode', 'code = ?', array('puc'));
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
